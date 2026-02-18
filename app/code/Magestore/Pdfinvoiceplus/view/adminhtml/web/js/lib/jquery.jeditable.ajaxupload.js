define([
    'jquery',
    'jeditable',
    'jquery/file-uploader',
], function($) {
    $.editable.addInputType('ajaxupload', {
        /* create input element */
        element: function (settings) {
            settings.onblur = 'ignore';
            var input = $('<input type="file" id="' + settings.id + '" data-url="' + settings.imageUploadUrl+'" name="image" multiple="multiple" />');
            var inputFormkey = $('<input type="hidden" value="" name="form_key" />');
            $(this).append(input);
            $(this).append(inputFormkey);
            if (settings.id == 'change-background') {
                var deletebutton = $('<input style="margin-left:10px;color:red" type="button" id="delete_' + settings.id + '" value="Delete"/>');
                deletebutton.click(function () {
                    $('#container-inner').css('background-image', 'none');
                });
                $(this).append(deletebutton);
            }
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
                    if ($(original).hasClass('changebackground') == true) {
                        if(data.result && data.result.url) {
                            $('#container-inner').css('background-image', 'url("' + data.result.url + '")');
                        }
                    } else {
                        if(data.result) {
                            $(original).html(data.result);
                        }
                        original.editing = false;
                    }
                },
                add: function(event, data) {
                    $(this).fileupload('process', data).done(function() {
                        data.submit();
                    });
                },
                progress: function(e, data) {
                    //var progress = parseInt(data.loaded / data.total * 100, 10);
                    //$dropPlaceholder.find('.progress-bar').addClass('in-progress').text(progress + '%');
                },
                start: function(event) {
                    //var uploaderContainer = $(event.target).closest('.image-placeholder');

                    //uploaderContainer.addClass('loading');
                    //loaderImage.show();
                },
                stop: function(event) {
                    //var uploaderContainer = $(event.target).closest('.image-placeholder');

                    //uploaderContainer.removeClass('loading');
                    //loaderImage.hide();
                }
            });
            $("button.autogrow-submit", form).remove();
            //$("button:submit", form).bind('click', function () {

                //$(".message").show();
                //$.ajaxFileUpload({
                //    url: settings.target,
                //    secureuri: false,
                //    fileElementId: settings.id,
                //    dataType: 'html',
                //    success: function (data, status) {
                //        if ($(original).hasClass('changebackground') == true) {
                //            if (data) {
                //                $('#container-inner').css('background-image', 'url("' + data + '")');
                //            }
                //        } else {
                //            if (data) {
                //                $(original).html(data);
                //                original.editing = false;
                //            }
                //        }
                //    },
                //    error: function (data, status, e) {
                //        alert(e);
                //    }
                //});
                //return (false);
            //});
        }
    });
});