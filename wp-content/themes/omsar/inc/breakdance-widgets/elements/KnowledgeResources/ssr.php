<?php
/**
 * Breakdance SSR — Knowledge and Resources element.
 *
 * @var array $propertiesData
 * @var int|null $nodeId
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings   = omsar_bd_knowledge_resources_settings_from_breakdance( $propertiesData );
$element_id = isset( $nodeId ) ? (string) $nodeId : wp_unique_id( 'omsar-bd-kr-' );

omsar_bd_render_knowledge_resources_widget( $settings, $element_id );
