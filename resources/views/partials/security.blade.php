@extends('docs::master')
@section('title', 'Security violations!')
@section('content')

    @if(isset($parameters))
    You didn't has sufficient @if(isset($parameters->roles)) -roles- @endif @if(isset($parameters->permissions)) -permissions- @endif to access this page.
    <ul>
        @foreach($parameters as $parameter => $value)
        <li>{{ $parameter }} :
        @if(is_array($value))
            @foreach($value as $item)
                {{ $item }}
                @endforeach
            @else
            {{ $value }}
            @endif
            @endforeach
    </ul>
        @else
    You didn't has sufficient roles or permissions to access this page!
        @endif
    @endsection