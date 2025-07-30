<?php 
wp_enqueue_media();
wp_enqueue_script( 'viklanuploadavatar', VELOCITY_IKLAN_PLUGIN_URL . 'public/js/avatar-upload.js', array('jquery', 'media-editor'), '1.0', true);
$avatar_url = get_user_meta(get_current_user_id(), 'avatar', true);
if($avatar_url){
    $profile_picture = '<img class="rounded rounded-circle" src="'.$avatar_url.'" />';
} else {
    $profile_picture = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="text-black-50 p-4 bi bi-image" viewBox="0 0 16 16"><path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/><path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1z"/></svg>';
}
?>

<div class="card">
    <div class="card-body text-center">
        <?php wp_nonce_field('update_avatar_nonce', 'update_avatar_nonce'); ?>
        <input type="hidden" id="avatar-url" name="avatar_url">
        <div id="preview-avatar" class="mb-3 ratio ratio-1x1">
            <?php echo $profile_picture; ?>
        </div>
        <input type="button" class="btn btn-primary btn-sm mb-0" id="upload-avatar-button" value="Ubah Foto Profil">
    </div>
</div>