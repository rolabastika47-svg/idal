/**
 * Survey Form (stepped wizard)
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

        const verifyStep = qs('#omsarSurveyVerifyStep', root);
        const formStep = qs('#omsarSurveyFormStep', root);
        const verifyForm = qs('#omsarSurveyVerifyForm', root);
        const surveyForm = qs('#omsarSurveyForm', root);
        const verifyError = qs('#omsarSurveyVerifyError', root);
        const formMessage = qs('#omsarSurveyFormMessage', root);
        const verifyBtn = qs('#omsarSurveyVerifyBtn', root);
        const propertiesContainer = qs('#properties-container', root);
        const stepperItems = qsa('.omsar-stepper-item', root);

        const state = {
            currentStep: 'step-intro',
            history: ['step-intro'],
            verifiedCode: '',
            numberOfProperties: 0,
            propertyData: {}, // Store property data to preserve between navigations
        };

        function stepIndex(stepId) {
            const order = ['step-intro', 'step-1', 'step-2', 'step-3'];
            const i = order.indexOf(stepId);
            return i >= 0 ? i + 1 : 1;
        }

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

        function setMessage(el, msg, isError) {
            if (!el) return;
            el.textContent = msg || '';
            el.className = 'omsar-survey-' + (isError ? 'error' : 'message') + ' mb-3';
            if (msg) {
                show(el);
                el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                hide(el);
            }
        }

        function setLoading(btn, loading) {
            if (!btn) return;
            btn.disabled = loading;
            btn.dataset.originalText = btn.dataset.originalText || btn.textContent;
            btn.textContent = loading ? (typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.loading ? omsarSurveyLabels.loading : 'Loading...') : btn.dataset.originalText;
        }

        function getValue(name) {
            const el = qs('[name="' + name + '"]', surveyForm);
            if (!el) return '';
            if (el.type === 'radio') {
                const checked = qs('[name="' + name + '"]:checked', surveyForm);
                return checked ? checked.value : '';
            }
            return el.value || '';
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
        }

        function validateStep(stepId) {
            const stepEl = qs('[data-step="' + stepId + '"]', formStep);
            if (!stepEl) return true;

            const requiredFields = qsa('[data-required="1"]', stepEl);
            let isValid = true;

            requiredFields.forEach(function (field) {
                const value = field.value ? field.value.trim() : '';
                const conditionalAttr = field.getAttribute('data-required-conditional');
                
                if (conditionalAttr) {
                    // Check if the conditional field value matches the requirement
                    const conditionalField = qs('[name="' + conditionalAttr + '"]:checked', surveyForm) || qs('[name="' + conditionalAttr + '"]', surveyForm);
                    const conditionalValue = conditionalField ? conditionalField.value : '';
                    
                    // For rental fields, they're required only if ownership_type is 'rented'
                    if (conditionalAttr.includes('ownership_type')) {
                        if (conditionalValue === 'rented' && !value) {
                            field.classList.add('is-invalid');
                            isValid = false;
                        } else {
                            field.classList.remove('is-invalid');
                        }
                    } else {
                        // Other conditional fields
                        if (conditionalValue && !value) {
                            field.classList.add('is-invalid');
                            isValid = false;
                        } else {
                            field.classList.remove('is-invalid');
                        }
                    }
                } else {
                    if (!value) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                }
            });

            return isValid;
        }

        function generatePropertyFields(index) {
            const propertyIndex = index + 1;
            const propertyLabel = typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.propertyLabel 
                ? omsarSurveyLabels.propertyLabel + ' ' + propertyIndex
                : 'Property ' + propertyIndex;
            const propertyHtml = `
                <div class="omsar-property-group" data-property-index="${index}">
                    <h4 class="omsar-property-title">${propertyLabel}</h4>
                    
                    <h5 class="omsar-property-subtitle">${typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.propertyLocation ? omsarSurveyLabels.propertyLocation : 'Property location'}</h5>

                    <div class="omsar-form-row">
                        <div class="omsar-form-group">
                            <label class="form-label" for="property_governorate_${index}">${typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.governorate ? omsarSurveyLabels.governorate : 'Governorate'} <span class="omsar-required">*</span></label>
                            <select class="form-control" id="property_governorate_${index}" name="properties[${index}][governorate]" data-required="1" required>
                                <option value="">${typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.selectGovernorate ? omsarSurveyLabels.selectGovernorate : 'Select governorate'}</option>
                                ${(function() {
                                    var list = typeof omsarSurvey !== 'undefined' && Array.isArray(omsarSurvey.governorates) ? omsarSurvey.governorates : [];
                                    return list.map(function(g) { return '<option value="' + g.id + '">' + String(g.label).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;') + '</option>'; }).join('');
                                })()}
                            </select>
                        </div>
                        <div class="omsar-form-group">
                            <label class="form-label" for="property_district_${index}">${typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.district ? omsarSurveyLabels.district : 'District'} <span class="omsar-required">*</span></label>
                            <select class="form-control" id="property_district_${index}" name="properties[${index}][district]" data-required="1" required disabled>
                                <option value="">${typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.selectDistrict ? omsarSurveyLabels.selectDistrict : 'Select district'}</option>
                            </select>
                        </div>
                        <div class="omsar-form-group">
                            <label class="form-label" for="property_area_${index}">${typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.area ? omsarSurveyLabels.area : 'Area'} <span class="omsar-required">*</span></label>
                            <select class="form-control" id="property_area_${index}" name="properties[${index}][area]" data-required="1" required disabled>
                                <option value="">${typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.selectArea ? omsarSurveyLabels.selectArea : 'Select area'}</option>
                            </select>
                        </div>
                    </div>

                    <div class="omsar-form-row">
                        <div class="omsar-form-group">
                            <label class="form-label" for="property_number_${index}">${typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.propertyNumber ? omsarSurveyLabels.propertyNumber : 'Property Number'} <span class="omsar-required">*</span></label>
                            <input type="number" class="form-control" id="property_number_${index}" name="properties[${index}][property_number]"  min="1"
                                   placeholder="${typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.enterPropertyNumber ? omsarSurveyLabels.enterPropertyNumber : 'Enter property number'}" 
                                   data-required="1" required>
                        </div>
                        <div class="omsar-form-group">
                            <label class="form-label" for="section_number_${index}">${typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.sectionNumber ? omsarSurveyLabels.sectionNumber : 'Section Number'} <span class="omsar-required">*</span></label>
                            <input type="number" class="form-control" id="section_number_${index}" name="properties[${index}][section_number]"  min="1"
                                   placeholder="${typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.enterSectionNumber ? omsarSurveyLabels.enterSectionNumber : 'Enter section number'}" 
                                   data-required="1" required>
                        </div>
                    </div>

                    <div class="omsar-form-group">
                        <label class="form-label" for="property_area_sqm_${index}">${typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.propertyAreaSqm ? omsarSurveyLabels.propertyAreaSqm : 'Property area in square meters'} <span class="omsar-required">*</span></label>
                        <input type="number" class="form-control" id="property_area_sqm_${index}" name="properties[${index}][area_sqm]" 
                               step="0.01" min="1"
                               placeholder="${typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.enterPropertyArea ? omsarSurveyLabels.enterPropertyArea : 'Enter property area'}" 
                               data-required="1" required>
                    </div>

                    <div class="omsar-form-group">
                        <label class="form-label">${typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.propertyOwnershipType ? omsarSurveyLabels.propertyOwnershipType : 'Property ownership type'} <span class="omsar-required">*</span></label>
                        <div class="omsar-radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="properties[${index}][ownership_type]" id="ownership_state_${index}" value="state-owned" data-required="1" required>
                                <label class="form-check-label" for="ownership_state_${index}">${typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.stateOwned ? omsarSurveyLabels.stateOwned : 'State-owned'}</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="properties[${index}][ownership_type]" id="ownership_rented_${index}" value="rented" data-required="1" required>
                                <label class="form-check-label" for="ownership_rented_${index}">${typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.rented ? omsarSurveyLabels.rented : 'Rented'}</label>
                            </div>
                        </div>
                    </div>

                    <div class="omsar-rental-details" id="rental_details_${index}" style="display: none;">
                        <h5 class="omsar-subsection-title-1">${typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.rentalContractDetails ? omsarSurveyLabels.rentalContractDetails : 'Rental contract details'}</h5>
                        
                        <div class="omsar-form-row">
                            <div class="omsar-form-group" style="flex: 0 0 50%;">
                                <label class="form-label" for="first_lease_date_${index}">${typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.dateOfFirstLease ? omsarSurveyLabels.dateOfFirstLease : 'Date of first lease'} <span class="omsar-required">*</span></label>
                                <div class="omsar-field-error" id="first_lease_date_error_${index}" style="display: none; color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;"></div>
                                <input type="date" class="form-control" id="first_lease_date_${index}" name="properties[${index}][first_lease_date]" 
                                       data-required-conditional="properties[${index}][ownership_type]"
                                       data-date-compare="first_lease"
                                       data-date-compare-with="contract_start_${index}">
                            </div>
                            <div class="omsar-form-group" style="flex: 0 0 50%;">
                                <h6 class="omsar-field-group-title">${typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.durationOfCurrentContract ? omsarSurveyLabels.durationOfCurrentContract : 'Duration of the current contract'}</h6>
                                <div class="omsar-form-row" style="margin-top: 0;">
                                    <div class="omsar-form-group" style="flex: 1;">
                                        <label class="form-label" for="contract_start_${index}">${typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.startDate ? omsarSurveyLabels.startDate : 'Start date'} <span class="omsar-required">*</span></label>
                                        <input type="date" class="form-control" id="contract_start_${index}" name="properties[${index}][contract_start]" 
                                               data-required-conditional="properties[${index}][ownership_type]"
                                               data-date-compare="start"
                                               data-date-compare-with="contract_end_${index}">
                                    </div>
                                    <div class="omsar-form-group" style="flex: 1;">
                                        <label class="form-label" for="contract_end_${index}">${typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.endDate ? omsarSurveyLabels.endDate : 'End date'} <span class="omsar-required">*</span></label>
                                        <div class="omsar-field-error" id="contract_end_error_${index}" style="display: none; color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;"></div>
                                        <input type="date" class="form-control" id="contract_end_${index}" name="properties[${index}][contract_end]" 
                                               data-required-conditional="properties[${index}][ownership_type]"
                                               data-date-compare="end"
                                               data-date-compare-with="contract_start_${index}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="omsar-form-group">
                            <label class="form-label" for="lease_value_${index}">${typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.leaseValue ? omsarSurveyLabels.leaseValue : 'Lease value'} <span class="omsar-required">*</span></label>
                            <input type="number" class="form-control" id="lease_value_${index}" name="properties[${index}][lease_value]" 
                                   step="0.01" min="0"
                                   data-required-conditional="properties[${index}][ownership_type]"
                                   placeholder="${typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.enterLeaseValue ? omsarSurveyLabels.enterLeaseValue : 'Enter lease value'}">
                        </div>

                        <div class="omsar-form-group">
                            <label class="form-label" for="exchange_rate_${index}">${typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.exchangeRateUsed ? omsarSurveyLabels.exchangeRateUsed : 'Exchange rate used'} <span class="omsar-required">*</span></label>
                            <input type="number" class="form-control" id="exchange_rate_${index}" name="properties[${index}][exchange_rate]" 
                                   step="0.01" min="0"
                                   data-required-conditional="properties[${index}][ownership_type]"
                                   placeholder="${typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.enterExchangeRate ? omsarSurveyLabels.enterExchangeRate : 'Enter exchange rate'}">
                        </div>

                        <div class="omsar-form-group">
                            <label class="form-label" for="price_per_sqm_${index}">${typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.pricePerSquareMeter ? omsarSurveyLabels.pricePerSquareMeter : 'Price per square meter'}</label>
                            <input type="number" class="form-control" id="price_per_sqm_${index}" name="properties[${index}][price_per_sqm]" 
                                   step="0.01" min="0"
                                   placeholder="${typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.enterPricePerSquareMeter ? omsarSurveyLabels.enterPricePerSquareMeter : 'Enter price per square meter'}">
                        </div>

                        <div class="omsar-form-group">
                            <label class="form-label" for="property_certificate_${index}">${typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.attachPropertyCertificate ? omsarSurveyLabels.attachPropertyCertificate : 'Attach property certificate'}</label>
                            <input type="file" class="form-control" id="property_certificate_${index}" name="properties[${index}][certificate]" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                </div>
            `;
            return propertyHtml;
        }

        function savePropertyData() {
            // Save all property data before regenerating
            if (!propertiesContainer) return;
            
            const propertyGroups = qsa('.omsar-property-group', propertiesContainer);
            propertyGroups.forEach(function(group) {
                const index = group.getAttribute('data-property-index');
                if (index === null) return;
                
                const data = {};
                const inputs = qsa('input, select, textarea', group);
                inputs.forEach(function(input) {
                    if (input.name && input.name.startsWith('properties[' + index + ']')) {
                        const fieldName = input.name.match(/\[(\w+)\]$/);
                        if (fieldName) {
                            if (input.type === 'radio') {
                                const checked = qs('[name="' + input.name + '"]:checked', group);
                                if (checked) {
                                    data[fieldName[1]] = checked.value;
                                }
                            } else if (input.type === 'file') {
                                // Files can't be preserved, skip
                            } else {
                                data[fieldName[1]] = input.value;
                            }
                        }
                    }
                });
                
                if (Object.keys(data).length > 0) {
                    state.propertyData[index] = data;
                }
            });
        }

        function getSelect2Options() {
            const dir = document.documentElement.getAttribute('dir') || document.body.getAttribute('dir') || 'ltr';
            return {
                width: '100%',
                language: {
                    noResults: function () { return ''; },
                    searching: function () { return ''; },
                    inputTooShort: function () { return ''; },
                    inputTooLong: function () { return ''; },
                    errorLoading: function () { return ''; },
                    loadingMore: function () { return ''; }
                },
                allowClear: false,
                dir: dir
            };
        }

        function initSelect2OnSelect(el) {
            if (typeof jQuery === 'undefined' || typeof jQuery.fn.select2 === 'undefined') return;
            const $el = jQuery(el);
            if ($el.hasClass('select2-hidden-accessible')) return;
            $el.select2(getSelect2Options());
        }

        function destroySelect2OnSelect(el) {
            if (typeof jQuery === 'undefined' || typeof jQuery.fn.select2 === 'undefined') return;
            const $el = jQuery(el);
            if ($el.hasClass('select2-hidden-accessible')) {
                try { $el.select2('destroy'); } catch (e) {}
            }
        }

        function initSelect2OnPropertyDropdowns(container) {
            if (!container) return;
            qsa('select[id^="property_governorate_"], select[id^="property_district_"], select[id^="property_area_"]', container).forEach(initSelect2OnSelect);
        }

        function updateDistrictDropdown(index) {
            const govSelect = qs('#property_governorate_' + index, propertiesContainer);
            const distSelect = qs('#property_district_' + index, propertiesContainer);
            const areaSelect = qs('#property_area_' + index, propertiesContainer);
            if (!govSelect || !distSelect) return;
            destroySelect2OnSelect(distSelect);
            if (areaSelect) {
                destroySelect2OnSelect(areaSelect);
                areaSelect.innerHTML = '<option value="">' + String((typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.selectArea) ? omsarSurveyLabels.selectArea : 'Select area').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;') + '</option>';
                areaSelect.value = '';
                areaSelect.disabled = true;
            }
            var govValue = govSelect.value;
            if (typeof jQuery !== 'undefined' && jQuery(govSelect).hasClass('select2-hidden-accessible')) {
                govValue = jQuery(govSelect).val() || govValue;
            }
            const governorateId = govValue ? parseInt(govValue, 10) : 0;
            const list = (typeof omsarSurvey !== 'undefined' && Array.isArray(omsarSurvey.districts)) ? omsarSurvey.districts : [];
            const selectDistrictLabel = (typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.selectDistrict) ? omsarSurveyLabels.selectDistrict : 'Select district';
            const firstOpt = '<option value="">' + String(selectDistrictLabel).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;') + '</option>';
            if (!governorateId) {
                distSelect.innerHTML = firstOpt;
                distSelect.value = '';
                distSelect.disabled = true;
                return;
            }
            const filtered = list.filter(function(d) { return d.governorate_id === governorateId; });
            const opts = filtered.map(function(d) {
                return '<option value="' + d.id + '">' + String(d.label).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;') + '</option>';
            }).join('');
            distSelect.innerHTML = firstOpt + opts;
            distSelect.value = '';
            distSelect.disabled = false;
            initSelect2OnSelect(distSelect);
        }

        function updateAreaDropdown(index, restoreValue) {
            const distSelect = qs('#property_district_' + index, propertiesContainer);
            const areaSelect = qs('#property_area_' + index, propertiesContainer);
            if (!distSelect || !areaSelect) return;
            destroySelect2OnSelect(areaSelect);
            var distValue = distSelect.value;
            // Try to get value from Select2 if it's initialized
            if (typeof jQuery !== 'undefined' && jQuery(distSelect).hasClass('select2-hidden-accessible')) {
                distValue = jQuery(distSelect).val() || distValue;
            }
            const districtId = distValue ? parseInt(distValue, 10) : 0;
            const selectAreaLabel = (typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.selectArea) ? omsarSurveyLabels.selectArea : 'Select area';
            const firstOpt = '<option value="">' + String(selectAreaLabel).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;') + '</option>';
            if (!districtId) {
                areaSelect.innerHTML = firstOpt;
                areaSelect.value = '';
                areaSelect.disabled = true;
                return;
            }
            // Fetch cities via AJAX
            const formData = new FormData();
            formData.append('action', 'omsar_get_cities_by_district');
            const nonceInput = qs('[name="omsar_survey_form_nonce"]', surveyForm);
            const nonce = nonceInput ? nonceInput.value : (typeof omsarSurvey !== 'undefined' && omsarSurvey.nonce ? omsarSurvey.nonce : '');
            if (!nonce) {
                areaSelect.innerHTML = firstOpt;
                areaSelect.value = '';
                areaSelect.disabled = true;
                return;
            }
            formData.append('nonce', nonce);
            formData.append('district_id', districtId);
            const ajaxUrl = typeof omsarSurvey !== 'undefined' && omsarSurvey.ajaxUrl ? omsarSurvey.ajaxUrl : '';
            if (!ajaxUrl) {
                areaSelect.innerHTML = firstOpt;
                areaSelect.value = '';
                areaSelect.disabled = true;
                return;
            }
            fetch(ajaxUrl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            }).then(function(r) { 
                if (!r.ok) {
                    throw new Error('Network response was not ok');
                }
                return r.json(); 
            }).then(function(res) {
                if (res.success && res.data && Array.isArray(res.data.cities)) {
                    const opts = res.data.cities.map(function(c) {
                        return '<option value="' + c.id + '">' + String(c.label).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;') + '</option>';
                    }).join('');
                    areaSelect.innerHTML = firstOpt + opts;
                    // Restore value if provided
                    if (restoreValue) {
                        areaSelect.value = restoreValue;
                    } else {
                        areaSelect.value = '';
                    }
                    areaSelect.disabled = false;
                    initSelect2OnSelect(areaSelect);
                    // Trigger change event for Select2 if value was restored
                    if (restoreValue && typeof jQuery !== 'undefined' && jQuery.fn.select2 && jQuery(areaSelect).hasClass('select2-hidden-accessible')) {
                        jQuery(areaSelect).trigger('change');
                    }
                } else {
                    areaSelect.innerHTML = firstOpt;
                    areaSelect.value = '';
                    areaSelect.disabled = true;
                }
            }).catch(function(err) {
                areaSelect.innerHTML = firstOpt;
                areaSelect.value = '';
                areaSelect.disabled = true;
            });
        }

        function restorePropertyData(index) {
            // Restore data for a specific property
            if (!state.propertyData[index]) return;
            
            const data = state.propertyData[index];
            const propGroup = qs('[data-property-index="' + index + '"]', propertiesContainer);
            if (!propGroup) return;
            
            // Track if ownership_type is being restored
            let ownershipTypeRestored = false;
            let ownershipTypeValue = '';
            
            Object.keys(data).forEach(function(key) {
                const field = qs('[name="properties[' + index + '][' + key + ']"]', propGroup);
                if (field) {
                    if (field.type === 'radio') {
                        const radio = qs('[name="properties[' + index + '][' + key + ']"][value="' + data[key] + '"]', propGroup);
                        if (radio) {
                            radio.checked = true;
                            // Track ownership_type restoration
                            if (key === 'ownership_type') {
                                ownershipTypeRestored = true;
                                ownershipTypeValue = data[key];
                            }
                        }
                    } else if (field.type !== 'file') {
                        field.value = data[key];
                        if (key === 'governorate') {
                            updateDistrictDropdown(index);
                        }
                    }
                }
            });
            
            // Manually show/hide rental details if ownership_type was restored
            if (ownershipTypeRestored) {
                const rentalDetails = qs('#rental_details_' + index, propertiesContainer);
                if (rentalDetails) {
                    if (ownershipTypeValue === 'rented') {
                        show(rentalDetails);
                        // Make rental fields required
                        const rentalFields = qsa('[data-required-conditional="properties[' + index + '][ownership_type]"]', rentalDetails);
                        rentalFields.forEach(function(field) {
                            field.setAttribute('required', 'required');
                            field.setAttribute('data-required', '1');
                        });
                    } else {
                        hide(rentalDetails);
                        // Remove required from rental fields but preserve values in state
                        const rentalFields = qsa('[data-required-conditional="properties[' + index + '][ownership_type]"]', rentalDetails);
                        rentalFields.forEach(function(field) {
                            field.removeAttribute('required');
                            field.removeAttribute('data-required');
                            field.classList.remove('is-invalid');
                        });
                        // Clear date comparison errors
                        const dateError = qs('#contract_end_error_' + index, propertiesContainer);
                        if (dateError) {
                            hide(dateError);
                        }
                        const firstLeaseError = qs('#first_lease_date_error_' + index, propertiesContainer);
                        if (firstLeaseError) {
                            hide(firstLeaseError);
                        }
                    }
                }
            }
            
            // Restore district after governorate has populated the dropdown
            if (data.district) {
                const distSelect = qs('#property_district_' + index, propertiesContainer);
                if (distSelect) {
                    distSelect.value = data.district;
                    if (typeof jQuery !== 'undefined' && jQuery.fn.select2 && jQuery(distSelect).hasClass('select2-hidden-accessible')) {
                        jQuery(distSelect).trigger('change');
                    }
                    // Update area dropdown after district is set, and restore area value if available
                    updateAreaDropdown(index, data.area || null);
                }
            } else if (data.area) {
                // If district is not set but area is, clear area dropdown
                const areaSelect = qs('#property_area_' + index, propertiesContainer);
                if (areaSelect) {
                    const selectAreaLabel = (typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.selectArea) ? omsarSurveyLabels.selectArea : 'Select area';
                    areaSelect.innerHTML = '<option value="">' + String(selectAreaLabel).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;') + '</option>';
                    areaSelect.value = '';
                    areaSelect.disabled = true;
                }
            }
        }

        function generateProperties() {
            if (!propertiesContainer) return;
            
            const numProps = parseInt(state.numberOfProperties, 10);
            if (numProps < 1) return;

            // Save existing data before clearing
            savePropertyData();

            propertiesContainer.innerHTML = '';
            
            for (let i = 0; i < numProps; i++) {
                const propertyDiv = document.createElement('div');
                propertyDiv.innerHTML = generatePropertyFields(i);
                propertiesContainer.appendChild(propertyDiv.firstElementChild);
            }
            
            // Restore data after generating fields
            for (let i = 0; i < numProps; i++) {
                restorePropertyData(i);
            }

            // Add event listeners for ownership type changes
            qsa('[name^="properties["][name$="][ownership_type]"]', propertiesContainer).forEach(function (radio) {
                radio.addEventListener('change', function () {
                    const index = this.name.match(/\[(\d+)\]/)[1];
                    const rentalDetails = qs('#rental_details_' + index, propertiesContainer);
                    if (rentalDetails) {
                        if (this.value === 'rented') {
                            show(rentalDetails);
                            // Make rental fields required
                            const rentalFields = qsa('[data-required-conditional="properties[' + index + '][ownership_type]"]', rentalDetails);
                            rentalFields.forEach(function(field) {
                                field.setAttribute('required', 'required');
                                field.setAttribute('data-required', '1');
                            });
                        } else {
                            hide(rentalDetails);
                            // Remove required from rental fields but preserve values in state
                            const rentalFields = qsa('[data-required-conditional="properties[' + index + '][ownership_type]"]', rentalDetails);
                            rentalFields.forEach(function(field) {
                                field.removeAttribute('required');
                                field.removeAttribute('data-required');
                                // Don't clear values, just remove validation
                                field.classList.remove('is-invalid');
                            });
                            // Clear date comparison errors
                            const dateError = qs('#contract_end_error_' + index, propertiesContainer);
                            if (dateError) {
                                hide(dateError);
                            }
                            const firstLeaseError = qs('#first_lease_date_error_' + index, propertiesContainer);
                            if (firstLeaseError) {
                                hide(firstLeaseError);
                            }
                        }
                    }
                });
            });

            // Save property data on any input change
            propertiesContainer.addEventListener('input', function(e) {
                const field = e.target;
                if (field.name && field.name.startsWith('properties[')) {
                    const match = field.name.match(/properties\[(\d+)\]\[(\w+)\]/);
                    if (match) {
                        const index = match[1];
                        const key = match[2];
                        if (!state.propertyData[index]) {
                            state.propertyData[index] = {};
                        }
                        if (field.type === 'radio') {
                            const checked = qs('[name="' + field.name + '"]:checked', propertiesContainer);
                            if (checked) {
                                state.propertyData[index][key] = checked.value;
                            }
                        } else if (field.type !== 'file') {
                            state.propertyData[index][key] = field.value;
                        }
                    }
                }
            });

            
            // Add event listeners for date comparison validation and governorate -> district
            propertiesContainer.addEventListener('change', function(e) {
                const field = e.target;
                if (field.name && field.name.indexOf('[governorate]') !== -1) {
                    const index = field.name.match(/\[(\d+)\]/)[1];
                    updateDistrictDropdown(index);
                    if (!state.propertyData[index]) state.propertyData[index] = {};
                    state.propertyData[index].governorate = field.value;
                }
                if (field.name && field.name.indexOf('[district]') !== -1) {
                    const index = field.name.match(/\[(\d+)\]/)[1];
                    // Only update area dropdown if district has a value
                    if (field.value) {
                        updateAreaDropdown(index);
                    } else {
                        // Clear and disable area dropdown if district is cleared
                        const areaSelect = qs('#property_area_' + index, propertiesContainer);
                        if (areaSelect) {
                            destroySelect2OnSelect(areaSelect);
                            const selectAreaLabel = (typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.selectArea) ? omsarSurveyLabels.selectArea : 'Select area';
                            areaSelect.innerHTML = '<option value="">' + String(selectAreaLabel).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;') + '</option>';
                            areaSelect.value = '';
                            areaSelect.disabled = true;
                        }
                    }
                    if (!state.propertyData[index]) state.propertyData[index] = {};
                    state.propertyData[index].district = field.value;
                }
                if (field.name && field.name.startsWith('properties[')) {
                    const match = field.name.match(/properties\[(\d+)\]\[(\w+)\]/);
                    if (match) {
                        const index = match[1];
                        const key = match[2];
                        if (!state.propertyData[index]) state.propertyData[index] = {};
                        state.propertyData[index][key] = field.value;
                    }
                }
                const dateCompare = field.getAttribute('data-date-compare');
                const dateCompareWith = field.getAttribute('data-date-compare-with');
                
                if (dateCompare && dateCompareWith) {
                    const compareField = qs('#' + dateCompareWith, propertiesContainer);
                    const index = field.id.match(/_(\d+)$/)[1];
                    
                    if (dateCompare === 'first_lease') {
                        // first_lease_date must be before or equal to contract_start
                        if (field.value && compareField && compareField.value) {
                            const fieldDate = new Date(field.value);
                            const compareDate = new Date(compareField.value);
                            
                            if (fieldDate > compareDate) {
                                field.classList.add('is-invalid');
                                const errorMsg = qs('#first_lease_date_error_' + index, propertiesContainer);
                                if (errorMsg) {
                                    const msg = typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.firstLeaseDateMustBeBefore 
                                        ? omsarSurveyLabels.firstLeaseDateMustBeBefore 
                                        : 'Date of first lease must be before or equal to contract start date.';
                                    errorMsg.textContent = msg;
                                    show(errorMsg);
                                }
                            } else {
                                field.classList.remove('is-invalid');
                                const errorMsg = qs('#first_lease_date_error_' + index, propertiesContainer);
                                if (errorMsg) {
                                    hide(errorMsg);
                                }
                            }
                        }
                    } else if (dateCompare === 'start') {
                        // contract_start must be later than or equal to first_lease_date and earlier than contract_end
                        if (field.value) {
                            // Check against contract_end
                            if (compareField && compareField.value) {
                                const fieldDate = new Date(field.value);
                                const compareDate = new Date(compareField.value);
                                if (fieldDate >= compareDate) {
                                    compareField.classList.add('is-invalid');
                                    const errorMsg = qs('#contract_end_error_' + index, propertiesContainer);
                                    if (errorMsg) {
                                        const msg = typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.endDateMustBeLater 
                                            ? omsarSurveyLabels.endDateMustBeLater 
                                            : 'End date must be later than start date.';
                                        errorMsg.textContent = msg;
                                        show(errorMsg);
                                    }
                                } else {
                                    compareField.classList.remove('is-invalid');
                                    const errorMsg = qs('#contract_end_error_' + index, propertiesContainer);
                                    if (errorMsg) {
                                        hide(errorMsg);
                                    }
                                }
                            }
                            
                            // Check against first_lease_date
                            const firstLeaseField = qs('#first_lease_date_' + index, propertiesContainer);
                            if (firstLeaseField && firstLeaseField.value) {
                                const fieldDate = new Date(field.value);
                                const firstLeaseDate = new Date(firstLeaseField.value);
                                if (firstLeaseDate > fieldDate) {
                                    firstLeaseField.classList.add('is-invalid');
                                    const errorMsg = qs('#first_lease_date_error_' + index, propertiesContainer);
                                    if (errorMsg) {
                                        const msg = typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.firstLeaseDateMustBeBefore 
                                            ? omsarSurveyLabels.firstLeaseDateMustBeBefore 
                                            : 'Date of first lease must be before or equal to contract start date.';
                                        errorMsg.textContent = msg;
                                        show(errorMsg);
                                    }
                                } else {
                                    firstLeaseField.classList.remove('is-invalid');
                                    const errorMsg = qs('#first_lease_date_error_' + index, propertiesContainer);
                                    if (errorMsg) {
                                        hide(errorMsg);
                                    }
                                }
                            }
                        }
                    } else if (dateCompare === 'end') {
                        // contract_end must be later than contract_start
                        if (field.value && compareField && compareField.value) {
                            const fieldDate = new Date(field.value);
                            const compareDate = new Date(compareField.value);
                            
                            if (fieldDate <= compareDate) {
                                field.classList.add('is-invalid');
                                const errorMsg = qs('#contract_end_error_' + index, propertiesContainer);
                                if (errorMsg) {
                                    const msg = typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.endDateMustBeLater 
                                        ? omsarSurveyLabels.endDateMustBeLater 
                                        : 'End date must be later than start date.';
                                    errorMsg.textContent = msg;
                                    show(errorMsg);
                                }
                            } else {
                                field.classList.remove('is-invalid');
                                const errorMsg = qs('#contract_end_error_' + index, propertiesContainer);
                                if (errorMsg) {
                                    hide(errorMsg);
                                }
                            }
                        }
                    }
                }
            });

            initSelect2OnPropertyDropdowns(propertiesContainer);

            // Governorate change: update district dropdown (must use jQuery so Select2's change is caught)
            qsa('select[id^="property_governorate_"]', propertiesContainer).forEach(function (el) {
                var index = el.id.replace('property_governorate_', '');
                if (typeof jQuery !== 'undefined') {
                    jQuery(el).off('change.omsarDistrict').on('change.omsarDistrict', function () {
                        updateDistrictDropdown(index);
                    });
                }
            });
            
            // District change: update area dropdown (must use jQuery so Select2's change is caught)
            qsa('select[id^="property_district_"]', propertiesContainer).forEach(function (el) {
                var index = el.id.replace('property_district_', '');
                if (typeof jQuery !== 'undefined') {
                    var $el = jQuery(el);
                    // Remove any existing listeners
                    $el.off('change.omsarArea select2:select.omsarArea');
                    // Listen to both change and select2:select events
                    $el.on('change.omsarArea select2:select.omsarArea', function () {
                        var districtValue = jQuery(this).val();
                        if (districtValue) {
                            updateAreaDropdown(index);
                        } else {
                            // Clear and disable area dropdown if district is cleared
                            const areaSelect = qs('#property_area_' + index, propertiesContainer);
                            if (areaSelect) {
                                destroySelect2OnSelect(areaSelect);
                                const selectAreaLabel = (typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.selectArea) ? omsarSurveyLabels.selectArea : 'Select area';
                                areaSelect.innerHTML = '<option value="">' + String(selectAreaLabel).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;') + '</option>';
                                areaSelect.value = '';
                                areaSelect.disabled = true;
                            }
                        }
                    });
                } else {
                    // Fallback for non-jQuery environments
                    el.addEventListener('change', function() {
                        if (this.value) {
                            updateAreaDropdown(index);
                        } else {
                            const areaSelect = qs('#property_area_' + index, propertiesContainer);
                            if (areaSelect) {
                                const selectAreaLabel = (typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.selectArea) ? omsarSurveyLabels.selectArea : 'Select area';
                                areaSelect.innerHTML = '<option value="">' + String(selectAreaLabel).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;') + '</option>';
                                areaSelect.value = '';
                                areaSelect.disabled = true;
                            }
                        }
                    });
                }
            });
        }

        // Verification form handler
        if (verifyForm) {
            verifyForm.addEventListener('submit', function(e) {
                e.preventDefault();
                setMessage(verifyError, '', false);
                const code = qs('#omsar_verification_code', verifyForm).value.trim();
                if (!code) return;
                
                setLoading(verifyBtn, true);
                const formData = new FormData();
                formData.append('action', 'omsar_verify_survey_code');
                formData.append('nonce', qs('[name="omsar_survey_verify_nonce"]', verifyForm).value);
                formData.append('verification_code', code);
                
                fetch(omsarSurvey.ajaxUrl, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                }).then(function(r) { return r.json(); }).then(function(res) {
                    setLoading(verifyBtn, false);
                    if (res.success) {
                        state.verifiedCode = code;
                        hide(verifyStep);
                        show(formStep);
                        showStep('step-intro', true);
                    } else {
                        const msg = res.data && res.data.message ? res.data.message : (typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.invalidCode ? omsarSurveyLabels.invalidCode : 'Invalid verification code.');
                        setMessage(verifyError, msg, true);
                    }
                }).catch(function() {
                    setLoading(verifyBtn, false);
                    const msg = typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.error ? omsarSurveyLabels.error : 'An error occurred. Please try again.';
                    setMessage(verifyError, msg, true);
                });
            });
        }

        // Step navigation handlers
        if (surveyForm) {
            surveyForm.addEventListener('click', function(e) {
                const btn = e.target.closest('[data-action]');
                if (!btn) return;
                
                e.preventDefault();
                const action = btn.getAttribute('data-action');
                
                if (action === 'next') {
                    if (!validateStep(state.currentStep)) {
                        const msg = typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.fillRequiredFields ? omsarSurveyLabels.fillRequiredFields : 'Please fill in all required fields.';
                        setMessage(formMessage, msg, true);
                        return;
                    }
                    
                    // Special handling for step-1 to generate properties
                    if (state.currentStep === 'step-1') {
                        const numProps = parseInt(qs('#number_of_properties', surveyForm).value, 10);
                        if (numProps < 1) {
                            const msg = typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.validNumberOfProperties ? omsarSurveyLabels.validNumberOfProperties : 'Please enter a valid number of properties.';
                            setMessage(formMessage, msg, true);
                            return;
                        }
                        
                        // Save property data before updating number
                        savePropertyData();
                        
                        const oldNumProps = state.numberOfProperties;
                        state.numberOfProperties = numProps;
                        
                        // Only regenerate if number changed or properties don't exist
                        if (oldNumProps !== numProps || !qs('.omsar-property-group', propertiesContainer)) {
                            generateProperties();
                        }
                    }
                    
                    const nextSteps = {
                        'step-intro': 'step-1',
                        'step-1': 'step-2',
                        'step-2': 'step-3'
                    };
                    const next = nextSteps[state.currentStep];
                    if (next) showStep(next, true);
                } else if (action === 'back') {
                    // Save property data before navigating back
                    if (state.currentStep === 'step-2') {
                        savePropertyData();
                    }
                    
                    state.history.pop(); // Remove current step
                    const prev = state.history[state.history.length - 1] || 'step-intro';
                    showStep(prev, false);
                } else if (action === 'submit') {
                    if (!validateStep(state.currentStep)) {
                        const msg = typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.fillRequiredFields ? omsarSurveyLabels.fillRequiredFields : 'Please fill in all required fields.';
                        setMessage(formMessage, msg, true);
                        return;
                    }
                    
                    // Validate all properties
                    if (state.numberOfProperties > 0) {
                        let allPropsValid = true;
                        for (let i = 0; i < state.numberOfProperties; i++) {
                            const propGroup = qs('[data-property-index="' + i + '"]', propertiesContainer);
                            if (propGroup) {
                                const requiredFields = qsa('[data-required="1"]', propGroup);
                                requiredFields.forEach(function(field) {
                                    const value = field.value ? field.value.trim() : '';
                                    const conditionalAttr = field.getAttribute('data-required-conditional');
                                    
                                    if (conditionalAttr) {
                                        // Check if the conditional field value matches the requirement
                                        const conditionalField = qs('[name="' + conditionalAttr + '"]:checked', surveyForm) || qs('[name="' + conditionalAttr + '"]', surveyForm);
                                        const conditionalValue = conditionalField ? conditionalField.value : '';
                                        
                                        // For rental fields, they're required only if ownership_type is 'rented'
                                        if (conditionalAttr.includes('ownership_type')) {
                                            if (conditionalValue === 'rented' && !value) {
                                                field.classList.add('is-invalid');
                                                allPropsValid = false;
                                            } else {
                                                field.classList.remove('is-invalid');
                                            }
                                        } else {
                                            // Other conditional fields
                                            if (conditionalValue && !value) {
                                                field.classList.add('is-invalid');
                                                allPropsValid = false;
                                            } else {
                                                field.classList.remove('is-invalid');
                                            }
                                        }
                                    } else {
                                        if (!value) {
                                            field.classList.add('is-invalid');
                                            allPropsValid = false;
                                        } else {
                                            field.classList.remove('is-invalid');
                                        }
                                    }
                                });
                                
                                // Validate date comparison for rental contracts
                                const ownershipType = qs('[name="properties[' + i + '][ownership_type]"]:checked', propGroup);
                                if (ownershipType && ownershipType.value === 'rented') {
                                    const firstLeaseDate = qs('#first_lease_date_' + i, propGroup);
                                    const contractStart = qs('#contract_start_' + i, propGroup);
                                    const contractEnd = qs('#contract_end_' + i, propGroup);
                                    
                                    // Validate first_lease_date <= contract_start
                                    if (firstLeaseDate && contractStart && firstLeaseDate.value && contractStart.value) {
                                        const firstLease = new Date(firstLeaseDate.value);
                                        const startDate = new Date(contractStart.value);
                                        if (firstLease > startDate) {
                                            firstLeaseDate.classList.add('is-invalid');
                                            const errorMsg = qs('#first_lease_date_error_' + i, propGroup);
                                            if (errorMsg) {
                                                const msg = typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.firstLeaseDateMustBeBefore 
                                                    ? omsarSurveyLabels.firstLeaseDateMustBeBefore 
                                                    : 'Date of first lease must be before or equal to contract start date.';
                                                errorMsg.textContent = msg;
                                                show(errorMsg);
                                            }
                                            allPropsValid = false;
                                        } else {
                                            firstLeaseDate.classList.remove('is-invalid');
                                            const errorMsg = qs('#first_lease_date_error_' + i, propGroup);
                                            if (errorMsg) {
                                                hide(errorMsg);
                                            }
                                        }
                                    }
                                    
                                    // Validate contract_end > contract_start
                                    if (contractStart && contractEnd && contractStart.value && contractEnd.value) {
                                        const startDate = new Date(contractStart.value);
                                        const endDate = new Date(contractEnd.value);
                                        if (endDate <= startDate) {
                                            contractEnd.classList.add('is-invalid');
                                            const errorMsg = qs('#contract_end_error_' + i, propGroup);
                                            if (errorMsg) {
                                                const msg = typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.endDateMustBeLater 
                                                    ? omsarSurveyLabels.endDateMustBeLater 
                                                    : 'End date must be later than start date.';
                                                errorMsg.textContent = msg;
                                                show(errorMsg);
                                            }
                                            allPropsValid = false;
                                        } else {
                                            contractEnd.classList.remove('is-invalid');
                                            const errorMsg = qs('#contract_end_error_' + i, propGroup);
                                            if (errorMsg) {
                                                hide(errorMsg);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if (!allPropsValid) {
                            const msg = typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.fillAllPropertyInfo ? omsarSurveyLabels.fillAllPropertyInfo : 'Please fill in all property information before continuing.';
                            setMessage(formMessage, msg, true);
                            return;
                        }
                    }
                    
                    // Submit form
                    const submitBtn = qs('[data-action="submit"]', surveyForm);
                    setLoading(submitBtn, true);
                    const formData = new FormData(surveyForm);
                    formData.append('action', 'omsar_submit_survey_form');
                    formData.append('nonce', qs('[name="omsar_survey_form_nonce"]', surveyForm).value);
                    if (state.verifiedCode) {
                        formData.append('verification_code', state.verifiedCode);
                    }
                    
                    fetch(omsarSurvey.ajaxUrl, {
                        method: 'POST',
                        body: formData,
                        credentials: 'same-origin'
                    }).then(function(r) { return r.json(); }).then(function(res) {
                        setLoading(submitBtn, false);
                        if (res.success) {
                            const successUrl = (typeof omsarSurvey !== 'undefined' && omsarSurvey.successPageUrl) ? omsarSurvey.successPageUrl : '';
                            if (successUrl) {
                                window.location.href = successUrl;
                                return;
                            }
                            const msg = res.data && res.data.message ? res.data.message : (typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.success ? omsarSurveyLabels.success : 'Thank you for completing the survey.');
                            setMessage(formMessage, msg, false);
                            surveyForm.reset();
                            state.verifiedCode = '';
                            state.numberOfProperties = 0;
                            state.propertyData = {}; // Clear property data on successful submission
                            
                            // Clear verification code field
                            const verificationCodeInput = qs('#omsar_verification_code', verifyForm);
                            if (verificationCodeInput) {
                                verificationCodeInput.value = '';
                            }
                            
                            // Clear verification error message
                            setMessage(verifyError, '', false);
                            
                            setTimeout(function() {
                                hide(formStep);
                                show(verifyStep);
                                state.currentStep = 'step-intro';
                                state.history = ['step-intro'];
                            }, 3000);
                        } else {
                            const msg = res.data && res.data.message ? res.data.message : (typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.error ? omsarSurveyLabels.error : 'An error occurred. Please try again.');
                            setMessage(formMessage, msg, true);
                        }
                    }).catch(function() {
                        setLoading(submitBtn, false);
                        const msg = typeof omsarSurveyLabels !== 'undefined' && omsarSurveyLabels.error ? omsarSurveyLabels.error : 'An error occurred. Please try again.';
                        setMessage(formMessage, msg, true);
                    });
                }
            });
        }
    });
})();
