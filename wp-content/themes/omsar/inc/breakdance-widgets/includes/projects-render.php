<?php
/**
 * Breakdance Projects element rendering (mirrors Elementor OMSAR_Projects_Widget).
 *
 * @package OmsarBreakdanceElements
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<string, string>
 */
function omsar_bd_projects_get_post_type_options() {
	$post_types = get_post_types( array( 'public' => true ), 'objects' );
	$options    = array();
	foreach ( $post_types as $post_type ) {
		$options[ $post_type->name ] = $post_type->label;
	}
	return $options;
}

/**
 * @return array<string, string>
 */
function omsar_bd_projects_get_taxonomy_options() {
	$options      = array( '' => __( 'Select Taxonomy', 'omsar' ) );
	$taxonomies   = get_taxonomies( array( 'public' => true ), 'objects' );
	foreach ( $taxonomies as $tax ) {
		$options[ $tax->name ] = $tax->label;
	}
	return $options;
}

/**
 * @return array<string, string>
 */
function omsar_bd_projects_get_acf_field_options() {
	$options = array( '' => __( 'Select Custom Field', 'omsar' ) );
	if ( ! function_exists( 'acf_get_field_groups' ) ) {
		return $options;
	}
	foreach ( acf_get_field_groups() as $field_group ) {
		$fields = acf_get_fields( $field_group['ID'] );
		if ( ! $fields ) {
			continue;
		}
		foreach ( $fields as $field ) {
			$label = ! empty( $field['label'] ) ? $field['label'] : $field['name'];
			if ( ! empty( $field['type'] ) ) {
				$label .= ' (' . $field['type'] . ')';
			}
			$options[ $field['name'] ] = $label;
		}
	}
	return $options;
}

/**
 * @return array<string, mixed>
 */
function omsar_bd_projects_default_settings() {
	return array(
		'post_type'                    => 'projects',
		'posts_per_page'               => 9,
		'enable_load_more'             => false,
		'load_more_text'               => function_exists( 'pll__' ) ? pll__( 'Load More' ) : __( 'Load More', 'omsar' ),
		'orderby'                      => 'date',
		'order'                        => 'DESC',
		'enable_search'                => true,
		'search_placeholder'           => __( 'Search projects...', 'omsar' ),
		'search_label'                 => __( 'Search:', 'omsar' ),
		'enable_dropdown_filter'       => false,
		'dropdown_filter_label'        => __( 'Filter:', 'omsar' ),
		'dropdown_filter_taxonomy'     => '',
		'dropdown_filter_custom_field' => '',
		'default_image'                => '',
		'badge_source'                 => 'custom_field',
		'badge_taxonomy'               => '',
		'badge_custom_field'           => 'pillar_option',
		'badge_taxonomy_display'       => 'name',
		'make_card_clickable'          => true,
		'show_read_more'               => true,
		'read_more_text'               => function_exists( 'pll__' ) ? pll__( 'Read More' ) : __( 'Read More', 'omsar' ),
		'columns'                      => '3',
	);
}

/**
 * @param array<string, mixed> $settings Settings.
 * @return array<string, mixed>
 */
function omsar_bd_projects_normalize_settings( $settings ) {
	$defaults = omsar_bd_projects_default_settings();
	$settings = wp_parse_args( is_array( $settings ) ? $settings : array(), $defaults );

	foreach ( array( 'enable_load_more', 'enable_search', 'enable_dropdown_filter', 'make_card_clickable', 'show_read_more' ) as $bool_key ) {
		if ( isset( $settings[ $bool_key ] ) && ( $settings[ $bool_key ] === 'yes' || $settings[ $bool_key ] === true || $settings[ $bool_key ] === 1 ) ) {
			$settings[ $bool_key ] = true;
		} elseif ( isset( $settings[ $bool_key ] ) ) {
			$settings[ $bool_key ] = false;
		}
	}

	if ( is_array( $settings['default_image'] ?? null ) ) {
		$settings['default_image'] = $settings['default_image']['url'] ?? '';
	}

	return $settings;
}

/**
 * @param array<string, mixed> $properties_data Breakdance properties.
 * @return array<string, mixed>
 */
