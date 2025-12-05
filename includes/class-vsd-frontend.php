<?php
/**
 * Frontend Display Handler
 * Displays vinyl metadata on product pages
 */

if (!defined('ABSPATH')) {
    exit;
}

class VSD_Frontend {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Display meta on single product page
        add_action('woocommerce_single_product_summary', array($this, 'display_vinyl_meta'), 25);
        
        // Add custom tab for tracklist
        add_filter('woocommerce_product_tabs', array($this, 'add_tracklist_tab'));
    }
    
    /**
     * Display vinyl metadata on product page
     */
    public function display_vinyl_meta() {
        global $product;
        
        if (!$product) {
            return;
        }
        
        $product_id = $product->get_id();
        $discogs_id = get_post_meta($product_id, '_vsd_discogs_id', true);
        
        // Only display for products imported from Discogs
        if (empty($discogs_id)) {
            return;
        }
        
        $artist = get_post_meta($product_id, '_vsd_artist', true);
        $label = get_post_meta($product_id, '_vsd_label', true);
        $country = get_post_meta($product_id, '_vsd_country', true);
        $date = get_post_meta($product_id, '_vsd_date', true);
        $format = get_post_meta($product_id, '_vsd_format', true);
        $genre = get_post_meta($product_id, '_vsd_genre', true);
        $style = get_post_meta($product_id, '_vsd_style', true);
        
        ?>
        <div class="vsd-product-meta">
            <h3><?php esc_html_e('Vinyl Details', 'vinyl-shop-discogs'); ?></h3>
            
            <?php if ($artist): ?>
                <div class="vsd-product-meta-item">
                    <strong><?php esc_html_e('Artist:', 'vinyl-shop-discogs'); ?></strong> 
                    <?php echo esc_html($artist); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($label): ?>
                <div class="vsd-product-meta-item">
                    <strong><?php esc_html_e('Label:', 'vinyl-shop-discogs'); ?></strong> 
                    <?php echo esc_html($label); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($format): ?>
                <div class="vsd-product-meta-item">
                    <strong><?php esc_html_e('Format:', 'vinyl-shop-discogs'); ?></strong> 
                    <?php echo esc_html($format); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($country): ?>
                <div class="vsd-product-meta-item">
                    <strong><?php esc_html_e('Country:', 'vinyl-shop-discogs'); ?></strong> 
                    <?php echo esc_html($country); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($date): ?>
                <div class="vsd-product-meta-item">
                    <strong><?php esc_html_e('Released:', 'vinyl-shop-discogs'); ?></strong> 
                    <?php echo esc_html($date); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($genre): ?>
                <div class="vsd-product-meta-item">
                    <strong><?php esc_html_e('Genre:', 'vinyl-shop-discogs'); ?></strong> 
                    <?php echo esc_html($genre); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($style): ?>
                <div class="vsd-product-meta-item">
                    <strong><?php esc_html_e('Style:', 'vinyl-shop-discogs'); ?></strong> 
                    <?php echo esc_html($style); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Add tracklist tab to product tabs
     *
     * @param array $tabs
     * @return array
     */
    public function add_tracklist_tab($tabs) {
        global $product;
        
        if (!$product) {
            return $tabs;
        }
        
        $product_id = $product->get_id();
        $tracklist = get_post_meta($product_id, '_vsd_tracklist', true);
        
        if (!empty($tracklist)) {
            $tabs['tracklist'] = array(
                'title' => __('Tracklist', 'vinyl-shop-discogs'),
                'priority' => 20,
                'callback' => array($this, 'render_tracklist_tab')
            );
        }
        
        return $tabs;
    }
    
    /**
     * Render tracklist tab content
     */
    public function render_tracklist_tab() {
        global $product;
        
        if (!$product) {
            return;
        }
        
        $product_id = $product->get_id();
        $tracklist = get_post_meta($product_id, '_vsd_tracklist', true);
        
        if (empty($tracklist)) {
            return;
        }
        
        ?>
        <div class="vsd-tracklist">
            <h2><?php esc_html_e('Tracklist', 'vinyl-shop-discogs'); ?></h2>
            <pre><?php echo esc_html($tracklist); ?></pre>
        </div>
        <?php
    }
}

// Initialize frontend class
if (!is_admin()) {
    new VSD_Frontend();
}
