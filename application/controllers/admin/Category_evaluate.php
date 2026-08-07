<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Category_evaluate extends AdminController
{
    function __construct()
    {
        parent::__construct();
        $this->preViewCategoryEvaluate = has_permission('type_evaluate','','view');
        $this->preAddCategoryEvaluate = has_permission('type_evaluate','','create');
        $this->preEditCategoryEvaluate = has_permission('type_evaluate','','edit');
        $this->preDeleteCategoryEvaluate = has_permission('type_evaluate','','delete');

        $this->preViewCategoryEvaluateDetail = has_permission('category_evaluate','','view');
        $this->preAddCategoryEvaluateDetail = has_permission('category_evaluate','','create');
        $this->preEditCategoryEvaluateDetail = has_permission('category_evaluate','','edit');
        $this->preDeleteCategoryEvaluateDetail = has_permission('category_evaluate','','delete');
    }
    public function index()
    {
        if (!$this->preViewCategoryEvaluate) {
            access_denied();
        }
        $data['title'] = _l('ch_type_evaluate');
        $this->load->view('admin/category_evaluate/manage', $data);
    }
    public function table()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_category_evaluate.id as id',
            'tbl_category_evaluate.code as code',
            'tbl_category_evaluate.name as name',
            '1'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_category_evaluate';
        $where = [];
        $filter = [];
        $j = 0;
        $join = [];
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tbl_category_evaluate.id'], '', []);
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
                if ($aColumns[$i] == 'tbl_category_evaluate.id as id') {
                    $_data = '<div class="text-center">' . $j . '</div>';
                }
                if ($aColumns[$i] == '1') {
                    $_data = '<a href="#" class="btn btn-default btn-icon" onclick="view_init_department(' . $aRow['id'] . '); return false;"><i class="fa fa-eye"></i></a>';
                    $_data .= icon_btn('category_evaluate/delete_category_evaluate/' . $aRow['id'], 'remove', 'btn-danger delete-reminders');
                }
                $row[] = $_data;
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }
    public function delete_category_evaluate($id)
    {
        if (!$id) {
            die('ch_no_items');
        }
        if (!$this->preDeleteCategoryEvaluate) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $this->db->from('tbl_evaluate');
        $this->db->where('tbl_evaluate.category_evaluate_id', $id);
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if (!empty($q)) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_no_delete_esxit')
            ));
            die;
        }

        $this->db->select('tbl_category_evaluate.*');
        $this->db->from('tbl_category_evaluate');
        $this->db->where('tbl_category_evaluate.id', $id);
        $dtData = $this->db->get()->row_array();
        $success    = false;
        $this->db->where('id', $id);
        $this->db->delete('tbl_category_evaluate');
        if ($this->db->affected_rows() > 0) {
            insertActivityLog([
                'type_parent_obj' => 'category_evaluate',
                'table_obj' => 'tbl_category_evaluate',
                'id_obj' => $id,
                'name_obj' => $dtData['code'],
                'content' => lang('Xóa loại đánh giá') . ' [' . $dtData['code'] . ']',
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
    public function get_row_category_evaluate($id)
    {
        $this->db->select('tbl_category_evaluate.*');
        $this->db->where('tbl_category_evaluate.id', $id);
        $data = $this->db->get('tbl_category_evaluate')->row();
        echo json_encode($data);
    }
    public function update_category_evaluate($id = "")
    {
        if ($id != "") {
            if (!$this->preEditCategoryEvaluate) {
                $data['success'] = false;
                $data['message'] = lang('sum_to_edit_status');
                echo json_encode($data);
                die();
            }
            $message    = '';
            $alert_type = 'warning';
            $this->db->select('tbl_category_evaluate.*');
            $this->db->from('tbl_category_evaluate');
            $this->db->where('tbl_category_evaluate.id', $id);
            $dtData = $this->db->get()->row_array();
            if ($this->input->post()) {
                if (!empty($dtData) && $dtData['code'] != $this->input->post('code')) {
                    $this->form_validation->set_rules('code', lang("ch_code_category_evaluate"), 'trim|required|is_unique[tbl_category_evaluate.code]');
                }
                $this->form_validation->set_rules('name', lang("ch_name_category_evaluate"), 'required');
                if ($this->form_validation->run() == true) {
                    $data = $this->input->post(NULL, FALSE);
                    $this->db->where('id', $id);
                    $id = $this->db->update('tbl_category_evaluate', $data);
                    if ($id) {
                        insertActivityLog([
                            'type_parent_obj' => 'category_evaluate',
                            'table_obj' => 'tbl_category_evaluate',
                            'id_obj' => $id,
                            'name_obj' => $dtData['code'],
                            'content' => lang('Sửa loại đánh giá') . ' [' . $dtData['code'] . ']',
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
                if (!$this->preAddCategoryEvaluate) {
                    $data['success'] = false;
                    $data['message'] = lang('sum_to_add_status');
                    echo json_encode($data);
                    die();
                }
                $this->form_validation->set_rules('code', lang("ch_code_category_evaluate"), 'trim|required|is_unique[tbl_category_evaluate.code]');
                $this->form_validation->set_rules('name', lang("ch_name_category_evaluate"), 'required');
                $message = '';
                if ($this->form_validation->run() == true) {
                    $data = $this->input->post(NULL, FALSE);
                    $data['created_by'] = get_staff_user_id();
                    $data['date_created'] = date('Y-m-d H:i:s');
                    $this->db->insert('tbl_category_evaluate', $data);
                    $id = $this->db->insert_id();
                    if ($id) {
                        insertActivityLog([
                            'type_parent_obj' => 'category_evaluate',
                            'table_obj' => 'tbl_category_evaluate',
                            'id_obj' => $id,
                            'name_obj' => $data['code'],
                            'content' => lang('Thêm mới loại đánh giá') . ' [' . $data['code'] . ']',
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
    public function add_category_evaluate()
    {
        $success = false;
        $message = _l('Thêm không thành công');
        if ($this->input->post()) {
            if (!$this->preAddCategoryEvaluate) {
                $data['success'] = false;
                $data['message'] = lang('sum_to_add_status');
                echo json_encode($data);
                die();
            }
            $this->form_validation->set_rules('code', lang("ch_code_category_evaluate"), 'trim|required|is_unique[tbl_category_evaluate.code]');
            $this->form_validation->set_rules('name', lang("ch_name_category_evaluate"), 'required');
            $message = '';
            if ($this->form_validation->run() == true) {
                $data = $this->input->post(NULL, FALSE);
                $data['created_by'] = get_staff_user_id();
                $data['date_created'] = date('Y-m-d H:i:s');
                $this->db->insert('tbl_category_evaluate', $data);
                $id = $this->db->insert_id();
                if ($id) {
                    insertActivityLog([
                        'type_parent_obj' => 'category_evaluate',
                        'table_obj' => 'tbl_category_evaluate',
                        'id_obj' => $id,
                        'name_obj' => $data['code'],
                        'content' => lang('Thêm mới loại đánh giá') . ' [' . $data['code'] . ']',
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

    public function category_evaluate(){
        if (!$this->preViewCategoryEvaluateDetail) {
            access_denied();
        }
        $data['title'] = _l('ch_category_evaluate');
        $this->load->view('admin/category_evaluate/category_evaluate', $data);
    }

    public function getCategoryEvaluate(){
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_category_evaluate_detail.id as id',
            'tbl_category_evaluate.name as name_category_evaluate',
            'tbl_category_evaluate_detail.name as name',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_category_evaluate_detail';
        $where = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tbl_category_evaluate ON tbl_category_evaluate.id = tbl_category_evaluate_detail.category_evaluate_id'
        ];


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left">'.$aRow['name_category_evaluate'].'</div>';
            $row[] = '<div class="text-left">'.$aRow['name'].'</div>';

            $edit = $this->preEditCategoryEvaluateDetail ? '<a class="tnh-modal" href="' . base_url('admin/category_evaluate/category_evaluate_detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit')  . '</a>' : '';

            $delete = $this->preDeleteCategoryEvaluateDetail ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/category_evaluate/category_evaluate_delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete')  . '</a>' : '';
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

    public function category_evaluate_detail($id = 0){
        $data = [];
        $this->db->select('tbl_category_evaluate_detail.*');
        $this->db->from('tbl_category_evaluate_detail');
        $this->db->where('tbl_category_evaluate_detail.id',$id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()){
            $this->form_validation->set_rules('name', lang("Tên nhóm đánh giá"), 'required');
            $this->form_validation->set_rules('category_evaluate_id', lang("Loại đánh giá"), 'required');
            if (empty($id)){
                if ($this->form_validation->run() == true) {
                    $category_evaluate_id = ($this->input->post('category_evaluate_id'));
                    $name = ($this->input->post('name'));
                    $fields = [
                        'category_evaluate_id' => $category_evaluate_id,
                        'name' => $name,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->insert('tbl_category_evaluate_detail',$fields);
                    $id = $this->db->insert_id();
                    if ($id){
                        insertActivityLog([
                            'type_parent_obj' => 'category_evaluate_detail',
                            'table_obj' => 'tbl_category_evaluate_detail',
                            'id_obj' => $id,
                            'name_obj' => $name,
                            'content' => lang('Thêm mới nhóm đánh giá') . ' [' . $name . ']',
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
                    $category_evaluate_id = ($this->input->post('category_evaluate_id'));
                    $name = ($this->input->post('name'));
                    $fields = [
                        'category_evaluate_id' => $category_evaluate_id,
                        'name' => $name,
                    ];
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_category_evaluate_detail',$fields);
                    if ($success){
                        insertActivityLog([
                            'type_parent_obj' => 'category_evaluate_detail',
                            'table_obj' => 'tbl_category_evaluate_detail',
                            'id_obj' => $id,
                            'name_obj' => $dtData['name'],
                            'content' => lang('Sửa nhóm đánh giá') . ' [' . $dtData['name'] . ']',
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
                if (!$this->preAddCategoryEvaluateDetail){
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_category_evaluate');
            } else {
                if (!$this->preEditCategoryEvaluateDetail){
                    accessDenied(true);
                }
                $data['dtData'] = $dtData;
                $data['title'] = lang('dt_edit_category_evaluate');
            }
        }
        $data['dtTypeEvaluate'] = get_table_where('tbl_category_evaluate');
        $data['id'] = $id;
        $this->load->view('admin/category_evaluate/detail_category_evaluate',$data);
    }

    public function category_evaluate_delete($id){
        if (!$this->preDeleteCategoryEvaluateDetail){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_category_evaluate_detail.*');
        $this->db->from('tbl_category_evaluate_detail');
        $this->db->where('tbl_category_evaluate_detail.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_category_evaluate_detail');
        if ($success){

            insertActivityLog([
                'type_parent_obj' => 'category_evaluate_detail',
                'table_obj' => 'tbl_category_evaluate_detail',
                'id_obj' => $id,
                'name_obj' => $dtData['name'],
                'content' => lang('Xóa danh mục đánh giá') . ' [' . $dtData['name'] . ']',
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
