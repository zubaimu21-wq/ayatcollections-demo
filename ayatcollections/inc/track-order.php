<?php
/**
 * Order tracking: a "Track Order" page where customers enter their order number
 * and phone (or email) and see a step-by-step journey of their parcel.
 *
 * Store owners set the courier, tracking number and stage on the order screen
 * (WooCommerce → Orders → open an order → "SHAYA order tracking").
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

/**
 * Tracking stages in order.
 *
 * @return array slug => label.
 */
function ayat_track_stages() {
	return array(
		'placed'    => __( 'Order placed', 'ayatcollections' ),
		'confirmed' => __( 'Confirmed', 'ayatcollections' ),
		'packed'    => __( 'Gift-boxed & packed', 'ayatcollections' ),
		'shipped'   => __( 'With the courier', 'ayatcollections' ),
		'delivered' => __( 'Delivered', 'ayatcollections' ),
	);
}

/**
 * URL of the Track Order page.
 *
 * @return string
 */
function ayat_track_url() {
	$page = get_page_by_path( 'track-order' );
	return $page ? get_permalink( $page ) : home_url( '/track-order/' );
}

/**
 * Create the Track Order page once, so the header link always works.
 */
function ayat_track_ensure_page() {
	if ( get_option( 'ayat_track_page_done' ) || ! class_exists( 'WooCommerce' ) ) {
		return;
	}
	if ( ! get_page_by_path( 'track-order' ) ) {
		wp_insert_post(
			array(
				'post_title'   => __( 'Track Order', 'ayatcollections' ),
				'post_name'    => 'track-order',
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '[ayat_track_order]',
			)
		);
	}
	update_option( 'ayat_track_page_done', 1 );
}
add_action( 'init', 'ayat_track_ensure_page', 30 );

/**
 * Work out which stage an order is at.
 *
 * @param WC_Order $order Order.
 * @return string Stage slug, or 'cancelled'.
 */
function ayat_track_stage( $order ) {
	$status = $order->get_status();
	if ( in_array( $status, array( 'cancelled', 'refunded', 'failed' ), true ) ) {
		return 'cancelled';
	}
	$manual = (string) $order->get_meta( '_ayat_stage' );
	if ( $manual && array_key_exists( $manual, ayat_track_stages() ) ) {
		return $manual;
	}
	if ( 'completed' === $status ) {
		return 'delivered';
	}
	if ( 'processing' === $status ) {
		return 'confirmed';
	}
	return 'placed';
}

/**
 * Only digits, last 10 (so 0311…, +92311… and 92311… all match).
 *
 * @param string $phone Phone.
 * @return string
 */
function ayat_track_phone_key( $phone ) {
	$d = preg_replace( '/\D/', '', (string) $phone );
	return substr( $d, -10 );
}

/**
 * [ayat_track_order] shortcode.
 *
 * @return string
 */
