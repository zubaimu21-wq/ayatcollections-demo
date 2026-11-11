<?php
/**
 * Homepage. Layout follows the "Jewelry Collection Landing Page" Figma
 * template (Maker, CC BY 4.0), rebuilt for Ayat Collections + WooCommerce.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

get_header();
$shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
?>
<main id="main" class="site-main home-main">
	<h1 class="screen-reader-text"><?php echo esc_html( get_bloginfo( 'name' ) ); ?> — <?php echo esc_html( get_bloginfo( 'description' ) ); ?></h1>

	<?php get_template_part( 'template-parts/hero-slider' ); ?>

	<section class="showcase showcase--one" aria-label="<?php esc_attr_e( 'Featured collections', 'ayatcollections' ); ?>">
		<div class="wrap showcase__grid">
			<div class="showcase__big"><?php echo ayat_home_img( 'f1_big', 'showcase__img', '(max-width: 700px) 100vw, 50vw' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<?php ayat_home_card( 'f1_small', 'showcase__card--small' ); ?>
			<?php ayat_home_card( 'f1_tall', 'showcase__card--tall' ); ?>
			<span class="ring ring--a" aria-hidden="true"></span>
			<span class="ring ring--b" aria-hidden="true"></span>
		</div>
	</section>

	<section class="story" aria-labelledby="story-title">
		<div class="wrap story__grid">
			<div class="story__text">
				<p class="eyebrow"><?php echo esc_html( ayat_home( 'col_eyebrow' ) ); ?></p>
				<h2 id="story-title" class="display"><?php echo esc_html( ayat_home( 'col_title' ) ); ?></h2>
				<p class="lead"><?php echo esc_html( ayat_home( 'col_text' ) ); ?></p>
				<a class="btn-line" href="<?php echo esc_url( ayat_resolve_link( ayat_home( 'col_link' ) ) ); ?>"><?php echo esc_html( ayat_home( 'col_btn' ) ); ?></a>
			</div>
			<div class="story__media"><?php echo ayat_home_img( 'col_img', 'showcase__img', '(max-width: 700px) 100vw, 50vw' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<span class="ring ring--c" aria-hidden="true"></span>
		</div>
	</section>

	<section class="showcase showcase--two" aria-label="<?php esc_attr_e( 'More collections', 'ayatcollections' ); ?>">
		<div class="wrap showcase__grid">
			<div class="showcase__big"><?php echo ayat_home_img( 'f2_big', 'showcase__img', '(max-width: 700px) 100vw, 50vw' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<?php ayat_home_card( 'f2_small', 'showcase__card--small' ); ?>
			<?php ayat_home_card( 'f2_tall', 'showcase__card--tall' ); ?>
		</div>
	</section>

	<?php $best = ayat_best_sellers( 4 ); ?>
	<?php if ( $best ) : ?>
		<section class="best" aria-labelledby="best-title">
			<div class="wrap">
				<h2 id="best-title" class="display display--center"><?php echo esc_html( ayat_home( 'best_title' ) ); ?></h2>
				<ul class="best__grid" role="list">
					<?php foreach ( $best as $p ) : ?>
						<li class="best__item">
							<a href="<?php echo esc_url( $p->get_permalink() ); ?>" class="best__link">
								<span class="best__media"><?php echo $p->get_image( 'woocommerce_thumbnail', array( 'class' => 'best__img', 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<span class="best__name"><?php echo esc_html( $p->get_name() ); ?></span>
								<span class="best__price"><?php echo wp_kses_post( wc_price( $p->is_type( 'variable' ) ? $p->get_variation_price( 'min' ) : $p->get_price() ) ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
				<p class="center"><a class="btn-line btn-line--light" href="<?php echo esc_url( $shop ); ?>"><?php echo esc_html( ayat_home( 'best_btn' ) ); ?></a></p>
			</div>
		</section>
	<?php endif; ?>

	<section class="gift" aria-labelledby="gift-title">
		<div class="wrap">
			<h2 id="gift-title" class="display"><?php echo esc_html( ayat_home( 'gift_title' ) ); ?></h2>
			<div class="gift__grid">
				<div class="gift__media"><?php echo ayat_home_img( 'gift_img', 'showcase__img', '(max-width: 700px) 100vw, 55vw' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<span class="ring ring--d" aria-hidden="true"></span>
				<div class="gift__text">
					<p class="lead"><?php echo esc_html( ayat_home( 'gift_text' ) ); ?></p>
					<a class="btn-line" href="<?php echo esc_url( ayat_resolve_link( ayat_home( 'gift_link' ) ) ); ?>"><?php echo esc_html( ayat_home( 'gift_btn' ) ); ?></a>
				</div>
			</div>
		</div>
	</section>
</main>
<?php
get_footer();
