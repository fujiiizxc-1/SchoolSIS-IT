<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') · Student Information System</title>
    <link rel="stylesheet" href="{{ asset('sis.css') }}">
</head>
<body>
    <a class="skip" href="#main">Skip to main content</a>
    <header class="site-header">
        <div class="shell header-inner">
            <a class="brand" href="{{ auth()->check() ? route('dashboard') : route('login') }}">Student Information System</a>
            @auth
                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button class="secondary" type="submit">Log out</button>
                </form>
            @else
                <nav aria-label="Account" class="nav">
                    <a href="{{ route('login') }}" data-auth-toggle="login" @if (request()->routeIs('login')) aria-current="page" @endif>Log in</a>
                    <a href="{{ route('register') }}" data-auth-toggle="register" @if (request()->routeIs('register')) aria-current="page" @endif>Register</a>
                </nav>
            @endauth
        </div>
    </header>
    <div class="shell app-frame @guest public-frame @endguest">
        @auth
            @include('components.role-nav')
        @endauth
        <main class="workspace @guest public-main @endguest @yield('main-class')" id="main" tabindex="-1">
            @if (session('status'))
                <p class="notice" role="status">{{ session('status') }}</p>
            @endif
            @if (session('error'))
                <p class="notice notice-error" role="alert">{{ session('error') }}</p>
            @endif
            @if ($errors->any())
                <div class="notice notice-error" role="alert">
                    <strong>Check the form and try again.</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</body>
</html>
