<?php
/**
 * Product Model
 */

if (!defined('ABSPATH')) {
    exit;
}

class VSD_Model_Product {
    
    /**
     * Create WooCommerce product from Discogs data
     *
     * @param array $release Discogs release data
     * @return int|WP_Error Product ID or error
     */
    public function create_from_discogs($release) {
        if (empty($release)) {
            return new WP_Error('empty_release', __('Release data is empty.', 'vinyl-shop-discogs'));
        }
        
        // Check if product already exists with this Discogs ID
        $existing_id = $this->get_product_by_discogs_id($release['id']);
        if ($existing_id) {
            return new WP_Error('product_exists', __('Product already exists for this release.', 'vinyl-shop-discogs'));
        }
        
        // Allow modification of release data before product creation
        $release = apply_filters('vsd_before_create_product', $release);
        
        // Fire action before product creation
        do_action('vsd_before_product_created', $release);
        
        // Create product
        $product = new WC_Product_Simple();
        
        // Set basic product data
        $title = isset($release['title']) ? $release['title'] : __('Untitled Release', 'vinyl-shop-discogs');
        $product->set_name($title);
        
        // Set description
        $description = $this->build_description($release);
        $product->set_description($description);
        $product->set_short_description($this->build_short_description($release));
        
        // Set product status (allow filtering)
        $default_status = get_option('vsd_default_product_status', 'draft');
        $status = apply_filters('vsd_product_status', $default_status, $release);
        $product->set_status($status);
        
        // Set SKU
        if (isset($release['id'])) {
            $product->set_sku('DISCOGS-' . $release['id']);
        }
        
        // Save product
        $product_id = $product->save();
        
        if (!$product_id) {
            return new WP_Error('save_failed', __('Failed to create product.', 'vinyl-shop-discogs'));
        }
        
        // Add custom meta fields
        $this->add_meta_fields($product_id, $release);
        
        // Add categories (Genre and Style)
        $this->add_categories($product_id, $release);
        
        // Add product image
        $this->add_product_image($product_id, $release);
        
        // Fire action after product creation
        do_action('vsd_after_product_created', $product_id, $release);
        
        return $product_id;
    }
    
    /**
     * Add meta fields to product
     *
     * @param int $product_id
     * @param array $release
     */
    private function add_meta_fields($product_id, $release) {
        // Store Discogs ID
        if (isset($release['id'])) {
            update_post_meta($product_id, '_vsd_discogs_id', $release['id']);
        }
        
        // Release name
        if (isset($release['title'])) {
            update_post_meta($product_id, '_vsd_release_name', sanitize_text_field($release['title']));
        }
        
        // Artist
        if (isset($release['artists']) && is_array($release['artists'])) {
            $artists = array();
            foreach ($release['artists'] as $artist) {
                if (isset($artist['name'])) {
                    $artists[] = $artist['name'];
                }
            }
            update_post_meta($product_id, '_vsd_artist', sanitize_text_field(implode(', ', $artists)));
        } elseif (isset($release['artists_sort'])) {
            update_post_meta($product_id, '_vsd_artist', sanitize_text_field($release['artists_sort']));
        }
        
        // Country
        if (isset($release['country'])) {
            update_post_meta($product_id, '_vsd_country', sanitize_text_field($release['country']));
        }
        
        // Date/Year
        if (isset($release['released'])) {
            update_post_meta($product_id, '_vsd_date', sanitize_text_field($release['released']));
        } elseif (isset($release['year'])) {
            update_post_meta($product_id, '_vsd_date', sanitize_text_field($release['year']));
        }
        
        // Label
        if (isset($release['labels']) && is_array($release['labels'])) {
            $labels = array();
            foreach ($release['labels'] as $label) {
                if (isset($label['name'])) {
                    $labels[] = $label['name'];
                }
            }
            update_post_meta($product_id, '_vsd_label', sanitize_text_field(implode(', ', $labels)));
        }
        
        // Genre
        if (isset($release['genres']) && is_array($release['genres'])) {
            update_post_meta($product_id, '_vsd_genre', sanitize_text_field(implode(', ', $release['genres'])));
        }
        
        // Style
        if (isset($release['styles']) && is_array($release['styles'])) {
            update_post_meta($product_id, '_vsd_style', sanitize_text_field(implode(', ', $release['styles'])));
        }
        
        // Tracklist
        if (isset($release['tracklist']) && is_array($release['tracklist'])) {
            $tracklist = $this->format_tracklist($release['tracklist']);
            update_post_meta($product_id, '_vsd_tracklist', wp_slash($tracklist));
        }
        
        // Format
        if (isset($release['formats']) && is_array($release['formats'])) {
            $formats = array();
            foreach ($release['formats'] as $format) {
                if (isset($format['name'])) {
                    $format_text = $format['name'];
                    if (isset($format['qty'])) {
                        $format_text .= ' (' . $format['qty'] . 'x)';
                    }
                    if (isset($format['descriptions']) && is_array($format['descriptions'])) {
                        $format_text .= ' - ' . implode(', ', $format['descriptions']);
                    }
                    $formats[] = $format_text;
                }
            }
            update_post_meta($product_id, '_vsd_format', sanitize_text_field(implode(', ', $formats)));
        }
        
        // Store thumbnail URL
        if (isset($release['thumb'])) {
            update_post_meta($product_id, '_vsd_thumbnail_url', esc_url_raw($release['thumb']));
        }
    }
    
