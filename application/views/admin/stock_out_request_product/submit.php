<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<style>
    .sidebar {
        display: none;
    }
</style>
<?php echo form_open('admin/stock_out_request_product/submit/' . ($id ?? '') . '', array('id' => 'submit_form')); ?>
<div>
    <div class="panel_s mbot10 H_scroll" id="">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <?= $this->load->view('admin/breadcrumb') ?>
        </div>
    </div>
    <div class="content ae-content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('info') ?></h3>
                    </div>
                    <div class="panel-body">
                        <table class="tnh-tb table-bordered table-hover">
                            <tbody>
                                <tr>
                                    <td style="width: 15%;">
                                        <?= lang('Mã Số Phiếu', 'code') ?>
                                    </td>
                                    <td style="width: 35%;">
                                        <div class="form-group">
                                            <input type="text" name="code" class="form-control" id="code" value="<?= $value['code'] ?? 'Tự động hệ thống' ?>" readonly="" aria-invalid="false">
                                        </div>
                                    </td>
                                    <td style="width: 15%;">
                                        <?= lang('Ngày lập phiếu', 'date') ?>
                                    </td>
                                    <td style="width: 35%;">
                                        <?= form_input('date', (!empty($value['date']) ? _dt($value['date']) : date('d/m/Y H:i')), 'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required ') ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?= lang('tnh_reference_productions_orders', 'production_order_id') ?></td>
                                    <td colspan="1">
                                        <input type="text" name="production_order_id" data-placeholder="<?= lang('tnh_reference_productions_orders') ?>" id="production_order_id" class="production_order_id" style="width: 100%;" value="<?= $value['production_order_id'] ?? '' ?>">
                                    </td>

                                    <td><?= lang('note', 'note') ?></td>
                                    <td colspan="1">
                                        <textarea name="note" id="note" class="form-control note" rows="3"><?= !empty($value) ? $value['note'] : '' ?></textarea>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <!-- <div>
                    <label for="item"><?= lang('Sản phẩm') ?></label>
                    <input type="text" name="item" id="item" style="width: 100%;" data-placeholder="<?= lang('Sản phẩm') ?>" value="">
                </div> -->
                <div class="table-responsive">
                    <table id="tb-items" class="dt-tnh table table-hover" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 30px;"><?= lang('STT') ?></th>
                                <th><?= lang('cong_orders') ?></th>
                                <th><?= lang('materials') ?></th>
                                <th><?= lang('Tên NPL') ?></th>
                                <th><?= lang('unit') ?></th>
                                <th><?= lang('Tổng Số Lượng Sản Xuất') ?></th>
                                <th><?= lang('Số Lượng Tồn Kho') ?></th>
                                <th><?= lang('Số Lượng Cần SX') ?></th>
                                <th><?= lang('Số Lượng Cần Mua') ?></th>
                                <th><?= lang('Tác Vụ') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $counter = 0;
                            if (!empty($value['items'])) {
                                foreach ($value['items'] as $itemRow => $itemValue) {
                                    $imageUrl = base_url('assets/images/tnh/no_image.png');
                                    if (!empty($itemValue['image'])) {
                                        $imageUrl = base_url($itemValue['image']);
                                    }
                            ?>
                                    <tr>
                                        <td class="text-center">
                                            <div class="stt"><?= (++$itemRow) ?></div>
                                        </td>
                                        <td>
                                            <div class="td-order"><input type="text" name="order_id[<?= $counter ?>]" data-placeholder="<?= lang('cong_orders') ?>" class="order_id" id="order_id_<?= $counter ?>" style="width: 100%;" value="<?= $itemValue['order_id'] ?? '' ?>"></div>
                                        </td>
                                        <td>
                                            <div class="code_item">
                                                <input type="hidden" name="counter[]" class="counter" value="<?= $counter ?>">
                                                <input type="hidden" name="item_row_id[<?= $counter ?>]" class="item_row_id" value="<?= $itemValue['id'] ?>">
                                                <input type="text" name="item_id[<?= $counter ?>]" id="item_id_<?= $counter ?>" class="item_id" data-placeholder="<?= lang('materials') ?>" value="<?= $itemValue['item_id'] . '__' . $itemValue['item_type'] ?>">
                                            </div>
                                        </td>
                                        <td class="item_name"><?= $itemValue['name'] ?? '' ?></td>
                                        <td class="item_unit text-center"><?= $itemValue['unit_name'] ?? '' ?></td>
                                        <td>
                                            <div class="production_quantity text-center"><?= formatNumber($itemValue['production_quantity']) ?></div>
                                            <input type="hidden" name="production_quantity[<?= $counter ?>]" id="production_quantity_<?= $counter ?>" class="input_production_quantity" value="<?= $itemValue['production_quantity'] ?? 0 ?>">
                                        </td>
                                        <td>
                                            <div class="stock_quantity text-center">0</div>
                                        </td>
                                        <td>
                                            <div class="production_require_quantity text-center"><?= formatNumber($itemValue['production_require_quantity']) ?></div>
                                            <input type="hidden" name="production_require_quantity[<?= $counter ?>]" id="production_require_quantity_<?= $counter ?>" class="input_production_require_quantity" value="<?= $itemValue['production_require_quantity'] ?? 0 ?>">
                                        </td>
                                        <td>
                                            <div class="purchase_require_quantity text-center"><?= formatNumber($itemValue['purchase_require_quantity']) ?></div>
                                            <input type="text" name="purchase_require_quantity[<?= $counter ?>]" id="purchase_require_quantity_<?= $counter ?>" class="input_purchase_require_quantity number-format form-control" value="<?= $itemValue['purchase_require_quantity'] ?? 0 ?>">
                                        </td>
                                        <td class="text-center"><a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a></td>
                                    </tr>
                            <?php $counter++;
                                }
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="btn-bottom-toolbar btn-toolbar-container-out text-right" style="width: 100%">
            <button type="submit" class="btn btn-info only-save btn-submit">
                <?php echo _l('submit'); ?>
            </button>
        </div>
    </div>
</div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript">
    var counter = <?= $counter ?>;
    var count_errors = 0;
    var csrf_token_name = "<?= $this->security->get_csrf_token_name() ?>";
    var hash = "<?= $this->security->get_csrf_hash() ?>";
    var paramsData = {};
    paramsData[csrf_token_name] = hash;

    $(document).ready(function() {
        ajaxSelectParamsCallback('#client_id', 'admin/clients/searchCustomers/', $('#client_id').val(), false, true);
        ajaxSelectParamsCallback($('#item'), 'admin/quotation_request/searchProductsSelect2', 0);
        ajaxSelectParamsCallback('#production_order_id', 'admin/manufactures/searchProductionsOrders', $('#production_order_id').val(), false, true);

        addRow();
    });

    $("#production_order_id").change(function() {
        $('#tb-items tbody').empty();
        addRow();
    })

    $(document).on('change', '.order_id', function() {
        var item = $(this).closest('tr').find('input.item_id');
        item.val('');
        // console.log(item);
        // console.log(item.attr('id'));
        ajaxSelectParamsCallback(`#${item.attr('id')}`, 'admin/stock_out_request_product/selectItem', 0, {
            'production_order_id': $('#production_order_id').val(),
            'order_id': $($(this)).val()
        }, true);
        item.change();

        getTotal();
    });

    $(document).on('change', 'input.item_id', function() {
        var item = $(this).val();
        var itemData = $(this).select2('data');
        var stockQuantity = 0;
        if (item) {
            console.log(item);
            paramsData['item'] = item;
            $.ajax({
                    // url: site.base_url+'admin/stock/rowItem',
                    url: site.base_url + 'admin/stock_out_request_product/getItemStockQuantity',
                    type: 'POST',
                    dataType: 'json',
                    data: paramsData
                })
                .done(function(data) {
                    if (data) {
                        stockQuantity = data.stock_quantity;
                    }
                })
                .fail(function() {
                    console.log("error");
                })
        }

        // console.log(itemData);
        var tdItemName = $(this).closest('tr').find('.item_name');
        tdItemName.html(itemData?.item_name ?? '');

        var tdItemUnit = $(this).closest('tr').find('.item_unit');
        tdItemUnit.html(itemData?.unit_name ?? '');

        var tdProductionQuantity = $(this).closest('tr').find('.production_quantity');
        var inputProductionQuantity = $(this).closest('tr').find('.input_production_quantity');
        tdProductionQuantity.html(tnhFormatNumber(itemData?.quantity ?? 0));
        inputProductionQuantity.val(itemData?.quantity ?? 0);

        var tdStockQuantity = $(this).closest('tr').find('.stock_quantity');
        tdStockQuantity.html(tnhFormatNumber(stockQuantity));

        var tdProductionRequireQuantity = $(this).closest('tr').find('.production_require_quantity');
        var inputProductionRequireQuantity = $(this).closest('tr').find('.input_production_require_quantity');
        tdProductionRequireQuantity.html(tnhFormatNumber(itemData?.quantity_rest ?? 0));
        inputProductionRequireQuantity.val(itemData?.quantity_rest ?? 0);

        var purchaseRequireQuantity = Number(itemData?.quantity ?? 0) - stockQuantity;
        if (purchaseRequireQuantity < 0) purchaseRequireQuantity = 0;
        // var tdPurchaseRequireQuantity = $(this).closest('tr').find('.purchase_require_quantity');
        var inputPurchaseRequireQuantity = $(this).closest('tr').find('.input_purchase_require_quantity');
        // tdPurchaseRequireQuantity.html(tnhFormatNumber(purchaseRequireQuantity));
        inputPurchaseRequireQuantity.val(tnhFormatNumber(purchaseRequireQuantity));
    });

    // $("#item").change(function() {
    //     dtItems = $(this).select2('data');
    //     loadItem(dtItems)
    //     $(this).select2("val", "");
    // })

    function addRow() {
        // console.log(dtItems);return;
        tdStt = `<div class="stt"></div>`;
        tdOrder = `<div class="td-order"><input type="text" name="order_id[${counter}]" data-placeholder="<?= lang('cong_orders') ?>" class="order_id" id="order_id_${counter}" style="width: 100%;" value=""></div>`;
        tdItemCode = `<div class="code_item">
            <input type="hidden" name="counter[]" class="counter" value="${counter}">
            <input type="text" name="item_id[${counter}]" class="item_id" id="item_id_${counter}" data-placeholder="<?= lang('materials') ?>" value="">
        </div>`;

        // Tổng số lương sản xuất
        tdProductionQuantity = `<div class="production_quantity text-center"></div>
        <input type="hidden" name="production_quantity[${counter}]" id="production_quantity_${counter}" class="input_production_quantity">`;

        // Số lượng tồn kho
        tdStockQuantity = `<div class="stock_quantity text-center"></div>`;

        // Số lương cần SX
        tdProductionRequireQuantity = `<div class="production_require_quantity text-center"></div>
        <input type="hidden" name="production_require_quantity[${counter}]" id="production_require_quantity_${counter}" class="input_production_require_quantity">`;

        // Số lượng cần mua
        tdPurchaseRequireQuantity = `<div class="purchase_require_quantity text-center"></div>
        <input type="text" name="purchase_require_quantity[${counter}]" id="purchase_require_quantity_${counter}" class="input_purchase_require_quantity number-format form-control">`;

        tdActions = `<a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a>`;

        trItem = `<tr>
            <td class="text-center">${tdStt}</td>
            <td class="text-center">${tdOrder}</td>
            <td>${tdItemCode}</td>
            <td class="item_name"></td>
            <td class="item_unit text-center"></td>
            <td>${tdProductionQuantity}</td>
            <td>${tdStockQuantity}</td>
            <td>${tdProductionRequireQuantity}</td>
            <td>${tdPurchaseRequireQuantity}</td>
            <td class="td-actions text-center">${tdActions}</td>
        </tr>`;

        $("#tb-items").find('tbody').append(trItem);
        ajaxSelectParamsCallback(`#order_id_${counter}`, 'admin/stock_out_request_product/selectOrder', 0, {
            'production_order_id': $('#production_order_id').val()
        }, true);
        ajaxSelectParamsCallback(`#item_id_${counter}`, 'admin/stock_out_request_product/selectItem', 0, {
            'production_order_id': $('#production_order_id').val(),
            'order_id': $(`#order_id_${counter}`).val()
        }, true);

        init_selectpicker();
        counter++;

        getTotal();
    }

    function removeRow(el) {
        $(el).closest('tr').remove();
        getTotal();
    }

    function getTotal() {
        tb = '#tb-items tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        var stt = 0;
        count_errors = 0;
        var addRowFlag = true;
        for (ii = 0; ii < n; ii++) {
            stt++;
            element = $(tb)[ii];
            $(element).find('.stt').html(stt);

            var order_id = $(element).find('input.order_id').val();
            if (!order_id) {
                addRowFlag = false;
            }
        }

        if (addRowFlag) {
            addRow();
        }
    }

    appValidateForm($('#submit_form'), {
        reference_no: 'required',
        date: 'required',
        client_id: 'required',
    }, submit);

    function submit(form) {
        if (count_errors > 0) {
            alert_float('danger', lang_core['check_date_enter']);
            return;
        }

        $('.btn-submit').attr('disabled', 'disabled');
        for (var i = 0; i < tinymce.editors.length; i++) {
            tinymce.editors[i].save();
        }
        var url = form.action;
        var form = $(form),
            formData = new FormData(),
            formParams = form.serializeArray();

        $.each(form.find('input[type="file"]'), function(i, tag) {
            $.each($(tag)[0].files, function(i, file) {
                formData.append(tag.name, file);
            });
        });
        $.each(formParams, function(i, val) {
            formData.append(val.name, val.value);
        });

        $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
            })
            .done(function(data) {
                // console.log(data);
                if (data.result) {
                    alert_float('success', data.message);
                    // window.location.href = site.base_url+'admin/stock_out_request_product';
                    window.location.href = "<?= $breadcrumb[0]['link'] ?>";
                } else {
                    alert_float('danger', data.message);
                    $('.btn-submit').removeAttr('disabled', 'disabled');
                }
            })
            .fail(function() {
                alert_float('danger', lang_core['errors']);
                $('.btn-submit').removeAttr('disabled', 'disabled');
            });
        return false;
    }
</script>
<?php //$this->load->view('admin/stage_control_galvanize/script_js.php') 
?>