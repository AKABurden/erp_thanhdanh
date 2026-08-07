<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Record_reduce extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $data['title'] = lang('Ghi giảm');
        $this->load->view('admin/record_reduce/manage', $data);
    }
    public function add_record_reduce($id='')
    {
       $data['title'] =_l('Thêm ghi giảm');
       $data['code'] = get_option('record_reduce').'-'.sprintf('%06d', ch_getMaxID('id', 'tblrecord_increased') + 1);
       $this->load->view('admin/record_reduce/detail',$data);
    }
    public function edit_record_reduce($id='')
    {
       $data['title'] =_l('Sửa ghi giảm');
       $data['items'] = get_table_where('tblrecord_reduce',array('id'=>$id),'','row');
       $data['items']->items = get_table_where('tblrecord_reduce_detail',array('id_record_reduce'=>$id));
       $data['type_record_reduce'] =get_table_where('tbltype_record_reduce');
       $data['code'] = get_option('record_reduce').'-'.sprintf('%06d', ch_getMaxID('id', 'tblrecord_increased') + 1);
       $this->load->view('admin/record_reduce/detail',$data);
    }
    public function table()
    {
        $this->app->get_table_data('record_reduce');
    }
    public function delete($id='')
    {
        $alert_type = 'warning';
        $message    = _l('ch_no_delete');  
        $detail = get_table_where('tblrecord_reduce_detail',array('id_record_reduce'=>$id));
        if($this->db->delete('tblrecord_reduce',array('id'=>$id)))
        {   
            $this->db->delete('tblrecord_reduce_detail',array('id_record_reduce'=>$id));
            foreach ($detail as $key => $value) {
                $this->db->update('tblother_payslips',array('check_tscd'=>0),array('id'=>$value['id_other_payslips'])); 
            }
            $alert_type = 'success';
            $message    = _l('ch_delete');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
            ));

    }
    public function add($id='')
    {
        if ($this->input->post()) {
            if(empty($id)){
            $alert_type = 'danger';
            $message = _l('Thêm không thành công');

            $data = $this->input->post();
            $items = $data['items'];
            unset($data['items']);
            $data['date_of_recording_increases'] = to_sql_date($data['date_of_recording_increases']);
            $data['date_depreciation'] = to_sql_date($data['date_depreciation']);
            $data['number_used_time'] = str_replace(',', '', $data['number_used_time']);
            $data['original_price'] = str_replace(',', '', $data['original_price']);
            $data['value_of_depreciation'] = str_replace(',', '', $data['value_of_depreciation']);
            $data['monthly_depreciation_rate'] = str_replace(',', '', $data['monthly_depreciation_rate']);
            $data['annual_depreciation_rate'] = str_replace(',', '', $data['annual_depreciation_rate']);
            $data['monthly_depreciation_value'] = str_replace(',', '', $data['monthly_depreciation_value']);
            $data['yearly_depreciation_value'] = str_replace(',', '', $data['yearly_depreciation_value']);
            $data['accumulated_depreciation'] = str_replace(',', '', $data['accumulated_depreciation']);
            $data['residual_value'] = str_replace(',', '', $data['residual_value']);    
            $data['staff_id'] = get_staff_user_id();
            $data['date_create'] = date('Y-m-d H:i:s');
            $this->db->insert('tblrecord_reduce',$data);
            $id = $this->db->insert_id();
            if($id)
            {

                foreach ($items as $key => $value) {
                    $_data = array();
                    if(!empty($value['custom_item_select']))
                    {
                        $_data['id_record_reduce'] = $id;
                        $_data['id_other_payslips'] = $value['custom_item_select'];
                        $custom_item_select = get_table_where('tblother_payslips',array('id'=>$value['custom_item_select']),'','row');
                        $_data['total'] = $custom_item_select->total;
                        $this->db->insert('tblrecord_reduce_detail',$_data);
                        $id_increased_detail = $this->db->insert_id();
                        if($id_increased_detail)
                        {
                           $this->db->update('tblother_payslips',array('check_tscd'=>$id_increased_detail),array('id'=>$custom_item_select->id)); 
                        }
                    }
                }
                $alert_type = 'success';
                $message = _l('ch_added_successfuly');
                
            }
            echo json_encode(array(
                    'alert_type' => $alert_type,
                    'message' => $message
                ));die;
            }elseif(!empty($id)){
            {
                    $alert_type = 'danger';
                    $message = _l('Sửa không thành công');

                    $data = $this->input->post();
                    $items = $data['items'];
                    unset($data['items']);
                    $data['date_of_recording_increases'] = to_sql_date($data['date_of_recording_increases']);
                    $data['date_depreciation'] = to_sql_date($data['date_depreciation']);
                    $data['number_used_time'] = str_replace(',', '', $data['number_used_time']);
                    $data['original_price'] = str_replace(',', '', $data['original_price']);
                    $data['value_of_depreciation'] = str_replace(',', '', $data['value_of_depreciation']);
                    $data['monthly_depreciation_rate'] = str_replace(',', '', $data['monthly_depreciation_rate']);
                    $data['annual_depreciation_rate'] = str_replace(',', '', $data['annual_depreciation_rate']);
                    $data['monthly_depreciation_value'] = str_replace(',', '', $data['monthly_depreciation_value']);
                    $data['yearly_depreciation_value'] = str_replace(',', '', $data['yearly_depreciation_value']);
                    $data['accumulated_depreciation'] = str_replace(',', '', $data['accumulated_depreciation']);
                    $data['residual_value'] = str_replace(',', '', $data['residual_value']);    
                    $success = $this->db->update('tblrecord_reduce',$data,array('id'=>$id));
                    if($success)
                    {

                        $check = array();
                        foreach ($items as $key => $value) {
                            $_data = array();
                            if(!empty($value['custom_item_select']))
                            {
                                $ktr = get_table_where('tblrecord_reduce_detail',array('id_record_reduce'=>$id,'id_other_payslips'=>$value['custom_item_select']),'','row');
                                if(empty($ktr))
                                {
                                    $_data['id_record_reduce'] = $id;
                                    $_data['id_other_payslips'] = $value['custom_item_select'];
                                    $custom_item_select = get_table_where('tblother_payslips',array('id'=>$value['custom_item_select']),'','row');
                                    $_data['total'] = $custom_item_select->total;
                                    $this->db->insert('tblrecord_reduce_detail',$_data);
                                    $id_increased_detail = $this->db->insert_id();
                                    if($id_increased_detail)
                                    {
                                       $check[] = $id_increased_detail;
                                       $this->db->update('tblother_payslips',array('check_tscd'=>$id_increased_detail),array('id'=>$custom_item_select->id)); 
                                    }  
                                }else
                                {
                                        $check[] = $ktr->id;
                                }
                                
                            }
                        }
                        $this->db->where('id_record_reduce',$id);
                        $this->db->where_not_in('id',$check);
                        $not = $this->db->get('tblrecord_reduce_detail')->result_array();
                        foreach ($not as $key => $value) {
                            $this->db->delete('tblrecord_reduce_detail',array('id'=>$value['id']));

                            $this->db->update('tblother_payslips',array('check_tscd'=>0),array('id'=>$value['id_other_payslips'])); 
                        }
                        $alert_type = 'success';
                        $message = _l('Cập nhật thành công');
                    }
                    echo json_encode(array(
                            'alert_type' => $alert_type,
                            'message' => $message
                        ));die;
                }
            }
        }
    }
    public function get_items($id='')
    {
        $data = get_table_where('tblother_payslips',array('id'=>$id),'','row');
        $data->date = _d($data->date);
        echo json_encode($data);
    }
    public function SearchItems($id='')
    {

            $search = $this->input->get('term');
            $types = $this->input->get('types');

            if(!empty($types))
            {
                $array = array();
                $get = get_table_where('tblrecord_reduce_detail',array('id_record_reduce'=>$types));
                foreach ($get as $key => $value) {
                    $array[] = $value['id_other_payslips'];
                }

                $this->db->select('
                tblother_payslips.id as id,
                concat(prefix,"-",code) as text'
                , false);
                $this->db->where_in('tblother_payslips.id',$array);
                $resultss = $this->db->get('tblother_payslips')->result_array();
            }

            $this->db->select('
                    tblother_payslips.id as id,
                    concat(prefix,"-",code) as text'

            , false);
            $this->db->group_by('tblother_payslips.id');
            $this->db->order_by('tblother_payslips.id', 'DESC');
            $this->db->where('tblother_payslips.objects',5);
            $this->db->where('tblother_payslips.status',1);
            $this->db->limit(50);
            
            if (!empty($search))
            {
                $this->db->like('concat(prefix,"-",code)', $search);
            }else {
                if($id > 0) {
                    $this->db->where('tblother_payslips.id', $id);
                    $items['results'] = $this->db->get('tblother_payslips')->result_array();
                    echo json_encode($items);die();
                }else
                {
                $this->db->where('tblother_payslips.check_tscd',0);
                }
            }

            $items['results'] = $this->db->get('tblother_payslips')->result_array();
            if(!empty($resultss))
            {
            $items['results']=array_merge($resultss,$items['results']);   
            }
            echo json_encode($items);die();
    }
}