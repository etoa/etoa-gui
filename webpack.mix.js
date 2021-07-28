let mix = require('laravel-mix');

mix
  .js('resources/js/admin.js', 'js')
  .sass('resources/sass/admin.scss', 'css')
  .setPublicPath('htdocs/web')
  .options({
    processCssUrls: false
  })
  .webpackConfig({
    resolve: {
      alias: {
        'jquery-ui': 'jquery-ui-bundle/jquery-ui.js'
      }
    }
  })
  .disableSuccessNotifications();
