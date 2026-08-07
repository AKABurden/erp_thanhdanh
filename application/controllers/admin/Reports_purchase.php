<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Reports_purchase extends AdminController
{
    /**
     * Codeigniter Instance
     * Expenses detailed report filters use $ci
     * @var object
     */
    private $ci;

    public function __construct()
    {
        parent::__construct();

        $this->ci = &get_instance();
        $this->load->model('reports_model');
        $this->load->model('dashboard_model');
        $this->load->model('reports_model');
        $this->load->model('dashboard_model');
        $this->load->model('orders_model');
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');

        $this->preViewSyntheticPurchaseCostMonth = true;
        $this->preViewSyntheticPlanPaySlipDepartmentMonth = true;
        $this->preViewSyntheticPlanDepartment = true;
    }

    public function syntheticPurchaseCostMonth(){
        if (!$this->preViewSyntheticPurchaseCostMonth) {
            access_denied();
        }
        $data = [];
        $title = lang('Báo Cáo Tổng Hợp Chi Mua Hàng /Tháng');
        $data['title'] = $title;
        $this->load->view('admin/reports_purchase/synthetic_purchase_cost_month', $data);
    }

    public function getSyntheticPurchaseCostMonth(){
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');


        $tbImport = "(
            SELECT 
                tblimport_items.tax_rate,
                tblimport_items.amount,
                tblimport.date_create as date_import,
                tblimport.delivery_supplier_code as delivery_supplier_code,
                CONCAT(tblimport.prefix,'-',tblimport.code) as code_import,
                tblimport_items.id_purchase_order_items as id_purchase_order_items,
                tblother_payslips_detail.total as total_pay_slip,
                CONCAT(tblother_payslips.prefix,'-',tblother_payslips.code) as code_other_payslips,
                tblother_payslips.date as date_debt_payment,
                tblpurchase_invoice.code_invoice as code_invoice,
                tblpurchase_invoice.date_misa as date_misa
            FROM tblimport
            JOIN tblimport_items ON tblimport_items.id_import = tblimport.id  
            LEFT JOIN tblother_payslips_detail ON tblother_payslips_detail.id_import = tblimport.id  
            LEFT JOIN tblother_payslips ON tblother_payslips.id = tblother_payslips_detail.other_pay 
            LEFT JOIN tblpurchase_invoice_items ON tblpurchase_invoice_items.id_import_item = tblimport_items.id 
            LEFT JOIN tblpurchase_invoice ON tblpurchase_invoice.id = tblpurchase_invoice_items.purchase_invoice_id
        ) tb_import";

        $aColumns = [
            'tblpurchases.id as id',
            'CONCAT(tblsuppliers.prefix,"-",tblsuppliers.code) as code_supplier',
            'tblsuppliers.company as name_supplier',
            'CONCAT(tblpurchases.prefix,"",tblpurchases.code) as code_purchase',
            '"" as code_item',
            '"" as name_item',
            'tblinternal_proposal.code as code_internal_proposal',
            'tblproduction_report.reference_no as code_production_report',
            'group_rl.name as check_urgent',
            'CONCAT(tblpurchase_order.prefix,"-",tblpurchase_order.code) as code_purchase_order',
            '"" as date_import',
            '"" as delivery_supplier_code',
            '"" as code_import',
            '"" as code_invoice',
            '0 as tax_rate',
            '0 as amount',
            'tblsuppliers.time_payment as time_payment',
            '"" as date_debt_payment',
            'tblsuggestion.code as code_suggestion',
            'tblplan_propose.code as code_plan_propose',
            '"" as code_other_payslips',
            '0 as total_pay_slip',
            '0 as total_old',
            '"" as date_misa',
            '"" as date_foso',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tblpurchases';

        $where = [];

        $join = [
            'INNER JOIN tblpurchases_items ON tblpurchases_items.purchases_id = tblpurchases.id',
            'LEFT JOIN tbl_internal_proposal_purchase_items ON tbl_internal_proposal_purchase_items.id_purchases_items = tblpurchases_items.id',
            'LEFT JOIN tblinternal_proposal ON tblinternal_proposal.id = tbl_internal_proposal_purchase_items.id_internal_proposal',
            'LEFT JOIN tblpurchase_order_items ON tblpurchase_order_items.purchase_items_id = tblpurchases_items.id',
            'LEFT JOIN tblpurchase_order ON tblpurchase_order.id = tblpurchase_order_items.id_purchase_order',
            'LEFT JOIN tblsuppliers ON tblsuppliers.id = tblpurchase_order.suppliers_id',
            'LEFT JOIN '.$tbImport.' ON tb_import.id_purchase_order_items = tblpurchase_order_items.id',
            'LEFT JOIN tblsuggestion ON tblsuggestion.id_internal_proposal = tblinternal_proposal.id AND tblsuggestion.purchase_order_id = tblpurchase_order.id',
            'LEFT JOIN tblplan_propose ON tblplan_propose.id_internal_proposal = tblinternal_proposal.id AND tblplan_propose.id_purchase_order_internal = tblpurchase_order.id',
            'LEFT JOIN tblproduction_report ON tblproduction_report.id_internal_proposal = tblinternal_proposal.id',
            'LEFT JOIN tbl_recommended_list group_rl ON group_rl.id = tblinternal_proposal.recommended_list_group_id',
        ];

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search);
            array_push($where, "AND tblpurchases.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search);
            array_push($where, "AND tblpurchases.date <= '" . $end_date_search . "'");
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tblpurchases_items.type',
            'tblpurchases_items.product_id',
            'tblpurchase_order_items.id as id_purchase_order_items',
            'tb_import.tax_rate',
            'tb_import.amount',
            'tb_import.date_import as date_import',
            'tb_import.delivery_supplier_code as delivery_supplier_code',
            'tb_import.code_import as code_import',
            'tb_import.total_pay_slip as total_pay_slip',
            'tb_import.code_other_payslips as code_other_payslips',
            'tb_import.date_debt_payment as date_debt_payment',
            'tb_import.code_invoice as code_invoice',
            'tb_import.date_misa as date_misa',
            'tb_import.date_debt_payment as date_foso',
        ], '', []);
        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $type_item = $aRow['type'];
            $items_id = $aRow['product_id'];
            $getItem = get_full_item_new($items_id, $type_item);

            $total_old = $aRow['amount'] - $aRow['total_pay_slip'];
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="width:100px">'.$aRow['code_supplier'].'</div>';
            $row[] = '<div class="text-left" style="width:150px">'.$aRow['name_supplier'].'</div>';
            $row[] = '<div class="text-left" style="width:100px">'.$aRow['code_purchase'].'</div>';
            $row[] = '<div class="text-left" style="width:100px">'.$getItem->code.'</div>';
            $row[] = '<div class="text-left" style="min-width:150px">'.$getItem->name.'</div>';
            $row[] = '<div class="text-left" style="width:100px">'.$aRow['code_internal_proposal'].'</div>';
            $row[] = '<div class="text-left" style="width:100px">'.$aRow['code_production_report'].'</div>';
            $string = mb_strtolower($aRow['check_urgent']);
            $search_khan = "khẩn";
            $check_urgent_khan = '';
            if(str_contains($string, $search_khan)){
                $check_urgent_khan = 'khẩn';
            }
            $check_urgent_gap = '';
            $search_gap = "gấp";
            if(str_contains($string, $search_gap)){
                $check_urgent_gap = 'gấp';
            }
            $check_urgent = '';
            if(!empty($check_urgent_khan) || !empty($check_urgent_gap)){
                $check_urgent = 'Khẩn gấp';
            }

            $row[] = '<div class="text-left">'.$check_urgent.'</div>';
            $row[] = '<div class="text-left" style="width:100px">'.$aRow['code_purchase_order'].'</div>';
            $row[] = '<div class="text-left" style="width:100px">' . (!empty($aRow['date_import']) ? _dt($aRow['date_import']) : '' )  . '</div>';
            $row[] = '<div class="text-left" style="width:100px">' . ($aRow['delivery_supplier_code']) . '</div>';
            $row[] = '<div class="text-left" style="width:100px">' . ($aRow['code_import']) . '</div>';
            $row[] = '<div class="text-left" style="width:100px">' . ($aRow['code_invoice']) . '</div>';
            $row[] = '<div class="text-center">' . ($aRow['tax_rate']) . '</div>';
            $row[] = '<div class="text-right">' . formatMoney($aRow['amount']) . '</div>';
            $row[] = '<div class="text-center">' . ($aRow['time_payment']) . '</div>';
            $row[] = '<div class="text-left" style="width:100px">' . (!empty($aRow['date_debt_payment']) ? _dhau($aRow['date_debt_payment']) : '') . '</div>';
            $row[] = '<div class="text-left" style="width:100px">' . ($aRow['code_suggestion']) . '</div>';
            $row[] = '<div class="text-left" style="width:100px">' . ($aRow['code_plan_propose']) . '</div>';
            $row[] = '<div class="text-left" style="width:100px">' . ($aRow['code_other_payslips']) . '</div>';
            $row[] = '<div class="text-right" style="width:100px">' . formatMoney($aRow['total_pay_slip']) . '</div>';
            $row[] = '<div class="text-right" style="width:100px">' . formatMoney($total_old) . '</div>';
            $row[] = '<div class="text-left" style="width:100px">' . (!empty($aRow['date_misa']) ? _dhau($aRow['date_misa']) : '') . '</div>';
            $row[] = '<div class="text-left" style="width:100px">' . (!empty($aRow['date_foso']) ? _dhau($aRow['date_foso']) : '') . '</div>';

            $output['aaData'][] = $row;
        }
        echo json_encode($output);
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
            $inputFileName = 'uploads/import_ch/bao_cao_tong_hop_chi_mua_hang_thang.xlsx';
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
            $row = 2;
            $staff_id = get_staff_user_id();
            $tbImport = "(
                SELECT 
                    tblimport_items.tax_rate,
                    tblimport_items.amount,
                    tblimport.date_create as date_import,
                    tblimport.delivery_supplier_code as delivery_supplier_code,
                    CONCAT(tblimport.prefix,'-',tblimport.code) as code_import,
                    tblimport_items.id_purchase_order_items as id_purchase_order_items,
                    tblother_payslips_detail.total as total_pay_slip,
                    CONCAT(tblother_payslips.prefix,'-',tblother_payslips.code) as code_other_payslips,
                    tblother_payslips.date as date_debt_payment,
                    tblpurchase_invoice.code_invoice as code_invoice,
                    tblpurchase_invoice.date_misa as date_misa
                FROM tblimport
                JOIN tblimport_items ON tblimport_items.id_import = tblimport.id  
                LEFT JOIN tblother_payslips_detail ON tblother_payslips_detail.id_import = tblimport.id  
                LEFT JOIN tblother_payslips ON tblother_payslips.id = tblother_payslips_detail.other_pay 
                LEFT JOIN tblpurchase_invoice_items ON tblpurchase_invoice_items.id_import_item = tblimport_items.id 
                LEFT JOIN tblpurchase_invoice ON tblpurchase_invoice.id = tblpurchase_invoice_items.purchase_invoice_id
            ) tb_import";
            $this->db->select(
               'tblpurchases.id as id,
                CONCAT(tblsuppliers.prefix,"-",tblsuppliers.code) as code_supplier,
                tblsuppliers.company as name_supplier,
                CONCAT(tblpurchases.prefix,"",tblpurchases.code) as code_purchase,
                "" as code_item,
                "" as name_item,
                tblinternal_proposal.code as code_internal_proposal,
                tblproduction_report.reference_no as code_production_report,
                group_rl.name as check_urgent,
                CONCAT(tblpurchase_order.prefix,"-",tblpurchase_order.code) as code_purchase_order,
                "" as date_import,
                "" as delivery_supplier_code,
                "" as code_import,
                "" as code_invoice,
                0 as tax_rate,
                0 as amount,
                tblsuppliers.time_payment as time_payment,
                "" as date_debt_payment,
                tblsuggestion.code as code_suggestion,
                tblplan_propose.code as code_plan_propose,
                "" as code_other_payslips,
                0 as total_pay_slip,
                0 as total_old,
                tblpurchases_items.type,
                tblpurchases_items.product_id,
                tblpurchase_order_items.id as id_purchase_order_items,
                tb_import.tax_rate,
                tb_import.amount,
                tb_import.date_import as date_import,
                tb_import.delivery_supplier_code as delivery_supplier_code,
                tb_import.code_import as code_import,
                tb_import.total_pay_slip as total_pay_slip,
                tb_import.code_other_payslips as code_other_payslips,
                tb_import.date_debt_payment as date_debt_payment,
                tb_import.code_invoice as code_invoice,
                tb_import.date_misa as date_misa,
                tb_import.date_debt_payment as date_foso,
            ');
            $this->db->from('tblpurchases');
            $this->db->join('tblpurchases_items', 'tblpurchases_items.purchases_id = tblpurchases.id', 'inner');
            $this->db->join('tbl_internal_proposal_purchase_items', 'tbl_internal_proposal_purchase_items.id_purchases_items = tblpurchases_items.id', 'left');
            $this->db->join('tblinternal_proposal', 'tblinternal_proposal.id = tbl_internal_proposal_purchase_items.id_internal_proposal', 'left');
            $this->db->join('tblpurchase_order_items', 'tblpurchase_order_items.purchase_items_id = tblpurchases_items.id', 'left');
            $this->db->join('tblpurchase_order', 'tblpurchase_order.id = tblpurchase_order_items.id_purchase_order', 'left');
            $this->db->join('tblsuppliers', 'tblsuppliers.id = tblpurchase_order.suppliers_id', 'left');
            $this->db->join($tbImport, 'tb_import.id_purchase_order_items = tblpurchase_order_items.id', 'left');
            $this->db->join('tblsuggestion', 'tblsuggestion.id_internal_proposal = tblinternal_proposal.id AND tblsuggestion.purchase_order_id = tblpurchase_order.id', 'left');
            $this->db->join('tblplan_propose', 'tblplan_propose.id_internal_proposal = tblinternal_proposal.id AND tblplan_propose.id_purchase_order_internal = tblpurchase_order.id', 'left');
            $this->db->join('tblproduction_report', 'tblproduction_report.id_internal_proposal = tblinternal_proposal.id', 'left');
            $this->db->join('tbl_recommended_list group_rl', 'group_rl.id = tblinternal_proposal.recommended_list_group_id', 'left');
            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search);
                $this->db->where("tblpurchases.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search);
                $this->db->where("tblpurchases.date <= '" . $end_date_search . "'");
            }
            $this->db->order_by('CONCAT(tblsuppliers.prefix,"-",tblsuppliers.code) desc');
            $items = $this->db->get()->result_array();

            $dem = 0;

            foreach ($items as $key => $value) {
                $row++;
                $dem++;
                $type_item = $value['type'];
                $items_id = $value['product_id'];
                $getItem = get_full_item_new($items_id, $type_item);

                $total_old = $value['amount'] - $value['total_pay_slip'];
                $string = mb_strtolower($value['check_urgent']);
                $search_khan = "khẩn";
                $check_urgent_khan = '';
                if(str_contains($string, $search_khan)){
                    $check_urgent_khan = 'khẩn';
                }
                $check_urgent_gap = '';
                $search_gap = "gấp";
                if(str_contains($string, $search_gap)){
                    $check_urgent_gap = 'gấp';
                }
                $check_urgent = '';
                if(!empty($check_urgent_khan) || !empty($check_urgent_gap)){
                    $check_urgent = 'Khẩn gấp';
                }
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[0] . $row, $dem);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, $value['code_supplier'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, ($value['name_supplier']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row, ($value['code_purchase']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[4] . $row, $getItem->code, PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[5] . $row, $getItem->name, PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[6] . $row, $value['code_internal_proposal']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[7] . $row, $value['code_production_report']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[8] . $row,$check_urgent);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[9] . $row, ($value['code_purchase_order']), PHPExcel_Cell_DataType::TYPE_STRING);

                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[10] . $row, (!empty($value['date_import']) ? _dt($value['date_import']) : '' ), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[11] . $row, ($value['delivery_supplier_code']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[12] . $row, ($value['code_import']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[13] . $row, ($value['code_invoice']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[14] . $row, ($value['tax_rate']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[15] . $row, ($value['amount']))->getStyle("$columsExcel[21]$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['total_pay_slip']));
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[16] . $row, ($value['time_payment']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[17] . $row, (!empty($value['date_debt_payment']) ? _dhau($value['date_debt_payment']) : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[18] . $row, ($value['code_suggestion']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[19] . $row, ($value['code_plan_propose']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[20] . $row, ($value['code_other_payslips']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[21] . $row, ($value['total_pay_slip']))->getStyle("$columsExcel[21]$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['total_pay_slip']));
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[22] . $row, ($total_old))->getStyle("$columsExcel[22]$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($total_old));
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[23] . $row, (!empty($value['date_misa']) ? _dhau($value['date_misa']) : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[24] . $row, (!empty($value['date_foso']) ? _dhau($value['date_foso']) : ''), PHPExcel_Cell_DataType::TYPE_STRING);

            }
            $objPHPExcel->getActiveSheet()->getStyle('A4:Y' . $row)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('A4:Y' . $row)->applyFromArray([
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

            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $filename = lang('bao_cao_tong_hop_chi_mua_hang_thang') . '.xls';
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

    public function synthetic_plan_payslip_department_month(){
        if (!$this->preViewSyntheticPlanPaySlipDepartmentMonth) {
            access_denied();
        }
        $data = [];
        $title = lang('Báo Cáo Tổng Hợp Kế Hoạch Chi Phòng Ban');
        $data['title'] = $title;
        $this->load->view('admin/reports_purchase/synthetic_plan_payslip_department_month', $data);
    }

    public function getSyntheticPlanPayslipDepartmentMonth(){
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');


        $tb_other_pay = "(
             SELECT 
                tblother_payslip_cost_detail.object_id,
                SUM(tblother_payslip_cost_detail.total) as total_pay_slip
            FROM tblother_payslip_cost_detail
            WHERE tblother_payslip_cost_detail.table_object = 'tbl_suggest_payslips_items'
            GROUP BY tblother_payslip_cost_detail.object_id
        ) tb_other_pay";

        $tb_suggestion= "(
            SELECT 
                tblpurchase_invoice.tax_rate,
                tblpurchase_invoice.code_invoice as code_invoice,
                tblpurchase_invoice.date_misa as date_misa,
                tblsuggestion.id_internal_proposal as id_internal_proposal
            FROM tblsuggestion
            LEFT JOIN tblpurchase_invoice ON tblpurchase_invoice.id = tblsuggestion.red_invoice
            GROUP BY tblsuggestion.id_internal_proposal
        ) tb_suggestion";

        $tbDepartment = "(
            SELECT
                tblstaff_departments.staffid as staffid,
                GROUP_CONCAT(tbl_room.name) as name_department
            FROM tbldepartments
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            JOIN tbl_room ON tbl_room.id = tbldepartments.room_id
            GROUP BY tblstaff_departments.staffid
        ) tb_department";

        $aColumns = [
            'tbl_suggest_payslips.id as id',
            'tblbranch.name as name_branch',
            'tb_department.name_department as name_department',
            'tbl_suggest_payslips.reference_no as reference_no',
            'tbl_type_cost.name as group_pay_slip',
            'tblinternal_proposal.code as code_internal_proposal',
            'tblinternal_proposal.date as date_internal_proposal',
            '"" as date_finish_payslip',
            'tblsuppliers.time_payment as time_payment',
            'tbl_suggest_payslips_items.total as total',
            'tb_suggestion.tax_rate as tax_rate',
            '0 as total_pay_slip',
            'tb_suggestion.code_invoice as code_invoice',
            'tblplan_propose.code as code_plan_propose',
            '"" as code_other_payslips',
            'tb_other_pay.total_pay_slip as total_pay_slip',
            '0 as total_old',
            'tblproduction_report.reference_no as code_production_report',
            'tb_suggestion.date_misa as date_misa',
            '"" as date_foso',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_payslips';

        $where = [
            'AND tbl_suggest_payslips_items.cost_id != 0'
        ];

        $join = [
            'INNER JOIN tbl_suggest_payslips_items ON tbl_suggest_payslips_items.suggest_payslips_id = tbl_suggest_payslips.id',
            'INNER JOIN tblstaff ON tblstaff.staffid = tbl_suggest_payslips.staff_id',
            'LEFT JOIN tblsuppliers ON tblsuppliers.id = tbl_suggest_payslips.suppliers_id',
            'LEFT JOIN tblbranch ON tblbranch.id = tbl_suggest_payslips.branch_id',
            'LEFT JOIN tbl_suggest_muti_id ON tbl_suggest_muti_id.suggest_id = tbl_suggest_payslips.id',
            'LEFT JOIN tblinternal_proposal ON tblinternal_proposal.id = tbl_suggest_muti_id.id_internal_proposal',
            'LEFT JOIN '.$tb_suggestion.' ON tb_suggestion.id_internal_proposal = tblinternal_proposal.id ',
            'LEFT JOIN tblplan_propose ON tblplan_propose.id_internal_proposal = tblinternal_proposal.id',
            'LEFT JOIN tblproduction_report ON tblproduction_report.id_internal_proposal = tblinternal_proposal.id',
            'LEFT JOIN '.$tb_other_pay.' ON tb_other_pay.object_id = tbl_suggest_payslips_items.id',
            'LEFT JOIN tblcosts ON tblcosts.id = tbl_suggest_payslips_items.cost_id',
            'LEFT JOIN tbl_type_cost ON tbl_type_cost.id = tblcosts.type_cost',
            'LEFT JOIN '.$tbDepartment.' ON tb_department.staffid = tblstaff.staffid',
        ];

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search). ' 00:00:00';
            array_push($where, "AND tbl_suggest_payslips.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search).' 23:59:59';
            array_push($where, "AND tbl_suggest_payslips.date <= '" . $end_date_search . "'");
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_suggest_payslips_items.id as suggest_payslips_items_id'
        ], '', []);
        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {

            $this->db->select('
                CONCAT(tblother_payslips.prefix,"-",tblother_payslips.code) as code_other_payslip,
                tblother_payslip_cost_detail.total as total_pay_slip,
                tblother_payslips.date as date_pay_slip
            ');
            $this->db->from('tblother_payslips');
            $this->db->join('tblother_payslip_cost_detail','tblother_payslip_cost_detail.other_payslip_id = tblother_payslips.id');
            $this->db->where('tblother_payslip_cost_detail.object_id',$aRow['suggest_payslips_items_id']);
            $this->db->where('tblother_payslip_cost_detail.table_object','tbl_suggest_payslips_items');
            $dtOtherPay = $this->db->get()->result_array();
            $date_finish_payslip = '';
            $code_other_payslip = '';
            $date_pay_slip = '';
            if (!empty($dtOtherPay)){
                $date_finish_payslip = $dtOtherPay[count($dtOtherPay) - 1]['date_pay_slip'];
                foreach ($dtOtherPay as $k => $v){
                    $code_other_payslip .= '<div>'.$v['code_other_payslip'].'</div>';
                    $date_pay_slip .= !empty($v['date_pay_slip']) ? '<div>'._dhau($v['date_pay_slip']).'</div>' : '';
                }
            }
            $total_old = $aRow['total'] - $aRow['total_pay_slip'];
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="width:100px">'.$aRow['name_branch'].'</div>';
            $row[] = '<div class="text-left" style="width:150px">'.$aRow['name_department'].'</div>';
            $row[] = '<div class="text-left" style="width:100px">'.$aRow['reference_no'].'</div>';
            $row[] = '<div class="text-left" style="width:100px">'.$aRow['group_pay_slip'].'</div>';
            $row[] = '<div class="text-left" style="min-width:150px">'.$aRow['code_internal_proposal'].'</div>';
            $row[] = '<div class="text-left" style="width:100px">'.(!empty($aRow['date_internal_proposal']) ? _dt($aRow['date_internal_proposal']) : '').'</div>';
            $row[] = '<div class="text-left" style="width:100px">' . (!empty($date_finish_payslip) ? _dhau($date_finish_payslip) : '' )  . '</div>';
            $row[] = '<div class="text-center" style="width:100px">' . ($aRow['time_payment']) . '</div>';
            $row[] = '<div class="text-right" style="width:100px">' . formatMoney($aRow['total']) . '</div>';
            $row[] = '<div class="text-center">' . ($aRow['tax_rate']) . '</div>';
            $row[] = '<div class="text-right">' . formatMoney($aRow['total']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['code_invoice']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['code_plan_propose']) . '</div>';
            $row[] = '<div class="text-left">' . $code_other_payslip . '</div>';
            $row[] = '<div class="text-right" style="width:100px">' . formatMoney($aRow['total_pay_slip']) . '</div>';
            $row[] = '<div class="text-right" style="width:100px">' . formatMoney($total_old) . '</div>';
            $row[] = '<div class="text-left" style="width:100px">' . $aRow['code_production_report'] . '</div>';
            $row[] = '<div class="text-left" style="width:100px">' . (!empty($aRow['date_misa']) ? _dhau($aRow['date_misa']) : '') . '</div>';
            $row[] = '<div class="text-left" style="width:100px">' . $date_pay_slip . '</div>';

            $output['aaData'][] = $row;
            $dtOtherPay = [];
            if (!empty($dtOtherPay)){
                foreach ($dtOtherPay as $k => $v){
                    $row = array();
                    $row[] = '<div class="text-center"></div>';
                    $row[] = '<div class="text-left" style="width:100px"></div>';
                    $row[] = '<div class="text-left" style="width:150px"></div>';
                    $row[] = '<div class="text-left" style="width:100px"></div>';
                    $row[] = '<div class="text-left" style="width:100px"></div>';
                    $row[] = '<div class="text-left" style="min-width:150px"></div>';
                    $row[] = '<div class="text-left" style="width:100px"></div>';
                    $row[] = '<div class="text-left" style="width:100px"></div>';
                    $row[] = '<div class="text-center" style="width:100px"></div>';
                    $row[] = '<div class="text-right" style="width:100px"></div>';
                    $row[] = '<div class="text-center"></div>';
                    $row[] = '<div class="text-right"></div>';
                    $row[] = '<div class="text-left"></div>';
                    $row[] = '<div class="text-left"></div>';
                    $row[] = '<div class="text-left">' . ($v['code_other_payslip']) . '</div>';
                    $row[] = '<div class="text-right" style="width:100px">' . formatMoney($v['total_pay_slip']) . '</div>';
                    $row[] = '<div class="text-right" style="width:100px"></div>';
                    $row[] = '<div class="text-left" style="width:100px"></div>';
                    $row[] = '<div class="text-left" style="width:100px">' . (!empty($aRow['date_misa']) ? _dhau($aRow['date_misa']) : '') . '</div>';
                    $row[] = '<div class="text-left" style="width:100px">' . (!empty($v['date_pay_slip']) ? _dhau($v['date_pay_slip']) : '') . '</div>';

                    $output['aaData'][] = $row;
                }
            }

        }
        echo json_encode($output);
    }

    public function exportExcelSyntheticPlanPayslipDepartmentMonth()
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
            $inputFileName = 'uploads/import_ch/bao_cao_tong_hop_ke_hoach_chi_phong_ban.xlsx';
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
            $row = 2;
            $staff_id = get_staff_user_id();
            $tb_other_pay = "(
             SELECT 
                tblother_payslip_cost_detail.object_id,
                SUM(tblother_payslip_cost_detail.total) as total_pay_slip
            FROM tblother_payslip_cost_detail
            WHERE tblother_payslip_cost_detail.table_object = 'tbl_suggest_payslips_items'
            GROUP BY tblother_payslip_cost_detail.object_id
        ) tb_other_pay";

            $tb_suggestion= "(
            SELECT 
                tblpurchase_invoice.tax_rate,
                tblpurchase_invoice.code_invoice as code_invoice,
                tblpurchase_invoice.date_misa as date_misa,
                tblsuggestion.id_internal_proposal as id_internal_proposal
            FROM tblsuggestion
            LEFT JOIN tblpurchase_invoice ON tblpurchase_invoice.id = tblsuggestion.red_invoice
            GROUP BY tblsuggestion.id_internal_proposal
        ) tb_suggestion";

            $tbDepartment = "(
            SELECT
                tblstaff_departments.staffid as staffid,
                GROUP_CONCAT(tbl_room.name) as name_department
            FROM tbldepartments
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            JOIN tbl_room ON tbl_room.id = tbldepartments.room_id
            GROUP BY tblstaff_departments.staffid
        ) tb_department";
            $this->db->select(
                'tbl_suggest_payslips.id as id,
                tblbranch.name as name_branch,
                tb_department.name_department as name_department,
                tbl_suggest_payslips.reference_no as reference_no,
                tbl_type_cost.name as group_pay_slip,
                tblinternal_proposal.code as code_internal_proposal,
                tblinternal_proposal.date as date_internal_proposal,
                "" as date_finish_payslip,
                tblsuppliers.time_payment as time_payment,
                tbl_suggest_payslips_items.total as total,
                tb_suggestion.tax_rate as tax_rate,
                0 as total_pay_slip,
                tb_suggestion.code_invoice as code_invoice,
                tblplan_propose.code as code_plan_propose,
                "" as code_other_payslips,
                tb_other_pay.total_pay_slip as total_pay_slip,
                0 as total_old,
                tblproduction_report.reference_no as code_production_report,
                tb_suggestion.date_misa as date_misa,
                "" as date_foso,
                tbl_suggest_payslips_items.id as suggest_payslips_items_id
            ');
            $this->db->from('tbl_suggest_payslips');
            $this->db->join('tbl_suggest_payslips_items', 'tbl_suggest_payslips_items.suggest_payslips_id = tbl_suggest_payslips.id', 'inner');
            $this->db->join('tblstaff', 'tblstaff.staffid = tbl_suggest_payslips.staff_id', 'inner');
            $this->db->join('tblsuppliers', 'tblsuppliers.id = tbl_suggest_payslips.suppliers_id', 'left');
            $this->db->join('tblbranch', 'tblbranch.id = tbl_suggest_payslips.branch_id', 'left');
            $this->db->join('tbl_suggest_muti_id', 'tbl_suggest_muti_id.suggest_id = tbl_suggest_payslips.id', 'left');
            $this->db->join('tblinternal_proposal', 'tblinternal_proposal.id = tbl_suggest_muti_id.id_internal_proposal', 'left');
            $this->db->join($tb_suggestion, 'tb_suggestion.id_internal_proposal = tblinternal_proposal.id', 'left');
            $this->db->join('tblplan_propose', 'tblplan_propose.id_internal_proposal = tblinternal_proposal.id', 'left');
            $this->db->join('tblproduction_report', 'tblproduction_report.id_internal_proposal = tblinternal_proposal.id', 'left');
            $this->db->join($tb_other_pay, 'tb_other_pay.object_id = tbl_suggest_payslips_items.id', 'left');
            $this->db->join('tblcosts', 'tblcosts.id = tbl_suggest_payslips_items.cost_id', 'left');
            $this->db->join('tbl_type_cost', 'tbl_type_cost.id = tblcosts.type_cost', 'left');
            $this->db->join($tbDepartment, 'tb_department.staffid = tblstaff.staffid', 'left');

            $this->db->where('tbl_suggest_payslips_items.cost_id != 0');
            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search). ' 00:00:00';
                $this->db->where("tbl_suggest_payslips.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search).' 23:59:59';
                $this->db->where("tbl_suggest_payslips.date <= '" . $end_date_search . "'");
            }
            $this->db->order_by('tbl_suggest_payslips.id desc');
            $items = $this->db->get()->result_array();

            $dem = 0;

            foreach ($items as $key => $value) {
                $row++;
                $dem++;
                $total_old = $value['total'] - $value['total_pay_slip'];
                $this->db->select('
                    CONCAT(tblother_payslips.prefix,"-",tblother_payslips.code) as code_other_payslip,
                    tblother_payslip_cost_detail.total as total_pay_slip,
                    tblother_payslips.date as date_pay_slip
                ');
                $this->db->from('tblother_payslips');
                $this->db->join('tblother_payslip_cost_detail','tblother_payslip_cost_detail.other_payslip_id = tblother_payslips.id');
                $this->db->where('tblother_payslip_cost_detail.object_id',$value['suggest_payslips_items_id']);
                $this->db->where('tblother_payslip_cost_detail.table_object','tbl_suggest_payslips_items');
                $dtOtherPay = $this->db->get()->result_array();
                $date_finish_payslip = '';
                $code_other_payslip = '';
                $date_pay_slip = '';
                if (!empty($dtOtherPay)){
                    $date_finish_payslip = $dtOtherPay[count($dtOtherPay) - 1]['date_pay_slip'];
                    foreach ($dtOtherPay as $k => $v){
                        $code_other_payslip .= $v['code_other_payslip']."\n";
                        $date_pay_slip .= !empty($v['date_pay_slip']) ? _dhau($v['date_pay_slip']) : ''."\n";
                    }
                }
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[0] . $row, $dem);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, $value['name_branch'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, ($value['name_department']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row, ($value['reference_no']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[4] . $row, $value['group_pay_slip'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[5] . $row, $value['code_internal_proposal'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[6] . $row, (!empty($aRow['date_internal_proposal']) ? $aRow['date_internal_proposal'] : ''));
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[7] . $row, (!empty($date_finish_payslip) ? _dhau($date_finish_payslip) : '' ));
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[8] . $row,$value['time_payment']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[9] . $row, ($value['total']))->getStyle("$columsExcel[9]$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['total']));
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[10] . $row, (!empty($value['tax_rate']) ? ($value['tax_rate']) : 0), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[11] . $row, ($value['total']))->getStyle("$columsExcel[11]$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['total']));
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[12] . $row, ($value['code_invoice']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[13] . $row, ($value['code_plan_propose']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[14] . $row, $code_other_payslip, PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[15] . $row, ($value['total_pay_slip']))->getStyle("$columsExcel[15]$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['total_pay_slip']));
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[16] . $row, ($total_old))->getStyle("$columsExcel[16]$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($total_old));
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[17] . $row, $value['code_production_report'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[18] . $row, (!empty($value['date_misa']) ? _dhau($value['date_misa']) : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[19] . $row, $date_pay_slip, PHPExcel_Cell_DataType::TYPE_STRING);
                $dtOtherPay = [];
                if (!empty($dtOtherPay)) {
                    foreach ($dtOtherPay as $k => $v) {
                        $row++;
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[0] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row,'', PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[4] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[5] . $row,'' , PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[6] . $row,'');
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[7] . $row,'');
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[8] . $row,'');
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[9] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[10] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[11] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[12] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[13] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[14] . $row, ($v['code_other_payslip']), PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[15] . $row, ($v['total_pay_slip']))->getStyle("$columsExcel[15]$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($v['total_pay_slip']));
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[16] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[17] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[18] . $row, (!empty($value['date_misa']) ? _dhau($value['date_misa']) : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[19] . $row, (!empty($v['date_pay_slip']) ? _dhau($v['date_pay_slip']) : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                    }
                }
            }
            $objPHPExcel->getActiveSheet()->getStyle('A3:T' . $row)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('A3:T' . $row)->applyFromArray([
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

            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $filename = lang('bao_cao_tong_hop_ke_hoach_chi_phong_ban') . '.xls';
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

    public function synthetic_plan_department(){
        if (!$this->preViewSyntheticPlanDepartment) {
            access_denied();
        }
        $data = [];
        $title = lang('Báo Cáo Tổng Hợp Kế Hoạch Phòng Ban');
        $data['title'] = $title;
        $this->load->view('admin/reports_purchase/synthetic_plan_department', $data);
    }

    public function getSyntheticPlanDepartment(){
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');


        $tb_other_pay = "(
             SELECT 
                tblother_payslips_detail.id_service as object_id,
                SUM(tblother_payslips_detail.total) as total_pay_slip
            FROM tblother_payslips_detail
            GROUP BY tblother_payslips_detail.id_service
        ) tb_other_pay";

        $tb_suggestion= "(
            SELECT 
                tblpurchase_invoice.tax_rate,
                tblpurchase_invoice.code_invoice as code_invoice,
                tblpurchase_invoice.date_misa as date_misa,
                tblsuggestion.id as suggestion_id
            FROM tblsuggestion
            LEFT JOIN tblpurchase_invoice ON tblpurchase_invoice.id = tblsuggestion.red_invoice
        ) tb_suggestion";

        $aColumns = [
            'tbl_suggest_plan_deparment.id as id',
            'tblbranch.name as name_branch',
            'tbldepartments.name as name_department',
            'tbl_suggest_plan_deparment.reference_no as reference_no',
            'tblinternal_proposal.code as code_internal_proposal',
            '"" as time_payment',
            '"" as date_payslip',
            'tblinternal_proposal.money as total',
            'tb_suggestion.tax_rate as tax_rate',
            'tblinternal_proposal.money as total',
            'tb_suggestion.code_invoice as code_invoice',
            'tblplan_propose.code as code_plan_propose',
            '"" as code_other_payslips',
            'tb_other_pay.total_pay_slip as total_pay_slip',
            '0 as total_old',
            'tblproduction_report.reference_no as code_production_report',
            'tb_suggestion.date_misa as date_misa',
            '"" as date_foso',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_plan_deparment';

        $where = [
        ];

        $join = [
            'INNER JOIN tblstaff ON tblstaff.staffid = tbl_suggest_plan_deparment.staff_id',
            'LEFT JOIN tblbranch ON tblbranch.id = tbl_suggest_plan_deparment.branch_id',
            'LEFT JOIN tblinternal_proposal ON tblinternal_proposal.suggest_id = tbl_suggest_plan_deparment.id AND tblinternal_proposal.category_recommended_id = 66',
            'LEFT JOIN '.$tb_suggestion.' ON tb_suggestion.suggestion_id = tblinternal_proposal.id_suggestion ',
            'LEFT JOIN tblplan_propose ON tblplan_propose.id_internal_proposal = tblinternal_proposal.id',
            'LEFT JOIN tblproduction_report ON tblproduction_report.id_internal_proposal = tblinternal_proposal.id',
            'LEFT JOIN '.$tb_other_pay.' ON tb_other_pay.object_id = tb_suggestion.suggestion_id',
            'LEFT JOIN tbldepartments ON tbldepartments.departmentid = tbl_suggest_plan_deparment.deparment_id',
        ];

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search). ' 00:00:00';
            array_push($where, "AND tbl_suggest_plan_deparment.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search).' 23:59:59';
            array_push($where, "AND tbl_suggest_plan_deparment.date <= '" . $end_date_search . "'");
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tb_suggestion.suggestion_id as suggestion_id'
        ], '', []);
        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $dtOtherPay = [];
            if (!empty($aRow['suggestion_id'])) {
                $this->db->select('
                CONCAT(tblother_payslips.prefix,"-",tblother_payslips.code) as code_other_payslip,
                tblother_payslips_detail.total as total_pay_slip,
                tblother_payslips.date as date_pay_slip
            ');
                $this->db->from('tblother_payslips');
                $this->db->join('tblother_payslips_detail',
                    'tblother_payslips_detail.other_pay = tblother_payslips.id');
                $this->db->where('tblother_payslips_detail.id_service', $aRow['suggestion_id']);
                $dtOtherPay = $this->db->get()->result_array();
            }
            $total_old = $aRow['total'] - $aRow['total_pay_slip'];
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="width:100px">'.$aRow['name_branch'].'</div>';
            $row[] = '<div class="text-left" style="width:150px">'.$aRow['name_department'].'</div>';
            $row[] = '<div class="text-left" style="width:100px">'.$aRow['reference_no'].'</div>';
            $row[] = '<div class="text-left" style="width:100px">'.$aRow['code_internal_proposal'].'</div>';
            $row[] = '<div class="text-center" style="width:100px">' . ($aRow['time_payment']) . '</div>';
            $row[] = '<div class="text-center" style="width:100px"></div>';
            $row[] = '<div class="text-right" style="width:100px">' . formatMoney($aRow['total']) . '</div>';
            $row[] = '<div class="text-center">' . ($aRow['tax_rate']) . '</div>';
            $row[] = '<div class="text-right">' . formatMoney($aRow['total']) . '</div>';
            $row[] = '<div class="text-left" style="width:100px">' . ($aRow['code_invoice']) . '</div>';
            $row[] = '<div class="text-left" style="width:100px">' . ($aRow['code_plan_propose']) . '</div>';
            $row[] = '<div class="text-left"></div>';
            $row[] = '<div class="text-right" style="width:100px">' . formatMoney($aRow['total_pay_slip']) . '</div>';
            $row[] = '<div class="text-right" style="width:100px">' . formatMoney($total_old) . '</div>';
            $row[] = '<div class="text-left" style="width:100px">' . $aRow['code_production_report'] . '</div>';
            $row[] = '<div class="text-left" style="width:100px">' . (!empty($aRow['date_misa']) ? _dhau($aRow['date_misa']) : '') . '</div>';
            $row[] = '<div class="text-left" style="width:100px">' . (!empty($aRow['date_foso']) ? _dhau($aRow['date_foso']) : '') . '</div>';

            $output['aaData'][] = $row;
            if (!empty($dtOtherPay)){
                foreach ($dtOtherPay as $k => $v){
                    $row = array();
                    $row[] = '<div class="text-center"></div>';
                    $row[] = '<div class="text-left" style="width:100px"></div>';
                    $row[] = '<div class="text-left" style="width:150px"></div>';
                    $row[] = '<div class="text-left" style="width:100px"></div>';
                    $row[] = '<div class="text-left" style="width:100px"></div>';
                    $row[] = '<div class="text-left" style="min-width:150px"></div>';
                    $row[] = '<div class="text-left" style="width:100px">'._dhau($v['date_pay_slip']).'</div>';
                    $row[] = '<div class="text-center" style="width:100px"></div>';
                    $row[] = '<div class="text-right" style="width:100px"></div>';
                    $row[] = '<div class="text-center"></div>';
                    $row[] = '<div class="text-right"></div>';
                    $row[] = '<div class="text-left"></div>';
                    $row[] = '<div class="text-left" style="min-width:100px">' . ($v['code_other_payslip']) . '</div>';
                    $row[] = '<div class="text-right" style="width:100px">' . formatMoney($v['total_pay_slip']) . '</div>';
                    $row[] = '<div class="text-right" style="width:100px"></div>';
                    $row[] = '<div class="text-left" style="width:100px"></div>';
                    $row[] = '<div class="text-left" style="width:100px">' . (!empty($aRow['date_misa']) ? _dhau($aRow['date_misa']) : '') . '</div>';
                    $row[] = '<div class="text-left" style="width:100px">' . (!empty($v['date_pay_slip']) ? _dhau($v['date_pay_slip']) : '') . '</div>';

                    $output['aaData'][] = $row;
                }
            }

        }
        echo json_encode($output);
    }
    public function exportExcelSyntheticPlanDepartment(){
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
            $inputFileName = 'uploads/import_ch/bao_cao_tong_hop_ke_hoach_phong_ban.xlsx';
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
            $row = 2;
            $staff_id = get_staff_user_id();
            $tb_other_pay = "(
                 SELECT 
                    tblother_payslips_detail.id_service as object_id,
                    SUM(tblother_payslips_detail.total) as total_pay_slip
                FROM tblother_payslips_detail
                GROUP BY tblother_payslips_detail.id_service
            ) tb_other_pay";

            $tb_suggestion= "(
                SELECT 
                    tblpurchase_invoice.tax_rate,
                    tblpurchase_invoice.code_invoice as code_invoice,
                    tblpurchase_invoice.date_misa as date_misa,
                    tblsuggestion.id as suggestion_id
                FROM tblsuggestion
                LEFT JOIN tblpurchase_invoice ON tblpurchase_invoice.id = tblsuggestion.red_invoice
            ) tb_suggestion";
            $this->db->select(
                'tbl_suggest_plan_deparment.id as id,
                tblbranch.name as name_branch,
                tbldepartments.name as name_department,
                tbl_suggest_plan_deparment.reference_no as reference_no,
                tblinternal_proposal.code as code_internal_proposal,
                "" as time_payment,
                "" as date_payslip,
                tblinternal_proposal.money as total,
                tb_suggestion.tax_rate as tax_rate,
                tblinternal_proposal.money as total,
                tb_suggestion.code_invoice as code_invoice,
                tblplan_propose.code as code_plan_propose,
                "" as code_other_payslips,
                tb_other_pay.total_pay_slip as total_pay_slip,
                0 as total_old,
                tblproduction_report.reference_no as code_production_report,
                tb_suggestion.date_misa as date_misa,
                "" as date_foso,
                tb_suggestion.suggestion_id as suggestion_id,
            ');
            $this->db->from('tbl_suggest_plan_deparment');
            $this->db->join('tblstaff', 'tblstaff.staffid = tbl_suggest_plan_deparment.staff_id', 'inner');
            $this->db->join('tblbranch', 'tblbranch.id = tbl_suggest_plan_deparment.branch_id', 'inner');
            $this->db->join('tblinternal_proposal', 'tblinternal_proposal.suggest_id = tbl_suggest_plan_deparment.id AND tblinternal_proposal.category_recommended_id = 66', 'left');
            $this->db->join($tb_suggestion, 'tb_suggestion.suggestion_id = tblinternal_proposal.id_suggestion', 'left');
            $this->db->join('tblplan_propose', 'tblplan_propose.id_internal_proposal = tblinternal_proposal.id', 'left');
            $this->db->join('tblproduction_report', 'tblproduction_report.id_internal_proposal = tblinternal_proposal.id', 'left');
            $this->db->join($tb_other_pay, 'tb_other_pay.object_id = tb_suggestion.suggestion_id', 'left');
            $this->db->join('tbldepartments', 'tbldepartments.departmentid = tbl_suggest_plan_deparment.deparment_id', 'left');

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search). ' 00:00:00';
                $this->db->where("tbl_suggest_plan_deparment.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search).' 23:59:59';
                $this->db->where("tbl_suggest_plan_deparment.date <= '" . $end_date_search . "'");
            }
            $this->db->order_by('tbl_suggest_plan_deparment.id desc');
            $items = $this->db->get()->result_array();
            $dem = 0;

            foreach ($items as $key => $value) {
                $row++;
                $dem++;
                $total_old = $value['total'] - $value['total_pay_slip'];
                $dtOtherPay = [];
                if (!empty($value['suggestion_id'])) {
                    $this->db->select('
                    CONCAT(tblother_payslips.prefix,"-",tblother_payslips.code) as code_other_payslip,
                    tblother_payslips_detail.total as total_pay_slip,
                    tblother_payslips.date as date_pay_slip
                ');
                    $this->db->from('tblother_payslips');
                    $this->db->join('tblother_payslips_detail',
                        'tblother_payslips_detail.other_pay = tblother_payslips.id');
                    $this->db->where('tblother_payslips_detail.id_service', $value['suggestion_id']);
                    $dtOtherPay = $this->db->get()->result_array();
                }
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[0] . $row, $dem);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, $value['name_branch'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, ($value['name_department']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row, ($value['reference_no']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[4] . $row, $value['code_internal_proposal'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[5] . $row, $value['time_payment'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[6] . $row, '');
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[7] . $row, $value['total'])->getStyle("$columsExcel[7]$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['total']));
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[8] . $row,$value['tax_rate']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[9] . $row, ($value['total']))->getStyle("$columsExcel[9]$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['total']));
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[10] . $row, ($value['code_invoice']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[11] . $row, ($value['code_plan_propose']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[12] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[13] . $row, ($value['total_pay_slip']))->getStyle("$columsExcel[13]$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['total_pay_slip']));
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[14] . $row, ($total_old))->getStyle("$columsExcel[14]$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($total_old));
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[15] . $row, $value['code_production_report'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[16] . $row, (!empty($value['date_misa']) ? _dhau($value['date_misa']) : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[17] . $row, (!empty($value['date_foso']) ? _dhau($value['date_foso']) : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                if (!empty($dtOtherPay)) {
                    foreach ($dtOtherPay as $k => $v) {
                        $row++;
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[0] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row,'', PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[4] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[5] . $row,'' , PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[6] . $row,_dhau($v['date_pay_slip']));
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[7] . $row,'');
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[8] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[9] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[10] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[11] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[12] . $row, ($v['code_other_payslip']), PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[13] . $row, ($v['total_pay_slip']))->getStyle("$columsExcel[13]$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($v['total_pay_slip']));
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[14] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[15] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[16] . $row, (!empty($value['date_misa']) ? _dhau($value['date_misa']) : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[17] . $row, (!empty($v['date_pay_slip']) ? _dhau($v['date_pay_slip']) : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                    }
                }
            }
            $objPHPExcel->getActiveSheet()->getStyle('A3:R' . $row)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('A3:R' . $row)->applyFromArray([
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

            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $filename = lang('bao_cao_tong_hop_ke_hoach_phong_ban') . '.xls';
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
