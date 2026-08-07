<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Handling_price_model extends App_Model
{
    private $contact_columns;

    public function __construct()
    {
        parent::__construct();
    }

    public function getMaterialPriceQuotes($id)
    {
        $this->db->select('
            tbl_materials.id as id,
            "materials" as type,
            tbl_materials.code as code,
            tbl_materials.name as name,
            tblunits.unit as unit_name,
            tbl_materials.price_sell as price_sell,
            tbl_materials.mode as mode,
            tbl_materials.unit_id as unit_id,
            tbl_materials.is_single_use as is_single_use
        ');
        $this->db->from('tbl_materials');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_materials.unit_id', 'left');
        $this->db->where('tbl_materials.id', $id);
        $materials = $this->db->get()->row_array();
        return $materials;
    }

    public function getItemsStagesPriceQuotes($id)
    {
        $this->db->select('
            tbl_stages.id as id,
            "stages" as type,
            tbl_stages.code as code,
            tbl_stages.name as name,
            "" as unit_name,
            tbl_stages.stage_price_gauge as price_sell,
            "" as mode
        ');
        $this->db->from('tbl_stages');
        $this->db->where('tbl_stages.id', $id);
        $stage = $this->db->get()->row_array();
        return $stage;
    }

    public function getCustomersGroups($customer_id)
    {
        $this->db->select('
            GROUP_CONCAT(tblcustomers_groups.name) as group_name,
            GROUP_CONCAT(tblcustomer_groups.groupid) as group_id,

        ', false);
        $this->db->from('tblcustomer_groups');
        $this->db->join('tblcustomers_groups', 'tblcustomers_groups.id = tblcustomer_groups.groupid');
        $this->db->where('tblcustomer_groups.customer_id', $customer_id);
        return $this->db->get()->row_array();
    }

    public function getMachines()
    {
        $this->db->select('
            tbl_machines.id as id,
            tbl_machines.code as code,
            tbl_machines.name as name,
        ', false);
        $this->db->from('tbl_machines');
        return $this->db->get()->result_array();
    }

    public function rowMachines($id)
    {
        $this->db->select('tbl_machines.*');
        $this->db->from('tbl_machines');
        $this->db->where('tbl_machines.id', $id);
        return $this->db->get()->row_array();
    }

    public function getStagePrice($stageId, $quote_stage_id, $id_customer, $height_layout, $width_layout, $height = null, $width = null)
    {
        $this->db->select('
            tbl_stages.id as id,
            "stages" as type,
            tbl_stages.code as code,
            tbl_stages.name as name,
            0 price_sell,
            0 as is_single_use,
            tbl_stages.formula_m2 as formula_m2
        ');
        $this->db->from('tbl_stages');
        $this->db->where('tbl_stages.id', $stageId);
        $stage = $this->db->get()->row_array();
        // print_arrays($stageId, '<br>', $quote_stage_id, '<br>', $id_customer, '<br>', $height_layout, '<br>', $width_layout);

        $price_sell = 0;
        if (!empty($stage)) {
            $this->db->where('id_stage', $stageId);
            // $this->db->where('EXISTS (SELECT 1 FROM tbl_stage_quote_client WHERE tbl_stage_quote_client.id_stage_quote = tbl_stage_quote_detail.id_stage_quote AND tbl_stage_quote_client.id_client = "' . $id_customer . '")');
            $this->db->where('id_stage_quote', $quote_stage_id);

            // Code cũ không lọc theo dài rộng
            // $price_sell = $this->db->get('tbl_stage_quote_detail')->row('price');

            // Code mới để lấy theo dài rộng (height, width)
            // if ($height !== null) {
            //     $this->db->where('height', $height);
            // }
            // if ($width !== null) {
            //     $this->db->where('width', $width);
            // }
            $price_sell = $this->db->get('tbl_stage_quote_detail')->row('price');
            $price_sell = !empty($price_sell) ? $price_sell : 0;

            if ($stage['formula_m2']) {
                $price_sell = ($price_sell * $height_layout * $width_layout) / 10000;
            }
        }
        return $price_sell;
    }
}
