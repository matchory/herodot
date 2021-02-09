@extends('herodot::layouts.default')

@section('title', 'API Documentation')

@section('navigation')
    @parent
    @include('herodot::partials.navigation')
@endsection

@section('header')
    @include('herodot::partials.header')
@endsection

@section('content')
    <article class="container mx-auto py-8">
        @foreach ($groups as $name => $endpoints)
            <x-herodot-group :name="$name" :endpoints="$endpoints"></x-herodot-group>
        @endforeach
    </article>
@endsection

@section('footer')
    @include('herodot::partials.footer')
@endsection
