/**
 * Partnership Inquiry Page JavaScript
 * Handles partnership form functionality
 */

(function() {
    'use strict';

    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        const partnershipForm = document.getElementById('partnershipForm');

        // Form submission handler
        if (partnershipForm) {
            const messageContainer = document.getElementById('partnershipFormMessage');
            const submitButton = partnershipForm.querySelector('button[type="submit"]');
            let originalButtonText = '';
            
            partnershipForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Check if partnershipData is available
                if (typeof partnershipData === 'undefined') {
                    console.error('Partnership form data not initialized');
                    return;
                }
                
                // Disable submit button to prevent double submission
                if (submitButton) {
                    submitButton.disabled = true;
                    originalButtonText = submitButton.textContent;
                    submitButton.textContent = submitButton.getAttribute('data-submitting') || 'Submitting...';
                }
                
                // Clear any previous messages
                if (messageContainer) {
                    messageContainer.style.display = 'none';
                    messageContainer.className = 'contact-form-message';
                    messageContainer.textContent = '';
                }
                
                // Get form data
                const formData = new FormData(partnershipForm);
                formData.append('action', 'submit_partnership_form');
                formData.append('nonce', partnershipData.nonce);
                
                // Send AJAX request
                fetch(partnershipData.ajaxurl, {
                    method: 'POST',
                    body: formData
                })
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    // Re-enable submit button
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.textContent = originalButtonText || 'Submit';
                    }
                    
                    if (messageContainer) {
                        if (data.success) {
                            // Success
                            messageContainer.textContent = data.data.message || partnershipData.successMessage || 'Thank you for your partnership inquiry! We will get back to you soon.';
                            messageContainer.className = 'contact-form-message contact-form-message-success';
                            messageContainer.style.display = 'block';
                            
                            // Scroll to message
                            messageContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                            
                            // Reset form
                            partnershipForm.reset();
                            
                            // Hide message after 5 seconds
                            setTimeout(function() {
                                messageContainer.style.display = 'none';
                            }, 5000);
                        } else {
                            // Error
                            messageContainer.textContent = data.data.message || 'An error occurred. Please try again.';
                            messageContainer.className = 'contact-form-message contact-form-message-error';
                            messageContainer.style.display = 'block';
                            
                            // Scroll to message
                            messageContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }
                    }
                })
                .catch(function(error) {
                    console.error('Partnership form submission error:', error);
                    
                    // Re-enable submit button
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.textContent = originalButtonText || 'Submit';
                    }
                    
                    // Show error message
                    if (messageContainer) {
                        messageContainer.textContent = 'An error occurred. Please try again.';
                        messageContainer.className = 'contact-form-message contact-form-message-error';
                        messageContainer.style.display = 'block';
                        messageContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                });
            });
        }
    });
})();

