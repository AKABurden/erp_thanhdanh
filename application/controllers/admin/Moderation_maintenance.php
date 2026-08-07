<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Moderation_maintenance extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->preViewModerationMaintenance = true;
        $this->preViewOwnModerationMaintenance = true;
        $this->preAddModerationMaintenance = true;
        $this->preEditModerationMaintenance = true;
        $this->preApproveModerationMaintenance = true;
        $this->preDeleteModerationMaintenance = true;
    }

    public function index()
    {
        if (!$this->preViewModerationMaintenance && !$this->preViewOwnModerationMaintenance) {
            access_denied();
        }
        $data['title'] = _l('ch_moderation_maintenance');
        $this->load->view('admin/moderation_maintenance/index', $data);
    }
    public function getModerationMaintenance()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_suggest_maintenance_item.id as id',
            'tbl_suggest_maintenance.reference_no as reference_no',
            'tbl_suggest_maintenance.date as date',
            'tbl_type_maintenance.name as name_type_maintenance',
            'tbl_category_maintenance.name as name_category_maintenance',
            'tbl_machines_maintenance.name as name_machines_maintenance',
            'tbldepartments.name as name_department',
            'tbl_suggest_maintenance.detail as detail',
            'tbl_suggest_maintenance.quantity as quantity',
            'tbl_machines.code as code_machines',
            'tbl_machines.name as name_machines',
            'tblbranch.name as name_branch',
            'tbl_result.name as name_result',
            '(SELECT GROUP_CONCAT(tblproduction_report.name_report)
                FROM tblproduction_report
                WHERE tblproduction_report.object_id = tbl_suggest_maintenance_item.id AND tblproduction_report.object_type = "suggest_maintenance"
            ) as name_report',
            'tbl_suggest_maintenance_item.expected_date as expected_date',
            'tbl_suggest_maintenance_item.start_date as start_date',
            'tbl_suggest_maintenance_item.end_date as end_date',
            'tbl_suggest_maintenance_item.standard as standard'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_maintenance';
        $where = [];
        $filter = [];
        $join = [
            'INNER JOIN tblbranch ON tblbranch.id = tbl_suggest_maintenance.branch_id',
            'LEFT JOIN tbl_machines ON tbl_machines.id = tbl_suggest_maintenance.machines_id',
            'LEFT JOIN tbl_suggest_maintenance_item ON tbl_suggest_maintenance_item.suggest_maintenance_id = tbl_suggest_maintenance.id',
            'LEFT JOIN tbl_type_maintenance ON tbl_type_maintenance.id = tbl_suggest_maintenance.type_maintenance',
            'LEFT JOIN tbl_category_maintenance ON tbl_category_maintenance.id = tbl_suggest_maintenance.category_maintenance',
            'LEFT JOIN tbl_machines_maintenance ON tbl_machines_maintenance.id = tbl_suggest_maintenance_item.machines_maintenance_id',
            'LEFT JOIN tbldepartments ON tbldepartments.departmentid = tbl_suggest_maintenance.department_id',
            'LEFT JOIN tbl_result ON tbl_result.id = tbl_suggest_maintenance_item.result_id',
        ];

        if (!$this->preViewModerationMaintenance) {
            array_push($where, 'AND (tbl_suggest_maintenance.created_by = ' . get_staff_user_id() . ')');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_suggest_maintenance.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_suggest_maintenance.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tbl_suggest_maintenance.id as idmain'], 'GROUP BY tbl_suggest_maintenance_item.id');

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_maintenance/view/' . $aRow['idmain']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-center" style="width: 80px">' . _d($aRow['date']) . '</div>';

            $row[] = '<div class="text-center" style="width: 120px">' . ($aRow['name_type_maintenance']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['name_category_maintenance']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['name_machines_maintenance']) . '</div>';
            $row[] = '<div class="text-left" style="width: 150px">' . ($aRow['name_department']) . '</div>';

            $row[] = '<div class="text-left" style="width: 150px">' . ($aRow['detail']) . '</div>';
            $row[] = '<div class="text-center" style="width: 150px">' . formatNumber($aRow['quantity']) . '</div>';
            $row[] = '<div class="text-left" style="width: 150px">' . ($aRow['code_machines']) . '</div>';
            $row[] = '<div class="text-left" style="width: 150px">' . ($aRow['name_machines']) . '</div>';
            $row[] = '<div class="text-left" style="width: 150px">' . ($aRow['name_branch']) . '</div>';
            $row[] = '<div class="text-left" style="width: 150px">' . ($aRow['name_result']) . '</div>';
            $arrReport = $aRow['name_report'];
            $htmlReport = '';
            if (!empty($arrReport)) {
                $arrReport = explode('||', $arrReport);
                if (!empty($arrReport)) {
                    foreach ($arrReport as $kk => $vv) {
                        $vv = explode('__', $vv);
                        $htmlReport .= $vv[0] . ',';
                    }
                }
            }
            $row[] = '<div class="text-left" style="width: 150px">' . ($htmlReport) . '</div>';
            $row[] = '<div><input type="text" style="width: 150px;" onchange="updateDate(this,' . $aRow['id'] . ', \'expected_date\')" name="expected_date" class="form-control datetimepicker " value="' . (!empty($aRow['expected_date']) ? date_format(date_create($aRow['expected_date']), 'd/m/Y H:i') : '') . '"></div>';
            $row[] = '<div><input type="text" style="width: 150px;" onchange="updateDate(this,' . $aRow['id'] . ', \'start_date\')" name="start_date" class="form-control datetimepicker " value="' . (!empty($aRow['start_date']) ? date_format(date_create($aRow['start_date']), 'd/m/Y H:i') : '') . '"></div>';
            $row[] = '<div><input type="text" style="width: 150px;" onchange="updateDate(this,' . $aRow['id'] . ', \'end_date\')" name="end_date" class="form-control datetimepicker " value="' . (!empty($aRow['end_date']) ? date_format(date_create($aRow['end_date']), 'd/m/Y H:i') : '') . '"></div>';

            $row[] = '<div class="text-left" style="width: 150px">' . ($aRow['standard']) . '</div>';

            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }
    function updateDate()
    {
        $data = $this->input->post();
        if (!empty($data['id']) && !empty($data['_value']) && !empty($data['name'])) {
            $ins = [];
            $ins[$data['name']] = to_sql_date($data['_value'], true);
            $this->db->where('id', $data['id']);
            $result = $this->db->update('tbl_suggest_maintenance_item', $ins);
            if (!empty($result)) {
                $data['result'] = 1;
                $data['message'] = lang('Cập nhật thành công');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('Cập nhật thất bại');
            }
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Cập nhật thất bại');
        }
        echo json_encode($data);
    }
    public function exportExcel()
    {
        $columsExcel = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
            'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ',
            'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ',
            'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ'
        ];
        if ($this->input->post('export_excel')) {

            ini_set('memory_limit', '3500M');
            require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
            $this->load->library('PHPExcel');
            $inputFileName = 'uploads/import_ch/Phieu_dieu_do_cong_viec_bao_duong.xlsx';
            //  Read your Excel workbook
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
                die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
            }
            $BStylenumber = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => '111112'),
                    'size'  => 11,
                    'name'  => 'Times New Roman'
                ),
                'alignment' => array(
                    'horizontal' => 'center',
                ),
            );
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestRow = $objWorksheet->getHighestRow();
            $check_key = array_search($highestColumn, $columsExcel);
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $row = 3;
            $staff_id = get_staff_user_id();
            $this->db->select('
                tbl_suggest_maintenance_item.id as id,
                tbl_suggest_maintenance.reference_no as reference_no,
                tbl_suggest_maintenance.date as date,
                tbl_type_maintenance.name as name_type_maintenance,
                tbl_category_maintenance.name as name_category_maintenance,
                tbl_machines_maintenance.name as name_machines_maintenance,
                tbldepartments.name as name_department,
                tbl_suggest_maintenance.detail as detail,
                tbl_suggest_maintenance.quantity as quantity,
                tbl_machines.code as code_machines,
                tbl_machines.name as name_machines,
                tblbranch.name as name_branch,
                tbl_result.name as name_result,
                (SELECT GROUP_CONCAT(tblproduction_report.name_report)
                    FROM tblproduction_report
                    WHERE tblproduction_report.object_id = tbl_suggest_maintenance_item.id AND tblproduction_report.object_type = "suggest_maintenance"
                ) as name_report,
                tbl_suggest_maintenance_item.expected_date as expected_date,
                tbl_suggest_maintenance_item.start_date as start_date,
                tbl_suggest_maintenance_item.end_date as end_date,
                tbl_suggest_maintenance_item.standard as standard
            ');
            $this->db->from('tbl_suggest_maintenance');
            $this->db->join('tblbranch', 'tblbranch.id = tbl_suggest_maintenance.branch_id', 'inner');
            $this->db->join('tbl_machines', 'tbl_machines.id = tbl_suggest_maintenance.machines_id', 'inner');
            $this->db->join('tbl_suggest_maintenance_item', 'tbl_suggest_maintenance_item.suggest_maintenance_id = tbl_suggest_maintenance.id', 'left');
            $this->db->join('tbl_type_maintenance', 'tbl_type_maintenance.id = tbl_suggest_maintenance.type_maintenance', 'left');
            $this->db->join('tbl_category_maintenance', 'tbl_category_maintenance.id = tbl_suggest_maintenance.category_maintenance', 'left');
            $this->db->join('tbl_machines_maintenance', 'tbl_machines_maintenance.id = tbl_suggest_maintenance_item.machines_maintenance_id', 'left');
            $this->db->join('tbldepartments', 'tbldepartments.departmentid = tbl_suggest_maintenance.department_id', 'left');
            $this->db->join('tbl_result', 'tbl_result.id = tbl_suggest_maintenance_item.result_id', 'left');

            if (!$this->preViewModerationMaintenance) {
                $this->db->where('(tbl_suggest_maintenance.created_by = ' . $staff_id . ')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_maintenance.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_maintenance.date <= '" . $end_date_search . "'");
            }
            $this->db->order_by('tbl_suggest_maintenance.id asc');
            $items = $this->db->get()->result_array();
            $dem = 0;
            $this->load->library('ciqrcode');

            foreach ($items as $key => $value) {
                $row++;
                $dem++;
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[0] . $row, $dem);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, $value['reference_no'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, _d($value['date']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row, ($value['name_type_maintenance']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[4] . $row, ($value['name_category_maintenance']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[5] . $row, ($value['name_machines_maintenance']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[6] . $row, ($value['name_department']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[7] . $row, ($value['detail']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[8] . $row, $value['quantity'])->getStyle($columsExcel[8] . $row);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[9] . $row, ($value['code_machines']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[10] . $row, ($value['name_machines']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[11] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[12] . $row, ($value['name_branch']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[13] . $row, ($value['name_result']), PHPExcel_Cell_DataType::TYPE_STRING);
                $arrReport = $value['name_report'];
                $htmlReport = '';
                if (!empty($arrReport)) {
                    $arrReport = explode('||', $arrReport);
                    if (!empty($arrReport)) {
                        foreach ($arrReport as $kk => $vv) {
                            $vv = explode('__', $vv);
                            $htmlReport .= $vv[0] . ',';
                        }
                    }
                }
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[14] . $row, ($htmlReport), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[15] . $row, _d($value['expected_date']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[16] . $row, _d($value['start_date']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[17] . $row, _d($value['end_date']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[18] . $row, ($value['standard']), PHPExcel_Cell_DataType::TYPE_STRING);

                if (!empty($value['barcode'])) {
                    $code = $value['barcode'];
                } else {
                    $code = 'suggest_maintenance||' . $value['id'];
                    $this->db->where('id', $value['id']);
                    $this->db->update('tbl_suggest_maintenance', ['barcode' => $code]);
                }
                $qr = vn_to_str(str_replace('||', '__', $code));
                $folder = FCPATH . 'uploads/suggest_maintenance/';
                if (!file_exists($folder)) {
                    mkdir($folder);
                    fopen($folder . 'index.html', 'w');
                }
                if (!file_exists($folder . 'qrcode' . '/')) {
                    mkdir($folder . 'qrcode' . '/');
                    fopen($folder . 'qrcode' . '/' . 'index.html', 'w');
                }
                $params['data'] = $code;
                $params['level'] = 'H';
                $params['size'] = 40;
                $params['savename'] = $folder . 'qrcode/' . $qr . '.png';
                $this->ciqrcode->generate($params);
                $img = ($folder . 'qrcode/' . $qr . '.png');
                if (!empty($img)) {
                    $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                    $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                    $objDrawing1->setPath($img);
                    $objDrawing1->setWidth(90);
                    $objDrawing1->setHeight(65);
                    $objDrawing1->setOffsetX(20);
                    $objDrawing1->setOffsetY(2);
                    $objDrawing1->setCoordinates($columsExcel[19] . $row);
                }
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(60);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[19] . $row, '')->getStyle($columsExcel[19] . $row)->applyFromArray($BStylenumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }
            $objPHPExcel->getActiveSheet()->getStyle('A4:T' . $row)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('A4:T' . $row)->applyFromArray([
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
            ]);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[0])->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[1])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[2])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[3])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[4])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[5])->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[6])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[7])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[8])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[9])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[10])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[11])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[12])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[13])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[14])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[15])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[16])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[17])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[18])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[19])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[20])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[21])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[22])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[23])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[24])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[25])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[26])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[27])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[28])->setWidth(20);

            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $filename = lang('Phieu_dieu_do_cong_viec_bao_duong') . '.xls';
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
}
