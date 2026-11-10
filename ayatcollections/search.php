<?php
/**
 * Site search results (product searches use the WooCommerce shop template).
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main">
	<div class="container container--narrow">
		<header class="page-header">
			<?php /* translators: %s: search term */ ?>
			<h1 class="page-title"><?php printf( esc_html__( 'Results for “%s”', 'ayatcollections' ), esc_html( get_search_query() ) ); ?></h1>
		</header>
		<?php if ( have_posts() ) : ?>
			<ul class="search-results" role="list">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
				<?php endwhile; ?>
			</ul>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'No matches. Try another word.', 'ayatcollections' ); ?></p>
			<?php get_search_form(); ?>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
