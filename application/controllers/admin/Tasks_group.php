<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tasks_group extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function insertTasks_group($data)
    {
        $this->db->insert('tbl_task_group', $data);
        return $this->db->insert_id();
    }

    public function updateTasks_group($id, $data)
    {
        $this->db->where('tbl_task_group.id', $id);
        return $this->db->update('tbl_task_group', $data);
    }

    public function deleteTasks_group($id)
    {
        $this->db->where('tbl_task_group.id', $id);
        return $this->db->delete('tbl_task_group');
    }

    public function getTasks_groupById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_task_group');
        $this->db->where('tbl_task_group.id', $id);
        return $this->db->get()->row_array();
    }
    function index()
    {
        $data = [];
        $data['title'] = lang('Danh Sách Nhóm Công Việc Phòng Ban');
        $data['type'] = 1;
        $this->load->view('admin/tasks_group/manage', $data);
    }
    function getTasks_group()
    {
        $aColumns = [
            'tbl_task_group.id as id',
            'tbl_task_group.code as code_group',
            'tbl_task_group.name as name_group',
            '"" as actions'
        ];
        $type_search = $this->input->post('type_search');

        $sIndexColumn = 'id';
        $sTable       = 'tbl_task_group';
        $where        = [];
        $filter = [];

        $join = [];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);
        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];
            $edit = '<a class="tnh-modal" href="' . base_url('admin/tasks_group/handling/' . $id) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/tasks_group/delete/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';

            $row = [];
            foreach ($aColumns as $k => $v) {
                $_data = $aRow[$v];
                if ($v == 'actions') {
                    $_data = $actions;
                } else if ($v == 'id') {
                    $_data = '<div class="text-center">' . $start . '</div>';
                } else {
                    $_data = '<div class="text-center">' . $_data . '</div>';
                }

                $row[] = $_data;
            }


            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }
    function handling($id = '', $type = 1)
    {

        $data = [];
        $tasks_group = $id ? $this->getTasks_groupById($id) : [];
        if ($this->input->post()) {
            if (!empty($id)) {
                if ($tasks_group['code'] != $this->input->post('code')) {
                    // $this->form_validation->set_rules('code', lang("Mã nhóm nội quy"), 'trim|required|is_unique[tbl_task_group.reference_no]');
                    $this->db->where('code', $this->input->post('code'));
                    $check_id = $this->db->get('tbl_task_group')->row_array();
                    if (!empty($check_id)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Mã trùng vui lòng kiểm tra lại');
                        echo json_encode($data);
                    }
                }
            } else {
                // $this->form_validation->set_rules('code', lang("Mã nhóm nội quy"), 'trim|required|is_unique[tbl_task_group.code]');
                $this->db->where('code', $this->input->post('code'));
                $check_id = $this->db->get('tbl_task_group')->row_array();
                if (!empty($check_id)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Mã trùng vui lòng kiểm tra lại');
                    echo json_encode($data);
                }
            }
            $this->form_validation->set_rules('name', lang("Tên nhóm nội quy"), 'required');
            if ($this->form_validation->run() == true) {
                $code = ($this->input->post('code'));
                $name = ($this->input->post('name'));
                $option = [
                    'code' => $code,
                    'name' => $name,
                ];
                if ($id) {
                    $option['staff_update'] = get_staff_user_id();
                    $option['date_update'] = date('Y-m-d H:i:s');
                    $ins = $this->updateTasks_group($id, $option);
                    $standard_id = $id;
                } else {
                    $option['staff_create'] = get_staff_user_id();
                    $option['date_create'] = date('Y-m-d H:i:s');
                    $ins = $this->insertTasks_group($option);
                    $standard_id = $ins;
                }

                if (!empty($standard_id)) {
                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            return;
        }

        $data['id'] = $id;
        $data['type'] = $type;
        $data['dtData'] = $tasks_group;
        $title = '';
        $title = $id ? lang('Sửa Danh Sách Nhóm Công Việc Phòng Ban') : lang('Thêm Danh Sách Nhóm Công Việc Phòng Ban');
        $data['title'] = $title;
        $this->load->view('admin/tasks_group/handling', $data);
    }
    public function delete($id)
    {
        $data = [];
        if ($this->deleteTasks_group($id)) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }
    function modal_excel($type = 0)
    {
        $title = '';
        $title = lang('Import Danh Sách Nhóm Công Việc Phòng Ban');
        $data['title'] = $title;
        $data['type'] = $type;
        $this->load->view('admin/tasks_group/import_excel', $data);
    }
    function import($type = 0)
    {
        ob_end_clean();
        ini_set('max_execution_time', 800);
        $dataPost = $this->input->post();
        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $this->load->helper('security');
        $data = [];
        if (!empty($_FILES['file'])) {
            $fullfile = $_FILES['file']['tmp_name'];
            $nameFile = $_FILES['file']['name'];
            $extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if ($extension != 'XLSX' && $extension != 'XLS') {
                echo json_encode(['success' => false, 'alert_type' => 'success', 'message' => _l('cong_not_type')]);
                die();
            }
            $inputFileType = PHPExcel_IOFactory::identify($fullfile);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load("$fullfile");
            $total_sheets = $objPHPExcel->getSheetCount();
            $allSheetName = $objPHPExcel->getSheetNames();

            $vaKey = '';
            $process = [];
            $month = [];
            $maintenance = [];
            $note_main = [];
            $listRow = [
                0 => 'code',
                1 => 'name',
            ];
            for ($sheet = 0; $sheet < $total_sheets; $sheet++) {
                $objWorksheet = $objPHPExcel->setActiveSheetIndex($sheet);
                $highestRow = $objWorksheet->getHighestRow();
                $highestColumn = $objWorksheet->getHighestColumn();
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                $vaKey = '';
                for ($i = 2; $i <= $highestRow; $i++) {
                    $redata = [];
                    for ($j = 0; $j < $highestColumnIndex; $j++) {
                        $Val = $objWorksheet->getCellByColumnAndRow($j, $i)->getValue();
                        $redata[$listRow[$j]] = trim($Val);
                    }
                    $data[] = $redata;
                }
            }
        }
        $count = 0;
        $errors = '';
        $dem = 0;
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $dem++;
                $checkerrors = 0;
                $code = '';
                $name = '';
                $categories = '';
                foreach ($value as $k => $v) {
                    if ($k == 'code') {
                        if (empty($v)) {
                            $errors .= '<div>Dòng [' . ($dem) . '] Mã không được để trống</div>';
                            $checkerrors = 1;
                        } else {
                            $code = $v;
                        }
                    }
                    if ($k == 'name') {
                        if (empty($v)) {
                            $errors .= '<div>Dòng [' . ($dem) . '] Tên không được để trống</div>';
                            $checkerrors = 1;
                        } else {
                            $name = $v;
                        }
                    }
                }
                if ($checkerrors == 0) {
                    $this->db->where('code', $code);
                    $check_id = $this->db->get('tbl_task_group')->row_array();
                    $id = '';
                    if (!empty($check_id)) {
                        $id = $check_id['id'];
                    }
                    $option = [
                        'code' => $code,
                        'name' => $name,
                    ];
                    if ($id) {
                        $option['staff_update'] = get_staff_user_id();
                        $option['date_update'] = date('Y-m-d H:i:s');
                        $ins = $this->updateTasks_group($id, $option);
                    } else {
                        $option['staff_create'] = get_staff_user_id();
                        $option['date_create'] = date('Y-m-d H:i:s');
                        $ins = $this->insertTasks_group($option);
                    }
                    if (!empty($ins)) {
                        $count++;
                    }
                }
            }
        }
        echo json_encode(
            [
                'success' => true,
                'alert_type' => 'success',
                'errors' => $errors,
                'message' => 'Import thành công ' . $count . ' Items',
            ]
        );
        die();
    }
}
