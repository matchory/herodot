module.exports = {
    title:       'Herodot',
    base:        '/herodot/',
    description: 'A versatile documentation generator for APIs built with Laravel.',
    themeConfig: {
        repo:         'matchory/herodot',
        docsDir:      'docs',
        docsBranch:   'main',
        editLinks:    true,
        editLinkText: 'Edit this page on GitHub',
        lastUpdated:  'Last Updated',

        sidebar: {
            '/guide/': getGuideSidebar( 'Guide', 'Advanced' ),
        },
        nav:     [
            { text: 'Guide', link: '/guide/' },
            { text: 'Configuration', link: '/configuration' },
            { text: 'Extensions', link: '/guide/extensions' },
        ],

        smoothScroll: true,

        searchPlaceholder: 'Search...',
        algolia:           {
            apiKey:    'bdc970b323ae8878bdf507cdf58545d0',
            indexName: 'herodot',
        },
    },
};

function getGuideSidebar( groupA, groupB ) {
    return [
        {
            title:       groupA,
            collapsable: false,
            children:    [
                [ '', 'Introduction' ],
                [ 'installation', 'Installation' ],
                [ 'usage', 'Usage' ],
                [ 'strategies', 'Documenting your API' ],
                [ 'types', 'Types' ],
                [ 'generating-docs', 'Generating Documentation' ],
            ],
        },
        {
            title:       groupB,
            collapsable: false,
            children:    [
                [ 'extensions', 'Extending Herodot' ],
                [ 'custom-strategies', 'Custom Strategies' ],
                [ 'custom-printers', 'Custom Printers' ],
            ],
        },
    ];
}
