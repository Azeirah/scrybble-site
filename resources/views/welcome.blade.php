<x-app-layout>
    <x-slot name="header">
        <h2>
            {{ __('Roadmap') }}
        </h2>
    </x-slot>
    @section('content')
        <div id="root" class="h-100"></div>
    @endsection
</x-app-layout>
