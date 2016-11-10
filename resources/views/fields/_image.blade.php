@if(isset($list) && $list)
    {{-- list display here --}}
    {{ $value }}
@else
    {{-- manipulate some php vars --}}
    <?php
        $dir = scandir($view_options['image_options']['path']);
        $options = array();
        foreach($dir as $k => $filename) {
            if($filename == "." || $filename == "..") {
                continue;
            }

            $options[rtrim($view_options['image_options']['dir'], "/") . "/" . $filename] = $filename;
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
    </style>
    @append

    {{-- edit form display here --}}
    @if($view_options['image_options']['preview'])
        <img src="{{ $value }}" id="{{ $name }}_preview">
    @endif

    {!! FormGroup::select($name, [
        'selected' => $value,
        'value' => $options,
        'label' => $label,
        'disabled' => !$editable
    ]) !!}

    {{-- inject some javascript --}}
    @section('scripts')
    <script>
        $(document).ready(function(){
            $('#{{ $name }}').change(function(e){
                $('#{{ $name }}_preview').fadeOut(500, function(){
                    $(this).prop('src', $('#{{ $name }}').val());
                    $(this).fadeIn(500);
                });
            });
        });
    </script>
    @append
@endif
