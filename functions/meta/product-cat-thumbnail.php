<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
// Add the thumbnail field to the product_cat taxonomy
function wp_taxonomy_thumbnail_field_add( $taxonomy ) {
    ?>
    <div class="form-field">
        <label for="thumbnail"><?php _e( 'Thumbnail', 'wp-products-by-wbthnk' ); ?></label>
        <input type="hidden" id="taxonomy_thumbnail" name="taxonomy_thumbnail" class="custom_media_url" value="">
        <button type="button" class="taxonomy_thumbnail_button button"><?php _e( 'Add Image', 'wp-products-by-wbthnk' ); ?></button>
        <img src="" id="taxonomy_thumbnail_preview" style="max-width: 100%; display: block; margin-top: 10px;">
    </div>
    <?php
}

function wp_taxonomy_thumbnail_field_edit( $term ) {
    $thumbnail_id = get_term_meta( $term->term_id, '_thumbnail_id', true );
    $thumbnail_url = $thumbnail_id ? wp_get_attachment_url( $thumbnail_id ) : '';
    ?>
    <div class="form-field">
        <label for="thumbnail"><?php _e( 'Thumbnail', 'wp-products-by-wbthnk' ); ?></label>
        <input type="hidden" id="taxonomy_thumbnail" name="taxonomy_thumbnail" class="custom_media_url" value="<?php echo esc_attr( $thumbnail_id ); ?>">
        <button type="button" class="taxonomy_thumbnail_button button"><?php _e( 'Add Image', 'wp-products-by-wbthnk' ); ?></button>
        <img src="<?php echo esc_attr( $thumbnail_url ); ?>" id="taxonomy_thumbnail_preview" style="max-width: 100%; display: block; margin-top: 10px;">
    </div>
    <?php
}
add_action( 'product_cat_add_form_fields', 'wp_taxonomy_thumbnail_field_add' );
add_action( 'product_cat_edit_form_fields', 'wp_taxonomy_thumbnail_field_edit' );

// Save the thumbnail field data when the taxonomy is saved
function wp_save_taxonomy_thumbnail_field( $term_id ) {
    if ( isset( $_POST['taxonomy_thumbnail'] ) ) {
        $thumbnail_id = absint( $_POST['taxonomy_thumbnail'] );
        update_term_meta( $term_id, '_thumbnail_id', $thumbnail_id );
    }
}
add_action( 'edited_product_cat', 'wp_save_taxonomy_thumbnail_field' );
add_action( 'create_product_cat', 'wp_save_taxonomy_thumbnail_field' );

// Enqueue scripts and styles for the thumbnail field
function wp_enqueue_taxonomy_thumbnail_scripts( $hook ) {
    if ( $hook == 'edit-tags.php' || $hook == 'term.php' ) {

        // Get the root plugin folder URL
        $plugin_url = plugin_dir_url(dirname(__FILE__, 2));

        wp_enqueue_media();
        wp_enqueue_script( 'taxonomy-thumbnail', $plugin_url . 'assets/js/taxonomy-thumbnail.js', array( 'jquery' ), '1.0', true );
        wp_localize_script( 'taxonomy-thumbnail', 'taxonomyThumbnail', array(
            'buttonText' => __( 'Add Image', 'wp-products-by-wbthnk' ),
            'frameTitle' => __( 'Select or Upload Media', 'wp-products-by-wbthnk' ),
        ) );
        wp_enqueue_style( 'taxonomy-thumbnail', $plugin_url . 'assets/css/taxonomy-thumbnail.css', array(), '1.0' );
        wp_add_inline_style( 'taxonomy-thumbnail', '.taxonomy_thumbnail_preview img { max-width: 50px; max-height: 50px; }' );
    }
}
add_action( 'admin_enqueue_scripts', 'wp_enqueue_taxonomy_thumbnail_scripts' );

/**
 * Display category thumbnail in custom column of product category taxonomy list.
 *
 * Taxonomy Key: 'product_cat' & Meta Key: '_thumbnail_id'
 *
 * @param array  $columns List of column objects.
 * @return array
 */
function custom_taxonomy_columns( $columns ) {
    $columns['taxonomy-image'] = __( 'Thumbnail', 'wp-products-by-wbthnk' );
    return $columns;
}
add_filter( 'manage_edit-product_cat_columns', 'custom_taxonomy_columns' );

/**
 * Display category thumbnail in custom column of product category taxonomy list.
 *
 * Taxonomy Key: 'product_cat' & Meta Key: '_thumbnail_id'
 *
 * @param string $content Column content.
 * @param string $column  Column ID.
 * @param int    $term_id Term ID.
 * @return string
 */
function custom_taxonomy_column_content( $content, $column, $term_id ) {
    if ( 'taxonomy-image' === $column ) {
        $thumbnail_id = get_term_meta( $term_id, '_thumbnail_id', true );
        if ( $thumbnail_id ) {
            $image = wp_get_attachment_image_src( $thumbnail_id, 'full' );
            if ( $image ) {
                $content = '<img src="' . esc_url( $image[0] ) . '" alt="' . esc_attr__( 'Category Image', 'wp-products-by-wbthnk' ) . '" />';
            }
        }
    }
    return $content;
}
add_filter( 'manage_product_cat_custom_column', 'custom_taxonomy_column_content', 10, 3 );
