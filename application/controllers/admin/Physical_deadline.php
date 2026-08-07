<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Physical_deadline extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('physical_deadline_model');
    }
    function index()
    {
        $data = [];
        $data['title'] = lang('Danh Sách Thời Hạn Khám Sức Khỏe');
        $data['type'] = 1;
        $this->load->view('admin/physical_deadline/manage', $data);
    }
    function getPhysicalDeadline()
    {
        $aColumns = [
            'tbl_physical_deadline.id as id',
            'tblroles.name as location',
            'tbl_physical_deadline.code as code',
            'tbl_physical_deadline.time as time',
            '"" as actions'
        ];
        $type_search = $this->input->post('type_search');

        $sIndexColumn = 'id';
        $sTable       = 'tbl_physical_deadline';
        $where        = [];
        $filter = [];

        $join = [
            'LEFT JOIN tblroles ON tblroles.roleid=tbl_physical_deadline.location',
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);
        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];
            $edit = '<a class="tnh-modal" href="' . base_url('admin/physical_deadline/handling/' . $id) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/physical_deadline/delete/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
        $physical_deadline = $id ? $this->physical_deadline_model->getPhysicalDeadlineById($id) : [];
        if ($this->input->post()) {
            if (!empty($id)) {
                if ($physical_deadline['code'] != $this->input->post('code')) {
                    $this->form_validation->set_rules('code', lang("Mã"), 'trim|required|is_unique[tbl_physical_deadline.code]');
                }
                if ($physical_deadline['location'] != $this->input->post('location')) {
                    $this->form_validation->set_rules('location', lang("Vị trí"), 'trim|required|is_unique[tbl_physical_deadline.location]');
                }
            } else {
                $this->form_validation->set_rules('code', lang("Mã"), 'trim|required|is_unique[tbl_physical_deadline.code]');
                $this->form_validation->set_rules('location', lang("Vị trí"), 'trim|required|is_unique[tbl_physical_deadline.location]');
            }
            $this->form_validation->set_rules('time', lang("Thời Gian Xét Tăng Lương"), 'required');
            if ($this->form_validation->run() == true) {
                $code = ($this->input->post('code'));
                $time = number_unformat($this->input->post('time'));
                $location = ($this->input->post('location'));
                $option = [
                    'code' => $code,
                    'time' => $time,
                    'location' => $location,
                ];
                if ($id) {
                    $option['staff_update'] = get_staff_user_id();
                    $option['date_update'] = date('Y-m-d H:i:s');
                    $ins = $this->physical_deadline_model->updatePhysicalDeadline($id, $option);
                    $standard_id = $id;
                } else {
                    $option['staff_create'] = get_staff_user_id();
                    $option['date_create'] = date('Y-m-d H:i:s');
                    $ins = $this->physical_deadline_model->insertPhysicalDeadline($option);
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
        $data['dtData'] = $physical_deadline;
        $title = '';
        $title = $id ? lang('Sửa Danh Sách Thời Hạn Khám Sức Khỏe') : lang('Thêm Danh Sách Thời Hạn Khám Sức Khỏe');
        $data['title'] = $title;
        $this->load->view('admin/physical_deadline/handling', $data);
    }
    public function delete($id)
    {
        $data = [];
        if ($this->physical_deadline_model->deletePhysicalDeadline($id)) {
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
        $title = lang('Import Danh Sách Thời Hạn Khám Sức Khỏe');
        $data['title'] = $title;
        $data['type'] = $type;
        $this->load->view('admin/physical_deadline/import_excel', $data);
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
                1 => 'time',
                2 => 'location',
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
                $time = '';
                $location = '';
                foreach ($value as $k => $v) {
                    if ($k == 'code') {
                        if (empty($v)) {
                            $errors .= '<div>Dòng [' . ($dem) . '] Mã không được để trống</div>';
                            $checkerrors = 1;
                        } else {
                            $code = $v;
                        }
                    }
                    if ($k == 'time') {
                        if (empty($v)) {
                            $errors .= '<div>Dòng [' . ($dem) . '] Thời gian không được để trống</div>';
                            $checkerrors = 1;
                        } else {
                            if (!is_numeric($v)) {
                                $errors .= '<div>Dòng [' . ($dem) . '] Thời gian phải là số</div>';
                                $checkerrors = 1;
                            } else {
                                $time = $v;
                            }
                        }
                    }
                    if ($k == 'location') {
                        if (empty($v)) {
                            $errors .= '<div>Dòng [' . ($dem) . '] Vị trí không được để trống</div>';
                            $checkerrors = 1;
                        } else {
                            $role = get_table_where('tblroles', ['code_role' => $v], '', 'row_array');
                            if (empty($v)) {
                                $errors .= '<div>Dòng [' . ($dem) . '] Vị trí không tồn tại</div>';
                                $checkerrors = 1;
                            } else {
                                $location = $role['roleid'];
                            }
                        }
                    }
                }
                if ($checkerrors == 0) {
                    $this->db->where('code', $code);
                    $check_id = $this->db->get('tbl_physical_deadline')->row_array();
                    $id = '';
                    if (!empty($check_id)) {
                        $id = $check_id['id'];
                    }
                    if (!empty($id)) {
                        $this->db->where('id != ', $id);
                    }
                    $this->db->where('location', $location);
                    $check_location = $this->db->get('tbl_physical_deadline')->row_array();
                    if (!empty($check_location)) {
                        $errors .= '<div>Dòng [' . ($dem) . '] Vị trí đã được sủ dụng</div>';
                        $checkerrors = 1;
                    }
                }
                if ($checkerrors == 0) {
                    $this->db->where('code', $code);
                    $check_id = $this->db->get('tbl_physical_deadline')->row_array();
                    $id = '';
                    if (!empty($check_id)) {
                        $id = $check_id['id'];
                    }

                    $option = [
                        'code' => $code,
                        'time' => $time,
                        'location' => $location,
                    ];
                    if ($id) {
                        $option['staff_update'] = get_staff_user_id();
                        $option['date_update'] = date('Y-m-d H:i:s');
                        $ins = $this->physical_deadline_model->updatePhysicalDeadline($id, $option);
                    } else {
                        $option['staff_create'] = get_staff_user_id();
                        $option['date_create'] = date('Y-m-d H:i:s');
                        $ins = $this->physical_deadline_model->insertPhysicalDeadline($option);
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
