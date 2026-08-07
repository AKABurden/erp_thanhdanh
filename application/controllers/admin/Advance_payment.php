<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Advance_payment extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('costs_model');
    }
    public function view_modal($id='')
    {
        $data['items'] = get_table_where('tbladvance_payment',array('id'=>$id),'','row');
        $data['dataLog'] = get_table_where('tblactivity_log_v2',array('table_obj'=>'tbladvance_payment','id_obj'=>$id),'id DESC');
        $this->load->view('admin/advance_payment/view_modal',$data);
    }
    public function index()
    {
        if (!has_permission('advance_payment', '', 'view') && !has_permission('advance_payment', '', 'view_own')) {
                access_denied('advance_payment');
        }
        $data['payment_modes'] = get_table_where('tblpayment_modes',array('active'=>1));
        $this->db->select('tblstaff.staffid, CONCAT(firstname," ",lastname) as name');
        $this->db->from('tblstaff');
        $data['dataStaff'] = $this->db->get()->result_array();
        $data['tnh'] = true;
        if (!has_permission('advance_payment', '', 'view')&&!has_permission('advance_payment', '', 'view_own')) {
                access_denied('advance_payment');
        }
        $data['title']          = _l('ch_advance_payment');
        $this->load->view('admin/advance_payment/manage', $data);
    }
    public function table()
    {
        $this->app->get_table_data('advance_payment');
    }
    public function add()
    {
        $success = true;
        $alert_type = 'warning';
        $message    = _l('Không thành công');
        if ($this->input->post()) {
            $data = $this->input->post();
            if(!empty($data['id']))
            {
                $up = array(
                    'date'=>to_sql_date($data['date']),
                    'staff'=>$data['objects_id'],
                    'paymode_c'=>$data['paymode_c'],
                    'paymode_n'=>$data['paymode_n'],
                    'total'=> str_replace(',', '', $data['total']),
                    'note'=>$data['note'],
                    'id_costs'=>$data['id_costs'],
                );
                $this->db->update('tbladvance_payment',$up,array('id'=>$data['id']));
                
                    $success = true;
                    $alert_type = 'success';
                    $message    = _l('Sửa thành công');
            }else{
                $in = array(
                    'date'=>to_sql_date($data['date']),
                    'staff'=>$data['objects_id'],
                    'paymode_c'=>$data['paymode_c'],
                    'paymode_n'=>$data['paymode_n'],
                    'date_create'=>date('Y-m-d H:i:s'),
                    'staff_create'=>get_staff_user_id(),
                    'total'=> str_replace(',', '', $data['total']),
                    'note'=>$data['note'],
                    'id_costs'=>$data['id_costs'],
                    'code'=>('TU'.'-'.sprintf('%06d', ch_getMaxID('id', 'tbladvance_payment') + 1)),
                );
                $this->db->insert('tbladvance_payment',$in);
                $insert_id = $this->db->insert_id();
                if(!empty($insert_id))
                {
                    $success = true;
                    $alert_type = 'success';
                    $message    = _l('ch_added_successfuly');
                }
            }
        }
        echo json_encode(array(
            'success' => $success, 
            'alert_type' => $alert_type,
            'message' => $message
        ));die;
    }
    public function count_all()
    {
        if(has_permission('advance_payment','','view_own')&&!is_admin()) {
            $count = get_table_where_select('count(*) as alls','tbladvance_payment',array('staff_create'=>get_staff_user_id()),'','row');
            $no_pay = get_table_where_select('count(*) as no_pay','tbladvance_payment',array('status'=>0,'staff_create'=>get_staff_user_id()),'','row');
        } else {
            $this->db->select('count(*) as alls');
            $count = $this->db->get('tbladvance_payment')->row();

            $this->db->select('count(*) as no_pay');
            $this->db->where('tbladvance_payment.status', 0);
            $no_pay = $this->db->get('tbladvance_payment')->row();

        }
        $data['all'] = $count->alls;
        $data['no_pay'] = $no_pay->no_pay;
        $data['pay'] = $data['all']-$data['no_pay'];
        echo json_encode($data);
    }
    public function SearchClient($id='',$type='')
    {
        $arrStaffId = get_group_branch();
        $data = [];
        $search = $this->input->get('term');
        if (empty($type))
        {
        $type = $this->input->get('type');
        }
        $limit_one = 20;
        if($type == 1 || $type == 5) //5: type bảo hành hoàng crm bổ xung
        {
        $this->db->select('
            tblclients.userid as id,
            tblclients.company as text,
            CONCAT(tblclients.prefix_client,tblclients.code_client) as code_client'
        , false);
        if($arrStaffId != array()) {
            $this->db->where('tblclients.addedfrom IN ('.implode(",", $arrStaffId).')');
        }

        if (!empty($search))
        {
            $this->db->group_start();
            $this->db->like('tblclients.company', $search);
            $this->db->or_like('CONCAT(tblclients.prefix_client, tblclients.code_client)', $search);
            $this->db->group_end();
        }
        if(!empty($id))
        {
        $this->db->where('tblclients.userid',$id);    
        }
        $this->db->order_by('tblclients.company', 'DESC');
        $this->db->limit($limit_one);
        $client = $this->db->get('tblclients')->result_array();
        $data['results'] = $client;
        }elseif($type == 2)
        {
        $this->db->select('
            tblsuppliers.id as id,
            tblsuppliers.company as text,
            CONCAT(tblsuppliers.prefix,tblsuppliers.code) as code_client'
        , false);
        if($arrStaffId != array()) {
            $this->db->where('tblsuppliers.addedfrom IN ('.implode(",", $arrStaffId).')');
        }
        if (!empty($search))
        {
            $this->db->group_start();
            $this->db->like('tblsuppliers.company', $search);
            $this->db->or_like('CONCAT(tblsuppliers.prefix, tblsuppliers.code)', $search);
            $this->db->group_end();
        }
        if(!empty($id))
        {
        $this->db->where('tblsuppliers.id',$id);    
        }
        $this->db->order_by('tblsuppliers.company', 'DESC');
        $this->db->limit($limit_one);
        $suppliers = $this->db->get('tblsuppliers')->result_array();
        $data['results'] = $suppliers;    
        }elseif($type == 3)
        {
        $this->db->select('
            tblstaff.staffid as id,
            CONCAT(tblstaff.lastname,tblstaff.firstname) as text'
        , false);
        if (!empty($search))
        {
            $this->db->group_start();
            $this->db->like('CONCAT(tblstaff.lastname, tblstaff.firstname)', $search);
            $this->db->group_end();
        }
        if(!empty($id))
        {
        $this->db->where('tblstaff.staffid',$id);    
        }
        $this->db->limit($limit_one);
        $suppliers = $this->db->get('tblstaff')->result_array();
        $data['results'] = $suppliers;    
        }
        echo json_encode($data);die();

    }
    public function delete($id)
    {
        if(!has_permission('advance_payment','','delete')){
            echo json_encode(array(
            'alert_type' => 'warning',
            'message' => _l('ch_delete_not')
            ));die;
        }
        $get_code = get_table_where('tbladvance_payment',array('id'=>$id),'','row');
        activity_log_v2('tamung','tbladvance_payment',$id,$get_code->code,'Xóa phiếu tạm ứng ['.$get_code->code.']');
        $this->db->delete('tbladvance_payment',array('id'=>$id));
        $alert_type = 'success';
        $message    = _l('ch_delete');
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }
    public function advance_payment($id='')
    {
       $data['vouchers_id'] = array();
       if(!empty($id))
       {
       $data['items'] = get_table_where('tbladvance_payment',array('id'=>$id),'','row');
            
       }
 
       $data['id'] = 0;
       $data['payment_modes'] = get_table_where('tblpayment_modes',array('active'=>1));
       $data['costs'] = array();
       $this->costs_model->get_by_id(0,$data['costs']);
       $data['code'] = 'TU'.'-'.sprintf('%06d', ch_getMaxID('id', 'tbladvance_payment') + 1);
       $this->load->view('admin/advance_payment/detail',$data);
    }
    public function update_status_not()
    {
        if (!has_permission('advance_payment', '', 'approve')) {
            echo json_encode(array(
                'success' => false,
                'alert_type' => 'warning',
                'message' => _l('ch_approve_not')
            ));die;
        }
        if ($this->input->post()) {
            $id=$this->input->post('id');
            $status=$this->input->post('status');
            $advance_payment = get_table_where('tbladvance_payment',array('id'=>$id),'','row');
            if($advance_payment->status == 0)
            {
                die;
            }
            $staff_id=get_staff_user_id();
            $date=date('Y-m-d H:i:s');
            $history_status=$staff_id.','.$date;
            $data =array(
                'history_status'=>'',
                'status' => (0),
            );
            $success=$this->db->update('tbladvance_payment',$data,array('id'=>$id));
        }
        if($success) {
            $get_code = get_table_where('tbladvance_payment',array('id'=>$id),'','row');
            activity_log_v2('tamung','tbladvance_payment',$id,$get_code->code,'Cập nhật trạng thái phiếu tạm ứng ['.$get_code->code.']');
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
    public function update_status()
    {
        if (!has_permission('advance_payment', '', 'approve')) {
            echo json_encode(array(
                'success' => false,
                'alert_type' => 'warning',
                'message' => _l('ch_approve_not')
            ));die;
        }
        if ($this->input->post()) {
            $id=$this->input->post('id');
            $status=$this->input->post('status');
            $advance_payment = get_table_where('tbladvance_payment',array('id'=>$id),'','row');
            if($advance_payment->status == 1)
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
            $success=$this->db->update('tbladvance_payment',$data,array('id'=>$id));
        }
        if($success) {
            $get_code = get_table_where('tbladvance_payment',array('id'=>$id),'','row');
            activity_log_v2('tamung','tbladvance_payment',$id,$get_code->code,'Cập nhật trạng thái phiếu tạm ứng ['.$get_code->code.']');
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
    public function print_pdf($id='')
    {
        ob_start();
        $data = new stdClass();
        $dataMain = get_table_where('tbladvance_payment',array('id'=>$id),'','row');
        $table = '';
        $data->content = '';
        $data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;">'._l('TẠM ỨNG').'</span><br>';
        
        $data->content .= '<span style="text-align: center;font-style: italic;">'._l('ch_number').': '.$dataMain->code.'</span><br>';

        $day = date('d', strtotime($dataMain->date));
        $month = date('m', strtotime($dataMain->date));
        $year = date('Y', strtotime($dataMain->date));
        $date = _l('ch_day') . ' ' . $day . ' ' . _l('ch_month') . ' ' . $month . ' ' . _l('ch_year') . ' ' . $year;
        $data->content .= '<span style="text-align: center;font-style: italic;">'.$date.'</span><br><br>';
        $pay_modec = get_table_where('tblpayment_modes',array('id'=>$dataMain->paymode_c),'','row');
        $pay_moden = get_table_where('tblpayment_modes',array('id'=>$dataMain->paymode_n),'','row');
        $_data = get_staff_full_name($dataMain->staff);
        $data->content .= '
        <span style="font-weight: bold;">'._l('ch_units_in').': </span><span style="font-weight: bold;">'.$_data.'</span><br/><br>';
        

        $data->content .='
            <span style="font-weight: bold;">'._l('ch_note_pay_slips').': </span><span>'.$dataMain->note.'</span><br><br>
            <span style="font-weight: bold;">'._l('Phương thức thanh toán chuyển').': </span><span>'.$pay_modec->name.'</span><br><br>
            <span style="font-weight: bold;">'._l('Phương thức thanh toán nhận').': </span><span>'.$pay_moden->name.'</span><br><br>
            <span style="font-weight: bold;">'._l('expense_add_edit_amount').': </span><span>'.number_format($dataMain->total).'</span><br><br>
            <span style="font-weight: bold;">'._l('ch_write_in_words').': </span><span>'.ucfirst(convert_number_to_words($dataMain->total)).' đồng</span><br>';
        $date_2 = _l('ch_day') . ' ........ ' . _l('ch_month') . ' ........ ' . _l('ch_year') . ' ........';
        $data->content .= '<span style="text-align: right;font-style: italic;">'.$date_2.'</span><br>';
        $table = '<table class="table table-bordered" width="100%">
                <thead>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">'._l('ch_ceo').'</span><br>
                            <span>'._l('ch_signature').'</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">'._l('ch_chief_accountant').'</span><br>
                            <span>'._l('ch_signature').'</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">'._l('ch_cashier').'</span><br>
                            <span>'._l('ch_signature').'</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">'._l('ch_vote_maker').'</span><br>
                            <span>'._l('ch_signature').'</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">'._l('ch_recipient_pirce').'</span><br>
                            <span>'._l('ch_signature').'</span>
                        </td>
                    </tr>
                </tbody>
            </table>';
        $data->content .= $table;
        $datas = '<br><br><br><br><br><span style="text-align: center;">__________________________________________________________________</span><br>';
        $company_logo = get_option('company_logo');
        $img = file_get_contents(base_url('uploads/company/').$company_logo);
        $html= '<table  class="table table-bordered" width="100%">
                <thead>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="width: 20%;">
                        <img width="112" height="50" src="data:image/png;base64,'.base64_encode($img).'"/>
                        </td>
                        <td style="width: 80%;">
                            <span style="font-weight: bold;font-size: 14px;">'.get_option('invoice_company_name').'</span><br>
                            <span style="font-size: 12px;">'.lang('tnh_address').': '.get_option('invoice_company_address').'</span><br>
                            <span style="font-size: 12px;">'.lang('tnh_phone').': '.get_option('invoice_company_phonenumber').'</span><br>
                            <span style="font-size: 12px;">'.lang('Fax').': '.get_option('fax_company').'</span><br>
                            <span style="font-size: 12px;">'._l('Email').': '.get_option('email_company').'</span><br>
                        </td>
                    </tr>
                </tbody>
            </table>';
        $data->content .=$datas.$html.$data->content;
        $pdf      = print_pdf_ch($data);
        $type     = 'I';
        $pdf->Output($dataMain->code . '.pdf', $type);
    }    
}