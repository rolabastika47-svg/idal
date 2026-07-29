<?php
/**
 * Breakdance SSR — Line Chart element.
 *
 * @var array $propertiesData
 * @var int|null $nodeId
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings   = omsar_bd_line_chart_settings_from_breakdance( $propertiesData );
$element_id = isset( $nodeId ) ? (string) $nodeId : wp_unique_id( 'omsar-bd-line-' );

omsar_bd_render_line_chart_widget( $settings, $element_id, array( 'breakdance' => true ) );
