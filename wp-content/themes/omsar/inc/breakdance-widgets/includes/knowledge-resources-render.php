<?php
/**
 * Breakdance Knowledge and Resources element rendering (shared with Elementor widget).
 *
 * @package OmsarBreakdanceElements
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default settings aligned with the Elementor Knowledge and Resources widget.
 *
 * @return array<string, mixed>
 */
function omsar_bd_knowledge_resources_default_settings() {
	$search_placeholder = function_exists( 'pll__' ) ? pll__( 'Search posts...' ) : __( 'Search posts...', 'omsar' );

	return array(
		'posts_per_page'     => 6,
		'columns'            => '2',
		'orderby'            => 'date',
		'order'              => 'DESC',
		'default_image'      => array(
			'url' => '',
			'id'  => 0,
		),
		'search_placeholder' => $search_placeholder,
		'search_align'       => 'left',
		'cards_clickable'    => false,
	);
}

/**
 * @param array<string, mixed> $settings Raw settings.
 * @return array<string, mixed>
 */
function omsar_bd_normalize_knowledge_resources_settings( $settings ) {
	$defaults = omsar_bd_knowledge_resources_default_settings();
	$settings = wp_parse_args( is_array( $settings ) ? $settings : array(), $defaults );

	$settings['posts_per_page'] = max( 1, min( 100, (int) $settings['posts_per_page'] ) );
	$settings['columns']        = (string) ( $settings['columns'] ?? '2' );

	if ( isset( $settings['cards_clickable'] ) && ( $settings['cards_clickable'] === 'yes' || $settings['cards_clickable'] === true || $settings['cards_clickable'] === '1' || $settings['cards_clickable'] === 1 ) ) {
		$settings['cards_clickable'] = true;
	} else {
		$settings['cards_clickable'] = ! empty( $settings['cards_clickable'] );
	}

	if ( is_string( $settings['default_image'] ) ) {
		$settings['default_image'] = array( 'url' => $settings['default_image'] );
	}

	$allowed_align = array( 'left', 'center', 'right' );
	if ( ! in_array( $settings['search_align'], $allowed_align, true ) ) {
		$settings['search_align'] = 'left';
	}

	return $settings;
}

/**
 * @param array<string, mixed> $properties_data Breakdance properties.
 * @return array<string, mixed>
 */
function omsar_bd_knowledge_resources_settings_from_breakdance( $properties_data ) {
	$content = $properties_data['content']['content'] ?? array();

	$default_image = $content['default_image'] ?? array();
	if ( is_string( $default_image ) ) {
		$default_image = array( 'url' => $default_image );
	}

	$settings = array(
		'posts_per_page'     => isset( $content['posts_per_page'] ) ? (int) $content['posts_per_page'] : 6,
		'columns'            => (string) ( $content['columns'] ?? '2' ),
		'orderby'            => $content['orderby'] ?? 'date',
		'order'              => $content['order'] ?? 'DESC',
		'default_image'      => $default_image,
		'search_placeholder' => isset( $content['search_placeholder'] ) ? trim( (string) $content['search_placeholder'] ) : '',
		'search_align'       => $content['search_align'] ?? 'left',
		'cards_clickable'    => ! empty( $content['cards_clickable'] ),
	);

	$settings = omsar_bd_normalize_knowledge_resources_settings( $settings );

	// Breakdance instances often saved with "center"; Elementor layout uses left-aligned compact search.
	if ( ( $settings['search_align'] ?? '' ) === 'center' ) {
		$settings['search_align'] = 'left';
	}

	return $settings;
}

/**
 * @param array<string, mixed> $settings Elementor widget settings.
 * @return array<string, mixed>
 */
function omsar_bd_knowledge_resources_settings_from_elementor( $settings ) {
	$mapped = array(
		'posts_per_page'     => isset( $settings['posts_per_page'] ) ? (int) $settings['posts_per_page'] : 6,
		'columns'            => (string) ( $settings['columns'] ?? '2' ),
		'orderby'            => $settings['orderby'] ?? 'date',
		'order'              => $settings['order'] ?? 'DESC',
		'default_image'      => $settings['default_image'] ?? array(),
		'search_placeholder' => isset( $settings['search_placeholder'] ) ? trim( (string) $settings['search_placeholder'] ) : '',
		'search_align'       => $settings['search_align'] ?? 'left',
		'cards_clickable'    => isset( $settings['cards_clickable'] ) && $settings['cards_clickable'] === 'yes',
	);

	return omsar_bd_normalize_knowledge_resources_settings( $mapped );
}

/**
 * @return array<string, string>
 */
function omsar_bd_knowledge_resources_labels() {
	return array(
		'downloads'   => function_exists( 'pll__' ) ? pll__( 'Downloads' ) : __( 'Downloads', 'omsar' ),
		'load_more'   => function_exists( 'pll__' ) ? pll__( 'Load More' ) : __( 'Load More', 'omsar' ),
		'no_results'  => function_exists( 'pll__' ) ? pll__( 'No knowledge and resources are available at this time.' ) : __( 'No knowledge and resources are available at this time.', 'omsar' ),
	);
}

