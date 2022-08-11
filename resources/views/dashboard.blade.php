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
            @if($gumroadLicenseValid)
                @if ($isRmApiAuthenticated)
                    <x-remarkable-tree :currentWorkingDirectory="$currentWorkingDirectory" :ls="$ls"/>
                @else
                    <x-one-time-code/>
                @endif
            @else
                <div class="card" style="width: 32rem">
                    <div class="card-body">
                        <h1>Account set-up (step 1/2)</h1>
                        <div>
                            <h2>Connect your gumroad license</h2>
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="list-unstyled" style="margin-bottom: 0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form method="POST" action="/connect-license">
                                @csrf
                                <div class="input-group">
                                    <input class="form-control input-group-text" required
                                           name="license" type="text" placeholder="Your license" value="{{old('license')}}">
                                    <button class="btn btn-primary" type="submit">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endsection


</x-app-layout>
