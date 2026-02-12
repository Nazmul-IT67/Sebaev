@extends('backend.app')

@section('title', 'User Report')

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

                    <li class="breadcrumb-item text-muted"> Report </li>
                    <li class="breadcrumb-item text-muted"> Report Details </li>

                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Info-->
        </div>
    </div>
    <!--end::Toolbar-->

    <div class="mt-4 container-fluid">
        <div class="border-0 shadow-lg card">
            <div class="text-white card-header bg-dark d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white">🧾 Report Details</h5>
                <span class="badge bg-secondary">Reporter: {{ $report->user->name }}</span>
            </div>

            <div class="card-body row g-4">
                <div class="col-lg-12">
                    {{-- report message show --}}
                    <div class="alert alert-info">
                        <strong>Report Reason:</strong> {{ $report->reason }}
                    </div>
                </div>
                {{-- Movement Section --}}
                @if ($report->movement)
                    <div class="col-md-6">
                        <div class="shadow-sm card h-100 border-info">
                            <div class="card-header bg-info d-flex justify-content-between align-items-center">
                                <h5 class="mt-4 text-white">Movement</h5>
                                <form action="{{ route('admin.report.destroy_movement', $report->movement->id) }}"
                                    method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete Movement</button>
                                </form>
                            </div>
                            <div class="card-body">
                                <div class="p-5 mb-3 rounded shadow-sm border-info">

                                    <h5 class="card-title">{{ $report->movement->title ?? 'N/A' }}</h5>

                                    <div class="mb-3">
                                        <p class="text-muted">
                                            {{ $report->movement->description ?? 'No description available.' }}</p>
                                    </div>

                                    @if ($report->movement->video)
                                        <div style="height: 300px; overflow: hidden;">
                                            <video controls style="height: 100%; width: 100%; object-fit: cover;">
                                                <source src="{{ asset($report->movement->video) }}" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                    @endif
                                </div>




                                <hr>
                                <div class="mt-5 border-0 shadow-sm card bg-light">
                                    <div class="card-header bg-info-subtle d-flex justify-content-between align-items-center"
                                        style="min-height: 20px;">
                                        <h5 class="mt-4 text-dark">Movement Creator</h5>
                                    </div>
                                    <div class="card-body d-flex justify-content-between">
                                        <div>
                                            <strong>Name:</strong> {{ $report->movement->user->name ?? 'N/A' }}
                                        </div>
                                        <div class="text-end">
                                            <strong>Email:</strong> {{ $report->movement->user->email ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                @endif

                {{-- Post Section --}}
                @if ($report->post)
                    <div class="col-md-6">
                        <div class="shadow-sm card h-100 border-primary">
                            <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                                <h5 class="mt-4 text-white">Post</h5>

                                <form action="{{ route('admin.report.destroy_post', $report->post->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete Post</button>
                                </form>
                            </div>
                            <div class="card-body">
                                <div class="p-5 mb-3 rounded shadow-sm border-info">

                                    <h5 class="card-title">{{ $report->post->title ?? 'N/A' }}</h5>

                                    <div class="mb-3">
                                        <p class="text-muted">
                                            {{ $report->post->description ?? 'No description available.' }}</p>
                                    </div>

                                    @if ($report->post->video)
                                        <div style="height: 300px; overflow: hidden;">
                                            <video controls style="height: 100%; width: 100%; object-fit: cover;">
                                                <source src="{{ $report->post->video }}" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                    @endif
                                </div>

                                <hr>
                                <div class="mt-5 border-0 shadow-sm card bg-light">
                                    <div class="card-header bg-primary-subtle d-flex justify-content-between align-items-center"
                                        style="min-height: 20px;">
                                        <h5 class="mt-4 text-dark">Post Creator</h5>
                                    </div>
                                    <div class="card-body d-flex justify-content-between">
                                        <div>
                                            <strong>Name:</strong> {{ $report->post->user->name ?? 'N/A' }}
                                        </div>
                                        <div class="text-end">
                                            <strong>Email:</strong> {{ $report->post->user->email ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection



@push('script')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                }
            });
            if (!$.fn.DataTable.isDataTable('#data-table')) {
                let dTable = $('#data-table').DataTable({
                    order: [],
                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "All"]
                    ],
                    processing: true,
                    responsive: true,
                    serverSide: true,

                    language: {
                        processing: `<div class="text-center">
                            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                            <span class="visually-hidden">Loading...</span>
                          </div>
                            </div>`
                    },

                    scroller: {
                        loadingIndicator: false
                    },
                    pagingType: "full_numbers",
                    dom: "<'row justify-content-between table-topbar'<'col-md-2 col-sm-4 px-0'l><'col-md-2 col-sm-4 px-0'f>>tipr",
                    ajax: {
                        url: "{{ route('admin.report.index') }}",
                        type: "get",
                    },

                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'user_name',
                            name: 'user_name',
                            orderable: true,
                            searchable: true
                        },
                        {
                            data: 'email',
                            name: 'email',
                            orderable: true,
                            searchable: true
                        },
                        {
                            data: 'post_name',
                            name: 'post_name',
                            orderable: true,
                            searchable: true
                        },
                        {
                            data: 'reason',
                            name: 'reason',
                            orderable: true,
                            searchable: true
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ],
                });

                dTable.buttons().container().appendTo('#file_exports');
                new DataTable('#example', {
                    responsive: true
                });
            }
        });

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
        // Delete Button
        function deleteItem(id) {
            let url = "{{ route('admin.report.destroy', ':id') }}";
            let csrfToken = '{{ csrf_token() }}';
            $.ajax({
                type: "DELETE",
                url: url.replace(':id', id),
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(resp) {
                    console.log(resp);
                    // Reloade DataTable
                    $('#data-table').DataTable().ajax.reload();
                    if (resp.success === true) {
                        // show toast message
                        toastr.success(resp.message);

                    } else if (resp.errors) {
                        toastr.error(resp.errors[0]);
                    } else {
                        toastr.error(resp.message);
                    }
                },
                error: function(error) {
                    // location.reload();
                }
            })
        }
    </script>
@endpush
