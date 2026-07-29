<?php
/**
 * TGM Plugin Activation Configuration
 * 
 * This file configures the TGM Plugin Activation library to handle
 * required and recommended plugin installation and activation.
 * 
 * @package OMSAR
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Include the TGM_Plugin_Activation class.
 * 
 * Download TGM Plugin Activation from:
 * https://github.com/TGMPA/TGM-Plugin-Activation
 * 
 * Place the class-tgm-plugin-activation.php file in:
 * wp-content/themes/omsar/inc/tgm/class-tgm-plugin-activation.php
 */
require_once get_template_directory() . '/inc/tgm/class-tgm-plugin-activation.php';

/**
 * Register the required and recommended plugins for this theme.
 * 
 * Plugins are categorized as:
 * - 'required': Must be installed and activated for theme to work properly
 * - 'recommended': Suggested plugins that enhance theme functionality
 */
add_action( 'tgmpa_register', 'omsar_register_required_plugins' );

function omsar_register_required_plugins() {
	/**
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(

		// Required Plugins
		array(
			'name'     => 'Elementor',
			'slug'     => 'elementor',
			'required' => true,
		),
		array(
			'name'     => 'Advanced Custom Fields Pro',
			'slug'     => 'advanced-custom-fields-pro',
			'source'   => get_template_directory() . '/inc/plugins/advanced-custom-fields-pro.zip', // Path to plugin zip file
			'required' => true,
		),
		array(
			'name'     => 'Polylang',
			'slug'     => 'polylang',
			'required' => true,
		),

		// Recommended Plugins
		array(
			'name'     => 'Akismet Anti-Spam',
			'slug'     => 'akismet',
			'required' => false,
		),
		array(
			'name'     => 'Wordfence Security',
			'slug'     => 'wordfence',
			'required' => false,
		),
		array(
			'name'     => 'Limit Login Attempts Reloaded',
			'slug'     => 'limit-login-attempts-reloaded',
			'required' => false,
		),
		array(
			'name'     => 'WPS Hide Login',
			'slug'     => 'wps-hide-login',
			'required' => false,
		),
		array(
			'name'     => 'Custom Post Type UI',
			'slug'     => 'custom-post-type-ui',
			'required' => true,
		),
		array(
			'name'     => 'The Plus Addons for Elementor',
			'slug'     => 'the-plus-addons-for-elementor-page-builder',
			'required' => false,
		),
		array(
			'name'     => 'Advanced Google reCAPTCHA',
			'slug'     => 'advanced-google-recaptcha',
			'required' => true,
		),
		array(
			'name'     => 'UserWay Accessibility Widget',
			'slug'     => 'userway-accessibility-widget',
			'required' => false,
		),
		array(
			'name'     => 'OMSAR Social Share',
			'slug'     => 'omsar-social-share',
			'source'   => get_template_directory() . '/inc/plugins/omsar-social-share.zip', // Path to plugin zip file
			'required' => true,
		),
		array(
			'name'     => 'One Click Demo Import',
			'slug'     => 'one-click-demo-import',
			'required' => false,
		),

	);

	/**
	 * Array of configuration settings. Amend each line as needed.
	 * 
	 * TGMPA will start providing localized text strings soon. If you already have translations
	 * of our standard strings available, please help us make TGMPA even better by giving us access
	 * to these translations or by sending in a pull-request with .po file(s) with the translations.
	 * 
	 * Only uncomment the strings in the config array if you want to customize the strings.
	 */
	$config = array(
		'id'           => 'omsar',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins', // Menu slug.
		'parent_slug'  => 'themes.php',            // Parent menu slug.
		'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.

		/*
		'strings'      => array(
			'page_title'                      => __( 'Install Required Plugins', 'omsar' ),
			'menu_title'                      => __( 'Install Plugins', 'omsar' ),
			'installing'                      => __( 'Installing Plugin: %s', 'omsar' ),
			'updating'                        => __( 'Updating Plugin: %s', 'omsar' ),
			'oops'                            => __( 'Something went wrong with the plugin API.', 'omsar' ),
			'notice_can_install_required'     => _n_noop(
				'This theme requires the following plugin: %1$s.',
				'This theme requires the following plugins: %1$s.',
				'omsar'
			),
			'notice_can_install_recommended'  => _n_noop(
				'This theme recommends the following plugin: %1$s.',
				'This theme recommends the following plugins: %1$s.',
				'omsar'
			),
			'notice_ask_to_update'            => _n_noop(
				'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
				'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
				'omsar'
			),
			'notice_ask_to_update_maybe'      => _n_noop(
				'There is an update available for: %1$s.',
				'There are updates available for the following plugins: %1$s.',
				'omsar'
			),
			'notice_can_activate_required'    => _n_noop(
				'The following required plugin is currently inactive: %1$s.',
				'The following required plugins are currently inactive: %1$s.',
				'omsar'
			),
			'notice_ask_to_activate'          => _n_noop(
				'The following plugin needs to be activated: %1$s.',
				'The following plugins need to be activated: %1$s.',
				'omsar'
			),
			'install_link'                    => _n_noop(
				'Begin installing plugin',
				'Begin installing plugins',
				'omsar'
			),
			'update_link'                     => _n_noop(
				'Begin updating plugin',
				'Begin updating plugins',
				'omsar'
			),
			'activate_link'                   => _n_noop(
				'Begin activating plugin',
				'Begin activating plugins',
				'omsar'
			),
			'return'                          => __( 'Return to Required Plugins Installer', 'omsar' ),
			'plugin_activated'                => __( 'Plugin activated successfully.', 'omsar' ),
			'activated_successfully'          => __( 'The following plugin was activated successfully:', 'omsar' ),
			'plugin_already_active'           => __( 'No action taken. Plugin %1$s was already active.', 'omsar' ),
			'plugin_needs_higher_version'     => __( 'Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', 'omsar' ),
			'plugin_complete'                 => __( 'All plugins installed and activated successfully. %1$s', 'omsar' ),
			'dismiss'                         => __( 'Dismiss this notice', 'omsar' ),
			'notice_cannot_install_activate'  => __( 'There are one or more required or recommended plugins to install, update or activate.', 'omsar' ),
			'contact_admin'                   => __( 'Please contact the administrator of this site for help.', 'omsar' ),
			'nag_type'                        => '', // Determines admin notice type - can only be one of the typical WP notice classes, such as 'updated', 'update-nag', 'notice-warning', 'notice-info' or 'error'. Some of which may not work as expected in older WP versions.
		),
		*/
	);

	tgmpa( $plugins, $config );
}

