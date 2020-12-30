module.exports = {
    title:       'Herodot',
    base:        '/herodot/',
    description: 'A versatile documentation generator for APIs built with Laravel.',
    themeConfig: {
        editLinkText: 'Edit this page on GitHub',
        lastUpdated:  'Last Updated',

        sidebar: {
            '/guide/': getGuideSidebar( 'Guide', 'Advanced' ),
        },
        nav:     [
            { text: 'Guide', link: '/guide/' },
            { text: 'Configuration', link: '/configuration' },
            { text: 'Extensions', link: '/guide/extensions' },
            { text: 'Github', link: 'https://github.com/matchory/herodot' },
        ],
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
                ['strategies', 'Documenting your API']
            ],
        },
        {
            title:       groupB,
            collapsable: false,
            children:    [
                ['extensions', 'Extending Herodot'],
                ['custom-strategies', 'Custom Strategies']
            ],
        },
    ];
}
