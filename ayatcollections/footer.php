<?php
/**
 * Footer: logo, link columns, social links and copyright.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

$socials = ayat_social_links();
$shop    = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
$colls   = get_page_by_path( 'collections' );
$arr     = get_page_by_path( 'new-arrivals' );
?>
<footer class="site-footer">
	<div class="wrap site-footer__grid">
		<div class="site-footer__brand">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-footer__logo">
				<img src="<?php echo esc_url( AYAT_URI . '/assets/images/logo-gold.webp' ); ?>" width="68" height="70" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" loading="lazy">
			</a>
			<?php if ( get_theme_mod( 'ayat_address' ) ) : ?>
				<p><?php echo esc_html( get_theme_mod( 'ayat_address' ) ); ?></p>
			<?php endif; ?>
		</div>

		<nav class="site-footer__col" aria-label="<?php esc_attr_e( 'Shop', 'ayatcollections' ); ?>">
			<h2 class="site-footer__title"><?php esc_html_e( 'Shop', 'ayatcollections' ); ?></h2>
			<ul role="list">
				<li><a href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'All jewellery', 'ayatcollections' ); ?></a></li>
				<?php if ( $arr ) : ?><li><a href="<?php echo esc_url( get_permalink( $arr ) ); ?>"><?php esc_html_e( 'New Arrivals', 'ayatcollections' ); ?></a></li><?php endif; ?>
				<?php if ( $colls ) : ?><li><a href="<?php echo esc_url( get_permalink( $colls ) ); ?>"><?php esc_html_e( 'Collections', 'ayatcollections' ); ?></a></li><?php endif; ?>
			</ul>
		</nav>

		<nav class="site-footer__col" aria-label="<?php esc_attr_e( 'Collections', 'ayatcollections' ); ?>">
			<h2 class="site-footer__title"><?php esc_html_e( 'Collections', 'ayatcollections' ); ?></h2>
			<ul role="list">
				<?php foreach ( ayat_get_collections( 6 ) as $t ) : ?>
					<li><a href="<?php echo esc_url( get_term_link( $t ) ); ?>"><?php echo esc_html( $t->name ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</nav>

		<nav class="site-footer__col" aria-label="<?php esc_attr_e( 'Help', 'ayatcollections' ); ?>">
			<h2 class="site-footer__title"><?php esc_html_e( 'Help', 'ayatcollections' ); ?></h2>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => false,
					'depth'          => 1,
					'fallback_cb'    => static function () {
						echo '<ul role="list">';
						echo '<li><a href="' . esc_url( home_url( '/#about' ) ) . '">' . esc_html__( 'About us', 'ayatcollections' ) . '</a></li>';
						echo '<li><a href="' . esc_url( home_url( '/#contact' ) ) . '">' . esc_html__( 'Contact us', 'ayatcollections' ) . '</a></li>';
						if ( function_exists( 'wc_get_page_permalink' ) ) {
							echo '<li><a href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '">' . esc_html__( 'My account', 'ayatcollections' ) . '</a></li>';
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

		<div class="site-footer__col">
			<h2 class="site-footer__title"><?php esc_html_e( 'Follow Us', 'ayatcollections' ); ?></h2>
			<?php if ( $socials ) : ?>
				<ul class="site-footer__social" role="list">
					<?php foreach ( $socials as $label => $url ) : ?>
						<li><a href="<?php echo esc_url( $url ); ?>" rel="noopener" aria-label="<?php echo esc_attr( $label ); ?>"><?php echo ayat_social_icon( $label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php if ( get_theme_mod( 'ayat_phone' ) ) : ?>
				<p><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', get_theme_mod( 'ayat_phone' ) ) ); ?>"><?php echo esc_html( get_theme_mod( 'ayat_phone' ) ); ?></a></p>
			<?php endif; ?>
		</div>
	</div>
	<div class="wrap site-footer__bottom">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
		<p><?php esc_html_e( 'All rights reserved', 'ayatcollections' ); ?></p>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
