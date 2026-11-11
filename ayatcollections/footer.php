<?php
/**
 * Site footer: call to action, logo, links and social icons.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

$socials  = ayat_social_links();
$cta_link = ayat_home( 'cta_link' );
if ( '' === $cta_link ) {
	$cta_link = isset( $socials['WhatsApp'] ) ? $socials['WhatsApp'] : ( get_theme_mod( 'ayat_email' ) ? 'mailto:' . antispambot( get_theme_mod( 'ayat_email' ) ) : '' );
}
if ( '' === $cta_link ) {
	$cta_link = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
}
$icons = array(
	'Facebook'  => 'facebook.svg',
	'Instagram' => 'instagram.svg',
	'YouTube'   => 'youtube.svg',
);
?>
<footer class="site-footer">
	<div class="wrap site-footer__cta">
		<h2 class="display display--center"><?php echo esc_html( ayat_home( 'cta_title' ) ); ?></h2>
		<p class="site-footer__text"><?php echo esc_html( ayat_home( 'cta_text' ) ); ?></p>
		<?php if ( $cta_link ) : ?>
			<a class="btn-line btn-line--light" href="<?php echo esc_url( ayat_resolve_link( $cta_link ) ); ?>" rel="noopener"><?php echo esc_html( ayat_home( 'cta_btn' ) ); ?></a>
		<?php endif; ?>
	</div>

	<div class="wrap site-footer__bar">
		<a class="site-footer__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<img src="<?php echo esc_url( AYAT_URI . '/assets/images/logo-gold.webp' ); ?>" width="58" height="60" alt="" loading="lazy">
			<span>Ayat Collections</span>
		</a>

		<nav class="site-footer__links" aria-label="<?php esc_attr_e( 'Footer', 'ayatcollections' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => false,
					'depth'          => 1,
					'fallback_cb'    => static function () {
						echo '<ul role="list">';
						foreach ( ayat_get_collections( 4 ) as $t ) {
							echo '<li><a href="' . esc_url( get_term_link( $t ) ) . '">' . esc_html( $t->name ) . '</a></li>';
						}
						if ( function_exists( 'wc_get_page_permalink' ) ) {
							echo '<li><a href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '">' . esc_html__( 'My account', 'ayatcollections' ) . '</a></li>';
						}
						$privacy = get_privacy_policy_url();
						if ( $privacy ) {
							echo '<li><a href="' . esc_url( $privacy ) . '">' . esc_html__( 'Privacy', 'ayatcollections' ) . '</a></li>';
						}
						echo '</ul>';
					},
				)
			);
			?>
		</nav>

		<?php if ( $socials ) : ?>
			<ul class="site-footer__social" role="list">
				<?php foreach ( $socials as $label => $url ) : ?>
					<li>
						<a href="<?php echo esc_url( $url ); ?>" rel="noopener" aria-label="<?php echo esc_attr( $label ); ?>">
							<?php if ( isset( $icons[ $label ] ) ) : ?>
								<img src="<?php echo esc_url( AYAT_URI . '/assets/images/design/' . $icons[ $label ] ); ?>" width="43" height="45" alt="" loading="lazy">
							<?php else : ?>
								<svg width="43" height="45" viewBox="0 0 43 45" aria-hidden="true"><ellipse cx="21.5" cy="22.5" rx="21" ry="22" fill="none" stroke="#fff"/><path fill="#fff" d="M21.6 13.5a8.9 8.9 0 0 0-7.6 13.4l-1.2 4.4 4.5-1.2a8.9 8.9 0 1 0 4.3-16.6Zm0 16.3c-1.4 0-2.7-.4-3.8-1.1l-.3-.2-2.7.7.7-2.6-.2-.3a7.4 7.4 0 1 1 6.3 3.5Zm4-5.5c-.2-.1-1.3-.7-1.5-.7-.2-.1-.4-.1-.5.1l-.7.9c-.1.2-.3.2-.5.1a6 6 0 0 1-3-2.6c-.2-.4.2-.4.6-1.2.1-.1 0-.3 0-.4l-.7-1.6c-.2-.4-.4-.4-.5-.4h-.4c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 1.9s.8 2.2.9 2.4c.1.1 1.6 2.5 4 3.5 1.5.6 2 .7 2.8.6.4-.1 1.3-.5 1.5-1.1.2-.5.2-1 .1-1.1l-.5-.2Z"/></svg>
							<?php endif; ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
	<p class="wrap site-footer__copy">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
</footer>
<?php wp_footer(); ?>
</body>
</html>
