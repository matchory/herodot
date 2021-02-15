import { applyTheme, cycleTheme } from '@/modules/theme-toggle';
import '../css/style.pcss';

window.document.addEventListener(
    'DOMContentLoaded',
    () => init( window ),
);

/**
 * Initializes the page modules.
 *
 * @param global
 */
function init( global: Window ): void {
    setupTheming();
    setupSearch();

    function setupTheming(): void {
        const prefersDarkScheme = global.matchMedia(
            '(prefers-color-scheme: dark)',
        );

        const themeToggle = global.document.querySelector(
            '[data-theme-toggle]',
        );

        // Apply the theme if the system color scheme preference changes
        prefersDarkScheme.addEventListener(
            'change',
            (): void => applyTheme(
                global.document.documentElement,
                prefersDarkScheme,
            ),
        );

        // Cycle, then apply the theme if the theme toggle is clicked
        if ( themeToggle ) {
            themeToggle.addEventListener( 'click', (): void => {
                cycleTheme();
                applyTheme(
                    global.document.documentElement,
                    prefersDarkScheme,
                );
            } );
        }
    }

    function setupSearch(): void {
        const isMacOs = /(Mac|iPhone|iPod|iPad)/i.test(
            global.navigator.platform,
        );

        if ( isMacOs ) {
            global.document.documentElement.classList.add( 'macos' );
        }

        const searchForm: HTMLInputElement | null = document.querySelector(
            '[data-search-form]',
        );

        if ( !searchForm ) {
            return;
        }

        // Open the search box on pressing CMD+K/CTRL+K
        global.addEventListener(
            'keydown',
            ( {
                  metaKey,
                  ctrlKey,
                  key,
              } ) => key === 'k' && (
                ( isMacOs && metaKey ) ||
                ( !isMacOs && ctrlKey )
            )
                     ? openSearch( searchForm )
                     : null,
        );

        // Close the search form on pressing Escape while it's focused
        searchForm.addEventListener(
            'keydown',
            ( { key } ) => key === 'Escape'
                           ? closeSearch( searchForm )
                           : null,
        );

        function openSearch( element: HTMLInputElement ): void {
            element.focus();
        }

        function closeSearch( element: HTMLInputElement ): void {
            element.blur();
        }
    }
}

