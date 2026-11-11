<?php
/**
 * Homepage content. Section order follows the Weblium "Jewelry" template;
 * every heading, text, photo and link is editable in
 * Appearance → Customize → Homepage. Bundled photos are DEMO images from
 * Unsplash (see assets/images/demo/credits.json) — replace them with your own.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

/**
 * Field definitions: key => [section, type, label, default].
 * Image defaults are file names in assets/images/demo/ (without .webp).
 *
 * @return array
 */
function ayat_home_fields() {
	return array(
		// 1. Hero.
		'hero_title'      => array( 'hero', 'text', __( 'Heading', 'ayatcollections' ), __( 'Royal Gold', 'ayatcollections' ) ),
		'hero_text'       => array( 'hero', 'textarea', __( 'Text', 'ayatcollections' ), __( 'Artificial jewellery made for the special people in your life — kundan, pearls and polished gold tone, designed in Pakistan and delivered with Cash on Delivery.', 'ayatcollections' ) ),
		'hero_btn'        => array( 'hero', 'text', __( 'Button label', 'ayatcollections' ), __( 'Shop Now', 'ayatcollections' ) ),
		'hero_link'       => array( 'hero', 'link', __( 'Button link', 'ayatcollections' ), '/shop/' ),
		'hero_img'        => array( 'hero', 'image', __( 'Photo', 'ayatcollections' ), 'demo-hero' ),
		// 2. Collections.
		'col_title'       => array( 'collections', 'text', __( 'Collections heading', 'ayatcollections' ), __( 'Our Collections', 'ayatcollections' ) ),
		'new_title'       => array( 'collections', 'text', __( 'New arrivals heading', 'ayatcollections' ), __( 'New Arrivals', 'ayatcollections' ) ),
		// 3. Bridal / bespoke gallery.
		'bes_title'       => array( 'bespoke', 'text', __( 'Heading', 'ayatcollections' ), __( 'Bridal Jewellery', 'ayatcollections' ) ),
		'bes_img'         => array( 'bespoke', 'image', __( 'Large photo', 'ayatcollections' ), 'demo-bespoke-main' ),
		'bes_img2'        => array( 'bespoke', 'image', __( 'Small photo 1', 'ayatcollections' ), 'demo-bespoke-1' ),
		'bes_img3'        => array( 'bespoke', 'image', __( 'Small photo 2', 'ayatcollections' ), 'demo-bespoke-2' ),
		'bes_link'        => array( 'bespoke', 'link', __( 'Link', 'ayatcollections' ), '/product-category/necklaces/' ),
		// 4. What makes us special.
		'sp_title'        => array( 'special', 'text', __( 'Heading', 'ayatcollections' ), __( 'What Makes Us Special?', 'ayatcollections' ) ),
		'sp1_img'         => array( 'special', 'image', __( 'Card 1 photo', 'ayatcollections' ), 'demo-special-1' ),
		'sp1_text'        => array( 'special', 'text', __( 'Card 1 caption', 'ayatcollections' ), __( 'Hand-finished Detail', 'ayatcollections' ) ),
		'sp2_img'         => array( 'special', 'image', __( 'Card 2 photo', 'ayatcollections' ), 'demo-special-2' ),
		'sp2_text'        => array( 'special', 'text', __( 'Card 2 caption', 'ayatcollections' ), __( 'Stones, Kundan & Pearls', 'ayatcollections' ) ),
		'sp3_img'         => array( 'special', 'image', __( 'Card 3 photo', 'ayatcollections' ), 'demo-special-3' ),
		'sp3_text'        => array( 'special', 'text', __( 'Card 3 caption', 'ayatcollections' ), __( 'Designs for Every Occasion', 'ayatcollections' ) ),
		// 5. About.
		'about_title'     => array( 'about', 'text', __( 'Heading', 'ayatcollections' ), __( 'About Us', 'ayatcollections' ) ),
		'about_text'      => array( 'about', 'textarea', __( 'Text', 'ayatcollections' ), __( "Ayat Collections brings the beauty of traditional and modern jewellery to every woman in Pakistan.\n\nFrom bridal kundan sets to everyday earrings, every piece is chosen for its finish, comfort and shine — at prices that let you wear something special more often.\n\nWe pack every order in a gift box and deliver nationwide with Cash on Delivery, so shopping with us is simple and safe.", 'ayatcollections' ) ),
		'about_img'       => array( 'about', 'image', __( 'Photo', 'ayatcollections' ), 'demo-about' ),
		// 6. Royal banner.
		'royal_title'     => array( 'royal', 'text', __( 'Heading', 'ayatcollections' ), __( 'The Royal Touch', 'ayatcollections' ) ),
		'royal_text'      => array( 'royal', 'textarea', __( 'Text', 'ayatcollections' ), __( 'Want a set made just for your big day? Share your outfit and colours with us and we will help you choose or customise the perfect pieces.', 'ayatcollections' ) ),
		'royal_btn'       => array( 'royal', 'text', __( 'Button label', 'ayatcollections' ), __( 'Contact Us', 'ayatcollections' ) ),
		'royal_link'      => array( 'royal', 'link', __( 'Button link', 'ayatcollections' ), '#contact' ),
		'royal_img'       => array( 'royal', 'image', __( 'Photo', 'ayatcollections' ), 'demo-royal-banner' ),
		// 7. Services.
		'sv_title'        => array( 'services', 'text', __( 'Heading', 'ayatcollections' ), __( 'Special Services', 'ayatcollections' ) ),
		'sv1_img'         => array( 'services', 'image', __( 'Service 1 photo', 'ayatcollections' ), 'demo-service-bridal' ),
		'sv1_text'        => array( 'services', 'text', __( 'Service 1 name', 'ayatcollections' ), __( 'Bridal Sets', 'ayatcollections' ) ),
		'sv2_img'         => array( 'services', 'image', __( 'Service 2 photo', 'ayatcollections' ), 'demo-service-custom' ),
		'sv2_text'        => array( 'services', 'text', __( 'Service 2 name', 'ayatcollections' ), __( 'Custom Orders', 'ayatcollections' ) ),
		'sv3_img'         => array( 'services', 'image', __( 'Service 3 photo', 'ayatcollections' ), 'demo-service-gift' ),
		'sv3_text'        => array( 'services', 'text', __( 'Service 3 name', 'ayatcollections' ), __( 'Gift Packing', 'ayatcollections' ) ),
		'sv4_img'         => array( 'services', 'image', __( 'Service 4 photo', 'ayatcollections' ), 'demo-service-care' ),
		'sv4_text'        => array( 'services', 'text', __( 'Service 4 name', 'ayatcollections' ), __( 'Polish & Repair', 'ayatcollections' ) ),
		// 8. Steps.
		'st_title'        => array( 'steps', 'text', __( 'Heading', 'ayatcollections' ), __( 'How Custom Orders Work', 'ayatcollections' ) ),
		'st1'             => array( 'steps', 'text', __( 'Step 1 title', 'ayatcollections' ), __( 'Share Your Idea', 'ayatcollections' ) ),
		'st1_text'        => array( 'steps', 'text', __( 'Step 1 text', 'ayatcollections' ), __( 'Send us your outfit, colours and budget on WhatsApp.', 'ayatcollections' ) ),
		'st2'             => array( 'steps', 'text', __( 'Step 2 title', 'ayatcollections' ), __( 'We Suggest a Design', 'ayatcollections' ) ),
		'st2_text'        => array( 'steps', 'text', __( 'Step 2 text', 'ayatcollections' ), __( 'We share matching pieces or a custom set with photos.', 'ayatcollections' ) ),
		'st3'             => array( 'steps', 'text', __( 'Step 3 title', 'ayatcollections' ), __( 'You Approve', 'ayatcollections' ) ),
		'st3_text'        => array( 'steps', 'text', __( 'Step 3 text', 'ayatcollections' ), __( 'Make changes until it is exactly right for you.', 'ayatcollections' ) ),
		'st4'             => array( 'steps', 'text', __( 'Step 4 title', 'ayatcollections' ), __( 'Delivered to You', 'ayatcollections' ) ),
		'st4_text'        => array( 'steps', 'text', __( 'Step 4 text', 'ayatcollections' ), __( 'Gift-boxed and delivered with Cash on Delivery.', 'ayatcollections' ) ),
		// 9. Quote.
		'q_title'         => array( 'quote', 'text', __( 'Heading', 'ayatcollections' ), __( 'Light and Shine', 'ayatcollections' ) ),
		'q_text'          => array( 'quote', 'textarea', __( 'Quote', 'ayatcollections' ), __( '“We choose every stone and finish so that it catches the light the way real gold does. When you wear Ayat, we want you to feel like royalty.”', 'ayatcollections' ) ),
		'q_by'            => array( 'quote', 'text', __( 'Name', 'ayatcollections' ), __( 'Ayat, Founder', 'ayatcollections' ) ),
		'q_img'           => array( 'quote', 'image', __( 'Photo', 'ayatcollections' ), 'demo-quote' ),
		// 10. Boutique.
		'b_title'         => array( 'boutique', 'text', __( 'Heading', 'ayatcollections' ), __( 'Shop Online, Anywhere in Pakistan', 'ayatcollections' ) ),
		'b_lead'          => array( 'boutique', 'text', __( 'Intro line', 'ayatcollections' ), __( 'Welcome to the world of Ayat.', 'ayatcollections' ) ),
		'b_text'          => array( 'boutique', 'textarea', __( 'Text', 'ayatcollections' ), __( "Browse the full collection here and order in a few taps. Pay with Cash on Delivery when your parcel arrives.\n\nNeed help choosing? Message us on WhatsApp and we will send photos and videos of any piece before you order.", 'ayatcollections' ) ),
		'b_img'           => array( 'boutique', 'image', __( 'Photo', 'ayatcollections' ), 'demo-boutique' ),
		// 11. Newsletter.
		'nl_title'        => array( 'newsletter', 'text', __( 'Heading', 'ayatcollections' ), __( 'Join Our List', 'ayatcollections' ) ),
		'nl_text'         => array( 'newsletter', 'text', __( 'Text', 'ayatcollections' ), __( 'Get 10% off your first order and be the first to see new collections.', 'ayatcollections' ) ),
		'nl_shortcode'    => array( 'newsletter', 'text', __( 'Form shortcode (optional, e.g. from Mailchimp for WP)', 'ayatcollections' ), '' ),
		'nl_img'          => array( 'newsletter', 'image', __( 'Photo', 'ayatcollections' ), 'demo-newsletter' ),
		// 12. Tips.
		'tp_title'        => array( 'tips', 'text', __( 'Heading', 'ayatcollections' ), __( 'Jewellery Care Tips', 'ayatcollections' ) ),
		'tp1'             => array( 'tips', 'text', __( 'Tip 1 title', 'ayatcollections' ), __( 'Keep It Dry', 'ayatcollections' ) ),
		'tp1_text'        => array( 'tips', 'textarea', __( 'Tip 1 text', 'ayatcollections' ), __( 'Take your jewellery off before washing hands, bathing or swimming. Water and soap dull the gold-tone finish over time.', 'ayatcollections' ) ),
		'tp1_img'         => array( 'tips', 'image', __( 'Tip 1 photo', 'ayatcollections' ), 'demo-tip-1' ),
		'tp2'             => array( 'tips', 'text', __( 'Tip 2 title', 'ayatcollections' ), __( 'Perfume First, Jewellery Last', 'ayatcollections' ) ),
		'tp2_text'        => array( 'tips', 'textarea', __( 'Tip 2 text', 'ayatcollections' ), __( 'Apply perfume, lotion and hairspray before putting on your pieces, and let them dry. Chemicals are the main cause of tarnish.', 'ayatcollections' ) ),
		'tp2_img'         => array( 'tips', 'image', __( 'Tip 2 photo', 'ayatcollections' ), 'demo-tip-2' ),
		'tp3'             => array( 'tips', 'text', __( 'Tip 3 title', 'ayatcollections' ), __( 'Store Each Piece Separately', 'ayatcollections' ) ),
		'tp3_text'        => array( 'tips', 'textarea', __( 'Tip 3 text', 'ayatcollections' ), __( 'Keep every piece in its pouch or box so stones do not scratch and chains do not tangle. Wipe with a soft dry cloth after wearing.', 'ayatcollections' ) ),
		'tp3_img'         => array( 'tips', 'image', __( 'Tip 3 photo', 'ayatcollections' ), 'demo-tip-3' ),
		// 13. Contact.
		'ct_title'        => array( 'contact', 'text', __( 'Heading', 'ayatcollections' ), __( 'Contact Us', 'ayatcollections' ) ),
		'ct_text'         => array( 'contact', 'text', __( 'Text', 'ayatcollections' ), __( 'Fill out the form to ask about a piece, a custom set or your order.', 'ayatcollections' ) ),
		'ct_img'          => array( 'contact', 'image', __( 'Background photo', 'ayatcollections' ), 'demo-contact-bg' ),
	);
}

