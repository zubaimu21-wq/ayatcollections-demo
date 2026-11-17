<?php
/**
 * Easypaisa (manual mobile-wallet payment).
 *
 * The customer chooses Easypaisa at checkout, places the order,
 * sends the amount to the store's wallet number and shares the screenshot on
 * WhatsApp. The order waits as "On hold" until the store confirms the money
 * arrived and changes it to "Processing".
 *
 * WooCommerce → Settings → Payments → Easypaisa: enter the account
 * title and number, then switch it on. It stays hidden until it has a number.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
	return;
}

/**
 * Shared manual wallet gateway.
 */
abstract class Ayat_Wallet_Gateway extends WC_Payment_Gateway {

	/** @var string Wallet brand, e.g. Easypaisa. */
	public $brand = '';

	/** @var string Account title. */
	public $account_title = '';

	/** @var string Account / mobile number. */
	public $account_number = '';

	/**
	 * Set up.
	 */
	public function __construct() {
		$this->has_fields         = false;
		$this->method_title       = $this->brand;
		/* translators: %s: wallet name */
		$this->method_description = sprintf( __( 'Customer sends the order amount to your %s account and shares the screenshot on WhatsApp. The order stays "On hold" until you confirm the payment.', 'ayatcollections' ), $this->brand );
		$this->init_form_fields();
		$this->init_settings();
		$this->title          = $this->get_option( 'title' );
		$this->description    = $this->get_option( 'description' );
		$this->account_title  = $this->get_option( 'account_title' );
		$this->account_number = $this->get_option( 'account_number' );

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_box' ) );
		add_action( 'woocommerce_email_before_order_table', array( $this, 'email_details' ), 10, 3 );
	}

