const mix = require('laravel-mix');
require('laravel-mix-tailwind');
require('laravel-mix-purgecss');

// noinspection JSUnusedGlobalSymbols
mix.copy('resources/icons', 'public/icons')
    .copy('resources/manifest.json', 'public')
    .js('resources/js/app.js', 'public/js')
    .js('app/CRM/resources/js/app.js', 'public/js/crm.js')
    .js('app/Deluge/resources/js/app.js', 'public/js/deluge.js')
    .js('app/Gamble/resources/js/app.js', 'public/js/gamble.js')
    .postCss('resources/css/app.css', 'public/css', [
        require('postcss-import'),
        require('tailwindcss'),
        require('postcss-nested'),
    ])
    .tailwind('./tailwind.config.js')
    .purgeCss({
        extensions: ['js', 'vue', 'css', 'blade.php'],
        folders: ['resources', 'app/CRM/resources', 'app/Deluge/resources', 'app/Gamble/resources'],
        defaultExtractor: content => content.match(/[\w-/.:]+(?<!:)/g) || [],
    })

    .webpackConfig({
        watchOptions: {
            ignored: /node_modules/,
        },
        output: {
            chunkFilename: 'js/[name].[chunkhash].js',
        },
    })
    .sourceMaps();

if (mix.inProduction()) {
    mix.version();
}
