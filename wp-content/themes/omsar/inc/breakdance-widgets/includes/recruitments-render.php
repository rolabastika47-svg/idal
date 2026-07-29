<?php
/**
 * Breakdance Recruitments element rendering.
 *
 * @package OmsarBreakdanceElements
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default settings Breakdance Recruitments element.
 *
 * @return array<string, mixed>
 */
function omsar_bd_recruitments_default_settings() {
	return array(
		'orderby'                        => 'meta_value',
		'order'                          => 'DESC',
		'columns'                        => '3',
		'posts_per_page'                 => 9,
		'filter_label'                   => null,
		'card_background_type'           => 'image',
		'card_background_image'          => array(
			'url' => get_template_directory_uri() . '/assets/images/project-2.png',
			'id'  => 0,
		),
		'card_background_overlay'        => 'no',
		'card_gradient_color_from'       => 'rgba(173, 216, 230, 0.25)',
		'card_gradient_color_to'         => 'rgba(255, 255, 255, 0.95)',
		'card_gradient_angle'            => array(
			'size' => 135,
			'unit' => 'deg',
		),
		'card_gradient_overlay_opacity'  => array(
			'size' => 0.65,
			'unit' => '',
		),
		'card_overlay_border_radius'     => array(
			'size' => 12,
			'unit' => 'px',
		),
	);
}

/**
 * Normalize Breakdance element settings into a common array.
 *
 * @param array<string, mixed> $settings Raw settings.
 * @return array<string, mixed>
 */
function omsar_bd_normalize_recruitments_settings( $settings ) {
	$defaults = omsar_bd_recruitments_default_settings();
	$settings = wp_parse_args( is_array( $settings ) ? $settings : array(), $defaults );

	if ( isset( $settings['card_background_overlay'] ) && $settings['card_background_overlay'] === 'yes' ) {
		$settings['card_background_overlay'] = 'yes';
	} elseif ( empty( $settings['card_background_overlay'] ) || $settings['card_background_overlay'] === 'no' ) {
		$settings['card_background_overlay'] = 'no';
	}

	return $settings;
}

/**
 * Map Breakdance $propertiesData to normalized widget settings.
 *
 * @param array<string, mixed> $properties_data Breakdance properties.
 * @return array<string, mixed>
 */
function omsar_bd_recruitments_settings_from_breakdance( $properties_data ) {
	$content = $properties_data['content']['content'] ?? array();
	$design  = $properties_data['design']['card'] ?? $properties_data['design']['design'] ?? array();

	$background_image = $design['card_background_image'] ?? array();
	if ( is_string( $background_image ) ) {
		$background_image = array( 'url' => $background_image );
	}

	$settings = array(
		'orderby'                       => $content['orderby'] ?? 'meta_value',
		'order'                       => $content['order'] ?? 'DESC',
		'columns'                     => (string) ( $content['columns'] ?? '3' ),
		'posts_per_page'              => isset( $content['posts_per_page'] ) ? (int) $content['posts_per_page'] : 9,
		'filter_label'                => isset( $content['filter_label'] ) && $content['filter_label'] !== '' ? $content['filter_label'] : null,
		'card_background_type'        => $design['card_background_type'] ?? 'image',
		'card_background_image'       => $background_image,
		'card_background_overlay'     => ! empty( $design['card_background_overlay'] ) ? 'yes' : 'no',
		'card_gradient_color_from'    => $design['card_gradient_color_from'] ?? 'rgba(173, 216, 230, 0.4)',
		'card_gradient_color_to'      => $design['card_gradient_color_to'] ?? 'rgba(255, 255, 255, 0.95)',
		'card_gradient_angle'         => array(
			'size' => isset( $design['card_gradient_angle']['number'] ) ? (float) $design['card_gradient_angle']['number'] : 135,
			'unit' => 'deg',
		),
		'card_gradient_overlay_opacity' => array(
			'size' => isset( $design['card_gradient_overlay_opacity']['number'] ) ? (float) $design['card_gradient_overlay_opacity']['number'] : 0.8,
			'unit' => '',
		),
		'card_overlay_border_radius'  => array(
			'size' => isset( $design['card_overlay_border_radius']['number'] ) ? (float) $design['card_overlay_border_radius']['number'] : 12,
			'unit' => 'px',
		),
	);

	return omsar_bd_normalize_recruitments_settings( $settings );
}

