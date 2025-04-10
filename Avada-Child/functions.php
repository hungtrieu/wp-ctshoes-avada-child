<?php
include_once get_stylesheet_directory() . '/woocommerce-filter.php';

function enqueue_custom_script() {
    $asset_version = '1.0.5'; // Phiên bản của bạn
    // Đảm bảo jQuery đã được tải
    wp_enqueue_script('jquery');

    wp_enqueue_style(
        'slick-carousel',
        'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css', 
        array(), 
        $asset_version
    );

    wp_enqueue_style(
        'slick-theme',
        'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css',
        array('slick-carousel'),
        $asset_version
    );
    // Tải file custom.js của bạn
    wp_enqueue_script(
        'slick-carousel',
        'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', 
        array('jquery'), 
        $asset_version, 
        true
    );
    wp_enqueue_script(
        'custom-js', 
        get_stylesheet_directory_uri() . '/custom.js', 
        array('jquery', 'slick-carousel'), 
        $asset_version, 
        true
    );
}
add_action('wp_enqueue_scripts', 'enqueue_custom_script');

function theme_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'avada-stylesheet' ) );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

function avada_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_child_theme_textdomain( 'Avada', $lang );
}
add_action( 'after_setup_theme', 'avada_lang_setup' );

function product_image_grid_4_shortcode() {
    global $product;

    if (!$product) {
        return '';
    }

    $images = [];

    // Lấy ảnh đại diện sản phẩm
    if (has_post_thumbnail($product->get_id())) {
        $images[] = get_the_post_thumbnail_url($product->get_id(), 'large');
    }

    // Lấy các ảnh từ product gallery
    $gallery_images = $product->get_gallery_image_ids();
    if (!empty($gallery_images)) {
        foreach (array_slice($gallery_images, 0, 3) as $image_id) {
            $images[] = wp_get_attachment_image_url($image_id, 'full');
        }
    }

    $image_count = count($images);
    if ($image_count === 0) {
        return '';
    }

    // Layout grid hiển thị ảnh thu nhỏ
    $grid_style = $image_count === 1
        ? 'width: 100%; max-width: 800px; margin: 0 auto;'
        : 'display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;';

    $output = '<div class="custom-product-image-grid" style="' . esc_attr($grid_style) . '">';

    foreach ($images as $index => $img_url) {
        $output .= '<div class="product-image-item" style="text-align: center;">
                        <img src="' . esc_url($img_url) . '" data-index="' . esc_attr($index) . '" class="popup-trigger" style="cursor:pointer; max-width:100%; height:auto;" />
                    </div>';
    }

    $output .= '</div>';

    // Overlay với carousel ảnh lớn
    $output .= '
    <div id="image-popup-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.85); z-index:9999; justify-content:center; align-items:center;">
        <div style="position:relative; max-width:90%; max-height:90%;">
            <button id="popup-close-btn" style="position:absolute; top:-30px; right:0; background:#fff; border:none; padding:5px 10px; cursor:pointer;">Đóng</button>
            <div class="popup-carousel">';
    
    foreach ($images as $img_url) {
        $output .= '<div><img src="' . esc_url($img_url) . '" style="max-height:80vh; max-width:100%; margin: 0 auto; display:block;" /></div>';
    }

    $output .= '   </div>
        </div>
    </div>';

    return $output;
}
add_shortcode('product-image-grid-4', 'product_image_grid_4_shortcode');