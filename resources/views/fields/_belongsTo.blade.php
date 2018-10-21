@if(isset($list) && $list)
    {{-- list view here --}}
    {{ is_null($selected) || $selected == 0 ? '' : $value[$selected] }}
@else
    {{-- edit view here --}}
    <?php
    $default = "-- Select " . ucwords($label) . " --";
    $value = [$default] + $value;
    ?>

    {!! FormGroup::select($name, ['value' => $value, 'selected' => $selected, 'label' => $label ]) !!}

    @section('scripts')
    <script>
        $(document).ready(function(){
            $('select#{{ $name }}').select2();
        });
    </script>
    @append
@endif