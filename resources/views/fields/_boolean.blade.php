@if(isset($list) && $list)
    {{-- list view here --}}
@else
    {{-- edit view with options here --}}
    <?php
    $options = array(
        'selected' => 0,//$selected,
        'value' => $value,
        'label' => $label,
        'disabled' => !$editable
    );
    ?>

    @if(is_array($view_options) && count($view_options) > 0)
        <?php $options = array_merge($options, $view_options); ?>
    @endif

    {!! FormGroup::boolean($name, $options) !!}
@endif