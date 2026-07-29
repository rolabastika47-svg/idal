<?php
/**
 * Breakdance Procurement Notices element rendering (shared with Elementor widget).
 *
 * @package OmsarBreakdanceElements
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default settings aligned with the Elementor Procurement Notices widget.
 *
 * @return array<string, mixed>
 */
function omsar_bd_procurement_notices_default_settings() {
	return array(
		'posts_per_page'   => 6,
		'orderby'          => 'date',
		'order'            => 'DESC',
		'columns'          => '3',
		'default_image'    => array(
			'url' => '',
			'id'  => 0,
		),
		'filter_label'     => '',
		'gradient_enabled' => false,
	);
}

/**
 * @param array<string, mixed> $settings Raw settings.
 * @return array<string, mixed>
 */
function omsar_bd_normalize_procurement_notices_settings( $settings ) {
	$defaults = omsar_bd_procurement_notices_default_settings();
	$settings = wp_parse_args( is_array( $settings ) ? $settings : array(), $defaults );

	$settings['posts_per_page'] = max( 1, (int) $settings['posts_per_page'] );
	$settings['columns']        = (string) ( $settings['columns'] ?? '3' );

	if ( isset( $settings['gradient_enabled'] ) && ( $settings['gradient_enabled'] === 'yes' || $settings['gradient_enabled'] === true ) ) {
		$settings['gradient_enabled'] = true;
	} else {
		$settings['gradient_enabled'] = ! empty( $settings['gradient_enabled'] );
	}

	if ( is_string( $settings['default_image'] ) ) {
		$settings['default_image'] = array( 'url' => $settings['default_image'] );
	}

	return $settings;
}

/**
 * @param array<string, mixed> $properties_data Breakdance properties.
 * @return array<string, mixed>
 */
function omsar_bd_procurement_notices_settings_from_breakdance( $properties_data ) {
	$content = $properties_data['content']['content'] ?? array();

	$default_image = $content['default_image'] ?? array();
	if ( is_string( $default_image ) ) {
		$default_image = array( 'url' => $default_image );
	}

	$settings = array(
		'posts_per_page'   => isset( $content['posts_per_page'] ) ? (int) $content['posts_per_page'] : 6,
		'orderby'          => $content['orderby'] ?? 'date',
		'order'            => $content['order'] ?? 'DESC',
		'columns'          => (string) ( $content['columns'] ?? '3' ),
		'default_image'    => $default_image,
		'filter_label'     => isset( $content['filter_label'] ) ? trim( (string) $content['filter_label'] ) : '',
		'gradient_enabled' => ! empty( $content['gradient_enabled'] ),
	);

	return omsar_bd_normalize_procurement_notices_settings( $settings );
}

/**
 * @param array<string, mixed> $settings Elementor widget settings.
 * @return array<string, mixed>
 */
function omsar_bd_procurement_notices_settings_from_elementor( $settings ) {
	$mapped = array(
		'posts_per_page'   => isset( $settings['posts_per_page'] ) ? (int) $settings['posts_per_page'] : 6,
		'orderby'          => $settings['orderby'] ?? 'date',
		'order'            => $settings['order'] ?? 'DESC',
		'columns'          => (string) ( $settings['columns'] ?? '3' ),
		'default_image'    => $settings['default_image'] ?? array(),
		'filter_label'     => isset( $settings['filter_label'] ) ? trim( (string) $settings['filter_label'] ) : '',
		'gradient_enabled' => isset( $settings['gradient_enabled'] ) && $settings['gradient_enabled'] === 'yes',
	);

	return omsar_bd_normalize_procurement_notices_settings( $mapped );
}

/**
 * @return array<string, string>
 */
