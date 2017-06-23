@if(isset($list) && $list)
    {{-- list view here --}}
    {{ $value[is_null($selected) ? key($value) : $selected] }}
@else
    {{-- edit view here --}}
    @section('head')
    <style>
        input[name="{{ $name }}_display"][readonly] {
            background-color: inherit;
        }
        input[name="{{ $name }}_display"][readonly]:hover {
            cursor: pointer;
        }
    </style>
    @append

    <div class="form-group">
        <label for="{{ $name }}">{{ $label }}:</label>
        <div class="input-group">
            {!! Form::text($name . '_display', $value[is_null($selected) ? key($value) : $selected], [
                'readonly' => 'readonly',
                'class' => 'form-control',
                'id' => $name . '_display'
            ]) !!}
            <input type="hidden" name="{{ $name }}" value="{{ is_null($selected) ? key($value) : $selected }}">

            <div class="input-group-btn" id="{{ $name }}_dropdown">
                <button type="button" class="btn btn-default dropdown-toggle"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="glyphicon glyphicon-chevron-down"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                @foreach($value as $val => $lbl)
                    <li>
                        <a href="#" data-value="{{ $val }}">{{ $lbl }}</a>
                    </li>
                @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{-- inject some javascript --}}
    @section('scripts')
    <script>
        $(document).ready(function(){
            $('#{{ $name }}_dropdown ul.dropdown-menu li a').click(function(e){
                e.preventDefault();

                var value = $(this).data("value");
                var label = $(this).html();

                $('input[name="{{ $name }}_display"]').val(label);
                $('input[name="{{ $name }}"]').val(value);
            });

            $('#{{ $name }}_display').click(function(e){
                e.stopPropagation();

                $("#{{ $name }}_dropdown").find('[data-toggle=dropdown]').dropdown('toggle');
            });
        });
    </script>
    @append
@endif