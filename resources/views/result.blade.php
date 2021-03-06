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
                <th class="password-strength">Password Strength</th>
                <th class="duplicates">Number of duplicates</th>
                <th class="exploits">Number of exploits</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr class="{{$item->warningClass()}}">
                    <td class="site-name">{{$item->getSiteName()}}</td>
                    <td class="username">{{$item->getUsername()}}</td>
                    <td class="password-strength {{$item->getPasswordStrengthClass()}}">
                        <b>{{$item->getPasswordStrength()}}</b></td>
                    <td class="duplicates">{{$item->getNumberOfDuplicates()}}</td>
                    <td class="exploits">{{$item->getExploited()}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
