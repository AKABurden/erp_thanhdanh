<?php echo form_open('admin/manufactures_temp/addPurchasePlan/', array('id'=>'add-purchase-new')); ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('Tạo mua hàng') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <?= lang('date', 'date') ?>
                        <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : date('d/m/Y H:i:s')), 'placeholder="' . lang('date') . '" id="date" required class="form-control input-tip datetimepicker"'); ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <?= lang('Ngày cần hàng', 'date_need') ?>
                        <?php echo form_input('date_need', (isset($_POST['date_need']) ? $_POST['date_need'] : date('d/m/Y')), 'placeholder="' . lang('Ngày cần hàng') . '" id="date_need" required class="form-control input-tip datepicker"'); ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <?= lang('ch_name_p', 'name') ?>
                        <?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : lang('ch_purchases')), 'placeholder="' . lang('ch_name_p') . '" id="name" class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <?= lang('ch_note_t', 'note') ?>
                        <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ''), 'placeholder="' . lang('ch_note_t') . '" id="note" class="form-control input-tip" style="height: 50px;"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="table-view-items" class="table dataTable table-view-items" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 30px;">
                                    </th>
                                    <th class="text-center" style="width: 200px;"><?= lang('materials') ?></th>
                                    <th class="text-center" style="width: 100px;"><?= lang('tnh_standard_unit') ?></th>
                                    <th class="text-center" style="width: 100px;"><?= lang('quantity') ?></th>
                                    <th class="text-center" style="width: 100px;"><?= lang('Số lượng(ĐV chuẩn)') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $dataItem = $this->input->post('dataItem');
                                    $arrItemId = [];
                                    if ($dataItem) {
                                        foreach ($dataItem as $key => $value) {
                                            $_arr = explode('__', $value);
                                            $arrItemId[] = $_arr[1];
                                        }
                                    }

                                    $tbWarehouseProduct = "(
                                        SELECT
                                            tblwarehouse_items.id_items as id_items,
                                            SUM(tblwarehouse_items.product_quantity) as product_quantity,
                                            SUM(tblwarehouse_items.product_quantity_unit) as product_quantity_unit
                                        FROM tblwarehouse_items
                                        WHERE tblwarehouse_items.type_items = 'product' AND tblwarehouse_items.warehouse_id != ".WAREHOUSES_CAPACITY." AND tblwarehouse_items.id_items IN (".(implode(',', $arrItemId)).")
                                        GROUP BY tblwarehouse_items.id_items
                                    ) tb_quantity_warehouse";
                            
                                    $tbWarehouseMaterials = "(
                                        SELECT
                                            tblwarehouse_items.id_items as id_items,
                                            SUM(tblwarehouse_items.product_quantity) as product_quantity,
                                            SUM(tblwarehouse_items.product_quantity_unit) as product_quantity_unit
                                        FROM tblwarehouse_items
                                        WHERE tblwarehouse_items.type_items = 'nvl' AND tblwarehouse_items.warehouse_id != ".WAREHOUSES_CAPACITY." AND tblwarehouse_items.id_items IN (".(implode(',', $arrItemId)).")
                                        GROUP BY tblwarehouse_items.id_items
                                    ) tb_quantity_warehouse";
                            
                                   

                                    

                                    $wherePL = '';
                                    $wherePurchase = '';
                                    $wherePQ = '';
                                    $whereTransfer = '';
                                    $productions_plan_search = $this->input->post('productions_plan_id');
                                    if (!empty($productions_plan_search)) {
                                        $wherePL.= ' AND tbl_productions_plan_bom.productions_plan_id IN ('.implode(',', $productions_plan_search).')';
                                        $wherePQ.= 'WHERE tbl_productions_plan_compensation.productions_plan_id IN ('.implode(',', $productions_plan_search).')';
                                        $whereTransfer .= ' AND tbltransfer_warehouse.productions_capacity_id IN (' . implode(',', $productions_plan_search) . ')';
                                        $wherePurchase.= ' AND exists (
                                            SELECT
                                                tbl_purchases_plans.purchases_id
                                            FROM tbl_purchases_plans
                                            WHERE tbl_purchases_plans.purchases_id = tblpurchases.id AND tbl_purchases_plans.productions_plan_id IN ('.implode(',', $productions_plan_search).')
                                        )';
                                    }

                                    $tbTransfer = "(
                                        SELECT
                                            tbltransfer_warehouse.productions_capacity_id,
                                            tbltransfer_warehouse_detail.type as type, 
                                            tbltransfer_warehouse_detail.id_items as id_items,
                                            SUM(tbltransfer_warehouse_detail.quantity_net) as quantity,
                                            SUM(tbltransfer_warehouse_detail.quantity_unit) as quantity_unit
                                        FROM tbltransfer_warehouse
                                        INNER JOIN tbltransfer_warehouse_detail ON tbltransfer_warehouse_detail.id_transfer = tbltransfer_warehouse.id
                                        WHERE tbltransfer_warehouse.productions_capacity_id > 0 $whereTransfer
                                        GROUP BY tbltransfer_warehouse_detail.type, tbltransfer_warehouse_detail.id_items
                                    ) tb_transfer";
                                    $whereImport = '';
                                    $tbImport = "(
                                        SELECT
                                            IF(tblimport_items.type = 'nvl', 'materials', 'products') as type, 
                                            tblimport_items.product_id as id_items,
                                            SUM(tblimport_items.quantity_net) as quantity,
                                            SUM(tblimport_items.quantity_unit) as quantity_unit,
                                            SUM(tblimport_items.quantity_stock) as quantity_stock
                                        FROM tblimport
                                        INNER JOIN tblimport_items ON tblimport_items.id_import = tblimport.id
                                        WHERE tblimport.type_plan > 0 AND tblimport.warehouseman_id > 0 AND tblimport_items.type IN ('product', 'nvl') $whereImport 
                                        GROUP BY tblimport_items.product_id,tblimport_items.type
                                    ) tb_import";
                                    $tbPurchase = "(
                                        SELECT
                                            IF(tblpurchases_items.type = 'nvl', 'materials', 'products') as type_item, 
                                            tblpurchases_items.type as type,
                                            tblpurchases_items.product_id as product_id,
                                            SUM(tblpurchases_items.quantity_net) as quantity_net
                                        FROM tblpurchases
                                        INNER JOIN tblpurchases_items ON tblpurchases_items.purchases_id = tblpurchases.id
                                        WHERE tblpurchases.is_plans = 1 AND tblpurchases_items.type IN ('product', 'nvl') $wherePurchase
                                        GROUP BY tblpurchases_items.type, tblpurchases_items.product_id
                                    ) tb_purchase";

                                    $tbProductionsPlanCompensation = "(
                                        SELECT
                                            tbl_productions_plan_compensation.item_id, 
                                            tbl_productions_plan_compensation.item_type,
                                            SUM(tbl_productions_plan_compensation.quantity_primary) as quantity_primary,
                                            SUM(tbl_productions_plan_compensation.quantity_compensation) as quantity_compensation
                                        FROM tbl_productions_plan_compensation
                                        $wherePQ
                                        GROUP BY tbl_productions_plan_compensation.item_id, tbl_productions_plan_compensation.item_type
                                    ) tb_productions_plan_compensation";

                                    $this->db->simple_query('SET SESSION group_concat_max_len=28446744073709551615');
                                    $tbProductionsPlanBom = "(
                                        (
                                            SELECT 
                                                tbl_productions_plan_bom.item_id as item_id,
                                                tbl_productions_plan_bom.id as id,
                                                tbl_products.code as item_code,
                                                tbl_products.name as item_name,
                                                tbl_productions_plan_bom.item_type as item_type,
                                                SUM(tbl_productions_plan_bom.quantity_primary) + coalesce(tb_productions_plan_compensation.quantity_primary, 0) as quantity_primary,
                                                SUM(tbl_productions_plan_bom.quantity) as quantity,
                                                tblunits.unit as unit_name,
                                                unit_primary.unit as unit_primary_name,
                                                tb_quantity_warehouse.product_quantity as quantity_inventory,
                                                tb_transfer.quantity as quantity_transfer,
                                                GROUP_CONCAT(DISTINCT tbl_productions_plan_bom.productions_plan_id) as productions_plan_id,
                                                1 as exchange_standard_unit,
                                                1 as exchange_unit
                                            FROM tbl_productions_plan_bom
                                            INNER JOIN tbl_products ON tbl_products.id = tbl_productions_plan_bom.item_id
                                            LEFT JOIN tblunits ON tblunits.unitid = tbl_productions_plan_bom.unit_id
                                            LEFT JOIN tblunits unit_primary ON unit_primary.unitid = tbl_productions_plan_bom.unit_parent_id
                                            LEFT JOIN $tbWarehouseProduct ON tb_quantity_warehouse.id_items = tbl_productions_plan_bom.item_id
                                            LEFT JOIN $tbTransfer ON tb_transfer.id_items = tbl_products.id AND tb_transfer.type = 'product' 
                                            LEFT JOIN $tbProductionsPlanCompensation ON tb_productions_plan_compensation.item_id = tbl_productions_plan_bom.item_id AND tb_productions_plan_compensation.item_type = tbl_productions_plan_bom.item_type
                                            WHERE tbl_productions_plan_bom.item_type IN ('semi_products_outside') $wherePL
                                            GROUP BY tbl_productions_plan_bom.item_id
                                        )
                                        UNION ALL
                                        (
                                            SELECT 
                                                tbl_productions_plan_bom.item_id as item_id,
                                                tbl_productions_plan_bom.id as id,
                                                tbl_materials.code as item_code,
                                                tbl_materials.name as item_name,
                                                tbl_productions_plan_bom.item_type as item_type,
                                                ((SUM(tbl_productions_plan_bom.quantity_primary) + coalesce(tb_productions_plan_compensation.quantity_primary, 0)) * tbl_materials.exchange_unit / tbl_materials.exchange_standard_unit) as quantity_primary,
                                                SUM(tbl_productions_plan_bom.quantity + tbl_productions_plan_bom.quantity_compensation + tbl_productions_plan_bom.quantity_compensation_sm) as quantity,
                                                tblunits.unit as unit_name,
                                                unit_stock.unit as unit_primary_name,
                                                tb_quantity_warehouse.product_quantity as quantity_inventory,
                                                tb_transfer.quantity as quantity_transfer,
                                                GROUP_CONCAT(DISTINCT tbl_productions_plan_bom.productions_plan_id) as productions_plan_id, 
                                                tbl_materials.exchange_standard_unit as exchange_standard_unit,
                                                tbl_materials.exchange_unit as exchange_unit
                                            FROM tbl_productions_plan_bom
                                            INNER JOIN tbl_materials ON tbl_materials.id = tbl_productions_plan_bom.item_id
                                            LEFT JOIN tblunits ON tblunits.unitid = tbl_productions_plan_bom.unit_id
                                            LEFT JOIN tblunits unit_primary ON unit_primary.unitid = tbl_productions_plan_bom.unit_parent_id
                                            LEFT JOIN tblunits unit_stock ON unit_stock.unitid = tbl_materials.standard_unit
                                            LEFT JOIN $tbWarehouseMaterials ON tb_quantity_warehouse.id_items = tbl_productions_plan_bom.item_id
                                            LEFT JOIN $tbTransfer ON tb_transfer.id_items = tbl_materials.id AND tb_transfer.type = 'nvl'
                                            LEFT JOIN $tbProductionsPlanCompensation ON tb_productions_plan_compensation.item_id = tbl_productions_plan_bom.item_id AND tb_productions_plan_compensation.item_type = tbl_productions_plan_bom.item_type
                                            WHERE tbl_productions_plan_bom.item_type IN ('materials') $wherePL
                                            GROUP BY tbl_productions_plan_bom.item_id
                                        )
                                    ) tb_cs";

                                    $dataItem = $this->input->post('dataItem');
                                    $whereQuery = '';
                                    if (!empty($dataItem)) {
                                        $whereQuery.= ' AND (tb_cs.item_type, tb_cs.item_id) IN (';
                                        foreach ($dataItem as $key => $value) {
                                            $dtItem = explode('__', $value);
                                            $whereQuery.= "('".$dtItem[0]."', ".$dtItem[1]."),";
                                        }
                                        $whereQuery = rtrim($whereQuery, ",");
                                        $whereQuery.= ')';
                                    } else {
                                        $whereQuery = ' AND tb_cs.item_id = 0';
                                    }

                                    // (coalesce(tb_cs.quantity_primary, 0) - coalesce(tb_cs.quantity_inventory, 0) - (coalesce(tb_purchase.quantity_net * tb_cs.exchange_unit / tb_cs.exchange_standard_unit, 0) - coalesce(tb_import.quantity_stock, 0)) - coalesce(tb_cs.quantity_transfer, 0)) as quantity_rest,
                                    $query = "(
                                        SELECT
                                            CONCAT(tb_cs.item_type, '__', tb_cs.item_id) as item_id,
                                            CONCAT(tb_cs.item_name, '(', tb_cs.item_code,')') as item_code,
                                            tb_cs.unit_primary_name as unit_primary_name,
                                            tb_cs.quantity_primary as quantity_primary,
                                            tb_cs.quantity_inventory as quantity_inventory,
                                            coalesce(tb_purchase.quantity_net) as quantity_purchase,
                                            coalesce(tb_import.quantity_unit) as quantity_unit,
                                            (coalesce(tb_cs.quantity_primary, 0) - coalesce(tb_cs.quantity_inventory, 0) - (IF(coalesce(tb_purchase.quantity_net * tb_cs.exchange_unit / tb_cs.exchange_standard_unit, 0) - coalesce(tb_import.quantity_stock, 0) > 0, coalesce(tb_purchase.quantity_net * tb_cs.exchange_unit / tb_cs.exchange_standard_unit, 0) - coalesce(tb_import.quantity_stock, 0), 0)) - coalesce(tb_cs.quantity_transfer, 0)) as quantity_rest,
                                            tb_cs.productions_plan_id as productions_plan_id,
                                            tb_cs.exchange_standard_unit as exchange_standard_unit,
                                            tb_cs.exchange_unit as exchange_unit
                                        FROM $tbProductionsPlanBom
                                        LEFT JOIN $tbPurchase ON tb_purchase.type_item = tb_cs.item_type AND tb_purchase.product_id = tb_cs.item_id
                                        LEFT JOIN $tbImport ON tb_import.id_items = tb_cs.item_id AND tb_import.type = tb_cs.item_type
                                        WHERE (coalesce(tb_cs.quantity_primary, 0) - coalesce(tb_cs.quantity_inventory, 0) - (coalesce(tb_purchase.quantity_net, 0) - coalesce(tb_import.quantity_unit, 0)) - coalesce(tb_cs.quantity_transfer, 0)) > 0 $whereQuery
                                    )";
                                    // WHERE (coalesce(tb_cs.quantity_primary, 0) - coalesce(tb_cs.quantity_inventory, 0) - (coalesce(tb_purchase.quantity_net, 0) - coalesce(tb_import.quantity_stock, 0)) - coalesce(tb_cs.quantity_transfer, 0)) > 0 $whereQuery
                                    $items = $this->db->query($query)->result_array();
                                    $counter = 0;
                                    $trHtml = '';
                                    $arrProductionsPlanId = '';
                                    $stt = 0;
                                    if (!empty($items)) {
                                        foreach ($items as $key => $value) {
                                            if ($value['quantity_rest'] <= 0) continue;
                                            $stt++;
                                            $tdNumber = '<div class="stt text-center">'.($stt).'</div>';
                                            $tdCode = '<div class="td-code mbot10">
                                                <input type="hidden" name="counter['.$counter.']" id="counter" class="form-control counter" value="'.$counter.'">
                                                <input type="hidden" name="items['.$counter.']" class="form-control items" value="'.$value['item_id'].'">
                                                '.$value['item_code'].'
                                            </div>';

                                            $tdUnit = '<div class="td-unit text-center">'.$value['unit_primary_name'].'</div>';
                                            $tdQuantity = '<div class="td-quantity">
                                                <input type="text" onchange="totalPurchasePlan()" name="quantity['.$counter.']" class="form-control quantity number-format text-center" style="width: 100%;" value="'.formatNumber(ceil($value['quantity_rest'] * $value['exchange_standard_unit']/$value['exchange_unit']), 0).'">
                                            </div>';

                                            $trHtml.= '<tr>
                                                <td>'.$tdNumber.'</td>
                                                <td>'.$tdCode.'</td>
                                                <td>'.$tdUnit.'</td>
                                                <td class="text-center">'.formatNumber(ceil($value['quantity_rest']), 0).'</td>
                                                <td>'.$tdQuantity.'</td>
                                            </tr>';
                                            $counter++;

                                            $arrProductionsPlanId.= $value['productions_plan_id'].',';
                                        }
                                    }

                                    $arrProductionsPlanId = rtrim($arrProductionsPlanId, ',');
                                    if (!empty($arrProductionsPlanId)) {
                                        $arrProductionsPlanId = explode(',', $arrProductionsPlanId);
                                        $arrProductionsPlanId = array_unique($arrProductionsPlanId);
                                    }
                                    $strProductionsPlanId = !empty($arrProductionsPlanId) ? implode(',', $arrProductionsPlanId) : '';
                                    echo $trHtml;
                                ?>
                            </tbody>
                            <tfoot>
                                <tr class="bold">
                                    <td></td>
                                    <td class="text-center"><?= lang('tnh_grand_total') ?></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                        <input type="hidden" name="arrProductionsPlanId" id="arrProductionsPlanId" class="form-control" value="<?= $strProductionsPlanId ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="save" id="save" class="form-control" value="1">
            <button type="submit" class="btn btn-primary add"><?= lang('save') ?></button>
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>

<script>
    function totalPurchasePlan() {
        tb = '#table-view-items tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        total_quantity = 0;
        for (ii = 0; ii < n; ii++)
        {
            element = $(tb)[ii];
            quantity = intVal($(element).find('.quantity').val());
            total_quantity+= quantity;
        }

        $('#table-view-items tfoot tr td:nth-child(5)').html('<div class="text-center">'+tnhFormatNumber(total_quantity)+'</div>');
    }

    $(function(){
        init_datepicker();
        totalPurchasePlan();
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