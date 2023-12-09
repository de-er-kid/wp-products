<?php

if (!defined('ABSPATH')) {
    exit;
}

// Register Custom Taxonomy
function wbthnk_product_category() {

	$labels = array(
		'name'                       => _x( 'Product Categories', 'wp-products-by-wbthnk' ),
		'singular_name'              => _x( 'Product Category', 'wp-products-by-wbthnk' ),
		'menu_name'                  => __( 'Categories', 'wp-products-by-wbthnk' ),
		'all_items'                  => __( 'All Items', 'wp-products-by-wbthnk' ),
		'parent_item'                => __( 'Parent Item', 'wp-products-by-wbthnk' ),
		'parent_item_colon'          => __( 'Parent Item:', 'wp-products-by-wbthnk' ),
		'new_item_name'              => __( 'New Item Name', 'wp-products-by-wbthnk' ),
		'add_new_item'               => __( 'Add New Item', 'wp-products-by-wbthnk' ),
		'edit_item'                  => __( 'Edit Item', 'wp-products-by-wbthnk' ),
		'update_item'                => __( 'Update Item', 'wp-products-by-wbthnk' ),
		'view_item'                  => __( 'View Item', 'wp-products-by-wbthnk' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'wp-products-by-wbthnk' ),
		'add_or_remove_items'        => __( 'Add or remove items', 'wp-products-by-wbthnk' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'wp-products-by-wbthnk' ),
		'popular_items'              => __( 'Popular Items', 'wp-products-by-wbthnk' ),
		'search_items'               => __( 'Search Items', 'wp-products-by-wbthnk' ),
		'not_found'                  => __( 'Not Found', 'wp-products-by-wbthnk' ),
		'no_terms'                   => __( 'No items', 'wp-products-by-wbthnk' ),
		'items_list'                 => __( 'Items list', 'wp-products-by-wbthnk' ),
		'items_list_navigation'      => __( 'Items list navigation', 'wp-products-by-wbthnk' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'product_cat', array( 'product' ), $args );

}
add_action( 'init', 'wbthnk_product_category', 0 );