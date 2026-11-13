<?php
/**
 * Sale campaigns (11.11, Eid, Black Friday...).
 *
 * Appearance → Customize → Sale banner: switch on, write the text, choose an end
 * date. A bar with a live countdown appears at the very top of every page and
 * hides itself when the sale ends. Products with a WooCommerce sale price show a
 * "-30%" badge and, while the sale is on, a "Sale picks" row on the homepage.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

/**
 * Customizer settings.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function ayat_sale_customize( $wp_customize ) {
	$wp_customize->add_section(
		'ayat_sale',
		array(
			'title'       => __( 'Sale banner (11.11, Eid…)', 'ayatcollections' ),
			'priority'    => 25,
			'description' => __( 'Turn on a sale bar with a countdown at the top of every page. Set sale prices on the products themselves (Products → Edit → Sale price).', 'ayatcollections' ),
		)
	);
	$fields = array(
		'ayat_sale_on'   => array( 'checkbox', __( 'Show the sale banner', 'ayatcollections' ), false, 'wp_validate_boolean' ),
		'ayat_sale_text' => array( 'text', __( 'Banner text', 'ayatcollections' ), __( '11.11 Sale · Up to 30% off all jewellery', 'ayatcollections' ), 'sanitize_text_field' ),
		'ayat_sale_code' => array( 'text', __( 'Promo code (optional)', 'ayatcollections' ), '', 'sanitize_text_field' ),
		'ayat_sale_link' => array( 'text', __( 'Button link', 'ayatcollections' ), '/shop/', 'sanitize_text_field' ),
		'ayat_sale_end'  => array( 'text', __( 'Ends on (YYYY-MM-DD HH:MM, leave empty for no countdown)', 'ayatcollections' ), '', 'ayat_sale_clean_date' ),
	);
	foreach ( $fields as $id => $f ) {
		$wp_customize->add_setting( $id, array( 'default' => $f[2], 'sanitize_callback' => $f[3] ) );
		$wp_customize->add_control( $id, array( 'section' => 'ayat_sale', 'type' => $f[0], 'label' => $f[1] ) );
	}
}
add_action( 'customize_register', 'ayat_sale_customize' );

/**
 * Keep only a valid "YYYY-MM-DD HH:MM" date.
 *
 * @param string $value Input.
 * @return string
 */
function ayat_sale_clean_date( $value ) {
	$value = trim( (string) $value );
	return preg_match( '/^\d{4}-\d{2}-\d{2}( \d{2}:\d{2})?$/', $value ) ? $value : '';
}

/**
 * Sale end as a timestamp (site time zone), or 0.
 *
 * @return int
 */
function ayat_sale_end_ts() {
	$end = ayat_sale_clean_date( get_theme_mod( 'ayat_sale_end', '' ) );
	if ( ! $end ) {
		return 0;
	}
	if ( strlen( $end ) === 10 ) {
		$end .= ' 23:59';
	}
	$date = date_create_immutable_from_format( 'Y-m-d H:i', $end, wp_timezone() );
	return $date ? $date->getTimestamp() : 0;
}

/**
 * Is a sale running right now?
 *
 * @return bool
 */
function ayat_sale_active() {
	if ( ! get_theme_mod( 'ayat_sale_on', false ) ) {
		return false;
	}
	$end = ayat_sale_end_ts();
	return ! $end || $end > time();
}

/**
 * The sale bar (top of every page).
 */
