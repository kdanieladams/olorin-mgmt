
@if(isset($list) && $list)
    {{-- list view here --}}
@elseif(is_array($view_options) && count($view_options) > 0)
    {{-- edit view with options here --}}
    <?php
        $options = array(
            'selected' => $selected,
            'value' => $value,
            'label' => $label,
            'disabled' => !$editable
        );

        $options = array_merge($options, $view_options);
    ?>

    {!! FormGroup::multiselect($name, $options) !!}
@else
    {{-- edit view here --}}
    {!! FormGroup::multiselect($name, [
        'selected' => $selected,
        'value' => $value,
        'label' => $label,
        'disabled' => !$editable
    ]) !!}
@endif