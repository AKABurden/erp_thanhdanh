<?php echo form_open('admin/orders/add_purchase/'.$id, array('id'=>'add-purchase-new')); ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= _l('tnh_pruchases_items'); ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('orders', 'orders') ?>
                        <input type="text" name="" id="" class="form-control" value="<?= $order['reference_no'] ?>" readonly="">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('customers', 'customers') ?>
                        <input type="text" name="" id="" class="form-control" value="<?= $order['customer_name'] ?>" readonly="">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('date', 'date') ?>
                        <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : date('d/m/Y H:i:s')), 'placeholder="'.lang('date').'" id="date" required class="form-control input-tip datetimepicker"'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('ch_name_p', 'name') ?>
                        <?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : lang('ch_purchases')), 'placeholder="'.lang('ch_name_p').'" id="name" class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <?= lang('ch_note_t', 'note') ?>
                        <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ''), 'placeholder="'.lang('ch_note_t').'" id="note" class="form-control input-tip" style="height: 50px;"'); ?>
                    </div>
                </div>
                <div class="col-md-12 mbot5">
                    <div class="text-right">
                        <button type="button" onClick="loadAllItemsMissing(this)" class="btn btn-primary btn-load-missing"><?= lang('tnh_load_all_items_missing_warehouse') ?></button>
                        <button type="button" onClick="removeItems(this)" class="btn btn-danger btn-referesh"><?= lang('tnh_referesh') ?></button>
                    </div>
                </div>
                <div class="col-md-12">
                    <table id="tb-item-purchases" class="dt-tnh tnh-table table table-bordered table-hover dont-responsive-table">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;"><a class="btn btn-info btn-icon add-row"><i class="fa fa-plus"></i></a></th>
                                <th style="width: 170px;"><?= lang('tnh_item_code') ?></th>
                                <th style="width: 150px;"><?= lang('tnh_item_name') ?></th>
                                <th style="width: 70px;" class="text-center"><?= lang('unit') ?></th>
                                <th style="width: 100px;" class="text-center"><?= lang('quantity') ?></th>
                                <th class="text-center"><?= lang('tnh_quantity_purchase') ?></th>
                                <th style="width: 70px;" class="text-center"><?= lang('actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
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
<?php echo form_close(); ?>
<script>
    var dtItemPurchases = '';
    var counter = 0;
    var order_purchase_id = '<?= $id ?>';
    var arr_id = [];

    function totalPurchases()
    {
        tb = '#tb-item-purchases tbody tr';
        var n = $(tb).length;
        var stt = 0;
        count_errors = 0;
        arr_id = [];
        for (ii = 0; ii < n; ii++)
        {
            stt++;
            element = $(tb)[ii];
            $(element).find('.stt').html(stt);
            item_current_id = $(element).find('input.items_id').val();
            if (item_current_id) {
                index = jQuery.inArray(item_current_id, arr_id);
                if (index !== -1)
                {
                    // alert('Mặt hàng này đã được chọn vui lòng không chọn lại');
                } else {
                    arr_id.push(item_current_id);
                }
            }
        }
    }

    function chonseItem(el, idEl)
    {
        trCurItem = $(el).closest('tr');
        dataItem = $('#'+idEl).select2("data");
        if (dataItem) {
            orderItemID = dataItem.order_item_id
            itemId = dataItem.id
            itemName = dataItem.name;
            itemType = itemId.split('__')[0];
            unitName = dataItem.unit_name;
            quantityOrder = dataItem.total_quantity;

            trCurItem.find('.td-item-name').html(itemName);
            trCurItem.find('.td-type-item').html(lang_core[itemType]);
            trCurItem.find('.td-unit').html(unitName);

            trCurItem.find('.td-quantity-order').html(tnhFormatNumber(quantityOrder));
            trCurItem.find('.order_item_id').val(orderItemID);

            if (jQuery.inArray(itemId, arr_id) !== -1) {
                alert('Mặt hàng này đã được chọn vui lòng không chọn lại');
                dtItemPurchases.row( trCurItem ).remove().draw();
                return;
            }

            lastrow = $('#tb-item-purchases tbody tr')[$('#tb-item-purchases tbody tr').length - 1];
            if ($(lastrow).find('input.items_id').select2('val')) {
                $('.add-row').click();
            }
        } else {

        }
    }

    function removeRow(el)
    {
        dtItemPurchases.row( $(el).parents('tr') ).remove().draw();
    }

    function removeItems(_this)
    {
        dtItemPurchases.rows().remove().draw();
    }

    function loadAllItemsMissing(_this)
    {
        dtItemPurchases.rows().remove().draw();
        $.ajax({
            url: site.base_url+'admin/orders/getAllItemsMissingWarehouse',
            type: 'POST',
            dataType: 'json',
            data: {
                order_id: order_purchase_id,
                "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>"
            },
        })
        .done(function(data) {
            if (data) {
                $.each(data.items, function(index, el) {
                    tdNumber = '<div class="stt text-center"></div>';
                    tdCode = '<div class="td-code mbot10">\
                        <input type="hidden" name="counter['+counter+']" id="counter" class="form-control counter" value="'+counter+'">\
                        <input type="hidden" name="order_item_id['+counter+']" id="order_item_id" class="form-control order_item_id" value="'+el.order_item_id+'">\
                        <input type="text" name="items_id['+counter+']" id="items_'+counter+'" class="items_id modal-select2" style="width: 100%;" onchange="chonseItem(this, \'items_'+counter+'\')" data-placeholder="'+ lang_core['choose'] +'" value="'+el.id+'"></div>'+
                        '<div class="type-item"></div>'+
                    '</div>';

                    tdName = '<div class="td-item-name">'+el.text+'</div>';
                    tdUnit = '<div class="td-unit text-center">'+el.unit_name+'</div>';
                    tdQuantityOrder = '<div class="td-quantity-order text-center">'+el.total_quantity+'</div>';
                    tdQuantity = '<div class="td-quantity"><input type="text" name="quantity['+counter+']" id="quantity[]" class="form-control quantity number-format" style="width: 100%;" value="'+el.quantity_purchase+'"></div>';
                    tdActions = '<div class="text-center"><i onclick="removeRow(this)" class="fa fa-remove btn btn-danger remove-row"></i></div>';

                    rowNode = dtItemPurchases.row.add( [
                        tdNumber,
                        tdCode,
                        tdName,
                        tdUnit,
                        tdQuantityOrder,
                        tdQuantity,
                        tdActions
                    ] ).draw( false ).node();

                    ajaxSelectParamsCallback($('#items_'+ counter +''), 'admin/orders/searchItemsOrders', $('#items_'+ counter +'').val(), {'order_id': order_purchase_id});
                    counter++;
                });
                totalPurchases();
            }
        })
        .fail(function() {
            console.log("error");
        });
    }

    $(function(){
        init_datepicker();
        dtItemPurchases = $('#tb-item-purchases').DataTable({
            "language": app.lang.datatables,
            "pageLength": intVal(app.options.tables_pagination_limit),
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
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
            tdNumber = '<div class="stt text-center"></div>';
            tdCode = '<div class="td-code mbot10">\
                <input type="hidden" name="counter['+counter+']" id="counter" class="form-control counter" value="'+counter+'">\
                <input type="hidden" name="order_item_id['+counter+']" id="order_item_id" class="form-control order_item_id" value="">\
                <input type="text" name="items_id['+counter+']" id="items_'+counter+'" class="items_id modal-select2" style="width: 100%;" onchange="chonseItem(this, \'items_'+counter+'\')" data-placeholder="'+ lang_core['choose'] +'" value=""></div>'+
                '<div class="type-item"></div>'+
            '</div>';

            tdName = '<div class="td-item-name"></div>';
            tdTypeItem = '<div class="td-type-item text-center"></div>';
            tdUnit = '<div class="td-unit text-center"></div>';
            tdQuantityOrder = '<div class="td-quantity-order text-center"></div>';
            tdQuantity = '<div class="td-quantity"><input type="text" name="quantity['+counter+']" id="quantity[]" class="form-control quantity number-format" style="width: 100%;" value="1"></div>';
            tdActions = '<div class="text-center"><i onclick="removeRow(this)" class="fa fa-remove btn btn-danger remove-row"></i></div>';

            rowNode = dtItemPurchases.row.add( [
                tdNumber,
                tdCode,
                tdName,
                tdUnit,
                tdQuantityOrder,
                tdQuantity,
                tdActions
            ] ).draw( false ).node();

            ajaxSelectParamsCallback($('#items_'+ counter +''), 'admin/orders/searchItemsOrders', 0, {'order_id': order_purchase_id});

            counter++;
            totalPurchases();
        });

        $(document).ready(function() {
            $('.add-row').click();
        });

        appValidateForm($('#add-purchase-new'), {
            'date': 'required'
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