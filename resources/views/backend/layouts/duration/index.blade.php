@extends('backend.app')
@section('title', 'Update Duration , Size & Donation Amount')
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
                        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary"> Home </a>
                    </li>

                    <li class="breadcrumb-item text-muted"> Update Duration , Size & Donation Amount </li>

                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Info-->
        </div>
    </div>
    <!--end::Toolbar-->

    <section>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-body">
                        <h1 class="mb-4">Update Duration , Size & Donation Amount</h1>
                        <form action="{{ route('admin.duration.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mt-4">
                                <label for="duration" class="form-label">Duration (in seconds)</label>
                                <input type="text" name="duration" id="duration" class="form-control @error('duration') is-invalid @enderror" value="{{ $data->duration ?? old('duration') }}" >
                                @error('duration')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mt-4">
                                <label for="size" class="form-label">Size (in Kilobytes)</label>
                                <input type="text" name="size" id="size" class="form-control @error('size') is-invalid @enderror" value="{{ $data->size ?? old('size') }}" >
                                @error('size')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mt-4">
                                <label for="donation_amount" class="form-label">Donation Amount</label>
                                <input type="text" name="donation_amount" id="donation_amount" class="form-control @error('donation_amount') is-invalid @enderror" value="{{ $data->donation_amount ?? old('donation_amount') }}" >
                                @error('donation_amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mt-4">
                                <input type="submit" class="btn btn-primary btn-lg" value="Submit">
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-danger btn-lg">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
