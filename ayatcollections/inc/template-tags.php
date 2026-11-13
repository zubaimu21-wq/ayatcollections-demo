<?php
/**
 * Small template helpers.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

/**
 * Inline SVG icon.
 *
 * @param string $name Icon name.
 * @return string
 */
function ayat_icon( $name ) {
	$paths = array(
		'search' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
		'bag'    => '<path d="M5 8h14l-1.2 12H6.2L5 8Z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/>',
		'user'   => '<circle cx="12" cy="8" r="4"/><path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6"/>',
		'menu'   => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'close'  => '<path d="M6 6l12 12M18 6 6 18"/>',
		'prev'   => '<path d="m15 5-7 7 7 7"/>',
		'next'   => '<path d="m9 5 7 7-7 7"/>',
		'pause'  => '<path d="M9 5v14M15 5v14"/>',
		'play'   => '<path d="M8 5v14l11-7z"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return '<svg class="icon icon-' . esc_attr( $name ) . '" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths[ $name ] . '</svg>';
}

/**
 * Site logo: Customizer logo if set, otherwise the bundled wordmark.
 */
function ayat_logo() {
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}
	// Bundled SHAYA by Aayat Collections logo (transparent, gold).
	printf(
		'<a href="%1$s" class="site-logo" rel="home"><img src="%2$s" width="277" height="360" alt="" class="site-logo__mark"><span class="screen-reader-text">%3$s</span></a>',
		esc_url( home_url( '/' ) ),
		esc_url( AYAT_URI . '/assets/images/logo-shaya.webp' ),
		esc_html( get_bloginfo( 'name' ) ? get_bloginfo( 'name' ) : 'Ayat Collections' )
	);
}

/**
 * Fallback primary menu used until a menu is assigned in Appearance → Menus.
 */
function ayat_fallback_menu() {
	$shop     = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
	$arrivals = get_page_by_path( 'new-arrivals' );
	$colls    = get_page_by_path( 'collections' );

	$items = array(
		array( __( 'Shop', 'ayatcollections' ), $shop ),
		array( __( 'Collections', 'ayatcollections' ), $colls ? get_permalink( $colls ) : $shop ),
		array( __( 'New Arrivals', 'ayatcollections' ), $arrivals ? get_permalink( $arrivals ) : add_query_arg( 'orderby', 'date', $shop ) ),
	);

	echo '<ul id="primary-menu" class="menu">';
	foreach ( $items as $item ) {
		printf( '<li class="menu-item"><a href="%s">%s</a></li>', esc_url( $item[1] ), esc_html( $item[0] ) );
	}
	echo '</ul>';
}

/**
 * Number of items in the bag.
 *
 * @return int
 */
function ayat_cart_count() {
	if ( function_exists( 'WC' ) && WC()->cart ) {
		return (int) WC()->cart->get_cart_contents_count();
	}
	return 0;
}

/**
 * Bag link markup (also used for AJAX fragments).
 *
 * @return string
 */
function ayat_bag_link() {
	$count = ayat_cart_count();
	$url   = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' );
	/* translators: %d: number of items */
	$label = sprintf( _n( 'Bag, %d item', 'Bag, %d items', $count, 'ayatcollections' ), $count );

	return sprintf(
		'<a href="%1$s" class="header-icon header-bag" aria-label="%2$s">%3$s<span class="header-icon__text">%4$s</span><span class="bag-count%5$s" aria-hidden="true">%6$d</span></a>',
		esc_url( $url ),
		esc_attr( $label ),
		ayat_icon( 'bag' ),
		esc_html__( 'Bag', 'ayatcollections' ),
		$count ? '' : ' is-empty',
		$count
	);
}

/**
 * Gold line icon for a jewellery category (used in the header category strip).
 *
 * @param string $slug Category slug.
 * @return string SVG markup.
 */
function ayat_cat_icon( $slug ) {
	$icons = array(
		'all'                   => '<path d="M12 3v4M12 17v4M3 12h4M17 12h4"/><path d="m12 8 1.2 2.8L16 12l-2.8 1.2L12 16l-1.2-2.8L8 12l2.8-1.2Z"/>',
		'earrings'              => '<path d="M8.5 3.5v3"/><circle cx="8.5" cy="7.8" r="1.2"/><path d="M8.5 9 6 14a2.6 2.6 0 1 0 5 0Z"/><path d="M15.5 3.5v3"/><circle cx="15.5" cy="7.8" r="1.2"/><path d="M15.5 9v2.5"/><circle cx="15.5" cy="14" r="2"/>',
		'bracelets'             => '<ellipse cx="12" cy="12" rx="8.5" ry="5"/><circle cx="5" cy="9.8" r=".9"/><circle cx="9" cy="7.4" r=".9"/><circle cx="15" cy="7.4" r=".9"/><circle cx="19" cy="9.8" r=".9"/><circle cx="12" cy="17" r="1.3"/>',
		'anja'                  => '<circle cx="12" cy="4.8" r="1.8"/><path d="M12 6.6v3.2M12 9.8 7.5 14M12 9.8l4.5 4.2"/><circle cx="12" cy="9.8" r="1"/><path d="M4.5 15.5c2.5 3.3 12.5 3.3 15 0"/>',
		'anklets'               => '<path d="M8 3v8.5c0 3.5 2.4 6.5 6.5 7.5H18a2 2 0 0 0 0-4h-2.6c-2 0-3.4-1.3-3.4-3.6V3"/><path d="M6 11.5c2.4 1.6 8.3 1.6 10.7 0"/><circle cx="11.3" cy="14.2" r=".9"/>',
		'rings'                 => '<circle cx="12" cy="14.5" r="6"/><path d="m9.4 6.2 2.6-3 2.6 3-2.6 2.4Z"/>',
		'hand-cuffs'            => '<path d="M5.5 9.5a7 7 0 1 0 13 0"/><path d="M5.5 9.5c0-1 .6-1.6 1.4-1.6M18.5 9.5c0-1-.6-1.6-1.4-1.6"/><path d="M8 15.5h8"/>',
		'adjustable-hand-cuffs' => '<path d="M6.5 9a6 6 0 1 0 11 0"/><path d="M2.5 9h4M17.5 9h4M4 7.4 2.5 9 4 10.6M20 7.4 21.5 9 20 10.6"/>',
		'nose-rings'            => '<path d="M15.6 9.2a6 6 0 1 1-7.2 0"/><circle cx="12" cy="7.5" r="1.7"/>',
		'necklaces'             => '<path d="M4 3.5c0 7.5 3.6 12 8 12s8-4.5 8-12"/><path d="M12 15.5v1.6"/><path d="m12 17.1-2.2 2.6L12 22l2.2-2.3Z"/>',
		'permanent-bracelets'   => '<path d="M12 12c-1.6-2.4-3-3.6-4.6-3.6a3.6 3.6 0 0 0 0 7.2c1.6 0 3-1.2 4.6-3.6 1.6-2.4 3-3.6 4.6-3.6a3.6 3.6 0 0 1 0 7.2c-1.6 0-3-1.2-4.6-3.6Z"/>',
		'charms'                => '<path d="M4 4c3.5 3 12.5 3 16 0"/><path d="M12 6.4v2.4"/><path d="M12 20s-5.2-3.1-5.2-6.6a2.6 2.6 0 0 1 5.2-1 2.6 2.6 0 0 1 5.2 1C17.2 16.9 12 20 12 20Z"/>',
	);
	$path = isset( $icons[ $slug ] ) ? $icons[ $slug ] : $icons['all'];
	return '<svg class="cat-icon" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $path . '</svg>';
}