function omsar_bd_projects_settings_from_breakdance( $properties_data ) {
	$content = $properties_data['content']['content'] ?? array();
	$image   = $content['default_image'] ?? '';
	if ( is_array( $image ) ) {
		$image = $image['url'] ?? '';
	}

	return omsar_bd_projects_normalize_settings(
		array(
			'post_type'                    => $content['post_type'] ?? 'projects',
			'posts_per_page'               => isset( $content['posts_per_page'] ) ? (int) $content['posts_per_page'] : 9,
			'enable_load_more'             => $content['enable_load_more'] ?? false,
			'load_more_text'               => $content['load_more_text'] ?? '',
			'orderby'                      => $content['orderby'] ?? 'date',
			'order'                        => $content['order'] ?? 'DESC',
			'enable_search'                => $content['enable_search'] ?? true,
			'search_placeholder'           => $content['search_placeholder'] ?? '',
			'search_label'                 => $content['search_label'] ?? '',
			'enable_dropdown_filter'       => $content['enable_dropdown_filter'] ?? false,
			'dropdown_filter_label'        => $content['dropdown_filter_label'] ?? '',
			'dropdown_filter_taxonomy'     => $content['dropdown_filter_taxonomy'] ?? '',
			'dropdown_filter_custom_field' => $content['dropdown_filter_custom_field'] ?? '',
			'default_image'                => $image,
			'badge_source'                 => $content['badge_source'] ?? 'custom_field',
			'badge_taxonomy'               => $content['badge_taxonomy'] ?? '',
			'badge_custom_field'           => $content['badge_custom_field'] ?? 'pillar_option',
			'badge_taxonomy_display'       => $content['badge_taxonomy_display'] ?? 'name',
			'make_card_clickable'          => $content['make_card_clickable'] ?? true,
			'show_read_more'               => $content['show_read_more'] ?? true,
			'read_more_text'               => $content['read_more_text'] ?? '',
			'columns'                      => isset( $content['columns'] ) ? (string) $content['columns'] : '3',
		)
	);
}

/**
 * @param string $field_name ACF field name.
 * @return string
 */
function omsar_bd_projects_get_acf_field_type( $field_name ) {
	if ( ! function_exists( 'acf_get_field_groups' ) || empty( $field_name ) ) {
		return '';
	}
	foreach ( acf_get_field_groups() as $field_group ) {
		$fields = acf_get_fields( $field_group['ID'] );
		if ( ! $fields ) {
			continue;
		}
		foreach ( $fields as $field ) {
			if ( $field['name'] === $field_name ) {
				return ! empty( $field['type'] ) ? $field['type'] : '';
			}
		}
	}
	return '';
}

/**
 * @param int    $post_id Post ID.
 * @param string $source Badge source.
 * @param string $taxonomy Taxonomy slug.
 * @param string $custom_field ACF field name.
 * @param string $taxonomy_display name|id.
 * @return string
 */
function omsar_bd_projects_get_badge_text( $post_id, $source, $taxonomy = '', $custom_field = '', $taxonomy_display = 'name' ) {
	if ( $source === 'none' ) {
		return '';
	}

	if ( $source === 'taxonomy' && ! empty( $taxonomy ) ) {
		$terms = get_the_terms( $post_id, $taxonomy );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$term = array_shift( $terms );
			return $term->name;
		}
	}

	if ( $source === 'custom_field' && ! empty( $custom_field ) ) {
		$value = function_exists( 'get_field' ) ? get_field( $custom_field, $post_id ) : get_post_meta( $post_id, $custom_field, true );
		if ( empty( $value ) ) {
			return '';
		}

		$field_type = omsar_bd_projects_get_acf_field_type( $custom_field );
		if ( $field_type === 'taxonomy' ) {
			$term = null;
			if ( is_array( $value ) && ! empty( $value[0] ) ) {
				$first = $value[0];
				if ( is_object( $first ) && isset( $first->term_id ) ) {
					$term = $first;
				} elseif ( is_numeric( $first ) ) {
					$term = get_term( $first );
				}
			} elseif ( is_object( $value ) && isset( $value->term_id ) ) {
				$term = $value;
			} elseif ( is_numeric( $value ) ) {
				$term = get_term( $value );
			}
			if ( $term && ! is_wp_error( $term ) ) {
				return $taxonomy_display === 'id' ? (string) $term->term_id : $term->name;
			}
			if ( is_object( $value ) && isset( $value->name ) ) {
				return $value->name;
			}
		} else {
			if ( is_array( $value ) ) {
				$parts = array();
				foreach ( $value as $item ) {
					if ( is_object( $item ) && isset( $item->name ) ) {
						$parts[] = $item->name;
					} else {
						$parts[] = (string) $item;
					}
				}
				return implode( ', ', array_filter( $parts ) );
			}
			if ( is_object( $value ) && isset( $value->name ) ) {
				return $value->name;
			}
			return (string) $value;
		}
	}

	return '';
}

