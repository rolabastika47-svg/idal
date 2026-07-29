/**
 * Citizen Survey (2030) – stepped wizard with verification
 * Dedicated script for the Citizen Survey template only.
 */
(function () {
    'use strict';

    function qs(sel, root) { return (root || document).querySelector(sel); }
    function qsa(sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); }

    function show(el) { if (el) { el.hidden = false; el.style.display = ''; } }
    function hide(el) { if (el) { el.hidden = true; el.style.display = 'none'; } }

    document.addEventListener('DOMContentLoaded', function () {
        const root = qs('#omsarSurveyWizard');
        if (!root) return;

        const formStep = qs('#omsarSurveyFormStep', root);
        const surveyForm = qs('#omsarSurveyForm', root);
        const formMessage = qs('#omsarSurveyFormMessage', root);
        const stepperItems = qsa('.omsar-stepper-item', root);

        const config = typeof omsarCitizenSurvey !== 'undefined' ? omsarCitizenSurvey : { ajaxUrl: '' };
        const labels = typeof omsarCitizenSurveyLabels !== 'undefined' ? omsarCitizenSurveyLabels : {};

        const state = {
            currentStep: 'step-intro',
            history: ['step-intro'],
        };

        function progressIndex(stepId) {
            const order = ['step-intro', 'step-1', 'step-2', 'step-3'];
            return order.indexOf(stepId) + 1;
        }

        function updateStepper(stepId) {
            const active = progressIndex(stepId);
            stepperItems.forEach(function (item) {
                const idx = parseInt(item.getAttribute('data-progress-step') || '0', 10);
                item.classList.remove('is-active', 'is-complete');
                item.removeAttribute('aria-current');
                if (!idx) return;
                if (idx < active) item.classList.add('is-complete');
                if (idx === active) {
                    item.classList.add('is-active');
                    item.setAttribute('aria-current', 'step');
                }
            });
        }

        const fieldRequiredMsg = (labels && labels.thisFieldRequired) ? labels.thisFieldRequired : 'This field is required.';

        function setMessage(el, msg, isError, skipScroll) {
            if (!el) return;
            el.textContent = msg || '';
            el.className = 'omsar-survey-' + (isError ? 'error' : 'message') + ' mb-3';
            if (msg) {
                show(el);
                if (!skipScroll) el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                hide(el);
            }
        }

        function getOrCreateFieldError(container) {
            if (!container) return null;
            var el = qs('.omsar-field-error', container);
            if (el) return el;
            el = document.createElement('div');
            el.className = 'omsar-field-error';
            el.setAttribute('role', 'alert');
            el.style.cssText = 'color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem; display: none;';
            container.appendChild(el);
            return el;
        }

        function showFieldError(container, text) {
            var el = getOrCreateFieldError(container);
            if (el) { el.textContent = text || fieldRequiredMsg; el.style.display = ''; el.hidden = false; }
        }

        function hideFieldError(container) {
            var el = container ? qs('.omsar-field-error', container) : null;
            if (el) { el.textContent = ''; el.style.display = 'none'; el.hidden = true; }
        }

        function setLoading(btn, loading) {
            if (!btn) return;
            btn.disabled = loading;
            btn.dataset.originalText = btn.dataset.originalText || btn.textContent;
            btn.textContent = loading ? (labels.loading || 'Loading...') : btn.dataset.originalText;
        }

        function showStep(stepId, pushHistory) {
            const steps = qsa('.omsar-step', formStep);
            steps.forEach(function (s) {
                if (s.getAttribute('data-step') === stepId) show(s);
                else hide(s);
            });
            state.currentStep = stepId;
            if (pushHistory) {
                const last = state.history[state.history.length - 1];
                if (last !== stepId) state.history.push(stepId);
            }
            setMessage(formMessage, '', false);
            updateStepper(stepId);
            window.scrollTo({ top: 0, behavior: 'smooth' });
            
            // Re-initialize ranking inputs visibility when step is shown
            const currentStepEl = qs('[data-step="' + stepId + '"]', formStep);
            if (currentStepEl) {
                initRankingInputs(currentStepEl);
            }
            updateStepQuestionNumbers(stepId);
        }

        function isElementVisible(el, withinRoot) {
            if (!el || !withinRoot) return false;
            var node = el;
            while (node && node !== withinRoot) {
                var style = window.getComputedStyle(node);
                if (style.display === 'none' || style.visibility === 'hidden') return false;
                node = node.parentElement;
            }
            return true;
        }

        function updateStepQuestionNumbers(stepId) {
            var stepEl = qs('[data-step="' + stepId + '"]', formStep);
            if (!stepEl) return;
            var groups = qsa('.omsar-form-group', stepEl);
            var n = 0;
            groups.forEach(function (g) {
                var numSpan = qs('.omsar-q-num', g);
                if (!numSpan) return;
                if (!isElementVisible(g, stepEl)) return;
                n += 1;
                numSpan.textContent = n + '.';
            });
        }

        function validateRankingGroup(groupEl) {
            if (!groupEl) return true;
            const max = parseInt(groupEl.getAttribute('data-ranking-max') || '3', 10);
            const cbs = qsa('.omsar-ranking-cb', groupEl);
            const checked = cbs.filter(function (cb) { return cb.checked; });
            if (checked.length !== max) return false;
            const values = [];
            checked.forEach(function (cb) {
                const forId = cb.value;
                const numInput = qs('[data-ranking-for="' + forId + '"]', groupEl);
                if (numInput && numInput.value) {
                    const n = parseInt(numInput.value, 10);
                    if (n >= 1 && n <= max) values.push(n);
                }
            });
            if (values.length !== max) return false;
            const unique = values.slice().filter(function (v, i, a) { return a.indexOf(v) === i; });
            return unique.length === max;
        }

        function isFieldVisible(field) {
            if (!field) return false;
            // Check if field itself is hidden
            if (field.hidden || field.type === 'hidden') return false;
            // Check computed style
            var fieldStyle = window.getComputedStyle(field);
            if (fieldStyle.display === 'none' || fieldStyle.visibility === 'hidden' || field.offsetParent === null) return false;
            // Check all parent elements
            var parent = field.parentElement;
            while (parent && parent !== root && parent !== document.body) {
                if (parent.hidden) return false;
                var parentStyle = window.getComputedStyle(parent);
                if (parentStyle.display === 'none' || parentStyle.visibility === 'hidden') return false;
                parent = parent.parentElement;
            }
            return true;
        }

        function validateStep(stepId, opts) {
            var scrollToFirstInvalid = true;
            var setErrors = true;
            if (opts !== undefined && opts !== null) {
                if (typeof opts === 'boolean') {
                    scrollToFirstInvalid = opts;
                } else {
                    scrollToFirstInvalid = opts.scrollToFirstInvalid !== false;
                    setErrors = opts.setErrors !== false;
                }
            }
            const stepEl = qs('[data-step="' + stepId + '"]', formStep);
            if (!stepEl) return true;

            var isValid = true;
            var scope = root || document;

            if (setErrors) {
                qsa('.omsar-field-error', stepEl).forEach(function (el) { el.textContent = ''; el.style.display = 'none'; el.hidden = true; });
            }

            if (stepId === 'step-1') {
                if (setErrors) {
                    qsa('.is-invalid', stepEl).forEach(function (el) { el.classList.remove('is-invalid'); });
                    qsa('.omsar-survey-error', stepEl).forEach(function (el) { hide(el); });
                }

                var q1Group = qs('#q1_valid', stepEl) && qs('#q1_valid', stepEl).closest('.omsar-form-group');
                var q1Checked = qsa('input[name="q1_institutions[]"]:checked', scope).filter(function(cb) { return isFieldVisible(cb); });
                if (!q1Checked.length) {
                    isValid = false;
                    if (setErrors) {
                        var q1ValidEl = qs('#q1_valid', stepEl);
                        if (q1ValidEl) q1ValidEl.classList.add('is-invalid');
                        qsa('input[name="q1_institutions[]"]', stepEl).forEach(function (cb) { if (isFieldVisible(cb)) cb.classList.add('is-invalid'); });
                        if (q1Group) showFieldError(q1Group, fieldRequiredMsg);
                    }
                } else {
                    var q1ValidEl = qs('#q1_valid', stepEl);
                    if (q1ValidEl) { q1ValidEl.value = '1'; q1ValidEl.classList.remove('is-invalid'); }
                    qsa('input[name="q1_institutions[]"]', stepEl).forEach(function (cb) { cb.classList.remove('is-invalid'); });
                    if (q1Group) hideFieldError(q1Group);
                }

                if (citizenQ2Q5 && isFieldVisible(citizenQ2Q5)) {
                    var q2Group = qs('input[name="q2_satisfaction"]', stepEl) && qs('input[name="q2_satisfaction"]', stepEl).closest('.omsar-form-group');
                    var q2Radios = qsa('input[name="q2_satisfaction"]', scope).filter(function(r) { return isFieldVisible(r); });
                    var q2Checked = q2Radios.find(function(r) { return r.checked; });
                    if (!q2Checked) {
                        isValid = false;
                        if (setErrors) {
                            qsa('input[name="q2_satisfaction"]', stepEl).forEach(function (r) { if (isFieldVisible(r)) r.classList.add('is-invalid'); });
                            if (q2Group) showFieldError(q2Group, fieldRequiredMsg);
                        }
                    } else {
                        qsa('input[name="q2_satisfaction"]', stepEl).forEach(function (r) { r.classList.remove('is-invalid'); });
                        if (q2Group) hideFieldError(q2Group);
                    }

                    var q3Group = qs('.omsar-q3-matrix-wrap', stepEl) && qs('.omsar-q3-matrix-wrap', stepEl).closest('.omsar-form-group');
                    var q3AllValid = true;
                    for (var r = 0; r < 6; r++) {
                        var rowRadios = qsa('input[name="q3_matrix[' + r + '][selected_value]"]', scope).filter(function(radio) { return isFieldVisible(radio); });
                        var rowChecked = rowRadios.find(function(radio) { return radio.checked; });
                        if (!rowChecked) {
                            isValid = false;
                            q3AllValid = false;
                            if (setErrors) {
                                qsa('input[name="q3_matrix[' + r + '][selected_value]"]', stepEl).forEach(function (radio) { if (isFieldVisible(radio)) radio.classList.add('is-invalid'); });
                            }
                        } else {
                            qsa('input[name="q3_matrix[' + r + '][selected_value]"]', stepEl).forEach(function (radio) { radio.classList.remove('is-invalid'); });
                        }
                    }
                    if (q3Group) { if (q3AllValid) hideFieldError(q3Group); else if (setErrors) showFieldError(q3Group, fieldRequiredMsg); }
                }
            } else {
                const requiredFields = qsa('[data-required="1"]', stepEl);
                const seenRadioGroups = {};
                requiredFields.forEach(function (field) {
                    if (!isFieldVisible(field)) return;
                    var container = field.closest('.omsar-form-group');
                    var value;
                    if (field.type === 'radio') {
                        if (!seenRadioGroups[field.name]) {
                            var radios = qsa('input[name="' + field.name + '"]', scope);
                            var checkedVisible = null;
                            for (var i = 0; i < radios.length; i++) {
                                if (radios[i].checked && isFieldVisible(radios[i])) {
                                    checkedVisible = radios[i];
                                    break;
                                }
                            }
                            seenRadioGroups[field.name] = checkedVisible ? checkedVisible.value : '';
                        }
                        value = seenRadioGroups[field.name];
                    } else if (field.type === 'checkbox' && field.name && field.name.indexOf('q1_institutions') !== -1) {
                        value = qsa('input[name="q1_institutions[]"]:checked', scope).length > 0 ? '1' : '';
                    } else {
                        value = field.value ? field.value.trim() : '';
                    }
                    var conditionalAttr = field.getAttribute('data-required-conditional');
                    if (conditionalAttr) {
                        var conditionalField = qs('input[name="' + conditionalAttr + '"]:checked', scope) || qs('[name="' + conditionalAttr + '"]', scope);
                        var conditionalValue = conditionalField ? conditionalField.value : '';
                        if (conditionalValue && !value) {
                            isValid = false;
                            if (setErrors) {
                                field.classList.add('is-invalid');
                                if (container) showFieldError(container, fieldRequiredMsg);
                            }
                        } else {
                            field.classList.remove('is-invalid');
                            if (container) hideFieldError(container);
                        }
                    } else {
                        if (!value) {
                            isValid = false;
                            if (setErrors) {
                                field.classList.add('is-invalid');
                                if (container) showFieldError(container, fieldRequiredMsg);
                            }
                        } else {
                            field.classList.remove('is-invalid');
                            if (container) hideFieldError(container);
                        }
                    }
                });
            }

            const rankingGroups = qsa('.omsar-ranking-group', stepEl);
            rankingGroups.forEach(function (group) {
                if (!isFieldVisible(group)) return;
                if (!validateRankingGroup(group)) {
                    isValid = false;
                    if (setErrors) {
                        const errEl = qs('.omsar-survey-error', group);
                        if (errEl) {
                            errEl.textContent = labels.rankingError || 'Please select exactly 3 options and assign unique rankings from 1 to 3.';
                            show(errEl);
                        }
                        qsa('.omsar-ranking-num', group).forEach(function (n) { n.classList.add('is-invalid'); });
                    }
                } else {
                    const errEl = qs('.omsar-survey-error', group);
                    if (errEl) hide(errEl);
                    qsa('.omsar-ranking-num', group).forEach(function (n) { n.classList.remove('is-invalid'); });
                }
            });

            if (!isValid && scrollToFirstInvalid) {
                const firstInvalid = qs('.is-invalid', stepEl);
                if (firstInvalid) firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return isValid;
        }

        function clearInputsIn(el) {
            if (!el) return;
            qsa('input, select, textarea', el).forEach(function (inp) {
                if (inp.type === 'radio' || inp.type === 'checkbox') inp.checked = false;
                else {
                    inp.value = '';
                    if (inp.tagName === 'SELECT' && typeof jQuery !== 'undefined' && jQuery.fn.select2 && jQuery(inp).hasClass('select2-hidden-accessible')) {
                        jQuery(inp).trigger('change');
                    }
                }
            });
        }

        function getSelect2Options() {
            var dir = document.documentElement.getAttribute('dir') || document.body.getAttribute('dir') || 'ltr';
            var searchPlaceholder = (labels && labels.searchPlaceholder) ? labels.searchPlaceholder : 'Search...';
            return {
                width: '100%',
                placeholder: searchPlaceholder,
                allowClear: true,
                language: {
                    noResults: function () { return ''; },
                    searching: function () { return ''; },
                    inputTooShort: function () { return ''; },
                    inputTooLong: function () { return ''; },
                    errorLoading: function () { return ''; },
                    loadingMore: function () { return ''; }
                },
                dir: dir
            };
        }

        function initCitizenSurveySelect2(el) {
            if (typeof jQuery === 'undefined' || typeof jQuery.fn.select2 === 'undefined') return;
            var $el = jQuery(el);
            if ($el.length === 0 || $el.hasClass('select2-hidden-accessible')) return;
            $el.select2(getSelect2Options());
        }

        // Conditional visibility: Q1 → Q2–Q5; Q9 → Q10; Q10 → Q11/Q12; Q17 → sector/status
        const citizenQ2Q5 = qs('#omsar_citizen_q2_q5_group', root);
        const citizenQ10 = qs('#omsar_citizen_q10_group', root);
        const citizenQ11 = qs('#omsar_citizen_q11_group', root);
        const citizenQ12 = qs('#omsar_citizen_q12_group', root);
        const citizenQ17Yes = qs('#omsar_citizen_q17_yes_group', root);
        const citizenQ17No = qs('#omsar_citizen_q17_no_group', root);
        const q1ValidInput = qs('#q1_valid', root);

        if (surveyForm && citizenQ2Q5) {
            qsa('input[name="q1_institutions[]"]', surveyForm).forEach(function (cb) {
                cb.addEventListener('change', function () {
                    const isNotAny = this.value === 'not_any';
                    if (isNotAny && this.checked) {
                        // If "not_any" is checked, uncheck all other Q1 checkboxes
                        qsa('input[name="q1_institutions[]"]', surveyForm).forEach(function (other) {
                            if (other !== cb && other.value !== 'not_any') {
                                other.checked = false;
                            }
                        });
                    } else if (!isNotAny && this.checked) {
                        // If any other option is checked, uncheck "not_any"
                        const notAnyCb = qs('input[name="q1_institutions[]"][value="not_any"]', surveyForm);
                        if (notAnyCb) notAnyCb.checked = false;
                    }
                    const anyChecked = qsa('input[name="q1_institutions[]"]:checked', surveyForm).length > 0;
                    if (q1ValidInput) q1ValidInput.value = anyChecked ? '1' : '';
                    const notAnyChecked = qs('input[name="q1_institutions[]"][value="not_any"]:checked', surveyForm);
                    if (notAnyChecked) {
                        hide(citizenQ2Q5);
                        clearInputsIn(citizenQ2Q5);
                        // Remove required attributes and invalid states from all fields in the hidden group
                        qsa('[data-required="1"]', citizenQ2Q5).forEach(function (f) { 
                            f.removeAttribute('data-required'); 
                            f.removeAttribute('required'); 
                            f.classList.remove('is-invalid'); 
                        });
                        // Also clear invalid states from ranking groups and their error messages
                        qsa('.omsar-ranking-group', citizenQ2Q5).forEach(function (group) {
                            group.classList.remove('is-invalid');
                            qsa('.omsar-survey-error', group).forEach(function (err) { hide(err); });
                            qsa('.omsar-ranking-num', group).forEach(function (n) { n.classList.remove('is-invalid'); });
                        });
                    } else {
                        show(citizenQ2Q5);
                        qsa('.omsar-form-group input, .omsar-form-group select, .omsar-form-group textarea', citizenQ2Q5).forEach(function (f) {
                            if (f.name && (f.name.indexOf('q2_') === 0 || f.name.indexOf('q3_') === 0 || f.name.indexOf('q4_') === 0 || f.name.indexOf('q5_') === 0)) {
                                f.setAttribute('data-required', '1');
                                if (f.type !== 'checkbox') f.setAttribute('required', 'required');
                            }
                        });
                    }
                    updateStepQuestionNumbers('step-1');
                });
            });
        }
        if (surveyForm && citizenQ10) {
            // Initialize Q9-related groups - ensure all are hidden by default
            if (citizenQ10) hide(citizenQ10);
            if (citizenQ11) hide(citizenQ11);
            if (citizenQ12) hide(citizenQ12);
            
            // Function to update Q9 groups visibility
            function updateQ9Groups() {
                const selectedValue = qs('input[name="q9_nationality"]:checked', surveyForm);
                if (!selectedValue) {
                    // No value selected - hide all Q9-related groups
                    if (citizenQ10) { hide(citizenQ10); clearInputsIn(citizenQ10); }
                    if (citizenQ11) { hide(citizenQ11); clearInputsIn(citizenQ11); }
                    if (citizenQ12) { hide(citizenQ12); clearInputsIn(citizenQ12); }
                } else if (selectedValue.value === 'lebanese') {
                    show(citizenQ10);
                    // Also update Q10/Q11/Q12 based on Q10 selection
                    updateQ10Groups();
                } else {
                    // "other" nationality - hide all groups
                    if (citizenQ10) { hide(citizenQ10); clearInputsIn(citizenQ10); }
                    if (citizenQ11) { hide(citizenQ11); clearInputsIn(citizenQ11); }
                    if (citizenQ12) { hide(citizenQ12); clearInputsIn(citizenQ12); }
                }
                updateStepQuestionNumbers('step-3');
            }
            
            // Function to update Q10/Q11/Q12 groups visibility
            function updateQ10Groups() {
                const q10Selected = qs('input[name="q10_residence"]:checked', surveyForm);
                if (!q10Selected) {
                    // No Q10 value selected - hide Q11 and Q12
                    if (citizenQ11) { hide(citizenQ11); clearInputsIn(citizenQ11); }
                    if (citizenQ12) { hide(citizenQ12); clearInputsIn(citizenQ12); }
                } else if (q10Selected.value === 'lebanon') {
                    if (citizenQ11) show(citizenQ11);
                    if (citizenQ12) { hide(citizenQ12); clearInputsIn(citizenQ12); }
                } else if (q10Selected.value === 'abroad') {
                    if (citizenQ12) show(citizenQ12);
                    if (citizenQ11) { hide(citizenQ11); clearInputsIn(citizenQ11); }
                }
                updateStepQuestionNumbers('step-3');
            }
            
            // Check initial state on page load
            updateQ9Groups();
            
            // Handle Q9 radio button changes
            qsa('input[name="q9_nationality"]', surveyForm).forEach(function (r) {
                r.addEventListener('change', function () {
                    updateQ9Groups();
                });
            });
            
            // Handle Q10 radio button changes
            qsa('input[name="q10_residence"]', surveyForm).forEach(function (r) {
                r.addEventListener('change', function () {
                    updateQ10Groups();
                });
            });
            // Initialize Q17 groups - ensure both are hidden by default
            if (citizenQ17Yes) hide(citizenQ17Yes);
            if (citizenQ17No) hide(citizenQ17No);
            
            // Function to update Q17 groups visibility
            function updateQ17Groups() {
                const selectedValue = qs('input[name="q17_employed"]:checked', surveyForm);
                if (!selectedValue) {
                    // No value selected - hide both groups
                    if (citizenQ17Yes) { hide(citizenQ17Yes); clearInputsIn(citizenQ17Yes); }
                    if (citizenQ17No) { hide(citizenQ17No); clearInputsIn(citizenQ17No); }
                } else if (selectedValue.value === 'yes') {
                    if (citizenQ17Yes) show(citizenQ17Yes);
                    if (citizenQ17No) { hide(citizenQ17No); clearInputsIn(citizenQ17No); }
                } else if (selectedValue.value === 'no') {
                    if (citizenQ17No) show(citizenQ17No);
                    if (citizenQ17Yes) { hide(citizenQ17Yes); clearInputsIn(citizenQ17Yes); }
                }
                updateStepQuestionNumbers('step-3');
            }
            
            // Check initial state on page load
            updateQ17Groups();
            
            // Handle Q17 radio button changes
            qsa('input[name="q17_employed"]', surveyForm).forEach(function (r) {
                r.addEventListener('change', function () {
                    updateQ17Groups();
                });
            });
        }

        // Validate ranking number input value
        function validateRankingNumber(numInput) {
            const value = numInput.value.trim();
            const numValue = parseInt(value, 10);
            const isValid = value === '' || (numValue >= 1 && numValue <= 3);
            
            // Get or create error message element
            let errorMsg = numInput.parentElement.querySelector('.omsar-ranking-error');
            if (!errorMsg) {
                errorMsg = document.createElement('div');
                errorMsg.className = 'omsar-ranking-error';
                errorMsg.style.cssText = 'color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem; display: none;';
                numInput.parentElement.appendChild(errorMsg);
            }
            
            if (!isValid && value !== '') {
                numInput.classList.add('is-invalid');
                errorMsg.textContent = labels.rankingNumberError || 'Please enter a number between 1 and 3.';
                errorMsg.style.display = 'block';
            } else {
                numInput.classList.remove('is-invalid');
                errorMsg.style.display = 'none';
            }
            
            return isValid;
        }

        // Ranking: one source of truth – sync checkboxes, ranks (by selection order), and disabled state
        function syncRankingGroup(group) {
            if (!group) return;
            const max = parseInt(group.getAttribute('data-ranking-max') || '3', 10);
            const cbs = qsa('.omsar-ranking-cb', group);
            var order = (group.dataset.rankingOrder || '').split(',').filter(Boolean);
            var checkedValues = cbs.filter(function (cb) { return cb.checked; }).map(function (cb) { return cb.value; });
            order = order.filter(function (v) { return checkedValues.indexOf(v) !== -1; });
            cbs.forEach(function (cb) {
                if (cb.checked && order.indexOf(cb.value) === -1) order.push(cb.value);
            });
            group.dataset.rankingOrder = order.join(',');
            var atLimit = order.length >= max;

            cbs.forEach(function (cb) {
                const numInput = qs('.omsar-ranking-num[data-ranking-for="' + cb.value + '"]', group);
                if (!numInput) return;

                if (cb.checked) {
                    const rank = order.indexOf(cb.value) + 1;
                    numInput.value = String(rank);
                    numInput.style.setProperty('display', 'block', 'important');
                    numInput.style.visibility = 'visible';
                    numInput.removeAttribute('hidden');
                    numInput.classList.remove('is-invalid');
                    cb.disabled = false;
                    var err = numInput.parentElement.querySelector('.omsar-ranking-error');
                    if (err) err.style.display = 'none';
                } else {
                    numInput.value = '';
                    numInput.style.setProperty('display', 'none', 'important');
                    numInput.style.visibility = 'hidden';
                    numInput.setAttribute('hidden', 'hidden');
                    numInput.classList.remove('is-invalid');
                    cb.disabled = atLimit;
                    var err = numInput.parentElement.querySelector('.omsar-ranking-error');
                    if (err) err.style.display = 'none';
                }
            });
        }

        function initRankingInputs(scope) {
            const searchScope = scope || root;
            const rankingGroups = qsa('.omsar-ranking-group', searchScope);
            rankingGroups.forEach(function (group) {
                syncRankingGroup(group);
                if (group.dataset.rankingDelegate === 'true') return;
                group.dataset.rankingDelegate = 'true';
                group.addEventListener('change', function (e) {
                    if (e.target && e.target.classList && e.target.classList.contains('omsar-ranking-cb')) {
                        syncRankingGroup(group);
                    }
                });
            });
        }
        
        // Initialize ranking inputs on page load
        initRankingInputs();

        // Select2 on Governorate, Region abroad, Annual income (searchable dropdowns; fixes macOS native select overlap)
        ['q11_governorate', 'q12_region_abroad', 'q16_income'].forEach(function (id) {
            var sel = qs('#' + id, root);
            if (sel) initCitizenSurveySelect2(sel);
        });

        // Show form step directly on page load
        if (formStep) {
            show(formStep);
            showStep('step-intro', true);
        }

        // Step navigation
        if (surveyForm) {
            surveyForm.addEventListener('click', function (e) {
                const btn = e.target.closest('[data-action]');
                if (!btn) return;
                e.preventDefault();
                const action = btn.getAttribute('data-action');

                if (action === 'next') {
                    if (!validateStep(state.currentStep)) {
                        setMessage(formMessage, labels.fillRequiredFields || 'Please fill in all required fields.', true, true);
                        var stepEl = qs('[data-step="' + state.currentStep + '"]', formStep);
                        var firstInvalid = stepEl ? qs('.is-invalid', stepEl) : null;
                        if (firstInvalid) firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return;
                    }
                    const nextSteps = { 'step-intro': 'step-1', 'step-1': 'step-2', 'step-2': 'step-3' };
                    const next = nextSteps[state.currentStep];
                    if (next) showStep(next, true);
                } else if (action === 'back') {
                    state.history.pop();
                    const prev = state.history[state.history.length - 1] || 'step-intro';
                    showStep(prev, false);
                } else if (action === 'submit') {
                    if (!validateStep(state.currentStep)) {
                        setMessage(formMessage, labels.fillRequiredFields || 'Please fill in all required fields.', true, true);
                        var stepEl = qs('[data-step="' + state.currentStep + '"]', formStep);
                        var firstInvalid = stepEl ? qs('.is-invalid', stepEl) : null;
                        if (firstInvalid) firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return;
                    }
                    const submitBtn = qs('[data-action="submit"]', surveyForm);
                    setLoading(submitBtn, true);
                    const formData = new FormData(surveyForm);
                    formData.append('action', 'omsar_submit_citizen_survey_form');
                    formData.append('nonce', qs('[name="omsar_survey_form_nonce"]', surveyForm).value);
                    fetch(config.ajaxUrl, {
                        method: 'POST',
                        body: formData,
                        credentials: 'same-origin'
                    }).then(function (r) { return r.json(); }).then(function (res) {
                        setLoading(submitBtn, false);
                        if (res.success) {
                            var successUrl = (config && config.successPageUrl) ? config.successPageUrl : '';
                            if (successUrl) {
                                window.location.href = successUrl;
                                return;
                            }
                            setMessage(formMessage, (res.data && res.data.message) ? res.data.message : (labels.success || 'Thank you for completing the survey.'), false);
                            surveyForm.reset();
                            if (q1ValidInput) q1ValidInput.value = '';
                            setTimeout(function () {
                                state.currentStep = 'step-intro';
                                state.history = ['step-intro'];
                                showStep('step-intro', false);
                            }, 3000);
                        } else {
                            setMessage(formMessage, (res.data && res.data.message) ? res.data.message : (labels.error || 'An error occurred. Please try again.'), true);
                        }
                    }).catch(function () {
                        setLoading(submitBtn, false);
                        setMessage(formMessage, labels.error || 'An error occurred. Please try again.', true);
                    });
                }
            });

            // Live validation: only clear error and red border when user corrects a field (do not show new errors)
            var liveValidateTimer = null;
            function runLiveValidate() {
                if (state.currentStep === 'step-intro') return;
                validateStep(state.currentStep, { scrollToFirstInvalid: false, setErrors: false });
            }
            if (surveyForm) {
                surveyForm.addEventListener('change', function () {
                    runLiveValidate();
                });
                surveyForm.addEventListener('input', function () {
                    if (liveValidateTimer) clearTimeout(liveValidateTimer);
                    liveValidateTimer = setTimeout(runLiveValidate, 300);
                });
            }
        }
    });
})();
