const fs = require('fs');
const path = require('path');
const sharp = require('sharp');

async function convertSVGtoPNG() {
    console.log('🔄 Converting SVG icons to PNG...');
    
    const publicDir = path.join(__dirname, '../public');
    
    const icons = [
        { svg: 'pwa-64x64.svg', png: 'pwa-64x64.png', size: 64 },
        { svg: 'pwa-192x192.svg', png: 'pwa-192x192.png', size: 192 },
        { svg: 'pwa-512x512.svg', png: 'pwa-512x512.png', size: 512 },
        { svg: 'maskable-icon-512x512.svg', png: 'maskable-icon-512x512.png', size: 512 },
        { svg: 'favicon-pwa.svg', png: 'favicon.ico', size: 32 },
        { svg: 'apple-touch-icon-pwa.svg', png: 'apple-touch-icon.png', size: 180 }
    ];
    
    let converted = 0;
    
    for (const icon of icons) {
        const svgPath = path.join(publicDir, icon.svg);
        const pngPath = path.join(publicDir, icon.png);
        
        try {
            if (fs.existsSync(svgPath)) {
                const svgBuffer = fs.readFileSync(svgPath);
                
                if (icon.png.endsWith('.ico')) {
                    // For favicon, create ICO format
                    await sharp(svgBuffer)
                        .resize(icon.size, icon.size)
                        .png()
                        .toFile(pngPath.replace('.ico', '.png'));
                    
                    console.log(`✅ Converted ${icon.svg} to ${icon.png.replace('.ico', '.png')}`);
                } else {
                    // Regular PNG conversion
                    await sharp(svgBuffer)
                        .resize(icon.size, icon.size)
                        .png({ quality: 90, compressionLevel: 9 })
                        .toFile(pngPath);
                    
                    console.log(`✅ Converted ${icon.svg} to ${icon.png} (${icon.size}x${icon.size})`);
                }
                
                converted++;
            } else {
                console.log(`⚠️ SVG file not found: ${icon.svg}`);
            }
        } catch (error) {
            console.error(`❌ Failed to convert ${icon.svg}:`, error.message);
        }
    }
    
    console.log(`\n🎉 Successfully converted ${converted} icons to PNG format!`);
    
    // Clean up SVG files (optional)
    const cleanup = process.argv.includes('--cleanup');
    if (cleanup) {
        console.log('\n🧹 Cleaning up SVG files...');
        for (const icon of icons) {
            const svgPath = path.join(publicDir, icon.svg);
            if (fs.existsSync(svgPath)) {
                fs.unlinkSync(svgPath);
                console.log(`🗑️ Removed ${icon.svg}`);
            }
        }
    }
}

// Exécuter la conversion
if (require.main === module) {
    convertSVGtoPNG().catch(console.error);
}

module.exports = { convertSVGtoPNG };
