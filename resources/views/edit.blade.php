@extends('mgmt::master')

@section('title', 'Edit ' . $model_name)

@section('main')
    <h1>Edit {{ $model_name }}</h1>
    <form method="POST" action="/mgmt/update/{{ $model_name }}/{{ $item->id }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <div class="row">
            @if($related_fields)
                <div class="col-md-8">
            @else
                <div class="col-md-12">
            @endif

            {{-- Render primary fields in a large column. --}}
            @foreach($item->mgmt_fields as $index => $field)
                @if($field->sidebar === false)
                    @if($field->editable === false && $field->related === true)
                        <div class="form-group">
                            <label>{{ $field->label }}</label><br>
                            <a href="/mgmt/edit/{{ $field->class_name }}/{{ $item->{$field->name}->id }}">
                                {{ $item->{$field->name}->{$field->getLabelKey($item)} }}
                            </a>
                        </div>
                    @else
                        @if($field->related === true)
                            @include('mgmt::fields._' . $field->relationship, [
                                'value' => $field->getRelatedItems($item),
                                'selected' => $field->getRelatedId($item),
                                'name' => $field->name,
                                'label' => $field->label,
                                'editable' => $field->editable,
                                'view_options' => $field->view_options
                            ])
                        @else
                            @include('mgmt::fields._' . $field->type, [
                                'value' => $item->{$field->name},
                                'name' => $field->name,
                                'label' => $field->label,
                                'editable' => $field->editable,
                                'view_options' => $field->view_options
                            ])
                        @endif
                    @endif
                @endif
            @endforeach

            </div>

            {{-- Render related fields in a side-bar. --}}
            <div class="col-md-4">
                @foreach($item->mgmt_fields as $index => $field)
                    @if($field->sidebar)
                        @if($field->editable === false)
                            <div class="form-group">
                                <label>{{ $field->label }}</label><br>
                                <a href="/mgmt/edit/{{ $field->class_name }}/{{ $item->{$field->name}->id }}">
                                    {{ $item->{$field->name}->{$field->getLabelKey($item)} }}
                                </a>
                            </div>
                        @else
                            @if($field->related === true)
                                @include('mgmt::fields._' . $field->relationship, [
                                    'value' => $field->getRelatedItems($item),
                                    'selected' => $field->getRelatedId($item),
                                    'name' => $field->name,
                                    'label' => $field->label,
                                    'editable' => $field->editable,
                                    'view_options' => $field->view_options
                                ])
                            @else
                                @include('mgmt::fields._' . $field->type, [
                                    'value' => $item->{$field->name},
                                    'name' => $field->name,
                                    'label' => $field->label,
                                    'editable' => $field->editable,
                                    'view_options' => $field->view_options
                                ])
                            @endif
                        @endif
                    @endif
                @endforeach
                <hr />
                <input type="submit" class="btn btn-success" value="Save Changes" />
                <button type="button" class="btn btn-danger" onclick="window.location.href = '/mgmt/delete/{{ $model_name }}/{{ $item->id }}';">Delete</button>
                <button type="button" class="btn" onclick="window.location.href = '/mgmt/{{ $model_name }}';">Cancel</button>
            </div>
        </div>
    </form>
@stop