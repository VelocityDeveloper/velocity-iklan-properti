<?php
defined('ABSPATH') || exit;

//[velocity-iklan-ratio-image size="large" ratio="16:9"]
add_shortcode('velocity-iklan-ratio-image', 'velocity_iklan_ratio_image');
function velocity_iklan_ratio_image($atts) {
	global $post;
    $atribut = shortcode_atts( array(
        'size'      => 'large', // thumbnail, medium, large, full
        'ratio'     => '16:9', // 16:9, 8:5, 4:3, 3:2, 1:1
        'post_id'  	=> $post->ID,
    ), $atts );
    $post_id    = $atribut['post_id'];
    $size       = $atribut['size'];
    $ratio      = $atribut['ratio'];
    $ratio      = $ratio?str_replace(":","-",$ratio):'';

    $attachments = get_posts( array(
        'post_type' 		=> 'attachment',
        'posts_per_page' 	=> 1,
        'post_parent' 		=> $post_id,
        'orderby'          => 'date',
        'order'            => 'DESC',
    ));
    if (has_post_thumbnail($post_id)){
	    $urlimg = get_the_post_thumbnail_url($post_id,$size);    
	} elseif($attachments) {
        $urlimg = wp_get_attachment_url( $attachments[0]->ID, 'full' );
    } else{        
        $urlimg = VELOCITY_IKLAN_PLUGIN_URL.'public/img/no-image.png';
    }

    $html = '<div class="velocitymp-ratio-image">';
        $html .= '<a class="velocitymp-ratio-image-link" href="'.get_the_permalink($post_id).'" title="'.get_the_title($post_id).'">';
            $html .= '<div class="velocitymp-ratio-image-box velocitymp-ratio-image-'.$ratio.'" style="background-image: url('.$urlimg.');">';
                $html .= '<img src="'.$urlimg.'" loading="lazy" class="velocitymp-ratio-image-img d-none"/>';
            $html .= '</div>';
        $html .= '</a>';
    $html .= '</div>';
	return $html;
}


add_shortcode('velocity-iklan-harga', function($atts) {
    global $post;
    $atribut = shortcode_atts( array(
        'post_id' => $post->ID,
    ), $atts );

    $post_id = $atribut['post_id'];
    $price = get_post_meta($post_id, 'harga', true);
    $harga = (int) preg_replace('/[^0-9]/', '', $price); // <-- Cast jadi int

    // Kalau kosong, tampilkan "Hubungi" atau Rp 0
    if ($harga > 0) {
        $html = 'Rp ' . number_format($harga, 0, ',', '.');
    } else {
        $html = 'Hubungi Admin';
    }

    return $html;
});



// [velocity-iklan-kategori]
add_shortcode('velocity-iklan-kategori', function($atts) {
    $terms = get_terms(array(
        'taxonomy' => 'kategori',
        'hide_empty' => true,
        'parent' => 0,
    ));  
    $html = '';                      
    if(!empty($terms)) {
        $html .= '<div class="row">';
        foreach ($terms as $term) {
            $html .= '<div class="col-6 col-md-3 mb-4 text-center">';
                $html .= '<div class="h-100 border rounded-0">';
                    $html .= '<a class="h-100 d-block py-2 px-1" href="'.get_term_link($term->term_id).'">';
                        $html .= velocity_term_image($term->term_id,'large',array('class'=>'mb-2 velocity-term-image'));
                        $html .= '<div class="lh-sm">';
                            $html .= $term->name;
                        $html .= '</div>';
                    $html .= '</a>';
                $html .= '</div>';
            $html .= '</div>';
        }
        $html .= '</div>';
    }
    return $html;
});


// [velocity-iklan-taxonomy]
add_shortcode('velocity-iklan-taxonomy', function($atts) {
    $atts = shortcode_atts( array(
        'post_id' => get_the_ID(),
        'taxonomy' => 'kategori',
        'separator' => ', ',
    ), $atts, 'custom_taxonomy' );

    $post_id = intval($atts['post_id']);
    $taxonomy = sanitize_key($atts['taxonomy']);
    $separator = $atts['separator'];

    $terms = wp_get_post_terms($post_id, $taxonomy);

    if (empty($terms) || is_wp_error($terms)) {
        return '';
    }

    // Urutkan array terms berdasarkan parent-child hierarchy
    usort($terms, function($a, $b) {
        return $a->parent - $b->parent;
    });

    $term_links = array();
    foreach ($terms as $term) {
        $term_link = '<a href="' . esc_url(get_term_link($term)) . '" class="text-muted">' . esc_html($term->name) . '</a>';
        $term_links[] = $term_link;
    }

    $output = implode($separator, $term_links);

    return $output;
});



