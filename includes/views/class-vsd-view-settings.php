<?php
/**
 * Settings View
 */

if (!defined('ABSPATH')) {
    exit;
}

class VSD_View_Settings {
    
    /**
     * Render the settings page
     */
    public function render() {
        $discogs_token = get_option('vsd_discogs_token', '');
        $default_status = get_option('vsd_default_product_status', 'draft');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('Vinyl Shop Discogs - Settings', 'vinyl-shop-discogs'); ?></h1>
            
            <form method="post" action="">
                <?php wp_nonce_field('vsd_settings_nonce'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="vsd_discogs_token">
                                <?php echo esc_html__('Discogs API Token', 'vinyl-shop-discogs'); ?>
                            </label>
                        </th>
                        <td>
                            <input 
                                type="text" 
                                id="vsd_discogs_token" 
                                name="vsd_discogs_token" 
                                value="<?php echo esc_attr($discogs_token); ?>" 
                                class="regular-text"
                            />
                            <p class="description">
                                <?php 
                                printf(
                                    esc_html__('Get your API token from %s', 'vinyl-shop-discogs'),
                                    '<a href="https://www.discogs.com/settings/developers" target="_blank">Discogs Developer Settings</a>'
                                );
                                ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="vsd_default_product_status">
                                <?php echo esc_html__('Default Product Status', 'vinyl-shop-discogs'); ?>
                            </label>
                        </th>
                        <td>
                            <select id="vsd_default_product_status" name="vsd_default_product_status">
                                <option value="draft" <?php selected($default_status, 'draft'); ?>>
                                    <?php echo esc_html__('Draft', 'vinyl-shop-discogs'); ?>
                                </option>
                                <option value="publish" <?php selected($default_status, 'publish'); ?>>
                                    <?php echo esc_html__('Published', 'vinyl-shop-discogs'); ?>
                                </option>
                            </select>
                            <p class="description">
                                <?php echo esc_html__('Status for newly created products from Discogs.', 'vinyl-shop-discogs'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(__('Save Settings', 'vinyl-shop-discogs'), 'primary', 'vsd_settings_submit'); ?>
            </form>
        </div>
        <?php
    }
}
