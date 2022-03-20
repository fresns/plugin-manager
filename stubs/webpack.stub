const dotenvExpand = require('dotenv-expand');
dotenvExpand(require('dotenv').config({ path: '../../../../.env'/*, debug: true*/}));

const mix = require('laravel-mix');
require('laravel-mix-merge-manifest');

mix.setPublicPath('../../public').mergeManifest();

mix.js(__dirname + '/Resources/assets/js/app.js', 'plugins/$KEBAB_NAME$/js/app.js')
    .sass( __dirname + '/Resources/assets/sass/app.scss','plugins/$KEBAB_NAME$/css/app.css');

if (mix.inProduction()) {
    mix.version();
}
