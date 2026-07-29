# OMSAR Theme Installation Guide

This guide will help you set up the OMSAR WordPress theme installer workflow.

## Overview

The theme installer workflow includes three main components:

1. **Plugin Installation** - Automatic detection and installation of required/recommended plugins
2. **Demo Import** - One-click import of demo content (pages, posts, menus, widgets, customizer settings)
3. **Custom Post Types & ACF Fields** - Starter files for custom post types, taxonomies, and ACF JSON field loading

## Prerequisites

Before starting, ensure you have:

- WordPress 5.0 or higher installed
- PHP 7.4 or higher
- Access to WordPress admin panel

## Step 1: Download TGM Plugin Activation Library

1. Download the TGM Plugin Activation library from:
   https://github.com/TGMPA/TGM-Plugin-Activation

2. Extract the ZIP file and locate `class-tgm-plugin-activation.php`

3. Create the following directory structure in your theme:
   ```
   wp-content/themes/omsar/inc/tgm/
   ```

4. Place `class-tgm-plugin-activation.php` in:
   ```
   wp-content/themes/omsar/inc/tgm/class-tgm-plugin-activation.php
   ```

## Step 2: Prepare Plugin Files

### For WordPress.org Plugins

Plugins available on WordPress.org will be automatically downloaded during installation. No action needed.

### For Premium/Bundled Plugins

For plugins that are not on WordPress.org (like Elementor Pro, ACF Pro), you need to:

1. Create the following directory:
   ```
   wp-content/themes/omsar/inc/plugins/
   ```

2. Place plugin ZIP files in this directory with these exact names:
   - `elementor-pro.zip` - Elementor Pro plugin
   - `advanced-custom-fields-pro.zip` - ACF Pro plugin
   - `omsar-social-share.zip` - Custom OMSAR Social Share plugin (if applicable)

**Important:** Only include plugin ZIP files that you have a license to distribute.

## Step 3: Prepare Demo Content Files

1. Create the following directory:
   ```
   wp-content/themes/omsar/demo-content/
   ```

2. Export your demo content and place the following files in this directory:

   - **demo-content.xml** - WordPress export file containing pages, posts, media, etc.
     - Export from: Tools → Export → All content
   
   - **widgets.wie** - Widget Importer & Exporter file
     - Export from: Tools → Widget Importer & Exporter → Export Widgets
   
   - **customizer.dat** - Customizer settings export
     - Export from: Appearance → Customize → Export/Import → Export

3. (Optional) For multi-language setups, you can create separate files:
   - `demo-content-ar.xml` - Arabic content
   - `demo-content-en.xml` - English content
   - `widgets-ar.wie` - Arabic widgets
   - `widgets-en.wie` - English widgets
   - `customizer-ar.dat` - Arabic customizer settings
   - `customizer-en.dat` - English customizer settings

4. (Optional) Add a preview image:
   - `preview.jpg` - Screenshot of your demo site (recommended size: 1200x800px)

## Step 4: Configure Custom Post Types and Taxonomies

1. Open `wp-content/themes/omsar/inc/custom-post-types.php`

2. Uncomment and modify the example code to register your custom post types and taxonomies

3. Use `register_post_type()` for custom post types and `register_taxonomy()` for taxonomies

4. Example structure is provided in the file with detailed comments

## Step 5: Prepare ACF JSON Field Groups

1. Create the following directory:
   ```
   wp-content/themes/omsar/inc/acf-json/
   ```

2. Export your ACF field groups:
   - Go to Custom Fields → Field Groups
   - Edit each field group
   - Click "Export" and save as JSON

3. Place the JSON files in `inc/acf-json/` directory

4. The theme will automatically load these field groups on activation

## Step 6: Theme Installation Process

### For End Users (Theme Installation)

1. **Install the theme:**
   - Upload the theme ZIP file via WordPress admin: Appearance → Themes → Add New → Upload Theme
   - Or extract the theme folder to `wp-content/themes/omsar/`

2. **Activate the theme:**
   - Go to Appearance → Themes
   - Click "Activate" on the OMSAR theme

3. **Install Required Plugins:**
   - After activation, you'll see a notice prompting to install required plugins
   - Click "Install Required Plugins"
   - Follow the on-screen instructions to install and activate plugins

4. **Import Demo Content:**
   - Install the "One Click Demo Import" plugin (if not already installed)
   - Go to Appearance → Demo Import
   - Select your demo import set
   - Click "Import" and wait for the process to complete

5. **Configure Languages (if using Polylang):**
   - Go to Languages → Settings
   - Add your languages (English, Arabic, etc.)
   - Assign language to imported content
   - Configure menu locations for each language

## File Structure

After setup, your theme directory should look like this:

```
wp-content/themes/omsar/
├── inc/
│   ├── tgm/
│   │   └── class-tgm-plugin-activation.php
│   ├── plugins/
│   │   ├── elementor-pro.zip
│   │   ├── advanced-custom-fields-pro.zip
│   │   └── omsar-social-share.zip
│   ├── acf-json/
│   │   ├── group_xxxxx.json
│   │   └── group_yyyyy.json
│   ├── tgm-plugin-activation.php
│   ├── demo-import.php
│   ├── custom-post-types.php
│   └── acf-json-loader.php
├── demo-content/
│   ├── demo-content.xml
│   ├── widgets.wie
│   ├── customizer.dat
│   └── preview.jpg
├── functions.php
└── (other theme files...)
```

## Customization

### Adding More Plugins

Edit `wp-content/themes/omsar/inc/tgm-plugin-activation.php`:

1. Add a new array entry in the `$plugins` array
2. Set `'required' => true` for required plugins or `'required' => false` for recommended
3. For WordPress.org plugins, only specify `name` and `slug`
4. For bundled plugins, also specify `source` path

### Modifying Demo Import

Edit `wp-content/themes/omsar/inc/demo-import.php`:

1. Modify the `omsar_ocdi_import_files()` function to add more import sets
2. Customize `omsar_ocdi_after_import_setup()` to configure post-import settings

### Adding Custom Post Types

Edit `wp-content/themes/omsar/inc/custom-post-types.php`:

1. Add your `register_post_type()` calls in `omsar_register_custom_post_types()`
2. Add your `register_taxonomy()` calls in `omsar_register_custom_taxonomies()`

## Troubleshooting

### Plugins Not Installing

- Ensure TGM Plugin Activation library is properly installed
- Check file permissions on `inc/tgm/` directory
- Verify plugin ZIP files are in the correct location for bundled plugins

### Demo Import Fails

- Check that demo content files exist in `demo-content/` directory
- Verify file permissions are correct
- Increase PHP memory limit if needed
- Check PHP error logs for specific issues

### ACF Fields Not Loading

- Ensure ACF Pro is installed and activated
- Verify JSON files are in `inc/acf-json/` directory
- Check file permissions on JSON files
- Verify JSON files are valid (not corrupted)

### Custom Post Types Not Showing

- Flush rewrite rules: Settings → Permalinks → Save Changes
- Check that `flush_rewrite_rules()` is called after registration
- Verify post type registration code is correct

## Support

For issues or questions, refer to:

- TGM Plugin Activation: https://github.com/TGMPA/TGM-Plugin-Activation
- One Click Demo Import: https://wordpress.org/plugins/one-click-demo-import/
- ACF Documentation: https://www.advancedcustomfields.com/resources/

## Notes

- **Do not create the ZIP file manually** - The workflow is designed to work inside the theme folder. Create the ZIP file after adding all code and demo files.
- The installer workflow supports both English and Arabic content through Polylang integration.
- All code includes clear comments explaining each step for easy customization.
