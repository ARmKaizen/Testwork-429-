(function($) {

    $(document).ready(function() {

        $('#save-post').click(function(){
            // var url = 'https://test.ainuan.kz/wp-admin/post.php';
            var url = ajax_object.post_url;
            console.log(url);
            var data = $('form#post').serializeArray();
            console.log(tinymce.activeEditor.getContent());

            if(tinymce.activeEditor.getContent()){ // пришлось отдельно добавлять так как при редактировании контента обновленная часть не записывалась, надо разобратся почему так ))
                data.push({
                    "name": "content",
                    "value": tinymce.activeEditor.getContent()
                });
            }

            data.push({name: 'save_post_ajax', value: 1});

            var ajax_updated = false;

            $(window).unbind('beforeunload.edit-post');
            $(window).on( 'beforeunload.edit-post', function() {
                var editor = typeof tinymce !== 'undefined' && tinymce.get('content');

                if ( ( editor && !editor.isHidden() && editor.isDirty() ) ||
                    ( wp.autosave && wp.autosave.getCompareString() != ajax_updated) ) {
                    return postL10n.saveAlert;
                }
            });


            $.post(url, data, function(response) {
                if (response.success) {
                    if (typeof tinyMCE !== 'undefined') {
                        for (id in tinyMCE.editors) {
                            var editor = tinyMCE.get(id);
                            // editor.isNotDirty = true; // почему то выдает ошибку Uncaught TypeError: Cannot set properties of null (setting 'isNotDirty')
                        }
                    }
                    ajax_updated = wp.autosave.getCompareString();

                    console.log('Saved post successfully');
                    $('#publishing-action').append( '<div id="local-storage-notice" class="hidden notice is-dismissible notice-success" style="display: block;">Сохранен</div>');
                } else {
                    console.log('ERROR: Server returned false. ',response);
                }
            }).fail(function(response) {
                console.log('ERROR: Could not contact server. ',response);
            });
        });

    });
})(jQuery);