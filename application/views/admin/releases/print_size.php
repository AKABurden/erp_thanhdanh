<div class="modal-dialog" style="width: 70%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('In theo size') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <span class="bold"><?= lang('Phiếu giao hàng') ?>:</span><span><?= $delivery['reference_no'] ?></span>
                </div>
                <div class="col-md-12 hide">
                    <div class="checkbox checkbox-info" style="float: left; margin-top: 9px;">
                        <input type="radio" name="type_print" id="print_size" checked value="5">
                        <label for="print_size"><?= lang('ch_print_size') ?></label>
                    </div>
                </div>
                <div class="col-md-12">
                    <table id="table-items" class="table table-items-new">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 80px;">
                                <div class="checkbox mass_select_all_wrap text-center checkbox-info">
                                    <input type="checkbox" id="mass_select_all-items" onclick="checkAll(this)" data-to-table="items-new"><label for="mass_select_all-items"></label>
                                </div>
                            </th>
                            <th class="text-center"><?= lang('Tên / Mã thành phẩm') ?></th>
                            <th class="text-center hide_size hide"><?= lang('Chọn cột size') ?></th>
                            <th class="text-center hide_size hide"><?= lang('Chọn cột barcode') ?></th>
                            <th class="text-center"><?= lang('tnh_unit') ?></th>
                            <th class="text-center"><?= lang('quantity') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($items)) : ?>
                            <?php foreach ($items as $key => $value) : ?>
                                <?php
                                $type_item = $value['type_item'];
                                $items_id = $value['item_id'];

                                $info = [];
                                if ($type_item == "products") {
                                    $info = $this->products_model->rowProduct($items_id);
                                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                                    if (!empty($info['images'])) {
                                        $images = base_url('uploads/products/' . $info['images']);
                                    }
                                } else if ($type_item == "materials") {
                                    $info = $this->items_model->rowMaterial($items_id);
                                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                                    if (!empty($info['images'])) {
                                        $images = base_url('uploads/materials/' . $info['images']);
                                    }
                                }
                                $productsColumns = $this->products_model->getProductsColumns($items_id);
                                $thSub = '<option value=""></option>';
                                if (!empty($productsColumns)) {
                                    foreach ($productsColumns as $k => $v) {
                                        $thSub .= '<option value="' . $value['id_delivery_item'] . '|_|' . $v['name'] . '">' . $v['name'] . '</option>';
                                    }
                                }
                                ?>
                                <tr>
                                    <td class="text-center">
                                        <div class="checkbox checkbox-info">
                                            <input type="checkbox" name="order_item_id[]" value="<?= $value['id_delivery_item'] ?>">
                                            <label for="order_item_id_<?= $value['id_delivery_item'] ?>"></label>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?= $info['name'] ?>(<?= $info['code'] ?>)
                                    </td>
                                    <td class="text-center hide_size hide">
                                        <div class="form-group " style="width: 100%;">
                                            <select class="VT1" name="VT1[]" id="VT1" style="width: 100%;" data-width="100%" data-placeholder="<?php echo _l('size'); ?>" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <?= $thSub ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td class="text-center hide_size hide">
                                        <div class="form-group " style="width: 100%;">
                                            <select class="VT2" name="VT2[]" id="VT2" style="width: 100%;"  data-width="100%" data-placeholder="<?php echo _l('barcode'); ?>" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <?= $thSub ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td class="text-center" style="width: 100px;"><?= $unit['unit'] ?></td>
                                    <td class="text-center" style="width: 100px;"><?= formatNumber($value['quantity']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="printTemOrders()"><?= lang('print') ?></button>
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<div id="form-tem-hidden"></div>
<script type="text/javascript">
    function checkAll(_this) {
        rows = $('#table-items').find('tbody tr');
        checked = $(_this).prop('checked');
        $.each(rows, function(key, val) {
            $(val).find('td').eq(0).find('input').prop('checked', checked);
        });
    }
    $('.VT1').select2();
    $('.VT2').select2();

    function printTemOrders() {
        pId = '';
        iLength = $('input[name="order_item_id[]"]').length;
        if (iLength) {
            $.each($('input[name="order_item_id[]"]'), function(index, value) {
                order_item_id = $(value).prop('checked');
                if (order_item_id) {
                    order_item_id = $(value).val();
                    pId += order_item_id + ',';
                }
            });
            if (pId) {
                pId = pId.substring(0, pId.length - 1);
            }
        }
        vt1 = '';
        iLengthvt1 = $('select[name="VT1[]"]').length;
        if (iLengthvt1) {
            $.each($('select[name="VT1[]"]'), function(index, value) {
                vt1_id = $(value).val();
                vt1 += vt1_id + '_____';
            });
            if (vt1) {
                vt1 = vt1.substring(0, vt1.length - 5);
            }
        }

        vt2 = '';
        iLengthvt2 = $('select[name="VT2[]"]').length;
        if (iLengthvt2) {
            $.each($('select[name="VT2[]"]'), function(index, value) {
                vt2_id = $(value).val();
                vt2 += vt2_id + '_____';
            });
            if (vt2) {
                vt2 = vt2.substring(0, vt2.length - 5);
            }
        }

        if (!pId) {
            alert_float('danger', 'Vui lòng chọn mặt hàng In size');
            return;
        }
        type_print = $('input[name="type_print"]:checked').val();

        var url = site.base_url + 'admin/releases/get_print_size';

        var inputs = '';
        inputs += `<input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">`;
        inputs += `<input type="hidden" name="p_id" value="${pId}">`;
        inputs += `<input type="hidden" name="delivery_id" value="<?= $id ?>">`;
        inputs += `<input type="hidden" name="type_print" value="${type_print}">`;
        inputs += `<input type="hidden" name="vt1" value="${vt1}">`;
        inputs += `<input type="hidden" name="vt2" value="${vt2}">`;
        $("#form-tem-hidden").html('<form target="_blank" action="' + url + '" method="post" id="poster-detail-1">' + inputs + '</form>');
        $("#poster-detail-1").submit();
    }

    $('input[name="type_print"]').on('click', function() {
        type_prints = $('input[name="type_print"]:checked').val();
        $('.hide_size').addClass('hide');
        if(type_prints == 5){
            $('.hide_size').removeClass('hide');
        }
    });
    $('input[name="type_print"]').click();
</script>