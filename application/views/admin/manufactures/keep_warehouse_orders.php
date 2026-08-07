<?php echo form_open('admin/manufactures_temp/saveKeepWarehouseOrders/', array('id' => 'handling-keep-stock-manu')); ?>
<div class="modal-dialog modal-lg" style="width: 90%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('Giữ kho đơn hàng') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <?php
                    $product_id = $this->input->post('product_id');
                    $dtProduct = $this->products_model->rowProduct($product_id);

                    $start_date_search = !empty($this->input->post('start_date_search_manufactures')) ? ($this->input->post('start_date_search_manufactures')) : date('Y-m-d');
                    $end_date_search = !empty($this->input->post('end_date_search_manufactures')) ? ($this->input->post('end_date_search_manufactures')) : date('Y-m-d');
                    $customer_search = !empty($this->input->post('customer_search_manufactures')) ? $this->input->post('customer_search_manufactures') : 0;
                    $type_orders_search_manufactures = $this->input->post('type_orders_search_manufactures');
                    $search_date_order_manufactures = $this->input->post('search_date_order_manufactures');
                    ?>
                    <div class="hide"><?= lang('tnh_products', 'products_keep') ?></div>
                    <div class="hide">
                        <?= $dtProduct['name'] ?>(<?= $dtProduct['code'] ?>)
                    </div>
                </div>
                <div class="col-md-12 mtop10">
                    <div class="table-responsive">
                        <table id="table-view-items" class="table dataTable table-view-items" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;">
                                    </th>
                                    <th class="text-center" style="width: 120px;"><?= lang('dt_product_code') ?></th>
                                    <th class="text-center" style="width: 120px;"><?= lang('dt_product_name') ?></th>
                                    <th class="text-center" style="width: 80px;"><?= lang('tnh_conversion_unit') ?></th>
                                    <th class="text-center" style="width: 100px;"><?= lang('Ngày đơn hàng') ?></th>
                                    <th class="text-center" style="width: 100px;"><?= lang('customers') ?></th>
                                    <th class="text-center" style="width: 100px;"><?= lang('tnh_orders') ?></th>
                                    <th class="text-center" style="width: 100px;"><?= lang('dt_date_delivery') ?></th>
                                    <th class="text-center" style="width: 100px;"><?= lang('tnh_type_orders') ?></th>
                                    <th class="text-center" style="width: 100px;"><?= lang('quantity') ?></th>
                                    <th class="text-center" style="width: 350px;"><?= lang('Kho hàng - Số lượng') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $slKeep = "COALESCE((
                                        SELECT SUM(tbltransfer_warehouse_detail.quantity_net) as quantity_net
                                        FROM tbltransfer_warehouse_detail
                                        WHERE tbltransfer_warehouse_detail.order_id_item = tbl_order_items.id AND tbltransfer_warehouse_detail.tranfer_business_item_id = 0
                                    ), 0)";

                                $keepTranferBusinessItem = 'COALESCE((
                                    SELECT
                                        SUM(tbl_tranfer_business_item.quantity) as quantity
                                    FROM tbl_tranfer_business_item
                                    WHERE tbl_tranfer_business_item.order_item_id = tbl_order_items.id
                                ), 0)';

                                $tbQuantity = "(
                                        SELECT
                                            tbl_order_items.order_id as order_id,
                                            tbl_order_items.item_id,
                                            tbl_order_items.id as order_item_id,
                                            tbl_order_item_shippings.date_shipping,
                                            SUM((tbl_order_item_shippings.quantity_shipping * IF (tbl_products.unit_id = tbl_order_items.unit_id, tbl_products.conversion_quantity_unit, 1)) - tbl_order_item_shippings.quantity_plan_item - ($slKeep) - $keepTranferBusinessItem) as quantity_delivery,
                                            tbl_order_items.unit_id as unit_id_order
                                        FROM tbl_order_items
                                        INNER JOIN tbl_order_item_shippings ON tbl_order_items.id = tbl_order_item_shippings.order_item_id
                                        INNER JOIN tbl_products ON tbl_products.id = tbl_order_items.item_id
                                        WHERE tbl_order_items.type_item = 'products' AND tbl_order_items.item_id IN (" . $product_id . ") AND tbl_order_item_shippings.date_shipping >= '$start_date_search' AND tbl_order_item_shippings.date_shipping <= '$end_date_search'
                                        GROUP BY tbl_order_items.order_id, tbl_order_items.item_id, tbl_order_items.id
                                    ) tb_quantity";

                                $this->db->select('
                                        tbl_orders.id as order_id,
                                        tbl_products.code as code,
                                        tbl_products.name as name,
                                        tbl_orders.date as date,
                                        tblclients.company as company,
                                        tbl_orders.reference_no as reference_no,
                                        tb_quantity.date_shipping as date_shipping,
                                        tbl_type_orders.name as name_type_orders,
                                        tb_quantity.quantity_delivery as quantity_delivery,
                                        tbl_products.id as item_id,
                                        tb_quantity.order_item_id as order_item_id,
                                        tbl_type_orders.color as color_type_orders,
                                        tblunits.unit as unit_name,
                                        tbl_products.conversion_quantity_unit as conversion_quantity_unit,
                                        tbl_products.unit_id as unit_id,
                                        tb_quantity.unit_id_order as unit_id_order,
                                    ', false);
                                $this->db->from('tbl_orders');
                                $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id');
                                $this->db->join($tbQuantity, 'tbl_orders.id = tb_quantity.order_id');
                                $this->db->join('tbl_products', 'tbl_products.id = tb_quantity.item_id');
                                $this->db->join('tblunits', 'tblunits.unitid = tbl_products.conversion_unit', 'left');
                                $this->db->join('tbl_type_orders', 'tbl_type_orders.id = tbl_orders.type_orders');

                                if (!empty($customer_search)) {
                                    $this->db->where('tbl_orders.customer_id', $customer_search);
                                }
                                if (!empty($type_orders_search_manufactures)){
                                    $this->db->where('tbl_orders.type_orders IN ('.$type_orders_search_manufactures.')');
                                }

                                if (!empty($search_date_order_manufactures)){
                                    $search_date_order_manufactures = explode(' - ', $search_date_order_manufactures);
                                    $search_date_order_manufactures_start = to_sql_date($search_date_order_manufactures[0]) .' 00:00:00';
                                    $search_date_order_manufactures_end = to_sql_date($search_date_order_manufactures[1]).' 23:59:59';

                                    $this->db->where('tbl_orders.date >= "'.$search_date_order_manufactures_start.'" AND tbl_orders.date <= "'.$search_date_order_manufactures_end.'"');
                                }

                                $this->db->where('tb_quantity.quantity_delivery > 0 AND tbl_orders.status = "approved"', false, false);
                                $this->db->where('tbl_orders.is_cancel', 0);
                                $orders = $this->db->get()->result_array();
                                $counter = 0;
                                ?>
                                <?php if (!empty($orders)) : ?>
                                    <?php foreach ($orders as $key => $aRow) : ?>
                                        <?php
                                            $order_id = $aRow['order_id'];
                                            $type_warehouse = 'product';
                                            $item_id = $aRow['item_id'];
                                            $order_item_id = $aRow['order_item_id'];
                                            $exchange_unit = 1;
                                            $exchange_stock = $aRow['conversion_quantity_unit'];
                                            $conversion_quantity_unit = 1;
                                            if ($aRow['unit_id_order'] == $aRow['unit_id']) {
                                                $conversion_quantity_unit = $aRow['conversion_quantity_unit'];
                                            }
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="text-center">
                                                    <div class="">
                                                        <input type="hidden" name="items[<?= $order_id ?>][product_id]" class="form-control" value="<?= $item_id ?>">
                                                        <input type="hidden" name="items[<?= $order_id ?>][quantity_delivery]" class="form-control" value="<?= $aRow['quantity_delivery'] ?>">
                                                        <input type="checkbox" name="items[<?= $order_id ?>][order_item_id]" class="order_item_id" id="order_item_id<?= $order_id ?>" value="<?= $order_item_id ?>"><label for="order_item_id<?= $order_id?>"></label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?= $aRow['code'] ?>
                                            </td>
                                            <td>
                                                <?= $aRow['name'] ?>
                                            </td>
                                            <td class="text-center">
                                                <?= $aRow['unit_name'] ?>
                                            </td>
                                            <td>
                                                <div class="text-center"><?= _d($aRow['date']) ?></div>
                                            </td>
                                            <td><?= $aRow['company'] ?></td>
                                            <td><?= $aRow['reference_no'] ?></td>
                                            <td>
                                                <div class="text-center"><?= _d($aRow['date_shipping']) ?></div>
                                            </td>
                                            <?php
                                            $color_type_orders = $aRow['color_type_orders'];
                                            $name_type_orders = '<div class="text-center"><span class="btn" style="background: ' . $color_type_orders . '; color: white; cursor: auto;">' . $aRow['name_type_orders'] . '<span></div>';
                                            ?>
                                            <td><?= $name_type_orders ?></td>

                                            <td><div class="text-center"><?= formatNumber($aRow['quantity_delivery']) ?></div></td>
                                            <td>
                                                <?php
                                                    $this->db->select("
                                                        tblwarehouse_items.*,
                                                        SUM(tblwarehouse_items.product_quantity) as product_quantity,
                                                        tbllocaltion_warehouses.name as name_local,
                                                        tbllocaltion_warehouses.pod_id as pod_id,
                                                        tblwarehouse.name as name_warehouse,
                                                        tblwarehouse_items.lot_code as lot_code,
                                                        tblwarehouse_items.date_sx as date_sx,
                                                        tblwarehouse_items.date_sd as date_sd,
                                                        tblwarehouse_items.date_use as date_use
                                                        ");
                                                    $this->db->where('tblwarehouse_items.product_quantity > ', 0);
                                                    $this->db->where('tblwarehouse_items.id_items', $item_id);
                                                    $this->db->where('tblwarehouse_items.type_items', $type_warehouse);
                                                    $this->db->where('tblwarehouse.id !=', WAREHOUSES_CAPACITY);
                                                    $this->db->where('tblwarehouse.id !=', WAREHOUSES_HOLD);
                                                    $this->db->where('tblwarehouse.id !=', WAREHOUSES_ERRORS);
                                                    $this->db->where('tblwarehouse.id !=', WAREHOUSES_TAMP);
                                                    $this->db->where('tbllocaltion_warehouses.stage_id', 0);
                                                    $this->db->join(
                                                        'tbllocaltion_warehouses',
                                                        'tbllocaltion_warehouses.id = tblwarehouse_items.localtion and tbllocaltion_warehouses.warehouse = tblwarehouse_items.warehouse_id',
                                                        'left'
                                                    );
                                                    $this->db->join('tblwarehouse', 'tblwarehouse.id = tbllocaltion_warehouses.warehouse', 'left');
                                                    $this->db->group_start();
                                                    $this->db->where('tbllocaltion_warehouses.pod_id', 0);
                                                    $this->db->or_where('exists (
                                                        SELECT tbl_productions_orders_details.id
                                                        FROM tbl_productions_orders_details
                                                        WHERE tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id AND tbl_productions_orders_details.object_type = "business_plan"
                                                    )', false, false);
                                                    $this->db->or_where('exists (
                                                        SELECT tbl_productions_orders_details.id
                                                        FROM tbl_productions_orders_details
                                                        INNER JOIN tbl_orders ON tbl_orders.id = tbl_productions_orders_details.object_id
                                                        WHERE tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id AND tbl_productions_orders_details.object_type = "orders" AND tbl_orders.type_orders = 4
                                                    )', false, false);
                                                    $this->db->group_end();
                                                    $this->db->group_by('tblwarehouse_items.warehouse_id, tblwarehouse_items.localtion,lot_code,date_sx,date_sd,date_use');
                                                    $warehouse = $this->db->get('tblwarehouse_items')->result_array();
                                                    if (!empty($warehouse)){
                                                        foreach ($warehouse as $kk => $vv){
                                                            $warehouse[$kk]['date_sx'] = !empty($vv['date_sx']) ? _dhau($vv['date_sx']) : '';
                                                            $warehouse[$kk]['date_sd'] = !empty($vv['date_sd']) ? _dhau($vv['date_sd']) : '';
                                                            $warehouse[$kk]['lot_code'] = !empty($vv['lot_code']) ? ($vv['lot_code']) : '';
                                                            $warehouse[$kk]['date_use'] = !empty($vv['date_use']) ? ($vv['date_use']) : '';
                                                        }
                                                    }

                                                    $htmlWarehouseNew = '';
                                                    if (!empty($warehouse)) {
                                                        foreach ($warehouse as $k => $val) {
                                                            // isChecked = '';
                                                            // quantityW = 0;
                                                            // tempQuantity = cQuantityNeedHold;
                                                            // if (cQuantityNeedHold > 0) {
                                                            //     cQuantityNeedHold = cQuantityNeedHold - el.product_quantity;
                                                            //     if (cQuantityNeedHold > 0) {
                                                            //         quantityW = el.product_quantity;
                                                            //     } else {
                                                            //         quantityW = tempQuantity;
                                                            //     }
                                                            // }

                                                            // if (quantityW > 0) {
                                                            //     isChecked = 'checked';
                                                            // }

                                                            // $val['product_quantity'] = $val['product_quantity']/$conversion_quantity_unit;

                                                            $this->db->select('tbl_business_plan.reference_no as reference_no');
                                                            $this->db->from('tbl_productions_orders_details');
                                                            $this->db->join('tbl_business_plan','tbl_business_plan.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "business_plan"');
                                                            $this->db->where('tbl_productions_orders_details.id',$val['pod_id']);
                                                            $bussiness = $this->db->get()->row_array();

                                                            if (empty($bussiness)) {
                                                                $this->db->select('tbl_orders.reference_no as reference_no');
                                                                $this->db->from('tbl_productions_orders_details');
                                                                $this->db->join('tbl_orders','tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"');
                                                                $this->db->where('tbl_productions_orders_details.id',$val['pod_id']);
                                                                $bussiness = $this->db->get()->row_array();
                                                            }

                                                            $pod_id = $val['pod_id'];
                                                            if (!empty($pod_id)) {
                                                                // $tranfer_business_plan = "(
                                                                //     SELECT
                                                                //         tbl_productions_orders_details.id as pod_id,
                                                                //         SUM(tbl_tranfer_business_item.quantity) as quantity_business
                                                                //     FROM tbl_tranfer_business_item
                                                                //     INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.production_plan_item_id = tbl_tranfer_business_item.business_plan_item_id
                                                                //     INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
                                                                //     WHERE tbl_productions_orders_items.object_item_type = 'business_plan'
                                                                //     AND tbl_productions_orders_details.id = $pod_id
                                                                // )";
                                                                // $dtTranferBusinessPlan = $this->db->query($tranfer_business_plan)->row_array();
                                                                // if (!empty($dtTranferBusinessPlan)) {
                                                                //     $val['product_quantity'] = $val['product_quantity'] - $dtTranferBusinessPlan['quantity_business'];
                                                                // }
                                                                
                                                                // if ($val['product_quantity'] <= 0) {
                                                                //     continue;
                                                                // }
                                                                // print_arrays($dtTranferBusinessPlan);
                                                            }


                                                            $tdTick = '<div class="checkbox checkbox-info" style="margin-bottom: 0;">
                                                                <input type="checkbox" onChange="totalKeepSupplies()" class="tick" name="items['.$order_id.'][tick]['.$counter.']"
                                                                value="'.$val['warehouse_id'].'__'.$val['localtion'].'" id="tick-'.$counter.'-'.$k.'">
                                                                <label for="tick-'.$counter.'-'.$k.'"></label>
                                                            </div>';

                                                            $date_sx = $val['date_sx'] != null ? $val['date_sx'] : '';
                                                            $date_sd = $val['date_sd'] != null ? $val['date_sd'] : '';
                                                            $tdWarehouseNew = '<div>'.$val['name_warehouse'].' -'.$val['name_local'].'
                                                                - <b class="text-primary">'.formatNumber($val['product_quantity']).'
                                                                </b>'.(!empty($bussiness['reference_no']) ? '('.$bussiness['reference_no'].')' : '').'</div><div>
                                                                <p>Lot: '.$val['lot_code'].'</p>
                                                                <p>Ngày SX: '.$val['date_sx'].'</p>
                                                                <p>Ngày SD: '.$val['date_sd'].'</p>
                                                            </div><div class="show-errors text-danger"></div>';
                                                            $tdQuantityCoordinator = '
                                                                <input type="text"  quantity-warehouse="'.$val['product_quantity'].'"
                                                                onChange="totalKeepSuppliesCheck(this)" name="items['.$order_id.'][quantity_coordinator]['.$counter.']" class="form-control quantity-coordinator" value="0">
                                                                <input type="hidden" name="items['.$order_id.'][lot_code]['.$counter.']" class="form-control" value="'.$val['lot_code'].'">
                                                                <input type="hidden" name="items['.$order_id.'][date_sx]['.$counter.']" class="form-control" value="'.$val['date_sx'].'">
                                                                <input type="hidden" name="items['.$order_id.'][date_sd]['.$counter.']" class="form-control" value="'.$val['date_sd'].'">
                                                                <input type="hidden" name="items['.$order_id.'][date_use]['.$counter.']" class="form-control" value="'.$val['date_use'].'">
                                                            ';

                                                            $htmlWarehouseNew.= '<tr class="not-tr">
                                                                <td style="width: 50px;" class="text-center">'.$tdTick.'</td>
                                                                <td style="width: 350px;">'.$tdWarehouseNew.'</td>
                                                                <td style="width: 100px;">'.$tdQuantityCoordinator.'</td>
                                                                </tr>';
                                                            $counter++;
                                                        }
                                                    } else {
                                                        $htmlWarehouseNew = '<div class="label label-danger" style="border-radius:15px;padding:5px 10px;">Tồn kho hết</div>';
                                                    }
                                                    $tdWarehouses = '<table class="tnh-table table-warehouse" style="margin: 0;">
                                                        '.$htmlWarehouseNew.'
                                                    </table>';
                                                    echo $tdWarehouses;
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                            $counter++;
                                        ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <!-- <tfoot>
                                <tr class="bold">
                                    <td></td>
                                    <td class="text-center"><?= '' //lang('tnh_grand_total') 
                                                            ?></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot> -->
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-default btn-success add-keep-stock-manu"><?= lang('save') ?></button>
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    var oTableModal = '';
    var fnserverparamsModal = {
        start_date_search: "<?= $this->input->post('start_date_search_manufactures') ?>",
        end_date_search: "<?= $this->input->post('end_date_search_manufactures') ?>",
        customer_search: "<?= $this->input->post('customer_search_manufactures') ?>",
        product_id: "<?= $this->input->post('product_id') ?>",
    };

    function totalKeepSupplies() {
    }

    function totalKeepSuppliesCheck(_this) {
        $(_this).closest('tr').find('.tick').prop('checked', true);
        $(_this).parents('tr').find('.order_item_id').prop('checked', true);
    }

    $(function () {
        appValidateForm($('#handling-keep-stock-manu'), {}, handlingKeepStockManu);
        function handlingKeepStockManu(form) {
            $('.add-keep-stock-manu').attr('disabled', 'disabled');
            var data = $(form).serialize();
            var url = form.action;
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                data: data,
            })
                .done(function (data) {
                    if (data.result) {
                        alert_float('success', data.message);
                        oTable.draw(false);
                        $('.modal-dialog .close').trigger('click');
                    } else {
                        alert_float('danger', data.message);
                        $('.add-keep-stock-manu').removeAttr('disabled', 'disabled');
                    }
                })
                .fail(function () {
                    $('.add-keep-stock-manu').removeAttr('disabled', 'disabled');
                    console.log("error");
                });
            return false;
        }
        $('.status').selectpicker();
    })
</script>