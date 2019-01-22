@extends('base')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/result.css') }}">
@endpush

@section('content')
    <div class="content-wrapper">
        @include('uploadFile')
        <table class="table table-hover result-table">
            <thead>
            <tr>
                <th class="site-name">Site Name</th>
                <th class="username">Username</th>
                <th class="exploits">Number of exploits</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr @if($item->showWarning()) class="danger" @endif>
                    <td class="site-name">{{$item->getSiteName()}}</td>
                    <td class="username">{{$item->getUsername()}}</td>
                    <td class="exploits">{{$item->getExploited()}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
