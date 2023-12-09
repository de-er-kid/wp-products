<?php

if (!defined('ABSPATH')) {
    exit;
}

// Add custom columns to the product list table
add_filter( 'manage_edit-product_columns', 'wbthnk_custom_product_columns' );
function wbthnk_custom_product_columns( $columns ) {
    $new_columns = array();

    // Add Product Image, Name, Categories, Tags, Featured, Price and Date columns
    $new_columns['cb'] = '<input type="checkbox" />';
    $new_columns['product_image'] = __( 'Image', 'wp-products-by-wbthnk' );
    $new_columns['title'] = __( 'Name', 'wp-products-by-wbthnk' );
    $new_columns['product_categories'] = __( 'Categories', 'wp-products-by-wbthnk' );
    $new_columns['product_tags'] = __( 'Tags', 'wp-products-by-wbthnk' );
    $new_columns['product_featured'] = __( 'Featured', 'wp-products-by-wbthnk' );
    $new_columns['product_price'] = __( 'Price', 'wp-products-by-wbthnk' );
    $new_columns['date'] = __( 'Date', 'wp-products-by-wbthnk' );

    return $new_columns;
}

// Output content for custom columns
add_action( 'manage_product_posts_custom_column', 'wbthnk_custom_product_column_content' );
function wbthnk_custom_product_column_content( $column ) {
    global $post;

    switch ( $column ) {
        case 'product_image':
            if ( has_post_thumbnail( $post->ID ) ) {
                echo get_the_post_thumbnail( $post->ID, array( 50, 50 ) );
            }
            break;

        case 'product_categories':
            $categories = get_the_terms( $post->ID, 'product_cat' );
            if ( ! empty( $categories ) ) {
                $category_names = array();
                foreach ( $categories as $category ) {
                    $category_names[] = $category->name;
                }
                echo implode( ', ', $category_names );
            }
            break;

        case 'product_tags':
            $tags = get_the_terms( $post->ID, 'product_tag' );
            if ( ! empty( $tags ) ) {
                $tag_names = array();
                foreach ( $tags as $tag ) {
                    $tag_names[] = $tag->name;
                }
                echo implode( ', ', $tag_names );
            }
            break;

        case 'product_featured':
            $is_featured = get_post_meta( $post->ID, '_featured', true );
            $post_id = $post->ID;
            if ( $is_featured == 'yes' ) {
                echo '<span class="wbthnk-featured-action" data-post-id="' . esc_attr( $post_id ) . '"><span class="wbthnk-featured dashicons dashicons-star-filled" title="' . esc_attr__( 'Remove Featured', 'wp-products-by-wbthnk' ) . '"></span></span>';
            } else {
                echo '<span class="wbthnk-featured-action" data-post-id="' . esc_attr( $post_id ) . '"><span class="wbthnk-featured dashicons dashicons-star-empty" title="' . esc_attr__( 'Make Featured', 'wp-products-by-wbthnk' ) . '"></span></span>';
            }
            break;
            

        case 'product_price':
            $regular_price = get_post_meta( $post->ID, '_regular_price', true );
            $sale_price = get_post_meta( $post->ID, '_sale_price', true );
            global $store_currency_sign;

            if ( $sale_price ) {
                echo '<del>' . $store_currency_sign . number_format($regular_price, 2) . '</del> ' . $store_currency_sign . number_format($sale_price, 2);
            } else {
                echo $store_currency_sign . number_format($regular_price, 2);
            }
            break;
    }
}

// Make the "Featured" column sortable
add_filter( 'manage_edit-product_sortable_columns', 'wbthnk_product_sortable_columns' );
function wbthnk_product_sortable_columns( $columns ) {
    $columns['product_featured'] = 'featured';
    return $columns;
}

// Handle sorting by the "Featured" column
add_action( 'pre_get_posts', 'wbthnk_product_sort_featured' );
function wbthnk_product_sort_featured( $query ) {
    if ( ! is_admin() ) {
        return;
    }

    $orderby = $query->get( 'orderby' );
    if ( 'featured' == $orderby ) {
        $query->set( 'meta_key', '_featured' );
        $query->set( 'orderby', 'meta_value' );
    }
}

// Handle making a product featured from the list table
add_action( 'wp_ajax_wbthnk_make_product_featured', 'wbthnk_make_product_featured' );
function wbthnk_make_product_featured() {
    $post_id = absint( $_POST['post_id'] );
    $is_featured = get_post_meta( $post_id, '_featured', true );

    if ( $is_featured == 'yes' ) {
        delete_post_meta( $post_id, '_featured' );
    } else {
        update_post_meta( $post_id, '_featured', 'yes' );
    }

    wp_send_json_success();
}

// Enqueue scripts and styles for the featured column
add_action( 'admin_enqueue_scripts', 'wbthnk_enqueue_featured_scripts' );
function wbthnk_enqueue_featured_scripts() {
    global $typenow;

    if ( 'product' == $typenow ) {
        wp_enqueue_script( 'wbthnk-product-featured', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/product-featured.js', array( 'jquery' ), '1.0.0', true );
        wp_localize_script( 'wbthnk-product-featured', 'wbthnk_product_featured', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'wbthnk_make_product_featured' )
        ) );
        wp_enqueue_style( 'wbthnk-product-featured', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/css/product-featured.css', array(), '1.0.0' );
    }
}