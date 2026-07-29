<?php
/**
 * Elementor Custom Table Widget Class
 * 
 * Displays a customizable table with configurable columns and rows
 * 
 * @package OMSAR
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

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class OMSAR_Data_Table_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'omsar_data_table';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Data Table', 'omsar' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'eicon-table';
	}

	/**
	 * Get widget categories.
	 */
	public function get_categories() {
		return [ 'omsar-elements' ];
	}

	/**
	 * Get widget keywords.
	 */
	public function get_keywords() {
		return [ 'table', 'data table', 'grid', 'data', 'rows', 'columns' ];
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {

		// Content Section
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Table Content', 'omsar' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'table_title',
			[
				'label' => esc_html__( 'Table Title', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Table title goes here', 'omsar' ),
				'placeholder' => esc_html__( 'Enter table title', 'omsar' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'show_export_icon',
			[
				'label' => esc_html__( 'Show Export Icon', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'omsar' ),
				'label_off' => esc_html__( 'Hide', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'no',
				'description' => esc_html__( 'Display an icon to export the table as CSV or image', 'omsar' ),
			]
		);

		$this->add_control(
			'show_zoom_icon',
			[
				'label' => esc_html__( 'Show Zoom/Expand Icon', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'omsar' ),
				'label_off' => esc_html__( 'Hide', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'no',
				'description' => esc_html__( 'Display an icon to open the table in full view/zoom mode', 'omsar' ),
			]
		);

		// Create repeater for column labels (only labels, no nested values)
		$column_repeater = new Repeater();

		$column_repeater->add_control(
			'column_label',
			[
				'label' => esc_html__( 'Column Label', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Column', 'omsar' ),
				'placeholder' => esc_html__( 'Enter column label', 'omsar' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'table_columns',
			[
				'label' => esc_html__( 'Table Columns', 'omsar' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $column_repeater->get_controls(),
				'default' => [
					[
						'column_label' => 'Column 1',
					],
					[
						'column_label' => 'Column 2',
					],
					[
						'column_label' => 'Column 3',
					],
				],
				'title_field' => '{{{ column_label }}}',
				'description' => esc_html__( 'Add column labels for your table. Each column represents one field in your data rows.', 'omsar' ),
			]
		);

		// Create repeater for rows with flat structure (no nested repeaters)
		// Each row has a single field with pipe-separated values that dynamically matches column count
		$row_repeater = new Repeater();

		$row_repeater->add_control(
			'row_values',
			[
				'label' => esc_html__( 'Row Values', 'omsar' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => '',
				'placeholder' => esc_html__( 'Value 1 | Value 2 | Value 3', 'omsar' ),
				'description' => esc_html__( 'Enter values separated by pipe (|). Each value corresponds to one column in order. Example: "Value 1 | Value 2 | Value 3"', 'omsar' ),
				'rows' => 3,
				'label_block' => true,
			]
		);

		$this->add_control(
			'table_rows',
			[
				'label' => esc_html__( 'Table Rows', 'omsar' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $row_repeater->get_controls(),
				'default' => [
					[
						'row_values' => 'Data 1 | Data 2 | Data 3',
					],
					[
						'row_values' => 'Data 4 | Data 5 | Data 6',
					],
				],
				'title_field' => '{{{ row_values }}}',
				'description' => esc_html__( 'Add rows to your table. Each row represents one record. Enter values separated by pipe (|) - the number of values should match the number of columns.', 'omsar' ),
			]
		);

		$this->end_controls_section();

		// Style Section - Title
		$this->start_controls_section(
			'style_title_section',
			[
				'label' => esc_html__( 'Title Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .omsar-table-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-table-title',
				'default' => [
					'font_weight' => '600',
					'font_size' => [
						'size' => '20',
						'unit' => 'px',
					],
				],
			]
		);

		$this->add_control(
			'title_alignment',
			[
				'label' => esc_html__( 'Alignment', 'omsar' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'omsar' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'omsar' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'omsar' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'left',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .omsar-table-title' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_margin',
			[
				'label' => esc_html__( 'Margin Bottom', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-table-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Style Section - Table Header
		$this->start_controls_section(
			'style_header_section',
			[
				'label' => esc_html__( 'Table Header', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'header_background',
			[
				'label' => esc_html__( 'Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f8f9fa',
				'selectors' => [
					'{{WRAPPER}} .omsar-table thead th' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'header_text_color',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .omsar-table thead th' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'header_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-table thead th',
			]
		);

		$this->add_control(
			'header_padding',
			[
				'label' => esc_html__( 'Padding', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '12',
					'right' => '16',
					'bottom' => '12',
					'left' => '16',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-table thead th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Style Section - Table Body
		$this->start_controls_section(
			'style_body_section',
			[
				'label' => esc_html__( 'Table Body', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'cell_text_color',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .omsar-table tbody td' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cell_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-table tbody td',
			]
		);

		$this->add_control(
			'cell_padding',
			[
				'label' => esc_html__( 'Cell Padding', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '12',
					'right' => '16',
					'bottom' => '12',
					'left' => '16',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-table tbody td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'row_border_color',
			[
				'label' => esc_html__( 'Row Border Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#e9ecef',
				'selectors' => [
					'{{WRAPPER}} .omsar-table tbody tr' => 'border-bottom-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'row_border_width',
			[
				'label' => esc_html__( 'Row Border Width', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-table tbody tr' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Style Section - Table Container
		$this->start_controls_section(
			'style_container_section',
			[
				'label' => esc_html__( 'Table Container', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'container_padding',
			[
				'label' => esc_html__( 'Padding', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '20',
					'right' => '20',
					'bottom' => '20',
					'left' => '20',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-table-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'container_background',
			[
				'label' => esc_html__( 'Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-table-wrapper' => 'background-color: {{VALUE}};',
				],
			]
		);

		// Border Style
		$this->add_control(
			'container_border_style',
			[
				'label' => esc_html__( 'Border Style', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => esc_html__( 'None', 'omsar' ),
					'solid' => esc_html__( 'Solid', 'omsar' ),
					'dashed' => esc_html__( 'Dashed', 'omsar' ),
					'dotted' => esc_html__( 'Dotted', 'omsar' ),
					'double' => esc_html__( 'Double', 'omsar' ),
					'groove' => esc_html__( 'Groove', 'omsar' ),
					'ridge' => esc_html__( 'Ridge', 'omsar' ),
					'inset' => esc_html__( 'Inset', 'omsar' ),
					'outset' => esc_html__( 'Outset', 'omsar' ),
				],
				'default' => 'none',
				'selectors' => [
					'{{WRAPPER}} .omsar-table-wrapper' => 'border-style: {{VALUE}};',
				],
			]
		);

		// Border Width
		$this->add_control(
			'container_border_width',
			[
				'label' => esc_html__( 'Border Width', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default' => [
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-table-wrapper' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'container_border_style!' => 'none',
				],
			]
		);

		// Border Color
		$this->add_control(
			'container_border_color',
			[
				'label' => esc_html__( 'Border Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#dddddd',
				'selectors' => [
					'{{WRAPPER}} .omsar-table-wrapper' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'container_border_style!' => 'none',
				],
			]
		);

		$this->add_control(
			'container_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 8,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-table-wrapper' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		// Box Shadow
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'container_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-table-wrapper',
			]
		);

		$this->end_controls_section();

		// Advanced Section
		$this->start_controls_section(
			'advanced_section',
			[
				'label' => esc_html__( 'Advanced', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'table_width',
			[
				'label' => esc_html__( 'Table Width', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 2000,
						'step' => 10,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-table-wrapper' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'margin',
			[
				'label' => esc_html__( 'Margin', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .omsar-table-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Migrate old column-based structure to new flat row-based structure.
	 * 
	 * Converts:
	 * - Old: columns → column_values (repeater) → row_value
	 * - New: rows → col_1_value, col_2_value, col_3_value, etc.
	 * 
	 * @param array $settings Widget settings.
	 * @return array Migrated settings.
	 */
	protected function migrate_old_structure( $settings ) {
		// Ensure settings is an array
		if ( ! is_array( $settings ) ) {
			return $settings;
		}
		
		// Add error handling to prevent fatal errors
		try {
		// If new structure already exists and is valid, return as-is
		if ( ! empty( $settings['table_rows'] ) ) {
			if ( is_string( $settings['table_rows'] ) ) {
				$settings['table_rows'] = [];
			} elseif ( is_array( $settings['table_rows'] ) ) {
				// Validate structure - check if rows have row_values field (new format)
				$is_valid = true;
				$has_new_format = false;
				foreach ( $settings['table_rows'] as $row ) {
					if ( ! is_array( $row ) ) {
						$is_valid = false;
						break;
					}
					// Check if row has row_values field (new pipe-separated format)
					if ( isset( $row['row_values'] ) ) {
						$has_new_format = true;
					}
				}
				// If valid and has new format, return as-is
				if ( $is_valid && $has_new_format ) {
					return $settings;
				}
				// If has old col_X_value format, convert to new format
				if ( $is_valid ) {
					$converted_rows = [];
					foreach ( $settings['table_rows'] as $row ) {
						$row_values = [];
						$col_index = 1;
						$has_old_format = false;
						
						// Check for old col_X_value fields
						while ( isset( $row[ 'col_' . $col_index . '_value' ] ) ) {
							$row_values[] = $row[ 'col_' . $col_index . '_value' ];
							$has_old_format = true;
							$col_index++;
						}
						
						if ( $has_old_format && ! empty( $row_values ) ) {
							$converted_rows[] = [
								'row_values' => implode( ' | ', $row_values ),
							];
						} else {
							$converted_rows[] = $row;
						}
					}
					$settings['table_rows'] = $converted_rows;
					return $settings;
				}
			}
		}

			// If old structure doesn't exist, return as-is
			if ( empty( $settings['table_columns'] ) || ! is_array( $settings['table_columns'] ) ) {
				return $settings;
			}

			$table_columns = $settings['table_columns'];
			$migrated_rows = [];

			// Check if old structure exists (has column_values or column_title)
			$has_old_structure = false;
			foreach ( $table_columns as $column ) {
				if ( ! is_array( $column ) ) {
					continue;
				}
				// Check for old structure: column_values or column_title (instead of column_label)
				if ( ( ! empty( $column['column_values'] ) && is_array( $column['column_values'] ) ) || 
					 ( ! empty( $column['column_title'] ) && empty( $column['column_label'] ) ) ) {
					$has_old_structure = true;
					break;
				}
			}

			if ( ! $has_old_structure ) {
				return $settings;
			}

			// Find maximum number of rows across all columns
			$max_rows = 0;
			foreach ( $table_columns as $column ) {
				if ( ! is_array( $column ) ) {
					continue;
				}
				$column_values = ! empty( $column['column_values'] ) ? $column['column_values'] : [];
				if ( is_array( $column_values ) ) {
					$row_count = count( $column_values );
					if ( $row_count > $max_rows ) {
						$max_rows = $row_count;
					}
				}
			}

			// Build rows: each row contains values from all columns at the same index
			// Convert to pipe-separated format: "value1 | value2 | value3"
			for ( $row_index = 0; $row_index < $max_rows; $row_index++ ) {
				$row_values = [];
				
				foreach ( $table_columns as $column ) {
					if ( ! is_array( $column ) ) {
						$row_values[] = '';
						continue;
					}
					
					$column_values = ! empty( $column['column_values'] ) ? $column['column_values'] : [];
					$cell_value = '';
					
					if ( is_array( $column_values ) && isset( $column_values[ $row_index ] ) ) {
						$row_item = $column_values[ $row_index ];
						if ( is_array( $row_item ) && ! empty( $row_item['row_value'] ) ) {
							$cell_value = $row_item['row_value'];
						}
					}
					
					$row_values[] = $cell_value;
				}
				
				if ( ! empty( $row_values ) ) {
					// Join values with pipe separator
					$migrated_rows[] = [
						'row_values' => implode( ' | ', $row_values ),
					];
				}
			}

			// Clean up old column structure: convert column_title to column_label, remove column_values
			$cleaned_columns = [];
			foreach ( $table_columns as $column ) {
				if ( ! is_array( $column ) ) {
					continue;
				}
				$cleaned_columns[] = [
					'column_label' => ! empty( $column['column_label'] ) ? $column['column_label'] : ( ! empty( $column['column_title'] ) ? $column['column_title'] : '' ),
				];
			}

			// Update settings with migrated data
			$settings['table_columns'] = $cleaned_columns;
			$settings['table_rows'] = $migrated_rows;

			return $settings;
		} catch ( Exception $e ) {
			// If migration fails, return original settings to prevent fatal error
			return $settings;
		}
	}

	/**
	 * Render widget output on the frontend.
	 */
	protected function render() {
		if ( function_exists( 'omsar_bd_render_data_table_widget' ) ) {
			omsar_bd_render_data_table_widget(
				omsar_bd_data_table_settings_from_elementor( $this->get_settings_for_display() ),
				(string) $this->get_id()
			);
			return;
		}

		$settings = $this->get_settings_for_display();
		
		// Migrate old structure to new structure if needed
		$settings = $this->migrate_old_structure( $settings );

		$table_title = ! empty( $settings['table_title'] ) ? esc_html( $settings['table_title'] ) : '';
		$table_columns = ! empty( $settings['table_columns'] ) ? $settings['table_columns'] : [];
		$table_rows = ! empty( $settings['table_rows'] ) ? $settings['table_rows'] : [];
		$show_export_icon = ! empty( $settings['show_export_icon'] ) && $settings['show_export_icon'] === 'yes';
		$show_zoom_icon = ! empty( $settings['show_zoom_icon'] ) && $settings['show_zoom_icon'] === 'yes';

		// Generate unique ID for this widget instance
		$widget_id = 'omsar-table-' . $this->get_id();
		$table_id = $widget_id . '-table';

		?>
		<style>
			#<?php echo esc_attr( $widget_id ); ?> .omsar-table-header {
				display: flex;
				justify-content: space-between;
				align-items: center;
				margin-bottom: 20px;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-table-header .omsar-table-title {
				margin: 0;
				flex: 1;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-table-header-actions {
				display: flex;
				gap: 8px;
				align-items: center;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-table-action-icon {
				display: inline-flex;
				align-items: center;
				justify-content: center;
				width: 32px;
				height: 32px;
				border: none;
				border-radius: 4px;
				background-color: #ffffff;
				color: #374151;
				cursor: pointer;
				transition: all 0.2s ease;
				position: relative;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-table-action-icon:hover {
				background-color: #f3f4f6;
				border-color: #9ca3af;
				color: #111827;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-table-action-icon:active {
				background-color: #e5e7eb;
				transform: scale(0.95);
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-table-action-icon svg {
				width: 18px;
				height: 18px;
				fill: currentColor;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-table-action-icon[data-tooltip]:hover::before {
				content: attr(data-tooltip);
				position: absolute;
				bottom: 100%;
				left: 50%;
				transform: translateX(-50%);
				margin-bottom: 5px;
				padding: 6px 10px;
				background-color: #1f2937;
				color: #ffffff;
				font-size: 12px;
				white-space: nowrap;
				border-radius: 4px;
				pointer-events: none;
				z-index: 1000;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-table-action-icon[data-tooltip]:hover::after {
				content: '';
				position: absolute;
				bottom: 100%;
				left: 50%;
				transform: translateX(-50%);
				margin-bottom: -1px;
				width: 0;
				height: 0;
				border-left: 5px solid transparent;
				border-right: 5px solid transparent;
				border-top: 5px solid #1f2937;
				pointer-events: none;
				z-index: 1000;
			}
			/* Modal styles for zoom view */
			.omsar-table-modal {
				display: none;
				position: fixed;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				background-color: rgba(0, 0, 0, 0.75);
				z-index: 9999;
				overflow: auto;
				padding: 20px;
				box-sizing: border-box;
			}
			.omsar-table-modal.active {
				display: flex;
				align-items: center;
				justify-content: center;
			}
			.omsar-table-modal-content {
				position: relative;
				background-color: #ffffff;
				border-radius: 8px;
				padding: 30px;
				max-width: 90%;
				max-height: 100%;
				overflow: hidden;
				box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
			}
			.omsar-table-modal-close {
				position: absolute;
				top: 10px;
				right: 10px;
				width: 32px;
				height: 32px;
				border: none;
				background-color: #f3f4f6;
				border-radius: 4px;
				cursor: pointer;
				display: flex;
				align-items: center;
				justify-content: center;
				color: #374151;
				transition: all 0.2s ease;
			}
			html[dir="rtl"] .omsar-table-modal-close {
				left: 10px;  
				right: auto;  
			}
			.omsar-table-modal-close:hover {
				background-color: #e5e7eb;
				color: #111827;
			}
			.omsar-table-modal-close svg {
				width: 18px;
				height: 18px;
				fill: currentColor;
			}
			.omsar-table-modal-title {
				margin: 0 0 20px 0;
				font-size: 20px;
				font-weight: 600;
			}
			.omsar-table-modal-container {
				width: 100%;
				overflow-x: auto;
			}
			.omsar-table-modal-container table {
				width: 100%;
				min-width: 100%;
			}
		</style>
		<div class="omsar-table-wrapper" id="<?php echo esc_attr( $widget_id ); ?>">
			<?php if ( ! empty( $table_title ) || $show_export_icon || $show_zoom_icon ) : ?>
				<div class="omsar-table-header">
			<?php if ( ! empty( $table_title ) ) : ?>
				<h3 class="omsar-table-title"><?php echo esc_html( $table_title ); ?></h3>
					<?php else : ?>
						<div></div>
					<?php endif; ?>
					<?php if ( $show_export_icon || $show_zoom_icon ) : ?>
						<div class="omsar-table-header-actions">
							<?php if ( $show_export_icon ) : ?>
								<button type="button" class="omsar-table-action-icon omsar-table-export-icon" data-tooltip="<?php echo esc_attr__( 'Export Table', 'omsar' ); ?>" aria-label="<?php echo esc_attr__( 'Export table', 'omsar' ); ?>" data-widget-id="<?php echo esc_attr( $widget_id ); ?>" data-table-id="<?php echo esc_attr( $table_id ); ?>" data-table-title="<?php echo esc_attr( $table_title ); ?>">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
										<path d="M19 12v7H5v-7H3v7c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-7h-2zm-6 .67l2.59-2.58L17 11.5l-5 5-5-5 1.41-1.41L11 12.67V3h2z"/>
									</svg>
								</button>
							<?php endif; ?>
							<?php if ( $show_zoom_icon ) : ?>
								<button type="button" class="omsar-table-action-icon omsar-table-zoom-icon" data-tooltip="<?php echo esc_attr__( 'Open in Full View', 'omsar' ); ?>" aria-label="<?php echo esc_attr__( 'Open table in full view', 'omsar' ); ?>" data-widget-id="<?php echo esc_attr( $widget_id ); ?>" data-table-id="<?php echo esc_attr( $table_id ); ?>" data-table-title="<?php echo esc_attr( $table_title ); ?>">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
										<path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/>
									</svg>
								</button>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			
			<?php if ( ! empty( $table_columns ) ) : ?>
				<table class="omsar-table" id="<?php echo esc_attr( $table_id ); ?>">
					<thead>
						<tr>
							<?php foreach ( $table_columns as $column ) : ?>
								<?php $column_label = ! empty( $column['column_label'] ) ? esc_html( $column['column_label'] ) : ''; ?>
								<th><?php echo esc_html( $column_label ); ?></th>
							<?php endforeach; ?>
						</tr>
					</thead>
					<tbody>
						<?php if ( ! empty( $table_rows ) ) : ?>
							<?php foreach ( $table_rows as $row ) : ?>
								<tr>
									<?php
									// Parse pipe-separated values from row_values field
									$row_values_str = ! empty( $row['row_values'] ) ? trim( $row['row_values'] ) : '';
									$row_values_array = [];
									
									// Check if we have the pipe-separated format
									if ( ! empty( $row_values_str ) && strpos( $row_values_str, '|' ) !== false ) {
										// Split by pipe and trim each value
										$row_values_array = array_map( 'trim', explode( '|', $row_values_str ) );
									} else {
										// Fallback: check for old col_X_value structure
										$col_index = 1;
										foreach ( $table_columns as $column ) {
											$field_name = 'col_' . $col_index . '_value';
											if ( isset( $row[ $field_name ] ) && ! empty( $row[ $field_name ] ) ) {
												$row_values_array[] = trim( $row[ $field_name ] );
											} else {
												$row_values_array[] = '';
											}
											$col_index++;
										}
									}
									
									// Ensure we have enough values for all columns
									$column_count = count( $table_columns );
									while ( count( $row_values_array ) < $column_count ) {
										$row_values_array[] = '';
									}
									
									// Output cells matching column count
									foreach ( $table_columns as $col_index => $column ) {
										$cell_value = isset( $row_values_array[ $col_index ] ) ? esc_html( $row_values_array[ $col_index ] ) : '';
										?>
										<td><?php echo $cell_value; ?></td>
										<?php
									}
									?>
								</tr>
							<?php endforeach; ?>
						<?php else : ?>
							<?php
							// Show empty rows if no data provided
							for ( $i = 0; $i < 3; $i++ ) {
								echo '<tr>';
								foreach ( $table_columns as $column ) {
									echo '<td></td>';
								}
								echo '</tr>';
							}
							?>
						<?php endif; ?>
					</tbody>
				</table>
			<?php else : ?>
				<div class="omsar-table-empty-message">
					<p><?php echo esc_html__( 'Please add at least one column to display the table.', 'omsar' ); ?></p>
				</div>
			<?php endif; ?>
		</div>

		<script type="text/javascript">
		(function() {
			'use strict';
			
			var initTableActions = function() {
				var widgetId = '<?php echo esc_js( $widget_id ); ?>';
				var tableId = '<?php echo esc_js( $table_id ); ?>';
				var tableTitle = <?php echo wp_json_encode( $table_title ); ?>;

				// Export functionality
				var exportIcon = document.querySelector('#' + widgetId + ' .omsar-table-export-icon');
				if (exportIcon) {
					exportIcon.addEventListener('click', function() {
						var table = document.getElementById(tableId);
						if (!table) {
							return;
						}

						// Create CSV content
						var csvContent = [];
						
						// Get headers
						var headers = [];
						var headerCells = table.querySelectorAll('thead th');
						headerCells.forEach(function(cell) {
							headers.push('"' + cell.textContent.trim().replace(/"/g, '""') + '"');
						});
						csvContent.push(headers.join(','));

						// Get rows
						var rows = table.querySelectorAll('tbody tr');
						rows.forEach(function(row) {
							var rowData = [];
							var cells = row.querySelectorAll('td');
							cells.forEach(function(cell) {
								rowData.push('"' + cell.textContent.trim().replace(/"/g, '""') + '"');
							});
							csvContent.push(rowData.join(','));
						});

						// Create download link
						var csv = csvContent.join('\n');
						var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
						var link = document.createElement('a');
						var url = URL.createObjectURL(blob);
						link.setAttribute('href', url);
						link.setAttribute('download', (tableTitle || 'table').replace(/[^a-z0-9]/gi, '_').toLowerCase() + '.csv');
						link.style.visibility = 'hidden';
						document.body.appendChild(link);
						link.click();
						document.body.removeChild(link);
					});
				}

				// Zoom/Expand modal functionality
				var zoomIcon = document.querySelector('#' + widgetId + ' .omsar-table-zoom-icon');
				if (zoomIcon) {
					zoomIcon.addEventListener('click', function() {
						var table = document.getElementById(tableId);
						if (!table) {
							return;
						}

						// Create modal if it doesn't exist
						var modalId = 'omsar-table-modal-' + widgetId;
						var existingModal = document.getElementById(modalId);
						if (existingModal) {
							existingModal.remove();
						}

						var modal = document.createElement('div');
						modal.id = modalId;
						modal.className = 'omsar-table-modal active';
						modal.setAttribute('role', 'dialog');
						modal.setAttribute('aria-modal', 'true');
						modal.setAttribute('aria-labelledby', modalId + '-title');

						var modalContent = document.createElement('div');
						modalContent.className = 'omsar-table-modal-content container';

						var closeButton = document.createElement('button');
						closeButton.type = 'button';
						closeButton.className = 'omsar-table-modal-close';
						closeButton.setAttribute('aria-label', 'Close modal');
						closeButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>';
						closeButton.addEventListener('click', function() {
							modal.classList.remove('active');
							setTimeout(function() {
								modal.remove();
							}, 300);
						});

						if (tableTitle) {
							var modalTitle = document.createElement('h3');
							modalTitle.id = modalId + '-title';
							modalTitle.className = 'omsar-table-modal-title';
							modalTitle.textContent = tableTitle;
							modalContent.appendChild(modalTitle);
						}

						var modalContainer = document.createElement('div');
						modalContainer.className = 'omsar-table-modal-container';

						// Clone table for modal
						var clonedTable = table.cloneNode(true);
						clonedTable.style.width = '100%';
						
						// Copy class name from original table
						clonedTable.className = table.className;
						
						// Copy computed styles from original table to cloned table
						var originalStyles = window.getComputedStyle(table);
						
						// Copy all relevant styles from the original table
						var styleProps = [
							'fontFamily', 'fontSize', 'fontWeight', 'fontStyle',
							'color', 'textAlign', 'lineHeight',
							'borderCollapse', 'borderSpacing', 'width'
						];
						
						styleProps.forEach(function(prop) {
							clonedTable.style[prop] = originalStyles[prop];
						});
						
						// Copy styles from thead th elements
						var originalTheadThs = table.querySelectorAll('thead th');
						var clonedTheadThs = clonedTable.querySelectorAll('thead th');
						originalTheadThs.forEach(function(originalTh, index) {
							if (clonedTheadThs[index]) {
								var originalThStyles = window.getComputedStyle(originalTh);
								var clonedTh = clonedTheadThs[index];
								['backgroundColor', 'color', 'fontFamily', 'fontSize', 'fontWeight', 
								 'padding', 'textAlign', 'border'].forEach(function(prop) {
									clonedTh.style[prop] = originalThStyles[prop];
								});
							}
						});
						
						// Copy styles from tbody td elements
						var originalTbodyTds = table.querySelectorAll('tbody td');
						var clonedTbodyTds = clonedTable.querySelectorAll('tbody td');
						originalTbodyTds.forEach(function(originalTd, index) {
							if (clonedTbodyTds[index]) {
								var originalTdStyles = window.getComputedStyle(originalTd);
								var clonedTd = clonedTbodyTds[index];
								['color', 'fontFamily', 'fontSize', 'fontWeight', 
								 'padding', 'textAlign', 'borderBottom', 'borderBottomColor', 
								 'borderBottomWidth', 'borderBottomStyle'].forEach(function(prop) {
									clonedTd.style[prop] = originalTdStyles[prop];
								});
							}
						});
						
						// Copy styles from tbody tr elements
						var originalTbodyTrs = table.querySelectorAll('tbody tr');
						var clonedTbodyTrs = clonedTable.querySelectorAll('tbody tr');
						originalTbodyTrs.forEach(function(originalTr, index) {
							if (clonedTbodyTrs[index]) {
								var originalTrStyles = window.getComputedStyle(originalTr);
								var clonedTr = clonedTbodyTrs[index];
								['borderBottom', 'borderBottomColor', 'borderBottomWidth', 
								 'borderBottomStyle'].forEach(function(prop) {
									clonedTr.style[prop] = originalTrStyles[prop];
								});
							}
						});
						
						modalContainer.appendChild(clonedTable);

						modalContent.appendChild(closeButton);
						modalContent.appendChild(modalContainer);
						modal.appendChild(modalContent);
						document.body.appendChild(modal);

						// Close modal on ESC key
						var handleEsc = function(e) {
							if (e.key === 'Escape') {
								modal.classList.remove('active');
								setTimeout(function() {
									modal.remove();
								}, 300);
								document.removeEventListener('keydown', handleEsc);
							}
						};
						document.addEventListener('keydown', handleEsc);

						// Close modal on backdrop click
						modal.addEventListener('click', function(e) {
							if (e.target === modal) {
								modal.classList.remove('active');
								setTimeout(function() {
									modal.remove();
								}, 300);
							}
						});
					});
				}
			};

			if (document.readyState === 'loading') {
				document.addEventListener('DOMContentLoaded', initTableActions);
			} else {
				initTableActions();
			}
		})();
		</script>
		<?php
	}

	/**
	 * Render widget output in the editor.
	 */
	protected function content_template() {
		?>
		<#
		var tableTitle = settings.table_title || '';
		var tableColumns = settings.table_columns || [];
		var tableRows = settings.table_rows || [];
		var showExportIcon = settings.show_export_icon === 'yes';
		var showZoomIcon = settings.show_zoom_icon === 'yes';
		var widgetId = 'omsar-table-' + view.getIDInt();
		var tableId = widgetId + '-table';
		
		// Migrate old structure to new structure if needed (for editor preview)
		if (!tableRows || tableRows.length === 0) {
			// Check if old structure exists
			var hasOldStructure = false;
			_.each(tableColumns, function(column) {
				if (column.column_values && column.column_values.length > 0) {
					hasOldStructure = true;
				}
				if (column.column_title && !column.column_label) {
					hasOldStructure = true;
				}
			});
			
			if (hasOldStructure) {
				// Find maximum number of rows across all columns
				var maxRows = 0;
				_.each(tableColumns, function(column) {
					var columnValues = column.column_values || [];
					if (columnValues.length > maxRows) {
						maxRows = columnValues.length;
					}
				});
				
				// Build rows from old structure - convert to pipe-separated format
				tableRows = [];
				for (var rowIndex = 0; rowIndex < maxRows; rowIndex++) {
					var rowValues = [];
					_.each(tableColumns, function(column) {
						var columnValues = column.column_values || [];
						var cellValue = '';
						if (columnValues[rowIndex] && columnValues[rowIndex].row_value) {
							cellValue = columnValues[rowIndex].row_value;
						}
						rowValues.push(cellValue);
					});
					if (rowValues.length > 0) {
						tableRows.push({
							row_values: rowValues.join(' | ')
						});
					}
				}
				
				// Clean up columns: convert column_title to column_label
				_.each(tableColumns, function(column) {
					if (column.column_title && !column.column_label) {
						column.column_label = column.column_title;
					}
				});
			}
		}
		#>
		<style>
			#{{ widgetId }} .omsar-table-header {
				display: flex;
				justify-content: space-between;
				align-items: center;
				margin-bottom: 20px;
			}
			#{{ widgetId }} .omsar-table-header .omsar-table-title {
				margin: 0;
				flex: 1;
			}
			#{{ widgetId }} .omsar-table-header-actions {
				display: flex;
				gap: 8px;
				align-items: center;
			}
			#{{ widgetId }} .omsar-table-action-icon {
				display: inline-flex;
				align-items: center;
				justify-content: center;
				width: 32px;
				height: 32px;
				border: none;
				border-radius: 4px;
				background-color: #ffffff;
				color: #374151;
				cursor: pointer;
				transition: all 0.2s ease;
				position: relative;
			}
			#{{ widgetId }} .omsar-table-action-icon:hover {
				background-color: #f3f4f6;
				border-color: #9ca3af;
				color: #111827;
			}
			#{{ widgetId }} .omsar-table-action-icon:active {
				background-color: #e5e7eb;
				transform: scale(0.95);
			}
			#{{ widgetId }} .omsar-table-action-icon svg {
				width: 18px;
				height: 18px;
				fill: currentColor;
			}
			#{{ widgetId }} .omsar-table-action-icon[data-tooltip]:hover::before {
				content: attr(data-tooltip);
				position: absolute;
				bottom: 100%;
				left: 50%;
				transform: translateX(-50%);
				margin-bottom: 5px;
				padding: 6px 10px;
				background-color: #1f2937;
				color: #ffffff;
				font-size: 12px;
				white-space: nowrap;
				border-radius: 4px;
				pointer-events: none;
				z-index: 1000;
			}
			#{{ widgetId }} .omsar-table-action-icon[data-tooltip]:hover::after {
				content: '';
				position: absolute;
				bottom: 100%;
				left: 50%;
				transform: translateX(-50%);
				margin-bottom: -1px;
				width: 0;
				height: 0;
				border-left: 5px solid transparent;
				border-right: 5px solid transparent;
				border-top: 5px solid #1f2937;
				pointer-events: none;
				z-index: 1000;
			}
			.omsar-table-modal {
				display: none;
				position: fixed;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				background-color: rgba(0, 0, 0, 0.75);
				z-index: 9999;
				overflow: auto;
				padding: 20px;
				box-sizing: border-box;
			}
			.omsar-table-modal.active {
				display: flex;
				align-items: center;
				justify-content: center;
			}
			.omsar-table-modal-content {
				position: relative;
				background-color: #ffffff;
				border-radius: 8px;
				padding: 30px;
				max-width: 90%;
				max-height: 100%;
				overflow: hidden;
				box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
			}
			.omsar-table-modal-close {
				position: absolute;
				top: 10px;
				right: 10px;
				width: 32px;
				height: 32px;
				border: none;
				background-color: #f3f4f6;
				border-radius: 4px;
				cursor: pointer;
				display: flex;
				align-items: center;
				justify-content: center;
				color: #374151;
				transition: all 0.2s ease;
			}
			html[dir="rtl"] .omsar-table-modal-close {
				left: 10px;  
				right: auto;  
			}
			.omsar-table-modal-close:hover {
				background-color: #e5e7eb;
				color: #111827;
			}
			.omsar-table-modal-close svg {
				width: 18px;
				height: 18px;
				fill: currentColor;
			}
			.omsar-table-modal-title {
				margin: 0 0 20px 0;
				font-size: 20px;
				font-weight: 600;
			}
			.omsar-table-modal-container {
				width: 100%;
				overflow-x: auto;
			}
			.omsar-table-modal-container table {
				width: 100%;
				min-width: 100%;
			}
		</style>
		<div class="omsar-table-wrapper" id="{{ widgetId }}">
			<# if (tableTitle || showExportIcon || showZoomIcon) { #>
				<div class="omsar-table-header">
			<# if (tableTitle) { #>
				<h3 class="omsar-table-title">{{{ tableTitle }}}</h3>
					<# } else { #>
						<div></div>
					<# } #>
					<# if (showExportIcon || showZoomIcon) { #>
						<div class="omsar-table-header-actions">
							<# if (showExportIcon) { #>
								<button type="button" class="omsar-table-action-icon omsar-table-export-icon" data-tooltip="<?php echo esc_attr__( 'Export Table', 'omsar' ); ?>" aria-label="<?php echo esc_attr__( 'Export table', 'omsar' ); ?>" data-widget-id="{{{ widgetId }}}" data-table-id="{{{ tableId }}}" data-table-title="{{{ tableTitle }}}">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
										<path d="M19 12v7H5v-7H3v7c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-7h-2zm-6 .67l2.59-2.58L17 11.5l-5 5-5-5 1.41-1.41L11 12.67V3h2z"/>
									</svg>
								</button>
							<# } #>
							<# if (showZoomIcon) { #>
								<button type="button" class="omsar-table-action-icon omsar-table-zoom-icon" data-tooltip="<?php echo esc_attr__( 'Open in Full View', 'omsar' ); ?>" aria-label="<?php echo esc_attr__( 'Open table in full view', 'omsar' ); ?>" data-widget-id="{{{ widgetId }}}" data-table-id="{{{ tableId }}}" data-table-title="{{{ tableTitle }}}">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
										<path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/>
									</svg>
								</button>
							<# } #>
						</div>
					<# } #>
				</div>
			<# } #>
			
			<# if (tableColumns.length > 0) { #>
				<table class="omsar-table" id="{{{ tableId }}}">
					<thead>
						<tr>
							<# _.each(tableColumns, function(column) { #>
								<th>{{{ column.column_label || column.column_title || '' }}}</th>
							<# }); #>
						</tr>
					</thead>
					<tbody>
						<# if (tableRows && tableRows.length > 0) { #>
							<# _.each(tableRows, function(row) { #>
								<tr>
									<#
									// Parse pipe-separated values from row_values field
									var rowValuesStr = (row.row_values || '').trim();
									var rowValuesArray = [];
									
									// Check if we have the pipe-separated format
									if (rowValuesStr && rowValuesStr.indexOf('|') !== -1) {
										// Split by pipe and trim each value
										rowValuesArray = rowValuesStr.split('|').map(function(val) {
											return val.trim();
										});
									} else {
										// Fallback: check for old col_X_value structure
										var colIndex = 1;
										_.each(tableColumns, function(column) {
											var fieldName = 'col_' + colIndex + '_value';
											if (row[fieldName]) {
												rowValuesArray.push(String(row[fieldName]).trim());
											} else {
												rowValuesArray.push('');
											}
											colIndex++;
										});
									}
									
									// Ensure we have enough values for all columns
									var columnCount = tableColumns.length;
									while (rowValuesArray.length < columnCount) {
										rowValuesArray.push('');
									}
									
									// Output cells matching column count
									_.each(tableColumns, function(column, colIndex) {
										var cellValue = rowValuesArray[colIndex] || '';
									#>
										<td>{{{ cellValue }}}</td>
									<# }); #>
								</tr>
							<# }); #>
						<# } else { #>
							<# for (var i = 0; i < 3; i++) { #>
								<tr>
									<# _.each(tableColumns, function(column) { #>
										<td></td>
									<# }); #>
								</tr>
							<# } #>
						<# } #>
					</tbody>
				</table>
			<# } else { #>
				<div class="omsar-table-empty-message">
					<p><?php echo esc_html__( 'Please add at least one column to display the table.', 'omsar' ); ?></p>
				</div>
			<# } #>
		</div>

		<script type="text/javascript">
		(function() {
			'use strict';
			
			var initTableActions = function() {
				var widgetId = '{{ widgetId }}';
				var tableId = '{{ tableId }}';
				var tableTitle = '{{ tableTitle }}';

				// Export functionality
				var exportIcon = document.querySelector('#' + widgetId + ' .omsar-table-export-icon');
				if (exportIcon) {
					exportIcon.addEventListener('click', function() {
						var table = document.getElementById(tableId);
						if (!table) {
							return;
						}

						// Create CSV content
						var csvContent = [];
						
						// Get headers
						var headers = [];
						var headerCells = table.querySelectorAll('thead th');
						headerCells.forEach(function(cell) {
							headers.push('"' + cell.textContent.trim().replace(/"/g, '""') + '"');
						});
						csvContent.push(headers.join(','));

						// Get rows
						var rows = table.querySelectorAll('tbody tr');
						rows.forEach(function(row) {
							var rowData = [];
							var cells = row.querySelectorAll('td');
							cells.forEach(function(cell) {
								rowData.push('"' + cell.textContent.trim().replace(/"/g, '""') + '"');
							});
							csvContent.push(rowData.join(','));
						});

						// Create download link
						var csv = csvContent.join('\n');
						var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
						var link = document.createElement('a');
						var url = URL.createObjectURL(blob);
						link.setAttribute('href', url);
						link.setAttribute('download', (tableTitle || 'table').replace(/[^a-z0-9]/gi, '_').toLowerCase() + '.csv');
						link.style.visibility = 'hidden';
						document.body.appendChild(link);
						link.click();
						document.body.removeChild(link);
					});
				}

				// Zoom/Expand modal functionality
				var zoomIcon = document.querySelector('#' + widgetId + ' .omsar-table-zoom-icon');
				if (zoomIcon) {
					zoomIcon.addEventListener('click', function() {
						var table = document.getElementById(tableId);
						if (!table) {
							return;
						}

						// Create modal if it doesn't exist
						var modalId = 'omsar-table-modal-' + widgetId;
						var existingModal = document.getElementById(modalId);
						if (existingModal) {
							existingModal.remove();
						}

						var modal = document.createElement('div');
						modal.id = modalId;
						modal.className = 'omsar-table-modal active';
						modal.setAttribute('role', 'dialog');
						modal.setAttribute('aria-modal', 'true');
						modal.setAttribute('aria-labelledby', modalId + '-title');

						var modalContent = document.createElement('div');
						modalContent.className = 'omsar-table-modal-content container';

						var closeButton = document.createElement('button');
						closeButton.type = 'button';
						closeButton.className = 'omsar-table-modal-close';
						closeButton.setAttribute('aria-label', 'Close modal');
						closeButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>';
						closeButton.addEventListener('click', function() {
							modal.classList.remove('active');
							setTimeout(function() {
								modal.remove();
							}, 300);
						});

						if (tableTitle) {
							var modalTitle = document.createElement('h3');
							modalTitle.id = modalId + '-title';
							modalTitle.className = 'omsar-table-modal-title';
							modalTitle.textContent = tableTitle;
							modalContent.appendChild(modalTitle);
						}

						var modalContainer = document.createElement('div');
						modalContainer.className = 'omsar-table-modal-container';

						// Clone table for modal
						var clonedTable = table.cloneNode(true);
						clonedTable.style.width = '100%';
						
						// Copy class name from original table
						clonedTable.className = table.className;
						
						// Copy computed styles from original table to cloned table
						var originalStyles = window.getComputedStyle(table);
						
						// Copy all relevant styles from the original table
						var styleProps = [
							'fontFamily', 'fontSize', 'fontWeight', 'fontStyle',
							'color', 'textAlign', 'lineHeight',
							'borderCollapse', 'borderSpacing', 'width'
						];
						
						styleProps.forEach(function(prop) {
							clonedTable.style[prop] = originalStyles[prop];
						});
						
						// Copy styles from thead th elements
						var originalTheadThs = table.querySelectorAll('thead th');
						var clonedTheadThs = clonedTable.querySelectorAll('thead th');
						originalTheadThs.forEach(function(originalTh, index) {
							if (clonedTheadThs[index]) {
								var originalThStyles = window.getComputedStyle(originalTh);
								var clonedTh = clonedTheadThs[index];
								['backgroundColor', 'color', 'fontFamily', 'fontSize', 'fontWeight', 
								 'padding', 'textAlign', 'border'].forEach(function(prop) {
									clonedTh.style[prop] = originalThStyles[prop];
								});
							}
						});
						
						// Copy styles from tbody td elements
						var originalTbodyTds = table.querySelectorAll('tbody td');
						var clonedTbodyTds = clonedTable.querySelectorAll('tbody td');
						originalTbodyTds.forEach(function(originalTd, index) {
							if (clonedTbodyTds[index]) {
								var originalTdStyles = window.getComputedStyle(originalTd);
								var clonedTd = clonedTbodyTds[index];
								['color', 'fontFamily', 'fontSize', 'fontWeight', 
								 'padding', 'textAlign', 'borderBottom', 'borderBottomColor', 
								 'borderBottomWidth', 'borderBottomStyle'].forEach(function(prop) {
									clonedTd.style[prop] = originalTdStyles[prop];
								});
							}
						});
						
						// Copy styles from tbody tr elements
						var originalTbodyTrs = table.querySelectorAll('tbody tr');
						var clonedTbodyTrs = clonedTable.querySelectorAll('tbody tr');
						originalTbodyTrs.forEach(function(originalTr, index) {
							if (clonedTbodyTrs[index]) {
								var originalTrStyles = window.getComputedStyle(originalTr);
								var clonedTr = clonedTbodyTrs[index];
								['borderBottom', 'borderBottomColor', 'borderBottomWidth', 
								 'borderBottomStyle'].forEach(function(prop) {
									clonedTr.style[prop] = originalTrStyles[prop];
								});
							}
						});
						
						modalContainer.appendChild(clonedTable);

						modalContent.appendChild(closeButton);
						modalContent.appendChild(modalContainer);
						modal.appendChild(modalContent);
						document.body.appendChild(modal);

						// Close modal on ESC key
						var handleEsc = function(e) {
							if (e.key === 'Escape') {
								modal.classList.remove('active');
								setTimeout(function() {
									modal.remove();
								}, 300);
								document.removeEventListener('keydown', handleEsc);
							}
						};
						document.addEventListener('keydown', handleEsc);

						// Close modal on backdrop click
						modal.addEventListener('click', function(e) {
							if (e.target === modal) {
								modal.classList.remove('active');
								setTimeout(function() {
									modal.remove();
								}, 300);
							}
						});
					});
				}
			};

			if (document.readyState === 'loading') {
				document.addEventListener('DOMContentLoaded', initTableActions);
			} else {
				initTableActions();
			}
		})();
		</script>
		<?php
	}
}

