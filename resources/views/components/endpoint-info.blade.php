<?php
/* @var Matchory\Herodot\Entities\Endpoint $endpoint */

?>
<div {{ $attributes->merge([ 'class' => 'flex-grow-0 flex-shrink-0 w-1/2' ]) }}>
    <div class="text-base leading-relaxed mb-4">
        @if ($endpoint->getDescription())
            <div class="markdown">{!! trim($description()) !!}</div>
        @else
            <span class="block py-4 text-gray-400">No description available</span>
        @endif
    </div>

    @if (count($endpoint->getUrlParams()))
        <section class="mb-4">
            <header class="py-2">
                <h4 class="text-lg font-medium">URI Parameters</h4>
            </header>

            <ul>

                @foreach($endpoint->getUrlParams() as $param)
                    <li class="py-2 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center text-xs">
                            <code class="text-sm">
                                {{ $param->getName() }}
                            </code>

                            @unless($param->getTypeDefinition()->isAny())
                                <x-herodot-badges:type :parameter="$param" class="ml-2" />
                            @endunless

                            @if ($param->isRequired())
                                <x-herodot-badges:required :parameter="$param" class="ml-2" />
                            @else
                                <x-herodot-badges:optional :parameter="$param" class="ml-2" />
                            @endif

                            @if ($param->isDeprecated())
                                <x-herodot-badges:deprecated :parameter="$param" class="ml-2" />
                            @endif
                        </div>

                        <div class="mt-2 text-sm">
                            @if ($param->getDescription())
                                <p class="text-gray-700">{{ $param->getDescription() }}</p>
                            @else
                                <span class="text-gray-400 dark:text-gray-400">No description available</span>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    @if (count($endpoint->getQueryParams()))
        <section class="mb-4">
            <header class="py-2">
                <h4 class="text-lg font-medium">Query Parameters</h4>
            </header>

            <ul>
                @foreach($endpoint->getQueryParams() as $param)
                    <li class="py-2 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center text-xs">
                            <code class="text-sm">
                                {{ $param->getName() }}
                            </code>

                            @unless($param->getTypeDefinition()->isAny())
                                <x-herodot-badges:type :parameter="$param" class="ml-2" />
                            @endunless

                            @if ($param->isRequired())
                                <x-herodot-badges:required :parameter="$param" class="ml-2" />
                            @else
                                <x-herodot-badges:optional :parameter="$param" class="ml-2" />
                            @endif

                            @if ($param->isDeprecated())
                                <x-herodot-badges:deprecated :parameter="$param" class="ml-2" />
                            @endif
                        </div>

                        <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">{{ $param->getDescription() }}</p>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    @if (count($endpoint->getBodyParams()))
        <section class="mb-4">
            <header class="py-2">
                <h4 class="text-lg font-medium">Body Parameters</h4>
            </header>

            <ul>
                @foreach($endpoint->getBodyParams() as $param)
                    <li class="py-2 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center text-xs">
                            <code class="text-sm">
                                {{ $param->getName() }}
                            </code>

                            @unless($param->getTypeDefinition()->isAny())
                                <x-herodot-badges:type :parameter="$param" class="ml-2" />
                            @endunless

                            @if ($param->isRequired())
                                <x-herodot-badges:required :parameter="$param" class="ml-2" />
                            @else
                                <x-herodot-badges:optional :parameter="$param" class="ml-2" />
                            @endif

                            @if ($param->isDeprecated())
                                <x-herodot-badges:deprecated :parameter="$param" class="ml-2" />
                            @endif
                        </div>

                        <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">{{ $param->getDescription() }}</p>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    {{ $slot }}
</div>
