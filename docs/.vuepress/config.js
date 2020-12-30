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
            apiKey:    'c1977f79b897ef9bd4a2ea6600cb7916',
            indexName: 'herodot_docs',
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
