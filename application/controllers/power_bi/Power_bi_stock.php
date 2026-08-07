<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Power_bi_stock extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function warehouses()
    {
        $this->db->select('tblwarehouse.id,tblwarehouse.name,tblgroup_warehouse.name as group,"" as branch');
        $this->db->join('tblgroup_warehouse', 'tblgroup_warehouse.id=tblwarehouse.id_group_warehouse', 'left');
        $warehouse = $this->db->get('tblwarehouse')->result_array();
        $this->response($warehouse, REST_Controller::HTTP_OK);
    }
    public function GetWeek()
    {
        $ddate = date('Y-m-d');
        $date = new DateTime($ddate);
        $week = $date->format("W");
        return $week;
    }
    function getStartAndEndDate($week, $year)
    {
        $dto = new DateTime();
        $dto->setISODate($year, $week);
        $ret['week_start'] = $dto->format('Y-m-d');
        $dto->modify('+6 days');
        $ret['week_end'] = $dto->format('Y-m-d');
        return $ret;
    }
    function sum($data, $date_start, $date_end, $warehouse)
    {
        $total['total_begin'] = 0;
        $total['total_nhap'] = 0;
        $total['total_xuat'] = 0;
        $total['total_left'] = 0;
        if (!empty($data)) {
            for ($i = 0; $i < count($data); $i++) {
                $row = (object)$data[$i];
                if (($row->warehouse_id == $warehouse) && ($row->date_warehouse  >= $date_start) && ($row->date_warehouse  < $date_end)) {
                    $total['total_begin']  += $row->total_begin;
                    $total['total_nhap']  += $row->total_nhap;
                    $total['total_xuat']  += $row->total_xuat;
                    $total['total_left']  += $row->total_left;
                }
            }
        }
        return $total;
    }
    function sum_begin($data, $date_start, $date_end, $warehouse)
    {
        $total['total_begin'] = 0;
        $total['total_nhap'] = 0;
        $total['total_xuat'] = 0;
        $total['total_left'] = 0;
        if (!empty($data)) {
            for ($i = 0; $i < count($data); $i++) {
                $row = (object)$data[$i];

                if (($row->warehouse_id == $warehouse) && ($row->date_warehouse  <= $date_start)) {
                    // $total += $row->total_begin;
                    $total['total_begin']  += $row->total_nhap - $row->total_xuat;
                    // $total['total_begin']  += $row->total_begin;
                    // $total['total_nhap']  += $row->total_nhap;
                    // $total['total_xuat']  += $row->total_xuat;
                    // $total['total_left']  += $row->total_left;
                }
            }
        }
        return $total;
    }
    public function get_stock_product()
    {

        $week = $this->GetWeek();
        $last_week = $week - 5;
        $last_week_year = $week - 5;

        if ($last_week < 0) {
            $last_week = 5 - abs($last_week);
        }
        $this->db->where('type_items', 'product');
        $this->db->order_by('warehouse_id,date_warehouse', 'acs');
        $warehouse_product = $this->db->get('tbl_warehouse_stock_price')->result_array();
        $this->db->order_by('id', 'acs');
        $warehouse = $this->db->get('tblwarehouse')->result_array();
        $ins = array();
        $dems = 0;

        foreach ($warehouse as $key => $value) {
            $dauki = 0;
            if ($last_week_year < 0) {
                $j = 52 - abs($last_week_year);
                for ($i = $j; $i <= 52; $i++) {
                    $dates = $this->getStartAndEndDate($i, (date('Y') - 1));
                    if ($i == $last_week) {
                        $ins[$dems]['warehouse_id'] = $value['id'];
                        $ins[$dems]['week'] = 'Tuần ' . $i;
                        $total = $this->sum_products($warehouse_product, $dates['week_start'], $dates['week_end'], $value['id']);

                        $total_dauki = $this->sum_begin_products($warehouse_product, $dates['week_start'], NULL, $value['id']);
                        $ins[$dems]['total_begin'] = $total_dauki['total_begin'];
                        $ins[$dems]['total_nhap'] = $total['total_nhap'];
                        $ins[$dems]['total_xuat'] = $total['total_xuat'];
                        $dauki += $total_dauki['total_begin'] + $total['total_nhap'] - $total['total_xuat'];
                        $ins[$dems]['total_left'] = $dauki;
                        $dems++;
                    } else {
                        $ins[$dems]['warehouse_id'] = $value['id'];
                        $ins[$dems]['week'] = 'Tuần ' . $i;
                        $total = $this->sum_products($warehouse_product, $dates['week_start'], $dates['week_end'], $value['id']);
                        // $ins[$dems]['total_begin'] = $total['total_begin'];
                        $ins[$dems]['total_begin'] = $dauki;
                        $ins[$dems]['total_nhap'] = $total['total_nhap'];
                        $ins[$dems]['total_xuat'] = $total['total_xuat'];
                        $dauki += $total['total_nhap'] - $total['total_xuat'];
                        $ins[$dems]['total_left'] = $dauki;
                        $dems++;
                    }
                }
            }
            for ($i = $last_week; $i <= $week; $i++) {
                $dates = $this->getStartAndEndDate($i, date('Y'));
                if ($i == $last_week) {
                    // $ins[$dems]['warehouse_id'] = $value['id'];
                    // $ins[$dems]['week'] = 'Tuần ' . $i;
                    // $total = $this->sum_products($warehouse_product, $dates['week_start'], $dates['week_end'], $value['id']);

                    // $total_dauki = $this->sum_begin_products($warehouse_product, $dates['week_start'], NULL, $value['id']);
                    // $ins[$dems]['total_begin'] = $total_dauki['total_begin'];
                    // $ins[$dems]['total_nhap'] = $total['total_nhap'];
                    // $ins[$dems]['total_xuat'] = $total['total_xuat'];
                    // $dauki += $total_dauki['total_begin'] + $total['total_nhap'] - $total['total_xuat'];
                    // $ins[$dems]['total_left'] = $dauki;
                    // $dems++;
                    if ($last_week_year < 0) {
                        $ins[$dems]['warehouse_id'] = $value['id'];
                        $ins[$dems]['week'] = 'Tuần ' . $i;
                        $total = $this->sum_products($warehouse_product, $dates['week_start'], $dates['week_end'], $value['id']);
                        $ins[$dems]['total_begin'] = ($dauki);
                        $ins[$dems]['total_nhap'] = ($total['total_nhap']);
                        $ins[$dems]['total_xuat'] = ($total['total_xuat']);
                        $dauki += $total['total_nhap'] - $total['total_xuat'];
                        $ins[$dems]['total_left'] = ($dauki);
                        $ins[$dems]['tuan'] = $i;
                        $dems++;
                    } else {
                        $ins[$dems]['warehouse_id'] = $value['id'];
                        $ins[$dems]['week'] = 'Tuần ' . $i;
                        $total = $this->sum_products($warehouse_product, $dates['week_start'], $dates['week_end'], $value['id']);
                        $total_dauki = $this->sum_begin_products($warehouse_product, $dates['week_start'], NULL, $value['id']);
                        $ins[$dems]['total_begin'] = ($total_dauki['total_begin']);
                        $ins[$dems]['total_nhap'] = ($total['total_nhap']);
                        $ins[$dems]['total_xuat'] = ($total['total_xuat']);
                        $dauki += ($total_dauki['total_begin'] + $total['total_nhap'] - $total['total_xuat']);
                        $ins[$dems]['total_left'] = ($dauki);
                        $ins[$dems]['tuan'] = $i;
                        $dems++;
                    }
                } else {
                    $ins[$dems]['warehouse_id'] = $value['id'];
                    $ins[$dems]['week'] = 'Tuần ' . $i;
                    $total = $this->sum_products($warehouse_product, $dates['week_start'], $dates['week_end'], $value['id']);
                    // $ins[$dems]['total_begin'] = $total['total_begin'];
                    $ins[$dems]['total_begin'] = $dauki;
                    $ins[$dems]['total_nhap'] = $total['total_nhap'];
                    $ins[$dems]['total_xuat'] = $total['total_xuat'];
                    $dauki += $total['total_nhap'] - $total['total_xuat'];
                    $ins[$dems]['total_left'] = $dauki;
                    $dems++;
                }
            }
        }
        $this->response($ins, REST_Controller::HTTP_OK);

        // foreach ($ins as $key => $value) {
        //     $html .= '<tr>
        //                 <td>' . $value['warehouse_id'] . '</td>
        //                 <td>' . $value['week'] . '</td>
        //                 <td>' . round($value['total_begin']) . '</td>
        //                 <td>' . round($value['total_nhap']) . '</td>
        //                 <td>' . round($value['total_xuat']) . '</td>
        //                 <td>' . round($value['total_left']) . '</td>
        //             </tr>';
        // }
        // $html .= '</tbody>
        //     </table>';
        // echo ($html);
        // die();
    }
    public function tests($value = '')
    {
        $dates = $this->getStartAndEndDate(47, date('Y'));
        var_dump($dates);
        die;
    }
    public function get_stock()
    {
        $week = $this->GetWeek();
        $last_week = $week - 5;
        $last_week_year = $week - 5;

        if ($last_week < 0) {
            $last_week = 5 - abs($last_week);
        }
        $this->db->where('type_items', 'nvl');
        $this->db->order_by('warehouse_id,date_warehouse', 'acs');
        $warehouse_product = $this->db->get('tbl_warehouse_stock_price')->result_array();


        $this->db->order_by('id', 'acs');
        $warehouse = $this->db->get('tblwarehouse')->result_array();
        $ins = array();
        $dems = 0;

        foreach ($warehouse as $key => $value) {
            $dauki = 0;
            if ($last_week_year < 0) {
                $j = 52 - abs($last_week_year);
                for ($i = $j; $i <= 52; $i++) {
                    $dates = $this->getStartAndEndDate($i, (date('Y') - 1));
                    if ($i == $j) {
                        $ins[$dems]['warehouse_id'] = $value['id'];
                        $ins[$dems]['week'] = 'Tuần ' . $i;
                        $total = $this->sum($warehouse_product, $dates['week_start'], $dates['week_end'], $value['id']);

                        $total_dauki = $this->sum_begin($warehouse_product, $dates['week_start'], NULL, $value['id']);
                        $ins[$dems]['total_begin'] = $total_dauki['total_begin'];
                        $ins[$dems]['total_nhap'] = $total['total_nhap'];
                        $ins[$dems]['total_xuat'] = $total['total_xuat'];
                        $dauki += $total_dauki['total_begin'] + $total['total_nhap'] - $total['total_xuat'];
                        $ins[$dems]['total_left'] = $dauki;
                        $dems++;
                    } else {
                        $ins[$dems]['warehouse_id'] = $value['id'];
                        $ins[$dems]['week'] = 'Tuần ' . $i;
                        $total = $this->sum($warehouse_product, $dates['week_start'], $dates['week_end'], $value['id']);
                        // $ins[$dems]['total_begin'] = $total['total_begin'];
                        $ins[$dems]['total_begin'] = $dauki;
                        $ins[$dems]['total_nhap'] = $total['total_nhap'];
                        $ins[$dems]['total_xuat'] = $total['total_xuat'];
                        $dauki += $total['total_nhap'] - $total['total_xuat'];
                        $ins[$dems]['total_left'] = $dauki;
                        $dems++;
                    }
                }
            }
            for ($i = $last_week; $i <= $week; $i++) {
                $dates = $this->getStartAndEndDate($i, date('Y'));
                if ($i == $last_week) {
                    if ($last_week_year < 0) {
                        $ins[$dems]['warehouse_id'] = $value['id'];
                        $ins[$dems]['week'] = 'Tuần ' . $i;
                        $total = $this->sum($warehouse_product, $dates['week_start'], $dates['week_end'], $value['id']);
                        $ins[$dems]['total_begin'] = ($dauki);
                        $ins[$dems]['total_nhap'] = ($total['total_nhap']);
                        $ins[$dems]['total_xuat'] = ($total['total_xuat']);
                        $dauki += $total['total_nhap'] - $total['total_xuat'];
                        $ins[$dems]['total_left'] = ($dauki);
                        $ins[$dems]['tuan'] = $i;
                        $dems++;
                    }else{
                        $ins[$dems]['warehouse_id'] = $value['id'];
                        $ins[$dems]['week'] = 'Tuần ' . $i;
                        $total = $this->sum($warehouse_product, $dates['week_start'], $dates['week_end'], $value['id']);

                        $total_dauki = $this->sum_begin($warehouse_product, $dates['week_start'], NULL, $value['id']);
                        $ins[$dems]['total_begin'] = ($total_dauki['total_begin']);
                        $ins[$dems]['total_nhap'] = ($total['total_nhap']);
                        $ins[$dems]['total_xuat'] = ($total['total_xuat']);
                        $dauki += ($total_dauki['total_begin'] + $total['total_nhap'] - $total['total_xuat']);
                        $ins[$dems]['total_left'] = ($dauki);
                        $ins[$dems]['tuan'] = $i;
                        $dems++;
                    }
                } else {
                    $ins[$dems]['warehouse_id'] = $value['id'];
                    $ins[$dems]['week'] = 'Tuần ' . $i;
                    $total = $this->sum($warehouse_product, $dates['week_start'], $dates['week_end'], $value['id']);
                    $ins[$dems]['total_begin'] = ($dauki);
                    $ins[$dems]['total_nhap'] = ($total['total_nhap']);
                    $ins[$dems]['total_xuat'] = ($total['total_xuat']);
                    $dauki += $total['total_nhap'] - $total['total_xuat'];
                    $ins[$dems]['total_left'] = ($dauki);
                    $ins[$dems]['tuan'] = $i;
                    $dems++;
                }
            }
        }
        $this->response($ins, REST_Controller::HTTP_OK);
    }
    public function add_stock($month = 1, $year = 2023)
    {
        $this->db->select('tblwarehouse_product.*,DATE_FORMAT(date_warehouse, "%Y-%m-%d") as date_warehouse');
        $this->db->where('type_items', 'nvl');
        $this->db->where('month(date_warehouse)', $month);
        $this->db->where('year(date_warehouse)', $year);
        $this->db->order_by('date_warehouse', 'acs');
        $this->db->group_by('product_id,DATE_FORMAT(date_warehouse, "%Y-%m-%d")');
        $warehouse_product = $this->db->get('tblwarehouse_product')->result_array();

        $this->db->where('type_items', 'nvl');
        $this->db->where('month(date_warehouse)', $month);
        $this->db->where('year(date_warehouse)', $year);
        $this->db->delete('tbl_warehouse_stock_price');

        foreach ($warehouse_product as $key => $value) {
            $ktr = get_table_where('tbl_warehouse_stock_price', array('id' => $value['id']), '', 'row');
            if (empty($ktr)) {
                // $sumFExistsQall_price = getStartInventory_v2Array($value['product_id'], $value['type_items'], $value['warehouse_id'], $value['date_warehouse']);
                $sumFExistsQall_price = 0;
                $price_trongki = getStartInventory_trongkiArray_dashbo($value['product_id'], $value['type_items'], $value['warehouse_id'], $value['date_warehouse'], $value['date_warehouse']);
                $total = $sumFExistsQall_price + $price_trongki['exists_quantity_import'] - $price_trongki['exists_quantity_export'];
                if ($total < 0) {
                    $total = 0;
                }
                $ins = array();
                $ins['id'] = $value['id'];
                $ins['warehouse_id'] = $value['warehouse_id'];
                $ins['product_id'] = $value['product_id'];
                $ins['type_items'] = $value['type_items'];
                $ins['date_warehouse'] = $value['date_warehouse'];
                $ins['total_begin'] = $sumFExistsQall_price;
                $ins['total_nhap'] = $price_trongki['exists_quantity_import'];
                $ins['total_xuat'] = $price_trongki['exists_quantity_export'];
                $ins['total_left'] = $total;
                $this->db->insert('tbl_warehouse_stock_price', $ins);
            }
        }
    }
    public function add_stock_tp()
    {
        // $group_price_detail = ' (SELECT *
        //     FROM tblgroup_price_detail
        //     GROUP BY tblgroup_price_detail.product_id
        //     ORDER BY id DESC
        //    ) group_price_detail';
        $group_price_detail = '(
            SELECT t1.*
            FROM tblgroup_price_detail t1
                INNER JOIN
                (
                    SELECT product_id, MAX(id) AS max_id
                    FROM tblgroup_price_detail
                    GROUP BY product_id
                ) t2
                ON t1.product_id = t2.product_id AND t1.id = t2.max_id
           ) group_price_detail';

        $query = "
        SELECT
        price_tam.id as id,
        price_tam.item_id as item_id,
        price_tam.type_item as type_item,
        price_tam.warehouse_id as warehouse_id,
        price_tam.quantity_stock as quantity_stock,
        price_tam.quantity_stock_export as quantity_stock_export,
        price_tam.date_warehouse as date_warehouse,
        COALESCE(group_price_detail.price,0) as price,
        (price_tam.quantity_stock * COALESCE(group_price_detail.price,0)) as total_import,
        (price_tam.quantity_stock_export * COALESCE(group_price_detail.price,0)) as total_export

        FROM
            ( 
                SELECT
                tbladjusted_items.id as id,
                tbladjusted_items.product_id as item_id,
                tbladjusted_items.type as type_item,
                tbladjusted_items.warehouse_id as warehouse_id,
                tbladjusted_items.quantity_stock as quantity_stock,
                0 as quantity_stock_export,
                DATE_FORMAT(tbladjusted.date_create, '%Y-%m-%d') as date_warehouse,
                1 as type
                FROM tbladjusted_items
                LEFT JOIN tbladjusted ON tbladjusted.id = tbladjusted_items.id_adjusted 
                WHERE tbladjusted_items.type = 'product'

                UNION ALL

                SELECT
                tbl_purchase_product_items.id as id,
                tbl_purchase_product_items.item_id as item_id,
                tbl_purchase_product_items.type_item as type_item,
                tbl_purchase_products.warehouse_id as warehouse_id,
                tbl_purchase_product_items.quantity_stock as quantity_stock,
                0 as quantity_stock_export,
                DATE_FORMAT(tbl_purchase_products.date_warehouseman, '%Y-%m-%d') as date_warehouse,
                1 as type
                FROM tbl_purchase_product_items
                LEFT JOIN tbl_purchase_products ON tbl_purchase_products.id = tbl_purchase_product_items.purchase_product_id 
                LEFT JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbl_purchase_product_items.productions_orders_details_id
                LEFT JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tbl_purchase_product_items.location_id
                WHERE ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != 'orders'  AND tbllocaltion_warehouses.stage_id = 0)  or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0   AND tbllocaltion_warehouses.stage_id = 0)) and warehouseman_id > 0 and 
                tbl_purchase_product_items.type_item = 'products'
                UNION ALL

                SELECT
                tbltransfer_warehouse_detail.id as id,
                tbltransfer_warehouse_detail.id_items as item_id,
                tbltransfer_warehouse_detail.type as type_item,
                tbltransfer_warehouse_detail.warehouses_id as warehouse_id,
                0 as quantity_stock,
                tbltransfer_warehouse_detail.quantity_net as quantity_stock_export,
                DATE_FORMAT(tbltransfer_warehouse.warehouseman_date, '%Y-%m-%d') as date_warehouse,
                2 as type
                FROM tbltransfer_warehouse_detail
                LEFT JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer 
                WHERE tbltransfer_warehouse_detail.type = 'product' and tbltransfer_warehouse.order_id > 0  and warehouseman_id > 0
            ) price_tam
            LEFT JOIN $group_price_detail ON group_price_detail.product_id = price_tam.item_id
            ORDER BY price_tam.date_warehouse asc        

        ";
        $warehouse_product = $this->db->query($query)->result_array();
        $week = $this->GetWeek();
        $last_week = $week - 5;
        $last_week_year = $week - 5;

        if ($last_week < 0) {
            $last_week = 5 - abs($last_week);
        }
        $this->db->order_by('id', 'acs');
        $warehouse = $this->db->get('tblwarehouse')->result_array();
        $ins = array();
        $dems = 0;

        foreach ($warehouse as $key => $value) {
            $dauki = 0;
            if ($last_week_year < 0) {
                $j = 52 - abs($last_week_year);
                for ($i = $j; $i <= 52; $i++) {
                    $dates = $this->getStartAndEndDate($i, (date('Y') - 1));
                    if ($i == $j) {
                        $ins[$dems]['warehouse_id'] = $value['id'];
                        $ins[$dems]['week'] = 'Tuần ' . $i;
                        $total = $this->sum_products($warehouse_product, $dates['week_start'], $dates['week_end'], $value['id']);
                        $total_dauki = $this->sum_begin_products($warehouse_product, $dates['week_start'], NULL, $value['id']);
                        $ins[$dems]['total_begin'] = ($total_dauki['total_begin']);
                        $ins[$dems]['total_nhap'] = ($total['total_nhap']);
                        $ins[$dems]['total_xuat'] = ($total['total_xuat']);
                        $dauki += ($total_dauki['total_begin'] + $total['total_nhap'] - $total['total_xuat']);
                        $ins[$dems]['total_left'] = ($dauki);
                        $ins[$dems]['tuan'] = $i;
                        $dems++;
                    } else {
                        $ins[$dems]['warehouse_id'] = $value['id'];
                        $ins[$dems]['week'] = 'Tuần ' . $i;
                        $total = $this->sum_products($warehouse_product, $dates['week_start'], $dates['week_end'], $value['id']);
                        $ins[$dems]['total_begin'] = ($dauki);
                        $ins[$dems]['total_nhap'] = ($total['total_nhap']);
                        $ins[$dems]['total_xuat'] = ($total['total_xuat']);
                        $dauki += $total['total_nhap'] - $total['total_xuat'];
                        $ins[$dems]['total_left'] = ($dauki);
                        $ins[$dems]['tuan'] = $i;
                        $dems++;
                    }
                }
            }
            for ($i = $last_week; $i <= $week; $i++) {
                $dates = $this->getStartAndEndDate($i, date('Y'));
                if ($i == $last_week) {
                    if ($last_week_year < 0) {
                        $ins[$dems]['warehouse_id'] = $value['id'];
                        $ins[$dems]['week'] = 'Tuần ' . $i;
                        $total = $this->sum_products($warehouse_product, $dates['week_start'], $dates['week_end'], $value['id']);
                        $ins[$dems]['total_begin'] = ($dauki);
                        $ins[$dems]['total_nhap'] = ($total['total_nhap']);
                        $ins[$dems]['total_xuat'] = ($total['total_xuat']);
                        $dauki += $total['total_nhap'] - $total['total_xuat'];
                        $ins[$dems]['total_left'] = ($dauki);
                        $ins[$dems]['tuan'] = $i;
                        $dems++;
                    } else {
                        $ins[$dems]['warehouse_id'] = $value['id'];
                        $ins[$dems]['week'] = 'Tuần ' . $i;
                        $total = $this->sum_products($warehouse_product, $dates['week_start'], $dates['week_end'], $value['id']);
                        $total_dauki = $this->sum_begin_products($warehouse_product, $dates['week_start'], NULL, $value['id']);
                        $ins[$dems]['total_begin'] = ($total_dauki['total_begin']);
                        $ins[$dems]['total_nhap'] = ($total['total_nhap']);
                        $ins[$dems]['total_xuat'] = ($total['total_xuat']);
                        $dauki += ($total_dauki['total_begin'] + $total['total_nhap'] - $total['total_xuat']);
                        $ins[$dems]['total_left'] = ($dauki);
                        $ins[$dems]['tuan'] = $i;
                        $dems++;
                    }
                } else {
                    $ins[$dems]['warehouse_id'] = $value['id'];
                    $ins[$dems]['week'] = 'Tuần ' . $i;
                    $total = $this->sum_products($warehouse_product, $dates['week_start'], $dates['week_end'], $value['id']);
                    $ins[$dems]['total_begin'] = ($dauki);
                    $ins[$dems]['total_nhap'] = ($total['total_nhap']);
                    $ins[$dems]['total_xuat'] = ($total['total_xuat']);
                    $dauki += $total['total_nhap'] - $total['total_xuat'];
                    $ins[$dems]['total_left'] = ($dauki);
                    $ins[$dems]['tuan'] = $i;
                    $dems++;
                }
            }
        }
        $this->response($ins, REST_Controller::HTTP_OK);
    }
    function sum_products($data, $date_start, $date_end, $warehouse)
    {
        $total['total_nhap'] = 0;
        $total['total_xuat'] = 0;
        if (!empty($data)) {
            for ($i = 0; $i < count($data); $i++) {
                $row = (object)$data[$i];
                if (($row->warehouse_id == $warehouse) && ($row->date_warehouse  >= $date_start) && ($row->date_warehouse  < $date_end)) {
                    $total['total_nhap']  += $row->total_import;
                    $total['total_xuat']  += $row->total_export;
                }
            }
        }
        return $total;
    }
    function sum_begin_products($data, $date_start, $date_end, $warehouse)
    {
        $total['total_begin'] = 0;
        if (!empty($data)) {
            for ($i = 0; $i < count($data); $i++) {
                $row = (object)$data[$i];

                if (($row->warehouse_id == $warehouse) && ($row->date_warehouse  <= $date_start)) {
                    // $total += $row->total_begin;
                    $total['total_begin']  += $row->total_import - $row->total_export;
                }
            }
        }
        return $total;
    }
    // public function add_stock_tp($month = 1, $year = 2023)
    // {
    //     $this->db->select('tblwarehouse_product.*,DATE_FORMAT(date_warehouse, "%Y-%m-%d") as date_warehouse');
    //     $this->db->where('type_items', 'product');
    //     $this->db->where('month(date_warehouse)', $month);
    //     $this->db->where('year(date_warehouse)', $year);
    //     $this->db->order_by('date_warehouse', 'acs');
    //     $this->db->group_by('product_id,DATE_FORMAT(date_warehouse, "%Y-%m-%d")');
    //     $warehouse_product = $this->db->get('tblwarehouse_product')->result_array();

    //     $this->db->where('month(date_warehouse)', $month);
    //     $this->db->where('year(date_warehouse)', $year);
    //     $this->db->where('type_items', 'product');
    //     $this->db->delete('tbl_warehouse_stock_price');
    //     foreach ($warehouse_product as $key => $value) {
    //         $sumFExistsQall_price = getStartInventory_v2Array($value['product_id'], $value['type_items'], $value['warehouse_id'], $value['date_warehouse']);
    //         $price_trongki = getStartInventory_trongkiArray($value['product_id'], $value['type_items'], $value['warehouse_id'], $value['date_warehouse'], $value['date_warehouse']);
    //         $total = $sumFExistsQall_price + $price_trongki['exists_quantity_import'] - $price_trongki['exists_quantity_export'];
    //         if ($total < 0) {
    //             $total = 0;
    //         }
    //         $ins = array();
    //         $ins['id'] = $value['id'];
    //         $ins['warehouse_id'] = $value['warehouse_id'];
    //         $ins['product_id'] = $value['product_id'];
    //         $ins['type_items'] = $value['type_items'];
    //         $ins['date_warehouse'] = $value['date_warehouse'];
    //         $ins['total_begin'] = $sumFExistsQall_price;
    //         $ins['total_nhap'] = $price_trongki['exists_quantity_import'];
    //         $ins['total_xuat'] = $price_trongki['exists_quantity_export'];
    //         $ins['total_left'] = $total;

    //         $this->db->insert('tbl_warehouse_stock_price', $ins);
    //     }
    // }
    public function stock_nvl()
    {
        $this->db->order_by('date_warehouse', 'acs');
        $warehouse_product = $this->db->get('tbl_warehouse_stock_price')->result_array();
        $this->response($warehouse_product, REST_Controller::HTTP_OK);
    }
    public function index_get()
    {
    }
    public function GetTimeNvl()
    {
        $this->db->select('tblwarehouse_items.*,ROUND(product_quantity, 0) as product_quantity,DATEDIFF(date_sd, "' . date('Y-m-d') . '") as days,IF(DATEDIFF(date_sd, "' . date('Y-m-d') . '") < 0,CONCAT("Trễ hạn"),IF((DATEDIFF(date_sd, "' . date('Y-m-d') . '") >= 0 and DATEDIFF(date_sd, "' . date('Y-m-d') . '") < 30),CONCAT("30 Ngày"),IF((DATEDIFF(date_sd, "' . date('Y-m-d') . '") >= 30 and DATEDIFF(date_sd, "' . date('Y-m-d') . '") < 60),CONCAT("60 Ngày"),IF((DATEDIFF(date_sd, "' . date('Y-m-d') . '") >= 60 and DATEDIFF(date_sd, "' . date('Y-m-d') . '") < 90),CONCAT("90 Ngày"),IF((DATEDIFF(date_sd, "' . date('Y-m-d') . '") >= 90),CONCAT("> 90 Ngày"),""))))) as type_day');
        $this->db->where('type_items', 'nvl');
        $this->db->where('date_sd !=', NULL);
        $this->db->where('product_quantity >', 0);
        $warehouse_items = $this->db->get('tblwarehouse_items')->result_array();
        $this->response($warehouse_items, REST_Controller::HTTP_OK);
    }
    public function GetTimeProductsNew()
    {
        $this->db->select('tblwarehouse_items.*,ROUND(product_quantity, 0) as product_quantity,DATEDIFF(date_sd, "' . date('Y-m-d') . '") AS days,IF(DATEDIFF(date_sd, "' . date('Y-m-d') . '") < 0,CONCAT("Trễ hạn"),IF((DATEDIFF(date_sd, "' . date('Y-m-d') . '") >= 0 and DATEDIFF(date_sd, "' . date('Y-m-d') . '") < 30),CONCAT("30 Ngày"),IF((DATEDIFF(date_sd, "' . date('Y-m-d') . '") >= 30 and DATEDIFF(date_sd, "' . date('Y-m-d') . '") < 60),CONCAT("60 Ngày"),IF((DATEDIFF(date_sd, "' . date('Y-m-d') . '") >= 60 and DATEDIFF(date_sd, "' . date('Y-m-d') . '") < 90),CONCAT("90 Ngày"),IF((DATEDIFF(date_sd, "' . date('Y-m-d') . '") >= 90),CONCAT("> 90 Ngày"),""))))) as type_day');
        $this->db->where('type_items', 'product');
        $this->db->where('date_sd !=', NULL);
        $this->db->where('product_quantity >', 0);
        $this->db->where('tbl_products.type_products', 'products');
        $this->db->join('tbl_products', 'tbl_products.id=tblwarehouse_items.id_items', 'left');
        $warehouse_items = $this->db->get('tblwarehouse_items')->result();
        $this->response($warehouse_items, REST_Controller::HTTP_OK);
    }
    public function GetTimeBTPProducts()
    {
        $this->db->select('tblwarehouse_items.*,ROUND(product_quantity, 0) as product_quantity,DATEDIFF(date_sd, "' . date('Y-m-d') . '") AS days,IF(DATEDIFF(date_sd, "' . date('Y-m-d') . '") < 0,CONCAT("Trễ hạn"),IF((DATEDIFF(date_sd, "' . date('Y-m-d') . '") >= 0 and DATEDIFF(date_sd, "' . date('Y-m-d') . '") < 30),CONCAT("30 Ngày"),IF((DATEDIFF(date_sd, "' . date('Y-m-d') . '") >= 30 and DATEDIFF(date_sd, "' . date('Y-m-d') . '") < 60),CONCAT("60 Ngày"),IF((DATEDIFF(date_sd, "' . date('Y-m-d') . '") >= 60 and DATEDIFF(date_sd, "' . date('Y-m-d') . '") < 90),CONCAT("90 Ngày"),IF((DATEDIFF(date_sd, "' . date('Y-m-d') . '") >= 90),CONCAT("> 90 Ngày"),""))))) as type_day');
        $this->db->where('type_items', 'product');
        $this->db->where('date_sd !=', NULL);
        $this->db->where('product_quantity >', 0);
        $this->db->where('tbl_products.type_products !=', 'products');
        $this->db->join('tbl_products', 'tbl_products.id=tblwarehouse_items.id_items', 'left');
        $warehouse_items = $this->db->get('tblwarehouse_items')->result_array();
        $this->response($warehouse_items, REST_Controller::HTTP_OK);
    }
    public function GetTimeStock()
    {
        $localtion_warehouses = ' (SELECT tbl_productions_orders_items.object_item_type,tbl_order_items.price as price_order,tbl_order_items.item_name,tbl_order_items.order_id,tbllocaltion_warehouses.id as localtion_warehousesid
            FROM tbllocaltion_warehouses
            INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id
            INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id AND tbl_productions_orders_items.object_item_type = "orders"
            INNER JOIN tbl_order_items ON tbl_order_items.id = tbl_productions_orders_items.production_plan_item_id) localtion_warehouses';
        $query = "
            SELECT
            tblwarehouse_product.id as id,
            tblwarehouse_product.product_id as id_items,
            IF(tbl_products.type_products != '',IF(type_products = 'products','product','btpproduct'),'nvl') as type_items,
            CONCAT(tblwarehouse_product.type_items,'_',tblwarehouse_product.product_id) as map_items,
            ROUND(tblwarehouse_product.quantity_left, 0) as product_quantity,
            tblwarehouse_product.lot_code as lot_code,
            tblwarehouse_product.date_sd as date_sd,
            tblwarehouse_product.date_sx as date_sx,
            tblwarehouse_product.date_use as date_use,
            tblwarehouse_product.date_warehouse as date_warehouse,
            tblwarehouse_product.warehouse_id as warehouse_id,
            IF(tbl_products.type_products != '',tbl_products.type_products,'nvl') as type_products,
            DATEDIFF(date_sd, '" . date('Y-m-d') . "') AS days,
            IF(DATEDIFF(date_sd, '" . date('Y-m-d') . "') < 0,CONCAT('Trễ hạn'),IF((DATEDIFF(date_sd, '" . date('Y-m-d') . "') >= 0 and DATEDIFF(date_sd, '" . date('Y-m-d') . "') < 30),CONCAT('30 Ngày'),IF((DATEDIFF(date_sd, '" . date('Y-m-d') . "') >= 30 and DATEDIFF(date_sd, '" . date('Y-m-d') . "') < 60),CONCAT('60 Ngày'),IF((DATEDIFF(date_sd, '" . date('Y-m-d') . "') >= 60 and DATEDIFF(date_sd, '" . date('Y-m-d') . "') < 90),CONCAT('90 Ngày'),IF((DATEDIFF(date_sd, '" . date('Y-m-d') . "') >= 90),CONCAT('> 90 Ngày'),''))))) as type_day
            FROM tblwarehouse_product
            LEFT JOIN tbl_products ON tbl_products.id = tblwarehouse_product.product_id AND tblwarehouse_product.type_items = 'product'
            WHERE tblwarehouse_product.quantity_left > 0.0001 AND date_sd is not NULL AND tblwarehouse_product.type_items = 'nvl'

            UNION ALL

            SELECT
            tblwarehouse_product.id as id,
            tblwarehouse_product.product_id as id_items,
            IF(tbl_products.type_products != '',IF(type_products = 'products','product','btpproduct'),'nvl') as type_items,
            CONCAT(tblwarehouse_product.type_items,'_',tblwarehouse_product.product_id) as map_items,
            ROUND(tblwarehouse_product.quantity_left, 0) as product_quantity,
            tblwarehouse_product.lot_code as lot_code,
            tblwarehouse_product.date_sd as date_sd,
            tblwarehouse_product.date_sx as date_sx,
            tblwarehouse_product.date_use as date_use,
            tblwarehouse_product.date_warehouse as date_warehouse,
            tblwarehouse_product.warehouse_id as warehouse_id,
            IF(tbl_products.type_products != '',tbl_products.type_products,'nvl') as type_products,
            DATEDIFF(date_sd, '" . date('Y-m-d') . "') AS days,
            IF(DATEDIFF(date_sd, '" . date('Y-m-d') . "') < 0,CONCAT('Trễ hạn'),IF((DATEDIFF(date_sd, '" . date('Y-m-d') . "') >= 0 and DATEDIFF(date_sd, '" . date('Y-m-d') . "') < 30),CONCAT('30 Ngày'),IF((DATEDIFF(date_sd, '" . date('Y-m-d') . "') >= 30 and DATEDIFF(date_sd, '" . date('Y-m-d') . "') < 60),CONCAT('60 Ngày'),IF((DATEDIFF(date_sd, '" . date('Y-m-d') . "') >= 60 and DATEDIFF(date_sd, '" . date('Y-m-d') . "') < 90),CONCAT('90 Ngày'),IF((DATEDIFF(date_sd, '" . date('Y-m-d') . "') >= 90),CONCAT('> 90 Ngày'),''))))) as type_day
            FROM tblwarehouse_product
            LEFT JOIN tbl_products ON tbl_products.id = tblwarehouse_product.product_id AND tblwarehouse_product.type_items = 'product'
            LEFT JOIN $localtion_warehouses ON localtion_warehouses.localtion_warehousesid = tblwarehouse_product.localtion
            LEFT JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_product.localtion
            LEFT JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id
            WHERE tblwarehouse_product.quantity_left > 0.0001 AND date_sd is not NULL AND tblwarehouse_product.type_items = 'product' AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != 'orders'  AND tbllocaltion_warehouses.stage_id = 0)  or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0   AND tbllocaltion_warehouses.stage_id = 0))
        ";
        $result = $this->db->query($query)->result_array();

        $this->response($result, REST_Controller::HTTP_OK);
    }
    public function GetPurchaseOrder()
    {
        $query = "
            SELECT
            tblpurchase_order_items.id as id,
            tblpurchase_order_items.product_id as id_items,
            tblpurchase_order_items.type as type_items,
            CONCAT(tblpurchase_order_items.type,'_',tblpurchase_order_items.product_id) as map_items,
            ROUND(tblpurchase_order_items.quantity_stock, 0) as quantity_stock,
            ROUND(tblpurchase_order_items.total_suppliers, 0) as total_suppliers
            FROM tblpurchase_order_items
        ";
        $result = $this->db->query($query)->result_array();
        $this->response($result, REST_Controller::HTTP_OK);
    }
    public function GetProducts()
    {
        $query = "
            SELECT
                tbl_products.id as id,
                tbl_products.category_id as category_id,
                tbl_products.type_products as type_products,
                IF(type_products = 'products','product','btpproduct') as type_items,
                IF(type_products = 'products','THÀNH PHẨM','BÁN THÀNH PHẨM') as name_type,
                CONCAT('product_',tbl_products.id) as map_items,
                tbl_products.code as code,
                tbl_products.name as name,
                tbl_products.unit_id as unit_id
            FROM tbl_products

            UNION ALL

            SELECT
                tbl_materials.id as id,
                tbl_materials.category_id as category_id,
                'nvl' as type_products,
                'nvl' as type_items,
                'NGUYÊN VẬT LIỆU' as name_type,
                CONCAT('nvl_',tbl_materials.id) as map_items,
                tbl_materials.code as code,
                tbl_materials.name as name,
                tbl_materials.unit_id as unit_id
            FROM tbl_materials
        ";
        $result = $this->db->query($query)->result_array();
        $this->response($result, REST_Controller::HTTP_OK);
    }
    public function GetCategory()
    {
        $query = "
            SELECT
                tbl_category_items.id as id,
                tbl_category_items.code as code,
                tbl_category_items.name as name,
                CONCAT('nvl_',tbl_category_items.id) as map_category
            FROM tbl_category_items

            UNION ALL

            SELECT
                tbl_category_products.id as id,
                tbl_category_products.code as code,
                tbl_category_products.name as name,
                CONCAT(IF(type_products = 'products','product_','btpproduct_'),tbl_category_products.id) as map_category
            FROM tbl_category_products
            LEFT JOIN tbl_products ON tbl_products.category_id = tbl_category_products.id
            GROUP BY tbl_category_products.id
        ";
        $result = $this->db->query($query)->result_array();
        $this->response($result, REST_Controller::HTTP_OK);
    }
    public function GetSuggestExporting()
    {
        $query = "
            SELECT
                tbl_suggest_exporting_items.item_id,
                tbl_suggest_exporting_items.type_item,
                ROUND(tbl_suggest_exporting_items.quantity_warehouse, 0) as quantity_warehouse,
                CONCAT('nvl_',tbl_suggest_exporting_items.item_id) as map_items
            FROM tbl_suggest_exporting_items
            WHERE tbl_suggest_exporting_items.type_item = 'materials'
        ";
        $result = $this->db->query($query)->result_array();
        $this->response($result, REST_Controller::HTTP_OK);
    }

    public function ListStock()
    {
        $query = "
            (
                SELECT
                    tblimport.warehouseman_date as warehouseman_date,
                    tblimport.date as date,
                    concat(tblimport.prefix,'-',tblimport.code) as code,
                    tblimport.note as reason,
                    tblimport_items.quantity_net as import_quantity,
                    0 as export_quantity,
                    tblimport.id as id_main,
                    tblimport_items.product_id as product_id,
                    tblimport_items.type as type,
                    tblimport_items.localtion_warehouses_id as localtion_id,
                    '' as id_import,
                    tblimport.warehouse_id as warehouse_id,
                    quantity_payment,
                    price
                FROM tblimport_items
                LEFT JOIN tblimport ON tblimport.id = tblimport_items.id_import
                WHERE tblimport.warehouseman_id != 0
                ORDER BY product_id asc
            )

            UNION ALL    
            (
                SELECT
                    tbl_purchase_products.date_warehouseman as warehouseman_date,
                    tbl_purchase_products.date as date,
                    tbl_purchase_products.reference_no as code,
                    tbl_purchase_products.note as reason,
                    tbl_purchase_product_items.quantity as import_quantity,
                    0 as export_quantity,
                    tbl_purchase_products.id as id_main,
                    tbl_purchase_product_items.item_id as product_id,
                    " . ch_where('tbl_purchase_product_items', 'type_item') . " as type,
                    tbl_purchase_product_items.location_id as localtion_id,
                    '' as id_import,
                    tbl_purchase_products.warehouse_id as warehouse_id,
                    quantity_payment,
                    price
                FROM tbl_purchase_product_items
                LEFT JOIN tbl_purchase_products ON tbl_purchase_products.id = tbl_purchase_product_items.purchase_product_id
                WHERE tbl_purchase_products.warehouseman_id != 0
                ORDER BY item_id asc
            )
            UNION ALL    
            (
                SELECT
                    tbl_deliveries.date_warehouseman as warehouseman_date,
                    tbl_deliveries.date as date,
                    tbl_deliveries.reference_no as code,
                    tbl_deliveries.note as reason,
                    0 as import_quantity,
                    tbl_delivery_items.quantity as export_quantity,
                    tbl_deliveries.id as id_main,
                    tbl_delivery_items.item_id as product_id,
                    " . ch_where('tbl_delivery_items', 'type_item') . " as type,
                    tbl_delivery_items.location_id as localtion_id,
                    id_import,
                    tbl_delivery_items.warehouse_id as warehouse_id,
                    0 as quantity_payment,
                    0 as price
                FROM tbl_delivery_items
                LEFT JOIN tbl_deliveries ON tbl_deliveries.id = tbl_delivery_items.delivery_id
                WHERE tbl_deliveries.warehouseman_id != 0
                ORDER BY item_id asc
            )
            UNION ALL
            (
                SELECT
                    tblreturn_suppliers.data_warehouseman as warehouseman_date,
                    tblreturn_suppliers.date as date,
                    concat(tblreturn_suppliers.prefix,'',tblreturn_suppliers.code) as code,
                    tblreturn_suppliers.note as reason,
                    0 as import_quantity,
                    tblreturn_suppliers_items.quantity_net as export_quantity,
                    tblreturn_suppliers.id as id_main,
                    tblreturn_suppliers_items.product_id as product_id,
                    tblreturn_suppliers_items.type as type,
                    tblreturn_suppliers_items.localtion_warehouses_id as localtion_id,
                    id_import,
                    tblreturn_suppliers.warehouse_id as warehouse_id,
                    0 as quantity_payment,
                    0 as price
                FROM tblreturn_suppliers_items
                LEFT JOIN tblreturn_suppliers ON tblreturn_suppliers.id = tblreturn_suppliers_items.id_return
                WHERE tblreturn_suppliers.warehouseman_id != 0 
                ORDER BY product_id asc
            )
            UNION ALL
            (
                SELECT
                    tbladjusted.date_create as warehouseman_date,
                    tbladjusted.date as date,
                    concat(tbladjusted.prefix,'',tbladjusted.code) as code,
                    tbladjusted.note as reason,
                    0 as import_quantity,
                    tbladjusted_items.quantity_net as export_quantity,
                    tbladjusted.id as id_main,
                    tbladjusted_items.product_id as product_id,
                    tbladjusted_items.type as type,
                    tbladjusted_items.localtion as localtion_id,
                    id_import,
                    tbladjusted.warehouse_id as warehouse_id,
                    quantity_payment,
                    price
                FROM tbladjusted_items
                LEFT JOIN tbladjusted ON tbladjusted.id = tbladjusted_items.id_adjusted
                WHERE tbladjusted.type = 2
                ORDER BY product_id asc
            )
            UNION ALL
            (
                SELECT
                    tbladjusted.date_create as warehouseman_date,
                    tbladjusted.date as date,
                    concat(tbladjusted.prefix,'',tbladjusted.code) as code,
                    tbladjusted.note as reason,
                    tbladjusted_items.quantity_net as import_quantity,
                    0 as export_quantity,
                    tbladjusted.id as id_main,
                    tbladjusted_items.product_id as product_id,
                    tbladjusted_items.type as type,
                    tbladjusted_items.localtion as localtion_id,
                    id_import,
                    tbladjusted.warehouse_id as warehouse_id,
                    quantity_payment,
                    price
                FROM tbladjusted_items
                LEFT JOIN tbladjusted ON tbladjusted.id = tbladjusted_items.id_adjusted
                WHERE tbladjusted.type = 1
                ORDER BY product_id asc
            )
            UNION ALL
            (
                SELECT
                    tbltransfer_warehouse.warehouseman_date as warehouseman_date,
                    tbltransfer_warehouse.date as date,
                    concat(tbltransfer_warehouse.prefix,'-',tbltransfer_warehouse.code) as code,
                    tbltransfer_warehouse.note as reason,
                    tbltransfer_warehouse_detail.quantity_net as import_quantity,
                    0 as export_quantity,
                    tbltransfer_warehouse.id as id_main,
                    tbltransfer_warehouse_detail.id_items as product_id,
                    tbltransfer_warehouse_detail.type as type,
                    tbltransfer_warehouse_detail.localtion_to as localtion_id,
                    '' as id_import,
                    tbltransfer_warehouse_detail.warehouses_to as warehouses_id,
                    quantity_payment,
                    price
                FROM tbltransfer_warehouse_detail
                LEFT JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer
                WHERE  tbltransfer_warehouse.warehouseman_id != 0
                ORDER BY id_items asc
            )
            UNION ALL
            (
                SELECT
                    tbltransfer_warehouse.warehouseman_date as warehouseman_date,
                    tbltransfer_warehouse.date as date,
                    concat(tbltransfer_warehouse.prefix,'-',tbltransfer_warehouse.code) as code,
                    tbltransfer_warehouse.note as reason,
                    0 as import_quantity,
                    tbltransfer_warehouse_detail.quantity_net as export_quantity,
                    tbltransfer_warehouse.id as id_main,
                    tbltransfer_warehouse_detail.id_items as product_id,
                    tbltransfer_warehouse_detail.type as type,
                    tbltransfer_warehouse_detail.localtion_id as localtion_id,
                    id_import,
                    tbltransfer_warehouse_detail.warehouses_id as warehouses_id,
                    quantity_payment,
                    price
                FROM tbltransfer_warehouse_detail
                LEFT JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer
                WHERE tbltransfer_warehouse.warehouseman_id != 0
                ORDER BY id_items asc
            )
            UNION ALL
            (
                SELECT
                    tbl_suggest_exporting.date_warehouseman as warehouseman_date,
                    tbl_suggest_exporting.date as date,
                    tbl_suggest_exporting.reference_stock as code,
                    tbl_suggest_exporting.note as reason,
                    0 as import_quantity,
                    tbl_suggest_exporting_items.quantity_exchange as export_quantity,
                    tbl_suggest_exporting.id as id_main,
                    tbl_suggest_exporting_items.item_id as product_id,
                    " . ch_where('tbl_suggest_exporting_items', 'type_item') . " as type,
                    tbl_suggest_exporting_items.location_id as localtion_id,
                    id_import,
                    tbl_suggest_exporting_items.warehouse_item_id as warehouse_item_id,
                    0 as quantity_payment,
                    0 as price
                FROM tbl_suggest_exporting_items
                LEFT JOIN tbl_suggest_exporting ON tbl_suggest_exporting.id = tbl_suggest_exporting_items.suggest_exporting_id
                WHERE  tbl_suggest_exporting.warehouseman_id != 0 AND tbl_suggest_exporting.status_stock is not NULL
                ORDER BY item_id asc
            )
            UNION ALL
            (
                SELECT
                    tbl_returned_goods.warehouseman_date as warehouseman_date,
                    tbl_returned_goods.date as date,
                    tbl_returned_goods.reference_no as code,
                    tbl_returned_goods.note as reason,
                    tbl_returned_goods_items.quantity as import_quantity,
                    0 as export_quantity,
                    tbl_returned_goods.id as id_main,
                    tbl_returned_goods_items.item_id as product_id,
                    " . ch_where('tbl_returned_goods_items', 'type_item') . " as type,
                    tbl_returned_goods_items.localtion_id as localtion_id,
                    id_import,
                    tbl_returned_goods_items.warehouse_id as warehouse_id,
                    0 as quantity_payment,
                    0 as price
                FROM tbl_returned_goods_items
                LEFT JOIN tbl_returned_goods ON tbl_returned_goods.id = tbl_returned_goods_items.returned_goods_id
                WHERE tbl_returned_goods.warehouseman_id != 0
                ORDER BY item_id asc
            )
            UNION ALL
            (
                SELECT
                    tblexport_different.date_create as warehouseman_date,
                    tblexport_different.date as date,
                    concat(tblexport_different.prefix,'-',tblexport_different.code) as code,
                    tblexport_different.note as reason,
                    0 as import_quantity,
                    tbltblexport_different_items.quantity_net as export_quantity,
                    tblexport_different.id as id_main,
                    tbltblexport_different_items.product_id as product_id,
                    tbltblexport_different_items.type as type,
                    tbltblexport_different_items.localtion_warehouses_id as localtion_id,
                    id_import,
                    tbltblexport_different_items.warehouses_id as warehouses_id,
                    0 as quantity_payment,
                    0 as price
                FROM tbltblexport_different_items
                LEFT JOIN tblexport_different ON tblexport_different.id = tbltblexport_different_items.id_export_different
                WHERE tblexport_different.warehouseman_id != 0
                ORDER BY product_id asc
            )
        ";
        $data = $this->db->query($query)->result_array();
        $this->response($data, REST_Controller::HTTP_OK);
    }
    public function Getwarehouse_product()
    {
        $warehouse = $this->db->get('tblwarehouse_product')->result_array();
        $this->response($warehouse, REST_Controller::HTTP_OK);
    }
}
