<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Power_bi_stock extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function warehouse()
    {
        $this->db->select('tblwarehouse.id,tblwarehouse.name,"" as group,"" as branch');

        $warehouse = $this->db->get('tblwarehouse')->result_array();

        $html = '<table>
                <thead>
                    <tr>
                        <th>id</th>
                        <th>name</th>
                        <th>group</th>
                        <th>branch</th>
                    </tr>
                    </thead>
                <tbody>';
        foreach ($warehouse as $key => $value) {
            $html .= '<tr>
                        <td>' . $value['id'] . '</td>
                        <td>' . $value['name'] . '</td>
                        <td>' . $value['group'] . '</td>
                        <td>' . $value['branch'] . '</td>
                    </tr>';
        }
        $html .= '</tbody>
            </table>';
        echo ($html);
        die();
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

                    $total['total_begin']  += $row->total_begin;
                    $total['total_nhap']  += $row->total_nhap;
                    $total['total_xuat']  += $row->total_xuat;
                    $total['total_left']  += $row->total_left;
                }
            }
        }
        return $total;
    }
    public function get_stock_product()
    {
        $week = $this->GetWeek();
        $last_week = $week - 5;
        $this->db->where('type_items', 'product');
        $this->db->order_by('warehouse_id,date_warehouse', 'acs');
        $warehouse_product = $this->db->get('tbl_warehouse_stock_price')->result_array();
        $html = '<table>
                <thead>
                    <tr>
                        <th>warehouse_id</th>
                        <th>week</th>
                        <th>total_begin</th>
                        <th>total_nhap</th>
                        <th>total_xuat</th>
                        <th>total_left</th>
                    </tr>
                    </thead>
                <tbody>';

        $this->db->order_by('id', 'acs');
        $warehouse = $this->db->get('tblwarehouse')->result_array();
        $ins = array();
        $dems = 0;
        
        foreach ($warehouse as $key => $value) {
            $dauki = 0;
            for ($i = $last_week; $i <= $week; $i++) {
                $dates = $this->getStartAndEndDate($i, date('Y'));
                if ($i == $last_week) {
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
                    $dauki+= $total['total_nhap'] - $total['total_xuat'];
                    $ins[$dems]['total_left'] = $dauki;
                    $dems++;
                }
            }
        }
        foreach ($ins as $key => $value) {
            $html .= '<tr>
                        <td>' . $value['warehouse_id'] . '</td>
                        <td>' . $value['week'] . '</td>
                        <td>' . round($value['total_begin']) . '</td>
                        <td>' . round($value['total_nhap']) . '</td>
                        <td>' . round($value['total_xuat']) . '</td>
                        <td>' . round($value['total_left']) . '</td>
                    </tr>';
        }
        $html .= '</tbody>
            </table>';
        echo ($html);
        die();
    }
    public function get_stock()
    {
        $week = $this->GetWeek();
        $last_week = $week - 5;
        $this->db->where('type_items', 'nvl');
        $this->db->order_by('warehouse_id,date_warehouse', 'acs');
        $warehouse_product = $this->db->get('tbl_warehouse_stock_price')->result_array();
        $html = '<table>
                <thead>
                    <tr>
                        <th>warehouse_id</th>
                        <th>week</th>
                        <th>total_begin</th>
                        <th>total_nhap</th>
                        <th>total_xuat</th>
                        <th>total_left</th>
                    </tr>
                    </thead>
                <tbody>';

        $this->db->order_by('id', 'acs');
        $warehouse = $this->db->get('tblwarehouse')->result_array();
        $ins = array();
        $dems = 0;
        
        foreach ($warehouse as $key => $value) {
            $dauki = 0;
            for ($i = $last_week; $i <= $week; $i++) {
                $dates = $this->getStartAndEndDate($i, date('Y'));
                if ($i == $last_week) {
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
                    $dauki+= $total['total_nhap'] - $total['total_xuat'];
                    $ins[$dems]['total_left'] = $dauki;
                    $dems++;
                }
            }
        }
        foreach ($ins as $key => $value) {
            $html .= '<tr>
                        <td>' . $value['warehouse_id'] . '</td>
                        <td>' . $value['week'] . '</td>
                        <td>' . round($value['total_begin']) . '</td>
                        <td>' . round($value['total_nhap']) . '</td>
                        <td>' . round($value['total_xuat']) . '</td>
                        <td>' . round($value['total_left']) . '</td>
                    </tr>';
        }
        $html .= '</tbody>
            </table>';
        echo ($html);
        die();
    }
    public function add_stock()
    {
        $this->db->select('tblwarehouse_product.*,DATE_FORMAT(date_warehouse, "%Y-%m-%d") as date_warehouse');
        $this->db->where('type_items', 'nvl');
        $this->db->where('month(date_warehouse)', 7);
        $this->db->order_by('date_warehouse', 'acs');
        $this->db->group_by('product_id,DATE_FORMAT(date_warehouse, "%Y-%m-%d")');
        $warehouse_product = $this->db->get('tblwarehouse_product')->result_array();

        $this->db->where('type_items', 'nvl');
        $this->db->where('month(date_warehouse)', 7);
        $this->db->delete('tbl_warehouse_stock_price');

        foreach ($warehouse_product as $key => $value) {
            $sumFExistsQall_price = getStartInventory_v2Array($value['product_id'], $value['type_items'], $value['warehouse_id'], $value['date_warehouse']);
            $price_trongki = getStartInventory_trongkiArray($value['product_id'], $value['type_items'], $value['warehouse_id'], $value['date_warehouse'], $value['date_warehouse']);
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
    public function add_stock_tp()
    {
        $this->db->select('tblwarehouse_product.*,DATE_FORMAT(date_warehouse, "%Y-%m-%d") as date_warehouse');
        $this->db->where('type_items', 'product');
        $this->db->where('month(date_warehouse)', 11);
        $this->db->order_by('date_warehouse', 'acs');
        $this->db->group_by('product_id,DATE_FORMAT(date_warehouse, "%Y-%m-%d")');
        $warehouse_product = $this->db->get('tblwarehouse_product')->result_array();

        $this->db->where('month(date_warehouse)', 11);
        $this->db->where('type_items', 'product');
        $this->db->delete('tbl_warehouse_stock_price');

        foreach ($warehouse_product as $key => $value) {
            $sumFExistsQall_price = getStartInventory_v2Array($value['product_id'], $value['type_items'], $value['warehouse_id'], $value['date_warehouse']);
            $price_trongki = getStartInventory_trongkiArray($value['product_id'], $value['type_items'], $value['warehouse_id'], $value['date_warehouse'], $value['date_warehouse']);
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
    public function stock_nvl()
    {
        $this->db->order_by('date_warehouse', 'acs');
        $this->db->where('date_warehouse >=', '2023-02-27');
        $warehouse_product = $this->db->get('tbl_warehouse_stock_price')->result_array();
        $html = '<table>
                <thead>
                    <tr>
                        <th>id</th>
                        <th>warehouse_id</th>
                        <th>product_id</th>
                        <th>type_items</th>
                        <th>date_warehouse</th>
                        <th>total_begin</th>
                        <th>total_nhap</th>
                        <th>total_xuat</th>
                        <th>total_left</th>
                    </tr>
                    </thead>
                <tbody>';
        foreach ($warehouse_product as $key => $value) {
            $html .= '<tr>
                        <td>' . $value['id'] . '</td>
                        <td>' . $value['warehouse_id'] . '</td>
                        <td>' . $value['product_id'] . '</td>
                        <td>' . $value['type_items'] . '</td>
                        <td>' . $value['date_warehouse'] . '</td>
                        <td>' . round($value['total_begin']) . '</td>
                        <td>' . round($value['total_nhap']) . '</td>
                        <td>' . round($value['total_xuat']) . '</td>
                        <td>' . round($value['total_left']) . '</td>
                    </tr>';
        }
        $html .= '</tbody>
            </table>';
        echo ($html);
        die();
    }
    // public function index_get($type = 'GetTimeNvl')
    // {
    //     if($type == 'GetTimeNvl'){
    //         $this->response($this->GetTimeNvl(), REST_Controller::HTTP_OK);
    //     }
    //     if($type == 'GetTimeProducts'){
    //         $this->response($this->GetTimeProducts(), REST_Controller::HTTP_OK);
    //     }
    //     if($type == 'GetTimeBTPProducts'){
    //         $this->response($this->GetTimeBTPProducts(), REST_Controller::HTTP_OK);
    //     }
    // }
    public function index_get(){
        
    }
    public function GetTimeNvl()
    {
        $this->db->select('tblwarehouse_items.*,DATEDIFF(date_sd, "'.date('Y-m-d').'") as days,IF(DATEDIFF(date_sd, "'.date('Y-m-d').'") < 0,CONCAT("Trễ hạn"),IF((DATEDIFF(date_sd, "'.date('Y-m-d').'") >= 0 and DATEDIFF(date_sd, "'.date('Y-m-d').'") < 30),CONCAT("30 Ngày"),IF((DATEDIFF(date_sd, "'.date('Y-m-d').'") >= 30 and DATEDIFF(date_sd, "'.date('Y-m-d').'") < 60),CONCAT("60 Ngày"),IF((DATEDIFF(date_sd, "'.date('Y-m-d').'") >= 60 and DATEDIFF(date_sd, "'.date('Y-m-d').'") < 90),CONCAT("90 Ngày"),IF((DATEDIFF(date_sd, "'.date('Y-m-d').'") >= 90),CONCAT("Hơn 90 Ngày"),""))))) as type_day');
        $this->db->where('type_items', 'nvl');
        $this->db->where('date_sd !=', NULL);
        $this->db->where('product_quantity >', 0);
        $warehouse_items = $this->db->get('tblwarehouse_items')->result_array();  
        $this->response($warehouse_items, REST_Controller::HTTP_OK);

        // return $warehouse_items;
        // $this->response($warehouse_items, REST_Controller::HTTP_OK);
        // echo '<pre>';print_arrays($warehouse_items);die;  
        // $html = '<table>
        //         <thead>
        //             <tr>
        //                 <th>id</th>
        //                 <th>warehouse_id</th>
        //                 <th>product_id</th>
        //                 <th>type_items</th>
        //                 <th>product_quantity</th>
        //                 <th>days</th>
        //                 <th>type_day</th>
        //             </tr>
        //             </thead>
        //         <tbody>';
        // foreach ($warehouse_items as $key => $value) {
        //     $type_day = '';
        //     if($value['days'] < 0 ){
        //         $type_day = 'Trễ hạn';
        //     }
        //     if($value['days'] >= 0 && $value['days'] < 30){
        //         $type_day = '30 Ngày';
        //     }
        //     if($value['days'] >= 30 && $value['days'] < 60){
        //         $type_day = '60 Ngày';
        //     }
        //     if($value['days'] >= 60 && $value['days'] < 90){
        //         $type_day = '90 Ngày';
        //     }
        //     if($value['days'] >= 90){
        //         $type_day = 'Hơn 90 Ngày';
        //     }
        //     $html .= '<tr>
        //                 <td>' . $value['id'] . '</td>
        //                 <td>' . $value['warehouse_id'] . '</td>
        //                 <td>' . $value['id_items'] . '</td>
        //                 <td>' . $value['type_items'] . '</td>
        //                 <td>' . $value['product_quantity'] . '</td>
        //                 <td>' . $value['days'] . '</td>
        //                 <td>' . $type_day . '</td>
        //             </tr>';
        // }
        // $html .= '</tbody>
        //     </table>';
        // echo ($html);
        // die();
    }
    public function GetTimeProducts()
    {
        $this->db->select('tblwarehouse_items.*,DATEDIFF(date_sd, "'.date('Y-m-d').'") AS days,IF(DATEDIFF(date_sd, "'.date('Y-m-d').'") < 0,CONCAT("Trễ hạn"),IF((DATEDIFF(date_sd, "'.date('Y-m-d').'") >= 0 and DATEDIFF(date_sd, "'.date('Y-m-d').'") < 30),CONCAT("30 Ngày"),IF((DATEDIFF(date_sd, "'.date('Y-m-d').'") >= 30 and DATEDIFF(date_sd, "'.date('Y-m-d').'") < 60),CONCAT("60 Ngày"),IF((DATEDIFF(date_sd, "'.date('Y-m-d').'") >= 60 and DATEDIFF(date_sd, "'.date('Y-m-d').'") < 90),CONCAT("90 Ngày"),IF((DATEDIFF(date_sd, "'.date('Y-m-d').'") >= 90),CONCAT("Hơn 90 Ngày"),""))))) as type_day');
        $this->db->where('type_items', 'product');
        $this->db->where('date_sd !=', NULL);
        $this->db->where('product_quantity >', 0);
        $this->db->where('tbl_products.type_products', 'products');
        $this->db->join('tbl_products', 'tbl_products.id=tblwarehouse_items.id_items', 'left');
        $warehouse_items = $this->db->get('tblwarehouse_items')->result(); 
        echo json_encode($warehouse_items);
        // return $warehouse_items;
        // $this->response($warehouse_items, REST_Controller::HTTP_OK);


        // $html = '<table>
        //         <thead>
        //             <tr>
        //                 <th>id</th>
        //                 <th>warehouse_id</th>
        //                 <th>product_id</th>
        //                 <th>type_items</th>
        //                 <th>product_quantity</th>
        //                 <th>days</th>
        //                 <th>type_day</th>
        //             </tr>
        //             </thead>
        //         <tbody>';
        // foreach ($warehouse_items as $key => $value) {
        //     $type_day = '';
        //     if($value['days'] < 0 ){
        //         $type_day = 'Trễ hạn';
        //     }
        //     if($value['days'] >= 0 && $value['days'] < 30){
        //         $type_day = '30 Ngày';
        //     }
        //     if($value['days'] >= 30 && $value['days'] < 60){
        //         $type_day = '60 Ngày';
        //     }
        //     if($value['days'] >= 60 && $value['days'] < 90){
        //         $type_day = '90 Ngày';
        //     }
        //     if($value['days'] >= 90){
        //         $type_day = 'Hơn 90 Ngày';
        //     }
        //     $html .= '<tr>
        //                 <td>' . $value['id'] . '</td>
        //                 <td>' . $value['warehouse_id'] . '</td>
        //                 <td>' . $value['id_items'] . '</td>
        //                 <td>' . $value['type_items'] . '</td>
        //                 <td>' . $value['product_quantity'] . '</td>
        //                 <td>' . $value['days'] . '</td>
        //                 <td>' . $type_day . '</td>
        //             </tr>';
        // }
        // $html .= '</tbody>
        //     </table>';
        // echo ($html);
        // die();
    }
    public function GetTimeBTPProducts()
    {
        $this->db->select('tblwarehouse_items.*,DATEDIFF(date_sd, "'.date('Y-m-d').'") AS days,IF(DATEDIFF(date_sd, "'.date('Y-m-d').'") < 0,CONCAT("Trễ hạn"),IF((DATEDIFF(date_sd, "'.date('Y-m-d').'") >= 0 and DATEDIFF(date_sd, "'.date('Y-m-d').'") < 30),CONCAT("30 Ngày"),IF((DATEDIFF(date_sd, "'.date('Y-m-d').'") >= 30 and DATEDIFF(date_sd, "'.date('Y-m-d').'") < 60),CONCAT("60 Ngày"),IF((DATEDIFF(date_sd, "'.date('Y-m-d').'") >= 60 and DATEDIFF(date_sd, "'.date('Y-m-d').'") < 90),CONCAT("90 Ngày"),IF((DATEDIFF(date_sd, "'.date('Y-m-d').'") >= 90),CONCAT("Hơn 90 Ngày"),""))))) as type_day');
        $this->db->where('type_items', 'product');
        $this->db->where('date_sd !=', NULL);
        $this->db->where('product_quantity >', 0);
        $this->db->where('tbl_products.type_products !=', 'products');
        $this->db->join('tbl_products', 'tbl_products.id=tblwarehouse_items.id_items', 'left');
        $warehouse_items = $this->db->get('tblwarehouse_items')->result_array();  
        // return $warehouse_items;
        $this->response($warehouse_items, REST_Controller::HTTP_OK);


        // $html = '<table>
        //         <thead>
        //             <tr>
        //                 <th>id</th>
        //                 <th>warehouse_id</th>
        //                 <th>product_id</th>
        //                 <th>type_items</th>
        //                 <th>product_quantity</th>
        //                 <th>days</th>
        //                 <th>type_day</th>
        //             </tr>
        //             </thead>
        //         <tbody>';
        // foreach ($warehouse_items as $key => $value) {
        //     $type_day = '';
        //     if($value['days'] < 0 ){
        //         $type_day = 'Trễ hạn';
        //     }
        //     if($value['days'] >= 0 && $value['days'] < 30){
        //         $type_day = '30 Ngày';
        //     }
        //     if($value['days'] >= 30 && $value['days'] < 60){
        //         $type_day = '60 Ngày';
        //     }
        //     if($value['days'] >= 60 && $value['days'] < 90){
        //         $type_day = '90 Ngày';
        //     }
        //     if($value['days'] >= 90){
        //         $type_day = 'Hơn 90 Ngày';
        //     }
        //     $html .= '<tr>
        //                 <td>' . $value['id'] . '</td>
        //                 <td>' . $value['warehouse_id'] . '</td>
        //                 <td>' . $value['id_items'] . '</td>
        //                 <td>' . $value['type_items'] . '</td>
        //                 <td>' . $value['product_quantity'] . '</td>
        //                 <td>' . $value['days'] . '</td>
        //                 <td>' . $type_day . '</td>
        //             </tr>';
        // }
        // $html .= '</tbody>
        //     </table>';
        // echo ($html);
        // die();
    }
}
