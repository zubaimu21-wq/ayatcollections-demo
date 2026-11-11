<?php
/**
 * Homepage sections (layout based on the "Jewelry Collection Landing Page"
 * Figma template by Maker, CC BY 4.0). Every text, image and link is editable
 * in Appearance → Customize → Homepage sections.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default content.
 *
 * @return array
 */
function ayat_home_defaults() {
	return array(
		// Showcase 1: big photo + two collection cards.
		'f1_big'        => 'feature-1',
		'f1_big_alt'    => __( 'Model wearing gold chain necklaces, rings and stacked bracelets', 'ayatcollections' ),
		'f1_small'      => 'feature-1-small',
		'f1_small_cat'  => 'necklaces',
		'f1_tall'       => 'feature-1-tall',
		'f1_tall_cat'   => 'bracelets',
		// Collection story.
		'col_img'       => 'collection',
		'col_img_alt'   => __( 'Statement crystal earring with gold detailing', 'ayatcollections' ),
		'col_eyebrow'   => __( 'New Collection', 'ayatcollections' ),
		'col_title'     => __( 'The luxury of gold with the elegance of precious stones', 'ayatcollections' ),
		'col_text'      => __( 'The timeless beauty of gold meets the captivating allure of kundan, pearls and stones. Every piece is finished by hand in Pakistan to add a touch of opulence to your everyday and your celebrations.', 'ayatcollections' ),
		'col_btn'       => __( 'Explore Collection', 'ayatcollections' ),
		'col_link'      => '/new-arrivals/',
		// Showcase 2.
		'f2_big'        => 'feature-2',
		'f2_big_alt'    => __( 'Model in a white shirt wearing layered gold pendant necklaces', 'ayatcollections' ),
		'f2_small'      => 'feature-2-small',
		'f2_small_cat'  => 'earrings',
		'f2_tall'       => 'feature-2-tall',
		'f2_tall_cat'   => 'rings',
		// Best sellers.
		'best_title'    => __( 'Shop Best Sellers', 'ayatcollections' ),
		'best_btn'      => __( 'Shop All', 'ayatcollections' ),
		// Gifting.
		'gift_img'      => 'gift',
		'gift_img_alt'  => __( 'Gold necklace with drop charms on a dark green surface', 'ayatcollections' ),
		'gift_title'    => __( 'Gifting someone special?', 'ayatcollections' ),
		'gift_text'     => __( 'Make it unforgettable. Use code GIFT15 for 15% off selected pieces, gift-boxed and delivered with Cash on Delivery across Pakistan.', 'ayatcollections' ),
		'gift_btn'      => __( 'Explore Collection', 'ayatcollections' ),
		'gift_link'     => '/shop/',
		// Footer call to action.
		'cta_title'     => __( 'Looking for a personalized piece?', 'ayatcollections' ),
		'cta_text'      => __( 'We can help with that! Our team can customise a set for your wedding, mehndi or a special gift. Message us and request a quote.', 'ayatcollections' ),
		'cta_btn'       => __( 'Get in Touch', 'ayatcollections' ),
		'cta_link'      => '',
	);
}

/**
 * Read a homepage setting.
 *
 * @param string $key Key.
 * @return string
 */
function ayat_home( $key ) {
	$d = ayat_home_defaults();
	return (string) get_theme_mod( 'ayat_home_' . $key, isset( $d[ $key ] ) ? $d[ $key ] : '' );
}

/**
 * Image markup for an image setting: uploaded attachment or bundled design photo.
 *
 * @param string $key   Setting key (e.g. f1_big).
 * @param string $class CSS class.
 * @param string $sizes Sizes attribute.
 * @return string
 */
function ayat_home_img( $key, $class, $sizes = '50vw' ) {
	$id  = absint( get_theme_mod( 'ayat_home_' . $key . '_id', 0 ) );
	$alt = ayat_home( $key . '_alt' );
	if ( $id ) {
		return wp_get_attachment_image( $id, 'large', false, array( 'class' => $class, 'loading' => 'lazy', 'sizes' => $sizes, 'alt' => $alt ? $alt : get_post_meta( $id, '_wp_attachment_image_alt', true ) ) );
	}
	$file = ayat_home( $key );
	$path = AYAT_DIR . '/assets/images/design/' . $file . '.webp';
	$size = file_exists( $path ) ? wp_getimagesize( $path ) : array( 800, 800 );
	return sprintf(
		'<img class="%1$s" src="%2$s" width="%3$d" height="%4$d" alt="%5$s" loading="lazy" decoding="async">',
		esc_attr( $class ),
		esc_url( AYAT_URI . '/assets/images/design/' . $file . '.webp' ),
		(int) $size[0],
		(int) $size[1],
		esc_attr( $alt )
	);
}

