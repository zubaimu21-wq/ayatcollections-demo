<?php
/**
 * Customizer helpers and the "Store details" section.
 * Homepage sections are registered in inc/home.php.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

/**
 * Sanitize a checkbox.
 *
 * @param mixed $value Value.
 * @return bool
 */
function ayat_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/**
 * Sanitize a link that may be relative ("/shop/") or absolute.
 *
 * @param string $value URL.
 * @return string
 */
function ayat_sanitize_link( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}
	if ( '/' === $value[0] && ( ! isset( $value[1] ) || '/' !== $value[1] ) ) {
		return '/' . ltrim( preg_replace( '/[^A-Za-z0-9\-._~\/?#\[\]@!$&\'()*+,;=%]/', '', $value ), '/' );
	}
	if ( 0 === strpos( $value, '#' ) ) {
		return preg_replace( '/[^A-Za-z0-9\-_#]/', '', $value );
	}
	return esc_url_raw( $value );
}

/**
 * Resolve a stored link: relative paths are made absolute against the site URL.
 *
 * @param string $value Stored URL.
 * @return string
 */
function ayat_resolve_link( $value ) {
	if ( '' === $value ) {
		return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
	}
	if ( '/' === $value[0] ) {
		return home_url( $value );
	}
	if ( '#' === $value[0] ) {
		return home_url( '/' ) . $value;
	}
	return $value;
}

/**
 * Store details (phone, email, socials, announcement).
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function ayat_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'ayat_home',
		array(
			'title'       => __( 'Homepage', 'ayatcollections' ),
			'description' => __( 'Edit every homepage section: photos, headings, text, buttons and links.', 'ayatcollections' ),
			'priority'    => 25,
		)
	);

	$wp_customize->add_section( 'ayat_store', array( 'title' => __( 'Store details', 'ayatcollections' ), 'priority' => 26 ) );
	$store = array(
		'ayat_announcement' => array( __( 'Announcement bar (leave empty to hide)', 'ayatcollections' ), __( 'Cash on Delivery across Pakistan', 'ayatcollections' ), 'sanitize_text_field' ),
		'ayat_phone'        => array( __( 'WhatsApp / phone (e.g. 0300 1234567)', 'ayatcollections' ), '0311 0290489', 'sanitize_text_field' ),
		'ayat_email'        => array( __( 'Support email', 'ayatcollections' ), '', 'sanitize_email' ),
		'ayat_address'      => array( __( 'Boutique address', 'ayatcollections' ), '', 'sanitize_text_field' ),
		'ayat_instagram'    => array( __( 'Instagram URL', 'ayatcollections' ), 'https://www.instagram.com/aayatcollections38/', 'esc_url_raw' ),
		'ayat_facebook'     => array( __( 'Facebook URL', 'ayatcollections' ), 'https://www.facebook.com/share/1GpbKWSSMf/', 'esc_url_raw' ),
		'ayat_tiktok'       => array( __( 'TikTok URL', 'ayatcollections' ), 'https://www.tiktok.com/@aayat.collections', 'esc_url_raw' ),
		'ayat_youtube'      => array( __( 'YouTube URL', 'ayatcollections' ), 'https://www.youtube.com/@aayatcollection38', 'esc_url_raw' ),
	);
	foreach ( $store as $key => $f ) {
		$wp_customize->add_setting( $key, array( 'default' => $f[1], 'sanitize_callback' => $f[2] ) );
		$wp_customize->add_control( $key, array( 'label' => $f[0], 'section' => 'ayat_store', 'type' => 'text' ) );
	}
}
add_action( 'customize_register', 'ayat_customize_register' );
