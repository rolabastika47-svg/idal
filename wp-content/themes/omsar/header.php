<!DOCTYPE html>
<?php
// Get current language and set direction
$current_lang = function_exists('pll_current_language') ? pll_current_language() : 'en';
$is_rtl = ($current_lang === 'ar' || is_rtl());
$html_dir = $is_rtl ? 'rtl' : 'ltr';
$html_lang = $current_lang;
?>
<html dir="<?php echo esc_attr($html_dir); ?>" lang="<?php echo esc_attr($html_lang); ?>">

<head>
    <!-- Basic Meta -->
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- Mobile Meta -->
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1.0, shrink-to-fit=no" />

    <!-- Favicon from WordPress Site Identity -->
    <?php if ( function_exists( 'has_site_icon' ) && has_site_icon() ) : ?>
        <?php wp_site_icon(); ?>
    <?php endif; ?>

    <!-- SEO Meta Tags (Title, Description, Open Graph, Twitter) handled by Yoast SEO -->
    <?php wp_head(); ?>
</head>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-6LG3M53E4G"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-6LG3M53E4G');
</script>

 <?php
    $logo_id = null;
    if($current_lang == 'ar'){
    $logo_id = get_field('logo_ar', 'option');
    }else{
    $logo_id = get_field('logo', 'option');
    }
    
    $logo_url = wp_get_attachment_image_url($logo_id, 'full');
 ?>

<body>
<?php 
    // Open background div for inner pages (not front page) right after body tag
    if (!is_front_page() && (is_page() || is_single()) && function_exists('omsar_get_background_pattern_style')) {
        echo '<div class="background"' . omsar_get_background_pattern_style() . '>';
    }