/**
 * Resolve configured card background image URL.
 *
 * @param array<string, mixed> $settings Widget settings.
 * @return string
 */
function omsar_bd_recruitments_resolve_background_image_url( $settings ) {
	$background_type = $settings['card_background_type'] ?? 'image';
	$background_image = '';

	if ( isset( $settings['card_background_image'] ) ) {
		if ( is_string( $settings['card_background_image'] ) && $settings['card_background_image'] !== '' ) {
			$background_image = $settings['card_background_image'];
		} elseif ( is_array( $settings['card_background_image'] ) ) {
			if ( ! empty( $settings['card_background_image']['url'] ) ) {
				$background_image = $settings['card_background_image']['url'];
			} elseif ( ! empty( $settings['card_background_image']['id'] ) ) {
				$image_url = wp_get_attachment_image_url( (int) $settings['card_background_image']['id'], 'full' );
				if ( $image_url ) {
					$background_image = $image_url;
				}
			}
		}
	}

	if ( $background_type === 'image' && $background_image === '' ) {
		$background_image = get_template_directory_uri() . '/assets/images/project-2.png';
	}

	return $background_image;
}

/**
 * Build inline gradient CSS for card ::before overlay.
 *
 * @param array<string, mixed> $settings Widget settings.
 * @return string
 */
function omsar_bd_recruitments_build_gradient_css( $settings ) {
	$gradient_color_from = ! empty( $settings['card_gradient_color_from'] ) ? $settings['card_gradient_color_from'] : 'rgba(173, 216, 230, 0.4)';
	$gradient_color_to   = ! empty( $settings['card_gradient_color_to'] ) ? $settings['card_gradient_color_to'] : 'rgba(255, 255, 255, 0.95)';
	$gradient_angle      = ! empty( $settings['card_gradient_angle']['size'] ) ? $settings['card_gradient_angle']['size'] : 135;
	$gradient_unit       = ! empty( $settings['card_gradient_angle']['unit'] ) ? $settings['card_gradient_angle']['unit'] : 'deg';
	$gradient_opacity    = ! empty( $settings['card_gradient_overlay_opacity']['size'] ) ? $settings['card_gradient_overlay_opacity']['size'] : 0.8;
	$overlay_radius      = ! empty( $settings['card_overlay_border_radius']['size'] ) ? $settings['card_overlay_border_radius']['size'] : 12;
	$overlay_radius_unit = ! empty( $settings['card_overlay_border_radius']['unit'] ) ? $settings['card_overlay_border_radius']['unit'] : 'px';

	return sprintf(
		'background: linear-gradient(%s%s, %s 0%%, %s 100%%); opacity: %s; border-radius: %s%s;',
		esc_attr( $gradient_angle ),
		esc_attr( $gradient_unit ),
		esc_attr( $gradient_color_from ),
		esc_attr( $gradient_color_to ),
		esc_attr( $gradient_opacity ),
		esc_attr( $overlay_radius ),
		esc_attr( $overlay_radius_unit )
	);
}

/**
 * Get recruitment status type for a post.
 *
 * @param int $post_id Post ID.
 * @return string open|upcoming|closed
 */
function omsar_bd_recruitments_get_status_type( $post_id ) {
	if ( function_exists( 'omsar_get_recruitment_status_from_acf' ) ) {
		return omsar_get_recruitment_status_from_acf( $post_id );
	}

	$opening_date = get_field( 'opening_date', $post_id );
	$closing_date = get_field( 'closing_date', $post_id );

	if ( function_exists( 'omsar_compute_recruitment_status_from_date_values' ) ) {
		return omsar_compute_recruitment_status_from_date_values( $opening_date, $closing_date );
	}

	return 'upcoming';
}

/**
 * Format an ACF date value as d/m/Y.
 *
 * @param mixed $date_value Raw date value.
 * @return string
 */
