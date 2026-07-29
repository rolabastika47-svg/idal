<?php
/**
 * Breakdance Posts by Taxonomy element rendering.
 *
 * @package OmsarBreakdanceElements
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default settings for Breakdance Posts by Taxonomy element.
 *
 * @return array<string, mixed>
 */
function omsar_bd_posts_by_taxonomy_default_settings() {
	return array(
		'taxonomy'                => '',
		'use_acf_field'           => false,
		'acf_field_name'          => 'publication_category',
		'post_type'               => 'post',
		'show_empty_terms'        => false,
		'show_all_tab'            => true,
		'hide_tabs_navigation'    => false,
		'posts_per_page'          => 6,
		'enable_load_more'        => false,
		'load_more_text'          => function_exists( 'pll__' ) ? pll__( 'Load More' ) : __( 'Load More', 'omsar' ),
		'orderby'                 => 'date',
		'order'                   => 'DESC',
		'enable_search'           => false,
		'search_placeholder'      => __( 'Search posts...', 'omsar' ),
		'search_label'            => '',
		'search_alignment'        => 'left',
		'listing_style'           => 'style1',
		'show_date'               => true,
		'default_image'           => array(
			'url' => '',
		),
		'clickable_cards'         => true,
		'columns'                 => '3',
		'style5_content_overlay'  => false,
		'style5_side_layout'      => false,
		'style5_show_categories'  => true,
		'style5_show_excerpt'     => true,
		'style5_show_date'        => true,
		'style5_show_author'      => true,
		'custom_field_meta_key'   => '',
		'custom_field_meta_value' => '',
	);
}

/**
 * Normalize settings into Elementor-compatible value shapes.
 *
 * @param array<string, mixed> $settings Raw settings.
 * @return array<string, mixed>
 */
function omsar_bd_normalize_posts_by_taxonomy_settings( $settings ) {
	$defaults = omsar_bd_posts_by_taxonomy_default_settings();
	$settings = wp_parse_args( is_array( $settings ) ? $settings : array(), $defaults );

	$toggle_keys = array(
		'use_acf_field',
		'show_empty_terms',
		'show_all_tab',
		'hide_tabs_navigation',
		'enable_load_more',
		'enable_search',
		'show_date',
		'clickable_cards',
		'style5_content_overlay',
		'style5_side_layout',
		'style5_show_categories',
		'style5_show_excerpt',
		'style5_show_date',
		'style5_show_author',
	);

	foreach ( $toggle_keys as $key ) {
		$value             = $settings[ $key ] ?? 'no';
		$settings[ $key ]  = ( $value === 'yes' || $value === true || $value === 1 || $value === '1' ) ? 'yes' : 'no';
	}

	if ( isset( $settings['default_image'] ) && is_string( $settings['default_image'] ) ) {
		$settings['default_image'] = array( 'url' => $settings['default_image'] );
	} elseif ( ! isset( $settings['default_image'] ) || ! is_array( $settings['default_image'] ) ) {
		$settings['default_image'] = array( 'url' => '' );
	}

	if ( ! isset( $settings['default_image']['url'] ) ) {
		$settings['default_image']['url'] = '';
	}

	$settings['posts_per_page'] = isset( $settings['posts_per_page'] ) ? (int) $settings['posts_per_page'] : 6;
	$settings['columns']        = isset( $settings['columns'] ) ? (int) $settings['columns'] : 3;

	if ( $settings['columns'] < 1 ) {
		$settings['columns'] = 3;
	}

	return $settings;
}

/**
 * Map Breakdance propertiesData to normalized settings array.
 *
 * @param array<string, mixed> $properties_data Breakdance properties.
 * @return array<string, mixed>
 */
function omsar_bd_posts_by_taxonomy_settings_from_breakdance( $properties_data ) {
	$content = $properties_data['content']['content'] ?? array();
	$design  = $properties_data['design']['design'] ?? $properties_data['design'] ?? array();

	$default_image = $content['default_image'] ?? '';
	if ( is_string( $default_image ) ) {
		$default_image = array( 'url' => $default_image );
	}

	$settings = array(
		'taxonomy'                => $content['taxonomy'] ?? '',
		'use_acf_field'           => $content['use_acf_field'] ?? 'no',
		'acf_field_name'          => $content['acf_field_name'] ?? 'publication_category',
		'post_type'               => $content['post_type'] ?? 'post',
		'show_empty_terms'        => $content['show_empty_terms'] ?? 'no',
		'show_all_tab'            => $content['show_all_tab'] ?? 'yes',
		'hide_tabs_navigation'    => $content['hide_tabs_navigation'] ?? 'no',
		'posts_per_page'          => isset( $content['posts_per_page'] ) ? (int) $content['posts_per_page'] : 6,
		'enable_load_more'        => $content['enable_load_more'] ?? 'no',
		'load_more_text'          => $content['load_more_text'] ?? esc_html__( 'Load More', 'omsar' ),
		'orderby'                 => $content['orderby'] ?? 'date',
		'order'                   => $content['order'] ?? 'DESC',
		'enable_search'           => $content['enable_search'] ?? 'no',
		'search_placeholder'      => isset( $content['search_placeholder'] ) ? (string) $content['search_placeholder'] : __( 'Search posts...', 'omsar' ),
		'search_label'            => isset( $content['search_label'] ) ? (string) $content['search_label'] : '',
		'search_alignment'        => $design['search']['search_alignment'] ?? $content['search_alignment'] ?? 'left',
		'listing_style'           => $content['listing_style'] ?? 'style1',
		'show_date'               => $content['show_date'] ?? 'yes',
		'default_image'           => $default_image,
		'clickable_cards'         => $content['clickable_cards'] ?? 'yes',
		'columns'                 => isset( $content['columns'] ) ? (int) $content['columns'] : 3,
		'style5_content_overlay'  => $content['style5_content_overlay'] ?? 'no',
		'style5_side_layout'      => $content['style5_side_layout'] ?? 'no',
		'style5_show_categories'  => $content['style5_show_categories'] ?? 'yes',
		'style5_show_excerpt'     => $content['style5_show_excerpt'] ?? 'yes',
		'style5_show_date'        => $content['style5_show_date'] ?? 'yes',
		'style5_show_author'      => $content['style5_show_author'] ?? 'yes',
		'custom_field_meta_key'   => $content['custom_field_meta_key'] ?? '',
		'custom_field_meta_value' => $content['custom_field_meta_value'] ?? '',
	);

	return omsar_bd_normalize_posts_by_taxonomy_settings( $settings );
}

