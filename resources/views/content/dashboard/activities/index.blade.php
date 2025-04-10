@php
use Illuminate\Support\Str;
@endphp

@extends('layouts/contentNavbarLayout')

@section('title', __('Recent Activities'))

@section('content')
<div class="container-fluid p-0">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">{{ __('Recent Activities') }}</h5>
        </div>
        <div class="card-body">
            <div id="activities-container">
                @include('content.dashboard.activities._list')
            </div>

            <div id="activities-loader" class="text-center d-none">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
let page = 1;
let loading = false;
let hasMore = true;

const loadMoreActivities = () => {
    if (loading || !hasMore) return;

    loading = true;
    page++;

    const loader = document.getElementById('activities-loader');
    loader.classList.remove('d-none');

    fetch(`/dashboard/activities/load-more?page=${page}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('activities-container');
            container.insertAdjacentHTML('beforeend', data.html);
            hasMore = data.hasMore;
            loading = false;
            loader.classList.add('d-none');
        })
        .catch(error => {
            console.error('Error loading more activities:', error);
            loading = false;
            loader.classList.add('d-none');
        });
};

// Detect when user scrolls near bottom
window.addEventListener('scroll', () => {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 1000) {
        loadMoreActivities();
    }
});
</script>
@endsection
