<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Promotion extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    // index cho list
    // public function index()
    // {
    //     $data['title'] = _l('promotion_list_full_name');
    //     $this->load->view('admin/promotion/manage', $data);
    // }
    public function table_promotion_list($value='')
    {
        if (!has_permission('promotion', '', 'view')) {
            ajax_access_denied();
        }
        $this->app->get_table_data('promotion_list');
    }
    public function table_promotion_detail($value='')
    {
        if (!has_permission('promotion', '', 'view')) {
            ajax_access_denied();
        }
        $this->app->get_table_data('promotion_detail');
    }
    public function group_detail($id = '')
    {
        $data = $this->input->post();
        if($id == "") {
            $in = array(
                'name'=>$data['name']
            );
            $this->db->insert('tblpromotion_list',$in);
            echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('add_promotion_list_success')));
        }
        else {
            $in = array(
                'name'=>$data['name']
            );
            $this->db->where('id',$id);
            $this->db->update('tblpromotion_list',$in);
            echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('edit_promotion_list_success')));
        }
    }
    public function getData_promotion_list($id='')
    {
        $this->db->select('tblpromotion_list.*');
        $this->db->where('id',$id);
        $data = $this->db->get('tblpromotion_list')->row();
        echo json_encode($data);
    }

    public function index()
    // public function promotion_detail()
    {
        $data['title']          = _l('promotion');
        $this->load->view('admin/promotion/promotion_detail', $data);
    }
    public function detail($id='')
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            if(!isset($data['area_of_application'])) {
                $data['area_of_application'] = null;
            }

            if(!isset($data['groups_in']) || $data['area_of_application'] != 'area') {
                $data['groups_in'] = null;
            }
            else {
                $data['groups_in'] = implode(',', $data['groups_in']);
            }

            if(!isset($data['date_active'])) {
                $data['date_active'] = null;
            }
            else {
                $data['date_active'] = explode(' - ', $data['date_active']);
            }
            if($id == "") {
                $in = array(
                    // 'promotion_list_id'=>$data['promotion_list_id'],
                    'name'=>$data['name'],
                    'type'=>$data['type'],
                    'method_of_application'=>$data['method_of_application'],
                    'area_of_application'=>$data['area_of_application'],
                    'groups_in'=>$data['groups_in'],
                    'date_active_start'=>to_sql_date($data['date_active'][0]),
                    'date_active_end'=>to_sql_date($data['date_active'][1])
                );
                $this->db->insert('tblpromotion',$in);
                $insert_id = $this->db->insert_id();
                if($insert_id) {
                    //thêm khách hàng
                    if(!isset($data['customer']) || ($data['method_of_application'] != 'other' && $data['area_of_application'] != 'other')) {
                        $data['customer'] = array();
                    }
                    foreach ($data['customer'] as $key => $value) {
                        $val_customer = array(
                            'promotion_id'=>$insert_id,
                            'customer_id'=>$value['id_item']
                        );
                        $this->db->insert('tblpromotion_customer',$val_customer);
                    }
                    //end
                    //loại khuyến mãi theo chiết khấu
                    if($data['type']=='discount') {
                        if(!isset($data['time_sales'])) {
                            $data['time_sales'] = null;
                        }
                        else {
                            $data['time_sales'] = explode(' - ', $data['time_sales']);
                        }
                        $in_discount = array(
                            'promotion_id'=>$insert_id,
                            'type_discount'=>$data['type_discount'],
                            'time_sales_start'=>to_sql_date($data['time_sales'][0]),
                            'time_sales_end'=>to_sql_date($data['time_sales'][1]),
                            'note'=>$data['note_type_discount']
                        );
                        $this->db->insert('tblpromotion_discount',$in_discount);

                        if(!isset($data['item_discount'])) {
                            $data['item_discount'] = array();
                        }
                        if(!isset($data['item_discount_item']) || $data['type_discount'] == 1) {
                            $data['item_discount_item'] = array();
                        }
                        foreach ($data['item_discount'] as $key => $value) {
                            $val_discount = array(
                                'promotion_id'=>$insert_id,
                                'limit_sales'=>str_replace(',',"",$value['limit_sales']),
                                'type_limit_discount'=>$value['type_discount'],
                                'limit_discount'=>str_replace(',',"",$value['limit_discount'])
                            );
                            $this->db->insert('tblpromotion_discount_amount',$val_discount);
                        }
                        set_alert('success', _l('cong_add_true'));
                        redirect(admin_url('promotion'));
                    }
                    //loại khuyến mãi theo bộ
                    else if($data['type']=='item') {
                        if(!isset($data['item'])) {
                            $data['item'] = array();
                        }
                        if(!isset($data['items_gift'])) {
                            $data['items_gift'] = array();
                        }
                        foreach ($data['item'] as $key => $value) {
                            $value['id_item'] = explode('_', $value['id_item']);
                            $val_item = array(
                                'promotion_id'=>$insert_id,
                                'id_item'=>$value['id_item'][1],
                                'type_item'=>$value['id_item'][0],
                                'quantity'=>str_replace(',',"",$value['quantity']),
                                'note'=>$data['note_type_item']
                            );
                            $this->db->insert('tblpromotion_item',$val_item);
                        }
                        foreach ($data['items_gift'] as $key => $value) {
                            $value['id'] = explode('_', $value['id']);
                            $value['items_id'] = explode('_', $value['items_id']);
                            $val_items_gift = array(
                                'promotion_id'=>$insert_id,
                                'promotion_item_id'=>$value['items_id'][1],
                                'promotion_item_type'=>$value['items_id'][0],
                                'id_item'=>$value['id'][1],
                                'type_item'=>$value['id'][0],
                                'quantity'=>str_replace(',',"",$value['quantity'])
                            );
                            $this->db->insert('tblpromotion_item_gift',$val_items_gift);
                        }
                        set_alert('success', _l('cong_add_true'));
                        redirect(admin_url('promotion'));
                    }
                    //loại khuyến mãi theo doanh số
                    else if($data['type']=='sales') {
                        if(!isset($data['date_active_sales'])) {
                            $data['date_active_sales'] = null;
                        }
                        else {
                            $data['date_active_sales'] = explode(' - ', $data['date_active_sales']);
                        }

                        if(!isset($data['type_limit_points']) == 'Điểm') {
                            $data['type_sales'] = null;
                        }

                        $in_sales = array(
                            'promotion_id'=>$insert_id,
                            'type_limit_points'=>$data['type_limit_points'],
                            'limit_points'=>str_replace(',',"",$data['limit_points']),
                            'date_active_sales_start'=>to_sql_date($data['date_active_sales'][0]),
                            'date_active_sales_end'=>to_sql_date($data['date_active_sales'][1]),
                            'type_sales'=>$data['type_sales'],
                            'exchange_amount'=>str_replace(',',"",$data['exchange_amount']),
                            'exchange_points'=>str_replace(',',"",$data['exchange_points']),
                            'note'=>$data['note_type_sales']
                        );
                        $this->db->insert('tblpromotion_sales',$in_sales);

                        if(!isset($data['item'])) {
                            $data['item'] = array();
                        }
                        foreach ($data['item'] as $key => $value) {
                            $value['id_item'] = explode('_', $value['id_item']);
                            $item_item = array(
                                'promotion_id'=>$insert_id,
                                'id_item'=>$value['id_item'][1],
                                'type_item'=>$value['id_item'][0],
                                'quantity'=>str_replace(',',"",$value['quantity'])
                            );
                            $this->db->insert('tblpromotion_sales_item_gift',$item_item);
                        }
                        set_alert('success', _l('cong_add_true'));
                        redirect(admin_url('promotion'));
                    }
                }
            }
            else {
                $checkStatus = get_table_where('tblpromotion',array('id'=>$id),'','row');
                if($checkStatus->status != 0) {
                    set_alert('warning', _l('cong_event_active_false'));
                    redirect(admin_url('promotion'));
                }
                $in = array(
                    // 'promotion_list_id'=>$data['promotion_list_id'],
                    'name'=>$data['name'],
                    'type'=>$data['type'],
                    'method_of_application'=>$data['method_of_application'],
                    'area_of_application'=>$data['area_of_application'],
                    'groups_in'=>$data['groups_in'],
                    'date_active_start'=>to_sql_date($data['date_active'][0]),
                    'date_active_end'=>to_sql_date($data['date_active'][1])
                );
                $this->db->where('id',$id);
                $results = $this->db->update('tblpromotion',$in);
                if($results) {
                    //thêm khách hàng
                    if(!isset($data['customer']) || ($data['method_of_application'] != 'other' && $data['area_of_application'] != 'other')) {
                        $data['customer'] = array();
                    }
                    $arr_ID_customer = array();
                    foreach ($data['customer'] as $key => $value) {
                        $val_customer = array(
                            'promotion_id'=>$id,
                            'customer_id'=>$value['id_item']
                        );
                        $checkExists = get_table_where('tblpromotion_customer',array('promotion_id'=>$id,'customer_id'=>$value['id_item']),'','row');
                        if($checkExists) {
                            $this->db->where('id',$checkExists->id);
                            $this->db->update('tblpromotion_customer',$val_customer);
                            $arr_ID_customer[] = $checkExists->id;
                        }
                        else {
                            $this->db->insert('tblpromotion_customer',$val_customer);
                            $arr_ID_customer[] = $this->db->insert_id();
                        }
                    }
                    //xóa
                    if(count($arr_ID_customer) > 0) {
                        $this->db->where('promotion_id',$id);
                        $this->db->where_not_in('id',$arr_ID_customer);
                        $this->db->delete('tblpromotion_customer');
                    }
                    else {
                        $this->db->where('promotion_id',$id);
                        $this->db->delete('tblpromotion_customer');
                    }
                    //end
                    //loại khuyến mãi theo chiết khấu
                    if($data['type']=='discount') {
                        if(!isset($data['time_sales'])) {
                            $data['time_sales'] = null;
                        }
                        else {
                            $data['time_sales'] = explode(' - ', $data['time_sales']);
                        }
                        $in_discount = array(
                            'promotion_id'=>$id,
                            'type_discount'=>$data['type_discount'],
                            'time_sales_start'=>to_sql_date($data['time_sales'][0]),
                            'time_sales_end'=>to_sql_date($data['time_sales'][1]),
                            'note'=>$data['note_type_discount']
                        );
                        $this->db->where('promotion_id',$id);
                        $this->db->update('tblpromotion_discount',$in_discount);

                        if(!isset($data['item_discount'])) {
                            $data['item_discount'] = array();
                        }
                        if(!isset($data['item_discount_item']) || $data['type_discount'] == 1) {
                            $data['item_discount_item'] = array();
                        }
                        $arr_ID_amount = array();
                        $arr_ID_item = array();
                        foreach ($data['item_discount'] as $key => $value) {
                            $val_discount = array(
                                'promotion_id'=>$id,
                                'limit_sales'=>str_replace(',',"",$value['limit_sales']),
                                'type_limit_discount'=>$value['type_discount'],
                                'limit_discount'=>str_replace(',',"",$value['limit_discount'])
                            );
                            if(isset($value['id_amount']) && is_numeric($value['id_amount'])) {
                                $this->db->where('id',$value['id_amount']);
                                $this->db->update('tblpromotion_discount_amount',$val_discount);
                                $arr_ID_amount[] = $value['id_amount'];
                            }
                            else {
                                $this->db->insert('tblpromotion_discount_amount',$val_discount);
                                $id_amount = $this->db->insert_id();
                                $arr_ID_amount[] = $id_amount;
                            }
                        }

                        //xóa
                        if(count($arr_ID_amount) > 0) {
                            $this->db->where('promotion_id',$id);
                            $this->db->where_not_in('id',$arr_ID_amount);
                            $this->db->delete('tblpromotion_discount_amount');
                        }
                        else {
                            $this->db->where('promotion_id',$id);
                            $this->db->delete('tblpromotion_discount_amount');
                        }
                        set_alert('success', _l('cong_update_true'));
                        redirect(admin_url('promotion/detail/'.$id));
                    }
                    //loại khuyến mãi theo bộ
                    if($data['type']=='item') {
                        if(!isset($data['item'])) {
                            $data['item'] = array();
                        }
                        if(!isset($data['items_gift'])) {
                            $data['items_gift'] = array();
                        }
                        $arr_ID_item = array();
                        $arr_ID_item_gift = array();
                        foreach ($data['item'] as $key => $value) {
                            $value['id_item'] = explode('_', $value['id_item']);
                            $val_item = array(
                                'promotion_id'=>$id,
                                'id_item'=>$value['id_item'][1],
                                'type_item'=>$value['id_item'][0],
                                'quantity'=>str_replace(',',"",$value['quantity']),
                                'note'=>$data['note_type_item']
                            );
                            $checkExists = get_table_where('tblpromotion_item',array('promotion_id'=>$id,'id_item'=>$value['id_item'][1],'type_item'=>$value['id_item'][0]),'','row');
                            if($checkExists) {
                                $this->db->where('id',$checkExists->id);
                                $this->db->update('tblpromotion_item',$val_item);
                                $arr_ID_item[] = $checkExists->id;
                            }
                            else {
                                $this->db->insert('tblpromotion_item',$val_item);
                                $id_item = $this->db->insert_id();
                                $arr_ID_item[] = $id_item;
                            }
                        }
                        foreach ($data['items_gift'] as $key => $value) {
                            $value['id'] = explode('_', $value['id']);
                            $value['items_id'] = explode('_', $value['items_id']);
                            $val_items_gift = array(
                                'promotion_id'=>$id,
                                'promotion_item_id'=>$value['items_id'][1],
                                'promotion_item_type'=>$value['items_id'][0],
                                'id_item'=>$value['id'][1],
                                'type_item'=>$value['id'][0],
                                'quantity'=>str_replace(',',"",$value['quantity'])
                            );
                            $checkExists2 = get_table_where('tblpromotion_item_gift',array('promotion_id'=>$id,'id_item'=>$value['id'][1],'type_item'=>$value['id'][0],'promotion_item_id'=>$value['items_id'][1],'promotion_item_type'=>$value['items_id'][0]),'','row');
                            if($checkExists2) {
                                $this->db->where('id',$checkExists2->id);
                                $this->db->update('tblpromotion_item_gift',$val_items_gift);
                                $arr_ID_item_gift[] = $checkExists2->id;
                            }
                            else {
                                $this->db->insert('tblpromotion_item_gift',$val_items_gift);
                                $id_item = $this->db->insert_id();
                                $arr_ID_item_gift[] = $id_item;
                            }
                        }

                        //xóa
                        if(count($arr_ID_item) > 0) {
                            $this->db->where('promotion_id',$id);
                            $this->db->where_not_in('id',$arr_ID_item);
                            $this->db->delete('tblpromotion_item');
                        }
                        else {
                            $this->db->where('promotion_id',$id);
                            $this->db->delete('tblpromotion_item');
                        }

                        if(count($arr_ID_item_gift) > 0) {
                            $this->db->where('promotion_id',$id);
                            $this->db->where_not_in('id',$arr_ID_item_gift);
                            $this->db->delete('tblpromotion_item_gift');
                        }
                        else {
                            $this->db->where('promotion_id',$id);
                            $this->db->delete('tblpromotion_item_gift');
                        }
                        set_alert('success', _l('cong_update_true'));
                        redirect(admin_url('promotion/detail/'.$id));
                    }
                    //loại khuyến mãi theo doanh số
                    else if($data['type']=='sales') {
                        if(!isset($data['date_active_sales'])) {
                            $data['date_active_sales'] = null;
                        }
                        else {
                            $data['date_active_sales'] = explode(' - ', $data['date_active_sales']);
                        }

                        if($data['type_limit_points'] == 'Điểm') {
                            $data['type_sales'] = null;
                        }

                        $in_sales = array(
                            'promotion_id'=>$id,
                            'type_limit_points'=>$data['type_limit_points'],
                            'limit_points'=>str_replace(',',"",$data['limit_points']),
                            'date_active_sales_start'=>to_sql_date($data['date_active_sales'][0]),
                            'date_active_sales_end'=>to_sql_date($data['date_active_sales'][1]),
                            'type_sales'=>$data['type_sales'],
                            'exchange_amount'=>str_replace(',',"",$data['exchange_amount']),
                            'exchange_points'=>str_replace(',',"",$data['exchange_points']),
                            'note'=>$data['note_type_sales']
                        );
                        $this->db->where('promotion_id',$id);
                        $this->db->update('tblpromotion_sales',$in_sales);

                        if(!isset($data['item'])) {
                            $data['item'] = array();
                        }
                        $arr_ID_points = array();
                        $arr_ID_item = array();
                        foreach ($data['item'] as $key => $value) {
                            $value['id_item'] = explode('_', $value['id_item']);
                            $item_item = array(
                                'promotion_id'=>$id,
                                'id_item'=>$value['id_item'][1],
                                'type_item'=>$value['id_item'][0],
                                'quantity'=>str_replace(',',"",$value['quantity'])
                            );
                            $checkExists2 = get_table_where('tblpromotion_sales_item_gift',array('promotion_id'=>$id,'id_item'=>$value['id_item'][1],'type_item'=>$value['id_item'][0]),'','row');
                            if($checkExists2) {
                                $this->db->where('id',$checkExists2->id);
                                $this->db->update('tblpromotion_sales_item_gift',$item_item);
                                $arr_ID_item[] = $checkExists2->id;
                            }
                            else {
                                $this->db->insert('tblpromotion_sales_item_gift',$item_item);
                                $id_item = $this->db->insert_id();
                                $arr_ID_item[] = $id_item;
                            }
                        }

                        //xóa
                        if(count($arr_ID_item) > 0) {
                            $this->db->where('promotion_id',$id);
                            $this->db->where_not_in('id',$arr_ID_item);
                            $this->db->delete('tblpromotion_sales_item_gift');
                        }
                        else {
                            $this->db->where('promotion_id',$id);
                            $this->db->delete('tblpromotion_sales_item_gift');
                        }
                        set_alert('success', _l('cong_update_true'));
                        redirect(admin_url('promotion/detail/'.$id));
                    }
                }
            }
        }
        $data['clients'] = array();
        if ($id == "") {
            $data['dataItem'] = array();
            $data['dataAmount'] = array();
            $data['dataItem_gift'] = array();
            $data['dataCustomer'] = array();
            $data['title'] = _l('add_promotion');
        }
        else {
            $checkStatus = get_table_where('tblpromotion',array('id'=>$id),'','row');
            if($checkStatus->status != 0) {
                set_alert('warning', _l('cong_event_active_false'));
                redirect(admin_url('promotion'));
            }

            $data['title'] = _l('edit_promotion');
            $data['dataMain'] = get_table_where('tblpromotion',array('id'=>$id),'','row');
            $this->db->select('tblpromotion_customer.*, tblclients.company as company');
            $this->db->where('promotion_id', $id);
            $this->db->join('tblclients', 'tblclients.userid = tblpromotion_customer.customer_id', 'left');
            $data['dataCustomer'] = $this->db->get('tblpromotion_customer')->result_array();
            if(!$data['dataCustomer']) {
                $data['dataCustomer'] = array();
            }
            //loại khuyến mãi theo chiết khấu
            if($data['dataMain']->type == 'discount') {
                $data['dataSub'] = get_table_where('tblpromotion_discount',array('promotion_id'=>$id),'','row');
                $data['dataItem_gift'] = array(); //bổ xung view loại 3
                $data['dataGift'] = array(); //bổ xung view loại 2
                
                $dataAmount = get_table_where('tblpromotion_discount_amount',array('promotion_id'=>$id));
                if(empty($dataAmount)) {
                    $data['dataAmount'] = array();
                }
                else {
                    foreach ($dataAmount as $key => $value) {
                        $data['dataAmount'][$key]['id_amount'] = $value['id'];
                        $data['dataAmount'][$key]['limit_sales'] = number_format($value['limit_sales']);
                        $data['dataAmount'][$key]['type_limit_discount'] = $value['type_limit_discount'];
                        $data['dataAmount'][$key]['limit_discount'] = number_format($value['limit_discount']);
                    }
                }
            }
            //loại khuyến mãi theo bộ
            else if($data['dataMain']->type == 'item') {
                $data['dataAmount'] = array(); //bổ xung view loại 1
                $data['dataItem_gift'] = array(); //bổ xung view loại 3
                $dataItem = get_table_where('tblpromotion_item',array('promotion_id'=>$id));
                if(empty($dataItem)) {
                    $data['dataItem'] = array();
                }
                else {
                    foreach ($dataItem as $key => $value) {
                        if($value['type_item'] == 'items') {
                            $get_item = get_table_where('tblitems',array('id'=>$value['id_item']),'','row');
                            $data['dataItem'][$key]['id_item'] = $value['type_item'] .'_'. $get_item->id;
                            $data['dataItem'][$key]['name_item'] = $get_item->name .' - '. number_format($get_item->price);
                            $data['dataItem'][$key]['img_item'] = (!empty($get_item->avatar) && $get_item->avatar != '') ? $get_item->avatar : 'uploads/no-img.jpg';
                        }
                        else if($value['type_item'] == 'product') {
                            $get_item = get_table_where('tbl_products',array('id'=>$value['id_item']),'','row');
                            $data['dataItem'][$key]['id_item'] = $value['type_item'] .'_'. $get_item->id;
                            $data['dataItem'][$key]['name_item'] = $get_item->name .' - '. number_format($get_item->price_sell);
                            $data['dataItem'][$key]['img_item'] = (!empty($get_item->images) && $get_item->images != '') ? 'uploads/products/'.$get_item->images : 'uploads/no-img.jpg';
                        }
                        else if($value['type_item'] == 'nvl') {
                            $get_item = get_table_where('tbl_materials',array('id'=>$value['id_item']),'','row');
                            $data['dataItem'][$key]['id_item'] = $value['type_item'] .'_'. $get_item->id;
                            $data['dataItem'][$key]['name_item'] = $get_item->name .' - '. number_format($get_item->price_sell);
                            $data['dataItem'][$key]['img_item'] = (!empty($get_item->images) && $get_item->images != '') ? 'uploads/materials/'.$get_item->images : 'uploads/no-img.jpg';
                        }
                        else if($value['type_item'] == 'categoriesProducts') {
                            $get_item = get_table_where('tbl_category_products',array('id'=>$value['id_item']),'','row');
                            $data['dataItem'][$key]['id_item'] = $value['type_item'] .'_'. $get_item->id;
                            $data['dataItem'][$key]['name_item'] = '<span class="inline-block label label-warning">'._l('tnh_category_product').'</span> '.$get_item->name;
                            $data['dataItem'][$key]['img_item'] = '';
                        }
                        else if($value['type_item'] == 'categoriesItems') {
                            $get_item = get_table_where('tblcategories',array('id'=>$value['id_item']),'','row');
                            $data['dataItem'][$key]['id_item'] = $value['type_item'] .'_'. $get_item->id;
                            $data['dataItem'][$key]['name_item'] = '<span class="inline-block label label-warning">'._l('ch_categories').'</span> '.$get_item->category;
                            $data['dataItem'][$key]['img_item'] = '';
                        }

                        $data['dataItem'][$key]['quantity'] = number_format($value['quantity']);
                        $data['dataItem'][$key]['note'] = $value['note'];

                        $dataGift = get_table_where('tblpromotion_item_gift',array('promotion_id'=>$id,'promotion_item_id'=>$value['id_item'],'promotion_item_type'=>$value['type_item']));
                        if(empty($dataGift)) {
                            $data['dataItem'][$key]['dataGift'] = array();
                        }
                        else {
                            foreach ($dataGift as $key_Gift => $value_Gift) {
                                if($value_Gift['type_item'] == 'items') {
                                    $get_item = get_table_where('tblitems',array('id'=>$value_Gift['id_item']),'','row');
                                    $data['dataItem'][$key]['dataGift'][$key_Gift]['id_item'] = $value_Gift['type_item'] .'_'. $get_item->id;
                                    $data['dataItem'][$key]['dataGift'][$key_Gift]['name_item'] = $get_item->name .' - '. number_format($get_item->price);
                                    $data['dataItem'][$key]['dataGift'][$key_Gift]['img_item'] = (!empty($get_item->avatar) && $get_item->avatar != '') ? $get_item->avatar : 'uploads/no-img.jpg';
                                }
                                else if($value_Gift['type_item'] == 'product') {
                                    $get_item = get_table_where('tbl_products',array('id'=>$value_Gift['id_item']),'','row');
                                    $data['dataItem'][$key]['dataGift'][$key_Gift]['id_item'] = $value_Gift['type_item'] .'_'. $get_item->id;
                                    $data['dataItem'][$key]['dataGift'][$key_Gift]['name_item'] = $get_item->name .' - '. number_format($get_item->price_sell);
                                    $data['dataItem'][$key]['dataGift'][$key_Gift]['img_item'] = (!empty($get_item->images) && $get_item->images != '') ? 'uploads/products/'.$get_item->images : 'uploads/no-img.jpg';
                                }
                                else if($value_Gift['type_item'] == 'nvl') {
                                    $get_item = get_table_where('tbl_materials',array('id'=>$value_Gift['id_item']),'','row');
                                    $data['dataItem'][$key]['dataGift'][$key_Gift]['id_item'] = $value_Gift['type_item'] .'_'. $get_item->id;
                                    $data['dataItem'][$key]['dataGift'][$key_Gift]['name_item'] = $get_item->name .' - '. number_format($get_item->price_sell);
                                    $data['dataItem'][$key]['dataGift'][$key_Gift]['img_item'] = (!empty($get_item->images) && $get_item->images != '') ? 'uploads/materials/'.$get_item->images : 'uploads/no-img.jpg';
                                }
                                $data['dataItem'][$key]['dataGift'][$key_Gift]['quantity'] = number_format($value_Gift['quantity']);
                            }
                        }
                    }
                }
            }
            //loại khuyến mãi theo doanh số
            else if($data['dataMain']->type == 'sales') {
                $data['dataSub'] = get_table_where('tblpromotion_sales',array('promotion_id'=>$id),'','row');
                $data['dataSub']->type_discount = ''; //bổ xung view loại 1
                $data['dataAmount'] = array(); //bổ xung view loại 1
                $data['dataGift'] = array(); //bổ xung view loại 2
                
                $dataItem_gift = get_table_where('tblpromotion_sales_item_gift',array('promotion_id'=>$id));
                if(empty($dataItem_gift)) {
                    $data['dataItem_gift'] = array();
                }
                else {
                    foreach ($dataItem_gift as $key => $value) {
                        if($value['type_item'] == 'items') {
                            $get_item = get_table_where('tblitems',array('id'=>$value['id_item']),'','row');
                            $data['dataItem_gift'][$key]['id_item'] = $value['type_item'] .'_'. $get_item->id;
                            $data['dataItem_gift'][$key]['name_item'] = $get_item->name .' - '. number_format($get_item->price);
                            $data['dataItem_gift'][$key]['img_item'] = (!empty($get_item->avatar) && $get_item->avatar != '') ? $get_item->avatar : 'uploads/no-img.jpg';
                        }
                        else if($value['type_item'] == 'product') {
                            $get_item = get_table_where('tbl_products',array('id'=>$value['id_item']),'','row');
                            $data['dataItem_gift'][$key]['id_item'] = $value['type_item'] .'_'. $get_item->id;
                            $data['dataItem_gift'][$key]['name_item'] = $get_item->name .' - '. number_format($get_item->price_sell);
                            $data['dataItem_gift'][$key]['img_item'] = (!empty($get_item->images) && $get_item->images != '') ? 'uploads/products/'.$get_item->images : 'uploads/no-img.jpg';
                        }
                        else if($value['type_item'] == 'nvl') {
                            $get_item = get_table_where('tbl_materials',array('id'=>$value['id_item']),'','row');
                            $data['dataItem_gift'][$key]['id_item'] = $value['type_item'] .'_'. $get_item->id;
                            $data['dataItem_gift'][$key]['name_item'] = $get_item->name .' - '. number_format($get_item->price_sell);
                            $data['dataItem_gift'][$key]['img_item'] = (!empty($get_item->images) && $get_item->images != '') ? 'uploads/materials/'.$get_item->images : 'uploads/no-img.jpg';
                        }
                        $data['dataItem_gift'][$key]['quantity'] = number_format($value['quantity']);
                    }
                }
            }
        }
        
        $data['groups'] = get_table_where('tblcustomers_groups');
        $data['promotion_list'] = get_table_where('tblpromotion_list');
        $this->load->view('admin/promotion/detail', $data);
    }
    public function SearchItems()
    {
        $data = [];
        $search = $this->input->get('term');
        $type = $this->input->get('type');
        $limit_one = 15;
        $limit_two = 15;
        $limit_all = 50;

        if($type == -1)
        {
            $this->db->select('
                    concat("items_",tblitems.id) as id,
                    tblitems.name as text,
                    tblitems.price,
                    concat("items") as type,
                    tblitems.avatar as img'
            , false);
            if (!empty($search))
            {
                $this->db->group_start();
                $this->db->like('tblitems.name', $search);
                $this->db->or_like('tblitems.code', $search);
                $this->db->group_end();
            }
            $this->db->order_by('name', 'DESC');
            $this->db->limit($limit_one);
            $items = $this->db->get('tblitems')->result_array();
            if(!empty($items)) {
                $data['results'][] =
                    [
                        'text' => _l('Sản phẩm'),
                        'children' => $items
                    ];
            }
            $count_items = count($items);
            $this->db->select('
                concat("product_",tbl_products.id) as id,
                tbl_products.name as text,
                tbl_products.price_sell as price,
                concat("product") as type,
                CONCAT("uploads/products/", "", tbl_products.images, "") as img'
            , false);
            if (!empty($search))
            {
                $this->db->group_start();
                $this->db->like('tbl_products.name', $search);
                $this->db->or_like('tbl_products.code', $search);
                $this->db->group_end();
            }
            $this->db->order_by('tbl_products.name', 'DESC');
            $this->db->limit($limit_two);
            // $this->db->limit(($limit_all - $count_product));
            $product = $this->db->get('tbl_products')->result_array();
            if(!empty($product))
            {
                $data['results'][] =
                    [
                        'text' => _l('Thành phẩm'),
                        'children' => $product
                    ];
            }

            $count_product = count($product);
            $this->db->select('
                concat("nvl_",tbl_materials.id) as id,
                tbl_materials.name as text,
                tbl_materials.price_sell as price,
                concat("nvl") as type,
                CONCAT("uploads/materials/", "", tbl_materials.images, "") as img'
            , false);
            if (!empty($search))
            {
                $this->db->group_start();
                $this->db->like('tbl_materials.name', $search);
                $this->db->or_like('tbl_materials.code', $search);
                $this->db->group_end();
            }
            $this->db->order_by('tbl_materials.name', 'DESC');
            $this->db->limit(($limit_all - $count_product - $count_items));
            $product = $this->db->get('tbl_materials')->result_array();
            if(!empty($product))
            {
                $data['results'][] =
                    [
                        'text' => _l('Nguyên vật liệu'),
                        'children' => $product
                    ];
            }
        } else if($type == 'items') {
            $this->db->select('
                    id as id,
                    tblitems.name as text,
                    tblitems.price,
                    items as type,
                    tblitems.avatar as img'
            , false);
            if (!empty($search))
            {
                $this->db->group_start();
                $this->db->like('tblitems.name', $search);
                $this->db->or_like('tblitems.code', $search);
                $this->db->group_end();
            }
            $this->db->order_by('name', 'DESC');
            $this->db->limit(50);
            $items = $this->db->get('tblitems')->result_array();
            if(!empty($items)) {
                $data['results'][] =
                    [
                        'text' => _l('Sản phẩm'),
                        'children' => $items
                    ];
            }
        } else if($type == 'product') {
            $this->db->select('
                id as id,
                tbl_products.name as text,
                tbl_products.price_sell as price,
                product as type,
                CONCAT("uploads/products/", "", tbl_products.images, "") as img'
            , false);
            if (!empty($search))
            {
                $this->db->group_start();
                $this->db->like('tbl_products.name', $search);
                $this->db->or_like('tbl_products.code', $search);
                $this->db->group_end();
            }
            $this->db->order_by('tbl_products.name', 'DESC');
            $this->db->limit(50);
            // $this->db->limit(($limit_all - $count_product));
            $product = $this->db->get('tbl_products')->result_array();
            if(!empty($product))
            {
                $data['results'][] =
                    [
                        'text' => _l('Thành phẩm'),
                        'children' => $product
                    ];
            }
        } else if($type == 'nvl') {
            $this->db->select('
                id as id,
                tbl_materials.name as text,
                tbl_materials.price_sell as price,
                nvl as type,
                CONCAT("uploads/materials/", "", tbl_materials.images, "") as img'
            , false);
            if (!empty($search))
            {
                $this->db->group_start();
                $this->db->like('tbl_materials.name', $search);
                $this->db->or_like('tbl_materials.code', $search);
                $this->db->group_end();
            }
            $this->db->order_by('tbl_materials.name', 'DESC');
            $this->db->limit(50);
            $product = $this->db->get('tbl_materials')->result_array();
            if(!empty($product))
            {
                $data['results'][] =
                    [
                        'text' => _l('Nguyên vật liệu'),
                        'children' => $product
                    ];
            }
        }
        echo json_encode($data);die();
    }

    public function SearchCategories()
    {
        $data['results'][] =
            [
                'text' => _l('tnh_category_product'),
                'children' => $this->CategoriesProducts()
            ];
        $data['results'][] =
            [
                'text' => _l('ch_categories'),
                'children' => $this->CategoriesItems()
            ];
        echo json_encode($data);die();
    }
    public function CategoriesProducts($id = 0, &$outputProducts = array(), $parent_id = 0, $indent = '') {
        $this->db->select('*');
        $this->db->from('tbl_category_products');
        $this->db->where('tbl_category_products.parent_id', $parent_id);
        $this->db->order_by('tbl_category_products.parent_id');
        $query = $this->db->get()->result_array();

        foreach ($query as $k => $item) {
            if ($item['parent_id'] == $parent_id) {
                if ($item['id'] == $id && $id != 0) {
                    continue;
                }
                $this->db->select('
                    CONCAT("categoriesProducts_",id) as id,
                    CONCAT("'.$indent.'➪&nbsp;",tbl_category_products.name) as text,
                    tbl_category_products.name as name_item,
                    "'._l('tnh_category_product').'" as type_item'
                , false);
                $this->db->where('id',$item['id']);
                $outputProducts[] = $this->db->get('tbl_category_products')->row();

                $this->CategoriesProducts($id, $outputProducts, $item['id'], $indent . "&nbsp;&nbsp;&nbsp;&nbsp;");
            }
        }
        return $outputProducts;
    }
    public function CategoriesItems($id = 0, &$outputItems = array(), $parent_id = 0, $indent = '') {
        $this->db->select('*');
        $this->db->from('tblcategories');
        $this->db->where('tblcategories.category_parent', $parent_id);
        $this->db->order_by('tblcategories.category_parent');
        $query = $this->db->get()->result_array();

        foreach ($query as $k => $item) {
            if ($item['category_parent'] == $parent_id) {
                if ($item['id'] == $id && $id != 0) {
                    continue;
                }
                $this->db->select('
                    CONCAT("categoriesItems_",id) as id,
                    CONCAT("'.$indent.'➪&nbsp;",tblcategories.category) as text,
                    tblcategories.category as name_item,
                    "'._l('ch_categories').'" as type_item'
                , false);
                $this->db->where('id',$item['id']);
                $outputItems[] = $this->db->get('tblcategories')->row();

                $this->CategoriesItems($id, $outputItems, $item['id'], $indent . "&nbsp;&nbsp;&nbsp;&nbsp;");
            }
        }
        return $outputItems;
    }

    public function SearchCustomer()
    {
        $data = [];
        $search = $this->input->get('term');
        $this->db->select('
            tblclients.userid as id,
            tblclients.company as text'
        , false);
        if (!empty($search))
        {
            $this->db->group_start();
            $this->db->like('tblclients.company', $search);
            $this->db->group_end();
        }
        $this->db->order_by('tblclients.company', 'DESC');
        $this->db->limit(50);
        $client = $this->db->get('tblclients')->result_array();
        if($client) {
            $data['results'][] =
                [
                    'text' => _l('cong_client'),
                    'children' => $client
                ];
        }
        echo json_encode($data);die();
    }

    public function delete_promotion_list($id='')
    {
        $checkExists = get_table_where('tblpromotion',array('promotion_list_id'=>$id),'','row');
        if($checkExists) {
            echo json_encode(array('success' => false, 'alert_type' => 'danger', 'message' => _l('dont_delete_promotion')));
        }
        else {
            $this->db->where('id',$id);
            $this->db->delete('tblpromotion_list');

            echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('ch_delete_successfuly')));
        }
    }

    public function delete_promotion($id='')
    {
        $this->db->where('id',$id);
        $this->db->delete('tblpromotion');

        $this->db->where('promotion_id',$id);
        $this->db->delete('tblpromotion_customer');

        $this->db->where('promotion_id',$id);
        $this->db->delete('tblpromotion_discount');

        $this->db->where('promotion_id',$id);
        $this->db->delete('tblpromotion_discount_amount');

        $this->db->where('promotion_id',$id);
        $this->db->delete('tblpromotion_discount_item');

        $this->db->where('promotion_id',$id);
        $this->db->delete('tblpromotion_item');

        $this->db->where('promotion_id',$id);
        $this->db->delete('tblpromotion_item_gift');

        $this->db->where('promotion_id',$id);
        $this->db->delete('tblpromotion_sales');

        $this->db->where('promotion_id',$id);
        $this->db->delete('tblpromotion_sales_item');

        $this->db->where('promotion_id',$id);
        $this->db->delete('tblpromotion_sales_item_gift');

        echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('ch_delete_successfuly')));
    }

    public function update_status($value='')
    {
        if ($this->input->post()) {
            $id = $this->input->post('id');
            $status = $this->input->post('status');
            $staff_id = get_staff_user_id();
            $date = date('Y-m-d H:i:s');
            $data =array(
                'status' => ($status+1),
                'active_status' => $staff_id .'|'. $date,
            );
            $this->db->where('id',$id);
            $success = $this->db->update('tblpromotion',$data);
        }
        if($success) {
            echo json_encode(array(
                'success' => $success,
                'alert_type' => 'success',
                'message' => _l('ch_successful_approval')
            ));
        }
        else {
            echo json_encode(array(
                'success' => $success,
                'alert_type' => 'danger',
                'message' => _l('ch_no_successful_approval')
            ));
        }
        die;
    }
    public function count_all()
    {
        $count = get_table_where_select('count(*) as alls','tblpromotion',array(),'','row');
        $status1 = get_table_where_select('count(*) as status1','tblpromotion',array('status'=>0),'','row');
        $status2 = get_table_where_select('count(*) as status2','tblpromotion',array('status'=>1),'','row');
        $status3 = get_table_where_select('count(*) as status3','tblpromotion',array('status'=>2),'','row');
        $status4 = get_table_where_select('count(*) as status4','tblpromotion',array('status'=>3),'','row');

        $data['all'] = 0;
        $data['status1'] = 0;
        $data['status2'] = 0;

        if(!empty($count)) {
            $data['all'] = $count->alls;
        }
        if(!empty($status1)) {
            $data['status1'] = $status1->status1;
        }
        if(!empty($status2)) {
            $data['status2'] = $status2->status2;
        }
        if(!empty($status3)) {
            $data['status3'] = $status3->status3;
        }
        if(!empty($status4)) {
            $data['status4'] = $status4->status4;
        }

        echo json_encode($data);
    }
    public function view($id = '')
    {
        $data['clients'] = array();
        $data['title'] = _l('edit_promotion');
        $data['dataMain'] = get_table_where('tblpromotion',array('id'=>$id),'','row');
        if($data['dataMain']->method_of_application == 'one') {
            $data['dataMethod'] = '<span>'._l('promotion_application_one').'</span>';
        }
        else if($data['dataMain']->method_of_application == 'all') {
            $data['dataMethod'] = '<span>'._l('promotion_application_all').'</span>';
        }
        else if($data['dataMain']->method_of_application == 'other') {
            $data['dataMethod'] = '<span>'._l('promotion_application_other').'</span>';
        }

        if($data['dataMain']->area_of_application == 'all') {
            $data['dataArea'] = '<span>'._l('cong_all').'</span>';
        }
        else if($data['dataMain']->area_of_application == 'area') {
            $data['dataArea'] = '<span>'._l('promotion_area').'</span>';
        }
        else if($data['dataMain']->area_of_application == 'other') {
            $data['dataArea'] = '<span>'._l('promotion_area_other').'</span>';
        }
        else {
            $data['dataArea'] = '';
        }

        if(!empty($data['dataMain']->groups_in)) {
            $val = '';
            $arr = explode(',', $data['dataMain']->groups_in);
            foreach ($arr as $key_arr => $value_arr) {
                $get_name = get_table_where('tblcustomers_groups',array('id'=>$value_arr),'','row');
                if($get_name) {
                    $val .= $get_name->name . ', ';
                }
            }
            $data['dataGroup_customer'] = trim($val,', ');
        }
        else {
            $data['dataGroup_customer'] = '';
        }

        $this->db->select('tblpromotion_customer.*, tblclients.company as company');
        $this->db->where('promotion_id', $id);
        $this->db->join('tblclients', 'tblclients.userid = tblpromotion_customer.customer_id', 'left');
        $data['dataCustomer'] = $this->db->get('tblpromotion_customer')->result_array();
        if(!$data['dataCustomer']) {
            $data['dataCustomer'] = array();
        }
        //loại khuyến mãi theo chiết khấu
        if($data['dataMain']->type == 'discount') {
            $data['dataSub'] = get_table_where('tblpromotion_discount',array('promotion_id'=>$id),'','row');
            $data['dataItem_gift'] = array(); //bổ xung view loại 3
            $data['dataGift'] = array(); //bổ xung view loại 2
            $data['dataType'] = '<span>'._l('promotion_by_discount').'</span>';
            
            $dataAmount = get_table_where('tblpromotion_discount_amount',array('promotion_id'=>$id));
            if(empty($dataAmount)) {
                $data['dataAmount'] = array();
            }
            else {
                foreach ($dataAmount as $key => $value) {
                    $data['dataAmount'][$key]['id_amount'] = $value['id'];
                    $data['dataAmount'][$key]['limit_sales'] = number_format($value['limit_sales']);
                    $data['dataAmount'][$key]['type_limit_discount'] = $value['type_limit_discount'];
                    $data['dataAmount'][$key]['limit_discount'] = number_format($value['limit_discount']);
                }
            }
        }
        //loại khuyến mãi theo bộ
        else if($data['dataMain']->type == 'item') {
            $data['dataAmount'] = array(); //bổ xung view loại 1
            $data['dataItem_gift'] = array(); //bổ xung view loại 3
            $data['dataType'] = '<span>'._l('promotion_by_item').'</span>';

            $dataItem = get_table_where('tblpromotion_item',array('promotion_id'=>$id));
            if(empty($dataItem)) {
                $data['dataItem'] = array();
            }
            else {
                foreach ($dataItem as $key => $value) {
                    if($value['type_item'] == 'items') {
                        $get_item = get_table_where('tblitems',array('id'=>$value['id_item']),'','row');
                        $data['dataItem'][$key]['id_item'] = $value['type_item'] .'_'. $get_item->id;
                        $data['dataItem'][$key]['name_item'] = $get_item->name .' - '. number_format($get_item->price);
                        $data['dataItem'][$key]['img_item'] = (!empty($get_item->avatar) && $get_item->avatar != '') ? $get_item->avatar : 'uploads/no-img.jpg';
                    }
                    else if($value['type_item'] == 'product') {
                        $get_item = get_table_where('tbl_products',array('id'=>$value['id_item']),'','row');
                        $data['dataItem'][$key]['id_item'] = $value['type_item'] .'_'. $get_item->id;
                        $data['dataItem'][$key]['name_item'] = $get_item->name .' - '. number_format($get_item->price_sell);
                        $data['dataItem'][$key]['img_item'] = (!empty($get_item->images) && $get_item->images != '') ? 'uploads/products/'.$get_item->images : 'uploads/no-img.jpg';
                    }
                    else if($value['type_item'] == 'nvl') {
                        $get_item = get_table_where('tbl_materials',array('id'=>$value['id_item']),'','row');
                        $data['dataItem'][$key]['id_item'] = $value['type_item'] .'_'. $get_item->id;
                        $data['dataItem'][$key]['name_item'] = $get_item->name .' - '. number_format($get_item->price_sell);
                        $data['dataItem'][$key]['img_item'] = (!empty($get_item->images) && $get_item->images != '') ? 'uploads/materials/'.$get_item->images : 'uploads/no-img.jpg';
                    }
                    else if($value['type_item'] == 'categoriesProducts') {
                        $get_item = get_table_where('tbl_category_products',array('id'=>$value['id_item']),'','row');
                        $data['dataItem'][$key]['id_item'] = $value['type_item'] .'_'. $get_item->id;
                        $data['dataItem'][$key]['name_item'] = '<span class="inline-block label label-warning">'._l('tnh_category_product').'</span> '.$get_item->name;
                        $data['dataItem'][$key]['img_item'] = '';
                    }
                    else if($value['type_item'] == 'categoriesItems') {
                        $get_item = get_table_where('tblcategories',array('id'=>$value['id_item']),'','row');
                        $data['dataItem'][$key]['id_item'] = $value['type_item'] .'_'. $get_item->id;
                        $data['dataItem'][$key]['name_item'] = '<span class="inline-block label label-warning">'._l('ch_categories').'</span> '.$get_item->category;
                        $data['dataItem'][$key]['img_item'] = '';
                    }

                    $data['dataItem'][$key]['quantity'] = number_format($value['quantity']);
                    $data['dataItem'][$key]['note'] = $value['note'];

                    $dataGift = get_table_where('tblpromotion_item_gift',array('promotion_id'=>$id,'promotion_item_id'=>$value['id_item'],'promotion_item_type'=>$value['type_item']));
                    if(empty($dataGift)) {
                        $data['dataItem'][$key]['dataGift'] = array();
                    }
                    else {
                        foreach ($dataGift as $key_Gift => $value_Gift) {
                            if($value_Gift['type_item'] == 'items') {
                                $get_item = get_table_where('tblitems',array('id'=>$value_Gift['id_item']),'','row');
                                $data['dataItem'][$key]['dataGift'][$key_Gift]['id_item'] = $value_Gift['type_item'] .'_'. $get_item->id;
                                $data['dataItem'][$key]['dataGift'][$key_Gift]['name_item'] = $get_item->name .' - '. number_format($get_item->price);
                                $data['dataItem'][$key]['dataGift'][$key_Gift]['img_item'] = (!empty($get_item->avatar) && $get_item->avatar != '') ? $get_item->avatar : 'uploads/no-img.jpg';
                            }
                            else if($value_Gift['type_item'] == 'product') {
                                $get_item = get_table_where('tbl_products',array('id'=>$value_Gift['id_item']),'','row');
                                $data['dataItem'][$key]['dataGift'][$key_Gift]['id_item'] = $value_Gift['type_item'] .'_'. $get_item->id;
                                $data['dataItem'][$key]['dataGift'][$key_Gift]['name_item'] = $get_item->name .' - '. number_format($get_item->price_sell);
                                $data['dataItem'][$key]['dataGift'][$key_Gift]['img_item'] = (!empty($get_item->images) && $get_item->images != '') ? 'uploads/products/'.$get_item->images : 'uploads/no-img.jpg';
                            }
                            else if($value_Gift['type_item'] == 'nvl') {
                                $get_item = get_table_where('tbl_materials',array('id'=>$value_Gift['id_item']),'','row');
                                $data['dataItem'][$key]['dataGift'][$key_Gift]['id_item'] = $value_Gift['type_item'] .'_'. $get_item->id;
                                $data['dataItem'][$key]['dataGift'][$key_Gift]['name_item'] = $get_item->name .' - '. number_format($get_item->price_sell);
                                $data['dataItem'][$key]['dataGift'][$key_Gift]['img_item'] = (!empty($get_item->images) && $get_item->images != '') ? 'uploads/materials/'.$get_item->images : 'uploads/no-img.jpg';
                            }
                            $data['dataItem'][$key]['dataGift'][$key_Gift]['quantity'] = number_format($value_Gift['quantity']);
                        }
                    }
                }
            }
        }
        //loại khuyến mãi theo doanh số
        else if($data['dataMain']->type == 'sales') {
            $data['dataSub'] = get_table_where('tblpromotion_sales',array('promotion_id'=>$id),'','row');
            $data['dataSub']->type_discount = ''; //bổ xung view loại 1
            $data['dataAmount'] = array(); //bổ xung view loại 1
            $data['dataGift'] = array(); //bổ xung view loại 2
            $data['dataType'] = '<span>'._l('promotion_by_sales').'</span>';

            $dataItem_gift = get_table_where('tblpromotion_sales_item_gift',array('promotion_id'=>$id));
            if(empty($dataItem_gift)) {
                $data['dataItem_gift'] = array();
            }
            else {
                foreach ($dataItem_gift as $key => $value) {
                    if($value['type_item'] == 'items') {
                        $get_item = get_table_where('tblitems',array('id'=>$value['id_item']),'','row');
                        $data['dataItem_gift'][$key]['id_item'] = $value['type_item'] .'_'. $get_item->id;
                        $data['dataItem_gift'][$key]['name_item'] = $get_item->name .' - '. number_format($get_item->price);
                        $data['dataItem_gift'][$key]['img_item'] = (!empty($get_item->avatar) && $get_item->avatar != '') ? $get_item->avatar : 'uploads/no-img.jpg';
                    }
                    else if($value['type_item'] == 'product') {
                        $get_item = get_table_where('tbl_products',array('id'=>$value['id_item']),'','row');
                        $data['dataItem_gift'][$key]['id_item'] = $value['type_item'] .'_'. $get_item->id;
                        $data['dataItem_gift'][$key]['name_item'] = $get_item->name .' - '. number_format($get_item->price_sell);
                        $data['dataItem_gift'][$key]['img_item'] = (!empty($get_item->images) && $get_item->images != '') ? 'uploads/products/'.$get_item->images : 'uploads/no-img.jpg';
                    }
                    else if($value['type_item'] == 'nvl') {
                        $get_item = get_table_where('tbl_materials',array('id'=>$value['id_item']),'','row');
                        $data['dataItem_gift'][$key]['id_item'] = $value['type_item'] .'_'. $get_item->id;
                        $data['dataItem_gift'][$key]['name_item'] = $get_item->name .' - '. number_format($get_item->price_sell);
                        $data['dataItem_gift'][$key]['img_item'] = (!empty($get_item->images) && $get_item->images != '') ? 'uploads/materials/'.$get_item->images : 'uploads/no-img.jpg';
                    }
                    $data['dataItem_gift'][$key]['quantity'] = number_format($value['quantity']);
                }
            }
        }
        
        $data['groups'] = get_table_where('tblcustomers_groups');
        $data['promotion_list'] = get_table_where('tblpromotion_list');
        $data['title'] = _l('view_promotion');
        $this->load->view('admin/promotion/view', $data);
    }

    public function view_result($id = '')
    {
        $dataMain = get_table_where('tblpromotion',array('id'=>$id),'','row');
        $type = '';
        if($dataMain->type == 'discount') {
            $type = _l('promotion_by_discount');
        }
        else if($dataMain->type == 'item') {
            $type = _l('promotion_by_item');
        }
        else if($dataMain->type == 'sales') {
            $type = _l('promotion_by_sales');
        }

        $data['dataResult'] = array();
        $key_main = 0;
        // type = discount
        if($dataMain->type == 'discount') {
            $data['typeResult'] = 'discount';
            $get_promotion_discount = get_table_where('tblpromotion_discount',array('promotion_id'=>$id),'','row');
            $get_promotion_discount_amount = get_table_where('tblpromotion_discount_amount',array('promotion_id'=>$id),'limit_sales DESC');
            //k xét riêng khách hàng
            if($dataMain->method_of_application != 'other') {
                //xét tất cả khách hàng
                if($dataMain->area_of_application == 'all') {
                    //xét tất cả sản phẩm - thành phẩm
                    if($get_promotion_discount->type_discount == 1) {
                        //lấy khách hàng có đơn thỏa mãn
                        $get_all_customer_order = get_table_where('tbl_orders',array('status'=>'approved','DATE(date) >='=>$get_promotion_discount->time_sales_start,'DATE(date) <='=>$get_promotion_discount->time_sales_end),'','result_array','customer_id');
                        foreach ($get_all_customer_order as $key => $value) {
                            //tổng đơn hàng của khách hàng
                            $get_customer_order = get_table_where('tbl_orders',array('status'=>'approved','DATE(date) >='=>$get_promotion_discount->time_sales_start,'DATE(date) <='=>$get_promotion_discount->time_sales_end,'customer_id'=>$value['customer_id']));
                            $total = 0;
                            foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                $total += $value_customer_order['grand_total_items'];
                            }
                            //kiểm tra giá trị khuyến mãi
                            $result_true = false;
                            foreach ($get_promotion_discount_amount as $key_promotion_discount_amount => $value_promotion_discount_amount) {
                                if($total >= $value_promotion_discount_amount['limit_sales']) {
                                    $result_true = true;
                                    $result_type = $value_promotion_discount_amount['type_limit_discount'];
                                    $result_discount = $value_promotion_discount_amount['limit_discount'];
                                    break;
                                }
                            }
                            if($result_true == true) {
                                $data['dataResult'][$key_main]['customer_name'] = $value['customer_name'];
                                $data['dataResult'][$key_main]['discount'] = number_format($result_discount).' '.$result_type;
                                foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['date_order'] = $value_customer_order['date'];
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['number_order'] = $value_customer_order['reference_no'];
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['list_items'] = _l('cong_all');
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['total'] = $value_customer_order['grand_total_items'];
                                }
                                $key_main++;
                            }
                        }
                    }
                    else if($get_promotion_discount->type_discount == 2) {
                        //lấy khách hàng có đơn thỏa mãn
                        $get_all_customer_order = get_table_where('tbl_orders',array('status'=>'approved','DATE(date) >='=>$get_promotion_discount->time_sales_start,'DATE(date) <='=>$get_promotion_discount->time_sales_end),'','result_array','customer_id');
                        foreach ($get_all_customer_order as $key => $value) {
                            //tổng đơn hàng của khách hàng
                            $get_customer_order = get_table_where('tbl_orders',array('status'=>'approved','DATE(date) >='=>$get_promotion_discount->time_sales_start,'DATE(date) <='=>$get_promotion_discount->time_sales_end,'customer_id'=>$value['customer_id']));
                            $total = 0;
                            foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                $get_items = get_table_where('tbl_order_items',array('order_id'=>$value_customer_order['id']));
                                foreach ($get_items as $key_items => $value_items) {
                                    if($value_items['type_item'] == 'products') {
                                        $get_product = get_table_where('tbl_products',array('id'=>$value_items['item_id']),'','row');
                                        if($get_product->calculated_on_sales == 1) {
                                            $total += $value_items['total_amount'];
                                        }
                                    }
                                    if($value_items['type_item'] == 'items') {
                                        $get_product = get_table_where('tblitems',array('id'=>$value_items['item_id']),'','row');
                                        if($get_product->calculated_on_sales == 1) {
                                            $total += $value_items['total_amount'];
                                        }
                                    }
                                }
                            }

                            //kiểm tra giá trị khuyến mãi
                            $result_true = false;
                            foreach ($get_promotion_discount_amount as $key_promotion_discount_amount => $value_promotion_discount_amount) {
                                if($total >= $value_promotion_discount_amount['limit_sales']) {
                                    $result_true = true;
                                    $result_type = $value_promotion_discount_amount['type_limit_discount'];
                                    $result_discount = $value_promotion_discount_amount['limit_discount'];
                                    break;
                                }
                            }
                            if($result_true == true) {
                                $data['dataResult'][$key_main]['customer_name'] = $value['customer_name'];
                                $data['dataResult'][$key_main]['discount'] = number_format($result_discount).' '.$result_type;

                                $list_item = '';
                                foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['date_order'] = $value_customer_order['date'];
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['number_order'] = $value_customer_order['reference_no'];

                                    $get_items = get_table_where('tbl_order_items',array('order_id'=>$value_customer_order['id']));
                                    foreach ($get_items as $key_items => $value_items) {
                                        if($value_items['type_item'] == 'products') {
                                            $get_product = get_table_where('tbl_products',array('id'=>$value_items['item_id']),'','row');
                                            if($get_product->calculated_on_sales == 1) {
                                                $list_item .= $get_product->name.' ('.$get_product->code.'), ';
                                            }
                                        }
                                        if($value_items['type_item'] == 'items') {
                                            $get_product = get_table_where('tblitems',array('id'=>$value_items['item_id']),'','row');
                                            if($get_product->calculated_on_sales == 1) {
                                                $list_item .= $get_product->name.' ('.$get_product->code.'), ';
                                            }
                                        }
                                    }
                                    $list_item = trim($list_item,', ');
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['list_items'] = $list_item;
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['total'] = $total;
                                }
                                $key_main++;
                            }
                        }
                    }
                }
                else if($dataMain->area_of_application == 'area') {
                    if(!empty($dataMain->groups_in) && $dataMain->groups_in != '') {
                        $arrID_group = explode(',', $dataMain->groups_in);
                        $arrID_customer = array();
                        foreach ($arrID_group as $key_group => $value_group) {
                            $get_group = get_table_where('tblcustomer_groups',array('groupid'=>$value_group));
                            foreach ($get_group as $k => $v) {
                                $arrID_customer[] = $v['customer_id'];
                            }
                        }
                        if($get_promotion_discount->type_discount == 1) {
                            //lấy khách hàng có đơn thỏa mãn
                            $this->db->select('tbl_orders.*');
                            $this->db->where('tbl_orders.status','approved');
                            $this->db->where('DATE(tbl_orders.date) >=',$get_promotion_discount->time_sales_start);
                            $this->db->where('DATE(tbl_orders.date) <=',$get_promotion_discount->time_sales_end);
                            $this->db->where_in('tbl_orders.customer_id',$arrID_customer);
                            $this->db->group_by('tbl_orders.customer_id');
                            $get_all_customer_order = $this->db->get('tbl_orders')->result_array();

                            foreach ($get_all_customer_order as $key => $value) {
                                //tổng đơn hàng của khách hàng
                                $get_customer_order = get_table_where('tbl_orders',array('status'=>'approved','DATE(date) >='=>$get_promotion_discount->time_sales_start,'DATE(date) <='=>$get_promotion_discount->time_sales_end,'customer_id'=>$value['customer_id']));
                                $total = 0;
                                foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                    $total += $value_customer_order['grand_total_items'];
                                }
                                //kiểm tra giá trị khuyến mãi
                                $result_true = false;
                                foreach ($get_promotion_discount_amount as $key_promotion_discount_amount => $value_promotion_discount_amount) {
                                    if($total >= $value_promotion_discount_amount['limit_sales']) {
                                        $result_true = true;
                                        $result_type = $value_promotion_discount_amount['type_limit_discount'];
                                        $result_discount = $value_promotion_discount_amount['limit_discount'];
                                        break;
                                    }
                                }
                                if($result_true == true) {
                                    $data['dataResult'][$key_main]['customer_name'] = $value['customer_name'];
                                    $data['dataResult'][$key_main]['discount'] = number_format($result_discount).' '.$result_type;
                                    foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['date_order'] = $value_customer_order['date'];
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['number_order'] = $value_customer_order['reference_no'];
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['list_items'] = _l('cong_all');
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['total'] = $value_customer_order['grand_total_items'];
                                    }
                                    $key_main++;
                                }
                            }
                        }
                        else if($get_promotion_discount->type_discount == 2) {
                            //lấy khách hàng có đơn thỏa mãn
                            $this->db->select('tbl_orders.*');
                            $this->db->where('tbl_orders.status','approved');
                            $this->db->where('DATE(tbl_orders.date) >=',$get_promotion_discount->time_sales_start);
                            $this->db->where('DATE(tbl_orders.date) <=',$get_promotion_discount->time_sales_end);
                            $this->db->where_in('tbl_orders.customer_id',$arrID_customer);
                            $this->db->group_by('tbl_orders.customer_id');
                            $get_all_customer_order = $this->db->get('tbl_orders')->result_array();

                            foreach ($get_all_customer_order as $key => $value) {
                                //tổng đơn hàng của khách hàng
                                $get_customer_order = get_table_where('tbl_orders',array('status'=>'approved','DATE(date) >='=>$get_promotion_discount->time_sales_start,'DATE(date) <='=>$get_promotion_discount->time_sales_end,'customer_id'=>$value['customer_id']));
                                $total = 0;
                                foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                    $get_items = get_table_where('tbl_order_items',array('order_id'=>$value_customer_order['id']));
                                    foreach ($get_items as $key_items => $value_items) {
                                        if($value_items['type_item'] == 'products') {
                                            $get_product = get_table_where('tbl_products',array('id'=>$value_items['item_id']),'','row');
                                            if($get_product->calculated_on_sales == 1) {
                                                $total += $value_items['total_amount'];
                                            }
                                        }
                                        if($value_items['type_item'] == 'items') {
                                            $get_product = get_table_where('tblitems',array('id'=>$value_items['item_id']),'','row');
                                            if($get_product->calculated_on_sales == 1) {
                                                $total += $value_items['total_amount'];
                                            }
                                        }
                                    }
                                }

                                //kiểm tra giá trị khuyến mãi
                                $result_true = false;
                                foreach ($get_promotion_discount_amount as $key_promotion_discount_amount => $value_promotion_discount_amount) {
                                    if($total >= $value_promotion_discount_amount['limit_sales']) {
                                        $result_true = true;
                                        $result_type = $value_promotion_discount_amount['type_limit_discount'];
                                        $result_discount = $value_promotion_discount_amount['limit_discount'];
                                        break;
                                    }
                                }
                                if($result_true == true) {
                                    $data['dataResult'][$key_main]['customer_name'] = $value['customer_name'];
                                    $data['dataResult'][$key_main]['discount'] = number_format($result_discount).' '.$result_type;

                                    $list_item = '';
                                    foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['date_order'] = $value_customer_order['date'];
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['number_order'] = $value_customer_order['reference_no'];

                                        $get_items = get_table_where('tbl_order_items',array('order_id'=>$value_customer_order['id']));
                                        foreach ($get_items as $key_items => $value_items) {
                                            if($value_items['type_item'] == 'products') {
                                                $get_product = get_table_where('tbl_products',array('id'=>$value_items['item_id']),'','row');
                                                if($get_product->calculated_on_sales == 1) {
                                                    $list_item .= $get_product->name.' ('.$get_product->code.'), ';
                                                }
                                            }
                                            if($value_items['type_item'] == 'items') {
                                                $get_product = get_table_where('tblitems',array('id'=>$value_items['item_id']),'','row');
                                                if($get_product->calculated_on_sales == 1) {
                                                    $list_item .= $get_product->name.' ('.$get_product->code.'), ';
                                                }
                                            }
                                        }
                                        $list_item = trim($list_item,', ');
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['list_items'] = $list_item;
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['total'] = $total;
                                    }
                                    $key_main++;
                                }
                            }
                        }
                    }
                }
                else if($dataMain->area_of_application == 'other') {
                    $arrID_customer = array();
                    $get_customer = get_table_where('tblpromotion_customer',array('promotion_id'=>$id));
                    foreach ($get_customer as $key_customer => $value_customer) {
                        $arrID_customer[] = $value_customer['customer_id'];
                    }
                    if($arrID_customer != array()) {
                        if($get_promotion_discount->type_discount == 1) {
                            //lấy khách hàng có đơn thỏa mãn
                            $this->db->select('tbl_orders.*');
                            $this->db->where('tbl_orders.status','approved');
                            $this->db->where('DATE(tbl_orders.date) >=',$get_promotion_discount->time_sales_start);
                            $this->db->where('DATE(tbl_orders.date) <=',$get_promotion_discount->time_sales_end);
                            $this->db->where_in('tbl_orders.customer_id',$arrID_customer);
                            $this->db->group_by('tbl_orders.customer_id');
                            $get_all_customer_order = $this->db->get('tbl_orders')->result_array();

                            foreach ($get_all_customer_order as $key => $value) {
                                //tổng đơn hàng của khách hàng
                                $get_customer_order = get_table_where('tbl_orders',array('status'=>'approved','DATE(date) >='=>$get_promotion_discount->time_sales_start,'DATE(date) <='=>$get_promotion_discount->time_sales_end,'customer_id'=>$value['customer_id']));
                                $total = 0;
                                foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                    $total += $value_customer_order['grand_total_items'];
                                }
                                //kiểm tra giá trị khuyến mãi
                                $result_true = false;
                                foreach ($get_promotion_discount_amount as $key_promotion_discount_amount => $value_promotion_discount_amount) {
                                    if($total >= $value_promotion_discount_amount['limit_sales']) {
                                        $result_true = true;
                                        $result_type = $value_promotion_discount_amount['type_limit_discount'];
                                        $result_discount = $value_promotion_discount_amount['limit_discount'];
                                        break;
                                    }
                                }
                                if($result_true == true) {
                                    $data['dataResult'][$key_main]['customer_name'] = $value['customer_name'];
                                    $data['dataResult'][$key_main]['discount'] = number_format($result_discount).' '.$result_type;
                                    foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['date_order'] = $value_customer_order['date'];
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['number_order'] = $value_customer_order['reference_no'];
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['list_items'] = _l('cong_all');
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['total'] = $value_customer_order['grand_total_items'];
                                    }
                                    $key_main++;
                                }
                            }
                        }
                        else if($get_promotion_discount->type_discount == 2) {
                            //lấy khách hàng có đơn thỏa mãn
                            $this->db->select('tbl_orders.*');
                            $this->db->where('tbl_orders.status','approved');
                            $this->db->where('DATE(tbl_orders.date) >=',$get_promotion_discount->time_sales_start);
                            $this->db->where('DATE(tbl_orders.date) <=',$get_promotion_discount->time_sales_end);
                            $this->db->where_in('tbl_orders.customer_id',$arrID_customer);
                            $this->db->group_by('tbl_orders.customer_id');
                            $get_all_customer_order = $this->db->get('tbl_orders')->result_array();
                            
                            foreach ($get_all_customer_order as $key => $value) {
                                //tổng đơn hàng của khách hàng
                                $get_customer_order = get_table_where('tbl_orders',array('status'=>'approved','DATE(date) >='=>$get_promotion_discount->time_sales_start,'DATE(date) <='=>$get_promotion_discount->time_sales_end,'customer_id'=>$value['customer_id']));
                                $total = 0;
                                foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                    $get_items = get_table_where('tbl_order_items',array('order_id'=>$value_customer_order['id']));
                                    foreach ($get_items as $key_items => $value_items) {
                                        if($value_items['type_item'] == 'products') {
                                            $get_product = get_table_where('tbl_products',array('id'=>$value_items['item_id']),'','row');
                                            if($get_product->calculated_on_sales == 1) {
                                                $total += $value_items['total_amount'];
                                            }
                                        }
                                        if($value_items['type_item'] == 'items') {
                                            $get_product = get_table_where('tblitems',array('id'=>$value_items['item_id']),'','row');
                                            if($get_product->calculated_on_sales == 1) {
                                                $total += $value_items['total_amount'];
                                            }
                                        }
                                    }
                                }

                                //kiểm tra giá trị khuyến mãi
                                $result_true = false;
                                foreach ($get_promotion_discount_amount as $key_promotion_discount_amount => $value_promotion_discount_amount) {
                                    if($total >= $value_promotion_discount_amount['limit_sales']) {
                                        $result_true = true;
                                        $result_type = $value_promotion_discount_amount['type_limit_discount'];
                                        $result_discount = $value_promotion_discount_amount['limit_discount'];
                                        break;
                                    }
                                }
                                if($result_true == true) {
                                    $data['dataResult'][$key_main]['customer_name'] = $value['customer_name'];
                                    $data['dataResult'][$key_main]['discount'] = number_format($result_discount).' '.$result_type;

                                    $list_item = '';
                                    foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['date_order'] = $value_customer_order['date'];
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['number_order'] = $value_customer_order['reference_no'];

                                        $get_items = get_table_where('tbl_order_items',array('order_id'=>$value_customer_order['id']));
                                        foreach ($get_items as $key_items => $value_items) {
                                            if($value_items['type_item'] == 'products') {
                                                $get_product = get_table_where('tbl_products',array('id'=>$value_items['item_id']),'','row');
                                                if($get_product->calculated_on_sales == 1) {
                                                    $list_item .= $get_product->name.' ('.$get_product->code.'), ';
                                                }
                                            }
                                            if($value_items['type_item'] == 'items') {
                                                $get_product = get_table_where('tblitems',array('id'=>$value_items['item_id']),'','row');
                                                if($get_product->calculated_on_sales == 1) {
                                                    $list_item .= $get_product->name.' ('.$get_product->code.'), ';
                                                }
                                            }
                                        }
                                        $list_item = trim($list_item,', ');
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['list_items'] = $list_item;
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['total'] = $total;
                                    }
                                    $key_main++;
                                }
                            }
                        }
                    }
                }
            }
            else if($dataMain->method_of_application == 'other') {
                $arrID_customer = array();
                $get_customer = get_table_where('tblpromotion_customer',array('promotion_id'=>$id));
                foreach ($get_customer as $key_customer => $value_customer) {
                    $arrID_customer[] = $value_customer['customer_id'];
                }
                if($arrID_customer != array()) {
                    if($get_promotion_discount->type_discount == 1) {
                        //lấy khách hàng có đơn thỏa mãn
                        $this->db->select('tbl_orders.*');
                        $this->db->where('tbl_orders.status','approved');
                        $this->db->where('DATE(tbl_orders.date) >=',$get_promotion_discount->time_sales_start);
                        $this->db->where('DATE(tbl_orders.date) <=',$get_promotion_discount->time_sales_end);
                        $this->db->where_in('tbl_orders.customer_id',$arrID_customer);
                        $this->db->group_by('tbl_orders.customer_id');
                        $get_all_customer_order = $this->db->get('tbl_orders')->result_array();

                        foreach ($get_all_customer_order as $key => $value) {
                            //tổng đơn hàng của khách hàng
                            $get_customer_order = get_table_where('tbl_orders',array('status'=>'approved','DATE(date) >='=>$get_promotion_discount->time_sales_start,'DATE(date) <='=>$get_promotion_discount->time_sales_end,'customer_id'=>$value['customer_id']));
                            $total = 0;
                            foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                $total += $value_customer_order['grand_total_items'];
                            }
                            //kiểm tra giá trị khuyến mãi
                            $result_true = false;
                            foreach ($get_promotion_discount_amount as $key_promotion_discount_amount => $value_promotion_discount_amount) {
                                if($total >= $value_promotion_discount_amount['limit_sales']) {
                                    $result_true = true;
                                    $result_type = $value_promotion_discount_amount['type_limit_discount'];
                                    $result_discount = $value_promotion_discount_amount['limit_discount'];
                                    break;
                                }
                            }
                            if($result_true == true) {
                                $data['dataResult'][$key_main]['customer_name'] = $value['customer_name'];
                                $data['dataResult'][$key_main]['discount'] = number_format($result_discount).' '.$result_type;
                                foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['date_order'] = $value_customer_order['date'];
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['number_order'] = $value_customer_order['reference_no'];
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['list_items'] = _l('cong_all');
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['total'] = $value_customer_order['grand_total_items'];
                                }
                                $key_main++;
                            }
                        }
                    }
                    else if($get_promotion_discount->type_discount == 2) {
                        //lấy khách hàng có đơn thỏa mãn
                        $this->db->select('tbl_orders.*');
                        $this->db->where('tbl_orders.status','approved');
                        $this->db->where('DATE(tbl_orders.date) >=',$get_promotion_discount->time_sales_start);
                        $this->db->where('DATE(tbl_orders.date) <=',$get_promotion_discount->time_sales_end);
                        $this->db->where_in('tbl_orders.customer_id',$arrID_customer);
                        $this->db->group_by('tbl_orders.customer_id');
                        $get_all_customer_order = $this->db->get('tbl_orders')->result_array();
                        
                        foreach ($get_all_customer_order as $key => $value) {
                            //tổng đơn hàng của khách hàng
                            $get_customer_order = get_table_where('tbl_orders',array('status'=>'approved','DATE(date) >='=>$get_promotion_discount->time_sales_start,'DATE(date) <='=>$get_promotion_discount->time_sales_end,'customer_id'=>$value['customer_id']));
                            $total = 0;
                            foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                $get_items = get_table_where('tbl_order_items',array('order_id'=>$value_customer_order['id']));
                                foreach ($get_items as $key_items => $value_items) {
                                    if($value_items['type_item'] == 'products') {
                                        $get_product = get_table_where('tbl_products',array('id'=>$value_items['item_id']),'','row');
                                        if($get_product->calculated_on_sales == 1) {
                                            $total += $value_items['total_amount'];
                                        }
                                    }
                                    if($value_items['type_item'] == 'items') {
                                        $get_product = get_table_where('tblitems',array('id'=>$value_items['item_id']),'','row');
                                        if($get_product->calculated_on_sales == 1) {
                                            $total += $value_items['total_amount'];
                                        }
                                    }
                                }
                            }

                            //kiểm tra giá trị khuyến mãi
                            $result_true = false;
                            foreach ($get_promotion_discount_amount as $key_promotion_discount_amount => $value_promotion_discount_amount) {
                                if($total >= $value_promotion_discount_amount['limit_sales']) {
                                    $result_true = true;
                                    $result_type = $value_promotion_discount_amount['type_limit_discount'];
                                    $result_discount = $value_promotion_discount_amount['limit_discount'];
                                    break;
                                }
                            }
                            if($result_true == true) {
                                $data['dataResult'][$key_main]['customer_name'] = $value['customer_name'];
                                $data['dataResult'][$key_main]['discount'] = number_format($result_discount).' '.$result_type;

                                $list_item = '';
                                foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['date_order'] = $value_customer_order['date'];
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['number_order'] = $value_customer_order['reference_no'];

                                    $get_items = get_table_where('tbl_order_items',array('order_id'=>$value_customer_order['id']));
                                    foreach ($get_items as $key_items => $value_items) {
                                        if($value_items['type_item'] == 'products') {
                                            $get_product = get_table_where('tbl_products',array('id'=>$value_items['item_id']),'','row');
                                            if($get_product->calculated_on_sales == 1) {
                                                $list_item .= $get_product->name.' ('.$get_product->code.'), ';
                                            }
                                        }
                                        if($value_items['type_item'] == 'items') {
                                            $get_product = get_table_where('tblitems',array('id'=>$value_items['item_id']),'','row');
                                            if($get_product->calculated_on_sales == 1) {
                                                $list_item .= $get_product->name.' ('.$get_product->code.'), ';
                                            }
                                        }
                                    }
                                    $list_item = trim($list_item,', ');
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['list_items'] = $list_item;
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['total'] = $total;
                                }
                                $key_main++;
                            }
                        }
                    }
                }
            }
        }
        // type = sales
        else if($dataMain->type == 'sales') {
            $data['typeResult'] = 'sales';
            $data['item_gift'] = array();
            $get_promotion_sales = get_table_where('tblpromotion_sales',array('promotion_id'=>$id),'','row');
            $get_promotion_sales_item_gift = get_table_where('tblpromotion_sales_item_gift',array('promotion_id'=>$id));
            foreach ($get_promotion_sales_item_gift as $key_item_gift => $value_item_gift) {
                if($value_item_gift['type_item'] == 'items') {
                    $get_item_gift = get_table_where('tblitems',array('id'=>$value_item_gift['id_item']),'','row');
                    $data['item_gift'][$key_item_gift]['name'] = $get_item_gift->name.' ('.$get_item_gift->code.')';
                    $data['item_gift'][$key_item_gift]['type'] = _l('ch_items');
                    $data['item_gift'][$key_item_gift]['quantity'] = number_format($value_item_gift['quantity']);
                }
                else if($value_item_gift['type_item'] == 'product') {
                    $get_item_gift = get_table_where('tbl_products',array('id'=>$value_item_gift['id_item']),'','row');
                    $data['item_gift'][$key_item_gift]['name'] = $get_item_gift->name.' ('.$get_item_gift->code.')';
                    $data['item_gift'][$key_item_gift]['type'] = _l('product');
                    $data['item_gift'][$key_item_gift]['quantity'] = number_format($value_item_gift['quantity']);
                }
            }

            if($dataMain->method_of_application != 'other') {
                if($dataMain->area_of_application == 'all') {
                    if($get_promotion_sales->type_sales == 1) {
                        $get_all_customer_order = get_table_where('tbl_orders',array('status'=>'approved','DATE(date) >='=>$get_promotion_sales->date_active_sales_start,'DATE(date) <='=>$get_promotion_sales->date_active_sales_end),'','result_array','customer_id');
                        foreach ($get_all_customer_order as $key => $value) {
                            //tổng đơn hàng của khách hàng
                            $get_customer_order = get_table_where('tbl_orders',array('status'=>'approved','DATE(date) >='=>$get_promotion_sales->date_active_sales_start,'DATE(date) <='=>$get_promotion_sales->date_active_sales_end,'customer_id'=>$value['customer_id']));
                            $total = 0;
                            foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                $total += $value_customer_order['grand_total_items'];
                            }

                            $result_true = false;
                            //áp dụng tiền
                            if($total >= $get_promotion_sales->limit_points) {
                                $result_true = true;
                                $result_type = $get_promotion_sales->type_limit_points;
                                $result_discount = $get_promotion_sales->limit_points;
                            }
                            if($result_true == true) {
                                $data['dataResult'][$key_main]['customer_name'] = $value['customer_name'];
                                foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['date_order'] = $value_customer_order['date'];
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['number_order'] = $value_customer_order['reference_no'];
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['list_items'] = _l('cong_all');
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['total'] = $value_customer_order['grand_total_items'];
                                }
                                $key_main++;
                            }
                        }
                    }
                    else if($get_promotion_sales->type_sales == 2) {
                        //lấy khách hàng có đơn thỏa mãn
                        $get_all_customer_order = get_table_where('tbl_orders',array('status'=>'approved','DATE(date) >='=>$get_promotion_sales->date_active_sales_start,'DATE(date) <='=>$get_promotion_sales->date_active_sales_end),'','result_array','customer_id');
                        foreach ($get_all_customer_order as $key => $value) {
                            //tổng đơn hàng của khách hàng
                            $get_customer_order = get_table_where('tbl_orders',array('status'=>'approved','DATE(date) >='=>$get_promotion_sales->date_active_sales_start,'DATE(date) <='=>$get_promotion_sales->date_active_sales_end,'customer_id'=>$value['customer_id']));
                            $total = 0;
                            foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                $get_items = get_table_where('tbl_order_items',array('order_id'=>$value_customer_order['id']));
                                foreach ($get_items as $key_items => $value_items) {
                                    if($value_items['type_item'] == 'products') {
                                        $get_product = get_table_where('tbl_products',array('id'=>$value_items['item_id']),'','row');
                                        if($get_product->calculated_on_sales == 1) {
                                            $total += $value_items['total_amount'];
                                        }
                                    }
                                    if($value_items['type_item'] == 'items') {
                                        $get_product = get_table_where('tblitems',array('id'=>$value_items['item_id']),'','row');
                                        if($get_product->calculated_on_sales == 1) {
                                            $total += $value_items['total_amount'];
                                        }
                                    }
                                }
                            }

                            //kiểm tra giá trị khuyến mãi
                            $result_true = false;
                            //áp dụng tiền
                            if($total >= $get_promotion_sales->limit_points) {
                                $result_true = true;
                                $result_type = $get_promotion_sales->type_limit_points;
                                $result_discount = $get_promotion_sales->limit_points;
                            }
                            if($result_true == true) {
                                $data['dataResult'][$key_main]['customer_name'] = $value['customer_name'];

                                $list_item = '';
                                foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['date_order'] = $value_customer_order['date'];
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['number_order'] = $value_customer_order['reference_no'];

                                    $get_items = get_table_where('tbl_order_items',array('order_id'=>$value_customer_order['id']));
                                    foreach ($get_items as $key_items => $value_items) {
                                        if($value_items['type_item'] == 'products') {
                                            $get_product = get_table_where('tbl_products',array('id'=>$value_items['item_id']),'','row');
                                            if($get_product->calculated_on_sales == 1) {
                                                $list_item .= $get_product->name.' ('.$get_product->code.'), ';
                                            }
                                        }
                                        if($value_items['type_item'] == 'items') {
                                            $get_product = get_table_where('tblitems',array('id'=>$value_items['item_id']),'','row');
                                            if($get_product->calculated_on_sales == 1) {
                                                $list_item .= $get_product->name.' ('.$get_product->code.'), ';
                                            }
                                        }
                                    }
                                    $list_item = trim($list_item,', ');
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['list_items'] = $list_item;
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['total'] = $total;
                                }
                                $key_main++;
                            }
                        }
                    }
                }
                else if($dataMain->area_of_application == 'area') {
                    if(!empty($dataMain->groups_in) && $dataMain->groups_in != '') {
                        $arrID_group = explode(',', $dataMain->groups_in);
                        $arrID_customer = array();
                        foreach ($arrID_group as $key_group => $value_group) {
                            $get_group = get_table_where('tblcustomer_groups',array('groupid'=>$value_group));
                            foreach ($get_group as $k => $v) {
                                $arrID_customer[] = $v['customer_id'];
                            }
                        }
                        if($get_promotion_sales->type_sales == 1) {
                            //lấy khách hàng có đơn thỏa mãn
                            $this->db->select('tbl_orders.*');
                            $this->db->where('tbl_orders.status','approved');
                            $this->db->where('DATE(tbl_orders.date) >=',$get_promotion_sales->date_active_sales_start);
                            $this->db->where('DATE(tbl_orders.date) <=',$get_promotion_sales->date_active_sales_end);
                            $this->db->where_in('tbl_orders.customer_id',$arrID_customer);
                            $this->db->group_by('tbl_orders.customer_id');
                            $get_all_customer_order = $this->db->get('tbl_orders')->result_array();

                            foreach ($get_all_customer_order as $key => $value) {
                                //tổng đơn hàng của khách hàng
                                $get_customer_order = get_table_where('tbl_orders',array('status'=>'approved','DATE(date) >='=>$get_promotion_sales->date_active_sales_start,'DATE(date) <='=>$get_promotion_sales->date_active_sales_end,'customer_id'=>$value['customer_id']));
                                $total = 0;
                                foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                    $total += $value_customer_order['grand_total_items'];
                                }

                                $result_true = false;
                                //áp dụng tiền
                                if($total >= $get_promotion_sales->limit_points) {
                                    $result_true = true;
                                    $result_type = $get_promotion_sales->type_limit_points;
                                    $result_discount = $get_promotion_sales->limit_points;
                                }
                                if($result_true == true) {
                                    $data['dataResult'][$key_main]['customer_name'] = $value['customer_name'];
                                    foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['date_order'] = $value_customer_order['date'];
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['number_order'] = $value_customer_order['reference_no'];
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['list_items'] = _l('cong_all');
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['total'] = $value_customer_order['grand_total_items'];
                                    }
                                    $key_main++;
                                }
                            }
                        }
                        else if($get_promotion_sales->type_sales == 2) {
                            //lấy khách hàng có đơn thỏa mãn
                            $this->db->select('tbl_orders.*');
                            $this->db->where('tbl_orders.status','approved');
                            $this->db->where('DATE(tbl_orders.date) >=',$get_promotion_sales->date_active_sales_start);
                            $this->db->where('DATE(tbl_orders.date) <=',$get_promotion_sales->date_active_sales_end);
                            $this->db->where_in('tbl_orders.customer_id',$arrID_customer);
                            $this->db->group_by('tbl_orders.customer_id');
                            $get_all_customer_order = $this->db->get('tbl_orders')->result_array();

                            foreach ($get_all_customer_order as $key => $value) {
                                //tổng đơn hàng của khách hàng
                                $get_customer_order = get_table_where('tbl_orders',array('status'=>'approved','DATE(date) >='=>$get_promotion_sales->date_active_sales_start,'DATE(date) <='=>$get_promotion_sales->date_active_sales_end,'customer_id'=>$value['customer_id']));
                                $total = 0;
                                foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                    $get_items = get_table_where('tbl_order_items',array('order_id'=>$value_customer_order['id']));
                                    foreach ($get_items as $key_items => $value_items) {
                                        if($value_items['type_item'] == 'products') {
                                            $get_product = get_table_where('tbl_products',array('id'=>$value_items['item_id']),'','row');
                                            if($get_product->calculated_on_sales == 1) {
                                                $total += $value_items['total_amount'];
                                            }
                                        }
                                        if($value_items['type_item'] == 'items') {
                                            $get_product = get_table_where('tblitems',array('id'=>$value_items['item_id']),'','row');
                                            if($get_product->calculated_on_sales == 1) {
                                                $total += $value_items['total_amount'];
                                            }
                                        }
                                    }
                                }

                                //kiểm tra giá trị khuyến mãi
                                $result_true = false;
                                //áp dụng tiền
                                if($total >= $get_promotion_sales->limit_points) {
                                    $result_true = true;
                                    $result_type = $get_promotion_sales->type_limit_points;
                                    $result_discount = $get_promotion_sales->limit_points;
                                }
                                if($result_true == true) {
                                    $data['dataResult'][$key_main]['customer_name'] = $value['customer_name'];

                                    $list_item = '';
                                    foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['date_order'] = $value_customer_order['date'];
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['number_order'] = $value_customer_order['reference_no'];

                                        $get_items = get_table_where('tbl_order_items',array('order_id'=>$value_customer_order['id']));
                                        foreach ($get_items as $key_items => $value_items) {
                                            if($value_items['type_item'] == 'products') {
                                                $get_product = get_table_where('tbl_products',array('id'=>$value_items['item_id']),'','row');
                                                if($get_product->calculated_on_sales == 1) {
                                                    $list_item .= $get_product->name.' ('.$get_product->code.'), ';
                                                }
                                            }
                                            if($value_items['type_item'] == 'items') {
                                                $get_product = get_table_where('tblitems',array('id'=>$value_items['item_id']),'','row');
                                                if($get_product->calculated_on_sales == 1) {
                                                    $list_item .= $get_product->name.' ('.$get_product->code.'), ';
                                                }
                                            }
                                        }
                                        $list_item = trim($list_item,', ');
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['list_items'] = $list_item;
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['total'] = $total;
                                    }
                                    $key_main++;
                                }
                            }
                        }
                    }
                }
                else if($dataMain->area_of_application == 'other') {
                    $arrID_customer = array();
                    $get_customer = get_table_where('tblpromotion_customer',array('promotion_id'=>$id));
                    foreach ($get_customer as $key_customer => $value_customer) {
                        $arrID_customer[] = $value_customer['customer_id'];
                    }
                    if($arrID_customer != array()) {
                        if($get_promotion_sales->type_sales == 1) {
                            //lấy khách hàng có đơn thỏa mãn
                            $this->db->select('tbl_orders.*');
                            $this->db->where('tbl_orders.status','approved');
                            $this->db->where('DATE(tbl_orders.date) >=',$get_promotion_sales->date_active_sales_start);
                            $this->db->where('DATE(tbl_orders.date) <=',$get_promotion_sales->date_active_sales_end);
                            $this->db->where_in('tbl_orders.customer_id',$arrID_customer);
                            $this->db->group_by('tbl_orders.customer_id');
                            $get_all_customer_order = $this->db->get('tbl_orders')->result_array();

                            foreach ($get_all_customer_order as $key => $value) {
                                //tổng đơn hàng của khách hàng
                                $get_customer_order = get_table_where('tbl_orders',array('status'=>'approved','DATE(date) >='=>$get_promotion_sales->date_active_sales_start,'DATE(date) <='=>$get_promotion_sales->date_active_sales_end,'customer_id'=>$value['customer_id']));
                                $total = 0;
                                foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                    $total += $value_customer_order['grand_total_items'];
                                }

                                $result_true = false;
                                //áp dụng tiền
                                if($total >= $get_promotion_sales->limit_points) {
                                    $result_true = true;
                                    $result_type = $get_promotion_sales->type_limit_points;
                                    $result_discount = $get_promotion_sales->limit_points;
                                }
                                if($result_true == true) {
                                    $data['dataResult'][$key_main]['customer_name'] = $value['customer_name'];
                                    foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['date_order'] = $value_customer_order['date'];
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['number_order'] = $value_customer_order['reference_no'];
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['list_items'] = _l('cong_all');
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['total'] = $value_customer_order['grand_total_items'];
                                    }
                                    $key_main++;
                                }
                            }
                        }
                        else if($get_promotion_sales->type_sales == 2) {
                            //lấy khách hàng có đơn thỏa mãn
                            $this->db->select('tbl_orders.*');
                            $this->db->where('tbl_orders.status','approved');
                            $this->db->where('DATE(tbl_orders.date) >=',$get_promotion_sales->date_active_sales_start);
                            $this->db->where('DATE(tbl_orders.date) <=',$get_promotion_sales->date_active_sales_end);
                            $this->db->where_in('tbl_orders.customer_id',$arrID_customer);
                            $this->db->group_by('tbl_orders.customer_id');
                            $get_all_customer_order = $this->db->get('tbl_orders')->result_array();

                            foreach ($get_all_customer_order as $key => $value) {
                                //tổng đơn hàng của khách hàng
                                $get_customer_order = get_table_where('tbl_orders',array('status'=>'approved','DATE(date) >='=>$get_promotion_sales->date_active_sales_start,'DATE(date) <='=>$get_promotion_sales->date_active_sales_end,'customer_id'=>$value['customer_id']));
                                $total = 0;
                                foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                    $get_items = get_table_where('tbl_order_items',array('order_id'=>$value_customer_order['id']));
                                    foreach ($get_items as $key_items => $value_items) {
                                        if($value_items['type_item'] == 'products') {
                                            $get_product = get_table_where('tbl_products',array('id'=>$value_items['item_id']),'','row');
                                            if($get_product->calculated_on_sales == 1) {
                                                $total += $value_items['total_amount'];
                                            }
                                        }
                                        if($value_items['type_item'] == 'items') {
                                            $get_product = get_table_where('tblitems',array('id'=>$value_items['item_id']),'','row');
                                            if($get_product->calculated_on_sales == 1) {
                                                $total += $value_items['total_amount'];
                                            }
                                        }
                                    }
                                }

                                //kiểm tra giá trị khuyến mãi
                                $result_true = false;
                                //áp dụng tiền
                                if($total >= $get_promotion_sales->limit_points) {
                                    $result_true = true;
                                    $result_type = $get_promotion_sales->type_limit_points;
                                    $result_discount = $get_promotion_sales->limit_points;
                                }
                                if($result_true == true) {
                                    $data['dataResult'][$key_main]['customer_name'] = $value['customer_name'];

                                    $list_item = '';
                                    foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['date_order'] = $value_customer_order['date'];
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['number_order'] = $value_customer_order['reference_no'];

                                        $get_items = get_table_where('tbl_order_items',array('order_id'=>$value_customer_order['id']));
                                        foreach ($get_items as $key_items => $value_items) {
                                            if($value_items['type_item'] == 'products') {
                                                $get_product = get_table_where('tbl_products',array('id'=>$value_items['item_id']),'','row');
                                                if($get_product->calculated_on_sales == 1) {
                                                    $list_item .= $get_product->name.' ('.$get_product->code.'), ';
                                                }
                                            }
                                            if($value_items['type_item'] == 'items') {
                                                $get_product = get_table_where('tblitems',array('id'=>$value_items['item_id']),'','row');
                                                if($get_product->calculated_on_sales == 1) {
                                                    $list_item .= $get_product->name.' ('.$get_product->code.'), ';
                                                }
                                            }
                                        }
                                        $list_item = trim($list_item,', ');
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['list_items'] = $list_item;
                                        $data['dataResult'][$key_main]['order'][$key_customer_order]['total'] = $total;
                                    }
                                    $key_main++;
                                }
                            }
                        }
                    }
                }
            }
            else if($dataMain->method_of_application == 'other') {
                $arrID_customer = array();
                $get_customer = get_table_where('tblpromotion_customer',array('promotion_id'=>$id));
                foreach ($get_customer as $key_customer => $value_customer) {
                    $arrID_customer[] = $value_customer['customer_id'];
                }
                if($arrID_customer != array()) {
                    if($get_promotion_sales->type_sales == 1) {
                        //lấy khách hàng có đơn thỏa mãn
                        $this->db->select('tbl_orders.*');
                        $this->db->where('tbl_orders.status','approved');
                        $this->db->where('DATE(tbl_orders.date) >=',$get_promotion_sales->date_active_sales_start);
                        $this->db->where('DATE(tbl_orders.date) <=',$get_promotion_sales->date_active_sales_end);
                        $this->db->where_in('tbl_orders.customer_id',$arrID_customer);
                        $this->db->group_by('tbl_orders.customer_id');
                        $get_all_customer_order = $this->db->get('tbl_orders')->result_array();

                        foreach ($get_all_customer_order as $key => $value) {
                            //tổng đơn hàng của khách hàng
                            $get_customer_order = get_table_where('tbl_orders',array('status'=>'approved','DATE(date) >='=>$get_promotion_sales->date_active_sales_start,'DATE(date) <='=>$get_promotion_sales->date_active_sales_end,'customer_id'=>$value['customer_id']));
                            $total = 0;
                            foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                $total += $value_customer_order['grand_total_items'];
                            }

                            $result_true = false;
                            //áp dụng tiền
                            if($total >= $get_promotion_sales->limit_points) {
                                $result_true = true;
                                $result_type = $get_promotion_sales->type_limit_points;
                                $result_discount = $get_promotion_sales->limit_points;
                            }
                            if($result_true == true) {
                                $data['dataResult'][$key_main]['customer_name'] = $value['customer_name'];
                                foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['date_order'] = $value_customer_order['date'];
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['number_order'] = $value_customer_order['reference_no'];
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['list_items'] = _l('cong_all');
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['total'] = $value_customer_order['grand_total_items'];
                                }
                                $key_main++;
                            }
                        }
                    }
                    else if($get_promotion_sales->type_sales == 2) {
                        //lấy khách hàng có đơn thỏa mãn
                        $this->db->select('tbl_orders.*');
                        $this->db->where('tbl_orders.status','approved');
                        $this->db->where('DATE(tbl_orders.date) >=',$get_promotion_sales->date_active_sales_start);
                        $this->db->where('DATE(tbl_orders.date) <=',$get_promotion_sales->date_active_sales_end);
                        $this->db->where_in('tbl_orders.customer_id',$arrID_customer);
                        $this->db->group_by('tbl_orders.customer_id');
                        $get_all_customer_order = $this->db->get('tbl_orders')->result_array();

                        foreach ($get_all_customer_order as $key => $value) {
                            //tổng đơn hàng của khách hàng
                            $get_customer_order = get_table_where('tbl_orders',array('status'=>'approved','DATE(date) >='=>$get_promotion_sales->date_active_sales_start,'DATE(date) <='=>$get_promotion_sales->date_active_sales_end,'customer_id'=>$value['customer_id']));
                            $total = 0;
                            foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                $get_items = get_table_where('tbl_order_items',array('order_id'=>$value_customer_order['id']));
                                foreach ($get_items as $key_items => $value_items) {
                                    if($value_items['type_item'] == 'products') {
                                        $get_product = get_table_where('tbl_products',array('id'=>$value_items['item_id']),'','row');
                                        if($get_product->calculated_on_sales == 1) {
                                            $total += $value_items['total_amount'];
                                        }
                                    }
                                    if($value_items['type_item'] == 'items') {
                                        $get_product = get_table_where('tblitems',array('id'=>$value_items['item_id']),'','row');
                                        if($get_product->calculated_on_sales == 1) {
                                            $total += $value_items['total_amount'];
                                        }
                                    }
                                }
                            }

                            //kiểm tra giá trị khuyến mãi
                            $result_true = false;
                            //áp dụng tiền
                            if($total >= $get_promotion_sales->limit_points) {
                                $result_true = true;
                                $result_type = $get_promotion_sales->type_limit_points;
                                $result_discount = $get_promotion_sales->limit_points;
                            }
                            if($result_true == true) {
                                $data['dataResult'][$key_main]['customer_name'] = $value['customer_name'];

                                $list_item = '';
                                foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['date_order'] = $value_customer_order['date'];
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['number_order'] = $value_customer_order['reference_no'];

                                    $get_items = get_table_where('tbl_order_items',array('order_id'=>$value_customer_order['id']));
                                    foreach ($get_items as $key_items => $value_items) {
                                        if($value_items['type_item'] == 'products') {
                                            $get_product = get_table_where('tbl_products',array('id'=>$value_items['item_id']),'','row');
                                            if($get_product->calculated_on_sales == 1) {
                                                $list_item .= $get_product->name.' ('.$get_product->code.'), ';
                                            }
                                        }
                                        if($value_items['type_item'] == 'items') {
                                            $get_product = get_table_where('tblitems',array('id'=>$value_items['item_id']),'','row');
                                            if($get_product->calculated_on_sales == 1) {
                                                $list_item .= $get_product->name.' ('.$get_product->code.'), ';
                                            }
                                        }
                                    }
                                    $list_item = trim($list_item,', ');
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['list_items'] = $list_item;
                                    $data['dataResult'][$key_main]['order'][$key_customer_order]['total'] = $total;
                                }
                                $key_main++;
                            }
                        }
                    }
                }
            }
        }
        // end
        $data['title'] = $dataMain->name.' ('.$type.')';
        $this->load->view('admin/promotion/view_result', $data);
    }
}