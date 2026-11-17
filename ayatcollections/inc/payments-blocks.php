<?php
/**
 * Easypaisa in the WooCommerce block checkout.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

/**
 * One block integration per wallet.
 */
final class Ayat_Wallet_Blocks extends AbstractPaymentMethodType {

	/**
	 * Gateway id.
	 *
	 * @param string $id Gateway id.
	 */
	public function __construct( $id ) {
		$this->name = $id;
	}

	/** Load settings. */
	public function initialize() {
		$this->settings = get_option( 'woocommerce_' . $this->name . '_settings', array() );
	}

	/**
	 * Shown only when on and a number is set.
	 *
	 * @return bool
	 */
	public function is_active() {
		return 'yes' === ( $this->settings['enabled'] ?? 'no' ) && '' !== trim( (string) ( $this->settings['account_number'] ?? '' ) );
	}

	/**
	 * Script.
	 *
	 * @return array
	 */
	public function get_payment_method_script_handles() {
		wp_register_script(
			'ayat-wallet-blocks',
			AYAT_URI . '/assets/js/wallet-blocks.js',
			array( 'wc-blocks-registry', 'wc-settings', 'wp-element', 'wp-html-entities' ),
			AYAT_VERSION,
			true
		);
		return array( 'ayat-wallet-blocks' );
	}

	/**
	 * Data for the script.
	 *
	 * @return array
	 */
	public function get_payment_method_data() {
		$brand = 'Easypaisa';
		return array(
			'title'         => $this->settings['title'] ?? $brand,
			'description'   => $this->settings['description'] ?? '',
			'accountTitle'  => $this->settings['account_title'] ?? '',
			'accountNumber' => $this->settings['account_number'] ?? '',
			'brand'         => $brand,
			'supports'      => array( 'products' ),
		);
	}
}
