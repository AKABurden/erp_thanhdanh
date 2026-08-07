<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Power_bi_purchases extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    public function ListStaff()
    {
        $this->db->select('
            tblstaff.staffid as staffid,
            CONCAT(firstname," ",lastname) as full_name,
        ');
        $staff = $this->db->get('tblstaff')->result_array();
        $this->response($staff, REST_Controller::HTTP_OK);
    }
    public function ListSuppliers()
    {
        $this->db->select('
            tblsuppliers.id as id,
            tblsuppliers.code as code,
            tblsuppliers.company as company,
        ');
        $suppliers = $this->db->get('tblsuppliers')->result_array();
        $today = date('Y-m-d');
        $week30s = strtotime(date("Y-m-d", strtotime($today)) . " -30 day");
        $week30 = strftime("%Y-%m-%d", $week30s);
        $week60s = strtotime(date("Y-m-d", strtotime($today)) . " -60 day");
        $week60 = strftime("%Y-%m-%d", $week60s);
        $week90s = strtotime(date("Y-m-d", strtotime($today)) . " -90 day");
        $week90 = strftime("%Y-%m-%d", $week90s);
        foreach ($suppliers as $key => $value) {
            // // $whereJoin = array();
            // // $whereJoin['where'] = array(
            // //     'tblimport.suppliers_id' => $value['id'],
            // // );
            // // $whereJoin['where_or'] = "((tblpurchase_order.status_pay != 2))"; 
            // // $whereJoin['join'] = array(
            // //     'tblpurchase_order,tblpurchase_order.id=tblimport.id_order,inner'
            // // );
            // // $whereJoin['field'] = 'tblimport.total';
            // // $subtotal = sum_from_table_join('tblimport', $whereJoin);

            // // $whereJoin1 = array();
            // // $whereJoin1['where'] = array(
            // //     'tblpurchase_order.suppliers_id' => $value['id'],
            // //     'tblpurchase_order.id IN(select id_order from tblimport)',
            // // );
            // // $whereJoin1['where_or'] = "((tblpurchase_order.status_pay != 2 AND tblpurchase_order.red_invoice = 0) or (tblpurchase_invoice.status != 2 AND tblpurchase_order.status_pay = 0))";
            // // $whereJoin1['join'] = array('tblpurchase_invoice,tblpurchase_invoice.id=tblpurchase_order.red_invoice,left');
            // // $whereJoin1['field'] = 'tblpurchase_order.amount_paid';
            // // $amount_paid = sum_from_table_join('tblpurchase_order', $whereJoin1);

            // // $whereJoin2 = array();
            // // $whereJoin2['where'] = array(
            // //     'tblpurchase_order.suppliers_id' => $value['id'],
            // //     'tblpurchase_order.id IN(select id_order from tblimport)',
            // // );
            // // $whereJoin2['where_or'] = "((tblpurchase_order.status_pay != 2 AND tblpurchase_order.red_invoice = 0))"; 
            // // $whereJoin2['join'] = array('tblpurchase_invoice,tblpurchase_invoice.id=tblpurchase_order.red_invoice,left');
            // // $whereJoin2['field'] = 'tblpurchase_order.price_other_expenses';
            // // $amount_paid_invoice = sum_from_table_join('tblpurchase_order', $whereJoin2);

            // // $whereJoin5 = array();
            // // $whereJoin5['where'] = array(
            // //     'tbl_services.suppliers_id' => $value['id'],
            // //     'tbl_services.status' => 1,
            // // );
            // // $whereJoin5['join'] = array();
            // // $whereJoin5['field'] = '(tbl_services.subtotal - tbl_services.payment)';
            // // $services = sum_from_table_join_v2('tbl_services', $whereJoin5);

            // $suppliers[$key]['debt'] = ($subtotal - $amount_paid - $amount_paid_invoice + $services);
            $suppliers[$key]['debt'] = debt_supplierts_v3($value['id']) - pay_slip_ch_v3($value['id']);

            // $whereJoin = array();
            // $whereJoin['where'] = array(
            //     'tblimport.suppliers_id' => $value['id'],
            //     'tblimport.date >=' => $week30,
            //     'tblimport.date <=' => $today,
            // );
            // $whereJoin['where_or'] = "((tblpurchase_order.status_pay != 2))"; 
            // $whereJoin['join'] = array(
            //     'tblpurchase_order,tblpurchase_order.id=tblimport.id_order,inner'
            // );
            // $whereJoin['field'] = 'tblimport.total';
            // $subtotal = sum_from_table_join('tblimport', $whereJoin);

            // $whereJoin1 = array();
            // $whereJoin1['where'] = array(
            //     'tblpurchase_order.suppliers_id' => $value['id'],
            //     'tblpurchase_order.date >=' => $week30,
            //     'tblpurchase_order.date <=' => $today,
            //     'tblpurchase_order.id IN(select id_order from tblimport)',
            // );
            // $whereJoin1['where_or'] = "((tblpurchase_order.status_pay != 2 AND tblpurchase_order.red_invoice = 0) or (tblpurchase_invoice.status != 2 AND tblpurchase_order.status_pay = 0))";
            // $whereJoin1['join'] = array('tblpurchase_invoice,tblpurchase_invoice.id=tblpurchase_order.red_invoice,left');
            // $whereJoin1['field'] = 'tblpurchase_order.amount_paid';
            // $amount_paid = sum_from_table_join('tblpurchase_order', $whereJoin1);

            // $whereJoin2 = array();
            // $whereJoin2['where'] = array(
            //     'tblpurchase_order.suppliers_id' => $value['id'],
            //     'tblpurchase_order.date >=' => $week30,
            //     'tblpurchase_order.date <=' => $today,
            //     'tblpurchase_order.id IN(select id_order from tblimport)',
            // );
            // $whereJoin2['where_or'] = "((tblpurchase_order.status_pay != 2 AND tblpurchase_order.red_invoice = 0))"; 
            // $whereJoin2['join'] = array('tblpurchase_invoice,tblpurchase_invoice.id=tblpurchase_order.red_invoice,left');
            // $whereJoin2['field'] = 'tblpurchase_order.price_other_expenses';
            // $amount_paid_invoice = sum_from_table_join('tblpurchase_order', $whereJoin2);

            // $whereJoin5 = array();
            // $whereJoin5['where'] = array(
            //     'tbl_services.suppliers_id' => $value['id'],
            //     'tbl_services.status' => 1,
            //     'tbl_services.date >=' => $week30,
            //     'tbl_services.date <=' => $today,
            // );
            // $whereJoin5['join'] = array();
            // $whereJoin5['field'] = '(tbl_services.subtotal - tbl_services.payment)';
            // $services = sum_from_table_join_v2('tbl_services', $whereJoin5);

            // $suppliers[$key]['debt_30N'] = ($subtotal - $amount_paid - $amount_paid_invoice + $services);

            $suppliers[$key]['debt_30N'] = debt_supplierts_v3($value['id'], $week30, $today) - pay_slip_ch_v3($value['id'], $week30, $today);

            // $whereJoin = array();
            // $whereJoin['where'] = array(
            //     'tblimport.suppliers_id' => $value['id'],
            //     'tblimport.date >=' => $week60,
            //     'tblimport.date <' => $week30,
            // );
            // $whereJoin['join'] = array();
            // $whereJoin['where_or'] = "((tblpurchase_order.status_pay != 2))";
            // $whereJoin['join'] = array(
            //     'tblpurchase_order,tblpurchase_order.id=tblimport.id_order,inner'
            // );
            // $whereJoin['field'] = 'tblimport.total';
            // $subtotal = sum_from_table_join('tblimport', $whereJoin);

            // $whereJoin1 = array();
            // $whereJoin1['where'] = array(
            //     'tblpurchase_order.suppliers_id' => $value['id'],
            //     'tblpurchase_order.date >=' => $week60,
            //     'tblpurchase_order.date <' => $week30,
            //     'tblpurchase_order.id IN(select id_order from tblimport)',
            // );
            // $whereJoin1['where_or'] = "((tblpurchase_order.status_pay != 2 AND tblpurchase_order.red_invoice = 0) or (tblpurchase_invoice.status != 2 AND tblpurchase_order.status_pay = 0))";
            // $whereJoin1['join'] = array('tblpurchase_invoice,tblpurchase_invoice.id=tblpurchase_order.red_invoice,left');
            // $whereJoin1['field'] = 'tblpurchase_order.amount_paid';
            // $amount_paid = sum_from_table_join('tblpurchase_order', $whereJoin1);

            // $whereJoin2 = array();
            // $whereJoin2['where'] = array(
            //     'tblpurchase_order.suppliers_id' => $value['id'],
            //     'tblpurchase_order.date >=' => $week60,
            //     'tblpurchase_order.date <' => $week30,
            //     'tblpurchase_order.id IN(select id_order from tblimport)',
            // );
            // $whereJoin2['where_or'] = "((tblpurchase_order.status_pay != 2 AND tblpurchase_order.red_invoice = 0) or (tblpurchase_invoice.status != 2 AND tblpurchase_order.status_pay = 0))";
            // $whereJoin2['join'] = array('tblpurchase_invoice,tblpurchase_invoice.id=tblpurchase_order.red_invoice,left');
            // $whereJoin2['field'] = 'tblpurchase_order.price_other_expenses';
            // $amount_paid_invoice = sum_from_table_join('tblpurchase_order', $whereJoin2);

            // $whereJoin5 = array();
            // $whereJoin5['where'] = array(
            //     'tbl_services.suppliers_id' => $value['id'],
            //     'tbl_services.status' => 1,
            //     'tbl_services.date >=' => $week60,
            //     'tbl_services.date <=' => $week30,
            // );
            // $whereJoin5['join'] = array();
            // $whereJoin5['field'] = '(tbl_services.subtotal - tbl_services.payment)';
            // $services = sum_from_table_join_v2('tbl_services', $whereJoin5);
            // $suppliers[$key]['debt_30N60N'] = ($subtotal - $amount_paid - $amount_paid_invoice + $services);
            $suppliers[$key]['debt_30N60N'] = debt_supplierts_v3($value['id'], $week60, $week30) - pay_slip_ch_v3($value['id'], $week60, $week30);


            // $whereJoin = array();
            // $whereJoin['where'] = array(
            //     'tblimport.suppliers_id' => $value['id'],
            //     'tblimport.date <' => $week60,
            // );
            // $whereJoin['join'] = array();
            // $whereJoin['where_or'] = "((tblpurchase_order.status_pay != 2))";
            // $whereJoin['join'] = array(
            //     'tblpurchase_order,tblpurchase_order.id=tblimport.id_order,inner'
            // );
            // $whereJoin['field'] = 'tblimport.total';
            // $subtotal = sum_from_table_join('tblimport', $whereJoin);


            // $whereJoin1 = array();
            // $whereJoin1['where'] = array(
            //     'tblpurchase_order.suppliers_id' => $value['id'],
            //     'tblpurchase_order.date <' => $week60,
            //     'tblpurchase_order.id IN(select id_order from tblimport)',
            // );
            // $whereJoin1['where_or'] = "((tblpurchase_order.status_pay != 2 AND tblpurchase_order.red_invoice = 0) or (tblpurchase_invoice.status != 2 AND tblpurchase_order.status_pay = 0))";
            // $whereJoin1['join'] = array('tblpurchase_invoice,tblpurchase_invoice.id=tblpurchase_order.red_invoice,left');
            // $whereJoin1['field'] = 'tblpurchase_order.amount_paid';
            // $amount_paid = sum_from_table_join('tblpurchase_order', $whereJoin1);

            // $whereJoin2 = array();
            // $whereJoin2['where'] = array(
            //     'tblpurchase_order.suppliers_id' => $value['id'],
            //     'tblpurchase_order.date <' => $week60,
            //     'tblpurchase_order.id IN(select id_order from tblimport)',
            // );
            // $whereJoin2['where_or'] = "((tblpurchase_order.status_pay != 2 AND tblpurchase_order.red_invoice = 0) or (tblpurchase_invoice.status != 2 AND tblpurchase_order.status_pay = 0))";
            // $whereJoin2['join'] = array('tblpurchase_invoice,tblpurchase_invoice.id=tblpurchase_order.red_invoice,left');
            // $whereJoin2['field'] = 'tblpurchase_order.price_other_expenses';
            // $amount_paid_invoice = sum_from_table_join('tblpurchase_order', $whereJoin2);

            // $whereJoin5 = array();
            // $whereJoin5['where'] = array(
            //     'tbl_services.suppliers_id' => $value['id'],
            //     'tbl_services.status' => 1,
            //     'tbl_services.date <' => $week60,
            // );
            // $whereJoin5['join'] = array();
            // $whereJoin5['field'] = '(tbl_services.subtotal - tbl_services.payment)';
            // $services = sum_from_table_join_v2('tbl_services', $whereJoin5);
            // $suppliers[$key]['debt_60N'] = ($subtotal - $amount_paid - $amount_paid_invoice + $services);
            $suppliers[$key]['debt_60N'] = debt_supplierts_v3($value['id'], NULL, $week60) - pay_slip_ch_v3($value['id'], NULL, $week60);
        }
        $this->response($suppliers, REST_Controller::HTTP_OK);
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
    public function ListPurchases()
    {
        $purchase_order = '
            COALESCE(
            (
                SELECT COUNT(tblpurchase_order.id) 
                FROM tblpurchase_order 
                WHERE tblpurchase_order.id_purchases = tblpurchases.id';
        $purchase_order .= '),0)';
        $suppliers = '
            COALESCE(
            (
                SELECT tblpurchase_order.suppliers_id
                FROM tblpurchase_order 
                WHERE tblpurchase_order.id_purchases = tblpurchases.id
                ORDER BY tblpurchase_order.id desc
                limit 1
            ),0)';
        $this->db->select("
            tblpurchases.id as id,
            CONCAT(tblpurchases.prefix,'-',tblpurchases.code) as code,
            tblpurchases.name_purchase as name_purchase,
            DATE_FORMAT(tblpurchases.date,'%Y-%m-%d') as date,
            tblpurchases.explanation as explanation,
            tblpurchases.status as status,
            tblpurchases.date_create as date_create,
            tblpurchases.staff_create as staff_create,
            tblpurchases.type as type,
            $purchase_order as id_order,
            $suppliers as id_suppliers,
            tblpurchases.is_plans as is_plans,
            tblpurchases.order_id as order_id,
            tblpurchases.type_plan as type_plan,
        ");
        $purchases = $this->db->get('tblpurchases')->result_array();
        $this->response($purchases, REST_Controller::HTTP_OK);
    }
    public function ListPurchasesItems()
    {
        $this->db->select("
            tblpurchases_items.id as id,
            tblpurchases_items.purchases_id as purchases_id,
            tblpurchases_items.product_id as product_id,
            tblpurchases_items.type as type,
            CONCAT(tblpurchases_items.type,'_',tblpurchases_items.product_id) as mapitems,
            tblpurchases_items.quantity as quantity,
            tblpurchases_items.quantity_net as quantity_net,
            tblpurchases_items.order_item_id as order_item_id,
        ");
        $purchases_items = $this->db->get('tblpurchases_items')->result_array();
        $this->response($purchases_items, REST_Controller::HTTP_OK);
    }

    public function ListPurchasesOrder()
    {
        $import = '
            COALESCE(
            (
                SELECT COUNT(tblimport.id) 
                FROM tblimport 
                WHERE tblimport.id_order = tblpurchase_order.id),0)';
        $date_import = '
            COALESCE(
            (
                SELECT tblimport.date
                FROM tblimport 
                WHERE tblimport.id_order = tblpurchase_order.id
                ORDER BY tblimport.id desc
                limit 1
            ),"' . date('Y-m-d') . '")';
        $purchase_order_items = '
            COALESCE(
            (
                SELECT COALESCE(SUM(tblpurchase_order_items.quantity_suppliers),0)
                FROM tblpurchase_order_items 
                WHERE tblpurchase_order_items.id_purchase_order = tblpurchase_order.id),0)';
        $quantili_import = '
            COALESCE((Select SUM(tblimport_items.quantity_net) from tblimport_items LEFT JOIN tblimport ON tblimport.id=tblimport_items.id_import where tblimport.id_order=tblpurchase_order.id';
        $quantili_import .= '),0)';
        // (tblpurchase_order_items.quantity_suppliers - COALESCE((Select SUM(tblimport_items.quantity_net) from tblimport_items LEFT JOIN tblimport ON tblimport.id=tblimport_items.id_import where tblimport.id_order=tblpurchase_order.id AND tblimport_items.product_id=tblpurchase_order_items.product_id AND tblimport_items.type=tblpurchase_order_items.type),0)) as leftss
        $this->db->select("
            tblpurchase_order.id as id,
            CONCAT(tblpurchase_order.prefix,'-',tblpurchase_order.code) as code,
            tblpurchase_order.date as date,
            tblpurchase_order.note as note,
            tblpurchase_order.status as status,
            tblpurchase_order.date_create as date_create,
            tblpurchase_order.staff_create as staff_create,
            tblpurchase_order.total as total,
            ROUND(tblpurchase_order.totalAll_suppliers,0) as totalAll_suppliers,
            tblpurchase_order.total_novat as total_novat,
            tblpurchase_order.price_suppliers as price_suppliers,
            tblpurchase_order.status_pay as status_pay,
            tblpurchase_order.amount_paid_debt as amount_paid_debt,
            ROUND(tblpurchase_order.amount_paid,0) as amount_paid,
            tblpurchase_order.delivery_date as delivery_date,
            tblpurchase_order.delivery_cost as delivery_cost,
            tblpurchase_order.id_purchases as id_purchases,
            tblpurchase_order.suppliers_id as suppliers_id,
            tblpurchase_order.id_branch as id_branch,
            tblpurchase_order.type_order as type_order,
            $date_import as date_import,
            $purchase_order_items as purchase_order_items,
            $quantili_import as quantili_import,
            $import as id_import
        ");
        $purchase_order = $this->db->get('tblpurchase_order')->result_array();
        $this->response($purchase_order, REST_Controller::HTTP_OK);
    }
    public function ListPurchasesOrderItems()
    {
        $this->db->select("
            tblpurchase_order_items.id as id,
            tblpurchase_order_items.id_purchase_order as id_purchase_order,
            tblpurchase_order_items.product_id as product_id,
            tblpurchase_order_items.type as type,
            CONCAT(tblpurchase_order_items.type,'_',tblpurchase_order_items.product_id) as mapitems,
            tblpurchase_order_items.quantity as quantity,
            tblpurchase_order_items.quantity_suppliers as quantity_suppliers,
            tblpurchase_order_items.price_suppliers as price_suppliers,
            tblpurchase_order_items.tax_rate as tax_rate,
            ROUND(tblpurchase_order_items.total_suppliers,0) as total_suppliers,
            tblpurchase_order_items.quantity_unit as quantity_unit,
            tblpurchase_order_items.quantity_payment as quantity_payment,
            tblpurchase_order_items.quantity_stock as quantity_stock,
        ");
        $purchases_items = $this->db->get('tblpurchase_order_items')->result_array();
        $this->response($purchases_items, REST_Controller::HTTP_OK);
    }
    public function ListImport()
    {
        $this->db->select("
            tblimport.id as id,
            tblimport.id_order as id_order,
            CONCAT(tblimport.prefix,'-',tblimport.code) as code,
            tblimport.warehouse_id as warehouse_id,
            tblimport.date as date,
            tblimport.suppliers_id as suppliers_id,
            tblimport.status as status,
            tblimport.date_create as date_create,
            tblimport.staff_create as staff_create,
            tblimport.total as total,
            tblimport.total_novat as total_novat,
            tblimport.warehouseman_id as warehouseman_id,
            tblimport.warehouseman_date as warehouseman_date,
            tblimport.red_invoice as red_invoice,
            tblimport.status_qc as status_qc,
            tblimport.date_qc as date_qc,
            tblimport.type_plan as type_plan,
            tblimport.plan_id as plan_id,

        ");
        $import = $this->db->get('tblimport')->result_array();
        $this->response($import, REST_Controller::HTTP_OK);
    }
    public function ListImportItems()
    {
        $this->db->select("
            tblimport_items.id as id,
            tblimport_items.id_import as id_import,
            tblimport_items.id_purchase_order_items as id_purchase_order_items,
            tblimport_items.product_id as product_id,
            tblimport_items.type as type,
            CONCAT(tblimport_items.type,'_',tblimport_items.product_id) as mapitems,
            tblimport_items.quantity as quantity,
            tblimport_items.quantity_net as quantity_net,
            tblimport_items.tax_rate as tax_rate,
            tblimport_items.price as price,
            tblimport_items.amount as amount,
            tblimport_items.lot_code as lot_code,
            tblimport_items.date_sx as date_sx,
            tblimport_items.date_sd as date_sd,
            tblimport_items.date_use as date_use,
            tblimport_items.order_item_id as order_item_id,
            tblimport_items.quantity_unit as quantity_unit,
            tblimport_items.quantity_stock as quantity_stock,
            tblimport_items.quantity_payment as quantity_payment,
        ");
        $import_items = $this->db->get('tblimport_items')->result_array();
        $this->response($import_items, REST_Controller::HTTP_OK);
    }

    public function ListProductionReport()
    {
        $this->db->select("
            tblproduction_report.id as id,
            DATE_FORMAT(tblproduction_report.date,'%Y-%m-%d') as date,
            tblproduction_report.id_trouble as id_trouble,
            tblproduction_report.suppler_id as suppler_id,
            tblproduction_report.id_branch as id_branch
        ");
        $this->db->where('suppler_id > ', 0);
        $production_report = $this->db->get('tblproduction_report')->result_array();
        if(empty($production_report)){
            $production_report[] = [
                'id'=>0,
                'date'=>NULL,
                'id_trouble'=>0,
                'suppler_id'=>0,
                'id_branch'=>0
            ];
        }
        $this->response($production_report, REST_Controller::HTTP_OK);
    }
    public function Trouble()
    {
        $this->db->select("
            tbltrouble.id as id,
            tbltrouble.name_stage as name_stage,
            tbltrouble.code as code,
            tbltrouble.name
        ");
        $trouble = $this->db->get('tbltrouble')->result_array();
        $this->response($trouble, REST_Controller::HTTP_OK);
    }
    public function getitems()
    {
        $today = date('Y-m-d');
        $week30s = strtotime(date("Y-m-d", strtotime($today)) . " -30 day");
        $week30 = strftime("%Y-%m-%d", $week30s);
        $week60s = strtotime(date("Y-m-d", strtotime($today)) . " -60 day");
        $week60 = strftime("%Y-%m-%d", $week60s);
        $week90s = strtotime(date("Y-m-d", strtotime($today)) . " -90 day");
        $week90 = strftime("%Y-%m-%d", $week90s);
        // $suppliers[$key]['debt_60N'] = debt_supplierts_v3($value['id'], NULL, $week60) - pay_slip_ch_v3($value['id'], NULL, $week60);

        $suppliers['debt_supplierts_v3'] = debt_supplierts_v3(86, NULL, $week60) ;
        $suppliers['pay_slip_ch_v3'] = pay_slip_ch_v3(86, NULL, $week60);
        echo '<pre>';print_arrays($suppliers);die;
    }
}
