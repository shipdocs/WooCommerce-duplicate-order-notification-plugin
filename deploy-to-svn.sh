#!/bin/bash
#
# Deploy plugin to WordPress.org SVN
# Usage: ./deploy-to-svn.sh <version> <svn-password>
# Example: ./deploy-to-svn.sh 1.2.3 svn_HS82Z3WGTgPimjZfkJ2RfTerfRwC6Wcae9ec32a

set -e

# Configuration
PLUGIN_SLUG="duplicate-order-prevention-for-woocommerce"
SVN_USERNAME="martinsplinter"
SVN_REPO="https://plugins.svn.wordpress.org/${PLUGIN_SLUG}"
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
TEMP_SVN_DIR="/tmp/${PLUGIN_SLUG}-svn"

# Check arguments
if [ -z "$1" ] || [ -z "$2" ]; then
    echo "❌ Usage: ./deploy-to-svn.sh <version> <svn-password>"
    echo "Example: ./deploy-to-svn.sh 1.2.3 svn_HS82Z3WGTgPimjZfkJ2RfTerfRwC6Wcae9ec32a"
    exit 1
fi

VERSION="$1"
SVN_PASSWORD="$2"

echo "🚀 Deploying v${VERSION} to WordPress.org SVN..."
echo ""

# Step 1: Checkout SVN repo
echo "📥 Checking out SVN repository..."
rm -rf "$TEMP_SVN_DIR"
svn co "$SVN_REPO" "$TEMP_SVN_DIR" --depth=immediates > /dev/null

# Step 2: Update trunk
echo "📝 Updating trunk with latest code..."
cd "$TEMP_SVN_DIR/trunk"
rm -rf *
cp -r "$SCRIPT_DIR"/* .

# Remove development files
rm -rf .git .gitignore docker-compose.yml BUILD-ZIP.md IMPLEMENTATION-SUMMARY.md README.md SUBMISSION-GUIDE.md UPGRADE-SUMMARY.md WORDPRESS-PLUGIN-ANALYSIS.md build-zip.sh duplicate-order-prevention-for-woocommerce.zip deploy-to-svn.sh

# Step 3: Add files to SVN
echo "➕ Adding files to SVN..."
svn add * > /dev/null 2>&1 || true

# Step 4: Commit trunk
echo "💾 Committing trunk (v${VERSION})..."
cd "$TEMP_SVN_DIR"
svn commit -m "v${VERSION}: Update plugin" \
    --username "$SVN_USERNAME" \
    --password "$SVN_PASSWORD" \
    --non-interactive > /dev/null

# Step 5: Create tag
echo "🏷️  Creating tag v${VERSION}..."
svn copy "$SVN_REPO/trunk" "$SVN_REPO/tags/${VERSION}" \
    -m "Tag version ${VERSION}" \
    --username "$SVN_USERNAME" \
    --password "$SVN_PASSWORD" \
    --non-interactive > /dev/null

# Cleanup
rm -rf "$TEMP_SVN_DIR"

echo ""
echo "✅ Successfully deployed v${VERSION} to WordPress.org!"
echo ""
echo "📋 Next steps:"
echo "  1. Wait 15 minutes for WordPress.org to process the update"
echo "  2. Visit: https://wordpress.org/plugins/${PLUGIN_SLUG}/"
echo "  3. Verify v${VERSION} is listed as the latest version"
echo ""

