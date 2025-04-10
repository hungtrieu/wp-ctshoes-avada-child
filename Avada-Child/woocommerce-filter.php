<?php

// Thêm shortcode hiển thị bộ lọc
add_shortcode('woocommerce_custom_filter', 'woocommerce_custom_filter_callback');

function woocommerce_custom_filter_callback() {
    ob_start();

    // Lấy URL hiện tại
    global $wp_query, $wp;
    $current_url = home_url(add_query_arg(array(), $wp->request));
    $current_category = $wp_query->get_queried_object();

    // Lấy các filter đã chọn
    $selected_filters = [];
    foreach ($_GET as $key => $value) {
        if (strpos($key, 'filter_') === 0) {
            $selected_filters[$key] = explode(',', sanitize_text_field($value));
        }
    }

    // Lấy thuộc tính sản phẩm hiện có trong danh mục
    $attribute_slugs = [];

    if ($current_category && isset($current_category->term_id)) {
        $category_id = $current_category->term_id;
        $category_attributes = wc_get_attribute_taxonomies();
        foreach ($category_attributes as $attribute) {
            $attribute_slugs[] = $attribute->attribute_name;
        }
        
        // Lấy danh sách danh mục con nếu có
        $categories = get_terms([
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
            'parent' => $category_id
        ]);

        $show_categories = !empty($categories);

    } else {
        $categories = get_terms([
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
            'parent' => 0
        ]);
        $show_categories = true;
    }

    // Lấy tất cả các thuộc tính được sắp xếp theo attribute_id từ mới đến cũ
    $attributes = wc_get_attribute_taxonomies();
    usort($attributes, function($a, $b) {
        return $b->attribute_id - $a->attribute_id;
    });
    ?>

    <div class="filter-toggle-wrapper">
        <?php if ($show_categories): ?>
            <a href="#" class="category-toggle">DANH MỤC</a>
        <?php endif; ?>
        <a href="#" class="filter-toggle">BỘ LỌC</a>
		<a href="#" class="sort-toggle">SẮP XẾP</a>
        <a href="<?php echo $current_url; ?>" class="view-all">Xem tất cả</a>

        <?php if ($show_categories): ?>
            <div class="category-dropdown hidden">
                <h4>Danh mục sản phẩm</h4>
                <?php foreach ($categories as $category): ?>
                    <div>
                        <input type="checkbox" class="category-checkbox" data-url="<?php echo get_term_link($category); ?>" />
                        <label><a href="<?php echo get_term_link($category); ?>"><?php echo $category->name; ?></a></label>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="filter-dropdown hidden">
            <h4>Bộ lọc sản phẩm</h4>
            <?php foreach ($attributes as $attribute):
                $input_type = $attribute->attribute_type === 'avada_button' ? 'button' : 'checkbox';
            ?>
                <div>
                    <h5><?php echo wc_attribute_label($attribute->attribute_name); ?></h5>
                    <?php
                        $terms = get_terms([ 'taxonomy' => 'pa_' . $attribute->attribute_name, 'hide_empty' => false ]);
                        foreach ($terms as $term):
                            if ($input_type === 'button'): ?>
                                <button class="filter-button" data-filter-name="<?php echo $attribute->attribute_name; ?>" data-filter-value="<?php echo $term->slug; ?>">
                                    <?php echo $term->name; ?>
                                </button>
                        <?php else: ?>
                            <label>
                                <input type="checkbox" name="filter_<?php echo $attribute->attribute_name; ?>" value="<?php echo $term->slug; ?>">
                                <?php echo $term->name; ?>
                            </label>
                        <?php 
                            endif; 
                        endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
		
		<div class="sort-dropdown hidden">
            <h4>Sắp xếp sản phẩm</h4>
            <ul>
                <li><a href="<?php echo add_query_arg('orderby', 'menu_order', $current_url); ?>">Mặc định</a></li>
                <li><a href="<?php echo add_query_arg('orderby', 'popularity', $current_url); ?>">Phổ biến</a></li>
                <li><a href="<?php echo add_query_arg('orderby', 'date', $current_url); ?>">Mới nhất</a></li>
                <li><a href="<?php echo add_query_arg('orderby', 'price', $current_url); ?>">Giá: Thấp đến Cao</a></li>
                <li><a href="<?php echo add_query_arg('orderby', 'price-desc', $current_url); ?>">Giá: Cao đến Thấp</a></li>
            </ul>
        </div>
    </div>



    <style>
        .filter-toggle-wrapper { position: relative; z-index: 9999; }
        .filter-dropdown, .category-dropdown, .sort-dropdown { position: absolute; top: 40px; left: 0; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); z-index: 9999; }
        .filter-dropdown.hidden, .category-dropdown.hidden, .sort-dropdown.hidden { display: none; }
		.filter-toggle-wrapper .fade { opacity: 0; transition: opacity 0.3s; }
        .filter-toggle-wrapper .fade.show { opacity: 1; }
        .filter-toggle-wrapper .view-all { margin-left: 20px; cursor: pointer; color: #000; text-decoration: underline; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dropdowns = document.querySelectorAll('.filter-dropdown, .category-dropdown, .sort-dropdown');
            const toggleButtons = document.querySelectorAll('.filter-toggle, .category-toggle, .sort-toggle');

            toggleButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    dropdowns.forEach(dropdown => {
                        dropdown.classList.add('hidden');
                        dropdown.classList.remove('show');
                    });
                    const targetDropdown = document.querySelector(this.classList.contains('filter-toggle') ? '.filter-dropdown' : this.classList.contains('category-toggle') ? '.category-dropdown' : '.sort-dropdown');
                    targetDropdown.classList.toggle('hidden');
                    setTimeout(() => targetDropdown.classList.toggle('show'), 100);
                });
            });
        });
    </script>

    <?php
    return ob_get_clean();
}

