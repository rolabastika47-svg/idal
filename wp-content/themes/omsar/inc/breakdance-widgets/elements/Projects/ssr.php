<?php
/**
 * Breakdance SSR — OMSAR Projects element.
 *
 * @var array $propertiesData
 * @var int|null $nodeId
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings   = omsar_bd_projects_settings_from_breakdance( $propertiesData );
$element_id = isset( $nodeId ) ? (string) $nodeId : wp_unique_id( 'omsar-bd-proj-' );

omsar_bd_render_projects_widget( $settings, $element_id );
