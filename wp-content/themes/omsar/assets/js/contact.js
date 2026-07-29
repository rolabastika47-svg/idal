/**
 * Contact Us Page JavaScript
 * Handles contact form functionality
 */

(function() {
    'use strict';

    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        // Character counter for message field
        const messageField = document.getElementById('message');
        const charCount = document.getElementById('charCount');
        const contactForm = document.getElementById('contactForm');

        // Character counter functionality
        if (messageField && charCount) {
            messageField.addEventListener('input', function() {
                charCount.textContent = this.value.length;
            });
        }

        // Form submission handler
        if (contactForm) {
            const messageContainer = document.getElementById('contactFormMessage');
            const submitButton = contactForm.querySelector('button[type="submit"]');
            let originalButtonText = '';
            
            contactForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Check if contactData is available
                if (typeof contactData === 'undefined') {
                    console.error('Contact form data not initialized');
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
                const formData = new FormData(contactForm);
                formData.append('action', 'submit_contact_form');
                formData.append('nonce', contactData.nonce);
                
                // Send AJAX request
                fetch(contactData.ajaxurl, {
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
                            messageContainer.textContent = data.data.message || contactData.successMessage || 'Thank you for your submission!';
                            messageContainer.className = 'contact-form-message contact-form-message-success';
                            messageContainer.style.display = 'block';
                            
                            // Scroll to message
                            messageContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                            
                            // Reset form
                            contactForm.reset();
                            
                            // Reset character count
                            if (charCount) {
                                charCount.textContent = '0';
                            }
                            
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
                    console.error('Contact form submission error:', error);
                    
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

