<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pos extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('category_model');
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('orders_model');
    }
    public function index()
    {
        $data['dataItem'] = get_table_where('tblitems',array('type_items'=>'items'));
        if(!$data['dataItem']) {
            $data['dataItem'] = array();
        }
        $data['categories'] = [];
        $this->category_model->get_by_id(0,$data['categories']);
        $data['title'] = _l('Pos');
        $this->load->view('admin/pos/manage', $data);
    }
    public function getData_items()
    {
        $data = $this->input->post();
        if($data['search'] != '') {
            $this->db->select('tblitems.id, tblitems.name, tblitems.code, tblitems.price, tblitems.avatar');
            $this->db->like('tblitems.name',$data['search']);
            $this->db->or_like('tblitems.code',$data['search']);
            $this->db->or_like('tblitems.price',$data['search']);
            $result = $this->db->get('tblitems')->result_array();
            if($result) {
                foreach ($result as $key => $value) {
                    $data_result[$key]['id'] = $value['id'];
                    $data_result[$key]['name'] = $value['name'];
                    $data_result[$key]['code'] = $value['code'];
                    $data_result[$key]['price'] = $value['price'];
                    if(empty($value['avatar'])) {
                        $data_result[$key]['avatar'] = 'uploads/no-img.jpg';
                    }
                    else {
                        $data_result[$key]['avatar'] = $value['avatar'];
                    }
                }
            }
            else {
                $data_result = array();
            }
        }
        else {
            $data_result = array();
        }
        echo json_encode($data_result);die;
    }
    public function getData_filter_items()
    {
        $data = $this->input->post();
        $key_sub = 0;
        $arrData = array();
        if(isset($data['arr_id'])) {
            foreach ($data['arr_id'] as $key_main => $value_main) {
                $arrID_child = array();
                $this->get_childs_id_items($value_main, $arrID_child);
                if($arrID_child != array()) {
                    $this->db->select('tblitems.*');
                    $this->db->where_in('category_id',$arrID_child);
                    $Data = $this->db->get('tblitems')->result_array();
                    foreach ($Data as $key => $value) {
                        $arrData[$key_sub]['id'] = $value['id'];
                        $arrData[$key_sub]['name'] = $value['name'];
                        $arrData[$key_sub]['code'] = $value['code'];
                        $arrData[$key_sub]['price'] = $value['price'];
                        if(empty($value['avatar'])) {
                            $arrData[$key_sub]['avatar'] = 'uploads/no-img.jpg';
                        }
                        else {
                            $arrData[$key_sub]['avatar'] = $value['avatar'];
                        }
                        $key_sub++;
                    }
                }
            }
            if($data['amount_from'] != '' && $data['amount_to'] != '') {
                if(isset($arrData) && !empty($arrData)) {
                    foreach ($arrData as $key => $value) {
                        if($value['price'] > str_replace(',', "", $data['amount_from']) && $value['price'] < str_replace(',', "", $data['amount_to'])) {
                            $data_result[$key]['id'] = $value['id'];
                            $data_result[$key]['name'] = $value['name'];
                            $data_result[$key]['code'] = $value['code'];
                            $data_result[$key]['price'] = $value['price'];
                            if(empty($value['avatar'])) {
                                $data_result[$key]['avatar'] = 'uploads/no-img.jpg';
                            }
                            else {
                                $data_result[$key]['avatar'] = $value['avatar'];
                            }
                        }
                    }
                }
                else {
                    $data_result = array();
                }
            }
            else {
                if(isset($arrData) && !empty($arrData)) {
                    foreach ($arrData as $key => $value) {
                        $data_result[$key]['id'] = $value['id'];
                        $data_result[$key]['name'] = $value['name'];
                        $data_result[$key]['code'] = $value['code'];
                        $data_result[$key]['price'] = $value['price'];
                        if(empty($value['avatar'])) {
                            $data_result[$key]['avatar'] = 'uploads/no-img.jpg';
                        }
                        else {
                            $data_result[$key]['avatar'] = $value['avatar'];
                        }
                    }
                }
                else {
                    $data_result = array();
                }
            }
        }
        else {
            $this->db->select('tblitems.*');
            if($data['amount_from'] != '' && $data['amount_to'] != '') {
                $this->db->where('tblitems.price >',str_replace(',', "", $data['amount_from']));
                $this->db->where('tblitems.price <',str_replace(',', "", $data['amount_to']));
            }
            $result = $this->db->get('tblitems')->result_array();
            if($result) {
                foreach ($result as $key => $value) {
                    $data_result[$key]['id'] = $value['id'];
                    $data_result[$key]['name'] = $value['name'];
                    $data_result[$key]['code'] = $value['code'];
                    $data_result[$key]['price'] = $value['price'];
                    if(empty($value['avatar'])) {
                        $data_result[$key]['avatar'] = 'uploads/no-img.jpg';
                    }
                    else {
                        $data_result[$key]['avatar'] = $value['avatar'];
                    }
                }
            }
            else {
                $data_result = array();
            }
        }
        echo json_encode($data_result);die;
    }
    public function getData_items_by_category()
    {
        $data = $this->input->post();
        $key_sub = 0;
        $arrData = array();
        if(isset($data['arr_id'])) {
            foreach ($data['arr_id'] as $key_main => $value_main) {
                $arrID_child = array();
                $this->get_childs_id_items($value_main, $arrID_child);
                if($arrID_child != array()) {
                    $this->db->select('tblitems.*');
                    $this->db->where_in('category_id',$arrID_child);
                    $Data = $this->db->get('tblitems')->result_array();

                    foreach ($Data as $key => $value) {
                        $arrData[$key_sub]['id'] = $value['id'];
                        $arrData[$key_sub]['name'] = $value['name'];
                        $arrData[$key_sub]['code'] = $value['code'];
                        $arrData[$key_sub]['price'] = $value['price'];
                        if(empty($value['avatar'])) {
                            $arrData[$key_sub]['avatar'] = 'uploads/no-img.jpg';
                        }
                        else {
                            $arrData[$key_sub]['avatar'] = $value['avatar'];
                        }
                        $key_sub++;
                    }
                }
            }
        }
        else {
            $this->db->select('tblitems.*');
            $result = $this->db->get('tblitems')->result_array();
            if($result) {
                foreach ($result as $key => $value) {
                    $arrData[$key]['id'] = $value['id'];
                    $arrData[$key]['name'] = $value['name'];
                    $arrData[$key]['code'] = $value['code'];
                    $arrData[$key]['price'] = $value['price'];
                    if(empty($value['avatar'])) {
                        $arrData[$key]['avatar'] = 'uploads/no-img.jpg';
                    }
                    else {
                        $arrData[$key]['avatar'] = $value['avatar'];
                    }
                }
            }
        }
        echo json_encode($arrData);die;
    }

    function get_childs_id_items($parent_id='', &$result=array()) {
        array_push($result, $parent_id);
        $this->db->where('category_parent', $parent_id);
        $items = $this->db->get('tblcategories')->result();
        foreach($items as $value) {
            $this->get_childs_id_items($value->id, $result);
        }
    }

    public function addPos()
    {
        $data = [];
        if ($this->input->post('add'))
        {
            $this->form_validation->set_rules('date_create', lang("date"), 'required');
            if ($this->form_validation->run() == true)
            {
                // print_arrays($this->input->post());
                $date = to_sql_date($this->input->post('date_create'), true);
                $customer_id = 334;
                $address_delivery = 0;
                $employees = get_staff_user_id();
                $note = $this->input->post('note');

                $count_items = 0;
                $total_quantity = 0;
                $total_amount_items = 0;
                $total_tax_items = 0;
                $total_discount_percent_items = 0;
                $total_discount_direct_items = 0;
                $grand_total_items = 0;
                $tax_id = $this->input->post('tax_id') ? $this->input->post('tax_id') : 0;
                $tax_name = 0;
                $tax_rate = 0;
                $total_tax = 0;
                $discount_percent = $this->input->post('discount_percent') ? $this->input->post('discount_percent') : 0;
                $total_discount_percent = 0;
                $total_discount_direct = $this->input->post('discount_direct') ? number_unformat($this->input->post('discount_direct')) : 0;
                $grand_total = 0;
                $status = 'un_approved';

                $items_id = $this->input->post('item_id');
                if (!empty($items_id)) {
                    foreach ($items_id as $key => $item_id) {
                        if (empty($item_id)) continue;

                        $type_item = "items";
                        if ($type_item == "items") {
                            $info = $this->items_model->rowItems($item_id);
                        }
                        if (empty($info)) {
                            continue;
                        }
                        $items_code = $info['code'];
                        $items_name = $info['name'];
                        $quantity = number_unformat($this->input->post('quantity')[$key]);
                        $price = $info['price'];
                        $amount = $quantity * $price;

                        $grand_total_item = $amount;
                        //tax item
                        $tax_id_item = 0;
                        $tax_name_item = "0%";
                        $tax_rate_item = 0;
                        $tax_amount_item = 0;
                        if (!empty($tax_id_item)) {
                            $info_tax = $this->site_model->rowTax($tax_id_item);
                            if (!empty($info_tax)) {
                                $tax_name_item = $info_tax['name'];
                                $tax_rate_item = $info_tax['taxrate'];
                            }
                        }

                        if ($tax_rate_item > 0) {
                            $tax_amount_item = $grand_total_item * ($tax_rate_item/100);
                            $total_tax_items+= $tax_amount_item;
                            $grand_total_item+= $tax_amount_item;
                        }

                        //end
                        //discount percent item
                        $discount_percent_item = 0;
                        $discount_percent_amount_item = 0;
                        if ($discount_percent_item > 0) {
                            $discount_percent_amount_item = $grand_total_item*($discount_percent_item/100);
                            $total_discount_percent_items+= $discount_percent_amount_item;
                            $grand_total_item-= $discount_percent_amount_item;
                        }
                        //end
                        $discount_direct_amount_item = 0;

                        $total_discount_direct_items+= $discount_direct_amount_item;
                        $grand_total_item-= $discount_direct_amount_item;

                        $items[] = [
                            'type_item' => $type_item,
                            'item_id' => $item_id,
                            'item_code' => $items_code,
                            'item_name' => $items_name,
                            'quantity' => $quantity,
                            'price' => $price,
                            'amount' => $amount,
                            'tax_id_item' => $tax_id_item,
                            'tax_name_item' => $tax_name_item,
                            'tax_rate_item' => $tax_rate_item,
                            'tax_amount_item' => $tax_amount_item,
                            'discount_percent_item' => $discount_percent_item,
                            'discount_percent_amount_item' => $discount_percent_amount_item,
                            'discount_direct_amount_item' => $discount_direct_amount_item,
                            'total_amount' => $grand_total_item,
                            'sub' => false
                        ];

                        $total_quantity+= $quantity;
                        $total_amount_items+= $amount;
                        $grand_total_items+= $grand_total_item;
                    }
                }

                if (empty($items)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_not_items');
                    echo json_encode($data);
                    die;
                }

                $count_items = count($items);
                $grand_total = $grand_total_items;
                // print_arrays($grand_total);
                if (!empty($tax_id)) {
                    $info_tax = $this->site_model->rowTax($tax_id);
                    if (!empty($info_tax)) {
                        $tax_name = $info_tax['name'];
                        $tax_rate = $info_tax['taxrate'];
                    }
                }
                if ($tax_rate > 0) {
                    $total_tax = $grand_total_items * ($tax_rate/100);
                }
                $grand_total+= $total_tax;

                if ($discount_percent > 0) {
                    $total_discount_percent = $grand_total * ($discount_percent/100);
                }
                $grand_total-= $total_discount_percent;
                $grand_total-= $total_discount_direct;

                $type_customer = "customers";
                //handing customer
                if ($type_customer == "customers") {
                    $row_customer = $this->site_model->rowCustomer($customer_id);
                    if (empty($row_customer)) {
                        $data['result'] = 0;
                        $data['message'] = lang('tnh_customer_not_exist');
                        echo json_encode($data);
                        die;
                    }
                    $customer_name = $row_customer['company'];
                    $address_delivery_id = $address_delivery;
                }
                //end
                $options = [
                    'date' => $date,
                    'reference_no' => getReference('orders'),
                    'customer_id' => $customer_id,
                    'customer_name' => $customer_name,
                    'address_delivery_id' => $address_delivery_id,
                    'employee_id' => $employees,
                    'note' => $note,
                    'count_items' => $count_items,
                    'total_quantity' => $total_quantity,
                    'total_amount_items' => $total_amount_items,
                    'total_tax_items' => $total_tax_items,
                    'total_discount_percent_items' => $total_discount_percent_items,
                    'total_discount_direct_items' => $total_discount_direct_items,
                    'grand_total_items' => $grand_total_items,
                    'tax_id' => $tax_id,
                    'tax_name' => $tax_name,
                    'tax_rate' => $tax_rate,
                    'total_tax' => $total_tax,
                    'discount_percent' => $discount_percent,
                    'total_discount_percent' => $total_discount_percent,
                    'total_discount_direct' => $total_discount_direct,
                    'grand_total' => $grand_total,
                    'status' => $status,
                    'date_created' => date('Y-m-d H:i'),
                    'created_by' => get_staff_user_id(),
                    'pos' => 1,
                ];
                $order_id = $this->orders_model->insertOrdersNew($options);
                if ($order_id) {
                    updateReference('orders');

                    foreach ($items as $key => $value) {
                        $value['order_id'] = $order_id;
                        $sub = $value['sub'];
                        unset($value['sub']);
                        $order_item_id = $this->orders_model->insertOrderItemsNew($value);
                    }

                    set_alert('success', lang('success'));
                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
        }
        echo json_encode($data);
    }
}
