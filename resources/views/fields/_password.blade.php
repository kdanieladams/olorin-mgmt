@if(isset($list) && $list)
    {{-- list display here --}}
@else
    {{-- edit form display here --}}
    {!! FormGroup::password($name, ['value' => $value, 'disabled' => !$editable]) !!}
    {!! FormGroup::password($name . "_confirm", ['value' => $value, 'disabled' => !$editable]) !!}
@endif