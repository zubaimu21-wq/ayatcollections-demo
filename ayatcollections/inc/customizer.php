<?php
/**
 * Customizer: hero slides, focal points, announcement bar and footer text.
 * Appearance → Customize → Homepage slider.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

/**
 * Number of hero slides offered in the Customizer.
 */
function ayat_slide_count() {
	return 3;
}

/**
 * Default content for each slide. Images fall back to the photos bundled with the theme.
 *
 * @return array
 */
function ayat_slide_defaults() {
	return array(
		1 => array(
			'file'         => 'hero-1',
			'eyebrow'      => __( 'Bridal Edit', 'ayatcollections' ),
			'headline'     => __( 'Kundan made for your moment', 'ayatcollections' ),
			'text'         => __( 'Statement chokers and sets, hand-finished in gold tone.', 'ayatcollections' ),
			'button_label' => __( 'Shop Necklaces', 'ayatcollections' ),
			'button_url'   => '/product-category/necklaces/',
			'alt'          => __( 'Gold kundan choker with emerald and ruby stones on deep purple velvet', 'ayatcollections' ),
			'focal_x'      => 72,
			'focal_y'      => 50,
			'm_focal_x'    => 74,
			'm_focal_y'    => 50,
		),
		2 => array(
			'file'         => 'hero-2',
			'eyebrow'      => __( 'New Arrivals', 'ayatcollections' ),
			'headline'     => __( 'Jhumkas that catch the light', 'ayatcollections' ),
			'text'         => __( 'Fresh earrings and rings, added every week.', 'ayatcollections' ),
			'button_label' => __( 'Shop New Arrivals', 'ayatcollections' ),
			'button_url'   => '/new-arrivals/',
			'alt'          => __( 'Gold jhumka earrings with pearls and a gold ring on plum silk', 'ayatcollections' ),
			'focal_x'      => 78,
			'focal_y'      => 55,
			'm_focal_x'    => 80,
			'm_focal_y'    => 55,
		),
		3 => array(
			'file'         => 'hero-3',
			'eyebrow'      => __( 'Everyday Gold', 'ayatcollections' ),
			'headline'     => __( 'Bangles and bracelets to stack', 'ayatcollections' ),
			'text'         => __( 'Light, polished pieces for every day of the week.', 'ayatcollections' ),
			'button_label' => __( 'Shop Bracelets', 'ayatcollections' ),
			'button_url'   => '/product-category/bracelets/',
			'alt'          => __( 'Stacked gold bangles and a zircon tennis bracelet on black marble with purple satin', 'ayatcollections' ),
			'focal_x'      => 70,
			'focal_y'      => 50,
			'm_focal_x'    => 72,
			'm_focal_y'    => 50,
		),
	);
}

/**
 * Read one slide's settings with defaults applied.
 *
 * @param int $i Slide number (1-based).
 * @return array|null Null when the slide is disabled.
 */
function ayat_get_slide( $i ) {
	$defaults = ayat_slide_defaults();
	$d        = isset( $defaults[ $i ] ) ? $defaults[ $i ] : end( $defaults );

	if ( ! get_theme_mod( "ayat_slide_{$i}_enabled", true ) ) {
		return null;
	}

	$image_id  = absint( get_theme_mod( "ayat_slide_{$i}_image", 0 ) );
	$mobile_id = absint( get_theme_mod( "ayat_slide_{$i}_mobile_image", 0 ) );

	$slide = array(
		'image_id'     => $image_id,
		'mobile_id'    => $mobile_id,
		'fallback'     => AYAT_URI . '/assets/images/hero/' . $d['file'] . '.webp',
		'fallback_sm'  => AYAT_URI . '/assets/images/hero/' . $d['file'] . '-960.webp',
		'eyebrow'      => get_theme_mod( "ayat_slide_{$i}_eyebrow", $d['eyebrow'] ),
		'headline'     => get_theme_mod( "ayat_slide_{$i}_headline", $d['headline'] ),
		'text'         => get_theme_mod( "ayat_slide_{$i}_text", $d['text'] ),
		'button_label' => get_theme_mod( "ayat_slide_{$i}_button_label", $d['button_label'] ),
		'button_url'   => get_theme_mod( "ayat_slide_{$i}_button_url", $d['button_url'] ),
		'alt'          => get_theme_mod( "ayat_slide_{$i}_alt", '' ),
		'focal_x'      => (int) get_theme_mod( "ayat_slide_{$i}_focal_x", $d['focal_x'] ),
		'focal_y'      => (int) get_theme_mod( "ayat_slide_{$i}_focal_y", $d['focal_y'] ),
		'm_focal_x'    => (int) get_theme_mod( "ayat_slide_{$i}_m_focal_x", $d['m_focal_x'] ),
		'm_focal_y'    => (int) get_theme_mod( "ayat_slide_{$i}_m_focal_y", $d['m_focal_y'] ),
	);

	if ( '' === $slide['alt'] ) {
		$slide['alt'] = $image_id ? (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true ) : '';
		if ( '' === $slide['alt'] ) {
			$slide['alt'] = $image_id ? '' : $d['alt'];
		}
	}

	return $slide;
}