function ayat_sale_bar() {
	if ( ! ayat_sale_active() ) {
		return;
	}
	$text = get_theme_mod( 'ayat_sale_text', __( '11.11 Sale · Up to 30% off all jewellery', 'ayatcollections' ) );
	$code = get_theme_mod( 'ayat_sale_code', '' );
	$link = function_exists( 'ayat_resolve_link' ) ? ayat_resolve_link( get_theme_mod( 'ayat_sale_link', '/shop/' ) ) : home_url( '/shop/' );
	$end  = ayat_sale_end_ts();
	?>
	<div class="sale-bar" data-sale-end="<?php echo esc_attr( $end ? $end * 1000 : '' ); ?>" role="region" aria-label="<?php esc_attr_e( 'Sale', 'ayatcollections' ); ?>">
		<div class="sale-bar__inner">
			<span class="sale-bar__spark" aria-hidden="true">✦</span>
			<p class="sale-bar__text"><?php echo esc_html( $text ); ?></p>
			<?php if ( $end ) : ?>
				<p class="sale-bar__timer" aria-live="off">
					<span class="screen-reader-text"><?php echo esc_html( sprintf( /* translators: %s: date */ __( 'Ends %s', 'ayatcollections' ), wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $end ) ) ); ?></span>
					<span aria-hidden="true"><b data-d>00</b>d <b data-h>00</b>h <b data-m>00</b>m <b data-s>00</b>s</span>
				</p>
			<?php endif; ?>
			<?php if ( $code ) : ?>
				<p class="sale-bar__code"><?php esc_html_e( 'Code', 'ayatcollections' ); ?> <strong><?php echo esc_html( $code ); ?></strong></p>
			<?php endif; ?>
			<a class="sale-bar__btn" href="<?php echo esc_url( $link ); ?>"><?php esc_html_e( 'Shop the sale', 'ayatcollections' ); ?> <span aria-hidden="true">&rarr;</span></a>
		</div>
	</div>
	<?php
}

/**
 * "-30%" badge instead of "Sale".
 *
 * @param string     $html    Badge.
 * @param WP_Post    $post    Post.
 * @param WC_Product $product Product.
 * @return string
 */
function ayat_sale_flash( $html, $post = null, $product = null ) {
	if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
		return '<span class="onsale">' . esc_html__( 'Sale', 'ayatcollections' ) . '</span>';
	}
	$max = 0;
	$ids = $product->is_type( 'variable' ) ? $product->get_children() : array( $product->get_id() );
	foreach ( $ids as $id ) {
		$p = wc_get_product( $id );
		if ( ! $p ) {
			continue;
		}
		$reg  = (float) $p->get_regular_price();
		$sale = (float) $p->get_sale_price();
		if ( $reg > 0 && $sale > 0 && $sale < $reg ) {
			$max = max( $max, round( ( $reg - $sale ) / $reg * 100 ) );
		}
	}
	return '<span class="onsale">' . esc_html( $max ? '-' . $max . '%' : __( 'Sale', 'ayatcollections' ) ) . '</span>';
}
add_filter( 'woocommerce_sale_flash', 'ayat_sale_flash', 20, 3 );

/**
 * Homepage "Sale picks" row (only while a sale is on and something is discounted).
 */
function ayat_sale_section() {
	if ( ! ayat_sale_active() || ! function_exists( 'wc_get_product_ids_on_sale' ) || ! wc_get_product_ids_on_sale() ) {
		return;
	}
	$link = function_exists( 'ayat_resolve_link' ) ? ayat_resolve_link( get_theme_mod( 'ayat_sale_link', '/shop/' ) ) : home_url( '/shop/' );
	?>
	<section class="home-sec sale-picks" aria-labelledby="sale-title">
		<div class="wrap">
			<div class="sec-head">
				<div>
					<p class="eyebrow"><?php esc_html_e( 'Limited time', 'ayatcollections' ); ?></p>
					<h2 id="sale-title"><?php echo esc_html( get_theme_mod( 'ayat_sale_text', __( '11.11 Sale · Up to 30% off all jewellery', 'ayatcollections' ) ) ); ?></h2>
				</div>
				<a class="link-gold" href="<?php echo esc_url( $link ); ?>"><?php esc_html_e( 'See all deals', 'ayatcollections' ); ?></a>
			</div>
			<div class="all-rounds"><?php echo do_shortcode( '[products on_sale="true" limit="8" columns="4"]' ); ?></div>
		</div>
	</section>
	<?php
}
