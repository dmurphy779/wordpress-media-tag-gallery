//http://www.discussdesk.com/how-to-open-bootstrap-modal-popup-on-ajax-click-function.htm#sthash.junUqVEs.dpuf
jQuery(document).ready(function ($) {

    $('body').on('click', 'button.load-more-media', function () {
        if ($('.no-media').length) {
            $(this).addClass('disabled');
            $(this).remove();
        } else {
            var mediatag_group_inner = $(this).parents('.mediatag-group-wrapper').find('.mediatag-group-inner');
            var offset = $('> div', mediatag_group_inner).length;
            var tagNames = $(this).parents('.mediatag-group-wrapper').data('tag-group');

            $('.dynamic-total').attr('data-total', offset);//remove after this is tested
            $.ajax({
                url: loadmoremediaObj.ajax_url,
                type: 'POST',
                data: {
                    action: 'load_more_media_ajax',
                    offset: offset,
                    tagNames: tagNames
                },
                dataType: 'html',
                success: function (response) {
                    mediatag_group_inner.append(response);
                }
            });

        }
    });
});

