@if(isset($list) && $list)
    {{-- list view here --}}
@else
    {{-- edit view with options here --}}
    <?php
    $options = array(
        'selected' => $field->getRelatedId($item),
        'value' => $field->getRelatedItems($item),
        'label' => $field->label,
        'disabled' => !$field->editable
    );

    $related_items = $options['value'];
    $meta_fields = false;

    if(empty($related_items)) {
        $classref = $field->{$field->relationship};
        $meta_fields = $classref::first()->mgmt_fields;
    }

    $meta_fields = $meta_fields ?: $related_items[key($related_items)]->mgmt_fields;

    if(is_array($field->view_options) && count($field->view_options) > 0){
        $options = array_merge($options, $field->view_options);
    }
    ?>

    <div class="form-group">
        <label>{{ $options['label'] }}:</label>
        <div class="inner-field-container">
            <table class="table table-striped">
                <thead>
                <tr>
                    @foreach($meta_fields as $rel_field)
                        @if(strtolower($rel_field->name) != strtolower($model_name))
                            <th>{{ $rel_field->label }}</th>
                        @endif
                    @endforeach
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($related_items as $related_item)
                    <tr>
                        @foreach($related_item->mgmt_fields as $related_item_field)
                            @if(strtolower($related_item_field->name) != strtolower($model_name))
                                <td>
                                    @include('mgmt::fields._' . $related_item_field->type, [
                                        'list' => true,
                                        'name' => $related_item_field->name,
                                        'value' => $related_item->{$related_item_field->name},
                                        'view_options' => $related_item_field->view_options
                                    ])
                                </td>
                            @endif
                        @endforeach
                        <td>
                            <a href="#">Edit</a> | <a href="#">Delete</a>
                        </td>
                    </tr>
                @endforeach
                <tr class="separator-row"></tr>
                <tr>
                    @foreach($meta_fields as $rel_field)
                        @if(strtolower($rel_field->name) != strtolower($model_name))
                            <td>
                                @include('mgmt::fields._' . $rel_field->type, [
                                    'value' => null,
                                    'name' => 'inner_' . $rel_field->name,
                                    'label' => $rel_field->label,
                                    'editable' => true,
                                    'view_options' => $rel_field->view_options
                                ])
                            </td>
                        @endif
                    @endforeach
                    <td>
                        <button class="btn btn-success" type="button" onclick="inner_{{ $field->name }}_submit()">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                            Add One
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    {{-- inject some javascript --}}
    @section('scripts')
    <script>
        var inner_{{ $field->name }}_submit = function(){
            var postData = {};
            var fieldNames = [
            @foreach($meta_fields as $rel_field)
                @if(strtolower($rel_field->name) != strtolower($model_name))
                'inner_{{ $rel_field->name }}',
                @endif
            @endforeach
            ];

            $.each(fieldNames, function(index, fieldName){
                var elm = $("[name='" + fieldName + "']");
                var val = '';

                if(elm.attr("type") == "radio") {
                    val = $("[name='" + fieldName + "']:checked").val();
                }
                else {
                    val = elm.val();
                }

                postData[fieldName] = val;
            });

            console.log("Post Data:");
            console.log("=========");
            console.log(postData);
        };
    </script>
    @append
@endif