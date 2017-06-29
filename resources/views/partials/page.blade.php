@extends('docs::master')
@section('title', $title)
@section('content')

    <div class="index">
        @if(isset($index))
            @if(is_array($index))
                @if(count($index) > 0)
                    @foreach($index as $item)
                        {!! $item !!}
                    @endforeach
                @endif
            @else
                {!! $index !!}
            @endif
        @endif
    </div>

    <div class="page">
        {!! $page !!}
    </div>

    @endsection