/**
 * Collection card used in the showcase sections.
 *
 * @param string $key   Image key (f1_small, f1_tall, ...).
 * @param string $class Extra class (card--small / card--tall).
 */
function ayat_home_card( $key, $class ) {
	if ( ! function_exists( 'wc_price' ) ) {
		return;
	}
	$slug = ayat_home( $key . '_cat' );
	$term = $slug && taxonomy_exists( 'product_cat' ) ? get_term_by( 'slug', $slug, 'product_cat' ) : false;
	if ( ! $term ) {
		return;
	}
	$link = get_term_link( $term );
	$min  = ayat_category_min_price( $term->term_id );
	printf( '<a class="showcase__card %1$s" href="%2$s">', esc_attr( $class ), esc_url( $link ) );
	echo '<span class="showcase__media">' . ayat_home_img( $key, 'showcase__img', '(max-width: 700px) 100vw, 280px' ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '<span class="showcase__name">' . esc_html( $term->name ) . '</span>';
	if ( $min ) {
		/* translators: %s: price */
		echo '<span class="showcase__price">' . wp_kses_post( sprintf( __( 'From %s', 'ayatcollections' ), wc_price( $min ) ) ) . '</span>';
	}
	echo '</a>';
}

/**
 * Lowest published price in a category.
 *
 * @param int $term_id Term ID.
 * @return float
 */
function ayat_category_min_price( $term_id ) {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return 0;
	}
	$ids = wc_get_products(
		array(
			'status'   => 'publish',
			'limit'    => 50,
			'return'   => 'ids',
			'category' => array( get_term( $term_id )->slug ),
		)
	);
	$min = 0;
	foreach ( $ids as $id ) {
		$p = wc_get_product( $id );
		$v = $p ? (float) ( $p->is_type( 'variable' ) ? $p->get_variation_price( 'min' ) : $p->get_price() ) : 0;
		if ( $v && ( ! $min || $v < $min ) ) {
			$min = $v;
		}
	}
	return $min;
}

/**
 * Best-selling products (falls back to newest when nothing has sold yet).
 *
 * @param int $limit Count.
 * @return WC_Product[]
 */
function ayat_best_sellers( $limit = 4 ) {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return array();
	}
	$ids = get_posts(
		array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'fields'         => 'ids',
			'meta_key'       => 'total_sales', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'orderby'        => array( 'meta_value_num' => 'DESC', 'date' => 'DESC' ),
			'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => array( 'exclude-from-catalog' ),
					'operator' => 'NOT IN',
				),
			),
		)
	);
	return array_filter( array_map( 'wc_get_product', $ids ) );
}

/**
 * Social links shown in the footer.
 *
 * @return array label => [url, icon file or inline svg key]
 */
function ayat_social_links() {
	$phone = preg_replace( '/[^0-9]/', '', (string) get_theme_mod( 'ayat_phone', '' ) );
	if ( $phone && 0 === strpos( $phone, '0' ) ) {
		$phone = '92' . substr( $phone, 1 );
	}
	return array_filter(
		array(
			'WhatsApp'  => $phone ? 'https://wa.me/' . $phone : '',
			'Facebook'  => get_theme_mod( 'ayat_facebook', '' ),
			'Instagram' => get_theme_mod( 'ayat_instagram', '' ),
			'YouTube'   => get_theme_mod( 'ayat_youtube', '' ),
		)
	);
}

/**
 * Customizer: Homepage sections.
 *
 * @param WP_Customize_Manager $c Manager.
 */
