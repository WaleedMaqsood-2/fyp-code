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
        <h1>Analytics</h1>
    <!-- Add your analytics content here -->
</div>
</div>
@endsection


@php
  $searchAction = route('admin.analytics.search');
  $searchPlaceholder = 'Search Analytics...';
@endphp