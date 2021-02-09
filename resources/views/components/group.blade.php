<section class="herodot-endpoint-groups__endpoint-group">
    @if ($name)
        <header class="mx-8 pb-4">
            <h2 class="text-3xl leading-relaxed" id="{{ Str::slug($name) }}">
                <a href="#{{ Str::slug($name) }}">{{ $name }}</a>
            </h2>
        </header>
    @endif

    @foreach($endpoints as $endpoint)
        <x-herodot-endpoint :endpoint="$endpoint" class="herodot-endpoint mb-8" />

        @unless($loop->last)
            <hr class="mx-8 mb-6 border-t-2 border-gray-100 dark:border-gray-800">
        @endunless
    @endforeach
</section>
