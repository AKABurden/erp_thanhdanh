<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Kpi extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->type_check = 'dashboard';
        if ($this->input->get('type')) {
            $this->type_check = $this->input->get('type');
        }

        $this->perViewCategoryKpi = true;
        $this->perAddCategoryKpi = true;
        $this->perDeleteCategoryKpi = true;

        $this->perViewDetailTask = true;
        $this->perAddDetailTask = true;
        $this->perDeleteDetailTask = true;

        $this->perViewSuggestKpi = true;
        $this->perAddSuggestKpi = true;
        $this->perEditSuggestKpi = true;
        $this->perApproveSuggestKpi = true;
        $this->perDeleteSuggestKpi = true;

        $this->perViewProposeKpi = true;
        $this->perAddProposeKpi = true;
        $this->perEditProposeKpi = true;
        $this->perApproveProposeKpi = true;
        $this->perDeleteProposeKpi = true;

        $this->perViewListCriteriaDepartment = true;
        $this->perAddListCriteriaDepartment = true;

        $this->type = [
            [
                'id' => 1,
                'name' => lang('Thưởng'),
            ],
            [
                'id' => 2,
                'name' => lang('Kỷ luật'),
            ]
        ];
    }

    public function category_kpi()
    {
        $data = [];
        if (!$this->perViewCategoryKpi) {
            access_denied('category_kpi');
        }
        $data['title'] = _l('dt_category_kpi');
        $this->load->view('admin/kpi/category_kpi', $data);
    }

    public function getCategoryKpi()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_category_kpi.id as id',
            'tbl_category_kpi.code as code',
            'tbl_category_kpi.name as name',
            'tbl_category_kpi.created_by as created_by'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_category_kpi';
        $where = [];
        $filter = [];

        $join = [];


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left"><a target="_blank" href="' . base_url('admin/kpi/view_category_kpi/' . $aRow['id']) . '">' . $aRow['code'] . '</a></div>';
            $row[] = '<div class="text-left">' . $aRow['name'] . '</div>';
            $row[] = '<div class="text-left">' . get_staff_full_name($aRow['created_by']) . '</div>';

            $view = '<a target="_blank" href="' . base_url('admin/kpi/view_category_kpi/' . $aRow['id']) . '"><i class="fa fa-eye"></i> ' . lang('xem') . '</a>';

            $delete = $this->perDeleteCategoryKpi ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/kpi/delete_category_kpi/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>' : '';
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $view . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[] = '<div>' . $actions . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function import_category_kpi()
    {
        $data = [];
        if (!empty($_FILES)) {
            ini_set('max_execution_time', 800);
            require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
            $this->load->helper('security');
            $count = 0;
            $errors = '';
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
                // $objReader->setReadDataOnly(true);
                $objPHPExcel = $objReader->load("$fullfile");

                $total_sheets = $objPHPExcel->getSheetCount();

                $allSheetName = $objPHPExcel->getSheetNames();
                $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
                $highestRow = $objWorksheet->getHighestRow();
                $highestColumn = $objWorksheet->getHighestColumn();
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('I');
                $arraydata = array();

                $fields = $this->input->post('fields');
                for ($row = 2; $row <= $highestRow; ++$row) {
                    for ($col = 0; $col < $highestColumnIndex; ++$col) {
                        $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                        $arraydata[$row - 2][$col] = $value;
                    }
                }
                $dataArray = [];
                $index_parent = 0;
                $reference = '';
                $i = 2;
                foreach ($arraydata as $key => $value) {

                    if (empty($value[0])) {
                        $errors .= '<div>Dòng [' . $i . '] Không tìm thấy mã nhóm KPI</div>';
                        continue;
                    }
                    $code_category_kpi = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[0])), 'UTF-8');

                    if (empty($value[1])) {
                        $errors .= '<div>Dòng [' . $i . '] Không tìm thấy tên nhóm KPI</div>';
                        continue;
                    }
                    $name_category_kpi = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[1])), 'UTF-8');
                    $name = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[2])), 'UTF-8');

                    if (empty($value[3])) {
                        $errors .= '<div>Dòng [' . $i . '] Không tìm thấy tiêu chí</div>';
                        continue;
                    }
                    $type = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[3])), 'UTF-8');

                    if (empty($value[4])) {
                        $errors .= '<div>Dòng [' . $i . '] Không tìm thấy mã KPI</div>';
                        continue;
                    }
                    $i++;
                    $code = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[4])), 'UTF-8');
                    $measure = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[5])), 'UTF-8');
                    $time = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[6])), 'UTF-8');
                    $weight = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[7])), 'UTF-8');
                    $reporting_cycle = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[8])), 'UTF-8');

                    if (!empty($code_category_kpi) && $code_category_kpi != $reference) {
                        $dataArray[$index_parent]['code_category_kpi'] = $code_category_kpi;
                        $dataArray[$index_parent]['name_category_kpi'] = $name_category_kpi;

                        $parent_current = $index_parent;
                        $reference = $code_category_kpi;
                        $index_parent++;
                    }
                    $dataArray[$parent_current]['items'][] = [
                        'code_category_kpi' => $code_category_kpi,
                        'name_category_kpi' => $name_category_kpi,
                        'name' => $name,
                        'type' => $type,
                        'code' => $code,
                        'measure' => $measure,
                        'time' => $time,
                        'weight' => $weight,
                        'reporting_cycle' => $reporting_cycle,
                    ];
                }
                $count = 0;
                if (!empty($dataArray)) {
                    foreach ($dataArray as $key => $value) {
                        $code_category_kpi = $value['code_category_kpi'];
                        $name_category_kpi = $value['name_category_kpi'];

                        $this->db->from('tbl_category_kpi');
                        $this->db->where('tbl_category_kpi.code', $code_category_kpi);
                        $dtCategoryKpi = $this->db->get()->row_array();
                        if (!empty($dtCategoryKpi)) {
                            $category_kpi_id = $dtCategoryKpi['id'];
                            $this->db->where('id', $code_category_kpi);
                            $this->db->update('tbl_category_kpi', [
                                'name' => $name_category_kpi
                            ]);
                            $count++;
                        } else {
                            $this->db->insert('tbl_category_kpi', [
                                'code' => $code_category_kpi,
                                'name' => $name_category_kpi,
                                'created_by' => get_staff_user_id(),
                                'date_created' => date('Y-m-d H:i:s'),
                            ]);
                            $category_kpi_id = $this->db->insert_id();
                            $count++;
                        }
                        $items = $value['items'];
                        if (!empty($items)) {
                            foreach ($items as $kk => $vv) {
                                $name = $vv['name'];
                                $type = $vv['type'];
                                $code = $vv['code'];
                                $measure = $vv['measure'];
                                $time = $vv['time'];
                                $weight = $vv['weight'];
                                $reporting_cycle = $vv['reporting_cycle'];

                                $this->db->from('tbl_category_kpi_criteria');
                                $this->db->where('tbl_category_kpi_criteria.category_kpi_id', $category_kpi_id);
                                $this->db->where('tbl_category_kpi_criteria.code', $code);
                                $dtCategoryKpiCri = $this->db->get()->row_array();
                                if (!empty($dtCategoryKpiCri)) {
                                    $this->db->where('tbl_category_kpi_criteria.id', $dtCategoryKpiCri['id']);
                                    $this->db->update('tbl_category_kpi_criteria', [
                                        'name' => $name,
                                        'type' => $type,
                                        'measure' => $measure,
                                        'time' => $time,
                                        'weight' => $weight,
                                        'reporting_cycle' => $reporting_cycle,
                                    ]);
                                } else {
                                    $this->db->insert('tbl_category_kpi_criteria', [
                                        'category_kpi_id' => $category_kpi_id,
                                        'code' => $code,
                                        'name' => $name,
                                        'type' => $type,
                                        'measure' => $measure,
                                        'time' => $time,
                                        'weight' => $weight,
                                        'reporting_cycle' => $reporting_cycle,
                                    ]);
                                }
                            }
                        }
                    }
                }
                echo json_encode(
                    [
                        'success' => true,
                        'errors' => $errors,
                        'alert_type' => 'success',
                        'message' => 'Thêm mới và cập nhật thành công ' . $count . ' nhóm KPI',
                    ]
                );
                die();
            }
            echo json_encode([
                'success' => true,
                'errors' => $errors,
                'alert_type' => 'success',
                'message' => 'Import thành công ' . $count . ' dòng',
            ]);
            die();
        }
        $data['title'] = _l('Import Nhóm KPI');
        $this->load->view('admin/kpi/import_category_kpi', $data);
    }

    public function view_category_kpi($id)
    {
        $data = [];
        $data['title'] = lang('dt_view_category_kpi');
        $this->db->from('tbl_category_kpi');
        $this->db->where('tbl_category_kpi.id', $id);
        $dtCategoryKpi = $this->db->get()->row_array();

        $this->db->from('tbl_category_kpi_criteria');
        $this->db->where('tbl_category_kpi_criteria.category_kpi_id', $id);
        $this->db->where('tbl_category_kpi_criteria.type', 1);
        $dtCategoryKpiCriNL = $this->db->get()->result_array();

        $this->db->from('tbl_category_kpi_criteria');
        $this->db->where('tbl_category_kpi_criteria.category_kpi_id', $id);
        $this->db->where('tbl_category_kpi_criteria.type', 2);
        $dtCategoryKpiCriTT = $this->db->get()->result_array();

        $data['dtCategoryKpi'] = $dtCategoryKpi;
        $data['dtCategoryKpiCriNL'] = $dtCategoryKpiCriNL;
        $data['dtCategoryKpiCriTT'] = $dtCategoryKpiCriTT;
        $data['breadcrumb'] = [array('link' => base_url('admin/kpi/category_kpi'), 'page' => lang('dt_category_kpi')), array('link' => '#', 'page' => lang('dt_view_category_kpi'))];
        $this->load->view('admin/kpi/view_category_kpi', $data);
    }

    public function delete_category_kpi($id)
    {
        if (!$this->perDeleteCategoryKpi) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }

        $this->db->from('tbl_category_kpi');
        $this->db->where('tbl_category_kpi.id', $id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);
            die();
        }

        $this->db->where('id', $id);
        $success = $this->db->delete('tbl_category_kpi');
        if ($success) {
            $this->db->where('tbl_category_kpi_criteria.category_kpi_id', $id);
            $this->db->delete('tbl_category_kpi_criteria');
            insertActivityLog([
                'type_parent_obj' => 'category_kpi',
                'table_obj' => 'tbl_category_kpi',
                'id_obj' => $id,
                'name_obj' => $dtData['code'],
                'content' => lang('Xóa nhóm KPIs') . ' [' . $dtData['code'] . ']',
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

    public function detail_task()
    {
        $data = [];
        if (!$this->perViewDetailTask) {
            access_denied('detail_task');
        }
        $data['title'] = _l('dt_detail_task');
        $this->load->view('admin/kpi/detail_task', $data);
    }

    public function getDetailTask()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_detail_task.id as id',
            'tbl_detail_task.code as code',
            'tbl_detail_task.name as name',
            'tblroles.code_role as code_role',
            'tblroles.name as name_role',
            'tblroles.name_position as name_position'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_detail_task';
        $where = [];
        $filter = [];

        $join = [
            'INNER JOIN tblroles ON tblroles.roleid = tbl_detail_task.role_id'
        ];


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left"><a target="_blank" href="' . base_url('admin/kpi/view_detail_task/' . $aRow['id']) . '">' . $aRow['code'] . '</a></div>';
            $row[] = '<div class="text-left">' . $aRow['name'] . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['code_role']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['name_role']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['name_position']) . '</div>';

            $view = '<a target="_blank" href="' . base_url('admin/kpi/view_detail_task/' . $aRow['id']) . '"><i class="fa fa-eye"></i> ' . lang('Xem') . '</a>';

            $delete = $this->perDeleteDetailTask ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/kpi/delete_detail_task/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>' : '';
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $view . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[] = '<div>' . $actions . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function import_detail_task()
    {
        $data = [];
        if (!empty($_FILES)) {
            ini_set('max_execution_time', 800);
            require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
            $this->load->helper('security');
            $count = 0;
            $errors = '';
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
                // $objReader->setReadDataOnly(true);
                $objPHPExcel = $objReader->load("$fullfile");

                $total_sheets = $objPHPExcel->getSheetCount();

                $allSheetName = $objPHPExcel->getSheetNames();
                $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
                $highestRow = $objWorksheet->getHighestRow();
                $highestColumn = $objWorksheet->getHighestColumn();
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('H');
                $arraydata = array();

                $fields = $this->input->post('fields');
                for ($row = 2; $row <= $highestRow; ++$row) {
                    for ($col = 0; $col < $highestColumnIndex; ++$col) {
                        $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                        $arraydata[$row - 2][$col] = $value;
                    }
                }
                $dataArray = [];
                $index_parent = 0;
                $index_parent_items = 0;
                $reference = '';
                $i = 2;
                foreach ($arraydata as $key => $value) {

                    if (empty($value[0])) {
                        $errors .= '<div>Dòng [' . $i . '] Không tìm thấy Mã mô tả công việc theo vị trí</div>';
                        continue;
                    }
                    $code = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[0])), 'UTF-8');

                    if (empty($value[1])) {
                        $errors .= '<div>Dòng [' . $i . '] Không tìm thấy tên mô tả công việc theo vị trí</div>';
                        continue;
                    }
                    $name = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[1])), 'UTF-8');

                    if (empty($value[2])) {
                        $errors .= '<div>Dòng [' . $i . '] Không tìm thấy mã vị trí</div>';
                        continue;
                    }
                    $code_role = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[2])), 'UTF-8');

                    if (empty($value[3])) {
                        $errors .= '<div>Dòng [' . $i . '] Không tìm thấy danh mục</div>';
                        continue;
                    }
                    $category = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[3])), 'UTF-8');
                    $i++;
                    $note = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[4])), 'UTF-8');
                    $code_dg = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[5])), 'UTF-8');
                    $code_kpi = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[6])), 'UTF-8');
                    $regulations = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[7])), 'UTF-8');

                    if (!empty($code) && $code != $reference) {
                        $dataArray[$index_parent]['code'] = $code;
                        $dataArray[$index_parent]['name'] = $name;
                        $dataArray[$index_parent]['code_role'] = $code_role;

                        $parent_current = $index_parent;
                        $reference = $code;
                        $index_parent++;
                        $refItem = '';
                    }

                    $dataArray[$parent_current]['items'][] = [
                        'code' => $code,
                        'name' => $name,
                        'code_role' => $code_role,
                        'category' => $category,
                        'note' => $note,
                        'code_dg' => $code_dg,
                        'code_kpi' => $code_kpi,
                        'regulations' => $regulations,
                    ];
                    //                    if (!empty($category) && $category != $refItem) {
                    //                        $dataArray[$parent_current]['items'][$index_parent_items] = [
                    //                            'code' => $code,
                    //                            'name' => $name,
                    //                            'code_role' => $code_role,
                    //                            'category' => $category,
                    //                            'note' => $note,
                    //                            'code_kpi' => $code_kpi
                    //                        ];
                    //
                    //                        $parent_current_item = $index_parent_items;
                    //                        $refItem = $category;
                    //                        $index_parent_items++;
                    //                    }
                    //
                    //                    $dataArray[$parent_current]['items'][$parent_current_item]['detail'][] = [
                    //                        'code' => $code,
                    //                        'name' => $name,
                    //                        'code_role' => $code_role,
                    //                        'category' => $category,
                    //                        'note' => $note,
                    //                        'code_kpi' => $code_kpi
                    //                    ];

                }
                $count = 0;

                if (!empty($dataArray)) {
                    foreach ($dataArray as $key => $value) {
                        $code = $value['code'];
                        $name = $value['name'];
                        $code_role = $value['code_role'];

                        $this->db->from('tblroles');
                        $this->db->where('tblroles.code_role', $code_role);
                        $dtRole = $this->db->get()->row_array();
                        if (empty($dtRole)) {
                            $errors .= '<div>Mã vị trí [' . $code_role . '] Không tồn tại trên phần mềm!</div>';
                            continue;
                        }

                        $role_id = $dtRole['roleid'];
                        $kpi_category_id = $dtRole['kpi_category_id'];

                        $this->db->from('tbl_category_kpi');
                        $this->db->where('tbl_category_kpi.id', $kpi_category_id);
                        $dtCategoryKpi = $this->db->get()->row_array();

                        if (empty($dtCategoryKpi)) {
                            $errors .= '<div>Mã vị trí [' . $code_role . '] chưa có nhóm KPIS!</div>';
                            continue;
                        }

                        $this->db->from('tbl_detail_task');
                        $this->db->where('tbl_detail_task.code', $code);
                        $dtDetailTask = $this->db->get()->row_array();
                        if (!empty($dtDetailTask)) {
                            $detail_task_id = $dtDetailTask['id'];
                            $this->db->where('id', $detail_task_id);
                            $this->db->update('tbl_detail_task', [
                                'name' => $name,
                                'role_id' => $role_id,
                            ]);
                            $count++;
                        } else {
                            $this->db->insert('tbl_detail_task', [
                                'code' => $code,
                                'name' => $name,
                                'role_id' => $role_id,
                                'created_by' => get_staff_user_id(),
                                'date_created' => date('Y-m-d H:i:s'),
                            ]);
                            $detail_task_id = $this->db->insert_id();
                            $count++;
                        }
                        $items = $value['items'];
                        if (!empty($items)) {
                            foreach ($items as $kk => $vv) {
                                $category = $vv['category'];
                                $note = $vv['note'];
                                $code_dg = $vv['code_dg'];
                                $code_kpi = $vv['code_kpi'];
                                $regulations = $vv['regulations'];

                                $this->db->from('tbl_category_detail_task');
                                $this->db->where('tbl_category_detail_task.name', $category);
                                $dtCategoryDetailTask = $this->db->get()->row_array();
                                if (!empty($dtCategoryDetailTask)) {
                                    $category_id = $dtCategoryDetailTask['id'];
                                } else {
                                    $this->db->insert('tbl_category_detail_task', [
                                        'name' => $category
                                    ]);
                                    $category_id = $this->db->insert_id();
                                }

                                $this->db->from('tbl_category_kpi_criteria');
                                $this->db->where('tbl_category_kpi_criteria.category_kpi_id', $kpi_category_id);
                                $this->db->where('tbl_category_kpi_criteria.code', $code_kpi);
                                $dtCategoryKpiCri = $this->db->get()->row_array();
                                $category_kpi_criteria_id = 0;
                                if (!empty($dtCategoryKpiCri)) {
                                    $category_kpi_criteria_id = $dtCategoryKpiCri['id'];
                                }

                                $this->db->from('tbl_detail_task_detail');
                                $this->db->where('tbl_detail_task_detail.detail_task_id', $detail_task_id);
                                $this->db->where('tbl_detail_task_detail.category_id', $category_id);
                                $this->db->where('tbl_detail_task_detail.code', $code_dg);
                                $dtDetailTaskDetail = $this->db->get()->row_array();
                                if (!empty($dtDetailTaskDetail)) {
                                    $this->db->where('tbl_detail_task_detail.id', $dtDetailTaskDetail['id']);
                                    $this->db->update('tbl_detail_task_detail', [
                                        'note' => $note,
                                        'category_id' => $category_id,
                                        'category_kpi_id' => $kpi_category_id,
                                        'category_kpi_criteria_id' => $category_kpi_criteria_id,
                                        'regulations' => $regulations,
                                    ]);
                                } else {
                                    $this->db->insert('tbl_detail_task_detail', [
                                        'detail_task_id' => $detail_task_id,
                                        'note' => $note,
                                        'code' => $code_dg,
                                        'category_id' => $category_id,
                                        'category_kpi_id' => $kpi_category_id,
                                        'category_kpi_criteria_id' => $category_kpi_criteria_id,
                                        'regulations' => $regulations,
                                    ]);
                                }
                            }
                        }
                    }
                }
                echo json_encode(
                    [
                        'success' => true,
                        'errors' => $errors,
                        'alert_type' => 'success',
                        'message' => 'Thêm mới và cập nhật thành công ' . $count . ' mô tả công việc theo vị trí',
                    ]
                );
                die();
            }
            echo json_encode([
                'success' => true,
                'errors' => $errors,
                'alert_type' => 'success',
                'message' => 'Import thành công ' . $count . ' dòng',
            ]);
            die();
        }
        $data['title'] = _l('Import Mô Tả Công Việc Theo Vị trí');
        $this->load->view('admin/kpi/import_detail_task', $data);
    }

    public function view_detail_task($id)
    {
        $data = [];
        $data['title'] = lang('dt_view_detail_task');
        $this->db->select('tbl_detail_task.*,tblroles.code_role,tblroles.name as name_role,tblroles.name_position');
        $this->db->from('tbl_detail_task');
        $this->db->join('tblroles', 'tblroles.roleid = tbl_detail_task.role_id');
        $this->db->where('tbl_detail_task.id', $id);
        $dtData = $this->db->get()->row_array();

        $this->db->select('tbl_detail_task_detail.*,tbl_category_detail_task.name as name_category,
            tbl_category_kpi_criteria.name as name_kpi,
            tbl_category_kpi_criteria.type as type_kpi,
            tbl_category_kpi_criteria.code as code_kpi,
        ');
        $this->db->from('tbl_detail_task_detail');
        $this->db->join('tbl_category_detail_task', 'tbl_category_detail_task.id = tbl_detail_task_detail.category_id');
        $this->db->join('tbl_category_kpi_criteria', 'tbl_category_kpi_criteria.id = tbl_detail_task_detail.category_kpi_criteria_id', 'left');
        $this->db->where('tbl_detail_task_detail.detail_task_id', $id);
        $dtDataItems = $this->db->get()->result_array();
        $arrItems = [];
        if (!empty($dtDataItems)) {
            foreach ($dtDataItems as $key => $value) {
                if (!empty($arrItems[$value['name_category']])) {
                    $arrItems[$value['name_category']][] = $value;
                } else {
                    $arrItems[$value['name_category']][] = $value;
                }
            }
        }



        $data['dtData'] = $dtData;
        $data['arrItems'] = $arrItems;
        $data['breadcrumb'] = [array('link' => base_url('admin/kpi/detail_task'), 'page' => lang('dt_detail_task')), array('link' => '#', 'page' => lang('dt_view_detail_task'))];
        $this->load->view('admin/kpi/view_detail_task', $data);
    }

    public function delete_detail_task($id)
    {
        if (!$this->perDeleteDetailTask) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }

        $this->db->from('tbl_detail_task');
        $this->db->where('tbl_detail_task.id', $id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);
            die();
        }

        $this->db->where('id', $id);
        $success = $this->db->delete('tbl_detail_task');
        if ($success) {

            $this->db->where('tbl_detail_task_detail.detail_task_id', $id);
            $this->db->delete('tbl_detail_task_detail');

            insertActivityLog([
                'type_parent_obj' => 'tbl_detail_task',
                'table_obj' => 'tbl_detail_task',
                'id_obj' => $id,
                'name_obj' => $dtData['code'],
                'content' => lang('Xóa mô tả công việc theo vị trí') . ' [' . $dtData['code'] . ']',
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

    public function suggest_kpi()
    {
        $data = [];
        if (!$this->perViewSuggestKpi) {
            access_denied('suggest_kpi');
        }
        $data['title'] = _l('dt_suggest_kpi');
        $this->load->view('admin/kpi/suggest_kpi', $data);
    }

    public function getSuggestKpi()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_suggest_kpi.id as id',
            'tbl_suggest_kpi.date as date',
            'tbl_suggest_kpi.reference_no as reference_no',
            'tbl_suggest_kpi.staff_suggest as staff_suggest',
            'tblroles.code_role as code_role',
            'tbl_suggest_kpi.status as status',
            'tbl_suggest_kpi.created_by as created_by',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_kpi';
        $where = [];
        $filter = [];

        $join = [
            'INNER JOIN tblroles ON tblroles.roleid = tbl_suggest_kpi.role_id'
        ];


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_suggest_kpi.date_status as date_status',
            'tbl_suggest_kpi.staff_status as staff_status',
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $dtCategoryRecom = get_table_where('tbl_category_recommended', ['name_table' => 'tbl_suggest_kpi'], '', 'row_array');
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
            $this->db->from('tblinternal_proposal');
            $this->db->where('tblinternal_proposal.suggest_id', $aRow['id']);
            $this->db->where('tblinternal_proposal.category_recommended_id', !empty($dtCategoryRecom) ? $dtCategoryRecom['id'] : 0);
            $dtInternal = $this->db->get()->row_array();
            $htmlInternal = '';
            if (!empty($dtInternal)) {
                $htmlInternal = '<div class="label label-success pull-left mtop5 text-center" style="padding: 5px">Phiếu: <a class="c_modal" href="' . admin_url('internal_proposal/view/' . $dtInternal['id']) . '">' . $dtInternal['code'] . '</a></div>';
            }

            $row[] = '<div class="text-left"><a class="tnh-modal" href="' . base_url('admin/kpi/view_suggest_kpi/' . $aRow['id']) . '">' . $aRow['reference_no'] . '</a></div>' . $htmlInternal;
            $row[] = '<div class="text-left">' . get_staff_full_name($aRow['staff_suggest']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['code_role']) . '</div>';

            if ($aRow['status'] == 0) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 1)\' id=\'agree\' suggest_repalce_id=\'' . $aRow['id'] . '\' value=\'1\' class=\'btn btn-success\'>' . lang('tnh_agree') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-danger po">' . lang('Chưa duyệt') . '</span></div>';
            } else if ($aRow['status'] == 1) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 0)\' id=\'agree\' suggest_repalce_id=\'' . $aRow['id'] . '\' value=\'0\' class=\'btn btn-danger\'>' . lang('Hủy duyệt') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-success po">' . lang('Đã duyệt') . '</span></div>';
                $_data .= '<div style="margin-top: 5px"> Người duyệt: ' . get_staff_full_name($aRow['staff_status']) . '</div>';
            } else {
                $_data = '';
            }
            $row[] = '<div class="text-left" style="width: 100px">' . $_data . '</div>';
            $row[] = '<div class="text-left">' . get_staff_full_name($aRow['created_by']) . '</div>';

            $view = '<a class="tnh-modal" href="' . base_url('admin/kpi/view_suggest_kpi/' . $aRow['id']) . '"><i class="fa fa-eye"></i> ' . lang('Xem phiếu') . '</a>';
            $edit = '<a class="tnh-modal" href="' . base_url('admin/kpi/detail_suggest_kpi/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('Sửa phiếu') . '</a>';

            $delete = $this->perDeleteSuggestKpi ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/kpi/delete_suggest_kpi/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>' : '';
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $view . '</li>
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[] = '<div>' . $actions . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function detail_suggest_kpi($id = 0)
    {
        $data = [];
        $this->db->select('tbl_suggest_kpi.*');
        $this->db->from('tbl_suggest_kpi');
        $this->db->where('tbl_suggest_kpi.id', $id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()) {
            if (empty($id)) {
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_kpi.reference_no]');
            } else {
                if ($dtData['reference_no'] != $this->input->post('reference_no')) {
                    $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_kpi.reference_no]');
                }
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
            $this->form_validation->set_rules('staff_suggest', lang("Nhân viên yêu cầu đánh giá"), 'required');
            $this->form_validation->set_rules('role_id', lang("Mã chức vụ đánh giá"), 'required');
            if (empty($id)) {
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('suggest_kpi');
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $staff_suggest = $this->input->post('staff_suggest');
                    $role_id = $this->input->post('role_id');
                    $note = $this->input->post('note');
                    $month = $this->input->post('month');
                    $year = $this->input->post('year');
                    $counter = $this->input->post('counter');
                    $items = [];
                    if (!empty($counter)) {
                        foreach ($counter as $key => $value) {
                            $category_kpi_id = $this->input->post('category_kpi_id')[$value];
                            $category_kpi_criteria_id = $this->input->post('category_kpi_criteria_id')[$value];
                            if (empty($category_kpi_id)) {
                                continue;
                            }
                            $result_id = $this->input->post('result_id')[$value];
                            $weight = $this->input->post('weight')[$value];

                            $items[] = [
                                'category_kpi_id' => $category_kpi_id,
                                'category_kpi_criteria_id' => $category_kpi_criteria_id,
                                'weight' => $weight,
                                'result_id' => $result_id,
                            ];
                        }
                    }
                    if (empty($items)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Không có tiêu chí đánh giá KPI!');
                        echo json_encode($data);
                        die();
                    }
                    $fields = [
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'staff_suggest' => $staff_suggest,
                        'role_id' => $role_id,
                        'note' => $note,
                        'month' => $month,
                        'year' => $year,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->insert('tbl_suggest_kpi', $fields);
                    $id = $this->db->insert_id();
                    if ($id) {
                        if (getReference('suggest_kpi') == $reference_no) {
                            updateReference('suggest_kpi');
                        }
                        if (!empty($items)) {
                            foreach ($items as $key => $value) {
                                $value['suggest_kpi_id'] = $id;
                                $this->db->insert('tbl_suggest_kpi_item', $value);
                            }
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_kpi',
                            'table_obj' => 'tbl_suggest_kpi',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu đánh giá KPI') . ' [' . $reference_no . ']',
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
                echo json_encode($data);
                die();
            } else {
                if ($this->form_validation->run() == true) {
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $staff_suggest = $this->input->post('staff_suggest');
                    $role_id = $this->input->post('role_id');
                    $note = $this->input->post('note');
                    $month = $this->input->post('month');
                    $year = $this->input->post('year');
                    $counter = $this->input->post('counter');
                    $items = [];
                    if (!empty($counter)) {
                        foreach ($counter as $key => $value) {
                            $category_kpi_id = $this->input->post('category_kpi_id')[$value];
                            $category_kpi_criteria_id = $this->input->post('category_kpi_criteria_id')[$value];
                            if (empty($category_kpi_id)) {
                                continue;
                            }
                            $result_id = $this->input->post('result_id')[$value];
                            $weight = $this->input->post('weight')[$value];
                            $suggest_kpi_item_id = !empty($this->input->post('suggest_kpi_item_id')[$value]) ? $this->input->post('suggest_kpi_item_id')[$value] : 0;

                            $items[] = [
                                'id' => $suggest_kpi_item_id,
                                'category_kpi_id' => $category_kpi_id,
                                'category_kpi_criteria_id' => $category_kpi_criteria_id,
                                'weight' => $weight,
                                'result_id' => $result_id,
                            ];
                        }
                    }
                    $fields = [
                        'date' => $date,
                        'staff_suggest' => $staff_suggest,
                        'role_id' => $role_id,
                        'note' => $note,
                        'month' => $month,
                        'year' => $year,
                        'branch_id' => $branch_id,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->where('id', $id);
                    $success = $this->db->update('tbl_suggest_kpi', $fields);
                    if ($success) {
                        if (!empty($items)) {
                            $this->db->where('suggest_kpi_id', $id);
                            $this->db->delete('tbl_suggest_kpi_item');


                            foreach ($items as $key => $value) {
                                $value['suggest_kpi_id'] = $id;
                                $this->db->insert('tbl_suggest_kpi_item', $value);
                            }
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_kpi',
                            'table_obj' => 'tbl_suggest_kpi',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu đánh giá KPI') . ' [' . $dtData['reference_no'] . ']',
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
                echo json_encode($data);
                die();
            }
        } else {
            if (empty($id)) {
                if (!$this->perAddCategoryKpi) {
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_suggest_kpi');
            } else {
                if (!$this->perEditSuggestKpi) {
                    accessDenied(true);
                }
                if ($dtData['status'] == 1) {
                    refererModel(lang('Phiếu đã duyệt không thể sửa !'));
                }
                $this->db->select('
                    tbl_suggest_kpi_item.*,
                    tbl_category_kpi.name as name_category,
                    tbl_category_kpi_criteria.type as type,
                    tbl_category_kpi_criteria.name as name_kpi,
                    tbl_category_kpi_criteria.measure as measure,
                    tbl_category_kpi_criteria.code as code_kpi,
                    tbl_category_kpi_criteria.time as time,
                    tbl_suggest_kpi_item.weight as weight,
                    tbl_detail_task_detail.regulations as regulations,
                    0 as report,
                ');
                $this->db->from('tbl_suggest_kpi_item');
                $this->db->join('tbl_category_kpi', 'tbl_category_kpi.id = tbl_suggest_kpi_item.category_kpi_id');
                $this->db->join('tbl_category_kpi_criteria', 'tbl_category_kpi_criteria.id = tbl_suggest_kpi_item.category_kpi_criteria_id');
                $this->db->join('tbl_detail_task_detail', 'tbl_detail_task_detail.category_kpi_criteria_id = tbl_category_kpi_criteria.id', 'left');
                $this->db->where('tbl_suggest_kpi_item.suggest_kpi_id', $id);
                $dtItems = $this->db->get()->result_array();
                $month = $dtData['month'];
                $year = $dtData['year'];
                $staff_suggest = $dtData['staff_suggest'];
                if (!empty($dtItems)) {
                    foreach ($dtItems as $key => $value) {
                        $category_kpi_criteria_id = $value['category_kpi_criteria_id'];
                        $tb_tamp = "(
                            SELECT
                                COUNT(tbl_production_report_kpi.category_kpi_criteria_id) as total
                            FROM tblproduction_report
                            JOIN tbl_production_report_kpi ON tbl_production_report_kpi.production_report_id = tblproduction_report.id
                            WHERE DATE_FORMAT(tblproduction_report.date, '%m') = $month AND DATE_FORMAT(tblproduction_report.date, '%Y') = $year
                            AND tblproduction_report.staff_responsible = $staff_suggest AND tbl_production_report_kpi.category_kpi_criteria_id = $category_kpi_criteria_id
                        )";
                        $dtProductionKpi = $this->db->query($tb_tamp)->row_array();
                        $dtItems[$key]['report'] = !empty($dtProductionKpi['total']) ? $dtProductionKpi['total'] : 0;
                    }
                }
                $this->db->select('
                    tblroles.roleid as roleid,
                    tblroles.name as name,
                ');
                $this->db->from('tblstaff');
                $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role');
                $this->db->where('tblstaff.staffid', $dtData['staff_suggest']);
                $dtRole = $this->db->get()->result_array();

                $data['roles'] = $dtRole;
                $data['dtData'] = $dtData;
                $data['dtItems'] = $dtItems;
                $data['title'] = lang('dt_edit_suggest_kpi');
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        //        $data['roles'] = [];
        //        $this->get_parent(0, $data['roles']);
        $data['reference_no'] = getReference('suggest_kpi');
        $data['dtResult'] = get_table_where('tbl_result');
        $this->load->view('admin/kpi/detail_suggest_kpi', $data);
    }

    public function view_suggest_kpi($id)
    {
        $data = [];
        $data['title'] = lang('dt_view_suggest_kpi');
        $this->db->select('tbl_suggest_kpi.*,tblroles.name as name_role');
        $this->db->from('tbl_suggest_kpi');
        $this->db->from('tblroles', 'tblroles.roleid = tbl_suggest_kpi.role_id');
        $this->db->where('tbl_suggest_kpi.id', $id);
        $dtData = $this->db->get()->row_array();
        $this->db->select('
            tbl_suggest_kpi_item.*,
            tbl_category_kpi.name as name_category,
            tbl_category_kpi_criteria.type as type,
            tbl_category_kpi_criteria.name as name_kpi,
            tbl_category_kpi_criteria.measure as measure,
            tbl_category_kpi_criteria.code as code_kpi,
            tbl_category_kpi_criteria.time as time,
            tbl_suggest_kpi_item.weight as weight,
            tbl_detail_task_detail.regulations as regulations,
            0 as report,
            tbl_result.name as name_result,
        ');
        $this->db->from('tbl_suggest_kpi_item');
        $this->db->join('tbl_category_kpi', 'tbl_category_kpi.id = tbl_suggest_kpi_item.category_kpi_id');
        $this->db->join('tbl_category_kpi_criteria', 'tbl_category_kpi_criteria.id = tbl_suggest_kpi_item.category_kpi_criteria_id');
        $this->db->join('tbl_detail_task_detail', 'tbl_detail_task_detail.category_kpi_criteria_id = tbl_category_kpi_criteria.id', 'left');
        $this->db->join('tbl_result', 'tbl_result.id = tbl_suggest_kpi_item.result_id', 'left');
        $this->db->where('tbl_suggest_kpi_item.suggest_kpi_id', $id);
        $dtItems = $this->db->get()->result_array();
        $month = $dtData['month'];
        $year = $dtData['year'];
        $staff_suggest = $dtData['staff_suggest'];
        if (!empty($dtItems)) {
            foreach ($dtItems as $key => $value) {
                $category_kpi_criteria_id = $value['category_kpi_criteria_id'];
                $tb_tamp = "(
                            SELECT
                                COUNT(tbl_production_report_kpi.category_kpi_criteria_id) as total
                            FROM tblproduction_report
                            JOIN tbl_production_report_kpi ON tbl_production_report_kpi.production_report_id = tblproduction_report.id
                            WHERE DATE_FORMAT(tblproduction_report.date, '%m') = $month AND DATE_FORMAT(tblproduction_report.date, '%Y') = $year
                            AND tblproduction_report.staff_responsible = $staff_suggest AND tbl_production_report_kpi.category_kpi_criteria_id = $category_kpi_criteria_id
                        )";
                $dtProductionKpi = $this->db->query($tb_tamp)->row_array();
                $dtItems[$key]['report'] = !empty($dtProductionKpi['total']) ? $dtProductionKpi['total'] : 0;
            }
        }
        $data['dtData'] = $dtData;
        $data['dtItems'] = $dtItems;
        $this->load->view('admin/kpi/view_suggest_kpi', $data);
    }

    public function agreeSuggestKpi()
    {
        if (!$this->perApproveSuggestKpi) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }

        $data = [];
        $suggest_id = $this->input->post('suggest_id');
        $status = $this->input->post('status');

        $this->db->select('tbl_suggest_kpi.*');
        $this->db->from('tbl_suggest_kpi');
        $this->db->where('tbl_suggest_kpi.id', $suggest_id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
        } else {

            if (($dtData['status'] == $status)) {
                $data['result'] = 0;
                $data['message'] = lang('Trạng thái đã được cập nhật vui lòng làm mới danh sách');
                echo json_encode($data);
                return;
            }

            if ($status == 0) {
                $dtCategoryRecom = get_table_where('tbl_category_recommended', ['name_table' => 'tbl_suggest_kpi'], '', 'row_array');
                $this->db->from('tblinternal_proposal');
                $this->db->where('tblinternal_proposal.suggest_id', $suggest_id);
                $this->db->where('tblinternal_proposal.category_recommended_id', !empty($dtCategoryRecom) ? $dtCategoryRecom['id'] : 0);
                $dtInternal = $this->db->get()->row_array();
                if (!empty($dtInternal)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Đã tạo phiếu đề xuất nội bộ đánh giá KPI không thể bỏ duyệt !');
                    echo json_encode($data);
                    return;
                }
            }

            $date_status = date('Y-m-d H:i:s');
            $staff_status = get_staff_user_id();

            $options = [
                'status' => $status,
                'date_status' => $date_status,
                'staff_status' => $staff_status,
            ];

            $this->db->where('id', $suggest_id);
            $up = $this->db->update('tbl_suggest_kpi', $options);
            if ($up) {
                insertActivityLog([
                    'type_parent_obj' => 'suggest_kpi',
                    'table_obj' => 'tbl_suggest_kpi',
                    'id_obj' => $suggest_id,
                    'name_obj' => $dtData['reference_no'],
                    'content' => lang('Duyệt phiếu yêu cầu đánh giá KPI') . ' [' . $dtData['reference_no'] . ']',
                    'actions' => 'approved'
                ]);
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        }
        echo json_encode($data);
    }

    public function delete_suggest_kpi($id)
    {
        if (!$this->perDeleteSuggestKpi) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $data = [];
        $this->db->select('tbl_suggest_kpi.*');
        $this->db->from('tbl_suggest_kpi');
        $this->db->where('tbl_suggest_kpi.id', $id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);
            die();
        }


        if ($dtData['status'] == 1) {
            $data['result'] = 0;
            $data['message'] = lang('Phiếu đã được duyệt không thể xóa !');
            echo json_encode($data);
            die();
        }

        $this->db->where('id', $id);
        $success = $this->db->delete('tbl_suggest_kpi');
        if ($success) {
            $this->db->where('tbl_suggest_kpi_item.suggest_kpi_id', $id);
            $this->db->delete('tbl_suggest_kpi_item');

            insertActivityLog([
                'type_parent_obj' => 'suggest_kpi',
                'table_obj' => 'tbl_suggest_kpi',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu đánh giá KPI') . ' [' . $dtData['reference_no'] . ']',
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

    public function getRoleByStaff()
    {
        $data = [];
        $staff_suggest = !empty($this->input->post('staff_suggest')) ? $this->input->post('staff_suggest') : 0;

        $this->db->select('
            tblroles.roleid as roleid,
            tblroles.name as name,
        ');
        $this->db->from('tblstaff');
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role');
        $this->db->where('tblstaff.staffid', $staff_suggest);
        $dtRole = $this->db->get()->result_array();
        $data['dtRole'] = $dtRole;
        echo json_encode($data);
    }

    public function getDataRole()
    {
        $data = [];
        $role_id = !empty($this->input->post('role_id')) ? $this->input->post('role_id') : 0;
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $staff_suggest = $this->input->post('staff_suggest');

        $tb_tamp = "(
            SELECT
                tbl_production_report_kpi.category_kpi_criteria_id as category_kpi_criteria_id,
                COUNT(tbl_production_report_kpi.category_kpi_criteria_id) as total
            FROM tblproduction_report
            JOIN tbl_production_report_kpi ON tbl_production_report_kpi.production_report_id = tblproduction_report.id
            WHERE DATE_FORMAT(tblproduction_report.date, '%m') = $month AND DATE_FORMAT(tblproduction_report.date, '%Y') = $year
            AND tblproduction_report.staff_responsible = $staff_suggest
            GROUP BY tbl_production_report_kpi.category_kpi_criteria_id
        ) tb_tamp";

        $this->db->select('
            tbl_category_kpi.name as name_category,
            tbl_category_kpi_criteria.type as type,
            tbl_category_kpi_criteria.name as name_kpi,
            tbl_category_kpi_criteria.measure as measure,
            tbl_category_kpi_criteria.category_kpi_id as category_kpi_id,
            tbl_category_kpi_criteria.id as category_kpi_criteria_id,
            tbl_category_kpi_criteria.code as code_kpi,
            tbl_category_kpi_criteria.time as time,
            tbl_category_kpi_criteria.weight as weight,
            tbl_detail_task_detail.regulations as regulations,
            COALESCE(tb_tamp.total,0) as report,
        ');
        $this->db->from('tblroles');
        $this->db->join('tbl_category_kpi', 'tbl_category_kpi.id = tblroles.kpi_category_id');
        $this->db->join('getDataRole', 'tbl_category_kpi_criteria.category_kpi_id = tbl_category_kpi.id');
        $this->db->join('tbl_detail_task_detail', 'tbl_detail_task_detail.category_kpi_criteria_id = tbl_category_kpi_criteria.id', 'left');
        $this->db->join($tb_tamp, 'tb_tamp.category_kpi_criteria_id = tbl_category_kpi_criteria.id', 'left');
        $this->db->where('tblroles.roleid', $role_id);
        $dtCategoryKpi = $this->db->get()->result_array();
        $data['dtCategoryKpi'] = $dtCategoryKpi;
        echo json_encode($data);
    }

    public function get_parent($id_parent = 0, &$array_category = [], $level = 0)
    {
        if (is_numeric($level)) {
            $this->db->where(array('roles_parent' => $id_parent));
            $current_level = $this->db->get('tblroles')->result_array();
            if ($current_level) {
                foreach ($current_level as $key => $value) {
                    $sub = "";
                    for ($i = 0; $i < $level; $i++) {
                        $sub .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                    }
                    $sub .= "&#10154;";
                    $current_level[$key]['name'] = $sub . " " . $current_level[$key]['name'];
                    array_push($array_category, $current_level[$key]);
                    $this->get_parent($value['roleid'], $array_category, $level + 1);
                }
            } else {
                return;
            }
        }
    }


    public function propose_kpi()
    {
        $data = [];
        if (!$this->perViewProposeKpi) {
            access_denied('propose_kpi');
        }
        $data['title'] = _l('dt_propose_kpi');
        $this->load->view('admin/kpi/propose_kpi', $data);
    }

    public function getProposeKpi()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_suggest_kpi.id as id',
            'tbl_suggest_kpi.date as date',
            'tbl_suggest_kpi.reference_no as reference_no',
            'tbl_suggest_kpi.staff_suggest as staff_suggest',
            'tblroles.code_role as code_role',
            'tbl_suggest_kpi.status as status',
            'tbl_suggest_kpi.created_by as created_by',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_kpi';
        $where = [];
        $filter = [];

        $join = [
            'INNER JOIN tblroles ON tblroles.roleid = tbl_suggest_kpi.role_id'
        ];


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_suggest_kpi.date_status as date_status',
            'tbl_suggest_kpi.staff_status as staff_status',
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left"><a class="tnh-modal" href="' . base_url('admin/kpi/view_suggest_kpi/' . $aRow['id']) . '">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left">' . get_staff_full_name($aRow['staff_suggest']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['code_role']) . '</div>';

            if ($aRow['status'] == 0) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 1)\' id=\'agree\' suggest_repalce_id=\'' . $aRow['id'] . '\' value=\'1\' class=\'btn btn-success\'>' . lang('tnh_agree') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-danger po">' . lang('Chưa duyệt') . '</span></div>';
            } else if ($aRow['status'] == 1) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 0)\' id=\'agree\' suggest_repalce_id=\'' . $aRow['id'] . '\' value=\'0\' class=\'btn btn-danger\'>' . lang('Hủy duyệt') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-success po">' . lang('Đã duyệt') . '</span></div>';
                $_data .= '<div style="margin-top: 5px"> Người duyệt: ' . get_staff_full_name($aRow['staff_status']) . '</div>';
            } else {
                $_data = '';
            }
            $row[] = '<div class="text-left" style="width: 100px">' . $_data . '</div>';
            $row[] = '<div class="text-left">' . get_staff_full_name($aRow['created_by']) . '</div>';

            $view = '<a class="tnh-modal" href="' . base_url('admin/kpi/view_suggest_kpi/' . $aRow['id']) . '"><i class="fa fa-eye"></i> ' . lang('Xem phiếu') . '</a>';
            $edit = '<a class="tnh-modal" href="' . base_url('admin/kpi/detail_suggest_kpi/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('Sửa phiếu') . '</a>';

            $delete = $this->perDeleteSuggestKpi ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/kpi/delete_suggest_kpi/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>' : '';
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $view . '</li>
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[] = '<div>' . $actions . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function detail_propose_kpi($id = 0)
    {
        $data = [];
        $this->db->select('tbl_suggest_kpi.*');
        $this->db->from('tbl_suggest_kpi');
        $this->db->where('tbl_suggest_kpi.id', $id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()) {
            if (empty($id)) {
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_kpi.reference_no]');
            } else {
                if ($dtData['reference_no'] != $this->input->post('reference_no')) {
                    $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_kpi.reference_no]');
                }
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
            $this->form_validation->set_rules('staff_suggest', lang("Nhân viên yêu cầu đánh giá"), 'required');
            $this->form_validation->set_rules('role_id', lang("Mã chức vụ đánh giá"), 'required');
            if (empty($id)) {
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('suggest_kpi');
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $staff_suggest = $this->input->post('staff_suggest');
                    $role_id = $this->input->post('role_id');
                    $note = $this->input->post('note');
                    $counter = $this->input->post('counter');
                    $items = [];
                    if (!empty($counter)) {
                        foreach ($counter as $key => $value) {
                            $category_kpi_id = $this->input->post('category_kpi_id')[$value];
                            $category_kpi_criteria_id = $this->input->post('category_kpi_criteria_id')[$value];
                            if (empty($category_kpi_id)) {
                                continue;
                            }
                            $result_id = $this->input->post('result_id')[$value];

                            $items[] = [
                                'category_kpi_id' => $category_kpi_id,
                                'category_kpi_criteria_id' => $category_kpi_criteria_id,
                                'result_id' => $result_id,
                            ];
                        }
                    }
                    if (empty($items)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Không có tiêu chí đánh giá KPI!');
                        echo json_encode($data);
                        die();
                    }
                    $fields = [
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'staff_suggest' => $staff_suggest,
                        'role_id' => $role_id,
                        'note' => $note,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->insert('tbl_suggest_kpi', $fields);
                    $id = $this->db->insert_id();
                    if ($id) {
                        if (getReference('suggest_kpi') == $reference_no) {
                            updateReference('suggest_kpi');
                        }
                        if (!empty($items)) {
                            foreach ($items as $key => $value) {
                                $value['suggest_kpi_id'] = $id;
                                $this->db->insert('tbl_suggest_kpi_item', $value);
                            }
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_kpi',
                            'table_obj' => 'tbl_suggest_kpi',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu đánh giá KPI') . ' [' . $reference_no . ']',
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
                echo json_encode($data);
                die();
            } else {
                if ($this->form_validation->run() == true) {
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $staff_suggest = $this->input->post('staff_suggest');
                    $role_id = $this->input->post('role_id');
                    $note = $this->input->post('note');
                    $counter = $this->input->post('counter');
                    $items = [];
                    if (!empty($counter)) {
                        foreach ($counter as $key => $value) {
                            $category_kpi_id = $this->input->post('category_kpi_id')[$value];
                            $category_kpi_criteria_id = $this->input->post('category_kpi_criteria_id')[$value];
                            if (empty($category_kpi_id)) {
                                continue;
                            }
                            $result_id = $this->input->post('result_id')[$value];
                            $suggest_kpi_item_id = !empty($this->input->post('suggest_kpi_item_id')[$value]) ? $this->input->post('suggest_kpi_item_id')[$value] : 0;

                            $items[] = [
                                'id' => $suggest_kpi_item_id,
                                'category_kpi_id' => $category_kpi_id,
                                'category_kpi_criteria_id' => $category_kpi_criteria_id,
                                'result_id' => $result_id,
                            ];
                        }
                    }
                    $fields = [
                        'date' => $date,
                        'staff_suggest' => $staff_suggest,
                        'role_id' => $role_id,
                        'note' => $note,
                        'branch_id' => $branch_id,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->where('id', $id);
                    $success = $this->db->update('tbl_suggest_kpi', $fields);
                    if ($success) {
                        if (!empty($items)) {
                            $this->db->where('suggest_kpi_id', $id);
                            $this->db->delete('tbl_suggest_kpi_item');


                            foreach ($items as $key => $value) {
                                $value['suggest_kpi_id'] = $id;
                                $this->db->insert('tbl_suggest_kpi_item', $value);
                            }
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_kpi',
                            'table_obj' => 'tbl_suggest_kpi',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu đánh giá KPI') . ' [' . $dtData['reference_no'] . ']',
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
                echo json_encode($data);
                die();
            }
        } else {
            if (empty($id)) {
                if (!$this->perAddCategoryKpi) {
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_suggest_kpi');
            } else {
                if (!$this->perEditSuggestKpi) {
                    accessDenied(true);
                }
                if ($dtData['status'] == 1) {
                    refererModel(lang('Phiếu đã duyệt không thể sửa !'));
                }
                $this->db->select('
                    tbl_suggest_kpi_item.*,
                    tbl_category_kpi.name as name_category,
                    tbl_category_kpi_criteria.type as type,
                    tbl_category_kpi_criteria.name as name_kpi,
                    tbl_category_kpi_criteria.measure as measure,
                    tbl_category_kpi_criteria.code as code_kpi,
                    tbl_category_kpi_criteria.time as time,
                    tbl_category_kpi_criteria.weight as weight,
                    tbl_detail_task_detail.regulations as regulations,
                    0 as report,
                ');
                $this->db->from('tbl_suggest_kpi_item');
                $this->db->join('tbl_category_kpi', 'tbl_category_kpi.id = tbl_suggest_kpi_item.category_kpi_id');
                $this->db->join('tbl_category_kpi_criteria', 'tbl_category_kpi_criteria.id = tbl_suggest_kpi_item.category_kpi_criteria_id');
                $this->db->join('tbl_detail_task_detail', 'tbl_detail_task_detail.category_kpi_criteria_id = tbl_category_kpi_criteria.id', 'left');
                $this->db->where('tbl_suggest_kpi_item.suggest_kpi_id', $id);
                $dtItems = $this->db->get()->result_array();
                $data['dtData'] = $dtData;
                $data['dtItems'] = $dtItems;
                $data['title'] = lang('dt_edit_suggest_kpi');
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        $data['roles'] = [];
        $this->get_parent(0, $data['roles']);
        $data['reference_no'] = getReference('suggest_kpi');
        $data['dtResult'] = get_table_where('tbl_result');
        $this->load->view('admin/kpi/detail_suggest_kpi', $data);
    }

    public function list_criteria_department()
    {
        $data = [];
        if (!$this->perViewListCriteriaDepartment) {
            access_denied('category_kpi');
        }
        $data['title'] = _l('Mục Tiêu KPI Phòng Ban');
        $dtDepartment = get_table_where('tbldepartments', ['room_id !=' => 0]);
        $data['dtDepartment'] = $dtDepartment;
        $this->load->view('admin/kpi/list_criteria_department', $data);
    }

    public function loadDataTableListCriteriaDepartment()
    {
        $data = [];

        $tHead = '';
        $html = '';
        $tfoot = '';
        $is_admin = is_admin();
        $arrIDStaff = employee_manage_staff();
        $deparment_id = !empty($this->input->get('department_search')) ? $this->input->get('department_search') : 0;
        $this->db->select('tbl_kpi_list_criteria_department.*,
        parent.code as code_parent,
        parent.name as name_parent,
        parent.id as id_parent,
        parent_new.code as code_parent_new,
        parent_new.name as name_parent_new,
        parent_new.id as id_parent_new,
        tbldepartments.code as code_department,
        tbldepartments.name as name_department,
        tblroles.code_role as code_role,
        tblroles.name_position as name_position,
        tblcategory_tasks.code as name_tasks,
        tblclients.zcode as name_clients,
        tblsuppliers.code as name_suppliers,
        tblcategory_tasks_process_child.name as name_tasks_process,
        tbl_machines.code as name_machines');
        $this->db->from('tbl_kpi_list_criteria_department');
        $this->db->where('tbl_kpi_list_criteria_department.department_id', $deparment_id);
        $this->db->where('tbl_kpi_list_criteria_department.violate IS NOT NULL');
        $this->db->where('TRIM(tbl_kpi_list_criteria_department.violate) !=', '');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tbl_kpi_list_criteria_department.department_id', 'inner');
        $this->db->join('tbl_kpi_list_criteria_department parent', 'parent.id = tbl_kpi_list_criteria_department.parent_id', 'left');
        $this->db->join('tbl_kpi_list_criteria_department parent_new', 'parent_new.id = parent.parent_id', 'left');
        $this->db->join('tblroles', 'tblroles.roleid = tbl_kpi_list_criteria_department.role_id', 'left');
        $this->db->join('tblcategory_tasks', 'tblcategory_tasks.id = tbl_kpi_list_criteria_department.id_tasks', 'left');
        $this->db->join('tblclients', 'tblclients.userid = tbl_kpi_list_criteria_department.id_clients', 'left');
        $this->db->join('tblsuppliers', 'tblsuppliers.id = tbl_kpi_list_criteria_department.id_suppliers', 'left');
        $this->db->join('tbl_machines', 'tbl_machines.id = tbl_kpi_list_criteria_department.id_machines', 'left');
        $this->db->join('tblcategory_tasks_process_child', 'tblcategory_tasks_process_child.id = tbl_kpi_list_criteria_department.id_task_procedure', 'left');


        $dtDataListCriteriaDepartment = $this->db->get()->result_array();

        $tHead = '<tr style="background-color: #D9F5D6">
            <th class="text-center" style="width: 50px;background-color: #D9F5D6 !important;">' . lang('STT') . '</th>
            <th class="text-center" style="width: 150px;background-color: #D9F5D6 !important;">' . lang('Mã phòng ban') . '</th>
            <th class="text-center" style="width: 150px;background-color: #D9F5D6 !important;">' . lang('Tên phòng ban') . '</th>
            <th class="text-center" style="width: 150px;background-color: #D9F5D6 !important;">' . lang('Mã mục tiêu') . '</th>
            <th class="text-center" style="width: 150px;background-color: #D9F5D6 !important;">' . lang('Tên mục tiêu') . '</th>
            <th class="text-center" style="width: 150px;background-color: #D9F5D6 !important;">' . lang('KPI phòng ban') . '</th>
            <th class="text-center" style="width: 150px;background-color: #D9F5D6 !important;">' . lang('Trọng số KPI phòng ban') . '</th>
            <th class="text-center" style="width: 150px;background-color: #D9F5D6 !important;">' . lang('Mã vị trí') . '</th>
            <th class="text-center" style="width: 150px;background-color: #D9F5D6 !important;">' . lang('Chức vụ') . '</th>
            <th class="text-center" style="width: 150px;background-color: #D9F5D6 !important;">' . lang('Mã công việc') . '</th>
            <th class="text-center" style="width: 150px;background-color: #D9F5D6 !important;">' . lang('Công việc') . '</th>
            <th class="text-center" style="width: 150px;background-color: #D9F5D6 !important;">' . lang('KPI vị trí') . '</th>
            <th class="text-center" style="width: 150px;background-color: #D9F5D6 !important;">' . lang('Trọng số KPI vị trí') . '</th>
            <th class="text-center" style="width: 150px;background-color: #D9F5D6 !important;">' . lang('Điểm KPI chuẩn') . '</th>
            <th class="text-center" style="width: 150px;background-color: #D9F5D6 !important;">' . lang('Cho phép vi phạm') . '</th>
            <th class="text-center" style="width: 150px;background-color: #D9F5D6 !important;">' . lang('Mã vi phạm') . '</th>
            <th class="text-center" style="width: 150px;background-color: #D9F5D6 !important;">' . lang('Loại vi phạm') . '</th>
            <th class="text-center" style="width: 150px;background-color: #D9F5D6 !important;">' . lang('Mức độ vi phạm') . '</th>
            <th class="text-center" style="width: 150px;background-color: #D9F5D6 !important;">' . lang('Mô tả vi phạm') . '</th>
            <th class="text-center" style="width: 140px;background-color: #D9F5D6 !important;">' . lang('Lần Vi Phạm') . '</th>
            <th class="text-center" style="width: 140px;background-color: #D9F5D6 !important;">' . lang('Điểm cộng') . '</th>
            <th class="text-center" style="width: 140px;background-color: #D9F5D6 !important;">' . lang('Điểm trừ') . '</th>
            <th class="text-center" style="width: 140px;background-color: #D9F5D6 !important;">' . lang('Tổng điểm trừ') . '</th>
            <th class="text-center" style="width: 140px;background-color: #D9F5D6 !important;">' . lang('Điều chỉnh duyệt') . '</th>
            <th class="text-center" style="width: 140px;background-color: #D9F5D6 !important;">' . lang('Điểm điều chỉnh') . '</th>
            <th class="text-center" style="width: 140px;background-color: #D9F5D6 !important;">' . lang('Điểm sau xử lý') . '</th>
            <th class="text-center" style="width: 140px;background-color: #D9F5D6 !important;">' . lang('KPI tiền chuẩn') . '</th>
            <th class="text-center" style="width: 140px;background-color: #D9F5D6 !important;">' . lang('Tỉ lệ hưởng KPI') . '</th>
            <th class="text-center" style="width: 140px;background-color: #D9F5D6 !important;">' . lang('KPI tiền thực nhận') . '</th>
            <th class="text-center" style="width: 140px;background-color: #D9F5D6 !important;">' . lang('Loại KPI') . '</th>
            <th class="text-center" style="width: 140px;background-color: #D9F5D6 !important;">' . lang('Ghi chú') . '</th>
        ';
        $tHead .= '</tr>';
        $stt = 1;
        // tối đa 3 lần
        $checkId = 0;
        $i = 0;
        if (!empty($dtDataListCriteriaDepartment)) {
            foreach ($dtDataListCriteriaDepartment as $key => $value) {
                $this->db->select('tbl_kpi_list_criteria_department_violate.*');
                $this->db->from('tbl_kpi_list_criteria_department_violate');
                $this->db->where('tbl_kpi_list_criteria_department_violate.kpi_list_criteria_department_id', (!empty($value['id_parent_new']) ? $value['id_parent_new'] : $value['id_parent']));
                $dtDataListCriteriaDepartmentViolate = $this->db->get()->result_array();
                $parentId = !empty($value['id_parent_new'])
                    ? $value['id_parent_new']
                    : $value['id_parent'];

                if ($checkId != $parentId) {
                    $i = 0;
                    $checkId = $parentId;
                }


                $html .= '<tr>';
                $html .= '<td style="text-align: left"></td>';
                $html .= '<td style="text-align: left">' . $value['code_department'] . '</td>';
                $html .= '<td style="text-align: left">' . $value['name_department'] . '</td>';
                $html .= '<td style="text-align: left;width: 250px">' .  (!empty($value['code_parent_new']) ? $value['code_parent_new'] : $value['code_parent']) . '</td>';
                $html .= '<td style="text-align: left">' .  (!empty($value['name_parent_new']) ? $value['name_parent_new'] : $value['name_parent']) . '</td>';
                $html .= '<td style="text-align: left">' .  (!empty($value['name_parent']) ? $value['name_parent'] . '-' . $value['evaluation_criteria'] : $value['evaluation_criteria']) . '</td>';
                $html .= '<td style="text-align: center">' . (!empty($value['weight']) ? $value['weight'] . '%' : '') . '</td>';
                $html .= '<td style="text-align: left">' . $value['code_role'] . '</td>';
                $html .= '<td style="text-align: right">' . $value['name_position'] . '</td>';
                $html .= '<td style="text-align: left">' . $value['name_tasks'] . '</td>';
                $html .= '<td style="text-align: left">' . $value['name_tasks_process'] . '</td>';
                $html .= '<td style="text-align: left">' . $value['kpi_position'] . '</td>';
                $html .= '<td style="text-align: left">' . $value['weight_kpi_position'] . '</td>';
                $html .= '<td style="text-align: left">' . $value['point_kpi_position'] . '</td>';
                $html .= '<td style="text-align: left">' . $value['check_violate'] . '</td>';
                $html .= '<td style="text-align: left">' . $value['violate'] . '</td>';
                $html .= '<td style="text-align: left">' . $value['type_violate'] . '</td>';
                $html .= '<td style="text-align: left">' . $value['level_violate'] . '</td>';
                $html .= '<td style="text-align: left">' . $value['note_violate'] . '</td>';
                $html .= '<td style="text-align: left">'.(!empty($dtDataListCriteriaDepartmentViolate[$i]['violations']) ? $dtDataListCriteriaDepartmentViolate[$i]['violations'] : '').'</td>';
                $html .= '<td style="text-align: left">'.(!empty($dtDataListCriteriaDepartmentViolate[$i]['point_new']) ? $dtDataListCriteriaDepartmentViolate[$i]['point_new'] : '').'</td>';
                $html .= '<td style="text-align: left">'.(!empty($dtDataListCriteriaDepartmentViolate[$i]['point']) ? ($dtDataListCriteriaDepartmentViolate[$i]['point'] == -1 ? '' : $dtDataListCriteriaDepartmentViolate[$i]['point']) : '').'</td>';
                $html .= '<td style="text-align: right">' . $value['total_point'] . '</td>';
                $html .= '<td style="text-align: right">' . $value['adjust_browsing'] . '</td>';
                $html .= '<td style="text-align: right">' . $value['adjust_point'] . '</td>';
                $html .= '<td style="text-align: right">' . $value['point_old'] . '</td>';
                $html .= '<td style="text-align: right">' . formatMoney($value['money_kpi']) . '</td>';
                $html .= '<td style="text-align: right">' . $value['ratio_kpi'] . '</td>';
                $html .= '<td style="text-align: right">' . formatMoney($value['money_real_kpi']) . '</td>';
                $type = '';
                if ($value['type_p'] == 1) {
                    $type = 'Lương P1';
                }
                if ($value['type_p'] == 2) {
                    $type = 'Lương P2';
                }
                if ($value['type_p'] == 3) {
                    $type = 'Lương P3';
                }
                $html .= '<td style="text-align: center">' . $type . '</td>';
                $html .= '<td style="text-align: right">' . $value['note'] . '</td>';
                $html .= '</tr>';
                $i ++;
            }
        }

        $data['tHead'] = $tHead;
        $data['tfoot'] = '';
        $data['html'] = $html;
        $this->load->view('admin/kpi/load_view_list_criteria_department', $data);
    }


    public function import_list_criteria_department()
    {

        $data = [];
        if (!empty($_FILES)) {
            ini_set('max_execution_time', 800);
            ini_set('memory_limit', '3500M');
            require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
            $this->load->helper('security');
            $count = 0;
            $errors = '';
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
                // $objReader->setReadDataOnly(true);
                $objPHPExcel = $objReader->load("$fullfile");

                $total_sheets = $objPHPExcel->getSheetCount();

                $allSheetName = $objPHPExcel->getSheetNames();
                $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
                $highestRow = $objWorksheet->getHighestRow();
                $highestColumn = $objWorksheet->getHighestColumn();
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('AE');
                $arraydata = array();

                $fields = $this->input->post('fields');
                for ($row = 3; $row <= $highestRow; ++$row) {
                    for ($col = 0; $col < $highestColumnIndex; ++$col) {
                        $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                        $arraydata[$row - 2][$col] = $value;
                    }
                }
                $dataArray = [];
                $arrData = [];
                $array_criteria_department_violate = [];

                foreach ($arraydata as $key => $value) {
                    $code_department = ucfirst(preg_replace('/\s+/', ' ', trim($value[1])));
                    $code = ucfirst(preg_replace('/\s+/', ' ', trim($value[3])));
                    $name = ucfirst(preg_replace('/\s+/', ' ', trim($value[4])));
                    $evaluation_criteria = ucfirst(preg_replace('/\s+/', ' ', trim($value[5])));
                    $weight = ucfirst(preg_replace('/\s+/', ' ', trim($value[6])));
                    $code_role = ucfirst(preg_replace('/\s+/', ' ', trim($value[7])));

                    $task = ucfirst(preg_replace('/\s+/', ' ', trim($value[9])));
                    $task_procedure = ucfirst(preg_replace('/\s+/', ' ', trim($value[10])));
                    $kpi_position = ucfirst(preg_replace('/\s+/', ' ', trim($value[11])));
                    $weight_kpi_position = ucfirst(preg_replace('/\s+/', ' ', trim($value[12])));
                    $point_kpi_position = ucfirst(preg_replace('/\s+/', ' ', trim($value[13])));
                    $check_violate = ucfirst(preg_replace('/\s+/', ' ', trim($value[14])));
                    $violate = ucfirst(preg_replace('/\s+/', ' ', trim($value[15])));
                    $type_violate = ucfirst(preg_replace('/\s+/', ' ', trim($value[16])));
                    $level_violate = ucfirst(preg_replace('/\s+/', ' ', trim($value[17])));
                    $note_violate = ucfirst(preg_replace('/\s+/', ' ', trim($value[18])));
                    $point_new = ucfirst(preg_replace('/\s+/', ' ', trim($value[19])));
                    $point = ucfirst(preg_replace('/\s+/', ' ', trim($value[20])));
                    $violations = ucfirst(preg_replace('/\s+/', ' ', trim($value[21])));
                    $total_point = ucfirst(preg_replace('/\s+/', ' ', trim($value[22])));
                    $adjust_browsing = ucfirst(preg_replace('/\s+/', ' ', trim($value[23])));
                    $adjust_point = ucfirst(preg_replace('/\s+/', ' ', trim($value[24])));
                    $point_old = ucfirst(preg_replace('/\s+/', ' ', trim($value[25])));
                    $money_kpi = ucfirst(preg_replace('/\s+/', ' ', trim($value[26])));
                    $ratio_kpi = ucfirst(preg_replace('/\s+/', ' ', trim($value[27])));
                    $money_real_kpi = ucfirst(preg_replace('/\s+/', ' ', trim($value[28])));
                    $type_p = strtoupper(preg_replace('/\s+/', ' ', trim($value[29])));
                    $note = ucfirst(preg_replace('/\s+/', ' ', trim($value[30])));
                    if (empty($code_department)) {
                        continue;
                    }
                    $this->db->from('tbldepartments');
                    $this->db->where('tbldepartments.code', $code_department);
                    $dtDepartment = $this->db->get()->row_array();

                    $this->db->from('tblroles');
                    $this->db->where('tblroles.code_role', $code_role);
                    $dtRole = $this->db->get()->row_array();

                    if ($type_p == 'P1'){
                        $type_p = 1;
                    } else if ($type_p == 'P2'){
                        $type_p = 2;
                    } else if ($type_p== 'P3'){
                        $type_p = 3;
                    }
                    $arrData[] = [
                        'code' => $code,
                        'name' => $name,
                        'evaluation_criteria' => $evaluation_criteria,
                        'violate' => $violate,
                        'type_p' => $type_p,
                        'weight' => $weight,
                        'point' => $point,
                        'point_new' => $point,
                        'point_cong' => $point_new,
                        'code_role' => $code_role,
                        'role_id' => $dtRole['roleid'] ?? 0,
                        'kpi_position' => $kpi_position,
                        'weight_kpi_position' => $weight_kpi_position,
                        'point_kpi_position' => $point_kpi_position,
                        'check_violate' => $check_violate,
                        'type_violate' => $type_violate,
                        'level_violate' => $level_violate,
                        'note_violate' => $note_violate,
                        'total_point' => $total_point,
                        'adjust_browsing' => $adjust_browsing,
                        'adjust_point' => $adjust_point,
                        'point_old' => $point_old,
                        'money_kpi' => $money_kpi,
                        'ratio_kpi' => $ratio_kpi,
                        'money_real_kpi' => $money_real_kpi,
                        'type_kpi' => 1,
                        'violations' => $violations,
                        'note' => $note,
                        'department_id' => $dtDepartment['departmentid'] ?? 0,
                        'code_department' => $code_department,
                        'task' => $task,
                        'task_procedure' => $task_procedure,
                    ];
                }

                $count = 0;
                $arrId = [];
                $dataArrayResult = $this->dt_get_parent($arrData);

                foreach ($dataArrayResult as $key => $value) {
                    if (!empty($value['child'])) {
                        $child_1 = $value['child'];
                        foreach ($child_1 as $kk => $vv) {
                            if (!in_array($vv['type_p'], ['1', '2', '3'])) {
                                $errors = 'Ảnh hưởng lương "' . $vv['type_p'] . '" không đúng quy định. Vui lòng kiểm tra lại file import.';
                                echo json_encode(
                                    [
                                        'success' => true,
                                        'errors' => $errors,
                                        'alert_type' => 'success',
                                        'message' => 'Import thất bại',
                                    ]
                                );
                                die();
                            }
                            if (!empty($vv['total']) && !is_numeric($vv['total'])) {
                                $errors = 'Số tiền KPI "' . $vv['total'] . '" Phải là số. Vui lòng kiểm tra lại file import.';
                                echo json_encode(
                                    [
                                        'success' => true,
                                        'errors' => $errors,
                                        'alert_type' => 'success',
                                        'message' => 'Import thất bại',
                                    ]
                                );
                                die();
                            }
                            if (!empty($vv['task'])) {
                                $tasks = get_table_where('tblcategory_tasks', array('code' => $vv['task']), '', 'row_array');
                                if (empty($tasks)) {
                                    $errors = 'Không tìm thấy mã công việc. Vui lòng kiểm tra lại file import.';
                                    echo json_encode(
                                        [
                                            'success' => true,
                                            'errors' => $errors,
                                            'alert_type' => 'success',
                                            'message' => 'Import thất bại',
                                        ]
                                    );
                                    die();
                                } else {
                                    if (!empty($vv['task_procedure'])) {
                                        $this->db->where('id_category_tasks', $tasks['id']);
                                        $this->db->where('name', $vv['task_procedure']);
                                        $task_procedure = $this->db->get('tblcategory_tasks_process_child')->row_array();
                                        if (empty($task_procedure)) {
                                            $errors = 'Không tìm thấy quy chuẩn công việc ' . $vv['task_procedure'] . ' thuộc công việc ' . $vv['task'] . '. Vui lòng kiểm tra lại file import.';
                                            echo json_encode(
                                                [
                                                    'success' => true,
                                                    'errors' => $errors,
                                                    'alert_type' => 'success',
                                                    'message' => 'Import thất bại',
                                                ]
                                            );
                                            die();
                                        }
                                    }
                                }
                            }
                            if (($vv['weight'] < 0 || $vv['weight'] > 100)) {
                                $errors = 'Trọng Số "' . $vv['weight'] . '" Phải trong phạm vi 0-100. Vui lòng kiểm tra lại file import.';
                                echo json_encode(
                                    [
                                        'success' => true,
                                        'errors' => $errors,
                                        'alert_type' => 'success',
                                        'message' => 'Import thất bại',
                                    ]
                                );
                                die();
                            }

                            if (empty($vv['department_id'])) {
                                $errors = 'Phòng ban "' . $vv['code_department'] . '" không tồn tại. Vui lòng kiểm tra lại file import.';
                                echo json_encode(
                                    [
                                        'success' => true,
                                        'errors' => $errors,
                                        'alert_type' => 'success',
                                        'message' => 'Import thất bại',
                                    ]
                                );
                                die();
                            }

                            if (empty($vv['role_id'])) {
                                $errors = 'Vị trí "' . $vv['code_role'] . '" không tồn tại. Vui lòng kiểm tra lại file import.';
                                echo json_encode(
                                    [
                                        'success' => true,
                                        'errors' => $errors,
                                        'alert_type' => 'success',
                                        'message' => 'Import thất bại',
                                    ]
                                );
                                die();
                            }
                        }
                    }
                }

                if (!empty($dataArrayResult)) {
                    foreach ($dataArrayResult as $key => $value) {
                        $this->insertKpiDepartment($value, 0, $count, $arrId);
                    }
                }

                echo json_encode(
                    [
                        'success' => true,
                        'errors' => $errors,
                        'alert_type' => 'success',
                        'message' => 'Thêm mới và cập nhật thành công ' . $count . ' mục tiêu kpi phòng ban',
                    ]
                );
                die();
            }
            echo json_encode([
                'success' => true,
                'errors' => $errors,
                'alert_type' => 'success',
                'message' => 'Import thành công ' . $count . ' dòng',
            ]);
            die();
        }
        $data['title'] = _l('Import Mục Tiêu KPI Phòng Ban');
        $dtDepartment = get_table_where('tbldepartments', ['room_id !=' => 0]);
        $data['dtDepartment'] = $dtDepartment;
        $this->load->view('admin/kpi/import_list_criteria_department', $data);
    }

    public function dt_get_parent($data)
    {
        $result = [];

        foreach ($data as $item) {

            $code = $item['code'];

            if (!isset($result[$code])) {
                $result[$code] = [
                    'code' => $item['code'],
                    'name' => $item['name'],
                    'department_id' => $item['department_id'],
                    'child' => []
                ];
            }

            $result[$code]['child'][] = $item;
        }

        return array_values($result);
    }

    public function dt_get_parent_new($data)
    {
        $result = [];

        foreach ($data as $item) {

            $arr_code = explode('-', $item['evaluation_criteria']);
            if (count($arr_code) > 1) {
                $code = $arr_code[0];
                if (!isset($result[$code])) {
                    $result[$code] = [
                        'code' => $code,
                        'name' => $code,
                        'department_id' => $item['department_id'],
                        'child' => []
                    ];
                }
                $item['code'] = trim($arr_code[1] ?? $arr_code[0]);
                $item['name'] = trim($arr_code[1] ?? $arr_code[0]);
                $item['evaluation_criteria'] = trim($arr_code[1] ?? $arr_code[0]);
                $result[$code]['child'][] = $item;
            } else {
                $result[] = $item;
            }
        }

        return array_values($result);
    }

    function insertKpiDepartment($value, $parent_id = 0, &$count = 0, &$arrId = [])
    {
        $child = !empty($value['child']) ? $value['child'] : [];
        $child = $this->dt_get_parent_new($child);
        unset($value['child']);
        if (!empty($value['violations'])) {
            $violations_text = 'Lần ' . $value['violations'] . '';
            $violations = $value['violations'];
            $point_violations = $value['point'];
            $point_violations_text = $value['point'] . 'đ';
            $point_new = $value['point_cong'];
            $point_new_text = $value['point_cong'] . 'đ';;
        }
        $value['total_kpi'] = NULL;
        $value['id_tasks'] = NULL;
        $value['id_clients'] = NULL;
        $value['id_suppliers'] = NULL;
        $value['id_machines'] = NULL;
        $value['id_task_procedure'] = NULL;
        if (!empty($value['task'])) {
            $tasks = get_table_where('tblcategory_tasks', array('code' => $value['task']), '', 'row_array');
            if (!empty($tasks)) {
                $value['id_tasks'] = $tasks['id'];
                $this->db->where('id_category_tasks', $tasks['id']);
                $this->db->where('name', $value['task_procedure']);
                $task_procedure = $this->db->get('tblcategory_tasks_process_child')->row_array();
                if (!empty($task_procedure)) {
                    $value['id_task_procedure'] = $task_procedure['id'];
                }
            }
        }

        unset($value['point_cong']);
        unset($value['task']);
        unset($value['clients']);
        unset($value['suppliers']);
        unset($value['machines']);
        unset($value['total']);
        unset($value['task_procedure']);

        $this->db->from('tbl_kpi_list_criteria_department');
        $this->db->where('code', $value['code']);
        $this->db->where('department_id', $value['department_id']);
        $dtData = $this->db->get()->row_array();
        $value['created_by'] = get_staff_user_id();
        $value['date_created'] = date('Y-m-d H:i:s');
        if (!empty($dtData)) {
            $this->db->where('id', $dtData['id']);
            $success = $this->db->update('tbl_kpi_list_criteria_department', $value);
            if ($success) {
                $count++;
                $parent_id = $dtData['id'];
            }
            $arrId[] = $dtData['id'];
        } else {
            $this->db->insert('tbl_kpi_list_criteria_department', $value);
            $success = $this->db->insert_id();
            if ($success) {
                $count++;
                $parent_id = $success;
            }
            $arrId[] = $success;
        }
        if (!empty($violations)) {
            $this->db->insert('tbl_kpi_list_criteria_department_violate', [
                'kpi_list_criteria_department_id_parent' => $parent_id,
                'kpi_list_criteria_department_id' => $parent_id,
                'violations_text' => $violations_text,
                'point_text' => $point_violations_text,
                'violations' => $violations,
                'point' => $point_violations,
                'point_new' => $point_new,
                'point_new_text' => $point_new_text,
            ]);
        }
        $this->db->where('tbl_kpi_list_criteria_department_violate.kpi_list_criteria_department_id_parent', $parent_id);
        $this->db->delete('tbl_kpi_list_criteria_department_violate');


        if (!empty($child)) {
            $child = array_values($child);
            foreach ($child as $kk => $vv) {

                if (!empty($vv['violations'])) {
                    $violations_text = 'Lần ' . $vv['violations'] . '';
                    $violations = $vv['violations'];
                    $point_violations = $vv['point'];
                    $point_violations_text = $vv['point'] . 'đ';
                    $point_new = $vv['point_cong'];
                    $point_new_text = $vv['point_cong'] . 'đ';
                }

                if (!empty($vv['code_department'])) {
                    unset($vv['code_department']);
                }
                if (!empty($vv['code_role'])) {
                    unset($vv['code_role']);
                }


                $vv['total_kpi'] = null;
                $vv['id_tasks'] = NULL;
                $vv['id_clients'] = NULL;
                $vv['id_suppliers'] = NULL;
                $vv['id_machines'] = NULL;
                $vv['id_task_procedure'] = NULL;
                if (!empty($vv['task'])) {
                    $tasks = get_table_where('tblcategory_tasks', array('code' => $vv['task']), '', 'row_array');
                    if (!empty($tasks)) {
                        $vv['id_tasks'] = $tasks['id'];
                        $this->db->where('id_category_tasks', $tasks['id']);
                        $this->db->where('name', $vv['task_procedure']);
                        $task_procedure = $this->db->get('tblcategory_tasks_process_child')->row_array();

                        if (!empty($task_procedure)) {
                            $vv['id_task_procedure'] = $task_procedure['id'];
                        }
                    }
                }
                if (!empty($vv['clients'])) {
                    $clients = get_table_where('tblclients', array('zcode' => $vv['clients']), '', 'row_array');
                    if (!empty($clients)) {
                        $vv['id_clients'] = $clients['userid'];
                    }
                }
                if (!empty($vv['suppliers'])) {
                    $suppliers = get_table_where('tblsuppliers', array('code' => $vv['suppliers']), '', 'row_array');
                    if (!empty($suppliers)) {
                        $vv['id_suppliers'] = $suppliers['id'];
                    }
                }
                if (!empty($vv['machines'])) {
                    $machines = get_table_where('tbl_machines', array('code' => $vv['machines']), '', 'row_array');
                    if (!empty($machines)) {
                        $vv['id_machines'] = $machines['id'];
                    }
                }
                unset($vv['point_cong']);
                unset($vv['task']);
                unset($vv['clients']);
                unset($vv['suppliers']);
                unset($vv['machines']);
                unset($vv['total']);
                unset($vv['task_procedure']);

                $vv['parent_id'] = $parent_id;
                $vv['created_by'] = get_staff_user_id();
                $vv['date_created'] = date('Y-m-d H:i:s');
                $child1 = !empty($vv['child']) ? $vv['child'] : [];
                unset($vv['child']);
                $this->db->from('tbl_kpi_list_criteria_department');
                $this->db->where('code', $vv['code']);
                $this->db->where('department_id', $vv['department_id']);
                $dtData = $this->db->get()->row_array();
                if (!empty($dtData)) {
                    $this->db->where('id', $dtData['id']);
                    $success = $this->db->update('tbl_kpi_list_criteria_department', $vv);
                    if ($success) {
                        $count++;
                        $parent_id_child = $dtData['id'];
                    }
                    $arrId[] = $dtData['id'];
                } else {
                    $this->db->insert('tbl_kpi_list_criteria_department', $vv);
                    $success = $this->db->insert_id();
                    if ($success) {
                        $count++;
                        $parent_id_child = $success;
                    }
                    $arrId[] = $success;
                }
                if (!empty($violations)) {
                    $this->db->insert('tbl_kpi_list_criteria_department_violate', [
                        'kpi_list_criteria_department_id_parent' => $parent_id,
                        'kpi_list_criteria_department_id' => $parent_id,
                        'violations_text' => $violations_text,
                        'point_text' => $point_violations_text,
                        'violations' => $violations,
                        'point' => $point_violations,
                        'point_new' => $point_new,
                        'point_new_text' => $point_new_text,
                    ]);
                }

                if (!empty($child1)) {
                    $child1 = array_values($child1);
                    foreach ($child1 as $kkk => $vvv) {

                        $violations_text = 'Lần ' . $vvv['violations'] . '';
                        $violations = $vvv['violations'];
                        $point_violations = $vvv['point'];
                        $point_violations_text = $vvv['point'] . 'đ';
                        $point_new = $vvv['point_cong'];
                        $point_new_text = $vvv['point_cong'] . 'đ';

                        unset($vvv['code_department']);
                        unset($vvv['code_role']);
                        $vvv['total_kpi'] = null;
                        $vvv['id_tasks'] = NULL;
                        $vvv['id_clients'] = NULL;
                        $vvv['id_suppliers'] = NULL;
                        $vvv['id_machines'] = NULL;
                        $vvv['id_task_procedure'] = NULL;
                        if (!empty($vvv['task'])) {
                            $tasks = get_table_where('tblcategory_tasks', array('code' => $vvv['task']), '', 'row_array');
                            if (!empty($tasks)) {
                                $vvv['id_tasks'] = $tasks['id'];
                                $this->db->where('id_category_tasks', $tasks['id']);
                                $this->db->where('name', $vvv['task_procedure']);
                                $task_procedure = $this->db->get('tblcategory_tasks_process_child')->row_array();
                                if (!empty($task_procedure)) {
                                    $vvv['id_task_procedure'] = $task_procedure['id'];
                                }
                            }
                        }
                        if (!empty($vvv['task'])) {
                            $tasks = get_table_where('tblcategory_tasks', array('code' => $vvv['task']), '', 'row_array');
                            if (!empty($tasks)) {
                                $vvv['id_tasks'] = $tasks['id'];
                            }
                        }
                        if (!empty($vvv['clients'])) {
                            $clients = get_table_where('tblclients', array('zcode' => $vvv['clients']), '', 'row_array');
                            if (!empty($clients)) {
                                $vvv['id_clients'] = $clients['userid'];
                            }
                        }
                        if (!empty($vvv['suppliers'])) {
                            $suppliers = get_table_where('tblsuppliers', array('code' => $vvv['suppliers']), '', 'row_array');
                            if (!empty($suppliers)) {
                                $vvv['id_suppliers'] = $suppliers['id'];
                            }
                        }
                        if (!empty($vvv['machines'])) {
                            $machines = get_table_where('tbl_machines', array('code' => $vvv['machines']), '', 'row_array');
                            if (!empty($machines)) {
                                $vvv['id_machines'] = $machines['id'];
                            }
                        }
                        unset($vvv['point_cong']);
                        unset($vvv['task']);
                        unset($vvv['clients']);
                        unset($vvv['suppliers']);
                        unset($vvv['machines']);
                        unset($vvv['total']);
                        unset($vvv['task_procedure']);
                        $vvv['parent_id'] = $parent_id_child;
                        $vvv['created_by'] = get_staff_user_id();
                        $vvv['date_created'] = date('Y-m-d H:i:s');
                        $this->db->from('tbl_kpi_list_criteria_department');
                        $this->db->where('code', $vvv['code']);
                        $this->db->where('department_id', $vvv['department_id']);
                        $dtData = $this->db->get()->row_array();
                        if (!empty($dtData)) {
                            $this->db->where('id', $dtData['id']);
                            $success = $this->db->update('tbl_kpi_list_criteria_department', $vvv);
                            if ($success) {
                                $count++;
                                $parent_id_child_new = $dtData['id'];
                            }
                            $arrId[] = $dtData['id'];
                        } else {
                            $this->db->insert('tbl_kpi_list_criteria_department', $vvv);
                            $success = $this->db->insert_id();
                            if ($success) {
                                $count++;
                                $parent_id_child_new = $success;
                            }
                            $arrId[] = $success;
                        }

                        if (!empty($violations)) {
                            $this->db->insert('tbl_kpi_list_criteria_department_violate', [
                                'kpi_list_criteria_department_id_parent' => $parent_id,
                                'kpi_list_criteria_department_id' => $parent_id,
                                'violations_text' => $violations_text,
                                'point_text' => $point_violations_text,
                                'violations' => $violations,
                                'point' => $point_violations,
                                'point_new' => $point_new,
                                'point_new_text' => $point_new_text,
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function import_list_criteria_department_old()
    {

        $data = [];
        if (!empty($_FILES)) {
            ini_set('max_execution_time', 800);
            ini_set('memory_limit', '3500M');
            require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
            $this->load->helper('security');
            $count = 0;
            $errors = '';
            $data = [];
            $department_id = $this->input->post('department_id');
            if (empty($department_id)) {
                echo json_encode(
                    [
                        'success' => false,
                        'alert_type' => 'danger',
                        'message' => 'Vui lòng chọn phòng ban',
                    ]
                );
                die();
            }
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
                // $objReader->setReadDataOnly(true);
                $objPHPExcel = $objReader->load("$fullfile");

                $total_sheets = $objPHPExcel->getSheetCount();

                $allSheetName = $objPHPExcel->getSheetNames();
                $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
                $highestRow = $objWorksheet->getHighestRow();
                $highestColumn = $objWorksheet->getHighestColumn();
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('AE');
                $arraydata = array();

                $fields = $this->input->post('fields');
                for ($row = 2; $row <= $highestRow; ++$row) {
                    for ($col = 0; $col < $highestColumnIndex; ++$col) {
                        $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                        $arraydata[$row - 2][$col] = $value;
                    }
                }
                $dataArray = [];
                $arrData = [];
                $array_criteria_department_violate = [];

                foreach ($arraydata as $key => $value) {
                    $code_parent = ucfirst(preg_replace('/\s+/', ' ', trim($value[0])));
                    $code = ucfirst(preg_replace('/\s+/', ' ', trim($value[1])));
                    $evaluation_criteria = ucfirst(preg_replace('/\s+/', ' ', trim($value[2])));

                    $task = ucfirst(preg_replace('/\s+/', ' ', trim($value[3])));
                    $task_procedure = ucfirst(preg_replace('/\s+/', ' ', trim($value[4])));
                    $clients = ucfirst(preg_replace('/\s+/', ' ', trim($value[5])));
                    $suppliers = ucfirst(preg_replace('/\s+/', ' ', trim($value[6])));
                    $machines = ucfirst(preg_replace('/\s+/', ' ', trim($value[7])));
                    $total = ucfirst(preg_replace('/\s+/', ' ', trim($value[8])));



                    $violate = ucfirst(preg_replace('/\s+/', ' ', trim($value[9])));

                    $type_p = ucfirst(preg_replace('/\s+/', ' ', trim($value[10])));
                    $weight = ucfirst(preg_replace('/\s+/', ' ', trim($value[11])));



                    $point = ucfirst(preg_replace('/\s+/', ' ', trim($value[12])));
                    $violations_text = ucfirst(preg_replace('/\s+/', ' ', trim($value[13])));
                    $point_violations_text = ucfirst(preg_replace('/\s+/', ' ', trim($value[14])));
                    $violations = ucfirst(preg_replace('/\s+/', ' ', trim($value[15])));
                    $point_violations = ucfirst(preg_replace('/\s+/', ' ', trim($value[16])));
                    $point_vuot_text = ucfirst(preg_replace('/\s+/', ' ', trim($value[17])));
                    $point_vuot = ucfirst(preg_replace('/\s+/', ' ', trim($value[18])));

                    $arrData[] = [
                        'name_parent' => $code_parent,
                        'code' => !empty($code) ? $code : $evaluation_criteria,
                        'name' => $code,
                        'evaluation_criteria' => $evaluation_criteria,
                        'violate' => $violate,
                        'type_p' => $type_p,
                        'weight' => $weight,
                        'point' => $point,
                        'violations_text' => $violations_text,
                        'point_violations_text' => $point_violations_text,
                        'violations' => $violations,
                        'point_violations' => $point_violations,
                        'department_id' => $department_id,
                        'point_new' => $point_vuot,
                        'point_new_text' => $point_vuot_text,
                        'task' => $task,
                        'task_procedure' => $task_procedure,
                        'clients' => $clients,
                        'suppliers' => $suppliers,
                        'machines' => $machines,
                        'total' => $total,
                    ];
                }

                $count = 0;
                $arrId = [];
                $dataArrayResult = $this->dt_get_parent($arrData);

                foreach ($dataArrayResult as $key => $value) {
                    if (!empty($value['child'])) {
                        $child_1 = $value['child'];
                        foreach ($child_1 as $k => $v) {
                            if (!empty($v['child'])) {
                                $child_2 = $v['child'];
                                foreach ($child_2 as $kk => $vv) {
                                    if (!in_array($vv['type_p'], ['1', '2', '3'])) {
                                        $errors = 'Ảnh hưởng lương (Nhập số) "' . $vv['type_p'] . '" không đúng quy định. Vui lòng kiểm tra lại file import.';
                                        echo json_encode(
                                            [
                                                'success' => true,
                                                'errors' => $errors,
                                                'alert_type' => 'success',
                                                'message' => 'Import thất bại',
                                            ]
                                        );
                                        die();
                                    }
                                    if (!empty($vv['total']) && !is_numeric($vv['total'])) {
                                        $errors = 'Số tiền KPI "' . $vv['total'] . '" Phải là số. Vui lòng kiểm tra lại file import.';
                                        echo json_encode(
                                            [
                                                'success' => true,
                                                'errors' => $errors,
                                                'alert_type' => 'success',
                                                'message' => 'Import thất bại',
                                            ]
                                        );
                                        die();
                                    }
                                    if (!empty($vv['task'])) {
                                        $tasks = get_table_where('tblcategory_tasks', array('code' => $vv['task']), '', 'row_array');
                                        if (empty($tasks)) {
                                            $errors = 'Không tìm thấy mã công việc. Vui lòng kiểm tra lại file import.';
                                            echo json_encode(
                                                [
                                                    'success' => true,
                                                    'errors' => $errors,
                                                    'alert_type' => 'success',
                                                    'message' => 'Import thất bại',
                                                ]
                                            );
                                            die();
                                        } else {
                                            if (!empty($vv['task_procedure'])) {
                                                $this->db->where('id_category_tasks', $tasks['id']);
                                                $this->db->where('name', $vv['task_procedure']);
                                                $task_procedure = $this->db->get('tblcategory_tasks_process_child')->row_array();
                                                if (empty($task_procedure)) {
                                                    $errors = 'Không tìm thấy quy chuẩn công việc ' . $vv['task_procedure'] . ' thuộc công việc ' . $vv['task'] . '. Vui lòng kiểm tra lại file import.';
                                                    echo json_encode(
                                                        [
                                                            'success' => true,
                                                            'errors' => $errors,
                                                            'alert_type' => 'success',
                                                            'message' => 'Import thất bại',
                                                        ]
                                                    );
                                                    die();
                                                }
                                            }
                                        }
                                    }
                                    if (!empty($vv['clients'])) {
                                        $clients = get_table_where('tblclients', array('zcode' => $vv['clients']), '', 'row_array');
                                        if (empty($clients)) {
                                            $errors = 'Không tìm thấy khách hàng. Vui lòng kiểm tra lại file import.';
                                            echo json_encode(
                                                [
                                                    'success' => true,
                                                    'errors' => $errors,
                                                    'alert_type' => 'success',
                                                    'message' => 'Import thất bại',
                                                ]
                                            );
                                            die();
                                        }
                                    }
                                    if (!empty($vv['suppliers'])) {
                                        $suppliers = get_table_where('tblsuppliers', array('code' => $vv['suppliers']), '', 'row_array');
                                        if (empty($suppliers)) {
                                            $errors = 'Không tìm thấy nhà cung cấp. Vui lòng kiểm tra lại file import.';
                                            echo json_encode(
                                                [
                                                    'success' => true,
                                                    'errors' => $errors,
                                                    'alert_type' => 'success',
                                                    'message' => 'Import thất bại',
                                                ]
                                            );
                                            die();
                                        }
                                    }
                                    if (!empty($vv['machines'])) {
                                        $machines = get_table_where('tbl_machines', array('code' => $vv['machines']), '', 'row_array');
                                        if (empty($machines)) {
                                            $errors = 'Không tìm thấy thiết bị. Vui lòng kiểm tra lại file import.';
                                            echo json_encode(
                                                [
                                                    'success' => true,
                                                    'errors' => $errors,
                                                    'alert_type' => 'success',
                                                    'message' => 'Import thất bại',
                                                ]
                                            );
                                            die();
                                        }
                                    }
                                    if (($vv['weight'] < 0 || $vv['weight'] > 100)) {
                                        $errors = 'Trọng Số "' . $vv['weight'] . '" Phải trong phạm vi 0-100. Vui lòng kiểm tra lại file import.';
                                        echo json_encode(
                                            [
                                                'success' => true,
                                                'errors' => $errors,
                                                'alert_type' => 'success',
                                                'message' => 'Import thất bại',
                                            ]
                                        );
                                        die();
                                    }
                                }
                            }
                        }
                    }
                }

                if (!empty($dataArrayResult)) {
                    foreach ($dataArrayResult as $key => $value) {
                        $this->insertKpiDepartment($value, 0, $count, $arrId);
                    }
                }

                foreach ($arrData as $key => $value) {
                    $this->db->where('code', $value['name_parent']);
                    $this->db->where('department_id', $value['department_id']);
                    $dtData = $this->db->get('tbl_kpi_list_criteria_department')->row();
                    if (!empty($dtData)) {
                        $this->insertKpiDepartment($value, $dtData->id, $count, $arrId);
                    }
                }
                $arrId = array_unique($arrId);
                $this->db->where_not_in('tbl_kpi_list_criteria_department.id', $arrId);
                $this->db->where('tbl_kpi_list_criteria_department.department_id', $department_id);
                $this->db->delete('tbl_kpi_list_criteria_department');
                echo json_encode(
                    [
                        'success' => true,
                        'errors' => $errors,
                        'alert_type' => 'success',
                        'message' => 'Thêm mới và cập nhật thành công ' . $count . ' mục tiêu kpi phòng ban',
                    ]
                );
                die();
            }
            echo json_encode([
                'success' => true,
                'errors' => $errors,
                'alert_type' => 'success',
                'message' => 'Import thành công ' . $count . ' dòng',
            ]);
            die();
        }
        $data['title'] = _l('Import Mục Tiêu KPI Phòng Ban');
        $dtDepartment = get_table_where('tbldepartments', ['room_id !=' => 0]);
        $data['dtDepartment'] = $dtDepartment;
        $this->load->view('admin/kpi/import_list_criteria_department', $data);
    }

    public function dt_get_parent_old($data, $name_parent = '', $break = 0)
    {
        $result = [];
        if ($break == 20) {
            return $result;
        }
        foreach ($data as $key => $item) {
            $break++;
            if ($item['code'] != '') {
                if ($item['name_parent'] == $name_parent) {
                    $result[$key] = $item;
                    $child = $this->dt_get_parent($data, $item['code'], $break);

                    if (!empty($child)) {
                        $result[$key]['child'] = $child;
                    }
                }
            }
        }

        return $result;
    }

    // viết tạm
    function insertKpiDepartment_old($value, $parent_id = 0, &$count = 0, &$arrId = [])
    {
        $child = !empty($value['child']) ? $value['child'] : [];

        $violations_text = $value['violations_text'];
        $point_violations_text = $value['point_violations_text'];
        $violations = $value['violations'];
        $point_violations = $value['point_violations'];
        $point_new = $value['point_new'];
        $point_new_text = $value['point_new_text'];

        unset($value['violations_text']);
        unset($value['point_violations_text']);
        unset($value['violations']);
        unset($value['point_violations']);
        unset($value['name_parent']);
        unset($value['child']);
        unset($value['point_new']);
        unset($value['point_new_text']);
        $value['total_kpi'] = $value['total'];
        $value['id_tasks'] = NULL;
        $value['id_clients'] = NULL;
        $value['id_suppliers'] = NULL;
        $value['id_machines'] = NULL;
        $value['id_task_procedure'] = NULL;
        if (!empty($value['task'])) {
            $tasks = get_table_where('tblcategory_tasks', array('code' => $value['task']), '', 'row_array');
            if (!empty($tasks)) {
                $value['id_tasks'] = $tasks['id'];
                $this->db->where('id_category_tasks', $tasks['id']);
                $this->db->where('name', $value['task_procedure']);
                $task_procedure = $this->db->get('tblcategory_tasks_process_child')->row_array();
                if (!empty($task_procedure)) {
                    $value['id_task_procedure'] = $task_procedure['id'];
                }
            }
        }
        if (!empty($value['clients'])) {
            $clients = get_table_where('tblclients', array('zcode' => $value['clients']), '', 'row_array');
            if (!empty($clients)) {
                $value['id_clients'] = $clients['userid'];
            }
        }
        if (!empty($value['suppliers'])) {
            $suppliers = get_table_where('tblsuppliers', array('code' => $value['suppliers']), '', 'row_array');
            if (!empty($suppliers)) {
                $value['id_suppliers'] = $suppliers['id'];
            }
        }
        if (!empty($value['machines'])) {
            $machines = get_table_where('tbl_machines', array('code' => $value['machines']), '', 'row_array');
            if (!empty($machines)) {
                $value['id_machines'] = $machines['id'];
            }
        }

        unset($value['task']);
        unset($value['clients']);
        unset($value['suppliers']);
        unset($value['machines']);
        unset($value['total']);
        unset($value['task_procedure']);

        $this->db->from('tbl_kpi_list_criteria_department');
        $this->db->where('code', $value['code']);
        $this->db->where('department_id', $value['department_id']);
        $dtData = $this->db->get()->row_array();
        $value['created_by'] = get_staff_user_id();
        $value['date_created'] = date('Y-m-d H:i:s');
        if (!empty($dtData)) {
            $this->db->where('id', $dtData['id']);
            $success = $this->db->update('tbl_kpi_list_criteria_department', $value);
            if ($success) {
                $count++;
                $parent_id = $dtData['id'];
            }
            $arrId[] = $dtData['id'];
        } else {
            $this->db->insert('tbl_kpi_list_criteria_department', $value);
            $success = $this->db->insert_id();
            if ($success) {
                $count++;
                $parent_id = $success;
            }
            $arrId[] = $success;
        }
        $this->db->where('tbl_kpi_list_criteria_department_violate.kpi_list_criteria_department_id', $parent_id);
        $this->db->delete('tbl_kpi_list_criteria_department_violate');

        if (!empty($violations)) {
            $this->db->insert('tbl_kpi_list_criteria_department_violate', [
                'kpi_list_criteria_department_id' => $parent_id,
                'violations_text' => $violations_text,
                'point_text' => $point_violations_text,
                'violations' => $violations,
                'point' => $point_violations,
                'point_new' => $point_new,
                'point_new_text' => $point_new_text,
            ]);
        }

        if (!empty($child)) {
            $child = array_values($child);
            foreach ($child as $kk => $vv) {

                $violations_text = $vv['violations_text'];
                $point_violations_text = $vv['point_violations_text'];
                $violations = $vv['violations'];
                $point_violations = $vv['point_violations'];
                $point_new = $vv['point_new'];
                $point_new_text = $vv['point_new_text'];

                unset($vv['violations_text']);
                unset($vv['point_violations_text']);
                unset($vv['violations']);
                unset($vv['point_violations']);
                unset($vv['name_parent']);
                unset($vv['point_new']);
                unset($vv['point_new_text']);


                $vv['total_kpi'] = $vv['total'];
                $vv['id_tasks'] = NULL;
                $vv['id_clients'] = NULL;
                $vv['id_suppliers'] = NULL;
                $vv['id_machines'] = NULL;
                $vv['id_task_procedure'] = NULL;
                if (!empty($vv['task'])) {
                    $tasks = get_table_where('tblcategory_tasks', array('code' => $vv['task']), '', 'row_array');
                    if (!empty($tasks)) {
                        $vv['id_tasks'] = $tasks['id'];
                        $this->db->where('id_category_tasks', $tasks['id']);
                        $this->db->where('name', $vv['task_procedure']);
                        $task_procedure = $this->db->get('tbl_tasks_process_child')->row_array();
                        if (!empty($task_procedure)) {
                            $vv['id_task_procedure'] = $task_procedure['id'];
                        }
                    }
                }
                if (!empty($vv['clients'])) {
                    $clients = get_table_where('tblclients', array('zcode' => $vv['clients']), '', 'row_array');
                    if (!empty($clients)) {
                        $vv['id_clients'] = $clients['userid'];
                    }
                }
                if (!empty($vv['suppliers'])) {
                    $suppliers = get_table_where('tblsuppliers', array('code' => $vv['suppliers']), '', 'row_array');
                    if (!empty($suppliers)) {
                        $vv['id_suppliers'] = $suppliers['id'];
                    }
                }
                if (!empty($vv['machines'])) {
                    $machines = get_table_where('tbl_machines', array('code' => $vv['machines']), '', 'row_array');
                    if (!empty($machines)) {
                        $vv['id_machines'] = $machines['id'];
                    }
                }

                unset($vv['task']);
                unset($vv['clients']);
                unset($vv['suppliers']);
                unset($vv['machines']);
                unset($vv['total']);
                unset($vv['task_procedure']);

                $vv['parent_id'] = $parent_id;
                $vv['created_by'] = get_staff_user_id();
                $vv['date_created'] = date('Y-m-d H:i:s');
                $child1 = !empty($vv['child']) ? $vv['child'] : [];
                unset($vv['child']);
                $this->db->from('tbl_kpi_list_criteria_department');
                $this->db->where('code', $vv['code']);
                $this->db->where('department_id', $vv['department_id']);
                $dtData = $this->db->get()->row_array();
                if (!empty($dtData)) {
                    $this->db->where('id', $dtData['id']);
                    $success = $this->db->update('tbl_kpi_list_criteria_department', $vv);
                    if ($success) {
                        $count++;
                        $parent_id_child = $dtData['id'];
                    }
                    $arrId[] = $dtData['id'];
                } else {
                    $this->db->insert('tbl_kpi_list_criteria_department', $vv);
                    $success = $this->db->insert_id();
                    if ($success) {
                        $count++;
                        $parent_id_child = $success;
                    }
                    $arrId[] = $success;
                }
                if (!empty($violations)) {
                    $this->db->insert('tbl_kpi_list_criteria_department_violate', [
                        'kpi_list_criteria_department_id' => $parent_id,
                        'violations_text' => $violations_text,
                        'point_text' => $point_violations_text,
                        'violations' => $violations,
                        'point' => $point_violations,
                        'point_new' => $point_new,
                        'point_new_text' => $point_new_text,
                    ]);
                }

                if (!empty($child1)) {
                    $child1 = array_values($child1);
                    foreach ($child1 as $kkk => $vvv) {

                        $violations_text = $vvv['violations_text'];
                        $point_violations_text = $vvv['point_violations_text'];
                        $violations = $vvv['violations'];
                        $point_violations = $vvv['point_violations'];
                        $point_new = $vvv['point_new'];
                        $point_new_text = $vvv['point_new_text'];

                        unset($vvv['violations_text']);
                        unset($vvv['point_violations_text']);
                        unset($vvv['violations']);
                        unset($vvv['point_violations']);
                        unset($vvv['name_parent']);
                        unset($vvv['child']);
                        unset($vvv['point_new']);
                        unset($vvv['point_new_text']);
                        $vvv['total_kpi'] = $vvv['total'];
                        $vvv['id_tasks'] = NULL;
                        $vvv['id_clients'] = NULL;
                        $vvv['id_suppliers'] = NULL;
                        $vvv['id_machines'] = NULL;
                        $vvv['id_task_procedure'] = NULL;
                        if (!empty($vvv['task'])) {
                            $tasks = get_table_where('tblcategory_tasks', array('code' => $vvv['task']), '', 'row_array');
                            if (!empty($tasks)) {
                                $vvv['id_tasks'] = $tasks['id'];
                                $this->db->where('id_category_tasks', $tasks['id']);
                                $this->db->where('name', $vvv['task_procedure']);
                                $task_procedure = $this->db->get('tbl_tasks_process_child')->row_array();
                                if (!empty($task_procedure)) {
                                    $vvv['id_task_procedure'] = $task_procedure['id'];
                                }
                            }
                        }
                        if (!empty($vvv['task'])) {
                            $tasks = get_table_where('tblcategory_tasks', array('code' => $vvv['task']), '', 'row_array');
                            if (!empty($tasks)) {
                                $vvv['id_tasks'] = $tasks['id'];
                            }
                        }
                        if (!empty($vvv['clients'])) {
                            $clients = get_table_where('tblclients', array('zcode' => $vvv['clients']), '', 'row_array');
                            if (!empty($clients)) {
                                $vvv['id_clients'] = $clients['userid'];
                            }
                        }
                        if (!empty($vvv['suppliers'])) {
                            $suppliers = get_table_where('tblsuppliers', array('code' => $vvv['suppliers']), '', 'row_array');
                            if (!empty($suppliers)) {
                                $vvv['id_suppliers'] = $suppliers['id'];
                            }
                        }
                        if (!empty($vvv['machines'])) {
                            $machines = get_table_where('tbl_machines', array('code' => $vvv['machines']), '', 'row_array');
                            if (!empty($machines)) {
                                $vvv['id_machines'] = $machines['id'];
                            }
                        }

                        unset($vvv['task']);
                        unset($vvv['clients']);
                        unset($vvv['suppliers']);
                        unset($vvv['machines']);
                        unset($vvv['total']);
                        unset($vvv['task_procedure']);
                        $vvv['parent_id'] = $parent_id_child;
                        $vvv['created_by'] = get_staff_user_id();
                        $vvv['date_created'] = date('Y-m-d H:i:s');
                        $this->db->from('tbl_kpi_list_criteria_department');
                        $this->db->where('code', $vvv['code']);
                        $this->db->where('department_id', $vvv['department_id']);
                        $dtData = $this->db->get()->row_array();
                        if (!empty($dtData)) {
                            $this->db->where('id', $dtData['id']);
                            $success = $this->db->update('tbl_kpi_list_criteria_department', $vvv);
                            if ($success) {
                                $count++;
                            }
                            $arrId[] = $dtData['id'];
                        } else {
                            $this->db->insert('tbl_kpi_list_criteria_department', $vvv);
                            $success = $this->db->insert_id();
                            if ($success) {
                                $count++;
                            }
                            $arrId[] = $success;
                        }

                        if (!empty($violations)) {
                            $this->db->insert('tbl_kpi_list_criteria_department_violate', [
                                'kpi_list_criteria_department_id' => $parent_id,
                                'violations_text' => $violations_text,
                                'point_text' => $point_violations_text,
                                'violations' => $violations,
                                'point' => $point_violations,
                                'point_new' => $point_new,
                                'point_new_text' => $point_new_text,
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function staff_kpi_evaluation()
    {
        $data = [];
        $data['title'] = lang('Đánh giá KPI');
        $dtDepartment = get_table_where('tbldepartments', ['room_id !=' => 0]);
        $data['dtDepartment'] = $dtDepartment;
        $data['type_check'] = $this->type_check;
        $data['staff_id_selected'] = $this->input->get('staff_id') ?? 0;
        $data['month_selected'] = $this->input->get('month') ?? null;
        $data['year_selected'] = $this->input->get('year') ?? null;
        $this->load->view('admin/kpi/staff_kpi_evaluation', $data);
    }

    public function view_kpi_evaluation()
    {
        $month = $this->input->post('filter_month');
        $year = $this->input->post('year');
        $staff = $this->input->post('staff');
        $department_search = $this->input->post('department_search');
        $precious = $this->input->post('precious');
        $month_year = $year . '-' . $month;
        $month_year_start = null;
        $month_year_end = null;
        if (!empty($precious)) {
            if ($precious == 1) {
                $month_year_start = $year . '-01';
                $month_year_end = $year . '-03';
            } elseif ($precious == 2) {
                $month_year_start = $year . '-04';
                $month_year_end = $year . '-06';
            } elseif ($precious == 3) {
                $month_year_start = $year . '-07';
                $month_year_end = $year . '-09';
            } elseif ($precious == 4) {
                $month_year_start = $year . '-10';
                $month_year_end = $year . '-12';
            }
        }
        $whereDate = '';
        $whereDateTask = '';
        $whereDateOld = '';
        $whereDateTaskOld = '';
        $whereDateDecision = '';
        $whereDateAudit = '';
        if (!empty($month_year_start)) {
            $whereDate = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") <= "' . $month_year_end . '"';
            $whereDateTask = 'AND DATE_FORMAT(tbltasks.dateadded, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tbltasks.dateadded, "%Y-%m") <= "' . $month_year_end . '"';
            $whereDateOld = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") < "' . $month_year_start . '"';
            $whereDateTaskOld = 'AND DATE_FORMAT(tbltasks.dateadded, "%Y-%m") < "' . $month_year_start . '"';
            $whereDateDecision = 'AND DATE_FORMAT(tbl_decision_bonus_discipline.date, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tbl_decision_bonus_discipline.date, "%Y-%m") <= "' . $month_year_end . '"';
            $whereDateAudit = 'AND DATE_FORMAT(tbl_audit.audit_date, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tbl_audit.audit_date, "%Y-%m") <= "' . $month_year_end . '"';
        } else {
            $whereDate = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") = "' . $month_year . '"';
            $whereDateTask = 'AND DATE_FORMAT(tbltasks.dateadded, "%Y-%m") = "' . $month_year . '"';
            $whereDateOld = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") < "' . $month_year . '"';
            $whereDateTaskOld = 'AND DATE_FORMAT(tbltasks.dateadded, "%Y-%m") < "' . $month_year . '"';
            $whereDateDecision = 'AND DATE_FORMAT(tbl_decision_bonus_discipline.date, "%Y-%m") = "' . $month_year . '"';
            $whereDateAudit = 'AND DATE_FORMAT(tbl_audit.audit_date, "%Y-%m") = "' . $month_year . '"';
        }

        $tb_tamp_audit = "(
            SELECT
                tblstaff_departments.staffid as staff_id,
                COUNT(tbl_audit.id) as total_audit
            FROM tbl_audit
            JOIN tbl_room ON tbl_room.id = tbl_audit.dept_id
            JOIN tbldepartments ON tbldepartments.room_id = tbl_room.id
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            WHERE EXISTS (
                SELECT 1
                FROM tbl_audit_checklist
                WHERE tbl_audit_checklist.audit_id = tbl_audit.id
                AND tbl_audit_checklist.status = 'no'
            )
            $whereDateAudit 
            GROUP BY tblstaff_departments.staffid
        ) tb_tamp_audit";

        $tb_tamp_task_process = "(
            SELECT 
                tbltask_assigned.staffid as staff_id,
                COUNT(tbltasks.id) as total_task
            FROM tbltasks
            JOIN tbltask_assigned ON tbltask_assigned.taskid = tbltasks.id
            WHERE tbltasks.id != 0 AND tbltasks.status != 5 $whereDateTask
            GROUP BY tbltask_assigned.staffid
        ) tb_tamp_task_process";

        $tb_tamp_task = "(
            SELECT 
                tbltask_assigned.staffid as staff_id,
                COUNT(tbltasks.id) as total_task
            FROM tbltasks
            JOIN tbltask_assigned ON tbltask_assigned.taskid = tbltasks.id
            WHERE tbltasks.id != 0 $whereDateTask
            GROUP BY tbltask_assigned.staffid
        ) tb_tamp_task";

        $tb_tamp = "(
            SELECT 
                tblproduction_report.staff_responsible as staff_id,
                GROUP_CONCAT((tblproduction_report.kpi_list_criteria_department_id) SEPARATOR ',') as kpi_list_criteria_department_id,
                COUNT(tblproduction_report.id) as violate
            FROM tblproduction_report
            WHERE kpi_list_criteria_department_id != 0 AND tblproduction_report.violate = 1
            $whereDate
            GROUP BY tblproduction_report.staff_responsible
        ) tb_tamp";

        $tb_tamp_vuot = "(
            SELECT 
                tblproduction_report.staff_responsible as staff_id,
                GROUP_CONCAT((tblproduction_report.kpi_list_criteria_department_id) SEPARATOR ',') as kpi_list_criteria_department_id,
                COUNT(tblproduction_report.id) as vuot
            FROM tblproduction_report
            WHERE tblproduction_report.type_report = 2 AND kpi_list_criteria_department_id != 0
            $whereDate
            GROUP BY tblproduction_report.staff_responsible
        ) tb_tamp_vuot";

        $tb_tamp_report = "(
             SELECT
                tblproduction_report.staff_responsible as staff_id,
                COUNT(tblproduction_report.id) as count_bckph,
                SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 1 THEN 1 ELSE 0 END) AS violation_p1,
                SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 2 THEN 1 ELSE 0 END) AS violation_p2,
                SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 3 THEN 1 ELSE 0 END) AS violation_p3,
                SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 1 THEN tbl_kpi_list_criteria_department.weight ELSE 0 END) AS weight_p1,
                SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 2 THEN tbl_kpi_list_criteria_department.weight ELSE 0 END) AS weight_p2,
                SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 3 THEN tbl_kpi_list_criteria_department.weight ELSE 0 END) AS weight_p3
            FROM tblproduction_report
            LEFT JOIN tbl_kpi_list_criteria_department
            ON tbl_kpi_list_criteria_department.id =
               COALESCE(
                   NULLIF(tblproduction_report.kpi_list_criteria_department_id_childd, 0),
                   tblproduction_report.kpi_list_criteria_department_id_child
               )
            WHERE tblproduction_report.id != 0  $whereDate
            GROUP BY staff_responsible
        ) tb_tamp_report";

        $tb_tamp_report_process = "(
             SELECT
                tblproduction_report.staff_responsible as staff_id,
                COUNT(tblproduction_report.id) as count_bckph
            FROM tblproduction_report
            LEFT JOIN tbl_kpi_list_criteria_department
            ON tbl_kpi_list_criteria_department.id =
               COALESCE(
                   NULLIF(tblproduction_report.kpi_list_criteria_department_id_childd, 0),
                   tblproduction_report.kpi_list_criteria_department_id_child
               )
            WHERE tblproduction_report.id != 0 AND EXISTS (
                SELECT 1 
                FROM tbl_process_production_report 
                WHERE tbl_process_production_report.production_report_id = tblproduction_report.id
                AND tbl_process_production_report.staff_process = 0
             )  $whereDate
            GROUP BY staff_responsible
        ) tb_tamp_report_process";


        $tb_tamp_old = "(
            SELECT 
                tblproduction_report.staff_responsible as staff_id,
                GROUP_CONCAT((tblproduction_report.kpi_list_criteria_department_id) SEPARATOR ',') as kpi_list_criteria_department_id,
                COUNT(tblproduction_report.id) as violate
            FROM tblproduction_report
            WHERE kpi_list_criteria_department_id != 0 AND tblproduction_report.violate = 1
            $whereDateOld
            GROUP BY tblproduction_report.staff_responsible
        ) tb_tamp_old";

        $tb_tamp_report_old = "(
             SELECT
                tblproduction_report.staff_responsible as staff_id,
                COUNT(tblproduction_report.id) as count_bckph,
                SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 1 THEN 1 ELSE 0 END) AS violation_p1,
                SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 2 THEN 1 ELSE 0 END) AS violation_p2,
                SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 3 THEN 1 ELSE 0 END) AS violation_p3
            FROM tblproduction_report
            LEFT JOIN tbl_kpi_list_criteria_department
            ON tbl_kpi_list_criteria_department.id =
               COALESCE(
                   NULLIF(tblproduction_report.kpi_list_criteria_department_id_childd, 0),
                   tblproduction_report.kpi_list_criteria_department_id_child
               )
            WHERE tblproduction_report.id != 0  $whereDateOld
            GROUP BY staff_responsible
        ) tb_tamp_report_old";

        $this->db->select('
            tblstaff.staffid as staffid,
            CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname,
            COALESCE(tb_tamp_task.total_task,0) as total_task,
            COALESCE(tb_tamp_report_old.count_bckph,0) as count_bckph_old,
            COALESCE(tb_tamp_report.count_bckph,0) as count_bckph,
            COALESCE(tb_tamp_old.violate,0) as violate_old,
            COALESCE(tb_tamp.violate,0) as violate,
            COALESCE(tb_tamp_vuot.vuot,0) as vuot,
            COALESCE(tb_tamp_report.violation_p1,0) as violation_p1,
            COALESCE(tb_tamp_report.violation_p2,0) as violation_p2,
            COALESCE(tb_tamp_report.violation_p3,0) as violation_p3,
            tb_tamp.kpi_list_criteria_department_id,
            tb_tamp_vuot.kpi_list_criteria_department_id as kpi_list_criteria_department_id_vuot,
            "" as rating,
            COALESCE(tb_tamp_report.weight_p2,0) as weight_p2,
            COALESCE(tb_tamp_report.weight_p3,0) as weight_p3,
            COALESCE(tb_tamp_task_process.total_task,0) as total_task_process,
            COALESCE(tb_tamp_report_process.count_bckph,0) as count_bckph_process,
            COALESCE(tb_tamp_audit.total_audit,0) as total_audit
        ');
        $this->db->from('tblstaff');
        //        $this->db->where('EXISTS (
        //            SELECT 1
        //            FROM tblproduction_report
        //            WHERE tblproduction_report.staff_responsible = tblstaff.staffid AND tblproduction_report.violate = 1
        //            AND kpi_list_criteria_department_id != 0 AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") = "'.$month_year.'"
        //        )');
        $this->db->where('active', 1);
        if (!empty($staff)) {
            $this->db->where('tblstaff.staffid', $staff);
        }
        if (!empty($department_search)) {
            $this->db->where('EXISTS (
                SELECT 1
                FROM tblstaff_departments
                WHERE tblstaff_departments.staffid = tblstaff.staffid
                AND tblstaff_departments.departmentid = ' . $department_search . '
            )');
        }
        $this->db->join($tb_tamp, 'tblstaff.staffid = tb_tamp.staff_id', 'left');
        $this->db->join($tb_tamp_vuot, 'tblstaff.staffid = tb_tamp_vuot.staff_id', 'left');
        $this->db->join($tb_tamp_report, 'tblstaff.staffid = tb_tamp_report.staff_id', 'left');
        $this->db->join($tb_tamp_old, 'tblstaff.staffid = tb_tamp_old.staff_id', 'left');
        $this->db->join($tb_tamp_report_old, 'tblstaff.staffid = tb_tamp_report_old.staff_id', 'left');
        $this->db->join($tb_tamp_task, 'tblstaff.staffid = tb_tamp_task.staff_id', 'left');
        $this->db->join($tb_tamp_task_process, 'tblstaff.staffid = tb_tamp_task_process.staff_id', 'left');
        $this->db->join($tb_tamp_report_process, 'tblstaff.staffid = tb_tamp_report_process.staff_id', 'left');
        $this->db->join($tb_tamp_audit, 'tblstaff.staffid = tb_tamp_audit.staff_id', 'left');
        $dtStaff = $this->db->get()->result_array();

        $dtCriteriaDepartmentViolateNew = [];
        $dtCriteriaDepartmentViolateNewVuot = [];
        if (!empty($dtStaff)) {
            foreach ($dtStaff as $key => $value) {
                $staffid = $value['staffid'];
                $kpi_list_criteria_department_id_db = $value['kpi_list_criteria_department_id'];
                $kpi_list_criteria_department_id = explode(',', $kpi_list_criteria_department_id_db);
                if (!empty($kpi_list_criteria_department_id)) {
                    $this->db->where_in('kpi_list_criteria_department_id', $kpi_list_criteria_department_id);
                    $this->db->from('tbl_kpi_list_criteria_department_violate');
                    $dtCriteriaDepartmentViolate = $this->db->get()->result_array();
                }
                if (!empty($dtCriteriaDepartmentViolate)) {
                    $dtCriteriaDepartmentViolate = array_reduce($dtCriteriaDepartmentViolate, function ($carry, $item) {
                        $carry[$item['kpi_list_criteria_department_id']][] = $item;
                        return $carry;
                    });
                }
                $dtCriteriaDepartmentViolateNew[$staffid] = $dtCriteriaDepartmentViolate;


                $kpi_list_criteria_department_id_db_vuot = $value['kpi_list_criteria_department_id_vuot'];
                $kpi_list_criteria_department_id_vuot = explode(',', $kpi_list_criteria_department_id_db_vuot);
                if (!empty($kpi_list_criteria_department_id_vuot)) {
                    $this->db->where_in('kpi_list_criteria_department_id', $kpi_list_criteria_department_id_vuot);
                    $this->db->from('tbl_kpi_list_criteria_department_violate');
                    $dtCriteriaDepartmentViolateVuot = $this->db->get()->result_array();
                }
                if (!empty($dtCriteriaDepartmentViolateVuot)) {
                    $dtCriteriaDepartmentViolateVuot = array_reduce($dtCriteriaDepartmentViolateVuot, function ($carry, $item) {
                        $carry[$item['kpi_list_criteria_department_id']][] = $item;
                        return $carry;
                    });
                }
                $dtCriteriaDepartmentViolateNewVuot[$staffid] = $dtCriteriaDepartmentViolateVuot;
            }
        }
        $html = '';
        if (!empty($dtStaff)) {
            foreach ($dtStaff as $key => $value) {
                $pointMax = 100;
                $kpi_list_criteria_department_id_db = $value['kpi_list_criteria_department_id'];
                $kpi_list_criteria_department_id = explode(',', $kpi_list_criteria_department_id_db);
                $countedArray = [];
                if (!empty($kpi_list_criteria_department_id[0])) {
                    $countedArray = array_count_values($kpi_list_criteria_department_id);
                }
                $dtCriteriaDepartmentViolate = !empty($dtCriteriaDepartmentViolateNew[$value['staffid']]) ? $dtCriteriaDepartmentViolateNew[$value['staffid']] : [];
                $point = 0;
                if (!empty($countedArray)) {
                    foreach ($countedArray as $k => $v) {
                        $dtData = !empty($dtCriteriaDepartmentViolate[$k]) ? $dtCriteriaDepartmentViolate[$k] : [];
                        $violations = array_column($dtData, 'violations');
                        $violationsToPoint = [];
                        if (!empty($dtData)) {
                            foreach ($dtData as $item) {
                                $violationsToPoint[$item['violations']] = $item['point'];
                            }
                        }
                        $maxViolations = max($violations);
                        if ($v <= $maxViolations) {
                            if (array_key_exists($v, $violationsToPoint)) {
                                if ($violationsToPoint[$v] == -1) {
                                    $point += $violationsToPoint[$v - 1];
                                } else {
                                    $point += $violationsToPoint[$v];
                                }
                            }
                        } else {
                            $point += $violationsToPoint[$maxViolations - 1];
                        }
                    }
                }

                //vuot
                $pointNew = 0;
                $kpi_list_criteria_department_id_db_vuot = $value['kpi_list_criteria_department_id_vuot'];
                $kpi_list_criteria_department_id_vuot = explode(',', $kpi_list_criteria_department_id_db_vuot);
                $countedArrayVuot = [];
                if (!empty($kpi_list_criteria_department_id_vuot[0])) {
                    $countedArrayVuot = array_count_values($kpi_list_criteria_department_id_vuot);
                }
                $dtCriteriaDepartmentViolateVuot = !empty($dtCriteriaDepartmentViolateNewVuot[$value['staffid']]) ? $dtCriteriaDepartmentViolateNewVuot[$value['staffid']] : [];

                if (!empty($countedArrayVuot)) {
                    foreach ($countedArrayVuot as $k => $v) {
                        $dtData = !empty($dtCriteriaDepartmentViolateVuot[$k]) ? $dtCriteriaDepartmentViolateVuot[$k] : [];
                        $violations = array_column($dtData, 'violations');
                        $violationsToPoint = [];
                        if (!empty($dtData)) {
                            foreach ($dtData as $item) {
                                $violationsToPoint[$item['violations']] = $item['point_new'];
                            }
                        }
                        $maxViolations = max($violations);
                        if ($v < $maxViolations) {
                            if (array_key_exists($v, $violationsToPoint)) {
                                $pointNew += $violationsToPoint[$v];
                            }
                        } else {
                            $pointNew += $violationsToPoint[$maxViolations - 1];
                        }
                    }
                }


                $pointCurrent = $pointMax - $point + $pointNew;
                if ($pointCurrent <= 0) {
                    $pointCurrent = 1;
                }
                if ($pointCurrent > 100) {
                    $pointCurrent = 100;
                }


                $this->db->from('tbl_decision_bonus_discipline');
                $this->db->where('tbl_decision_bonus_discipline.type_quota_bonus_discipline_id', 1);
                $this->db->where('tbl_decision_bonus_discipline.object_id', $value['staffid']);
                $this->db->where('tbl_decision_bonus_discipline.object_type = "staff" ' . $whereDateDecision . '');
                $dtCountDecision = $this->db->count_all_results();

                $dtRating = ratingKpiDepartment($pointCurrent);

                //phải có quyết định khen thưởng thì mới xuất sắc
                if (!empty($dtRating)) {
                    if ($dtRating[0]['id'] == 1 && empty($dtCountDecision)) {
                        $dtRating = ratingKpiDepartment(-1, 2);
                    }
                }

                $bonus = !empty($dtRating) ? $dtRating[0]['bonus'] : [];
                $discipline = !empty($dtRating) ? $dtRating[0]['discipline'] : [];
                $htmlBouns = '';
                $htmlDiscipline = '';
                if (!empty($bonus)) {
                    foreach ($bonus as $k => $v) {
                        $htmlBouns .= '<div>' . $v['name'] . '</div>';
                    }
                }
                if (!empty($discipline)) {
                    foreach ($discipline as $k => $v) {
                        $htmlDiscipline .= '<div>' . $v['name'] . '</div>';
                    }
                }
                $avatar = '<a href="' . admin_url('staff/profile/' . $value['staffid']) . '">' . staff_profile_image(
                    $value['staffid'],
                    [
                        'staff-profile-image-small',
                    ]
                ) . '</a>';
                $check_p3 = 'Không';
                if ($value['total_task_process'] == 0 && $value['count_bckph_process'] == 0 && $value['violate'] == 0 && $value['total_audit'] == 0) {
                    $check_p3 = 'Có';
                }
                $html .= '<tr>
                     <td class="text-center stt_all">' . (++$key) . '</td>
                     <td>' . $avatar . '<a href="' . base_url('admin/kpi/view_kpi_evaluation_staff/' . $value['staffid'] . '/' . $month . '/' . $year . '/' . $precious) . '" class="tnh-modal">' . $value['fullname'] . '</a></td>
                     <td class="text-center" style="font-weight: bold;font-size: 15px">' . ($value['total_task'] != 0 ? $value['total_task'] : '-') . '</td>
                     <td class="text-center" style="font-weight: bold;font-size: 15px">' . ($value['count_bckph_old'] != 0 ? $value['count_bckph_old'] : '-') . '</td>
                     <td class="text-center" style="font-weight: bold;font-size: 15px">' . ($value['count_bckph'] != 0 ? $value['count_bckph'] : '-') . '</td>
                     <td class="text-center" style="font-weight: bold;font-size: 15px">' . ($value['violate_old'] != 0 ? $value['violate_old'] : '-') . '</td>
                     <td class="text-center" style="font-weight: bold;font-size: 15px"><a href="' . base_url('admin/kpi/view_detail_production_report/' . $value['staffid'] . '/' . $month . '/' . $year . '/' . $precious) . '" class="tnh-modal">' . $value['violate'] . '</a>
                        <input type="hidden" class="violate_input" value="' . $value['violate'] . '">
                     </td>
                      <td class="text-center" style="font-weight: bold;font-size: 15px"><a href="' . base_url('admin/kpi/view_detail_production_report_vuot/' . $value['staffid'] . '/' . $month . '/' . $year . '/' . $precious) . '" class="tnh-modal">' . $value['vuot'] . '</a>
                        <input type="hidden" class="vuot_input" value="' . $value['vuot'] . '">
                     </td>
                     <td class="text-center" style="font-weight: bold;font-size: 15px">' . ($value['violation_p1'] != 0 ? $value['violation_p1'] : '-') . '</td>
                     <td class="text-center" style="font-weight: bold;font-size: 15px">' . ($value['violation_p2'] != 0 ? $value['violation_p2'] : '-') . '</td>
                     <td class="text-center" style="font-weight: bold;font-size: 15px">' . ($value['violation_p3'] != 0 ? $value['violation_p3'] : '-') . '</td>
                     <td class="text-center" style="font-weight: bold;font-size: 15px">' . $pointCurrent . '</td>
                     <td class="text-center" style="background-color: ' . (!empty($dtRating) ? $dtRating[0]['color'] : '') . '">' . (!empty($dtRating) ? $dtRating[0]['title'] : '') . '</td>
                     <td class="text-left">
                          <div style="">
                            <div class="content-text">
                                <div>Thưởng : ' . $htmlBouns . '</div>
                                <div>Kỷ luật : ' . $htmlDiscipline . '</div>
                            </div>
                            <a onclick="toggleContent(this)">Xem thêm</a>
                          </div>
                    </td>
                    <td class="text-center" style="background-color: #3d69ef;color: white"><a href="' . base_url('admin/kpi/view_detail_p2/' . $value['staffid'] . '/' . $month . '/' . $year . '/' . $precious) . '" class="tnh-modal" style="color:white">' . ((100 - $value['weight_p2']) > 0 ? (100 - $value['weight_p2']) . ' %' : '-') . '</a></td>
                    <td class="text-center" style="background-color: #047857;"><a href="' . base_url('admin/kpi/view_detail_p3/' . $value['staffid'] . '/' . $month . '/' . $year . '/' . $precious) . '" class="tnh-modal" style="color:white">' . $check_p3 . '</a></td>
                </tr>';
            }
        }
        $data['html'] = $html;
        echo json_encode($data);
    }

    /**
     * Export Excel KPI Evaluation
     * Xuat bao cao danh gia KPI ra file Excel
     */
    public function export_excel_kpi_evaluation()
    {
        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        // Lay tham so loc
        $month = $this->input->post('filter_month');
        $year = $this->input->post('year');
        $staff = $this->input->post('staff');
        $department_search = $this->input->post('department_search');
        $precious = $this->input->post('precious');

        $month_year = $year . '-' . $month;
        $month_year_start = null;
        $month_year_end = null;

        if (!empty($precious)) {
            if ($precious == 1) {
                $month_year_start = $year . '-01';
                $month_year_end = $year . '-03';
            } elseif ($precious == 2) {
                $month_year_start = $year . '-04';
                $month_year_end = $year . '-06';
            } elseif ($precious == 3) {
                $month_year_start = $year . '-07';
                $month_year_end = $year . '-09';
            } elseif ($precious == 4) {
                $month_year_start = $year . '-10';
                $month_year_end = $year . '-12';
            }
        }

        $whereDate = '';
        $whereDateTask = '';
        $whereDateOld = '';
        $whereDateTaskOld = '';
        $whereDateDecision = '';
        $whereDateAudit = '';

        if (!empty($month_year_start)) {
            $whereDate = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") <= "' . $month_year_end . '"';
            $whereDateTask = 'AND DATE_FORMAT(tbltasks.dateadded, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tbltasks.dateadded, "%Y-%m") <= "' . $month_year_end . '"';
            $whereDateOld = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") < "' . $month_year_start . '"';
            $whereDateTaskOld = 'AND DATE_FORMAT(tbltasks.dateadded, "%Y-%m") < "' . $month_year_start . '"';
            $whereDateDecision = 'AND DATE_FORMAT(tbl_decision_bonus_discipline.date, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tbl_decision_bonus_discipline.date, "%Y-%m") <= "' . $month_year_end . '"';
            $whereDateAudit = 'AND DATE_FORMAT(tbl_audit.audit_date, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tbl_audit.audit_date, "%Y-%m") <= "' . $month_year_end . '"';
        } else {
            $whereDate = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") = "' . $month_year . '"';
            $whereDateTask = 'AND DATE_FORMAT(tbltasks.dateadded, "%Y-%m") = "' . $month_year . '"';
            $whereDateOld = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") < "' . $month_year . '"';
            $whereDateTaskOld = 'AND DATE_FORMAT(tbltasks.dateadded, "%Y-%m") < "' . $month_year . '"';
            $whereDateDecision = 'AND DATE_FORMAT(tbl_decision_bonus_discipline.date, "%Y-%m") = "' . $month_year . '"';
            $whereDateAudit = 'AND DATE_FORMAT(tbl_audit.audit_date, "%Y-%m") = "' . $month_year . '"';
        }

        // Subquery: so lan audit co checklist khong dat
        $tb_tamp_audit = "(
            SELECT
                tblstaff_departments.staffid as staff_id,
                COUNT(tbl_audit.id) as total_audit
            FROM tbl_audit
            JOIN tbl_room ON tbl_room.id = tbl_audit.dept_id
            JOIN tbldepartments ON tbldepartments.room_id = tbl_room.id
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            WHERE EXISTS (
                SELECT 1
                FROM tbl_audit_checklist
                WHERE tbl_audit_checklist.audit_id = tbl_audit.id
                AND tbl_audit_checklist.status = 'no'
            )
            $whereDateAudit
            GROUP BY tblstaff_departments.staffid
        ) tb_tamp_audit";

        // Subquery: tong so cong viec (chua hoan thanh)
        $tb_tamp_task_process = "(
            SELECT
                tbltask_assigned.staffid as staff_id,
                COUNT(tbltasks.id) as total_task
            FROM tbltasks
            JOIN tbltask_assigned ON tbltask_assigned.taskid = tbltasks.id
            WHERE tbltasks.id != 0 AND tbltasks.status != 5 $whereDateTask
            GROUP BY tbltask_assigned.staffid
        ) tb_tamp_task_process";

        // Subquery: tong so cong viec (tat ca)
        $tb_tamp_task = "(
            SELECT
                tbltask_assigned.staffid as staff_id,
                COUNT(tbltasks.id) as total_task
            FROM tbltasks
            JOIN tbltask_assigned ON tbltask_assigned.taskid = tbltasks.id
            WHERE tbltasks.id != 0 $whereDateTask
            GROUP BY tbltask_assigned.staffid
        ) tb_tamp_task";

        // Subquery: so phieu vi pham (hien tai)
        $tb_tamp = "(
            SELECT
                tblproduction_report.staff_responsible as staff_id,
                GROUP_CONCAT((tblproduction_report.kpi_list_criteria_department_id) SEPARATOR ',') as kpi_list_criteria_department_id,
                COUNT(tblproduction_report.id) as violate
            FROM tblproduction_report
            WHERE kpi_list_criteria_department_id != 0 AND tblproduction_report.violate = 1
            $whereDate
            GROUP BY tblproduction_report.staff_responsible
        ) tb_tamp";

        // Subquery: so phieu vuot (hien tai)
        $tb_tamp_vuot = "(
            SELECT
                tblproduction_report.staff_responsible as staff_id,
                GROUP_CONCAT((tblproduction_report.kpi_list_criteria_department_id) SEPARATOR ',') as kpi_list_criteria_department_id,
                COUNT(tblproduction_report.id) as vuot
            FROM tblproduction_report
            WHERE tblproduction_report.type_report = 2 AND kpi_list_criteria_department_id != 0
            $whereDate
            GROUP BY tblproduction_report.staff_responsible
        ) tb_tamp_vuot";

        // Subquery: thong ke phieu theo loai vi pham
        $tb_tamp_report = "(
            SELECT
               tblproduction_report.staff_responsible as staff_id,
               COUNT(tblproduction_report.id) as count_bckph,
               SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 1 THEN 1 ELSE 0 END) AS violation_p1,
               SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 2 THEN 1 ELSE 0 END) AS violation_p2,
               SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 3 THEN 1 ELSE 0 END) AS violation_p3,
               SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 1 THEN tbl_kpi_list_criteria_department.weight ELSE 0 END) AS weight_p1,
               SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 2 THEN tbl_kpi_list_criteria_department.weight ELSE 0 END) AS weight_p2,
               SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 3 THEN tbl_kpi_list_criteria_department.weight ELSE 0 END) AS weight_p3
            FROM tblproduction_report
            LEFT JOIN tbl_kpi_list_criteria_department
            ON tbl_kpi_list_criteria_department.id =
               COALESCE(
                   NULLIF(tblproduction_report.kpi_list_criteria_department_id_childd, 0),
                   tblproduction_report.kpi_list_criteria_department_id_child
               )
            WHERE tblproduction_report.id != 0  $whereDate
            GROUP BY staff_responsible
        ) tb_tamp_report";

        // Subquery: phieu chua xu ly
        $tb_tamp_report_process = "(
            SELECT
               tblproduction_report.staff_responsible as staff_id,
               COUNT(tblproduction_report.id) as count_bckph
            FROM tblproduction_report
            LEFT JOIN tbl_kpi_list_criteria_department
            ON tbl_kpi_list_criteria_department.id =
               COALESCE(
                   NULLIF(tblproduction_report.kpi_list_criteria_department_id_childd, 0),
                   tblproduction_report.kpi_list_criteria_department_id_child
               )
            WHERE tblproduction_report.id != 0 AND EXISTS (
                SELECT 1
                FROM tbl_process_production_report
                WHERE tbl_process_production_report.production_report_id = tblproduction_report.id
                AND tbl_process_production_report.staff_process = 0
             )  $whereDate
            GROUP BY staff_responsible
        ) tb_tamp_report_process";

        // Subquery: so phieu vi pham (ky truoc)
        $tb_tamp_old = "(
            SELECT
                tblproduction_report.staff_responsible as staff_id,
                GROUP_CONCAT((tblproduction_report.kpi_list_criteria_department_id) SEPARATOR ',') as kpi_list_criteria_department_id,
                COUNT(tblproduction_report.id) as violate
            FROM tblproduction_report
            WHERE kpi_list_criteria_department_id != 0 AND tblproduction_report.violate = 1
            $whereDateOld
            GROUP BY tblproduction_report.staff_responsible
        ) tb_tamp_old";

        // Subquery: thong ke phieu ky truoc
        $tb_tamp_report_old = "(
            SELECT
               tblproduction_report.staff_responsible as staff_id,
               COUNT(tblproduction_report.id) as count_bckph,
               SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 1 THEN 1 ELSE 0 END) AS violation_p1,
               SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 2 THEN 1 ELSE 0 END) AS violation_p2,
               SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 3 THEN 1 ELSE 0 END) AS violation_p3
            FROM tblproduction_report
            LEFT JOIN tbl_kpi_list_criteria_department
            ON tbl_kpi_list_criteria_department.id =
               COALESCE(
                   NULLIF(tblproduction_report.kpi_list_criteria_department_id_childd, 0),
                   tblproduction_report.kpi_list_criteria_department_id_child
               )
            WHERE tblproduction_report.id != 0  $whereDateOld
            GROUP BY staff_responsible
        ) tb_tamp_report_old";

        // Truy van chinh lay du lieu nhan vien
        $this->db->select('
            tblstaff.staffid as staffid,
            CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname,
            tblroles.name as position_name,
            GROUP_CONCAT(DISTINCT tbldepartments.name SEPARATOR ", ") as department_name,
            COALESCE(tb_tamp_task.total_task,0) as total_task,
            COALESCE(tb_tamp_report_old.count_bckph,0) as count_bckph_old,
            COALESCE(tb_tamp_report.count_bckph,0) as count_bckph,
            COALESCE(tb_tamp_old.violate,0) as violate_old,
            COALESCE(tb_tamp.violate,0) as violate,
            COALESCE(tb_tamp_vuot.vuot,0) as vuot,
            COALESCE(tb_tamp_report.violation_p1,0) as violation_p1,
            COALESCE(tb_tamp_report.violation_p2,0) as violation_p2,
            COALESCE(tb_tamp_report.violation_p3,0) as violation_p3,
            tb_tamp.kpi_list_criteria_department_id,
            tb_tamp_vuot.kpi_list_criteria_department_id as kpi_list_criteria_department_id_vuot,
            "" as rating,
            COALESCE(tb_tamp_report.weight_p2,0) as weight_p2,
            COALESCE(tb_tamp_report.weight_p3,0) as weight_p3,
            COALESCE(tb_tamp_task_process.total_task,0) as total_task_process,
            COALESCE(tb_tamp_report_process.count_bckph,0) as count_bckph_process,
            COALESCE(tb_tamp_audit.total_audit,0) as total_audit
        ');
        $this->db->from('tblstaff');
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
        $this->db->join('tblstaff_departments', 'tblstaff_departments.staffid = tblstaff.staffid', 'left');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tblstaff_departments.departmentid', 'left');
        $this->db->group_by('tblstaff.staffid');
        $this->db->where('active', 1);
        if (!empty($staff)) {
            $this->db->where('tblstaff.staffid', $staff);
        }
        if (!empty($department_search)) {
            $this->db->where('tblstaff_departments.departmentid', $department_search);
        }
        $this->db->join($tb_tamp, 'tblstaff.staffid = tb_tamp.staff_id', 'left');
        $this->db->join($tb_tamp_vuot, 'tblstaff.staffid = tb_tamp_vuot.staff_id', 'left');
        $this->db->join($tb_tamp_report, 'tblstaff.staffid = tb_tamp_report.staff_id', 'left');
        $this->db->join($tb_tamp_old, 'tblstaff.staffid = tb_tamp_old.staff_id', 'left');
        $this->db->join($tb_tamp_report_old, 'tblstaff.staffid = tb_tamp_report_old.staff_id', 'left');
        $this->db->join($tb_tamp_task, 'tblstaff.staffid = tb_tamp_task.staff_id', 'left');
        $this->db->join($tb_tamp_task_process, 'tblstaff.staffid = tb_tamp_task_process.staff_id', 'left');
        $this->db->join($tb_tamp_report_process, 'tblstaff.staffid = tb_tamp_report_process.staff_id', 'left');
        $this->db->join($tb_tamp_audit, 'tblstaff.staffid = tb_tamp_audit.staff_id', 'left');
        $dtStaff = $this->db->get()->result_array();

        // Lay du lieu vi pham KPI
        $dtCriteriaDepartmentViolateNew = [];
        $dtCriteriaDepartmentViolateNewVuot = [];
        if (!empty($dtStaff)) {
            foreach ($dtStaff as $key => $value) {
                $staffid = $value['staffid'];
                $kpi_list_criteria_department_id_db = $value['kpi_list_criteria_department_id'];
                $kpi_list_criteria_department_id = explode(',', $kpi_list_criteria_department_id_db);
                if (!empty($kpi_list_criteria_department_id)) {
                    $this->db->where_in('kpi_list_criteria_department_id', $kpi_list_criteria_department_id);
                    $this->db->from('tbl_kpi_list_criteria_department_violate');
                    $dtCriteriaDepartmentViolate = $this->db->get()->result_array();
                }
                if (!empty($dtCriteriaDepartmentViolate)) {
                    $dtCriteriaDepartmentViolate = array_reduce($dtCriteriaDepartmentViolate, function ($carry, $item) {
                        $carry[$item['kpi_list_criteria_department_id']][] = $item;
                        return $carry;
                    });
                }
                $dtCriteriaDepartmentViolateNew[$staffid] = $dtCriteriaDepartmentViolate;

                // Vuot
                $kpi_list_criteria_department_id_db_vuot = $value['kpi_list_criteria_department_id_vuot'];
                $kpi_list_criteria_department_id_vuot = explode(',', $kpi_list_criteria_department_id_db_vuot);
                if (!empty($kpi_list_criteria_department_id_vuot)) {
                    $this->db->where_in('kpi_list_criteria_department_id', $kpi_list_criteria_department_id_vuot);
                    $this->db->from('tbl_kpi_list_criteria_department_violate');
                    $dtCriteriaDepartmentViolateVuot = $this->db->get()->result_array();
                }
                if (!empty($dtCriteriaDepartmentViolateVuot)) {
                    $dtCriteriaDepartmentViolateVuot = array_reduce($dtCriteriaDepartmentViolateVuot, function ($carry, $item) {
                        $carry[$item['kpi_list_criteria_department_id']][] = $item;
                        return $carry;
                    });
                }
                $dtCriteriaDepartmentViolateNewVuot[$staffid] = $dtCriteriaDepartmentViolateVuot;
            }
        }

        // === Bat dau tao Excel ===
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        // Font mac dinh
        $objPHPExcel->getDefaultStyle()->applyFromArray([
            'font' => [
                'name' => 'Times New Roman'
            ],
        ]);

        // Tieu de bao cao
        $periodLabel = !empty($month_year_start) ? ('Quý ' . $precious . ' Năm ' . $year) : ('Tháng ' . $month . ' Năm ' . $year);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'BÁO CÁO ĐÁNH GIÁ KPI - ' . strtoupper($periodLabel));
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
            ],
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ]
        ]);
        $objPHPExcel->getActiveSheet()->mergeCells('A1:R1');
        $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(30);

        // Header row
        $headers = [
            'STT',
            'Phòng ban',
            'Vị trí',
            'Nhân viên',
            'Tổng công việc',
            'Số BCKPH kỳ trước',
            'Số BCKPH kỳ này',
            'Vi phạm kỳ trước',
            'Vi phạm kỳ này',
            'Vượt mức',
            'Vi phạm P1',
            'Vi phạm P2',
            'Vi phạm P3',
            'Điểm KPI',
            'Xếp loại',
            'Kết quả (Thưởng/Kỷ luật)',
            'P2 (%)',
            'P3',
        ];

        $colWidths = [6, 25, 25, 20, 12, 14, 14, 14, 25, 25, 10, 10, 10, 10, 14, 30, 10, 8];

        $sttRow = 3;
        $colIndex = 'A';
        foreach ($headers as $idx => $header) {
            $col = chr(65 + $idx); // A, B, C, ...
            $objPHPExcel->getActiveSheet()->setCellValue($col . $sttRow, $header);
            $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth($colWidths[$idx]);
        }
        $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:R$sttRow")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 10,
                'name' => 'Times New Roman'
            ],
            'borders' => [
                'allborders' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                ]
            ],
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'fill' => [
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => '92D050'],
            ],
        ]);
        // Cột Phòng ban, Vị trí căn trái + wrap text
        $objPHPExcel->getActiveSheet()->getStyle("B$sttRow")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle("C$sttRow")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getRowDimension($sttRow)->setRowHeight(35);

        // Data rows
        $rowNum = $sttRow + 1;
        $stt = 1;

        foreach ($dtStaff as $value) {
            // --- Chi tiet phieu vi pham ---
            $violate_detail = [];
            if ($value['violate'] > 0) {
                $this->db->select('
                    tblproduction_report.id,
                    tblproduction_report.reference_no,
                    tblproduction_report.date,
                    tblproduction_report.name_report,
                    tblproduction_report.kpi_list_criteria_department_violate,
                    tbl_kpi_dept_main.name as kpi_name,
                    tbl_kpi_dept_child.evaluation_criteria as kpi_child_criteria,
                    tbl_kpi_dept_child.name as kpi_child_name,
                    tbl_kpi_dept_childd.evaluation_criteria as kpi_grand_criteria
                ');
                $this->db->from('tblproduction_report');
                $this->db->join('tbl_kpi_list_criteria_department as tbl_kpi_dept_main', 'tbl_kpi_dept_main.id = tblproduction_report.kpi_list_criteria_department_id', 'left');
                $this->db->join('tbl_kpi_list_criteria_department as tbl_kpi_dept_child', 'tbl_kpi_dept_child.id = tblproduction_report.kpi_list_criteria_department_id_child', 'left');
                $this->db->join('tbl_kpi_list_criteria_department as tbl_kpi_dept_childd', 'tbl_kpi_dept_childd.id = tblproduction_report.kpi_list_criteria_department_id_childd', 'left');
                $this->db->where('tblproduction_report.staff_responsible', $value['staffid']);
                $this->db->where('tblproduction_report.kpi_list_criteria_department_id !=', 0);
                $this->db->where('tblproduction_report.violate', 1);
                $this->db->where("DATE_FORMAT(tblproduction_report.date, '%Y-%m') " . (!empty($month_year_start) ? "BETWEEN '$month_year_start' AND '$month_year_end'" : "= '$month_year'"), NULL, FALSE);
                $this->db->order_by('tblproduction_report.date', 'desc');
                $lstViolate = $this->db->get()->result_array();
                foreach ($lstViolate as $vp) {
                    $kpiInfo = '';
                    if (!empty($vp['kpi_name'])) {
                        $kpiInfo .= '+' . $vp['kpi_name'];
                    }
                    if (!empty($vp['kpi_child_criteria'])) {
                        $kpiInfo .= ' - ' . $vp['kpi_child_criteria'];
                    } elseif (!empty($vp['kpi_child_name'])) {
                        $kpiInfo .= ' - ' . $vp['kpi_child_name'];
                    }
                    if (!empty($vp['kpi_grand_criteria'])) {
                        $kpiInfo .= ' - ' . $vp['kpi_grand_criteria'];
                    }
                    $violateText = !empty($vp['kpi_list_criteria_department_violate']) ? $vp['kpi_list_criteria_department_violate'] : '';
                    $violate_detail[] = $vp['reference_no'] . ' ' . $kpiInfo . (!empty($violateText) ? ' [' . $violateText . ']' : '');
                }
            }

            // --- Chi tiet phieu vuot ---
            $vuot_detail = [];
            if ($value['vuot'] > 0) {
                $this->db->select('
                    tblproduction_report.id,
                    tblproduction_report.reference_no,
                    tblproduction_report.date,
                    tblproduction_report.name_report,
                    tblproduction_report.point_kpi,
                    tblproduction_report.kpi_list_criteria_department_violate,
                    tbl_kpi_dept_main.name as kpi_name,
                    tbl_kpi_dept_child.evaluation_criteria as kpi_child_criteria,
                    tbl_kpi_dept_child.name as kpi_child_name,
                    tbl_kpi_dept_childd.evaluation_criteria as kpi_grand_criteria
                ');
                $this->db->from('tblproduction_report');
                $this->db->join('tbl_kpi_list_criteria_department as tbl_kpi_dept_main', 'tbl_kpi_dept_main.id = tblproduction_report.kpi_list_criteria_department_id', 'left');
                $this->db->join('tbl_kpi_list_criteria_department as tbl_kpi_dept_child', 'tbl_kpi_dept_child.id = tblproduction_report.kpi_list_criteria_department_id_child', 'left');
                $this->db->join('tbl_kpi_list_criteria_department as tbl_kpi_dept_childd', 'tbl_kpi_dept_childd.id = tblproduction_report.kpi_list_criteria_department_id_childd', 'left');
                $this->db->where('tblproduction_report.staff_responsible', $value['staffid']);
                $this->db->where('tblproduction_report.kpi_list_criteria_department_id !=', 0);
                $this->db->where('tblproduction_report.type_report', 2);
                $this->db->where("DATE_FORMAT(tblproduction_report.date, '%Y-%m') " . (!empty($month_year_start) ? "BETWEEN '$month_year_start' AND '$month_year_end'" : "= '$month_year'"), NULL, FALSE);
                $this->db->order_by('tblproduction_report.date', 'desc');
                $lstVuot = $this->db->get()->result_array();
                foreach ($lstVuot as $vt) {
                    $kpiInfo = '';
                    if (!empty($vt['kpi_name'])) {
                        $kpiInfo .= '+' . $vt['kpi_name'];
                    }
                    if (!empty($vt['kpi_child_criteria'])) {
                        $kpiInfo .= ' - ' . $vt['kpi_child_criteria'];
                    } elseif (!empty($vt['kpi_child_name'])) {
                        $kpiInfo .= ' - ' . $vt['kpi_child_name'];
                    }
                    if (!empty($vt['kpi_grand_criteria'])) {
                        $kpiInfo .= ' - ' . $vt['kpi_grand_criteria'];
                    }
                    $vuot_detail[] = $vt['reference_no'] . ' ' . $kpiInfo . (!empty($vt['kpi_list_criteria_department_violate']) ? ' [' . $vt['kpi_list_criteria_department_violate'] . ']' : '');
                }
            }

            // Tinh diem KPI (cung logic nhu view_kpi_evaluation)
            $pointMax = 100;
            $kpi_list_criteria_department_id_db = $value['kpi_list_criteria_department_id'];
            $kpi_list_criteria_department_id = explode(',', $kpi_list_criteria_department_id_db);
            $countedArray = [];
            if (!empty($kpi_list_criteria_department_id[0])) {
                $countedArray = array_count_values($kpi_list_criteria_department_id);
            }
            $dtCriteriaDepartmentViolate = !empty($dtCriteriaDepartmentViolateNew[$value['staffid']]) ? $dtCriteriaDepartmentViolateNew[$value['staffid']] : [];
            $point = 0;
            if (!empty($countedArray)) {
                foreach ($countedArray as $k => $v) {
                    $dtData = !empty($dtCriteriaDepartmentViolate[$k]) ? $dtCriteriaDepartmentViolate[$k] : [];
                    $violations = array_column($dtData, 'violations');
                    $violationsToPoint = [];
                    if (!empty($dtData)) {
                        foreach ($dtData as $item) {
                            $violationsToPoint[$item['violations']] = $item['point'];
                        }
                    }
                    $maxViolations = !empty($violations) ? max($violations) : 0;
                    if ($v <= $maxViolations) {
                        if (array_key_exists($v, $violationsToPoint)) {
                            if ($violationsToPoint[$v] == -1) {
                                $point += $violationsToPoint[$v - 1];
                            } else {
                                $point += $violationsToPoint[$v];
                            }
                        }
                    } else {
                        if ($maxViolations > 0) {
                            $point += $violationsToPoint[$maxViolations - 1];
                        }
                    }
                }
            }

            // Diem vuot
            $pointNew = 0;
            $kpi_list_criteria_department_id_db_vuot = $value['kpi_list_criteria_department_id_vuot'];
            $kpi_list_criteria_department_id_vuot = explode(',', $kpi_list_criteria_department_id_db_vuot);
            $countedArrayVuot = [];
            if (!empty($kpi_list_criteria_department_id_vuot[0])) {
                $countedArrayVuot = array_count_values($kpi_list_criteria_department_id_vuot);
            }
            $dtCriteriaDepartmentViolateVuot = !empty($dtCriteriaDepartmentViolateNewVuot[$value['staffid']]) ? $dtCriteriaDepartmentViolateNewVuot[$value['staffid']] : [];
            if (!empty($countedArrayVuot)) {
                foreach ($countedArrayVuot as $k => $v) {
                    $dtDataVuot = !empty($dtCriteriaDepartmentViolateVuot[$k]) ? $dtCriteriaDepartmentViolateVuot[$k] : [];
                    $violationsVuot = array_column($dtDataVuot, 'violations');
                    $violationsToPointVuot = [];
                    if (!empty($dtDataVuot)) {
                        foreach ($dtDataVuot as $item) {
                            $violationsToPointVuot[$item['violations']] = $item['point_new'];
                        }
                    }
                    $maxViolationsVuot = !empty($violationsVuot) ? max($violationsVuot) : 0;
                    if ($v < $maxViolationsVuot) {
                        if (array_key_exists($v, $violationsToPointVuot)) {
                            $pointNew += $violationsToPointVuot[$v];
                        }
                    } else {
                        if ($maxViolationsVuot > 0) {
                            $pointNew += $violationsToPointVuot[$maxViolationsVuot - 1];
                        }
                    }
                }
            }

            $pointCurrent = $pointMax - $point + $pointNew;
            if ($pointCurrent <= 0) {
                $pointCurrent = 1;
            }
            if ($pointCurrent > 100) {
                $pointCurrent = 100;
            }

            // Kiem tra quyet dinh khen thuong
            $this->db->from('tbl_decision_bonus_discipline');
            $this->db->where('tbl_decision_bonus_discipline.type_quota_bonus_discipline_id', 1);
            $this->db->where('tbl_decision_bonus_discipline.object_id', $value['staffid']);
            $this->db->where('tbl_decision_bonus_discipline.object_type = "staff" ' . $whereDateDecision . '');
            $dtCountDecision = $this->db->count_all_results();

            // Lay xep loai KPI
            $dtRating = ratingKpiDepartment($pointCurrent);
            if (!empty($dtRating)) {
                if ($dtRating[0]['id'] == 1 && empty($dtCountDecision)) {
                    $dtRating = ratingKpiDepartment(-1, 2);
                }
            }

            // Thuong / Ky luat
            $bonusNames = [];
            $disciplineNames = [];
            $bonus = !empty($dtRating) ? $dtRating[0]['bonus'] : [];
            $discipline = !empty($dtRating) ? $dtRating[0]['discipline'] : [];
            if (!empty($bonus)) {
                foreach ($bonus as $k => $v) {
                    $bonusNames[] = $v['name'];
                }
            }
            if (!empty($discipline)) {
                foreach ($discipline as $k => $v) {
                    $disciplineNames[] = $v['name'];
                }
            }
            $resultText = '';
            if (!empty($bonusNames)) {
                $resultText .= 'Thưởng: ' . implode(', ', $bonusNames);
            }
            if (!empty($disciplineNames)) {
                if (!empty($resultText)) $resultText .= "\n";
                $resultText .= 'Kỷ luật: ' . implode(', ', $disciplineNames);
            }

            $ratingTitle = !empty($dtRating) ? $dtRating[0]['title'] : '';
            $ratingColor = !empty($dtRating) ? $dtRating[0]['color'] : '';
            $check_p3 = 'Không';
            if ($value['total_task_process'] == 0 && $value['count_bckph_process'] == 0 && $value['violate'] == 0 && $value['total_audit'] == 0) {
                $check_p3 = 'Có';
            }
            $p2_percent = (100 - $value['weight_p2']) > 0 ? (100 - $value['weight_p2']) . ' %' : '-';

            // Ghi dong du lieu
            $violateTextDisplay = !empty($violate_detail) ? " - " . implode("\n - ", $violate_detail) : ($value['violate'] != 0 ? $value['violate'] : '-');
            $vuotTextDisplay = !empty($vuot_detail) ? " - " . implode("\n - ", $vuot_detail) : ($value['vuot'] != 0 ? $value['vuot'] : '-');

            $dataRow = [
                $stt,
                $value['department_name'],
                $value['position_name'],
                $value['fullname'],
                ($value['total_task'] != 0 ? $value['total_task'] : '-'),
                ($value['count_bckph_old'] != 0 ? $value['count_bckph_old'] : '-'),
                ($value['count_bckph'] != 0 ? $value['count_bckph'] : '-'),
                ($value['violate_old'] != 0 ? $value['violate_old'] : '-'),
                $violateTextDisplay,
                $vuotTextDisplay,
                ($value['violation_p1'] != 0 ? $value['violation_p1'] : '-'),
                ($value['violation_p2'] != 0 ? $value['violation_p2'] : '-'),
                ($value['violation_p3'] != 0 ? $value['violation_p3'] : '-'),
                $pointCurrent,
                $ratingTitle,
                $resultText,
                $p2_percent,
                $check_p3,
            ];

            for ($i = 0; $i < count($dataRow); $i++) {
                $col = chr(65 + $i);
                $objPHPExcel->getActiveSheet()->setCellValue($col . $rowNum, $dataRow[$i]);
            }
            // Wrap text cho cot vi pham, vuot, ket qua
            $objPHPExcel->getActiveSheet()->getStyle("I$rowNum")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("J$rowNum")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("P$rowNum")->getAlignment()->setWrapText(true);
            // Chinh do cao dong cho nhung dong co noi dung dai
            $maxLines = max(count($violate_detail), count($vuot_detail));
            if ($maxLines > 1) {
                $objPHPExcel->getActiveSheet()->getRowDimension($rowNum)->setRowHeight(15 * ($maxLines + 1));
            }

            // To mau xep loai
            if (!empty($ratingColor)) {
                $objPHPExcel->getActiveSheet()->getStyle('O' . $rowNum)->applyFromArray([
                    'fill' => [
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => ['rgb' => $ratingColor],
                    ],
                ]);
            }

            // Border
            $objPHPExcel->getActiveSheet()->getStyle("A$rowNum:R$rowNum")->applyFromArray([
                'borders' => [
                    'allborders' => [
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    ]
                ],
                'alignment' => [
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ],
                'font' => [
                    'size' => 10,
                    'name' => 'Times New Roman'
                ],
            ]);

            // Canh trai + wrap text cho cot phong ban, vi tri, vi pham, vuot, ket qua
            $objPHPExcel->getActiveSheet()->getStyle("B$rowNum")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $objPHPExcel->getActiveSheet()->getStyle("C$rowNum")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $objPHPExcel->getActiveSheet()->getStyle("I$rowNum")->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $objPHPExcel->getActiveSheet()->getStyle("J$rowNum")->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $objPHPExcel->getActiveSheet()->getStyle("P$rowNum")->getAlignment()->setWrapText(true);

            $rowNum++;
            $stt++;
        }

        // Border cho header
        $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:R$sttRow")->getFont()->setBold(true);

        // Freeze pane
        $objPHPExcel->getActiveSheet()->freezePane('A' . ($sttRow + 1));

        // Output file
        $filename = 'BaoCaoDanhGiaKPI_' . ($month_year_start ?: $month_year) . '_' . date('Ymd_His') . '.xls';
        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();
        $response = array(
            'result' => 1,
            'filename' => $filename,
            'message' => lang('success'),
            'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        );
        die(json_encode($response));
    }

    public function view_kpi_evaluation_staff($staff_id = 0, $month = 0, $year = 0, $precious = 0)
    {
        $month_year = $year . '-' . $month;
        $month_year_start = null;
        $month_year_end = null;
        if (!empty($precious)) {
            if ($precious == 1) {
                $month_year_start = $year . '-01';
                $month_year_end = $year . '-03';
            } elseif ($precious == 2) {
                $month_year_start = $year . '-04';
                $month_year_end = $year . '-06';
            } elseif ($precious == 3) {
                $month_year_start = $year . '-07';
                $month_year_end = $year . '-09';
            } elseif ($precious == 4) {
                $month_year_start = $year . '-10';
                $month_year_end = $year . '-12';
            }
        }
        $whereDate = '';
        if (!empty($month_year_start)) {
            $whereDate = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") <= "' . $month_year_end . '"';
        } else {
            $whereDate = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") = "' . $month_year . '"';
        }
        $data = [];
        $data['title'] = lang('Xem đánh giá KPI');
        $tb_tamp = "(
            SELECT 
                tblproduction_report.staff_responsible as staff_id,
                GROUP_CONCAT((tblproduction_report.kpi_list_criteria_department_id) SEPARATOR ',') as kpi_list_criteria_department_id,
                COUNT(tblproduction_report.id) as violate
            FROM tblproduction_report
            WHERE kpi_list_criteria_department_id != 0 AND tblproduction_report.violate = 1
            $whereDate
            AND tblproduction_report.staff_responsible = $staff_id
        )";
        $query = $this->db->query($tb_tamp)->row_array();
        $html = '';
        if (!empty($query)) {
            $kpi_list_criteria_department_id_db = $query['kpi_list_criteria_department_id'];
            $kpi_list_criteria_department_id = explode(',', $kpi_list_criteria_department_id_db);
            $dtKpiCriteriaDepartment = [];
            $arrId = [0];
            if (!empty($kpi_list_criteria_department_id[0])) {
                $kpi_list_criteria_department_id = array_unique($kpi_list_criteria_department_id);
                foreach ($kpi_list_criteria_department_id as $key => $value) {
                    $arrId = array_merge($arrId, get_child_kpi_department($value));
                }
            }
            $this->db->where_in('tbl_kpi_list_criteria_department.id', $arrId);
            $this->db->from('tbl_kpi_list_criteria_department');
            $dtKpiCriteriaDepartment = $this->db->get()->result_array();
            $dtData = recursiveListCriteriaDepartment($dtKpiCriteriaDepartment);
            if (!empty($dtData)) {
                $stt = 1;
                foreach ($dtData as $key => $value) {
                    $children = $value['children'];
                    $this->db->select('tbl_kpi_list_criteria_department_violate.*');
                    $this->db->from('tbl_kpi_list_criteria_department_violate');
                    $this->db->where('tbl_kpi_list_criteria_department_violate.kpi_list_criteria_department_id', $value['id']);
                    $dtDataListCriteriaDepartmentViolate = $this->db->get()->result_array();
                    $children = flatten_children($children);
                    $maxCount = max(count($dtDataListCriteriaDepartmentViolate) - 1, count($children));
                    $html .= '<tr style="background-color: #FFF258">';
                    $html .= '<td style="text-align: center">' . intToRoman($stt) . '</td>';
                    $html .= '<td style="text-align: left;font-weight: bold">' . $value['name'] . '</td>';
                    $html .= '<td style="text-align: left"></td>';
                    $html .= '<td style="text-align: left"></td>';
                    $html .= '<td style="text-align: left"></td>';
                    $html .= '<td style="text-align: left"></td>';
                    $html .= '<td style="text-align: left"></td>';
                    $html .= '</tr>';
                    $stt++;
                    $level = 1;
                    $sttChild = 0;
                    $sttChildNew = 1;
                    if ($maxCount) {
                        for ($i = 0; $i < $maxCount; $i++) {
                            $v = !empty($children[$i]) ? $children[$i] : [];
                            if (!empty($v)) {
                                $violate = 0;
                                if ($v['level'] == 1) {
                                    $sttChild++;
                                    $tb_tamp = "(
                                        SELECT 
                                            COUNT(tblproduction_report.id) as violate
                                        FROM tblproduction_report
                                        WHERE kpi_list_criteria_department_id_child = " . $v['id'] . " AND tblproduction_report.violate = 1
                                        $whereDate
                                        AND tblproduction_report.staff_responsible = " . $staff_id . "
                                    )";
                                    $violate = $this->db->query($tb_tamp)->row_array()['violate'];
                                }
                                if ($v['level'] == 2) {
                                    $sttChildNew++;
                                    if ($level != $v['level']) {
                                        $sttChildNew = 1;
                                    }
                                    $tb_tamp = "(
                                        SELECT 
                                            COUNT(tblproduction_report.id) as violate
                                        FROM tblproduction_report
                                        WHERE kpi_list_criteria_department_id_childd = " . $v['id'] . " AND tblproduction_report.violate = 1
                                        $whereDate
                                        AND tblproduction_report.staff_responsible = " . $staff_id . "
                                    )";
                                    $violate = $this->db->query($tb_tamp)->row_array()['violate'];
                                }
                                $level = $v['level'];
                            }
                            $html .= '<tr>';
                            if (!empty($v)) {
                                if ($v['level'] == 1) {
                                    $html .= '<td style="text-align: center">1.' . $sttChild . '</td>';
                                    $html .= '<td style="text-align: left;font-weight: bold">' . (!empty($v) ? $v['name'] : '') . '</td>';
                                } else {
                                    $html .= '<td style="text-align: center">' . $sttChildNew . '</td>';
                                    $html .= '<td style="text-align: left;font-weight: bold"></td>';
                                }
                            } else {
                                $html .= '<td style="text-align: center"></td>';
                                $html .= '<td style="text-align: left;font-weight: bold"></td>';
                            }
                            $html .= '<td style="text-align: left">' . (!empty($v) ? $v['evaluation_criteria'] : '') . '</td>';
                            $html .= '<td style="text-align: left">' . (!empty($v) ? $v['violate'] : '') . '</td>';
                            if (!empty($v)) {
                                if ($v['level'] == 1) {
                                    $html .= '<td style="text-align: center">' . (!empty($violate) && (!empty($v) && !empty($v['violate'])) ? $violate : '') . '</td>';
                                } else {
                                    $html .= '<td style="text-align: center">' . (!empty($violate) ? $violate : '') . '</td>';
                                }
                            } else {
                                $html .= '<td style="text-align: center"></td>';
                            }
                            $html .= '<td style="text-align: left">' . (!empty($dtDataListCriteriaDepartmentViolate[$i]) ? $dtDataListCriteriaDepartmentViolate[$i]['violations_text'] : "") . '</td>';
                            $html .= '<td style="text-align: left">' . (!empty($dtDataListCriteriaDepartmentViolate[$i]) ? $dtDataListCriteriaDepartmentViolate[$i]['point_text'] : "") . '</td>';
                            $html .= '</tr>';
                        }
                    }
                }
            }
        }
        $data['html'] = $html;
        $this->load->view('admin/kpi/view_kpi_evaluation_staff', $data);
    }

    public function view_detail_production_report($staff_id = 0, $month = 0, $year = 0, $precious = 0)
    {
        $data = [];
        $data['title'] = lang('Xem Số Phiếu Vi Phạm');
        $data['month'] = $month;
        $data['year'] = $year;
        $data['staff_id'] = $staff_id;
        $data['precious'] = $precious;
        $this->load->view('admin/kpi/view_detail_production_report', $data);
    }

    public function getDetailProductionReport()
    {
        $staff_id = $this->input->post('staff_id');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $precious = $this->input->post('precious');
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $month_year = $year . '-' . $month;
        $month_year_start = null;
        $month_year_end = null;
        if (!empty($precious)) {
            if ($precious == 1) {
                $month_year_start = $year . '-01';
                $month_year_end = $year . '-03';
            } elseif ($precious == 2) {
                $month_year_start = $year . '-04';
                $month_year_end = $year . '-06';
            } elseif ($precious == 3) {
                $month_year_start = $year . '-07';
                $month_year_end = $year . '-09';
            } elseif ($precious == 4) {
                $month_year_start = $year . '-10';
                $month_year_end = $year . '-12';
            }
        }
        $whereDate = '';
        if (!empty($month_year_start)) {
            $whereDate = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") <= "' . $month_year_end . '"';
        } else {
            $whereDate = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") = "' . $month_year . '"';
        }
        $aColumns = [
            'tblproduction_report.id as id',
            'tblproduction_report.date as date',
            'tblproduction_report.reference_no as reference_no',
            'tblproduction_report.name_report as name_report',
            '"" as violate'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tblproduction_report';
        $where = [];

        array_push($where, 'AND tblproduction_report.kpi_list_criteria_department_id != 0');
        array_push($where, 'AND tblproduction_report.violate = 1');
        array_push($where, 'AND tblproduction_report.staff_responsible = ' . $staff_id . ' ' . $whereDate . '');
        $filter = [];

        $join = [];


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'kpi_list_criteria_department_id',
            'kpi_list_criteria_department_id_child',
            'kpi_list_criteria_department_id_childd',
            'kpi_list_criteria_department_violate',
        ], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $stt = 1;
        foreach ($rResult as $key => $aRow) {
            $row = [];
            $start++;
            $row[] = '<div class="text-center">' . $stt . '</div>';
            $row[] = '<div class="text-left">' . (!empty($aRow['date']) ? _dt($aRow['date']) : '') . '</div>';
            $row[] = '<div class="text-left">' . $aRow['reference_no'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['name_report'] . '</div>';
            $this->db->from('tbl_kpi_list_criteria_department');
            $this->db->where('tbl_kpi_list_criteria_department.id', $aRow['kpi_list_criteria_department_id']);
            $dtDataKpi = $this->db->get()->row_array();

            $this->db->from('tbl_kpi_list_criteria_department');
            $this->db->where('tbl_kpi_list_criteria_department.id', $aRow['kpi_list_criteria_department_id_child']);
            $dtDataKpiDetail = $this->db->get()->row_array();

            $this->db->from('tbl_kpi_list_criteria_department');
            $this->db->where('tbl_kpi_list_criteria_department.id', $aRow['kpi_list_criteria_department_id_childd']);
            $dtDataKpiDetailNew = $this->db->get()->row_array();

            $name_kpi_department = !empty($dtDataKpi) ? '+' . $dtDataKpi['name'] : '';
            $name_kpi_department_detail = (!empty($dtDataKpiDetail) ? '+' . (!empty($dtDataKpiDetail['evaluation_criteria']) ? $dtDataKpiDetail['evaluation_criteria'] : $dtDataKpiDetail['name']) : '') . '-' . (!empty($dtDataKpiDetailNew) ? $dtDataKpiDetailNew['evaluation_criteria'] : '');
            $row[] = '<div class="text-left">' . $name_kpi_department . '<br>' . $name_kpi_department_detail . '<br><span style="color: red">+' . $aRow['kpi_list_criteria_department_violate'] . '</span></div>';
            $stt++;
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function view_detail_production_report_vuot($staff_id = 0, $month = 0, $year = 0, $precious = 0)
    {
        $data = [];
        $data['title'] = lang('Xem Số Phiếu Vượt');
        $data['month'] = $month;
        $data['year'] = $year;
        $data['staff_id'] = $staff_id;
        $data['precious'] = $precious;
        $this->load->view('admin/kpi/view_detail_production_report_vuot', $data);
    }

    public function getDetailProductionReportVuot()
    {
        $staff_id = $this->input->post('staff_id');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $precious = $this->input->post('precious');
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $month_year = $year . '-' . $month;
        $month_year_start = null;
        $month_year_end = null;
        if (!empty($precious)) {
            if ($precious == 1) {
                $month_year_start = $year . '-01';
                $month_year_end = $year . '-03';
            } elseif ($precious == 2) {
                $month_year_start = $year . '-04';
                $month_year_end = $year . '-06';
            } elseif ($precious == 3) {
                $month_year_start = $year . '-07';
                $month_year_end = $year . '-09';
            } elseif ($precious == 4) {
                $month_year_start = $year . '-10';
                $month_year_end = $year . '-12';
            }
        }
        $whereDate = '';
        if (!empty($month_year_start)) {
            $whereDate = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") <= "' . $month_year_end . '"';
        } else {
            $whereDate = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") = "' . $month_year . '"';
        }
        $aColumns = [
            'tblproduction_report.id as id',
            'tblproduction_report.date as date',
            'tblproduction_report.reference_no as reference_no',
            'tblproduction_report.name_report as name_report',
            'tblproduction_report.point_kpi as point_kpi'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tblproduction_report';
        $where = [];

        array_push($where, 'AND tblproduction_report.kpi_list_criteria_department_id != 0');
        array_push($where, 'AND tblproduction_report.type_report = 2');
        array_push($where, 'AND tblproduction_report.staff_responsible = ' . $staff_id . ' ' . $whereDate . '');
        $filter = [];

        $join = [];


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'kpi_list_criteria_department_id',
            'kpi_list_criteria_department_id_child',
            'kpi_list_criteria_department_id_childd',
            'kpi_list_criteria_department_violate',
        ], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $stt = 1;
        foreach ($rResult as $key => $aRow) {
            $row = [];
            $start++;
            $row[] = '<div class="text-center">' . $stt . '</div>';
            $row[] = '<div class="text-left">' . (!empty($aRow['date']) ? _dt($aRow['date']) : '') . '</div>';
            $row[] = '<div class="text-left">' . $aRow['reference_no'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['name_report'] . '</div>';
            $this->db->from('tbl_kpi_list_criteria_department');
            $this->db->where('tbl_kpi_list_criteria_department.id', $aRow['kpi_list_criteria_department_id']);
            $dtDataKpi = $this->db->get()->row_array();

            $this->db->from('tbl_kpi_list_criteria_department');
            $this->db->where('tbl_kpi_list_criteria_department.id', $aRow['kpi_list_criteria_department_id_child']);
            $dtDataKpiDetail = $this->db->get()->row_array();

            $this->db->from('tbl_kpi_list_criteria_department');
            $this->db->where('tbl_kpi_list_criteria_department.id', $aRow['kpi_list_criteria_department_id_childd']);
            $dtDataKpiDetailNew = $this->db->get()->row_array();

            $name_kpi_department = !empty($dtDataKpi) ? '+' . $dtDataKpi['name'] : '';
            $name_kpi_department_detail = (!empty($dtDataKpiDetail) ? '+' . (!empty($dtDataKpiDetail['evaluation_criteria']) ? $dtDataKpiDetail['evaluation_criteria'] : $dtDataKpiDetail['name']) : '') . '-' . (!empty($dtDataKpiDetailNew) ? $dtDataKpiDetailNew['evaluation_criteria'] : '');
            $row[] = '<div class="text-left">' . $name_kpi_department . '<br>' . $name_kpi_department_detail . '<br><span style="color: red">+' . $aRow['kpi_list_criteria_department_violate'] . '</span></div>';
            $stt++;
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function get_staff_by_department()
    {
        $department_search = $this->input->post('department_search') ?? 0;
        $this->db->select('tblstaff.staffid as staffid, CONCAT(tblstaff.firstname," ",tblstaff.lastname) as firstname');
        $this->db->from('tblstaff');
        $this->db->where('EXISTS (
            SELECT 1
            FROM tblstaff_departments
            WHERE tblstaff_departments.staffid = tblstaff.staffid
            AND tblstaff_departments.departmentid = ' . $department_search . '
        )');
        $staffs = $this->db->get()->result_array();
        $data = [];
        $data['staffs'] = $staffs;
        echo json_encode($data);
    }

    public function exportExcel()
    {
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            // print_arrays($this->input->post());
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();

            $month = $this->input->post('filter_month');
            $year = $this->input->post('year');
            $staff = $this->input->post('staff');
            $department_search = $this->input->post('department_search');
            $precious = $this->input->post('precious');
            $month_year = $year . '-' . $month;
            $month_year_start = null;
            $month_year_end = null;
            if (!empty($precious)) {
                if ($precious == 1) {
                    $month_year_start = $year . '-01';
                    $month_year_end = $year . '-03';
                } elseif ($precious == 2) {
                    $month_year_start = $year . '-04';
                    $month_year_end = $year . '-06';
                } elseif ($precious == 3) {
                    $month_year_start = $year . '-07';
                    $month_year_end = $year . '-09';
                } elseif ($precious == 4) {
                    $month_year_start = $year . '-10';
                    $month_year_end = $year . '-12';
                }
            }

            $whereDate = '';
            if (!empty($month_year_start)) {
                $whereDate = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") <= "' . $month_year_end . '"';
            } else {
                $whereDate = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") = "' . $month_year . '"';
            }
            $tb_tamp = "(
            SELECT 
                tblproduction_report.staff_responsible as staff_id,
                GROUP_CONCAT((tblproduction_report.kpi_list_criteria_department_id) SEPARATOR ',') as kpi_list_criteria_department_id,
                COUNT(tblproduction_report.id) as violate
            FROM tblproduction_report
            WHERE kpi_list_criteria_department_id != 0 AND tblproduction_report.violate = 1
            $whereDate
            GROUP BY tblproduction_report.staff_responsible
        ) tb_tamp";
            $this->db->select('
            tblstaff.staffid as staffid,
            CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname,
            COALESCE(violate,0) as violate,
            tb_tamp.kpi_list_criteria_department_id,
            "" as rating
        ');
            $this->db->from('tblstaff');
            //        $this->db->where('EXISTS (
            //            SELECT 1
            //            FROM tblproduction_report
            //            WHERE tblproduction_report.staff_responsible = tblstaff.staffid AND tblproduction_report.violate = 1
            //            AND kpi_list_criteria_department_id != 0 AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") = "'.$month_year.'"
            //        )');
            $this->db->where('active', 1);
            if (!empty($staff)) {
                $this->db->where('tblstaff.staffid', $staff);
            }
            if (!empty($department_search)) {
                $this->db->where('EXISTS (
                SELECT 1
                FROM tblstaff_departments
                WHERE tblstaff_departments.staffid = tblstaff.staffid
                AND tblstaff_departments.departmentid = ' . $department_search . '
            )');
            }
            $this->db->join($tb_tamp, 'tblstaff.staffid = tb_tamp.staff_id', 'left');
            $dtData = $this->db->get()->result_array();


            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
            $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
                ->setWidth(20);
            $decimals_money = get_option('decimals_money');
            $decimals_number = get_option('decimals_number');
            $number_excel_money = '#,##0' . ($decimals_money > 0 ? '.' . sprintf("%0" . $decimals_money . "s", 0) : '');
            $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf(
                "%0" . $decimals_number . "s",
                0
            ) : '');
            $company = get_option('invoice_company_name');
            $address = get_option('invoice_company_address');
            $company_vat = get_option('company_vat');
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name' => 'Times New Roman'
                ),
            ]);
            $objPHPExcel->getActiveSheet()->setCellValue(
                'A1',
                ('ĐÁNH GIÁ KPI')
            )->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow . '', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $sttRow . '', 'Nhân Viên');
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow . '', 'Số Phiếu Vi Phạm');
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow . '', 'Điểm Số');
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow . '', 'Xếp Loại')->getStyle("E$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $sttRow . '', 'Kết Quả')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:F$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name' => 'Times New Roman'
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '92D050'),
                ),
            ]);
            $this->load->library('ciqrcode');
            $dtCriteriaDepartmentViolateNew = [];
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $staffid = $value['staffid'];
                    $kpi_list_criteria_department_id_db = $value['kpi_list_criteria_department_id'];
                    $kpi_list_criteria_department_id = explode(',', $kpi_list_criteria_department_id_db);
                    if (!empty($kpi_list_criteria_department_id)) {
                        $this->db->where_in('kpi_list_criteria_department_id', $kpi_list_criteria_department_id);
                        $this->db->from('tbl_kpi_list_criteria_department_violate');
                        $dtCriteriaDepartmentViolate = $this->db->get()->result_array();
                    }
                    if (!empty($dtCriteriaDepartmentViolate)) {
                        $dtCriteriaDepartmentViolate = array_reduce($dtCriteriaDepartmentViolate, function ($carry, $item) {
                            $carry[$item['kpi_list_criteria_department_id']][] = $item;
                            return $carry;
                        });
                    }
                    $dtCriteriaDepartmentViolateNew[$staffid] = $dtCriteriaDepartmentViolate;
                }
            }
            $rowBegin = $sttRow;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $rowBegin++;

                    $pointMax = 100;
                    $kpi_list_criteria_department_id_db = $value['kpi_list_criteria_department_id'];
                    $kpi_list_criteria_department_id = explode(',', $kpi_list_criteria_department_id_db);
                    $countedArray = [];
                    if (!empty($kpi_list_criteria_department_id[0])) {
                        $countedArray = array_count_values($kpi_list_criteria_department_id);
                    }
                    $dtCriteriaDepartmentViolate = !empty($dtCriteriaDepartmentViolateNew[$value['staffid']]) ? $dtCriteriaDepartmentViolateNew[$value['staffid']] : [];
                    $point = 0;
                    if (!empty($countedArray)) {
                        foreach ($countedArray as $k => $v) {
                            $dtData = !empty($dtCriteriaDepartmentViolate[$k]) ? $dtCriteriaDepartmentViolate[$k] : [];
                            $violations = array_column($dtData, 'violations');
                            $violationsToPoint = [];
                            if (!empty($dtData)) {
                                foreach ($dtData as $item) {
                                    $violationsToPoint[$item['violations']] = $item['point'];
                                }
                            }
                            $maxViolations = max($violations);
                            if ($v < $maxViolations) {
                                if (array_key_exists($v, $violationsToPoint)) {
                                    $point += $violationsToPoint[$v];
                                }
                            } else {
                                $point += $violationsToPoint[$maxViolations];
                            }
                        }
                    }
                    $pointCurrent = $pointMax - $point;
                    if ($pointCurrent <= 0) {
                        $pointCurrent = 1;
                    }
                    $dtRating = ratingKpiDepartment($pointCurrent);
                    $bonus = !empty($dtRating) ? $dtRating[0]['bonus'] : [];
                    $discipline = !empty($dtRating) ? $dtRating[0]['discipline'] : [];
                    $htmlBouns = '';
                    $htmlDiscipline = '';
                    if (!empty($bonus)) {
                        foreach ($bonus as $k => $v) {
                            $htmlBouns .= $v . "\n";
                        }
                    }
                    $htmlBouns = trim("\n" . $htmlBouns . "\n");
                    if (!empty($discipline)) {
                        foreach ($discipline as $k => $v) {
                            $htmlDiscipline .= $v . "\n";
                        }
                    }
                    $htmlDiscipline = trim("\n" . $htmlDiscipline . "\n");

                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['fullname']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['violate']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $pointCurrent)->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", (!empty($dtRating) ? $dtRating[0]['name'] : ''))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", 'Thưởng ' . "\n" . $htmlBouns . "\n" . 'Kỷ Luật ' . "\n" . $htmlDiscipline)->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);


                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:F$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:A$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);

                    $objPHPExcel->getActiveSheet()->getStyle("B$rowBegin:B$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);

                    $objPHPExcel->getActiveSheet()->getStyle("C$rowBegin:C$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("D$rowBegin:D$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("E$rowBegin:E$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                }
            }
            $filename = lang('danh_gia_kpi') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
            ob_start();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="$filename"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();
            $response = array(
                'result' => 1,
                'filename' => $filename,
                'message' => lang('success'),
                'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
            );
            die(json_encode($response));
        }
    }

    public function category_evaluate_kpi()
    {
        $data = [];
        $data['title'] = lang('Danh Mục Đánh Giá KPI');
        $this->load->view('admin/kpi/category_evaluate_kpi', $data);
    }

    public function view_category_evaluate_kpi()
    {
        $this->db->select("*");
        $this->db->from('tbl_category_evaluate_kpi');
        $dtData = $this->db->get()->result_array();
        $html = "";
        if (!empty($dtData)) {
            foreach ($dtData as $key => $value) {
                $category_evaluate_kpi = $value['id'];
                $this->db->select("*");
                $this->db->from("tbl_category_evaluate_kpi_detail");
                $this->db->where("tbl_category_evaluate_kpi_detail.category_evaluate_kpi_id", $category_evaluate_kpi);
                $this->db->where("tbl_category_evaluate_kpi_detail.type", 1);
                $dtItemsBonus = $this->db->get()->result_array();

                $this->db->select("*");
                $this->db->from("tbl_category_evaluate_kpi_detail");
                $this->db->where("tbl_category_evaluate_kpi_detail.category_evaluate_kpi_id", $category_evaluate_kpi);
                $this->db->where("tbl_category_evaluate_kpi_detail.type", 2);
                $dtItemsDiscipline = $this->db->get()->result_array();
                $htmlBouns = '';
                $htmlDiscipline = '';
                if (!empty($dtItemsBonus)) {
                    foreach ($dtItemsBonus as $k => $v) {
                        $htmlBouns .= '<div>' . $v['name'] . '</div>';
                    }
                }
                if (!empty($dtItemsDiscipline)) {
                    foreach ($dtItemsDiscipline as $k => $v) {
                        $htmlDiscipline .= '<div>' . $v['name'] . '</div>';
                    }
                }
                $html .= '<tr>';
                $html .= '<td class="text-center">' . (++$key) . '</td>';
                $html .= '<td class="text-left">' . $value['name'] . '<div><a class="tnh-modal" href="' . base_url('admin/kpi/updateCategoryEvaluateKpiNew/' . $category_evaluate_kpi . '') . '">Cập nhập thưởng/kỷ luật </a></div></td>';
                $html .= '<td class="text-left"><input type="text" class="form-control" value="' . $value['point_min'] . '" onchange="updateCategoryEvaluateKpi(this,' . $category_evaluate_kpi . ',\'point_min\')"></td>';
                $html .= '<td class="text-left"><input type="text" class="form-control" value="' . $value['point_max'] . '" onchange="updateCategoryEvaluateKpi(this,' . $category_evaluate_kpi . ',\'point_max\')"></td>';
                $html .= '<td class="text-left"><input type="text" class="form-control" value="' . $value['color'] . '" onchange="updateCategoryEvaluateKpi(this,' . $category_evaluate_kpi . ',\'color\')">
                </td>';
                $html .= '<td class="text-left td-bonus">' . $htmlBouns . '</td>';
                $html .= '<td class="text-left td-discipline">' . $htmlDiscipline . '</td>';
                $html .= '</tr>';
            }
        }
        $data['html'] = $html;
        echo json_encode($data);
    }

    public function updateCategoryEvaluateKpi()
    {
        $category_evaluate_kpi = $this->input->post('category_evaluate_kpi');
        $name = $this->input->post('name');
        $value = $this->input->post('value');
        $this->db->where('id', $category_evaluate_kpi);
        $success = $this->db->update('tbl_category_evaluate_kpi', [
            $name => $value
        ]);
        if ($success) {
            $data['result'] = true;
            $data['message'] = lang('Thành công');
        } else {
            $data['result'] = false;
            $data['message'] = lang('Thất bại');
        }
        echo json_encode($data);
    }


    public function updateCategoryEvaluateKpiNew($category_evaluate_kpi = 0)
    {
        $data = [];
        if ($this->input->post()) {
            $counter = $this->input->post('counter');
            $items = [];
            if (!empty($counter)) {
                foreach ($counter as $key => $value) {
                    $id_item = !empty($this->input->post('id_item')[$value]) ? $this->input->post('id_item')[$value] : 0;
                    $name = $this->input->post('name')[$value];
                    $type = $this->input->post('type')[$value];
                    if (empty($name)) {
                        continue;
                    }
                    $items[] = [
                        'id' => $id_item,
                        'category_evaluate_kpi_id' => $category_evaluate_kpi,
                        'name' => $name,
                        'type' => $type,
                    ];
                }
            }
            if (empty($items)) {
                $data['result'] = false;
                $data['message'] = lang('Không có dữ liệu');
                echo json_encode($data);
                die();
            }
            $count = 0;
            $this->db->where('category_evaluate_kpi_id', $category_evaluate_kpi);
            $this->db->delete('tbl_category_evaluate_kpi_detail');
            foreach ($items as $key => $value) {
                $check = get_table_where('tbl_category_evaluate_kpi_detail', ['id' => $value['id']], '', 'row_array');
                if (!empty($check)) {
                    $this->db->where('id', $value['id']);
                    $this->db->update('tbl_category_evaluate_kpi_detail', [
                        'category_evaluate_kpi_id' => $value['category_evaluate_kpi_id'],
                        'type' => $value['type'],
                        'name' => $value['name'],
                    ]);
                    $count++;
                } else {
                    $this->db->insert('tbl_category_evaluate_kpi_detail', [
                        'category_evaluate_kpi_id' => $value['category_evaluate_kpi_id'],
                        'type' => $value['type'],
                        'name' => $value['name'],
                    ]);
                    $count++;
                }
            }
            if ($count > 0) {
                $data['result'] = true;
                $data['message'] = lang('Cập nhập thành công');
            } else {
                $data['result'] = false;
                $data['message'] = lang('Cập nhập thất bại');
            }
            echo json_encode($data);
            die();
        }
        $this->db->select("*");
        $this->db->from("tbl_category_evaluate_kpi_detail");
        $this->db->where("tbl_category_evaluate_kpi_detail.category_evaluate_kpi_id", $category_evaluate_kpi);
        $dtItems = $this->db->get()->result_array();
        $data['title'] = lang('Cập Nhập Danh Mục Thưởng/Kỷ Luật Đánh Giá KPI');
        $dtType = $this->type;
        $data['dtType'] = $dtType;
        $data['dtItems'] = $dtItems;
        $data['category_evaluate_kpi'] = $category_evaluate_kpi;
        $this->load->view('admin/kpi/update_category_evaluate_kpi', $data);
    }



    public function view_kpi_evaluation_staff_old($staff_id = 0, $month = 0, $year = 0)
    {
        $month_year = $year . '-' . $month;
        $data = [];
        $data['title'] = lang('Xem đánh giá KPI');
        $tb_tamp = "(
            SELECT 
                tblproduction_report.staff_responsible as staff_id,
                GROUP_CONCAT((tblproduction_report.kpi_list_criteria_department_id) SEPARATOR ',') as kpi_list_criteria_department_id,
                COUNT(tblproduction_report.id) as violate
            FROM tblproduction_report
            WHERE kpi_list_criteria_department_id != 0 AND tblproduction_report.violate = 1
            AND DATE_FORMAT(tblproduction_report.date, '%Y-%m') = '" . $month_year . "'
            AND tblproduction_report.staff_responsible = $staff_id
        )";
        $query = $this->db->query($tb_tamp)->row_array();
        $html = '';
        if (!empty($query)) {
            $kpi_list_criteria_department_id_db = $query['kpi_list_criteria_department_id'];
            $kpi_list_criteria_department_id = explode(',', $kpi_list_criteria_department_id_db);
            $dtKpiCriteriaDepartment = [];
            $arrId = [0];
            if (!empty($kpi_list_criteria_department_id[0])) {
                $kpi_list_criteria_department_id = array_unique($kpi_list_criteria_department_id);
                foreach ($kpi_list_criteria_department_id as $key => $value) {
                    $arrId = array_merge($arrId, get_child_kpi_department($value));
                }
            }
            $this->db->where_in('tbl_kpi_list_criteria_department.id', $arrId);
            $this->db->from('tbl_kpi_list_criteria_department');
            $dtKpiCriteriaDepartment = $this->db->get()->result_array();
            $dtData = recursiveListCriteriaDepartment($dtKpiCriteriaDepartment);
            if (!empty($dtData)) {
                $stt = 1;
                foreach ($dtData as $key => $value) {
                    $children = $value['children'];
                    $html .= '<tr style="background-color: #FFF258">';
                    $html .= '<td style="text-align: center">' . intToRoman($stt) . '</td>';
                    $html .= '<td style="text-align: left;font-weight: bold">' . $value['name'] . '</td>';
                    $html .= '<td style="text-align: left"></td>';
                    $html .= '<td style="text-align: left"></td>';
                    $html .= '<td style="text-align: left"></td>';
                    $html .= '</tr>';
                    $sttChild = 1;
                    if (!empty($children)) {
                        foreach ($children as $k => $v) {
                            $child = !empty($v['children']) ? $v['children'] : [];
                            if (!empty($v['evaluation_criteria'])) {
                                $tb_tamp = "(
                                        SELECT 
                                            COUNT(tblproduction_report.id) as violate
                                        FROM tblproduction_report
                                        WHERE kpi_list_criteria_department_id_child = " . $v['id'] . " AND tblproduction_report.violate = 1
                                        AND DATE_FORMAT(tblproduction_report.date, '%Y-%m') = '" . $month_year . "'
                                        AND tblproduction_report.staff_responsible = " . $staff_id . "
                                    )";
                                $violate_child = $this->db->query($tb_tamp)->row_array()['violate'];
                            } else {
                                $violate_child = 0;
                            }
                            $html .= '<tr style="">';
                            $html .= '<td style="text-align: center">1.' . $sttChild . '</td>';
                            $html .= '<td style="text-align: left;font-weight: bold">' . $v['name'] . '</td>';
                            $html .= '<td style="text-align: left">' . $v['evaluation_criteria'] . '</td>';
                            $html .= '<td style="text-align: left">' . $v['violate'] . '</td>';
                            $html .= '<td style="text-align: center">' . (!empty($violate_child) ? $violate_child : '-') . '</td>';
                            $html .= '</tr>';
                            $sttChild++;
                            $sttChildNew = 1;
                            if (!empty($child)) {
                                foreach ($child as $kk => $vv) {
                                    $tb_tamp = "(
                                        SELECT 
                                            COUNT(tblproduction_report.id) as violate
                                        FROM tblproduction_report
                                        WHERE kpi_list_criteria_department_id_childd = " . $vv['id'] . " AND tblproduction_report.violate = 1
                                        AND DATE_FORMAT(tblproduction_report.date, '%Y-%m') = '" . $month_year . "'
                                        AND tblproduction_report.staff_responsible = " . $staff_id . "
                                    )";
                                    $violate = $this->db->query($tb_tamp)->row_array()['violate'];
                                    // $type = '';
                                    // if ($vv['type_p'] == 1) {
                                    //     $type = 'Lương P1';
                                    // }
                                    // if ($vv['type_p'] == 2) {
                                    //     $type = 'Lương P2';
                                    // }
                                    // if ($vv['type_p'] == 3) {
                                    //     $type = 'Lương P2';
                                    // }

                                    $html .= '<tr style="">';
                                    $html .= '<td style="text-align: center">' . $sttChildNew . '</td>';
                                    $html .= '<td style="text-align: left;font-weight: bold"></td>';
                                    $html .= '<td style="text-align: center">' . $vv['type_p'] . '</td>';
                                    $html .= '<td style="text-align: center">' . $vv['violate'] . '</td>';
                                    $html .= '<td style="text-align: center">' . (!empty($violate) ? $violate : '-') . '</td>';
                                    $html .= '</tr>';
                                    $sttChildNew++;
                                }
                            }
                        }
                    }
                }
            }
        }
        $data['html'] = $html;
        $this->load->view('admin/kpi/view_kpi_evaluation_staff', $data);
    }

    public function view_detail_p3($staff_id = 0, $month = 0, $year = 0, $precious = 0)
    {
        $data = [];
        $data['title'] = lang('Xem gate chặn P3');
        $data['month'] = $month;
        $data['year'] = $year;
        $data['staff_id'] = $staff_id;
        $data['precious'] = $precious;
        $this->load->view('admin/kpi/view_detail_p3', $data);
    }

    public function table_detail_p3()
    {
        $staff_id = $this->input->post('staff_id');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $precious = $this->input->post('precious');
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $month_year = $year . '-' . $month;
        $month_year_start = null;
        $month_year_end = null;
        if (!empty($precious)) {
            if ($precious == 1) {
                $month_year_start = $year . '-01';
                $month_year_end = $year . '-03';
            } elseif ($precious == 2) {
                $month_year_start = $year . '-04';
                $month_year_end = $year . '-06';
            } elseif ($precious == 3) {
                $month_year_start = $year . '-07';
                $month_year_end = $year . '-09';
            } elseif ($precious == 4) {
                $month_year_start = $year . '-10';
                $month_year_end = $year . '-12';
            }
        }
        $whereDate = '';
        $whereDateTask = '';
        $whereDateAudit = '';
        if (!empty($month_year_start)) {
            $whereDate = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") <= "' . $month_year_end . '"';
            $whereDateTask = 'AND DATE_FORMAT(tbltasks.dateadded, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tbltasks.dateadded, "%Y-%m") <= "' . $month_year_end . '"';
            $whereDateAudit = 'AND DATE_FORMAT(tbl_audit.audit_date, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tbl_audit.audit_date, "%Y-%m") <= "' . $month_year_end . '"';
        } else {
            $whereDate = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") = "' . $month_year . '"';
            $whereDateTask = 'AND DATE_FORMAT(tbltasks.dateadded, "%Y-%m") = "' . $month_year . '"';
            $whereDateAudit = 'AND DATE_FORMAT(tbl_audit.audit_date, "%Y-%m") = "' . $month_year . '"';
        }

        $tb_tamp = "(
            SELECT
                tblproduction_report.id,
                tblproduction_report.date,
                tblproduction_report.reference_no,
                tblproduction_report.name_report,
                0 as status,
                1 as type
            FROM tblproduction_report
            WHERE tblproduction_report.id != 0 AND EXISTS (
                SELECT 1 
                FROM tbl_process_production_report 
                WHERE tbl_process_production_report.production_report_id = tblproduction_report.id
                AND tbl_process_production_report.staff_process = 0
             ) AND tblproduction_report.staff_responsible = $staff_id $whereDate
             
            UNION ALL
            
            SELECT
                tblproduction_report.id,
                tblproduction_report.date,
                tblproduction_report.reference_no,
                tblproduction_report.name_report,
                0 as status,
                2 as type
            FROM tblproduction_report
            WHERE tblproduction_report.id != 0 AND tblproduction_report.violate = 1 
            AND tblproduction_report.staff_responsible = $staff_id $whereDate
            
            UNION ALL 
             
            SELECT 
                tbltasks.id,
                tbltasks.dateadded as date,
                tblcategory_tasks.code COLLATE utf8_unicode_ci AS reference_no,
                tbltasks.name COLLATE utf8_unicode_ci AS name_report,
                0 as status,
                3 as type
            FROM tbltasks
            LEFT JOIN tblcategory_tasks ON tblcategory_tasks.id = tbltasks.category_tasks
            JOIN tbltask_assigned ON tbltask_assigned.taskid = tbltasks.id
            WHERE tbltasks.id != 0 AND tbltasks.status != 5 
            AND tbltask_assigned.staffid = $staff_id $whereDateTask    
             
            UNION ALL 
             
            SELECT 
                tbl_audit.id,
                tbl_audit.audit_date as date,
                tbl_audit.audit_code COLLATE utf8_unicode_ci AS reference_no,
                tbl_audit.audit_code COLLATE utf8_unicode_ci AS name_report,
                0 as status,
                4 as type
            FROM tbl_audit
            JOIN tbl_room ON tbl_room.id = tbl_audit.dept_id
            JOIN tbldepartments ON tbldepartments.room_id = tbl_room.id
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            WHERE EXISTS (
                SELECT 1
                FROM tbl_audit_checklist
                WHERE tbl_audit_checklist.audit_id = tbl_audit.id
                AND tbl_audit_checklist.status = 'no'
            )
            AND tblstaff_departments.staffid = $staff_id $whereDateAudit 
        ) tb_tamp";

        $aColumns = [
            'tb_tamp.id as id',
            'tb_tamp.date as date',
            'tb_tamp.reference_no as reference_no',
            'tb_tamp.name_report as name_report',
            'tb_tamp.status as status'
        ];
        $sIndexColumn = 'id';
        $sTable = $tb_tamp;
        $where = [];

        $filter = [];

        $join = [];


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tb_tamp.type'
        ], '', [], ['union_all' => true]);

        $output = $result['output'];
        $rResult = $result['rResult'];

        $start = $this->input->post('start');
        $stt = 1;
        foreach ($rResult as $key => $aRow) {
            $row = [];
            $start++;
            $htmlType = '';
            $htmlTypeNew = '';
            if ($aRow['type'] == 2) {
                $htmlType = 'Phiếu vi phạm';
                $htmlTypeNew  = '<div class="label label-danger">Vi phạm</div>';
            } elseif ($aRow['type'] == 1) {
                $htmlType = 'BCKPH chưa hoàn thành';
                $htmlTypeNew  = '<div class="label label-primary">BCKPH</div>';
            } elseif ($aRow['type'] == 3) {
                $htmlType = 'Công việc chưa hoàn thành';
                $htmlTypeNew  = '<div class="label label-success">Công việc</div>';
            } elseif ($aRow['type'] == 4) {
                $htmlType = 'Audit fail';
                $htmlTypeNew  = '<div class="label label-warning">Audit</div>';
            }
            $row[] = '<div class="text-center">' . $stt . '</div>';
            $row[] = '<div class="text-left">' . (!empty($aRow['date']) ? _dt($aRow['date']) : '') . '</div>';
            $row[] = '<div class="text-left">' . $aRow['reference_no'] . '</div>' . $htmlTypeNew;
            $row[] = '<div class="text-left">' . $aRow['name_report'] . '</div>';
            $row[] = '<div class="text-left">' . $htmlType . '</div>';
            $stt++;
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function view_detail_p2($staff_id = 0, $month = 0, $year = 0, $precious = 0)
    {
        $data = [];
        $data['title'] = lang('Xem log vi phạm giảm phần trăm p2');
        $data['month'] = $month;
        $data['year'] = $year;
        $data['staff_id'] = $staff_id;
        $data['precious'] = $precious;
        $this->load->view('admin/kpi/view_detail_p2', $data);
    }

    public function table_detail_p2()
    {
        $staff_id = $this->input->post('staff_id');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $precious = $this->input->post('precious');
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $month_year = $year . '-' . $month;
        $month_year_start = null;
        $month_year_end = null;
        if (!empty($precious)) {
            if ($precious == 1) {
                $month_year_start = $year . '-01';
                $month_year_end = $year . '-03';
            } elseif ($precious == 2) {
                $month_year_start = $year . '-04';
                $month_year_end = $year . '-06';
            } elseif ($precious == 3) {
                $month_year_start = $year . '-07';
                $month_year_end = $year . '-09';
            } elseif ($precious == 4) {
                $month_year_start = $year . '-10';
                $month_year_end = $year . '-12';
            }
        }
        $whereDate = '';
        if (!empty($month_year_start)) {
            $whereDate = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") <= "' . $month_year_end . '"';
        } else {
            $whereDate = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") = "' . $month_year . '"';
        }

        $tb_tamp = "(
            SELECT
                tblproduction_report.id,
                tblproduction_report.date,
                tblproduction_report.reference_no,
                tblproduction_report.name_report,
                tbl_kpi_list_criteria_department.weight as weight
            FROM tblproduction_report
            LEFT JOIN tbl_kpi_list_criteria_department
            ON tbl_kpi_list_criteria_department.id =
               COALESCE(
                   NULLIF(tblproduction_report.kpi_list_criteria_department_id_childd, 0),
                   tblproduction_report.kpi_list_criteria_department_id_child
               )
            WHERE tblproduction_report.id != 0 
            AND tblproduction_report.staff_responsible = $staff_id
            AND tbl_kpi_list_criteria_department.type_p = 2 $whereDate
        ) tb_tamp";

        $aColumns = [
            'tb_tamp.id as id',
            'tb_tamp.date as date',
            'tb_tamp.reference_no as reference_no',
            'tb_tamp.name_report as name_report',
            'tb_tamp.weight as weight'
        ];
        $sIndexColumn = 'id';
        $sTable = $tb_tamp;
        $where = [];

        $filter = [];

        $join = [];


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', [], ['union_all' => true]);

        $output = $result['output'];
        $rResult = $result['rResult'];

        $start = $this->input->post('start');
        $stt = 1;
        foreach ($rResult as $key => $aRow) {
            $row = [];
            $start++;
            $row[] = '<div class="text-center">' . $stt . '</div>';
            $row[] = '<div class="text-left">' . (!empty($aRow['date']) ? _dt($aRow['date']) : '') . '</div>';
            $row[] = '<div class="text-left">' . $aRow['reference_no'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['name_report'] . '</div>';
            $row[] = '<div class="text-center">' . $aRow['weight'] . '%' . '</div>';
            $stt++;
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }
}
