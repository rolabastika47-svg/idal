<?php
/**
 * Static Card List rendering (shared between Elementor widget and Breakdance element).
 *
 * @package OmsarBreakdanceElements
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<string, mixed>
 */
function omsar_bd_static_card_list_default_settings() {
	return array(
		'card_style'       => 'style1',
		'columns'          => '3',
		'columns_tablet'   => '2',
		'columns_mobile'   => '1',
		'cards'            => array(
			array(
				'title'              => __( 'Service One', 'omsar' ),
				'description'        => __( 'Brief description of the service offered.', 'omsar' ),
				'background_type'    => 'color',
				'background_color'   => '#6366f1',
				'gradient_color_from' => '#6366f1',
				'gradient_color_to'   => '#8b5cf6',
				'categories'         => array(
					array(
						'category_text'  => __( 'CATEGORY', 'omsar' ),
						'category_color' => '#ffffff',
					),
				),
				'button_text'        => __( 'Learn More', 'omsar' ),
				'button_link'        => '',
				'button_new_tab'     => false,
				'button_nofollow'    => false,
			),
			array(
				'title'              => __( 'Service Two', 'omsar' ),
				'description'        => __( 'Another service we provide to clients.', 'omsar' ),
				'background_type'    => 'color',
				'background_color'   => '#6366f1',
				'gradient_color_from' => '#6366f1',
				'gradient_color_to'   => '#8b5cf6',
				'categories'         => array(
					array(
						'category_text'  => __( 'CATEGORY', 'omsar' ),
						'category_color' => '#ffffff',
					),
				),
				'button_text'        => __( 'Learn More', 'omsar' ),
				'button_link'        => '',
				'button_new_tab'     => false,
				'button_nofollow'    => false,
			),
			array(
				'title'              => __( 'Service Three', 'omsar' ),
				'description'        => __( 'Additional offering for customers.', 'omsar' ),
				'background_type'    => 'color',
				'background_color'   => '#6366f1',
				'gradient_color_from' => '#6366f1',
				'gradient_color_to'   => '#8b5cf6',
				'categories'         => array(
					array(
						'category_text'  => __( 'CATEGORY', 'omsar' ),
						'category_color' => '#ffffff',
					),
				),
				'button_text'        => __( 'Learn More', 'omsar' ),
				'button_link'        => '',
				'button_new_tab'     => false,
				'button_nofollow'    => false,
			),
		),
	);
}

/**
 * @return array<string, mixed>
 */
function omsar_bd_static_card_list_default_content_properties() {
	$defaults = omsar_bd_static_card_list_default_settings();

	return array(
		'content' => array(
			'card_style'       => $defaults['card_style'],
			'columns'          => $defaults['columns'],
			'columns_tablet'   => $defaults['columns_tablet'],
			'columns_mobile'   => $defaults['columns_mobile'],
			'cards'            => $defaults['cards'],
		),
	);
}

/**
 * @param mixed $image
 * @return string
 */
function omsar_bd_static_card_list_image_url( $image ) {
	if ( is_string( $image ) ) {
		return $image;
	}

	if ( is_array( $image ) && ! empty( $image['url'] ) ) {
		return (string) $image['url'];
	}

	return '';
}

/**
 * @param array<string, mixed> $card
 * @return array<string, mixed>
 */
