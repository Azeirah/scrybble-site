<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        @if ($isRmApiAuthenticated)
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h1 class="text-xl font-bold">{{$currentWorkingDirectory}}</h1>
                        <ul>
                            @foreach($ls as $item)
                                <li class="flex items-center">
                                    @if($item['type'] === 'd')
                                        <svg class="inline-block h-4 w-4 text-indigo-600" fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                        </svg>
                                    @else
                                        <svg class="inline-block h-4 w-4 text-indigo-500" fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    @endif
                                    <span class="ml-1"><a href="{{'/dashboard?path=' . $item['path']}}">{{$item['name']}}</a></span></li>
                            @endforeach
                        </ul>
                        @else
                            <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
                                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                    <div class="p-6 bg-white border-b border-gray-200">
                                        <h1 class="text-xl font-bold mb-6">Get started</h1>
                                        <p class="mb-2">Provide your ReMarkable <i>one-time-code</i> below</p>
                                        <a class="underline mb-4 inline-block" target="_blank"
                                           href="https://my.remarkable.com/device/desktop/connect">Get one-time-code</a>
                                        <form class="flex flex-col justify-between" method="POST" action="/onetimecode">
                                            <div class="flex flex-wrap items-stretch w-full mb-4 relative">
                                                @csrf
                                                <input required minlength=8 maxlength=8 pattern="[a-z]{8}"
                                                       class="flex-shrink flex-grow flex-auto leading-normal flex-1 h-10 text-lg tracking-widest bg-indigo-50 rounded-r-none border-r-0 text-center text-slate-900 placeholder-slate-400 rounded-md pl-10 pr-10 text-indigo-900 border focus:border-indigo-600 border-indigo-900"
                                                       placeholder="aabbccdd" name="code" type="text">
                                                <div class="flex -mr-px">
                                                    <input type="submit" value="submit"
                                                           class="hover:cursor-pointer hover:bg-indigo-700 bg-indigo-600 text-indigo-100 flex items-center leading-normal bg-grey-lighter rounded rounded-l-none border text-lg border-l-0 border-indigo-900 px-3 whitespace-no-wrap text-gray-800 text-sm">
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
    </div>
</x-app-layout>
