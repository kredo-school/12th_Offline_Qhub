@extends('layouts.app')

@section('title', 'Admin: Post-Detail')

@section('content')

    {{-- 投稿表示 + PCメニュー --}}
    <div class="container-fluid">
        @include('posts.components.modals.calendar-modal')

        <div class="row justify-content-center align-items-start mt-3">
            <div class="col-12 col-md-9">
                @include('posts.components.post-card', ['post' => $post])
            </div>
            {{-- <div class="col-md-3 d-none d-md-block ps-md-4 sidebar-sticky">
                @include('posts.components.sidebar-menu')
            </div> --}}
        </div>
    </div>

    {{-- pagination --}}
    {{-- <div class="d-flex justify-content-center my-pagination post-pagination">
        {{ $all_posts->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div> --}}

@endsection
