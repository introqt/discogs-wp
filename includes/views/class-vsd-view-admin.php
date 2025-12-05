<?php
/**
 * Admin View
 */

if (!defined('ABSPATH')) {
    exit;
}

class VSD_View_Admin {
    
    /**
     * Render the admin page
     */
    public function render() {
        ?>
        <div class="wrap vsd-admin-wrap">
            <h1><?php echo esc_html__('Vinyl Shop - Discogs Search', 'vinyl-shop-discogs'); ?></h1>
            
            <div class="vsd-search-container">
                <div class="vsd-search-box">
                    <h2><?php echo esc_html__('Search Discogs', 'vinyl-shop-discogs'); ?></h2>
                    <p><?php echo esc_html__('Search for vinyl records on Discogs and add them as WooCommerce products.', 'vinyl-shop-discogs'); ?></p>
                    
                    <form id="vsd-search-form" class="vsd-search-form">
                        <input 
                            type="text" 
                            id="vsd-search-query" 
                            name="query" 
                            placeholder="<?php echo esc_attr__('Enter artist, album, or release...', 'vinyl-shop-discogs'); ?>"
                            class="regular-text"
                        />
                        <button type="submit" class="button button-primary">
                            <?php echo esc_html__('Search', 'vinyl-shop-discogs'); ?>
                        </button>
                    </form>
                </div>
                
                <div id="vsd-search-results" class="vsd-search-results">
                    <!-- Search results will be loaded here via AJAX -->
                </div>
                
                <div id="vsd-loading" class="vsd-loading" style="display: none;">
                    <span class="spinner is-active"></span>
                    <p><?php echo esc_html__('Searching...', 'vinyl-shop-discogs'); ?></p>
                </div>
                
                <div id="vsd-message" class="vsd-message"></div>
            </div>
        </div>
        <?php
    }
}
