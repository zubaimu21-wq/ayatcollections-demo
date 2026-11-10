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
	if ( ! is_front_page() || ! function_exists( 'ayat_get_slide' ) ) {
		return;
	}
	for ( $i = 1; $i <= ayat_slide_count(); $i++ ) {
		$slide = ayat_get_slide( $i );
		if ( ! $slide ) {
			continue;
		}
		if ( $slide['image_id'] ) {
			$src    = wp_get_attachment_image_url( $slide['image_id'], 'ayat-hero' );
			$srcset = wp_get_attachment_image_srcset( $slide['image_id'], 'ayat-hero' );
			if ( $src ) {
				printf(
					'<link rel="preload" as="image" href="%1$s"%2$s imagesizes="100vw" fetchpriority="high">' . "\n",
					esc_url( $src ),
					$srcset ? ' imagesrcset="' . esc_attr( $srcset ) . '"' : ''
				);
			}
		} else {
			printf(
				'<link rel="preload" as="image" href="%1$s" imagesrcset="%2$s 960w, %1$s 1792w" imagesizes="100vw" fetchpriority="high">' . "\n",
				esc_url( $slide['fallback'] ),
				esc_url( $slide['fallback_sm'] )
			);
		}
		break; // Only the first visible slide.
	}
}
add_action( 'wp_head', 'ayat_preload_hero', 2 );

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
