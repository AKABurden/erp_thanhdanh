<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Type_improve extends AdminController
{
    function __construct()
    {
        parent::__construct();
        $this->preViewTypeImprove = has_permission('type_improve','','view');
        $this->preAddTypeImprove = has_permission('type_improve','','create');
        $this->preEditTypeImprove = has_permission('type_improve','','edit');
        $this->preDeleteTypeImprove = has_permission('type_improve','','delete');
    }
    public function index()
    {
        if (!$this->preViewTypeImprove) {
            access_denied();
        }
        $data['title'] = _l('ch_type_improve');
        $this->load->view('admin/type_improve/manage', $data);
    }
    public function table()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_type_improve.id as id',
            'tbl_type_improve.code as code',
            'tbl_type_improve.name as name',
            '1'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_type_improve';
        $where = [];
        $filter = [];
        $j = 0;
        $join = [];
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tbl_type_improve.id'], '', []);
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
                if ($aColumns[$i] == 'tbl_type_improve.id as id') {
                    $_data = '<div class="text-center">' . $j . '</div>';
                }
                if ($aColumns[$i] == '1') {
                    $_data = '<a href="#" class="btn btn-default btn-icon" onclick="view_init_department(' . $aRow['id'] . '); return false;"><i class="fa fa-eye"></i></a>';
                    $_data .= icon_btn('type_improve/delete_type_improve/' . $aRow['id'], 'remove', 'btn-danger delete-reminders');
                }
                $row[] = $_data;
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }
    public function delete_type_improve($id)
    {
        if (!$id) {
            die('ch_no_items');
        }
        if (!$this->preDeleteTypeImprove) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $this->db->from('tbl_request_improve');
        $this->db->where('tbl_request_improve.type_improve', $id);
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if (!empty($q)) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_no_delete_esxit')
            ));
            die;
        }

        $this->db->select('tbl_type_improve.*');
        $this->db->from('tbl_type_improve');
        $this->db->where('tbl_type_improve.id', $id);
        $dtData = $this->db->get()->row_array();
        $success    = false;
        $this->db->where('id', $id);
        $this->db->delete('tbl_type_improve');
        if ($this->db->affected_rows() > 0) {
            insertActivityLog([
                'type_parent_obj' => 'type_improve',
                'table_obj' => 'tbl_type_improve',
                'id_obj' => $id,
                'name_obj' => $dtData['code'],
                'content' => lang('Xóa loại cải tiến') . ' [' . $dtData['code'] . ']',
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
    public function get_row_type_improve($id)
    {
        $this->db->select('tbl_type_improve.*');
        $this->db->where('tbl_type_improve.id', $id);
        $data = $this->db->get('tbl_type_improve')->row();
        echo json_encode($data);
    }
    public function update_type_improve($id = "")
    {
        if ($id != "") {
            if (!$this->preEditTypeImprove) {
                $data['success'] = false;
                $data['message'] = lang('sum_to_edit_status');
                echo json_encode($data);
                die();
            }
            $message    = '';
            $alert_type = 'warning';
            $this->db->select('tbl_type_improve.*');
            $this->db->from('tbl_type_improve');
            $this->db->where('tbl_type_improve.id', $id);
            $dtData = $this->db->get()->row_array();
            if ($this->input->post()) {
                if (!empty($dtData) && $dtData['code'] != $this->input->post('code')) {
                    $this->form_validation->set_rules('code', lang("ch_code_type_improve"), 'trim|required|is_unique[tbl_type_improve.code]');
                }
                $this->form_validation->set_rules('name', lang("ch_name_type_improve"), 'required');
                if ($this->form_validation->run() == true) {
                    $data = $this->input->post(NULL, FALSE);
                    $this->db->where('id', $id);
                    $id = $this->db->update('tbl_type_improve', $data);
                    if ($id) {
                        insertActivityLog([
                            'type_parent_obj' => 'type_improve',
                            'table_obj' => 'tbl_type_improve',
                            'id_obj' => $id,
                            'name_obj' => $dtData['code'],
                            'content' => lang('Sửa loại cải tiến') . ' [' . $dtData['code'] . ']',
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
                if (!$this->preAddTypeImprove) {
                    $data['success'] = false;
                    $data['message'] = lang('sum_to_add_status');
                    echo json_encode($data);
                    die();
                }
                $this->form_validation->set_rules('code', lang("ch_code_type_improve"), 'trim|required|is_unique[tbl_type_improve.code]');
                $this->form_validation->set_rules('name', lang("ch_name_type_improve"), 'required');
                $message = '';
                if ($this->form_validation->run() == true) {
                    $data = $this->input->post(NULL, FALSE);
                    $data['created_by'] = get_staff_user_id();
                    $data['date_create'] = date('Y-m-d H:i:s');
                    $this->db->insert('tbl_type_improve', $data);
                    $id = $this->db->insert_id();
                    if ($id) {
                        insertActivityLog([
                            'type_parent_obj' => 'type_improve',
                            'table_obj' => 'tbl_type_improve',
                            'id_obj' => $id,
                            'name_obj' => $data['code'],
                            'content' => lang('Thêm mới loại cải tiến') . ' [' . $data['code'] . ']',
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
    public function add_type_improve()
    {
        $success = false;
        $message = _l('Thêm không thành công');
        if ($this->input->post()) {
            if (!$this->preAddTypeImprove) {
                $data['success'] = false;
                $data['message'] = lang('sum_to_add_status');
                echo json_encode($data);
                die();
            }
            $this->form_validation->set_rules('code', lang("ch_code_type_improve"), 'trim|required|is_unique[tbl_type_improve.code]');
            $this->form_validation->set_rules('name', lang("ch_name_type_improve"), 'required');
            $message = '';
            if ($this->form_validation->run() == true) {
                $data = $this->input->post(NULL, FALSE);
                $data['created_by'] = get_staff_user_id();
                $data['date_create'] = date('Y-m-d H:i:s');
                $this->db->insert('tbl_type_improve', $data);
                $id = $this->db->insert_id();
                if ($id) {
                    insertActivityLog([
                        'type_parent_obj' => 'type_improve',
                        'table_obj' => 'tbl_type_improve',
                        'id_obj' => $id,
                        'name_obj' => $data['code'],
                        'content' => lang('Thêm mới loại cải tiến') . ' [' . $data['code'] . ']',
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
