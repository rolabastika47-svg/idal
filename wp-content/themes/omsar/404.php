<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package OMSAR
 */

get_header();

// Get current language for translations
$current_lang = function_exists('pll_current_language') ? pll_current_language() : 'en';
$is_rtl = ($current_lang === 'ar' || is_rtl());
$go_home = function_exists('pll__') ? pll__('Go to Home') : __('Go to Home', 'omsar');
$home_url = function_exists('pll_home_url') ? pll_home_url($current_lang) : home_url('/');
?>

<div id="primary" class="content-area">
    <section class="error-404-section">
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-50">
                <div class="col-12 col-lg-8 col-xl-7">
                    <div class="error-404-content text-center">
                        <!-- Animated 404 Number -->
                        <div class="error-404-number">
                            <span class="error-number">4</span>
                            <span class="error-icon">
                                <i class="bi bi-exclamation-triangle"></i>
                            </span>
                            <span class="error-number">4</span>
                        </div>

                        <!-- Error Title -->
                        <h1 class="error-404-title">
                            <?php 
                            echo function_exists('pll__') 
                                ? pll__('Page Not Found') 
                                : esc_html__('Page Not Found', 'omsar'); 
                            ?>
                        </h1>

                        <!-- Error Message -->
                        <p class="error-404-message">
                            <?php 
                            echo function_exists('pll__') 
                                ? pll__('Sorry, the page you are looking for does not exist or has been moved') 
                                : esc_html__('Sorry, the page you are looking for does not exist or has been moved', 'omsar'); 
                            ?>
                        </p>

                        <a href="<?php echo esc_url($home_url); ?>" class="omsar-btn error-404-btn">
                            <?php echo esc_html($go_home); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
get_footer();
?>

