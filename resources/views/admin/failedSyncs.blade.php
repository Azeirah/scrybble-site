@extends('layouts.app')

<style>
    thead th {
        position: sticky;
        top: 0;
    }
</style>

@section('content')
    <div class="m-4">
        <h1>Failed Syncs</h1>
        <table class="table table-dark table-bordered table-striped">
            <thead>
            <tr>
                <th>User</th>
                <th>When</th>
                <th>Filename</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($failed_syncs as $failed_sync)
                <tr>
                    <td>{{ $failed_sync['user'] }}</td>
                    <td>{{$failed_sync['created_at']}}</td>
                    <td>
                        <a href="/admin/failed_syncs/dl?user={{$failed_sync['user_id']}}&path={{urlencode($failed_sync['filename'])}}" download>
                            {{ $failed_sync['filename'] }}
                        </a>
                        {{--                        @else--}}
                        {{--                            <pre><code>{{json_encode(json_decode($failed_sync['context']), JSON_PRETTY_PRINT)}}</code></pre>--}}
                        {{--                        @endif--}}
                    </td>
                </tr>
            @endforeach
            </tbody>
    </div>
    </table>
@endsection
