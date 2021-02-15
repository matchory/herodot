// @ts-nocheck
const { resolve }          = require( 'path' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const isProduction         = process.env.NODE_ENV === 'production';

module.exports = {

    // Set mode conditionally depending on the NODE_ENV webpack runs in
    mode: isProduction ? 'production' : 'development',

    // Make sure webpack operates in the resources directory
    context: resolve( __dirname, 'resources' ),

    // Start with the Typescript index file - look here for the main CSS import
    entry: './ts/index.ts',

    // Write the output to the public directory, which is where we want to
    // deploy our assets so the production variants are bundled with the
    // composer package.
    output: {
        path:     resolve( __dirname, 'public' ),
        filename: isProduction
                  ? 'bundle.min.js'
                  : 'bundle.js',
    },

    // Source maps in "production" help developers
    devtool: isProduction ? 'source-map' : 'inline-source-map',

    // Make sure to resolve TS and JS files
    resolve: {
        extensions: [ '.ts', '.js' ],

        alias: {
            '@': resolve( __dirname, 'resources', 'ts' ),
        },
    },

    module: {
        rules: [

            // Setup the Typescript loader
            {
                test:    /\.ts$/,
                use:     'ts-loader',
                exclude: /node_modules/,
            },

            // Setup the PostCSS loader with a complex loader chain:
            {
                test:    /\.(p?css)|(postcss)$/i,
                exclude: /node_modules/,
                use:     [

                    // 1. Make sure CSS will be written to its own output file
                    MiniCssExtractPlugin.loader,

                    // 2. Handle stylesheets as CSS
                    'css-loader',

                    // 3. Let PostCSS transform everything to ordinary CSS
                    'postcss-loader',
                ],
            },
        ],
    },

    devServer: {

        // Let the dev server serve the assets
        contentBase: resolve( __dirname, 'public' ),
    },

    plugins: [
        new MiniCssExtractPlugin( {

            // We definitely want a ".min" suffix on the filename in production
            filename: isProduction
                      ? '[name].min.css'
                      : '[name].css',
        } ),
    ],
};
