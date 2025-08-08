<?php
/**
 * The template for modify query product
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package velocity toko
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

function velocity_search_custom_query( $query ) {
    if ( is_archive() && is_post_type_archive( 'iklan' ) && $query->is_main_query() ) {

        // Sorting
        $short = isset($_GET['short']) ? $_GET['short'] : '';
        if ($short == 'murah') {
            $query->set('orderby', 'meta_value_num');
            $query->set('meta_key', 'harga');
            $query->set('order', 'ASC');
        } elseif ($short == 'mahal') {
            $query->set('orderby', 'meta_value_num');
            $query->set('meta_key', 'harga');
            $query->set('order', 'DESC');
        } elseif ($short == 'baru') {
            $query->set('orderby', 'date');
            $query->set('order', 'DESC');
        } elseif ($short == 'lama') {
            $query->set('orderby', 'date');
            $query->set('order', 'ASC');
        } elseif ($short == 'namaa') {
            $query->set('orderby', 'title');
            $query->set('order', 'ASC');
        } elseif ($short == 'namaz') {
            $query->set('orderby', 'title');
            $query->set('order', 'DESC');
        }

        // Meta Query: harga min & max
        $metaquery = array();

        $hargamin = isset($_GET['minprice']) && $_GET['minprice'] !== '' ? $_GET['minprice'] : '';
        if ($hargamin) {
            $metaquery[] = array(
                'key'     => 'harga',
                'value'   => $hargamin,
                'compare' => '>=',
                'type'    => 'numeric',
            );
        }

        $hargamax = isset($_GET['maxprice']) && $_GET['maxprice'] !== '' ? $_GET['maxprice'] : '';
        if ($hargamax) {
            $metaquery[] = array(
                'key'     => 'harga',
                'value'   => $hargamax,
                'compare' => '<=',
                'type'    => 'numeric',
            );
        }

        // jumlah kamar tidur
        $kamar = isset($_GET['kamar']) && $_GET['kamar'] !== '' ? $_GET['kamar'] : '';
        if ($kamar) {
            $metaquery[] = array(
                'key'     => 'jumlahkamartidur',
                'value'   => $kamar,
                'compare' => '>=',
                'type'    => 'numeric',
            );
        }

        // jenis (post meta)
        $jenis = isset($_GET['jns']) && $_GET['jns'] !== '' ? $_GET['jns'] : '';
        if ($jenis) {
            $metaquery[] = array(
                'key'     => 'jenis',
                'value'   => $jenis,
                'compare' => '=',
            );
        }

        if (count($metaquery) > 1) {
            $metaquery['relation'] = 'AND';
        }

        if (!empty($metaquery)) {
            $query->set('meta_query', $metaquery);
        }

        // Taxonomy Query
        $taxquery = array();

        // kategori (custom taxonomy)
        $kategori = isset($_GET['kat']) ? $_GET['kat'] : '';
        if (!empty($kategori)) {
            $taxquery[] = array(
                'taxonomy' => 'kategori',
                'field'    => 'term_id',
                'terms'    => $kategori,
            );
        }

        // lokasi (custom taxonomy)
       $lokasi = isset($_GET['lok']) ? $_GET['lok'] : '';
        if (!empty($lokasi)) {
            $taxquery[] = array(
                'taxonomy' => 'lokasi',
                'field'    => 'term_id',
                'terms'    => $lokasi,
            );
        }

        if (count($taxquery) > 1) {
            $taxquery['relation'] = 'AND';
        }

        if (!empty($taxquery)) {
            $query->set('tax_query', $taxquery);
        }

        $query->set('post_type', 'iklan');

    }
}
add_filter('pre_get_posts', 'velocity_search_custom_query');
