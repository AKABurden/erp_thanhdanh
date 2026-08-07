<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Reports_summary extends AdminController
{
    public function __construct()
    {
        //cccc
        parent::__construct();
    }

    public function device() {
        $data['title']   = _l('Báo Cáo Tổng Hợp Thiết Bị/Tháng');
        $this->load->view('admin/reports_summary/device', $data);
    }

    public function getDevice() {
        $aColumns = [
            'tbl_suggest_maintenance.id as id',
            'tbl_suggest_maintenance.reference_no as code_suggest_maintenance',//phiếu yêu cầu bảo dưỡng
            'tblcategory_maintenance.name as name_category_machine',//nhóm thiết bị sản xuất
            'tbl_machines.code as code_machine',//mã thiết bị sản xuất
            'tbl_machines.name as name_machine',//tên thiết bị sản xuất
//            '(SELECT GROUP_CONCAT(tbl_suggest_check.reference_no SEPARATOR ",\n ") FROM tbl_suggest_check WHERE tbl_suggest_check.suggest_maintenance_id =  tbl_suggest_maintenance.id) as code_vs5s',//mã phiếu vs5s
//            'tbl_calibration_oisl.code as code_oil',//mã phiếu dầu
//            'tbl_calibration_oisl.code as code_oisl',//mã phiếu nhớt
//            'tbl_calibration_electric.code as code_electric',//mã phiếu điện
//            'tbl_calibration_mechanical.code as code_mechanical',//mã phiếu cơ,
//            'tbl_calibration_compressed_air.code as code_compressed_air',//mã phiếu khí nén
//            'tbl_categories_maintenance_cold.id as code_cold',//mã phiếu dàn lạnh,
            '(SELECT GROUP_CONCAT(tbl_suggest_check.reference_no SEPARATOR ",\n ") FROM tbl_suggest_check WHERE tbl_suggest_check.suggest_maintenance_id =  tbl_suggest_maintenance.id) as code_vs5s',//mã phiếu vs5s
            '(SELECT GROUP_CONCAT(tbl_calibration_oisl.code SEPARATOR ",\n ") 
                    FROM tbl_maintenance_calibration tbl_calibration_oisl 
                    WHERE tbl_calibration_oisl.suggest_maintenance_id = tbl_suggest_maintenance.id 
                    AND tbl_calibration_oisl.type = 6) as code_oil',//mã phiếu dầu

            '(SELECT GROUP_CONCAT(tbl_calibration_oisl.code SEPARATOR ",\n ") 
                    FROM tbl_maintenance_calibration tbl_calibration_oisl 
                    WHERE tbl_calibration_oisl.suggest_maintenance_id = tbl_suggest_maintenance.id 
                    AND tbl_calibration_oisl.type = 6) as code_oisl',//mã phiếu nhớt

            '(SELECT GROUP_CONCAT(tbl_calibration_electric.code SEPARATOR ",\n ") 
                    FROM tbl_maintenance_calibration tbl_calibration_electric 
                    WHERE tbl_calibration_electric.suggest_maintenance_id = tbl_suggest_maintenance.id 
                    AND tbl_calibration_electric.type = 3) as code_electric',//mã phiếu điện

            '(SELECT GROUP_CONCAT(tbl_calibration_mechanical.code SEPARATOR ",\n ") 
                    FROM tbl_maintenance_calibration tbl_calibration_mechanical 
                    WHERE tbl_calibration_mechanical.suggest_maintenance_id = tbl_suggest_maintenance.id 
                    AND tbl_calibration_mechanical.type = 2) as code_mechanical',//mã phiếu cơ


            '(SELECT GROUP_CONCAT(tbl_calibration_compressed_air.code SEPARATOR ",\n ") 
                    FROM tbl_maintenance_calibration tbl_calibration_compressed_air 
                    WHERE tbl_calibration_compressed_air.suggest_maintenance_id = tbl_suggest_maintenance.id 
                    AND tbl_calibration_compressed_air.type = 2) as code_compressed_air',//mã phiếu khí nén

            '(SELECT GROUP_CONCAT(tbl_categories_maintenance_cold.code SEPARATOR ",\n ") 
                    FROM tbl_categories_maintenance tbl_categories_maintenance_cold 
                    WHERE tbl_categories_maintenance_cold.suggest_maintenance_id = tbl_suggest_maintenance.id 
                    AND tbl_categories_maintenance_cold.type = "refrigeration"
                    ) as code_cold',//mã phiếu dàn lạnh,
            'tblmaintenance_ticket.name as maintenance',//bảo dưỡng
            'tbl_calibration.code as calibration',//hiệu chuẩn,
            'tblproduction_report.reference_no as code_bckph',//bckph,
            'tbl_suggest_maintenance.downtime as downtime',//thời gian ngưng máy,
            '"" as date_updated_foso',//ngày cập nhật foso,,
            '(SELECT GROUP_CONCAT(tbl_request_repair.reference_no SEPARATOR ",\n ") FROM tbl_request_repair WHERE production_report_id = tblproduction_report.id) as code_suggest_repair',//mã phiếu yêu cầu sửa chữa
            'tblinternal_proposal.code as code_suggest',//mã đề xuất,
            '(SELECT GROUP_CONCAT(CONCAT(tblother_payslips.prefix, tblother_payslips.code) SEPARATOR ",\n ") FROM tblother_payslips WHERE tblother_payslips.vouchers_id = tblsuggestion.id AND type_vouchers = 12) as code_payment',//phiếu chi,
            '(SELECT SUM(total) FROM tblother_payslips WHERE tblother_payslips.vouchers_id = tblsuggestion.id AND type_vouchers = 12) as amount',//số tiền,,
            '"" as vat',//vat,
            '0 as total',//thành tiền,
        ];

        $where = [];
        if($this->input->post('start_date')) {
            $where[] = 'AND DATE_FORMAT(tbl_suggest_maintenance.date, "%Y-%m-%d") >= "' . to_sql_date($this->input->post('start_date')).'"';
        }
        if($this->input->post('end_date')) {
            $where[] = 'AND DATE_FORMAT(tbl_suggest_maintenance.date, "%Y-%m-%d") <= "' . to_sql_date($this->input->post('end_date')).'"';
        }

        $sIndexColumn = 'id';
        $sTable       = 'tbl_suggest_maintenance';
        $join = [
            'LEFT JOIN tbl_suggest_maintenance_item ON tbl_suggest_maintenance_item.suggest_maintenance_id = tbl_suggest_maintenance.id',
//            'LEFT JOIN tbl_category_maintenance ON tbl_category_maintenance.id = tbl_suggest_maintenance.category_maintenance',
            'INNER JOIN tbl_machines ON tbl_machines.id = tbl_suggest_maintenance.machines_id',
            'LEFT JOIN tblcategory_maintenance ON tblcategory_maintenance.id = tbl_machines.category_machine_id',
            'LEFT JOIN tblmaintenance_ticket ON tblmaintenance_ticket.id_machines = tbl_machines.id',
            'LEFT JOIN tbl_maintenance_calibration tbl_calibration ON tbl_calibration.machines_id = tbl_machines.id AND tbl_calibration.type = 1',
            'LEFT JOIN tblproduction_report ON tblproduction_report.object_id = tbl_suggest_maintenance_item.id AND tblproduction_report.object_type = "suggest_maintenance"',
            'LEFT JOIN tblinternal_proposal ON tblinternal_proposal.suggest_id = tbl_suggest_maintenance.id',
            'LEFT JOIN tbl_category_recommended ON tbl_category_recommended.id = tblinternal_proposal.category_recommended_id AND tbl_category_recommended.name_table = "tbl_suggest_maintenance" and tbl_category_recommended.type is null',
//            'LEFT JOIN tbl_request_repair ON tbl_request_repair.production_report_id = tblproduction_report.id',
            'LEFT JOIN tblsuggestion ON tblsuggestion.id = tblinternal_proposal.id_suggestion',
//            'LEFT JOIN tblother_payslips ON tblother_payslips.vouchers_id = tblsuggestion.id AND type_vouchers = 12',
//            'LEFT JOIN tbl_maintenance_calibration tbl_calibration_oisl ON tbl_calibration_oisl.machines_id = tbl_machines.id AND tbl_calibration_oisl.type = 6',
//            'LEFT JOIN tbl_maintenance_calibration tbl_calibration_electric ON tbl_calibration_electric.machines_id = tbl_machines.id AND tbl_calibration_electric.type = 3',
//            'LEFT JOIN tbl_maintenance_calibration tbl_calibration_mechanical ON tbl_calibration_mechanical.machines_id = tbl_machines.id AND tbl_calibration_mechanical.type = 2',
//            'LEFT JOIN tbl_maintenance_calibration tbl_calibration_compressed_air ON tbl_calibration_compressed_air.machines_id = tbl_machines.id AND tbl_calibration_compressed_air.type = 5',
//            'LEFT JOIN tbl_categories_maintenance tbl_categories_maintenance_cold ON tbl_categories_maintenance_cold.id_machines = tbl_machines.id AND tbl_categories_maintenance_cold.type = "refrigeration"',
        ];
        $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);
        $output       = $result['output'];
        $rResult      = $result['rResult'];
        foreach ($rResult as $key =>  $aRow) {
            $row = [];
            $row[] = ($this->input->post('start')) + $key + 1;
            $row[] = $aRow['code_suggest_maintenance'];
            $row[] = $aRow['name_category_machine'];
            $row[] = $aRow['code_machine'];
            $row[] = $aRow['name_machine'];
            $row[] = $aRow['code_vs5s'];
            $row[] = $aRow['code_oil'];
            $row[] = $aRow['code_oisl'];
            $row[] = $aRow['code_electric'];
            $row[] = $aRow['code_mechanical'];
            $row[] = $aRow['code_compressed_air'];
            $row[] = $aRow['code_cold'];
            $row[] = $aRow['maintenance'];
            $row[] = $aRow['calibration'];
            $row[] = $aRow['code_bckph'];
            $row[] = $aRow['downtime'];
            $row[] = $aRow['date_updated_foso'];
            $row[] = $aRow['code_suggest_repair'];
            $row[] = $aRow['code_suggest'];
            $row[] = $aRow['code_payment'];
            $row[] = $aRow['amount'];
            $row[] = $aRow['vat'];
            $row[] = $aRow['amount'];
            $output['aaData'][] = $row;
        }
        echo json_encode($output);die();

    }

    public function getDeviceExcel(){
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            // print_arrays($this->input->post());
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();


            $this->db->select([
                'tbl_suggest_maintenance.id as id',
                'tbl_suggest_maintenance.reference_no as code_suggest_maintenance',//phiếu yêu cầu bảo dưỡng
                'tblcategory_maintenance.name as name_category_machine',//nhóm thiết bị sản xuất
                'tbl_machines.code as code_machine',//mã thiết bị sản xuất
                'tbl_machines.name as name_machine',//tên thiết bị sản xuất
                '(SELECT GROUP_CONCAT(tbl_suggest_check.reference_no SEPARATOR ",\n ") FROM tbl_suggest_check WHERE tbl_suggest_check.suggest_maintenance_id =  tbl_suggest_maintenance.id) as code_vs5s',//mã phiếu vs5s
                '(SELECT GROUP_CONCAT(tbl_calibration_oisl.code SEPARATOR ",\n ") 
                    FROM tbl_maintenance_calibration tbl_calibration_oisl 
                    WHERE tbl_calibration_oisl.suggest_maintenance_id = tbl_suggest_maintenance.id 
                    AND tbl_calibration_oisl.type = 6) as code_oil',//mã phiếu dầu

                '(SELECT GROUP_CONCAT(tbl_calibration_oisl.code SEPARATOR ",\n ") 
                    FROM tbl_maintenance_calibration tbl_calibration_oisl 
                    WHERE tbl_calibration_oisl.suggest_maintenance_id = tbl_suggest_maintenance.id 
                    AND tbl_calibration_oisl.type = 6) as code_oisl',//mã phiếu nhớt

                '(SELECT GROUP_CONCAT(tbl_calibration_electric.code SEPARATOR ",\n ") 
                    FROM tbl_maintenance_calibration tbl_calibration_electric 
                    WHERE tbl_calibration_electric.suggest_maintenance_id = tbl_suggest_maintenance.id 
                    AND tbl_calibration_electric.type = 3) as code_electric',//mã phiếu điện

                '(SELECT GROUP_CONCAT(tbl_calibration_mechanical.code SEPARATOR ",\n ") 
                    FROM tbl_maintenance_calibration tbl_calibration_mechanical 
                    WHERE tbl_calibration_mechanical.suggest_maintenance_id = tbl_suggest_maintenance.id 
                    AND tbl_calibration_mechanical.type = 2) as code_mechanical',//mã phiếu cơ

                '(SELECT GROUP_CONCAT(tbl_calibration_compressed_air.code SEPARATOR ",\n ") 
                    FROM tbl_maintenance_calibration tbl_calibration_compressed_air 
                    WHERE tbl_calibration_compressed_air.suggest_maintenance_id = tbl_suggest_maintenance.id 
                    AND tbl_calibration_compressed_air.type = 2) as code_compressed_air',//mã phiếu khí nén

                '(SELECT GROUP_CONCAT(tbl_categories_maintenance_cold.code SEPARATOR ",\n ") 
                    FROM tbl_categories_maintenance tbl_categories_maintenance_cold 
                    WHERE tbl_categories_maintenance_cold.suggest_maintenance_id = tbl_suggest_maintenance.id 
                        AND tbl_categories_maintenance_cold.type = "refrigeration"
                    ) as code_cold',//mã phiếu dàn lạnh,


