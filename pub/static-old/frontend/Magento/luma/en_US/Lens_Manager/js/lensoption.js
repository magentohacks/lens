define([
    "jquery",
    'Magento_Ui/js/modal/alert',
    "jquery/ui",
    'mage/cookies',
], function ($, alert) {
    'use strict';
    $.widget('mage.lensoption', {
        _create: function () {
            var self = this;
            var options = self.options.optionsData;
            var inventoryStatus = false;
            $(document).ready(function() {
                $('.product-options-bottom').after($(".time-to-free-delivery"));
                tick();
            })
            $('select').on('change', function(){
                var optionId = $(this).attr('id').split('_')[1];
                var side = $(this).attr('data-side');
                var value = this.value;
                $(this).closest('li').next().find('select').removeAttr('disabled');
                updateOptions(optionId, value, side);
            });
            $('select').on('change', function(){
                $(this).closest('li').next().find('select').val('');
                $(this).closest('li').nextAll(":gt(0)").find('select').val('').prop('disabled','disabled');
            })
            
            function updateOptions(optionId, value, side) {
                var arrayToUpdate = options[optionId];
                var valuesToUpdate = arrayToUpdate[value];
                if (!$.isEmptyObject(valuesToUpdate)) {
                    $('#'+side+'_main #select_'+optionId).closest('li').nextAll().find('select option:gt(0)').remove();
                    $.each(valuesToUpdate, function(index, value){
                        $.each(value, function(optionValue, optionText){
                            $('#'+side+'_main #select_'+index).append(`<option value="${optionValue}">${optionText}</option>`);
                        })
                    });
                }
            }
            $('#left_checked').click(function(){
                if ($(this).prop("checked") == false) {
                    reset('left');
                } else if ($(this).prop("checked") == true) {
                    $("#left_main").find("select:nth(0)").removeAttr('disabled');
                }
            });
            $('#right_checked').click(function(){
                if ($(this).prop("checked") == false) {
                    reset('right');
                } else if ($(this).prop("checked") == true) {
                    $("#right_main").find("select:nth(0)").removeAttr('disabled');
                }
            });

            function reset(side) {
                $("#"+side+"_main").find("select").prop('disabled', 'disabled');
                $("#"+side+"_main").find("select").val('');
                // $("#"+side+"_main").find("select:gt(0) option").remove();
                // $("#"+side+"_main").find("select:gt(0)").append("<option>-- Please Select --</option>");
            }

            function checkProductAvailability() {
                var selectedData = getSelectedOptionData();
                $.ajax({
                    showLoader: true,
                    type: "POST",
                    dataType: "json",
                    url: self.options.url,
                    data: {
                        'selectedData': selectedData,
                        'productId' : $("input[name=product]").val(),
                        'form_key': $.mage.cookies.get('form_key'),
                    },
                    cache: false,
                    success: function(data){
                        inventoryStatus = data.status;
                    }
                });
            }

            function getSelectedOptionData()
            {
                var selectedOptionsData = {};
                selectedOptionsData['rightEye'] = {};
                selectedOptionsData['leftEye'] = {};
                var isRightChecked = $('#right_checked').is(':checked');
                var isLeftChecked = $('#left_checked').is(':checked');
                if (isRightChecked) {
                    $('#right_main select').each(function() {
                        selectedOptionsData['rightEye'][$(this).attr('data-label')] = $(this).find('option:selected').text();
                    });
                }
                if (isLeftChecked) {
                    $('#left_main select').each(function() {
                        selectedOptionsData['leftEye'][$(this).attr('data-label')] = $(this).find('option:selected').text();
                    });
                }
                return selectedOptionsData;
            }

            $(".quantity-manager .add-action").click(function(){
                if ($(this).hasClass('add-up') ) {
                    if (parseInt($(this).parent().parent().find('.quantity-number input.qty').val()) < 12) {
                        $(this).parent().parent().find('.quantity-number input.qty').val(parseInt($(this).parent().parent().find('.quantity-number input.qty').val()) + 1);
                    }
                } else {
                    if (parseInt($(this).parent().parent().find('.quantity-number input.qty').val()) > 1) {
                        $(this).parent().parent().find('.quantity-number input.qty').val(parseInt($(this).parent().parent().find('.quantity-number input.qty').val()) - 1);
                    }
                }
            })

            jQuery('#right_checked').click(function () {
                updateMessage();
            })
            jQuery('#left_checked').click(function () {
                updateMessage();
            })
            jQuery("#right_main select").on('change', function(){
                updateMessage();
            })
            jQuery("#left_main select").on('change', function(){
                updateMessage();
            })        
            function updateMessage() {
                var rightFlag = true;
                var leftFlag = true;
                var isRightChecked = jQuery('#right_checked').is(':checked');
                var isLeftChecked = jQuery('#left_checked').is(':checked');
                if (isRightChecked) {
                    jQuery('#right_main select').each(function() {
                        if (jQuery(this).val() == "") {
                            rightFlag = false;
                            return false;
                        }
                    });
                } else {
                    rightFlag = true;
                }
                if (isLeftChecked) {
                    jQuery('#left_main select').each(function() {
                        if (jQuery(this).val() == "") {
                            leftFlag = false;
                            return false;
                        }
                    });
                } else {
                    leftFlag = true;
                }
                if (!isRightChecked && !isLeftChecked) {
                    jQuery('#demo').addClass('do_not_show');
                    jQuery(".warning_message").removeClass('do_not_show');
                } else {
                    if (rightFlag && leftFlag) {
                        checkProductAvailability();
                        jQuery('#demo').removeClass('do_not_show');
                        jQuery(".warning_message").addClass('do_not_show');
                    } else {
                        jQuery('#demo').addClass('do_not_show');
                        jQuery(".warning_message").removeClass('do_not_show');
                    }
                }
            }
            var start = new Date;
            var date = new Date();
            var todayDay = date.getDay();
            if (todayDay == 6) {
                start.setHours(12, 0, 0);	// 12 noon
            } else if (todayDay != 0) {
                start.setHours(17, 0, 0); // 5pm
            } else {
                start.setHours(0, 0, 0); // midnight
            }
            function pad(num) {
                return ("0" + parseInt(num)).substr(-2);
            }

            function tick() {
                var showFlag = false;
                var now = new Date;
                var remain = ((start - now) / 1000);
                var hh = pad((remain / 60 / 60) % 60);
                var mm = pad((remain / 60) % 60);
                var ss = pad(remain % 60);
                var stockStatus = "<p><span class='hemag_stock_status'>-- In Stock --</span></p>";
                var dispatchToday = "<span class='hj_normal_text'>Order within </span> <span class='hj_time_text'>"+ hh + ":" + mm + ":" + ss + "</span><span class='hj_normal_text'> for Dispatch Today</span>";
                if (todayDay == 0 || todayDay == 6) {
                    var dispatchTomorrow = "<span class='hj_normal_text'>This Order will be Dispatched on Monday</span>";
                } else {
                    var dispatchTomorrow = "<span class='hj_normal_text'>This Order will be Dispatched Tomorrow</span>";
                }
                var dispatchLater = "<span class = 'dispatch_later'>The product will be dispatched in</span></br><span> 1-2 working days</span>"
                console.log(inventoryStatus);
                if (inventoryStatus) {
                    if (now < start) {
                        var messageString = stockStatus + dispatchToday;
                    } else {
                        var messageString = stockStatus + dispatchTomorrow;
                    }
                } else {
                    var messageString = dispatchLater;
                }
                $('#demo').html(messageString);
                setTimeout(tick, 1000);
            }
            document.addEventListener('DOMContentLoaded', tick);
        }
    });
    return $.mage.lensoption;
});