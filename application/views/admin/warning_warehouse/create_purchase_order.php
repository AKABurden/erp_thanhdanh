<?php echo form_open('admin/warning_warehouse/add_purchase_order', array('id'=>'add-purchase-order-new')); ?>
<div class="modal fade in" id="create_purchase_order_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    data-backdrop="static" data-keyboard="false" aria-hidden="false">
    <div class="modal-dialog modal-lg" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= _l('Thêm đơn đặt hàng'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('date', 'date') ?>
                            <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : date('d/m/Y')), 'placeholder="'.lang('date').'" id="date" required class="form-control input-tip datetimepicker"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <?= lang('Nhà cung cấp', 'suppliers_id') ?>
                        <div class="form-group">
                            <?php $value ='';
                                echo render_select('suppliers_id',$suppliers,array('id','company'),'',$value);
								?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('Nhân viên', 'id_staff') ?>
                            <?php $value = get_staff_user_id(); ?>
                            <?php echo render_select('id_staff', $staff, array('staffid', array('firstname', 'lastname')),'',$value,array('disabled'=>true)); ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('Ngày dự kiến giao hàng', 'delivery_date') ?>
                            <?php $delivery_date = ''; ?>
                            <?php echo render_date_input('delivery_date', '', $delivery_date); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('Chi phí giao hàng', 'delivery_cost') ?>
                            <?php
                                $value =  0;
                            ?>
                            <input type="text" onkeyup="formatNumBerKeyUpCus(this)" name="delivery_cost"
                                class="form-control delivery_cost" id="delivery_cost" value="<?= $value ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('Khoảng trừ giảm', 'reduce_cost') ?>
                            <?php
                              $value = 0;
                            ?>
                            <input type="text" onkeyup="formatNumBerKeyUpCus(this)" name="reduce_cost"
                                class="form-control reduce_cost" id="reduce_cost" value="<?= $value ?>">
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('ch_note_t', 'note') ?>
                            <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ''), 'placeholder="'.lang('ch_note_t').'" id="note" class="form-control input-tip" style="height: 50px;"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12 hide">
                        <div class="text-right">
                            <a class="btn btn-success btn-xs" href="javascript:void(0)"
                                onclick="loadAllPuchases(this)"><?= lang('tnh_load_all_lack') ?></a>
                            <a class="btn btn-danger btn-xs" href="javascript:void(0)"
                                onclick="removeAllPurchases(this)"><?= lang('tnh_delete_all') ?></a>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="tb-item-purchases" class="table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;"><a
                                                class="btn btn-info btn-icon add-row"><i class="fa fa-plus"></i>
                                                <?= lang('Thêm') ?></a></th>
                                        <th class="text-center" style="width: 170px;"><?= lang('tnh_item_code') ?></th>
                                        <th class="text-center" style="width: 70px;" class="text-center">
                                            <?= lang('unit') ?>
                                        </th>
                                        <th style="width: 80px;" class="text-center">
                                            <?= lang('Số lượng') ?>
                                        </th>
                                        <th style="width: 100px;" class="text-center">
                                            <?= lang('Đơn giá') ?>
                                        </th>
                                        <th style="width: 80px;" class="text-center">
                                            <?= lang('Thuế') ?>
                                        </th>
                                        <th style="width: 100px;" class="text-center">
                                            <?= lang('Khuyến mãi') ?>
                                        </th>
                                        <th style="width: 100px;" class="text-center">
                                            <?= lang('Thành tiền') ?>
                                        </th>
                                        <th style="width: 120px;" class="text-center">
                                            <?= lang('Ghi chú') ?>
                                        </th>
                                        <th style="width: 70px;" class="text-center"><?= lang('actions') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <table class="table tnh-tb table-bordered table-hover">
                                <tbody>
                                    <tr>
                                        <td style="width: 15%;"><?= lang('tax', 'tax') ?></td>
                                        <td style="width: 35%;">
                                            <?=str_replace(' tax',' tax_all',get_taxes_dropdown_template('tax_all'))?>
                                        </td>
                                        <td style="width: 15%;"><?= lang('Chiết khấu', 'cost_delivery') ?></td>
                                        <td style="width: 35%;">
                                            <div>
                                                <div>
                                                    <input type="text" name="valtype_check_suppliers" value="1"
                                                        class="hide" id="valtype_check_suppliers">
                                                    <div style="float: left;" class="radio radio-primary radio-inline">
                                                        <input type="radio" class="type_check_suppliers"
                                                            name="type_check_suppliers" value="1"
                                                            checked><label>%</label>
                                                    </div>
                                                    <div class="radio radio-primary radio-inline">
                                                        <input type="radio" name="type_check_suppliers"
                                                            class="type_check_suppliers" value="2">
                                                        <label>Tiền</label>
                                                    </div>
                                                </div>
                                                <div style="display: flex; align-items: center;">
                                                    <input placeholder="Chiết khấu" class="form-control"
                                                        id="discount_percent_suppliers" value="0"
                                                        name="discount_percent_suppliers"
                                                        style="width: 100%;float: left;"
                                                        onkeyup="formatNumBerKeyUp(this)">
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="success" style="font-weight: 700;">
                                        <td><?= lang('tnh_grand_total', 'grand_total') ?></td>
                                        <td colspan="3" class="td-grand-total-all text-right">0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="save" id="save" class="form-control" value="1">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
                <button type="submit" class="btn btn-primary add"><?= _l('save') ?></button>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