function ayat_track_shortcode() {
	if ( ! function_exists( 'wc_get_order' ) ) {
		return '';
	}
	$order   = null;
	$error   = '';
	$num_in  = '';
	$who_in  = '';
	$checked = isset( $_POST['ayat_track_nonce'] );

	if ( $checked ) {
		$num_in = isset( $_POST['order_no'] ) ? sanitize_text_field( wp_unslash( $_POST['order_no'] ) ) : '';
		$who_in = isset( $_POST['order_who'] ) ? sanitize_text_field( wp_unslash( $_POST['order_who'] ) ) : '';
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ayat_track_nonce'] ) ), 'ayat_track' ) ) {
			$error = __( 'Your session expired. Please try again.', 'ayatcollections' );
		} else {
			$id   = absint( preg_replace( '/\D/', '', $num_in ) );
			$cand = $id ? wc_get_order( $id ) : false;
			$ok   = false;
			if ( $cand && is_a( $cand, 'WC_Order' ) && $who_in ) {
				if ( is_email( $who_in ) ) {
					$ok = strtolower( $who_in ) === strtolower( (string) $cand->get_billing_email() );
				} else {
					$key = ayat_track_phone_key( $who_in );
					$ok  = strlen( $key ) === 10 && $key === ayat_track_phone_key( $cand->get_billing_phone() );
				}
			}
			if ( $ok ) {
				$order = $cand;
			} else {
				$error = __( 'We could not find an order with those details. Check the order number and use the phone number or email you ordered with.', 'ayatcollections' );
			}
		}
	}

	ob_start();
	?>
	<div class="ayat-track">
		<div class="ayat-track__intro">
			<p class="eyebrow"><?php esc_html_e( 'Where is my order?', 'ayatcollections' ); ?></p>
			<p><?php esc_html_e( 'Enter your order number and the phone number you ordered with to follow your SHAYA box from our studio to your door.', 'ayatcollections' ); ?></p>
		</div>
		<form class="ayat-track__form" method="post" action="<?php echo esc_url( ayat_track_url() ); ?>">
			<?php wp_nonce_field( 'ayat_track', 'ayat_track_nonce' ); ?>
			<p class="ayat-track__field">
				<label for="ayat-track-no"><?php esc_html_e( 'Order number', 'ayatcollections' ); ?></label>
				<input id="ayat-track-no" name="order_no" inputmode="numeric" autocomplete="off" placeholder="<?php esc_attr_e( 'e.g. 1045', 'ayatcollections' ); ?>" value="<?php echo esc_attr( $num_in ); ?>" required>
			</p>
			<p class="ayat-track__field">
				<label for="ayat-track-who"><?php esc_html_e( 'Phone or email', 'ayatcollections' ); ?></label>
				<input id="ayat-track-who" name="order_who" autocomplete="tel" placeholder="03XX XXXXXXX" value="<?php echo esc_attr( $who_in ); ?>" required>
			</p>
			<button type="submit" class="btn-ink"><?php esc_html_e( 'Track my order', 'ayatcollections' ); ?></button>
		</form>
		<?php if ( $error ) : ?>
			<p class="ayat-track__error" role="alert"><?php echo esc_html( $error ); ?></p>
		<?php endif; ?>
		<?php
		if ( $order ) {
			echo ayat_track_result( $order ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'ayat_track_order', 'ayat_track_shortcode' );

/**
 * Result card: journey steps, courier details and items.
 *
 * @param WC_Order $order Order.
 * @return string
 */
function ayat_track_result( $order ) {
	$stage   = ayat_track_stage( $order );
	$stages  = ayat_track_stages();
	$keys    = array_keys( $stages );
	$at      = array_search( $stage, $keys, true );
	$courier = (string) $order->get_meta( '_ayat_courier' );
	$tno     = (string) $order->get_meta( '_ayat_tracking_no' );
	$wa      = function_exists( 'ayat_store_whatsapp' ) ? ayat_store_whatsapp() : '';
	$created = $order->get_date_created();

	ob_start();
	?>
	<section class="ayat-track__result" aria-live="polite">
		<header class="ayat-track__head">
			<div><small><?php esc_html_e( 'Order', 'ayatcollections' ); ?></small><strong>#<?php echo esc_html( $order->get_order_number() ); ?></strong></div>
			<div><small><?php esc_html_e( 'Placed on', 'ayatcollections' ); ?></small><strong><?php echo esc_html( $created ? wc_format_datetime( $created ) : '' ); ?></strong></div>
			<div><small><?php esc_html_e( 'Total', 'ayatcollections' ); ?></small><strong><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></strong></div>
			<div><small><?php esc_html_e( 'City', 'ayatcollections' ); ?></small><strong><?php echo esc_html( $order->get_billing_city() ); ?></strong></div>
		</header>

		<?php if ( 'cancelled' === $stage ) : ?>
			<p class="ayat-track__error"><?php esc_html_e( 'This order was cancelled. Message us on WhatsApp if you think this is a mistake.', 'ayatcollections' ); ?></p>
		<?php else : ?>
			<ol class="ayat-journey" role="list">
				<?php foreach ( $keys as $i => $key ) : ?>
					<?php $state = $i < $at ? 'done' : ( $i === $at ? 'now' : 'next' ); ?>
					<li class="ayat-journey__step is-<?php echo esc_attr( $state ); ?>"<?php echo 'now' === $state ? ' aria-current="step"' : ''; ?>>
						<span class="ayat-journey__gem" aria-hidden="true"></span>
						<span class="ayat-journey__label"><?php echo esc_html( $stages[ $key ] ); ?></span>
						<?php if ( 'now' === $state ) : ?><span class="ayat-journey__now"><?php esc_html_e( 'Current step', 'ayatcollections' ); ?></span><?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>

		<?php if ( $courier || $tno ) : ?>
			<div class="ayat-track__courier">
				<?php if ( $courier ) : ?><p><small><?php esc_html_e( 'Courier', 'ayatcollections' ); ?></small><?php echo esc_html( $courier ); ?></p><?php endif; ?>
				<?php if ( $tno ) : ?><p><small><?php esc_html_e( 'Tracking number', 'ayatcollections' ); ?></small><span class="ayat-track__tno"><?php echo esc_html( $tno ); ?></span></p><?php endif; ?>
			</div>
		<?php endif; ?>

		<ul class="ayat-track__items" role="list">
			<?php foreach ( $order->get_items() as $item ) : ?>
				<li><span><?php echo esc_html( $item->get_name() ); ?> &times; <?php echo esc_html( $item->get_quantity() ); ?></span><span><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?></span></li>
			<?php endforeach; ?>
		</ul>

		<?php if ( $wa ) : ?>
			<p class="ayat-track__help"><?php esc_html_e( 'Questions about your delivery?', 'ayatcollections' ); ?> <a href="<?php echo esc_attr( 'https://wa.me/' . $wa . '?text=' . rawurlencode( sprintf( /* translators: %s: order number */ __( 'Assalam o Alaikum! I would like an update on order #%s.', 'ayatcollections' ), $order->get_order_number() ) ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Ask us on WhatsApp', 'ayatcollections' ); ?></a></p>
		<?php endif; ?>
	</section>
	<?php
	return ob_get_clean();
}

/**
 * Admin: courier, tracking number and stage fields on the order screen.
 *
 * @param WC_Order $order Order.
 */
function ayat_track_admin_fields( $order ) {
	$stage = (string) $order->get_meta( '_ayat_stage' );
	?>
	<div class="ayat-track-admin" style="clear:both;padding-top:12px">
		<h3><?php esc_html_e( 'SHAYA order tracking', 'ayatcollections' ); ?></h3>
		<p class="form-field form-field-wide">
			<label for="ayat_stage"><?php esc_html_e( 'Tracking stage shown to the customer', 'ayatcollections' ); ?></label>
			<select id="ayat_stage" name="ayat_stage">
				<option value=""><?php esc_html_e( 'Automatic (from order status)', 'ayatcollections' ); ?></option>
				<?php foreach ( ayat_track_stages() as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $stage, $key ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p class="form-field form-field-wide">
			<label for="ayat_courier"><?php esc_html_e( 'Courier (e.g. TCS, Leopards, M&P, PostEx)', 'ayatcollections' ); ?></label>
			<input type="text" id="ayat_courier" name="ayat_courier" value="<?php echo esc_attr( $order->get_meta( '_ayat_courier' ) ); ?>">
		</p>
		<p class="form-field form-field-wide">
			<label for="ayat_tracking_no"><?php esc_html_e( 'Courier tracking number', 'ayatcollections' ); ?></label>
			<input type="text" id="ayat_tracking_no" name="ayat_tracking_no" value="<?php echo esc_attr( $order->get_meta( '_ayat_tracking_no' ) ); ?>">
		</p>
	</div>
	<?php
}
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'ayat_track_admin_fields' );

/**
 * Save the tracking fields.
 *
 * @param int $order_id Order ID.
 */
function ayat_track_admin_save( $order_id ) {
	if ( ! current_user_can( 'edit_shop_orders' ) || ! isset( $_POST['ayat_courier'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- WooCommerce verifies the order form nonce.
		return;
	}
	$order = wc_get_order( $order_id );
	if ( ! $order ) {
		return;
	}
	// phpcs:disable WordPress.Security.NonceVerification.Missing
	$stage = isset( $_POST['ayat_stage'] ) ? sanitize_key( wp_unslash( $_POST['ayat_stage'] ) ) : '';
	$order->update_meta_data( '_ayat_stage', array_key_exists( $stage, ayat_track_stages() ) ? $stage : '' );
	$order->update_meta_data( '_ayat_courier', sanitize_text_field( wp_unslash( $_POST['ayat_courier'] ) ) );
	$order->update_meta_data( '_ayat_tracking_no', isset( $_POST['ayat_tracking_no'] ) ? sanitize_text_field( wp_unslash( $_POST['ayat_tracking_no'] ) ) : '' );
	// phpcs:enable
	$order->save();
}
add_action( 'woocommerce_process_shop_order_meta', 'ayat_track_admin_save', 50 );

/**
 * Show the tracking link in customer emails and on the thank-you page.
 *
 * @param WC_Order $order Order.
 */
function ayat_track_thankyou_link( $order ) {
	if ( is_numeric( $order ) ) {
		$order = wc_get_order( $order );
	}
	if ( ! $order ) {
		return;
	}
	printf(
		'<p class="ayat-track-link">%1$s <a href="%2$s">%3$s</a></p>',
		esc_html__( 'Follow your parcel any time:', 'ayatcollections' ),
		esc_url( ayat_track_url() ),
		/* translators: %s: order number */
		esc_html( sprintf( __( 'track order #%s', 'ayatcollections' ), $order->get_order_number() ) )
	);
}
add_action( 'woocommerce_thankyou', 'ayat_track_thankyou_link', 20 );
