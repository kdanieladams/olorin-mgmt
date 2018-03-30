@extends('mgmt::master')
@section('title', $model_name . ' List')
@section('main')
    @if(empty($exmp->create_permission) || $user->hasPermission($exmp->create_permission))
    <button class="create-btn" onclick="window.location.href = '/mgmt/create/{{ $exmp->getUrlFriendlyName() }}';">
        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
        Create a New {{ $model_name }}
    </button>
    @endif
    <h1>{{ $model_name }} List</h1>
    <table class="table" id="listTable">
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
                    <?php $fieldtype = $field->type; ?>
                    @if($fieldtype == "related")
                        @include('mgmt::fields._' . $field->relationship, [
                            'list' => true,
                            'value' => $field->getRelatedItems($item),
                            'selected' => $field->getRelatedId($item),
                            'name' => $field->name,
                            'label' => $field->label,
                            'view_options' => $field->view_options
                        ])
                    @else
                        @include('mgmt::fields._' . $fieldtype, [
                            'list' => true,
                            'name' => $field->name,
                            'value' => $item->{$field->name},
                            'view_options' => $field->view_options
                        ])
                    @endif

                </td>
            @endforeach
                <td>
                    <div class="btn-group pull-right">
                        <a href="/mgmt/edit/{{ $item->getUrlFriendlyName() }}/{{ $item->id }}" class="btn btn-hollow">
                            <span class="glyphicon glyphicon-edit"></span>
                            Edit
                        </a>
                        <a href="/mgmt/delete/{{ $item->getUrlFriendlyName() }}/{{ $item->id }}" class="btn btn-hollow-danger">
                            <span class="glyphicon glyphicon-trash"></span>
                            Delete
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

@stop
@section('scripts')
    <script>
        $(document).ready(function(){
            $('#listTable').DataTable({
                stateSave: true
            });
        });
    </script>
@append