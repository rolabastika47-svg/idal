<?php
/**
 * One Click Demo Import Configuration
 * 
 * This file configures the One Click Demo Import plugin to handle
 * demo content import (pages, posts, menus, widgets, customizer settings).
 * 
 * @package OMSAR
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Filter demo import files
 * 
 * Place your demo content files in:
 * wp-content/themes/omsar/demo-content/
 * 
 * Required files:
 * - demo-content.xml (WordPress export file with pages, posts, etc.)
 * 
 * Optional files:
 * - widgets.wie (Widget Importer & Exporter file) - Will be included if file exists and contains valid data
 * - customizer.dat (Customizer settings export) - Will be included if file exists
 * - preview.jpg (Preview image for demo import screen) - Will be included if file exists
 * 
 * Optional files for multi-language setups:
 * - demo-content-ar.xml (Arabic content)
 * - demo-content-en.xml (English content)
 */
/**
 * Register CPTs function - defined early so it can be called immediately
 */
if ( ! function_exists( 'omsar_ensure_cpt_registration' ) ) {
	function omsar_ensure_cpt_registration() {
		// CPT labels use translation functions; WordPress 6.7+ requires init or later.
		if ( ! did_action( 'init' ) && ! doing_action( 'init' ) ) {
			return;
		}

		// Ensure custom post types file is loaded
		$cpt_file = get_template_directory() . '/inc/custom-post-types.php';
		if ( file_exists( $cpt_file ) ) {
			require_once $cpt_file;
		}
		
		// Force registration of custom post types and taxonomies
		// This ensures they're available during import
		if ( function_exists( 'cptui_register_my_cpts' ) ) {
			cptui_register_my_cpts();
		}
		if ( function_exists( 'cptui_register_my_cpts_testimonials' ) ) {
			cptui_register_my_cpts_testimonials();
		}
		if ( function_exists( 'cptui_register_my_cpts_projects' ) ) {
			cptui_register_my_cpts_projects();
		}
		if ( function_exists( 'cptui_register_my_cpts_former_ministers' ) ) {
			cptui_register_my_cpts_former_ministers();
		}
		if ( function_exists( 'cptui_register_my_cpts_publication' ) ) {
			cptui_register_my_cpts_publication();
		}
		if ( function_exists( 'cptui_register_my_cpts_contact_us_forms' ) ) {
			cptui_register_my_cpts_contact_us_forms();
		}
		if ( function_exists( 'cptui_register_my_cpts_partnerships' ) ) {
			cptui_register_my_cpts_partnerships();
		}
		if ( function_exists( 'cptui_register_my_cpts_recruitments' ) ) {
			cptui_register_my_cpts_recruitments();
		}
		
		// Register taxonomies
		if ( function_exists( 'cptui_register_my_taxes' ) ) {
			cptui_register_my_taxes();
		}
		if ( function_exists( 'cptui_register_my_taxes_pillars' ) ) {
			cptui_register_my_taxes_pillars();
		}
		if ( function_exists( 'cptui_register_my_taxes_posts_type' ) ) {
			cptui_register_my_taxes_posts_type();
		}
		if ( function_exists( 'cptui_register_my_taxes_project_status' ) ) {
			cptui_register_my_taxes_project_status();
		}
		if ( function_exists( 'cptui_register_my_taxes_publication_category' ) ) {
			cptui_register_my_taxes_publication_category();
		}
		
		// Flush rewrite rules to ensure everything is registered
		flush_rewrite_rules( false );
	}
}

add_filter( 'ocdi/import_files', 'omsar_ocdi_import_files' );

