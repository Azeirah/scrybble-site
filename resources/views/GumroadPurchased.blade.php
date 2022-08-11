<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Purchased') }}
        </h2>
    </x-slot>

    @section('content')
        <div class="container">
            <h1>Thank you for your purchase, you can now start using Scrybble.</h1>
            <p>Get started by creating an account</p>
            <a class="btn btn-primary" href="{{route('register')}}">Sign up</a>
        </div>
    @endsection
</x-app-layout>
