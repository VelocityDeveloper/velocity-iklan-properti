<?php


$action     = isset($_GET['action'])?$_GET['action']:'';
$idpost     = isset($_GET['id'])?$_GET['id']:'';        

// echo '<div class="text-right mb-3">';
//     echo '<a href="?hal=pasang-iklan&action=add#formPost" class="btn btn-sm btn-info"> <i class="fa fa-plus-circle"></i> Tambah Produk</a>';
// echo '</div>';

    $action     = $action=='edit'?'edit':'add';
    $args       = [
        'post_type' => 'iklan',
    ];
    $metakey    = [            
        'post_title'    => [
            'type'      => 'text',
            'title'     => 'Judul',
            'desc'      => 'Judul iklan',
            'required'  => true,
        ],
        'post_content'  => [
            'type'      => 'editor',
            'title'     => 'Deskripsi',
            'desc'      => '',
            'required'  => false,
        ],
        '_thumbnail_id'=> [
            'type'      => 'featured',
            'title'     => 'Gambar',
            'desc'      => 'Foto utama',
            'required'  => true,
        ],
        'gallery'=> [
            'type'      => 'gallery',
            'title'     => 'Galeri',
            'desc'      => 'Galeri foto lainnya',
            'required'  => false,
        ],
        'harga'          => [
            'type'      => 'number',
            'title'     => 'Harga',
            'placeholder'     => '2500000',
            'desc'      => 'Isikan angka saja tanpa karakter khusus, contoh: 2500000',
            'required'  => true,
        ],
        'kategori' => [
            'type'      => 'taxonomy',
            'title'     => 'Kategori',
            'desc'      => '',
            'required'  => false,
        ],

        /* tambahan */

        'jenis' => [
            'type'      => 'select',
            'title'     => 'Jenis',
            'required'  => true,
            'options'    => [
                'jual' => 'Dijual',
                'sewa' => 'Disewakan',
            ],
        ],
        'luastanah' => [
            'type'      => 'number',
            'title'     => 'Luas Tanah',
            'desc'      => 'm2',
            'required'  => false,
        ],
        'luasbangunan' => [
            'type'      => 'number',
            'title'     => 'Luas Bangunan',
            'desc'      => 'm2',
            'required'  => false,
        ],
        'jumlahkamartidur' => [
            'type'      => 'number',
            'title'     => 'Jumlah Kamar Tidur',
            'desc'      => 'contoh: 2',
            'required'  => false,
        ],
        'jumlahkamarmandi' => [
            'type'      => 'number',
            'title'     => 'Jumlah Kamar Mandi',
            'desc'      => 'contoh: 2',
            'required'  => false,
        ],
        'jumlahlantai' => [
            'type'      => 'number',
            'title'     => 'Jumlah Lantai',
            'desc'      => 'contoh: 2',
            'required'  => false,
        ],
        'fasilitas'=> [
            'type'      => 'text',
            'title'     => 'Fasilitas',
            'desc'      => 'Daftar fasilitas',
            'placeholder'   => 'Kolam Renang',
            'required'  => false,
            'clone'     => true,
        ],
        'kemudahanakses' => [
            'type'      => 'text',
            'title'     => 'Kemudahan Akses ke',
            'desc'      => 'Kemudahan akses ke fasilitas umum',
            'placeholder' => 'Rumah Sakit',
            'required'  => true,
            'clone'     => true,
        ],

        /* akhir tambahan */


        'lokasi'        => [
            'type'      => 'taxonomy',
            'title'     => 'Lokasi',
            'desc'      => '',
            'required'  => false,
        ],
        'alamat' => [
            'type'      => 'textarea',
            'title'     => 'Alamat Lengkap',
            'required'  => false,
            'desc'      => 'Isi alamat lengkap seperti jalan maupun RT/RW.',
        ],
    ];

    if ($action == 'edit' && $idpost) {
        $post = get_post($idpost);

        if (!$post) {
            echo '<div class="alert alert-danger mt-3">Iklan tidak ditemukan.</div>';
            return;
        }

        // Cek apakah user saat ini adalah pemilik post
        if ((int) $post->post_author !== get_current_user_id()) {
            echo '<div class="alert alert-danger mt-3">Anda tidak memiliki akses untuk ubah iklan ini.</div>';
            return;
        }

        $args['ID'] = $idpost;
    }

    $form = New Frontpost();

    $titlecard = $action=='add'?'<i class="fa fa-plus-circle me-1"></i> Pasang Iklan':' <i class="fa fa-pencil me-1"></i> Edit Iklan';
    echo '<div class="card shadow mx-auto my-3">';
        echo '<div class="card-header">';
            echo '<span class="font-weight-bold fs-5">'.$titlecard.'</span>';
        echo '</div>';
        echo '<div class="card-body p-md-4">';
            echo $form->formPost($args,$action,$metakey);
        echo '</div>';
    echo '</div>';

