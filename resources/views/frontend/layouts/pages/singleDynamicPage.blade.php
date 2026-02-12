@extends('frontend.app')
@section('title', "$dynamicPage->page_title")

@section('content')
    <section>
        <div class="container">
            <div class="row">
                <div class="py-5 my-5 col-12">
                    <div class="card">
                        <p class="mt-4 lead">{!! $dynamicPage->page_content !!}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
