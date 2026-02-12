@extends('backend.app')

@section('title', 'Product Create')

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

                    <li class="breadcrumb-item text-muted"> SubCategory </li>
                    <li class="breadcrumb-item text-muted"> SubCategory Create </li>

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
                        <form method="POST" action="{{ route('admin.subcategory.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="input-style-1 mt-4">
                                <label for="en_subcategory_name">English Subcategory Name:</label>
                                <input type="text" placeholder="Enter Name" id="en_subcategory_name"
                                    class="form-control @error('en_subcategory_name') is-invalid @enderror" name="en_subcategory_name"
                                    value="{{ old('en_subcategory_name') }}" />
                                @error('en_subcategory_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="input-style-1 mt-4">
                                <label for="sp_subcategory_name">Spanish Subcategory Name:</label>
                                <input type="text" placeholder="Enter Name" id="sp_subcategory_name"
                                    class="form-control @error('sp_subcategory_name') is-invalid @enderror" name="sp_subcategory_name"
                                    value="{{ old('sp_subcategory_name') }}" />
                                @error('sp_subcategory_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="input-style-1 mt-4">
                                <label for="fr_subcategory_name">French Subcategory Name:</label>
                                <input type="text" placeholder="Enter Name" id="fr_subcategory_name"
                                    class="form-control @error('fr_subcategory_name') is-invalid @enderror" name="fr_subcategory_name"
                                    value="{{ old('fr_subcategory_name') }}" />
                                @error('fr_subcategory_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="input-style-1 mt-4">
                                <label for="ca_subcategory_name">Catalan Subcategory Name:</label>
                                <input type="text" placeholder="Enter Name" id="ca_subcategory_name"
                                    class="form-control @error('ca_subcategory_name') is-invalid @enderror" name="ca_subcategory_name"
                                    value="{{ old('ca_subcategory_name') }}" />
                                @error('ca_subcategory_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="input-style-1 mt-4">
                                <label for="blog_category_id">Category:</label>
                                <select name="category_id"
                                    class="form-control dropdown @error('category_id') is-invalid @enderror"
                                    id="category_id">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->en_category_name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>



                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ route('admin.subcategory.index') }}" class="btn btn-danger me-2">Cancel</a>
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