// [velocity-iklan-profile]
add_shortcode('velocity-iklan-profile', function() {
    require_once VELOCITY_IKLAN_PLUGIN_DIR . 'public/page-profile.php';
});

// [velocity-iklan-filter]
add_shortcode('velocity-iklan-filter', function() {
    require_once VELOCITY_IKLAN_PLUGIN_DIR . 'public/page-iklan-filter.php';
});



// [velocity-iklan-loop post_id="" class=""]
add_shortcode('velocity-iklan-loop', function($atts) {
	global $post;
    $atribut = shortcode_atts(array(
        'class'    => 'block-primary h-100 p-3',
        'post_id'  => $post->ID,
        'style'    => 'list',
    ), $atts);

    $post_id = $atribut['post_id'];
    $class   = $atribut['class'];
    $style   = $atribut['style'];
    
    $kamartidur  = get_post_meta($post_id, 'jumlahkamartidur', true);
    $kamarmandi  = get_post_meta($post_id, 'jumlahkamarmandi', true);
    $luasbangunan  = get_post_meta($post_id, 'jumlahlantai', true);
    $alamat  = get_post_meta($post_id, 'alamat', true);
    $jenis   = get_post_meta($post_id, 'jenis', true);
    $iklan_class = $jenis == 'premium' ? 'iklan-premium ' : 'iklan-biasa ';
    $post_classes = $iklan_class . $class;
    $style_class = $style == 'grid' ? 'row' : 'w-100';
    $style_col1 = $style == 'grid' ? 'col-sm-6 col-md-5 pe-md-0 mb-2 mb-md-0' : 'mb-2';
    $style_col2 = $style == 'grid' ? 'col-sm-6 col-md-7' : 'w-100';

    $html = '';
    $html .= '<div class="' . esc_attr($post_classes) . '" id="post-' . esc_attr($post_id) . '">';
    $html .= '<div class="'.$style_class.'">';
        $html .= '<div class="'.$style_col1.'">';
            $html .= iklan_galeri_foto();
        $html .= '</div>'; // col-content

    // === CONTENT ===
    $html .= '<div class="'.$style_col2.'">';
        $html .= '<div class="fw-bold mb-2 fs-5"><a href="' . get_the_permalink($post_id) . '" rel="bookmark">' . get_the_title($post_id) . '</a></div>';
        $html .= '<div class="mb-2 fw-bold">' . do_shortcode('[velocity-iklan-harga post_id="' . $post_id . '"]') . '</div>';
        if(!empty($alamat)){
            $html .= '<div class="mb-2"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pin-map" viewBox="0 0 16 16"> <path fill-rule="evenodd" d="M3.1 11.2a.5.5 0 0 1 .4-.2H6a.5.5 0 0 1 0 1H3.75L1.5 15h13l-2.25-3H10a.5.5 0 0 1 0-1h2.5a.5.5 0 0 1 .4.2l3 4a.5.5 0 0 1-.4.8H.5a.5.5 0 0 1-.4-.8z"/> <path fill-rule="evenodd" d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6M4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999z"/> </svg> '.$alamat.'</div>';
        }
        if(!empty($kamartidur)){
            $html .= '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-door-open" viewBox="0 0 16 16"> <path d="M8.5 10c-.276 0-.5-.448-.5-1s.224-1 .5-1 .5.448.5 1-.224 1-.5 1"/> <path d="M10.828.122A.5.5 0 0 1 11 .5V1h.5A1.5 1.5 0 0 1 13 2.5V15h1.5a.5.5 0 0 1 0 1h-13a.5.5 0 0 1 0-1H3V1.5a.5.5 0 0 1 .43-.495l7-1a.5.5 0 0 1 .398.117M11.5 2H11v13h1V2.5a.5.5 0 0 0-.5-.5M4 1.934V15h6V1.077z"/> </svg> <small>'.$kamartidur.'</small> | ';
        }
        if(!empty($kamarmandi)){
            $html .= '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-droplet" viewBox="0 0 16 16"> <path fill-rule="evenodd" d="M7.21.8C7.69.295 8 0 8 0q.164.544.371 1.038c.812 1.946 2.073 3.35 3.197 4.6C12.878 7.096 14 8.345 14 10a6 6 0 0 1-12 0C2 6.668 5.58 2.517 7.21.8m.413 1.021A31 31 0 0 0 5.794 3.99c-.726.95-1.436 2.008-1.96 3.07C3.304 8.133 3 9.138 3 10a5 5 0 0 0 10 0c0-1.201-.796-2.157-2.181-3.7l-.03-.032C9.75 5.11 8.5 3.72 7.623 1.82z"/> <path fill-rule="evenodd" d="M4.553 7.776c.82-1.641 1.717-2.753 2.093-3.13l.708.708c-.29.29-1.128 1.311-1.907 2.87z"/> </svg> <small>'.$kamarmandi.'</small> | ';
        }
        if(!empty($luasbangunan)){
            $html .= '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-aspect-ratio" viewBox="0 0 16 16"> <path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h13A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 12.5zM1.5 3a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5z"/> <path d="M2 4.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1H3v2.5a.5.5 0 0 1-1 0zm12 7a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1 0-1H13V8.5a.5.5 0 0 1 1 0z"/> </svg> <small>'.$luasbangunan.'</small>';
        }
    $html .= '</div>'; // col-content

    $html .= '</div>'; // entry-content
    $html .= '</div>'; // outer wrapper

    return $html;
});