function omsar_bd_procurement_notices_labels() {
	return array(
		'open'              => function_exists( 'pll__' ) ? pll__( 'Open' ) : __( 'Open', 'omsar' ),
		'closed'            => function_exists( 'pll__' ) ? pll__( 'Closed' ) : __( 'Closed', 'omsar' ),
		'cancelled'         => function_exists( 'pll__' ) ? pll__( 'Cancelled' ) : __( 'Cancelled', 'omsar' ),
		'apply'             => function_exists( 'pll__' ) ? pll__( 'Apply' ) : __( 'Apply', 'omsar' ),
		'load_more'         => function_exists( 'pll__' ) ? pll__( 'Load More' ) : __( 'Load More', 'omsar' ),
		'no_results'        => function_exists( 'pll__' ) ? pll__( 'No procurement notices are available at this time. Please check back later.' ) : __( 'No procurement notices are available at this time. Please check back later.', 'omsar' ),
		'filter_aria'       => function_exists( 'pll__' ) ? pll__( 'Filter by status' ) : __( 'Filter by status', 'omsar' ),
		'opening_date'      => function_exists( 'pll__' ) ? pll__( 'Opening Date' ) : __( 'Opening Date', 'omsar' ),
		'closing_date'      => function_exists( 'pll__' ) ? pll__( 'Closing Date' ) : __( 'Closing Date', 'omsar' ),
	);
}

/**
 * Format a procurement date field for display (d/m/Y).
 *
 * @param mixed $date_value Raw date value.
 * @return string
 */
function omsar_bd_format_procurement_display_date( $date_value ) {
	if ( empty( $date_value ) ) {
		return '';
	}

	$date_str = trim( (string) $date_value );

	if ( preg_match( '/^\d{1,2}\/\d{1,2}\/\d{4}$/', $date_str ) ) {
		$date_obj = DateTime::createFromFormat( 'd/m/Y', $date_str );
		return ( $date_obj !== false ) ? $date_obj->format( 'd/m/Y' ) : $date_str;
	}

	if ( is_numeric( $date_value ) && strlen( $date_str ) === 8 ) {
		$date_obj = DateTime::createFromFormat( 'Ymd', $date_str );
		if ( $date_obj !== false ) {
			return $date_obj->format( 'd/m/Y' );
		}
		return date( 'd/m/Y', (int) $date_value );
	}

	if ( is_numeric( $date_value ) && strlen( $date_str ) === 10 ) {
		return date( 'd/m/Y', (int) $date_value );
	}

	if ( is_numeric( $date_value ) ) {
		return date( 'd/m/Y', (int) $date_value );
	}

	$timestamp = strtotime( $date_str );
	if ( $timestamp !== false ) {
		return date( 'd/m/Y', $timestamp );
	}

	$date_obj = DateTime::createFromFormat( 'Y-m-d', $date_str );
	return ( $date_obj !== false ) ? $date_obj->format( 'd/m/Y' ) : $date_str;
}

/**
 * Truncate text to a word limit.
 *
 * @param string $text       Text to truncate.
 * @param int    $word_limit Word limit.
 * @return string
 */
function omsar_bd_truncate_procurement_words( $text, $word_limit = 10 ) {
	if ( empty( $text ) ) {
		return '';
	}

	$text = trim( strip_tags( $text ) );
	if ( $text === '' ) {
		return '';
	}

	$words = array_values(
		array_filter(
			explode( ' ', $text ),
			static function ( $word ) {
				return trim( $word ) !== '';
			}
		)
	);

	if ( count( $words ) > $word_limit ) {
		return implode( ' ', array_slice( $words, 0, $word_limit ) ) . '…';
	}

	return $text;
}

/**
 * Resolve default image URL from settings.
 *
 * @param array<string, mixed> $settings Widget settings.
 * @return string
 */
function omsar_bd_procurement_notices_default_image_url( $settings ) {
	$default_image = $settings['default_image'] ?? array();
	if ( is_array( $default_image ) ) {
		if ( ! empty( $default_image['url'] ) ) {
			return (string) $default_image['url'];
		}
		if ( ! empty( $default_image['id'] ) ) {
			$url = wp_get_attachment_url( (int) $default_image['id'] );
			return $url ? $url : '';
		}
	}
	return is_string( $default_image ) ? $default_image : '';
}