?>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-scroll header-menu">
        <div class="container">
            <!-- Logo (Order 1) -->
            <!-- <a class="navbar-brand me-auto me-lg-0" href="#">
                <img src="assets/images/omsar-logo.svg" alt="Republic of Lebanon OMSAR" class="logo-image">
            </a> -->
            <a href="<?php echo home_url(); ?>">
                <img src="<?php echo esc_url($logo_url); ?>" class="logo" height="100" width="100" />
            </a>

            <!-- Mobile Icons: Visible on all screens, positioned by flex -->
            <!-- Mobile: Order 2 (after logo, before burger) -->
            <!-- Desktop: Order 3 (after menu) -->
            <div class="d-flex align-items-center order-2 order-lg-3 ms-auto ms-lg-0 me-3 me-lg-0 action-icons mobile-actions-group">
                <div class="header-search-wrapper position-relative me-3">
                    <a href="#" class="text-dark search-icon-toggle" role="button" aria-label="<?php esc_attr_e('Toggle search', 'omsar'); ?>">
                        <i class="bi bi-search fs-5"></i>
                    </a>
                    <div class="header-search-dropdown">
                        <form role="search" method="get" class="header-search-form" action="<?php echo esc_url(omsar_get_search_url()); ?>">
                            <div class="search-input-wrapper">
                                <input 
                                    type="search" 
                                    class="header-search-input" 
                                    placeholder="<?php echo function_exists('pll__') ? esc_attr(pll__('Search...')) : esc_attr__('Search...', 'omsar'); ?>" 
                                    value="<?php echo get_search_query(); ?>" 
                                    name="s" 
                                    id="header-search-input"
                                    autocomplete="off"
                                    aria-label="<?php esc_attr_e('Search input', 'omsar'); ?>"
                                    required
                                />
                                <button type="submit" class="header-search-submit" aria-label="<?php esc_attr_e('Submit search', 'omsar'); ?>">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php 
                // Language switcher - preserve relevant query params when switching languages
                $other_lang_url = '';
                $other_lang_slug = '';
                
                // Get other language URL (WPML or Polylang)
                if ( defined( 'ICL_SITEPRESS_VERSION' ) && function_exists( 'icl_get_languages' ) ) {
                    foreach ( icl_get_languages( 'skip_missing=0' ) as $lang ) {
                        if ( $lang['language_code'] !== apply_filters( 'wpml_current_language', null ) ) {
                            $other_lang_url = isset( $lang['url'] ) ? $lang['url'] : '';
                            $other_lang_slug = isset( $lang['language_code'] ) ? $lang['language_code'] : '';
                            break;
                        }
                    }
                } elseif ( function_exists( 'pll_the_languages' ) ) {
                    foreach ( pll_the_languages( array( 'raw' => 1 ) ) as $lang ) {
                        if ( $lang['slug'] !== pll_current_language() ) {
                            $other_lang_url = isset( $lang['url'] ) ? $lang['url'] : '';
                            $other_lang_slug = isset( $lang['slug'] ) ? $lang['slug'] : '';
                            break;
                        }
                    }
                }
                
                // Preserve specific query parameters when switching languages.
                // This template relies on query params (e.g. procurement details page).
                if ( $other_lang_url ) {
                    $query_params_to_keep = array();
                    if ( isset( $_GET['minister_id'] ) && $_GET['minister_id'] !== '' ) {
                        $query_params_to_keep['minister_id'] = intval( $_GET['minister_id'] );
                    }
                    if ( isset( $_GET['procurement_id'] ) && $_GET['procurement_id'] !== '' ) {
                        $query_params_to_keep['procurement_id'] = absint( $_GET['procurement_id'] );
                    }

                    if ( ! empty( $query_params_to_keep ) ) {
                        $other_lang_url = add_query_arg( $query_params_to_keep, $other_lang_url );
                    }
                }
                
                // Display text
                $display_text = '';
                if ( $other_lang_slug === 'ar' ) $display_text = 'ع';
                elseif ( $other_lang_slug === 'en' ) $display_text = 'EN';
                elseif ( $other_lang_slug ) $display_text = strtoupper( $other_lang_slug[0] );
                
                // Output link or fallback
                if ( $other_lang_url && $display_text ) {
                    echo '<a href="' . esc_url( $other_lang_url ) . '" class="lang-toggle me-3">' . esc_html( $display_text ) . '</a>';
                } else {
                    omsar_lang_toggle();
                }
                ?>
            </div>

            <!-- Toggler (Burger Menu) -->
            <!-- Mobile: Order 3 (last) -->
            <button class="navbar-toggler collapsed order-3" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="icon-bar top-bar"></span>
                <span class="icon-bar middle-bar"></span>
                <span class="icon-bar bottom-bar"></span>
            </button>

            <!-- Menu Items -->
            <!-- Desktop: Order 2 (middle) -->
            <div class="collapse navbar-collapse order-lg-2" id="mainNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-medium">
                    <?php
                    // Get menu location for current language (Polylang compatible)
                    // Polylang automatically filters menu locations by current language
                    $menu_id = omsar_get_menu_location_id( 'primary_menu' );

                    if ( $menu_id ) {
                        $menu = wp_get_nav_menu_object( $menu_id );
                        
                        if ( $menu ) {
                            // Get menu items
                            // Note: wp_get_nav_menu_items respects Polylang's language filtering
                            $menu_items = wp_get_nav_menu_items( $menu->term_id );

                            if ( $menu_items && ! empty( $menu_items ) ) {
                                $parents  = [];
                                $children = [];

                                foreach ( $menu_items as $item ) {
                                    if ( $item->menu_item_parent == 0 ) {
                                        $parents[] = $item;
                                    } else {
                                        $children[ $item->menu_item_parent ][] = $item;
                                    }
                                }

                                foreach ( $parents as $item ) {
                                    $has_children = isset( $children[ $item->ID ] );
                                    $is_active = in_array( 'current-menu-item', $item->classes ) ||
                                                in_array( 'current-menu-parent', $item->classes ) ||
                                                in_array( 'current-menu-ancestor', $item->classes );

                                    if ( $has_children ) {
                                        echo '<li class="nav-item dropdown">';
                                        echo '<a class="nav-link dropdown-toggle' . ( $is_active ? ' active' : '' ) . '" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">';
                                        echo esc_html( $item->title );
                                        echo '</a>';
                                        echo '<ul class="dropdown-menu">';

                                        foreach ( $children[ $item->ID ] as $child ) {
                                            echo '<li><a class="dropdown-item" href="' . esc_url( $child->url ) . '">' . esc_html( $child->title ) . '</a></li>';
                                        }

                                        echo '</ul>';
                                        echo '</li>';
                                    } else {
                                        echo '<li class="nav-item">';
                                        echo '<a class="nav-link' . ( $is_active ? ' active' : '' ) . '" href="' . esc_url( $item->url ) . '">';
                                        echo esc_html( $item->title );
                                        echo '</a>';
                                        echo '</li>';
                                    }
                                }
                            }
                        }
                    }
                    ?>
                </ul>
            </div>

        </div>
    </nav>

    <?php
    // Custom hook for content after header (for page banner, etc.)
    do_action('omsar_after_header');
    ?>
    <div class="omsar-site-content" id="omsar-site-content">

