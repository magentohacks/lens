define([
    'jquery',
    'jquery/ui',
    'zebraTooltips'
], function($) {
    "use strict";

    $.widget('magestore.toolTip', {
        options: {
            max_width: 500,
            content: '',
            background_color: '#4C9ED9'
        },

        _create: function() {
            $.Zebra_Tooltips($(this.element), this.options);
        },
    });
    return $.magestore.toolTip;
});
