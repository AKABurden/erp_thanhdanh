<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Discount extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('discount_model');
    }
    public function index()
    {
        if (!has_permission('discount', '', 'view')) {
                access_denied('Discount');
        }
        $data['title']          = _l('ch_discount');
        $this->load->view('admin/discount/manage', $data);
    }
    public function update_status()
    {
        if ($this->input->post()) {
            $id=$this->input->post('id');
            $status=$this->input->post('status');
            $other_payslips = get_table_where('tbldiscount',array('id'=>$id),'','row');
            if($other_payslips->status == 1)
            {
                die;
            }
            $staff_id=get_staff_user_id();
            $date=date('Y-m-d H:i:s');
            $history_status=$staff_id.','.$date;
            $data =array(
                'history_status'=>$history_status,
                'status' => ($status+1),
            );
            $success=$this->db->update('tbldiscount',$data,array('id'=>$id));
        }
        if($success) {
            echo json_encode(array(
                'success' => $success,
                'alert_type' => 'success',
                'message' => _l('ch_successful_approval')
            ));
        }
        else
        {
            echo json_encode(array(
                'success' => $success,
                'alert_type' => 'danger',
                'message' => _l('ch_no_successful_approval')
            ));
        }
        die;
    }
  	public function table()
    {
        if (!has_permission('discount', '', 'view')) {
                ajax_access_denied();
        }
        $this->app->get_table_data('discount');
    }
    public function payment($id = '')
    {
        if (!has_permission('discount', '', 'create')) {
                ajax_access_denied();
        }
        if ($this->input->post()) {
            if ($id == '') {

                if (!has_permission('discount', '', 'create')) {
                    access_denied('discount');
                }
                $data                 = $this->input->post();

                $data['note'] = $this->input->post('note',true);
                $data['name_discount'] = $this->input->post('name_discount',true);

                if(isset($data['items']) && count($data['items']) > 0)
                {
                    $id = $this->discount_model->add_payment($data,2);
                }
                
                if ($id) {
                    set_alert('success', _l('ch_added_successfuly'));
                    redirect(admin_url('discount'));
                }
            } else {
                if (!has_permission('discount', '', 'edit')) {
                        access_denied('discount');
                }
                $data                 = $this->input->post();
                $data['note'] = $this->input->post('note',true);
                $success = $this->discount_model->update_payment($data, $id);
                if ($success == true) {
                    set_alert('success', _l('ch_updated_successfuly'));
                }
                redirect(admin_url('discount/payment/' . $id));
            }
            }
            if($id != '')
            {
                $data['title']          = _l('ch_edit_discounts'); 
                $data['discount'] = $this->discount_model->get_payment($id);
            }else
            {
                $data['title']          = _l('ch_add_discounts');  
            }
            $data['market'] = get_table_where('tblcustomers_groups');
            
            $data['payment'] = get_table_where('tblpayment_time_level');
            
            $this->load->view('admin/discount/detail_payment', $data);
        }
    public function trade($id = '')
    {
        if (!has_permission('discount', '', 'create')) {
                ajax_access_denied();
        }
        if ($this->input->post()) {
            if ($id == '') {

                if (!has_permission('discount', '', 'create')) {
                    access_denied('discount');
                }
                $data                 = $this->input->post();
                $data['note'] = $this->input->post('note',true);
                $data['name_discount'] = $this->input->post('name_discount',true);

                if(isset($data['items']) && count($data['items']) > 0)
                {
                    $id = $this->discount_model->add($data,1);
                }
                
                if ($id) {
                    set_alert('success', _l('ch_added_successfuly'));
                    redirect(admin_url('discount'));
                }
            } else {
                if (!has_permission('discount', '', 'edit')) {
                        access_denied('discount');
                }
                $data                 = $this->input->post();
                $data['note'] = $this->input->post('note',true);
                $success = $this->discount_model->update($data, $id);
                if ($success == true) {
                    set_alert('success', _l('ch_updated_successfuly'));
                }
                redirect(admin_url('discount/trade/' . $id));
            }
            }
            if($id != '')
            {
                $data['title']          = _l('ch_edit_discounts'); 
                $data['discount'] = $this->discount_model->get($id);
            }else
            {
                $data['title']          = _l('ch_add_discounts');  
            }
            $data['market'] = get_table_where('tblcustomers_groups');
            
            $data['categories'] = $this->categories_id();
            $data['categories_product'] = $this->categories_product_id();
            
            $this->load->view('admin/discount/detail', $data);
        }
        function categories_product_id()
        {
                $data = array();
                $categories = get_table_where('tbl_category_products',array('parent_id'=>0));
                $level = 1;
                $class = '';
                $idd = '';
                foreach ($categories as $key => $value) {
                    $idd = $value['id'];
                    $class='parent_'.$value['id'];
                    $categories[$key]['class'] = $class;
                    $categories[$key]['idd'] = $idd;
                    $categories[$key]['lever'] = $level;
                    $data[] = $categories[$key];
                    $_data =get_table_where('tbl_category_products',array('parent_id'=>$value['id']));
                    if($_data)
                    {
                    $this->get_categories_products_id($data,$value['id'],($level + 1),'',$class,$idd);
                    }else
                    {

                       $class='';
                       continue;
                    }
                }
                // echo '<pre>';
                // var_dump($data);die;
                return $data;
        }  
        function get_categories_products_id(&$data,$id,$level,$html='',$class='',$idd)
        {
            $classs = $class;
            $iddd = $idd;
            $html = $html.'&nbsp;<i class="lnr lnr-exit" style="color: #ff6f00"></i>&nbsp;';
            $categories = get_table_where('tbl_category_products',array('parent_id'=>$id));
            foreach ($categories as $key => $value) {
                $idd=$value['id'].','.$iddd;
                $class='parent_'.$value['id'].' '.$classs;
                $categories[$key]['class'] = $class;
                $categories[$key]['idd'] = $idd;
                $categories[$key]['name'] = $html.$value['name'];
                $categories[$key]['lever'] = $level;
                $data[] = $categories[$key];
                $_data =get_table_where('tbl_category_products',array('parent_id'=>$value['id']));
                if($_data)
                {

                $this->get_categories_id($data,$value['id'],($level + 1),'<i class="lnr lnr-exit" style="color: #ff6f00"></i>',$class,$idd);
                }else
                {

                    $class = $classs;
                    $idd = $iddd;
                    continue;
                }
            }
        }                
        function categories_id()
        {
                $data = array();
                $categories = get_table_where('tblcategories',array('category_parent'=>0));
                $level = 1;
                $class = '';
                $idd = '';
                foreach ($categories as $key => $value) {
                    $idd = $value['id'];
                    $class='parent_'.$value['id'];
                    $categories[$key]['class'] = $class;
                    $categories[$key]['idd'] = $idd;
                    $categories[$key]['lever'] = $level;
                    $data[] = $categories[$key];
                    $_data =get_table_where('tblcategories',array('category_parent'=>$value['id']));
                    if($_data)
                    {
                    $this->get_categories_id($data,$value['id'],($level + 1),'',$class,$idd);
                    }else
                    {

                       $class='';
                       continue;
                    }
                }
                // echo '<pre>';
                // var_dump($data);die;
                return $data;
        }  
        public function SearchClient()
        {
            $data = [];
            $type = $this->input->get('type');
            if(!empty($type)){
            $search = $this->input->get('term');
                $this->db->select('
                        userid as id,
                        tblclients.company as text,
                        CONCAT(prefix_client,code_client) as code_clients,
                        tblclients.address as address,
                        tblclients.phonenumber as phonenumber,
                        '
                , false);
                if (!empty($search))
                {
                    $this->db->group_start();
                    $this->db->like('CONCAT(prefix_client,code_client)', $search);
                    $this->db->or_like('tblclients.company', $search);
                    $this->db->or_like('tblclients.address', $search);
                    $this->db->or_like('tblclients.phonenumber', $search);
                    $this->db->group_end();
                }
                $this->db->join('tblcustomer_groups','tblcustomer_groups.customer_id = tblclients.userid','left');
                $this->db->where_in('tblcustomer_groups.groupid', $type);
                $this->db->order_by('company', 'DESC');
                $this->db->group_by('tblclients.userid');
                $this->db->limit(50);
                $items = $this->db->get('tblclients')->result_array();
                if(!empty($items)) {
                    $data['results'] = $items;
                }
            }else
            {
                $data['results'] = array();
            }
                echo json_encode($data);die();
            
        } 
        function get_categories_id(&$data,$id,$level,$html='',$class='',$idd)
        {
            $classs = $class;
            $iddd = $idd;
            $html = $html.'&nbsp;<i class="lnr lnr-exit" style="color: #ff6f00"></i>&nbsp;';
            $categories = get_table_where('tblcategories',array('category_parent'=>$id));
            foreach ($categories as $key => $value) {
                $idd=$value['id'].','.$iddd;
                $class='parent_'.$value['id'].' '.$classs;
                $categories[$key]['class'] = $class;
                $categories[$key]['idd'] = $idd;
                $categories[$key]['category'] = $html.$value['category'];
                $categories[$key]['lever'] = $level;
                $data[] = $categories[$key];
                $_data =get_table_where('tblcategories',array('category_parent'=>$value['id']));
                if($_data)
                {

                $this->get_categories_id($data,$value['id'],($level + 1),'<i class="lnr lnr-exit" style="color: #ff6f00"></i>',$class,$idd);
                }else
                {

                    $class = $classs;
                    $idd = $iddd;
                    continue;
                }
            }
        }
    public function delete($id)
    {
        if (!is_admin()) {
            access_denied('Delete Discount Warehouse');
        }
        $response = $this->discount_model->delete($id);
        $alert_type = 'warning';
        $message    = _l('ch_no_delete');  
        if ($response) {
            $alert_type = 'success';
            $message    = _l('ch_delete');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
            ));
    }
    public function discount_data($id='')
    {
        $data['categories'] = $this->categories_id();
        $data['categories_product'] = $this->categories_product_id();
        $data['discount'] = $this->discount_model->get($id);
        $this->load->view('admin/discount/view_modal',$data);
    }
    public function view_sales_discount($id='')
    {
        $this->load->view('admin/discount/view_sale');
    }    
    public function discount_data_payment($id='')
    {
        $data['discount'] = $this->discount_model->get_payment($id);
        $this->load->view('admin/discount/view_modal_payment',$data);
    }
    public function count_all()
    {
        $count = get_table_where_select('count(*) as alls','tbldiscount',array(),'','row');
        $no_pay = get_table_where_select('count(*) as datail','tbldiscount',array('type'=>1),'','row');
        $pay_client = get_table_where_select('count(*) as payment','tbldiscount',array('type'=>2),'','row');
        $data['all'] = $count->alls;
        $data['datail'] = $no_pay->datail;
        $data['payment'] = $pay_client->payment;
        echo json_encode($data);
    }

    public function apply_discount($id = '')
    {
        $success = false;
        $alert_type = 'danger';
        $message = _l('apply_no_change');

        $get_detail = get_table_where('tbldiscount',array('id'=>$id),'','row');
        if($get_detail->id_client == 1) {
            $total = 0;
            if(!empty($get_detail->type_client) && $get_detail->type_client != '') {
                $arr_group = explode(',', $get_detail->type_client);
                foreach ($arr_group as $key => $value) {
                    $get_id_customer = get_table_where('tblcustomer_groups',array('groupid'=>$value));
                    foreach ($get_id_customer as $key_customer => $value_customer) {
                        $this->db->set('id_discount_client',$id);
                        $this->db->where('userid',$value_customer['customer_id']);
                        $this->db->update('tblclients');
                        if($this->db->affected_rows() > 0) {
                            $total++;
                        }
                    }
                }
            }

            $success = true;
            $alert_type = 'success';
            $message = _l('apply_success').$total.' '._l('client_lowercase');
        }
        else if($get_detail->id_client == 2) {
            $total = 0;
            $arr_group = get_table_where('tbldiscount_client',array('id_discount'=>$id));
            foreach ($arr_group as $key => $value) {
                $this->db->set('id_discount_client',$id);
                $this->db->where('userid',$value['id_client']);
                $this->db->update('tblclients');
                if($this->db->affected_rows() > 0) {
                    $total++;
                }
            }

            $success = true;
            $alert_type = 'success';
            $message = _l('apply_success').$total.' '._l('client_lowercase');
        }
        echo json_encode(array('success' => $success, 'alert_type' => $alert_type, 'message' => $message));
    }

    public function set_discount_to_customer()
    {
        $data = $this->input->post();
        $this->db->set('id_discount_client',$data['id_discount']);
        $this->db->where('userid',$data['id_customer']);
        $this->db->update('tblclients');

        $get_code = get_table_where('tblclients',array('userid'=>$data['id_customer']),'','row');
        activity_log_v2('client','tblclients',$data['id_customer'],$get_code->company,'Cập nhật chiết khấu khách hàng ['.$get_code->company.']');
    }
}