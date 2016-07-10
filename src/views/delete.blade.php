@extends('mgmt.master')

@section('title', 'Delete ' . $model_name)

@section('main')
    <h1>Delete {{ $model_name }}</h1>
    <div class="alert alert-danger">Are you sure you want to delete the following {{ $model_name }}?</div>
    @foreach($item->mgmt_fields as $field)
        @if($field->list)
            <strong>{{ $field->label }}:</strong>&nbsp;
            {{ $item->{$field->name} }}<br>
        @endif
    @endforeach
    <hr>
    <form method="POST" action="/mgmt/remove/{{ $model_name }}/{{ $item->id }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <input type="submit" class="btn btn-danger" value="Delete" />
        <button type="button" class="btn" onclick="window.history.back()">Cancel</button>
    </form>
@endsection