var dtItemPurchases = '';
var counter = 0;
var arr_id = [];
var id_item = '<?= $id ?>';
var taxes_dropdown_template = <?=json_encode($taxes)?>;

function totalPurchases() {
    tb = '#tb-item-purchases tbody tr';
    var n = $(tb).length;
    var stt = 0;
    count_errors = 0;
    arr_id = [];
    var tax_all = $(".tax_all").find(":selected").val();
    var valtype_check = $('#valtype_check_suppliers').val();
    var total_amount_all = 0;
    for (ii = 0; ii < n; ii++) {
        stt++;
        element = $(tb)[ii];
        $(element).find('select.tax').selectpicker('val', tax_all);
        $(element).find('select.tax').change();
        $(element).find('.stt').html(stt);
        item_current_id = $(element).find('input.items_id').val();
        quantity = intVal($(element).find('input.quantity').val());
        price = intVal($(element).find('input.price').val());
        promotion_expected = intVal($(element).find('input.promotion_expected').val());
        tax_item = $(element).find('.tax_rate').val();
        if (item_current_id) {
            index = jQuery.inArray(item_current_id, arr_id);
            if (index !== -1) {} else {
                arr_id.push(item_current_id);
            }
        }
        var total_amount = quantity * price * (1 + tax_item / 100) - promotion_expected;
        total_amount_all += total_amount;
        $(element).find('.td-amount').html(tnhFormatMoney(total_amount));
    }
    var discount_percent_suppliers = $("#discount_percent_suppliers").val();
    if (valtype_check == 1) {
        if (intVal(discount_percent_suppliers) < 0) {
            $("#discount_percent_suppliers").val(0);
        }
        if (intVal(discount_percent_suppliers) > 100) {
            $("#discount_percent_suppliers").val(100);
        }
    }
    var delivery_cost = intVal($("#delivery_cost").val());
    var reduce_cost = intVal($("#reduce_cost").val());
    var discount_percent_suppliers_total = 0;

    if (valtype_check == 1) {
        discount_percent_suppliers_total = total_amount_all * discount_percent_suppliers / 100;
        total_amount_all = total_amount_all - discount_percent_suppliers_total;
    } else if (valtype_check == 2) {
        discount_percent_suppliers_total = intVal(discount_percent_suppliers);
        if (discount_percent_suppliers_total >= total_amount_all) {
            $('#discount_percent_suppliers').val(formatNumber(total_amount_all));
            discount_percent_suppliers_total = total_amount_all;
        }
        total_amount_all = total_amount_all - discount_percent_suppliers_total;
    }
    $('.td-grand-total-all').text(tnhFormatNumber((total_amount_all + delivery_cost) - reduce_cost));
}
$(document).on('change', '.type_check_suppliers', function(e) {
    var val = $(this).val();
    $('#valtype_check_suppliers').val(val);
    $('#discount_percent_suppliers').val(0);
    totalPurchases();
});
$(".tax_all").change(function() {
    totalPurchases();
});
$("#discount_percent_suppliers").keyup(function() {
    totalPurchases();
});
$("#delivery_cost").keyup(function() {
    totalPurchases();
});
$("#reduce_cost").keyup(function() {
    totalPurchases();
});
$(document).on('change', '.quantity,.price,.promotion_expected', function(e) {
    totalPurchases();
});

