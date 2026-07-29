<?php
/**
 * Breakdance SSR — Procurement Notices element.
 *
 * @var array $propertiesData
 * @var int|null $nodeId
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings   = omsar_bd_procurement_notices_settings_from_breakdance( $propertiesData );
$element_id = isset( $nodeId ) ? (string) $nodeId : wp_unique_id( 'omsar-bd-proc-' );

omsar_bd_render_procurement_notices_widget( $settings, $element_id );
