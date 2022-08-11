<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Scrybble</title>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        /* Custom default button */
        .btn-secondary,
        .btn-secondary:hover,
        .btn-secondary:focus {
            color: #333;
            text-shadow: none; /* Prevent inheritance from `body` */
        }


        /*
         * Base structure
         */
        body {
            text-shadow: 0 .05rem .1rem rgba(0, 0, 0, .5);
            box-shadow: inset 0 0 5rem rgba(0, 0, 0, .5);
        }

        .cover-container {
            max-width: 42em;
        }


        /*
         * Header
         */

        .nav-masthead .nav-link {
            color: rgba(255, 255, 255, .5);
            border-bottom: .25rem solid transparent;
        }

        .nav-masthead .nav-link:hover,
        .nav-masthead .nav-link:focus {
            border-bottom-color: rgba(255, 255, 255, .25);
        }

        .nav-masthead .nav-link + .nav-link {
            margin-left: 1rem;
        }

        .nav-masthead .active {
            color: #fff;
            border-bottom-color: #fff;
        }
    </style>

    {{--    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">--}}
</head>
<body class="d-flex h-100 text-center text-bg-dark">

<div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
    <header class="mb-auto">
        <div>
            <h3 class="float-md-start mb-0">Scrybble</h3>
            <nav class="nav nav-masthead justify-content-center float-md-end">
                <a class="nav-link fw-bold py-1 px-0 active" aria-current="page" href="#">Home</a>
                <a class="nav-link fw-bold py-1 px-0" href="#">Features</a>
                <a class="nav-link fw-bold py-1 px-0" href="#">Contact</a>
            </nav>
        </div>
    </header>

    <main class="px-3">
        <h1>ReMarkable to Obsidian sync</h1>
        <p class="lead">This is the interface for the RM to Obsidian tool.</p>
        <p class="lead">
            <a href="https://streamsoft.gumroad.com/l/remarkable-to-obsidian" class="btn btn-lg btn-secondary fw-bold border-white bg-white">Learn more</a>
            <a href="{{url('register')}}" class="btn btn-lg btn-outline-secondary fw-bold ">I have a license</a>
        </p>
    </main>

    <x-footer/>
</div>

{{--<div>--}}
{{--    @if (Route::has('login'))--}}
{{--        <div>--}}
{{--            @auth--}}
{{--                <a href="{{ url('/home') }}">Home</a>--}}
{{--            @else--}}
{{--                <a href="{{ route('login') }}">Log in</a>--}}

{{--                @if (Route::has('register'))--}}
{{--                    <a href="{{ route('register') }}">Register</a>--}}
{{--                @endif--}}
{{--            @endauth--}}
{{--        </div>--}}
{{--    @endif--}}

{{--    <h1>ReMarkable to Obsidian</h1>--}}
{{--</div>--}}
</body>
</html>
