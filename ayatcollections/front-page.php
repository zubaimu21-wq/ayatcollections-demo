<?php
/**
 * Homepage: jewellery-box hero, collections, new arrivals, shop the look, trust strip.
 * Texts and photos: Appearance → Customize → Homepage.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

get_header();

$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
$arrivals = get_page_by_path( 'new-arrivals' );

// Every collection (category) with products, in the order set in Products → Categories.
$terms = ayat_get_collections();
if ( ! $terms ) {
	$terms = array();
}

// Shop the look products.
$look = array();
if ( function_exists( 'wc_get_product' ) ) {
	foreach ( array_filter( array_map( 'trim', explode( ',', (string) ayat_home( 'look_products' ) ) ) ) as $slug ) {
		$post = get_page_by_path( sanitize_title( $slug ), OBJECT, 'product' );
		$prod = $post ? wc_get_product( $post ) : null;
		if ( $prod && $prod->is_visible() ) {
			$look[] = $prod;
		}
	}
}
?>
<main id="main" class="site-main home-main">

	<h1 class="screen-reader-text"><?php echo esc_html( get_bloginfo( 'name' ) . ' – ' . __( 'gold-tone stainless steel jewellery', 'ayatcollections' ) ); ?></h1>

	<section class="ayat-slider ayat-slider--banners" data-slider aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Featured collections', 'ayatcollections' ); ?>">
		<div class="ayat-slider__track" data-slider-track>
			<?php for ( $s = 1; $s <= 6; $s++ ) : ?>
				<?php
				$b_link = trim( (string) ayat_home( 'banner' . $s . '_link' ) );
				$b_term = ( $b_link && false === strpos( $b_link, '/' ) ) ? get_term_by( 'slug', $b_link, 'product_cat' ) : false;
				if ( $b_term && ! is_wp_error( $b_term ) ) {
					$b_url = get_term_link( $b_term );
				} elseif ( $b_link && function_exists( 'ayat_resolve_link' ) ) {
					$b_url = ayat_resolve_link( $b_link );
				} else {
					$b_url = $shop_url;
				}
				$b_alt  = (string) ayat_home( 'banner' . $s . '_alt' );
				$b_id   = absint( get_theme_mod( 'ayat_home_banner' . $s . '_img_id', 0 ) );
				$b_load = 1 === $s ? array( 'loading' => 'eager', 'fetchpriority' => 'high' ) : array( 'loading' => 'eager' );
				?>
				<div class="ayat-slide ayat-slide--banner" role="group" aria-roledescription="slide" aria-label="<?php echo esc_attr( $s . ' / 6' ); ?>">
					<a class="ayat-banner" href="<?php echo esc_url( $b_url ); ?>">
						<?php
						if ( $b_id ) {
							echo wp_get_attachment_image( $b_id, 'full', false, array_merge( $b_load, array( 'class' => 'ayat-banner__img', 'alt' => $b_alt, 'sizes' => '100vw' ) ) );
						} else {
							$b_file = ayat_home( 'banner' . $s . '_img' );
							$b_base = AYAT_URI . '/assets/images/demo/' . $b_file;
							printf(
								'<img class="ayat-banner__img" src="%1$s.webp" srcset="%1$s-m.webp 1000w, %1$s.webp 1983w" sizes="100vw" width="1983" height="793" alt="%2$s" loading="eager"%3$s decoding="async">',
								esc_url( $b_base ),
								esc_attr( $b_alt ),
								1 === $s ? ' fetchpriority="high"' : ''
							);
						}
						?>
					</a>
				</div>
			<?php endfor; ?>
		</div>
		<button class="ayat-slider__nav ayat-slider__nav--prev" type="button" data-slider-prev aria-label="<?php esc_attr_e( 'Previous banner', 'ayatcollections' ); ?>">&#8249;</button>
		<button class="ayat-slider__nav ayat-slider__nav--next" type="button" data-slider-next aria-label="<?php esc_attr_e( 'Next banner', 'ayatcollections' ); ?>">&#8250;</button>
		<div class="ayat-slider__dots" data-slider-dots>
			<?php for ( $s = 1; $s <= 6; $s++ ) : ?>
				<button type="button" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: banner number */ __( 'Go to banner %d', 'ayatcollections' ), $s ) ); ?>"<?php echo 1 === $s ? ' aria-current="true"' : ''; ?>></button>
			<?php endfor; ?>
		</div>
	</section>


	<?php if ( function_exists( 'ayat_sale_section' ) ) { ayat_sale_section(); } ?>

	<section class="home-sec" id="collections" aria-labelledby="col-title">
		<div class="wrap">
			<div class="sec-head" data-reveal>
				<h2 id="col-title"><?php echo esc_html( ayat_home( 'col_title' ) ); ?></h2>
				<a class="link-gold" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'View all', 'ayatcollections' ); ?></a>
			</div>
			<ul class="cat-rounds" role="list">
				<?php foreach ( $terms as $term ) : ?>
					<?php $img = ayat_collection_image_id( $term ); ?>
					<li>
						<a href="<?php echo esc_url( get_term_link( $term ) ); ?>" class="cat-rounds__link">
							<span class="cat-rounds__img"><?php echo $img ? wp_get_attachment_image( $img, 'woocommerce_thumbnail', false, array( 'alt' => '', 'loading' => 'lazy' ) ) : '<span class="cat-rounds__initial" aria-hidden="true">' . esc_html( mb_substr( $term->name, 0, 1 ) ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<span class="cat-rounds__name"><?php echo esc_html( $term->name ); ?></span>
							<?php /* translators: %d: number of products */ ?>
							<span class="cat-rounds__count"><?php echo esc_html( sprintf( _n( '%d piece', '%d pieces', (int) $term->count, 'ayatcollections' ), (int) $term->count ) ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

	<?php if ( class_exists( 'WooCommerce' ) ) : ?>
		<section class="home-sec home-sec--gold home-products" id="shop-now" aria-labelledby="new-title">
			<div class="wrap">
				<div class="sec-head" data-reveal>
					<h2 id="new-title"><?php echo esc_html( ayat_home( 'new_title' ) ); ?></h2>
					<a class="link-gold" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Shop all', 'ayatcollections' ); ?></a>
				</div>
				<div class="all-rounds"><?php echo do_shortcode( '[products limit="-1" columns="4" orderby="menu_order title" order="ASC" visibility="visible"]' ); ?></div>
			</div>
		</section>
	<?php endif; ?>

	<section class="home-sec models" aria-labelledby="models-title">
		<div class="wrap">
			<div class="sec-head sec-head--center">
				<p class="eyebrow"><?php esc_html_e( 'Lookbook', 'ayatcollections' ); ?></p>
				<h2 id="models-title"><?php echo esc_html( ayat_home( 'models_title' ) ); ?></h2>
			</div>
			<ul class="models__list" role="list">
				<?php for ( $m = 1; $m <= 3; $m++ ) : ?>
					<?php
					$m_term = get_term_by( 'slug', ayat_home( 'model' . $m . '_cat' ), 'product_cat' );
					$m_url  = $m_term && ! is_wp_error( $m_term ) ? get_term_link( $m_term ) : $shop_url;
					?>
					<li>
						<a class="models__card" href="<?php echo esc_url( $m_url ); ?>">
							<?php echo ayat_home_img( 'model' . $m . '_img', ayat_home( 'model' . $m . '_label' ), 'models__img' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span class="models__label"><?php echo esc_html( ayat_home( 'model' . $m . '_label' ) ); ?> <span aria-hidden="true">&rarr;</span></span>
						</a>
					</li>
				<?php endfor; ?>
			</ul>
		</div>
	</section>

	<?php if ( $look ) : ?>
		<section class="home-sec look" aria-labelledby="look-title">
			<div class="wrap look__inner">
				<figure class="look__media" data-reveal>
					<?php echo ayat_home_img( 'look_img', ayat_home( 'look_title' ), 'look__img' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</figure>
				<div class="look__copy" data-reveal>
					<p class="eyebrow"><?php esc_html_e( 'Shop the Look', 'ayatcollections' ); ?></p>
					<h2 id="look-title"><?php echo esc_html( ayat_home( 'look_title' ) ); ?></h2>
					<p><?php echo esc_html( ayat_home( 'look_text' ) ); ?></p>
					<ol class="look__list">
						<?php foreach ( $look as $prod ) : ?>
							<li>
								<a href="<?php echo esc_url( $prod->get_permalink() ); ?>">
									<?php echo $prod->get_image( 'woocommerce_thumbnail', array( 'loading' => 'lazy', 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<span class="look__name"><?php echo esc_html( $prod->get_name() ); ?></span>
									<span class="look__price"><?php echo wp_kses_post( $prod->get_price_html() ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ol>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php
	$reviews   = function_exists( 'ayat_get_reviews' ) ? ayat_get_reviews( 0, 6 ) : array();
	$rev_short = trim( (string) ayat_home( 'rev_shortcode' ) );
	?>
	<?php if ( $reviews || $rev_short ) : ?>
		<section class="home-sec reviews" aria-labelledby="rev-title">
			<div class="wrap">
				<div class="sec-head sec-head--center">
					<h2 id="rev-title"><?php echo esc_html( ayat_home( 'rev_title' ) ); ?></h2>
					<p><?php echo esc_html( ayat_home( 'rev_text' ) ); ?></p>
				</div>
				<?php
				if ( $reviews ) {
					ayat_render_reviews( $reviews );
				}
				if ( $rev_short ) {
					echo '<div class="reviews__widget">' . do_shortcode( $rev_short ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>
		</section>
	<?php endif; ?>

	<section class="perks" aria-label="<?php esc_attr_e( 'Why shop with us', 'ayatcollections' ); ?>">
		<ul class="wrap perks__list" role="list">
			<li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 7h12v9H2zM14 10h4l3 3v3h-7M5.5 19a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3ZM17.5 19a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z"/></svg><span><strong><?php esc_html_e( 'Cash on Delivery', 'ayatcollections' ); ?></strong><?php esc_html_e( 'Across Pakistan', 'ayatcollections' ); ?></span></li>
			<li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 4 6v6c0 4.5 3.4 8 8 9 4.6-1 8-4.5 8-9V6l-8-3Z"/><path d="m8.5 12 2.5 2.5 4.5-5"/></svg><span><strong><?php esc_html_e( 'Secure Shopping', 'ayatcollections' ); ?></strong><?php esc_html_e( 'Your trust matters', 'ayatcollections' ); ?></span></li>
			<li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7.5 12 3l9 4.5v9L12 21l-9-4.5zM3 7.5 12 12l9-4.5M12 12v9"/></svg><span><strong><?php esc_html_e( 'Gift Boxed', 'ayatcollections' ); ?></strong><?php esc_html_e( 'Easy exchange', 'ayatcollections' ); ?></span></li>
			<li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 14v-2a8 8 0 0 1 16 0v2"/><path d="M4 14h3v5H4zM17 14h3v5h-3zM20 19c0 1.5-2 2-5 2"/></svg><span><strong><?php esc_html_e( 'WhatsApp Support', 'ayatcollections' ); ?></strong><?php esc_html_e( 'Help before you order', 'ayatcollections' ); ?></span></li>
		</ul>
	</section>
</main>
<?php
get_footer();
