<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Production_order_request_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function submit($formData, $id = null)
    {
        if(empty($formData['item_id'])) {
            $response['submitId'] =  null;
            $response['message'] =  'Lưu thất bại. Sản phẩm khổng thể trống';
            return $response;
        }

        $arrField = [
            'code',
            'date_create',
            'date',
            'note',
            'create_by',
        ];
        $formData['date'] = (!empty($formData['date']) ? to_sql_date($formData['date'], true) : null);
        if (empty($id)) { //insert
            $formData['code'] = getReference('production_order_request');
            // $formData['code'] = get_option('prefix_spending_plan') . '-' . sprintf('%06d', ch_getMaxID('id', 'tblspending_plan') + 1);
            $formData['create_by'] = get_staff_user_id();
        } else { //update
            unset($arrField['code']);
            unset($arrField['create_by']);
        }

        $submitData = [];
        foreach ($arrField as $field) {
            if (isset($formData[$field])) {
                $submitData[$field] = $formData[$field];
            }
        }

        if (empty($id)) { // insert
            $this->db->insert('tblproduction_order_request', $submitData);
            $submitId = $this->db->insert_id();

            if (getReference('production_order_request') == $formData['code']) {
                updateReference('production_order_request');
            }
        } else { //update
            if ($this->db->update('tblproduction_order_request', $submitData, ['id' => $id])) {
                $submitId = $id;
            } else {
                $submitId = false;
            }
        }

        if (!empty($submitId)) {
            // Thêm bảng chi tiết
            $submitItemData = [];
            $arrItemId = $formData['item_row_id'] ?? [];
            $arrItemBrand = $formData['brand_ids'] ?? [];
            $arrItemLink = $formData['link'] ?? [];
            $arrItems = $formData['item_id'];
            $arrTimeNorm = $formData['time_norm'];
            $ignoreDeleteId = [];
            foreach($arrItems as $itemIndex => $itemValue) {
                $item = explode('__', $itemValue);
                $itemId = $item[0];
                $itemType = $item[1];
                $submitItemData = [
                    'id' => $arrItemId[$itemIndex] ?? null,
                    'production_order_request_id' => $submitId,
                    'item_type' => $itemType,
                    'item_id' => $itemId,
                    'brand_ids' => (!empty($arrItemBrand[$itemIndex]) ? implode(',', $arrItemBrand[$itemIndex]) : null),
                    'time_norm' => $arrTimeNorm[$itemIndex] ?? null,
                ];

                if (empty($submitItemData['id'])) { // insert
                    unset($submitItemData['id']);
                    $this->db->insert('tblproduction_order_request_item', $submitItemData);
                    $insertedId = $this->db->insert_id();
                    $ignoreDeleteId[] = $insertedId;
                } else { // update
                    $itemRowId = $submitItemData['id'];
                    unset($submitItemData['id']);
                    if ($this->db->update('tblproduction_order_request_item', $submitItemData, ['id' => $itemRowId])) {
                        $ignoreDeleteId[] = $itemRowId;
                    }
                }
            }

            // Xóa bảng chi tiết
            $this->db->where('production_order_request_id', $submitId);
            $this->db->where_not_in('id', $ignoreDeleteId);
            $this->db->delete('tblproduction_order_request_item');
        }

        $response['submitId'] =  $submitId;
        $response['message'] =  (($submitId) ? 'Thành công' : 'Thất bại');
        return $response;
    }

    public function get($id) {
        $result = get_table_where('tblproduction_order_request', ['id' => $id], '', 'row_array');
        $result['items'] = $this->getItems($id);
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
        $this->db->delete('tblproduction_order_request', ['id' => $id]);
        $this->db->delete('tblproduction_order_request_item', ['production_order_request_id' => $id]);

        $result['result'] = 1;
        $result['message'] = 'Xóa thành công';
        return $result;
    }
}