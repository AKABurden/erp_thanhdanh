<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Categories_maintenance extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('categories_maintenance_model');
    }

    function refrigeration()
    {
        $data = [];
        $data['title'] = lang('Danh Mục Bảo Dưỡng Điện Lạnh');
        $data['type'] = 'refrigeration';
        $this->load->view('admin/categories_maintenance/refrigeration', $data);
    }

    function electricitywater()
    {
        $data = [];
        $data['title'] = lang('Danh Mục Bảo Dưỡng Điện Nước Gia Dụng');
        $data['type'] = 'electricitywater';
        $this->load->view('admin/categories_maintenance/refrigeration', $data);
    }

    function camera()
    {
        $data = [];
        $data['title'] = lang('Danh Mục Bảo Dưỡng Hệ Thống Camera');
        $data['type'] = 'camera';
        $this->load->view('admin/categories_maintenance/refrigeration', $data);
    }

    function ctp()
    {
        $data = [];
        $data['title'] = lang('Danh Mục Bảo Dưỡng Hệ Thống CTP');
        $data['type'] = 'ctp';
        $this->load->view('admin/categories_maintenance/refrigeration', $data);
    }

    function wastewater()
    {
        $data = [];
        $data['title'] = lang('Danh Mục Bảo Dưỡng Hệ Thống Nước Thải');
        $data['type'] = 'wastewater';
        $this->load->view('admin/categories_maintenance/refrigeration', $data);
    }

    function hardware()
    {
        $data = [];
        $data['title'] = lang('Danh Mục Bảo Dưỡng Phần Cứng');
        $data['type'] = 'hardware';
        $this->load->view('admin/categories_maintenance/refrigeration', $data);
    }

    function software()
    {
        $data = [];
        $data['title'] = lang('Danh Mục Bảo Dưỡng Phẩn Mềm');
        $data['type'] = 'software';
        $this->load->view('admin/categories_maintenance/refrigeration', $data);
    }

    function pccc()
    {
        $data = [];
        $data['title'] = lang('Danh Mục Bảo Dưỡng Phòng Cháy Chữa Cháy');
        $data['type'] = 'pccc';
        $this->load->view('admin/categories_maintenance/refrigeration', $data);
    }

    function transportation()
    {
        $data = [];
        $data['title'] = lang('Danh Mục Bảo Dưỡng Phương Tiện Vận Chuyển');
        $data['type'] = 'transportation';
        $this->load->view('admin/categories_maintenance/refrigeration', $data);
    }

    function sever()
    {
        $data = [];
        $data['title'] = lang('Danh Mục Bảo Dưỡng Sever');
        $data['type'] = 'sever';
        $this->load->view('admin/categories_maintenance/refrigeration', $data);
    }

    function laborsafety()
    {
        $data = [];
        $data['title'] = lang('Danh Mục Bảo Dưỡng Thiết Bị An Toàn Lao Động');
        $data['type'] = 'laborsafety';
        $this->load->view('admin/categories_maintenance/refrigeration', $data);
    }

    function testingequipment()
    {
        $data = [];
        $data['title'] = lang('Danh Mục Bảo Dưỡng Thiết Bị Đo Kiểm');
        $data['type'] = 'testingequipment';
        $this->load->view('admin/categories_maintenance/refrigeration', $data);
    }

    function office()
    {
        $data = [];
        $data['title'] = lang('Danh Mục Bảo Dưỡng Thiết Bị Văn Phòng');
        $data['type'] = 'office';
        $this->load->view('admin/categories_maintenance/refrigeration', $data);
    }

    function equipmentproductivity()
    {
        $data = [];
        $data['title'] = lang('Năng Suất Thiết Bị');
        $data['type'] = 'equipmentproductivity';
        $this->load->view('admin/categories_maintenance/refrigeration', $data);
    }

    public function getCategoriesMaintenance()
    {
        $type_search = $this->input->post('type_search');
        $aColumns = ['tbl_categories_maintenance.id as id',
                     'tbl_categories_maintenance.code as code_categories_maintenance',
                     'tbl_category_machines.code as code_group',
                     'tbl_category_machines.name as name_group',
                     'tbl_machines.code as code_machines',
                     'tbl_machines.name as name_machines',
                     'tbl_machines_maintenance_h.name as name_machines_maintenance',
                     'tbl_category_maintenance.name as group_bd',
                     'tbl_categories_maintenance.detail as detail',
                     'tbl_categories_maintenance.quantity as quantity',
                     'tbl_categories_maintenance.date_regulations as date_regulations',
                     'tbl_categories_maintenance.date_start as date_start',
                     'tbl_categories_maintenance.date_renewals as date_renewals',
                     'tbl_suggest_maintenance.reference_no as code_suggest_maintenance'
                     , '"" as actions'];
        $sIndexColumn = 'id';
        $sTable = 'tbl_categories_maintenance';
        $where = [];
        $filter = [];
        $join = [
            'LEFT JOIN tbl_category_machines ON tbl_category_machines.id = tbl_categories_maintenance.id_category_machines',
            'LEFT JOIN tbl_machines ON tbl_machines.id = tbl_categories_maintenance.id_machines',
            'LEFT JOIN tbl_machines_maintenance_h ON tbl_machines_maintenance_h.id = tbl_categories_maintenance.id_maintenance_department',
            'LEFT JOIN tbl_category_maintenance ON tbl_category_maintenance.id = tbl_categories_maintenance.id_category_maintenance',
            'LEFT JOIN tbl_suggest_maintenance ON tbl_suggest_maintenance.id = tbl_categories_maintenance.suggest_maintenance_id'
        ];
        array_push($where, " AND tbl_categories_maintenance.type = '" . $type_search . "'");
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);
        $quantity_total = 0;
        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $quantity_total += $aRow['quantity'];
            $start++;
            $row = [];
            $id = $aRow['id'];
            $edit = '<a class="tnh-modal" href="' . base_url('admin/categories_maintenance/handling/' . $id . '/' . $type_search) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/categories_maintenance/delete/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
                    // $_data = '<div class="text-center">
                    //     <div class="checkbox checkbox-info">
                    //         <input type="checkbox" name="standard_id[]" id="check-item' . $id . '" value="' . $id . '">
                    //         <label for="check-item' . $id . '"></label>
                    //     </div>
                    // </div>';
                } else if ($v == 'date_start' || $v == 'date_renewals') {
                    $_data = '<div class="text-center">' . _dt($_data) . '</div>';
                } else {
                    $_data = '<div class="text-center">' . $_data . '</div>';
                }
                $row[] = $_data;
            }
            $output['aaData'][] = $row;
        }
        $output['quantity_total'] = $quantity_total;
        echo json_encode($output);
    }

    public function handling($id = 0, $type = '')
    {
        $data = [];
        $categories_maintenance = $id ? $this->categories_maintenance_model->getCategoriesMaintenanceById($id) : [];
        if ($this->input->post()) {
            $this->form_validation->set_rules('machines_id', lang("Thiết bị"), 'required');
            if ($this->form_validation->run() == true) {
                $id_category_machines = _string($this->input->post('category_machine_id'));
                $id_machines = ($this->input->post('machines_id'));
                $id_maintenance_department = ($this->input->post('maintenance_department'));
                $id_category_maintenance = ($this->input->post('category_maintenance'));
                $suggest_maintenance_id = ($this->input->post('suggest_maintenance_id'));
                $code = ($this->input->post('code'));
                $detail = ($this->input->post('detail'));
                $date_start = to_sql_date($this->input->post('date_start'), true);
                $date_renewals = to_sql_date($this->input->post('date_renewals'), true);
                $quantity = number_unformat($this->input->post('quantity'));
                $date_regulations = number_unformat($this->input->post('date_regulations'));
                $option = [
                    'type' => $type,
                    'id_machines' => $id_machines,
                    'id_category_machines' => $id_category_machines,
                    'id_maintenance_department' => $id_maintenance_department,
                    'id_category_maintenance' => $id_category_maintenance,
                    'detail' => $detail, 'date_start' => $date_start,
                    'date_renewals' => $date_renewals,
                    'quantity' => $quantity,
                    'date_regulations' => $date_regulations,
                    'suggest_maintenance_id' => $suggest_maintenance_id,
                    'code' => $code,
                ];
                if ($id) {
                    $option['staff_update'] = get_staff_user_id();
                    $option['date_update'] = date('Y-m-d H:i:s');
                    $ins = $this->categories_maintenance_model->updateCategoriesMaintenance($id, $option);
                    $standard_id = $id;
                } else {
                    $option['staff_create'] = get_staff_user_id();
                    $option['date_create'] = date('Y-m-d H:i:s');
                    $ins = $this->categories_maintenance_model->insertCategoriesMaintenance($option);
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
        $data['dtData'] = $categories_maintenance;
        $title = '';
        if ($type == 'refrigeration') {
            $title = $id ? lang('Sửa Danh Mục Bảo Dưỡng Điện Lạnh') : lang('Thêm Danh Mục Bảo Dưỡng Điện Lạnh');
        } else if ($type == 'electricitywater') {
            $title = $id ? lang('Sửa Danh Mục Bảo Dưỡng Điện Nước Gia Dụng') : lang('Thêm Danh Mục Bảo Dưỡng Điện Nước Gia Dụng');
        } else if ($type == 'camera') {
            $title = $id ? lang('Sửa Danh Mục Bảo Dưỡng Hệ Thống Camera') : lang('Thêm Danh Mục Bảo Dưỡng Hệ Thống Camera');
        } else if ($type == 'ctp') {
            $title = $id ? lang('Sửa Danh Mục Bảo Dưỡng Hệ Thống CTP') : lang('Thêm Danh Mục Bảo Dưỡng Hệ Thống CTP');
        } else if ($type == 'wastewater') {
            $title = $id ? lang('Sửa Danh Mục Bảo Dưỡng Hệ Thống Nước Thải') : lang('Thêm Danh Mục Bảo Dưỡng Hệ Thống Nước Thải');
        } else if ($type == 'hardware') {
            $title = $id ? lang('Sửa Danh Mục Bảo Dưỡng Phần Cứng') : lang('Thêm Danh Mục Bảo Dưỡng Phần Cứng');
        } else if ($type == 'software') {
            $title = $id ? lang('Sửa Danh Mục Bảo Dưỡng Phẩn Mềm') : lang('Thêm Danh Mục Bảo Dưỡng Phẩn Mềm');
        } else if ($type == 'pccc') {
            $title = $id ? lang('Sửa Danh Mục Bảo Dưỡng Phòng Cháy Chữa Cháy') : lang('Thêm Danh Mục Bảo Dưỡng Phòng Cháy Chữa Cháy');
        } else if ($type == 'transportation') {
            $title = $id ? lang('Sửa Danh Mục Bảo Dưỡng Phương Tiện Vận Chuyển') : lang('Thêm Danh Mục Bảo Dưỡng Phương Tiện Vận Chuyển');
        } else if ($type == 'sever') {
            $title = $id ? lang('Sửa Danh Mục Bảo Dưỡng Sever') : lang('Thêm Danh Mục Bảo Dưỡng Sever');
        } else if ($type == 'laborsafety') {
            $title = $id ? lang('Sửa Danh Mục Bảo Dưỡng Thiết Bị An Toàn Lao Động') : lang('Thêm Danh Mục Bảo Dưỡng Thiết Bị An Toàn Lao Động');
        } else if ($type == 'testingequipment') {
            $title = $id ? lang('Sửa Danh Mục Bảo Dưỡng Thiết Bị Đo Kiểm') : lang('Thêm Danh Mục Bảo Dưỡng Thiết Bị Đo Kiểm');
        } else if ($type == 'office') {
            $title = $id ? lang('Sửa Danh Mục Bảo Dưỡng Thiết Bị Văn Phòng') : lang('Thêm Danh Mục Bảo Dưỡng Thiết Bị Văn Phòng');
        } else if ($type == 'equipmentproductivity') {
            $title = $id ? lang('Sửa Năng Suất Thiết Bị') : lang('Thêm Năng Suất Thiết Bị');
        }
        if (!empty($type)) {
            $this->db->where('is_type', $type);
        }
        $data['dtCategoryMachines'] = $this->db->get('tbl_category_machines')->result_array();
        $data['dtCategoryMaintenance'] = get_table_where('tbl_category_maintenance');
        $data['dtTypeMaintenance'] = get_table_where('tbl_type_maintenance');
        $data['dtDepartment'] = get_table_where('tbldepartments');
        $value = !empty($categories_maintenance) ? $categories_maintenance['id_machines'] : 0;
        $data['dtMaintenanceDepartment'] = get_table_where('tbl_machines_maintenance_h', ['machines_id' => $value]);
        $data['title'] = $title;
        $data['suggest_maintenance_id'] = $this->db->get_where('tbl_suggest_maintenance')->result_array();
        $this->load->view('admin/categories_maintenance/handling', $data);
    }

    public function searchMachines($id = 0)
    {
        $term = $this->input->get('term');
        $category_machine_id = $this->input->get('params');
        if ($category_machine_id == 0) {
            $category_machine_id = -1;
        }
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_machines.id as id, 
            tbl_machines.name as text,
            tbl_machines.code as code,
            tbl_machines.name as name,
        ', false);
        $this->db->from('tbl_machines');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_machines.name', $term);
            $this->db->or_like('tbl_machines.code', $term);
            $this->db->group_end();
        }
        $this->db->where('tbl_machines.category_machine_id', $category_machine_id);
        $this->db->limit($limit);
        $pod = $this->db->get()->result_array();
        $data['results'][] = ['text' => lang('Thiết bị'), 'children' => $pod];
        if (!empty($id)) {
            $dtMachines = get_table_where('tbl_machines', ['id' => $id], '', 'row_array');
            $data['row'] = ['id' => $dtMachines['id'], 'text' => $dtMachines['name']];
        }
        echo json_encode($data);
    }

    public function getMaintenaceMachines()
    {
        $data = [];
        $machines_id = $this->input->post('machines_id');
        $this->db->select('tbl_machines_maintenance_h.*');
        $this->db->from('tbl_machines_maintenance_h');
        $this->db->where('tbl_machines_maintenance_h.machines_id', $machines_id);
        $dtData = $this->db->get()->result_array();
        $string_option = "<option></option>";
        foreach ($dtData as $key => $value) {
            $string_option .= '<option value="' . $value['id'] . '">' . $value['name'] . '</option>';
        }
        echo json_encode($string_option);
    }

    public function delete($id)
    {
        $data = [];
        if ($this->categories_maintenance_model->deleteCategoriesMaintenance($id)) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    function modal_excel($type = '')
    {
        // $data['title'] = _l('Import excel máy móc');
        $title = '';
        if ($type == 'refrigeration') {
            $title = lang('Import Danh Mục Bảo Dưỡng Điện Lạnh');
        } else if ($type == 'electricitywater') {
            $title = lang('Import Danh Mục Bảo Dưỡng Điện Nước Gia Dụng');
        } else if ($type == 'camera') {
            $title = lang('Import Danh Mục Bảo Dưỡng Hệ Thống Camera');
        } else if ($type == 'ctp') {
            $title = lang('Import Danh Mục Bảo Dưỡng Hệ Thống CTP');
        } else if ($type == 'wastewater') {
            $title = lang('Import Danh Mục Bảo Dưỡng Hệ Thống Nước Thải');
        } else if ($type == 'hardware') {
            $title = lang('Import Danh Mục Bảo Dưỡng Phần Cứng');
        } else if ($type == 'software') {
            $title = lang('Import Danh Mục Bảo Dưỡng Phẩn Mềm');
        } else if ($type == 'pccc') {
            $title = lang('Import Danh Mục Bảo Dưỡng Phòng Cháy Chữa Cháy');
        } else if ($type == 'transportation') {
            $title = lang('Import Danh Mục Bảo Dưỡng Phương Tiện Vận Chuyển');
        } else if ($type == 'sever') {
            $title = lang('Import Danh Mục Bảo Dưỡng Sever');
        } else if ($type == 'laborsafety') {
            $title = lang('Import Danh Mục Bảo Dưỡng Thiết Bị An Toàn Lao Động');
        } else if ($type == 'testingequipment') {
            $title = lang('Import Danh Mục Bảo Dưỡng Thiết Bị Đo Kiểm');
        } else if ($type == 'office') {
            $title = lang('Import Danh Mục Bảo Dưỡng Thiết Bị Văn Phòng');
        } else if ($type == 'equipmentproductivity') {
            $title = lang('Import Năng Suất Thiết Bị');
        }
        $data['title'] = $title;
        $data['type'] = $type;
        $this->load->view('admin/categories_maintenance/import_excel', $data);
    }

    public function import_categories_maintenance($type = '')
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
            $listRow = [0 => 'code_group', 1 => 'code_machines', 2 => 'code_machines_maintenance', 3 => 'code_group_bd', 4 => 'detail', 5 => 'quantity', 6 => 'date_regulations', 7 => 'date_start', 8 => 'date_renewals',];
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
                $id_category_machines = 0;
                $id_machines = 0;
                $id_maintenance_department = 0;
                $id_category_maintenance = 0;
                $detail = 0;
                $quantity = 0;
                $date_regulations = 0;
                $date_start = NULL;
                $date_renewals = NULL;
                foreach ($value as $k => $v) {
                    if ($k == 'detail') {
                        $detail = $v;
                    }
                    if ($k == 'quantity') {
                        $quantity = number_unformat($v);
                    }
                    if ($k == 'date_regulations') {
                        $date_regulations = number_unformat($v);
                    }
                    if ($k == 'date_start') {
                        if (!empty($v)) {
                            $date_start = to_sql_date($v);
                        }
                    }
                    if ($k == 'date_renewals') {
                        if (!empty($v)) {
                            $date_renewals = to_sql_date($v);
                        }
                    }
                    if ($k == 'code_group') {
                        $ktr_code_group = get_table_where('tbl_category_machines', ['code' => $v], '', 'row_array');
                        if (empty($ktr_code_group)) {
                            $errors .= '<div>Dòng [' . ($dem) . '] Không tìm thấy nhóm thiết bị</div>';
                            $checkerrors = 1;
                        } else {
                            $id_category_machines = $ktr_code_group['id'];
                        }
                    }
                    if ($k == 'code_machines') {
                        $ktr_code_machines = get_table_where('tbl_machines', ['code' => $v, 'category_machine_id' => $id_category_machines], '', 'row_array');
                        if (empty($ktr_code_machines)) {
                            $errors .= '<div>Dòng [' . ($dem) . '] Không tìm thấy thiết bị</div>';
                            $checkerrors = 1;
                        } else {
                            $id_machines = $ktr_code_machines['id'];
                        }
                    }
                    if ($k == 'code_machines_maintenance') {
                        $ktr_code_machines_maintenance = get_table_where('tbl_machines_maintenance_h', ['name' => $v, 'machines_id' => $id_machines], '', 'row_array');
                        if (empty($ktr_code_machines_maintenance)) {
                            $errors .= '<div>Dòng [' . ($dem) . '] Không tìm thấy bộ phận bảo dưỡng thuộc thiết bị</div>';
                            $checkerrors = 1;
                        } else {
                            $id_maintenance_department = $ktr_code_machines_maintenance['id'];
                        }
                    }
                    if ($k == 'code_group_bd') {
                        $ktr_code_group_bd = get_table_where('tbl_category_maintenance', ['code' => $v], '', 'row_array');
                        if (empty($ktr_code_group_bd)) {
                            $errors .= '<div>Dòng [' . ($dem) . '] Không tìm thấy nhóm bảo dưỡng</div>';
                            $checkerrors = 1;
                        } else {
                            $id_category_maintenance = $ktr_code_group_bd['id'];
                        }
                    }
                }
                if ($checkerrors == 0) {
                    $this->db->where('type', $type);
                    $this->db->where('id_machines', $id_machines);
                    $this->db->where('id_category_machines', $id_category_machines);
                    $this->db->where('id_maintenance_department', $id_maintenance_department);
                    $this->db->where('id_category_maintenance', $id_category_maintenance);
                    $check_id = $this->db->get('tbl_categories_maintenance')->row_array();
                    $id = '';
                    if (!empty($check_id)) {
                        $id = $check_id['id'];
                    }
                    $option = ['type' => $type, 'id_machines' => $id_machines, 'id_category_machines' => $id_category_machines, 'id_maintenance_department' => $id_maintenance_department, 'id_category_maintenance' => $id_category_maintenance, 'detail' => $detail, 'date_start' => $date_start, 'date_renewals' => $date_renewals, 'quantity' => $quantity, 'date_regulations' => $date_regulations,];
                    if ($id) {
                        $option['staff_update'] = get_staff_user_id();
                        $option['date_update'] = date('Y-m-d H:i:s');
                        $ins = $this->categories_maintenance_model->updateCategoriesMaintenance($id, $option);
                    } else {
                        $option['staff_create'] = get_staff_user_id();
                        $option['date_create'] = date('Y-m-d H:i:s');
                        $ins = $this->categories_maintenance_model->insertCategoriesMaintenance($option);
                    }
                    if (!empty($ins)) {
                        $count++;
                    }
                }
            }
        }
        echo json_encode(['success' => true, 'alert_type' => 'success', 'errors' => $errors, 'message' => 'Import thành công ' . $count . ' Items',]);
        die();
    }

    function equipment_consumption()
    {
        $data = [];
        $data['title'] = lang('Danh Sách Định Mức Tiêu Hao Thiết Bị');
        $data['type'] = 'equipment_consumption';
        $this->load->view('admin/categories_maintenance/equipment_consumption', $data);
    }

    function replacement_supplies()
    {
        $data = [];
        $data['title'] = lang('Danh Sách Định Mức Vật Tư Thay Thế');
        $data['type'] = 'replacement_supplies';
        $this->load->view('admin/categories_maintenance/equipment_consumption', $data);
    }

    public function getEquipment_consumption()
    {
        $type_search = $this->input->post('type_search');
        $aColumns = ['tbl_equipment_consumption.id as id', 'tbl_category_machines.code as code_group', 'tbl_category_machines.name as name_group', 'tbl_machines.code as code_machines', 'tbl_machines.name as name_machines', 'tbl_equipment_consumption.detail as detail', '"" as actions'];
        $sIndexColumn = 'id';
        $sTable = 'tbl_equipment_consumption';
        $where = [];
        $filter = [];
        $join = ['LEFT JOIN tbl_category_machines ON tbl_category_machines.id = tbl_equipment_consumption.id_category_machines', 'LEFT JOIN tbl_machines ON tbl_machines.id = tbl_equipment_consumption.id_machines',];
        array_push($where, " AND tbl_equipment_consumption.type = '" . $type_search . "'");
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);
        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];
            $edit = '<a class="tnh-modal" href="' . base_url('admin/categories_maintenance/handling_equipment_consumption/' . $id . '/' . $type_search) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/categories_maintenance/deleteEquipment_consumption/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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

    function handling_equipment_consumption($id = 0, $type = '')
    {
        $data = [];
        $categories_maintenance = $id ? $this->categories_maintenance_model->getEquipmentConsumptionById($id) : [];
        if ($this->input->post()) {
            $this->form_validation->set_rules('machines_id', lang("Thiết bị"), 'required');
            if ($this->form_validation->run() == true) {
                $id_category_machines = _string($this->input->post('category_machine_id'));
                $id_machines = ($this->input->post('machines_id'));
                $detail = ($this->input->post('detail'));
                $option = ['type' => $type, 'id_machines' => $id_machines, 'id_category_machines' => $id_category_machines, 'detail' => $detail,];
                if ($id) {
                    $option['staff_update'] = get_staff_user_id();
                    $option['date_update'] = date('Y-m-d H:i:s');
                    $ins = $this->categories_maintenance_model->updateEquipmentConsumption($id, $option);
                    $standard_id = $id;
                } else {
                    $option['staff_create'] = get_staff_user_id();
                    $option['date_create'] = date('Y-m-d H:i:s');
                    $ins = $this->categories_maintenance_model->insertEquipmentConsumption($option);
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
        $data['dtData'] = $categories_maintenance;
        $title = '';
        if ($type == 'equipment_consumption') {
            $title = $id ? lang('Sửa Định Mức Tiêu Hao Thiết Bị') : lang('Thêm Định Mức Tiêu Hao Thiết Bị');
        } else if ($type == 'replacement_supplies') {
            $title = $id ? lang('Sửa Định Mức Vật Tư Thay Thế') : lang('Thêm Định Mức Vật Tư Thay Thế');
        }
        $data['dtCategoryMachines'] = $this->db->get('tbl_category_machines')->result_array();
        $data['dtCategoryMaintenance'] = get_table_where('tbl_category_maintenance');
        $data['dtTypeMaintenance'] = get_table_where('tbl_type_maintenance');
        $data['dtDepartment'] = get_table_where('tbldepartments');
        $value = !empty($categories_maintenance) ? $categories_maintenance['id_machines'] : 0;
        $data['dtMaintenanceDepartment'] = get_table_where('tbl_machines_maintenance_h', ['machines_id' => $value]);
        $data['title'] = $title;
        $this->load->view('admin/categories_maintenance/handling_equipment_consumption', $data);
    }

    function modal_excel_equipment_consumption($type = '')
    {
        $title = '';
        if ($type == 'equipment_consumption') {
            $title = lang('Import Định Mức Tiêu Hao Thiết Bị');
        } else if ($type == 'replacement_supplies') {
            $title = lang('Import Định Mức Vật Tư Thay Thế');
        }
        $data['title'] = $title;
        $data['type'] = $type;
        $this->load->view('admin/categories_maintenance/import_excel_equipment_consumption', $data);
    }

    public function import_equipment_consumption($type = '')
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
            $listRow = [0 => 'code_group', 1 => 'code_machines', 2 => 'detail',];
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
                $id_category_machines = 0;
                $id_machines = 0;
                $id_maintenance_department = 0;
                $id_category_maintenance = 0;
                $detail = 0;
                $quantity = 0;
                $date_regulations = 0;
                $date_start = NULL;
                $date_renewals = NULL;
                foreach ($value as $k => $v) {
                    if ($k == 'detail') {
                        $detail = $v;
                    }
                    if ($k == 'code_group') {
                        $ktr_code_group = get_table_where('tbl_category_machines', ['code' => $v], '', 'row_array');
                        if (empty($ktr_code_group)) {
                            $errors .= '<div>Dòng [' . ($dem) . '] Không tìm thấy nhóm thiết bị</div>';
                            $checkerrors = 1;
                        } else {
                            $id_category_machines = $ktr_code_group['id'];
                        }
                    }
                    if ($k == 'code_machines') {
                        $ktr_code_machines = get_table_where('tbl_machines', ['code' => $v, 'category_machine_id' => $id_category_machines], '', 'row_array');
                        if (empty($ktr_code_machines)) {
                            $errors .= '<div>Dòng [' . ($dem) . '] Không tìm thấy thiết bị</div>';
                            $checkerrors = 1;
                        } else {
                            $id_machines = $ktr_code_machines['id'];
                        }
                    }
                }
                if ($checkerrors == 0) {
                    $this->db->where('type', $type);
                    $this->db->where('id_machines', $id_machines);
                    $this->db->where('id_category_machines', $id_category_machines);
                    $check_id = $this->db->get('tbl_equipment_consumption')->row_array();
                    $id = '';
                    if (!empty($check_id)) {
                        $id = $check_id['id'];
                    }
                    $option = ['type' => $type, 'id_machines' => $id_machines, 'id_category_machines' => $id_category_machines, 'detail' => $detail,];
                    if ($id) {
                        $option['staff_update'] = get_staff_user_id();
                        $option['date_update'] = date('Y-m-d H:i:s');
                        $ins = $this->categories_maintenance_model->insertEquipmentConsumption($id, $option);
                    } else {
                        $option['staff_create'] = get_staff_user_id();
                        $option['date_create'] = date('Y-m-d H:i:s');
                        $ins = $this->categories_maintenance_model->insertEquipmentConsumption($option);
                    }
                    if (!empty($ins)) {
                        $count++;
                    }
                }
            }
        }
        echo json_encode(['success' => true, 'alert_type' => 'success', 'errors' => $errors, 'message' => 'Import thành công ' . $count . ' Items',]);
        die();
    }

    function imported_documents()
    {
        $data = [];
        $data['title'] = lang('Danh Mục Chứng Từ Nhập Khẩu');
        $data['type'] = 'imported_documents';
        $this->load->view('admin/categories_maintenance/import_export', $data);
    }

    function exportd_documents()
    {
        $data = [];
        $data['title'] = lang('Danh Mục Chứng Từ Xuất Khẩu');
        $data['type'] = 'exportd_documents';
        $this->load->view('admin/categories_maintenance/import_export', $data);
    }

    public function getImportedExport()
    {
        $type_search = $this->input->post('type_search');
        $aColumns = ['tbl_import_export.id as id', 'tbl_import_export.code as code_group', 'tbl_import_export.name as name_group', 'tbl_import_export.categories as categories', '"" as actions'];
        $sIndexColumn = 'id';
        $sTable = 'tbl_import_export';
        $where = [];
        $filter = [];
        $join = [];
        array_push($where, " AND tbl_import_export.type = '" . $type_search . "'");
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);
        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];
            $edit = '<a class="tnh-modal" href="' . base_url('admin/categories_maintenance/handling_import_export/' . $id . '/' . $type_search) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/categories_maintenance/delete_import_export/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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

    function handling_import_export($id = 0, $type = '')
    {
        $data = [];
        $categories_maintenance = $id ? $this->categories_maintenance_model->getImportedExportById($id) : [];
        if ($this->input->post()) {
            $this->form_validation->set_rules('code', lang("Mã"), 'required');
            if ($this->form_validation->run() == true) {
                $code = _string($this->input->post('code'));
                $name = ($this->input->post('name'));
                $categories = ($this->input->post('categories'));
                $option = ['type' => $type, 'code' => $code, 'name' => $name, 'categories' => $categories,];
                if ($id) {
                    $option['staff_update'] = get_staff_user_id();
                    $option['date_update'] = date('Y-m-d H:i:s');
                    $ins = $this->categories_maintenance_model->updateImportedExport($id, $option);
                    $standard_id = $id;
                } else {
                    $option['staff_create'] = get_staff_user_id();
                    $option['date_create'] = date('Y-m-d H:i:s');
                    $ins = $this->categories_maintenance_model->insertImportedExport($option);
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
        $data['dtData'] = $categories_maintenance;
        $title = '';
        if ($type == 'imported_documents') {
            $title = $id ? lang('Sửa Danh Mục Chứng Từ Nhập Khẩu') : lang('Thêm Danh Mục Chứng Từ Nhập Khẩu');
        } else if ($type == 'exportd_documents') {
            $title = $id ? lang('Sửa Danh Mục Chứng Từ Xuất Khẩu') : lang('Thêm Danh Mục Chứng Từ Xuất Khẩu');
        }
        $data['dtCategoryMachines'] = $this->db->get('tbl_category_machines')->result_array();
        $data['dtCategoryMaintenance'] = get_table_where('tbl_category_maintenance');
        $data['dtTypeMaintenance'] = get_table_where('tbl_type_maintenance');
        $data['dtDepartment'] = get_table_where('tbldepartments');
        $data['title'] = $title;
        $this->load->view('admin/categories_maintenance/handling_import_export', $data);
    }

    function modal_excel_import_export($type = '')
    {
        $title = '';
        if ($type == 'imported_documents') {
            $title = lang('Import Danh Mục Chứng Từ Nhập Khẩu');
        } else if ($type == 'exportd_documents') {
            $title = lang('Import Danh Mục Chứng Từ Xuất Khẩu');
        }
        $data['title'] = $title;
        $data['type'] = $type;
        $this->load->view('admin/categories_maintenance/import_excel_import_export', $data);
    }

    public function import_import_export($type = '')
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
            $listRow = [0 => 'code', 1 => 'name', 2 => 'categories',];
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
                    $this->db->where('type', $type);
                    $this->db->where('code', $code);
                    $check_id = $this->db->get('tbl_import_export')->row_array();
                    $id = '';
                    if (!empty($check_id)) {
                        $id = $check_id['id'];
                    }
                    $option = ['type' => $type, 'code' => $code, 'name' => $name, 'categories' => $categories,];
                    if ($id) {
                        $option['staff_update'] = get_staff_user_id();
                        $option['date_update'] = date('Y-m-d H:i:s');
                        $ins = $this->categories_maintenance_model->updateImportedExport($id, $option);
                    } else {
                        $option['staff_create'] = get_staff_user_id();
                        $option['date_create'] = date('Y-m-d H:i:s');
                        $ins = $this->categories_maintenance_model->insertImportedExport($option);
                    }
                    if (!empty($ins)) {
                        $count++;
                    }
                }
            }
        }
        echo json_encode(['success' => true, 'alert_type' => 'success', 'errors' => $errors, 'message' => 'Import thành công ' . $count . ' Items',]);
        die();
    }

    public function delete_import_export($id)
    {
        $data = [];
        if ($this->categories_maintenance_model->deleteImportedExport($id)) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function deleteEquipment_consumption($id)
    {
        $data = [];
        if ($this->categories_maintenance_model->deleteEquipmentConsumption($id)) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }
}
