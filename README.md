# Vinyl Shop Discogs - WordPress Plugin

A WordPress plugin that integrates with the Discogs API to search for vinyl records and add them as WooCommerce products with complete metadata.

## Features

- **Discogs API Integration**: Search the vast Discogs database for vinyl records
- **WooCommerce Integration**: Automatically creates products with all relevant data
- **MVC Architecture**: Clean, maintainable code structure
- **Admin Interface**: User-friendly search interface in WordPress admin panel
- **Complete Metadata**: Stores all relevant vinyl information as product meta fields

## Requirements

- WordPress 5.8 or higher
- WooCommerce 5.0 or higher
- PHP 7.4 or higher
- Discogs API Token (free)

## Installation

1. Upload the `vinyl-shop-discogs` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **Vinyl Shop > Settings** and enter your Discogs API token
4. Start searching and adding vinyl records!

## Getting a Discogs API Token

1. Create a free account at [Discogs.com](https://www.discogs.com)
2. Go to [Developer Settings](https://www.discogs.com/settings/developers)
3. Click "Generate new token"
4. Copy the token and paste it in the plugin settings

## Usage

### Searching for Vinyl Records

1. Navigate to **Vinyl Shop** in your WordPress admin menu
2. Enter an artist name, album title, or release name in the search box
3. Click "Search" to find records in the Discogs database
4. Browse through the results with pagination

### Adding Products

1. Find the vinyl record you want to add
2. Click the "Add as Product" button
3. The plugin will automatically:
   - Create a WooCommerce product
   - Download and set the cover image
   - Add all metadata (artist, label, tracklist, etc.)
   - Create/assign genre and style categories
   - Set a unique SKU based on the Discogs release ID

### Product Metadata

Each product created includes the following custom meta fields:

- **Release Name**: Full title of the release
- **Thumbnail**: Cover image URL
- **Genre**: Musical genre(s) - also added as product category
- **Style**: Musical style(s) - also added as subcategory
- **Country**: Country of release
- **Tracklist**: Complete track listing with durations
- **Label**: Record label(s)
- **Artist**: Artist name(s)
- **Date**: Release year or date
- **Format**: Vinyl format (LP, 12", etc.)

## Plugin Structure

```
vinyl-shop-discogs/
├── assets/
│   ├── css/
│   │   └── admin.css
│   └── js/
│       └── admin.js
├── includes/
│   ├── controllers/
│   │   └── class-vsd-controller-admin.php
│   ├── models/
│   │   └── class-vsd-model-product.php
│   ├── services/
│   │   └── class-vsd-service-discogs-api.php
│   ├── views/
│   │   ├── class-vsd-view-admin.php
│   │   └── class-vsd-view-settings.php
│   └── class-vsd-install.php
└── vinyl-shop-discogs.php
```

## MVC Architecture

The plugin follows the Model-View-Controller (MVC) pattern:

- **Models** (`includes/models/`): Handle data and business logic
  - `VSD_Model_Product`: Manages WooCommerce product creation and metadata

- **Views** (`includes/views/`): Handle presentation and HTML output
  - `VSD_View_Admin`: Main search interface
  - `VSD_View_Settings`: Settings page

- **Controllers** (`includes/controllers/`): Handle user input and coordinate between models and views
  - `VSD_Controller_Admin`: Manages admin pages and AJAX requests

- **Services** (`includes/services/`): Handle external API communication
  - `VSD_Service_Discogs_API`: Discogs API integration

## Settings

### Default Product Status

Choose whether newly created products should be:
- **Draft**: Requires manual review and publishing
- **Published**: Immediately visible in your store

## Frequently Asked Questions

**Q: Do I need to pay for the Discogs API?**  
A: No, the Discogs API is free to use. You just need to create a free account and generate a token.

**Q: Are images downloaded to my server?**  
A: Yes, cover images are downloaded and stored in your WordPress media library.

**Q: Can I edit products after they're created?**  
A: Yes! Products are standard WooCommerce products and can be edited normally.

**Q: What if a product already exists?**  
A: The plugin checks if a product with the same Discogs ID exists and won't create duplicates.

**Q: How are categories created?**  
A: Genres become main categories, and styles become subcategories under their respective genres.

## Support

For issues, questions, or feature requests, please contact the plugin developer.

## License

This plugin is licensed under the GPL v2 or later.

## Changelog

### 1.0.0
- Initial release
- Discogs API integration
- Search functionality
- WooCommerce product creation
- Complete metadata support
- Category management
- Image downloading
- Settings page