function iklan_galeri_foto($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $gallery_ids  = get_post_meta($post_id, 'gallery', true);
    $thumbnail_id = get_post_thumbnail_id($post_id);
    $html = '';

    if ($thumbnail_id && (!is_array($gallery_ids) || !in_array($thumbnail_id, $gallery_ids))) {
        if (!is_array($gallery_ids)) $gallery_ids = [];
        array_unshift($gallery_ids, $thumbnail_id);
    }

    $carousel_id = 'carousel-' . wp_rand() . $post_id;

    if (!empty($gallery_ids) && count($gallery_ids) > 1) {
        $html .= '<div id="' . esc_attr($carousel_id) . '" class="carousel slide">';
        $html .= '<div class="carousel-inner">';

        $i = 0;
        foreach ($gallery_ids as $img_id) {
            $active = ($i === 0) ? ' active' : '';
            $img = wp_get_attachment_image($img_id, 'large', false, ['class' => 'd-block w-100 h-100 object-fit-cover']);
            $html .= '<div class="carousel-item' . $active . '">';
            $html .= '<div class="ratio ratio-4x3 bg-light overflow-hidden">' . $img . '</div>';
            $html .= '</div>';
            $i++;
        }

        $html .= '</div>'; // .carousel-inner
        $html .= '
            <button class="carousel-control-prev" type="button" data-bs-target="#' . esc_attr($carousel_id) . '" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#' . esc_attr($carousel_id) . '" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>';
        $html .= '</div>'; // .carousel
    }
    elseif (!empty($gallery_ids)) {
        $img = wp_get_attachment_image($gallery_ids[0], 'large', false, ['class' => 'w-100 h-100 object-fit-cover', 'loading' => 'lazy']);
        $html .= '<div class="ratio ratio-4x3 bg-light overflow-hidden mb-2">';
        $html .= '<a href="' . get_the_permalink($post_id) . '">' . $img . '</a>';
        $html .= '</div>';
    }
    elseif ($thumbnail_id) {
        $img = wp_get_attachment_image($thumbnail_id, 'large', false, ['class' => 'w-100 h-100 object-fit-cover', 'loading' => 'lazy']);
        $html .= '<div class="ratio ratio-4x3 bg-light overflow-hidden mb-2">';
        $html .= '<a href="' . get_the_permalink($post_id) . '">' . $img . '</a>';
        $html .= '</div>';
    }
    else {
        $html .= '<div class="ratio ratio-4x3 bg-light overflow-hidden mb-2">';
        $html .= '<svg xmlns="http://www.w3.org/2000/svg" style="padding: 2rem;background-color: #ececec;width: 100%;height: 100%;" viewBox="0 0 60 60"><path fill="#5F7D95" d="M30,5 C44,5 55,16 55,30 C55,44 44,55 30,55 C16,55 5,44 5,30 C5,16 16,5 30,5 Z" /></svg>';
        $html .= '</div>';
    }

    return $html;
}

// Shortcode wrapper
add_shortcode('iklan_galeri_foto', function($atts) {
    $atts = shortcode_atts([
        'post_id' => get_the_ID(),
    ], $atts);
    return iklan_galeri_foto($atts['post_id']);
});



