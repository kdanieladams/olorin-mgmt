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
    $value = new \Carbon\Carbon($value);
    $value = $value->toDateString(); //str_replace(' ', 'T', $value->toDateTimeString());
    ?>
    <div class="form-group">
        <label for="{{ $name }}">{{ $label }}:</label>
        <div class="input-group">
            @if(isset($editable) && !$editable)
                {!! Form::input('date', $name, $value, ['disabled' => 'disabled', 'class' => 'form-control']) !!}
            @else
                {!! Form::input('date', $name, $value, ['class' => 'form-control', 'step' => '1']) !!}
            @endif
            <span class="input-group-btn">
                <button type='button' class="btn btn-cancel pull-right" onclick='$("input[name=\"{{ $name }}\"]").val(formatLocalDate())'>
                    Now
                </button>
            </span>
        </div>
    </div>
@endif