/**
 * Render a single procurement notice card.
 *
 * @param int                  $post_id  Post ID.
 * @param array<string, mixed> $args     Card arguments.
 */
function omsar_bd_render_procurement_notice_card( $post_id, $args = array() ) {
	$labels           = $args['labels'] ?? omsar_bd_procurement_notices_labels();
	$default_image    = $args['default_image'] ?? '';
	$gradient_enabled = ! empty( $args['gradient_enabled'] );

	$current_lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
	$is_arabic    = ( $current_lang === 'ar' || is_rtl() );

	$publication_custom_date    = '';
	$submission_deadline        = '';
	$procurement_description    = '';
	$procurement_description_ar = '';
	$procurement_title_ar       = '';

	if ( function_exists( 'get_field' ) ) {
		$publication_custom_date    = get_field( 'publication_custom_date', $post_id );
		$submission_deadline        = get_field( 'submission_deadline', $post_id );
		$procurement_description    = get_field( 'procurement_description', $post_id );
		$procurement_description_ar = get_field( 'procurement_description_ar', $post_id );
		$procurement_title_ar       = get_field( 'procurement_title_ar', $post_id );
	} else {
		$publication_custom_date    = get_post_meta( $post_id, 'publication_custom_date', true );
		$submission_deadline        = get_post_meta( $post_id, 'submission_deadline', true );
		$procurement_description    = get_post_meta( $post_id, 'procurement_description', true );
		$procurement_description_ar = get_post_meta( $post_id, 'procurement_description_ar', true );
		$procurement_title_ar       = get_post_meta( $post_id, 'procurement_title_ar', true );
	}

	$card_title = $is_arabic && ! empty( $procurement_title_ar ) ? $procurement_title_ar : get_the_title( $post_id );
	$description_raw = $is_arabic && ! empty( $procurement_description_ar ) ? $procurement_description_ar : $procurement_description;
	$description     = omsar_bd_truncate_procurement_words( $description_raw, 10 );

	$formatted_pub_date = omsar_bd_format_procurement_display_date( $publication_custom_date );
	$formatted_deadline = omsar_bd_format_procurement_display_date( $submission_deadline );

	$status = function_exists( 'omsar_get_procurement_status' )
		? omsar_get_procurement_status( $post_id )
		: 'closed';

	if ( $status === 'cancelled' ) {
		$status_label = $labels['cancelled'];
	} elseif ( $status === 'closed' ) {
		$status_label = $labels['closed'];
	} else {
		$status_label = $labels['open'];
	}

	$image_url = '';
	if ( has_post_thumbnail( $post_id ) ) {
		$image_url = get_the_post_thumbnail_url( $post_id, 'large' );
	} elseif ( ! empty( $default_image ) ) {
		$image_url = $default_image;
	}

	$card_style = $image_url ? 'background-image: url(' . esc_url( $image_url ) . ');' : '';

	$post_permalink = function_exists( 'omsar_get_procurement_single_page_url' )
		? omsar_get_procurement_single_page_url( $post_id )
		: get_permalink( $post_id );
	if ( ! $post_permalink ) {
		$post_permalink = get_permalink( $post_id );
	}

	$card_classes = 'omsar-procurement-card';
	if ( $is_arabic ) {
		$card_classes .= ' rtl-card';
	}
	?>
	<div class="<?php echo esc_attr( $card_classes ); ?>" <?php echo $card_style ? 'style="' . esc_attr( $card_style ) . '"' : ''; ?><?php echo $is_arabic ? ' dir="rtl"' : ''; ?>>
		<?php if ( $gradient_enabled ) : ?>
		<div class="omsar-procurement-card-gradient"></div>
		<?php endif; ?>

		<a href="<?php echo esc_url( $post_permalink ); ?>" class="omsar-procurement-card-link">
			<div class="omsar-procurement-card-content">
				<div class="omsar-procurement-card-header">
					<h3 class="omsar-procurement-card-title"><?php echo esc_html( $card_title ); ?></h3>
					<span class="omsar-procurement-status-badge omsar-status-<?php echo esc_attr( $status ); ?>">
						<?php echo esc_html( $status_label ); ?>
					</span>
				</div>

				<?php if ( $formatted_pub_date || $formatted_deadline ) : ?>
				<div class="omsar-procurement-card-dates">
					<div class="omsar-procurement-date-range">
						<?php if ( $formatted_pub_date ) : ?>
							<div class="omsar-procurement-date-row">
								<i class="bi bi-calendar2-check" aria-hidden="true"></i>
								<span class="omsar-procurement-date-label"><?php echo esc_html( $labels['opening_date'] ); ?>:</span>
								<span class="omsar-procurement-date-value"><?php echo esc_html( $formatted_pub_date ); ?></span>
							</div>
						<?php endif; ?>
						<?php if ( $formatted_deadline ) : ?>
							<div class="omsar-procurement-date-row">
								<i class="bi bi-calendar2-x" aria-hidden="true"></i>
								<span class="omsar-procurement-date-label"><?php echo esc_html( $labels['closing_date'] ); ?>:</span>
								<span class="omsar-procurement-date-value"><?php echo esc_html( $formatted_deadline ); ?></span>
							</div>
						<?php endif; ?>
					</div>
				</div>
				<?php endif; ?>

				<?php if ( ! empty( $description ) ) : ?>
				<div class="omsar-procurement-card-description">
					<span class="omsar-procurement-description-info">
						<?php echo esc_html( $description ); ?>
					</span>
				</div>
				<?php endif; ?>
			</div>
		</a>
		<?php
		if ( function_exists( 'omsar_render_procurement_apply_button' ) ) {
			omsar_render_procurement_apply_button( $post_id, $labels['apply'], $status, 'card' );
		}
		?>
	</div>
	<?php
}

