<?php
// Close inner-page site content wrapper before footer.
if (!is_front_page()) {
    echo '</div><!-- .omsar-site-content -->';
}

do_action('omsar_before_footer');

$current_lang = function_exists('pll_current_language')
    ? pll_current_language()
    : 'en';

// Force the new Arabic About Us page to behave as Arabic.
$is_arabic_idal_page = (
    $current_lang === 'ar'
    || is_page(26862)
    || is_page(26916)
    || is_page_template('page-home-arabic.php')
);

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
        $style_parts[] =
            '--footer-main-color: '
            . esc_attr($footer_main_color)
            . ';';
    }

    if ($footer_shadow_color) {
        $style_parts[] =
            '--footer-shadow-color: '
            . esc_attr($footer_shadow_color)
            . ';';
    }
}

$footer_style = '';

if (!empty($style_parts)) {
    $footer_style =
        ' style="'
        . esc_attr(implode(' ', $style_parts))
        . '"';
}
?>


<?php
/*
|--------------------------------------------------------------------------
| IDAL ABOUT US FOOTER
|--------------------------------------------------------------------------
| English About Us: 26733
| Arabic About Us:  26862
|--------------------------------------------------------------------------
*/
?>

<?php if (
    is_front_page()
    || is_page(array(26733, 26862, 26916))
    || is_page_template('page-home-arabic.php')
) : ?>


<style>

/* ==========================================================
   IDAL FOOTER - BASE
========================================================== */

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


/* ==========================================================
   MAIN COLUMNS
========================================================== */

.idal-about-footer-main {
    width: 100%;
    max-width: 1800px;
    margin: 0 auto;

    display: grid;

    grid-template-columns:
        1.15fr
        1fr
        0.9fr;

    gap: 90px;
    align-items: start;

    min-height: 345px;
}

.idal-about-footer-column {
    min-width: 0;
}


/* ==========================================================
   LOGO / BRAND
========================================================== */

.idal-about-footer-logo {
    margin: 0 0 24px;

    color: #ffffff !important;

    font-size: 38px;
    line-height: 1;
    font-weight: 800;
}


/* ==========================================================
   DESCRIPTION
========================================================== */

.idal-about-footer-description {
    margin: 0 0 31px;

    color: #ffffff !important;

    font-size: 18px;
    line-height: 1.7;
    font-weight: 400;
}


/* ==========================================================
   TITLES
========================================================== */

.idal-about-footer-title {
    margin: 0 0 25px;

    color: #ffffff !important;

    font-size: 14px;
    line-height: 1.3;
    font-weight: 800;

    letter-spacing: 1.6px;
    text-transform: uppercase;
}


/* ==========================================================
   SOCIALS
========================================================== */

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


/* ==========================================================
   CONTACT
========================================================== */

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


/* ==========================================================
   QUICK LINKS
========================================================== */

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


/* ==========================================================
   BOTTOM
========================================================== */

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


/* ==========================================================
   ARABIC RTL
========================================================== */

.idal-about-footer.idal-about-footer-ar {
    direction: rtl;
    text-align: right;
}

.idal-about-footer.idal-about-footer-ar
.idal-about-footer-main {
    direction: rtl;
}

.idal-about-footer.idal-about-footer-ar
.idal-about-footer-column {
    text-align: right;
}

.idal-about-footer.idal-about-footer-ar
.idal-about-footer-title {
    text-align: right;
    letter-spacing: 0;
    text-transform: none;
}

.idal-about-footer.idal-about-footer-ar
.idal-about-footer-description {
    text-align: right;
}

.idal-about-footer.idal-about-footer-ar
.idal-about-contact {
    text-align: right;
}

.idal-about-footer.idal-about-footer-ar
.idal-about-links {
    align-items: flex-start;
    text-align: right;
}

.idal-about-footer.idal-about-footer-ar
.idal-about-links > a {
    width: 100%;
    text-align: right;
}

.idal-about-footer.idal-about-footer-ar
.idal-about-socials {
    direction: rtl;
    justify-content: flex-start;
}

.idal-about-footer.idal-about-footer-ar
.idal-about-footer-bottom {
    direction: rtl;
    text-align: right;
}


/* ==========================================================
   TABLET
========================================================== */

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


/* ==========================================================
   MOBILE
========================================================== */

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

    .idal-about-footer.idal-about-footer-ar
    .idal-about-footer-bottom {
        align-items: flex-end;
    }
}

</style>


