@push('css')
    <link rel="stylesheet" href="{{ asset('css/fileUpload.css') }}">
@endpush

@push('js')
    <script src="{{ asset('js/fileUpload.js') }}"></script>
@endpush

<div class="file-upload-wrapper">
    <form method="post" action="/" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="panel-body ">
            <div class="input-group">
                <label class="input-group-btn">
                                    <span class="btn btn-default">
                                        Browse Password File <input required type="file" name="passwordFile" style="display: none;">
                                    </span>
                </label>
                <input type="text" class="form-control upload-text" readonly="">
            </div>
            <button class="btn btn-default" id="upload-btn">Upload</button>

        </div>
    </form>
    @if($error)
        <div class="alert alert-danger">
            <strong>{{$error}}</strong>
        </div>
    @endif
</div>
