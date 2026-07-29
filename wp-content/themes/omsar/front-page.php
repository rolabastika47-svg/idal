<?php
get_header();

?>

<div class="background front-page"<?php echo omsar_get_background_pattern_style(); ?>>
    <!-- IDAL Raouche Hero Section -->
    <section class="hero-section idal-raouche-hero">
        <img
            src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/raouche-hero-full.png'); ?>"
            alt="Raouché Rocks and Beirut coastline"
            class="idal-raouche-hero-image"
        >

        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 col-xl-5">
                    <div class="idal-hero-content">
                        <h1 class="hero-title">
                            <span class="hero-title-blue">Invest in Lebanon.</span>
                            <span class="hero-title-green">Export to the World.</span>
                        </h1>

                        <p class="hero-description">
                            Connecting investors and exporters to Lebanon’s strategic opportunities,
                            world-class talent, and gateway to global markets.
                        </p>

                        <div class="idal-hero-actions">
                            <a href="<?php echo esc_url(home_url('/investment/')); ?>"
                               class="idal-hero-button idal-hero-button-primary">
                                <span>Invest in Lebanon</span>
                                <span class="idal-button-arrow" aria-hidden="true">→</span>
                            </a>

                            <a href="<?php echo esc_url(home_url('/export/')); ?>"
                               class="idal-hero-button idal-hero-button-secondary">
                                <span>Export from Lebanon</span>
                                <span class="idal-button-arrow" aria-hidden="true">→</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    


    <!-- Pillars Section -->
    <section class="pillars-section">
        <div class="container section-top-space pillars-container">
            <!-- Section Header -->
            <div class="row title-section">
                <div class="col-12 text-center">
                        <?php $section_title = get_field('pillar_section_title'); ?>
                <h2 class="section-title title-bottom-space"><?php echo esc_html($section_title); ?></h2>
                </div>
            </div>

            <!-- Pillars Grid -->
            <div class="row g-4 justify-content-center investment-export-row">
                <?php if ( have_rows('pillars_rep') ): ?>
                    <?php
                    $pillar_count = 0;

                    while ( have_rows('pillars_rep') ) : the_row();

                        $pillar_count++;

                        if ( $pillar_count > 2 ) {
                            break;
                        }

                        $title = get_sub_field('pillar_title');
                        $desc  = get_sub_field('pillar_desciption');
                        $link  = get_sub_field('pillar_link');
                        $bg    = get_sub_field('pillar_image'); // image URL
                    ?>
                        <div class="col-md-6">
                            <div class="pillar-card pillar-card-<?php echo esc_attr($pillar_count); ?>"
                                <?php if($bg): ?>
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

                                    <?php if($link): ?>
                                        <a href="<?php echo esc_url($link); ?>" class="pillar-link">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/arrow.svg"alt="More" class="pillar-arrow">
                                        </a>
                                    <?php endif; ?>
                                </div>

                                    <?php if($desc): ?>
                                        <p class="pillar-desc">
                                            <?php echo nl2br(esc_html($desc)); ?>
                                        </p>
                                    <?php endif; ?>

                                    <?php if($link): ?>
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
            </div>
        </div>
    </section>

    <!-- Vision & Mission Section -->
    <section class="vision-mission-section vision-mission-reference">
        <div class="container">
            <div class="row g-5 justify-content-center align-items-start">
                <div class="col-md-5">
                    <article class="vision-mission-copy vision-copy">
                        <h3>Our Vision</h3>
                        <span class="vision-mission-line" aria-hidden="true"></span>
                        <p>
                            A forward Lebanon — a competitive, resilient economy where investors
                            and Lebanese talent thrive together.
                        </p>
                    </article>
                </div>

                <div class="col-md-5">
                    <article class="vision-mission-copy mission-copy">
                        <h3>Our Mission</h3>
                        <span class="vision-mission-line" aria-hidden="true"></span>
                        <p>
                            To attract quality investment, support exporters, and deliver reliable,
                            efficient services that drive long-term growth.
                        </p>
                    </article>
                </div>
            </div>
        </div>
    </section>



    <!-- testimonials -->
    <?php
    // Query testimonials post type
    $testimonials_args = array(
        'post_type' => 'testimonials',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC'
    );

    $testimonials_query = new WP_Query($testimonials_args);

    // Only show section if we have testimonials
    if ($testimonials_query->have_posts()) :
    ?>
    <section class="testimonials-section">
        <div class="container section-top-space">
            <div class="row title-section">
                <div class="col-12 text-center">
                    <?php $section_title = get_field('section5_title'); ?>
                    <h2 class="section-title title-bottom-space"><?php echo esc_html($section_title); ?></h2>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="owl-carousel owl-theme testimonials-carousel">
                        <?php
                        while ($testimonials_query->have_posts()) :
                            $testimonials_query->the_post();
                            $author_name = get_the_title();
                            $testimonial_text = get_field('description');
                            $author_role = get_field('position');
                        ?>
                            <div class="item">
                                <div class="testimonial-card">
                                    <div class="quote-icon"></div>
                                    <?php if ($testimonial_text): ?>
                                        <p class="testimonial-text">
                                            <?php echo wp_kses_post($testimonial_text); ?>
                                        </p>
                                    <?php endif; ?>
                                    <div class="testimonial-author">
                                        <?php if ($author_name): ?>
                                            <h5 class="author-name"><?php echo esc_html($author_name); ?></h5>
                                        <?php endif; ?>
                                        <?php if ($author_role): ?>
                                            <span class="author-role"><?php echo esc_html($author_role); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>




<?php get_footer(); ?>