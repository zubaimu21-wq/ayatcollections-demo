<?php
/**
 * Footer: social links, centred logo, tagline, copyright and links.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;
$socials = ayat_social_links();
?>
<footer class="site-footer">
	<div class="wrap site-footer__row">
		<ul class="site-footer__social" role="list">
			<?php foreach ( $socials as $label => $url ) : ?>
				<li><a href="<?php echo esc_url( $url ); ?>" rel="noopener" aria-label="<?php echo esc_attr( $label ); ?>"><?php echo ayat_social_icon( $label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
			<?php endforeach; ?>
		</ul>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-footer__logo"><img src="<?php echo esc_url( AYAT_URI . '/assets/images/logo-gold.webp' ); ?>" width="116" height="120" alt="" loading="lazy"><span class="site-logo__text">Ayat <span>Collections</span></span></a>
		<p class="site-footer__tagline"><?php esc_html_e( 'More than just jewellery', 'ayatcollections' ); ?> &#9825;</p>
	</div>
	<div class="wrap site-footer__bottom">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>. <?php esc_html_e( 'All rights reserved.', 'ayatcollections' ); ?></p>
		<p class="site-footer__links">
			<?php if ( get_privacy_policy_url() ) : ?><a href="<?php echo esc_url( get_privacy_policy_url() ); ?>"><?php esc_html_e( 'Privacy Policy', 'ayatcollections' ); ?></a> | <?php endif; ?>
			<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?><a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'My Account', 'ayatcollections' ); ?></a> | <?php endif; ?>
			<a href="<?php echo esc_url( home_url( '/#contact' ) ); ?>"><?php esc_html_e( 'Contact Us', 'ayatcollections' ); ?></a>
		</p>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
