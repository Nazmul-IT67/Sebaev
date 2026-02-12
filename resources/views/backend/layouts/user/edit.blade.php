@extends('backend.app')

@section('title', 'Edit User')

@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div class=" container-fluid  d-flex flex-stack flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex flex-column align-items-start justify-content-center flex-wrap me-2">
                <!--begin::Title-->
                <h1 class="text-dark fw-bold mb-2 my-1 fs-2">
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
                    <li class="breadcrumb-item text-muted"> Edit User </li>

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
                <form method="POST" action="{{ route('admin.users.update') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-style">
                        <div class="card card-body">
                            <div class="col-md-12">
                                <div class="input-style-1 mb-4">
                                    <label class="form-label fw-bold mb-2">Profile Picture</label>
                                    <div class="profile-info">
                                        <div class="d-flex align-items-center">
                                            <div class="profile-image position-relative">
                                                <img src="{{ $user->avatar && file_exists(public_path($user->avatar))
                                                    ? asset($user->avatar)
                                                    : asset('backend/images/profile.jpeg') }}"
                                                    class="rounded-circle" width="100" height="100"
                                                    alt="Profile Picture">
                                                <div class="update-image position-absolute" style="bottom:0; right:0;">
                                                    <input type="file" name="avatar" id="profile_picture_input"
                                                        style="display: none;">
                                                    <label for="profile_picture_input"
                                                        class="btn btn-sm btn-primary m-0 p-1">
                                                        <i class="lni lni-cloud-upload"></i>
                                                    </label>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    @error('avatar')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="card-style mt-8">
                            <div class="card card-body">
                                <div class="row">
                                    <input type="hidden" name="id" value="{{ $user->id }}">
                                    <div class="col-md-12">
                                        <div class="input-style-1 mb-4">
                                            <label class="form-label fw-bold mb-2">Name</label>
                                            <input type="text" name="name" placeholder="Enter full name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                value="{{ old('name', $user->name) }}">
                                            @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="input-style-1 mb-4">
                                            <label class="form-label fw-bold mb-2">Email</label>
                                            <input type="email" name="email" placeholder="Enter email address"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email', $user->email) }}">
                                            @error('email')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="input-style-1 mb-4">
                                            <label class="form-label fw-bold mb-2">Password</label>
                                            <input type="password" name="password" placeholder="Enter password"
                                                class="form-control @error('password') is-invalid @enderror">
                                            @error('password')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-style mt-8">
                                <div class="card card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-style-1 mb-4">
                                                <label class="form-label fw-bold mb-2">Date of Birth</label>
                                                <input type="date" id="date_of_birth" name="date_of_birth"
                                                    class="form-control @error('date_of_birth') is-invalid @enderror"
                                                    value="{{ old('date_of_birth', $user->date_of_birth) }}">
                                                @error('date_of_birth')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="input-style-1 mb-4">
                                                <label class="form-label fw-bold mb-2">Gender</label>
                                                <select name="gender"
                                                    class="form-control @error('gender') is-invalid @enderror">
                                                    <option value="">Select Gender</option>
                                                    <option value="male"
                                                        {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>
                                                        Male
                                                    </option>
                                                    <option value="female"
                                                        {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>
                                                        Female
                                                    </option>
                                                    <option value="other"
                                                        {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>
                                                        Other
                                                    </option>
                                                </select>
                                                @error('gender')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-style-1 mb-4">
                                                    <label class="form-label fw-bold mb-2">Company VAT ID (Optional)</label>
                                                    <input type="text" id="company_vat_id" name="company_vat_id"
                                                        class="form-control @error('company_vat_id') is-invalid @enderror"
                                                        value="{{ old('company_vat_id', $user->company_vat_id) }}"
                                                        placeholder="Enter company VAT ID">
                                                    @error('company_vat_id')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="input-style-1 mb-4">
                                                    <label class="form-label fw-bold mb-2">Company Website
                                                        (Optional)</label>
                                                    <input type="url" id="company_website" name="company_website"
                                                        class="form-control @error('company_website') is-invalid @enderror"
                                                        value="{{ old('company_website', $user->company_website) }}"
                                                        placeholder="Enter company website">
                                                    @error('company_website')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-style-1 mb-4">
                                                    <label class="form-label fw-bold mb-2">User Type</label>
                                                    <select name="user_type"
                                                        class="form-control @error('user_type') is-invalid @enderror">
                                                        <option value="">Select User Type</option>
                                                        <option value="individual"
                                                            {{ old('user_type', $user->user_type) == 'individual' ? 'selected' : '' }}>
                                                            Individual</option>
                                                        <option value="company"
                                                            {{ old('user_type', $user->user_type) == 'company' ? 'selected' : '' }}>
                                                            Company</option>
                                                    </select>
                                                    @error('user_type')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="input-style-1 mb-4">
                                                    <label class="form-label fw-bold mb-2">Country</label>
                                                    <select name="country_id"
                                                        class="form-control @error('country_id') is-invalid @enderror">
                                                        <option value="">Select Country</option>
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country->id }}"
                                                                {{ old('country_id', $user->country_id) == $country->id ? 'selected' : '' }}>
                                                                {{ $country->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('country_id')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="input-style-1 mb-4">
                                                    <label class="form-label fw-bold mb-2">Role</label>
                                                    <select name="role_id"
                                                        class="form-control @error('role_id') is-invalid @enderror">
                                                        <option value="">Select Role</option>
                                                        @foreach ($roles as $role)
                                                            <option value="{{ $role->id }}"
                                                                {{ old('role_id', $role->id) == $role->id ? 'selected' : '' }}>
                                                                {{ Str::title(str_replace('_', ' ', $role->name)) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('role_id')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-style mt-8">
                                    <div class="card card-body">
                                        <div class="input-style-1 mb-4">
                                            <label class="form-label fw-bold mb-2">User Interests</label>
                                            <div class="row">
                                                @php
                                                    $selectedSubCategories = old(
                                                        'sub_category_ids',
                                                        isset($user)
                                                            ? $user->subCategories->pluck('id')->toArray()
                                                            : [],
                                                    );
                                                @endphp
                                                @foreach ($interests as $interest)
                                                    <div class="col-md-3">
                                                        <div class="form-check m-1">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="sub_category_ids[]" value="{{ $interest->id }}"
                                                                id="interest{{ $interest->id }}"
                                                                {{ in_array($interest->id, $selectedSubCategories) ? 'checked' : '' }}>

                                                            <label class="form-check-label"
                                                                for="interest{{ $interest->id }}">
                                                                {{ ucfirst($interest->en_subcategory_name) }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @error('sub_category_ids')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-check mb-4 mt-8">
                                            <input type="checkbox" name="agree_to_terms" value="1"
                                                class="form-check-input @error('agree_to_terms') is-invalid @enderror"
                                                {{ old('agree_to_terms', $user->agree_to_terms ?? false) ? 'checked' : '' }}>
                                            <label class="form-label fw-bold mb-2">
                                                Agree to terms
                                            </label>

                                            @error('agree_to_terms')
                                                <span class="text-danger d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary">Update User</button>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-danger">Cancel</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        const input = document.getElementById('profile_picture_input');
        const img = document.querySelector('.profile-image img');
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
@endpush
