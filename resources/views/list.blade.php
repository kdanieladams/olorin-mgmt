@extends('mgmt::master')

@section('title', $model_name . ' List')

@section('main')
    <button class="create-btn" onclick="window.location.href = '/mgmt/create/{{ $items[0]->getUrlFriendlyName() }}';">
        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
        Create a New {{ $model_name }}
    </button>
    <h1>{{ $model_name }} List</h1>
    <table class="table">
        <thead>
            <tr>
            @foreach($list_fields as $field)
                <th>{{ ucwords($field->label) }}</th>
            @endforeach
                <th></th>
            </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
            <tr>
            @foreach($list_fields as $field)
                <td>
                    @include('mgmt::fields._' . $field->type, [
                        'list' => true,
                        'value' => $item->{$field->name},
                        'view_options' => $field->view_options
                    ])
                </td>
            @endforeach
                <td>
                    <a href="/mgmt/edit/{{ $item->getUrlFriendlyName() }}/{{ $item->id }}">Edit</a>
                    @if(Route::has('show-' . strtolower($model_name)))
                        @if(isset($item->slug))
                            | <a href="{{ route('show-' . strtolower($model_name), $item->slug) }}">View</a>
                        @else
                            | <a href="{{ route('show-' . strtolower($model_name), $item->id) }}">View</a>
                        @endif
                    @endif
                    | <a href="/mgmt/delete/{{ $item->getUrlFriendlyName() }}/{{ $item->id }}">Delete</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

@stop