// [velocity-iklan-penjual]
add_shortcode('velocity-iklan-penjual', function($atts) {
    // fallback aman untuk global post
    global $post;

    $atribut = shortcode_atts([
        'post_id'   => 0,
        'author_id' => '',
    ], $atts);

    // 1) Tentukan post_id: gunakan atribut, lalu global $post bila ada
    $post_id = intval($atribut['post_id']);
    if (!$post_id && isset($post) && is_object($post) && !empty($post->ID)) {
        $post_id = intval($post->ID);
    }

    // 2) Tentukan author_id: atribut -> dari post -> dari author archive query -> kosong
    $author_id = intval($atribut['author_id']);
    if (!$author_id && $post_id) {
        $author_id = intval(get_post_field('post_author', $post_id));
    }
    if (!$author_id) {
        // coba ambil dari queried object (page author archive)
        $qo = get_queried_object();
        if ($qo && isset($qo->ID)) { // untuk WP_User di author archive
            $author_id = intval($qo->ID);
        } else {
            // kadang get_query_var('author') menyimpan ID author
            $q_author = intval(get_query_var('author'));
            if ($q_author) {
                $author_id = $q_author;
            }
        }
    }

    // Jika tetap tidak ada author_id, hentikan dan beri output kosong atau pesan aman
    if (!$author_id) {
        return '<div class="alert alert-danger">Author not found.</div>';
    }

    // Pastikan user ada
    $user_info = get_userdata($author_id);
    if (!$user_info) {
        return '<div class="alert alert-danger">User not found.</div>';
    }

    // Ambil data user dengan aman
    $author_name   = $user_info->display_name ?? '';
    $author_email  = $user_info->user_email ?? '';
    $author_phone  = get_user_meta($author_id, 'phone', true);
    $author_bio    = get_user_meta($author_id, 'bio', true);
    $author_alamat = get_user_meta($author_id, 'alamat', true);
    $avatar_url    = get_user_meta($author_id, 'avatar', true);
    $user_registered = $user_info->user_registered ?? '';
    $lastlogin     = get_user_meta($author_id, 'lastlogin', true);

    $html = '<div class="velocity-info-penjual text-center text-md-start">';
    $html .= '<div class="row align-items-center">';

    // Kolom 1: Foto
    $html .= '<div class="col-sm-3 col-md-2 text-center">';
    if ($avatar_url) {
        $html .= '<img class="img-fluid rounded-circle" width="100" height="100" src="' . esc_url($avatar_url) . '" alt="' . esc_attr($author_name) . '">';
    } else {
        $html .= '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" fill="currentColor" class="text-black-50 bi bi-image" viewBox="0 0 16 16"><path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/><path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2z"/></svg>';
    }
    $html .= '</div>';

    // Kolom 2: Info penjual
    $html .= '<div class="col-sm-6 col-md-7 my-sm-0 my-2">';
    $html .= '<div class="fw-bold mb-2 fs-6 text-uppercase"><a class="text-dark" href="' . get_author_posts_url($author_id) . '" title="' . esc_attr($author_name) . '">' . esc_html($author_name) . '</a></div>';
    $html .= wpautop($author_bio);

    if ($author_alamat) {
        $html .= '<p class="mb-1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-door me-1" viewBox="0 0 16 16"> <path d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4.5a.5.5 0 0 0 .5-.5v-4h2v4a.5.5 0 0 0 .5.5H14a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293zM2.5 14V7.707l5.5-5.5 5.5 5.5V14H10v-4a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v4z"/> </svg>' . esc_html($author_alamat) . '</p>';
    }

    if ($user_registered) {
        $html .= '<p class="mb-1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar2-date me-1" viewBox="0 0 16 16"> <path d="M6.445 12.688V7.354h-.633A13 13 0 0 0 4.5 8.16v.695c.375-.257.969-.62 1.258-.777h.012v4.61zm1.188-1.305c.047.64.594 1.406 1.703 1.406 1.258 0 2-1.066 2-2.871 0-1.934-.781-2.668-1.953-2.668-.926 0-1.797.672-1.797 1.809 0 1.16.824 1.77 1.676 1.77.746 0 1.23-.376 1.383-.79h.027c-.004 1.316-.461 2.164-1.305 2.164-.664 0-1.008-.45-1.05-.82zm2.953-2.317c0 .696-.559 1.18-1.184 1.18-.601 0-1.144-.383-1.144-1.2 0-.823.582-1.21 1.168-1.21.633 0 1.16.398 1.16 1.23"/> <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1z"/> <path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5z"/> </svg>Sejak ' . date('d/m/Y', strtotime($user_registered)) . '</p>';
    }

    if ($lastlogin) {
        $diff = human_time_diff(strtotime($lastlogin), current_time('timestamp'));
        $html .= '<p class="mb-1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock me-1" viewBox="0 0 16 16"> <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/> <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/> </svg> Aktif ' . $diff . ' lalu</p>';
    }
    $html .= '</div>';

    // Kolom 3: Kontak
    $html .= '<div class="col-md-3">';
    if ($author_phone) {
        $html .= '<a href="tel:' . esc_attr($author_phone) . '" class="btn btn-md btn-outline-dark w-100 mb-2 py-2"><i class="bi bi-telephone me-1"></i>Telepon</a>';
    }

    $html .= '<a href="mailto:' . esc_attr($author_email) . '?subject=' . esc_attr(get_the_title($post_id)) . '&body=' . esc_url(get_the_permalink($post_id)) . '" class="btn btn-md btn-outline-dark w-100 py-2"><i class="bi bi-envelope me-1"></i>Email</a>';
    $html .= '</div>';

    $html .= '</div>'; // .row
    $html .= '</div>'; // .velocity-info-penjual

    return $html;
});


