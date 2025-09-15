<!DOCTYPE html>
<html lang="en">
<head>
  @include('layouts.head')
  @stack('styles' )
</head>
<body>

    @include('layouts.sidebar')

      @include('layouts.navbar')

@yield('content')


@include('layouts.footer')
@include('layouts.scripts')
@yield('scripts')

</body>
</html>