function omsar_ocdi_import_files() {
	// Build import array with required files
	$import_files = array(
		'import_file_name'           => 'OMSAR Demo Content',
		'import_file_url'            => get_template_directory_uri() . '/demo-content/demo-content.xml',
		'import_notice'              => __( 'After importing the demo content, you may need to configure Polylang language settings and assign menus to menu locations. Custom post types and taxonomies will be automatically registered during import.', 'omsar' ),
		'preview_url'                => 'https://your-demo-site-url.com',
	);
	
	// Add widget file only if it exists and is valid (not empty)
	$widget_file = get_template_directory() . '/demo-content/widgets.wie';
	if ( file_exists( $widget_file ) ) {
		$widget_content = file_get_contents( $widget_file );
		// Check if file is not empty and contains valid widget data (not just [])
		if ( ! empty( trim( $widget_content ) ) && $widget_content !== '[]' ) {
			$import_files['import_widget_file_url'] = get_template_directory_uri() . '/demo-content/widgets.wie';
		}
	}
	
	// Add customizer file only if it exists
	$customizer_file = get_template_directory() . '/demo-content/customizer.dat';
	if ( file_exists( $customizer_file ) ) {
		$import_files['import_customizer_file_url'] = get_template_directory_uri() . '/demo-content/customizer.dat';
	}
	
	// Add preview image only if it exists
	$preview_image = get_template_directory() . '/demo-content/preview.jpg';
	if ( file_exists( $preview_image ) ) {
		$import_files['import_preview_image_url'] = get_template_directory_uri() . '/demo-content/preview.jpg';
	}
	
	return array( $import_files );
	// Add additional import sets for multi-language if needed
	// array(
	// 	'import_file_name'           => 'OMSAR Demo Content (Arabic)',
	// 	'import_file_url'            => get_template_directory_uri() . '/demo-content/demo-content-ar.xml',
	// 	'import_widget_file_url'     => get_template_directory_uri() . '/demo-content/widgets-ar.wie',
	// 	'import_customizer_file_url' => get_template_directory_uri() . '/demo-content/customizer-ar.dat',
	// 	'import_preview_image_url'   => get_template_directory_uri() . '/demo-content/preview-ar.jpg',
	// 	'import_notice'              => __( 'This will import Arabic demo content. Make sure Polylang is configured first.', 'omsar' ),
	// 	'preview_url'                => 'https://your-demo-site-url.com/ar',
	// ),
	// array(
	// 	'import_file_name'           => 'OMSAR Demo Content (English)',
	// 	'import_file_url'            => get_template_directory_uri() . '/demo-content/demo-content-en.xml',
	// 	'import_widget_file_url'     => get_template_directory_uri() . '/demo-content/widgets-en.wie',
	// 	'import_customizer_file_url' => get_template_directory_uri() . '/demo-content/customizer-en.dat',
	// 	'import_preview_image_url'   => get_template_directory_uri() . '/demo-content/preview-en.jpg',
	// 	'import_notice'              => __( 'This will import English demo content. Make sure Polylang is configured first.', 'omsar' ),
	// 	'preview_url'                => 'https://your-demo-site-url.com/en',
	// ),
}

/**
 * Action hook after demo import
 * 
 * This function runs after the demo content is imported.
 * Use it to set up menus, assign pages, configure settings, etc.
 */
add_action( 'ocdi/after_import', 'omsar_ocdi_after_import_setup' );

function omsar_ocdi_after_import_setup() {
	// Set a flag that menu assignment is needed
	// This ensures menus are assigned after theme setup completes
	set_transient( 'omsar_pending_menu_assignment', true, 300 ); // 5 minute expiry

	// Set front page (if you have a "Home" page in your demo content)
	$front_page = get_page_by_title( 'Home' );
	if ( $front_page ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front_page->ID );
	}

	// Set blog page (if you have a "Blog" page in your demo content)
	$blog_page = get_page_by_title( 'Blog' );
	if ( $blog_page ) {
		update_option( 'page_for_posts', $blog_page->ID );
	}

	// Configure Polylang language settings and assign languages to imported content
	if ( function_exists( 'pll_set_post_language' ) && function_exists( 'pll_save_post_translations' ) ) {
		// Get available languages
		$languages = pll_languages_list();
		
		if ( ! empty( $languages ) ) {
			// Set default language
			$default_lang = 'en'; // Change to 'ar' if Arabic is your default
			if ( ! in_array( $default_lang, $languages ) && ! empty( $languages ) ) {
				$default_lang = $languages[0]; // Use first available language
			}
			
			// Assign languages to all imported posts/pages
			omsar_assign_polylang_languages( $default_lang, $languages );
			
			// Link translations between posts
			omsar_link_polylang_translations( $languages );
		}
	}

	// Import ACF options page field values
	omsar_import_acf_options_page_values();

	// Flush rewrite rules to ensure custom post types work correctly
	flush_rewrite_rules();
}

/**
 * Assign menus after theme setup and Polylang initialization
 * 
 * This hook fires after after_setup_theme completes and ensures
 * menu locations are registered before assignment.
 * Priority 20 ensures Polylang has initialized (Polylang uses priority 10).
 */
add_action( 'init', 'omsar_delayed_menu_assignment', 20 );

function omsar_delayed_menu_assignment() {
	// Only proceed if menu assignment is pending
	if ( ! get_transient( 'omsar_pending_menu_assignment' ) ) {
		return;
	}
	
	// Ensure menu locations are registered
	// This is a safety check - they should already be registered via after_setup_theme
	$registered_locations = get_registered_nav_menus();
	if ( empty( $registered_locations ) ) {
		// If locations aren't registered yet, try again on next request
		// This handles edge cases where init fires before after_setup_theme
		return;
	}
	
	// Wait for Polylang to be ready if it's active
	if ( function_exists( 'PLL' ) || function_exists( 'pll_current_language' ) ) {
		// Check if Polylang languages are available
		if ( function_exists( 'pll_languages_list' ) ) {
			$languages = pll_languages_list();
			// If Polylang is active but languages aren't ready, wait
			if ( empty( $languages ) ) {
				return;
			}
		}
	}
	
	// Now assign menus
	omsar_assign_menus_to_locations();
	
	// Clear the flag
	delete_transient( 'omsar_pending_menu_assignment' );
}

/**
 * Fallback: Assign menus on admin_init (for admin requests during import)
 * 
 * This ensures menus are assigned even if init hook doesn't fire properly
 * during AJAX import requests.
 */
