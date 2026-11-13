<?php
/**
 * Appearance → Ayatcollections setup.
 *
 * One-click staging setup: imports the 8 sample products (with their photos
 * added to the Media Library), the 4 collections, the Collections and New
 * Arrivals pages, the primary menu, and optional WooCommerce settings.
 * Safe to run more than once: existing SKUs, terms and pages are skipped.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the admin page.
 */
function ayat_setup_menu() {
	add_theme_page(
		__( 'Ayatcollections setup', 'ayatcollections' ),
		__( 'Ayatcollections setup', 'ayatcollections' ),
		'manage_options',
		'ayat-setup',
		'ayat_setup_page'
	);
}
add_action( 'admin_menu', 'ayat_setup_menu' );

/**
 * Notice after activation.
 */
function ayat_setup_notice() {
	$screen = get_current_screen();
	if ( ! current_user_can( 'manage_options' ) || ( $screen && 'appearance_page_ayat-setup' === $screen->id ) || get_option( 'ayat_setup_done' ) ) {
		return;
	}
	printf(
		'<div class="notice notice-info"><p>%1$s <a href="%2$s">%3$s</a></p></div>',
		esc_html__( 'Ayatcollections theme is active.', 'ayatcollections' ),
		esc_url( admin_url( 'themes.php?page=ayat-setup' ) ),
		esc_html__( 'Run the one-click store setup →', 'ayatcollections' )
	);
}
add_action( 'admin_notices', 'ayat_setup_notice' );

/**
 * Load sample data.
 *
 * @return array
 */
