<?php
/**
 * Breakdance SSR — Statistics Card element.
 *
 * @var array $propertiesData
 * @var int|null $nodeId
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings   = omsar_bd_stat_card_settings_from_breakdance( $propertiesData );
$design     = $propertiesData['design'] ?? array();
$element_id = isset( $nodeId ) ? (string) $nodeId : wp_unique_id( 'omsar-bd-stat-card-' );

omsar_bd_render_stat_card_widget( $settings, $design, $element_id );
