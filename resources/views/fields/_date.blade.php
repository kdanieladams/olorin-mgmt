@if(is_null($value))
    <?php $value = (new \Carbon\Carbon())->format("Y-m-d"); ?>
@endif

@if(isset($list) && $list)
    {{-- list display here --}}
    <?php
        if(isset($view_options['date_format'])) {
            $value = \Carbon\Carbon::createFromFormat("Y-m-d", $value);
            echo date($view_options['date_format'], $value->getTimestamp());
        }
        else {
            echo $value;
        }
    ?>
@else
    {{-- edit form display here --}}
    <?php
        $value = \Carbon\Carbon::createFromFormat("Y-m-d", $value);
        $value = $value->format("n/j/Y");
    ?>

    @section('head')
    <link href="/css/bootstrap-datepicker3.min.css" type="text/css" rel="stylesheet">
    <style>
        body.mgmt .datepicker-days table tr td {
            min-width: 25px;
        }
        body.mgmt .datepicker-days table tr td.day:hover {
            color: #121212;
        }
    </style>
    @append

    <div class="form-group">
        <label for="{{ $name }}">{{ ucwords(str_replace("_", " ", $label)) }}:</label>
        <div class="input-group">
            @if(isset($editable) && !$editable)
                {!! Form::text($name, $value, ['disabled' => 'disabled', 'class' => 'form-control']) !!}
            @else

                {!! Form::text($name, $value, ['class' => 'form-control', 'step' => '1']) !!}
            @endif
            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
        </div>
    </div>

    @section('scripts')
    <script src="/js/bootstrap-datepicker.min.js"></script>
    <script>
        $(document).ready(function(){
            $('input[name="{{ $name }}"]').datepicker({
                daysOfWeekHighlighted: "0,6",
                todayHighlight: true
            });
        });
    </script>
    @append
@endif