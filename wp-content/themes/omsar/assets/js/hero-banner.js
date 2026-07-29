// Hero Banner Carousel Initialization
$(document).ready(function(){

    var heroAnimationTimers = [];

    function clearHeroAnimationTimers() {
        heroAnimationTimers.forEach(clearTimeout);
        heroAnimationTimers = [];
    }

    // Animate hero elements sequentially (title → description → button)
    function animateHeroElements($item) {
        if (!$item || !$item.length) {
            return;
        }

        clearHeroAnimationTimers();

        var $title = $item.find('.hero-title');
        var $description = $item.find('.hero-description');
        var $button = $item.find('.btn-custom');

        $title.removeClass('hero-animated');
        $description.removeClass('hero-animated');
        $button.removeClass('hero-animated');

        if ($item[0]) {
            $item[0].offsetHeight;
        }

        if ($title.length) {
            heroAnimationTimers.push(setTimeout(function() {
                $title.addClass('hero-animated');
            }, 100));
        }
        if ($description.length) {
            heroAnimationTimers.push(setTimeout(function() {
                $description.addClass('hero-animated');
            }, 400));
        }
        if ($button.length) {
            heroAnimationTimers.push(setTimeout(function() {
                $button.addClass('hero-animated');
            }, 700));
        }
    }

    function initHeroIntroAnimation($carousel) {
        var introComplete = false;

        function markIntroComplete() {
            if (introComplete) {
                return;
            }
            introComplete = true;
            $carousel.addClass('hero-text-static');
        }

        function playIntro() {
            animateHeroElements($carousel.find('.owl-item.active .item'));
            heroAnimationTimers.push(setTimeout(markIntroComplete, 900));
        }

        setTimeout(playIntro, 200);

        $carousel.on('translated.owl.carousel', function() {
            clearHeroAnimationTimers();
            markIntroComplete();
        });
    }

    function setupHeroAutoplay($carousel, autoplayTimeout, $dotsContainer) {
        var autoplayTimer = null;

        function clearAutoplayTimer() {
            if (autoplayTimer) {
                clearTimeout(autoplayTimer);
                autoplayTimer = null;
            }
            $carousel.trigger('stop.owl.autoplay');
        }

        function scheduleAutoplay() {
            clearAutoplayTimer();
            autoplayTimer = setTimeout(function() {
                autoplayTimer = null;
                $carousel.trigger('next.owl.carousel');
            }, autoplayTimeout);
        }

        $carousel.on('translate.owl.carousel', clearAutoplayTimer);
        $carousel.on('translated.owl.carousel', scheduleAutoplay);

        if ($dotsContainer && $dotsContainer.length) {
            $dotsContainer.on('click', '.owl-dot', clearAutoplayTimer);
        }

        scheduleAutoplay();
    }

    // Initialize full carousel (with background images/videos) - MODE 2
    $(".hero-banner-carousel:not(.hero-banner-text-only)").each(function() {
        var $carousel = $(this);
        var slideCount = parseInt($carousel.data('slide-count'), 10) || $carousel.find('.item').length;
        var isSingleSlide = slideCount <= 1;
        var autoplayTimeout = 6000;

        $carousel.owlCarousel({
            items: 1,
            loop: slideCount > 2,
            rewind: !isSingleSlide && slideCount <= 2,
            autoplay: false,
            autoplayHoverPause: false,
            nav: false,
            dots: !isSingleSlide,
            smartSpeed: 1000,
            onInitialized: function(event) {
                initHeroIntroAnimation($(event.target));
            }
        });

        if (!isSingleSlide) {
            setupHeroAutoplay($carousel, autoplayTimeout);
        }
    });

    // Initialize text-only carousel (static background, only text slides)
    $(".hero-banner-text-only").each(function() {
        var $carousel = $(this);
        var $heroSection = $carousel.closest('.hero-section-static');
        var slideCount = parseInt($carousel.data('slide-count'), 10) || $carousel.find('.item').length;
        var isSingleSlide = slideCount <= 1;
        var autoplayTimeout = 6000;
        var $dotsContainer = false;

        if ($heroSection.length && !isSingleSlide) {
            $heroSection.children('.owl-dots').remove();
            $dotsContainer = $('<div class="owl-dots owl-theme"></div>');
            $heroSection.append($dotsContainer);
        }

        $carousel.owlCarousel({
            items: 1,
            loop: slideCount > 2,
            rewind: !isSingleSlide && slideCount <= 2,
            autoplay: false,
            autoplayHoverPause: false,
            nav: false,
            dots: !isSingleSlide,
            dotsContainer: $dotsContainer || false,
            smartSpeed: 1000,
            onInitialized: function(event) {
                var $carousel = $(event.target);
                setTimeout(function() {
                    $carousel.find('.owl-dots').remove();
                }, 200);
                initHeroIntroAnimation($carousel);
            }
        });

        if (!isSingleSlide) {
            setupHeroAutoplay($carousel, autoplayTimeout, $dotsContainer);
        }
    });

});


// Stop default hero background video from looping
$(document).ready(function () {
    var video = document.querySelector('.hero-bg-video-default');

    if (video) {
        video.addEventListener('ended', function () {
            video.pause();
            video.currentTime = video.duration;
        });
    }
});

$(document).ready(function () {
    var video = document.querySelector('.hero-bg-video');

    if (video) {
        video.addEventListener('ended', function () {
            video.pause();
            video.currentTime = video.duration;
        });
    }
});
