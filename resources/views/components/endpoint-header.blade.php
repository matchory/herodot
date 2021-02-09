<header {{ $attributes->merge([ 'class' => 'mb-2' ]) }}>
    <x-herodot-endpoint-title :endpoint="$endpoint" class="herodot-endpoint__endpoint-title" />
    <x-herodot-endpoint-uri :endpoint="$endpoint" class="herodot-endpoint__endpoint-uri" />

    {{ $slot }}
</header>
