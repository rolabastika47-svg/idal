<?php
/**
 * OMSAR Breakdance custom elements (theme-based, mirrors elementor-widgets structure).
 *
 * @package OMSAR
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/inc/breakdance-widgets/helpers.php';

$omsar_bd_includes = OMSAR_BREAKDANCE_PATH . 'includes/';

require_once $omsar_bd_includes . 'element-design-controls.php';
require_once $omsar_bd_includes . 'recruitments-render.php';
require_once $omsar_bd_includes . 'recruitments-ajax.php';
require_once $omsar_bd_includes . 'projects-render.php';
require_once $omsar_bd_includes . 'projects-content-controls.php';
require_once $omsar_bd_includes . 'projects-design-controls.php';
require_once $omsar_bd_includes . 'posts-by-taxonomy-render.php';
require_once $omsar_bd_includes . 'posts-by-taxonomy-content-controls.php';
require_once $omsar_bd_includes . 'posts-by-taxonomy-design-controls.php';
require_once $omsar_bd_includes . 'procurement-notices-render.php';
require_once $omsar_bd_includes . 'procurement-notices-content-controls.php';
require_once $omsar_bd_includes . 'procurement-notices-design-controls.php';
require_once $omsar_bd_includes . 'knowledge-resources-render.php';
require_once $omsar_bd_includes . 'knowledge-resources-content-controls.php';
require_once $omsar_bd_includes . 'knowledge-resources-design-controls.php';
require_once $omsar_bd_includes . 'bar-chart-render.php';
require_once $omsar_bd_includes . 'bar-chart-content-controls.php';
require_once $omsar_bd_includes . 'bar-chart-design-controls.php';
require_once $omsar_bd_includes . 'data-table-render.php';
require_once $omsar_bd_includes . 'data-table-content-controls.php';
require_once $omsar_bd_includes . 'data-table-design-controls.php';
require_once $omsar_bd_includes . 'circular-chart-render.php';
require_once $omsar_bd_includes . 'circular-chart-content-controls.php';
require_once $omsar_bd_includes . 'circular-chart-design-controls.php';
require_once $omsar_bd_includes . 'line-chart-render.php';
require_once $omsar_bd_includes . 'line-chart-content-controls.php';
require_once $omsar_bd_includes . 'line-chart-design-controls.php';
require_once $omsar_bd_includes . 'static-card-list-render.php';
require_once $omsar_bd_includes . 'static-card-list-content-controls.php';
require_once $omsar_bd_includes . 'static-card-list-design-controls.php';
require_once $omsar_bd_includes . 'timeline-render.php';
require_once $omsar_bd_includes . 'timeline-content-controls.php';
require_once $omsar_bd_includes . 'timeline-design-controls.php';
require_once $omsar_bd_includes . 'animated-stats-counter-render.php';
require_once $omsar_bd_includes . 'animated-stats-counter-content-controls.php';
require_once $omsar_bd_includes . 'animated-stats-counter-design-controls.php';
require_once $omsar_bd_includes . 'stat-card-render.php';
require_once $omsar_bd_includes . 'stat-card-content-controls.php';
require_once $omsar_bd_includes . 'stat-card-design-controls.php';

/**
 * Load Breakdance elements as soon as Breakdance is available (see helpers.php).
 */
if ( did_action( 'breakdance_loaded' ) ) {
	omsar_bd_init_breakdance_elements();
} else {
	add_action( 'breakdance_loaded', 'omsar_bd_init_breakdance_elements', 9 );
}

/**
 * OMSAR category in the Breakdance element panel.
 */
add_filter(
	'breakdance_element_categories',
	function ( $categories ) {
		$categories[] = array(
			'slug'  => 'omsar',
			'label' => 'OMSAR',
		);
		return $categories;
	}
);

/**
 * Frontend assets for Breakdance OMSAR elements.
 */