function omsar_bd_recruitments_format_date_value( $date_value ) {
	if ( empty( $date_value ) ) {
		return '';
	}

	if ( is_numeric( $date_value ) && strlen( (string) $date_value ) === 8 ) {
		$date_obj = DateTime::createFromFormat( 'Ymd', (string) $date_value );
		return $date_obj ? $date_obj->format( 'd/m/Y' ) : (string) $date_value;
	}

	$date_obj = DateTime::createFromFormat( 'Y-m-d', (string) $date_value );
	if ( ! $date_obj ) {
		$date_obj = DateTime::createFromFormat( 'd/m/Y', (string) $date_value );
	}

	return $date_obj ? $date_obj->format( 'd/m/Y' ) : (string) $date_value;
}

/**
 * Get translated labels used by the recruitments widget.
 *
 * @return array<string, string>
 */
function omsar_bd_recruitments_get_labels() {
	return array(
		'open'            => function_exists( 'pll__' ) ? pll__( 'Open' ) : 'Open',
		'closed'          => function_exists( 'pll__' ) ? pll__( 'Closed' ) : 'Closed',
		'upcoming'        => function_exists( 'pll__' ) ? pll__( 'Upcoming' ) : 'Upcoming',
		'apply'           => function_exists( 'pll__' ) ? pll__( 'Apply' ) : 'Apply',
		'notify_me'       => function_exists( 'pll__' ) ? pll__( 'Notify me' ) : 'Notify me',
		'open_tab'        => function_exists( 'pll__' ) ? pll__( 'Open for applications' ) : 'Open for applications',
		'upcoming_tab'    => function_exists( 'pll__' ) ? pll__( 'Upcoming positions' ) : 'Upcoming positions',
		'closed_tab'      => function_exists( 'pll__' ) ? pll__( 'Closed positions' ) : 'Closed positions',
		'load_more'       => function_exists( 'pll__' ) ? pll__( 'Load More' ) : 'Load More',
		'filter_aria'     => function_exists( 'pll__' ) ? pll__( 'Filter by status' ) : 'Filter by status',
		'no_open_message' => function_exists( 'pll__' ) ? pll__( 'There are currently no open positions. Check upcoming opportunities and sign-up to be notified when they become available.' ) : 'There are currently no open positions. Check upcoming opportunities and sign-up to be notified when they become available.',
	);
}

/**
 * Render a single recruitment card.
 *
 * @param int                  $post_id Post ID.
 * @param array<string, mixed> $args    Render arguments.
 */
