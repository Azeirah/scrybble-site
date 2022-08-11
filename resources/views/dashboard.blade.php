<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @section('content')
        <style>
            a {
                text-decoration: none;
                font-weight: bold;
            }
        </style>
        <div class="container">
            @if ($isRmApiAuthenticated)
                <x-remarkable-tree :currentWorkingDirectory="$currentWorkingDirectory" :ls="$ls"/>
            @else
                <x-one-time-code/>
            @endif
        </div>
    @endsection


</x-app-layout>