/**
 * Read a homepage setting.
 *
 * @param string $key Key.
 * @return string
 */
function ayat_home( $key ) {
	$f = ayat_home_fields();
	return (string) get_theme_mod( 'ayat_home_' . $key, isset( $f[ $key ] ) ? $f[ $key ][3] : '' );
}

/**
 * URL of an image setting (uploaded image or bundled demo photo).
 *
 * @param string $key Field key.
 * @return string
 */
function ayat_home_img_url( $key ) {
	$id = absint( get_theme_mod( 'ayat_home_' . $key . '_id', 0 ) );
	if ( $id ) {
		return (string) wp_get_attachment_image_url( $id, 'large' );
	}
	return AYAT_URI . '/assets/images/demo/' . ayat_home( $key ) . '.webp';
}

/**
 * Image markup for an image setting.
 *
 * @param string $key   Field key.
 * @param string $alt   Alt text.
 * @param string $class CSS class.
 * @param bool   $eager Load eagerly (hero).
 * @return string
 */
function ayat_home_img( $key, $alt, $class = '', $eager = false ) {
	$id   = absint( get_theme_mod( 'ayat_home_' . $key . '_id', 0 ) );
	$load = $eager ? array( 'loading' => 'eager', 'fetchpriority' => 'high' ) : array( 'loading' => 'lazy' );
	if ( $id ) {
		return wp_get_attachment_image( $id, 'large', false, array_merge( $load, array( 'class' => $class, 'alt' => $alt ) ) );
	}
	$file = ayat_home( $key );
	$path = AYAT_DIR . '/assets/images/demo/' . $file . '.webp';
	$size = file_exists( $path ) ? wp_getimagesize( $path ) : array( 800, 800 );
	return sprintf(
		'<img class="%1$s" src="%2$s" width="%3$d" height="%4$d" alt="%5$s" loading="%6$s"%7$s decoding="async" data-demo-image="1">',
		esc_attr( $class ),
		esc_url( AYAT_URI . '/assets/images/demo/' . $file . '.webp' ),
		(int) $size[0],
		(int) $size[1],
		esc_attr( $alt ),
		$eager ? 'eager' : 'lazy',
		$eager ? ' fetchpriority="high"' : ''
	);
}

