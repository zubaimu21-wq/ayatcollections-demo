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
 * "Order on WhatsApp" under Add to Bag, with the product prefilled.
 */
add_action(
	'woocommerce_single_product_summary',
	static function () {
		global $product;
		$wa = function_exists( 'ayat_store_whatsapp' ) ? ayat_store_whatsapp() : '';
		if ( ! $wa || ! $product ) {
			return;
		}
		/* translators: 1: product, 2: price, 3: link */
		$msg = sprintf( __( 'Assalam o Alaikum! I would like to order: %1$s (%2$s) %3$s', 'ayatcollections' ), $product->get_name(), wp_strip_all_tags( wc_price( wc_get_price_to_display( $product ) ) ), get_permalink( $product->get_id() ) );
		echo '<a class="pdp-wa" href="' . esc_url( 'https://wa.me/' . $wa . '?text=' . rawurlencode( $msg ) ) . '" target="_blank" rel="noopener">' . ( function_exists( 'ayat_brand_logo' ) ? ayat_brand_logo( 'WhatsApp', 22 ) : '' ) . '<span>' . esc_html__( 'Order on WhatsApp', 'ayatcollections' ) . '</span></a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	},
	31
);

/**
 * Trust cards under the buttons.
 */
add_action(
	'woocommerce_single_product_summary',
	static function () {
		$items = array(
			array( 'M3 7.5h18v9H3zM7 12h.01M17 12h.01M12 14.2a2.2 2.2 0 1 0 0-4.4 2.2 2.2 0 0 0 0 4.4Z', __( 'Cash on Delivery', 'ayatcollections' ), __( 'or pay by Easypaisa', 'ayatcollections' ) ),
			array( 'M3 6.5h10.5v9H3zM13.5 9.5h4l3.5 3.5v2.5h-7.5M7 18.8a1.8 1.8 0 1 0 0-3.6 1.8 1.8 0 0 0 0 3.6ZM17 18.8a1.8 1.8 0 1 0 0-3.6 1.8 1.8 0 0 0 0 3.6Z', __( 'Delivery in 3–5 days', 'ayatcollections' ), __( 'All over Pakistan', 'ayatcollections' ) ),
			array( 'M4 10h16v10H4zM3 7h18v3H3zM12 7v13M12 7c-1.5-3-5-3-5-1s3 1 5 1c2 0 5 1 5-1s-3.5-2-5 1', __( 'SHAYA gift box', 'ayatcollections' ), __( 'Ready to gift', 'ayatcollections' ) ),
			array( 'M4 12a8 8 0 0 1 13.7-5.6L20 8.7M20 4v4.7h-4.7M20 12a8 8 0 0 1-13.7 5.6L4 15.3M4 20v-4.7h4.7', __( '7-day exchange', 'ayatcollections' ), __( 'Easy and simple', 'ayatcollections' ) ),
		);
		echo '<ul class="pdp-trust" role="list">';
		foreach ( $items as $it ) {
			echo '<li><span class="pdp-trust__ic" aria-hidden="true"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="' . esc_attr( $it[0] ) . '"/></svg></span><span><strong>' . esc_html( $it[1] ) . '</strong><small>' . esc_html( $it[2] ) . '</small></span></li>';
		}
		echo '</ul>';
	},
	35
);

/**
 * Related products: heading, and always fill up to four.
 */
add_filter( 'woocommerce_product_related_products_heading', static fn() => __( 'You may also like', 'ayatcollections' ) );
add_filter(
	'woocommerce_related_products',
	static function ( $ids, $product_id, $args ) {
		$want = 4;
		if ( count( $ids ) >= $want ) {
			return $ids;
		}
		$more = wc_get_products(
			array(
				'status'  => 'publish',
				'limit'   => $want * 2,
				'orderby' => 'rand',
				'exclude' => array_merge( array( $product_id ), $ids ),
				'return'  => 'ids',
			)
		);
		return array_slice( array_merge( $ids, $more ), 0, $want );
	},
	10,
	3
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

/* ---------- Checkout fields for Pakistan ---------- */

/**
 * Postcode optional in Pakistan (most customers don't know it).
 *
 * @param array $locale Locales.
 * @return array
 */
function ayat_pk_locale( $locale ) {
	$locale['PK']['postcode']['required'] = false;
	$locale['PK']['postcode']['hidden']   = false;
	return $locale;
}
add_filter( 'woocommerce_get_country_locale', 'ayat_pk_locale' );

/**
 * Phone is required: Cash on Delivery and Easypaisa orders are confirmed by phone/WhatsApp.
 * Works for the block checkout (option) and the classic checkout (field).
 */
add_filter( 'pre_option_woocommerce_checkout_phone_field', static fn() => 'required' );
add_filter(
	'woocommerce_billing_fields',
	static function ( $fields ) {
		if ( isset( $fields['billing_phone'] ) ) {
			$fields['billing_phone']['required'] = true;
		}
		if ( isset( $fields['billing_postcode'] ) ) {
			$fields['billing_postcode']['required'] = false;
		}
		return $fields;
	}
);
