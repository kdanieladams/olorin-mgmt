@if(isset($list) && $list)
    {{-- list view here --}}
    @if(isset($view_options['labels']) && is_array($view_options['labels']) && count($view_options['labels']) == 2)
        {{ $value == 1 ? $view_options['labels']['true'] : $view_options['labels']['false'] }}
    @else
        {{ $value == 1 ? "True" : "False" }}
    @endif
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