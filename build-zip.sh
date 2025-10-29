#!/bin/bash
#
# Build script for creating WordPress.org submission ZIP
# 
# Usage: ./build-zip.sh
#
# This script creates a clean ZIP file containing only the files needed
# for WordPress.org submission, excluding development documentation and Git files.

set -e

# Configuration
PLUGIN_NAME="duplicate-order-prevention-for-woocommerce"
TEMP_DIR="/tmp/${PLUGIN_NAME}"
ZIP_FILE="${PLUGIN_NAME}.zip"
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

echo "🔨 Building WordPress.org submission ZIP..."
echo ""

# Clean up previous builds
echo "📦 Cleaning up previous builds..."
rm -rf "$TEMP_DIR"
rm -f "${SCRIPT_DIR}/${ZIP_FILE}"

# Create temp directory
echo "📁 Creating temporary directory..."
mkdir -p "$TEMP_DIR"

# Copy plugin files
echo "📄 Copying plugin files..."
cp "${SCRIPT_DIR}/duplicate-order-prevention-for-woocommerce.php" "$TEMP_DIR/"
cp "${SCRIPT_DIR}/readme.txt" "$TEMP_DIR/"
cp "${SCRIPT_DIR}/uninstall.php" "$TEMP_DIR/"

# Copy directories
echo "📂 Copying directories..."
cp -r "${SCRIPT_DIR}/includes" "$TEMP_DIR/"
cp -r "${SCRIPT_DIR}/assets" "$TEMP_DIR/"

# Create languages directory (required by WordPress.org)
echo "🌍 Creating languages directory..."
mkdir -p "$TEMP_DIR/languages"

# Create ZIP file
echo "🗜️  Creating ZIP archive..."
cd /tmp
zip -r "${ZIP_FILE}" "${PLUGIN_NAME}" -x "*.DS_Store" "*__MACOSX*" > /dev/null

# Move to script directory
mv "${ZIP_FILE}" "${SCRIPT_DIR}/"
cd "${SCRIPT_DIR}"

# Show results
echo ""
echo "✅ ZIP file created successfully!"
echo ""
echo "📦 File: ${ZIP_FILE}"
echo "📊 Size: $(du -h "${ZIP_FILE}" | cut -f1)"
echo "📁 Location: ${SCRIPT_DIR}/${ZIP_FILE}"
echo ""

# Verify ZIP contents
echo "📋 Contents:"
unzip -l "${ZIP_FILE}" | tail -n +4 | head -n -2 | awk '{print "   " $4}'
echo ""

# Test ZIP integrity
echo "🔍 Verifying ZIP integrity..."
if unzip -t "${ZIP_FILE}" > /dev/null 2>&1; then
    echo "✅ ZIP file is valid"
else
    echo "❌ ZIP file is corrupted!"
    exit 1
fi

echo ""
echo "🎉 Ready for WordPress.org submission!"
echo ""
echo "Next steps:"
echo "  1. Go to https://wordpress.org/plugins/developers/add/"
echo "  2. Upload ${ZIP_FILE}"
echo "  3. Submit for review"
echo ""
echo "For detailed instructions, see SUBMISSION-GUIDE.md"
