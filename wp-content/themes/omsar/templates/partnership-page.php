<?php
/*
Template Name: Partnership Page
*/
get_header();
?>

<section class="contact-page-section">
        <div class="container">
            <!-- Form and Contact Info Section -->
            <div class="row form-info-row">
                <!-- Form Column -->
                <div class="col-lg-12 col-xl-12 mb-4 mb-lg-0">
                    <div class="contact-form-wrapper">
                        <h2 class="form-title"><?php pll_e('Inquire About Our Partnership Program'); ?></h2>
                        <form id="partnershipForm" class="contact-form">
                            <!-- Row 1: Full Name, Email Address, Phone Number -->
                            <div class="row mb-3">
                                <div class="col-12 col-md-4 mb-3 mb-md-0">
                                    <label for="fullName" class="form-label"><?php pll_e('Full Name'); ?>*</label>
                                    <input type="text" class="form-control" id="fullName" name="fullName" placeholder="<?php echo esc_attr(pll__('Enter your full name')); ?>" required>
                                </div>
                                <div class="col-12 col-md-4 mb-3 mb-md-0">
                                    <label for="email" class="form-label"><?php pll_e('Email Address'); ?>*</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="<?php echo esc_attr(pll__('Enter your email address')); ?>" required>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="phone" class="form-label"><?php pll_e('Phone Number'); ?>*</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="<?php echo esc_attr(pll__('Enter your phone number')); ?>" required>
                                </div>
                            </div>
                            <!-- Row 2: Website URL and Address -->
                            <div class="row mb-3">
                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                    <label for="website" class="form-label"><?php pll_e('Website URL'); ?>*</label>
                                    <input type="url" class="form-control" id="website" name="website" placeholder="https://example.com" required>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="address" class="form-label"><?php pll_e('Address'); ?>*</label>
                                    <input type="text" class="form-control" id="address" name="address" placeholder="<?php echo esc_attr(pll__('Enter your address')); ?>" required>
                                </div>
                            </div>
                            <!-- Row 3: Notes -->
                            <div class="mb-3">
                                <label for="notes" class="form-label"><?php pll_e('Notes'); ?>*</label>
                                <textarea class="form-control contact-msg" id="notes" name="notes" rows="6" placeholder="<?php echo esc_attr(pll__('Tell us about your partnership interest...')); ?>" required></textarea>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-submit"><?php pll_e('Submit'); ?></button>
                            </div>
                            <div id="partnershipFormMessage" class="contact-form-message" style="display: none;"></div>
                        </form>
                    </div>
                </div>

                
            </div>
        </div>
    </section>

<?php get_footer(); ?>
