<section {{ $attributes->merge([ 'class' => 'p-8' ]) }}>
    <x-herodot-endpoint-header :endpoint="$endpoint" />

    <div class="flex items-start justify-evenly">
        <x-herodot-endpoint-info :endpoint="$endpoint" />
        <x-herodot-endpoint-sample :endpoint="$endpoint" />
    </div>

    {{ $slot }}
</section>
