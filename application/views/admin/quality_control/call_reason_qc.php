<?php echo form_open('admin/orders/calPriceOrders', array('id' => 'cal-price-orders')); ?>
<style type="text/css">
.modal-dialog {
    overflow-y: initial !important
}

.content-price {
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}

/* .tableFixHead          { overflow-y: auto; height: 100px; }
    .tableFixHead thead  th { position: sticky; top: 0; } */
</style>
<div class="modal-dialog modal-lg" style="width: 50%;">
    <div class="modal-content">
        <div class="modal-header">
            <?php $title = '';
            if ($type == 1) {
                $title = 'Tái chế SL: '.$quantity;
            } elseif ($type == 2) {
                $title = 'Phế SL: '.$quantity;
            }
            ?>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= _l('Nguyên nhân lỗi'); ?> <span class="hide"
                    style="color: red"><?= $title ?></span></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 hide">
                    <span style="color: red;text-transform: uppercase">Tổng số lượng: <span
                            class="qty_all">0</span></span>
                </div>
                <input type="hidden" name="type" class="type" value="<?= $type ?>">
                <input type="hidden" name="quantity_check" class="quantity_check" value="<?= $quantity ?>">
                <div class="col-md-12">
                    <div class="content-price">
                        <table id="tb-quote_norm" class="table  tableFixHead dataTable table-cs-border"
                            style="margin-top: 10px !important;">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px"><?= lang('tnh_numbers') ?></th>
                                    <th class="text-center"><?= lang('Tên nguyên nhân') ?></th>
                                    <th class="text-center" style="width: 150px"><?= lang('Số lượng ') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data_json)) { ?>
                                <?php $i = 0;
                                foreach ($data_json as $key => $value): ?>
                                <tr>
                                    <td class="td-number-quote text-center"><?= ++$key ?></td>
                                    <td class="td-name-quote text-left"><?= $value['reason_name'] ?>
                                        <input type="hidden" name="reason_id[]" class="form-control reason_id"
                                            value="<?= $value['reason_id'] ?>">
                                        <input type="hidden" name="reason_name[]" class="form-control reason_name"
                                            value="<?= $value['reason_name'] ?>">
                                    </td>
                                    <td class="td-qty-quote"><input type="text" name="quantity_quote[]"
                                            onchange="totalCalPrices()" class="form-control quantity_quote money-format"
                                            value="<?= $value['quantity_quote'] ?>">
                                    </td>
                                </tr>
                                <?php $i++; endforeach; ?>
                                <?php } else { ?>
                                <?php if (!empty($reasons)) { ?>
                                <?php foreach ($reasons as $key => $value) { ?>
                                <tr>
                                    <td class="td-number-quote text-center"><?= ++$key ?></td>
                                    <td class="td-name-quote text-left"><?= $value['name'] ?>
                                        <input type="hidden" name="reason_id[]" class="form-control reason_id"
                                            value="<?= $value['id'] ?>">
                                        <input type="hidden" name="reason_name[]" class="form-control reason_name"
                                            value="<?= $value['name'] ?>">
                                    </td>
                                    <td class="td-qty-quote"><input type="text" name="quantity_quote[]"
                                            onchange="totalCalPrices()" class="form-control quantity_quote money-format"
                                            value="0">
                                    </td>
                                </tr>
                                <?php } ?>
                                <?php } ?>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="cal_item_id" id="cal_item_id" class="form-control cal_item_id"
                value="<?= $item_id ?>">
            <input type="hidden" name="save" id="save" class="form-control" value="1">
            <span class="btn btn-primary" style="float: right;" onclick="handlingCalPrice()"><?= lang('Chọn') ?></span>
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            &nbsp;
            <!-- <button type="submit" class="btn btn-primary add"><?= _l('save') ?></button> -->
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
init_selectpicker();
<?php if (!empty($data_json)){ ?>
totalCalPrices();
<?php } ?>

function totalCalPrices() {

    //quote
    currency_rate_price = intVal($('#currency_rate_price').val());
    var tbQuote = '#tb-quote_norm tbody tr';
    var nQuote = $(tbQuote).length;
    var totalQuote = 0;
    var quantity_check = $(".quantity_check").val();
    var type_check = $(".type").val();
    qty_check = 0;
    count_errors = 0;
    for (iQuote = 0; iQuote < nQuote; iQuote++) {
        elQuote = $(tbQuote)[iQuote];
        quantity_quote = intVal($(elQuote).find('.quantity_quote').val());
        qty_check += quantity_quote;

    }
    // if (qty_check > quantity_check) {
    //     alert('Số lượng chi tiết phải nhỏ hơn ' + quantity_check);
    //     count_errors++;
    // }
    if (qty_check == 0) {
        alert('Số lượng chi tiết phải lớn hơn 0 ');
        count_errors++;
    }
    $('.qty_all').html(tnhFormatNumber(qty_check));
    //end quote

}


function handlingCalPrice() {
    if (count_errors > 0) {
        alert_float('danger', lang_core['check_date_enter']);
        return;
    }
    var type_check = $(".type").val();
    var form = $('#cal-price-orders'),
        formData = new FormData(),
        formParams = form.serializeArray();

    $.each(formParams, function(i, val) {
        formData.append(val.name, val.value);
    });
    qty_all = intVal($(".qty_all").html());

    $.ajax({
            url: site.base_url + 'admin/quality_control/handlingCalReason',
            type: 'POST',
            dataType: 'JSON',
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
        })
        .done(function(data) {
            if (data.dataJSonReason) {
                if (type_check == 1) {
                    cTrChonse.find('.data_json_taiche').val(data.dataJSonReason);
                    cTrChonse.find('.qty_json_taiche').val(qty_all);
                } else if (type_check == 2) {
                    cTrChonse.find('.data_json_phe').val(data.dataJSonReason);
                    cTrChonse.find('.qty_json_phe').val(qty_all);
                }

                alert_float('success', '<?= lang('success') ?>');
                $('.modal-dialog .close').trigger('click');
                totalCheckQuality();
            } else {
                if (type_check == 1) {
                    cTrChonse.find('.data_json_taiche').val('');
                    cTrChonse.find('.qty_json_taiche').val(0);
                } else if (type_check == 2) {
                    cTrChonse.find('.data_json_phe').val('');
                    cTrChonse.find('.qty_json_phe').val(0);
                }
                alert_float('success', '<?= lang('errors') ?>');
                totalCheckQuality();
            }
        })
        .fail(function() {
            alert_float('danger', 'error');
        });
    return false;
}
</script>