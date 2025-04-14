jQuery(document).ready(function () {
    let slickInitialized = false;
    
    jQuery('.popup-trigger').on('click', function () {
        var index = jQuery(this).data('index');
        jQuery('#image-popup-overlay').css('display', 'flex').hide().fadeIn();

        if (!slickInitialized) {
            jQuery('.popup-carousel').slick({
                infinite: true,
                slidesToShow: 1,
                slidesToScroll: 1,
                dots: false,
                arrows: true,
            });
            slickInitialized = true;
        } else {
            jQuery('.popup-carousel').slick('setPosition');
        }

        jQuery('.popup-carousel').slick('slickGoTo', index);
    });

    jQuery('#popup-close-btn').on('click', function () {
        jQuery('#image-popup-overlay').fadeOut();
    });
  
    jQuery('#image-popup-overlay').on('click', function (e) {
        if (e.target === this) {
            jQuery('#image-popup-overlay').fadeOut();
        }
    });

    function initMobileSlider() {
        if (window.innerWidth <= 767) {
            if (!jQuery('.mobile-slider').hasClass('slick-initialized')) {
                jQuery('.mobile-slider').slick({
                    arrows: true,
                    dots: false,
                    infinite: false,
                    slidesToShow: 1,
                });
            }
        } else {
            if (jQuery('.mobile-slider').hasClass('slick-initialized')) {
                jQuery('.mobile-slider').slick('unslick');
            }
        }
    }

    initMobileSlider();
    jQuery(window).on('resize', initMobileSlider);
});