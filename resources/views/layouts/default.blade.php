<html lang="en">
    <head>
        <title>@yield('title') | {{ config('app.name') }}</title>
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Material+Icons&family=JetBrains+Mono:wght@400;700&family=Open+Sans:wght@300;400;600&display=swap"
              rel="stylesheet">
        <link href="{{ asset('vendor/matchory/main.min.css') }}" rel="stylesheet" />
        <script>
            if ( localStorage.theme === 'dark' ) {
                document.documentElement.classList.add( 'dark' );
            } else if ( localStorage.theme === 'light' ) {
                document.documentElement.classList.add( 'light' );
            } else if ( !( 'theme' in localStorage ) ) {
                if ( window.matchMedia( '(prefers-color-scheme:dark)' ).matches ) {
                    document.documentElement.classList.add( 'dark', 'auto' );
                } else {
                    document.documentElement.classList.add( 'light', 'auto' );
                }
            }
        </script>
    </head>
    <body class="flex dark:bg-gray-900 dark:text-gray-100">
        @yield('navigation')

        <main class="flex-grow">
            @yield('header')
            @yield('content')
            @yield('footer')
        </main>

        <script src="{{ asset('vendor/matchory/bundle.min.js') }}"></script>
    </body>
</html>
