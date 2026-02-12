@extends('backend.app')

@section('title', 'Edit Role Permissions')

@section('content')

<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
    <div class="container-fluid d-flex flex-stack flex-wrap flex-sm-nowrap">
        <div class="d-flex flex-column align-items-start justify-content-center flex-wrap me-2">
            <h1 class="text-dark fw-bold my-1 fs-2">Edit Role Permissions</h1>

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
                <form action="{{ route('admin.permissions.update', $role->id) }}" method="POST">
                    @csrf                    
                    <!-- Role Name (disabled) -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Role Name</label>
                        <select class="form-control" disabled>
                            <option>{{ $role->name }}</option>
                        </select>
                    </div>

                    <!-- Permissions -->
                    @forelse ($permissions as $groupName => $groupPermissions)
                        <div class="mb-4 p-3 border rounded">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong class="text-capitalize">{{ $groupName }} Permissions</strong>
                                <!-- Select All -->
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input select-all"
                                        data-group="{{ $groupName }}" id="selectAll{{ $groupName }}">
                                    <label class="form-check-label" for="selectAll{{ $groupName }}">Select All</label>
                                </div>
                            </div>

                            <hr>

                            <div class="row g-2">
                                @foreach ($groupPermissions as $permission)
                                    <div class="col-md-3">
                                        <div class="form-check d-flex align-items-center">
                                            <input type="checkbox" class="form-check-input permission-switch me-2"
                                                name="permissions[]" value="{{ $permission->id }}"
                                                data-group="{{ $groupName }}" id="permission{{ $permission->id }}"
                                                {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                            <label class="form-check-label text-capitalize"
                                                for="permission{{ $permission->id }}">
                                                {{ Str::replace('_', ' ', $permission->name) }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">No permissions found.</p>
                    @endforelse

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-start gap-2">
                        <a href="{{ route('admin.permissions.index') }}" class="btn btn-danger">
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
@push('script')
<script>
    $('.select-all').on('change', function() {
        var group = $(this).data('group');
        $('input.permission-switch[data-group="' + group + '"]').prop('checked', $(this).is(':checked'));
    });
</script>
@endpush
