(function($) {
    var resizeEle = $('#scribiteeditor_ckeditor_resizemode');
    var agDetails = $('#sm_autogrow_details');
    var rsDetails = $('#sm_resize_details');
    $(document).ready(function() {
        switch (resizeEle.val()) {
            case 'resize':
                agDetails.addClass('hidden');
                break;
            case 'autogrow':
                rsDetails.addClass('hidden');
                break;
            case 'noresize':
                rsDetails.addClass('hidden');
                agDetails.addClass('hidden');
                break;
        }

        resizeEle.change(sm_sizing_onchange);
    });

    function sm_sizing_onchange()
    {
        switch (resizeEle.val()) {
            case 'resize':
                rsDetails.removeClass('hidden');
                agDetails.addClass('hidden');
                break;
            case 'autogrow':
                rsDetails.addClass('hidden');
                agDetails.removeClass('hidden');
                break;
            case 'noresize':
                rsDetails.addClass('hidden');
                agDetails.addClass('hidden');
                break;
        }
    }
})(jQuery);
