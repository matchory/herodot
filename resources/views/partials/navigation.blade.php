<nav class="sticky flex flex-col top-0 w-1/5 max-w-xs h-full flex-shrink-0 border-r border-gray-200 dark:border-gray-800 dark:bg-gray-900">
    <header class="flex justify-between items-center leading-8 px-4 py-3">
        <h1 class="text-2xl font-bold truncate max-w-full">
            {{ config('herodot.blade.title', config('app.name')) }}
        </h1>

        <button class="p-2 rounded-full focus:outline-none bg-gray-100 hover:bg-gray-200 focus:ring dark:focus:ring-blue-500 text-gray-500 dark:bg-gray-600 dark:hover:bg-gray-700 dark:text-gray-100"
                data-theme-toggle>
            <span class="material-icons theme-toggle theme-toggle--light">light_mode</span>
            <span class="material-icons theme-toggle theme-toggle--dark">dark_mode</span>
            <span class="material-icons theme-toggle theme-toggle--auto">brightness_auto</span>
        </button>
    </header>

    <div class="relative overflow-y-auto p-4">
        <ul>
            @foreach ($pages->keys() as $title)
                <li class="@if(!$loop->first) mt-4 @endif">
                    <a href="#{{ Str::slug($title) }}" class="hover:text-blue-500">
                        {{ $title }}
                    </a>
                </li>
            @endforeach
        </ul>

        <ul>
            @foreach($groups as $group => $endpoints)
                <li class="@if(!$loop->first) mt-4 @endif">
                    <a href="#{{ Str::slug($group) }}" class="hover:text-blue-500">
                        {{ $group }}
                    </a>

                    <ul>
                        @foreach($endpoints as $endpoint)
                            <li>
                                <a href="#{{ Str::slug($group . ' ' . $endpoint->getTitle()) }}"
                                   class="text-gray-600 dark:text-gray-400 hover:text-blue-500 text-sm">
                                    {{ rtrim($endpoint->getTitle(), '.') }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>
    </div>
</nav>
