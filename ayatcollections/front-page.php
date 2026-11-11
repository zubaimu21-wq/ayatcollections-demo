<?php
/**
 * Homepage — section order follows the Weblium "Jewelry" template,
 * with WooCommerce collections and new arrivals added for shopping.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

get_header();
$h       = 'ayat_home';
$contact = isset( $_GET['contact'] ) ? sanitize_key( wp_unslash( $_GET['contact'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
?>
<main id="main" class="site-main home-main">

	<!-- 1. Hero -->
	<section class="hero-split">
		<div class="wrap hero-split__grid">
			<div class="hero-split__text">
				<h1 class="hero-split__title"><?php echo esc_html( $h( 'hero_title' ) ); ?></h1>
				<?php echo ayat_ornament(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<p class="hero-split__lead"><?php echo esc_html( $h( 'hero_text' ) ); ?></p>
				<a class="btn-gold" href="<?php echo esc_url( ayat_resolve_link( $h( 'hero_link' ) ) ); ?>"><?php echo esc_html( $h( 'hero_btn' ) ); ?></a>
			</div>
			<div class="hero-split__media hero-show" aria-label="<?php esc_attr_e( 'Featured jewellery', 'ayatcollections' ); ?>" role="img">
				<?php echo ayat_home_img( 'hero_img', __( 'Gold ring with a crystal bar', 'ayatcollections' ), 'hero-show__img', true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php
				$ayat_show = array(
					'demo-product-drop-earrings' => __( 'Gold-tone ribbed hoop earrings', 'ayatcollections' ),
					'demo-product-choker'        => __( 'Gold-tone heart pendant with crystals', 'ayatcollections' ),
					'demo-product-jhumka'        => __( 'Gold-tone jhumka earrings', 'ayatcollections' ),
				);
				foreach ( $ayat_show as $file => $alt ) {
					printf( '<img class="hero-show__img" src="%1$s" width="1000" height="1000" alt="%2$s" loading="lazy" decoding="async" data-demo-image="1">', esc_url( AYAT_URI . '/assets/images/demo/' . $file . '.webp' ), esc_attr( $alt ) );
				}
				?>
				<span class="hero-show__shine" aria-hidden="true"></span>
			</div>
		</div>
	</section>

	<?php
	$ayat_strip = function_exists( 'ayat_best_sellers' ) ? ayat_best_sellers( 8 ) : array();
	if ( $ayat_strip ) :
		?>
		<section class="marquee" aria-label="<?php esc_attr_e( 'Shop the collection', 'ayatcollections' ); ?>">
			<button type="button" class="marquee__toggle" aria-pressed="false" aria-label="<?php esc_attr_e( 'Pause moving products', 'ayatcollections' ); ?>" onclick="var m=this.parentNode,p=m.classList.toggle('is-paused');this.setAttribute('aria-pressed',p);this.innerHTML=p?'&#9654;':'&#10074;&#10074;';this.setAttribute('aria-label',p?'Play moving products':'Pause moving products');">&#10074;&#10074;</button>
			<div class="marquee__track">
				<?php for ( $ayat_loop = 0; $ayat_loop < 2; $ayat_loop++ ) : ?>
					<ul class="marquee__list" role="list"<?php echo $ayat_loop ? ' aria-hidden="true"' : ''; ?>>
						<?php foreach ( $ayat_strip as $p ) : ?>
							<li class="marquee__item">
								<a href="<?php echo esc_url( $p->get_permalink() ); ?>"<?php echo $ayat_loop ? ' tabindex="-1"' : ''; ?>>
									<?php echo $p->get_image( 'woocommerce_thumbnail', array( 'class' => 'marquee__img', 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<span class="marquee__name"><?php echo esc_html( $p->get_name() ); ?></span>
									<span class="marquee__price"><?php echo wp_kses_post( wc_price( $p->is_type( 'variable' ) ? $p->get_variation_price( 'min' ) : $p->get_price() ) ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endfor; ?>
			</div>
		</section>
	<?php endif; ?>

	<!-- 2. Collections -->
	<section class="band band--grey" id="collections" aria-labelledby="col-title">
		<div class="wrap">
			<h2 id="col-title" class="sec-title"><?php echo esc_html( $h( 'col_title' ) ); ?></h2>
			<?php echo ayat_ornament(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php ayat_render_collection_cards( ayat_get_collections( 8 ) ); ?>
		</div>
	</section>

	<?php if ( class_exists( 'WooCommerce' ) ) : ?>
		<section class="band" aria-labelledby="new-title">
			<div class="wrap">
				<h2 id="new-title" class="sec-title"><?php echo esc_html( $h( 'new_title' ) ); ?></h2>
				<?php echo ayat_ornament(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo do_shortcode( '[products limit="8" columns="4" orderby="date" order="DESC" visibility="visible"]' ); ?>
				<p class="center"><a class="btn-line" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'View All', 'ayatcollections' ); ?></a></p>
			</div>
		</section>
	<?php endif; ?>

	<!-- 3. Bridal gallery -->
	<section class="band band--grey" aria-labelledby="bes-title">
		<div class="wrap">
			<h2 id="bes-title" class="sec-title"><?php echo esc_html( $h( 'bes_title' ) ); ?></h2>
			<?php echo ayat_ornament(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<a class="gallery3" href="<?php echo esc_url( ayat_resolve_link( $h( 'bes_link' ) ) ); ?>">
				<span class="gallery3__main"><?php echo ayat_home_img( 'bes_img', __( 'Bride wearing gold kundan jewellery with a red sari', 'ayatcollections' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="gallery3__side"><?php echo ayat_home_img( 'bes_img2', __( 'Gold paisley earrings on black velvet', 'ayatcollections' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="gallery3__side"><?php echo ayat_home_img( 'bes_img3', __( 'Gold temple necklace with a ruby pendant', 'ayatcollections' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			</a>
		</div>
	</section>

	<!-- 4. What makes us special -->
	<section class="band band--dark" aria-labelledby="sp-title">
		<div class="wrap">
			<h2 id="sp-title" class="sec-title"><?php echo esc_html( $h( 'sp_title' ) ); ?></h2>
			<?php echo ayat_ornament(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<ul class="cards3" role="list">
				<?php for ( $i = 1; $i <= 3; $i++ ) : ?>
					<li class="cards3__item">
						<span class="cards3__media"><?php echo ayat_home_img( "sp{$i}_img", $h( "sp{$i}_text" ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<span class="cards3__cap"><?php echo esc_html( $h( "sp{$i}_text" ) ); ?></span>
					</li>
				<?php endfor; ?>
			</ul>
		</div>
	</section>

	<!-- 5. About -->
	<section class="split split--about" id="about" aria-labelledby="about-title">
		<div class="split__media split__media--white"><?php echo ayat_home_img( 'about_img', __( 'Woman wearing traditional gold jewellery', 'ayatcollections' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		<div class="split__text split__text--grey">
			<h2 id="about-title" class="sec-title sec-title--left"><?php echo esc_html( $h( 'about_title' ) ); ?></h2>
			<?php echo ayat_ornament(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div class="prose"><?php echo ayat_home_paras( 'about_text' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		</div>
	</section>

	<!-- 6. Dark banner -->
	<section class="royal" aria-labelledby="royal-title">
		<div class="royal__media"><?php echo ayat_home_img( 'royal_img', __( 'Gold crown pendant necklace on a dark background', 'ayatcollections' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		<div class="wrap royal__text">
			<h2 id="royal-title" class="royal__title"><?php echo esc_html( $h( 'royal_title' ) ); ?></h2>
			<p><?php echo esc_html( $h( 'royal_text' ) ); ?></p>
			<a class="btn-gold" href="<?php echo esc_url( ayat_resolve_link( $h( 'royal_link' ) ) ); ?>"><?php echo esc_html( $h( 'royal_btn' ) ); ?></a>
		</div>
	</section>

	<!-- 7. Services -->
	<section class="band band--grey" aria-labelledby="sv-title">
		<div class="wrap">
			<h2 id="sv-title" class="sec-title"><?php echo esc_html( $h( 'sv_title' ) ); ?></h2>
			<?php echo ayat_ornament(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<ul class="grid2" role="list">
				<?php for ( $i = 1; $i <= 4; $i++ ) : ?>
					<li class="grid2__item">
						<span class="grid2__media"><?php echo ayat_home_img( "sv{$i}_img", $h( "sv{$i}_text" ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<span class="grid2__cap"><?php echo esc_html( $h( "sv{$i}_text" ) ); ?></span>
					</li>
				<?php endfor; ?>
			</ul>
		</div>
	</section>

	<!-- 8. Steps -->
	<section class="band" aria-labelledby="st-title">
		<div class="wrap">
			<h2 id="st-title" class="sec-title"><?php echo esc_html( $h( 'st_title' ) ); ?></h2>
			<?php echo ayat_ornament(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<ol class="steps" role="list">
				<?php
				$icons = array(
					'<path d="M4 6h16v10H8l-4 4z"/>',
					'<path d="M5 19 19 5M14 5h5v5"/><circle cx="8" cy="8" r="3"/>',
					'<path d="m5 12 4 4 10-10"/>',
					'<path d="M3 8h13v9H3zM16 11h3l2 3v3h-5"/><circle cx="7" cy="18" r="1.6"/><circle cx="18" cy="18" r="1.6"/>',
				);
				for ( $i = 1; $i <= 4; $i++ ) :
					?>
					<li class="steps__item">
						<svg class="steps__icon" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true"><?php echo $icons[ $i - 1 ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></svg>
						<h3 class="steps__title"><?php echo esc_html( $h( "st{$i}" ) ); ?></h3>
						<p><?php echo esc_html( $h( "st{$i}_text" ) ); ?></p>
					</li>
				<?php endfor; ?>
			</ol>
		</div>
	</section>

	<!-- 9. Quote -->
	<section class="split split--quote" aria-labelledby="q-title">
		<div class="split__text split__text--grey">
			<h2 id="q-title" class="sec-title sec-title--left"><?php echo esc_html( $h( 'q_title' ) ); ?></h2>
			<?php echo ayat_ornament(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<blockquote class="quote"><p><?php echo esc_html( $h( 'q_text' ) ); ?></p><cite><?php echo esc_html( $h( 'q_by' ) ); ?></cite></blockquote>
		</div>
		<div class="split__media split__media--dark"><?php echo ayat_home_img( 'q_img', __( 'Gold and stone bracelets on a dark table', 'ayatcollections' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
	</section>

	<!-- 10. Shop online -->
	<section class="split split--boutique" aria-labelledby="b-title">
		<div class="split__media"><?php echo ayat_home_img( 'b_img', __( 'A wall of gold jhumka earrings in a jewellery shop', 'ayatcollections' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		<div class="split__text">
			<h2 id="b-title" class="sec-title sec-title--left"><?php echo esc_html( $h( 'b_title' ) ); ?></h2>
			<?php echo ayat_ornament(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<p class="split__lead"><?php echo esc_html( $h( 'b_lead' ) ); ?></p>
			<div class="prose"><?php echo ayat_home_paras( 'b_text' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<?php if ( get_theme_mod( 'ayat_address' ) ) : ?>
				<p class="prose"><strong><?php echo esc_html( get_theme_mod( 'ayat_address' ) ); ?></strong></p>
			<?php endif; ?>
		</div>
	</section>

	<!-- 11. Newsletter -->
	<section class="newsletter" aria-labelledby="nl-title">
		<div class="newsletter__media"><?php echo ayat_home_img( 'nl_img', '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		<div class="wrap newsletter__inner">
			<h2 id="nl-title" class="royal__title"><?php echo esc_html( $h( 'nl_title' ) ); ?></h2>
			<p><?php echo esc_html( $h( 'nl_text' ) ); ?></p>
			<?php if ( $h( 'nl_shortcode' ) ) : ?>
				<?php echo do_shortcode( $h( 'nl_shortcode' ) ); ?>
			<?php else : ?>
				<form class="newsletter__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="ayat_contact">
					<input type="hidden" name="name" value="Newsletter signup">
					<input type="hidden" name="message" value="Please add me to the Ayat Collections mailing list.">
					<?php wp_nonce_field( 'ayat_contact', 'ayat_contact_nonce' ); ?>
					<label class="screen-reader-text" for="nl-email"><?php esc_html_e( 'Email', 'ayatcollections' ); ?></label>
					<input id="nl-email" type="email" name="email" required placeholder="<?php esc_attr_e( 'Your email', 'ayatcollections' ); ?>">
					<button type="submit" class="btn-gold"><?php esc_html_e( 'Subscribe', 'ayatcollections' ); ?></button>
				</form>
			<?php endif; ?>
		</div>
	</section>

	<!-- 12. Tips -->
	<section class="band band--grey" aria-labelledby="tp-title">
		<div class="wrap">
			<h2 id="tp-title" class="sec-title"><?php echo esc_html( $h( 'tp_title' ) ); ?></h2>
			<?php echo ayat_ornament(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div class="tips">
				<?php for ( $i = 1; $i <= 3; $i++ ) : ?>
					<article class="tips__row<?php echo 2 === $i ? ' tips__row--flip' : ''; ?>">
						<div class="tips__text">
							<h3><?php echo esc_html( $h( "tp{$i}" ) ); ?></h3>
							<p><?php echo esc_html( $h( "tp{$i}_text" ) ); ?></p>
						</div>
						<div class="tips__media"><?php echo ayat_home_img( "tp{$i}_img", $h( "tp{$i}" ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
					</article>
				<?php endfor; ?>
			</div>
		</div>
	</section>

	<!-- 13. Trust strip (in place of press logos) -->
	<section class="trust" aria-label="<?php esc_attr_e( 'Why shop with us', 'ayatcollections' ); ?>">
		<ul class="wrap trust__list" role="list">
			<li><strong><?php esc_html_e( 'Cash on Delivery', 'ayatcollections' ); ?></strong><span><?php esc_html_e( 'Pay when it arrives', 'ayatcollections' ); ?></span></li>
			<li><strong><?php esc_html_e( 'Nationwide Delivery', 'ayatcollections' ); ?></strong><span><?php esc_html_e( '3–5 working days', 'ayatcollections' ); ?></span></li>
			<li><strong><?php esc_html_e( 'Gift Boxed', 'ayatcollections' ); ?></strong><span><?php esc_html_e( 'Every order', 'ayatcollections' ); ?></span></li>
			<li><strong><?php esc_html_e( 'Easy Exchange', 'ayatcollections' ); ?></strong><span><?php esc_html_e( 'Within 7 days', 'ayatcollections' ); ?></span></li>
		</ul>
	</section>

	<!-- 14. Contact -->
	<section class="contact" id="contact" aria-labelledby="ct-title" style="--bg:url('<?php echo esc_url( ayat_home_img_url( 'ct_img' ) ); ?>')">
		<div class="contact__card">
			<h2 id="ct-title" class="sec-title"><?php echo esc_html( $h( 'ct_title' ) ); ?></h2>
			<?php echo ayat_ornament(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<p class="contact__intro"><?php echo esc_html( $h( 'ct_text' ) ); ?></p>
			<?php if ( 'sent' === $contact ) : ?>
				<p class="contact__note contact__note--ok" role="status"><?php esc_html_e( 'Thank you! We will reply soon.', 'ayatcollections' ); ?></p>
			<?php elseif ( 'error' === $contact ) : ?>
				<p class="contact__note" role="alert"><?php esc_html_e( 'Please fill in your name, a phone or email, and a message.', 'ayatcollections' ); ?></p>
			<?php endif; ?>
			<form class="contact__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="ayat_contact">
				<?php wp_nonce_field( 'ayat_contact', 'ayat_contact_nonce' ); ?>
				<p class="hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></p>
				<label class="screen-reader-text" for="ct-name"><?php esc_html_e( 'Full name', 'ayatcollections' ); ?></label>
				<input id="ct-name" name="name" type="text" required autocomplete="name" placeholder="<?php esc_attr_e( 'Your full name *', 'ayatcollections' ); ?>">
				<label class="screen-reader-text" for="ct-phone"><?php esc_html_e( 'Phone', 'ayatcollections' ); ?></label>
				<input id="ct-phone" name="phone" type="tel" autocomplete="tel" placeholder="<?php esc_attr_e( 'Phone / WhatsApp', 'ayatcollections' ); ?>">
				<label class="screen-reader-text" for="ct-email"><?php esc_html_e( 'Email', 'ayatcollections' ); ?></label>
				<input id="ct-email" name="email" type="email" autocomplete="email" placeholder="<?php esc_attr_e( 'Email', 'ayatcollections' ); ?>">
				<label class="screen-reader-text" for="ct-msg"><?php esc_html_e( 'Message', 'ayatcollections' ); ?></label>
				<textarea id="ct-msg" name="message" rows="4" required placeholder="<?php esc_attr_e( 'Your message *', 'ayatcollections' ); ?>"></textarea>
				<button type="submit" class="btn-gold btn-gold--block"><?php esc_html_e( 'Send Message', 'ayatcollections' ); ?></button>
			</form>
		</div>
	</section>
</main>
<?php
get_footer();