/**
 * Sanitize a 0–100 percentage.
 *
 * @param mixed $value Value.
 * @return int
 */
function ayat_sanitize_percent( $value ) {
	return max( 0, min( 100, (int) $value ) );
}

/**
 * Sanitize the slider interval (5–6 seconds).
 *
 * @param mixed $value Value in ms.
 * @return int
 */
function ayat_sanitize_interval( $value ) {
	return max( 5000, min( 6000, (int) $value ) );
}

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
		// Relative path: keep only characters that are valid in a URL path/query.
		return '/' . ltrim( preg_replace( '/[^A-Za-z0-9\-._~\/?#\[\]@!$&\'()*+,;=%]/', '', $value ), '/' );
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
	return $value;
}

/**
 * Register Customizer controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function ayat_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'ayat_home',
		array(
			'title'       => __( 'Homepage slider', 'ayatcollections' ),
			'description' => __( 'Edit the hero slides. Focal point sets which part of the photo stays visible when the banner crops it (0 = left/top, 100 = right/bottom).', 'ayatcollections' ),
			'priority'    => 25,
		)
	);

	$wp_customize->add_section(
		'ayat_slider_general',
		array(
			'title' => __( 'Slider settings', 'ayatcollections' ),
			'panel' => 'ayat_home',
		)
	);
	$wp_customize->add_setting( 'ayat_slider_interval', array( 'default' => 5500, 'sanitize_callback' => 'ayat_sanitize_interval' ) );
	$wp_customize->add_control(
		'ayat_slider_interval',
		array(
			'label'       => __( 'Time per slide (ms)', 'ayatcollections' ),
			'section'     => 'ayat_slider_general',
			'type'        => 'number',
			'input_attrs' => array( 'min' => 5000, 'max' => 6000, 'step' => 250 ),
		)
	);

	$defaults = ayat_slide_defaults();

	for ( $i = 1; $i <= ayat_slide_count(); $i++ ) {
		$d       = $defaults[ $i ];
		$section = "ayat_slide_{$i}";

		$wp_customize->add_section(
			$section,
			array(
				/* translators: %d: slide number */
				'title' => sprintf( __( 'Slide %d', 'ayatcollections' ), $i ),
				'panel' => 'ayat_home',
			)
		);

		$wp_customize->add_setting( "{$section}_enabled", array( 'default' => true, 'sanitize_callback' => 'ayat_sanitize_checkbox' ) );
		$wp_customize->add_control( "{$section}_enabled", array( 'label' => __( 'Show this slide', 'ayatcollections' ), 'section' => $section, 'type' => 'checkbox' ) );

		$wp_customize->add_setting( "{$section}_image", array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				"{$section}_image",
				array(
					'label'       => __( 'Slide photo (wide, about 1920×820)', 'ayatcollections' ),
					'description' => __( 'Leave empty to use the bundled sample photo.', 'ayatcollections' ),
					'section'     => $section,
					'mime_type'   => 'image',
				)
			)
		);

		$wp_customize->add_setting( "{$section}_mobile_image", array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				"{$section}_mobile_image",
				array(
					'label'       => __( 'Optional mobile photo', 'ayatcollections' ),
					'description' => __( 'Shown above the text on phones. Defaults to the main photo.', 'ayatcollections' ),
					'section'     => $section,
					'mime_type'   => 'image',
				)
			)
		);

		$text_fields = array(
			'alt'          => array( __( 'Image alt text', 'ayatcollections' ), '', 'sanitize_text_field' ),
			'eyebrow'      => array( __( 'Small label above headline', 'ayatcollections' ), $d['eyebrow'], 'sanitize_text_field' ),
			'headline'     => array( __( 'Headline', 'ayatcollections' ), $d['headline'], 'sanitize_text_field' ),
			'text'         => array( __( 'Supporting line', 'ayatcollections' ), $d['text'], 'sanitize_text_field' ),
			'button_label' => array( __( 'Button label', 'ayatcollections' ), $d['button_label'], 'sanitize_text_field' ),
			'button_url'   => array( __( 'Button link (e.g. /product-category/rings/)', 'ayatcollections' ), $d['button_url'], 'ayat_sanitize_link' ),
		);
		foreach ( $text_fields as $key => $f ) {
			$wp_customize->add_setting( "{$section}_{$key}", array( 'default' => $f[1], 'sanitize_callback' => $f[2] ) );
			$wp_customize->add_control( "{$section}_{$key}", array( 'label' => $f[0], 'section' => $section, 'type' => 'text' ) );
		}

		$focal = array(
			'focal_x'   => __( 'Desktop focal point – horizontal %', 'ayatcollections' ),
			'focal_y'   => __( 'Desktop focal point – vertical %', 'ayatcollections' ),
			'm_focal_x' => __( 'Mobile focal point – horizontal %', 'ayatcollections' ),
			'm_focal_y' => __( 'Mobile focal point – vertical %', 'ayatcollections' ),
		);
		foreach ( $focal as $key => $label ) {
			$wp_customize->add_setting( "{$section}_{$key}", array( 'default' => $d[ $key ], 'sanitize_callback' => 'ayat_sanitize_percent' ) );
			$wp_customize->add_control(
				"{$section}_{$key}",
				array(
					'label'       => $label,
					'section'     => $section,
					'type'        => 'range',
					'input_attrs' => array( 'min' => 0, 'max' => 100, 'step' => 1 ),
				)
			);
		}
	}

	// Store details shown in the footer.
	$wp_customize->add_section( 'ayat_store', array( 'title' => __( 'Store details', 'ayatcollections' ), 'priority' => 26 ) );
	$store = array(
		'ayat_intro_title'  => array( __( 'Homepage intro heading (leave empty to hide)', 'ayatcollections' ), __( 'Jewellery for every celebration, and every day in between', 'ayatcollections' ) ),
		'ayat_intro_text'   => array( __( 'Homepage intro text', 'ayatcollections' ), __( 'Kundan, pearls and polished gold tone — designed in Pakistan and finished by hand, at prices that let you wear something special more often.', 'ayatcollections' ) ),
		'ayat_announcement' => array( __( 'Announcement bar (leave empty to hide)', 'ayatcollections' ), __( 'Cash on Delivery across Pakistan', 'ayatcollections' ) ),
		'ayat_footer_about' => array( __( 'Footer description', 'ayatcollections' ), __( 'Artificial jewellery designed in Pakistan — kundan, pearls and everyday gold tone.', 'ayatcollections' ) ),
		'ayat_phone'        => array( __( 'WhatsApp / phone', 'ayatcollections' ), '' ),
		'ayat_email'        => array( __( 'Support email', 'ayatcollections' ), '' ),
		'ayat_instagram'    => array( __( 'Instagram URL', 'ayatcollections' ), '' ),
		'ayat_facebook'     => array( __( 'Facebook URL', 'ayatcollections' ), '' ),
	);
	foreach ( $store as $key => $f ) {
		$cb = in_array( $key, array( 'ayat_instagram', 'ayat_facebook' ), true ) ? 'esc_url_raw' : 'sanitize_text_field';
		$wp_customize->add_setting( $key, array( 'default' => $f[1], 'sanitize_callback' => $cb ) );
		$wp_customize->add_control( $key, array( 'label' => $f[0], 'section' => 'ayat_store', 'type' => 'text' ) );
	}
}
add_action( 'customize_register', 'ayat_customize_register' );
