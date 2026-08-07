<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Invoice_items_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get invoice item by ID
     * @param  mixed $id
     * @return mixed - array if not passed id, object if id passed
     */
    public function get($id = '')
    {
        $columns             = $this->db->list_fields(db_prefix() . 'items');
        $rateCurrencyColumns = '';
        foreach ($columns as $column) {
            if (strpos($column, 'rate_currency_') !== false) {
                $rateCurrencyColumns .= $column . ',';
            }
        }
        $this->db->select($rateCurrencyColumns . '' . db_prefix() . 'items.id as itemid,rate,
            t1.taxrate as taxrate,t1.id as taxid,t1.name as taxname,
            t2.taxrate as taxrate_2,t2.id as taxid_2,t2.name as taxname_2,
            description,long_description,group_id,' . db_prefix() . 'items_groups.name as group_name,unit');
        $this->db->from(db_prefix() . 'items');
        $this->db->join('' . db_prefix() . 'taxes t1', 't1.id = ' . db_prefix() . 'items.tax', 'left');
        $this->db->join('' . db_prefix() . 'taxes t2', 't2.id = ' . db_prefix() . 'items.tax2', 'left');
        $this->db->join(db_prefix() . 'items_groups', '' . db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id', 'left');
        $this->db->order_by('description', 'asc');
        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'items.id', $id);

            return $this->db->get()->row();
        }

        return $this->db->get()->result_array();
    }
    public function get_full_edit($id = '',$warehouse_id='')
    {
        $this->db->select('tblitems.*,tblunits.unit as unit_name,tbltaxes.name as tax_name, tbltaxes.taxrate as tax_rate')->distinct();
        $this->db->from('tblitems');
        $this->db->join('tblunits','tblunits.unitid=tblitems.unit','left');
        $this->db->join('tbltaxes','tbltaxes.id=tblitems.tax','left');
        $this->db->order_by('tblitems.id', 'desc');
        
        if (is_numeric($warehouse_id)) {
            $this->db->where('tblwarehouses_products.warehouse_id', $warehouse_id);
        }
        if (is_numeric($id)) {
            
            $this->db->where('tblitems.id', $id);
            $item = $this->db->get()->row();
            return $item;
        }
        return $this->db->get()->result_array();
    }
    public function get_grouped()
    {
        $items = [];
        $this->db->order_by('name', 'asc');
        $groups = $this->db->get(db_prefix() . 'items_groups')->result_array();

        array_unshift($groups, [
            'id'   => 0,
            'name' => '',
        ]);

        foreach ($groups as $group) {
            $this->db->select('*,' . db_prefix() . 'items_groups.name as group_name,' . db_prefix() . 'items.id as id');
            $this->db->where('group_id', $group['id']);
            $this->db->join(db_prefix() . 'items_groups', '' . db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id', 'left');
            $this->db->order_by('description', 'asc');
            $_items = $this->db->get(db_prefix() . 'items')->result_array();
            if (count($_items) > 0) {
                $items[$group['id']] = [];
                foreach ($_items as $i) {
                    array_push($items[$group['id']], $i);
                }
            }
        }

        return $items;
    }
    public function change_items_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'items', [
            'active' => $status,
        ]);

        if ($this->db->affected_rows() > 0) {
            log_activity('Items Status Changed [ID: ' . $id . ' Status(Active/Inactive): ' . $status . ']');

            return true;
        }

        return false;
    }
    /**
     * Add new invoice item
     * @param array $data Invoice item data
     * @return boolean
     */
    public function add($data)
    {
        unset($data['itemid']);
        if ($data['tax'] == '') {
            unset($data['tax']);
        }
        if (isset($data['group_id']) && $data['group_id'] == '') {
            $data['group_id'] = 0;
        }
        if (isset($data['brand_id']) && $data['brand_id'] == '') {
            $data['brand_id'] = 0;
        }
        if (empty($data['is_tax'])) {
            $data['is_tax'] = 0;
        }
        $data['active'] = 1;
        $data['prefix'] = get_option('prefix_product');
        $data['staff_id'] = get_staff_user_id();
        $data['date_create'] = date('Y-m-d H:i:s');
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }
        $this->db->insert('tblitems', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            if (isset($custom_fields)) {
                $_custom_fields = $custom_fields;
                // Possible request from the register area with 2 types of custom fields for contact and for comapny/customer
                    unset($custom_fields);
                    $custom_fields['items']                = $_custom_fields['items'];
                handle_custom_fields_post($insert_id, $custom_fields);
            }
            logActivity('New Invoice Item Added [ID:' . $insert_id . ', ' . $data['description'] . ']');
            return $insert_id;
        }
        return false;
    }

    /**
     * Update invoiec item
     * @param  array $data Invoice data to update
     * @return boolean
     */
    public function edit($data,$id)
    {
        $item = $this->get_full_edit($id);
        if(isset($data['price']) && isset($item) && $data['price'] != $item->price) {
            $price_history_data = array(
                'item_id' => $item->id,
                'price' => $item->price,
                'staff' => get_staff_user_id(),
                'new_price' => $data['price']
            );

            $this->db->insert('tblitem_price_history', $price_history_data);
        }
        if(isset($data['price_single']) && isset($item) && $data['price_single'] != $item->price) {
            $price_single_history_data = array(
                'item_id' => $item->id,
                'price' => $item->price_single,
                'staff' => get_staff_user_id(),
                'new_price' => $data['price_single']
            );

            $this->db->insert('tblitem_price_single_history', $price_single_history_data);
        }
        if ($data['tax'] == '') {
            unset($data['tax']);
        }
        if (isset($data['group_id']) && $data['group_id'] == '') {
            $data['group_id'] = 0;
        }
        if (isset($data['brand_id']) && $data['brand_id'] == '') {
            $data['brand_id'] = 0;
        }
        if (empty($data['is_tax'])) {
            $data['is_tax'] = 0;
        }
        $data['active'] = 1;
        unset($data['DataTables_Table_0_length']);  
        unset($data['DataTables_Table_1_length']);    
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }
        $this->db->where('id', $id);
        $success = $this->db->update(db_prefix() . 'items', $data);
        if ($success) {
            if (isset($custom_fields)) {
            $_custom_fields = $custom_fields;
                unset($custom_fields);
                $custom_fields['items']                = $_custom_fields['items'];
                handle_custom_fields_post($id, $custom_fields);
            }
            log_activity('Invoice Item Updated [ID: ' . $id . ']');
            $affectedRows++;
        }

        // if (isset($custom_fields)) {
        //     if (handle_custom_fields_post($itemid, $custom_fields, true)) {
        //         $affectedRows++;
        //     }
        // }

        // if ($affectedRows > 0) {
        //     hooks()->do_action('item_updated', $itemid);
        // }

        return $affectedRows > 0 ? true : false;
    }

    public function search($q)
    {
        $this->db->select('rate, id, description as name, long_description as subtext');
        $this->db->like('description', $q);
        $this->db->or_like('long_description', $q);

        $items = $this->db->get(db_prefix() . 'items')->result_array();

        foreach ($items as $key => $item) {
            $items[$key]['subtext'] = strip_tags(mb_substr($item['subtext'], 0, 200)) . '...';
            $items[$key]['name']    = '(' . app_format_number($item['rate']) . ') ' . $item['name'];
        }

        return $items;
    }

    /**
     * Delete invoice item
     * @param  mixed $id
     * @return boolean
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'items');
        if ($this->db->affected_rows() > 0) {
            $this->db->where('relid', $id);
            $this->db->where('fieldto', 'items_pr');
            $this->db->delete(db_prefix() . 'customfieldsvalues');

            $this->db->where('item_id', $id);
            $this->db->delete('tblitem_price_history');
            $this->db->where('item_id', $id);
            $this->db->delete('tblitem_price_single_history');
            log_activity('Invoice Item Deleted [ID: ' . $id . ']');

            hooks()->do_action('item_deleted', $id);

            return true;
        }

        return false;
    }

    public function get_groups()
    {
        $this->db->order_by('name', 'asc');

        return $this->db->get(db_prefix() . 'items_groups')->result_array();
    }
    public function get_brands()
    {
        $this->db->order_by('name', 'asc');

        return $this->db->get(db_prefix() . 'items_brands')->result_array();
    }
    public function add_brand($data)
    {
        $this->db->insert(db_prefix() . 'items_brands', $data);
        log_activity('Items Brand Created [Name: ' . $data['name'] . ']');

        return $this->db->insert_id();
    }
    public function edit_brand($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'items_brands', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Items Brand Updated [Name: ' . $data['name'] . ']');

            return true;
        }

        return false;
    }
    public function delete_brand($id)
    {
        $this->db->where('id', $id);
        $group = $this->db->get(db_prefix() . 'items_brands')->row();

        if ($group) {
            // $this->db->where('brand_id', $id);
            // $this->db->update(db_prefix() . 'items', [
            //     'brand_id' => 0,
            // ]);

            $this->db->where('id', $id);
            $this->db->delete(db_prefix() . 'items_brands');

            log_activity('Item Brand Deleted [Name: ' . $group->name . ']');

            return true;
        }

        return false;
    }
    public function add_group($data)
    {
        $this->db->insert(db_prefix() . 'items_groups', $data);
        log_activity('Items Group Created [Name: ' . $data['name'] . ']');

        return $this->db->insert_id();
    }

    public function edit_group($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'items_groups', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Items Group Updated [Name: ' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    public function delete_group($id)
    {
        $this->db->where('id', $id);
        $group = $this->db->get(db_prefix() . 'items_groups')->row();

        if ($group) {
            $this->db->where('group_id', $id);
            $this->db->update(db_prefix() . 'items', [
                'group_id' => 0,
            ]);

            $this->db->where('id', $id);
            $this->db->delete(db_prefix() . 'items_groups');

            log_activity('Item Group Deleted [Name: ' . $group->name . ']');

            return true;
        }

        return false;
    }
    public function delete_image_product($image=NULL,$product_id=NULL)
    {
        if(is_numeric($product_id) && isset($image))
        {
            $item=$this->db->get_where('tblitems',array('id'=>$product_id))->row();
            
            $images=explode(',',$item->images_product);
            foreach ($images as $key => $value) {
                if($value==$image)
                {
                    unset($images[$key]);
                    unlink($image);
                }
            }
            
            $this->db->update('tblitems',array('images_product'=>implode(',', $images)),array('id'=>$item->id));
            if($this->db->affected_rows()>0)
            {
                return true;
            }
        }
        return false;
    }
    public function delete_image_avatar($product_id=NULL)
    {
        if(is_numeric($product_id))
        {   
            if (file_exists(get_upload_path_by_type_ch('items') . $product_id)) {
                delete_dir_ch(get_upload_path_by_type_ch('items') . $product_id);
            }
            $this->db->update('tblitems',array('avatar'=>NULL),array('id'=>$product_id));
            if($this->db->affected_rows()>0)
            {
                return true;
            }
        }
        return false;
    }
}
