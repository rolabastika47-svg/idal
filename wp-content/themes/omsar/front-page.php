<?php
get_header();

/*
|--------------------------------------------------------------------------
| IDAL HOME PAGE - ENGLISH / ARABIC
|--------------------------------------------------------------------------
*/

$current_lang = function_exists('pll_current_language')
    ? pll_current_language()
    : 'en';

$is_arabic = ($current_lang === 'ar');


/*
|--------------------------------------------------------------------------
| Helper: get translated page URL using Polylang
|--------------------------------------------------------------------------
*/

$get_translated_page_url = function ($slug) use ($current_lang) {

    $page = get_page_by_path($slug);

    if ($page && function_exists('pll_get_post')) {

        $translated_page_id = pll_get_post($page->ID, $current_lang);

        if ($translated_page_id) {
            return get_permalink($translated_page_id);
        }
    }

    return home_url('/' . trim($slug, '/') . '/');
};


/*
|--------------------------------------------------------------------------
| Investment + Export links
|--------------------------------------------------------------------------
*/

$investment_url = $get_translated_page_url('investment');
$export_url     = $get_translated_page_url('export');

?>

<style>

/* =========================================================
   ARABIC HOME RTL
   ========================================================= */

.idal-home-arabic {
    direction: rtl;
}

.idal-home-arabic .idal-raouche-hero-image {
    transform: scaleX(-1);
}

.idal-home-arabic .idal-hero-content {
    direction: rtl;
    text-align: right;
}

.idal-home-arabic .hero-title,
.idal-home-arabic .hero-description {
    text-align: right;
}

.idal-home-arabic .idal-hero-actions {
    direction: rtl;
}

.idal-home-arabic .pillar-card-content {
    direction: rtl;
    text-align: right;
}

.idal-home-arabic .pillar-title,
.idal-home-arabic .pillar-desc,
.idal-home-arabic .pillar-eyebrow {
    text-align: right;
}

.idal-home-arabic .vision-mission-copy {
    direction: rtl;
    text-align: right;
}

.idal-home-arabic .testimonial-card {
    direction: rtl;
    text-align: right;
}

.idal-home-arabic .testimonial-author {
    text-align: right;
}

</style>


<div
    class="background front-page <?php echo $is_arabic ? 'idal-home-arabic' : 'idal-home-english'; ?>"
    dir="<?php echo $is_arabic ? 'rtl' : 'ltr'; ?>"
    <?php echo omsar_get_background_pattern_style(); ?>
