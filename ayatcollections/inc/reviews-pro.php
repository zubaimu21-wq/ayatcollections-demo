<?php
/**
 * Real customer reviews on product pages.
 *
 * - Customers write a review (stars, title, text, up to 3 photos) on the product page.
 * - Every review arrives as "Pending" in WP Admin → Customer Reviews; nothing is shown
 *   until the store owner publishes it.
 * - "Verified buyer" is added automatically when the phone or email matches a
 *   processing/completed order that contains the product.
 * - Product pages show a rating summary (average + 5→1 star bars), sorting and
 *   photo reviews; shop cards show stars and the review count.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

/**
 * Published reviews for a product.
 *
 * @param int $product_id Product ID.
 * @return WP_Post[]
 */
function ayat_rv_for_product( $product_id ) {
	return get_posts(
		array(
			'post_type'      => 'ayat_review',
			'post_status'    => 'publish',
			'posts_per_page' => 200,
			'no_found_rows'  => true,
			'meta_query'     => array( array( 'key' => '_ayat_product', 'value' => (int) $product_id ) ), // phpcs:ignore WordPress.DB.SlowDBQuery
		)
	);
}

/**
 * Rating summary for a product (cached).
 *
 * @param int $product_id Product ID.
 * @return array{count:int,avg:float,bars:array}
 */
function ayat_rv_stats( $product_id ) {
	$key   = 'ayat_rv_stats_' . (int) $product_id;
	$stats = get_transient( $key );
	if ( is_array( $stats ) ) {
		return $stats;
	}
	$bars = array( 5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0 );
	$sum  = 0;
	foreach ( ayat_rv_for_product( $product_id ) as $r ) {
		$n = max( 1, min( 5, (int) get_post_meta( $r->ID, '_ayat_rating', true ) ) );
		++$bars[ $n ];
		$sum += $n;
	}
	$count = array_sum( $bars );
	$stats = array(
		'count' => $count,
		'avg'   => $count ? round( $sum / $count, 1 ) : 0,
		'bars'  => $bars,
	);
	set_transient( $key, $stats, DAY_IN_SECONDS );
	return $stats;
}

/**
 * Clear cached stats when a review changes.
 *
 * @param int $post_id Review ID.
 */
function ayat_rv_flush( $post_id ) {
	if ( 'ayat_review' !== get_post_type( $post_id ) ) {
		return;
	}
	$pid = (int) get_post_meta( $post_id, '_ayat_product', true );
	if ( $pid ) {
		delete_transient( 'ayat_rv_stats_' . $pid );
	}
}
add_action( 'save_post', 'ayat_rv_flush', 50 );
add_action( 'transition_post_status', function ( $new, $old, $post ) { ayat_rv_flush( $post->ID ); }, 10, 3 );
add_action( 'before_delete_post', 'ayat_rv_flush' );

/**
 * Gold stars markup (supports halves for averages).
 *
 * @param float $rating Rating 0–5.
 * @return string
 */
function ayat_rv_stars( $rating ) {
	$pct = max( 0, min( 100, $rating / 5 * 100 ) );
	/* translators: %s: rating */
	$label = sprintf( __( 'Rated %s out of 5', 'ayatcollections' ), number_format_i18n( $rating, 1 ) );
	return '<span class="rv-stars" role="img" aria-label="' . esc_attr( $label ) . '"><span class="rv-stars__fill" style="width:' . esc_attr( round( $pct, 1 ) ) . '%"></span></span>';
}

/**
 * Was this review written by someone who ordered the product?
 *
 * @param string $contact    Phone or email from the form.
 * @param int    $product_id Product ID.
 * @return bool
 */
function ayat_rv_is_verified( $contact, $product_id ) {
	if ( ! function_exists( 'wc_get_orders' ) || ! $contact ) {
		return false;
	}
	$email = is_email( $contact ) ? strtolower( $contact ) : '';
	$phone = $email ? '' : substr( preg_replace( '/\D/', '', $contact ), -10 );
	if ( ! $email && strlen( $phone ) < 10 ) {
		return false;
	}
	$orders = wc_get_orders(
		array(
			'limit'        => 300,
			'status'       => array( 'processing', 'completed' ),
			'date_created' => '>' . ( time() - YEAR_IN_SECONDS ),
		)
	);
	foreach ( $orders as $order ) {
		$match = $email ? strtolower( (string) $order->get_billing_email() ) === $email : substr( preg_replace( '/\D/', '', (string) $order->get_billing_phone() ), -10 ) === $phone;
		if ( ! $match ) {
			continue;
		}
		foreach ( $order->get_items() as $item ) {
			if ( (int) $item->get_product_id() === (int) $product_id ) {
				return true;
			}
		}
	}
	return false;
}