<footer
    class="
        idal-about-footer
        <?php
        echo $is_arabic_idal_page
            ? 'idal-about-footer-ar'
            : 'idal-about-footer-en';
        ?>
    "
>


    <div class="idal-about-footer-main">


        <!-- ==================================================
             BRAND / SOCIAL
        =================================================== -->

        <div class="idal-about-footer-column">


            <h2 class="idal-about-footer-logo">

                <?php if ($is_arabic_idal_page) : ?>

                    إيدال

                <?php else : ?>

                    IDAL

                <?php endif; ?>

            </h2>


            <p class="idal-about-footer-description">

                <?php if ($is_arabic_idal_page) : ?>

                    نربط المستثمرين والمصدّرين بالفرص
                    الاستراتيجية في لبنان وبالأسواق العالمية.

                <?php else : ?>

                    Connecting investors and exporters to<br>
                    Lebanon's strategic opportunities and<br>
                    gateway to global markets.

                <?php endif; ?>

            </p>


            <h4 class="idal-about-footer-title">

                <?php if ($is_arabic_idal_page) : ?>

                    تابعونا

                <?php else : ?>

                    FOLLOW US

                <?php endif; ?>

            </h4>


            <div class="idal-about-socials">


                <!-- LinkedIn -->

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


                <!-- Instagram -->

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


                <!-- X -->

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


                <!-- Facebook -->

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



        <!-- ==================================================
             CONTACT
        =================================================== -->

        <div
            class="
                idal-about-footer-column
                idal-about-contact
            "
        >


            <h4 class="idal-about-footer-title">

                <?php if ($is_arabic_idal_page) : ?>

                    اتصل بنا

                <?php else : ?>

                    CONTACT US

                <?php endif; ?>

            </h4>


            <p>

                <?php if ($is_arabic_idal_page) : ?>

                    الهاتف:

                <?php else : ?>

                    Phone:

                <?php endif; ?>

                <a href="tel:+9611983306">

                    +961 1 983 306

                </a>

            </p>


            <p>

                <?php if ($is_arabic_idal_page) : ?>

                    البريد الإلكتروني:

                <?php else : ?>

                    Email:

                <?php endif; ?>

                <a href="mailto:invest@idal.com.lb">

                    invest@idal.com.lb

                </a>

            </p>


            <p>

                <?php if ($is_arabic_idal_page) : ?>

                    العنوان:
                    برج اللعازارية،
                    رياض الصلح،
                    بيروت – لبنان

                <?php else : ?>

                    Address:
                    Lazariah Tower,
                    Riad El Solh,
                    Beirut – Lebanon

                <?php endif; ?>

            </p>


        </div>



        <!-- ==================================================
             QUICK LINKS
        =================================================== -->

        <div
            class="
                idal-about-footer-column
                idal-about-links
            "
        >


            <h4 class="idal-about-footer-title">

                <?php if ($is_arabic_idal_page) : ?>

                    روابط سريعة

                <?php else : ?>

                    QUICK LINKS

                <?php endif; ?>

            </h4>



            <!-- ABOUT US -->

            <a
                href="<?php
                echo esc_url(
                    $is_arabic_idal_page
                        ? home_url('/about-us-arabic/')
                        : home_url('/en/about-us/')
                );
                ?>"
            >

                <?php if ($is_arabic_idal_page) : ?>

                    من نحن

                <?php else : ?>

                    About Us

                <?php endif; ?>

            </a>



            <!-- INVESTMENT -->

            <a
                href="<?php
                echo esc_url(
                    $is_arabic_idal_page
                        ? home_url('/investment/')
                        : home_url('/en/investment/')
                );
                ?>"
            >

                <?php if ($is_arabic_idal_page) : ?>

                    الاستثمار في لبنان

                <?php else : ?>

                    Invest in Lebanon

                <?php endif; ?>

            </a>



            <!-- EXPORT -->

            <a
                href="<?php
                echo esc_url(
                    $is_arabic_idal_page
                        ? home_url('/export/')
                        : home_url('/en/export/')
                );
                ?>"
            >

                <?php if ($is_arabic_idal_page) : ?>

                    التصدير من لبنان

                <?php else : ?>

                    Export from Lebanon

                <?php endif; ?>

            </a>



            <!-- CONTACT -->

            <a
                href="<?php
                echo esc_url(
                    $is_arabic_idal_page
                        ? home_url('/contact-us/')
                        : home_url('/en/contact-us/')
                );
                ?>"
            >

                <?php if ($is_arabic_idal_page) : ?>

                    اتصل بنا

                <?php else : ?>

                    Contact

                <?php endif; ?>

            </a>


        </div>


    </div>



    <!-- ==================================================
         FOOTER BOTTOM
    =================================================== -->

    <div class="idal-about-footer-bottom">


        <div>

            <?php if ($is_arabic_idal_page) : ?>

                المؤسسة العامة لتشجيع الاستثمارات في لبنان
                <?php echo esc_html(date('Y')); ?>
                ©

            <?php else : ?>

                Investment Development Authority of Lebanon
                <?php echo esc_html(date('Y')); ?>
                ©

            <?php endif; ?>

        </div>



        <div class="idal-about-footer-policy">


            <a
                href="<?php
                echo esc_url(
                    home_url('/privacy-policy/')
                );
                ?>"
            >

                <?php if ($is_arabic_idal_page) : ?>

                    سياسة الخصوصية

                <?php else : ?>

                    Privacy Policy

                <?php endif; ?>

            </a>


            <span>|</span>


            <a
                href="<?php
                echo esc_url(
                    home_url('/terms-of-use/')
                );
                ?>"
            >

                <?php if ($is_arabic_idal_page) : ?>

                    شروط الاستخدام

                <?php else : ?>

                    Terms of Use

                <?php endif; ?>

            </a>


        </div>


    </div>


