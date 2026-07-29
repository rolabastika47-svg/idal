<?php
/*
Template Name: Secured Survey Form
*/

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<section class="omsar-survey-section static-height">
    <div class="container" id="omsarSurveyWizard">
        <div class="omsar-survey-card">
            
            <!-- Verification step (shown first) -->
            <div id="omsarSurveyVerifyStep" class="omsar-survey-step">
                <h2 class="omsar-survey-title"><?php echo esc_html(function_exists('pll__') ? pll__('Verification Code') : __('Verification Code', 'omsar')); ?></h2>
                <p class="omsar-survey-desc"><?php echo esc_html(function_exists('pll__') ? pll__('Please enter the verification code to access the survey form.') : __('Please enter the verification code to access the survey form.', 'omsar')); ?></p>
                <form id="omsarSurveyVerifyForm" class="omsar-survey-verify-form">
                    <?php wp_nonce_field('omsar_survey_verify_nonce', 'omsar_survey_verify_nonce'); ?>
                    <div class="omsar-form-group mb-3">
                        <label for="omsar_verification_code" class="form-label"><?php echo esc_html(function_exists('pll__') ? pll__('Verification Code') : __('Verification Code', 'omsar')); ?> <span class="omsar-required">*</span></label>
                        <input type="text" id="omsar_verification_code" name="verification_code" class="form-control" placeholder="<?php echo esc_attr(function_exists('pll__') ? pll__('Enter verification code') : __('Enter verification code', 'omsar')); ?>" required autocomplete="one-time-code">
                    </div>
                    <div id="omsarSurveyVerifyError" class="omsar-survey-error mb-3" role="alert" style="display: none;"></div>
                    <button type="submit" class="omsar-btn omsar-survey-btn" id="omsarSurveyVerifyBtn"><?php echo esc_html(function_exists('pll__') ? pll__('Verify') : __('Verify', 'omsar')); ?></button>
                </form>
            </div>

            <!-- Form step (hidden until verified) -->
            <div id="omsarSurveyFormStep" class="omsar-survey-step" style="display: none;">
                <form id="omsarSurveyForm" class="omsar-survey-form" enctype="multipart/form-data" novalidate>
                    <?php wp_nonce_field('omsar_survey_form_nonce', 'omsar_survey_form_nonce'); ?>

                    <!-- Stepper -->
                    <nav class="omsar-stepper" aria-label="<?php echo esc_attr(function_exists('pll__') ? pll__('Form progress') : 'Form progress'); ?>">
                        <ol class="omsar-stepper-list">
                            <li class="omsar-stepper-item" data-progress-step="1">
                                <span class="omsar-stepper-dot" aria-hidden="true"></span>
                                <span class="omsar-stepper-label"><?php pll_e('Introduction'); ?></span>
                            </li>
                            <li class="omsar-stepper-item" data-progress-step="2">
                                <span class="omsar-stepper-dot" aria-hidden="true"></span>
                                <span class="omsar-stepper-label"><?php pll_e('General Information'); ?></span>
                            </li>
                            <li class="omsar-stepper-item" data-progress-step="3">
                                <span class="omsar-stepper-dot" aria-hidden="true"></span>
                                <span class="omsar-stepper-label"><?php pll_e('Properties'); ?></span>
                            </li>
                            <li class="omsar-stepper-item" data-progress-step="4">
                                <span class="omsar-stepper-dot" aria-hidden="true"></span>
                                <span class="omsar-stepper-label"><?php pll_e('Contact Information'); ?></span>
                            </li>
                        </ol>
                    </nav>

                    <div id="omsarSurveyFormMessage" class="omsar-survey-message mb-3" role="alert" style="display: none;"></div>

                    <!-- Step 1: Introduction -->
                    <div class="omsar-step" data-step="step-intro">
                        <div class="omsar-complaint-title"><?php pll_e('Survey Form'); ?></div>
                        <div class="omsar-complaint-desc">
                            <?php pll_e('This survey form collects information about administrative properties and buildings. Please fill in all required fields accurately.'); ?>
                        </div>
                        <div class="omsar-complaint-actions">
                            <button class="omsar-btn" type="button" data-action="next"><?php pll_e('Continue'); ?></button>
                        </div>
                    </div>

                    <!-- Step 2: General Information about the Administration -->
                    <div class="omsar-step" data-step="step-1" hidden>
                        <h3 class="omsar-step-title"><?php pll_e('General Information about the Administration'); ?></h3>
                        
                        <div class="omsar-form-group">
                            <label class="form-label" for="administration_name"><?php pll_e('Administration Name'); ?> <span class="omsar-required">*</span></label>
                            <input type="text" class="form-control" id="administration_name" name="administration_name" 
                                   placeholder="<?php echo esc_attr(function_exists('pll__') ? pll__('Enter administration name') : ''); ?>" 
                                   data-required="1" required>
                        </div>

                        <div class="omsar-form-group">
                            <label class="form-label" for="number_of_properties"><?php pll_e('Number of properties/buildings under the administration'); ?> <span class="omsar-required">*</span></label>
                            <input type="number" class="form-control" id="number_of_properties" name="number_of_properties" 
                                   min="1" 
                                   placeholder="<?php echo esc_attr(function_exists('pll__') ? pll__('Enter number of properties') : ''); ?>" 
                                   data-required="1" required>
                            <!-- <span class="omsar-field-hint"><?php //pll_e('Please enter the total number of properties or buildings.'); ?></span> -->
                        </div>

                        <div class="omsar-complaint-actions">
                            <button class="omsar-btn omsar-btn-secondary" type="button" data-action="back"><?php pll_e('Back'); ?></button>
                            <button class="omsar-btn" type="button" data-action="next"><?php pll_e('Continue'); ?></button>
                        </div>
                    </div>

                    <!-- Step 3: List of Properties / Buildings (Dynamic) -->
                    <div class="omsar-step" data-step="step-2" hidden>
                        <h3 class="omsar-step-title"><?php pll_e('List of Properties / Buildings'); ?></h3>
                        <div class="omsar-complaint-note omsar-complaint-note--step">
                            <?php pll_e('Please fill in information for each property separately.'); ?>
                        </div>
                        
                        <div id="properties-container">
                            <!-- Properties will be dynamically generated here -->
                        </div>

                        <div class="omsar-complaint-actions">
                            <button class="omsar-btn omsar-btn-secondary" type="button" data-action="back"><?php pll_e('Back'); ?></button>
                            <button class="omsar-btn" type="button" data-action="next"><?php pll_e('Continue'); ?></button>
                        </div>
                    </div>

                    <!-- Step 4: Survey Filler Information -->
                    <div class="omsar-step" data-step="step-3" hidden>
                        <h3 class="omsar-step-title"><?php pll_e('Survey Filler Information'); ?></h3>
                        
                        <div class="omsar-form-row">
                            <div class="omsar-form-group">
                                <label class="form-label" for="survey_full_name"><?php pll_e('Full name of the person filling out the survey'); ?> <span class="omsar-required">*</span></label>
                                <input type="text" class="form-control" id="survey_full_name" name="survey_full_name" 
                                       placeholder="<?php echo esc_attr(function_exists('pll__') ? pll__('Enter your full name') : ''); ?>" 
                                       data-required="1" required>
                            </div>
                            <div class="omsar-form-group">
                                <label class="form-label" for="survey_job_title"><?php pll_e('Job Title'); ?> <span class="omsar-required">*</span></label>
                                <input type="text" class="form-control" id="survey_job_title" name="survey_job_title" 
                                       placeholder="<?php echo esc_attr(function_exists('pll__') ? pll__('Enter your job title') : ''); ?>" 
                                       data-required="1" required>
                            </div>
                        </div>

                        <div class="omsar-form-row">
                            <div class="omsar-form-group">
                                <label class="form-label" for="survey_phone"><?php pll_e('Phone Number'); ?> <span class="omsar-required">*</span></label>
                                <input type="tel" class="form-control" id="survey_phone" name="survey_phone" 
                                       placeholder="<?php echo esc_attr(function_exists('pll__') ? pll__('Enter your phone number') : ''); ?>" 
                                       data-required="1" required>
                            </div>
                            <div class="omsar-form-group">
                                <label class="form-label" for="survey_email"><?php pll_e('Email'); ?> <span class="omsar-required">*</span></label>
                                <input type="email" class="form-control" id="survey_email" name="survey_email" 
                                       placeholder="<?php echo esc_attr(function_exists('pll__') ? pll__('Enter your email address') : ''); ?>" 
                                       data-required="1" required>
                            </div>
                        </div>

                        <div class="omsar-complaint-actions">
                            <button class="omsar-btn omsar-btn-secondary" type="button" data-action="back"><?php pll_e('Back'); ?></button>
                            <button class="omsar-btn" type="button" data-action="submit"><?php pll_e('Submit'); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
var omsarSurvey = <?php echo wp_json_encode(['ajaxUrl' => admin_url('admin-ajax.php')]); ?>;
var omsarSurveyLabels = <?php echo wp_json_encode([
    'loading' => function_exists('pll__') ? pll__('Loading...') : __('Loading...', 'omsar'),
    'invalidCode' => function_exists('pll__') ? pll__('Invalid verification code. Please try again.') : __('Invalid verification code. Please try again.', 'omsar'),
    'error' => function_exists('pll__') ? pll__('An error occurred. Please try again.') : __('An error occurred. Please try again.', 'omsar'),
    'success' => function_exists('pll__') ? pll__('Thank you for completing the survey.') : __('Thank you for completing the survey.', 'omsar'),
]); ?>;
</script>

<?php get_footer(); ?>
