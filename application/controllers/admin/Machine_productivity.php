<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Machine_productivity extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');
        $this->load->model('category_model');

        $this->preViewMachineProductivity = true;
    }

    public function index()
    {
        if (!$this->preViewMachineProductivity) {
            access_denied();
        }
        $data['title'] = _l('Năng suất máy');
        $this->load->view('admin/machine_productivity/index', $data);
    }

    public function getMachineProductivity(){
        $machines_search = $this->input->post('machines_search');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $whereTamp = '';
        $whereTampPurchase = '';
        if (!empty($machines_search)) {
            $whereTamp .= ' AND tbl_production_lists_items.may_in = ' . $machines_search;
            $whereTampPurchase .= ' AND tbl_production_lists_items.may_in = ' . $machines_search;
        }
        if (!empty($start_date_search)) {
            $whereTamp .= ' AND DATE_FORMAT(tbl_production_lists_items.ngay_hoan_thanh_in, "%Y-%m-%d") >= "' . to_sql_date($start_date_search) . '"';
            $whereTampPurchase .= ' AND DATE_FORMAT(tbl_production_lists_items.ngay_hoan_thanh_in, "%Y-%m-%d") >= "' . to_sql_date($start_date_search) . '"';
        }
        if (!empty($end_date_search)) {
            $whereTamp .= ' AND DATE_FORMAT(tbl_production_lists_items.ngay_hoan_thanh_in, "%Y-%m-%d") <= "' . to_sql_date($end_date_search) . '"';
            $whereTampPurchase .= ' AND DATE_FORMAT(tbl_production_lists_items.ngay_hoan_thanh_in, "%Y-%m-%d") <= "' . to_sql_date($end_date_search) . '"';
        }
        $tb_tamp = "(
            SELECT
                tbl_production_lists_items.may_in as machines_id,
                SUM(
                    COALESCE(tbl_production_lists_items.thoi_gian_in, 0) +
                    COALESCE(tbl_production_lists_items.thoi_gian_xu_ly, 0)
                ) AS thoi_gian_du_kien,
                SUM(tbl_production_lists_items.thoi_gian_xu_ly) as thoi_gian_xu_ly,
                count(DISTINCT tbl_productions_orders.id) as totalManu
            FROM tbl_production_lists_items
            JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_production_lists_items.po_id
            WHERE tbl_production_lists_items.may_in != 0 $whereTamp
            GROUP BY tbl_production_lists_items.may_in
        ) tb_tamp";
        $tb_tamp_production = "(
            SELECT
                tblproduction_report.machines_id as machines_id,
                tblproduction_report.production_stage as stage_id,
                SUM(tblproduction_report.downtime) as downtime
            FROM tblproduction_report
            GROUP BY tblproduction_report.machines_id,tblproduction_report.production_stage
        ) tb_tamp_production";

        $tb_tamp_thuc_te = "(
            SELECT
                tbl_production_lists_items.may_in as machines_id,
                SUM(tbl_production_lists_items.tong_tua) as total_quantity
            FROM tbl_production_lists_items
            WHERE tbl_production_lists_items.may_in != '' $whereTampPurchase
            GROUP BY tbl_production_lists_items.may_in
        ) tb_tamp_thuc_te";

        $aColumns = [
            'tbl_machines.id as id',
            'tbl_machines.code as code_machines',
            'tbl_machines.name as name_machines',
            'tb_tamp.totalManu as totalManu',
            '0 as quota_productivity',
            '0 as total_hour',
            '"" as result'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_machines';
        $where = [];
        $filter = [];

        if (!empty($machines_search)){
            array_push($where,'AND tbl_machines.id = '.$machines_search.'');
        }

        $join = [
            'INNER JOIN '.$tb_tamp.' ON tb_tamp.machines_id = tbl_machines.id',
            'LEFT JOIN '.$tb_tamp_production.' ON tb_tamp_production.machines_id = tbl_machines.id',
            'LEFT JOIN '.$tb_tamp_thuc_te.' ON tb_tamp_thuc_te.machines_id = tbl_machines.id',
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tb_tamp_production.downtime as total_hour_production',
            'tb_tamp.totalManu as totalManu',
            'tbl_machines.preparation_time as preparation_time',
            'tbl_machines.time_change_size as time_change_size',
            'tbl_machines.used_time as used_time',
            'tbl_machines.quota_productivity as quota_productivity',
            'tb_tamp_thuc_te.total_quantity as total_quantity',
            'tb_tamp.thoi_gian_du_kien as thoi_gian_du_kien',
            'tb_tamp.thoi_gian_xu_ly as thoi_gian_xu_ly',
        ], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];

        $j = 0;
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $machines_id = $aRow['id'];
            $thoi_gian_du_kien = $aRow['thoi_gian_du_kien'];
            $thoi_gian_xu_ly = $aRow['thoi_gian_xu_ly'];
            $quota_productivity = $aRow['quota_productivity'];
