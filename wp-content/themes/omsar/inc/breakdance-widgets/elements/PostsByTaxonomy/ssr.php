<?php
/**
 * Breakdance SSR — List Posts by Taxonomy element.
 *
 * @var array $propertiesData
 * @var int|null $nodeId
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings   = omsar_bd_posts_by_taxonomy_settings_from_breakdance( $propertiesData );
$element_id = isset( $nodeId ) ? (string) $nodeId : wp_unique_id( 'omsar-bd-tax-' );

omsar_bd_render_posts_by_taxonomy_widget( $settings, $element_id );
