//http://www.discussdesk.com/how-to-open-bootstrap-modal-popup-on-ajax-click-function.htm#sthash.junUqVEs.dpuf
jQuery(document).ready(function ($) {
    var $modal = $('#load_popup_modal_show_id');
    $('body').on('click', '.modal_popup', function (e) {
        e.preventDefault();
        id = $(this).data('id');
        $.ajax({
            url: loadmodalObj.ajax_url,
            type: 'POST',
            data: {
                action: 'load_modal_ajax',
                id: id
            },
            dataType: 'html',
            success: function (response) {
                $modal.modal('show');
                $('#load_popup_modal_show_id').html(response);
            }
        });
    });
});