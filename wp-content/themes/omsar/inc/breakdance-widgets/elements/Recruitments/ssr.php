<?php
/**
 * Breakdance SSR — OMSAR Recruitments element.
 *
 * @var array $propertiesData
 * @var int|null $nodeId
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings   = omsar_bd_recruitments_settings_from_breakdance( $propertiesData );
$element_id = isset( $nodeId ) ? (string) $nodeId : wp_unique_id( 'omsar-bd-rec-' );

omsar_bd_render_recruitments_widget( $settings, $element_id );
