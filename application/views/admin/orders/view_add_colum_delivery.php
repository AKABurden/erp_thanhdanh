<div class="modal-dialog" style="width: 70%;" id="modal_add_colum">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('Thêm cột giao hàng') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <span class="bold"><?= lang('Đơn hàng') ?>:</span><span><?= $order['reference_no'] ?></span>
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
                            <th class="text-center"><?= lang('Mã thành phẩm') ?></th>
                            <th class="text-center"><?= lang('Tên thành phẩm') ?></th>
                            <th class="text-center hide_size hide"><?= lang('Chọn cột 1') ?></th>
                            <th class="text-center hide_size hide"><?= lang('Chọn cột 2') ?></th>
                            <th class="text-center hide_size1 hide"><?= lang('Chọn cột 3') ?></th>
                            <th class="text-center hide_size1 hide"><?= lang('Chọn cột 4') ?></th>
                            <th class="text-center"><?= lang('tnh_unit') ?></th>
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

                                $colum_vt1 = $value['colum_delivery1'];
                                $colum_vt2 = $value['colum_delivery2'];

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
                                $thSub1 = '<option value=""></option>';
                                $thSub2 = '<option value=""></option>';
                                if (!empty($productsColumns)) {
                                    foreach ($productsColumns as $k => $v) {
                                        $thSub1 .= '<option ' .( $value['id'] . '|_|' . $v['name'] == $value['id']. '|_|' . $colum_vt1 ? 'selected' : '' ). ' value="' . $value['id'] . '|_|' . $v['name']. '">' . $v['name'] . '</option>';
                                        $thSub2 .= '<option ' .( $value['id'] . '|_|' . $v['name'] == $value['id']. '|_|' . $colum_vt2 ? 'selected' : '' ). ' value="' . $value['id'] . '|_|' . $v['name']. '">' . $v['name'] . '</option>';
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
                                    <td style="width: 150px">
                                        <?= $info['code'] ?>
                                    </td>
                                    <td class="text-left" style="width: 250px">
                                        <?= $info['name'] ?>
                                    </td>
                                    <td class="text-center hide_size hide">
                                        <div class="form-group " style="width: 100%;">
                                            <select class="VT1" name="VT1[]" id="VT1" style="width: 100%;" data-width="100%" data-placeholder="<?php echo _l('cột 1'); ?>" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <?= $thSub1 ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td class="text-center hide_size hide">
                                        <div class="form-group " style="width: 100%;">
                                            <select class="VT2" name="VT2[]" id="VT2" style="width: 100%;"  data-width="100%" data-placeholder="<?php echo _l('cột 2'); ?>" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <?= $thSub2 ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td class="text-center hide_size1 hide">
                                        <div class="form-group " style="width: 100%;">
                                            <select class="VT3" name="VT3[]" id="VT3" style="width: 100%;"  data-width="100%" data-placeholder="<?php echo _l('cột 3'); ?>" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <?= $thSub ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td class="text-center hide_size1 hide">
                                        <div class="form-group " style="width: 100%;">
                                            <select class="VT4" name="VT4[]" id="VT4" style="width: 100%;"  data-width="100%" data-placeholder="<?php echo _l('cột 4'); ?>" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <?= $thSub ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td class="text-center" style="width: 100px;"><?= $unit['unit'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="printTemOrders()"><?= lang('Lưu lại') ?></button>
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
            alert_float('danger', 'Vui lòng chọn mặt hàng cần thêm cột giao hàng');
            return;
        }
        type_print = $('input[name="type_print"]:checked').val();

        var url = site.base_url + 'admin/orders/save_colum_delivery';

        var inputs = '';
        inputs += `<input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">`;
        inputs += `<input type="hidden" name="p_id" value="${pId}">`;
        inputs += `<input type="hidden" name="order_id" value="<?= $id ?>">`;
        inputs += `<input type="hidden" name="type_print" value="${type_print}">`;
        inputs += `<input type="hidden" name="vt1" value="${vt1}">`;
        inputs += `<input type="hidden" name="vt2" value="${vt2}">`;
        inputs += `<input type="hidden" name="vt3" value="${vt3}">`;
        inputs += `<input type="hidden" name="vt4" value="${vt4}">`;
        $("#form-tem-hidden").html('<form action="' + url + '" method="post" id="poster-detail-1">' + inputs + '</form>');

        var formData = new FormData();
        var formParams = $("#poster-detail-1").serializeArray();

        $.each(formParams, function(i, val) {
            formData.append(val.name, val.value);
        });
        $.ajax({
            url : url,
            type : 'POST',
            dataType: 'JSON',
            cache : false,
            contentType : false,
            processData : false,
            data: formData,
        })
            .done(function(data) {
                if (data.result) {
                    alert_float('success',data.message);
                    $('.modal-dialog .close').trigger('click');
                } else {
                    alert_float('danger',data.message);
                }
            })
            .fail(function() {
                alert_float('danger', lang_core['errors']);
                $('.add-order').removeAttr('disabled', 'disabled');
            });
        return false;
        // $("#poster-detail-1").submit();
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