add_action( 'admin_init', 'omsar_delayed_menu_assignment_admin', 20 );

function omsar_delayed_menu_assignment_admin() {
	// Only proceed if menu assignment is pending
	if ( ! get_transient( 'omsar_pending_menu_assignment' ) ) {
		return;
	}
	
	// Ensure menu locations are registered
	$registered_locations = get_registered_nav_menus();
	if ( empty( $registered_locations ) ) {
		return;
	}
	
	// Wait for Polylang to be ready if it's active
	if ( function_exists( 'PLL' ) || function_exists( 'pll_current_language' ) ) {
		if ( function_exists( 'pll_languages_list' ) ) {
			$languages = pll_languages_list();
			if ( empty( $languages ) ) {
				return;
			}
		}
	}
	
	// Now assign menus
	omsar_assign_menus_to_locations();
	
	// Clear the flag
	delete_transient( 'omsar_pending_menu_assignment' );
}

/**
 * Assign menus to theme menu locations
 * 
 * This function automatically assigns imported menus to their proper locations.
 * It tries multiple matching strategies:
 * 1. Exact name match
 * 2. Case-insensitive match
 * 3. Partial name match (e.g., "Primary" matches "Primary Menu")
 * 4. For Polylang: Assigns menus per language if language-specific menus exist
 * 
 * IMPORTANT: This function should only be called after:
 * - after_setup_theme has completed (menu locations registered)
 * - Polylang has initialized (if active)
 * - Menu locations are verified to exist
 */