function omsar_bd_render_recruitment_card( $post_id, $args = array() ) {
	$defaults = array(
		'background_type'    => 'image',
		'background_image'   => '',
		'background_overlay' => false,
		'is_arabic'          => false,
		'labels'             => omsar_bd_recruitments_get_labels(),
	);
	$args = wp_parse_args( $args, $defaults );
	$labels = $args['labels'];

	if ( $args['is_arabic'] ) {
		$job_title_ar = get_field( 'job_title_ar', $post_id );
		$entity_ar    = get_field( 'entity_ar', $post_id );
		$job_title    = ! empty( $job_title_ar ) ? $job_title_ar : get_the_title( $post_id );
		$entity       = ! empty( $entity_ar ) ? $entity_ar : get_field( 'entity', $post_id );
		$job_link     = get_field( 'job_link_ar', $post_id );
		if ( empty( $job_link ) ) {
			$job_link = get_field( 'job_link', $post_id );
		}
	} else {
		$job_title = get_the_title( $post_id );
		$entity    = get_field( 'entity', $post_id );
		$job_link  = get_field( 'job_link', $post_id );
	}

	$item_background_image = $args['background_image'];
	if ( $args['background_type'] === 'image' ) {
		if ( has_post_thumbnail( $post_id ) ) {
			$item_background_image = get_the_post_thumbnail_url( $post_id, 'large' );
		} elseif ( ! empty( $args['background_image'] ) ) {
			$item_background_image = $args['background_image'];
		}
	}

	$opening_date_formatted = omsar_bd_recruitments_format_date_value( get_field( 'opening_date', $post_id ) );
	$closing_date_formatted = omsar_bd_recruitments_format_date_value( get_field( 'closing_date', $post_id ) );

	$status_type = omsar_bd_recruitments_get_status_type( $post_id );
	if ( 'upcoming' === $status_type ) {
		$status_badge = $labels['upcoming'];
	} elseif ( 'open' === $status_type ) {
		$status_badge = $labels['open'];
	} else {
		$status_badge = $labels['closed'];
	}

	$status_class = 'status-' . $status_type;
	?>
	<div class="omsar-recruitment-item">
		<div class="omsar-recruitment-card <?php echo $args['is_arabic'] ? 'rtl-card' : ''; ?><?php echo $args['background_overlay'] ? ' has-overlay' : ''; ?>"
			<?php if ( $args['is_arabic'] ) : ?> dir="rtl"<?php endif; ?>
			<?php if ( $args['background_type'] === 'image' && ! empty( $item_background_image ) ) : ?>
				style="background-image: url('<?php echo esc_url( $item_background_image ); ?>');"
			<?php endif; ?>>
			<div class="omsar-recruitment-content">
				<div class="omsar-recruitment-header">
					<h3 class="omsar-recruitment-title"><?php echo esc_html( $job_title ); ?></h3>
					<div class="omsar-recruitment-badge <?php echo esc_attr( $status_class ); ?>">
						<?php echo esc_html( $status_badge ); ?>
					</div>
				</div>

				<div class="omsar-recruitment-details">
					<?php if ( $entity ) : ?>
						<p class="omsar-recruitment-entity">
							<i class="bi bi-building" aria-hidden="true"></i>
							<span class="detail-value"><?php echo esc_html( $entity ); ?></span>
						</p>
					<?php endif; ?>

					<?php if ( $opening_date_formatted || $closing_date_formatted ) : ?>
						<p class="omsar-recruitment-dates">
							<i class="bi bi-calendar3" aria-hidden="true"></i>
							<?php if ( $opening_date_formatted && $closing_date_formatted ) : ?>
								<span class="detail-value"><?php echo esc_html( $opening_date_formatted ); ?> - <?php echo esc_html( $closing_date_formatted ); ?></span>
							<?php elseif ( $opening_date_formatted ) : ?>
								<span class="detail-value"><?php echo esc_html( $opening_date_formatted ); ?></span>
							<?php elseif ( $closing_date_formatted ) : ?>
								<span class="detail-value"><?php echo esc_html( $closing_date_formatted ); ?></span>
							<?php endif; ?>
						</p>
					<?php endif; ?>
				</div>

				<?php if ( 'open' === $status_type && ! empty( $job_link ) ) : ?>
					<div class="omsar-recruitment-actions">
						<a href="<?php echo esc_url( $job_link ); ?>" class="omsar-recruitment-apply-btn" target="_blank" rel="noopener noreferrer">
							<?php echo esc_html( $labels['apply'] ); ?>
						</a>
					</div>
				<?php elseif ( 'upcoming' === $status_type ) : ?>
					<div class="omsar-recruitment-actions">
						<button type="button" class="omsar-recruitment-apply-btn omsar-recruitment-notify-btn" data-recruitment-id="<?php echo esc_attr( $post_id ); ?>">
							<?php echo esc_html( $labels['notify_me'] ); ?>
						</button>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Print notify-me modal once per page.
 */
function omsar_bd_render_recruitments_notify_modal() {
	if ( ! empty( $GLOBALS['omsar_bd_recruitment_notify_modal_printed'] ) ) {
		return;
	}

	$GLOBALS['omsar_bd_recruitment_notify_modal_printed'] = true;
	$current_lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
	$is_arabic    = ( $current_lang === 'ar' || is_rtl() );
	?>
	<div id="omsar-bd-recruitment-notify-modal" class="omsar-bd-recruitment-notify-modal" role="dialog" aria-labelledby="omsar-bd-recruitment-notify-modal-title" aria-hidden="true"<?php if ( $is_arabic ) : ?> dir="rtl"<?php endif; ?>>
		<div class="omsar-bd-recruitment-notify-modal-overlay"></div>
		<div class="omsar-bd-recruitment-notify-modal-content">
			<button type="button" class="omsar-bd-recruitment-notify-modal-close" aria-label="<?php echo esc_attr( function_exists( 'pll__' ) ? pll__( 'Close' ) : 'Close' ); ?>">
				<span aria-hidden="true">&times;</span>
			</button>
			<div class="omsar-bd-recruitment-notify-modal-body">
				<h2 id="omsar-bd-recruitment-notify-modal-title" class="omsar-bd-recruitment-notify-modal-title">
					<?php echo esc_html( function_exists( 'pll__' ) ? pll__( 'Notify me' ) : 'Notify Me' ); ?>
				</h2>
				<p class="omsar-bd-recruitment-notify-modal-message">
					<?php echo esc_html( function_exists( 'pll__' ) ? pll__( 'Share your email to be notified when this position becomes available.' ) : 'Share your email to be notified when this position becomes available.' ); ?>
				</p>
				<form id="omsar-bd-recruitment-notify-form" class="omsar-bd-recruitment-notify-form">
					<input type="hidden" name="recruitment_id" id="omsar-bd-recruitment-notify-recruitment-id" value="">
					<div class="omsar-bd-recruitment-notify-form-group">
						<label for="omsar-bd-recruitment-notify-email" class="omsar-bd-recruitment-notify-label">
							<?php echo esc_html( function_exists( 'pll__' ) ? pll__( 'Email Address' ) : 'Email Address' ); ?>
						</label>
						<input
							type="email"
							id="omsar-bd-recruitment-notify-email"
							name="email"
							class="omsar-bd-recruitment-notify-input"
							required
							aria-required="true"
							aria-invalid="false"
							aria-describedby="omsar-bd-recruitment-notify-email-error"
						>
						<span id="omsar-bd-recruitment-notify-email-error" class="omsar-bd-recruitment-notify-error" role="alert" aria-live="polite"></span>
					</div>
					<div class="omsar-bd-recruitment-notify-form-actions">
						<button type="submit" class="omsar-bd-recruitment-notify-submit-btn">
							<span class="omsar-bd-recruitment-notify-submit-text"><?php echo esc_html( function_exists( 'pll__' ) ? pll__( 'Submit' ) : 'Submit' ); ?></span>
							<span class="omsar-bd-recruitment-notify-submit-loader" style="display: none;">
								<span class="spinner"></span>
								<?php echo esc_html( function_exists( 'pll__' ) ? pll__( 'Submitting...' ) : 'Submitting...' ); ?>
							</span>
						</button>
					</div>
					<div id="omsar-bd-recruitment-notify-success" class="omsar-bd-recruitment-notify-success" role="alert" aria-live="polite" style="display: none;"></div>
				</form>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Render the full recruitments widget markup.
 *
 * @param array<string, mixed> $settings    Widget settings.
 * @param string               $element_id  Unique element instance ID.
 * @param string               $wrapper_class_prefix Optional class prefix (elementor-element|bde-element).
 */
function omsar_bd_render_recruitments_widget( $settings, $element_id, $wrapper_class_prefix = 'bde-element' ) {
	$settings = omsar_bd_normalize_recruitments_settings( $settings );

	$orderby        = ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'meta_value';
	$order          = ! empty( $settings['order'] ) ? $settings['order'] : 'DESC';
	$columns        = isset( $settings['columns'] ) ? (string) $settings['columns'] : '3';
	$posts_per_page = isset( $settings['posts_per_page'] ) ? max( 1, (int) $settings['posts_per_page'] ) : 9;
	$background_type = $settings['card_background_type'] ?? 'image';
	$background_image = omsar_bd_recruitments_resolve_background_image_url( $settings );
	$background_overlay = ( $settings['card_background_overlay'] ?? 'no' ) === 'yes';
	$labels = omsar_bd_recruitments_get_labels();

	$query = new WP_Query(
		array(
			'post_type'      => 'recruitments',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'no_found_rows'  => true,
		)
	);

	$all_posts = $query->posts;
	if ( ! empty( $all_posts ) && function_exists( 'omsar_sort_recruitments_by_status_priority' ) ) {
		$all_posts = omsar_sort_recruitments_by_status_priority( $all_posts );
	}
	if ( function_exists( 'omsar_filter_recruitments_by_status' ) ) {
		$all_posts = omsar_filter_recruitments_by_status( $all_posts, 'open' );
	}

	$total_open   = count( $all_posts );
	$has_more_open = $total_open > $posts_per_page;
	$display_posts = array_slice( $all_posts, 0, $posts_per_page );
	$has_posts     = ! empty( $display_posts );

	$current_lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
	$is_arabic    = ( $current_lang === 'ar' || is_rtl() );
	$filter_label = ! empty( $settings['filter_label'] ) ? $settings['filter_label'] : null;

	$card_args = array(
		'background_type'    => $background_type,
		'background_image'   => $background_image,
		'background_overlay' => $background_overlay,
		'is_arabic'          => $is_arabic,
		'labels'             => $labels,
	);
	?>
	<div class="omsar-bd-recruitments-widget<?php echo $is_arabic ? ' omsar-bd-rtl' : ''; ?> <?php echo esc_attr( $wrapper_class_prefix ); ?>-<?php echo esc_attr( $element_id ); ?>"
		<?php if ( $is_arabic ) : ?>dir="rtl" <?php endif; ?>
		data-background-type="<?php echo esc_attr( $background_type ); ?>"
		data-background-image="<?php echo esc_attr( $background_image ); ?>"
		data-background-overlay="<?php echo esc_attr( $background_overlay ? 'yes' : 'no' ); ?>"
		data-orderby="<?php echo esc_attr( $orderby ); ?>"
		data-order="<?php echo esc_attr( $order ); ?>"
		data-columns="<?php echo esc_attr( $columns ); ?>"
		data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>">
		<div class="omsar-recruitments-filters" data-widget-id="<?php echo esc_attr( $element_id ); ?>" role="group" aria-label="<?php echo esc_attr( $labels['filter_aria'] ); ?>">
			<?php if ( ! empty( $filter_label ) ) : ?>
				<span class="omsar-recruitment-filter-label"><?php echo esc_html( $filter_label ); ?></span>
			<?php endif; ?>
			<div class="omsar-recruitment-status-tabs" role="tablist">
				<button type="button" class="omsar-recruitment-status-tab active" role="tab" aria-selected="true" data-status="open" data-widget-id="<?php echo esc_attr( $element_id ); ?>"><?php echo esc_html( $labels['open_tab'] ); ?></button>
				<button type="button" class="omsar-recruitment-status-tab" role="tab" aria-selected="false" data-status="upcoming" data-widget-id="<?php echo esc_attr( $element_id ); ?>"><?php echo esc_html( $labels['upcoming_tab'] ); ?></button>
				<button type="button" class="omsar-recruitment-status-tab" role="tab" aria-selected="false" data-status="closed" data-widget-id="<?php echo esc_attr( $element_id ); ?>"><?php echo esc_html( $labels['closed_tab'] ); ?></button>
			</div>
		</div>

		<div id="recruitments-cards-container" class="omsar-recruitments-grid" style="grid-template-columns: repeat(<?php echo esc_attr( $columns ); ?>, 1fr);" data-widget-id="<?php echo esc_attr( $element_id ); ?>">
			<?php if ( $has_posts ) : ?>
				<?php
				foreach ( $display_posts as $post ) {
					omsar_bd_render_recruitment_card( $post->ID, $card_args );
				}
				?>
			<?php else : ?>
				<div class="omsar-recruitment-item omsar-recruitment-empty-state omsar-recruitment-no-open-positions" data-status="open">
					<p class="omsar-recruitment-empty-message"><?php echo esc_html( $labels['no_open_message'] ); ?></p>
				</div>
			<?php endif; ?>
		</div>

		<div class="omsar-recruitment-load-more-wrapper"<?php echo $has_more_open ? '' : ' style="display:none;"'; ?>>
			<button type="button" class="omsar-recruitment-load-more-btn" data-page="1" data-status="open" data-total="<?php echo (int) $total_open; ?>" data-has-more="<?php echo $has_more_open ? '1' : '0'; ?>"><?php echo esc_html( $labels['load_more'] ); ?></button>
		</div>
	</div>
	<?php

	omsar_bd_render_recruitments_notify_modal();
}

