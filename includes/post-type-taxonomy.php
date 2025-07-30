<?php

// Register New post type & taxonomy
add_action('init', 'velocity_ikaln_post_type_taxonomy');
function velocity_ikaln_post_type_taxonomy() {
    register_post_type('iklan', array(
        'labels' => array(
            'name' => 'Iklan',
            'singular_name' => 'iklan',
            'add_new' => 'Tambah Iklan Baru',
            'add_new_item' => 'Tambah Iklan Baru',
            'edit_item' => 'Ubah Iklan',
            'view_item' => 'Lihat Iklan',
            'search_items' => 'Cari iklan',
            'not_found' => 'Tidak ditemukan iklan',
            'not_found_in_trash' => 'Tidak ada iklan di kotak sampah'
        ),
        'menu_icon' => 'dashicons-welcome-widgets-menus',
        'public' => true,
        'has_archive' => true,
        'rewrite' => true,
        'capability_type' => 'post',
        'taxonomies' => array('kategori','lokasi'),
        'supports' => array(
            'title',
            'editor',
            'comments',
            'thumbnail',
			'author',
        ),
   ));
	register_taxonomy(
		'kategori',
		array('iklan'),
		array(
			'label' => __( 'Kategori Iklan' ),
			'rewrite' => array( 'slug' => 'kategori' ),
			'hierarchical' => true,
		)
	);
	register_taxonomy(
		'lokasi',
		array('iklan'),
		array(
			'label' => __( 'Lokasi Iklan' ),
			'rewrite' => array( 'slug' => 'lokasi' ),
			'hierarchical' => true,
		)
	);
}

// Tambahkan kolom ke halaman admin untuk menampilkan taxonomy
function custom_columns_head($defaults) {
    $new_columns = array();
    foreach ($defaults as $key => $value) {
        $new_columns[$key] = $value;
        if ($key == 'title') {
            $new_columns['kategori'] = 'Kategori';
            $new_columns['lokasi'] = 'Lokasi';
        }
    }
    return $new_columns;
}
add_filter('manage_iklan_posts_columns', 'custom_columns_head');

function custom_columns_content($column_name, $post_id) {
    if ($column_name == 'kategori') {
        $terms = get_the_terms($post_id, 'kategori');
        if ($terms) {
            $terms_array = array();
            foreach ($terms as $term) {
                $terms_array[] = $term->name;
            }
            echo implode(', ', $terms_array);
        } else {
            echo 'Tidak ada';
        }
    }
    if ($column_name == 'lokasi') {
        $terms = get_the_terms($post_id, 'lokasi');
        if ($terms) {
            $terms_array = array();
            foreach ($terms as $term) {
                $terms_array[] = $term->name;
            }
            echo implode(', ', $terms_array);
        } else {
            echo 'Tidak ada';
        }
    }
}
add_action('manage_iklan_posts_custom_column', 'custom_columns_content', 10, 2);


// Register New Page
add_action( 'init', 'velocity_iklan_create_page' );
function velocity_iklan_create_page() {
    $new_pages = array(
        array(
            'slug'      => 'akun-saya',
            'title'     => 'Akun Saya',
            'content'   => '[velocity-iklan-profile]'
        ),
    );
    foreach ($new_pages as $page) {
        if ( null == get_page_by_path($page['slug']) ) {
            wp_insert_post(
                array(
                    'comment_status'    => 'closed',
                    'ping_status'       => 'closed',
                    'post_author'       => 1,
                    'post_name'         => $page['slug'],
                    'post_title'        => $page['title'],
                    'post_content'      => $page['content'],
                    'post_status'       => 'publish',
                    'post_type'         => 'page',
                    'page_template'     => 'page-templates/empty.php'
                )
            );
        }
    }
}


//register iklan template
add_filter( 'template_include', 'velocityiklan_register_template' );
function velocityiklan_register_template( $template ) {
    if ( is_post_type_archive('iklan') ) {
        $template = VELOCITY_IKLAN_PLUGIN_DIR . 'public/templates/archive-iklan.php';
    }
    if ( is_tax('kategori') || is_tax('lokasi') ) {
        $template = VELOCITY_IKLAN_PLUGIN_DIR . 'public/templates/archive-kategori-lokasi.php';
    }
    if ( is_author() ) {
        $template = VELOCITY_IKLAN_PLUGIN_DIR . 'public/templates/author.php';
    }
    return $template;
}


add_filter('post_class', 'velocity_filter_post_class', 10, 3);
function velocity_filter_post_class($classes, $class, $post_id) {
    // Ganti class "type-iklan" menjadi "type-velocity-mp" agar tidak terdeteksi AdBlock
    $key = array_search('type-iklan', $classes);
    if ($key !== false) {
        $classes[$key] = 'type-velocity-mp';
    }

    // Kamu juga bisa hilangkan class "iklan" jika muncul
    $key2 = array_search('iklan', $classes);
    if ($key2 !== false) {
        unset($classes[$key2]);
        // Atau bisa diganti jadi aman:
         $classes[$key2] = 'velocity-mp';
    }

    return $classes;
}