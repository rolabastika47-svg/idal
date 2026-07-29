<?php
/**
 * Breakdance SSR — Animated Stats Counter element.
 *
 * @var array $propertiesData
 * @var int|null $nodeId
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings   = omsar_bd_animated_stats_counter_settings_from_breakdance( $propertiesData );
$element_id = isset( $nodeId ) ? (string) $nodeId : wp_unique_id( 'omsar-bd-stats-counter-' );

omsar_bd_render_animated_stats_counter_widget( $settings, $element_id );
