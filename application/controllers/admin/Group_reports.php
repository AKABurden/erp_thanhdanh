<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Group_reports extends AdminController
{
    function __construct()
    {
        parent::__construct();
        $this->preViewGroupReports = true;
        $this->preViewOwnGroupReports = true;
        $this->preAddGroupReports = true;
        $this->preEditGroupReports = true;
        $this->preApproveGroupReports = true;
        $this->preDeleteGroupReports = true;
    }
    public function index()
    {
        if (!$this->preViewGroupReports && !$this->preViewOwnGroupReports) {
            access_denied();
        }
        $data['type_reports'] = get_table_where('tbl_type_reports');
        $data['title'] = _l('ch_group_reports');
        $this->load->view('admin/group_reports/manage', $data);
    }
    public function table()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_group_reports.id as id',
            'tbl_type_reports.code as code_type_reports',
            'tbl_type_reports.name as name_type_reports',
            'tbl_group_reports.code as code',
            'tbl_group_reports.name as name',
            '1'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_group_reports';
        $where = [];
        $filter = [];
        $j = 0;
        $join = [
            'LEFT JOIN tbl_type_reports ON tbl_type_reports.id=tbl_group_reports.id_type_reports',
        ];
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tbl_group_reports.id'], '', []);
        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $j++;
            for ($i = 0; $i < count($aColumns); $i++) {
                if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                    $_data = $aRow[strafter($aColumns[$i], 'as ')];
                } else {
                    $_data = $aRow[$aColumns[$i]];
                }
                if ($aColumns[$i] == 'tbl_group_reports.id as id') {
                    $_data = '<div class="text-center">' . $j . '</div>';
                }
                if ($aColumns[$i] == '1') {
                    $_data = '<a href="#" class="btn btn-default btn-icon" onclick="view_init_department(' . $aRow['id'] . '); return false;"><i class="fa fa-eye"></i></a>';
                    $_data .= icon_btn('group_reports/delete_group_reports/' . $aRow['id'], 'remove', 'btn-danger delete-reminders');
                }
                $row[] = $_data;
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }
    public function delete_group_reports($id)
    {
        if (!$id) {
            die('ch_no_items');
        }
        if (!$this->preDeleteGroupReports) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $this->db->select('tbl_group_reports.*');
        $this->db->from('tbl_group_reports');
        $this->db->where('tbl_group_reports.id', $id);
        $dtData = $this->db->get()->row_array();
        $success    = false;
        $this->db->where('id', $id);
        $this->db->delete('tbl_group_reports');
        if ($this->db->affected_rows() > 0) {
            insertActivityLog([
                'type_parent_obj' => 'group_reports',
                'table_obj' => 'tbl_group_reports',
                'id_obj' => $id,
                'name_obj' => $dtData['code'],
                'content' => lang('Xóa loại báo cáo') . ' [' . $dtData['code'] . ']',
                'actions' => 'delete'
            ]);
            $success    = true;
        }
        $alert_type = 'warning';
        $message    = _l('ch_no_delete');
        if ($success) {
            $alert_type = 'success';
            $message    = _l('ch_delete');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }
    public function get_row_group_reports($id)
    {
        $this->db->select('tbl_group_reports.*');
        $this->db->where('tbl_group_reports.id', $id);
        $data = $this->db->get('tbl_group_reports')->row();
        echo json_encode($data);
    }
    public function update_group_reports($id = "")
    {
        if ($id != "") {
            if (!$this->preEditGroupReports) {
                $data['success'] = false;
                $data['message'] = lang('sum_to_edit_status');
                echo json_encode($data);
                die();
            }
            $message    = '';
            $alert_type = 'warning';
            $this->db->select('tbl_group_reports.*');
            $this->db->from('tbl_group_reports');
            $this->db->where('tbl_group_reports.id', $id);
            $dtData = $this->db->get()->row_array();
            if ($this->input->post()) {
                if (!empty($dtData) && $dtData['code'] != $this->input->post('code')) {
                    $this->form_validation->set_rules('code', lang("ch_code_group_reports"), 'trim|required|is_unique[tbl_group_reports.code]');
                }
                $this->form_validation->set_rules('name', lang("ch_name_group_reports"), 'required');
                if ($this->form_validation->run() == true) {
                    $data = $this->input->post(NULL, FALSE);
                    $this->db->where('id', $id);
                    $id = $this->db->update('tbl_group_reports', $data);
                    if ($id) {
                        insertActivityLog([
                            'type_parent_obj' => 'group_reports',
                            'table_obj' => 'tbl_group_reports',
                            'id_obj' => $id,
                            'name_obj' => $dtData['code'],
                            'content' => lang('Sửa loại báo cáo') . ' [' . $dtData['code'] . ']',
                            'actions' => 'edit'
                        ]);
                        $success = true;
                        $message = _l('ch_updatee_items');
                    }
                } else {
                    $success = 0;
                    $message = validation_errors();
                }
            }
            echo json_encode(array(
                'success' => $success,
                'message' => $message
            ));
        } else {
            if ($this->input->post()) {
                if (!$this->preAddGroupReports) {
                    $data['success'] = false;
                    $data['message'] = lang('sum_to_add_status');
                    echo json_encode($data);
                    die();
                }
                $this->form_validation->set_rules('code', lang("ch_code_group_reports"), 'trim|required|is_unique[tbl_group_reports.code]');
                $this->form_validation->set_rules('name', lang("ch_name_group_reports"), 'required');
                $message = '';
                if ($this->form_validation->run() == true) {
                    $data = $this->input->post(NULL, FALSE);
                    $data['staff_create'] = get_staff_user_id();
                    $data['date_create'] = date('Y-m-d H:i:s');
                    $this->db->insert('tbl_group_reports', $data);
                    $id = $this->db->insert_id();
                    if ($id) {
                        insertActivityLog([
                            'type_parent_obj' => 'group_reports',
                            'table_obj' => 'tbl_group_reports',
                            'id_obj' => $id,
                            'name_obj' => $data['code'],
                            'content' => lang('Thêm mới loại báo cáo') . ' [' . $data['code'] . ']',
                            'actions' => 'add'
                        ]);
                        $success = true;
                        $message = _l('ch_added_successfuly');
                    }
                } else {
                    $success = 0;
                    $message = validation_errors();
                }
            }
            echo json_encode(array(
                'alert_type' => $success,
                'message' => $message
            ));
        }
        die;
    }
    public function add_group_reports()
    {
        $success = false;
        $message = _l('Thêm không thành công');
        if ($this->input->post()) {
            if (!$this->preAddGroupReports) {
                $data['success'] = false;
                $data['message'] = lang('sum_to_add_status');
                echo json_encode($data);
                die();
            }
            $this->form_validation->set_rules('code', lang("ch_code_group_reports"), 'trim|required|is_unique[tbl_group_reports.code]');
            $this->form_validation->set_rules('name', lang("ch_name_group_reports"), 'required');
            $message = '';
            if ($this->form_validation->run() == true) {
                $data = $this->input->post(NULL, FALSE);
                $data['staff_create'] = get_staff_user_id();
                $data['date_create'] = date('Y-m-d H:i:s');
                $this->db->insert('tbl_group_reports', $data);
                $id = $this->db->insert_id();
                if ($id) {
                    insertActivityLog([
                        'type_parent_obj' => 'group_reports',
                        'table_obj' => 'tbl_group_reports',
                        'id_obj' => $id,
                        'name_obj' => $data['code'],
                        'content' => lang('Thêm mới loại báo cáo') . ' [' . $data['code'] . ']',
                        'actions' => 'add'
                    ]);
                    $success = true;
                    $message = _l('ch_added_successfuly');
                }
            } else {
                $success = 0;
                $message = validation_errors();
            }
            echo json_encode(array(
                'success' => $success,
                'message' => $message
            ));
            die;
        }
    }
}
