<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Allowance_staff extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->perViewAllowanceToxic = has_permission('allowance_toxic','','view');
        $this->perEditAllowanceToxic = has_permission('allowance_toxic','','edit');
        $this->perViewAllowancePCCC = has_permission('allowance_pccc','','view');
        $this->perEditAllowancePCCC = has_permission('allowance_pccc','','edit');
        $this->perViewAllowanceFSC = has_permission('allowance_fsc','','view');
        $this->perEditAllowanceFSC = has_permission('allowance_fsc','','edit');
        $this->perViewSeniority = has_permission('seniority','','view');
    }

    public function allowance_toxic()
    {
        if (!$this->perViewAllowanceToxic) {
            access_denied('allowance_toxic');
        }
        $data['title'] = _l('dt_allowance_toxic');
        $this->load->view('admin/allowance_staff/allowance_toxic', $data);
    }

    public function getAllowanceToxic()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tblstaff.staffid as id',
            'tblstaff.code as code',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name',
            'tblstaff.date_dochai as date_dochai',
            'tbl_staff_allowance.amount as amount',
        ];
        $sIndexColumn = 'staffid';
        $sTable = 'tblstaff';
        $where = [
            'AND tbl_staff_allowance.category_id = '.ALLOWANCE_DH.' AND tbl_staff_allowance.amount > 0'
        ];
        $filter = [];

        $join = [
            'INNER JOIN tbl_staff_allowance ON tbl_staff_allowance.staff_id = tblstaff.staffid'
        ];


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left">' . $aRow['code'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['name'] . '</div>';
            $row[] = '<div class="text-left">' . (!empty($aRow['date_dochai']) ? _dhau($aRow['date_dochai']) : '') . '</div>';
            $row[] = '<div class="text-right">' . formatMoney($aRow['amount']) . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function import_allowance_toxic() {
        if(!$this->perEditAllowanceToxic){
            accessDenied(true);
        }
        $data['title'] = _l('Import danh sách trợ cấp độc hại');
        $this->load->view('admin/allowance_staff/excel_import_allowance_toxic', $data);
    }


    public function excel_import_allowance_toxic()
    {
        ob_end_clean();
        ini_set('max_execution_time', 800);
        $dataPost = $this->input->post();
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
            // $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('W');
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('AG');
            $arraydata = array();

            $fields = $this->input->post('fields');
            for ($row = 2; $row <= $highestRow; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 2][$col] = $value;
                }
            }

            $dataArray = [];
            foreach ($arraydata as $key => $value) {

                if (empty($value[0])) {
                    $errors .= '<div>Dòng [' . $key . '] Không tìm thấy mã nhân viên</div>';
                    continue;
                }
                $code_staff = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[0])), 'UTF-8');

                if (empty($value[2])) {
                    $errors .= '<div>Dòng [' . $key . '] Không tìm thấy ngày bắt đầu tính phép</div>';
                    continue;
                }

                $date_dochai = ($value[2]);
                if (gettype($date_dochai) == 'double' || gettype($date_dochai) == 'int') {
                    $date_dochai = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($date_dochai));
                } else if (gettype($date_dochai) == 'string') {
                    $date_dochai = to_sql_date($date_dochai);
                }
                if (!isset($value[3])) {
                    $errors .= '<div>Dòng [' . $key . '] Không tìm thấy mức hưởng/ tháng</div>';
                    continue;
                }
                $amount = number_unformat($value[3]);

                if (!empty($code_staff)) {
                    $dtStaff = get_table_where('tblstaff', ['code' => $code_staff], '', 'row_array', '', 'staffid');
                    if (!empty($dtStaff)) {
                        $staffid = $dtStaff['staffid'];
                    } else {
                        $errors .= '<div>Dòng [' . $key . '] Không tìm thấy mã nhân viên trong phần mềm [' . $code_staff . ']</div>';
                        continue;
                    }
                }

                $dataArray[] = [
                    'code_staff' => $code_staff,
                    'date_dochai' => $date_dochai,
                    'amount' => $amount,
                    'staff_id' => $staffid,
                ];
            }
            $count = 0;
            foreach($dataArray as $key => $value) {
               $dtExist = get_table_where('tbl_staff_allowance',['category_id' => ALLOWANCE_DH,'staff_id' => $value['staff_id']],'','row_array');
               $this->db->where('staffid',$value['staff_id']);
               $this->db->update('tblstaff',[
                   'date_dochai' => ($value['date_dochai'])
               ]);
               if (!empty($dtExist)){
                   $this->db->where('id',$dtExist['id']);
                   $success = $this->db->update('tbl_staff_allowance',[
                       'amount' => $value['amount']
                   ]);
                   if ($success){
                       $count ++;
                   }
               } else {
                   $this->db->insert('tbl_staff_allowance',[
                       'category_id' => ALLOWANCE_DH,
                       'staff_id' => $value['staff_id'],
                       'amount' => $value['amount'],
                       'date_created' => date('Y-m-d H:i:s'),
                       'created_by' => get_staff_user_id(),
                   ]);
                   $insert_id = $this->db->insert_id();
                   if ($insert_id){
                       $count ++;
                   }
               }
            }
            echo json_encode(
                [
                    'success' => true,
                    'errors' => $errors,
                    'alert_type' => 'success',
                    'message' => 'Thêm mới và cập nhật thành công ' . $count . ' trợ cấp',
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

    public function allowance_pccc()
    {
        if (!$this->perViewAllowancePCCC) {
            access_denied('allowance_pccc');
        }
        $data['title'] = _l('dt_allowance_pccc');
        $this->load->view('admin/allowance_staff/allowance_pccc', $data);
    }

    public function getAllowancePCCC()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tblstaff.staffid as id',
            'tblstaff.code as code',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name',
            'tblroles.name as name_role',
            'tbl_staff_allowance.amount as amount',
        ];
        $sIndexColumn = 'staffid';
        $sTable = 'tblstaff';
        $where = [
            'AND tbl_staff_allowance.category_id = '.ALLOWANCE_PCCC.' AND tbl_staff_allowance.amount > 0'
        ];
        $filter = [];

        $join = [
            'INNER JOIN tbl_staff_allowance ON tbl_staff_allowance.staff_id = tblstaff.staffid',
            'LEFT JOIN tblroles ON tblroles.roleid = tblstaff.role'
        ];


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left">' . $aRow['code'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['name'] . '</div>';
            $row[] = '<div class="text-left">' . (!empty($aRow['name_role']) ? ($aRow['name_role']) : '') . '</div>';
            $row[] = '<div class="text-right">' . formatMoney($aRow['amount']) . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function import_allowance_pccc() {
        if(!$this->perEditAllowancePCCC){
            accessDenied(true);
        }
        $data['title'] = _l('Import danh sách trợ cấp PCCC');
        $this->load->view('admin/allowance_staff/excel_import_allowance_pccc', $data);
    }

    public function excel_import_allowance_pccc()
    {
        ob_end_clean();
        ini_set('max_execution_time', 800);
        $dataPost = $this->input->post();
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
            // $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('W');
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('AG');
            $arraydata = array();

            $fields = $this->input->post('fields');
            for ($row = 2; $row <= $highestRow; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 2][$col] = $value;
                }
            }

            $dataArray = [];
            foreach ($arraydata as $key => $value) {

                if (empty($value[0])) {
                    $errors .= '<div>Dòng [' . $key . '] Không tìm thấy mã nhân viên</div>';
                    continue;
                }
                $code_staff = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[0])), 'UTF-8');

                if (!isset($value[3])) {
                    $errors .= '<div>Dòng [' . $key . '] Không tìm thấy số tiền</div>';
                    continue;
                }
                $amount = number_unformat($value[3]);

                if (!empty($code_staff)) {
                    $dtStaff = get_table_where('tblstaff', ['code' => $code_staff], '', 'row_array', '', 'staffid');
                    if (!empty($dtStaff)) {
                        $staffid = $dtStaff['staffid'];
                    } else {
                        $errors .= '<div>Dòng [' . $key . '] Không tìm thấy mã nhân viên trong phần mềm [' . $code_staff . ']</div>';
                        continue;
                    }
                }

                $dataArray[] = [
                    'code_staff' => $code_staff,
                    'amount' => $amount,
                    'staff_id' => $staffid,
                ];
            }
            $count = 0;
            foreach($dataArray as $key => $value) {
                $dtExist = get_table_where('tbl_staff_allowance',['category_id' => ALLOWANCE_PCCC,'staff_id' => $value['staff_id']],'','row_array');
                if (!empty($dtExist)){
                    $this->db->where('id',$dtExist['id']);
                    $success = $this->db->update('tbl_staff_allowance',[
                        'amount' => $value['amount']
                    ]);
                    if ($success){
                        $count ++;
                    }
                } else {
                    $this->db->insert('tbl_staff_allowance',[
                        'category_id' => ALLOWANCE_PCCC,
                        'staff_id' => $value['staff_id'],
                        'amount' => $value['amount'],
                        'date_created' => date('Y-m-d H:i:s'),
                        'created_by' => get_staff_user_id(),
                    ]);
                    $insert_id = $this->db->insert_id();
                    if ($insert_id){
                        $count ++;
                    }
                }
            }
            echo json_encode(
                [
                    'success' => true,
                    'errors' => $errors,
                    'alert_type' => 'success',
                    'message' => 'Thêm mới và cập nhật thành công ' . $count . ' trợ cấp',
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

    public function allowance_fsc()
    {
        if (!$this->perViewAllowanceFSC) {
            access_denied('allowance_fsc');
        }
        $data['title'] = _l('dt_allowance_fsc');
        $this->load->view('admin/allowance_staff/allowance_fsc', $data);
    }

    public function getAllowanceFSC()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tblstaff.staffid as id',
            'tblstaff.code as code',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name',
            'tbl_staff_allowance.amount as amount',
        ];
        $sIndexColumn = 'staffid';
        $sTable = 'tblstaff';
        $where = [
            'AND tbl_staff_allowance.category_id = '.ALLOWANCE_FSC.' AND tbl_staff_allowance.amount > 0'
        ];
        $filter = [];

        $join = [
            'INNER JOIN tbl_staff_allowance ON tbl_staff_allowance.staff_id = tblstaff.staffid',
        ];


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left">' . $aRow['code'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['name'] . '</div>';
            $row[] = '<div class="text-right">' . formatMoney($aRow['amount']) . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function import_allowance_fsc() {
        if (!$this->perEditAllowanceFSC)
        $data['title'] = _l('Import danh sách trợ cấp FSC');
        $this->load->view('admin/allowance_staff/excel_import_allowance_fsc', $data);
    }

    public function excel_import_allowance_fsc()
    {
        ob_end_clean();
        ini_set('max_execution_time', 800);
        $dataPost = $this->input->post();
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
            // $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('W');
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('AG');
            $arraydata = array();

            $fields = $this->input->post('fields');
            for ($row = 2; $row <= $highestRow; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 2][$col] = $value;
                }
            }

            $dataArray = [];
            foreach ($arraydata as $key => $value) {

                if (empty($value[0])) {
                    $errors .= '<div>Dòng [' . $key . '] Không tìm thấy mã nhân viên</div>';
                    continue;
                }
                $code_staff = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[0])), 'UTF-8');

                if (!isset($value[2])) {
                    $errors .= '<div>Dòng [' . $key . '] Không tìm thấy số tiền</div>';
                    continue;
                }
                $amount = number_unformat($value[2]);

                if (!empty($code_staff)) {
                    $dtStaff = get_table_where('tblstaff', ['code' => $code_staff], '', 'row_array', '', 'staffid');
                    if (!empty($dtStaff)) {
                        $staffid = $dtStaff['staffid'];
                    } else {
                        $errors .= '<div>Dòng [' . $key . '] Không tìm thấy mã nhân viên trong phần mềm [' . $code_staff . ']</div>';
                        continue;
                    }
                }

                $dataArray[] = [
                    'code_staff' => $code_staff,
                    'amount' => $amount,
                    'staff_id' => $staffid,
                ];
            }
            $count = 0;
            foreach($dataArray as $key => $value) {
                $dtExist = get_table_where('tbl_staff_allowance',['category_id' => ALLOWANCE_FSC,'staff_id' => $value['staff_id']],'','row_array');
                if (!empty($dtExist)){
                    $this->db->where('id',$dtExist['id']);
                    $success = $this->db->update('tbl_staff_allowance',[
                        'amount' => $value['amount']
                    ]);
                    if ($success){
                        $count ++;
                    }
                } else {
                    $this->db->insert('tbl_staff_allowance',[
                        'category_id' => ALLOWANCE_FSC,
                        'staff_id' => $value['staff_id'],
                        'amount' => $value['amount'],
                        'date_created' => date('Y-m-d H:i:s'),
                        'created_by' => get_staff_user_id(),
                    ]);
                    $insert_id = $this->db->insert_id();
                    if ($insert_id){
                        $count ++;
                    }
                }
            }
            echo json_encode(
                [
                    'success' => true,
                    'errors' => $errors,
                    'alert_type' => 'success',
                    'message' => 'Thêm mới và cập nhật thành công ' . $count . ' trợ cấp',
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

    public function seniority()
    {
        if (!$this->perViewSeniority) {
            access_denied('seniority');
        }

        $data['title'] = _l('dt_seniority');
        $this->load->view('admin/allowance_staff/seniority', $data);
    }

    public function getSeniority()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $aColumns = [
            'tblstaff.staffid as id',
            'tblstaff.code as code',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name',
            'tblstaff.day_in as day_in',
            'ROUND(DATEDIFF(CURDATE(),day_in) / 365,2) as seniority'
        ];
        $sIndexColumn = 'staffid';
        $sTable = 'tblstaff';
        $where = [
            'AND ROUND(DATEDIFF(CURDATE(),day_in) / 365,2) >= 5'
        ];
        $filter = [];

        $join = [
        ];


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left">' . $aRow['code'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['name'] . '</div>';
            $row[] = '<div class="text-left">' . (!empty($aRow['day_in']) ? _dhau($aRow['day_in']) : '') . '</div>';
            $row[] = '<div class="text-center">'.$aRow['seniority'].'</div>';
            $row[] = '<div class="text-right">'.formatMoney($aRow['seniority'] * 100000,0).'</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }
}