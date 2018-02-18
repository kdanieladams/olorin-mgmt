@if(isset($list) && $list)
    {{-- list display here --}}
    <div style="background-color:{{ $value }}; height: 16px;"></div>
@else
    {{-- edit form display here --}}
    {!! FormGroup::color($name, ['label' => $label, 'value' => $value, 'disabled' => !$editable]) !!}
@endif