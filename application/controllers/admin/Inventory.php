<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Inventory extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('inventory_model');
        $this->load->model('invoice_items_model');
        $this->load->model('adjusted_model');
    }
    public function index()
    {
        if (!has_permission('inventory', '', 'view') && !has_permission('inventory', '', 'view_own')) {
            access_denied('Inventory');
        }
        $data['type_items'] = get_table_where('tbltype_items', array('active' => 1));
        $data['title']          = _l('ch_inventory_warehouse');
        $this->load->view('admin/inventory/manage', $data);
    }
    public function table()
    {
        $this->app->get_table_data('inventory');
    }
    public function update_submit($id = '')
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $counts = 0;
            foreach ($data['items'] as $key => $item) {
                if (!empty($item['quantity_net'])) {
                    $_item['quantity'] = $item['quantity'];
                    $_item['quantity_net'] = $item['quantity_net'];
                    $_item['quantity_diff'] = $item['quantity_diff'];
                    $_item['handling'] = $item['handling'];
                    $this->db->update('tblinventory_items', $_item, array('id' => $item['id']));
                    if ($this->db->affected_rows()) {
                        $counts++;
                    }
                }
            }
            echo json_encode([
                'success' => true,
                'id'      => $data['id_inventory'],
            ]);
        }
    }

    public function test_quantity_times($id = '')
    {
        $inventory = get_table_where('tblinventory', array('id' => $id), '', 'row');
        $item = get_table_where('tblinventory_items', array('inventory_id' => $id));
        $count = array();
        $i = 0;

        foreach ($item as $key => $value) {
            $quantity = $this->get_localtion($value['product_id'], $inventory->date, $inventory->warehouse_id, $value['localtion'], $value['type'], $value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use']);
            if ($quantity != $value['quantity']) {

                $value['name_localtion'] = get_listname_localtion_warehouse($value['localtion']);
                $value['get_quantity_time'] = $quantity;
                $value['items'] = $this->invoice_items_model->get_full_item($value['product_id'], $value['type']);
                $count['item'][$i] = $value;
                $i++;
            }
        }
        $inventory->date = _d($inventory->date);
        $count['inventory'] = $inventory;
        echo json_encode($count);
    }
    public function get_localtion($id, $date, $warehouses, $localtion, $type, $lot_code = NULL, $date_sx = NULL, $date_sd = NULL, $date_use = NULL)
    {
        $data = array();
        if ($date != date('Y-m-d')) {
            $whereJoin = array();
            $whereJoin['where'] = array(
                'date_warehouse <= ' => $date . ' 23:59:59',
                'product_id ' => $id,
                'localtion ' => $localtion,
                'type_items ' => $type,
                'lot_code ' => $lot_code,
                'date_sx ' => $date_sx,
                'date_sd ' => $date_sd,
                'date_use ' => $date_use,
            );
            $whereJoin['join'] = array();
            $whereJoin['field'] = 'quantity';
            $get_quantity_import = sum_from_table_join('tblwarehouse_product', $whereJoin);
            $whereJoin_export = array();
            $whereJoin_export['where'] = array(
                'date_warehouse <= ' => $date . ' 23:59:59',
                'product_id ' => $id,
                'localtion ' => $localtion,
                'type_items ' => $type,
                'lot_code ' => $lot_code,
                'date_sx ' => $date_sx,
                'date_sd ' => $date_sd,
                'date_use ' => $date_use,
            );
            $whereJoin_export['join'] = array();
            $whereJoin_export['field'] = 'quantity';
            $get_quantity_export = sum_from_table_join('tblwarehouse_export', $whereJoin_export);
            if (empty($get_quantity_export)) {
                $get_quantity_export = 0;
            }
            if (empty($get_quantity_import)) {
                $get_quantity_import = 0;
            }
        } else {
            $localtion = get_table_where('tblwarehouse_items', array('warehouse_id' => $warehouses, 'id_items' => $id, 'type_items' => $type, 'lot_code' => $lot_code, 'date_sx' => $date_sx, 'date_sd' => $date_sd, 'date_use' => $date_use), '', 'row');
            $get_quantity_export = 0;
            $get_quantity_import = 0;
            if (!empty($localtion)) {
                $get_quantity_import = $localtion->product_quantity;
            }
        }
        $get_quantity_import = $get_quantity_import - $get_quantity_export;
        return $get_quantity_import;
    }
    public function SearchItems()
    {
        $data = [];
        $search = $this->input->get('term');
        $type = $this->input->get('type');
        $limit_one = 15;
        $limit_two = 15;
        $limit_all = 50;

        if ($type == -1) {
            $this->db->select(
                '
                    id,
                    tblitems.name as text,
                    tblitems.price,
                    concat("items") as type,
                    tblitems.avatar as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tblitems.name', $search);
                $this->db->or_like('tblitems.code', $search);
                $this->db->group_end();
            }
            $this->db->order_by('name', 'DESC');
            $this->db->limit($limit_one);
            $items = $this->db->get('tblitems')->result_array();
            if (!empty($items)) {
                $data['results'][] =
                    [
                        'text' => _l('Sản phẩm'),
                        'children' => $items
                    ];
            }
            $count_items = count($items);
            $this->db->select(
                '
                id as id,
                tbl_products.name as text,
                tbl_products.price_sell as price,
                concat("product") as type,
                CONCAT("uploads/products/", "", tbl_products.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_products.name', $search);
                $this->db->or_like('tbl_products.code', $search);
                $this->db->group_end();
            }
            $this->db->order_by('tbl_products.name', 'DESC');
            $this->db->limit($limit_two);
            // $this->db->limit(($limit_all - $count_product));
            $product = $this->db->get('tbl_products')->result_array();
            if (!empty($product)) {
                $data['results'][] =
                    [
                        'text' => _l('Thành phẩm'),
                        'children' => $product
                    ];
            }

            $count_product = count($product);
            $this->db->select(
                '
                id as id,
                tbl_materials.name as text,
                tbl_materials.price_sell as price,
                concat("nvl") as type,
                CONCAT("uploads/materials/", "", tbl_materials.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_materials.name', $search);
                $this->db->or_like('tbl_materials.code', $search);
                $this->db->group_end();
            }
            $this->db->order_by('tbl_materials.name', 'DESC');
            $this->db->limit(($limit_all - $count_product - $count_items));
            $product = $this->db->get('tbl_materials')->result_array();
            if (!empty($product)) {
                $data['results'][] =
                    [
                        'text' => _l('Nguyên vật liệu'),
                        'children' => $product
                    ];
            }
        } else
        if ($type == 'items') {
            $this->db->select(
                '
                    id as id,
                    tblitems.name as text,
                    tblitems.price,
                    items as type,
                    tblitems.avatar as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tblitems.name', $search);
                $this->db->or_like('tblitems.code', $search);
                $this->db->group_end();
            }
            $this->db->order_by('name', 'DESC');
            $this->db->limit(50);
            $items = $this->db->get('tblitems')->result_array();
            if (!empty($items)) {
                $data['results'][] =
                    [
                        'text' => _l('Sản phẩm'),
                        'children' => $items
                    ];
            }
        } else
        if ($type == 'product') {
            $this->db->select(
                '
                id as id,
                tbl_products.name as text,
                tbl_products.price_sell as price,
                product as type,
                CONCAT("uploads/products/", "", tbl_products.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_products.name', $search);
                $this->db->or_like('tbl_products.code', $search);
                $this->db->group_end();
            }
            $this->db->order_by('tbl_products.name', 'DESC');
            $this->db->limit(50);
            // $this->db->limit(($limit_all - $count_product));
            $product = $this->db->get('tbl_products')->result_array();
            if (!empty($product)) {
                $data['results'][] =
                    [
                        'text' => _l('Thành phẩm'),
                        'children' => $product
                    ];
            }
        } elseif ($type == 'nvl') {
            $this->db->select(
                '
                id as id,
                tbl_materials.name as text,
                tbl_materials.price_sell as price,
                nvl as type,
                CONCAT("uploads/materials/", "", tbl_materials.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_materials.name', $search);
                $this->db->or_like('tbl_materials.code', $search);
                $this->db->group_end();
            }
            $this->db->order_by('tbl_materials.name', 'DESC');
            $this->db->limit(50);
            $product = $this->db->get('tbl_materials')->result_array();
            if (!empty($product)) {
                $data['results'][] =
                    [
                        'text' => _l('Nguyên vật liệu'),
                        'children' => $product
                    ];
            }
        }
        echo json_encode($data);
        die();
    }
    public function detail($id = '')
    {
        if (!has_permission('inventory', '', 'create')) {
            ajax_access_denied();
        }
        if ($this->input->post()) {
            if ($id == '') {

                if (!has_permission('inventory', '', 'create')) {
                    access_denied('inventory');
                }

                $data                 = $this->input->post();
                $data['note'] = $this->input->post('note', true);
                if (isset($data['items']) && count($data['items']) > 0) {

                    $id = $this->inventory_model->add($data);
                }

                if ($id) {
                    $this->create_adj($id);
                    $get_code = get_table_where('tblinventory', array('id' => $id), '', 'row');
                    activity_log_v2('inventory', 'tblinventory', $id, $get_code->prefix . $get_code->code, 'Thêm mới phiếu kiểm kê kho [' . $get_code->prefix . $get_code->code . ']');
                    set_alert('success', _l('ch_added_successfuly'));
                    redirect(admin_url('inventory'));
                }
            } else {
                if (!has_permission('inventory', '', 'edit')) {
                    access_denied('inventory');
                }
                $data                 = $this->input->post();
                $data['note'] = $this->input->post('note', true);
                $success = $this->inventory_model->update($data, $id);
                if ($success == true) {
                    $get_code = get_table_where('tblinventory', array('id' => $id), '', 'row');
                    activity_log_v2('inventory', 'tblinventory', $id, $get_code->prefix . $get_code->code, 'Cập nhật phiếu kiểm kê kho [' . $get_code->prefix . $get_code->code . ']');
                    set_alert('success', _l('ch_updated_successfuly'));
                }
                redirect(admin_url('inventory/detail/' . $id));
            }
        }
        if ($id != '') {
            if (!has_permission('inventory', '', 'edit')) {
                access_denied('inventory');
            }
            $data['title']          = _l('ch_edit_inventorys');
            $data['items'] = $this->inventory_model->get($id);
        } else {
            if (!has_permission('inventory', '', 'create')) {
                access_denied('inventory');
            }
            $data['title']          = _l('ch_add_inventorys');
        }
        $data['taxes'] = get_taxes_dropdown_template('', 0);
        $data['tax'] = get_table_where('tbltaxes');
        $type_items = get_table_where('tbltype_items', array('active' => 1));
        $count = 0;
        $data['type_items'][0] = array('type' => '-1', 'name' => _l('task_list_all'));
        foreach ($type_items as $key => $value) {
            $count++;
            $data['type_items'][$count] = $value;
        }
        $data['suppliers'] = get_table_where('tblsuppliers', array('type' => 0));
        $data['warehouse'] = get_table_where('tblwarehouse', array('id !=' => 8));
        $data['localtion_warehouses'] = array();

        $this->load->view('admin/inventory/detail', $data);
    }
    public function create_adj($id = '')
    {

        $items = $this->inventory_model->get($id);
        $duong = get_table_where('tblinventory_items', array('inventory_id' => $id, 'quantity_diff >' => 0));
        if (!empty($duong)) {
            $data['id_inventory'] = $id;
            $data['prefix'] = get_option('prefix_detail_up');
            $data['code'] = sprintf('%06d', ch_getMaxID('id', 'tbladjusted') + 1);
            $data['date'] = $items->date;
            $data['date_create'] = date('Y-m-d H:i:s');
            $data['warehouse_id'] = $items->warehouse_id;
            $data['staff_id'] = get_staff_user_id();
            $data['type'] = 1;
            $this->db->insert('tbladjusted', $data);
            $id_duong = $this->db->insert_id();
            $warehouse_id = $items->warehouse_id;
            $date_import = date('Y-m-d');
            $_item = array();
            $amount_sub = 0;
            foreach ($duong as $key => $value) {
                $price = $this->adjusted_model->get_full_item($value['product_id'], $value['type']);
                $_item['id_adjusted'] = $id_duong;
                $_item['product_id'] = $value['product_id'];
                $_item['unit_cost'] = $price->price;
                $_item['warehouse_id'] = $warehouse_id;
                $_item['localtion'] = $value['localtion'];
                $_item['type'] = $value['type'];
                $_item['lot_code'] = $value['lot_code'];
                $_item['date_sx'] = $value['date_sx'];
                $_item['date_sd'] = $value['date_sd'];
                $_item['date_use'] = $value['date_use'];
                $_item['info_items'] = $value['info_items'];
                $_item['quantity_unit'] = $value['quantity_unit'];
                $_item['quantity_stock'] = $value['quantity_stock'];
                $_item['quantity_payment'] = $value['quantity_payment'];
                $_item['exchange_unit'] = $value['exchange_unit'];
                $_item['exchange_stock'] = $value['exchange_stock'];
                $_item['exchange_payment'] = $value['exchange_payment'];


                $_item['quantity'] = str_replace(',', '', $value['quantity']);
                $_item['quantity_net'] = str_replace(',', '', $value['quantity_diff']);
                $_item['price'] = $value['price'];
                $_item['amount'] = $_item['price'] * abs(str_replace(',', '', $_item['quantity_net']));
                $amount_sub += $_item['amount'];
                $this->db->insert('tbladjusted_items', $_item);
                $idd = $this->db->insert_id();
                $date_warehouse = date('Y-m-d H:i:s');
                $localtion =  $_item['localtion'];
                $product_id = $_item['product_id'];
                $type_items = $_item['type'];
                $quantity = $_item['quantity_net'];
                $price = $_item['price'];
                $count = increaseadjuProductQuantity($warehouse_id, $id_duong, $date_warehouse, $data['date'], $product_id, $quantity, $localtion, $type_items, $price, $value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use'], $_item['quantity_unit'], $_item['quantity_payment']);
                //tăng kho tổng
                increaseWarehuseQuantity($warehouse_id, $localtion, $product_id, $quantity, $type_items, $value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use'], $_item['quantity_unit'], $_item['quantity_payment']);
            }
            $this->db->update('tbladjusted', array('subtotal' => $amount_sub), array('id' => $id_duong));
        }
        $am = get_table_where('tblinventory_items', array('inventory_id' => $id, 'quantity_diff <=' => 0));
        if (!empty($am)) {
            $amount_sub = 0;
            $data['id_inventory'] = $id;
            $data['prefix'] = get_option('prefix_detail_up');
            $data['code'] = sprintf('%06d', ch_getMaxID('id', 'tbladjusted') + 1);
            $data['date'] =  $items->date;
            $data['date_create'] = date('Y-m-d H:i:s');
            $data['warehouse_id'] = $items->warehouse_id;
            $data['staff_id'] = get_staff_user_id();
            $data['type'] = 2;
            $this->db->insert('tbladjusted', $data);
            $id_am = $this->db->insert_id();
            $_item = array();
            $warehouse_id = $items->warehouse_id;
            foreach ($am as $key => $value) {
                $price = $this->adjusted_model->get_full_item($value['product_id'], $value['type']);
                $_item['id_adjusted'] = $id_am;
                $_item['product_id'] = $value['product_id'];
                $_item['unit_cost'] = $price->price;
                $_item['warehouse_id'] = $warehouse_id;
                $_item['localtion'] = $value['localtion'];
                $_item['type'] = $value['type'];
                $_item['lot_code'] = $value['lot_code'];
                $_item['date_sx'] = $value['date_sx'];
                $_item['date_sd'] = $value['date_sd'];
                $_item['date_use'] = $value['date_use'];
                $_item['info_items'] = $value['info_items'];
                $_item['quantity_unit'] = $value['quantity_unit'];
                $_item['quantity_stock'] = $value['quantity_stock'];
                $_item['quantity_payment'] = $value['quantity_payment'];
                $_item['exchange_unit'] = $value['exchange_unit'];
                $_item['exchange_stock'] = $value['exchange_stock'];
                $_item['exchange_payment'] = $value['exchange_payment'];
                $_item['quantity'] = str_replace(',', '', $value['quantity']);
                $_item['quantity_net'] = abs(str_replace(',', '', $value['quantity_diff']));
                $_item['price'] = $value['price'];
                $_item['amount'] = $_item['price'] * abs(str_replace(',', '', $_item['quantity_net']));
                $amount_sub += $_item['amount'];
                $this->db->insert('tbladjusted_items', $_item);
                $idd = $this->db->insert_id();


                $date_warehouse = date('Y-m-d H:i:s');
                $localtion  =  $_item['localtion'];
                $product_id = $_item['product_id'];
                $type_items = $_item['type'];
                $quantity = abs($_item['quantity_net']);
                export_AdjuWarehuseQuantity($warehouse_id, $id_am, $date_warehouse, $data['date'], $product_id, $quantity, $localtion, $type_items, $value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use'], $_item['quantity_unit'], $_item['quantity_payment']);
                $count = decreaseAdjuWarehuseQuantity($warehouse_id, $idd, $product_id, $quantity, $localtion, $type_items, $value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use'], $_item['quantity_unit'], $_item['quantity_payment']);
                //trừ kho tổng
                decreaseWarehuseQuantity($warehouse_id, $localtion, $product_id, $quantity, $type_items, $value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use'], $_item['quantity_unit'], $_item['quantity_payment']);
            }
            $this->db->update('tbladjusted', array('subtotal' => $amount_sub), array('id' => $id_am));
        }
        set_alert('success', _l('ch_added_successfuly'));
        redirect(admin_url('inventory'));
    }
    public function add()
    {
        if ($this->input->post()) {
            $message = '';
            $data = $this->input->post();
            unset($data['id']);
            if ($data['costs_parent'] == NULL || $data['costs_parent'] == '') {
                $data['lever'] = 1;
            } else {
                $lever = 1;
                $parent = $data['costs_parent'];


                while ($parent > 0) {
                    $ktr = get_table_where('tblcosts', array('id' => $parent), '', 'row');
                    $parent = $ktr->costs_parent;
                    $lever++;
                }
                $data['lever'] = $lever;
            }
            $this->db->insert('tblcosts', $data);

            $id = $this->db->insert_id();
            if ($id) {
                $success = true;
                $message = _l('ch_added_successfuly');
            }
            echo json_encode(array(
                'success' => $success,
                'message' => $message
            ));
            die;
        }
    }
    public function update()
    {
        if ($this->input->post()) {
            $message = '';
            $data = $this->input->post();
            $id = $data['id'];
            unset($data['id']);
            $this->db->where('id', $id);
            $idd = $this->db->update('tblcosts', $data);

            if ($id) {
                $success = true;
                $message = _l('ch_updated_successfuly');
            }
            echo json_encode(array(
                'success' => $success,
                'message' => $message
            ));
            die;
        }
    }
    public function test_quantity()
    {
        $warehouse_id_main = $this->input->post('warehouse_id_main');
        $test_quantity = 0;
        $product = explode(',', trim($this->input->post('product_id'), ','));
        $this->db->select('count(*) as count');
        foreach ($product as $key => $v) {
            $product_id = explode('|', $v);
            $this->db->or_group_start();
            $this->db->where('id_items', $product_id[1]);
            $this->db->where('type_items', $product_id[0]);
            $this->db->where('localtion', $product_id[2]);
            $this->db->where('product_quantity >=', $product_id[3]);
            $this->db->group_end();
        }
        $this->db->where('warehouse_id', $warehouse_id_main);
        $result = $this->db->get('tblwarehouse_items')->row();
        if ($result->count == count($product)) {
            $data['success'] = true;
        } else {
            $data['success'] = false;
            foreach ($product as $key => $v) {
                $product_id = explode('|', $v);
                $this->db->select('product_quantity');
                $this->db->where('id_items', $product_id[1]);
                $this->db->where('type_items', $product_id[0]);
                $this->db->where('localtion', $product_id[2]);
                $this->db->where('warehouse_id', $warehouse_id_main);
                $data['items'][$product_id[4]] = $this->db->get('tblwarehouse_items')->row()->product_quantity;
            }
        }
        echo json_encode($data);
        die;
    }
    public function delete($id)
    {
        if (!has_permission('inventory', '', 'delete')) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_no_delete')
            ));
            die;
        }
        $get_code = get_table_where('tblinventory', array('id' => $id), '', 'row');
        $ktr_exit = get_table_where('tbladjusted', array('id_inventory' => $id));

        foreach ($ktr_exit as $key => $value) {
            if ($value['type'] == 1) {
                $test_quantity = get_table_where('tblwarehouse_product', array('import_id' => $value['id'], 'quantity_export >' => 0, 'type_export' => 3), '', 'row');
                if (!empty($test_quantity)) {
                    echo json_encode(array(
                        'alert_type' => 'danger',
                        'message' => 'Phiếu điều chỉnh ' . $value['prefix'] . '-' . $value['code'] . ' đã có xuất kho, Không thể xóa'
                    ));
                    die;
                }
            }
        }
        activity_log_v2('inventory', 'tblinventory', $id, $get_code->prefix . $get_code->code, 'Xóa phiếu kiểm kê kho [' . $get_code->prefix . $get_code->code . ']');
        $response = $this->inventory_model->delete($id);
        $alert_type = 'warning';
        $message    = _l('ch_no_delete');
        if ($response) {
            foreach ($ktr_exit as $key => $value) {
                $this->adjusted_model->delete($value['id']);
            }
            $alert_type = 'success';
            $message    = _l('ch_delete');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }

    public function inventory_data($id = '')
    {
        $data['items'] = $this->inventory_model->get($id);
        $data['dataLog'] = get_table_where('tblactivity_log_v2', array('table_obj' => 'tblinventory', 'id_obj' => $id), 'id DESC');
        $this->load->view('admin/inventory/view_modal', $data);
    }
    public function update_status($value = '')
    {
        if (!has_permission('inventory', '', 'approve')) {
            echo json_encode(array(
                'success' => false,
                'message' => _l('ch_approve_not')
            ));
            die;
        }
        if ($this->input->post()) {
            $id = $this->input->post('id');
            $status = $this->input->post('status');
            $import = get_table_where('tblinventory', array('id' => $id), '', 'row');
            // if($import->status == 1)
            // {
            //     die;
            // }
            $staff_id = get_staff_user_id();
            $date = date('Y-m-d H:i:s');
            $history_status = $import->history_status;
            $history_status .= '|' . $staff_id . ',' . $date;
            if ($status == 1) {
                $ktr_exit = get_table_where('tbladjusted', array('id_inventory' => $id), '', 'row');
                if (!empty($ktr_exit)) {
                    echo json_encode(array(
                        'success' => true,
                        'alert_type' => 'warning',
                        'message' => _l('Đã tạo điều chỉnh không thể bỏ duyệt')
                    ));
                    die;
                }
                $data = array(
                    'history_status' => '',
                    'status' => 0,
                );
            } else {
                $data = array(
                    'history_status' => $history_status,
                    'status' => ($status + 1),
                );
            }
            $this->db->where('id', $id);
            $success = $this->db->update('tblinventory', $data);
        }
        if ($success) {
            $get_code = get_table_where('tblinventory', array('id' => $id), '', 'row');
            if ($status == 1) {
                activity_log_v2('inventory', 'tblinventory', $id, $get_code->prefix . $get_code->code, 'Cập nhật trạng thái bỏ duyệt phiếu kiểm kê kho [' . $get_code->prefix . $get_code->code . ']');
                echo json_encode(array(
                    'success' => $success,
                    'alert_type' => 'success',
                    'message' => _l('Bỏ duyệt thành công')
                ));
            } else {
                activity_log_v2('inventory', 'tblinventory', $id, $get_code->prefix . $get_code->code, 'Cập nhật trạng thái phiếu kiểm kê kho [' . $get_code->prefix . $get_code->code . ']');
                echo json_encode(array(
                    'success' => $success,
                    'alert_type' => 'success',
                    'message' => _l('ch_successful_approval')
                ));
            }
        } else {
            echo json_encode(array(
                'success' => $success,
                'alert_type' => 'danger',
                'message' => _l('ch_no_successful_approval')
            ));
        }
        die;
    }
    public function print_pdf($id = '')
    {
        ob_start();
        $data = new stdClass();
        // $dataField = get_table_where('tbl_field_pdf',array('parent_field'=>'import'),'','row');
        $dataMain = get_table_where('tblinventory', array('id' => $id), '', 'row');
        $dataSub = get_table_where('tblinventory_items', array('inventory_id' => $id));
        $warehouse = get_table_where('tblwarehouse', array('id' => $dataMain->warehouse_id), '', 'row');
        $table = '';
        $img = file_get_contents(base_url('uploads/company/') . get_option('company_logo'));
        $data->img = '';
        $data->content = '';
        // $data->content .= '<span style="text-align: center;">_____________________________________________________________________________________________</span><br><br>';
        $data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;">PHIẾU KIỂM KÊ</span><br><br>';
        $data->content .= '<span style="text-align: right;font-style: italic;">' . _l('ch_code_p') . ': ' . $dataMain->prefix . '-' . $dataMain->code . '</span><br>';
        $data->content .= '<span style="text-align: right;font-style: italic;">' . _l('ch_date_p') . ': ' . _d($dataMain->date) . '</span><br><br>';
        $data->content .= '
            <span style="font-weight: bold;">' . _l('ch_staff_p') . ': </span><span>' . get_staff_full_name($dataMain->staff_id) . '</span><br>
            <span style="font-weight: bold;">' . _l('Kho hàng') . ': </span><span>' . $warehouse->name . '</span><br>
            <span style="font-weight: bold;">' . _l('ch_note_t') . ': </span><span>' . $dataMain->note . '</span><br><br>
        ';


        $table = '
            <table class="table table-bordered" border="1" width="100%">
                <thead>
                    <tr>
                        <td style="width: 6%;text-align: center;font-weight: bold;">' . _l('STT') . '</td>
        ';
        $table .= '<td style="width: 15%;text-align: center;font-weight: bold;">' . _l('Mã mặt hàng') . '</td>';
        $table .= '<td style="width: 25%;text-align: center;font-weight: bold;">' . _l('ch_items_name_t') . '</td>';
        $table .= '<td style="width: 7%;text-align: center;font-weight: bold;">' . _l('tnh_dvt') . '</td>';
        $table .= '<td style="width: 13%;text-align: center;font-weight: bold;">' . _l('warehouse_localtion') . '</td>';
        $table .= '<td style="width: 10%;text-align: center;font-weight: bold;">' . _l('item_quantity') . '</td>';
        $table .= '<td style="width: 14%;text-align: center;font-weight: bold;">' . _l('Số lượng thực') . '</td>';
        $table .= '<td style="width: 12%;text-align: center;font-weight: bold;">' . _l('Chênh lệch') . '</td>';
        $table .= '</tr>
                </thead>
                <tbody>';
        $sum_quantity = 0;
        $quantity_net = 0;
        $quantity_diff = 0;
        foreach ($dataSub as $key => $value) {
            $table .= '<tr>';
            $dataItem = $this->invoice_items_model->get_full_item($value['product_id'], $value['type']);
            $dataLocaltion = get_table_where('tbllocaltion_warehouses', array('id' => $value['localtion']), '', 'row');

            $table .= '<td style="width: 6%;text-align: center;">' . ++$key . '</td>';
            $table .= '<td style="width: 15%;text-align: left;">' . $dataItem->code  . '</td>';
            $table .= '<td style="width: 25%;text-align: left;">' . $dataItem->name . GetQuycach($value['product_id'], $value['type']) . GetThongso($value['type'], $value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use']) . '</td>';
            $table .= '<td style="width: 7%;text-align: left;">' . $dataItem->unit_name_stock . '</td>';
            $table .= '<td style="width: 13%;text-align: left;">' . $dataLocaltion->name . '</td>';
            $table .= '<td style="width: 10%;text-align: center;">' . formatNumber($value['quantity']) . '</td>';
            $table .= '<td style="width: 14%;text-align: center;">' . formatNumber($value['quantity_net']) . '</td>';
            $table .= '<td style="width: 12%;text-align: center;">' . formatNumber($value['quantity_diff']) . '</td>';
            $table .= '</tr>';
            $sum_quantity += $value['quantity'];
            $quantity_net += $value['quantity_net'];
            $quantity_diff += $value['quantity_diff'];
        }
        $table .= '<tr>
                <td colspan="5" style="text-align: center;font-weight: bold;">' . _l('invoice_dt_table_heading_amount') . '</td>';
        $table .= '<td style="text-align: center;">' . formatNumber($sum_quantity) . '</td>';
        $table .= '<td style="text-align: center;">' . formatNumber($quantity_net) . '</td>';
        $table .= '<td style="text-align: center;">' . formatNumber($quantity_diff) . '</td>';
        $table .= '</tr>';
        $table .= '</tbody>
            </table>';
        $data->content .= $table;


        $table = '<table class="table table-bordered" width="100%">
                <thead>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Người Đề Nghị</span><br>
                            <span>(ký, ghi rõ họ tên)</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Người Giao</span><br>
                            <span>(ký, ghi rõ họ tên)</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Người Nhận</span><br>
                            <span>(ký, ghi rõ họ tên)</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Thủ Kho</span><br>
                            <span>(ký, ghi rõ họ tên)</span>
                        </td>
                    </tr>
                </tbody>
            </table>';
        $data->content .= $table;
        $pdf      = print_pdf($data);
        $type     = 'I';
        $pdf->Output(slug_it('') . '.pdf', $type);
    }
    public function count_all()
    {
        if (has_permission('inventory', '', 'view_own') && !is_admin()) {
            $count = get_table_where_select('count(*) as alls', 'tblinventory', array('staff_id' => get_staff_user_id()), '', 'row');
            $status0 = get_table_where_select('count(*) as status0', 'tblinventory', array('status' => 1, 'staff_id' => get_staff_user_id()), '', 'row');
            $status1 = get_table_where_select('count(*) as status1', 'tblinventory', array('status' => 2, 'staff_id' => get_staff_user_id()), '', 'row');
        } else {
            $count = get_table_where_select('count(*) as alls', 'tblinventory', array(), '', 'row');
            $status0 = get_table_where_select('count(*) as status0', 'tblinventory', array('status' => 1), '', 'row');
            $status1 = get_table_where_select('count(*) as status1', 'tblinventory', array('status' => 2), '', 'row');
        }
        $data['all'] = $count->alls;
        $data['status0'] = $status0->status0;
        $data['status1'] = $status1->status1;

        echo json_encode($data);
    }
    function get_items($code = '', $type = '')
    {
        if ($type == 'Thành phẩm') {
            $type = 'product';
        }
        if ($type == 'Nguyên vật liệu') {
            $type = 'nvl';
        }
        if ($type == 'Công cụ - Vật tư') {
            $type = 'tools';
        }
        if ($type == 'Hàng hóa') {
            $type = 'items';
        }
        $CI = &get_instance();
        $item = array();
        $table = get_table_where('tbltype_items', array('type' => $type), '', 'row')->table;
        if ($type == 'items') {
            $CI->db->select('tblitems.*,tblunits.unit as unit_name,tblitems.price as price');
            $CI->db->from('tblitems');
            $CI->db->join('tblunits', 'tblunits.unitid=tblitems.unit', 'left');
            $CI->db->where('tblitems.code', $code);
            $item = $CI->db->get()->row();
        } elseif ($type == 'tools') {
            $CI->db->select('tbl_tools_supplies.*,tblunits.unit as unit_name,tbl_tools_supplies.price_sell  as price');
            $CI->db->from('tbl_tools_supplies');
            $CI->db->join('tblunits', 'tblunits.unitid=tbl_tools_supplies.unit_id', 'left');
            $CI->db->where('tbl_tools_supplies.code', $code);
            $item = $CI->db->get()->row();
        } else {
            $CI->db->select($table . '.*,tblunits.unit as unit_name,' . $table . '.price_sell  as price');
            $CI->db->from($table);
            $CI->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
            $CI->db->where($table . '.code', $code);
            $item = $CI->db->get()->row();
        }

        return $item;
    }
    public function import_items()
    {
        ob_end_clean();
        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $this->load->helper('security');
        $data = $this->input->post();
        $CountAdd = 0;
        $CountUpdate = 0;
        $CountDelete = 0;
        $CountAll = 0;
        if (!empty($_FILES['file'])) {
            $fullfile = $_FILES['file']['tmp_name'];
            $extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if ($extension != 'XLSX' && $extension != 'XLS' && $extension != 'CSV') {
                echo json_encode(['success' => false, 'alert_type' => 'success', 'message' => _l('cong_not_type')]);
                die();
            }

            $inputFileType = PHPExcel_IOFactory::identify($fullfile);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load("$fullfile");
            $total_sheets = $objPHPExcel->getSheetCount();
            $allSheetName = $objPHPExcel->getSheetNames();
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow = $objWorksheet->getHighestRow();
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('O');

            $row_start = 2; // read start
            $row_end = $highestRow; // read end

            $list_data = [];
            $dem = 2;
            for ($row = $row_start; $row <= $row_end; ++$row) {
                $list_data[$row]['count'] = $dem;
                $list_data[$row]['code_items'] = $objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
                $list_data[$row]['name_items'] = $objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
                $list_data[$row]['type'] = $objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
                $list_data[$row]['unit'] = $objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
                $list_data[$row]['localtion'] = $objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
                $list_data[$row]['quantity'] =  number_unformat($objWorksheet->getCellByColumnAndRow(5, $row)->getValue());
                $list_data[$row]['lot_code'] = $objWorksheet->getCellByColumnAndRow(6, $row)->getValue();
                $list_data[$row]['date_sx'] = $objWorksheet->getCellByColumnAndRow(7, $row)->getValue();
                $list_data[$row]['date_sd'] = $objWorksheet->getCellByColumnAndRow(8, $row)->getValue();
                $dem++;
            }
            $i = 0;
            $list_data_orr = [];
            foreach ($list_data as $key => $value) {
                if ($value['quantity'] === '') {
                    $list_data_orr[$i] = $value;
                    $list_data_orr[$i]['title'] = 'Số lượng không được để rổng';
                    $i++;
                    unset($list_data[$key]);
                    continue;
                }
                if (empty($value['code_items'])) {
                    $list_data_orr[$i] = $value;
                    $list_data_orr[$i]['title'] = 'Mã mặt hàng không được để rổng';
                    $i++;
                    unset($list_data[$key]);
                    continue;
                }
                if (empty($value['localtion'])) {
                    $list_data_orr[$i] = $value;
                    $list_data_orr[$i]['title'] = 'Vị trí không được để rổng';
                    $i++;
                    unset($list_data[$key]);
                    continue;
                }

                $ktr = $this->get_items($value['code_items'], $value['type']);
                if ($value['type'] == 'Thành phẩm') {
                    $list_data[$key]['type'] = 'product';
                }
                if ($value['type'] == 'Nguyên vật liệu') {
                    $list_data[$key]['type'] = 'nvl';
                }
                if ($value['type'] == 'Công cụ - Vật tư') {
                    $list_data[$key]['type'] = 'tools';
                }
                if ($value['type'] == 'Hàng hóa') {
                    $list_data[$key]['type'] = 'items';
                }
                if (!empty($ktr)) {
                    $list_data[$key]['id_items'] =  $ktr->id;
                } else {
                    $list_data_orr[$i] = $value;
                    $list_data_orr[$i]['title'] = 'Không tồn tại mặt hàng trong phầm mềm';
                    $i++;
                    unset($list_data[$key]);
                    continue;
                }
                if ($list_data[$key]['type'] == 'product') {
                    if ($ktr->is_no_stock == 1) {
                        $list_data_orr[$i] = $value;
                        $list_data_orr[$i]['title'] = 'Mặt hàng không sản xuất tồn, Không thể kiểm kê';
                        $i++;
                        unset($list_data[$key]);
                        continue;
                    }
                }
                $localtion = get_table_where('tbllocaltion_warehouses', array('warehouse' => $data['warehouse_id'], 'code' => $value['localtion']), '', 'row');
                if (!empty($localtion)) {
                    if ($localtion->child == 0) {
                        $list_data_orr[] = $value;
                        unset($list_data[$key]);
                        continue;
                    } else {
                        $list_data[$key]['id_localtion'] =  $localtion->id;
                    }
                } else {
                    $list_data_orr[$i] = $value;
                    $list_data_orr[$i]['title'] = 'Không tồn tại vị trí trong phầm mềm';
                    $i++;
                    unset($list_data[$key]);
                    continue;
                }
                if (!empty($value['date_sx'])) {
                    if (!to_sql_date($value['date_sx'])) {
                        $list_data_orr[$i] = $value;
                        $list_data_orr[$i]['title'] = 'Ngày sản xuất không đúng định dạng và phải là dạng chuỗi';
                        $i++;
                        unset($list_data[$key]);
                        continue;
                    }
                }
                if (!empty($value['date_sd'])) {
                    if (!to_sql_date($value['date_sd'])) {
                        $list_data_orr[$i] = $value;
                        $list_data_orr[$i]['title'] = 'Ngày sử dụng không đúng định dạng và phải là dạng chuỗi';
                        $i++;
                        unset($list_data[$key]);
                        continue;
                    }
                }
                if (!empty($value['date_sx'])) {
                    if (empty($value['date_sd'])) {
                        $list_data_orr[$i] = $value;
                        $list_data_orr[$i]['title'] = 'Ngày sử dụng không được để rổng';
                        $i++;
                        unset($list_data[$key]);
                        continue;
                    }
                }
                if (!empty($value['date_sd'])) {
                    if (empty($value['date_sx'])) {
                        $list_data_orr[$i] = $value;
                        $list_data_orr[$i]['title'] = 'Ngày sản xuất không được để rổng';
                        $i++;
                        unset($list_data[$key]);
                        continue;
                    }
                }
                $date_sx = NULL;
                $date_sd = NULL;
                $date_use = NULL;
                $lot_code = NULL;
                if (!empty($value['lot_code'])) {
                    $lot_code = $value['lot_code'];
                }
                if (!empty($value['date_sx']) && !empty($value['date_sx'])) {
                    $date_sx = to_sql_date($value['date_sx']);
                    $date_sd = to_sql_date($value['date_sd']);
                    if ($date_sx > $date_sd) {
                        $list_data_orr[$i] = $value;
                        $list_data_orr[$i]['title'] = 'Ngày sản xuất không được lớn hơn ngày sử dụng';
                        $i++;
                        unset($list_data[$key]);
                        continue;
                    }
                    $datediff = abs(strtotime($date_sd) - strtotime($date_sx));
                    $date_use = $datediff / (60 * 60 * 24);
                    $date_sx = ($value['date_sx']);
                    $date_sd = ($value['date_sd']);
                }
                $list_data[$key]['date_sx'] = $date_sx;
                $list_data[$key]['date_sd'] = $date_sd;
                $list_data[$key]['date_use'] = $date_use;
                $list_data[$key]['lot_code'] = $lot_code;
            }
            echo json_encode([
                'success' => true,
                'alert_type' => 'success',
                'list_data' => $list_data,
                'list_data_orr' => $list_data_orr,
            ]);
            die();
        }
    }
    public function SearchItems_new($id = '', $tyle_chose = '')
    {
        $data = [];
        $search = $this->input->get('term');
        $type = $this->input->get('type');
        if (empty($type)) {
            $type = $tyle_chose;
        }
        $limit_one = 12;
        $limit_two = 12;
        $limit_three = 12;
        $limit_all = 50;

        if ($type == -1) {
            $this->db->select(
                '
                    id,
                    "" as mode,
                    tblitems.name as text,
                    tblitems.code as code,
                    tblitems.price,
                    concat("items") as type,
                    tblitems.avatar as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tblitems.name', $search);
                $this->db->or_like('tblitems.code', $search);
                $this->db->group_end();
            }
            $this->db->order_by('name', 'DESC');
            $this->db->limit($limit_one);
            $items = $this->db->get('tblitems')->result_array();
            if (!empty($items)) {
                $data['results'][] =
                    [
                        'text' => _l('Sản phẩm'),
                        'children' => $items
                    ];
            }
            $count_items = count($items);
            $this->db->select(
                '
                id as id,
                mode,
                tbl_products.code as code,
                tbl_products.name as text,
                tbl_products.price_sell as price,
                concat("product") as type,
                type_products as type_v1,
                CONCAT("uploads/products/", "", tbl_products.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_products.name', $search);
                $this->db->or_like('tbl_products.code', $search);
                $this->db->group_end();
            }
            $this->db->where('tbl_products.type_products !=', 'semi_products');
            $this->db->where('tbl_products.is_no_stock', 0);
            $this->db->order_by('tbl_products.name', 'DESC');
            $this->db->limit($limit_two);
            // $this->db->limit(($limit_all - $count_product));
            $product = $this->db->get('tbl_products')->result_array();
            if (!empty($product)) {
                $data['results'][] =
                    [
                        'text' => _l('Thành phẩm'),
                        'children' => $product
                    ];
            }

            $count_product = count($product);
            $this->db->select(
                '
                id as id,
                mode,
                tbl_products.code as code,
                tbl_products.name as text,
                tbl_products.price_sell as price,
                concat("product") as type,
                type_products as type_v1,
                CONCAT("uploads/products/", "", tbl_products.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_products.name', $search);
                $this->db->or_like('tbl_products.code', $search);
                $this->db->group_end();
            }
            $this->db->where('tbl_products.is_no_stock', 0);
            $this->db->where('tbl_products.type_products', 'semi_products');
            $this->db->order_by('tbl_products.name', 'DESC');
            $this->db->limit($limit_two);
            // $this->db->limit(($limit_all - $count_product));
            $products = $this->db->get('tbl_products')->result_array();
            if (!empty($products)) {
                $data['results'][] =
                    [
                        'text' => _l('Bán thành phẩm(SX)'),
                        'children' => $products
                    ];
            }

            $count_product = count($product);
            $this->db->select(
                '
                id as id,
                "" as mode,
                tbl_tools_supplies.code as code,
                tbl_tools_supplies.name as text,
                tbl_tools_supplies.price_sell as price,
                concat("tools") as type,
                CONCAT("uploads/tools_supplies/", "", tbl_tools_supplies.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_tools_supplies.name', $search);
                $this->db->or_like('tbl_tools_supplies.code', $search);
                $this->db->group_end();
            }
            $this->db->order_by('tbl_tools_supplies.name', 'DESC');
            $this->db->limit($limit_three);
            $tools = $this->db->get('tbl_tools_supplies')->result_array();
            if (!empty($tools)) {
                $data['results'][] =
                    [
                        'text' => _l('Công cụ - Vật tư'),
                        'children' => $tools
                    ];
            }

            $count_tools = count($tools);
            $this->db->select(
                '
                id as id,
                mode,
                tbl_materials.code as code,
                tbl_materials.name as text,
                tbl_materials.price_sell as price,
                concat("nvl") as type,
                CONCAT("uploads/materials/", "", tbl_materials.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_materials.name', $search);
                $this->db->or_like('tbl_materials.code', $search);
                $this->db->group_end();
            }
            $this->db->order_by('tbl_materials.name', 'DESC');
            $this->db->limit(($limit_all - $count_product - $count_tools - $count_items));
            $product = $this->db->get('tbl_materials')->result_array();
            if (!empty($product)) {
                $data['results'][] =
                    [
                        'text' => _l('Nguyên vật liệu'),
                        'children' => $product
                    ];
            }
        } else
        if ($type == 'items') {
            $this->db->select(
                '
                    id as id,
                    "" as mode,
                    tblitems.code as code,
                    tblitems.name as text,
                    tblitems.price,
                    concat("items") as type,
                    tblitems.avatar as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tblitems.name', $search);
                $this->db->or_like('tblitems.code', $search);
                $this->db->group_end();
            } else {
                if ($id > 0) {
                    $this->db->group_start();
                    $this->db->where('tblitems.id', $id);
                    $this->db->group_end();
                }
            }
            $this->db->order_by('name', 'DESC');
            $this->db->limit(50);
            $items = $this->db->get('tblitems')->result_array();
            if (!empty($items)) {
                $data['results'][] =
                    [
                        'text' => _l('Sản phẩm'),
                        'children' => $items
                    ];
            }
        } else
        if ($type == 'product') {
            $this->db->select(
                '
                id as id,
                mode,
                tbl_products.code as code,
                tbl_products.name as text,
                tbl_products.price_sell as price,
                concat("product") as type,
                type_products as type_v1,
                CONCAT("uploads/products/", "", tbl_products.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_products.name', $search);
                $this->db->or_like('tbl_products.code', $search);
                $this->db->group_end();
            } else {
                if ($id > 0) {
                    $this->db->group_start();
                    $this->db->where('tbl_products.id', $id);
                    $this->db->group_end();
                }
            }
            $this->db->where('tbl_products.is_no_stock', 0);
            $this->db->where('tbl_products.type_products !=', 'semi_products');
            $this->db->order_by('tbl_products.name', 'DESC');
            $this->db->limit(50);
            // $this->db->limit(($limit_all - $count_product));
            $product = $this->db->get('tbl_products')->result_array();
            if (!empty($product)) {
                $data['results'][] =
                    [
                        'text' => _l('Thành phẩm'),
                        'children' => $product
                    ];
            }
        } else
        if ($type == 'semi_products') {
            $this->db->select(
                '
                id as id,
                mode,
                tbl_products.code as code,
                tbl_products.name as text,
                tbl_products.price_sell as price,
                concat("product") as type,
                type_products as type_v1,
                CONCAT("uploads/products/", "", tbl_products.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_products.name', $search);
                $this->db->or_like('tbl_products.code', $search);
                $this->db->group_end();
            } else {
                if ($id > 0) {
                    $this->db->group_start();
                    $this->db->where('tbl_products.id', $id);
                    $this->db->group_end();
                }
            }
            $this->db->where('tbl_products.is_no_stock', 0);
            $this->db->where('tbl_products.type_products', 'semi_products');
            $this->db->order_by('tbl_products.name', 'DESC');
            $this->db->limit(50);
            // $this->db->limit(($limit_all - $count_product));
            $product = $this->db->get('tbl_products')->result_array();
            if (!empty($product)) {
                $data['results'][] =
                    [
                        'text' => _l('Bán thành phẩm(SX)'),
                        'children' => $product
                    ];
            }
        } elseif ($type == 'nvl') {
            $this->db->select(
                '
                id as id,
                mode,
                tbl_materials.code as code,
                tbl_materials.name as text,
                tbl_materials.price_sell as price,
                concat("nvl") as type,
                CONCAT("uploads/materials/", "", tbl_materials.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_materials.name', $search);
                $this->db->or_like('tbl_materials.code', $search);
                $this->db->group_end();
            } else {
                if ($id > 0) {
                    $this->db->group_start();
                    $this->db->where('tbl_materials.id', $id);
                    $this->db->group_end();
                }
            }
            $this->db->order_by('tbl_materials.name', 'DESC');
            $this->db->limit(50);
            $product = $this->db->get('tbl_materials')->result_array();
            if (!empty($product)) {
                $data['results'][] =
                    [
                        'text' => _l('Nguyên vật liệu'),
                        'children' => $product
                    ];
            }
        } elseif ($type == 'tools') {
            $this->db->select(
                '
                id as id,
                "" as mode,
                tbl_tools_supplies.code as code,
                tbl_tools_supplies.name as text,
                tbl_tools_supplies.price_sell as price,
                concat("tools") as type,
                CONCAT("uploads/tools_supplies/", "", tbl_tools_supplies.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_tools_supplies.name', $search);
                $this->db->or_like('tbl_tools_supplies.code', $search);
                $this->db->group_end();
            } else {
                if ($id > 0) {
                    $this->db->group_start();
                    $this->db->where('tbl_tools_supplies.id', $id);
                    $this->db->group_end();
                }
            }
            $this->db->order_by('tbl_tools_supplies.name', 'DESC');
            $this->db->limit(50);
            $tools = $this->db->get('tbl_tools_supplies')->result_array();
            if (!empty($tools)) {
                $data['results'][] =
                    [
                        'text' => _l('Công cụ - Vật tư'),
                        'children' => $tools
                    ];
            }
        }
        echo json_encode($data);
        die();
    }
    public function SearchItems_hs($id = '', $tyle_chose = '')
    {
        $data = [];
        $search = $this->input->get('term');
        $type = $this->input->get('types');

        $limit_one = 12;
        $limit_two = 12;
        $limit_three = 12;
        $limit_all = 50;

        if ($type == -1) {
            $this->db->select(
                '
                    id,
                    "" as mode,
                    tblitems.name as text,
                    tblitems.code as code,
                    tblitems.price,
                    concat("items") as type,
                    tblitems.avatar as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tblitems.name', $search);
                $this->db->or_like('tblitems.code', $search);
                $this->db->group_end();
            }
            $this->db->order_by('name', 'DESC');
            $this->db->limit($limit_one);
            $items = $this->db->get('tblitems')->result_array();
            if (!empty($items)) {
                $data['results'][] =
                    [
                        'text' => _l('Sản phẩm'),
                        'children' => $items
                    ];
            }
            $count_items = count($items);
            $this->db->select(
                '
                id as id,
                mode,
                tbl_products.code as code,
                tbl_products.name as text,
                tbl_products.price_sell as price,
                concat("product") as type,
                type_products as type_v1,
                CONCAT("uploads/products/", "", tbl_products.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_products.name', $search);
                $this->db->or_like('tbl_products.code', $search);
                $this->db->group_end();
            }
            $this->db->where('tbl_products.type_products !=', 'semi_products');
            $this->db->where('tbl_products.is_no_stock', 0);
            $this->db->order_by('tbl_products.name', 'DESC');
            $this->db->limit($limit_two);
            // $this->db->limit(($limit_all - $count_product));
            $product = $this->db->get('tbl_products')->result_array();
            if (!empty($product)) {
                $data['results'][] =
                    [
                        'text' => _l('Thành phẩm'),
                        'children' => $product
                    ];
            }

            $count_product = count($product);
            $this->db->select(
                '
                id as id,
                mode,
                tbl_products.code as code,
                tbl_products.name as text,
                tbl_products.price_sell as price,
                concat("product") as type,
                type_products as type_v1,
                CONCAT("uploads/products/", "", tbl_products.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_products.name', $search);
                $this->db->or_like('tbl_products.code', $search);
                $this->db->group_end();
            }
            $this->db->where('tbl_products.is_no_stock', 0);
            $this->db->where('tbl_products.type_products', 'semi_products');
            $this->db->order_by('tbl_products.name', 'DESC');
            $this->db->limit($limit_two);
            // $this->db->limit(($limit_all - $count_product));
            $products = $this->db->get('tbl_products')->result_array();
            if (!empty($products)) {
                $data['results'][] =
                    [
                        'text' => _l('Bán thành phẩm(SX)'),
                        'children' => $products
                    ];
            }

            $count_product = count($product);
            $this->db->select(
                '
                id as id,
                "" as mode,
                tbl_tools_supplies.code as code,
                tbl_tools_supplies.name as text,
                tbl_tools_supplies.price_sell as price,
                concat("tools") as type,
                CONCAT("uploads/tools_supplies/", "", tbl_tools_supplies.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_tools_supplies.name', $search);
                $this->db->or_like('tbl_tools_supplies.code', $search);
                $this->db->group_end();
            }
            $this->db->order_by('tbl_tools_supplies.name', 'DESC');
            $this->db->limit($limit_three);
            $tools = $this->db->get('tbl_tools_supplies')->result_array();
            if (!empty($tools)) {
                $data['results'][] =
                    [
                        'text' => _l('Công cụ - Vật tư'),
                        'children' => $tools
                    ];
            }

            $count_tools = count($tools);
            $this->db->select(
                '
                id as id,
                mode,
                tbl_materials.code as code,
                tbl_materials.name as text,
                tbl_materials.price_sell as price,
                concat("nvl") as type,
                CONCAT("uploads/materials/", "", tbl_materials.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_materials.name', $search);
                $this->db->or_like('tbl_materials.code', $search);
                $this->db->group_end();
            }
            $this->db->order_by('tbl_materials.name', 'DESC');
            $this->db->limit(($limit_all - $count_product - $count_tools - $count_items));
            $product = $this->db->get('tbl_materials')->result_array();
            if (!empty($product)) {
                $data['results'][] =
                    [
                        'text' => _l('Nguyên vật liệu'),
                        'children' => $product
                    ];
            }
        } else
        if ($type == 'items') {
            $this->db->select(
                '
                    id as id,
                    "" as mode,
                    tblitems.code as code,
                    tblitems.name as text,
                    tblitems.price,
                    concat("items") as type,
                    tblitems.avatar as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tblitems.name', $search);
                $this->db->or_like('tblitems.code', $search);
                $this->db->group_end();
            } else {
                if ($id > 0) {
                    $this->db->group_start();
                    $this->db->where('tblitems.id', $id);
                    $this->db->group_end();
                }
            }
            $this->db->order_by('name', 'DESC');
            $this->db->limit(50);
            $items = $this->db->get('tblitems')->result_array();
            if (!empty($items)) {
                $data['results'][] =
                    [
                        'text' => _l('Sản phẩm'),
                        'children' => $items
                    ];
            }
        } else
        if ($type == 'product') {
            $this->db->select(
                '
                id as id,
                mode,
                tbl_products.code as code,
                tbl_products.name as text,
                tbl_products.price_sell as price,
                concat("product") as type,
                type_products as type_v1,
                CONCAT("uploads/products/", "", tbl_products.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_products.name', $search);
                $this->db->or_like('tbl_products.code', $search);
                $this->db->group_end();
            } else {
                if ($id > 0) {
                    $this->db->group_start();
                    $this->db->where('tbl_products.id', $id);
                    $this->db->group_end();
                }
            }
            $this->db->where('tbl_products.is_no_stock', 0);
            $this->db->where('tbl_products.type_products !=', 'semi_products');
            $this->db->order_by('tbl_products.name', 'DESC');
            $this->db->limit(50);
            // $this->db->limit(($limit_all - $count_product));
            $product = $this->db->get('tbl_products')->result_array();
            if (!empty($product)) {
                $data['results'][] =
                    [
                        'text' => _l('Thành phẩm'),
                        'children' => $product
                    ];
            }
        } else
        if ($type == 'semi_products') {
            $this->db->select(
                '
                id as id,
                mode,
                tbl_products.code as code,
                tbl_products.name as text,
                tbl_products.price_sell as price,
                concat("product") as type,
                type_products as type_v1,
                CONCAT("uploads/products/", "", tbl_products.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_products.name', $search);
                $this->db->or_like('tbl_products.code', $search);
                $this->db->group_end();
            } else {
                if ($id > 0) {
                    $this->db->group_start();
                    $this->db->where('tbl_products.id', $id);
                    $this->db->group_end();
                }
            }
            $this->db->where('tbl_products.is_no_stock', 0);
            $this->db->where('tbl_products.type_products', 'semi_products');
            $this->db->order_by('tbl_products.name', 'DESC');
            $this->db->limit(50);
            // $this->db->limit(($limit_all - $count_product));
            $product = $this->db->get('tbl_products')->result_array();
            if (!empty($product)) {
                $data['results'][] =
                    [
                        'text' => _l('Bán thành phẩm(SX)'),
                        'children' => $product
                    ];
            }
        } elseif ($type == 'nvl') {
            $this->db->select(
                '
                id as id,
                mode,
                tbl_materials.code as code,
                tbl_materials.name as text,
                tbl_materials.price_sell as price,
                concat("nvl") as type,
                CONCAT("uploads/materials/", "", tbl_materials.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_materials.name', $search);
                $this->db->or_like('tbl_materials.code', $search);
                $this->db->group_end();
            } else {
                if ($id > 0) {
                    $this->db->group_start();
                    $this->db->where('tbl_materials.id', $id);
                    $this->db->group_end();
                }
            }
            $this->db->order_by('tbl_materials.name', 'DESC');
            $this->db->limit(50);
            $product = $this->db->get('tbl_materials')->result_array();
            if (!empty($product)) {
                $data['results'][] =
                    [
                        'text' => _l('Nguyên vật liệu'),
                        'children' => $product
                    ];
            }
        } elseif ($type == 'tools') {
            $this->db->select(
                '
                id as id,
                "" as mode,
                tbl_tools_supplies.code as code,
                tbl_tools_supplies.name as text,
                tbl_tools_supplies.price_sell as price,
                concat("tools") as type,
                CONCAT("uploads/tools_supplies/", "", tbl_tools_supplies.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_tools_supplies.name', $search);
                $this->db->or_like('tbl_tools_supplies.code', $search);
                $this->db->group_end();
            } else {
                if ($id > 0) {
                    $this->db->group_start();
                    $this->db->where('tbl_tools_supplies.id', $id);
                    $this->db->group_end();
                }
            }
            $this->db->order_by('tbl_tools_supplies.name', 'DESC');
            $this->db->limit(50);
            $tools = $this->db->get('tbl_tools_supplies')->result_array();
            if (!empty($tools)) {
                $data['results'][] =
                    [
                        'text' => _l('Công cụ - Vật tư'),
                        'children' => $tools
                    ];
            }
        }
        echo json_encode($data);
        die();
    }

    public function export_excel()
    {
        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
        $objPHPExcel->getDefaultStyle()->applyFromArray([
            'font' => array(
                'name'  => 'Times New Roman'
            ),
        ]);

        $this->db->select('
            CONCAT(tblinventory.prefix, tblinventory.code) as code,
            tblinventory.date as date,
            CONCAT(tblpay_slip.prefix, "-", tblpay_slip.code) as pay_slip_code,
            tblpay_slip.date as pay_slip_date,
            CONCAT(tblimport.prefix, "-", tblimport.code) as import_code,
            CONCAT(tblpurchase_order.prefix, "-", tblpurchase_order.code) as purchase_order_code,
            tblimport.date as import_date,
            tblpurchase_order.date as purchase_order_date,
            tblsuppliers.code as supplier_code,
            tblsuppliers.company as supplier_name,
            tblpayment_modes.name as payment_method,
            CONCAT(tblpurchases.prefix, tblpurchases.code) as purchase_code,
            tblpurchases.name_purchase as purchase_name,
            tblpurchases.date as purchase_date,
            tblpurchases.delivery_date as delivery_date,
            tb_purchase_supplier.company as purchase_supplier_name,
            tblpurchases_items.product_id as item_id,
            tblpurchases_items.type as item_type,
            tbl_internal_proposal_purchase_items.quantity_payment as purchase_item_quantity,
            tbl_internal_proposal_purchase_items.price as purchase_item_price,
            (tbl_internal_proposal_purchase_items.quantity_payment * tbl_internal_proposal_purchase_items.price) as purchase_item_total,
            tblpurchase_order_items.tax_rate as purchase_order_item_tax_rate,
            (tblpurchase_order_items.quantity_payment * tblpurchase_order_items.price_suppliers * (tblpurchase_order_items.tax_rate/100)) as purchase_order_item_tax_total,
            tblpurchase_order_items.total_suppliers as purchase_order_item_total,
            tblimport.status_qc as import_qc,
            tblimport.warehouseman_id as import_warehouse_confirm,
            tblpay_slip.staff_id as pay_slip_staff_create,
            tblwarehouse.name as warehouse,
            tbllocaltion_warehouses.name as warehouse_location,
            tblinventory_items.quantity as quantity,
            tblinventory_items.quantity_net as actual_quantity,
            tblinventory_items.quantity_diff as difference_quantity,
        ');
        $this->db->from('tblinventory_items');
        $search_date = $this->input->post('search_date');
        if ($search_date) {
            $data_start = explode(' - ', $search_date);
            $this->db->where('tblinventory.date >=' , to_sql_date($data_start[0]).' 00:00:00');
            $this->db->where('tblinventory.date <=' , to_sql_date($data_start[1]).' 23:59:59');
        }
        $this->db->join('tblinventory', 'tblinventory.id = tblinventory_items.inventory_id', 'inner');
        $this->db->join('tblimport_items', 'tblimport_items.product_id = tblinventory_items.product_id AND tblimport_items.type = tblinventory_items.type AND tblimport_items.lot_code = tblinventory_items.lot_code AND tblimport_items.localtion_warehouses_id = tblinventory_items.localtion', 'left');
        $this->db->join('tblimport', 'tblimport.id = tblimport_items.id_import', 'left');
        $this->db->join('tblpurchase_order_items', 'tblpurchase_order_items.id = tblimport_items.id_purchase_order_items', 'left');
        $this->db->join('tblpurchase_order', 'tblpurchase_order.id = tblpurchase_order_items.id_purchase_order', 'left');
        $this->db->join('tblsuppliers', 'tblsuppliers.id = tblpurchase_order.suppliers_id', 'left');
        $this->db->join('tblpay_slip_detail', 'tblpay_slip_detail.id_old = tblpurchase_order_items.id_purchase_order', 'left');
        $this->db->join('tblpay_slip', 'tblpay_slip.id = tblpay_slip_detail.id_pay_slip', 'left');
        $this->db->join('tblpayment_modes', 'tblpayment_modes.id = tblpay_slip.payment_mode', 'left');
        $this->db->join('tblpurchases_items', 'tblpurchases_items.id = tblpurchase_order_items.purchase_items_id', 'left');
        $this->db->join('tblpurchases', 'tblpurchases.id = tblpurchases_items.purchases_id', 'left');
        $this->db->join('tbl_internal_proposal_purchase_items', 'tbl_internal_proposal_purchase_items.id_purchases = tblpurchases.id', 'left');
        $this->db->join('tblsuppliers tb_purchase_supplier', 'tb_purchase_supplier.id = tbl_internal_proposal_purchase_items.suppliers_id', 'left');
        $this->db->join('tblwarehouse', 'tblwarehouse.id = tblinventory_items.warehouse_id', 'left');
        $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblinventory_items.localtion', 'left');
        $this->db->group_by('tblinventory_items.id');
        $this->db->order_by('tblinventory.id', 'desc');
        $this->db->order_by('tblinventory_items.type', 'asc');
        $this->db->order_by('tblinventory_items.product_id', 'asc');
        $this->db->order_by('tblimport_items.id', 'desc');
        $this->db->order_by('tblpurchase_order_items.id', 'desc');
        $result = $this->db->get()->result_array();
        $styleTitle = [
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font' => array(
                'bold' => true,
                'size' => 18,
                'name' => 'Times New Roman'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ];

        $styleHeader = [
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font' => array(
                // 'bold' => true,
                // 'color' => array('rgb' => '111112'),
                'size' => 12,
                'name' => 'Times New Roman'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '4BACC6'),
                'size' => 12,
                // 'bold' => true
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ];

        $stylePlain = [
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font' => array(
                // 'bold' => false,
                // 'color' => array('rgb' => '111112'),
                'size' => 11,
                'name' => 'Times New Roman'
            ),
        ];

        $headerFillColor = [
            'A' => array('rgb' => '4BACC6'),
            'D' => array('rgb' => '00B0F0'),
            'F' => array('rgb' => 'B8CCE4'),
            'I' => array('rgb' => 'FABF8F'),
            'O' => array('rgb' => 'FFFF00'),
            'AH' => array('rgb' => 'FABF8F'),
            'AK' => array('rgb' => 'B8CCE4'),
            'AM' => array('rgb' => '00B0F0'),
            'AQ' => array('rgb' => '4BACC6'),
        ];

        $cloumns_excel = cloumns_excel();
        $colName = [
            'code' => 'Mã Phiếu Kiểm Kê',
            'name' => 'Tên Phiếu Kiểm Kê', // Chưa có
            'date' => 'Ngày Lập Kiểm Kê',
            'pay_slip_code' => 'Mã Phiếu Chi Mua Hàng',
            'pay_slip_date' => 'Ngày Lập PCMH',
            'import_code' => 'Mã Nhập Kho',
            'purchase_order_code' => 'Mã Tham Chiếu',
            'import_date' => 'Ngày Nhập Kho',
            'purchase_order_code-2' => 'PO-Đơn Mua Hàng',
            'purchase_order_date' => 'Ngày Lập PO',
            'supplier_code' => 'Mã NCC',
            'supplier_name' => 'Nhà Cung Cấp',
            'payment_date' => 'Thời Hạn Thanh Toán', // Chưa có
            'payment_method' => 'Phương Pháp Thanh Toán',
            'purchase_code' => 'Mã YCMH',
            'purchase_name' => 'Tên Mã YCMH',
            'purchase_date' => 'Ngày Lập YCMH',
            'delivery_date' => 'Ngày Về NPL',
            'purchase_supplier_name' => 'Loại Nhà Cung Cấp',
            'purchase_item_category' => 'Nhóm NPL',
            'purchase_item_specie' => 'Chủng Loại NPL',
            'purchase_item_code' => 'Mã NPL',
            'purchase_item_name' => 'Tên NPL',
            'purchase_item_name_mode' => 'Quy Cách',
            'purchase_item_unit' => 'Đơn Vị Chuẩn',
            'purchase_item_unit_stock' => 'Đơn Vị Vào Kho',
            'purchase_item_unit_payment' => 'Đơn Vị Thanh Toán',
            'purchase_item_quantity' => 'Số Lượng',
            'purchase_item_price' => 'Giá Nhập',
            'purchase_item_total' => 'Tổng Tiền',
            'purchase_item_packaging_standard' => 'Tiêu Chuẩn Đóng Gói', // Trống
            'purchase_item_time_stock' => 'Thời Gian Lưu Kho',
            'purchase_item_quantity_minimum' => 'Tồn Cho Phép',
            'purchase_order_item_tax_rate' => '% Thuế',
            'purchase_order_item_tax_total' => 'Tổng Tiền Thuế',
            'purchase_order_item_total' => 'Thành Tiền',
            'import_qc' => 'QC',
            'import_warehouse_confirm' => 'Duyệt Kho',
            'pay_slip_staff_create' => 'Người Đề Xuất',
            'pay_slip_staff_create-2' => 'Người Đề Xuất Duyệt', // Chưa có
            'pay_slip_staff_create-3' => 'Trưởng Phòng Duyệt', // Chưa có
            'pay_slip_staff_create-4' => 'Thủ Quỹ Hoàn Thành', // Chưa có
            'warehouse' => 'Kho Hàng',
            'warehouse_location' => 'Vị Trí Kho',
            'quantity' => 'Số Lượng',
            'actual_quantity' => 'Số Lượng Thực',
            'difference_quantity' => 'Chênh Lệch',
        ];
        $aColumns = array_keys($colName);

        $excelRowNum = 1;
        $maxCol = count($colName) - 1;
        $objPHPExcel->getActiveSheet()->mergeCells('A' . ($excelRowNum) . ':' . $cloumns_excel[$maxCol] . $excelRowNum);
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $excelRowNum, ('PHIẾU KIỂM KÊ'))->getStyle("A" . $excelRowNum)->applyFromArray($styleTitle);
        // $objPHPExcel->getActiveSheet()->freezePane('A1');

        $excelRowNum = 2;
        foreach ($aColumns as $key => $value) {
            foreach ($headerFillColor as $colIndex => $color) {
                if ($cloumns_excel[$key] == $colIndex) {
                    $styleHeader['fill']['color'] = $color;
                    unset($headerFillColor[$colIndex]);
                    break;
                }
            }
            $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$key] . $excelRowNum, ($colName[$value]))->getStyle($cloumns_excel[$key] . ($excelRowNum))->applyFromArray($styleHeader);
            $objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$key])->setAutoSize(true);
        }

        $excelRowNum = 3;
        foreach ($result as $key => $aRow) {
            if (!empty($aRow['purchase_order_code'])) {
                $aRow['purchase_order_code-2'] = $aRow['purchase_order_code'];
            }
            if (!empty($aRow['item_id']) && !empty($aRow['item_type'])) {
                $getItem = get_full_item_new($aRow['item_id'], $aRow['item_type']);
                if (!empty($getItem)) {
                    $aRow['purchase_item_category'] = $getItem->name_category;
                    $aRow['purchase_item_specie'] = $getItem->name_species;
                    $aRow['purchase_item_code'] = $getItem->code;
                    $aRow['purchase_item_name'] = $getItem->name;
                    $aRow['purchase_item_name_mode'] = $getItem->name_mode;
                    $aRow['purchase_item_unit'] = $getItem->unit_name;
                    $aRow['purchase_item_unit_stock'] = $getItem->unit_name_stock;
                    $aRow['purchase_item_unit_payment'] = $getItem->unit_name_payment;
                    $aRow['purchase_item_time_stock'] = $getItem->time_stock;
                    $aRow['purchase_item_quantity_minimum'] = $getItem->quantity_minimum;
                }
            }

            if (isset($aRow['pay_slip_staff_create'])) {
                $aRow['pay_slip_staff_create'] = get_staff_full_name($aRow['pay_slip_staff_create']);
            }

            if (isset($aRow['import_qc'])) {
                if (!empty($aRow['import_qc'])) {
                    $aRow['import_qc'] = 'Đã kiểm tra';
                } else {
                    $aRow['import_qc'] = 'Chưa kiểm tra';
                }
            }

            if (isset($aRow['import_warehouse_confirm'])) {
                if (!empty($aRow['import_warehouse_confirm'])) {
                    $aRow['import_warehouse_confirm'] = 'Đã duyệt kho';
                } else {
                    $aRow['import_warehouse_confirm'] = 'Chưa duyệt kho';
                }
            }

            foreach ($aColumns as $colIndex => $colCode) {
                if (str_contains($colCode, 'date')) {
                    $cellValue = (isset($aRow[$colCode]) ? _d($aRow[$colCode]) : '');
                } else {
                    $cellValue = (isset($aRow[$colCode]) ? $aRow[$colCode] : '');
                }

                $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$colIndex] . $excelRowNum, $cellValue)->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain);
            }
            $excelRowNum++;
        }

        $filename = 'Phieu_kiem_ke' . '.xls';
        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="$filename"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        $response =  array(
            'result' => 1,
            'message' => lang('success'),
            'filename' => $filename,
            'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        );
        die(json_encode($response));
    }
}
