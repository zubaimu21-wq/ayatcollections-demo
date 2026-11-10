<?php
/**
 * Fallback template (blog, archives).
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main">
	<div class="container container--narrow">
		<header class="page-header">
			<h1 class="page-title">
				<?php
				if ( is_home() && ! is_front_page() ) {
					single_post_title();
				} elseif ( is_archive() ) {
					the_archive_title();
				} else {
					esc_html_e( 'Journal', 'ayatcollections' );
				}
				?>
			</h1>
		</header>
		<?php if ( have_posts() ) : ?>
			<div class="post-list">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<a href="<?php the_permalink(); ?>" class="post-card__media" tabindex="-1" aria-hidden="true"><?php the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy' ) ); ?></a>
						<?php endif; ?>
						<h2 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<div class="post-card__excerpt"><?php the_excerpt(); ?></div>
					</article>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing here yet.', 'ayatcollections' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
