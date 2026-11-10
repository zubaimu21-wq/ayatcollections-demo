<?php
/**
 * WooCommerce integration. Uses native WooCommerce templates and data; this
 * file only changes wrappers, labels and small presentation details.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

/* ---------- Wrappers ---------- */

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

add_action(
	'woocommerce_before_main_content',
	static function () {
		echo '<main id="main" class="site-main shop-main"><div class="container">';
	},
	10
);
add_action(
	'woocommerce_after_main_content',
	static function () {
		echo '</div></main>';
	},
	10
);

/**
 * Breadcrumb markup.
 */
add_filter(
	'woocommerce_breadcrumb_defaults',
	static function ( $args ) {
		$args['delimiter']   = '<span class="sep" aria-hidden="true">/</span>';
		$args['wrap_before'] = '<nav class="woocommerce-breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'ayatcollections' ) . '">';
		$args['wrap_after']  = '</nav>';
		return $args;
	}
);

/* ---------- Labels ---------- */

add_filter( 'woocommerce_product_single_add_to_cart_text', static fn() => __( 'Add to Bag', 'ayatcollections' ) );
add_filter(
	'woocommerce_product_add_to_cart_text',
	static function ( $text, $product ) {
		if ( $product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() ) {
			return __( 'Add to Bag', 'ayatcollections' );
		}
		return $text;
	},
	10,
	2
);

/**
 * Always show stock status on product pages, even when stock is not managed.
 */
add_filter(
	'woocommerce_get_availability_text',
	static function ( $text, $product ) {
		if ( '' === $text && $product->is_in_stock() ) {
			return __( 'In stock', 'ayatcollections' );
		}
		return $text;
	},
	10,
	2
);

/* ---------- Header bag count (AJAX fragments) ---------- */

add_filter(
	'woocommerce_add_to_cart_fragments',
	static function ( $fragments ) {
		$fragments['a.header-bag'] = ayat_bag_link();
		return $fragments;
	}
);

/* ---------- Shop / collection toolbar ---------- */

remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

/**
 * Search, collection filter and sorting above the product grid.
 */
function ayat_shop_toolbar() {
	$current = is_product_category() ? get_queried_object_id() : 0;
	$shop    = wc_get_page_permalink( 'shop' );
	$terms   = ayat_get_collections();
	?>
	<div class="shop-toolbar">
		<div class="shop-toolbar__search"><?php get_product_search_form(); ?></div>

		<nav class="shop-toolbar__filter" aria-label="<?php esc_attr_e( 'Filter by collection', 'ayatcollections' ); ?>">
			<ul class="chip-list" role="list">
				<li><a class="chip<?php echo $current ? '' : ' is-active'; ?>" href="<?php echo esc_url( $shop ); ?>"<?php echo $current ? '' : ' aria-current="page"'; ?>><?php esc_html_e( 'All', 'ayatcollections' ); ?></a></li>
				<?php foreach ( $terms as $term ) : ?>
					<?php $active = ( $current === $term->term_id ) || ( $current && term_is_ancestor_of( $term->term_id, $current, 'product_cat' ) ); ?>
					<li><a class="chip<?php echo $active ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $term ) ); ?>"<?php echo $active ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $term->name ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</nav>

		<div class="shop-toolbar__meta">
			<?php woocommerce_result_count(); ?>
			<?php woocommerce_catalog_ordering(); ?>
		</div>
	</div>
	<?php
	if ( is_active_sidebar( 'shop-filters' ) ) {
		echo '<details class="shop-filters"><summary>' . esc_html__( 'More filters', 'ayatcollections' ) . '</summary><div class="shop-filters__body">';
		dynamic_sidebar( 'shop-filters' );
		echo '</div></details>';
	}
}
add_action( 'woocommerce_before_shop_loop', 'ayat_shop_toolbar', 15 );

/**
 * Sorting options: keep price sorting prominent.
 */
add_filter(
	'woocommerce_catalog_orderby',
	static function ( $options ) {
		$options['date']       = __( 'Newest first', 'ayatcollections' );
		$options['price']      = __( 'Price: low to high', 'ayatcollections' );
		$options['price-desc'] = __( 'Price: high to low', 'ayatcollections' );
		return $options;
	}
);

/* ---------- Product cards ---------- */

add_filter( 'loop_shop_columns', static fn() => 4 );
add_filter( 'loop_shop_per_page', static fn() => 16 );

/**
 * "New" badge for products published in the last 30 days.
 */
add_action(
	'woocommerce_before_shop_loop_item_title',
	static function () {
		global $product;
		if ( ! $product || $product->is_on_sale() ) {
			return;
		}
		$created = $product->get_date_created();
		if ( $created && ( time() - $created->getTimestamp() ) < 30 * DAY_IN_SECONDS ) {
			echo '<span class="badge badge--new">' . esc_html__( 'New', 'ayatcollections' ) . '</span>';
		}
	},
	9
);

/**
 * Collection name above the card title.
 */
add_action(
	'woocommerce_shop_loop_item_title',
	static function () {
		global $product;
		if ( ! $product ) {
			return;
		}
		$terms = get_the_terms( $product->get_id(), 'product_cat' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			echo '<span class="card-cat">' . esc_html( $terms[0]->name ) . '</span>';
		}
	},
	5
);

/**
 * Product card titles are h3 inside a section that has an h2.
 */
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
add_action(
	'woocommerce_shop_loop_item_title',
	static function () {
		echo '<h3 class="woocommerce-loop-product__title">' . esc_html( get_the_title() ) . '</h3>';
	},
	10
);

/**
 * Sale flash text.
 */
add_filter( 'woocommerce_sale_flash', static fn() => '<span class="onsale">' . esc_html__( 'Sale', 'ayatcollections' ) . '</span>' );

/* ---------- Single product ---------- */

add_filter(
	'woocommerce_output_related_products_args',
	static function ( $args ) {
		$args['posts_per_page'] = 4;
		$args['columns']        = 4;
		return $args;
	}
);

/**
 * Short delivery / payment notes under Add to Bag.
 */
add_action(
	'woocommerce_single_product_summary',
	static function () {
		echo '<ul class="product-assurance" role="list">';
		echo '<li>' . esc_html__( 'Cash on Delivery available', 'ayatcollections' ) . '</li>';
		echo '<li>' . esc_html__( 'Delivery across Pakistan in 3–5 working days', 'ayatcollections' ) . '</li>';
		echo '<li>' . esc_html__( 'Packed in an Ayatcollections gift box', 'ayatcollections' ) . '</li>';
		echo '</ul>';
	},
	35
);

/**
 * Higher-quality single image: use the full gallery size.
 */
add_filter( 'woocommerce_gallery_image_size', static fn() => 'woocommerce_single' );

/* ---------- Cart / checkout ---------- */

/**
 * Rename the "Cart" page heading to "Your Bag" without touching the page.
 */
add_filter(
	'the_title',
	static function ( $title, $id = 0 ) {
		if ( ! is_admin() && $id && function_exists( 'wc_get_page_id' ) && (int) $id === (int) wc_get_page_id( 'cart' ) && in_the_loop() ) {
			return __( 'Your Bag', 'ayatcollections' );
		}
		return $title;
	},
	10,
	2
);
