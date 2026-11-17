<?php
/**
 * Site header: utility links (left), logo (centre), search + account + bag (right),
 * then a strip of category chips that link straight to each category.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;
$track_url    = function_exists( 'ayat_track_url' ) ? ayat_track_url() : '';
$head_wa      = function_exists( 'ayat_store_whatsapp' ) ? ayat_store_whatsapp() : preg_replace( '/^0/', '92', preg_replace( '/[^0-9]/', '', (string) get_theme_mod( 'ayat_phone', '0311 0290489' ) ) );
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

<?php if ( function_exists( 'ayat_sale_bar' ) ) { ayat_sale_bar(); } ?>

<header class="site-header site-header--v3" id="site-header">
	<div class="site-header__inner site-header__inner--full">
		<div class="header-left">
			<button class="header-icon menu-toggle" type="button" aria-controls="primary-nav" aria-expanded="false"><?php echo ayat_icon( 'menu' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="screen-reader-text"><?php esc_html_e( 'Menu', 'ayatcollections' ); ?></span></button>
			<?php if ( $track_url ) : ?>
				<a class="header-util" href="<?php echo esc_url( $track_url ); ?>">
					<?php echo ayat_brand_logo( 'Track', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span class="header-util__text"><?php esc_html_e( 'Track order', 'ayatcollections' ); ?></span>
				</a>
			<?php endif; ?>
			<?php if ( $head_wa ) : ?>
				<a class="header-util header-util--wa" href="<?php echo esc_attr( 'https://wa.me/' . $head_wa ); ?>" target="_blank" rel="noopener">
					<?php echo ayat_brand_logo( 'WhatsApp', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span class="header-util__text"><?php esc_html_e( 'Order on WhatsApp', 'ayatcollections' ); ?></span>
				</a>
			<?php endif; ?>
		</div>

		<div class="site-branding"><?php ayat_logo(); ?></div>

		<div class="header-actions">
			<form class="header-searchbar ayat-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label class="screen-reader-text" for="header-searchbar-input"><?php esc_html_e( 'Search jewellery', 'ayatcollections' ); ?></label>
				<input id="header-searchbar-input" type="search" name="s" placeholder="<?php esc_attr_e( 'Search cuffs, rings, earrings…', 'ayatcollections' ); ?>" autocomplete="off">
				<?php if ( class_exists( 'WooCommerce' ) ) : ?><input type="hidden" name="post_type" value="product"><?php endif; ?>
				<button type="submit" aria-label="<?php esc_attr_e( 'Search', 'ayatcollections' ); ?>"><?php echo ayat_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
			</form>
			<button class="header-icon search-toggle" type="button" aria-controls="header-search" aria-expanded="false"><?php echo ayat_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="screen-reader-text"><?php esc_html_e( 'Search', 'ayatcollections' ); ?></span></button>
			<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
				<a class="header-icon header-account" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php echo ayat_icon( 'user' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="screen-reader-text"><?php esc_html_e( 'My account', 'ayatcollections' ); ?></span></a>
			<?php endif; ?>
			<?php echo ayat_bag_link(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>

	<nav id="primary-nav" class="primary-nav" aria-label="<?php esc_attr_e( 'Main', 'ayatcollections' ); ?>">
		<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'menu_id' => 'primary-menu', 'depth' => 2, 'fallback_cb' => 'ayat_fallback_menu' ) ); ?>
		<?php if ( $track_url ) : ?>
			<p class="primary-nav__extra"><a href="<?php echo esc_url( $track_url ); ?>"><?php esc_html_e( 'Track your order', 'ayatcollections' ); ?></a></p>
		<?php endif; ?>
	</nav>

	<?php
	$strip_cats = function_exists( 'ayat_get_collections' ) ? ayat_get_collections() : array();
	if ( $strip_cats ) :
		$strip_shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
		?>
		<nav class="cat-strip" aria-label="<?php esc_attr_e( 'Shop by category', 'ayatcollections' ); ?>">
			<ul class="cat-strip__list" role="list">
				<li><a class="cat-strip__link<?php echo ( function_exists( 'is_shop' ) && is_shop() ) ? ' is-current' : ''; ?>" href="<?php echo esc_url( $strip_shop ); ?>"><span class="cat-strip__thumb"><?php echo ayat_cat_icon( 'all' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><?php esc_html_e( 'All', 'ayatcollections' ); ?></a></li>
				<?php foreach ( $strip_cats as $strip_term ) : ?>
					<?php $strip_cur = function_exists( 'is_product_category' ) && is_product_category( $strip_term->slug ); ?>
					<li><a class="cat-strip__link<?php echo $strip_cur ? ' is-current' : ''; ?>" href="<?php echo esc_url( get_term_link( $strip_term ) ); ?>"<?php echo $strip_cur ? ' aria-current="page"' : ''; ?>><span class="cat-strip__thumb"><?php echo ayat_cat_icon( $strip_term->slug ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><?php echo esc_html( $strip_term->name ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</nav>
	<?php endif; ?>

	<div class="header-search" id="header-search" hidden>
		<div class="wrap"><?php if ( function_exists( 'get_product_search_form' ) ) { get_product_search_form(); } else { get_search_form(); } ?></div>
	</div>
</header>
