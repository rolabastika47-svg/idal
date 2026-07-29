<?php

namespace OmsarBreakdanceElements;

\Breakdance\ElementStudio\registerElementForEditing(
	'OmsarBreakdanceElements',
	omsar_bd_get_element_studio_relative_path( __DIR__ ),
	'element',
	false
);

class KnowledgeResources extends \Breakdance\Elements\Element {

	public static function name() {
		return 'Knowledge and Resources';
	}

	public static function slug() {
		return __CLASS__;
	}

	public static function category() {
		return 'omsar';
	}

	public static function className() {
		return 'omsar-knowledge-resources-element';
	}

	public static function tag() {
		return 'div';
	}

	public static function badge() {
		return array(
			'backgroundColor' => '#5693ff',
			'textColor'       => '#ffffff',
			'label'           => 'OMSAR',
		);
	}

	public static function defaultProperties() {
		return array(
			'content' => array(
				'content' => omsar_bd_knowledge_resources_default_settings(),
			),
			'design'  => omsar_bd_knowledge_resources_default_design_properties(),
		);
	}

	public static function defaultCss() {
		return file_get_contents( __DIR__ . '/default.css' );
	}

	public static function cssTemplate() {
		return file_get_contents( __DIR__ . '/css.twig' );
	}

	public static function template() {
		return file_get_contents( __DIR__ . '/html.twig' );
	}

	public static function contentControls() {
		return omsar_bd_knowledge_resources_content_controls();
	}

	public static function designControls() {
		return omsar_bd_knowledge_resources_design_controls();
	}

	public static function dependencies() {
		if ( ! defined( 'OMSAR_BREAKDANCE_ELEMENTS_URL' ) ) {
			return false;
		}

		$base  = OMSAR_BREAKDANCE_ELEMENTS_URL;
		$theme = get_template_directory_uri();

		$ajax_config = array(
			'ajaxurl'                 => admin_url( 'admin-ajax.php' ),
			'knowledgeResourcesNonce' => wp_create_nonce( 'load_more_knowledge_resources_nonce' ),
			'loadMoreText'            => function_exists( 'pll__' ) ? pll__( 'Load More' ) : __( 'Load More', 'omsar' ),
			'loadingText'             => function_exists( 'pll__' ) ? pll__( 'Loading...' ) : __( 'Loading...', 'omsar' ),
			'krSearchError'           => __( 'Error loading search results.', 'omsar' ),
		);

		return array(
			array(
				'title'  => 'OMSAR Knowledge Icons',
				'styles' => array( 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css' ),
			),
			array(
				'title'  => 'OMSAR Knowledge Theme Styles',
				'styles' => array( $theme . '/assets/css/knowledge-resources-widget.css' ),
			),
			array(
				'title'  => 'OMSAR Knowledge Breakdance Styles',
				'styles' => array( $base . 'assets/css/knowledge-resources.css' ),
			),
			array(
				'title'         => 'OMSAR Knowledge AJAX',
				'scripts'       => array( $theme . '/assets/js/ajax.js' ),
				'inlineScripts' => array(
					'window.omsarAjax = Object.assign(window.omsarAjax || {}, ' . wp_json_encode( $ajax_config ) . ');',
				),
			),
		);
	}
}
