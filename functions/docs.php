<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_menu', 'wp_products_by_wbthnk_add_menu');

function wp_products_by_wbthnk_add_menu() {
  add_submenu_page(
    'edit.php?post_type=product', // the parent menu slug
    'Docs for WP Products by webth.ink', // the page title
    'Docs', // the menu title
    'manage_options', // the capability required to access the page
    'wp-products-by-wbthnk-docs', // the unique menu slug
    'wp_products_by_wbthnk_docs_page' // the function to display the page
  );
}

function wp_products_by_wbthnk_docs_page() {
  ?>
  <div class="wrap">
    <h1>Docs for WP Products by webth.ink</h1>
    <p>Better documentations are coming in next version.</p>
  </div>
  <?php
}