<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rules_group extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('rules_group_model');
    }
    function index()
    {
        $data = [];
        $data['title'] = lang('Danh Sách Nhóm Nội Quy');
        $data['type'] = 1;
        $this->load->view('admin/rules_group/manage', $data);
    }
    // function rules_group()
    // {
    //     $data = [];
    //     $data['title'] = lang('Danh Sách Nhóm Nội Quy');
    //     $data['type'] = 1;
    //     $this->load->view('admin/rules_group/manage', $data);
    // }
    function getRulesGroup()
    {
        $aColumns = [
            'tbl_category_regulations.id as id',
            'tbl_category_regulations.code as code_group',
            'tbl_category_regulations.name as name_group',
            '"" as actions'
        ];
        $type_search = $this->input->post('type_search');

        $sIndexColumn = 'id';
        $sTable       = 'tbl_category_regulations';
        $where        = [];
        $filter = [];

        $join = [];
        array_push($where, " AND tbl_category_regulations.type = '" . $type_search . "'");

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);
        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];
            $edit = '<a class="tnh-modal" href="' . base_url('admin/rules_group/handling/' . $id) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/rules_group/delete/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
        $rules_group = $id ? $this->rules_group_model->getRules_groupById($id) : [];
        if ($this->input->post()) {
            if (!empty($id)) {
                if ($rules_group['code'] != $this->input->post('code')) {
                    // $this->form_validation->set_rules('code', lang("Mã nhóm nội quy"), 'trim|required|is_unique[tbl_category_regulations.reference_no]');
                    $this->db->where('code', $this->input->post('code'));
                    $this->db->where('type', $type);
                    $check_id = $this->db->get('tbl_category_regulations')->row_array();
                    if (!empty($check_id)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Mã trùng vui lòng kiểm tra lại');
                        echo json_encode($data);
                    }
                }
            } else {
                // $this->form_validation->set_rules('code', lang("Mã nhóm nội quy"), 'trim|required|is_unique[tbl_category_regulations.code]');
                $this->db->where('code', $this->input->post('code'));
                $this->db->where('type', $type);
                $check_id = $this->db->get('tbl_category_regulations')->row_array();
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
                    'type' => $type,
                ];
                if ($id) {
                    $ins = $this->rules_group_model->updateRules_group($id, $option);
                    $standard_id = $id;
                } else {
                    $option['create_by'] = get_staff_user_id();
                    $option['date_create'] = date('Y-m-d H:i:s');
                    $ins = $this->rules_group_model->insertRules_group($option);
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
        $data['dtData'] = $rules_group;
        $title = '';
        $title = $id ? lang('Sửa Danh Sách Nhóm Nội Quy') : lang('Thêm Danh Sách Nhóm Nội Quy');
        $data['title'] = $title;
        $this->load->view('admin/rules_group/handling', $data);
    }
    public function delete($id)
    {
        $data = [];
        if ($this->rules_group_model->deleteRules_group($id)) {
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
        $title = lang('Import Danh Sách Nhóm Nội Quy');
        $data['title'] = $title;
        $data['type'] = $type;
        $this->load->view('admin/rules_group/import_excel', $data);
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
                    if ($k == 'categories') {
                        $categories = $v;
                    }
                }
                if ($checkerrors == 0) {
                    $this->db->where('code', $code);
                    $this->db->where('type', $type);
                    $check_id = $this->db->get('tbl_category_regulations')->row_array();
                    $id = '';
                    if (!empty($check_id)) {
                        $id = $check_id['id'];
                    }
                    $option = [
                        'code' => $code,
                        'type' => $type,
                        'name' => $name,
                    ];
                    if ($id) {
                        $ins = $this->rules_group_model->updateRules_group($id, $option);
                    } else {
                        $option['create_by'] = get_staff_user_id();
                        $option['date_create'] = date('Y-m-d H:i:s');
                        $ins = $this->rules_group_model->insertRules_group($option);
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