function changeTax(_this) {
    var tax_rate = intVal($(_this).find('option:selected').attr('data-taxrate'));
    if (isNaN(tax_rate)) tax_rate = 0;
    $(_this).parents('tr').find('input.tax_rate').val(tax_rate);
}


function chonseItem(el, idEl) {
    trCurItem = $(el).closest('tr');
    dataItem = $('#' + idEl).select2("data");
    if (dataItem) {
        itemId = dataItem.id
        itemName = dataItem.name;
        itemType = itemId.split('__')[0];
        unitName = dataItem.unit_name;
        if (dataItem.item_type == 'nvl') {
            dtType_item = '<div class="label label-success">Nguyên vật liệu</div>';
        } else if (dataItem.item_type == 'product') {
            dtType_item = '<div class="label label-warning">Bán thành phẩm(MN)</div>';
        } else if (dataItem.item_type == 'tools') {
            dtType_item = '<div class="label label-primary">Công cụ vật tư</div>';
        }
        dtQuantityInventory = intVal(dataItem.quantity_inventory);
        quantity = intVal(dataItem.quantity);
        quantity_bom = intVal(dataItem.quantity_bom);
        if ((quantity + quantity_bom) - dtQuantityInventory < 0) {
            dtQuantity = 0;
        } else {
            dtQuantity = (quantity + quantity_bom) - dtQuantityInventory;
        }
        trCurItem.find('.td-item-name').html(itemName);
        trCurItem.find('.type-item').html(dtType_item);
        trCurItem.find('.td-unit').html(unitName);
        trCurItem.find('.quantity').val(tnhFormatNumber(dtQuantity));

        if (jQuery.inArray(itemId, arr_id) !== -1) {
            alert('Mặt hàng này đã được chọn vui lòng không chọn lại');
            dtItemPurchases.row(trCurItem).remove().draw();
            return;
        }

        lastrow = $('#tb-item-purchases tbody tr')[$('#tb-item-purchases tbody tr').length - 1];
        if ($(lastrow).find('input.items_id').select2('val')) {
            $('.add-row').click();
        }
    } else {

    }
}

function removeRow(el) {
    dtItemPurchases.row($(el).parents('tr')).remove().draw();
}

function removeAllPurchases(_this) {
    dtItemPurchases.rows().remove().draw();
}

function loadAllPuchases(_this) {
    $.ajax({
        type: "POST",
        url: site.base_url + 'admin/warning_warehouse/loadItemsWarningWarehouse',
        data: {
            '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
            id_item: id_item,
        },
        dataType: "json",
        success: function(response) {
            if (response) {
                dtItemPurchases.rows().remove().draw();
                $.each(response.items, function(index, item) {
                    createRow(item);
                });
            }
        }
    });
}

