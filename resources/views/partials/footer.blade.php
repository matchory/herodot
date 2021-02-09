<footer class="flex justify-center items-center mt-auto">
    @foreach(config('herodot.blade.footer_links', []) as $title => $link)
        <a href="{{ $link }}" class="mx-1 underline text-xs">{{ $title }}</a>
    @endforeach
</footer>
