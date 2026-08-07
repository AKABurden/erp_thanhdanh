<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Board extends AdminController
{
    function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $data['title'] = _l('ch_board');
        $this->load->view('admin/boards/manage', $data);
    }
    public function table()
    {

        $aColumns = [
            'tbl_board.id as id',
            'tbl_board.code as code',
            'tbl_board.name as name',
            '1'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_board';
        $where = [];
        $filter = [];
        $j = 0;
        $join = [];
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tbl_board.id'], '', []);
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
                if ($aColumns[$i] == 'tbl_board.id as id') {
                    $_data = '<div class="text-center">' . $j . '</div>';
                }
                if ($aColumns[$i] == '1') {
                    $_data = '<a href="#" class="btn btn-default btn-icon" onclick="view_init_department(' . $aRow['id'] . '); return false;"><i class="fa fa-eye"></i></a>';
                    $_data .= icon_btn('board/delete_board/' . $aRow['id'], 'remove', 'btn-danger delete-reminders');
                }
                $row[] = $_data;
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }
    public function delete_board($id)
    {
        if (!$id) {
            die('ch_no_items');
        }
        $this->db->select('tbl_board.*');
        $this->db->from('tbl_board');
        $this->db->where('tbl_board.id', $id);
        $dtData = $this->db->get()->row_array();
        $success    = false;
        $this->db->where('id', $id);
        $this->db->delete('tbl_board');
        if ($this->db->affected_rows() > 0) {
            $success    = true;
        }
        $alert_type = 'warning';
        $message    = _l('ch_no_delete');
        if ($success) {
            insertActivityLog([
                'type_parent_obj' => 'board',
                'table_obj' => 'tbl_board',
                'id_obj' => $id,
                'name_obj' => $dtData['code'],
                'content' => lang('Xóa ban') . ' [' . $dtData['code'] . ']',
                'actions' => 'delete'
            ]);
            $alert_type = 'success';
            $message    = _l('ch_delete');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }
    public function get_row_board($id)
    {
        $this->db->select('tbl_board.*');
        $this->db->where('tbl_board.id', $id);
        $data = $this->db->get('tbl_board')->row();
        echo json_encode($data);
    }
    public function update_board($id = "")
    {
        if ($id != "") {
            $message    = '';
            $alert_type = 'warning';
            $this->db->select('tbl_board.*');
            $this->db->from('tbl_board');
            $this->db->where('tbl_board.id', $id);
            $dtData = $this->db->get()->row_array();
            if ($this->input->post()) {
                if (!empty($dtData) && $dtData['code'] != $this->input->post('code')) {
                    $this->form_validation->set_rules('code', lang("ch_code_board"), 'trim|required|is_unique[tbl_board.code]');
                }
                $this->form_validation->set_rules('name', lang("ch_name_board"), 'required');
                if ($this->form_validation->run() == true) {
                    $data = $this->input->post(NULL, FALSE);
                    $this->db->where('id', $id);
                    $id = $this->db->update('tbl_board', $data);
                    if ($id) {
                        insertActivityLog([
                            'type_parent_obj' => 'board',
                            'table_obj' => 'tbl_board',
                            'id_obj' => $id,
                            'name_obj' => $dtData['code'],
                            'content' => lang('Sửa ban') . ' [' . $dtData['code'] . ']',
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
                $this->form_validation->set_rules('code', lang("ch_code_board"), 'trim|required|is_unique[tbl_board.code]');
                $this->form_validation->set_rules('name', lang("ch_name_board"), 'required');
                $message = '';
                if ($this->form_validation->run() == true) {
                    $data = $this->input->post(NULL, FALSE);
                    $data['created_by'] = get_staff_user_id();
                    $data['date_created'] = date('Y-m-d H:i:s');
                    $this->db->insert('tbl_board', $data);
                    $id = $this->db->insert_id();
                    if ($id) {
                        insertActivityLog([
                            'type_parent_obj' => 'board',
                            'table_obj' => 'tbl_board',
                            'id_obj' => $id,
                            'name_obj' => $data['code'],
                            'content' => lang('Thêm mới ban') . ' [' . $data['code'] . ']',
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
    public function add_board()
    {
        $success = false;
        $message = _l('Thêm không thành công');
        if ($this->input->post()) {
            $this->form_validation->set_rules('code', lang("ch_code_board"), 'trim|required|is_unique[tbl_board.code]');
            $this->form_validation->set_rules('name', lang("ch_name_board"), 'required');
            $message = '';
            if ($this->form_validation->run() == true) {
                $data = $this->input->post(NULL, FALSE);
                $data['created_by'] = get_staff_user_id();
                $data['date_created'] = date('Y-m-d H:i:s');
                $this->db->insert('tbl_board', $data);
                $id = $this->db->insert_id();
                if ($id) {
                    insertActivityLog([
                        'type_parent_obj' => 'board',
                        'table_obj' => 'tbl_board',
                        'id_obj' => $id,
                        'name_obj' => $data['code'],
                        'content' => lang('Thêm mới ban') . ' [' . $data['code'] . ']',
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
