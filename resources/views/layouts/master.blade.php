<!DOCTYPE html>
<html lang="en">
<head>
  @include('layouts.head')
</head>
<body>

    @include('layouts.sidebar')

      @include('layouts.navbar')

        @yield('content')
    
    
        @include('layouts.scripts')
      @include('layouts.footer')
@yield('scripts')

</body>
</html>
