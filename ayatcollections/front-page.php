<?php
/**
 * Homepage: hero slider, collections, new arrivals.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

get_header();

$collections_page = get_page_by_path( 'collections' );
$arrivals_page    = get_page_by_path( 'new-arrivals' );
$shop_url         = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
?>
<main id="main" class="site-main home-main">
	<h1 class="screen-reader-text"><?php echo esc_html( get_bloginfo( 'name' ) ); ?> — <?php echo esc_html( get_bloginfo( 'description' ) ? get_bloginfo( 'description' ) : __( 'Artificial jewellery', 'ayatcollections' ) ); ?></h1>

	<?php get_template_part( 'template-parts/hero-slider' ); ?>

	<?php
	$intro_title = get_theme_mod( 'ayat_intro_title', __( 'Jewellery for every celebration, and every day in between', 'ayatcollections' ) );
	$intro_text  = get_theme_mod( 'ayat_intro_text', __( 'Kundan, pearls and polished gold tone — designed in Pakistan and finished by hand, at prices that let you wear something special more often.', 'ayatcollections' ) );
	if ( $intro_title ) :
		?>
		<section class="intro" aria-labelledby="home-intro-title">
			<div class="container intro__inner">
				<h2 id="home-intro-title" class="intro__title"><?php echo esc_html( $intro_title ); ?></h2>
				<div class="intro__side">
					<?php if ( $intro_text ) : ?>
						<p><?php echo esc_html( $intro_text ); ?></p>
					<?php endif; ?>
					<a class="circle-link" href="<?php echo esc_url( $shop_url ); ?>"><span><?php esc_html_e( 'Explore the shop', 'ayatcollections' ); ?></span></a>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section class="section section--collections" aria-labelledby="home-collections-title">
		<div class="container">
			<div class="section-head">
				<h2 id="home-collections-title" class="section-title"><?php esc_html_e( 'Shop by collection', 'ayatcollections' ); ?></h2>
				<a class="link-more" href="<?php echo esc_url( $collections_page ? get_permalink( $collections_page ) : $shop_url ); ?>"><?php esc_html_e( 'All collections', 'ayatcollections' ); ?></a>
			</div>
			<?php ayat_render_collection_cards( ayat_get_collections( 8 ) ); ?>
		</div>
	</section>

	<?php if ( class_exists( 'WooCommerce' ) ) : ?>
		<section class="section section--arrivals" aria-labelledby="home-arrivals-title">
			<div class="container">
				<div class="section-head">
					<h2 id="home-arrivals-title" class="section-title"><?php esc_html_e( 'New Arrivals', 'ayatcollections' ); ?></h2>
					<a class="link-more" href="<?php echo esc_url( $arrivals_page ? get_permalink( $arrivals_page ) : add_query_arg( 'orderby', 'date', $shop_url ) ); ?>"><?php esc_html_e( 'View all', 'ayatcollections' ); ?></a>
				</div>
				<?php echo do_shortcode( '[products limit="8" columns="4" orderby="date" order="DESC" visibility="visible"]' ); ?>
			</div>
		</section>
	<?php endif; ?>

	<section class="trust-strip" aria-label="<?php esc_attr_e( 'Why shop with us', 'ayatcollections' ); ?>">
		<ul class="container trust-strip__list" role="list">
			<li><strong><?php esc_html_e( 'Cash on Delivery', 'ayatcollections' ); ?></strong><span><?php esc_html_e( 'Pay when your order arrives', 'ayatcollections' ); ?></span></li>
			<li><strong><?php esc_html_e( 'Nationwide delivery', 'ayatcollections' ); ?></strong><span><?php esc_html_e( '3–5 working days', 'ayatcollections' ); ?></span></li>
			<li><strong><?php esc_html_e( 'Gift-ready packing', 'ayatcollections' ); ?></strong><span><?php esc_html_e( 'Every piece arrives boxed', 'ayatcollections' ); ?></span></li>
		</ul>
	</section>
</main>
<?php
get_footer();
