<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Work_norm_group_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function submit($formData, $id = null)
    {
        if(empty($formData['code'])) {
            $response['submitId'] =  null;
            $response['message'] =  'Mã không thể trống';
            return $response;
        }

        $arrField = [
            'code',
            'name',
            'task',
            'productivity_hour',
            'formula',
            'norm',
            'unit_id',
            'number_execution',
        ];

        $submitData = [];
        foreach ($arrField as $field) {
            if (isset($formData[$field])) {
                $submitData[$field] = $formData[$field];
            }
        }

        $submitId = null;
        if (empty($id)) { // insert
            $this->db->insert('tbl_work_norm_group', $submitData);
            $submitId = $this->db->insert_id();

        } else { //update
            if ($this->db->update('tbl_work_norm_group', $submitData, ['id' => $id])) {
                $submitId = $id;
            } else {
                $submitId = null;
            }
        }

        // if (!empty($submitId)) {
        // }

        $response['submitId'] =  $submitId;
        $response['message'] =  (($submitId) ? 'Thành công' : 'Thất bại');
        return $response;
    }

    public function getByCode($code) {
        $result = get_table_where('tbl_work_norm_group', ['code' => $code], '', 'row_array');
        return $result;
    }

    public function getItems($id) {
        $tb_brand = '(
            SELECT
                tbl_brand.name as name,
                tbl_brand.id as id
            FROM tbl_brand
            GROUP BY tbl_brand.id
        ) as tb_brand';
        $this->db->select('
            tblproduction_order_request_item.*,
            IF(tbl_products.images IS NOT NULL && tbl_products.images != "", CONCAT("uploads/products/", "", tbl_products.images, ""), "") as image,
            tbl_products.code as item_code,
            tbl_products.name as item_name,
            tbl_products.height as height,
            tbl_products.wide as wide,
            tbl_products.packing as packing,
            tbl_products.quantity_max as quantity_max,
            tbl_products.time_inventory as time_inventory,
            tbl_products.quota_time_change_one as quota_time_change_one,
            GROUP_CONCAT(tb_brand.name SEPARATOR ", ") as brand_name,
            tbl_species.name as specie_name,
            tbl_category_products.name as category_name,
            tb_unit_measure.unit as unit_measure,
            tblunits.unit as unit_name,
        ');
        $this->db->from('tblproduction_order_request_item');
        $this->db->join('tbl_products', 'tbl_products.id = tblproduction_order_request_item.item_id AND tblproduction_order_request_item.item_type = "products"', 'left');
        $this->db->join($tb_brand, 'FIND_IN_SET(tb_brand.id, tblproduction_order_request_item.brand_ids) > 0', 'left');
        $this->db->join('tbl_species', 'tbl_species.id = tbl_products.species', 'left');
        $this->db->join('tbl_category_products', 'tbl_category_products.id = tbl_products.category_id', 'left');
        $this->db->join('tblunits', 'tbl_products.unit_id = tblunits.unitid', 'left');
        $this->db->join('tblunits tb_unit_measure', 'tbl_products.unit_measure = tb_unit_measure.unitid', 'left');
        $this->db->where('tblproduction_order_request_item.production_order_request_id', $id);
        $this->db->group_by('tblproduction_order_request_item.id');
        $result = $this->db->get()->result_array();
        foreach($result as $key => $value) {
            $result[$key]['brand_ids'] = explode(',', $value['brand_ids']);
        }
        
        return $result;
    }

    public function delete($id) {
        $this->db->delete('tbl_work_norm_group', ['id' => $id]);

        $result['isSuccess'] = true;
        $result['message'] = 'Xóa thành công';
        return $result;
    }
}