/**
 * Render the Procurement Notices widget.
 *
 * @param array<string, mixed> $settings              Widget settings.
 * @param string               $element_id            Unique element ID.
 * @param string               $wrapper_class_prefix  Wrapper class prefix (elementor-element or bde-element).
 */
function omsar_bd_render_procurement_notices_widget( $settings, $element_id, $wrapper_class_prefix = 'bde-element' ) {
	$settings       = omsar_bd_normalize_procurement_notices_settings( $settings );
	$labels         = omsar_bd_procurement_notices_labels();
	$posts_per_page = (int) $settings['posts_per_page'];
	$orderby        = $settings['orderby'];
	$order          = $settings['order'];
	$columns        = $settings['columns'];
	$default_image  = omsar_bd_procurement_notices_default_image_url( $settings );
	$gradient_enabled = (bool) $settings['gradient_enabled'];
	$filter_label   = $settings['filter_label'] !== '' ? $settings['filter_label'] : null;

	$procurement_query = new WP_Query(
		array(
			'post_type'      => 'procurement_notices',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'no_found_rows'  => true,
		)
	);

	$all_posts = $procurement_query->posts;
	if ( ! empty( $all_posts ) && function_exists( 'omsar_sort_procurements_by_status_priority' ) ) {
		$all_posts = omsar_sort_procurements_by_status_priority( $all_posts );
	}
	$total_in_db = count( $all_posts );

	if ( $total_in_db <= 0 ) {
		?>
		<div class="omsar-procurement-notices-widget omsar-bd-procurement-notices-widget <?php echo esc_attr( $wrapper_class_prefix ); ?>-<?php echo esc_attr( $element_id ); ?>">
			<div class="omsar-procurement-no-results">
				<p class="omsar-procurement-no-results-message"><?php echo esc_html( $labels['no_results'] ); ?></p>
			</div>
		</div>
		<?php
		return;
	}

	$open_posts = function_exists( 'omsar_filter_procurements_by_status' )
		? omsar_filter_procurements_by_status( $all_posts, 'open' )
		: $all_posts;
	$total_open   = count( $open_posts );
	$display_posts = array_slice( $open_posts, 0, $posts_per_page );
	$has_more_open = $total_open > $posts_per_page;
	$has_posts     = ! empty( $display_posts );

	$current_lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
	$is_arabic    = ( $current_lang === 'ar' || is_rtl() );

	$card_args = array(
		'default_image'    => $default_image,
		'gradient_enabled' => $gradient_enabled,
		'labels'           => $labels,
	);
	?>
	<div class="omsar-procurement-notices-widget omsar-bd-procurement-notices-widget<?php echo $is_arabic ? ' omsar-bd-rtl' : ''; ?> <?php echo esc_attr( $wrapper_class_prefix ); ?>-<?php echo esc_attr( $element_id ); ?>"
		<?php if ( $is_arabic ) : ?>dir="rtl" <?php endif; ?>
		data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>"
		data-orderby="<?php echo esc_attr( $orderby ); ?>"
		data-order="<?php echo esc_attr( $order ); ?>"
		data-columns="<?php echo esc_attr( $columns ); ?>"
		data-default-image="<?php echo esc_attr( $default_image ); ?>"
		data-gradient-enabled="<?php echo esc_attr( $gradient_enabled ? 'yes' : 'no' ); ?>">

		<div class="omsar-procurement-filters" data-widget-id="<?php echo esc_attr( $element_id ); ?>" role="group" aria-label="<?php echo esc_attr( $labels['filter_aria'] ); ?>">
			<label class="omsar-procurement-filter-label"><?php echo esc_html( $filter_label ?? '' ); ?></label>
			<div class="omsar-procurement-status-tabs" role="tablist">
				<button type="button" class="omsar-procurement-status-tab active" role="tab" aria-selected="true" data-status="open" data-widget-id="<?php echo esc_attr( $element_id ); ?>"><?php echo esc_html( $labels['open'] ); ?></button>
				<button type="button" class="omsar-procurement-status-tab" role="tab" aria-selected="false" data-status="cancelled" data-widget-id="<?php echo esc_attr( $element_id ); ?>"><?php echo esc_html( $labels['cancelled'] ); ?></button>
				<button type="button" class="omsar-procurement-status-tab" role="tab" aria-selected="false" data-status="closed" data-widget-id="<?php echo esc_attr( $element_id ); ?>"><?php echo esc_html( $labels['closed'] ); ?></button>
			</div>
		</div>

		<div id="procurement-cards-container" class="omsar-procurement-notices-grid" style="grid-template-columns: repeat(<?php echo esc_attr( $columns ); ?>, 1fr); --omsar-procurement-columns: <?php echo esc_attr( $columns ); ?>;" data-widget-id="<?php echo esc_attr( $element_id ); ?>">
			<?php
			if ( $has_posts ) :
				foreach ( $display_posts as $post ) :
					setup_postdata( $post );
					omsar_bd_render_procurement_notice_card( $post->ID, $card_args );
				endforeach;
				wp_reset_postdata();
			else :
				echo '<div class="omsar-procurement-no-results"><p class="omsar-procurement-no-results-message">' . esc_html( $labels['no_results'] ) . '</p></div>';
			endif;
			?>
		</div>

		<div class="omsar-procurement-load-more-wrapper"<?php echo $has_more_open ? '' : ' style="display:none;"'; ?>>
			<button type="button" class="omsar-procurement-load-more-btn" data-page="1" data-status="open" data-total="<?php echo (int) $total_open; ?>" data-has-more="<?php echo $has_more_open ? '1' : '0'; ?>"><?php echo esc_html( $labels['load_more'] ); ?></button>
		</div>
	</div>

	<?php
	if ( function_exists( 'omsar_procurement_render_apply_modal_once' ) ) {
		omsar_procurement_render_apply_modal_once();
	}
}
