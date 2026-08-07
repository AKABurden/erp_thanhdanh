<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Manufactures_model extends App_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function insertProductionsPlan($data)
    {
        $this->db->insert('tbl_productions_plan', $data);
        return $this->db->insert_id();
    }

    public function updateProductionsPlan($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_productions_plan', $data);
    }

    public function insertProductionsPlanItems($data)
    {
        $this->db->insert('tbl_productions_plan_items', $data);
        return $this->db->insert_id();
    }

    public function updateProductionsPlanItems($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_productions_plan_items', $data);
    }

    public function insertProductionsPlanDetails($data)
    {
        $this->db->insert('tbl_productions_plan_details', $data);
        return $this->db->insert_id();
    }

    public function rowProductionsPlan($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_productions_plan');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function rowProductionsPlanByItem($id)
    {
        $this->db->select('tbl_productions_plan.reference_no');
        $this->db->from('tbl_productions_plan');
        $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.productions_plan_id = tbl_productions_plan.id', 'inner');
        $this->db->where('tbl_productions_plan_items.id', $id);
        return $this->db->get()->row_array();
    }

    public function rowProductionsPlanItems($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_productions_plan_items');
        $this->db->where('productions_plan_id', $id);
        return $this->db->get()->row_array();
    }

    public function rowProductionsPlanItemsById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_productions_plan_items');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function deleteProductionsPlan($id) {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_productions_plan');
    }

    public function checkExistProductionsPlanByReferenceNo($reference_no)
    {
        $this->db->from('tbl_productions_plan');
        $this->db->where('tbl_productions_plan.reference_no', $reference_no);
        return $this->db->get()->num_rows();
    }

    public function searchProductionsPlan($q, $limit = 50)
    {
        $this->db->select('tbl_productions_plan.id as id, tbl_productions_plan.reference_no as name, DATE_FORMAT(tbl_productions_plan.date, "%d/%m/%Y") as subtext', false);
        $this->db->from('tbl_productions_plan');
        if (!empty($q))
        {
            $this->db->group_start();
            $this->db->like('tbl_productions_plan.reference_no', $q);
            $this->db->group_end();
        }
        $this->db->where('tbl_productions_plan.status', 'approved');
        $this->db->order_by('tbl_productions_plan.date', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function searchProductionsPlanForOrders($q, $limit = 10)
    {
        $tbOrders = "(
            SELECT
                tbl_orders.id as id,
                CONCAT(tbl_orders.reference_no, '(', tblclients.company,')') as reference_no
            FROM tbl_orders
            INNER JOIN tblclients ON tblclients.userid = tbl_orders.customer_id
        ) tb_order";

        $tbBusines = "(
            SELECT
                tbl_business_plan.id as id,
                CONCAT(tbl_business_plan.reference_no) as reference_no
            FROM tbl_business_plan
        ) tb_business_plan";

        $planPattern = "(
            SELECT CONCAT('<span class=\"text-success\">HĐSX bán thành phẩm</span>', '</br>', COALESCE(pp.options1_reference_no, ''), '</br>', COALESCE(pp.options2_reference_no, ''))
            FROM tbl_productions_plan as pp
            WHERE pp.id = tbl_productions_plan.pattern_id
        )";
        
        // coalesce(GROUP_CONCAT(DISTINCT tb_order.reference_no), "") as reference_orders,
        $this->db->select('
            tbl_productions_plan.id as id, 
            CONCAT(tbl_productions_plan.reference_no, " (", DATE_FORMAT(tbl_productions_plan.date, "%d/%m/%Y"), ")") as text,
            DATE_FORMAT(tbl_productions_plan.date, "%d/%m/%Y") as subtext,
            coalesce(GROUP_CONCAT(DISTINCT tb_order.reference_no), "") as reference_orders1,
            IF(tbl_productions_plan.pattern_id > 0, '.$planPattern.', coalesce(GROUP_CONCAT(DISTINCT tb_order.reference_no), "")) as reference_orders,
            coalesce(GROUP_CONCAT(DISTINCT tb_business_plan.reference_no), "") as reference_business_plan,
        ', false);
        $this->db->from('tbl_productions_plan');
        $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.productions_plan_id = tbl_productions_plan.id');
        $this->db->join($tbOrders, 'tbl_productions_plan_items.type_object = "orders" AND tbl_productions_plan_items.object_id = tb_order.id', 'left');
        $this->db->join($tbBusines, 'tbl_productions_plan_items.type_object = "business_plan" AND tbl_productions_plan_items.object_id = tb_business_plan.id', 'left');
        // if (!empty($q))
        // {
        //     $this->db->group_start();
        //     $this->db->like('tbl_productions_plan.reference_no', $q);
        //     $this->db->group_end();
        // }
        // $this->db->where('tbl_productions_plan.status !=', 'un_approved');
        $this->db->where('tbl_productions_plan.status', 'capacity');
        // $this->db->where('tbl_productions_plan.productions_orders_id', 0);
        $this->db->where('tbl_productions_plan.productions_orders_id !=', 2);
        $this->db->order_by('tbl_productions_plan.date', 'DESC');

        if (!empty($q))
        {
            $this->db->having("(reference_orders LIKE '%$q%' OR reference_business_plan LIKE '%$q%' OR text LIKE '%$q%')");
        }

        // if (!empty($q))
        // {
        //     $this->db->having('(reference_orders LIKE "%'.$q.'%" OR reference_business_plan LIKE "%'.$q.'%")');
        // }

        $this->db->group_by('tbl_productions_plan.id');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function rowReferenceProductionsPlanByArrId($arr_id)
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=150000000000');
        $this->db->select('GROUP_CONCAT(tbl_productions_plan.reference_no SEPARATOR ",") as reference_no', false);
        $this->db->from('tbl_productions_plan');
        $this->db->where_in('tbl_productions_plan.id', $arr_id);
        return $this->db->get()->row_array();
    }

    public function rowReferenceOrdersByArrId($arr_id)
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=150000000000');
        $this->db->select('GROUP_CONCAT(tbl_orders.reference_no SEPARATOR ",") as reference_no', false);
        $this->db->from('tbl_orders');
        $this->db->where_in('tbl_orders.id', $arr_id);
        return $this->db->get()->row_array();
    }

    public function rowReferenceBusinessPlanByArrId($arr_id)
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=150000000000');
        $this->db->select('GROUP_CONCAT(tbl_business_plan.reference_no SEPARATOR ",") as reference_no', false);
        $this->db->from('tbl_business_plan');
        $this->db->where_in('tbl_business_plan.id', $arr_id);
        return $this->db->get()->row_array();
    }

    public function checkStatusProductionsPlan($arr_id) {
        $this->db->simple_query('SET SESSION group_concat_max_len=150000000000');
        $this->db->select('GROUP_CONCAT(tbl_productions_plan.reference_no SEPARATOR ",") as reference_no', false);
        $this->db->from('tbl_productions_plan');
        $this->db->where_in('tbl_productions_plan.id', $arr_id);
        $this->db->where('tbl_productions_plan.status !=', 'approved');
        return $this->db->get()->row_array();
    }

    public function insertProductionsCapacity($data)
    {
        $this->db->insert('tbl_productions_capacity', $data);
        return $this->db->insert_id();
    }

    public function insertProductionsCapacityItems($data)
    {
        $this->db->insert('tbl_productions_capacity_items', $data);
        return $this->db->insert_id();
    }

    public function insertBatchProductionsCapacityItemsSub($data)
    {
        $this->db->insert_batch('tbl_productions_capacity_items_sub', $data);
        return $this->db->insert_id();
    }

    public function insertProductionsCapacityItemsSub($data)
    {
        $this->db->insert('tbl_productions_capacity_items_sub', $data);
        return $this->db->insert_id();
    }

    public function rowProductionsCapacity($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_productions_capacity');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function deleteProductionsCapacity($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_productions_capacity');
    }

    public function getProductionsCapacityItems($productions_capacity_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_productions_capacity_items');
        $this->db->where('tbl_productions_capacity_items.productions_capacity_id', $productions_capacity_id);
        return $this->db->get()->result_array();
    }

    public function deleteProductionsCapacityItems($productions_capacity_id)
    {
        $this->db->where('productions_capacity_id', $productions_capacity_id);
        return $this->db->delete('tbl_productions_capacity_items');
    }

    public function deleteProductionsCapacityItemsSub($productions_capacity_items_id) {
        $this->db->where('productions_capacity_items_id', $productions_capacity_items_id);
        return $this->db->delete('tbl_productions_capacity_items_sub');
    }

    public function insertProductionsCapacityItemsStages($data)
    {
        $this->db->insert('tbl_productions_capacity_items_stages', $data);
        return $this->db->insert_id();
    }

    public function insertBatchProductionsCapacityItemsStages($data)
    {
        return $this->db->insert_batch('tbl_productions_capacity_items_stages', $data);
    }

    public function deleteProductionsCapacityItemsStages($productions_capacity_items_id) {
        $this->db->where('productions_capacity_items_id', $productions_capacity_items_id);
        return $this->db->delete('tbl_productions_capacity_items_stages');
    }

    public function getProductionsCapacityItemsStages($productions_capacity_items_id) {
        $this->db->select('*');
        $this->db->from('tbl_productions_capacity_items_stages');
        $this->db->where('tbl_productions_capacity_items_stages.productions_capacity_items_id', $productions_capacity_items_id);
        return $this->db->get()->result_array();
    }

    public function getProductionsCapacityItemsSub($productions_capacity_items_id) {
        $this->db->select('tbl_productions_capacity_items_sub.*, tblunits.unit');
        $this->db->from('tbl_productions_capacity_items_sub');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_productions_capacity_items_sub.unit_id', 'left');
        $this->db->where('tbl_productions_capacity_items_sub.productions_capacity_items_id', $productions_capacity_items_id);
        $this->db->where('tbl_productions_capacity_items_sub.type_sub !=', 'element');
        $this->db->where('tbl_productions_capacity_items_sub.parent_id', 0);
        return $this->db->get()->result_array();
    }

    public function updateProductionsCapacity($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_productions_capacity', $data);
    }

    public function getProductionsCapacityItemsForPurchase($productions_capacity_id)
    {
        $this->db->select('tbl_productions_capacity_items_sub.*');
        $this->db->from('tbl_productions_capacity_items');
        $this->db->join('tbl_productions_capacity_items_sub', 'tbl_productions_capacity_items_sub.productions_capacity_items_id = tbl_productions_capacity_items.id', 'inner');
        $this->db->where('tbl_productions_capacity_items.productions_capacity_id', $productions_capacity_id);
        $this->db->where('tbl_productions_capacity_items_sub.type_sub !=', 'element');
        return $this->db->get()->result_array();
    }

    public function insertBatchProductionsCapacityItemsPurchases($data)
    {
        return $this->db->insert_batch('tbl_productions_capacity_items_purchases', $data);
    }

    public function deleteProductionsCapacityPurchases($productions_capacity_id) {
        $this->db->where('productions_capacity_id', $productions_capacity_id);
        return $this->db->delete('tbl_productions_capacity_items_purchases');
    }

    public function getProductionsCapacityItemsPurchases($productions_capacity_id) {
        $this->db->select('tbl_productions_capacity_items_purchases.*, tblunits.unit');
        $this->db->from('tbl_productions_capacity_items_purchases');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_productions_capacity_items_purchases.unit_id', 'left');
        $this->db->where('tbl_productions_capacity_items_purchases.productions_capacity_id', $productions_capacity_id);
        $this->db->where('tbl_productions_capacity_items_purchases.quantity_purchase_sub >', 0);
        return $this->db->get()->result_array();
    }

    public function getProductionsCapacityItemsPurchasesMaterial($productions_capacity_id) {
        $this->db->select('tbl_productions_capacity_items_purchases.*, tblunits.unit');
        $this->db->from('tbl_productions_capacity_items_purchases');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_productions_capacity_items_purchases.unit_id', 'left');
        $this->db->where('tbl_productions_capacity_items_purchases.productions_capacity_id', $productions_capacity_id);
        $this->db->where('tbl_productions_capacity_items_purchases.quantity_purchase_sub >', 0);
        $this->db->where('tbl_productions_capacity_items_purchases.type_sub !=', 'semi_products');
        return $this->db->get()->result_array();
    }

    public function getProductionsPlanForProductionsOrders($production_plan_id = [])
    {
        $this->db->select('
            tbl_productions_plan_items.product_id,
            tbl_products.code,
            tbl_products.name,
            IF(tbl_products.images IS NOT NULL && tbl_products.images != "", CONCAT("uploads/products/", "", tbl_products.images, ""), "") as images,
            SUM(tbl_productions_plan_items.quantity_total_details) as total_quantity
            ', false);
        $this->db->from('tbl_productions_plan');
        $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.productions_plan_id = tbl_productions_plan.id');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_plan_items.product_id');
        $this->db->where('tbl_productions_plan.status !=', 'un_approved');
        $this->db->where_in('tbl_productions_plan.id', $production_plan_id);
        $this->db->group_by('tbl_productions_plan_items.product_id');
        // print_arrays($this->db->get_compiled_select(), FALSE);
        return $this->db->get()->result_array();
    }

    public function getProductionsPlanItemsForProductionsOrders($term, $limit, $production_plan_id = [])
    {
        $this->db->select('
            tbl_productions_plan_items.id as id,
            CONCAT(tbl_products.name, "(", tbl_products.code, ")", "(", tbl_productions_plan.reference_no, ")") as text,
            ', false);
        $this->db->from('tbl_productions_plan');
        $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.productions_plan_id = tbl_productions_plan.id');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_plan_items.product_id');
        // $this->db->where('tbl_productions_plan.status !=', 'un_approved');
        $this->db->where('tbl_productions_plan.status', 'capacity');
        $this->db->where_in('tbl_productions_plan.id', $production_plan_id);
        // $this->db->where('tbl_productions_plan_items.quantity_production_orders < tbl_productions_plan_items.quantity_total_details');
        $this->db->where('tbl_productions_plan_items.quantity_production_orders < (tbl_productions_plan_items.quantity_total_details + tbl_productions_plan_items.quantity_reserve)');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_products.name', $term);
            $this->db->or_like('tbl_products.code', $term);
            $this->db->or_like('tbl_productions_plan.reference_no', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function rowProductionsPlanItemsForProductionsOrders($production_plan_item_id)
    {
        $this->db->select('
            tbl_productions_plan.reference_no as reference_no,
            tbl_productions_plan.id as production_plan_id,
            tbl_productions_plan_items.id as production_plan_item_id,
            tbl_productions_plan_items.product_id,
            tbl_products.code,
            tbl_products.name,
            IF(tbl_products.images IS NOT NULL && tbl_products.images != "", CONCAT("uploads/products/", "", tbl_products.images, ""), "") as images,
            ((tbl_productions_plan_items.quantity_total_details + tbl_productions_plan_items.quantity_reserve) - tbl_productions_plan_items.quantity_production_orders) as total_quantity
            ', false);
        $this->db->from('tbl_productions_plan');
        $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.productions_plan_id = tbl_productions_plan.id');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_plan_items.product_id');
        // $this->db->where('tbl_productions_plan.status !=', 'un_approved');
        $this->db->where('tbl_productions_plan.status', 'capacity');
        $this->db->where('tbl_productions_plan_items.id', $production_plan_item_id);
        $this->db->where('tbl_productions_plan_items.quantity_production_orders < (tbl_productions_plan_items.quantity_total_details + tbl_productions_plan_items.quantity_reserve)');
        return $this->db->get()->row_array();
    }

    public function getProductionsPlanItemsForProductionsOrdersNew($productions_plan_id)
    {
        $this->db->select('
            tbl_productions_plan.reference_no as reference_no,
            tbl_productions_plan.id as production_plan_id,
            tbl_productions_plan_items.id as production_plan_item_id,
            tbl_productions_plan_items.product_id,
            tbl_products.code,
            tbl_products.name,
            IF(tbl_products.images IS NOT NULL && tbl_products.images != "", CONCAT("uploads/products/", "", tbl_products.images, ""), "") as images,
            ((tbl_productions_plan_items.quantity_total_details + tbl_productions_plan_items.quantity_reserve) - tbl_productions_plan_items.quantity_production_orders) as total_quantity
            ', false);
        $this->db->from('tbl_productions_plan');
        $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.productions_plan_id = tbl_productions_plan.id');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_plan_items.product_id');
        $this->db->where('tbl_productions_plan.status !=', 'un_approved');
        $this->db->where_in('tbl_productions_plan_items.productions_plan_id', $productions_plan_id);
        // $this->db->where('tbl_productions_plan_items.quantity_production_orders < tbl_productions_plan_items.quantity_total_details');
        $this->db->where('tbl_productions_plan_items.quantity_production_orders < (tbl_productions_plan_items.quantity_total_details + tbl_productions_plan_items.quantity_reserve)');
        return $this->db->get()->result_array();
    }

    public function insertProductionsOrders($data) {
        $this->db->insert('tbl_productions_orders', $data);
        return $this->db->insert_id();
    }

    public function insertProductionsOrdersItems($data) {
        $this->db->insert('tbl_productions_orders_items', $data);
        return $this->db->insert_id();
    }

    public function checkStatusProductionsPlanForOrders($arr_id) {
        $this->db->simple_query('SET SESSION group_concat_max_len=150000000000');

        $isOrderItems = "(
            SELECT tbl_order_items.id
            FROM tbl_order_items
            WHERE tbl_order_items.order_id = tbl_orders.id AND tbl_order_items.quantity_plan > tbl_order_items.quantity_productions_orders
        )";

        $this->db->select('GROUP_CONCAT(tbl_orders.reference_no SEPARATOR ",") as reference_no', false);
        $this->db->from('tbl_orders');
        $this->db->where_in('tbl_orders.id', $arr_id);
        $this->db->group_start();
        $this->db->where_not_in('tbl_orders.status', ['approved']);
        $this->db->or_where("NOT EXISTS ($isOrderItems)");
        $this->db->group_end();
        return $this->db->get()->row_array();
    }

    public function checkStatusProductionsPlanForBusinessOrders($arr_id) {
        $this->db->simple_query('SET SESSION group_concat_max_len=150000000000');

        $isBusinessPlan = "(
            SELECT tbl_business_plan_items.id
            FROM tbl_business_plan_items
            WHERE tbl_business_plan_items.business_plan_id = tbl_business_plan.id AND tbl_business_plan_items.quantity_plan > tbl_business_plan_items.quantity_productions_orders
        )";

        $this->db->select('GROUP_CONCAT(tbl_business_plan.reference_no SEPARATOR ",") as reference_no', false);
        $this->db->from('tbl_business_plan');
        $this->db->where_in('tbl_business_plan.id', $arr_id);
        $this->db->group_start();
        $this->db->where_in('tbl_business_plan.status', ['un_approved']);
        $this->db->or_where("NOT EXISTS ($isBusinessPlan)");
        $this->db->group_end();
        return $this->db->get()->row_array();
    }

    public function rowProductionsOrdersById($id, $where = null)
    {
        $this->db->select('*');
        $this->db->from('tbl_productions_orders');
        $this->db->where('tbl_productions_orders.id', $id);
        if (!empty($where)) {
            $this->db->where($where, false, false);
        }
        return $this->db->get()->row_array();
    }

    public function getProductionOrdersItemsAndProducts($production_orders_id)
    {
        $this->db->select('tbl_productions_orders_items.*, tbl_products.images, tbl_products.code as item_code, tbl_products.name as item_name, tblunits.unit as unit_name', false);
        $this->db->from('tbl_productions_orders_items');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id', 'left');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');
        $this->db->where('tbl_productions_orders_items.productions_orders_id', $production_orders_id);
        $this->db->where('tbl_productions_orders_items.type_items', 'products');
        return $this->db->get()->result_array();
    }

    public function checkExistProductionsOrdersByReferenceNo($reference_no)
    {
        $this->db->from('tbl_productions_orders');
        $this->db->where('tbl_productions_orders.reference_no', $reference_no);
        return $this->db->get()->num_rows();
    }

    public function deleteProductionsOrdersItems($productions_orders_id)
    {
        $this->db->where('tbl_productions_orders_items.productions_orders_id', $productions_orders_id);
        return $this->db->delete('tbl_productions_orders_items');
    }

    public function updateProductionsOrders($id, $data = [])
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_productions_orders', $data);
    }

    public function insertBatchProductionsOrdersItems($data = [])
    {
        return $this->db->insert_batch('tbl_productions_orders_items', $data);
    }

    public function deleteProductionsOrders($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_productions_orders');
    }

    public function updateProductionsPlanByOrders($productions_orders_id, $data = [])
    {
        $this->db->where('productions_orders_id', $productions_orders_id);
        return $this->db->update('tbl_productions_plan', $data);
    }

    public function getProductionsOrdersItems($productions_orders_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_productions_orders_items');
        $this->db->where('tbl_productions_orders_items.productions_orders_id', $productions_orders_id);
        return $this->db->get()->result_array();
    }

    public function insertProductionOrdersItemsSub($data = [])
    {
        $this->db->insert('tbl_productions_orders_items_sub', $data);
        return $this->db->insert_id();
    }

    public function insertBatchProductionOrdersItemsSub($data = [])
    {
        return $this->db->insert_batch('tbl_productions_orders_items_sub', $data);
    }

    public function insertBatchProductionOrdersItemsStages($data = [])
    {
        return $this->db->insert_batch('tbl_productions_orders_items_stages', $data);
    }

    public function deleteProductionsOrdersItemsSub($productions_orders_id)
    {
        $this->db->where('productions_orders_id', $productions_orders_id);
        return $this->db->delete('tbl_productions_orders_items_sub');
    }

    public function deleteProductionsOrdersItemsStages($productions_orders_id)
    {
        $this->db->where('productions_orders_id', $productions_orders_id);
        return $this->db->delete('tbl_productions_orders_items_stages');
    }

    public function getProductionsOrdersItemsSubTotalView($productions_orders_id)
    {
        $this->db->select('
            tbl_productions_orders_items_sub.type as type,
            tbl_productions_orders_items_sub.item_id as item_id,
            tbl_productions_orders_items_sub.unit_id as unit_id,
            tblunits.unit as unit,
            tbl_productions_orders_items_sub.item_code as item_code,
            tbl_productions_orders_items_sub.item_name as item_name,
            SUM(tbl_productions_orders_items_sub.quantity) as total_quantity
            ', false);
        $this->db->from('tbl_productions_orders_items_sub');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_productions_orders_items_sub.unit_id', 'left');
        $this->db->where('tbl_productions_orders_items_sub.productions_orders_id', $productions_orders_id);
        $this->db->where('tbl_productions_orders_items_sub.type !=', 'element');
        $this->db->where('tbl_productions_orders_items_sub.quantity >', 0);
        $this->db->group_by('tbl_productions_orders_items_sub.type, tbl_productions_orders_items_sub.item_id, tbl_productions_orders_items_sub.unit_id');
        return $this->db->get()->result_array();
    }

    public function getProductionsOrdersItemsStagesView($productions_orders_items_id)
    {
        $this->db->select('
            tbl_productions_orders_items_stages.*,
            tbl_stages.name as stage_name,
            tbl_machines.name as machine_name
            ', false);
        $this->db->from('tbl_productions_orders_items_stages');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id', 'left');
        $this->db->join('tbl_machines', 'tbl_machines.id = tbl_productions_orders_items_stages.machines', 'left');
        $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $productions_orders_items_id);
        $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_sub_id', 0);
        $this->db->order_by('tbl_productions_orders_items_stages.number ASC');
        return $this->db->get()->result_array();
    }

    public function getProductionsORdersItemsForCreated($productions_orders_id)
    {
        $this->db->select('tbl_productions_orders_items.*');
        $this->db->from('tbl_productions_orders_items');
        $this->db->where('tbl_productions_orders_items.productions_orders_id', $productions_orders_id);
        return $this->db->get()->result_array();
    }

    public function insertProductionsOrdersDetails($data)
    {
        $this->db->insert('tbl_productions_orders_details', $data);
        return $this->db->insert_id();
    }

    public function getProductionsOrdersItemsSubForDetail($productions_orders_items_id)
    {
        $this->db->select('tbl_productions_orders_items_sub.*');
        $this->db->from('tbl_productions_orders_items_sub');
        $this->db->where('tbl_productions_orders_items_sub.productions_orders_items_id', $productions_orders_items_id);
        $this->db->where('tbl_productions_orders_items_sub.type', 'semi_products');
        return $this->db->get()->result_array();
    }

    public function getProductionsCapacityItemsSubBySemiProduct($productions_capacity_id)
    {
        $this->db->select('
            tbl_productions_capacity_items_sub.id,
            tbl_productions_capacity_items_sub.id_sub,
            tbl_productions_capacity_items_sub.quantity_plan_sub,
            tbl_productions_capacity_items_sub.productions_capacity_items_id
            ');
        $this->db->from('tbl_productions_capacity_items');
        $this->db->join('tbl_productions_capacity_items_sub', 'tbl_productions_capacity_items.id = tbl_productions_capacity_items_sub.productions_capacity_items_id');
        $this->db->where('tbl_productions_capacity_items.productions_capacity_id', $productions_capacity_id);
        $this->db->where('tbl_productions_capacity_items_sub.type_sub', 'semi_products');
        return $this->db->get()->result_array();
    }

    /**
     * [totalProposedOfPurchases total proposed]
     * @return [type] [description]
     */
    public function totalProposedOfPurchases($product_id, $type)
    {
        $this->db->select('
            tblpurchases_items.type,
            tblpurchases_items.product_id,
            SUM(tblpurchases_items.quantity_net) as total_proposed
            ', false);
        $this->db->from('tblpurchases');
        $this->db->join('tblpurchases_items', 'tblpurchases.id = tblpurchases_items.purchases_id');
        $this->db->where('tblpurchases_items.product_id', $product_id);
        $this->db->where('tblpurchases_items.type', $type);
        $this->db->where('(
            SELECT count(tblpurchase_order.id)
            FROM tblpurchase_order
            WHERE tblpurchase_order.id_purchases = tblpurchases.id
        ) = 0');
        return $this->db->get()->row_array();
    }

    public function totalOrderingOfPurchaseOrderNotImportAndNotCancel($product_id, $type)
    {
        //lấy đơn hàng chưa hủy và chưa có nhập hàng và không tạo từ yêu cầu mua hàng từ kế hoạch hoạt định
        $this->db->select("
            SUM(tblpurchase_order_items.quantity_suppliers) as total_ordering
            ", false);
        $this->db->from('tblpurchase_order');
        $this->db->join('tblpurchase_order_items', 'tblpurchase_order.id = tblpurchase_order_items.id_purchase_order');
        $this->db->where('tblpurchase_order_items.product_id', $product_id);
        $this->db->where('tblpurchase_order_items.type', $type);
        $this->db->where('tblpurchase_order.cancel', 0);
        $this->db->where('tblpurchase_order.type_plan !=', 1);
        $this->db->where('(
            SELECT count(tblimport.id)
            FROM tblimport
            WHERE tblimport.id_order = tblpurchase_order.id
        ) = 0');
        return $this->db->get()->row_array();
    }

    public function totalOrderingOfPurchaseOrderImportAndNotCancel($product_id, $type)
    {
        //lấy đơn hàng chưa hủy và có nhập hàng một phần
        $this->db->select("
            GROUP_CONCAT(tblpurchase_order.id SEPARATOR ',') as order_id,
            SUM(tblpurchase_order_items.quantity_suppliers) as total_ordering_had_import
            ", false);
        $this->db->from('tblpurchase_order');
        $this->db->join('tblpurchase_order_items', 'tblpurchase_order.id = tblpurchase_order_items.id_purchase_order');
        $this->db->where('tblpurchase_order_items.product_id', $product_id);
        $this->db->where('tblpurchase_order_items.type', $type);
        $this->db->where('tblpurchase_order.cancel', 0);
        $this->db->where('tblpurchase_order.type_plan !=', 1);
        $this->db->where('(
            SELECT count(tblimport.id)
            FROM tblimport
            WHERE tblimport.id_order = tblpurchase_order.id
        ) > 0');
        // if ($product_id == 3) {
        //     print_arrays($this->db->get_compiled_select(), FALSE);
        // }
        return $this->db->get()->row_array();
    }

    public function totalImportOfImportOrderId($order_id = [], $product_id, $type)
    {
        $this->db->select("
            SUM(tblimport_items.quantity_net) as total_import
            ", false);
        $this->db->from('tblimport');
        $this->db->join('tblimport_items', 'tblimport.id = tblimport_items.id_import');
        $this->db->where('tblimport_items.type', $type);
        $this->db->where('tblimport_items.product_id', $product_id);
        $this->db->where_in('tblimport.id_order', $order_id);
        $this->db->where('tblimport.warehouseman_id >', 0);
        return $this->db->get()->row_array();
    }

    public function totalImportNotAgreeWarehouseNotPlan($order_id = [], $product_id, $type) {
        //lấy so luong nhập hàng chưa duyệt kho và không phải là kế hoạch sản xuất
        $this->db->select("
            SUM(tblimport_items.quantity_net) as total_import
            ", false);
        $this->db->from('tblimport');
        $this->db->join('tblimport_items', 'tblimport.id = tblimport_items.id_import');
        $this->db->where('tblimport_items.type', $type);
        $this->db->where('tblimport_items.product_id', $product_id);
        if (!empty($order_id))
        {
            $this->db->where_not_in('tblimport.id_order', $order_id);
        }
        $this->db->where('tblimport.warehouseman_id', 0);
        $this->db->where('tblimport.type_plan', 0);
        return $this->db->get()->row_array();
    }

    //total capacity
    public function totalCapacityNotPurchase($item_id, $type)
    {
        $this->db->select("
            SUM(tbl_productions_capacity_items_purchases.quantity_warehouse_reality) as total_warehouse_reality
            ", false);
        $this->db->from('tbl_productions_capacity');
        $this->db->join('tbl_productions_capacity_items_purchases', 'tbl_productions_capacity_items_purchases.productions_capacity_id = tbl_productions_capacity.id', 'inner');
        $this->db->where('tbl_productions_capacity_items_purchases.id_sub', $item_id);
        $this->db->where('tbl_productions_capacity_items_purchases.type_sub', $type);
        $this->db->where('tbl_productions_capacity.purchases_id', 0);
        return $this->db->get()->row_array();
    }

    //purchase
    public function totalProposedOfPurchasesNotOrder($product_id, $type)
    {
        $this->db->select('
            SUM(tblpurchases_items.quantity_net) as total_proposed
            ', false);
        $this->db->from('tblpurchases');
        $this->db->join('tblpurchases_items', 'tblpurchases.id = tblpurchases_items.purchases_id');
        $this->db->where('tblpurchases_items.product_id', $product_id);
        $this->db->where('tblpurchases_items.type', $type);
        $this->db->where('tblpurchases.id_order', 0);
        $this->db->where('tblpurchases.status !=', 4);
        $this->db->where('(
            SELECT count(tblpurchase_order.id)
            FROM tblpurchase_order
            WHERE tblpurchase_order.id_purchases = tblpurchases.id
        ) = 0');
        $this->db->where('(
            SELECT count(tbl_productions_capacity.id)
            FROM tbl_productions_capacity
            WHERE tbl_productions_capacity.purchases_id = tblpurchases.id
        ) = 0');
        return $this->db->get()->row_array();
    }

    public function totalProposedOfPurchasesApartOrder($product_id, $type)
    {
        $this->db->select("
            GROUP_CONCAT(tblpurchases.id SEPARATOR ',') as purchase_id,
            SUM(tblpurchases_items.quantity_net) as total_proposed
            ", false);
        $this->db->from('tblpurchases');
        $this->db->join('tblpurchases_items', 'tblpurchases.id = tblpurchases_items.purchases_id');
        $this->db->where('tblpurchases_items.product_id', $product_id);
        $this->db->where('tblpurchases_items.type', $type);
        $this->db->where('tblpurchases.id_order', 0);
        $this->db->where('tblpurchases.status !=', 4);
        $this->db->where('(
            SELECT count(tblpurchase_order.id)
            FROM tblpurchase_order
            WHERE tblpurchase_order.id_purchases = tblpurchases.id
        ) > 0');
        $this->db->where('(
            SELECT count(tbl_productions_capacity.id)
            FROM tbl_productions_capacity
            WHERE tbl_productions_capacity.purchases_id = tblpurchases.id
        ) = 0');
        // print_arrays($this->db->get_compiled_select(), FALSE);
        return $this->db->get()->row_array();
    }

    public function totalPurchaseOrderByPurchaseId($purchase_id = [], $product_id, $type)
    {
        $this->db->select('
            SUM(tblpurchase_order_items.quantity_suppliers) as total_ordering
            ', false);
        $this->db->from('tblpurchase_order');
        $this->db->join('tblpurchase_order_items', 'tblpurchase_order_items.id_purchase_order = tblpurchase_order.id');
        $this->db->where('tblpurchase_order_items.product_id', $product_id);
        $this->db->where('tblpurchase_order_items.type', $type);
        $this->db->where_in('tblpurchase_order.id_purchases', $purchase_id);
        return $this->db->get()->row_array();
    }
    //

    public function getProductionsOrdersItemsSubBySemiProduct($productions_orders_id)
    {
        $this->db->select('
            tbl_productions_orders_items_sub.id,
            tbl_productions_orders_items_sub.item_id,
            tbl_productions_orders_items_sub.type,
            tbl_productions_orders_items_sub.quantity,
            tbl_productions_orders_items_sub.productions_orders_items_id
            ', false);
        $this->db->from('tbl_productions_orders_items');
        $this->db->join('tbl_productions_orders_items_sub', 'tbl_productions_orders_items_sub.productions_orders_items_id = tbl_productions_orders_items.id');
        $this->db->where('tbl_productions_orders_items.type_items', 'products');
        $this->db->where('tbl_productions_orders_items_sub.type', 'semi_products');
        $this->db->where('tbl_productions_orders_items.productions_orders_id', $productions_orders_id);
        return $this->db->get()->result_array();
    }

    public function rowProductionsOrdersByDetail($id, $where = null)
    {
        $department = "(
            SELECT tbl_pod_departments.pod_id, GROUP_CONCAT(tbldepartments.name SEPARATOR ', ') as department_name
            FROM tbl_pod_departments
            INNER JOIN tbldepartments ON tbldepartments.departmentid = tbl_pod_departments.department_id
            GROUP BY tbl_pod_departments.pod_id
        ) as dp";

        $this->db
            ->select("
                tbl_productions_orders_details.id as id,
                tbl_productions_orders_details.productions_orders_item_id as productions_orders_item_id,
                tbl_productions_orders.reference_no as reference_no_order,
                tbl_productions_orders_details.reference_no as reference_no,
                tbl_productions_orders_details.deadline as deadline,
                dp.department_name as department_name,
                tbl_productions_orders_items.items_id as items_id,
                tbl_products.code as items_code,
                tbl_products.name as items_name,
                tbl_productions_orders_items.quantity as quantity,
                tbl_productions_orders_items.type_items as type_items,
                tblunits.unit as unit_name,
                tbl_productions_orders_details.quantity_finished as quantity_finished,
                0 as precent_finished,
                tbl_productions_orders_details.status as status,
                tbl_productions_orders_details.check_quality_id as check_quality_id,
                tbl_productions_orders_details.created_by as created_by,
                tbl_productions_orders_details.quantity_warehoused as quantity_warehoused,
                tbl_productions_orders_details.date_created as date_created,
                DATEDIFF(tbl_productions_orders_details.deadline, now()) as delay_progress,
                tbl_products.images as images,
                tbl_products.images_multiple as images_multiple,
                tbl_productions_orders_details.object_type as object_type,
                tbl_productions_orders_details.object_id as object_id,
                tbl_productions_orders_items.production_plan_item_id as production_plan_item_id,
                tblbranch.name as branch_name
                ", false)
            ->from('tbl_productions_orders_details')
            ->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id', 'left')
            ->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id', 'left')
            ->join('tblbranch', 'tblbranch.id = tbl_productions_orders.location_id', 'left')
            ->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id', 'left')
            ->join('tblunits', 'tblunits.unitid = tbl_products.conversion_unit', 'left')
            ->join('tbldepartments', 'tbldepartments.departmentid = tbl_productions_orders_details.departments', 'left')
            ->join($department, 'dp.pod_id = tbl_productions_orders_details.id', 'left');

        $this->db->where('tbl_productions_orders_details.id', $id);
        
        if (!empty($where)) {
            $this->db->where($where, false, false);
        }

        return $this->db->get()->row_array();
    }

    public function getProductionsOrdersItemsSubByDetail($productions_orders_items_id)
    {
        $this->db->select('
            tbl_productions_orders_items_sub.id as id,
            tbl_productions_orders_items_sub.type as type,
            tbl_productions_orders_items_sub.item_id as item_id,
            tbl_productions_orders_items_sub.item_code as item_code,
            tbl_productions_orders_items_sub.item_name as item_name,
            tblunits.unit as unit_name,
            tbl_productions_orders_items_sub.quantity as quantity,
            ', false);
        $this->db->from('tbl_productions_orders_items_sub');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_productions_orders_items_sub.unit_id', 'left');
        $this->db->where('tbl_productions_orders_items_sub.type', 'semi_products');
        $this->db->where('tbl_productions_orders_items_sub.productions_orders_items_id', $productions_orders_items_id);
        return $this->db->get()->result_array();
    }

    //
    public function deleteProductionsOdersDetail($productions_orders_id)
    {
        $this->db->where('productions_orders_id', $productions_orders_id);
        return $this->db->delete('tbl_productions_orders_details');
    }

    public function getProductionsOrdersItemsStagesOfProduct($productions_orders_items_id)
    {
        $this->db->select('
            tbl_productions_orders_items_stages.*,
            tbl_stages.name as stage_name,
            tbl_machines.name as machine_name
            ', false);
        $this->db->from('tbl_productions_orders_items_stages');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id', 'left');
        $this->db->join('tbl_machines', 'tbl_machines.id = tbl_productions_orders_items_stages.machines', 'left');
        $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $productions_orders_items_id);
        $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_sub_id', 0);
        return $this->db->get()->result_array();
    }

    public function getProductionsOrdersItemsStagesBySubId($productions_orders_items_id, $productions_orders_items_sub_id)
    {
        $this->db->select('
            tbl_productions_orders_items_stages.*,
            tbl_stages.name as stage_name,
            tbl_machines.name as machine_name
            ', false);
        $this->db->from('tbl_productions_orders_items_stages');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id', 'left');
        $this->db->join('tbl_machines', 'tbl_machines.id = tbl_productions_orders_items_stages.machines', 'left');
        $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $productions_orders_items_id);
        $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_sub_id', $productions_orders_items_sub_id);
        return $this->db->get()->result_array();
    }

    public function getMaterialExport($productions_orders_items_id)
    {
        $this->db
            ->select("
                tbl_productions_orders_items_sub.item_id,
                tbl_productions_orders_items_sub.unit_id,
                tbl_productions_orders_items_sub.item_code as item_code,
                tbl_productions_orders_items_sub.item_name as item_name,
                tblunits.unit as unit_name,
            ", false)
            ->from('tbl_productions_orders_items_sub')
            ->join('tblunits', 'tblunits.unitid = tbl_productions_orders_items_sub.unit_id', 'left');

        $this->db->group_by('tbl_productions_orders_items_sub.item_id, tbl_productions_orders_items_sub.unit_id');
        $this->db->where('tbl_productions_orders_items_sub.productions_orders_items_id', $productions_orders_items_id);
        return $this->db->get()->result_array();
    }

    public function getProductionsCapacityItemsPurchasesCal($productions_capacity_id)
    {
        $this->db->select('tbl_productions_capacity_items_purchases.*');
        $this->db->from('tbl_productions_capacity_items_purchases');
        $this->db->where('tbl_productions_capacity_items_purchases.productions_capacity_id', $productions_capacity_id);
        $this->db->where('(tbl_productions_capacity_items_purchases.type_sub = "materials" OR tbl_productions_capacity_items_purchases.type_sub = "semi_products_outside")');
        return $this->db->get()->result_array();
    }

    public function getProductionsCapacityItemsSubForPurchase($productions_capacity_id)
    {
        $this->db->select('
            tbl_productions_capacity_items_sub.type_sub,
            tbl_productions_capacity_items_sub.id_sub,
            SUM(tbl_productions_capacity_items_sub.quantity_plan_sub/tbl_productions_capacity_items_sub.quantity_exchange) as quantity_plan
            ', false);
        $this->db->from('tbl_productions_capacity_items');
        $this->db->join('tbl_productions_capacity_items_sub', 'tbl_productions_capacity_items_sub.productions_capacity_items_id = tbl_productions_capacity_items.id');
        $this->db->where('tbl_productions_capacity_items.productions_capacity_id', $productions_capacity_id);
        $this->db->where('(tbl_productions_capacity_items_sub.type_sub = "semi_products_outside" OR tbl_productions_capacity_items_sub.type_sub = "materials")');
        $this->db->group_by('tbl_productions_capacity_items_sub.type_sub, tbl_productions_capacity_items_sub.id_sub');
        return $this->db->get()->result_array();
    }

    public function updateBatchProductionsCapacityItemsPurchase($data = [])
    {
        return $this->db->update_batch('tbl_productions_capacity_items_purchases', $data, 'id');
    }

    public function getMaterialProductionsForExportSupplies($productions_orders_items_id)
    {
        $quantityExchange = "COALESCE((
            SELECT tbl_exchange_items.number_exchange
            FROM tbl_materials
            INNER JOIN tbl_exchange_items ON tbl_exchange_items.item_id = tbl_materials.id
            WHERE tbl_materials.id = tbl_productions_orders_items_sub.item_id AND tbl_productions_orders_items_sub.type = 'materials'
            LIMIT 1
        ), 1)";

        $quantityWarehouse = "(
            SELECT SUM(product_quantity * $quantityExchange)
            FROM tblwarehouse_items
            INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
            WHERE tblwarehouse_items.id_items = tbl_productions_orders_items_sub.item_id AND tblwarehouse_items.type_items =
            (CASE
                WHEN tbl_productions_orders_items_sub.type = 'materials' THEN 'nvl'
                WHEN tbl_productions_orders_items_sub.type = 'semi_products' THEN 'product'
                WHEN tbl_productions_orders_items_sub.type = 'semi_products_outside' THEN 'product'
                ELSE 'not'
            END) AND tblwarehouse.supplier_id = 0
        )";

        $this->db->select("
            tbl_productions_orders_items_sub.item_id as item_id,
            tbl_productions_orders_items_sub.unit_id as unit_id,
            tbl_productions_orders_items_sub.type as type_item,
            tbl_productions_orders_items_sub.unit_parent_id as unit_parent_id,
            tbl_productions_orders_items_sub.item_code as item_code,
            tbl_productions_orders_items_sub.item_name as item_name,
            tblunits.unit as unit_name,
            SUM(tbl_productions_orders_items_sub.quantity) as quantity,
            tbl_productions_orders_items_sub.quantity_exchange as quantity_exchange,
            COALESCE($quantityWarehouse, 0) as quantity_warehouse,
            GROUP_CONCAT(distinct(tbl_productions_orders_items_sub.leadtime) SEPARATOR '</br>') as leadtime,
            GROUP_CONCAT(distinct(tbl_stages.name) SEPARATOR '</br>') as stage,
            ", false);
        $this->db->from('tbl_productions_orders_items_sub');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_productions_orders_items_sub.unit_id', 'left');
        $this->db->where("(tbl_productions_orders_items_sub.type = 'materials' OR  tbl_productions_orders_items_sub.type = 'semi_products_outside' OR tbl_productions_orders_items_sub.type = 'semi_products')");
        $this->db->where('tbl_productions_orders_items_sub.productions_orders_items_id', $productions_orders_items_id);
        $this->db->group_by('tbl_productions_orders_items_sub.item_id, tbl_productions_orders_items_sub.unit_id, tbl_productions_orders_items_sub.type');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_sub.stage_item_id', 'left');

        $this->db->where('tbl_productions_orders_items_sub.quantity >', 0);
        $this->db->order_by('quantity_warehouse DESC');
        // print_arrays($this->db->get_compiled_select(), FALSE);
        return $this->db->get()->result_array();
    }

    public function insertSuggestExporting($data)
    {
        $this->db->insert('tbl_suggest_exporting', $data);
        return $this->db->insert_id();
    }

    public function insertSuggestExportingItems($data = [])
    {
        $this->db->insert('tbl_suggest_exporting_items', $data);
        return $this->db->insert_id();
    }

    public function insertBatchSuggestExportingItems($data = [])
    {
        return $this->db->insert_batch('tbl_suggest_exporting_items', $data);
    }

    public function rowSuggestExporting($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_suggest_exporting');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function getSuggestExportingItemsView($suggest_exporting_id)
    {
        $this->db->select('
            tbl_suggest_exporting_items.*, tblunits.unit as unit_name, unit_parent.unit as unit_name_parent, unit_warehouse.unit as unit_name_warehouse
        ');
        $this->db->from('tbl_suggest_exporting_items');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_suggest_exporting_items.unit_id', 'left');
        $this->db->join('tblunits unit_parent', 'unit_parent.unitid = tbl_suggest_exporting_items.unit_parent_id', 'left');
        $this->db->join('tblunits unit_warehouse', 'unit_warehouse.unitid = tbl_suggest_exporting_items.unit_warehouse', 'left');
        $this->db->where('tbl_suggest_exporting_items.suggest_exporting_id', $suggest_exporting_id);
        return $this->db->get()->result_array();
    }

    public function getSuggestExportingItems($suggest_exporting_id)
    {
        $this->db->select('tbl_suggest_exporting_items.*');
        $this->db->from('tbl_suggest_exporting_items');
        $this->db->where('tbl_suggest_exporting_items.suggest_exporting_id', $suggest_exporting_id);
        return $this->db->get()->result_array();
    }

    public function deleteSuggestExportingById($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_suggest_exporting');
    }

    public function deleteSuggestExportingItems($suggest_exporting_id)
    {
        $this->db->where('suggest_exporting_id', $suggest_exporting_id);
        return $this->db->delete('tbl_suggest_exporting_items');
    }

    public function deleteSuggestExportingItemsById($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_suggest_exporting_items');
    }

    public function updateSuggestExportingById($id, $data = [])
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_suggest_exporting', $data);
    }

    public function updateSuggestExportingItemsById($id, $data = [])
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_suggest_exporting_items', $data);
    }

    public function updateBatchSuggestExportingItemsById($data = [])
    {
        return $this->db->update_batch('tbl_suggest_exporting_items', $data, 'id');
    }

    public function getSuggestExportingItemsByNotArrId($arr_not_id, $suggest_exporting_id)
    {
        $this->db->select('id');
        $this->db->from('tbl_suggest_exporting_items');
        $this->db->where('tbl_suggest_exporting_items.suggest_exporting_id', $suggest_exporting_id);
        if (!empty($arr_not_id)) {
            $this->db->where_not_in('tbl_suggest_exporting_items.id', $arr_not_id);
        }
        return $this->db->get()->result_array();
    }

    public function checkCoditionDeleteDetail($id)
    {
        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbl_suggest_exporting', 'tbl_productions_orders_details.id = tbl_suggest_exporting.productions_orders_details_id');
        $this->db->where('tbl_productions_orders_details.productions_orders_id', $id);
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if ($q) {
            return lang('tnh_created_suggest_not_remove');
        }

        //condition purchase products
        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbl_purchase_products', 'tbl_purchase_products.productions_orders_details_id = tbl_productions_orders_details.id');
        $this->db->where('tbl_productions_orders_details.productions_orders_id', $id);
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if ($q) {
            return lang('tnh_created_purchase_product_not_remove');
        }

        //condition purchase internal
        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbl_purchase_internal', 'tbl_purchase_internal.pod_id = tbl_productions_orders_details.id');
        $this->db->where('tbl_productions_orders_details.productions_orders_id', $id);
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if ($q) {
            return lang('tnh_created_purchase_internal_not_remove');
        }

        //QC
        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbl_check_quality_items', 'tbl_check_quality_items.pod_id = tbl_productions_orders_details.id');
        $this->db->where('tbl_productions_orders_details.productions_orders_id', $id);
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if ($q) {
            return lang('Đã tạo QC không thể xóa');
        }

        $this->db->from('tbl_suggest_exporting');
        $this->db->where('tbl_suggest_exporting.po_id', $id);
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if ($q) {
            return lang('Đã xuất kho sản xuất không thể xóa');
        }

        return false;
    }

    public function rowProductionsOrdersDetais($id)
    {
        $quantity = "COALESCE((
            SELECT tbl_productions_orders_items.quantity
            FROM tbl_productions_orders_items
            WHERE tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id
        ), 0)";

        $quantityShiftWork = "COALESCE((
            SELECT SUM(tbltasks.quantity_shift_work)
            FROM tbltasks
            WHERE tbltasks.rel_type = 'order_production_details' AND tbltasks.rel_id = tbl_productions_orders_details.id
        ), 0)";

        $this->db->select("tbl_productions_orders_details.*, ($quantity - $quantityShiftWork) as quantity_rest", false);
        $this->db->from('tbl_productions_orders_details');
        $this->db->where('tbl_productions_orders_details.id', $id);
        return $this->db->get()->row_array();
    }

    public function checkExistSuggestExportingReferenceNo($reference_no)
    {
        $this->db->from('tbl_suggest_exporting');
        $this->db->where('tbl_suggest_exporting.reference_no', $reference_no);
        return $this->db->get()->num_rows();
    }

    public function getAllStaff()
    {
        $this->db->select('tblstaff.staffid, tblstaff.lastname, tblstaff.firstname,role, CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname', false);
        $this->db->from('tblstaff');
        return $this->db->get()->result_array();
    }

    public function insertUpdateInfoStage($data = [])
    {
        $this->db->insert('tbl_update_info_stage', $data);
        return $this->db->insert_id();
    }

    public function updateUpdateInfoStage($id, $data = [])
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_update_info_stage', $data);
    }

    public function deleteUpdateInfoStage($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_update_info_stage');
    }

    public function getUpdateInfoStage($productions_od_id, $productions_ois_id)
    {
        $this->db->select('tbl_update_info_stage.*, CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as employee_name', false);
        $this->db->from('tbl_update_info_stage');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_update_info_stage.employee_id', 'left');
        $this->db->where('tbl_update_info_stage.productions_od_id', $productions_od_id);
        $this->db->where('tbl_update_info_stage.productions_ois_id', $productions_ois_id);
        return $this->db->get()->result_array();
    }

    public function getMaxUpdateInfoStage($productions_od_id, $productions_ois_id)
    {
        $this->db->select('MAX(tbl_update_info_stage.id) as max_id', false);
        $this->db->from('tbl_update_info_stage');
        $this->db->where('tbl_update_info_stage.productions_od_id', $productions_od_id);
        $this->db->where('tbl_update_info_stage.productions_ois_id', $productions_ois_id);
        return $this->db->get()->row_array();
    }

    public function quantitySuggestExportingItem($productions_orders_details_id, $type_item, $item_id, $unit_id)
    {
        $this->db->select('
            tbl_suggest_exporting_items.type_item,
            tbl_suggest_exporting_items.item_id,
            tbl_suggest_exporting_items.unit_id,
            SUM(tbl_suggest_exporting_items.quantity_export) as quantity_exported
            ', false);
        $this->db->from('tbl_suggest_exporting');
        $this->db->join('tbl_suggest_exporting_items', 'tbl_suggest_exporting_items.suggest_exporting_id = tbl_suggest_exporting.id', 'inner');
        $this->db->where('tbl_suggest_exporting.productions_orders_details_id', $productions_orders_details_id);
        $this->db->where('tbl_suggest_exporting_items.type_item', $type_item);
        $this->db->where('tbl_suggest_exporting_items.item_id', $item_id);
        $this->db->where('tbl_suggest_exporting_items.unit_id', $unit_id);
        // $this->db->where_in('tbl_suggest_exporting.type_pattern_id', [1, 2]);
        $this->db->where_in('tbl_suggest_exporting.type', [1]);
        $this->db->group_by('tbl_suggest_exporting_items.type_item, tbl_suggest_exporting_items.item_id, tbl_suggest_exporting_items.unit_id');
        return $this->db->get()->row_array();
    }

    public function updateQuantityBadAndSuccess($id, $quantity_bad, $quantity_success, $time, $options)
    {
        $this->db->where('tbl_productions_orders_items_stages.id', $id);
        if ($options == 1) {
            $this->db->set('total_quantity_bad', 'total_quantity_bad+'.$quantity_bad, false);
            $this->db->set('total_quantity_success', $quantity_success);
            $this->db->set('total_time', 'total_time+'.$time, false);
        } else if ($options == 2) {
            $this->db->set('total_quantity_bad', 'total_quantity_bad-'.$quantity_bad, false);
            $this->db->set('total_quantity_success', 'total_quantity_success+'.$quantity_bad, false);
            $this->db->set('total_time', 'total_time-'.$time, false);
        }
        return $this->db->update('tbl_productions_orders_items_stages');
    }

    public function rowProductionsOrdersItemsStagesId($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_productions_orders_items_stages');
        $this->db->where('tbl_productions_orders_items_stages.id', $id);
        return $this->db->get()->row_array();
    }

    public function rowUpdateInfoStageById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_update_info_stage');
        $this->db->where('tbl_update_info_stage.id', $id);
        return $this->db->get()->row_array();
    }

    public function insertQuantityFinishedPre($data = [])
    {
        $this->db->insert('tbl_quantity_finished_pre', $data);
        return $this->db->insert_id();
    }

    public function updateQuantityFinishedPre($productions_orders_detail_id, $productions_orders_items_stage_id, $data = [])
    {
        $this->db->where('productions_orders_detail_id', $productions_orders_detail_id);
        $this->db->where('productions_orders_items_stage_id', $productions_orders_items_stage_id);
        return $this->db->update('tbl_quantity_finished_pre', $data);
    }

    public function checkQuantityFinishedPre($productions_orders_detail_id, $productions_orders_items_stage_id)
    {
        $this->db->from('tbl_quantity_finished_pre');
        $this->db->where('productions_orders_detail_id', $productions_orders_detail_id);
        $this->db->where('productions_orders_items_stage_id', $productions_orders_items_stage_id);
        return $this->db->get()->num_rows();
    }

    public function getUpdateInfoStageHandings($productions_od_id, $productions_ois_id)
    {
        $this->db->select('tbl_update_info_stage.*', false);
        $this->db->from('tbl_update_info_stage');
        $this->db->where('tbl_update_info_stage.productions_od_id', $productions_od_id);
        $this->db->where('tbl_update_info_stage.productions_ois_id', $productions_ois_id);
        return $this->db->get()->result_array();
    }

    public function updateBatchUpdateInfoStage($data = [])
    {
        return $this->db->update_batch('tbl_update_info_stage', $data, 'id');
    }

    public function updateProductionsOrdersItemsStages($id, $data = [])
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_productions_orders_items_stages', $data);
    }

    public function rowQuantityFinishedPre($productions_orders_detail_id, $productions_orders_items_stage_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_quantity_finished_pre');
        $this->db->where('tbl_quantity_finished_pre.productions_orders_detail_id', $productions_orders_detail_id);
        $this->db->where('tbl_quantity_finished_pre.productions_orders_items_stage_id', $productions_orders_items_stage_id);
        return $this->db->get()->row_array();
    }

    public function getProductionsOISByProductionOrdersId($productions_orders_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_productions_orders_items_stages');
        $this->db->where('tbl_productions_orders_items_stages.productions_orders_id', $productions_orders_id);
        return $this->db->get()->result_array();
    }

    public function getProductionsOrdersDetails($productions_orders_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_productions_orders_details');
        $this->db->where('tbl_productions_orders_details.productions_orders_id', $productions_orders_id);
        return $this->db->get()->result_array();
    }

    public function deleteUpdateInfoStageByProductionODId($productions_od_id)
    {
        $this->db->where('tbl_update_info_stage.productions_od_id', $productions_od_id);
        return $this->db->delete('tbl_update_info_stage');
    }

    public function deleteQuantityFinishedPreByProductionODId($productions_orders_detail_id)
    {
        $this->db->where('tbl_quantity_finished_pre.productions_orders_detail_id', $productions_orders_detail_id);
        return $this->db->delete('tbl_quantity_finished_pre');
    }

    public function updateProductionsOrdersDetailsById($id, $data = [])
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_productions_orders_details', $data);
    }

    public function rowProductionsOrdersDetaisForWarehousing($id)
    {
        $this->db->select('
            tbl_productions_orders_details.id as id,
            tbl_productions_orders_details.quantity_finished as quantity_finished,
            tbl_productions_orders_details.quantity_warehoused as quantity_warehoused,
            tbl_productions_orders_items.type_items as type_items,
            tbl_productions_orders_items.items_id as items_id,
            tbl_productions_orders_items.items_code as items_code,
            tbl_productions_orders_items.items_name as items_name,
            tbl_productions_orders_details.status as status,
            tbl_productions_orders_details.check_quality_id as check_quality_id
            ');
        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
        $this->db->where('tbl_productions_orders_details.id', $id);
        // print_arrays($this->db->get_compiled_select(), FALSE);
        return $this->db->get()->row_array();
    }

    public function insertWarehousingProduct($data = [])
    {
        $this->db->insert('tbl_warehousing_product', $data);
        return $this->db->insert_id();
    }

    public function insertWarehousingProductItems($data = [])
    {
        $this->db->insert('tbl_warehousing_product_items', $data);
        return $this->db->insert_id();
    }

    public function getWarehousingProductByPOD($productions_orders_details_id)
    {
        $this->db->select('
            tbl_warehousing_product.date as date,
            tbl_warehousing_product.reference_no as reference_no,
            tbl_warehousing_product_items.item_code as item_code,
            tbl_warehousing_product_items.item_name as item_name,
            tbl_warehousing_product_items.quantity as quantity,
            tbllocaltion_warehouses.name as location_name,
            tblwarehouse.name as warehouse_name
            ');
        $this->db->from('tbl_warehousing_product');
        $this->db->join('tbl_warehousing_product_items', 'tbl_warehousing_product_items.warehousing_product_id = tbl_warehousing_product.id');
        $this->db->join('tblwarehouse', 'tblwarehouse.id = tbl_warehousing_product.warehouse_id', 'left');
        $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tbl_warehousing_product_items.location_id', 'left');
        $this->db->where('tbl_warehousing_product.productions_orders_details_id', $productions_orders_details_id);
        return $this->db->get()->result_array();
    }

    public function getPurchasesProductByPOD($productions_orders_details_id)
    {
        $this->db->select('
            tbl_purchase_products.date as date,
            tbl_purchase_products.reference_no as reference_no,
            tbl_purchase_product_items.item_code as item_code,
            tbl_purchase_product_items.item_name as item_name,
            tbl_purchase_product_items.quantity as quantity,
            tbllocaltion_warehouses.name as location_name,
            tblwarehouse.name as warehouse_name
            ');
        $this->db->from('tbl_purchase_products');
        $this->db->join('tbl_purchase_product_items', 'tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id');
        $this->db->join('tblwarehouse', 'tblwarehouse.id = tbl_purchase_products.warehouse_id', 'left');
        $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tbl_purchase_product_items.location_id', 'left');
        $this->db->where('tbl_purchase_products.productions_orders_details_id', $productions_orders_details_id);
        $this->db->where('tbl_purchase_products.final_stage', 1);
        return $this->db->get()->result_array();
    }

    public function insertPODEmployees($data = [])
    {
        $this->db->insert('tbl_pod_employees', $data);
        return $this->db->insert_id();
    }

    public function insertBatchPODEmployees($data = [])
    {
        $this->db->insert_batch('tbl_pod_employees', $data);
        return $this->db->insert_id();
    }

    public function deletePODEmployeesByPODId($pod_id)
    {
        $this->db->where('tbl_pod_employees.productions_orders_details_id', $pod_id);
        return $this->db->delete('tbl_pod_employees');
    }

    public function getPODEmployeesByPODId($pod_id)
    {
        $this->db->select('tblstaff.*, tbl_pod_employees.productions_orders_details_id, tbl_pod_employees.employee_id');
        $this->db->from('tbl_pod_employees');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_pod_employees.employee_id', 'inner');
        $this->db->where('tbl_pod_employees.productions_orders_details_id', $pod_id);
        return $this->db->get()->result_array();
    }

    public function getPODEmployees($pod_id)
    {
        $this->db->select('tbl_pod_employees.*');
        $this->db->from('tbl_pod_employees');
        $this->db->where('tbl_pod_employees.productions_orders_details_id', $pod_id);
        return $this->db->get()->result_array();
    }

    public function getDataForTaskAndEmail($productions_orders_id)
    {
        $this->db->select('
            tbl_productions_orders_details.id as id,
            tbl_productions_orders_details.reference_no as reference_no,
            tbl_productions_orders_details.deadline as deadline,
            tbl_productions_orders_details.date_created as date_created,
            tbl_productions_orders_items.items_code as items_code,
            tbl_productions_orders_items.items_name as items_name,
            tbl_productions_orders_items.quantity as quantity,
        ', false);
        $this->db->from('tbl_productions_orders_details');
        // $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.productions_orders_id = tbl_productions_orders_details.productions_orders_id');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
        $this->db->where('tbl_productions_orders_details.productions_orders_id', $productions_orders_id);
        return $this->db->get()->result_array();
    }

    public function getEmailPOD($productions_orders_id)
    {
        $this->db->select('
            tblstaff.email, tbltasks.name, tbltasks.description, tblstaff.id_zalo
        ');
        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbltasks', 'tbltasks.rel_id = tbl_productions_orders_details.id AND tbltasks.rel_type = "order_production_details"');
        $this->db->join('tbltask_assigned', 'tbltask_assigned.taskid = tbltasks.id');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbltask_assigned.staffid');
        $this->db->where('tbl_productions_orders_details.productions_orders_id', $productions_orders_id);
        return $this->db->get()->result_array();
    }

    public function searchProductionsOrders($q, $limit = 50)
    {
        $branch_staff = get_array_branch_staff();
        $is_admin = is_admin();

        $this->db->simple_query('SET SESSION group_concat_max_len=150000000');
        $items = "(
            SELECT
                GROUP_CONCAT(CONCAT(tbl_productions_orders_items.items_name, '(', tbl_productions_orders_items.items_code, ')') SEPARATOR '|||')
            FROM tbl_productions_orders_items
            WHERE tbl_productions_orders_items.productions_orders_id = tbl_productions_orders.id
        )";

        $this->db->select("tbl_productions_orders.id as id, tbl_productions_orders.reference_no as text, $items as items", false);
        $this->db->from('tbl_productions_orders');
        if (!empty($q))
        {
            $this->db->group_start();
            $this->db->like('tbl_productions_orders.reference_no', $q);
            $this->db->or_like('tbl_productions_orders.reference_no', $q);
            $this->db->group_end();
        }

        if (!$is_admin) {
            if (empty($branch_staff)) $branch_staff = [0];
            $this->db->where('tbl_productions_orders.location_id IN ('.implode(',', $branch_staff).')', false, false);
        }

        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function updateQuantityWarehoused($id, $quantity_warehoused, $options = 0)
    {
        $this->db->where('tbl_productions_orders_details.id', $id);
        if ($options == 0) {
            //plus
            $this->db->set('quantity_warehoused', 'quantity_warehoused+'.$quantity_warehoused, false);
        } else if ($options == 1) {
            //minus
            $this->db->set('quantity_warehoused', 'quantity_warehoused-'.$quantity_warehoused, false);
        }
        $rs = $this->db->update('tbl_productions_orders_details');
        if ($rs) {
            calCostingFinishedProduct($id);
            return true;
        } 

        return false;
    }

    public function getViewProductionsCapacity($productions_capacity_id)
    {
        $this->db->select("
            0 as number_records,
            tbl_productions_capacity_items.items_code as code,
            tbl_productions_capacity_items.items_name as name,
            tblunits.unit as unit,
            tbl_productions_capacity_items.quantity_minimum as quantity_minimum,
            COALESCE(tbl_productions_capacity_items.quantity_warehouse, 0) as quantity_warehouses,
            COALESCE(tbl_productions_capacity_items.quantity_plan, 0) as quantity_use,
            COALESCE(tbl_productions_capacity_items.quantity_purchase, 0) as quantity_productions,
            tbl_productions_capacity_items.number_labor as number_labor,
            tbl_productions_capacity_items.versions_bom as sub,
            tbl_productions_capacity_items.versions_stages as st,
            tbl_productions_capacity_items.id as id,
            ", false)
        ->from('tbl_productions_capacity_items')
        ->join('tbl_products', 'tbl_products.id = tbl_productions_capacity_items.items_id', 'left')
        ->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');

        $this->db->where('tbl_productions_capacity_items.productions_capacity_id', $productions_capacity_id);
        return $this->db->get()->result_array();
    }

    public function getViewProductionsCapacityStatistical($productions_capacity_id)
    {
        $this->db->select("
            0 as number_records,
            tbl_productions_capacity_items_purchases.code_sub as code,
            tbl_productions_capacity_items_purchases.name_sub as name,
            tbl_productions_capacity_items_purchases.type_sub as type,
            tblunits.unit as unit_name,
            tbl_productions_capacity_items_purchases.quantity_mini_exchange as quantity_minimum,
            tbl_productions_capacity_items_purchases.quantity_warehouse_reality as quantity_warehouse,
            COALESCE(tbl_productions_capacity_items_purchases.quantity_plan_sub, 0) as quantity_plan,
            COALESCE(tbl_productions_capacity_items_purchases.quantity_purchase_sub, 0) as quantity_purchase,
            ", false)
        ->from('tbl_productions_capacity_items_purchases')
        ->join('tblunits', 'tblunits.unitid = tbl_productions_capacity_items_purchases.unit_id', 'left');

        $this->db->where('tbl_productions_capacity_items_purchases.productions_capacity_id', $productions_capacity_id);
        $this->db->where('tbl_productions_capacity_items_purchases.quantity_purchase_sub >', 0);
        return $this->db->get()->result_array();
    }

    public function searchOrdersByPlan($q, $limit = 50, $dateStart, $dateEnd)
    {
        $sumTotalQuantityDate = "COALESCE((
            SELECT SUM(tbl_order_item_shippings.quantity_shipping)
            FROM tbl_order_item_shippings
            WHERE tbl_order_item_shippings.order_item_id = tbl_order_items.id AND DATE_FORMAT(tbl_order_item_shippings.date_shipping, '%Y-%m-%d') >= '$dateStart' AND DATE_FORMAT(tbl_order_item_shippings.date_shipping, '%Y-%m-%d') <= '$dateEnd'
        ), 0)";

        $quantityPlan = "(
            SELECT COUNT(*)
            FROM tbl_order_items
            WHERE ($sumTotalQuantityDate > tbl_order_items.quantity_plan) AND tbl_order_items.order_id = tbl_orders.id AND tbl_order_items.type_item = 'products'
        )";

        $where = "(
            SELECT COUNT(*)
            FROM tbl_order_items
            INNER JOIN tbl_order_item_shippings ON tbl_order_item_shippings.order_item_id = tbl_order_items.id
            WHERE tbl_order_items.order_id = tbl_orders.id AND DATE_FORMAT(tbl_order_item_shippings.date_shipping, '%Y-%m-%d') >= '$dateStart' AND DATE_FORMAT(tbl_order_item_shippings.date_shipping, '%Y-%m-%d') <= '$dateEnd' AND tbl_order_items.type_item = 'products' AND tbl_order_item_shippings.quantity_shipping > tbl_order_item_shippings.quantity_plan_item
        )";

        $this->db->select('tbl_orders.id as id, tbl_orders.reference_no as text', false);
        $this->db->from('tbl_orders');
        if (!empty($q))
        {
            $this->db->group_start();
            $this->db->like('tbl_orders.reference_no', $q);
            $this->db->group_end();
        }
        $this->db->where('tbl_orders.status !=', 'un_approved');
        $this->db->where('tbl_orders.productions_plan_id', 0);
        $this->db->where('tbl_orders.is_cancel', 0);
        $this->db->where("$where > 0");
        // $this->db->where("($quantityPlan > 0)");
        $this->db->order_by('tbl_orders.date', 'DESC');
        // print_arrays($this->db->get_compiled_select());
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function searchBusinessByPlan($q, $limit = 50, $dateStart, $dateEnd)
    {
        $where = "(
            SELECT COUNT(*)
            FROM tbl_business_plan_items
            INNER JOIN tbl_business_plan_items_date ON tbl_business_plan_items_date.business_plan_items_id = tbl_business_plan_items.id
            WHERE tbl_business_plan_items.business_plan_id = tbl_business_plan.id AND DATE_FORMAT(tbl_business_plan_items_date.date, '%Y-%m-%d') >= '$dateStart' AND DATE_FORMAT(tbl_business_plan_items_date.date, '%Y-%m-%d') <= '$dateEnd' AND tbl_business_plan_items.type_items = 'products' AND tbl_business_plan_items_date.quantity > tbl_business_plan_items_date.quantity_plan_item
        )";

        $this->db->select('tbl_business_plan.id as id, CONCAT(tbl_business_plan.reference_no, " (", tbl_business_plan.plan_name, ")") as text', false);
        $this->db->from('tbl_business_plan');
        if (!empty($q))
        {
            $this->db->group_start();
            $this->db->like('tbl_business_plan.reference_no', $q);
            $this->db->group_end();
        }
        $this->db->where('tbl_business_plan.status !=', 'un_approved');
        $this->db->where('tbl_business_plan.productions_plan_id', 0);
        $this->db->where("$where > 0");
        $this->db->order_by('tbl_business_plan.date', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function insertProductionsPlanOrders($data = [])
    {
        $this->db->insert('tbl_productions_plan_orders', $data);
        return $this->db->insert_id();
    }

    public function insertBatchProductionsPlanOrders($data = [])
    {
        return $this->db->insert_batch('tbl_productions_plan_orders', $data);
    }

    public function deleteProductionsPlanOrders($productions_order_id)
    {
        $this->db->where('productions_order_id', $productions_order_id);
        return $this->db->delete('tbl_productions_plan_orders');
    }

    public function updateQuantityProductionOrders($production_plan_item_id, $quantity, $options = 0, $type = "orders")
    {
        // $this->db->where('id', $production_plan_item_id);
        // if ($options == 2) {
        //     $this->db->set('tbl_productions_plan_items.quantity_production_orders', 'tbl_productions_plan_items.quantity_production_orders-'.$quantity, false);
        // } else if ($options == 1) {
        //     $this->db->set('tbl_productions_plan_items.quantity_production_orders', 'tbl_productions_plan_items.quantity_production_orders+'.$quantity, false);
        // }
        // return $this->db->update('tbl_productions_plan_items');
        
        if ($type == "orders") {
            $this->db->where('id', $production_plan_item_id);
            if ($options == 2) {
                $this->db->set('tbl_order_items.quantity_productions_orders', 'tbl_order_items.quantity_productions_orders-'.$quantity, false);
            } else if ($options == 1) {
                $this->db->set('tbl_order_items.quantity_productions_orders', 'tbl_order_items.quantity_productions_orders+'.$quantity, false);
            }
            return $this->db->update('tbl_order_items');
        } else if ($type == "business_plan") {
            $this->db->where('id', $production_plan_item_id);
            if ($options == 2) {
                $this->db->set('tbl_business_plan_items.quantity_productions_orders', 'tbl_business_plan_items.quantity_productions_orders-'.$quantity, false);
            } else if ($options == 1) {
                $this->db->set('tbl_business_plan_items.quantity_productions_orders', 'tbl_business_plan_items.quantity_productions_orders+'.$quantity, false);
            }
            return $this->db->update('tbl_business_plan_items');
        }

    }

    public function checkPlanFullByOrder($productions_plan_id, $type = "orders")
    {
        if ($type == "orders") {
            $this->db->from('tbl_order_items');
            $this->db->where('tbl_order_items.order_id', $productions_plan_id);
            $this->db->where('tbl_order_items.quantity_plan > tbl_order_items.quantity_productions_orders');
            $this->db->limit(1);
        } else if ($type == "business_plan") {
            $this->db->from('tbl_business_plan_items');
            $this->db->where('tbl_business_plan_items.business_plan_id', $productions_plan_id);
            $this->db->where('tbl_business_plan_items.quantity_plan > tbl_business_plan_items.quantity_productions_orders');
            $this->db->limit(1);
        }
        return $this->db->get()->num_rows();
    }

    public function checkPlanQtyByOrder($productions_plan_id, $type = "orders")
    {
        // $this->db->from('tbl_productions_plan_items');
        // $this->db->where('tbl_productions_plan_items.productions_plan_id', $productions_plan_id);
        // $this->db->where('tbl_productions_plan_items.quantity_production_orders >', 0);
        // $this->db->limit(1);

        if ($type == "orders") {
            $this->db->from('tbl_order_items');
            $this->db->where('tbl_order_items.order_id', $productions_plan_id);
            $this->db->where('tbl_order_items.quantity_productions_orders >', 0);
            $this->db->limit(1);
        } else if ($type == "business_plan") {
            $this->db->from('tbl_business_plan_items');
            $this->db->where('tbl_business_plan_items.business_plan_id', $productions_plan_id);
            $this->db->where('tbl_business_plan_items.quantity_productions_orders >', 0);
            $this->db->limit(1);
        }
        return $this->db->get()->num_rows();
    }

    public function rowProductionsOrdersItemsAndPlanById($id)
    {
        $this->db->select('tbl_productions_plan_items.type_object, tbl_productions_plan_items.object_id');
        $this->db->from('tbl_productions_orders_items');
        $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.id = tbl_productions_orders_items.production_plan_item_id', 'inner');
        $this->db->where('tbl_productions_orders_items.id', $id);
        return $this->db->get()->row_array();
    }

    public function insertPODDepartments($data)
    {
        $this->db->insert('tbl_pod_departments', $data);
        return $this->db->insert_id();
    }

    public function deletePODDepartments($pod_id)
    {
        $this->db->where('tbl_pod_departments.pod_id', $pod_id);
        return $this->db->delete('tbl_pod_departments');
    }

    public function rowSuggestExportingItem($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_suggest_exporting_items');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function rowWarehouseItems($id_items, $warehouse_id, $location_id, $type_items)
    {
        $this->db->select('*');
        $this->db->from('tblwarehouse_items');
        $this->db->where('tblwarehouse_items.id_items', $id_items);
        $this->db->where('tblwarehouse_items.warehouse_id', $warehouse_id);
        $this->db->where('tblwarehouse_items.localtion', $location_id);
        $this->db->where('tblwarehouse_items.type_items', $type_items);
        return $this->db->get()->row_array();
    }

    public function getSuggestExportingItemsConvertStock($suggest_exporting_id)
    {
        $this->db->select('tbl_suggest_exporting_items.*, tblunits.unit as unit_name, unit_parent.unit as unit_name_parent');
        $this->db->from('tbl_suggest_exporting_items');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_suggest_exporting_items.unit_id', 'left');
        $this->db->join('tblunits unit_parent', 'unit_parent.unitid = tbl_suggest_exporting_items.unit_parent_id', 'left');
        $this->db->where('tbl_suggest_exporting_items.suggest_exporting_id', $suggest_exporting_id);
        $this->db->where('(tbl_suggest_exporting_items.quantity_export - tbl_suggest_exporting_items.quantity_convert_stock) > 0');
        return $this->db->get()->result_array();
    }

    public function updateSetSuggestExportingItem($id, $quantity_convert_stock, $options = 0)
    {
        $this->db->where('tbl_suggest_exporting_items.id', $id);
        if ($options === 0) {
            //plus
            $this->db->set('tbl_suggest_exporting_items.quantity_convert_stock', 'tbl_suggest_exporting_items.quantity_convert_stock+'.$quantity_convert_stock, false);
        } else if ($options === 1) {
            //minus
            $this->db->set('tbl_suggest_exporting_items.quantity_convert_stock', 'tbl_suggest_exporting_items.quantity_convert_stock-'.$quantity_convert_stock, false);
        }
        return $this->db->update('tbl_suggest_exporting_items');
    }

    public function checkFinishedSuggestExportingItems($suggest_exporting_id)
    {
        $this->db->from('tbl_suggest_exporting_items');
        $this->db->where('suggest_exporting_id', $suggest_exporting_id);
        $this->db->where('quantity_convert_stock < quantity_export');
        return $this->db->get()->num_rows();
    }

    public function checkExiSuggestExportingItems($suggest_exporting_id)
    {
        $this->db->from('tbl_suggest_exporting_items');
        $this->db->where('suggest_exporting_id', $suggest_exporting_id);
        $this->db->where('quantity_convert_stock >', 0);
        return $this->db->get()->num_rows();
    }

    public function updateSetSuggestExporting($id, $quantityStock, $statusConvertStock, $options = 0)
    {
        $this->db->where('tbl_suggest_exporting.id', $id);
        if ($options === 0) {
            //plus
            $this->db->set('tbl_suggest_exporting.total_quantity_stock', 'tbl_suggest_exporting.total_quantity_stock+'.$quantityStock, false);
        } else if ($options === 1) {
            //minus
            $this->db->set('tbl_suggest_exporting.total_quantity_stock', 'tbl_suggest_exporting.total_quantity_stock-'.$quantityStock, false);
        }
        $this->db->set('tbl_suggest_exporting.status_convert_stock', $statusConvertStock);
        return $this->db->update("tbl_suggest_exporting");
    }

    public function updateSetPOD($id, $quantity, $options = 0)
    {
        $this->db->where('id', $id);
        if ($options === 0) {
            $this->db->set('quantity_finished', 'quantity_finished+'.$quantity, false);
        }
        if ($options === 1) {
            $this->db->set('quantity_finished', 'quantity_finished-'.$quantity, false);
        }
        return $this->db->update('tbl_productions_orders_details');
    }

    public function rowProductionsORdersItemsById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_productions_orders_items');
        $this->db->where('tbl_productions_orders_items.id', $id);
        return $this->db->get()->row_array();
    }

    public function getQuantityExportMaterial($type, $item_id, $production_orders_id)
    {
        $this->db->select("
            SUM(tbl_suggest_exporting_items.quantity_export) as quantity_export
        ");
        $this->db->from('tbl_suggest_exporting');
        $this->db->join('tbl_suggest_exporting_items', 'tbl_suggest_exporting_items.suggest_exporting_id = tbl_suggest_exporting.id');
        $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.id = tbl_suggest_exporting.productions_orders_details_id');
        $this->db->where('tbl_suggest_exporting.reference_stock IS NOT NULL');
        $this->db->where('tbl_suggest_exporting.status_stock =', 'approved');
        $this->db->where('tbl_suggest_exporting.warehouseman_id >', 0);
        $this->db->where('(tbl_suggest_exporting.type_pattern_id IN (1, 2) OR tbl_suggest_exporting.type IN (1, 2))');
        $this->db->where('tbl_suggest_exporting_items.type_item', $type);
        $this->db->where('tbl_suggest_exporting_items.item_id', $item_id);
        $this->db->where('tbl_productions_orders_details.productions_orders_id', $production_orders_id);
        return $this->db->get()->row_array();
    }

    public function rowProductionsOrdersDetailsByPOI($productions_orders_item_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_productions_orders_details');
        $this->db->where('tbl_productions_orders_details.productions_orders_item_id', $productions_orders_item_id);
        return $this->db->get()->row_array();
    }

    public function rowProductionsOrdersDetailsByID($id)
    {
        $this->db->select('tbl_productions_orders_details.*, tbl_productions_orders_items.items_code, tbl_productions_orders_items.items_name, tbl_productions_orders_items.quantity', false);
        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
        $this->db->where('tbl_productions_orders_details.id', $id);
        return $this->db->get()->row_array();
    }

    public function countNotApprovePO()
    {
        $this->db->from('tbl_productions_orders');
        $this->db->where('tbl_productions_orders.status', 'un_approved');
        return $this->db->get()->num_rows();
    }

    public function countNotApproveSug()
    {
        $this->db->from('tbl_suggest_exporting');
        $this->db->where('tbl_suggest_exporting.status', 'un_approved');
        $this->db->where('tbl_suggest_exporting.reference_stock IS NULL');
        return $this->db->get()->num_rows();
    }

    public function getProductionsCapacityItemsSubForPurchaseTypeReplace($productions_capacity_id, $type_replace)
    {
        $this->db->select('
            tbl_productions_capacity_items_sub.type_sub,
            tbl_productions_capacity_items_sub.id_sub,
            SUM(tbl_productions_capacity_items_sub.quantity_plan_sub/tbl_productions_capacity_items_sub.quantity_exchange) as quantity_plan
            ', false);
        $this->db->from('tbl_productions_capacity_items');
        $this->db->join('tbl_productions_capacity_items_sub', 'tbl_productions_capacity_items_sub.productions_capacity_items_id = tbl_productions_capacity_items.id');
        $this->db->where('tbl_productions_capacity_items.productions_capacity_id', $productions_capacity_id);
        $this->db->where('(tbl_productions_capacity_items_sub.type_sub = "semi_products_outside" OR tbl_productions_capacity_items_sub.type_sub = "materials")');
        $this->db->where('tbl_productions_capacity_items_sub.type_replace', $type_replace);
        $this->db->group_by('tbl_productions_capacity_items_sub.type_sub, tbl_productions_capacity_items_sub.id_sub');
        return $this->db->get()->result_array();
    }

    public function getProductionsCapacityItemsSubTypeReplace($productions_capacity_id, $type_replace)
    {
        $this->db->select('
            tbl_productions_capacity_items_sub.id as items_sub_id,
            tbl_productions_capacity_items_sub.productions_capacity_items_id as productions_capacity_items_id,
            tbl_productions_capacity_items_sub.type_sub as type_sub,
            tbl_productions_capacity_items_sub.id_sub as id_sub,
            tbl_productions_capacity_items_sub.unit_id as unit_id,
            tbl_productions_capacity_items_sub.quantity_exchange as quantity_exchange,
            SUM(tbl_productions_capacity_items_sub.quantity_plan_sub) as quantity_plan_bom,
            SUM(tbl_productions_capacity_items_sub.quantity_plan_sub/tbl_productions_capacity_items_sub.quantity_exchange) as quantity_plan
            ', false);
        $this->db->from('tbl_productions_capacity_items');
        $this->db->join('tbl_productions_capacity_items_sub', 'tbl_productions_capacity_items_sub.productions_capacity_items_id = tbl_productions_capacity_items.id');
        $this->db->where('tbl_productions_capacity_items.productions_capacity_id', $productions_capacity_id);
        // $this->db->where('(tbl_productions_capacity_items_sub.type_sub = "semi_products_outside" OR tbl_productions_capacity_items_sub.type_sub = "materials")');
        $this->db->where('(tbl_productions_capacity_items_sub.type_sub != "element")');
        $this->db->where('tbl_productions_capacity_items_sub.type_replace', $type_replace);
        $this->db->order_by('tbl_productions_capacity_items_sub.type_sub ASC, tbl_productions_capacity_items_sub.id_sub ASC');
        $this->db->group_by('tbl_productions_capacity_items_sub.type_sub, tbl_productions_capacity_items_sub.id_sub');
        return $this->db->get()->result_array();
    }

    public function insertBatchProductionsCapacityItemsBomWR($data)
    {
        return $this->db->insert_batch('tbl_productions_capacity_items_bom_warehouse_reality', $data);
    }

    public function deleteProductionsCapacityItemsBomWR($productions_capacity_id) {
        $this->db->where('productions_capacity_id', $productions_capacity_id);
        return $this->db->delete('tbl_productions_capacity_items_bom_warehouse_reality');
    }

    public function layDanhSachBomDKThayThe($productions_capacity_items_id, $type_replace)
    {
        $this->db->select('
            tbl_productions_capacity_items_sub.id,
            tbl_productions_capacity_items_sub.parent_id,
            tbl_productions_capacity_items_sub.productions_capacity_items_id,
            tbl_productions_capacity_items_sub.type_sub,
            tbl_productions_capacity_items_sub.id_sub,
            tbl_productions_capacity_items_sub.unit_id,
            tbl_productions_capacity_items_sub.quantity_plan_sub,
            tbl_productions_capacity_items_sub.quantity_exchange,
            tbl_productions_capacity_items_sub.quantity_purchase_sub,
            tbl_productions_capacity_items_sub.quantity_plan_sub_reality,
            '
        );
        $this->db->from('tbl_productions_capacity_items_sub');
        $this->db->where('tbl_productions_capacity_items_sub.productions_capacity_items_id', $productions_capacity_items_id);
        $this->db->where('tbl_productions_capacity_items_sub.type_replace', $type_replace);
        $this->db->where_not_in('tbl_productions_capacity_items_sub.type_sub', ['element']);
        return $this->db->get()->result_array();
    }

    public function layDanhSachBomDKThayTheVaParent($id, $type_replace)
    {
        $this->db->select('
            tbl_productions_capacity_items_sub.id,
            tbl_productions_capacity_items_sub.parent_id,
            tbl_productions_capacity_items_sub.productions_capacity_items_id,
            tbl_productions_capacity_items_sub.type_sub,
            tbl_productions_capacity_items_sub.id_sub,
            tbl_productions_capacity_items_sub.unit_id,
            tbl_productions_capacity_items_sub.quantity_plan_sub,
            tbl_productions_capacity_items_sub.quantity_exchange,
            tbl_productions_capacity_items_sub.quantity_purchase_sub,
            tbl_productions_capacity_items_sub.quantity_plan_sub_reality,
            '
        );
        $this->db->from('tbl_productions_capacity_items_sub');
        $this->db->where('tbl_productions_capacity_items_sub.parent_id', $id);
        $this->db->where('tbl_productions_capacity_items_sub.type_replace', $type_replace);
        $this->db->where_not_in('tbl_productions_capacity_items_sub.type_sub', ['element']);
        return $this->db->get()->result_array();
    }

    public function checkExistWarehouseReality($type_sub, $id_sub, $productions_capacity_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_productions_capacity_items_bom_warehouse_reality');
        $this->db->where('tbl_productions_capacity_items_bom_warehouse_reality.type_sub', $type_sub);
        $this->db->where('tbl_productions_capacity_items_bom_warehouse_reality.id_sub', $id_sub);
        $this->db->where('tbl_productions_capacity_items_bom_warehouse_reality.productions_capacity_id', $productions_capacity_id);
        $this->db->where('tbl_productions_capacity_items_bom_warehouse_reality.quantity_warehouse_reality >', 0);
        return $this->db->get()->row_array();
    }

    public function updateWarehouseReality($id, $quantity, $option = 1, $primary = 0)
    {
        $this->db->where('id', $id);
        if ($option == 1 && $primary == 1) {
            $this->db->set('quantity_warehouse_reality', 'quantity_warehouse_reality-'.$quantity, false);
        }
        $this->db->set('quantity_warehouse', 'quantity_warehouse-'.$quantity, false);
        return $this->db->update('tbl_productions_capacity_items_bom_warehouse_reality');
    }

    public function updateProductionsCapacityItemsSub($productions_capacity_items_id, $data = [])
    {
        $this->db->where('tbl_productions_capacity_items_sub.productions_capacity_items_id', $productions_capacity_items_id);
        return $this->db->update('tbl_productions_capacity_items_sub', $data);
    }

    public function getProductionsCapacityItemsSubViewBom($type_sub = [], $type_replace = [], $parent_id, $capacity_item_id)
    {
        $this->db->select('
            tbl_productions_capacity_items_sub.*,
            tblunits.unit as unit_name
        ', false);
        $this->db->from('tbl_productions_capacity_items_sub');
        $this->db->where_in('tbl_productions_capacity_items_sub.type_sub', $type_sub);
        $this->db->where_in('tbl_productions_capacity_items_sub.type_replace', $type_replace);
        $this->db->where('tbl_productions_capacity_items_sub.parent_id', $parent_id);
        $this->db->where('tbl_productions_capacity_items_sub.productions_capacity_items_id', $capacity_item_id);
        // $this->db->where('(tbl_productions_capacity_items_sub.quantity_plan_sub_reality > 0 OR tbl_productions_capacity_items_sub.type_sub = "semi_products" OR )');

        $this->db->join('tblunits', 'tblunits.unitid = tbl_productions_capacity_items_sub.unit_id');
        $this->db->order_by('tbl_productions_capacity_items_sub.id ASC');
        return $this->db->get()->result_array();
    }

    public function getMaxWarehouseMaterialReality($type_sub, $id_sub)
    {
        $this->db->select('*');
        $this->db->from('tbl_productions_capacity_items_bom_warehouse_reality');
        $this->db->where('tbl_productions_capacity_items_bom_warehouse_reality.id_sub', $id_sub);
        $this->db->where('tbl_productions_capacity_items_bom_warehouse_reality.type_sub', $type_sub);
        $this->db->where('tbl_productions_capacity_items_bom_warehouse_reality.quantity_warehouse_default !=', 0);
        $this->db->order_by('tbl_productions_capacity_items_bom_warehouse_reality.id DESC');
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }

    public function countProductionsPlanStatus($status_table)
    {
        $tbTransfer = "(
            SELECT
                tbltransfer_warehouse.productions_capacity_id as productions_capacity_id,
                COUNT(tbltransfer_warehouse.id) as ct_transfer_warehouse
            FROM tbltransfer_warehouse
            GROUP BY tbltransfer_warehouse.productions_capacity_id
        ) tb_transfer";

        $tbPurchases = "(
            SELECT
                tblpurchases.id_plan as id_plan,
                COUNT(tblpurchases.id) as ct_purchases
            FROM tblpurchases
            GROUP BY tblpurchases.id_plan
        ) tb_purchases";

        $this->db->from('tbl_productions_plan');
        if ($status_table) {
            if ($status_table == "ycmh") {
                $this->db->join($tbPurchases, 'tb_purchases.id_plan = tbl_productions_plan.id', 'inner');
                $this->datatables->where('tb_purchases.ct_purchases >', 0);
            } else if ($status_table == "keep") {
                $this->db->join($tbTransfer, 'tb_transfer.productions_capacity_id = tbl_productions_plan.id', 'inner');
                $this->datatables->where('tb_transfer.ct_transfer_warehouse >', 0);
            }
			else if ($status_table == "not_keep") {
                $this->db->join($tbTransfer, 'tb_transfer.productions_capacity_id = tbl_productions_plan.id', 'left');
                $this->datatables->where('tb_transfer.ct_transfer_warehouse is null');
            }
        }

        if (!$this->perViewProductionsPlan) {
            $this->datatables->where('tbl_productions_plan.created_by', get_staff_user_id());
        }
        return $this->db->count_all_results();
    }

    public function countProductionsCapacity($status_table)
    {
        $this->db->from('tbl_productions_capacity');
        if ($status_table == 'purchases' || $status_table == 'un_purchases') {
            $this->db->where('tbl_productions_capacity.status_purchases', $status_table);
        } else if ($status_table) {
            $this->db->where('tbl_productions_capacity.status', $status_table);
        }
        return $this->db->get()->num_rows();
    }

    public function countProductionsOrders($status_table)
    {

        $delayProgress = "(
            SELECT COUNT(*)
            FROM tbl_productions_orders_details
            WHERE tbl_productions_orders_details.productions_orders_id = tbl_productions_orders.id AND tbl_productions_orders_details.status != 'complete_production' AND DATEDIFF(tbl_productions_orders_details.deadline, now()) < 0
        )";

        $conditionWarehousing = "(
            SELECT COUNT(*)
            FROM tbl_productions_orders_details
            WHERE tbl_productions_orders_details.productions_orders_id = tbl_productions_orders.id AND tbl_productions_orders_details.status = 'complete_production' AND tbl_productions_orders_details.quantity_warehoused > 0
        )";

        $conditionCompleted = "(
            SELECT COUNT(*)
            FROM tbl_productions_orders_details
            WHERE tbl_productions_orders_details.productions_orders_id = tbl_productions_orders.id AND tbl_productions_orders_details.status != 'complete_production'
        )";

        $conditionQC = "(
            SELECT COUNT(*)
            FROM tbl_productions_orders_details
            WHERE tbl_productions_orders_details.productions_orders_id = tbl_productions_orders.id AND tbl_productions_orders_details.check_quality_id > 0
        )";

        $this->db->from('tbl_productions_orders');
        if (!empty($status_table))
        {
            if ($status_table == 1) {
                $this->db->where('tbl_productions_orders.status_details', 0);
            } else if ($status_table == 2) {
                $this->db->where('tbl_productions_orders.status_details', 1);
                $this->db->where("$conditionCompleted != 0");
            } else if ($status_table == 3) {
                $this->db->where("$conditionWarehousing = tbl_productions_orders.count_items");
            } else if ($status_table == 4) {
                $this->db->where("$delayProgress > 0 && $conditionCompleted != 0");
            } else if ($status_table == 5) {
                $this->db->where("($conditionCompleted = 0 && tbl_productions_orders.status_details = 1)");
            } else if ($status_table == 6) {
                $this->db->where("$conditionQC = tbl_productions_orders.count_items");
            }
        }
        return $this->db->get()->num_rows();
    }

    public function countProductionsOrdersDetail($status_table)
    {
        $this->db->from('tbl_productions_orders_details');
        if (!empty($status_table)) {
            if ($status_table == 1) {
            } else if ($status_table == 2) {
                $this->db->where('tbl_productions_orders_details.status', 'un_produced');
            } else if ($status_table == 3) {
                $this->db->where("tbl_productions_orders_details.quantity_warehoused >", 0);
            } else if ($status_table == 4) {
                $this->db->where("(tbl_productions_orders_details.status != 'complete_production' && DATEDIFF(tbl_productions_orders_details.deadline, now()) < 0)");
            } else if ($status_table == 5) {
                $this->db->where('tbl_productions_orders_details.status', 'complete_production');
            } else if ($status_table == 6) {
                $this->db->where('tbl_productions_orders_details.check_quality_id >', 0);
            }
        }
        return $this->db->get()->num_rows();
    }

    public function countSuggestExporting($status_table)
    {
        $this->db->from('tbl_suggest_exporting');
        if (!empty($status_table)) {
            $this->db->where('tbl_suggest_exporting.status', $status_table);
        }
        $this->db->where('(tbl_suggest_exporting.type = 1 OR tbl_suggest_exporting.type = 3)');
        $this->db->where('tbl_suggest_exporting.pattern_id', 0);
        return $this->db->get()->num_rows();
    }

    public function getProductionsCapacitySubForProductionsOrders($productions_plan_id, $type_items, $items_id)
    {
        $this->db->select('tbl_productions_capacity_items.*');
        $this->db->from('tbl_productions_capacity');
        $this->db->join('tbl_productions_capacity_items', 'tbl_productions_capacity_items.productions_capacity_id = tbl_productions_capacity.id');
        $this->db->where('tbl_productions_capacity.productions_plan_id', $productions_plan_id);
        $this->db->where('tbl_productions_capacity_items.type_items', $type_items);
        $this->db->where('tbl_productions_capacity_items.items_id', $items_id);
        // print_arrays($this->db->get_compiled_select());
        return $this->db->get()->row_array();
    }

    public function getProductionsCapacityItemsSubOrders($productions_capacity_items_id, $type_replace, $parent_id = 0)
    {
        $this->db->select('tbl_productions_capacity_items_sub.*');
        $this->db->from('tbl_productions_capacity_items_sub');
        $this->db->where('tbl_productions_capacity_items_sub.productions_capacity_items_id', $productions_capacity_items_id);
        $this->db->where('tbl_productions_capacity_items_sub.type_replace', $type_replace);
        if ($parent_id) {
            $this->db->where('tbl_productions_capacity_items_sub.parent_id', $parent_id);
        }
        $this->db->order_by('tbl_productions_capacity_items_sub.id ASC');
        return $this->db->get()->result_array();
    }

    public function updateQuantityPCI($id, $quantity_orders, $option = 1)
    {
        $this->db->where('id', $id);
        if ($option == 1)
        {
            $this->db->set('quantity_orders', 'quantity_orders+'.$quantity_orders, false);
        } else if ($option == 2) {
            $this->db->set('quantity_orders', 'quantity_orders-'.$quantity_orders, false);
        }

        return $this->db->update('tbl_productions_capacity_items');
    }

    public function updateQuantityPCISub($id, $quantity_orders_bom, $quantity_orders_primary, $option = 1)
    {
        $this->db->where('id', $id);
        if ($option == 1)
        {
            $this->db->set('quantity_orders_bom', 'quantity_orders_bom+'.$quantity_orders_bom, false);
            $this->db->set('quantity_orders_primary', 'quantity_orders_primary+'.$quantity_orders_primary, false);
        } else if ($option == 2) {
            $this->db->set('quantity_orders_bom', 'quantity_orders_bom-'.$quantity_orders_bom, false);
            $this->db->set('quantity_orders_primary', 'quantity_orders_primary-'.$quantity_orders_primary, false);
        }
        return $this->db->update('tbl_productions_capacity_items_sub');
    }

    public function getProductionsOrdersItemsSub($productions_orders_id)
    {
        $this->db->select('tbl_productions_orders_items_sub.*');
        $this->db->from('tbl_productions_orders_items_sub');
        $this->db->where('tbl_productions_orders_items_sub.productions_orders_id', $productions_orders_id);
        // $this->db->where('tbl_productions_orders_items_sub.type !=', 'element');
        return $this->db->get()->result_array();
    }

    //20-05-2020
    public function updateQuantityPlan($quantity, $options = 0, $object_id, $type_object)
    {   
        if ($type_object == "orders") {
            $this->db->where('id', $object_id);
            if ($options === 0) {
                $this->db->set('tbl_order_items.quantity_plan', 'tbl_order_items.quantity_plan+'.$quantity, false);
            }
            if ($options === 1) {
                $this->db->set('tbl_order_items.quantity_plan', 'tbl_order_items.quantity_plan-'.$quantity, false);
            }
            return $this->db->update('tbl_order_items');
        } else if ($type_object == "business_plan") {
            $this->db->where('id', $object_id);
            if ($options === 0) {
                $this->db->set('tbl_business_plan_items.quantity_plan', 'tbl_business_plan_items.quantity_plan+'.$quantity, false);
            }
            if ($options === 1) {
                $this->db->set('tbl_business_plan_items.quantity_plan', 'tbl_business_plan_items.quantity_plan-'.$quantity, false);
            }
            return $this->db->update('tbl_business_plan_items');
        }
    }

    public function updateQuantityPlanShippings($quantity, $options = 0, $object_id, $type_object, $date)
    {   
        if ($type_object == "orders") {
            $this->db->where('order_item_id', $object_id);
            $this->db->where('date_shipping', $date);
            if ($options === 0) {
                // $this->db->set('tbl_order_items.quantity_plan_item', 'tbl_order_items.quantity_plan_item+'.$quantity, false);
                // $this->db->set('tbl_order_item_shippings.quantity_plan_item', 'tbl_order_item_shippings.quantity_shipping', false);

                $this->db->set('tbl_order_item_shippings.quantity_plan_item', 'tbl_order_item_shippings.quantity_plan_item+'.$quantity, false);
            }
            if ($options === 1) {
                // $this->db->set('tbl_order_items.quantity_plan_item', 'tbl_order_items.quantity_plan_item-'.$quantity, false);
                // $this->db->set('tbl_order_item_shippings.quantity_plan_item', 0, false);

                $this->db->set('tbl_order_item_shippings.quantity_plan_item', 'tbl_order_item_shippings.quantity_plan_item-'.$quantity, false);
            }
            return $this->db->update('tbl_order_item_shippings');
        } else if ($type_object == "business_plan") {
            $this->db->where('business_plan_items_id', $object_id);
            $this->db->where('date', $date);
            if ($options === 0) {
                // $this->db->set('tbl_business_plan_items_date.quantity_plan_item', 'tbl_business_plan_items_date.quantity_plan_item+'.$quantity, false);
                // $this->db->set('tbl_business_plan_items_date.quantity_plan_item', 'tbl_business_plan_items_date.quantity', false);

                $this->db->set('tbl_business_plan_items_date.quantity_plan_item', 'tbl_business_plan_items_date.quantity_plan_item+'.$quantity, false);
            }
            if ($options === 1) {
                // $this->db->set('tbl_business_plan_items_date.quantity_plan_item', 'tbl_business_plan_items_date.quantity_plan_item-'.$quantity, false);
                // $this->db->set('tbl_business_plan_items_date.quantity_plan_item', 0, false);
                $this->db->set('tbl_business_plan_items_date.quantity_plan_item', 'tbl_business_plan_items_date.quantity_plan_item-'.$quantity, false);
            }
            return $this->db->update('tbl_business_plan_items_date');
        }
    }

    public function checkFinishedPlan($object_id, $type_object)
    {
        if ($type_object == "orders") {
            $sumTotalQuantityDate = "COALESCE((
                SELECT SUM(tbl_order_item_shippings.quantity_shipping)
                FROM tbl_order_item_shippings
                WHERE tbl_order_item_shippings.order_item_id = tbl_order_items.id
            ), 0)";

            $query = "(
                SELECT COUNT(*) as ctRest
                FROM tbl_order_items
                INNER JOIN tbl_products ON tbl_products.id = tbl_order_items.item_id
                WHERE tbl_order_items.order_id = $object_id AND (($sumTotalQuantityDate * IF (tbl_products.unit_id = tbl_order_items.unit_id, tbl_products.conversion_quantity_unit, 1)) > tbl_order_items.quantity_plan)
            )";
        } else if ($type_object == "business_plan") {
            $sumTotalQuantityDate = "COALESCE((
                SELECT SUM(tbl_business_plan_items_date.quantity)
                FROM tbl_business_plan_items_date
                WHERE tbl_business_plan_items_date.business_plan_items_id = tbl_business_plan_items.id
            ), 0)";

            $query = "(
                SELECT COUNT(*) as ctRest
                FROM tbl_business_plan_items
                WHERE tbl_business_plan_items.business_plan_id = $object_id AND ($sumTotalQuantityDate > tbl_business_plan_items.quantity_plan)
            )";
        }

        return $this->db->query($query)->row_array()['ctRest'];
    }

    public function getProductionsPlanById($productions_plan_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_productions_plan_items');
        $this->db->where('tbl_productions_plan_items.productions_plan_id', $productions_plan_id);
        return $this->db->get()->result_array();
    }

    public function getProductionsPlanDetails($production_plan_item_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_productions_plan_details');
        $this->db->where('tbl_productions_plan_details.productions_plan_item_id', $production_plan_item_id);
        return $this->db->get()->result_array();
    }

    public function deleteProductionsPlanWarehouses($productions_plan_id)
    {
        $this->db->where('tbl_productions_plan_warehouses.productions_plan_id', $productions_plan_id);
        return $this->db->delete('tbl_productions_plan_warehouses');
    }

    public function getTaskPodWork($id)
    {
        $quantityImport = "(
            SELECT
            FROM 
        )";


        $staff_user_id = get_staff_user_id();
        $this->db->select('*');
        $this->db->from('tbltasks');
        $this->db->where('tbltasks.rel_type', 'order_production_details');
        $this->db->where('tbltasks.rel_id', $id);
        $this->db->where('tbltasks.quantity_shift_work >', 0);

        if (!is_admin())
        {
            $isRole = "(
                SELECT COUNT(*)
                FROM tbltask_assigned
                WHERE tbltask_assigned.staffid = $staff_user_id AND tbltask_assigned.taskid = tbltasks.id
            )";
            $this->db->where("($isRole > 0)");
        }

        return $this->db->get()->result_array();
    }

    public function getPriceLastNotZero($product_id, $type_items)
    {
        $this->db->select('tblwarehouse_product.price');
        $this->db->from('tblwarehouse_product');
        $this->db->where('tblwarehouse_product.product_id', $product_id);
        $this->db->where('tblwarehouse_product.type_items', $type_items);
        $this->db->where('tblwarehouse_product.price >', 0);
        $this->db->order_by('tblwarehouse_product.date_warehouse DESC');
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }


    //----------
    public function updateQuantityExportPurchaseInternal($id, $quantity, $option = 1)
    {
        $this->db->where('id', $id);
        if ($option == 1) {
            $this->db->set('tbl_purchase_internal_items.quantity_export', 'tbl_purchase_internal_items.quantity_export+'.$quantity, false);
        } else if ($option == 2) {
            $this->db->set('tbl_purchase_internal_items.quantity_export', 'tbl_purchase_internal_items.quantity_export-'.$quantity, false);
        }

        return $this->db->update('tbl_purchase_internal_items');
    }

    public function getPurchaseInternalByExport($id)
    {
        $isQuantityExport = "(
            SELECT COUNT(*)
            FROM tbl_purchase_internal_items
            WHERE tbl_purchase_internal_items.quantity_export < tbl_purchase_internal_items.quantity AND tbl_purchase_internal_items.purchase_internal_id = tbl_purchase_internal.id
        )";
        
        $this->db->select('*');
        $this->db->from('tbl_purchase_internal');
        $this->db->where('tbl_purchase_internal.warehouseman_id >', 0);
        $this->db->where("$isQuantityExport > 0");
        $this->db->where('tbl_purchase_internal.id', $id);
        return $this->db->get()->row_array();
    }

    public function getPurchaseInternalItemsById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_purchase_internal_items');
        $this->db->where('tbl_purchase_internal_items.id', $id);
        return $this->db->get()->row_array();
    }

    public function getTotalQuantityWarehouseLocationAndWarehouse($id_items, $type_items, $location_id, $warehouse_id)
    {
        if ($type_items == "materials") {
            $type_items = 'nvl';
        } else if ($type_items == "tools_supplies") {
            $type_items = 'tools';
        } else if ($type_items == "items") {
            $type_items = 'items';
        } else {
            $type_items = 'product';
        }

        $this->db->select('SUM(tblwarehouse_items.product_quantity) as total_quantity', false);
        $this->db->from('tblwarehouse_items');
        $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_items.localtion');
        $this->db->where('tblwarehouse_items.id_items', $id_items);
        $this->db->where('tblwarehouse_items.type_items', $type_items);
        $this->db->where('tblwarehouse_items.localtion', $location_id);
        $this->db->where('tblwarehouse_items.warehouse_id', $warehouse_id);
        $this->db->where('tbllocaltion_warehouses.status !=', 2);
        return $this->db->get()->row_array();
    }

    public function getMaterialSuggestExporting($productions_orders_items_id)
    {
        $this->db->select("
            tbl_productions_orders_items_sub.item_id as item_id,
            tbl_productions_orders_items_sub.unit_id as unit_id,
            tbl_productions_orders_items_sub.type as type_item,
            tbl_productions_orders_items_sub.unit_parent_id as unit_parent_id,
            tbl_productions_orders_items_sub.item_code as item_code,
            tbl_productions_orders_items_sub.item_name as item_name,
            tblunits.unit as unit_name,
            SUM(tbl_productions_orders_items_sub.quantity) as quantity,
            tbl_productions_orders_items_sub.quantity_exchange as quantity_exchange,
            GROUP_CONCAT(distinct(tbl_productions_orders_items_sub.leadtime) SEPARATOR '</br>') as leadtime,
            GROUP_CONCAT(distinct(tbl_stages.name) SEPARATOR '</br>') as stage,
            ", false);
        $this->db->from('tbl_productions_orders_items_sub');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_productions_orders_items_sub.unit_id', 'left');
        $this->db->where("(tbl_productions_orders_items_sub.type = 'materials' OR  tbl_productions_orders_items_sub.type = 'semi_products_outside' OR tbl_productions_orders_items_sub.type = 'semi_products')");
        $this->db->where('tbl_productions_orders_items_sub.productions_orders_items_id', $productions_orders_items_id);
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_sub.stage_item_id', 'left');
        $this->db->where('tbl_productions_orders_items_sub.quantity >', 0);
        $this->db->group_by('tbl_productions_orders_items_sub.item_id, tbl_productions_orders_items_sub.unit_id, tbl_productions_orders_items_sub.type');
        return $this->db->get()->result_array();
    }

    //
    public function insertProductionsPlanBom($data) {
        $this->db->insert('tbl_productions_plan_bom', $data);
        return $this->db->insert_id();
    }

    public function handlingBom($quantity, $product_id, $productions_plan_id, $productions_plan_items_id, $parent_id = 0, $key = 0, $versions = false, $quantity_order = 0, $quantity_compensation_sm = 0, $temp_product_id = 0) {
        if ($key == 5) {
            return true;
        }
        if (empty($versions)) {
            $product = get_table_where('tbl_products', ['id' => $product_id], '', 'row_array', '', 'id, versions');
            $versions = $product['versions'];
        }
        if (!empty($versions)) {
            $version = $this->products_model->getBomByProductIdAndVersions($product_id, $versions);
            if (!empty($version)) {
                $elements = $this->products_model->getVersionsElementByVersionId($version['id']);
                $iiPartent = 0;
                if (!empty($elements)) {
                    foreach ($elements as $k => $val) {
                        $quantity_element = $val['quantity'];
                        $total_quantity_element = $quantity / $quantity_element;
                        $quantity_compensation_sm = $quantity_compensation_sm/$quantity_element;

                        $option = [
                            'productions_plan_id' => $productions_plan_id,
                            'productions_plan_items_id' => $productions_plan_items_id,
                            'item_type' => 'element',
                            'item_id' => 0,
                            'item_code' => $val['element_name'],
                            'item_name' => $val['element_name'],
                            'unit_id' => 0,
                            'quantity_single' => $quantity_element,
                            'quantity' => $total_quantity_element,
                            'unit_parent_id' => 0,
                            'quantity_exchange' => 1,
                            'quantity_primary' => $total_quantity_element,
                            'leadtime' => 0,
                            'stage_item_id' => 0,
                            // 'parent_id' => ($temp_product_id == $product_id) ? 0 : $parent_id,
                            'parent_id' => $parent_id,
                            'quantity_order' => $quantity_order,
                            'quantity_compensation' => 0,
                            'type_element_item' => $val['type_element'],
                            'quantity_compensation_primary' => 0,
                            'quantity_element' => $quantity_element,
                            'exchange_unit' => 1,
                            'is_zinc' => 0,
                        ];

                        $elementInsert = $this->insertProductionsPlanBom($option);
                        $iiPartent++;
                        $element_items = $this->products_model->getElementItemsByElementId($val['id']);
                        if (!empty($element_items)) {
                            foreach ($element_items as $i => $el) {
                                $quantity_single = $el['quantity'];
                                // $total_quantity_item = $quantity_order/$total_quantity_element * $quantity_single;
                                $total_quantity_item = $quantity_order/$el['number_children_size'] * $quantity_single;
                                $quantity_primary = 0;
                                $is_single_use = 0;
                                $quota_material_replace_t = 0;
                                $quantity_single_use = 0;
                                $exchange_unit = 1;
                                $conversion_quantity_unit = 1;
                                $is_zinc = 0;

                                $dtStage = get_table_where('tbl_stages', ['id' => $el['stage_id']], '', 'row_array');
                                if (!empty($dtStage['quota_material_replace_t'])) {
                                    $quota_material_replace_t = $dtStage['quota_material_replace_t'];
                                }

                                if ($el['type'] == "semi_products" || $el['type'] == "semi_products_outside") {
                                    $info = $this->products_model->rowProduct($el['item_id']);
                                    $unit_parent_id = $info['unit_id'];
                                    $quantity_exchange = 1;
                                    $quantity_primary = $total_quantity_item;
                                    $conversion_quantity_unit = $info['conversion_quantity_unit'];
                                } else {
                                    $info = $this->items_model->rowMaterial($el['item_id']);
                                    $unit_id = $el['unit_id'];
                                    $unit_parent_id = $info['unit_id'];
                                    $row_exchange = $this->products_model->rowExchangeItems($el['item_id'], $unit_id);
                                    $quantity_exchange = 1;
                                    if (!empty($row_exchange)) {
                                        $quantity_exchange = $row_exchange['number_exchange'];
                                    }

                                    if (!empty($exchange_unit)) {
                                        $exchange_unit = $info['exchange_unit'];
                                    }

                                    $is_single_use = !empty($info['is_single_use']) ? $info['is_single_use'] : 0;
                                    if ($is_single_use) {
                                        if ($quota_material_replace_t != 0) {
                                            $quantity_single_use = ceil($quantity_order/$quota_material_replace_t * $quantity_single);
                                            // $quantity_primary = ceil($quantity_single_use/$quantity_exchange * $exchange_unit);
                                            $quantity_primary = ceil($quantity_single_use * $quantity_exchange / $exchange_unit);
                                        }
                                    } else {
                                        if ($quantity_exchange != 0) {
                                            $quantity_primary = $total_quantity_item * $quantity_exchange / $exchange_unit;
                                        }
                                    }

                                    $is_zinc = !empty($info['is_zinc']) ? $info['is_zinc'] : 0;
                                    if ($is_zinc) {
                                        $quantity_single_use = $quantity_single;
                                        $total_quantity_item = $quantity_single;
                                        $quantity_primary = ceil($total_quantity_item * $quantity_exchange / $exchange_unit);
                                    }

                                    $key = 0;
                                }

                                // $quantity_compensation_primary = $el['quantity_compensation']/$quantity_exchange * $exchange_unit;
                                // $quantity_compensation_sm_primary = $quantity_compensation_sm/$quantity_exchange * $exchange_unit;

                                $quantity_compensation_primary = $el['quantity_compensation'] * $quantity_exchange / $exchange_unit;
                                $quantity_compensation_sm_primary = $quantity_compensation_sm * $quantity_exchange / $exchange_unit;

                                $paper_exchange = 0;
                                // if (!$is_single_use) {
                                    if ($el['number_children_size']) {
                                        $paper_exchange = $quantity_order/$el['number_children_size'];
                                    }
                                // } 

                                $option = [
                                    'productions_plan_id' => $productions_plan_id,
                                    'productions_plan_items_id' => $productions_plan_items_id,
                                    'parent_id' => $elementInsert,
                                    'item_type' => $el['type'],
                                    'item_id' => $el['item_id'],
                                    'item_code' => $info['code'],
                                    'item_name' => $info['name'],
                                    'unit_id' => $el['unit_id'],
                                    'quantity_single' => $quantity_single,
                                    'quantity' => $is_single_use ? $quantity_single_use : $total_quantity_item,
                                    'unit_parent_id' => $unit_parent_id,
                                    'quantity_exchange' => $quantity_exchange,
                                    'quantity_primary' => $quantity_primary,
                                    'leadtime' => $el['leadtime'],
                                    'stage_item_id' => $el['stage_id'],
                                    'quantity_order' => $quantity_order,
                                    'is_single_use' => $is_single_use,
                                    'quota_material_replace_t' => $quota_material_replace_t,
                                    'quantity_compensation' => $el['quantity_compensation'],
                                    'type_element_item' => $el['type_element_item'],
                                    'quantity_compensation_primary' => $quantity_compensation_primary,
                                    'quantity_element' => $quantity_element,
                                    'quantity_compensation_sm' => $quantity_compensation_sm,
                                    'quantity_compensation_sm_primary' => $quantity_compensation_sm_primary,

                                    'landscape_print_size' => $el['landscape_print_size'],
                                    'vertical_print_size' => $el['vertical_print_size'],
                                    'number_children_size' => $el['number_children_size'],
                                    'paper_exchange' => $paper_exchange,
                                    'hand_input_paper_exchange' => $el['hand_input_paper_exchange'],
                                    'exchange_unit' => $exchange_unit,
                                    'conversion_quantity_unit' => $conversion_quantity_unit,
                                    'is_zinc' => $is_zinc

                                ];
                                $parent_id1 = $this->insertProductionsPlanBom($option);
                                if (!empty($parent_id1)) {
                                    if ($el['type'] == "semi_products") {
                                        $key++;
                                        // $this->handlingBom($total_quantity_item, $info['id'], $productions_plan_id, $productions_plan_items_id, $parent_id1, $key, '', $total_quantity_item, $el['quantity_compensation'], $temp_product_id);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function handlingBomProductionsPlan($id) {
        $productions_plan_id = $id;
        $this->db->select('
            tbl_productions_plan_items.id as id,
            tbl_productions_plan_items.product_id as product_id,
            tbl_productions_plan_items.quantity_total_details as quantity_total_details,
            tbl_productions_plan_items.quantity_reserve as quantity_reserve,
            tbl_productions_plan_items.versions as versions,
        ', false);
        $this->db->from('tbl_productions_plan_items');
        $this->db->where('tbl_productions_plan_items.productions_plan_id', $id);
        $productions_plan_items = $this->db->get()->result_array();
        if (!empty($productions_plan_items)) {
            foreach ($productions_plan_items as $key => $value) {
                $versions = $value['versions'];
                $product_id = $value['product_id'];
                $quantity = $value['quantity_total_details'] + $value['quantity_reserve'];
                $productions_plan_items_id = $value['id'];
                $this->handlingBom($quantity, $product_id, $productions_plan_id, $productions_plan_items_id, 0, 0,$versions, $quantity, 0, $product_id);
            }
        }
    }

    public function viewProductionsPlanSemiProducts($productions_plan_id) {

        $tbWarehouse = "(
            SELECT
                tblwarehouse_items.id_items as id_items,
                SUM(tblwarehouse_items.product_quantity) as product_quantity
            FROM tblwarehouse_items
            WHERE tblwarehouse_items.type_items = 'product' AND tblwarehouse_items.warehouse_id != ".WAREHOUSES_CAPACITY."
            GROUP BY tblwarehouse_items.id_items
        ) tb_quantity_warehouse";

        $this->db->select('
            tbl_productions_plan_bom.item_id as item_id,
            tbl_productions_plan_bom.id as id,
            tbl_products.code as item_code,
            tbl_products.name as item_name,
            tbl_productions_plan_bom.item_type as item_type,
            SUM(tbl_productions_plan_bom.quantity_primary) as quantity_primary,
            SUM(tbl_productions_plan_bom.quantity) as quantity,
            tblunits.unit as unit_name,
            unit_primary.unit as unit_primary_name,
            0 as quantity_inventory,
            tbl_products.images as images,
        ', false);
        $this->db->from('tbl_productions_plan_bom');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_plan_bom.item_id');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_productions_plan_bom.unit_id', 'left');
        $this->db->join('tblunits unit_primary', 'unit_primary.unitid = tbl_productions_plan_bom.unit_parent_id', 'left');
        // $this->db->join($tbWarehouse, 'tb_quantity_warehouse.id_items = tbl_productions_plan_bom.item_id', 'left');
        $this->db->where('tbl_productions_plan_bom.productions_plan_id', $productions_plan_id);
        $this->db->where_in('tbl_productions_plan_bom.item_type', ['semi_products', 'semi_products_outside']);
        $this->db->group_by('tbl_productions_plan_bom.item_id');
        // return $this->db->get()->result_array();
        $rs = $this->db->get()->result_array();

        if (!empty($rs)) {
            $arrItemId = array_column($rs, 'item_id');
            if (!empty($arrItemId)) {
                $arrItemId = array_unique($arrItemId);
                $tbWarehouse = "
                    SELECT
                        tblwarehouse_items.id_items as id_items,
                        SUM(tblwarehouse_items.product_quantity) as product_quantity
                    FROM tblwarehouse_items
                    WHERE tblwarehouse_items.type_items = 'product' AND tblwarehouse_items.warehouse_id != ".WAREHOUSES_CAPACITY." AND tblwarehouse_items.id_items IN (".implode(',', $arrItemId).")
                    GROUP BY tblwarehouse_items.id_items
                ";
                $listWarehouses = $this->db->query($tbWarehouse)->result_array();
                if (!empty($listWarehouses)) {
                    $listWarehouses = array_reduce($listWarehouses, function($carry, $item) {
                        $carry[$item['id_items']] = $item;
                        return $carry;
                    });
                }
            }

            foreach ($rs as $key => $value) {
                $dataWarehouse = $listWarehouses[$value['item_id']] ?? null;
                $rs[$key]['quantity_inventory'] = $dataWarehouse['product_quantity'] ?? 0;
            }
        }

        return $rs;
    }

    public function viewProductionsPlanMaterials($productions_plan_id) {
        $tbWarehouse = "(
            SELECT
                tblwarehouse_items.id_items as id_items,
                SUM(tblwarehouse_items.product_quantity) as product_quantity
            FROM tblwarehouse_items
            WHERE tblwarehouse_items.type_items = 'nvl' AND tblwarehouse_items.warehouse_id != ".WAREHOUSES_CAPACITY."
            GROUP BY tblwarehouse_items.id_items
        ) tb_quantity_warehouse";

        $this->db->select('
            tbl_productions_plan_bom.item_id as item_id,
            tbl_productions_plan_bom.id as id,
            tbl_materials.code as item_code,
            tbl_materials.name as item_name,
            SUM(tbl_productions_plan_bom.quantity_primary) as quantity_primary,
            SUM(tbl_productions_plan_bom.quantity) as quantity,
            SUM(tbl_productions_plan_bom.quantity_compensation) as quantity_compensation,
            SUM(tbl_productions_plan_bom.quantity_compensation_primary) as quantity_compensation_primary,
            SUM(tbl_productions_plan_bom.quantity_compensation_sm_primary) as quantity_compensation_sm_primary,
            tblunits.unit as unit_name,
            unit_primary.unit as unit_primary_name,
            0 as quantity_inventory,
            tbl_materials.images as images,
            unit_stock.unit as unit_name_stock,
            tbl_materials.exchange_standard_unit as exchange_standard_unit,
            tbl_materials.exchange_unit as exchange_unit
        ', false);
        $this->db->from('tbl_productions_plan_bom');
        $this->db->join('tbl_materials', 'tbl_materials.id = tbl_productions_plan_bom.item_id');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_productions_plan_bom.unit_id', 'left');
        $this->db->join('tblunits unit_primary', 'unit_primary.unitid = tbl_productions_plan_bom.unit_parent_id', 'left');
        $this->db->join('tblunits unit_stock', 'unit_stock.unitid = tbl_materials.standard_unit', 'left');
        // $this->db->join($tbWarehouse, 'tb_quantity_warehouse.id_items = tbl_productions_plan_bom.item_id', 'left');
        $this->db->where('tbl_productions_plan_bom.productions_plan_id', $productions_plan_id);
        $this->db->where_in('tbl_productions_plan_bom.item_type', ['materials']);
        $this->db->group_by('tbl_productions_plan_bom.item_id');
        // return $this->db->get()->result_array();
        $rs = $this->db->get()->result_array();

        if (!empty($rs)) {
            $arrItemId = array_column($rs, 'item_id');
            if (!empty($arrItemId)) {
                $arrItemId = array_unique($arrItemId);
                $tbWarehouse = "
                    SELECT
                        tblwarehouse_items.id_items as id_items,
                        SUM(tblwarehouse_items.product_quantity) as product_quantity
                    FROM tblwarehouse_items
                    WHERE tblwarehouse_items.type_items = 'nvl' AND tblwarehouse_items.warehouse_id != ".WAREHOUSES_CAPACITY." AND tblwarehouse_items.id_items IN (".implode(',', $arrItemId).")
                    GROUP BY tblwarehouse_items.id_items
                ";
                $listWarehouses = $this->db->query($tbWarehouse)->result_array();
                if (!empty($listWarehouses)) {
                    $listWarehouses = array_reduce($listWarehouses, function($carry, $item) {
                        $carry[$item['id_items']] = $item;
                        return $carry;
                    });
                }
            }

            foreach ($rs as $key => $value) {
                $dataWarehouse = $listWarehouses[$value['item_id']] ?? null;
                $rs[$key]['quantity_inventory'] = $dataWarehouse['product_quantity'] ?? 0;
            }
        }

        return $rs;
    }
    

    public function deleteProductionsPlanBom($productions_plan_id) {
        $this->db->where('productions_plan_id', $productions_plan_id);
        return $this->db->delete('tbl_productions_plan_bom');
    }

    public function rowOrdersItemsById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_order_items');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function rowBusinessItemsById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_business_plan_items');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    //
    public function handlingBomOrders($quantity, $product_id, $productions_orders_id, $productions_orders_items_id, $parent_id = 0, $key = 0, $versions = false, $quantity_order = 0, $quantity_compensation_sm = 0, $temp_product_id = 0) {
        if ($key == 5) {
            return true;
        }

        if (empty($versions)) {
            $product = get_table_where('tbl_products', ['id' => $product_id], '', 'row_array', '', 'id, versions');
            $versions = $product['versions'];
        }
        if (!empty($versions)) {
            $version = $this->products_model->getBomByProductIdAndVersions($product_id, $versions);
            if (!empty($version)) {
                $elements = $this->products_model->getVersionsElementByVersionId($version['id']);
                $iiPartent = 0;
                if (!empty($elements)) {
                    foreach ($elements as $k => $val) {
                        $quantity_element = $val['quantity'];
                        $total_quantity_element = $quantity / $quantity_element;
                        $quantity_compensation_sm = $quantity_compensation_sm/$quantity_element;

                        $option = [
                            'productions_orders_id' => $productions_orders_id,
                            'productions_orders_items_id' => $productions_orders_items_id,
                            'type' => 'element',
                            'item_id' => 0,
                            'item_code' => $val['element_name'],
                            'item_name' => $val['element_name'],
                            'unit_id' => 0,
                            'quantity_single' => $quantity_element,
                            'quantity' => $total_quantity_element,
                            'unit_parent_id' => 0,
                            'quantity_exchange' => 1,
                            'quantity_primary' => $total_quantity_element,
                            'leadtime' => 0,
                            'stage_item_id' => 0,
                            // 'parent_id' => ($temp_product_id == $product_id) ? 0 : $parent_id,
                            'parent_id' => $parent_id,
                            'quantity_order' => $quantity_order,
                            'quantity_compensation' => 0,
                            'type_element_item' => $val['type_element'],
                            'quantity_compensation_primary' => 0,
                            'quantity_element' => $quantity_element,
                            'exchange_unit' => 1,
                            'is_zinc' => 0,
                        ];

                        $elementInsert = $this->insertProductionOrdersItemsSub($option);
                        $iiPartent++;
                        $element_items = $this->products_model->getElementItemsByElementId($val['id']);
                        if (!empty($element_items)) {
                            foreach ($element_items as $i => $el) {
                                $quantity_single = $el['quantity'];
                                // $total_quantity_item = $total_quantity_element * $quantity_single;
                                $total_quantity_item = $quantity_order/$el['number_children_size'] * $quantity_single;
                                $quantity_primary = 0;
                                $is_single_use = 0;
                                $quota_material_replace_t = 0;
                                $quantity_single_use = 0;
                                $exchange_unit = 1;
                                $conversion_quantity_unit = 1;
                                $is_zinc = 0;

                                $dtStage = get_table_where('tbl_stages', ['id' => $el['stage_id']], '', 'row_array');
                                if (!empty($dtStage['quota_material_replace_t'])) {
                                    $quota_material_replace_t = $dtStage['quota_material_replace_t'];
                                }

                                if ($el['type'] == "semi_products" || $el['type'] == "semi_products_outside") {
                                    $info = $this->products_model->rowProduct($el['item_id']);
                                    $unit_parent_id = $info['unit_id'];
                                    $quantity_exchange = 1;
                                    $quantity_primary = $total_quantity_item;
                                    $conversion_quantity_unit = $info['conversion_quantity_unit'];
                                } else {
                                    $info = $this->items_model->rowMaterial($el['item_id']);
                                    $unit_id = $el['unit_id'];
                                    $unit_parent_id = $info['unit_id'];
                                    $row_exchange = $this->products_model->rowExchangeItems($el['item_id'], $unit_id);
                                    $quantity_exchange = 1;
                                    if (!empty($row_exchange)) {
                                        $quantity_exchange = $row_exchange['number_exchange'];
                                    }

                                    if (!empty($info['exchange_unit'])) {
                                        $exchange_unit = $info['exchange_unit'];
                                    }

                                    $is_single_use = !empty($info['is_single_use']) ? $info['is_single_use'] : 0;
                                    if ($is_single_use) {
                                        if ($quota_material_replace_t != 0) {
                                            $quantity_single_use = ceil($quantity_order/$quota_material_replace_t * $quantity_single);
                                            // $quantity_primary = ceil($quantity_single_use/$quantity_exchange * $exchange_unit);
                                            $quantity_primary = ceil($quantity_single_use * $quantity_exchange / $exchange_unit);
                                        }
                                    } else {
                                        if ($quantity_exchange != 0) {
                                            // $quantity_primary = $total_quantity_item/$quantity_exchange * $exchange_unit;
                                            $quantity_primary = $total_quantity_item * $quantity_exchange / $exchange_unit;
                                        }
                                    }

                                    $is_zinc = !empty($info['is_zinc']) ? $info['is_zinc'] : 0;
                                    if ($is_zinc) {
                                        $quantity_single_use = $quantity_single;
                                        $total_quantity_item = $quantity_single;
                                        $quantity_primary = ceil($total_quantity_item * $quantity_exchange / $exchange_unit);
                                    }

                                    $key = 0;
                                }

                                // $quantity_compensation_primary = $el['quantity_compensation']/$quantity_exchange * $exchange_unit;
                                // $quantity_compensation_sm_primary = $quantity_compensation_sm/$quantity_exchange * $exchange_unit;

                                $quantity_compensation_primary = $el['quantity_compensation'] * $quantity_exchange / $exchange_unit;
                                $quantity_compensation_sm_primary = $quantity_compensation_sm * $quantity_exchange / $exchange_unit;

                                $paper_exchange = 0;
                                // if (!$is_single_use) {
                                    $paper_exchange = $quantity_order/$el['number_children_size'];
                                // } 

                                $option = [
                                    'productions_orders_id' => $productions_orders_id,
                                    'productions_orders_items_id' => $productions_orders_items_id,
                                    'parent_id' => $elementInsert,
                                    'type' => $el['type'],
                                    'item_id' => $el['item_id'],
                                    'item_code' => $info['code'],
                                    'item_name' => $info['name'],
                                    'unit_id' => $el['unit_id'],
                                    'quantity_single' => $quantity_single,
                                    'quantity' => $is_single_use ? $quantity_single_use : $total_quantity_item,
                                    'unit_parent_id' => $unit_parent_id,
                                    'quantity_exchange' => $quantity_exchange,
                                    'quantity_primary' => $quantity_primary,
                                    'leadtime' => $el['leadtime'],
                                    'stage_item_id' => $el['stage_id'],
                                    'quantity_order' => $quantity_order,
                                    'is_single_use' => $is_single_use,
                                    'quota_material_replace_t' => $quota_material_replace_t,
                                    'quantity_compensation' => $el['quantity_compensation'],
                                    'type_element_item' => $el['type_element_item'],
                                    'quantity_compensation_primary' => $quantity_compensation_primary,
                                    'quantity_element' => $quantity_element,
                                    'quantity_compensation_sm' => $quantity_compensation_sm,
                                    'quantity_compensation_sm_primary' => $quantity_compensation_sm_primary,

                                    'landscape_print_size' => $el['landscape_print_size'],
                                    'vertical_print_size' => $el['vertical_print_size'],
                                    'number_children_size' => $el['number_children_size'],
                                    'paper_exchange' => $paper_exchange,
                                    'hand_input_paper_exchange' => $el['hand_input_paper_exchange'],
                                    'exchange_unit' => $exchange_unit,
                                    'conversion_quantity_unit' => $conversion_quantity_unit,
                                    'is_zinc' => $is_zinc
                                ];
                                $parent_id1 = $this->insertProductionOrdersItemsSub($option);
                                if (!empty($parent_id1)) {
                                    if ($el['type'] == "semi_products") {
                                        $key++;
                                        // $this->handlingBomOrders($total_quantity_item, $info['id'], $productions_orders_id, $productions_orders_items_id, $parent_id1, $key, '', $total_quantity_item, $el['quantity_compensation'], $temp_product_id);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function handlingBomProductionsOrders($id) {
        $productions_orders_id = $id;
        $this->db->select('
            tbl_productions_orders_items.id as id,
            tbl_productions_orders_items.items_id as product_id,
            tbl_productions_orders_items.quantity as quantity,
            tbl_productions_orders_items.versions_bom as versions_bom
        ', false);
        $this->db->from('tbl_productions_orders_items');
        $this->db->where('tbl_productions_orders_items.productions_orders_id', $id);
        $productions_plan_items = $this->db->get()->result_array();
        if (!empty($productions_plan_items)) {
            foreach ($productions_plan_items as $key => $value) {
                $product_id = $value['product_id'];
                $quantity = $value['quantity'];
                $productions_orders_items_id = $value['id'];
                $versions_bom = $value['versions_bom'];
                $this->handlingBomOrders($quantity, $product_id, $productions_orders_id, $productions_orders_items_id, 0, 0, $versions_bom, $quantity, 0, $product_id);
            }
        }
    }

    public function getProductionsPlanItemsForObject($item_object_id, $type_object) {
        $this->db->select('tbl_productions_plan_items.id, tbl_productions_plan_items.productions_plan_id');
        $this->db->from('tbl_productions_plan_items');
        $this->db->where('tbl_productions_plan_items.type_object', $type_object);
        $this->db->where('tbl_productions_plan_items.item_object_id', $item_object_id);
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }

    public function getProductionsPlanOrders($productions_order_id) {
        $this->db->select('*');
        $this->db->from('tbl_productions_plan_orders');
        $this->db->where('tbl_productions_plan_orders.productions_order_id', $productions_order_id);
        return $this->db->get()->result_array();
    }

    public function handlingProductionsDetail($id) {
        $productions_orders = $this->manufactures_model->rowProductionsOrdersById($id);
        $items = $this->manufactures_model->getProductionsORdersItemsForCreated($id);
        $data['result'] = 1;
        $data['message'] = lang('success');
        if ($productions_orders['status'] != "approved") {
            $data['result'] = 0;
            $data['message'] = lang('tnh_please_approved');
            return $data;
        }

        if ($productions_orders['status_details']) {
            $data['result'] = 0;
            $data['message'] = lang('tnh_created_an_order_details');
            return $data;
        }

        $flag = false;
        foreach ($items as $key => $value) {
            $deadline = '0000-00-00';
            $dateCreated = date('Y-m-d H:i:s');
            $created_by = get_staff_user_id();
            $reference_no = getReference('productions_orders_details');

            $object_id = 0;
            $object_type = $value['object_item_type'];
            if ($object_type == "orders") {
                $order_item = $this->orders_model->rowOrderItemsById($value['production_plan_item_id']);
                $object_id = $order_item['order_id'];
            } else if ($object_type == "business_plan") {
                $business_item = $this->rowBusinessItemsById($value['production_plan_item_id']);
                $object_id = $business_item['business_plan_id'];
            }

            $details = [
                'reference_no' => $reference_no,
                'productions_orders_id' => $id,
                'productions_orders_item_id' => $value['id'],
                'deadline' => $deadline,
                'departments' => 0,
                'status' => 'un_produced',
                'created_by' => $created_by,
                'date_created' => $dateCreated,
                'object_id' => $object_id,
                'object_type' => $object_type,
            ];

            $pod_id = $this->manufactures_model->insertProductionsOrdersDetails($details);
            if ($pod_id) {
                $flag = true;
                updateReference('productions_orders_details');
                $employees = null;
                $emp = [];
                if (!empty($employees)) {
                    foreach ($employees as $k => $val) {
                        $emp[] = [
                            'employee_id' => $val,
                            'productions_orders_details_id' => $pod_id,
                        ];
                    }
                    if (!empty($emp)) {
                        $this->manufactures_model->insertBatchPODEmployees($emp);
                    }
                }

                if (!empty($departments)) {
                    foreach ($departments as $k => $val) {
                        $depart = [
                            'pod_id' => $pod_id,
                            'department_id' => $val,
                        ];
                        $this->manufactures_model->insertPODDepartments($depart);
                    }
                }
            }
        }
        if ($flag == true) {
            $this->manufactures_model->updateProductionsOrders($id, ['status_details' => 1]);
            insertActivityLog([
                'type_parent_obj' => 'productions_orders',
                'table_obj' => 'tbl_productions_orders',
                'id_obj' => $id,
                'name_obj' => $productions_orders['reference_no'],
                'content' => lang('tnh_his_created_productions_detail_productions_orders') . ' [' . $productions_orders['reference_no'] . ']',
                'actions' => 'created_productions_detail'
            ]);
            $data['result'] = 1;
            $data['message'] = lang('success');
        }
        return $data;
    }

    public function handlingStagesProductionsOrders($productions_orders_id) {
        $items = $this->manufactures_model->getProductionsORdersItemsForCreated($productions_orders_id);
        $stages = [];
        $this->deleteProductionsOrdersItemsStages($productions_orders_id);
        foreach ($items as $key => $value) {
            $object_id = 0;
            $versions_stage = $value['versions_stage'];
            $object_type = $value['object_item_type'];
            $active = 0;
            $staff_active = 0;
            $date_active = null;
            if ($object_type == "orders") {
                $order_item = $this->orders_model->rowOrderItemsById($value['production_plan_item_id']);
                $object_id = $order_item['order_id'];

                $active = $order_item['active'];
                $staff_active = $order_item['staff_active'];
                $date_active = $order_item['date_active'];
            } else if ($object_type == "business_plan") {
                $business_item = $this->rowBusinessItemsById($value['production_plan_item_id']);
                $object_id = $business_item['business_plan_id'];
                $active = $business_item['active'];
                $staff_active = $business_item['staff_active'];
                $date_active = $business_item['date_active'];
            }

            $versions_stage = $value['versions_stage'];
            $vs = $this->products_model->getProductStagesByProductIdAndVersions($value['items_id'], $versions_stage);
            if (!empty($vs)) {
                $product_stages = $this->products_model->getProductStagesVersions($vs['id']);
                if (!empty($product_stages)) {
                    foreach ($product_stages as $i => $el) {
                        $active_temp = 0;
                        $staff_active_temp = 0;
                        $date_active_temp = null;
                        if ($el['stage_id'] == STAGE_PRINT_BARCODE) {
                            $active_temp = $active;
                            $staff_active_temp = $staff_active;
                            $date_active_temp = $date_active;
                        }

                        $stages[] = [
                            'productions_orders_id' => $productions_orders_id,
                            'productions_orders_items_id' => $value['id'],
                            'stage_id' => $el['stage_id'],
                            'machines' => $el['machines'],
                            'number' => $el['number'],
                            'number_hours' => $el['number_hours'],
                            'final_stage' => $el['final_stage'],
                            'object_type' => $object_type,
                            'object_item_id' => $value['production_plan_item_id'],
                            'object_id' => $object_id,
                            'active' => $active_temp,
                            'staff_active' => $staff_active_temp,
                            'date_active' => $date_active_temp,
                            'type' => $el['type'],
                            'face' => $el['face'],
                            'face_after' => $el['face_after'],
                            'number_face' => $el['number_face'],
                            'number_operations' => $el['number_operations'],
                            'number_cutting' => $el['number_cutting'],
                            'quota_time_f1' => $el['quota_time_f1'],
                            'quota_time_f2' => $el['quota_time_f2'],
                        ];
                    }
                }
            }
        }
        if (!empty($stages)) {
            $this->insertBatchProductionOrdersItemsStages($stages);
        }
        return true;
    }

    public function updateProductionsOrderItemsStages($id, $data) {
        $this->db->where('tbl_productions_orders_items_stages.id', $id);
        return $this->db->update('tbl_productions_orders_items_stages', $data);
    }

    public function getProductionsOrderItemsStagesById($id) {
        $this->db->select('*');
        $this->db->from('tbl_productions_orders_items_stages');
        $this->db->where('tbl_productions_orders_items_stages.id', $id);
        return $this->db->get()->row_array();
    }

    public function getProductionsPlanPOD($id) {
        $this->db->select('tbl_productions_plan.id, tbl_productions_plan.reference_no', false);
        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
        $this->db->join('tbl_productions_plan', 'tbl_productions_plan.id = tbl_productions_orders_items.plan_id');
        $this->db->where('tbl_productions_orders_details.id', $id);
        return $this->db->get()->row_array();
    }

    public function getWarehouseProductionsPlan($productions_plan_id, $item_type, $id_items, $warehouse_id = 0) {
        if ($item_type == "materials") {
            $item_type = "nvl";
        } else if ($item_type == "tools_supplies") {
            $item_type = "tools";
        } else {
            $item_type = "product";
        }
        $this->db->select('
            CONCAT(tbllocaltion_warehouses.warehouse, "__", tbllocaltion_warehouses.id) as id,
            CONCAT(tbllocaltion_warehouses.name, "(SL: ", tblwarehouse_items.product_quantity,")") as text,
            tblwarehouse_items.product_quantity as product_quantity
        ');
        $this->db->from('tblwarehouse_items');
        $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_items.localtion');
        $this->db->where('tblwarehouse_items.product_quantity >', 0);
        $this->db->where('tbllocaltion_warehouses.productions_plan_id =', $productions_plan_id);
        $this->db->where('tblwarehouse_items.type_items =', $item_type);
        $this->db->where('tblwarehouse_items.id_items =', $id_items);
        if (empty($warehouse_id)) {
            $this->db->where('tblwarehouse_items.warehouse_id =', WAREHOUSES_CAPACITY);
        } else {
            $this->db->where('tblwarehouse_items.warehouse_id =', $warehouse_id);
        }
        $rs = $this->db->get()->row_array();
        return $rs;
    }

    public function getWarehouseProductionsPlanNew($productions_plan_id, $item_type, $id_items, $warehouse_id = 0) {
        if ($item_type == "materials") {
            $item_type = "nvl";
        } else if ($item_type == "tools_supplies") {
            $item_type = "tools";
        } else {
            $item_type = "product";
        }
        $this->db->select("
            CONCAT(tblwarehouse_items.warehouse_id, '__', tblwarehouse_items.localtion, '__', coalesce(tblwarehouse_items.lot_code, 'NULL'), '__', coalesce(tblwarehouse_items.date_sx, 'NULL'), '__', coalesce(tblwarehouse_items.date_sd, 'NULL'), '__', coalesce(tblwarehouse_items.date_use, 'NULL')) as id,
            tbllocaltion_warehouses.name as text,
            SUM(tblwarehouse_items.product_quantity) as product_quantity,
            tbllocaltion_warehouses.name as name_localtion_warehouses,
            tblwarehouse_items.lot_code as lot_code, 
            tblwarehouse_items.date_sx as date_sx,
            tblwarehouse_items.date_sd as date_sd, 
            tblwarehouse_items.date_use as date_use,
            tblwarehouse_items.localtion
        ");
        $this->db->from('tblwarehouse_items');
        $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_items.localtion');
        $this->db->where('tblwarehouse_items.product_quantity >', 0);
        $this->db->where('tbllocaltion_warehouses.productions_plan_id =', $productions_plan_id);
        $this->db->where('tblwarehouse_items.type_items =', $item_type);
        $this->db->where('tblwarehouse_items.id_items =', $id_items);
        if (empty($warehouse_id)) {
            $this->db->where('tblwarehouse_items.warehouse_id =', WAREHOUSES_CAPACITY);
        } else {
            $this->db->where('tblwarehouse_items.warehouse_id =', $warehouse_id);
        }
        $this->db->group_by('tblwarehouse_items.localtion, tblwarehouse_items.lot_code, tblwarehouse_items.date_sx, tblwarehouse_items.date_sd, tblwarehouse_items.date_use');
        $rs = $this->db->get()->result_array();
        if (!empty($rs)) {
            foreach ($rs as $k => $val) {
                $name_localtion_warehouses = $val['name_localtion_warehouses'];
                $product_quantity = '(SL: '.$val['product_quantity'].')';
                $lot_code = $val['lot_code'] ? ' - Lot: '.$val['lot_code'] : '';
                $date_sx = $val['date_sx'] ? ' - Ngày SX: '._d($val['date_sx']) : '';
                $date_sd = $val['date_sd'] ? ' - Ngày SD: '._d($val['date_sd']) : '';

                $rs[$k]['text'] = $name_localtion_warehouses.$product_quantity.$lot_code.$date_sx.$date_sd;
            }
        }
        return $rs;
    }



    public function insertBatchPurchaseProductPoisub($data) {
        return $this->db->insert_batch('tbl_purchase_product_poisub', $data);
    }

    public function deletePurchaseProductPoisub($purchase_product_id) {
        $this->db->where('tbl_purchase_product_poisub.purchase_product_id', $purchase_product_id);
        return $this->db->delete('tbl_purchase_product_poisub');
    }

    public function getProductionsOrdersItemsStagesById($id)
    {
        $this->db->select('
            tbl_stages.name as stage_name,
            ', false);
        $this->db->from('tbl_productions_orders_items_stages');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id', 'left');
        $this->db->where('tbl_productions_orders_items_stages.id', $id);
        return $this->db->get()->row_array();
    }

    public function getWarehousePOD($pod_id, $item_type, $id_items, $warehouse_id = 0, $stage_id = 0) {
        if ($item_type == "materials") {
            $item_type = "nvl";
        } else if ($item_type == "tools_supplies") {
            $item_type = "tools";
        } else {
            $item_type = "product";
        }

        // CONCAT(tbllocaltion_warehouses.warehouse, "__", tbllocaltion_warehouses.id) as id,
        $this->db->select('
            CONCAT(tbllocaltion_warehouses.warehouse, "__", tbllocaltion_warehouses.id, "__", coalesce(tblwarehouse_items.lot_code, "NULL"), "__", coalesce(tblwarehouse_items.date_sx, "NULL"), "__", coalesce(tblwarehouse_items.date_sd, "NULL"), "__", coalesce(tblwarehouse_items.date_use, "NULL")) as id,
            CONCAT(tbllocaltion_warehouses.name, "(SL: ", tblwarehouse_items.product_quantity,")") as text,
            tblwarehouse_items.product_quantity as product_quantity
        ');
        $this->db->from('tblwarehouse_items');
        $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_items.localtion');
        $this->db->where('tblwarehouse_items.product_quantity >', 0);
        $this->db->where('tbllocaltion_warehouses.pod_id =', $pod_id);
        $this->db->where('tblwarehouse_items.type_items =', $item_type);
        $this->db->where('tblwarehouse_items.id_items =', $id_items);
        if (empty($warehouse_id)) {
            $this->db->where('tblwarehouse_items.warehouse_id =', WAREHOUSES_CAPACITY);
        } else {
            $this->db->where('tblwarehouse_items.warehouse_id =', $warehouse_id);
        }
        $this->db->where('tblwarehouse_items.warehouse_id !=', WAREHOUSES_ERRORS);
        $this->db->where('tbllocaltion_warehouses.stage_id =', $stage_id);
        // print_arrays($this->db->get_compiled_select());
        return $this->db->get()->row_array();
    }

    public function getProductionsOrdersItemsStagesByIdAll($id)
    {
        $this->db->select('
            tbl_productions_orders_items_stages.*,
            tbl_stages.name as stage_name,
        ', false);
        $this->db->from('tbl_productions_orders_items_stages');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id', 'left');
        $this->db->where('tbl_productions_orders_items_stages.id', $id);
        return $this->db->get()->row_array();
    }

    public function isWarehouses($item_id, $item_type, $productions_plan_id = 0) {
        if ($item_type == "materials") {
            $item_type = "nvl";
        } else if ($item_type == "tools_supplies") {
            $item_type = "tools";
        } else {
            $item_type = "product";
        }

        $this->db->select('
            count(tblwarehouse_items.id) as ct_warehouses
        ');
        $this->db->from('tblwarehouse_items');
        $this->db->where('tblwarehouse_items.product_quantity >', 0);
        $this->db->where('tblwarehouse_items.type_items =', $item_type);
        $this->db->where('tblwarehouse_items.id_items =', $item_id);
        if (!empty($productions_plan_id)) {
            $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_items.localtion');
            $this->db->group_start();
            $this->db->where('tbllocaltion_warehouses.productions_plan_id =', 0);
            $this->db->or_where('tbllocaltion_warehouses.productions_plan_id =', $productions_plan_id);
            $this->db->group_end();
        }
        return (float)$this->db->get()->row_array()['ct_warehouses'];
    }

    public function getExportMaterials($pod_id) {
        $tbItems = "(
            SELECT tb_cs.*
            FROM (
                (
                    SELECT
                        tbl_suggest_exporting_items.item_id as item_id,
                        tbl_suggest_exporting_items.type_item as type_item,
                        tbl_materials.code as item_code,
                        tbl_materials.name as item_name,
                        tbl_materials.images as images,
                        SUM(tbl_suggest_exporting_items.quantity_exchange) as quantity
                    FROM tbl_suggest_exporting
                    INNER JOIN tbl_suggest_exporting_items ON tbl_suggest_exporting_items.suggest_exporting_id = tbl_suggest_exporting.id
                    INNER JOIN tbl_materials ON tbl_suggest_exporting_items.item_id = tbl_materials.id
                    WHERE tbl_suggest_exporting_items.type_item = 'materials' AND tbl_suggest_exporting.productions_orders_details_id = ".$this->db->escape($pod_id)."
                    GROUP BY tbl_suggest_exporting_items.item_id
                )
                UNION ALL
                (
                    SELECT
                        tbl_suggest_exporting_items.item_id as item_id,
                        tbl_suggest_exporting_items.type_item as type_item,
                        tbl_tools_supplies.code as item_code,
                        tbl_tools_supplies.name as item_name,
                        tbl_tools_supplies.images as images,
                        SUM(tbl_suggest_exporting_items.quantity_exchange) as quantity
                    FROM tbl_suggest_exporting
                    INNER JOIN tbl_suggest_exporting_items ON tbl_suggest_exporting_items.suggest_exporting_id = tbl_suggest_exporting.id
                    INNER JOIN tbl_tools_supplies ON tbl_suggest_exporting_items.item_id = tbl_tools_supplies.id
                    WHERE tbl_suggest_exporting_items.type_item = 'tools_supplies' AND tbl_suggest_exporting.productions_orders_details_id = ".$this->db->escape($pod_id)."
                    GROUP BY tbl_suggest_exporting_items.item_id
                )
                UNION ALL
                (
                    SELECT
                        tbl_suggest_exporting_items.item_id as item_id,
                        tbl_suggest_exporting_items.type_item as type_item,
                        tbl_products.code as item_code,
                        tbl_products.name as item_name,
                        tbl_products.images as images,
                        SUM(tbl_suggest_exporting_items.quantity_exchange) as quantity
                    FROM tbl_suggest_exporting
                    INNER JOIN tbl_suggest_exporting_items ON tbl_suggest_exporting_items.suggest_exporting_id = tbl_suggest_exporting.id
                    INNER JOIN tbl_products ON tbl_suggest_exporting_items.item_id = tbl_products.id
                    WHERE tbl_suggest_exporting_items.type_item NOT IN ('tools_supplies', 'materials', 'products') AND tbl_suggest_exporting.productions_orders_details_id = ".$this->db->escape($pod_id)."
                    GROUP BY tbl_suggest_exporting_items.item_id
                )
            ) tb_cs
        )";

        return $this->db->query($tbItems)->result_array();
    }

    public function loadDataSemiProducts($pod_id, $production_plan_id, $tempQuantity = 0, $actions = '', $is_w = 1, $pois_id = 0) {

        $isWarehouses = 0;
        $quantityTotalProducts = 0;
        if ($actions == 'qc') {
            $this->db->select('
                tbl_productions_orders_items.quantity as quantity
            ', false);
            $this->db->from('tbl_productions_orders_details');
            $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
            $this->db->where('tbl_productions_orders_details.id', $pod_id);
            $dtPois = $this->db->get()->row_array();
            $quantityTotalProducts = $dtPois['quantity'];
        }

        $stage_id = 0;
        if (!empty($pois_id)) {
            $this->db->select('tbl_productions_orders_items_stages.*');
            $this->db->from('tbl_productions_orders_items_stages');
            $this->db->where('tbl_productions_orders_items_stages.id', $pois_id);
            $dtPOIS = $this->db->get()->row_array();
            if (!empty($dtPOIS)) {
                $stage_id = $dtPOIS['stage_id'];
            }
        }

        $this->db->select('
            CONCAT("pod__", tbl_products.id) as id,
            tbl_products.id as product_id,
            tbl_products.name as name,
            tbl_products.code as code,
            GROUP_CONCAT(DISTINCT tbl_productions_orders_items_sub.id) as poisub_id,
            tbl_productions_orders_items_sub.quantity_exchange as quantity_exchange,
            tbl_productions_orders_items_sub.quantity_single as quantity_single,
            SUM(tbl_productions_orders_items_sub.quantity_primary + tbl_productions_orders_items_sub.quantity_compensation_primary) as quantity_primary,
            SUM(tbl_productions_orders_items_sub.quantity + tbl_productions_orders_items_sub.quantity_compensation) as quantity,
            tbl_products.images as images,
            tbl_productions_orders_items_sub.unit_id as unit_id,
            tbl_productions_orders_items_sub.unit_parent_id as unit_parent_id,
            tbl_products.versions as versions
        ', false);
        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbl_productions_orders_items_sub', 'tbl_productions_orders_items_sub.productions_orders_items_id = tbl_productions_orders_details.productions_orders_item_id');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items_sub.item_id');
        $this->db->where('tbl_productions_orders_details.id', $pod_id);
        $this->db->where('tbl_productions_orders_items_sub.type', 'semi_products');
        // if (!empty($stage_id)) {
        //     $this->db->where_in('tbl_productions_orders_items_sub.stage_item_id', [0, $stage_id]);
        // }
        $this->db->group_by('tbl_products.id');
        $productions_orders_subs = $this->db->get()->result_array();
        // print_arrays($productions_orders_subs);
        $arrTempPOS = [];
        if (!empty($productions_orders_subs)) {
            $isWarehouses = 1;
            foreach ($productions_orders_subs as $key => $productions_orders_sub) {
                if (!empty($productions_orders_sub['images'])) {
                    $productions_orders_subs[$key]['images'] = base_url('uploads/products/'.$productions_orders_sub['images']);
                }

                $quantity = $productions_orders_sub['quantity'];
                $qtyW = 0;
                if ($actions == "qc") {
                    $quantitySemi = 0;
                    if (!empty($quantityTotalProducts)) {
                        $quantitySemi = $tempQuantity * $productions_orders_sub['quantity']/$quantityTotalProducts;
                    }
                    $productions_orders_sub['quantity'] = $quantitySemi;
                } else if ($is_w == 1) {
                    $this->db->select('SUM(tblwarehouse_items.product_quantity) as quantity_warehouses');
                    $this->db->from('tblwarehouse_items');
                    $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_items.localtion');
                    $this->db->where('tblwarehouse_items.warehouse_id', WAREHOUSES_CAPACITY);
                    $this->db->where('tbllocaltion_warehouses.productions_plan_id', $production_plan_id);
                    $this->db->where('tblwarehouse_items.id_items', $productions_orders_sub['product_id']);
                    $this->db->where('tblwarehouse_items.type_items', 'product');
                    $quantity_warehouse = $this->db->get()->row_array()['quantity_warehouses'];
                    if ($quantity_warehouse > 0) {
                        $tempQuantity = $quantity_warehouse - $quantity;
                        if ($tempQuantity >= 0) {
                            continue;
                        } else {
                            $quantity = abs($tempQuantity);
                            $qtyW = $quantity;
                        }
                    }

                    //
                }

                if ($actions != 'qc') {
                    $this->db->select('SUM(tbl_purchase_product_items.quantity) as quantity', false);
                    $this->db->from('tbl_purchase_products');
                    $this->db->join('tbl_purchase_product_items', 'tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id');
                    $this->db->where('tbl_purchase_products.pois_id', $pois_id);
                    $this->db->where('tbl_purchase_product_items.item_id', $productions_orders_sub['product_id']);
                    $purchase_products = $this->db->get()->row_array();
                    if (!empty($purchase_products)) {
                        $tempQuantity = (float)$purchase_products['quantity'] - $productions_orders_sub['quantity'] - $qtyW;
                        if ($tempQuantity >= 0) {
                            continue;
                        } else {
                            $quantity = abs($tempQuantity);
                        }
                        $productions_orders_sub['quantity'] = $quantity;
                    }
                }

                $poisub_id = $productions_orders_sub['poisub_id'];
                $query = "(
                    SELECT
                        CONCAT(sub1.type, '__', sub1.item_id) as item_cs_id,
                        sub1.type as item_type,
                        sub1.item_id as item_id,
                        (sub1.quantity_single/sub1.quantity_exchange) as quantity_singe_primary,
                        sub1.quantity_exchange as quantity_exchange,
                        sub1.quantity_single as quantity_single,
                        SUM(sub1.quantity_primary) as quantity_primary,
                        SUM(sub1.quantity + sub1.quantity_compensation + sub1.quantity_compensation_sm) as quantity,
                        sub1.unit_id as unit_id,
                        sub1.unit_parent_id as unit_parent_id,
                        sub1.is_single_use as is_single_use,
                        sub1.quantity_order as quantity_order,
                        sub1.quota_material_replace_t as quota_material_replace_t,
                        SUM(sub1.quantity_element) as quantity_element,
                        SUM(sub1.quantity_compensation_primary) as quantity_compensation_primary,
                        SUM(sub1.quantity_compensation + sub1.quantity_compensation_sm) as quantity_compensation,
                        unit_manufactures.unit as unit_name_manufactures,
                        SUM(sub1.landscape_print_size) as landscape_print_size,
                        SUM(sub1.vertical_print_size) as vertical_print_size,
                        SUM(sub1.number_children_size) as number_children_size,
                        SUM(sub1.paper_exchange) as paper_exchange

                    FROM tbl_productions_orders_items_sub
                    INNER JOIN tbl_productions_orders_items_sub sub1 ON sub1.parent_id = tbl_productions_orders_items_sub.id
                    LEFT JOIN tblunits unit_manufactures ON unit_manufactures.unitid = sub1.unit_id
                    WHERE tbl_productions_orders_items_sub.parent_id IN ('$poisub_id') AND tbl_productions_orders_items_sub.type_element_item = 2
                    GROUP BY sub1.type, sub1.item_id
                )";
                // print_arrays($query);
                $subItems = $this->db->query($query)->result_array();
                if (!empty($subItems)) {
                    foreach ($subItems as $k => $v) {
                        $item_type = $v['item_type'];
                        $item_id = $v['item_id'];
                        $images = '';

                        if ($item_type == "materials") {
                            $info = $this->items_model->rowMaterial($item_id);
                            if (!empty($info['images'])) {
                                $images = base_url('uploads/materials/'.$info['images']);
                            }
                        } else if ($item_type == "tools_supplies") {
                            $info = $this->tools_supplies_model->rowToolsSupplies($item_id);
                            if (!empty($info['images'])) {
                                $images = base_url('uploads/tools_supplies/'.$info['images']);
                            }
                        } else {
                            $info = $this->products_model->rowProduct($item_id);
                            if (!empty($info['images'])) {
                                $images = base_url('uploads/products/'.$info['images']);
                            }
                        }

                        if (empty($images)) {
                            $images = base_url('assets/images/tnh/no_image.png');
                        }

                        if ($v['is_single_use']) {
                            // $subItems[$k]['quantity'] = $v['quantity_order']/$v['quota_material_replace_t'];
                        } else {
                            if ($actions == "qc") {
                                $quantityM = 0;
                                if (!empty($quantityTotalProducts)) {
                                    $quantityM = $tempQuantity * $v['quantity']/$quantityTotalProducts;
                                }
                                $subItems[$k]['quantity'] = $quantityM;
                            } else if ($is_w == 1) {
                                // $quantitySub = $quantity * $v['quantity_single'];
                                // $subItems[$k]['quantity'] = $quantitySub;
                            }

                            if ($actions != "qc") {
                                $quantity_material = $productions_orders_sub['quantity']/$v['number_children_size'] * $v['quantity_single'];
                                $subItems[$k]['quantity'] = $quantity_material;
                            }
                        }


                        // $warehousePlan = $this->manufactures_model->getWarehouseProductionsPlanNew($production_plan_id, $item_type, $item_id);
                        // $isWarehouses = $this->manufactures_model->isWarehouses($item_id, $item_type, $production_plan_id);
                        $isWarehouses = $this->manufactures_model->isWarehouses($item_id, $item_type);
                        $warehouses_manufactures = $this->manufactures_model->getWarehouseManufactures($item_type, $item_id);

                        //warehouses
                        if ($item_type == "materials") {
                            $item_type = "nvl";
                        } else if ($item_type == "tools_supplies") {
                            $item_type = "tools";
                        } else {
                            $item_type = "product";
                        }
                        $this->db->select('tblwarehouse.id, tblwarehouse.name');
                        $this->db->from('tblwarehouse');
                        if (!empty($item_id)) {
                            $this->db->where('(
                                EXISTS (
                                    SELECT tblwarehouse_items.id
                                    FROM tblwarehouse_items
                                    WHERE tblwarehouse_items.type_items = "' . $item_type . '" AND tblwarehouse_items.id_items = "' . $item_id . '" AND tblwarehouse.id = tblwarehouse_items.warehouse_id
                                )
                            )');
                        }
                        $warehouses = $this->db->get()->result_array();
                        $w = [];
                        if (!empty($warehouses)) {
                            foreach ($warehouses as $kW => $vW) {
                                $warehouse_id = $vW['id'];

                                $tbQuantityWarehouses = '(
                                    SELECT
                                        tblwarehouse_items.localtion as localtion_id,
                                        SUM(tblwarehouse_items.product_quantity) as product_quantity
                                    FROM tblwarehouse_items
                                    WHERE tblwarehouse_items.type_items = "' . $item_type . '" AND tblwarehouse_items.id_items = "' . $item_id . '" AND tblwarehouse_items.warehouse_id = "' . $warehouse_id . '"
                                    GROUP BY tblwarehouse_items.localtion
                                ) tb_quantity_warehouses';

                                $this->db->select('
                                    CONCAT(tbllocaltion_warehouses.warehouse, "__", tbllocaltion_warehouses.id) as id,
                                    CONCAT(tbllocaltion_warehouses.name, "(SL: ", tb_quantity_warehouses.product_quantity,")") as text,
                                    tb_quantity_warehouses.product_quantity as product_quantity
                                ', false);
                                $this->db->from('tbllocaltion_warehouses');
                                $this->db->join($tbQuantityWarehouses, 'tb_quantity_warehouses.localtion_id = tbllocaltion_warehouses.id');
                                $this->db->where('tbllocaltion_warehouses.warehouse', $warehouse_id);
                                $this->db->where('tb_quantity_warehouses.product_quantity >', 0);
                                if (!empty($production_plan_id)) {
                                    $this->db->group_start();
                                    $this->db->where('tbllocaltion_warehouses.productions_plan_id', $production_plan_id);
                                    $this->db->or_where('tbllocaltion_warehouses.productions_plan_id', 0);
                                    $this->db->group_end();
                                }
                                $location_warehouses = $this->db->get()->result_array();
                                $w[] = ['text' => $vW['name'], 'children' => $location_warehouses];
                            }
                        }
                        $subItems[$k]['warehouses'] = $w;

                        $item_code = $info['code'];
                        $item_name = $info['name'];
                        $subItems[$k]['item_code'] = $item_code;
                        $subItems[$k]['item_name'] = $item_name;
                        $subItems[$k]['images'] = $images;
                        $subItems[$k]['warehousePlan'] = json_encode($warehouses_manufactures);
                        $subItems[$k]['isWarehouses'] = ($isWarehouses);
                    }
                } else {
                    $product_id = $productions_orders_sub['product_id'];
                    $versions = $productions_orders_sub['versions'];
                    $this->db->select("
                    tbl_product_versions.id as version_id,
                    ");
                    $this->db->from('tbl_product_versions');
                    $this->db->where('tbl_product_versions.product_id', $product_id);
                    $this->db->where('tbl_product_versions.versions', $versions);
                    $product_verions = $this->db->get()->row_array();
                    $product_verions = null;
                    if (!empty($product_verions)) {
                        $version_id = $product_verions['version_id'];
                        $this->db->select('
                            CONCAT(tbl_element_items.type, "__", tbl_element_items.item_id) as item_cs_id,
                            tbl_element_items.item_id as item_id,
                            tbl_element_items.type as item_type,
                            (tbl_versions_element.quantity * tbl_element_items.quantity) as quantity,
                            tbl_element_items.unit_id as unit_id,
                        ');
                        $this->db->from('tbl_versions_element');
                        $this->db->join('tbl_element_items', 'tbl_element_items.element_id = tbl_versions_element.id');
                        $this->db->where('tbl_versions_element.version_id', $version_id);
                        $this->db->group_by('tbl_element_items.item_id, tbl_element_items.type');
                        $versions_element = $this->db->get()->result_array();
                        if (!empty($versions_element)) {
                            foreach ($versions_element as $k => $v) {
                                $item_type = $v['item_type'];
                                $item_id = $v['item_id'];

                                $quantity_primary = $v['quantity'];
                                $quantity_singe_primary = $v['quantity'];

                                $images = '';
                                if ($item_type == "materials") {
                                    $info = $this->items_model->rowMaterial($item_id);
                                    $unit_id = $v['unit_id'];
                                    $row_exchange = $this->products_model->rowExchangeItems($item_id, $unit_id);
                                    $quantity_exchange = 1;
                                    if (!empty($row_exchange)) {
                                        $quantity_exchange = $row_exchange['number_exchange'];
                                    }
                                    $quantity_primary = $v['quantity'] / $quantity_exchange;
                                    if (!empty($info['images'])) {
                                        $images = base_url('uploads/materials/'.$info['images']);
                                    }
                                } else if ($item_type == "tools_supplies") {
                                    $info = $this->tools_supplies_model->rowToolsSupplies($item_id);
                                    if (!empty($info['images'])) {
                                        $images = base_url('uploads/tools_supplies/'.$info['images']);
                                    }
                                } else {
                                    $info = $this->products_model->rowProduct($item_id);
                                    if (!empty($info['images'])) {
                                        $images = base_url('uploads/products/'.$info['images']);
                                    }
                                }

                                if (empty($images)) {
                                    $images = base_url('assets/images/tnh/no_image.png');
                                }

                                $warehousePlan = $this->manufactures_model->getWarehouseProductionsPlanNew($production_plan_id, $item_type, $item_id);

                                $isWarehouses = $this->manufactures_model->isWarehouses($item_id, $item_type, $production_plan_id);

                                $item_code = $info['code'];
                                $item_name = $info['name'];
                                $versions_element[$k]['unit_parent_id'] = $info['unit_id'];
                                $versions_element[$k]['item_code'] = $item_code;
                                $versions_element[$k]['item_name'] = $item_name;
                                $versions_element[$k]['quantity_primary'] = $quantity_primary;
                                $versions_element[$k]['quantity_singe_primary'] = $quantity_primary;
                                $versions_element[$k]['quantity_exchange'] = $quantity_exchange;
                                $versions_element[$k]['quantity_single'] = $quantity_primary;
                                $versions_element[$k]['images'] = $images;
                                $versions_element[$k]['isWarehouses'] = $isWarehouses;
                                $versions_element[$k]['warehousePlan'] = json_encode($warehousePlan);

                                //warehouses
                                if ($item_type == "materials") {
                                    $item_type = "nvl";
                                } else if ($item_type == "tools_supplies") {
                                    $item_type = "tools";
                                } else {
                                    $item_type = "product";
                                }
                                $this->db->select('tblwarehouse.id, tblwarehouse.name');
                                $this->db->from('tblwarehouse');
                                if (!empty($item_id)) {
                                    $this->db->where('(
                                        EXISTS (
                                            SELECT tblwarehouse_items.id
                                            FROM tblwarehouse_items
                                            WHERE tblwarehouse_items.type_items = "' . $item_type . '" AND tblwarehouse_items.id_items = "' . $item_id . '" AND tblwarehouse.id = tblwarehouse_items.warehouse_id
                                        )
                                    )');
                                }
                                $warehouses = $this->db->get()->result_array();
                                $w = [];
                                if (!empty($warehouses)) {
                                    foreach ($warehouses as $kW => $vW) {
                                        $warehouse_id = $vW['id'];

                                        $tbQuantityWarehouses = '(
                                            SELECT
                                                tblwarehouse_items.localtion as localtion_id,
                                                SUM(tblwarehouse_items.product_quantity) as product_quantity
                                            FROM tblwarehouse_items
                                            WHERE tblwarehouse_items.type_items = "' . $item_type . '" AND tblwarehouse_items.id_items = "' . $item_id . '" AND tblwarehouse_items.warehouse_id = "' . $warehouse_id . '"
                                            GROUP BY tblwarehouse_items.localtion
                                        ) tb_quantity_warehouses';

                                        $this->db->select('
                                            CONCAT(tbllocaltion_warehouses.warehouse, "__", tbllocaltion_warehouses.id) as id,
                                            CONCAT(tbllocaltion_warehouses.name, "(SL: ", tb_quantity_warehouses.product_quantity,")") as text,
                                            tb_quantity_warehouses.product_quantity as product_quantity
                                        ', false);
                                        $this->db->from('tbllocaltion_warehouses');
                                        $this->db->join($tbQuantityWarehouses, 'tb_quantity_warehouses.localtion_id = tbllocaltion_warehouses.id');
                                        $this->db->where('tbllocaltion_warehouses.warehouse', $warehouse_id);
                                        $this->db->where('tb_quantity_warehouses.product_quantity >', 0);
                                        if (!empty($production_plan_id)) {
                                            $this->db->group_start();
                                            $this->db->where('tbllocaltion_warehouses.productions_plan_id', $production_plan_id);
                                            $this->db->or_where('tbllocaltion_warehouses.productions_plan_id', 0);
                                            $this->db->group_end();
                                        }
                                        $location_warehouses = $this->db->get()->result_array();
                                        $w[] = ['text' => $vW['name'], 'children' => $location_warehouses];
                                    }
                                }
                                $versions_element[$k]['warehouses'] = $w;
                            }
                        }
                    }
                    $subItems = !empty($versions_element) ? $versions_element : null;
                }
                $productions_orders_sub['subItems'] = $subItems;
                $arrTempPOS[$key] = $productions_orders_sub;
            }
        }
        $data['isWarehouses'] = $isWarehouses;
        $data['productions_orders_subs'] = $arrTempPOS;
        return $data;
    }

    public function isQuantityWarehouses($item_id, $item_type, $quantity = 0, $warehouse_id = 0, $productions_plan_id = 0, $location_id = 0,  $lot_code = NULL, $date_sx = NULL, $date_sd = NULL, $date_use = NULL) {
        if ($item_type == "materials") {
            $item_type = "nvl";
        } else if ($item_type == "tools_supplies") {
            $item_type = "tools";
        } else {
            $item_type = "product";
        }

        $this->db->select('
            tbllocaltion_warehouses.name as location_name,
            tblwarehouse.name as warehouses_name,
            tblwarehouse_items.product_quantity as product_quantity
        ');
        $this->db->from('tblwarehouse_items');
        $this->db->where('tblwarehouse_items.product_quantity <', $quantity, false);
        $this->db->where('tblwarehouse_items.type_items =', $item_type);
        $this->db->where('tblwarehouse_items.id_items =', $item_id);
        $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_items.localtion');
        $this->db->join('tblwarehouse', 'tblwarehouse.id = tblwarehouse_items.warehouse_id');
        if (!empty($productions_plan_id)) {
            $this->db->where('tbllocaltion_warehouses.productions_plan_id =', $productions_plan_id);
        }

        if (!empty($warehouse_id)) {
            $this->db->where('tblwarehouse_items.warehouse_id =', $warehouse_id);
        }

        if (!empty($location_id)) {
            $this->db->where('tblwarehouse_items.localtion =', $location_id);
        }

        $this->db->where('tblwarehouse_items.lot_code', $lot_code);
        $this->db->where('tblwarehouse_items.date_sx', $date_sx);
        $this->db->where('tblwarehouse_items.date_sd', $date_sd);
        $this->db->where('tblwarehouse_items.date_use', $date_use);
        return $this->db->get()->row_array();
    }

    public function loadDataProducts($pod_id, $production_plan_id, $tempQuantity = 0, $actions = '', $pois_id = 0) {

        $quantityTotalProducts = 0;
        if ($actions == 'qc') {
            $this->db->select('
                tbl_productions_orders_items.quantity as quantity
            ', false);
            $this->db->from('tbl_productions_orders_details');
            $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
            $this->db->where('tbl_productions_orders_details.id', $pod_id);
            $dtPois = $this->db->get()->row_array();
            $quantityTotalProducts = $dtPois['quantity'];
        }

        $stage_id = 0;
        if (!empty($pois_id)) {
            $this->db->select('tbl_productions_orders_items_stages.*');
            $this->db->from('tbl_productions_orders_items_stages');
            $this->db->where('tbl_productions_orders_items_stages.id', $pois_id);
            $dtPOIS = $this->db->get()->row_array();
            if (!empty($dtPOIS)) {
                $stage_id = $dtPOIS['stage_id'];
            }
        }

        $this->db->select('
            tbl_productions_orders_items.id as poi_id,
            CONCAT("pod__", tbl_products.id) as id,
            tbl_products.id as product_id,
            tbl_products.name as name,
            tbl_products.code as code,
            tbl_productions_orders_items.quantity as quantity,
            1 as quantity_primary,
            1 as quantity_single,
            1 as quantity_exchange,
            tbl_productions_orders_items.quantity as quantity_primary,
            tbl_products.images as images,
            tbl_products.unit_id as unit_id,
            tbl_products.unit_id as unit_parent_id,
            tbl_products.versions as versions,
            tbl_productions_orders_items.versions_bom as versions_bom,
            0 as poisub_id
        ', false);
        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
        $this->db->where('tbl_productions_orders_details.id', $pod_id);
        // $this->db->where('tbl_productions_orders_items.type_items', 'products');
        $this->db->group_by('tbl_products.id');
        $productions_orders_items = $this->db->get()->result_array();
        if (!empty($productions_orders_items)) {
            foreach ($productions_orders_items as $key => $poi) {
                $poi_id = $poi['poi_id'];
                if (!empty($poi['images'])) {
                    $productions_orders_items[$key]['images'] = base_url('uploads/products/'.$poi['images']);
                }
				$productions_orders_items[$key]['stage_id'] = $stage_id;

                if ($actions == "qc") {
                    $quantitySemi = 0;
                    if (!empty($quantityTotalProducts)) {
                        $quantitySemi = $tempQuantity * $poi['quantity']/$quantityTotalProducts;
                    }
                    $productions_orders_items[$key]['quantity'] = $quantitySemi;
                } else {
                    $this->db->select('SUM(tbl_purchase_product_items.quantity) as quantity', false);
                    $this->db->from('tbl_purchase_products');
                    $this->db->join('tbl_purchase_product_items', 'tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id');
                    $this->db->where('tbl_purchase_products.pois_id', $pois_id);
                    $this->db->where('tbl_purchase_product_items.item_id', $poi['product_id']);
                    $purchase_products = $this->db->get()->row_array();
                    if (!empty($purchase_products)) {
                        $tempQuantity = (float)$purchase_products['quantity'] - $poi['quantity'];
                        if ($tempQuantity >= 0) {
                            continue;
                        } else {
                            $quantity = abs($tempQuantity);
                        }
                        $productions_orders_items[$key]['quantity'] = $quantity;
                    }
                }

                $poisub_id = '0';
                $whereMore = '';
                if (!empty($stage_id)) {
                    $whereMore = ' AND (sub1.stage_item_id IN (0, '.$stage_id.') OR sub1.type = "semi_products") ';
                }

                $query = "(
                    SELECT
                        CONCAT(sub1.type, '__', sub1.item_id) as item_cs_id,
                        sub1.type as item_type,
                        sub1.item_id as item_id,
                        (sub1.quantity_single/sub1.quantity_exchange) as quantity_singe_primary,
                        sub1.quantity_exchange as quantity_exchange,
                        sub1.quantity_single as quantity_single,
                        SUM(sub1.quantity_primary) as quantity_primary,
                        SUM(sub1.quantity + sub1.quantity_compensation + sub1.quantity_compensation_sm) as quantity,
                        sub1.unit_id as unit_id,
                        sub1.unit_parent_id as unit_parent_id,
                        sub1.is_single_use as is_single_use,
                        sub1.quantity_order as quantity_order,
                        sub1.quota_material_replace_t as quota_material_replace_t,
                        SUM(sub1.quantity_element) as quantity_element,
                        SUM(sub1.quantity_compensation_primary) as quantity_compensation_primary,
                        SUM(sub1.quantity_compensation + sub1.quantity_compensation_sm) as quantity_compensation,
                        unit_manufactures.unit as unit_name_manufactures,
                        SUM(sub1.landscape_print_size) as landscape_print_size,
                        SUM(sub1.vertical_print_size) as vertical_print_size,
                        SUM(sub1.number_children_size) as number_children_size,
                        SUM(sub1.paper_exchange) as paper_exchange

                    FROM tbl_productions_orders_items_sub
                    INNER JOIN tbl_productions_orders_items_sub sub1 ON sub1.parent_id = tbl_productions_orders_items_sub.id
                    LEFT JOIN tblunits unit_manufactures ON unit_manufactures.unitid = sub1.unit_id
                    WHERE tbl_productions_orders_items_sub.productions_orders_items_id = '$poi_id' AND tbl_productions_orders_items_sub.type IN ('element') AND tbl_productions_orders_items_sub.parent_id = 0 $whereMore AND (sub1.type_element_item = 2 OR sub1.type = 'semi_products')
                    GROUP BY sub1.type, sub1.item_id
                )";
                $arrRemoveSemiProduct = [];
                $subItems = $this->db->query($query)->result_array();
                $subItems = [];
                if (!empty($subItems)) {
                    foreach ($subItems as $k => $v) {
                        $item_type = $v['item_type'];
                        $item_id = $v['item_id'];
                        $images = '';

                        $type_purchase_product = '';
                        if ($item_type == "element") {
                            continue;
                        } else if ($item_type == "materials") {
                            $info = $this->items_model->rowMaterial($item_id);
                            if (!empty($info['images'])) {
                                $images = base_url('uploads/materials/'.$info['images']);
                            }
                        } else if ($item_type == "tools_supplies") {
                            $info = $this->tools_supplies_model->rowToolsSupplies($item_id);
                            if (!empty($info['images'])) {
                                $images = base_url('uploads/tools_supplies/'.$info['images']);
                            }
                        } else {
                            $arrRemoveSemiProduct[] = $item_id;
                            $info = $this->products_model->rowProduct($item_id);
                            $type_purchase_product = "products";
                            if (!empty($info['images'])) {
                                $images = base_url('uploads/products/'.$info['images']);
                            }
                        }

                        if (empty($images)) {
                            $images = base_url('assets/images/tnh/no_image.png');
                        }

                        $this->db->select('tbl_purchase_products.warehouse_id');
                        $this->db->from('tbl_purchase_product_items');
                        $this->db->join('tbl_purchase_products', 'tbl_purchase_products.id = tbl_purchase_product_items.purchase_product_id');
                        $this->db->where('tbl_purchase_product_items.productions_orders_details_id', $pod_id);
                        $this->db->where('tbl_purchase_product_items.type_item', $type_purchase_product);
                        $this->db->where('tbl_purchase_product_items.item_id', $item_id);
                        $this->db->order_by('tbl_purchase_product_items.id DESC');
                        $this->db->limit(1);
                        $purchase_product = $this->db->get()->row_array();

                        $warehousePlan = $this->manufactures_model->getWarehouseProductionsPlanNew($production_plan_id, $item_type, $item_id);
                        $warehousePOD = $this->manufactures_model->getWarehousePOD($pod_id, $item_type, $item_id, $purchase_product['warehouse_id']);
                        if (!empty($warehousePOD)) {
                            $warehousePOD[] = $warehousePOD;
                        }

                        $isWarehouses = $this->manufactures_model->isWarehouses($item_id, $item_type);
                        $warehouses_manufactures = $this->manufactures_model->getWarehouseManufactures($item_type, $item_id);
                        
                        //warehouses
                        if ($item_type == "materials") {
                            $item_type = "nvl";
                        } else if ($item_type == "tools_supplies") {
                            $item_type = "tools";
                        } else {
                            $item_type = "product";
                        }
                        $this->db->select('tblwarehouse.id, tblwarehouse.name');
                        $this->db->from('tblwarehouse');
                        if (!empty($item_id)) {
                            $this->db->where('(
                                EXISTS (
                                    SELECT tblwarehouse_items.id
                                    FROM tblwarehouse_items
                                    WHERE tblwarehouse_items.type_items = "' . $item_type . '" AND tblwarehouse_items.id_items = "' . $item_id . '" AND tblwarehouse.id = tblwarehouse_items.warehouse_id
                                )
                            )');
                        }
                        $warehouses = $this->db->get()->result_array();
                        $w = [];
                        if (!empty($warehouses)) {
                            foreach ($warehouses as $kW => $vW) {
                                $warehouse_id = $vW['id'];

                                $tbQuantityWarehouses = '(
                                    SELECT
                                        tblwarehouse_items.localtion as localtion_id,
                                        SUM(tblwarehouse_items.product_quantity) as product_quantity
                                    FROM tblwarehouse_items
                                    WHERE tblwarehouse_items.type_items = "' . $item_type . '" AND tblwarehouse_items.id_items = "' . $item_id . '" AND tblwarehouse_items.warehouse_id = "' . $warehouse_id . '"
                                    GROUP BY tblwarehouse_items.localtion
                                ) tb_quantity_warehouses';

                                $this->db->select('
                                    CONCAT(tbllocaltion_warehouses.warehouse, "__", tbllocaltion_warehouses.id) as id,
                                    CONCAT(tbllocaltion_warehouses.name, "(SL: ", tb_quantity_warehouses.product_quantity,")") as text,
                                    tb_quantity_warehouses.product_quantity as product_quantity
                                ', false);
                                $this->db->from('tbllocaltion_warehouses');
                                $this->db->join($tbQuantityWarehouses, 'tb_quantity_warehouses.localtion_id = tbllocaltion_warehouses.id');
                                $this->db->where('tbllocaltion_warehouses.warehouse', $warehouse_id);
                                $this->db->where('tb_quantity_warehouses.product_quantity >', 0);
                                $location_warehouses = $this->db->get()->result_array();
                                $w[] = ['text' => $vW['name'], 'children' => $location_warehouses];
                            }
                        }
                        $subItems[$k]['warehouses'] = $w;

                        if ($actions == "qc") {
                            $quantityM = 0;
                            if (!empty($quantityTotalProducts)) {
                                $quantityM = $tempQuantity * $v['quantity']/$quantityTotalProducts;
                            }
                            $subItems[$k]['quantity'] = $quantityM;
                        } else {
                            $quantity_material = $productions_orders_items[$key]['quantity']/$v['number_children_size'] * $v['quantity_single'];
                            $subItems[$k]['quantity'] = $quantity_material;
                        }

                        $item_code = $info['code'];
                        $item_name = $info['name'];
                        $subItems[$k]['item_code'] = $item_code;
                        $subItems[$k]['item_name'] = $item_name;
                        $subItems[$k]['images'] = $images;
                        $subItems[$k]['isWarehouses'] = $isWarehouses;
                        $subItems[$k]['warehousePlan'] = (!empty($warehousePOD)) ? json_encode($warehousePOD) : json_encode($warehouses_manufactures);
                    }
                }

                //Lấy các BTP bổ sung thêm
                $this->db->select("
                    CONCAT(tbl_products.type_products, '__', tbl_products.id) as item_cs_id,
                    tbl_products.type_products as item_type,
                    tbl_products.id as item_id,
                    1 as quantity_singe_primary,
                    1 as quantity_exchange,
                    1 as quantity_single,
                    SUM(tblwarehouse_items.product_quantity) as quantity_primary,
                    SUM(tblwarehouse_items.product_quantity) as quantity,
                    tbl_products.unit_id as unit_id,
                    tbl_products.unit_id as unit_parent_id,
                    0 as is_single_use,
                    0 as quantity_order,
                    0 as quota_material_replace_t
                ", false);
                $this->db->from('tblwarehouse_items');
                $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_items.localtion');
                $this->db->join('tbl_products', 'tbl_products.id = tblwarehouse_items.id_items');
                $this->db->where('tblwarehouse_items.type_items', 'product');
                $this->db->where('tbl_products.type_products', 'semi_products');
                $this->db->where('tbllocaltion_warehouses.pod_id', $pod_id);
                $this->db->where('tblwarehouse_items.product_quantity >', 0);
                if (!empty($arrRemoveSemiProduct)) {
                    $this->db->where_not_in('tbl_products.id', $arrRemoveSemiProduct);
                }
                $this->db->group_by('tbl_products.id');
                $semi_product_more = $this->db->get()->result_array();
                $semi_product_more = [];
                if (!empty($semi_product_more)) {
                    foreach ($semi_product_more as $k => $v) {
                        $item_type = $v['item_type'];
                        $item_id = $v['item_id'];
                        $images = '';

                        $arrRemoveSemiProduct[] = $item_id;
                        $info = $this->products_model->rowProduct($item_id);
                        $type_purchase_product = "products";
                        if (!empty($info['images'])) {
                            $images = base_url('uploads/products/'.$info['images']);
                        }

                        if (empty($images)) {
                            $images = base_url('assets/images/tnh/no_image.png');
                        }

                        $this->db->select('tbl_purchase_products.warehouse_id, tbl_purchase_product_items.quantity_single');
                        $this->db->from('tbl_purchase_product_items');
                        $this->db->join('tbl_purchase_products', 'tbl_purchase_products.id = tbl_purchase_product_items.purchase_product_id');
                        $this->db->where('tbl_purchase_product_items.productions_orders_details_id', $pod_id);
                        $this->db->where('tbl_purchase_product_items.type_item', $type_purchase_product);
                        $this->db->where('tbl_purchase_product_items.item_id', $item_id);
                        $this->db->order_by('tbl_purchase_product_items.id DESC');
                        $this->db->limit(1);
                        $purchase_product = $this->db->get()->row_array();

                        $warehousePlan = $this->manufactures_model->getWarehouseProductionsPlanNew($production_plan_id, $item_type, $item_id);
                        $warehousePOD = $this->manufactures_model->getWarehousePOD($pod_id, $item_type, $item_id, $purchase_product['warehouse_id']);
                        if (!empty($warehousePOD)) {
                            $warehousePOD[] = $warehousePOD;
                        }

                        $isWarehouses = $this->manufactures_model->isWarehouses($item_id, $item_type);

                        //warehouses
                        if ($item_type == "materials") {
                            $item_type = "nvl";
                        } else if ($item_type == "tools_supplies") {
                            $item_type = "tools";
                        } else {
                            $item_type = "product";
                        }
                        $this->db->select('tblwarehouse.id, tblwarehouse.name');
                        $this->db->from('tblwarehouse');
                        if (!empty($item_id)) {
                            $this->db->where('(
                                EXISTS (
                                    SELECT tblwarehouse_items.id
                                    FROM tblwarehouse_items
                                    WHERE tblwarehouse_items.type_items = "' . $item_type . '" AND tblwarehouse_items.id_items = "' . $item_id . '" AND tblwarehouse.id = tblwarehouse_items.warehouse_id
                                )
                            )');
                        }
                        $warehouses = $this->db->get()->result_array();
                        $w = [];
                        if (!empty($warehouses)) {
                            foreach ($warehouses as $kW => $vW) {
                                $warehouse_id = $vW['id'];

                                $tbQuantityWarehouses = '(
                                    SELECT
                                        tblwarehouse_items.localtion as localtion_id,
                                        SUM(tblwarehouse_items.product_quantity) as product_quantity
                                    FROM tblwarehouse_items
                                    WHERE tblwarehouse_items.type_items = "' . $item_type . '" AND tblwarehouse_items.id_items = "' . $item_id . '" AND tblwarehouse_items.warehouse_id = "' . $warehouse_id . '"
                                    GROUP BY tblwarehouse_items.localtion
                                ) tb_quantity_warehouses';

                                $this->db->select('
                                    CONCAT(tbllocaltion_warehouses.warehouse, "__", tbllocaltion_warehouses.id) as id,
                                    CONCAT(tbllocaltion_warehouses.name, "(SL: ", tb_quantity_warehouses.product_quantity,")") as text,
                                    tb_quantity_warehouses.product_quantity as product_quantity
                                ', false);
                                $this->db->from('tbllocaltion_warehouses');
                                $this->db->join($tbQuantityWarehouses, 'tb_quantity_warehouses.localtion_id = tbllocaltion_warehouses.id');
                                $this->db->where('tbllocaltion_warehouses.warehouse', $warehouse_id);
                                $this->db->where('tb_quantity_warehouses.product_quantity >', 0);
                                $location_warehouses = $this->db->get()->result_array();
                                $w[] = ['text' => $vW['name'], 'children' => $location_warehouses];
                            }
                        }
                        $semi_product_more[$k]['warehouses'] = $w;

                        if ($actions == "qc") {
                            $quantityM = 0;
                            if (!empty($quantityTotalProducts)) {
                                $quantityM = $tempQuantity * $v['quantity']/$quantityTotalProducts;
                            }
                            $semi_product_more[$k]['quantity'] = $quantityM;
                        } else {
                            $quantitySub = $productions_orders_items[$key]['quantity'] * $v['quantity_single'];
                            $semi_product_more[$k]['quantity'] = $quantitySub;
                        }

                        $item_code = $info['code'];
                        $item_name = $info['name'];
                        $semi_product_more[$k]['quantity_single'] = $purchase_product['quantity_single'];
                        $semi_product_more[$k]['item_code'] = $item_code;
                        $semi_product_more[$k]['item_name'] = $item_name;
                        $semi_product_more[$k]['images'] = $images;
                        $semi_product_more[$k]['isWarehouses'] = $isWarehouses;
                        $semi_product_more[$k]['warehousePlan'] = (!empty($warehousePOD)) ? json_encode($warehousePOD) : json_encode($warehousePlan);
                    }
                    $subItems = array_merge($subItems, $semi_product_more);
                    
                }
                $productions_orders_items[$key]['subItems'] = $subItems;
            }
        }
        return $productions_orders_items;
    }

    public function loadDataProductsStep($pod_id, $pois_id, $tempQuantity = 0, $actions = '') {

        $quantityTotalProducts = 0;
        if ($actions == 'qc') {
            $this->db->select('
                tbl_productions_orders_items.quantity as quantity
            ', false);
            $this->db->from('tbl_productions_orders_details');
            $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
            $this->db->where('tbl_productions_orders_details.id', $pod_id);
            $dtPois = $this->db->get()->row_array();
            $quantityTotalProducts = $dtPois['quantity'];
        }

        $dtPois = $this->manufactures_model->getProductionsOrdersItemsStagesByIdAll($pois_id);
        $stage_id_current = $dtPois['stage_id'];
        $poi_id = $dtPois['productions_orders_items_id'];
        $number = $dtPois['number'] - 1;
        $this->db->select('
            tbl_productions_orders_items_stages.id as id,
            tbl_stages.id as stage_id,
            tbl_stages.name as stage_name
        ');
        $this->db->from('tbl_productions_orders_items_stages');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id', 'left');
        $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $poi_id);
        $this->db->where('tbl_productions_orders_items_stages.number', $number);
        $dtPoisPre = $this->db->get()->row_array();
        // print_arrays($dtPoisPre);
        $stage_id = $dtPoisPre['stage_id'];
        $this->db->select('
            tbl_productions_orders_items.id as poi_id,
            CONCAT("pod__", tbl_products.id) as id,
            tbl_products.id as product_id,
            tbl_products.name as name,
            tbl_products.code as code,
            tbl_productions_orders_items.quantity as quantity,
            1 as quantity_primary,
            1 as quantity_single,
            1 as quantity_exchange,
            tbl_productions_orders_items.quantity as quantity_primary,
            tbl_products.images as images,
            tbl_products.unit_id as unit_id,
            tbl_products.unit_id as unit_parent_id,
            tbl_products.versions as versions,
            tbl_productions_orders_items.versions_bom as versions_bom,
            0 as poisub_id
        ', false);
        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
        $this->db->where('tbl_productions_orders_details.id', $pod_id);
        // $this->db->where('tbl_productions_orders_items.type_items', 'products');
        $this->db->group_by('tbl_products.id');
        $productions_orders_items = $this->db->get()->result_array();
        if (!empty($productions_orders_items)) {
            foreach ($productions_orders_items as $key => $poi) {
                $poi_id = $poi['poi_id'];
                if (!empty($poi['images'])) {
                    $productions_orders_items[$key]['images'] = base_url('uploads/products/'.$poi['images']);
                }
                $productions_orders_items[$key]['stage_name'] = $dtPois['stage_name'];
                $productions_orders_items[$key]['stage_id'] = $dtPois['stage_id'];
                $poisub_id = '0';
                $quantityProduct = $poi['quantity_primary'];

                if ($actions == "qc") {
                    $quantitySemi = 0;
                    if (!empty($quantityTotalProducts)) {
                        $quantitySemi = $tempQuantity * $poi['quantity']/$quantityTotalProducts;
                    }
                    $productions_orders_items[$key]['quantity'] = $quantitySemi;
                }

                $whereQC = 'AND tbl_purchase_products.type < 10';
                if ($actions == 'qc') {
                    $whereQC = 'AND tbl_purchase_products.type >= 10';
                }

                // $query = "(
                //     SELECT
                //         CONCAT(tbl_purchase_product_items.type_item, '__', tbl_purchase_product_items.item_id) as item_cs_id,
                //         tbl_purchase_product_items.type_item as item_type,
                //         tbl_purchase_product_items.item_id as item_id,
                //         (tbl_purchase_product_items.quantity_single/tbl_purchase_product_items.quantity_exchange) as quantity_singe_primary,
                //         tbl_purchase_product_items.quantity_exchange as quantity_exchange,
                //         tbl_purchase_product_items.quantity_single as quantity_single,
                //         SUM(tbl_purchase_product_items.quantity) as quantity_primary,
                //         SUM(tbl_purchase_product_items.quantity) as quantity,
                //         0 as unit_id,
                //         0 as unit_parent_id,
                //         GROUP_CONCAT(DISTINCT tbl_purchase_products.warehouse_id) as warehouse_id,
                //         0 as is_single_use,
                //         0 as quantity_order,
                //         0 as quota_material_replace_t
                //     FROM tbl_purchase_products
                //     INNER JOIN tbl_purchase_product_items ON tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id
                //     WHERE tbl_purchase_products.productions_orders_details_id = '$pod_id' AND tbl_purchase_products.pois_id = '".$dtPoisPre['id']."' AND tbl_purchase_products.is_errors = 0 $whereQC
                //     GROUP BY tbl_purchase_product_items.type_item, tbl_purchase_product_items.item_id
                // )";
                // $subItems = $this->db->query($query)->result_array();

                $query = "(
                    SELECT
                        CONCAT('products', '__', tblwarehouse_items.id_items) as item_cs_id,
                        tblwarehouse_items.type_items as item_type,
                        tblwarehouse_items.id_items as item_id,
                        1 as quantity_singe_primary,
                        1 as quantity_exchange,
                        1 as quantity_single,
                        SUM(tblwarehouse_items.product_quantity) as quantity_primary,
                        SUM(tblwarehouse_items.product_quantity) as quantity,
                        0 as unit_id,
                        0 as unit_parent_id,
                        GROUP_CONCAT(DISTINCT tblwarehouse_items.warehouse_id) as warehouse_id,
                        0 as is_single_use,
                        0 as quantity_order,
                        0 as quota_material_replace_t,
                        1 as landscape_print_size,
                        1 as vertical_print_size,
                        1 as number_children_size,
                        1 as paper_exchange
                    FROM tblwarehouse_items
                    INNER JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion
                    WHERE tblwarehouse_items.type_items = 'product' AND tbllocaltion_warehouses.pod_id = $pod_id AND tbllocaltion_warehouses.stage_id = $stage_id AND tblwarehouse_items.warehouse_id 
                    != ".WAREHOUSES_ERRORS." 
                    GROUP BY tblwarehouse_items.id_items, tblwarehouse_items.type_items
                )";
                $subItems = $this->db->query($query)->result_array();

                if (!empty($subItems)) {
                    foreach ($subItems as $k => $v) {
                        $item_type = $v['item_type'];
                        $item_id = $v['item_id'];
                        $images = '';

                        if (!empty($v)) {
                            $productions_orders_items[$key]['quantity'] = $v['quantity_primary'];
                            $quantity_sx = $v['quantity_primary'];
                        }

                        $type_purchase_product = '';
                        $info = $this->products_model->rowProduct($item_id);
                        $type_purchase_product = "products";
                        if (!empty($info['images'])) {
                            $images = base_url('uploads/products/'.$info['images']);
                        }

                        if (empty($images)) {
                            $images = base_url('assets/images/tnh/no_image.png');
                        }

                        $warehouse_id = $v['warehouse_id'];
                        $arrWarehouse = explode(',', $warehouse_id);
                        $arrW = [];
                        foreach ($arrWarehouse as $kW => $vW) {
                            if (!empty($arrW)) continue;
                            $warehousePOD = $this->manufactures_model->getWarehousePOD($pod_id, $item_type, $item_id, $vW, $stage_id);
                            if (!empty($warehousePOD)) {
                                $arrW[$kW]['warehousePlan'] = $warehousePOD;
                            }
                        }

                        // print_arrays($arrW);

                        $isWarehouses = $this->manufactures_model->isWarehouses($item_id, $item_type);

                        //warehouses
                        if ($item_type == "materials") {
                            $item_type = "nvl";
                        } else if ($item_type == "tools_supplies") {
                            $item_type = "tools";
                        } else {
                            $item_type = "product";
                        }
                        $this->db->select('tblwarehouse.id, tblwarehouse.name');
                        $this->db->from('tblwarehouse');
                        if (!empty($item_id)) {
                            $this->db->where('(
                                EXISTS (
                                    SELECT tblwarehouse_items.id
                                    FROM tblwarehouse_items
                                    WHERE tblwarehouse_items.type_items = "' . $item_type . '" AND tblwarehouse_items.id_items = "' . $item_id . '" AND tblwarehouse.id = tblwarehouse_items.warehouse_id
                                )
                            )');
                        }
                        $warehouses = $this->db->get()->result_array();
                        $w = [];
                        if (!empty($warehouses)) {
                            foreach ($warehouses as $kW => $vW) {
                                $warehouse_id = $vW['id'];

                                $tbQuantityWarehouses = '(
                                    SELECT
                                        tblwarehouse_items.localtion as localtion_id,
                                        SUM(tblwarehouse_items.product_quantity) as product_quantity
                                    FROM tblwarehouse_items
                                    WHERE tblwarehouse_items.type_items = "' . $item_type . '" AND tblwarehouse_items.id_items = "' . $item_id . '" AND tblwarehouse_items.warehouse_id = "' . $warehouse_id . '"
                                    GROUP BY tblwarehouse_items.localtion
                                ) tb_quantity_warehouses';

                                $this->db->select('
                                    CONCAT(tbllocaltion_warehouses.warehouse, "__", tbllocaltion_warehouses.id) as id,
                                    CONCAT(tbllocaltion_warehouses.name, "(SL: ", tb_quantity_warehouses.product_quantity,")") as text,
                                    tb_quantity_warehouses.product_quantity as product_quantity
                                ', false);
                                $this->db->from('tbllocaltion_warehouses');
                                $this->db->join($tbQuantityWarehouses, 'tb_quantity_warehouses.localtion_id = tbllocaltion_warehouses.id');
                                $this->db->where('tbllocaltion_warehouses.warehouse', $warehouse_id);
                                $this->db->where('tb_quantity_warehouses.product_quantity >', 0);
                                $location_warehouses = $this->db->get()->result_array();
                                $w[] = ['text' => $vW['name'], 'children' => $location_warehouses];
                            }
                        }
                        $subItems[$k]['warehouses'] = $w;

                        if ($actions == "qc") {
                            $quantitySemi = 0;
                            if (!empty($quantityTotalProducts)) {
                                $quantitySemi = $tempQuantity * $v['quantity']/$quantityTotalProducts;
                            }
                            $subItems[$k]['quantity'] = $quantitySemi;
                        } else {
                            if ($v['quantity'] > $quantityProduct) {
                                $subItems[$k]['quantity'] = $quantityProduct;
                            }
                        }

                        $item_code = $info['code'];
                        $item_name = $info['name'];
                        $subItems[$k]['stage_name'] = $dtPoisPre['stage_name'];
                        $subItems[$k]['unit_id'] = $info['unit_id'];
                        $subItems[$k]['unit_parent_id'] = $info['unit_id'];
                        $subItems[$k]['item_code'] = $item_code;
                        $subItems[$k]['item_name'] = $item_name;
                        $subItems[$k]['images'] = $images;
                        $subItems[$k]['arrW'] = $arrW;
                        $subItems[$k]['isWarehouses'] = $isWarehouses;
                        $subItems[$k]['warehousePlan'] = json_encode($arrW);

                    }
                }
                

                if (!empty($stage_id_current)) {
                    //NVL BTP more

                    $productions_plan = $this->manufactures_model->getProductionsPlanPOD($pod_id);
                    $production_plan_id = $productions_plan['id'];
                    $whereMore = ' AND sub1.stage_item_id IN ('.$stage_id_current.') ';

                    $query = "(
                        SELECT
                            CONCAT(sub1.type, '__', sub1.item_id) as item_cs_id,
                            sub1.type as item_type,
                            sub1.item_id as item_id,
                            (sub1.quantity_single/sub1.quantity_exchange) as quantity_singe_primary,
                            sub1.quantity_exchange as quantity_exchange,
                            sub1.quantity_single as quantity_single,
                            SUM(sub1.quantity_primary) as quantity_primary,
                            SUM(sub1.quantity) as quantity,
                            sub1.unit_id as unit_id,
                            sub1.unit_parent_id as unit_parent_id,
                            sub1.is_single_use as is_single_use,
                            sub1.quantity_order as quantity_order,
                            sub1.quota_material_replace_t as quota_material_replace_t,
                            SUM(sub1.landscape_print_size) as landscape_print_size,
                            SUM(sub1.vertical_print_size) as vertical_print_size,
                            SUM(sub1.number_children_size) as number_children_size,
                            SUM(sub1.paper_exchange) as paper_exchange

                        FROM tbl_productions_orders_items_sub
                        INNER JOIN tbl_productions_orders_items_sub sub1 ON sub1.parent_id = tbl_productions_orders_items_sub.id
                        WHERE tbl_productions_orders_items_sub.productions_orders_items_id = '$poi_id' AND tbl_productions_orders_items_sub.type IN ('element') AND tbl_productions_orders_items_sub.parent_id = 0 $whereMore
                        GROUP BY sub1.type, sub1.item_id
                    )";
                    $arrRemoveSemiProduct = [];
                    $subItemsMore = $this->db->query($query)->result_array();
                    $subItemsMore = [];
                    if (!empty($subItemsMore)) {
                        foreach ($subItemsMore as $k => $v) {
                            $item_type = $v['item_type'];
                            $item_id = $v['item_id'];
                            $images = '';
    
                            $type_purchase_product = '';
                            if ($item_type == "element") {
                                continue;
                            } else if ($item_type == "materials") {
                                $info = $this->items_model->rowMaterial($item_id);
                                if (!empty($info['images'])) {
                                    $images = base_url('uploads/materials/'.$info['images']);
                                }
                            } else if ($item_type == "tools_supplies") {
                                $info = $this->tools_supplies_model->rowToolsSupplies($item_id);
                                if (!empty($info['images'])) {
                                    $images = base_url('uploads/tools_supplies/'.$info['images']);
                                }
                            } else {
                                $arrRemoveSemiProduct[] = $item_id;
                                $info = $this->products_model->rowProduct($item_id);
                                $type_purchase_product = "products";
                                if (!empty($info['images'])) {
                                    $images = base_url('uploads/products/'.$info['images']);
                                }
                            }
    
                            if (empty($images)) {
                                $images = base_url('assets/images/tnh/no_image.png');
                            }
    
                            $this->db->select('tbl_purchase_products.warehouse_id');
                            $this->db->from('tbl_purchase_product_items');
                            $this->db->join('tbl_purchase_products', 'tbl_purchase_products.id = tbl_purchase_product_items.purchase_product_id');
                            $this->db->where('tbl_purchase_product_items.productions_orders_details_id', $pod_id);
                            $this->db->where('tbl_purchase_product_items.type_item', $type_purchase_product);
                            $this->db->where('tbl_purchase_product_items.item_id', $item_id);
                            $this->db->order_by('tbl_purchase_product_items.id DESC');
                            $this->db->limit(1);
                            $purchase_product = $this->db->get()->row_array();
    
                            // $warehousePlan = $this->manufactures_model->getWarehouseProductionsPlan($production_plan_id, $item_type, $item_id);
                            $warehousePlan = $this->manufactures_model->getWarehouseProductionsPlanNew($production_plan_id, $item_type, $item_id);
                            $warehousePOD = $this->manufactures_model->getWarehousePOD($pod_id, $item_type, $item_id, $purchase_product['warehouse_id']);
                            if (!empty($warehousePOD)) {
                                $warehousePOD[] = $warehousePOD;
                            }
    
                            $isWarehouses = $this->manufactures_model->isWarehouses($item_id, $item_type);
    
                            //warehouses
                            if ($item_type == "materials") {
                                $item_type = "nvl";
                            } else if ($item_type == "tools_supplies") {
                                $item_type = "tools";
                            } else {
                                $item_type = "product";
                            }
                            $this->db->select('tblwarehouse.id, tblwarehouse.name');
                            $this->db->from('tblwarehouse');
                            if (!empty($item_id)) {
                                $this->db->where('(
                                    EXISTS (
                                        SELECT tblwarehouse_items.id
                                        FROM tblwarehouse_items
                                        WHERE tblwarehouse_items.type_items = "' . $item_type . '" AND tblwarehouse_items.id_items = "' . $item_id . '" AND tblwarehouse.id = tblwarehouse_items.warehouse_id
                                    )
                                )');
                            }
                            $warehouses = $this->db->get()->result_array();
                            $w = [];
                            if (!empty($warehouses)) {
                                foreach ($warehouses as $kW => $vW) {
                                    $warehouse_id = $vW['id'];
    
                                    $tbQuantityWarehouses = '(
                                        SELECT
                                            tblwarehouse_items.localtion as localtion_id,
                                            SUM(tblwarehouse_items.product_quantity) as product_quantity
                                        FROM tblwarehouse_items
                                        WHERE tblwarehouse_items.type_items = "' . $item_type . '" AND tblwarehouse_items.id_items = "' . $item_id . '" AND tblwarehouse_items.warehouse_id = "' . $warehouse_id . '"
                                        GROUP BY tblwarehouse_items.localtion
                                    ) tb_quantity_warehouses';
    
                                    $this->db->select('
                                        CONCAT(tbllocaltion_warehouses.warehouse, "__", tbllocaltion_warehouses.id) as id,
                                        CONCAT(tbllocaltion_warehouses.name, "(SL: ", tb_quantity_warehouses.product_quantity,")") as text,
                                        tb_quantity_warehouses.product_quantity as product_quantity
                                    ', false);
                                    $this->db->from('tbllocaltion_warehouses');
                                    $this->db->join($tbQuantityWarehouses, 'tb_quantity_warehouses.localtion_id = tbllocaltion_warehouses.id');
                                    $this->db->where('tbllocaltion_warehouses.warehouse', $warehouse_id);
                                    $this->db->where('tb_quantity_warehouses.product_quantity >', 0);
                                    $location_warehouses = $this->db->get()->result_array();
                                    $w[] = ['text' => $vW['name'], 'children' => $location_warehouses];
                                }
                            }
                            $subItemsMore[$k]['warehouses'] = $w;
    
                            if ($actions == "qc") {
                                $quantityM = 0;
                                if (!empty($quantityTotalProducts)) {
                                    $quantityM = $tempQuantity * $v['quantity']/$quantityTotalProducts;
                                }
                                $subItemsMore[$k]['quantity'] = $quantityM;
                            } else {
                                $quantityM = $productions_orders_items[$key]['quantity'] * $v['quantity_single'];
                                $subItemsMore[$k]['quantity'] = $quantityM;
                            }

                            // $arrWarehouse = [WAREHOUSES_CAPACITY];
                            // $arrW = [];
                            // foreach ($arrWarehouse as $kW => $vW) {
                            //     if (!empty($arrW)) continue;
                            //     $warehousePOD = $this->manufactures_model->getWarehousePOD($pod_id, $item_type, $item_id, $vW, $stage_id);
                            //     $arrW[$kW]['warehousePlan'] = $warehousePOD;
                            // }
                            // $arrW[0]['warehousePlan'] = (!empty($warehousePOD)) ? ($warehousePOD) : ($warehousePlan);
                            
                            $arrPlan = [];
                            if ($warehousePlan) {
                                foreach ($warehousePlan as $kk => $vv) {
                                    $arrPlan[]['warehousePlan'] = $vv;
                                }
                            }
                            // print_arrays($arrPlan);
                            if (!empty($warehousePOD)) {
                                $arrW[0]['warehousePlan'] = $warehousePOD;
                            } else {
                                $arrW = $arrPlan;
                            }
                            // $arrW[0]['warehousePlan'] = (!empty($warehousePOD)) ? ($warehousePOD) : ($arrPlan);
    
                            $item_code = $info['code'];
                            $item_name = $info['name'];
                            $subItemsMore[$k]['item_code'] = $item_code;
                            $subItemsMore[$k]['item_name'] = $item_name;
                            $subItemsMore[$k]['images'] = $images;
                            $subItemsMore[$k]['isWarehouses'] = $isWarehouses;
                            // $subItemsMore[$k]['warehousePlan'] = (!empty($warehousePOD)) ? json_encode($warehousePOD) : json_encode($warehousePlan);
                            $subItemsMore[$k]['warehousePlan'] = json_encode($arrW);
                            $subItemsMore[$k]['arrW'] = $arrW;
                        }
                        $subItems = array_merge($subItems, $subItemsMore);
                    }
                }

                $productions_orders_items[$key]['subItems'] = $subItems;
            }
        }
        return $productions_orders_items;
    }

    public function getCheckQualityItemsById($id) {
        $this->db->select('tbl_check_quality_items.*');
        $this->db->from('tbl_check_quality_items');
        $this->db->where('tbl_check_quality_items.id', $id);
        return $this->db->get()->row_array();
    }

    public function insertQCRemake($data) {
        $this->db->insert('tbl_qc_remake', $data);
        return $this->db->insert_id();
    }

    public function getQCRemakeCQIId($check_quality_items_id) {
        $this->db->select('
            tbl_qc_remake.*,
            CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name,
            tblstaff.profile_image as profile_image
        ', false);
        $this->db->from('tbl_qc_remake');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_qc_remake.staff_status', 'left');
        $this->db->where('tbl_qc_remake.check_quality_items_id', $check_quality_items_id);
        return $this->db->get()->row_array();
    }

    public function deleteQCRemakeCqiId($check_quality_items_id) {
        $this->db->where('check_quality_items_id', $check_quality_items_id);
        return $this->db->delete('tbl_qc_remake');
    }

    public function updateQCRemake($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('tbl_qc_remake', $data);
    }

    public function getStagesPO($productions_orders_id) {
        $this->db->select('
            tbl_productions_orders_items_stages.stage_id, tbl_stages.name as name, tbl_stages.code as code, tbl_stages.id as id
        ', false);
        $this->db->from('tbl_productions_orders_items_stages');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
        $this->db->where('tbl_stages.parent_id', 0);
        $this->db->where('tbl_productions_orders_items_stages.productions_orders_id', $productions_orders_id);
        // $this->db->order_by('tbl_productions_orders_items_stages.number ASC');
        $this->db->order_by('tbl_productions_orders_items_stages.final_stage ASC, tbl_productions_orders_items_stages.number ASC');
        $this->db->group_by('tbl_productions_orders_items_stages.stage_id');
        return $this->db->get()->result_array();
    }

    //
    public function getHandlingPOISSemiProducts($arrPOIS) {
        $this->db->select("
            tbl_productions_orders_items_stages.id as id,
            tbl_productions_orders_details.id as pod_id,
            tbl_productions_orders_items.id as poi_id,
            tbl_productions_orders_items_stages.id as pois_id,
            tbl_products.name as item_name,
            tbl_products.code as item_code,
            tbl_products.images as images,
            tbl_productions_plan.id as pp_id,
            tbl_productions_orders_items_stages.number as number
        ", false);
        $this->db->from('tbl_productions_orders_items_stages');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_items_stages.productions_orders_items_id');
        $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id');
        $this->db->join('tbl_productions_plan', 'tbl_productions_plan.id = tbl_productions_orders_items.plan_id', 'left');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
        if (empty($arrPOIS)) {
            $this->db->where('tbl_productions_orders_items_stages.id', 0);
        } else {
            $this->db->where_in('tbl_productions_orders_items_stages.id', $arrPOIS);
        }
        $this->db->where('tbl_productions_orders_items_stages.active', 0);
        $items = $this->db->get()->result_array();
        return $items;
    }

    public function getProductionsPlanItemsView($id) {
        $this->db->select('
            tbl_productions_plan_items.id, 
            tbl_productions_plan_items.quantity_total_details,
            tbl_productions_plan_items.quantity_reserve,
            tbl_products.name as item_name,
            tbl_products.code as item_code,
            tbl_products.images as images,
            tblunits.unit as unit_name
        ', false);
        $this->db->from('tbl_productions_plan_items');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_plan_items.product_id');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');
        $this->db->where('tbl_productions_plan_items.productions_plan_id', $id);
        $ppi = $this->db->get()->result_array();
        return $ppi;
    }

    public function getProductionsPlanBom($productions_plan_items_id, $arrItemType = [], $productions_plan_id = 0) {
        $this->db->select('tbl_productions_plan_bom.*, tblunits.unit as unit_name', false);
        $this->db->from('tbl_productions_plan_bom');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_productions_plan_bom.unit_id');
        if (!empty($productions_plan_items_id)) {
            $this->db->where('tbl_productions_plan_bom.productions_plan_items_id', $productions_plan_items_id);
        }
        if (!empty($productions_plan_id)) {
            $this->db->where('tbl_productions_plan_bom.productions_plan_id', $productions_plan_id);
        }
        $this->db->where_in('tbl_productions_plan_bom.item_type', $arrItemType);
        return $this->db->get()->result_array();
    }

    public function getWarehousesPlanBom($production_plan_id, $type, $type_warehouse) {
        $warehouse = [];
        $this->db->simple_query('SET SESSION group_concat_max_len=1500000000');
        $this->db->select('
            GROUP_CONCAT(distinct tbl_productions_plan_bom.item_id) as item_id
        ');
        $this->db->from('tbl_productions_plan_bom');
        $this->db->where('tbl_productions_plan_bom.productions_plan_id', $production_plan_id);
        $this->db->where('tbl_productions_plan_bom.item_type', $type);
        $productions_plan_bom = $this->db->get()->row_array();

        $this->db->select('
            tblwarehouse_items.id_items as id_items,
            SUM(tblwarehouse_items.product_quantity) as product_quantity
        ');
        $this->db->from('tblwarehouse_items');
        $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_items.localtion');
        $this->db->where_in('tblwarehouse_items.id_items', explode(',', $productions_plan_bom['item_id']));
        $this->db->where('tblwarehouse_items.type_items', $type_warehouse);
        $this->db->where_in('tbllocaltion_warehouses.productions_plan_id', [$production_plan_id, 0]);
        $this->db->where('tbllocaltion_warehouses.pod_id', 0);
        $this->db->group_by('tblwarehouse_items.id_items');
        $warehouse_items = $this->db->get()->result_array();
        if (!empty($warehouse_items)) {
            foreach ($warehouse_items as $key => $value) {
                $index = $type.'__'.$value['id_items'];
                $warehouse[$index] = [
                    'item_type' => $type,
                    'item_id' => $value['id_items'],
                    'quantity' => $value['product_quantity'],
                ];
            }
        }

        return $warehouse;
    }

    public function getMaterialBomSemiProduct($arrSemiProduct) {
        $arrMaterial = [];
        return $arrMaterial;

        if (!empty($arrSemiProduct)) {
            foreach ($arrSemiProduct as $key => $value) {
                $quantitySemi = $value;
                $this->db->select('tbl_productions_plan_bom.id as id, tbl_productions_plan_bom.quantity_single');
                $this->db->from('tbl_productions_plan_bom');
                $this->db->where('tbl_productions_plan_bom.parent_id', $key);
                $element = $this->db->get()->result_array();
                if (!empty($element)) {
                    foreach ($element as $k => $val) {
                        $quantityE = $quantitySemi / $val['quantity_single'];
                        $this->db->select('
                            tbl_productions_plan_bom.id as id, 
                            tbl_productions_plan_bom.quantity_single, 
                            tbl_productions_plan_bom.quantity_exchange, 
                            tbl_productions_plan_bom.item_id as item_id,
                            tbl_productions_plan_bom.is_single_use as is_single_use,
                            tbl_productions_plan_bom.quota_material_replace_t as quota_material_replace_t,
                            tbl_productions_plan_bom.quantity_order as quantity_order,
                            tbl_productions_plan_bom.quantity_compensation as quantity_compensation,
                            tbl_materials.exchange_unit as exchange_unit
                        ');
                        $this->db->from('tbl_productions_plan_bom');
                        $this->db->join('tbl_materials', 'tbl_materials.id = tbl_productions_plan_bom.item_id');
                        $this->db->where('tbl_productions_plan_bom.parent_id', $val['id']);
                        $this->db->where('tbl_productions_plan_bom.item_type', 'materials');
                        $material = $this->db->get()->result_array();
                        if (!empty($material)) {
                            foreach ($material as $kk => $vv) {
                                $is_single_use = $vv['is_single_use'];
                                if ($is_single_use > 0) {
                                    $quantityMaterial = 0;
                                    // $quantity = ($quantityE * $vv['quantity_single']) + $vv['quantity_compensation'];
                                    // $quantityMaterial = $quantity/$vv['quota_material_replace_t'] * $vv['quantity_single'];
                                    // $quantityMaterial = $quantityMaterial/$vv['quantity_exchange'];
                                    $quantity = ($quantityE * $vv['quantity_single']) + $vv['quantity_compensation'];
                                    $quantityMaterial = ceil($quantity/$vv['quota_material_replace_t'] * $vv['quantity_single']);

                                    $quantityMaterial = ceil($quantityMaterial * $vv['quantity_exchange'] / $vv['exchange_unit']);
                                    
                                } else {
                                    $quantity = ($quantityE * $vv['quantity_single']) + $vv['quantity_compensation'];
                                    $quantityMaterial = 0;
                                    if (!empty($vv['quantity_exchange'])) {
                                        // $quantityMaterial = $quantity/$vv['quantity_exchange'];
                                        $quantityMaterial = $quantity * $vv['quantity_exchange'] / $vv['exchange_unit'];
                                    }
                                }

                                $index = 'materials__'.$vv['item_id'];
                                if (!empty($arrMaterial[$index])) {
                                    $arrMaterial[$index]['quantity'] = $arrMaterial[$index]['quantity'] + $quantityMaterial;
                                } else {
                                    $arrMaterial[$index]['quantity'] = $quantityMaterial;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $arrMaterial;
    }

    public function getTotalQuantitW($id_items, $type_items, $location_id, $warehouse_id, $lot_code = NULL, $date_sx = NULL, $date_sd = NULL, $date_use = NULL)
    {
        if ($type_items == "materials") {
            $type_items = 'nvl';
        } else if ($type_items == "tools_supplies") {
            $type_items = 'tools';
        } else if ($type_items == "items") {
            $type_items = 'items';
        } else {
            $type_items = 'product';
        }

        $this->db->select('SUM(tblwarehouse_items.product_quantity) as total_quantity', false);
        $this->db->from('tblwarehouse_items');
        $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_items.localtion');
        $this->db->where('tblwarehouse_items.id_items', $id_items);
        $this->db->where('tblwarehouse_items.type_items', $type_items);
        $this->db->where('tblwarehouse_items.localtion', $location_id);
        $this->db->where('tblwarehouse_items.warehouse_id', $warehouse_id);

        $this->db->where('tblwarehouse_items.lot_code', $lot_code);
        $this->db->where('tblwarehouse_items.date_sx', $date_sx);
        $this->db->where('tblwarehouse_items.date_sd', $date_sd);
        $this->db->where('tblwarehouse_items.date_use', $date_use);
        // print_arrays($this->db->get_compiled_select());
        return $this->db->get()->row_array();
    }

    public function isQCPre($pod_id, $poi_id, $pois_id, $number, $isOne = false) {
        $number_pre = $number - 1;

        if ($isOne == true) {
            $this->db->select('
                tbl_productions_orders_items_stages.stage_id,
                tbl_stages.name as stage_name,
                tbl_stages.status_qc as status_qc,
                tbl_productions_orders_details.id as pod_id
            ', false);
            $this->db->from('tbl_productions_orders_items_stages');
            $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items_stages.productions_orders_items_id');
            $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
            $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $poi_id);
            $this->db->where('tbl_productions_orders_items_stages.number <', $number);
            $dtPois = $this->db->get()->result_array();
            $isQC = 4;
            if (!empty($dtPois)) {
                foreach ($dtPois as $key => $pois) {
                    // if (empty($pod_id)) $pod_id = $pois['pod_id'];
                    $pod_id = $pois['pod_id'];
                    $stage_id = $pois['stage_id'];
                    $stage_name = $pois['stage_name'];
                    $status_qc = $pois['status_qc'];
                    if (!empty($status_qc)) {
                        $isQC = 1;
                        $this->db->select('
                            tbl_check_quality_items.id as id,
                            tbl_check_quality_items.result as result,
                        ');
                        $this->db->from('tbl_check_quality_items');
                        $this->db->where('tbl_check_quality_items.pod_id', $pod_id);
                        $this->db->where('tbl_check_quality_items.id_stage', $stage_id);
                        $dbQC = $this->db->get()->result_array();
                        if (!empty($dbQC)) {
                            foreach ($dbQC as $k => $v) {
                                $result = $v['result'];
                                if ($result == 2) {
                                    $isQC = 2;
                                    $this->db->from('tbl_check_quality_items_stage');
                                    $this->db->group_start();
                                    $this->db->where('tbl_check_quality_items_stage.status_result', 0);
                                    $this->db->group_end();
                                    $this->db->where('tbl_check_quality_items_stage.check_quality_items_id', $v['id']);
                                    $qc_remake = $this->db->count_all_results();
                                    if (!empty($qc_remake)) {
                                        $isQC = 3;
                                        return $isQC;
                                    } else {
                                        $isQC = 1;
                                    }
                                }
                            }
                        } else {
                            $isQC = 2;
                        }

                        //
                        $this->db->from('tbl_check_quality_items_stage');
                        $this->db->where('tbl_check_quality_items_stage.status_result', 0);
                        $this->db->where('tbl_check_quality_items_stage.pod_id', $pod_id);
                        $this->db->where('tbl_check_quality_items_stage.stage_id', $stage_id);
                        $qc_remake = $this->db->count_all_results();
                        if (!empty($qc_remake)) {
                            $isQC = 3;
                            return $isQC;
                        }
                    }
                }
                return $isQC;
            }
            return 4;
        }

        $query = "
            SELECT
                tbl_productions_orders_items.object_item_type as object_item_type,
                tbl_order_items.order_id as object_id,
                tbl_productions_orders_items.productions_orders_id as productions_orders_id
            FROM tbl_productions_orders_items
            INNER JOIN tbl_order_items ON tbl_order_items.id = tbl_productions_orders_items.production_plan_item_id
            WHERE tbl_productions_orders_items.id = '$poi_id' AND tbl_productions_orders_items.object_item_type = 'orders'

            UNION ALL

            SELECT
                tbl_productions_orders_items.object_item_type as object_item_type,
                tbl_business_plan_items.business_plan_id as object_id,
                tbl_productions_orders_items.productions_orders_id as productions_orders_id
            FROM tbl_productions_orders_items
            INNER JOIN tbl_business_plan_items ON tbl_business_plan_items.id = tbl_productions_orders_items.production_plan_item_id
            WHERE tbl_productions_orders_items.id = '$poi_id' AND tbl_productions_orders_items.object_item_type = 'business_plan'
        ";
        $object = $this->db->query($query)->row_array();
        
        $object_id = $object['object_id'];
        $productions_orders_id = $object['productions_orders_id'];
        $object_item_type = $object['object_item_type'];
        
        if ($object_item_type == "orders") {
            $this->db->select('tbl_productions_orders_items.id');
            $this->db->from('tbl_order_items');
            $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.production_plan_item_id = tbl_order_items.id');
            $this->db->where('tbl_productions_orders_items.productions_orders_id', $productions_orders_id);
            $this->db->where('tbl_order_items.order_id', $object_id);
            $this->db->where('tbl_productions_orders_items.object_item_type', $object_item_type);
            $items = $this->db->get()->result_array();
        } else {
            $this->db->select('tbl_productions_orders_items.id');
            $this->db->from('tbl_business_plan_items');
            $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.production_plan_item_id = tbl_business_plan_items.id');
            $this->db->where('tbl_productions_orders_items.productions_orders_id', $productions_orders_id);
            $this->db->where('tbl_business_plan_items.business_plan_id', $object_id);
            $this->db->where('tbl_productions_orders_items.object_item_type', $object_item_type);
            $items = $this->db->get()->result_array();
        }

        $isQC = 4;
        if (!empty($items)) {
            foreach ($items as $kI => $vI) {
                $poi_id = $vI['id'];
                $this->db->select('
                    tbl_productions_orders_items_stages.stage_id,
                    tbl_stages.name as stage_name,
                    tbl_stages.status_qc as status_qc,
                    tbl_productions_orders_details.id as pod_id
                ', false);
                $this->db->from('tbl_productions_orders_items_stages');
                $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items_stages.productions_orders_items_id');
                $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
                $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $poi_id);
                $this->db->where('tbl_productions_orders_items_stages.number <', $number);
                $dtPois = $this->db->get()->result_array();
                // $isQC = 4;
                if (!empty($dtPois)) {
                    foreach ($dtPois as $key => $pois) {
                        // if (empty($pod_id)) $pod_id = $pois['pod_id'];
                        $pod_id = $pois['pod_id'];
                        $stage_id = $pois['stage_id'];
                        $stage_name = $pois['stage_name'];
                        $status_qc = $pois['status_qc'];
                        if (!empty($status_qc)) {
                            $isQC = 1;
                            $this->db->select('
                                tbl_check_quality_items.id as id,
                                tbl_check_quality_items.result as result,
                            ');
                            $this->db->from('tbl_check_quality_items');
                            $this->db->where('tbl_check_quality_items.pod_id', $pod_id);
                            $this->db->where('tbl_check_quality_items.id_stage', $stage_id);
                            $dbQC = $this->db->get()->result_array();
                            if (!empty($dbQC)) {
                                foreach ($dbQC as $k => $v) {
                                    $result = $v['result'];
                                    if ($result == 2) {
                                        $isQC = 2;
                                        $this->db->from('tbl_check_quality_items_stage');
                                        $this->db->group_start();
                                        $this->db->where('tbl_check_quality_items_stage.status_result', 0);
                                        $this->db->group_end();
                                        $this->db->where('tbl_check_quality_items_stage.check_quality_items_id', $v['id']);
                                        $qc_remake = $this->db->count_all_results();
                                        if (!empty($qc_remake)) {
                                            $isQC = 3;
                                            return $isQC;
                                        } else {
                                            $isQC = 1;
                                        }
                                    }
                                }
                            } else {
                                $isQC = 2;
                            }

                            //
                            $this->db->from('tbl_check_quality_items_stage');
                            $this->db->where('tbl_check_quality_items_stage.status_result', 0);
                            $this->db->where('tbl_check_quality_items_stage.pod_id', $pod_id);
                            $this->db->where('tbl_check_quality_items_stage.stage_id', $stage_id);
                            $qc_remake = $this->db->count_all_results();
                            if (!empty($qc_remake)) {
                                $isQC = 3;
                                return $isQC;
                            }
                        }
                    }
                    // return $isQC;
                }
            }
            return $isQC;
        }

        return 4;
    }

    public function updateFinishedStagesOutsourcing($outsourcing_id, $active = 1) {
        $staff_active = get_staff_user_id();
        $date_active = date('Y-m-d h:i:s');
        $arrUpdate = [];
        $this->db->select('
            tbl_outsource_items.pod_id as pod_id,
            tbl_outsource_items.id_stage as stage_id,
        ');
        $this->db->from('tbl_outsource');
        $this->db->join('tbl_outsource_items', 'tbl_outsource_items.outsource_id = tbl_outsource.id');
        $this->db->where('tbl_outsource.id', $outsourcing_id);
        $items = $this->db->get()->result_array();
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $pod_id = $value['pod_id'];
                $stage_id = $value['stage_id'];

                $this->db->select('
                    tbl_productions_orders_items_stages.id as pois_id
                ', false);
                $this->db->from('tbl_productions_orders_details');
                $this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.productions_orders_items_id = tbl_productions_orders_details.productions_orders_item_id');
                $this->db->where('tbl_productions_orders_details.id', $pod_id);
                $this->db->where('tbl_productions_orders_items_stages.stage_id', $stage_id);
                $this->db->where('tbl_productions_orders_items_stages.active', 0);
                $pois = $this->db->get()->result_array();
                if (!empty($pois)) {
                    foreach ($pois as $k => $val) {
                        if ($active == 1) {
                            $arrUpdate[] = [
                                'id' => $val['pois_id'],
                                'staff_active' => $staff_active,
                                'date_active' => $date_active,
                                'active' => $active,
                            ];
                        } else {
                            $arrUpdate[] = [
                                'id' => $val['pois_id'],
                                'staff_active' => 0,
                                'date_active' => null,
                                'active' => 0,
                            ];
                        }
                    }
                }
            }
        }

        if (!empty($arrUpdate)) {
            $this->db->update_batch('tbl_productions_orders_items_stages', $arrUpdate, 'id');
        }
        return true;
    }

    public function searchW($item_type, $item_id, $production_plan_id = 0) {
        if ($item_type == "materials") {
            $item_type = "nvl";
        } else if ($item_type == "tools_supplies") {
            $item_type = "tools";
        } else {
            $item_type = "product";
        }

        $results = [];
        $this->db->select('tblwarehouse.id, tblwarehouse.name');
        $this->db->from('tblwarehouse');
        if (!empty($item_id)) {
            $this->db->where('(
                EXISTS (
                    SELECT tblwarehouse_items.id
                    FROM tblwarehouse_items
                    WHERE tblwarehouse_items.type_items = "'.$item_type.'" AND tblwarehouse_items.id_items = "'.$item_id.'" AND tblwarehouse.id = tblwarehouse_items.warehouse_id
                )
            )');
        }
        $warehouses = $this->db->get()->result_array();
        if (!empty($warehouses)) {
            foreach ($warehouses as $key => $value) {
                $warehouse_id = $value['id'];

                $tbQuantityWarehouses = '(
                    SELECT
                        tblwarehouse_items.localtion as localtion_id,
                        SUM(tblwarehouse_items.product_quantity) as product_quantity
                    FROM tblwarehouse_items
                    WHERE tblwarehouse_items.type_items = "'.$item_type.'" AND tblwarehouse_items.id_items = "'.$item_id.'" AND tblwarehouse_items.warehouse_id = "'.$warehouse_id.'"
                    GROUP BY tblwarehouse_items.localtion
                ) tb_quantity_warehouses';

                $this->db->select('
                    tbllocaltion_warehouses.productions_plan_id as productions_plan_id,
                    CONCAT(tbllocaltion_warehouses.warehouse, "__", tbllocaltion_warehouses.id) as id,
                    CONCAT(tbllocaltion_warehouses.name, "(SL: ", tb_quantity_warehouses.product_quantity,")") as text,
                    tbllocaltion_warehouses.name as location_name,
                    tb_quantity_warehouses.product_quantity as product_quantity
                ', false);
                $this->db->from('tbllocaltion_warehouses');
                $this->db->join($tbQuantityWarehouses, 'tb_quantity_warehouses.localtion_id = tbllocaltion_warehouses.id');
                $this->db->where('tbllocaltion_warehouses.warehouse', $warehouse_id);
                $this->db->where('tb_quantity_warehouses.product_quantity >', 0);
                if (!empty($production_plan_id)) {
                    $this->db->group_start();
                    $this->db->where('tbllocaltion_warehouses.productions_plan_id', $production_plan_id);
                    $this->db->or_where('tbllocaltion_warehouses.productions_plan_id', 0);
                    $this->db->group_end();
                }
                $location_warehouses = $this->db->get()->result_array();
                $results[] = ['text' => $value['name'], 'children' => $location_warehouses];
            }
        }
        $data['results'] = $results;
        return $data;
    }

    public function updateFinishedStagesImportOutsourcing($import_outsource_id, $active = 1) {
        $staff_active = get_staff_user_id();
        $date_active = date('Y-m-d h:i:s');
        $arrUpdate = [];
        $this->db->select('
            tbl_import_outsource_items.pod_id as pod_id,
            tbl_import_outsource_items.stage_id_default as stage_id,
        ');
        $this->db->from('tbl_import_outsource');
        $this->db->join('tbl_import_outsource_items', 'tbl_import_outsource_items.import_outsource_id = tbl_import_outsource.id');
        $this->db->where('tbl_import_outsource.id', $import_outsource_id);
        $items = $this->db->get()->result_array();
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $pod_id = $value['pod_id'];
                $stage_id = $value['stage_id'];

                $this->db->select('
                    tbl_productions_orders_items_stages.id as pois_id
                ', false);
                $this->db->from('tbl_productions_orders_details');
                $this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.productions_orders_items_id = tbl_productions_orders_details.productions_orders_item_id');
                $this->db->where('tbl_productions_orders_details.id', $pod_id);
                $this->db->where('tbl_productions_orders_items_stages.stage_id', $stage_id);
                $this->db->where('tbl_productions_orders_items_stages.active', 0);
                $pois = $this->db->get()->result_array();
                if (!empty($pois)) {
                    foreach ($pois as $k => $val) {
                        if ($active == 1) {
                            $arrUpdate[] = [
                                'id' => $val['pois_id'],
                                'staff_active' => $staff_active,
                                'date_active' => $date_active,
                                'active' => $active,
                            ];
                        } else {
                            $arrUpdate[] = [
                                'id' => $val['pois_id'],
                                'staff_active' => 0,
                                'date_active' => null,
                                'active' => 0,
                            ];
                        }
                    }
                }
            }
        }

        if (!empty($arrUpdate)) {
            $this->db->update_batch('tbl_productions_orders_items_stages', $arrUpdate, 'id');
        }
        return true;
    }

    public function getOrderItemByDelivery($poi_id) {
        $this->db->select('
                tbl_productions_orders_items.id as poi_id,
                tbl_products.code as item_code,
                tbl_products.name as item_name,
                tbl_products.images as images,
                tbl_products.id as item_id,
                tbl_productions_orders_details.quantity_warehoused as quantity_warehoused,
                tbl_productions_orders_details.quantity_delivery as quantity_delivery,
                tbl_orders.reference_no as reference_no,
                tbl_orders.customer_id as customer_id,
                tbl_productions_orders_details.id as pod_id,
                tbl_orders.id as order_id,
                tbl_productions_orders_items.production_plan_item_id as order_item_id
            ');
        $this->db->from('tbl_productions_orders_items');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
        $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id');
        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_orders_details.object_id');

        $this->db->where('tbl_productions_orders_items.id', $poi_id);
        // $this->db->where('(tbl_productions_orders_details.quantity_warehoused - tbl_productions_orders_details.quantity_delivery) > 0');
        $this->db->where('tbl_productions_orders_details.object_type', 'orders');
        return $this->db->get()->row_array();
    }

    public function updateProductionsOrdersDetails($id, $quantity, $options = 1) {
        $this->db->where('tbl_productions_orders_details.id', $id);
        if ($options == 1) {
            $this->db->set('tbl_productions_orders_details.quantity_delivery', 'tbl_productions_orders_details.quantity_delivery+'.$quantity, false);
        } else if ($options == 2) {
            $this->db->set('tbl_productions_orders_details.quantity_delivery', 'tbl_productions_orders_details.quantity_delivery-'.$quantity, false);
        }
        return $this->db->update('tbl_productions_orders_details');
    }

    public function handlingCheckQualityItemsStage($check_quality_id, $actions = "delete") {
        $this->db->select('tbl_check_quality_items.cqis_id');
        $this->db->from('tbl_check_quality_items');
        $this->db->where('tbl_check_quality_items.check_quality_id', $check_quality_id);
        $items = $this->db->get()->result_array();
        $arrUpdateZero = [];
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $arrUpdateZero[] = [
                    'id' => $value['cqis_id'],
                    'status_result' => 0
                ];
            }
        }
        if (!empty($arrUpdateZero)) {
            $this->db->update_batch('tbl_check_quality_items_stage', $arrUpdateZero, 'id');
        }

        if ($actions == "delete") {
            $arrInsert = [];
            $arrUpdate = [];
            $cIndex = 0;
            //delete
            $this->db->where('tbl_check_quality_items_stage.check_quality_id', $check_quality_id);
            // $this->db->where('tbl_check_quality_items_stage.active', 0);
            $this->db->delete('tbl_check_quality_items_stage');

            //insert
            $this->db->select('
                tbl_check_quality_items.id as id, 
                tbl_check_quality_items.pod_id as pod_id,
                tbl_check_quality_items.id_stage as id_stage_end,
                tbl_check_quality_items.id_stage_again as id_stage_begin,
                tbl_check_quality_items.result as result,
                tbl_check_quality_items.quantity_recycling as quantity_recycling,
                tbl_check_quality_items.cqis_id as cqis_id
            ');
            $this->db->from('tbl_check_quality_items');
            $this->db->where('tbl_check_quality_items.check_quality_id', $check_quality_id);
            $this->db->where('tbl_check_quality_items.result', 2);
            $quality_items = $this->db->get()->result_array();
            if (!empty($quality_items)) {
                foreach ($quality_items as $key => $value) {
                    $check_quality_items_id = $value['id'];
                    $pod_id = $value['pod_id'];
                    $id_stage_begin = $value['id_stage_begin'];
                    $id_stage_end = $value['id_stage_end'];
                    $result = $value['result'];
                    $quantity_recycling = $value['quantity_recycling'];

                    $this->db->select('
                        tbl_productions_orders_details.productions_orders_item_id as poi_id,
                        tbl_productions_orders_details.productions_orders_id as po_id,
                    ');
                    $this->db->from('tbl_productions_orders_details');
                    $this->db->where('tbl_productions_orders_details.id', $pod_id);
                    $dtPod = $this->db->get()->row_array();
                    if (!empty($dtPod)) {
                        $po_id = $dtPod['po_id'];
                        $poi_id = $dtPod['poi_id'];
                        $this->db->select('tbl_productions_orders_items_stages.number as number');
                        $this->db->from('tbl_productions_orders_items_stages');
                        $this->db->where('tbl_productions_orders_items_stages.stage_id', $id_stage_begin);
                        $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $poi_id);
                        $this->db->order_by('tbl_productions_orders_items_stages.id ASC');
                        $dtPoisBegin = $this->db->get()->row_array();
                        $numberBegin = $dtPoisBegin['number'];

                        $this->db->select('tbl_productions_orders_items_stages.number as number');
                        $this->db->from('tbl_productions_orders_items_stages');
                        $this->db->where('tbl_productions_orders_items_stages.stage_id', $id_stage_end);
                        $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $poi_id);
                        $this->db->order_by('tbl_productions_orders_items_stages.id DESC');
                        $dtPoisLast = $this->db->get()->row_array();
                        $numberLast = $dtPoisLast['number'];
                        if (empty($numberBegin)) $numberBegin = $numberLast;

                        $this->db->select('
                            tbl_productions_orders_items_stages.id as pois_id,
                            tbl_productions_orders_items_stages.productions_orders_id as po_id,
                            tbl_productions_orders_items_stages.productions_orders_items_id as poi_id,
                            tbl_productions_orders_items_stages.number as number,
                            tbl_productions_orders_items_stages.stage_id as stage_id,
                            tbl_productions_orders_items_stages.final_stage as final_stage,
                            tbl_stages.status_qc as status_qc,
                            tbl_stages.type as type,
                        ');
                        $this->db->from('tbl_productions_orders_items_stages');
                        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
                        $this->db->where('tbl_productions_orders_items_stages.number >=', $numberBegin);
                        $this->db->where('tbl_productions_orders_items_stages.number <=', $numberLast);
                        $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $poi_id);
                        $this->db->order_by('tbl_productions_orders_items_stages.number ASC');
                        $dtPois = $this->db->get()->result_array();
                        $type = 0;
                        $isTypeProductStep = false;
                        if (!empty($dtPois)) {
                            foreach ($dtPois as $k => $val) {
                                $pois_id = $val['pois_id'];
                                $final_stage = $val['final_stage'];
                                $stage_id = $val['stage_id'];
                                $number = $val['number'];
                                $typeStages = $val['type'];
                                $type = $typeStages;
                                if ($isTypeProductStep) {
                                    $type = 3;
                                }

                                if ($typeStages == 2) {
                                    $isTypeProductStep = true;
                                }

                                $this->db->select('tbl_check_quality_items_stage.id');
                                $this->db->from('tbl_check_quality_items_stage');
                                $this->db->where('tbl_check_quality_items_stage.pois_id', $pois_id);
                                $this->db->where('tbl_check_quality_items_stage.stage_id', $stage_id);
                                $this->db->where('tbl_check_quality_items_stage.check_quality_items_id', $check_quality_items_id);
                                $check_quality_items_stage = $this->db->get()->row_array();

                                $arrInsert[$cIndex] = [
                                    'check_quality_id' => $check_quality_id,
                                    'pod_id' => $pod_id,
                                    'pois_id' => $pois_id,
                                    'stage_id' => $stage_id,
                                    'type' => $type,
                                    'check_quality_items_id' => $check_quality_items_id,
                                    'final_stage' => $final_stage,
                                    'po_id' => $po_id,
                                    'number' => $number,
                                ];
                                if (!empty($check_quality_items_stage)) {
                                    $arrInsert[$cIndex]['id'] = $check_quality_items_stage['id'];
                                }
                                $cIndex++;
                            }
                        }
                    }
                }
            }

            $this->db->select('
                tbl_check_quality_items.id as id, 
                tbl_check_quality_items.pod_id as pod_id,
                tbl_check_quality_items.id_stage as id_stage_end,
                tbl_check_quality_items.id_stage_again as id_stage_begin,
                tbl_check_quality_items.result as result,
                tbl_check_quality_items.quantity_recycling as quantity_recycling,
                tbl_check_quality_items.cqis_id as cqis_id
            ');
            $this->db->from('tbl_check_quality_items');
            $this->db->where('tbl_check_quality_items.check_quality_id', $check_quality_id);
            $quality_items = $this->db->get()->result_array();
            if (!empty($quality_items)) {
                foreach ($quality_items as $key => $value) {
                    $check_quality_items_id = $value['id'];
                    $pod_id = $value['pod_id'];
                    $id_stage_begin = $value['id_stage_begin'];
                    $id_stage_end = $value['id_stage_end'];
                    $result = $value['result'];

                    //update reuslt cqis
                    if (!empty($value['cqis_id'])) {
                        $arrUpdate[] = [
                            'id' => $value['cqis_id'],
                            'status_result' => $result,
                        ];
                    }

                    
                }
            }

            if (!empty($arrInsert)) {
                $this->db->insert_batch('tbl_check_quality_items_stage', $arrInsert);
            }

            if (!empty($arrUpdate)) {
                $this->db->update_batch('tbl_check_quality_items_stage', $arrUpdate, 'id');
            }
        }

        return true;
    }

    public function updateCheckQualityItemsStage($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('tbl_check_quality_items_stage', $data);
    }

    public function getCheckQualityItemsStageById($id) {
        $this->db->select('
            tbl_check_quality_items_stage.*,
            tbl_check_quality_items_stage.active as status,
            tbl_productions_orders_items_stages.productions_orders_items_id as poi_id,
            tbl_productions_orders_items_stages.number as number_pois,
            tbl_productions_orders_items_stages.stage_id as stage_id_pois,
            tbl_productions_orders_items_stages.productions_orders_id as po_id_pois,
        ');
        $this->db->from('tbl_check_quality_items_stage');
        $this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.id = tbl_check_quality_items_stage.pois_id', 'inner');
        $this->db->where('tbl_check_quality_items_stage.id', $id);
        // print_arrays($this->db->get_compiled_select());
        return $this->db->get()->row_array();
    }

    public function totalQuantityWarehouseManufactures($type_items, $id_items) {
        $this->db->select('
            SUM(tblwarehouse_items.product_quantity) as product_quantity,
        ', false);
        $this->db->from('tblwarehouse_items');
        $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_items.localtion');
        $this->db->where('tblwarehouse_items.type_items', $type_items);
        $this->db->where('tblwarehouse_items.id_items', $id_items);
        $this->db->where_not_in('tblwarehouse_items.warehouse_id', [WAREHOUSES_CAPACITY]);
        // $this->db->where('tbllocaltion_warehouses.type', 0);
        return $this->db->get()->row_array();
    }

    public function autoProductionsOrders($productions_plan_id) {

        $this->db->select('
            tbl_productions_plan.id as id,
            tbl_productions_plan.date as date,
            tbl_productions_plan.id_branch as id_branch,
            tbl_productions_plan.options1 as options1,
            tbl_productions_plan.options2 as options2,
        ');
        $this->db->from('tbl_productions_plan');
        $this->db->where('tbl_productions_plan.id', $productions_plan_id);
        $dt_productions_plan = $this->db->get()->row_array();
        if (!empty($dt_productions_plan)) {

            $total_quantity = 0;
            $count_items = 0;
            $productions_plan_reference = '';
            $options1 = $dt_productions_plan['options1'];
            $options2 = $dt_productions_plan['options2'];
            $productions_plan_orders = [];
            $arrOrderId = [];
            $arrBusinessPlanId = [];
            $items = [];

            $this->db->select('
                tbl_productions_plan_items.type_object as type_object, 
                tbl_productions_plan_items.object_id as object_id
            ', false);
            $this->db->from('tbl_productions_plan_items');
            $this->db->where('tbl_productions_plan_items.productions_plan_id', $productions_plan_id);
            $this->db->group_by('tbl_productions_plan_items.type_object, tbl_productions_plan_items.object_id');
            $items_group = $this->db->get()->result_array();
            $productions_plan = [];

            if (!empty($items_group)) {
                foreach ($items_group as $key => $value) {
                    $object_type = $value['type_object'];
                    $object_id = $value['object_id'];

                    if ($object_type == "orders") {
                        $arrOrderId[] = $object_id;
                    } else if ($object_type == "business_plan") {
                        $arrBusinessPlanId[] = $object_id;
                    }

                    $strPO = $object_type.'__'.$object_id;
                    $productions_plan[] = $strPO;

                    $productions_plan_orders[$strPO] = [
                        'productions_plan_id' => $object_id,
                        'productions_order_id' => 0,
                        'total_quantity' => 0,
                        'count_items' => 0,
                        'object_type' => $object_type,
                    ];

                    $this->db->select('
                        tbl_productions_plan_items.*
                    ', false);
                    $this->db->from('tbl_productions_plan_items');
                    $this->db->where('tbl_productions_plan_items.productions_plan_id', $productions_plan_id);
                    $this->db->where('tbl_productions_plan_items.type_object', $object_type);
                    $this->db->where('tbl_productions_plan_items.object_id', $object_id);
                    $itemsP = $this->db->get()->result_array();
                    if (!empty($itemsP)) {
                        foreach ($itemsP as $k => $v) {
                            $info = $this->products_model->rowProduct($v['product_id']);
                            $items_code = $info['code'];
                            $items_name = $info['name'];
                            $quantity = $v['quantity_total_details'] + $v['quantity_reserve'];
                            $versions_bom = $v['versions'];
                            // $versions_stage = $info['versions_stage'];
                            $versions_stage = $v['versions_stage'];

                            $items[] = [
                                'production_plan_item_id' => $v['item_object_id'],
                                'type_items' => 'products',
                                'items_id' => $v['product_id'],
                                'items_code' => $items_code,
                                'items_name' => $items_name,
                                'quantity' => $quantity,
                                'note_items' => '',
                                'versions_bom' => $versions_bom,
                                'versions_stage' => $versions_stage,
                                'productions_capacity_items_id' => 0,
                                'object_item_type' => $object_type,
                                'plan_item_id' => $v['id'],
                                'plan_id' => $v['productions_plan_id'],
                                'arrBOM' => []
                            ];

                            $productions_plan_orders[$strPO]['count_items'] = $productions_plan_orders[$strPO]['count_items'] + 1;
                            $productions_plan_orders[$strPO]['total_quantity'] = $productions_plan_orders[$strPO]['total_quantity'] + $quantity;

                            $total_quantity+= $quantity;
                            $count_items++;
                        }
                    }
                }
            }

            if (!empty($arrOrderId)) {
                $productions_plan_reference = $this->manufactures_model->rowReferenceOrdersByArrId($arrOrderId)['reference_no'].', ';
            }

            if (!empty($arrBusinessPlanId)) {
                $productions_plan_reference.= $this->manufactures_model->rowReferenceBusinessPlanByArrId($arrBusinessPlanId)['reference_no'].', ';
            }

            if (!empty($productions_plan_reference)) {
                $productions_plan_reference = rtrim($productions_plan_reference, ', ');
            }

            $productions_plan = implode(',', $productions_plan);

            $staff_id = get_staff_user_id();
            $date_default = date('Y-m-d H:i:s');
            
            $reference_no = getReference('productions_orders');
            $date = $dt_productions_plan['date'];
            $location = $dt_productions_plan['id_branch'];
            $options = [
                'date' => $date,
                'reference_no' => $reference_no,
                'location_id' => $location,
                'productions_plan_id' => $productions_plan,
                'productions_plan_reference_no' => $productions_plan_reference,
                'total_quantity' => $total_quantity,
                'count_items' => $count_items,
                'note' => '',
                'status' => 'approved',
                'user_status' => $staff_id,
                'date_status' => $date_default,
                'date_created' => $date_default,
                'created_by' => $staff_id,
                'options1' => $options1,
                'options2' => $options2,
            ];
            $productions_orders_id = $this->manufactures_model->insertProductionsOrders($options);
            if ($productions_orders_id) {
                updateReference('productions_orders');

                foreach ($items as $key => $value) {
                    $value['productions_orders_id'] = $productions_orders_id;
                    unset($value['arrBOM']);
                    $object_item_type = $value['object_item_type'];
                    $productions_orders_items_id = $this->manufactures_model->insertProductionsOrdersItems($value);
                    if ($productions_orders_items_id) {
                        $this->manufactures_model->updateQuantityProductionOrders($value['production_plan_item_id'], $value['quantity'], $plus = 1, $object_item_type);

                        $object_id = 0;
                        $object_type = $value['object_item_type'];
                        if ($object_type == "orders") {
                            $order_item = $this->orders_model->rowOrderItemsById($value['production_plan_item_id']);
                            $object_id = $order_item['order_id'];
                        } else if ($object_type == "business_plan") {
                            $business_item = $this->manufactures_model->rowBusinessItemsById($value['production_plan_item_id']);
                            $object_id = $business_item['business_plan_id'];
                        }

                        
                    }
                }

                if (!empty($productions_plan_orders)) {
                    foreach ($productions_plan_orders as $key => $value) {
                        $value['productions_order_id'] = $productions_orders_id;
                        $object_type = $value['object_type'];
                        if ($this->manufactures_model->insertProductionsPlanOrders($value)) {
                            $statusPL = 2;
                            if ($this->manufactures_model->checkPlanFullByOrder($value['productions_plan_id'], $object_type)) {
                                $statusPL = 1;
                            }
                            if ($object_type == "orders") {
                                $this->orders_model->updateOrdersNew($value['productions_plan_id'], ['status_productions_orders' => $statusPL]);
                            } else if ($object_type == "business_plan") {
                                $this->business_plan_model->updateBusinessPlan($value['productions_plan_id'], ['status_productions_orders' => $statusPL]);
                            }
                        }
                    }
                }

                $this->manufactures_model->handlingBomProductionsOrders($productions_orders_id);
                $this->manufactures_model->handlingProductionsDetail($productions_orders_id);
                $this->manufactures_model->handlingStagesProductionsOrders($productions_orders_id);
                $this->manufactures_model->updateProgress($productions_orders_id);
                
                noti_custom('create_productions_orders', $productions_orders_id, get_staff_user_id(), 0, '', ['actions' => 'add']);
                
                insertActivityLog([
                    'type_parent_obj' => 'productions_orders',
                    'table_obj' => 'tbl_productions_orders',
                    'id_obj' => $productions_orders_id,
                    'name_obj' => $reference_no,
                    'content' => lang('tnh_his_add_productions_orders') . ' [' . $reference_no . ']',
                    'actions' => 'add'
                ]);

                return $productions_orders_id;
            }
        }

        return false;
    }
    public function updateProgress($productions_orders_id = '')
    {
        $this->db->select('
            tbl_productions_orders_items_stages.id as id,
            tbl_stages.name as stage_name, 
            tbl_productions_orders_details.reference_no,
            tbl_productions_orders_items.items_code,
            tbl_productions_orders_items.items_name,    
            tbl_productions_orders_items.quantity,    
            SUM(tbl_purchase_products.total_quantity) as quantity_done,    
        ', false);
        $this->db->from('tbl_productions_orders_items_stages');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
        $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items_stages.productions_orders_items_id', 'left');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_items_stages.productions_orders_items_id', 'left');
        $this->db->join('tbl_purchase_products', 'tbl_purchase_products.pois_id = tbl_productions_orders_items_stages.id', 'left');
        $this->db->where('tbl_productions_orders_details.productions_orders_id', $productions_orders_id);
        $this->db->where('tbl_productions_orders_items_stages.stage_id !=', 2);
        $this->db->group_by('tbl_productions_orders_items_stages.id');
        $this->db->order_by('tbl_productions_orders_items_stages.id ASC,tbl_productions_orders_items_stages.number ASC');
        $rows = $this->db->get()->result_array();
        foreach ($rows as $key => $value) {
            $updatedRow = $this->_api_row($value);
            sendSocket([
                'action'     => 'add',
                'newRow' => $updatedRow
            ], [], 'loadProgress');
        }
    }
    private function _api_row($r)
    {
        // Chuẩn hóa row theo format frontend đang dùng
        return [
            'stage_id' => $r['id'],
            'order_code' => $r['reference_no'],
            'sku'        => $r['items_code'],
            'stage'      => $r['stage_name'],
            'qty_plan'   => (int) $r['quantity'],
            'qty_done'   => (int) $r['quantity_done'],
            'qty_todo'   => (int) ($r['quantity'] - $r['quantity_done']),
            'percent'    => round(($r['quantity_done'] / $r['quantity'] * 100), 2),
            'bar_color'  => $this->_getColor(($r['quantity_done'] / $r['quantity'] * 100)),
        ];
    }
    // ---------- Helpers ----------

    private function _getColor($percent)
    {
        if ($percent >= 75) return '#22c55e'; // green
        if ($percent >= 40) return '#facc15'; // yellow
        return '#ef4444'; // red
    }
    public function getProductionsOrdersDetailStaff($productions_detail_id) {

    }

    public function getProductionsPlanOrdersByOrders($productions_order_id) {
        $query = "(
            SELECT
                tbl_productions_plan_orders.productions_order_id as productions_order_id,
                tbl_orders.reference_no as reference_no,
                tblclients.company
            FROM tbl_productions_plan_orders
            INNER JOIN tbl_orders ON tbl_orders.id = tbl_productions_plan_orders.productions_plan_id
            INNER JOIN tblclients ON tblclients.userid = tbl_orders.customer_id
            WHERE tbl_productions_plan_orders.object_type = 'orders' AND tbl_productions_plan_orders.productions_order_id = ".$productions_order_id."
        )";
        return $this->db->query($query)->result_array();
    }

    public function getProductionsPlanOrdersByBusinessPlan($productions_order_id) {
        $query = "(
            SELECT
                tbl_productions_plan_orders.productions_order_id as productions_order_id,
                tbl_business_plan.reference_no reference_no_business_plan
            FROM tbl_productions_plan_orders
            INNER JOIN tbl_business_plan ON tbl_business_plan.id = tbl_productions_plan_orders.productions_plan_id
            WHERE tbl_productions_plan_orders.object_type = 'business_plan' AND tbl_productions_plan_orders.productions_order_id = ".$productions_order_id."
        )";
        return $this->db->query($query)->result_array();
    }

    public function insertBatchProductionsPlanObject($data) {
        return $this->db->insert_batch('tbl_productions_plan_object', $data);
    }

    public function deleteProductionsPlanObject($productions_plan_id) {
        $this->db->where('tbl_productions_plan_object.productions_plan_id', $productions_plan_id);
        return $this->db->delete('tbl_productions_plan_object');
    }

    public function getBusinessPlanByProductionsPlanPreventiveId($productions_plan_preventive_id) {
        $this->db->select('tbl_business_plan.*');
        $this->db->from('tbl_business_plan');
        $this->db->where('tbl_business_plan.productions_plan_preventive_id', $productions_plan_preventive_id);
        return $this->db->get()->row_array();
    }

    public function handlingPreventive($productions_plan_id, $arrItemsPerventive) {
        $business_plan_id = 0;
        $this->db->select('
            tbl_productions_plan.id,
            tbl_productions_plan.reference_no,
            tbl_productions_plan.date as date,
            tbl_productions_plan.id_branch as id_branch
        ', false);
        $this->db->from('tbl_productions_plan');
        $this->db->where('tbl_productions_plan.id', $productions_plan_id);
        $productions_plan = $this->db->get()->row_array();
        if (!empty($productions_plan)) {
            $reference_no = getReference('business_plan');
            $plan_name = 'Dự phòng kế hoạch NPL '.$productions_plan['reference_no'];

            $this->db->select('
                tbldepartments.departmentid as departmentid
            ', false);
            $this->db->from('tbldepartments');
            $this->db->order_by('tbldepartments.departmentid ASC');
            $this->db->limit(1);
            $dtDepartments = $this->db->get()->row_array();
            $departments = !empty($dtDepartments) ? $dtDepartments['departmentid'] : 1;

            $total_quantity = 0;
            $count_items = 0;
            $date = $productions_plan['date'];
            $date_sub = date_format(date_create($date), 'Y-m-d');
            $note = '';

            if (!empty($arrItemsPerventive)) {
                foreach ($arrItemsPerventive as $key => $value) {
                    $items_id = $value['product_id'];
                    $type_items = 'products';
                    $info = $this->products_model->rowProduct($items_id);
                    if (empty($info)) {
                        continue;
                    }

                    $items_code = $info['code'];
                    $items_name = $info['name'];
                    $quantity = $value['quantity_preventive'];
                    $total_quantity_sub = 0;

                    $sub = [];
                    $sub[] = [
                        'date' => $date_sub,
                        'quantity' => $quantity,
                        'quantity_plan_item' => $quantity,
                    ];

                    $items[] = [
                        'type_items' => $type_items,
                        'items_id' => $items_id,
                        'items_code' => $items_code,
                        'items_name' => $items_name,
                        'quantity' => $quantity,
                        'versions' => $value['versions_perventive'],
                        'versions_stage' => $value['versions_stages_perventive'],
                        'note_items' => '',
                        'order_item_id' => 0,
                        'unit_id' => $info['conversion_unit'],
                        'conversion_quantity_unit' => $info['conversion_quantity_unit'],
                        'sub' => $sub
                    ];

                    $total_quantity+= $quantity;
                }
            }

            $count_items = count($items);
            $options = [
                'date' => $date,
                'reference_no' => $reference_no,
                'plan_name' => $plan_name,
                'departments_id' => $departments,
                'total_quantity' => $total_quantity,
                'count_items' => $count_items,
                'note' => $note,
                'status' => 'approved',
                'date_status' => date('Y-m-d H:i:s'),
                'user_status' => get_staff_user_id(),
                'productions_plan_id' => 0,
                'date_created' => date('Y-m-d H:i'),
                'created_by' => get_staff_user_id(),
                'productions_plan_id' => 1,
                'order_id' => 0,
                'productions_plan_preventive_id' => $productions_plan_id,
                'id_branch' => $productions_plan['id_branch'],
            ];
            $business_plan_id = $this->business_plan_model->insertBusinessPlan($options);
            if ($business_plan_id) {
                updateReference('business_plan');

                foreach ($items as $key => $value) {
                    $op = [
                        'business_plan_id' => $business_plan_id,
                        'type_items' => $value['type_items'],
                        'items_id' => $value['items_id'],
                        'items_code' => $value['items_code'],
                        'items_name' => $value['items_name'],
                        'quantity' => $value['quantity'],
                        'quantity_plan' => $value['quantity'],
                        'note_items' => $value['note_items'],
                        'order_item_id' => $value['order_item_id'],
                        'unit_id' => $value['unit_id'],
                        'conversion_quantity_unit' => $value['conversion_quantity_unit'],
                    ];
                    $business_plan_item_id = $this->business_plan_model->insertBusinessPlanItems($op);
                    if ($business_plan_item_id) {
                        $items[$key]['business_plan_item_id'] = $business_plan_item_id;
                        $sb = $value['sub'];
                        foreach ($sb as $k => $val) {
                            $sb[$k]['business_plan_items_id'] = $business_plan_item_id;
                        }
                        if (!empty($sb)) {
                            $this->business_plan_model->insertBatchBusinessPlanItemsDate($sb);
                        }
                    }
                }

                if (!empty($items)) {
                    foreach ($items as $key => $value) {
                        $warehouses = "(
                            SELECT
                                SUM(tblwarehouse_items.product_quantity) as quantity_warehouses
                            FROM tblwarehouse_items
                            WHERE tblwarehouse_items.type_items = 'product' AND tblwarehouse_items.id_items = ".$value['items_id']."
                            GROUP BY tblwarehouse_items.id_items
                        )";
                        $dtW = $this->db->query($warehouses)->row_array();

                        $productions_plan_items = [
                            'productions_plan_id' => $productions_plan_id,
                            'type_object' => 'business_plan',
                            'object_id' => $business_plan_id,
                            'item_object_id' => $value['business_plan_item_id'],
                            'product_id' => $value['items_id'],
                            'quantity_minimum' => 0,
                            'quantity_warehouses' => (float)$dtW['quantity_warehouses'],
                            'status' => 'not',
                            'quantity_reserve' => 0,
                            'quantity_total_details' => $value['quantity'],
                            'versions' => $value['versions'],
                            'versions_stage' => $value['versions_stage'],
                            'is_preventive' => 1,
                        ];
                        $productions_plan_item_id = $this->manufactures_model->insertProductionsPlanItems($productions_plan_items);
                        if ($productions_plan_item_id) {
                            $productions_plan_details[] = [
                                'productions_plan_item_id' => $productions_plan_item_id,
                                'date' => $date_sub,
                                'quantity' => $value['quantity']
                            ];
                        }
                    }

                    if (!empty($productions_plan_details)) {
                        $this->db->insert_batch('tbl_productions_plan_details', $productions_plan_details);
                    }
                }

                $this->business_plan_model->handlingStagesBusinessPlan($business_plan_id);
                insertActivityLog([
                    'type_parent_obj' => 'business_plan',
                    'table_obj' => 'tbl_business_plan',
                    'id_obj' => $business_plan_id,
                    'name_obj' => $reference_no,
                    'content' => lang('tnh_his_add_business_plan').' ['.$reference_no.']',
                    'actions' => 'add'
                ]);
            }

        }
        return $business_plan_id;
    }

    public function loadDataPrepareMaterials($pod_id, $production_plan_id, $tempQuantity = 0, $actions = '', $pois_id = 0) {

        $this->db->select('
            tbl_productions_orders_items.id as poi_id,
            CONCAT("pod__", tbl_products.id) as id,
            tbl_products.id as product_id,
            tbl_products.name as name,
            tbl_products.code as code,
            tbl_productions_orders_items.quantity as quantity,
            1 as quantity_primary,
            1 as quantity_single,
            1 as quantity_exchange,
            tbl_productions_orders_items.quantity as quantity_primary,
            tbl_products.images as images,
            tbl_products.unit_id as unit_id,
            tbl_products.unit_id as unit_parent_id,
            tbl_products.versions as versions,
            tbl_productions_orders_items.versions_bom as versions_bom,
            0 as poisub_id
        ', false);
        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
        $this->db->where('tbl_productions_orders_details.id', $pod_id);
        $this->db->where('tbl_productions_orders_items.type_items', 'products');
        $this->db->group_by('tbl_products.id');
        $productions_orders_items = $this->db->get()->row_array();
        $items = [];
        if (!empty($productions_orders_items)) {

            $poi_id = $productions_orders_items['poi_id'];
            $this->db->select('
                CONCAT(tbl_productions_orders_items_sub.type, "__", tbl_productions_orders_items_sub.item_id) as item_cs_id,
                tbl_productions_orders_items_sub.type as type,
                tbl_productions_orders_items_sub.item_id as item_id,
                SUM(tbl_productions_orders_items_sub.quantity + tbl_productions_orders_items_sub.quantity_compensation + tbl_productions_orders_items_sub.quantity_compensation_sm) as quantity,
                tbl_productions_orders_items_sub.unit_id as unit_id,
                tbl_productions_orders_items_sub.unit_parent_id as unit_parent_id,
                tbl_productions_orders_items_sub.quantity_single as quantity_single,
                tbl_productions_orders_items_sub.quantity_exchange,
                tbl_productions_orders_items_sub.is_single_use,
                SUM(tbl_productions_orders_items_sub.quantity_compensation + tbl_productions_orders_items_sub.quantity_compensation_sm) as total_quantity_compensation
            ', false);
            $this->db->from('tbl_productions_orders_items_sub');
            $this->db->where('tbl_productions_orders_items_sub.type_element_item', 1);
            $this->db->where('tbl_productions_orders_items_sub.productions_orders_items_id', $poi_id);
            $this->db->where('tbl_productions_orders_items_sub.type !=', 'element');
            $this->db->where('tbl_productions_orders_items_sub.type =', 'materials');
            $this->db->group_by('tbl_productions_orders_items_sub.type, tbl_productions_orders_items_sub.item_id');
            $subItems = $this->db->get()->result_array();
            // print_arrays($subItems);
            if (!empty($subItems)) {
                foreach ($subItems as $key => $value) {
                    $item_type = $value['type'];
                    $item_id = $value['item_id'];
                    $images = '';
                    $type_purchase_product = '';
                    if ($item_type == "element") {
                        continue;
                    } else if ($item_type == "materials") {
                        $info = $this->items_model->rowMaterial($item_id);
                        if (!empty($info['images'])) {
                            $images = base_url('uploads/materials/'.$info['images']);
                        }
                    } else if ($item_type == "tools_supplies") {
                        $info = $this->tools_supplies_model->rowToolsSupplies($item_id);
                        if (!empty($info['images'])) {
                            $images = base_url('uploads/tools_supplies/'.$info['images']);
                        }
                    } else {
                        $arrRemoveSemiProduct[] = $item_id;
                        $info = $this->products_model->rowProduct($item_id);
                        $type_purchase_product = "products";
                        if (!empty($info['images'])) {
                            $images = base_url('uploads/products/'.$info['images']);
                        }
                    }

                    $isWarehouses = $this->manufactures_model->isWarehouses($item_id, $item_type);

                    $unit_manufactures = $this->unit_model->rowUnit($value['unit_id']);

                    $warehouses_manufactures = $this->manufactures_model->getWarehouseManufactures($item_type, $item_id);

                    $item_code = $info['code'];
                    $item_name = $info['name'];
                    $subItems[$key]['item_code'] = $item_code;
                    $subItems[$key]['item_name'] = $item_name;
                    $subItems[$key]['images'] = $images;
                    $subItems[$key]['isWarehouses'] = $isWarehouses;
                    $subItems[$key]['unit_name_manufactures'] = $unit_manufactures['unit'];
                    $subItems[$key]['warehousePlan'] = json_encode($warehouses_manufactures);
                }
            }
            $items = $subItems;
        }
        return $items;
    }

    public function getWarehouseManufactures($item_type, $id_items, $warehouse_id = 0, $localtion = 0) {
        if ($item_type == "materials") {
            $item_type = "nvl";
        } else if ($item_type == "tools_supplies") {
            $item_type = "tools";
        } else {
            $item_type = "product";
        }
        $this->db->select("
            CONCAT(tblwarehouse_items.warehouse_id, '__', tblwarehouse_items.localtion, '__', coalesce(tblwarehouse_items.lot_code, 'NULL'), '__', coalesce(tblwarehouse_items.date_sx, 'NULL'), '__', coalesce(tblwarehouse_items.date_sd, 'NULL'), '__', coalesce(tblwarehouse_items.date_use, 'NULL')) as id,
            tbllocaltion_warehouses.name as text,
            SUM(tblwarehouse_items.product_quantity_unit) as product_quantity,
            tbllocaltion_warehouses.name as name_localtion_warehouses,
            tblwarehouse_items.lot_code as lot_code, 
            tblwarehouse_items.date_sx as date_sx,
            tblwarehouse_items.date_sd as date_sd, 
            tblwarehouse_items.date_use as date_use
        ");
        $this->db->from('tblwarehouse_items');
        $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_items.localtion');
        $this->db->where('tblwarehouse_items.product_quantity_unit >', 0);
        $this->db->where('tblwarehouse_items.type_items =', $item_type);
        $this->db->where('tblwarehouse_items.id_items =', $id_items);
        if (empty($warehouse_id)) {
            $this->db->where('tblwarehouse_items.warehouse_id =', WAREHOUSES_CAPACITY);
        } else {
            $this->db->where('tblwarehouse_items.warehouse_id =', $warehouse_id);
        }

        if (empty($localtion)) {
            $this->db->where('tblwarehouse_items.localtion =', LOCATIONS_DEFAULT_MANUFACTURES);
        } else {
            $this->db->where('tblwarehouse_items.localtion =', $localtion);
        }

        $this->db->group_by('tblwarehouse_items.lot_code, tblwarehouse_items.date_sx, tblwarehouse_items.date_sd, tblwarehouse_items.date_use');
        $rs = $this->db->get()->result_array();
        if (!empty($rs)) {
            foreach ($rs as $k => $val) {
                $name_localtion_warehouses = $val['name_localtion_warehouses'];
                $product_quantity = '(SL chuẩn: '.$val['product_quantity'].')';
                $lot_code = $val['lot_code'] ? ' - Lot: '.$val['lot_code'] : '';
                $date_sx = $val['date_sx'] ? ' - Ngày SX: '._d($val['date_sx']) : '';
                $date_sd = $val['date_sd'] ? ' - Ngày SD: '._d($val['date_sd']) : '';

                $rs[$k]['text'] = $name_localtion_warehouses.$product_quantity.$lot_code.$date_sx.$date_sd;
            }
        }
        return $rs;
    }

    public function handlingBomPP(&$arrBOMMaterial=[], $product_id, $versions, $quantity, $key = 0, $quantity_order = 0, $quantity_compensation_sm = 0) {
        if ($key == 5) {
            return true;
        }

        if (!empty($versions) && !empty($product_id)) {
            $version = $this->products_model->getBomByProductIdAndVersions($product_id, $versions);
            if (!empty($version)) {
                $elements = $this->products_model->getVersionsElementByVersionId($version['id']);
                $iiPartent = 0;

                if (!empty($elements)) {
                    foreach ($elements as $k => $val) {
                        $quantity_element = $val['quantity'];
                        $total_quantity_element = $quantity / $quantity_element;
                        $quantity_compensation_sm = $quantity_compensation_sm/$quantity_element;
                        $element_items = $this->products_model->getElementItemsByElementId($val['id']);
                        if (!empty($element_items)) {
                            foreach ($element_items as $i => $el) {
                                $quantity_single = $el['quantity'];
                                $total_quantity_item = $quantity_order/$el['number_children_size'] * $quantity_single;
                                $quantity_primary = 0;
                                $is_single_use = 0;
                                $quota_material_replace_t = 0;
                                $quantity_single_use = 0;
                                $is_zinc = 0;

                                $dtStage = get_table_where('tbl_stages', ['id' => $el['stage_id']], '', 'row_array');
                                if (!empty($dtStage['quota_material_replace_t'])) {
                                    $quota_material_replace_t = $dtStage['quota_material_replace_t'];
                                }

                                if ($el['type'] == "semi_products" || $el['type'] == "semi_products_outside") {
                                    $info = $this->products_model->rowProduct($el['item_id']);
                                    $unit_parent_id = $info['unit_id'];
                                    $quantity_exchange = 1;
                                    $quantity_primary = $total_quantity_item;
                                } else {
                                    $info = $this->items_model->rowMaterial($el['item_id']);
                                    $unit_id = $el['unit_id'];
                                    $unit_parent_id = $info['unit_id'];
                                    $row_exchange = $this->products_model->rowExchangeItems($el['item_id'], $unit_id);
                                    $quantity_exchange = 1;
                                    if (!empty($row_exchange)) {
                                        $quantity_exchange = $row_exchange['number_exchange'];
                                    }

                                    $is_single_use = !empty($info['is_single_use']) ? $info['is_single_use'] : 0;
                                    if ($is_single_use) {
                                        if ($quota_material_replace_t != 0) {
                                            $quantity_single_use = ceil($quantity_order/$quota_material_replace_t * $quantity_single);
                                            $quantity_primary = ceil($quantity_single_use/$quantity_exchange);
                                        }
                                    } else {
                                        if ($quantity_exchange != 0) {
                                            $quantity_primary = $total_quantity_item/$quantity_exchange;
                                        }
                                    }

                                    $is_zinc = !empty($info['is_zinc']) ? $info['is_zinc'] : 0;
                                    if ($is_zinc) {
                                        $quantity_single_use = $quantity_single;
                                        $total_quantity_item = $quantity_single;
                                    }

                                    $key = 0;
                                }

                                $paper_exchange = 0;
                                if (!$el['number_children_size']) {
                                    $paper_exchange = $quantity_order/$el['number_children_size'];
                                } 

                                if ($el['type'] == "materials") {
                                    $arrBOMMaterial[] = [
                                        'item_type' => $el['type'],
                                        'item_id' => $el['item_id'],
                                        'item_code' => $info['code'],
                                        'item_name' => $info['name'],
                                        'unit_id' => $el['unit_id'],
                                        'quantity_single' => $quantity_single,
                                        'quantity' => $is_single_use ? $quantity_single_use : $total_quantity_item,
                                        'unit_parent_id' => $unit_parent_id,
                                        'quantity_compensation' => $el['quantity_compensation'] + $quantity_compensation_sm,
                                        'quantity_compensation_sm' => $quantity_compensation_sm,
                                        'standard_unit' => $el['type'] == "semi_products_outside" ? $el['unit_id'] : $info['standard_unit'],
                                        'exchange_standard_unit' => $el['type'] == "semi_products_outside" ? 1 : $info['exchange_standard_unit'],
                                        'exchange_unit' => $el['type'] == "semi_products_outside" ? 1 : $info['exchange_unit'],
                                        'quantity_exchange' => $quantity_exchange,
                                        'is_zinc' => $is_zinc
                                    ];
                                } else {
                                    $arrBOMMaterial[] = [
                                        'item_type' => $el['type'],
                                        'item_id' => $el['item_id'],
                                        'item_code' => $info['code'],
                                        'item_name' => $info['name'],
                                        'unit_id' => $el['unit_id'],
                                        'quantity_single' => $quantity_single,
                                        'quantity' => $is_single_use ? $quantity_single_use : $total_quantity_item,
                                        'unit_parent_id' => $unit_parent_id,
                                        'quantity_compensation' => $el['quantity_compensation'] + $quantity_compensation_sm,
                                        'quantity_compensation_sm' => $quantity_compensation_sm,
                                        'standard_unit' => $el['unit_id'],
                                        'exchange_standard_unit' => 1,
                                        'exchange_unit' => 1,
                                        'quantity_exchange' => $quantity_exchange,
                                        'is_zinc' => $is_zinc
                                    ];
                                }

                                if ($el['type'] == "semi_products") {
                                    $key++;
                                    // $this->handlingBomPP($arrBOMMaterial, $info['id'], $info['versions'], $total_quantity_item, $key, $total_quantity_item, $el['quantity_compensation']);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function insertBatchProductionsPlanCompensation($data) {
        return $this->db->insert_batch('tbl_productions_plan_compensation', $data);
    }

    public function deleteProductionsPlanCompensation($productions_plan_id) {
        $this->db->where('tbl_productions_plan_compensation.productions_plan_id', $productions_plan_id);
        return $this->db->delete('tbl_productions_plan_compensation');
    }

    public function productionsPlanCompensation($productions_plan_id, $item_id, $item_type) {
        $this->db->select('
            tbl_productions_plan_compensation.*
        ', false);
        $this->db->from('tbl_productions_plan_compensation');
        $this->db->where('tbl_productions_plan_compensation.productions_plan_id', $productions_plan_id);
        $this->db->where('tbl_productions_plan_compensation.item_id', $item_id);
        $this->db->where('tbl_productions_plan_compensation.item_type', $item_type);
        return $this->db->get()->row_array();
    }

    public function getWarehousesPlanBomNew($production_plan_id, $items_id, $type, $type_warehouse) {
        $warehouse = [];
        // $this->db->simple_query('SET SESSION group_concat_max_len=1500000000');
        // $this->db->select('
        //     GROUP_CONCAT(distinct tbl_productions_plan_bom.item_id) as item_id
        // ');
        // $this->db->from('tbl_productions_plan_bom');
        // $this->db->where('tbl_productions_plan_bom.productions_plan_id', $production_plan_id);
        // $this->db->where('tbl_productions_plan_bom.item_type', $type);
        // $productions_plan_bom = $this->db->get()->row_array();

        $this->db->select('
            SUM(tblwarehouse_items.product_quantity) as product_quantity
        ');
        $this->db->from('tblwarehouse_items');
        $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_items.localtion');
        // $this->db->where_in('tblwarehouse_items.id_items', explode(',', $productions_plan_bom['item_id']));
        $this->db->where('tblwarehouse_items.type_items', $type_warehouse);
        $this->db->where('tblwarehouse_items.id_items', $items_id);
        $this->db->where_in('tbllocaltion_warehouses.productions_plan_id', [$production_plan_id, 0]);
        $this->db->where('tbllocaltion_warehouses.pod_id', 0);
        return $this->db->get()->row_array();
        // $this->db->group_by('tblwarehouse_items.id_items');
        // $warehouse_items = $this->db->get()->result_array();
        // if (!empty($warehouse_items)) {
        //     foreach ($warehouse_items as $key => $value) {
        //         $index = $type.'__'.$value['id_items'];
        //         $warehouse[$index] = [
        //             'item_type' => $type,
        //             'item_id' => $value['id_items'],
        //             'quantity' => $value['product_quantity'],
        //         ];
        //     }
        // }

        // return $warehouse;
    }

    public function getProductionsPlanPO($productions_orders_id) {
        $this->db->select('
            tbl_productions_orders_items.plan_id as id
        ', false);
        $this->db->from('tbl_productions_orders_items');
        $this->db->where('tbl_productions_orders_items.productions_orders_id', $productions_orders_id);
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }

    public function getWarehouseManufacturesNew($item_type, $id_items, $warehouse_id = 0, $localtion = 0, $productions_plan_id = 0) {
        if ($item_type == "materials") {
            $item_type = "nvl";
        } else if ($item_type == "tools_supplies") {
            $item_type = "tools";
        } else {
            $item_type = "product";
        }

        $this->db->select("
            CONCAT(tblwarehouse_items.warehouse_id, '__', tblwarehouse_items.localtion, '__', coalesce(tblwarehouse_items.lot_code, 'NULL'), '__', coalesce(tblwarehouse_items.date_sx, 'NULL'), '__', coalesce(tblwarehouse_items.date_sd, 'NULL'), '__', coalesce(tblwarehouse_items.date_use, 'NULL')) as id,
            tbllocaltion_warehouses.name as text,
            SUM(tblwarehouse_items.product_quantity) as product_quantity,
            tbllocaltion_warehouses.name as name_localtion_warehouses,
            tblwarehouse_items.lot_code as lot_code, 
            tblwarehouse_items.date_sx as date_sx,
            tblwarehouse_items.date_sd as date_sd, 
            tblwarehouse_items.date_use as date_use,
            tblwarehouse_items.localtion
        ");
        $this->db->from('tblwarehouse_items');
        $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_items.localtion');
        $this->db->where('tblwarehouse_items.product_quantity >', 0);
        $this->db->where('tblwarehouse_items.type_items =', $item_type);
        $this->db->where('tblwarehouse_items.id_items =', $id_items);
        if (empty($warehouse_id)) {
            $this->db->where('tblwarehouse_items.warehouse_id =', WAREHOUSES_CAPACITY);
        } else {
            $this->db->where('tblwarehouse_items.warehouse_id =', $warehouse_id);
        }

        if (empty($localtion)) {
            $this->db->group_start();
            $this->db->where('tblwarehouse_items.localtion =', LOCATIONS_DEFAULT_MANUFACTURES);
            if (!empty($productions_plan_id)) {
                $this->db->or_where('tbllocaltion_warehouses.productions_plan_id =', $productions_plan_id);
            }
            $this->db->group_end();
            $this->db->order_by('tbllocaltion_warehouses.productions_plan_id DESC');
        } else {
            $this->db->where('tblwarehouse_items.localtion =', $localtion);
        }

        $this->db->group_by('tblwarehouse_items.localtion, tblwarehouse_items.lot_code, tblwarehouse_items.date_sx, tblwarehouse_items.date_sd, tblwarehouse_items.date_use');
        $rs = $this->db->get()->result_array();
        if (!empty($rs)) {
            foreach ($rs as $k => $val) {
                $name_localtion_warehouses = $val['name_localtion_warehouses'];
                $product_quantity = '(SL kho: '.$val['product_quantity'].')';
                $lot_code = $val['lot_code'] ? ' - Lot: '.$val['lot_code'] : '';
                $date_sx = $val['date_sx'] ? ' - Ngày SX: '._d($val['date_sx']) : '';
                $date_sd = $val['date_sd'] ? ' - Ngày SD: '._d($val['date_sd']) : '';

                $rs[$k]['text'] = $name_localtion_warehouses.$product_quantity.$lot_code.$date_sx.$date_sd;
            }
        }
        return $rs;
    }

    public function updateProductionsOrdersItemsStagesPO($productions_orders_id, $stage_id, $data) {
        $this->db->where('tbl_productions_orders_items_stages.productions_orders_id', $productions_orders_id);
        $this->db->where('tbl_productions_orders_items_stages.stage_id', $stage_id);
        $this->db->where('tbl_productions_orders_items_stages.active', 0);
        return $this->db->update('tbl_productions_orders_items_stages', $data);
    }

    public function updateProductionsOrdersItemsStagesUnPO($productions_orders_id, $stage_id) {
        $this->db->from('tbl_suggest_exporting');
        $this->db->where('tbl_suggest_exporting.po_id', $productions_orders_id);
        $this->db->where('tbl_suggest_exporting.stage_id', $stage_id);
        $is_suggest_exporting = $this->db->count_all_results();
        if (!$is_suggest_exporting) {
            $this->db->where('tbl_productions_orders_items_stages.productions_orders_id', $productions_orders_id);
            $this->db->where('tbl_productions_orders_items_stages.stage_id', $stage_id);
            return $this->db->update('tbl_productions_orders_items_stages', [
                'active' => 0,
                'staff_active' => 0,
                'date_active' => NULL,
            ]);
        }
        return false;
    }

    public function isEditProductionsPlan($productions_plan_id) {
        $data = [];
        $data['result'] = 1;
        $data['message'] = lang('success');

        $this->db->select('
            tbl_productions_orders_items.*,
            tbl_productions_orders_details.id as pod_id,
        ', false);
        $this->db->from('tbl_productions_orders_items');
        $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id');
        $this->db->where('tbl_productions_orders_items.plan_id', $productions_plan_id);
        $productions_orders_items = $this->db->get()->result_array();
        $productions_orders_id = 0;

        if ($productions_orders_items) {
            foreach ($productions_orders_items as $key => $value) {
                $pod_id = $value['pod_id'];
                $poi_id = $value['id'];

                $productions_orders_id = $value['productions_orders_id'];

                $this->db->from('tbl_suggest_exporting');
                $this->db->where('tbl_suggest_exporting.productions_orders_details_id', $pod_id);
                $this->db->where('tbl_suggest_exporting.productions_orders_details_id !=', 0);
                $rs = $this->db->count_all_results();
                if ($rs) {
                    $data['result'] = 0;
                    $data['message'] = lang('Đã tạo phiếu xuất kho sản xuất không thể sửa');
                    return $data;
                }

                $this->db->from('tbl_purchase_products');
                $this->db->where('tbl_purchase_products.productions_orders_details_id', $pod_id);
                $this->db->where('tbl_purchase_products.productions_orders_details_id !=', 0);
                $rs = $this->db->count_all_results();
                if ($rs) {
                    $data['result'] = 0;
                    $data['message'] = lang('Đã tạo nhập kho thành phẩm không thể sửa');
                    return $data;
                }

                //
                $this->db->from('tbl_productions_orders_items_stages');
                $this->db->where('tbl_productions_orders_items_stages.machines_id !=', 0);
                $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $poi_id);
                $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id !=', 0);
                $rs = $this->db->count_all_results();
                if ($rs) {
                    $data['result'] = 0;
                    $data['message'] = lang('Đã tạo điều độ sản xuất không thể sửa');
                    return $data;
                }

                //
                $production_plan_item_id = $value['production_plan_item_id'];
                if ($value['object_item_type'] == 'business_plan') {
                    $this->db->from('tbl_tranfer_business_item');
                    $this->db->where('tbl_tranfer_business_item.business_plan_item_id', $production_plan_item_id);
                    $rs = $this->db->count_all_results();
                    if ($rs) {
                        $data['result'] = 0;
                        $data['message'] = lang('Đã tạo giữ kho trên truyền không thể sửa');
                        return $data;
                    }
                }

                //
                $this->db->from('tbl_productions_orders_detail_staff');
                $this->db->where('tbl_productions_orders_detail_staff.productions_detail_id', $pod_id);
                $this->db->where('tbl_productions_orders_detail_staff.productions_detail_id !=', 0);
                $rs = $this->db->count_all_results();
                if ($rs) {
                    $data['result'] = 0;
                    $data['message'] = lang('Đã phân công nhân viên không thể sửa');
                    return $data;
                }
            }
        }

        $this->db->from('tbl_suggest_exporting');
        $this->db->where('tbl_suggest_exporting.po_id', $productions_orders_id);
        $this->db->where('tbl_suggest_exporting.po_id !=', 0);
        $rs = $this->db->count_all_results();
        if ($rs) {
            $data['result'] = 0;
            $data['message'] = lang('Đã tạo phiếu xuất kho sản xuất không thể sửa');
            return $data;
        }

        $this->db->from('tbl_purchase_products');
        $this->db->where('tbl_purchase_products.po_id', $productions_orders_id);
        $this->db->where('tbl_purchase_products.po_id !=', 0);
        $rs = $this->db->count_all_results();
        if ($rs) {
            $data['result'] = 0;
            $data['message'] = lang('Đã tạo nhập kho thành phẩm không thể sửa');
            return $data;
        }

        $this->db->from('tbl_manufactures');
        $this->db->where('tbl_manufactures.id_production_detail', $productions_orders_id);
        $this->db->where('tbl_manufactures.id_production_detail !=', 0);
        $rs = $this->db->count_all_results();
        if ($rs) {
            $data['result'] = 0;
            $data['message'] = lang('Đã tạo phiếu xả khổ không thể sửa');
            return $data;
        }

        return $data;
    }

    public function isTranferBusinessItem($poi_id) {

        $this->db->select('tbl_productions_orders_items.*');
        $this->db->from('tbl_productions_orders_items');
        $this->db->where('tbl_productions_orders_items.id', $poi_id);
        $this->db->where('tbl_productions_orders_items.object_item_type', 'business_plan');
        $productions_orders_items = $this->db->get()->row_array();
        if (!empty($productions_orders_items)) {
            $items_id = $productions_orders_items['items_id'];
            $production_plan_item_id = $productions_orders_items['production_plan_item_id'];

            $this->db->from('tbl_tranfer_business_item');
            $this->db->where('tbl_tranfer_business_item.business_plan_item_id', $production_plan_item_id);
            $is_tranfer_business_item = $this->db->get()->row_array();
            if (!empty($is_tranfer_business_item)) {
                return $production_plan_item_id;
            }
        }

        return false;
    }

    public function getDataTransferItemAndPurchase($business_plan_item_id, $pod_id) {
        $this->db->select('SUM(tbl_tranfer_business_item.quantity) as quantity', false);
        $this->db->from('tbl_tranfer_business_item');
        $this->db->where('tbl_tranfer_business_item.business_plan_item_id', $business_plan_item_id);
        $tranfer_business_item = $this->db->get()->row_array();

        $quantity_tranfer_business_item = (float)$tranfer_business_item['quantity'];

        $this->db->select('
            SUM(tbl_purchase_product_items.quantity) as quantity
        ', false);
        $this->db->from('tbl_purchase_products');
        $this->db->join('tbl_purchase_product_items', 'tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id');
        $this->db->where('tbl_purchase_products.type_business_plan', 1);
        $this->db->where('tbl_purchase_products.productions_orders_details_id', $pod_id);
        $purchase_product_items = $this->db->get()->row_array();
        $quantity_purchase_products = (float)$purchase_product_items['quantity'];

        $data['quantity_tranfer_business_item'] = $quantity_tranfer_business_item;
        $data['quantity_purchase_products'] = $quantity_purchase_products;

        return $data;
    }

    public function handlingPreventiveUpdate($productions_plan_id, $arrItemsPerventive, $dt_business_plan_preventive) {
        $business_plan_id = 0;
        $this->db->select('
            tbl_productions_plan.id,
            tbl_productions_plan.reference_no,
            tbl_productions_plan.date as date,
            tbl_productions_plan.id_branch as id_branch
        ', false);
        $this->db->from('tbl_productions_plan');
        $this->db->where('tbl_productions_plan.id', $productions_plan_id);
        $productions_plan = $this->db->get()->row_array();
        if (!empty($productions_plan)) {
            // $reference_no = getReference('business_plan');
            $reference_no = $dt_business_plan_preventive['reference_no'];
            $plan_name = 'Dự phòng kế hoạch NPL '.$productions_plan['reference_no'];

            $this->db->select('
                tbldepartments.departmentid as departmentid
            ', false);
            $this->db->from('tbldepartments');
            $this->db->order_by('tbldepartments.departmentid ASC');
            $this->db->limit(1);
            $dtDepartments = $this->db->get()->row_array();
            $departments = !empty($dtDepartments) ? $dtDepartments['departmentid'] : 1;

            $total_quantity = 0;
            $count_items = 0;
            $date = $productions_plan['date'];
            $date_sub = date_format(date_create($date), 'Y-m-d');
            $note = '';

            if (!empty($arrItemsPerventive)) {
                foreach ($arrItemsPerventive as $key => $value) {
                    $items_id = $value['product_id'];
                    $type_items = 'products';
                    $info = $this->products_model->rowProduct($items_id);
                    if (empty($info)) {
                        continue;
                    }

                    $items_code = $info['code'];
                    $items_name = $info['name'];
                    $quantity = $value['quantity_preventive'];
                    $total_quantity_sub = 0;

                    $sub = [];
                    $sub[] = [
                        'date' => $date_sub,
                        'quantity' => $quantity,
                        'quantity_plan_item' => $quantity,
                    ];

                    $items[] = [
                        // 'id' => $value['id'],
                        'id' => $value['productions_plan_items_id_item_object_id_preventive'],
                        'type_items' => $type_items,
                        'items_id' => $items_id,
                        'items_code' => $items_code,
                        'items_name' => $items_name,
                        'quantity' => $quantity,
                        'versions' => $value['versions_perventive'],
                        'versions_stage' => $value['versions_stages_perventive'],
                        'note_items' => '',
                        'order_item_id' => 0,
                        'unit_id' => $info['conversion_unit'],
                        'conversion_quantity_unit' => $info['conversion_quantity_unit'],
                        'sub' => $sub
                    ];

                    $total_quantity+= $quantity;
                }
            }

            $count_items = count($items);
            $_staff_id = get_staff_user_id();
            $options = [
                'id' => $dt_business_plan_preventive['id'],
                'date' => $date,
                'reference_no' => $reference_no,
                'plan_name' => $plan_name,
                'departments_id' => $departments,
                'total_quantity' => $total_quantity,
                'count_items' => $count_items,
                'note' => $note,
                'status' => 'approved',
                'date_status' => date('Y-m-d H:i:s'),
                'user_status' => $_staff_id,
                // 'productions_plan_id' => 0,
                'date_created' => $dt_business_plan_preventive['date_created'],
                'created_by' => $dt_business_plan_preventive['created_by'],
                'productions_plan_id' => 1,
                'date_updated' => date('Y-m-d H:i:s'),
                'updated_by' => $_staff_id,
                'order_id' => 0,
                'productions_plan_preventive_id' => $productions_plan_id,
                'id_branch' => $productions_plan['id_branch'],
            ];
            $business_plan_id = $this->business_plan_model->insertBusinessPlan($options);
            if ($business_plan_id) {
                // updateReference('business_plan');

                foreach ($items as $key => $value) {
                    $op = [
                        'id' => $value['id'],
                        'business_plan_id' => $business_plan_id,
                        'type_items' => $value['type_items'],
                        'items_id' => $value['items_id'],
                        'items_code' => $value['items_code'],
                        'items_name' => $value['items_name'],
                        'quantity' => $value['quantity'],
                        'quantity_plan' => $value['quantity'],
                        'note_items' => $value['note_items'],
                        'order_item_id' => $value['order_item_id'],
                        'unit_id' => $value['unit_id'],
                        'conversion_quantity_unit' => $value['conversion_quantity_unit'],
                    ];
                    $business_plan_item_id = $this->business_plan_model->insertBusinessPlanItems($op);
                    if ($business_plan_item_id) {
                        $items[$key]['business_plan_item_id'] = $business_plan_item_id;
                        $sb = $value['sub'];
                        foreach ($sb as $k => $val) {
                            $sb[$k]['business_plan_items_id'] = $business_plan_item_id;
                        }
                        if (!empty($sb)) {
                            $this->business_plan_model->insertBatchBusinessPlanItemsDate($sb);
                        }
                    }
                }

                if (!empty($items)) {
                    foreach ($items as $key => $value) {
                        $warehouses = "(
                            SELECT
                                SUM(tblwarehouse_items.product_quantity) as quantity_warehouses
                            FROM tblwarehouse_items
                            WHERE tblwarehouse_items.type_items = 'product' AND tblwarehouse_items.id_items = ".$value['items_id']."
                            GROUP BY tblwarehouse_items.id_items
                        )";
                        $dtW = $this->db->query($warehouses)->row_array();

                        $productions_plan_items = [
                            'productions_plan_id' => $productions_plan_id,
                            'type_object' => 'business_plan',
                            'object_id' => $business_plan_id,
                            'item_object_id' => $value['business_plan_item_id'],
                            'product_id' => $value['items_id'],
                            'quantity_minimum' => 0,
                            'quantity_warehouses' => (float)$dtW['quantity_warehouses'],
                            'status' => 'not',
                            'quantity_reserve' => 0,
                            'quantity_total_details' => $value['quantity'],
                            'versions' => $value['versions'],
                            'versions_stage' => $value['versions_stage'],
                            'is_preventive' => 1,
                        ];
                        $productions_plan_item_id = $this->manufactures_model->insertProductionsPlanItems($productions_plan_items);
                        if ($productions_plan_item_id) {
                            $productions_plan_details[] = [
                                'productions_plan_item_id' => $productions_plan_item_id,
                                'date' => $date_sub,
                                'quantity' => $value['quantity']
                            ];
                        }
                    }

                    if (!empty($productions_plan_details)) {
                        $this->db->insert_batch('tbl_productions_plan_details', $productions_plan_details);
                    }
                }

                $this->business_plan_model->handlingStagesBusinessPlan($business_plan_id);
                insertActivityLog([
                    'type_parent_obj' => 'business_plan',
                    'table_obj' => 'tbl_business_plan',
                    'id_obj' => $business_plan_id,
                    'name_obj' => $reference_no,
                    'content' => lang('tnh_his_edit_business_plan').' ['.$reference_no.']',
                    'actions' => 'edit'
                ]);
            }

        }
        return $business_plan_id;
    }

    public function autoProductionsOrdersUpdate($productions_plan_id, $dtProductionsOrders) {

        $this->db->select('
            tbl_productions_plan.id as id,
            tbl_productions_plan.date as date,
            tbl_productions_plan.id_branch as id_branch,
            tbl_productions_plan.options1 as options1,
            tbl_productions_plan.options2 as options2,
        ');
        $this->db->from('tbl_productions_plan');
        $this->db->where('tbl_productions_plan.id', $productions_plan_id);
        $dt_productions_plan = $this->db->get()->row_array();
        if (!empty($dt_productions_plan)) {

            $total_quantity = 0;
            $count_items = 0;
            $productions_plan_reference = '';
            $options1 = $dt_productions_plan['options1'];
            $options2 = $dt_productions_plan['options2'];
            $productions_plan_orders = [];
            $arrOrderId = [];
            $arrBusinessPlanId = [];
            $items = [];

            $this->db->select('
                tbl_productions_plan_items.type_object as type_object, 
                tbl_productions_plan_items.object_id as object_id
            ', false);
            $this->db->from('tbl_productions_plan_items');
            $this->db->where('tbl_productions_plan_items.productions_plan_id', $productions_plan_id);
            $this->db->group_by('tbl_productions_plan_items.type_object, tbl_productions_plan_items.object_id');
            $items_group = $this->db->get()->result_array();
            $productions_plan = [];

            if (!empty($items_group)) {
                foreach ($items_group as $key => $value) {
                    $object_type = $value['type_object'];
                    $object_id = $value['object_id'];

                    if ($object_type == "orders") {
                        $arrOrderId[] = $object_id;
                    } else if ($object_type == "business_plan") {
                        $arrBusinessPlanId[] = $object_id;
                    }

                    $strPO = $object_type.'__'.$object_id;
                    $productions_plan[] = $strPO;

                    $productions_plan_orders[$strPO] = [
                        'productions_plan_id' => $object_id,
                        'productions_order_id' => 0,
                        'total_quantity' => 0,
                        'count_items' => 0,
                        'object_type' => $object_type,
                    ];

                    $this->db->select('
                        tbl_productions_plan_items.*
                    ', false);
                    $this->db->from('tbl_productions_plan_items');
                    $this->db->where('tbl_productions_plan_items.productions_plan_id', $productions_plan_id);
                    $this->db->where('tbl_productions_plan_items.type_object', $object_type);
                    $this->db->where('tbl_productions_plan_items.object_id', $object_id);
                    $itemsP = $this->db->get()->result_array();
                    if (!empty($itemsP)) {
                        foreach ($itemsP as $k => $v) {
                            $info = $this->products_model->rowProduct($v['product_id']);
                            $items_code = $info['code'];
                            $items_name = $info['name'];
                            $quantity = $v['quantity_total_details'] + $v['quantity_reserve'];
                            $versions_bom = $v['versions'];
                            // $versions_stage = $info['versions_stage'];
                            $versions_stage = $v['versions_stage'];

                            $items[] = [
                                'production_plan_item_id' => $v['item_object_id'],
                                'type_items' => 'products',
                                'items_id' => $v['product_id'],
                                'items_code' => $items_code,
                                'items_name' => $items_name,
                                'quantity' => $quantity,
                                'note_items' => '',
                                'versions_bom' => $versions_bom,
                                'versions_stage' => $versions_stage,
                                'productions_capacity_items_id' => 0,
                                'object_item_type' => $object_type,
                                'plan_item_id' => $v['id'],
                                'plan_id' => $v['productions_plan_id'],
                                'arrBOM' => []
                            ];

                            $productions_plan_orders[$strPO]['count_items'] = $productions_plan_orders[$strPO]['count_items'] + 1;
                            $productions_plan_orders[$strPO]['total_quantity'] = $productions_plan_orders[$strPO]['total_quantity'] + $quantity;

                            $total_quantity+= $quantity;
                            $count_items++;
                        }
                    }
                }
            }

            if (!empty($arrOrderId)) {
                $productions_plan_reference = $this->manufactures_model->rowReferenceOrdersByArrId($arrOrderId)['reference_no'].', ';
            }

            if (!empty($arrBusinessPlanId)) {
                $productions_plan_reference.= $this->manufactures_model->rowReferenceBusinessPlanByArrId($arrBusinessPlanId)['reference_no'].', ';
            }

            if (!empty($productions_plan_reference)) {
                $productions_plan_reference = rtrim($productions_plan_reference, ', ');
            }

            $productions_plan = implode(',', $productions_plan);

            $staff_id = get_staff_user_id();
            $date_default = date('Y-m-d H:i:s');
            
            // $reference_no = getReference('productions_orders');
            $reference_no = $dtProductionsOrders['reference_no'];
            $date = $dt_productions_plan['date'];
            $location = $dt_productions_plan['id_branch'];
            $options = [
                'date' => $date,
                'reference_no' => $reference_no,
                'location_id' => $location,
                'productions_plan_id' => $productions_plan,
                'productions_plan_reference_no' => $productions_plan_reference,
                'total_quantity' => $total_quantity,
                'count_items' => $count_items,
                'note' => '',
                'status' => 'approved',
                'user_status' => $staff_id,
                'date_status' => $date_default,
                'date_created' => $dtProductionsOrders['date_created'],
                'created_by' => $dtProductionsOrders['created_by'],
                'updated_by' => $staff_id,
                'date_updated' => $date_default,
                'options1' => $options1,
                'options2' => $options2,
            ];
            $productions_orders_id = $this->manufactures_model->insertProductionsOrders($options);
            if ($productions_orders_id) {
                // updateReference('productions_orders');

                foreach ($items as $key => $value) {
                    $value['productions_orders_id'] = $productions_orders_id;
                    unset($value['arrBOM']);
                    $object_item_type = $value['object_item_type'];
                    $productions_orders_items_id = $this->manufactures_model->insertProductionsOrdersItems($value);
                    if ($productions_orders_items_id) {
                        $this->manufactures_model->updateQuantityProductionOrders($value['production_plan_item_id'], $value['quantity'], $plus = 1, $object_item_type);

                        $object_id = 0;
                        $object_type = $value['object_item_type'];
                        if ($object_type == "orders") {
                            $order_item = $this->orders_model->rowOrderItemsById($value['production_plan_item_id']);
                            $object_id = $order_item['order_id'];
                        } else if ($object_type == "business_plan") {
                            $business_item = $this->manufactures_model->rowBusinessItemsById($value['production_plan_item_id']);
                            $object_id = $business_item['business_plan_id'];
                        }

                        
                    }
                }

                if (!empty($productions_plan_orders)) {
                    foreach ($productions_plan_orders as $key => $value) {
                        $value['productions_order_id'] = $productions_orders_id;
                        $object_type = $value['object_type'];
                        if ($this->manufactures_model->insertProductionsPlanOrders($value)) {
                            $statusPL = 2;
                            if ($this->manufactures_model->checkPlanFullByOrder($value['productions_plan_id'], $object_type)) {
                                $statusPL = 1;
                            }
                            if ($object_type == "orders") {
                                $this->orders_model->updateOrdersNew($value['productions_plan_id'], ['status_productions_orders' => $statusPL]);
                            } else if ($object_type == "business_plan") {
                                $this->business_plan_model->updateBusinessPlan($value['productions_plan_id'], ['status_productions_orders' => $statusPL]);
                            }
                        }
                    }
                }

                $this->manufactures_model->handlingBomProductionsOrders($productions_orders_id);
                $this->manufactures_model->handlingProductionsDetail($productions_orders_id);
                $this->manufactures_model->handlingStagesProductionsOrders($productions_orders_id);

                // noti_custom('create_productions_orders', $productions_orders_id, get_staff_user_id(), 0, '', ['actions' => 'add']);
                insertActivityLog([
                    'type_parent_obj' => 'productions_orders',
                    'table_obj' => 'tbl_productions_orders',
                    'id_obj' => $productions_orders_id,
                    'name_obj' => $reference_no,
                    'content' => lang('tnh_his_edit_productions_orders') . ' [' . $reference_no . ']',
                    'actions' => 'edit'
                ]);

                return true;
            }
        }

        return false;
    }

    public function getProductionsPlanObject($productions_plan_id) {
        $this->db->select('*');
        $this->db->from('tbl_productions_plan_object');
        $this->db->where('tbl_productions_plan_object.productions_plan_id', $productions_plan_id);
        return $this->db->get()->result_array();
    }

    public function deleteProductionsPlanItems($productions_plan_id) {
        $this->db->where('tbl_productions_plan_items.productions_plan_id', $productions_plan_id);
        return $this->db->delete('tbl_productions_plan_items');
    }
	
	public function getAllStaffRole()
	{
		$this->db->select('tblstaff.staffid, tblstaff.lastname, tblstaff.firstname,role, CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname', false);
		$this->db->select('tblroles.name as name_role');
		$this->db->from('tblstaff');
		$this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
		return $this->db->get()->result_array();
	}

    public function checkPrepareMaterialsTotal($data)
    {
        $po_id = $data['po_id'];
        if (empty($po_id)) {
            return false;
        }

        $productions_plan = $this->manufactures_model->getProductionsPlanPO($po_id);
        $production_plan_id = $productions_plan['id'];
        if (empty($production_plan_id)) $production_plan_id = 0;

        $tbProductionsPlanCompensation = "(
            SELECT
                tbl_productions_plan_compensation.item_id, 
                tbl_productions_plan_compensation.item_type,
                tbl_productions_plan_compensation.quantity_primary as quantity_primary,
                tbl_productions_plan_compensation.quantity_compensation as quantity_compensation
            FROM tbl_productions_plan_compensation
            WHERE tbl_productions_plan_compensation.productions_plan_id = $production_plan_id
            GROUP BY tbl_productions_plan_compensation.item_id, tbl_productions_plan_compensation.item_type
        ) tb_productions_plan_compensation";

        $this->db->select('
            tbl_productions_orders_items_sub.type as type,
            tbl_productions_orders_items_sub.item_id as item_id,
            CONCAT(tbl_productions_orders_items_sub.type, "__", tbl_productions_orders_items_sub.item_id) as item_cs_id,
            SUM(tbl_productions_orders_items_sub.quantity) + tb_productions_plan_compensation.quantity_compensation as quantity,
            tbl_productions_orders_items_sub.quantity_exchange as quantity_exchange,
            tb_productions_plan_compensation.quantity_compensation as quantity_compensation,
            tbl_productions_orders_items_sub.is_single_use as is_single_use,
            tbl_productions_orders_items_sub.quantity_single as quantity_single,
        ', false);
        $this->db->from('tbl_productions_orders_items_sub');
        $this->db->join($tbProductionsPlanCompensation, 'tb_productions_plan_compensation.item_id = tbl_productions_orders_items_sub.item_id AND tb_productions_plan_compensation.item_type = tbl_productions_orders_items_sub.type', 'left');
        $this->db->where('tbl_productions_orders_items_sub.productions_orders_id', $po_id);
        $this->db->where('tbl_productions_orders_items_sub.type !=', 'element');
        $this->db->group_by('tbl_productions_orders_items_sub.type, tbl_productions_orders_items_sub.item_id');
        $itemsBOM = $this->db->get()->result_array();
        $is_export_npl = 1;
        $is_export_vtsx = 1;
        $flag_npl = 0;
        $flag_vtsx = 0;
        if (!empty($itemsBOM)) {
            foreach ($itemsBOM as $key => $value) {
                $item_type = $value['type'];
                $item_id = $value['item_id'];
                $is_vtsx = 0;
                
                if ($item_type == "materials") {
                    $info = $this->items_model->rowMaterial($item_id);
                    $category = get_table_where('tbl_category_items', ['id' => $info['category_id']], '', 'row_array', '', 'is_vtsx');
                    if (!empty($category) && $category['is_vtsx'] == 1) {
                        $is_vtsx = 1;
                    }
                }

                if ($is_vtsx == 1 && $flag_vtsx == 0) {
                    $flag_vtsx = 1;
                } else if ($flag_npl == 0 && $is_vtsx == 0) {
                    $flag_npl = 1;
                }

                $this->db->select('
                    SUM(tbl_suggest_exporting_items.quantity_export) as quantity_export
                ', false);
                $this->db->from('tbl_suggest_exporting');
                $this->db->join('tbl_suggest_exporting_items', 'tbl_suggest_exporting_items.suggest_exporting_id = tbl_suggest_exporting.id');
                $this->db->where('tbl_suggest_exporting.po_id', $po_id);
                $this->db->where('tbl_suggest_exporting.stage_id', STAGES_MATERIAL);
                $this->db->where('tbl_suggest_exporting_items.type_item', $item_type);
                $this->db->where('tbl_suggest_exporting_items.item_id', $item_id);
                $suggest_exporting = $this->db->get()->row_array();
                $quantity_export = (float)$suggest_exporting['quantity_export'] ?? 0;
                $quantity = (float)$value['quantity'];
                
                $quantity_rest = $quantity - $quantity_export;
                if (toZeroIfSmall($quantity_rest) > 0) {
                    if ($is_vtsx == 1) {
                        $is_export_vtsx = 0;
                    } else {
                        $is_export_npl = 0;
                    }
                }
            }
        }

        if ($flag_npl == 0) {
            $is_export_npl = 2;
        }
        
        if ($flag_vtsx == 0) {
            $is_export_vtsx = 2;
        }
        // if ($po_id == 35614) {
        //     print_arrays($is_export_npl, '<br>', $is_export_vtsx, '<br>', $flag_npl, '<br>', $flag_vtsx);
        // }

        $option = [
            'is_export_npl' => $is_export_npl,
            'is_export_vtsx' => $is_export_vtsx
        ];

        $this->db->where('tbl_productions_orders.id', $po_id);
        $this->db->update('tbl_productions_orders', $option);
    }
}