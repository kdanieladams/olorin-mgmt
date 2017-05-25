@if(isset($list) && $list)
    {{-- list display here --}}
    {{ $value }}
@else
    {{-- edit form display here --}}
    {!! FormGroup::text($name, ['label' => $label, 'value' => $value, 'disabled' => !$editable]) !!}
@endif