@if(isset($list) && $list)
    {{-- list view here --}}
@else
    {{-- edit view here --}}
    {!! FormGroup::select($name, [
        'selected' => $selected,
        'value' => $value,
        'label' => $label,
        'disabled' => !$editable
    ]) !!}
@endif