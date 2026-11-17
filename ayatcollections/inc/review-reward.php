<?php
/**
 * "Review & save": a one-time discount code for customers whose website review
 * is approved.
 *
 * When the store publishes a review written on a product page (Customer Reviews
 * → Pending → Publish), and the reviewer is a verified buyer, a single-use
 * WooCommerce coupon is created. If the reviewer gave an email it is sent
 * automatically; for a phone number the admin gets a "Send on WhatsApp" button.
 *
 * Appearance → Customize → Review reward: switch on/off, percentage, validity.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

/**
 * Settings.
 *
 * @param WP_Customize_Manager $c Customizer.
 */
function ayat_rr_customize( $c ) {
	$c->add_section(
		'ayat_rr',
		array(
			'title'       => __( 'Review reward (discount)', 'ayatcollections' ),
			'priority'    => 27,
			'description' => __( 'Customers who leave an honest review on a product page get a one-time discount code once you publish their review.', 'ayatcollections' ),
		)
	);
	$fields = array(
		'ayat_rr_on'       => array( 'checkbox', __( 'Offer a discount for reviews', 'ayatcollections' ), true, 'wp_validate_boolean' ),
		'ayat_rr_percent'  => array( 'number', __( 'Discount (%)', 'ayatcollections' ), 10, 'absint' ),
		'ayat_rr_days'     => array( 'number', __( 'Code valid for (days)', 'ayatcollections' ), 30, 'absint' ),
		'ayat_rr_verified' => array( 'checkbox', __( 'Only for verified buyers (phone/email matches an order)', 'ayatcollections' ), true, 'wp_validate_boolean' ),
	);
	foreach ( $fields as $id => $f ) {
		$c->add_setting( $id, array( 'default' => $f[2], 'sanitize_callback' => $f[3] ) );
		$c->add_control( $id, array( 'section' => 'ayat_rr', 'type' => $f[0], 'label' => $f[1] ) );
	}
}
add_action( 'customize_register', 'ayat_rr_customize' );

/**
 * Is the offer on?
 *
 * @return bool
 */
function ayat_rr_on() {
	return (bool) get_theme_mod( 'ayat_rr_on', true ) && class_exists( 'WC_Coupon' );
}

/**
 * Discount percentage.
 *
 * @return int
 */
function ayat_rr_percent() {
	return max( 1, min( 90, (int) get_theme_mod( 'ayat_rr_percent', 10 ) ) );
}

/**
 * Offer banner.
 *
 * @param string $context 'product' or 'home'.
 * @return string
 */
function ayat_rr_banner( $context = 'product' ) {
	if ( ! ayat_rr_on() ) {
		return '';
	}
	$pct  = ayat_rr_percent();
	$days = max( 1, (int) get_theme_mod( 'ayat_rr_days', 30 ) );
	/* translators: %d: percent */
	$head = sprintf( __( 'Review & get %d%% off', 'ayatcollections' ), $pct );
	$text = 'home' === $context
		/* translators: %d: percent */
		? sprintf( __( 'Bought from SHAYA? Leave an honest review on the product page and get %d%% off your next order.', 'ayatcollections' ), $pct )
		/* translators: 1: percent, 2: days */
		: sprintf( __( 'Share an honest review of your SHAYA piece. Once it is approved, we send you a one-time %1$d%% discount code for your next order (valid %2$d days).', 'ayatcollections' ), $pct, $days );
	return '<div class="rr-offer rr-offer--' . esc_attr( $context ) . '"><span class="rr-offer__badge" aria-hidden="true">' . esc_html( $pct ) . '%<small>' . esc_html__( 'off', 'ayatcollections' ) . '</small></span><p><strong>' . esc_html( $head ) . '</strong>' . esc_html( $text ) . '</p></div>';
}

/**
 * Normalise a phone to 92XXXXXXXXXX for WhatsApp.
 *
 * @param string $contact Phone or email.
 * @return string
 */
function ayat_rr_wa_number( $contact ) {
	$d = preg_replace( '/\D/', '', (string) $contact );
	if ( strlen( $d ) < 10 ) {
		return '';
	}
	return '92' . substr( $d, -10 );
}

/**
 * Create the code when a website review is published.
 *
 * @param string  $new  New status.
 * @param string  $old  Old status.
 * @param WP_Post $post Post.
 */
