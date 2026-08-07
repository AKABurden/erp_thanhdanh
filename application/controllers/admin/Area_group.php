<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Area_group extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('area_group_model');
    }
    function index()
    {
        $data = [];
        $data['title'] = lang('Danh Sách Nhóm Khu Vực');
        $data['type'] = 1;
        $this->load->view('admin/area_group/manage', $data);
    }
    function getAreaGroup()
    {
        $aColumns = [
            'tbl_area_group.id as id',
            'tbl_area_group.code as code',
            'tbl_area_group.name as name',
            '"" as actions'
        ];
        $type_search = $this->input->post('type_search');

        $sIndexColumn = 'id';
        $sTable       = 'tbl_area_group';
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
            $edit = '<a class="tnh-modal" href="' . base_url('admin/area_group/handling/' . $id) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/area_group/delete/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
        $area_group = $id ? $this->area_group_model->getAreaGroupById($id) : [];
        if ($this->input->post()) {
            if (!empty($id)) {
                if ($area_group['code'] != $this->input->post('code')) {
                    $this->form_validation->set_rules('code', lang("Mã quy định"), 'trim|required|is_unique[tbl_area_group.code]');
                }
            } else {
                $this->form_validation->set_rules('code', lang("Mã quy định"), 'trim|required|is_unique[tbl_area_group.code]');
            }
            $this->form_validation->set_rules('name', lang("Tên quy định"), 'required');
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
                    $ins = $this->area_group_model->updateAreaGroup($id, $option);
                    $standard_id = $id;
                } else {
                    $option['staff_create'] = get_staff_user_id();
                    $option['date_create'] = date('Y-m-d H:i:s');
                    $ins = $this->area_group_model->insertAreaGroup($option);
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
        $data['dtData'] = $area_group;
        $title = '';
        $title = $id ? lang('Sửa Danh Sách Nhóm Khu Vực') : lang('Thêm Danh Sách Nhóm Khu Vực');
        $data['title'] = $title;
        $this->load->view('admin/area_group/handling', $data);
    }
    public function delete($id)
    {
        $data = [];
        if ($this->area_group_model->deleteAreaGroup($id)) {
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
        $title = lang('Import Danh Sách Nhóm Khu Vực');
        $data['title'] = $title;
        $data['type'] = $type;
        $this->load->view('admin/area_group/import_excel', $data);
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
                    $check_id = $this->db->get('tbl_area_group')->row_array();
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
                        $ins = $this->area_group_model->updateAreaGroup($id, $option);
                    } else {
                        $option['staff_create'] = get_staff_user_id();
                        $option['date_create'] = date('Y-m-d H:i:s');
                        $ins = $this->area_group_model->insertAreaGroup($option);
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
