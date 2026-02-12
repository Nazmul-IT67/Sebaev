@extends('backend.app')

@section('title', $user->name)

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

                    <li class="breadcrumb-item text-muted"> User </li>
                    <li class="breadcrumb-item text-muted"> {{ $user->name }} </li>

                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Info-->
        </div>
    </div>
    <!--end::Toolbar-->

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 mx-auto">
                <div class="card p-5">
                    <h3 class="mb-4">User Information</h3>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>ID</th>
                                    <td>{{ $user->id }}</td>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>Password</th>
                                    <td>{{ $user->password }}</td>
                                </tr>
                                <tr>
                                    <th>Date Of Birth</th>
                                    <td>{{ $user->date_of_birth }}</td>
                                </tr>
                                <tr>
                                    <th>Gender</th>
                                    <td>{{ Str::title(str_replace('_', ' ', $user->gender)) }}</td>
                                </tr>
                                <tr>
                                    <th>User Type</th>
                                    <td>{{ Str::title(str_replace('_', ' ', $user->user_type)) }}</td>
                                </tr>
                                <tr>
                                    <th>Country</th>
                                    <td>{{ $user->country->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Company_vat ID</th>
                                    <td>{{ $user->company_vat_id ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Company Website</th>
                                    <td>{{ $user->company_website ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td><span class="badge badge-lg bg-warning">{{ Str::title(str_replace('_', ' ', $user->status)) }}</span></td>
                                </tr>
                                <tr>
                                    <th>Registration Date</th>
                                    <td>{{ $user->created_at ? $user->created_at->format('d M Y, h:i A') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>User Interests</th>
                                    <td>
                                        @foreach ($user->subCategories as $interest)
                                            <span class="badge bg-info">{{ $interest->en_subcategory_name }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-info">Back</a>
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary">Edit</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