function ayat_sample_data() {
	$file = AYAT_DIR . '/sample-data/products.json';
	if ( ! file_exists( $file ) ) {
		return array( 'categories' => array(), 'products' => array() );
	}
	$data = json_decode( (string) file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	return is_array( $data ) ? $data : array( 'categories' => array(), 'products' => array() );
}

/**
 * Copy a bundled sample photo into the Media Library (once).
 *
 * @param string $slug    File name without extension.
 * @param string $alt     Alt text.
 * @param int    $post_id Parent post.
 * @return int Attachment ID or 0.
 */
function ayat_import_image( $slug, $alt, $post_id = 0 ) {
	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_ayat_sample_image', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => $slug, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	if ( $existing ) {
		return (int) $existing[0];
	}

	$src = AYAT_DIR . '/assets/images/demo/' . $slug . '.webp';
	if ( ! file_exists( $src ) ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$tmp = wp_tempnam( $slug . '.webp' );
	if ( ! $tmp || ! copy( $src, $tmp ) ) {
		return 0;
	}

	$id = media_handle_sideload(
		array(
			'name'     => $slug . '.webp',
			'tmp_name' => $tmp,
		),
		$post_id,
		$alt
	);

	if ( is_wp_error( $id ) ) {
		wp_delete_file( $tmp );
		return 0;
	}

	update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	update_post_meta( $id, '_ayat_sample_image', $slug );

	// Label clearly as a demo image in the Media Library, with the Unsplash credit.
	$credit = '';
	$cfile  = AYAT_DIR . '/assets/images/demo/credits.json';
	if ( file_exists( $cfile ) ) {
		foreach ( (array) json_decode( (string) file_get_contents( $cfile ), true ) as $c ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			if ( isset( $c['file'] ) && $c['file'] === $slug . '.webp' ) {
				$credit = sprintf( 'Photo by %s on Unsplash (%s)', $c['photographer'], $c['url'] );
			}
		}
	}
	wp_update_post(
		array(
			'ID'           => $id,
			'post_title'   => 'DEMO IMAGE – replace with your own photo – ' . $slug,
			'post_excerpt' => $credit,
		)
	);
	return (int) $id;
}

/**
 * Run the import.
 *
 * @param array $opts Selected options.
 * @return string[] Log lines.
 */
function ayat_run_setup( $opts ) {
	$log = array();

	if ( ! empty( $opts['permalinks'] ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
		$log[] = __( 'Permalinks set to “Post name” (/%postname%/).', 'ayatcollections' );
	}

	if ( ! empty( $opts['noindex'] ) ) {
		update_option( 'blog_public', 0 );
		$log[] = __( 'Search engines discouraged (staging). Re-enable in Settings → Reading before launch.', 'ayatcollections' );
	}

	if ( ! class_exists( 'WooCommerce' ) ) {
		$log[] = __( 'WooCommerce is not active — install and activate it, then run this again to import products.', 'ayatcollections' );
		flush_rewrite_rules();
		return $log;
	}

	if ( ! empty( $opts['store'] ) ) {
		update_option( 'woocommerce_currency', 'PKR' );
		update_option( 'woocommerce_price_num_decimals', 0 );
		update_option( 'woocommerce_currency_pos', 'left_space' );
		if ( ! get_option( 'woocommerce_default_country' ) || 'US:CA' === get_option( 'woocommerce_default_country' ) ) {
			update_option( 'woocommerce_default_country', 'PK' );
		}
		update_option( 'woocommerce_enable_guest_checkout', 'yes' );
		update_option( 'woocommerce_manage_stock', 'yes' );

		$cod                 = (array) get_option( 'woocommerce_cod_settings', array() );
		$cod['enabled']      = 'yes';
		$cod['title']        = __( 'Cash on Delivery', 'ayatcollections' );
		$cod['description']  = __( 'Pay in cash when your order is delivered.', 'ayatcollections' );
		$cod['instructions'] = __( 'Please keep the exact amount ready at delivery.', 'ayatcollections' );
		update_option( 'woocommerce_cod_settings', $cod );

		$log[] = __( 'Store currency PKR (no decimals), guest checkout on, stock management on, Cash on Delivery enabled.', 'ayatcollections' );
	}

	$data = ayat_sample_data();

	if ( ! empty( $opts['products'] ) ) {
		$cat_ids = array();
		foreach ( $data['categories'] as $order => $c ) {
			$term = term_exists( $c['slug'], 'product_cat' );
			if ( ! $term ) {
				$term = wp_insert_term( $c['name'], 'product_cat', array( 'slug' => $c['slug'], 'description' => $c['description'] ) );
				if ( is_wp_error( $term ) ) {
					continue;
				}
				/* translators: %s: category */
				$log[] = sprintf( __( 'Collection created: %s', 'ayatcollections' ), $c['name'] );
			}
			$term_id              = (int) $term['term_id'];
			$cat_ids[ $c['slug'] ] = $term_id;
			update_term_meta( $term_id, 'order', $order );
			// The category thumbnail is set from its first product's photo during the product steps.
		}

		update_option( 'ayat_import_cats', $cat_ids, false );
		$log[] = __( 'Products are imported one per step on the next screens…', 'ayatcollections' );
	}

	if ( ! empty( $opts['pages'] ) ) {
		$log = array_merge( $log, ayat_setup_pages() );
	}

	flush_rewrite_rules();
	update_option( 'ayat_setup_done', time() );
	return $log;
}

/**
 * Import a single sample product (one per request keeps each step short,
 * so shared hosting time limits are never reached).
 *
 * @param int $index Product index in products.json.
 * @return string Log line.
 */
function ayat_import_product( $index ) {
	$data = ayat_sample_data();
	if ( ! isset( $data['products'][ $index ] ) || ! class_exists( 'WooCommerce' ) ) {
		return '';
	}
	$p       = $data['products'][ $index ];
	$cat_ids = (array) get_option( 'ayat_import_cats', array() );

	if ( wc_get_product_id_by_sku( $p['sku'] ) ) {
		/* translators: %s: SKU */
		return sprintf( __( 'Skipped (SKU exists): %s', 'ayatcollections' ), $p['sku'] );
	}

	{
			$product = 'variable' === $p['type'] ? new WC_Product_Variable() : new WC_Product_Simple();
			$product->set_name( $p['name'] );
			$product->set_sku( $p['sku'] );
			$product->set_status( 'publish' );
			$product->set_catalog_visibility( 'visible' );
			$product->set_short_description( $p['short'] );
			$product->set_description( wpautop( $p['description'] ) );
			$product->set_featured( ! empty( $p['featured'] ) );
			if ( isset( $cat_ids[ $p['category'] ] ) ) {
				$product->set_category_ids( array( $cat_ids[ $p['category'] ] ) );
			}

			if ( 'variable' === $p['type'] ) {
				$attr = new WC_Product_Attribute();
				$attr->set_name( $p['attribute'] );
				$attr->set_options( $p['options'] );
				$attr->set_visible( true );
				$attr->set_variation( true );
				$product->set_attributes( array( $attr ) );
			} else {
				$product->set_regular_price( $p['regular_price'] );
				if ( ! empty( $p['sale_price'] ) ) {
					$product->set_sale_price( $p['sale_price'] );
				}
				$product->set_manage_stock( true );
				$product->set_stock_quantity( (int) $p['stock'] );
			}

			$product_id = $product->save();

			$img = ayat_import_image( $p['image'], $p['alt'], $product_id );
			if ( $img ) {
				$product->set_image_id( $img );
				$product->save();
				$cat = isset( $cat_ids[ $p['category'] ] ) ? (int) $cat_ids[ $p['category'] ] : 0;
				if ( $cat && ! get_term_meta( $cat, 'thumbnail_id', true ) ) {
					$cat_img = 0;
					foreach ( $data['categories'] as $c ) {
						if ( $c['slug'] === $p['category'] && ! empty( $c['image'] ) ) {
							/* translators: %s: collection name */
							$cat_img = ayat_import_image( $c['image'], sprintf( __( '%s collection (demo photo)', 'ayatcollections' ), $c['name'] ) );
						}
					}
					update_term_meta( $cat, 'thumbnail_id', $cat_img ? $cat_img : $img );
				}
			}

			if ( 'variable' === $p['type'] ) {
				$key = sanitize_title( $p['attribute'] );
				foreach ( $p['options'] as $n => $opt ) {
					$v = new WC_Product_Variation();
					$v->set_parent_id( $product_id );
					$v->set_attributes( array( $key => $opt ) );
					$v->set_sku( $p['sku'] . '-' . sanitize_title( $opt ) );
					$v->set_regular_price( $p['regular_price'] );
					$v->set_manage_stock( true );
					$v->set_stock_quantity( isset( $p['variation_stock'][ $n ] ) ? (int) $p['variation_stock'][ $n ] : 5 );
					$v->set_status( 'publish' );
					$v->save();
				}
				WC_Product_Variable::sync( $product_id );
				wc_delete_product_transients( $product_id );
			}

			/* translators: 1: product, 2: SKU */
			return sprintf( __( 'Product created: %1$s (%2$s)', 'ayatcollections' ), $p['name'], $p['sku'] );
	}
}

/**
 * Create the Collections and New Arrivals pages and the primary menu.
 *
 * @return string[] Log lines.
 */
function ayat_setup_pages() {
	$log = array();
	{
		$pages = array(
			'collections'  => array( __( 'Collections', 'ayatcollections' ), 'page-templates/collections.php', __( 'Browse every Ayatcollections collection.', 'ayatcollections' ) ),
			'new-arrivals' => array( __( 'New Arrivals', 'ayatcollections' ), 'page-templates/new-arrivals.php', __( 'The newest pieces, added every week.', 'ayatcollections' ) ),
		);
		$ids   = array();
		foreach ( $pages as $slug => $pg ) {
			$page = get_page_by_path( $slug );
			if ( $page ) {
				$ids[ $slug ] = $page->ID;
				continue;
			}
			$ids[ $slug ] = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => $pg[0],
					'post_name'    => $slug,
					'post_content' => $pg[2],
				)
			);
			update_post_meta( $ids[ $slug ], '_wp_page_template', $pg[1] );
			/* translators: %s: page title */
			$log[] = sprintf( __( 'Page created: %s', 'ayatcollections' ), $pg[0] );
		}

		$locations = get_theme_mod( 'nav_menu_locations', array() );
		if ( empty( $locations['primary'] ) ) {
			$menu_id = wp_create_nav_menu( __( 'Primary', 'ayatcollections' ) );
			if ( ! is_wp_error( $menu_id ) ) {
				$items = array(
					array( __( 'Shop', 'ayatcollections' ), wc_get_page_id( 'shop' ) ),
					array( __( 'Collections', 'ayatcollections' ), $ids['collections'] ),
					array( __( 'New Arrivals', 'ayatcollections' ), $ids['new-arrivals'] ),
				);
				foreach ( $items as $item ) {
					if ( $item[1] > 0 ) {
						wp_update_nav_menu_item(
							$menu_id,
							0,
							array(
								'menu-item-title'     => $item[0],
								'menu-item-object'    => 'page',
								'menu-item-object-id' => $item[1],
								'menu-item-type'      => 'post_type',
								'menu-item-status'    => 'publish',
							)
						);
					}
				}
				foreach ( array() as $title => $url ) {
					wp_update_nav_menu_item(
						$menu_id,
						0,
						array(
							'menu-item-title'  => $title,
							'menu-item-url'    => $url,
							'menu-item-type'   => 'custom',
							'menu-item-status' => 'publish',
						)
					);
				}
				$locations['primary'] = $menu_id;
				set_theme_mod( 'nav_menu_locations', $locations );
				$log[] = __( 'Primary menu created: Shop, New Arrivals, Collections, About Us, Contact.', 'ayatcollections' );
			}
		}
	}
	return $log;
}

