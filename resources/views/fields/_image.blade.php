@if(isset($list) && $list)
    {{-- inject some styles --}}
    @section('head')
        <style>
            #{{ $name }}_preview {
                display: block;
                max-width: 100%;
                max-height: 50px;
                margin: 0;
            }
        </style>
    @append

    {{-- list display here --}}
    <img src="{{ $value }}" id="{{ $name }}_preview">
@else
    {{-- manipulate some php vars --}}
    <?php
        $dir = scandir($view_options['image_options']['path']);
        $options = array();
        $selected_option = "";

        foreach($dir as $k => $filename) {
            if($filename == "." || $filename == "..") {
                continue;
            }
            $opt_val = rtrim($view_options['image_options']['dir'], "/") . "/" . $filename;
            $options[$opt_val] = $filename;

            if($opt_val == $value) {
                $selected_option = $filename;
            }
        }
    ?>

    {{-- inject some styles --}}
    @section('head')
        <style>
            #{{ $name }}_preview {
                display: block;
                max-width: 100%;
                max-height: 150px;
                margin: 0 auto;
            }

            input[name="{{ $name }}_display"][readonly] {
                background-color: inherit;
            }
            input[name="{{ $name }}_display"][readonly]:hover {
                cursor: pointer;
            }
        </style>
    @append

    {{-- edit form display here --}}
    @if($view_options['image_options']['preview'])
        <img src="{{ $value }}" id="{{ $name }}_preview">
    @endif

    <div class="form-group">
        <label for="{{ $name }}">{{ $label }}:</label>
        <div class="input-group">
            @if(isset($editable) && !$editable)
                {{ $selected_option }}
            @else
                {!! Form::text($name . '_display', $selected_option, [
                    'readonly' => 'readonly',
                    'class' => 'form-control',
                    'id' => $name . '_display'
                ]) !!}
                <input type="hidden" name="{{ $name }}" value="{{ $value }}">

                <div class="input-group-btn" id="{{ $name }}_dropdown">
                    <button type="button" class="btn btn-default dropdown-toggle"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="glyphicon glyphicon-chevron-down"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                    @foreach($options as $val => $opt)
                        <li>
                            <a href="#" data-value="{{ $val }}">{{ $opt }}</a>
                        </li>
                    @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    {{-- inject some javascript --}}
    @section('scripts')
    <script>
        $(document).ready(function(){
            $('input[name="{{ $name }}"]').on('selected-new-img', function(e){
                $('#{{ $name }}_preview').fadeOut(500, function(){
                    $(this).prop('src', $('input[name="{{ $name }}"]').val());
                    $(this).fadeIn(500);
                });
            });

            $('#{{ $name }}_dropdown ul.dropdown-menu li a').click(function(e){
                e.preventDefault();

                var value = $(this).data("value");
                var label = $(this).html();

                $('input[name="{{ $name }}_display"]').val(label);
                $('input[name="{{ $name }}"]').val(value);
                $('input[name="{{ $name }}"]').trigger('selected-new-img');
            });

            $('#{{ $name }}_display').click(function(e){
                e.stopPropagation();

                $("#{{ $name }}_dropdown").find('[data-toggle=dropdown]').dropdown('toggle');
            });
        });
    </script>
    @append
@endif
