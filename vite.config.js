import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';

export default defineConfig({
  base: '',
  plugins: [
    react({
      jsxRuntime: 'classic',
      babel: {
        presets: ['@babel/preset-react']
      }
    })
  ],
  build: {
    outDir: 'assets',
    assetsDir: 'assets',
    rollupOptions: {
      input: {
        admin: resolve(__dirname, 'src/admin/admin.js'),
        frontend: resolve(__dirname, 'src/frontend/index.js'),
        main: resolve(__dirname,'src/frontend/styles/main.scss'),
        index_css: resolve(__dirname,'src/index.scss')
      },
      output: {
        format: 'es',
        entryFileNames: 'js/[name].js',
        chunkFileNames: 'js/[name]-[hash].js',
        assetFileNames: ({name}) => {
          if (/\.(css|scss)$/.test(name ?? '')) {
            return 'css/[name][extname]';
          }
          if (/\.(png|jpe?g|gif|svg|webp)$/.test(name ?? '')) {
            return 'images/[name][extname]';
          }
          return 'assets/[name]-[hash][extname]';
        }
      }
    }
  }
});