//            $thoi_gian_du_kien = ($thoi_gian_du_kien - $thoi_gian_xu_ly);
            $dinh_muc_may = ($thoi_gian_du_kien - $thoi_gian_xu_ly) * $quota_productivity;
            $dinh_muc_may = floor($dinh_muc_may);
            if ($dinh_muc_may < 0){
                $dinh_muc_may = 0;
            }
            $total_hour_production = $aRow['total_hour_production'];
            $totalManu = $aRow['totalManu'];
            $preparation_time = $aRow['preparation_time'];
            $time_change_size = $aRow['time_change_size'];
            $used_time = $aRow['used_time'];
            $total_quantity = $aRow['total_quantity'];
            $used_time = ($used_time - (($preparation_time + $time_change_size) * $totalManu) - $total_hour_production) * $quota_productivity;
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="width: 150px">' . ($aRow['code_machines']) . '</div>';
            $row[] = '<div class="text-left" style="width: 150px">' . ($aRow['name_machines']) . '</div>';
            $row[] = '<div class="text-center"><a class="tnh-modal" href="'.base_url('admin/machine_productivity/view_manu/'.$machines_id.'/'.to_sql_date($start_date_search).'/'.to_sql_date($end_date_search).'').'">' . formatNumber($totalManu) . '</div>';
            $row[] = '<div class="text-center">' . formatNumber($dinh_muc_may) . '</div>';
            $row[] = '<div class="text-center">' . formatNumber($total_quantity) . '</div>';
            if (empty($dinh_muc_may)){
                $percent = 100;
            } else {
                $percent = ($total_quantity - $dinh_muc_may) / $dinh_muc_may * 100;
            }
            if ($total_quantity > $dinh_muc_may) {
                $result = '<span style="color: green">' . lang('Vượt') . '</span>';
            } elseif ($dinh_muc_may == $total_quantity) {
                $result = '<span>' . lang('Đạt') . '</span>';
            } else {
                $result = '<span style="color: red">' . lang('Không Đạt') . '</span>';
            }
            $row[] = '<div class="text-center">'.$result.' ( '.formatNumber($percent).'%'.' )</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function exportExcel(){
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            $machines_search = $this->input->post('machines_search');
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();
            $whereTamp = '';
            $whereTampPurchase = '';
            if (!empty($machines_search)) {
                $whereTamp .= ' AND tbl_production_lists_items.may_in = ' . $machines_search;
                $whereTampPurchase .= ' AND tbl_production_lists_items.may_in = ' . $machines_search;
            }
            if (!empty($start_date_search)) {
                $whereTamp .= ' AND DATE_FORMAT(tbl_production_lists_items.ngay_hoan_thanh_in, "%Y-%m-%d") >= "' . to_sql_date($start_date_search) . '"';
                $whereTampPurchase .= ' AND DATE_FORMAT(tbl_production_lists_items.ngay_hoan_thanh_in, "%Y-%m-%d") >= "' . to_sql_date($start_date_search) . '"';
            }
            if (!empty($end_date_search)) {
                $whereTamp .= ' AND DATE_FORMAT(tbl_production_lists_items.ngay_hoan_thanh_in, "%Y-%m-%d") <= "' . to_sql_date($end_date_search) . '"';
                $whereTampPurchase .= ' AND DATE_FORMAT(tbl_production_lists_items.ngay_hoan_thanh_in, "%Y-%m-%d") <= "' . to_sql_date($end_date_search) . '"';
            }

            $tb_tamp = "(
                SELECT
                    tbl_production_lists_items.may_in as machines_id,
                    SUM(
                        COALESCE(tbl_production_lists_items.thoi_gian_in, 0) +
                        COALESCE(tbl_production_lists_items.thoi_gian_xu_ly, 0)
                    ) AS thoi_gian_du_kien,
                    SUM(tbl_production_lists_items.thoi_gian_xu_ly) as thoi_gian_xu_ly,
                    count(DISTINCT tbl_productions_orders.id) as totalManu
                FROM tbl_production_lists_items
                JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_production_lists_items.po_id
                WHERE tbl_production_lists_items.may_in != 0 $whereTamp
                GROUP BY tbl_production_lists_items.may_in
            ) tb_tamp";
            $tb_tamp_production = "(
                SELECT
                    tblproduction_report.machines_id as machines_id,
                    tblproduction_report.production_stage as stage_id,
                    SUM(tblproduction_report.downtime) as downtime
                FROM tblproduction_report
                GROUP BY tblproduction_report.machines_id,tblproduction_report.production_stage
            ) tb_tamp_production";

            $tb_tamp_thuc_te = "(
                SELECT
                tbl_production_lists_items.may_in as machines_id,
                SUM(tbl_production_lists_items.tong_tua) as total_quantity
                FROM tbl_production_lists_items
                WHERE tbl_production_lists_items.may_in != '' $whereTampPurchase
                GROUP BY tbl_production_lists_items.may_in
            ) tb_tamp_thuc_te";

            $this->db->select('
                tbl_machines.id as id,
                tbl_machines.name as code_machines,
                tbl_machines.name as name_machines,
                tb_tamp_production.downtime as total_hour_production,
                tb_tamp.totalManu as totalManu,
                tbl_machines.preparation_time as preparation_time,
                tbl_machines.time_change_size as time_change_size,
                tbl_machines.used_time as used_time,
                tbl_machines.quota_productivity as quota_productivity,
                tb_tamp_thuc_te.total_quantity as total_quantity,
                tb_tamp.thoi_gian_du_kien as thoi_gian_du_kien,
                tb_tamp.thoi_gian_xu_ly as thoi_gian_xu_ly,
            ');
            $this->db->from('tbl_machines');
            $this->db->join($tb_tamp,'tb_tamp.machines_id = tbl_machines.id','inner');
            $this->db->join($tb_tamp_production,'tb_tamp_production.machines_id = tbl_machines.id','left');
            $this->db->join($tb_tamp_thuc_te,'tb_tamp_thuc_te.machines_id = tbl_machines.id','left');

            if (!empty($machines_search)) {
                $this->db->where("tbl_machines.id =  ".$machines_search."");
            }
            $this->db->order_by('tbl_machines.id', 'desc');
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
            $objPHPExcel->getActiveSheet()->setCellValue('A1',
                ('NĂNG SUẤT MÁY'))->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Mã Thiết Bị Máy Móc');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Tên Thiết Bị Máy Móc');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Tổng S Lệnh');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Định Mức Trên Máy (Tờ)');
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Định Mức Thực Tế (SL Nhập Kho)')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Kết Qu')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:G$sttRow")->applyFromArray([
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
            $rowBegin = $sttRow;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $rowBegin++;
                    $machines_id = $value['id'];
                    $thoi_gian_du_kien = $value['thoi_gian_du_kien'];
                    $thoi_gian_xu_ly = $value['thoi_gian_xu_ly'];
                    $quota_productivity = $value['quota_productivity'];
