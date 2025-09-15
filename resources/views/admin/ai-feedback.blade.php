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
    <h1>AI Feedback</h1>
    <!-- Add your AI feedback content here -->
</div>
</div>

@endsection

@php
  $searchConfig = [
    'endpoint' => route('admin.ai.search'),
    'suggestionKey' => 'feedback',
    'resultKey' => 'feedback',
  ];
@endphp

<script>
  window.searchConfig = @json($searchConfig);
</script>
@php
  $searchAction = route('admin.ai.search');
  $searchPlaceholder = 'Search AI Feedback...';
@endphp