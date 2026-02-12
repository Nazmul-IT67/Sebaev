@extends('backend.app')

@section('title', 'Edit Role')

@section('content')

    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div class="container-fluid d-flex flex-stack flex-wrap flex-sm-nowrap">
            <div class="d-flex flex-column align-items-start justify-content-center flex-wrap me-2">
                <h1 class="text-dark fw-bold my-1 fs-2">Edit Role</h1>

                <ul class="breadcrumb fw-semibold fs-base my-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item text-muted">Roles</li>
                    <li class="breadcrumb-item text-muted">Edit</li>
                </ul>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card p-5">
                    <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
                        @csrf
                        <!-- Role Name -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                Role Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $role->name) }}"
                                class="form-control @error('name') is-invalid @enderror">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-start gap-2">
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-danger">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
