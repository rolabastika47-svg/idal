<?php
/*
Template Name: Contact Us Page
*/
get_header();

// Get map iframe from ACF options
$map_location = get_field('map_location', 'option');

// Get current language for ACF field selection
$current_lang = function_exists('pll_current_language') ? pll_current_language() : 'en';

// Add content after page banner (inside the background div - opened by banner function)

    ?>
    <!-- Contact Section -->
    <section class="contact-page-section">
        <div class="container">
            <!-- Form and Contact Info Section -->
            <div class="row form-info-row">
                <!-- Form Column -->
                <div class="col-lg-8 mb-4 mb-lg-0">
                    <div class="contact-form-wrapper">
                        <h2 class="form-title">
                            <?php echo function_exists('pll__') ? pll__('Send us a Message') : __('Send us a Message', 'omsar'); ?>
                        </h2>

                        <form id="contactForm" class="contact-form">

                            <div class="row mb-3">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <label for="fullName" class="form-label">
                                        <?php echo function_exists('pll__') ? pll__('Full Name*') : __('Full Name*', 'omsar'); ?>
                                    </label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="fullName"
                                        name="fullName"
                                        placeholder="<?php echo function_exists('pll__') ? esc_attr(pll__('Enter your full name')) : esc_attr__('Enter your full name', 'omsar'); ?>"
                                        required
                                    >
                                </div>

                                <div class="col-md-4 mb-3 mb-md-0">
                                    <label for="email" class="form-label">
                                        <?php echo function_exists('pll__') ? pll__('Email Address*') : __('Email Address*', 'omsar'); ?>
                                    </label>
                                    <input
                                        type="email"
                                        class="form-control"
                                        id="email"
                                        name="email"
                                        placeholder="<?php echo function_exists('pll__') ? esc_attr(pll__('Enter your email address')) : esc_attr__('Enter your email address', 'omsar'); ?>"
                                        required
                                    >
                                </div>

                                <div class="col-md-4">
                                    <label for="phone" class="form-label">
                                        <?php echo function_exists('pll__') ? pll__('Phone') : __('Phone', 'omsar'); ?>
                                    </label>
                                    <input
                                        type="tel"
                                        class="form-control"
                                        id="phone"
                                        name="phone"
                                        placeholder="<?php echo function_exists('pll__') ? esc_attr(pll__('Enter your phone number')) : esc_attr__('Enter your phone number', 'omsar'); ?>"
                                    >
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label">
                                    <?php echo function_exists('pll__') ? pll__('Message *') : __('Message *', 'omsar'); ?>
                                </label>
                                <textarea
                                    class="form-control contact-msg"
                                    id="message"
                                    name="message"
                                    rows="6"
                                    placeholder="<?php echo function_exists('pll__') ? esc_attr(pll__('Enter your message')) : esc_attr__('Enter your message', 'omsar'); ?>"
                                    required
                                ></textarea>

                                <div class="character-count">
                                    <span id="charCount">0</span>
                                    <?php echo function_exists('pll__') ? pll__('characters') : __('characters', 'omsar'); ?>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-submit">
                                <?php echo function_exists('pll__') ? pll__('Submit') : __('Submit', 'omsar'); ?>
                            </button>

                            <!-- Success/Error Message Container -->
                            <div id="contactFormMessage" class="contact-form-message" style="display: none;"></div>

                        </form>
                    </div>
                </div>


                <!-- Contact Info Cards Column -->
                <div class="col-lg-4">
                    <!-- Contact Information Card -->
                    <?php 
                    // Decide which fields to use based on language
                    $contact_details_field ='contact_details';
                    if( have_rows($contact_details_field, 'option') ): ?>
                        <div class="contact-info-card-side">
                            <h5 class="contact-info-title"><?php echo function_exists('pll__') ? pll__('Contact Information') : __('Contact Information', 'omsar'); ?></h5>
                            <?php while( have_rows($contact_details_field, 'option') ) : the_row(); 
                                // $icon  = get_sub_field('social_icon');
                                $link  = get_sub_field('link');

                                if ($current_lang === 'ar') {
                                    $title = get_sub_field('title_ar');
                                    $value = get_sub_field('value_ar');
                                } else {
                                    $title = get_sub_field('title');
                                    $value = get_sub_field('value');
                                }
                                // Handle icon URL - can be array (file array) or string (URL)
                                $icon_id  = get_sub_field('social_icon');
                                $icon_url = $icon_id ? wp_get_attachment_image_url( $icon_id, 'full' ) : '';

                            ?>
                                <div class="contact-info-item">
                                    <?php if ($icon_url): ?>
                                        <img  src="<?php echo esc_url($icon_url); ?>" alt="<?php echo esc_attr($title); ?>" class="contact-icon-side">
                                    <?php endif; ?>
                                    <span class="contact-info-text">
                                        <?php if($link): ?>
                                            <a class="no-style-link" href="<?php echo esc_url($link); ?>"><?php echo esc_html($value); ?></a>
                                        <?php else: ?>
                                            <?php echo esc_html($value); ?>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Follow Us Card -->
                    <?php 
                    $social_text = get_field('social_text', 'option'); 
                    if( have_rows('social_media', 'option') ): 
                    ?>
                        <div class="follow-us-card-side">
                            <h5 class="follow-us-title"><?php echo pll_e('Follow Us') ?></h5>
                            <div class="social-icons-side">
                                <?php while( have_rows('social_media', 'option') ) : the_row(); 
                                    // $icon = get_sub_field('icon');
                                    $link = get_sub_field('social_link'); 

                                    $icon_id  = get_sub_field('icon');
                                    $icon_url = $icon_id ? wp_get_attachment_image_url( $icon_id, 'full' ) : '';

                                ?>
                                    <?php if ( $icon_url && $link ) : ?>
                                        <a href="<?php echo esc_url($link); ?>" class="social-icon-link-side" target="_blank" rel="noopener noreferrer">
                                            <img src="<?php echo esc_url($icon_url); ?>" alt="" class="social-icon-img-side">
                                        </a>
                                    <?php endif; ?>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Map Section -->
            <div class="row map-section-row">
                <div class="col-12">
                    <div class="map-wrapper">
                        <?php if ($map_location): ?>
                            <?php echo $map_location; ?>
                        <?php else: ?>
                            <!-- Fallback map if map_location is not set -->
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d26496.13582263592!2d35.48368274937676!3d33.8892165859779!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x151f17215880a78f%3A0x729182bae99836b4!2sBeirut!5e0!3m2!1sen!2slb!4v1765451166152!5m2!1sen!2slb" class="contact-map" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php

    ?>

    <?php get_footer(); ?>

