<nav class="sticky flex flex-col top-0 w-1/5 max-w-xs h-full flex-shrink-0 border-r border-gray-200 dark:border-gray-800 dark:bg-gray-900">
    <header class="flex justify-between items-center leading-8 px-4 py-3">
        <h1 class="text-2xl font-bold truncate max-w-full">
            {{ config('herodot.blade.title', config('app.name')) }}
        </h1>

        <!--
        <button>
            <span class="material-icons">light_mode</span>
            <span class="material-icons">dark_mode</span>
        </button>
        -->
    </header>

    <div class="relative overflow-y-auto p-4">
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
