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
    <h1>AI Usage</h1>
    <!-- Add your AI usage content here -->
</div>
</div>
@endsection


@php
  $searchAction = route('admin.ai.search');
  $searchPlaceholder = 'Search AI Usage...';
@endphp