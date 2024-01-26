<?php
/*
 * Plugin Name:       WP Products
 * Plugin URI:        https://sinan.pro/plugins/wp-products
 * Description:       A minimal plugin for on the go development for an eCommerce WordPress with no bloat. This plugin only focuses on product post type, product category taxonomy & the least meta boxes needed.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Sinan
 * Author URI:        mailto:info@sinan.pro
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://wp.sinan.pro/plugins/wp-products
 * Text Domain:       wp-products-by-wbthnk
 * Domain Path:       /languages
 */

 
if (!defined('ABSPATH')) {
    exit;
}

//  Require all files here
 require_once plugin_dir_path(__FILE__) . 'functions/product-base.php';
 require_once plugin_dir_path(__FILE__) . 'functions/product-cat-tax.php';
 require_once plugin_dir_path(__FILE__) . 'functions/product-tag-tax.php';
 require_once plugin_dir_path(__FILE__) . 'functions/products-admin-ui.php';

 require_once plugin_dir_path(__FILE__) . 'functions/meta/product-price.php';
 require_once plugin_dir_path(__FILE__) . 'functions/meta/featured-product.php';
 require_once plugin_dir_path(__FILE__) . 'functions/meta/product-cat-thumbnail.php';

 require_once plugin_dir_path(__FILE__) . 'functions/store-settings.php';
 require_once plugin_dir_path(__FILE__) . 'functions/docs.php';
 require_once plugin_dir_path(__FILE__) . 'functions/store-currency-sign.php';


//  require_once plugin_dir_path(__FILE__) . 'functions/.php';