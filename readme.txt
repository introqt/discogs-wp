=== Vinyl Shop Discogs ===
Contributors: yourname
Tags: discogs, vinyl, woocommerce, music, records
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Search Discogs database and import vinyl records as WooCommerce products with complete metadata.

== Description ==

Vinyl Shop Discogs is a powerful WordPress plugin that integrates your WooCommerce store with the Discogs database - the largest online database of music releases. This plugin allows you to easily search for vinyl records and import them as fully-featured WooCommerce products.

= Key Features =

* **Search Discogs Database** - Search millions of vinyl releases from the Discogs database
* **One-Click Import** - Add vinyl records as WooCommerce products with a single click
* **Complete Metadata** - Automatically imports all relevant information including:
  * Release name and title
  * Artist information
  * Album cover art
  * Record label
  * Release year/date
  * Country of release
  * Genre and style (as categories)
  * Complete tracklist with durations
  * Format details (LP, 12", etc.)
* **Smart Categories** - Automatically creates and assigns product categories based on genres and styles
* **Image Management** - Downloads and sets cover images from Discogs
* **MVC Architecture** - Clean, maintainable code following WordPress best practices
* **User-Friendly Interface** - Easy-to-use admin panel for searching and adding products
* **Duplicate Prevention** - Prevents adding the same release multiple times
* **Customizable Settings** - Choose default product status (draft/published)

= How It Works =

1. Install and activate the plugin
2. Get a free Discogs API token
3. Enter your API token in the plugin settings
4. Search for vinyl records in the admin panel
5. Click "Add as Product" to import releases
6. Edit product details, set prices, and manage inventory
7. Publish to your store!

= Perfect For =

* Record stores
* Vinyl collectors
* Music retailers
* Online music shops
* DJs and music enthusiasts

= Requirements =

* WordPress 5.8 or higher
* WooCommerce 5.0 or higher
* PHP 7.4 or higher
* Free Discogs account and API token

== Installation ==

= Automatic Installation =

1. Log in to your WordPress admin panel
2. Go to Plugins > Add New
3. Search for "Vinyl Shop Discogs"
4. Click "Install Now" and then "Activate"

= Manual Installation =

1. Download the plugin ZIP file
2. Extract the ZIP file
3. Upload the `vinyl-shop-discogs` folder to `/wp-content/plugins/`
4. Activate the plugin through the 'Plugins' menu in WordPress

= Configuration =

1. Get your free Discogs API token from https://www.discogs.com/settings/developers
2. In WordPress admin, go to Vinyl Shop > Settings
3. Enter your API token
4. Choose your default product status
5. Save settings

For detailed installation instructions, see INSTALL.md

== Frequently Asked Questions ==

= Do I need a Discogs account? =

Yes, you need a free Discogs account to generate an API token. Sign up at https://www.discogs.com/

= Is the Discogs API free? =

Yes, the Discogs API is free to use. You just need to create an account and generate a token.

= Are images downloaded to my server? =

Yes, cover images are downloaded and stored in your WordPress media library.

= Can I edit products after importing? =

Absolutely! Imported products are standard WooCommerce products and can be edited just like any other product.

= What happens if I try to add the same release twice? =

The plugin prevents duplicates by checking if a product with the same Discogs release ID already exists.

= How are categories assigned? =

Genres from Discogs become main product categories, and styles become subcategories under their respective genres.

= Can I set prices during import? =

Prices are not included in Discogs data, so you'll need to set them manually after import. This is by design, as pricing is specific to your business.

= Does this work with variable products? =

Currently, the plugin creates simple products. Variable product support may be added in future versions.

== Screenshots ==

1. Admin search interface - Search the Discogs database
2. Search results - Browse vinyl releases with cover art and details
3. Product creation - One-click import to WooCommerce
4. Settings page - Configure your API token and preferences
5. Product display - Vinyl metadata displayed on product pages
6. Tracklist tab - Full track listing on product page

== Changelog ==

= 1.0.0 =
* Initial release
* Discogs API integration
* Search functionality with pagination
* WooCommerce product creation
* Metadata import (artist, label, genre, style, country, tracklist, etc.)
* Automatic category management
* Image downloading and assignment
* Settings page for API configuration
* Frontend display of vinyl metadata
* Tracklist product tab
* Duplicate prevention
* MVC architecture

== Upgrade Notice ==

= 1.0.0 =
Initial release of Vinyl Shop Discogs plugin.

== Additional Info ==

= Support =

For support, feature requests, or bug reports, please contact the plugin developer.

= Documentation =

Full documentation is available in the README.md file included with the plugin.

= Contributing =

Contributions are welcome! The plugin follows WordPress coding standards and uses MVC architecture for maintainability.

== Credits ==

* Discogs API - https://www.discogs.com/developers/
* WooCommerce - https://woocommerce.com/

== Privacy Policy ==

This plugin connects to the Discogs API to search and retrieve vinyl record information. When you perform a search or import a product:
* Your search queries are sent to Discogs servers
* Product information and images are retrieved from Discogs
* Your Discogs API token is used for authentication
* No personal user data is collected or sent by this plugin
* Images are downloaded to your WordPress media library

For Discogs privacy policy, visit: https://www.discogs.com/privacy