/**
 * Filter posts by custom field (post meta).
 *
 * @param array<int, WP_Post> $posts      Array of post objects.
 * @param string              $meta_key   Meta key to filter by.
 * @param string              $meta_value Meta value to match.
 * @return array<int, WP_Post>
 */
function omsar_bd_filter_posts_by_taxonomy_custom_field( $posts, $meta_key, $meta_value ) {
	if ( empty( $meta_key ) || empty( $meta_value ) ) {
		return $posts;
	}

	$filtered_posts = array();
	foreach ( $posts as $post ) {
		$post_meta_value = get_post_meta( $post->ID, $meta_key, true );

		// Handle different meta value types.
		if ( is_array( $post_meta_value ) ) {
			// If meta value is array, check if meta_value is in array.
			if ( in_array( $meta_value, $post_meta_value, true ) ) {
				$filtered_posts[] = $post;
			}
		} else {
			// Direct comparison.
			if ( (string) $post_meta_value === (string) $meta_value ) {
				$filtered_posts[] = $post;
			}
		}
	}

	return $filtered_posts;
}

/**
 * Render widget output on the frontend.
 *
 * @param array<string, mixed> $settings            Widget settings.
 * @param string               $element_id          Unique element ID.
 * @param string               $wrapper_extra_class Extra wrapper class.
 */
