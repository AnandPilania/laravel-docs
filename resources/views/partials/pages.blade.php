@extends('docs::master')
@section('title')
    {{ $title . ' : ' . $versionOrPage }}
@section('content')
    <div class="lists">
        <ul>
            @if(count($pages) > 0)
                @foreach($pages as $page)
                    <li><a href="{{ $title . '/' . $versionOrPage . '/' . $page }}"><div></div><span>{{ $page }}</span></a></li>
                @endforeach
            @else
                <li>No pages found!!</li>
            @endif
        </ul>
    </div>
    @endsection