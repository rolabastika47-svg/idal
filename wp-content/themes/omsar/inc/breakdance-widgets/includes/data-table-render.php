<?php
/**
 * Data Table rendering (shared between Elementor widget and Breakdance element).
 *
 * @package OmsarBreakdanceElements
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<string, mixed>
 */
function omsar_bd_data_table_default_settings() {
	return array(
		'table_title'      => __( 'Table title goes here', 'omsar' ),
		'show_export_icon' => 'no',
		'show_zoom_icon'   => 'no',
		'table_columns'    => array(
			array( 'column_label' => 'Column 1' ),
			array( 'column_label' => 'Column 2' ),
			array( 'column_label' => 'Column 3' ),
		),
		'table_rows'       => array(
			array( 'row_values' => 'Data 1 | Data 2 | Data 3' ),
			array( 'row_values' => 'Data 4 | Data 5 | Data 6' ),
		),
	);
}

/**
 * @return array<string, mixed>
 */
function omsar_bd_data_table_default_design_properties() {
	return array(
		'title'     => array(
			'color'     => '#000000',
			'alignment' => 'left',
			'margin'    => array(
				'margin' => array(
					'bottom' => array( 'number' => 20, 'unit' => 'px', 'style' => '20px' ),
				),
			),
		),
		'header'    => array(
			'background_color' => '#f8f9fa',
			'text_color'       => '#000000',
			'padding'          => array(
				'padding' => array(
					'top'    => array( 'number' => 12, 'unit' => 'px', 'style' => '12px' ),
					'right'  => array( 'number' => 16, 'unit' => 'px', 'style' => '16px' ),
					'bottom' => array( 'number' => 12, 'unit' => 'px', 'style' => '12px' ),
					'left'   => array( 'number' => 16, 'unit' => 'px', 'style' => '16px' ),
				),
			),
		),
		'body'      => array(
			'text_color'       => '#000000',
			'padding'          => array(
				'padding' => array(
					'top'    => array( 'number' => 12, 'unit' => 'px', 'style' => '12px' ),
					'right'  => array( 'number' => 16, 'unit' => 'px', 'style' => '16px' ),
					'bottom' => array( 'number' => 12, 'unit' => 'px', 'style' => '12px' ),
					'left'   => array( 'number' => 16, 'unit' => 'px', 'style' => '16px' ),
				),
			),
			'row_border_color' => '#e9ecef',
			'row_border_width' => array( 'number' => 1, 'unit' => 'px', 'style' => '1px' ),
		),
		'container' => array(
			'background_color'     => '#ffffff',
			'border_style'         => 'none',
			'border_color'         => '#dddddd',
			'border_radius'        => array( 'number' => 8, 'unit' => 'px', 'style' => '8px' ),
			'padding'              => array(
				'padding' => array(
					'top'    => array( 'number' => 20, 'unit' => 'px', 'style' => '20px' ),
					'right'  => array( 'number' => 20, 'unit' => 'px', 'style' => '20px' ),
					'bottom' => array( 'number' => 20, 'unit' => 'px', 'style' => '20px' ),
					'left'   => array( 'number' => 20, 'unit' => 'px', 'style' => '20px' ),
				),
			),
		),
		'advanced'  => array(
			'table_width' => array( 'number' => 100, 'unit' => '%', 'style' => '100%' ),
		),
	);
}

/**
 * @param mixed  $value
 * @param string $default
 * @return string
 */
function omsar_bd_data_table_yes_no( $value, $default = 'no' ) {
	if ( $value === 'yes' || $value === true || $value === 1 || $value === '1' ) {
		return 'yes';
	}
	if ( $value === 'no' || $value === false || $value === 0 || $value === '0' ) {
		return 'no';
	}
	if ( $value === null || $value === '' ) {
		return $default;
	}
	return (string) $value;
}

/**
 * @param array<string, mixed> $settings
 * @return array<string, mixed>
 */
