<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') · Student Information System</title>
    <link rel="stylesheet" href="{{ asset('sis.css') }}">
</head>
<body>
    <main class="shell narrow">
        <section class="panel">
            <h1>@yield('title')</h1>
            <p class="intro">@yield('explanation')</p>
            <a class="button secondary" href="{{ url('/') }}">Go to the student information system</a>
        </section>
    </main>
</body>
</html>
