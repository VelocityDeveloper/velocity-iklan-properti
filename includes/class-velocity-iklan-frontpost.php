<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


class Frontpost {
    
    public static $metakey = [
        'places'        => [
            'type'      => 'taxonomy',
            'title'     => 'Kategori Tempat',
            'desc'      => '',
            'required'  => false,
        ],
        'post_title'    => [
            'type'      => 'text',
            'title'     => 'Nama',
            'desc'      => 'Nama tempat',
            'required'  => true,
        ],
        '_thumbnail_id'=> [
            'type'      => 'featured',
            'title'     => 'Gambar',
            'desc'      => 'Foto Utama',
            'required'  => true,
        ],
        'post_content'  => [
            'type'      => 'textarea',
            'title'     => 'Deskripsi',
            'desc'      => '',
            'required'  => false,
        ],
        'nowa'          => [
            'type'      => 'text',
            'title'     => 'Nomor Whatsapp',
            'desc'      => '',
            'required'  => true,
        ],
        'nohp'          => [
            'type'      => 'text',
            'title'     => 'Nomor Handphone',
            'desc'      => '',
            'required'  => true,
        ],
        'alamat'        => [
            'type'      => 'alamat',
            'title'     => 'Detail Alamat',
            'desc'      => '',
            'required'  => true,
        ],
        'alamat-lengkap'=> [
            'type'      => 'textarea',
            'title'     => 'Alamat Lengkap',
            'desc'      => '',
            'required'  => false,
        ],
        // 'post_category' => [
        //     'type'      => 'category',
        //     'title'     => 'Kategori',
        //     'desc'      => '',
        //     'required'  => false,
        // ],
    ];
    
    public static function slugify($text) {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        
        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        
        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        
        // trim
        $text = trim($text, '-');
        
        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);
        
        // lowercase
        $text = strtolower($text);
        
