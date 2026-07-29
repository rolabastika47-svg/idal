<?php
/**
 * Breakdance SSR — Bar Chart element.
 *
 * @var array $propertiesData
 * @var int|null $nodeId
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings   = omsar_bd_bar_chart_settings_from_breakdance( $propertiesData );
$element_id = isset( $nodeId ) ? (string) $nodeId : wp_unique_id( 'omsar-bd-chart-' );

omsar_bd_render_bar_chart_widget( $settings, $element_id, array( 'breakdance' => true ) );