/**
 * Render the admin page.
 */
function ayat_setup_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$log  = array();
	$keys = array( 'products', 'pages', 'store', 'permalinks', 'noindex' );
	if ( isset( $_POST['ayat_setup_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ayat_setup_nonce'] ) ), 'ayat_setup' ) ) {
		$opts = array();
		foreach ( $keys as $k ) {
			$opts[ $k ] = ! empty( $_POST[ 'ayat_' . $k ] );
		}
		$log = ayat_run_setup( $opts );
		update_option( 'ayat_import_log', $log, false );
		$next = ( $opts['products'] && class_exists( 'WooCommerce' ) ) ? 0 : null;
	} elseif ( isset( $_GET['ayat_step'], $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'ayat_step' ) ) {
		$step   = absint( $_GET['ayat_step'] );
		$log    = (array) get_option( 'ayat_import_log', array() );
		$line   = ayat_import_product( $step );
		if ( $line ) {
			$log[] = $line;
			update_option( 'ayat_import_log', $log, false );
		}
		$next = $step + 1;
	}

	$total = count( ayat_sample_data()['products'] );
	if ( isset( $next ) && null !== $next && $next >= $total ) {
		$next  = null;
		$log[] = __( 'All sample products imported.', 'ayatcollections' );
		delete_option( 'ayat_import_log' );
		flush_rewrite_rules();
	}
	$next_url = ( isset( $next ) && null !== $next ) ? wp_nonce_url( admin_url( 'themes.php?page=ayat-setup&ayat_step=' . $next ), 'ayat_step' ) : '';

	$labels = array(
		'products'   => __( 'Import 8 sample products, 4 collections and their photos (skips existing SKUs)', 'ayatcollections' ),
		'pages'      => __( 'Create the Collections and New Arrivals pages and the primary menu', 'ayatcollections' ),
		'store'      => __( 'Set currency to PKR, enable guest checkout, stock management and Cash on Delivery', 'ayatcollections' ),
		'permalinks' => __( 'Use clean permalinks (/%postname%/)', 'ayatcollections' ),
		'noindex'    => __( 'Staging: discourage search engines from indexing this site', 'ayatcollections' ),
	);
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Ayatcollections setup', 'ayatcollections' ); ?></h1>
		<p><?php esc_html_e( 'Prepares a staging store in one step. Everything created here is ordinary WooCommerce data you can edit or delete in Products, Pages and Media.', 'ayatcollections' ); ?></p>
		<?php if ( ! class_exists( 'WooCommerce' ) ) : ?>
			<div class="notice notice-warning inline"><p><?php esc_html_e( 'WooCommerce is not active. Install it from Plugins → Add New first.', 'ayatcollections' ); ?></p></div>
		<?php endif; ?>

		<?php if ( $log ) : ?>
			<div class="notice notice-success inline"><ul style="list-style:disc;padding-left:20px">
				<?php foreach ( $log as $line ) : ?>
					<li><?php echo esc_html( $line ); ?></li>
				<?php endforeach; ?>
			</ul></div>
		<?php endif; ?>

		<?php if ( $next_url ) : ?>
			<?php /* translators: 1: current product number, 2: total */ ?>
			<p><strong><?php printf( esc_html__( 'Importing product %1$d of %2$d…', 'ayatcollections' ), (int) $next + 1, (int) $total ); ?></strong>
			<a class="button" id="ayat-next" href="<?php echo esc_url( $next_url ); ?>"><?php esc_html_e( 'Continue', 'ayatcollections' ); ?></a></p>
			<script>setTimeout(function(){ window.location.href = document.getElementById('ayat-next').href; }, 300);</script>
		<?php endif; ?>

		<form method="post">
			<?php wp_nonce_field( 'ayat_setup', 'ayat_setup_nonce' ); ?>
			<fieldset>
				<?php foreach ( $labels as $k => $label ) : ?>
					<p><label><input type="checkbox" name="ayat_<?php echo esc_attr( $k ); ?>" value="1" checked> <?php echo esc_html( $label ); ?></label></p>
				<?php endforeach; ?>
			</fieldset>
			<?php submit_button( __( 'Run setup', 'ayatcollections' ) ); ?>
		</form>

		<h2><?php esc_html_e( 'Next steps', 'ayatcollections' ); ?></h2>
		<ol>
			<li><?php esc_html_e( 'Appearance → Customize → Site Identity: upload your logo.', 'ayatcollections' ); ?></li>
			<li><?php esc_html_e( 'Appearance → Customize → Homepage slider: change photos, focal points, headlines, buttons and links.', 'ayatcollections' ); ?></li>
			<li><?php esc_html_e( 'WooCommerce → Settings → Shipping: add a “Pakistan” zone with your flat rate / free shipping.', 'ayatcollections' ); ?></li>
			<li><?php esc_html_e( 'WooCommerce → Settings → Payments: configure Cash on Delivery and any card / wallet gateway.', 'ayatcollections' ); ?></li>
			<li><?php esc_html_e( 'Before launch: Settings → Reading → untick “Discourage search engines”.', 'ayatcollections' ); ?></li>
		</ol>
	</div>
	<?php
}


