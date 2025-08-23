/**
 * Script pour créer des icônes PWA de base
 * Pour une version production, utilisez un outil comme @capacitor/assets ou faites-les manuellement
 */

const fs = require('fs');
const path = require('path');

// Configuration des icônes nécessaires
const iconSizes = [
    { size: 64, filename: 'pwa-64x64.png' },
    { size: 192, filename: 'pwa-192x192.png' },
    { size: 512, filename: 'pwa-512x512.png' },
    { size: 512, filename: 'maskable-icon-512x512.png' }
];

// Créer un SVG simple avec texte OnlyVerified
const createSVGIcon = (size) => {
    const fontSize = Math.floor(size / 8);
    const iconSvg = `
<svg width="${size}" height="${size}" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:#6366f1;stop-opacity:1" />
      <stop offset="100%" style="stop-color:#8b5cf6;stop-opacity:1" />
    </linearGradient>
  </defs>
  
  <!-- Background circle -->
  <circle cx="${size/2}" cy="${size/2}" r="${size/2 - 2}" fill="url(#grad1)" stroke="white" stroke-width="2"/>
  
  <!-- Icon content -->
  <g transform="translate(${size/2}, ${size/2})">
    <!-- Checkmark symbol -->
    <path d="M -${size/6} 0 L -${size/12} ${size/8} L ${size/4} -${size/6}" 
          stroke="white" 
          stroke-width="${Math.max(2, size/32)}" 
          fill="none" 
          stroke-linecap="round" 
          stroke-linejoin="round"/>
  </g>
  
  <!-- Text (for larger icons) -->
  ${size >= 192 ? `
    <text x="${size/2}" y="${size - fontSize}" 
          text-anchor="middle" 
          fill="white" 
          font-family="Arial, sans-serif" 
          font-size="${fontSize}" 
          font-weight="bold">OV</text>
  ` : ''}
</svg>`;
    
    return iconSvg.trim();
};

// Fonction principale
async function createPWAIcons() {
    console.log('🎨 Creating PWA icons...');
    
    const publicDir = path.join(__dirname, '../public');
    
    // Vérifier que le répertoire public existe
    if (!fs.existsSync(publicDir)) {
        console.error('❌ Public directory not found!');
        return;
    }
    
    let iconsCreated = 0;
    
    // Créer chaque icône
    for (const icon of iconSizes) {
        try {
            const svgContent = createSVGIcon(icon.size);
            const svgPath = path.join(publicDir, icon.filename.replace('.png', '.svg'));
            
            // Créer le fichier SVG temporaire
            fs.writeFileSync(svgPath, svgContent);
            
            // Pour cette demo, on va juste créer les SVG
            // En production, vous voudrez convertir en PNG avec un outil comme sharp
            console.log(`✅ Created ${icon.filename.replace('.png', '.svg')} (${icon.size}x${icon.size})`);
            iconsCreated++;
            
            // Note: Pour convertir en PNG, vous pouvez utiliser:
            // const sharp = require('sharp');
            // await sharp(Buffer.from(svgContent))
            //     .resize(icon.size, icon.size)
            //     .png()
            //     .toFile(path.join(publicDir, icon.filename));
            
        } catch (error) {
            console.error(`❌ Failed to create ${icon.filename}:`, error.message);
        }
    }
    
    // Créer aussi favicon.ico et apple-touch-icon.png comme des SVG
    try {
        const faviconSvg = createSVGIcon(32);
        fs.writeFileSync(path.join(publicDir, 'favicon-pwa.svg'), faviconSvg);
        
        const appleTouchSvg = createSVGIcon(180);
        fs.writeFileSync(path.join(publicDir, 'apple-touch-icon-pwa.svg'), appleTouchSvg);
        
        iconsCreated += 2;
        console.log('✅ Created favicon-pwa.svg and apple-touch-icon-pwa.svg');
    } catch (error) {
        console.error('❌ Failed to create additional icons:', error.message);
    }
    
    console.log(`\n🎉 Successfully created ${iconsCreated} PWA icons!`);
    console.log('\n📝 Next steps:');
    console.log('1. Convert SVG icons to PNG format using a tool like sharp or online converter');
    console.log('2. Replace the generated SVG files with proper PNG files');
    console.log('3. Optimize images for web (compress, WebP format, etc.)');
    console.log('4. Test PWA installation on mobile devices');
    
    console.log('\n🔗 Useful tools:');
    console.log('- PWA Asset Generator: https://www.pwabuilder.com/imageGenerator');
    console.log('- Favicon Generator: https://realfavicongenerator.net/');
    console.log('- Sharp (Node.js): npm install sharp');
}

// Exécuter le script
if (require.main === module) {
    createPWAIcons().catch(console.error);
}

module.exports = { createPWAIcons };
