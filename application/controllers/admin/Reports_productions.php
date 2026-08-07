<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Reports_productions extends AdminController
{
    /**
     * Codeigniter Instance
     * Expenses detailed report filters use $ci
     * @var object
     */
    private $ci;

    public function __construct()
    {
        parent::__construct();
        // if (!has_permission('reports', '', 'view')) {
        //     access_denied('reports');
        // }
        $this->ci = &get_instance();
        $this->load->model('reports_model');
        $this->load->model('dashboard_model');
        $this->load->model('reports_model');
        $this->load->model('dashboard_model');
        $this->load->model('orders_model');
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');

        $this->perViewProductionsOrders = has_permission('manufactures_productions_orders', '', 'view');
    }

    public function tableProduct_error_old()
    {
        if (!$this->perViewProductionsOrders) {
            accessDenied($js = true);
        }

        $products = $this->input->post('products');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $productions_orders_search = $this->input->post('productions_orders_search');

        $aColumns = [
            'tbl_productions_orders.id as po_id',
            'tbl_productions_orders.date as date',
            'tbl_productions_orders.reference_no as reference_no',
            'tbl_products.code as item_code',
            'tbl_products.name as item_name',
            'SUM(tbl_productions_orders_items.quantity) as production_quantity',
            '(SUM(tbl_productions_orders_items.quantity) - SUM(tbl_purchase_products.total_quantity)) as import_quantity',
            'SUM(tbl_purchase_products.total_quantity) as error_quantity',
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_purchase_products';
        $join = [
            'LEFT JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id',
            'LEFT JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id',
            'LEFT JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id',
            'LEFT JOIN tbl_products ON tbl_products.id = tbl_productions_orders_items.items_id',
        ];
        $groupBy = 'GROUP BY tbl_productions_orders_items.id, tbl_products.id';
        $where        = [
            'AND tbl_purchase_products.is_errors = 1',
        ];
        if (!empty($productions_orders_search)) {
            array_push($where, "AND tbl_productions_orders.id IN ($productions_orders_search)");
        }
        if (!empty($products)) {
            $product_id = '';
            $arr_products = explode(",", $products);
            foreach ($arr_products as $product) {
                $product_id .= explode("__", $product)[0] . ', ';
            }
            $product_id = substr($product_id, 0, -2);
            array_push($where, "AND tbl_products.id IN ($product_id)");
        }
        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_productions_orders.date >= '$start_date_search'");
        }
        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_productions_orders.date <= '$end_date_search'");
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_purchase_products.id as pr_id',
            'tbl_productions_orders_items.items_id',
            'tbl_productions_orders_details.id as pr_detail_id',
        ], $groupBy, []);

        $output = $result['output'];
        $rResult = $result['rResult'];

        $stt = 0;
        foreach ($rResult as $key => $aRow) {
            $stt++;
            $row = [];
            for ($i = 0; $i < count($aColumns); $i++) {
                if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                    $_data = $aRow[strafter($aColumns[$i], 'as ')];
                } else {
                    // $_data = $aRow[$aColumns[$i]];
                }

                if ($aColumns[$i] == 'tbl_productions_orders.id as po_id') {
                    $_data = $stt;
                }

                if ($aColumns[$i] == 'tbl_productions_orders.date as date') {
                    $_data = _dhau($aRow['date']);
                }
                if ($aColumns[$i] == 'SUM(tbl_productions_orders_items.quantity) as production_quantity') {
                    $_data = '<div class="text-right">' . number_format($aRow['production_quantity']) . '</div>';
                }
                if ($aColumns[$i] == '(SUM(tbl_productions_orders_items.quantity) - SUM(tbl_purchase_products.total_quantity)) as import_quantity') {
                    $_data = '<div class="text-right">' . number_format($aRow['import_quantity']) . '</div>';
                }
                if ($aColumns[$i] == 'SUM(tbl_purchase_products.total_quantity) as error_quantity') {
                    $_data = '<div class="text-right">' . number_format($aRow['error_quantity']) . '</div>';
                }
                $row[] = $_data;
            }

            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function tableProduct_error()
    {
        if (!$this->perViewProductionsOrders) {
            accessDenied($js = true);
        }

        $products = $this->input->post('products');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $productions_orders_search = $this->input->post('productions_orders_search');
        $prices = '(SELECT t1.`price` as prices,t1.`product_id`,t1.`product_type`
        FROM tblgroup_price_detail t1
        INNER JOIN
        (
            SELECT `product_id`,`product_type`, MAX(`price`) AS max_price
            FROM tblgroup_price_detail
            GROUP BY `product_id`,`product_type`
        ) t2
            ON t1.`product_id` = t2.`product_id` AND t1.`product_type` = t2.`product_type` AND t1.price = t2.max_price GROUP BY `product_id`,`product_type`) prices';


        $aColumns = [
            'tbl_purchase_products.id as pp_id',
            'tbl_purchase_products.date as date',
            'tbl_purchase_products.reference_no as reference_purchase',
            'tbl_productions_orders.reference_no as reference_no',
            'tbl_products.code as item_code',
            'tbl_products.name as item_name',
            'tbl_type_print.name as name_print',
            'tbl_productions_orders_items.quantity as quantity_manufactures',
            'pr_parent.total_quantity as quantity_import',
            '0 as quantity_pass',
            'tbl_purchase_products.total_quantity as quantity_error',
            '0 as total_quantity_error',
            '(tbl_purchase_products.total_quantity/tbl_productions_orders.total_quantity) * 100 as ratio',
            // // 'SUM(tbl_productions_orders_items.quantity) as production_quantity',
            // // '(SUM(tbl_productions_orders_items.quantity) - SUM(tbl_purchase_products.total_quantity)) as import_quantity',
            // // 'SUM(tbl_purchase_products.total_quantity) as error_quantity',
            // 'IF(object_item_type = "orders", tbl_order_items.price, prices) as prices',
            // // '(IF(object_item_type = "orders", tbl_order_items.price, prices) * tbl_purchase_products.total_quantity) as total_price'
            // '(IF(object_item_type = "orders", tbl_order_items.price, prices) * tbl_purchase_products.total_quantity) as total_price'

            '0 as prices',
            '0 as total_price'
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_purchase_products';
        $join = [
            'LEFT JOIN tbl_purchase_products pr_parent ON pr_parent.id = tbl_purchase_products.parent_id',
            'INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id',
            'INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id',
            'INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id',
            'INNER JOIN tbl_products ON tbl_products.id = tbl_productions_orders_items.items_id',
            'LEFT JOIN tbl_type_print ON tbl_type_print.id = tbl_products.type_print',
            'LEFT JOIN tbl_order_items ON tbl_order_items.id = tbl_productions_orders_items.production_plan_item_id',
            // 'LEFT JOIN ' . $prices . ' ON prices.product_id = tbl_productions_orders_items.items_id AND prices.product_type = "product"',
        ];

        // $groupBy = 'GROUP BY tbl_productions_orders_items.id, tbl_products.id';
        $groupBy = '';
        $where        = [
            'AND tbl_purchase_products.is_errors = 1',
        ];
        if (!empty($productions_orders_search)) {
            array_push($where, "AND tbl_productions_orders.id IN ($productions_orders_search)");
        }

        if (!empty($products)) {
            $product_id = '';
            $arr_products = explode(",", $products);
            foreach ($arr_products as $product) {
                $product_id .= explode("__", $product)[0] . ', ';
            }
            $product_id = substr($product_id, 0, -2);
            array_push($where, "AND tbl_products.id IN ($product_id)");
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            // array_push($where, "AND tbl_productions_orders.date >= '$start_date_search'");
            array_push($where, "AND tbl_purchase_products.date >= '$start_date_search'");
        }
        
        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            // array_push($where, "AND tbl_productions_orders.date <= '$end_date_search'");
            array_push($where, "AND tbl_purchase_products.date <= '$end_date_search'");
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_purchase_products.id as pr_id',
            'tbl_productions_orders_items.items_id',
            'tbl_productions_orders_details.id as pr_detail_id',
            'tbl_order_items.is_lot as is_lot',
            'tbl_purchase_products.total_quantity as qty',
            'tbl_order_items.price as price',
            'tbl_productions_orders.id as po_id',
            'tbl_productions_orders.total_quantity as total_quantity_po',
            'object_item_type',
            
        ], $groupBy, []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $footer_data = array(
            'slsx' => 0,
            'sln' => 0,
            'slv' => 0,
            'sll' => 0,
            'tt' => 0,
        );
        $stt = 0;

        $arrItemsId = !empty($rResult) ? array_column($rResult, 'items_id') : null;
        if ($arrItemsId) {
            $arrItemsId = array_unique($arrItemsId);

            $prices = '
                SELECT t1.`price` as prices, t1.`product_id`, t1.`product_type`
                FROM tblgroup_price_detail t1
                INNER JOIN
                (
                    SELECT `product_id`,`product_type`, MAX(`price`) AS max_price
                    FROM tblgroup_price_detail
                    GROUP BY `product_id`,`product_type`
                ) t2 ON t1.`product_id` = t2.`product_id` AND t1.`product_type` = t2.`product_type` AND t1.price = t2.max_price 
                WHERE t1.product_type = "product" AND t1.product_id IN ('.implode(',', $arrItemsId).')
                GROUP BY `product_id`, `product_type`
            ';
            $listPrices = $this->db->query($prices)->result_array();
            if ($listPrices) {
                $listPrices = array_reduce($listPrices, function($carry, $item) {
                    $carry[$item['product_id']] = $item;
                    return $carry;
                });
            }
        }

        //
        $po_id = !empty($rResult) ? array_column($rResult, 'po_id') : null;
        if (!empty($po_id)) {
            $this->db->select('
                tbl_purchase_product_items.item_id as item_id,
                tbl_purchase_products.po_id as po_id,
                SUM(tbl_purchase_product_items.quantity) as quantity
            ', false);
            $this->db->from('tbl_purchase_products');
            $this->db->join('tbl_purchase_product_items', 'tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id');
            $this->db->where('tbl_purchase_products.is_pass', 1);
            $this->db->where_in('tbl_purchase_products.po_id', $po_id);
            $this->db->group_by('tbl_purchase_product_items.item_id, tbl_purchase_products.po_id');
            $purchase_products_pass = $this->db->get()->result_array();
            if ($purchase_products_pass) {
                $purchase_products_pass = array_reduce($purchase_products_pass, function($carry, $item) {
                    $_index = $item['item_id'].'__'.$item['po_id'];
                    $carry[$_index] = $item;
                    return $carry;
                });
            }
        }

        foreach ($rResult as $key => $aRow) {
            $stt++;
            $row = [];

            $dtPrice = $listPrices[$aRow['items_id']] ?? null;
            $object_item_type = $aRow['object_item_type'];
            $price = $aRow['price'];

            if ($object_item_type == 'orders') {
                $aRow['prices'] = $price;
            } else if ($dtPrice) {
                $aRow['prices'] = $dtPrice['prices'];
            }

            $_index = $aRow['items_id'].'__'.$aRow['po_id'];
            $dtPurchaseProductsPass = $purchase_products_pass[$_index] ?? null;
            $quantity_pass = $dtPurchaseProductsPass['quantity'] ?? 0;
            $aRow['quantity_pass'] = $quantity_pass;

            $aRow['total_price'] = ($aRow['qty'] - $aRow['quantity_pass']) * $aRow['prices'];

            $total_quantity_success = $aRow['qty'] - $aRow['quantity_pass'];

            $aRow['total_quantity_error'] = $total_quantity_success;

            $total_quantity_po = $aRow['total_quantity_po'];
            $aRow['ratio'] = $total_quantity_success/$total_quantity_po * 100;

            if (!empty($aRow['is_lot'])) {
                $aRow['total_price'] = $aRow['prices'];
            } 

            if ($aRow['object_item_type'] != "orders" && $aRow['prices'] > 100000) {
                $aRow['total_price'] = $aRow['prices'];
            }

            $row[0] = $aRow['pp_id'];
            $row[1] = _dhau($aRow['date']);
            $row[2] = $aRow['reference_purchase'];
            $row[3] = $aRow['reference_no'];
            $row[4] = $aRow['item_code'];
            $row[5] = $aRow['item_name'];
            $row[6] = $aRow['name_print'];
            $row[7] = '<div class="text-center">' . formatNumber($aRow['quantity_manufactures']) . '</div>';
            $row[8] = '<div class="text-center">' . formatNumber($aRow['quantity_import']) . '</div>';
            $row[9] = '<div class="text-center">' . formatNumber($aRow['quantity_pass']) . '</div>';
            $row[10] = '<div class="text-center">' . formatNumber($aRow['quantity_error']) . '</div>';
            $row[11] = '<div class="text-center">' . formatNumber($aRow['total_quantity_error']) . '</div>';
            $row[12] = '<div class="text-center">' . formatNumber($aRow['ratio']) . '%</div>';
            $row[13] = '<div class="text-right">' . formatNumber($aRow['prices']) . '</div>';
            $row[14] = '<div class="text-right">' . formatNumber($aRow['total_price']) . '</div>';
            $footer_data['slsx']+= $aRow['quantity_manufactures'];
            $footer_data['sln']+= $aRow['quantity_import'];
            $footer_data['slv']+= $aRow['quantity_pass'];
            $footer_data['sll']+= $aRow['quantity_error'];
            $footer_data['tt']+= $aRow['total_price'];

            // for ($i = 0; $i < count($aColumns); $i++) {
            //     if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            //         $_data = $aRow[strafter($aColumns[$i], 'as ')];
            //     } else {
            //         // $_data = $aRow[$aColumns[$i]];
            //     }

            //     if ($aColumns[$i] == 'tbl_productions_orders.id as po_id') {
            //         $_data = $stt;
            //     }

            //     if ($aColumns[$i] == 'tbl_productions_orders.date as date') {
            //         $_data = _dhau($aRow['date']);
            //     }
            //     if ($aColumns[$i] == 'SUM(tbl_productions_orders_items.quantity) as production_quantity') {
            //         $_data = '<div class="text-right">'.number_format($aRow['production_quantity']) .'</div>';
            //     }
            //     if ($aColumns[$i] == '(SUM(tbl_productions_orders_items.quantity) - SUM(tbl_purchase_products.total_quantity)) as import_quantity') {
            //         $_data = '<div class="text-right">'.number_format($aRow['import_quantity']) .'</div>';
            //     }
            //     if ($aColumns[$i] == 'SUM(tbl_purchase_products.total_quantity) as error_quantity') {
            //         $_data = '<div class="text-right">'.number_format($aRow['error_quantity']).'</div>';
            //     }
            //     $row[] = $_data;
            // }

            $output['aaData'][] = $row;
        }
        foreach ($footer_data as $key => $total) {
            $footer_data[$key] = number_format($total);
        }

        $output['sums'] = $footer_data;
        $output['title_excel'] = [handlingTitleExcel()['title']];
        echo json_encode($output);
    }

    public function tableComplete_stage()
    {
        if (!$this->perViewProductionsOrders) {
            accessDenied($js = true);
        }

        $products = $this->input->post('products');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $productions_orders_search = $this->input->post('productions_orders_search');
        $type_stage = $this->input->post('type_stage');
        $status_search = $this->input->post('status_search'); {
            $tbPurchasesErrors = "(
                SELECT
                    tbl_purchase_products.productions_orders_details_id as productions_orders_details_id,
                    SUM(tbl_purchase_products.total_quantity) as quantity_errors
                FROM tbl_purchase_products
                WHERE tbl_purchase_products.is_errors = 1
                GROUP BY tbl_purchase_products.productions_orders_details_id
            ) tb_purchases_errors";

            $tbDateDelivery = "(
            SELECT
                tbl_productions_orders_items.productions_orders_id as productions_orders_id,
                tbl_productions_orders_items.items_id as items_id,
                MIN(tbl_order_item_shippings.date_shipping) as date_shipping
            FROM tbl_order_item_shippings
            JOIN tbl_productions_orders_items ON tbl_productions_orders_items.production_plan_item_id = tbl_order_item_shippings.order_item_id AND object_item_type = 'orders'
            GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
            ) tb_date_delivery";

            $tbDateExport = "(
            SELECT
                tbl_productions_orders_items_stages.productions_orders_items_id as productions_orders_items_id,
                tbl_productions_orders_items_stages.date_active as date_active
            FROM tbl_productions_orders_items_stages
            WHERE tbl_productions_orders_items_stages.stage_id = '" . STAGES_MATERIAL . "' AND tbl_productions_orders_items_stages.date_active IS NOT NULL
            GROUP BY tbl_productions_orders_items_stages.productions_orders_items_id
            ) tb_date_export";

            $whereMoreProductionsOrderItems = '';
            if (!empty($start_date_search) || !empty($end_date_search)) {
                $whereDate = '';
                if (!empty($start_date_search)) {
                    $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                    $whereDate .= ' AND tbl_productions_orders_items_stages.date_active >= "' . $start_date_search . '"';
                }

                if (!empty($end_date_search)) {
                    $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                    $whereDate .= ' AND tbl_productions_orders_items_stages.date_active <= "' . $end_date_search . '"';
                }

                $whereM = '';
                if (!empty($type_stage)) {
                    $type_stage_search = implode(', ', $type_stage);
                    $whereM .= ' AND tbl_productions_orders_items_stages.stage_id IN (' . $type_stage_search . ')';
                }


                $whereMoreProductionsOrderItems .= ' AND exists (
                    SELECT 
                        tbl_productions_orders_items_stages.id 
                    FROM tbl_productions_orders_items_stages
                    WHERE tbl_productions_orders_items_stages.productions_orders_items_id = tbl_productions_orders_items.id AND tbl_productions_orders_items_stages.active = 1 ' . $whereDate . ' ' . $whereM . '
                )';
            }

            if (!empty($type_stage)) {
                $type_stage_search = implode(', ', $type_stage);
                $whereMoreProductionsOrderItems .= ' AND exists (
                    SELECT 
                        tbl_productions_orders_items_stages.id 
                    FROM tbl_productions_orders_items_stages
                    WHERE tbl_productions_orders_items_stages.productions_orders_items_id = tbl_productions_orders_items.id AND tbl_productions_orders_items_stages.active = 1 AND tbl_productions_orders_items_stages.stage_id IN (' . $type_stage_search . ')
                )';
            }

            // $tbProductionsOrderItems = "(
            //     SELECT
            //         tbl_productions_orders_items.id as po_item_id,
            //         tbl_productions_orders_items.productions_orders_id as productions_orders_id,
            //         tb_date_delivery.date_shipping as date_shipping,
            //         tb_date_export.date_active as date_export,
            //         tbl_productions_plan.note as note_plan,
            //         tbl_productions_orders_items.items_id as items_id,
            //         tbl_productions_orders_items.items_name as items_name,
            //         tbl_productions_orders_items.items_code as items_code,
            //         SUM(tbl_productions_orders_items.quantity) as quantity,
            //         SUM(tbl_productions_orders_details.quantity_warehoused) as quantity_warehoused,
            //         SUM(tb_purchases_errors.quantity_errors) as quantity_errors,
            //         tbl_productions_orders_items.plan_id as plan_id
            //     FROM tbl_productions_orders_items
            //     INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
            //     INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_orders_items.plan_id
            //     LEFT JOIN $tbDateExport ON tb_date_export.productions_orders_items_id = tbl_productions_orders_items.id 
            //     LEFT JOIN $tbPurchasesErrors ON tb_purchases_errors.productions_orders_details_id = tbl_productions_orders_details.id 
            //     LEFT JOIN $tbDateDelivery ON tb_date_delivery.productions_orders_id = tbl_productions_orders_items.productions_orders_id AND tb_date_delivery.items_id = tbl_productions_orders_items.items_id
            //     WHERE tbl_productions_orders_items.id > 0 $whereMoreProductionsOrderItems
            //     GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
            // ) tb_production_order_item";

            $tbProductionsOrderItems = "(
                SELECT
                    tbl_productions_orders_items.id as po_item_id,
                    tbl_productions_orders_items.productions_orders_id as productions_orders_id,
                    GROUP_CONCAT(tbl_productions_orders_items.id) as _poi_id,
                    GROUP_CONCAT(tbl_productions_orders_details.id) as _pod_id,
                    '' as date_shipping,
                    '' as date_export,
                    tbl_productions_plan.note as note_plan,
                    tbl_productions_orders_items.items_id as items_id,
                    tbl_productions_orders_items.items_name as items_name,
                    tbl_productions_orders_items.items_code as items_code,
                    SUM(tbl_productions_orders_items.quantity) as quantity,
                    SUM(tbl_productions_orders_details.quantity_warehoused) as quantity_warehoused,
                    SUM(0) as quantity_errors,
                    tbl_productions_orders_items.plan_id as plan_id
                FROM tbl_productions_orders_items
                INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
                INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_orders_items.plan_id
                WHERE 1 $whereMoreProductionsOrderItems
                GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
            ) tb_production_order_item";

            $tbStatus = "(
                SELECT
                    tb_cs.productions_orders_items_id as productions_orders_items_id,
                    tb_cs.final_stage as final_stage,
                    tb_cs.number as number,
                    (tbl_stages.name) as stage_name,
                    (tbl_stages.id) as stage_id
                FROM (
                    SELECT
                        tbl_productions_orders_items_stages.productions_orders_items_id as productions_orders_items_id,
                        MAX(tbl_productions_orders_items_stages.id) as id,
                        MAX(tbl_productions_orders_items_stages.final_stage) as final_stage,
                        MAX(tbl_productions_orders_items_stages.number) as number
                    FROM tbl_productions_orders_items_stages
                    WHERE tbl_productions_orders_items_stages.active = 1
                    GROUP BY tbl_productions_orders_items_stages.productions_orders_items_id
                    ORDER BY tbl_productions_orders_items_stages.active DESC, MAX(tbl_productions_orders_items_stages.number) DESC
                ) tb_cs
                INNER JOIN tbl_productions_orders_items_stages ON tb_cs.id = tbl_productions_orders_items_stages.id
                INNER JOIN tbl_stages ON tbl_stages.id = tbl_productions_orders_items_stages.stage_id
            ) tb_status";
        }
        $aColumns = [
            'tbl_productions_orders.id as po_id',
            'tbl_productions_orders.date as date',
            'tbl_productions_orders.reference_no as reference_no',
            'tb_production_order_item.items_code as items_code',
            'tb_production_order_item.items_name as items_name',
            'tb_production_order_item.date_shipping as date_delivery',
            'tb_production_order_item.quantity as production_quantity',
            '6 as ignore6',
            'IF(COALESCE(tb_production_order_item.quantity,0) = (COALESCE(tb_production_order_item.quantity_warehoused,0) + COALESCE(tb_production_order_item.quantity_errors,0)),1,0) as status_new',
            'tb_status.stage_name',
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_productions_orders';
        $join = [
            // 'LEFT JOIN '.$tbProductionsOrderItems.' ON tb_production_order_item.productions_orders_id = tbl_productions_orders.id',
            'INNER JOIN ' . $tbProductionsOrderItems . ' ON tb_production_order_item.productions_orders_id = tbl_productions_orders.id',
            'LEFT JOIN ' . $tbStatus . ' ON tb_status.productions_orders_items_id = tb_production_order_item.po_item_id',
            // 'LEFT JOIN tbl_products ON tbl_products.id = tb_production_order_item.items_id',
        ];
        $groupBy = '';
        $where = [];
        if (!empty($products)) {
            $product_id = '';
            $arr_products = explode(",", $products);
            foreach ($arr_products as $product) {
                $product_id .= explode("__", $product)[0] . ', ';
            }
            $product_id = substr($product_id, 0, -2);
            array_push($where, "AND tb_production_order_item.items_id IN ($product_id)");
        }
        if (!empty($productions_orders_search)) {
            array_push($where, "AND tbl_productions_orders.id IN ($productions_orders_search)");
        }

        // if (!empty($start_date_search)) {
        //     $start_date_search = to_sql_date($start_date_search). ' 00:00:00';
        //     array_push($where, "AND tbl_productions_orders.date >= '$start_date_search'");
        // }
        // if (!empty($end_date_search)) {
        //     $end_date_search = to_sql_date($end_date_search). ' 23:59:59';
        //     array_push($where, "AND tbl_productions_orders.date <= '$end_date_search'");
        // }

        if (!empty($start_date_search) || !empty($end_date_search)) {

            // array_push($where, "AND exists (
            //     SELECT
            //     FROM tbl_productions_orders_items_stages
            //     WHERE tbl_productions_orders_items_stages.productions_orders_id 
            // )");
        }

        // if (!empty($type_stage)) {
        //     $tbStage = "(
        //         SELECT tbl_stages.id as stages_id, tbl_productions_orders_items_stages.productions_orders_items_id
        //         FROM tbl_productions_orders_items_stages
        //         LEFT JOIN tbl_stages ON tbl_stages.id = tbl_productions_orders_items_stages.stage_id
        //         LEFT JOIN tbl_category_stages ON tbl_category_stages.id = tbl_stages.category_stages
        //         LEFT JOIN tblstaff ON tblstaff.staffid = tbl_productions_orders_items_stages.staff_active
        //     ) tbStage";

        //     $join[] = 'LEFT JOIN '.$tbStage.' ON tbStage.productions_orders_items_id = tb_production_order_item.po_item_id';

        //     $type_stage_search = implode(', ', $type_stage);
        //     array_push($where, "AND tbStage.stages_id IN ($type_stage_search)");
        // }
        if ($status_search != '') {
            array_push($where, "AND IF(COALESCE(tb_production_order_item.quantity,0) = (COALESCE(tb_production_order_item.quantity_warehoused,0) + COALESCE(tb_production_order_item.quantity_errors,0)),1,0) = $status_search");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tb_production_order_item.items_id as items_id',
            'tb_production_order_item.plan_id as plan_id',
            'tb_status.stage_id',
            'tb_production_order_item._poi_id as _poi_id',
            'tb_production_order_item._pod_id as _pod_id',
            'tb_production_order_item.quantity_warehoused as quantity_warehoused',
        ], $groupBy, []);
        $output = $result['output'];
        $rResult = $result['rResult'];

        $group_id = 0;
        $stt = 0;
        foreach ($rResult as $key => $aRow) {
            $row = [];
            $stt++;

            $productions_orders_id = $aRow['po_id'];
            $items_id = $aRow['items_id'];
            $plan_id = $aRow['plan_id'];

            $_poi_id = $aRow['_poi_id'];
            $_pod_id = $aRow['_pod_id'];

            $tbPurchasesErrors = "(
                SELECT
                    tbl_purchase_products.productions_orders_details_id as productions_orders_details_id,
                    SUM(tbl_purchase_products.total_quantity) as quantity_errors
                FROM tbl_purchase_products
                WHERE tbl_purchase_products.is_errors = 1 AND tbl_purchase_products.productions_orders_details_id IN ($_pod_id)
            )";
            $_query = $this->db->query($tbPurchasesErrors)->row_array();
            $aRow['quantity_errors'] = !empty($_query['quantity_errors']) ? $_query['quantity_errors'] : 0;

            $tbDateDelivery = "(
                SELECT
                    tbl_productions_orders_items.productions_orders_id as productions_orders_id,
                    tbl_productions_orders_items.items_id as items_id,
                    MIN(tbl_productions_plan_details.date) as date_shipping
                FROM tbl_productions_plan_items
                INNER JOIN tbl_productions_plan_details ON tbl_productions_plan_details.productions_plan_item_id = tbl_productions_plan_items.id
                JOIN tbl_productions_orders_items ON tbl_productions_orders_items.plan_item_id = tbl_productions_plan_items.id 
                WHERE tbl_productions_plan_items.is_preventive = 0 AND tbl_productions_orders_items.items_id = $items_id AND tbl_productions_orders_items.productions_orders_id = $productions_orders_id
            )";
            $_query = $this->db->query($tbDateDelivery)->row_array();
            $aRow['date_delivery'] = !empty($_query['date_shipping']) ? $_query['date_shipping'] : false;

            if ($aRow['production_quantity'] <= (float)$aRow['quantity_errors'] + (float)$aRow['quantity_warehoused']) {
                $aRow['status_new'] = 1;
            }

            $row[0] = $stt;
            $row[1] = _dhau($aRow['date']);
            $row[2] = $aRow['reference_no'];
            $row[3] = $aRow['items_code'];
            $row[4] = $aRow['items_name'];
            $row[5] = '<div class="text-left">' . (!empty($aRow['date_delivery']) ? _dhau($aRow['date_delivery']) : '') . '</div>';
            $row[6] = '<div class="text-right">' . number_format($aRow['production_quantity']) . '</div>';

            // Số lượng tờ in
            {
                $flagGroup = false;
                if ($group_id != $aRow['po_id']) {
                    $group_id = $aRow['po_id'];
                    $flagGroup = true;
                }
                $this->db->select('
                    (ppb_materials.item_type) as type, 
                    (ppb_materials.item_id), 
                    (ppb_materials.landscape_print_size), 
                    (ppb_materials.number_children_size), 
                    (ppb_materials.unit_parent_id), 
                    (ppb_materials.quantity_single),
                    SUM(ppb_materials.quantity) as quantity,
                    (ppb_materials.quantity_single) as quantity_single,
                ', false);
                $this->db->from('tbl_productions_plan_bom ppb_primary');
                $this->db->join('tbl_productions_plan_bom ppb_materials ', 'ppb_primary.id = (ppb_materials.parent_id)', 'inner');
                $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.id = ppb_primary.productions_plan_items_id', 'inner');
                $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.plan_item_id = tbl_productions_plan_items.id', 'inner');
                $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_plan_items.product_id', 'inner');
                $this->db->where('tbl_productions_orders_items.productions_orders_id', $aRow['po_id']);
                $this->db->where('ppb_primary.parent_id', 0);
                // $this->db->where('(ppb_materials.item_type)', 'materials');
                $this->db->where('(tbl_productions_orders_items.items_id)', $aRow['items_id']);

                $this->db->where('(
                    ppb_materials.item_type IN ("semi_products", "semi_products_outside")
                    OR exists (
                        SELECT
                            tbl_materials.id
                        FROM tbl_materials
                        INNER JOIN tbl_category_items ON tbl_category_items.id = tbl_materials.category_id
                        WHERE ppb_materials.item_type = "materials" AND tbl_materials.id = ppb_materials.item_id AND tbl_category_items.is_primary = 1
                    )
                )', false, false);

                $this->db->group_by('ppb_materials.item_type, ppb_materials.item_id, ppb_materials.landscape_print_size, ppb_materials.number_children_size, ppb_materials.unit_parent_id, ppb_materials.quantity_single', false);
                $bom = $this->db->get()->result_array();

                if (FIX_QUANTITY_COMPENSATION) {
                    $arrCountItems = [];
                    if (!empty($bom)) {
                        foreach ($bom as $kB => $vB) {
                            $strKey = $vB['type'].'__'.$vB['item_id'];
                            if (!empty($arrCountItems[$strKey])) {
                                $arrCountItems[$strKey]['count'] = $arrCountItems[$strKey]['count'] + 1;
                            } else {
                                $arrCountItems[$strKey]['count'] = 1;
                                $arrCountItems[$strKey]['decimal'] = 0;
                            }
                        }
                    }
                }

                // print_arrays($bom);
                $total_paper_exchange = 0;
                if (!empty($bom)) {
                    foreach ($bom as $kB => $vB) {
                        $item_id = $vB['item_id'];
                        $type = $vB['type'];
                        if ($flagGroup == true) {
                            $productionsPlanCompensation = $this->manufactures_model->productionsPlanCompensation($aRow['plan_id'], $item_id, $type);
                            $quantity_compensation = $productionsPlanCompensation['quantity_compensation'];
                        } else {
                            $quantity_compensation = 0;
                        }

                        //fix quantity compensation
                        if (FIX_QUANTITY_COMPENSATION) {
                            $strKey = $vB['type'].'__'.$vB['item_id'];
                            $count_item = $arrCountItems[$strKey]['count'];
                            $division = $quantity_compensation/$count_item;
                            if (is_decimal($division)) {
                                if ($arrCountItems[$strKey]['decimal']) {
                                    $quantity_compensation = floor($division);
                                } else {
                                    $arrCountItems[$strKey]['decimal'] = 1;
                                    $quantity_compensation = ceil($division);
                                }
                            } else {
                                $quantity_compensation = $division;
                            }
                        }
                        //

                        // $quantity = ceil($vB['quantity']);
                        $quantity = ceil(round($vB['quantity'], 4));
                        $quantity_single = $vB['quantity_single'];
                        $quantity_need = $quantity + $quantity_compensation;
                        $paper_exchange = $quantity_single > 0 ? ceil($quantity_need / $quantity_single) : 0;
                        $total_paper_exchange += $paper_exchange;
                    }
                }
                $quantityNew = $total_paper_exchange;
            }
            $row[7] = '<div class="text-right">' . number_format($quantityNew) . '</div>';

            $status_new = '';
            if ($aRow['status_new'] == 1) {
                $status_new = '<div class="label label-success">Hoàn thành</div>';
            } else {
                $status_new = '<div class="label label-info">Đang sản xuất</div>';
            }
            $row[8] = $status_new;
            // $row[7] = $aRow['status_new'];

            // Công đoạn
            $htmlProcess = '<div class="text-danger text-left italic tag-not-stage" style="width: 180px;">' . lang('Chưa thực hiện sản xuất') . '</div>';
            if (!empty($aRow['stage_name'])) {
                $htmlProcess = '
                <div style="position: relative;">
                    <span class="dot-cs"></span>
                    <div style="margin-left: 10px;">' . $aRow['stage_name'] . '</div'
                    . '</div>';
            }
            $row[9] = $htmlProcess;

            $output['aaData'][] = $row;
        }

        $output['title_excel'] = [handlingTitleExcel()['title']];
        echo json_encode($output);
    }
}
