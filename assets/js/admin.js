/**
 * Vinyl Shop Discogs - Admin JavaScript
 */

(function($) {
    'use strict';
    
    let currentPage = 1;
    let currentQuery = '';
    
    $(document).ready(function() {
        initSearchForm();
    });
    
    /**
     * Initialize search form
     */
    function initSearchForm() {
        $('#vsd-search-form').on('submit', function(e) {
            e.preventDefault();
            
            const query = $('#vsd-search-query').val().trim();
            
            if (query === '') {
                showMessage('Please enter a search query.', 'error');
                return;
            }
            
            currentQuery = query;
            currentPage = 1;
            searchDiscogs(query, 1);
        });
    }
    
    /**
     * Search Discogs API
     */
    function searchDiscogs(query, page) {
        showLoading();
        hideMessage();
        
        $.ajax({
            url: vsdAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'vsd_search_discogs',
                nonce: vsdAdmin.nonce,
                query: query,
                page: page
            },
            success: function(response) {
                hideLoading();
                
                if (response.success) {
                    displayResults(response.data);
                } else {
                    showMessage(response.data.message || vsdAdmin.strings.search_error, 'error');
                }
            },
            error: function() {
                hideLoading();
                showMessage(vsdAdmin.strings.search_error, 'error');
            }
        });
    }
    
    /**
     * Display search results
     */
    function displayResults(data) {
        const $resultsContainer = $('#vsd-search-results');
        $resultsContainer.empty();
        
        if (!data.results || data.results.length === 0) {
            $resultsContainer.html('<p>No results found. Try a different search query.</p>');
            return;
        }
        
        // Create results grid
        const $grid = $('<div class="vsd-results-grid"></div>');
        
        data.results.forEach(function(item) {
            const $item = createResultItem(item);
            $grid.append($item);
        });
        
        $resultsContainer.append($grid);
        
        // Add pagination if needed
        if (data.pagination && data.pagination.pages > 1) {
            const $pagination = createPagination(data.pagination);
            $resultsContainer.append($pagination);
        }
    }
    
    /**
     * Create result item HTML
     */
    function createResultItem(item) {
        const imageUrl = item.cover_image || item.thumb || '';
        const hasImage = imageUrl !== '';
        
        const $item = $('<div class="vsd-result-item" data-release-id="' + item.id + '"></div>');
        
        // Image
        if (hasImage) {
            $item.append('<img src="' + imageUrl + '" alt="' + escapeHtml(item.title) + '" class="vsd-result-image">');
        } else {
            $item.append('<div class="vsd-result-image no-image">No Image</div>');
        }
        
        // Title
        $item.append('<h3 class="vsd-result-title">' + escapeHtml(item.title) + '</h3>');
        
        // Meta information
        if (item.year) {
            $item.append('<div class="vsd-result-meta"><strong>Year:</strong> ' + escapeHtml(item.year) + '</div>');
        }
        
        if (item.label) {
            $item.append('<div class="vsd-result-meta"><strong>Label:</strong> ' + escapeHtml(item.label) + '</div>');
        }
        
        if (item.country) {
            $item.append('<div class="vsd-result-meta"><strong>Country:</strong> ' + escapeHtml(item.country) + '</div>');
        }
        
        if (item.genre) {
            $item.append('<div class="vsd-result-meta"><strong>Genre:</strong> ' + escapeHtml(item.genre) + '</div>');
        }
        
        if (item.style) {
            $item.append('<div class="vsd-result-meta"><strong>Style:</strong> ' + escapeHtml(item.style) + '</div>');
        }
        
        if (item.format) {
            $item.append('<div class="vsd-result-meta"><strong>Format:</strong> ' + escapeHtml(item.format) + '</div>');
        }
        
        // Actions
        const $actions = $('<div class="vsd-result-actions"></div>');
        const $addButton = $('<button type="button" class="button button-primary vsd-add-product">Add as Product</button>');
        
        $addButton.on('click', function() {
            addProduct(item.id, $item);
        });
        
        $actions.append($addButton);
        $item.append($actions);
        
        return $item;
    }
    
    /**
     * Create pagination
     */
    function createPagination(pagination) {
        const $pagination = $('<div class="vsd-pagination"></div>');
        
        const $prevButton = $('<button type="button" class="button">Previous</button>');
        if (pagination.page <= 1) {
            $prevButton.prop('disabled', true);
        } else {
            $prevButton.on('click', function() {
                currentPage--;
                searchDiscogs(currentQuery, currentPage);
            });
        }
        
        const $pageInfo = $('<span>Page ' + pagination.page + ' of ' + pagination.pages + '</span>');
        
        const $nextButton = $('<button type="button" class="button">Next</button>');
        if (pagination.page >= pagination.pages) {
            $nextButton.prop('disabled', true);
        } else {
            $nextButton.on('click', function() {
                currentPage++;
                searchDiscogs(currentQuery, currentPage);
            });
        }
        
        $pagination.append($prevButton);
        $pagination.append($pageInfo);
        $pagination.append($nextButton);
        
        return $pagination;
    }
    
    /**
     * Add product to WooCommerce
     */
    function addProduct(releaseId, $itemElement) {
        $itemElement.addClass('adding');
        hideMessage();
        
        $.ajax({
            url: vsdAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'vsd_add_product',
                nonce: vsdAdmin.nonce,
                release_id: releaseId
            },
            success: function(response) {
                $itemElement.removeClass('adding');
                
                if (response.success) {
                    $itemElement.addClass('added');
                    
                    // Update actions
                    const $actions = $itemElement.find('.vsd-result-actions');
                    $actions.html(
                        '<div>✓ Product added successfully!</div>' +
                        '<a href="' + response.data.edit_url + '" class="button" style="margin-top: 10px; width: 100%;">Edit Product</a>'
                    );
                    
                    showMessage(vsdAdmin.strings.add_success, 'success');
                } else {
                    showMessage(response.data.message || vsdAdmin.strings.add_error, 'error');
                }
            },
            error: function() {
                $itemElement.removeClass('adding');
                showMessage(vsdAdmin.strings.add_error, 'error');
            }
        });
    }
    
    /**
     * Show loading indicator
     */
    function showLoading() {
        $('#vsd-loading').show();
        $('#vsd-search-results').hide();
    }
    
    /**
     * Hide loading indicator
     */
    function hideLoading() {
        $('#vsd-loading').hide();
        $('#vsd-search-results').show();
    }
    
    /**
     * Show message
     */
    function showMessage(message, type) {
        const $messageDiv = $('#vsd-message');
        $messageDiv.removeClass('success error');
        $messageDiv.addClass(type);
        $messageDiv.html('<p>' + escapeHtml(message) + '</p>');
        $messageDiv.show();
        
        // Auto-hide success messages after 5 seconds
        if (type === 'success') {
            setTimeout(function() {
                $messageDiv.fadeOut();
            }, 5000);
        }
    }
    
    /**
     * Hide message
     */
    function hideMessage() {
        $('#vsd-message').hide();
    }
    
    /**
     * Escape HTML
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
})(jQuery);
