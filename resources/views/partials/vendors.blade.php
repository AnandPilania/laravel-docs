@extends('docs::master')
@section('title', 'Docs')
@section('content')
    <div class="lists">
        <ul>
            @if(count($vendors) > 0)
                @foreach($vendors['dirs'] as $dir)
                    <li><a href="{{ $dir }}"><div></div><span>{{ $dir }}</span></a></li>
                @endforeach
                @foreach($vendors['files'] as $file)
                    <li><a href="{{ $file }}"><div></div><span>{{ $file }}</span></a></li>
                @endforeach
            @else
                <li>No vendors found!!</li>
            @endif
        </ul>
    </div>
@endsection