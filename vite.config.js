import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';

export default defineConfig({
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
    assetsDir: '',
    rollupOptions: {
      input: {
        admin: resolve(__dirname, 'src/admin/admin.js'),
        frontend: resolve(__dirname, 'src/frontend/index.js'),
        frontend_css: resolve(__dirname,'src/frontend/styles/frontend.scss'),
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
          return 'assets/[name]-[hash][extname]';
        }
      }
    }
  }
});