function omsar_bd_normalize_static_card_list_card( $card ) {
	if ( ! is_array( $card ) ) {
		return array();
	}

	$categories = array();
	if ( ! empty( $card['categories'] ) && is_array( $card['categories'] ) ) {
		foreach ( $card['categories'] as $category ) {
			if ( ! is_array( $category ) ) {
				continue;
			}
			$categories[] = array(
				'category_text'  => isset( $category['category_text'] ) ? (string) $category['category_text'] : '',
				'category_color' => ! empty( $category['category_color'] ) ? (string) $category['category_color'] : '#ffffff',
			);
		}
	}

	return array(
		'background_type'     => ! empty( $card['background_type'] ) ? (string) $card['background_type'] : 'color',
		'background_color'    => ! empty( $card['background_color'] ) ? (string) $card['background_color'] : '#6366f1',
		'background_image'    => omsar_bd_static_card_list_image_url( $card['background_image'] ?? '' ),
		'gradient_color_from' => ! empty( $card['gradient_color_from'] ) ? (string) $card['gradient_color_from'] : '#6366f1',
		'gradient_color_to'   => ! empty( $card['gradient_color_to'] ) ? (string) $card['gradient_color_to'] : '#8b5cf6',
		'title'               => isset( $card['title'] ) ? (string) $card['title'] : '',
		'description'         => isset( $card['description'] ) ? (string) $card['description'] : '',
		'categories'          => $categories,
		'button_text'         => isset( $card['button_text'] ) ? (string) $card['button_text'] : '',
		'button_link'         => isset( $card['button_link'] ) ? (string) $card['button_link'] : '',
		'button_new_tab'      => ! empty( $card['button_new_tab'] ),
		'button_nofollow'     => ! empty( $card['button_nofollow'] ),
	);
}

/**
 * @param array<string, mixed> $settings
 * @return array<string, mixed>
 */
function omsar_bd_normalize_static_card_list_settings( $settings ) {
	$defaults = omsar_bd_static_card_list_default_settings();
	$settings = wp_parse_args( is_array( $settings ) ? $settings : array(), $defaults );

	$cards = array();
	if ( ! empty( $settings['cards'] ) && is_array( $settings['cards'] ) ) {
		foreach ( $settings['cards'] as $card ) {
			$normalized = omsar_bd_normalize_static_card_list_card( $card );
			if ( ! empty( $normalized ) ) {
				$cards[] = $normalized;
			}
		}
	}

	$settings['cards'] = $cards;

	foreach ( array( 'columns', 'columns_tablet', 'columns_mobile' ) as $column_key ) {
		$settings[ $column_key ] = in_array( (string) $settings[ $column_key ], array( '1', '2', '3', '4' ), true )
			? (string) $settings[ $column_key ]
			: $defaults[ $column_key ];
	}

	$settings['card_style'] = in_array( $settings['card_style'], array( 'style1', 'style2' ), true )
		? $settings['card_style']
		: 'style1';

	return $settings;
}

/**
 * @param array<string, mixed> $properties_data
 * @return array<string, mixed>
 */
function omsar_bd_static_card_list_settings_from_breakdance( $properties_data ) {
	$content = $properties_data['content']['content'] ?? array();

	$settings = array(
		'card_style'       => $content['card_style'] ?? 'style1',
		'columns'          => $content['columns'] ?? '3',
		'columns_tablet'   => $content['columns_tablet'] ?? '2',
		'columns_mobile'   => $content['columns_mobile'] ?? '1',
		'cards'            => $content['cards'] ?? array(),
	);

	return omsar_bd_normalize_static_card_list_settings( $settings );
}

/**
 * @param array<string, mixed> $settings
 * @param string               $element_id
 */
