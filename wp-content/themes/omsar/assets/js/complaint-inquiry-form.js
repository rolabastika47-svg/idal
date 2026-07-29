/**
 * Complaint / Inquiry Submission Form (stepped wizard)
 */
(function () {
    'use strict';

    function qs(sel, root) { return (root || document).querySelector(sel); }
    function qsa(sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); }

    function show(el) { if (el) { el.hidden = false; el.style.display = ''; } }
    function hide(el) { if (el) { el.hidden = true; el.style.display = 'none'; } }

    function getWordsCount(value) {
        const text = (value || '').trim().replace(/\s+/g, ' ');
        if (!text) return 0;
        return text.split(' ').length;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const root = qs('#omsarComplaintWizard');
        if (!root) return;

        const form = qs('#omsarComplaintForm', root);
        const steps = qsa('.omsar-step', root);
        const messageBox = qs('#omsarComplaintMessage', root);
        const stepperItems = qsa('.omsar-stepper-item', root);
        const step6El = qs('[data-step="step-6"]', root);
        const step6Stepper = qs('.omsar-stepper-item[data-progress-step="6"]', root);

        const state = {
            currentStep: 'step-1',
            history: ['step-1'],
        };

        function stepIndex(stepId) {
            const order = ['step-1','step-2','step-3','step-4','step-5','step-6','step-7'];
            const i = order.indexOf(stepId);
            return i >= 0 ? i + 1 : 1;
        }

        function progressIndex(stepId) {
            const order = ['step-1','step-2','step-3','step-4','step-5','step-6','step-7'];
            return order.indexOf(stepId) + 1;
        }

        function updateStepper(stepId) {
            const active = progressIndex(stepId);
            const issueType = getValue('issue_type');
            const useSeash = (issueType === 'sea_sh');

            stepperItems.forEach(function (item) {
                const idx = parseInt(item.getAttribute('data-progress-step') || '0', 10);
                item.classList.remove('is-active', 'is-complete');
                item.removeAttribute('aria-current');
                if (idx === 6) {
                    if (useSeash) {
                        item.classList.remove('omsar-stepper-item--hidden');
                    } else {
                        item.classList.add('omsar-stepper-item--hidden');
                    }
                }
                if (!idx) return;
                if (idx === 6 && !useSeash) return;
                if (idx < active) item.classList.add('is-complete');
                if (idx === active) {
                    item.classList.add('is-active');
                    item.setAttribute('aria-current', 'step');
                }
            });
        }

        function setMessage(type, text) {
            if (!messageBox) return;
            messageBox.className = 'omsar-complaint-message ' + (type ? ('omsar-complaint-message-' + type) : '');
            messageBox.textContent = text || '';
            if (text) {
                show(messageBox);
                messageBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                hide(messageBox);
            }
        }

        function getValue(name) {
            const el = qs('[name="' + name + '"]', form);
            if (!el) return '';
            if (el.type === 'radio') {
                const checked = qs('[name="' + name + '"]:checked', form);
                return checked ? checked.value : '';
            }
            return el.value || '';
        }

        function showStep(stepId, pushHistory, clearMsg) {
            steps.forEach(function (s) {
                if (s.getAttribute('data-step') === stepId) show(s);
                else hide(s);
            });
            state.currentStep = stepId;
            if (pushHistory) {
                const last = state.history[state.history.length - 1];
                if (last !== stepId) state.history.push(stepId);
            }
            if (clearMsg !== false) setMessage('', '');
            updateStep7Title();
            updateStepper(stepId);

            var seashNote = qs('#seash_consent_note', root);
            if (seashNote) {
                if (getValue('issue_type') === 'sea_sh') show(seashNote);
                else hide(seashNote);
            }

            // Scroll to top of page when step changes
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function updateStep7Title() {
            var issueType = getValue('issue_type');
            var useSeash = (issueType === 'sea_sh');
            var step7Title = qs('[data-step="step-7"] .omsar-step-title', root);
            if (step7Title) {
                // Use the pre-translated strings from data attributes
                var titleStep5 = step7Title.getAttribute('data-title-step5') || '5. Confidentiality and Consent';
                var titleStep6 = step7Title.getAttribute('data-title-step6') || '6. Confidentiality and Consent';
                step7Title.textContent = useSeash ? titleStep6 : titleStep5;
            }
        }

        function toggleConditionalSections() {
            var issueType = getValue('issue_type');
            var onBehalf = getValue('on_behalf');
            var onWrap = qs('#on_behalf_explanation_wrap', root);
            var otherWrap = qs('#issue_type_other_wrap', root);

            if (onWrap) {
                if (onBehalf === 'yes') {
                    show(onWrap);
                } else {
                    hide(onWrap);
                    // Clear the explanation field and remove validation errors when hidden
                    var explanationEl = qs('#on_behalf_explanation', form);
                    if (explanationEl) {
                        explanationEl.value = '';
                        explanationEl.classList.remove('is-invalid');
                    }
                }
            }
            if (otherWrap) {
                if (issueType === 'other') {
                    show(otherWrap);
                } else {
                    hide(otherWrap);
                    var inp = qs('#issue_type_other_input', form);
                    if (inp) {
                        inp.value = '';
                        inp.classList.remove('is-invalid');
                    }
                }
            }
            updateStep7Title();
            updateStepper(state.currentStep);
        }

        function validateVisibleStep() {
            var visible = qs('.omsar-step:not([hidden])', root);
            if (!visible) return { valid: true, message: '' };

            qsa('.is-invalid', root).forEach(function (el) { el.classList.remove('is-invalid'); });

            var required = qsa('[data-required="1"]', visible);
            var missing = [];
            for (var i = 0; i < required.length; i++) {
                var el = required[i];
                var name = el.getAttribute('name');
                if (!name) continue;
                var ok = false;
                if (el.type === 'radio') {
                    var ch = qs('[name="' + name + '"]:checked', visible);
                    ok = !!ch;
                    if (!ok) qsa('[name="' + name + '"]', visible).forEach(function (r) { r.classList.add('is-invalid'); });
                } else {
                    ok = (el.value || '').trim() !== '';
                    if (!ok) el.classList.add('is-invalid');
                }
                if (!ok) missing.push(el);
            }

            var stepId = visible.getAttribute('data-step');
            var emailEl = qs('[name="email"]', form);
            if (emailEl && (emailEl.value || '').trim() !== '') {
                var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!re.test(emailEl.value.trim())) {
                    emailEl.classList.add('is-invalid');
                    return { valid: false, message: (window.omsarComplaintForm && window.omsarComplaintForm.invalidEmailError) || 'Please enter a valid email address.' };
                }
            }

            // Validate conditional required fields
            if (stepId === 'step-2') {
                var onBehalf = getValue('on_behalf');
                var onBehalfExplanationEl = qs('#on_behalf_explanation', form);
                // Explanation is required only if on_behalf is "yes"
                if (onBehalf === 'yes' && onBehalfExplanationEl && onBehalfExplanationEl.offsetParent) {
                    if ((onBehalfExplanationEl.value || '').trim() === '') {
                        onBehalfExplanationEl.classList.add('is-invalid');
                        missing.push(onBehalfExplanationEl);
                    }
                }
            }
            
            // Check conditional required fields using data-required-conditional attribute
            qsa('[data-required-conditional]', visible).forEach(function (el) {
                var conditionField = el.getAttribute('data-required-conditional');
                var conditionValue = getValue(conditionField);
                // For on_behalf, check if value is "yes"
                // For issue_type, check if value is "other"
                var shouldBeRequired = false;
                if (conditionField === 'on_behalf' && conditionValue === 'yes') {
                    shouldBeRequired = true;
                } else if (conditionField === 'issue_type' && conditionValue === 'other') {
                    shouldBeRequired = true;
                }
                
                if (shouldBeRequired && el.offsetParent) {
                    if ((el.value || '').trim() === '') {
                        el.classList.add('is-invalid');
                        missing.push(el);
                    }
                }
            });

            if (stepId === 'step-6') {
                var anon = qs('[name="sea_sh_anonymous"]:checked', form);
                var ref = qs('[name="sea_sh_referral"]:checked', form);
                if (!anon) {
                    qsa('[name="sea_sh_anonymous"]', form).forEach(function (r) { r.classList.add('is-invalid'); });
                    missing.push(1);
                }
                if (!ref) {
                    qsa('[name="sea_sh_referral"]', form).forEach(function (r) { r.classList.add('is-invalid'); });
                    missing.push(1);
                }
            }

            qsa('textarea[data-max-words]', visible).forEach(function (ta) {
                var max = parseInt(ta.getAttribute('data-max-words'), 10) || 200;
                if (getWordsCount(ta.value) > max) {
                    ta.classList.add('is-invalid');
                    return { valid: false, message: (window.omsarComplaintForm && window.omsarComplaintForm.wordLimitError) || 'Please limit your response to 200 words.' };
                }
            });

            if (missing.length) {
                return { valid: false, message: (window.omsarComplaintForm && window.omsarComplaintForm.requiredFieldsError) || 'Please fill in all required fields before continuing.' };
            }
            return { valid: true, message: '' };
        }

        function validateAll() {
            qsa('.is-invalid', root).forEach(function (el) { el.classList.remove('is-invalid'); });

            // Validate required fields
            var requiredFields = [
                { name: 'preferred_contact', step: 'step-2' },
                { name: 'on_behalf', step: 'step-2' },
                { name: 'inquiry_text', step: 'step-3' },
                { name: 'issue_type', step: 'step-4' },
                { name: 'complaint_text', step: 'step-5' },
                { name: 'consent_followup', step: 'step-7' }
            ];
            
            for (var i = 0; i < requiredFields.length; i++) {
                var field = requiredFields[i];
                var value = getValue(field.name);
                if (!value || value.trim() === '') {
                    var els = qsa('[name="' + field.name + '"]', form);
                    els.forEach(function (el) {
                        if (el.type === 'radio') {
                            el.classList.add('is-invalid');
                        } else {
                            el.classList.add('is-invalid');
                        }
                    });
                    return { valid: false, message: (window.omsarComplaintForm && window.omsarComplaintForm.requiredFieldsError) || 'Please fill in all required fields.', step: field.step };
                }
            }
            
            // Validate conditional required fields
            var onBehalf = getValue('on_behalf');
            // Explanation is required only if on_behalf is "yes"
            if (onBehalf === 'yes') {
                var onBehalfExplanationEl = qs('#on_behalf_explanation', form);
                if (onBehalfExplanationEl && (onBehalfExplanationEl.value || '').trim() === '') {
                    onBehalfExplanationEl.classList.add('is-invalid');
                    return { valid: false, message: (window.omsarComplaintForm && window.omsarComplaintForm.requiredFieldsError) || 'Please fill in all required fields.', step: 'step-2' };
                }
            }
            
            // Check all conditional required fields
            var conditionalFields = qsa('[data-required-conditional]', form);
            for (var c = 0; c < conditionalFields.length; c++) {
                var el = conditionalFields[c];
                var conditionField = el.getAttribute('data-required-conditional');
                var conditionValue = getValue(conditionField);
                if (conditionValue === 'yes' && el.offsetParent) {
                    if ((el.value || '').trim() === '') {
                        el.classList.add('is-invalid');
                        var step = el.closest('[data-step]');
                        return { valid: false, message: (window.omsarComplaintForm && window.omsarComplaintForm.requiredFieldsError) || 'Please fill in all required fields.', step: step ? step.getAttribute('data-step') : 'step-2' };
                    }
                }
            }
            
            // Validate conditional required fields
            var issueType = getValue('issue_type');
            // Issue type "other" input is required only if issue_type is "other"
            if (issueType === 'other') {
                var otherEl = qs('#issue_type_other_input', form);
                if (otherEl && (otherEl.value || '').trim() === '') {
                    otherEl.classList.add('is-invalid');
                    return { valid: false, message: (window.omsarComplaintForm && window.omsarComplaintForm.pleaseSpecifyError) || 'Please specify the issue type.', step: 'step-4' };
                }
            }
            
            // Check all conditional required fields using data-required-conditional attribute
            var conditionalFields = qsa('[data-required-conditional]', form);
            for (var c = 0; c < conditionalFields.length; c++) {
                var el = conditionalFields[c];
                var conditionField = el.getAttribute('data-required-conditional');
                var conditionValue = getValue(conditionField);
                var shouldBeRequired = false;
                
                // For on_behalf, check if value is "yes"
                if (conditionField === 'on_behalf' && conditionValue === 'yes') {
                    shouldBeRequired = true;
                }
                // For issue_type, check if value is "other"
                else if (conditionField === 'issue_type' && conditionValue === 'other') {
                    shouldBeRequired = true;
                }
                
                if (shouldBeRequired && el.offsetParent) {
                    if ((el.value || '').trim() === '') {
                        el.classList.add('is-invalid');
                        var step = el.closest('[data-step]');
                        var errorMsg = conditionField === 'issue_type' 
                            ? ((window.omsarComplaintForm && window.omsarComplaintForm.pleaseSpecifyError) || 'Please specify the issue type.')
                            : ((window.omsarComplaintForm && window.omsarComplaintForm.requiredFieldsError) || 'Please fill in all required fields.');
                        return { valid: false, message: errorMsg, step: step ? step.getAttribute('data-step') : 'step-2' };
                    }
                }
            }

            var consent = getValue('consent_followup');
            if (!consent || (consent !== 'yes' && consent !== 'no')) {
                qsa('[name="consent_followup"]', form).forEach(function (r) { r.classList.add('is-invalid'); });
                return { valid: false, message: (window.omsarComplaintForm && window.omsarComplaintForm.requiredFieldsError) || 'Please fill in all required fields.', step: 'step-7' };
            }

            var emailEl = qs('[name="email"]', form);
            if (emailEl && (emailEl.value || '').trim() !== '') {
                var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!re.test(emailEl.value.trim())) {
                    emailEl.classList.add('is-invalid');
                    return { valid: false, message: (window.omsarComplaintForm && window.omsarComplaintForm.invalidEmailError) || 'Please enter a valid email address.', step: 'step-2' };
                }
            }

            if (issueType === 'sea_sh') {
                var anon = qs('[name="sea_sh_anonymous"]:checked', form);
                var ref = qs('[name="sea_sh_referral"]:checked', form);
                if (!anon) {
                    qsa('[name="sea_sh_anonymous"]', form).forEach(function (r) { r.classList.add('is-invalid'); });
                    return { valid: false, message: 'Please select whether you would like to remain anonymous.', step: 'step-6' };
                }
                if (!ref) {
                    qsa('[name="sea_sh_referral"]', form).forEach(function (r) { r.classList.add('is-invalid'); });
                    return { valid: false, message: 'Please select your referral preference.', step: 'step-6' };
                }
            }

            var textareas = qsa('textarea[data-max-words]', form);
            for (var t = 0; t < textareas.length; t++) {
                var ta = textareas[t];
                var max = parseInt(ta.getAttribute('data-max-words'), 10) || 200;
                if (getWordsCount(ta.value) > max) {
                    ta.classList.add('is-invalid');
                    var step = ta.closest('[data-step]');
                    return { valid: false, message: (window.omsarComplaintForm && window.omsarComplaintForm.wordLimitError) || 'Please limit your response to 200 words.', step: step ? step.getAttribute('data-step') : 'step-3' };
                }
            }

            return { valid: true, message: '', step: null };
        }

        function nextStep() {
            var stepId = state.currentStep;
            var issueType = getValue('issue_type');

            if (stepId === 'step-1') return 'step-2';
            if (stepId === 'step-2') return 'step-3';
            if (stepId === 'step-3') return 'step-4';
            if (stepId === 'step-4') return 'step-5';
            if (stepId === 'step-5') return (issueType === 'sea_sh') ? 'step-6' : 'step-7';
            if (stepId === 'step-6') return 'step-7';
            return null;
        }

        function prevStep() {
            var stepId = state.currentStep;
            var issueType = getValue('issue_type');

            if (stepId === 'step-2') return 'step-1';
            if (stepId === 'step-3') return 'step-2';
            if (stepId === 'step-4') return 'step-3';
            if (stepId === 'step-5') return 'step-4';
            if (stepId === 'step-6') return 'step-5';
            if (stepId === 'step-7') return (issueType === 'sea_sh') ? 'step-6' : 'step-5';
            return null;
        }

        function goNext() {
            var v = validateVisibleStep();
            if (!v.valid) {
                setMessage('error', v.message);
                var first = qs('.is-invalid', root);
                if (first) {
                    first.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    setTimeout(function () { if (first.focus) first.focus(); }, 300);
                }
                return;
            }
            var next = nextStep();
            if (next) showStep(next, true, true);
        }

        function goBack() {
            if (state.history.length <= 1) return;
            state.history.pop();
            var prev = state.history[state.history.length - 1];
            showStep(prev, false, true);
        }

        qsa('[data-action="next"]', root).forEach(function (btn) {
            btn.addEventListener('click', function (e) { e.preventDefault(); goNext(); });
        });
        qsa('[data-action="back"]', root).forEach(function (btn) {
            btn.addEventListener('click', function (e) { e.preventDefault(); goBack(); });
        });

        qsa('textarea[data-max-words]', form).forEach(function (ta) {
            var counter = qs('[data-word-counter-for="' + ta.id + '"]', root);
            var max = parseInt(ta.getAttribute('data-max-words'), 10) || 200;
            function update() {
                var n = getWordsCount(ta.value);
                if (counter) counter.textContent = n + '/' + max;
                if (n > max) ta.classList.add('is-invalid');
                else ta.classList.remove('is-invalid');
            }
            ta.addEventListener('input', update);
            update();
        });

        qsa('[name="issue_type"]', root).forEach(function (el) {
            el.addEventListener('change', toggleConditionalSections);
        });
        qsa('[name="on_behalf"]', root).forEach(function (el) {
            el.addEventListener('change', toggleConditionalSections);
        });

        var submitButtons = qsa('[data-action="submit"]', root);
        submitButtons.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                setMessage('', '');
                var v = validateAll();
                if (!v.valid) {
                    setMessage('error', v.message);
                    if (v.step) showStep(v.step, false, false);
                    var first = qs('.is-invalid', root);
                    if (first) {
                        first.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        setTimeout(function () { if (first.focus) first.focus(); }, 300);
                    }
                    return;
                }

                if (typeof window.omsarComplaintForm === 'undefined') {
                    setMessage('error', 'Form not initialized.');
                    return;
                }

                // Store original button text and show loader
                var originalText = btn.textContent || btn.innerText;
                btn.disabled = true;
                btn.classList.add('loading');
                btn.setAttribute('data-original-text', originalText);

                var fd = new FormData(form);
                fd.append('action', 'submit_complaint_inquiry_form');
                fd.append('nonce', window.omsarComplaintForm.nonce);

                fetch(window.omsarComplaintForm.ajaxurl, { method: 'POST', body: fd })
                    .then(function (r) {
                        if (!r.ok) throw new Error('HTTP ' + r.status);
                        return r.json();
                    })
                    .then(function (data) {
                        btn.disabled = false;
                        btn.classList.remove('loading');
                        var originalText = btn.getAttribute('data-original-text');
                        if (originalText) {
                            btn.textContent = originalText;
                            btn.removeAttribute('data-original-text');
                        }
                        if (data && data.success) {
                            setMessage('success', (data.data && data.data.message) || window.omsarComplaintForm.successMessage);
                            form.reset();
                            state.history = ['step-1'];
                            showStep('step-1', false, false);
                            toggleConditionalSections();
                            qsa('textarea[data-max-words]', form).forEach(function (ta) {
                                var c = qs('[data-word-counter-for="' + ta.id + '"]', root);
                                if (c) c.textContent = '0/200';
                            });
                        } else {
                            setMessage('error', (data && data.data && data.data.message) || window.omsarComplaintForm.genericError);
                        }
                    })
                    .catch(function (err) {
                        btn.disabled = false;
                        btn.classList.remove('loading');
                        var originalText = btn.getAttribute('data-original-text');
                        if (originalText) {
                            btn.textContent = originalText;
                            btn.removeAttribute('data-original-text');
                        }
                        setMessage('error', window.omsarComplaintForm.genericError);
                    });
            });
        });

        showStep('step-1', false);
        toggleConditionalSections();
        updateStep7Title();
    });
})();
