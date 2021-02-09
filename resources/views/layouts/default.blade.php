<html lang="en" class="dark:bg-gray-900 dark:text-gray-100">
    <head>
        <title>@yield('title') | {{ config('app.name') }}</title>
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Material+Icons&family=JetBrains+Mono:wght@400;700&family=Open+Sans:wght@300;400;600&display=swap"
              rel="stylesheet">
        <link href="{{ asset('vendor/matchory/css/style.css') }}" rel="stylesheet" />
    </head>
    <body class="flex">
        @yield('navigation')

        <main class="flex-grow">
            @yield('header')
            @yield('content')
            @yield('footer')
        </main>
    </body>
</html>
