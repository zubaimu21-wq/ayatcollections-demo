<?php
/**
 * Product search form (overrides woocommerce/templates/product-searchform.php).
 *
 * @package Ayatcollections
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

$ayat_id = wp_unique_id( 'woocommerce-product-search-field-' );
?>
<form role="search" method="get" class="woocommerce-product-search ayat-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $ayat_id ); ?>"><?php esc_html_e( 'Search products', 'ayatcollections' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $ayat_id ); ?>" class="search-field" placeholder="<?php esc_attr_e( 'Search earrings, necklaces, rings…', 'ayatcollections' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
	<button type="submit" class="ayat-search__btn"><?php echo ayat_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="screen-reader-text"><?php esc_html_e( 'Search', 'ayatcollections' ); ?></span></button>
	<input type="hidden" name="post_type" value="product" />
</form>
