<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Department_work_norms extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('department_work_norms_model');
    }
    function index()
    {
        $data = [];
        $data['title'] = lang('Danh Sách Định Mức Công Việc Phòng Ban');
        $data['type'] = 1;
        $this->load->view('admin/department_work_norms/manage', $data);
    }
    function getDepartmentWorkNorms()
    {
        $aColumns = [
            'tbl_department_work_norms.id as id',
            'tblcategory_tasks.code as category_tasks',
            'tbl_department_work_norms.code as code',
            'tbl_department_work_norms.quota as quota',
            '"" as actions'
        ];
        $type_search = $this->input->post('type_search');

        $sIndexColumn = 'id';
        $sTable       = 'tbl_department_work_norms';
        $where        = [];
        $filter = [];

        $join = [
            'LEFT JOIN tblcategory_tasks ON tblcategory_tasks.id=tbl_department_work_norms.code_task',
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tblcategory_tasks.content'], '', []);
        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];
            $edit = '<a class="tnh-modal" href="' . base_url('admin/department_work_norms/handling/' . $id) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/department_work_norms/delete/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
        $department_work_norms = $id ? $this->department_work_norms_model->getDepartmentWorkNormsById($id) : [];
        if ($this->input->post()) {
            if (!empty($id)) {
                if ($department_work_norms['code'] != $this->input->post('code')) {
                    $this->form_validation->set_rules('code', lang("Mã"), 'trim|required|is_unique[tbl_department_work_norms.code]');
                }
                if ($department_work_norms['code_task'] != $this->input->post('code_task')) {
                    $this->form_validation->set_rules('code_task', lang("Mã công việc"), 'trim|required|is_unique[tbl_department_work_norms.code_task]');
                }
            } else {
                $this->form_validation->set_rules('code', lang("Mã"), 'trim|required|is_unique[tbl_department_work_norms.code]');
                $this->form_validation->set_rules('code_task', lang("Mã công việc"), 'trim|required|is_unique[tbl_department_work_norms.code_task]');
            }
            $this->form_validation->set_rules('quota', lang("Định mức"), 'required');
            if ($this->form_validation->run() == true) {
                $code = ($this->input->post('code'));
                $quota = number_unformat($this->input->post('quota'));
                $code_task = ($this->input->post('code_task'));
                $option = [
                    'code' => $code,
                    'quota' => $quota,
                    'code_task' => $code_task,
                ];
                if ($id) {
                    $option['staff_update'] = get_staff_user_id();
                    $option['date_update'] = date('Y-m-d H:i:s');
                    $ins = $this->department_work_norms_model->updateDepartmentWorkNorms($id, $option);
                    $standard_id = $id;
                } else {
                    $option['staff_create'] = get_staff_user_id();
                    $option['date_create'] = date('Y-m-d H:i:s');
                    $ins = $this->department_work_norms_model->insertDepartmentWorkNorms($option);
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
        $data['dtData'] = $department_work_norms;
        $title = '';
        $title = $id ? lang('Sửa Danh Sách Định Mức Công Việc Phòng Ban') : lang('Thêm Danh Sách Định Mức Công Việc Phòng Ban');
        $data['title'] = $title;
        $this->load->view('admin/department_work_norms/handling', $data);
    }
    public function delete($id)
    {
        $data = [];
        if ($this->department_work_norms_model->deleteDepartmentWorkNorms($id)) {
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
        $title = lang('Import Danh Sách Định Mức Công Việc Phòng Ban');
        $data['title'] = $title;
        $data['type'] = $type;
        $this->load->view('admin/department_work_norms/import_excel', $data);
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
                1 => 'quota',
                2 => 'category_tasks',
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
                $quota = '';
                $category_tasks = '';
                foreach ($value as $k => $v) {
                    if ($k == 'code') {
                        if (empty($v)) {
                            $errors .= '<div>Dòng [' . ($dem) . '] Mã không được để trống</div>';
                            $checkerrors = 1;
                        } else {
                            $code = $v;
                        }
                    }
                    if ($k == 'quota') {
                        if (empty($v)) {
                            $errors .= '<div>Dòng [' . ($dem) . '] Định mức không được để trống/div>';
                            $checkerrors = 1;
                        } else {
                            if (!is_numeric($v)) {
                                $errors .= '<div>Dòng [' . ($dem) . '] Định mức phải là số</div>';
                                $checkerrors = 1;
                            } else {
                                $quota = $v;
                            }
                        }
                    }
                    if ($k == 'category_tasks') {
                        if (empty($v)) {
                            $errors .= '<div>Dòng [' . ($dem) . '] Mã công việc không được để trống/div>';
                            $checkerrors = 1;
                        } else {
                            $category_taskss = get_table_where('tblcategory_tasks', ['code' => $v], '', 'row_array');

                            if (empty($category_taskss)) {
                                $errors .= '<div>Dòng [' . ($dem) . '] Mã công việc không tồn tại</div>';
                                $checkerrors = 1;
                            } else {
                                $category_tasks = $category_taskss['id'];
                            }
                        }
                    }
                }
                if ($checkerrors == 0) {
                    $this->db->where('code', $code);
                    $check_id = $this->db->get('tbl_department_work_norms')->row_array();
                    $id = '';
                    if (!empty($check_id)) {
                        $id = $check_id['id'];
                    }
                    if (!empty($id)) {
                        $this->db->where('id != ', $id);
                    }
                    $this->db->where('code_task', $category_tasks);
                    $code_task = $this->db->get('tbl_department_work_norms')->row_array();
                    if (!empty($code_task)) {
                        $errors .= '<div>Dòng [' . ($dem) . '] Mã công việc đã được sủ dụng</div>';
                        $checkerrors = 1;
                    }
                }
                if ($checkerrors == 0) {
                    $this->db->where('code', $code);
                    $check_id = $this->db->get('tbl_department_work_norms')->row_array();
                    $id = '';
                    if (!empty($check_id)) {
                        $id = $check_id['id'];
                    }

                    $option = [
                        'code' => $code,
                        'quota' => $quota,
                        'code_task' => $category_tasks,
                    ];
                    if ($id) {
                        $option['staff_update'] = get_staff_user_id();
                        $option['date_update'] = date('Y-m-d H:i:s');
                        $ins = $this->department_work_norms_model->updateDepartmentWorkNorms($id, $option);
                    } else {
                        $option['staff_create'] = get_staff_user_id();
                        $option['date_create'] = date('Y-m-d H:i:s');
                        $ins = $this->department_work_norms_model->insertDepartmentWorkNorms($option);
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
