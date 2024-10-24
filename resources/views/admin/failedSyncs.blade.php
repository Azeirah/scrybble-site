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
                <th>#</th>
                <th>User</th>
                <th>When</th>
                <th>Filename</th>
                <th>Generated output</th>
                <th>ReMarkable input</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($shared as $shared_file)
                <tr>
                    <td>{{$shared_file['id']}}</td>
{{--                    if is admin--}}
                    <td>{{$shared_file['user']}}</td>
                    <td>{{$shared_file['created_at']}}</td>
                    <td>{{$shared_file['filename']}}</td>
                    <td>
                        @if($shared_file['output_href'])
                        <a href="{{$shared_file['output_href']}}">Download</a>
                        @else
                            Gone
                        @endif
                    </td>
                    <td>
                        @if($shared_file['input_href'])
                            <a href="{{$shared_file['input_href']}}">Download</a>
                        @else
                            Gone
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