</footer>


<?php else : ?>


<?php
/*
|--------------------------------------------------------------------------
| ORIGINAL OMSAR FOOTER
|--------------------------------------------------------------------------
| This stays unchanged for every other page.
|--------------------------------------------------------------------------
*/
?>


<footer
    class="footer-section"
    <?php echo $footer_style; ?>
>


    <div class="container">


        <div class="row contact-section">


            <div class="col-12 col-lg-9 contact-us-column">


                <h5 class="footer-heading mb-4">
                    Contact Us
                </h5>


                <div class="idal-footer-contact-grid">


                    <!-- ADDRESS -->

                    <div
                        class="
                            idal-footer-contact-item
                            idal-footer-address
                        "
                    >

                        <span class="idal-footer-label">
                            Address
                        </span>

                        <span class="idal-footer-value">

                            Lazarieh Tower,
                            4th Floor,
                            Emir Bechir Street,
                            Riad El-Solh,
                            Beirut,
                            Lebanon,
                            P.O. Box 113-7251

                        </span>

                    </div>



                    <!-- PHONE -->

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



                    <!-- FAX -->

                    <div class="idal-footer-contact-item">

                        <span class="idal-footer-label">
                            Fax
                        </span>

                        <span class="idal-footer-value">

                            +961 1 983302

                        </span>

                    </div>



                    <!-- EMAIL -->

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



            <!-- FOLLOW US -->

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
                            class="
                                social-icon-link
                                idal-social-link
                            "
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="IDAL on Facebook"
                            title="Facebook"
                        >

                            <span aria-hidden="true">
                                f
                            </span>

                        </a>



                        <a
                            href="https://www.instagram.com/invest_lebanon/"
                            class="
                                social-icon-link
                                idal-social-link
                            "
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="IDAL on Instagram"
                            title="Instagram"
                        >

                            <span aria-hidden="true">
                                ◎
                            </span>

                        </a>



                        <a
                            href="https://www.linkedin.com/company/investment-development-authority-of-lebanon"
                            class="
                                social-icon-link
                                idal-social-link
                            "
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="IDAL on LinkedIn"
                            title="LinkedIn"
                        >

                            <span aria-hidden="true">
                                in
                            </span>

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
                    ? get_field(
                        $copy_right_text_field,
                        'option'
                    )
                    : '';


            $copy_right_designed_by =
                function_exists('get_field')
                    ? get_field(
                        $copy_right_designed_field,
                        'option'
                    )
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


            <div
                class="
                    col-md-6
                    text-center
                    text-md-start
                    mb-2
                    mb-md-0
                "
            >

                <p
                    class="
                        mb-0
                        small
                        text-muted
                        copyright-text
                    "
                >

                    <?php
                    echo esc_html($final_text);
                    ?>

                </p>

            </div>



            <div
                class="
                    col-md-6
                    text-center
                    text-md-end
                "
            >

                <p
                    class="
                        mb-0
                        small
                        text-muted
                        copyright-text
                    "
                >


                    <?php

                    if ($copy_right_designed_by) {


                        echo esc_html(
                            $copy_right_designed_by
                        );


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