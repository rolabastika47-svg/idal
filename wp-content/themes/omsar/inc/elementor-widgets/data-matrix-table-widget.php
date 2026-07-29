<?php
/**
 * Elementor Data Matrix Table Widget Class
 * 
 * Displays a customizable matrix-style table with row titles and column titles
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

class OMSAR_Data_Matrix_Table_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'omsar_data_matrix_table';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Data Matrix Table', 'omsar' );
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
		return [ 'table', 'matrix', 'data table', 'grid', 'data', 'rows', 'columns', 'matrix table' ];
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

		$this->add_control(
			'row_column_title',
			[
				'label' => esc_html__( 'Row Column Title', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Item', 'omsar' ),
				'placeholder' => esc_html__( 'Enter row column title (e.g., Item)', 'omsar' ),
				'label_block' => true,
				'description' => esc_html__( 'Title for the first column that contains row titles (e.g., "Item", "Category", etc.)', 'omsar' ),
			]
		);

		// Row titles repeater
		$row_title_repeater = new Repeater();
		
		$row_title_repeater->add_control(
			'row_title',
			[
				'label' => esc_html__( 'Row Title', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Enter row title', 'omsar' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'table_row_titles',
			[
				'label' => esc_html__( 'Row Titles', 'omsar' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $row_title_repeater->get_controls(),
				'default' => [
					[
						'row_title' => 'Row 1',
					],
					[
						'row_title' => 'Row 2',
					],
					[
						'row_title' => 'Row 3',
					],
				],
				'title_field' => '{{{ row_title }}}',
				'description' => esc_html__( 'Add row titles. These will appear in the first column of the table. Each row title corresponds to one data row.', 'omsar' ),
			]
		);

		// Create nested repeater for column values
		$column_values_repeater = new Repeater();
		
		$column_values_repeater->add_control(
			'row_value',
			[
				'label' => esc_html__( 'Row Value', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Enter value', 'omsar' ),
				'label_block' => true,
			]
		);

		$column_repeater = new Repeater();

		$column_repeater->add_control(
			'column_title',
			[
				'label' => esc_html__( 'Column Title', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Column', 'omsar' ),
				'placeholder' => esc_html__( 'Enter column title', 'omsar' ),
				'label_block' => true,
			]
		);

		$column_repeater->add_control(
			'column_values',
			[
				'label' => esc_html__( 'Row Values', 'omsar' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $column_values_repeater->get_controls(),
				'default' => [
					[
						'row_value' => 'Value 1',
					],
					[
						'row_value' => 'Value 2',
					],
					[
						'row_value' => 'Value 3',
					],
				],
				'title_field' => '{{{ row_value }}}',
				'min_items' => 0,
				'description' => esc_html__( 'Add row values for this column. Each item represents one row. Rows are aligned across columns by index (Row 1, Row 2, Row 3, etc.).', 'omsar' ),
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
						'column_title' => 'Column 1',
						'column_values' => [
							[ 'row_value' => 'Data 1' ],
							[ 'row_value' => 'Data 4' ],
						],
					],
					[
						'column_title' => 'Column 2',
						'column_values' => [
							[ 'row_value' => 'Data 2' ],
							[ 'row_value' => 'Data 5' ],
						],
					],
					[
						'column_title' => 'Column 3',
						'column_values' => [
							[ 'row_value' => 'Data 3' ],
							[ 'row_value' => 'Data 6' ],
						],
					],
				],
				'title_field' => '{{{ column_title }}}',
				'description' => esc_html__( 'Add columns to your table. Each column has its own repeater for row values. Rows are built by aligning values across columns by index.', 'omsar' ),
			]
		);

		$this->end_controls_section();

		// Style Section - Title (Primary Header)
		$this->start_controls_section(
			'style_title_section',
			[
				'label' => esc_html__( 'Primary Header (Table Title)', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_background',
			[
				'label' => esc_html__( 'Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#3A66B3',
				'selectors' => [
					'{{WRAPPER}} .omsar-table-title-wrapper' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-table-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_padding',
			[
				'label' => esc_html__( 'Padding', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '15',
					'right' => '20',
					'bottom' => '15',
					'left' => '20',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-table-title-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-table-title-wrapper' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Style Section - Table Header (Secondary Header)
		$this->start_controls_section(
			'style_header_section',
			[
				'label' => esc_html__( 'Secondary Header (Column Headers)', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'header_background',
			[
				'label' => esc_html__( 'Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#4A7EC0',
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
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-table thead th' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'header_border_color',
			[
				'label' => esc_html__( 'Border Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-table thead th' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .omsar-table thead' => 'border-bottom-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'header_border_width',
			[
				'label' => esc_html__( 'Border Width', 'omsar' ),
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
					'{{WRAPPER}} .omsar-table thead th' => 'border-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .omsar-table thead' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
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

		// Style Section - Row Titles (First Column)
		$this->start_controls_section(
			'style_row_titles_section',
			[
				'label' => esc_html__( 'Row Titles (First Column)', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'row_title_background',
			[
				'label' => esc_html__( 'Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f8f9fa',
				'selectors' => [
					'{{WRAPPER}} .omsar-table tbody th' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'row_title_text_color',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .omsar-table tbody th' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'row_title_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-table tbody th',
			]
		);

		$this->add_control(
			'row_title_padding',
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
					'{{WRAPPER}} .omsar-table tbody th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'row_title_alignment',
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
					'{{WRAPPER}} .omsar-table tbody th' => 'text-align: {{VALUE}};',
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
			'enable_alternating_rows',
			[
				'label' => esc_html__( 'Enable Alternating Row Colors', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'omsar' ),
				'label_off' => esc_html__( 'No', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'row_even_background',
			[
				'label' => esc_html__( 'Even Row Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#F0F0F0',
				'condition' => [
					'enable_alternating_rows' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-table tbody tr:nth-child(even) td' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .omsar-table tbody tr:nth-child(even) th' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'row_odd_background',
			[
				'label' => esc_html__( 'Odd Row Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'condition' => [
					'enable_alternating_rows' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-table tbody tr:nth-child(odd) td' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .omsar-table tbody tr:nth-child(odd) th' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'row_border_color',
			[
				'label' => esc_html__( 'Row Border Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#e0e0e0',
				'selectors' => [
					'{{WRAPPER}} .omsar-table tbody tr' => 'border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} .omsar-table tbody td' => 'border-right-color: {{VALUE}};',
					'{{WRAPPER}} .omsar-table tbody th' => 'border-right-color: {{VALUE}};',
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
					'{{WRAPPER}} .omsar-table tbody tr' => 'border-bottom-width: {{SIZE}}{{UNIT}}; border-bottom-style: solid;',
					'{{WRAPPER}} .omsar-table tbody td' => 'border-right-width: {{SIZE}}{{UNIT}}; border-right-style: solid;',
					'{{WRAPPER}} .omsar-table tbody th' => 'border-right-width: {{SIZE}}{{UNIT}}; border-right-style: solid;',
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
			'title_wrapper_border_radius',
			[
				'label' => esc_html__( 'Title Wrapper Border Radius (Top)', 'omsar' ),
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
					'{{WRAPPER}} .omsar-table-wrapper' => 'overflow: hidden;',
					'{{WRAPPER}} .omsar-table-title-wrapper' => 'border-top-left-radius: {{SIZE}}{{UNIT}}; border-top-right-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'table_bottom_border_radius',
			[
				'label' => esc_html__( 'Table Bottom Border Radius', 'omsar' ),
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
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-table' => 'border-bottom-left-radius: {{SIZE}}{{UNIT}}; border-bottom-right-radius: {{SIZE}}{{UNIT}}; border-top-left-radius: 0 !important; border-top-right-radius: 0 !important;',
					'{{WRAPPER}} .omsar-table tbody tr:last-child th:first-child' => 'border-bottom-left-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .omsar-table tbody tr:last-child td:first-child' => 'border-bottom-left-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .omsar-table tbody tr:last-child td:last-child' => 'border-bottom-right-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .omsar-table tbody tr:last-child th:last-child' => 'border-bottom-right-radius: {{SIZE}}{{UNIT}};',
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
	 * Render widget output on the frontend.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$table_title = ! empty( $settings['table_title'] ) ? esc_html( $settings['table_title'] ) : '';
		$table_columns = ! empty( $settings['table_columns'] ) ? $settings['table_columns'] : [];
		$table_row_titles = ! empty( $settings['table_row_titles'] ) ? $settings['table_row_titles'] : [];
		$row_column_title = ! empty( $settings['row_column_title'] ) ? esc_html( $settings['row_column_title'] ) : '';
		$show_export_icon = ! empty( $settings['show_export_icon'] ) && $settings['show_export_icon'] === 'yes';
		$show_zoom_icon = ! empty( $settings['show_zoom_icon'] ) && $settings['show_zoom_icon'] === 'yes';

		// Generate unique ID for this widget instance
		$widget_id = 'omsar-table-' . $this->get_id();
		$table_id = $widget_id . '-table';

		// Find the maximum number of rows across all columns and row titles
		$max_rows = count( $table_row_titles );
		foreach ( $table_columns as $column ) {
			$column_values = ! empty( $column['column_values'] ) ? $column['column_values'] : [];
			$row_count = count( $column_values );
			if ( $row_count > $max_rows ) {
				$max_rows = $row_count;
			}
		}

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
			#<?php echo esc_attr( $widget_id ); ?> .omsar-table-wrapper {
				overflow: hidden;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-table-title-wrapper {
				width: 100%;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-table {
				border-collapse: separate;
				border-spacing: 0;
				width: 100%;
				border: 1px solid #e0e0e0;
				border-top: none;
				margin: 0;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-table thead th {
				border-style: solid;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-table thead th:not(:last-child) {
				border-right-width: 1px;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-table tbody td:last-child,
			#<?php echo esc_attr( $widget_id ); ?> .omsar-table tbody th:last-child {
				border-right: none;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-table tbody tr:last-child td,
			#<?php echo esc_attr( $widget_id ); ?> .omsar-table tbody tr:last-child th {
				border-bottom: none;
			}
		</style>
		<div class="omsar-table-wrapper" id="<?php echo esc_attr( $widget_id ); ?>">
			<?php if ( ! empty( $table_title ) ) : ?>
				<div class="omsar-table-title-wrapper">
					<h3 class="omsar-table-title"><?php echo esc_html( $table_title ); ?></h3>
				</div>
			<?php endif; ?>
			<?php if ( $show_export_icon || $show_zoom_icon ) : ?>
				<div class="omsar-table-header">
					<div></div>
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
				</div>
			<?php endif; ?>
			
			<?php if ( ! empty( $table_columns ) ) : ?>
				<table class="omsar-table" id="<?php echo esc_attr( $table_id ); ?>">
					<thead>
						<tr>
							<th><?php echo esc_html( $row_column_title ); ?></th>
							<?php foreach ( $table_columns as $column ) : ?>
								<?php $column_title = ! empty( $column['column_title'] ) ? esc_html( $column['column_title'] ) : ''; ?>
								<th><?php echo esc_html( $column_title ); ?></th>
							<?php endforeach; ?>
						</tr>
					</thead>
					<tbody>
						<?php if ( $max_rows > 0 ) : ?>
							<?php for ( $row_index = 0; $row_index < $max_rows; $row_index++ ) : ?>
								<tr>
									<?php
									// First cell: row title
									$row_title = '';
									if ( isset( $table_row_titles[ $row_index ] ) && ! empty( $table_row_titles[ $row_index ]['row_title'] ) ) {
										$row_title = esc_html( $table_row_titles[ $row_index ]['row_title'] );
									}
									?>
									<th><?php echo $row_title; ?></th>
									<?php foreach ( $table_columns as $column ) : ?>
										<?php
										$column_values = ! empty( $column['column_values'] ) ? $column['column_values'] : [];
										$cell_value = '';
										if ( isset( $column_values[ $row_index ] ) && ! empty( $column_values[ $row_index ]['row_value'] ) ) {
											$cell_value = esc_html( $column_values[ $row_index ]['row_value'] );
										}
										?>
										<td><?php echo $cell_value; ?></td>
									<?php endforeach; ?>
								</tr>
							<?php endfor; ?>
						<?php else : ?>
							<?php
							// Show empty rows if no data provided
							for ( $i = 0; $i < 3; $i++ ) {
								echo '<tr>';
								echo '<th></th>';
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
				var tableColumns = <?php echo wp_json_encode( $table_columns ); ?>;
				var tableRowTitles = <?php echo wp_json_encode( $table_row_titles ); ?>;
				var maxRows = <?php echo intval( $max_rows ); ?>;

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
						
						// Get headers (include row column title)
						var headers = [];
						var headerCells = table.querySelectorAll('thead th');
						headerCells.forEach(function(cell) {
							headers.push('"' + cell.textContent.trim().replace(/"/g, '""') + '"');
						});
						csvContent.push(headers.join(','));

						// Get rows (include row titles)
						var rows = table.querySelectorAll('tbody tr');
						rows.forEach(function(row) {
							var rowData = [];
							var cells = row.querySelectorAll('th, td');
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
						
						// Copy styles from tbody th elements (row titles)
						var originalTbodyThs = table.querySelectorAll('tbody th');
						var clonedTbodyThs = clonedTable.querySelectorAll('tbody th');
						originalTbodyThs.forEach(function(originalTh, index) {
							if (clonedTbodyThs[index]) {
								var originalThStyles = window.getComputedStyle(originalTh);
								var clonedTh = clonedTbodyThs[index];
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
		var tableRowTitles = settings.table_row_titles || [];
		var rowColumnTitle = settings.row_column_title || '';
		var showExportIcon = settings.show_export_icon === 'yes';
		var showZoomIcon = settings.show_zoom_icon === 'yes';
		var widgetId = 'omsar-table-' + view.getIDInt();
		var tableId = widgetId + '-table';
		
		// Find the maximum number of rows across all columns and row titles
		var maxRows = tableRowTitles.length;
		_.each(tableColumns, function(column) {
			var columnValues = column.column_values || [];
			if (columnValues.length > maxRows) {
				maxRows = columnValues.length;
			}
		});
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
			#{{ widgetId }} .omsar-table-wrapper {
				overflow: hidden;
			}
			#{{ widgetId }} .omsar-table-title-wrapper {
				width: 100%;
			}
			#{{ widgetId }} .omsar-table {
				border-collapse: separate;
				border-spacing: 0;
				width: 100%;
				border: 1px solid #e0e0e0;
				border-top: none;
				margin: 0;
			}
			#{{ widgetId }} .omsar-table thead th {
				border-style: solid;
			}
			#{{ widgetId }} .omsar-table thead th:not(:last-child) {
				border-right-width: 1px;
			}
			#{{ widgetId }} .omsar-table tbody td:last-child,
			#{{ widgetId }} .omsar-table tbody th:last-child {
				border-right: none;
			}
			#{{ widgetId }} .omsar-table tbody tr:last-child td,
			#{{ widgetId }} .omsar-table tbody tr:last-child th {
				border-bottom: none;
			}
		</style>
		<div class="omsar-table-wrapper" id="{{ widgetId }}">
			<# if (tableTitle) { #>
				<div class="omsar-table-title-wrapper">
					<h3 class="omsar-table-title">{{{ tableTitle }}}</h3>
				</div>
			<# } #>
			<# if (showExportIcon || showZoomIcon) { #>
				<div class="omsar-table-header">
					<div></div>
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
				</div>
			<# } #>
			
			<# if (tableColumns.length > 0) { #>
				<table class="omsar-table" id="{{{ tableId }}}">
					<thead>
						<tr>
							<th>{{{ rowColumnTitle }}}</th>
							<# _.each(tableColumns, function(column) { #>
								<th>{{{ column.column_title || '' }}}</th>
							<# }); #>
						</tr>
					</thead>
					<tbody>
						<# if (maxRows > 0) { #>
							<# for (var rowIndex = 0; rowIndex < maxRows; rowIndex++) { #>
								<tr>
									<#
									var rowTitle = '';
									if (tableRowTitles[rowIndex] && tableRowTitles[rowIndex].row_title) {
										rowTitle = tableRowTitles[rowIndex].row_title;
									}
									#>
									<th>{{{ rowTitle }}}</th>
									<# _.each(tableColumns, function(column) { #>
										<#
										var columnValues = column.column_values || [];
										var cellValue = '';
										if (columnValues[rowIndex] && columnValues[rowIndex].row_value) {
											cellValue = columnValues[rowIndex].row_value;
										}
										#>
										<td>{{{ cellValue }}}</td>
									<# }); #>
								</tr>
							<# } #>
						<# } else { #>
							<# for (var i = 0; i < 3; i++) { #>
								<tr>
									<th></th>
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
						
						// Get headers (include row column title)
						var headers = [];
						var headerCells = table.querySelectorAll('thead th');
						headerCells.forEach(function(cell) {
							headers.push('"' + cell.textContent.trim().replace(/"/g, '""') + '"');
						});
						csvContent.push(headers.join(','));

						// Get rows (include row titles)
						var rows = table.querySelectorAll('tbody tr');
						rows.forEach(function(row) {
							var rowData = [];
							var cells = row.querySelectorAll('th, td');
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
						
						// Copy styles from tbody th elements (row titles)
						var originalTbodyThs = table.querySelectorAll('tbody th');
						var clonedTbodyThs = clonedTable.querySelectorAll('tbody th');
						originalTbodyThs.forEach(function(originalTh, index) {
							if (clonedTbodyThs[index]) {
								var originalThStyles = window.getComputedStyle(originalTh);
								var clonedTh = clonedTbodyThs[index];
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