function createRow(dataItem = false) {

    dtItems_id = '';
    dtType_item = '';
    dtUnitName = '';
    dtQuantity = 1;
    txtJsonItemsId = null;
    if (dataItem) {
        dtItems_id = dataItem.id;
        if (dataItem.item_type == 'nvl') {
            dtType_item = '<div class="label label-success">Nguyên vật liệu</div>';
        } else if (dataItem.item_type == 'product') {
            dtType_item = '<div class="label label-warning">Bán thành phẩm(MN)</div>';
        } else if (dataItem.item_type == 'tools') {
            dtType_item = '<div class="label label-primary">Công cụ vật tư</div>';
        }
        dtQuantityInventory = intVal(dataItem.quantity_inventory);
        quantity = intVal(dataItem.quantity);
        quantity_bom = intVal(dataItem.quantity_bom);
        if ((quantity + quantity_bom) - dtQuantityInventory < 0) {
            dtQuantity = 0;
        } else {
            dtQuantity = (quantity + quantity_bom) - dtQuantityInventory;
        }
        txtJsonItemsId = {
            'id': dtItems_id,
            'text': dataItem.text
        };

        dtUnitName = dataItem.unit_name;
    }

    tdNumber = '<div class="stt text-center"></div>';
    tdCode = '<div class="td-code mbot10"><input type="hidden" name="counter[' + counter +
        ']" id="counter" class="form-control counter" value="' + counter + '">\
            <input type="text" name="items_id[' + counter + ']" id="items_' + counter +
        '" class="items_id modal-select2" style="width: 100%;" onchange="chonseItem(this, \'items_' + counter +
        '\')" data-placeholder="' + lang_core['choose'] + '" value="' + dtItems_id + '"></div>' +
        '<div class="type-item">' + dtType_item +
        '</div>';

    tdTypeItem = `<div class="td-type-item text-center">${dtType_item}</div>`;
    tdUnit = `<div class="td-unit text-center">${dtUnitName}</div>`;
    tdQuantity = '<div class="td-quantity"><input type="text" name="quantity[' + counter +
        ']" id="quantity[]" class="form-control quantity" onkeyup="formatNumBerKeyUpCus(this)" style="width: 100%;" value="' +
        tnhFormatNumber(
            dtQuantity) + '"></div>';
    tdPrice = '<div class="td-price"><input type="text" name="price[' + counter +
        ']" id="price[]" class="form-control price" onkeyup="formatNumBerKeyUpCus(this)" style="width: 100%;" value="0"></div>';
    var taxTemplate = taxes_dropdown_template;
    taxTemplate = taxTemplate.replace('name=""', 'name="tax_id[' + counter + ']"');
    tdTax = '<div class="td-tax">' + taxTemplate + '<input type="hidden" class="tax_rate" name="tax_rate[' +
        counter + ']"  value="0"></div>';
    tdPromotion = '<div class="td-promotion_expected"><input type="text" name="promotion_expected[' + counter +
        ']" id="promotion_expected[]" class="form-control promotion_expected" onkeyup="formatNumBerKeyUpCus(this)" style="width: 100%;" value="0"></div>';
    tdAmount = '<div class="td-amount text-right"></div>';
    tdNote = '<div class="td-note"><textarea class="note" name="note_item[' + counter + ']" ></textarea></div>';
    tdActions =
        '<div class="text-center"><i onclick="removeRow(this)" class="fa fa-remove btn btn-danger remove-row"></i></div>';

    rowNode = dtItemPurchases.row.add([
        tdNumber,
        tdCode,
        tdUnit,
        tdQuantity,
        tdPrice,
        tdTax,
        tdPromotion,
        tdAmount,
        tdNote,
        tdActions
    ]).draw(false).node();

    if (txtJsonItemsId) {
        ajaxSelectParamsCallback($('#items_' + counter + ''), 'admin/warning_warehouse/getItemsWarningWarehouse',
            dtItems_id, {
                id_item: id_item
            }, false, txtJsonItemsId);
    } else {
        ajaxSelectParamsCallback($('#items_' + counter + ''), 'admin/warning_warehouse/getItemsWarningWarehouse',
            0, {
                id_item: id_item
            });
    }
    $('.selectpicker').selectpicker('refresh');
    counter++;
    totalPurchases();
}

$(function() {
    init_datepicker();
    init_selectpicker();
    loadAllPuchases();
    dtItemPurchases = $('#tb-item-purchases').DataTable({
        "language": app.lang.datatables,
        "pageLength": intVal(app.options.tables_pagination_limit),
        "lengthMenu": [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "<?= lang('all') ?>"]
        ],
        'searching': false,
        'ordering': false,
        'paging': false,
        "info": false,
        "initComplete": function(settings, json) {
            var t = this;
            t.parents('.table-loading').removeClass('table-loading');
            t.removeClass('dt-table-loading');
            mainWrapperHeightFix();
        },
    });

    $('.add-row').on('click', function(event) {
        event.preventDefault();
        createRow();
    });

    $(document).ready(function() {
        $('.add-row').click();
    });

    appValidateForm($('#add-purchase-order-new'), {
        'date': 'required',
        'suppliers_id': 'required',
        'delivery_date': 'required',

    }, convert);

    function convert(form) {
        $('.add').attr('disabled', 'disabled');
        // var data = $(form).serialize();
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
        //
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
                if (data.result) {
                    alert_float('success', data.message);
                    if (typeof oTable != 'undefined' && oTable != '') {
                        oTable.draw();
                    }
                    $('.modal-dialog .close').trigger('click');
                } else {
                    alert_float('danger', data.message);
                    $('.add').removeAttr('disabled', 'disabled');
                }
            })
            .fail(function() {
                alert_float('danger', 'error');
                $('.add').removeAttr('disabled', 'disabled');
            });
        return false;
    }
})
</script>