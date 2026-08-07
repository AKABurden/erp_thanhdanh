<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Production_list_model extends App_Model
{
	private $contact_columns;

    public function __construct()
    {
        parent::__construct();
    }

    public function insertProductionLists($data) {
        $this->db->insert('tbl_production_lists', $data);
        return $this->db->insert_id();
    }

    public function insertProductionListsTotal($data) {
        $this->db->insert('tbl_production_lists_total', $data);
        return $this->db->insert_id();
    }

    public function insertBatchProductionListsItems($data) {
        return $this->db->insert_batch('tbl_production_lists_items', $data);
    }

    public function insertBatchProductionListsDate($data) {
        return $this->db->insert_batch('tbl_production_lists_date', $data);
    }

    public function deleteProductionLists($id) {
        $this->db->where('tbl_production_lists.id', $id);
        return $this->db->delete('tbl_production_lists');
    }

    public function deleteProductionListsTotal($production_list_id) {
        $this->db->where('tbl_production_lists_total.production_list_id', $production_list_id);
        return $this->db->delete('tbl_production_lists_total');
    }

    public function deleteProductionListsItems($production_list_id) {
        $this->db->where('tbl_production_lists_items.production_list_id', $production_list_id);
        return $this->db->delete('tbl_production_lists_items');
    }

    public function deleteProductionListsDate($production_list_id) {
        $this->db->where('tbl_production_lists_date.production_list_id', $production_list_id);
        return $this->db->delete('tbl_production_lists_date');
    }

    public function getProductionListsById($id) {
        $this->db->select('tbl_production_lists.*');
        $this->db->from('tbl_production_lists');
        $this->db->where('tbl_production_lists.id', $id);
        return $this->db->get()->row_array();
    }

    public function getProductionListsDate($production_list_id) {
        $this->db->select('tbl_production_lists_date.*');
        $this->db->from('tbl_production_lists_date');
        $this->db->where('tbl_production_lists_date.production_list_id', $production_list_id);
        return $this->db->get()->result_array();
    }

    public function getProductionListsItems($production_list_id) {
        $this->db->select('tbl_production_lists_items.*');
        $this->db->from('tbl_production_lists_items');
        $this->db->where('tbl_production_lists_items.production_list_id', $production_list_id);
        return $this->db->get()->result_array();
    }

    public function getProductionListsTotal($production_list_id) {
        $this->db->select('tbl_production_lists_total.*');
        $this->db->from('tbl_production_lists_total');
        $this->db->where('tbl_production_lists_total.production_list_id', $production_list_id);
        return $this->db->get()->result_array();
    }

    public function updateProductionLists($id, $data) {
        $this->db->where('tbl_production_lists.id', $id);
        return $this->db->update('tbl_production_lists', $data);
    }

    public function deleteProductionListsTotalTypeProductionlistId($production_list_id, $type_productionlist_id) {
        $this->db->where('tbl_production_lists_total.production_list_id', $production_list_id);
        $this->db->where('tbl_production_lists_total.type_productionlist_id', $type_productionlist_id);
        return $this->db->delete('tbl_production_lists_total');
    }

    public function deleteProductionListsItemsTypeProductionlistId($production_list_id, $type_productionlist_id) {
       
        // $this->db->join('tbl_production_lists_total', 'tbl_production_lists_total.id = tbl_production_lists_items.production_list_total_id');
        $this->db->where('tbl_production_lists_items.production_list_id', $production_list_id);
        $this->db->where(' exists (
            SELECT 1
            FROM tbl_production_lists_total
            WHERE tbl_production_lists_total.id = tbl_production_lists_items.production_list_total_id AND tbl_production_lists_total.type_productionlist_id = '.$type_productionlist_id.'
        )', false, false);
        return $this->db->delete('tbl_production_lists_items');
    }

    public function deleteProductionListsDateTypeProductionlistId($production_list_id, $type_productionlist_id) {
        // $this->db->join('tbl_production_lists_total', 'tbl_production_lists_total.id = tbl_production_lists_date.production_list_total_id');
        // $this->db->where('tbl_production_lists_total.type_productionlist_id', $type_productionlist_id);
        $this->db->where('tbl_production_lists_date.production_list_id', $production_list_id);
        $this->db->where(' exists (
            SELECT 1
            FROM tbl_production_lists_total
            WHERE tbl_production_lists_total.id = tbl_production_lists_date.production_list_total_id AND tbl_production_lists_total.type_productionlist_id = '.$type_productionlist_id.'
        )', false, false);
        return $this->db->delete('tbl_production_lists_date');
    }

    public function getTypeProductionsListsTotal($production_list_id) {
        // $this->db->select('
        //     tbl_production_lists_total.type_productionlist_id as type_productionlist_id,
        //     tbl_type_productionlist.code as code,
        //     GROUP_CONCAT(tbl_production_lists_total.id) as id_plt 
        // ', false);
        // $this->db->group_by('tbl_production_lists_total.type_productionlist_id');

        $this->db->select('
            tbl_production_lists_total.*,
            tbl_type_productionlist.code as code
        ', false);
        $this->db->from('tbl_production_lists_total');
        $this->db->join('tbl_type_productionlist', 'tbl_type_productionlist.id = tbl_production_lists_total.type_productionlist_id');
        $this->db->where('tbl_production_lists_total.production_list_id', $production_list_id);
        $this->db->order_by('tbl_production_lists_total.type_productionlist_id ASC');
        return $this->db->get()->result_array();
    }

    public function getProductionListsDatePLT($production_list_total_id) {
        $this->db->select('
            tbl_production_lists_date.*
        ', false);
        $this->db->from('tbl_production_lists_date');
        $this->db->where('tbl_production_lists_date.production_list_total_id', $production_list_total_id);
        return $this->db->get()->result_array();
    }

    public function getProductionListsItemsView($production_list_total_id) {
        $this->db->select('
            tbl_production_lists_items.*,
            tbl_productions_orders.reference_no as reference_no,
            tbl_products.code as item_code,
            tbl_products.name as item_name,
            CONCAT(tbl_machines.code, "(", tbl_machines.name,")") as machines_name
        ', false);
        $this->db->from('tbl_production_lists_items');
        $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_production_lists_items.po_id');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_production_lists_items.item_id');
        $this->db->join('tbl_machines', 'tbl_machines.id = tbl_production_lists_items.may_in', 'left');
        $this->db->where('production_list_total_id', $production_list_total_id);
        return $this->db->get()->result_array();
    }

    public function getTypeProductionList() {
        $this->db->select('tbl_type_productionlist.*', false);
        $this->db->from('tbl_type_productionlist');
        $this->db->order_by('tbl_type_productionlist.id ASC');
        return $this->db->get()->result_array();
    }

    public function getStatusProductionsLists() {
        $this->db->select('tbl_status_productions_lists.*');
        $this->db->from('tbl_status_productions_lists');
        return $this->db->get()->result_array();
    }

    public function updateProductionListsItems($id, $data) {
        $this->db->where('tbl_production_lists_items.id', $id);
        return $this->db->update('tbl_production_lists_items', $data);
    }

    public function getProductionListsItemsByUp($po_id, $production_list_id, $stage_id) {
        $this->db->select('
            tbl_production_lists_items.*
        ', false);
        $this->db->from('tbl_production_lists_items');
        $this->db->where('tbl_production_lists_items.po_id', $po_id);
        $this->db->where('tbl_production_lists_items.production_list_id', $production_list_id);
        $this->db->where('tbl_production_lists_items.stage_id', $stage_id);
        return $this->db->get()->row_array();
    }

    public function insertModerationPlan($data) {
        $this->db->insert('tbl_moderation_plan', $data);
        return $this->db->insert_id();
    }

    public function updateModerationPlan($po_id, $item_id, $type_productionlist_id, $stage_id, $data) {
        $this->db->where('tbl_moderation_plan.po_id', $po_id);
        $this->db->where('tbl_moderation_plan.item_id', $item_id);
        $this->db->where('tbl_moderation_plan.type_productionlist_id', $type_productionlist_id);
        $this->db->where('tbl_moderation_plan.stage_id', $stage_id);
        return $this->db->update('tbl_moderation_plan', $data);
    }

    public function getModerationPlan($po_id, $item_id, $type_productionlist_id, $stage_id) {
        $this->db->select('tbl_moderation_plan.*');
        $this->db->from('tbl_moderation_plan');
        $this->db->where('tbl_moderation_plan.po_id', $po_id);
        $this->db->where('tbl_moderation_plan.item_id', $item_id);
        $this->db->where('tbl_moderation_plan.type_productionlist_id', $type_productionlist_id);
        $this->db->where('tbl_moderation_plan.stage_id', $stage_id);
        return $this->db->get()->row_array();
    }

    public function getCategoryStages() {
        $this->db->select('tbl_category_stages.*');
        $this->db->from('tbl_category_stages');
        $this->db->where('tbl_category_stages.type_use', 0);
        // $this->db->where('tbl_category_stages.type_productionlist_id >', 0);
        return $this->db->get()->result_array();
    }

    public function getMachines($category_stage_id = 0) {
        $this->db->select('
            tbl_machines.id as id,
            tbl_machines.code as code,
            tbl_machines.name as name,
            tbl_machines.product_in_month as product_in_month,
            tbl_machines.preparation_time as preparation_time,
        ', false);
        $this->db->from('tbl_machines');
        if (!empty($category_stage_id)) {
            $this->db->where(' exists (
                SELECT 1
                FROM tbl_machines_stage
                WHERE tbl_machines_stage.category_stage_id = '.$category_stage_id.' AND tbl_machines.id = tbl_machines_stage.machines_id
            )', false, false);
        }
        return $this->db->get()->result_array();
    }

    public function getMachinesById($id) {
        $this->db->select('
            tbl_machines.*
        ', false);
        $this->db->from('tbl_machines');
        $this->db->where('tbl_machines.id', $id);
        return $this->db->get()->row_array();
    }

    public function submitModerationPlan($option) {
        if (!empty($option['po_id']) && !empty($option['item_id']) && !empty($option['type_productionlist_id']) && !empty($option['stage_id'])) {
            $moderationPlan = $this->getModerationPlan($option['po_id'], $option['item_id'], $option['type_productionlist_id'], $option['stage_id']);
            if (!empty($moderationPlan)) {
                $result = $this->updateModerationPlan($option['po_id'], $option['item_id'], $option['type_productionlist_id'], $option['stage_id'], $option);
            } else {
                $result = $this->insertModerationPlan($option);
            }

            if (!empty($option['ngay_bat_dau_thuc_te']) || !empty($option['ngay_ket_thuc_thuc_te'])) {
                $moderationPlan = $this->getModerationPlan($option['po_id'], $option['item_id'], $option['type_productionlist_id'], $option['stage_id']);
                $ngay_bat_dau_thuc_te = $moderationPlan['ngay_bat_dau_thuc_te'];
                $ngay_ket_thuc_thuc_te = $moderationPlan['ngay_ket_thuc_thuc_te'];
                $so_gio_thuc_te = 0;
                if (!empty($ngay_bat_dau_thuc_te) && !empty($ngay_ket_thuc_thuc_te)) {
                    $time1 = strtotime($ngay_bat_dau_thuc_te);
                    $time2 = strtotime($ngay_ket_thuc_thuc_te);
                    $hours = ($time2 - $time1) / 3600;
                    $hours = number_format($hours, 3);
                    $so_gio_thuc_te = $hours;
                    $optionsUp['so_gio_thuc_te'] = $so_gio_thuc_te;
                    $result = $this->updateModerationPlan($option['po_id'], $option['item_id'], $option['type_productionlist_id'], $option['stage_id'], $optionsUp);
                }

            }
        } else {
            $result = false;
        }
        return $result;
    }

    public function getBOMZinc($plan_id) {
        $this->db->select('
            SUM(tbl_productions_plan_compensation.quantity_compensation) as quantity_compensation
        ', false);
        $this->db->from('tbl_productions_plan_compensation');
        $this->db->where('tbl_productions_plan_compensation.is_zinc', 1);
        $this->db->where('tbl_productions_plan_compensation.productions_plan_id', $plan_id);
        return $this->db->get()->row_array();
    }

    public function rowProductionListsTotalDateEnd($date_end, $type_productionlist_id, $before = false) {
        $data = [];
        $this->db->select('tbl_production_lists_total.*', false);
        $this->db->from('tbl_production_lists_total');
        $this->db->where('tbl_production_lists_total.date_end', $date_end);
        $this->db->where('tbl_production_lists_total.type_productionlist_id', $type_productionlist_id);
        $this->db->limit(1);
        $production_lists_total = $this->db->get()->row_array();

        $result = 0;
        if (empty($production_lists_total) && $before) {
            $this->db->select('tbl_production_lists_total.*', false);
            $this->db->from('tbl_production_lists_total');
            $this->db->where('tbl_production_lists_total.date_end <', $date_end);
            $this->db->where('tbl_production_lists_total.type_productionlist_id', $type_productionlist_id);
            $this->db->order_by('tbl_production_lists_total.date_end DESC');
            $this->db->limit(1);
            $production_lists_total = $this->db->get()->row_array();

            $result = 1;
        }

        $data['result'] = $result;
        $data['production_lists_total'] = $production_lists_total;
        return $data;
    }

    public function updateProductionListsTotal($id, $data) {
        $this->db->where('tbl_production_lists_total.id', $id);
        return $this->db->update('tbl_production_lists_total', $data);
    }

    public function deleteProductionListsInDate($date_handling) {
        $this->db->where_in('tbl_production_lists_date.date_handling', $date_handling);
        return $this->db->delete('tbl_production_lists_date');
    }

    public function deleteProductionListsItemsListId($arrId) {
        if (empty($arrId)) return null;
        
        $this->db->where_in('tbl_production_lists_items.id', $arrId);
        return $this->db->delete('tbl_production_lists_items');
    }

    public function getProductionListsItemsMul($arrPoId, $key = false) {
        if (empty($arrPoId)) return null;

        $this->db->select('tbl_production_lists_items.*, tbl_machines.name as name_machine', false);
        $this->db->from('tbl_production_lists_items');
        $this->db->join('tbl_machines', 'tbl_machines.id = tbl_production_lists_items.may_in', 'left');
        $this->db->where_in('tbl_production_lists_items.po_id', $arrPoId);
        // $this->db->where_in('tbl_production_lists_items.item_id', $arrItemId);
        $production_lists_items = $this->db->get()->result_array();
        if ($production_lists_items && $key) {
            $production_lists_items = array_reduce($production_lists_items, function($carry, $item) {
                $po_id = $item['po_id'];
                $item_id = $item['item_id'];
                $face = $item['face'];
                $face_after = $item['face_after'];
                $_key = $item['_key'];
                $stage_id = $item['stage_id'];
                $_index = $po_id.'__'.$item_id.'__'.$face.'__'.$face_after.'__'.$_key.'__'.$stage_id;
                $carry[$_index] = $item;

                return $carry;
            });
        }

        return $production_lists_items;
    }

    public function getTimeCardAlignment() {
        $this->db->select('tbl_time_card_alignment.*', false);
        $this->db->from('tbl_time_card_alignment');
        return $this->db->get()->result_array();
    }
}