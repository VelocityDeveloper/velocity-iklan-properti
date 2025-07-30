jQuery(document).ready(function($) {
    // pengajuan product
    $(document).on('click','.velocity-premium-button', function() {
        var idp = $(this).attr('id');
        if(idp){
            $(this).html('<div class="spinner-grow spinner-grow-sm"><span class="visually-hidden">Loading...</span></div>');
            jQuery.ajax({
                type    : "POST",
                url     : velocityiklan.ajaxurl,
                data    : {action:'iklanpremium',id:idp},
                success :function(data) {
                    $('.card-product-'+idp).addClass('bg-light border-dark');
                    $('#btn-'+idp).remove();
                },
            }); 
        }
    });
    // Remove product
    $(document).on('click','.btn-product-delete', function() {
        var idp = $(this).attr('id');
        if(idp){
            if (confirm("Hapus produk ini?") == true) {
                $(this).html('<div class="spinner-grow spinner-grow-sm"><span class="visually-hidden">Loading...</span></div>');
                jQuery.ajax({
                    type    : "POST",
                    url     : velocityiklan.ajaxurl,
                    data    : {action:'deleteproduct',id:idp},
                    success :function(data) {
                        $('.card-product-'+idp).addClass('border border-danger');   
                        setTimeout(function() {
                            $('.product-'+idp).remove();
                        }, 1700);
                    },
                }); 
            }
        }
    });
});