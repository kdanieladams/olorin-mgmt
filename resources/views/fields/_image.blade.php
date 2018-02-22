<?php
    if(is_null($value)) {
        $value = current($view_options['image_options']['options']);
    }

    $image_url = rtrim($view_options['image_options']['dir'], "/") . "/" . $value;
?>

@if(isset($list) && $list)
    {{-- list display here --}}
    <img src="{{ $image_url }}" class="image-preview">
@else
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
        <img src="{{ $image_url }}" id="{{ $name }}_preview">
    @endif

    <div class="form-group">
        <label for="{{ $name }}">{{ $label }}:</label>
        <div class="input-group">
            @if(isset($editable) && !$editable)
                {{ $value }}
            @else
                {!! Form::text($name . '_display', $value, [
                    'readonly' => 'readonly',
                    'class' => 'form-control',
                    'id' => $name . '_display'
                ]) !!}
                <input type="hidden" name="{{ $name }}" value="{{ $value }}" data-url="{{ $image_url }}">
                <input type="file" class="hidden" name="{{ $name }}_file" id="{{ $name }}_file" accept="image/*">

                <div class="input-group-btn" id="{{ $name }}_dropdown">
                    <button type="button" class="btn btn-default dropdown-toggle"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="glyphicon glyphicon-chevron-down"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                    @foreach($view_options['image_options']['options'] as $url => $filename)
                        <li>
                            <a href="#" data-value="{{ $filename }}" data-url="{{ $url }}">{{ $filename }}</a>
                        </li>
                    @endforeach
                    </ul>
                    <button type="button" id="{{ $name }}_upload_btn"
                            class="btn btn-warning">
                        <span class="glyphicon glyphicon-cloud-upload"></span>
                        Upload
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- inject some javascript --}}
    @section('scripts')
    <script>
        function {{ $name }}SetOptionEventHandlers() {
            $('#{{ $name }}_dropdown ul.dropdown-menu li a').off('click');
            $('#{{ $name }}_dropdown ul.dropdown-menu li a').click(function(e){
                e.preventDefault();

                var value = $(this).data("value");
                var label = $(this).html();
                var url = $(this).data('url');

                $('input[name="{{ $name }}_display"]').val(label);
                $('input[name="{{ $name }}"]').val(value);
                $('input[name="{{ $name }}"]').data('url', url);
                $('input[name="{{ $name }}"]').trigger('selected-new-img');
            });
        }

        $(document).ready(function(){
            $('input[name="{{ $name }}"]').on('selected-new-img', function(e){
                $('#{{ $name }}_preview').fadeOut(500, function(){
                    $(this).prop('src', $('input[name="{{ $name }}"]').data('url'));
                    $(this).fadeIn(500);
                });
            });

            {{ $name }}SetOptionEventHandlers();

            $('#{{ $name }}_display').click(function(e){
                e.stopPropagation();

                $("#{{ $name }}_dropdown").find('[data-toggle=dropdown]').dropdown('toggle');
            });

            $('#{{ $name }}_upload_btn').click(function(e){
                $('#{{ $name }}_file').click();
            });
            $('#{{ $name }}_file').on('change', function(e){
                var url = window.URL.createObjectURL(this.files[0]),
                    filename = this.files[0].name,
                    label = filename,
                    value = filename;

                // add it to the list of images
                $('#{{ $name }}_dropdown ul.dropdown-menu').children('.appended').remove();
                $('#{{ $name }}_dropdown ul.dropdown-menu')
                    .append('<li class="appended"><a href="#" data-value="' + filename + '" data-url="' + url + '">' + filename + '</a></li>');
                {{ $name }}SetOptionEventHandlers();

                // display the image
                $('input[name="{{ $name }}_display"]').val(label);
                $('input[name="{{ $name }}"]').val(value);
                $('input[name="{{ $name }}"]').data('url', url);
                $('input[name="{{ $name }}"]').trigger('selected-new-img');
            });
        });
    </script>
    @append
@endif
