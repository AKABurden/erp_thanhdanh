<?php
$customer_search = $this->input->post('customer_search');
$start_date_search = to_sql_date($this->input->post('start_date_search'));
$end_date_search = to_sql_date($this->input->post('end_date_search'));
$products_search = str_replace('__products', '', $this->input->post('products_search'));
$category_product_search = $this->input->post('category_product_search');
$type_view_search = $this->input->post('type_view_search');
$dateNow = date('Y-m-d');
$this->db->simple_query('SET SESSION group_concat_max_len=18446744073709551615');
if ($type_view_search == 1 || $type_view_search == 2) {
    $this->db->select('
        GROUP_CONCAT(distinct tbl_products.id) as product_id,
        GROUP_CONCAT(distinct tbl_orders.customer_id) as customer_id,
        GROUP_CONCAT(distinct tbl_orders.id) as order_id,
        GROUP_CONCAT(distinct tbl_order_item_shippings.order_item_id) as order_item_id,
        GROUP_CONCAT(distinct tbl_order_item_shippings.date_shipping ORDER BY tbl_order_item_shippings.date_shipping ASC) as date_shipping,
    ', false);
    $this->db->from('tbl_order_item_shippings');
    $this->db->join('tbl_order_items', 'tbl_order_items.id = tbl_order_item_shippings.order_item_id');
    $this->db->join('tbl_products', 'tbl_products.id = tbl_order_items.item_id');
    $this->db->join('tbl_orders', 'tbl_orders.id = tbl_order_items.order_id');
    if (!empty($customer_search)) {
        $this->db->where('tbl_orders.customer_id', $customer_search);
    }
    $this->db->where('(tbl_order_item_shippings.quantity_shipping > tbl_order_item_shippings.quantity_plan_item)');
    $this->db->where('tbl_order_item_shippings.date_shipping >=', $start_date_search);
    $this->db->where('tbl_order_item_shippings.date_shipping <=', $end_date_search);
    if (!empty($products_search)) {
        $this->db->where('tbl_products.id', $products_search);
    }

    if (!empty($category_product_search)) {
        $this->db->where_in('tbl_products.category_id', $category_product_search);
    }

    $this->db->where('tbl_order_items.type_item', 'products');
    $this->db->where('tbl_products.type_products', 'products');
    $this->db->where('tbl_orders.status', 'approved');
    $order_items_shipping = $this->db->get()->row_array();

    //business plan
    if ($type_view_search == 1) {
        $this->db->select('
            GROUP_CONCAT(distinct tbl_products.id) as product_id,
            GROUP_CONCAT(distinct tbl_business_plan.id) as business_plan_id,
            GROUP_CONCAT(distinct tbl_business_plan_items_date.business_plan_items_id) as business_plan_items_id,
            GROUP_CONCAT(distinct tbl_business_plan_items_date.date ORDER BY tbl_business_plan_items_date.date ASC) as date_shipping,
        ', false);
        $this->db->from('tbl_business_plan_items_date');
        $this->db->join('tbl_business_plan_items', 'tbl_business_plan_items.id = tbl_business_plan_items_date.business_plan_items_id');
        $this->db->join('tbl_business_plan', 'tbl_business_plan.id = tbl_business_plan_items.business_plan_id');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_business_plan_items.items_id');
        $this->db->where('(tbl_business_plan_items_date.quantity > tbl_business_plan_items_date.quantity_plan_item)');
        $this->db->where('tbl_business_plan_items_date.date >=', $start_date_search);
        $this->db->where('tbl_business_plan_items_date.date <=', $end_date_search);
        if (!empty($products_search)) {
            $this->db->where('tbl_products.id', $products_search);
        }

        if (!empty($category_product_search)) {
            $this->db->where_in('tbl_products.category_id', $category_product_search);
        }
        $this->db->where('tbl_products.type_products', 'products');
        $this->db->where('tbl_business_plan.status', 'approved');
        $business_plan_items_date = $this->db->get()->row_array();
    }

    $thHead = '';
    $arrProductId = [];
    $arrOrderItemId = [];
    $arrDateShipping = [];
    $arrCustomerId = [];
    $arrOrderId = [];

    if ($type_view_search == 2) {
        if (!empty($order_items_shipping['product_id'])) {
            $arrDateShipping = explode(',', $order_items_shipping['date_shipping']);
            $arrOrderItemId = explode(',', $order_items_shipping['order_item_id']);
            $arrProductId = explode(',', $order_items_shipping['product_id']);
            $arrCustomerId = explode(',', $order_items_shipping['customer_id']);
            $arrOrderId = explode(',', $order_items_shipping['order_id']);
            if (!empty($arrDateShipping)) {
                foreach ($arrDateShipping as $key => $value) {
                    $strWarning = '';
                    $thHead .= '<th class="text-center" style="width: 80px; '.$strWarning.'">' . _d($value) . '</th>';
                }
            }
        }
    } else if ($type_view_search == 1) {
        $arrDateShippingOrders = !empty($order_items_shipping['date_shipping']) ? explode(',', $order_items_shipping['date_shipping']) : [];
        $arrDateShippingOrderBusinessPlan = !empty($business_plan_items_date['date_shipping']) ? explode(',', $business_plan_items_date['date_shipping']) : [];
        $arrDateShipping = array_merge($arrDateShippingOrders, $arrDateShippingOrderBusinessPlan);
        $arrDateShipping = array_unique($arrDateShipping);
        usort($arrDateShipping, "date_sort");

        $arrOrderItemId = !empty($order_items_shipping['order_item_id']) ? explode(',', $order_items_shipping['order_item_id']) : [];
        $arrBusinessPlanItemId = !empty($business_plan_items_date['business_plan_items_id']) ? explode(',', $business_plan_items_date['business_plan_items_id']) : [];

        $arrCustomerId = !empty($order_items_shipping['customer_id']) ? explode(',', $order_items_shipping['customer_id']) : [];

        $arrOrderId = !empty($order_items_shipping['order_id']) ? explode(',', $order_items_shipping['order_id']) : [];
        $arrBusinessPlanId = !empty($business_plan_items_date['business_plan_id']) ? explode(',', $business_plan_items_date['business_plan_id']) : [];

        $arrProductOrdersId = !empty($order_items_shipping['product_id']) ? explode(',', $order_items_shipping['product_id']) : [];
        $arrProductBusinessPlanId = !empty($business_plan_items_date['product_id']) ? explode(',', $business_plan_items_date['product_id']) : [];
        $arrProductId = array_merge($arrProductOrdersId, $arrProductBusinessPlanId);
        $arrProductId = array_unique($arrProductId);
        if (!empty($arrDateShipping)) {
            foreach ($arrDateShipping as $key => $value) {
                $strWarning = '';
                $thHead .= '<th class="text-center" style="width: 80px; '.$strWarning.'">' . _d($value) . '</th>';
            }
        }
    }
}
?>

<?php if ($type_view_search == 1) : ?>
    <table id="tb-manufactures" class="table table-hover table-tb-manufactures" style="min-width: 100%;">
        <thead>
            <tr>
                <th style="width: 30px;">
                    <div class="mass_select_all_wrap text-center">
                        <input type="checkbox" id="mass_select_all" data-to-table="tb-manufactures">
                        <label for="mass_select_all"></label>
                    </div>
                </th>
                <th class="text-center" style="width: 150px;"><?= lang('tnh_items') ?></th>
                <th class="text-center" style="width: 80px;"><?= lang('Tổng SL cần sản xuất') ?></th>
                <th class="text-center" style="width: 80px;"><?= lang('Tổng SL hàng sẵn trong kho') ?></th>
                <?= $thHead ?>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($arrProductId)) : ?>
                <?php foreach ($arrProductId as $key => $value) : ?>
                    <?php
                    $this->db->select('
                        tbl_products.id as id,
                        tbl_products.code as code,
                        tbl_products.name as name,
                    ', false);
                    $this->db->from('tbl_products');
                    $this->db->where('tbl_products.id', $value);
                    $products = $this->db->get()->row_array();

                    $tdQuantityShipping = '';
                    $totalQuantity = 0;
                    
                    $slKeep = 0;

                    if (!empty($arrOrderItemId)) {
                        $this->db->select('
                            tbl_order_item_shippings.date_shipping as date_shipping,
                            SUM(tbl_order_item_shippings.quantity_shipping - tbl_order_item_shippings.quantity_plan_item - IF (tbl_order_items.quantity_delivery > 0, tbl_order_items.quantity_delivery, ' . $slKeep . ')) as quantity_delivery,
                        ', false);
                        $this->db->from('tbl_order_item_shippings');
                        $this->db->join('tbl_order_items', 'tbl_order_items.id = tbl_order_item_shippings.order_item_id');
                        $this->db->where_in('tbl_order_item_shippings.date_shipping', $arrDateShipping);
                        $this->db->where('tbl_order_items.item_id', $value);
                        $this->db->where_in('tbl_order_item_shippings.order_item_id', $arrOrderItemId);
                        $this->db->group_by('tbl_order_item_shippings.date_shipping');
                        $dtDateShipping = $this->db->get()->result_array();
                    }


                    //business_plan
                    if (!empty($arrBusinessPlanItemId)) {
                        $this->db->select('
                            tbl_business_plan_items_date.date as date_shipping,
                            SUM(tbl_business_plan_items_date.quantity - tbl_business_plan_items_date.quantity_plan_item) as quantity_delivery,
                        ', false);
                        $this->db->from('tbl_business_plan_items_date');
                        $this->db->join('tbl_business_plan_items', 'tbl_business_plan_items.id = tbl_business_plan_items_date.business_plan_items_id');
                        $this->db->where_in('tbl_business_plan_items_date.date', $arrDateShipping);
                        $this->db->where('tbl_business_plan_items.items_id', $value);
                        $this->db->where_in('tbl_business_plan_items_date.business_plan_items_id', $arrBusinessPlanItemId);
                        $this->db->group_by('tbl_business_plan_items_date.date');
                        $dtDateShippingBusinessPlan = $this->db->get()->result_array();
                    }

                    foreach ($arrDateShipping as $kDate => $vDate) {
                        $order_item_shippings = null;

                        if (!empty($dtDateShipping)) {
                            foreach ($dtDateShipping as $kD => $vD) {
                                if ($vD['date_shipping'] == $vDate) {
                                    $order_item_shippings = $vD;
                                    break;
                                }
                            }
                        }

                        //business plan
                        if (!empty($dtDateShippingBusinessPlan)) {
                            foreach ($dtDateShippingBusinessPlan as $kD => $vD) {
                                if ($vD['date_shipping'] == $vDate) {
                                    if (!empty($order_item_shippings)) {
                                        $order_item_shippings['quantity_delivery'] = $order_item_shippings['quantity_delivery'] + $vD['quantity_delivery'];
                                    } else {
                                        $order_item_shippings = $vD;
                                    }
                                    break;
                                }
                            }
                        }


                        if (empty($order_item_shippings)) {
                            $order_item_shippings['quantity_delivery'] = 0;
                        }

                        $txtQuantity = !empty($order_item_shippings['quantity_delivery']) ? formatNumber($order_item_shippings['quantity_delivery']) : '';

                        $strWarning = '';
                        if ($dateNow > $vDate && $order_item_shippings['quantity_delivery'] > 0) {
                            $strWarning = 'background: #ff000052 !important;';
                        }

                        $tdQuantityShipping .= '<td class="text-center" style="'.$strWarning.'">' . $txtQuantity . '</td>';
                        $totalQuantity += $order_item_shippings['quantity_delivery'];
                    }

                    if ($totalQuantity <= 0) {
                        continue;
                    }

                    $quanityKeepDelivery = 0;
                    $tdQuantityKeep = '';
                    $txtQuantityKeep = !empty($quantityKeep) ? formatNumber($quantityKeep) : '';
                    $tdQuantityKeep .= '<div class="text-center"><span class="pointer bold text-primary" onclick="showDetailManufacturesKeep(this, ' . $value . ')">' . $txtQuantityKeep . '</span></div>';

                    $quantityW = $this->manufactures_model->totalQuantityWarehouseManufactures('product', $value);
                    $txtQuantityW = !empty($quantityW['product_quantity']) ? formatNumber($quantityW['product_quantity']) : '';
                    $tdQuantityWarehouses = '<td class="text-center bold"><span class="pointer bold text-primary" onclick="showDetailManufacturesWarehouses(this, ' . $value . ')">' . $txtQuantityW . '</span></td>';

                    echo '<tr>
                        <td class="text-center">
                            <div class=""><input type="checkbox" name="product_id[]" id="check-item' . $value . '" value="' . $value . '"><label for="check-item' . $value . '"></label></div>
                        </td>
                        <td class="text-left">' . $products['name'] . '(' . $products['code'] . ')</td>
                        <td class="text-center text-danger bold" style="font-size: 18px;"><span class="pointer" onclick="showDetailManufactures(this, ' . $value . ')">' . formatNumber($totalQuantity) . '</span></td>
                        ' . $tdQuantityWarehouses . '
                        ' . $tdQuantityShipping . '
                    </tr>';
                    ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
        $(document).ready(function() {
            var dt = $('#tb-manufactures').DataTable({
                "language": lang_datatables,
                'searching': false,
                'ordering': false,
                'paging': false,
                scrollY: height_body,
                scrollX: true,
                fixedColumns: {
                    leftColumns: 3,
                    rightColumns: 0
                },
                "initComplete": function(settings, json) {
                    var t = this;
                    $('.table-loading').removeClass('table-loading');
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    mainWrapperHeightFix();
                },
                'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            });
        });
    </script>
<?php elseif ($type_view_search == 2) : ?>
    <table id="tb-manufactures" class="table table-hover table-tb-manufactures" style="min-width: 100%;">
        <thead>
            <tr>
                <th class="text-center" style="width: 150px;"><?= lang('customers') ?></th>
                <th class="text-center" style="width: 150px;"><?= lang('orders') ?></th>
                <th class="text-center" style="width: 150px;"><?= lang('tnh_items') ?></th>
                <th class="text-center" style="width: 80px;"><?= lang('Tổng SL cần sản xuất') ?></th>
                <th class="text-center" style="width: 80px;"><?= lang('Tổng SL hàng sẵn trong kho') ?></th>
                <?= $thHead ?>
            </tr>
        </thead>
        <tbody>
            <?php
                $tdDateM = '';
                foreach ($arrDateShipping as $kDate => $vDate) {
                    $tdDateM.= '<td></td>';
                }
            ?>
            <?php if(!empty($arrCustomerId)): ?>
                <?php foreach($arrCustomerId as $key => $value): ?>
                    <?php
                        $this->db->select('
                            tblclients.company as company
                        ', false);
                        $this->db->from('tblclients');
                        $this->db->where('tblclients.userid', $value);
                        $dtCustomer = $this->db->get()->row_array();
                        if (!empty($arrOrderId)) {
                            $slKeep = 0;

                            $isExists = "(
                                SELECT
                                    tbl_order_items.id
                                FROM tbl_order_item_shippings
                                INNER JOIN tbl_order_items ON tbl_order_items.id = tbl_order_item_shippings.order_item_id
                                WHERE tbl_order_items.order_id = tbl_orders.id AND tbl_order_item_shippings.date_shipping >= ' $start_date_search' AND tbl_order_item_shippings.date_shipping <= ' $end_date_search'
                                HAVING SUM(tbl_order_item_shippings.quantity_shipping - tbl_order_item_shippings.quantity_plan_item - IF (tbl_order_items.quantity_delivery > 0, tbl_order_items.quantity_delivery, $slKeep)) > 0
                            )";

                            $this->db->select('tbl_orders.id, tbl_orders.reference_no', false);
                            $this->db->from('tbl_orders');
                            $this->db->where_in('tbl_orders.id', $arrOrderId);
                            $this->db->where('tbl_orders.customer_id', $value);
                            $this->db->where(" exists $isExists", false, false);
                            // print_arrays($this->db->get_compiled_select());
                            $dtOrders = $this->db->get()->result_array();
                            if (empty($dtOrders)) {
                                continue;
                            }
                        }
                    ?>
                    <tr class="bold">
                        <td>
                            <?= $dtCustomer['company'] ?>
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <?= $tdDateM ?>
                    </tr>
                    <?php if(!empty($arrOrderId)): ?>
                        <?php
                                
                        ?>
                        <?php if(!empty($dtOrders)): ?>
                            <?php foreach($dtOrders as $kOrder => $vOrder): ?>
                                <?php
                                    //order items
                                    $this->db->select('
                                        tbl_order_items.id as order_item_id,
                                        tbl_products.id as product_id,
                                        tbl_products.code as code,
                                        tbl_products.name as name,
                                    ');
                                    $this->db->from('tbl_order_items');
                                    $this->db->join('tbl_products', 'tbl_products.id = tbl_order_items.item_id');
                                    $this->db->where_in('tbl_order_items.id', $arrOrderItemId);
                                    $this->db->where('tbl_order_items.order_id', $vOrder['id']);
                                    $this->db->where('tbl_order_items.type_item', 'products');
                                    $this->db->where('tbl_products.type_products', 'products');
                                    $products = $this->db->get()->result_array();
                                    $trProducts = '';
                                    if (!empty($products)) {
                                        foreach ($products as $kP => $vP) {
                                            $tdQuantityShipping = '';
                                            $totalQuantity = 0;
                                            
                                            $slKeep = 0;

                                            $this->db->select('
                                                tbl_order_item_shippings.date_shipping as date_shipping,
                                                SUM(tbl_order_item_shippings.quantity_shipping - tbl_order_item_shippings.quantity_plan_item - IF (tbl_order_items.quantity_delivery > 0, tbl_order_items.quantity_delivery, ' . $slKeep . ')) as quantity_delivery
                                            ', false);
                                            $this->db->from('tbl_order_item_shippings');
                                            $this->db->join('tbl_order_items', 'tbl_order_items.id = tbl_order_item_shippings.order_item_id');
                                            $this->db->where_in('tbl_order_item_shippings.date_shipping', $arrDateShipping);
                                            // $this->db->where('tbl_order_items.item_id', $value);
                                            $this->db->where('tbl_order_item_shippings.order_item_id', $vP['order_item_id']);
                                            $this->db->group_by('tbl_order_item_shippings.date_shipping');
                                            $dtDateShipping = $this->db->get()->result_array();

                                            foreach ($arrDateShipping as $kDate => $vDate) {
                                                $order_item_shippings = null;
                                                foreach ($dtDateShipping as $kD => $vD) {
                                                    if ($vD['date_shipping'] == $vDate) {
                                                        $order_item_shippings = $vD;
                                                        break;
                                                    }
                                                }

                                                if (empty($order_item_shippings)) {
                                                    $order_item_shippings['quantity_delivery'] = 0;
                                                }
                        
                                                $txtQuantity = !empty($order_item_shippings['quantity_delivery']) ? formatNumber($order_item_shippings['quantity_delivery']) : '';
                                                $tdQuantityShipping .= '<td class="text-center">' . $txtQuantity . '</td>';
                                                $totalQuantity += $order_item_shippings['quantity_delivery'];
                                            }

                                            if ($totalQuantity <= 0) {
                                                continue;
                                            }

                                            $quanityKeepDelivery = 0;
                                            $tdQuantityKeep = '';

                                            $txtQuantityKeep = !empty($quantityKeep) ? formatNumber($quantityKeep) : '';
                                            $tdQuantityKeep .= '<div class="text-center">' . $txtQuantityKeep . '</div>';

                                            $quantityW = $this->manufactures_model->totalQuantityWarehouseManufactures('product', $value);
                                            $txtQuantityW = !empty($quantityW['product_quantity']) ? formatNumber($quantityW['product_quantity']) : '';
                                            $tdQuantityWarehouses = '<td class="text-center bold">' . $txtQuantityW . '</td>';

                                            $trProducts.= '<tr>
                                                <td></td>
                                                <td></td>
                                                <td class="text-left">' . $vP['name'] . '(' . $vP['code'] . ')<div class=""></td>
                                                <td class="text-center text-danger bold" style="font-size: 18px;">' . formatNumber($totalQuantity) . '</td>
                                                ' . $tdQuantityWarehouses . '
                                                ' . $tdQuantityShipping . '
                                            </tr>';
                                        }
                                    }
                                ?>
                                <?php if(!empty($trProducts)): ?>
                                    <tr class="bold">
                                        <td></td>
                                        <td><?= $vOrder['reference_no'] ?></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <?= $tdDateM ?>
                                    </tr>
                                    <?php echo $trProducts; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
        $(document).ready(function() {
            var dt = $('#tb-manufactures').DataTable({
                "language": lang_datatables,
                'searching': false,
                'ordering': false,
                'paging': false,
                scrollY: height_body,
                scrollX: true,
                fixedColumns: {
                    leftColumns: 3,
                    rightColumns: 0
                },
                "initComplete": function(settings, json) {
                    var t = this;
                    $('.table-loading').removeClass('table-loading');
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    mainWrapperHeightFix();
                },
                'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            });
        });
    </script>
<?php endif; ?>