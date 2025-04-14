<?php
include_once get_stylesheet_directory() . '/woocommerce-filter.php';

function enqueue_custom_script() {
	$js_version = '1.0.20';
    // Đảm bảo jQuery đã được tải
    wp_enqueue_script('jquery');

    wp_enqueue_style(
        'slick-carousel',
        'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css', 
        array(), 
        $js_version
    );

    wp_enqueue_style(
        'slick-theme',
        'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css',
        array('slick-carousel'),
        $js_version
    );
    // Tải file custom.js của bạn
    wp_enqueue_script(
        'slick-carousel',
        'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', 
        array('jquery'), 
        $js_version, 
        true
    );
    wp_enqueue_script(
        'custom-js', 
        get_stylesheet_directory_uri() . '/custom.js', 
        array('jquery', 'slick-carousel'), 
        $js_version, 
        true
    );
}
add_action('wp_enqueue_scripts', 'enqueue_custom_script');

function theme_enqueue_styles() {
	
	wp_enqueue_style(
        'font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css',
    );
	$css_version = '1.0.18';
    // Enqueue stylesheet của child theme
    wp_enqueue_style(
        'child-style',
        get_stylesheet_directory_uri() . '/style.css',
		['font-awesome'],
        $css_version
    );
}
add_action('wp_enqueue_scripts', 'theme_enqueue_styles');

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

    $images = $images_full = [];

    if (has_post_thumbnail($product->get_id())) {
        $images[] = get_the_post_thumbnail_url($product->get_id(), 'large');
        $images_full[] = get_the_post_thumbnail_url($product->get_id(), 'full');
    }

    $gallery_images = $product->get_gallery_image_ids();
    if (!empty($gallery_images)) {
        foreach (array_slice($gallery_images, 0, 3) as $image_id) {
            $images[] = wp_get_attachment_image_url($image_id, 'large');
            $images_full[] = wp_get_attachment_image_url($image_id, 'full');
        }
    }

    $image_count = count($images);
    if ($image_count === 0) {
        return '';
    }

    $container_class = $image_count === 1 ? 'single-image' : 'image-grid mobile-slider';

    $output = '<div class="product-image-wrapper">';
    $output .= '<div class="product-image-grid ' . esc_attr($container_class) . '">';

    foreach ($images as $index => $img_url) {
        $output .= '<div class="product-image-item">
                        <img src="' . esc_url($img_url) . '" data-index="' . esc_attr($index) . '" class="popup-trigger" />
                    </div>';
    }

    $output .= '</div></div>';

    // Overlay popup
    $output .= '
    <div id="image-popup-overlay" style="display:none;">
        <div class="popup-container">
            <button id="popup-close-btn"><i class="fa fa-times"></i></button>
            <div class="popup-carousel">';

    foreach ($images_full as $img_url) {
        $output .= '<div><img src="' . esc_url($img_url) . '" /></div>';
    }

    $output .= '</div></div></div>';

    return $output;
}
add_shortcode('product-image-grid-4', 'product_image_grid_4_shortcode');