function omsar_bd_render_posts_by_taxonomy_widget( $settings, $element_id, $wrapper_extra_class = '' ) {
	$settings = omsar_bd_normalize_posts_by_taxonomy_settings( $settings );

	$taxonomy                = ! empty( $settings['taxonomy'] ) ? $settings['taxonomy'] : '';
	$post_type               = ! empty( $settings['post_type'] ) ? $settings['post_type'] : 'post';
	$show_empty              = $settings['show_empty_terms'] === 'yes';
	$show_all                = $settings['show_all_tab'] === 'yes';
	$posts_per_page          = ! empty( $settings['posts_per_page'] ) ? (int) $settings['posts_per_page'] : -1;
	$orderby                 = ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'date';
	$order                   = ! empty( $settings['order'] ) ? $settings['order'] : 'DESC';
	$use_acf                 = $settings['use_acf_field'] === 'yes';
	$acf_field_name          = ! empty( $settings['acf_field_name'] ) ? $settings['acf_field_name'] : 'publication_category';
	$enable_search           = $settings['enable_search'] === 'yes';
	$search_placeholder      = isset( $settings['search_placeholder'] ) ? trim( (string) $settings['search_placeholder'] ) : '';
	if ( $search_placeholder === '' ) {
		$search_placeholder = __( 'Search posts...', 'omsar' );
	}
	$search_label            = isset( $settings['search_label'] ) ? trim( (string) $settings['search_label'] ) : '';
	$search_aria_label       = $search_label !== '' ? $search_label : $search_placeholder;
	$search_alignment        = ! empty( $settings['search_alignment'] ) ? $settings['search_alignment'] : 'left';
	$listing_style           = ! empty( $settings['listing_style'] ) ? $settings['listing_style'] : 'style1';
	$clickable_cards         = isset( $settings['clickable_cards'] ) && $settings['clickable_cards'] === 'yes';
	$enable_load_more        = isset( $settings['enable_load_more'] ) && $settings['enable_load_more'] === 'yes';
	$load_more_text          = ! empty( $settings['load_more_text'] ) ? $settings['load_more_text'] : esc_html__( 'Load More', 'omsar' );
	$default_image           = isset( $settings['default_image']['url'] ) ? $settings['default_image']['url'] : '';
	$show_date               = isset( $settings['show_date'] ) && $settings['show_date'] === 'yes';
	$hide_tabs               = isset( $settings['hide_tabs_navigation'] ) && $settings['hide_tabs_navigation'] === 'yes';
	$columns                 = ! empty( $settings['columns'] ) ? max( 1, (int) $settings['columns'] ) : 3;
	$grid_column_style       = sprintf( '--omsar-grid-columns: %1$d; grid-template-columns: repeat(%1$d, 1fr);', $columns );
	$custom_field_meta_key   = ! empty( $settings['custom_field_meta_key'] ) ? $settings['custom_field_meta_key'] : '';
	$custom_field_meta_value = ! empty( $settings['custom_field_meta_value'] ) ? $settings['custom_field_meta_value'] : '';

	// Style 5 display options.
	$style5_content_overlay = isset( $settings['style5_content_overlay'] ) && $settings['style5_content_overlay'] === 'yes';
	$style5_side_layout     = isset( $settings['style5_side_layout'] ) && $settings['style5_side_layout'] === 'yes';
	$style5_show_categories = isset( $settings['style5_show_categories'] ) && $settings['style5_show_categories'] === 'yes';
	$style5_show_excerpt    = isset( $settings['style5_show_excerpt'] ) && $settings['style5_show_excerpt'] === 'yes';
	$style5_show_date       = isset( $settings['style5_show_date'] ) && $settings['style5_show_date'] === 'yes';
	$style5_show_author     = isset( $settings['style5_show_author'] ) && $settings['style5_show_author'] === 'yes';

	// If taxonomy is empty, automatically hide tabs and show all posts.
	if ( empty( $taxonomy ) ) {
		$hide_tabs = true;
	}

	// Get all terms from the selected taxonomy (for tabs) - only if taxonomy is set and tabs are not hidden.
	$terms = array();
	if ( ! empty( $taxonomy ) && ! $hide_tabs ) {
		$term_args = array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => ! $show_empty,
			'orderby'    => 'name',
			'order'      => 'ASC',
		);

		// If Polylang is active, get terms for current language.
		// But also include translations to ensure we can match posts correctly.
		if ( function_exists( 'pll_current_language' ) ) {
			$current_lang = pll_current_language();
			if ( $current_lang ) {
				$term_args['lang'] = $current_lang;
			}
		}

		$terms = get_terms( $term_args );

		if ( is_wp_error( $terms ) ) {
			echo '<p>' . esc_html__( 'Error retrieving taxonomy terms: ', 'omsar' ) . esc_html( $terms->get_error_message() ) . '</p>';
			return;
		}

		if ( empty( $terms ) && ! $show_empty ) {
			echo '<p>' . esc_html__( 'No taxonomy terms found for taxonomy: ', 'omsar' ) . esc_html( $taxonomy ) . '</p>';
			return;
		}
	}

	// If using ACF, we need to check which terms actually have posts assigned via ACF.
	// Only process if we have taxonomy and not hiding tabs.
	if ( ! empty( $taxonomy ) && ! $hide_tabs && $use_acf && function_exists( 'get_field' ) ) {
		// Get all posts of the post type.
		$all_posts_check = get_posts(
			array(
				'post_type'      => $post_type,
				'posts_per_page' => -1,
				'post_status'    => 'publish',
			)
		);

		// Collect unique category values from ACF fields.
		$acf_categories_found = array();
		foreach ( $all_posts_check as $post_check ) {
			$acf_category = get_field( $acf_field_name, $post_check->ID );
			if ( ! empty( $acf_category ) ) {
				if ( is_array( $acf_category ) ) {
					foreach ( $acf_category as $cat ) {
						$cat_name = is_array( $cat ) ? ( $cat['label'] ?? $cat['value'] ?? '' ) : $cat;
						if ( ! empty( $cat_name ) && ! in_array( $cat_name, $acf_categories_found, true ) ) {
							$acf_categories_found[] = $cat_name;
						}
					}
				} else {
					$cat_name = is_array( $acf_category ) ? ( $acf_category['label'] ?? $acf_category['value'] ?? '' ) : $acf_category;
					if ( ! empty( $cat_name ) && ! in_array( $cat_name, $acf_categories_found, true ) ) {
						$acf_categories_found[] = $cat_name;
					}
				}
			}
		}

		// Filter terms to only include those that match ACF categories or show all if show_empty is enabled.
		if ( ! $show_empty ) {
			$terms = array_filter(
				$terms,
				function ( $term ) use ( $acf_categories_found ) {
					return in_array( $term->name, $acf_categories_found, true );
				}
			);
			$terms = array_values( $terms ); // Re-index array.
		}
	}

	// Get all posts for "All" tab or when tabs are hidden.
	// If load more is enabled, get all posts first, then limit display.
	$all_posts = array();
	if ( $show_all || $hide_tabs || empty( $taxonomy ) ) {
		$all_posts_args = array(
			'post_type'         => $post_type,
			'posts_per_page'    => $enable_load_more ? -1 : $posts_per_page,
			'post_status'       => 'publish',
			'orderby'           => $orderby,
			'order'             => $order,
			'suppress_filters'  => false,
			'no_found_rows'     => true,
		);

		// Add meta query if custom field filter is set.
		if ( ! empty( $custom_field_meta_key ) && ! empty( $custom_field_meta_value ) ) {
			$all_posts_args['meta_query'] = array(
				array(
					'key'     => $custom_field_meta_key,
					'value'   => $custom_field_meta_value,
					'compare' => '=',
				),
			);
		}

		// Use WP_Query for better Polylang support.
		$all_posts_query_obj = new WP_Query( $all_posts_args );
		$all_posts           = $all_posts_query_obj->posts;

		// Additional filtering for array meta values (meta_query doesn't handle arrays well).
		if ( ! empty( $custom_field_meta_key ) && ! empty( $custom_field_meta_value ) ) {
			$all_posts = omsar_bd_filter_posts_by_taxonomy_custom_field( $all_posts, $custom_field_meta_key, $custom_field_meta_value );
		}
	}

	$widget_id    = 'omsar-posts-taxonomy-' . $element_id;
	$current_lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
	$is_arabic    = ( $current_lang === 'ar' || is_rtl() );
	?>
	<div class="omsar-posts-by-taxonomy-widget omsar-bd-posts-by-taxonomy-widget<?php echo $is_arabic ? ' omsar-bd-rtl' : ''; ?> <?php echo esc_attr( $wrapper_extra_class ); ?>" id="<?php echo esc_attr( $widget_id ); ?>"<?php if ( $is_arabic ) : ?> dir="rtl"<?php endif; ?>>
		<?php if ( $enable_search ) : ?>
		<!-- Search Box (for non-Style 4) -->
		<div class="omsar-posts-search-wrapper omsar-search-align-<?php echo esc_attr( $search_alignment ); ?>">
			<?php if ( $search_label !== '' ) : ?>
				<label for="<?php echo esc_attr( $widget_id ); ?>-search" class="omsar-posts-search-label"><?php echo esc_html( $search_label ); ?></label>
			<?php endif; ?>
			<div class="omsar-posts-search-field">
				<input type="text"
					class="omsar-posts-search-input"
					id="<?php echo esc_attr( $widget_id ); ?>-search"
					placeholder="<?php echo esc_attr( $search_placeholder ); ?>"
					aria-label="<?php echo esc_attr( $search_aria_label ); ?>">
				<i class="bi bi-search omsar-search-icon" aria-hidden="true"></i>
			</div>
		</div>
		<?php endif; ?>

		<!-- Tabs Navigation -->
		<?php if ( ! $hide_tabs && ! empty( $taxonomy ) && ! empty( $terms ) ) : ?>
		<div class="omsar-taxonomy-tabs-nav">
			<ul class="nav nav-tabs omsar-taxonomy-tabs" role="tablist">
				<?php if ( $show_all && ! empty( $all_posts ) ) : ?>
					<li class="nav-item" role="presentation">
						<button class="nav-link active" id="<?php echo esc_attr( $widget_id ); ?>-all-tab"
								data-bs-toggle="tab"
								data-bs-target="#<?php echo esc_attr( $widget_id ); ?>-all"
								type="button"
								role="tab">
							<?php echo function_exists( 'pll__' ) ? pll__( 'All' ) : esc_html__( 'All', 'omsar' ); ?>
						</button>
					</li>
				<?php endif; ?>

				<?php foreach ( $terms as $index => $term ) : ?>
					<?php
					$term_slug = sanitize_title( $term->slug );
					$is_first  = ( $index === 0 && ( ! $show_all || empty( $all_posts ) ) );
					?>
					<li class="nav-item" role="presentation">
						<button class="nav-link <?php echo $is_first ? 'active' : ''; ?>"
								id="<?php echo esc_attr( $widget_id ); ?>-<?php echo esc_attr( $term_slug ); ?>-tab"
								data-bs-toggle="tab"
								data-bs-target="#<?php echo esc_attr( $widget_id ); ?>-<?php echo esc_attr( $term_slug ); ?>"
								type="button"
								role="tab">
							<?php echo esc_html( $term->name ); ?>
						</button>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php endif; ?>

		<!-- Tabs Content -->
		<?php if ( $hide_tabs || empty( $taxonomy ) || empty( $terms ) ) : ?>
			<!-- Show all posts directly when tabs are hidden -->
			<?php
			// When tabs are hidden, show all posts.
			$displayed_all_posts = $enable_load_more && $posts_per_page > 0 ? array_slice( $all_posts, 0, $posts_per_page ) : $all_posts;
			$has_more_all_posts  = $enable_load_more && $posts_per_page > 0 && count( $all_posts ) > $posts_per_page;
			?>
			<div class="omsar-posts-grid <?php echo esc_attr( $listing_style ); ?>"
				style="<?php echo esc_attr( $grid_column_style ); ?>"
				data-tab="all"
				data-widget-id="<?php echo esc_attr( $widget_id ); ?>"
				data-post-type="<?php echo esc_attr( $post_type ); ?>"
				data-taxonomy="<?php echo esc_attr( $taxonomy ); ?>"
				data-use-acf="<?php echo $use_acf ? '1' : '0'; ?>"
				data-acf-field="<?php echo esc_attr( $acf_field_name ); ?>"
				data-listing-style="<?php echo esc_attr( $listing_style ); ?>"
				data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>"
				data-orderby="<?php echo esc_attr( $orderby ); ?>"
				data-order="<?php echo esc_attr( $order ); ?>"
				data-default-image="<?php echo esc_url( $default_image ); ?>"
				data-custom-field-meta-key="<?php echo esc_attr( $custom_field_meta_key ); ?>"
				data-custom-field-meta-value="<?php echo esc_attr( $custom_field_meta_value ); ?>"
				data-columns="<?php echo esc_attr( $columns ); ?>"
				data-style5-show-categories="<?php echo $style5_show_categories ? '1' : '0'; ?>"
				data-style5-show-excerpt="<?php echo $style5_show_excerpt ? '1' : '0'; ?>"
				data-style5-show-date="<?php echo $style5_show_date ? '1' : '0'; ?>"
				data-style5-show-author="<?php echo $style5_show_author ? '1' : '0'; ?>"
				data-style5-content-overlay="<?php echo $style5_content_overlay ? '1' : '0'; ?>"
				data-style5-side-layout="<?php echo $style5_side_layout ? '1' : '0'; ?>"
				data-clickable-cards="<?php echo $clickable_cards ? '1' : '0'; ?>"
				data-current-page="1"
				data-total-posts="<?php echo count( $all_posts ); ?>">
				<?php foreach ( $displayed_all_posts as $post ) : ?>
					<?php
					setup_postdata( $post );
					omsar_bd_render_posts_by_taxonomy_card( $post, $widget_id, $listing_style, $clickable_cards, $default_image, $show_date, $style5_show_categories, $style5_show_excerpt, $style5_show_date, $style5_show_author, $style5_content_overlay, $style5_side_layout );
					?>
				<?php endforeach; ?>
				<?php wp_reset_postdata(); ?>
			</div>
			<?php if ( $enable_load_more && $has_more_all_posts ) : ?>
				<div class="omsar-load-more-wrapper" data-tab="all">
					<button class="omsar-load-more-btn" data-widget-id="<?php echo esc_attr( $widget_id ); ?>" data-tab="all">
						<?php echo esc_html( $load_more_text ); ?>
					</button>
				</div>
			<?php endif; ?>
		<?php else : ?>
		<div class="tab-content omsar-taxonomy-tabs-content">
			<!-- All Posts Tab -->
			<?php if ( $show_all && ! empty( $all_posts ) ) : ?>
				<?php
				// Limit posts if load more is enabled.
				$displayed_all_posts = $enable_load_more && $posts_per_page > 0 ? array_slice( $all_posts, 0, $posts_per_page ) : $all_posts;
				$has_more_all_posts  = $enable_load_more && $posts_per_page > 0 && count( $all_posts ) > $posts_per_page;
				?>
				<div class="tab-pane fade show active"
					id="<?php echo esc_attr( $widget_id ); ?>-all"
					role="tabpanel">
					<div class="omsar-posts-grid <?php echo esc_attr( $listing_style ); ?>"
						style="<?php echo esc_attr( $grid_column_style ); ?>"
						data-tab="all"
						data-widget-id="<?php echo esc_attr( $widget_id ); ?>"
						data-post-type="<?php echo esc_attr( $post_type ); ?>"
						data-taxonomy="<?php echo esc_attr( $taxonomy ); ?>"
						data-use-acf="<?php echo $use_acf ? '1' : '0'; ?>"
						data-acf-field="<?php echo esc_attr( $acf_field_name ); ?>"
						data-listing-style="<?php echo esc_attr( $listing_style ); ?>"
						data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>"
						data-orderby="<?php echo esc_attr( $orderby ); ?>"
						data-order="<?php echo esc_attr( $order ); ?>"
						data-default-image="<?php echo esc_url( $default_image ); ?>"
						data-custom-field-meta-key="<?php echo esc_attr( $custom_field_meta_key ); ?>"
						data-custom-field-meta-value="<?php echo esc_attr( $custom_field_meta_value ); ?>"
						data-columns="<?php echo esc_attr( $columns ); ?>"
						data-style5-show-categories="<?php echo $style5_show_categories ? '1' : '0'; ?>"
						data-style5-show-excerpt="<?php echo $style5_show_excerpt ? '1' : '0'; ?>"
						data-style5-show-date="<?php echo $style5_show_date ? '1' : '0'; ?>"
						data-style5-show-author="<?php echo $style5_show_author ? '1' : '0'; ?>"
						data-style5-content-overlay="<?php echo $style5_content_overlay ? '1' : '0'; ?>"
						data-style5-side-layout="<?php echo $style5_side_layout ? '1' : '0'; ?>"
						data-clickable-cards="<?php echo $clickable_cards ? '1' : '0'; ?>"
						data-current-page="1"
						data-total-posts="<?php echo count( $all_posts ); ?>">
						<?php foreach ( $displayed_all_posts as $post ) : ?>
							<?php
							setup_postdata( $post );
							omsar_bd_render_posts_by_taxonomy_card( $post, $widget_id, $listing_style, $clickable_cards, $default_image, $show_date, $style5_show_categories, $style5_show_excerpt, $style5_show_date, $style5_show_author, $style5_content_overlay, $style5_side_layout );
							?>
						<?php endforeach; ?>
						<?php wp_reset_postdata(); ?>
					</div>
					<?php if ( $enable_load_more && $has_more_all_posts ) : ?>
						<div class="omsar-load-more-wrapper" data-tab="all">
							<button class="omsar-load-more-btn" data-widget-id="<?php echo esc_attr( $widget_id ); ?>" data-tab="all">
								<?php echo esc_html( $load_more_text ); ?>
							</button>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<!-- Term Tabs -->
			<?php foreach ( $terms as $index => $term ) : ?>
				<?php
				$term_slug = sanitize_title( $term->slug );
				$is_first  = ( $index === 0 && ( ! $show_all || empty( $all_posts ) ) );

				// Get posts for this term.
				if ( $use_acf && function_exists( 'get_field' ) ) {
					// Filter by ACF field value matching term ID.
					$all_posts_for_filter_args = array(
						'post_type'      => $post_type,
						'posts_per_page' => -1,
						'post_status'    => 'publish',
					);

					// Add meta query if custom field filter is set.
					if ( ! empty( $custom_field_meta_key ) && ! empty( $custom_field_meta_value ) ) {
						$all_posts_for_filter_args['meta_query'] = array(
							array(
								'key'     => $custom_field_meta_key,
								'value'   => $custom_field_meta_value,
								'compare' => '=',
							),
						);
					}

					$all_posts_for_filter = get_posts( $all_posts_for_filter_args );

					$term_posts = array();
					foreach ( $all_posts_for_filter as $post_filter ) {
						$acf_category = get_field( $acf_field_name, $post_filter->ID );
						$matches      = false;

						if ( ! empty( $acf_category ) ) {
							// Handle taxonomy term object from ACF.
							if ( is_object( $acf_category ) && isset( $acf_category->term_id ) ) {
								// ACF returns taxonomy term object - compare by term_id.
								if ( $acf_category->term_id == $term->term_id ) {
									$matches = true;
								}
							} elseif ( is_array( $acf_category ) ) {
								foreach ( $acf_category as $cat ) {
									// Handle term object.
									if ( is_object( $cat ) && isset( $cat->term_id ) ) {
										if ( $cat->term_id == $term->term_id ) {
											$matches = true;
											break;
										}
									} elseif ( is_object( $cat ) && isset( $cat->name ) ) {
										// Fallback: compare by name.
										if ( $cat->name === $term->name || $cat->term_id == $term->term_id ) {
											$matches = true;
											break;
										}
									} else {
										// Handle array or string - could be term ID or name.
										$cat_value = is_array( $cat ) ? ( $cat['value'] ?? $cat['label'] ?? '' ) : $cat;
										// Compare as term ID (numeric) or name (string).
										if ( $cat_value == $term->term_id || $cat_value === $term->name ) {
											$matches = true;
											break;
										}
									}
								}
							} else {
								// Handle string/integer - ACF field stores term ID as number.
								// Compare directly with term_id (most common case).
								if ( $acf_category == $term->term_id ) {
									$matches = true;
								} elseif ( is_object( $acf_category ) && isset( $acf_category->term_id ) ) {
									// Fallback: term object.
									if ( $acf_category->term_id == $term->term_id ) {
										$matches = true;
									}
								} else {
									// Last resort: compare as string/name.
									$cat_value = is_array( $acf_category ) ? ( $acf_category['value'] ?? $acf_category['label'] ?? '' ) : $acf_category;
									if ( $cat_value == $term->term_id || $cat_value === $term->name ) {
										$matches = true;
									}
								}
							}
						}

						if ( $matches ) {
							$term_posts[] = $post_filter;
						}
					}

					// Apply custom field filter if set.
					if ( ! empty( $custom_field_meta_key ) && ! empty( $custom_field_meta_value ) ) {
						$term_posts = omsar_bd_filter_posts_by_taxonomy_custom_field( $term_posts, $custom_field_meta_key, $custom_field_meta_value );
					}

					// Sort posts.
					if ( $orderby === 'date' ) {
						usort(
							$term_posts,
							function ( $a, $b ) use ( $order ) {
								$date_a = strtotime( $a->post_date );
								$date_b = strtotime( $b->post_date );
								return $order === 'DESC' ? $date_b - $date_a : $date_a - $date_b;
							}
						);
					} elseif ( $orderby === 'title' ) {
						usort(
							$term_posts,
							function ( $a, $b ) use ( $order ) {
								$cmp = strcasecmp( $a->post_title, $b->post_title );
								return $order === 'DESC' ? -$cmp : $cmp;
							}
						);
					}

					// Don't limit here if load more is enabled - we'll limit in display.
				} else {
					// Use standard taxonomy query.
					// If load more is enabled, get all posts first.
					$query_args = array(
						'post_type'        => $post_type,
						'posts_per_page'   => $enable_load_more ? -1 : $posts_per_page,
						'post_status'      => 'publish',
						'tax_query'        => array(
							array(
								'taxonomy'         => $taxonomy,
								'field'            => 'term_id',
								'terms'            => $term->term_id,
								'include_children' => false,
							),
						),
						'orderby'          => $orderby,
						'order'            => $order,
						'suppress_filters' => false,
						'no_found_rows'    => true,
					);

					// Add meta query if custom field filter is set.
					if ( ! empty( $custom_field_meta_key ) && ! empty( $custom_field_meta_value ) ) {
						$query_args['meta_query'] = array(
							array(
								'key'     => $custom_field_meta_key,
								'value'   => $custom_field_meta_value,
								'compare' => '=',
							),
						);
					}

					// Use WP_Query instead of get_posts for better Polylang support.
					$term_query = new WP_Query( $query_args );
					$term_posts = $term_query->posts;

					// Apply custom field filter if set (for array meta values).
					if ( ! empty( $custom_field_meta_key ) && ! empty( $custom_field_meta_value ) ) {
						$term_posts = omsar_bd_filter_posts_by_taxonomy_custom_field( $term_posts, $custom_field_meta_key, $custom_field_meta_value );
					}

					// If no posts found and Polylang is active, try getting all language versions of the term.
					if ( empty( $term_posts ) && function_exists( 'pll_get_term_translations' ) ) {
						$term_translations = pll_get_term_translations( $term->term_id );
						if ( ! empty( $term_translations ) && count( $term_translations ) > 1 ) {
							// Get all term IDs (all language versions).
							$all_term_ids                    = array_values( $term_translations );
							$query_args['tax_query'][0]['terms'] = $all_term_ids;
							$term_query                      = new WP_Query( $query_args );
							$term_posts                      = $term_query->posts;
						}
					}

					// If still no posts, try with slug instead of term_id.
					if ( empty( $term_posts ) ) {
						$query_args['tax_query'][0]['field'] = 'slug';
						$query_args['tax_query'][0]['terms'] = $term->slug;
						$term_query                           = new WP_Query( $query_args );
						$term_posts                           = $term_query->posts;
					}

					// Last resort: Get all posts and filter manually by checking their terms.
					if ( empty( $term_posts ) ) {
						$all_posts_check = get_posts(
							array(
								'post_type'        => $post_type,
								'posts_per_page'   => -1,
								'post_status'      => 'publish',
								'suppress_filters' => false,
							)
						);

						$term_posts = array();
						// Get all term IDs including translations.
						$term_ids_to_check = array( $term->term_id );
						if ( function_exists( 'pll_get_term_translations' ) ) {
							$term_translations = pll_get_term_translations( $term->term_id );
							if ( ! empty( $term_translations ) ) {
								$term_ids_to_check = array_merge( $term_ids_to_check, array_values( $term_translations ) );
							}
						}

						foreach ( $all_posts_check as $post_check ) {
							$post_terms = wp_get_post_terms( $post_check->ID, $taxonomy, array( 'fields' => 'ids' ) );

							// Check if post has any of the term IDs (including translations).
							$has_term = ! empty( array_intersect( $post_terms, $term_ids_to_check ) );
							if ( $has_term ) {
								$term_posts[] = $post_check;
							}
						}

						// Sort the manually filtered posts.
						if ( $orderby === 'date' ) {
							usort(
								$term_posts,
								function ( $a, $b ) use ( $order ) {
									$date_a = strtotime( $a->post_date );
									$date_b = strtotime( $b->post_date );
									return $order === 'DESC' ? $date_b - $date_a : $date_a - $date_b;
								}
							);
						} elseif ( $orderby === 'title' ) {
							usort(
								$term_posts,
								function ( $a, $b ) use ( $order ) {
									$cmp = strcasecmp( $a->post_title, $b->post_title );
									return $order === 'DESC' ? -$cmp : $cmp;
								}
							);
						}
					}
				}
				?>
				<div class="tab-pane fade <?php echo $is_first ? 'show active' : ''; ?>"
					id="<?php echo esc_attr( $widget_id ); ?>-<?php echo esc_attr( $term_slug ); ?>"
					role="tabpanel">
					<?php if ( ! empty( $term_posts ) ) : ?>
						<?php
						// Limit posts if load more is enabled.
						$displayed_term_posts = $enable_load_more && $posts_per_page > 0 ? array_slice( $term_posts, 0, $posts_per_page ) : $term_posts;
						$has_more_term_posts  = $enable_load_more && $posts_per_page > 0 && count( $term_posts ) > $posts_per_page;
						?>
						<div class="omsar-posts-grid <?php echo esc_attr( $listing_style ); ?>"
							style="<?php echo esc_attr( $grid_column_style ); ?>"
							data-tab="<?php echo esc_attr( $term_slug ); ?>"
							data-widget-id="<?php echo esc_attr( $widget_id ); ?>"
							data-post-type="<?php echo esc_attr( $post_type ); ?>"
							data-taxonomy="<?php echo esc_attr( $taxonomy ); ?>"
							data-term-id="<?php echo esc_attr( $term->term_id ); ?>"
							data-term-name="<?php echo esc_attr( $term->name ); ?>"
							data-use-acf="<?php echo $use_acf ? '1' : '0'; ?>"
							data-acf-field="<?php echo esc_attr( $acf_field_name ); ?>"
							data-listing-style="<?php echo esc_attr( $listing_style ); ?>"
							data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>"
							data-orderby="<?php echo esc_attr( $orderby ); ?>"
							data-order="<?php echo esc_attr( $order ); ?>"
							data-default-image="<?php echo esc_url( $default_image ); ?>"
							data-custom-field-meta-key="<?php echo esc_attr( $custom_field_meta_key ); ?>"
							data-custom-field-meta-value="<?php echo esc_attr( $custom_field_meta_value ); ?>"
							data-columns="<?php echo esc_attr( $columns ); ?>"
							data-style5-show-categories="<?php echo $style5_show_categories ? '1' : '0'; ?>"
							data-style5-show-excerpt="<?php echo $style5_show_excerpt ? '1' : '0'; ?>"
							data-style5-show-date="<?php echo $style5_show_date ? '1' : '0'; ?>"
							data-style5-show-author="<?php echo $style5_show_author ? '1' : '0'; ?>"
							data-style5-content-overlay="<?php echo $style5_content_overlay ? '1' : '0'; ?>"
							data-style5-side-layout="<?php echo $style5_side_layout ? '1' : '0'; ?>"
							data-clickable-cards="<?php echo $clickable_cards ? '1' : '0'; ?>"
							data-current-page="1"
							data-total-posts="<?php echo count( $term_posts ); ?>">
							<?php foreach ( $displayed_term_posts as $post ) : ?>
								<?php
								setup_postdata( $post );
								omsar_bd_render_posts_by_taxonomy_card( $post, $widget_id, $listing_style, $clickable_cards, $default_image, $show_date, $style5_show_categories, $style5_show_excerpt, $style5_show_date, $style5_show_author, $style5_content_overlay, $style5_side_layout );
								?>
							<?php endforeach; ?>
							<?php wp_reset_postdata(); ?>
						</div>
						<?php if ( $enable_load_more && $has_more_term_posts ) : ?>
							<div class="omsar-load-more-wrapper" data-tab="<?php echo esc_attr( $term_slug ); ?>">
								<button class="omsar-load-more-btn" data-widget-id="<?php echo esc_attr( $widget_id ); ?>" data-tab="<?php echo esc_attr( $term_slug ); ?>">
									<?php echo esc_html( $load_more_text ); ?>
								</button>
							</div>
						<?php endif; ?>
					<?php else : ?>
						<div class="omsar-no-posts">
							<p><?php echo function_exists( 'pll__' ) ? esc_html( pll__( 'No posts found in this category.' ) ) : esc_html__( 'No posts found in this category.', 'omsar' ); ?></p>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Render a single post card.
 *
 * @param WP_Post $post Post object.
 * @param string  $widget_id Widget id.
 * @param string  $style Listing style.
 * @param bool    $clickable Whether card is clickable.
 * @param string  $default_image Default image URL.
 * @param bool    $show_date Show date.
 * @param bool    $style5_show_categories Style5 categories toggle.
 * @param bool    $style5_show_excerpt Style5 excerpt toggle.
 * @param bool    $style5_show_date Style5 date toggle.
 * @param bool    $style5_show_author Style5 author toggle.
 * @param bool    $style5_content_overlay Style5 overlay toggle.
 * @param bool    $style5_side_layout Style5 side layout toggle.
 */
