<div id="quote_shipping_unit_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <?php echo form_open(admin_url('quote_shipping_unit/detail/' . (!empty($quote_shipping_unit) ? $quote_shipping_unit->id : '')),
            ['id' => 'from_quote_shipping_unit']); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?= !empty($title) ? $title : '' ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <table class="tnh-tb table-bordered table-hover">
                    <tbody>
                        <tr>
                            <td style="width: 15%;">
                                <?= lang('Ngày', 'date') ?>
                            </td>
                            <td style="width: 35%;">
                                <input type="text" name="date" id="date" class="form-control datetimepicker"
                                       value="<?=!empty($quote_shipping_unit) ? _dt($quote_shipping_unit->date) : _dt(date('Y-m-d H:i:s'))?>"
                                >
                            </td>
                            <td style="width: 15%;">
								<?= lang('Nhà Cung Cấp', 'id_supplier') ?>
                            </td>
                            <td style="width: 35%;">
                                <select name="id_supplier" id="id_supplier" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98" data-placeholder="<?= lang('Nhà Cung Cấp') ?>" class="selectpicker">
                                    <option></option>
                                    <?php if(!empty($list_suppliers)) {
                                        foreach($list_suppliers as $supplier) { ?>
                                            <?php $selected = (!empty($quote_shipping_unit) && $quote_shipping_unit->id_supplier == $supplier['id']) ? 'selected' : ''?>
                                            <option value="<?=$supplier['id']?>" <?=$selected?>><?=$supplier['company']?></option>
                                        <?php }
                                    }?>
                                </select>
                            </td>

                        </tr>
                        <tr>
                            <td style="width: 15%;">
								<?= lang('Mã Chuyến', 'code') ?>
                            </td>
                            <td style="width: 35%;">
                                <input type="text" name="code" id="code" class="form-control" readonly
                                       value="<?=!empty($quote_shipping_unit) ? ($quote_shipping_unit->code) : ''?>">
                            </td>
                            <td style="width: 15%;">
                                <?= lang('Tên Chuyến', 'name') ?>
                            </td>
                            <td style="width: 35%;">
                                <input type="text" name="name" id="name" class="form-control"
                                       value="<?=!empty($quote_shipping_unit) ? $quote_shipping_unit->name : ''?>"
                                >
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 15%;">
								<?= lang('Đơn Giá', 'price') ?>
                            </td>
                            <td style="width: 35%;">
                                <input type="text" name="price" id="price" class="form-control"
                                       value="<?=!empty($quote_shipping_unit) ? $quote_shipping_unit->price : ''?>"
                                >
                            </td>
                            <td style="width: 15%;">
                                <?= lang('Đơn Vị Tính', 'unit_id') ?>
                            </td>
                            <td style="width: 35%;">
                                <?php $value = !empty($quote_shipping_unit) ? $quote_shipping_unit->unit_id : ''?>
                                <?php echo render_select('unit_id', (!empty($list_unit) ? $list_unit : []), ['unitid', 'unit', 'code_unit'], '', $value);?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 15%;">
								<?= lang('Đơn Vị Thanh Toán', 'currencies') ?>
                            </td>
                            <td style="width: 35%;">
                                <?php $value = !empty($quote_shipping_unit) ? $quote_shipping_unit->id_currencies : ''?>
                                <?php echo render_select('id_currencies', (!empty($list_currencies) ? $list_currencies : []), ['id', 'name', 'symbol'], '', $value);?>
                            </td>
                            <td style="width: 15%;">
								<?= lang('Ghi Chú', 'note') ?>
                            </td>
                            <td style="width: 35%;">
                                <?php $value = !empty($quote_shipping_unit) ? $quote_shipping_unit->note : ''?>
                                <?php echo render_textarea('note', '', $value);?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-info add"><?php echo _l('submit'); ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<script type="text/javascript">
    init_selectpicker();
    init_datepicker();
    $('#quote_shipping_unit_modal').modal('show');

    appValidateForm($('#from_quote_shipping_unit'), {
        id_supplier:'required',
        name:'required',
    }, addFrom);

    function addFrom(form) {
        $('.add').attr('disabled', 'disabled');
        var data = $(form).serializeArray();
        var url = form.action;
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            data: data,
        })
        .done(function(data) {
            alert_float(data.alert_type, data.message);
            if (data.success) {
                if (typeof oTable != 'undefined') {
                    oTable.draw();
                }
                $('#quote_shipping_unit_modal').modal('hide');
                $('.add').removeAttr('disabled', 'disabled');
            }
            else {
                alert_float('danger', data.message);
                $('.add').removeAttr('disabled', 'disabled');
            }
        })
        .fail(function() {
            console.log("error");
        });
        return false;
    }
</script>