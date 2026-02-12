@extends('backend.app')

@section('title', 'Manage Permission')

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

                    <li class="breadcrumb-item text-muted"> Permission </li>
                    <li class="breadcrumb-item text-muted"> Manage </li>

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
                <div class="card p-5">
                    <div class="card-style mb-30">
                        <div class="d-flex justify-content-end mb-3">
                            <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary" type="button">Manage</a>
                        </div>
                        <div class="table-wrapper table-responsive">
                            <table id="data-table" class="table">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Role Name</th>
                                        <th>Permissions</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($roles as $role)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ Str::title($role->name) }}</td>
                                            <td>
                                                @php
                                                    $colors = [
                                                        'bg-primary',
                                                        'bg-secondary',
                                                        'bg-success',
                                                        'bg-danger',
                                                        'bg-warning',
                                                        'bg-info',
                                                        'bg-dark',
                                                    ];
                                                @endphp

                                                @foreach ($role->permissions as $permission)
                                                    <span class="badge {{ $colors[$loop->index % count($colors)] }} me-1">
                                                        {{ $permission->name }}
                                                    </span>
                                                @endforeach

                                            </td>
                                            <td>{{ $role->created_at->format('d M Y') }}</td>
                                            <td>
                                                <a href="{{ route('admin.permissions.edit', $role->id) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="showDeleteConfirm({{ $role->id }})">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        // delete Confirm
        function showDeleteConfirm(id) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure you want to delete this record?',
                text: 'If you delete this, it will be gone forever.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteItem(id);
                }
            });
        }
        function deleteItem(id) {
            let url = "{{ route('admin.permissions.destroy', ':id') }}".replace(':id', id);
            $.ajax({
                type: "DELETE",
                url: url,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(resp) {
                    toastr.success(resp.message, 'Deleted', {
                        timeOut: 7000
                    });

                    $('button[onclick="showDeleteConfirm(' + id + ')"]').closest('tr').fadeOut(500, function() {
                        $(this).remove();
                    });
                }
            });
        }
    </script>
@endpush