/**
 * @param WP_Post              $post Post object.
 * @param array<string, mixed> $args Card render args.
 */
function omsar_bd_projects_render_card( $post, $args = array() ) {
	$defaults = array(
		'default_image'              => '',
		'badge_source'               => 'none',
		'badge_taxonomy'             => '',
		'badge_custom_field'         => '',
		'badge_taxonomy_display'     => 'name',
		'read_more_text'             => __( 'Read More', 'omsar' ),
		'filter_dropdown_taxonomy'   => '',
		'filter_by_acf_field'        => '',
		'show_read_more'             => true,
		'make_card_clickable'        => true,
		'is_arabic'                  => false,
	);
	$args    = wp_parse_args( $args, $defaults );
	$post_id = $post->ID;

	$post_image = '';
	if ( has_post_thumbnail( $post_id ) ) {
		$post_image = get_the_post_thumbnail_url( $post_id, 'large' );
	} elseif ( ! empty( $args['default_image'] ) ) {
		$post_image = $args['default_image'];
	}

	$post_title = get_the_title( $post_id );
	$post_link  = get_permalink( $post_id );

	$post_excerpt = '';
	if ( has_excerpt( $post_id ) ) {
		$post_excerpt = wp_trim_words( get_the_excerpt( $post_id ), 20, '...' );
	} elseif ( ! empty( $post->post_content ) ) {
		$post_excerpt = wp_trim_words( strip_shortcodes( $post->post_content ), 20, '...' );
	}

	$post_excerpt_for_search = '';
	if ( has_excerpt( $post_id ) ) {
		$post_excerpt_for_search = strtolower( strip_tags( get_the_excerpt( $post_id ) ) );
	} elseif ( ! empty( $post->post_content ) ) {
		$post_excerpt_for_search = strtolower( strip_tags( wp_trim_words( strip_shortcodes( $post->post_content ), 50, '' ) ) );
	}

	$badge_text = omsar_bd_projects_get_badge_text(
		$post_id,
		$args['badge_source'],
		$args['badge_taxonomy'],
		$args['badge_custom_field'],
		$args['badge_taxonomy_display']
	);

	$filter_value = '';
	if ( ! empty( $args['filter_dropdown_taxonomy'] ) ) {
		$filter_taxonomy = $args['filter_dropdown_taxonomy'];
		$acf_field       = $args['filter_by_acf_field'];

		if ( ! empty( $acf_field ) && function_exists( 'get_field' ) ) {
			$acf_field_value = get_field( $acf_field, $post_id );
			$acf_field_type  = omsar_bd_projects_get_acf_field_type( $acf_field );

			if ( ! empty( $acf_field_value ) ) {
				if ( $acf_field_type === 'taxonomy' ) {
					$term_id = null;
					if ( is_array( $acf_field_value ) ) {
						$term_item = reset( $acf_field_value );
						if ( is_object( $term_item ) && isset( $term_item->term_id ) ) {
							$term_id = $term_item->term_id;
						} elseif ( is_numeric( $term_item ) ) {
							$term_id = $term_item;
						}
					} elseif ( is_object( $acf_field_value ) && isset( $acf_field_value->term_id ) ) {
						$term_id = $acf_field_value->term_id;
					} elseif ( is_numeric( $acf_field_value ) ) {
						$term_id = $acf_field_value;
					}
					if ( $term_id ) {
						$term = get_term( $term_id );
						if ( $term && ! is_wp_error( $term ) && $term->taxonomy === $filter_taxonomy ) {
							$filter_value = (string) $term_id;
						}
					}
				} else {
					$acf_value_text = omsar_bd_projects_get_badge_text( $post_id, 'custom_field', '', $acf_field, 'name' );
					if ( ! empty( $acf_value_text ) ) {
						$filter_terms = get_terms(
							array(
								'taxonomy'   => $filter_taxonomy,
								'hide_empty' => false,
							)
						);
						if ( ! is_wp_error( $filter_terms ) ) {
							foreach ( $filter_terms as $term ) {
								if ( strcasecmp( $term->name, $acf_value_text ) === 0 ) {
									$filter_value = (string) $term->term_id;
									break;
								}
							}
						}
					}
				}
			}
		} else {
			$filter_terms = get_the_terms( $post_id, $filter_taxonomy );
			if ( $filter_terms && ! is_wp_error( $filter_terms ) && ! empty( $filter_terms ) ) {
				$filter_term  = array_shift( $filter_terms );
				$filter_value = (string) $filter_term->term_id;
			}
		}
	}

	$item_style = ! empty( $post_image ) ? 'style="background-image: url(' . esc_url( $post_image ) . ');"' : '';
	$filter_attr = ! empty( $filter_value ) ? 'data-filter-value="' . esc_attr( $filter_value ) . '"' : '';

	$card_clickable_class = $args['make_card_clickable'] ? 'omsar-card-clickable' : '';
	$card_clickable_attr  = $args['make_card_clickable'] ? 'data-card-link="' . esc_url( $post_link ) . '"' : '';
	$title_link           = $args['make_card_clickable'] ? 'javascript:void(0);' : esc_url( $post_link );
	$title_link_attr      = $args['make_card_clickable'] ? 'onclick="return false;"' : '';
	$read_more_link       = $args['make_card_clickable'] ? 'javascript:void(0);' : esc_url( $post_link );
	$read_more_link_attr  = $args['make_card_clickable'] ? 'onclick="return false;"' : '';
	?>
	<div class="omsar-post-card omsar-post-card-style4 <?php echo $args['is_arabic'] ? 'rtl-card ' : ''; ?><?php echo esc_attr( $card_clickable_class ); ?>"
		<?php if ( $args['is_arabic'] ) : ?>dir="rtl" <?php endif; ?>
		data-post-title="<?php echo esc_attr( strtolower( $post_title ) ); ?>"
		data-post-excerpt="<?php echo esc_attr( $post_excerpt_for_search ); ?>"
		data-post-link="<?php echo esc_url( $post_link ); ?>"
		<?php echo $item_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php echo $filter_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php echo $card_clickable_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<div class="omsar-post-content">
			<?php if ( ! empty( $badge_text ) ) : ?>
				<span class="omsar-post-badge"><?php echo esc_html( $badge_text ); ?></span>
			<?php endif; ?>

			<h3 class="omsar-post-title">
				<a href="<?php echo $args['make_card_clickable'] ? 'javascript:void(0);' : esc_url( $post_link ); ?>" <?php echo $title_link_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<?php echo esc_html( $post_title ); ?>
				</a>
			</h3>

			<?php if ( ! empty( $post_excerpt ) ) : ?>
				<div class="omsar-post-excerpt">
					<?php echo wp_kses_post( $post_excerpt ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $args['show_read_more'] ) : ?>
				<a href="<?php echo $args['make_card_clickable'] ? 'javascript:void(0);' : esc_url( $post_link ); ?>" class="omsar-post-read-more" <?php echo $read_more_link_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<?php echo esc_html( $args['read_more_text'] ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

/**
 * Render full projects widget.
 *
 * @param array<string, mixed> $settings Widget settings.
 * @param string               $element_id Unique instance ID.
 */
function omsar_bd_render_projects_widget( $settings, $element_id ) {
	$settings = omsar_bd_projects_normalize_settings( $settings );

	$post_type        = $settings['post_type'];
	$posts_per_page   = max( 1, (int) $settings['posts_per_page'] );
	$orderby          = $settings['orderby'];
	$order            = $settings['order'];
	$default_image    = $settings['default_image'];
	$badge_source     = $settings['badge_source'];
	$badge_taxonomy   = $settings['badge_taxonomy'];
	$badge_custom_field = $settings['badge_custom_field'];
	$badge_taxonomy_display = $settings['badge_taxonomy_display'];
	$make_card_clickable = $settings['make_card_clickable'];
	$show_read_more   = $settings['show_read_more'];
	$read_more_text   = $settings['read_more_text'];
	$enable_search    = $settings['enable_search'];
	$search_placeholder = $settings['search_placeholder'];
	$search_label     = $settings['search_label'];
	$enable_load_more = $settings['enable_load_more'];
	$load_more_text   = $settings['load_more_text'];
	$enable_dropdown  = $settings['enable_dropdown_filter'];
	$dropdown_label   = $settings['dropdown_filter_label'];
	$dropdown_taxonomy = $settings['dropdown_filter_taxonomy'];
	$dropdown_custom_field = $settings['dropdown_filter_custom_field'];
	$columns          = $settings['columns'];

	if ( empty( $dropdown_custom_field ) && $badge_source === 'custom_field' && ! empty( $badge_custom_field ) ) {
		$dropdown_custom_field = $badge_custom_field;
	}

	$widget_id = 'omsar-projects-bd-' . $element_id;

	$query_args = array(
		'post_type'              => $post_type,
		'posts_per_page'         => -1,
		'post_status'            => 'publish',
		'orderby'                => $orderby,
		'order'                  => $order,
		'suppress_filters'       => false,
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
	);

	if ( function_exists( 'pll_current_language' ) ) {
		$lang = pll_current_language();
		if ( $lang ) {
			$query_args['lang'] = $lang;
		}
	}

	$all_posts = get_posts( $query_args );

	$displayed_posts = $enable_load_more ? array_slice( $all_posts, 0, $posts_per_page ) : $all_posts;
	$has_more_posts    = $enable_load_more && count( $all_posts ) > $posts_per_page;

	$current_lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
	$is_arabic    = ( $current_lang === 'ar' || is_rtl() );

	$card_args = array(
		'default_image'            => $default_image,
		'badge_source'             => $badge_source,
		'badge_taxonomy'           => $badge_taxonomy,
		'badge_custom_field'       => $badge_custom_field,
		'badge_taxonomy_display'   => $badge_taxonomy_display,
		'read_more_text'           => $read_more_text,
		'filter_dropdown_taxonomy' => $enable_dropdown && ! empty( $dropdown_taxonomy ) ? $dropdown_taxonomy : '',
		'filter_by_acf_field'      => $dropdown_custom_field,
		'show_read_more'           => $show_read_more,
		'make_card_clickable'      => $make_card_clickable,
		'is_arabic'                => $is_arabic,
	);
	?>
	<div class="omsar-projects-widget omsar-bd-projects-widget<?php echo $is_arabic ? ' omsar-bd-rtl' : ''; ?>" id="<?php echo esc_attr( $widget_id ); ?>"<?php if ( $is_arabic ) : ?> dir="rtl"<?php endif; ?>>

		<?php if ( $enable_search || ( $enable_dropdown && ! empty( $dropdown_taxonomy ) ) ) : ?>
		<div class="omsar-style4-filters-container">
			<div class="omsar-style4-filters-row">
				<?php if ( $enable_search ) : ?>
				<div class="omsar-style4-filter-item omsar-search-filter-item">
					<?php if ( ! empty( $search_label ) ) : ?>
						<label for="<?php echo esc_attr( $widget_id ); ?>-search" class="omsar-style4-filter-label"><?php echo esc_html( $search_label ); ?></label>
					<?php endif; ?>
					<div class="omsar-posts-search-wrapper-inline">
						<input type="text"
							class="omsar-posts-search-input"
							id="<?php echo esc_attr( $widget_id ); ?>-search"
							placeholder="<?php echo esc_attr( $search_placeholder ); ?>"
							aria-label="<?php esc_attr_e( 'Search projects', 'omsar' ); ?>">
						<i class="bi bi-search omsar-search-icon" aria-hidden="true"></i>
					</div>
				</div>
				<?php endif; ?>

				<?php if ( $enable_dropdown && ! empty( $dropdown_taxonomy ) ) :
					$filter_terms = get_terms(
						array(
							'taxonomy'   => $dropdown_taxonomy,
							'hide_empty' => false,
							'orderby'    => 'name',
							'order'      => 'ASC',
						)
					);
					?>
				<div class="omsar-style4-filter-item omsar-dropdown-filter-item">
					<?php if ( ! empty( $dropdown_label ) ) : ?>
						<label for="<?php echo esc_attr( $widget_id ); ?>-dropdown-filter" class="omsar-style4-filter-label"><?php echo esc_html( $dropdown_label ); ?></label>
					<?php endif; ?>
					<div class="omsar-style4-dropdown-input-wrapper">
						<select class="omsar-style4-dropdown" id="<?php echo esc_attr( $widget_id ); ?>-dropdown-filter" aria-label="<?php esc_attr_e( 'Filter projects', 'omsar' ); ?>">
							<option value=""><?php echo function_exists( 'pll__' ) ? esc_html( pll__( 'All' ) ) : esc_html__( 'All', 'omsar' ); ?></option>
							<?php if ( ! is_wp_error( $filter_terms ) ) : ?>
								<?php foreach ( $filter_terms as $term ) : ?>
									<option value="<?php echo esc_attr( $term->term_id ); ?>"><?php echo esc_html( $term->name ); ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
						<i class="bi bi-chevron-down omsar-style4-dropdown-icon" aria-hidden="true"></i>
					</div>
				</div>
				<?php endif; ?>
			</div>
		</div>
		<?php endif; ?>

		<div class="omsar-projects-grid omsar-posts-grid style4"
			data-widget-id="<?php echo esc_attr( $widget_id ); ?>"
			data-post-type="<?php echo esc_attr( $post_type ); ?>"
			data-tab="all"
			data-orderby="<?php echo esc_attr( $orderby ); ?>"
			data-order="<?php echo esc_attr( $order ); ?>"
			data-listing-style="style4"
			data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>"
			data-current-page="1"
			data-total-posts="<?php echo (int) count( $all_posts ); ?>"
			data-default-image="<?php echo esc_url( $default_image ); ?>"
			data-badge-source="<?php echo esc_attr( $badge_source ); ?>"
			data-badge-taxonomy="<?php echo esc_attr( $badge_taxonomy ); ?>"
			data-badge-custom-field="<?php echo esc_attr( $badge_custom_field ); ?>"
			data-badge-taxonomy-display="<?php echo esc_attr( $badge_taxonomy_display ); ?>"
			data-read-more-text="<?php echo esc_attr( $read_more_text ); ?>"
			data-show-read-more="<?php echo $show_read_more ? 'yes' : 'no'; ?>"
			data-clickable-cards="<?php echo $make_card_clickable ? '1' : '0'; ?>"
			data-dropdown-filter-taxonomy="<?php echo esc_attr( $enable_dropdown && ! empty( $dropdown_taxonomy ) ? $dropdown_taxonomy : '' ); ?>"
			data-dropdown-filter-custom-field="<?php echo esc_attr( $enable_dropdown && ! empty( $dropdown_custom_field ) ? $dropdown_custom_field : '' ); ?>"
			style="grid-template-columns: repeat(<?php echo esc_attr( $columns ); ?>, 1fr);">
			<?php
			foreach ( $displayed_posts as $post ) {
				omsar_bd_projects_render_card( $post, $card_args );
			}
			?>
		</div>

		<?php if ( $enable_load_more && $has_more_posts ) : ?>
		<div class="omsar-load-more-wrapper" data-tab="all">
			<button type="button" class="omsar-load-more-btn" data-widget-id="<?php echo esc_attr( $widget_id ); ?>" data-tab="all">
				<?php echo esc_html( $load_more_text ); ?>
			</button>
		</div>
		<?php endif; ?>
	</div>
	<?php
}
