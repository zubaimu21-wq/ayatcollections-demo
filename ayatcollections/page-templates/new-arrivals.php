<?php
/**
 * Template Name: New Arrivals
 *
 * Newest published products first, using the native WooCommerce products shortcode.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main shop-main">
	<div class="container">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<header class="page-header page-header--center">
				<?php the_title( '<h1 class="page-title">', '</h1>' ); ?>
				<?php if ( get_the_content() ) : ?>
					<div class="page-intro"><?php the_content(); ?></div>
				<?php endif; ?>
			</header>
		<?php endwhile; ?>
		<?php
		if ( class_exists( 'WooCommerce' ) ) {
			echo do_shortcode( '[products limit="16" columns="4" orderby="date" order="DESC" paginate="true" visibility="visible"]' );
		}
		?>
	</div>
</main>
<?php
get_footer();
