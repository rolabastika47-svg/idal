<?php
/**
 * Breakdance SSR — Custom Timeline element.
 *
 * @var array $propertiesData
 * @var int|null $nodeId
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings   = omsar_bd_timeline_settings_from_breakdance( $propertiesData );
$element_id = isset( $nodeId ) ? (string) $nodeId : wp_unique_id( 'omsar-bd-timeline-' );

omsar_bd_render_timeline_widget( $settings, $element_id );
