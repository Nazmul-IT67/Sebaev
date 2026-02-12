@extends('backend.app')

@section('title', 'System settings')

@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div class="flex-wrap container-fluid d-flex flex-stack flex-sm-nowrap">
            <!--begin::Info-->
            <div class="flex-wrap d-flex flex-column align-items-start justify-content-center me-2">
                <!--begin::Title-->
                <h1 class="my-1 text-dark fw-bold fs-2">
                    Dashboard <small class="text-muted fs-6 fw-normal ms-1"></small>
                </h1>
                <!--end::Title-->

                <!--begin::Breadcrumb-->
                <ul class="my-1 breadcrumb fw-semibold fs-base">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">
                            Home </a>
                    </li>

                    <li class="breadcrumb-item text-muted"> Setting </li>
                    <li class="breadcrumb-item text-muted"> System Settings </li>

                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Info-->
        </div>
    </div>
    <!--end::Toolbar-->

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="mb-4 card-style">
                    <div class="card card-body">
                        <form method="POST" action="{{ route('system.update') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="mt-4 col-md-6">
                                    <div class="input-style-1">
                                        <label for="logo">Logo:</label>
                                        <input type="file" class="dropify @error('logo') is-invalid @enderror" name="logo"
                                            id="logo"
                                            data-default-file="@isset($setting){{ asset($setting->logo) }}@endisset" />
                                    </div>
                                    @error('logo')
                                        <span class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="mt-4 col-md-6">
                                    <div class="input-style-1">
                                        <label for="favicon">Favicon:</label>
                                        <input type="file" class="dropify @error('favicon') is-invalid @enderror"
                                            name="favicon" id="favicon"
                                            data-default-file="@isset($setting){{ asset($setting->favicon) }}@endisset" />
                                    </div>
                                    @error('favicon')
                                        <span class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="mt-4 col-md-12">
                                    <div class="input-style-1">
                                        <label for="copyright_text">Copy Rights Text:</label>
                                        <input type="text" placeholder="Copy Rights Text" id="copyright_text"
                                            class="form-control @error('copyright_text') is-invalid @enderror"
                                            name="copyright_text" value="{{ $setting->copyright_text ?? '' }}" />
                                        @error('copyright_text')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 col-12">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-danger me-2">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        ClassicEditor
            .create(document.querySelector('#description'))
            .catch(error => {
                console.error(error);
            });
    </script>
@endpush