/**
 * Paragraphs from a textarea setting.
 *
 * @param string $key Field key.
 * @return string
 */
function ayat_home_paras( $key ) {
	$out = '';
	foreach ( preg_split( "/\n\s*\n/", trim( ayat_home( $key ) ) ) as $p ) {
		$out .= '<p>' . esc_html( trim( $p ) ) . '</p>';
	}
	return $out;
}

/**
 * Gold ornament divider used under section headings.
 *
 * @return string
 */
function ayat_ornament() {
	return '<span class="ornament" aria-hidden="true"><svg width="74" height="10" viewBox="0 0 74 10"><path d="M0 5h29M45 5h29" stroke="currentColor" stroke-width="1"/><path d="M37 0l5 5-5 5-5-5z" fill="currentColor"/><circle cx="31" cy="5" r="1.4" fill="currentColor"/><circle cx="43" cy="5" r="1.4" fill="currentColor"/></svg></span>';
}

/**
 * Social links (only those filled in).
 *
 * @return array label => url
 */
function ayat_social_links() {
	$phone = preg_replace( '/[^0-9]/', '', (string) get_theme_mod( 'ayat_phone', '' ) );
	if ( $phone && 0 === strpos( $phone, '0' ) ) {
		$phone = '92' . substr( $phone, 1 );
	}
	return array_filter(
		array(
			'Facebook'  => get_theme_mod( 'ayat_facebook', '' ),
			'Instagram' => get_theme_mod( 'ayat_instagram', '' ),
			'TikTok'    => get_theme_mod( 'ayat_tiktok', '' ),
			'YouTube'   => get_theme_mod( 'ayat_youtube', '' ),
			'WhatsApp'  => $phone ? 'https://wa.me/' . $phone : '',
		)
	);
}

