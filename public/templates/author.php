<?php get_header();
$current_author = get_queried_object();
$author_name = $current_author->display_name;
$author_id = $current_author->ID; ?>

<div class="container py-5">
    <div class="row">
        <div class="col-9 pe-0">
            <div class="fs-5 fw-bold mb-3"><?php echo $author_name; ?></div>
        </div>
        <div class="col-3 ps-0 text-end">
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#detailpenjual">
                Detail Penjual
            </button>
            <div class="modal fade" id="detailpenjual" tabindex="-1" aria-labelledby="detailpenjualLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailpenjualLabel"><?php echo $author_name; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-start">
                        <?php echo do_shortcode('[velocity-iklan-penjual author_id="'.$author_id.'"]'); ?>
                    </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
            <?php 
            $paged = isset($_GET['no'])? $_GET['no'] : 1;
            $args = array(
                'post_type' => 'iklan',
                'post_status' => 'publish',
                'author'    => $author_id,
                'posts_per_page' => get_option('posts_per_page'),
                'paged' => $paged,
            );
            $query = new WP_Query($args);
            if ($query->have_posts()) {
                echo '<div class="row">';
                while ($query->have_posts()) {
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
                        'format'          => '/?no=%#%',
                        'total'           => $numpages,
                        'current'         => $paged,
                        'show_all'        => False,
                        'end_size'        => 1,
                        'mid_size'        => 2,
                        'prev_next'       => True,
                        'prev_text'       => __('&laquo;'),
                        'next_text'       => __('&raquo;'),
                        'type'            => 'plain',
                        'add_args'        => false,
                        'add_fragment'    => ''
                    );
                    $paginate_links = paginate_links($pagination_args);
    
                    if ($paginate_links) {
                        echo "<nav class='vmpc-pagination my-3'>";
                        echo "<span class='page-numbers page-num'>Page " . $paged . " of " . $numpages . "</span> ";
                        echo $paginate_links;
                        echo "</nav>";
                    }
                    // End Pagination
            } else {
                echo '<div class="alert alert-warning py-2">Belum ada iklan.</div>';
            } ?>
</div>

<?php
get_footer();