function ayat_rr_on_publish( $new, $old, $post ) {
	if ( 'publish' !== $new || 'publish' === $old || 'ayat_review' !== $post->post_type || ! ayat_rr_on() ) {
		return;
	}
	if ( 'Website' !== get_post_meta( $post->ID, '_ayat_source', true ) || get_post_meta( $post->ID, '_ayat_reward_code', true ) ) {
		return;
	}
	$contact = trim( (string) get_post_meta( $post->ID, '_ayat_contact', true ) );
	if ( '' === $contact ) {
		update_post_meta( $post->ID, '_ayat_reward_note', 'no-contact' );
		return;
	}
	if ( get_theme_mod( 'ayat_rr_verified', true ) && ! get_post_meta( $post->ID, '_ayat_verified', true ) ) {
		update_post_meta( $post->ID, '_ayat_reward_note', 'not-verified' );
		return;
	}

	$pct  = ayat_rr_percent();
	$days = max( 1, (int) get_theme_mod( 'ayat_rr_days', 30 ) );
	$code = 'SHAYA-' . strtoupper( wp_generate_password( 6, false, false ) );

	$coupon = new WC_Coupon();
	$coupon->set_code( $code );
	$coupon->set_discount_type( 'percent' );
	$coupon->set_amount( $pct );
	$coupon->set_usage_limit( 1 );
	$coupon->set_usage_limit_per_user( 1 );
	$coupon->set_individual_use( true );
	$coupon->set_date_expires( time() + $days * DAY_IN_SECONDS );
	/* translators: %d: review ID */
	$coupon->set_description( sprintf( __( 'Review reward for review #%d', 'ayatcollections' ), $post->ID ) );
	if ( is_email( $contact ) ) {
		$coupon->set_email_restrictions( array( $contact ) );
	}
	$coupon_id = $coupon->save();
	if ( ! $coupon_id ) {
		update_post_meta( $post->ID, '_ayat_reward_note', 'error' );
		return;
	}
	update_post_meta( $post->ID, '_ayat_reward_code', $code );

	if ( is_email( $contact ) ) {
		$name    = get_the_title( $post );
		$subject = sprintf( /* translators: %d: percent */ __( 'Thank you for your review – here is %d%% off', 'ayatcollections' ), $pct );
		$body    = sprintf(
			/* translators: 1: name, 2: percent, 3: code, 4: days, 5: shop URL */
			__( "Assalam o Alaikum %1\$s,\n\nThank you for reviewing SHAYA by Aayat Collections! As promised, here is %2\$d%% off your next order.\n\nYour code: %3\$s\n(one-time use, valid for %4\$d days – enter it at checkout under \"Add coupons\")\n\nShop now: %5\$s\n\nWith love,\nSHAYA by Aayat Collections", 'ayatcollections' ),
			$name,
			$pct,
			$code,
			$days,
			home_url( '/shop/' )
		);
		$sent = wp_mail( $contact, $subject, $body );
		update_post_meta( $post->ID, '_ayat_reward_note', $sent ? 'emailed' : 'email-failed' );
	} else {
		update_post_meta( $post->ID, '_ayat_reward_note', 'send-whatsapp' );
	}
}
add_action( 'transition_post_status', 'ayat_rr_on_publish', 20, 3 );

/**
 * Admin box on the review screen.
 */
function ayat_rr_meta_box() {
	add_meta_box( 'ayat_rr_box', __( 'Review reward (discount code)', 'ayatcollections' ), 'ayat_rr_meta_box_html', 'ayat_review', 'side', 'default' );
}
add_action( 'add_meta_boxes', 'ayat_rr_meta_box' );

/**
 * Admin box HTML.
 *
 * @param WP_Post $post Review.
 */
function ayat_rr_meta_box_html( $post ) {
	$code    = get_post_meta( $post->ID, '_ayat_reward_code', true );
	$note    = get_post_meta( $post->ID, '_ayat_reward_note', true );
	$contact = get_post_meta( $post->ID, '_ayat_contact', true );
	if ( 'Website' !== get_post_meta( $post->ID, '_ayat_source', true ) ) {
		echo '<p>' . esc_html__( 'Only reviews written on the website get an automatic code.', 'ayatcollections' ) . '</p>';
		return;
	}
	if ( ! $code ) {
		$msgs = array(
			'no-contact'   => __( 'No code: the customer did not give a phone or email.', 'ayatcollections' ),
			'not-verified' => __( 'No code: phone/email does not match an order (not a verified buyer).', 'ayatcollections' ),
			'error'        => __( 'The code could not be created. Try publishing again.', 'ayatcollections' ),
		);
		echo '<p>' . esc_html( $msgs[ $note ] ?? __( 'A code is created automatically when you publish this review.', 'ayatcollections' ) ) . '</p>';
		return;
	}
	echo '<p><strong>' . esc_html__( 'Code:', 'ayatcollections' ) . '</strong> <code>' . esc_html( $code ) . '</code></p>';
	if ( 'emailed' === $note ) {
		echo '<p>' . esc_html__( 'Sent to the customer by email.', 'ayatcollections' ) . '</p>';
		return;
	}
	if ( 'email-failed' === $note ) {
		echo '<p>' . esc_html__( 'The email could not be sent – please send the code to the customer yourself.', 'ayatcollections' ) . '</p>';
	}
	$wa = ayat_rr_wa_number( $contact );
	if ( $wa ) {
		$msg = sprintf(
			/* translators: 1: name, 2: percent, 3: code */
			__( 'Assalam o Alaikum %1$s! Thank you for your review of SHAYA. Here is your %2$d%% discount code for your next order: %3$s (one-time use).', 'ayatcollections' ),
			get_the_title( $post ),
			ayat_rr_percent(),
			$code
		);
		echo '<p><a class="button button-primary" href="' . esc_url( 'https://wa.me/' . $wa . '?text=' . rawurlencode( $msg ) ) . '" target="_blank" rel="noopener">' . esc_html__( 'Send code on WhatsApp', 'ayatcollections' ) . '</a></p>';
	}
}
