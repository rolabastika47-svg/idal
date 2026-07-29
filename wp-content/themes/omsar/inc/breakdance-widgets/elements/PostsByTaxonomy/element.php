<?php

namespace OmsarBreakdanceElements;

\Breakdance\ElementStudio\registerElementForEditing(
	'OmsarBreakdanceElements',
	omsar_bd_get_element_studio_relative_path( __DIR__ ),
	'element',
	false
);

class PostsByTaxonomy extends \Breakdance\Elements\Element {

	public static function name() {
		return 'List Posts by Taxonomy';
	}

	public static function uiIcon() {
		return 'ListIcon';
	}

	public static function slug() {
		return __CLASS__;
	}

	public static function category() {
		return 'omsar';
	}

	public static function className() {
		return 'omsar-posts-by-taxonomy-element';
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
				'content' => omsar_bd_posts_by_taxonomy_default_settings(),
			),
			'design'  => omsar_bd_posts_by_taxonomy_default_design_properties(),
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
		return omsar_bd_posts_by_taxonomy_content_controls();
	}

	public static function designControls() {
		return omsar_bd_posts_by_taxonomy_design_controls();
	}

	public static function dependencies() {
		if ( ! defined( 'OMSAR_BREAKDANCE_ELEMENTS_URL' ) ) {
			return false;
		}

		$base = OMSAR_BREAKDANCE_ELEMENTS_URL;

		return array(
			array(
				'title'  => 'OMSAR Posts by Taxonomy Icons',
				'styles' => array( 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css' ),
			),
			array(
				'title'  => 'OMSAR Posts by Taxonomy Theme Styles',
				'styles' => array( get_template_directory_uri() . '/assets/css/elementor-style.css' ),
			),
			array(
				'title'  => 'OMSAR Posts by Taxonomy Styles',
				'styles' => array( $base . 'assets/css/posts-by-taxonomy.css' ),
			),
		);
	}
}