//                'tbl_categories_maintenance_cold.id as code_cold',//mã phiếu dàn lạnh,
                'tblmaintenance_ticket.name as maintenance',//bảo dưỡng
                'tbl_calibration.code as calibration',//hiệu chuẩn,
                'tblproduction_report.reference_no as code_bckph',//bckph,
                'tbl_suggest_maintenance.downtime as downtime',//thời gian ngưng máy,
                '"" as date_updated_foso',//ngày cập nhật foso,,
                '(SELECT GROUP_CONCAT(tbl_request_repair.reference_no SEPARATOR ",\n ") FROM tbl_request_repair WHERE production_report_id = tblproduction_report.id) as code_suggest_repair',//mã phiếu yêu cầu sửa chữa
                'tblinternal_proposal.code as code_suggest',//mã đề xuất,
                '(SELECT GROUP_CONCAT(CONCAT(tblother_payslips.prefix, tblother_payslips.code) SEPARATOR ",\n ") FROM tblother_payslips WHERE tblother_payslips.vouchers_id = tblsuggestion.id AND type_vouchers = 12) as code_payment',//phiếu chi,
                '(SELECT SUM(total) FROM tblother_payslips WHERE tblother_payslips.vouchers_id = tblsuggestion.id AND type_vouchers = 12) as amount',//số tiền,,
                '"" as vat',//vat,
                '0 as total',//thành tiền,
            ]);
            $this->db->from('tbl_suggest_maintenance');
            $this->db->join('tbl_suggest_maintenance_item','tbl_suggest_maintenance_item.suggest_maintenance_id = tbl_suggest_maintenance.id','left');
//            $this->db->join('tbl_category_maintenance','tbl_category_maintenance.id = tbl_suggest_maintenance.category_maintenance','left');
            $this->db->join('tbl_machines','tbl_machines.id = tbl_suggest_maintenance.machines_id');
            $this->db->join('tblcategory_maintenance','tblcategory_maintenance.id = tbl_machines.category_machine_id','left');
            $this->db->join('tblmaintenance_ticket','tblmaintenance_ticket.id_machines = tbl_machines.id','left');
            $this->db->join('tbl_maintenance_calibration tbl_calibration','tbl_calibration ON tbl_calibration.machines_id = tbl_machines.id AND tbl_calibration.type = 1','left');
            $this->db->join('tblproduction_report','tblproduction_report.object_id = tbl_suggest_maintenance_item.id AND tblproduction_report.object_type = "suggest_maintenance"','left');
            $this->db->join('tblinternal_proposal','tblinternal_proposal.suggest_id = tbl_suggest_maintenance.id','left');
            $this->db->join('tbl_category_recommended','tbl_category_recommended.id = tblinternal_proposal.category_recommended_id AND tbl_category_recommended.name_table = "tbl_suggest_maintenance"','left');
            $this->db->join('tblsuggestion','tblsuggestion.id = tblinternal_proposal.id_suggestion','left');

//            $this->db->join('tbl_maintenance_calibration tbl_calibration_oisl','tbl_calibration_oisl ON tbl_calibration_oisl.suggest_maintenance_id = tbl_suggest_maintenance.id AND tbl_calibration_oisl.type = 6','left');
//            $this->db->join('tbl_maintenance_calibration tbl_calibration_electric','tbl_calibration_electric.suggest_maintenance_id = tbl_suggest_maintenance.id AND tbl_calibration_electric.type = 3','left');
//            $this->db->join('tbl_maintenance_calibration tbl_calibration_mechanical','tbl_calibration_mechanical.suggest_maintenance_id = tbl_suggest_maintenance.id AND tbl_calibration_mechanical.type = 2','left');
//            $this->db->join('tbl_maintenance_calibration tbl_calibration_compressed_air','tbl_calibration_compressed_air.suggest_maintenance_id = tbl_suggest_maintenance.id AND tbl_calibration_compressed_air.type = 5','left');
//            $this->db->join('tbl_categories_maintenance tbl_categories_maintenance_cold','tbl_categories_maintenance_cold.suggest_maintenance_id = tbl_suggest_maintenance.id AND tbl_categories_maintenance_cold.type = "refrigeration"','left');

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_maintenance.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_maintenance.date <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tbl_suggest_maintenance.id desc');
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
            $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf("%0" . $decimals_number . "s",
                        0) : '');
            $company = get_option('invoice_company_name');
            $address = get_option('invoice_company_address');
            $company_vat = get_option('company_vat');
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name'  => 'Times New Roman'
                ),
            ]);
            $titleAppend = '';
            if(!empty($start_date_search) && !empty($end_date_search)){
                $titleAppend = 'Từ ngày ' . _d($start_date_search) . ' đến ngày ' . _d($end_date_search);
            }
            $objPHPExcel->getActiveSheet()->setCellValue('A1',
                ('Báo Cáo Tổng Hợp Thiết Bị/Tháng ') . $titleAppend)->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:R1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Phiếu Yêu cầu bảo dưỡng');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Nhóm Thiết Bị Sản Xuất');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Mã Thiết Bị Sản Xuất');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Tên Thiết Bị Sản Xuất');
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Mã Phiếu VS5S')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Mã Phiếu Dầu')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Mã Phiếu Nhớt')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Mã Phiếu Điện')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Mã Phiếu Cơ')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Mã Phiếu Nén')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Mã Phiếu Dàn Lạnh')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$sttRow.'', 'Bảo Dưỡng')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$sttRow.'', 'Hiệu Chuẩn')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$sttRow.'', 'BCKPH')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$sttRow.'', 'Thời Gian Ngưng Máy')->getStyle("P$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$sttRow.'', 'Ngày Cập Nhật FOSO')->getStyle("Q$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('R'.$sttRow.'', 'Mã Phiếu Yêu Cầu Sửa Chữa')->getStyle("R$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('S'.$sttRow.'', 'Mã Đề Xuất')->getStyle("S$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('T'.$sttRow.'', 'Phiếu Chi')->getStyle("T$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('U'.$sttRow.'', 'Số Tiền')->getStyle("U$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('V'.$sttRow.'', 'VAT')->getStyle("V$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('W'.$sttRow.'', 'Thành Tiền')->getStyle("W$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:W$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name'  => 'Times New Roman'
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
            $rowBegin = $sttRow;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $rowBegin++;
                    $row = [];
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['code_suggest_maintenance']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['name_category_machine']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['code_machine'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['name_machine']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $value['code_vs5s'])->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $value['code_oil'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin",$value['code_oisl'])->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $value['code_electric'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $value['code_mechanical'])->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $value['code_compressed_air'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $value['code_cold'])->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", $value['maintenance'])->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", $value['calibration'])->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", $value['code_bckph'])->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", $value['downtime'])->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin", $value['date_updated_foso'])->getStyle("Q$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin", $value['code_suggest_repair'])->getStyle("R$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("S$rowBegin", $value['code_suggest'])->getStyle("S$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("T$rowBegin", $value['code_payment'])->getStyle("T$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("U$rowBegin", $value['amount'])->getStyle("U$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("V$rowBegin", $value['vat'])->getStyle("V$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("W$rowBegin", $value['amount'])->getStyle("W$rowBegin")->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:W$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:A$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("I$rowBegin:I$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                }
            }
            $filename = lang('bao_cao_tong_hop_thiet_bi_thang') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(10);
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

    public function human() {
        $data['title']   = _l('Báo Cáo Tổng Hợp Nhân Sự/Tháng');
        $this->load->view('admin/reports_summary/human', $data);
    }

    public function getHuman() {

        $wherePayroll = [];
        $whereRecords = [];
        $whereDecision = [];
        $whereOvertime_detail = [];
        if($this->input->post('start_date')) {
            $start_date = to_sql_date($this->input->post('start_date'));
            $FmDate = DateTime::createFromFormat('Y-m-d', $start_date);
            $year = $FmDate->format('Y');
            $month = $FmDate->format('m');
            $day = $FmDate->format('d');

            $wherePayroll[] = 'AND STR_TO_DATE(CONCAT(tbl_payroll.year, "-", tbl_payroll.month, "-01"), "%Y-%m-%d") >= "'.($year.'-'.$month.'-01').'"';

            $whereRecords[] = 'AND DATE_FORMAT(tblviolation_records.date, "%Y-%m-%d") >= "'.$start_date.'"';
            $whereDecision[] = 'AND DATE_FORMAT(tbl_decision_bonus_discipline.date, "%Y-%m-%d") >= "'.$start_date.'"';
            $whereOvertime_detail[] = 'AND DATE_FORMAT(tbl_business_fee_boiler_overtime_detail.date, "%Y-%m-%d") >= "'.$start_date.'"';

            //            $where[] = 'AND DATE_FORMAT(tbl_suggest_maintenance.date, "%Y-%m-%d") >= "' . to_sql_date($this->input->post('start_date')).'"';
        }
        if($this->input->post('end_date')) {
            $end_date = to_sql_date($this->input->post('end_date'));
            $FmDate = DateTime::createFromFormat('Y-m-d', $end_date);
            $year = $FmDate->format('Y');
            $month = $FmDate->format('m');
            $day = $FmDate->format('d');
            $wherePayroll[] = 'AND STR_TO_DATE(CONCAT(tbl_payroll.year, "-", tbl_payroll.month, "-31"), "%Y-%m-%d") <= "'.$year.'-'.$month.'-31"';
            $whereRecords[] = 'AND DATE_FORMAT(tblviolation_records.date, "%Y-%m-%d") <= "'.$end_date.'"';
            $whereDecision[] = 'AND DATE_FORMAT(tbl_decision_bonus_discipline.date, "%Y-%m-%d") <= "'.$end_date.'"';
            $whereOvertime_detail[] = 'AND DATE_FORMAT(tbl_business_fee_boiler_overtime_detail.date, "%Y-%m-%d") <= "'.$end_date.'"';

            //            $where[] = 'AND DATE_FORMAT(tbl_suggest_maintenance.date, "%Y-%m-%d") <= "' . to_sql_date($this->input->post('start_date')).'"';
        }
//        print_arrays($wherePayroll);

        $aColumns = [
            'tblstaff.staffid as staffid',
            'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname',
            '(
                SELECT GROUP_CONCAT(tblbranch.name) 
                FROM tblbranch 
                JOIN tblstaff_branch ON tblstaff_branch.id_branch = tblbranch.id
                WHERE tblstaff_branch.staffid = tblstaff.staffid
            ) as name_branch',
            '(
                SELECT GROUP_CONCAT(tbldepartments.name) 
                FROM tbldepartments 
                LEFT JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
                WHERE tblstaff_departments.staffid = tblstaff.staffid
            ) as name_room',
//            '(
//                SELECT SUM(COALESCE(tbl_timekeeping_detail.count_hour, 0))
//                FROM tbl_timekeeping_detail
//                WHERE staff_id = tblstaff.staffid
//            ) as count_hour',// tổng giờ công'
            '(
                SELECT SUM(COALESCE(tbl_payroll_item.day_number, 0)) 
                FROM tbl_payroll_item 
                JOIN tbl_payroll ON tbl_payroll.id = tbl_payroll_item.payroll_id
                WHERE tbl_payroll_item.staff_id = tblstaff.staffid
                '.implode(' ', $wherePayroll).'
            ) as count_hour',// tổng giờ công
            '(
                SELECT SUM(COALESCE(tbl_payroll_item.day_number_off_new, 0)) 
                FROM tbl_payroll_item 
                JOIN tbl_payroll ON tbl_payroll.id = tbl_payroll_item.payroll_id
                WHERE tbl_payroll_item.staff_id = tblstaff.staffid
                '.implode(' ', $wherePayroll).'
            ) as number_date',// số ngày đã nghĩ
