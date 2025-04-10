jQuery(document).ready(function ($) {
    let slickInitialized = false;

    // Khi người dùng click vào ảnh thumbnail
    $('.custom-product-image-grid').on('click', '.popup-trigger', function () {
        $('#image-popup-overlay').fadeIn();

        // Khởi tạo slick carousel nếu chưa
        if (!slickInitialized) {
            $('.popup-carousel').slick({
                infinite: true,
                slidesToShow: 1,
                slidesToScroll: 1,
                dots: true,
                arrows: true,
            });
            slickInitialized = true;
        }

        // Hiển thị đúng slide
        const index = $(this).data('index');
        $('.popup-carousel').slick('slickGoTo', index);
    });

    // Đóng popup
    $('#popup-close-btn').on('click', function () {
        $('#image-popup-overlay').fadeOut();
    });
});