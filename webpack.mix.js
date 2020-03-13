let mix = require('laravel-mix');

mix.sass('app/stylesheet/virtuagora.scss', 'public/assets/css')
    .js('app/components/virtuagora.js', 'public/assets/js')
    .extract(['vue'])
    .setPublicPath('public/')
    .webpackConfig({ devtool: "inline-source-map" });