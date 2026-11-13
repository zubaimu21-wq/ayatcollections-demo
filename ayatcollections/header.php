<?php
/**
 * Site header: one row: logo, menu, then search, account and bag.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;
$announcement = get_theme_mod( 'ayat_announcement', __( 'Cash on Delivery across Pakistan', 'ayatcollections' ) );
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'ayatcollections' ); ?></a>

<?php if ( $announcement ) : ?>
	<div class="announcement"><p><?php echo esc_html( $announcement ); ?></p></div>
<?php endif; ?>

<header class="site-header" id="site-header">
	<div class="site-header__inner site-header__inner--full">
		<div class="site-branding"><?php ayat_logo(); ?></div>
		<nav id="primary-nav" class="primary-nav" aria-label="<?php esc_attr_e( 'Main', 'ayatcollections' ); ?>">
			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'menu_id' => 'primary-menu', 'depth' => 2, 'fallback_cb' => 'ayat_fallback_menu' ) ); ?>
		</nav>
		<div class="header-actions">
			<button class="header-icon search-toggle" type="button" aria-controls="header-search" aria-expanded="false"><?php echo ayat_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="screen-reader-text"><?php esc_html_e( 'Search', 'ayatcollections' ); ?></span></button>
			<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
				<a class="header-icon header-account" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php echo ayat_icon( 'user' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="screen-reader-text"><?php esc_html_e( 'My account', 'ayatcollections' ); ?></span></a>
			<?php endif; ?>
			<?php echo ayat_bag_link(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<button class="header-icon menu-toggle" type="button" aria-controls="primary-nav" aria-expanded="false"><?php echo ayat_icon( 'menu' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="screen-reader-text"><?php esc_html_e( 'Menu', 'ayatcollections' ); ?></span></button>
		</div>
	</div>
	<?php
	$strip_cats = function_exists( 'ayat_get_collections' ) ? ayat_get_collections() : array();
	if ( $strip_cats ) :
		$strip_shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
		?>
		<nav class="cat-strip" aria-label="<?php esc_attr_e( 'Shop by category', 'ayatcollections' ); ?>">
			<ul class="cat-strip__list" role="list">
				<li><a class="cat-strip__link cat-strip__link--all<?php echo ( function_exists( 'is_shop' ) && is_shop() ) ? ' is-current' : ''; ?>" href="<?php echo esc_url( $strip_shop ); ?>"><span class="cat-strip__dot" aria-hidden="true">&#10022;</span><?php esc_html_e( 'All', 'ayatcollections' ); ?></a></li>
				<?php foreach ( $strip_cats as $strip_term ) : ?>
					<?php
					$strip_img = function_exists( 'ayat_collection_image_id' ) ? ayat_collection_image_id( $strip_term ) : 0;
					$strip_cur = function_exists( 'is_product_category' ) && is_product_category( $strip_term->slug );
					?>
					<li><a class="cat-strip__link<?php echo $strip_cur ? ' is-current' : ''; ?>" href="<?php echo esc_url( get_term_link( $strip_term ) ); ?>"<?php echo $strip_cur ? ' aria-current="page"' : ''; ?>>
						<span class="cat-strip__thumb" aria-hidden="true"><?php echo $strip_img ? wp_get_attachment_image( $strip_img, 'thumbnail', false, array( 'alt' => '', 'loading' => 'eager' ) ) : esc_html( mb_substr( $strip_term->name, 0, 1 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<?php echo esc_html( $strip_term->name ); ?>
					</a></li>
				<?php endforeach; ?>
			</ul>
		</nav>
	<?php endif; ?>
	<div class="header-search" id="header-search" hidden>
		<div class="wrap"><?php if ( function_exists( 'get_product_search_form' ) ) { get_product_search_form(); } else { get_search_form(); } ?></div>
	</div>
</header>
