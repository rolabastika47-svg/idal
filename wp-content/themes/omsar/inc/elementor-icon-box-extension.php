<?php
/**
 * Elementor Icon Box Widget Extension
 * 
 * Extends the Elementor Icon Box widget with:
 * - Background Color control for SVG icon
 * - Border Radius control for SVG icon container
 * 
 * @package OMSAR
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Check if Elementor is installed and active
 */
if ( ! did_action( 'elementor/loaded' ) ) {
	return;
}

/**
 * Add custom controls to Icon Box widget
 * 
 * @param \Elementor\Widget_Base $widget The widget instance
 * @param string $section_id The section ID
 * @param array $args Section arguments
 */
function omsar_extend_icon_box_controls( $widget, $section_id, $args ) {
	// Only modify the Icon Box widget
	if ( 'icon-box' !== $widget->get_name() ) {
		return;
	}

	// Add controls to the Content tab (icon_section) or Style tab (icon_style_section)
	// Try multiple section IDs for compatibility with different Elementor versions
	$content_sections = [ 'icon_section', 'section_icon' ];
	$style_sections = [ 'icon_style_section', 'section_style_icon', 'section_icon_style' ];
	
	// Add to Content tab
	if ( in_array( $section_id, $content_sections, true ) ) {
		$widget->add_control(
			'icon_svg_bg_color',
			[
				'label' => esc_html__( 'Icon Background Color', 'omsar' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-box-icon .elementor-icon' => 'background-color: {{VALUE}} !important;',
				],
				'condition' => [
					'selected_icon[value]!' => '',
				],
				'separator' => 'before',
			]
		);

		$widget->add_control(
			'icon_svg_border_radius',
			[
				'label' => esc_html__( 'Icon Border Radius', 'omsar' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-box-icon .elementor-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);
	}

	// Also add to Style tab as an alternative location
	if ( in_array( $section_id, $style_sections, true ) ) {
		$widget->add_control(
			'icon_svg_bg_color_style',
			[
				'label' => esc_html__( 'Icon Background Color', 'omsar' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-box-icon .elementor-icon' => 'background-color: {{VALUE}} !important;',
				],
				'condition' => [
					'selected_icon[value]!' => '',
				],
				'separator' => 'before',
			]
		);

		$widget->add_control(
			'icon_svg_border_radius_style',
			[
				'label' => esc_html__( 'Icon Border Radius', 'omsar' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-box-icon .elementor-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);
	}
}
add_action( 'elementor/element/before_section_end', 'omsar_extend_icon_box_controls', 10, 3 );

/**
 * Helper function to get icon box custom settings
 * 
 * @param array $settings Widget settings
 * @return array Array with bg_color and border_radius
 */
function omsar_get_icon_box_custom_styles( $settings ) {
	$styles = [
		'bg_color' => '',
		'border_radius' => '',
	];

	// Get background color (check both control names)
	if ( ! empty( $settings['icon_svg_bg_color'] ) ) {
		$styles['bg_color'] = $settings['icon_svg_bg_color'];
	} elseif ( ! empty( $settings['icon_svg_bg_color_style'] ) ) {
		$styles['bg_color'] = $settings['icon_svg_bg_color_style'];
	}

	// Get border radius (check both control names)
	$border_radius_values = null;
	if ( ! empty( $settings['icon_svg_border_radius'] ) ) {
		$border_radius_values = $settings['icon_svg_border_radius'];
	} elseif ( ! empty( $settings['icon_svg_border_radius_style'] ) ) {
		$border_radius_values = $settings['icon_svg_border_radius_style'];
	}

	// Process border radius dimensions
	if ( $border_radius_values && isset( $border_radius_values['top'] ) && isset( $border_radius_values['unit'] ) ) {
		$unit = $border_radius_values['unit'];
		$top = ! empty( $border_radius_values['top'] ) ? $border_radius_values['top'] : 0;
		$right = ! empty( $border_radius_values['right'] ) ? $border_radius_values['right'] : 0;
		$bottom = ! empty( $border_radius_values['bottom'] ) ? $border_radius_values['bottom'] : 0;
		$left = ! empty( $border_radius_values['left'] ) ? $border_radius_values['left'] : 0;
		$styles['border_radius'] = sprintf( '%s%s %s%s %s%s %s%s', $top, $unit, $right, $unit, $bottom, $unit, $left, $unit );
	}

	return $styles;
}

/**
 * Add inline CSS to prevent background from affecting parent containers
 * Only ensures parent elements don't get background color
 */
function omsar_add_icon_box_css() {
	// Only add CSS if Elementor is active
	if ( ! did_action( 'elementor/loaded' ) ) {
		return;
	}
	?>
	<style id="omsar-icon-box-extension-css">
		/* Prevent background from affecting parent containers */
		.elementor-widget-icon-box .elementor-icon-box-icon {
			background-color: transparent !important;
		}
		.elementor-widget-icon-box .elementor-icon-box-content {
			background-color: transparent !important;
		}
	</style>
	<?php
}
add_action( 'wp_head', 'omsar_add_icon_box_css', 100 );

/**
 * Modify the widget content output to apply inline styles
 * This ensures the styles are applied directly to the icon element
 * 
 * @param string $content The widget content
 * @param \Elementor\Widget_Base $widget The widget instance
 * @return string Modified content
 */
function omsar_modify_icon_box_output( $content, $widget ) {
	// Only process Icon Box widgets
	if ( 'icon-box' !== $widget->get_name() ) {
		return $content;
	}

	$settings = $widget->get_settings_for_display();
	$styles = omsar_get_icon_box_custom_styles( $settings );

	// Build inline style string - only background and border radius
	$inline_styles = [];
	
	if ( ! empty( $styles['bg_color'] ) ) {
		$inline_styles[] = 'background-color: ' . esc_attr( $styles['bg_color'] ) . ';';
	}
	if ( ! empty( $styles['border_radius'] ) ) {
		$inline_styles[] = 'border-radius: ' . esc_attr( $styles['border_radius'] ) . ';';
	}

	// Apply styles to icon element in the content
	// Only target the icon container that's inside .elementor-icon-box-icon
	if ( ! empty( $inline_styles ) ) {
		$style_string = implode( ' ', $inline_styles );
		
		// Find the icon element that's within the icon-box-icon container
		// Match pattern: elementor-icon-box-icon > elementor-icon
		$content = preg_replace_callback(
			'/(<(?:div|span|i)[^>]*class="[^"]*elementor-icon[^"]*"[^>]*)(>)/i',
			function( $matches ) use ( $style_string, $content ) {
				// Check if this icon is within icon-box-icon container
				$match_pos = strpos( $content, $matches[0] );
				if ( $match_pos === false ) {
					return $matches[0];
				}
				
				// Get content before this match
				$before_match = substr( $content, 0, $match_pos );
				
				// Find the last opening of icon-box-icon before this icon
				$last_icon_box_icon = strrpos( $before_match, 'elementor-icon-box-icon' );
				$last_icon_box_content = strrpos( $before_match, 'elementor-icon-box-content' );
				
				// Only apply if icon-box-icon appears before icon-box-content (meaning icon is in icon container)
				if ( $last_icon_box_icon === false || ( $last_icon_box_content !== false && $last_icon_box_content > $last_icon_box_icon ) ) {
					return $matches[0]; // Not in icon container, skip
				}
				
				$tag = $matches[1];
				$closing = $matches[2];
				
				// Check if style attribute already exists
				if ( preg_match( '/style\s*=\s*["\']([^"\']*)["\']/', $tag, $style_matches ) ) {
					// Merge styles intelligently
					$existing_style = $style_matches[1];
					$existing_parts = explode( ';', $existing_style );
					$new_parts = explode( ';', $style_string );
					$existing_props = [];
					
					// Parse existing styles
					foreach ( $existing_parts as $part ) {
						$part = trim( $part );
						if ( ! empty( $part ) && preg_match( '/^([^:]+):(.+)$/', $part, $prop_matches ) ) {
							$prop = trim( $prop_matches[1] );
							$existing_props[ $prop ] = trim( $prop_matches[2] );
						}
					}
					
					// Add new styles, overriding existing ones
					foreach ( $new_parts as $part ) {
						$part = trim( $part );
						if ( ! empty( $part ) && preg_match( '/^([^:]+):(.+)$/', $part, $prop_matches ) ) {
							$prop = trim( $prop_matches[1] );
							$existing_props[ $prop ] = trim( $prop_matches[2] );
						}
					}
					
					// Rebuild style string
					$merged_style = '';
					foreach ( $existing_props as $prop => $value ) {
						$merged_style .= $prop . ': ' . $value . '; ';
					}
					$merged_style = trim( $merged_style );
					
					$tag = preg_replace( '/style\s*=\s*["\'][^"\']*["\']/', 'style="' . esc_attr( $merged_style ) . '"', $tag );
				} else {
					// Add new style attribute
					$tag .= ' style="' . esc_attr( $style_string ) . '"';
				}
				
				return $tag . $closing;
			},
			$content
		);
	}

	return $content;
}
add_filter( 'elementor/widget/render_content', 'omsar_modify_icon_box_output', 10, 2 );

