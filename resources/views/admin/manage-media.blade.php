@extends('layouts.master')
@section('content')
<div class="container">
    <div class="ms-2 mt-4">
  @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif
    @if (session('success'))
    <div class="alert alert-success mt-2">
        {{ session('success') }}
    </div>
@endif
        <h1 >Manage Media</h1>
        <!-- Add your manage media content here -->
    </div>
</div>
@endsection

@php
  $searchConfig = [
    'endpoint' => route('admin.media.search'),
    'suggestionKey' => 'media',
    'resultKey' => 'media',
  ];
@endphp

<script>
  window.searchConfig = @json($searchConfig);
</script>

@php
  $searchAction = route('admin.media.search');
  $searchPlaceholder = 'Search Media...';
@endphp