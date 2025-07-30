jQuery(document).ready(function($) {
    
    $('#upload-avatar-button').on('click', function() {
        var customUploader = wp.media({
            title: 'Pilih Foto',
            button: {
                text: 'Pilih'
            },
            multiple: false
        });

        customUploader.on('select', function() {
            var attachment = customUploader.state().get('selection').first().toJSON();
            $('#avatar-url').val(attachment.url);
            $('#preview-avatar').html('<img src="' + attachment.url + '" class="rounded rounded-circle">');
            var update_avatar_nonce = $('#update_avatar_nonce').val();
            if (attachment.url) {
                $.ajax({
                    url: velocityiklan.ajaxurl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'update_avatar_action',
                        avatar_url: attachment.url,
                        update_avatar_nonce: update_avatar_nonce
                    },
                    success: function(response) {
                        alert('Avatar berhasil diperbarui.');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert('Kesalahan AJAX: ' + textStatus + ' - ' + errorThrown);
                    }
                });
            }
        });

        customUploader.open();
    });
});
