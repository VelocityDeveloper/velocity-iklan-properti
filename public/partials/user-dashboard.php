<?php
$user_id = get_current_user_id();
$paged = isset($_GET['no'])? $_GET['no'] : 1;
$args = array(
    'post_type'         => 'iklan',
    'author'            => $user_id,
	'posts_per_page'    => 12,
	'paged'		        => $paged,
);
$the_query = new WP_Query( $args );
?>

<div class="my-4">

        <?php if ( $the_query->have_posts() ): ?>            
            <div class="row">
            <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
                <?php $post_id = get_the_ID(); ?>
                <?php $jenis = get_post_meta( $post_id, 'jenis', true ); ?>
                <?php if($jenis == 'premium'){
                    $tooltip = 'Iklan Premium';
                    $button_class = 'btn-warning';
                    $iklan_class = 'iklan-premium';
                } elseif($jenis == 'pengajuan'){
                    $tooltip = 'Peninjauan Iklan Premium';
                    $button_class = 'btn-dark';
                    $iklan_class = 'border border-dark bg-light';
                } else {
                    $tooltip = 'Ajukan Iklan Premium';
                    $button_class = 'btn-secondary velocity-premium-button';
                    $iklan_class = 'border';
                } ?>
                <div class="col-md-6 mb-3 mb-sm-4 product-<?php echo $post_id; ?>">
                    <div class="row align-items-center m-0 <?php echo $iklan_class; ?> position-relative card-product-<?php echo $post_id; ?>">
                        <a class="position-absolute top-0 end-0 text-muted w-auto collapsed" data-bs-toggle="collapse" href="#trd-<?php echo $post_id; ?>" role="button" aria-expanded="false" aria-controls="trd-<?php echo $post_id; ?>"><i class="fa fa-ellipsis-h"></i></a>
                        <div id="trd-<?php echo $post_id; ?>" class="p-2 position-absolute bottom-0 end-0 w-auto collapse">
                            <ul class="list-group">
                                <li class="list-group-item p-0 text-center border-0 mb-1" id="btn-<?php echo $post_id; ?>"">
                                <a class="btn btn-sm <?php echo $button_class;?>" id="<?php echo $post_id; ?>" title="<?php echo $tooltip;?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" class="text-white bi bi-star-fill" viewBox="0 0 16 16"><path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/></svg>
                                </a></li>
                                <li class="list-group-item p-0 text-center border-0 mb-1"><a class="edit btn btn-sm btn-info" href="?hal=pasang-iklan&action=edit&id=<?php echo $post_id; ?>" title="Ubah Iklan">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16"><path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/></svg>
                                </a></li>
                                <li class="list-group-item p-0 text-center border-0"><a class="btn-product-delete btn btn-sm btn-danger text-white" id="<?php echo $post_id; ?>" title="Hapus Iklan">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/><path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/></svg>
                                </a></li>
                            </ul>
					    </div>
                        <div class="col-4 ps-0 pb-0"> 
                            <?php echo do_shortcode('[velocity-iklan-ratio-image size="large" ratio="1:1 "]'); ?>
                        </div>
                        <div class="col">
                            <a href="<?php echo get_the_permalink(); ?>" class="font-weight-bold lh-sm" target="_blank" rel="noopener noreferrer">
                                <?php echo get_the_title(); ?>
                            </a>
                            <div class="my-2">
                                <small>
                                    <span class="d-block font-weight-bold">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="align-middle bi bi-tag me-1" viewBox="0 0 16 16"><path d="M6 4.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m-1 0a.5.5 0 1 0-1 0 .5.5 0 0 0 1 0"/><path d="M2 1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 1 6.586V2a1 1 0 0 1 1-1m0 5.586 7 7L13.586 9l-7-7H2z"/></svg><?php echo do_shortcode('[velocity-iklan-harga]'); ?>
                                    </span>
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" class="align-middle bi bi-clock me-2" viewBox="0 0 16 16"><path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/></svg><?php echo get_the_date('d/m/Y H:i'); ?>
                                    </span>
                                </small>
                                <br>
                                <small>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="align-middle bi bi-pin me-1" viewBox="0 0 16 16"><path d="M4.146.146A.5.5 0 0 1 4.5 0h7a.5.5 0 0 1 .5.5c0 .68-.342 1.174-.646 1.479-.126.125-.25.224-.354.298v4.431l.078.048c.203.127.476.314.751.555C12.36 7.775 13 8.527 13 9.5a.5.5 0 0 1-.5.5h-4v4.5c0 .276-.224 1.5-.5 1.5s-.5-1.224-.5-1.5V10h-4a.5.5 0 0 1-.5-.5c0-.973.64-1.725 1.17-2.189A6 6 0 0 1 5 6.708V2.277a3 3 0 0 1-.354-.298C4.342 1.674 4 1.179 4 .5a.5.5 0 0 1 .146-.354m1.58 1.408-.002-.001zm-.002-.001.002.001A.5.5 0 0 1 6 2v5a.5.5 0 0 1-.276.447h-.002l-.012.007-.054.03a5 5 0 0 0-.827.58c-.318.278-.585.596-.725.936h7.792c-.14-.34-.407-.658-.725-.936a5 5 0 0 0-.881-.61l-.012-.006h-.002A.5.5 0 0 1 10 7V2a.5.5 0 0 1 .295-.458 1.8 1.8 0 0 0 .351-.271c.08-.08.155-.17.214-.271H5.14q.091.15.214.271a1.8 1.8 0 0 0 .37.282"/></svg><?php echo do_shortcode("[velocity-iklan-taxonomy taxonomy='lokasi']"); ?>
                                </small>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
            </div>
            <?php global $wp;
                $current_url = home_url( add_query_arg( array(), $wp->request ) );
                $numpages = $the_query->max_num_pages;
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
            ?>
        <?php else: ?>
            <div class="card-body">
                <div class="alert alert-warning m-0">Belum ada produk disini</div>
            </div>
        <?php endif; ?>

</div>

<?php 
/* Restore original Post Data */
wp_reset_postdata(); 
?>