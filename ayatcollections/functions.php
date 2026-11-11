<?php
/**
 * Ayatcollections theme bootstrap.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

define( 'AYAT_VERSION', '1.0.0' );
define( 'AYAT_DIR', get_template_directory() );
define( 'AYAT_URI', get_template_directory_uri() );

require AYAT_DIR . '/inc/template-tags.php';
require AYAT_DIR . '/inc/customizer.php';
require AYAT_DIR . '/inc/collections.php';
require AYAT_DIR . '/inc/seo.php';
require AYAT_DIR . '/inc/home.php';

if ( class_exists( 'WooCommerce' ) ) {
	require AYAT_DIR . '/inc/woocommerce.php';
}

if ( is_admin() ) {
	require AYAT_DIR . '/inc/sample-import.php';
}

/**
 * Theme supports, menus and image sizes.
 */
function ayat_setup() {
	load_theme_textdomain( 'ayatcollections', AYAT_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 320,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 600,
			'single_image_width'    => 900,
			'product_grid'          => array(
				'default_rows'    => 3,
				'min_rows'        => 1,
				'default_columns' => 4,
				'min_columns'     => 2,
				'max_columns'     => 4,
			),
		)
	);
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary menu', 'ayatcollections' ),
			'footer'  => __( 'Footer menu', 'ayatcollections' ),
		)
	);

	add_image_size( 'ayat-hero', 1920, 820, true );
	add_image_size( 'ayat-card', 720, 900, true );
}
add_action( 'after_setup_theme', 'ayat_setup' );

/**
 * Widget area for the shop sidebar (filters).
 */
function ayat_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Shop filters', 'ayatcollections' ),
			'id'            => 'shop-filters',
			'description'   => __( 'Shown beside the shop and collection pages. Add WooCommerce filter widgets or blocks here.', 'ayatcollections' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'ayat_widgets_init' );

/**
 * Styles and scripts.
 */
function ayat_assets() {
	wp_enqueue_style( 'ayat-fonts', 'https://fonts.googleapis.com/css2?family=Kaisei+Decol:wght@400;700&family=Inter:wght@300;400;500;600&display=swap', array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	wp_enqueue_style( 'ayat-style', get_stylesheet_uri(), array(), AYAT_VERSION );

	wp_enqueue_script( 'ayat-site', AYAT_URI . '/assets/js/site.js', array(), AYAT_VERSION, array( 'strategy' => 'defer', 'in_footer' => true ) );

	if ( is_front_page() ) {
		wp_enqueue_script( 'ayat-slider', AYAT_URI . '/assets/js/slider.js', array(), AYAT_VERSION, array( 'strategy' => 'defer', 'in_footer' => true ) );
	}
}
add_action( 'wp_enqueue_scripts', 'ayat_assets' );

/**
 * Preconnect to the font host.
 *
 * @param array  $urls          URLs.
 * @param string $relation_type Relation.
 * @return array
 */
function ayat_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'ayat_resource_hints', 10, 2 );
