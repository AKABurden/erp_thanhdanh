<div class="modal-dialog" style="width: 70%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('In Tem') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <span class="bold"><?= lang('tnh_reference_orders') ?>:</span><span><?= $order['reference_no'] ?></span>
                </div>
                <div class="col-md-12">
                    <!-- <div class="checkbox checkbox-info" style="float: left; padding-left: 5px;">
                        <input type="radio" name="type_print" id="print_normal" checked value="1">
                        <label for="print_normal"><?= lang('tnh_print_normal') ?></label>
                    </div> -->
                    <div class="checkbox checkbox-info" style="float: left; padding-left: 5px;">
                        <input type="radio" name="type_print" id="print_normal" checked value="3">
                        <label for="print_normal"><?= lang('ch_print_odd') ?></label>
                    </div>
                    <div class="checkbox checkbox-info" style="float: left; margin-top: 9px;">
                        <input type="radio" name="type_print" id="print_fixe" value="4">
                        <label for="print_fixe"><?= lang('ch_print_fixed') ?></label>
                    </div>
                    <div class="checkbox checkbox-info" style="float: left; margin-top: 9px;">
                        <input type="radio" name="type_print" id="print_size" value="5">
                        <label for="print_size"><?= lang('ch_print_size') ?></label>
                    </div>
                    <div class="checkbox checkbox-info" style="float: left; margin-top: 9px;">
                        <input type="radio" name="type_print" id="print_to_customer" value="2">
                        <label for="print_to_customer"><?= lang('tnh_print_to_customer') ?></label>
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
                                <th class="text-center hide_size hide"><?= lang('Chọn cột VT1') ?></th>
                                <th class="text-center hide_size hide"><?= lang('Chọn cột VT2') ?></th>
                                <th class="text-center hide_size hide"><?= lang('Chọn cột VT3') ?></th>
                                <th class="text-center hide_size hide"><?= lang('Chọn cột VT4') ?></th>
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


                                    $colum_vt1 = '';
                                    $colum_vt2 = '';
                                    $colum_vt3 = '';
                                    $colum_vt4 = '';

                                    $info = [];
                                    if ($type_item == "products") {
                                        $info = $this->products_model->rowProduct($items_id);
                                        $unit = $this->unit_model->rowUnit($value['unit_id']);
                                        if (!empty($info['images'])) {
                                            $images = base_url('uploads/products/' . $info['images']);
                                        }
                                        $colum_vt1 = $info['colum_vt1'];
                                        $colum_vt2 = $info['colum_vt2'];
                                        $colum_vt3 = $info['colum_vt3'];
                                        $colum_vt4 = $info['colum_vt4'];

                                    } else if ($type_item == "materials") {
                                        $info = $this->items_model->rowMaterial($items_id);
                                        $unit = $this->unit_model->rowUnit($info['unit_id']);
                                        if (!empty($info['images'])) {
                                            $images = base_url('uploads/materials/' . $info['images']);
                                        }
                                    }
                                    $productsColumns = $this->products_model->getProductsColumns($items_id);
                                    $thSub1 = '<option value=""></option>';
                                    $thSub2 = '<option value=""></option>';
                                    $thSub3 = '<option value=""></option>';
                                    $thSub4 = '<option value=""></option>';
                                    if (!empty($productsColumns)) {
                                        foreach ($productsColumns as $k => $v) {
                                            $thSub1 .= '<option '.( $value['id']. '|_|' . $v['name'] == $value['id']. '|_|' . $colum_vt1 ? 'selected' : '' ).' value="' . $value['id'] . '|_|' . $v['name'] . '">' . $v['name'] . '</option>';
                                            $thSub2 .= '<option '.( $value['id']. '|_|' . $v['name'] == $value['id']. '|_|' . $colum_vt2 ? 'selected' : '' ).' value="' . $value['id'] . '|_|' . $v['name'] . '">' . $v['name'] . '</option>';
                                            $thSub3 .= '<option '.( $value['id']. '|_|' . $v['name'] == $value['id']. '|_|' . $colum_vt3 ? 'selected' : '' ).' value="' . $value['id'] . '|_|' . $v['name'] . '">' . $v['name'] . '</option>';
                                            $thSub4 .= '<option '.( $value['id']. '|_|' . $v['name'] == $value['id']. '|_|' . $colum_vt4 ? 'selected' : '' ).' value="' . $value['id'] . '|_|' . $v['name'] . '">' . $v['name'] . '</option>';
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td class="text-center">
                                            <div class="checkbox checkbox-info">
                                                <input type="checkbox" name="order_item_id[]" value="<?= $value['id'] ?>">
                                                <label for="order_item_id_<?= $value['id'] ?>"></label>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?= $info['name'] ?>(<?= $info['code'] ?>)
                                        </td>
                                        <td class="text-center hide_size hide">
                                            <div class="form-group " style="width: 100px;">
                                                <select class="VT1" name="VT1[]" id="VT1" style="width: 100%;" data-width="100%" data-placeholder="<?php echo _l('VT1'); ?>" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <?= $thSub1 ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="text-center hide_size hide">
                                            <div class="form-group " style="width: 100px;">
                                                <select class="VT2" name="VT2[]" id="VT2" style="width: 100%;"  data-width="100%" data-placeholder="<?php echo _l('VT2'); ?>" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <?= $thSub2 ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="text-center hide_size hide">
                                            <div class="form-group " style="width: 100px;">
                                                <select class="VT3" name="VT3[]" id="VT3" style="width: 100%;"  data-width="100%" data-placeholder="<?php echo _l('VT3'); ?>" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <?= $thSub3 ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="text-center hide_size hide">
                                            <div class="form-group " style="width: 100px;">
                                                <select class="VT4" name="VT4[]" id="VT4" style="width: 100%;"  data-width="100%" data-placeholder="<?php echo _l('VT4'); ?>" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <?= $thSub4 ?>
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
    $('.VT3').select2();
    $('.VT4').select2();

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
        vt3 = '';
        iLengthvt3 = $('select[name="VT3[]"]').length;
        if (iLengthvt3) {
            $.each($('select[name="VT3[]"]'), function(index, value) {
                vt3_id = $(value).val();
                vt3 += vt3_id + '_____';
            });
            if (vt3) {
                vt3 = vt3.substring(0, vt3.length - 5);
            }
        }

        vt4 = '';
        iLengthvt4 = $('select[name="VT4[]"]').length;
        if (iLengthvt4) {
            $.each($('select[name="VT4[]"]'), function(index, value) {
                vt4_id = $(value).val();
                vt4 += vt4_id + '_____';
            });
            if (vt4) {
                vt4 = vt4.substring(0, vt4.length - 5);
            }
        }

        if (!pId) {
            alert_float('danger', 'Vui lòng chọn mặt hàng In tem');
            return;
        }
        type_print = $('input[name="type_print"]:checked').val();

        var url = site.base_url + 'admin/orders/get_print_tem';

        var inputs = '';
        inputs += `<input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">`;
        inputs += `<input type="hidden" name="p_id" value="${pId}">`;
        inputs += `<input type="hidden" name="order_id" value="<?= $id ?>">`;
        inputs += `<input type="hidden" name="type_print" value="${type_print}">`;
        inputs += `<input type="hidden" name="vt1" value="${vt1}">`;
        inputs += `<input type="hidden" name="vt2" value="${vt2}">`;
        inputs += `<input type="hidden" name="vt3" value="${vt3}">`;
        inputs += `<input type="hidden" name="vt4" value="${vt4}">`;
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
</script>