function omsar_assign_menus_to_locations() {
	// Verify menu locations are registered
	$registered_locations = get_registered_nav_menus();
	if ( empty( $registered_locations ) ) {
		// Menu locations not registered yet - this shouldn't happen if called correctly
		// but we'll return early to prevent errors
		return;
	}
	
	// Get all available menus
	$all_menus = get_terms( array(
		'taxonomy' => 'nav_menu',
		'hide_empty' => false,
	) );
	
	if ( empty( $all_menus ) || is_wp_error( $all_menus ) ) {
		return;
	}
	
	// Define menu location mappings
	// Format: 'location_slug' => array of possible menu names to match
	$menu_location_map = array(
		'primary_menu' => array(
			'primary menu',
			'primary',
			'main menu',
			'main',
			'header menu',
			'header',
			'primary navigation',
			'navigation',
		),
		'topbar_menu' => array(
			'top bar menu',
			'topbar menu',
			'top bar',
			'topbar',
			'secondary menu',
			'secondary',
			'utility menu',
			'utility',
		),
		'footer_menu' => array(
			'footer menu',
			'footer',
			'bottom menu',
			'bottom',
		),
	);
	
	// Check if Polylang is active
	$polylang_active = function_exists( 'PLL' ) || function_exists( 'pll_current_language' );
	$languages = array();
	
	if ( $polylang_active && function_exists( 'pll_languages_list' ) ) {
		$languages = pll_languages_list();
	}
	
	// Get current menu locations (may already have some assignments)
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	
	// Ensure $locations is an array
	if ( ! is_array( $locations ) ) {
		$locations = array();
	}
	
	// Assign menus to locations
	// For Polylang: If language-specific menus exist (e.g., "Primary Menu EN", "Primary Menu AR"),
	// we'll assign them. Otherwise, assign the best matching menu which Polylang will use for all languages.
	$locations_changed = false;
	
	foreach ( $registered_locations as $location_slug => $location_name ) {
		// Only process locations in our mapping
		if ( ! isset( $menu_location_map[ $location_slug ] ) ) {
			continue;
		}
		
		// Skip if already assigned (unless we're forcing reassignment)
		if ( isset( $locations[ $location_slug ] ) && $locations[ $location_slug ] > 0 ) {
			// Verify the assigned menu still exists
			$assigned_menu = wp_get_nav_menu_object( $locations[ $location_slug ] );
			if ( $assigned_menu && ! is_wp_error( $assigned_menu ) ) {
				continue;
			}
			// Menu doesn't exist anymore, clear it
			unset( $locations[ $location_slug ] );
			$locations_changed = true;
		}
		
		$possible_names = $menu_location_map[ $location_slug ];
		$menu_assigned = false;
		$found_menu = null;
		
		// If Polylang is active, try to find the default language menu first
		if ( $polylang_active && ! empty( $languages ) ) {
			// Get default language
			$default_lang = 'en'; // Default to English
			if ( function_exists( 'pll_default_language' ) ) {
				$default_lang = pll_default_language();
			} elseif ( ! empty( $languages ) ) {
				$default_lang = $languages[0];
			}
			
			// Try to find menu for default language (e.g., "Primary Menu EN")
			foreach ( $possible_names as $menu_name_pattern ) {
				$menu_name_variations = array(
					ucfirst( $menu_name_pattern ) . ' ' . strtoupper( $default_lang ),
					$menu_name_pattern . ' (' . strtoupper( $default_lang ) . ')',
					$menu_name_pattern . ' - ' . strtoupper( $default_lang ),
					$menu_name_pattern . ' ' . strtoupper( $default_lang ),
				);
				
				foreach ( $menu_name_variations as $menu_name_variation ) {
					$menu = get_term_by( 'name', $menu_name_variation, 'nav_menu' );
					if ( $menu && ! is_wp_error( $menu ) ) {
						$found_menu = $menu;
						$menu_assigned = true;
						break 2;
					}
				}
			}
		}
		
		// If no language-specific menu found, try generic menu name
		if ( ! $menu_assigned ) {
			foreach ( $possible_names as $menu_name_pattern ) {
				$menu = omsar_find_menu_by_name( $all_menus, $menu_name_pattern );
				
				if ( $menu ) {
					$found_menu = $menu;
					$menu_assigned = true;
					break;
				}
			}
		}
		
		// If still no menu found, try partial matching as last resort
		if ( ! $menu_assigned ) {
			foreach ( $all_menus as $menu ) {
				if ( is_wp_error( $menu ) ) {
					continue;
				}
				$menu_name_lower = strtolower( $menu->name );
				foreach ( $possible_names as $menu_name_pattern ) {
					if ( strpos( $menu_name_lower, strtolower( $menu_name_pattern ) ) !== false ||
						 strpos( strtolower( $menu_name_pattern ), $menu_name_lower ) !== false ) {
						$found_menu = $menu;
						$menu_assigned = true;
						break 2;
					}
				}
			}
		}
		
		// Assign the found menu
		if ( $found_menu && isset( $found_menu->term_id ) ) {
			$locations[ $location_slug ] = (int) $found_menu->term_id;
			$locations_changed = true;
		}
	}
	
	// Save the menu locations only if something changed
	if ( $locations_changed ) {
		// CRITICAL: Directly update the database option that WordPress uses
		// This bypasses any caching and ensures the menu locations are saved immediately
		$theme_slug = get_stylesheet();
		$option_name = "theme_mods_{$theme_slug}";
		
		// Get current theme mods
		$theme_mods = get_option( $option_name, array() );
		if ( ! is_array( $theme_mods ) ) {
			$theme_mods = array();
		}
		
		// Update nav_menu_locations in theme mods
		$theme_mods['nav_menu_locations'] = $locations;
		
		// Directly update the option (bypasses set_theme_mod caching)
		update_option( $option_name, $theme_mods, false ); // false = don't autoload
		
		// Also use set_theme_mod to trigger WordPress hooks
		set_theme_mod( 'nav_menu_locations', $locations );
		
		// CRITICAL: For Polylang, we need to ensure the menu assignment is recognized
		// Polylang filters menu locations by language, so we need to trigger its update mechanism
		if ( $polylang_active && function_exists( 'PLL' ) ) {
			// Polylang stores menu locations per language
			// We need to ensure Polylang recognizes the menu assignment
			
			// Trigger Polylang's menu location update
			// This is what happens when you save a menu in admin with Polylang active
			if ( isset( PLL()->nav_menu ) ) {
				// Clear Polylang's menu cache
				if ( isset( PLL()->nav_menu->cache ) ) {
					PLL()->nav_menu->cache = array();
				}
				
				// Trigger Polylang's menu location filter
				// This ensures Polylang recognizes the menu assignment
				if ( method_exists( PLL()->nav_menu, 'theme_mod_nav_menu_locations' ) ) {
					// This method is called when menu locations are updated
					PLL()->nav_menu->theme_mod_nav_menu_locations( $locations );
				}
			}
		}
		
		// CRITICAL: Trigger WordPress actions that fire when menu is saved
		// This ensures all hooks and filters run, including Polylang's
		foreach ( $locations as $location_slug => $menu_id ) {
			if ( isset( $menu_location_map[ $location_slug ] ) && $menu_id > 0 ) {
				// Trigger menu update action
				do_action( 'wp_update_nav_menu', $menu_id );
			}
		}
		
		// Trigger menu locations update action
		do_action( 'wp_update_nav_menu_locations', $locations );
		
		// Clear all caches
		wp_cache_delete( 'nav_menu_locations', 'theme_mods_' . $theme_slug );
		wp_cache_delete( $theme_slug, 'theme_mods' );
		wp_cache_delete( 'alloptions', 'options' );
		wp_cache_flush();
		
		// Clear Polylang cache
		if ( $polylang_active && function_exists( 'PLL' ) ) {
			if ( isset( PLL()->model ) && method_exists( PLL()->model, 'clean_languages_cache' ) ) {
				PLL()->model->clean_languages_cache();
			}
		}
		
		// Force WordPress to recognize the menu locations
		// This ensures menus appear on frontend immediately
		global $_wp_registered_nav_menus;
		$_wp_registered_nav_menus = get_registered_nav_menus();
		
		// FINAL STEP: Programmatically trigger the menu save that happens in admin
		// This ensures Polylang and WordPress both fully recognize the menu assignment
		// We do this by updating each menu object, which triggers the save mechanism
		foreach ( $locations as $location_slug => $menu_id ) {
			if ( isset( $menu_location_map[ $location_slug ] ) && $menu_id > 0 ) {
				$menu_obj = wp_get_nav_menu_object( $menu_id );
				if ( $menu_obj && ! is_wp_error( $menu_obj ) ) {
					// Update the menu term to trigger WordPress save mechanism
					// This is what happens when you click "Save Menu" in admin
					wp_update_term( $menu_id, 'nav_menu', array(
						'name' => $menu_obj->name,
						'slug' => $menu_obj->slug,
					) );
				}
			}
		}
	}
}

