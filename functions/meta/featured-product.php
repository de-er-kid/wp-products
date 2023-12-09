<?php

if (!defined('ABSPATH')) {
    exit;
}

function wp_products_register_featured_meta() {
    add_meta_box( 'wp_products_featured_meta', __( 'Featured', 'wp-products-by-wbthnk' ), 'wp_products_featured_meta_callback', 'product' );
}
add_action( 'add_meta_boxes', 'wp_products_register_featured_meta' );

function wp_products_featured_meta_callback( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'wp_products_featured_nonce' );
    $featured = get_post_meta( $post->ID, '_featured', true );
    ?>
    <p>
        <label for="wp_products_featured"><?php _e( 'Featured Product', 'wp-products-by-wbthnk' );?></label>
        <select name="wp_products_featured" id="wp_products_featured">
            <option value="no" <?php selected( $featured, 'no' ); ?>><?php _e( 'No', 'wp-products-by-wbthnk' );?></option>
            <option value="yes" <?php selected( $featured, 'yes' ); ?>><?php _e( 'Yes', 'wp-products-by-wbthnk' );?></option>
        </select>
    </p>
    <?php
}

function wp_products_save_featured_meta( $post_id ) {
    if ( !isset( $_POST['wp_products_featured_nonce'] ) || !wp_verify_nonce( $_POST['wp_products_featured_nonce'], basename( __FILE__ ) ) ) {
        return $post_id;
    }
    $post_type = get_post_type( $post_id );
    if ( 'product' == $post_type ) {
        $featured = sanitize_text_field( $_POST['wp_products_featured'] );
        update_post_meta( $post_id, '_featured', $featured );
    }
}
add_action( 'save_post', 'wp_products_save_featured_meta' );
