<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Room extends AdminController
{
    function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $data['title'] = _l('ch_room');
        $this->load->view('admin/room/manage', $data);
    }
    public function table()
    {

        $aColumns = [
            'tbl_room.id as id',
            'tbl_room.code as code',
            'tbl_room.name as name',
            'tbl_block.name as block_name',
            'tbl_room.email as email',
            '"" as dep_head',
            'tbl_room.room_goals as room_goals',
            'tbl_room.budget as budget',
            'tbl_room.status as status',
            'tbl_room.effective_from as effective_from',
            'tbl_room.effective_to as effective_to',
            'tbl_room.policy_link as policy_link',
            'tbl_room.workspace_link as workspace_link',
            'tbl_room.note as note',
            '1'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_room';
        $where = [];
        $filter = [];
        $j = 0;
        $join = [];
        $join[] = 'LEFT JOIN tbl_block ON tbl_block.id = tbl_room.block_id';
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tbl_room.id'], '', []);
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
                if ($aColumns[$i] == 'tbl_room.id as id') {
                    $_data = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
                }
                if ($aColumns[$i] == 'tbl_room.budget as budget') {
                    $_data = '<div>'.(!empty($aRow['budget']) ? formatMoney($aRow['budget']) : '').'</div>';
                }
                if ($aColumns[$i] == 'tbl_room.effective_from as effective_from') {
                    $_data = '<div>'.(!empty($aRow['effective_from']) ? _dhau($aRow['effective_from']) : '').'</div>';
                }
                if ($aColumns[$i] == 'tbl_room.effective_to as effective_to') {
                    $_data = '<div>'.(!empty($aRow['effective_to']) ? _dhau($aRow['effective_to']) : '').'</div>';
                }
                if ($aColumns[$i] == 'tbl_room.status as status') {
                    $checked = '';
                    if ($aRow['status'] == 1) {
                        $checked = 'checked';
                    }
                    $_data = '<div class="onoffswitch">
                        <input type="checkbox" data-switch-url="' . admin_url() . 'room/changeStatus" name="onoffswitch_new" class="onoffswitch-checkbox" id="c_new' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . $checked . '>
                        <label class="onoffswitch-label" for="c_new' . $aRow['id'] . '"></label>
                    </div>';
                }
                if ($aColumns[$i] == '1') {
                    $_data = '<a href="'.base_url('admin/room/update_room/'.$aRow['id'].'').'" class="btn btn-default btn-icon tnh-modal" ><i class="fa fa-edit"></i></a>';
                    $_data .= icon_btn('room/delete_room/' . $aRow['id'], 'remove', 'btn-danger delete-reminders');
                }
                $row[] = $_data;
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }
    public function delete_room($id)
    {
        if (!$id) {
            die('ch_no_items');
        }
        $this->db->select('tbl_room.*');
        $this->db->from('tbl_room');
        $this->db->where('tbl_room.id', $id);
        $dtData = $this->db->get()->row_array();
        $success    = false;
        $this->db->where('id', $id);
        $this->db->delete('tbl_room');
        if ($this->db->affected_rows() > 0) {
            $success    = true;
        }
        $alert_type = 'warning';
        $message    = _l('ch_no_delete');
        if ($success) {
            insertActivityLog([
                'type_parent_obj' => 'room',
                'table_obj' => 'tbl_room',
                'id_obj' => $id,
                'name_obj' => $dtData['code'],
                'content' => lang('Xóa phòng') . ' [' . $dtData['code'] . ']',
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
    public function get_row_room($id)
    {
        $this->db->select('tbl_room.*');
        $this->db->where('tbl_room.id', $id);
        $data = $this->db->get('tbl_room')->row();
        echo json_encode($data);
    }
    public function update_room($id = "")
    {
        if ($this->input->post()) {
            if ($id != "") {
                $message = '';
                $alert_type = 'warning';
                $this->db->select('tbl_room.*');
                $this->db->from('tbl_room');
                $this->db->where('tbl_room.id', $id);
                $dtData = $this->db->get()->row_array();
                if ($this->input->post()) {
                    if (!empty($dtData) && $dtData['code'] != $this->input->post('code')) {
                        $this->form_validation->set_rules('code', lang("ch_code_room"),
                            'trim|required|is_unique[tbl_room.code]');
                    }
                    $this->form_validation->set_rules('name', lang("ch_name_room"), 'required');
                    if ($this->form_validation->run() == true) {
                        $data = $this->input->post(null, false);
                        $data['effective_from'] = !empty($data['effective_from']) ? to_sql_date($data['effective_from']) : null;
                        $data['effective_to'] = !empty($data['effective_to']) ? to_sql_date($data['effective_to']) : null;
                        $data['budget'] = !empty($data['budget']) ? number_unformat($data['budget']) : 0;
                        $this->db->where('id', $id);
                        $success = $this->db->update('tbl_room', $data);
                        if ($success) {
                            $optionDeparment = [
                                'code' => $data['code'],
                                'name' => $data['name'],
                                'room_id' => $id,
                            ];
                            $this->db->from('tbldepartments');
                            $this->db->where('tbldepartments.room_id', $id);
                            $checkExists = $this->db->get()->row_array();
                            if (empty($checkExists)) {
                                $this->db->insert('tbldepartments', $optionDeparment);
                            } else {
                                $this->db->where('room_id', $id);
                                $this->db->update('tbldepartments', $optionDeparment);
                            }
                            insertActivityLog([
                                'type_parent_obj' => 'room',
                                'table_obj' => 'tbl_room',
                                'id_obj' => $id,
                                'name_obj' => $dtData['code'],
                                'content' => lang('Sửa phòng') . ' [' . $dtData['code'] . ']',
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
                    'result' => $success,
                    'message' => $message
                ));
            } else {
                if ($this->input->post()) {
                    $this->form_validation->set_rules('code', lang("ch_code_room"),
                        'trim|required|is_unique[tbl_room.code]');
                    $this->form_validation->set_rules('name', lang("ch_name_room"), 'required');
                    $message = '';
                    if ($this->form_validation->run() == true) {
                        $data = $this->input->post(null, false);
                        $data['created_by'] = get_staff_user_id();
                        $data['date_created'] = date('Y-m-d H:i:s');
                        $this->db->insert('tbl_room', $data);
                        $id = $this->db->insert_id();
                        if ($id) {
                            $optionDeparment = [
                                'code' => $data['code'],
                                'name' => $data['name'],
                                'room_id' => $id,
                            ];
                            $this->db->insert('tbldepartments', $optionDeparment);
                            insertActivityLog([
                                'type_parent_obj' => 'room',
                                'table_obj' => 'tbl_room',
                                'id_obj' => $id,
                                'name_obj' => $data['code'],
                                'content' => lang('Thêm mới phòng') . ' [' . $data['code'] . ']',
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
                    'result' => $success,
                    'message' => $message
                ));
            }
            die;
        }

        if (empty($id)){
            $title = lang('Thêm phòng ban');
        } else {
            $title = lang('Sửa phòng ban');
        }
        $dtData = get_table_where('tbl_room',['id' => $id],'','row_array');
        $dtBlock = get_table_where('tbl_block');
        $data['dtData'] = $dtData;
        $data['dtBlock'] = $dtBlock;
        $data['id'] = $id;
        $data['title'] = $title;
        $this->load->view('admin/room/detail',$data);
    }
    public function add_room()
    {
        $success = false;
        $message = _l('Thêm không thành công');
        if ($this->input->post()) {
            $this->form_validation->set_rules('code', lang("ch_code_room"), 'trim|required|is_unique[tbl_room.code]');
            $this->form_validation->set_rules('name', lang("ch_name_room"), 'required');
            $message = '';
            if ($this->form_validation->run() == true) {
                $data = $this->input->post(NULL, FALSE);
                $data['created_by'] = get_staff_user_id();
                $data['date_created'] = date('Y-m-d H:i:s');
                $this->db->insert('tbl_room', $data);
                $id = $this->db->insert_id();
                if ($id) {
                    $optionDeparment = [
                        'code' => $data['code'],
                        'name' => $data['name'],
                        'room_id' => $id,
                    ];
                    $this->db->insert('tbldepartments',$optionDeparment);
                    insertActivityLog([
                        'type_parent_obj' => 'room',
                        'table_obj' => 'tbl_room',
                        'id_obj' => $id,
                        'name_obj' => $data['code'],
                        'content' => lang('Thêm mới phòng') . ' [' . $data['code'] . ']',
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

    public function changeStatus($id, $status){
        $data = [];
        $this->db->where('id', $id);
        $success = $this->db->update('tbl_room', [
            'status' => $status
        ]);

        if ($success) {
            $data['result'] = 1;
            $data['message'] = lang('Thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Thất bại');
        }
        echo json_encode($data);
    }

    public function print_pdf_html()
    {
        ob_start();
        $data = [];
        $ids = $this->input->get('ids');
        $arrId = explode(',', $ids);
        $title = lang('IN QR PHÒNG');
        $items = null;
        if (!empty($arrId)) {
            $this->db->select("
                tbl_room.*,
            ");
            $this->db->from('tbl_room');
            $this->db->where_in('tbl_room.id', $arrId);
            $items = $this->db->get()->result_array();
        }
        $data['items'] = $items;

        $content = ob_get_contents();

        $data['object'] = "room";
        $data['hide'] = 'hide';
        $data['title'] = $title;
        $data['content'] = $content;
        ob_end_clean();
        $pdf = print_pdf_qr_dtmv($data);
        $type = 'I';
        if ($type == "S") {
            return $pdf->Output(slug_it('qr') . '.pdf', $type);
        } else {
            $pdf->Output(slug_it('qr') . '.pdf', $type);
        }
    }

    public function getListRoom(){
        $tb_branch = "(
            SELECT
                GROUP_CONCAT(tblbranch.name) as branch_name,
                tblstaff_branch.staffid
            FROM tblstaff_branch
            JOIN tblbranch ON tblbranch.id = tblstaff_branch.id_branch
            GROUP BY tblstaff_branch.staffid
        ) tb_branch";
        $aColumns = [
            'tbl_room.id as id',
            'tblstaff.code as code_staff',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name_staff',
            'tb_branch.branch_name as branch',
            'tbl_room.code as code',
            'tbl_room.name as name',
            'tbl_block.name as block_name',
            'tbl_room.email as email',
            '"" as dep_head',
            'tbl_room.room_goals as room_goals',
            'tbl_room.budget as budget',
            'tbl_room.status as status',
            'tbl_room.effective_from as effective_from',
            'tbl_room.effective_to as effective_to',
            'tbl_room.policy_link as policy_link',
            'tbl_room.workspace_link as workspace_link',
            'tbl_room.note as note',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_room';
        $where = [];
        $filter = [];
        $j = 0;
        $join = [];
        $join[] = 'LEFT JOIN tbl_block ON tbl_block.id = tbl_room.block_id';
        $join[] = 'INNER JOIN tbldepartments ON tbldepartments.room_id = tbl_room.id';
        $join[] = 'LEFT JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid';
        $join[] = 'LEFT JOIN tblstaff ON tblstaff.staffid = tblstaff_departments.staffid';
        $join[] = 'LEFT JOIN '.$tb_branch.' ON tb_branch.staffid = tblstaff.staffid';
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tbl_room.id,tblstaff.staffid as staff_id'], '', []);
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
                if ($aColumns[$i] == 'tbl_room.id as id') {
                    $_data = '<div class="text-center">'.$j.'</div>';
                }
                if ($aColumns[$i] == 'tbl_room.budget as budget') {
                    $_data = '<div>'.(!empty($aRow['budget']) ? formatMoney($aRow['budget']) : '').'</div>';
                }
                if ($aColumns[$i] == 'tbl_room.effective_from as effective_from') {
                    $_data = '<div>'.(!empty($aRow['effective_from']) ? _dhau($aRow['effective_from']) : '').'</div>';
                }
                if ($aColumns[$i] == 'tbl_room.effective_to as effective_to') {
                    $_data = '<div>'.(!empty($aRow['effective_to']) ? _dhau($aRow['effective_to']) : '').'</div>';
                }
                if ($aColumns[$i] == 'tblstaff.code as code_staff') {
                    $_data = '<div><a target="_blank" href="'.base_url('admin/staff/profile/'.$aRow['staff_id'].'').'">'.($aRow['code_staff']).'</a></div>';
                }
                if ($aColumns[$i] == 'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name_staff') {
                    $_data = '<div><a target="_blank" href="'.base_url('admin/staff/profile/'.$aRow['staff_id'].'').'">'.($aRow['name_staff']).'</a></div>';
                }
                if ($aColumns[$i] == 'tbl_room.status as status') {
                    $checked = '';
                    if ($aRow['status'] == 1) {
                        $checked = 'checked';
                    }
                    $_data = '<div class="onoffswitch">
                        <input type="checkbox" data-switch-url="' . admin_url() . 'room/changeStatus" name="onoffswitch_new" class="onoffswitch-checkbox" id="c_new' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . $checked . '>
                        <label class="onoffswitch-label" for="c_new' . $aRow['id'] . '"></label>
                    </div>';
                }
                $row[] = $_data;
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }
}
