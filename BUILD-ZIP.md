# Creating WordPress.org Submission ZIP

This document explains how to create a clean ZIP file for WordPress.org submission.

## Quick Command

Run this command from the plugin root directory:

```bash
bash /tmp/create-plugin-zip.sh
```

Or use the detailed script below.

## What Gets Included

The ZIP file includes only the files needed for WordPress.org:

- ✅ `duplicate-order-prevention.php` - Main plugin file
- ✅ `readme.txt` - WordPress.org readme
- ✅ `uninstall.php` - Uninstall cleanup
- ✅ `includes/` - PHP class files
- ✅ `assets/` - CSS and JavaScript files
- ✅ `languages/` - Empty directory for translations

## What Gets Excluded

The following files are excluded (they're for GitHub only):

- ❌ `.git/` - Git repository data
- ❌ `.gitignore` - Git configuration
- ❌ `README.md` - GitHub readme
- ❌ `WORDPRESS-PLUGIN-ANALYSIS.md` - Development docs
- ❌ `UPGRADE-SUMMARY.md` - Development docs
- ❌ `SUBMISSION-GUIDE.md` - Development docs
- ❌ `IMPLEMENTATION-SUMMARY.md` - Development docs

## Manual Creation Script

```bash
#!/bin/bash

# Configuration
PLUGIN_NAME="duplicate-order-prevention-for-woocommerce"
TEMP_DIR="/tmp/${PLUGIN_NAME}"
ZIP_FILE="${PLUGIN_NAME}.zip"

# Clean up
rm -rf "$TEMP_DIR"
rm -f "$ZIP_FILE"

# Create temp directory
mkdir -p "$TEMP_DIR"

# Copy plugin files
cp duplicate-order-prevention.php "$TEMP_DIR/"
cp readme.txt "$TEMP_DIR/"
cp uninstall.php "$TEMP_DIR/"

# Copy directories
cp -r includes "$TEMP_DIR/"
cp -r assets "$TEMP_DIR/"

# Create languages directory
mkdir -p "$TEMP_DIR/languages"

# Create ZIP
cd /tmp
zip -r "${PLUGIN_NAME}.zip" "${PLUGIN_NAME}" -x "*.DS_Store" "*__MACOSX*"

# Move to current directory
mv "${ZIP_FILE}" "$OLDPWD/"
cd "$OLDPWD"

echo "✅ Created ${ZIP_FILE} (ready for WordPress.org submission)"
```

## Verification

After creating the ZIP, verify its contents:

```bash
unzip -l duplicate-order-prevention-for-woocommerce.zip
```

Expected structure:
```
duplicate-order-prevention-for-woocommerce/
├── assets/
│   ├── css/
│   │   └── duplicate-order-prevention.css
│   └── js/
│       └── duplicate-order-prevention.js
├── includes/
│   ├── class-duplicate-checker.php
│   └── class-notification-handler.php
├── languages/
├── duplicate-order-prevention.php
├── readme.txt
└── uninstall.php
```

## File Size

The ZIP file should be approximately **13 KB** (compressed).

## Submission Process

After creating the ZIP:

1. Go to https://wordpress.org/plugins/developers/add/
2. Upload `duplicate-order-prevention-for-woocommerce.zip`
3. Submit for review
4. Wait 1-14 days for review
5. Address any feedback

For detailed submission instructions, see `SUBMISSION-GUIDE.md`.

## Automated Build

The ZIP file is automatically excluded from Git (see `.gitignore` line 26: `*.zip`).

To integrate with CI/CD, add this to your workflow:

```yaml
- name: Create WordPress.org ZIP
  run: |
    bash create-plugin-zip.sh
    
- name: Upload artifact
  uses: actions/upload-artifact@v3
  with:
    name: wordpress-plugin
    path: duplicate-order-prevention-for-woocommerce.zip
```

## Notes

- The ZIP file is a **build artifact** and should not be committed to Git
- Always create a fresh ZIP before submission
- Test the ZIP by installing it on a test WordPress site
- The `languages/` directory is empty but required by WordPress.org standards
