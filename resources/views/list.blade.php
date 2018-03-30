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
    </table>
@stop
@section('scripts')
    <script>
        $(document).ready(function(){
            $('#listTable').DataTable({
                processing: true,
                stateSave: true,
                serverSide: true,
                columns: [
                    @foreach($list_fields as $field)
                    {'name': '{{ $field->name }}'},
                    @endforeach
                    {
                        'name': '',
                        'sortable': false,
                        'searchable': false
                    }
                ],
                ajax: {
                    headers: {
                      'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    url:'/mgmt/jqdt/list/{{ $exmp->getUrlFriendlyName() }}',
                    type: 'POST'
                }
            });
        });
    </script>
@append