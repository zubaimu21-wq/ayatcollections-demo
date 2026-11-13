<?php
/**
 * New-order alerts so the store owner knows the moment an order comes in.
 *
 * 1. Email: WooCommerce's own "New order" email, sent to the address(es) set here.
 * 2. WhatsApp alert (optional): a message to the owner's WhatsApp through the free
 *    CallMeBot service (the owner activates it once and pastes the API key here).
 * 3. "Confirm on WhatsApp" button on the order-received page, so customers can send
 *    their order details straight to the store's WhatsApp.
 *
 * Settings: WooCommerce → Order alerts.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

/**
 * Saved settings with defaults.
 *
 * @return array
 */
function ayat_alert_settings() {
	return wp_parse_args(
		get_option( 'ayat_order_alerts', array() ),
		array(
			'wa_on'      => 0,
			'wa_phone'   => '',
			'wa_key'     => '',
			'confirm_on' => 1,
			'store_wa'   => '',
		)
	);
}

/**
 * Digits-only phone with 92 country code (0300… → 92300…).
 *
 * @param string $phone Phone.
 * @return string
 */
function ayat_alert_phone( $phone ) {
	$d = preg_replace( '/[^0-9]/', '', (string) $phone );
	if ( 0 === strpos( $d, '00' ) ) {
		$d = substr( $d, 2 );
	} elseif ( 0 === strpos( $d, '0' ) ) {
		$d = '92' . substr( $d, 1 );
	}
	return $d;
}

/**
 * Store WhatsApp number for the customer button (setting, else the store phone).
 *
 * @return string
 */
function ayat_store_whatsapp() {
	$s = ayat_alert_settings();
	return ayat_alert_phone( $s['store_wa'] ? $s['store_wa'] : get_theme_mod( 'ayat_phone', '' ) );
}

/**
 * Short plain-text order summary.
 *
 * @param WC_Order $order Order.
 * @param bool     $for_owner Include the admin link.
 * @return string
 */
function ayat_order_summary( $order, $for_owner = true ) {
	$lines   = array();
	$lines[] = sprintf( ( $for_owner ? 'New order' : 'Order' ) . ' #%s – %s', $order->get_order_number(), wp_strip_all_tags( html_entity_decode( wc_price( $order->get_total() ) ) ) );
	$lines[] = sprintf( 'Payment: %s', $order->get_payment_method_title() );
	$lines[] = trim( $order->get_formatted_billing_full_name() . ', ' . $order->get_billing_phone() . ', ' . $order->get_billing_city(), ', ' );
	foreach ( $order->get_items() as $item ) {
		$lines[] = sprintf( '• %s × %d', $item->get_name(), $item->get_quantity() );
	}
	if ( $for_owner ) {
		$lines[] = admin_url( 'post.php?post=' . $order->get_id() . '&action=edit' );
	}
	return implode( "\n", $lines );
}

/**
 * Send a WhatsApp message to the owner via CallMeBot.
 *
 * @param string $text Message.
 * @param bool   $blocking Wait for the reply (used by the test button).
 * @return true|WP_Error
 */
function ayat_send_whatsapp_alert( $text, $blocking = false ) {
	$s = ayat_alert_settings();
	if ( ! $s['wa_phone'] || ! $s['wa_key'] ) {
		return new WP_Error( 'ayat_wa', __( 'Add your WhatsApp number and CallMeBot API key first.', 'ayatcollections' ) );
	}
	$url = add_query_arg(
		array(
			'phone'  => '+' . ayat_alert_phone( $s['wa_phone'] ),
			'text'   => rawurlencode( $text ),
			'apikey' => rawurlencode( $s['wa_key'] ),
		),
		'https://api.callmebot.com/whatsapp.php'
	);
	$res = wp_remote_get( $url, array( 'timeout' => $blocking ? 15 : 5, 'blocking' => $blocking ) );
	if ( is_wp_error( $res ) ) {
		return $res;
	}
	if ( $blocking && 200 !== (int) wp_remote_retrieve_response_code( $res ) ) {
		return new WP_Error( 'ayat_wa', wp_strip_all_tags( wp_remote_retrieve_body( $res ) ) );
	}
	return true;
}

/**
 * On a new order: WhatsApp alert to the owner (once per order).
 *
 * @param int|WC_Order $order Order or ID.
 */
function ayat_new_order_alert( $order ) {
	$order = $order instanceof WC_Order ? $order : wc_get_order( $order );
	$s     = ayat_alert_settings();
	if ( ! $order || ! $s['wa_on'] || $order->get_meta( '_ayat_alert_sent' ) ) {
		return;
	}
	$order->update_meta_data( '_ayat_alert_sent', time() );
	$order->save_meta_data();
	ayat_send_whatsapp_alert( ayat_order_summary( $order ) );
}
add_action( 'woocommerce_checkout_order_processed', 'ayat_new_order_alert', 20 );
add_action( 'woocommerce_store_api_checkout_order_processed', 'ayat_new_order_alert', 20 );

