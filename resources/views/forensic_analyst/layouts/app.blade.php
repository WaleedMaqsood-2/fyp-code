<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>@yield('title', 'Forensic Analyst Dashboard')</title>

       <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Fonts & Custom CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="{{ asset('css/forensic_analyst/app.css') }}" rel="stylesheet">
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
    @include('forensic_analyst.layouts.header')

        @include('forensic_analyst.layouts.sidebar')

        <main class="content ">
            @yield('content')
        </main>
        @include('forensic_analyst.layouts.footer')


    @include('forensic_analyst.layouts.scripts')
    @stack('scripts')
    
</body>
</html>
