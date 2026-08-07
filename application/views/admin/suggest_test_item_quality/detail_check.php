<div id="suggest_test_item_quality_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="min-width: 70%;">
        <?php echo form_open(admin_url('suggest_test_item_quality/detail_check/' . $type . '/' . (!empty($suggest_test_item_quality) ? $suggest_test_item_quality->id : '')),
            ['id' => 'from_suggest_test_item_quality']); ?>
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
                                <?= lang('dt_reference_suggest', 'reference_no') ?>
                            </td>
                            <td style="width: 35%;">
                                <div class="form-group">
                                    <input type="text" name="reference_no" class="form-control" id="reference_no"
                                           value="<?= !empty($suggest_test_item_quality) ? $suggest_test_item_quality->code : (!empty($codeDefault) ? $codeDefault : '') ?>" readonly>
                                </div>
                            </td>
                            <td style="width: 15%;">
                                <?= lang('Ngày Kiểm Tra', 'date') ?>
                            </td>
                            <td style="width: 35%;">
                                <?= form_input('date',
                                    set_value('date') ? set_value('date') : !empty($suggest_test_item_quality) ? _dt($suggest_test_item_quality->date) : date('d/m/Y H:i'),
                                    'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required ') ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 15%;"><?= lang($type_object['name_object'], 'id_supplier') ?></td>
                            <td style="width: 35%;"><input name="id_supplier"
                                        id="id_supplier"
                                        class="id_supplier"
                                        data-placeholder="<?= lang($type_object['name_object']) ?>"
                                        style="width: 100%;"
                                        required
                                        value="<?=!empty($suggest_test_item_quality->id_supplier) ? $suggest_test_item_quality->id_supplier : '' ?>">
                            </td>
                            <td style="width: 15%;"><?= lang($type_object['name_po'], 'id_purchase_order') ?></td>
                            <td style="width: 35%;"><input name="id_purchase_order"
                                        id="id_purchase_order"
                                        class="id_purchase_order"
                                        data-placeholder="<?= lang($type_object['name_po']) ?>"
                                        style="width: 100%;"
                                       required
                                        value="<?=!empty($suggest_test_item_quality->id_purchase_order) ? $suggest_test_item_quality->id_purchase_order : '' ?>">
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 15%;"><?= lang(($type == 'products' ? 'Sản Phẩm' : 'Nguyên Phụ Liệu'), 'id_items') ?></td>
                            <td style="width: 35%;">
                                <select id="id_items"
                                        name="id_items[]"
                                        multiple="true"
                                        class="selectpicker" data-width="100%"
                                        data-none-selected-text="Không có mục nào được chọn"
                                        data-actions-box="true"
                                        required
                                        data-live-search="true" tabindex="-98">
                                        <?php if(!empty($items)) {
                                            foreach($items as $key => $value) {?>
                                                <option value="<?=$value['id']?>" <?=(!empty($id_items[$value['id']]) ? 'selected' : '')?> data-subtext="<?=$value['code']?>" data-quantity="<?=$value['quantity']?>"><?=$value['name']?></option>
                                            <?php }
                                        }?>
                                </select>
                            </td>
                            <td style="width: 15%;"><?= lang('Ghi chú', 'note') ?></td>
                            <td style="width: 35%;">
                                <textarea name="note" id="note" class="form-control note" cols="3" rows="4"><?= !empty($dtData) ? $dtData['note'] : '' ?></textarea>
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
    $('#suggest_test_item_quality_modal').modal('show');
    
    init_datepicker();
    init_selectpicker('refresh');
    
    ajaxSelectParams('#id_supplier', 'admin/suggest_test_item_quality/search_supplier_or_supplier/<?=$type_object['tableDataObject']?>', $('#id_supplier').val(), true, true);
    ajaxSelectParams('#id_purchase_order', 'admin/suggest_test_item_quality/search_purchase_order/<?=$type_object['tableDataOrder']?>/' + $('#id_supplier').val(), $('#id_purchase_order').val(), true, true);
    
    $('#id_supplier').change(function() {
        $('#id_purchase_order').val();
        ajaxSelectParams('#id_purchase_order', 'admin/suggest_test_item_quality/search_purchase_order/<?=$type_object['tableDataOrder']?>/' + $('#id_supplier').val(), '', true, true);
    })
    
    $('#id_purchase_order').change(function() {
        $('#id_items').html('');
        $.get(admin_url + 'suggest_test_item_quality/get_items_purchase_order/<?=$type_object['tableDataOrderDetail']?>/<?=$type?>/' + $('#id_purchase_order').val(), function(result) {
            result = JSON.parse(result);
            $.each(result, function(index, value) {
                $('#id_items').append(`<option value="${value.id}" data-quantity="${value.quantity}" data-subtext="${value.code}">${value.name}</option>`);
            })
            $('#id_items').selectpicker('refresh')
        })
    })

    appValidateForm($('#from_suggest_test_item_quality'), {
        date:'required',
        id_supplier: 'required',
        id_purchase_order: 'required',
        id_items: 'required',
    }, addFrom);

    function addFrom(form) {
        $('.add').attr('disabled', 'disabled');
        var data = $(form).serializeArray();
        var url = form.action;
        var list_id_items = $('#id_items').find('option:selected');
        var dataQuantity = {};
        $.each(list_id_items, function(index, value) {
            data.push({'name' : 'quantity['+$(value).val()+']', value: $(value).attr('data-quantity')});
        })
        
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
                $('#suggest_test_item_quality_modal').modal('hide');
                $('.add').removeAttr('disabled', 'disabled');
            } else {
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