jQuery(function($){
    var bi_url = document.location.origin + "/wpe/";
    var wphub_invoice = {
        init:function(){
            $(document).on("click",'a.invoice-preview',{view:this},this.invoice_preview);
        },
        invoice_set_url: function(uri,key,value){
            var re = new RegExp("([?|&])" + key + "=.*?(&|$)", "i");
            var separator = uri.indexOf("?") !== -1 ? "&" : "?";
            if (uri.match(re)) {
                return uri.replace(re, "$1" + key + "=" + value + "$2");
            } else {
                return uri + separator + key + "=" + value;
            }
        },
        invoice_preview:function(e){
            e.stopPropagation();
            e.preventDefault();
            var temp = $('#wphub_invoice_template').val();
            var url = wphub_invoice.invoice_set_url(wphub_pdf.ajax,'template',temp);
            window.open(url);
        },
    };
    wphub_invoice.init();
});