function omsar_bd_normalize_data_table_settings( $settings ) {
	$defaults = omsar_bd_data_table_default_settings();
	$settings = wp_parse_args( is_array( $settings ) ? $settings : array(), $defaults );

	$settings['show_export_icon'] = omsar_bd_data_table_yes_no( $settings['show_export_icon'] ?? 'no', 'no' );
	$settings['show_zoom_icon']   = omsar_bd_data_table_yes_no( $settings['show_zoom_icon'] ?? 'no', 'no' );

	if ( ! is_array( $settings['table_columns'] ) ) {
		$settings['table_columns'] = $defaults['table_columns'];
	}
	if ( ! is_array( $settings['table_rows'] ) ) {
		$settings['table_rows'] = $defaults['table_rows'];
	}

	return omsar_bd_data_table_migrate_old_structure( $settings );
}

/**
 * @param array<string, mixed> $properties_data
 * @return array<string, mixed>
 */
function omsar_bd_data_table_settings_from_breakdance( $properties_data ) {
	$content = $properties_data['content'] ?? array();
	$table   = $content['table'] ?? array();
	$columns = $content['columns'] ?? array();
	$rows    = $content['rows'] ?? array();

	$settings = array(
		'table_title'      => isset( $table['table_title'] ) ? trim( (string) $table['table_title'] ) : '',
		'show_export_icon' => ! empty( $table['show_export_icon'] ),
		'show_zoom_icon'   => ! empty( $table['show_zoom_icon'] ),
		'table_columns'    => $columns['table_columns'] ?? array(),
		'table_rows'       => $rows['table_rows'] ?? array(),
	);

	return omsar_bd_normalize_data_table_settings( $settings );
}

/**
 * @param array<string, mixed> $settings
 * @return array<string, mixed>
 */
function omsar_bd_data_table_settings_from_elementor( $settings ) {
	return omsar_bd_normalize_data_table_settings( $settings );
}
function omsar_bd_data_table_migrate_old_structure( $settings ) {
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
 * Render the data table widget markup and scripts.
 *
 * @param array<string, mixed> $settings
 * @param string               $element_id
 */
function omsar_bd_render_data_table_widget( $settings, $element_id ) {
	$settings = omsar_bd_normalize_data_table_settings( $settings );
	$current_lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
	$is_arabic    = ( $current_lang === 'ar' || is_rtl() );
	$table_title = ! empty( $settings['table_title'] ) ? esc_html( $settings['table_title'] ) : '';
		$table_columns = ! empty( $settings['table_columns'] ) ? $settings['table_columns'] : [];
		$table_rows = ! empty( $settings['table_rows'] ) ? $settings['table_rows'] : [];
		$show_export_icon = ! empty( $settings['show_export_icon'] ) && $settings['show_export_icon'] === 'yes';
		$show_zoom_icon = ! empty( $settings['show_zoom_icon'] ) && $settings['show_zoom_icon'] === 'yes';

		// Generate unique ID for this widget instance
		$widget_id = 'omsar-table-' . $element_id;
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
			<?php if ( $is_arabic ) : ?>
			#<?php echo esc_attr( $widget_id ); ?> .omsar-table-title,
			#<?php echo esc_attr( $widget_id ); ?> .omsar-table thead th,
			#<?php echo esc_attr( $widget_id ); ?> .omsar-table tbody td,
			#<?php echo esc_attr( $widget_id ); ?> .omsar-table-empty-message {
				text-align: right;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-table {
				direction: rtl;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-table-header .omsar-table-title {
				text-align: right;
			}
			<?php endif; ?>
		</style>
		<div class="omsar-table-wrapper<?php echo $is_arabic ? ' omsar-bd-rtl' : ''; ?>" id="<?php echo esc_attr( $widget_id ); ?>"<?php echo $is_arabic ? ' dir="rtl"' : ''; ?>>
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

