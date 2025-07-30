
<div class="mt-4 py-2 px-3 mb-2 bg-dark text-white fs-6 rounded-top">Pengajuan Iklan Premium</div>

<?php
$args = array(
    'post_type' => 'iklan',
    'showposts' => -1,
    'meta_key' => 'jenis',
    'meta_value' => 'pengajuan',
);

$iklan_query = new WP_Query($args);

if ($iklan_query->have_posts()) :
    echo '<table class="table">';
        echo '<thead>';
            echo '<tr>';
                echo '<th scope="col">Nama Iklan</th>';
                echo '<th scope="col">Pengiklan</th>';
                echo '<th scope="col">Tindakan</th>';
            echo '</tr>';
        echo '</thead>';
    echo '<tbody>';
    while ($iklan_query->have_posts()) :
        $iklan_query->the_post();
        $id = get_the_ID();
        echo '<tr class="tr-'.$id.'">';
            echo '<td><a href="'.get_the_permalink().'" target="_blank">'.get_the_title().'</a></td>';
            echo '<td>';
                the_author();
            echo '</td>';
            echo '<td class="aksi-'.$id.'">';
                echo '<div class="konfirmasi-premium btn btn-sm btn-success me-2" id="'.$id.'">Terima</div>';
                echo '<div class="konfirmasi-premium btn btn-sm btn-danger" id="'.$id.'">Hapus</div>';
            echo '</td>';
        echo '</tr>';
    endwhile;
    echo '</tbody>';
    echo '</table>';
    wp_reset_postdata();
else :
    echo '<div class="alert alert-warning py-2">Tidak ada pengajuan.</div>';
endif;
?>