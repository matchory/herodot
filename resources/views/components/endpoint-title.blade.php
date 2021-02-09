<?php
/* @var Matchory\Herodot\Entities\Endpoint $endpoint */ ?>
<h3 {{ $attributes->merge([ 'class' => 'mb-2 text-xl font-medium leading-loose' ]) }}
    id="{{ Str::slug($endpoint->getGroup() . ' ' . $endpoint->getTitle()) }}">

    <a href="#{{ Str::slug($endpoint->getGroup() . ' ' . $endpoint->getTitle()) }}">
        {{ rtrim($endpoint->getTitle(), '.') }}
    </a>

    {{ $slot }}
</h3>
