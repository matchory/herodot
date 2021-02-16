<article class="mb-8">
    <header class="mx-8 pb-4">
        <h2 class="text-3xl leading-relaxed" id="{{ $slug() }}">
            <a href="#{{ $slug() }}">{{ $title() }}</a>
        </h2>
    </header>

    <div class="mb-4 p-8 text-base leading-relaxed">
        <div class="markdown">{!! trim($content()) !!}</div>
    </div>
</article>