//            '(
//                SELECT SUM(COALESCE(tbl_paid_holiday_leave_detail.number_date, 0))
//                FROM tbl_timekeeping_detail
//                JOIN tbl_paid_holiday_leave_detail ON tbl_paid_holiday_leave_detail.id = tbl_timekeeping_detail.paid_holiday_detail_id
//                WHERE staff_id = tblstaff.staffid
//            ) as number_date',// số ngày đã nghĩ
            '(
                SELECT COUNT(tblviolation_records.id) 
                FROM tblviolation_records 
                WHERE tblviolation_records.staff_id = tblstaff.staffid
                AND tblviolation_records.status = 1
                '.implode(' ', $whereRecords).'
            ) as count_violation_records',//biên bản vi phạm'
            '(
                SELECT COUNT(tbl_decision_bonus_discipline.id)
                FROM tbl_decision_bonus_discipline
                WHERE tbl_decision_bonus_discipline.object_type = "staff" AND tbl_decision_bonus_discipline.object_id = tblstaff.staffid
                AND tbl_decision_bonus_discipline.type_quota_bonus_discipline_id = 1
                AND tbl_decision_bonus_discipline.status = 1
                 '.implode(' ', $whereDecision).'
            ) as count_decision_bonus',//khen thưởng
            '(
                SELECT COUNT(tbl_decision_bonus_discipline.id)
                FROM tbl_decision_bonus_discipline
                WHERE tbl_decision_bonus_discipline.object_type = "staff" AND tbl_decision_bonus_discipline.object_id = tblstaff.staffid
                AND tbl_decision_bonus_discipline.type_quota_bonus_discipline_id = 2
                AND tbl_decision_bonus_discipline.status = 1
                 '.implode(' ', $whereDecision).'
            ) as count_discipline',//kỹ luật'
            '(
                SELECT SUM(COALESCE(tbl_business_fee_boiler_overtime_detail.weekday, 0)) + SUM(COALESCE(tbl_business_fee_boiler_overtime_detail.sunday, 0)) + SUM(COALESCE(tbl_business_fee_boiler_overtime_detail.holiday, 0)) 
                FROM tbl_business_fee_boiler_overtime_detail
                JOIN tbl_business_fee_boiler_overtime ON tbl_business_fee_boiler_overtime.id = tbl_business_fee_boiler_overtime_detail.business_fee_boiler_overtime_id
                WHERE tbl_business_fee_boiler_overtime.staff_id = tblstaff.staffid
                 '.implode(' ', $whereOvertime_detail).'
            ) as count_overtime',//tăng ca
            'salary_bhxh',//lương căn bản
            '(COALESCE(coefficient_position, 0)) as luong_nang_luc',//lương năng lực
            '(
                SELECT SUM(COALESCE(tbl_payroll_item.total_real, 0)) 
                FROM tbl_payroll_item 
                JOIN tbl_payroll ON tbl_payroll.id = tbl_payroll_item.payroll_id
                WHERE tbl_payroll_item.staff_id = tblstaff.staffid
                '.implode(' ', $wherePayroll).'
            ) as total_real',//lương thực tế
            'insurrance_book_number',//bảo hểm xã hội
            'number_bhty',//Số thẻ BHYT
            '(
                SELECT SUM(COALESCE(tbl_allowance_reduce_payroll.amount, 0)) 
                FROM tbl_allowance_reduce_payroll 
                LEFT JOIN tbl_payroll_item ON tbl_payroll_item.id = tbl_allowance_reduce_payroll.payroll_item_id
                LEFT JOIN tbl_payroll ON tbl_payroll.id = tbl_payroll_item.payroll_id
                WHERE tbl_payroll_item.staff_id = tblstaff.staffid
                '.implode(' ', $wherePayroll).'
            ) as lapc',//phụ cấp,
            '(salary_bhxh_new * 1 /100) as cong_doan',//công đoàn
            '(
                SELECT SUM(COALESCE(tbl_payroll_item.total_vat, 0)) 
                FROM tbl_payroll_item 
                JOIN tbl_payroll ON tbl_payroll.id = tbl_payroll_item.payroll_id
                WHERE tbl_payroll_item.staff_id = tblstaff.staffid
                '.implode(' ', $wherePayroll).'
            ) as total_vat',//thuế TNCN
            'status_work', //trạng thái làm việc
        ];
        $where = [];
        $sIndexColumn = 'staffid';
        $sTable       = 'tblstaff';
        $join = [
//            'JOIN tblstaff_branch ON tblstaff_branch.staffid = tblstaff.staffid',
//            'JOIN tblbranch ON tblbranch.id = tblstaff_branch.id_branch',
        ];
        $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'coefficient_responsibility',
            'salary_bhxh_new',
        ]);
        $output       = $result['output'];
        $rResult      = $result['rResult'];
        foreach ($rResult as $key =>  $aRow) {
            $row = [];
            $row[] = ($this->input->post('start')) + $key + 1;
            $row[] = $aRow['fullname'];// họ tên nhân viên
            $row[] = $aRow['name_branch']; // chi nhánh
            $row[] = $aRow['name_room']; // phòng ban
            $row[] = !empty($aRow['count_hour']) ? number_format_data($aRow['count_hour']) : ''; // số giờ công
            $row[] = !empty($aRow['number_date']) ? number_format_data($aRow['number_date']) : ''; // số ngày đã nghĩ
            $row[] = !empty($aRow['count_violation_records']) ? number_format_data($aRow['count_violation_records']) : ''; // số biên bản vi phạm
            $row[] = !empty($aRow['count_decision_bonus']) ? number_format_data($aRow['count_decision_bonus']) : ''; // khen thưởng
            $row[] = !empty($aRow['count_discipline']) ? number_format_data($aRow['count_discipline']) : ''; // kỹ luật
            $row[] = !empty($aRow['count_overtime']) ? number_format_data($aRow['count_overtime']) : ''; // tăng ca
            $row[] = !empty($aRow['salary_bhxh']) ? number_format_data($aRow['salary_bhxh']) : ''; // lương căn bản

            $aRow['luong_nang_luc'] = $aRow['luong_nang_luc'] * number_unformat(get_option('salary_minimum_new'));

            $row[] = !empty($aRow['luong_nang_luc']) ? number_format_data($aRow['luong_nang_luc']) : ''; // lương năng lực
            $row[] = !empty($aRow['total_real']) ? number_format_data($aRow['total_real']) : ''; // lương thực tế
            $row[] = !empty($aRow['insurrance_book_number']) ? ($aRow['insurrance_book_number']) : ''; //bảo hểm xã hội
            $row[] = !empty($aRow['number_bhty']) ? $aRow['number_bhty'] : ''; //Số thẻ BHYT
            $row[] = !empty($aRow['lapc']) ? number_format_data($aRow['lapc']) : ''; //Phụ Cấp
            $row[] = !empty($aRow['cong_doan']) ? number_format_data($aRow['cong_doan']) : ''; //Công đoàn
            $row[] = !empty($aRow['total_vat']) ? number_format_data($aRow['total_vat']) : ''; //thuế TNCN
            $row[] = $aRow['status_work'] == 0 ? 'Thử Việc' : ($aRow['status_work'] == 1 ? 'Đang Làm Việc' : "-"); //đang hoạt động
            $row[] = $aRow['status_work'] == 2 ? "Nghĩ Việc" : '-'; //ngừng việc
            $row[] = ''; //Ngày Cập Nhật Foso
            $output['aaData'][] = $row;
        }
        echo json_encode($output);die();
    }

    public function getHumanExcel(){
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            // print_arrays($this->input->post());
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();
            $wherePayroll = [];
            $whereRecords = [];
            $whereDecision = [];
            $whereOvertime_detail = [];
            if($this->input->post('start_date_search')) {
                $start_date = to_sql_date($this->input->post('start_date_search'));
                $FmDate = DateTime::createFromFormat('Y-m-d', $start_date);
                $year = $FmDate->format('Y');
                $month = $FmDate->format('m');
                $day = $FmDate->format('d');

                $wherePayroll[] = 'AND STR_TO_DATE(CONCAT(tbl_payroll.year, "-", tbl_payroll.month, "-01"), "%Y-%m-%d") >= "'.($year.'-'.$month.'-01').'"';

                $whereRecords[] = 'AND DATE_FORMAT(tblviolation_records.date, "%Y-%m-%d") >= "'.$start_date.'"';
                $whereDecision[] = 'AND DATE_FORMAT(tbl_decision_bonus_discipline.date, "%Y-%m-%d") >= "'.$start_date.'"';
                $whereOvertime_detail[] = 'AND DATE_FORMAT(tbl_business_fee_boiler_overtime_detail.date, "%Y-%m-%d") >= "'.$start_date.'"';
            }
            if($this->input->post('end_date_search')) {
                $end_date = to_sql_date($this->input->post('end_date_search'));
                $FmDate = DateTime::createFromFormat('Y-m-d', $end_date);
                $year = $FmDate->format('Y');
                $month = $FmDate->format('m');
                $day = $FmDate->format('d');
                $wherePayroll[] = 'AND STR_TO_DATE(CONCAT(tbl_payroll.year, "-", tbl_payroll.month, "-31"), "%Y-%m-%d") <= "'.$year.'-'.$month.'-31"';
                $whereRecords[] = 'AND DATE_FORMAT(tblviolation_records.date, "%Y-%m-%d") <= "'.$end_date.'"';
                $whereDecision[] = 'AND DATE_FORMAT(tbl_decision_bonus_discipline.date, "%Y-%m-%d") <= "'.$end_date.'"';
                $whereOvertime_detail[] = 'AND DATE_FORMAT(tbl_business_fee_boiler_overtime_detail.date, "%Y-%m-%d") <= "'.$end_date.'"';
            }

            $this->db->select([
                    'tblstaff.staffid as staffid',
                    'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname',
                    '(
                        SELECT GROUP_CONCAT(tblbranch.name) 
                        FROM tblbranch 
                        JOIN tblstaff_branch ON tblstaff_branch.id_branch = tblbranch.id
                        WHERE tblstaff_branch.staffid = tblstaff.staffid
                    ) as name_branch',
                    '(
                        SELECT GROUP_CONCAT(tbldepartments.name) 
                        FROM tbldepartments 
                        LEFT JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
                        WHERE tblstaff_departments.staffid = tblstaff.staffid
                    ) as name_room',
                    '(
                        SELECT SUM(COALESCE(tbl_payroll_item.day_number, 0)) 
                        FROM tbl_payroll_item 
                        JOIN tbl_payroll ON tbl_payroll.id = tbl_payroll_item.payroll_id
                        WHERE tbl_payroll_item.staff_id = tblstaff.staffid
                        '.implode(' ', $wherePayroll).'
                    ) as count_hour',// tổng giờ công
                    '(
                        SELECT SUM(COALESCE(tbl_payroll_item.day_number_off_new, 0)) 
                        FROM tbl_payroll_item 
                        JOIN tbl_payroll ON tbl_payroll.id = tbl_payroll_item.payroll_id
                        WHERE tbl_payroll_item.staff_id = tblstaff.staffid
                        '.implode(' ', $wherePayroll).'
                    ) as number_date',// số ngày đã nghĩ
                    '(
                        SELECT COUNT(tblviolation_records.id) 
                        FROM tblviolation_records 
                        WHERE tblviolation_records.staff_id = tblstaff.staffid
                        AND tblviolation_records.status = 1
                        '.implode(' ', $whereRecords).'
                    ) as count_violation_records',//biên bản vi phạm'
                    '(
                        SELECT COUNT(tbl_decision_bonus_discipline.id)
                        FROM tbl_decision_bonus_discipline
                        WHERE tbl_decision_bonus_discipline.object_type = "staff" AND tbl_decision_bonus_discipline.object_id = tblstaff.staffid
                        AND tbl_decision_bonus_discipline.type_quota_bonus_discipline_id = 1
                        AND tbl_decision_bonus_discipline.status = 1
                         '.implode(' ', $whereDecision).'
                    ) as count_decision_bonus',//khen thưởng
                    '(
                        SELECT COUNT(tbl_decision_bonus_discipline.id)
                        FROM tbl_decision_bonus_discipline
                        WHERE tbl_decision_bonus_discipline.object_type = "staff" AND tbl_decision_bonus_discipline.object_id = tblstaff.staffid
                        AND tbl_decision_bonus_discipline.type_quota_bonus_discipline_id = 2
                        AND tbl_decision_bonus_discipline.status = 1
                         '.implode(' ', $whereDecision).'
                    ) as count_discipline',//kỹ luật'
                    '(
                        SELECT SUM(COALESCE(tbl_business_fee_boiler_overtime_detail.weekday, 0)) + SUM(COALESCE(tbl_business_fee_boiler_overtime_detail.sunday, 0)) + SUM(COALESCE(tbl_business_fee_boiler_overtime_detail.holiday, 0)) 
                        FROM tbl_business_fee_boiler_overtime_detail
                        JOIN tbl_business_fee_boiler_overtime ON tbl_business_fee_boiler_overtime.id = tbl_business_fee_boiler_overtime_detail.business_fee_boiler_overtime_id
                        WHERE tbl_business_fee_boiler_overtime.staff_id = tblstaff.staffid
                         '.implode(' ', $whereOvertime_detail).'
                    ) as count_overtime',//tăng ca
                    'salary_bhxh',//lương căn bản
                    '(COALESCE(coefficient_position, 0)) as luong_nang_luc',//lương năng lực
                    '(
                        SELECT SUM(COALESCE(tbl_payroll_item.total_real, 0)) 
                        FROM tbl_payroll_item 
                        JOIN tbl_payroll ON tbl_payroll.id = tbl_payroll_item.payroll_id
                        WHERE tbl_payroll_item.staff_id = tblstaff.staffid
                        '.implode(' ', $wherePayroll).'
                    ) as total_real',//lương thực tế
                    'insurrance_book_number',//bảo hểm xã hội
                    'number_bhty',//Số thẻ BHYT
                    '(
                        SELECT SUM(COALESCE(tbl_allowance_reduce_payroll.amount, 0)) 
                        FROM tbl_allowance_reduce_payroll 
                        LEFT JOIN tbl_payroll_item ON tbl_payroll_item.id = tbl_allowance_reduce_payroll.payroll_item_id
                        LEFT JOIN tbl_payroll ON tbl_payroll.id = tbl_payroll_item.payroll_id
                        WHERE tbl_payroll_item.staff_id = tblstaff.staffid
                        '.implode(' ', $wherePayroll).'
                    ) as lapc',//phụ cấp,
                    '(salary_bhxh_new * 1 /100) as cong_doan',//công đoàn
                    '(
                        SELECT SUM(COALESCE(tbl_payroll_item.total_vat, 0)) 
                        FROM tbl_payroll_item 
                        JOIN tbl_payroll ON tbl_payroll.id = tbl_payroll_item.payroll_id
                        WHERE tbl_payroll_item.staff_id = tblstaff.staffid
                        '.implode(' ', $wherePayroll).'
                    ) as total_vat',//thuế TNCN
                    'status_work', //trạng thái làm việc
                ]);
            $this->db->from('tblstaff');

            $this->db->order_by('tblstaff.staffid desc');
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
            $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf("%0" . $decimals_number . "s",
                        0) : '');
            $company = get_option('invoice_company_name');
            $address = get_option('invoice_company_address');
            $company_vat = get_option('company_vat');
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name'  => 'Times New Roman'
                ),
            ]);
            $titleAppend = '';
            if(!empty($start_date_search) && !empty($end_date_search)){
                $titleAppend = 'Từ ngày ' . _d($start_date_search) . ' đến ngày ' . _d($end_date_search);
            }
            $objPHPExcel->getActiveSheet()->setCellValue('A1',
                ('Báo Cáo Tổng Hợp Nhân Sự/Tháng ') . $titleAppend)->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:R1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Nhân Viên');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Chi Nhánh');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Phòng Ban');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Tổng Số Giờ Công');
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Phép')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Vi Phạm')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Khen Thưởng')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Kỷ Luật')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Tăng Ca')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Lương Căn Bản')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Lương Năng Lực')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$sttRow.'', 'Lương Thực')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$sttRow.'', 'BHXH')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$sttRow.'', 'BHYT')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$sttRow.'', 'Phúc Lợi')->getStyle("P$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$sttRow.'', 'Công Đoàn')->getStyle("Q$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('R'.$sttRow.'', 'Thuế TNCN')->getStyle("R$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('S'.$sttRow.'', 'Đang Hoạt Động')->getStyle("S$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('T'.$sttRow.'', 'Ngừng Việc')->getStyle("T$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('U'.$sttRow.'', 'Ngày Cập Nhật Foso')->getStyle("U$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:U$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name'  => 'Times New Roman'
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
            $rowBegin = $sttRow;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $rowBegin++;
                    $row = [];
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['fullname']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['name_branch']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['name_room'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['count_hour']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->getStyle("E$rowBegin")
                        ->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $value['number_date'])->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->getStyle("F$rowBegin")
                        ->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $value['count_violation_records'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->getStyle("G$rowBegin")
                        ->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin",$value['count_decision_bonus'])->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->getStyle("H$rowBegin")
                        ->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $value['count_discipline'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->getStyle("I$rowBegin")
                        ->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $value['count_overtime'])->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->getStyle("J$rowBegin")
                        ->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $value['salary_bhxh'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->getStyle("K$rowBegin")
                        ->getNumberFormat()->setFormatCode('#,##0');

                    $luong_nang_luc = $value['luong_nang_luc'] * number_unformat(get_option('salary_minimum_new'));

                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $luong_nang_luc)->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->getStyle("L$rowBegin")
                        ->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", $value['total_real'])->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->getStyle("M$rowBegin")
                        ->getNumberFormat()->setFormatCode('#,##0');

                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", $value['insurrance_book_number'])->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", $value['number_bhty'])->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", $value['lapc'])->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->getStyle("P$rowBegin")
                        ->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin", $value['cong_doan'])->getStyle("Q$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->getStyle("Q$rowBegin")
                        ->getNumberFormat()->setFormatCode('#,##0');

                    $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin", $value['total_vat'])->getStyle("R$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->getStyle("R$rowBegin")
                        ->getNumberFormat()->setFormatCode('#,##0');

                    $objPHPExcel->getActiveSheet()->setCellValue("S$rowBegin", ($value['status_work'] == 0 ? 'Thử Việc' : ($value['status_work'] == 1 ? 'Đang Làm Việc' : "-")))->getStyle("S$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("T$rowBegin", ($value['status_work'] == 2 ? "Nghĩ Việc" : '-'))->getStyle("T$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("U$rowBegin", '')->getStyle("U$rowBegin")->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:U$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:A$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("I$rowBegin:I$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                }
            }
            $filename = lang('bao_cao_tong_hop_nhan_su_thang') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(10);
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

    public function evaluate() {
        $data['title']   = _l('Báo Cáo Tổng Hợp Giấy Phép');
        $this->load->view('admin/reports_summary/evaluate', $data);
    }

    public function getEvaluate() {
        $aColumns = [
            'tblinternal_proposal.id as id',
            'tblinternal_proposal.code as code_internal_proposal',// mã đề xuất nội bộ
            'tbl_recommended_list.name as name_recommended_list', // Nhóm Giấy Phép-Chứng Nhận
            'tbl_evaluate.code_evaluate as code_evaluate', // mã
            'tbl_evaluate.name_evaluate as name_evaluate', // tên Giấy Phép-Chứng Nhận
            'tbl_evaluate.date_of_issue as date_of_issue', // tên cấp
            'tbl_evaluate.time_of_use as time_of_use', // thời gian sử dụng
            'tbl_evaluate.reissue_date as reissue_date', // ngày tái cấp
            'tblinternal_proposal.money as money', // chi phí
            'tbl_evaluate.unit_of_level as unit_of_level', // đơn vị cấp
            'tbl_evaluate.training_unit as training_unit', // đơn vị đào tạo
            'tbl_evaluate.notary_public as notary_public', // đơn vị công chứng
            '"" as date_update_foso', // Ngày cập nhật foso
            'tbl_evaluate.adjustment_date as adjustment_date', // Ngày điều chỉnh
            'tbl_evaluate.active as active', // hoạt động
        ];

        $where = [];
        $sIndexColumn = 'id';
        $sTable       = 'tblinternal_proposal';
        $join = [
            'LEFT JOIN tbl_recommended_list ON tbl_recommended_list.id = tblinternal_proposal.recommended_list_group_id',
            'LEFT JOIN tbl_evaluate ON tbl_evaluate.id = tblinternal_proposal.suggest_id',
            'LEFT JOIN tbl_category_recommended ON tbl_category_recommended.id = tblinternal_proposal.category_recommended_id',
        ];
//        $where[] = 'AND tbl_category_recommended.type IN ("license","certification", "evaluate", "customers", "certificate",  "educate")';
        $where[] = 'AND tbl_recommended_list.type_plan_propose IN ("license", "certification")';
        $where[] = 'AND tbl_category_recommended.type IN ("license", "certification")';
        if($this->input->post('start_date')) {
            $start_date = to_sql_date($this->input->post('start_date'));
            $where[] = 'AND tblinternal_proposal.date >= "' . $start_date.'"';
        }
        if($this->input->post('end_date')) {
            $end_date = to_sql_date($this->input->post('end_date'));
            $where[] = 'AND DATE_FORMAT(tblinternal_proposal.date, "%Y-%m-%d") <= "' . $end_date . '"';
        }
        $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);
        $output  = $result['output'];
        $rResult  = $result['rResult'];
        foreach ($rResult as $key =>  $aRow) {
            $row = [];
            $row[] = ($this->input->post('start')) + $key + 1;
            $row[] = $aRow['code_internal_proposal'];// mã đề xuất nội bộ
            $row[] = $aRow['name_recommended_list'];// Nhóm Giấy Phép-Chứng Nhận
            $row[] = $aRow['code_evaluate'];// mã
            $row[] = $aRow['name_evaluate'];// tên Giấy Phép-Chứng Nhận
            $row[] = _dt($aRow['date_of_issue']);// ngày cấp
            $row[] = $aRow['time_of_use'];// thời gian sử dụng
            $row[] = _dt($aRow['reissue_date']);// ngày tái cấp
            $row[] = number_format_data($aRow['money']);// chi phí
            $row[] = $aRow['unit_of_level'];// đơn vị cấp
            $row[] = $aRow['training_unit'];// đơn vị đào tạo
            $row[] = $aRow['notary_public'];// đơn vị công chứng
            $row[] = $aRow['date_update_foso'];//  Ngày cập nhật foso
            $row[] = _dt($aRow['adjustment_date']);// Ngày điều chỉnh
            $statusOne = '';
            $statusTwo = '';
            if(!empty($aRow['active']))
            {
                if($aRow['active'] == 1)
                {
                    $statusOne = 'Hoạt động';
                }
                else if($aRow['active'] == 2) {
                    $statusTwo = 'Ngừng hoạt động';
                }
            }
            else {
                $statusOne = 'Chưa Hoạt Động';
            }

            $row[] = $statusOne;// hoạt động
            $row[] = $statusTwo;// hoạt động
            $output['aaData'][] = $row;
        }
        echo json_encode($output);die();
    }

    public function getEvaluateExcel(){
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            // print_arrays($this->input->post());
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();
            $wherePayroll = [];
            $whereRecords = [];
            $whereDecision = [];
            $whereOvertime_detail = [];
            if($this->input->post('start_date_search')) {
                $start_date = to_sql_date($this->input->post('start_date_search'));
                $this->db->where('tblinternal_proposal.date >= "' . $start_date.'"', false, false);
            }
            if($this->input->post('end_date_search')) {
                $end_date = to_sql_date($this->input->post('end_date_search'));
                $this->db->where('DATE_FORMAT(tblinternal_proposal.date, "%Y-%m-%d") <= "' . $end_date . '"', false, false);
            }

            $this->db->select([
                'tblinternal_proposal.id as id',
                'tblinternal_proposal.code as code_internal_proposal',// mã đề xuất nội bộ
                'tbl_recommended_list.name as name_recommended_list', // Nhóm Giấy Phép-Chứng Nhận
                'tbl_evaluate.code_evaluate as code_evaluate', // mã
                'tbl_evaluate.name_evaluate as name_evaluate', // tên Giấy Phép-Chứng Nhận
                'tbl_evaluate.date_of_issue as date_of_issue', // tên cấp
                'tbl_evaluate.time_of_use as time_of_use', // thời gian sử dụng
                'tbl_evaluate.reissue_date as reissue_date', // ngày tái cấp
                'tblinternal_proposal.money as money', // chi phí
                'tbl_evaluate.unit_of_level as unit_of_level', // đơn vị cấp
                'tbl_evaluate.training_unit as training_unit', // đơn vị đào tạo
                'tbl_evaluate.notary_public as notary_public', // đơn vị công chứng
                '"" as date_update_foso', // Ngày cập nhật foso
                'tbl_evaluate.adjustment_date as adjustment_date', // Ngày điều chỉnh
                'tbl_evaluate.active as active', // hoạt động
            ]);
            $this->db->from('tblinternal_proposal');
            $this->db->join('tbl_recommended_list', 'tbl_recommended_list.id = tblinternal_proposal.recommended_list_group_id', 'left');
            $this->db->join('tbl_evaluate', 'tbl_evaluate.id = tblinternal_proposal.suggest_id', 'left');
            $this->db->join('tbl_category_recommended', 'tbl_category_recommended.id = tblinternal_proposal.category_recommended_id', 'left');
            $this->db->where_in('tbl_recommended_list.type_plan_propose', ["license", "certification"]);
            $this->db->where_in('tbl_category_recommended.type', ["license", "certification"]);
            $this->db->order_by('tblinternal_proposal.id desc');
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
            $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf("%0" . $decimals_number . "s",
                        0) : '');
            $company = get_option('invoice_company_name');
            $address = get_option('invoice_company_address');
            $company_vat = get_option('company_vat');
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name'  => 'Times New Roman'
                ),
            ]);
            $titleAppend = '';
            if(!empty($start_date_search) && !empty($end_date_search)){
                $titleAppend = 'Từ ngày ' . _d($start_date_search) . ' đến ngày ' . _d($end_date_search);
            }
            $objPHPExcel->getActiveSheet()->setCellValue('A1',
                ('Báo Cáo Tổng Hợp Giấy Phép') . $titleAppend)->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:R1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Mã Đề Xuất Nội Bộ');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Nhóm Giấy Phép-Chứng Nhận')->getStyle("C$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Mã');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Tên Giấy Phép-Chứng Nhận')->getStyle("E$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Ngày Cấp')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Thời Hạn Sử Dụng')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Ngày Tái Cấp')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Chi Phí')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Đơn Vị Cấp')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Đơn Vị Đào Tạo')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Đơn Vị Công Chứng')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$sttRow.'', 'Ngày Cập Nhật Foso')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$sttRow.'', 'Ngày Điều Chỉnh')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$sttRow.'', 'Đang Sử Dụng')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$sttRow.'', 'Ngưng Sử Dụng')->getStyle("P$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:P$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name'  => 'Times New Roman'
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
            $rowBegin = $sttRow;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $rowBegin++;
                    $row = [];
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['code_internal_proposal']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['name_recommended_list']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['code_evaluate'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['name_evaluate']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", _dt($value['date_of_issue']))->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $value['time_of_use'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", _dt($value['reissue_date']))->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $value['money'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->getStyle("I$rowBegin")
                        ->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $value['unit_of_level'])->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $value['training_unit'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $value['notary_public'])->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", "")->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", _dt($value['adjustment_date']))->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                    $statusOne = '';
                    $statusTwo = '';
                    if(!empty($value['active']))
                    {
                        if($value['active'] == 1)
                        {
                            $statusOne = 'Hoạt động';
                        }
                        else if($value['active'] == 2) {
                            $statusTwo = 'Ngừng hoạt động';
                        }
                    }
                    else {
                        $statusOne = 'Chưa Hoạt Động';
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", $statusOne)->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", $statusTwo)->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:P$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:A$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("I$rowBegin:I$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                }
            }
            $filename = lang('bao_cao_tong_hop_giay_phep') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
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

    public function fixation() {
        $data['title']   = _l('Báo Cáo Tổng Hợp Qui Định-Qui Chế');
        $this->load->view('admin/reports_summary/fixation', $data);
    }

    public function getFixation() {

        $tableObject = '(
                SELECT 
                      tbl_regulations.id as id,
                      tbl_regulations.code as code,
                      tbl_regulations.name as name,
                      tbl_regulations.date_issued as date_issued,
                      tbl_regulations.time_of_use as time_of_use,
                      tbl_regulations.reissue_date as reissue_date,
                      tbl_regulations.adjustment_date as adjustment_date,
                      tbl_regulations.active as active
                FROM tbl_regulations
                UNION
                SELECT 
                      tbldecision.id as id,
                      tbldecision.code as code,
                      tbldecision.name as name,
                      tbldecision.date_issued as date_issued,
                      tbldecision.time_of_use as time_of_use,
                      tbldecision.reissue_date as reissue_date,
                      tbldecision.adjustment_date as adjustment_date,
                      tbldecision.active as active
                FROM tbldecision
            ) tblobjectDetail
        ';


        $aColumns = [
            'tblinternal_proposal.id as id',
            'tblinternal_proposal.code as code_internal_proposal',// mã đề xuất nội bộ
            'tbl_recommended_list.name as name_recommended_list', // Nhóm Qui Định-Qui Chế
            'tblobjectDetail.code as code', // mã
            'tblobjectDetail.name as name', // Tên Quy Định Quy Chế
            'tblobjectDetail.date_issued as date_issued', // ngày ban hành
            'tblobjectDetail.time_of_use as time_of_use', // thời gian sử dụng
            'tblobjectDetail.reissue_date as reissue_date', // ngày tái ban hành
            '"" as date_update_foso', // Ngày cập nhật foso
            'tblobjectDetail.adjustment_date as adjustment_date', // Ngày điều chỉnh
            'tblobjectDetail.active as active', // Sử dụng
        ];

        $where = [];
        $sIndexColumn = 'id';
        $sTable       = 'tblinternal_proposal';
        $join = [
            'LEFT JOIN tbl_recommended_list ON tbl_recommended_list.id = tblinternal_proposal.recommended_list_group_id',
            'LEFT JOIN '.$tableObject.' ON tblobjectDetail.id = tblinternal_proposal.suggest_id',
            'LEFT JOIN tbl_category_recommended ON tbl_category_recommended.id = tblinternal_proposal.category_recommended_id',
        ];
        $where[] = 'AND tbl_recommended_list.type_plan_propose IN ("rules", "fixation", "decision")';
        $where[] = 'AND tbl_category_recommended.type IN ("rules", "fixation", "decision")';
        if($this->input->post('start_date')) {
            $start_date = to_sql_date($this->input->post('start_date'));
            $where[] = 'AND tblinternal_proposal.date >= "' . $start_date.'"';
        }
        if($this->input->post('end_date')) {
            $end_date = to_sql_date($this->input->post('end_date'));
            $where[] = 'AND DATE_FORMAT(tblinternal_proposal.date, "%Y-%m-%d") <= "' . $end_date . '"';
        }
        $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);
        $output  = $result['output'];
        $rResult  = $result['rResult'];
        foreach ($rResult as $key =>  $aRow) {
            $row = [];
            $row[] = ($this->input->post('start')) + $key + 1;
            $row[] = $aRow['code_internal_proposal'];// mã đề xuất nội bộ
            $row[] = $aRow['name_recommended_list'];// Nhóm Giấy Phép-Chứng Nhận
            $row[] = $aRow['code'];// mã
            $row[] = $aRow['name'];// tên Giấy Phép-Chứng Nhận
            $row[] = _dt($aRow['date_issued']);// ngày cấp
            $row[] = $aRow['time_of_use'];// thời gian sử dụng
            $row[] = _dt($aRow['reissue_date']);// ngày tái cấp
            $row[] = $aRow['date_update_foso'];//  Ngày cập nhật foso
            $row[] = _dt($aRow['adjustment_date']);// Ngày điều chỉnh
            $statusOne = '';
            $statusTwo = '';
            if(!empty($aRow['active']))
            {
                if($aRow['active'] == 1)
                {
                    $statusOne = 'Đang Sử Dụng';
                }
                else if($aRow['active'] == 2) {
                    $statusTwo = 'Ngừng Sử Dụng';
                }
            }
            else {
                $statusTwo = 'Chưa Sử Dụng';
            }

            $row[] = $statusOne;// Sử Dụng
            $row[] = $statusTwo;// Sử Dụng
            $output['aaData'][] = $row;
        }
        echo json_encode($output);die();
    }

    public function getFixationExcel(){
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            // print_arrays($this->input->post());
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();

            $tableObject = '(
                SELECT 
                      tbl_regulations.id as id,
                      tbl_regulations.code as code,
                      tbl_regulations.name as name,
                      tbl_regulations.date_issued as date_issued,
                      tbl_regulations.time_of_use as time_of_use,
                      tbl_regulations.reissue_date as reissue_date,
                      tbl_regulations.adjustment_date as adjustment_date,
                      tbl_regulations.active as active
                FROM tbl_regulations
                UNION
                SELECT 
                      tbldecision.id as id,
                      tbldecision.code as code,
                      tbldecision.name as name,
                      tbldecision.date_issued as date_issued,
                      tbldecision.time_of_use as time_of_use,
                      tbldecision.reissue_date as reissue_date,
                      tbldecision.adjustment_date as adjustment_date,
                      tbldecision.active as active
                FROM tbldecision
            ) tblobjectDetail
        ';

            if($this->input->post('start_date_search')) {
                $start_date = to_sql_date($this->input->post('start_date_search'));
                $this->db->where('tbl_evaluate.date_evaluate >= "' . $start_date.'"', false, false);
            }
            if($this->input->post('end_date_search')) {
                $end_date = to_sql_date($this->input->post('end_date_search'));
                $this->db->where('DATE_FORMAT(tbl_evaluate.date_evaluate, "%Y-%m-%d") <= "' . $end_date . '"', false, false);
            }

            $this->db->select([
                'tblinternal_proposal.id as id',
                'tblinternal_proposal.code as code_internal_proposal',// mã đề xuất nội bộ
                'tbl_recommended_list.name as name_recommended_list', // Nhóm Qui Định-Qui Chế
                'tblobjectDetail.code as code', // mã
                'tblobjectDetail.name as name', // Tên Quy Định Quy Chế
                'tblobjectDetail.date_issued as date_issued', // ngày ban hành
                'tblobjectDetail.time_of_use as time_of_use', // thời gian sử dụng
                'tblobjectDetail.reissue_date as reissue_date', // ngày tái ban hành
                '"" as date_update_foso', // Ngày cập nhật foso
                'tblobjectDetail.adjustment_date as adjustment_date', // Ngày điều chỉnh
                'tblobjectDetail.active as active', // Sử Dụng
            ]);
            $this->db->from('tblinternal_proposal');
            $this->db->join('tbl_recommended_list', 'tbl_recommended_list.id = tblinternal_proposal.recommended_list_group_id', 'left');
            $this->db->join($tableObject, 'tblobjectDetail.id = tblinternal_proposal.suggest_id', 'left');
            $this->db->join('tbl_category_recommended', 'tbl_category_recommended.id = tblinternal_proposal.category_recommended_id', 'left');
            $this->db->where_in('tbl_recommended_list.type_plan_propose', ["rules", "fixation", "decision"]);
            $this->db->where_in('tbl_category_recommended.type', ["rules", "fixation", "decision"]);
            $this->db->order_by('tblinternal_proposal.id desc');
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
            $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf("%0" . $decimals_number . "s",
                        0) : '');
            $company = get_option('invoice_company_name');
            $address = get_option('invoice_company_address');
            $company_vat = get_option('company_vat');
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name'  => 'Times New Roman'
                ),
            ]);
            $titleAppend = '';
            if(!empty($start_date_search) && !empty($end_date_search)){
                $titleAppend = 'Từ ngày ' . _d($start_date_search) . ' đến ngày ' . _d($end_date_search);
            }
            $objPHPExcel->getActiveSheet()->setCellValue('A1',
                ('Báo Cáo Tổng Hợp Qui Định Qui Chế') . $titleAppend)->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:R1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Mã Đề Xuất Nội Bộ');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Nhóm Qui Định-Qui Chế')->getStyle("C$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Mã');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Tên Qui Định-Qui Chế')->getStyle("E$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Ngày Ban Hành')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Thời Hạn Sử Dụng')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Ngày Tái Ban Hành')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Ngày Cập Nhật Foso')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Ngày Điều Chỉnh')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Đang Sử Dụng')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Ngưng Sử Dụng')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:P$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name'  => 'Times New Roman'
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
            $rowBegin = $sttRow;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $rowBegin++;
                    $statusOne = '';
                    $statusTwo = '';
                    if(!empty($value['active']))
                    {
                        if($value['active'] == 1)
                        {
                            $statusOne = 'Sử Dụng';
                        }
                        else if($value['active'] == 2) {
                            $statusTwo = 'Ngừng Sử Dụng';
                        }
                    }
                    else {
                        $statusOne = 'Chưa Sử Dụng';
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['code_internal_proposal']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['name_recommended_list']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['code'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['name']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", _dt($value['date_issued']))->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $value['time_of_use'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", _dt($value['reissue_date']))->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $value['date_update_foso'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", _dt($value['adjustment_date']))->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);


                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $statusOne)->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $statusTwo)->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:L$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:A$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("I$rowBegin:I$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                }
            }
            $filename = lang('bao_cao_tong_quy_dinh_quy_che') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
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


    public function tasks() {
        $data['title']   = _l('Báo Cáo Tổng Hợp Công Việc-Qui Trình');
        $this->load->view('admin/reports_summary/tasks', $data);
    }

    public function getTasks() {
        $aColumns = [
            'tblcategory_tasks.id as id',
            '(
                SELECT 
                    GROUP_CONCAT(tbl_room.name) 
                FROM tbl_room 
                WHERE FIND_IN_SET(tblcategory_tasks.departments, tbl_room.id)
            ) as name_departments_tasks',// phòng ban
            'tblcategory_tasks.code as code',//mã công việc
            'tblcategory_tasks.content as name', // Tên công việc
            "(SELECT count(*) FROM tblcategory_tasks_process WHERE tblcategory_tasks_process.id_category_tasks = tblcategory_tasks.id) as process",//CÓ QUY TRÌNH
            "(SELECT count(*) FROM tblcategory_tasks_process_child WHERE tblcategory_tasks_process_child.id_category_tasks = tblcategory_tasks.id) as process_child",//CÓ QUY CHUẨN
            "date_approve as date_approve",//Ngày Ban Hành/Có Cam Kết
            "date_create as date_create",//Ngày Áp dụng
            "date_update as date_update_foso",//Ngày Cập Nhật Foso
            "date_update as date_update",//Ngày điều chỉnh
            "active as active",//đang sử dng
        ];
        $where = [];
        $sIndexColumn = 'id';
        $sTable       = 'tblcategory_tasks';
        $join = [];
        if($this->input->post('start_date')) {
            $start_date = to_sql_date($this->input->post('start_date'));
            $where[] = 'AND tblcategory_tasks.date_create >= "' . $start_date.'"';
        }
        if($this->input->post('end_date')) {
            $end_date = to_sql_date($this->input->post('end_date'));
            $where[] = 'AND DATE_FORMAT(tblcategory_tasks.date_create, "%Y-%m-%d") <= "' . $end_date . '"';
        }
        $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);
        $output  = $result['output'];
        $rResult  = $result['rResult'];
        foreach ($rResult as $key =>  $aRow) {
            $row = [];
            $row[] = ($this->input->post('start')) + $key + 1;
            $row[] = $aRow['name_departments_tasks'];// Phòng Ban
            $row[] = $aRow['code'];// mã công việc
            $row[] = $aRow['name'];// tên công việc
            $row[] = !empty($aRow['process']) ? 'Có Qui Trình' : '';//Quy trình
            $row[] = !empty($aRow['process_child']) ? 'Có Qui ChuẨn' : '';//Quy chuẩn
            $row[] = !empty($aRow['date_approve']) ? _dt($aRow['date_approve']) : NULL;// ngày tái cấp
            $row[] = !empty($aRow['date_create']) ? _dt($aRow['date_create']) : NULL;// Ngày Áp dụng
            $row[] = !empty($aRow['date_update_foso']) ? _dt($aRow['date_update_foso']) : NULL;//  Ngày cập nhật foso
            $row[] = !empty($aRow['date_update']) ? _dt($aRow['date_update']) : '';//  Ngày điều chỉnh
            $statusOne = '';
            $statusTwo = '';
            if(!empty($aRow['active']))
            {
                if($aRow['active'] == 1)
                {
                    $statusOne = 'Đang Sử Dụng';
                }
                else if($aRow['active'] == 2) {
                    $statusTwo = 'Ngừng Sử Dụng';
                }
            }
            else {
                $statusTwo = 'Chưa Sử Dụng';
            }
            $row[] = $statusOne;// Sử Dụng
            $row[] = $statusTwo;// Sử Dụng
            $output['aaData'][] = $row;
        }
        echo json_encode($output);die();
    }

    public function getTasksExcel(){
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            // print_arrays($this->input->post());
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();

            if($this->input->post('start_date_search')) {
                $start_date = to_sql_date($this->input->post('start_date_search'));
                $this->db->where('tblcategory_tasks.date_create >= "' . $start_date.'"', false, false);
            }
            if($this->input->post('end_date_search')) {
                $end_date = to_sql_date($this->input->post('end_date_search'));
                $this->db->where('DATE_FORMAT(tblcategory_tasks.date_create, "%Y-%m-%d") <= "' . $end_date . '"', false, false);
            }

            $this->db->select([
                'tblcategory_tasks.id as id',
                '(
                    SELECT 
                        GROUP_CONCAT(tbl_room.name) 
                    FROM tbl_room 
                    WHERE FIND_IN_SET(tblcategory_tasks.departments, tbl_room.id)
                ) as name_departments_tasks',// phòng ban
                'tblcategory_tasks.code as code',//mã công việc
                'tblcategory_tasks.content as name', // Tên công việc
                "(SELECT count(*) FROM tblcategory_tasks_process WHERE tblcategory_tasks_process.id_category_tasks = tblcategory_tasks.id) as process",//CÓ QUY TRÌNH
                "(SELECT count(*) FROM tblcategory_tasks_process_child WHERE tblcategory_tasks_process_child.id_category_tasks = tblcategory_tasks.id) as process_child",//CÓ QUY CHUẨN
                "date_approve as date_approve",//Ngày Ban Hành/Có Cam Kết
                "date_create as date_create",//Ngày Áp dụng
                "date_update as date_update_foso",//Ngày Cập Nhật Foso
                "date_update as date_update",//Ngày điều chỉnh
                "active as active",//đang sử dng
            ]);
            $this->db->from('tblcategory_tasks');
            $this->db->order_by('tblcategory_tasks.id desc');
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
            $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf("%0" . $decimals_number . "s",
                        0) : '');
            $company = get_option('invoice_company_name');
            $address = get_option('invoice_company_address');
            $company_vat = get_option('company_vat');
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name'  => 'Times New Roman'
                ),
            ]);
            $titleAppend = '';
            if(!empty($start_date_search) && !empty($end_date_search)){
                $titleAppend = 'Từ ngày ' . _d($start_date_search) . ' đến ngày ' . _d($end_date_search);
            }
            $objPHPExcel->getActiveSheet()->setCellValue('A1',
                ('Báo Cáo Tổng Hợp Công Việc Qui Trình') . $titleAppend)->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:R1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Phòng Ban');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Mã Công Việc')->getStyle("C$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Tên Công Việc');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Có Qui Trình')->getStyle("E$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Có Qui Chuẩn')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Có Ban Hành/Có Cam Kết')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Ngày Áp Dụng')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Ngày Cập Nhật Foso')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Ngày Điều Chỉnh')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Đang Sử Dụng')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Ngưng Sử Dụng')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:L$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name'  => 'Times New Roman'
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
            $rowBegin = $sttRow;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $rowBegin++;
                    $statusOne = '';
                    $statusTwo = '';
                    if(!empty($value['active']))
                    {
                        if($value['active'] == 1)
                        {
                            $statusOne = 'Sử Dụng';
                        }
                        else if($value['active'] == 2) {
                            $statusTwo = 'Ngừng Sử Dụng';
                        }
                    }
                    else {
                        $statusOne = 'Chưa Sử Dụng';
                    }


                    //            row[] = ($this->input->post('start')) + $key + 1;
                    //            $row[] = $aRow['name_departments_tasks'];// Phòng Ban
                    //            $row[] = $aRow['code'];// mã công việc
                    //            $row[] = $aRow['name'];// tên công việc
                    //            $row[] = !empty($aRow['process']) ? 'Có Qui Trình' : '';//Quy trình
                    //            $row[] = !empty($aRow['process_child']) ? 'Có Qui ChuẨn' : '';//Quy chuẩn
                    //            $row[] = !empty($aRow['date_approve']) ? _dt($aRow['date_approve']) : NULL;// ngày tái cấp
                    //            $row[] = !empty($aRow['date_create']) ? _dt($aRow['date_create']) : NULL;// Ngày Áp dụng
                    //            $row[] = !empty($aRow['date_update_foso']) ? _dt($aRow['date_update_foso']) : NULL;//  Ngày cập nhật foso
                    //            $row[] = !empty($aRow['date_update']) ? _dt($aRow['date_update']) : '';//  Ngày điều chỉnh

                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['name_departments_tasks']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['code']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['name'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", (!empty($aRow['process']) ? 'Có Qui Trình' : ''))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", (!empty($aRow['process_child']) ? 'Có Qui ChuẨn' : ''))->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", _dt($value['date_approve']))->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", _dt($value['date_create']))->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", _dt($value['date_update_foso']))->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", _dt($value['date_update']))->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);


                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $statusOne)->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $statusTwo)->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:L$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:A$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("I$rowBegin:I$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                }
            }
            $filename = lang('bao_cao_tong_hop_cong_viec_qui_trinh') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
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


    public function InternalProposal() {
        $data['title']   = _l('Báo Cáo Tổng Hợp Đề Xuất');
        $this->load->view('admin/reports_summary/internal_proposal', $data);
    }

    public function getInternalProposal() {
        $aColumns = [
            'tblinternal_proposal.id as id',
            'tbl_recommended_list.name as name_recommended_list',// nhóm đề xuất
            'tbltype.name as name_group',// loại đề xuất
            'tblinternal_proposal.code as code',//mã đề xuất
            '(
                SELECT GROUP_CONCAT(CONCAT(COALESCE(tblstaff.firstname, "")," ",COALESCE(tblstaff.lastname, "")) SEPARATOR ",\n ")
                FROM tbl_internal_proposal_process 
                JOIN tblstaff ON tblstaff.staffid = tbl_internal_proposal_process.staff_id
                WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id 
                AND tbl_internal_proposal_process.bod = 5
            ) as staff_create',//Lập đề xuất
            '(
                SELECT GROUP_CONCAT(CONCAT(COALESCE(tblstaff.firstname, "")," ",COALESCE(tblstaff.lastname, "")) SEPARATOR ",\n ")
                FROM tbl_internal_proposal_process 
                JOIN tblstaff ON tblstaff.staffid = tbl_internal_proposal_process.staff_id
                WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id 
                AND tbl_internal_proposal_process.bod = 2
            ) as staff_active',//duyệt đề xuất
            '(
                SELECT GROUP_CONCAT(CONCAT(COALESCE(tblstaff.firstname, "")," ",COALESCE(tblstaff.lastname, "")) SEPARATOR ",\n ")
                FROM tbl_internal_proposal_process 
                JOIN tblstaff ON tblstaff.staffid = tbl_internal_proposal_process.staff_id
                WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id 
                AND tbl_internal_proposal_process.bod = 1
            ) as staff_active_pending',//duyệt thực thi
            '(
                SELECT GROUP_CONCAT(CONCAT(COALESCE(tblstaff.firstname, "")," ",COALESCE(tblstaff.lastname, "")) SEPARATOR ",\n ")
                FROM tbl_internal_proposal_process 
                JOIN tblstaff ON tblstaff.staffid = tbl_internal_proposal_process.staff_id
                WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id 
                AND tbl_internal_proposal_process.bod = 6
            ) as staff_finish',//hoàn thành
            '(
                SELECT GROUP_CONCAT(CONCAT(COALESCE(tblstaff.firstname, "")," ",COALESCE(tblstaff.lastname, "")) SEPARATOR ",\n ")
                FROM tbl_internal_proposal_process 
                JOIN tblstaff ON tblstaff.staffid = tbl_internal_proposal_process.staff_id
                WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id 
                AND tbl_internal_proposal_process.bod = 7
            ) as staff_kt_success',//kiểm tra soát hoàn thành
            '(
                SELECT GROUP_CONCAT(CONCAT(COALESCE(tblstaff.firstname, "")," ",COALESCE(tblstaff.lastname, "")) SEPARATOR ",\n ")
                FROM tbl_internal_proposal_process 
                JOIN tblstaff ON tblstaff.staffid = tbl_internal_proposal_process.staff_id
                WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id 
                AND tbl_internal_proposal_process.bod = 4
            ) as staff_success',//tra soát hoàn thành
            '(
                SELECT GROUP_CONCAT(tblproduction_report.reference_no SEPARATOR ",\n ") 
                FROM tblproduction_report
                WHERE tblproduction_report.id_internal_proposal = tblinternal_proposal.id
                AND type_report = 1
            ) as production_report',//báo cáo không phù hợp
            'tblviolate.reference_no as violate',//xử lý vi phạm
            '(
                SELECT GROUP_CONCAT(tbl_inspection_criteria.name SEPARATOR ",\n ")
                FROM tbl_setting_production_report_inspection_criteria_process 
                JOIN tbl_inspection_criteria ON tbl_inspection_criteria.id = tbl_setting_production_report_inspection_criteria_process.inspection_criteria
                WHERE tblviolate.id = tbl_setting_production_report_inspection_criteria_process.production_report
                AND tbl_setting_production_report_inspection_criteria_process.process_id = 5
            ) as prevent',//Phòng ngừa
            '"" as date_update_foso',//ngày cập nhật foso
        ];
        $where = [];
        $sIndexColumn = 'id';
        $sTable       = 'tblinternal_proposal';
        $join = [
            'LEFT JOIN tbl_recommended_list ON tbl_recommended_list.id = tblinternal_proposal.recommended_list_group_id',
            'LEFT JOIN tbl_recommended_list tbltype ON tbltype.id = tblinternal_proposal.recommended_list_id',
            'LEFT JOIN tblproduction_report tblviolate ON tblviolate.id_internal_proposal = tblinternal_proposal.id AND type_report = 4',
        ];
        if($this->input->post('start_date')) {
            $start_date = to_sql_date($this->input->post('start_date'));
            $where[] = 'AND tblinternal_proposal.date_create >= "' . $start_date.'"';
        }
        if($this->input->post('end_date')) {
            $end_date = to_sql_date($this->input->post('end_date'));
            $where[] = 'AND DATE_FORMAT(tblinternal_proposal.date_create, "%Y-%m-%d") <= "' . $end_date . '"';
        }
        $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);
        $output  = $result['output'];
        $rResult  = $result['rResult'];
        foreach ($rResult as $key =>  $aRow) {
            $row = [];
            $row[] = ($this->input->post('start')) + $key + 1;
            $row[] = $aRow['name_recommended_list'];// nhóm đề xuất
            $row[] = $aRow['name_group'];// loại đề xuất
            $row[] = $aRow['code'];//mã đề xuất
            $row[] = $aRow['staff_create'];//lập đề xuất
            $row[] = $aRow['staff_active'];//duyệt đề xuất
            $row[] = $aRow['staff_active_pending'];//duyệt thực thi
            $row[] = '<div style="white-space: break-spaces">' . $aRow['staff_finish']. '</div>';//hoàn thành đề xuất
            $row[] = $aRow['staff_kt_success'];//kim tra, tra soát hoàn thành
            $row[] = $aRow['staff_success'];//tra soát hoàn thành
            $row[] = $aRow['production_report'];//báo cáo không phù hợp
            $row[] = $aRow['violate'];//xử lý vi phạm
            $row[] = $aRow['prevent'];//Phòng ngừa
            $row[] = $aRow['date_update_foso'];//ngày cập nhật foso
            $output['aaData'][] = $row;
        }
        echo json_encode($output);die();
    }

    public function getInternalProposalExcel(){
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            // print_arrays($this->input->post());
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();

            if($this->input->post('start_date_search')) {
                $start_date = to_sql_date($this->input->post('start_date_search'));
                $this->db->where('tblinternal_proposal.date_create >= "' . $start_date.'"', false, false);
            }
            if($this->input->post('end_date_search')) {
                $end_date = to_sql_date($this->input->post('end_date_search'));
                $this->db->where('DATE_FORMAT(tblinternal_proposal.date_create, "%Y-%m-%d") <= "' . $end_date . '"', false, false);
            }

            $this->db->select([
                'tblinternal_proposal.id as id',
                'tbl_recommended_list.name as name_recommended_list',// nhóm đề xuất
                'tbltype.name as name_group',// loại đề xuất
                'tblinternal_proposal.code as code',//mã đề xuất
                '(
                SELECT GROUP_CONCAT(CONCAT(COALESCE(tblstaff.firstname, "")," ",COALESCE(tblstaff.lastname, "")) SEPARATOR ",\n ")
                FROM tbl_internal_proposal_process 
                JOIN tblstaff ON tblstaff.staffid = tbl_internal_proposal_process.staff_id
                WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id 
                AND tbl_internal_proposal_process.bod = 5
            ) as staff_create',//Lập đề xuất
                '(
                SELECT GROUP_CONCAT(CONCAT(COALESCE(tblstaff.firstname, "")," ",COALESCE(tblstaff.lastname, "")) SEPARATOR ",\n ")
                FROM tbl_internal_proposal_process 
                JOIN tblstaff ON tblstaff.staffid = tbl_internal_proposal_process.staff_id
                WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id 
                AND tbl_internal_proposal_process.bod = 2
            ) as staff_active',//duyệt đề xuất
                '(
                SELECT GROUP_CONCAT(CONCAT(COALESCE(tblstaff.firstname, "")," ",COALESCE(tblstaff.lastname, "")) SEPARATOR ",\n ")
                FROM tbl_internal_proposal_process 
                JOIN tblstaff ON tblstaff.staffid = tbl_internal_proposal_process.staff_id
                WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id 
                AND tbl_internal_proposal_process.bod = 1
            ) as staff_active_pending',//duyệt thực thi
                '(
                SELECT GROUP_CONCAT(CONCAT(COALESCE(tblstaff.firstname, "")," ",COALESCE(tblstaff.lastname, "")) SEPARATOR ",\n ")
                FROM tbl_internal_proposal_process 
                JOIN tblstaff ON tblstaff.staffid = tbl_internal_proposal_process.staff_id
                WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id 
                AND tbl_internal_proposal_process.bod = 6
            ) as staff_finish',//hoàn thành
                '(
                SELECT GROUP_CONCAT(CONCAT(COALESCE(tblstaff.firstname, "")," ",COALESCE(tblstaff.lastname, "")) SEPARATOR ",\n ")
                FROM tbl_internal_proposal_process 
                JOIN tblstaff ON tblstaff.staffid = tbl_internal_proposal_process.staff_id
                WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id 
                AND tbl_internal_proposal_process.bod = 7
            ) as staff_kt_success',//kiểm tra soát hoàn thành
                '(
                SELECT GROUP_CONCAT(CONCAT(COALESCE(tblstaff.firstname, "")," ",COALESCE(tblstaff.lastname, "")) SEPARATOR ",\n ")
                FROM tbl_internal_proposal_process 
                JOIN tblstaff ON tblstaff.staffid = tbl_internal_proposal_process.staff_id
                WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id 
                AND tbl_internal_proposal_process.bod = 4
            ) as staff_success',//tra soát hoàn thành
                '(
                SELECT GROUP_CONCAT(tblproduction_report.reference_no SEPARATOR ",\n ") 
                FROM tblproduction_report
                WHERE tblproduction_report.id_internal_proposal = tblinternal_proposal.id
                AND type_report = 1
            ) as production_report',//báo cáo không phù hợp
                'tblviolate.reference_no as violate',//xử lý vi phạm
                '(
                SELECT GROUP_CONCAT(tbl_inspection_criteria.name SEPARATOR ",\n ")
                FROM tbl_setting_production_report_inspection_criteria_process 
                JOIN tbl_inspection_criteria ON tbl_inspection_criteria.id = tbl_setting_production_report_inspection_criteria_process.inspection_criteria
                WHERE tblviolate.id = tbl_setting_production_report_inspection_criteria_process.production_report
                AND tbl_setting_production_report_inspection_criteria_process.process_id = 5
            ) as prevent',//Phòng ngừa
                '"" as date_update_foso',//ngày cập nhật foso
            ]);//ngày cập nhật foso);
            $this->db->join('tbl_recommended_list', 'tbl_recommended_list.id = tblinternal_proposal.recommended_list_group_id', 'left');
            $this->db->join('tbl_recommended_list tbltype', 'tbltype.id = tblinternal_proposal.recommended_list_id', 'left');
            $this->db->join('tblproduction_report tblviolate', 'tblviolate.id_internal_proposal = tblinternal_proposal.id AND type_report = 4', 'left');
            $this->db->from('tblinternal_proposal');
            $this->db->order_by('tblinternal_proposal.id desc');
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
            $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf("%0" . $decimals_number . "s",
                        0) : '');
            $company = get_option('invoice_company_name');
            $address = get_option('invoice_company_address');
            $company_vat = get_option('company_vat');
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name'  => 'Times New Roman'
                ),
            ]);
            $titleAppend = '';
            if(!empty($start_date_search) && !empty($end_date_search)){
                $titleAppend = 'Từ ngày ' . _d($start_date_search) . ' đến ngày ' . _d($end_date_search);
            }
            $objPHPExcel->getActiveSheet()->setCellValue('A1',
                ('Báo Cáo Tổng Hợp Đề Xuất') . $titleAppend)->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:R1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Nhóm Đề Xuất');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Loại Đề Xuất')->getStyle("C$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Mã Đề Xuất');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Lập Đề Xuất')->getStyle("E$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Duyệt Đề Xuất')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Duyệt Thực Thi')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Hoàn Thành Đề Xuất')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Kiểm Tra Hoàn Thành')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Tra Soát Hoàn Thành')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Mã BCKPH')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Xử Lý Vi Phạm')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$sttRow.'', 'Qui Trình Phòng Ngừa')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$sttRow.'', 'Ngày Cập Nhật Foso')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:N$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name'  => 'Times New Roman'
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
            $rowBegin = $sttRow;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['name_recommended_list']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['name_group']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['code'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", $value['staff_create'])->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $value['staff_active'])->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $value['staff_active_pending'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", $value['staff_finish'])->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $value['staff_kt_success'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $value['staff_success'])->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);


                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $value['production_report'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $value['violate'])->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", $value['prevent'])->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", $value['date_update_foso'])->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:N$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:A$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("I$rowBegin:I$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                }
            }
            $filename = lang('bao_cao_tong_hop_de_xuat') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
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

    public function InternalProposalUrgent() {
        $data['title']   = _l('Báo Cáo Tổng Hợp Đề Xuất Gấp Khẩn');
        $this->load->view('admin/reports_summary/internal_proposal_urgent', $data);
    }

    public function getInternalProposalUrgent() {
//        $tb_tamp = "(
//            SELECT
//                tblinternal_proposal_recommended.id_internal_proposal as id_internal_proposal,
//                GROUP_CONCAT(CONCAT('-',tbl_recommended_list.name) SEPARATOR '<br>') as name
//            FROM tblinternal_proposal_recommended
//            JOIN tbl_recommended_list ON tbl_recommended_list.id = tblinternal_proposal_recommended.recommended_list_detail_id
//            WHERE tbl_recommended_list.name like '%khẩn%'
//            GROUP BY tblinternal_proposal_recommended.id_internal_proposal
//        ) tb_tamp";
        $tb_tamp = "(
            SELECT
                tblinternal_proposal_recommended.id_internal_proposal as id_internal_proposal,
                GROUP_CONCAT(CONCAT('-',tbl_recommended_list.name) SEPARATOR '<br>') as name
            FROM tblinternal_proposal_recommended
            JOIN tbl_recommended_list ON tbl_recommended_list.id = tblinternal_proposal_recommended.recommended_list_detail_id
            WHERE tbl_recommended_list.name like '%Khẩn%'
            GROUP BY tblinternal_proposal_recommended.id_internal_proposal
        ) tb_tamp";


        $aColumns = [
            'tblinternal_proposal.id as id',
            'tblinternal_proposal.create_by as staff_urgent',// nguời xác nhận gấp khẩn
            'tbltype.name as name_group',// loại đề xuất
            'tblinternal_proposal.code as code',//mã đề xuất
            '(
                SELECT date_status
                FROM tbl_internal_proposal_process 
                WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id 
                AND tbl_internal_proposal_process.bod = 5
                LIMIT 1
            ) as date_create',//Lập đề xuất
            '(
                SELECT date_status
                FROM tbl_internal_proposal_process 
                WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id 
                AND tbl_internal_proposal_process.bod = 2
                LIMIT 1
            ) as date_active',//duyệt đề xuất
            '(
                SELECT date_status
                FROM tbl_internal_proposal_process 
                WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id 
                AND tbl_internal_proposal_process.bod = 1
                LIMIT 1
            ) as date_active_pending',//duyệt thực thi
            '(
                SELECT date_status
                FROM tbl_internal_proposal_process 
                WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id 
                AND tbl_internal_proposal_process.bod = 6
                LIMIT 1
            ) as date_finish',//hoàn thành
            '(
                SELECT MAX(date_status)
                FROM tbl_internal_proposal_process 
                WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id 
                AND tbl_internal_proposal_process.bod = 7
                LIMIT 1
            ) as date_kt_success',//kiểm tra soát hoàn thành
            '(
                SELECT date_status
                FROM tbl_internal_proposal_process 
                WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id 
                AND tbl_internal_proposal_process.bod = 4
                LIMIT 1
            ) as date_success',//tra soát hoàn thành
            '(
                SELECT GROUP_CONCAT(tblproduction_report.reference_no SEPARATOR ",\n ") 
                FROM tblproduction_report
                WHERE tblproduction_report.id_internal_proposal = tblinternal_proposal.id
                AND type_report = 1
            ) as production_report',//báo cáo không phù hợp
            'tblviolate.reference_no as violate',//xử lý vi phạm
            '(
                SELECT GROUP_CONCAT(tbl_inspection_criteria.name SEPARATOR ",\n ")
                FROM tbl_setting_production_report_inspection_criteria_process 
                JOIN tbl_inspection_criteria ON tbl_inspection_criteria.id = tbl_setting_production_report_inspection_criteria_process.inspection_criteria
                WHERE tblviolate.id = tbl_setting_production_report_inspection_criteria_process.production_report
                AND tbl_setting_production_report_inspection_criteria_process.process_id = 5
            ) as prevent',//Phòng ngừa
            '(
                SELECT date_status
                FROM tbl_internal_proposal_process 
                WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id 
                AND tbl_internal_proposal_process.bod = 3
                LIMIT 1
            ) as date_update_foso',//ngày cập nhật foso
            '"" as date_edit_foso',// ngày điều chỉnh foso
            '"" as time_tvp', //Thời Gian Tái Vi Phạm
            '"" as date_tvp', //Ngày Tái Vi Phạm
            '"" as price_tvp', //Chi Phí Thiệt Hại
            '"" as type_kl', //Hình Thức Kỷ Luật
            '"" as date_append_kl', //Ngày Áp Dụng Kỷ Luật
            '"" as date_success_save', //Ngày Hoàn Thành Lưu Trữ

        ];
