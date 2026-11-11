<?php
/**
 * Site footer.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

$phone = get_theme_mod( 'ayat_phone', '' );
$email = get_theme_mod( 'ayat_email', '' );
$insta = get_theme_mod( 'ayat_instagram', '' );
$fb    = get_theme_mod( 'ayat_facebook', '' );
?>
<footer class="site-footer">
	<div class="container site-footer__grid">
		<div class="footer-brand">
			<p class="footer-logo"><img src="<?php echo esc_url( AYAT_URI . '/assets/images/logo-black.webp' ); ?>" width="117" height="120" alt="" loading="lazy"><span>Ayat Collections</span></p>
			<p><?php echo esc_html( get_theme_mod( 'ayat_footer_about', __( 'Artificial jewellery designed in Pakistan — kundan, pearls and everyday gold tone.', 'ayatcollections' ) ) ); ?></p>
		</div>

		<nav class="footer-col" aria-label="<?php esc_attr_e( 'Shop links', 'ayatcollections' ); ?>">
			<h2 class="footer-title"><?php esc_html_e( 'Shop', 'ayatcollections' ); ?></h2>
			<ul role="list">
				<?php foreach ( ayat_get_collections( 6 ) as $term ) : ?>
					<li><a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</nav>

		<nav class="footer-col" aria-label="<?php esc_attr_e( 'Help links', 'ayatcollections' ); ?>">
			<h2 class="footer-title"><?php esc_html_e( 'Help', 'ayatcollections' ); ?></h2>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => false,
					'depth'          => 1,
					'fallback_cb'    => static function () {
						echo '<ul role="list">';
						if ( function_exists( 'wc_get_page_permalink' ) ) {
							echo '<li><a href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '">' . esc_html__( 'My account', 'ayatcollections' ) . '</a></li>';
							echo '<li><a href="' . esc_url( wc_get_cart_url() ) . '">' . esc_html__( 'Bag', 'ayatcollections' ) . '</a></li>';
						}
						$privacy = get_privacy_policy_url();
						if ( $privacy ) {
							echo '<li><a href="' . esc_url( $privacy ) . '">' . esc_html__( 'Privacy policy', 'ayatcollections' ) . '</a></li>';
						}
						echo '</ul>';
					},
				)
			);
			?>
		</nav>

		<div class="footer-col">
			<h2 class="footer-title"><?php esc_html_e( 'Contact', 'ayatcollections' ); ?></h2>
			<ul role="list">
				<?php if ( $phone ) : ?>
					<li><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></li>
				<?php endif; ?>
				<?php if ( $email ) : ?>
					<li><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></li>
				<?php endif; ?>
				<?php if ( $insta ) : ?>
					<li><a href="<?php echo esc_url( $insta ); ?>" rel="noopener">Instagram</a></li>
				<?php endif; ?>
				<?php if ( $fb ) : ?>
					<li><a href="<?php echo esc_url( $fb ); ?>" rel="noopener">Facebook</a></li>
				<?php endif; ?>
				<?php if ( ! $phone && ! $email && ! $insta && ! $fb && current_user_can( 'edit_theme_options' ) ) : ?>
					<li><?php esc_html_e( 'Add contact details in Appearance → Customize → Store details.', 'ayatcollections' ); ?></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
	<div class="container site-footer__bottom">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