>


    <!-- =====================================================
         IDAL RAOUCHE HERO SECTION
         ===================================================== -->

    <section class="hero-section idal-raouche-hero">

        <img
            src="<?php echo esc_url(
                get_template_directory_uri() .
                '/assets/images/raouche-hero-full.png'
            ); ?>"
            alt="<?php echo $is_arabic
                ? 'صخرة الروشة وساحل بيروت'
                : 'Raouché Rocks and Beirut coastline'; ?>"
            class="idal-raouche-hero-image"
        >


        <div class="container">

            <div class="row align-items-center">

                <div class="col-lg-6 col-xl-5">

                    <div class="idal-hero-content">


                        <h1 class="hero-title">

                            <?php if ($is_arabic) : ?>

                                <span class="hero-title-blue">
                                    استثمر في لبنان.
                                </span>

                                <span class="hero-title-green">
                                    صدّر إلى العالم.
                                </span>

                            <?php else : ?>

                                <span class="hero-title-blue">
                                    Invest in Lebanon.
                                </span>

                                <span class="hero-title-green">
                                    Export to the World.
                                </span>

                            <?php endif; ?>

                        </h1>



                        <p class="hero-description">

                            <?php if ($is_arabic) : ?>

                                نربط المستثمرين والمصدّرين بالفرص الاستراتيجية في لبنان،
                                والكفاءات المتميزة، وبوابة لبنان إلى الأسواق العالمية.

                            <?php else : ?>

                                Connecting investors and exporters to Lebanon’s strategic opportunities,
                                world-class talent, and gateway to global markets.

                            <?php endif; ?>

                        </p>



                        <div class="idal-hero-actions">


                            <a
                                href="<?php echo esc_url($investment_url); ?>"
                                class="idal-hero-button idal-hero-button-primary"
                            >

                                <span>
                                    <?php echo $is_arabic
                                        ? 'استثمر في لبنان'
                                        : 'Invest in Lebanon'; ?>
                                </span>

                                <span
                                    class="idal-button-arrow"
                                    aria-hidden="true"
                                >
                                    <?php echo $is_arabic ? '←' : '→'; ?>
                                </span>

                            </a>



                            <a
                                href="<?php echo esc_url($export_url); ?>"
                                class="idal-hero-button idal-hero-button-secondary"
                            >

                                <span>
                                    <?php echo $is_arabic
                                        ? 'صدّر من لبنان'
                                        : 'Export from Lebanon'; ?>
                                </span>

                                <span
                                    class="idal-button-arrow"
                                    aria-hidden="true"
                                >
                                    <?php echo $is_arabic ? '←' : '→'; ?>
                                </span>

                            </a>


                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>



    <!-- =====================================================
         PILLARS SECTION
         ===================================================== -->

    <section class="pillars-section">

        <div class="container section-top-space pillars-container">

            <div class="row title-section">
                <div class="col-12 text-center">

                    <h2 class="section-title title-bottom-space">
                        <?php if ($is_arabic) : ?>
                            الاستثمار والتصدير
                        <?php else : ?>
                            <?php
                            $section_title = get_field('pillar_section_title');
                            echo esc_html($section_title);
                            ?>
                        <?php endif; ?>
                    </h2>

                </div>
            </div>

            <div class="row g-4 justify-content-center investment-export-row">

                <?php if ($is_arabic) : ?>

                    <?php
                    $english_home_id = 6;
                    $english_pillars = get_field('pillars_rep', $english_home_id);

                    $arabic_cards = array(
                        array(
                            'eyebrow' => 'للمستثمرين',
                            'title'   => 'استثمر في لبنان',
                            'desc'    => 'اكتشف الحوافز والتراخيص والدعم المتكامل لإطلاق أعمالك وتنميتها في لبنان.',
                            'button'  => 'استكشف الاستثمار',
                            'link'    => $investment_url,
                        ),
                        array(
                            'eyebrow' => 'للمصدّرين',
                            'title'   => 'صدّر من لبنان',
                            'desc'    => 'استفد من برامج الدعم المالي واللوجستي التي تساعد المنتجين اللبنانيين على الوصول إلى الأسواق العالمية.',
                            'button'  => 'استكشف التصدير',
                            'link'    => $export_url,
                        ),
                    );

                    for ($i = 0; $i < 2; $i++) :

                        $card = $arabic_cards[$i];
                        $bg = '';

                        if (!empty($english_pillars[$i]['pillar_image'])) {
                            $bg = $english_pillars[$i]['pillar_image'];
                        }

                        if (is_array($bg) && !empty($bg['url'])) {
                            $bg = $bg['url'];
                        }
                    ?>

                        <div class="col-md-6">

                            <div
                                class="pillar-card pillar-card-<?php echo esc_attr($i + 1); ?>"
                                <?php if ($bg) : ?>
                                    style="background-image: url('<?php echo esc_url($bg); ?>');"
                                <?php endif; ?>
                            >

                                <div class="pillar-card-content">

                                    <span class="pillar-eyebrow">
                                        <?php echo esc_html($card['eyebrow']); ?>
                                    </span>

                                    <div class="d-flex justify-content-between align-items-start mb-3">

                                        <h3 class="pillar-title">
                                            <?php echo esc_html($card['title']); ?>
                                        </h3>

                                        <a
                                            href="<?php echo esc_url($card['link']); ?>"
                                            class="pillar-link"
                                            aria-label="<?php echo esc_attr($card['button']); ?>"
                                        >
                                            <span aria-hidden="true">↖</span>
                                        </a>

                                    </div>

                                    <p class="pillar-desc">
                                        <?php echo esc_html($card['desc']); ?>
                                    </p>

                                    <a
                                        href="<?php echo esc_url($card['link']); ?>"
                                        class="pillar-button"
                                    >
                                        <?php echo esc_html($card['button']); ?>
                                        <span aria-hidden="true">←</span>
                                    </a>

                                </div>

                            </div>

                        </div>

                    <?php endfor; ?>

                <?php else : ?>

                    <?php if (have_rows('pillars_rep')) : ?>

                        <?php
                        $pillar_count = 0;

                        while (have_rows('pillars_rep')) :
                            the_row();

                            $pillar_count++;

                            if ($pillar_count > 2) {
                                break;
                            }

                            $title = get_sub_field('pillar_title');
                            $desc  = get_sub_field('pillar_desciption');
                            $link  = get_sub_field('pillar_link');
                            $bg    = get_sub_field('pillar_image');
                        ?>

                            <div class="col-md-6">

                                <div
                                    class="pillar-card pillar-card-<?php echo esc_attr($pillar_count); ?>"
                                    <?php if ($bg) : ?>
                                        style="background-image: url('<?php echo esc_url($bg); ?>');"
                                    <?php endif; ?>
                                >

                                    <div class="pillar-card-content">

                                        <span class="pillar-eyebrow">
                                            <?php echo $pillar_count === 1 ? 'For Investors' : 'For Exporters'; ?>
                                        </span>

                                        <div class="d-flex justify-content-between align-items-start mb-3">

                                            <h3 class="pillar-title">
                                                <?php echo nl2br(esc_html($title)); ?>
                                            </h3>

                                            <?php if ($link) : ?>
                                                <a href="<?php echo esc_url($link); ?>" class="pillar-link">
                                                    <img
                                                        src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/arrow.svg'); ?>"
                                                        alt="More"
                                                        class="pillar-arrow"
                                                    >
                                                </a>
                                            <?php endif; ?>

                                        </div>

                                        <?php if ($desc) : ?>
                                            <p class="pillar-desc">
                                                <?php echo nl2br(esc_html($desc)); ?>
                                            </p>
                                        <?php endif; ?>

                                        <?php if ($link) : ?>
                                            <a href="<?php echo esc_url($link); ?>" class="pillar-button">
                                                <?php echo $pillar_count === 1 ? 'Explore Investment' : 'Explore Export'; ?>
                                                <span aria-hidden="true">→</span>
                                            </a>
                                        <?php endif; ?>

                                    </div>

                                </div>

                            </div>

                        <?php endwhile; ?>

                    <?php endif; ?>

                <?php endif; ?>

            </div>

        </div>

    </section>



    <!-- =====================================================
         VISION & MISSION SECTION
         ===================================================== -->

    <section class="vision-mission-section vision-mission-reference">

        <div class="container">

            <div class="row g-5 justify-content-center align-items-start">


                <!-- Vision -->

                <div class="col-md-5">

                    <article class="vision-mission-copy vision-copy">


                        <h3>

                            <?php echo $is_arabic
                                ? 'رؤيتنا'
                                : 'Our Vision'; ?>

                        </h3>


                        <span
                            class="vision-mission-line"
                            aria-hidden="true"
                        ></span>


                        <p>

                            <?php if ($is_arabic) : ?>

                                لبنان متقدّم باقتصاد تنافسي ومرن،
                                يزدهر فيه المستثمرون والكفاءات اللبنانية معًا.

                            <?php else : ?>

                                A forward Lebanon — a competitive, resilient economy where investors
                                and Lebanese talent thrive together.

                            <?php endif; ?>

                        </p>


                    </article>

                </div>



                <!-- Mission -->

                <div class="col-md-5">

                    <article class="vision-mission-copy mission-copy">


                        <h3>

                            <?php echo $is_arabic
                                ? 'مهمتنا'
                                : 'Our Mission'; ?>

                        </h3>


                        <span
                            class="vision-mission-line"
                            aria-hidden="true"
                        ></span>


                        <p>

                            <?php if ($is_arabic) : ?>

                                جذب الاستثمارات النوعية، ودعم المصدّرين،
                                وتقديم خدمات موثوقة وفعّالة تساهم في تحقيق
                                نمو مستدام على المدى الطويل.

                            <?php else : ?>

                                To attract quality investment, support exporters, and deliver reliable,
                                efficient services that drive long-term growth.

                            <?php endif; ?>

                        </p>


                    </article>

                </div>


            </div>

        </div>

    </section>



    <!-- =====================================================
         TESTIMONIALS SECTION
         ===================================================== -->

    <?php

    $testimonials_args = array(

        'post_type'      => 'testimonials',

        'posts_per_page' => -1,

        'post_status'    => 'publish',

        'orderby'        => 'date',

        'order'          => 'DESC',

    );


    /*
     * Tell Polylang which testimonial language
     * should be loaded.
     */

    if (function_exists('pll_current_language')) {

        $testimonials_args['lang'] =
            $current_lang;
    }


    $testimonials_query =
        new WP_Query($testimonials_args);


    if ($testimonials_query->have_posts()) :
    ?>


        <section class="testimonials-section">

            <div class="container section-top-space">


                <div class="row title-section">

                    <div class="col-12 text-center">


                        <?php
                        $section_title =
                            get_field('section5_title');
                        ?>


                        <h2 class="section-title title-bottom-space">

                            <?php

                            if ($is_arabic) {

                                if (!empty($section_title)) {
                                    echo esc_html(
                                        $section_title
                                    );
                                } else {
                                    echo 'قصص وشهادات';
                                }

                            } else {

                                echo esc_html(
                                    $section_title
                                );

                            }

                            ?>

                        </h2>


                    </div>

                </div>



                <div class="row">

                    <div class="col-12">


                        <div class="owl-carousel owl-theme testimonials-carousel">


                            <?php

                            while (
                                $testimonials_query->have_posts()
                            ) :

                                $testimonials_query->the_post();


                                $author_name =
                                    get_the_title();


                                $testimonial_text =
                                    get_field(
                                        'description'
                                    );


                                $author_role =
                                    get_field(
                                        'position'
                                    );

                            ?>


                                <div class="item">


                                    <div class="testimonial-card">


                                        <div class="quote-icon"></div>



                                        <?php if ($testimonial_text) : ?>

                                            <p class="testimonial-text">

                                                <?php
                                                echo wp_kses_post(
                                                    $testimonial_text
                                                );
                                                ?>

                                            </p>

                                        <?php endif; ?>



                                        <div class="testimonial-author">


                                            <?php if ($author_name) : ?>

                                                <h5 class="author-name">

                                                    <?php
                                                    echo esc_html(
                                                        $author_name
                                                    );
                                                    ?>

                                                </h5>

                                            <?php endif; ?>



                                            <?php if ($author_role) : ?>

                                                <span class="author-role">

                                                    <?php
                                                    echo esc_html(
                                                        $author_role
                                                    );
                                                    ?>

                                                </span>

                                            <?php endif; ?>


                                        </div>


                                    </div>

                                </div>


                            <?php

                            endwhile;

                            wp_reset_postdata();

                            ?>


                        </div>

                    </div>

                </div>


            </div>

        </section>


    <?php endif; ?>


</div>


<?php get_footer(); ?>