function omsar_bd_render_posts_by_taxonomy_card( $post, $widget_id = '', $style = 'style1', $clickable = true, $default_image = '', $show_date = true, $style5_show_categories = true, $style5_show_excerpt = true, $style5_show_date = true, $style5_show_author = true, $style5_content_overlay = false, $style5_side_layout = false ) {
	$post_id = $post->ID;

	// Get featured image or default image.
	$post_image = '';
	if ( has_post_thumbnail( $post_id ) ) {
		$post_image = get_the_post_thumbnail_url( $post_id, 'large' );
	} elseif ( ! empty( $default_image ) ) {
		$post_image = $default_image;
	}

	$post_title = get_the_title( $post_id );
	$post_date  = get_the_date( 'M j, Y', $post_id );
	$post_link  = get_permalink( $post_id );

	// Get post excerpt for display (style5).
	$post_excerpt = '';
	if ( has_excerpt( $post_id ) ) {
		$post_excerpt = wp_trim_words( get_the_excerpt( $post_id ), 20, '...' );
	} elseif ( ! empty( $post->post_content ) ) {
		$post_excerpt = wp_trim_words( strip_shortcodes( $post->post_content ), 20, '...' );
	}

	// Get full excerpt for search (all styles).
	$post_excerpt_for_search = '';
	if ( has_excerpt( $post_id ) ) {
		$post_excerpt_for_search = strtolower( strip_tags( get_the_excerpt( $post_id ) ) );
	} elseif ( ! empty( $post->post_content ) ) {
		$post_excerpt_for_search = strtolower( strip_tags( wp_trim_words( strip_shortcodes( $post->post_content ), 50, '' ) ) );
	}

	// Get post author.
	$post_author = get_the_author_meta( 'display_name', $post->post_author );

	// Get post categories for style5.
	$post_categories = array();
	if ( $style === 'style5' ) {
		$categories = get_the_category( $post_id );
		if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
			foreach ( $categories as $category ) {
				$post_categories[] = $category;
			}
		}
	}

	// Get PDFs from documents repeater field.
	$publication_pdfs = array();
	if ( function_exists( 'get_field' ) && function_exists( 'have_rows' ) ) {
		if ( have_rows( 'documents', $post_id ) ) {
			while ( have_rows( 'documents', $post_id ) ) {
				the_row();
				$pdf_file = get_sub_field( 'pdf' );
				if ( $pdf_file ) {
					// Handle both file ID and file array.
					if ( is_numeric( $pdf_file ) ) {
						$pdf_url   = wp_get_attachment_url( $pdf_file );
						$pdf_title = get_the_title( $pdf_file );
					} elseif ( is_array( $pdf_file ) ) {
						$pdf_url   = $pdf_file['url'] ?? '';
						$pdf_title = $pdf_file['title'] ?? basename( $pdf_url );
					} else {
						$pdf_url   = $pdf_file;
						$pdf_title = basename( $pdf_url );
					}

					if ( $pdf_url ) {
						$publication_pdfs[] = array(
							'url'   => $pdf_url,
							'title' => $pdf_title,
						);
					}
				}
			}
		}
	}

	$card_classes = 'omsar-post-card omsar-post-card-' . esc_attr( $style );
	if ( $clickable ) {
		$card_classes .= ' omsar-post-card-clickable';
	}
	?>
	<div class="<?php echo esc_attr( $card_classes ); ?>"
		data-post-title="<?php echo esc_attr( strtolower( $post_title ) ); ?>"
		data-post-excerpt="<?php echo esc_attr( $post_excerpt_for_search ); ?>"
		<?php echo $clickable ? 'data-post-link="' . esc_url( $post_link ) . '"' : ''; ?>>
		<?php if ( $style === 'style5' ) : ?>
			<?php if ( $style5_content_overlay && $post_image ) : ?>
				<!-- Style 5: Content Overlay on Image -->
				<div class="omsar-post-card-style5-overlay" style="background-image: url(<?php echo esc_url( $post_image ); ?>);">
					<div class="omsar-post-card-style5-overlay-content">
						<?php if ( $style5_show_categories && ! empty( $post_categories ) ) : ?>
							<div class="omsar-post-categories">
								<?php foreach ( $post_categories as $category ) : ?>
									<span class="omsar-post-category-tag"><?php echo esc_html( strtoupper( $category->name ) ); ?></span>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<h3 class="omsar-post-title">
							<?php if ( $clickable ) : ?>
								<a href="<?php echo esc_url( $post_link ); ?>">
									<?php echo esc_html( $post_title ); ?>
								</a>
							<?php else : ?>
								<?php echo esc_html( $post_title ); ?>
							<?php endif; ?>
						</h3>

						<?php if ( $style5_show_excerpt && ! empty( $post_excerpt ) ) : ?>
							<div class="omsar-post-excerpt">
								<?php echo esc_html( $post_excerpt ); ?>
							</div>
						<?php endif; ?>

						<?php if ( $style5_show_date || $style5_show_author ) : ?>
							<div class="omsar-post-meta">
								<?php if ( $style5_show_date && $post_date ) : ?>
									<span class="omsar-post-date"><?php echo esc_html( $post_date ); ?></span>
								<?php endif; ?>
								<?php if ( $style5_show_author && ! empty( $post_author ) ) : ?>
									<span class="omsar-post-author"><?php echo esc_html__( 'By', 'omsar' ); ?> <?php echo esc_html( $post_author ); ?></span>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php elseif ( $style5_side_layout ) : ?>
				<!-- Style 5: Side Layout (Image Left, Content Right) -->
				<?php if ( $post_image ) : ?>
					<div class="omsar-post-image-side">
						<?php if ( $clickable ) : ?>
							<a href="<?php echo esc_url( $post_link ); ?>">
								<img src="<?php echo esc_url( $post_image ); ?>" alt="<?php echo esc_attr( $post_title ); ?>">
							</a>
						<?php else : ?>
							<img src="<?php echo esc_url( $post_image ); ?>" alt="<?php echo esc_attr( $post_title ); ?>">
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="omsar-post-content-side">
					<?php if ( $style5_show_categories && ! empty( $post_categories ) ) : ?>
						<div class="omsar-post-categories">
							<?php foreach ( $post_categories as $category ) : ?>
								<span class="omsar-post-category-tag"><?php echo esc_html( strtoupper( $category->name ) ); ?></span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<h3 class="omsar-post-title">
						<?php if ( $clickable ) : ?>
							<a href="<?php echo esc_url( $post_link ); ?>">
								<?php echo esc_html( $post_title ); ?>
							</a>
						<?php else : ?>
							<?php echo esc_html( $post_title ); ?>
						<?php endif; ?>
					</h3>

					<?php if ( $style5_show_date && $post_date ) : ?>
						<div class="omsar-post-date-side">
							<?php echo esc_html( $post_date ); ?>
						</div>
					<?php endif; ?>

					<?php if ( $style5_show_excerpt && ! empty( $post_excerpt ) ) : ?>
						<div class="omsar-post-excerpt">
							<?php echo esc_html( $post_excerpt ); ?>
						</div>
					<?php endif; ?>
				</div>
			<?php else : ?>
				<!-- Style 5: Normal Layout (Image and Content Separate) -->
				<?php if ( $post_image ) : ?>
					<div class="omsar-post-image-header">
						<?php if ( $clickable ) : ?>
							<a href="<?php echo esc_url( $post_link ); ?>">
								<img src="<?php echo esc_url( $post_image ); ?>" alt="<?php echo esc_attr( $post_title ); ?>">
							</a>
						<?php else : ?>
							<img src="<?php echo esc_url( $post_image ); ?>" alt="<?php echo esc_attr( $post_title ); ?>">
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="omsar-post-content">
					<?php if ( $style5_show_categories && ! empty( $post_categories ) ) : ?>
						<div class="omsar-post-categories">
							<?php foreach ( $post_categories as $category ) : ?>
								<span class="omsar-post-category-tag"><?php echo esc_html( strtoupper( $category->name ) ); ?></span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<h3 class="omsar-post-title">
						<?php if ( $clickable ) : ?>
							<a href="<?php echo esc_url( $post_link ); ?>">
								<?php echo esc_html( $post_title ); ?>
							</a>
						<?php else : ?>
							<?php echo esc_html( $post_title ); ?>
						<?php endif; ?>
					</h3>

					<?php if ( $style5_show_excerpt && ! empty( $post_excerpt ) ) : ?>
						<div class="omsar-post-excerpt">
							<?php echo esc_html( $post_excerpt ); ?>
						</div>
					<?php endif; ?>

					<?php if ( $style5_show_date || $style5_show_author ) : ?>
						<div class="omsar-post-meta">
							<?php if ( $style5_show_date && $post_date ) : ?>
								<span class="omsar-post-date"><?php echo esc_html( $post_date ); ?></span>
							<?php endif; ?>
							<?php if ( $style5_show_author && ! empty( $post_author ) ) : ?>
								<span class="omsar-post-author"><?php echo esc_html__( 'By', 'omsar' ); ?> <?php echo esc_html( $post_author ); ?></span>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		<?php elseif ( $style === 'style3' ) : ?>
			<!-- Style 3: Content above image -->
			<div class="omsar-post-content">
				<h3 class="omsar-post-title">
					<?php if ( $clickable ) : ?>
						<a href="<?php echo esc_url( $post_link ); ?>">
							<?php echo esc_html( $post_title ); ?>
						</a>
					<?php else : ?>
						<?php echo esc_html( $post_title ); ?>
					<?php endif; ?>
				</h3>

				<?php if ( $show_date && $post_date ) : ?>
					<div class="omsar-post-date">
						<i class="bi bi-calendar3"></i>
						<?php echo esc_html( $post_date ); ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $publication_pdfs ) ) : ?>
					<div class="omsar-post-pdfs">
						<strong><?php echo function_exists( 'pll__' ) ? pll__( 'Downloads' ) : esc_html__( 'Downloads', 'omsar' ); ?>:</strong>
						<ul class="pdf-list">
							<?php foreach ( $publication_pdfs as $pdf ) : ?>
								<?php
								$pdf_url   = $pdf['url'] ?? '';
								$pdf_title = $pdf['title'] ?? basename( $pdf_url );
								if ( $pdf_url ) :
									?>
									<li>
										<a href="<?php echo esc_url( $pdf_url ); ?>" target="_blank" rel="noopener noreferrer" class="document-link">
											<i class="bi bi-file-earmark-pdf"></i>
											<?php echo esc_html( $pdf_title ); ?>
										</a>
									</li>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $post_image && $style !== 'style5' ) : ?>
			<div class="omsar-post-image">
				<?php if ( $clickable ) : ?>
					<a href="<?php echo esc_url( $post_link ); ?>">
						<img src="<?php echo esc_url( $post_image ); ?>" alt="<?php echo esc_attr( $post_title ); ?>">
					</a>
				<?php else : ?>
					<img src="<?php echo esc_url( $post_image ); ?>" alt="<?php echo esc_attr( $post_title ); ?>">
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $style !== 'style3' && $style !== 'style5' ) : ?>
			<div class="omsar-post-content">
				<h3 class="omsar-post-title">
					<?php if ( $clickable ) : ?>
						<a href="<?php echo esc_url( $post_link ); ?>">
							<?php echo esc_html( $post_title ); ?>
						</a>
					<?php else : ?>
						<?php echo esc_html( $post_title ); ?>
					<?php endif; ?>
				</h3>

				<?php if ( $show_date && $post_date ) : ?>
					<div class="omsar-post-date">
						<i class="bi bi-calendar3"></i>
						<?php echo esc_html( $post_date ); ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $publication_pdfs ) ) : ?>
					<div class="omsar-post-pdfs">
						<strong><?php echo function_exists( 'pll__' ) ? pll__( 'Downloads' ) : esc_html__( 'Downloads', 'omsar' ); ?>:</strong>
						<ul class="pdf-list">
							<?php foreach ( $publication_pdfs as $pdf ) : ?>
								<?php
								$pdf_url   = $pdf['url'] ?? '';
								$pdf_title = $pdf['title'] ?? basename( $pdf_url );
								if ( $pdf_url ) :
									?>
									<li>
										<a href="<?php echo esc_url( $pdf_url ); ?>" target="_blank" rel="noopener noreferrer" class="document-link">
											<i class="bi bi-file-earmark-pdf"></i>
											<?php echo esc_html( $pdf_title ); ?>
										</a>
									</li>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

