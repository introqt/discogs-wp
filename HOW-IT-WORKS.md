# How the Vinyl Shop Discogs Plugin Works

## 🎯 High-Level Overview

The plugin lets WordPress store owners search Discogs (the world's largest music database) and import vinyl records as WooCommerce products with one click. It's built using **MVC architecture** (Model-View-Controller) for clean separation of concerns.

---

## 📋 The Complete User Flow

```
1. Store owner opens "Vinyl Shop" in WordPress admin
   ↓
2. Types search query: "Pink Floyd Dark Side"
   ↓
3. Plugin sends AJAX request to Discogs API
   ↓
4. Discogs returns vinyl records matching query
   ↓
5. Results displayed in grid with cover art
   ↓
6. Owner clicks "Add as Product" button
   ↓
7. Plugin fetches full release details from Discogs
   ↓
8. Creates WooCommerce product with all metadata
   ↓
9. Downloads album cover as featured image
   ↓
10. Creates categories for genres/styles
    ↓
11. ✅ Product ready to sell!
```

---

## 🏗️ Architecture Breakdown

### **1. Entry Point** (`vinyl-shop-discogs.php`)

```php
// WordPress loads this file first
Vinyl_Shop_Discogs::get_instance();
```

**What it does:**

- **Singleton pattern** - ensures only one instance runs
- **Autoloader** - automatically loads classes when needed (converts `VSD_Model_Product` → `class-vsd-model-product.php`)
- **Hook registration** - connects plugin to WordPress events
- **Asset loading** - enqueues CSS/JS on admin pages

**Key code:**

```php
public function autoload($class) {
    $prefix = 'VSD_';
    if (strncmp($prefix, $class, strlen($prefix)) === 0) {
        // VSD_Controller_Admin → class-vsd-controller-admin.php
        $file = 'class-' . str_replace('_', '-', strtolower($class)) . '.php';
        require $file;
    }
}
```

---

### **2. Controller Layer** (`includes/controllers/`)

#### Handles user actions and coordinates everything

```php
class VSD_Controller_Admin {
    public function __construct() {
        // Register WordPress hooks
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_ajax_vsd_search_discogs', array($this, 'ajax_search_discogs'));
        add_action('wp_ajax_vsd_add_product', array($this, 'ajax_add_product'));
    }
}
```

**What it does:**

1. **Creates admin menu** - Adds "Vinyl Shop" to WordPress sidebar
2. **Handles AJAX search** - When user searches, processes request
3. **Handles AJAX add** - When user clicks "Add", creates product

**Flow for search:**

```php
ajax_search_discogs() {
    1. Verify nonce (security)
    2. Check user permissions
    3. Get search query from $_POST
    4. Call API service: $api->search($query)
    5. Return JSON results to JavaScript
}
```

**Flow for adding product:**

```php
ajax_add_product() {
    1. Verify nonce
    2. Check permissions
    3. Get release ID from $_POST
    4. API: Get full release details
    5. Model: Create WooCommerce product
    6. Return success + product edit URL
}
```

---

### **3. Model Layer** (`includes/models/`)

#### Handles data and business logic

```php
class VSD_Model_Product {
    public function create_from_discogs($release) {
        // 1. Check for duplicates
        // 2. Create WooCommerce product
        // 3. Add all metadata
        // 4. Create categories
        // 5. Download images
        // 6. Return product ID
    }
}
```

**Step-by-step process:**

#### Step 1: Create Product

```php
$product = new WC_Product_Simple();
$product->set_name($release['title']);
$product->set_description($this->build_description($release));
$product->set_sku('DISCOGS-' . $release['id']); // Unique SKU
$product_id = $product->save();
```

#### Step 2: Add Metadata

```php
update_post_meta($product_id, '_vsd_artist', $release['artists']);
update_post_meta($product_id, '_vsd_label', $release['labels']);
update_post_meta($product_id, '_vsd_country', $release['country']);
update_post_meta($product_id, '_vsd_tracklist', $formatted_tracklist);
// ... etc
```

#### Step 3: Create Categories

```php
// Genres become main categories
$genre_term = wp_insert_term('Rock', 'product_cat');

// Styles become subcategories
$style_term = wp_insert_term('Progressive Rock', 'product_cat', [
    'parent' => $genre_term['term_id']
]);

// Assign to product
wp_set_object_terms($product_id, [$genre_term_id, $style_term_id], 'product_cat');
```

#### Step 4: Download Image

```php
$tmp = download_url($release['cover_image']);
$attachment_id = media_handle_sideload($tmp, 0, $release['title']);
set_post_thumbnail($product_id, $attachment_id);
```

---

### **4. Service Layer** (`includes/services/`)

#### Handles external API communication

```php
class VSD_Service_Discogs_API {
    private $token; // API token from settings
    
    public function search($query) {
        $url = 'https://api.discogs.com/database/search?q=' . $query . '&token=' . $this->token;
        $response = wp_remote_get($url);
        return json_decode($response['body']);
    }
    
    public function get_release($release_id) {
        $url = 'https://api.discogs.com/releases/' . $release_id . '?token=' . $this->token;
        $response = wp_remote_get($url);
        return json_decode($response['body']);
    }
}
```

**What API returns for search:**

```json
{
    "results": [
        {
            "id": 249504,
            "title": "Pink Floyd - The Dark Side Of The Moon",
            "year": "1973",
            "genre": ["Rock"],
            "style": ["Progressive Rock"],
            "label": ["Harvest"],
            "thumb": "https://example.com/image.jpg"
        }
    ]
}
```

**What API returns for release details:**

```json
{
    "id": 249504,
    "title": "The Dark Side Of The Moon",
    "artists": [{"name": "Pink Floyd"}],
    "labels": [{"name": "Harvest"}],
    "country": "UK",
    "released": "1973-03-01",
    "genres": ["Rock"],
    "styles": ["Progressive Rock"],
    "tracklist": [
        {"position": "A1", "title": "Speak To Me", "duration": "1:30"},
        {"position": "A2", "title": "Breathe", "duration": "2:43"}
    ],
    "images": [
        {"uri": "https://example.com/full-size.jpg"}
    ]
}
```

---

### **5. View Layer** (`includes/views/`)

#### Handles HTML output

```php
class VSD_View_Admin {
    public function render() {
        ?>
        <div class="wrap">
            <h1>Vinyl Shop - Discogs Search</h1>
            
            <form id="vsd-search-form">
                <input type="text" id="vsd-search-query" />
                <button type="submit">Search</button>
            </form>
            
            <div id="vsd-search-results"></div>
        </div>
        <?php
    }
}
```

**Views are "dumb"** - they just display data, no logic.

---

### **6. Frontend Assets** (`assets/`)

#### JavaScript (`assets/js/admin.js`)

```javascript
// When user submits search form
$('#vsd-search-form').on('submit', function(e) {
    e.preventDefault();
    
    // AJAX call to WordPress
    $.ajax({
        url: vsdAdmin.ajax_url,
        data: {
            action: 'vsd_search_discogs',
            nonce: vsdAdmin.nonce,
            query: $('#vsd-search-query').val()
        },
        success: function(response) {
            displayResults(response.data);
        }
    });
});

// When user clicks "Add as Product"
$('.vsd-add-product').on('click', function() {
    $.ajax({
        url: vsdAdmin.ajax_url,
        data: {
            action: 'vsd_add_product',
            nonce: vsdAdmin.nonce,
            release_id: $(this).data('release-id')
        },
        success: function(response) {
            // Show success message + edit link
        }
    });
});
```

#### CSS (`assets/css/admin.css`)

Styles the admin interface with grid layout, loading spinners, etc.

---

## 🔄 Complete Data Flow Example

Let's trace a real search for "Pink Floyd":

### Step 1: User Types & Clicks Search

```
Browser → JavaScript catches form submit
```

### Step 2: AJAX Request

```javascript
POST /wp-admin/admin-ajax.php
{
    action: 'vsd_search_discogs',
    nonce: 'abc123',
    query: 'Pink Floyd'
}
```

### Step 3: WordPress Routes to Controller

```
WordPress sees action='vsd_search_discogs'
→ Calls VSD_Controller_Admin::ajax_search_discogs()
```

### Step 4: Controller Validates & Delegates

```php
// Security check
check_ajax_referer('vsd_ajax_nonce', 'nonce');

// Permission check
if (!current_user_can('manage_woocommerce')) {
    wp_send_json_error();
}

// Call service
$api = new VSD_Service_Discogs_API();
$results = $api->search('Pink Floyd');
```

### Step 5: Service Calls Discogs API

```php
GET https://api.discogs.com/database/search?q=Pink+Floyd&token=xxx
→ Returns JSON with vinyl records
```

### Step 6: Controller Returns to JavaScript

```json
{
    "success": true,
    "data": {
        "results": [...],
        "pagination": {...}
    }
}
```

### Step 7: JavaScript Displays Results

```javascript
response.data.results.forEach(function(item) {
    // Create HTML card for each vinyl
    $grid.append(createResultCard(item));
});
```

### Step 8: User Clicks "Add as Product"

```
→ New AJAX request with release_id
→ Controller calls Model
→ Model creates WooCommerce product
→ Success response with edit URL
```

---

## 🗄️ Database Storage

### WooCommerce Product (wp_posts)

```sql
post_type = 'product'
post_title = 'Pink Floyd - Dark Side Of The Moon'
post_content = '<description with tracklist>'
```

### Product Meta (wp_postmeta)

```sql
_vsd_discogs_id = 249504
_vsd_artist = 'Pink Floyd'
_vsd_label = 'Harvest'
_vsd_country = 'UK'
_vsd_date = '1973'
_vsd_genre = 'Rock'
_vsd_style = 'Progressive Rock'
_vsd_tracklist = 'A1. Speak To Me (1:30)...'
```

### Categories (wp_terms + wp_term_taxonomy)

```sql
term: 'Rock' (parent: 0)
term: 'Progressive Rock' (parent: Rock)
```

### Featured Image (wp_posts)

```sql
post_type = 'attachment'
→ Linked to product via _thumbnail_id meta
```

---

## 🔐 Security Features

1. **Nonce verification** - Every AJAX request verified
2. **Capability checks** - `current_user_can('manage_woocommerce')`
3. **Sanitization** - All input cleaned: `sanitize_text_field()`
4. **Escaping** - All output escaped: `esc_html()`, `esc_url()`
5. **Direct access prevention** - `if (!defined('ABSPATH')) exit;`

---

## 🎨 MVC Benefits

**Without MVC (messy):**

```php
// Everything in one file - hard to maintain
function handle_search() {
    // HTML mixed with logic
    echo '<form>';
    $data = call_api();
    echo '<results>';
    // Database queries here too
}
```

**With MVC (clean):**

```php
Controller: Receives request → calls Model/Service
Service: Talks to Discogs API
Model: Creates WooCommerce product
View: Displays HTML
```

Each component has **one job** and can be:

- Tested independently
- Modified without breaking others
- Reused in different contexts

---

## 🚀 Summary

The plugin is essentially a **bridge** between Discogs and WooCommerce:

```
Discogs API ←→ [Plugin Service] ←→ [Plugin Model] ←→ WooCommerce
                        ↕
                  [Controller]
                        ↕
                    [View] ←→ User
```

It automates what would otherwise be manual data entry, turning this:

- Search Discogs website
- Copy album name
- Find cover image
- Save image
- Create product in WooCommerce
- Manually enter 15+ fields
- Repeat for each vinyl

Into this:

- Search
- Click "Add"
- Done! ✅

---

## 📚 File Reference

### Core Files

| File | Purpose |
|------|---------|
| `vinyl-shop-discogs.php` | Main plugin file, entry point, autoloader |
| `includes/class-vsd-install.php` | Activation/deactivation handler |
| `includes/class-vsd-frontend.php` | Frontend product display |

### MVC Components

| File | Layer | Purpose |
|------|-------|---------|
| `includes/controllers/class-vsd-controller-admin.php` | Controller | Admin page & AJAX handlers |
| `includes/models/class-vsd-model-product.php` | Model | Product creation & metadata |
| `includes/services/class-vsd-service-discogs-api.php` | Service | Discogs API integration |
| `includes/views/class-vsd-view-admin.php` | View | Search interface HTML |
| `includes/views/class-vsd-view-settings.php` | View | Settings page HTML |

### Assets

| File | Purpose |
|------|---------|
| `assets/css/admin.css` | Admin interface styling |
| `assets/js/admin.js` | AJAX search & add functionality |

---

## 🔧 Extension Points

The plugin provides hooks for customization:

### Actions

```php
// Before product creation
do_action('vsd_before_product_created', $release);

// After product creation
do_action('vsd_after_product_created', $product_id, $release);
```

### Filters

```php
// Modify release data before creating product
$release = apply_filters('vsd_before_create_product', $release);

// Change default product status
$status = apply_filters('vsd_product_status', 'draft', $release);
```

See `HOOKS.php` for more examples.
