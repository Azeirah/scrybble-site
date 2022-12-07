<style>
    #landing-page {
        height: calc(100% - 56px);
    }
</style>

<x-app-layout>
    <x-slot name="header">
        <h2>
            {{ __('Roadmap') }}
        </h2>
    </x-slot>
    @section('content')
        <div id="root"></div>
{{--        <div id="landing-page" class="container d-flex align-items-center justify-content-center text-center">--}}
{{--            <main>--}}
{{--                <h1>ReMarkable to Obsidian sync</h1>--}}
{{--                <p class="lead">This is the interface for the RM to Obsidian tool.</p>--}}
{{--                <p class="lead">--}}
{{--                    <a href="https://streamsoft.gumroad.com/l/remarkable-to-obsidian"--}}
{{--                       class="btn btn-lg btn-secondary fw-bold border-white bg-white">Learn more</a>--}}
{{--                    <a href="{{url('register')}}" class="btn btn-lg btn-outline-secondary fw-bold ">I have a license</a>--}}
{{--                </p>--}}
{{--            </main>--}}
{{--        </div>--}}
        <x-footer></x-footer>
    @endsection
</x-app-layout>
