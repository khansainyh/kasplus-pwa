import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
            transformAssetUrls: {
                // Ini akan memastikan URL font di CSS diproses dengan benar
                fonts: true,
                // Kamu juga bisa menambahkan base atau includeAbsolute jika ada masalah lain
                // base: null, // Defaultnya '/' yang sudah benar untuk public
                // includeAbsolute: false, // Atau true, tergantung kebutuhan, tapi jarang untuk font
            },            
        }),
    ],
        // Ini juga bisa membantu memastikan publicDir eksplisit, meskipun defaultnya sudah 'public'
    publicDir: 'public',
});
