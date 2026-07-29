// Scroll Script
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar-scroll');
    if (navbar) {
        if (window.scrollY > 50) {
            navbar.classList.add('bg-white', 'shadow-sm');
        } else {
            navbar.classList.remove('bg-white', 'shadow-sm');
        }
    }
});

// Header Search Toggle
(function() {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function() {
        const searchToggle = document.querySelector('.search-icon-toggle');
        const searchWrapper = document.querySelector('.header-search-wrapper');
        const searchInput = document.querySelector('.header-search-input');
        
        if (!searchToggle || !searchWrapper) {
            return;
        }
        
        // Toggle search dropdown on icon click
        searchToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Toggle active class
            searchWrapper.classList.toggle('active');
            
            // Focus input when opening
            if (searchWrapper.classList.contains('active') && searchInput) {
                setTimeout(function() {
                    searchInput.focus();
                }, 100);
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (searchWrapper && searchWrapper.classList.contains('active')) {
                // Check if click is outside the search wrapper (toggle is inside wrapper, so this covers both)
                if (!searchWrapper.contains(e.target)) {
                    searchWrapper.classList.remove('active');
                }
            }
        });
        
        // Close dropdown on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && searchWrapper && searchWrapper.classList.contains('active')) {
                searchWrapper.classList.remove('active');
            }
        });
        
        // Prevent form submission from closing dropdown (optional - keep it open after search)
        const searchForm = document.querySelector('.header-search-form');
        if (searchForm) {
            searchForm.addEventListener('submit', function() {
                // Keep dropdown open or close it - you can adjust this behavior
                // For now, we'll let it close naturally when page navigates
            });
        }
    });
})();

