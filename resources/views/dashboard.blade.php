<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if ($isRmApiAuthenticated)
                        You are authenticated
                    @else
                        <h1 class="text-xl font-bold mb-6">Get started</h1>
                        <p class="mb-2">Provide your ReMarkable <i>one-time-code</i> below</p>
                        <a class="underline mb-4 inline-block" target="_blank" href="https://my.remarkable.com/device/desktop/connect">Get one-time-code</a>
                        <form class="flex flex-col justify-between">
                            <div class="flex flex-wrap items-stretch w-full mb-4 relative">
                                <input required minlength=8 maxlength=8 pattern="[a-z]{8}" class="flex-shrink flex-grow flex-auto leading-normal flex-1 h-10 text-lg tracking-widest bg-indigo-50 rounded-r-none border-r-0 text-center text-slate-900 placeholder-slate-400 rounded-md pl-10 pr-10 text-indigo-900 border focus:border-indigo-600 border-indigo-900" placeholder="aabbccdd" type="text">
                                <div class="flex -mr-px">
                                    <input type="submit" value="submit" class="hover:cursor-pointer hover:bg-indigo-700 bg-indigo-600 text-indigo-100 flex items-center leading-normal bg-grey-lighter rounded rounded-l-none border text-lg border-l-0 border-indigo-900 px-3 whitespace-no-wrap text-gray-800 text-sm">
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
