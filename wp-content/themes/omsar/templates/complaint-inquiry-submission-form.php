<?php
/*
Template Name: Complaint / Inquiry Submission Form
*/

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<section class="omsar-complaint-section static-height">
    <div class="container" id="omsarComplaintWizard">
        <div class="omsar-complaint-card">
            <form id="omsarComplaintForm" enctype="multipart/form-data" novalidate>

                <!-- Stepper -->
                <nav class="omsar-stepper" aria-label="<?php echo esc_attr(function_exists('pll__') ? pll__('Form progress') : 'Form progress'); ?>">
                    <ol class="omsar-stepper-list">
                        <li class="omsar-stepper-item" data-progress-step="1">
                            <span class="omsar-stepper-dot" aria-hidden="true"></span>
                            <span class="omsar-stepper-label"><?php pll_e('Introduction'); ?></span>
                        </li>
                        <li class="omsar-stepper-item" data-progress-step="2">
                            <span class="omsar-stepper-dot" aria-hidden="true"></span>
                            <span class="omsar-stepper-label"><?php pll_e('Contact'); ?></span>
                        </li>
                        <li class="omsar-stepper-item" data-progress-step="3">
                            <span class="omsar-stepper-dot" aria-hidden="true"></span>
                            <span class="omsar-stepper-label"><?php pll_e('Inquiry'); ?></span>
                        </li>
                        <li class="omsar-stepper-item" data-progress-step="4">
                            <span class="omsar-stepper-dot" aria-hidden="true"></span>
                            <span class="omsar-stepper-label"><?php pll_e('Issue type'); ?></span>
                        </li>
                        <li class="omsar-stepper-item" data-progress-step="5">
                            <span class="omsar-stepper-dot" aria-hidden="true"></span>
                            <span class="omsar-stepper-label"><?php pll_e('Complaint'); ?></span>
                        </li>
                        <li class="omsar-stepper-item omsar-stepper-item-conditional" data-progress-step="6">
                            <span class="omsar-stepper-dot" aria-hidden="true"></span>
                            <span class="omsar-stepper-label"><?php pll_e('SEA/SH'); ?></span>
                        </li>
                        <li class="omsar-stepper-item" data-progress-step="7">
                            <span class="omsar-stepper-dot" aria-hidden="true"></span>
                            <span class="omsar-stepper-label"><?php pll_e('Consent'); ?></span>
                        </li>
                    </ol>
                </nav>

                <div id="omsarComplaintMessage" class="omsar-complaint-message" aria-live="polite"></div>

                <!-- Step 1: Introduction -->
                <div class="omsar-step" data-step="step-1">
                    <div class="omsar-complaint-title"><?php pll_e('Complaint / Inquiry Submission Form'); ?></div>
                    <div class="omsar-complaint-desc">
                        <?php pll_e('The Complaint and Inquiry Form provides a safe, accessible, and confidential channel for citizens to raise inquiries or complaints. The mechanism is designed in line with the do-no-harm principle and aims to ensure fairness, transparency, and accountability in the handling of all complaints.'); ?>
                    </div>
                    <div class="omsar-complaint-actions">
                        <button class="omsar-btn" type="button" data-action="next"><?php pll_e('Continue'); ?></button>
                    </div>
                </div>

                <!-- Step 2: Contact Details -->
                <div class="omsar-step" data-step="step-2" hidden>
                    <h3 class="omsar-step-title"><?php pll_e('1. Contact Details'); ?></h3>
                    <div class="omsar-complaint-note omsar-complaint-note--step">
                        <p><?php pll_e('You may submit a complaint or inquiry anonymously.'); ?></p>
                        <p><?php pll_e('If you choose to remain anonymous, we will not be able to communicate updates to you, but the complaint/inquiry will still be reviewed and addressed.'); ?></p>
                    </div>

                    <div class="omsar-form-row">
                        <div class="omsar-form-group">
                            <label class="form-label" for="full_name"><?php pll_e('Full Name'); ?></label>
                            <input type="text" class="form-control" id="full_name" name="full_name"
                                   placeholder="<?php echo esc_attr(function_exists('pll__') ? pll__('Enter your full name') : ''); ?>">
                        </div>
                        <div class="omsar-form-group">
                            <label class="form-label" for="phone_number"><?php pll_e('Phone Number'); ?></label>
                            <input type="tel" class="form-control" id="phone_number" name="phone_number"
                                   placeholder="<?php echo esc_attr(function_exists('pll__') ? pll__('Enter your phone number') : ''); ?>">
                        </div>
                    </div>

                    <div class="omsar-form-row">
                        <div class="omsar-form-group">
                            <label class="form-label" for="email"><?php pll_e('Email'); ?></label>
                            <input type="email" class="form-control" id="email" name="email"
                                   placeholder="<?php echo esc_attr(function_exists('pll__') ? pll__('Enter your email address') : ''); ?>">
                        </div>
                        <div class="omsar-form-group">
                            <label class="form-label" for="address"><?php pll_e('Address'); ?></label>
                            <input type="text" class="form-control" id="address" name="address"
                                   placeholder="<?php echo esc_attr(function_exists('pll__') ? pll__('Enter your address') : ''); ?>">
                        </div>
                    </div>

                    <div class="omsar-form-group">
                        <label class="form-label"><?php pll_e('Preferred method of contact'); ?> <span class="omsar-required">*</span></label>
                        <div class="omsar-radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="preferred_contact" id="preferred_contact_email" value="email" data-required="1">
                                <label class="form-check-label" for="preferred_contact_email"><?php pll_e('Email'); ?></label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="preferred_contact" id="preferred_contact_phone" value="phone" data-required="1">
                                <label class="form-check-label" for="preferred_contact_phone"><?php pll_e('Phone/WhatsApp'); ?></label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="preferred_contact" id="preferred_contact_none" value="no_response" data-required="1">
                                <label class="form-check-label" for="preferred_contact_none"><?php pll_e('No response needed'); ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="omsar-form-group">
                        <label class="form-label"><?php pll_e('Are you submitting on behalf of someone else?'); ?> <span class="omsar-required">*</span></label>
                        <div class="omsar-radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="on_behalf" id="on_behalf_yes" value="yes" data-required="1">
                                <label class="form-check-label" for="on_behalf_yes"><?php pll_e('Yes'); ?></label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="on_behalf" id="on_behalf_no" value="no" data-required="1">
                                <label class="form-check-label" for="on_behalf_no"><?php pll_e('No'); ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="omsar-form-group" id="on_behalf_explanation_wrap" style="display: none;">
                        <label class="form-label" for="on_behalf_explanation"><?php pll_e('Explanation'); ?> <span class="omsar-required">*</span></label>
                        <textarea class="form-control" id="on_behalf_explanation" name="on_behalf_explanation" rows="3" data-required-conditional="on_behalf"></textarea>
                    </div>

                    <div class="omsar-complaint-actions">
                        <button class="omsar-btn omsar-btn-secondary" type="button" data-action="back"><?php pll_e('Back'); ?></button>
                        <button class="omsar-btn" type="button" data-action="next"><?php pll_e('Continue'); ?></button>
                    </div>
                </div>

                <!-- Step 3: Inquiry -->
                <div class="omsar-step" data-step="step-3" hidden>
                    <h3 class="omsar-step-title"><?php pll_e('2. What is your inquiry?'); ?></h3>
                    <div class="omsar-form-group">
                        <label class="form-label" for="inquiry_text"><?php pll_e('Inquiry'); ?> <span class="omsar-required">*</span></label>
                        <textarea class="form-control" id="inquiry_text" name="inquiry_text" rows="5" data-max-words="200" data-required="1" required
                                  placeholder="<?php echo esc_attr(function_exists('pll__') ? pll__('Type your inquiry here') : ''); ?>"></textarea>
                        <div class="omsar-word-count"><span data-word-counter-for="inquiry_text"></span></div>
                    </div>
                    <div class="omsar-complaint-actions">
                        <button class="omsar-btn omsar-btn-secondary" type="button" data-action="back"><?php pll_e('Back'); ?></button>
                        <button class="omsar-btn" type="button" data-action="next"><?php pll_e('Continue'); ?></button>
                    </div>
                </div>

                <!-- Step 4: Issue type -->
                <div class="omsar-step" data-step="step-4" hidden>
                    <h3 class="omsar-step-title"><?php pll_e('3. What type of issue are you reporting?'); ?> <span class="omsar-required">*</span></h3>
                    <div class="omsar-complaint-note omsar-complaint-note--step">
                        <?php pll_e('Please select whichever applies:'); ?>
                    </div>
                    <div class="omsar-radio-group omsar-radio-group--stacked">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="issue_type" id="issue_type_general" value="general_admin" data-required="1">
                            <label class="form-check-label" for="issue_type_general"><?php pll_e('General or administrative concern'); ?></label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="issue_type" id="issue_type_conduct" value="conduct_code" data-required="1">
                            <label class="form-check-label" for="issue_type_conduct"><?php pll_e('Conduct or Code of Conduct issue'); ?></label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="issue_type" id="issue_type_working" value="working_environment" data-required="1">
                            <label class="form-check-label" for="issue_type_working"><?php pll_e('Working environment or interaction issue'); ?></label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="issue_type" id="issue_type_fraud" value="fraud_corruption" data-required="1">
                            <label class="form-check-label" for="issue_type_fraud"><?php pll_e('Fraud or corruption concern'); ?></label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="issue_type" id="issue_type_env" value="env_social" data-required="1">
                            <label class="form-check-label" for="issue_type_env"><?php pll_e('Environmental or social concern'); ?></label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="issue_type" id="issue_type_procurement" value="procurement" data-required="1">
                            <label class="form-check-label" for="issue_type_procurement"><?php pll_e('Procurement related'); ?></label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="issue_type" id="issue_type_seash" value="sea_sh" data-required="1">
                            <label class="form-check-label" for="issue_type_seash"><?php pll_e('SEA/SH complaint (confidential pathway)'); ?></label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="issue_type" id="issue_type_other" value="other" data-required="1">
                            <label class="form-check-label" for="issue_type_other"><?php pll_e('Other'); ?></label>
                        </div>
                        <div class="omsar-form-group omsar-form-group--indent" id="issue_type_other_wrap" style="display: none;">
                            <label class="form-label" for="issue_type_other_input"><?php pll_e('Please specify'); ?> <span class="omsar-required">*</span></label>
                            <input type="text" class="form-control" id="issue_type_other_input" name="issue_type_other" data-required-conditional="issue_type"
                                   placeholder="<?php echo esc_attr(function_exists('pll__') ? pll__('Please specify') : ''); ?>">
                        </div>
                    </div>
                    <div class="omsar-complaint-actions">
                        <button class="omsar-btn omsar-btn-secondary" type="button" data-action="back"><?php pll_e('Back'); ?></button>
                        <button class="omsar-btn" type="button" data-action="next"><?php pll_e('Continue'); ?></button>
                    </div>
                </div>

                <!-- Step 5: Complaint -->
                <div class="omsar-step" data-step="step-5" hidden>
                    <h3 class="omsar-step-title"><?php pll_e('4. What is your complaint?'); ?></h3>
                    <div class="omsar-complaint-note omsar-complaint-note--step">
                        <?php pll_e('Include any information you believe is relevant'); ?>
                    </div>
                    <div class="omsar-form-group">
                        <label class="form-label" for="complaint_text"><?php pll_e('Complaint'); ?> <span class="omsar-required">*</span> <span class="omsar-label-hint">(<?php pll_e('Max 200 words'); ?>)</span></label>
                        <textarea class="form-control" id="complaint_text" name="complaint_text" rows="5" data-max-words="200" data-required="1" required
                                  placeholder="<?php echo esc_attr(function_exists('pll__') ? pll__('Type your complaint here') : ''); ?>"></textarea>
                        <div class="omsar-word-count"><span data-word-counter-for="complaint_text"></span></div>
                    </div>
                    <div class="omsar-form-group">
                        <label class="form-label" for="complaint_files"><?php pll_e('Supporting documents'); ?></label>
                        <input class="form-control" type="file" id="complaint_files" name="complaint_files[]" multiple>
                        <span class="omsar-field-hint"><?php pll_e('Please attach supporting documents, screenshots, or photos'); ?></span>
                    </div>
                    <div class="omsar-complaint-actions">
                        <button class="omsar-btn omsar-btn-secondary" type="button" data-action="back"><?php pll_e('Back'); ?></button>
                        <button class="omsar-btn" type="button" data-action="next"><?php pll_e('Continue'); ?></button>
                    </div>
                </div>

                <!-- Step 6: SEA/SH (conditional) -->
                <div class="omsar-step" data-step="step-6" hidden>
                    <h3 class="omsar-step-title"><?php pll_e('5. SEA/SH Complaints'); ?></h3>
                
                    <div class="omsar-complaint-note omsar-complaint-note--step">
                        <!-- <p><?php //pll_e('This section applies ONLY if you selected “SEA/SH complaint” above.'); ?></p> -->
                        <p><?php pll_e('SEA/SH complaints may be submitted anonymously.'); ?></p>
                        <p><?php pll_e('No identifying information is required. The project does not investigate SEA/SH cases but will offer referral to specialized support services.'); ?></p>
                    </div>

                    

                    <div class="omsar-form-group">
                        <label class="form-label"><?php pll_e('Would you like to remain anonymous?'); ?> <span class="omsar-required">*</span></label>
                        <div class="omsar-radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="sea_sh_anonymous" id="sea_sh_anonymous_yes" value="yes" data-required="1">
                                <label class="form-check-label" for="sea_sh_anonymous_yes"><?php pll_e('Yes'); ?></label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="sea_sh_anonymous" id="sea_sh_anonymous_no" value="no" data-required="1">
                                <label class="form-check-label" for="sea_sh_anonymous_no"><?php pll_e('No'); ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="omsar-form-group">
                        <label class="form-label"><?php pll_e('Would you like referral to specialized support?'); ?> <span class="omsar-required">*</span></label>
                        <div class="omsar-radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="sea_sh_referral" id="sea_sh_referral_yes" value="yes" data-required="1">
                                <label class="form-check-label" for="sea_sh_referral_yes"><?php pll_e('Yes'); ?></label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="sea_sh_referral" id="sea_sh_referral_no" value="no" data-required="1">
                                <label class="form-check-label" for="sea_sh_referral_no"><?php pll_e('No'); ?></label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="sea_sh_referral" id="sea_sh_referral_unsure" value="unsure" data-required="1">
                                <label class="form-check-label" for="sea_sh_referral_unsure"><?php pll_e('Unsure'); ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="omsar-form-group">
                        <label class="form-label" for="sea_sh_info"><?php pll_e('Optional information'); ?></label>
                        <textarea class="form-control" id="sea_sh_info" name="sea_sh_info" rows="4" data-max-words="200"
                                  placeholder="<?php echo esc_attr(function_exists('pll__') ? pll__('Please indicate any information you are comfortable sharing') : ''); ?>"></textarea>
                        <div class="omsar-word-count"><span data-word-counter-for="sea_sh_info"></span></div>
                    </div>

                    <div class="omsar-complaint-actions">
                        <button class="omsar-btn omsar-btn-secondary" type="button" data-action="back"><?php pll_e('Back'); ?></button>
                        <button class="omsar-btn" type="button" data-action="next"><?php pll_e('Continue'); ?></button>
                    </div>
                </div>

                <!-- Step 7: Consent -->
                <div class="omsar-step" data-step="step-7" hidden>
                    <h3 class="omsar-step-title" 
                        data-title-step5="<?php echo esc_attr(function_exists('pll__') ? pll__('5. Confidentiality and Consent') : '5. Confidentiality and Consent'); ?>"
                        data-title-step6="<?php echo esc_attr(function_exists('pll__') ? pll__('6. Confidentiality and Consent') : '6. Confidentiality and Consent'); ?>">
                        <?php pll_e('6. Confidentiality and Consent'); ?>
                    </h3>
                    <div class="omsar-form-group">
                        <label class="form-label"><?php pll_e('Do you consent to be contacted for follow-up?'); ?></label>
                        <div class="omsar-radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="consent_followup" id="consent_followup_yes" value="yes" data-required="1">
                                <label class="form-check-label" for="consent_followup_yes"><?php pll_e('Yes'); ?></label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="consent_followup" id="consent_followup_no" value="no" data-required="1">
                                <label class="form-check-label" for="consent_followup_no"><?php pll_e('No'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="omsar-complaint-note omsar-complaint-note--step" id="seash_consent_note" style="display: none;">
                        <?php pll_e('For SEA/SH complaints, confidentiality is guaranteed and names are not required.'); ?>
                    </div>
                    <div class="omsar-complaint-actions">
                        <button class="omsar-btn omsar-btn-secondary" type="button" data-action="back"><?php pll_e('Back'); ?></button>
                        <button class="omsar-btn" type="button" data-action="submit"><?php pll_e('Submit'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<?php get_footer(); ?>
