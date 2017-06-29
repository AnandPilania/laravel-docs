@extends('docs::master')
@section('title', $title)
@section('content')
    <div class="lists">
        <ul>
            @if(count($versions) > 0)
                @foreach($versions['dirs'] as $dir)
                    <li><a href="{{ $dir }}"><div></div><span>{{ $dir }}</span></a></li>
                @endforeach
                @foreach($versions['files'] as $file)
                    <li><a href="{{ $file }}"><div class="file"></div><span>{{ $file }}</span></a></li>
                @endforeach
            @else
                <li>No versions found for {{ $title }}!!</li>
            @endif
        </ul>
    </div>
    @endsection