/**
 * Order-received page: let the customer send the order to the store's WhatsApp.
 *
 * @param int $order_id Order ID.
 */
function ayat_thankyou_whatsapp( $order_id ) {
	$s     = ayat_alert_settings();
	$phone = ayat_store_whatsapp();
	$order = wc_get_order( $order_id );
	if ( ! $s['confirm_on'] || ! $phone || ! $order ) {
		return;
	}
	$text = "Assalam o Alaikum! I just placed an order.\n" . ayat_order_summary( $order, false );
	echo '<div class="ayat-wa-confirm"><p>' . esc_html__( 'Want a faster confirmation? Send your order to us on WhatsApp.', 'ayatcollections' ) . '</p>';
	echo '<a class="btn-ink ayat-wa-confirm__btn" href="' . esc_attr( 'https://wa.me/' . $phone . '?text=' . rawurlencode( $text ) ) . '" rel="noopener">' . esc_html__( 'Confirm order on WhatsApp', 'ayatcollections' ) . '</a></div>';
}
add_action( 'woocommerce_thankyou', 'ayat_thankyou_whatsapp', 5 );

/**
 * Admin page: WooCommerce → Order alerts.
 */
function ayat_alerts_menu() {
	add_submenu_page( 'woocommerce', __( 'Order alerts', 'ayatcollections' ), __( 'Order alerts', 'ayatcollections' ), 'manage_woocommerce', 'ayat-order-alerts', 'ayat_alerts_page' );
}
add_action( 'admin_menu', 'ayat_alerts_menu', 60 );

/**
 * Save settings / send a test.
 */
function ayat_alerts_save() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_die( esc_html__( 'Not allowed.', 'ayatcollections' ) );
	}
	check_admin_referer( 'ayat_alerts' );
	$in  = wp_unslash( $_POST ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$new = array(
		'wa_on'      => empty( $in['wa_on'] ) ? 0 : 1,
		'wa_phone'   => sanitize_text_field( isset( $in['wa_phone'] ) ? $in['wa_phone'] : '' ),
		'wa_key'     => sanitize_text_field( isset( $in['wa_key'] ) ? $in['wa_key'] : '' ),
		'confirm_on' => empty( $in['confirm_on'] ) ? 0 : 1,
		'store_wa'   => sanitize_text_field( isset( $in['store_wa'] ) ? $in['store_wa'] : '' ),
	);
	update_option( 'ayat_order_alerts', $new, false );

	// Email recipients for WooCommerce's "New order" email.
	$emails = array_filter( array_map( 'sanitize_email', array_map( 'trim', explode( ',', isset( $in['emails'] ) ? $in['emails'] : '' ) ) ) );
	if ( $emails ) {
		$mail              = get_option( 'woocommerce_new_order_settings', array() );
		$mail              = is_array( $mail ) ? $mail : array();
		$mail['enabled']   = 'yes';
		$mail['recipient'] = implode( ', ', $emails );
		update_option( 'woocommerce_new_order_settings', $mail );
	}

	$msg = 'saved';
	if ( ! empty( $in['test'] ) ) {
		$r   = ayat_send_whatsapp_alert( 'Test alert from your website: order alerts are working.', true );
		$msg = is_wp_error( $r ) ? 'testfail' : 'testok';
		if ( is_wp_error( $r ) ) {
			set_transient( 'ayat_alert_err', $r->get_error_message(), 60 );
		}
	}
	wp_safe_redirect( admin_url( 'admin.php?page=ayat-order-alerts&msg=' . $msg ) );
	exit;
}
add_action( 'admin_post_ayat_alerts', 'ayat_alerts_save' );

/**
 * Admin page markup.
 */
