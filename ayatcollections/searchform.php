<?php
/**
 * Site search form.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

$ayat_id = wp_unique_id( 'search-field-' );
?>
<form role="search" method="get" class="search-form ayat-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $ayat_id ); ?>"><?php esc_html_e( 'Search', 'ayatcollections' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $ayat_id ); ?>" class="search-field" placeholder="<?php esc_attr_e( 'Search…', 'ayatcollections' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
	<button type="submit" class="ayat-search__btn"><?php echo ayat_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="screen-reader-text"><?php esc_html_e( 'Search', 'ayatcollections' ); ?></span></button>
</form>
