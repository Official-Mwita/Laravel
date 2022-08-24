<!DOCKTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width" , initial-scale="1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        @auth
            <title>{{ auth()->user()->name }}</title>
        @endauth
        @guest
            <title>toPnoPch</title>
        @endguest

        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    </head>

    <body class="bg-light">
        <nav class="navbar navbar-white bg-white mb-4">
            @auth
                <a class="navbar-brand"><span class="text-capitalize">{{ auth()->user()->username }}</span></a>
                <form action="{{ route('admin_logout') }}" method="post" class="form-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-success my-2 my-sm-0">Logout</button>
                </form>
            @endauth
            @guest
                <a class="navbar-brand" href="/">Home</a>
                <a class="nav-link" href=" {{ route('admin_login') }}">Login</a>
            @endguest

            <form class="form-inline">
                <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
            </form>
        </nav>
        @yield('content')
    </body>

    </html>
