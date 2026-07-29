<?php

namespace OmsarBreakdanceElements;

\Breakdance\ElementStudio\registerElementForEditing(
	'OmsarBreakdanceElements',
	omsar_bd_get_element_studio_relative_path( __DIR__ ),
	'element',
	false
);

class Projects extends \Breakdance\Elements\Element {

	public static function name() {
		return 'Projects';
	}

	public static function slug() {
		return __CLASS__;
	}

	public static function category() {
		return 'omsar';
	}

	public static function className() {
		return 'omsar-projects-element';
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
				'content' => omsar_bd_projects_default_settings(),
			),
			'design'  => omsar_bd_projects_default_design_properties(),
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
		return omsar_bd_projects_content_controls();
	}

	public static function designControls() {
		return omsar_bd_projects_design_controls();
	}

	public static function dependencies() {
		if ( ! defined( 'OMSAR_BREAKDANCE_ELEMENTS_URL' ) ) {
			return false;
		}

		$base = OMSAR_BREAKDANCE_ELEMENTS_URL;

		return array(
			array(
				'title'  => 'OMSAR Projects Icons',
				'styles' => array( 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css' ),
			),
			array(
				'title'  => 'OMSAR Projects Styles',
				'styles' => array( $base . 'assets/css/projects.css' ),
			),
		);
	}
}
