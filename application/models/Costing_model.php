<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Costing_model extends App_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function costingOther($start_date, $end_date, $type)
    {
        $this->db->select('SUM(tblother_payslips.total) total', false);
        $this->db->from('tblother_payslips');
        $this->db->join('tblcosts', 'tblcosts.id = tblother_payslips.id_costs');
        $this->db->where('DATE_FORMAT(tblother_payslips.date, "%Y-%m-%d") >=', $start_date);
        $this->db->where('DATE_FORMAT(tblother_payslips.date, "%Y-%m-%d") <=', $end_date);
        $this->db->where('tblcosts.type', $type);
        return $this->db->get()->row_array();
    }

    public function directMaterialExport($start_date, $end_date)
    {
        $this->db->select("
            GROUP_CONCAT(DISTINCT(tbl_purchase_products.productions_orders_details_id)) as pod_id
        ", false);
        $this->db->from('tbl_purchase_products');
        $this->db->where('tbl_purchase_products.warehouseman_id >', 0);
        $this->db->where('DATE_FORMAT(tbl_purchase_products.date, "%Y-%m-%d") >=', $start_date);
        $this->db->where('DATE_FORMAT(tbl_purchase_products.date, "%Y-%m-%d") <=', $end_date);
        $purchaseProduct = $this->db->get()->row_array();
        // print_arrays($purchaseProduct);


        $this->db->select('SUM(tbl_suggest_exporting.grand_total) as total', false);
        $this->db->from('tbl_suggest_exporting');
        $this->db->where('tbl_suggest_exporting.warehouseman_id >', 0);

        if (!empty($purchaseProduct)) {
            $this->db->where_in('tbl_suggest_exporting.productions_orders_details_id', explode(',', $purchaseProduct['pod_id']));
        } else {
            $this->db->where('tbl_suggest_exporting.id', 0);
        }

        // $this->db->where('DATE_FORMAT(tbl_suggest_exporting.date_stock, "%Y-%m-%d") >=', $start_date);
        // $this->db->where('DATE_FORMAT(tbl_suggest_exporting.date_stock, "%Y-%m-%d") <=', $end_date);
        // print_arrays($this->db->get_compiled_select());
        return $this->db->get()->row_array();
    }

    public function truPheLieu($start_date, $end_date)
    {
        $this->db->select("
            GROUP_CONCAT(DISTINCT(tbl_purchase_products.productions_orders_details_id)) as pod_id
        ", false);
        $this->db->from('tbl_purchase_products');
        $this->db->where('tbl_purchase_products.warehouseman_id >', 0);
        $this->db->where('DATE_FORMAT(tbl_purchase_products.date, "%Y-%m-%d") >=', $start_date);
        $this->db->where('DATE_FORMAT(tbl_purchase_products.date, "%Y-%m-%d") <=', $end_date);
        $purchaseProduct = $this->db->get()->row_array();
        // print_arrays($purchaseProduct);


        $this->db->select('SUM(tbl_purchase_internal.grand_total) as total', false);
        $this->db->from('tbl_purchase_internal');
        $this->db->where('tbl_purchase_internal.warehouseman_id >', 0);

        if (!empty($purchaseProduct)) {
            $this->db->where_in('tbl_purchase_internal.pod_id', explode(',', $purchaseProduct['pod_id']));
        } else {
            $this->db->where('tbl_purchase_internal.id', 0);
        }

        return $this->db->get()->row_array();
    }

    public function getProductsCosting($start_date, $end_date)
    {
        $this->db->select("
            tbl_purchase_product_items.item_id as item_id,
            tbl_purchase_product_items.type_item as type_item,
            tbl_purchase_product_items.item_code as item_code,
            tbl_purchase_product_items.item_name as item_name,
            SUM(tbl_purchase_product_items.quantity) as quantity,
            GROUP_CONCAT(DISTINCT(tbl_purchase_products.id)) as pp_id
        ", false);
        $this->db->from('tbl_purchase_products');
        $this->db->join('tbl_purchase_product_items', 'tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id');
        $this->db->where('tbl_purchase_products.warehouseman_id >', 0);
        $this->db->where('DATE_FORMAT(tbl_purchase_products.date, "%Y-%m-%d") >=', $start_date);
        $this->db->where('DATE_FORMAT(tbl_purchase_products.date, "%Y-%m-%d") <=', $end_date);
        $this->db->group_by('tbl_purchase_product_items.item_id, tbl_purchase_product_items.type_item');
        return $this->db->get()->result_array();
    }

    //Chi phí NVL của mặt hàng
    public function costingMaterialOfProduct($item_id, $start_date, $end_date)
    {
        $this->db->select("
            GROUP_CONCAT(DISTINCT(tbl_purchase_products.productions_orders_details_id)) as pod_id
        ", false);
        $this->db->from('tbl_purchase_products');
        $this->db->join('tbl_purchase_product_items', 'tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id');
        $this->db->where('tbl_purchase_products.warehouseman_id >', 0);
        $this->db->where('DATE_FORMAT(tbl_purchase_products.date, "%Y-%m-%d") >=', $start_date);
        $this->db->where('DATE_FORMAT(tbl_purchase_products.date, "%Y-%m-%d") <=', $end_date);
        $this->db->where('tbl_purchase_product_items.item_id', $item_id);
        $purchaseProduct = $this->db->get()->row_array();


        if (!empty($purchaseProduct)) {
            $material = "(
                SELECT SUM(tbl_suggest_exporting.grand_total)
                FROM tbl_suggest_exporting
                WHERE tbl_suggest_exporting.warehouseman_id > 0 AND tbl_suggest_exporting.productions_orders_details_id IN (".$purchaseProduct['pod_id'].")
            )";
        } else {
            $material = 0;
        }
        
        // $material = "(
        //     SELECT SUM(tbl_suggest_exporting.grand_total)
        //     FROM tbl_suggest_exporting
        //     WHERE tbl_suggest_exporting.warehouseman_id > 0 AND DATE_FORMAT(tbl_suggest_exporting.date_stock, '%Y-%m-%d') >= '$start_date' AND DATE_FORMAT(tbl_suggest_exporting.date_stock, '%Y-%m-%d') <= '$end_date' AND tbl_suggest_exporting.productions_orders_details_id = tbl_purchase_products.productions_orders_details_id
        // )";

        $this->db->select("$material as total", false);
        $this->db->from('tbl_purchase_products');
        $this->db->join('tbl_purchase_product_items', 'tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id');
        // $this->db->where('tbl_purchase_products.warehouseman_id >', 0);
        $this->db->where('DATE_FORMAT(tbl_purchase_products.date, "%Y-%m-%d") >=', $start_date);
        $this->db->where('DATE_FORMAT(tbl_purchase_products.date, "%Y-%m-%d") <=', $end_date);
        $this->db->where('tbl_purchase_product_items.item_id', $item_id);
        $this->db->group_by('tbl_purchase_products.id');

        $queryMaterial = $this->db->get_compiled_select();

        $query = "(
            SELECT SUM(mt.total) as total
            FROM ($queryMaterial) as mt
        )";
        // print_arrays($query);

        $query = "(
            SELECT $material as total
        )";

        return $this->db->query($query)->row_array();
    }

    public function costingPurchaseInternal($item_id, $start_date, $end_date)
    {
        $this->db->select("
            GROUP_CONCAT(DISTINCT(tbl_purchase_products.productions_orders_details_id)) as pod_id
        ", false);
        $this->db->from('tbl_purchase_products');
        $this->db->join('tbl_purchase_product_items', 'tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id');
        $this->db->where('tbl_purchase_products.warehouseman_id >', 0);
        $this->db->where('DATE_FORMAT(tbl_purchase_products.date, "%Y-%m-%d") >=', $start_date);
        $this->db->where('DATE_FORMAT(tbl_purchase_products.date, "%Y-%m-%d") <=', $end_date);
        $this->db->where('tbl_purchase_product_items.item_id', $item_id);
        $purchaseProduct = $this->db->get()->row_array();


        if (!empty($purchaseProduct)) {
            $materialReturn = "(
                SELECT SUM(tbl_purchase_internal.grand_total)
                FROM tbl_purchase_internal
                WHERE tbl_purchase_internal.warehouseman_id > 0 AND tbl_purchase_internal.pod_id IN (".$purchaseProduct['pod_id'].")
            )";
        } else {
            $materialReturn = 0;
        }
        $query = "(
            SELECT $materialReturn as total
        )";

        return $this->db->query($query)->row_array();
    }

    public function insertCosting($data = [])
    {
        $this->db->insert('tbl_costing', $data);
        return $this->db->insert_id();
    }

    public function insertCostingItems($data = [])
    {
        $this->db->insert('tbl_costing_items', $data);
        return $this->db->insert_id();
    }

    public function updatePurchaseProductItems($purchase_product_id = [], $item_id, $price)
    {
        $this->db->where_in('tbl_purchase_product_items.purchase_product_id', $purchase_product_id);
        $this->db->where('tbl_purchase_product_items.item_id', $item_id);

        $this->db->set('tbl_purchase_product_items.price', $price, false);
        $this->db->set('tbl_purchase_product_items.amount', 'tbl_purchase_product_items.quantity*'.$price, false);
        // print_arrays($this->db->get_compiled_update('tbl_purchase_product_items'), FALSE);
        return $this->db->update('tbl_purchase_product_items');
    }

    public function calGrandTotalPurchaseProduct($purchase_product_id)
    {
        $this->db->select('SUM(tbl_purchase_product_items.amount) as grand_total', false);
        $this->db->from('tbl_purchase_product_items');
        $this->db->where('tbl_purchase_product_items.purchase_product_id', $purchase_product_id);
        $result = $this->db->get()->row_array();

        // print_arrays($purchase_product_id);
        $this->db->where('tbl_purchase_products.id', $purchase_product_id);
        return $this->db->update('tbl_purchase_products', ['grand_total' => $result['grand_total']]);
    }

    public function updatePriceWarehouseProduct($import_id, $product_id, $price)
    {
        $this->db->where_in('import_id', $import_id);
        $this->db->where('product_id', $product_id);
        $this->db->where('type_export', 18);
        $this->db->where('type_items', 'product');

        $this->db->set('price', $price, false);
        return $this->db->update('tblwarehouse_product');
    }

    public function rowCosting($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_costing');
        $this->db->where('tbl_costing.id', $id);
        return $this->db->get()->row_array();
    }

    public function getCostingItems($costing_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_costing_items');
        $this->db->where('tbl_costing_items.costing_id', $costing_id);
        return $this->db->get()->result_array();
    }
}