// [velocity-iklan-galeri]
add_shortcode('velocity-iklan-galeri', function($atts){
    $atribut = shortcode_atts( array(
        'post_id'   => get_the_ID(),
    ), $atts );
	$post_id = $atribut['post_id'];
    
    // Mendapatkan post meta 'galeri' berdasarkan ID pos yang diberikan
    $galeri_ids = get_post_meta($post_id, 'gallery', true);
    
    // Mendapatkan URL thumbnail post
    $thumbnail_url = get_the_post_thumbnail_url($post_id, 'large');

    $id = rand(100, 999);
    $output = '';
    if ($thumbnail_url || $galeri_ids) {
        // Menginisialisasi variabel untuk menyimpan output HTML
        $output .= '<div class="velocity-iklan-galeri parent-container-'.$id.'">';
            $output .= '<div id="galeri-slider-'.$id.'" class="carousel carousel-dark slide" data-bs-ride="carousel">';
                $output .= '<div class="carousel-inner text-center">';
                
                    // Menambahkan thumbnail post sebagai slide pertama
                    if ($thumbnail_url) {
                        $output .= '<div class="carousel-item active">';
                            $output .= '<a href="' . $thumbnail_url . '">';
                                $output .= '<img src="' . $thumbnail_url . '" class="w-auto">';
                            $output .= '</a>';
                        $output .= '</div>';
                    }
                    
                if (!empty($galeri_ids)) {
                    // Loop melalui setiap ID gambar dalam galeri
                    foreach ($galeri_ids as $key => $galeri_id) {
                        // Mendapatkan URL gambar
                        $image_url = wp_get_attachment_image_url($galeri_id, 'large');
                        // Mengecek apakah ini gambar pertama dalam galeri
                        $active_class = ($key == 0 && !$thumbnail_url) ? ' active' : '';
                        // Membuat elemen HTML untuk setiap gambar dalam slider
                        $output .= '<div class="carousel-item' . $active_class . '">';
                            $output .= '<a href="' . $image_url . '">';
                                $output .= '<img src="' . $image_url . '" class="w-auto">';
                            $output .= '</a>';
                        $output .= '</div>';
                    }

                }
                
                $output .= '</div>';
                
                if (!empty($galeri_ids)) {
                    // Menambahkan tombol navigasi
                    $output .= '<button class="carousel-control-prev" type="button" data-bs-target="#galeri-slider-'.$id.'" data-bs-slide="prev">';
                        $output .= '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
                        $output .= '<span class="visually-hidden">Previous</span>';
                    $output .= '</button>';
                    
                    $output .= '<button class="carousel-control-next" type="button" data-bs-target="#galeri-slider-'.$id.'" data-bs-slide="next">';
                        $output .= '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
                        $output .= '<span class="visually-hidden">Next</span>';
                    $output .= '</button>';
                }
            
            $output .= '</div>';
        $output .= '</div>';

        // Menambahkan script untuk Magnific Popup
        $output .= '<script>
            jQuery(document).ready(function($) {
                $(".parent-container-'.$id.'").magnificPopup({
                    delegate: "a",
                    type: "image",
                    gallery:{
                        enabled:true
                    }
                });
            });
        </script>';
    }

    return $output;
});

// [velocity-iklan-meta]
add_shortcode('velocity-iklan-meta', function($atts){
    $atribut = shortcode_atts( array(
        'key'   => '',
        'post_id'   => get_the_ID(),
    ), $atts );
	$post_id = $atribut['post_id'];
	$key = $atribut['key'];
    $value = get_post_meta($post_id, $key, true);
    $html = '';
    if(empty($key)){
        $html .= 'key is required, example: [velocity-iklan-meta key="detail"]';
    } elseif (is_array($value)) {
        $html .= '<table class="table">';
            $html .= '<tbody>';
                foreach($value as $str){
                    $cek = strpos($str, '=');
                    if($cek == true){
                        $nilai = explode('=',$str);
                        $html .= '<tr>';
                            $html .= '<td class="fw-bold">'.$nilai[0].'</td>';
                            $html .= '<td class="text-muted">'.$nilai[1].'</td>';
                        $html .= '</tr>';
                    } else {
                        $html .= '<tr>';
                            $html .= '<td class="fw-bold" colspan="2">'.$str.'</td>';
                        $html .= '</tr>';
                    }
                }
            $html .= '</tbody>';
        $html .= '</table>';
    } elseif($value) {
        $html .= $value;
    }
    return $html;
});


