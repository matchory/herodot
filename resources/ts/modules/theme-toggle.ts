export const autoTheme  = 'auto';
export const darkTheme  = 'dark';
export const lightTheme = 'light';

/**
 * Applies the currently active theme to the given element.
 *
 * @param element       Element to apply the theme to.
 * @param mediaQuery    Media Query to check if no theme is active.
 */
export function applyTheme(
    element: HTMLElement,
    mediaQuery: MediaQueryList,
): void {
    const theme = getTheme();

    switch ( theme ) {

        // Dark theme is active: Make it explicit.
        case darkTheme:
            element.classList.add( darkTheme );
            element.classList.remove( lightTheme, autoTheme );

            break;

        // Light theme is active: Make it explicit.
        case lightTheme:
            element.classList.add( lightTheme );
            element.classList.remove( darkTheme, autoTheme );
            break;

        // No theme is active: Let the media query decide.
        default:
            if ( mediaQuery.matches ) {
                element.classList.remove( lightTheme );
                element.classList.add( darkTheme, autoTheme );
            } else {
                element.classList.remove( darkTheme );
                element.classList.add( lightTheme, autoTheme );
            }
    }
}

/**
 * Reads the theme from the storage.
 */
export function getTheme(): string | null {
    const { theme } = localStorage;

    return theme || null;
}

/**
 * Sets the theme to the storage.
 *
 * @param theme
 */
export function setTheme( theme: string | null ): void {
    switch ( theme ) {
        case darkTheme:
        case lightTheme:
            localStorage.setItem( 'theme', theme );
            break;

        default:
            localStorage.removeItem( 'theme' );
    }
}

/**
 * Cycles the theme depending on the current theme.
 */
export function cycleTheme(): void {
    const theme = getTheme();

    switch ( theme ) {
        case darkTheme:
            return setTheme( lightTheme );

        case lightTheme:
            return setTheme( null );

        default:
            return setTheme( darkTheme );
    }
}
