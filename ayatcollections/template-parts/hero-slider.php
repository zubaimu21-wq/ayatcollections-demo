<?php
/**
 * Homepage hero slider. Content comes from Appearance → Customize → Homepage slider.
 *
 * @package Ayatcollections
 */

defined( 'ABSPATH' ) || exit;

$slides = array();
for ( $i = 1; $i <= ayat_slide_count(); $i++ ) {
	$s = ayat_get_slide( $i );
	if ( $s ) {
		$slides[] = $s;
	}
}
if ( ! $slides ) {
	return;
}
$total    = count( $slides );
$interval = ayat_sanitize_interval( get_theme_mod( 'ayat_slider_interval', 5500 ) );
?>
<section class="hero" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Featured collections', 'ayatcollections' ); ?>" data-interval="<?php echo esc_attr( $interval ); ?>">
	<div class="hero__viewport" aria-live="off">
		<?php foreach ( $slides as $n => $s ) : ?>
			<?php
			$first = ( 0 === $n );
			$style = sprintf( '--fx:%d%%;--fy:%d%%;--mfx:%d%%;--mfy:%d%%', $s['focal_x'], $s['focal_y'], $s['m_focal_x'], $s['m_focal_y'] );
			$attrs = array(
				'class'         => 'hero__img',
				'alt'           => $s['alt'],
				'sizes'         => '100vw',
				'loading'       => $first ? 'eager' : 'lazy',
				'decoding'      => $first ? 'sync' : 'async',
				'fetchpriority' => $first ? 'high' : 'low',
			);
			?>
			<div class="hero__slide<?php echo $first ? ' is-active' : ''; ?>" id="hero-slide-<?php echo esc_attr( $n + 1 ); ?>" role="group" aria-roledescription="slide"
				<?php /* translators: 1: slide number, 2: total */ ?>
				aria-label="<?php echo esc_attr( sprintf( __( '%1$d of %2$d', 'ayatcollections' ), $n + 1, $total ) ); ?>"
				<?php echo $first ? '' : 'aria-hidden="true"'; ?>
				style="<?php echo esc_attr( $style ); ?>">
				<div class="hero__media">
					<picture>
						<?php if ( $s['mobile_id'] ) : ?>
							<source media="(max-width: 700px)" srcset="<?php echo esc_attr( wp_get_attachment_image_srcset( $s['mobile_id'], 'large' ) ? wp_get_attachment_image_srcset( $s['mobile_id'], 'large' ) : wp_get_attachment_image_url( $s['mobile_id'], 'large' ) ); ?>" sizes="100vw">
						<?php endif; ?>
						<?php
						if ( $s['image_id'] ) {
							echo wp_get_attachment_image( $s['image_id'], 'ayat-hero', false, $attrs );
						} else {
							printf(
								'<img class="hero__img" src="%1$s" srcset="%2$s 960w, %1$s 1792w" sizes="100vw" width="1792" height="768" alt="%3$s" loading="%4$s" decoding="%5$s" fetchpriority="%6$s">',
								esc_url( $s['fallback'] ),
								esc_url( $s['fallback_sm'] ),
								esc_attr( $s['alt'] ),
								esc_attr( $attrs['loading'] ),
								esc_attr( $attrs['decoding'] ),
								esc_attr( $attrs['fetchpriority'] )
							);
						}
						?>
					</picture>
				</div>
				<div class="hero__content">
					<div class="hero__text">
						<?php if ( $s['eyebrow'] ) : ?>
							<p class="hero__eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p>
						<?php endif; ?>
						<h2 class="hero__title"><?php echo esc_html( $s['headline'] ); ?></h2>
						<?php if ( $s['text'] ) : ?>
							<p class="hero__lead"><?php echo esc_html( $s['text'] ); ?></p>
						<?php endif; ?>
						<?php if ( $s['button_label'] ) : ?>
							<a class="btn btn--gold" href="<?php echo esc_url( ayat_resolve_link( $s['button_url'] ) ); ?>"<?php echo $first ? '' : ' tabindex="-1"'; ?>><?php echo esc_html( $s['button_label'] ); ?></a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<?php if ( $total > 1 ) : ?>
		<div class="hero__controls">
			<div class="hero__controls-inner">
				<button type="button" class="hero__btn hero__prev" aria-controls="hero-slide-1" aria-label="<?php esc_attr_e( 'Previous slide', 'ayatcollections' ); ?>"><?php echo ayat_icon( 'prev' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
				<div class="hero__dots">
					<?php for ( $n = 0; $n < $total; $n++ ) : ?>
						<button type="button" class="hero__dot" data-slide="<?php echo esc_attr( $n ); ?>" aria-controls="hero-slide-<?php echo esc_attr( $n + 1 ); ?>"
							<?php /* translators: %d: slide number */ ?>
							aria-label="<?php echo esc_attr( sprintf( __( 'Show slide %d', 'ayatcollections' ), $n + 1 ) ); ?>"<?php echo 0 === $n ? ' aria-current="true"' : ''; ?>><span></span></button>
					<?php endfor; ?>
				</div>
				<button type="button" class="hero__btn hero__next" aria-controls="hero-slide-1" aria-label="<?php esc_attr_e( 'Next slide', 'ayatcollections' ); ?>"><?php echo ayat_icon( 'next' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
				<button type="button" class="hero__btn hero__toggle" aria-label="<?php esc_attr_e( 'Pause slideshow', 'ayatcollections' ); ?>" data-label-pause="<?php esc_attr_e( 'Pause slideshow', 'ayatcollections' ); ?>" data-label-play="<?php esc_attr_e( 'Play slideshow', 'ayatcollections' ); ?>">
					<span class="hero__toggle-pause"><?php echo ayat_icon( 'pause' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<span class="hero__toggle-play"><?php echo ayat_icon( 'play' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				</button>
			</div>
		</div>
	<?php endif; ?>
</section>
