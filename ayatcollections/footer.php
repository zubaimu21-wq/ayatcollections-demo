<?php
/**
 * Footer: brand + socials, shop links, help links, contact, bottom bar.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;
$socials  = ayat_social_links();
$phone    = get_theme_mod( 'ayat_phone', '' );
$email    = get_theme_mod( 'ayat_email', '' );
$address  = get_theme_mod( 'ayat_address', '' );
$whatsapp = isset( $socials['WhatsApp'] ) ? $socials['WhatsApp'] : '';
$cats     = function_exists( 'ayat_get_collections' ) ? array_slice( ayat_get_collections(), 0, 6 ) : array();
$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
?>
<footer class="site-footer">
	<div class="wrap site-footer__grid">
		<div class="site-footer__brand">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-footer__logo"><img src="<?php echo esc_url( AYAT_URI . '/assets/images/logo-shaya.webp' ); ?>" width="277" height="360" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" loading="lazy"></a>
			<p class="site-footer__about"><?php esc_html_e( 'Gold-tone stainless steel jewellery: anti-tarnish, waterproof and made for every day.', 'ayatcollections' ); ?></p>
			<?php if ( $socials ) : ?>
				<ul class="site-footer__social" role="list">
					<?php foreach ( $socials as $label => $url ) : ?>
						<li><a href="<?php echo esc_url( $url ); ?>" rel="noopener" aria-label="<?php echo esc_attr( $label ); ?>"><?php echo ayat_social_icon( $label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<nav class="site-footer__col" aria-label="<?php esc_attr_e( 'Shop', 'ayatcollections' ); ?>">
			<h2 class="site-footer__title"><?php esc_html_e( 'Shop', 'ayatcollections' ); ?></h2>
			<ul role="list">
				<?php foreach ( $cats as $term ) : ?>
					<li><a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a></li>
				<?php endforeach; ?>
				<li><a href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'All products', 'ayatcollections' ); ?></a></li>
			</ul>
		</nav>

		<nav class="site-footer__col" aria-label="<?php esc_attr_e( 'Help', 'ayatcollections' ); ?>">
			<h2 class="site-footer__title"><?php esc_html_e( 'Help', 'ayatcollections' ); ?></h2>
			<ul role="list">
				<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
					<li><a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'My account', 'ayatcollections' ); ?></a></li>
					<li><a href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'Shopping bag', 'ayatcollections' ); ?></a></li>
				<?php endif; ?>
				<?php if ( get_privacy_policy_url() ) : ?>
					<li><a href="<?php echo esc_url( get_privacy_policy_url() ); ?>"><?php esc_html_e( 'Privacy policy', 'ayatcollections' ); ?></a></li>
				<?php endif; ?>
				<?php if ( $whatsapp ) : ?>
					<li><a href="<?php echo esc_url( $whatsapp ); ?>" rel="noopener"><?php esc_html_e( 'Order on WhatsApp', 'ayatcollections' ); ?></a></li>
				<?php endif; ?>
			</ul>
		</nav>

		<div class="site-footer__col">
			<h2 class="site-footer__title"><?php esc_html_e( 'Contact', 'ayatcollections' ); ?></h2>
			<ul role="list" class="site-footer__contact">
				<?php if ( $phone ) : ?><li><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></li><?php endif; ?>
				<?php if ( $email ) : ?><li><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></li><?php endif; ?>
				<?php if ( $address ) : ?><li><?php echo esc_html( $address ); ?></li><?php endif; ?>
				<li><?php esc_html_e( 'Cash on Delivery across Pakistan', 'ayatcollections' ); ?></li>
				<li><?php esc_html_e( 'Delivery in 3–5 working days', 'ayatcollections' ); ?></li>
			</ul>
		</div>
	</div>
	<div class="site-footer__bottom">
		<div class="wrap site-footer__bottom-inner">
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>. <?php esc_html_e( 'All rights reserved.', 'ayatcollections' ); ?></p>
			<p><?php esc_html_e( 'SHAYA by Aayat Collections', 'ayatcollections' ); ?></p>
		</div>
		<p class="wrap site-footer__credit">
			<?php esc_html_e( 'Website created by Optix Technologies', 'ayatcollections' ); ?> &middot;
			<?php esc_html_e( 'Need a website?', 'ayatcollections' ); ?>
			<a href="https://wa.me/923392281890" rel="noopener"><?php esc_html_e( 'WhatsApp +92 339 2281890', 'ayatcollections' ); ?></a>
		</p>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
