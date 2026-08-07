<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Delivery_policy extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        if (!has_permission('delivery_policy', '', 'view') && !has_permission('delivery_policy', '', 'view_own')) {
            access_denied('delivery_policy');
        }
        $checkExists = get_table_where('tbldelivery_policy_market',array(),'','row');
        $checkExists2 = get_table_where('tbldelivery_policy_products',array(),'','row');
        if ($checkExists || $checkExists2) {
            $this->edit();
        }
        else {
            $this->add();
        }
    }

    public function add()
    {
        $checkExists = get_table_where('tbldelivery_policy_market',array(),'','row');
        $checkExists2 = get_table_where('tbldelivery_policy_products',array(),'','row');
        if ($checkExists || $checkExists2) {
            set_alert('danger', _l('dont_add_delivery_policy'));
            redirect(admin_url('delivery_policy'));
        }
        if($this->input->post()) {
            $data_post = $this->input->post();
            if(isset($data_post['market'])) {
                foreach ($data_post['market'] as $key => $value) {
                    $in = array(
                        'id_province'=>$value['id_city'],
                        'amount'=>str_replace(',', "", $value['amount']),
                    );
                    $this->db->insert('tbldelivery_policy_market', $in);
                }
            }
            if(isset($data_post['products'])) {
                foreach ($data_post['products'] as $key => $value) {
                    $in = array(
                        'id_categories'=>$value['id_group'],
                        'percent'=>str_replace(',', "", $value['percent']),
                    );
                    $this->db->insert('tbldelivery_policy_products', $in);
                }
            }
            set_alert('success', _l('delivery_policy_success'));
            redirect(admin_url('delivery_policy'));
        }
        $data['title'] = _l('delivery_policy');

        $default_country  = get_option('customer_default_country');
        $data['province'] = get_table_where('province', array('countries' => $default_country ));

        $data['group_product'] = get_table_where('tblcategories', array('category_parent' => 0));
        $this->load->view('admin/delivery_policy/add', $data);
    }

    public function edit()
    {
        $checkExists = get_table_where('tbldelivery_policy_market',array(),'','row');
        $checkExists2 = get_table_where('tbldelivery_policy_products',array(),'','row');
        if (!$checkExists && !$checkExists2) {
            set_alert('danger', _l('dont_update_delivery_policy'));
            redirect(admin_url('delivery_policy'));
        }
        if($this->input->post()) {
            $data_post = $this->input->post();
            if(isset($data_post['market'])) {
                $arrID_market = array();
                foreach ($data_post['market'] as $key => $value) {
                    $in = array(
                        'id_province'=>$value['id_city'],
                        'amount'=>str_replace(',', "", $value['amount']),
                    );
                    $check = get_table_where('tbldelivery_policy_market',array('id_province'=>$value['id_city']),'','row');
                    if($check) {
                        $this->db->where('id', $check->id);
                        $this->db->update('tbldelivery_policy_market', $in);
                        $arrID_market[] = $check->id;
                    }
                    else {
                        $this->db->insert('tbldelivery_policy_market', $in);
                        $arrID_market[] = $this->db->insert_id();
                    }
                }
                //xóa
                if($arrID_market != array()) {
                    $this->db->where_not_in('id', $arrID_market);
                    $this->db->delete('tbldelivery_policy_market');
                }
            }
            else {
                $this->db->empty_table('tbldelivery_policy_market');
            }

            if(isset($data_post['products'])) {
                $arrID_products = array();
                foreach ($data_post['products'] as $key => $value) {
                    $in = array(
                        'id_categories'=>$value['id_group'],
                        'percent'=>str_replace(',', "", $value['percent']),
                    );
                    $check = get_table_where('tbldelivery_policy_products',array('id_categories'=>$value['id_group']),'','row');
                    if($check) {
                        $this->db->where('id', $check->id);
                        $this->db->update('tbldelivery_policy_products', $in);
                        $arrID_products[] = $check->id;
                    }
                    else {
                        $this->db->insert('tbldelivery_policy_products', $in);
                        $arrID_products[] = $this->db->insert_id();
                    }
                }
                //xóa
                if($arrID_products != array()) {
                    $this->db->where_not_in('id', $arrID_products);
                    $this->db->delete('tbldelivery_policy_products');
                }
            }
            else {
                $this->db->empty_table('tbldelivery_policy_products');
            }

            set_alert('success', _l('update_delivery_policy_success'));
            redirect(admin_url('delivery_policy'));
        }
        $data['title'] = _l('delivery_policy');

        $default_country  = get_option('customer_default_country');
        $data['province'] = get_table_where('province', array('countries' => $default_country ));

        $data['group_product'] = get_table_where('tblcategories', array('category_parent' => 0));

        $get_data_market = get_table_where('tbldelivery_policy_market');
        if($get_data_market) {
            foreach ($get_data_market as $key => $value) {
                $data['data_market'][$key]['amount'] = number_format($value['amount']);
                $data['data_market'][$key]['id_province'] = $value['id_province'];
                $data['data_market'][$key]['name_province'] = get_table_where('tblprovince',array('provinceid'=>$value['id_province']),'','row')->name;
            }
        }
        else {
            $data['data_market'] = array();
        }

        $get_data_products = get_table_where('tbldelivery_policy_products');
        if($get_data_products) {
            foreach ($get_data_products as $key => $value) {
                $data['data_products'][$key]['percent'] = number_format($value['percent']);
                $data['data_products'][$key]['id_categories'] = $value['id_categories'];
                $data['data_products'][$key]['name_categories'] = get_table_where('tblcategories',array('id'=>$value['id_categories']),'','row')->category;
            }
        }
        else {
            $data['data_products'] = array();
        }

        $this->load->view('admin/delivery_policy/edit', $data);
    }
}