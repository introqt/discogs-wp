<?php
/**
 * Discogs API Service
 */

if (!defined('ABSPATH')) {
    exit;
}

class VSD_Service_Discogs_API {
    
    /**
     * API Base URL
     */
    private const API_BASE_URL = 'https://api.discogs.com';
    
    /**
     * API Token
     *
     * @var string
     */
    private $token;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->token = get_option('vsd_discogs_token', '');
    }
    
    /**
     * Search for releases
     *
     * @param string $query Search query
     * @param array $args Additional arguments
     * @return array|WP_Error
     */
    public function search($query, $args = array()) {
        if (empty($this->token)) {
            return new WP_Error('no_token', __('Discogs API token is not configured. Please add it in settings.', 'vinyl-shop-discogs'));
        }
        
        $defaults = array(
            'type' => 'release',
            'per_page' => 20,
            'page' => 1
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $url = add_query_arg(array(
            'q' => urlencode($query),
            'type' => $args['type'],
            'per_page' => $args['per_page'],
            'page' => $args['page'],
            'token' => $this->token
        ), self::API_BASE_URL . '/database/search');
        
        $response = $this->make_request($url);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        return $this->format_search_results($response);
    }
    
    /**
     * Get release details
     *
     * @param int $release_id Release ID
     * @return array|WP_Error
     */
    public function get_release($release_id) {
        if (empty($this->token)) {
            return new WP_Error('no_token', __('Discogs API token is not configured.', 'vinyl-shop-discogs'));
        }
        
        $url = add_query_arg(array(
            'token' => $this->token
        ), self::API_BASE_URL . '/releases/' . $release_id);
        
        $response = $this->make_request($url);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        return $response;
    }
    
    /**
     * Make API request
     *
     * @param string $url
     * @return array|WP_Error
     */
    private function make_request($url) {
        $response = wp_remote_get($url, array(
            'headers' => array(
                'User-Agent' => 'VinylShopDiscogs/1.0 +' . home_url()
            ),
            'timeout' => 15
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        if ($code !== 200) {
            $error_message = sprintf(__('Discogs API returned error code %d', 'vinyl-shop-discogs'), $code);
            
            $data = json_decode($body, true);
            if (isset($data['message'])) {
                $error_message .= ': ' . $data['message'];
            }
            
            return new WP_Error('api_error', $error_message);
        }
        
        $data = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('json_error', __('Failed to decode API response.', 'vinyl-shop-discogs'));
        }
        
        return $data;
    }
    
    /**
     * Format search results
     *
     * @param array $response
     * @return array
     */
    private function format_search_results($response) {
        $results = array();
        
        if (isset($response['results']) && is_array($response['results'])) {
            foreach ($response['results'] as $item) {
                $results[] = array(
                    'id' => isset($item['id']) ? $item['id'] : 0,
                    'title' => isset($item['title']) ? $item['title'] : '',
                    'year' => isset($item['year']) ? $item['year'] : '',
                    'format' => isset($item['format']) ? implode(', ', $item['format']) : '',
                    'label' => isset($item['label']) ? implode(', ', $item['label']) : '',
                    'country' => isset($item['country']) ? $item['country'] : '',
                    'genre' => isset($item['genre']) ? implode(', ', $item['genre']) : '',
                    'style' => isset($item['style']) ? implode(', ', $item['style']) : '',
                    'thumb' => isset($item['thumb']) ? $item['thumb'] : '',
                    'cover_image' => isset($item['cover_image']) ? $item['cover_image'] : '',
                );
            }
        }
        
        return array(
            'results' => $results,
            'pagination' => array(
                'page' => isset($response['pagination']['page']) ? $response['pagination']['page'] : 1,
                'pages' => isset($response['pagination']['pages']) ? $response['pagination']['pages'] : 1,
                'per_page' => isset($response['pagination']['per_page']) ? $response['pagination']['per_page'] : 20,
                'items' => isset($response['pagination']['items']) ? $response['pagination']['items'] : 0,
            )
        );
    }
    
    /**
     * Download image from URL
     *
     * @param string $image_url
     * @param string $title
     * @return int|WP_Error Attachment ID or error
     */
    public function download_image($image_url, $title = '') {
        if (empty($image_url)) {
            return new WP_Error('no_image', __('No image URL provided.', 'vinyl-shop-discogs'));
        }
        
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        $tmp = download_url($image_url);
        
        if (is_wp_error($tmp)) {
            return $tmp;
        }
        
        $file_array = array(
            'name' => basename($image_url),
            'tmp_name' => $tmp
        );
        
        $id = media_handle_sideload($file_array, 0, $title);
        
        if (is_wp_error($id)) {
            @unlink($file_array['tmp_name']);
            return $id;
        }
        
        return $id;
    }
}