/**
 * Find a menu by name using multiple matching strategies
 * 
 * @param array $all_menus Array of menu term objects
 * @param string $search_name The name to search for
 * @return WP_Term|false Menu term object or false if not found
 */
function omsar_find_menu_by_name( $all_menus, $search_name ) {
	if ( empty( $all_menus ) || empty( $search_name ) ) {
		return false;
	}
	
	$search_name_lower = strtolower( trim( $search_name ) );
	
	foreach ( $all_menus as $menu ) {
		$menu_name = strtolower( trim( $menu->name ) );
		
		// 1. Exact match (case-insensitive)
		if ( $menu_name === $search_name_lower ) {
			return $menu;
		}
		
		// 2. Starts with match (e.g., "primary" matches "Primary Menu")
		if ( strpos( $menu_name, $search_name_lower ) === 0 ) {
			return $menu;
		}
		
		// 3. Contains match (e.g., "primary" matches "Main Primary Menu")
		if ( strpos( $menu_name, $search_name_lower ) !== false ) {
			return $menu;
		}
		
		// 4. Reverse: search name contains menu name (e.g., "primary menu" contains "primary")
		if ( strpos( $search_name_lower, $menu_name ) !== false ) {
			return $menu;
		}
	}
	
	return false;
}

/**
 * Assign Polylang languages to imported posts
 * 
 * @param string $default_lang Default language code
 * @param array $languages Array of available language codes
 */
function omsar_assign_polylang_languages( $default_lang, $languages ) {
	// Get all post types (including custom post types)
	$post_types = get_post_types( array( 'public' => true ), 'names' );
	// Exclude some post types
	$excluded_types = array( 'attachment', 'nav_menu_item', 'revision' );
	$post_types = array_diff( $post_types, $excluded_types );
	
	// Get all posts without language assignment
	$posts = get_posts( array(
		'post_type'      => $post_types,
		'posts_per_page' => -1,
		'post_status'    => 'any',
		'meta_query'     => array(
			array(
				'key'     => '_pll_lang',
				'compare' => 'NOT EXISTS',
			),
		),
	) );
	
	foreach ( $posts as $post ) {
		// Try to detect language from post meta or title
		$detected_lang = omsar_detect_post_language( $post, $languages, $default_lang );
		
		// Assign language to post
		if ( function_exists( 'pll_set_post_language' ) ) {
			pll_set_post_language( $post->ID, $detected_lang );
		}
	}
}

/**
 * Detect post language from meta data or content
 * 
 * @param WP_Post $post Post object
 * @param array $languages Available language codes
 * @param string $default_lang Default language code
 * @return string Detected language code
 */
function omsar_detect_post_language( $post, $languages, $default_lang ) {
	// Check if language is stored in post meta (from export)
	$lang_meta = get_post_meta( $post->ID, '_polylang_lang', true );
	if ( ! empty( $lang_meta ) && in_array( $lang_meta, $languages ) ) {
		return $lang_meta;
	}
	
	// Check Polylang meta
	$pll_lang = get_post_meta( $post->ID, '_pll_lang', true );
	if ( ! empty( $pll_lang ) && in_array( $pll_lang, $languages ) ) {
		return $pll_lang;
	}
	
	// Try to detect from title/content (simple heuristic)
	$title = $post->post_title;
	$content = $post->post_content;
	
	// Check for Arabic characters
	if ( preg_match( '/[\x{0600}-\x{06FF}]/u', $title . $content ) ) {
		if ( in_array( 'ar', $languages ) ) {
			return 'ar';
		}
	}
	
	// Default to default language
	return $default_lang;
}

/**
 * Link Polylang translations between posts
 * 
 * @param array $languages Array of available language codes
 */
