<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Report_dashboards extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('products_model');
    }

    public function manufactures()
    {
        $data = [];
        $data['title'] = lang('V.DASHBOARD SẢN XUẤT');
        $this->load->view('admin/report_dashboards/manufactures', $data);
    }

    public function loadDataTotalAnnualProductivity($year) {
        $type_production_list = $this->products_model->getTypeProductionList();

        $date_start = $year.'-01-01 00:00:00';
        $date_end = $year.'-12-31 23:59:59';

        $arrInsert = [];
        foreach ($type_production_list as $key => $value) {
            $query = "
                SELECT
                    1 as type,
                    SUM(tbl_purchase_product_items.quantity) as quantity
                FROM tbl_purchase_products
                INNER JOIN tbl_purchase_product_items ON tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id
                WHERE tbl_purchase_products.productions_orders_details_id != 0 AND tbl_purchase_products.date >= '$date_start' AND tbl_purchase_products.date <= '$date_end' AND tbl_purchase_products.final_stage = 1 AND tbl_purchase_products.is_errors = 0 AND EXISTS (
                    SELECT 1
                    FROM tbl_productions_orders_details
                    INNER JOIN tbl_productions_orders_items_stages ON tbl_productions_orders_items_stages.productions_orders_items_id = tbl_productions_orders_details.productions_orders_item_id
                    INNER JOIN tbl_stages ON tbl_stages.id = tbl_productions_orders_items_stages.stage_id
                    INNER JOIN tbl_category_stages ON tbl_category_stages.id = tbl_stages.category_stages
                    WHERE tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id AND tbl_category_stages.type_productionlist_id = ".$value['id']."
                )
            ";
            $dtPurchaseProduct = $this->db->query($query)->row_array();
            if ($dtPurchaseProduct) {
                $arrInsert[] = [
                    'type_production_list_id' => $value['id'],
                    'year' => $year,
                    'quantity' => $dtPurchaseProduct['quantity'] ?? 0,
                    'final_stage' => 1,
                    'is_errors' => 0,
                    'is_pass' => 0,
                ];
            }

            //lỗi
            $query = "
                SELECT
                    2 as type,
                    SUM(tbl_purchase_product_items.quantity) as quantity
                FROM tbl_purchase_products
                INNER JOIN tbl_purchase_product_items ON tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id
                WHERE tbl_purchase_products.productions_orders_details_id != 0 AND tbl_purchase_products.date >= '$date_start' AND tbl_purchase_products.date <= '$date_end' AND tbl_purchase_products.is_errors = 1 AND EXISTS (
                    SELECT 1
                    FROM tbl_productions_orders_details
                    INNER JOIN tbl_productions_orders_items_stages ON tbl_productions_orders_items_stages.productions_orders_items_id = tbl_productions_orders_details.productions_orders_item_id
                    INNER JOIN tbl_stages ON tbl_stages.id = tbl_productions_orders_items_stages.stage_id
                    INNER JOIN tbl_category_stages ON tbl_category_stages.id = tbl_stages.category_stages
                    WHERE tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id AND tbl_category_stages.type_productionlist_id = ".$value['id']."
                )
            ";
            $dtPurchaseProductError = $this->db->query($query)->row_array();
            if ($dtPurchaseProductError) {
                $arrInsert[] = [
                    'type_production_list_id' => $value['id'],
                    'year' => $year,
                    'quantity' => $dtPurchaseProductError['quantity'] ?? 0,
                    'final_stage' => 0,
                    'is_errors' => 1,
                    'is_pass' => 0,
                ];
            }

            //vượt
            $query = "
                SELECT
                    3 as type,
                    SUM(tbl_purchase_product_items.quantity) as quantity
                FROM tbl_purchase_products
                INNER JOIN tbl_purchase_product_items ON tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id
                WHERE tbl_purchase_products.productions_orders_details_id != 0 AND tbl_purchase_products.date >= '$date_start' AND tbl_purchase_products.date <= '$date_end' AND tbl_purchase_products.is_pass = 1 AND EXISTS (
                    SELECT 1
                    FROM tbl_products
                    INNER JOIN tbl_product_stages ON tbl_product_stages.product_id = tbl_products.versions_stage AND tbl_product_stages.product_id = tbl_products.id
                    INNER JOIN tbl_product_stages_versions ON tbl_product_stages_versions.version_id = tbl_product_stages.id
                    INNER JOIN tbl_stages ON tbl_stages.id = tbl_product_stages_versions.stage_id
                    INNER JOIN tbl_category_stages ON tbl_category_stages.id = tbl_stages.category_stages
                    WHERE tbl_products.id = tbl_purchase_product_items.item_id AND tbl_category_stages.type_productionlist_id = ".$value['id']."
                )
            ";
            $dtPurchaseProductPass = $this->db->query($query)->row_array();
            if ($dtPurchaseProductPass) {
                $arrInsert[] = [
                    'type_production_list_id' => $value['id'],
                    'year' => $year,
                    'quantity' => $dtPurchaseProductPass['quantity'] ?? 0,
                    'final_stage' => 0,
                    'is_errors' => 0,
                    'is_pass' => 1,
                ];
            }

        }

        $this->db->where('tbl_total_purchase_product.year', $year);
        $this->db->delete('tbl_total_purchase_product');
        
        if (!empty($arrInsert)) {
            $this->db->insert_batch('tbl_total_purchase_product', $arrInsert);
        }
    }

    public function chartTotalAnnualProductivity() {
        $data = [];
        $year = $this->input->get('year');
        $type_production_list = $this->products_model->getTypeProductionList();
        $categories = [];

        $date_start = $year.'-01-01 00:00:00';
        $date_end = $year.'-12-31 23:59:59';

        usort($type_production_list, function($a, $b) {
            return $a['id'] - $b['id'];
        });

        $arrTypeId = array_column($type_production_list, 'id');
        // $queries = [];
        // foreach ($type_production_list as $key => $value) {
        //     $queries[] = "
        //         SELECT
        //             {$value['id']} AS type_productionlist_id,
        //             SUM(tbl_purchase_product_items.quantity) as quantity
        //         FROM tbl_purchase_products
        //         INNER JOIN tbl_purchase_product_items ON tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id
        //         WHERE tbl_purchase_products.productions_orders_details_id != 0 AND tbl_purchase_products.date >= '$date_start' AND tbl_purchase_products.date <= '$date_end' AND tbl_purchase_products.final_stage = 1 AND tbl_purchase_products.is_errors = 0 AND EXISTS (
        //             SELECT 1
        //             FROM tbl_productions_orders_details
        //             INNER JOIN tbl_productions_orders_items_stages ON tbl_productions_orders_items_stages.productions_orders_items_id = tbl_productions_orders_details.productions_orders_item_id
        //             INNER JOIN tbl_stages ON tbl_stages.id = tbl_productions_orders_items_stages.stage_id
        //             INNER JOIN tbl_category_stages ON tbl_category_stages.id = tbl_stages.category_stages
        //             WHERE tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id AND tbl_category_stages.type_productionlist_id = ".$value['id']."
        //         )
        //     ";

        //     print_arrays($queries);
        // }

        // $sql = implode(' UNION ALL ', $queries);
        // $results = $this->db->query($sql)->result_array();
        // print_arrays($results);

        $this->db->select('
            tbl_total_purchase_product.type_production_list_id as type_production_list_id,
            SUM(tbl_total_purchase_product.quantity) as quantity
        ', false);
        $this->db->from('tbl_total_purchase_product');
        $this->db->where('tbl_total_purchase_product.year', $year);
        $this->db->where('tbl_total_purchase_product.is_errors', 0);
        $this->db->group_by('tbl_total_purchase_product.type_production_list_id');
        $total_purchase_product = $this->db->get()->result_array();
        if ($total_purchase_product) {
            $total_purchase_product = array_reduce($total_purchase_product, function($carry, $item) {
                $carry[$item['type_production_list_id']] = $item;
                return $carry;
            });
        }

        $series = [];
        foreach ($type_production_list as $key => $value) {
            $categories[] = $value['code'];

            $dtTotalPurchaseProduct = $total_purchase_product[$value['id']] ?? null;
            $series[] = ($dtTotalPurchaseProduct['quantity'] ?? 0) * 1;
        }

        $data['categories'] = $categories;
        $data['series'] = $series;
        echo responseData($data);
    }

    public function chartRoundTotalAnnualProductivity() {
        $data = [];
        $year = $this->input->get('year');
        $type_production_list = $this->products_model->getTypeProductionList();
        $categories = [];

        $this->db->select('
            tbl_total_purchase_product.type_production_list_id as type_production_list_id,
            SUM(tbl_total_purchase_product.quantity) as quantity
        ', false);
        $this->db->from('tbl_total_purchase_product');
        $this->db->where('tbl_total_purchase_product.year', $year);
        $this->db->where('tbl_total_purchase_product.is_errors', 0);
        $this->db->group_by('tbl_total_purchase_product.type_production_list_id');
        $total_purchase_product = $this->db->get()->result_array();
        $total = 0;
        if ($total_purchase_product) {
            $total_purchase_product = array_reduce($total_purchase_product, function($carry, $item) use (&$total) {
                $carry[$item['type_production_list_id']] = $item;
                $total+= $item['quantity'];
                return $carry;
            });
        }

        $seriesData = [];
        foreach ($type_production_list as $key => $value) {
            $dtTotalPurchaseProduct = $total_purchase_product[$value['id']] ?? null;
            $quantity_current = ($dtTotalPurchaseProduct['quantity'] ?? 0) * 1;
            $y = $quantity_current/$total * 100;

            $seriesData[] = [
                'name' => $value['code'],
                'y' => round($y, 0),
            ];
        }

        $data['seriesData'] = $seriesData;
        echo responseData($data);
    }

    public function chartProductivityRate() {
        $data = [];

        $year = $this->input->get('year');
        $type_production_list = $this->products_model->getTypeProductionList();
        usort($type_production_list, function($a, $b) {
            return $a['id'] - $b['id'];
        });


        $total = 0;
        //đạt
        $this->db->select('
            tbl_total_purchase_product.type_production_list_id as type_production_list_id,
            SUM(tbl_total_purchase_product.quantity) as quantity
        ', false);
        $this->db->from('tbl_total_purchase_product');
        $this->db->where('tbl_total_purchase_product.year', $year);
        $this->db->where('tbl_total_purchase_product.is_errors', 0);
        $this->db->group_by('tbl_total_purchase_product.type_production_list_id');
        $total_purchase_product = $this->db->get()->result_array();
        if ($total_purchase_product) {
            $total_purchase_product = array_reduce($total_purchase_product, function($carry, $item) use (&$total) {
                $carry[$item['type_production_list_id']] = $item;
                $total+= $item['quantity'];
                return $carry;
            });
        }

        //lỗi
        $this->db->select('
            tbl_total_purchase_product.type_production_list_id as type_production_list_id,
            SUM(tbl_total_purchase_product.quantity) as quantity
        ', false);
        $this->db->from('tbl_total_purchase_product');
        $this->db->where('tbl_total_purchase_product.year', $year);
        $this->db->where('tbl_total_purchase_product.is_errors', 1);
        $this->db->group_by('tbl_total_purchase_product.type_production_list_id');
        $total_purchase_product_error = $this->db->get()->result_array();
        if ($total_purchase_product_error) {
            $total_purchase_product_error = array_reduce($total_purchase_product_error, function($carry, $item) use (&$total) {
                $carry[$item['type_production_list_id']] = $item;
                $total+= $item['quantity'];
                return $carry;
            });
        }

        //vượt
        $this->db->select('
            tbl_total_purchase_product.type_production_list_id as type_production_list_id,
            SUM(tbl_total_purchase_product.quantity) as quantity
        ', false);
        $this->db->from('tbl_total_purchase_product');
        $this->db->where('tbl_total_purchase_product.year', $year);
        $this->db->where('tbl_total_purchase_product.is_pass', 1);
        $this->db->group_by('tbl_total_purchase_product.type_production_list_id');
        $total_purchase_product_pass = $this->db->get()->result_array();
        if ($total_purchase_product_pass) {
            $total_purchase_product_pass = array_reduce($total_purchase_product_pass, function($carry, $item) use (&$total) {
                $carry[$item['type_production_list_id']] = $item;
                $total+= $item['quantity'];
                return $carry;
            });
        }

        $series = [
            [
                'name' => 'Vượt',
                'color' => 'red',
                'data' => null
            ],
            [
                'name' => 'Đạt',
                'color' => 'purple',
                'data' => null
            ],
            [
                'name' => 'Kém',
                'color' => 'yellow',
                'data' => null
            ]
        ];

        foreach ($type_production_list as $key => $value) {
            $categories[] = $value['code'];

            $_total_purchase_product_pass = $total_purchase_product_pass[$value['id']] ?? null;
            $quantity_pass = ($_total_purchase_product_pass['quantity'] ?? 0) * 1;

            $_total_purchase_product = $total_purchase_product[$value['id']] ?? null;
            $quantity = ($_total_purchase_product['quantity'] ?? 0) * 1;

            $_total_purchase_product_error = $total_purchase_product_error[$value['id']] ?? null;
            $quantity_error = ($_total_purchase_product_error['quantity'] ?? 0) * 1;

            $total_item = $quantity_pass + $quantity + $quantity_error;

            $_quantity_pass = 0;
            $_quantity = 0;
            $_quantity_error = 0;
            if ($total_item > 0) {
                $_quantity_pass = $quantity_pass/$total_item * 100;
                $_quantity = $quantity/$total_item * 100;
                $_quantity_error = $quantity_error/$total_item * 100;

            }

            $series[0]['data'][] = round($_quantity_pass, 0);
            $series[1]['data'][] = round($_quantity, 0);
            $series[2]['data'][] = round($_quantity_error, 0);
        }

        $data['categories'] = $categories;
        $data['series'] = $series;
        echo responseData($data);
    }

    public function quality()
    {
        $data = [];
        $data['title'] = lang('VIII.DASHBOARD CHẤT LƯỢNG');
        $this->load->view('admin/report_dashboards/quality', $data);
    }

    public function getMaxPriceProduct() {
        $query = "UPDATE tbl_products SET max_price = COALESCE((
            SELECT MAX(tblgroup_price_detail.price)
            FROM tblgroup_price_detail
            WHERE tblgroup_price_detail.product_type = 'product' AND tblgroup_price_detail.product_id = tbl_products.id
        ), 0)";

        $this->db->query($query);
    }

    public function chartTotalScrapValue() {
        $data = [];

        $year = $this->input->get('year');
        $date_start = $year.'-01-01 00:00:00';
        $date_end = $year.'-12-31 23:59:59';

        $months = [];
        $months['1'] = ('Jan');
        $months['2'] = ('Feb');
        $months['3'] = ('Mar');
        $months['4'] = ('Apr');
        $months['5'] = ('May');
        $months['6'] = ('Jun');
        $months['7'] = ('Jul');
        $months['8'] = ('Aug');
        $months['9'] = ('Sep');
        $months['10'] = ('Oct');
        $months['11'] = ('Nov');
        $months['12'] = ('Dec');

        $purchasePass = "(
            SELECT
                tbl_purchase_product_items.item_id as item_id,
                tbl_purchase_products.po_id as po_id,
                SUM(tbl_purchase_product_items.quantity) as quantity
            FROM tbl_purchase_products
            INNER JOIN tbl_purchase_product_items ON tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id
            WHERE tbl_purchase_products.is_pass = 1 AND tbl_purchase_products.po_id > 0 AND tbl_purchase_products.date >= '$date_start' AND tbl_purchase_products.date <= '$date_end'
            GROUP BY tbl_purchase_product_items.item_id, tbl_purchase_products.po_id
        ) tb_purchase_pass";

        $this->db->select('
            MONTH(tbl_purchase_products.date) AS month, 
            SUM(IF ((tbl_purchase_products.total_quantity - COALESCE(tb_purchase_pass.quantity, 0)) <= 0, 0, IF(object_item_type = "orders", IF(tbl_order_items.is_lot, tbl_order_items.price, (tbl_purchase_products.total_quantity - COALESCE(tb_purchase_pass.quantity, 0)) * tbl_order_items.price), IF (tbl_products.max_price > 100000, tbl_products.max_price, (tbl_purchase_products.total_quantity - COALESCE(tb_purchase_pass.quantity, 0)) * tbl_products.max_price))) ) as total_amount
        ', false);
        $this->db->from('tbl_purchase_products');
        $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
        $this->db->join('tbl_order_items', 'tbl_order_items.id = tbl_productions_orders_items.production_plan_item_id', 'left');
        $this->db->join($purchasePass, 'tb_purchase_pass.item_id = tbl_products.id AND tbl_productions_orders_items.productions_orders_id = tb_purchase_pass.po_id', 'left');

        $this->db->where('tbl_purchase_products.is_errors', 1);
        $this->db->where('tbl_purchase_products.date >=', $date_start);
        $this->db->where('tbl_purchase_products.date <=', $date_end);
        $this->db->group_by('month');
        $purchase_products_error = $this->db->get()->result_array();
        if (!empty($purchase_products_error)) {
            $purchase_products_error = array_reduce($purchase_products_error, function($carry, $item) {
                $carry[$item['month']] = $item;
                return $carry;
            });
        }

        $categories = [];
        $series = [];
        foreach ($months as $key => $month) {
            $categories[] = $month;
            $dtPurchaseProductsError = $purchase_products_error[$key] ?? null;
            $series[] = round(($dtPurchaseProductsError['total_amount'] ?? 0) * 1, 0);
        }

        $data['categories'] = $categories;
        $data['series'] = $series;
        echo responseData($data);
    }

    public function chartScrapRate() {
        $data = [];

        $year = $this->input->get('year');
        $date_start = $year.'-01-01 00:00:00';
        $date_end = $year.'-12-31 23:59:59';

        $purchasePass = "(
            SELECT
                tbl_purchase_product_items.item_id as item_id,
                tbl_purchase_products.po_id as po_id,
                SUM(tbl_purchase_product_items.quantity) as quantity
            FROM tbl_purchase_products
            INNER JOIN tbl_purchase_product_items ON tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id
            WHERE tbl_purchase_products.is_pass = 1 AND tbl_purchase_products.po_id > 0 AND tbl_purchase_products.date >= '$date_start' AND tbl_purchase_products.date <= '$date_end'
            GROUP BY tbl_purchase_product_items.item_id, tbl_purchase_products.po_id
        ) tb_purchase_pass";

        $this->db->select('
            tbl_category_stages.type_productionlist_id as type_productionlist_id,
            SUM(IF ((tbl_purchase_products.total_quantity - COALESCE(tb_purchase_pass.quantity, 0)) <= 0, 0, (tbl_purchase_products.total_quantity - COALESCE(tb_purchase_pass.quantity, 0)))) as total_quantity
        ', false);
        $this->db->from('tbl_purchase_products');
        $this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.id = tbl_purchase_products.pois_id');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_items_stages.productions_orders_items_id');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
        $this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_stages.category_stages');
        $this->db->join($purchasePass, 'tb_purchase_pass.item_id = tbl_productions_orders_items.items_id AND tbl_productions_orders_items.productions_orders_id = tb_purchase_pass.po_id', 'left');

        $this->db->where('tbl_purchase_products.is_errors', 1);
        $this->db->where('tbl_purchase_products.date >=', $date_start);
        $this->db->where('tbl_purchase_products.date <=', $date_end);
        $this->db->group_by('tbl_category_stages.type_productionlist_id');
        $purchase_products_error = $this->db->get()->result_array();

        $type_production_list = $this->products_model->getTypeProductionList();
        $type_production_list = array_reduce($type_production_list, function($carry, $item) {
            $carry[$item['id']] = $item;
            return $carry;
        });

        $series_data = [];
        if ($purchase_products_error) {
            $sumTotal = array_sum(array_column($purchase_products_error, 'total_quantity'));
            foreach ($purchase_products_error as $key => $value) {
                $type_productionlist_id = $value['type_productionlist_id'];
                $total_quantity = $value['total_quantity'];
                $name = $type_production_list[$type_productionlist_id] ?? null;
                $y = round(($total_quantity/$sumTotal * 1) * 100, 0);

                $series_data[] = [
                    'name' => !empty($name['code']) ? $name['code'] : 'Khác',
                    'y' => $y,
                ];
            }
        }

        $data['series_data'] = $series_data;
        echo responseData($data);
    }

    public function tabManufactures() {
        $data = [];
        $_value = $this->input->get('_value');
        $data['_value'] = $_value;
        $this->load->view('admin/report_dashboards/tab_manufactures', $data);
    }

    public function countPOByOrder() {
        $data = [];

        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        if (!empty($start_date)) $start_date = to_sql_date($start_date).' 00:00:00';
        if (!empty($end_date)) $end_date = to_sql_date($end_date). ' 23:59:59';

        $this->db->select('
            SUM(tbl_productions_plan_orders.total_quantity) as total_quantity_po,
            COUNT(distinct tbl_productions_plan_orders.productions_order_id) as count_po,
        ', false);
        $this->db->from('tbl_productions_plan_orders');
        $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_plan_orders.productions_order_id');
        $this->db->where('tbl_productions_plan_orders.object_type', 'orders');
        if ($start_date) {
            $this->db->where('tbl_productions_orders.date >=', $start_date);
        }

        if ($end_date) {
            $this->db->where('tbl_productions_orders.date <=', $end_date);
        }
        $poOrder = $this->db->get()->row_array();

        $data['total_quantity_po'] = abbreviateNumber($poOrder['total_quantity_po'] ?? 0, 2);
        $data['count_po'] = abbreviateNumber($poOrder['count_po'] ?? 0, 2);

        //preventive
        $this->db->select('
            SUM(tbl_productions_orders_items.quantity) as total_quantity_po_preventive,
            COUNT(distinct tbl_productions_orders_items.productions_orders_id) as count_po_preventive,
        ', false);
        $this->db->from('tbl_productions_orders_items');
        $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.id = tbl_productions_orders_items.plan_item_id');
        $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id');
        $this->db->where('tbl_productions_plan_items.is_preventive', 1);
        if ($start_date) {
            $this->db->where('tbl_productions_orders.date >=', $start_date);
        }

        if ($end_date) {
            $this->db->where('tbl_productions_orders.date <=', $end_date);
        }
        $preventive = $this->db->get()->row_array();

        $data['total_quantity_po_preventive'] = abbreviateNumber($preventive['total_quantity_po_preventive'] ?? 0, 2);
        $data['count_po_preventive'] = abbreviateNumber($preventive['count_po_preventive'] ?? 0, 2);

        echo responseData($data);
    }

    public function chartNumberProductionByTypeOrder() {
        $data = [];

        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        if (!empty($start_date)) $start_date = to_sql_date($start_date).' 00:00:00';
        if (!empty($end_date)) $end_date = to_sql_date($end_date). ' 23:59:59';

        $this->db->select('
            tbl_type_orders.id as id,
            tbl_type_orders.code as code,
            tbl_type_orders.name as name,
            tbl_type_orders.color as color,
        ', false);
        $this->db->from('tbl_type_orders');
        $type_orders = $this->db->get()->result_array();

        $arrTypeOrderId = array_column($type_orders, 'id');
        $this->db->select('
            tbl_orders.type_orders as type_orders,
            COUNT(distinct tbl_productions_plan_orders.productions_order_id) as count_po,
        ', false);
        $this->db->from('tbl_productions_plan_orders');
        $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_plan_orders.productions_order_id');
        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_plan_orders.productions_plan_id', 'inner');
        $this->db->where('tbl_productions_plan_orders.object_type', 'orders');
        $this->db->where_in('tbl_orders.type_orders', $arrTypeOrderId);
        if ($start_date) {
            $this->db->where('tbl_productions_orders.date >=', $start_date);
        }

        if ($end_date) {
            $this->db->where('tbl_productions_orders.date <=', $end_date);
        }
        $this->db->group_by('tbl_orders.type_orders');
        $result = $this->db->get()->result_array();
        if ($result) {
            $result = array_reduce($result, function($carry, $item) {
                $carry[$item['type_orders']] = $item;
                return $carry;
            });
        }

        $categories = [];
        $series = [];
        foreach ($type_orders as $key => $value) {
            $type_order_id = $value['id'];
            $categories[] = $value['name'];

            $dtResult = $result[$type_order_id] ?? null;
            $series[] = ($dtResult['count_po'] ?? 0) * 1;
        }

        $data['categories'] = $categories;
        $data['series'] = $series;
        echo responseData($data);
    }

    public function chartNumberGroupPO() {
        $data = [];

        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        if (!empty($start_date)) $start_date = to_sql_date($start_date).' 00:00:00';
        if (!empty($end_date)) $end_date = to_sql_date($end_date). ' 23:59:59';
        
        $series = [
            [
                'name' => 'Số lượng SX',
                'color' => 'blue',
                'data' => null
            ],
            [
                'name' => 'Số lượng nhập SX',
                'color' => 'yellow',
                'data' => null
            ]
        ];

        $this->db->select('
            tbl_category_stages.id as id,
            tbl_category_stages.name as name_category,
            SUM(tbl_productions_orders_items.quantity) as quantity
        ', false);
        $this->db->from('tbl_productions_orders_items');
        $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id');
        $this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.id = tbl_productions_orders_items.id');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
        $this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_stages.category_stages');
        if ($start_date) {
            $this->db->where('tbl_productions_orders.date >=', $start_date);
        }

        if ($end_date) {
            $this->db->where('tbl_productions_orders.date <=', $end_date);
        }
        $this->db->group_by('tbl_category_stages.id');
        $this->db->order_by('quantity DESC');
        $this->db->limit(15);
        $poItems = $this->db->get()->result_array();

        $arrCategoryId = array_column($poItems, 'id');

        if (!empty($arrCategoryId)) {
            $this->db->select('
                tbl_category_stages.id as id,
                tbl_category_stages.name as name_category,
                SUM(tbl_purchase_products.total_quantity) as quantity
            ', false);
            $this->db->from('tbl_purchase_products');
            $this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.id = tbl_purchase_products.pois_id');
            $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
            $this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_stages.category_stages');
            $this->db->where('tbl_purchase_products.pois_id >', 0);
            if ($start_date) {
                $this->db->where('tbl_purchase_products.date >=', $start_date);
            }
    
            if ($end_date) {
                $this->db->where('tbl_purchase_products.date <=', $end_date);
            }
            // $this->db->where('tbl_purchase_products.final_stage', 1);
            $this->db->where_in('tbl_category_stages.id', $arrCategoryId);
            $this->db->group_by('tbl_category_stages.id');
            $purchaseProduct = $this->db->get()->result_array();

            if ($purchaseProduct) {
                $purchaseProduct = array_reduce($purchaseProduct, function($carry, $item) {
                    $carry[$item['id']] = $item;
                    return $carry;
                });
            }
        }

        $categories = [];
        foreach ($poItems as $key => $value) {
            $category_id = $value['id'];
            $categories[] = $value['name_category'];
            $dtPurchaseProduct = $purchaseProduct[$category_id] ?? null;

            $series[0]['data'][] = round($value['quantity'], 0);
            $series[1]['data'][] = round(($dtPurchaseProduct['quantity'] ?? 0), 0);
        }

        $data['categories'] = $categories;
        $data['series'] = $series;
        echo responseData($data);
    }   

    public function chartQualityFinishedProduct() {
        $data = [];

        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        if (!empty($start_date)) $start_date = to_sql_date($start_date).' 00:00:00';
        if (!empty($end_date)) $end_date = to_sql_date($end_date). ' 23:59:59';

        $series = [
            [
                'name' => 'Tổng sản xuất',
                'color' => 'green',
                'data' => null
            ],
            [
                'name' => 'Đạt',
                'color' => 'blue',
                'data' => null
            ],
            [
                'name' => 'Lỗi',
                'color' => 'red',
                'data' => null
            ]
        ];

        $this->db->select('
            WEEK(tbl_productions_orders.date) as week_date,
            SUM(tbl_productions_orders_items.quantity) as quantity
        ', false);
        $this->db->from('tbl_productions_orders');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.productions_orders_id = tbl_productions_orders.id');
        if ($start_date) {
            $this->db->where('tbl_productions_orders.date >=', $start_date);
        }

        if ($end_date) {
            $this->db->where('tbl_productions_orders.date <=', $end_date);
        }
        $this->db->group_by('week_date');
        $po = $this->db->get()->result_array();

        if (!empty($po)) {
            $this->db->select('
                WEEK(tbl_purchase_products.date) as week_date,
                 SUM(tbl_purchase_products.total_quantity) as quantity
            ', false);
            $this->db->from('tbl_purchase_products');
            $this->db->where('((tbl_purchase_products.productions_orders_details_id > 0 AND tbl_purchase_products.final_stage = 1) OR tbl_purchase_products.productions_orders_details_id = 0)', false, false);
            $this->db->where('tbl_purchase_products.warehouseman_id !=', 0);
            if ($start_date) {
                $this->db->where('tbl_purchase_products.date >=', $start_date);
            }
    
            if ($end_date) {
                $this->db->where('tbl_purchase_products.date <=', $end_date);
            }

            $this->db->group_by('week_date');
            $purchase = $this->db->get()->result_array();
            if ($purchase) {
                $purchase = array_reduce($purchase, function($carry, $item) {
                    $carry[$item['week_date']] = $item;
                    return $carry;
                });
            }
        }

        $categories = [];
        $list = [];
        foreach ($po as $key => $value) {
            $week_date = $value['week_date'];
            $categories[] = 'Tuần '.($value['week_date'] + 1);
            $dtPurchase = $purchase[$week_date] ?? null;
            $quantityPurchase = ($dtPurchase['quantity'] ?? 0) * 1;
            $quantityError = $value['quantity'] - $quantityPurchase;
            if ($quantityError <= 0) {
                $quantityError = 0;
            }

            $_quantity = round($value['quantity'], 0);
            $_quantityPurchase = round($quantityPurchase, 0);
            $_quantityError = round($quantityError, 0);
            $rate = $_quantityError > 0 ? round($_quantityError/$_quantity * 100, 2) : 0;

            $series[0]['data'][] = $_quantity;
            $series[1]['data'][] = $_quantityPurchase;
            $series[2]['data'][] = $_quantityError;

            $list[] = [
                'name' => 'Tuần '.($value['week_date'] + 1),
                'data' => [
                    '_quantity' => $_quantity,
                    '_quantityPurchase' => $_quantityPurchase,
                    '_quantityError' => $_quantityError,
                    'rate' => $rate,
                ],
            ];
        }

        $data['categories'] = $categories;
        $data['series'] = $series;
        $data['list'] = $list;
        echo responseData($data);
    }

    public function countQC() {
        $data = [];

        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        if (!empty($start_date)) $start_date = to_sql_date($start_date).' 00:00:00';
        if (!empty($end_date)) $end_date = to_sql_date($end_date). ' 23:59:59';

        $this->db->select('
            SUM(tbl_check_quality_items.quantity_qc) as quantity_qc,
            SUM(tbl_check_quality_items.quantity_recycling) as quantity_recycling,
            SUM(tbl_check_quality_items.quantity_success) as quantity_success,
        ');
        $this->db->from('tbl_check_quality');
        $this->db->join('tbl_check_quality_items', 'tbl_check_quality_items.check_quality_id = tbl_check_quality.id');
        if ($start_date) {
            $this->db->where('tbl_check_quality.date >=', $start_date);
        }

        if ($end_date) {
            $this->db->where('tbl_check_quality.date <=', $end_date);
        }
        $checkQuantityItems = $this->db->get()->row_array();

        $data['quantity_qc'] = abbreviateNumber($checkQuantityItems['quantity_qc'] ?? 0, 2);
        $data['quantity_success'] = abbreviateNumber($checkQuantityItems['quantity_success'] ?? 0, 2);
        $data['quantity_recycling'] = abbreviateNumber($checkQuantityItems['quantity_recycling'] ?? 0, 2);

        echo responseData($data);
    }

    public function chartNumberErrorsByErrorName() {
        $data = [];

        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        if (!empty($start_date)) $start_date = to_sql_date($start_date).' 00:00:00';
        if (!empty($end_date)) $end_date = to_sql_date($end_date). ' 23:59:59';

        $this->db->select('
            tbl_check_quality_items_error.id_error as id_error,
            tbl_detail_errors.name as name_error,
            SUM(tbl_check_quality_items_error.quantity) as quantity,
        ');
        $this->db->from('tbl_check_quality');
        $this->db->join('tbl_check_quality_items', 'tbl_check_quality_items.check_quality_id = tbl_check_quality.id');
        $this->db->join('tbl_check_quality_items_error', 'tbl_check_quality_items_error.id_check_quality_item = tbl_check_quality_items.id');
        $this->db->join('tbl_detail_errors', 'tbl_detail_errors.id = tbl_check_quality_items_error.id_error');
        if ($start_date) {
            $this->db->where('tbl_check_quality.date >=', $start_date);
        }

        if ($end_date) {
            $this->db->where('tbl_check_quality.date <=', $end_date);
        }
        $this->db->order_by('quantity DESC');
        $this->db->group_by('tbl_check_quality_items_error.id_error');
        $checkQuantityItemsError = $this->db->get()->result_array();

        $series = [];
        $categories = [];
        if ($checkQuantityItemsError) {
            foreach ($checkQuantityItemsError as $key => $value) {
                $categories[] = $value['name_error'];
                $series[] = ($value['quantity'] ?? 0) * 1;
            }
        }

        $data['categories'] = $categories;
        $data['series'] = $series;
        echo responseData($data);
    }

    public function chartTopProductErrors() {
        $data = [];

        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        if (!empty($start_date)) $start_date = to_sql_date($start_date).' 00:00:00';
        if (!empty($end_date)) $end_date = to_sql_date($end_date). ' 23:59:59';

        $this->db->select('
            tbl_check_quality_items.item_id as item_id,
            tbl_products.code as product_code,
            tbl_products.name as product_name,
            SUM(tbl_check_quality_items.quantity_recycling) as quantity,
        ');
        $this->db->from('tbl_check_quality');
        $this->db->join('tbl_check_quality_items', 'tbl_check_quality_items.check_quality_id = tbl_check_quality.id');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_check_quality_items.item_id');
        if ($start_date) {
            $this->db->where('tbl_check_quality.date >=', $start_date);
        }

        if ($end_date) {
            $this->db->where('tbl_check_quality.date <=', $end_date);
        }
        $this->db->order_by('quantity DESC');
        $this->db->group_by('tbl_check_quality_items.item_id');
        $this->db->limit(10);
        $checkQuantityItemsError = $this->db->get()->result_array();

        $series = [];
        $categories = [];
        if ($checkQuantityItemsError) {
            foreach ($checkQuantityItemsError as $key => $value) {
                $categories[] = $value['product_name'];
                $series[] = ($value['quantity'] ?? 0) * 1;
            }
        }

        $data['categories'] = $categories;
        $data['series'] = $series;
        echo responseData($data);
    }

    public function chartTopClientErrors() {
        $data = [];

        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        if (!empty($start_date)) $start_date = to_sql_date($start_date).' 00:00:00';
        if (!empty($end_date)) $end_date = to_sql_date($end_date). ' 23:59:59';

        $this->db->select('
            tbl_orders.customer_id as customer_id,
            tblclients.zcode as zcode,
            SUM(tbl_check_quality_items.quantity_recycling) as quantity,
        ');
        $this->db->from('tbl_check_quality');
        $this->db->join('tbl_check_quality_items', 'tbl_check_quality_items.check_quality_id = tbl_check_quality.id');
        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_check_quality_items.order_id');
        $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id');
        $this->db->where('tbl_check_quality_items.object_type', 'orders');
        if ($start_date) {
            $this->db->where('tbl_check_quality.date >=', $start_date);
        }

        if ($end_date) {
            $this->db->where('tbl_check_quality.date <=', $end_date);
        }
        $this->db->order_by('quantity DESC');
        $this->db->group_by('tbl_orders.customer_id');
        $this->db->limit(10);
        $checkQuantityItemsError = $this->db->get()->result_array();

        $series = [];
        if ($checkQuantityItemsError) {
            $totalQuantity = array_sum(array_column($checkQuantityItemsError, 'quantity'));

            foreach ($checkQuantityItemsError as $key => $value) {
                // $y = round($value['quantity'] / $totalQuantity * 100, 2);
                $y = round($value['quantity'], 2);
                $series[] = [
                    'name' => $value['zcode'],
                    'y' => $y
                ];
            }
        }

        $data['series'] = $series;
        echo responseData($data);
    }


    //CÔNG
    //VII.DASHBOARD KỸ THUẬT
    public function dashboard_technique() {
        $data = [];
        $date_end = date('Y-m-t');
        $newdate = strtotime ( '-12 month' , strtotime ( $date_end ) ) ;
        $date_start = date( 'Y-m-01' , $newdate );
        $data['date_end'] = $date_end;
        $data['date_start'] = $date_start;
        $data['title'] = lang('VII.DASHBOARD KỸ THUẬT');
        $this->load->view('admin/report_dashboards/dashboard_technique', $data);
    }

    //Biểu đồ tổng phí bảo trì bảo dưỡng
    public function chartTechnique() {
        $data = [];
        $date_start = $this->input->get('start_date');
        $date_end = $this->input->get('end_date');
        if (!empty($date_start)) $date_start_before = $date_start = to_sql_date($date_start) . ' 00:00:00';
        if (!empty($date_end)) $date_end = to_sql_date($date_end) . ' 23:59:59';

        $typeReport = $this->input->get('type_time');
        $typeReport = !empty($typeReport) ? $typeReport : 'month';
        if ($typeReport == 'day') {
            if (!empty($date_start) && !empty($date_end)) {
                $DAY = (strtotime($date_end) - strtotime($date_start)) / (60 * 60 * 24);
                $dateFind[] = [
                    'date_start' => to_sql_date(_dC($date_start)),
                    'date_end' => to_sql_date(_dC($date_start)),
                ];
                $dateTitle[] = _d($date_start);
                for ($i = 0; $i < $DAY; $i++) {
                    $date_start = date("Y-m-d", strtotime("$date_start +1 day"));
                    $lables[] = _d($date_start);
                    $dateFind[] = [
                        'date_start' => to_sql_date(_dC($date_start)),
                        'date_end' => to_sql_date(_dC($date_start)),
                    ];
                }
            }
        }
        else if ($typeReport == 'month') {
            if (!empty($date_start) && !empty($date_end)) {
                $date_start = date("Y-m-01", strtotime("$date_start"));
                //				$date_end = date("Y-m-d", strtotime("$date_end"));
                $Dateone = new DateTime($date_start);
                $Datetwo = new DateTime($date_end);
                $year = $Datetwo->diff($Dateone);
                $MONTH = (int)(($year->y * 12) + ($year->m));
                $titleDateStart = date("m/Y", strtotime("$date_start"));
                $date_start_last = date("Y-m-01", strtotime("$date_start +1 month"));
                $date_start_last = date("Y-m-d", strtotime("$date_start_last -1 day"));
                $dateFind[] = [
                    'date_start' => date("Y-m-01", strtotime("$date_start")),
                    'date_end' => $date_start_last,
                ];
                $dataDay[] = days_in_month(date("m", strtotime("$date_start")), date("Y", strtotime("$date_start")));
                $lables[] = $titleDateStart;
                for ($i = 0; $i < $MONTH; $i++) {
                    $date_start = date("Y-m-01", strtotime("$date_start +1 month"));
                    $date_start_last = date("Y-m-01", strtotime("$date_start +1 month"));
                    $date_start_last = date("Y-m-d", strtotime("$date_start_last -1 day"));
                    $lables[] = date("m/Y", strtotime("$date_start"));
                    $dateFind[] = [
                        'date_start' => $date_start,
                        'date_end' => $date_start_last,
                    ];
                    $dataDay[] = days_in_month(date("m", strtotime("$date_start")), date("Y", strtotime("$date_start")));
                }
            }
        }
        else if ($typeReport == 'year') {
            if (!empty($date_start) && !empty($date_end)) {
                $date_start = date("Y-01-01", strtotime("$date_start"));
                $date_end = date("Y-01-01", strtotime("$date_end"));
                $YEAR = (strtotime($date_end) - strtotime($date_start)) / (60 * 60 * 24 * 30 * 365);
                $titleDateStart = date("Y", strtotime("$date_start"));
                $date_start_last = date("Y-m-01", strtotime("$date_start +1 year"));
                $date_start_last = date("Y-m-d", strtotime("$date_start_last -1 day"));
                $dateFind[] = [
                    'date_start' => $date_start,
                    'date_end' => $date_start_last
                ];
                $yearNow = date("Y", strtotime("$date_start"));
                if ($yearNow % 4 == 0) {
                    $dataDay[] = 366;
                } else {
                    $dataDay[] = 365;
                }
                $lables[] = $titleDateStart;
                for ($i = 0; $i < $YEAR; $i++) {
                    $date_start = date("Y-01-01", strtotime("$date_start +1 year"));
                    $date_start_last = date("Y-m-01", strtotime("$date_start +1 year"));
                    $date_start_last = date("Y-m-d", strtotime("$date_start_last -1 day"));
                    $dateTitle[] = date("Y", strtotime("$date_start"));
                    $dateFind[] = [
                        'date_start' => $date_start,
                        'date_end' => $date_start_last,
                    ];
                    $yearNow = date("Y", strtotime("$date_start"));
                    if ($yearNow % 4 == 0) {
                        $dataDay[] = 366;
                    } else {
                        $dataDay[] = 365;
                    }
                }
            }
        }

        $series = [
            'name' => 'Phiếu bảo trì',
            'colors' => [],
            'data' => null
        ];

        if($typeReport == 'day') {
            $arrayKey = [];
            foreach ($dateFind as $key => $value) {
                $series['data'][$key] = 0;
                $series['colors'][$key] = '#002060';
                $arrayKey[$value['date_start']] = $key;
            }

            $this->db->select('COUNT(*) num_rows, DATE_FORMAT(date, "%Y-%m-%d") as date');
            $this->db->where('date >= "' . $date_start_before . '"', false, false);
            $this->db->where('date <= "' . $date_end . '"', false, false);
            $this->db->group_by('DATE_FORMAT(date, "%Y-%m-%d")');
            $number_maintenance_tickett = $this->db->get_where('tblmaintenance_ticket')->result_array();
            foreach ($number_maintenance_tickett as $kl => $vl) {
                $series['data'][$arrayKey[$vl['date']]] = (int)$vl['num_rows'];
            }
        }
        else {
            foreach ($dateFind as $key => $value) {
                $series['colors'][$key] = '#002060';
                $this->db->where('date >= "' . $value['date_start'] . '"', false, false);
                $this->db->where('date <= "' . $value['date_end'] . '"', false, false);
                $series['data'][$key] = $this->db->get_where('tblmaintenance_ticket')->num_rows();
            }
        }



        $data['title'] = 'TỔNG PHIẾU BẢO TRÌ THIẾT BỊ MÁY MÓC';
        $data['categories'] = $lables;
        $data['series'] = $series;
        echo responseData($data);
    }

    //Biểu đồ tổng chi phí từng máy
    public function chartTechniqueDetail() {
        $data = [];

        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        if (!empty($start_date)) $start_date = to_sql_date($start_date).' 00:00:00';
        if (!empty($end_date)) $end_date = to_sql_date($end_date). ' 23:59:59';

        $list_machines = $this->db->get('tbl_machines')->result_array();




        $this->db->select('COUNT(*) as num_rows, id_machines');
        $this->db->where('date >= "'.$start_date.'"', false, false);
        $this->db->where('date <= "'.$end_date.'"', false, false);
        $this->db->group_by('tblmaintenance_ticket.id_machines');
        $maintenance_ticket = $this->db->get('tblmaintenance_ticket')->result_array();
        $array_main = [];
        foreach ($maintenance_ticket as $key => $value) {
            if(!empty($value['num_rows'])) {
                $array_main[$value['id_machines']] = $value['num_rows'];
            }
        }
        $lables = [];
        $series = [
            'name' => 'Số Phiếu Bảo Trì Từng Máy',
            'colors' => [],
            'data' => null
        ];
        foreach($list_machines as $key => $value) {
            if(!empty($array_main[$value['id']])) {
                $lables[] = $value['name'];
                $series['data'][] = (int)$array_main[$value['id']];
                $series['colors'][] = '#00b050';
            }
        }

        $data['categories'] = $lables;
        $data['series'] = $series;
        $data['title'] = 'Tổng Phiếu Bảo Trì Cho Từng Máy Theo Giai Đoạn';
        echo responseData($data);
    }

    //Biểu đồ bộ phận thay thế nhiều nhất
    public function chartTechniqueDesc() {
        $data = [];

        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        if (!empty($start_date)) $start_date = to_sql_date($start_date).' 00:00:00';
        if (!empty($end_date)) $end_date = to_sql_date($end_date). ' 23:59:59';

        $this->listTypeCategoryMachines = [
            'refrigeration' => 'Điện Lạnh',
            'electricitywater' => 'Điện Nước Gia Dụng',
            'camera' => 'Camera',
            'ctp' => 'Hệ Thống CTP',
            'wastewater' => 'Hệ Thống Nước Thải',
            'hardware' => 'Phần Cứng',
            'software' => 'Phần Mềm',
            'pccc' => 'Thiết Bị Phòng Cháy Chữa Cháy',
            'transportation' => 'Phương Tiện Vận Chuyển',
            'sever' => 'Sever',
            'laborsafety' => 'Thiết Bị An Toàn Lao Động',
            'testingequipment' => 'Thiết Bị Đo Kiểm',
            'office' => 'Thiết Bị Văn Phòng',
            'equipmentproductivity' => 'Năng Suất Thiết Bị',
            'other' => 'Thiết Bị Khác',
            'special' => 'Thiết Bị Đặc Biệt',
        ];


        $this->db->select('COUNT(*) as num_rows, tbl_category_machines.is_type');
        $this->db->where('tblmaintenance_ticket.date >= "'.$start_date.'"', false, false);
        $this->db->where('tblmaintenance_ticket.date <= "'.$end_date.'"', false, false);

        $this->db->join('tbl_machines', 'tbl_machines.id = tblmaintenance_ticket.id_machines', 'left');
        $this->db->join('tbl_category_machines', 'tbl_category_machines.id = tbl_machines.category_machine_id', 'left');
        $this->db->group_by('tbl_category_machines.is_type');
        $maintenance_ticket = $this->db->get('tblmaintenance_ticket')->result_array();
        $array_main = [];
        $allCount = 0;
        $other = 0;
        if(!empty($maintenance_ticket)) {
            foreach($maintenance_ticket as $key => $value) {
                if(!empty($value['num_rows'])) {
                    if(!empty($this->listTypeCategoryMachines[$value['is_type']])) {
                        $allCount += $value['num_rows'];
                        $array_main[$value['is_type']] = $value['num_rows'];
                    }
                    else {
                        $other += $value['num_rows'];
                        $allCount += $value['num_rows'];
                    }

                }
            }
        }
        if(!empty($other)) {
            if(!empty($array_main['other'])) {
                $array_main['other'] += $other;
            }
            else {
                $array_main['other'] = $other;
            }
        }
        $series = [
            'name' => 'Những Loại Danh Mục Thiết Bị Được Bảo Trì Nhiều Nhất',
            'color' => '#002060',
            'data' => []
        ];
        $lables = [];
        $selected = true;
        foreach($array_main as $key => $value) {
            if(!empty($this->listTypeCategoryMachines[$key])) {
                $lables[] = $this->listTypeCategoryMachines[$key];
                if(!empty($selected)) {
                    $series['data'][] = [
                        //                        $this->listTypeCategoryMachines[$key],
                        //                        (($value / $allCount) * 100),
                        'name' => $this->listTypeCategoryMachines[$key],
                        'y' => (($value / $allCount) * 100),
                        'sliced' => true,
                        'selected' => true,
                    ];
                }
                else {
                    $series['data'][] = [
                        $this->listTypeCategoryMachines[$key],
                        (($value / $allCount) * 100)
                    ];
                }
            }
        }

        $data['categories'] = $lables;
        $data['series'] = $series;
        $data['title'] = 'Những Loại Danh Mục Thiết Bị Được Bảo Trì Nhiều Nhất';
        echo responseData($data);
    }

    //XI.DASHBOARD KIỂM SOÁT NỘI BỘ
    public function dashboard_internal_control() {
        $data = [];
        $date_end = date('Y-m-t');
        $newdate = strtotime ( '-3 month' , strtotime ( $date_end ) ) ;
        $date_start = date( 'Y-m-01' , $newdate );
        $data['date_end'] = $date_end;
        $data['date_start'] = $date_start;
        $data['title'] = lang('XI.DASHBOARD KIỂM SOÁT NỘI BỘ');
        $this->load->view('admin/report_dashboards/dashboard_internal_control', $data);
    }

    //Biểu đồ tổng vi phạm phòng ban
    public function chartInternalControl() {
        $data = [];
        $date_start = $this->input->get('start_date');
        $date_end = $this->input->get('end_date');
        if (!empty($date_start)) $date_start_before = $date_start = to_sql_date($date_start) . ' 00:00:00';
        if (!empty($date_end)) $date_end = to_sql_date($date_end) . ' 23:59:59';

        $room = $this->db->get('tbl_room')->result_array();
        $typeReport = $this->input->get('type_time');
        $typeReport = !empty($typeReport) ? $typeReport : 'month';
        if ($typeReport == 'day') {
            if (!empty($date_start) && !empty($date_end)) {
                $DAY = (strtotime($date_end) - strtotime($date_start)) / (60 * 60 * 24);
                $dateFind[] = [
                    'date_start' => to_sql_date(_dC($date_start)),
                    'date_end' => to_sql_date(_dC($date_start)),
                ];
                $dateTitle[] = _d($date_start);
                for ($i = 0; $i < $DAY; $i++) {
                    $date_start = date("Y-m-d", strtotime("$date_start +1 day"));
                    $lables[] = _d($date_start);
                    $dateFind[] = [
                        'date_start' => to_sql_date(_dC($date_start)),
                        'date_end' => to_sql_date(_dC($date_start)),
                    ];
                }
            }
        }
        else if ($typeReport == 'month') {
            if (!empty($date_start) && !empty($date_end)) {
                $date_start = date("Y-m-01", strtotime("$date_start"));
                //              $date_end = date("Y-m-d", strtotime("$date_end"));
                $Dateone = new DateTime($date_start);
                $Datetwo = new DateTime($date_end);
                $year = $Datetwo->diff($Dateone);
                $MONTH = (int)(($year->y * 12) + ($year->m));
                $titleDateStart = date("m/Y", strtotime("$date_start"));
                $date_start_last = date("Y-m-01", strtotime("$date_start +1 month"));
                $date_start_last = date("Y-m-d", strtotime("$date_start_last -1 day"));
                $dateFind[] = [
                    'date_start' => date("Y-m-01", strtotime("$date_start")),
                    'date_end' => $date_start_last,
                ];
                $dataDay[] = days_in_month(date("m", strtotime("$date_start")), date("Y", strtotime("$date_start")));
                $lables[] = $titleDateStart;
                for ($i = 0; $i < $MONTH; $i++) {
                    $date_start = date("Y-m-01", strtotime("$date_start +1 month"));
                    $date_start_last = date("Y-m-01", strtotime("$date_start +1 month"));
                    $date_start_last = date("Y-m-d", strtotime("$date_start_last -1 day"));
                    $lables[] = date("m/Y", strtotime("$date_start"));
                    $dateFind[] = [
                        'date_start' => $date_start,
                        'date_end' => $date_start_last,
                    ];
                    $dataDay[] = days_in_month(date("m", strtotime("$date_start")), date("Y", strtotime("$date_start")));
                }
            }
        }
        else if ($typeReport == 'year') {
            if (!empty($date_start) && !empty($date_end)) {
                $date_start = date("Y-01-01", strtotime("$date_start"));
                $date_end = date("Y-01-01", strtotime("$date_end"));
                $YEAR = (strtotime($date_end) - strtotime($date_start)) / (60 * 60 * 24 * 30 * 365);
                $titleDateStart = date("Y", strtotime("$date_start"));
                $date_start_last = date("Y-m-01", strtotime("$date_start +1 year"));
                $date_start_last = date("Y-m-d", strtotime("$date_start_last -1 day"));
                $dateFind[] = [
                    'date_start' => $date_start,
                    'date_end' => $date_start_last
                ];
                $yearNow = date("Y", strtotime("$date_start"));
                if ($yearNow % 4 == 0) {
                    $dataDay[] = 366;
                } else {
                    $dataDay[] = 365;
                }
                $lables[] = $titleDateStart;
                for ($i = 0; $i < $YEAR; $i++) {
                    $date_start = date("Y-01-01", strtotime("$date_start +1 year"));
                    $date_start_last = date("Y-m-01", strtotime("$date_start +1 year"));
                    $date_start_last = date("Y-m-d", strtotime("$date_start_last -1 day"));
                    $dateTitle[] = date("Y", strtotime("$date_start"));
                    $dateFind[] = [
                        'date_start' => $date_start,
                        'date_end' => $date_start_last,
                    ];
                    $yearNow = date("Y", strtotime("$date_start"));
                    if ($yearNow % 4 == 0) {
                        $dataDay[] = 366;
                    } else {
                        $dataDay[] = 365;
                    }
                }
            }
        }
        $list_title = [
            1 => 'TỔNG BÁO CÁO KHÔNG PHÙ HỢP',
            2 => 'TỔNG BÁO CÁO VƯỢT',
            3 => 'TỔNG BÁO CÁO CẢI TIẾN',
        ];
        $series = [];
        foreach ($room as $k => $v) {
            $series[1][$k]['name'] = $v['name'];
            $series[2][$k]['name'] = $v['name'];
            $series[3][$k]['name'] = $v['name'];
            $series[1][$k]['data'] = [];
            $series[2][$k]['data'] = [];
            $series[3][$k]['data'] = [];
            if($typeReport == 'day') {
                $arrayKey = [];
                foreach ($dateFind as $key => $value) {
                    $series[1][$k]['data'][$key] = 0;
                    $series[2][$k]['data'][$key] = 0;
                    $series[3][$k]['data'][$key] = 0;
                    $arrayKey[$value['date_start']] = $key;
                }

                $this->db->select('COUNT(*) num_rows, type_report, DATE_FORMAT(date, "%Y-%m-%d") as date');
                $this->db->where('date >= "' . $date_start_before . '"', false, false);
                $this->db->where('date <= "' . $date_end . '"', false, false);
                $this->db->where_in('type_report', [1, 2, 3]);
                $this->db->group_by('type_report, DATE_FORMAT(date, "%Y-%m-%d")');
                $number_production_report = $this->db->get_where('tblproduction_report', ['id_departments' => $v['id']])->result_array();
                foreach ($number_production_report as $kl => $vl) {
                    $series[$vl['type_report']][$k]['data'][$arrayKey[$vl['date']]] = (int)$vl['num_rows'];
                }
            }
            else {
                foreach ($dateFind as $key => $value) {
                    $series[1][$k]['data'][$key] = 0;
                    $series[2][$k]['data'][$key] = 0;
                    $series[3][$k]['data'][$key] = 0;
                    $this->db->select('COUNT(*) num_rows, type_report');
                    $this->db->where('date >= "'. $value['date_start'].'"', false, false);
                    $this->db->where('date <= "'. $value['date_end'].'"', false, false);
                    $this->db->where_in('type_report', [1,2,3]);
                    $this->db->group_by('type_report');
                    $number_production_report = $this->db->get_where('tblproduction_report', ['id_departments' => $v['id']])->result_array();
                    foreach($number_production_report as $kl => $vl) {
                        $series[$vl['type_report']][$k]['data'][$key] = (int)$vl['num_rows'];
                    }
                }
            }

        }

        $data['categories'] = $lables;
        $data['series'] = $series;
        $data['list_title'] = $list_title;

        echo responseData($data);
    }

    //Biểu đồ BCKPH
    public function chartInternalRoom() {
        $data = [];
        $date_start = $this->input->get('start_date');
        $date_end = $this->input->get('end_date');
        if (!empty($date_start)) $date_start_before = $date_start = to_sql_date($date_start) . ' 00:00:00';
        if (!empty($date_end)) $date_end = to_sql_date($date_end) . ' 23:59:59';
        $room = $this->db->get('tbl_room')->result_array();

        $typeReport = $this->input->get('type_time');
        $typeReport = !empty($typeReport) ? $typeReport : 'month';
        if ($typeReport == 'day') {
            if (!empty($date_start) && !empty($date_end)) {
                $DAY = (strtotime($date_end) - strtotime($date_start)) / (60 * 60 * 24);
                $dateFind[] = [
                    'date_start' => to_sql_date(_dC($date_start)),
                    'date_end' => to_sql_date(_dC($date_start)),
                ];
                $dateTitle[] = _d($date_start);
                for ($i = 0; $i < $DAY; $i++) {
                    $date_start = date("Y-m-d", strtotime("$date_start +1 day"));
                    $lables[] = _d($date_start);
                    $dateFind[] = [
                        'date_start' => to_sql_date(_dC($date_start)),
                        'date_end' => to_sql_date(_dC($date_start)),
                    ];
                }
            }
        }
        else if ($typeReport == 'month') {
            if (!empty($date_start) && !empty($date_end)) {
                $date_start = date("Y-m-01", strtotime("$date_start"));
                //              $date_end = date("Y-m-d", strtotime("$date_end"));
                $Dateone = new DateTime($date_start);
                $Datetwo = new DateTime($date_end);
                $year = $Datetwo->diff($Dateone);
                $MONTH = (int)(($year->y * 12) + ($year->m));
                $titleDateStart = date("m/Y", strtotime("$date_start"));
                $date_start_last = date("Y-m-01", strtotime("$date_start +1 month"));
                $date_start_last = date("Y-m-d", strtotime("$date_start_last -1 day"));
                $dateFind[] = [
                    'date_start' => date("Y-m-01", strtotime("$date_start")),
                    'date_end' => $date_start_last,
                ];
                $dataDay[] = days_in_month(date("m", strtotime("$date_start")), date("Y", strtotime("$date_start")));
                $lables[] = $titleDateStart;
                for ($i = 0; $i < $MONTH; $i++) {
                    $date_start = date("Y-m-01", strtotime("$date_start +1 month"));
                    $date_start_last = date("Y-m-01", strtotime("$date_start +1 month"));
                    $date_start_last = date("Y-m-d", strtotime("$date_start_last -1 day"));
                    $lables[] = date("m/Y", strtotime("$date_start"));
                    $dateFind[] = [
                        'date_start' => $date_start,
                        'date_end' => $date_start_last,
                    ];
                    $dataDay[] = days_in_month(date("m", strtotime("$date_start")), date("Y", strtotime("$date_start")));
                }
            }
        }
        else if ($typeReport == 'year') {
            if (!empty($date_start) && !empty($date_end)) {
                $date_start = date("Y-01-01", strtotime("$date_start"));
                $date_end = date("Y-01-01", strtotime("$date_end"));
                $YEAR = (strtotime($date_end) - strtotime($date_start)) / (60 * 60 * 24 * 30 * 365);
                $titleDateStart = date("Y", strtotime("$date_start"));
                $date_start_last = date("Y-m-01", strtotime("$date_start +1 year"));
                $date_start_last = date("Y-m-d", strtotime("$date_start_last -1 day"));
                $dateFind[] = [
                    'date_start' => $date_start,
                    'date_end' => $date_start_last
                ];
                $yearNow = date("Y", strtotime("$date_start"));
                if ($yearNow % 4 == 0) {
                    $dataDay[] = 366;
                } else {
                    $dataDay[] = 365;
                }
                $lables[] = $titleDateStart;
                for ($i = 0; $i < $YEAR; $i++) {
                    $date_start = date("Y-01-01", strtotime("$date_start +1 year"));
                    $date_start_last = date("Y-m-01", strtotime("$date_start +1 year"));
                    $date_start_last = date("Y-m-d", strtotime("$date_start_last -1 day"));
                    $dateTitle[] = date("Y", strtotime("$date_start"));
                    $dateFind[] = [
                        'date_start' => $date_start,
                        'date_end' => $date_start_last,
                    ];
                    $yearNow = date("Y", strtotime("$date_start"));
                    if ($yearNow % 4 == 0) {
                        $dataDay[] = 366;
                    } else {
                        $dataDay[] = 365;
                    }
                }
            }
        }
        $series = [];
        foreach ($room as $k => $v) {
            $series[$k]['name'] = $v['name'];
            $series[$k]['data'] = [];
            if($typeReport == 'day') {
                $arrayKey = [];
                foreach ($dateFind as $key => $value) {
                    $series[$k]['data'][$key] = 0;
                    $arrayKey[$value['date_start']] = $key;
                }

                $this->db->select('COUNT(*) num_rows, DATE_FORMAT(date, "%Y-%m-%d") as date');
                $this->db->where('date >= "' . $date_start_before . '"', false, false);
                $this->db->where('date <= "' . $date_end . '"', false, false);
                $this->db->group_by('DATE_FORMAT(date, "%Y-%m-%d")');
                $number_production_report = $this->db->get_where('tblproduction_report', ['id_departments' => $v['id'], 'violate' => 1])->result_array();
                foreach ($number_production_report as $kl => $vl) {
                    $series[$k]['data'][$arrayKey[$vl['date']]] = (int)$vl['num_rows'];
                }
            }
            else {
                foreach ($dateFind as $key => $value) {
                    $this->db->where('date >= "' . $value['date_start'] . '"', false, false);
                    $this->db->where('date <= "' . $value['date_end'] . '"', false, false);
                    $series[$k]['data'][$key] = $this->db->get_where(
                        'tblproduction_report',
                        ['id_departments' => $v['id'], 'violate' => 1]
                    )->num_rows();
                }
            }
        }

        $data['title'] = 'TỔNG VI PHẠM PHÒNG BAN';

        $data['categories'] = $lables;
        $data['series'] = $series;

        echo responseData($data);
    }


    public function dashboard_kpi_room() {
        $data = [];
        $data['title'] = lang('XII.DASHBOARD BÁO CÁO KPIs PHÒNG BAN');
        $this->load->view('admin/report_dashboards/dashboard_kpi_room', $data);
    }

    public function chartViolate() {
        $data = [];
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        if (!empty($start_date)) $start_date = to_sql_date($start_date) . ' 00:00:00';
        if (!empty($end_date)) $end_date = to_sql_date($end_date) . ' 23:59:59';
        $lables = [];
        for ($i = 0; $i < 12; $i++) {
            $dateObj = DateTime::createFromFormat('!m', ($i + 1));
            $monthName = $dateObj->format('F'); // March
            $lables[] = $monthName;
        }

        $series = [];
        $list_type = [
            '5S-ATLD',
            'Báo Cáo',
            'Chất Lượng',
            'Quy Định Chung',
            'Qui Trình Bàn Giao',
            'Qui Trình Vận Hành',
            'Số Lượng',
            'Thời Gian',
        ];
        foreach ($list_type as $k => $v) {
            $series[$k]['name'] = $v;
            $series[$k]['data'] = [];
            foreach ($lables as $key => $value) {
                $series[$k]['data'][$key] = rand(1, 10000000);
            }
        }

        $data['title'] = 'TỔNG BCKPH';
        $data['categories'] = $lables;
        $data['series'] = $series;

        echo responseData($data);
    }

    public function chartRadioViolate()
    {
        $data = [];

        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        if (!empty($start_date)) $start_date = to_sql_date($start_date).' 00:00:00';
        if (!empty($end_date)) $end_date = to_sql_date($end_date). ' 23:59:59';

        $series = [
            'name' => 'TỶ LỆ KPIs NĂM',
            'color' => '#002060',
            'data' => []
        ];

        $series['data'] = [
            [
                'Đạt',
                18
            ],
            [
                'Vượt',
                9
            ],
            [
                'Kém',
                46
            ],
            [
                'Trung Bình',
                27
            ]
        ];
        $series['data'][0] = [
            'name' => 'Đạt',
            'y' => 18,
            'sliced' => true,
            'selected' => true,
        ];


//        $data['categories'] = $lables;
        $data['series'] = $series;
        $data['title'] = 'TỶ LỆ KPIS NĂM';
        echo responseData($data);
    }

    public function chartReportKPIRoom() {

        $data = [];
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        if (!empty($start_date)) $start_date = to_sql_date($start_date) . ' 00:00:00';
        if (!empty($end_date)) $end_date = to_sql_date($end_date) . ' 23:59:59';
        $room = $this->db->get('tbl_room')->result_array();
        $lables = [];
        foreach ($room as $k => $v) {
            $lables[] = $v['name'];
        }

        $series = [];
        $type_room = [
            'Đạt',
            'Kém',
            'Trung Bình',
        ];
        $type_color = [
            '#7e32b0',
            '#ff0000',
            '#86c157',
        ];
        foreach ($type_room as $k => $v) {
            $series[$k]['name'] = $v;
            $series[$k]['color'] = $type_color[$k];
            $series[$k]['data'] = [];
            foreach ($lables as $key => $value) {
                $series[$k]['data'][$key] = rand(1, 300);
            }
        }

        $data['title'] = 'BÁO CÁO CHỈ TIÊU KPIs PHÒNG BAN';
        $data['categories'] = $lables;
        $data['series'] = $series;

        echo responseData($data);
    }

    public function chartPointRoom() {
        $data = [];

        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        if (!empty($start_date)) $start_date = to_sql_date($start_date).' 00:00:00';
        if (!empty($end_date)) $end_date = to_sql_date($end_date). ' 23:59:59';

        $room = $this->db->get('tbl_room')->result_array();
        $lables = [];
        foreach ($room as $k => $v) {
            $lables[] = $v['code'];
        }

        $series = [
            'name' => 'KIỂM TRA KPIs BÌNH QUÂN',
            'colors' => [],
            'data' => null
        ];

        foreach($lables as $key => $value) {
            $series['data'][$key] = rand(1,120);
            $series['colors'][$key] = '#002060';
        }


        $data['title'] = 'KIỂM TRA KPIs BÌNH QUÂN';
        $data['categories'] = $lables;
        $data['series'] = $series;
        echo responseData($data);
    }
    //END CÔNG
}