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
            $('select').on('change', function() {
                if (this.value != "") {
                    var optionId = $(this).attr('id').split('_')[1];
                    var side = $(this).attr('data-side');
                    var value = this.value;
                    $(this).closest('li').next().find('select').removeAttr('disabled');
                    updateOptions(optionId, value, side);
                } else {
                    $(this).closest('li').next().find('select').val('');
                    $(this).closest('li').nextAll().find('select').val('').prop('disabled','disabled');
                }                
            });

            $('select').on('change', function() {
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
                    $("#left_main").find('ul.options').removeClass('no_display');
                    $("#left_main").find('.styledSelect').removeClass('disabled');
                }
            });
            $('#right_checked').click(function(){
                if ($(this).prop("checked") == false) {
                    reset('right');
                } else if ($(this).prop("checked") == true) {
                    $("#right_main").find("select:nth(0)").removeAttr('disabled');
                    $("#right_main").find('ul.options').removeClass('no_display');
                    $("#right_main").find('.styledSelect').removeClass('disabled');

                }
            });
            function reset(side) {
                $("#"+side+"_main").find("select").prop('disabled', 'disabled');
                $("#"+side+"_main").find("select").val('');
                $("#"+side+"_main").find('ul.options').addClass('no_display');
                $("#"+side+"_main").find('.styledSelect').text('-- Please Select --');
                $("#"+side+"_main").find('.styledSelect').addClass('disabled');

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
            $('#right_checked').click(function () {
                updateMessage();
            })
            $('#left_checked').click(function () {
                updateMessage();
            })
            $("#right_main select").on('change', function(){
                updateMessage();
            })
            $("#left_main select").on('change', function(){
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
                } 
                // else {
                    // if (rightFlag && leftFlag) {
                    //     checkProductAvailability();
                    //     jQuery('#demo').removeClass('do_not_show');
                    //     jQuery(".warning_message").addClass('do_not_show');
                    // } else {
                    //     jQuery('#demo').addClass('do_not_show');
                    //     jQuery(".warning_message").removeClass('do_not_show');
                    // }
                // }
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
            $('select.product-custom-option.attr-power').each(function() {

                // Cache the number of options
                var $this = $(this),
                side = $(this).attr('data-side'),
                numberOfOptions = $(this).children('option').length;

                // Hides the select element
                $this.addClass('s-hidden');

                // Wrap the select element in a div
                $this.wrap('<div class="select"></div>');

                // Insert a styled div to sit over the top of the hidden select element
                $this.after('<div class="styledSelect"></div>');

                // Cache the styled div
                var $styledSelect = $this.next('div.styledSelect');

                // Show the first select option in the styled div
                $styledSelect.text($this.children('option').eq(0).text());

                // Insert an unordered list after the styled div and also cache the list
                var $list = $('<ul />', {
                    'class': 'options '+side
                }).insertAfter($styledSelect);


                // Insert a list item into the unordered list for each select option
                for (var i = 0; i < numberOfOptions; i++) {
                    // $('<span />', {
                    // }).appendTo($list);
                    var optionText = $this.children('option').eq(i).text();
                    var optionValue = $this.children('option').eq(i).val();
                    var id = Math.abs(parseFloat(optionText));
                    if (id != NaN) {
                        if ($('ul.'+side+' span[title|="'+id).length) {
                            if (parseFloat(optionText) >= 0) {
                               
                                $('ul.'+side+' span[title|="'+id).append('<li class="pos" rel="'+optionValue+'">'+optionText+'</li>');
                            } else {
                                
                                $('ul.'+side+' span[title|="'+id).prepend('<li class="neg" rel="'+optionValue+'">'+optionText+'</li>');
                            }
                        } else {
                            $('ul.'+side).append('<span title="'+id+'"></span>');
                            if (parseFloat(optionText) >= 0) {
                                
                                $('ul.'+side+' span[title|="'+id).append('<li class="pos" rel="'+optionValue+'">'+optionText+'</li>');
                            } else {
                                
                                $('ul.'+side+' span[title|="'+id).prepend('<li class="neg" rel="'+optionValue+'">'+optionText+'</li>');
                            }
                        }
                    }

                }

                // Cache the list items ()
                var $listItems = $list.children('li');

                // Show the unordered list when the styled div is clicked (also hides it if the div is clicked again)
                $styledSelect.click(function(e) {
                    e.stopPropagation();
                    if ($(this).hasClass('active')) {
                        $(this).removeClass('active');
                        $(this).next('ul.options').hide();
                    } else {
                        $(this).addClass('active');
                        $(this).next('ul.options').show();
                    }
                });

                // Hides the unordered list when a list item is clicked and updates the styled div to show the selected list item
                // Updates the select element to have the value of the equivalent option
                $(document).on('click', 'ul.options span li', function(e) {    
                    e.stopPropagation();
                    $(this).parent().parent().parent().find('select').val($(this).attr('rel'));
                    var optionId = $(this).parent().parent().parent().find('select').attr('id').split('_')[1];
                    var side = $(this).parent().parent().parent().find('select').attr('data-side');
                    console.log(side);
                    $('#'+side+'_main .styledSelect').text($(this).text()).removeClass('active');
                    var value = $(this).attr('rel');
                    if (value != "") {
                        $(this).parents('li').next().find('select').removeAttr('disabled');
                        updateOptions(optionId, value, side);

                    } else {
                        $(this).parents('li').next().find('select').val('');
                        $(this).parents('li').nextAll().find('select').val('').prop('disabled','disabled');
                    }
                    $list.hide();
                });

                // Hides the unordered list when clicking outside of it
                $(document).click(function() {
                    $styledSelect.removeClass('active');
                    $list.hide();
                });

            });
            $(document).ready(function(){
                var titles = [],
                    elements = [];
                $('ul.options.left span').each(function(i, item) {
                    var title = parseFloat($(this).attr('title'));
                    if (!title) {
                    } else {
                        titles[i] = title;
                        elements[title] = $(this).remove().clone();
                    }
                });
                titles = titles.sort(function(a, b){return a-b});
                console.log(titles);
                $.each(titles,function(i, item) {
                    $('ul.options.left').append(elements[item]);
                });
            });

            $(document).ready(function(){
                var titles = [],
                    elements = [];
                $('ul.options.right span').each(function(i, item) {
                    var title = parseFloat($(this).attr('title'));
                    if (!title) {
                    } else {
                        titles[i] = title;
                        elements[title] = $(this).remove().clone();
                    }
                });
                titles = titles.sort(function(a, b){return a-b});
                console.log(titles);
                $.each(titles,function(i, item) {
                    $('ul.options.right').append(elements[item]);
                });
            });

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