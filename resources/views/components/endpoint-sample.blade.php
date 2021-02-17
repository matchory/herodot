<?php
/* @var Matchory\Herodot\Entities\Endpoint $endpoint */

?>
<div {{ $attributes->merge([ 'class' => 'sticky top-16' ]) }}>
    <div class="lg:ml-4 px-4 py-2 rounded-lg bg-gray-800">
        <header class="flex items-center mb-4 text-gray-400">
            <span class="uppercase text-xs mr-auto">Request</span>

            <label class="mr-2">
                <select class="bg-transparent text-xs">
                    <option disabled selected>Language</option>
                    <option>PHP</option>
                    <option>Go</option>
                    <option>JavaScript</option>
                    <option>Python</option>
                    <option>curl</option>
                </select>
            </label>

            <button>
                <span class="sr-only">Copy to clipboard</span>
                <span class="material-icons text-base">content_copy</span>
            </button>
        </header>

        <pre class="text-gray-100 py-2" data-sample-js>fetch()</pre>
    </div>

    {{ $slot }}
</div>
