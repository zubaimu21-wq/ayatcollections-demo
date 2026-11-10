<?php
/**
 * Single blog post.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main">
	<div class="container container--narrow">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="page-header">
					<?php the_title( '<h1 class="page-title">', '</h1>' ); ?>
					<p class="entry-meta"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
				</header>
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="entry-media"><?php the_post_thumbnail( 'large' ); ?></div>
				<?php endif; ?>
				<div class="entry-content"><?php the_content(); ?></div>
			</article>
			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
		endwhile;
		?>
	</div>
</main>
<?php
get_footer();
