<?php
/**
 * Footer: social links, centred logo, contact, policy links.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;
$socials = ayat_social_links();
$phone   = get_theme_mod( 'ayat_phone', '' );
$email   = get_theme_mod( 'ayat_email', '' );
$contact = isset( $socials['WhatsApp'] ) ? $socials['WhatsApp'] : ( $email ? 'mailto:' . $email : '' );
?>
<footer class="site-footer">
	<div class="wrap site-footer__row">
		<ul class="site-footer__social" role="list">
			<?php foreach ( $socials as $label => $url ) : ?>
				<li><a href="<?php echo esc_url( $url ); ?>" rel="noopener" aria-label="<?php echo esc_attr( $label ); ?>"><?php echo ayat_social_icon( $label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
			<?php endforeach; ?>
		</ul>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-footer__logo"><img src="<?php echo esc_url( AYAT_URI . '/assets/images/logo-gold.webp' ); ?>" width="116" height="120" alt="" loading="lazy"><span class="site-logo__text">Ayat <span>Collections</span></span></a>
		<p class="site-footer__contact">
			<?php if ( $phone ) : ?><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a><?php endif; ?>
			<?php if ( $email ) : ?><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a><?php endif; ?>
		</p>
	</div>
	<div class="wrap site-footer__bottom">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>. <?php esc_html_e( 'All rights reserved.', 'ayatcollections' ); ?></p>
		<p class="site-footer__links">
			<?php if ( get_privacy_policy_url() ) : ?><a href="<?php echo esc_url( get_privacy_policy_url() ); ?>"><?php esc_html_e( 'Privacy Policy', 'ayatcollections' ); ?></a><?php endif; ?>
			<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?><a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'My Account', 'ayatcollections' ); ?></a><?php endif; ?>
			<?php if ( $contact ) : ?><a href="<?php echo esc_url( $contact ); ?>"><?php esc_html_e( 'Contact Us', 'ayatcollections' ); ?></a><?php endif; ?>
		</p>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
