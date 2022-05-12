jQuery( document ).ready( function( $ ) {
    if(!$('#save-post').length > 0){
        // $('#publishing-action').append( '<input type="button"  class="button-primary" id="save-post" value="SAVE">');
        $('#publish').replaceWith('<input type="button"  class="button-primary" id="save-post" value="SAVE">');
    }
});


jQuery(document).ready( function($) {

    $('#remove_img').click(function () {
        Delete_Image($('#custom_image_id').val(),$("#post_ID").val());
    })

    $('#clear_custom_fields').click(function () {

        $('#_date').val('');
        $('#_custom_product_select').val('');

    });

    jQuery('input#custom_media_manager').click(function(e) {

        e.preventDefault();
        var image_frame;
        if(image_frame){
            image_frame.open();
        }

        image_frame = wp.media({
            title: 'Select Media',
            multiple : false,
            library : {
                type : 'image',
            }
        });

        image_frame.on('close',function() {

            var selection =  image_frame.state().get('selection');
            var gallery_ids = new Array();
            var my_index = 0;
            selection.each(function(attachment) {
                gallery_ids[my_index] = attachment['id'];
                my_index++;
            });
            var ids = gallery_ids.join(",");
            jQuery('input#custom_image_id').val(ids);
            Refresh_Image(ids);
        });

        image_frame.on('open',function() {

            var selection =  image_frame.state().get('selection');
            var ids = jQuery('input#custom_image_id').val().split(',');
            ids.forEach(function(id) {
                var attachment = wp.media.attachment(id);
                attachment.fetch();
                selection.add( attachment ? [ attachment ] : [] );
            });

        });

        image_frame.open();
    });

});


function Refresh_Image(the_id){
    var data = {
        action: 'custom_get_image',
        id: the_id
    };

    jQuery.get(ajaxurl, data, function(response) {

        if(response.success === true) {
            jQuery('#custom-preview-image').replaceWith( response.data.image );
        }
    });
}

function Delete_Image(the_id, post_id){

    var data = {
        action: 'delete_image',
        id: the_id,
        postid: post_id

    };

    jQuery.get(ajaxurl, data, function(response) {

        if(response.success === true) {
            console.log(response);
            Refresh_Image(4)
        }
    });
}
