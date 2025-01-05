<?php

/**
 * Class Product_Gallery_Metabox
 *
 * Adds a metabox for managing a gallery of images for products post type.
 */

class Product_Gallery_Metabox
{
    /**
     * Initializes hooks to add the metabox and save data.
     */
    // public static function init()
    // {
    //     add_action('add_meta_boxes', [self::class, 'add_gallery_metabox']);
    //     add_action('save_post', [self::class, 'save_gallery_metabox']);
    // }
    public function __construct()
    {
        add_action('add_meta_boxes', array($this, 'add_gallery_metabox'));
        add_action('save_post', array($this, 'save_gallery_metabox'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_media'));
    }

    /**
     * Registers the gallery metabox for all post types.
     */
    public function add_gallery_metabox()
    {
        $post_types = ['product'];
        foreach ($post_types as $post_type) {
            add_meta_box(
                $post_type . '_gallery',
                sprintf(__('Gallery (%s)', 'product-rental-cmc'), ucfirst($post_type)),
                array($this, 'render_gallery_metabox'),
                $post_type,
                'normal',
                'high'
            );
        }
    }

    /**
     * Enqueues the WordPress media scripts and styles.
     */
    public function enqueue_media($hook_suffix)
    {
        if ('post.php' !== $hook_suffix && 'post-new.php' !== $hook_suffix) {
            return;
        }
        wp_enqueue_media();
    }

    /**
     * Renders the gallery metabox content.
     *
     * @param WP_Post $post The current post object.
     */
    public function render_gallery_metabox($post)
    {
        $post_type = $post->post_type;
        wp_nonce_field($post_type . '_gallery_nonce_action', $post_type . '_gallery_nonce');

        $meta_key = "_{$post_type}_gallery_images";
        $gallery_images = get_post_meta($post->ID, $meta_key, true);
        $gallery_images = is_array($gallery_images) ? $gallery_images : [];

        echo '<div id="' . esc_attr($post_type) . '-gallery-wrapper">';
        foreach ($gallery_images as $image_id) {
            $image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
            echo '<div class="gallery-image">';
            echo '<img src="' . esc_url($image_url) . '" />';
            echo '<input type="hidden" name="' . esc_attr($post_type) . '_gallery_images[]" value="' . esc_attr($image_id) . '">';
            echo '<button class="remove-gallery-image button button-danger">X</button>';
            echo '</div>';
        }
        echo '</div>';
        echo '<button id="add-gallery-image" class="button">' . __('Add Image', 'product-rental-cmc') . '</button>';

?>
        <script>
            jQuery(document).ready(function($) {
                var frame;

                $('#<?php echo esc_js($post_type); ?>-gallery-wrapper').sortable({
                    items: '.gallery-image',
                    cursor: 'move',
                    containment: 'parent',
                    tolerance: 'pointer'
                });

                $('#add-gallery-image').on('click', function(e) {
                    e.preventDefault();

                    if (frame) {
                        frame.open();
                        return;
                    }

                    frame = wp.media({
                        title: '<?php echo esc_js(__('Select Images', 'product-rental-cmc')); ?>',
                        button: {
                            text: '<?php echo esc_js(__('Add to Gallery', 'product-rental-cmc')); ?>'
                        },
                        multiple: true
                    });

                    frame.on('select', function() {
                        var attachments = frame.state().get('selection').toJSON();
                        attachments.forEach(function(attachment) {
                            $('#<?php echo esc_js($post_type); ?>-gallery-wrapper').append(
                                '<div class="gallery-image">' +
                                '<img src="' + attachment.sizes.thumbnail.url + '" />' +
                                '<input type="hidden" name="<?php echo esc_js($post_type); ?>_gallery_images[]" value="' + attachment.id + '">' +
                                '<button class="remove-gallery-image button button-danger">X</button>' +
                                '</div>'
                            );
                        });
                    });

                    frame.open();
                });

                $(document).on('click', '.remove-gallery-image', function(e) {
                    e.preventDefault();
                    $(this).closest('.gallery-image').remove();
                });
            });
        </script>

        <style>
            .gallery-image {
                display: inline-block;
                margin: 5px;
                position: relative;
                cursor: move;
            }

            .gallery-image img {
                max-width: 100px;
                height: auto;
            }

            .remove-gallery-image {
                position: absolute;
                top: 5px;
                right: 5px;
            }

            #products-gallery-wrapper {
                border: 1px solid #ebebeb;
                padding: 10px;
                margin-bottom: 20px;
            }

            .remove-gallery-image.button.button-danger {
                padding: 0;
                border: 0;
                background-color: #fff;
                color: red;
                line-height: 0;
                font-size: 12px;
                width: 20px;
                max-height: 20px;
                font-weight: 700;
                min-height: 20px;
            }
        </style>
<?php
    }

    /**
     * Saves the gallery images when the post is saved.
     *
     * @param int $post_id The ID of the current post.
     */
    public function save_gallery_metabox($post_id)
    {
        $post_type = get_post_type($post_id);

        if (
            !isset($_POST[$post_type . '_gallery_nonce']) ||
            !wp_verify_nonce($_POST[$post_type . '_gallery_nonce'], $post_type . '_gallery_nonce_action')
        ) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $meta_key = "_{$post_type}_gallery_images";
        $gallery_images = isset($_POST[$post_type . '_gallery_images']) ? array_map('intval', $_POST[$post_type . '_gallery_images']) : [];
        update_post_meta($post_id, $meta_key, $gallery_images);
    }
}

new Product_Gallery_Metabox();