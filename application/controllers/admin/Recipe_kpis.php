<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Recipe_kpis extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function insertRecipe_kpis($data)
    {
        $this->db->insert('tbl_recipe_kpis', $data);
        return $this->db->insert_id();
    }

    public function updateRecipe_kpis($id, $data)
    {
        $this->db->where('tbl_recipe_kpis.id', $id);
        return $this->db->update('tbl_recipe_kpis', $data);
    }

    public function deleteRecipe_kpis($id)
    {
        $this->db->where('tbl_recipe_kpis.id', $id);
        return $this->db->delete('tbl_recipe_kpis');
    }

    public function getRecipe_kpisById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_recipe_kpis');
        $this->db->where('tbl_recipe_kpis.id', $id);
        return $this->db->get()->row_array();
    }
    function index()
    {
        $data = [];
        $data['title'] = lang('Danh Sách Công Thức Quy Đổi KPIs');
        $data['type'] = 1;
        $this->load->view('admin/recipe_kpis/manage', $data);
    }
    function getRecipe_kpis()
    {
        $aColumns = [
            'tbl_recipe_kpis.id as id',
            'tbl_category_kpi.code as code_group',
            'tbl_category_kpi.name as name_group',
            'tbl_category_kpi_criteria.name as kpis',
            'tbl_recipe_kpis.target as target',
            'tbl_recipe_kpis.weight as weight',
            'tbl_recipe_kpis.conversion_formula as conversion_formula',
            'tbl_recipe_kpis.point as point',
            '"" as actions'
        ];
        $type_search = $this->input->post('type_search');

        $sIndexColumn = 'id';
        $sTable       = 'tbl_recipe_kpis';
        $where        = [];
        $filter = [];

        $join = [
            'LEFT JOIN tbl_category_kpi ON tbl_category_kpi.id = tbl_recipe_kpis.category_kpi',
            'LEFT JOIN tbl_category_kpi_criteria ON tbl_category_kpi_criteria.id = tbl_recipe_kpis.kpis',

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
            $edit = '<a class="tnh-modal" href="' . base_url('admin/recipe_kpis/handling/' . $id) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/recipe_kpis/delete/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
    function handling($id = '')
    {

        $data = [];
        $recipe_kpis = $id ? $this->getRecipe_kpisById($id) : [];
        if ($this->input->post()) {
            $this->form_validation->set_rules('kpis', lang("KPIs"), 'required');
            if ($this->form_validation->run() == true) {
                $category_kpi = _string($this->input->post('category_kpi'));
                $kpis = ($this->input->post('kpis'));
                $conversion_formula = ($this->input->post('conversion_formula'));
                $weight = number_unformat($this->input->post('weight'));
                $target = number_unformat($this->input->post('target'));
                $point = number_unformat($this->input->post('point'));

                $option = [
                    'kpis' => $kpis,
                    'category_kpi' => $category_kpi,
                    'conversion_formula' => $conversion_formula,
                    'target' => $target,
                    'weight' => $weight,
                    'point' => $point
                ];
                if ($id) {
                    $option['staff_update'] = get_staff_user_id();
                    $option['date_update'] = date('Y-m-d H:i:s');
                    $ins = $this->updateRecipe_kpis($id, $option);
                    $standard_id = $id;
                } else {
                    $option['staff_create'] = get_staff_user_id();
                    $option['date_create'] = date('Y-m-d H:i:s');
                    $ins = $this->insertRecipe_kpis($option);
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
        $data['type'] = 0;
        $data['dtData'] = $recipe_kpis;
        $data['dtCategoryKpis'] = $this->db->get('tbl_category_kpi')->result_array();

        $title = '';
        $title = $id ? lang('Sửa Danh Sách Công Thức Quy Đổi KPIs') : lang('Thêm Danh Sách Công Thức Quy Đổi KPIs');
        $data['title'] = $title;
        $this->load->view('admin/recipe_kpis/handling', $data);
    }
    public function delete($id)
    {
        $data = [];
        if ($this->deleteRecipe_kpis($id)) {
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
        $title = lang('Import Danh Sách Công Thức Quy Đổi KPIs');
        $data['title'] = $title;
        $data['type'] = $type;
        $this->load->view('admin/recipe_kpis/import_excel', $data);
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
                0 => 'category_kpi',
                1 => 'kpis',
                2 => 'target',
                3 => 'weight',
                4 => 'conversion_formula',
                5 => 'point',
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
                $category_kpi = 0;
                $kpis = 0;
                $target = 0;
                $weight = 0;
                $conversion_formula = '';
                $point = 0;
                foreach ($value as $k => $v) {

                    if ($k == 'conversion_formula') {
                        $conversion_formula = $v;
                    }
                    if ($k == 'target') {
                        $target = number_unformat($v);
                    }
                    if ($k == 'weight') {
                        $weight = number_unformat($v);
                    }
                    if ($k == 'point') {
                        $point = number_unformat($v);
                    }
                    if ($k == 'category_kpi') {
                        $ktr_category_kpi = get_table_where('tbl_category_kpi', ['code' => $v], '', 'row_array');
                        if (empty($ktr_category_kpi)) {
                            $errors .= '<div>Dòng [' . ($dem) . '] Không tìm thấy nhóm KPIs</div>';
                            $checkerrors = 1;
                        } else {
                            $category_kpi = $ktr_category_kpi['id'];
                        }
                    }
                    if ($k == 'kpis') {
                        $ktr_kpis = get_table_where('tbl_category_kpi_criteria', ['code' => $v, 'category_kpi_id' => $category_kpi], '', 'row_array');
                        if (empty($ktr_kpis)) {
                            $errors .= '<div>Dòng [' . ($dem) . '] Không tìm thấy KPIs</div>';
                            $checkerrors = 1;
                        } else {
                            $kpis = $ktr_kpis['id'];
                        }
                    }
                }
                if ($checkerrors == 0) {
                    $this->db->where('kpis', $kpis);
                    $this->db->where('category_kpi', $category_kpi);
                    $check_id = $this->db->get('tbl_recipe_kpis')->row_array();
                    $id = '';
                    if (!empty($check_id)) {
                        $id = $check_id['id'];
                    }
                    $option = [
                        'category_kpi' => $category_kpi,
                        'kpis' => $kpis,
                        'target' => $target,
                        'conversion_formula' => $conversion_formula,
                        'weight' => $weight,
                        'point' => $point,
                    ];
                    if ($id) {
                        $option['staff_update'] = get_staff_user_id();
                        $option['date_update'] = date('Y-m-d H:i:s');
                        $ins = $this->updateRecipe_kpis($id, $option);
                    } else {
                        $option['staff_create'] = get_staff_user_id();
                        $option['date_create'] = date('Y-m-d H:i:s');
                        $ins = $this->insertRecipe_kpis($option);
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
    public function searchKpis($id = 0)
    {
        $term = $this->input->get('term');
        $category_machine_id = $this->input->get('params');
        if ($category_machine_id == 0) {
            $category_machine_id = -1;
        }
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_category_kpi_criteria.id as id, 
            tbl_category_kpi_criteria.name as text,
            tbl_category_kpi_criteria.code as code,
            tbl_category_kpi_criteria.name as name,
        ', false);
        $this->db->from('tbl_category_kpi_criteria');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->or_like('tbl_category_kpi_criteria.name', $term);
            $this->db->like('tbl_category_kpi_criteria.code', $term);
            $this->db->group_end();
        }
        $this->db->where('tbl_category_kpi_criteria.category_kpi_id', $category_machine_id);
        $this->db->limit($limit);
        $pod = $this->db->get()->result_array();
        $data['results'][] = ['text' => lang('KPIs'), 'children' => $pod];
        if (!empty($id)) {
            $dtMachines = get_table_where('tbl_category_kpi_criteria', ['id' => $id], '', 'row_array');
            $data['row'] = ['id' => $dtMachines['id'], 'text' => $dtMachines['name']];
        }
        echo json_encode($data);
    }
}
