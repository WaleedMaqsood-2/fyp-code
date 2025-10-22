<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>@yield('title', 'Police Dashboard')</title>
  <link href="{{ asset('css/police/main.css') }}" rel="stylesheet">
 @stack('styles')
 
</head>

<body>
@php
            $user =Auth::user();
          @endphp
          @if (!$user)
            @php
                header('Location: ' . route('login'));
                exit;
            @endphp
            @endif
         
  <!-- Header & Sidebar -->
  @include('police.layouts.header')
  @include('police.layouts.sidebar')

  <!-- Page Content -->
  <main>
    @yield('content')
  </main>

 @include('police.layouts.footer')

  @include('police.layouts.scripts')

</body>
</html>
