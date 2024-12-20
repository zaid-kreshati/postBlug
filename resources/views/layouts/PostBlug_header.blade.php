<!DOCTYPE html>
<html lang="en">
<head>

    <!-- Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Default Title')</title>


     <!-- Bootstrap CSS -->

     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

     <!-- Font Awesome -->
     <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

     <!-- Select2 CSS -->
     <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

     <!-- Your Custom CSS -->
     <link rel="stylesheet" href="{{ asset('css/styles.css') }}">

     <!-- Scripts -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

</head>
<body>

    <div class="header" >
            <!-- Logo can be added here -->
            <a href="{{ route('profile.index') }}" >
                <img src="{{ asset('PostBlug/profile-icon.webp') }}" alt="{{ __('Profile') }}" class="icon1" >
                </a>
            </nav>

        <nav class="nav">
            <a href="{{route('searchview')}}" for="searchview"  ">
                <img src="{{ asset('PostBlug/search-icon.jpg') }}" alt="{{ __('Search') }}" class="icon2" >
            </a>
            <a href="{{route('home')}}" for="homePhotoInput" >
                <img src="{{ asset('PostBlug/home-icon.webp') }}" alt="{{ __('Home') }}" class="icon3">
            </a>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                {{ __('Logout') }}
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </nav>
    </div>

    @yield('content')

</body>
</html>