//                    $thoi_gian_du_kien = ($thoi_gian_du_kien - $thoi_gian_xu_ly);
                    $dinh_muc_may = ($thoi_gian_du_kien - $thoi_gian_xu_ly) * $quota_productivity;
                    $dinh_muc_may = floor($dinh_muc_may);
                    if ($dinh_muc_may < 0){
                        $dinh_muc_may = 0;
                    }
                    $total_hour_production = $value['total_hour_production'];
                    $totalManu = $value['totalManu'];
                    $preparation_time = $value['preparation_time'];
                    $time_change_size = $value['time_change_size'];
                    $used_time = $value['used_time'];
                    $total_quantity = $value['total_quantity'];
                    $used_time = ($used_time - (($preparation_time + $time_change_size) * $totalManu) - $total_hour_production) * $quota_productivity;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['code_machines'])->getStyle("B$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['name_machines']))->getStyle("C$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $totalManu)->getStyle("D$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($totalManu));
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($dinh_muc_may))->getStyle("E$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($dinh_muc_may));
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", ($total_quantity))->getStyle("F$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($total_quantity));

                    if ($total_quantity > $dinh_muc_may) {
                        $result = 'Vượt';
                    } elseif ($dinh_muc_may == $total_quantity) {
                        $result = 'Đạt';
                    } else {
                        $result = 'Không Đạt';
                    }
                    if (empty($dinh_muc_may)){
                        $percent = 100;
                    } else {
                        $percent = ($total_quantity - $dinh_muc_may) / $dinh_muc_may * 100;
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin",$result. ' ( '.formatNumber($percent) .' )')->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:G$rowBegin")->applyFromArray([
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
                }
            }
            $filename = lang('nang_suat_may') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
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

    public function view_manu($machines_id, $start_date_search, $end_date_search){
        $whereTamp = '';
        $whereTamp .= ' AND tbl_production_lists_items.may_in = ' . $machines_id;
        $whereTamp .= ' AND DATE_FORMAT(tbl_production_lists_items.ngay_hoan_thanh_in, "%Y-%m-%d") >= "' . ($start_date_search) . '"';
        $whereTamp .= ' AND DATE_FORMAT(tbl_production_lists_items.ngay_hoan_thanh_in, "%Y-%m-%d") <= "' . ($end_date_search) . '"';

        $tb_tamp = "(
            SELECT 
                tbl_productions_orders.reference_no as reference_no,
                tbl_productions_orders.date as date
            FROM tbl_production_lists_items
            JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_production_lists_items.po_id
            WHERE tbl_production_lists_items.may_in != 0 $whereTamp
            GROUP BY tbl_productions_orders.id
        )";

        $query = $this->db->query($tb_tamp)->result_array();
        $data['title'] = lang('Xem số lnh sản xuất');
        $data['query'] = $query;
        $this->load->view('admin/machine_productivity/view_manu', $data);
    }

}