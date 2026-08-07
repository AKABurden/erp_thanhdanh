<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Set_prices extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        if (!has_permission('set_prices', '', 'view') && !has_permission('set_prices', '', 'view_own')) {
            access_denied('set_prices');
        }
        $data['groups'] = get_table_where('tblcustomers_groups');
        $data['groupss'] = $this->clients_model->get_groups();
        $data['title'] = _l('set_prices');
        $this->load->view('admin/set_prices/manage', $data);
    }

    public function change_view($type = '')
    {
        $data['groups'] = get_table_where('tblcustomers_groups');
        $data['groupss'] = $this->clients_model->get_groups();
        $data['title'] = _l('set_prices');

        if ($type == 1) {
            $this->load->view('admin/set_prices/content_1', $data);
        } else if ($type == 2) {
            $data['allData'] = get_table_where('tbl_set_prices', array('status' => 1));

            $arrDataItem = array();
            $keyMain = 0;
            $allItem = get_table_where('tblitems');
            foreach ($allItem as $key => $value) {
                $arrDataItem[$keyMain]['id'] = 'items_' . $value['id'];
                $arrDataItem[$keyMain]['type'] = 'items';
                $arrDataItem[$keyMain]['code'] = $value['code'];
                $arrDataItem[$keyMain]['name'] = $value['name'];
                $arrDataItem[$keyMain]['price_import'] = $value['price'];
                $get_last_price = get_table_where('tblimport_items', array('product_id' => $value['id'], 'type' => 'items'), 'id DESC', 'row');
                if ($get_last_price) {
                    $arrDataItem[$keyMain]['last_price'] = $get_last_price->price;
                } else {
                    $arrDataItem[$keyMain]['last_price'] = $value['price'];
                }
                $keyMain++;
            }

            $allProduct = get_table_where('tbl_products', array('type_products' => 'products'));
            foreach ($allProduct as $key => $value) {
                $arrDataItem[$keyMain]['id'] = 'product_' . $value['id'];
                $arrDataItem[$keyMain]['type'] = 'product';
                $arrDataItem[$keyMain]['code'] = $value['code'];
                $arrDataItem[$keyMain]['name'] = $value['name'];
                $arrDataItem[$keyMain]['price_import'] = $value['price_import'];
                $get_last_price = get_table_where('tblimport_items', array('product_id' => $value['id'], 'type' => 'product'), 'id DESC', 'row');
                if ($get_last_price) {
                    $arrDataItem[$keyMain]['last_price'] = $get_last_price->price;
                } else {
                    $arrDataItem[$keyMain]['last_price'] = $value['price_import'];
                }
                $keyMain++;
            }
            $data['allDataItem'] = $arrDataItem;
            $this->load->view('admin/set_prices/content_2', $data);
        }
    }

    public function table_set_prices($value = '')
    {
        $this->app->get_table_data('set_prices');
    }

    public function group_detail($id = '')
    {
        $data = $this->input->post();
        $group = '';
        $date_start = '';
        $date_end = '';
        if ($data['type_customer'] == 2) {
            if (!empty($data['groups_in'])) {
                $group = implode(',', $data['groups_in']);
            }
        }
        if (isset($data['checkbox_date'])) {
            $data['checkbox_date'] = 1;
        } else {
            $data['checkbox_date'] = 0;
            if (!empty($data['date_active'])) {
                $date = explode(' - ', $data['date_active']);
                $date_start = to_sql_date($date[0]);
                $date_end = to_sql_date($date[1]);
            }
        }
        if ($id == "") {
            $in = array(
                'name' => $data['name'],
                'checkbox_date' => $data['checkbox_date'],
                'date_start' => $date_start,
                'date_end' => $date_end,
                'status ' => $data['status'],
                'type_customer' => $data['type_customer'],
                'id_groups' => $group,
                'type_item' => $data['type_item'],
                'type_price_setting' => $data['type_price_setting'],
                'sum_OR_sub' => $data['sum_OR_sub'],
                'vnd_OR_percent' => $data['vnd_OR_percent'],
                'value_price_setting ' => str_replace(',', '', $data['value_price_setting'])
            );
            if (is_numeric($data['type_price_setting'])) {
                $this->db->insert('tbl_set_prices', $in);
                $insert_id = $this->db->insert_id();

                $get_data_item = get_table_where('tbl_set_prices_items', array('id_set_prices' => $data['type_price_setting']));
                foreach ($get_data_item as $key => $value) {
                    $in_item = array(
                        'id_set_prices' => $insert_id,
                        'type' => $value['type'],
                        'id_product' => $value['id_product']
                    );

                    if ($data['sum_OR_sub'] == 'sum') {
                        if ($data['vnd_OR_percent'] == 'vnd') {
                            $in_item['prices_new'] = $value['prices_new'] + str_replace(',', '', $data['value_price_setting']);
                        } else {
                            $in_item['prices_new'] = $value['prices_new'] + ($value['prices_new'] * $data['value_price_setting'] / 100);
                        }
                    } else {
                        if ($data['vnd_OR_percent'] == 'vnd') {
                            $in_item['prices_new'] = $value['prices_new'] - str_replace(',', '', $data['value_price_setting']);
                        } else {
                            $in_item['prices_new'] = $value['prices_new'] - ($value['prices_new'] * $data['value_price_setting'] / 100);
                        }
                    }
                    $this->db->insert('tbl_set_prices_items', $in_item);
                }
                echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('add_set_prices_success')));
            } else {
                $this->db->insert('tbl_set_prices', $in);
                echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('add_set_prices_success')));
            }
        } else {
            $in = array(
                'name' => $data['name'],
                'checkbox_date' => $data['checkbox_date'],
                'date_start' => $date_start,
                'date_end' => $date_end,
                'status ' => $data['status'],
                'type_customer' => $data['type_customer'],
                'id_groups' => $group,
                'type_item' => $data['type_item'],
                'type_price_setting' => $data['type_price_setting'],
                'sum_OR_sub' => $data['sum_OR_sub'],
                'vnd_OR_percent' => $data['vnd_OR_percent'],
                'value_price_setting ' => str_replace(',', '', $data['value_price_setting'])
            );
            if (is_numeric($data['type_price_setting'])) {
                $this->db->where('id', $id);
                $this->db->update('tbl_set_prices', $in);

                $get_data_item = get_table_where('tbl_set_prices_items', array('id_set_prices' => $data['type_price_setting']));
                foreach ($get_data_item as $key => $value) {
                    $in_item = array(
                        'id_set_prices' => $id,
                        'type' => $value['type'],
                        'id_product' => $value['id_product']
                    );

                    if ($data['sum_OR_sub'] == 'sum') {
                        if ($data['vnd_OR_percent'] == 'vnd') {
                            $in_item['prices_new'] = $value['prices_new'] + str_replace(',', '', $data['value_price_setting']);
                        } else {
                            $in_item['prices_new'] = $value['prices_new'] + ($value['prices_new'] * $data['value_price_setting'] / 100);
                        }
                    } else {
                        if ($data['vnd_OR_percent'] == 'vnd') {
                            $in_item['prices_new'] = $value['prices_new'] - str_replace(',', '', $data['value_price_setting']);
                        } else {
                            $in_item['prices_new'] = $value['prices_new'] - ($value['prices_new'] * $data['value_price_setting'] / 100);
                        }
                    }
                    $get_item_old = get_table_where('tbl_set_prices_items', array('id_set_prices' => $id));
                    foreach ($get_item_old as $key_item_old => $value_item_old) {
                        if ($value_item_old['id_product'] == $value['id_product']) {
                            $this->db->where('id', $value_item_old['id']);
                            $this->db->delete('tbl_set_prices_items');
                        }
                    }
                    $this->db->insert('tbl_set_prices_items', $in_item);
                }
                echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('edit_set_prices_success')));
            } else {
                $this->db->where('id', $id);
                $this->db->update('tbl_set_prices', $in);
                echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('edit_set_prices_success')));
            }
        }
    }

    public function detail($type_item = '', $id = '')
    {
        //type_item: 1 - hàng hóa || 2 - thành phẩm
        if ($this->input->post()) {
            $data = $this->input->post();
            $arrID = array();
            if (isset($data['items'])) {
                $items = $data['items'];
                foreach ($items as $key => $item) {
                    $str = explode("_", $item['id_items']);
                    $id_items = $str[1];
                    $type_item = $str[0];

                    $item_data = array(
                        'id_set_prices' => $id,
                        'type' => $type_item,
                        'id_product' => $id_items,
                        'prices_new' => str_replace(',', '', $item['prices_new'])
                    );
                    $checkExists = get_table_where('tbl_set_prices_items', array('id_set_prices' => $id, 'id_product' => $id_items, 'type' => $type_item), '', 'row');
                    if (!$checkExists) {
                        $this->db->insert('tbl_set_prices_items', $item_data);
                        $arrID[] = $this->db->insert_id();
                    } else {
                        $this->db->where('id', $checkExists->id);
                        $this->db->update('tbl_set_prices_items', $item_data);
                        $arrID[] = $checkExists->id;
                    }
                }
            }
            if ($arrID != array()) {
                $this->db->where('id_set_prices', $id);
                $this->db->where_not_in('id', $arrID);
            } else {
                $this->db->where('id_set_prices', $id);
            }
            $this->db->delete('tbl_set_prices_items');
            set_alert('success', _l('ch_updatee_items'));
            redirect(admin_url('set_prices/detail/1/' . $id));
        }

        $data['dataMain'] = get_table_where('tbl_set_prices', array('id' => $id), '', 'row');

        $this->db->select('tbl_set_prices_items.*');
        $this->db->from('tbl_set_prices_items');
        $this->db->where('tbl_set_prices_items.id_set_prices', $id);
        $dataSub = $this->db->get()->result_array();
        $data['dataSub'] = array();
        foreach ($dataSub as $key => $value) {
            if ($value['type'] == 'product') {
                $this->db->select('tbl_products.id as id_item, tbl_products.name as name_item, tbl_products.code as code_item, tbl_products.price_import as price_import, tbl_products.images as images');
                $this->db->from('tbl_products');
                $this->db->where('tbl_products.id', $value['id_product']);
                $dataItem = $this->db->get()->row();
                if ($dataItem) {
                    $data['dataSub'][$key]['type_item'] = 'product';
                    $data['dataSub'][$key]['prices_new'] = $value['prices_new'];
                    $data['dataSub'][$key]['id_item'] = $dataItem->id_item;
                    $data['dataSub'][$key]['name_item'] = $dataItem->name_item;
                    $data['dataSub'][$key]['code_item'] = $dataItem->code_item;
                    $data['dataSub'][$key]['price_import'] = $dataItem->price_import;
                    $data['dataSub'][$key]['images'] = $dataItem->images;
                    $get_last_price = get_table_where('tblimport_items', array('product_id' => $dataItem->id_item, 'type' => 'product'), 'id DESC', 'row');
                    if ($get_last_price) {
                        $data['dataSub'][$key]['last_price'] = $get_last_price->price;
                    } else {
                        $data['dataSub'][$key]['last_price'] = $dataItem->price_import;
                    }
                }
            } else if ($value['type'] == 'items') {
                $this->db->select('tblitems.id as id_item, tblitems.name as name_item, tblitems.code as code_item, tblitems.price as price_import, tblitems.avatar as images');
                $this->db->from('tblitems');
                $this->db->where('tblitems.id', $value['id_product']);
                $dataItem = $this->db->get()->row();
                if ($dataItem) {
                    $data['dataSub'][$key]['type_item'] = 'items';
                    $data['dataSub'][$key]['prices_new'] = $value['prices_new'];
                    $data['dataSub'][$key]['id_item'] = $dataItem->id_item;
                    $data['dataSub'][$key]['name_item'] = $dataItem->name_item;
                    $data['dataSub'][$key]['code_item'] = $dataItem->code_item;
                    $data['dataSub'][$key]['price_import'] = $dataItem->price_import;
                    $data['dataSub'][$key]['images'] = $dataItem->images;
                    $get_last_price = get_table_where('tblimport_items', array('product_id' => $dataItem->id_item, 'type' => 'items'), 'id DESC', 'row');
                    if ($get_last_price) {
                        $data['dataSub'][$key]['last_price'] = $get_last_price->price;
                    } else {
                        $data['dataSub'][$key]['last_price'] = $dataItem->price_import;
                    }
                }
            }
        }
        $data['id'] = $id;

        $data['title'] = _l('detail_set_prices');
        $this->load->view('admin/set_prices/detail', $data);
    }
    public function change_status($id = '', $status = '')
    {
        if ($id != '') {
            //2: k áp dụng, 1: áp dụng
            if ($status == 0) {
                $this->db->set('status', 2);
                $this->db->where('id', $id);
                $this->db->update('tbl_set_prices');
            } else if ($status == 1) {
                $this->db->set('status', 1);
                $this->db->where('id', $id);
                $this->db->update('tbl_set_prices');
            }
        }
    }
    public function getData($id = '')
    {
        $this->db->select('tbl_set_prices.*');
        $this->db->where('id', $id);
        $data = $this->db->get('tbl_set_prices')->row();
        $data->date_start = _d($data->date_start);
        $data->date_end = _d($data->date_end);
        $data->date_active = '';
        if ($data->checkbox_date == 0 || is_null($data->checkbox_date)) {
            $data->date_active = $data->date_start . ' - ' . $data->date_end;
        }
        $data->id_groups = explode(',', $data->id_groups);
        echo json_encode($data);
    }
    public function getData_items_by_category($id_category = '', $type_item = '')
    {
        $str = explode("_", $id_category);
        $id_category = $str[1];
        $type_item = $str[0];
        if ($type_item == 'product') {
            $arrID_child = array();
            $this->get_childs_id_product($id_category, $arrID_child);
            $arrData = array();
            if ($arrID_child != array()) {
                $this->db->select('tbl_products.*');
                $this->db->where_in('category_id', $arrID_child);
                $this->db->where('type_products', 'products');
                $Data = $this->db->get('tbl_products')->result_array();

                foreach ($Data as $key => $value) {
                    $arrData[$key]['type_item'] = 'product';
                    $arrData[$key]['id'] = $value['id'];
                    $arrData[$key]['name'] = $value['name'];
                    $arrData[$key]['code'] = $value['code'];
                    $arrData[$key]['price_import'] = $value['price_import'];
                    $arrData[$key]['images'] = $value['images'];
                    $get_last_price = get_table_where('tblimport_items', array('product_id' => $value['id'], 'type' => 'product'), 'id DESC', 'row');
                    if ($get_last_price) {
                        $arrData[$key]['last_price'] = $get_last_price->price;
                    } else {
                        $arrData[$key]['last_price'] = $value['price_import'];
                    }
                }
            }
            echo json_encode($arrData);
            die;
        } else if ($type_item == 'items') {
            $arrID_child = array();
            $this->get_childs_id_items($id_category, $arrID_child);
            $arrData = array();
            if ($arrID_child != array()) {
                $this->db->select('tblitems.*');
                $this->db->where_in('category_id', $arrID_child);
                $Data = $this->db->get('tblitems')->result_array();

                foreach ($Data as $key => $value) {
                    $arrData[$key]['type_item'] = 'items';
                    $arrData[$key]['id'] = $value['id'];
                    $arrData[$key]['name'] = $value['name'];
                    $arrData[$key]['code'] = $value['code'];
                    $arrData[$key]['price_import'] = $value['price'];
                    $arrData[$key]['images'] = $value['avatar'];
                    $get_last_price = get_table_where('tblimport_items', array('product_id' => $value['id'], 'type' => 'items'), 'id DESC', 'row');
                    if ($get_last_price) {
                        $arrData[$key]['last_price'] = $get_last_price->price;
                    } else {
                        $arrData[$key]['last_price'] = $value['price'];
                    }
                }
            }
            echo json_encode($arrData);
            die;
        }
    }
    public function getData_items_by_item($id_item = '', $type_item = '')
    {
        $str = explode("_", $id_item);
        $id_item = $str[1];
        $type_item = $str[0];
        if ($type_item == 'product') {
            $this->db->select('tbl_products.*');
            $this->db->where('type_products', 'products');
            $this->db->where('id', $id_item);
            $Data = $this->db->get('tbl_products')->row();
            if ($Data) {
                $Data->type_item = 'product';
                $get_last_price = get_table_where('tblimport_items', array('product_id' => $Data->id, 'type' => 'product'), 'id DESC', 'row');
                if ($get_last_price) {
                    $Data->last_price = $get_last_price->price;
                } else {
                    $Data->last_price = $Data->price_import;
                }
            }
            echo json_encode($Data);
            die;
        } else if ($type_item == 'items') {
            $this->db->select('tblitems.*');
            $this->db->where('id', $id_item);
            $Data = $this->db->get('tblitems')->row();
            if ($Data) {
                foreach ($Data as $key => $value) {
                    $Data->type_item = 'items';
                    $Data->price_import = $Data->price;
                    $Data->images = $Data->avatar;
                    $get_last_price = get_table_where('tblimport_items', array('product_id' => $Data->id, 'type' => 'items'), 'id DESC', 'row');
                    if ($get_last_price) {
                        $Data->last_price = $get_last_price->price;
                    } else {
                        $Data->last_price = $Data->price;
                    }
                }
            }
            echo json_encode($Data);
            die;
        }
    }
    function get_childs_id_product($parent_id = '', &$result = array())
    {
        array_push($result, $parent_id);
        $this->db->where('parent_id', $parent_id);
        $items = $this->db->get('tbl_category_products')->result();
        foreach ($items as $value) {
            $this->get_childs_id_product($value->id, $result);
        }
    }
    function get_childs_id_items($parent_id = '', &$result = array())
    {
        array_push($result, $parent_id);
        $this->db->where('category_parent', $parent_id);
        $items = $this->db->get('tblcategories')->result();
        foreach ($items as $value) {
            $this->get_childs_id_items($value->id, $result);
        }
    }

    public function SearchItems()
    {
        $data = [];
        $search = $this->input->get('term');
        $type = $this->input->get('types');
        $limit_one = 15;
        $limit_two = 15;
        $limit_all = 50;

        $this->db->select(
            '
            CONCAT("product_", tbl_products.id) as id,
            tbl_products.name as text,
            tbl_products.code as code,
            CONCAT("uploads/products/", "", tbl_products.images, "") as img',
            false
        );
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('tbl_products.name', $search);
            $this->db->or_like('tbl_products.code', $search);
            $this->db->group_end();
        }
        $this->db->where('tbl_products.type_products', 'products');
        $this->db->order_by('tbl_products.name', 'DESC');
        $this->db->limit(50);
        $product = $this->db->get('tbl_products')->result_array();

        $this->db->select(
            '
            CONCAT("items_", tblitems.id) as id,
            tblitems.name as text,
            tblitems.code as code,
            tblitems.avatar as img',
            false
        );
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('tblitems.name', $search);
            $this->db->or_like('tblitems.code', $search);
            $this->db->group_end();
        }
        $this->db->order_by('tblitems.name', 'DESC');
        $this->db->limit(50);
        $items = $this->db->get('tblitems')->result_array();

        if ($product || $items) {
            $data['results'] = [
                [
                    'text' => _l('ch_items'), 'children' => $items
                ],
                [
                    'text' => _l('tnh_products'), 'children' => $product
                ],
            ];
        }
        echo json_encode($data);
        die();
    }
    public function getData_price($type = '')
    {
        $arr = array();
        $arr[] = array(
            'id' => 'giá vốn',
            'name' => _l('cost_price'),
            'sub_name' => _l('system')
        );
        $arr[] = array(
            'id' => 'giá nhập cuối',
            'name' => _l('item_price_last'),
            'sub_name' => _l('system')
        );
        $get_price = get_table_where('tbl_set_prices');
        foreach ($get_price as $key => $value) {
            $value['sub_name'] = _l('table_set_prices');
            $arr[] = $value;
        }
        echo json_encode($arr);
        die();
    }
    public function delete_set_prices($id = '')
    {
        $checkExists = get_table_where('tbl_set_prices', array('type_price_setting' => $id), '', 'row');
        if ($checkExists) {
            echo json_encode(array('success' => false, 'alert_type' => 'danger', 'message' => _l('dont_delete_set_prices')));
        } else {
            $this->db->where('id', $id);
            $this->db->delete('tbl_set_prices');

            $this->db->where('id_set_prices', $id);
            $this->db->delete('tbl_set_prices_items');

            echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('ch_delete_successfuly')));
        }
    }

    public function apply_set_price($id = '')
    {
        $success = false;
        $alert_type = 'danger';
        $message = _l('apply_no_change');

        $get_detail = get_table_where('tbl_set_prices', array('id' => $id), '', 'row');
        if ($get_detail->type_customer == 1) {
            $this->db->set('table_price_id', $id);
            $update_success = $this->db->update('tblclients');
            if ($update_success) {
                $success = true;
                $alert_type = 'success';
                $message = _l('apply_success') . _l('customer_all');
            }
        } else if ($get_detail->type_customer == 2) {
            $total = 0;
            if (!empty($get_detail->id_groups) && $get_detail->id_groups != '') {
                $arr_group = explode(',', $get_detail->id_groups);
                foreach ($arr_group as $key => $value) {
                    $get_id_customer = get_table_where('tblcustomer_groups', array('groupid' => $value));
                    foreach ($get_id_customer as $key_customer => $value_customer) {
                        $this->db->set('table_price_id', $id);
                        $this->db->where('userid', $value_customer['customer_id']);
                        $this->db->update('tblclients');
                        if ($this->db->affected_rows() > 0) {
                            $total++;
                        }
                    }
                }
            }

            $success = true;
            $alert_type = 'success';
            $message = _l('apply_success') . $total . ' ' . _l('client_lowercase');
        }
        echo json_encode(array('success' => $success, 'alert_type' => $alert_type, 'message' => $message));
    }

    public function set_price_to_customer()
    {
        $data = $this->input->post();
        $this->db->set('table_price_id', $data['id_set_price']);
        $this->db->where('userid', $data['id_customer']);
        $this->db->update('tblclients');

        $get_code = get_table_where('tblclients', array('userid' => $data['id_customer']), '', 'row');
        activity_log_v2('client', 'tblclients', $data['id_customer'], $get_code->company, 'Cập nhật bảng giá khách hàng [' . $get_code->company . ']');
    }
    public function print_pdf($id = '')
    {
        ob_start();
        $data = new stdClass();
        $dataMain = get_table_where('tbl_set_prices', array('id' => $id), '', 'row');
        $table = '';
        $data->img = '';
        $data->content = '';
        $data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;">' . mb_strtoupper(_l('table_set_prices') . ' - ' . $dataMain->name, 'UTF-8') . '</span><br><br>';

        $table = '<br>
            <table class="table table-bordered" border="1">
                <thead>
                    
        ';
        $table .= '<tr>
                    <td style="width:5%;text-align: center;font-weight: bold;">' . _l('STT') . '</td>
                    <td style="width:55%;text-align: center;font-weight: bold;">' . _l('Tên hàng') . '</td>
                    <td style="width:20%;text-align: center;font-weight: bold;">' . _l('Đơn vị tính') . '</td>
                    <td style="width:20%;text-align: center;font-weight: bold;">' . _l('Đơn giá') . '</td>';
        $table .= '</tr>
                </thead>
                <tbody>';
        $this->db->where('tbl_set_prices_items.id_set_prices', $id);
        $this->db->from('tbl_set_prices_items');
        $dataSub = $this->db->get()->result_array();
        usort($dataSub, ch_make_cmp(['type' => "desc"]));
        foreach ($dataSub as $key => $value) {
            if ($value['type'] == 'items') {
                $this->db->select('tblitems.name,tblitems.code,tblunits.unit as unit_name,tblitems.category_id,tblcategories.category');
                $this->db->from('tblitems');
                $this->db->join('tblcategories', 'tblcategories.id=tblitems.category_id', 'left');
                $this->db->join('tblunits', 'tblunits.unitid=tblitems.unit', 'left');
                $this->db->where('tblitems.id', $value['id_product']);
                $dataSub[$key] = array_merge($dataSub[$key], $this->db->get()->row_array());
            } elseif ($value['type'] == 'product') {
                $this->db->select('tbl_products.name,tbl_products.code,tblunits.unit as unit_name,tbl_products.category_id,tbl_category_products.name as category');
                $this->db->from('tbl_products');
                $this->db->join('tbl_category_products', 'tbl_category_products.id=tbl_products.category_id', 'left');
                $this->db->join('tblunits', 'tblunits.unitid=tbl_products.unit_id', 'left');
                $this->db->where('tbl_products.id', $value['id_product']);
                $dataSub[$key] = array_merge($dataSub[$key], $this->db->get()->row_array());
            }
        }
        usort($dataSub, ch_make_cmp(['type' => "desc", 'category_id' => "desc"]));
        $_datas = '';
        $_datass = '';
        foreach ($dataSub as $key => $value) {
            if ($key == 0) {
                $table .= '<tr>
                                    <td colspan="5" style="width:100%;font-weight: bold;">' . mb_strtoupper($value['category'], 'UTF-8') . '</td>
                               </tr>';
                $table .= '<tr>
                                    <td style="width:5%;text-align: center;">' . ($key + 1) . '</td>
                                    <td style="width:55%">' . $value['name'] . '(' . $value['code'] . ')</td>
                                    <td style="width:20%;text-align: center;">' . $value['unit_name'] . '</td>
                                    <td style="width:20%;text-align: right">' . number_format($value['prices_new']) . '</td>
                               </tr>';
                $_datas = $value['category_id'];
                $_datass = $value['type'];
            } else {
                if (($_datas != $value['category_id']) || ($_datass != $value['type'])) {
                    $table .= '<tr>
                                    <td colspan="5" style="width:100%;font-weight: bold;">' . mb_strtoupper($value['category'], 'UTF-8') . '</td>
                               </tr>';
                    $_datas = $value['category_id'];
                    $_datass = $value['type'];
                }

                $table .= '<tr>
                                    <td style="width:5%;text-align: center;">' . ($key + 1) . '</td>
                                    <td style="width:55%">' . $value['name'] . '(' . $value['code'] . ')</td>
                                    <td style="width:20%;text-align: center;">' . $value['unit_name'] . '</td>
                                    <td style="width:20%;text-align: right">' . number_format($value['prices_new']) . '</td>
                               </tr>';
            }
        }
        // if($dataMain->type_item == 1)
        // {   

        //     $this->db->select('tblitems.name,tblitems.code,tblunits.unit as unit_name,tblitems.category_id,tblcategories.category,tbl_set_prices_items.prices_new');
        //     $this->db->from('tbl_set_prices_items');
        //     $this->db->join('tblitems','tblitems.id=tbl_set_prices_items.id_product','left');
        //     $this->db->join('tblcategories','tblcategories.id=tblitems.category_id','left');
        //     $this->db->join('tblunits','tblunits.unitid=tblitems.unit','left');
        //     $this->db->where('tbl_set_prices_items.id_set_prices', $id);
        //     $dataSub = $this->db->get()->result_array();
        //     usort($dataSub, ch_make_cmp(['category_id' => "desc"]));
        //     foreach ($dataSub as $key => $value) {
        //        if($key == 0)
        //        {
        //         $table .= '<tr>
        //                         <td colspan="5" style="width:100%;font-weight: bold;">'.mb_strtoupper($value['category'],'UTF-8').'</td>
        //                    </tr>';
        //         $table .= '<tr>
        //                         <td style="width:5%;text-align: center;">'.($key + 1).'</td>
        //                         <td style="width:55%">'.$value['name'].'('.$value['code'].')</td>
        //                         <td style="width:20%;text-align: center;">'.$value['unit_name'].'</td>
        //                         <td style="width:20%;text-align: right">'.number_format($value['prices_new']).'</td>
        //                    </tr>';
        //         $_data = $value['category_id'];
        //        }else
        //        {
        //         if($_data != $value['category_id'])
        //         {
        //             $table .= '<tr>
        //                         <td colspan="5" style="width:100%;font-weight: bold;">'.mb_strtoupper($value['category'],'UTF-8').'</td>
        //                    </tr>';
        //             $_data = $value['category_id'];                
        //         }

        //             $table .= '<tr>
        //                         <td style="width:5%;text-align: center;">'.($key + 1).'</td>
        //                         <td style="width:55%">'.$value['name'].'('.$value['code'].')</td>
        //                         <td style="width:20%;text-align: center;">'.$value['unit_name'].'</td>
        //                         <td style="width:20%;text-align: right">'.number_format($value['prices_new']).'</td>
        //                    </tr>';
        //        }
        //     }
        // }else
        // {
        //     $this->db->select('tbl_products.name,tbl_products.code,tblunits.unit as unit_name,tbl_products.category_id,tbl_category_products.name as name_category,tbl_set_prices_items.prices_new');
        //     $this->db->from('tbl_set_prices_items');
        //     $this->db->join('tbl_products','tbl_products.id=tbl_set_prices_items.id_product','left');
        //     $this->db->join('tbl_category_products','tbl_category_products.id=tbl_products.category_id','left');
        //     $this->db->join('tblunits','tblunits.unitid=tbl_products.unit_id','left');
        //     $this->db->where('tbl_set_prices_items.id_set_prices', $id);
        //     $dataSub = $this->db->get()->result_array();
        //     usort($dataSub, ch_make_cmp(['category_id' => "desc"]));
        //     foreach ($dataSub as $key => $value) {
        //        if($key == 0)
        //        {
        //         $table .= '<tr>
        //                         <td colspan="5" style="width:100%;font-weight: bold;">'.mb_strtoupper($value['name_category'],'UTF-8').'</td>
        //                    </tr>';
        //         $table .= '<tr>
        //                         <td style="width:5%;text-align: center;">'.($key + 1).'</td>
        //                         <td style="width:55%">'.$value['name'].'('.$value['code'].')</td>
        //                         <td style="width:20%;text-align: center;">'.$value['unit_name'].'</td>
        //                         <td style="width:20%;text-align: right">'.number_format($value['prices_new']).'</td>
        //                    </tr>';
        //         $_data = $value['category_id'];
        //        }else
        //        {
        //         if($_data != $value['category_id'])
        //         {
        //             $table .= '<tr>
        //                         <td colspan="5" style="width:100%;font-weight: bold;">'.mb_strtoupper($value['name_category'],'UTF-8').'</td>
        //                    </tr>';    
        //             $_data = $value['category_id'];       
        //         }

        //             $table .= '<tr>
        //                         <td style="width:5%;text-align: center;">'.($key + 1).'</td>
        //                         <td style="width:55%">'.$value['name'].'('.$value['code'].')</td>
        //                         <td style="width:20%;text-align: center;">'.$value['unit_name'].'</td>
        //                         <td style="width:20%;text-align: right">'.number_format($value['prices_new']).'</td>
        //                    </tr>';
        //        }
        //     }
        // }
        $table .= '</tbody>
            </table>';
        $data->content .= $table;
        $pdf      = print_pdf($data);
        $type     = 'I';
        $pdf->Output(slug_it('') . '.pdf', $type);
    }

    public function getData_import_data($id = '')
    {
        $data['dataMain'] = get_table_where('tbl_set_prices', array('id' => $id), '', 'row');
        $this->load->view('admin/set_prices/modal_import_data', $data);
    }

    public function import_data($id = '')
    {
        $dataMain = get_table_where('tbl_set_prices', array('id' => $id), '', 'row');
        $total_imported = 0;
        $load_result = false;
        $alert = [
            'success' => 0,
            'fail'    => [],
        ];
        if ($this->input->post()) {
            if (isset($_FILES['file_import']['name']) && $_FILES['file_import']['name'] != '') {
                $tmpFilePath = $_FILES['file_import']['tmp_name'];
                $ext = strtolower(pathinfo($_FILES['file_import']['name'], PATHINFO_EXTENSION));
                $type = $_FILES["file_import"]["type"];
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    $newFilePath = TEMP_FOLDER . $_FILES['file_import']['name'];
                    if (!file_exists(TEMP_FOLDER)) {
                        mkdir(TEMP_FOLDER, 777);
                    }
                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        $load_result = true;
                        $fd            = fopen($newFilePath, 'r');
                        $rows          = array();
                        if ($ext == 'csv') {
                            while ($row = fgetcsv($fd)) {
                                $rows[] = $row;
                            }
                        } else if ($ext == 'xlsx' || $ext == 'xls') {
                            if ($type == "application/octet-stream" || $type == "application/vnd.ms-excel" || $type == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
                                require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
                                $inputFileType = PHPExcel_IOFactory::identify($newFilePath);
                                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                                $objPHPExcel        = $objReader->load($newFilePath);
                                $allSheetName       = $objPHPExcel->getSheetNames();
                                $objWorksheet       = $objPHPExcel->setActiveSheetIndex(0);
                                $highestRow         = $objWorksheet->getHighestRow();
                                $highestColumn      = $objWorksheet->getHighestColumn();
                                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                                for ($row = 1; $row <= $highestRow; ++$row) {
                                    for ($col = 0; $col < $highestColumnIndex; ++$col) {
                                        $value                     = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                                        $rows[$row - 1][$col] = $value;
                                    }
                                }
                            }
                        } else {
                            fclose($fd);
                            unlink($newFilePath);
                            redirect('/');
                        }

                        fclose($fd);
                        $data['total_rows_post'] = count($rows);
                        unlink($newFilePath);

                        $data = [];
                        $data_ok = true;
                        $reason = "";
                        $dem_temp = 1;
                        $alert['success'] = 0;
                        $alert['fail'] = 0;

                        foreach ($rows as $row) {
                            $check_items = true;
                            $type = '';
                            if ($row[0] == 1) {
                                $type = 'items';
                                $this->db->select('tblitems.*');
                                $this->db->where('tblitems.code', $row[1]);
                                $check_exists = $this->db->get('tblitems')->row();
                                if (!$check_exists) {
                                    $check_items = false;
                                }
                            } else if ($row[0] == 2) {
                                $type = 'product';
                                $this->db->select('tbl_products.*');
                                $this->db->where('tbl_products.code', $row[1]);
                                $check_exists = $this->db->get('tbl_products')->row();
                                if (!$check_exists) {
                                    $check_items = false;
                                }
                            }

                            $data_ok = true;
                            if ($row[0] == 'Loại (1: hàng hóa/ 2: thành phẩm)') {
                                $dem_temp++;
                                $data_ok = false;
                                continue;
                            } else if (empty($row[0])) {
                                $reason .= "Không tìm thấy loại hàng hóa/thành phẩm tại dòng " . $dem_temp . "<br />";
                                $data_ok = false;
                                $dem_temp++;
                                continue;
                            } else if ($row[0] != 1 && $row[0] != 2) {
                                $reason .= "Loại hàng hóa/thành phẩm không hợp lệ tại dòng " . $dem_temp . "<br />";
                                $data_ok = false;
                                $dem_temp++;
                                continue;
                            } else if (empty($row[1])) {
                                $reason .= "Không tìm thấy mã hàng hóa/thành phẩm tại dòng " . $dem_temp . "<br />";
                                $data_ok = false;
                                $dem_temp++;
                                continue;
                            } else if ($check_items == false) {
                                $reason .= "Mã hàng hóa/thành phẩm không hợp lệ tại dòng " . $dem_temp . "<br />";
                                $data_ok = false;
                                $dem_temp++;
                                continue;
                            } else {
                                if (empty($row[2])) {
                                    $row[2] = 0;
                                } else {
                                    $row[2] = str_replace(',', "", $row[2]);
                                    $row[2] = str_replace('.', "", $row[2]);
                                }

                                if ($data_ok) {
                                    $in_import_item = array(
                                        'id_set_prices' => $id,
                                        'type' => $type,
                                        'id_product' => $check_exists->id,
                                        'prices_new' => $row[2]
                                    );

                                    //bỏ qua dữ liệu đã tồn tại
                                    if ($this->input->post('status') == 1) {
                                        $check_data = get_table_where('tbl_set_prices_items', array('id_set_prices' => $id, 'type' => $type, 'id_product' => $check_exists->id), '', 'row');
                                        if ($check_data) {
                                            $dem_temp++;
                                            continue;
                                        } else {
                                            $alert['success']++;
                                            $dem_temp++;
                                            $this->db->insert('tbl_set_prices_items', $in_import_item);
                                        }
                                    }
                                    //cập nhật dữ liệu đã tồn tại
                                    else if ($this->input->post('status') == 2) {
                                        $check_data = get_table_where('tbl_set_prices_items', array('id_set_prices' => $id, 'type' => $type, 'id_product' => $check_exists->id), '', 'row');
                                        if ($check_data) {
                                            $alert['success']++;
                                            $dem_temp++;
                                            $this->db->where('tbl_set_prices_items.id', $check_data->id);
                                            $this->db->update('tbl_set_prices_items', $in_import_item);
                                        } else {
                                            $alert['success']++;
                                            $dem_temp++;
                                            $this->db->insert('tbl_set_prices_items', $in_import_item);
                                        }
                                    }
                                }
                            }
                        }
                        $data['message'] = "Nhập thành công " . $alert['success'] . " trường dữ liệu. <br />";
                        $data['message'] .= $reason;
                    }
                }
            }
        }
        $this->session->set_flashdata('message', $data['message']);
        redirect(admin_url('set_prices/detail/' . $dataMain->type_item . '/' . $id));
    }

    public function getRow()
    {
        $data = $this->input->post();
        $id_exists = array();
        $html_thead = '';
        $html_tbody = array();
        if (isset($data['arrID'])) {
            foreach ($data['arrID'] as $key => $value) {
                $checkAdd = true;
                if (isset($data['arrID_exists'])) {
                    if (in_array($value, $data['arrID_exists'])) {
                        $checkAdd = false;
                    }
                }
                if ($checkAdd == true) {
                    //thead
                    $getData = get_table_where('tbl_set_prices', array('id' => $value), '', 'row');
                    $html_thead .= '
                        <th class="text-left bold colum_new" data-check="' . $value . '">' . $getData->name . '</th>
                    ';
                    //tbody
                    $getAllitem = array();
                    $keyMain = 0;
                    $allItem = get_table_where('tblitems');
                    foreach ($allItem as $keyItem => $valueItem) {
                        $getAllitem[$keyMain]['id'] = 'items_' . $valueItem['id'];
                        //check price new
                        $getPrice = get_table_where('tbl_set_prices_items', array('id_set_prices' => $value, 'type' => 'items', 'id_product' => $valueItem['id']), '', 'row');
                        if ($getPrice) {
                            $getAllitem[$keyMain]['price'] = '
                                <td class="text-right colum_new" data-check="' . $value . '">
                                    <input class="H_input align_right event-change" data-id="items_' . $valueItem['id'] . '" value="' . number_format($getPrice->prices_new) . '"></input>
                                </td>
                            ';
                        } else {
                            $getAllitem[$keyMain]['price'] = '
                                <td class="text-right colum_new" data-check="' . $value . '">
                                    <input class="H_input align_right event-change" data-id="items_' . $valueItem['id'] . '"></input>
                                </td>
                            ';
                        }
                        $keyMain++;
                    }

                    $allProduct = get_table_where('tbl_products', array('type_products' => 'products'));
                    foreach ($allProduct as $keyProduct => $valueProduct) {
                        $getAllitem[$keyMain]['id'] = 'product_' . $valueProduct['id'];
                        //check price new
                        $getPrice = get_table_where('tbl_set_prices_items', array('id_set_prices' => $value, 'type' => 'product', 'id_product' => $valueProduct['id']), '', 'row');
                        if ($getPrice) {
                            $getAllitem[$keyMain]['price'] = '
                                <td class="text-right colum_new" data-check="' . $value . '">
                                    <input class="H_input align_right event-change" data-id="product_' . $valueProduct['id'] . '" value="' . number_format($getPrice->prices_new) . '"></input>
                                </td>
                            ';
                        } else {
                            $getAllitem[$keyMain]['price'] = '
                                <td class="text-right colum_new" data-check="' . $value . '">
                                    <input class="H_input align_right event-change" data-id="product_' . $valueProduct['id'] . '"></input>
                                </td>
                            ';
                        }
                        $keyMain++;
                    }
                    $html_tbody = $getAllitem;

                    $id_exists = $value;
                }
            }
        }

        $result_data['id_exists'] = $id_exists;
        $result_data['html_thead'] = $html_thead;
        $result_data['html_tbody'] = $html_tbody;
        echo json_encode($result_data);
        die;
    }

    public function setRow()
    {
        $data = $this->input->post();
        $arrID = explode("_", $data['id_item']);
        $type = $arrID[0];
        $id = $arrID[1];
        $in = array(
            'id_set_prices' => $data['id_setPrice'],
            'type' => $type,
            'id_product' => $id,
            'prices_new' => str_replace(",", "", $data['val'])
        );
        $checkExists = get_table_where('tbl_set_prices_items', array('id_set_prices' => $data['id_setPrice'], 'type' => $type, 'id_product' => $id), '', 'row');
        if ($checkExists) {
            $this->db->where('id', $checkExists->id);
            $this->db->update('tbl_set_prices_items', $in);
        } else {
            $this->db->insert('tbl_set_prices_items', $in);
        }
    }
}