function omsar_link_polylang_translations( $languages ) {
	if ( ! function_exists( 'pll_save_post_translations' ) || count( $languages ) < 2 ) {
		return;
	}
	
	// Get all post types
	$post_types = get_post_types( array( 'public' => true ), 'names' );
	$excluded_types = array( 'attachment', 'nav_menu_item', 'revision' );
	$post_types = array_diff( $post_types, $excluded_types );
	
	// Group posts by title (potential translations)
	$posts_by_title = array();
	$all_posts = get_posts( array(
		'post_type'      => $post_types,
		'posts_per_page' => -1,
		'post_status'    => 'any',
	) );
	
	foreach ( $all_posts as $post ) {
		// Use a normalized title for grouping (remove language-specific characters)
		$normalized_title = sanitize_title( $post->post_title );
		if ( ! isset( $posts_by_title[ $normalized_title ] ) ) {
			$posts_by_title[ $normalized_title ] = array();
		}
		$posts_by_title[ $normalized_title ][] = $post;
	}
	
	// Link translations based on title similarity
	foreach ( $posts_by_title as $title_group => $posts ) {
		if ( count( $posts ) >= 2 ) {
			$translations = array();
			
			foreach ( $posts as $post ) {
				$lang = pll_get_post_language( $post->ID );
				if ( $lang && ! isset( $translations[ $lang ] ) ) {
					$translations[ $lang ] = $post->ID;
				}
			}
			
			// If we have translations in multiple languages, link them
			if ( count( $translations ) >= 2 ) {
				pll_save_post_translations( $translations );
			}
		}
	}
	
	// Also check for translation links stored in post meta (from export)
	foreach ( $all_posts as $post ) {
		$translation_ids = get_post_meta( $post->ID, '_polylang_translations', true );
		if ( ! empty( $translation_ids ) && is_array( $translation_ids ) ) {
			// Verify these post IDs exist
			$valid_translations = array();
			foreach ( $translation_ids as $lang_code => $trans_post_id ) {
				if ( get_post( $trans_post_id ) && in_array( $lang_code, $languages ) ) {
					$valid_translations[ $lang_code ] = $trans_post_id;
				}
			}
			
			if ( count( $valid_translations ) >= 2 ) {
				pll_save_post_translations( $valid_translations );
			}
		}
	}
}

/**
 * Ensure custom post types and taxonomies are registered before import
 * This is critical - WordPress importer will skip content for unregistered post types
 * Hook into multiple points to ensure registration happens early
 */
add_action( 'ocdi/before_widgets_import', 'omsar_ensure_cpt_registration' );
add_action( 'ocdi/before_content_import', 'omsar_ensure_cpt_registration' );
add_action( 'wp_ajax_ocdi_import_content', 'omsar_ensure_cpt_registration', 1 );
add_action( 'wp_ajax_ocdi_import_widgets', 'omsar_ensure_cpt_registration', 1 );
add_action( 'admin_init', 'omsar_ensure_cpt_registration', 1 );
add_action( 'init', 'omsar_ensure_cpt_registration', 0 ); // Priority 0 = very early

// Function definition moved above - see earlier in file

/**
 * Filter to allow importing custom post types
 * Tell WordPress importer to import custom post types
 * This filter runs on the raw post data before processing
 */
add_filter( 'wp_import_post_data_raw', 'omsar_allow_cpt_import_raw', 10, 1 );
function omsar_allow_cpt_import_raw( $post ) {
	// Ensure custom post types are registered before processing
	omsar_ensure_cpt_registration();
	
	// Check if this is a custom post type
	if ( isset( $post['post_type'] ) && $post['post_type'] !== 'post' && $post['post_type'] !== 'page' && $post['post_type'] !== 'attachment' && $post['post_type'] !== 'nav_menu_item' ) {
		// Verify the post type is registered
		if ( ! post_type_exists( $post['post_type'] ) ) {
			// Force registration again
			omsar_ensure_cpt_registration();
		}
	}
	
	// Return post data as-is (allows import to proceed)
	return $post;
}

/**
 * Filter processed post data to ensure custom post types are imported
 */
add_filter( 'wp_import_post_data_processed', 'omsar_allow_cpt_import', 10, 2 );
function omsar_allow_cpt_import( $postdata, $post ) {
	// Ensure custom post types are registered before processing
	omsar_ensure_cpt_registration();
	
	// If post type doesn't exist, try to register it again
	if ( isset( $postdata['post_type'] ) && ! post_type_exists( $postdata['post_type'] ) ) {
		omsar_ensure_cpt_registration();
	}
	
	// Return post data as-is (allows import to proceed)
	return $postdata;
}

/**
 * Ensure custom post types are available during import initialization
 * Hook into import process early - these hooks fire during WordPress importer execution
 */
add_action( 'import_start', 'omsar_ensure_cpt_registration' );
add_action( 'wp_import_post_meta', 'omsar_ensure_cpt_registration', 1 );
add_action( 'wp_import_insert_post', 'omsar_ensure_cpt_registration', 1 );

/**
 * Assign Polylang language to posts during import
 * This runs as each post is imported, allowing us to assign language immediately
 */