/**
 * Display admin notice after theme activation
 * This notice will guide users to install required plugins
 */
add_action( 'admin_notices', 'omsar_theme_activation_notice' );

function omsar_theme_activation_notice() {
	// Check if TGM Plugin Activation is available
	if ( ! class_exists( 'TGM_Plugin_Activation' ) ) {
		return;
	}

	// Only show notice if there are plugins to install
	$instance = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
	$plugins  = $instance->plugins;

	$required_plugins = array_filter( $plugins, function( $plugin ) {
		return isset( $plugin['required'] ) && $plugin['required'] === true;
	} );

	// Check if any required plugins are not installed or not active
	$needs_attention = false;
	foreach ( $required_plugins as $plugin ) {
		if ( ! $instance->is_plugin_installed( $plugin['slug'] ) || ! $instance->is_plugin_active( $plugin['slug'] ) ) {
			$needs_attention = true;
			break;
		}
	}

	if ( $needs_attention ) {
		$install_url = admin_url( 'themes.php?page=tgmpa-install-plugins' );
		?>
		<!-- <div class="notice notice-info is-dismissible">
			<p>
				<strong><?php //esc_html_e( 'OMSAR Theme Setup', 'omsar' ); ?></strong><br>
				<?php //esc_html_e( 'Please install and activate the required plugins to complete the theme setup.', 'omsar' ); ?>
				<a href="<?php //echo esc_url( $install_url ); ?>" class="button button-primary" style="margin-left: 10px;">
					<?php //esc_html_e( 'Install Required Plugins', 'omsar' ); ?>
				</a>
			</p>
		</div> -->
		<?php
	}
}

/**
 * Display admin notice after plugins are installed
 * This notice reminds users to add languages in Polylang to prevent website issues
 */
add_action( 'admin_notices', 'omsar_polylang_language_setup_notice' );

function omsar_polylang_language_setup_notice() {
	// Check if TGM Plugin Activation is available
	if ( ! class_exists( 'TGM_Plugin_Activation' ) ) {
		return;
	}

	// Only show if all required plugins are installed and activated
	$instance = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
	
	// Check if TGMPA is complete (all required plugins installed and activated)
	if ( ! $instance->is_tgmpa_complete() ) {
		return;
	}

	// Check if Polylang is active
	if ( ! function_exists( 'PLL' ) && ! function_exists( 'pll_the_languages' ) ) {
		return;
	}

	// Check if Polylang has languages configured
	$languages = array();
	
	// Try different methods to get languages list (compatible with different Polylang versions)
	if ( function_exists( 'pll_languages_list' ) ) {
		$languages = pll_languages_list();
	} elseif ( function_exists( 'PLL' ) && is_object( PLL() ) && isset( PLL()->model ) && method_exists( PLL()->model, 'get_languages_list' ) ) {
		$languages = PLL()->model->get_languages_list();
	} elseif ( function_exists( 'PLL' ) && is_object( PLL() ) && isset( PLL()->model ) && method_exists( PLL()->model, 'get_languages' ) ) {
		$languages = PLL()->model->get_languages();
	}
	
	// If we still don't have languages, check the option directly
	if ( empty( $languages ) ) {
		$polylang_options = get_option( 'polylang' );
		if ( isset( $polylang_options['default_lang'] ) && ! empty( $polylang_options['default_lang'] ) ) {
			// At least default language is set
			$languages = array( $polylang_options['default_lang'] );
		}
	}

	// If no languages are configured, show the notice
	if ( empty( $languages ) ) {
		$polylang_settings_url = admin_url( 'admin.php?page=mlang' );
		?>
		<div class="notice notice-warning is-dismissible">
			<p>
				<strong><?php esc_html_e( '⚠️ Important: Configure Polylang Languages', 'omsar' ); ?></strong><br>
				<?php esc_html_e( 'Polylang plugin is installed but no languages have been added yet. Please add languages in Polylang settings to prevent the website from breaking.', 'omsar' ); ?>
				<a href="<?php echo esc_url( $polylang_settings_url ); ?>" class="button button-primary" style="margin-left: 10px;">
					<?php esc_html_e( 'Add Languages to Polylang', 'omsar' ); ?>
				</a>
			</p>
		</div>
		<?php
	}
}