function omsar_bd_enqueue_breakdance_assets() {
	if ( ! defined( '__BREAKDANCE_VERSION' ) ) {
		return;
	}

	$base_path = OMSAR_BREAKDANCE_PATH;
	$base_url  = OMSAR_BREAKDANCE_URL;

	$styles = array(
		'omsar-bd-recruitments'           => 'assets/css/recruitments.css',
		'omsar-bd-recruitment-notify'     => 'assets/css/recruitment-notify.css',
		'omsar-bd-projects'               => 'assets/css/projects.css',
		'omsar-bd-posts-by-taxonomy'      => 'assets/css/posts-by-taxonomy.css',
		'omsar-bd-procurement-notices'    => 'assets/css/procurement-notices.css',
		'omsar-bd-knowledge-resources'    => 'assets/css/knowledge-resources.css',
	);

	$static_card_list_css = get_template_directory() . '/assets/css/static-card-list-widget.css';
	if ( file_exists( $static_card_list_css ) ) {
		wp_enqueue_style(
			'omsar-bd-static-card-list',
			get_template_directory_uri() . '/assets/css/static-card-list-widget.css',
			array(),
			filemtime( $static_card_list_css )
		);
	}

	$timeline_css = get_template_directory() . '/assets/css/timeline-widget.css';
	if ( file_exists( $timeline_css ) ) {
		wp_enqueue_style(
			'omsar-bd-timeline',
			get_template_directory_uri() . '/assets/css/timeline-widget.css',
			array(),
			filemtime( $timeline_css )
		);
	}

	$stats_counter_css = get_template_directory() . '/assets/css/animated-stats-counter-widget.css';
	$stats_counter_js  = get_template_directory() . '/assets/js/animated-stats-counter-widget.js';
	if ( file_exists( $stats_counter_css ) ) {
		wp_enqueue_style(
			'omsar-bd-animated-stats-counter',
			get_template_directory_uri() . '/assets/css/animated-stats-counter-widget.css',
			array(),
			filemtime( $stats_counter_css )
		);
	}
	if ( file_exists( $stats_counter_js ) ) {
		wp_enqueue_script(
			'omsar-bd-animated-stats-counter',
			get_template_directory_uri() . '/assets/js/animated-stats-counter-widget.js',
			array( 'jquery' ),
			filemtime( $stats_counter_js ),
			true
		);
	}

	foreach ( $styles as $handle => $relative ) {
		$file = $base_path . $relative;
		if ( file_exists( $file ) ) {
			wp_enqueue_style( $handle, $base_url . $relative, array(), filemtime( $file ) );
		}
	}

	$theme_elementor_css = get_template_directory() . '/assets/css/elementor-style.css';
	if ( file_exists( $theme_elementor_css ) ) {
		wp_enqueue_style(
			'omsar-bd-elementor-style',
			get_template_directory_uri() . '/assets/css/elementor-style.css',
			array(),
			filemtime( $theme_elementor_css )
		);
	}

	$recruitments_js = $base_path . 'assets/js/recruitments.js';
	if ( file_exists( $recruitments_js ) ) {
		wp_enqueue_script(
			'omsar-bd-recruitments',
			$base_url . 'assets/js/recruitments.js',
			array( 'jquery' ),
			filemtime( $recruitments_js ),
			true
		);

		wp_localize_script(
			'omsar-bd-recruitments',
			'omsarBdRecruitments',
			array(
				'ajaxurl'       => admin_url( 'admin-ajax.php' ),
				'nonce'         => wp_create_nonce( 'omsar_bd_recruitments_nonce' ),
				'loadMoreText'  => function_exists( 'pll__' ) ? pll__( 'Load More' ) : __( 'Load More', 'omsar' ),
				'filterError'   => __( 'Error filtering recruitments. Please try again.', 'omsar' ),
				'loadMoreError' => __( 'Error loading more recruitments. Please try again.', 'omsar' ),
				'timeoutError'  => __( 'Request timed out. Please try again.', 'omsar' ),
			)
		);
	}

	$notify_js = $base_path . 'assets/js/recruitment-notify.js';
	if ( file_exists( $notify_js ) ) {
		wp_enqueue_script(
			'omsar-bd-recruitment-notify',
			$base_url . 'assets/js/recruitment-notify.js',
			array( 'jquery' ),
			filemtime( $notify_js ),
			true
		);

		wp_localize_script(
			'omsar-bd-recruitment-notify',
			'omsarBdRecruitmentNotify',
			array(
				'ajaxurl'        => admin_url( 'admin-ajax.php' ),
				'nonce'          => wp_create_nonce( 'recruitment_notify_nonce' ),
				'emailRequired'  => function_exists( 'pll__' ) ? pll__( 'Email address is required.' ) : __( 'Email address is required.', 'omsar' ),
				'emailInvalid'   => function_exists( 'pll__' ) ? pll__( 'Please enter a valid email address.' ) : __( 'Please enter a valid email address.', 'omsar' ),
				'successMessage' => function_exists( 'pll__' ) ? pll__( 'Thank you for your interest. You will be notified by email as soon as this position becomes available. Please check your junk folder frequently to avoid missing our notifications.' ) : __( 'Thank you for your interest. You will be notified by email as soon as this position becomes available. Please check your junk folder frequently to avoid missing our notifications.', 'omsar' ),
				'errorGeneric'   => function_exists( 'pll__' ) ? pll__( 'An error occurred. Please try again.' ) : __( 'An error occurred. Please try again.', 'omsar' ),
				'errorTimeout'   => function_exists( 'pll__' ) ? pll__( 'Request timed out. Please try again.' ) : __( 'Request timed out. Please try again.', 'omsar' ),
			)
		);
	}

	$theme_uri = get_template_directory_uri();

	$search_js = get_template_directory() . '/assets/js/posts-by-taxonomy-search.js';
	if ( file_exists( $search_js ) ) {
		wp_enqueue_script(
			'omsar-bd-posts-taxonomy-search',
			$theme_uri . '/assets/js/posts-by-taxonomy-search.js',
			array( 'jquery' ),
			filemtime( $search_js ),
			true
		);
	}

	$filters_js = get_template_directory() . '/assets/js/style4-filters.js';
	if ( file_exists( $filters_js ) ) {
		wp_enqueue_script(
			'omsar-bd-style4-filters',
			$theme_uri . '/assets/js/style4-filters.js',
			array( 'jquery' ),
			filemtime( $filters_js ),
			true
		);
	}

	$load_more_js = get_template_directory() . '/assets/js/posts-by-taxonomy-load-more.js';
	if ( file_exists( $load_more_js ) ) {
		wp_enqueue_script(
			'omsar-bd-posts-taxonomy-load-more',
			$theme_uri . '/assets/js/posts-by-taxonomy-load-more.js',
			array( 'jquery' ),
			filemtime( $load_more_js ),
			true
		);

		wp_localize_script(
			'omsar-bd-posts-taxonomy-load-more',
			'omsarTaxonomyLoadMore',
			array(
				'ajaxurl'  => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'omsar_taxonomy_load_more_nonce' ),
				'moreText' => function_exists( 'pll__' ) ? pll__( 'more' ) : __( 'more', 'omsar' ),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'omsar_bd_enqueue_breakdance_assets', 25 );
