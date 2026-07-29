<?php
/**
 * Path helpers for OMSAR Breakdance elements (theme-based).
 *
 * @package OMSAR
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'OMSAR_BREAKDANCE_PATH' ) ) {
	define( 'OMSAR_BREAKDANCE_PATH', get_template_directory() . '/inc/breakdance-widgets/' );
}

if ( ! defined( 'OMSAR_BREAKDANCE_URL' ) ) {
	define( 'OMSAR_BREAKDANCE_URL', get_template_directory_uri() . '/inc/breakdance-widgets/' );
}

// Backward-compatible aliases used by element dependency callbacks.
if ( ! defined( 'OMSAR_BREAKDANCE_ELEMENTS_PATH' ) ) {
	define( 'OMSAR_BREAKDANCE_ELEMENTS_PATH', OMSAR_BREAKDANCE_PATH );
}

if ( ! defined( 'OMSAR_BREAKDANCE_ELEMENTS_URL' ) ) {
	define( 'OMSAR_BREAKDANCE_ELEMENTS_URL', OMSAR_BREAKDANCE_URL );
}

/**
 * Breakdance Element Studio paths are resolved relative to wp-content/plugins.
 * Use a plugins-relative path that points into the active theme directory.
 *
 * @return string
 */
function omsar_bd_elements_relative_path() {
	$theme_slug = basename( get_template_directory() );

	return '../themes/' . $theme_slug . '/inc/breakdance-widgets/elements';
}

/**
 * @param string $element_dir Absolute path to an element folder.
 * @return string
 */
function omsar_bd_get_element_studio_relative_path( $element_dir ) {
	return omsar_bd_elements_relative_path() . '/' . basename( $element_dir );
}

/**
 * Data attributes used by Breakdance chart elements (editor preview).
 *
 * @param string               $type   bar|line|circular
 * @param array<string, mixed> $config Chart config array.
 * @param bool                 $breakdance Whether Breakdance SSR is rendering.
 * @return string
 */
function omsar_bd_chart_wrapper_data_attributes( $type, $config, $breakdance = false ) {
	if ( ! $breakdance ) {
		return '';
	}

	return sprintf(
		' data-omsar-chart-type="%s" data-omsar-chart-config="%s"',
		esc_attr( $type ),
		esc_attr( wp_json_encode( $config ) )
	);
}

/**
 * Shared script dependencies for OMSAR chart elements in Breakdance.
 *
 * @return array<int, array<string, mixed>>
 */
function omsar_bd_chart_dependencies() {
	$theme = get_template_directory_uri();
	$script = $theme . '/assets/js/omsar-breakdance-charts.js';

	return array(
		array(
			'title'   => 'Chart.js',
			'scripts' => array( 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js' ),
		),
		array(
			'title'   => 'OMSAR Breakdance Charts',
			'scripts' => array( $script ),
		),
	);
}

/**
 * Register save location and load element classes.
 *
 * Must run on breakdance_loaded (priority 9) or immediately after it — not on
 * after_setup_theme, or Breakdance will not discover theme elements in the builder.
 */
function omsar_bd_init_breakdance_elements() {
	if ( ! defined( '__BREAKDANCE_VERSION' ) && ! class_exists( '\Breakdance\Elements\Element' ) ) {
		return;
	}

	static $initialized = false;
	if ( $initialized ) {
		return;
	}
	$initialized = true;

	if ( function_exists( '\Breakdance\ElementStudio\registerSaveLocation' ) ) {
		\Breakdance\ElementStudio\registerSaveLocation(
			omsar_bd_elements_relative_path(),
			'OmsarBreakdanceElements',
			'element',
			'OMSAR Elements',
			false
		);
	}

	if ( function_exists( '\Breakdance\Elements\registerCategory' ) ) {
		\Breakdance\Elements\registerCategory( 'omsar', 'OMSAR' );
	}

	$elements_dir = OMSAR_BREAKDANCE_PATH . 'elements/';
	$folders      = glob( $elements_dir . '*', GLOB_ONLYDIR );

	if ( ! is_array( $folders ) ) {
		return;
	}

	foreach ( $folders as $folder ) {
		$element_file = $folder . '/element.php';
		if ( is_readable( $element_file ) ) {
			require_once $element_file;
		}
	}
}
