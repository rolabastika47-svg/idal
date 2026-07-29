<?php
/**
 * Template Name: Survey Success
 * Displays after successful  survey submission (thank you message + home button).
 * Use Polylang: create one page per language with this template and link them as translations.
 *
 * @package OMSAR
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$current_lang = function_exists('pll_current_language') ? pll_current_language() : 'en';
$is_rtl = ($current_lang === 'ar' || is_rtl());
$thank_you = function_exists('pll__') ? pll__('Thank you for completing the survey.') : __('Thank you for completing the survey.', 'omsar');
$go_home = function_exists('pll__') ? pll__('Go to Home') : __('Go to Home', 'omsar');
$home_url = function_exists('pll_home_url') ? pll_home_url($current_lang) : home_url('/');
?>

<div id="primary" class="content-area">
    <section class="omsar-survey-success-section">
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-50">
                <div class="col-12 col-lg-8 col-xl-6">
                    <div class="omsar-survey-success-content text-center">
                        <div class="omsar-survey-success-icon" aria-hidden="true">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <h1 class="omsar-survey-success-title">
                            <?php echo esc_html($thank_you); ?>
                        </h1>
                        <a href="<?php echo esc_url($home_url); ?>" class="omsar-btn omsar-survey-success-btn">
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
