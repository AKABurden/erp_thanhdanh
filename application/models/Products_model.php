<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Products_model extends App_Model
{
    private $contact_columns;

    public function __construct()
    {
        parent::__construct();

        // $this->contact_columns = hooks()->apply_filters('contact_columns', ['firstname', 'lastname', 'email', 'phonenumber', 'title', 'password', 'send_set_password_email', 'donotsendwelcomeemail', 'permissions', 'direction', 'invoice_emails', 'estimate_emails', 'credit_note_emails', 'contract_emails', 'task_emails', 'project_emails', 'ticket_emails', 'is_primary']);

        // $this->load->model(['client_vault_entries_model', 'client_groups_model', 'statement_model']);
    }

    public function insertCategoryProducts($data)
    {
        $this->db->insert('_category_products', $data);
        return $this->db->insert_id();
    }

    public function rowCategoryProducts($id)
    {
        $this->db->select('*');
        $this->db->from('_category_products');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function updateCategoryProducts($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('_category_products', $data);
    }

    public function updateItems($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tblitems', $data);
    }

    public function deleteCategoryProducts($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('_category_products');
    }

    public function searchCategory($q, $limit = 50)
    {
        $this->db->select('tbl_category_products.id as id, tbl_category_products.name as name', false);
        $this->db->from('tbl_category_products');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('tbl_category_products.code', $q);
            $this->db->or_like('tbl_category_products.name', $q);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function insertProducts($data)
    {
        $this->db->insert('tbl_products', $data);
        return $this->db->insert_id();
    }

    public function updateProducts($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_products', $data);
    }

    public function deleteProducts($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_products');
    }

    public function rowProduct($id)
    {
        $this->db->select('tbl_products.*, tbl_category_products.name as category_name, tbl_category_products.code as category_code,tbl_species.name as species_name', false);
        $this->db->from('tbl_products');
        $this->db->join('tbl_category_products', 'tbl_category_products.id = tbl_products.category_id', 'left');
        $this->db->join('tbl_species', 'tbl_species.id = tbl_products.species', 'left');
        $this->db->where('tbl_products.id', $id);
        return $this->db->get()->row_array();
    }

    public function getColorsByProductId($product_id)
    {
        $this->db->select('tbl_colors.id, tbl_colors.name as color_name');
        $this->db->from('tbl_products_colors');
        $this->db->join('tbl_colors', 'tbl_colors.id = tbl_products_colors.color_id');
        $this->db->where('tbl_products_colors.product_id', $product_id);
        return $this->db->get()->result_array();
    }

    public function checkExistCategory($id)
    {
        $this->db->from('tbl_products');
        $this->db->where('tbl_products.category_id', $id);
        $this->db->limit(1);
        return $this->db->get()->num_rows();
    }

    //colors
    public function insertColors($data)
    {
        $this->db->insert('tbl_colors', $data);
        return $this->db->insert_id();
    }

    public function rowColors($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_colors');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function updateColors($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_colors', $data);
    }

    public function deleteColors($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_colors');
    }

    public function searchColors($q, $limit = 50)
    {
        $this->db->select('tbl_colors.id as id, tbl_colors.name as name', false);
        $this->db->from('tbl_colors');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('tbl_colors.code', $q);
            $this->db->or_like('tbl_colors.name', $q);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function checkExistColors($id)
    {
        $this->db->from('tbl_products_colors');
        $this->db->where('color_id', $id);
        $this->db->limit(1);
        return $this->db->get()->num_rows();
    }
    //end colors

    //product color
    function insertBatchProductsColors($data)
    {
        return $this->db->insert_batch('tbl_products_colors', $data);
    }

    function deleteProductsColorsByProductId($product_id)
    {
        $this->db->where('product_id', $product_id);
        return $this->db->delete('tbl_products_colors');
    }

    public function searchSemiProducts($q, $limit = 50)
    {
        $this->db->select('tbl_products.id as id, tbl_products.name as name', false);
        $this->db->from('tbl_products');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('tbl_products.code', $q);
            $this->db->or_like('tbl_products.name', $q);
            $this->db->group_end();
        }
        $this->db->where('type_products', 'semi_products');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function searchSelect2SemiProducts($q, $limit = 50, $type = "semi_products", $params = [])
    {
        $this->db->select('tbl_products.id as id, CONCAT(tbl_products.name, "(", tbl_products.code, ")") as text', false);
        $this->db->from('tbl_products');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('tbl_products.code', $q);
            $this->db->or_like('tbl_products.name', $q);
            $this->db->group_end();
        }

        if (!empty($params)) {
            $category = !empty($params['category_id_search']) ? $params['category_id_search'] : 0;
            if (!empty($category)) {
                $this->db->where('tbl_products.category_id', $category);
            }
        }

        $this->db->where('type_products', $type);
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function searchProducts($q, $limit = 50)
    {
        $this->db->select('tbl_products.id as id, tbl_products.code as name, tbl_products.name as product_name, IF(tbl_products.images IS NOT NULL && tbl_products.images != "", CONCAT("uploads/products/", "", tbl_products.images, ""), "") as images, tblunits.unit as unit_name_manu', false);
        $this->db->from('tbl_products');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_products.conversion_unit', 'left');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('tbl_products.code', $q);
            $this->db->or_like('tbl_products.name', $q);
            $this->db->group_end();
        }
        // $this->db->where('type_products', 'semi_products');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function searchProductsSelect2($q, $limit = 50)
    {
        $this->db->select('
            CONCAT(tbl_products.id, "__products") as id, 
            CONCAT(tbl_products.code) as text, 
            tbl_products.name as item_name, 
            IF(tbl_products.images IS NOT NULL && tbl_products.images != "", CONCAT("uploads/products/", "", tbl_products.images, ""), "") as images, 
            tblunits.unit as unit_name, 
            tbl_products.price_sell as price_sell, 
            tbl_products.info as info, 
            tbl_products.code as item_code, 
            CONCAT(tbl_products.category_id, "__products") as category_id,
            tblsize.name as size_name,
            tbl_products.loss as loss,
            tbl_products.quantity_child_sheet as quantity_child_sheet,
            tbl_products.quantity_sheet_bale as quantity_sheet_bale,
            tbl_products.mode_product as mode_product,
            tbl_products.product_name_customer as name_customer,
            tbl_products.height as height,
            tbl_products.wide as wide,
            tbl_products.wide as wide,
            tbl_products.packing as packing,
            tbl_products.quantity_max as quantity_max,
            tbl_products.time_inventory as time_inventory,
            tbl_products.quota_time_change_one as quota_time_change_one,
            unit_stock.unit as unit_stock,
            tbl_species.name as specie_name,
            tbl_category_products.name as category_name,
            tb_unit_measure.unit as unit_measure,
            tbl_brand.code as brand_code,
        ', false);
        $this->db->from('tbl_products');
        $this->db->join('tblunits', 'tbl_products.unit_id = tblunits.unitid', 'left');
        $this->db->join('tblunits unit_stock', 'tbl_products.conversion_unit = unit_stock.unitid', 'left');
        $this->db->join('tblunits tb_unit_measure', 'tbl_products.unit_measure = tb_unit_measure.unitid', 'left');
        $this->db->join('tblsize', 'tblsize.id = tbl_products.size', 'left');
        $this->db->join('tbl_species', 'tbl_species.id = tbl_products.species', 'left');
        $this->db->join('tbl_category_products', 'tbl_category_products.id = tbl_products.category_id', 'left');
        $this->db->join('tbl_brand', 'tbl_brand.id = tbl_products.brand_id', 'left');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('tbl_products.code', $q);
            $this->db->or_like('tbl_products.name', $q);
            $this->db->group_end();
        }
        $this->db->where('type_products !=', 'semi_products_outside');
        $this->db->where('tbl_products.status', 1);
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function searchItemsSelect2($q, $limit = 50)
    {
        $this->db->select('CONCAT(tblitems.id, "__items") as id, CONCAT(tblitems.code, "(", tblitems.name, ")") as text, tblitems.name as item_name, tblitems.avatar as images, tblunits.unit as unit_name, tblitems.price as price_sell, tblitems.info as info, CONCAT(tblitems.category_id, "__items") as category_id', false);
        $this->db->from('tblitems');
        $this->db->join('tblunits', 'tblitems.unit = tblunits.unitid', 'left');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('tblitems.code', $q);
            $this->db->or_like('tblitems.name', $q);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function checkProductVersions($product_id, $versions)
    {
        $this->db->from('tbl_product_versions');
        $this->db->where('tbl_product_versions.product_id', $product_id);
        $this->db->where('tbl_product_versions.versions', $versions);
        return $this->db->get()->num_rows();
    }

    public function deleteProductVersionsByProductIdAndVersion($product_id, $versions)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('versions', $versions);
        return $this->db->delete('tbl_product_versions');
    }

    public function getProductVersionsByProductId($product_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_product_versions');
        $this->db->where('tbl_product_versions.product_id', $product_id);
        $this->db->order_by('tbl_product_versions.id', 'DESC');
        return $this->db->get()->result_array();
    }

    public function getProductVersionsById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_product_versions');
        $this->db->where('tbl_product_versions.id', $id);
        return $this->db->get()->row_array();
    }

    public function getVersionsElementByVersionId($version_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_versions_element');
        $this->db->where('version_id', $version_id);
        return $this->db->get()->result_array();
    }

    public function getElementItemsByElementId($element_id)
    {
        $this->db->select('tbl_element_items.*, tblunits.unit');
        $this->db->from('tbl_element_items');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_element_items.unit_id', 'left');
        $this->db->where('element_id', $element_id);
        return $this->db->get()->result_array();
    }

    public function deleteProductsVersionsByProductId($product_id)
    {
        $versions = $this->getProductVersionsByProductId($product_id);
        $this->db->where('tbl_product_versions.product_id', $product_id);
        if ($this->db->delete('tbl_product_versions')) {
            foreach ($versions as $key => $value) {
                $vs_elements = $this->getVersionsElementByVersionId($value['id']);
                $this->db->where('tbl_versions_element.version_id', $value['id']);
                if ($this->db->delete('tbl_versions_element')) {
                    foreach ($vs_elements as $k => $val) {
                        $elementItems = $this->getElementItemsByElementId($val['id']);
                        $this->db->where('tbl_element_items.element_id', $val['id']);
                        $this->db->delete('tbl_element_items');

                        foreach ($elementItems as $v) {
                            $this->db->where('tbl_element_items_replace.element_item_id', $v['id']);
                            $this->db->delete('tbl_element_items_replace');
                        }
                    }
                }
            }
            return true;
        } else {
            return false;
        }
    }

    public function getElementItemsReplaceByElementItemId($element_item_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_element_items_replace');
        $this->db->where('tbl_element_items_replace.element_item_id', $element_item_id);
        return $this->db->get()->result_array();
    }

    public function deleteBOOMById($id)
    {
        $version = $this->getProductVersionsById($id);
        $this->db->where('tbl_product_versions.id', $id);
        if ($this->db->delete('tbl_product_versions')) {
            $vs_elements = $this->getVersionsElementByVersionId($id);
            $this->db->where('tbl_versions_element.version_id', $id);
            if ($this->db->delete('tbl_versions_element')) {
                foreach ($vs_elements as $k => $val) {
                    $elementItems = $this->getElementItemsByElementId($val['id']);
                    $this->db->where('tbl_element_items.element_id', $val['id']);
                    $this->db->delete('tbl_element_items');

                    foreach ($elementItems as $v) {
                        $this->db->where('tbl_element_items_replace.element_item_id', $v['id']);
                        $this->db->delete('tbl_element_items_replace');
                    }
                }
            }
            return true;
        } else {
            return false;
        }
    }

    public function insertBOM($data, $status = "unapplication", $bom_id = 0, $actions = 'add')
    {
        if (!empty($data)) {
            // print_arrays($actions);
            // print_arrays($data['element']);
            // if (!empty($bom_id)) {
            //     $this->deleteBOOMById($bom_id);
            // }
            if ($actions == "add") {
                $version_id = $this->db->insert('tbl_product_versions', [
                    'versions' => $data['versions'],
                    'product_id' => $data['product_id'],
                    'date_start' => !empty($data['date_start']) ? $data['date_start'] : null,
                    'date_end' => !empty($data['date_end']) ? $data['date_end'] : null,
                    'date_created' => !empty($data['date_created']) ? $data['date_created'] : date('Y-m-d H:i:s'),
                    'created_by' => !empty($data['created_by']) ? $data['created_by'] : get_staff_user_id(),
                ]);
                $version_id = $this->db->insert_id();
            } else if ($actions == "edit") {
                $bom = $this->products_model->getProductVersionsById($bom_id);
                if (!empty($bom_id)) {
                    $this->deleteBOOMById($bom_id);
                }
                $version_id = $this->db->insert('tbl_product_versions', [
                    'versions' => $data['versions'],
                    'product_id' => $data['product_id'],
                    'date_start' => !empty($data['date_start']) ? $data['date_start'] : null,
                    'date_end' => !empty($data['date_end']) ? $data['date_end'] : null,
                    'date_created' => !empty($bom['date_created']) ? $bom['date_created'] : date('Y-m-d H:i:s'),
                    'created_by' => !empty($bom['created_by']) ? $bom['created_by'] : get_staff_user_id(),
                    'date_updated' => date('Y-m-d H:i:s'),
                    'updated_by' => get_staff_user_id(),
                ]);
                $version_id = $this->db->insert_id();
            }
            if (!empty($version_id)) {
                $element = $data['element'];
                if (!empty($element)) {
                    foreach ($element as $k => $val) {
                        $el_id = $this->db->insert('tbl_versions_element', [
                            'version_id' => $version_id,
                            'element_name' => $val['element_name'],
                            'quantity' => $val['element_number'],
                            'type_element' => !empty($val['type_element']) ? $val['type_element'] : 1,
                        ]);
                        $el_id = $this->db->insert_id();
                        if ($el_id) {
                            if (!empty($val['items'])) {
                                $items = $val['items'];
                                foreach ($items as $v) {
                                    $replace = false;
                                    if (!empty($v['replace'])) {
                                        $replace = $v['replace'];
                                    }
                                    $this->db->insert('tbl_element_items', [
                                        'element_id' => $el_id,
                                        'type' => $v['type'],
                                        'item_id' => $v['item_id'],
                                        'unit_id' => $v['unit_id'],
                                        'quantity' => $v['element_item_number'],
                                        'quantity_compensation' => !empty($v['quantity_compensation']) ? $v['quantity_compensation'] : 0,
                                        'leadtime' => $v['leadtime'],
                                        'stage_id' => $v['stage'],
                                        'machines_id' => !empty($v['machines_id']) ? $v['machines_id'] : 0,
                                        'type_element_item' => !empty($v['type_element_item']) ? $v['type_element_item'] : 1,
                                        'landscape_print_size' => !empty($v['landscape_print_size']) ? $v['landscape_print_size'] : 0,
                                        'vertical_print_size' => !empty($v['vertical_print_size']) ? $v['vertical_print_size'] : 0,
                                        'number_children_size' => !empty($v['number_children_size']) ? $v['number_children_size'] : 0,
                                        'paper_exchange' => !empty($v['paper_exchange']) ? $v['paper_exchange'] : 0,
                                        'hand_input_paper_exchange' => !empty($v['hand_input_paper_exchange']) ? $v['hand_input_paper_exchange'] : 0,
                                        'face' => !empty($v['face']) ? $v['face'] : 0,
                                        'face_after' => !empty($v['face_after']) ? $v['face_after'] : 0,
                                    ]);
                                    $element_item_id = $this->db->insert_id();

                                    //handling insert replace
                                    if (!empty($element_item_id) && !empty($replace)) {
                                        foreach ($replace as $r) {
                                            $this->db->insert('tbl_element_items_replace', [
                                                'element_item_id' => $element_item_id,
                                                'type_replace' => $r['type_replace'],
                                                'item_id_replace' => $r['item_id_replace'],
                                                'unit_id_replace' => $r['unit_id_replace'],
                                                'quantity_replace' => $r['element_item_number_replace'],
                                                'leadtime_replace' => $r['leadtime_replace'],
                                                'stage_id_replace' => $r['stage_replace'],
                                            ]);
                                        }
                                    }
                                    //
                                }
                            }
                        }
                    }
                }
            }
            if ($version_id) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    public function insertStages($data)
    {
        $this->db->insert('tbl_stages', $data);
        return $this->db->insert_id();
    }

    public function rowStages($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_stages');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function updateStages($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_stages', $data);
    }

    public function deleteStages($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_stages');
    }

    public function searchStages($q, $limit = 50)
    {
        $this->db->select('tbl_stages.id as id, tbl_stages.name as name', false);
        $this->db->from('tbl_stages');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('tbl_stages.code', $q);
            $this->db->or_like('tbl_stages.name', $q);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function checkProductStages($product_id, $versions)
    {
        $this->db->from('tbl_product_stages');
        $this->db->where('tbl_product_stages.product_id', $product_id);
        $this->db->where('tbl_product_stages.versions', $versions);
        return $this->db->get()->num_rows();
    }

    public function insertProductStages($data, $status = "unapplication", $vs_stage_id = 0)
    {
        if (!empty($vs_stage_id)) {
            $this->deleteProductStagesById($vs_stage_id);
        }
        // print_arrays($data['items']);
        $this->db->insert('tbl_product_stages', [
            'product_id' => $data['product_id'],
            'versions' => $data['versions'],
            'status' => $status,
        ]);
        $version_id = $this->db->insert_id();
        if ($version_id) {
            $items = $data['items'];
            if (!empty($items)) {
                foreach ($items as $key => $value) {
                    $this->db->insert('tbl_product_stages_versions', [
                        'version_id' => $version_id,
                        'stage_id' => $value['stage'],
                        'machines' => $value['machines'],
                        'number' => $value['number'],
                        'number_hours' => $value['number_hours'],
                        'final_stage' => $value['final_stage'],
                        'type' => !empty($value['type']) ? $value['type'] : 0,
                        'face' => !empty($value['face']) ? $value['face'] : 0,
                        'face_after' => !empty($value['face_after']) ? $value['face_after'] : 0,
                        'number_face' => !empty($value['number_face']) ? $value['number_face'] : 0,
                        'number_operations' => !empty($value['number_operations']) ? $value['number_operations'] : 0,
                        'number_cutting' => !empty($value['number_cutting']) ? $value['number_cutting'] : 0,
                        'quota_time_f1' => !empty($value['quota_time_f1']) ? $value['quota_time_f1'] : 0,
                        'quota_time_f2' => !empty($value['quota_time_f2']) ? $value['quota_time_f2'] : 0,
                    ]);
                }
            }
            return true;
        }
        return false;
    }

    public function getProductStagesByProductId($product_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_product_stages');
        $this->db->where('tbl_product_stages.product_id', $product_id);
        $this->db->order_by('tbl_product_stages.id DESC');
        return $this->db->get()->result_array();
    }

    public function rowProductStagesById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_product_stages');
        $this->db->where('tbl_product_stages.id', $id);
        return $this->db->get()->row_array();
    }

    public function getProductStagesVersions($version_id)
    {
        $this->db->select('tbl_product_stages_versions.*, tbl_stages.name as stage_name, tbl_stages.code as stage_code, tbl_machines.code as machine_code');
        $this->db->from('tbl_product_stages_versions');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_product_stages_versions.stage_id', 'left');
        $this->db->join('tbl_machines', 'tbl_machines.id = tbl_product_stages_versions.machines', 'left');
        $this->db->where('tbl_product_stages_versions.version_id', $version_id);
        $this->db->order_by('tbl_product_stages_versions.number', 'ASC');
        return $this->db->get()->result_array();
    }

    public function deleteProductStages($product_id)
    {
        $product_stages = $this->getProductStagesByProductId($product_id);
        $this->db->where('tbl_product_stages.product_id', $product_id);
        if ($this->db->delete('tbl_product_stages')) {
            foreach ($product_stages as $key => $value) {
                $this->db->where('tbl_product_stages_versions.version_id', $value['id']);
                $this->db->delete('tbl_product_stages_versions');
            }
            return true;
        }
        return false;
    }

    public function deleteProductStagesById($id)
    {
        $this->db->where('tbl_product_stages.id', $id);
        if ($this->db->delete('tbl_product_stages')) {
            $this->db->where('tbl_product_stages_versions.version_id', $id);
            $this->db->delete('tbl_product_stages_versions');
            return true;
        }
        return false;
    }

    public function getBomByProductIdAndVersions($product_id, $versions)
    {
        $this->db->select('*');
        $this->db->from('tbl_product_versions');
        $this->db->where('tbl_product_versions.versions', $versions);
        $this->db->where('tbl_product_versions.product_id', $product_id);
        return $this->db->get()->row_array();
    }

    public function checkCategoryProductsByCode($code)
    {
        $this->db->from('tbl_category_products');
        $this->db->where('tbl_category_products.code', $code);
        $this->db->limit(1);
        return $this->db->get()->num_rows();
    }

    public function checkProductsByCode($code)
    {
        $this->db->from('tbl_products');
        $this->db->where('tbl_products.code', $code);
        $this->db->limit(1);
        return $this->db->get()->num_rows();
    }

    public function rowCategoryProductsByCode($code, $select, $option)
    {
        $this->db->select($select);
        $this->db->from('tbl_category_products');
        if ($option == "like") {
            $this->db->like('tbl_category_products.code', $code);
        } else if ($option == "where") {
            $this->db->where('tbl_category_products.code', $code);
        }
        return $this->db->get()->row_array();
    }

    public function rowColorByCode($code, $select, $option)
    {
        $this->db->select($select);
        $this->db->from('tbl_colors');
        if ($option == "like") {
            $this->db->like('tbl_colors.code', $code);
        } else if ($option == "where") {
            $this->db->where('tbl_colors.code', $code);
        }
        return $this->db->get()->row_array();
    }

    public function rowSizeByCode($code, $select, $option)
    {
        $this->db->select($select);
        $this->db->from('tblsize');
        if ($option == "like") {
            $this->db->like('tblsize.name', $code);
        } else if ($option == "where") {
            $this->db->where('tblsize.name', $code);
        }
        return $this->db->get()->row_array();
    }

    // public function getBomForProductionsCapacity($product_id, $versions)
    // {
    //     $this->db->select('');
    //     $this->db->from('tbl_product_versions');
    //     $this->db->join('tbl_versions_element', 'tbl_versions_element.version_id = tbl_product_versions.id');
    //     $this->db->join('tbl_versions_element', 'tbl_versions_element.version_id = tbl_product_versions.id');
    //     $this->db->where('tbl_product_versions.product_id', $product_id);
    //     $this->db->where('tbl_product_versions.versions', $product_id);
    // }

    public function checkStagesByParentId($id)
    {
        $this->db->from('tbl_stages');
        $this->db->where('tbl_stages.parent_id', $id);
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if (!empty($q)) {
            return $q;
        }
        return false;
    }

    public function checkStagesExist($id)
    {
        $this->db->from('tbl_product_stages_versions');
        $this->db->where('tbl_product_stages_versions.stage_id', $id);
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if ($q > 0) {
            return true;
        }

        $this->db->from('tbl_productions_orders_items_stages');
        $this->db->where('tbl_productions_orders_items_stages.stage_id', $id);
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if ($q > 0) {
            return true;
        }
        return false;
    }

    public function getProductStagesByProductIdAndVersions($product_id, $versions)
    {
        $this->db->select('*');
        $this->db->from('tbl_product_stages');
        $this->db->where('tbl_product_stages.product_id', $product_id);
        $this->db->where('tbl_product_stages.versions', $versions);
        return $this->db->get()->row_array();
    }

    public function checkExistProducts($id)
    {
        $this->db->from('tbl_element_items');
        $this->db->where('tbl_element_items.item_id', $id);
        $this->db->where('tbl_element_items.type', 'semi_products');
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if ($q) {
            return $q;
        }

        //bom
        $this->db->from('tbl_boms_element_items');
        $this->db->where('tbl_boms_element_items.type !=', 'materials');
        $this->db->where('tbl_boms_element_items.item_id', $id);
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if ($q) {
            return $q;
        }
        //bom product
        $this->db->from('tbl_element_items');
        $this->db->where('tbl_element_items.type !=', 'materials');
        $this->db->where('tbl_element_items.item_id', $id);
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if ($q) {
            return $q;
        }

        //quotes 01419f
        $this->db->from('tblmodules');
        $this->db->where('module_name', 'quotes');
        $this->db->where('active', 1);
        $result = $this->db->get()->num_rows();
        if ($result) {
            $this->db->from('tbl_quote_items');
            $this->db->where('tbl_quote_items.item_id', $id);
            $this->db->where('tbl_quote_items.type_item', 'products');
            $this->db->limit(1);
            $q = $this->db->get()->num_rows();
            if ($q) {
                return $q;
            }
        } else {
            //quotes
            $this->db->from('tblquotes_orders_items');
            $this->db->where('tblquotes_orders_items.id_product', $id);
            $this->db->where('tblquotes_orders_items.type_items', 'products');
            $this->db->limit(1);
            $q = $this->db->get()->num_rows();
            if ($q) {
                return $q;
            }
        }

        //orders
        $this->db->from('tblorders_items');
        $this->db->where('tblorders_items.id_product', $id);
        $this->db->where('tblorders_items.type_items', 'products');
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if ($q) {
            return $q;
        }

        //orders
        $this->db->from('tbl_order_items');
        $this->db->where('tbl_order_items.item_id', $id);
        $this->db->where('tbl_order_items.type_item', 'products');
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if ($q) {
            return $q;
        }

        //business plan
        $this->db->from('tbl_business_plan_items');
        $this->db->where('tbl_business_plan_items.items_id', $id);
        $this->db->where('tbl_business_plan_items.type_items', 'products');
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if ($q) {
            return $q;
        }

        //purchase
        $this->db->from('tblpurchases_items');
        $this->db->where('tblpurchases_items.product_id', $id);
        $this->db->where('tblpurchases_items.type', 'product');
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if ($q) {
            return $q;
        }

        //purchase order
        $this->db->from('tblpurchase_order_items');
        $this->db->where('tblpurchase_order_items.product_id', $id);
        $this->db->where('tblpurchase_order_items.type', 'product');
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if ($q) {
            return $q;
        }

        //import_items
        $this->db->from('tblimport_items');
        $this->db->where('tblimport_items.product_id', $id);
        $this->db->where('tblimport_items.type', 'product');
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if ($q) {
            return $q;
        }

        //productions orders items
        $this->db->from('tbl_productions_orders_items');
        $this->db->where('tbl_productions_orders_items.items_id', $id);
        $this->db->where('tbl_productions_orders_items.type_items', 'products');
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if ($q) {
            return $q;
        }

        //productions plan items
        $this->db->from('tbl_productions_plan_items');
        $this->db->where('tbl_productions_plan_items.product_id', $id);
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if ($q) {
            return $q;
        }

        $this->db->from('tblpurchases_items');
        $this->db->where('tblpurchases_items.product_id', $id);
        $this->db->where('tblpurchases_items.type', 'product');
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if (!empty($q)) {
            return $q;
        }

        $this->db->from('tblinventory_items');
        $this->db->where('tblinventory_items.product_id', $id);
        $this->db->where('tblinventory_items.type', 'product');
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if ($q) {
            return $q;
        }

        return false;
    }

    public function checkParentId($id)
    {
        $this->db->from('tbl_category_products');
        $this->db->where('tbl_category_products.parent_id', $id);
        return $this->db->get()->num_rows();
    }

    public function getUnitsByArrId($arr_id = [])
    {
        $this->db->select('*');
        $this->db->from('tblunits');
        $this->db->where_in('unitid', $arr_id);
        return $this->db->get()->result_array();
    }

    public function rowExchangeItems($item_id, $unit_id)
    {
        $this->db->select('tbl_exchange_items.item_id as item_id, tbl_exchange_items.unit_id as unit_id, IF (tbl_exchange_items.number_exchange != 0, tbl_exchange_items.number_exchange, 1) as number_exchange');
        $this->db->from('tbl_exchange_items');
        $this->db->where('tbl_exchange_items.item_id', $item_id);
        $this->db->where('tbl_exchange_items.unit_id', $unit_id);
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }

    public function insertCategoryBOM($data, $status = "unapplication", $id = 0, $actions = 'add')
    {
        if (!empty($data)) {
            if ($actions == 'add' || $actions == "copy") {
                $bom_id = $this->db->insert('tbl_boms', [
                    'versions' => $data['versions'],
                    'date_start' => $data['date_start'],
                    'date_end' => $data['date_end'],
                    'date_created' => date('Y-m-d H:i:s'),
                    'created_by' => get_staff_user_id(),
                ]);
                $bom_id = $this->db->insert_id();
            } else if ($actions = 'edit') {
                $this->db->where('id', $id);
                $up = $this->db->update('tbl_boms', [
                    'versions' => $data['versions'],
                    'date_start' => $data['date_start'],
                    'date_end' => $data['date_end'],
                    'date_updated' => date('Y-m-d H:i:s'),
                    'updated_by' => get_staff_user_id(),
                ]);
                if ($up) {
                    $bom_id = $id;
                    $bom_elements = $this->getBomsElementByBomId($id);
                    $this->db->where('tbl_boms_element.bom_id', $id);
                    if ($this->db->delete('tbl_boms_element')) {
                        foreach ($bom_elements as $k => $val) {
                            $elementItems = $this->getBomsElementItemsReplace($val['id']);
                            $this->db->where('tbl_boms_element_items.bom_element_id', $val['id']);
                            $this->db->delete('tbl_boms_element_items');

                            foreach ($elementItems as $v) {
                                $this->db->where('tbl_boms_element_items_replace.bom_element_item_id', $v['id']);
                                $this->db->delete('tbl_boms_element_items_replace');
                            }
                        }
                    }
                }
            }
            if (!empty($bom_id)) {
                $element = $data['element'];
                if (!empty($element)) {
                    foreach ($element as $k => $val) {
                        $el_id = $this->db->insert('tbl_boms_element', [
                            'bom_id' => $bom_id,
                            'element_name' => $val['element_name'],
                            'quantity' => $val['element_number'],
                            'type_element' => $val['type_element'],
                        ]);
                        $el_id = $this->db->insert_id();
                        if ($el_id) {
                            if (!empty($val['items'])) {
                                $items = $val['items'];
                                foreach ($items as $v) {
                                    $replace = false;
                                    if (!empty($v['replace'])) {
                                        $replace = $v['replace'];
                                    }
                                    $this->db->insert('tbl_boms_element_items', [
                                        'bom_element_id' => $el_id,
                                        'type' => $v['type'],
                                        'item_id' => $v['item_id'],
                                        'unit_id' => $v['unit_id'],
                                        'quantity' => $v['element_item_number'],
                                        'stage_id' => $v['stage'],
                                        'quantity_compensation' => $v['quantity_compensation'],
                                        'type_element_item' => $v['type_element_item'],
                                        'landscape_print_size' => $v['landscape_print_size'],
                                        'vertical_print_size' => $v['vertical_print_size'],
                                        'number_children_size' => $v['number_children_size'],
                                        'paper_exchange' => $v['paper_exchange'],
                                        'hand_input_paper_exchange' => $v['hand_input_paper_exchange'],
                                        'face' => $v['face'],
                                        'face_after' => $v['face_after'],
                                    ]);
                                    $element_item_id = $this->db->insert_id();

                                    //handling insert replace
                                    if (!empty($element_item_id) && !empty($replace)) {
                                        foreach ($replace as $r) {
                                            $this->db->insert('tbl_boms_element_items_replace', [
                                                'bom_element_item_id' => $element_item_id,
                                                'type_replace' => $r['type_replace'],
                                                'item_id_replace' => $r['item_id_replace'],
                                                'unit_id_replace' => $r['unit_id_replace'],
                                                'quantity_replace' => $r['element_item_number_replace'],
                                            ]);
                                        }
                                    }
                                    //
                                }
                            }
                        }
                    }
                }
                return true;
            }
            return false;
        }
        return false;
    }
    //
    public function rowBomById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_boms');
        $this->db->where('tbl_boms.id', $id);
        return $this->db->get()->row_array();
    }

    public function getBomsElementByBomId($bom_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_boms_element');
        $this->db->where('tbl_boms_element.bom_id', $bom_id);
        return $this->db->get()->result_array();
    }

    public function getBomsElementItemsByBEI($bom_element_id, $type = false)
    {
        $this->db->select('tbl_boms_element_items.*, tblunits.unit as unit');
        $this->db->from('tbl_boms_element_items');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_boms_element_items.unit_id', 'left');
        $this->db->where('tbl_boms_element_items.bom_element_id', $bom_element_id);
        if ($type) {
            $this->db->where_in('tbl_boms_element_items.type', $type);
        }
        return $this->db->get()->result_array();
    }

    public function deleteCategoryBomById($id)
    {
        $this->db->where('tbl_boms.id', $id);
        if ($this->db->delete('tbl_boms')) {
            $bom_elements = $this->getBomsElementByBomId($id);
            $this->db->where('tbl_boms_element.bom_id', $id);
            if ($this->db->delete('tbl_boms_element')) {
                foreach ($bom_elements as $k => $val) {
                    $elementItems = $this->getBomsElementItemsReplace($val['id']);
                    $this->db->where('tbl_boms_element_items.bom_element_id', $val['id']);
                    $this->db->delete('tbl_boms_element_items');

                    foreach ($elementItems as $v) {
                        $this->db->where('tbl_boms_element_items_replace.bom_element_item_id', $v['id']);
                        $this->db->delete('tbl_boms_element_items_replace');
                    }
                }
            }
            return true;
        } else {
            return false;
        }
    }

    public function getBoms()
    {
        $this->db->select('*');
        $this->db->from('tbl_boms');
        return $this->db->get()->result_array();
    }

    public function rowBomByVersion($versions)
    {
        $this->db->select('*');
        $this->db->from('tbl_boms');
        $this->db->where('tbl_boms.versions', $versions);
        return $this->db->get()->row_array();
    }

    public function insertBatchProductWarehouse($data)
    {
        return $this->db->insert_batch('tbl_product_warehouse', $data);
    }

    public function insertBatchProductSuppliers($data)
    {
        return $this->db->insert_batch('tbl_product_suppliers', $data);
    }

    public function deleteProductSuppliersByProductId($product_id)
    {
        $this->db->where('product_id', $product_id);
        return $this->db->delete('tbl_product_suppliers');
    }

    public function deleteProductWarehouseByProductId($product_id)
    {
        $this->db->where('product_id', $product_id);
        return $this->db->delete('tbl_product_warehouse');
    }

    public function getGroupProductSuppliers($product_id)
    {
        $this->db->select('tblsuppliers.company as supplier_company, tbl_product_suppliers.supplier_id');
        $this->db->from('tbl_product_suppliers');
        $this->db->join('tblsuppliers', 'tblsuppliers.id = tbl_product_suppliers.supplier_id');
        $this->db->where('tbl_product_suppliers.product_id', $product_id);
        $this->db->group_by('tbl_product_suppliers.supplier_id');
        return $this->db->get()->result_array();
    }

    public function getProductSuppliersByProductAndSupplier($product_id, $supplier_id)
    {
        $this->db->select('tbl_product_suppliers.*, tblprocedure_client_detail.name as procedure_detail_name', false);
        $this->db->from('tbl_product_suppliers');
        $this->db->join('tblprocedure_client_detail', 'tblprocedure_client_detail.id = tbl_product_suppliers.procedure_id');
        $this->db->where('tbl_product_suppliers.product_id', $product_id);
        $this->db->where('tbl_product_suppliers.supplier_id', $supplier_id);
        $this->db->order_by('tbl_product_suppliers.sequence', 'asc');
        return $this->db->get()->result_array();
    }

    public function getProductWarehouse($product_id)
    {
        $this->db->select('tbl_product_warehouse.*, tblwarehouse.name as warehouse_name, tbllocaltion_warehouses.name as location_name');
        $this->db->from('tbl_product_warehouse');
        $this->db->join('tblwarehouse', 'tblwarehouse.id = tbl_product_warehouse.warehouse_id');
        $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tbl_product_warehouse.location_id');
        $this->db->where('tbl_product_warehouse.product_id', $product_id);
        return $this->db->get()->result_array();
    }

    public function getProductSuppliersByProductId($product_id)
    {
        $this->db->select('tbl_product_suppliers.*', false);
        $this->db->from('tbl_product_suppliers');
        $this->db->where('tbl_product_suppliers.product_id', $product_id);
        return $this->db->get()->result_array();
    }

    public function getBomsProducts($product_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_product_versions');
        $this->db->where('tbl_product_versions.product_id', $product_id);
        return $this->db->get()->result_array();
    }

    public function getProductVersionsByNotIdAndProduct($versions = false, $product_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_product_versions');
        if (!empty($versions)) {
            $this->db->where_not_in('tbl_product_versions.versions', $versions);
        }
        $this->db->where('tbl_product_versions.product_id', $product_id);
        return $this->db->get()->result_array();
    }

    public function insertExchangeProducts($data)
    {
        return $this->db->insert_batch('tbl_exchange_products', $data);
    }

    public function getExchangeProductsByProductId($product_id)
    {
        $this->db->select('tbl_exchange_products.*');
        $this->db->from('tbl_exchange_products');
        $this->db->where('tbl_exchange_products.product_id', $product_id);
        return $this->db->get()->result_array();
    }

    public function rowExchangeProducts($product_id, $unit_id)
    {
        $this->db->select('tbl_exchange_products.*');
        $this->db->from('tbl_exchange_products');
        $this->db->where('tbl_exchange_products.product_id', $product_id);
        $this->db->where('tbl_exchange_products.unit_id', $unit_id);
        return $this->db->get()->row_array();
    }

    public function deleteExchangeByProductId($product_id)
    {
        $this->db->where('product_id', $product_id);
        return $this->db->delete('tbl_exchange_products');
    }

    public function getExchangeProductsViewByProductId($product_id)
    {
        $this->db->select('tbl_exchange_products.*, tblunits.unit as unit_name');
        $this->db->from('tbl_exchange_products');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_exchange_products.unit_id', 'left');
        $this->db->where('tbl_exchange_products.product_id', $product_id);
        return $this->db->get()->result_array();
    }

    public function rowProductByCode($code)
    {
        $this->db->select('tbl_products.id, tbl_products.unit_id, tbl_products.type_products, tbl_products.code, tbl_products.name, tbl_products.images');
        $this->db->from('tbl_products');
        $this->db->where('tbl_products.code', $code);
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }

    public function rowProductByCodeBom($code)
    {
        $this->db->select('tbl_products.id, tbl_products.unit_id, tbl_products.type_products');
        $this->db->from('tbl_products');
        $this->db->where('tbl_products.code_bom', $code);
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }

    public function rowMaterialByCode($code)
    {
        $this->db->select('tbl_materials.id, tbl_materials.unit_id');
        $this->db->from('tbl_materials');
        $this->db->where('tbl_materials.code', $code);
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }

    public function rowStageByCode($code)
    {
        $this->db->select('tbl_stages.id');
        $this->db->from('tbl_stages');
        $this->db->where('tbl_stages.code', $code);
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }

    public function insertElementItemsReplace($data = [])
    {
        $this->db->insert('tbl_element_items_replace', $data);
        return $this->db->insert_id();
    }

    public function getBomsElementItemsReplace($bom_element_item_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_boms_element_items_replace');
        $this->db->where('tbl_boms_element_items_replace.bom_element_item_id', $bom_element_item_id);
        return $this->db->get()->result_array();
    }

    public function getCostPriceProductBom($product_id)
    {
        $tbProducts = "(
            SELECT
                tbl_products.id as id,
                tbl_products.code as code,
                tbl_products.name as name,
                tbl_products.price_import as price_import
            FROM tbl_products
        ) tb_products";

        $tbMaterials = "(
            SELECT
                tbl_materials.id as id,
                tbl_materials.code as code,
                tbl_materials.name as name,
                tbl_materials.price_import as price_import
            FROM tbl_materials
        ) tb_materials";

        $this->db->select("
            tbl_element_items.type as type,
            tbl_element_items.item_id as item_id,
            tbl_element_items.unit_id as unit_id,
            SUM(tbl_versions_element.quantity * tbl_element_items.quantity) as quantity,
            IF (tbl_element_items.type = 'materials', tb_materials.price_import, tb_products.price_import) as price_import,
            IF (tbl_element_items.type = 'materials', tb_materials.code, tb_products.code) as code,
            IF (tbl_element_items.type = 'materials', tb_materials.name, tb_products.name) as name,
            tbl_element_items.type as type,
        ", false);
        $this->db->from('tbl_products');
        $this->db->join('tbl_product_versions', 'tbl_product_versions.product_id = tbl_products.id AND tbl_product_versions.versions = tbl_products.versions');
        $this->db->join('tbl_versions_element', 'tbl_versions_element.version_id = tbl_product_versions.id');
        $this->db->join('tbl_element_items', 'tbl_element_items.element_id = tbl_versions_element.id');

        $this->db->join($tbProducts, 'tb_products.id = tbl_element_items.item_id AND tbl_element_items.type != "materials"', 'left');
        $this->db->join($tbMaterials, 'tb_materials.id = tbl_element_items.item_id AND tbl_element_items.type = "materials"', 'left');

        $this->db->where('tbl_products.id', $product_id);
        $this->db->group_by('tbl_element_items.type, tbl_element_items.item_id');
        // print_arrays($this->db->get_compiled_select());
        return $this->db->get()->result_array();
    }

    public function searchMaterialsSelect2($q, $limit = 50)
    {
        $this->db->select('
            CONCAT(tbl_materials.id, "__materials") as id, CONCAT(tbl_materials.code, "(", tbl_materials.name, ")") as text, tbl_materials.name as item_name, IF(tbl_materials.images IS NOT NULL && tbl_materials.images != "", CONCAT("uploads/materials/", "", tbl_materials.images, ""), "") as images, tblunits.unit as unit_name, tbl_materials.price_sell as price_sell, "" as info, CONCAT(tbl_materials.category_id, "__materials") as category_id,
            tbl_materials.code as item_code, 
            "" as size_name,
            "" as loss,
            "" as quantity_child_sheet,
            "" as quantity_sheet_bale,
            "" as mode_product,
            tbl_materials.name_customer as name_customer
        ', false);
        $this->db->from('tbl_materials');
        $this->db->join('tblunits', 'tbl_materials.unit_id = tblunits.unitid', 'left');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('tbl_materials.code', $q);
            $this->db->or_like('tbl_materials.name', $q);
            $this->db->group_end();
        }
        // $this->db->where('type_products', 'semi_products');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function insertSize($data)
    {
        $this->db->insert('tblsize', $data);
        return $this->db->insert_id();
    }

    public function getSizeById($id)
    {
        $this->db->select('*');
        $this->db->from('tblsize');
        $this->db->where('tblsize.id', $id);
        return $this->db->get()->row_array();
    }

    public function handlingDesignStages($product_id)
    {
        return false;

        $recursive_stages_array = recursive_stages_array();
        $versions = 'VS' . date('Y');
        $vs_stage_id = 0;
        $status = 'unapplication';

        $options['versions'] = $versions;
        $options['product_id'] = $product_id;

        if (!empty($recursive_stages_array)) {
            $ct = count($recursive_stages_array);
            foreach ($recursive_stages_array as $key => $value) {
                $options['items'][$key]['stage'] = $value['id'];
                $options['items'][$key]['number'] = $key + 1;
                $options['items'][$key]['number_hours'] = 0;
                $options['items'][$key]['final_stage'] = 0;
                $options['items'][$key]['machines'] = 0;
                if ($ct - 1 == $key) {
                    $options['items'][$key]['final_stage'] = 1;
                }
            }

            $q = $this->insertProductStages($options, $status, $vs_stage_id);
            if ($q) {
                $this->updateProducts($product_id, ['versions_stage' => $versions]);
                return true;
            }
        }
        return false;
    }

    public function handlingBOMPOD($data)
    {
        if (!empty($data)) {
            foreach ($data as $kData => $dt) {
                $product_id = $dt['product_id'];
                $versions = 'VS' . date('Y');
                $date_start = null;
                $date_end = null;
                $status = "unapplication";

                $options['versions'] = $versions;
                $options['product_id'] = $product_id;
                $options['date_start'] = $date_start;
                $options['date_end'] = $date_end;
                $options['date_created'] = date('Y-m-d H:i:s');
                $options['created_by'] = get_staff_user_id();

                $key = 0;
                $options['element'][$key]['element_name'] = "TP9";
                $options['element'][$key]['element_number'] = 1;
                $arrMaterialsBOM = $dt['arrMaterialsBOM'];
                if (!empty($arrMaterialsBOM)) {
                    foreach ($arrMaterialsBOM as $k => $v) {
                        $options['element'][$key]['items'][$k]['type'] = $v['type'];
                        $options['element'][$key]['items'][$k]['item_id'] = $v['item_id'];
                        $options['element'][$key]['items'][$k]['unit_id'] = $v['unit_id'];
                        $options['element'][$key]['items'][$k]['element_item_number'] = $v['quantity'];
                        $options['element'][$key]['items'][$k]['leadtime'] = 0;
                        $options['element'][$key]['items'][$k]['stage'] = 0;
                    }
                }

                $this->db->select('tbl_product_versions.id as id', false);
                $this->db->from('tbl_product_versions');
                $this->db->where('tbl_product_versions.product_id', $product_id);
                $this->db->where('tbl_product_versions.versions', $versions);
                $product_versions = $this->db->get()->row_array();
                if (!empty($product_versions)) {
                    $bom_id = $product_versions['id'];
                    $actions = 'edit';
                } else {
                    $bom_id = 0;
                    $actions = 'add';
                }
                $q = $this->insertBOM($options, $status, $bom_id, $actions);
                if ($q) {
                    $this->updateProducts($product_id, ['versions' => $versions]);
                }
            }
        }
    }

    //category stages
    public function insertCategoryStages($data)
    {
        $this->db->insert('tbl_category_stages', $data);
        return $this->db->insert_id();
    }

    public function rowCategoryStages($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_category_stages');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function updateCategoryStages($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_category_stages', $data);
    }

    public function deleteCategoryStages($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_category_stages');
    }

    public function getCategoryStages()
    {
        $this->db->select('*');
        $this->db->from('tbl_category_stages');
        $this->db->where('type_use', 0);
        return $this->db->get()->result_array();
    }

    public function checkExistCategoryStages($id)
    {
        $this->db->from('tbl_stages');
        $this->db->where('tbl_stages.category_stages', $id);
        $this->db->limit(1);
        $q = $this->db->count_all_results();
        if (!empty($q)) {
            return true;
        }

        return false;
    }

    public function getStages()
    {
        $this->db->select('*');
        $this->db->from('tbl_stages');
        if (defined('TYPE_USE') && TYPE_USE == 1) {
            $this->db->where('tbl_stages.type_use', 0);
        }
        return $this->db->get()->result_array();
    }

    public function getTypePrint()
    {
        $this->db->select('*');
        $this->db->from('tbl_type_print');
        return $this->db->get()->result_array();
    }

    public function getTypePrintById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_type_print');
        $this->db->where('tbl_type_print.id', $id);
        return $this->db->get()->row_array();
    }

    public function getTypePrintByCode($code)
    {
        $this->db->select('*');
        $this->db->from('tbl_type_print');
        $this->db->where('tbl_type_print.code', $code);
        return $this->db->get()->row_array();
    }

    public function getColumnsByCode($code)
    {
        $this->db->select('*');
        $this->db->from('tbl_columns');
        $this->db->where('tbl_columns.code', $code);
        return $this->db->get()->row_array();
    }

    public function insertBatchCategoryProductsCustomers($data)
    {
        return $this->db->insert_batch('tbl_category_products_customers', $data);
    }

    public function deleteCategoryProductsCustomers($category_products_id)
    {
        $this->db->where('tbl_category_products_customers.category_products_id', $category_products_id);
        return $this->db->delete('tbl_category_products_customers');
    }

    public function getCategoryProductsCustomers($category_products_id)
    {
        $this->db->select('tbl_category_products_customers.*', false);
        $this->db->from('tbl_category_products_customers');
        $this->db->where('tbl_category_products_customers.category_products_id', $category_products_id);
        return $this->db->get()->result_array();
    }

    public function insertStageCriteria($data)
    {
        $this->db->insert('tbl_stage_criteria', $data);
        return $this->db->insert_id();
    }

    public function getStageCriterial($stage_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_stage_criteria');
        $this->db->where('tbl_stage_criteria.stage_id', $stage_id);
        return $this->db->get()->result_array();
    }

    public function deleteStageCriteria($stage_id)
    {
        $this->db->where('stage_id', $stage_id);
        $this->db->delete('tbl_stage_criteria');
    }

    public function getMachinesById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_machines');
        $this->db->where('tbl_machines.id', $id);
        return $this->db->get()->row_array();
    }

    public function rowMachinesByCode($code)
    {
        $this->db->select('tbl_machines.id');
        $this->db->from('tbl_machines');
        $this->db->where('tbl_machines.code', $code);
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }

    public function getProductsColumns($id)
    {
        // $this->db->select('
        //     tbl_columns_detail.*
        // ', false);
        // $this->db->from('tbl_products');
        // $this->db->join('tbl_columns', 'tbl_columns.id = tbl_products.columns_id');
        // $this->db->join('tbl_columns_detail', 'tbl_columns_detail.columns_id = tbl_columns.id');
        // $this->db->where('tbl_products.id', $id);
        // return $this->db->get()->result_array();


        $this->db->select('
            tbl_columns_detail.*
        ', false);
        $this->db->from('tbl_products');
        $this->db->join('tbl_products_columns', 'tbl_products_columns.product_id = tbl_products.id');
        $this->db->join('tbl_columns', 'tbl_columns.id = tbl_products_columns.columns_id');
        $this->db->join('tbl_columns_detail', 'tbl_columns_detail.columns_id = tbl_columns.id');
        $this->db->where('tbl_products.id', $id);
        return $this->db->get()->result_array();
    }

    public function insertBatchStagesCustomerPrices($data)
    {
        return $this->db->insert_batch('tbl_stages_customer_prices', $data);
    }

    public function deleteStagesCustomerPrices($stage_id)
    {
        $this->db->where('tbl_stages_customer_prices.stage_id', $stage_id);
        return $this->db->delete('tbl_stages_customer_prices');
    }

    public function getStagesCustomerPrices($stage_id)
    {
        $this->db->select('
            tbl_stages_customer_prices.*,
            tblcustomers_groups.name as customers_groups_name
        ', false);
        $this->db->from('tbl_stages_customer_prices');
        $this->db->join('tblcustomers_groups', 'tblcustomers_groups.id = tbl_stages_customer_prices.customers_groups_id');
        $this->db->where('tbl_stages_customer_prices.stage_id', $stage_id);
        return $this->db->get()->result_array();
    }

    public function insertBatchProductsColumns($data)
    {
        return $this->db->insert_batch('tbl_products_columns', $data);
    }

    public function deleteProductsColumns($product_id)
    {
        $this->db->where('product_id', $product_id);
        return $this->db->delete('tbl_products_columns');
    }

    public function getProductsColumnsMul($product_id)
    {
        $this->db->select('tbl_products_columns.*, tbl_columns.code as code_columns');
        $this->db->from('tbl_products_columns');
        $this->db->join('tbl_columns', 'tbl_columns.id = tbl_products_columns.columns_id');
        $this->db->where('tbl_products_columns.product_id', $product_id);
        return $this->db->get()->result_array();
    }

    public function checkCodeExistStages($code)
    {
        $this->db->select('id');
        $this->db->from('tbl_stages');
        $this->db->where('tbl_stages.code', $code);
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }

    public function getCategoryStagesByCode($code)
    {
        $this->db->select('
            tbl_category_stages.*
        ', false);
        $this->db->from('tbl_category_stages');
        $this->db->where('tbl_category_stages.code', $code);
        return $this->db->get()->row_array();
    }

    public function checkCodeExistCategoryStages($code)
    {
        $this->db->from('tbl_category_stages');
        $this->db->where('tbl_category_stages.code', $code);
        $this->db->limit(1);
        return $this->db->get()->num_rows();
    }

    public function getTypeProductionList()
    {
        $this->db->select('tbl_type_productionlist.*');
        $this->db->from('tbl_type_productionlist');
        return $this->db->get()->result_array();
    }

    public function getMachines()
    {
        $this->db->select('
            tbl_machines.id as id,
            tbl_machines.code as code,
            tbl_machines.name as name,
        ');
        $this->db->from('tbl_machines');
        return $this->db->get()->result_array();
    }

    public function searchQR($code = '')
    {
        $data = [];
        $code = explode('||', $code);
        if (empty($code[1])) {
            $data['items'] = [];
            $data['result'] = 0;
            $data['message'] = lang('Sai định dạng QR');
            return $data;
        }
        $type = $code[0];
        $id = $code[1];
        if ($type == 'products') {
            $type = 'product';
        } elseif ($type == 'materials') {
            $type = 'nvl';
        } elseif ($type == 'tools_supplies') {
            $type = 'tools';
        } else {
            $data['items'] = [];
            $data['result'] = 0;
            $data['message'] = lang('Sai định dạng QR');
            return $data;
        }
        $items = get_full_item_new($id, $type);
        if (empty($items)) {
            $data['items'] = [];
            $data['result'] = 0;
            $data['message'] = lang('Mặt hàng không tồn tại');
            return $data;
        }
        $items->type = $type;
        if ($type == 'tools') {
            $items->type_item = 'tools_supplies';
        }
        $items->html = format_item_color($id, $type);
        $items->avatar = (!empty($items->avatar) ? (file_exists($items->avatar) ? base_url($items->avatar) : (file_exists('uploads/materials/' . $items->avatar) ? base_url('uploads/materials/' . $items->avatar) : (file_exists('uploads/products/' . $items->avatar) ? base_url('uploads/products/' . $items->avatar) : (file_exists('uploads/tools_supplies/' . $items->avatar) ? base_url('uploads/tools_supplies/' . $items->avatar) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));

        $data['items'] = $items;
        $data['result'] = 1;
        $data['message'] = lang('Thành công');
        return $data;
    }

    public function getNormsByProductId($id)
    {
        $this->db->from('tbl_product_versions');
        $this->db->where('tbl_product_versions.product_id', $id);
        $this->db->get()->result_array();
    }
    public function rowProductALL($id)
    {
        $this->db->select('tbl_products.*, tbl_category_products.name as category_name, tbl_category_products.code as category_code,tbl_species.name as species_name,tb_unit_measure.unit as unit_measure,tbl_brand.name as brand_name', false);
        $this->db->from('tbl_products');
        $this->db->join('tbl_category_products', 'tbl_category_products.id = tbl_products.category_id', 'left');
        $this->db->join('tbl_species', 'tbl_species.id = tbl_products.species', 'left');
        $this->db->join('tblunits', 'tbl_products.unit_id = tblunits.unitid', 'left');
        $this->db->join('tblunits unit_stock', 'tbl_products.conversion_unit = unit_stock.unitid', 'left');
        $this->db->join('tblunits tb_unit_measure', 'tbl_products.unit_measure = tb_unit_measure.unitid', 'left');
        $this->db->join('tbl_brand', 'tbl_brand.id = tbl_products.brand_id', 'left');
        $this->db->where('tbl_products.id', $id);
        return $this->db->get()->row_array();
    }
}
