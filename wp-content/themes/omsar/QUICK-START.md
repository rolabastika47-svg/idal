# Quick Start Guide - OMSAR Theme Installer

This is a quick reference guide for setting up the OMSAR theme installer workflow.

## Required Downloads

1. **TGM Plugin Activation Library**
   - Download: https://github.com/TGMPA/TGM-Plugin-Activation
   - Extract and place `class-tgm-plugin-activation.php` in: `inc/tgm/`

## Directory Structure Setup

Create these directories and place files as indicated:

```
wp-content/themes/omsar/
├── inc/
│   ├── tgm/
│   │   └── class-tgm-plugin-activation.php  ← Download from GitHub
│   ├── plugins/
│   │   ├── elementor-pro.zip               ← Your plugin ZIP
│   │   ├── advanced-custom-fields-pro.zip  ← Your plugin ZIP
│   │   └── omsar-social-share.zip          ← Your plugin ZIP (if applicable)
│   └── acf-json/
│       └── (your ACF JSON files here)
└── demo-content/
    ├── demo-content.xml                    ← WordPress export
    ├── widgets.wie                         ← Widget export
    ├── customizer.dat                      ← Customizer export
    └── preview.jpg                         ← Optional preview image
```

## Quick Setup Checklist

- [ ] Download and place TGM Plugin Activation library
- [ ] Add premium plugin ZIP files to `inc/plugins/`
- [ ] Export and add demo content files to `demo-content/`
- [ ] Export ACF field groups to `inc/acf-json/`
- [ ] Customize `inc/custom-post-types.php` with your CPTs/taxonomies
- [ ] Test theme activation and plugin installation
- [ ] Test demo import process
- [ ] Create final ZIP file for distribution

## File Locations Reference

| Component | File Location |
|-----------|--------------|
| Plugin Activation Config | `inc/tgm-plugin-activation.php` |
| Demo Import Config | `inc/demo-import.php` |
| Custom Post Types | `inc/custom-post-types.php` |
| ACF JSON Loader | `inc/acf-json-loader.php` |
| Main Functions | `functions.php` (already includes all components) |

## Next Steps

1. Follow the detailed instructions in `INSTALLATION.md`
2. Customize the code files as needed for your specific requirements
3. Test the installation workflow on a fresh WordPress installation
4. Create the final ZIP file after all files are in place

## Important Notes

- **Do NOT create the ZIP file yet** - The workflow is designed to work inside the theme folder
- All code is ready to use - just add your actual content files (XML, WIE, DAT, JSON)
- The installer supports both English and Arabic content via Polylang
- All files include detailed comments for easy customization
