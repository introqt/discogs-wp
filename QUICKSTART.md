# Vinyl Shop Discogs Plugin - Quick Start Guide

## 🎵 What This Plugin Does

This WordPress plugin allows you to search the Discogs database (the world's largest music database) and import vinyl records as WooCommerce products with complete metadata including cover art, tracklists, artist info, and more.

## 📁 Complete File Structure

```
vinyl-shop-discogs/
├── vinyl-shop-discogs.php          # Main plugin file
├── README.md                        # Comprehensive documentation
├── INSTALL.md                       # Installation instructions
├── HOOKS.php                        # Hook examples for developers
├── readme.txt                       # WordPress.org plugin readme
├── .gitignore                       # Git ignore rules
│
├── assets/                          # Frontend assets
│   ├── css/
│   │   └── admin.css               # Admin panel styles
│   └── js/
│       └── admin.js                # Admin panel JavaScript
│
└── includes/                        # Core plugin files
    ├── class-vsd-install.php       # Installation/activation handler
    ├── class-vsd-frontend.php      # Frontend display handler
    │
    ├── controllers/                 # MVC Controllers
    │   └── class-vsd-controller-admin.php
    │
    ├── models/                      # MVC Models
    │   └── class-vsd-model-product.php
    │
    ├── services/                    # External services
    │   └── class-vsd-service-discogs-api.php
    │
    └── views/                       # MVC Views
        ├── class-vsd-view-admin.php
        └── class-vsd-view-settings.php
```

## ⚡ Quick Setup (5 Minutes)

### 1. Upload Plugin
Copy the entire `vinyl-shop-discogs` folder to:
```
/wp-content/plugins/vinyl-shop-discogs/
```

### 2. Activate
- Go to WordPress Admin → Plugins
- Find "Vinyl Shop Discogs"
- Click "Activate"

### 3. Get API Token
1. Go to: https://www.discogs.com/settings/developers
2. Click "Generate new token"
3. Copy the token

### 4. Configure
- WordPress Admin → Vinyl Shop → Settings
- Paste your API token
- Save settings

### 5. Start Adding Vinyl!
- WordPress Admin → Vinyl Shop
- Search for any vinyl record
- Click "Add as Product"

## 🎯 Key Features

### For Store Owners
- ✅ Search millions of vinyl releases
- ✅ One-click product import
- ✅ Automatic cover image download
- ✅ Complete metadata (artist, label, tracklist, etc.)
- ✅ Automatic category creation
- ✅ No duplicate products
- ✅ Draft or publish immediately

### For Developers
- ✅ Clean MVC architecture
- ✅ Extensible with hooks and filters
- ✅ WordPress coding standards
- ✅ Well-documented code
- ✅ Autoloading classes
- ✅ AJAX-powered interface

## 📊 Imported Product Data

Each imported vinyl record includes:

| Field | Description | Storage |
|-------|-------------|---------|
| **Release Name** | Full album/release title | Product meta |
| **Thumbnail** | Album cover image | Featured image |
| **Artist** | Artist name(s) | Product meta |
| **Genre** | Musical genre | Product category |
| **Style** | Musical style | Product subcategory |
| **Country** | Country of release | Product meta |
| **Label** | Record label | Product meta |
| **Date** | Release year/date | Product meta |
| **Tracklist** | Complete track listing | Product meta + tab |
| **Format** | Vinyl format (LP, 12", etc.) | Product meta |

## 🔌 Developer Hooks

### Actions
```php
do_action('vsd_before_product_created', $release);
do_action('vsd_after_product_created', $product_id, $release);
```

### Filters
```php
apply_filters('vsd_before_create_product', $release);
apply_filters('vsd_product_status', $status, $release);
apply_filters('vsd_search_results', $results);
```

See `HOOKS.php` for complete examples.

## 🎨 Customization Examples

### Auto-set Product Price
```php
add_action('vsd_after_product_created', function($product_id, $release) {
    $product = wc_get_product($product_id);
    $product->set_regular_price(29.99);
    $product->save();
}, 10, 2);
```

### Modify Product Title
```php
add_filter('vsd_before_create_product', function($release) {
    $release['title'] = '[VINYL] ' . $release['title'];
    return $release;
});
```

### Set Stock Quantity
```php
add_action('vsd_after_product_created', function($product_id) {
    $product = wc_get_product($product_id);
    $product->set_manage_stock(true);
    $product->set_stock_quantity(1);
    $product->save();
}, 10, 1);
```

## 🛠️ MVC Architecture

### Models (`includes/models/`)
Handle data and business logic:
- `VSD_Model_Product` - Creates and manages WooCommerce products

### Views (`includes/views/`)
Handle presentation and HTML:
- `VSD_View_Admin` - Search interface
- `VSD_View_Settings` - Settings page

### Controllers (`includes/controllers/`)
Handle user input and coordinate between models/views:
- `VSD_Controller_Admin` - Admin page management and AJAX

### Services (`includes/services/`)
Handle external API communication:
- `VSD_Service_Discogs_API` - Discogs API integration

## 🔐 Security Features

- ✅ Nonce verification for all AJAX requests
- ✅ Capability checks (manage_woocommerce)
- ✅ Data sanitization and escaping
- ✅ Direct access prevention
- ✅ Input validation

## 📱 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## 🐛 Troubleshooting

### Plugin Won't Activate
**Solution:** Make sure WooCommerce is installed and active first.

### API Token Error
**Solution:** Verify token is copied correctly with no spaces. Generate a new one if needed.

### Images Not Downloading
**Solution:** Check WordPress uploads folder has write permissions (755).

### Search Returns No Results
**Solution:** 
1. Verify API token is valid
2. Check internet connection
3. Try different search terms

## 📞 Support

For issues or questions:
1. Check the README.md
2. Review INSTALL.md
3. Check HOOKS.php for customization examples
4. Contact plugin developer

## 🚀 Next Steps After Installation

1. **Import Test Products**
   - Search for a popular album
   - Import 2-3 products to test

2. **Check Frontend Display**
   - View products on your store
   - Verify metadata displays correctly

3. **Set Pricing**
   - Edit imported products
   - Add regular prices
   - Set sale prices if needed

4. **Configure Stock**
   - Enable stock management
   - Set quantities

5. **Customize (Optional)**
   - Add hooks for auto-pricing
   - Modify product descriptions
   - Add custom fields

6. **Launch Your Store!**
   - Import more products
   - Organize categories
   - Start selling vinyl!

## 📄 License

GPL v2 or later - Free to use and modify

## 🎵 Happy Selling!

You're all set to start building your vinyl shop. The plugin handles all the technical details - you just search, click, and sell!

---

**Plugin Version:** 1.0.0  
**WordPress Required:** 5.8+  
**WooCommerce Required:** 5.0+  
**PHP Required:** 7.4+
