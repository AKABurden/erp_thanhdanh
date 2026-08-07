<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Business_plan_model extends App_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function insertBusinessPlan($data)
    {
    	$this->db->insert('tbl_business_plan', $data);
    	return $this->db->insert_id();
    }

    public function updateBusinessPlan($id, $data)
    {
    	$this->db->where('id', $id);
    	return $this->db->update('tbl_business_plan', $data);
    }

    public function deleteBusinessPlan($id)
    {
    	$this->db->where('id', $id);
    	return $this->db->delete('tbl_business_plan');
    }

    public function insertBusinessPlanItems($data)
    {
    	$this->db->insert('tbl_business_plan_items', $data);
    	return $this->db->insert_id();
    }

    public function updateBusinessPlanItems($id, $data)
    {
    	$this->db->where('id', $id);
    	return $this->db->update('tbl_business_plan_items', $data);
    }

    public function insertBusinessPlanItemsDate($data)
    {
    	$this->db->insert('tbl_business_plan_items_date', $data);
    	return $this->db->insert_id();
    }

    public function deleteBusinessPlanItemsDateBusinessPlanItemsId($business_plan_items_id)
    {
    	$this->db->where('business_plan_items_id', $business_plan_items_id);
    	return $this->db->delete('tbl_business_plan_items_date');
    }

    public function insertBatchBusinessPlanItemsDate($data)
    {
    	return $this->db->insert_batch('tbl_business_plan_items_date', $data);
    }

    public function rowBusinessPlanById($id)
    {
    	$this->db->select('*');
    	$this->db->from('tbl_business_plan');
    	$this->db->where('tbl_business_plan.id', $id);
    	return $this->db->get()->row_array();
    }

    public function getBusinessPlanItemsByBusinessPlanId($business_plan_id)
    {
    	$this->db->select('tbl_business_plan_items.*, tbl_products.images as images');
    	$this->db->from('tbl_business_plan_items');
    	$this->db->join('tbl_products', 'tbl_products.id = tbl_business_plan_items.items_id AND tbl_business_plan_items.type_items = "products"', 'left');
    	$this->db->where('tbl_business_plan_items.business_plan_id', $business_plan_id);
    	return $this->db->get()->result_array();
    }

    public function getBusinessPlanItemsDateByBusinessPlanItemsId($business_plan_items_id)
    {
    	$this->db->select('*');
    	$this->db->from('tbl_business_plan_items_date');
    	$this->db->where('business_plan_items_id', $business_plan_items_id);
    	return $this->db->get()->result_array();
    }

    public function getBusinessPlanItemsByNotArrId($arr_id, $business_plan_id) {
    	$this->db->select('*');
    	$this->db->from('tbl_business_plan_items');
        if (!empty($arr_id))
        {
            $this->db->where_not_in('tbl_business_plan_items.id', $arr_id);
        }
    	$this->db->where('tbl_business_plan_items.business_plan_id', $business_plan_id);
    	return $this->db->get()->result_array();
    }

    public function getBusinessPlanItems($business_plan_id) {
    	$this->db->select('tbl_business_plan_items.*');
    	$this->db->from('tbl_business_plan_items');
    	$this->db->where('tbl_business_plan_items.business_plan_id', $business_plan_id);
    	return $this->db->get()->result_array();
    }

    public function deleteBusinessPlanItems($id)
    {
    	$this->db->where('id', $id);
    	return $this->db->delete('tbl_business_plan_items');
    }

    public function updateBusinessPlanByProductionPlan($productions_plan_id, $data)
    {
        $this->db->where('productions_plan_id', $productions_plan_id);
        return $this->db->update('tbl_business_plan', $data);
    }

    public function searchBusinessPlan($q, $limit = 50)
    {
        $branch_staff = get_array_branch_staff();
        $is_admin = is_admin();

        $this->db->select('tbl_business_plan.id as id, tbl_business_plan.reference_no as text', false);
        $this->db->from('tbl_business_plan');
        if (!empty($q))
        {
            $this->db->group_start();
            $this->db->like('tbl_business_plan.reference_no', $q);
            $this->db->or_like('tbl_business_plan.reference_no', $q);
            $this->db->group_end();
        }

        if (!$is_admin) {
            if (empty($branch_staff)) $branch_staff = [0];
            $this->db->where('tbl_business_plan.id_branch IN ('.implode(',', $branch_staff).')', false, false);
        }

        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function isPlanItemsDate($id)
    {
        $this->db->from('tbl_business_plan_items');
        $this->db->join('tbl_business_plan_items_date', 'tbl_business_plan_items_date.business_plan_items_id = tbl_business_plan_items.id');
        $this->db->where('tbl_business_plan_items.business_plan_id', $id);
        return $this->db->get()->num_rows();
    }

    public function getMaxMinBusinessPlan($id)
    {
        $this->db->select("
            min(tbl_business_plan_items_date.date) as date_start,
            max(tbl_business_plan_items_date.date) as date_end,
        ", false);
        $this->db->from('tbl_business_plan_items');
        $this->db->join('tbl_business_plan_items_date', 'tbl_business_plan_items_date.business_plan_items_id = tbl_business_plan_items.id');
        $this->db->where('tbl_business_plan_items.business_plan_id', $id);
        return $this->db->get()->row_array();
    }

    public function getItemsProductionsPlan($id) {

        $warehouses = "(
            SELECT
                tblwarehouse_items.id_items,
                SUM(tblwarehouse_items.product_quantity) as quantity_warehouses
            FROM tblwarehouse_items
            WHERE tblwarehouse_items.type_items = 'product'
            GROUP BY tblwarehouse_items.id_items
        ) warehouses";

        $this->db->select('
            tbl_business_plan_items.*,
            tbl_products.code as items_code,
            tbl_products.name as items_name,
            tbl_products.quantity_minimum as quantity_minimum,
            COALESCE(warehouses.quantity_warehouses, 0) as quantity_warehouses,
        ', false);
        $this->db->from('tbl_business_plan_items');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_business_plan_items.items_id');
        $this->db->join($warehouses, 'warehouses.id_items = tbl_business_plan_items.items_id', 'left');
        $this->db->where('tbl_business_plan_items.business_plan_id', $id);
        $items = $this->db->get()->result_array();
        $arrItems = [];
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $business_plan_items_id = $value['id'];
                $this->db->select('*');
                $this->db->from('tbl_business_plan_items_date');
                $this->db->where('tbl_business_plan_items_date.business_plan_items_id', $business_plan_items_id);
                $this->db->where('(tbl_business_plan_items_date.quantity > tbl_business_plan_items_date.quantity_plan_item)');
                $business_plan_items_date = $this->db->get()->result_array();
                if (empty($business_plan_items_date)) continue;

                $arrItems[$key] = $value;
                $arrItems[$key]['arrDate'] = $business_plan_items_date;
            }
        }

        return $arrItems;
    }

    public function deleteBusinessPlanItemsStages($business_plan_id) {
        $this->db->where('tbl_business_plan_items_stages.business_plan_id', $business_plan_id);
        return $this->db->delete('tbl_business_plan_items_stages');
    }

    public function handlingStagesBusinessPlan($business_plan_id) {
        $this->deleteBusinessPlanItemsStages($business_plan_id);
        $this->db->select('
            tbl_business_plan_items.id as business_plan_item_id,
            tbl_business_plan_items.items_id as item_id,
            tbl_products.versions_stage as versions_stage
        ', false);
        $this->db->from('tbl_business_plan_items');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_business_plan_items.items_id');
        $this->db->where('tbl_business_plan_items.type_items', 'products');
        $this->db->where('tbl_business_plan_items.business_plan_id', $business_plan_id);
        $business_plan = $this->db->get()->result_array();
        if (!empty($business_plan)) {
            $arrayItems = [];
            $arrBusinessPlanItems = [];
            foreach ($business_plan as $key => $value) {
                $versions_stage = $value['versions_stage'];
                $item_id = $value['item_id'];
                if (empty($versions_stage)) continue;
                $arrBusinessPlanItems[] = [
                    'id' => $value['business_plan_item_id'],
                    'versions_stage' => $versions_stage
                ];

                $this->db->select('tbl_product_stages.id');
                $this->db->from('tbl_product_stages');
                $this->db->where('tbl_product_stages.product_id', $item_id);
                $this->db->where('tbl_product_stages.versions', $versions_stage);
                $product_stages = $this->db->get()->row_array();
                if (!empty($product_stages)) {
                    $product_stages_id = $product_stages['id'];
                    $this->db->select('*');
                    $this->db->from('tbl_product_stages_versions');
                    $this->db->where('tbl_product_stages_versions.version_id', $product_stages_id);
                    $product_stages_versions = $this->db->get()->result_array();
                    if (!empty($product_stages_versions)) {
                        foreach ($product_stages_versions as $k => $val) {
                            $arrayItems[] = [
                                'business_plan_id' => $business_plan_id,
                                'business_plan_items_id' => $value['business_plan_item_id'],
                                'stage_id' => $val['stage_id'],
                                'number' => $val['number'],
                                'number_hours' => $val['number_hours'],
                                'final_stage' => $val['final_stage'],
                            ];
                        }
                    }
                }
            }

            if (!empty($arrayItems)) {
                $this->db->insert_batch('tbl_business_plan_items_stages', $arrayItems);
            }

            if (!empty($arrBusinessPlanItems)) {
                $this->db->update_batch('tbl_business_plan_items', $arrBusinessPlanItems, 'id');
            }
        }

        return true;
    }
}