<?php
/**
 * Default page template. Also renders WooCommerce Cart, Checkout,
 * Order received and My Account pages (native shortcodes or blocks).
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

get_header();
$ayat_store_page = function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() );
?>
<main id="main" class="site-main page-main<?php echo $ayat_store_page ? ' store-page' : ''; ?>">
	<div class="container<?php echo $ayat_store_page ? '' : ' container--narrow'; ?>">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="page-header">
					<?php the_title( '<h1 class="page-title">', '</h1>' ); ?>
				</header>
				<div class="entry-content">
					<?php the_content(); ?>
				</div>
			</article>
			<?php
		endwhile;
		?>
	</div>
</main>
<?php
get_footer();
