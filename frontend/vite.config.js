import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    vue(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },
  server: {
    //https: true,
    proxy: {
      '/api': {
        secure: false,
        target: 'http://127.0.0.1:8000',
        changeOrigin: false,
        //rewrite: (path) => path.replace(/^\/api/, ''),
      },
    },
  },
  build: {
    outDir: '../backend/public/',
    manifest: true,
    rollupOptions: {
      // overwrite default .html entry
      input: 'src/main.js',
    },
  }
})