function ayat_alerts_page() {
	$s      = ayat_alert_settings();
	$mail   = get_option( 'woocommerce_new_order_settings', array() );
	$emails = is_array( $mail ) && ! empty( $mail['recipient'] ) ? $mail['recipient'] : get_option( 'admin_email' );
	$msg    = isset( $_GET['msg'] ) ? sanitize_key( $_GET['msg'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Order alerts', 'ayatcollections' ); ?></h1>
		<?php if ( 'saved' === $msg ) : ?><div class="notice notice-success"><p><?php esc_html_e( 'Saved.', 'ayatcollections' ); ?></p></div><?php endif; ?>
		<?php if ( 'testok' === $msg ) : ?><div class="notice notice-success"><p><?php esc_html_e( 'Test WhatsApp sent. Check your phone.', 'ayatcollections' ); ?></p></div><?php endif; ?>
		<?php if ( 'testfail' === $msg ) : ?><div class="notice notice-error"><p><?php echo esc_html( __( 'WhatsApp test failed: ', 'ayatcollections' ) . get_transient( 'ayat_alert_err' ) ); ?></p></div><?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="ayat_alerts">
			<?php wp_nonce_field( 'ayat_alerts' ); ?>
			<h2><?php esc_html_e( '1. Email', 'ayatcollections' ); ?></h2>
			<p><?php esc_html_e( 'An email is sent for every new order (WooCommerce "New order" email).', 'ayatcollections' ); ?></p>
			<table class="form-table" role="presentation">
				<tr><th scope="row"><label for="emails"><?php esc_html_e( 'Send new-order emails to', 'ayatcollections' ); ?></label></th>
					<td><input type="text" class="regular-text" id="emails" name="emails" value="<?php echo esc_attr( $emails ); ?>">
						<p class="description"><?php esc_html_e( 'Separate several addresses with commas. If emails do not arrive, install the free "WP Mail SMTP" plugin.', 'ayatcollections' ); ?></p></td></tr>
			</table>

			<h2><?php esc_html_e( '2. WhatsApp alert to your phone', 'ayatcollections' ); ?></h2>
			<ol>
				<li><?php esc_html_e( 'Save +34 644 66 32 62 (CallMeBot) in your phone contacts.', 'ayatcollections' ); ?></li>
				<li><?php esc_html_e( 'Send it this WhatsApp message: I allow callmebot to send me messages', 'ayatcollections' ); ?></li>
				<li><?php esc_html_e( 'It replies with your API key. Paste it below, tick "On" and press Save and send test.', 'ayatcollections' ); ?></li>
			</ol>
			<table class="form-table" role="presentation">
				<tr><th scope="row"><?php esc_html_e( 'WhatsApp alerts', 'ayatcollections' ); ?></th>
					<td><label><input type="checkbox" name="wa_on" value="1" <?php checked( $s['wa_on'] ); ?>> <?php esc_html_e( 'On', 'ayatcollections' ); ?></label></td></tr>
				<tr><th scope="row"><label for="wa_phone"><?php esc_html_e( 'Your WhatsApp number', 'ayatcollections' ); ?></label></th>
					<td><input type="text" class="regular-text" id="wa_phone" name="wa_phone" value="<?php echo esc_attr( $s['wa_phone'] ); ?>" placeholder="03001234567"></td></tr>
				<tr><th scope="row"><label for="wa_key"><?php esc_html_e( 'CallMeBot API key', 'ayatcollections' ); ?></label></th>
					<td><input type="text" class="regular-text" id="wa_key" name="wa_key" value="<?php echo esc_attr( $s['wa_key'] ); ?>" autocomplete="off"></td></tr>
			</table>
			<p class="description"><?php esc_html_e( 'The alert includes the customer name, phone, city and items, and is sent through the CallMeBot service.', 'ayatcollections' ); ?></p>

			<h2><?php esc_html_e( '3. "Confirm on WhatsApp" button for customers', 'ayatcollections' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr><th scope="row"><?php esc_html_e( 'Show the button', 'ayatcollections' ); ?></th>
					<td><label><input type="checkbox" name="confirm_on" value="1" <?php checked( $s['confirm_on'] ); ?>> <?php esc_html_e( 'On the order-received page', 'ayatcollections' ); ?></label></td></tr>
				<tr><th scope="row"><label for="store_wa"><?php esc_html_e( 'Store WhatsApp number', 'ayatcollections' ); ?></label></th>
					<td><input type="text" class="regular-text" id="store_wa" name="store_wa" value="<?php echo esc_attr( $s['store_wa'] ); ?>" placeholder="<?php echo esc_attr( get_theme_mod( 'ayat_phone', '03001234567' ) ); ?>">
						<p class="description"><?php esc_html_e( 'Leave empty to use the phone number from Appearance → Customize → Store details.', 'ayatcollections' ); ?></p></td></tr>
			</table>

			<p class="submit">
				<button type="submit" class="button button-primary"><?php esc_html_e( 'Save', 'ayatcollections' ); ?></button>
				<button type="submit" class="button" name="test" value="1"><?php esc_html_e( 'Save and send test WhatsApp', 'ayatcollections' ); ?></button>
			</p>
		</form>

		<h2><?php esc_html_e( '4. Phone app (recommended)', 'ayatcollections' ); ?></h2>
		<p><?php esc_html_e( 'Install the free WooCommerce app (Android / iPhone) and log in with this website. It plays a sound and shows a notification for every new order.', 'ayatcollections' ); ?></p>
	</div>
	<?php
}
