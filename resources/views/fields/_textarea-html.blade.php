@if(isset($list) && $list)
    {{-- list display here --}}
    {{ $value }}
@else
    {{-- edit form display here --}}
    {!! FormGroup::textarea($name, ['value' => $value, 'disabled' => !$editable]) !!}

    @section('scripts')
    <script type="text/javascript">
        // CK Editor overloads
        var CKEDITOR_BASEPATH = '/js/vendor/ckeditor/';
    </script>
    <script src="/js/vendor/ckeditor/ckeditor.js"></script>
    <script>
        $(document).ready(function(){
            CKEDITOR.replace({{ $name }});
        });
    </script>
    @stop
@endif