// Carousel Initialization
$(document).ready(function(){
    
    // Detect RTL
    var isRTL = $('html').attr('dir') === 'rtl' || $('body').attr('dir') === 'rtl';
    
    // Latest Projects Carousel
    var projectsCarousel = $(".projects-carousel").owlCarousel({
        center: true,
        items: 2,
        loop: true,
        autoplay: true,
        autoplayHoverPause: true, //stop on hover
        margin: 20,
        nav: false,
        dots: true,
        rtl: isRTL, // Enable RTL support - automatically reverses direction
        responsive:{
            0:{
                items: 1,
                center: false, // Disable center mode on mobile
                stagePadding: 0 // Full-width cards on mobile
            },
            768:{
                items: 1.5
            },
            1000:{
                items: 2.2
            }
        }
    });

    // Handle clicks on shadow overlays for projects carousel
    function handleShadowOverlayClick() {
        // Scope to projects carousel wrapper only
        const $wrapper = $('.projects-carousel-wrapper');
        
        // For RTL, swap left and right handlers
        if (isRTL) {
            // In RTL: left overlay should go next (which moves left visually)
            $wrapper.find('.carousel-shadow-left').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                projectsCarousel.trigger('next.owl.carousel');
            });
            
            // In RTL: right overlay should go prev (which moves right visually)
            $wrapper.find('.carousel-shadow-right').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                projectsCarousel.trigger('prev.owl.carousel');
            });
        } else {
            // Handle left shadow overlay click (LTR)
            $wrapper.find('.carousel-shadow-left').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                projectsCarousel.trigger('prev.owl.carousel');
            });
            
            // Handle right shadow overlay click (LTR)
            $wrapper.find('.carousel-shadow-right').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                projectsCarousel.trigger('next.owl.carousel');
            });
        }
    }
    
    // Initialize shadow overlay clicks after carousel is ready
    handleShadowOverlayClick();

    // Latest News Carousel
    $(".news-carousel").owlCarousel({
        items: 3, 
        loop: true,
        autoplay: true,
        autoplayHoverPause: true, //stop on hover
        margin: 20,
        nav: false,
        dots: true,
        stagePadding: 150, // Add padding to show partial cards on sides
        responsive:{
            0:{
                items: 1.2, // Peek effect on mobile
                margin: 10,
                stagePadding: 0 // No padding on mobile
            },
            768:{
                items: 2,
                stagePadding: 100 // Reduced padding on tablets
            },
            1000:{
                items: 3,
                stagePadding: 150 // Full padding for desktop layout: 1/2, 1, 1, 1/2
            },
            1600:{   // Large monitors
                items: 3,
                stagePadding: 250
            }
        },
        onInitialized: function() {
            // Copy inline background-image to CSS variable for news cards zoom effect
            updateNewsCardBackgrounds();
        },
        onTranslated: function() {
            // Update backgrounds after carousel translation (for cloned items)
            updateNewsCardBackgrounds();
        }
    });

    // Function to copy inline background-image to CSS variable for news cards
    function updateNewsCardBackgrounds() {
        $(".news-carousel .news-card").each(function() {
            const bgImage = $(this).css('background-image');
            if (bgImage && bgImage !== 'none') {
                $(this).css('--bg-image', bgImage);
            }
        });
    }

    // Testimonials Carousel
    $(".testimonials-carousel").owlCarousel({
        center: true,
        items: 3,
        loop: true,
        autoplay: true,
        autoplayHoverPause: true, //stop on hover
        margin: 30,
        nav: false,
        navText: ["<i class='bi bi-chevron-left'></i>", "<i class='bi bi-chevron-right'></i>"],
        dots: true,
        responsive:{
            0:{
                items: 1
            },
            768:{
                items: 1.5 // or 2
            },
            1000:{
                items: 3,
                nav: true,
                dots: false
            }
        }
    });

    // Counter Animation
    function animateCounter(counterElement) {
        const target = parseFloat(counterElement.getAttribute('data-target')) || 0;
        const unit = counterElement.getAttribute('data-unit') || '';
        const duration = 2000; // 2 seconds
        const startTime = performance.now();
        const startValue = 0;

        function updateCounter(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Easing function for smooth animation (ease-out)
            const easeOut = 1 - Math.pow(1 - progress, 3);
            
            const currentValue = Math.floor(startValue + (target - startValue) * easeOut);
            counterElement.textContent = currentValue + unit;
            
            if (progress < 1) {
                requestAnimationFrame(updateCounter);
            } else {
                // Ensure final value is exact
                counterElement.textContent = target + unit;
            }
        }

        requestAnimationFrame(updateCounter);
    }

    // Initialize counter animation with Intersection Observer
    function initCounterAnimation() {
        const counters = document.querySelectorAll('.card-number[data-target]');
        
        if (counters.length === 0) {
            return;
        }

        const observerOptions = {
            threshold: 0.5, // Trigger when 50% of the element is visible
            rootMargin: '0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
                    entry.target.classList.add('animated');
                    animateCounter(entry.target);
                    observer.unobserve(entry.target); // Stop observing once animated
                }
            });
        }, observerOptions);

        counters.forEach(function(counter) {
            observer.observe(counter);
        });
    }

    // Initialize on DOM ready
    initCounterAnimation();

    // Staggered Card Animation
    function initCardAnimation() {
        const cardsContainer = document.querySelector('.impact-cards-container');
        if (!cardsContainer) {
            return;
        }

        const cards = cardsContainer.querySelectorAll('.impact-card');
        if (cards.length === 0) {
            return;
        }

        // Detect RTL
        const isRTL = document.documentElement.getAttribute('dir') === 'rtl' || 
                     document.body.getAttribute('dir') === 'rtl';

        // Intersection Observer for viewport entry
        const observerOptions = {
            threshold: 0.2, // Trigger when 20% of container is visible
            rootMargin: '0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting && !entry.target.classList.contains('cards-animated')) {
                    entry.target.classList.add('cards-animated');
                    
                    // Animate each card with staggered delay (much slower)
                    cards.forEach(function(card, index) {
                        setTimeout(function() {
                            card.classList.add('card-animated');
                        }, index * 300); // 300ms delay between each card (much slower)
                    });

                    observer.unobserve(entry.target); // Stop observing once animated
                }
            });
        }, observerOptions);

        observer.observe(cardsContainer);
    }

    // Initialize card animation
    initCardAnimation();

    // Impact Section Title Animation
    function initImpactTitleAnimation() {
        const impactSection = document.querySelector('.impact-section');
        if (!impactSection) {
            return;
        }

        const titleSection = impactSection.querySelector('.title-section');
        if (!titleSection) {
            return;
        }

        // Intersection Observer for viewport entry
        const observerOptions = {
            threshold: 0.15, // Trigger when 15% of section is visible
            rootMargin: '0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting && !entry.target.classList.contains('impact-title-animated')) {
                    entry.target.classList.add('impact-title-animated');
                    
                    // Animate title
                    if (titleSection) {
                        setTimeout(function() {
                            titleSection.classList.add('section-animated');
                        }, 150);
                    }

                    observer.unobserve(entry.target); // Stop observing once animated
                }
            });
        }, observerOptions);

        observer.observe(impactSection);
    }

    // Initialize impact title animation
    initImpactTitleAnimation();

    // Staggered Pillar Cards Animation
    function initPillarAnimation() {
        const pillarsSection = document.querySelector('.pillars-section');
        if (!pillarsSection) {
            return;
        }

        const titleSection = pillarsSection.querySelector('.title-section');
        const pillarCards = pillarsSection.querySelectorAll('.pillar-card');
        if (pillarCards.length === 0) {
            return;
        }

        // Intersection Observer for viewport entry
        const observerOptions = {
            threshold: 0.2, // Trigger when 20% of section is visible
            rootMargin: '0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting && !entry.target.classList.contains('pillars-animated')) {
                    entry.target.classList.add('pillars-animated');
                    
                    // Animate title first
                    if (titleSection) {
                        setTimeout(function() {
                            titleSection.classList.add('section-animated');
                        }, 150);
                    }
                    
                    // Animate each card with staggered delay (much slower)
                    pillarCards.forEach(function(card, index) {
                        setTimeout(function() {
                            card.classList.add('pillar-animated');
                        }, 400 + (index * 300)); // Start after title, 300ms delay between each card for much slower animation
                    });

                    observer.unobserve(entry.target); // Stop observing once animated
                }
            });
        }, observerOptions);

        observer.observe(pillarsSection);
    }

    // Initialize pillar animation
    initPillarAnimation();

    // Latest Projects/Recruitments Section Animation
    function initLatestProjectsAnimation() {
        const projectsSection = document.querySelector('.latest-projects-section');
        if (!projectsSection) {
            return;
        }

        const titleSection = projectsSection.querySelector('.title-section');
        const carouselItems = projectsSection.querySelectorAll('.projects-carousel .item');
        const buttonRow = projectsSection.querySelector('.row.mt-4');

        // Intersection Observer for viewport entry
        const observerOptions = {
            threshold: 0.15, // Trigger when 15% of section is visible
            rootMargin: '0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting && !entry.target.classList.contains('projects-section-animated')) {
                    entry.target.classList.add('projects-section-animated');
                    
                    // Animate title first (slower)
                    if (titleSection) {
                        setTimeout(function() {
                            titleSection.classList.add('section-animated');
                        }, 150);
                    }

                    // Animate carousel items with staggered delay (slower)
                    if (carouselItems.length > 0) {
                        carouselItems.forEach(function(item, index) {
                            setTimeout(function() {
                                item.classList.add('card-animated');
                            }, 400 + (index * 180)); // Start after title, 180ms between each (slower)
                        });
                    }

                    // Animate button after cards (slower)
                    if (buttonRow) {
                        const delay = 400 + (carouselItems.length * 180) + 300;
                        setTimeout(function() {
                            buttonRow.classList.add('button-animated');
                        }, delay);
                    }

                    observer.unobserve(entry.target); // Stop observing once animated
                }
            });
        }, observerOptions);

        observer.observe(projectsSection);
    }

    // Initialize latest projects animation
    initLatestProjectsAnimation();

    // Latest News Section Animation - Fade In Up
    function initLatestNewsAnimation() {
        const newsSection = document.querySelector('.latest-news-section');
        if (!newsSection) {
            return;
        }

        const titleSection = newsSection.querySelector('.title-section');
        const carouselItems = newsSection.querySelectorAll('.news-carousel .item');
        const buttonRow = newsSection.querySelector('.row.mt-4');

        // Intersection Observer for viewport entry
        const observerOptions = {
            threshold: 0.15, // Trigger when 15% of section is visible
            rootMargin: '0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting && !entry.target.classList.contains('news-section-animated')) {
                    entry.target.classList.add('news-section-animated');
                    
                    // Animate title first (slower)
                    if (titleSection) {
                        setTimeout(function() {
                            titleSection.classList.add('section-animated');
                        }, 150);
                    }

                    // Animate carousel items with staggered delay (fade in up - slower)
                    if (carouselItems.length > 0) {
                        carouselItems.forEach(function(item, index) {
                            setTimeout(function() {
                                item.classList.add('card-animated');
                            }, 400 + (index * 180)); // Start after title, 180ms between each (slower)
                        });
                    }

                    // Animate button after cards (slower)
                    if (buttonRow) {
                        const delay = 400 + (carouselItems.length * 180) + 300;
                        setTimeout(function() {
                            buttonRow.classList.add('button-animated');
                        }, delay);
                    }

                    observer.unobserve(entry.target); // Stop observing once animated
                }
            });
        }, observerOptions);

        observer.observe(newsSection);
    }

    // Initialize latest news animation
    initLatestNewsAnimation();

    // Minister Section Animation - Fade In Up
    function initMinisterAnimation() {
        const ministerSection = document.querySelector('.minister-section');
        if (!ministerSection) {
            return;
        }

        const ministerCard = ministerSection.querySelector('.minister-card');
        if (!ministerCard) {
            return;
        }

        // Intersection Observer for viewport entry
        const observerOptions = {
            threshold: 0.2, // Trigger when 20% of section is visible
            rootMargin: '0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting && !entry.target.classList.contains('minister-section-animated')) {
                    entry.target.classList.add('minister-section-animated');
                    
                    // Animate the card with a slight delay for smooth effect
                    setTimeout(function() {
                        ministerCard.classList.add('minister-animated');
                    }, 150);

                    observer.unobserve(entry.target); // Stop observing once animated
                }
            });
        }, observerOptions);

        observer.observe(ministerSection);
    }

    // Initialize minister animation
    initMinisterAnimation();

    // ============================================
    // Media Listing Pages Animations (News, Events, Workshops)
    // ============================================
    
    /**
     * Initialize animations for media listing pages (news, events, workshops)
     */
    function initMediaListingAnimations() {
        // Only run on media listing pages
        if (!$('.media-page-section').length) {
            return;
        }

        // Animate filters
        function animateFilters() {
            const filtersRow = document.querySelector('.media-filters-row');
            if (filtersRow && !filtersRow.classList.contains('filters-animated')) {
                setTimeout(function() {
                    filtersRow.classList.add('filters-animated');
                }, 100);
            }
        }

        // Animate cards with stagger (works for news, events, workshops)
        function animateCards(containerSelector) {
            const cards = document.querySelectorAll(containerSelector + ' .news-card-item:not(.card-animated)');
            if (cards.length === 0) return;

            cards.forEach(function(card, index) {
                setTimeout(function() {
                    card.classList.add('card-animated');
                }, 150 + (index * 120)); // Stagger delay: 150ms base + 120ms per card
            });
        }

        // Animate load more button
        function animateLoadMoreButton(buttonSelector) {
            const loadMoreBtn = document.querySelector(buttonSelector);
            if (loadMoreBtn && !loadMoreBtn.classList.contains('button-animated')) {
                // Wait for cards to finish animating
                const cards = document.querySelectorAll('.media-page-section .news-card-item');
                const totalDelay = 150 + (cards.length * 120) + 300; // Base + card delays + extra delay
                
                setTimeout(function() {
                    loadMoreBtn.classList.add('button-animated');
                }, totalDelay);
            }
        }

        // Use Intersection Observer for cards animation
        function initCardsObserver(containerSelector, buttonSelector) {
            const cardsContainer = document.querySelector(containerSelector);
            if (!cardsContainer) return;

            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.1
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        animateCards(containerSelector);
                        if (buttonSelector) {
                            animateLoadMoreButton(buttonSelector);
                        }
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            observer.observe(cardsContainer);
        }

        // Initialize animations based on page type
        animateFilters();
        
        // News listing page
        if (document.querySelector('#news-cards-container')) {
            initCardsObserver('#news-cards-container', '#load-more-news-btn');
        }
        
        // Events listing page
        if (document.querySelector('#events-cards-container')) {
            initCardsObserver('#events-cards-container', '#load-more-events-btn');
        }
        
        // Workshop listing page
        if (document.querySelector('#workshop-cards-container')) {
            initCardsObserver('#workshop-cards-container', '#load-more-workshop-btn');
        }
    }

    // Initialize media listing animations
    initMediaListingAnimations();

    // Function to animate newly loaded cards (for AJAX) - works for all media types
    window.animateNewMediaCards = function($newCards) {
        if (!$newCards || $newCards.length === 0) return;

        $newCards.each(function(index) {
            const $card = $(this);
            // Reset to initial state
            $card.removeClass('card-animated');
            // Force reflow
            $card[0].offsetHeight;
            // Animate with stagger
            setTimeout(function() {
                $card.addClass('card-animated');
            }, 100 + (index * 100)); // Faster stagger for loaded cards
        });
    };
    
    // Keep backward compatibility for news
    window.animateNewNewsCards = window.animateNewMediaCards;

    // ============================================
    // Contact Us Page Animations
    // ============================================
    
    /**
     * Initialize animations for Contact Us page
     * Sequential animation: Form -> Contact Info -> Follow Us -> Map
     */
    function initContactPageAnimations() {
        // Only run on contact page
        const contactSection = document.querySelector('.contact-page-section');
        if (!contactSection) {
            return;
        }

        const formWrapper = contactSection.querySelector('.contact-form-wrapper');
        const contactInfoCard = contactSection.querySelector('.contact-info-card-side');
        const followUsCard = contactSection.querySelector('.follow-us-card-side');
        const mapWrapper = contactSection.querySelector('.map-wrapper');

        // Intersection Observer for viewport entry
        const observerOptions = {
            threshold: 0.1, // Trigger when 10% of section is visible
            rootMargin: '0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting && !entry.target.classList.contains('contact-section-animated')) {
                    entry.target.classList.add('contact-section-animated');
                    
                    // 1. Animate form wrapper first (Send us a Message)
                    if (formWrapper) {
                        setTimeout(function() {
                            formWrapper.classList.add('contact-animated');
                        }, 200);
                    }

                    // 2. Animate contact info card second
                    if (contactInfoCard) {
                        setTimeout(function() {
                            contactInfoCard.classList.add('contact-animated');
                        }, 500);
                    }

                    // 3. Animate follow us card third
                    if (followUsCard) {
                        setTimeout(function() {
                            followUsCard.classList.add('contact-animated');
                        }, 800);
                    }

                    // 4. Animate map wrapper last
                    if (mapWrapper) {
                        setTimeout(function() {
                            mapWrapper.classList.add('contact-animated');
                        }, 1100);
                    }

                    observer.unobserve(entry.target); // Stop observing once animated
                }
            });
        }, observerOptions);

        observer.observe(contactSection);
    }

    // Initialize contact page animations
    initContactPageAnimations();
});
