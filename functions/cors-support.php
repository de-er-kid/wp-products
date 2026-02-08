<?php
/**
 * CORS Support for WP Products REST API
 * 
 * Allows cross-origin requests from external applications
 * 
 * @package WP_Products
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add CORS headers to REST API responses
 */
function wp_products_add_cors_headers() {
    // Remove default WordPress CORS headers
    remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
    
    // Add our CORS headers
    add_filter( 'rest_pre_serve_request', function( $value ) {
        header( 'Access-Control-Allow-Origin: *' );
        header( 'Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS' );
        header( 'Access-Control-Allow-Credentials: true' );
        header( 'Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Nonce' );
        header( 'Access-Control-Expose-Headers: X-WP-Total, X-WP-TotalPages' );
        
        return $value;
    });
}
add_action( 'rest_api_init', 'wp_products_add_cors_headers', 15 );

/**
 * Handle OPTIONS preflight requests
 */
function wp_products_handle_preflight() {
    if ( $_SERVER['REQUEST_METHOD'] === 'OPTIONS' ) {
        header( 'Access-Control-Allow-Origin: *' );
        header( 'Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS' );
        header( 'Access-Control-Allow-Credentials: true' );
        header( 'Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Nonce' );
        header( 'Access-Control-Max-Age: 86400' );
        exit;
    }
}
add_action( 'init', 'wp_products_handle_preflight' );