	/**
	 * Admin fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'        => array(
				'title'   => __( 'Enable', 'ayatcollections' ),
				'type'    => 'checkbox',
				/* translators: %s: wallet name */
				'label'   => sprintf( __( 'Accept %s payments', 'ayatcollections' ), $this->brand ),
				'default' => 'no',
			),
			'title'          => array(
				'title'   => __( 'Title at checkout', 'ayatcollections' ),
				'type'    => 'text',
				'default' => $this->brand,
			),
			'description'    => array(
				'title'   => __( 'Text at checkout', 'ayatcollections' ),
				'type'    => 'textarea',
				/* translators: %s: wallet name */
				'default' => sprintf( __( 'Place your order, then send the total to our %s account below and share the payment screenshot on WhatsApp. We dispatch as soon as the payment is confirmed.', 'ayatcollections' ), $this->brand ),
			),
			'account_title'  => array(
				'title'       => __( 'Account title (name)', 'ayatcollections' ),
				'type'        => 'text',
				'default'     => '',
			),
			'account_number' => array(
				'title'       => __( 'Account / mobile number', 'ayatcollections' ),
				'type'        => 'text',
				'default'     => '',
				'description' => __( 'This method is hidden at checkout until a number is entered.', 'ayatcollections' ),
			),
		);
	}

	/**
	 * Hidden until a number is set.
	 *
	 * @return bool
	 */
	public function is_available() {
		return '' !== trim( (string) $this->account_number ) && parent::is_available();
	}

	/**
	 * Place the order on hold.
	 *
	 * @param int $order_id Order.
	 * @return array
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );
		/* translators: %s: wallet name */
		$order->update_status( 'on-hold', sprintf( __( 'Awaiting %s payment – check the screenshot before dispatch.', 'ayatcollections' ), $this->brand ) );
		wc_reduce_stock_levels( $order_id );
		WC()->cart->empty_cart();
		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}

	/**
	 * Account details as HTML.
	 *
	 * @param WC_Order $order Order.
	 * @param bool     $with_button Show the WhatsApp button.
	 * @return string
	 */
	public function details_html( $order, $with_button = true ) {
		$wa   = function_exists( 'ayat_store_whatsapp' ) ? ayat_store_whatsapp() : '';
		$html = '<section class="ayat-pay-box"><h2>' . esc_html(
			/* translators: %s: wallet name */
			sprintf( __( 'Pay with %s', 'ayatcollections' ), $this->brand )
		) . '</h2><ol>';
		$html .= '<li>' . esc_html__( 'Send this amount', 'ayatcollections' ) . ': <strong>' . wp_kses_post( wc_price( $order->get_total() ) ) . '</strong></li>';
		/* translators: %s: wallet name */
		$html .= '<li>' . esc_html( sprintf( __( 'To our %s account', 'ayatcollections' ), $this->brand ) ) . ': <strong>' . esc_html( $this->account_number ) . '</strong>' . ( $this->account_title ? ' (' . esc_html( $this->account_title ) . ')' : '' ) . '</li>';
		$html .= '<li>' . esc_html__( 'Share the payment screenshot with your order number', 'ayatcollections' ) . ': <strong>#' . esc_html( $order->get_order_number() ) . '</strong></li></ol>';
		if ( $with_button && $wa ) {
			/* translators: 1: order number, 2: wallet name, 3: amount */
			$msg   = sprintf( __( 'Assalam o Alaikum! I have paid for order #%1$s via %2$s (%3$s). Screenshot attached.', 'ayatcollections' ), $order->get_order_number(), $this->brand, wp_strip_all_tags( wc_price( $order->get_total() ) ) );
			$html .= '<a class="ayat-pay-box__wa" href="' . esc_url( 'https://wa.me/' . $wa . '?text=' . rawurlencode( $msg ) ) . '" target="_blank" rel="noopener">' . esc_html__( 'Send screenshot on WhatsApp', 'ayatcollections' ) . '</a>';
		}
		$html .= '<p class="ayat-pay-box__note">' . esc_html__( 'Your order is reserved. We dispatch as soon as the payment is confirmed.', 'ayatcollections' ) . '</p></section>';
		return $html;
	}

	/**
	 * Thank-you page.
	 *
	 * @param int $order_id Order.
	 */
	public function thankyou_box( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( $order ) {
			echo $this->details_html( $order ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Customer email (order on hold).
	 *
	 * @param WC_Order $order         Order.
	 * @param bool     $sent_to_admin Admin email.
	 * @param bool     $plain_text    Plain text.
	 */
	public function email_details( $order, $sent_to_admin, $plain_text = false ) {
		if ( $sent_to_admin || $order->get_payment_method() !== $this->id || ! $order->has_status( 'on-hold' ) ) {
			return;
		}
		if ( $plain_text ) {
			/* translators: 1: wallet, 2: amount, 3: number, 4: order */
			echo esc_html( sprintf( __( 'Pay with %1$s: send %2$s to %3$s and share the screenshot with order #%4$s on WhatsApp.', 'ayatcollections' ), $this->brand, wp_strip_all_tags( wc_price( $order->get_total() ) ), $this->account_number, $order->get_order_number() ) ) . "\n\n";
		} else {
			echo $this->details_html( $order ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}

/** Easypaisa. */
class Ayat_Gateway_Easypaisa extends Ayat_Wallet_Gateway {
	/** Set up. */
	public function __construct() {
		$this->id    = 'ayat_easypaisa';
		$this->brand = 'Easypaisa';
		parent::__construct();
	}
}

/**
 * Register the gateways.
 *
 * @param array $gateways Gateways.
 * @return array
 */
function ayat_add_wallet_gateways( $gateways ) {
	$gateways[] = 'Ayat_Gateway_Easypaisa';
	return $gateways;
}
add_filter( 'woocommerce_payment_gateways', 'ayat_add_wallet_gateways' );

/**
 * Block checkout support.
 */
add_action(
	'woocommerce_blocks_payment_method_type_registration',
	function ( $registry ) {
		if ( ! class_exists( '\Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
			return;
		}
		require_once __DIR__ . '/payments-blocks.php';
		$registry->register( new Ayat_Wallet_Blocks( 'ayat_easypaisa' ) );
	}
);