add_action( 'wp_import_insert_post', 'omsar_assign_language_during_import', 10, 4 );
function omsar_assign_language_during_import( $post_id, $original_post_id, $postdata, $post ) {
	// Only process if Polylang is active
	if ( ! function_exists( 'pll_set_post_language' ) ) {
		return;
	}
	
	// Skip if post already has language assigned
	if ( pll_get_post_language( $post_id ) ) {
		return;
	}
	
	// Get available languages
	$languages = pll_languages_list();
	if ( empty( $languages ) ) {
		return;
	}
	
	$default_lang = 'en';
	if ( ! in_array( $default_lang, $languages ) && ! empty( $languages ) ) {
		$default_lang = $languages[0];
	}
	
	// Check for language in post meta (from export)
	$lang_meta = get_post_meta( $post_id, '_polylang_lang', true );
	if ( empty( $lang_meta ) ) {
		$lang_meta = get_post_meta( $post_id, '_pll_lang', true );
	}
	
	// Use detected language or default
	$lang_to_assign = $default_lang;
	if ( ! empty( $lang_meta ) && in_array( $lang_meta, $languages ) ) {
		$lang_to_assign = $lang_meta;
	} else {
		// Try to detect from title/content
		$post_obj = get_post( $post_id );
		if ( $post_obj ) {
			$detected = omsar_detect_post_language( $post_obj, $languages, $default_lang );
			$lang_to_assign = $detected;
		}
	}
	
	// Assign language
	pll_set_post_language( $post_id, $lang_to_assign );
}

/**
 * Override WordPress importer's post type check
 * The importer checks if post_type_exists() - we ensure it does
 */
add_filter( 'wp_import_post_data_processed', 'omsar_force_cpt_registration_during_import', 5, 2 );
function omsar_force_cpt_registration_during_import( $postdata, $post ) {
	// Register CPTs before WordPress checks if they exist
	omsar_ensure_cpt_registration();
	
	// If post type still doesn't exist, try to register it dynamically
	if ( isset( $postdata['post_type'] ) && ! post_type_exists( $postdata['post_type'] ) ) {
		// Force registration one more time
		omsar_ensure_cpt_registration();
		
		// If still doesn't exist, it might be a custom post type that needs registration
		// Log for debugging
		if ( ! post_type_exists( $postdata['post_type'] ) ) {
			error_log( 'OMSAR Import: Post type not found: ' . $postdata['post_type'] . ' - Post ID: ' . ( isset( $post['post_id'] ) ? $post['post_id'] : 'unknown' ) );
		}
	}
	
	return $postdata;
}

/**
 * Filter to prevent WordPress importer from skipping custom post types
 * This ensures all post types are imported, even if they're not standard WordPress types
 */
add_filter( 'wp_import_post_data_raw', 'omsar_prevent_cpt_skip', 10, 1 );
function omsar_prevent_cpt_skip( $post ) {
	// Ensure CPTs are registered
	omsar_ensure_cpt_registration();
	
	// If this is a custom post type, ensure it exists
	if ( isset( $post['post_type'] ) && 
		 $post['post_type'] !== 'post' && 
		 $post['post_type'] !== 'page' && 
		 $post['post_type'] !== 'attachment' && 
		 $post['post_type'] !== 'nav_menu_item' && 
		 $post['post_type'] !== 'revision' ) {
		
		// Force registration
		omsar_ensure_cpt_registration();
		
		// If still not registered, try to register a basic version
		if ( ! post_type_exists( $post['post_type'] ) ) {
			// Register a basic post type to prevent import failure
			register_post_type( $post['post_type'], array(
				'public' => true,
				'show_ui' => true,
				'show_in_menu' => true,
				'has_archive' => false,
				'supports' => array( 'title', 'editor', 'thumbnail' ),
			) );
		}
	}
	
	return $post;
}

/**
 * Filter to ensure custom post types are included in import
 * This tells the importer which post types to process
 */
add_filter( 'wp_import_post_terms', 'omsar_import_custom_taxonomies', 10, 3 );
function omsar_import_custom_taxonomies( $terms, $post_id, $post ) {
	// Ensure taxonomies are registered
	omsar_ensure_cpt_registration();
	
	// Return terms as-is
	return $terms;
}

/**
 * Recursively replace URLs in array/string values
 * 
 * @param mixed $value The value to process
 * @param string $old_url The old site URL to replace
 * @param string $new_url The new site URL to use
 * @return mixed The value with URLs replaced
 */
function omsar_replace_urls_in_value( $value, $old_url, $new_url ) {
	if ( is_array( $value ) ) {
		foreach ( $value as $key => $item ) {
			$value[ $key ] = omsar_replace_urls_in_value( $item, $old_url, $new_url );
		}
	} elseif ( is_string( $value ) && ! empty( $value ) ) {
		// Replace the old URL with the new URL
		$value = str_replace( $old_url, $new_url, $value );
	}
	return $value;
}

/**
 * Extract base URL from a URL string
 * 
 * @param string $url Full URL
 * @return string Base URL (scheme + domain + path before wp-content)
 */
function omsar_extract_base_url( $url ) {
	$parsed = parse_url( $url );
	if ( ! $parsed || ! isset( $parsed['scheme'] ) || ! isset( $parsed['host'] ) ) {
		return '';
	}
	
	$base = $parsed['scheme'] . '://' . $parsed['host'];
	if ( isset( $parsed['port'] ) ) {
		$base .= ':' . $parsed['port'];
	}
	
	// Extract path up to wp-content if it exists
	if ( isset( $parsed['path'] ) ) {
		$wpcontent_pos = strpos( $parsed['path'], '/wp-content' );
		if ( $wpcontent_pos !== false ) {
			$base .= substr( $parsed['path'], 0, $wpcontent_pos );
		} else {
			$base .= $parsed['path'];
		}
	}
	
	return rtrim( $base, '/' );
}

