<?php 
$s          = isset($_GET['sq']) ? $_GET['sq'] : '';
$lokasi     = isset($_GET['lokasi']) ? $_GET['lokasi'] : '';
$kategori   = isset($_GET['cp']) ? $_GET['cp'] : '';
$paged      = isset($_GET['h'])? $_GET['h'] : 1;
$posts_per_page = get_option( 'posts_per_page' );
?>
<div class="container py-3">
    <h1 class="vmpc-title"><?php echo vmpc_title(); ?></h1>
    <?php
    $args = array(
        'post_type'         => 'iklan',
        'posts_per_page'    => $posts_per_page,
        'paged'             => $paged,
    );
    $args_premium = array(
        'post_type'         => 'iklan',
        'orderby'           => 'rand',
        'posts_per_page'    => 4,
        'showposts'         => 4,
        'meta_query'        => array(
            array(
                'key'     => 'jenis',
                'value'   => 'premium',
                'compare' => '=',
            ),
        ),
    );
    if($s){
        $args['s'] = $s;
        $args_premium['s'] = $s;
    }
    
    //taxonomy query
    $taxquery = array();

    //taxonomy kategori product 
    if($kategori) {
        $taxquery[] = array(
            'taxonomy' => 'kategori',
            'field'    => 'term_id',
            'terms'    => $kategori,
        );
    }

    //taxonomy lokasi 
    if($lokasi) {
        $taxquery[] = array(
            'taxonomy' => 'lokasi',
            'field'    => 'term_id',
            'terms'    => $lokasi,
        );
    }

    //if count taxquery more than 1, then set taxquery
    if(count($taxquery)>1){
        $taxquery['relation'] = 'AND';
    }

    if($taxquery) {
        $args['tax_query'] = $taxquery;
        $args_premium['tax_query'] = $taxquery;
    }
    $premium_posts = get_posts($args_premium);
    if ($premium_posts) {
        echo '<div class="row border border-secondary bg-light mt-4 mx-0 mb-4 pt-4 pb-0 px-2">';
        foreach ($premium_posts as $premium) {
            echo '<div class="col-md-3 col-6 mb-4">';
                echo do_shortcode('[velocity-iklan-loop post_id="'.$premium->ID.'"]');
            echo '</div>';
            $exclude_ids[] = $premium->ID;
        }
        echo '</div>';
        $args['post__not_in'] = $exclude_ids;
    }

    $query = new WP_Query( $args );
    if ( $query->have_posts() ) {
        echo '<div class="row">';
        while ( $query->have_posts() ) {
            $query->the_post();
            echo '<div class="col-md-3 col-6 mb-4">';
                echo do_shortcode('[velocity-iklan-loop]');
            echo '</div>';
        }
        echo '</div>';
        global $wp;
        $current_url = home_url( add_query_arg( array(), $wp->request ) );
        $numpages = $query->max_num_pages;
        $pagination_args = array(
            'base'            => $current_url.'%_%',
            'format'          => '/?h=%#%',
            'total'           => $numpages,
            'current'         => $paged,
            'show_all'        => false,
            'end_size'        => 1,
            'mid_size'        => 2,
            'prev_next'       => true,
            'prev_text'       => __('&laquo;'),
            'next_text'       => __('&raquo;'),
            'type'            => 'plain',
            'add_args'        => false,
            'add_fragment'    => ''
        );
        $paginate_links = paginate_links($pagination_args);        
        if ($paginate_links) {
            echo "<nav class='velocity-iklan-pagination'>";
                echo "<span class='page-numbers page-num'>Page " . $paged . " of " . $numpages . "</span> ";
                echo $paginate_links;
            echo "</nav>";
        }
    } else {
        echo '<div class="alert alert-warning py-2">Hasil pencarian tidak ditemukan.</div>';
    }

    wp_reset_postdata();
    ?>
</div>