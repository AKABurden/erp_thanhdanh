<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Depreciation extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $data['title'] = lang('Khấu hao tài sản cố định');
        $this->load->view('admin/depreciation/manage', $data);
    }
    public function add()
    {
        if ($this->input->post()) {
            $alert_type = 'danger';
            $message = _l('Thêm không thành công');
            $data = $this->input->post();
            $data['code'] = get_option('depreciation').'-'.sprintf('%06d', ch_getMaxID('id', 'tblrecord_increased') + 1);
            $data['date'] = to_sql_date($data['date']);
            $data['staff_id'] = get_staff_user_id();
            $data['date_create'] = date('Y-m-d H:i:s');
            $this->db->insert('tbldepreciation',$data);
            $id = $this->db->insert_id();
            if($id)
            {
                   $subtotal = 0;
                   $month = $data['month'];
                   $year = $data['year'];
                   $this->db->select('tblrecord_increased.*,tbltype_record_increased.name as name_type,tbldepartments.name as departments');
                   $this->db->where('date_depreciation <=',$year.'-'.$month.'-'.last_day($month,$year));
                   $this->db->where('date_end >=',$year.'-'.$month.'-'.last_day($month,$year));
                   $this->db->join('tbltype_record_increased','tbltype_record_increased.id = ' . 'tblrecord_increased.type_record_increased', 'left');
                   $this->db->join('tbldepartments','tbldepartments.departmentid = ' . 'tblrecord_increased.units_used', 'left');
                   $data['increased'] = $this->db->get('tblrecord_increased')->result_array();
                   foreach ($data['increased'] as $key => $value) {
                       if(($value['date_depreciation'] >= ($year.'-'.$month.'-01'))&&($value['date_depreciation'] <= ($year.'-'.$month.'-'.last_day($month,$year))))
                       {
                            $total_day = $value['monthly_depreciation_value']/last_day($month,$year);
                            $days = (strtotime(($year.'-'.$month.'-'.last_day($month,$year))) - strtotime($value['date_depreciation'])) / (60 * 60 * 24);
                            $total = $total_day*$days;
                            $data['increased'][$key]['total'] = $total;
                       }else
                       {
                            $total = $value['monthly_depreciation_value'];
                       }
                       $_data['id_depreciation'] = $id;
                       $_data['id_increased'] = $value['id'];
                       $_data['month'] = $month;
                       $_data['year'] = $year;
                       $_data['total'] = $total;
                       $this->db->insert('tbldepreciation_detail',$_data);
                       $subtotal+=$total;
                   }
                $this->db->update('tbldepreciation',array('total'=>$subtotal),array('id'=>$id));
                $alert_type = 'success';
                $message = _l('ch_added_successfuly');
                
            }
            echo json_encode(array(
                    'alert_type' => $alert_type,
                    'message' => $message
                ));die;
        }
    }
    public function add_depreciation()
    {
       $month = $this->input->post('month');
       $year = $this->input->post('year');
       $data['month']=$month;
       $data['year']=$year;
       // $this->db->where('Month(date_depreciation)',$month);
       // $this->db->where('Year(date_depreciation)',$year);
       $this->db->select('tblrecord_increased.*,tbltype_record_increased.name as name_type,tbldepartments.name as departments');
       $this->db->where('date_depreciation <=',$year.'-'.$month.'-'.last_day($month,$year));
       $this->db->where('date_end >=',$year.'-'.$month.'-'.last_day($month,$year));
       $this->db->join('tbltype_record_increased','tbltype_record_increased.id = ' . 'tblrecord_increased.type_record_increased', 'left');
       $this->db->join('tbldepartments','tbldepartments.departmentid = ' . 'tblrecord_increased.units_used', 'left');
       $data['increased'] = $this->db->get('tblrecord_increased')->result_array();
       $data['attribution'] = array();
       $count = 0;
       foreach ($data['increased'] as $key => $value) {
           if(($value['date_depreciation'] >= ($year.'-'.$month.'-01'))&&($value['date_depreciation'] <= ($year.'-'.$month.'-'.last_day($month,$year))))
           {
                $total_day = $value['monthly_depreciation_value']/last_day($month,$year);
                $days = (strtotime(($year.'-'.$month.'-'.last_day($month,$year))) - strtotime($value['date_depreciation'])) / (60 * 60 * 24);
                $total = $total_day*$days;
                $data['increased'][$key]['total'] = $total;
                $subtotals = $total;
           }else
           {
                $data['increased'][$key]['total'] = $value['monthly_depreciation_value'];
                $subtotals = $value['monthly_depreciation_value'];
           }
           $attribution = get_table_where('tblattribution',array('id_record_increased'=>$value['id']));
           foreach ($attribution as $ks => $vs) {
                $departments = get_table_where('tbldepartments',array('departmentid'=>$vs['units_useds']),'','row');
                $data['attribution'][$count]['increased_id'] = $value['id'];
                $data['attribution'][$count]['increased'] = $value['asset_name'];
                $data['attribution'][$count]['attribution_name'] = $departments->name;
                $data['attribution'][$count]['total'] = $vs['percent']*$subtotals/100;
                $data['attribution'][$count]['percent'] = $vs['percent'];
                $data['attribution'][$count]['subtotals'] = $subtotals;
                $count++;
           }
       }
       $data['title'] =_l('Thêm khấu hao tài sản tháng ').$month.' Năm '.$year;
       $data['code'] = get_option('depreciation').'-'.sprintf('%06d', ch_getMaxID('id', 'tblrecord_increased') + 1);
       $this->load->view('admin/depreciation/detail',$data);
    }
    public function edit_depreciation($id='')
    {
       $data['items'] = get_table_where('tbldepreciation',array('id'=>$id),'','row'); 
       $data['title'] =_l('Xem khấu hao tháng ').$data['items']->month.' năm '.$data['items']->year;
       $this->db->select('tbldepreciation_detail.*,tblrecord_increased.*,tblrecord_increased.id as idd,tbltype_record_increased.name as name_type,tbldepartments.name as departments');
       $this->db->join('tblrecord_increased','tblrecord_increased.id = ' . 'tbldepreciation_detail.id_increased', 'left');
       $this->db->join('tbltype_record_increased','tbltype_record_increased.id = ' . 'tblrecord_increased.type_record_increased', 'left');
       $this->db->join('tbldepartments','tbldepartments.departmentid = ' . 'tblrecord_increased.units_used', 'left');
       $this->db->where('tbldepreciation_detail.id_depreciation',$id);
       $data['increased'] = $this->db->get('tbldepreciation_detail')->result_array();
       $data['attribution'] = array();
       $count = 0;
       foreach ($data['increased'] as $key => $value) {
           $attribution = get_table_where('tblattribution',array('id_record_increased'=>$value['idd']));
           foreach ($attribution as $ks => $vs) {
                $departments = get_table_where('tbldepartments',array('departmentid'=>$vs['units_useds']),'','row');
                $data['attribution'][$count]['increased_id'] = $value['idd'];
                $data['attribution'][$count]['increased'] = $value['asset_name'];
                $data['attribution'][$count]['attribution_name'] = $departments->name;
                $data['attribution'][$count]['total'] = $vs['percent']*$value['total']/100;
                $data['attribution'][$count]['percent'] = $vs['percent'];
                $data['attribution'][$count]['subtotals'] = $value['total'];
                $count++;
           }
       }
       $data['code'] = get_option('depreciation').'-'.sprintf('%06d', ch_getMaxID('id', 'tblrecord_increased') + 1);
       $this->load->view('admin/depreciation/detail_vew',$data);
    }
    public function table()
    {
        $this->app->get_table_data('depreciation');
    }
    public function delete($id='')
    {
        $alert_type = 'warning';
        $message    = _l('ch_no_delete');  
        if($this->db->delete('tbldepreciation',array('id'=>$id)))
        {   
            $this->db->delete('tbldepreciation_detail',array('id_depreciation'=>$id));
            
            $alert_type = 'success';
            $message    = _l('ch_delete');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
            ));

    }

    public function depreciation(){
        $data['title'] = _l('dt_depreciation');
        $this->load->view('admin/depreciation/depreciation', $data);
    }

    public function getDepreciations(){
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_depreciation.id as id',
            'tbl_depreciation.code as code',
            'tbl_depreciation.name as name',
            'tbl_depreciation.time_depreciation as time_depreciation',
            'tbl_depreciation.date_start as date_start',
            'tbl_depreciation.date_end as date_end',
            'tbl_depreciation.depreciation_value as depreciation_value',
            'tbl_depreciation.residual_value as residual_value',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_depreciation';
        $where = [

        ];
        $filter = [];

        $join = [
        ];


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left">'.$aRow['code'].'</div>';
            $row[] = '<div class="text-left">'.$aRow['name'].'</div>';
            $row[] = '<div class="text-center">'.$aRow['time_depreciation'].'</div>';
            $row[] = '<div class="text-left">'.(!empty($aRow['date_start']) ? _dhau($aRow['date_start']) : '').'</div>';
            $row[] = '<div class="text-left">'.(!empty($aRow['date_end']) ? _dhau($aRow['date_end']) : '').'</div>';
            $row[] = '<div class="text-right">'.(!empty($aRow['depreciation_value']) ? formatMoney($aRow['depreciation_value']) : '').'</div>';
            $row[] = '<div class="text-right">'.(!empty($aRow['residual_value']) ? formatMoney($aRow['residual_value']) : '').'</div>';

            $edit = '<a class="tnh-modal" href="' . base_url('admin/depreciation/detail_depreciation/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit')  . '</a>';

            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/depreciation/delete_depreciation/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete')  . '</a>';
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[] = '<div>' . $actions . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function detail_depreciation($id = 0){
        $data = [];
        $this->db->select('tbl_depreciation.*');
        $this->db->from('tbl_depreciation');
        $this->db->where('tbl_depreciation.id',$id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()){
            if (!empty($id)){
                if ($dtData['code'] != $this->input->post('code')) {
                    $this->form_validation->set_rules('code', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_depreciation.code]');
                }
            } else {
                $this->form_validation->set_rules('code', lang("Mã Phiếu"), 'required|is_unique[tbl_depreciation.code]');
            }
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if (empty($id)){
                if ($this->form_validation->run() == true) {
                    $code = ($this->input->post('code'));
                    $name = ($this->input->post('name'));
                    $time_depreciation = number_unformat($this->input->post('time_depreciation'));
                    $date_start = ($this->input->post('date_start'));
                    $date_end = ($this->input->post('date_end'));
                    $depreciation_value = number_unformat($this->input->post('depreciation_value'));
                    $residual_value = number_unformat($this->input->post('residual_value'));
                    $fields = [
                        'code' => $code,
                        'name' => $name,
                        'time_depreciation' => $time_depreciation,
                        'date_start' => !empty($date_start) ? to_sql_date($date_start) : null,
                        'date_end' => !empty($date_end) ? to_sql_date($date_end) : null,
                        'depreciation_value' => $depreciation_value,
                        'residual_value' => $residual_value,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->insert('tbl_depreciation',$fields);
                    $id = $this->db->insert_id();
                    if ($id){
                        insertActivityLog([
                            'type_parent_obj' => 'depreciation',
                            'table_obj' => 'tbl_depreciation',
                            'id_obj' => $id,
                            'name_obj' => $code,
                            'content' => lang('Thêm mới khấu hao') . ' [' . $code . ']',
                            'actions' => 'add'
                        ]);
                        $data['result'] = 1;
                        $data['message'] = lang('Thêm thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Thêm thất bại');
                    }
                } else {
                    $data['result'] = 0;
                    $data['message'] = validation_errors();
                }
                echo json_encode($data);die();
            } else {
                if ($this->form_validation->run() == true) {
                    $code = ($this->input->post('code'));
                    $name = ($this->input->post('name'));
                    $time_depreciation = number_unformat($this->input->post('time_depreciation'));
                    $date_start = ($this->input->post('date_start'));
                    $date_end = ($this->input->post('date_end'));
                    $depreciation_value = number_unformat($this->input->post('depreciation_value'));
                    $residual_value = number_unformat($this->input->post('residual_value'));
                    $fields = [
                        'code' => $code,
                        'name' => $name,
                        'time_depreciation' => $time_depreciation,
                        'date_start' => !empty($date_start) ? to_sql_date($date_start) : null,
                        'date_end' => !empty($date_end) ? to_sql_date($date_end) : null,
                        'depreciation_value' => $depreciation_value,
                        'residual_value' => $residual_value,
                    ];
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_depreciation',$fields);
                    if ($success){
                        insertActivityLog([
                            'type_parent_obj' => 'depreciation',
                            'table_obj' => 'tbl_depreciation',
                            'id_obj' => $id,
                            'name_obj' => $dtData['code'],
                            'content' => lang('Sửa khấu hao') . ' [' . $dtData['code'] . ']',
                            'actions' => 'edit'
                        ]);
                        $data['result'] = 1;
                        $data['message'] = lang('Sửa thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Sửa thất bại');
                    }
                } else {
                    $data['result'] = 0;
                    $data['message'] = validation_errors();
                }
                echo json_encode($data);die();
            }
        } else {
            if (empty($id)){
                $data['title'] = lang('dt_add_depreciation');
            } else {
                $data['dtData'] = $dtData;
                $data['title'] = lang('dt_edit_depreciation');
            }
        }
        $data['id'] = $id;
        $this->load->view('admin/depreciation/detail_depreciation',$data);
    }

    public function delete_depreciation($id){
        $data = [];
        $this->db->select('tbl_depreciation.*');
        $this->db->from('tbl_depreciation');
        $this->db->where('tbl_depreciation.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_depreciation');
        if ($success){

            insertActivityLog([
                'type_parent_obj' => 'depreciation',
                'table_obj' => 'tbl_depreciation',
                'id_obj' => $id,
                'name_obj' => $dtData['code'],
                'content' => lang('Xóa khấu hao') . ' [' . $dtData['code'] . ']',
                'actions' => 'delete'
            ]);
            $data['result'] = 1;
            $data['message'] = lang('Xóa thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Xóa thất bại');
        }
        echo json_encode($data);
    }
}