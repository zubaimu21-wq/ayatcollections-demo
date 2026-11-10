<?php
/**
 * Template Name: Collections
 *
 * Lists every top-level product category that has a published product.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main">
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
		<?php ayat_render_collection_cards( ayat_get_collections(), 'h2' ); ?>
	</div>
</main>
<?php
get_footer();
