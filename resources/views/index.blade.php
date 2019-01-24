@extends('base')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endpush

@push('js')
    <script src="{{ asset('js/index.js') }}"></script>
    <script>
        const shouldCheckFile = {{($isProcessing ?? false) ? 'true' : 'false'}};
    </script>
@endpush

@section('content')
    <h1>Upload the json file you exported from Bitwarden</h1>
    @include('uploadFile')
    @includeWhen($isProcessing, 'loading')
@endsection
