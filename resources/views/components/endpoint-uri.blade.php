<?php
/* @var Matchory\Herodot\Entities\Endpoint $endpoint */

?>
<code {{ $attributes->merge([ 'class' => 'flex items-center w-1/2 p-4 text-sm rounded bg-gray-100 dark:bg-gray-800' ]) }}>
    <span class="py-1 px-2 rounded bg-blue-500 text-gray-100">{{ $methods() }}</span>
    <span class="flex items-center ml-3 text-gray-700 dark:text-gray-300">
        @foreach ($segments() as $segment)
            <span>/</span>
            @if ($segmentIsParam($segment))
                <span class="bg-blue-100 dark:bg-blue-900 dark:bg-opacity-50 p-0.5 rounded font-bold">{{ $segment }}</span>
            @else
                <span>{{ $segment }}</span>
            @endif
        @endforeach
    </span>

    {{ $slot }}
</code>
