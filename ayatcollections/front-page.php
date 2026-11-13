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

	<section class="hero-box" aria-labelledby="hero-title">
		<div class="wrap hero-box__inner">
			<div class="hero-box__copy">
				<p class="eyebrow"><?php echo esc_html( ayat_home( 'hero_eyebrow' ) ); ?></p>
				<h1 id="hero-title" class="hero-box__title"><?php echo esc_html( ayat_home( 'hero_title' ) ); ?> <em><?php echo esc_html( ayat_home( 'hero_script' ) ); ?></em></h1>
				<p class="hero-box__text"><?php echo esc_html( ayat_home( 'hero_text' ) ); ?></p>
				<p class="hero-box__actions">
					<a class="btn-ink" href="<?php echo esc_url( ayat_resolve_link( ayat_home( 'hero_link' ) ) ); ?>"><?php echo esc_html( ayat_home( 'hero_btn' ) ); ?></a>
					<a class="link-gold" href="#collections"><?php esc_html_e( 'Browse collections', 'ayatcollections' ); ?></a>
				</p>
			</div>
			<div class="hero-box__stage">
				<div class="jbox is-play" data-jbox>
					<div class="jbox__tray">
						<div class="jbox__cushion">
							<img class="jbox__jewel" src="<?php echo esc_url( ayat_home_img_url( 'hero_img' ) ); ?>" width="900" height="692" alt="<?php esc_attr_e( 'Five gold-tone stainless steel cuff bangles with crystals, shown in a black velvet jewellery box', 'ayatcollections' ); ?>" fetchpriority="high" decoding="async">
							<span class="jbox__glint" aria-hidden="true"></span>
						</div>
						<div class="jbox__lid" aria-hidden="true">
							<span class="jbox__lid-out"><img src="<?php echo esc_url( AYAT_URI . '/assets/images/logo-shaya.webp' ); ?>" width="277" height="360" alt=""></span>
							<span class="jbox__lid-in"><img src="<?php echo esc_url( AYAT_URI . '/assets/images/logo-shaya.webp' ); ?>" width="277" height="360" alt=""></span>
						</div>
					</div>
				</div>
				<button class="jbox__replay" type="button" data-jbox-toggle aria-pressed="false" data-pause="<?php esc_attr_e( 'Pause', 'ayatcollections' ); ?>" data-play="<?php esc_attr_e( 'Play', 'ayatcollections' ); ?>"><span aria-hidden="true">&#10074;&#10074;</span> <span data-label><?php esc_html_e( 'Pause', 'ayatcollections' ); ?></span></button>
			</div>
		</div>
	</section>

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
		<section class="home-sec home-sec--tint" aria-labelledby="new-title">
			<div class="wrap">
				<div class="sec-head" data-reveal>
					<h2 id="new-title"><?php echo esc_html( ayat_home( 'new_title' ) ); ?></h2>
					<a class="link-gold" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Shop all', 'ayatcollections' ); ?></a>
				</div>
				<div class="all-rounds"><?php echo do_shortcode( '[products limit="-1" columns="4" orderby="menu_order title" order="ASC" visibility="visible"]' ); ?></div>
			</div>
		</section>
	<?php endif; ?>

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
