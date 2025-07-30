<div class="my-4 row">
    <div class="col-sm-4 col-md-3">
        <?php require_once VELOCITY_IKLAN_PLUGIN_DIR . 'public/partials/user-foto-profil.php'; ?>
    </div>
    <div class="col-sm-8 col-md-9">
    <?php
        // Periksa jika formulir disubmit
        if (isset($_POST['update_account'])) {
            // Mendapatkan ID pengguna yang sedang masuk
            $current_user_id = get_current_user_id();

            // Mengambil data dari formulir
            $user_password = $_POST['user_password'];
            $confirm_password = $_POST['confirm_password'];
            $new_user_email = sanitize_email($_POST['new_user_email']);
            $first_name = sanitize_text_field($_POST['first_name']);
            $description = sanitize_text_field($_POST['description']);
            $alamat = sanitize_text_field($_POST['alamat']);
            $phone = sanitize_text_field($_POST['phone']);
            $phone = preg_replace("/[^0-9]/", "", $phone);

            $error = '';
            // Periksa apakah password baru diisi
            if (!empty($user_password)) {
                // Periksa apakah password baru dan pengulangan password sama
                if ($user_password == $confirm_password) {
                    // Mengupdate kata sandi pengguna
                    wp_set_password($user_password, $current_user_id);
                } else {
                    $error = 'Password dan pengulangan password tidak cocok.';
                }
            }

            // Periksa apakah alamat email baru diisi
            if (!empty($new_user_email)) {
                // Periksa apakah alamat email sudah terpakai
                if (email_exists($new_user_email) && email_exists($new_user_email) != $current_user_id) {
                    $error = 'Email sudah terdaftar oleh pengguna lain.';
                } else {
                    // Mengupdate alamat email pengguna
                    wp_update_user(array('ID' => $current_user_id, 'user_email' => $new_user_email));
                }
            }

            if(empty($error)){
                // Mengupdate nama pengguna
                wp_update_user(array('ID' => $current_user_id, 'first_name' => $first_name, 'display_name' => $first_name));

                // Mengupdate user
                update_user_meta($current_user_id, 'phone', $phone);
                update_user_meta($current_user_id, 'description', $description);
                update_user_meta($current_user_id, 'alamat', $alamat);

                echo '<div class="alert alert-success">Informasi pengguna telah berhasil diperbarui.</div>';
            } elseif($error) {
                echo '<div class="alert alert-danger">'.$error.'</div>';
            }
        }

        // Mendapatkan informasi pengguna saat ini
        $current_user = wp_get_current_user();
        $user_bio = get_user_meta($current_user->ID, 'description', true);
        $user_alamat = get_user_meta($current_user->ID, 'alamat', true);

        // Tampilkan formulir pengaturan akun
        ?>
        <form method="post" action="" id="update-account-form">

            <div class="mb-3">
                <label for="user_login" class="form-label">Username:</label>
                <input type="text" value="<?php echo esc_attr($current_user->user_login); ?>" class="form-control" disabled>
                <small>Username tidak dapat diganti</small>
            </div>

            <div class="mb-3">
                <label for="first_name" class="form-label">Nama:</label>
                <input type="text" name="first_name" value="<?php echo esc_attr($current_user->first_name); ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Telepon:</label>
                <input type="text" name="phone" value="<?php echo esc_attr(get_user_meta($current_user->ID, 'phone', true)); ?>" class="form-control" required>
                <small>Hanya angka saja, contoh: <strong>08123456789</strong></small>
            </div>

            <div class="mb-3">
                <label for="new_user_email" class="form-label">Email:</label>
                <input type="email" name="new_user_email" value="<?php echo esc_attr($current_user->user_email); ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi:</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?php echo esc_textarea($user_bio); ?></textarea>
            </div>

            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat Lengkap:</label>
                <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?php echo esc_textarea($user_alamat); ?></textarea>
            </div>

            <div class="mb-3">
                <label for="user_password" class="form-label">Password Baru:</label>
                <input type="password" name="user_password" class="form-control">
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Konfirmasi Password Baru:</label>
                <input type="password" name="confirm_password" class="form-control">
                <small id="password-message"></small>
            </div>

            <button type="submit" name="update_account" class="btn btn-primary">Update Account</button>
        </form>
    </div>
</div>

<script>
    jQuery(function($) {
        $(document).ready(function() {
            $('#update-account-form input[name="confirm_password"]').keyup(function() {
                var password = $('#update-account-form input[name="user_password"]').val();
                var confirmPassword = $(this).val();

                if (password !== confirmPassword) {
                    $('#password-message').html('<span class="text-danger">Password tidak cocok.</span>');
                } else {
                    $('#password-message').html('<span class="text-success">Password cocok.</span>');
                }
            });
        });
    });
</script>