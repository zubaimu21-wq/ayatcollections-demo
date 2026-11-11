<?php
/**
 * Homepage: banner, collection circles, best sellers, trust strip.
 * Texts and banner photo: Appearance → Customize → Homepage → 1. Top banner.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main home-main">

	<section class="banner" style="--banner:url('<?php echo esc_url( ayat_home_img_url( 'hero_img' ) ); ?>')">
		<div class="wrap banner__text">
			<p class="banner__eyebrow"><?php echo esc_html( ayat_home( 'hero_eyebrow' ) ); ?></p>
			<h1 class="banner__title"><?php echo esc_html( ayat_home( 'hero_title' ) ); ?> <span><?php echo esc_html( ayat_home( 'hero_script' ) ); ?></span></h1>
			<p class="banner__tags"><?php echo esc_html( ayat_home( 'hero_text' ) ); ?></p>
			<a class="btn-gold" href="<?php echo esc_url( ayat_resolve_link( ayat_home( 'hero_link' ) ) ); ?>"><?php echo esc_html( ayat_home( 'hero_btn' ) ); ?> <span aria-hidden="true">&rarr;</span></a>
		</div>
	</section>

	<section class="circles" aria-label="<?php esc_attr_e( 'Shop by collection', 'ayatcollections' ); ?>">
		<ul class="wrap circles__list" role="list">
			<?php foreach ( ayat_get_collections( 4 ) as $term ) : ?>
				<?php $img = ayat_collection_image_id( $term ); ?>
				<li>
					<a href="<?php echo esc_url( get_term_link( $term ) ); ?>" class="circles__link">
						<span class="circles__img"><?php echo $img ? wp_get_attachment_image( $img, 'medium', false, array( 'alt' => $term->name, 'loading' => 'lazy' ) ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<span class="circles__name"><?php echo esc_html( $term->name ); ?></span>
						<span class="circles__cta"><?php esc_html_e( 'Shop Now', 'ayatcollections' ); ?> &rarr;</span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</section>

	<?php if ( class_exists( 'WooCommerce' ) ) : ?>
		<section class="bestsellers" aria-labelledby="bs-title">
			<div class="wrap">
				<p class="bestsellers__eyebrow"><?php esc_html_e( 'Featured Products', 'ayatcollections' ); ?></p>
				<h2 id="bs-title" class="bestsellers__title"><span><?php esc_html_e( 'Our Best Sellers', 'ayatcollections' ); ?></span></h2>
				<?php echo do_shortcode( '[products limit="6" columns="6" orderby="popularity" visibility="visible"]' ); ?>
			</div>
		</section>
	<?php endif; ?>

	<section class="perks" aria-label="<?php esc_attr_e( 'Why shop with us', 'ayatcollections' ); ?>">
		<ul class="wrap perks__list" role="list">
			<li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 7h12v9H2zM14 10h4l3 3v3h-7M5.5 19a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3ZM17.5 19a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z"/></svg><span><strong><?php esc_html_e( 'Cash on Delivery', 'ayatcollections' ); ?></strong><?php esc_html_e( 'Available in Pakistan', 'ayatcollections' ); ?></span></li>
			<li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 4 6v6c0 4.5 3.4 8 8 9 4.6-1 8-4.5 8-9V6l-8-3Z"/><path d="m8.5 12 2.5 2.5 4.5-5"/></svg><span><strong><?php esc_html_e( 'Secure Shopping', 'ayatcollections' ); ?></strong><?php esc_html_e( 'Your trust matters', 'ayatcollections' ); ?></span></li>
			<li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7.5 12 3l9 4.5v9L12 21l-9-4.5zM3 7.5 12 12l9-4.5M12 12v9"/></svg><span><strong><?php esc_html_e( 'Easy Returns', 'ayatcollections' ); ?></strong><?php esc_html_e( 'Hassle-free exchange', 'ayatcollections' ); ?></span></li>
			<li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 14v-2a8 8 0 0 1 16 0v2"/><path d="M4 14h3v5H4zM17 14h3v5h-3zM20 19c0 1.5-2 2-5 2"/></svg><span><strong><?php esc_html_e( 'WhatsApp Support', 'ayatcollections' ); ?></strong><?php esc_html_e( 'Get help anytime', 'ayatcollections' ); ?></span></li>
		</ul>
	</section>
</main>
<?php
get_footer();
