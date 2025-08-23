#!/bin/bash

# Fix Vite Manifest Script for OnlyVerified
# Copies manifest.json from .vite subdirectory to build directory

BUILD_DIR="public/build"
VITE_MANIFEST="$BUILD_DIR/.vite/manifest.json"
TARGET_MANIFEST="$BUILD_DIR/manifest.json"

echo "🔧 Fixing Vite manifest location..."

# Check if .vite manifest exists
if [ -f "$VITE_MANIFEST" ]; then
    # Copy manifest to expected location
    cp "$VITE_MANIFEST" "$TARGET_MANIFEST"
    echo "✅ Copied manifest from $VITE_MANIFEST to $TARGET_MANIFEST"
    
    # Verify the copy worked
    if [ -f "$TARGET_MANIFEST" ]; then
        echo "✅ Manifest is now available at the expected location"
        echo "📊 File size: $(wc -c < "$TARGET_MANIFEST") bytes"
    else
        echo "❌ Failed to copy manifest file"
        exit 1
    fi
else
    echo "⚠️  Vite manifest not found at $VITE_MANIFEST"
    echo "💡 Try running: npm run build"
    exit 1
fi

echo "🎉 Vite manifest fix complete!"