// [velocity-cari-propeti]
add_shortcode('velocity-cari-propeti', function($atts){
	$s = isset($_GET['s'])?$_GET['s']:'';
	$jenis = isset($_GET['jenis'])?$_GET['jenis']:'';
	if ($jenis=='beli'){
		$checkbeli = 'checked';
	} elseif($jenis=='sewa'){
		$checksewa = 'checked';
	} else {
		$checkbeli = 'checked';
		$checksewa = '';
	}
    $html = '';
    $html .= '<form action="'.get_post_type_archive_link('iklan').'" class="needs-validation">';
        $html .= '<div class="bg-white form-atas row m-0 text-center">';
            $html .= '<div class="fl col-6 p-0"><input id="jual" type="radio" name="jenis" value="jual" '.$checkbeli.'><label for="jual">BELI PROPERTI</label></div>';
            $html .= '<div class="fr col-6 p-0"><input id="sewa" type="radio" name="jenis" value="sewa" '.$checksewa.'><label for="sewa">SEWA PROPERTI</label></div>';
        $html .= '</div>';
      $html .= '<div class="form-bawah">';
        $html .= '<input type="text" name="s" placeholder="Cari Properti" value="'.$s.'" required="required">';
        $html .= '<button type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>';
      $html .= '</div>';
    $html .= '</form>';
    
    return $html;
});


// [velocity-filter]
add_shortcode('velocity-filter', function () {
    ob_start();
    echo '<div class="velocity-filter">';
    
    // Menyertakan file dari folder plugin
    include VELOCITY_IKLAN_PLUGIN_DIR . 'includes/filter.php';
    
    echo '</div>';
    return ob_get_clean();
});



function spesifikasi_iklan() {
    global $post;
    $idproduk   = $post->ID;
    $date       = get_the_modified_date('d-m-Y', $idproduk);
    $kamartidur = get_post_meta($idproduk, 'jumlahkamartidur', true);
    $kamarmandi = get_post_meta($idproduk, 'jumlahkamarmandi', true);
    $luasbangunan = get_post_meta($idproduk, 'luasbangunan', true);
    $luastanah = get_post_meta($idproduk, 'luastanah', true);
    $jumlahlantai = get_post_meta($idproduk, 'jumlahlantai', true);

    $html = '<table class="table table-sm"><tbody>';

    if (!empty($kamartidur)) {
        $html .= '
        <tr>
            <th scope="row"><i class="fa fa-bed"></i></th>
            <td>Kamar Tidur</td>
            <td>' . esc_html($kamartidur) . '</td>
        </tr>';
    }

    if (!empty($kamarmandi)) {
        $html .= '
        <tr>
            <th scope="row"><i class="fa fa-bath"></i></th>
            <td>Kamar Mandi</td>
            <td>' . esc_html($kamarmandi) . '</td>
        </tr>';
    }

    if (!empty($luasbangunan)) {
        $html .= '
        <tr>
            <th scope="row"><i class="fa fa-home"></i></th>
            <td>Luas Bangunan</td>
            <td>' . esc_html($luasbangunan) . ' m2</td>
        </tr>';
    }

    if (!empty($luastanah)) {
        $html .= '
        <tr>
            <th scope="row"><i class="fa fa-square-o"></i></th>
            <td>Luas Tanah</td>
            <td>' . esc_html($luastanah) . ' m2</td>
        </tr>';
    }

    if (!empty($jumlahlantai)) {
        $html .= '
        <tr>
            <th scope="row"><i class="fa fa-window-minimize"></i></th>
            <td>Jumlah Lantai</td>
            <td>' . esc_html($jumlahlantai) . ' lantai</td>
        </tr>';
    }

    // Tanggal Ditambahkan selalu ditampilkan
    $html .= '
    <tr>
        <th scope="row"><i class="fa fa-calendar"></i></th>
        <td>Tanggal Ditambahkan</td>
        <td>' . get_the_date('j F Y', $idproduk) . '</td>
    </tr>';

    $html .= '</tbody></table>';

    return $html;
}
add_shortcode('spesifikasi-iklan', 'spesifikasi_iklan');


// [velocity-post-meta]
add_shortcode('velocity-post-meta', function($atts){
    $atts = shortcode_atts([
        'post_id' => get_the_ID(),
        'key'     => '',
    ], $atts);

    $post_id = $atts['post_id'];
    $key     = $atts['key'];

    if (empty($key)) return '';

    $meta = get_post_meta($post_id, $key, true);

    if (!$meta) return '';

    // Jika array
    if (is_array($meta)) {
        $output = '<ul class="list-group list-group-flush border-bottom">';
        foreach($meta as $item){
            $output .= '<li class="list-group-item p-1">' . esc_html($item) . '</li>';
        }
        $output .= '</ul>';
        return $output;
    }

    // Jika bukan array atau string yang dipisah koma
    return esc_html($meta);
});


