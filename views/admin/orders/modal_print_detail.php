<div class="modal fade in" id="view_print_qr" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <style>
        .items_show .btn.dropdown-toggle.btn-default {
            height: 100%!important;
        }
    </style>
    <div class="modal-dialog no-modal-header" style="min-width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <span class="book-title"><?= !empty($title) ? $title : '' ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <?php echo $content;?>
            </div>
            <div class="clearfix"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" onclick="print_qr_code_show(<?=$id?>)"><?= _l('In') ?></button>
                <button type="button" class="btn btn-danger" data-dismiss="modal"><?= _l('cong_exit') ?></button>
            </div>
        </div>
    </div>
</div>
<script>
    init_selectpicker();
    $('body').on('change', 'input[name="print_type"]', function(e) {
        if($('input[name="print_type"]:checked').val() == 2) {
            $('.items_show').removeClass('hide');
        }
        else {
            $('.items_show').addClass('hide');
        }
    })

    $('body').on('click', '.removeTrItem', function(e) {
        $(this).parents('tr').remove();
    })


    function print_qr_code_show(id) {
       var type_print =  $('input.check_print:checked');
       var items_product = [];
       var type_get = '?type=1';
       $.each(type_print, function(index, value) {
           var id = $(value).val();
           var quantity = $('#quantity_' + id).val();
           if(quantity && quantity > 0) {
                type_get += '&id_detail[' + index + ']=' + id + '&quantity[' + id + ']=' + quantity;
           }
       })
        var url = admin_url + 'orders/print_qr_code_html/' + id + type_get;
        printPdf(url);
    }
    function printPdf(url) {
        var iframe = document.createElement('iframe');
        // iframe.id = 'pdfIframe'
        iframe.className='pdfIframe'
        document.body.appendChild(iframe);
        iframe.style.display = 'none';
        iframe.onload = function () {
            setTimeout(function () {
                iframe.focus();
                iframe.contentWindow.print();
                URL.revokeObjectURL(url)
                // document.body.removeChild(iframe)
            }, 1);
        };
        iframe.src = url;
    }
</script>