        if (empty($text)) {
        return 'n-a';
        }
        return $text;
    }
    
    public static function submitPost($args,$post_type=null) {
        
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );
        
        $message    = [];
        $success    = '';
        
        if($args) {
        	$my_post['post_title']      = $args['post_title'];
        	$my_post['post_content']    = $args['post_content'];
        	$my_post['post_status']     = 'publish'; 
			if($post_type){
        		$my_post['post_type']   = $post_type;
			}
        	$my_post['post_author']     = $args['post_author'];
        	$my_post['post_slug']       = self::slugify($args['post_title']);
        	
        	if(isset($args['ID'])&&$args['ID']):
        	  $my_post['ID']            = $args['ID'];  
        	endif;
        	
        	if(isset($args['post_category'])&&$args['post_category']):
        	  $my_post['post_category'] = $args['post_category'];  
        	endif;
        	
    	    $pid = wp_insert_post( $my_post, true );
    	    
    	    //save meta post
    	    foreach ($args as $id => $value) {
                if($id!='post_title' || $id!='post_content' || $id!='post_category' || $id!='taxonomy' || $id!='-upload-file' ) {
                    update_post_meta($pid, $id, $value);
                }
                //taxonomy
                if($id=='taxonomy'){
                    foreach($value as $tax => $tag ) {
                        wp_set_post_terms($pid, $tag, $tax);
                    }
                }
                //-upload-file
                if($id=='-upload-file'){
                    foreach($value as $idfile => $val ) {
			    if(isset($val['name'])&&$val['name']):
			    //upload file
			    $attachment_id = media_handle_upload( $idfile, $pid);
			    //delete previous file 
			    if (get_post_meta($pid, $idfile,true)) {
				wp_delete_attachment( get_post_meta($pid, $idfile,true) );
			    }
		    	    //update to meta user
			    update_post_meta( $pid, $idfile, $attachment_id);
			    endif;
                    }
                }
            }
    	    $message[] = '<div class="alert alert-success"><a href="'.get_permalink( $pid ).'" target="_blank">'.$my_post['post_title'].'</a> Berhasil Diterbitkan.</div>';
        } else {
            $message[] = '<div class="alert alert-danger">Parameter Kosong</div>';
        }
        
        
        $result             = [];
        $result['message']  = $message;
        $result['success']  = $success;
        
        return $result;
		
    }
    
    public static function hapusPost($id=null) {
        if($id) {
	    ///get all attachment by id post
            $args = array(
        		'post_type'     => 'attachment',
        		'post_status'   => null,
        		'post_parent'   => $id,
        		); 
        	$attachments = get_posts($args);
            if(count($attachments) > 1) {
                foreach ($attachments as $attachment ) {
                    //delete attachment
                    wp_delete_attachment( $attachment->ID );
                } 
            }
            ///deleted data post
            wp_delete_post( $id, true );
            return true;
        } else {
            return false;
        }
    }
    
    public static function formPost($args=null,$action=null,$arraymeta=null) {
        
        $post_author    = isset($args['ID'])&&!empty(get_post_field('post_author',$args['ID']))?get_post_field('post_author',$args['ID']):get_current_user_id();
        $arraymeta      = !empty($arraymeta)?$arraymeta:self::$metakey;
        
        $antispam       = true;
        
        //check antispam
        if(isset($_POST['g-recaptcha-response']) && empty($_POST['g-recaptcha-response'])) {
            $antispam   = false;
        }
        
        ///submit data
        if(isset($_POST['inpudata']) && $antispam==true) {
	        if(isset($_FILES)&&!empty($_FILES)) {
                $_POST['-upload-file'] = $_FILES;
            }
            $result = self::submitPost($_POST,$args['post_type']);
            echo implode(" ",$result['message']);
        } else if(isset($_POST['inpudata']) && $antispam==false) {
            echo '<div class="alert alert-danger">Please verify Antispam</div>';
        }

        echo '<form name="input" method="POST" class="frontpost" id="formPost" action="" enctype="multipart/form-data">';
        
            echo '<input type="hidden" id="post_author" value="'.$post_author.'" name="post_author">';
            
            ///edit data
            if( $action=='edit' && $args['ID']) {
                echo '<input type="hidden" id="id" value="'.$args['ID'].'" name="ID" readonly>';
            }
            
            ///post type
            if( isset($args['post_type']) && $args['post_type']) {
                echo '<input type="hidden" id="id" value="'.$args['post_type'].'" name="post_typea" readonly>';
            }
            
            //Loop
        	foreach ($arraymeta as $idmeta => $fields ) {
				
				$nodeid 		= uniqid();
				$cloneable		= (isset($fields['clone']) && $fields['clone']==true)?'cloneable':'';
				$placeholder	= (isset($fields['placeholder']))?$fields['placeholder']:'';
        	    
        		echo '<div class="form-group mb-3 fields-'.$idmeta.' fields-'.$nodeid.' '.$cloneable.'" data-node="'.$nodeid.'">';

        		    $reqstar	= (isset($fields['required']) && $fields['required']==true)?'*':'';

        			if (isset($fields['required']) && $fields['required']==true) { $req = 'required'; } else { $req = ''; }
        			if (isset($fields['readonly']) && $fields['readonly']==true) { $read = 'readonly'; } else { $read = ''; }
        			
        			//get value
        			if ($action=='edit' && $args['ID']) {         			    
						if ($idmeta == 'post_content') {
							$value    = get_post_field('post_content', $args['ID']);
						} else if ($idmeta == 'post_title') {
							$value    = get_the_title($args['ID']);
						} else {
							$value = get_post_meta( $args['ID'], $idmeta , true );
						}
        			} elseif (isset($fields['default']) && ($action=='add')) { 
        			    $value = $fields['default']; 
        			} else { 
        			    $value = '';
        			}
        			
        			$condition  = '';
        			$condition2 = '';

            	    //show label             		    
                    if ($fields['type']!=='hidden' && empty($condition2)) {
                        echo '<label for="'.$idmeta.'" class="font-weight-bold">'.$fields['title'].$reqstar.'</label>';
                    }
                    
                    //show field
            		 if (empty($condition)) {
            			
            			//type input text
            			if ($fields['type']=='text') {
							if($cloneable) {

								$metaval = [];
								if($value&&is_array($value)){
									$metaval = $value;
								} else if($value&&!is_array($value)){
									$metaval[0] = $value;
								} else {
									$metaval[0] = ' ';
								}

								echo '<div class="list-cloneable">';
									foreach ($metaval as $keymv => $datamv) {
										echo '<div class="item-cloneable mb-1 d-flex align-items-center">';
											echo '<input type="text" id="'.$idmeta.'" value="'.$datamv.'" class="form-control '.$cloneable.'" name="'.$idmeta.'[]" placeholder="'.$placeholder.'" '.$req.' '.$read.'>';
											echo '<span class="btn btn-sm btn-danger ml-1 btn-clone-del" data-node="'.$nodeid.'"> <i class="fa fa-trash"></i> </span>';
										echo '</div>';
									}
								echo '</div>';
								echo '<div class="button-cloneable text-right">';
									echo '<span class="btn btn-sm btn-info btn-clone-add" data-node="'.$nodeid.'"> <i class="fa fa-plus"></i> Add</span>';
								echo '</div>';
							} else {
								echo '<input type="text" id="'.$idmeta.'" value="'.$value.'" class="form-control" name="'.$idmeta.'" placeholder="'.$placeholder.'" '.$req.' '.$read.'>';
							}
            			}
            			//type input number
            			if ($fields['type']=='number') {
            				echo '<input type="number" id="'.$idmeta.'" value="'.$value.'" class="form-control w-100" name="'.$idmeta.'" placeholder="'.$placeholder.'" '.$req.' '.$read.'>';
            			}
            			//type input textarea
            			if ($fields['type']=='textarea') {
            				echo '<textarea id="'.$idmeta.'" class="form-control" name="'.$idmeta.'" '.$req.' '.$read.'>'.$value.'</textarea>';
            			} 
            			//type input editor
            			if ($fields['type']=='editor') {
            				wp_editor( $value, $idmeta );
            			} 
            			//type input email
            			else if ($fields['type']=='email') {
            				echo '<input type="email" id="'.$idmeta.'" value="'.$value.'" pattern="[^ @]*@[^ @]*" class="form-control" name="'.$idmeta.'" placeholder="'.$placeholder.'" '.$req.' '.$read.'>';
            			} 
            			//type input date
            			else if ($fields['type']=='date') {
            				echo '<input type="date" id="'.$idmeta.'" value="'.$value.'" class="form-control datepicker" name="'.$idmeta.'" '.$req.' '.$read.'>';
            			}  
            			//type input password
            			else if ($fields['type']=='password') {
            				echo '<input type="password" id="'.$idmeta.'" class="form-control" value="'.$value.'" name="'.$idmeta.'" '.$req.'>';
            			} 
            			//type input option
            			else if ($fields['type']=='select') {
            				echo '<select id="'.$idmeta.'" class="form-control" name="'.$idmeta.'" '.$req.'>';
            					foreach ($fields['options'] as $option1 => $option2 ) {
            					    $option1 = is_numeric($option1)?$option2:$option1;
            						echo '<option value="'.$option1.'"';
            						if ($value==$option1) { echo 'selected';}
            						echo '>'.$option2.'</option>';
            					}
            				echo '</select>';
            			}
            			
            			//type input checkbox
            			else if ($fields['type']=='checkbox') {
							$val = $value?$value:[];
							foreach ($fields['option'] as $option1 => $option2 ) {
								$option1	= is_numeric($option1)?$option2:$option1;
								$stringname	= str_replace(' ', '', $option2);
								$checked	= in_array($option1, $val)?'checked':'';
								echo '
								<div class="form-check">
									<input class="form-check-input" type="checkbox" value="'.$option1.'" name="'.$idmeta.'[]" id="Check'.$stringname.'" '.$checked.'>
									<label class="form-check-label" for="Check'.$stringname.'">
										'.$option2.'
									</label>
								</div>
								';
							}
            			}
				 
            			//type input category
            			else if ($fields['type']=='category') {
            				echo '<select id="'.$idmeta.'" class="form-control" name="'.$idmeta.'" '.$req.'>';
                				$categories = get_categories( 
                                    array(
                            			'orderby' => 'name',
                            			'parent'  => 0,
                            			'hide_empty' => 0,
                            		) 
                            	);
                            	
                            	$val = isset($value[0])?$value[0]:[0];
                			    if(isset($args['ID'])&&$args['ID']):   
                                    $taxonomy_ids     = wp_get_object_terms($args['ID'], array($idmeta),  array("fields" => "ids"));
                                    $val              = $taxonomy_ids;
                                endif;
                                
            					foreach ($categories as $category ) {
            					    $seletc = in_array($category->term_id,$val)?'selected':'';
            						echo '<option value="'.$category->term_id.'" '.$seletc.'>'.$category->name.'</option>';
            						//child
            						$taxonomies         = array('taxonomy'=>'category');
            						$categories_child   = array('child_of'=> $category->term_id); 
            						$terms              = get_terms($taxonomies, $categories_child);
            						if (sizeof($terms)>0){
                            			foreach ( $terms as $term ) {
                            			    $seletc = in_array($term->term_id,$val)?'selected':'';
                            			    echo '<option value="'.$term->term_id.'" '.$seletc.'>&nbsp;&nbsp;'.$term->name.'</option>';
                            			}
            						}
            					}
            				echo '</select>';
            			}
            			
            			//type input taxonomy
            			else if ($fields['type']=='taxonomy') {
            			    
            			    $val = isset($value[0])?$value[0]:[0];
            			    if(isset($args['ID'])&&$args['ID']):   
                                $taxonomy_ids     = wp_get_object_terms($args['ID'], array($idmeta),  array("fields" => "ids"));
                                $val              = $taxonomy_ids;
                            endif;
            			    
            				echo '<select id="'.$idmeta.'" class="form-control" name="taxonomy['.$idmeta.'][]" '.$req.'>';
            						//taxonomy
            						$categories = get_categories( array('taxonomy' => $idmeta,'hide_empty'=> false,'parent' => 0,));
            						if (sizeof($categories)>0){
                                        echo '<option value="">Pilih '.$fields['title'].'</option>';
                            			foreach ( $categories as $category ) {
                            			    $seletc = in_array($category->term_id,$val)?'selected':'';
                            			    echo '<option value="'.$category->term_id.'" '.$seletc.'>'.$category->name.'</option>';
                    						//child
                    						$taxonomies         = array('taxonomy'=>$idmeta);
                    						$categories_child   = array('child_of'=> $category->term_id); 
                    						$terms              = get_terms($taxonomies, $categories_child);
                    						if (sizeof($terms)>0){
                                    			foreach ( $terms as $term ) {
                                    			    $seletc = in_array($term->term_id,$val)?'selected':'';
                                    			    echo '<option value="'.$term->term_id.'" '.$seletc.'>&nbsp;&nbsp;'.$term->name.'</option>';
                                    			}
                    						}
                            			}
            						}
            				echo '</select>';
            			}
            			
            			//type input featured
            			else if ($fields['type']=='featured') {
            			    
                            echo '<div class="frontpost-featured">';
                                echo '<div class="row-fpmedia row-fpmedia-'.$nodeid.'">';                                
                                    if($value){
                                        $idattc = $value;
                                        echo '<div class="fpmedia-col fpmedia-image-'.$nodeid.'" data-id="'.$idattc.'">';
                                            echo '<input name="'.$idmeta.'" value="'.$idattc.'" type="hidden">';
                                            echo '<img src="'.wp_get_attachment_image_url($idattc,'large').'"/>';
                                            echo '<span class="fpmedia-del dashicons dashicons-no-alt" data-id="'.$idattc.'"></span>';
                                        echo '</div>';
                                    }
                                echo '</div>';
                                echo '<span data-node="'.$nodeid.'" data-multiple="false" data-metakey="'.$idmeta.'" class="btn btn-secondary btn-sm text-white fpmedia-add" >Tambah Gambar</span>';
                            echo '</div>';

            				// $src = ($value && wp_get_attachment_url($value))?wp_get_attachment_image_src($value, 'thumbnail')[0]:'https://dummyimage.com/200x200/d6d6d6/ffffff&text=no+image';
            				// echo '<div class="file-upload img-frame mb-2"><img class="prevg ganti-'.$idmeta.'" src="'.$src.'" width="80"/></div>';
            				// echo '<input type="file" id="'.$idmeta.'" class="form-control-file imgchange" class-target="ganti-'.$idmeta.'" name="'.$idmeta.'" '.$req.' '.$read.'>';
            			}
            			
				        //type input file
            			if ($fields['type']=='file') {
            			    
            				if($value && wp_get_attachment_url($value)) {
            				    echo '<a href="'.wp_get_attachment_url($value).'" target="_blank" class="d-block my-2"><i class="fa fa-file fa-2x"></i></a>';
            				}
            				
            				echo '<input type="file" id="'.$idmeta.'" class="form-control-file" name="'.$idmeta.'" '.$req.' '.$read.'>';
            			}

                        // gallery
                        // untuk sementara hanya bisa 1 gallery 
                        if($fields['type'] == 'gallery') {							
                            echo '<div class="frontpost-gallery">';
                                echo '<div class="row-fpmedia row-fpmedia-multiple row-fpmedia-'.$nodeid.'">';
                                    if($value){
                                        foreach ($value as $key => $idattc) {
                                            echo '<div class="fpmedia-col fpmedia-image-'.$nodeid.'" data-id="'.$idattc.'">';
                                                echo '<input name="'.$idmeta.'[]" value="'.$idattc.'" type="hidden">';
                                                echo '<img src="'.wp_get_attachment_thumb_url($idattc).'"/>';
                                                echo '<span class="fpmedia-del dashicons dashicons-no-alt" data-id="'.$idattc.'"></span>';
                                            echo '</div>';
                                        }
                                    }
                                echo '</div>';
                                echo '<span data-node="'.$nodeid.'" data-multiple="add" data-metakey="'.$idmeta.'" class="btn btn-secondary btn-sm text-white fpmedia-add" >Tambah Gambar</span>';
                            echo '</div>';
                        }
            			
            			//type input hidden
            			if ($fields['type']=='hidden') {
            				echo '<input type="hidden" id="'.$idmeta.'" value="'.$value.'" name="'.$idmeta.'">';
            			}
            		
            		    	//type recaptcha
            			if ($fields['type']=='recaptcha' && !empty($fields['sitekey']) && !empty($fields['secret'])) {
            			    echo '<div class="'.$idmeta.' text-right">';
                            echo '<div id="'.$idmeta.'" class="g-recaptcha" data-callback="checkCaptcha" data-expired-callback="expiredCaptcha" data-sitekey="'.$fields['sitekey'].'"></div>';
                            echo '<div id="msg'.$idmeta.'"> </div>';
                                echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
                            echo '</div>';
            			}
            		
            			if (isset($fields['desc'])&&!empty($fields['desc'])) {
            				echo '<small class="text-secondary text-muted">*'.$fields['desc'].'</small>';				
            			}
        	        }
        		echo '</div>';
        	}
        	//END Loop
        	
    	    echo '<div class="text-right my-3"><button name="inpudata" type="submit" class="btn btn-info simpanUserbaru1"><i class="fa fa-floppy-o" aria-hidden="true"></i> Simpan</button></div>';
	    echo '</form>';	

		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		wp_enqueue_style('thickbox');
		wp_enqueue_script( 'velocityfrontpost', VELOCITY_IKLAN_PLUGIN_URL . 'public/js/frontpost.js');
		
		if(isset($args['ID']) && !empty($args['ID'])) {
			wp_enqueue_media(array(
				'post' => $args['ID'],
			));
		}
		
    }
    
    ///Tampil profil
    public static function lihatPost($idpost=null,$arraymeta=null) {
        
		//print_r($arraymeta);

        if(!empty($idpost)):

            $arraymeta = !empty($arraymeta)?$arraymeta:self::$metakey;
            
            echo '<table class="table table-lihatPost">';
        	foreach ($arraymeta as $idmeta => $fields) {
        	    
        		$value = get_post_meta($idpost,$idmeta,true);
        		
    			echo '<tr class="fields-'.$idmeta.'">';	
    				echo '<td class="font-weight-bold">'.$fields['title'].'</td>';
			
    				if ($fields['type']=='option') {
    					foreach ($fields['option'] as $option1 => $option2 ) {
    						if ($value==$option1) { echo '<td>'.$option2.'</td>';}
    					}
					
					} else if($fields['type']=='geolocation')  {
					    $latitude   = isset($value[0])?$value[0]:'';
					    $longitude  = isset($value[1])?$value[1]:'';
					    echo '<td>';
        				    if(!empty($latitude)&&!empty($longitude)): ?>
            				        <div id="<?= $idmeta;?>-frame-map"></div>
            				        <script>
            				            (function($){
            				            $( document ).ready(function() {
            				                 $('#<?= $idmeta;?>-frame-map').height(350);
                                             var mapOptions = {
                                                center: [<?= $latitude;?>, <?= $longitude;?>],
                                                zoom: 15,
                                             }
                                             var map = new L.map('<?= $idmeta;?>-frame-map', mapOptions);
                                             var layer = new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');
                                             map.addLayer(layer);
                                             var marker = L.marker([<?= $latitude;?>, <?= $longitude;?>]);
                                             marker.addTo(map);
            				            });
            				            })(jQuery);
            				        </script>
            				    <?php
            				    endif;
					    echo '</td>';
					} else if($fields['type']=='file')  {
            				$file = ($value && wp_get_attachment_url($value))?'<a href="'.wp_get_attachment_url($value).'" target="_blank" class="d-block my-2"><i class="fa fa-file fa-2x"></i></a>':'';
        				    echo '<td>'.$file.'</td>';
						
					} else if($fields['type']=='alamat')  {
        			        $provinsi       = isset($value[0][0])?$value[0][0]:'';
        			        $provinsiname   = isset($value[0][1])?$value[0][1]:'';
        			        $kota           = isset($value[1][0])?$value[1][0]:'';
        			        $kotaname       = isset($value[1][1])?$value[1][1]:'';
        			        $kecamatan      = isset($value[2][0])?$value[2][0]:'';
        			        $kecamatanname  = isset($value[2][1])?$value[2][1]:'';
        				    echo '<td>'.$kecamatanname.', '.$kotaname.', '.$provinsiname.'</td>';
    				}  else  {
    					echo '<td>'.$value.'</td>';
    				}	
			
    			echo '</tr>';
    			
        	}
        	echo '</table>';
            
        endif;
    }
    
    
    ///Tampil profil
    public static function lihatPostAdmin($idpost=null,$arraymeta=null) {
        
		//print_r($arraymeta);

        if(!empty($idpost)):

            $arraymeta = !empty($arraymeta)?$arraymeta:self::$metakey;
            
            echo '<table class="table form-table">';
        	foreach ($arraymeta as $idmeta => $fields) {
        	    
				if ($fields['type']=='taxonomy' || $idmeta=='post_title' || $idmeta=='post_content' || $fields['type']=='featured') {
					continue;
				}

				$clonable = isset($fields['clone'])?$fields['clone']:false;

        		$value = get_post_meta($idpost,$idmeta,true);
        		
    			echo '<tr class="fields-'.$idmeta.'">';	
    				echo '<th scope="row">'.$fields['title'].'</th>';
			
    				if ($fields['type']=='option') {
    					foreach ($fields['option'] as $option1 => $option2 ) {
    						if ($value==$option1) { echo '<td>'.$option2.'</td>';}
    					}					
					} else if($fields['type']=='geolocation')  {
					    $latitude   = isset($value[0])?$value[0]:'';
					    $longitude  = isset($value[1])?$value[1]:'';
					    echo '<td>';
        				    if(!empty($latitude)&&!empty($longitude)): ?>
            				        <div id="<?= $idmeta;?>-frame-map"></div>
            				        <script>
            				            (function($){
            				            $( document ).ready(function() {
            				                 $('#<?= $idmeta;?>-frame-map').height(350);
                                             var mapOptions = {
                                                center: [<?= $latitude;?>, <?= $longitude;?>],
                                                zoom: 15,
                                             }
                                             var map = new L.map('<?= $idmeta;?>-frame-map', mapOptions);
                                             var layer = new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');
                                             map.addLayer(layer);
                                             var marker = L.marker([<?= $latitude;?>, <?= $longitude;?>]);
                                             marker.addTo(map);
            				            });
            				            })(jQuery);
            				        </script>
            				    <?php
            				    endif;
					    echo '</td>';
					} else if($fields['type']=='file')  {
            				$file = ($value && wp_get_attachment_url($value))?'<a href="'.wp_get_attachment_url($value).'" target="_blank" class="d-block my-2"><i class="fa fa-file fa-2x"></i></a>':'';
        				    echo '<td>'.$file.'</td>';
						
					} else if($fields['type']=='alamat')  {
        			        $provinsi       = isset($value[0][0])?$value[0][0]:'';
        			        $provinsiname   = isset($value[0][1])?$value[0][1]:'';
        			        $kota           = isset($value[1][0])?$value[1][0]:'';
        			        $kotaname       = isset($value[1][1])?$value[1][1]:'';
        			        $kecamatan      = isset($value[2][0])?$value[2][0]:'';
        			        $kecamatanname  = isset($value[2][1])?$value[2][1]:'';
        				    echo '<td>'.$kecamatanname.', '.$kotaname.', '.$provinsiname.'</td>';

					} else if($fields['type']=='featured')  {
						
    					echo '<td>';
							echo wp_get_attachment_image_url($value,'large');
    					echo '</td>';

					} else if($fields['type']=='gallery')  {
						
    					echo '<td>';
						if($value){
							echo '<ol>';
							foreach ($value as $key => $idattc) {
								echo '<li>';
									echo '<img src="'.wp_get_attachment_thumb_url($idattc).'"/>';
								echo '</li>';
							}
							echo '</ol>';
						}
    					echo '</td>';

    				}  else if($fields['type']=='text' && $clonable )  {
						
    					echo '<td>';
						if($value){
							echo '<ol>';
							foreach ($value as $key => $datamv) {
								echo '<li>'.$datamv.'</li>';
							}
							echo '</ol>';
						}
    					echo '</td>';

					}  else  {
    					echo '<td>'.$value.'</td>';
    				}	
			
    			echo '</tr>';
    			
        	}
        	echo '</table>';
            
        endif;
    }
    
}
