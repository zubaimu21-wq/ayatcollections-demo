<?php
/**
 * Not found.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main">
	<div class="container container--narrow page-404">
		<h1 class="page-title"><?php esc_html_e( 'This page could not be found', 'ayatcollections' ); ?></h1>
		<p><?php esc_html_e( 'The piece you are looking for may have moved. Try a search or browse our collections.', 'ayatcollections' ); ?></p>
		<?php
		if ( function_exists( 'get_product_search_form' ) ) {
			get_product_search_form();
		} else {
			get_search_form();
		}
		?>
		<p><a class="btn btn--purple" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ); ?>"><?php esc_html_e( 'Go to the shop', 'ayatcollections' ); ?></a></p>
	</div>
</main>
<?php
get_footer();
