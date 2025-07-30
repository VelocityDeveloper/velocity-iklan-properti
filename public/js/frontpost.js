jQuery(document).ready(function($) {
    var galleryFrame;
    var gallerySelection = [];
    var nodeid = 0;
    var metakey;
    var multiple = 'add';
  
    // Frame  
    if (typeof wp.media !== 'undefined') {
  
        // Add
        jQuery('.fpmedia-add').click(function(e) {
            nodeid = $(this).data('node');
            metakey = $(this).data('metakey');
            multiple = $(this).data('multiple');
            e.preventDefault();          
      
            galleryFrame = wp.media.frames.mysite_gallery_frame = wp.media({
                title: 'Select Image',
                button: {
                    text: 'Insert Image'
                },
                library: {
                    type: 'image'
                },
                multiple: multiple,
            });

            galleryFrame.open();  
            
            var arrayImages = [];
            jQuery('.fpmedia-image-'+nodeid).each(function(index, element) {
                var idd = jQuery(this).data('id');
                if(idd) {
                    arrayImages.push(idd);
                    galleryFrame.state().get('selection').add(wp.media.attachment(idd));
                    // galleryFrame.state().get('selection').reset( idd ? [ wp.media.attachment( idd ) ] : [] );
                }      
            });

            galleryFrame.on('select', function() {
              	gallerySelection = [];
                gallerySelection = galleryFrame.state().get('selection');

                jQuery(".row-fpmedia-"+nodeid).empty();

                gallerySelection.map(function(attachment) {
                    // console.log(attachment);
                    
                    if (attachment.attributes.url !== 'undefined') {
                        //var nodeid = Math.random().toString(36).substring(7);
                        var id          = attachment.id;
                        var url         = (attachment.attributes.sizes !== undefined)?attachment.attributes.sizes.thumbnail.url:attachment.attributes.url;
                        var inputname   = multiple==='add'?metakey+'[]':metakey;

                        jQuery(".row-fpmedia-"+nodeid).append(`<div class="fpmedia-col fpmedia-image-${nodeid}" data-id="${id}"><input name="${inputname}" value="${id}" type="hidden"><img src="${url}" alt=""><span data-id="${id}" class="fpmedia-del dashicons dashicons-no-alt"></span></div>`);
                    }
                });
                // galleryFrame.close();
            });
            
        });
      
        // Remove
        jQuery('.row-fpmedia').on('click', '.fpmedia-del', function() {
            var imageWrapper = jQuery(this).parents('.fpmedia-col');
            imageWrapper.remove();
        });
  
        // Sortable
        jQuery('.row-fpmedia-multiple').sortable();
        jQuery('.row-fpmedia-multiple').disableSelection();  
  
    }

    function getOptionkota(nodeid){
        var val         = $(`.${nodeid} .alamat-provinsi`).val();
        var datavalue   = $(`.${nodeid} .alamat-kota`).data('value');
        if(val) {
            $(`.${nodeid} .part-kota label`).append('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
            jQuery.ajax({
                type    : "POST",
                url     : velocitymp.ajaxUrl,
                data    : {action:'optionkota',provid:val,datavalue:datavalue},
                success :function(data) {
                    $(`.${nodeid} .part-kota select`).html(data);
                    $(`.${nodeid} .part-kota label .fa`).remove();                     
        
                    if(data) {
                        getOptionkecamatan(nodeid);
                    }

                },
            }); 
        } else {
            $(`.${nodeid} .alamat-kota`).html('<option value="">Pilih Kota</option>');
        }

    }
    
    function getOptionkecamatan(nodeid){
        var val         = $(`.${nodeid} .alamat-kota`).val();
        var datavalue   = $(`.${nodeid} .alamat-kecamatan`).data('value');
        if(val) {
            $(`.${nodeid} .part-kecamatan label`).append('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
            jQuery.ajax({
                type    : "POST",
                url     : velocitymp.ajaxUrl,
                data    : {action:'optionkecamatan',cityid:val,datavalue:datavalue},
                success :function(data) {
                    $(`.${nodeid} .part-kecamatan select`).html(data);
                    $(`.${nodeid} .part-kecamatan label .fa`).remove();
                },
            }); 
        } else {
            $(`.${nodeid} .alamat-kecamatan`).html('<option value="">Pilih Kecamatan</option>');
        }
    }

    $('.alamat-provinsi').ready(function($) {
        var nodeid = $('.alamat-provinsi').data('node');
        var datavalue = $('.alamat-provinsi').data('value');
        var defkota = $(`.${nodeid} .alamat-kota`).data('value');

        $(`.${nodeid} .part-provinsi label`).append('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
        jQuery.ajax({
            type    : "POST",
            url     : velocitymp.ajaxUrl,
            data    : {action:'optionprovinsi',datavalue:datavalue},
            success :function(data) {
                $(`.${nodeid} .part-provinsi select`).html(data);
                $(`.${nodeid} .part-provinsi label .fa`).remove();

                //get default kota
                if(datavalue){
                    getOptionkota(nodeid);
                }
                
            },
        }); 
        
    });    
    
    $('.alamat-provinsi').change(function() {
        var nodeid  = jQuery(this).data('node');
        getOptionkota(nodeid);
        var val     = jQuery(this).val();
        var name    = $(`.${nodeid} .alamat-provinsi :selected`).text();
        name        = name?name:'';
        $(`.${nodeid} .alamat-provinsi-name`).val(name); 
    });

    $('.alamat-kota').change(function() {
        var nodeid  = jQuery(this).data('node');
        getOptionkecamatan(nodeid);
        var val     = jQuery(this).val();
        var name    = $(`.${nodeid} .alamat-kota :selected`).text();
        name        = name?name:'';
        $(`.${nodeid} .alamat-kota-name`).val(name); 
    });
    
    $('.alamat-kecamatan').change(function() {
        var nodeid  = jQuery(this).data('node');
        var val     = jQuery(this).val();
        var name    = $(`.${nodeid} .alamat-kecamatan :selected`).text();
        name        = name?name:'';
        $(`.${nodeid} .alamat-kecamatan-name`).val(name);
    });

    $(document).on('click','.btn-clone-add', function() {
        var nodeid  = $(this).data('node');
        var clone   = $(`.fields-${nodeid} .item-cloneable:first`).clone();
        clone.find('input').val('');
        $(`.fields-${nodeid} .list-cloneable`).append(clone);
        var count   = $(`.fields-${nodeid} .item-cloneable`).length;
        if(count > 1) {
            $(`.fields-${nodeid} .list-cloneable`).addClass('list-cloneable-multiple');
        }
    });
    
    // Remove
    $(document).on('click','.btn-clone-del', function() {
        var nodeid = $(this).data('node');
        
        if($(`.fields-${nodeid} .item-cloneable`).length > 1) {
            jQuery(this).parents('.item-cloneable').remove();
        }
        if($(`.fields-${nodeid} .item-cloneable`).length === 1) {
            $(`.fields-${nodeid} .list-cloneable`).removeClass('list-cloneable-multiple');
        }
    });

});
