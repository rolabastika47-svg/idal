/**
 * Breakdance OMSAR — Recruitment Notify Me modal.
 */
(function ($) {
    'use strict';

    var cfg = function () {
        return window.omsarBdRecruitmentNotify || {};
    };

    function getModal() {
        return $('#omsar-bd-recruitment-notify-modal');
    }

    function getElements() {
        var $modal = getModal();
        var $form = $('#omsar-bd-recruitment-notify-form');
        return {
            $modal: $modal,
            $overlay: $modal.find('.omsar-bd-recruitment-notify-modal-overlay'),
            $closeBtn: $modal.find('.omsar-bd-recruitment-notify-modal-close'),
            $form: $form,
            $emailInput: $('#omsar-bd-recruitment-notify-email'),
            $recruitmentIdInput: $('#omsar-bd-recruitment-notify-recruitment-id'),
            $submitBtn: $form.find('.omsar-bd-recruitment-notify-submit-btn'),
            $submitText: $form.find('.omsar-bd-recruitment-notify-submit-text'),
            $submitLoader: $form.find('.omsar-bd-recruitment-notify-submit-loader'),
            $errorMsg: $('#omsar-bd-recruitment-notify-email-error'),
            $successMsg: $('#omsar-bd-recruitment-notify-success')
        };
    }

    function validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function resetForm(el) {
        if (!el.$form.length) {
            return;
        }
        el.$form[0].reset();
        el.$errorMsg.text('').attr('aria-invalid', 'false');
        el.$emailInput.removeClass('error').attr('aria-invalid', 'false');
        el.$successMsg.hide().text('');
        el.$submitBtn.prop('disabled', false);
        el.$submitText.show();
        el.$submitLoader.hide();
        el.$form.find('.omsar-bd-recruitment-notify-form-group').show();
        el.$form.find('.omsar-bd-recruitment-notify-form-actions').show();
    }

    function showError(el, message) {
        el.$errorMsg.text(message).attr('aria-invalid', 'true');
        el.$emailInput.addClass('error').attr('aria-invalid', 'true');
        el.$emailInput.focus();
    }

    function clearError(el) {
        el.$errorMsg.text('').attr('aria-invalid', 'false');
        el.$emailInput.removeClass('error').attr('aria-invalid', 'false');
    }

    function showSuccess(el, message) {
        el.$form.find('.omsar-bd-recruitment-notify-form-group').hide();
        el.$form.find('.omsar-bd-recruitment-notify-form-actions').hide();
        el.$successMsg.text(message).css('display', 'block');
    }

    function openModal(recruitmentId) {
        var el = getElements();
        if (!el.$modal.length) {
            return;
        }
        resetForm(el);
        el.$recruitmentIdInput.val(recruitmentId);
        el.$modal.attr('aria-hidden', 'false').addClass('active');
        $('body').addClass('omsar-bd-modal-open').css('overflow', 'hidden');
        setTimeout(function () {
            el.$emailInput.focus();
        }, 100);
    }

    function closeModal() {
        var el = getElements();
        if (!el.$modal.length) {
            return;
        }
        el.$modal.attr('aria-hidden', 'true').removeClass('active');
        $('body').removeClass('omsar-bd-modal-open').css('overflow', '');
        resetForm(el);
    }

    $(document).on('click', '.omsar-bd-recruitments-widget .omsar-recruitment-notify-btn', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        var recruitmentId = $(this).data('recruitment-id');
        if (recruitmentId) {
            openModal(recruitmentId);
        }
    });

    $(document).on('click', '#omsar-bd-recruitment-notify-modal .omsar-bd-recruitment-notify-modal-close', function (e) {
        e.preventDefault();
        closeModal();
    });

    $(document).on('click', '#omsar-bd-recruitment-notify-modal .omsar-bd-recruitment-notify-modal-overlay', function (e) {
        if (e.target === this) {
            closeModal();
        }
    });

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && getModal().hasClass('active')) {
            closeModal();
        }
    });

    $(document).on('submit', '#omsar-bd-recruitment-notify-form', function (e) {
        e.preventDefault();
        var el = getElements();
        var settings = cfg();
        var email = el.$emailInput.val().trim();
        var recruitmentId = el.$recruitmentIdInput.val();

        clearError(el);

        if (!email) {
            showError(el, settings.emailRequired || 'Email address is required.');
            return;
        }
        if (!validateEmail(email)) {
            showError(el, settings.emailInvalid || 'Please enter a valid email address.');
            return;
        }
        if (!recruitmentId) {
            showError(el, settings.errorGeneric || 'An error occurred. Please try again.');
            return;
        }

        el.$submitBtn.prop('disabled', true);
        el.$submitText.hide();
        el.$submitLoader.show();

        $.ajax({
            url: settings.ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            timeout: 30000,
            cache: false,
            data: {
                action: 'submit_recruitment_notify',
                nonce: settings.nonce,
                email: email,
                recruitment_id: recruitmentId
            },
            success: function (response) {
                if (response && response.success) {
                    showSuccess(
                        el,
                        (response.data && response.data.message) ||
                            settings.successMessage ||
                            'Thank you! You will be notified when this recruitment becomes available.'
                    );
                } else {
                    showError(
                        el,
                        (response && response.data && response.data.message) ||
                            settings.errorGeneric ||
                            'An error occurred. Please try again.'
                    );
                    el.$submitBtn.prop('disabled', false);
                    el.$submitText.show();
                    el.$submitLoader.hide();
                }
            },
            error: function (xhr, status) {
                showError(
                    el,
                    status === 'timeout'
                        ? (settings.errorTimeout || 'Request timed out. Please try again.')
                        : (settings.errorGeneric || 'An error occurred. Please try again.')
                );
                el.$submitBtn.prop('disabled', false);
                el.$submitText.show();
                el.$submitLoader.hide();
            }
        });
    });

    $(document).on('input blur', '#omsar-bd-recruitment-notify-email', function () {
        var el = getElements();
        var email = el.$emailInput.val().trim();
        if (email && !validateEmail(email)) {
            showError(el, cfg().emailInvalid || 'Please enter a valid email address.');
        } else {
            clearError(el);
        }
    });
}(jQuery));
