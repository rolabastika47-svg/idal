<?php

namespace OmsarBreakdanceElements;

use function Breakdance\Elements\c;
\Breakdance\ElementStudio\registerElementForEditing(
	'OmsarBreakdanceElements',
	omsar_bd_get_element_studio_relative_path( __DIR__ ),
	'element',
	false
);

class Recruitments extends \Breakdance\Elements\Element {

	public static function name() {
		return 'Recruitments';
	}

	public static function slug() {
		return __CLASS__;
	}

	public static function category() {
		return 'omsar';
	}

	public static function className() {
		return 'omsar-recruitments-element';
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
				'content' => array(
					'orderby'        => 'meta_value',
					'order'          => 'DESC',
					'columns'        => '3',
					'posts_per_page' => 9,
					'filter_label'   => '',
				),
			),
			'design'  => omsar_bd_recruitments_default_design_properties(),
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
		return array(
			c(
				'content',
				'Content',
				array(
					c(
						'orderby',
						'Order By',
						array(),
						array(
							'type'   => 'dropdown',
							'layout' => 'inline',
							'items'  => array(
								array( 'text' => 'Date', 'value' => 'date' ),
								array( 'text' => 'Title', 'value' => 'title' ),
								array( 'text' => 'Closing Date', 'value' => 'meta_value' ),
							),
						),
						false,
						false,
						array()
					),
					c(
						'order',
						'Order',
						array(),
						array(
							'type'   => 'dropdown',
							'layout' => 'inline',
							'items'  => array(
								array( 'text' => 'Descending', 'value' => 'DESC' ),
								array( 'text' => 'Ascending', 'value' => 'ASC' ),
							),
						),
						false,
						false,
						array()
					),
					c(
						'columns',
						'Columns',
						array(),
						array(
							'type'   => 'dropdown',
							'layout' => 'inline',
							'items'  => array(
								array( 'text' => '1', 'value' => '1' ),
								array( 'text' => '2', 'value' => '2' ),
								array( 'text' => '3', 'value' => '3' ),
								array( 'text' => '4', 'value' => '4' ),
							),
						),
						false,
						false,
						array()
					),
					c(
						'posts_per_page',
						'Posts Per Page',
						array(),
						array(
							'type'         => 'number',
							'layout'       => 'inline',
							'rangeOptions' => array(
								'min'  => 1,
								'max'  => 50,
								'step' => 1,
							),
						),
						false,
						false,
						array()
					),
					c(
						'filter_label',
						'Filter Label',
						array(),
						array(
							'type'        => 'text',
							'layout'      => 'vertical',
							'placeholder' => 'Filter by:',
						),
						false,
						false,
						array()
					),
				),
				array( 'type' => 'section' ),
				false,
				false,
				array()
			),
		);
	}

	public static function designControls() {
		return omsar_bd_recruitments_design_controls();
	}

	public static function dependencies() {
		if ( ! defined( 'OMSAR_BREAKDANCE_ELEMENTS_URL' ) ) {
			return false;
		}

		$base = OMSAR_BREAKDANCE_ELEMENTS_URL;

		$recruitments_config = array(
			'ajaxurl'       => admin_url( 'admin-ajax.php' ),
			'nonce'         => wp_create_nonce( 'omsar_bd_recruitments_nonce' ),
			'loadMoreText'  => function_exists( 'pll__' ) ? pll__( 'Load More' ) : 'Load More',
			'filterError'   => __( 'Error filtering recruitments. Please try again.', 'omsar' ),
			'loadMoreError' => __( 'Error loading more recruitments. Please try again.', 'omsar' ),
			'timeoutError'  => __( 'Request timed out. Please try again.', 'omsar' ),
		);

		$notify_config = array(
			'ajaxurl'        => admin_url( 'admin-ajax.php' ),
			'nonce'          => wp_create_nonce( 'recruitment_notify_nonce' ),
			'emailRequired'  => function_exists( 'pll__' ) ? pll__( 'Email address is required.' ) : __( 'Email address is required.', 'omsar' ),
			'emailInvalid'   => function_exists( 'pll__' ) ? pll__( 'Please enter a valid email address.' ) : __( 'Please enter a valid email address.', 'omsar' ),
			'successMessage' => function_exists( 'pll__' ) ? pll__( 'Thank you for your interest. You will be notified by email as soon as this position becomes available. Please check your junk folder frequently to avoid missing our notifications.' ) : __( 'Thank you for your interest. You will be notified by email as soon as this position becomes available. Please check your junk folder frequently to avoid missing our notifications.', 'omsar' ),
			'errorGeneric'   => function_exists( 'pll__' ) ? pll__( 'An error occurred. Please try again.' ) : __( 'An error occurred. Please try again.', 'omsar' ),
			'errorTimeout'   => function_exists( 'pll__' ) ? pll__( 'Request timed out. Please try again.' ) : __( 'Request timed out. Please try again.', 'omsar' ),
		);

		return array(
			array(
				'title'         => 'OMSAR Recruitments Icons',
				'styles'        => array( 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css' ),
			),
			array(
				'title'         => 'OMSAR Recruitments',
				'scripts'       => array( $base . 'assets/js/recruitments.js' ),
				'styles'        => array( $base . 'assets/css/recruitments.css' ),
				'inlineScripts' => array(
					'window.omsarBdRecruitments = ' . wp_json_encode( $recruitments_config ) . ';',
				),
			),
			array(
				'title'         => 'OMSAR Recruitment Notify',
				'scripts'       => array( $base . 'assets/js/recruitment-notify.js' ),
				'styles'        => array( $base . 'assets/css/recruitment-notify.css' ),
				'inlineScripts' => array(
					'window.omsarBdRecruitmentNotify = ' . wp_json_encode( $notify_config ) . ';',
				),
			),
		);
	}
}
