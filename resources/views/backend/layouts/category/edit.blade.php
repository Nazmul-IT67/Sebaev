@extends('backend.app')

@section('title', 'Category Edit')

@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div class=" container-fluid  d-flex flex-stack flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex flex-column align-items-start justify-content-center flex-wrap me-2">
                <!--begin::Title-->
                <h1 class="text-dark fw-bold my-1 fs-2">
                    Dashboard <small class="text-muted fs-6 fw-normal ms-1"></small>
                </h1>
                <!--end::Title-->

                <!--begin::Breadcrumb-->
                <ul class="breadcrumb fw-semibold fs-base my-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">
                            Home </a>
                    </li>

                    <li class="breadcrumb-item text-muted"> Category </li>
                    <li class="breadcrumb-item text-muted"> Category Edit </li>

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
                <div class="card-style mb-4">
                    <div class="card card-body">
                        <form method="POST" action="{{ route('admin.categories.update', $data->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="input-style-1 mb-4">
                                <label for="en_category_name">English Category Name:</label>
                                <input type="text" placeholder="Enter Category name" id="en_category_name"
                                    class="form-control @error('en_category_name') is-invalid @enderror"
                                    name="en_category_name" value="{{ $data->en_category_name ?? old('en_category_name') }}" />

                                <span id="create-category-error" class="text-danger"></span>

                            </div>
                            <div class="input-style-1 mb-4">
                                <label for="sp_category_name">Spanish Category Name:</label>
                                <input type="text" placeholder="Enter Category name" id="sp_category_name"
                                    class="form-control @error('sp_category_name') is-invalid @enderror"
                                    name="sp_category_name" value="{{ $data->sp_category_name ?? old('sp_category_name') }}" />

                                <span id="create-category-error" class="text-danger"></span>

                            </div>

                            <div class="input-style-1 mb-4">
                                <label for="fr_category_name">French Category Name:</label>
                                <input type="text" placeholder="Enter Category name" id="fr_category_name"
                                    class="form-control @error('fr_category_name') is-invalid @enderror"
                                    name="fr_category_name" value="{{ $data->fr_category_name ?? old('fr_category_name') }}" />

                                <span id="create-category-error" class="text-danger"></span>

                            </div>

                            <div class="input-style-1 mb-4">
                                <label for="ca_category_name">Catalan Category Name:</label>
                                <input type="text" placeholder="Enter Category name" id="ca_category_name"
                                    class="form-control @error('ca_category_name') is-invalid @enderror"
                                    name="ca_category_name" value="{{ $data->ca_category_name ?? old('ca_category_name') }}" />

                                <span id="create-category-error" class="text-danger"></span>

                            </div>


                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="{{ route('admin.categories.index') }}" class="btn btn-danger me-2">Cancel</a>
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
