(function () {
    jQuery('body').on('click','.js-alerted',function () {
        let size_inp = jQuery('.from-trumb input').size();
        jQuery('.from-trumb').append('<input name=file_' + size_inp + ' type="file"><br>');
        return false;
    });
})(jQuery);