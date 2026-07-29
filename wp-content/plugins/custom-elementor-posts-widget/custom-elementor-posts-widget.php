<?php
/**
 * Plugin Name: Custom Elementor Posts Widget
 * Plugin URI: https://omsar.com
 * Description: A custom Elementor widget to display posts with list or carousel style
 * Version: 1.0.0
 * Author: OMSAR
 * Author URI: https://omsar.com
 * Text Domain: custom-elementor-posts
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * Elementor tested up to: 3.0.0
 * Elementor Pro tested up to: 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main Custom Elementor Posts Widget Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @since 1.0.0
 */
final class Custom_Elementor_Posts_Widget {

	/**
	 * Plugin Version
	 *
	 * @since 1.0.0
	 * @var string The plugin version.
	 */
	const VERSION = '1.0.0';

	/**
	 * Minimum Elementor Version
	 *
	 * @since 1.0.0
	 * @var string Minimum Elementor version required to run the plugin.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.0.0
	 * @var string Minimum PHP version required to run the plugin.
	 */
	const MINIMUM_PHP_VERSION = '7.2';

	/**
	 * Instance
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 * @var Custom_Elementor_Posts_Widget The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @return Custom_Elementor_Posts_Widget An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ] );
	}

	/**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function i18n() {
		load_plugin_textdomain( 'custom-elementor-posts' );
	}

	/**
	 * On Plugins Loaded
	 *
	 * Checks if Elementor has loaded, and performs some compatibility checks.
	 * If All checks pass, inits the plugin.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function on_plugins_loaded() {
		if ( $this->is_compatible() ) {
			add_action( 'elementor/init', [ $this, 'init' ] );
		}
	}

	/**
	 * Compatibility Checks
	 *
	 * Checks if the installed version of Elementor meets the plugin requirements.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function is_compatible() {
		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return false;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return false;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return false;
		}

		return true;
	}

	/**
	 * Initialize the plugin
	 *
	 * Load the plugin only after Elementor (and other plugins) are loaded.
	 * Register the widget scripts and styles.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init() {
		$this->i18n();

		// Add Plugin actions
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'widget_styles' ] );
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'widget_scripts' ] );
	}

	/**
	 * Register Widgets
	 *
	 * Register new Elementor widgets.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 */
	public function register_widgets( $widgets_manager ) {
		require_once( __DIR__ . '/widgets/posts-widget.php' );
		$widgets_manager->register( new \Custom_Elementor_Posts_Widget\Widgets\Posts_Widget() );
	}

	/**
	 * Widget Styles
	 *
	 * Register widget styles.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function widget_styles() {
		// Check if Owl Carousel CSS is already enqueued by theme
		$owl_deps = [];
		if ( ! wp_style_is( 'owl-carousel-css', 'enqueued' ) && ! wp_style_is( 'owl-carousel-css', 'registered' ) ) {
			// Register Owl Carousel CSS if not already registered
			wp_register_style( 'owl-carousel-css', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css', [], '2.3.4' );
			wp_enqueue_style( 'owl-carousel-css' );
			wp_register_style( 'owl-theme-css', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css', [ 'owl-carousel-css' ], '2.3.4' );
			wp_enqueue_style( 'owl-theme-css' );
			$owl_deps[] = 'owl-theme-css';
		}
		
		wp_register_style( 'custom-elementor-posts-widget', plugins_url( 'assets/css/widget-posts.css', __FILE__ ), $owl_deps, self::VERSION );
		wp_enqueue_style( 'custom-elementor-posts-widget' );
	}

	/**
	 * Widget Scripts
	 *
	 * Register widget scripts.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function widget_scripts() {
		// Check if Owl Carousel is already enqueued by theme
		$owl_deps = [ 'jquery' ];
		if ( ! wp_script_is( 'owl-carousel-js', 'enqueued' ) && ! wp_script_is( 'owl-carousel-js', 'registered' ) ) {
			// Register Owl Carousel if not already registered
			wp_register_script( 'owl-carousel-js', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js', [ 'jquery' ], '2.3.4', true );
			wp_enqueue_script( 'owl-carousel-js' );
		}
		$owl_deps[] = 'owl-carousel-js';
		
		wp_register_script( 'custom-elementor-posts-widget', plugins_url( 'assets/js/widget-posts.js', __FILE__ ), $owl_deps, self::VERSION, true );
		wp_enqueue_script( 'custom-elementor-posts-widget' );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have Elementor installed or activated.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_missing_main_plugin() {
		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'custom-elementor-posts' ),
			'<strong>' . esc_html__( 'Custom Elementor Posts Widget', 'custom-elementor-posts' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'custom-elementor-posts' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version() {
		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'custom-elementor-posts' ),
			'<strong>' . esc_html__( 'Custom Elementor Posts Widget', 'custom-elementor-posts' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'custom-elementor-posts' ) . '</strong>',
			self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_php_version() {
		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'custom-elementor-posts' ),
			'<strong>' . esc_html__( 'Custom Elementor Posts Widget', 'custom-elementor-posts' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'custom-elementor-posts' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}
}

Custom_Elementor_Posts_Widget::instance();

