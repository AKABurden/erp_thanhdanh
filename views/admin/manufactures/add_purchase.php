<?php echo form_open('admin/manufactures/add_purchase/'.$id, array('id'=>'add-purchase-new')); ?>
<div class="modal-dialog modal-lg" style="width: 70%;">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= _l('tnh_add_purchase'); ?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
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
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('ch_note_t', 'note') ?>
                        <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ''), 'placeholder="'.lang('ch_note_t').'" id="note" class="form-control input-tip" style="height: 50px;"'); ?>
                    </div>
                </div>
                <div class="col-md-12 hide">
                    <div class="text-right">
                        <a class="btn btn-success btn-xs" href="javascript:void(0)" onclick="loadAllPuchases(this)"><?= lang('tnh_load_all_lack') ?></a>
                        <a class="btn btn-danger btn-xs" href="javascript:void(0)" onclick="removeAllPurchases(this)"><?= lang('tnh_delete_all') ?></a>
                    </div>
                </div>
                <div class="col-md-12">
                    <table id="tb-item-purchases" class="table table-bordered table-hover dataTable">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;"><a class="btn btn-info btn-icon add-row"><i class="fa fa-plus"></i> <?= lang('tnh_add_row') ?></a></th>
                                <th class="text-center" style="width: 170px;"><?= lang('tnh_item_code') ?></th>
                                <th class="text-center" style="width: 100px;" class="text-center"><?= lang('type') ?></th>
                                <th class="text-center" style="width: 70px;" class="text-center"><?= lang('unit') ?></th>
                                <!-- <th style="width: 150px;" class="text-center"><?= lang('tnh_quantity_warehouses') ?></th> -->
                                <th style="width: 170px;" class="text-center"><?= lang('tnh_quantity_purchase') ?></th>
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
    var arr_id = [];
    var c_productions_plan_id = '<?= $id ?>';

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
            itemId = dataItem.id
            itemName = dataItem.name;
            itemType = itemId.split('__')[0];
            unitName = dataItem.unit_name;
            trCurItem.find('.td-item-name').html(itemName);
            trCurItem.find('.td-type-item').html(lang_core[dataItem.item_type_root]);
            trCurItem.find('.td-unit').html(unitName);

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

    function removeAllPurchases(_this) {
        dtItemPurchases.rows().remove().draw();
    }

    function loadAllPuchases(_this) {
        $.ajax({
            type: "POST",
            url: site.base_url+'admin/manufactures/loadAllKeepStockMaterial',
            data: {
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                productions_plan_id: c_productions_plan_id,
                'type': 'purchases'
            },
            dataType: "json",
            success: function (response) {
                if (response) {
                    dtItemPurchases.rows().remove().draw();
                    $.each(response.items, function (index, item) { 
                        createRow(item);
                    });
                }
            }
        });
    }

    function createRow(dataItem = false) {

        dtItems_id = '';
        dtType_item = '';
        dtWarehouses = '';
        dtQuantityHold = '';
        dtUnitName = '';
        dtQuantity = 1;
        txtJsonItemsId = null;
        if (dataItem) {
            dtItems_id = dataItem.id;
            dtType_item = lang_core[dataItem.item_type_root];
            quantity_net = intVal(dataItem.quantity_net);
            quantity_primary = intVal(dataItem.quantity_primary);
            quantity_purchase = intVal(dataItem.quantity_purchase);
            dtQuantity = quantity_primary - quantity_net - quantity_purchase;
            // dtQuantity = quantity_primary;
            if (dtQuantity < 0) dtQuantity = 0;
            if (dtQuantity == 0) return ''; 
            dtQuantityHold = quantity_net;
            txtJsonItemsId = {'id': dtItems_id, 'text': dataItem.text};
            dtWarehouses = dataItem.warehouses;
            dtUnitName = dataItem.unit_name;
        }

        tdNumber = '<div class="stt text-center"></div>';
        tdCode = '<div class="td-code mbot10"><input type="hidden" name="counter['+counter+']" id="counter" class="form-control counter" value="'+counter+'">\
            <input type="text" name="items_id['+counter+']" id="items_'+counter+'" class="items_id modal-select2" style="width: 100%;" onchange="chonseItem(this, \'items_'+counter+'\')" data-placeholder="'+ lang_core['choose'] +'" value="'+dtItems_id+'"></div>'+
            '<div class="type-item">'+
        '</div>';

        tdTypeItem = `<div class="td-type-item text-center">${dtType_item}</div>`;
        tdUnit = `<div class="td-unit text-center">${dtUnitName}</div>`;
        tdWarehouse = `<div class="td-warehouse text-center"></div>`;
        tdQuantity = '<div class="td-quantity"><input type="text" name="quantity['+counter+']" id="quantity[]" class="form-control quantity number-format" style="width: 100%;" value="'+tnhFormatNumber(dtQuantity)+'"></div>';
        tdActions = '<div class="text-center"><i onclick="removeRow(this)" class="fa fa-remove btn btn-danger remove-row"></i></div>';

        rowNode = dtItemPurchases.row.add( [
            tdNumber,
            tdCode,
            tdTypeItem,
            tdUnit,
            tdQuantity,
            tdActions
        ] ).draw( false ).node();
        
        if (txtJsonItemsId) {
            ajaxSelectParamsCallback($('#items_' + counter + ''), 'admin/manufactures/getItemsKeepStockMaterial', dtItems_id, {productions_plan_id: c_productions_plan_id}, false, txtJsonItemsId);
        } else {
            ajaxSelectParamsCallback($('#items_' + counter + ''), 'admin/manufactures/getItemsKeepStockMaterial', 0, {productions_plan_id: c_productions_plan_id});
        }
        counter++;
        totalPurchases();
    }

    $(function(){
        init_datepicker();
        loadAllPuchases();
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
            createRow();
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