/**
 * Normalize ACF file field (ID, array, or object) to url + label.
 *
 * @param mixed $file Raw value from document_resource.
 * @return array{url: string, label: string}|null
 */
function omsar_bd_knowledge_resources_normalize_file( $file ) {
	$id = null;

	if ( is_numeric( $file ) && $file > 0 ) {
		$id = (int) $file;
	} elseif ( is_array( $file ) ) {
		if ( ! empty( $file['ID'] ) ) {
			$id = (int) $file['ID'];
		} elseif ( ! empty( $file['id'] ) ) {
			$id = (int) $file['id'];
		}
		if ( $id && ! empty( $file['url'] ) ) {
			$url   = $file['url'];
			$label = ! empty( $file['title'] ) ? $file['title'] : ( ! empty( $file['filename'] ) ? $file['filename'] : basename( $url ) );
			return array(
				'url'   => $url,
				'label' => $label ?: __( 'Download', 'omsar' ),
			);
		}
	} elseif ( is_object( $file ) && isset( $file->ID ) ) {
		$id = (int) $file->ID;
	}

	if ( ! $id ) {
		return null;
	}

	$url = wp_get_attachment_url( $id );
	if ( ! $url ) {
		return null;
	}

	$title    = get_the_title( $id );
	$filename = basename( get_attached_file( $id ) );
	$label    = ( $title && $title !== 'Attachment' ) ? $title : ( $filename ?: __( 'Download', 'omsar' ) );

	return array(
		'url'   => $url,
		'label' => $label,
	);
}

/**
 * Get documents for a knowledge resource post.
 *
 * @param int $post_id Post ID.
 * @return array<int, array{url: string, label: string}>
 */
function omsar_bd_knowledge_resources_get_documents( $post_id ) {
	$documents = array();

	if ( ! function_exists( 'have_rows' ) || ! have_rows( 'resources_repeater', $post_id ) ) {
		return $documents;
	}

	while ( have_rows( 'resources_repeater', $post_id ) ) {
		the_row();
		$file = get_sub_field( 'document_resource' );
		if ( ! $file ) {
			continue;
		}
		$doc = omsar_bd_knowledge_resources_normalize_file( $file );
		if ( $doc ) {
			$documents[] = $doc;
		}
	}

	return $documents;
}

/**
 * Return HTML for a single Knowledge and Resources card.
 *
 * @param int                  $post_id         Post ID.
 * @param array<string, mixed> $settings        Normalized settings.
 * @param string               $downloads_label Downloads label.
 * @return string
 */
