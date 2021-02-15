const { blueGray }   = require( 'tailwindcss/colors' );
const { fontFamily } = require( 'tailwindcss/defaultTheme' );

module.exports = {
    purge:    [
        './resources/views/**/*.blade.php',
        './resources/ts/**/*.ts',
    ],
    darkMode: 'class',
    theme:    {
        extend: {
            colors:     {
                gray: blueGray,
            },
            fontFamily: {
                sans: [
                    '"Open Sans"',
                    ...fontFamily.sans,
                ],
                mono: [
                    '"JetBrains Mono"',
                    ...fontFamily.mono,
                ],
            },
        },
    },
    variants: {
        extend: {},
    },
    plugins:  [],
};
