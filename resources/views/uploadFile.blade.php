@extends('base')

@section('content')
    <form method="post" action="/" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="panel-body ">

            <div class="input-group file-upload-wrapper">
                <label class="input-group-btn">
                                    <span class="btn btn-primary">
                                        Browse… <input required type="file" name="passwordFile" style="display: none;">
                                    </span>
                </label>
                <input type="text" class="form-control upload-text" readonly="">
            </div>
            <button class="btn btn-primary">Upload</button>

        </div>
    </form>
@endsection
