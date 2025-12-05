<?php
/**
 * Admin Controller
 */

if (!defined('ABSPATH')) {
    exit;
}

class VSD_Controller_Admin {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_ajax_vsd_search_discogs', array($this, 'ajax_search_discogs'));
        add_action('wp_ajax_vsd_add_product', array($this, 'ajax_add_product'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Vinyl Shop Discogs', 'vinyl-shop-discogs'),
            __('Vinyl Shop', 'vinyl-shop-discogs'),
            'manage_woocommerce',
            'vinyl-shop-discogs',
            array($this, 'render_admin_page'),
            'dashicons-album',
            56
        );
        
        add_submenu_page(
            'vinyl-shop-discogs',
            __('Settings', 'vinyl-shop-discogs'),
            __('Settings', 'vinyl-shop-discogs'),
            'manage_options',
            'vinyl-shop-discogs-settings',
            array($this, 'render_settings_page')
        );
    }
    
    /**
     * Render admin page
     */
    public function render_admin_page() {
        $view = new VSD_View_Admin();
        $view->render();
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        // Handle form submission
        if (isset($_POST['vsd_settings_submit'])) {
            check_admin_referer('vsd_settings_nonce');
            
            update_option('vsd_discogs_token', sanitize_text_field($_POST['vsd_discogs_token']));
            update_option('vsd_default_product_status', sanitize_text_field($_POST['vsd_default_product_status']));
            
            echo '<div class="notice notice-success"><p>' . esc_html__('Settings saved successfully.', 'vinyl-shop-discogs') . '</p></div>';
        }
        
        $view = new VSD_View_Settings();
        $view->render();
    }
    
    /**
     * AJAX: Search Discogs
     */
    public function ajax_search_discogs() {
        check_ajax_referer('vsd_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'vinyl-shop-discogs')));
        }
        
        $query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';
        
        if (empty($query)) {
            wp_send_json_error(array('message' => __('Search query is required.', 'vinyl-shop-discogs')));
        }
        
        $api = new VSD_Service_Discogs_API();
        $results = $api->search($query);
        
        if (is_wp_error($results)) {
            wp_send_json_error(array('message' => $results->get_error_message()));
        }
        
        wp_send_json_success($results);
    }
    
    /**
     * AJAX: Add product
     */
    public function ajax_add_product() {
        check_ajax_referer('vsd_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'vinyl-shop-discogs')));
        }
        
        $release_id = isset($_POST['release_id']) ? intval($_POST['release_id']) : 0;
        
        if (empty($release_id)) {
            wp_send_json_error(array('message' => __('Release ID is required.', 'vinyl-shop-discogs')));
        }
        
        // Get release details from Discogs
        $api = new VSD_Service_Discogs_API();
        $release = $api->get_release($release_id);
        
        if (is_wp_error($release)) {
            wp_send_json_error(array('message' => $release->get_error_message()));
        }
        
        // Create WooCommerce product
        $product_model = new VSD_Model_Product();
        $product_id = $product_model->create_from_discogs($release);
        
        if (is_wp_error($product_id)) {
            wp_send_json_error(array('message' => $product_id->get_error_message()));
        }
        
        wp_send_json_success(array(
            'product_id' => $product_id,
            'edit_url' => admin_url('post.php?post=' . $product_id . '&action=edit')
        ));
    }
}
