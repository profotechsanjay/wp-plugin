
<?php 

$data = (json_decode($data)); 

$data = isset($data->fields)?$data->fields:$data;

$i = 1; $form_id = $survey_id;
foreach ($data as $d){
    $d->id = $i;
    $d->form_id = $form_id;  
    $i++;
}

$data = json_encode($data);

wp_enqueue_style('mapbox.css', 'https://api.tiles.mapbox.com/mapbox.js/v2.1.4/mapbox.css'); 

if(isset($survey->is_submitted) && $survey->is_submitted == 1){
    ?>
        <input type="hidden" id="surveysubmited" name="surveysubmited" value="1" />
    <?php
}
?>

<script type="text/javascript">

    var dtform = '<?php echo $data; ?>';
    dtform = $.parseJSON(dtform);        

    window.Fixtures || (window.Fixtures = {});

    Fixtures.RESPONSE_FIELD = {
        id: 1,
        label: 'Name',
        field_type: 'text'
    };

    Fixtures.KITCHEN_SINK_FORM = dtform;

    Fixtures.FormRendererOptions = {
        LOADED: function () {
            return {
                project_id: 'dummy_val',
                response_fields: [],
                response: {
                    id: 'xxx',
                    responses: {}
                },
                afterSubmit: false
            };
        },
        RESPONSE_LOADED: function () {
            return {
                project_id: 'dummy_val',
                response: {
                    responses: {
                        '1': 'hey'
                    }
                },
                afterSubmit: false
            };
        },
        PROJECT_LOADED: function () {
            return {
                project_id: 'dummy_val',
                response_fields: [_.clone(Fixtures.RESPONSE_FIELD)],
                response: {
                    id: 'xxx'
                },
                afterSubmit: false
            };
        },
        NOT_LOADED: function () {
            return {
                project_id: 'dummy_val',
                response: {
                    id: 'xxx'
                },
                afterSubmit: false
            };
        },
        KITCHEN_SINK: function () {
            return {
                project_id: 'dummy_val',
                response_fields: Fixtures.KITCHEN_SINK_FORM,
                response: {
                    id: 'xxx',
                    responses: {}
                }
            };
        },
        KITCHEN_SINK_REQ: function () {
            return {
                project_id: 'dummy_val',
                response_fields: _.map(Fixtures.KITCHEN_SINK_FORM, function (rf) {
                    return _.extend({}, rf, {required: true});
                }),
                response: {
                    id: 'xxx',
                    responses: {}
                }
            };
        },
        SIZES: function () {
            return {
                project_id: 'dummy_val',
                response_fields: [
                    {
                        id: 1,
                        form_id: 5,
                        label: "Text (small)",
                        field_options: {
                            size: 'small',
                            maxlength: 20
                        },
                        required: true,
                        blind: true,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "text",
                        cid: null
                    },
                    {
                        id: 2,
                        form_id: 5,
                        label: "Text (medium)",
                        field_options: {
                            size: 'medium',
                            minlength: 10,
                        },
                        required: true,
                        blind: true,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "text",
                        cid: null
                    },
                    {
                        id: 3,
                        form_id: 5,
                        label: "Text (large)",
                        field_options: {
                            size: 'large',
                            minlength: 10,
                            maxlength: 20
                        },
                        required: true,
                        blind: true,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "text",
                        cid: null
                    },
                    {
                        id: 4,
                        form_id: 5,
                        label: "paragraph (small)",
                        field_options: {
                            size: 'small',
                            minlength: 10,
                            maxlength: 20,
                            min_max_length_units: 'words'
                        },
                        required: true,
                        blind: true,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "paragraph",
                        cid: null
                    },
                    {
                        id: 5,
                        form_id: 5,
                        label: "paragraph (medium)",
                        field_options: {
                            size: 'medium',
                            minlength: 10,
                            maxlength: 20
                        },
                        required: true,
                        blind: true,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "paragraph",
                        cid: null
                    },
                    {
                        id: 6,
                        form_id: 5,
                        label: "paragraph (large)",
                        field_options: {
                            size: 'large',
                            minlength: 10,
                            maxlength: 20
                        },
                        required: true,
                        blind: true,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "paragraph",
                        cid: null
                    },
                    {
                        id: 7,
                        form_id: 5,
                        label: "block_of_text (small)",
                        field_options: {
                            size: 'small',
                            description: 'i am a block of text, yo'
                        },
                        required: true,
                        blind: true,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "block_of_text",
                        cid: null
                    },
                    {
                        id: 8,
                        form_id: 5,
                        label: "block_of_text (medium)",
                        field_options: {
                            size: 'medium',
                            description: 'i am a block of text, yo'
                        },
                        required: true,
                        blind: true,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "block_of_text",
                        cid: null
                    },
                    {
                        id: 9,
                        form_id: 5,
                        label: "block_of_text (large)",
                        field_options: {
                            size: 'large',
                            description: 'i am a block of text, yo'
                        },
                        required: true,
                        blind: true,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "block_of_text",
                        cid: null
                    },
                    {
                        id: 10,
                        form_id: 5,
                        label: "section_break (small)",
                        field_options: {
                            size: 'small',
                            description: 'i am a section break, yo'
                        },
                        required: true,
                        blind: true,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "section_break",
                        cid: null
                    },
                    {
                        id: 11,
                        form_id: 5,
                        label: "section_break (medium)",
                        field_options: {
                            size: 'medium',
                            description: 'i am a section break, yo'
                        },
                        required: true,
                        blind: true,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "section_break",
                        cid: null
                    },
                    {
                        id: 12,
                        form_id: 5,
                        label: "section_break (large)",
                        field_options: {
                            size: 'large',
                            description: 'i am a section break, yo'
                        },
                        required: true,
                        blind: true,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "section_break",
                        cid: null
                    }
                ],
                response: {
                    id: 'xxx',
                    responses: {}
                }
            };
        },
        SHORT: function () {
            return {
                project_id: 'dummy_val',
                response_fields: [
                    {
                        id: 34,
                        form_id: 5,
                        label: "",
                        field_options: {},
                        required: false,
                        blind: false,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "identification",
                        cid: null
                    }, {
                        id: 35,
                        form_id: 5,
                        label: "Text",
                        field_options: {},
                        required: false,
                        blind: false,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "text",
                        cid: null
                    }
                ],
                response: {
                    id: 'xxx',
                    responses: {}
                },
                afterSubmit: {
                    method: 'page',
                    html: 'You did it!'
                }
            };
        },
        CONDITIONAL: function () {
            return {
                project_id: 'dummy_val',
                response_fields: [
                    {
                        id: 35,
                        form_id: 5,
                        label: "Do you like conditional form fields?",
                        field_options: {
                            options: [
                                {
                                    label: 'Yes',
                                    checked: true
                                }, {
                                    label: 'No',
                                    checked: false
                                }
                            ]
                        },
                        required: false,
                        blind: false,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "dropdown",
                        cid: null
                    }, {
                        id: 36,
                        form_id: 5,
                        label: "Dang, that sucks.",
                        field_options: {
                            size: 'large',
                            conditions: [
                                {
                                    response_field_id: 35,
                                    method: 'eq',
                                    value: 'No'
                                }
                            ]
                        },
                        required: false,
                        blind: false,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "section_break",
                        cid: null
                    }, {
                        id: 37,
                        form_id: 5,
                        label: "What's your email address?",
                        required: false,
                        blind: false,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "email",
                        cid: null
                    }, {
                        id: 38,
                        form_id: 5,
                        label: "What are the nuclear launch codes?",
                        field_options: {
                            conditions: [
                                {
                                    response_field_id: 37,
                                    method: 'contains',
                                    value: 'whitehouse'
                                }
                            ]
                        },
                        required: false,
                        blind: false,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "text",
                        cid: null
                    }, {
                        id: 39,
                        form_id: 5,
                        label: "What's the most boring part of the job?",
                        field_options: {},
                        required: false,
                        blind: false,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "text",
                        cid: null
                    }, {
                        id: 40,
                        form_id: 5,
                        label: "Guess a number...",
                        required: false,
                        blind: false,
                        admin_only: false,
                        field_options: {
                            min: 1,
                            max: 10
                        },
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "number",
                        cid: null
                    }, {
                        id: 100,
                        form_id: 5,
                        label: "",
                        field_options: {},
                        required: false,
                        blind: false,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.217Z",
                        updated_at: "2014-08-22T20:50:37.217Z",
                        field_type: "page_break",
                        cid: null
                    }, {
                        id: 41,
                        form_id: 5,
                        label: "Why do you like big numbers?",
                        field_options: {
                            conditions: [
                                {
                                    response_field_id: 40,
                                    method: 'gt',
                                    value: '5'
                                }
                            ]
                        },
                        required: true,
                        blind: false,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "text",
                        cid: null
                    }, {
                        id: 101,
                        form_id: 5,
                        label: "",
                        field_options: {},
                        required: false,
                        blind: false,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.217Z",
                        updated_at: "2014-08-22T20:50:37.217Z",
                        field_type: "page_break",
                        cid: null
                    }, {
                        id: 42,
                        form_id: 5,
                        label: "Guess a price...",
                        required: false,
                        blind: false,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "price",
                        cid: null
                    }, {
                        id: 43,
                        form_id: 5,
                        label: "Why so expensive?",
                        field_options: {
                            conditions: [
                                {
                                    response_field_id: 42,
                                    method: 'gt',
                                    value: '5.00'
                                }
                            ]
                        },
                        required: false,
                        blind: false,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "text",
                        cid: null
                    }
                ],
                response: {
                    id: 'xxx',
                    responses: {}
                }
            };
        },
        CONDITIONAL_TWO: function () {
            return {
                project_id: 'dummy_val',
                response_fields: [
                    {
                        id: 35,
                        form_id: 5,
                        label: "Essay 1",
                        field_options: {
                            min_max_length_units: 'words',
                            maxlength: 100
                        },
                        required: false,
                        blind: false,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "paragraph",
                        cid: null
                    }, {
                        id: 36,
                        form_id: 5,
                        label: "Essay 2",
                        field_options: {
                            min_max_length_units: 'characters',
                            maxlength: 100
                        },
                        required: false,
                        blind: false,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "paragraph",
                        cid: null
                    }, {
                        id: 37,
                        form_id: 5,
                        label: "Please elaborate... (lt 10 words)",
                        field_options: {
                            conditions: [
                                {
                                    response_field_id: 35,
                                    method: 'shorter',
                                    value: '10'
                                }
                            ]
                        },
                        required: false,
                        blind: false,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "text",
                        cid: null
                    }, {
                        id: 38,
                        form_id: 5,
                        label: "No more! (gt 10 characters)",
                        field_options: {
                            conditions: [
                                {
                                    response_field_id: 36,
                                    method: 'longer',
                                    value: '10'
                                }
                            ]
                        },
                        required: false,
                        blind: false,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "text",
                        cid: null
                    }
                ],
                response: {
                    id: 'xxx',
                    responses: {}
                }
            };
        },
        CONDITIONAL_THREE: function () {
            return {
                project_id: 'dummy_val',
                response_fields: [
                    {
                        id: 35,
                        form_id: 5,
                        label: "Question 1",
                        field_options: {
                            options: [
                                {
                                    label: 'Yes',
                                    checked: false
                                }, {
                                    label: 'No',
                                    checked: false
                                }
                            ]
                        },
                        required: false,
                        blind: false,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "checkboxes",
                        cid: null
                    }, {
                        id: 36,
                        form_id: 5,
                        label: "Question 2",
                        field_options: {
                            options: [
                                {
                                    label: 'Yes',
                                    checked: false
                                }, {
                                    label: 'No',
                                    checked: false
                                }
                            ]
                        },
                        required: false,
                        blind: false,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "checkboxes",
                        cid: null
                    }, {
                        id: 37,
                        form_id: 5,
                        label: "Why do you like the word 'yes'?",
                        field_options: {
                            conditions: [
                                {
                                    response_field_id: 35,
                                    method: 'contains',
                                    value: 'Yes'
                                }, {
                                    response_field_id: 36,
                                    method: 'contains',
                                    value: 'Yes'
                                }
                            ]
                        },
                        required: false,
                        blind: false,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "text",
                        cid: null
                    }
                ],
                response: {
                    id: 'xxx',
                    responses: {}
                }
            };
        },
        BLANK: function () {
            return {
                project_id: 'dummy_val',
                response_fields: [],
                response: {
                    id: 'xxx',
                    responses: {}
                }
            };
        },
        BLANK_IDENTIFIED: function () {
            return {
                project_id: 'dummy_val',
                response_fields: [
                    {
                        id: 34,
                        form_id: 5,
                        label: "",
                        field_options: {},
                        required: false,
                        blind: false,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "identification",
                        cid: null
                    }
                ],
                response: {
                    id: 'xxx',
                    responses: {}
                }
            };
        },
        FILE: function () {
            return {
                project_id: 'dummy_val',
                response_fields: [
                    {
                        id: 35,
                        form_id: 5,
                        label: "Text",
                        field_options: {
                            file_types: 'images'
                        },
                        required: false,
                        blind: false,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.152Z",
                        updated_at: "2014-08-22T20:50:37.152Z",
                        field_type: "file",
                        cid: null
                    }
                ],
                response: {
                    id: 'xxx',
                    responses: {}
                }
            };
        },
        TABLE: function () {
            return {
                project_id: 'dummy_val',
                response_fields: [
                    {
                        id: 49,
                        form_id: 5,
                        label: "Table",
                        field_options: {
                            columns: [
                                {
                                    label: "column one"
                                }, {
                                    label: "column two"
                                }, {
                                    label: "column three"
                                }, {
                                    label: "column four"
                                }
                            ],
                            minrows: "4",
                            preset_values: {
                                "column one": ['123', '456', '', 'This is more preset row value goodness']
                            }
                        },
                        required: false,
                        blind: false,
                        admin_only: false,
                        created_at: "2014-08-22T20:50:37.524Z",
                        updated_at: "2014-08-22T20:50:37.524Z",
                        field_type: "table",
                        cid: null
                    }
                ],
                response: {
                    id: 'xxx',
                    responses: {}
                }
            };
        }
    };

   
   $(document).ready(function(){       
        if($('.fr_response_field_paragraph textarea').length > 0){
            $('.fr_response_field_paragraph textarea').val(decodeHtml($('.fr_response_field_paragraph textarea').val().replace(/<br\s?\/?>/g,"\n"))); 
        }
        
        $("input[type=text]").each(function(){
            var vl = $(this).val();
            $(this).val(decodeHtml(vl));
        })
        
        if($(".backpage").length > 0 && $("#surveysubmited").length && $("#surveysubmited").val() > 0){

            $(".fr_bottom,.fr_error").remove(); 
            $(".fr_page input,.fr_page textarea,.fr_page select").attr("disabled","disabled");
         }
    
   });

    function decodeHtml(html) {
        var txt = document.createElement("textarea");
        txt.innerHTML = html;
        return txt.value;
    }
</script>

<?php

wp_enqueue_script('store.js', TR_COUNT_PLUGIN_URL . '/assets/js/store.js');
wp_enqueue_script('preview.js', TR_COUNT_PLUGIN_URL . '/assets/js/preview.js');
?>