<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


// Add custom action for updating avatar
add_action( 'wp_ajax_nopriv_update_avatar_action', 'update_avatar_action_ajax' );
add_action('wp_ajax_update_avatar_action', 'update_avatar_action_ajax');
function update_avatar_action_ajax() {
    // Check nonce field
    if (!isset($_POST['update_avatar_nonce']) || !wp_verify_nonce($_POST['update_avatar_nonce'], 'update_avatar_nonce')) {
        wp_die('Akses tidak sah.');
    }

    // Check if avatar URL is set
    if (!empty($_POST['avatar_url'])) {
        // Sanitize avatar URL
        $avatar_url = esc_url($_POST['avatar_url']);

        // Update user avatar
        $user_id = get_current_user_id();
        update_user_meta($user_id, 'avatar', $avatar_url);
    }
}

// hapus iklan
add_action('wp_ajax_deleteproduct', 'deleteproduct_ajax');
function deleteproduct_ajax() {
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    if($id){
        wp_delete_post($id);
    }
    wp_die();
}

// pengajuan iklan premium
add_action('wp_ajax_iklanpremium', 'iklanpremium_ajax');
function iklanpremium_ajax() {
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    if($id){
        update_post_meta($id, 'jenis', 'pengajuan' );
    }
    wp_die();
}

// tindakan admin untuk iklan premium
add_action('wp_ajax_konfirmasipremium', 'konfirmasipremium_ajax');
function konfirmasipremium_ajax() {
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $confirm = isset($_POST['confirm']) ? $_POST['confirm'] : '';
    if($id && $confirm == 'Hapus'){
        delete_post_meta( $id, 'jenis');
    } elseif($id && $confirm == 'Terima'){
        update_post_meta($id, 'jenis', 'premium' );
    }
    wp_die();
}

/*
add_action('wp_ajax_optionprovinsi', 'optionprovinsi_ajax');
function optionprovinsi_ajax() {
    $provid     = isset($_POST['prov_id']) ? $_POST['prov_di'] : '';
    $datavalue  = isset($_POST['datavalue']) ? $_POST['datavalue'] : '';
    
    $Lokasi     = new Velocitymp\Lokasi;
    $getdata    = $Lokasi->province($provid);

    echo '<option value="">Pilih Provinsi</option>';
    if($getdata){
        foreach ($getdata as $key => $data) {
            echo '<option value="'.$data['province_id'].'" '.selected($datavalue,$data['province_id']).'>'.$data['province'].'</option>';
        }
    }

    wp_die();
}

add_action('wp_ajax_optionkota', 'optionkota_ajax');
function optionkota_ajax() {
    $provid     = isset($_POST['provid']) ? $_POST['provid'] : '';
    $datavalue  = isset($_POST['datavalue']) ? $_POST['datavalue'] : '';

    $Lokasi     = new Velocitymp\Lokasi;
    $getdata    = $Lokasi->city();

    echo '<option value="">Pilih Kota/Kabupaten</option>';
    if($getdata){
        foreach ($getdata as $key => $data) {
            if($provid === $data['province_id']) {
                $type = $data['type'];
                echo '<option value="'.$data['city_id'].'" '.selected($datavalue,$data['city_id']).'>'.$type.' '.$data['city_name'].'</option>';
            }
        }
    }

    wp_die();
}

add_action('wp_ajax_optionkecamatan', 'optionkecamatan_ajax');
function optionkecamatan_ajax() {
    $cityid     = isset($_POST['cityid']) ? $_POST['cityid'] : '';
    $datavalue  = isset($_POST['datavalue']) ? $_POST['datavalue'] : '';

    $Lokasi     = new Velocitymp\Lokasi;
    $getdata    = $Lokasi->subdistrictbycity($cityid);

    echo '<option value="">Pilih Kecamatan</option>';
    if($getdata){
        foreach ($getdata as $key => $data) {
            echo '<option value="'.$data['subdistrict_id'].'" '.selected($datavalue,$data['subdistrict_id']).'>'.$data['subdistrict_name'].'</option>';
        }
    }

    wp_die();
}


add_action('wp_ajax_loveproduct', 'loveproduct_ajax');
function loveproduct_ajax() {
    $idpost = isset($_POST['idpost']) ? $_POST['idpost'] : '';
    $iduser = isset($_POST['iduser']) ? $_POST['iduser'] : '';
    $icon   = '<i class="fa fa-heart-o"></i>';

    if($idpost && $iduser){
        $userlove   = get_user_meta($iduser,'love_products',true);
        $userlove   = $userlove?$userlove:[];
        $postlove   = get_post_meta($idpost,'love_users',true);
        $postlove   = $postlove?$postlove:[];

        if (in_array($idpost, $userlove)) {
            if (($key = array_search($idpost, $userlove)) !== false) {
                unset($userlove[$key]);
            }
            if (($key = array_search($iduser, $postlove)) !== false) {
                unset($postlove[$key]);
            }
        } else {
            array_push($userlove,$idpost);
            array_push($postlove,$iduser);
            $icon = '<i class="fa fa-heart text-danger"></i>';
        }

        //update
        update_user_meta( $iduser, 'love_products', $userlove );
        update_post_meta( $idpost, 'love_users', $postlove );
    } 

    echo $icon;

    wp_die();
}
*/