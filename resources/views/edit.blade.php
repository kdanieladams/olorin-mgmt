@extends('mgmt::master')

@section('title', 'Edit ' . $model_name)

@section('main')
    <h1>Edit {{ $model_name }}</h1>
    <form name="model_form" method="POST" action="/mgmt/update/{{ $item->getUrlFriendlyName() }}/{{ $item->id }}">
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
                    @if($field->editable === false && $field->related === true)
                        <div class="form-group">
                            <label>{{ $field->label }}</label><br>
                            <a href="/mgmt/edit/{{ $field->class_name }}/{{ $item->{$field->name}->id }}">
                                {{ $item->{$field->name}->{$field->getLabelKey($item)} }}
                            </a>
                        </div>
                    @elseif(!is_null($field->view) || $field->type == 'add-one')
                        @if($field->type == 'add-one' && is_null($field->view))
                            <?php $field->view = "mgmt::fields._add-one"; ?>
                        @endif

                        @include($field->view, ['field' => $field, 'item' => $item])
                    @else
                        @if($field->related === true)
                            @include('mgmt::fields._' . $field->relationship, [
                                'value' => $field->getRelatedOptions($item),
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
                        @elseif(!is_null($field->view))
                            @include($field->view, ['field' => $field, 'item' => $item])
                        @else
                            @if($field->related === true)
                                @include('mgmt::fields._' . $field->relationship, [
                                    'value' => $field->getRelatedOptions($item),
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
                <button type="button" class="btn btn-danger" onclick="window.location.href = '/mgmt/delete/{{ $item->getUrlFriendlyName() }}/{{ $item->id }}';">Delete</button>
                <button type="button" class="btn btn-cancel" onclick="window.location.href = '/mgmt/{{ $item->getUrlFriendlyName() }}';">Cancel</button>
            </div>
        </div>
    </form>
@stop
