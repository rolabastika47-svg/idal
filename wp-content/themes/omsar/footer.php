<?php
// On inner pages: close site content wrapper before footer.
if (!is_front_page()) {
    echo '</div><!-- .omsar-site-content -->';
}

do_action('omsar_before_footer');

$current_lang = function_exists('pll_current_language')
    ? pll_current_language()
    : 'en';

// Footer styling for inner pages.
$footer_pattern_style = '';
if (!is_front_page() && function_exists('omsar_get_footer_pattern_style')) {
    $footer_pattern_style = omsar_get_footer_pattern_style();
}

$footer_main_color = '';
$footer_shadow_color = '';

if (!is_front_page() && function_exists('get_field')) {
    $footer_main_color   = get_field('footer_main_color', 'option');
    $footer_shadow_color = get_field('footer_shadow_color', 'option');
}

$style_parts = [];

if (
    !empty($footer_pattern_style)
    && preg_match('/style="([^"]*)"/', $footer_pattern_style, $matches)
) {
    $style_parts[] = $matches[1];
}

if (!is_front_page()) {
    if ($footer_main_color) {
        $style_parts[] = '--footer-main-color: ' . esc_attr($footer_main_color) . ';';
    }

    if ($footer_shadow_color) {
        $style_parts[] = '--footer-shadow-color: ' . esc_attr($footer_shadow_color) . ';';
    }
}

$footer_style = '';

if (!empty($style_parts)) {
    $footer_style = ' style="' . esc_attr(implode(' ', $style_parts)) . '"';
}
?>

<footer class="footer-section"<?php echo $footer_style; ?>>
    <div class="container">

        <div class="row contact-section">
            <!-- Contact Us -->
            <div class="col-12 col-lg-9 contact-us-column">
                <h5 class="footer-heading mb-4">Contact Us</h5>

                <div class="idal-footer-contact-grid">

                    <div class="idal-footer-contact-item idal-footer-address">
                        <span class="idal-footer-label">Address</span>
                        <span class="idal-footer-value">
                            Lazarieh Tower, 4th Floor, Emir Bechir Street,
                            Riad El-Solh, Beirut, Lebanon, P.O. Box 113-7251
                        </span>
                    </div>

                    <div class="idal-footer-contact-item">
                        <span class="idal-footer-label">Phone</span>
                        <a class="idal-footer-value" href="tel:+9611983306">
                            +961 1 983306
                        </a>
                    </div>

                    <div class="idal-footer-contact-item">
                        <span class="idal-footer-label">Fax</span>
                        <span class="idal-footer-value">+961 1 983302</span>
                    </div>

                    <div class="idal-footer-contact-item">
                        <span class="idal-footer-label">Email</span>
                        <a class="idal-footer-value" href="mailto:invest@idal.com.lb">
                            invest@idal.com.lb
                        </a>
                    </div>

                </div>
            </div>

            <!-- Social Media -->
            <div class="col-12 col-lg-3 d-flex justify-content-lg-end follow-us-wrapper">
                <div>
                    <h5 class="footer-heading mb-3">Follow Us</h5>

                    <div class="social-icons d-flex gap-3">

                        <a
                            href="https://www.facebook.com/InvestinLebanon"
                            class="social-icon-link idal-social-link"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="IDAL on Facebook"
                            title="Facebook"
                        >
                            <span aria-hidden="true">f</span>
                        </a>

                        <a
                            href="https://www.instagram.com/invest_lebanon/"
                            class="social-icon-link idal-social-link"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="IDAL on Instagram"
                            title="Instagram"
                        >
                            <span aria-hidden="true">◎</span>
                        </a>

                        <a
                            href="https://www.linkedin.com/company/investment-development-authority-of-lebanon"
                            class="social-icon-link idal-social-link"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="IDAL on LinkedIn"
                            title="LinkedIn"
                        >
                            <span aria-hidden="true">in</span>
                        </a>

                    </div>
                </div>
            </div>
        </div>

        <hr class="footer-divider">

        <div class="row py-4 copyright-row">
            <?php
            $copy_right_text_field = $current_lang === 'ar'
                ? 'copy_right_text_ar'
                : 'copy_right_text';

            $copy_right_designed_field = $current_lang === 'ar'
                ? 'copy_right_designed_by_ar'
                : 'copy_right_designed_by';

            $copy_right_text = function_exists('get_field')
                ? get_field($copy_right_text_field, 'option')
                : '';

            $copy_right_designed_by = function_exists('get_field')
                ? get_field($copy_right_designed_field, 'option')
                : '';

            $current_year = date('Y');

            $final_text = $copy_right_text
                ? str_replace('{year}', $current_year, $copy_right_text)
                : 'Investment Development Authority of Lebanon ' . $current_year . ' ©';
            ?>

            <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                <p class="mb-0 small text-muted copyright-text">
                    <?php echo esc_html($final_text); ?>
                </p>
            </div>

            <div class="col-md-6 text-center text-md-end">
                <p class="mb-0 small text-muted copyright-text">
                    <?php
                    if ($copy_right_designed_by) {
                        echo esc_html($copy_right_designed_by);
                        echo ' <a href="https://ids.com.lb/" target="_blank" rel="noopener noreferrer" style="color:#192D50;font-weight:bold;">IDS</a>';
                    } else {
                        echo 'Designed &amp; Developed By ';
                        echo '<a href="https://ids.com.lb/" target="_blank" rel="noopener noreferrer" style="color:#192D50;font-weight:bold;">IDS</a>';
                    }
                    ?>
                </p>
            </div>
        </div>

    </div>
</footer>

<?php
do_action('omsar_after_footer');

// On the front page, close the site-content wrapper after the footer.
if (is_front_page()) {
    echo '</div><!-- .omsar-site-content -->';
}

wp_footer();
?>

<script
    type="module"
    src="<?php echo esc_url(get_template_directory_uri() . '/assets/js/chatbot-widget.min.js?v=1.0.3'); ?>">
</script>

</body>
</html>