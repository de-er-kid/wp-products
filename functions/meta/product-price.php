<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('add_meta_boxes', 'wbthnk_add_product_data_meta_box');
add_action('save_post_product', 'wbthnk_save_product_data_meta_box');

function wbthnk_enqueue_admin_scripts() {
    // Get the root plugin folder URL
    $plugin_url = plugin_dir_url(dirname(__FILE__, 2));

    // Enqueue scripts and styles
    wp_enqueue_style('wbthnk-admin-style', $plugin_url . 'assets/css/product-data.css');
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_script('wbthnk-admin-script', $plugin_url . 'assets/js/product-data.js', array('jquery-ui-datepicker'), '1.0', true);
}
add_action('admin_enqueue_scripts', 'wbthnk_enqueue_admin_scripts');

function wbthnk_add_product_data_meta_box() {
    add_meta_box(
        'wbthnk_product_data',
        __('Product Data', 'wp-products-by-wbthnk'),
        'wbthnk_render_product_data_meta_box',
        'product',
        'normal',
        'high'
    );
}

function wbthnk_render_product_data_meta_box() {
    global $post;

    $regular_price = get_post_meta($post->ID, '_regular_price', true);
    $sale_price = get_post_meta($post->ID, '_sale_price', true);
    $sale_price_dates_from = get_post_meta($post->ID, '_sale_price_dates_from', true);
    $sale_price_dates_to = get_post_meta($post->ID, '_sale_price_dates_to', true);

    // Use nonce for verification
    wp_nonce_field(plugin_basename(__FILE__), 'wbthnk_product_data_nonce');
?>

    <div class="pricing">
        <?php if (!empty($sale_price) && !empty($regular_price) && floatval($sale_price) > floatval($regular_price)) : ?>
            <div class="notice notice-error">
                <p><?php _e('Error: Sale price cannot be greater than the Regular price', 'wp-products-by-wbthnk'); ?></p>
            </div>
        <?php endif; ?>
        <div class="pricing-row">
            <div class="pricing-label">
                <label for="regular_price"><?php _e('Regular price:', 'wp-products-by-wbthnk'); ?></label>
            </div>
            <div class="pricing-field">
                <input type="text" id="regular_price" name="_regular_price" placeholder="<?php _e('0.00', 'wp-products-by-wbthnk'); ?>" value="<?php echo esc_attr($regular_price); ?>" />
            </div>
        </div>
        <div class="pricing-row">
            <div class="pricing-label">
                <label for="sale_price"><?php _e('Sale price:', 'wp-products-by-wbthnk'); ?></label>
            </div>
            <div class="pricing-field">
                <input type="text" id="sale_price" name="_sale_price" placeholder="<?php _e('0.00', 'wp-products-by-wbthnk'); ?>" value="<?php echo esc_attr($sale_price); ?>" />
            </div>
        </div>
        <div class="pricing-row">
            <div class="pricing-label">
                <label for="sale_price_dates_from"><?php _e('Sale price dates from:', 'wp-products-by-wbthnk'); ?></label>
            </div>
            <div class="pricing-field"><input type="text" id="sale_price_dates_from" name="_sale_price_dates_from" class="wbthnk-datepicker" placeholder="<?php _e('yyyy-mm-dd', 'wp-products-by-wbthnk'); ?>" value="<?php echo esc_attr($sale_price_dates_from); ?>" />
</div>
</div>
<div class="pricing-row">
<div class="pricing-label">
<label for="sale_price_dates_to"><?php _e('Sale price dates to:', 'wp-products-by-wbthnk'); ?></label>
</div>
<div class="pricing-field">
<input type="text" id="sale_price_dates_to" name="_sale_price_dates_to" class="wbthnk-datepicker" placeholder="<?php _e('yyyy-mm-dd', 'wp-products-by-wbthnk'); ?>" value="<?php echo esc_attr($sale_price_dates_to); ?>" />
</div>
</div>
</div>
<?php
}

function wbthnk_save_product_data_meta_box($post_id) {
    // Check if nonce is set
    if (!isset($_POST['wbthnk_product_data_nonce'])) {
        return;
    }
    // Verify nonce
    if (!wp_verify_nonce($_POST['wbthnk_product_data_nonce'], plugin_basename(__FILE__))) {
        return;
    }
    // Check if user has permissions to save data
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    // Save regular price
    if (isset($_POST['_regular_price'])) {
        update_post_meta($post_id, '_regular_price', sanitize_text_field($_POST['_regular_price']));
    }
    // Save sale price
    if (isset($_POST['_sale_price'])) {
        update_post_meta($post_id, '_sale_price', sanitize_text_field($_POST['_sale_price']));
    }
    // Save sale price dates from
    if (isset($_POST['_sale_price_dates_from'])) {
        update_post_meta($post_id, '_sale_price_dates_from', sanitize_text_field($_POST['_sale_price_dates_from']));
    }
    // Save sale price dates to
    if (isset($_POST['_sale_price_dates_to'])) {
        update_post_meta($post_id, '_sale_price_dates_to', sanitize_text_field($_POST['_sale_price_dates_to']));
    }
}

?>