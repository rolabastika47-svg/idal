/**
 * Recruitment Notify Me Modal and AJAX Handler
 * 
 * Handles the modal popup and AJAX submission for recruitment notifications
 */

(function($) {
    'use strict';

    // Modal elements
    var $modal = $('#omsar-recruitment-notify-modal');
    var $overlay = $modal.find('.omsar-recruitment-notify-modal-overlay');
    var $closeBtn = $modal.find('.omsar-recruitment-notify-modal-close');
    var $form = $('#omsar-recruitment-notify-form');
    var $emailInput = $('#omsar-recruitment-notify-email');
    var $recruitmentIdInput = $('#omsar-recruitment-notify-recruitment-id');
    var $submitBtn = $form.find('.omsar-recruitment-notify-submit-btn');
    var $submitText = $form.find('.omsar-recruitment-notify-submit-text');
    var $submitLoader = $form.find('.omsar-recruitment-notify-submit-loader');
    var $errorMsg = $('#omsar-recruitment-notify-email-error');
    var $successMsg = $('#omsar-recruitment-notify-success');

    /**
     * Open modal
     */
    function openModal(recruitmentId) {
        // Reset form
        resetForm();
        
        // Set recruitment ID
        $recruitmentIdInput.val(recruitmentId);
        
        // Show modal
        $modal.attr('aria-hidden', 'false').addClass('active');
        $('body').addClass('omsar-modal-open');
        
        // Focus on email input
        setTimeout(function() {
            $emailInput.focus();
        }, 100);
        
        // Prevent body scroll
        preventBodyScroll();
    }

    /**
     * Close modal
     */
    function closeModal() {
        $modal.attr('aria-hidden', 'true').removeClass('active');
        $('body').removeClass('omsar-modal-open');
        resetForm();
        restoreBodyScroll();
    }

    /**
     * Reset form to initial state
     */
    function resetForm() {
        $form[0].reset();
        $errorMsg.text('').attr('aria-invalid', 'false');
        $emailInput.removeClass('error').attr('aria-invalid', 'false');
        $successMsg.hide().text('');
        $submitBtn.prop('disabled', false);
        $submitText.show();
        $submitLoader.hide();
        
        // Show form elements again (in case they were hidden after success)
        $form.find('.omsar-recruitment-notify-form-group').show();
        $form.find('.omsar-recruitment-notify-form-actions').show();
    }

    /**
     * Validate email format
     */
    function validateEmail(email) {
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    /**
     * Show error message
     */
    function showError(message) {
        $errorMsg.text(message).attr('aria-invalid', 'true');
        $emailInput.addClass('error').attr('aria-invalid', 'true');
        $emailInput.focus();
    }

    /**
     * Clear error message
     */
    function clearError() {
        $errorMsg.text('').attr('aria-invalid', 'false');
        $emailInput.removeClass('error').attr('aria-invalid', 'false');
    }

    /**
     * Show success message
     */
    function showSuccess(message) {
        // Hide form elements but keep form visible for success message
        $form.find('.omsar-recruitment-notify-form-group').hide();
        $form.find('.omsar-recruitment-notify-form-actions').hide();
        
        // Show success message with proper display
        $successMsg.text(message).css('display', 'block');
        
        // Scroll to success message to ensure it's visible
        setTimeout(function() {
            $successMsg[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 100);
        
        // Don't auto-close modal - let user close it manually
    }

    /**
     * Prevent body scroll when modal is open
     */
    function preventBodyScroll() {
        if ($('body').hasClass('omsar-modal-open')) {
            $('body').css('overflow', 'hidden');
        }
    }

    /**
     * Restore body scroll when modal is closed
     */
    function restoreBodyScroll() {
        $('body').css('overflow', '');
    }

    /**
     * Handle form submission
     */
    $form.on('submit', function(e) {
        e.preventDefault();
        
        var email = $emailInput.val().trim();
        var recruitmentId = $recruitmentIdInput.val();
        
        // Clear previous errors
        clearError();
        
        // Validate email
        if (!email) {
            showError(recruitmentNotifyData.emailRequired || 'Email address is required.');
            return;
        }
        
        if (!validateEmail(email)) {
            showError(recruitmentNotifyData.emailInvalid || 'Please enter a valid email address.');
            return;
        }
        
        if (!recruitmentId) {
            showError(recruitmentNotifyData.errorGeneric || 'An error occurred. Please try again.');
            return;
        }
        
        // Disable submit button and show loading state
        $submitBtn.prop('disabled', true);
        $submitText.hide();
        $submitLoader.show();
        
        // Submit via AJAX
        $.ajax({
            url: recruitmentNotifyData.ajaxurl,
            type: 'POST',
            timeout: 30000,
            cache: false,
            data: {
                action: 'submit_recruitment_notify',
                nonce: recruitmentNotifyData.nonce,
                email: email,
                recruitment_id: recruitmentId
            },
            success: function(response) {
                if (response && response.success) {
                    showSuccess(response.data.message || recruitmentNotifyData.successMessage || 'Thank you! You will be notified when this recruitment becomes available.');
                } else {
                    var errorMessage = (response && response.data && response.data.message) 
                        ? response.data.message 
                        : (recruitmentNotifyData.errorGeneric || 'An error occurred. Please try again.');
                    showError(errorMessage);
                    $submitBtn.prop('disabled', false);
                    $submitText.show();
                    $submitLoader.hide();
                }
            },
            error: function(xhr, status, error) {
                var errorMessage = recruitmentNotifyData.errorGeneric || 'An error occurred. Please try again.';
                
                if (status === 'timeout') {
                    errorMessage = recruitmentNotifyData.errorTimeout || 'Request timed out. Please try again.';
                }
                
                showError(errorMessage);
                $submitBtn.prop('disabled', false);
                $submitText.show();
                $submitLoader.hide();
                
                console.error('Recruitment Notify AJAX Error:', status, error);
            }
        });
    });

    /**
     * Handle "Notify Me" button click
     */
    $(document).on('click', '.omsar-recruitments-widget .omsar-recruitment-notify-btn', function(e) {
        e.preventDefault();
        if ($(this).closest('.omsar-bd-recruitments-widget').length) {
            return;
        }
        var recruitmentId = $(this).data('recruitment-id');
        if (recruitmentId) {
            openModal(recruitmentId);
        }
    });

    /**
     * Handle close button click
     */
    $closeBtn.on('click', function(e) {
        e.preventDefault();
        closeModal();
    });

    /**
     * Handle overlay click
     */
    $overlay.on('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    /**
     * Handle ESC key press
     */
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $modal.hasClass('active')) {
            closeModal();
        }
    });

    /**
     * Real-time email validation
     */
    $emailInput.on('input blur', function() {
        var email = $(this).val().trim();
        if (email && !validateEmail(email)) {
            showError(recruitmentNotifyData.emailInvalid || 'Please enter a valid email address.');
        } else {
            clearError();
        }
    });

    /**
     * Handle window resize to maintain modal responsiveness
     */
    $(window).on('resize', function() {
        if ($modal.hasClass('active')) {
            // Ensure modal stays centered
            var modalContent = $modal.find('.omsar-recruitment-notify-modal-content');
            modalContent.css('max-height', $(window).height() - 40 + 'px');
        }
    });

})(jQuery);
