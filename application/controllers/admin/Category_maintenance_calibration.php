<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Category_maintenance_calibration extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->type = 1;
        if (!empty($_GET['type'])){
            $this->type = $_GET['type'];
        }
    }

    public function index()
    {
        $data['title'] = _l('dt_maintenance_calibration');
        $data['type'] = $this->type;
        $this->load->view('admin/category_maintenance_calibration/index', $data);
    }

    public function getMaintenanceCalibration(){
        $type = $this->input->post('type');
        $machines_search = $this->input->post('machines_search');
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_maintenance_calibration.id as id',
            'tbl_category_machines.code as code_category_machines',
            'tbl_category_machines.name as name_category_machines',
            'tbl_machines.code as code_machines',
            'tbl_machines.name as name_machines',
            'tbl_maintenance_calibration.department as department',
            'tbl_maintenance_calibration.detail as detail',
            'tbl_maintenance_calibration.grand_total as grand_total',
            'tbl_maintenance_calibration.date_start as date_start',
            'tbl_maintenance_calibration.date_end as date_end',
            'tbl_suggest_maintenance.reference_no as code_suggest_maintenance',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_maintenance_calibration';
        $where = [
            'AND tbl_maintenance_calibration.type = '.$type.''
        ];
        $filter = [];

        $join = [
            'INNER JOIN tbl_machines ON tbl_machines.id = tbl_maintenance_calibration.machines_id',
            'LEFT JOIN tbl_category_machines ON tbl_category_machines.id = tbl_machines.category_machine_id',
            'LEFT JOIN tbl_suggest_maintenance ON tbl_suggest_maintenance.id = tbl_maintenance_calibration.suggest_maintenance_id'
        ];

        if (!empty($machines_search)){
            array_push($where,'AND tbl_maintenance_calibration.machines_id = '.$machines_search.'');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_maintenance_calibration.type',
            'suggest_maintenance_id'
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left">'.$aRow['code_category_machines'].'</div>';
            $row[] = '<div class="text-left">'.($aRow['name_category_machines']).'</div>';
            $row[] = '<div class="text-left">'.$aRow['code_machines'].'</div>';
            $row[] = '<div class="text-left">'.$aRow['name_machines'].'</div>';
            $row[] = '<div class="text-left">'.($aRow['department']).'</div>';
            $row[] = '<div class="text-left">'.($aRow['detail']).'</div>';
            $row[] = '<div class="text-right">'.formatMoney($aRow['grand_total']).'</div>';
            $row[] = '<div class="text-left">'.(!empty($aRow['date_start']) ? _dhau($aRow['date_start']) : '').'</div>';
            $row[] = '<div class="text-left">'.(!empty($aRow['date_end']) ? _dhau($aRow['date_end']) : '').'</div>';

            if(!empty($aRow['code_suggest_maintenance'])) {
                $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="'.admin_url('suggest_maintenance/view/' . $aRow['suggest_maintenance_id']).'" data-toggle="modal" data-target="#myModal">' . $aRow['code_suggest_maintenance'] . '</a></div>';
            } else {
                $row[] = '<div class="text-left"></div>';
            }

            $edit = '<a class="tnh-modal" href="' . base_url('admin/category_maintenance_calibration/detail_maintenance_calibration/' . $aRow['id'].'?type='.$aRow['type'].'') . '"><i class="fa fa-edit"></i> ' . lang('edit')  . '</a>';

            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/category_maintenance_calibration/delete_maintenance_calibration/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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

    public function detail_maintenance_calibration($id = 0){
        $data = [];
        $this->db->select('tbl_maintenance_calibration.*');
        $this->db->from('tbl_maintenance_calibration');
        $this->db->where('tbl_maintenance_calibration.id',$id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()){
            $this->form_validation->set_rules('machines_id', lang("Thiết bị"), 'required');
            if (empty($id)){
                if ($this->form_validation->run() == true) {
                    $code = ($this->input->post('code'));
                    $machines_id = ($this->input->post('machines_id'));
                    $department = ($this->input->post('department'));
                    $detail = ($this->input->post('detail'));
                    $quantity = number_unformat($this->input->post('quantity'));
                    $price = number_unformat($this->input->post('price'));
                    $tax_id = ($this->input->post('tax_id'));
                    $deadline = ($this->input->post('deadline'));
                    $date_start = ($this->input->post('date_start'));
                    $date_end = ($this->input->post('date_end'));
                    $type = ($this->input->post('type'));
                    $suggest_maintenance_id = ($this->input->post('suggest_maintenance_id'));
                    $tax_rate = 0;
                    $total_tax = 0;
                    if (!empty($tax_id)) {
                        $info_tax = $this->site_model->rowTax($tax_id);
                        if (!empty($info_tax)) {
                            $tax_rate = $info_tax['taxrate'];
                        }
                    }
                    $amount = $quantity * $price;
                    $total_tax = $amount * $tax_rate / 100;
                    $grand_total = $amount + $total_tax;
                    $fields = [
                        'code' => $code,
                        'machines_id' => $machines_id,
                        'department' => $department,
                        'detail' => $detail,
                        'quantity' => $quantity,
                        'price' => $price,
                        'amount' => $amount,
                        'total_tax' => $total_tax,
                        'grand_total' => $grand_total,
                        'tax_id' => $tax_id,
                        'deadline' => $deadline,
                        'date_start' => !empty($date_start) ? to_sql_date($date_start) : null,
                        'date_end' => !empty($date_end) ? to_sql_date($date_end) : null,
                        'type' => $type,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'suggest_maintenance_id' => $suggest_maintenance_id,
                    ];
                    $this->db->insert('tbl_maintenance_calibration',$fields);
                    $id = $this->db->insert_id();
                    if ($id){
                        insertActivityLog([
                            'type_parent_obj' => 'maintenance_calibration',
                            'table_obj' => 'tbl_maintenance_calibration',
                            'id_obj' => $id,
                            'name_obj' => $code,
                            'content' => lang('dt_add_maintenance_calibration') . ' [' . $code . ']',
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
                    $machines_id = ($this->input->post('machines_id'));
                    $department = ($this->input->post('department'));
                    $detail = ($this->input->post('detail'));
                    $quantity = number_unformat($this->input->post('quantity'));
                    $price = number_unformat($this->input->post('price'));
                    $tax_id = ($this->input->post('tax_id'));
                    $deadline = ($this->input->post('deadline'));
                    $date_start = ($this->input->post('date_start'));
                    $date_end = ($this->input->post('date_end'));
                    $type = ($this->input->post('type'));
                    $suggest_maintenance_id = $this->input->post('suggest_maintenance_id');
                    $tax_rate = 0;
                    $total_tax = 0;
                    if (!empty($tax_id)) {
                        $info_tax = $this->site_model->rowTax($tax_id);
                        if (!empty($info_tax)) {
                            $tax_rate = $info_tax['taxrate'];
                        }
                    }
                    $amount = $quantity * $price;
                    $total_tax = $amount * $tax_rate / 100;
                    $grand_total = $amount + $total_tax;
                    $fields = [
                        'code' => $code,
                        'machines_id' => $machines_id,
                        'department' => $department,
                        'detail' => $detail,
                        'quantity' => $quantity,
                        'price' => $price,
                        'amount' => $amount,
                        'total_tax' => $total_tax,
                        'grand_total' => $grand_total,
                        'tax_id' => $tax_id,
                        'deadline' => $deadline,
                        'date_start' => !empty($date_start) ? to_sql_date($date_start) : null,
                        'date_end' => !empty($date_end) ? to_sql_date($date_end) : null,
                        'type' => $type,
                        'suggest_maintenance_id' => $suggest_maintenance_id,
                    ];
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_maintenance_calibration',$fields);
                    if ($success){
                        insertActivityLog([
                            'type_parent_obj' => 'maintenance_calibration',
                            'table_obj' => 'tbl_maintenance_calibration',
                            'id_obj' => $id,
                            'name_obj' => $dtData['code'],
                            'content' => lang('dt_edit_maintenance_calibration') . ' [' . $dtData['code'] . ']',
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
                $data['title'] = lang('dt_add_maintenance_calibration');
            } else {
                $data['dtData'] = $dtData;
                $data['title'] = lang('dt_edit_maintenance_calibration');
            }
        }
        $data['taxs'] = $this->site_model->getTaxs();
        $data['type'] = $this->type;
        $data['id'] = $id;
        $data['suggest_maintenance_id'] = $this->db->get_where('tbl_suggest_maintenance')->result_array();
        $this->load->view('admin/category_maintenance_calibration/detail_maintenance_calibration',$data);
    }

    public function delete_maintenance_calibration($id){
        $data = [];
        $this->db->select('tbl_maintenance_calibration.*');
        $this->db->from('tbl_maintenance_calibration');
        $this->db->where('tbl_maintenance_calibration.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_maintenance_calibration');
        if ($success){

            insertActivityLog([
                'type_parent_obj' => 'maintenance_calibration',
                'table_obj' => 'tbl_maintenance_calibration',
                'id_obj' => $id,
                'name_obj' => $dtData['code'],
                'content' => lang('dt_delete_maintenance_calibration') . ' [' . $dtData['code'] . ']',
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