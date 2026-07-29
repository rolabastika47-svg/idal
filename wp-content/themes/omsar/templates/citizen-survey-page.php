<?php
/*
Template Name: Citizen Survey
*/

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<section class="omsar-survey-section static-height">
    <div class="container" id="omsarSurveyWizard">
        <div class="omsar-survey-card">

            <!-- Form step (shown directly) -->
            <div id="omsarSurveyFormStep" class="omsar-survey-step">
                <form id="omsarSurveyForm" class="omsar-survey-form" enctype="multipart/form-data" novalidate>
                    <?php wp_nonce_field('omsar_survey_form_nonce', 'omsar_survey_form_nonce'); ?>

                    <!-- Stepper -->
                    <nav class="omsar-stepper" aria-label="<?php echo esc_attr(function_exists('pll__') ? pll__('Form progress') : 'Form progress'); ?>">
                        <ol class="omsar-stepper-list">
                            <li class="omsar-stepper-item" data-progress-step="1">
                                <span class="omsar-stepper-dot" aria-hidden="true"></span>
                                <span class="omsar-stepper-label"><?php echo esc_html(function_exists('pll__') ? pll__('Introduction') : __('Introduction', 'omsar')); ?></span>
                            </li>
                            <li class="omsar-stepper-item" data-progress-step="2">
                                <span class="omsar-stepper-dot" aria-hidden="true"></span>
                                <span class="omsar-stepper-label"><?php echo esc_html(function_exists('pll__') ? pll__('Interaction with Public Institutions') : __('Interaction with Public Institutions', 'omsar')); ?></span>
                            </li>
                            <li class="omsar-stepper-item" data-progress-step="3">
                                <span class="omsar-stepper-dot" aria-hidden="true"></span>
                                <span class="omsar-stepper-label"><?php echo esc_html(function_exists('pll__') ? pll__('Priorities and Reform Perceptions') : __('Priorities and Reform Perceptions', 'omsar')); ?></span>
                            </li>
                            <li class="omsar-stepper-item" data-progress-step="4">
                                <span class="omsar-stepper-dot" aria-hidden="true"></span>
                                <span class="omsar-stepper-label"><?php echo esc_html(function_exists('pll__') ? pll__('General Information') : __('General Information', 'omsar')); ?></span>
                            </li>
                        </ol>
                    </nav>

                    <div id="omsarSurveyFormMessage" class="omsar-survey-message mb-3" role="alert" style="display: none;"></div>

                    <!-- Step 1: Introduction -->
                    <div class="omsar-step" data-step="step-intro">
                        <div class="omsar-complaint-title"><?php echo esc_html(function_exists('pll__') ? pll__('Public Administration Reform Program 2030') : __('Public Administration Reform Program 2030', 'omsar')); ?></div>
                        <div class="omsar-complaint-desc">
                            <?php 
                            // Display the page content
                            $content = get_the_content();
                            if (empty($content)) {
                                // Fallback to post excerpt if content is empty
                                $content = get_the_excerpt();
                            }
                            echo apply_filters('the_content', $content);
                            ?>
                        </div>
                        <div class="omsar-complaint-actions">
                            <button class="omsar-btn" type="button" data-action="next"><?php echo esc_html(function_exists('pll__') ? pll__('Continue') : __('Continue', 'omsar')); ?></button>
                        </div>
                    </div>

                    <!-- Step 2: Section 1 - Interaction with Public Institutions -->
                    <div class="omsar-step" data-step="step-1" hidden>
                        <h3 class="omsar-step-title"><?php echo esc_html(function_exists('pll__') ? pll__('Interaction with Public Institutions') : __('Interaction with Public Institutions', 'omsar')); ?></h3>

                        <!-- Q1 Multiple choice (at least one required) -->
                        <input type="hidden" name="q1_valid" id="q1_valid" value="" data-required="1">
                        <div class="omsar-form-group">
                            <label class="form-label"><span class="omsar-q-num" aria-hidden="true">1.</span> <?php echo esc_html(function_exists('pll__') ? pll__('Which of the following public institutions have you interacted with during the past 12 months? (You may select more than one)') : __('Which of the following public institutions have you interacted with during the past 12 months? (You may select more than one)', 'omsar')); ?> <span class="omsar-required">*</span></label>
                            <div class="omsar-radio-group omsar-radio-group--stacked omsar-radio-group--two-cols">
                                <?php
                                $q1_options = array(
                                    'Ministry of Interior and Municipalities',
                                    'Ministry of Energy and Water',
                                    'Ministry of Finance',
                                    'Ministry of Public Health',
                                    'Ministry of Social Affairs',
                                    'Ministry of Education and Higher Education',
                                    'Ministry of Justice',
                                    'Ministry of Labor',
                                    'Ministry of Information',
                                    'Ministry of Economy and Trade',
                                    'Ministry of Telecommunications',
                                    'Ministry of Environment',
                                    'Ministry of Culture',
                                    'Ministry of Foreign Affairs and Emigrants',
                                    'Ministry of Agriculture',
                                    'Ministry of Tourism',
                                    'Ministry of Youth and Sports',
                                    'Ministry of Industry',
                                    'Ministry of Administrative Development',
                                    'Ministry of Displaced',
                                    'Ministry of Public Works and Transport',
                                    'Ministry of Defense',
                                    'Municipalities',
                                    'Governorates',
                                    'Mukhtars',
                                    'I have not interacted with any of the above',
                                );
                                foreach ($q1_options as $idx => $opt) {
                                    $val = ($opt === 'I have not interacted with any of the above') ? 'not_any' : 'q1_' . $idx;
                                    $label = function_exists('pll__') ? pll__($opt) : $opt;
                                    ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="q1_institutions[]" id="q1_<?php echo esc_attr($val); ?>" value="<?php echo esc_attr($val); ?>" data-required="1">
                                        <label class="form-check-label" for="q1_<?php echo esc_attr($val); ?>"><?php echo esc_html($label); ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <div id="omsar_citizen_q2_q5_group">
                            <!-- Q2 Single choice -->
                            <div class="omsar-form-group">
                                <label class="form-label"><span class="omsar-q-num" aria-hidden="true">2.</span> <?php echo esc_html(function_exists('pll__') ? pll__('Overall, how satisfied are you with the performance of the institutions you interacted with in the past 12 months?') : __('Overall, how satisfied are you with the performance of the institutions you interacted with in the past 12 months?', 'omsar')); ?> <span class="omsar-required">*</span></label>
                            <div class="omsar-radio-group omsar-radio-group--stacked">
                                <?php
                                $q2_opts = array('Very satisfied', 'Somewhat satisfied', 'Somewhat dissatisfied', 'Very dissatisfied', 'Not applicable / I don\'t know');
                                foreach ($q2_opts as $i => $o) {
                                    $v = 'q2_' . $i;
                                    ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="q2_satisfaction" id="q2_<?php echo esc_attr($v); ?>" value="<?php echo esc_attr($v); ?>" data-required="1">
                                        <label class="form-check-label" for="q2_<?php echo esc_attr($v); ?>"><?php echo esc_html(function_exists('pll__') ? pll__($o) : $o); ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                            </div>

                            <!-- Q3 Matrix -->
                            <div class="omsar-form-group">
                                <label class="form-label"><span class="omsar-q-num" aria-hidden="true">3.</span> <?php echo esc_html(function_exists('pll__') ? pll__('How satisfied are you with the following aspects?') : __('How satisfied are you with the following aspects?', 'omsar')); ?> <span class="omsar-required">*</span></label>
                                <?php
                                $q3_rows = array(
                                    'Clarity of required procedures',
                                    'Ease of completing the service',
                                    'Processing time',
                                    'Cost of service',
                                    'Staff cooperation',
                                    'Accessibility to responsible officials when needed',
                                );
                                $q3_cols = array('Very satisfied', 'Somewhat satisfied', 'Somewhat dissatisfied', 'Very dissatisfied', 'Not applicable / I don\'t know');
                                ?>
                                <div class="omsar-q3-matrix-wrap">
                                    <div class="table-responsive omsar-q3-matrix-table">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <?php foreach ($q3_cols as $c) { ?>
                                                        <th><?php echo esc_html(function_exists('pll__') ? pll__($c) : $c); ?></th>
                                                    <?php } ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($q3_rows as $ri => $row) {
                                                    $row_label = function_exists('pll__') ? pll__($row) : $row;
                                                    ?>
                                                    <tr>
                                                        <td class="omsar-q3-row-label"><?php echo esc_html($row_label); ?></td>
                                                        <?php foreach ($q3_cols as $ci => $col) {
                                                            $col_label = function_exists('pll__') ? pll__($col) : $col;
                                                            ?>
                                                            <td class="omsar-q3-cell">
                                                                <label class="omsar-matrix-option form-check">
                                                                    <input class="form-check-input" type="radio" name="q3_matrix[<?php echo esc_attr($ri); ?>][selected_value]" value="<?php echo esc_attr($ci); ?>" data-required="1" required>
                                                                    <span class="omsar-matrix-cell-label form-check-label"><?php echo esc_html($col_label); ?></span>
                                                                </label>
                                                            </td>
                                                        <?php } ?>
                                                        <!-- Hidden field for row_name -->
                                                        <input type="hidden" name="q3_matrix[<?php echo esc_attr($ri); ?>][row_name]" value="<?php echo esc_attr($row); ?>">
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Q4 Ranking Top 3 -->
                            <div class="omsar-form-group omsar-ranking-group" data-ranking-max="3">
                                <label class="form-label"><span class="omsar-q-num" aria-hidden="true">4.</span> <?php echo esc_html(function_exists('pll__') ? pll__('What are the three most important reforms that would improve your experience?') : __('What are the three most important reforms that would improve your experience?', 'omsar')); ?> <span class="omsar-required">*</span></label>
                                <div id="omsar_ranking_q4_error" class="omsar-survey-error mb-2" role="alert" style="display: none;"></div>
                                <?php
                                $q4_opts = array(
                                    'Providing clear and accessible information',
                                    'Inclusive services for all groups including persons with disabilities and elderly',
                                    'Simplifying procedures and reducing timeframes',
                                    'Unified government online portal',
                                    'Reducing service costs',
                                    'Enabling complaint submission and quick response',
                                    'Decentralizing procedures to municipalities',
                                    'Improving infrastructure and cleanliness of public offices',
                                );
                                foreach ($q4_opts as $i => $opt) {
                                    $id = 'q4_opt_' . $i;
                                    ?>
                                    <div class="omsar-ranking-row form-check d-flex align-items-center gap-2 mb-2">
                                        <input class="form-check-input omsar-ranking-cb" type="checkbox" name="q4_rank_cb[]" id="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($i); ?>">
                                        <label class="form-check-label mb-0" for="<?php echo esc_attr($id); ?>"><?php echo esc_html(function_exists('pll__') ? pll__($opt) : $opt); ?></label>
                                        <input type="number" class="form-control omsar-ranking-num" name="q4_rank_num[]" min="1" max="3" placeholder="1-3" style="width: 4rem; display: none !important;" data-ranking-for="<?php echo esc_attr($i); ?>" hidden>
                                    </div>
                                <?php } ?>
                            </div>

                            <!-- Q5 Open text -->
                            <div class="omsar-form-group">
                                <label class="form-label" for="q5_other_reforms"><span class="omsar-q-num" aria-hidden="true">5.</span> <?php echo esc_html(function_exists('pll__') ? pll__('Are there any other reforms you would like to suggest?') : __('Are there any other reforms you would like to suggest?', 'omsar')); ?></label>
                                <textarea class="form-control" id="q5_other_reforms" name="q5_other_reforms" rows="4" placeholder="<?php echo esc_attr(function_exists('pll__') ? pll__('Your suggestions...') : __('Your suggestions...', 'omsar')); ?>"></textarea>
                            </div>
                        </div>

                        <div class="omsar-complaint-actions">
                            <button class="omsar-btn omsar-btn-secondary" type="button" data-action="back"><?php echo esc_html(function_exists('pll__') ? pll__('Back') : __('Back', 'omsar')); ?></button>
                            <button class="omsar-btn" type="button" data-action="next"><?php echo esc_html(function_exists('pll__') ? pll__('Continue') : __('Continue', 'omsar')); ?></button>
                        </div>
                    </div>

                    <!-- Step 3: Section 2 - Priorities and Reform Perceptions -->
                    <div class="omsar-step" data-step="step-2" hidden>
                        <h3 class="omsar-step-title"><?php echo esc_html(function_exists('pll__') ? pll__('Priorities and Reform Perceptions') : __('Priorities and Reform Perceptions', 'omsar')); ?></h3>

                        <!-- Q6 -->
                        <div class="omsar-form-group">
                            <label class="form-label"><span class="omsar-q-num" aria-hidden="true">1.</span> <?php echo esc_html(function_exists('pll__') ? pll__('How much trust do you have in public institutions overall?') : __('How much trust do you have in public institutions overall?', 'omsar')); ?> <span class="omsar-required">*</span></label>
                            <div class="omsar-radio-group omsar-radio-group--stacked">
                                <?php
                                $q6_opts = array('Full trust', 'High trust', 'Moderate trust', 'Low trust', 'No trust at all');
                                foreach ($q6_opts as $i => $o) {
                                    ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="q6_trust" id="q6_<?php echo esc_attr($i); ?>" value="q6_<?php echo esc_attr($i); ?>" data-required="1" required>
                                        <label class="form-check-label" for="q6_<?php echo esc_attr($i); ?>"><?php echo esc_html(function_exists('pll__') ? pll__($o) : $o); ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- Q7 Ranking Top 3 -->
                        <div class="omsar-form-group omsar-ranking-group" data-ranking-max="3">
                            <label class="form-label"><span class="omsar-q-num" aria-hidden="true">2.</span> <?php echo esc_html(function_exists('pll__') ? pll__('Which three sectors should be prioritized?') : __('Which three sectors should be prioritized?', 'omsar')); ?> <span class="omsar-required">*</span></label>
                            <div id="omsar_ranking_q7_error" class="omsar-survey-error mb-2" role="alert" style="display: none;"></div>
                            <?php
                            $q7_opts = array(
                                'Health',
                                'Education',
                                'Energy and electricity',
                                'Water, waste and sanitation',
                                'Public transport and road safety',
                                'Employment and labor market',
                                'Banking, finance and insurance',
                                'Housing and real estate',
                                'Justice and public security',
                                'Environment and natural resources',
                            );
                            foreach ($q7_opts as $i => $opt) {
                                $id = 'q7_opt_' . $i;
                                ?>
                                <div class="omsar-ranking-row form-check d-flex align-items-center gap-2 mb-2" data-q7-index="<?php echo esc_attr($i); ?>">
                                    <input class="form-check-input omsar-ranking-cb" type="checkbox" name="q7_rank_cb[]" id="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($i); ?>">
                                    <label class="form-check-label mb-0" for="<?php echo esc_attr($id); ?>"><?php echo esc_html(function_exists('pll__') ? pll__($opt) : $opt); ?></label>
                                    <input type="number" class="form-control omsar-ranking-num" name="q7_rank_num[]" min="1" max="3" placeholder="1-3" style="width: 4rem; display: none !important;" data-ranking-for="<?php echo esc_attr($i); ?>" hidden>
                                </div>
                            <?php } ?>
                        </div>

                        <!-- Q8 -->
                        <div class="omsar-form-group">
                            <label class="form-label" for="q8_other_sectors"><span class="omsar-q-num" aria-hidden="true">3.</span> <?php echo esc_html(function_exists('pll__') ? pll__('Are there other priority sectors not listed above?') : __('Are there other priority sectors not listed above?', 'omsar')); ?></label>
                            <textarea class="form-control" id="q8_other_sectors" name="q8_other_sectors" rows="4" placeholder="<?php echo esc_attr(function_exists('pll__') ? pll__('Your response...') : __('Your response...', 'omsar')); ?>"></textarea>
                        </div>

                        <div class="omsar-complaint-actions">
                            <button class="omsar-btn omsar-btn-secondary" type="button" data-action="back"><?php echo esc_html(function_exists('pll__') ? pll__('Back') : __('Back', 'omsar')); ?></button>
                            <button class="omsar-btn" type="button" data-action="next"><?php echo esc_html(function_exists('pll__') ? pll__('Continue') : __('Continue', 'omsar')); ?></button>
                        </div>
                    </div>

                    <!-- Step 4: Section 3 - General Information -->
                    <div class="omsar-step" data-step="step-3" hidden>
                        <h3 class="omsar-step-title"><?php echo esc_html(function_exists('pll__') ? pll__('General Information') : __('General Information', 'omsar')); ?></h3>

                        <!-- Q9 -->
                        <div class="omsar-form-group">
                            <label class="form-label"><span class="omsar-q-num" aria-hidden="true">1.</span> <?php echo esc_html(function_exists('pll__') ? pll__('Are you Lebanese or another nationality?') : __('Are you Lebanese or another nationality?', 'omsar')); ?> <span class="omsar-required">*</span></label>
                            <div class="omsar-radio-group omsar-radio-group--stacked">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="q9_nationality" id="q9_lebanese" value="lebanese" data-required="1" required>
                                    <label class="form-check-label" for="q9_lebanese"><?php echo esc_html(function_exists('pll__') ? pll__('Lebanese') : __('Lebanese', 'omsar')); ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="q9_nationality" id="q9_other" value="other" data-required="1" required>
                                    <label class="form-check-label" for="q9_other"><?php echo esc_html(function_exists('pll__') ? pll__('Other nationality') : __('Other nationality', 'omsar')); ?></label>
                                </div>
                            </div>
                        </div>

                        <div id="omsar_citizen_q10_group" style="display: none;">
                            <!-- Q10 -->
                            <div class="omsar-form-group">
                                <label class="form-label"><span class="omsar-q-num" aria-hidden="true">2.</span> <?php echo esc_html(function_exists('pll__') ? pll__('Are you residing in Lebanon or abroad?') : __('Are you residing in Lebanon or abroad?', 'omsar')); ?> <span class="omsar-required">*</span></label>
                                <div class="omsar-radio-group omsar-radio-group--stacked">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="q10_residence" id="q10_lebanon" value="lebanon" data-required="1">
                                        <label class="form-check-label" for="q10_lebanon"><?php echo esc_html(function_exists('pll__') ? pll__('Residing in Lebanon') : __('Residing in Lebanon', 'omsar')); ?></label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="q10_residence" id="q10_abroad" value="abroad" data-required="1">
                                        <label class="form-check-label" for="q10_abroad"><?php echo esc_html(function_exists('pll__') ? pll__('Residing abroad') : __('Residing abroad', 'omsar')); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="omsar_citizen_q11_group" style="display: none;">
                            <div class="omsar-form-group">
                                <label class="form-label" for="q11_governorate"><span class="omsar-q-num" aria-hidden="true">3.</span> <?php echo esc_html(function_exists('pll__') ? pll__('Governorate (if residing in Lebanon)') : __('Governorate (if residing in Lebanon)', 'omsar')); ?> <span class="omsar-required">*</span></label>
                                <select class="form-control" id="q11_governorate" name="q11_governorate" data-required="1">
                                    <option value=""><?php echo esc_html(function_exists('pll__') ? pll__('Select') : __('Select', 'omsar')); ?></option>
                                    <option value="Beirut"><?php echo esc_html(function_exists('pll__') ? pll__('Beirut') : __('Beirut', 'omsar')); ?></option>
                                    <option value="Mount Lebanon"><?php echo esc_html(function_exists('pll__') ? pll__('Mount Lebanon') : __('Mount Lebanon', 'omsar')); ?></option>
                                    <option value="South"><?php echo esc_html(function_exists('pll__') ? pll__('South') : __('South', 'omsar')); ?></option>
                                    <option value="Nabatieh"><?php echo esc_html(function_exists('pll__') ? pll__('Nabatieh') : __('Nabatieh', 'omsar')); ?></option>
                                    <option value="North"><?php echo esc_html(function_exists('pll__') ? pll__('North') : __('North', 'omsar')); ?></option>
                                    <option value="Akkar"><?php echo esc_html(function_exists('pll__') ? pll__('Akkar') : __('Akkar', 'omsar')); ?></option>
                                    <option value="Baalbek-Hermel"><?php echo esc_html(function_exists('pll__') ? pll__('Baalbek-Hermel') : __('Baalbek-Hermel', 'omsar')); ?></option>
                                    <option value="Bekaa"><?php echo esc_html(function_exists('pll__') ? pll__('Bekaa') : __('Bekaa', 'omsar')); ?></option>
                                </select>
                            </div>
                        </div>

                        <div id="omsar_citizen_q12_group" style="display: none;">
                            <div class="omsar-form-group">
                                <label class="form-label" for="q12_region_abroad"><span class="omsar-q-num" aria-hidden="true">4.</span> <?php echo esc_html(function_exists('pll__') ? pll__('Region (if residing abroad)') : __('Region (if residing abroad)', 'omsar')); ?> <span class="omsar-required">*</span></label>
                                <select class="form-control" id="q12_region_abroad" name="q12_region_abroad" data-required="1">
                                    <option value=""><?php echo esc_html(function_exists('pll__') ? pll__('Select') : __('Select', 'omsar')); ?></option>
                                    <option value="GCC countries"><?php echo esc_html(function_exists('pll__') ? pll__('GCC countries') : __('GCC countries', 'omsar')); ?></option>
                                    <option value="Other Arab countries"><?php echo esc_html(function_exists('pll__') ? pll__('Other Arab countries') : __('Other Arab countries', 'omsar')); ?></option>
                                    <option value="Africa"><?php echo esc_html(function_exists('pll__') ? pll__('Africa') : __('Africa', 'omsar')); ?></option>
                                    <option value="Asia"><?php echo esc_html(function_exists('pll__') ? pll__('Asia') : __('Asia', 'omsar')); ?></option>
                                    <option value="North America"><?php echo esc_html(function_exists('pll__') ? pll__('North America') : __('North America', 'omsar')); ?></option>
                                    <option value="South America"><?php echo esc_html(function_exists('pll__') ? pll__('South America') : __('South America', 'omsar')); ?></option>
                                    <option value="Europe"><?php echo esc_html(function_exists('pll__') ? pll__('Europe') : __('Europe', 'omsar')); ?></option>
                                    <option value="Australia"><?php echo esc_html(function_exists('pll__') ? pll__('Australia') : __('Australia', 'omsar')); ?></option>
                                </select>
                            </div>
                        </div>

                        <!-- Q13 Age -->
                        <div class="omsar-form-group">
                            <label class="form-label"><span class="omsar-q-num" aria-hidden="true">5.</span> <?php echo esc_html(function_exists('pll__') ? pll__('Age group:') : __('Age group:', 'omsar')); ?> <span class="omsar-required">*</span></label>
                            <div class="omsar-radio-group omsar-radio-group--stacked">
                                <?php
                                $q13_opts = array('Under 18', '18–34', '35–49', '50–64', '65+');
                                foreach ($q13_opts as $i => $o) {
                                    ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="q13_age" id="q13_<?php echo esc_attr($i); ?>" value="q13_<?php echo esc_attr($i); ?>" data-required="1" required>
                                        <label class="form-check-label" for="q13_<?php echo esc_attr($i); ?>"><?php echo esc_html(function_exists('pll__') ? pll__($o) : $o); ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- Q14 Gender -->
                        <div class="omsar-form-group">
                            <label class="form-label"><span class="omsar-q-num" aria-hidden="true">6.</span> <?php echo esc_html(function_exists('pll__') ? pll__('Gender:') : __('Gender:', 'omsar')); ?> <span class="omsar-required">*</span></label>
                            <div class="omsar-radio-group omsar-radio-group--stacked">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="q14_gender" id="q14_female" value="female" data-required="1" required>
                                    <label class="form-check-label" for="q14_female"><?php echo esc_html(function_exists('pll__') ? pll__('Female') : __('Female', 'omsar')); ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="q14_gender" id="q14_male" value="male" data-required="1" required>
                                    <label class="form-check-label" for="q14_male"><?php echo esc_html(function_exists('pll__') ? pll__('Male') : __('Male', 'omsar')); ?></label>
                                </div>
                            </div>
                        </div>

                        <!-- Q15 Disability -->
                        <div class="omsar-form-group">
                            <label class="form-label"><span class="omsar-q-num" aria-hidden="true">7.</span> <?php echo esc_html(function_exists('pll__') ? pll__('Do you have any type of disability?') : __('Do you have any type of disability?', 'omsar')); ?> <span class="omsar-required">*</span></label>
                            <div class="omsar-radio-group omsar-radio-group--stacked">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="q15_disability" id="q15_yes" value="yes" data-required="1" required>
                                    <label class="form-check-label" for="q15_yes"><?php echo esc_html(function_exists('pll__') ? pll__('Yes') : __('Yes', 'omsar')); ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="q15_disability" id="q15_no" value="no" data-required="1" required>
                                    <label class="form-check-label" for="q15_no"><?php echo esc_html(function_exists('pll__') ? pll__('No') : __('No', 'omsar')); ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="q15_disability" id="q15_prefer_not" value="prefer_not" data-required="1" required>
                                    <label class="form-check-label" for="q15_prefer_not"><?php echo esc_html(function_exists('pll__') ? pll__('Prefer not to answer') : __('Prefer not to answer', 'omsar')); ?></label>
                                </div>
                            </div>
                        </div>

                        <!-- Q16 Income -->
                        <div class="omsar-form-group">
                            <label class="form-label" for="q16_income"><span class="omsar-q-num" aria-hidden="true">8.</span> <?php echo esc_html(function_exists('pll__') ? pll__('Annual income:') : __('Annual income:', 'omsar')); ?></label>
                            <select class="form-control" id="q16_income" name="q16_income">
                                <option value=""><?php echo esc_html(function_exists('pll__') ? pll__('Select') : __('Select', 'omsar')); ?></option>
                                <option value="0_4000"><?php echo esc_html(function_exists('pll__') ? pll__('0–4000$ (0–360,000,000 LBP)') : __('0–4000$ (0–360,000,000 LBP)', 'omsar')); ?></option>
                                <option value="4001_10000"><?php echo esc_html(function_exists('pll__') ? pll__('4001–10000$ (360,000,001–900,000,000 LBP)') : __('4001–10000$ (360,000,001–900,000,000 LBP)', 'omsar')); ?></option>
                                <option value="10001_20000"><?php echo esc_html(function_exists('pll__') ? pll__('10001–20000$ (900,000,001–1,800,000,000 LBP)') : __('10001–20000$ (900,000,001–1,800,000,000 LBP)', 'omsar')); ?></option>
                                <option value="20001_40000"><?php echo esc_html(function_exists('pll__') ? pll__('20001–40000$ (1,800,000,001–3,600,000,000 LBP)') : __('20001–40000$ (1,800,000,001–3,600,000,000 LBP)', 'omsar')); ?></option>
                                <option value="40001_80000"><?php echo esc_html(function_exists('pll__') ? pll__('40001–80000$ (3,600,000,001–7,200,000,000 LBP)') : __('40001–80000$ (3,600,000,001–7,200,000,000 LBP)', 'omsar')); ?></option>
                                <option value="80001_150000"><?php echo esc_html(function_exists('pll__') ? pll__('80001–150000$ (7,200,000,001–13,500,000,000 LBP)') : __('80001–150000$ (7,200,000,001–13,500,000,000 LBP)', 'omsar')); ?></option>
                                <option value="above_150000"><?php echo esc_html(function_exists('pll__') ? pll__('More than 13,500,000,000 LBP (more than 150,000$)') : __('More than 13,500,000,000 LBP (more than 150,000$)', 'omsar')); ?></option>
                                <option value="prefer_not"><?php echo esc_html(function_exists('pll__') ? pll__('Prefer not to answer') : __('Prefer not to answer', 'omsar')); ?></option>
                            </select>
                        </div>

                        <!-- Q17 Employed -->
                        <div class="omsar-form-group">
                            <label class="form-label"><span class="omsar-q-num" aria-hidden="true">9.</span> <?php echo esc_html(function_exists('pll__') ? pll__('Are you currently employed?') : __('Are you currently employed?', 'omsar')); ?> <span class="omsar-required">*</span></label>
                            <div class="omsar-radio-group omsar-radio-group--stacked">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="q17_employed" id="q17_yes" value="yes" data-required="1" required>
                                    <label class="form-check-label" for="q17_yes"><?php echo esc_html(function_exists('pll__') ? pll__('Yes') : __('Yes', 'omsar')); ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="q17_employed" id="q17_no" value="no" data-required="1" required>
                                    <label class="form-check-label" for="q17_no"><?php echo esc_html(function_exists('pll__') ? pll__('No') : __('No', 'omsar')); ?></label>
                                </div>
                            </div>
                        </div>

                        <div id="omsar_citizen_q17_yes_group" style="display: none;">
                            <div class="omsar-form-group">
                                <label class="form-label"><span class="omsar-q-num" aria-hidden="true">10.</span> <?php echo esc_html(function_exists('pll__') ? pll__('Employment sector') : __('Employment sector', 'omsar')); ?> <span class="omsar-required">*</span></label>
                                <div class="omsar-radio-group omsar-radio-group--stacked">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="q17_employment_sector" id="q17_public" value="public_sector" data-required-conditional="q17_employed">
                                        <label class="form-check-label" for="q17_public"><?php echo esc_html(function_exists('pll__') ? pll__('Public sector') : __('Public sector', 'omsar')); ?></label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="q17_employment_sector" id="q17_private" value="private_sector" data-required-conditional="q17_employed">
                                        <label class="form-check-label" for="q17_private"><?php echo esc_html(function_exists('pll__') ? pll__('Private sector') : __('Private sector', 'omsar')); ?></label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="q17_employment_sector" id="q17_ngo" value="ngo_sector" data-required-conditional="q17_employed">
                                        <label class="form-check-label" for="q17_ngo"><?php echo esc_html(function_exists('pll__') ? pll__('NGO sector') : __('NGO sector', 'omsar')); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="omsar_citizen_q17_no_group" style="display: none;">
                            <div class="omsar-form-group">
                                <label class="form-label"><span class="omsar-q-num" aria-hidden="true">11.</span> <?php echo esc_html(function_exists('pll__') ? pll__('Status') : __('Status', 'omsar')); ?> <span class="omsar-required">*</span></label>
                                <div class="omsar-radio-group omsar-radio-group--stacked">
                                    <?php
                                    $q17_no_opts = array('Student', 'Homemaker', 'Retired', 'Medical condition', 'Unemployed');
                                    foreach ($q17_no_opts as $i => $o) {
                                        $v = 'q17_no_' . $i;
                                        ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="q17_employment_status" id="<?php echo esc_attr($v); ?>" value="<?php echo esc_attr($v); ?>" data-required-conditional="q17_employed">
                                            <label class="form-check-label" for="<?php echo esc_attr($v); ?>"><?php echo esc_html(function_exists('pll__') ? pll__($o) : $o); ?></label>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                        <div class="omsar-complaint-actions">
                            <button class="omsar-btn omsar-btn-secondary" type="button" data-action="back"><?php echo esc_html(function_exists('pll__') ? pll__('Back') : __('Back', 'omsar')); ?></button>
                            <button class="omsar-btn" type="button" data-action="submit"><?php echo esc_html(function_exists('pll__') ? pll__('Submit') : __('Submit', 'omsar')); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
