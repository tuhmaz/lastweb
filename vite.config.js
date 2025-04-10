import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import html from '@rollup/plugin-html';
import { glob } from 'glob';
import path from 'path';
import viteCompression from 'vite-plugin-compression';

/**
 * Get Files from a directory
 * @param {string} query
 * @returns array
 */
function GetFilesArray(query) {
  return Array.from(new Set(glob.sync(query))); // Remove duplicate files by using Set initially
}
/**
 * Js Files
 */
// Page JS Files
const pageJsFiles = GetFilesArray('resources/assets/js/*.js');

// Processing Vendor JS Files
const vendorJsFiles = GetFilesArray('resources/assets/vendor/js/*.js');

// Processing Libs JS Files
const LibsJsFiles = GetFilesArray('resources/assets/vendor/libs/**/*.js');

/**
 * Scss Files
 */
// Processing Core, Themes & Pages Scss Files
const CoreScssFiles = GetFilesArray('resources/assets/vendor/scss/**/!(_)*.scss');
const RtlScssFiles = GetFilesArray('resources/assets/vendor/scss/rtl/**/!(_)*.scss');

// Processing Libs Scss & Css Files
const LibsScssFiles = GetFilesArray('resources/assets/vendor/libs/**/!(_)*.scss');
const LibsCssFiles = GetFilesArray('resources/assets/vendor/libs/**/*.css');

// Processing Fonts Scss Files
const FontsScssFiles = GetFilesArray('resources/assets/vendor/fonts/**/!(_)*.scss');

// Add cookie consent CSS
const CookieConsentCss = GetFilesArray('resources/css/cookie-consent.css');

// Processing Window Assignment for Libs like jKanban, pdfMake
function libsWindowAssignment() {
  return {
    name: 'libsWindowAssignment',

    transform(src, id) {
      if (id.includes('jkanban.js')) {
        return src.replace('this.jKanban', 'window.jKanban');
      } else if (id.includes('vfs_fonts')) {
        return src.replaceAll('this.pdfMake', 'window.pdfMake');
      }
    }
  };
}

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/css/app.css',
        'resources/assets/css/alemedu.css',
        'resources/js/app.js',
        'resources/js/sitemap-manager.js',
        'resources/js/articles.js',
        'resources/js/school-classes.js',
        'resources/css/cookie-consent.css',
        'resources/js/subjects.js',
        'resources/js/articles-form.js',
        'resources/assets/css/pages/notifications.css',
        'resources/assets/css/articles/articles-list-style.css',
        'resources/assets/js/articles/articles-management.js',
        'resources/assets/css/articles/article-details.css',
        'resources/assets/js/articles/article-details.js',
        'resources/assets/js/pages/monitoring.js',
        'resources/assets/css/pages/security.css',
        'resources/assets/js/pages/security/trusted-ips.js',
        'resources/assets/js/pages/security.js',
        'resources/assets/js/pages/security/blocked-ips.js',
        'resources/css/footer-front.css',
        'resources/assets/css/pages/articles.css',
        'resources/assets/css/dashboard.css',
        'resources/js/articles-show.js',
        'resources/assets/js/pages/dashboard.js',
        'resources/assets/css/pages/school-classes.css',
        'resources/assets/js/pages/performance.js',
        'resources/assets/css/pages/performance.css',
        'resources/assets/js/pages/performance-metrics.js',
        'resources/assets/js/pages/news.js',
        'resources/js/pages/news.js',
        'resources/css/pages/dashboard.css',
        'resources/assets/css/pages/about-us.css',
        'resources/assets/js/pages/userlist.js',
        'resources/scss/base/pages/news.scss',
        ...pageJsFiles,
        ...vendorJsFiles,
        ...LibsJsFiles,
        'resources/js/laravel-user-management.js',
        ...CoreScssFiles,
        ...RtlScssFiles,
        ...LibsScssFiles,
        ...LibsCssFiles,
        ...FontsScssFiles,

      ],
      refresh: true
    }),
    libsWindowAssignment(),
    viteCompression()
  ],
  resolve: {
    alias: {
      '~': path.resolve(__dirname, 'node_modules'),
      '@': path.resolve(__dirname, 'resources')
    }
  }
});