/**
 * Small social icon.
 *
 * @param string $name Network.
 * @return string
 */
function ayat_social_icon( $name ) {
	$p = array(
		'Facebook'  => '<path d="M13.5 8.5V7c0-.7.4-1 1-1h1.5V3.5h-2.2C11.4 3.5 10.5 5 10.5 7v1.5H8.5V11h2v9h3v-9h2.2l.3-2.5h-2.5Z"/>',
		'Instagram' => '<rect x="4" y="4" width="16" height="16" rx="4.5" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3.6" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="16.8" cy="7.2" r="1.1"/>',
		'TikTok'    => '<path d="M15.5 3.5c.4 2 1.8 3.4 3.8 3.7v2.9c-1.4 0-2.7-.4-3.8-1.1v5.6a5 5 0 1 1-5-5h.6v3a2 2 0 1 0 1.4 1.9V3.5h3Z"/>',
		'YouTube'   => '<path d="M21 8.2c-.2-1.3-1.1-2.2-2.4-2.3C16.8 5.7 14.4 5.6 12 5.6s-4.8.1-6.6.3C4.1 6 3.2 6.9 3 8.2 2.8 9.4 2.8 10.7 2.8 12s0 2.6.2 3.8c.2 1.3 1.1 2.2 2.4 2.3 1.8.2 4.2.3 6.6.3s4.8-.1 6.6-.3c1.3-.1 2.2-1 2.4-2.3.2-1.2.2-2.5.2-3.8s0-2.6-.2-3.8ZM10.2 14.8V9.2l4.8 2.8-4.8 2.8Z"/>',
		'WhatsApp'  => '<path d="M12 3.5a8.4 8.4 0 0 0-7.2 12.7L3.7 20.5l4.4-1.1A8.4 8.4 0 1 0 12 3.5Zm0 15.3c-1.3 0-2.6-.4-3.7-1l-.3-.2-2.6.7.7-2.5-.2-.3A6.9 6.9 0 1 1 12 18.8Zm3.8-5.2c-.2-.1-1.2-.6-1.4-.7-.2-.1-.3-.1-.5.1l-.7.8c-.1.2-.2.2-.4.1-.2-.1-.9-.3-1.7-1-.6-.6-1-1.2-1.1-1.4-.1-.2 0-.3.1-.4l.3-.4.2-.3v-.3l-.7-1.6c-.2-.4-.4-.4-.5-.4h-.4a.8.8 0 0 0-.6.3c-.2.2-.8.8-.8 1.9s.8 2.2.9 2.3c.1.2 1.6 2.4 3.9 3.4 1.9.7 2.3.6 2.7.5.4-.1 1.2-.5 1.4-1 .2-.5.2-.9.1-1l-.4-.2Z"/>',
	);
	return '<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">' . ( isset( $p[ $name ] ) ? $p[ $name ] : '' ) . '</svg>';
}

