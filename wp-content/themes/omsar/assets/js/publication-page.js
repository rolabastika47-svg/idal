/**
 * Publication Page JavaScript
 * 
 * Handles tab functionality and interactions for the Publication page
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        
        // Initialize Bootstrap tabs
        var publicationTabs = document.querySelectorAll('#publicationTabs button[data-bs-toggle="tab"]');
        
        if (publicationTabs.length > 0) {
            // Handle tab switching
            publicationTabs.forEach(function(tab) {
                tab.addEventListener('shown.bs.tab', function(event) {
                    // Optional: Track tab changes or perform actions
                    var targetTab = event.target.getAttribute('data-bs-target');
                    console.log('Switched to tab: ' + targetTab);
                    
                    // Optional: Update URL hash without page reload
                    if (history.pushState) {
                        var newHash = targetTab.replace('#', '');
                        history.pushState(null, null, '#' + newHash);
                    }
                });
            });
            
            // Handle browser back/forward buttons
            window.addEventListener('popstate', function(event) {
                var hash = window.location.hash;
                if (hash) {
                    var targetTab = document.querySelector('#publicationTabs button[data-bs-target="' + hash + '"]');
                    if (targetTab) {
                        var tab = new bootstrap.Tab(targetTab);
                        tab.show();
                    }
                }
            });
            
            // Check for hash on page load
            if (window.location.hash) {
                var hashTab = document.querySelector('#publicationTabs button[data-bs-target="' + window.location.hash + '"]');
                if (hashTab) {
                    var tab = new bootstrap.Tab(hashTab);
                    tab.show();
                }
            }
        }
        
        // Smooth scroll to tabs on mobile when switching
        if (window.innerWidth <= 991) {
            publicationTabs.forEach(function(tab) {
                tab.addEventListener('shown.bs.tab', function() {
                    var tabsNav = document.querySelector('.publication-tabs-nav');
                    if (tabsNav) {
                        var scrollPosition = tabsNav.offsetTop - 100; // Account for fixed header
                        window.scrollTo({
                            top: scrollPosition,
                            behavior: 'smooth'
                        });
                    }
                });
            });
        }
        
        // Handle PDF link clicks (optional tracking)
        $('.pdf-list a').on('click', function() {
            var pdfName = $(this).text().trim();
            var publicationTitle = $(this).closest('.publication-card').find('.publication-title').text().trim();
            
            // Optional: Track PDF downloads
            console.log('PDF clicked: ' + pdfName + ' from publication: ' + publicationTitle);
            
            // You can add analytics tracking here if needed
            // Example: gtag('event', 'download', { 'file_name': pdfName });
        });
        
        // Lazy load images if needed (optional optimization)
        if ('IntersectionObserver' in window) {
            var imageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                            observer.unobserve(img);
                        }
                    }
                });
            });
            
            document.querySelectorAll('.publication-image img[data-src]').forEach(function(img) {
                imageObserver.observe(img);
            });
        }
        
    });

})(jQuery);

