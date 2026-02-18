define([
    'jquery',
    'jeditable',
    'jquery/file-uploader',
], function($) {
    $.editable.addInputType('ajaxupload-logo', {
        /* create input element */
        element: function (settings) {
            settings.onblur = 'ignore';
            var input = $('<input type="file" id="' + settings.id + '" data-url="' + settings.imageUploadUrl+'" name="image" multiple="multiple" />');
            var inputFormkey = $('<input type="hidden" value="" name="form_key" />');
            $(this).append(input);
            $(this).append(inputFormkey);
            return (input);
        },
        content: function (string, settings, original) {
            /* do nothing */
        },
        plugin: function (settings, original) {
            var form = this, inputImage = $(form).find('input[type="file"]');
            form.attr("enctype", "multipart/form-data");
            $(form).find('input[name="form_key"]').val(window.form_key);
            inputImage.fileupload({
                dataType: 'json',
                acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
                maxFileSize: 100,
                done: function(event, data) {
                    if(data.result && data.result.url) {
                        $(original).find('.autogrow-cancel').click();
                        $(original).html('<img width="160" src="' + data.result.url + '"/>');
                    }
                },
                fail: function (event, data) {
                    alert('fail');
                },
            });
            $("button.autogrow-submit", form).remove();
        }
    });
});