function omsar_bd_render_static_card_list_widget( $settings, $element_id ) {
	$settings = omsar_bd_normalize_static_card_list_settings( $settings );

	$cards          = $settings['cards'];
	$card_style     = $settings['card_style'];
	$columns        = $settings['columns'];
	$columns_tablet = $settings['columns_tablet'];
	$columns_mobile = $settings['columns_mobile'];

	$widget_id = 'omsar-static-card-list-' . sanitize_html_class( $element_id );
	?>
	<style>
		#<?php echo esc_attr( $widget_id ); ?> .omsar-static-card-list {
			display: grid;
			grid-template-columns: repeat(<?php echo esc_attr( $columns ); ?>, 1fr);
		}

		@media (max-width: 1024px) {
			#<?php echo esc_attr( $widget_id ); ?> .omsar-static-card-list {
				grid-template-columns: repeat(<?php echo esc_attr( $columns_tablet ); ?>, 1fr);
			}
		}

		@media (max-width: 767px) {
			#<?php echo esc_attr( $widget_id ); ?> .omsar-static-card-list {
				grid-template-columns: repeat(<?php echo esc_attr( $columns_mobile ); ?>, 1fr);
			}
		}
	</style>

	<div id="<?php echo esc_attr( $widget_id ); ?>" class="omsar-static-card-list-wrapper">
		<div class="omsar-static-card-list omsar-static-card-list-<?php echo esc_attr( $card_style ); ?>">
			<?php foreach ( $cards as $index => $card ) : ?>
				<?php
				$card_item_id     = $widget_id . '-card-' . $index;
				$background_type  = $card['background_type'];
				$background_color = $card['background_color'];
				$background_image = $card['background_image'];
				$gradient_from    = $card['gradient_color_from'];
				$gradient_to      = $card['gradient_color_to'];
				$title            = $card['title'];
				$description      = $card['description'];
				$categories       = $card['categories'];
				$button_text      = $card['button_text'];
				$button_link      = $card['button_link'];
				$button_target    = ! empty( $card['button_new_tab'] ) ? ' target="_blank"' : '';
				$button_nofollow  = ! empty( $card['button_nofollow'] ) ? ' rel="nofollow"' : '';

				$background_style = '';
				if ( 'style2' === $card_style ) {
					if ( 'image' === $background_type && $background_image ) {
						$background_style = 'background-image: url(' . esc_url( $background_image ) . '); background-size: cover; background-position: center;';
					} elseif ( 'color' === $background_type ) {
						$background_style = 'background: linear-gradient(180deg, ' . esc_attr( $gradient_from ) . ' 0%, ' . esc_attr( $gradient_to ) . ' 100%);';
					}
				}
				?>
				<?php if ( 'style1' === $card_style && ( $background_image || $background_color ) ) : ?>
					<style>
						#<?php echo esc_attr( $card_item_id ); ?>::before {
							<?php if ( 'image' === $background_type && $background_image ) : ?>
								background-image: url(<?php echo esc_url( $background_image ); ?>);
								background-size: cover;
								background-position: center;
							<?php else : ?>
								background-color: <?php echo esc_attr( $background_color ); ?>;
							<?php endif; ?>
						}
					</style>
				<?php endif; ?>
				<div id="<?php echo esc_attr( $card_item_id ); ?>" class="omsar-static-card-list-item" style="<?php echo esc_attr( $background_style ); ?>">
					<?php if ( 'style1' === $card_style ) : ?>
						<div class="omsar-static-card-list-item-content">
							<?php if ( $title ) : ?>
								<h3 class="omsar-static-card-list-item-title"><?php echo esc_html( $title ); ?></h3>
							<?php endif; ?>
							<?php if ( $description ) : ?>
								<p class="omsar-static-card-list-item-description"><?php echo esc_html( $description ); ?></p>
							<?php endif; ?>
						</div>
					<?php else : ?>
						<div class="omsar-static-card-list-item-overlay">
							<?php if ( ! empty( $categories ) ) : ?>
								<div class="omsar-static-card-list-item-categories">
									<?php foreach ( $categories as $category ) : ?>
										<span class="omsar-static-card-list-item-category" style="color: <?php echo esc_attr( $category['category_color'] ); ?>;">
											<?php echo esc_html( $category['category_text'] ); ?>
										</span>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
							<?php if ( $title ) : ?>
								<h3 class="omsar-static-card-list-item-title"><?php echo esc_html( $title ); ?></h3>
							<?php endif; ?>
							<?php if ( $description ) : ?>
								<p class="omsar-static-card-list-item-description"><?php echo esc_html( $description ); ?></p>
							<?php endif; ?>
							<?php if ( $button_text && $button_link ) : ?>
								<a href="<?php echo esc_url( $button_link ); ?>" class="omsar-static-card-list-item-button"<?php echo $button_target . $button_nofollow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
									<?php echo esc_html( $button_text ); ?> →
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}
