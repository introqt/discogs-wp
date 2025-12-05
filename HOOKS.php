<?php
/**
 * Plugin Hooks and Filters Examples
 * 
 * This file demonstrates how to extend the Vinyl Shop Discogs plugin
 * using WordPress hooks and filters. Copy any of these examples to your
 * theme's functions.php or a custom plugin.
 */

// DO NOT include this file in your plugin - these are just examples!

/**
 * Example 1: Modify product data before creation
 * 
 * This filter allows you to modify the Discogs release data
 * before it's used to create a WooCommerce product.
 */
add_filter('vsd_before_create_product', 'custom_modify_product_data', 10, 1);
function custom_modify_product_data($release) {
    // Example: Add a prefix to all product titles
    if (isset($release['title'])) {
        $release['title'] = '[VINYL] ' . $release['title'];
    }
    
    return $release;
}

/**
 * Example 2: Set custom product price based on release year
 * 
 * Automatically set prices for imported products
 */
add_action('vsd_after_product_created', 'custom_set_product_price', 10, 2);
function custom_set_product_price($product_id, $release) {
    $product = wc_get_product($product_id);
    
    if (!$product) {
        return;
    }
    
    // Set price based on release year
    $year = isset($release['year']) ? intval($release['year']) : 0;
    
    if ($year < 1970) {
        $price = 50.00; // Vintage records
    } elseif ($year < 1990) {
        $price = 30.00; // 70s-80s records
    } elseif ($year < 2010) {
        $price = 20.00; // 90s-00s records
    } else {
        $price = 15.00; // Modern records
    }
    
    $product->set_regular_price($price);
    $product->save();
}

/**
 * Example 3: Add custom meta fields
 * 
 * Store additional custom data with products
 */
add_action('vsd_after_product_created', 'custom_add_meta_fields', 10, 2);
function custom_add_meta_fields($product_id, $release) {
    // Add Discogs URL
    if (isset($release['id'])) {
        $discogs_url = 'https://www.discogs.com/release/' . $release['id'];
        update_post_meta($product_id, '_discogs_url', $discogs_url);
    }
    
    // Add custom badge for rare releases
    if (isset($release['year']) && intval($release['year']) < 1960) {
        update_post_meta($product_id, '_rare_vinyl', 'yes');
    }
}

/**
 * Example 4: Send email notification when product is added
 */
add_action('vsd_after_product_created', 'custom_notify_on_import', 10, 2);
function custom_notify_on_import($product_id, $release) {
    $product = wc_get_product($product_id);
    
    if (!$product) {
        return;
    }
    
    $to = get_option('admin_email');
    $subject = 'New Vinyl Product Added: ' . $product->get_name();
    $message = sprintf(
        "A new vinyl product has been imported:\n\nTitle: %s\nArtist: %s\nEdit: %s",
        $product->get_name(),
        get_post_meta($product_id, '_vsd_artist', true),
        admin_url('post.php?post=' . $product_id . '&action=edit')
    );
    
    wp_mail($to, $subject, $message);
}

/**
 * Example 5: Automatically publish products from specific genres
 */
add_filter('vsd_product_status', 'custom_auto_publish_genres', 10, 2);
function custom_auto_publish_genres($status, $release) {
    // Auto-publish rock and jazz records
    $auto_publish_genres = array('Rock', 'Jazz');
    
    if (isset($release['genres']) && is_array($release['genres'])) {
        foreach ($release['genres'] as $genre) {
            if (in_array($genre, $auto_publish_genres)) {
                return 'publish';
            }
        }
    }
    
    return $status;
}

/**
 * Example 6: Add custom product tags based on style
 */
add_action('vsd_after_product_created', 'custom_add_style_tags', 10, 2);
function custom_add_style_tags($product_id, $release) {
    if (isset($release['styles']) && is_array($release['styles'])) {
        wp_set_object_terms($product_id, $release['styles'], 'product_tag');
    }
}

/**
 * Example 7: Set stock quantity for all imports
 */
add_action('vsd_after_product_created', 'custom_set_stock', 10, 2);
function custom_set_stock($product_id, $release) {
    $product = wc_get_product($product_id);
    
    if (!$product) {
        return;
    }
    
    $product->set_manage_stock(true);
    $product->set_stock_quantity(1);
    $product->set_stock_status('instock');
    $product->save();
}

/**
 * Example 8: Modify search results before display
 */
add_filter('vsd_search_results', 'custom_filter_search_results', 10, 1);
function custom_filter_search_results($results) {
    // Example: Only show vinyl formats, filter out CDs
    if (isset($results['results']) && is_array($results['results'])) {
        $results['results'] = array_filter($results['results'], function($item) {
            $format = isset($item['format']) ? strtolower($item['format']) : '';
            return strpos($format, 'vinyl') !== false || strpos($format, 'lp') !== false;
        });
    }
    
    return $results;
}

/**
 * Example 9: Add custom CSS class to imported products
 */
add_filter('post_class', 'custom_add_vinyl_class', 10, 3);
function custom_add_vinyl_class($classes, $class, $post_id) {
    if (get_post_type($post_id) === 'product') {
        $discogs_id = get_post_meta($post_id, '_vsd_discogs_id', true);
        
        if (!empty($discogs_id)) {
            $classes[] = 'vinyl-product';
            $classes[] = 'discogs-import';
        }
    }
    
    return $classes;
}

/**
 * Example 10: Custom frontend display of vinyl metadata
 */
add_action('woocommerce_single_product_summary', 'custom_vinyl_badge', 5);
function custom_vinyl_badge() {
    global $product;
    
    if (!$product) {
        return;
    }
    
    $discogs_id = get_post_meta($product->get_id(), '_vsd_discogs_id', true);
    
    if (!empty($discogs_id)) {
        echo '<div class="vinyl-badge" style="background: #000; color: #fff; padding: 5px 10px; display: inline-block; margin-bottom: 10px; border-radius: 3px;">';
        echo '🎵 Original Vinyl Record';
        echo '</div>';
    }
}

/**
 * Available Hooks in Vinyl Shop Discogs:
 * 
 * Actions:
 * - vsd_before_search - Fires before a Discogs search
 * - vsd_after_search - Fires after a Discogs search
 * - vsd_before_product_created - Fires before creating a product
 * - vsd_after_product_created - Fires after creating a product
 * - vsd_before_meta_added - Fires before adding meta fields
 * - vsd_after_meta_added - Fires after adding meta fields
 * 
 * Filters:
 * - vsd_api_request_args - Modify API request arguments
 * - vsd_search_results - Modify search results
 * - vsd_before_create_product - Modify release data before product creation
 * - vsd_product_status - Modify default product status
 * - vsd_product_data - Modify product data array
 * - vsd_product_categories - Modify assigned categories
 * - vsd_tracklist_format - Modify tracklist formatting
 * - vsd_product_description - Modify product description
 * - vsd_product_short_description - Modify short description
 */
