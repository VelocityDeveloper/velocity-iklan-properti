jQuery(document).ready(function($) {
    // konfirmasi produk premium
    $(document).on('click','.konfirmasi-premium', function() {
        var idp = $(this).attr('id');
        var aksi = $(this).html();
        if(idp){
            $(this).html('<div class="spinner-grow spinner-grow-sm"><span class="visually-hidden">Loading...</span></div>');
            jQuery.ajax({
                type    : "POST",
                url     : velocityiklan.ajaxurl,
                data    : {action:'konfirmasipremium',id:idp,confirm:aksi},
                success :function(data) {
					if(aksi == 'Hapus'){
                    	$('.tr-'+idp).remove();
					} else {
                    	$('.aksi-'+idp).html('Diterima');
						$('.tr-'+idp).css('background', '#d6f3d6');
					}
                },
            }); 
        }
    });
});