@extends('backend.app')

@section('title', 'Movement')

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

                    <li class="breadcrumb-item text-muted"> Movement </li>
                    <li class="breadcrumb-item text-muted"> Movement Document </li>

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
                                    <td>{{ $data->id }}</td>
                                </tr>
                                <tr>
                                    <th>Movement</th>
                                    <td>{{ $data->movement?->title }}</td>
                                </tr>
                                <tr>
                                    <th>Author</th>
                                    <td>{{ $data->user?->name }}</td>
                                </tr>
                                <tr>
                                    <th>Document</th>
                                    <td class="align-middle text-center">
                                        @if ($data->file_url)
                                            @php
                                                $isYoutube =
                                                    str_contains($data->file_url, 'youtube.com') ||
                                                    str_contains($data->file_url, 'youtu.be');

                                                if ($isYoutube) {
                                                    preg_match(
                                                        '/(?:v=|youtu\.be\/|embed\/|shorts\/)([a-zA-Z0-9_-]{11})/',
                                                        $data->file_url,
                                                        $matches,
                                                    );
                                                    $videoId = $matches[1] ?? null;
                                                    $thumb = $videoId
                                                        ? "https://img.youtube.com/vi/{$videoId}/mqdefault.jpg"
                                                        : asset('backend/images/video-placeholder.jpg');
                                                } else {
                                                    $thumb = asset('backend/images/video-placeholder.jpg');
                                                }
                                            @endphp

                                            <a href="{{ $data->file_url }}" target="_blank"
                                                class="d-inline-block position-relative">
                                                <img src="{{ $thumb }}" class="rounded shadow-sm"
                                                    style="width: 200px; height: 120px; object-fit: cover;"
                                                    alt="Video preview" loading="lazy">

                                                <div class="position-absolute top-50 start-50 translate-middle">
                                                    <i class="fa-solid fa-play-circle fa-2x text-white opacity-80"></i>
                                                </div>
                                            </a>

                                            <br>
                                            <small class="text-muted">
                                                {{ $isYoutube ? 'YouTube' : 'Video File' }}
                                            </small>
                                        @else
                                            <span class="text-muted small">No video</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Published</th>
                                    <td>{{ $data->created_at }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('admin.movements.index') }}" class="btn btn-info">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