    /**
     * Format tracklist
     *
     * @param array $tracklist
     * @return string
     */
    private function format_tracklist($tracklist) {
        $output = array();
        
        foreach ($tracklist as $track) {
            $line = '';
            
            if (isset($track['position']) && !empty($track['position'])) {
                $line .= $track['position'] . '. ';
            }
            
            if (isset($track['title'])) {
                $line .= $track['title'];
            }
            
            if (isset($track['duration']) && !empty($track['duration'])) {
                $line .= ' (' . $track['duration'] . ')';
            }
            
            if (!empty($line)) {
                $output[] = $line;
            }
        }
        
        return implode("\n", $output);
    }
    
    /**
     * Add categories to product
     *
     * @param int $product_id
     * @param array $release
     */
    private function add_categories($product_id, $release) {
        $category_ids = array();
        
        // Add genres as categories
        if (isset($release['genres']) && is_array($release['genres'])) {
            foreach ($release['genres'] as $genre) {
                $term = $this->get_or_create_category($genre, 0);
                if ($term && isset($term['term_id'])) {
                    $category_ids[] = $term['term_id'];
                    
                    // Add styles as subcategories under genre
                    if (isset($release['styles']) && is_array($release['styles'])) {
                        foreach ($release['styles'] as $style) {
                            $style_term = $this->get_or_create_category($style, $term['term_id']);
                            if ($style_term && isset($style_term['term_id'])) {
                                $category_ids[] = $style_term['term_id'];
                            }
                        }
                    }
                }
            }
        } elseif (isset($release['styles']) && is_array($release['styles'])) {
            // If no genres, add styles as top-level categories
            foreach ($release['styles'] as $style) {
                $term = $this->get_or_create_category($style, 0);
                if ($term && isset($term['term_id'])) {
                    $category_ids[] = $term['term_id'];
                }
            }
        }
        
        if (!empty($category_ids)) {
            wp_set_object_terms($product_id, $category_ids, 'product_cat');
        }
    }
    
    /**
     * Get or create product category
     *
     * @param string $name
     * @param int $parent_id
     * @return array|null
     */
    private function get_or_create_category($name, $parent_id = 0) {
        $term = term_exists($name, 'product_cat', $parent_id);
        
        if (!$term) {
            $term = wp_insert_term($name, 'product_cat', array(
                'parent' => $parent_id
            ));
            
            if (is_wp_error($term)) {
                return null;
            }
        }
        
        return $term;
    }
    
    /**
     * Add product image
     *
     * @param int $product_id
     * @param array $release
     */
    private function add_product_image($product_id, $release) {
        $image_url = '';
        
        // Try to get high-res image first
        if (isset($release['images']) && is_array($release['images']) && !empty($release['images'])) {
            $image_url = $release['images'][0]['uri'];
        } elseif (isset($release['cover_image'])) {
            $image_url = $release['cover_image'];
        } elseif (isset($release['thumb'])) {
            $image_url = $release['thumb'];
        }
        
        if (!empty($image_url)) {
            $api = new VSD_Service_Discogs_API();
            $title = isset($release['title']) ? $release['title'] : '';
            $attachment_id = $api->download_image($image_url, $title);
            
            if (!is_wp_error($attachment_id)) {
                set_post_thumbnail($product_id, $attachment_id);
            }
        }
    }
    
    /**
     * Build product description
     *
     * @param array $release
     * @return string
     */
    private function build_description($release) {
        $description = '';
        
        // Add notes if available
        if (isset($release['notes']) && !empty($release['notes'])) {
            $description .= wpautop($release['notes']);
        }
        
        // Add tracklist
        if (isset($release['tracklist']) && is_array($release['tracklist'])) {
            $description .= '<h3>' . __('Tracklist', 'vinyl-shop-discogs') . '</h3>';
            $description .= '<ol>';
            foreach ($release['tracklist'] as $track) {
                if (isset($track['title'])) {
                    $track_line = esc_html($track['title']);
                    if (isset($track['duration']) && !empty($track['duration'])) {
                        $track_line .= ' <em>(' . esc_html($track['duration']) . ')</em>';
                    }
                    $description .= '<li>' . $track_line . '</li>';
                }
            }
            $description .= '</ol>';
        }
        
        return $description;
    }
    
    /**
     * Build short description
     *
     * @param array $release
     * @return string
     */
    private function build_short_description($release) {
        $parts = array();
        
        if (isset($release['year']) && !empty($release['year'])) {
            $parts[] = $release['year'];
        }
        
        if (isset($release['country']) && !empty($release['country'])) {
            $parts[] = $release['country'];
        }
        
        if (isset($release['genres']) && is_array($release['genres'])) {
            $parts[] = implode(', ', $release['genres']);
        }
        
        return implode(' • ', $parts);
    }
    
    /**
     * Get product by Discogs ID
     *
     * @param int $discogs_id
     * @return int|false Product ID or false
     */
    private function get_product_by_discogs_id($discogs_id) {
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 1,
            'meta_query' => array(
                array(
                    'key' => '_vsd_discogs_id',
                    'value' => $discogs_id,
                    'compare' => '='
                )
            ),
            'fields' => 'ids'
        );
        
        $products = get_posts($args);
        
        return !empty($products) ? $products[0] : false;
    }
}
