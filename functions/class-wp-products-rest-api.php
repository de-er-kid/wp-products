<?php
/**
 * WP Products REST API
 * 
 * Handles REST API endpoints for products and product categories
 * 
 * @package WP_Products
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WP_Products_REST_API {

    /**
     * Namespace for the REST API
     *
     * @var string
     */
    private $namespace = 'wp-products/v1';

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    /**
     * Register REST API routes
     */
    public function register_routes() {
        
        // Products endpoints
        register_rest_route( $this->namespace, '/products', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_products' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' ),
                'args'                => $this->get_collection_params(),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'create_product' ),
                'permission_callback' => array( $this, 'create_item_permissions_check' ),
                'args'                => $this->get_product_schema(),
            ),
        ) );

        register_rest_route( $this->namespace, '/products/(?P<id>[\d]+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_product' ),
                'permission_callback' => array( $this, 'get_item_permissions_check' ),
                'args'                => array(
                    'id' => array(
                        'validate_callback' => function( $param ) {
                            return is_numeric( $param );
                        }
                    ),
                ),
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'update_product' ),
                'permission_callback' => array( $this, 'update_item_permissions_check' ),
                'args'                => $this->get_product_schema(),
            ),
            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => array( $this, 'delete_product' ),
                'permission_callback' => array( $this, 'delete_item_permissions_check' ),
            ),
        ) );

        // Product Categories endpoints
        register_rest_route( $this->namespace, '/categories', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_categories' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' ),
                'args'                => $this->get_category_collection_params(),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'create_category' ),
                'permission_callback' => array( $this, 'create_item_permissions_check' ),
                'args'                => $this->get_category_schema(),
            ),
        ) );

        register_rest_route( $this->namespace, '/categories/(?P<id>[\d]+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_category' ),
                'permission_callback' => array( $this, 'get_item_permissions_check' ),
                'args'                => array(
                    'id' => array(
                        'validate_callback' => function( $param ) {
                            return is_numeric( $param );
                        }
                    ),
                ),
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'update_category' ),
                'permission_callback' => array( $this, 'update_item_permissions_check' ),
                'args'                => $this->get_category_schema(),
            ),
            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => array( $this, 'delete_category' ),
                'permission_callback' => array( $this, 'delete_item_permissions_check' ),
            ),
        ) );

        // Products by category
        register_rest_route( $this->namespace, '/categories/(?P<id>[\d]+)/products', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_products_by_category' ),
            'permission_callback' => array( $this, 'get_items_permissions_check' ),
            'args'                => array(
                'id' => array(
                    'validate_callback' => function( $param ) {
                        return is_numeric( $param );
                    }
                ),
                'per_page' => array(
                    'default'  => 10,
                    'sanitize_callback' => 'absint',
                ),
                'page' => array(
                    'default'  => 1,
                    'sanitize_callback' => 'absint',
                ),
            ),
        ) );
    }

    /**
     * Get products
     */
    public function get_products( $request ) {
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => $request->get_param( 'per_page' ) ?: 10,
            'paged'          => $request->get_param( 'page' ) ?: 1,
            'post_status'    => $request->get_param( 'status' ) ?: 'publish',
            'orderby'        => $request->get_param( 'orderby' ) ?: 'date',
            'order'          => $request->get_param( 'order' ) ?: 'DESC',
        );

        // Search
        if ( $request->get_param( 'search' ) ) {
            $args['s'] = sanitize_text_field( $request->get_param( 'search' ) );
        }

        // Filter by category
        if ( $request->get_param( 'category' ) ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => absint( $request->get_param( 'category' ) ),
                ),
            );
        }

        $query = new WP_Query( $args );
        $products = array();

        foreach ( $query->posts as $post ) {
            $products[] = $this->prepare_product_response( $post );
        }

        $response = rest_ensure_response( $products );
        
        // Add pagination headers
        $total = $query->found_posts;
        $max_pages = $query->max_num_pages;
        
        $response->header( 'X-WP-Total', $total );
        $response->header( 'X-WP-TotalPages', $max_pages );

        return $response;
    }

    /**
     * Get single product
     */
    public function get_product( $request ) {
        $id = $request->get_param( 'id' );
        $post = get_post( $id );

        if ( ! $post || $post->post_type !== 'product' ) {
            return new WP_Error( 
                'product_not_found', 
                __( 'Product not found.', 'wp-products' ), 
                array( 'status' => 404 ) 
            );
        }

        return rest_ensure_response( $this->prepare_product_response( $post ) );
    }

    /**
     * Create product
     */
    public function create_product( $request ) {
        $prepared = $this->prepare_product_for_database( $request );

        $post_id = wp_insert_post( $prepared['post_data'], true );

        if ( is_wp_error( $post_id ) ) {
            return $post_id;
        }

        // Save meta data
        if ( ! empty( $prepared['meta_data'] ) ) {
            foreach ( $prepared['meta_data'] as $meta_key => $meta_value ) {
                update_post_meta( $post_id, $meta_key, $meta_value );
            }
        }

        // Set categories
        if ( ! empty( $prepared['categories'] ) ) {
            wp_set_object_terms( $post_id, $prepared['categories'], 'product_cat' );
        }

        $post = get_post( $post_id );
        
        $response = $this->prepare_product_response( $post );
        $response = rest_ensure_response( $response );
        $response->set_status( 201 );

        return $response;
    }

    /**
     * Update product
     */
    public function update_product( $request ) {
        $id = $request->get_param( 'id' );
        $post = get_post( $id );

        if ( ! $post || $post->post_type !== 'product' ) {
            return new WP_Error( 
                'product_not_found', 
                __( 'Product not found.', 'wp-products' ), 
                array( 'status' => 404 ) 
            );
        }

        $prepared = $this->prepare_product_for_database( $request );
        $prepared['post_data']['ID'] = $id;

        $post_id = wp_update_post( $prepared['post_data'], true );

        if ( is_wp_error( $post_id ) ) {
            return $post_id;
        }

        // Update meta data
        if ( ! empty( $prepared['meta_data'] ) ) {
            foreach ( $prepared['meta_data'] as $meta_key => $meta_value ) {
                update_post_meta( $post_id, $meta_key, $meta_value );
            }
        }

        // Update categories
        if ( ! empty( $prepared['categories'] ) ) {
            wp_set_object_terms( $post_id, $prepared['categories'], 'product_cat' );
        }

        $post = get_post( $post_id );
        
        return rest_ensure_response( $this->prepare_product_response( $post ) );
    }

    /**
     * Delete product
     */
    public function delete_product( $request ) {
        $id = $request->get_param( 'id' );
        $post = get_post( $id );

        if ( ! $post || $post->post_type !== 'product' ) {
            return new WP_Error( 
                'product_not_found', 
                __( 'Product not found.', 'wp-products' ), 
                array( 'status' => 404 ) 
            );
        }

        $previous = $this->prepare_product_response( $post );
        $result = wp_delete_post( $id, true );

        if ( ! $result ) {
            return new WP_Error( 
                'cant_delete', 
                __( 'The product cannot be deleted.', 'wp-products' ), 
                array( 'status' => 500 ) 
            );
        }

        return rest_ensure_response( array(
            'deleted' => true,
            'previous' => $previous,
        ) );
    }

    /**
     * Get categories
     */
    public function get_categories( $request ) {
        $args = array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => $request->get_param( 'hide_empty' ) !== false,
            'number'     => $request->get_param( 'per_page' ) ?: 10,
            'offset'     => ( ( $request->get_param( 'page' ) ?: 1 ) - 1 ) * ( $request->get_param( 'per_page' ) ?: 10 ),
            'orderby'    => $request->get_param( 'orderby' ) ?: 'name',
            'order'      => $request->get_param( 'order' ) ?: 'ASC',
        );

        if ( $request->get_param( 'search' ) ) {
            $args['search'] = sanitize_text_field( $request->get_param( 'search' ) );
        }

        if ( $request->get_param( 'parent' ) ) {
            $args['parent'] = absint( $request->get_param( 'parent' ) );
        }

        $terms = get_terms( $args );

        if ( is_wp_error( $terms ) ) {
            return $terms;
        }

        $categories = array();
        foreach ( $terms as $term ) {
            $categories[] = $this->prepare_category_response( $term );
        }

        return rest_ensure_response( $categories );
    }

    /**
     * Get single category
     */
    public function get_category( $request ) {
        $id = $request->get_param( 'id' );
        $term = get_term( $id, 'product_cat' );

        if ( is_wp_error( $term ) || ! $term ) {
            return new WP_Error( 
                'category_not_found', 
                __( 'Category not found.', 'wp-products' ), 
                array( 'status' => 404 ) 
            );
        }

        return rest_ensure_response( $this->prepare_category_response( $term ) );
    }

    /**
     * Create category
     */
    public function create_category( $request ) {
        $args = array(
            'name'        => $request->get_param( 'name' ),
            'slug'        => $request->get_param( 'slug' ),
            'description' => $request->get_param( 'description' ),
            'parent'      => $request->get_param( 'parent' ) ?: 0,
        );

        $result = wp_insert_term( $args['name'], 'product_cat', $args );

        if ( is_wp_error( $result ) ) {
            return $result;
        }

        $term = get_term( $result['term_id'], 'product_cat' );
        
        $response = $this->prepare_category_response( $term );
        $response = rest_ensure_response( $response );
        $response->set_status( 201 );

        return $response;
    }

    /**
     * Update category
     */
    public function update_category( $request ) {
        $id = $request->get_param( 'id' );
        $term = get_term( $id, 'product_cat' );

        if ( is_wp_error( $term ) || ! $term ) {
            return new WP_Error( 
                'category_not_found', 
                __( 'Category not found.', 'wp-products' ), 
                array( 'status' => 404 ) 
            );
        }

        $args = array();
        
        if ( $request->get_param( 'name' ) ) {
            $args['name'] = $request->get_param( 'name' );
        }
        
        if ( $request->get_param( 'slug' ) ) {
            $args['slug'] = $request->get_param( 'slug' );
        }
        
        if ( $request->get_param( 'description' ) ) {
            $args['description'] = $request->get_param( 'description' );
        }
        
        if ( $request->get_param( 'parent' ) !== null ) {
            $args['parent'] = absint( $request->get_param( 'parent' ) );
        }

        $result = wp_update_term( $id, 'product_cat', $args );

        if ( is_wp_error( $result ) ) {
            return $result;
        }

        $term = get_term( $result['term_id'], 'product_cat' );
        
        return rest_ensure_response( $this->prepare_category_response( $term ) );
    }

    /**
     * Delete category
     */
    public function delete_category( $request ) {
        $id = $request->get_param( 'id' );
        $term = get_term( $id, 'product_cat' );

        if ( is_wp_error( $term ) || ! $term ) {
            return new WP_Error( 
                'category_not_found', 
                __( 'Category not found.', 'wp-products' ), 
                array( 'status' => 404 ) 
            );
        }

        $previous = $this->prepare_category_response( $term );
        $result = wp_delete_term( $id, 'product_cat' );

        if ( is_wp_error( $result ) || ! $result ) {
            return new WP_Error( 
                'cant_delete', 
                __( 'The category cannot be deleted.', 'wp-products' ), 
                array( 'status' => 500 ) 
            );
        }

        return rest_ensure_response( array(
            'deleted' => true,
            'previous' => $previous,
        ) );
    }

    /**
     * Get products by category
     */
    public function get_products_by_category( $request ) {
        $category_id = $request->get_param( 'id' );
        
        $term = get_term( $category_id, 'product_cat' );
        if ( is_wp_error( $term ) || ! $term ) {
            return new WP_Error( 
                'category_not_found', 
                __( 'Category not found.', 'wp-products' ), 
                array( 'status' => 404 ) 
            );
        }

        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => $request->get_param( 'per_page' ) ?: 10,
            'paged'          => $request->get_param( 'page' ) ?: 1,
            'post_status'    => 'publish',
            'tax_query'      => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => $category_id,
                ),
            ),
        );

        $query = new WP_Query( $args );
        $products = array();

        foreach ( $query->posts as $post ) {
            $products[] = $this->prepare_product_response( $post );
        }

        $response = rest_ensure_response( $products );
        
        $response->header( 'X-WP-Total', $query->found_posts );
        $response->header( 'X-WP-TotalPages', $query->max_num_pages );

        return $response;
    }

    /**
     * Prepare product for response
     */
    private function prepare_product_response( $post ) {
        $categories = wp_get_post_terms( $post->ID, 'product_cat' );
        
        $category_data = array();
        if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) {
            foreach ( $categories as $cat ) {
                if ( is_object( $cat ) ) {
                    $category_data[] = array(
                        'id'   => $cat->term_id,
                        'name' => $cat->name,
                        'slug' => $cat->slug,
                    );
                }
            }
        }

        $featured_image = null;
        if ( has_post_thumbnail( $post->ID ) ) {
            $image_id = get_post_thumbnail_id( $post->ID );
            $image_data = wp_get_attachment_image_src( $image_id, 'full' );
            $featured_image = array(
                'id'  => $image_id,
                'url' => $image_data[0],
                'width' => $image_data[1],
                'height' => $image_data[2],
            );
        }

        return array(
            'id'             => $post->ID,
            'title'          => $post->post_title,
            'slug'           => $post->post_name,
            'description'    => $post->post_content,
            'excerpt'        => $post->post_excerpt,
            'status'         => $post->post_status,
            'featured_image' => $featured_image,
            'categories'     => $category_data,
            'meta_data'      => array(
                'price'          => get_post_meta( $post->ID, '_product_price', true ),
                'sale_price'     => get_post_meta( $post->ID, '_product_sale_price', true ),
                'sku'            => get_post_meta( $post->ID, '_product_sku', true ),
                'stock_quantity' => get_post_meta( $post->ID, '_product_stock_quantity', true ),
                'stock_status'   => get_post_meta( $post->ID, '_product_stock_status', true ),
            ),
            'date_created'   => $post->post_date,
            'date_modified'  => $post->post_modified,
        );
    }

    /**
     * Prepare category for response
     */
    private function prepare_category_response( $term ) {
        return array(
            'id'          => $term->term_id,
            'name'        => $term->name,
            'slug'        => $term->slug,
            'description' => $term->description,
            'parent'      => $term->parent,
            'count'       => $term->count,
        );
    }

    /**
     * Prepare product for database
     */
    private function prepare_product_for_database( $request ) {
        $post_data = array(
            'post_type'    => 'product',
            'post_title'   => $request->get_param( 'title' ),
            'post_content' => $request->get_param( 'description' ),
            'post_excerpt' => $request->get_param( 'excerpt' ),
            'post_status'  => $request->get_param( 'status' ) ?: 'publish',
        );

        if ( $request->get_param( 'slug' ) ) {
            $post_data['post_name'] = $request->get_param( 'slug' );
        }

        $meta_data = array();
        if ( $request->get_param( 'price' ) !== null ) {
            $meta_data['_product_price'] = sanitize_text_field( $request->get_param( 'price' ) );
        }
        if ( $request->get_param( 'sale_price' ) !== null ) {
            $meta_data['_product_sale_price'] = sanitize_text_field( $request->get_param( 'sale_price' ) );
        }
        if ( $request->get_param( 'sku' ) ) {
            $meta_data['_product_sku'] = sanitize_text_field( $request->get_param( 'sku' ) );
        }
        if ( $request->get_param( 'stock_quantity' ) !== null ) {
            $meta_data['_product_stock_quantity'] = absint( $request->get_param( 'stock_quantity' ) );
        }
        if ( $request->get_param( 'stock_status' ) ) {
            $meta_data['_product_stock_status'] = sanitize_text_field( $request->get_param( 'stock_status' ) );
        }

        $categories = array();
        if ( $request->get_param( 'categories' ) ) {
            $categories = array_map( 'absint', (array) $request->get_param( 'categories' ) );
        }

        return array(
            'post_data'  => $post_data,
            'meta_data'  => $meta_data,
            'categories' => $categories,
        );
    }

    /**
     * Permission check for reading items
     */
    public function get_items_permissions_check( $request ) {
        return true; // Public access for reading
    }

    /**
     * Permission check for reading single item
     */
    public function get_item_permissions_check( $request ) {
        return true; // Public access for reading
    }

    /**
     * Permission check for creating items
     */
    public function create_item_permissions_check( $request ) {
        return current_user_can( 'edit_posts' );
    }

    /**
     * Permission check for updating items
     */
    public function update_item_permissions_check( $request ) {
        return current_user_can( 'edit_posts' );
    }

    /**
     * Permission check for deleting items
     */
    public function delete_item_permissions_check( $request ) {
        return current_user_can( 'delete_posts' );
    }

    /**
     * Get collection parameters
     */
    private function get_collection_params() {
        return array(
            'per_page' => array(
                'default'           => 10,
                'sanitize_callback' => 'absint',
            ),
            'page' => array(
                'default'           => 1,
                'sanitize_callback' => 'absint',
            ),
            'search' => array(
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'status' => array(
                'default'           => 'publish',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'orderby' => array(
                'default'           => 'date',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'order' => array(
                'default'           => 'DESC',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'category' => array(
                'sanitize_callback' => 'absint',
            ),
        );
    }

    /**
     * Get category collection parameters
     */
    private function get_category_collection_params() {
        return array(
            'per_page' => array(
                'default'           => 10,
                'sanitize_callback' => 'absint',
            ),
            'page' => array(
                'default'           => 1,
                'sanitize_callback' => 'absint',
            ),
            'search' => array(
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'hide_empty' => array(
                'default'           => false,
                'sanitize_callback' => 'rest_sanitize_boolean',
            ),
            'parent' => array(
                'sanitize_callback' => 'absint',
            ),
            'orderby' => array(
                'default'           => 'name',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'order' => array(
                'default'           => 'ASC',
                'sanitize_callback' => 'sanitize_text_field',
            ),
        );
    }

    /**
     * Get product schema
     */
    private function get_product_schema() {
        return array(
            'title' => array(
                'required'          => true,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'description' => array(
                'sanitize_callback' => 'wp_kses_post',
            ),
            'excerpt' => array(
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'slug' => array(
                'sanitize_callback' => 'sanitize_title',
            ),
            'status' => array(
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'price' => array(
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'sale_price' => array(
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'sku' => array(
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'stock_quantity' => array(
                'sanitize_callback' => 'absint',
            ),
            'stock_status' => array(
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'categories' => array(
                'sanitize_callback' => function( $value ) {
                    return array_map( 'absint', (array) $value );
                },
            ),
        );
    }

    /**
     * Get category schema
     */
    private function get_category_schema() {
        return array(
            'name' => array(
                'required'          => true,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'slug' => array(
                'sanitize_callback' => 'sanitize_title',
            ),
            'description' => array(
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'parent' => array(
                'sanitize_callback' => 'absint',
            ),
        );
    }
}

// Initialize the REST API
new WP_Products_REST_API();