function omsar_bd_get_knowledge_resource_card_html( $post_id, $settings, $downloads_label = '' ) {
	$default_image   = '';
	if ( is_array( $settings['default_image'] ) && ! empty( $settings['default_image']['url'] ) ) {
		$default_image = $settings['default_image']['url'];
	} elseif ( is_string( $settings['default_image'] ) ) {
		$default_image = $settings['default_image'];
	}

	$cards_clickable = ! empty( $settings['cards_clickable'] );
	$downloads_label = $downloads_label ?: ( function_exists( 'pll__' ) ? pll__( 'Downloads' ) : __( 'Downloads', 'omsar' ) );

	$image_url = '';
	if ( has_post_thumbnail( $post_id ) ) {
		$image_url = get_the_post_thumbnail_url( $post_id, 'large' );
	}
	if ( ! $image_url && $default_image ) {
		$image_url = $default_image;
	}

	$title     = get_the_title( $post_id );
	$documents = omsar_bd_knowledge_resources_get_documents( $post_id );

	$card_class = 'omsar-kr-card';
	if ( ! $image_url ) {
		$card_class .= ' omsar-kr-card--no-image';
	}
	if ( $cards_clickable ) {
		$card_class .= ' omsar-kr-card--clickable';
	}
	$card_attrs = $cards_clickable ? ' data-href="' . esc_url( get_permalink( $post_id ) ) . '"' : '';

	ob_start();
	?>
	<article class="<?php echo esc_attr( $card_class ); ?>"<?php echo $card_attrs; ?>>
		<?php if ( $image_url ) : ?>
			<div class="omsar-kr-card-image">
				<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" />
			</div>
		<?php endif; ?>
		<div class="omsar-kr-card-content">
			<h3 class="omsar-kr-card-title"><?php echo esc_html( $title ); ?></h3>
			<?php if ( ! empty( $documents ) ) : ?>
				<div class="omsar-kr-downloads">
					<span class="omsar-kr-downloads-label"><?php echo esc_html( $downloads_label ); ?>:</span>
					<ul class="omsar-kr-download-list">
						<?php foreach ( $documents as $doc ) : ?>
							<li>
								<a href="<?php echo esc_url( $doc['url'] ); ?>" class="omsar-kr-download-link" target="_blank" rel="noopener noreferrer" download>
									<i class="bi bi-file-earmark-pdf" aria-hidden="true"></i>
									<?php echo esc_html( $doc['label'] ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
		</div>
	</article>
	<?php
	return (string) ob_get_clean();
}

/**
 * Render the Knowledge and Resources widget markup.
 *
 * @param array<string, mixed> $settings              Normalized settings.
 * @param string               $element_id            Unique element ID.
 * @param string               $wrapper_extra_class   Optional extra wrapper class.
 */
function omsar_bd_render_knowledge_resources_widget( $settings, $element_id, $wrapper_extra_class = '' ) {
	$settings = omsar_bd_normalize_knowledge_resources_settings( $settings );

	$posts_per_page = (int) $settings['posts_per_page'];
	$columns        = (string) $settings['columns'];
	$orderby        = (string) $settings['orderby'];
	$order          = (string) $settings['order'];
	$default_image  = '';
	if ( is_array( $settings['default_image'] ) && ! empty( $settings['default_image']['url'] ) ) {
		$default_image = $settings['default_image']['url'];
	} elseif ( is_string( $settings['default_image'] ) ) {
		$default_image = $settings['default_image'];
	}
	$search_placeholder = ! empty( $settings['search_placeholder'] )
		? $settings['search_placeholder']
		: ( function_exists( 'pll__' ) ? pll__( 'Search posts...' ) : __( 'Search posts...', 'omsar' ) );
	$search_align    = (string) $settings['search_align'];
	$cards_clickable = ! empty( $settings['cards_clickable'] );

	$current_lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
	$is_arabic    = ( $current_lang === 'ar' || is_rtl() );

	$query_args = array(
		'post_type'      => 'knowledge_resources',
		'posts_per_page' => $posts_per_page,
		'orderby'        => $orderby,
		'order'          => $order,
		'post_status'    => 'publish',
		'paged'          => 1,
		'no_found_rows'  => false,
	);

	if ( function_exists( 'pll_current_language' ) ) {
		$query_args['lang'] = pll_current_language();
	}

	$query       = new WP_Query( $query_args );
	$total_found = (int) $query->found_posts;
	$has_more    = $total_found > $posts_per_page;
	$labels      = omsar_bd_knowledge_resources_labels();

	$rtl_class = $is_arabic ? ' omsar-kr-rtl omsar-bd-rtl' : '';
	$extra     = trim( $wrapper_extra_class );
	?>
	<div class="omsar-knowledge-resources-widget omsar-bd-knowledge-resources-widget<?php echo esc_attr( $rtl_class ); ?><?php echo $extra ? ' ' . esc_attr( $extra ) : ''; ?>"
		<?php if ( $is_arabic ) : ?>dir="rtl"<?php endif; ?>
		data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>"
		data-columns="<?php echo esc_attr( $columns ); ?>"
		data-orderby="<?php echo esc_attr( $orderby ); ?>"
		data-order="<?php echo esc_attr( $order ); ?>"
		data-default-image="<?php echo esc_attr( $default_image ); ?>"
		data-search-placeholder="<?php echo esc_attr( $search_placeholder ); ?>"
		data-cards-clickable="<?php echo $cards_clickable ? '1' : '0'; ?>">
		<div class="omsar-kr-search-wrapper omsar-kr-search-align-<?php echo esc_attr( $search_align ); ?>" data-search-align="<?php echo esc_attr( $search_align ); ?>">
			<div class="omsar-kr-search-inner">
				<input type="search" class="omsar-kr-search-input" placeholder="<?php echo esc_attr( $search_placeholder ); ?>" autocomplete="off" aria-label="<?php echo esc_attr( $search_placeholder ); ?>" />
				<i class="bi bi-search omsar-kr-search-icon" aria-hidden="true"></i>
			</div>
		</div>
		<div class="omsar-kr-list omsar-kr-grid" style="grid-template-columns: repeat(<?php echo esc_attr( (int) $columns ); ?>, 1fr); --omsar-kr-columns: <?php echo esc_attr( (int) $columns ); ?>;" data-widget-id="<?php echo esc_attr( $element_id ); ?>">
			<?php
			$has_posts = $query->have_posts();
			if ( $has_posts ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					echo omsar_bd_get_knowledge_resource_card_html( get_the_ID(), $settings, $labels['downloads'] );
				}
				wp_reset_postdata();
			}
			?>
		</div>
		<div class="omsar-kr-no-results"<?php echo $has_posts ? ' style="display: none;"' : ''; ?>>
			<p class="omsar-kr-no-results-message"><?php echo esc_html( $labels['no_results'] ); ?></p>
		</div>
		<div class="omsar-kr-load-more-wrapper"<?php echo $has_more ? '' : ' style="display: none;"'; ?>>
			<button type="button" class="omsar-load-more-btn omsar-kr-load-more-btn"
				data-page="1"
				data-per-page="<?php echo esc_attr( $posts_per_page ); ?>"
				data-orderby="<?php echo esc_attr( $orderby ); ?>"
				data-order="<?php echo esc_attr( $order ); ?>"
				data-columns="<?php echo esc_attr( $columns ); ?>"
				data-default-image="<?php echo esc_attr( $default_image ); ?>"
				data-cards-clickable="<?php echo $cards_clickable ? '1' : '0'; ?>"
				data-total="<?php echo esc_attr( $total_found ); ?>">
				<?php echo esc_html( $labels['load_more'] ); ?>
			</button>
		</div>
	</div>
	<?php
}
