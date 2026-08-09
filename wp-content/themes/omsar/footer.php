<?php
// Close inner-page site content wrapper before footer.
if (!is_front_page()) {
    echo '</div><!-- .omsar-site-content -->';
}

do_action('omsar_before_footer');

$current_lang = function_exists('pll_current_language')
    ? pll_current_language()
    : 'en';

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

<?php if (is_page(26733)) : ?>

<style>
.idal-about-footer,
.idal-about-footer * {
    box-sizing: border-box;
}

.idal-about-footer {
    width: 100%;
    margin: 0;
    padding: 70px 60px 32px;
    color: #ffffff;
    background: linear-gradient(
        105deg,
        #08B8A2 0%,
        #0AAEC5 42%,
        #2056C8 100%
    );
    font-family: "Inter", Arial, sans-serif;
}

.idal-about-footer-main {
    width: 100%;
    max-width: 1800px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1.15fr 1fr 0.9fr;
    gap: 90px;
    align-items: start;
    min-height: 345px;
}

.idal-about-footer-column {
    min-width: 0;
}

.idal-about-footer-logo {
    margin: 0 0 24px;
    color: #ffffff !important;
    font-size: 38px;
    line-height: 1;
    font-weight: 800;
}

.idal-about-footer-description {
    margin: 0 0 31px;
    color: #ffffff !important;
    font-size: 18px;
    line-height: 1.7;
    font-weight: 400;
}

.idal-about-footer-title {
    margin: 0 0 25px;
    color: #ffffff !important;
    font-size: 14px;
    line-height: 1.3;
    font-weight: 800;
    letter-spacing: 1.6px;
    text-transform: uppercase;
}

.idal-about-socials {
    display: flex;
    align-items: center;
    gap: 12px;
}

.idal-about-social {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    background: rgba(255,255,255,0.16);
    color: #ffffff !important;
    text-decoration: none !important;
    font-size: 15px;
    font-weight: 700;
    line-height: 1;
    transition:
        transform 0.2s ease,
        background-color 0.2s ease;
}

.idal-about-social:hover {
    background: rgba(255,255,255,0.28);
    transform: translateY(-3px);
    color: #ffffff !important;
}

.idal-about-contact p {
    margin: 0 0 18px;
    color: #ffffff !important;
    font-size: 17px;
    line-height: 1.55;
}

.idal-about-contact a {
    color: #ffffff !important;
    text-decoration: none;
}

.idal-about-contact a:hover {
    text-decoration: underline;
}

