import { defineConfig } from 'vite';

export default defineConfig({
  root: 'assets',
  build: {
    outDir: '../public/build',
    emptyOutDir: true,
    rollupOptions: {
      input: {
        main: './public/assets/script.js',
        style: './public/assets/style.css'
      }
    }
  }
});