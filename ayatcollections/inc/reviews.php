<?php
/**
 * Customer reviews collected from social media (Instagram, Facebook, TikTok,
 * WhatsApp, Google or the website). Added by hand in WP Admin → Customer Reviews,
 * shown on the homepage and on the linked product's page.
 *
 * Auto-sync: paste the shortcode of a reviews plugin (for example a Google /
 * Facebook reviews widget) in Appearance → Customize → Homepage → "Reviews widget
 * shortcode" and it is shown under the reviews.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

/**
 * Review sources shown in the admin dropdown.
 *
 * @return array
 */
function ayat_review_sources() {
	return array( 'Instagram', 'Facebook', 'TikTok', 'WhatsApp', 'Google', 'Website' );
}

/**
 * Register the Customer Reviews post type.
 */
function ayat_register_reviews() {
	register_post_type(
		'ayat_review',
		array(
			'labels'          => array(
				'name'          => __( 'Customer Reviews', 'ayatcollections' ),
				'singular_name' => __( 'Customer Review', 'ayatcollections' ),
				'add_new_item'  => __( 'Add customer review', 'ayatcollections' ),
				'edit_item'     => __( 'Edit customer review', 'ayatcollections' ),
				'menu_name'     => __( 'Customer Reviews', 'ayatcollections' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_position'   => 57,
			'menu_icon'       => 'dashicons-star-filled',
			'supports'        => array( 'title', 'editor', 'thumbnail' ),
			'capability_type' => 'post',
		)
	);
}
add_action( 'init', 'ayat_register_reviews' );

/**
 * Title placeholder.
 *
 * @param string  $text Placeholder.
 * @param WP_Post $post Post.
 * @return string
 */
function ayat_review_title_placeholder( $text, $post ) {
	return 'ayat_review' === $post->post_type ? __( 'Customer name (for example: Ayesha, Lahore)', 'ayatcollections' ) : $text;
}
add_filter( 'enter_title_here', 'ayat_review_title_placeholder', 10, 2 );

/**
 * Meta box: rating, source, product, link.
 */
function ayat_review_meta_box() {
	add_meta_box( 'ayat_review_details', __( 'Review details', 'ayatcollections' ), 'ayat_review_meta_box_html', 'ayat_review', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'ayat_review_meta_box' );

/**
 * Meta box markup.
 *
 * @param WP_Post $post Post.
 */
function ayat_review_meta_box_html( $post ) {
	wp_nonce_field( 'ayat_review_save', 'ayat_review_nonce' );
	$rating  = (int) get_post_meta( $post->ID, '_ayat_rating', true );
	$rating  = $rating ? $rating : 5;
	$source  = get_post_meta( $post->ID, '_ayat_source', true );
	$product = (int) get_post_meta( $post->ID, '_ayat_product', true );
	$link    = get_post_meta( $post->ID, '_ayat_link', true );
	?>
	<p><label for="ayat_rating"><strong><?php esc_html_e( 'Stars', 'ayatcollections' ); ?></strong></label><br>
		<select name="ayat_rating" id="ayat_rating">
			<?php for ( $i = 5; $i >= 1; $i-- ) : ?>
				<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $rating, $i ); ?>><?php echo esc_html( str_repeat( '★', $i ) ); ?></option>
			<?php endfor; ?>
		</select></p>
	<p><label for="ayat_source"><strong><?php esc_html_e( 'Where was it posted?', 'ayatcollections' ); ?></strong></label><br>
		<select name="ayat_source" id="ayat_source">
			<?php foreach ( ayat_review_sources() as $s ) : ?>
				<option value="<?php echo esc_attr( $s ); ?>" <?php selected( $source, $s ); ?>><?php echo esc_html( $s ); ?></option>
			<?php endforeach; ?>
		</select></p>
	<p><label for="ayat_product"><strong><?php esc_html_e( 'Product (optional)', 'ayatcollections' ); ?></strong></label><br>
		<select name="ayat_product" id="ayat_product" style="max-width:100%">
			<option value="0"><?php esc_html_e( '— Homepage only —', 'ayatcollections' ); ?></option>
			<?php foreach ( get_posts( array( 'post_type' => 'product', 'numberposts' => 300, 'orderby' => 'title', 'order' => 'ASC' ) ) as $p ) : ?>
				<option value="<?php echo esc_attr( $p->ID ); ?>" <?php selected( $product, $p->ID ); ?>><?php echo esc_html( $p->post_title ); ?></option>
			<?php endforeach; ?>
		</select></p>
	<p><label for="ayat_link"><strong><?php esc_html_e( 'Link to the original post (optional)', 'ayatcollections' ); ?></strong></label><br>
		<input type="url" name="ayat_link" id="ayat_link" value="<?php echo esc_attr( $link ); ?>" style="width:100%" placeholder="https://"></p>
	<p class="description"><?php esc_html_e( 'Write the review text in the main box. A screenshot can be added as the Featured image.', 'ayatcollections' ); ?></p>
	<?php
}

/**
 * Save review details.
 *
 * @param int $post_id Post ID.
 */
function ayat_review_save( $post_id ) {
	if ( ! isset( $_POST['ayat_review_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['ayat_review_nonce'] ), 'ayat_review_save' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$rating = isset( $_POST['ayat_rating'] ) ? max( 1, min( 5, (int) $_POST['ayat_rating'] ) ) : 5;
	$source = isset( $_POST['ayat_source'] ) ? sanitize_text_field( wp_unslash( $_POST['ayat_source'] ) ) : 'Website';
	update_post_meta( $post_id, '_ayat_rating', $rating );
	update_post_meta( $post_id, '_ayat_source', in_array( $source, ayat_review_sources(), true ) ? $source : 'Website' );
	update_post_meta( $post_id, '_ayat_product', isset( $_POST['ayat_product'] ) ? absint( $_POST['ayat_product'] ) : 0 );
	update_post_meta( $post_id, '_ayat_link', isset( $_POST['ayat_link'] ) ? esc_url_raw( wp_unslash( $_POST['ayat_link'] ) ) : '' );
}
add_action( 'save_post_ayat_review', 'ayat_review_save' );

/**
 * Admin list columns.
 *
 * @param array $cols Columns.
 * @return array
 */
function ayat_review_columns( $cols ) {
	$date = $cols['date'];
	unset( $cols['date'] );
	$cols['ayat_rating'] = __( 'Stars', 'ayatcollections' );
	$cols['ayat_source'] = __( 'Source', 'ayatcollections' );
	$cols['ayat_prod']   = __( 'Product', 'ayatcollections' );
	$cols['date']        = $date;
	return $cols;
}
add_filter( 'manage_ayat_review_posts_columns', 'ayat_review_columns' );

/**
 * Admin list column values.
 *
 * @param string $col     Column.
 * @param int    $post_id Post ID.
 */
function ayat_review_column_value( $col, $post_id ) {
	if ( 'ayat_rating' === $col ) {
		echo esc_html( str_repeat( '★', (int) get_post_meta( $post_id, '_ayat_rating', true ) ) );
	} elseif ( 'ayat_source' === $col ) {
		echo esc_html( get_post_meta( $post_id, '_ayat_source', true ) );
		if ( get_post_meta( $post_id, '_ayat_sample', true ) ) {
			echo ' <em>(' . esc_html__( 'sample – delete before launch', 'ayatcollections' ) . ')</em>';
		}
	} elseif ( 'ayat_prod' === $col ) {
		$pid = (int) get_post_meta( $post_id, '_ayat_product', true );
		echo $pid ? esc_html( get_the_title( $pid ) ) : '—';
	}
}
add_action( 'manage_ayat_review_posts_custom_column', 'ayat_review_column_value', 10, 2 );

/**
 * Get published reviews.
 *
 * @param int $product_id Limit to one product (0 = all).
 * @param int $limit      Number of reviews.
 * @return WP_Post[]
 */
function ayat_get_reviews( $product_id = 0, $limit = 6 ) {
	$args = array(
		'post_type'      => 'ayat_review',
		'post_status'    => 'publish',
		'posts_per_page' => $limit,
		'no_found_rows'  => true,
	);
	if ( $product_id ) {
		$args['meta_query'] = array( array( 'key' => '_ayat_product', 'value' => (int) $product_id ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
	}
	return get_posts( $args );
}

/**
 * Review cards.
 *
 * @param WP_Post[] $reviews Reviews.
 * @param bool      $show_product Show the product link on each card.
 */
function ayat_render_reviews( $reviews, $show_product = true ) {
	echo '<ul class="reviews-grid" role="list">';
	foreach ( $reviews as $r ) {
		$rating = (int) get_post_meta( $r->ID, '_ayat_rating', true );
		$rating = $rating ? $rating : 5;
		$source = get_post_meta( $r->ID, '_ayat_source', true );
		$link   = get_post_meta( $r->ID, '_ayat_link', true );
		$pid    = (int) get_post_meta( $r->ID, '_ayat_product', true );
		$sample = get_post_meta( $r->ID, '_ayat_sample', true );
		echo '<li class="review-card">';
		/* translators: %d: star rating */
		echo '<p class="review-card__stars" aria-label="' . esc_attr( sprintf( __( 'Rated %d out of 5', 'ayatcollections' ), $rating ) ) . '">' . esc_html( str_repeat( '★', $rating ) . str_repeat( '☆', 5 - $rating ) ) . '</p>';
		echo '<blockquote class="review-card__text">' . wp_kses_post( wpautop( $r->post_content ) ) . '</blockquote>';
		if ( has_post_thumbnail( $r ) ) {
			echo '<div class="review-card__shot">' . get_the_post_thumbnail( $r, 'medium', array( 'loading' => 'lazy' ) ) . '</div>';
		}
		echo '<div class="review-card__foot">';
		echo '<span class="review-card__name">' . esc_html( get_the_title( $r ) ) . '</span>';
		if ( $source ) {
			$icon  = function_exists( 'ayat_social_icon' ) && in_array( $source, array( 'Instagram', 'Facebook', 'TikTok', 'WhatsApp', 'YouTube' ), true ) ? ayat_social_icon( $source ) : '';
			$label = $icon . '<span>' . esc_html( $source ) . '</span>';
			echo $link ? '<a class="review-card__source" href="' . esc_url( $link ) . '" rel="noopener nofollow">' . $label . '</a>' : '<span class="review-card__source">' . $label . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</div>';
		if ( $show_product && $pid && get_post_status( $pid ) === 'publish' ) {
			echo '<a class="review-card__product" href="' . esc_url( get_permalink( $pid ) ) . '">' . esc_html( get_the_title( $pid ) ) . ' &rarr;</a>';
		}
		if ( $sample ) {
			echo '<span class="review-card__sample">' . esc_html__( 'Sample review (demo)', 'ayatcollections' ) . '</span>';
		}
		echo '</li>';
	}
	echo '</ul>';
}

/**
 * Product page: social reviews linked to this product.
 */
function ayat_product_social_reviews() {
	global $product;
	if ( ! $product ) {
		return;
	}
	$reviews = ayat_get_reviews( $product->get_id(), 6 );
	if ( ! $reviews ) {
		return;
	}
	echo '<section class="product-social-reviews" aria-labelledby="psr-title"><h2 id="psr-title">' . esc_html__( 'What customers say', 'ayatcollections' ) . '</h2>';
	ayat_render_reviews( $reviews, false );
	echo '</section>';
}
add_action( 'woocommerce_after_single_product_summary', 'ayat_product_social_reviews', 12 );