.idal-about-links {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.idal-about-links > a {
    display: block;
    margin: 0 0 18px;
    color: #ffffff !important;
    font-size: 17px;
    text-decoration: none;
    transition: opacity 0.2s ease;
}

.idal-about-links > a:hover {
    opacity: 0.75;
    color: #ffffff !important;
}

.idal-about-footer-bottom {
    width: 100%;
    max-width: 1800px;
    margin: 0 auto;
    padding-top: 31px;
    border-top: 1px solid rgba(255,255,255,0.23);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 30px;
    color: rgba(255,255,255,0.92);
    font-size: 15px;
}

.idal-about-footer-policy {
    display: flex;
    align-items: center;
    gap: 8px;
}

.idal-about-footer-policy a {
    color: rgba(255,255,255,0.92) !important;
    text-decoration: none;
}

.idal-about-footer-policy a:hover {
    color: #ffffff !important;
    text-decoration: underline;
}

@media (max-width: 1024px) {
    .idal-about-footer {
        padding: 60px 35px 30px;
    }

    .idal-about-footer-main {
        grid-template-columns: 1fr 1fr;
        gap: 55px;
        min-height: auto;
    }

    .idal-about-links {
        grid-column: 1 / -1;
    }
}

@media (max-width: 767px) {
    .idal-about-footer {
        padding: 50px 25px 28px;
    }

    .idal-about-footer-main {
        display: flex;
        flex-direction: column;
        gap: 45px;
    }

    .idal-about-footer-description br {
        display: none;
    }

    .idal-about-footer-bottom {
        flex-direction: column;
        align-items: flex-start;
        gap: 18px;
    }
}
</style>

<footer class="idal-about-footer">

    <div class="idal-about-footer-main">

        <div class="idal-about-footer-column">

            <h2 class="idal-about-footer-logo">IDAL</h2>

            <p class="idal-about-footer-description">
                Connecting investors and exporters to<br>
                Lebanon's strategic opportunities and<br>
                gateway to global markets.
            </p>

            <h4 class="idal-about-footer-title">FOLLOW US</h4>

            <div class="idal-about-socials">

                <a
                    href="https://www.linkedin.com/company/investment-development-authority-of-lebanon"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="idal-about-social"
                    aria-label="IDAL on LinkedIn"
                    title="LinkedIn"
                >
                    in
                </a>

                <a
                    href="https://www.instagram.com/invest_lebanon/"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="idal-about-social"
                    aria-label="IDAL on Instagram"
                    title="Instagram"
                >
                    ig
                </a>

                <a
                    href="https://twitter.com/Invest_Lebanon"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="idal-about-social"
                    aria-label="IDAL on X"
                    title="X"
                >
                    x
                </a>

                <a
                    href="https://www.facebook.com/InvestinLebanon"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="idal-about-social"
                    aria-label="IDAL on Facebook"
                    title="Facebook"
                >
                    f
                </a>

            </div>
        </div>

        <div class="idal-about-footer-column idal-about-contact">

            <h4 class="idal-about-footer-title">CONTACT US</h4>

            <p>
                Phone:
                <a href="tel:+9611983306">
                    +961 1 983 306
                </a>
            </p>

            <p>
                Email:
                <a href="mailto:invest@idal.com.lb">
                    invest@idal.com.lb
                </a>
            </p>

            <p>
                Address: Lazariah Tower, Riad El Solh, Beirut – Lebanon
            </p>

        </div>

        <div class="idal-about-footer-column idal-about-links">

            <h4 class="idal-about-footer-title">QUICK LINKS</h4>

            <a href="<?php echo esc_url(home_url('/en/about-us/')); ?>">
                About Us
            </a>

            <a href="<?php echo esc_url(home_url('/en/investment/')); ?>">
                Invest in Lebanon
            </a>

            <a href="<?php echo esc_url(home_url('/en/export/')); ?>">
                Export from Lebanon
            </a>

            <a href="<?php echo esc_url(home_url('/en/contact-us/')); ?>">
                Contact
            </a>

        </div>

    </div>

    <div class="idal-about-footer-bottom">

        <div>
            Investment Development Authority of Lebanon
            <?php echo esc_html(date('Y')); ?> ©
        </div>

        <div class="idal-about-footer-policy">

            <a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>">
                Privacy Policy
            </a>

            <span>|</span>

            <a href="<?php echo esc_url(home_url('/terms-of-use/')); ?>">
                Terms of Use
            </a>

        </div>

    </div>

</footer>

<?php else : ?>

<footer class="footer-section"<?php echo $footer_style; ?>>

    <div class="container">

        <div class="row contact-section">

            <div class="col-12 col-lg-9 contact-us-column">

                <h5 class="footer-heading mb-4">
                    Contact Us
                </h5>

                <div class="idal-footer-contact-grid">

                    <div class="idal-footer-contact-item idal-footer-address">

                        <span class="idal-footer-label">
                            Address
                        </span>

                        <span class="idal-footer-value">
                            Lazarieh Tower, 4th Floor, Emir Bechir Street,
                            Riad El-Solh, Beirut, Lebanon, P.O. Box 113-7251
                        </span>

                    </div>

                    <div class="idal-footer-contact-item">

                        <span class="idal-footer-label">
                            Phone
                        </span>

                        <a
                            class="idal-footer-value"
                            href="tel:+9611983306"
                        >
                            +961 1 983306
                        </a>

                    </div>

                    <div class="idal-footer-contact-item">

                        <span class="idal-footer-label">
                            Fax
                        </span>

                        <span class="idal-footer-value">
                            +961 1 983302
                        </span>

                    </div>

                    <div class="idal-footer-contact-item">

                        <span class="idal-footer-label">
                            Email
                        </span>

                        <a
                            class="idal-footer-value"
                            href="mailto:invest@idal.com.lb"
                        >
                            invest@idal.com.lb
                        </a>

                    </div>

                </div>

            </div>

            <div
                class="
                    col-12
                    col-lg-3
                    d-flex
                    justify-content-lg-end
                    follow-us-wrapper
                "
            >

                <div>

                    <h5 class="footer-heading mb-3">
                        Follow Us
                    </h5>

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
            $copy_right_text_field =
                $current_lang === 'ar'
                ? 'copy_right_text_ar'
                : 'copy_right_text';

            $copy_right_designed_field =
                $current_lang === 'ar'
                ? 'copy_right_designed_by_ar'
                : 'copy_right_designed_by';

            $copy_right_text =
                function_exists('get_field')
                ? get_field($copy_right_text_field, 'option')
                : '';

            $copy_right_designed_by =
                function_exists('get_field')
                ? get_field($copy_right_designed_field, 'option')
                : '';

            $current_year = date('Y');

            $final_text =
                $copy_right_text
                ? str_replace(
                    '{year}',
                    $current_year,
                    $copy_right_text
                )
                : 'Investment Development Authority of Lebanon '
                    . $current_year
                    . ' ©';
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

                        echo ' <a
                            href="https://ids.com.lb/"
                            target="_blank"
                            rel="noopener noreferrer"
                            style="
                                color:#192D50;
                                font-weight:bold;
                            "
                        >
                            IDS
                        </a>';

                    } else {

                        echo 'Designed &amp; Developed By ';

                        echo '<a
                            href="https://ids.com.lb/"
                            target="_blank"
                            rel="noopener noreferrer"
                            style="
                                color:#192D50;
                                font-weight:bold;
                            "
                        >
                            IDS
                        </a>';
                    }
                    ?>

                </p>

            </div>

        </div>

    </div>

</footer>

<?php endif; ?>

<?php
do_action('omsar_after_footer');

if (is_front_page()) {
    echo '</div><!-- .omsar-site-content -->';
}

wp_footer();
?>

<script
    type="module"
    src="<?php
        echo esc_url(
            get_template_directory_uri()
            . '/assets/js/chatbot-widget.min.js?v=1.0.3'
        );
    ?>"
>
</script>

</body>
</html>