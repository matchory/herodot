<article class="mb-8">
    <header class="mx-8 pb-4">
        <h2 class="text-3xl leading-relaxed" id="{{ $page->slug() }}">
            <a href="#{{ $page->slug() }}">{{ $page->title() }}</a>
        </h2>
    </header>

    <div class="mb-4 p-8 text-base leading-relaxed">
        <div class="markdown">{!! $content() !!}</div>
    </div>
</article>
