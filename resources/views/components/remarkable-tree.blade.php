@props([
    'currentWorkingDirectory',
    'ls',
])

<h1>{{$currentWorkingDirectory}}</h1>
<ul>
    @foreach($ls as $item)
        <li class="list-unstyled ">
            @if($item['type'] === 'd')
                <svg class="bi" fill="none" style="width: 24px; height: 24px; display: inline-block;"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                </svg>
            @else
                <svg style="width: 24px; height: 24px; display: inline-block;" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            @endif
            @if($item['type'] === 'd')
                <span><a class="link-secondary"
                         href="{{'/dashboard?path=' . $item['path']}}">{{$item['name']}}</a></span>
            @else
                <span><a class="link-primary"
                         href="{{"/file?path={$item['path']}"}}">{{$item['name']}}</a></span>
            @endif
        </li>
    @endforeach
</ul>
