<?php
/**
 * Breakdance SSR — Static Card List element.
 *
 * @var array $propertiesData
 * @var int|null $nodeId
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings   = omsar_bd_static_card_list_settings_from_breakdance( $propertiesData );
$element_id = isset( $nodeId ) ? (string) $nodeId : wp_unique_id( 'omsar-bd-static-card-list-' );

omsar_bd_render_static_card_list_widget( $settings, $element_id );
