<section {{ $attributes->merge([ 'class' => 'p-8' ]) }}>
    <x-herodot-endpoint-header :endpoint="$endpoint" />

    <div class="flex flex-col lg:flex-row items-start justify-evenly">
        <x-herodot-endpoint-info :endpoint="$endpoint" class="flex-grow-0 flex-shrink-0 w-full lg:w-1/2" />
        <x-herodot-endpoint-sample :endpoint="$endpoint" class="flex-grow-0 flex-shrink-0 w-full lg:w-1/2" />
    </div>

    {{ $slot }}
</section>
