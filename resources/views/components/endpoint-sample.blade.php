<?php
/* @var Matchory\Herodot\Entities\Endpoint $endpoint */

?>
<div {{ $attributes->merge([ 'class' => 'sticky top-16 flex-grow-0 flex-shrink-0 w-1/2' ]) }}>
    <pre class="ml-4 p-4 rounded-lg bg-gray-800 text-gray-50">
        <code class="">    code sample here</code>
        <code>    code sample here</code>
        <code>    code sample here</code>
    </pre>

    {{ $slot }}
</div>
