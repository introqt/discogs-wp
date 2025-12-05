<?php
/**
 * Plugin Name: Vinyl Shop Discogs
 * Plugin URI: https://example.com
 * Description: Integration with Discogs API to add vinyl records as WooCommerce products
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: vinyl-shop-discogs
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('VSD_VERSION', '1.0.0');
define('VSD_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('VSD_PLUGIN_URL', plugin_dir_url(__FILE__));
define('VSD_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Plugin Class
 */
class Vinyl_Shop_Discogs {
    
    /**
     * Single instance of the class
     *
     * @var Vinyl_Shop_Discogs
     */
    private static $instance = null;
    
    /**
     * Get instance
     *
     * @return Vinyl_Shop_Discogs
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->includes();
        $this->init_hooks();
    }
    
    /**
     * Include required files
     */
    private function includes() {
        // Autoloader
        spl_autoload_register(array($this, 'autoload'));
        
        // Core includes
        require_once VSD_PLUGIN_DIR . 'includes/class-vsd-install.php';
        require_once VSD_PLUGIN_DIR . 'includes/class-vsd-frontend.php';
    }
    
    /**
     * Autoloader
     *
     * @param string $class
     */
    public function autoload($class) {
        $prefix = 'VSD_';
        $base_dir = VSD_PLUGIN_DIR . 'includes/';
        
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }
        
        $relative_class = substr($class, $len);
        $file = $base_dir . 'class-' . str_replace('_', '-', strtolower($relative_class)) . '.php';
        
        if (file_exists($file)) {
            require $file;
        }
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        register_activation_hook(__FILE__, array('VSD_Install', 'activate'));
        register_deactivation_hook(__FILE__, array('VSD_Install', 'deactivate'));
        
        add_action('plugins_loaded', array($this, 'init'), 10);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
            return;
        }
        
        // Load text domain
        load_plugin_textdomain('vinyl-shop-discogs', false, dirname(VSD_PLUGIN_BASENAME) . '/languages');
        
        // Initialize controllers
        if (is_admin()) {
            new VSD_Controller_Admin();
        }
    }
    
    /**
     * WooCommerce missing notice
     */
    public function woocommerce_missing_notice() {
        echo '<div class="error"><p>';
        echo esc_html__('Vinyl Shop Discogs requires WooCommerce to be installed and active.', 'vinyl-shop-discogs');
        echo '</p></div>';
    }
    
    /**
     * Enqueue admin scripts and styles
     *
     * @param string $hook
     */
    public function enqueue_admin_scripts($hook) {
        // Only load on our admin page
        if ($hook !== 'toplevel_page_vinyl-shop-discogs') {
            return;
        }
        
        wp_enqueue_style(
            'vsd-admin-style',
            VSD_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            VSD_VERSION
        );
        
        wp_enqueue_script(
            'vsd-admin-script',
            VSD_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            VSD_VERSION,
            true
        );
        
        wp_localize_script('vsd-admin-script', 'vsdAdmin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('vsd_ajax_nonce'),
            'strings' => array(
                'search_error' => __('Error searching Discogs. Please try again.', 'vinyl-shop-discogs'),
                'add_success' => __('Product added successfully!', 'vinyl-shop-discogs'),
                'add_error' => __('Error adding product. Please try again.', 'vinyl-shop-discogs'),
            )
        ));
    }
}

/**
 * Returns the main instance of Vinyl_Shop_Discogs
 *
 * @return Vinyl_Shop_Discogs
 */
function VSD() {
    return Vinyl_Shop_Discogs::get_instance();
}

// Initialize the plugin
VSD();