/**
 * Handle the review form (admin-post.php?action=ayat_submit_review).
 */
function ayat_rv_submit() {
	$pid  = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$back = $pid ? get_permalink( $pid ) : home_url( '/' );
	$fail = function ( $code ) use ( $back ) {
		wp_safe_redirect( add_query_arg( 'review', $code, $back ) . '#customer-reviews' );
		exit;
	};

	if ( ! isset( $_POST['ayat_rv_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ayat_rv_nonce'] ) ), 'ayat_rv_submit' ) ) {
		$fail( 'expired' );
	}
	if ( ! empty( $_POST['ayat_rv_site'] ) ) { // Honeypot: real people never fill this.
		$fail( 'thanks' );
	}
	if ( ! $pid || 'product' !== get_post_type( $pid ) ) {
		$fail( 'error' );
	}

	// Max 3 reviews per hour from one connection.
	$ip   = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$rate = 'ayat_rv_rate_' . md5( $ip );
	$hits = (int) get_transient( $rate );
	if ( $hits >= 3 ) {
		$fail( 'slow' );
	}

	$rating  = isset( $_POST['rating'] ) ? (int) $_POST['rating'] : 0;
	$name    = isset( $_POST['rv_name'] ) ? sanitize_text_field( wp_unslash( $_POST['rv_name'] ) ) : '';
	$city    = isset( $_POST['rv_city'] ) ? sanitize_text_field( wp_unslash( $_POST['rv_city'] ) ) : '';
	$contact = isset( $_POST['rv_contact'] ) ? sanitize_text_field( wp_unslash( $_POST['rv_contact'] ) ) : '';
	$title   = isset( $_POST['rv_title'] ) ? sanitize_text_field( wp_unslash( $_POST['rv_title'] ) ) : '';
	$body    = isset( $_POST['rv_body'] ) ? sanitize_textarea_field( wp_unslash( $_POST['rv_body'] ) ) : '';

	if ( $rating < 1 || $rating > 5 || '' === $name || mb_strlen( $body ) < 5 ) {
		$fail( 'missing' );
	}

	$review_id = wp_insert_post(
		array(
			'post_type'    => 'ayat_review',
			'post_status'  => 'pending',
			'post_title'   => mb_substr( $name, 0, 60 ),
			'post_content' => mb_substr( $body, 0, 2000 ),
		)
	);
	if ( ! $review_id || is_wp_error( $review_id ) ) {
		$fail( 'error' );
	}
	update_post_meta( $review_id, '_ayat_rating', $rating );
	update_post_meta( $review_id, '_ayat_source', 'Website' );
	update_post_meta( $review_id, '_ayat_product', $pid );
	update_post_meta( $review_id, '_ayat_city', mb_substr( $city, 0, 40 ) );
	update_post_meta( $review_id, '_ayat_title', mb_substr( $title, 0, 80 ) );
	update_post_meta( $review_id, '_ayat_contact', mb_substr( $contact, 0, 80 ) );
	update_post_meta( $review_id, '_ayat_verified', ayat_rv_is_verified( $contact, $pid ) ? 1 : 0 );

	// Up to 3 photos, images only, 5 MB each.
	if ( ! empty( $_FILES['rv_photos']['name'] ) && is_array( $_FILES['rv_photos']['name'] ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$files  = $_FILES['rv_photos']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$photos = array();
		$count  = min( 3, count( $files['name'] ) );
		for ( $i = 0; $i < $count; $i++ ) {
			if ( empty( $files['name'][ $i ] ) || UPLOAD_ERR_OK !== (int) $files['error'][ $i ] || (int) $files['size'][ $i ] > 5 * MB_IN_BYTES ) {
				continue;
			}
			$check = wp_check_filetype_and_ext( $files['tmp_name'][ $i ], $files['name'][ $i ] );
			if ( empty( $check['type'] ) || 0 !== strpos( $check['type'], 'image/' ) ) {
				continue;
			}
			$_FILES['ayat_rv_one'] = array(
				'name'     => $files['name'][ $i ],
				'type'     => $check['type'],
				'tmp_name' => $files['tmp_name'][ $i ],
				'error'    => 0,
				'size'     => $files['size'][ $i ],
			);
			$att = media_handle_upload( 'ayat_rv_one', $review_id, array(), array( 'test_form' => false ) );
			if ( ! is_wp_error( $att ) ) {
				$photos[] = (int) $att;
			}
		}
		if ( $photos ) {
			update_post_meta( $review_id, '_ayat_photos', $photos );
		}
	}

	set_transient( $rate, $hits + 1, HOUR_IN_SECONDS );

	// Let the store owner know.
	$admin = get_option( 'admin_email' );
	if ( $admin ) {
		wp_mail(
			$admin,
			/* translators: %s: product name */
			sprintf( __( 'New review waiting for approval: %s', 'ayatcollections' ), get_the_title( $pid ) ),
			sprintf( "%s ★%d\n\n%s\n\n%s", $name, $rating, $body, admin_url( 'edit.php?post_type=ayat_review&post_status=pending' ) )
		);
	}

	wp_safe_redirect( add_query_arg( 'review', 'thanks', $back ) . '#customer-reviews' );
	exit;
}
add_action( 'admin_post_nopriv_ayat_submit_review', 'ayat_rv_submit' );
add_action( 'admin_post_ayat_submit_review', 'ayat_rv_submit' );

