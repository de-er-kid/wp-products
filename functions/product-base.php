<?php

if (!defined('ABSPATH')) {
    exit;
}

// Register Product Post Type
function wbthnk_wp_products_post_type() {

	$labels = array(
		'name'                  => _x( 'Products', 'Post Type General Name', 'wp-products-by-wbthnk' ),
		'singular_name'         => _x( 'Product', 'Post Type Singular Name', 'wp-products-by-wbthnk' ),
		'menu_name'             => __( 'Products', 'wp-products-by-wbthnk' ),
		'name_admin_bar'        => __( 'Product', 'wp-products-by-wbthnk' ),
		'archives'              => __( 'Item Archives', 'wp-products-by-wbthnk' ),
		'attributes'            => __( 'Item Attributes', 'wp-products-by-wbthnk' ),
		'parent_item_colon'     => __( 'Parent Product:', 'wp-products-by-wbthnk' ),
		'all_items'             => __( 'All Products', 'wp-products-by-wbthnk' ),
		'add_new_item'          => __( 'Add New Product', 'wp-products-by-wbthnk' ),
		'add_new'               => __( 'New Product', 'wp-products-by-wbthnk' ),
		'new_item'              => __( 'New Item', 'wp-products-by-wbthnk' ),
		'edit_item'             => __( 'Edit Product', 'wp-products-by-wbthnk' ),
		'update_item'           => __( 'Update Product', 'wp-products-by-wbthnk' ),
		'view_item'             => __( 'View Product', 'wp-products-by-wbthnk' ),
		'view_items'            => __( 'View Items', 'wp-products-by-wbthnk' ),
		'search_items'          => __( 'Search products', 'wp-products-by-wbthnk' ),
		'not_found'             => __( 'No products found', 'wp-products-by-wbthnk' ),
		'not_found_in_trash'    => __( 'No products found in Trash', 'wp-products-by-wbthnk' ),
		'featured_image'        => __( 'Product Image', 'wp-products-by-wbthnk' ),
		'set_featured_image'    => __( 'Set product image', 'wp-products-by-wbthnk' ),
		'remove_featured_image' => __( 'Remove product image', 'wp-products-by-wbthnk' ),
		'use_featured_image'    => __( 'Use as product image', 'wp-products-by-wbthnk' ),
		'insert_into_item'      => __( 'Insert into item', 'wp-products-by-wbthnk' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'wp-products-by-wbthnk' ),
		'items_list'            => __( 'Items list', 'wp-products-by-wbthnk' ),
		'items_list_navigation' => __( 'Items list navigation', 'wp-products-by-wbthnk' ),
		'filter_items_list'     => __( 'Filter items list', 'wp-products-by-wbthnk' ),
	);
	$args = array(
		'label'                 => __( 'Product', 'wp-products-by-wbthnk' ),
		'description'           => __( 'Product information pages.', 'wp-products-by-wbthnk' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt'),
		'taxonomies'            => array( 'product_cat', 'product_tag' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 2,
		'menu_icon'				=> 'dashicons-cart',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
		'show_in_rest'          => false,
	);
	register_post_type( 'product', $args );

}
add_action( 'init', 'wbthnk_wp_products_post_type', 0 );

/**
 * Add Product Description heading above the main content editor on the 'product' post type.
 */
function wbthnk_add_editor_heading() {
    global $post, $wp_meta_boxes;

    if ( 'product' === $post->post_type ) {
        add_action( 'edit_form_after_title', 'wbthnk_render_editor_heading' );
    }
}

/**
 * Render Product Description heading.
 */
function wbthnk_render_editor_heading() {
    echo '<div class="postbox-header"><h2 class="hndle ui-sortable-handle" style="border: 1px solid;padding: 10px;border-bottom: 0px;font-size: 16px;font-weight: 600;">' . esc_html__( 'Product Description', 'wp-products-by-wbthnk' ) . '</h2></div>';
}

add_action( 'add_meta_boxes', 'wbthnk_add_editor_heading' );

/**
 * Product Short Description.
 */
function wbthnk_rename_metaboxes() {
    global $post, $wp_meta_boxes;

    // Rename the post excerpt meta box for product post type
    if ( 'product' === $post->post_type ) {
        $wp_meta_boxes['product']['normal']['core']['postexcerpt']['title'] = __( 'Product Short Description', 'wp-products-by-wbthnk' );
        $wp_meta_boxes['product']['normal']['core']['postexcerpt']['callback'] = 'wbthnk_product_short_description';
    }
}
add_action( 'add_meta_boxes', 'wbthnk_rename_metaboxes' );

function wbthnk_product_short_description( $post ) {
    wp_editor( htmlspecialchars_decode( $post->post_excerpt ), 'excerpt', array(
        'textarea_name' => 'excerpt',
        'media_buttons' => false,
        'wpautop' => false,
        'editor_height' => 100,
        'tinymce' => array(
            'plugins' => 'paste, charmap, colorpicker, textcolor',
            'toolbar1' => 'bold,italic,strikethrough,underline,|,bullist,numlist,blockquote,|,justifyleft,justifycenter,justifyright,|,link,unlink,|,pastetext,pasteword,removeformat,charmap,colorpicker,textcolor',
            'toolbar2' => '',
            'toolbar3' => '',
        ),
    ) );
}