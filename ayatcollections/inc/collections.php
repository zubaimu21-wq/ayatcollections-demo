<?php
/**
 * Automatic collections: every top-level product category that has at least
 * one published product is shown on the homepage and the Collections page.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get the collections to display.
 *
 * WooCommerce keeps the term count in sync with published, visible products,
 * so hide_empty=true drops categories that only contain drafts. Categories
 * are ordered by the drag-and-drop order in Products → Categories.
 *
 * @param int $limit 0 for all.
 * @return WP_Term[]
 */
function ayat_get_collections( $limit = 0 ) {
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return array();
	}

	$args = array(
		'taxonomy'   => 'product_cat',
		'parent'     => 0,
		'hide_empty' => true,
		'exclude'    => array( (int) get_option( 'default_product_cat', 0 ) ),
		'orderby'    => 'menu_order',
		'order'      => 'ASC',
	);
	if ( $limit > 0 ) {
		$args['number'] = $limit;
	}

	$terms = get_terms( $args );
	if ( is_wp_error( $terms ) ) {
		return array();
	}

	// Double-check that a published product exists (also in child categories).
	return array_values(
		array_filter(
			$terms,
			static function ( $term ) {
				$found = get_posts(
					array(
						'post_type'      => 'product',
						'post_status'    => 'publish',
						'posts_per_page' => 1,
						'fields'         => 'ids',
						'no_found_rows'  => true,
						'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
							array(
								'taxonomy'         => 'product_cat',
								'terms'            => $term->term_id,
								'include_children' => true,
							),
						),
					)
				);
				return ! empty( $found );
			}
		)
	);
}

/**
 * New categories go to the end of the collection list (drag to reorder them
 * in Products → Categories).
 *
 * @param int $term_id Term ID.
 */
function ayat_new_category_last( $term_id ) {
	$terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'fields' => 'ids' ) );
	update_term_meta( $term_id, 'order', is_array( $terms ) ? count( $terms ) : 99 );
}
add_action( 'created_product_cat', 'ayat_new_category_last', 20 );

/**
 * Attachment ID for a collection card: the category thumbnail, or the newest
 * product's featured image when no thumbnail has been set.
 *
 * @param WP_Term $term Category.
 * @return int
 */
function ayat_collection_image_id( $term ) {
	$thumb = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );
	if ( $thumb ) {
		return $thumb;
	}

	$ids = get_posts(
		array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_key'       => '_thumbnail_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'product_cat',
					'terms'    => $term->term_id,
				),
			),
		)
	);

	return $ids ? (int) get_post_thumbnail_id( $ids[0] ) : 0;
}

/**
 * Print the collection card grid.
 *
 * @param WP_Term[] $terms   Terms.
 * @param string    $heading Heading level tag for card titles.
 */
function ayat_render_collection_cards( $terms, $heading = 'h3' ) {
	if ( empty( $terms ) ) {
		echo '<p class="ayat-empty">' . esc_html__( 'Collections appear here once a category has a published product.', 'ayatcollections' ) . '</p>';
		return;
	}
	$tag = in_array( $heading, array( 'h2', 'h3' ), true ) ? $heading : 'h3';

	echo '<ul class="collection-grid" role="list">';
	foreach ( $terms as $term ) {
		$img  = ayat_collection_image_id( $term );
		$link = get_term_link( $term );
		if ( is_wp_error( $link ) ) {
			continue;
		}
		echo '<li class="collection-card">';
		echo '<a href="' . esc_url( $link ) . '" class="collection-card__link">';
		echo '<span class="collection-card__media">';
		if ( $img ) {
			echo wp_get_attachment_image(
				$img,
				'ayat-card',
				false,
				array(
					'loading' => 'lazy',
					'sizes'   => '(max-width: 600px) 50vw, (max-width: 1100px) 33vw, 25vw',
					/* translators: %s: collection name */
					'alt'     => sprintf( __( '%s collection', 'ayatcollections' ), $term->name ),
				)
			);
		} else {
			echo '<span class="collection-card__placeholder" aria-hidden="true"></span>';
		}
		echo '<span class="collection-card__initial" aria-hidden="true">' . esc_html( function_exists( 'mb_substr' ) ? mb_substr( $term->name, 0, 1 ) : substr( $term->name, 0, 1 ) ) . '</span>';
		echo '</span>';
		echo '<span class="collection-card__body">';
		echo '<' . $tag . ' class="collection-card__title">' . esc_html( $term->name ) . '</' . $tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		/* translators: %d: number of products */
		echo '<span class="collection-card__count">' . esc_html( sprintf( _n( '%d piece', '%d pieces', (int) $term->count, 'ayatcollections' ), (int) $term->count ) ) . ' · ' . esc_html__( 'Shop now', 'ayatcollections' ) . '</span>';
		echo '</span>';
		echo '</a></li>';
	}
	echo '</ul>';
}
