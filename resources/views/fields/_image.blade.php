<?php
    //if(is_null($value)) {
    //    $value = current($view_options['image_options']['options']);
    //}

    $image_url = is_null($value) ? '' : rtrim($view_options['image_options']['dir'], "/") . "/" . $value;
?>

@if(isset($list) && $list)
    {{-- list display here --}}
    <img src="{{ $image_url }}" class="image-preview">
@else
    <?php $default = "-- Select " . ucwords($label) . " --"; ?>
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
        <img src="{{ $image_url }}" id="{{ $name }}_preview" {!!  is_null($value) ? 'style="opacity: 0; height: 150px;"' : '' !!}>
    @endif

    <div class="form-group">
        <label for="{{ $name }}">{{ $label }}:</label>
        <div class="input-group">
            @if(isset($editable) && !$editable)
                {{ $value }}
            @else
                <input type="file" class="hidden" name="{{ $name }}_file" id="{{ $name }}_file" accept="image/*">
                <select id="{{ $name }}" name="{{ $name }}" class="form-control">
                    <option data-value="" data-url="">{{ $default }}</option>
                    @foreach($view_options['image_options']['options'] as $url => $filename)
                    <option data-value="{{ $filename }}"
                            data-url="{{ $url }}"
                            @if($value == $filename) selected @endif>
                        {{ $filename }}
                    </option>
                    @endforeach
                </select>
                <div class="input-group-btn" id="{{ $name }}_dropdown">
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
        $(document).ready(function(){
            $('#{{ $name }}').select2();
            
            $('#{{ $name }}').on('selected-new-img', function(e){
                $('#{{ $name }}_preview').animate({'opacity': 0}, 500, function(){
                    var url = $('#{{ $name }}').data('url');
                    if(url.length > 0) {
                        $(this).prop('src', url);
                        $(this).animate({'opacity': 1}, 500);
                    }
                });
            });

            $('#{{ $name }}').on('change', function(e){
                var opt = $(this).find(':selected');
                // display the image
                $('#{{ $name }}').data('url', opt.data('url'));
                $('#{{ $name }}').trigger('selected-new-img');
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
                $('#{{ $name }}').children('.appended').remove();
                $('#{{ $name }}')
                    .append('<option class="appended" data-value="' + filename + '" data-url="' + url + '">' 
                        + filename + '</option>');

                // display the image
                $('#{{ $name }}').data('url', url);
                $('#{{ $name }}').trigger('selected-new-img');
            });
        });
    </script>
    @append
@endif