/**
 * Import ACF options page field values
 * 
 * ACF options pages store values in wp_options table, not post meta.
 * WordPress XML exporter doesn't export options table data, so we need
 * to handle options page values separately.
 * 
 * This function attempts to import options page values from:
 * 1. ACF JSON export files (if they exist in demo-content folder)
 * 2. Options data stored in the database (if imported separately)
 */
function omsar_import_acf_options_page_values() {
	// Only proceed if ACF is active
	if ( ! function_exists( 'update_field' ) || ! function_exists( 'acf_get_field_groups' ) ) {
		return;
	}
	
	// Get field groups assigned to the theme-options page
	$field_groups = acf_get_field_groups( array(
		'options_page' => 'theme-options',
	) );
	
	if ( empty( $field_groups ) ) {
		return;
	}
	
	// Get current site URL
	$current_site_url = home_url();
	
	// Try to import from ACF JSON file
	$json_file = get_template_directory() . '/demo-content/acf-options-page.json';
	if ( file_exists( $json_file ) ) {
		$options_data = json_decode( file_get_contents( $json_file ), true );
		if ( ! empty( $options_data ) && is_array( $options_data ) ) {
			// Detect old URL from the first URL found in the data
			$old_url = '';
			$json_string = file_get_contents( $json_file );
			if ( preg_match( '#https?://[^\s"\'<>]+#', $json_string, $matches ) ) {
				$old_url = omsar_extract_base_url( $matches[0] );
			}
			
			// Replace URLs if we found an old URL and it's different from current
			if ( ! empty( $old_url ) && $old_url !== omsar_extract_base_url( $current_site_url ) ) {
				$options_data = omsar_replace_urls_in_value( $options_data, $old_url, omsar_extract_base_url( $current_site_url ) );
			}
			
			foreach ( $options_data as $field_name => $field_value ) {
				// Update field value for options page (use 'option' as post_id)
				update_field( $field_name, $field_value, 'option' );
			}
			return;
		}
	}
	
	// Alternative: Import from exported options in a PHP file
	$php_file = get_template_directory() . '/demo-content/acf-options-page.php';
	if ( file_exists( $php_file ) ) {
		$options_data = include $php_file;
		if ( ! empty( $options_data ) && is_array( $options_data ) ) {
			// Detect and replace URLs if needed
			$options_data_serialized = serialize( $options_data );
			if ( preg_match( '#https?://[^\s"\'<>]+#', $options_data_serialized, $matches ) ) {
				$old_url = omsar_extract_base_url( $matches[0] );
				if ( ! empty( $old_url ) && $old_url !== omsar_extract_base_url( $current_site_url ) ) {
					$options_data = omsar_replace_urls_in_value( $options_data, $old_url, omsar_extract_base_url( $current_site_url ) );
				}
			}
			
			foreach ( $options_data as $field_name => $field_value ) {
				update_field( $field_name, $field_value, 'option' );
			}
			return;
		}
	}
}

/**
 * Hook into wp_import_post_meta to capture ACF options page values
 * Some export plugins may export options page values as post meta on a special post
 */
add_action( 'wp_import_post_meta', 'omsar_import_options_page_meta', 10, 3 );
function omsar_import_options_page_meta( $post_id, $key, $value ) {
	// Only process if ACF is active
	if ( ! function_exists( 'update_field' ) ) {
		return;
	}
	
	// Check if this is an options page field (stored with 'options_' prefix in options table)
	// Some export methods might export options as post meta temporarily
	if ( strpos( $key, 'options_' ) === 0 ) {
		// Remove 'options_' prefix to get the field name
		$field_name = substr( $key, 8 );
		
		// Unserialize if needed
		$field_value = maybe_unserialize( $value );
		
		// Save to options table using ACF's update_field function
		update_field( $field_name, $field_value, 'option' );
		
		// Also save directly to options table (for compatibility)
		update_option( $key, $field_value );
	}
}

/**
 * Disable generation of attachment images during import
 * This speeds up the import process
 */
add_filter( 'ocdi/regenerate_thumbnails_in_content_import', '__return_false' );

/**
 * Change the time limit for import
 * Some large imports may need more time
 */
add_filter( 'ocdi/time_for_one_ajax_call', function() {
	return 300; // 5 minutes (increase if needed)
} );

/**
 * Add custom import notice
 */
add_filter( 'ocdi/plugin_page_setup', 'omsar_ocdi_plugin_page_setup' );

function omsar_ocdi_plugin_page_setup( $default_settings ) {
	$default_settings['parent_slug'] = 'themes.php';
	$default_settings['page_title']  = esc_html__( 'Demo Import', 'omsar' );
	$default_settings['menu_title']  = esc_html__( 'Demo Import', 'omsar' );
	$default_settings['capability']   = 'import';
	$default_settings['menu_slug']   = 'omsar-demo-import';

	return $default_settings;
}
