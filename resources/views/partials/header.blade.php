<header class="sticky top-0 bg-white dark:bg-gray-900 backdrop-blur border-b border-gray-200 dark:border-gray-800 z-50">
    <div class="container mx-auto py-4 px-8">
        <nav class="flex justify-end items-center">

            @foreach(config('herodot.blade.header_links', []) as $label => $link)
                <a href="{{ $link }}" class="ml-4 capitalize hover:underline text-blue-500 leading-6">{{ $label }}</a>
            @endforeach

            {{-- Required for empty header link spacing --}}
            &nbsp;
        </nav>
    </div>
</header>
