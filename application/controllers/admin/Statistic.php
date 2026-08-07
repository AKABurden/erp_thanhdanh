<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Statistic extends AdminController
{
    public function __construct()
    {
        //cccc
        parent::__construct();
    }

    public function customer() {
        $data['title']   = _l('Thống Kê Khách Hàng');
        $this->load->view('admin/statistic/customer', $data);
    }

    public function getCustomer() {
        $aColumns = [
            'tblclients.userid as id',
            '(
                SELECT 
                GROUP_CONCAT(name SEPARATOR ",") 
                FROM tblcustomer_groups 
                JOIN tblcustomers_groups ON tblcustomer_groups.groupid = tblcustomers_groups.id 
                WHERE customer_id = tblclients.userid ORDER by name ASC
            ) as customerGroups',// nhóm khách hàng
            'tblclients.zcode as zcode',// mã khách hàng
            'tblclients.company as company',// tên khách hàng
            'tblclients.vat as vat',// mã số thuế
            'tblclients.code_xnk as code_xnk',// mã số XNK
            '(SELECT tbltaxes.name from tbltaxes WHERE tbltaxes.id = tblclients.vat_id LIMIT 1) as tax_name',// tên thuế
            'tblclients.bank_account as bank_account',// số tài khoản ngân hàng
            'tblclients.name_account as name_account',// tên ngân hàng
            'tblclients.address_bank as address_bank',// địa chỉ ngân hàng
            'IF(tm_ck = 1, "Tiền Mặt", "Chuyển Khoản") as payment_method',// phương thức thanh toán
            '(SELECT tblcurrencies.name FROM tblcurrencies WHERE tblcurrencies.id = tblclients.currency LIMIT 1) as currency',// đơn vị thanh toán
            '(SELECT amount_to_vnd FROM tblcurrencies WHERE tblcurrencies.id = tblclients.currency LIMIT 1) as price_currency',// công thức chuyển đổi tiền
            'time_payment as time_payment',// thời hạn thu
            '(LAST_DAY(NOW()) + INTERVAL time_payment DAY) as date_payment',// ngày thu
            'type_contract as type_contract',// loại hợp đồng
            'tblclients.deadline_contract as deadline_contract',// thời hạn hợp đồng
            'tblclients.date_renewal as date_renewal',// ngày tái tục
            '(
                    SELECT GROUP_CONCAT(tblbranch.name SEPARATOR ",") FROM tblbranch
                    JOIN tbl_client_branch ON tblbranch.id = tbl_client_branch.branch_id 
                    WHERE tbl_client_branch.client_id = tblclients.userid
                 ) as branch', //xưởng chi nhánh
            'tblclients.address as address',// địa chỉ văn phòng
            'tblcontacts.firstname as name_contact',// người liên lạc
            'tblcontacts.phonenumber as phonenumber_contact',// số điện thoại
            '(SELECT tblshipping_client.address FROM tblshipping_client WHERE tblshipping_client.client = tblclients.userid LIMIT 1) as address_shipping',// địa chỉ giao hàng
            'IF(tblclients.active = 1, "Đang Hoạt Động", "") as active',// trạng thái
            'IF(tblclients.active = 0, "Ngưng Sữ Dụng", "") as inactive',// ngưng sử dụng
            'tblclients.datecreated as datecreated',// ngày tạo
            'tblclients.date_update as dateEdit',// ngày điều chỉnh

        ];

        $where = [];
        if($this->input->post('start_date')) {
            $where[] = 'AND DATE_FORMAT(tblclients.datecreated, "%Y-%m-%d") >= "' . to_sql_date($this->input->post('start_date')).'"';
        }
        if($this->input->post('ennd_date')) {
            $where[] = 'AND DATE_FORMAT(tblclients.datecreated, "%Y-%m-%d") <= "' . to_sql_date($this->input->post('start_date')).'"';
        }

        $sIndexColumn = 'userid';
        $sTable       = 'tblclients';
        $join = [
            'LEFT JOIN tblcontacts ON tblcontacts.userid = tblclients.userid AND tblcontacts.is_primary = 1',
        ];
        $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);
        $output       = $result['output'];
        $rResult      = $result['rResult'];
        foreach ($rResult as $key =>  $aRow) {
            $row = [];
            $row[] = ($this->input->post('start')) + $key + 1;
            $row[] = $aRow['customerGroups'];// nhóm khách hàng
            $row[] = $aRow['zcode'];// mã khách hàng
            $row[] = $aRow['company'];// tên khách hàng
            $row[] = $aRow['vat'];// mã số thuế
            $row[] = $aRow['code_xnk'];// mã số XNK
            $row[] = $aRow['tax_name'];// tên thuế
            $row[] = $aRow['bank_account'];// số tài khoản ngân hàng
            $row[] = $aRow['name_account'];// tên ngân hàng
            $row[] = $aRow['address_bank'];// địa chỉ ngân hàng
            $row[] = $aRow['payment_method'];// phương thức thanh toán
            $row[] = $aRow['currency'];// đơn vị thanh toán
            $row[] = $aRow['price_currency'];// công thức chuyển đổi tiền
            $row[] = $aRow['time_payment'];// thời hạn thu
            $row[] = !empty($aRow['date_payment']) ? _d($aRow['date_payment']) : '';// ngày thu
            $row[] = $aRow['type_contract'];// loại hợp đồng
            $row[] = _d($aRow['deadline_contract']);// thời hạn hợp đồng
            $row[] = !empty($aRow['date_renewal']) ? _d($aRow['date_renewal']) : '';// ngày tái tục
            $row[] = $aRow['branch']; //xưởng chi nhánh
            $row[] = $aRow['address'];// địa chỉ văn phòng
            $row[] = $aRow['name_contact'];// người liên lạc
            $row[] = $aRow['phonenumber_contact'];// số điện thoại
            $row[] = $aRow['address_shipping'];// địa chỉ giao hàng
            $row[] = $aRow['active'];// trạng thái
            $row[] = $aRow['inactive'];// ngưng sử dụng
            $row[] = _dt($aRow['datecreated']);// ngày tạo
            $row[] = _dt($aRow['dateEdit']);// ngày điều chỉnh

            $output['aaData'][] = $row;
        }
        echo json_encode($output);die();

    }

    public function getCustomerExcel(){
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
                'tblclients.userid as id',
                '(
                SELECT 
                GROUP_CONCAT(name SEPARATOR ",") 
                FROM tblcustomer_groups 
                JOIN tblcustomers_groups ON tblcustomer_groups.groupid = tblcustomers_groups.id 
                WHERE customer_id = tblclients.userid ORDER by name ASC
            ) as customerGroups',// nhóm khách hàng
                'tblclients.zcode as zcode',// mã khách hàng
                'tblclients.company as company',// tên khách hàng
                'tblclients.vat as vat',// mã số thuế
                'tblclients.code_xnk as code_xnk',// mã số XNK
                '(SELECT tbltaxes.name from tbltaxes WHERE tbltaxes.id = tblclients.vat_id LIMIT 1) as tax_name',// tên thuế
                'tblclients.bank_account as bank_account',// số tài khoản ngân hàng
                'tblclients.name_account as name_account',// tên ngân hàng
                'tblclients.address_bank as address_bank',// địa chỉ ngân hàng
                'IF(tm_ck = 1, "Tiền Mặt", "Chuyển Khoản") as payment_method',// phương thức thanh toán
                '(SELECT tblcurrencies.name FROM tblcurrencies WHERE tblcurrencies.id = tblclients.currency LIMIT 1) as currency',// đơn vị thanh toán
                '(SELECT amount_to_vnd FROM tblcurrencies WHERE tblcurrencies.id = tblclients.currency LIMIT 1) as price_currency',// công thức chuyển đổi tiền
                'time_payment as time_payment',// thời hạn thu
                '(LAST_DAY(NOW()) + INTERVAL time_payment DAY) as date_payment',// ngày thu
                'type_contract as type_contract',// loại hợp đồng
                'tblclients.deadline_contract as deadline_contract',// thời hạn hợp đồng
                'tblclients.date_renewal as date_renewal',// ngày tái tục
                '(
                    SELECT GROUP_CONCAT(tblbranch.name SEPARATOR ",") FROM tblbranch
                    JOIN tbl_client_branch ON tblbranch.id = tbl_client_branch.branch_id 
                    WHERE tbl_client_branch.client_id = tblclients.userid
                 ) as branch', //xưởng chi nhánh
                'tblclients.address as address',// địa chỉ văn phòng
                'tblcontacts.firstname as name_contact',// người liên lạc
                'tblcontacts.phonenumber as phonenumber_contact',// số điện thoại
                '(SELECT tblshipping_client.address FROM tblshipping_client WHERE tblshipping_client.client = tblclients.userid LIMIT 1) as address_shipping',// địa chỉ giao hàng
                'IF(tblclients.active = 1, "Đang Hoạt Động", "") as active',// trạng thái
                'IF(tblclients.active = 0, "Ngưng Sữ Dụng", "") as inactive',// ngưng sử dụng
                'tblclients.datecreated as datecreated',// ngày tạo
                'tblclients.date_update as dateEdit',// ngày điều chỉnh

            ]);
            $this->db->from('tblclients');
            $this->db->join('tblcontacts','tblcontacts.userid = tblclients.userid AND tblcontacts.is_primary = 1','left');
            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tblclients.datecreated >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tblclients.datecreated <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tblclients.userid desc');
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
                ('THỐNG KÊ KHÁCH HÀNG') . $titleAppend)->getStyle("A1")->applyFromArray([
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
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Nhóm KH');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Mã Khách Hàng');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Tên Khách Hàng');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Mã Số Thuế');
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Mã Số XNK')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'VAT')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Số Tài Khoản Ngân Hàng')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Tên Ngân Hàng')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Địa Chỉ Ngân Hàng')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Phương Thức Thanh Toán')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Đơn Vị Thanh Toán')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$sttRow.'', 'Công Thức Chuyển Đổi Tiền')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$sttRow.'', 'Thời Hạn Thu')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$sttRow.'', 'Ngày Thu')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$sttRow.'', 'Loại Hợp Đồng')->getStyle("P$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$sttRow.'', 'Thời Hạn Hợp Đồng')->getStyle("Q$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('R'.$sttRow.'', 'Ngày Tái Tục')->getStyle("R$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('S'.$sttRow.'', 'Xưởng, Chi Nhánh')->getStyle("S$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('T'.$sttRow.'', 'Địa Chỉ VP-Chi Nhánh')->getStyle("T$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('U'.$sttRow.'', 'Người Liên Lạc')->getStyle("U$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('V'.$sttRow.'', 'Điện Thoại')->getStyle("V$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('W'.$sttRow.'', 'Địa Chỉ Giao Hàng')->getStyle("W$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('X'.$sttRow.'', 'Đang Hoạt Động')->getStyle("X$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Y'.$sttRow.'', 'Ngưng Sử Dụng')->getStyle("Y$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Z'.$sttRow.'', 'Ngày Tạo')->getStyle("Z$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AA'.$sttRow.'', 'Ngày Điều Chỉnh')->getStyle("AA$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:AA$sttRow")->applyFromArray([
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
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['customerGroups']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['zcode']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['company'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['vat']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $value['code_xnk'])->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $value['tax_name'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin",$value['bank_account'])->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $value['name_account'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $value['address_bank'])->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $value['payment_method'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $value['currency'])->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", $value['price_currency'])->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", $value['time_payment'])->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", (!empty($value['date_payment']) ? _d($value['date_payment']) : ''))->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", $value['type_contract'])->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin", $value['deadline_contract'])->getStyle("Q$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin", (!empty($value['date_renewal']) ? _d($value['date_renewal']) : ''))->getStyle("R$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("S$rowBegin", $value['branch'])->getStyle("S$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("T$rowBegin", $value['address'])->getStyle("T$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("U$rowBegin", $value['name_contact'])->getStyle("U$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("V$rowBegin", $value['phonenumber_contact'])->getStyle("V$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("W$rowBegin", $value['address_shipping'])->getStyle("W$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("X$rowBegin", $value['active'])->getStyle("X$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Y$rowBegin", $value['inactive'])->getStyle("Y$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Z$rowBegin", _dt($value['datecreated']))->getStyle("Z$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AA$rowBegin", _dt($value['dateEdit']))->getStyle("AA$rowBegin")->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:AA$rowBegin")->applyFromArray([
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
            $filename = lang('thong_ke_khach_hang') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(25);
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


    public function supplier() {
        $data['title']   = _l('Thống Kê Nhà Cung Cấp');
        $this->load->view('admin/statistic/supplier', $data);
    }

    public function getSupplier() {
        $aColumns = [
            'tblsuppliers.id as id',
            '(
                SELECT GROUP_CONCAT(distinct(tblcosts.name) SEPARATOR ",\n") 
                FROM tblcosts 
                JOIN tblother_payslips ON tblother_payslips.id_costs = tblcosts.id
                WHERE tblother_payslips.objects_id = tblsuppliers.id
                AND tblother_payslips.objects = 2
             ) as group_cp',//mã nhóm chi phí
            '(
                SELECT tblsuppliers_groups.name
                FROM tblsuppliers_groups 
                WHERE tblsuppliers_groups.id = tblsuppliers.groups_in
            ) as SupplierGroups',// nhóm nhà cung cấp
            '(
                SELECT GROUP_CONCAT(distinct(tbl_category_items.name) SEPARATOR ",\n") 
                FROM tbl_category_items 
                LEFT JOIN tbl_materials ON tbl_materials.category_id = tbl_category_items.id
                JOIN tbl_material_suppliers ON tbl_material_suppliers.material_id = tbl_materials.id
                WHERE tbl_material_suppliers.supplier_id = tblsuppliers.id
            ) as group_npl',//Nhóm Nguyên Phụ Liệu
            'CONCAT(tblsuppliers.prefix, "-", tblsuppliers.code) as code_supplier',// mã nhà cung cấp
            'tblsuppliers.company as company',// tên nhà cung cấp
            'tblsuppliers.vat as vat',// mã số thuế
            'tblsuppliers.code_nxk as code_nxk',// mã số XNK
            'tblsuppliers.tax as tax_name',// VAT
            'tblsuppliers.bank_account as bank_account',// số tài khoản ngân hàng
            'tblsuppliers.name_account as name_account',// tên ngân hàng
            'tblsuppliers.address_bank as address_bank',// địa chỉ ngân hàng
            'IF(tblsuppliers.tm_ck = 1, "Tiền Mặt", "Chuyển Khoản") as payment_method',// phương thức thanh toán
            '(SELECT tblcurrencies.name FROM tblcurrencies WHERE tblcurrencies.id = tblsuppliers.default_currency LIMIT 1) as currency',// đơn vị thanh toán
            '(SELECT amount_to_vnd FROM tblcurrencies WHERE tblcurrencies.id = tblsuppliers.default_currency LIMIT 1) as price_currency',// công thức chuyển đổi tiền
            'tblsuppliers.time_payment as time_payment',// thời hạn chi
            'contract_number as type_contract',// loại hợp đồng
            'tblsuppliers.deadline_contract as deadline_contract',// thời hạn hợp đồng
            'tblsuppliers.renewal_date as date_renewal',// ngày tái tục
            '(
                SELECT GROUP_CONCAT(tblbranch.name SEPARATOR ",") FROM tblbranch
                JOIN tbl_suppliers_branch ON tbl_suppliers_branch.branch_id  = tblbranch.id
                WHERE tbl_suppliers_branch.suppliers_id = tblsuppliers.id
             ) as branch', //xưởng chi nhánh
            'tblsuppliers.address as address',// địa chỉ văn phòng
            'tblcontacts_suppliers.name as name_contact',// người liên lạc
            'tblcontacts_suppliers.phone as phonenumber_contact',// số điện thoại
            'tblsuppliers.address as address_shipping',// địa chỉ giao hàng
            'IF(tblsuppliers.active = 1, "Đang Hoạt Động", "") as active',// trạng thái
            'IF(tblsuppliers.active = 0, "Ngưng Sữ Dụng", "") as inactive',// ngưng sử dụng
            'tblsuppliers.datecreated as datecreated',// ngày tạo
            'tblsuppliers.date_update as dateEdit',// ngày điều chỉnh

        ];

        $where = [];
        if($this->input->post('start_date')) {
            $where[] = 'AND DATE_FORMAT(tblsuppliers.datecreated, "%Y-%m-%d") >= "' . to_sql_date($this->input->post('start_date')).'"';
        }
        if($this->input->post('ennd_date')) {
            $where[] = 'AND DATE_FORMAT(tblsuppliers.datecreated, "%Y-%m-%d") <= "' . to_sql_date($this->input->post('start_date')).'"';
        }

        $sIndexColumn = 'id';
        $sTable       = 'tblsuppliers';
        $join = [
                'LEFT JOIN tblcontacts_suppliers ON tblcontacts_suppliers.id_supplers = tblsuppliers.id',
        ];
        $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);
        $output       = $result['output'];
        $rResult      = $result['rResult'];
        foreach ($rResult as $key =>  $aRow) {
            $row = [];
            $row[] = ($this->input->post('start')) + $key + 1;
            $row[] = '<div style="white-space: pre;width:250px;">' . $aRow['group_cp'] . '</div>';//mã nhóm chi phí
            $row[] = '<div style="width:150px;">' . $aRow['SupplierGroups'] . '</div>';// nhóm nhà cung cấp
            $row[] = '<div style="white-space: pre;width:250px;">' . $aRow['group_npl'] . '</div>';//Nhóm Nguyên Phụ Liệu
            $row[] = $aRow['code_supplier'];// mã nhà cung cấp
            $row[] = '<div style="width:200px;">' . $aRow['company'] . '</div>';// tên nhà cung cấp
            $row[] = $aRow['vat'];// mã số thuế
            $row[] = $aRow['code_nxk'];// mã số XNK
            $row[] = $aRow['tax_name'];// VAT
            $row[] = $aRow['bank_account'];// số tài khoản ngân hàng
            $row[] = $aRow['name_account'];// tên ngân hàng
            $row[] = $aRow['address_bank'];// địa chỉ ngân hàng
            $row[] = $aRow['payment_method'];// phương thức thanh toán
            $row[] = $aRow['currency'];// đơn vị thanh toán
            $row[] = $aRow['price_currency'];// công thức chuyển đổi tiền
            $row[] = $aRow['time_payment'];// thời hạn chi
            $row[] = $aRow['type_contract'];// loại hợp đồng
            $row[] = $aRow['deadline_contract']; // thời hạn hợp đồng
            $row[] = $aRow['date_renewal'];// ngày tái tục
            $row[] = $aRow['branch'];//xưởng chi nhánh
            $row[] = $aRow['address'];// địa chỉ văn phòng
            $row[] = $aRow['name_contact'];// người liên lạc
            $row[] = $aRow['phonenumber_contact'];// người liên lạc
            $row[] = $aRow['address_shipping'];// địa chỉ giao hàng
            $row[] = $aRow['active'];// trạng thái
            $row[] = $aRow['inactive'];// ngưng sử dụng
            $row[] = _dt($aRow['datecreated']);// ngày tạo
            $row[] = _dt($aRow['dateEdit']);// ngày điều chỉnh

            $output['aaData'][] = $row;
        }
        echo json_encode($output);die();

    }

    public function getSupplierExcel(){
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
                'tblsuppliers.id as id',
                '(
                SELECT GROUP_CONCAT(distinct(tblcosts.name) SEPARATOR ",\n") 
                FROM tblcosts 
                JOIN tblother_payslips ON tblother_payslips.id_costs = tblcosts.id
                WHERE tblother_payslips.objects_id = tblsuppliers.id
                AND tblother_payslips.objects = 2
             ) as group_cp',//mã nhóm chi phí
                '(
                SELECT tblsuppliers_groups.name
                FROM tblsuppliers_groups 
                WHERE tblsuppliers_groups.id = tblsuppliers.groups_in
            ) as SupplierGroups',// nhóm nhà cung cấp
                '(
                SELECT GROUP_CONCAT(distinct(tbl_category_items.name) SEPARATOR ",\n") 
                FROM tbl_category_items 
                LEFT JOIN tbl_materials ON tbl_materials.category_id = tbl_category_items.id
                JOIN tbl_material_suppliers ON tbl_material_suppliers.material_id = tbl_materials.id
                WHERE tbl_material_suppliers.supplier_id = tblsuppliers.id
            ) as group_npl',//Nhóm Nguyên Phụ Liệu
                'CONCAT(tblsuppliers.prefix, "-", tblsuppliers.code) as code_supplier',// mã nhà cung cấp
                'tblsuppliers.company as company',// tên nhà cung cấp
                'tblsuppliers.vat as vat',// mã số thuế
                'tblsuppliers.code_nxk as code_nxk',// mã số XNK
                'tblsuppliers.tax as tax_name',// VAT
                'tblsuppliers.bank_account as bank_account',// số tài khoản ngân hàng
                'tblsuppliers.name_account as name_account',// tên ngân hàng
                'tblsuppliers.address_bank as address_bank',// địa chỉ ngân hàng
                'IF(tblsuppliers.tm_ck = 1, "Tiền Mặt", "Chuyển Khoản") as payment_method',// phương thức thanh toán
                '(SELECT tblcurrencies.name FROM tblcurrencies WHERE tblcurrencies.id = tblsuppliers.default_currency LIMIT 1) as currency',// đơn vị thanh toán
                '(SELECT amount_to_vnd FROM tblcurrencies WHERE tblcurrencies.id = tblsuppliers.default_currency LIMIT 1) as price_currency',// công thức chuyển đổi tiền
                'tblsuppliers.time_payment as time_payment',// thời hạn chi
                'contract_number as type_contract',// loại hợp đồng
                'tblsuppliers.deadline_contract as deadline_contract',// thời hạn hợp đồng
                'tblsuppliers.renewal_date as date_renewal',// ngày tái tục
                '(
                SELECT GROUP_CONCAT(tblbranch.name SEPARATOR ",") FROM tblbranch
                JOIN tbl_suppliers_branch ON tbl_suppliers_branch.branch_id  = tblbranch.id
                WHERE tbl_suppliers_branch.suppliers_id = tblsuppliers.id
             ) as branch', //xưởng chi nhánh
                'tblsuppliers.address as address',// địa chỉ văn phòng
                'tblcontacts_suppliers.name as name_contact',// người liên lạc
                'tblcontacts_suppliers.phone as phonenumber_contact',// số điện thoại
                'tblsuppliers.address as address_shipping',// địa chỉ giao hàng
                'IF(tblsuppliers.active = 1, "Đang Hoạt Động", "") as active',// trạng thái
                'IF(tblsuppliers.active = 0, "Ngưng Sữ Dụng", "") as inactive',// ngưng sử dụng
                'tblsuppliers.datecreated as datecreated',// ngày tạo
                'tblsuppliers.date_update as dateEdit',// ngày điều chỉnh

            ]);
            $this->db->from('tblsuppliers');
            $this->db->join('tblcontacts_suppliers','tblcontacts_suppliers.id_supplers = tblsuppliers.id','left');
            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tblsuppliers.datecreated >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tblsuppliers.datecreated <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tblsuppliers.id desc');
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
                ('THỐNG KÊ NHÀ CUNG CẤP') . $titleAppend)->getStyle("A1")->applyFromArray([
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
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Mã Nhóm Chi Phí');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Nhóm Nhà Cung Cấp');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Nhóm NPL');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Mã Nhà CC');
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Tên Nhà Cung Cấp')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Mã Số Thuế')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Mã Số XNK')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'VAT')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Số Tài Khoản Ngân Hàng')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Tên Ngân Hàng')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Địa Chỉ Ngân Hàng')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$sttRow.'', 'Phương Thức Thanh Toán')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$sttRow.'', 'Đơn Vị Thanh Toán')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$sttRow.'', 'Công Thức Chuyển Đổi Tiền')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$sttRow.'', 'Thời Hạn Chi')->getStyle("P$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$sttRow.'', 'Loại Hợp Đồng')->getStyle("Q$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('R'.$sttRow.'', 'Thời Hạn Hợp Đồng')->getStyle("R$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('S'.$sttRow.'', 'Ngày Tái Tục')->getStyle("S$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('T'.$sttRow.'', 'Xưởng, Chi Nhánh')->getStyle("T$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('U'.$sttRow.'', 'Địa Chỉ VP-Chi Nhánh')->getStyle("U$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('V'.$sttRow.'', 'Người Liên Lạc')->getStyle("V$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('W'.$sttRow.'', 'Điện Thoại')->getStyle("W$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('X'.$sttRow.'', 'Địa Chỉ Giao Hàng')->getStyle("X$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Y'.$sttRow.'', 'Đang Hoạt Động')->getStyle("Y$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Z'.$sttRow.'', 'Ngưng Sử Dụng')->getStyle("Z$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AA'.$sttRow.'', 'Ngày Tạo')->getStyle("AA$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AB'.$sttRow.'', 'Ngày Điều Chỉnh')->getStyle("AB$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:AB$sttRow")->applyFromArray([
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
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['group_cp']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['SupplierGroups']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['group_npl'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['code_supplier']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $value['company'])->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $value['vat'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin",$value['code_nxk'])->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $value['tax_name'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $value['bank_account'])->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $value['name_account'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $value['address_bank'])->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", $value['payment_method'])->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", $value['currency'])->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", (!empty($value['price_currency']) ? ($value['price_currency']) : ''))->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", $value['time_payment'])->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin", $value['type_contract'])->getStyle("Q$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin", (!empty($value['deadline_contract']) ? ($value['deadline_contract']) : ''))->getStyle("R$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("S$rowBegin", $value['date_renewal'])->getStyle("S$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("T$rowBegin", $value['branch'])->getStyle("T$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("U$rowBegin", $value['address'])->getStyle("U$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("V$rowBegin", $value['name_contact'])->getStyle("V$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("W$rowBegin", $value['phonenumber_contact'])->getStyle("W$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("X$rowBegin", $value['address_shipping'])->getStyle("X$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Y$rowBegin", $value['active'])->getStyle("Y$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Z$rowBegin", $value['inactive'])->getStyle("Z$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AA$rowBegin", _dt($value['datecreated']))->getStyle("AA$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AB$rowBegin", _dt($value['dateEdit']))->getStyle("AB$rowBegin")->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:AB$rowBegin")->applyFromArray([
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
            $filename = lang('thong_ke_nha_cung_cap') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(25);
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

    public function staff() {
        $data['title']   = _l('Thống Kê Nhân Viên');
        $this->load->view('admin/statistic/staff', $data);
    }

    public function getStaff() {
        $wherePayroll = [];
        $YearNowStart = date('Y');
        $YearNowEnd = date('Y');

        if($this->input->post('start_date')) {
            $start_date = to_sql_date($this->input->post('start_date'));
            $FmDate = DateTime::createFromFormat('Y-m-d', $start_date);
            $year = $FmDate->format('Y');
            $month = $FmDate->format('m');
            $day = $FmDate->format('d');

            $wherePayroll[] = 'AND STR_TO_DATE(CONCAT(tbl_payroll.year, "-", tbl_payroll.month, "-01"), "%Y-%m-%d") >= "'.($year.'-'.$month.'-01').'"';

            $YearNowStart = $year;
        }
        else {
            $wherePayroll[] = 'AND STR_TO_DATE(CONCAT(tbl_payroll.year, "-", tbl_payroll.month, "-01"), "%Y-%m-%d") >= "'.(date('Y').'-'.date('m').'-01').'"';
        }

        if($this->input->post('end_date')) {
            $end_date = to_sql_date($this->input->post('end_date'));
            $FmDate = DateTime::createFromFormat('Y-m-d', $end_date);
            $year = $FmDate->format('Y');
            $month = $FmDate->format('m');
            $day = $FmDate->format('d');
            $wherePayroll[] = 'AND STR_TO_DATE(CONCAT(tbl_payroll.year, "-", tbl_payroll.month, "-31"), "%Y-%m-%d") <= "'.$year.'-'.$month.'-31"';
            $YearNowEnd = $year;
        }
        else {
            $wherePayroll[] = 'AND STR_TO_DATE(CONCAT(tbl_payroll.year, "-", tbl_payroll.month, "-31"), "%Y-%m-%d") <= "'.date('Y-m-t').'"';
        }

        $aColumns = [
            'tblstaff.staffid as staffid',
            '(
                SELECT GROUP_CONCAT(tbldepartments.name) 
                FROM tbldepartments 
                LEFT JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
                WHERE tblstaff_departments.staffid = tblstaff.staffid
            ) as name_room',//phòng ban
            'tblroles.code_role as code_role',//mã vị trí
            'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname',//họ tên nhân viên
            'tblstaff.code as code_staff',// mã nhân viên
            'tblstaff.phonenumber as phonenumber',// số điện thoại
            'tblstaff.resident as resident',// địa chỉ nơi cư trú
            'tblstaff.birthday as birthday',// ngày sinh
            'tblstaff.birthplace as birthplace',// nơi sinh
            'tblstaff.domicile as domicile',// quê quán
            'tblstaff.cmnd_id_passport as cmnd_id_passport',// căn cước công dân
            'tblstaff.date_range as date_range',// ngày cấp
            'tblstaff.issued_by as issued_by',// nơi cấp
            'IF(tblstaff.marital_status = "alone", "Độc Thân", IF(tblstaff.marital_status = "marriage", "Kết Hôn", IF(tblstaff.marital_status = "divorce", "Ly Hôn", ""))) as marital_status', //IF(tblstaff.marital_status = "alone", "Độc Thân", "") as marital_status',// tình trạng hôn nhân - alone - độc thân, marriage - kết hôn, divorce - ly hôn
            'tblstaff.nationality as nationality',// Quốc tịch
            'tblstaff.nation as nation',// Dân tộc
            'tblstaff.account_name as account_name',// tài khoản ngân hàng
            'CONCAT(COALESCE(tblstaff.bank, "")) as bank_account',//Tên ngân hàng - chi nhánh
            'tbl_contract_labor.date_probation as date_probation',// ngày thử việc
            '"" as time_trial',// thời gian thử việc
            'tbl_contract_labor.code as code_contract_labor',//Hợp đồng lao động
            'tbl_contract_labor.date_sign_contract as date_sign_contract',//ngày ký hợp đồng
            'tbl_contract_labor.date_end as date_end_contract',//thời hạn hợp đồng
            'tbl_contract_labor.date_sign as date_sign',//ngày tái ký hợp đồng
            'tbl_contract_labor.salary_basic as salary_basic',//lương cơ bản
            'tbl_contract_labor.salary_position as salary_position',//lương năng lực
            '(
                SELECT SUM(COALESCE(tbl_allowance_reduce_payroll.amount, 0)) 
                FROM tbl_allowance_reduce_payroll 
                LEFT JOIN tbl_payroll_item ON tbl_payroll_item.id = tbl_allowance_reduce_payroll.payroll_item_id
                LEFT JOIN tbl_payroll ON tbl_payroll.id = tbl_payroll_item.payroll_id
                WHERE tbl_payroll_item.staff_id = tblstaff.staffid
                '.implode(' ', $wherePayroll).'
            ) as lapc',//phụ cấp,
            'salary_bhxh_new',//bảo hểm xã hội
            'insurrance_book_number',//Số sổ bảo hểm xã hội
            '(
                SELECT SUM(COALESCE(tbl_payroll_item.total_vat, 0)) 
                FROM tbl_payroll_item 
                JOIN tbl_payroll ON tbl_payroll.id = tbl_payroll_item.payroll_id
                WHERE tbl_payroll_item.staff_id = tblstaff.staffid
                '.implode(' ', $wherePayroll).'
            ) as total_vat',//thuế TNCN
            'personal_tax_code',//Mã số thuế cá nhân
            '(salary_bhxh_new * 4.5 /100) as bhty',//BHYT
            'number_bhty',// số thẻ BHYT
            '(
                SELECT SUM(tbl_setup_paid_holiday_staff.number_day) 
                FROM tbl_setup_paid_holiday
                JOIN tbl_setup_paid_holiday_staff ON tbl_setup_paid_holiday_staff.id_setup_paid_holiday = tbl_setup_paid_holiday.id
                WHERE tbl_setup_paid_holiday_staff.staff_id = tblstaff.staffid
                AND tbl_setup_paid_holiday.year >= "'.$YearNowStart.'" AND tbl_setup_paid_holiday.year <= "'.$YearNowEnd.'"
            ) as number_leave_year',//số ngày phép năm
            'tblstaff.seniority as seniority',//Thâm Niên
            'IF(tblstaff.active = 1, "Đang Hoạt Động", "") as active',// trạng thái
            'IF(tblstaff.active = 0, "Ngưng Sữ Dụng", "") as inactive',// ngưng sử dụng
            'tblstaff.datecreated as datecreated',// ngày tạo
            'tblstaff.date_update as dateEdit',// ngày điều chỉnh
        ];
        $where = [];
        $sIndexColumn = 'staffid';
        $sTable       = 'tblstaff';
        $join = [
            'LEFT JOIN tblroles ON tblroles.roleid = tblstaff.role',
            'LEFT JOIN tbl_contract_labor ON tbl_contract_labor.staff_id = tblstaff.staffid',
        ];
        $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'branch_bank'
        ]);
        $output       = $result['output'];
        $rResult      = $result['rResult'];
        foreach ($rResult as $key =>  $aRow) {
            $row = [];
            $row[] = ($this->input->post('start')) + $key + 1;
            $row[] = $aRow['name_room'];//phòng ban
            $row[] = $aRow['code_role']; // mã vị trí
            $row[] = $aRow['fullname']; //họ tên nhân viên
            $row[] = $aRow['code_staff']; //Mã nhân viên
            $row[] = !empty($aRow['phonenumber']) ? $aRow['phonenumber'] : ''; //số điện thoại
            $row[] = $aRow['resident']; //Địa chỉ nơi cư trú
            $row[] = !empty($aRow['birthday']) ? _dC($aRow['birthday']) : ''; //ngày sinh
            $row[] = $aRow['birthplace']; //nơi sinh
            $row[] = $aRow['domicile']; //quê quán
            $row[] = $aRow['cmnd_id_passport'];// căn cước công dân
            $row[] = _dt($aRow['date_range']);// ngày cấp
            $row[] = $aRow['issued_by'];// nơi cấp
            $row[] = $aRow['marital_status'];// tình trạng hôn nhân - alone - độc thân, marriage - kết hôn, divorce - ly hôn
            $row[] = $aRow['nationality']; //Quốc tịch
            $row[] = $aRow['nation']; //Dân tộc
            $row[] = $aRow['account_name']; //Tài khoản ngân hàng
            $brank = [];
            if(!empty($aRow['bank_account'])) {
                $brank[] = $aRow['bank_account'];
            }
            if(!empty($aRow['branch_bank'])) {
                $brank[] = $aRow['branch_bank'];
            }
            $row[] = implode(" - ", $brank); //Tên ngân hàng - Chi nhánh
            $row[] = $aRow['date_probation']; //Ngày thử việc
            $row[] = $aRow['time_trial']; //Thời gian thử việc
            $row[] = $aRow['code_contract_labor']; //Hợp đồng lao động
            $row[] = _dt($aRow['date_sign_contract']); //Ngày ký hợp đồng
            $row[] = _dt($aRow['date_end_contract']); //Thời hạn hợp đồng
            $row[] = _dt($aRow['date_sign']); //Ngày tái ký hợp đồng
            $row[] = number_format_data($aRow['salary_basic']); //Lương cơ bản
            $row[] = number_format_data($aRow['salary_position']); //Lương năng lực
            $row[] = number_format_data($aRow['lapc']); //Phụ cấp
            $row[] = !empty($aRow['salary_bhxh_new']) ? number_format_data($aRow['salary_bhxh_new']) : ''; //$aRow['salary_bhxh_new']; //Bảo hiểm xã hội
            $row[] = !empty($aRow['insurrance_book_number']) ? $aRow['insurrance_book_number'] : '';  //Số sổ bảo hiểm xã hội
            $row[] = number_format_data($aRow['total_vat']); //Thuế TNCN
            $row[] = $aRow['personal_tax_code']; //Mã số thuế cá nhân
            $row[] = '<div class="text-center">' . (!empty($aRow['bhty']) ? number_format_data($aRow['bhty']) : '') . '</div>'; //BHYT
            $row[] = '<div class="text-center">' . $aRow['number_bhty'] . '</div>'; //số thẻ BHYT
            $row[] = '<div class="text-center">' . $aRow['number_leave_year'] .'</div>'; //Số ngày phép năm
            $row[] = '<div class="text-center">' . (!empty($aRow['seniority']) ? $aRow['seniority'] : '') . '</div>'; //Thâm niên
            $row[] = $aRow['active']; //Trạng thái
            $row[] = $aRow['inactive']; //Ngưng sử dụng
            $row[] = _dt($aRow['datecreated']); //Ngày tạo
            $row[] = ''; //Ngày điều chỉnh
            $output['aaData'][] = $row;
        }
        echo json_encode($output);die();
    }

    public function getStaffExcel(){
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
            $YearNowStart = date('Y');
            $YearNowEnd = date('Y');

            if($this->input->post('start_date_search')) {
                $start_date = to_sql_date($this->input->post('start_date_search'));
                $FmDate = DateTime::createFromFormat('Y-m-d', $start_date);
                $year = $FmDate->format('Y');
                $month = $FmDate->format('m');
                $day = $FmDate->format('d');

                $wherePayroll[] = 'AND STR_TO_DATE(CONCAT(tbl_payroll.year, "-", tbl_payroll.month, "-01"), "%Y-%m-%d") >= "'.($year.'-'.$month.'-01').'"';

                $YearNowStart = $year;
            }
            else {
                $wherePayroll[] = 'AND STR_TO_DATE(CONCAT(tbl_payroll.year, "-", tbl_payroll.month, "-01"), "%Y-%m-%d") >= "'.(date('Y').'-'.date('m').'-01').'"';
            }

            if($this->input->post('end_date_search')) {
                $end_date = to_sql_date($this->input->post('end_date_search'));
                $FmDate = DateTime::createFromFormat('Y-m-d', $end_date);
                $year = $FmDate->format('Y');
                $month = $FmDate->format('m');
                $day = $FmDate->format('d');
                $wherePayroll[] = 'AND STR_TO_DATE(CONCAT(tbl_payroll.year, "-", tbl_payroll.month, "-31"), "%Y-%m-%d") <= "'.$year.'-'.$month.'-31"';
                $YearNowEnd = $year;
            }
            else {
                $wherePayroll[] = 'AND STR_TO_DATE(CONCAT(tbl_payroll.year, "-", tbl_payroll.month, "-31"), "%Y-%m-%d") <= "'.date('Y-m-t').'"';
            }


            $this->db->select([
                'tblstaff.staffid as staffid',
                '(
                    SELECT GROUP_CONCAT(tbldepartments.name) 
                    FROM tbldepartments 
                    LEFT JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
                    WHERE tblstaff_departments.staffid = tblstaff.staffid
                ) as name_room',//phòng ban
                'tblroles.code_role as code_role',//mã vị trí
                'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname',//họ tên nhân viên
                'tblstaff.code as code_staff',// mã nhân viên
                'tblstaff.phonenumber as phonenumber',// số điện thoại
                'tblstaff.resident as resident',// địa chỉ nơi cư trú
                'tblstaff.birthday as birthday',// ngày sinh
                'tblstaff.birthplace as birthplace',// nơi sinh
                'tblstaff.domicile as domicile',// quê quán
                'tblstaff.cmnd_id_passport as cmnd_id_passport',// căn cước công dân
                'tblstaff.date_range as date_range',// ngày cấp
                'tblstaff.issued_by as issued_by',// nơi cấp
                'IF(tblstaff.marital_status = "alone", "Độc Thân", IF(tblstaff.marital_status = "marriage", "Kết Hôn", IF(tblstaff.marital_status = "divorce", "Ly Hôn", ""))) as marital_status', //IF(tblstaff.marital_status = "alone", "Độc Thân", "") as marital_status',// tình trạng hôn nhân - alone - độc thân, marriage - kết hôn, divorce - ly hôn
                'tblstaff.nationality as nationality',// Quốc tịch
                'tblstaff.nation as nation',// Dân tộc
                'tblstaff.account_name as account_name',// tài khoản ngân hàng
                'CONCAT(COALESCE(tblstaff.bank, "")) as bank_account',//Tên ngân hàng - chi nhánh
                'tbl_contract_labor.date_probation as date_probation',// ngày thử việc
                '"" as time_trial',// thời gian thử việc
                'tbl_contract_labor.code as code_contract_labor',//Hợp đồng lao động
                'tbl_contract_labor.date_sign_contract as date_sign_contract',//ngày ký hợp đồng
                'tbl_contract_labor.date_end as date_end_contract',//thời hạn hợp đồng
                'tbl_contract_labor.date_sign as date_sign',//ngày tái ký hợp đồng
                'tbl_contract_labor.salary_basic as salary_basic',//lương cơ bản
                'tbl_contract_labor.salary_position as salary_position',//lương năng lực
                '(
                    SELECT SUM(COALESCE(tbl_allowance_reduce_payroll.amount, 0)) 
                    FROM tbl_allowance_reduce_payroll 
                    LEFT JOIN tbl_payroll_item ON tbl_payroll_item.id = tbl_allowance_reduce_payroll.payroll_item_id
                    LEFT JOIN tbl_payroll ON tbl_payroll.id = tbl_payroll_item.payroll_id
                    WHERE tbl_payroll_item.staff_id = tblstaff.staffid
                    '.implode(' ', $wherePayroll).'
                ) as lapc',//phụ cấp,
                'salary_bhxh_new',//bảo hểm xã hội
                'insurrance_book_number',//Số sổ bảo hểm xã hội
                '(
                    SELECT SUM(COALESCE(tbl_payroll_item.total_vat, 0)) 
                    FROM tbl_payroll_item 
                    JOIN tbl_payroll ON tbl_payroll.id = tbl_payroll_item.payroll_id
                    WHERE tbl_payroll_item.staff_id = tblstaff.staffid
                    '.implode(' ', $wherePayroll).'
                ) as total_vat',//thuế TNCN
                'personal_tax_code',//Mã số thuế cá nhân
                '(salary_bhxh_new * 4.5 /100) as bhty',//BHYT
                'number_bhty',// số thẻ BHYT
                '(
                    SELECT SUM(tbl_setup_paid_holiday_staff.number_day) 
                    FROM tbl_setup_paid_holiday
                    JOIN tbl_setup_paid_holiday_staff ON tbl_setup_paid_holiday_staff.id_setup_paid_holiday = tbl_setup_paid_holiday.id
                    WHERE tbl_setup_paid_holiday_staff.staff_id = tblstaff.staffid
                    AND tbl_setup_paid_holiday.year >= "'.$YearNowStart.'" AND tbl_setup_paid_holiday.year <= "'.$YearNowEnd.'"
                ) as number_leave_year',//số ngày phép năm
                'tblstaff.seniority as seniority',//Thâm Niên
                'IF(tblstaff.active = 1, "Đang Hoạt Động", "") as active',// trạng thái
                'IF(tblstaff.active = 0, "Ngưng Sữ Dụng", "") as inactive',// ngưng sử dụng
                'tblstaff.datecreated as datecreated',// ngày tạo
                'tblstaff.date_update as dateEdit',// ngày điều chỉnh
                'tblstaff.branch_bank'
            ]);
            $this->db->from('tblstaff');
            $this->db->join('tblroles','tblroles.roleid = tblstaff.role','left');
            $this->db->join('tbl_contract_labor','tbl_contract_labor.staff_id = tblstaff.staffid','left');

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
                ('THỐNG KÊ NHÂN VIÊN') . $titleAppend)->getStyle("A1")->applyFromArray([
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
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Phòng Ban');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Mã Vị Trí');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Họ Và Tên Nhân Viên');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Mã Nhân Viên');
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Số Điện Thoại')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Địa Chỉ Nơi Cư Trú')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Ngày Sinh')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Nơi Sinh')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Nguyên Quán')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'CCCD')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Ngày Cấp')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$sttRow.'', 'Nơi Cấp')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$sttRow.'', 'Tình Trạng Hôn Nhân')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$sttRow.'', 'Quốc Tịch')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$sttRow.'', 'Dân Tộc')->getStyle("P$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$sttRow.'', 'Tài Khoản Ngân Hàng')->getStyle("Q$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('R'.$sttRow.'', 'Tên Ngân Hàng-Chi Nhánh')->getStyle("R$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('S'.$sttRow.'', 'Ngày Thử Việc')->getStyle("S$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('T'.$sttRow.'', 'Thời Gian Thử Việc')->getStyle("T$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('U'.$sttRow.'', 'Mã Số Hợp Đồng')->getStyle("U$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('V'.$sttRow.'', 'Ngày Ký Hợp Đồng')->getStyle("V$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('W'.$sttRow.'', 'Thời Hạn Hợp Đồng')->getStyle("W$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('X'.$sttRow.'', 'Ngày Tái Ký Hợp Đồng')->getStyle("X$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Y'.$sttRow.'', 'Mức Lương Cơ Bản')->getStyle("Y$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Z'.$sttRow.'', 'Mức Lương Năng Lực')->getStyle("Z$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AA'.$sttRow.'', 'Phụ Cấp')->getStyle("AA$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AB'.$sttRow.'', 'BHXH')->getStyle("AB$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AC'.$sttRow.'', 'Số Sổ BHXH')->getStyle("AC$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AD'.$sttRow.'', 'Thuế TNCN')->getStyle("AD$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AE'.$sttRow.'', 'Mã Số Thuế Cá Nhân')->getStyle("AE$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AF'.$sttRow.'', 'BHYT')->getStyle("AF$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AG'.$sttRow.'', 'Số Thẻ BHYT')->getStyle("AG$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AH'.$sttRow.'', 'Số Ngày Phép Năm')->getStyle("AH$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AI'.$sttRow.'', 'Số Năm Thâm Niên')->getStyle("AI$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AJ'.$sttRow.'', 'Đang Hoạt Động')->getStyle("AJ$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AK'.$sttRow.'', 'Ngưng Sử Dụng')->getStyle("AK$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AL'.$sttRow.'', 'Ngày Tạo')->getStyle("AL$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AM'.$sttRow.'', 'Ngày Điều Chỉnh')->getStyle("AM$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:AM$sttRow")->applyFromArray([
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
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['name_room']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['code_role']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['fullname'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['code_staff']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", (!empty($value['phonenumber']) ? $value['phonenumber'] : ''))->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $value['resident'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", (!empty($value['birthday']) ? _dC($value['birthday']) : ''))->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $value['birthplace'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $value['domicile'])->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $value['cmnd_id_passport'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", _dC($value['date_range']))->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", $value['issued_by'])->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", $value['marital_status'])->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", $value['nationality'])->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", $value['nation'])->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin", $value['account_name'])->getStyle("Q$rowBegin")->getAlignment()->setWrapText(true);
                    $brank = [];
                    if(!empty($value['bank_account'])) {
                        $brank[] = $value['bank_account'];
                    }
                    if(!empty($value['branch_bank'])) {
                        $brank[] = $value['branch_bank'];
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin", implode(" - ", $brank))->getStyle("R$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("S$rowBegin", $value['date_probation'])->getStyle("S$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("T$rowBegin", $value['time_trial'])->getStyle("T$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("U$rowBegin", $value['code_contract_labor'])->getStyle("U$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("V$rowBegin", _dt($value['date_sign_contract']))->getStyle("V$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("W$rowBegin", _dt($value['date_end_contract']))->getStyle("W$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("X$rowBegin", _dt($value['date_sign']))->getStyle("X$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Y$rowBegin", number_format_data($value['salary_basic']))->getStyle("Y$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Z$rowBegin", number_format_data($value['salary_position']))->getStyle("Z$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AA$rowBegin", number_format_data($value['lapc']))->getStyle("AA$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AB$rowBegin", (!empty($value['salary_bhxh_new']) ? number_format_data($value['salary_bhxh_new']) : ''))->getStyle("AB$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AC$rowBegin", !empty($value['insurrance_book_number']) ? $value['insurrance_book_number'] : '')->getStyle("AC$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AD$rowBegin", number_format_data($value['total_vat']))->getStyle("AD$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AE$rowBegin", $value['personal_tax_code'])->getStyle("AE$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AF$rowBegin", (!empty($value['bhty']) ? number_format_data($value['bhty']) : ''))->getStyle("AF$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AG$rowBegin", $value['number_bhty'])->getStyle("AG$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AH$rowBegin", $value['number_leave_year'])->getStyle("AH$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AI$rowBegin", (!empty($value['seniority']) ? $value['seniority'] : ''))->getStyle("AI$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AJ$rowBegin", $value['active'])->getStyle("AJ$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AK$rowBegin", $value['inactive'])->getStyle("AK$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AL$rowBegin", _dt($value['datecreated']))->getStyle("AL$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AM$rowBegin", "")->getStyle("AM$rowBegin")->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:AM$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("H2:AM$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
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
            $filename = lang('thong_ke_nhan_vien') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AH')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AI')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AJ')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AK')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AL')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AM')->setWidth(25);
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

    public function machines() {
        $data['title']   = _l('Thống Kê Máy Móc');
        $this->load->view('admin/statistic/machines', $data);
    }

    public function getMachines() {
        $aColumns = [
            'tbl_machines.id as id',
            'tbl_category_machines.name as name_category',//mã nhóm thiết bị
            'tbl_machines.code as code_machines',//mã thiết bị
            'tbl_machines.name as name_machines',//tên thiết bị
            'tbl_machines.model as model',//Số model
            'tbl_machines.origin as origin',//Xuất xứ
            'tbl_machines.year_manu as year_manu',//năm xuất xứ
            'tblsuppliers.company as supplier', //nhà cung cấp

            'tbl_contracts_supplier.code as code_contract', //hợp đồng mua bán
            'tbl_contracts_supplier.date_start as date_signed', //ngày ký kết
            'tbl_contracts_supplier.date_of_receipt as date_of_receipt', //ngày tiếp nhận

            'tbl_machines.operating_gauge as operating_gauge',//khổ vận hành
            '(
                SELECT GROUP_CONCAT(tbl_machines_process.process SEPARATOR ",\n") 
                FROM tbl_machines_process 
                WHERE tbl_machines_process.machines_id = tbl_machines.id
            ) as process',//quy trình vận hành máy móc
            'tbl_packaging.code as code_standard',//tiêu chuẩn
            'tbl_machines.quota_productivity as quota_productivity',//Định Mức Năng Suất/h/Tháng
            'tbl_machines.preparation_time as preparation_time',//Định Mức Thời Gian Canh Bài
            'tbl_machines.soup_ingredients as soup_ingredients',//Định Mức NPL Canh Bài
            'tbl_categories_maintenance.code as bd_day', //Thẻ  Theo Dõi Bảo Dưỡng_Ngày
            'tbl_maintenance_calibration.code as code_hc', //Thẻ  Theo Dõi Hiệu Chuẩn
            'tbl_maintenance_calibration.deadline as deadline_hc', //Thời Gian Hiệu Chuẩn
            'tbl_maintenance_calibration.date_start as date_start_hc', //Ngày Hiệu Chuẩn
            'tbl_maintenance_calibration.date_end as date_end_hc', //Ngày tái tục
            'tbl_maintenance_calibration.grand_total as grand_total_hc', //ngân sách hiệu chuẩn
            'tbl_machines.time_change_size as time_change_size',//thời gian thay size
            'tbl_depreciable_assets.asset_value as price_machine',//giá trị
            'tbl_depreciable_assets.depreciation_period as depreciation_period',//thời gian khấu hao
            'tbl_depreciable_assets.date_depreciation as date_depreciation',//ngày bắt đầu khấu hao
            'tbl_depreciable_assets.asset_value - (LEAST(
                GREATEST(
                    TIMESTAMPDIFF(MONTH, tbl_depreciable_assets.date_depreciation, CURDATE()) / tbl_depreciable_assets.depreciation_period,
                    0
                ),
                1
            ) * tbl_depreciable_assets.asset_value) as money_depreciation_rates',//số tiền khấu hao
            '"" as residual_value',//giá trị còn lại
            'DATE_ADD(tbl_depreciable_assets.date_depreciation, INTERVAL tbl_depreciable_assets.depreciation_period MONTH) as date_remaining_day',//thời gian kết thúc khấu hao
            'IF(tbl_machines.status = "producing", "Đang Hoạt Động", "") as active',// trạng thái
            'IF(tbl_machines.status = "maintenance", "Ngừng Sửa Chữa", IF(tbl_machines.status = "damaged", "Tạm Ngừng Sửa Chữa", "")) as inactive',// ngưng sử dụng
            'tbl_machines.date_create as date_create',//ngày tạo
            'tbl_machines.date_update as date_update',//ngày điều chỉnh

        ];
        $where = [];
        if($this->input->post('start_date')) {
            $start_date = to_sql_date($this->input->post('start_date'));
            $where[] = 'AND DATE_FORMAT(tbl_machines.date_create, "%Y-%m-%d") >= "'.$start_date.'"';
        }

        if($this->input->post('end_date')) {
            $end_date = to_sql_date($this->input->post('end_date'));
            $where[] = 'AND DATE_FORMAT(tbl_machines.date_create, "%Y-%m-%d") <= "'.$end_date.'"';
        }
        $sIndexColumn = 'id';
        $sTable       = 'tbl_machines';
        $join = [
            'LEFT JOIN tbl_category_machines ON tbl_category_machines.id = tbl_machines.category_machine_id',
            'LEFT JOIN tbl_packaging ON tbl_packaging.id = tbl_machines.standard',
            'LEFT JOIN tbl_maintenance_calibration ON tbl_maintenance_calibration.machines_id = tbl_machines.id AND tbl_maintenance_calibration.type = 1',
            'LEFT JOIN tblsuppliers ON tblsuppliers.id = tbl_machines.supplier_id',
            'LEFT JOIN tbl_contracts_supplier ON tbl_contracts_supplier.supplier_id = tbl_machines.supplier_id',
            'LEFT JOIN tbl_categories_maintenance ON tbl_categories_maintenance.id_machines = tbl_machines.id',
            'LEFT JOIN tbl_depreciable_assets ON tbl_depreciable_assets.id_machines = tbl_machines.id AND tbl_depreciable_assets.id = 1',
        ];
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);
        $output       = $result['output'];
        $rResult      = $result['rResult'];
        foreach ($rResult as $key =>  $aRow) {
            $row = [];
            $row[] = ($this->input->post('start')) + $key + 1;
            $row[] = '<div class="pre">' . $aRow['name_category'] . '</div>';//mã nhóm thiết bị
            $row[] = '<div class="pre">' . $aRow['code_machines']. '</div>';//mã thiết bị
            $row[] = '<div class="pre">' . $aRow['name_machines']. '</div>';//tên thiết bị
            $row[] = $aRow['model'];//model
            $row[] = $aRow['origin'];//Xuất xứ
            $row[] = $aRow['year_manu'];//năm xuất xứ
            $row[] = '<div class="pre">' . $aRow['supplier']. '</div>';//nhà cung cấp
            $row[] = $aRow['code_contract'];//hợp đồng mua bán
            $row[] = _dC($aRow['date_signed']);//ngày ký kết
            $row[] = _dt($aRow['date_of_receipt']);//ngày tiếp nhận máy
            $row[] = $aRow['operating_gauge'];//khổ vận hành
            $row[] = '<div class="process pre" style="white-space: pre;">' . $aRow['process'] . '</div>';//quy trình vận hành máy móc
            $row[] = '<div class="code_standard  text-center">' . $aRow['code_standard'].'</div>';//tiêu chuẩn
            $row[] = '<div class="quota_productivity text-center">' . number_format_data($aRow['quota_productivity']) . '</div>';//Định Mức Năng Suất/h/Tháng
            $row[] = '<div class="text-center">' . $aRow['preparation_time'] . '</div>';//định mức canh bài
            $row[] = !empty($aRow['soup_ingredients']) ? $aRow['soup_ingredients'] : '';//canh bài
            $row[] = $aRow['bd_day'];//Thẻ Theo Dõi Bảo Dưỡng_Ngày
            $row[] = $aRow['code_hc'];//Thẻ Theo Dõi Hiệu Chuẩn
            $row[] = '<div class="text-center">' . $aRow['deadline_hc'] . '</div>';//Thời Gian Hiệu Chuẩn
            $row[] = _dC($aRow['date_start_hc']);//Ngày Hiệu Chuẩn
            $row[] = _dC($aRow['date_end_hc']);//Ngày tái tục
            $row[] = !empty($aRow['grand_total_hc']) ? number_format_data($aRow['grand_total_hc']) : '';//ngân sách hiệu chuẩn
            $row[] = !empty($aRow['time_change_size']) ? $aRow['time_change_size'] : '';//thời gian thay size
            $row[] = '<div class="price_machine">' . number_format_data($aRow['price_machine']).'</div>';//tổng giá trị
            $row[] = '<div class="text-center">' . $aRow['depreciation_period'] . '</div>';//thời gian khấu hao
            $row[] = _d($aRow['date_depreciation']);//ngày bắt đầu khấu hao
            $row[] = number_format_data($aRow['money_depreciation_rates']);//số tiền khấu hao
            $row[] = '<div class="text-right">' . number_format_data($aRow['price_machine'] - $aRow['money_depreciation_rates']) . '</div>';//giá trị còn lại
            $row[] = _d($aRow['date_remaining_day']);//thời gian kết thúc khấu hao

            $row[] = $aRow['active']; //Trạng thái
            $row[] = $aRow['inactive']; //Ngưng sử dụng
            $row[] = _dt($aRow['date_create']); //Ngày tạo
            $row[] = !empty($aRow['date_update']) ? _dt($aRow['date_update']) : ''; //Ngày điều chỉnh
            $output['aaData'][] = $row;
        }
        echo json_encode($output);die();
    }


    public function getMachinesExcel(){
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();

            if($this->input->post('start_date_search')) {
                $start_date = to_sql_date($this->input->post('start_date_search'));
                $this->db->where('DATE_FORMAT(tbl_machines.date_create, "%Y-%m-%d") >= "'.$start_date.'"');
            }

            if($this->input->post('end_date_search')) {
                $end_date = to_sql_date($this->input->post('end_date_search'));
                $this->db->where('DATE_FORMAT(tbl_machines.date_create, "%Y-%m-%d") <= "'.$end_date.'"');
            }

            $this->db->select([
                'tbl_machines.id as id',
                'tbl_category_machines.name as name_category',//mã nhóm thiết bị
                'tbl_machines.code as code_machines',//mã thiết bị
                'tbl_machines.name as name_machines',//tên thiết bị
                'tbl_machines.model as model',//Số model
                'tbl_machines.origin as origin',//Xuất xứ
                'tbl_machines.year_manu as year_manu',//năm xuất xứ
                'tblsuppliers.company as supplier', //nhà cung cấp

                'tbl_contracts_supplier.code as code_contract', //hợp đồng mua bán
                'tbl_contracts_supplier.date_start as date_signed', //ngày ký kết
                'tbl_contracts_supplier.date_of_receipt as date_of_receipt', //ngày tiếp nhận

                'tbl_machines.operating_gauge as operating_gauge',//khổ vận hành
                '(
                SELECT GROUP_CONCAT(tbl_machines_process.process SEPARATOR ",\n") 
                FROM tbl_machines_process 
                WHERE tbl_machines_process.machines_id = tbl_machines.id
            ) as process',//quy trình vận hành máy móc
                'tbl_packaging.code as code_standard',//tiêu chuẩn
                'tbl_machines.quota_productivity as quota_productivity',//Định Mức Năng Suất/h/Tháng
                'tbl_machines.preparation_time as preparation_time',//Định Mức Thời Gian Canh Bài
                'tbl_machines.soup_ingredients as soup_ingredients',//Định Mức NPL Canh Bài
                'tbl_categories_maintenance.code as bd_day', //Thẻ  Theo Dõi Bảo Dưỡng_Ngày
                'tbl_maintenance_calibration.code as code_hc', //Thẻ  Theo Dõi Hiệu Chuẩn
                'tbl_maintenance_calibration.deadline as deadline_hc', //Thời Gian Hiệu Chuẩn
                'tbl_maintenance_calibration.date_start as date_start_hc', //Ngày Hiệu Chuẩn
                'tbl_maintenance_calibration.date_end as date_end_hc', //Ngày tái tục
                'tbl_maintenance_calibration.grand_total as grand_total_hc', //ngân sách hiệu chuẩn
                'tbl_machines.time_change_size as time_change_size',//thời gian thay size
                'tbl_depreciable_assets.asset_value as price_machine',//giá trị
                'tbl_depreciable_assets.depreciation_period as depreciation_period',//thời gian khấu hao
                'tbl_depreciable_assets.date_depreciation as date_depreciation',//ngày bắt đầu khấu hao
                'tbl_depreciable_assets.asset_value - (LEAST(
                GREATEST(
                    TIMESTAMPDIFF(MONTH, tbl_depreciable_assets.date_depreciation, CURDATE()) / tbl_depreciable_assets.depreciation_period,
                    0
                ),
                1
            ) * tbl_depreciable_assets.asset_value) as money_depreciation_rates',//số tiền khấu hao
                '"" as residual_value',//giá trị còn lại
                'DATE_ADD(tbl_depreciable_assets.date_depreciation, INTERVAL tbl_depreciable_assets.depreciation_period MONTH) as date_remaining_day',//thời gian kết thúc khấu hao
                'IF(tbl_machines.status = "producing", "Đang Hoạt Động", "") as active',// trạng thái
                'IF(tbl_machines.status = "maintenance", "Ngừng Sửa Chữa", IF(tbl_machines.status = "damaged", "Tạm Ngừng Sửa Chữa", "")) as inactive',// ngưng sử dụng
                'tbl_machines.date_create as date_create',//ngày tạo
                'tbl_machines.date_update as date_update',//ngày điều chỉnh

            ]);
            $this->db->from('tbl_machines');
            $this->db->join('tbl_category_machines','tbl_category_machines.id = tbl_machines.category_machine_id','left');
            $this->db->join('tbl_packaging','tbl_packaging.id = tbl_machines.standard','left');
            $this->db->join('tbl_maintenance_calibration','tbl_maintenance_calibration.machines_id = tbl_machines.id AND tbl_maintenance_calibration.type = 1','left');
            $this->db->join('tblsuppliers','tblsuppliers.id = tbl_machines.supplier_id','left');
            $this->db->join('tbl_contracts_supplier','tbl_contracts_supplier.supplier_id = tbl_machines.supplier_id','left');
            $this->db->join('tbl_categories_maintenance','tbl_categories_maintenance.id_machines = tbl_machines.id','left');
            $this->db->join('tbl_depreciable_assets','tbl_depreciable_assets.id_machines = tbl_machines.id','left');

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
            $titleAppend = '';
            if(!empty($start_date_search) && !empty($end_date_search)){
                $titleAppend = 'Từ ngày ' . _d($start_date_search) . ' đến ngày ' . _d($end_date_search);
            }
            $objPHPExcel->getActiveSheet()->setCellValue('A1',
                ('THỐNG KÊ MÁY MÓC') . $titleAppend)->getStyle("A1")->applyFromArray([
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
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Nhóm Thiết Bị');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Mã Máy Móc, Thiết Bị');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Tên Máy Móc, Thiết Bị');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Số Model');
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Xuất Xứ')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Năm Sản Xuất')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Nhà Cung Cấp')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Hợp Đồng Mua Bán')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Ngày Ký Kết')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Ngày Tiếp Nhận Máy')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Khổ Vận Hành')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$sttRow.'', 'Quy Trình Vận Hành Máy Móc')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$sttRow.'', 'Tiêu Chuẩn')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$sttRow.'', 'Định Mức Năng Suất/h/Tháng')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$sttRow.'', 'Định Mức Thời Gian Canh Bài')->getStyle("P$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$sttRow.'', 'Định Mức NPL Canh Bài')->getStyle("Q$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('R'.$sttRow.'', 'Thẻ Theo Dõi Bảo Dưỡng_Ngày')->getStyle("R$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('S'.$sttRow.'', 'Thẻ Theo Dõi Hiệu Chuẩn')->getStyle("S$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('T'.$sttRow.'', 'Thời Gian Hiệu Chuẩn')->getStyle("T$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('U'.$sttRow.'', 'Ngày Hiệu Chuẩn')->getStyle("U$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('V'.$sttRow.'', 'Ngày Tái Tục')->getStyle("V$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('W'.$sttRow.'', 'Ngân Sách Hiệu Chuẩn')->getStyle("W$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('X'.$sttRow.'', 'Thời Gian Thay Size')->getStyle("X$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Y'.$sttRow.'', 'Tổng Giá Trị')->getStyle("Y$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Z'.$sttRow.'', 'Thời Gian Khấu Hao')->getStyle("Z$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AA'.$sttRow.'', 'Ngày Bắt Đầu Khấu Hao')->getStyle("AA$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AB'.$sttRow.'', 'Số Tiền Khấu Hao')->getStyle("AB$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AC'.$sttRow.'', 'Giá Trị Còn Lại')->getStyle("AC$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AD'.$sttRow.'', 'Thời Gian Kết Thúc Khấu Hao')->getStyle("AD$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AE'.$sttRow.'', 'Đang Hoạt Động')->getStyle("AE$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AF'.$sttRow.'', 'Ngưng Sử Dụng')->getStyle("AF$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AG'.$sttRow.'', 'Ngày Tạo')->getStyle("AG$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AH'.$sttRow.'', 'Ngày Điều Chỉnh')->getStyle("AH$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:AH$sttRow")->applyFromArray([
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
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['name_category']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['code_machines']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['name_machines'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['model']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", (!empty($value['origin']) ? $value['origin'] : ''))->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $value['year_manu'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", (!empty($value['supplier']) ? ($value['supplier']) : ''))->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $value['code_contract'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", _dC($value['date_signed']))->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", _dt($value['date_of_receipt']))->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $value['operating_gauge'])->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", $value['process'])->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", $value['code_standard'])->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", number_format_data($value['quota_productivity']))->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", $value['preparation_time'])->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin", (!empty($value['soup_ingredients']) ? $value['soup_ingredients'] : ''))->getStyle("Q$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin", $value['bd_day'])->getStyle("R$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("S$rowBegin", $value['code_hc'])->getStyle("S$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("T$rowBegin", $value['deadline_hc'])->getStyle("T$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("U$rowBegin", _dC($value['date_start_hc']))->getStyle("U$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("V$rowBegin", _dC($value['date_end_hc']))->getStyle("V$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("W$rowBegin", number_format_data($value['grand_total_hc']))->getStyle("W$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("X$rowBegin", $value['time_change_size'])->getStyle("X$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Y$rowBegin", number_format_data($value['price_machine']))->getStyle("Y$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Z$rowBegin", $value['depreciation_period'])->getStyle("Z$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AA$rowBegin", _d($value['date_depreciation']))->getStyle("AA$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AB$rowBegin", (!empty($value['money_depreciation_rates']) ? number_format_data($value['money_depreciation_rates']) : ''))->getStyle("AB$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AC$rowBegin", (number_format_data($value['price_machine'] - $value['money_depreciation_rates'])))->getStyle("AC$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AD$rowBegin", _d($value['date_remaining_day']))->getStyle("AD$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AE$rowBegin", $value['active'])->getStyle("AE$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AF$rowBegin", $value['inactive'])->getStyle("AF$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AG$rowBegin", _dt($value['date_create']))->getStyle("AG$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AH$rowBegin", (!empty($value['date_update']) ? _dt($value['date_update']) : ''))->getStyle("AH$rowBegin")->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:AH$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("H2:AH$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
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
            $filename = lang('thong_ke_may_moc') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AH')->setWidth(25);
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
