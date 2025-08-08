<?php
/**
 * Theme basic setup.
 *
 * @package vsstemmart
 */
    global $post;
    $s          = isset($_GET['s'])?$_GET['s']:'';
    $listshort  = isset($_GET['short'])?$_GET['short']:'';  
    $tipe       = isset($_GET['tipe'])?$_GET['tipe']:'';
    $kamar      = isset($_GET['kamar'])?$_GET['kamar']:'';
    $minprice   = isset($_GET['minprice'])?$_GET['minprice']:'';
    $maxprice   = isset($_GET['maxprice'])?$_GET['maxprice']:'';
	
    $category = get_queried_object();
    $cat = is_a($category, 'WP_Term') ? $category->term_id : '';
    $listkategori = isset($_GET['kat']) ? $_GET['kat'] : $cat;
    $listlokasi = isset($_GET['lok']) ? $_GET['lok'] : '';

    $kategori_terms = get_terms(array(
        'taxonomy' => 'kategori',
        'hide_empty' => false,
    ));

    $lokasi_terms = get_terms(array(
        'taxonomy' => 'lokasi',
        'hide_empty' => false,
    ));
    ?>
    <div class="card">
        <div class="card-header bg-white text-dark fs-5 fw-bold">
            Filter Produk
        </div>
        <div class="card-body">
            <form action="<?php echo get_post_type_archive_link('iklan');?>">
                <div class="form-group mb-3">
                    <label class="text-colortheme fw-bold d-block mb-1">Cari Properti</label>
                    <input type="text" class="form-control" name="s" placeholder="Cari.." value="<?php echo $s; ?>"/>
                </div>
                <div class="form-group mb-3">
                    <label class="text-colortheme fw-bold d-block mb-1">Rentang Harga</label>
                    <div class="row mb-1">
                        <div class="col-2 pe-0"><small>Min</small></div>
                        <div class="col-10">
                            <input type="number" class="form-control form-control-sm" name="minprice" value="<?php echo $minprice; ?>"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2 pe-0"><small>Max</small></div>
                        <div class="col-10">
                            <input type="number" class="form-control form-control-sm" name="maxprice" value="<?php echo $maxprice; ?>"/>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label class="text-colortheme fw-bold d-block mb-1">Urutkan</label>
					<select class="form-control" name="short">
                    <?php 
                    $shorting = array(
                        'Terbaru'   => 'baru', 
                        'Terlama'   => 'lama', 
                        'Termurah'  => 'murah', 
                        'Termahal'  => 'mahal', 
                        'Nama A-Z'  => 'namaa', 
                        'Nama Z-A'  => 'namaz'
                    );
                    foreach($shorting as $key => $val){ ?>
						<option value="<?php echo $val; ?>" <?php if($val == $listshort){echo 'selected';}; ?>><?php echo $key; ?></option>
                    <?php } ?>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label class="text-colortheme fw-bold d-block mb-1">Berdasarkan Kategori</label>
                    <select class="form-control" name="kat">
                        <option value="">Semua Kategori</option>
                        <?php foreach($kategori_terms as $kat): ?>
                            <option value="<?php echo esc_attr($kat->term_id ); ?>" <?php selected($listkategori, $kat->term_id ); ?>>
                                <?php echo esc_html($kat->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label class="text-colortheme fw-bold d-block mb-1">Berdasarkan Lokasi</label>
                    <select class="form-control" name="lok">
                        <option value="">Semua Lokasi</option>
                        <?php foreach($lokasi_terms as $lok): ?>
                            <option value="<?php echo esc_attr($lok->term_id); ?>" <?php selected($listlokasi, $lok->term_id); ?>>
                                <?php echo esc_html($lok->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label class="text-colortheme fw-bold d-block mb-1">Tipe</label>
					<select class="form-control" name="tipe">
						<option value="">Semua Tipe</option>
                    <?php 
                    $tipe_args = array('Dijual','Disewakan');
                    foreach($tipe_args as $tipe_value){ ?>
						<option value="<?php echo $tipe_value; ?>" <?php if($tipe_value == $tipe){echo 'selected';}; ?>><?php echo $tipe_value; ?></option>
                    <?php } ?>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label class="text-colortheme fw-bold d-block mb-1">Jumlah Kamar</label>
					<input type="number" class="form-control" name="kamar" value="<?php echo $kamar; ?>"/>
                </div>
                <button type="submit" class="btn btn-dark"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
            </form>
        </div>
    </div>
