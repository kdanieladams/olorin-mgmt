@extends('mgmt::master')

@section('title', 'Create a New ' . $model_name)

@section('main')
    <h1>Create a New {{ $model_name }}</h1>
    <form name="model_form" method="POST" action="/mgmt/savenew/{{ $model_name }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <div class="row">
        @if($has_sidebar)
            <div class="col-md-8">
        @else
            <div class="col-md-12">
        @endif

        {{-- Render primary fields in a large column. --}}
        @foreach($item->mgmt_fields as $index => $field)
            @if($field->sidebar === false)
                @if(!is_null($field->view) || $field->type == 'add-one')
                    @if($field->type == 'add-one' && is_null($field->view))
                        <?php $field->view = "mgmt::fields._add-one"; ?>
                    @endif

                    @include($field->view, ['field' => $field, 'item' => $item, 'create' => true])
                @elseif($field->related == true)
                    @include('mgmt::fields._' . $field->relationship, [
                        'value' => $field->getRelatedOptions($item),
                        'selected' => null,
                        'name' => $field->name,
                        'label' => $field->label,
                        'editable' => true,
                        'view_options' => $field->view_options
                    ])

                @else
                    @include('mgmt::fields._' . $field->type, [
                        'value' => null,
                        'name' => $field->name,
                        'label' => $field->label,
                        'editable' => true,
                        'view_options' => $field->view_options
                    ])
                @endif
            @endif
        @endforeach
            </div>
            {{-- Render related fields in a side-bar. --}}
            <div class="col-md-4">
                @foreach($item->mgmt_fields as $index => $field)
                    @if($field->sidebar === true)
                        @if($field->related === true)
                            @include('mgmt::fields._' . $field->relationship, [
                                'value' => $field->getRelatedOptions($item),
                                'selected' => null,
                                'name' => $field->name,
                                'label' => $field->label,
                                'editable' => true,
                                'view_options' => $field->view_options
                            ])
                        @else
                            @include('mgmt::fields._' . $field->type, [
                                'value' => null, //$item->{$field->name},
                                'name' => $field->name,
                                'label' => $field->label,
                                'editable' => true,
                                'view_options' => $field->view_options
                            ])
                        @endif
                    @endif
                @endforeach
                <hr />
                <input type="submit" class="btn btn-success" value="Create New {{ $model_name }}" />
                <button type="button" class="btn btn-cancel" onclick="window.history.back();">Cancel</button>
            </div>
        </div>
    </form>
@stop