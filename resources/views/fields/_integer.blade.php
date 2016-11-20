@if(isset($list) && $list)
    {{-- list display here --}}
    {{ $value }}
@else
    {{-- edit form display here --}}
    {!! FormGroup::number($name, ['value' => $value, 'disabled' => !$editable]) !!}
@endif