/**
 * Sample customer reviews for the demo only. Each one is titled and marked as a
 * sample (shown with a "Sample review (demo)" label) so it is never mistaken for
 * a real customer. Delete them in Customer Reviews before launch.
 *
 * @return int Number created.
 */
function ayat_import_sample_reviews() {
	if ( ! post_type_exists( 'ayat_review' ) || get_posts( array( 'post_type' => 'ayat_review', 'meta_key' => '_ayat_sample', 'numberposts' => 1, 'post_status' => 'any' ) ) ) { // phpcs:ignore WordPress.DB.SlowDBQuery
		return 0;
	}
	$items = array(
		array( 'Sample customer, Lahore', 'Instagram', 5, 'AYC-CUF-001', 'Sample text: the cuff looks exactly like the photos and the gold tone has not faded after weeks of daily wear.' ),
		array( 'Sample customer, Karachi', 'WhatsApp', 5, 'AYC-ANJ-001', 'Sample text: ordered on WhatsApp, got photos before confirming and the hand chain arrived gift-boxed.' ),
		array( 'Sample customer, Islamabad', 'TikTok', 4, 'AYC-ADJ-001', 'Sample text: lovely adjustable cuff, fits well and is comfortable to wear all day.' ),
		array( 'Sample customer, Faisalabad', 'Facebook', 5, 'AYC-PER-001', 'Sample text: wore the chain bracelet in the shower every day and it still shines.' ),
		array( 'Sample customer, Multan', 'Google', 5, 'AYC-ANK-001', 'Sample text: cash on delivery was easy and the anklet is light and pretty.' ),
		array( 'Sample customer, Rawalpindi', 'Instagram', 4, 'AYC-CHM-001', 'Sample text: the charm bracelet is a statement piece and the packing was beautiful.' ),
	);
	$n = 0;
	foreach ( $items as $it ) {
		$id = wp_insert_post(
			array(
				'post_type'    => 'ayat_review',
				'post_status'  => 'publish',
				'post_title'   => $it[0],
				'post_content' => $it[4],
			)
		);
		if ( ! $id || is_wp_error( $id ) ) {
			continue;
		}
		$pid = function_exists( 'wc_get_product_id_by_sku' ) ? (int) wc_get_product_id_by_sku( $it[3] ) : 0;
		update_post_meta( $id, '_ayat_source', $it[1] );
		update_post_meta( $id, '_ayat_rating', $it[2] );
		update_post_meta( $id, '_ayat_product', $pid );
		update_post_meta( $id, '_ayat_sample', 1 );
		$n++;
	}
	return $n;
}
