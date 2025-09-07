#!/bin/bash

# Development sync script for Menu Manager plugin
# This script syncs changes between the development repo and vendor directory

SOURCE_DIR="/home/binu/my-project/menu-manager-plugin"
TARGET_DIR="vendor/mycompany/menu-manager"

echo "🔄 Syncing Menu Manager plugin from development repo..."

# Remove existing vendor copy
rm -rf "$TARGET_DIR"

# Copy fresh from development repo
cp -r "$SOURCE_DIR" "$TARGET_DIR"

# Remove git files from vendor copy to avoid confusion
rm -rf "$TARGET_DIR/.git"

echo "✅ Menu Manager plugin synced successfully!"
echo "📝 You can now test changes in DDEV"
echo ""
echo "Next steps:"
echo "1. Make changes in: $SOURCE_DIR"
echo "2. Run: ./dev-sync.sh"
echo "3. Test with: ddev craft plugin/list"