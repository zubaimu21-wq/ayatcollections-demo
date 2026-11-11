<?php
/**
 * SEO and performance helpers. The theme leaves titles, meta descriptions,
 * sitemaps and schema to WordPress core and SEO plugins (Yoast, Rank Math,
 * SEOPress, AIOSEO all work with title-tag support).
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

/**
 * Preload the first hero image on the homepage so it is the LCP candidate.
 */
function ayat_preload_hero() {
	if ( ! is_front_page() || ! function_exists( 'ayat_home_img_url' ) ) {
		return;
	}
	printf( '<link rel="preload" as="image" href="%s" fetchpriority="high">' . "
", esc_url( ayat_home_img_url( 'hero_img' ) ) );
}
add_action( 'wp_head', 'ayat_preload_hero', 2 );

/**
 * Browser-tab and home-screen icon from the brand pack, used until a Site Icon
 * is set in Appearance → Customize → Site Identity.
 */
function ayat_fallback_site_icon() {
	if ( has_site_icon() ) {
		return;
	}
	$base = AYAT_URI . '/assets/images/';
	printf( '<link rel="icon" href="%s" sizes="32x32">' . "\n", esc_url( $base . 'favicon-32.png' ) );
	printf( '<link rel="icon" href="%s" sizes="192x192">' . "\n", esc_url( $base . 'site-icon-192.png' ) );
	printf( '<link rel="apple-touch-icon" href="%s">' . "\n", esc_url( $base . 'apple-touch-icon.png' ) );
	echo '<meta name="theme-color" content="#080808">' . "\n";
}
add_action( 'wp_head', 'ayat_fallback_site_icon', 3 );

/**
 * On the homepage everything below the hero is below the fold: make sure
 * product and collection images there are lazy-loaded (the hero sets its own
 * eager / high-priority attributes and is not affected).
 *
 * @param array $attr Image attributes.
 * @return array
 */
function ayat_lazy_home_images( $attr ) {
	if ( is_front_page() && ! is_admin() && empty( $attr['loading'] ) && ( empty( $attr['class'] ) || false === strpos( $attr['class'], 'hero__img' ) ) ) {
		$attr['loading'] = 'lazy';
		unset( $attr['fetchpriority'] );
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'ayat_lazy_home_images', 20 );

/**
 * Show a clear warning in the admin bar while search engines are blocked
 * (Settings → Reading → "Discourage search engines"). That core option sends
 * a noindex robots tag that every SEO plugin respects.
 *
 * @param WP_Admin_Bar $bar Admin bar.
 */
function ayat_indexing_badge( $bar ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$public = (int) get_option( 'blog_public', 1 );
	$bar->add_node(
		array(
			'id'    => 'ayat-indexing',
			'title' => $public ? __( 'Search: indexable', 'ayatcollections' ) : __( 'Staging: not indexed', 'ayatcollections' ),
			'href'  => admin_url( 'options-reading.php' ),
			'meta'  => array( 'class' => $public ? 'ayat-index-on' : 'ayat-index-off' ),
		)
	);
}
add_action( 'admin_bar_menu', 'ayat_indexing_badge', 100 );

/**
 * Admin-bar badge colours.
 */
function ayat_indexing_badge_css() {
	if ( ! is_admin_bar_showing() ) {
		return;
	}
	echo '<style>#wpadminbar .ayat-index-off>.ab-item{background:#8a1c1c!important;color:#fff!important}#wpadminbar .ayat-index-on>.ab-item{background:#1f6b3a!important;color:#fff!important}</style>';
}
add_action( 'wp_head', 'ayat_indexing_badge_css' );
add_action( 'admin_head', 'ayat_indexing_badge_css' );
