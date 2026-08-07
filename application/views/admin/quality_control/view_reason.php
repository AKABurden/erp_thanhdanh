<?php echo form_open('admin/orders/calPriceOrders', array('id' => 'cal-price-orders')); ?>
<style type="text/css">
.modal-dialog {
    overflow-y: initial !important
}

.content-price {
    max-height: calc(100vh - 300px);
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
            <h4 class="modal-title"><?= _l('Nguyên nhân lỗi'); ?> <span style="color: red"><?= $title ?></span></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
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
                                $qty = 0;
                                $stt = 1;
                                foreach ($data_json as $key => $value): ?>
                                <?php if ($value['quantity_quote'] != 0) { ?>
                                <tr>
                                    <td class="td-number-quote text-center"><?= $stt ?></td>
                                    <td class="td-name-quote text-left"><?= $value['reason_name'] ?>
                                        <input type="hidden" name="reason_id[]" class="form-control reason_id"
                                            value="<?= $value['reason_id'] ?>">
                                        <input type="hidden" name="reason_name[]" class="form-control reason_name"
                                            value="<?= $value['reason_name'] ?>">
                                    </td>
                                    <td class="td-qty-quote text-center">
                                        <?= $value['quantity_quote'] ?>
                                    </td>
                                </tr>
                                <?php $stt++;
                                    } ?>
                                <?php $qty += $value['quantity_quote'];
                                    $i++; endforeach; ?>
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
            <input type="hidden" name="cal_item_id" id="cal_item_id" class="form-control cal_item_id" value="">
            <input type="hidden" name="save" id="save" class="form-control" value="1">
            <input type="hidden" name="qty_check" id="qty_check" class="form-control" value="<?= $qty ?>">
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
    var qty_check = $("#qty_check").val();
    $('.qty_all').html(tnhFormatNumber(qty_check));

}
</script>