<?php
/**
 * Installation and Activation Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class VSD_Install {
    
    /**
     * Plugin activation
     */
    public static function activate() {
        // Check PHP version
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            deactivate_plugins(VSD_PLUGIN_BASENAME);
            wp_die(__('Vinyl Shop Discogs requires PHP 7.4 or higher.', 'vinyl-shop-discogs'));
        }
        
        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            deactivate_plugins(VSD_PLUGIN_BASENAME);
            wp_die(__('Vinyl Shop Discogs requires WooCommerce to be installed and active.', 'vinyl-shop-discogs'));
        }
        
        // Set default options
        self::set_default_options();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation
     */
    public static function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Set default options
     */
    private static function set_default_options() {
        $defaults = array(
            'vsd_discogs_token' => '',
            'vsd_default_product_status' => 'draft',
        );
        
        foreach ($defaults as $key => $value) {
            if (get_option($key) === false) {
                add_option($key, $value);
            }
        }
    }
}
