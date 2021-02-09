module.exports = ( { env } ) => ( {
    map:     { inline: false },
    plugins: {
        'postcss-import': {},
        tailwindcss:      {},
        autoprefixer:     {},

        cssnano: env === 'production'
                 ? { preset: 'default' }
                 : false,
    },
} );