//        'tb_tamp.name as name_detail_suggest',
        $where = [];
        $sIndexColumn = 'id';
        $sTable       = 'tblinternal_proposal';
        $join = [
            'LEFT JOIN tbl_recommended_list tbltype ON tbltype.id = tblinternal_proposal.recommended_list_id',
            'LEFT JOIN tblproduction_report tblviolate ON tblviolate.id_internal_proposal = tblinternal_proposal.id AND type_report = 4',
            'JOIN ' . $tb_tamp . ' ON tb_tamp.id_internal_proposal = tblinternal_proposal.id'
        ];
        if($this->input->post('start_date')) {
            $start_date = to_sql_date($this->input->post('start_date'));
            $where[] = 'AND tblinternal_proposal.date_create >= "' . $start_date.'"';
        }
        if($this->input->post('end_date')) {
            $end_date = to_sql_date($this->input->post('end_date'));
            $where[] = 'AND DATE_FORMAT(tblinternal_proposal.date_create, "%Y-%m-%d") <= "' . $end_date . '"';
        }
        $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);
        $output  = $result['output'];
        $rResult  = $result['rResult'];
        foreach ($rResult as $key =>  $aRow) {
            $row = [];
            $row[] = ($this->input->post('start')) + $key + 1;
            $row[] = get_staff_full_name($aRow['staff_urgent']);// Người xác nhận gấp khẩn
            $row[] = $aRow['name_group'];// loại đề xuất
            $row[] = $aRow['code'];//mã đề xuất
            $row[] = !empty($aRow['date_create']) ? _dt($aRow['date_create']) : '';//lập đề xuất
            $row[] = !empty($aRow['date_active']) ? _dt($aRow['date_active']) : '';//duyệt đề xuất
            $row[] = !empty($aRow['date_active_pending']) ? _dt($aRow['date_active_pending']) : '';//duyệt thực thi
            $row[] = !empty($aRow['date_finish']) ? _dt($aRow['date_finish']) : '';//hoàn thành đề xuất
            $row[] = !empty($aRow['date_kt_success']) ? _dt($aRow['date_kt_success']) : '';//kim tra, tra soát hoàn thành
            $row[] = !empty($aRow['date_success']) ? $aRow['date_success'] : '';//tra soát hoàn thành
            $row[] = $aRow['production_report'];//báo cáo không phù hợp
            $row[] = $aRow['violate'];//xử lý vi phạm
            $row[] = $aRow['prevent'];//Phòng ngừa
            $row[] = $aRow['date_update_foso'];//ngày cập nhật foso
            $row[] = $aRow['date_edit_foso']; // ngày điều chỉnh foso
            $row[] = $aRow['time_tvp']; // Thời Gian Tái Vi Phạm
            $row[] = $aRow['date_tvp']; // Ngày Tái Vi Phạm
            $row[] = $aRow['price_tvp']; // Chi Phí Thiệt Hại
            $row[] = $aRow['type_kl']; // Hình Thức Kỷ Luật
            $row[] = $aRow['date_append_kl']; // Ngày Áp Dụng Kỷ Luật
            $row[] = $aRow['date_success_save']; // Ngày Hoàn Thành Lưu Trữ
            $output['aaData'][] = $row;
        }
        echo json_encode($output);die();
    }

    public function getInternalProposalUrgentExcel(){
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            // print_arrays($this->input->post());
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();
            $tb_tamp = "(
                SELECT
                    tblinternal_proposal_recommended.id_internal_proposal as id_internal_proposal,
                    GROUP_CONCAT(CONCAT('-',tbl_recommended_list.name) SEPARATOR '<br>') as name
                FROM tblinternal_proposal_recommended
                JOIN tbl_recommended_list ON tbl_recommended_list.id = tblinternal_proposal_recommended.recommended_list_detail_id
                WHERE tbl_recommended_list.name like '%Khẩn%'
                GROUP BY tblinternal_proposal_recommended.id_internal_proposal
            ) tb_tamp";

            if($this->input->post('start_date_search')) {
                $start_date = to_sql_date($this->input->post('start_date_search'));
                $this->db->where('tblinternal_proposal.date_create >= "' . $start_date.'"', false, false);
            }
            if($this->input->post('end_date_search')) {
                $end_date = to_sql_date($this->input->post('end_date_search'));
                $this->db->where('DATE_FORMAT(tblinternal_proposal.date_create, "%Y-%m-%d") <= "' . $end_date . '"', false, false);
            }

            $this->db->select([
                'tblinternal_proposal.id as id',
                'tblinternal_proposal.create_by as staff_urgent',// nguời xác nhận gấp khẩn
                'tbltype.name as name_group',// loại đề xuất
                'tblinternal_proposal.code as code',//mã đề xuất
                '(
                    SELECT date_status
                    FROM tbl_internal_proposal_process 
                    WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id 
                    AND tbl_internal_proposal_process.bod = 5
                    LIMIT 1
                ) as date_create',//Lập đề xuất
                '(
                    SELECT date_status
                    FROM tbl_internal_proposal_process 
                    WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id 
                    AND tbl_internal_proposal_process.bod = 2
                    LIMIT 1
                ) as date_active',//duyệt đề xuất
                '(
                    SELECT date_status
                    FROM tbl_internal_proposal_process 
                    WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id 
                    AND tbl_internal_proposal_process.bod = 1
                    LIMIT 1
                ) as date_active_pending',//duyệt thực thi
                '(
                    SELECT date_status
                    FROM tbl_internal_proposal_process 
                    WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id 
                    AND tbl_internal_proposal_process.bod = 6
                    LIMIT 1
                ) as date_finish',//hoàn thành
                '(
                    SELECT MAX(date_status)
                    FROM tbl_internal_proposal_process 
                    WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id 
                    AND tbl_internal_proposal_process.bod = 7
                    LIMIT 1
                ) as date_kt_success',//kiểm tra soát hoàn thành
                '(
                    SELECT date_status
                    FROM tbl_internal_proposal_process 
                    WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id 
                    AND tbl_internal_proposal_process.bod = 4
                    LIMIT 1
                ) as date_success',//tra soát hoàn thành
                '(
                    SELECT GROUP_CONCAT(tblproduction_report.reference_no SEPARATOR ",\n ") 
                    FROM tblproduction_report
                    WHERE tblproduction_report.id_internal_proposal = tblinternal_proposal.id
                    AND type_report = 1
                ) as production_report',//báo cáo không phù hợp
                'tblviolate.reference_no as violate',//xử lý vi phạm
                '(
                    SELECT GROUP_CONCAT(tbl_inspection_criteria.name SEPARATOR ",\n ")
                    FROM tbl_setting_production_report_inspection_criteria_process 
                    JOIN tbl_inspection_criteria ON tbl_inspection_criteria.id = tbl_setting_production_report_inspection_criteria_process.inspection_criteria
                    WHERE tblviolate.id = tbl_setting_production_report_inspection_criteria_process.production_report
                    AND tbl_setting_production_report_inspection_criteria_process.process_id = 5
                ) as prevent',//Phòng ngừa
                '(
                    SELECT date_status
                    FROM tbl_internal_proposal_process 
                    WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id 
                    AND tbl_internal_proposal_process.bod = 3
                    LIMIT 1
                ) as date_update_foso',//ngày cập nhật foso
                '"" as date_edit_foso',// ngày điều chỉnh foso
                '"" as time_tvp', //Thời Gian Tái Vi Phạm
                '"" as date_tvp', //Ngày Tái Vi Phạm
                '"" as price_tvp', //Chi Phí Thiệt Hại
                '"" as type_kl', //Hình Thức Kỷ Luật
                '"" as date_append_kl', //Ngày Áp Dụng Kỷ Luật
                '"" as date_success_save', //Ngày Hoàn Thành Lưu Trữ
            ]);//ngày cập nhật foso);

            $this->db->join('tbl_recommended_list tbltype', 'tbltype.id = tblinternal_proposal.recommended_list_id', 'left');
            $this->db->join('tblproduction_report tblviolate', 'tblviolate.id_internal_proposal = tblinternal_proposal.id AND type_report = 4', 'left');
            $this->db->join($tb_tamp, 'tb_tamp.id_internal_proposal = tblinternal_proposal.id', 'left');
            $this->db->from('tblinternal_proposal');
            $this->db->order_by('tblinternal_proposal.id desc');
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
            $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf("%0" . $decimals_number . "s",
                        0) : '');
            $company = get_option('invoice_company_name');
            $address = get_option('invoice_company_address');
            $company_vat = get_option('company_vat');
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name'  => 'Times New Roman'
                ),
            ]);
            $titleAppend = '';
            if(!empty($start_date_search) && !empty($end_date_search)){
                $titleAppend = 'Từ ngày ' . _d($start_date_search) . ' đến ngày ' . _d($end_date_search);
            }
            $objPHPExcel->getActiveSheet()->setCellValue('A1',
                ('Báo Cáo Tổng Hợp Đề Xuất Gấp Khẩn') . $titleAppend)->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:T1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Người Xác Nhận Gấp Khẩn');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Loại Đề Xuất')->getStyle("C$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Mã Đề Xuất');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Giờ Lập Đề Xuất')->getStyle("E$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Giờ Duyệt Đề Xuất')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Giờ Duyệt Thực Thi')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Hoàn Thành Đề Xuất')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Kiểm Tra Hoàn Thành')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Tra Soát Hoàn Thành')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Mã BCKPH')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Xử Lý Vi Phạm')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$sttRow.'', 'Qui Trình Phòng Ngừa')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$sttRow.'', 'Ngày Cập Nhật Foso')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$sttRow.'', 'Ngày Điều Chỉnh Foso')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$sttRow.'', 'Thời Gian Tái Vi Phạm')->getStyle("P$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$sttRow.'', 'Ngày Tái Vi Phạm')->getStyle("Q$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('R'.$sttRow.'', 'Chi Phí Thiệt Hại')->getStyle("R$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('S'.$sttRow.'', 'Hình Thức Kỷ Luật')->getStyle("S$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('T'.$sttRow.'', 'Ngày Áp Dụng Kỷ Luật')->getStyle("T$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('U'.$sttRow.'', 'Ngày Hoàn Thành Lưu Trữ')->getStyle("U$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:U$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name'  => 'Times New Roman'
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
            $rowBegin = $sttRow;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", get_staff_full_name($value['staff_urgent']));
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['name_group']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['code'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", (!empty($aRow['date_create']) ? _dt($aRow['date_create']) : ''))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", (!empty($aRow['date_active']) ? _dt($aRow['date_active']) : ''))->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", (!empty($aRow['date_active_pending']) ? _dt($aRow['date_active_pending']) : ''))->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", (!empty($aRow['date_finish']) ? _dt($aRow['date_finish']) : ''))->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", (!empty($aRow['date_kt_success']) ? _dt($aRow['date_kt_success']) : ''))->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", (!empty($aRow['date_success']) ? $aRow['date_success'] : ''))->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);


                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $aRow['production_report'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $value['violate'])->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", $value['prevent'])->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", $value['date_update_foso'])->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", $value['date_edit_foso'])->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", $value['time_tvp'])->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin", $value['date_tvp'])->getStyle("Q$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin", $value['price_tvp'])->getStyle("R$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("S$rowBegin", $value['type_kl'])->getStyle("S$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("T$rowBegin", $value['date_append_kl'])->getStyle("T$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("U$rowBegin", $value['date_success_save'])->getStyle("U$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:U$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:A$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("I$rowBegin:I$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                }
            }
            $filename = lang('bao_cao_tong_hop_de_xuat_gap_khan') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(20);
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


    public function quotes() {
        $data = [];
        $data['title'] = lang('Báo Cáo Tổng Hợp Báo Giá/Tháng');
        $this->load->view('admin/reports_summary/quotes', $data);
    }

    public function getSummaryQuotes() {
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $customers_search = $this->input->post('customers_search');

        $aColumns = [
            'tbl_quotes.id as id',
            'tbl_quotes.customer_id as customer_id',
            '"" as customer_name',
            '"" as brand',
            '"" as code_quotation_request',
            'tbl_quotes.reference_no as reference_no',
            'tbl_quotes.date as date',
            'IF(tbl_quotes.date_updated IS NOT NULL, tbl_quotes.date_updated, tbl_quotes.date_created) as date_finished',
            '"" as item_code',
            '"" as item_name',
            '"" as is_lot',
            '"" as is_child',
            '"" as name_discount',
            'IF(tbl_quotes.status = "approved", tbl_quotes.date_status, "") as date_status',
            'tbl_quotes.date_updated as date_updated',
            '"" as is_order',
            '"" as is_not_order',
            '"" as is_quote_again',
            '"" as code_bckph',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_quotes';
        $join = [];

        $groupByAndOrderBy = '';
        $where = [];

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_quotes.date >= '$start_date_search'");
        }
        
        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_quotes.date <= '$end_date_search'");
        }

        if (!empty($customers_search)) {
            $customers_search = $this->db->escape($customers_search);
            array_push($where, "AND tbl_quotes.customer_id = $customers_search");
        }

        $groupByAndOrderBy = 'ORDER BY tbl_quotes.id DESC';
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], $groupByAndOrderBy, []);

        $output = $result['output'];
        $rResult = $result['rResult'];


        if (!empty($rResult)) {
            $arrCustomerId = [];
            $arrQuoteId = [];
            foreach ($rResult as $key => $value) {
                $arrCustomerId[] = $value['customer_id'];
                $arrQuoteId[] = $value['id'];
            }

            if (!empty($arrCustomerId)) {
                $arrCustomerId = array_unique($arrCustomerId);

                $tbGroupCustomer = '(
                    SELECT
                        tblcustomer_groups.customer_id as customer_id,
                        GROUP_CONCAT(tblcustomers_groups.name) as group_name
                    FROM tblcustomer_groups
                    INNER JOIN tblcustomers_groups ON tblcustomers_groups.id = tblcustomer_groups.groupid
                    WHERE tblcustomer_groups.customer_id IN ('.implode(',', $arrCustomerId).')
                    GROUP BY tblcustomer_groups.customer_id
                ) tb_customer_group';

                $this->db->select('
                    tblclients.userid,
                    tblclients.company_short,
                    tblclients.company,
                    tb_customer_group.group_name as brand,
                    tbl_discount.name as name_discount
                ', false);
                $this->db->from('tblclients');
                $this->db->join($tbGroupCustomer, 'tb_customer_group.customer_id = tblclients.userid', 'left');
                $this->db->join('tbl_discount', 'tbl_discount.id = tblclients.discount_id', 'left');
                $this->db->where_in('tblclients.userid', $arrCustomerId, false);
                $listCustomers = $this->db->get()->result_array();
                if (!empty($listCustomers)) {
                    $listCustomers = array_reduce($listCustomers, function($carry, $item) {
                        $carry[$item['userid']] = $item;
                        return $carry;
                    });
                }
            }

            //quote items

            $group_price_detail = "(
                SELECT 
                    tblgroup_price_detail.product_id as product_id,
                    tblgroup_price_detail.quotes_id as quotes_id,
                    tblgroup_price_detail.is_lot as is_lot

                FROM tblgroup_price_detail
                WHERE tblgroup_price_detail.quotes_id IN (".implode(',', $arrQuoteId).")
                GROUP BY tblgroup_price_detail.product_id, tblgroup_price_detail.quotes_id
            ) tb_group_price_detail";

            $this->db->select('
                tbl_quote_items.quote_id as quote_id,
                tbl_products.code as item_code,
                tbl_products.name as item_name,
                tb_group_price_detail.is_lot as is_lot,
            ');
            $this->db->from('tbl_quote_items');
            $this->db->join('tbl_products', 'tbl_products.id = tbl_quote_items.item_id');
            $this->db->join($group_price_detail, 'tb_group_price_detail.product_id = tbl_quote_items.item_id AND tb_group_price_detail.quotes_id = tbl_quote_items.quote_id', 'left');
            $this->db->where_in('tbl_quote_items.quote_id', $arrQuoteId, false);
            $this->db->where('tbl_quote_items.type_item', 'products');
            $quoteItems = $this->db->get()->result_array();
            if (!empty($quoteItems)) {
                $quoteItems = array_reduce($quoteItems, function($carry, $item) {
                    $carry[$item['quote_id']][] = $item;
                    return $carry;
                });
            }

            //is orders
            $this->db->select('
                tbl_orders.quotes_id,
                1 as is_order
            ', false);
            $this->db->from('tbl_orders');
            $this->db->where_in('tbl_orders.quotes_id', $arrQuoteId, false);
            $this->db->group_by('tbl_orders.quotes_id');
            $orders = $this->db->get()->result_array();
            if (!empty($orders)) {
                $orders = array_reduce($orders, function($carry, $item) {
                    $carry[$item['quotes_id']] = $item['is_order'];
                    return $carry;
                });
            }

            //yêu cầu
        }

        $aColumns = handlingColumns($aColumns);
        $stt = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $stt++;
            $customer_id = $aRow['customer_id'];
            $dtCustomer = $listCustomers[$customer_id] ?? null;
            $company_short = $dtCustomer['company_short'] ?? '';
            $customer_name = $dtCustomer['company'] ?? '';
            $name_discount = $dtCustomer['name_discount'] ?? '';
            $brand = $dtCustomer['brand'] ?? '';
            $items = $quoteItems[$aRow['id']] ?? [];
            $order = $orders[$aRow['id']] ?? 0;
            $is_order = !empty($order['is_order']) ? 1 : 0;

            if (!empty($items)) {
                foreach ($items as $kI => $item) {
                    $row = [];

                    $is_lot = $item['is_lot'] ?? 0;
                    foreach ($aColumns as $k => $v) {
                        $_data = $aRow[$v];
                        if ($kI != 0 && in_array($v, ['id', 'customer_id', 'customer_name', 'brand', 'date', 'date_finished', 'reference_no', 'name_discount', 'date_status', 'date_updated', 'is_order', 'is_not_order'])) {
                            $row[] = '';
                        } else {
                            if ($v == 'id') {
                                $row[] = '<div class="text-center">'.$stt.'</div>';
                            } else if ($v == 'customer_id') {
                                $row[] = $company_short;
                            } else if ($v == 'customer_name') {
                                $row[] = $customer_name;
                            } else if ($v == 'brand') {
                                $row[] = $brand;
                            } else if ($v == 'date') {
                                $row[] = _dt($_data);
                            } else if ($v == 'date_finished') {
                                $row[] = _dt($_data);
                            } else if ($v == 'item_code') {
                                $row[] = $item['item_code'];
                            } else if ($v == 'item_name') {
                                $row[] = '<div style="width: 120px; word-break: break-all;">'.$item['item_name'].'</div>';
                            } else if ($v == 'is_lot') {
                                $row[] = $is_lot == 1 ? '1' : '';
                            } else if ($v == 'is_child') {
                                $row[] = $is_lot == 0 ? '1' : '';
                            } else if ($v == 'name_discount') {
                                $row[] = $name_discount;
                            } else if ($v == 'date_status') {
                                $row[] = $_data ? _dt($_data) : '';
                            } else if ($v == 'date_updated') { 
                                $row[] = $_data ? _dt($_data) : '';
                            } else if ($v == 'is_order') {
                                $row[] = $is_order == 1 ? '1' : '';
                            } else if ($v == 'is_not_is_order') {
                                $row[] = $is_order == 0 ? '1' : '';
                            } else {
                                $row[] = $_data;
                            }
                        }
                    }
                    $output['aaData'][] = $row;
                }
            }
            
        }
        
        $output['title_excel'] = [handlingTitleExcel()['title']];
        echo json_encode($output);
    }

    public function sample_development() {
        $data = [];
        $data['title'] = lang('Báo Cáo Tổng Hợp PTM/Tháng');
        $this->load->view('admin/reports_summary/sample_development', $data);
    }

    public function orders() {
        $data = [];
        $data['title'] = lang('Báo Cáo Tổng Hợp Thu/Tháng');
        $this->load->view('admin/reports_summary/orders', $data);
    }

    public function planning() {
        $data = [];
        $data['title'] = lang('Báo Cáo Tổng Hợp KH Sản Xuất');
        $this->load->view('admin/reports_summary/planning', $data);
    }

    public function productivity() {
        $data = [];
        $data['title'] = lang('Báo Cáo Tổng Hợp Năng Suất Công Đoạn');
        $this->load->view('admin/reports_summary/productivity', $data);
    }
}
