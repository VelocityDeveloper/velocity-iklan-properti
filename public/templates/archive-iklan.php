<?php 
get_header();
$container = velocitytheme_option('justg_container_type', 'container');
$terms = get_terms(array(
    'taxonomy' => 'kategori',
    'hide_empty' => true,
    'parent' => 0,
));
?>
<div class="container py-3">
    <?php if(!empty($terms)) { ?>
    <div class="row m-0 border bg-white vmpc-kategori-list">
        <div class="col-md-3 ps-0 ps-md-3 pe-0 pe-md-0 py-md-3 bg-light border-end">
            <?php $i = 0;
                echo '<div class="nav nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">';
                    foreach ($terms as $term) {
                        $id = $term->term_id;
                        $no = ++$i;
                        $class = $no == 1 ? " active" : '';
                        echo '<div class="w-100 rounded-0 nav-link'.$class.'" id="v-pills-'.$id.'-tab" data-bs-toggle="pill" data-bs-target="#v-pills-'.$id.'" type="button" role="tab" aria-controls="v-pills-'.$id.'" aria-selected="true">';
                            echo $term->name;
                        echo '</div>';
                    }
                echo '</div>';
            ?>
        </div>
        <div class="col-md-9 py-3 px-md-3">
            <?php $i = 0;
                echo '<div class="tab-content" id="v-pills-tabContent">';
                    foreach ($terms as $term) {
                        $id = $term->term_id;
                        $no = ++$i;
                        $class = $no == 1 ? " show active" : '';
                        $child_terms = get_terms(array(
                            'taxonomy' => 'kategori',
                            'hide_empty' => true,
                            'parent' => $id,
                        ));
                        $childs = '';
                        $jml = array();
                        if (!empty($child_terms)) {
                            foreach ($child_terms as $child) {
                                $childs .= '<div class="col-6 col-md-3 mb-3 px-1"><a href="'.get_term_link($child->term_id).'">'
                                .$child->name.'</a></div>';
                                $jml[] = $child->count;
                            }
                        }
                        echo '<div class="tab-pane fade '.$class.'" id="v-pills-'.$id.'" role="tabpanel" aria-labelledby="v-pills-'.$id.'-tab">';
                            echo '<div class="row">';
                                echo '<div class="col-3 text-center">';
                                    echo velocity_term_image($term->term_id,'large',array('class'=>'mb-2 velocity-term-image'));
                                echo '</div>';
                                echo '<div class="col-9 lh-sm">';
                                    echo '<h5 class="fs-5">'.$term->name.'</h5>';
                                    echo '<small class="d-block mb-2">'.array_sum($jml).' iklan</small>';
                                    echo '<a href="'.get_term_link($term->term_id).'">Lihat semua iklan kategori ini >></a>';
                                echo '</div>';
                            echo '</div>';
                            if (!empty($child_terms)) {
                                echo '<div class="row m-0 border-top border-bottom pt-3 mt-3">';
                                    echo $childs;
                                echo '</div>';
                            }
                        echo '</div>';
                    }
                echo '</div>';
            ?>
        </div>
    </div>
    <?php } ?>
</div>

<?php
get_footer();