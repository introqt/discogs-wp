# Installation Instructions

## Step 1: Install the Plugin

1. Copy the entire `vinyl-shop-discogs` folder to your WordPress installation:

   ```text
   /wp-content/plugins/vinyl-shop-discogs/
   ```

2. Log in to your WordPress admin panel

3. Go to **Plugins > Installed Plugins**

4. Find "Vinyl Shop Discogs" in the list

5. Click **Activate**

## Step 2: Get Your Discogs API Token

1. Go to https://www.discogs.com/

2. Create a free account or log in if you already have one

3. Navigate to your account settings

4. Go to **Developer Settings**: https://www.discogs.com/settings/developers

5. Click the **Generate new token** button

6. Copy the token (it will look something like: `AbCdEfGhIjKlMnOpQrStUvWxYz123456789`)

## Step 3: Configure the Plugin

1. In your WordPress admin, go to **Vinyl Shop > Settings**

2. Paste your Discogs API token in the **Discogs API Token** field

3. Choose your **Default Product Status**:
   - **Draft**: New products will be saved as drafts (recommended for review before publishing)
   - **Published**: New products will be immediately published to your store

4. Click **Save Settings**

## Step 4: Start Adding Vinyl Records

1. Go to **Vinyl Shop** in the WordPress admin menu

2. Enter a search query (artist name, album title, etc.)

3. Click **Search**

4. Browse the results from Discogs

5. Click **Add as Product** on any vinyl record you want to add

6. The product will be automatically created with:
   - Product name
   - Cover image
   - Full description with tracklist
   - All metadata (artist, label, country, genre, style, etc.)
   - Proper categories

7. After adding, you can click **Edit Product** to:
   - Set the price
   - Adjust stock quantity
   - Modify any details
   - Publish (if saved as draft)

## Troubleshooting

### "WooCommerce Required" Error

- Make sure WooCommerce is installed and activated
- Go to **Plugins** and activate WooCommerce if it's installed but not active
- If WooCommerce is not installed, go to **Plugins > Add New**, search for "WooCommerce", install and activate it

### API Token Not Working

- Make sure you copied the entire token (no spaces before/after)
- Verify the token is active on your Discogs account
- Try generating a new token if the old one doesn't work

### Images Not Downloading

- Check that your WordPress installation has write permissions to the uploads folder
- Verify your server can make outbound HTTP requests
- Check PHP memory limit (minimum 128MB recommended)

### Products Not Appearing

- If products are set to "Draft" status, they won't appear in your store until published
- Go to **Products** in WordPress admin to see all products including drafts
- Edit the product and click **Publish** to make it visible

## File Permissions

Ensure your WordPress uploads directory has proper write permissions:
```
/wp-content/uploads/ (chmod 755 or 775)
```

## Server Requirements

- PHP 7.4 or higher
- WordPress 5.8 or higher
- WooCommerce 5.0 or higher
- PHP extensions:
  - curl (for API requests)
  - gd or imagick (for image processing)

## Support

If you encounter any issues not covered here, please check:
- WordPress error logs
- PHP error logs
- Browser console for JavaScript errors

## Next Steps

After installation:
1. Import a few test products
2. Check how they appear on your frontend
3. Adjust the theme styling if needed
4. Set prices and inventory for imported products
5. Start building your vinyl shop!
