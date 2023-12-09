<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register  product tags taxonomy
 *
 * @link https://developer.wordpress.org/reference/functions/register_taxonomy/
 */

 function wbthnk_register_taxonomy_product_tag() {
	$labels = array(
		'name'                       => __( 'Product Tags', 'wp-products-by-wbthnk' ),
		'singular_name'              => __( 'Product Tag', 'wp-products-by-wbthnk' ),
		'menu_name'                  => _x( 'Tags', 'Admin menu name', 'wp-products-by-wbthnk' ),
		'search_items'               => __( 'Search Product Tags', 'wp-products-by-wbthnk' ),
		'popular_items'              => __( 'Popular Product Tags', 'wp-products-by-wbthnk' ),
		'all_items'                  => __( 'All Product Tags', 'wp-products-by-wbthnk' ),
		'parent_item'                => __( 'Parent Product Tag', 'wp-products-by-wbthnk' ),
		'parent_item_colon'          => __( 'Parent Product Tag:', 'wp-products-by-wbthnk' ),
		'edit_item'                  => __( 'Edit Product Tag', 'wp-products-by-wbthnk' ),
		'view_item'                  => __( 'View Product Tag', 'wp-products-by-wbthnk' ),
		'update_item'                => __( 'Update Product Tag', 'wp-products-by-wbthnk' ),
		'add_new_item'               => __( 'Add New Product Tag', 'wp-products-by-wbthnk' ),
		'new_item_name'              => __( 'New Product Tag Name', 'wp-products-by-wbthnk' ),
		'separate_items_with_commas' => __( 'Separate tags with commas', 'wp-products-by-wbthnk' ),
		'add_or_remove_items'        => __( 'Add or remove tags', 'wp-products-by-wbthnk' ),
		'choose_from_most_used'      => __( 'Choose from the most used tags', 'wp-products-by-wbthnk' ),
		'not_found'                  => __( 'No tags found', 'wp-products-by-wbthnk' ),
		'no_terms'                   => __( 'No tags', 'wp-products-by-wbthnk' ),
		'items_list_navigation'      => __( 'Tags list navigation', 'wp-products-by-wbthnk' ),
		'items_list'                 => __( 'Tags list', 'wp-products-by-wbthnk' ),
	);

	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'public'                     => false,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => false,
		'show_tagcloud'              => false,
		'capabilities'              => array(
			'manage_terms' => 'manage_product_terms',
			'edit_terms'   => 'edit_product_terms',
			'delete_terms' => 'delete_product_terms',
			'assign_terms' => 'assign_product_terms',
		),
	);

	register_taxonomy( 'product_tag', array( 'product' ), $args );
}

add_action( 'init', 'wbthnk_register_taxonomy_product_tag', 5 );