/**
 * Customizer: one section per homepage block.
 *
 * @param WP_Customize_Manager $c Manager.
 */
function ayat_home_customize( $c ) {
	$sections = array(
		'hero'        => __( '1. Top banner', 'ayatcollections' ),
		'collections' => __( '2. Collections & New Arrivals', 'ayatcollections' ),
		'bespoke'     => __( '3. Bridal gallery', 'ayatcollections' ),
		'special'     => __( '4. What makes us special', 'ayatcollections' ),
		'about'       => __( '5. About us', 'ayatcollections' ),
		'royal'       => __( '6. Dark banner', 'ayatcollections' ),
		'services'    => __( '7. Services', 'ayatcollections' ),
		'steps'       => __( '8. How it works', 'ayatcollections' ),
		'quote'       => __( '9. Quote', 'ayatcollections' ),
		'boutique'    => __( '10. Shop online', 'ayatcollections' ),
		'newsletter'  => __( '11. Newsletter', 'ayatcollections' ),
		'tips'        => __( '12. Care tips', 'ayatcollections' ),
		'contact'     => __( '13. Contact', 'ayatcollections' ),
	);
	$p = 1;
	foreach ( $sections as $id => $title ) {
		$c->add_section( 'ayat_home_' . $id, array( 'title' => $title, 'panel' => 'ayat_home', 'priority' => $p++ ) );
	}
	foreach ( ayat_home_fields() as $key => $f ) {
		list( $sec, $type, $label, $default ) = $f;
		if ( 'image' === $type ) {
			$c->add_setting( "ayat_home_{$key}_id", array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
			$c->add_control( new WP_Customize_Media_Control( $c, "ayat_home_{$key}_id", array( 'label' => $label, 'description' => __( 'Empty = bundled demo photo.', 'ayatcollections' ), 'section' => 'ayat_home_' . $sec, 'mime_type' => 'image' ) ) );
			continue;
		}
		$cb = array( 'text' => 'sanitize_text_field', 'textarea' => 'sanitize_textarea_field', 'link' => 'ayat_sanitize_link' )[ $type ];
		$c->add_setting( "ayat_home_{$key}", array( 'default' => $default, 'sanitize_callback' => $cb ) );
		$c->add_control( "ayat_home_{$key}", array( 'label' => $label, 'section' => 'ayat_home_' . $sec, 'type' => 'textarea' === $type ? 'textarea' : 'text' ) );
	}
}
add_action( 'customize_register', 'ayat_home_customize', 20 );

/**
 * Contact form handler (homepage "Contact Us"): emails the site admin.
 */
function ayat_contact_submit() {
	if ( ! isset( $_POST['ayat_contact_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ayat_contact_nonce'] ) ), 'ayat_contact' ) ) {
		wp_die( esc_html__( 'Please reload the page and try again.', 'ayatcollections' ) );
	}
	if ( ! empty( $_POST['website'] ) ) { // Honeypot.
		wp_safe_redirect( home_url( '/' ) );
		exit;
	}
	$name    = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
	$phone   = sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) );
	$email   = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	$message = sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) );
	$ok      = $name && ( $phone || $email ) && $message;
	if ( $ok ) {
		$to   = get_theme_mod( 'ayat_email' ) ? get_theme_mod( 'ayat_email' ) : get_option( 'admin_email' );
		$body = "Name: {$name}\nPhone: {$phone}\nEmail: {$email}\n\n{$message}";
		wp_mail( $to, sprintf( '[%s] New message from %s', get_bloginfo( 'name' ), $name ), $body, $email ? array( 'Reply-To: ' . $email ) : array() );
	}
	wp_safe_redirect( add_query_arg( 'contact', $ok ? 'sent' : 'error', home_url( '/' ) ) . '#contact' );
	exit;
}
add_action( 'admin_post_nopriv_ayat_contact', 'ayat_contact_submit' );
add_action( 'admin_post_ayat_contact', 'ayat_contact_submit' );