/**
 * Product page: the reviews block.
 */
function ayat_rv_product_block() {
	global $product;
	if ( ! $product ) {
		return;
	}
	$pid     = $product->get_id();
	$stats   = ayat_rv_stats( $pid );
	$reviews = ayat_rv_for_product( $pid );
	$notice  = isset( $_GET['review'] ) ? sanitize_key( wp_unslash( $_GET['review'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$notes   = array(
		'thanks'  => __( 'Thank you! Your review was sent and will appear after we check it.', 'ayatcollections' ),
		'missing' => __( 'Please choose a star rating and write your name and a short review.', 'ayatcollections' ),
		'slow'    => __( 'You have sent several reviews already. Please try again in an hour.', 'ayatcollections' ),
		'expired' => __( 'The page expired. Please write your review again.', 'ayatcollections' ),
		'error'   => __( 'Something went wrong. Please try again.', 'ayatcollections' ),
	);
	?>
	<section class="rv" id="customer-reviews" aria-labelledby="rv-title">
		<div class="rv__top">
			<div class="rv__score">
				<h2 id="rv-title" class="rv__title"><?php esc_html_e( 'Customer Reviews', 'ayatcollections' ); ?></h2>
				<?php if ( $stats['count'] ) : ?>
					<p class="rv__avg"><strong><?php echo esc_html( number_format_i18n( $stats['avg'], 1 ) ); ?></strong><span>/ 5</span></p>
					<?php echo ayat_rv_stars( $stats['avg'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php /* translators: %d: number of reviews */ ?>
					<p class="rv__based"><?php echo esc_html( sprintf( _n( 'Based on %d review', 'Based on %d reviews', $stats['count'], 'ayatcollections' ), $stats['count'] ) ); ?></p>
				<?php else : ?>
					<p class="rv__based"><?php esc_html_e( 'No reviews yet. Be the first to share how you wear it.', 'ayatcollections' ); ?></p>
				<?php endif; ?>
			</div>
			<?php if ( $stats['count'] ) : ?>
				<ul class="rv__bars" role="list">
					<?php foreach ( $stats['bars'] as $star => $n ) : ?>
						<?php $w = $stats['count'] ? round( $n / $stats['count'] * 100 ) : 0; ?>
						<li>
							<span class="rv__bar-label"><?php echo esc_html( $star ); ?> <span aria-hidden="true">★</span></span>
							<span class="rv__bar" aria-hidden="true"><span style="width:<?php echo esc_attr( $w ); ?>%"></span></span>
							<?php /* translators: 1: number of reviews, 2: stars */ ?>
							<span class="rv__bar-count"><span class="screen-reader-text"><?php echo esc_html( sprintf( __( '%1$d reviews with %2$d stars', 'ayatcollections' ), $n, $star ) ); ?></span><span aria-hidden="true"><?php echo esc_html( $n ); ?></span></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<div class="rv__cta">
				<button type="button" class="btn-ink rv__write" aria-expanded="<?php echo in_array( $notice, array( 'missing', 'expired', 'error' ), true ) ? 'true' : 'false'; ?>" aria-controls="rv-form"><?php esc_html_e( 'Write a review', 'ayatcollections' ); ?></button>
				<p class="rv__promise"><?php esc_html_e( 'Every review is from a real customer and checked by our team.', 'ayatcollections' ); ?></p>
			</div>
		</div>

		<?php if ( $notice && isset( $notes[ $notice ] ) ) : ?>
			<p class="rv__notice<?php echo 'thanks' === $notice ? ' is-ok' : ''; ?>" role="status"><?php echo esc_html( $notes[ $notice ] ); ?></p>
		<?php endif; ?>

		<form id="rv-form" class="rv-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data" <?php echo in_array( $notice, array( 'missing', 'expired', 'error' ), true ) ? '' : 'hidden'; ?>>
			<input type="hidden" name="action" value="ayat_submit_review">
			<input type="hidden" name="product_id" value="<?php echo esc_attr( $pid ); ?>">
			<?php wp_nonce_field( 'ayat_rv_submit', 'ayat_rv_nonce' ); ?>
			<p class="rv-form__hp" aria-hidden="true"><label>Website <input type="text" name="ayat_rv_site" tabindex="-1" autocomplete="off"></label></p>
			<fieldset class="rv-form__rate">
				<legend><?php esc_html_e( 'Your rating', 'ayatcollections' ); ?> *</legend>
				<div class="rv-pick">
					<?php for ( $i = 5; $i >= 1; $i-- ) : ?>
						<input type="radio" id="rv-r<?php echo esc_attr( $i ); ?>" name="rating" value="<?php echo esc_attr( $i ); ?>" required>
						<?php /* translators: %d: stars */ ?>
						<label for="rv-r<?php echo esc_attr( $i ); ?>" title="<?php echo esc_attr( sprintf( _n( '%d star', '%d stars', $i, 'ayatcollections' ), $i ) ); ?>"><span class="screen-reader-text"><?php echo esc_html( sprintf( _n( '%d star', '%d stars', $i, 'ayatcollections' ), $i ) ); ?></span>★</label>
					<?php endfor; ?>
				</div>
			</fieldset>
			<div class="rv-form__grid">
				<p><label for="rv-name"><?php esc_html_e( 'Your name', 'ayatcollections' ); ?> *</label><input id="rv-name" name="rv_name" autocomplete="name" maxlength="60" required></p>
				<p><label for="rv-city"><?php esc_html_e( 'City', 'ayatcollections' ); ?></label><input id="rv-city" name="rv_city" autocomplete="address-level2" maxlength="40"></p>
				<p class="rv-form__wide"><label for="rv-contact"><?php esc_html_e( 'Phone or email used for your order', 'ayatcollections' ); ?></label><input id="rv-contact" name="rv_contact" autocomplete="tel" maxlength="80" aria-describedby="rv-contact-help"><small id="rv-contact-help"><?php esc_html_e( 'Never shown. We use it to add the "Verified buyer" badge.', 'ayatcollections' ); ?></small></p>
				<p class="rv-form__wide"><label for="rv-title2"><?php esc_html_e( 'Review title', 'ayatcollections' ); ?></label><input id="rv-title2" name="rv_title" maxlength="80" placeholder="<?php esc_attr_e( 'e.g. Still shining after a month', 'ayatcollections' ); ?>"></p>
				<p class="rv-form__wide"><label for="rv-body"><?php esc_html_e( 'Your review', 'ayatcollections' ); ?> *</label><textarea id="rv-body" name="rv_body" rows="4" maxlength="2000" required></textarea></p>
				<p class="rv-form__wide"><label for="rv-photos"><?php esc_html_e( 'Add photos (up to 3)', 'ayatcollections' ); ?></label><input id="rv-photos" name="rv_photos[]" type="file" accept="image/*" multiple></p>
			</div>
			<button type="submit" class="btn-ink"><?php esc_html_e( 'Send review', 'ayatcollections' ); ?></button>
		</form>

		<?php if ( $reviews ) : ?>
			<div class="rv__tools">
				<label for="rv-sort"><?php esc_html_e( 'Sort by', 'ayatcollections' ); ?></label>
				<select id="rv-sort" data-rv-sort>
					<option value="recent"><?php esc_html_e( 'Most recent', 'ayatcollections' ); ?></option>
					<option value="high"><?php esc_html_e( 'Highest rating', 'ayatcollections' ); ?></option>
					<option value="low"><?php esc_html_e( 'Lowest rating', 'ayatcollections' ); ?></option>
					<option value="photos"><?php esc_html_e( 'With photos first', 'ayatcollections' ); ?></option>
				</select>
			</div>
			<ul class="rv__list" role="list" data-rv-list>
				<?php foreach ( $reviews as $i => $r ) : ?>
					<?php
					$rating   = max( 1, min( 5, (int) get_post_meta( $r->ID, '_ayat_rating', true ) ) );
					$city     = (string) get_post_meta( $r->ID, '_ayat_city', true );
					$rtitle   = (string) get_post_meta( $r->ID, '_ayat_title', true );
					$verified = (bool) get_post_meta( $r->ID, '_ayat_verified', true );
					$photos   = (array) get_post_meta( $r->ID, '_ayat_photos', true );
					$photos   = array_filter( array_map( 'absint', $photos ) );
					if ( ! $photos && has_post_thumbnail( $r ) ) {
						$photos = array( get_post_thumbnail_id( $r ) );
					}
					$sample = get_post_meta( $r->ID, '_ayat_sample', true );
					?>
					<li class="rv-card" data-rating="<?php echo esc_attr( $rating ); ?>" data-time="<?php echo esc_attr( get_post_time( 'U', true, $r ) ); ?>" data-photos="<?php echo esc_attr( count( $photos ) ); ?>"<?php echo $i >= 5 ? ' hidden data-more' : ''; ?>>
						<div class="rv-card__head">
							<span class="rv-card__avatar" aria-hidden="true"><?php echo esc_html( mb_strtoupper( mb_substr( get_the_title( $r ), 0, 1 ) ) ); ?></span>
							<div>
								<p class="rv-card__name"><?php echo esc_html( get_the_title( $r ) ); ?><?php if ( $verified ) : ?> <span class="rv-card__verified"><svg viewBox="0 0 24 24" width="13" height="13" aria-hidden="true"><path fill="currentColor" d="m12 1 2.4 2.2 3.2-.4.9 3.1 2.9 1.5-1 3.1 1 3.1-2.9 1.5-.9 3.1-3.2-.4L12 23l-2.4-2.2-3.2.4-.9-3.1L2.6 16.6l1-3.1-1-3.1 2.9-1.5.9-3.1 3.2.4Zm-1.3 14.3 6-6-1.4-1.4-4.6 4.6-2.3-2.3-1.4 1.4Z"/></svg><?php esc_html_e( 'Verified buyer', 'ayatcollections' ); ?></span><?php endif; ?></p>
								<p class="rv-card__meta"><?php echo esc_html( trim( $city . ( $city ? ' · ' : '' ) . get_the_date( '', $r ) ) ); ?></p>
							</div>
							<?php echo ayat_rv_stars( $rating ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
						<?php if ( $rtitle ) : ?><p class="rv-card__title"><?php echo esc_html( $rtitle ); ?></p><?php endif; ?>
						<div class="rv-card__body"><?php echo wp_kses_post( wpautop( $r->post_content ) ); ?></div>
						<?php if ( $photos ) : ?>
							<div class="rv-card__pics">
								<?php foreach ( $photos as $att ) : ?>
									<a href="<?php echo esc_url( wp_get_attachment_image_url( $att, 'large' ) ); ?>" target="_blank" rel="noopener"><?php echo wp_get_attachment_image( $att, 'thumbnail', false, array( 'loading' => 'lazy', 'alt' => esc_attr__( 'Customer photo', 'ayatcollections' ) ) ); ?></a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
						<?php if ( $sample ) : ?><span class="review-card__sample"><?php esc_html_e( 'Sample review (demo)', 'ayatcollections' ); ?></span><?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php if ( count( $reviews ) > 5 ) : ?>
				<p class="rv__more-wrap"><button type="button" class="link-gold rv__more" data-rv-more><?php esc_html_e( 'Show more reviews', 'ayatcollections' ); ?></button></p>
			<?php endif; ?>
		<?php endif; ?>
	</section>
	<?php
}

/**
 * Swap the old product-page block for the new one.
 */
function ayat_rv_hooks() {
	remove_action( 'woocommerce_after_single_product_summary', 'ayat_product_social_reviews', 12 );
	add_action( 'woocommerce_after_single_product_summary', 'ayat_rv_product_block', 12 );
	add_action( 'woocommerce_after_shop_loop_item_title', 'ayat_rv_loop_stars', 6 );
	add_action( 'woocommerce_single_product_summary', 'ayat_rv_summary_stars', 7 );
}
add_action( 'init', 'ayat_rv_hooks', 20 );

/**
 * Stars + count on shop cards.
 */
function ayat_rv_loop_stars() {
	global $product;
	if ( ! $product ) {
		return;
	}
	$s = ayat_rv_stats( $product->get_id() );
	if ( ! $s['count'] ) {
		return;
	}
	/* translators: %d: number of reviews */
	echo '<p class="rv-loop">' . ayat_rv_stars( $s['avg'] ) . '<span>' . esc_html( sprintf( _n( '%d review', '%d reviews', $s['count'], 'ayatcollections' ), $s['count'] ) ) . '</span></p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Stars under the product title, linking to the reviews.
 */
function ayat_rv_summary_stars() {
	global $product;
	if ( ! $product ) {
		return;
	}
	$s = ayat_rv_stats( $product->get_id() );
	if ( ! $s['count'] ) {
		return;
	}
	/* translators: %d: number of reviews */
	echo '<a class="rv-loop rv-loop--single" href="#customer-reviews">' . ayat_rv_stars( $s['avg'] ) . '<span>' . esc_html( number_format_i18n( $s['avg'], 1 ) . ' · ' . sprintf( _n( '%d review', '%d reviews', $s['count'], 'ayatcollections' ), $s['count'] ) ) . '</span></a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Admin: show what the customer sent (city, title, contact, verified, photos).
 */
function ayat_rv_admin_box() {
	add_meta_box( 'ayat_rv_customer', __( 'Sent by the customer', 'ayatcollections' ), 'ayat_rv_admin_box_html', 'ayat_review', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'ayat_rv_admin_box' );

/**
 * Admin box markup.
 *
 * @param WP_Post $post Review.
 */
function ayat_rv_admin_box_html( $post ) {
	$photos = array_filter( array_map( 'absint', (array) get_post_meta( $post->ID, '_ayat_photos', true ) ) );
	echo '<p><strong>' . esc_html__( 'Review title:', 'ayatcollections' ) . '</strong> ' . esc_html( get_post_meta( $post->ID, '_ayat_title', true ) ) . '</p>';
	echo '<p><strong>' . esc_html__( 'City:', 'ayatcollections' ) . '</strong> ' . esc_html( get_post_meta( $post->ID, '_ayat_city', true ) ) . '</p>';
	echo '<p><strong>' . esc_html__( 'Phone / email (private):', 'ayatcollections' ) . '</strong> ' . esc_html( get_post_meta( $post->ID, '_ayat_contact', true ) ) . '</p>';
	echo '<p><strong>' . esc_html__( 'Verified buyer:', 'ayatcollections' ) . '</strong> ' . ( get_post_meta( $post->ID, '_ayat_verified', true ) ? esc_html__( 'Yes – matches an order for this product', 'ayatcollections' ) : esc_html__( 'No match found', 'ayatcollections' ) ) . '</p>';
	if ( $photos ) {
		echo '<p>';
		foreach ( $photos as $att ) {
			echo wp_get_attachment_image( $att, 'thumbnail' ) . ' ';
		}
		echo '</p>';
	}
	echo '<p class="description">' . esc_html__( 'Click "Publish" to show this review on the product page, or move it to the Bin to reject it.', 'ayatcollections' ) . '</p>';
}

/**
 * Admin menu bubble with the number of reviews waiting.
 */
function ayat_rv_menu_bubble() {
	global $menu;
	$n = (int) wp_count_posts( 'ayat_review' )->pending;
	if ( ! $n || ! is_array( $menu ) ) {
		return;
	}
	foreach ( $menu as $k => $item ) {
		if ( isset( $item[2] ) && 'edit.php?post_type=ayat_review' === $item[2] ) {
			$menu[ $k ][0] .= ' <span class="awaiting-mod"><span class="pending-count">' . $n . '</span></span>'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		}
	}
}
add_action( 'admin_menu', 'ayat_rv_menu_bubble', 99 );

/**
 * Hide WooCommerce's own reviews tab: the SHAYA block above replaces it.
 *
 * @param array $tabs Tabs.
 * @return array
 */
function ayat_rv_remove_wc_tab( $tabs ) {
	unset( $tabs['reviews'] );
	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'ayat_rv_remove_wc_tab', 98 );
