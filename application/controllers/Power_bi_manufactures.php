<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Power_bi_manufactures extends REST_Controller 
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getTypeOrders() {
        $this->db->select('
            tbl_type_orders.id as id,
            tbl_type_orders.code as code,
            tbl_type_orders.name as name,
            tbl_type_orders.color as color,
        ', false);
        $this->db->from('tbl_type_orders');
        $type_orders = $this->db->get()->result();
        $this->response($type_orders, REST_Controller::HTTP_OK);
    }

    public function getOrdersManufactures() {
        $this->db->select('
            tbl_orders.id as id_order,
            tbl_orders.date as date_order,
            tbl_orders.reference_no as reference_no_order,
            tbl_orders.customer_id as customer_id,
            tbl_orders.type_orders as type_orders,
            tbl_orders.created_by as created_by,
            tbl_orders.grand_total as grand_total,
            tbl_orders.total_tax as total_tax,
            tbl_orders.grand_total_quantity as grand_total_quantity,
            CONCAT("orders__", tbl_orders.id) as object_type_id
        ', false);
        $this->db->from('tbl_orders');
        $this->db->where('tbl_orders.status_productions_orders !=', 0);
        $orders = $this->db->get()->result();
        $this->response($orders, REST_Controller::HTTP_OK);
    }

    public function getProductionsOrders() {
        $this->db->select('
            tbl_productions_orders.id as po_id,
            tbl_productions_orders.date as date_po,
            tbl_productions_orders.total_quantity as total_quantity,
            tbl_productions_orders.created_by as created_by,
        ');
        $this->db->from('tbl_productions_orders');
        $productions_orders = $this->db->get()->result();
        $this->response($productions_orders, REST_Controller::HTTP_OK);
    }

    public function getProductionsOrdersItems() {
        $this->db->select('
            tbl_productions_orders_items.id as poi_id,
            tbl_productions_orders_items.items_id as items_id,
            tbl_productions_orders_items.production_plan_item_id as object_item_id,
            tbl_productions_orders_items.object_item_type as object_item_type,
            tbl_productions_orders_items.quantity as quantity,
            CONCAT(tbl_productions_orders_items.object_item_type, "__", tbl_productions_orders_items.object_item_type) as object_item_type_id,
            tbl_productions_orders_items.productions_orders_id as po_id,
            tbl_productions_orders_items.plan_item_id as plan_item_id,
            tbl_productions_orders_items.plan_id as plan_id,
        ', false);
        $this->db->from('tbl_productions_orders_items');
        $productions_orders_items = $this->db->get()->result();
        $this->response($productions_orders_items, REST_Controller::HTTP_OK);
    }

    public function getProductionsPlanOrders() {
        $this->db->select('
            tbl_productions_plan_orders.productions_plan_id as object_id,
            tbl_productions_plan_orders.object_type as object_type,
            tbl_productions_plan_orders.productions_order_id as productions_order_id,
            CONCAT(tbl_productions_plan_orders.object_type, "__", tbl_productions_plan_orders.productions_plan_id) as object_type_id,
            tbl_productions_plan_orders.total_quantity as total_quantity,
            tbl_productions_plan_orders.count_items as count_items,
        ');
        $this->db->from('tbl_productions_plan_orders');
        $productions_plan_orders = $this->db->get()->result();
        $this->response($productions_plan_orders, REST_Controller::HTTP_OK);
    }

    public function getBusinessManufactures() {
        $this->db->select('
            tbl_business_plan.id as id_business_plan,
            tbl_business_plan.date as date_business_plan,
            tbl_business_plan.reference_no as reference_no_order,
            tbl_business_plan.created_by as created_by,
            tbl_business_plan.total_quantity as total_quantity,
            CONCAT("business_plan__", tbl_business_plan.id) as object_type_id
        ', false);
        $this->db->from('tbl_business_plan');
        $this->db->where('tbl_business_plan.status_productions_orders !=', 0);
        $business_plan = $this->db->get()->result();
        $this->response($business_plan, REST_Controller::HTTP_OK);
    }

    public function getProductionsPlanItems() {
        $this->db->select('
            tbl_productions_plan_items.id as id,
            tbl_productions_plan_items.productions_plan_id as productions_plan_id,
            tbl_productions_plan_items.type_object as type_object,
            tbl_productions_plan_items.object_id as object_id,
            tbl_productions_plan_items.item_object_id as item_object_id,
            tbl_productions_plan_items.product_id as product_id,
            tbl_productions_plan_items.is_preventive as is_preventive,
            tbl_productions_plan_items.quantity_total_details as quantity_total_details,
        ');
        $this->db->from('tbl_productions_plan_items');
        $productions_plan_items = $this->db->get()->result();
        $this->response($productions_plan_items, REST_Controller::HTTP_OK);
    }

    public function getProductionsOrdersDetail() {
        $this->db->select('
            tbl_productions_orders_details.id as pod_id,
            tbl_productions_orders_details.productions_orders_id as po_id,
            tbl_productions_orders_details.productions_orders_item_id as poi_id,
            tbl_productions_orders_details.created_by as created_by,
            tbl_productions_orders_details.object_type as object_type,
            tbl_productions_orders_details.object_id as object_id,
            CONCAT(tbl_productions_orders_details.object_type, "__", tbl_productions_orders_details.object_id) as object_type_id,
        ', false);
        $this->db->from('tbl_productions_orders_details');
        $productions_orders_details = $this->db->get()->result();
        $this->response($productions_orders_details, REST_Controller::HTTP_OK);
    }

    public function getPurchaseProducts() {
        $this->db->select('
            tbl_purchase_products.id,
            tbl_purchase_products.productions_orders_details_id as pod_id,
            tbl_purchase_products.warehouseman_id as warehouseman_id,
            tbl_purchase_products.pois_id as pois_id,
            tbl_purchase_products.final_stage as final_stage,
            tbl_purchase_products.is_errors as is_errors,
            tbl_purchase_products.total_quantity as total_quantity,
            tbl_purchase_products.date as date
        ');
        $this->db->from('tbl_purchase_products');
        $this->db->where('tbl_purchase_products.warehouseman_id >', 0);
        $purchase_products = $this->db->get()->result();
        $this->response($purchase_products, REST_Controller::HTTP_OK);
    }

    public function getPurchaseProductItems() {
        $this->db->select('
            tbl_purchase_product_items.id,
            tbl_purchase_product_items.purchase_product_id,
            tbl_purchase_product_items.item_id,
            tbl_purchase_product_items.quantity,
        ');
        $this->db->from('tbl_purchase_product_items');
        $purchase_product_items = $this->db->get()->result();
        $this->response($purchase_product_items, REST_Controller::HTTP_OK);
    }

    public function getCategoryStages() {
        $this->db->select('
            tbl_category_stages.id as id_category_stages,
            tbl_category_stages.code as code_category_stages,
            tbl_category_stages.name as name_category_stages,
            tbl_category_stages.is_in as is_in,
            tbl_category_stages.check_offset as check_offset,
        ', false);
        $this->db->from('tbl_category_stages');
        $this->db->where('tbl_category_stages.is_in', 1);
        $category_stages = $this->db->get()->result();
        $this->response($category_stages, REST_Controller::HTTP_OK);
    }

    public function getStages() {
        $this->db->select('
            tbl_stages.id as id_stages,
            tbl_stages.name as name_stages,
            tbl_stages.code as code_stages,
            tbl_stages.category_stages as category_stages_id
        ', false);
        $this->db->from('tbl_stages');
        $stages = $this->db->get()->result();
        $this->response($stages, REST_Controller::HTTP_OK);
    }

    public function productionsOrdersItemsStages() {
        $this->db->select('
            tbl_productions_orders_items_stages.id as pois_id,
            tbl_productions_orders_items_stages.productions_orders_id as po_id,
            tbl_productions_orders_items_stages.productions_orders_items_id as poi_id,
            tbl_productions_orders_items_stages.stage_id as stage_id,
            tbl_productions_orders_items_stages.number as number,
            tbl_productions_orders_items_stages.active as active,
        ');
        $this->db->from('tbl_productions_orders_items_stages');
        $productions_orders_items_stages = $this->db->get()->result();
        $this->response($productions_orders_items_stages, REST_Controller::HTTP_OK);
    }

    public function getPurchaseProductsFinished() {
        $this->db->select('
            tbl_purchase_products.id,
            tbl_purchase_products.productions_orders_details_id as pod_id,
            tbl_purchase_products.warehouseman_id as warehouseman_id,
            tbl_purchase_products.pois_id as pois_id,
            tbl_purchase_products.final_stage as final_stage,
            tbl_purchase_products.is_errors as is_errors,
            tbl_purchase_products.total_quantity as total_quantity,
            tbl_purchase_products.date as date
        ');
        $this->db->from('tbl_purchase_products');
        $this->db->where('tbl_purchase_products.final_stage', 1);
        $this->db->where('tbl_purchase_products.warehouseman_id >', 0);
        $purchase_products = $this->db->get()->result();
        $this->response($purchase_products, REST_Controller::HTTP_OK);
    }

    public function getListCheckQuantity() {

        $tb_tam = "(
            SELECT
                tbl_check_quality_items.check_quality_id,
                SUM(tbl_check_quality_items.quantity_recycling) as quantity_recycling,
                SUM(tbl_check_quality_items.quantity_success) as quantity_success
            FROM tbl_check_quality_items
            GROUP BY tbl_check_quality_items.check_quality_id
        ) tb_tamp";

        $this->db->select('
            tbl_check_quality.id as id,
            tbl_check_quality.reference_no as reference_no,
            tbl_check_quality.date as date,
            tbl_check_quality.quantity_qc as quantity_qc,
            COALESCE(tb_tamp.quantity_recycling,0) as quantity_error,
            COALESCE(tb_tamp.quantity_success,0) as quantity_success
        ');
        $this->db->from('tbl_check_quality');
        $this->db->join($tb_tam,'tb_tamp.check_quality_id = tbl_check_quality.id');
        $checkQuantity = $this->db->get()->result();
        $this->response($checkQuantity, REST_Controller::HTTP_OK);
    }

    public function getListCheckQuantityItems() {
        $this->db->select('
            tbl_check_quality_items.id as check_quality_item_id,
            tbl_check_quality_items.check_quality_id as check_quality_id,
            tbl_check_quality_items.item_id as item_id,
            tbl_check_quality_items.pod_id as pod_id,
            tbl_check_quality_items.order_id as order_id,
            tbl_check_quality_items.quantity_qc as quantity_qc,
            tbl_check_quality_items.quantity_recycling as quantity_recycling,
            tbl_check_quality_items.quantity_success as quantity_success,
        ');
        $this->db->from('tbl_check_quality_items');
        $checkQuantityItems = $this->db->get()->result();
        $this->response($checkQuantityItems, REST_Controller::HTTP_OK);
    }

    public function getListCheckQuantityItemsError() {
        $this->db->select('
            tbl_check_quality_items_error.id_check_quality_item as id_check_quality_item,
            tbl_check_quality_items_error.id_error as id_error,
            tbl_check_quality_items_error.quantity as quantity,
        ');
        $this->db->from('tbl_check_quality_items_error');
        $checkQuantityItemsError = $this->db->get()->result();
        $this->response($checkQuantityItemsError, REST_Controller::HTTP_OK);
    }

    public function getListProducts() {
        $this->db->select('
            tbl_products.id as id,
            tbl_products.name as name,
            tbl_products.code as code,
        ');
        $this->db->from('tbl_products');
        $products = $this->db->get()->result();
        $this->response($products, REST_Controller::HTTP_OK);
    }

    public function getListError() {
        $this->db->select('
            tbl_detail_errors.id as id,
            tbl_detail_errors.name as name,
            tbl_detail_errors.code as code,
        ');
        $this->db->from('tbl_detail_errors');
        $detailError = $this->db->get()->result();
        $this->response($detailError, REST_Controller::HTTP_OK);
    }

    public function getListCheckQuantityItemsOrder() {
        $this->db->select('
            tbl_check_quality_items.id as check_quality_item_id,
            tbl_check_quality_items.check_quality_id as check_quality_id,
            tbl_check_quality_items.item_id as item_id,
            tbl_check_quality_items.pod_id as pod_id,
            tbl_check_quality_items.order_id as order_id,
            tbl_check_quality_items.quantity_qc as quantity_qc,
            tbl_check_quality_items.quantity_recycling as quantity_recycling,
            tbl_check_quality_items.quantity_success as quantity_success,
        ');
        $this->db->from('tbl_check_quality_items');
        $this->db->where('tbl_check_quality_items.object_type','orders');
        $this->db->where('tbl_check_quality_items.order_id > 0');
        $checkQuantityItemsOrder = $this->db->get()->result();
        $this->response($checkQuantityItemsOrder, REST_Controller::HTTP_OK);
    }


    public function getProductionsOrdersDetailOrder() {
        $this->db->select('
            tbl_productions_orders_details.id as pod_id,
            tbl_productions_orders_details.productions_orders_id as po_id,
            tbl_productions_orders_details.productions_orders_item_id as poi_id,
            tbl_productions_orders_details.created_by as created_by,
            tbl_productions_orders_details.object_type as object_type,
            tbl_productions_orders_details.object_id as object_id,
        ', false);
        $this->db->from('tbl_productions_orders_details');
        $this->db->where('tbl_productions_orders_details.object_type','orders');
        $productions_orders_details = $this->db->get()->result();
        $this->response($productions_orders_details, REST_Controller::HTTP_OK);
    }

    public function getListOrders() {
        $this->db->select('
            tbl_orders.id as order_id,
            tbl_orders.reference_no as reference_no,
            tblclients.company as company,
            tblclients.company_short as company_short,
        ');
        $this->db->from('tbl_orders');
        $this->db->join('tblclients','tblclients.userid = tbl_orders.customer_id');
        $dtOrders = $this->db->get()->result();
        $this->response($dtOrders, REST_Controller::HTTP_OK);
    }
    
}