<?php

namespace OmsarBreakdanceElements;

\Breakdance\ElementStudio\registerElementForEditing(
	'OmsarBreakdanceElements',
	omsar_bd_get_element_studio_relative_path( __DIR__ ),
	'element',
	false
);

class CustomTimeline extends \Breakdance\Elements\Element {

	public static function name() {
		return 'Custom Timeline';
	}

	public static function uiIcon() {
		return 'TimelineIcon';
	}

	public static function slug() {
		return __CLASS__;
	}

	public static function category() {
		return 'omsar';
	}

	public static function className() {
		return 'omsar-custom-timeline-element';
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
			'content' => omsar_bd_timeline_default_content_properties(),
			'design'  => omsar_bd_timeline_default_design_properties(),
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
		return omsar_bd_timeline_content_controls();
	}

	public static function designControls() {
		return omsar_bd_timeline_design_controls();
	}

	public static function dependencies() {
		$theme = get_template_directory_uri();

		return array(
			array(
				'title'  => 'OMSAR Custom Timeline Styles',
				'styles' => array( $theme . '/assets/css/timeline-widget.css' ),
			),
		);
	}
}