// [velocity-iklan-penjual]
add_shortcode('velocity-iklan-penjual', function($atts) {
    global $post;
    $atribut = shortcode_atts([
        'post_id'   => $post->ID,
        'author_id' => '',
    ], $atts);

    $post_id     = $atribut['post_id'];
    $author_id   = $atribut['author_id'] ?: get_post_field('post_author', $post_id);
    $author_name = get_the_author_meta('display_name', $author_id);
    $author_email = get_the_author_meta('user_email', $author_id);
    $author_phone = get_user_meta($author_id, 'phone', true);
    $author_bio   = get_user_meta($author_id, 'bio', true);
    $author_alamat = get_user_meta($author_id, 'alamat', true);
    $avatar_url   = get_user_meta($author_id, 'avatar', true);
    $user_info    = get_userdata($author_id);
    $user_registered = $user_info->user_registered ?? '';
    $lastlogin    = get_user_meta($author_id, 'lastlogin', true);

    $html = '<div class="velocity-info-penjual text-center text-md-start">';
    $html .= '<div class="row align-items-center">';

    // Kolom 1: Foto
    $html .= '<div class="col-sm-3 col-md-2 text-center">';
    if ($avatar_url) {
        $html .= '<img class="img-fluid rounded-circle" width="100" height="100" src="' . esc_url($avatar_url) . '" alt="' . esc_attr($author_name) . '">';
    } else {
        $html .= '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" fill="currentColor" class="text-black-50 bi bi-image" viewBox="0 0 16 16"><path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/><path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2z"/></svg>';
    }
    $html .= '</div>';

    // Kolom 2: Info penjual
    $html .= '<div class="col-sm-6 col-md-7 my-sm-0 my-2">';
    $html .= '<div class="fw-bold mb-2 fs-6 text-uppercase"><a class="text-dark" href="' . get_author_posts_url($author_id) . '" title="' . esc_attr($author_name) . '">' . esc_html($author_name) . '</a></div>';
    $html .= wpautop($author_bio);

    if ($author_alamat) {
        $html .= '<p class="mb-1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-door me-1" viewBox="0 0 16 16"> <path d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4.5a.5.5 0 0 0 .5-.5v-4h2v4a.5.5 0 0 0 .5.5H14a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293zM2.5 14V7.707l5.5-5.5 5.5 5.5V14H10v-4a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v4z"/> </svg>' . esc_html($author_alamat) . '</p>';
    }

    if ($user_registered) {
        $html .= '<p class="mb-1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar2-date me-1" viewBox="0 0 16 16"> <path d="M6.445 12.688V7.354h-.633A13 13 0 0 0 4.5 8.16v.695c.375-.257.969-.62 1.258-.777h.012v4.61zm1.188-1.305c.047.64.594 1.406 1.703 1.406 1.258 0 2-1.066 2-2.871 0-1.934-.781-2.668-1.953-2.668-.926 0-1.797.672-1.797 1.809 0 1.16.824 1.77 1.676 1.77.746 0 1.23-.376 1.383-.79h.027c-.004 1.316-.461 2.164-1.305 2.164-.664 0-1.008-.45-1.05-.82zm2.953-2.317c0 .696-.559 1.18-1.184 1.18-.601 0-1.144-.383-1.144-1.2 0-.823.582-1.21 1.168-1.21.633 0 1.16.398 1.16 1.23"/> <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1z"/> <path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5z"/> </svg>Sejak ' . date('d/m/Y', strtotime($user_registered)) . '</p>';
    }

    if ($lastlogin) {
        $diff = human_time_diff(strtotime($lastlogin), current_time('timestamp'));
        $html .= '<p class="mb-1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock me-1" viewBox="0 0 16 16"> <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/> <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/> </svg> Aktif ' . $diff . ' lalu</p>';
    }
    $html .= '</div>';

    // Kolom 3: Kontak
    $html .= '<div class="col-md-3">';
    if ($author_phone) {
        $html .= '<a href="tel:' . esc_attr($author_phone) . '" class="btn btn-md btn-outline-dark w-100 mb-2 py-2"><i class="bi bi-telephone me-1"></i>Telepon</a>';
    }

    $html .= '<a href="mailto:' . esc_attr($author_email) . '?subject=' . esc_attr(get_the_title($post_id)) . '&body=' . esc_url(get_the_permalink($post_id)) . '" class="btn btn-md btn-outline-dark w-100 py-2"><i class="bi bi-envelope me-1"></i>Email</a>';
    $html .= '</div>';

    $html .= '</div>'; // .row
    $html .= '</div>'; // .velocity-info-penjual

    return $html;
});



