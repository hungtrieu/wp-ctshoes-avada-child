jQuery(window).load(function () {
	let slickInitialized = false;
  
    jQuery('.custom-product-image-grid').on('click', '.popup-trigger', function () {
        jQuery('#image-popup-overlay').css('display', 'flex').hide().fadeIn();
		
        if (!slickInitialized) {
            jQuery('.popup-carousel').slick({
                infinite: true,
                slidesToShow: 1,
                slidesToScroll: 1,
                dots: true,
                arrows: true,
            });
            slickInitialized = true;
        } else {
            jQuery('.popup-carousel').slick('setPosition'); // cập nhật lại layout nếu đã init
        }

        const index = jQuery(this).data('index');
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
});