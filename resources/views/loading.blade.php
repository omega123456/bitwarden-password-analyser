@push('css')
    <link rel="stylesheet" href="{{ asset('css/loading.css') }}">
@endpush

@push('js')
    <script src="{{ asset('js/loading.js') }}"></script>
    <script>
        const shouldCheckFile = {{($isProcessing ?? false) ? 'true' : 'false'}};
    </script>
@endpush

<div class="screen-overlay">
    <div class="loader"></div>
</div>

