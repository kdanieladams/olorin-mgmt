@extends('mgmt::master')

@section('title', 'Create a New ' . $model_name)

@section('main')
    <h1>Create a New {{ $model_name }}</h1>
    <form method="POST" action="/mgmt/savenew/{{ $model_name }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <div class="row">
        @if($related_fields)
            <div class="col-md-8">
        @else
            <div class="col-md-12">
        @endif

        {{-- Render primary fields in a large column. --}}
        @foreach($item->mgmt_fields as $index => $field)
            @if($field->sidebar === false && $field->editable === true)
                @include('mgmt::fields._' . $field->type, [
                    'value' => null,
                    'name' => $field->name,
                    'label' => $field->label,
                    'editable' => $field->editable,
                    'view_options' => $field->view_options
                ])
            @endif
        @endforeach

            </div>

            {{-- Render related fields in a side-bar. --}}
            <div class="col-md-4">
                @foreach($item->mgmt_fields as $index => $field)
                    @if($field->sidebar === true && $field->editable === true)
                        @include('mgmt::fields._' . $field->relationship, [
                            'value' => $field->getRelatedItems($item),
                            'selected' => null,
                            'name' => $field->name,
                            'label' => $field->label,
                            'editable' => $field->editable,
                            'view_options' => $field->view_options
                        ])
                    @endif
                @endforeach
                <hr />
                <input type="submit" class="btn btn-success" value="Create New {{ $model_name }}" />
                <button type="button" class="btn btn-cancel" onclick="window.location.href = '/mgmt/{{ $item->getUrlFriendlyName() }}';">Cancel</button>
            </div>
        </div>
    </form>
@stop