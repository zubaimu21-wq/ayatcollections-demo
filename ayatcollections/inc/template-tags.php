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
	// Bundled brand logo (gold on black) with a readable wordmark beside it.
	printf(
		'<a href="%1$s" class="site-logo" rel="home"><img src="%2$s" width="232" height="240" alt="" class="site-logo__mark"><span class="site-logo__text">Ayat <span>Collections</span></span><span class="screen-reader-text">%3$s</span></a>',
		esc_url( home_url( '/' ) ),
		esc_url( AYAT_URI . '/assets/images/logo-gold.webp' ),
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
		array( __( 'New Arrivals', 'ayatcollections' ), $arrivals ? get_permalink( $arrivals ) : add_query_arg( 'orderby', 'date', $shop ) ),
		array( __( 'Collections', 'ayatcollections' ), $colls ? get_permalink( $colls ) : $shop ),
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