function ayat_home_customize( $c ) {
	$c->add_section( 'ayat_home_sections', array( 'title' => __( 'Homepage sections', 'ayatcollections' ), 'panel' => 'ayat_home', 'priority' => 5 ) );
	$cats = array( '' => __( '— None —', 'ayatcollections' ) );
	if ( taxonomy_exists( 'product_cat' ) ) {
		foreach ( get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) ) as $t ) {
			$cats[ $t->slug ] = $t->name;
		}
	}
	$fields = array(
		'f1_big'       => array( 'image', __( 'Showcase 1 – large photo', 'ayatcollections' ) ),
		'f1_small'     => array( 'image', __( 'Showcase 1 – small card photo', 'ayatcollections' ) ),
		'f1_small_cat' => array( 'cat', __( 'Showcase 1 – small card collection', 'ayatcollections' ) ),
		'f1_tall'      => array( 'image', __( 'Showcase 1 – tall card photo', 'ayatcollections' ) ),
		'f1_tall_cat'  => array( 'cat', __( 'Showcase 1 – tall card collection', 'ayatcollections' ) ),
		'col_img'      => array( 'image', __( 'Collection story – photo', 'ayatcollections' ) ),
		'col_eyebrow'  => array( 'text', __( 'Collection story – small label', 'ayatcollections' ) ),
		'col_title'    => array( 'text', __( 'Collection story – heading', 'ayatcollections' ) ),
		'col_text'     => array( 'textarea', __( 'Collection story – text', 'ayatcollections' ) ),
		'col_btn'      => array( 'text', __( 'Collection story – button label', 'ayatcollections' ) ),
		'col_link'     => array( 'link', __( 'Collection story – button link', 'ayatcollections' ) ),
		'f2_big'       => array( 'image', __( 'Showcase 2 – large photo', 'ayatcollections' ) ),
		'f2_small'     => array( 'image', __( 'Showcase 2 – small card photo', 'ayatcollections' ) ),
		'f2_small_cat' => array( 'cat', __( 'Showcase 2 – small card collection', 'ayatcollections' ) ),
		'f2_tall'      => array( 'image', __( 'Showcase 2 – tall card photo', 'ayatcollections' ) ),
		'f2_tall_cat'  => array( 'cat', __( 'Showcase 2 – tall card collection', 'ayatcollections' ) ),
		'best_title'   => array( 'text', __( 'Best sellers – heading', 'ayatcollections' ) ),
		'best_btn'     => array( 'text', __( 'Best sellers – button label', 'ayatcollections' ) ),
		'gift_img'     => array( 'image', __( 'Gifting – photo', 'ayatcollections' ) ),
		'gift_title'   => array( 'text', __( 'Gifting – heading', 'ayatcollections' ) ),
		'gift_text'    => array( 'textarea', __( 'Gifting – text', 'ayatcollections' ) ),
		'gift_btn'     => array( 'text', __( 'Gifting – button label', 'ayatcollections' ) ),
		'gift_link'    => array( 'link', __( 'Gifting – button link', 'ayatcollections' ) ),
		'cta_title'    => array( 'text', __( 'Footer – heading', 'ayatcollections' ) ),
		'cta_text'     => array( 'textarea', __( 'Footer – text', 'ayatcollections' ) ),
		'cta_btn'      => array( 'text', __( 'Footer – button label', 'ayatcollections' ) ),
		'cta_link'     => array( 'link', __( 'Footer – button link (empty = WhatsApp)', 'ayatcollections' ) ),
	);
	$d = ayat_home_defaults();
	foreach ( $fields as $key => $f ) {
		if ( 'image' === $f[0] ) {
			$c->add_setting( "ayat_home_{$key}_id", array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
			$c->add_control( new WP_Customize_Media_Control( $c, "ayat_home_{$key}_id", array( 'label' => $f[1], 'description' => __( 'Leave empty to use the bundled photo.', 'ayatcollections' ), 'section' => 'ayat_home_sections', 'mime_type' => 'image' ) ) );
			continue;
		}
		$cb = array( 'text' => 'sanitize_text_field', 'textarea' => 'sanitize_textarea_field', 'link' => 'ayat_sanitize_link', 'cat' => 'sanitize_title' )[ $f[0] ];
		$c->add_setting( "ayat_home_{$key}", array( 'default' => $d[ $key ], 'sanitize_callback' => $cb ) );
		$args = array( 'label' => $f[1], 'section' => 'ayat_home_sections', 'type' => 'cat' === $f[0] ? 'select' : ( 'textarea' === $f[0] ? 'textarea' : 'text' ) );
		if ( 'cat' === $f[0] ) {
			$args['choices'] = $cats;
		}
		$c->add_control( "ayat_home_{$key}", $args );
	}
	$c->add_setting( 'ayat_youtube', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
	$c->add_control( 'ayat_youtube', array( 'label' => __( 'YouTube URL', 'ayatcollections' ), 'section' => 'ayat_store', 'type' => 'text' ) );
}
add_action( 'customize_register', 'ayat_home_customize', 20 );
