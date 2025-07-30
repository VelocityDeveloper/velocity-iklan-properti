<?php 
get_header();
$container = velocitytheme_option('justg_container_type', 'container');
?>

<div class="container py-3">
    <h1 class="vmpc-title"><?php echo vmpc_title(); ?></h1>
    <?php
    $object = get_queried_object();
    $args_premium = array(
        'post_type'         => 'iklan',
        'orderby'           => 'rand',
        'posts_per_page'    => 4,
        'showposts'         => 4,
        'tax_query' => array(
            array(
                'taxonomy' => $object->taxonomy,
                'field' => 'slug',
                'terms' => $object->slug, 
            ),
        ),
        'meta_query'        => array(
            array(
                'key'     => 'jenis',
                'value'   => 'premium',
                'compare' => '=',
            ),
        ),
    );
    $premium_posts = get_posts($args_premium);
    if ($premium_posts) {
        echo '<div class="row border border-secondary bg-light mt-4 mx-0 mb-4 pt-4 pb-0 px-2">';
        foreach ($premium_posts as $premium) {
            echo '<div class="col-md-3 col-6 mb-4">';
                echo do_shortcode('[velocity-iklan-loop post_id="'.$premium->ID.'"]');
            echo '</div>';
        }
        echo '</div>';
    }

    if (have_posts()) {
        // Start the loop.
        echo '<div class="row">';
        while (have_posts()) {
            the_post();
            echo '<div class="col-md-3 col-6 mb-4">';
                echo do_shortcode('[velocity-iklan-loop]');
            echo '</div>';
        }
        echo '</div>';
        justg_pagination();
    } else {
        get_template_part('loop-templates/content', 'none');
    }
    ?>
</div>

<?php
get_footer();