function velocity_custom_menu_shortcode() {
    // Ambil semua term dari taxonomy 'lokasi'
    $terms = get_terms(array(
        'taxonomy'   => 'lokasi',
        'hide_empty' => false,
    ));

    // Ambil semua term dari taxonomy 'kategori'
    $terms_kategori = get_terms(array(
        'taxonomy'   => 'kategori',
        'hide_empty' => false,
    ));
    
    $search_url = get_post_type_archive_link('iklan');

    ob_start(); // Output buffering untuk shortcode

    ?>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="navbar-container">
            <!-- Toggle Button for Mobile (trigger offcanvas) -->
            <button class="navbar-toggler p-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Offcanvas Menu (replaces collapse) -->
            <div class="offcanvas offcanvas-start" tabindex="-1" id="mainNavbar" aria-labelledby="mainNavbarLabel">
                <div class="offcanvas-header border-bottom">
                    <div class="offcanvas-title fs-6" id="mainNavbarLabel">Menu</div>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                        <!-- Menu Lokasi -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="LokasiDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Lokasi
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="LokasiDropdown">
                                <?php
                                if (!is_wp_error($terms) && !empty($terms)) {
                                    foreach ($terms as $term) {
                                        $term_link = esc_url(get_term_link($term));
                                        echo '<li><a class="dropdown-item" href="' . $term_link . '">' . esc_html($term->name) . '</a></li>';
                                    }
                                }
                                ?>
                            </ul>
                        </li>

                        <!-- Menu Kategori Properti -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="kategoriDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Kategori Properti
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="kategoriDropdown">
                                <?php
                                if (!is_wp_error($terms_kategori) && !empty($terms_kategori)) {
                                    foreach ($terms_kategori as $term) {
                                        $term_link = esc_url(get_term_link($term));
                                        echo '<li><a class="dropdown-item" href="' . $term_link . '">' . esc_html($term->name) . '</a></li>';
                                    }
                                }
                                ?>
                            </ul>
                        </li>

                        <!-- Menu Kamar -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="kamarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Kamar
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="kamarDropdown">
                                <?php for ($i = 1; $i <= 5; $i++) : ?>
                                    <li><a class="dropdown-item" href="<?php echo esc_url($search_url.'?kamar='.$i); ?>"><?php echo $i; ?> Kamar</a></li>
                                <?php endfor; ?>
                            </ul>
                        </li>

                        <!-- Menu Harga -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="hargaDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Kisaran Harga
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="hargaDropdown">
                                <?php
                                $harga_ranges = array(
                                    array('label' => '0 - 50 Juta', 'minprice' => 0, 'maxprice' => 50000000),
                                    array('label' => '50 - 100 Juta', 'minprice' => 50000000, 'maxprice' => 100000000),
                                    array('label' => '100 - 500 Juta', 'minprice' => 100000000, 'maxprice' => 500000000),
                                    array('label' => '500 Juta - 1 Miliar', 'minprice' => 500000000, 'maxprice' => 1000000000),
                                    array('label' => '1 - 2.5 Miliar', 'minprice' => 1000000000, 'maxprice' => 2500000000),
                                    array('label' => '2.5 - 5 Miliar', 'minprice' => 2500000000, 'maxprice' => 5000000000),
                                    array('label' => '>5 Miliar', 'minprice' => 5000000000, 'maxprice' => 9999999999999),
                                );

                                foreach ($harga_ranges as $range) {
                                    $url = esc_url(add_query_arg(array(
                                        'minprice' => $range['minprice'],
                                        'maxprice' => $range['maxprice']
                                    ), $search_url));

                                    echo '<li><a class="dropdown-item" href="' . $url . '">' . esc_html($range['label']) . '</a></li>';
                                }
                                ?>
                            </ul>
                        </li>

                    </ul>
                </div> <!-- end offcanvas-body -->
            </div> <!-- end offcanvas -->
        </div>
    </nav>
    <?php

    return ob_get_clean();
}
add_shortcode('velocity-custom-menu', 'velocity_custom_menu_shortcode');


// [tanda-iklan-premium]
function tanda_iklan_premium($atts){
    global $post;

    $atribut = shortcode_atts(array(
        'post_id' => isset($post->ID) ? $post->ID : 0,
    ), $atts);

    $post_id = intval($atribut['post_id']);
    if (!$post_id) {
        return '';
    }

    $jenis = get_post_meta($post_id, 'jenis', true);

    if (strtolower(trim($jenis)) === 'premium') {
        return '<span class="tanda-iklan-premium">Premium</span>';
    }

    return '';
}
add_shortcode('tanda-iklan-premium', 'tanda_iklan_premium');

