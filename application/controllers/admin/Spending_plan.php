<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Spending_plan extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('spending_plan_model');

        $this->arrGroupPlan = [
            'date' => 'Ngày',
            'week' => 'Tuần',
            'month' => 'Tháng',
            'year' => 'Năm'
        ];
    }

    public function index() {
        if ($this->input->is_ajax_request()) {
            $data = [];
            $data['arrGroupPlan'] = $this->arrGroupPlan;
            $this->app->get_table_data('spending_plan', $data);
        }

        $data['title'] = _l('spending_plan');
        $this->load->view('admin/spending_plan/manage', $data);
    }

    public function submit($id = '') {
        if ($this->input->post()) {
            $formData = $this->input->post();

            $result = $this->spending_plan_model->submit($formData, $id);
            if (!empty($result['submitId'])) {
                echo json_encode(['success'=>true, 'alert_type'=>'success', 'message'=>_l('Thành công')]);
            } else {
                echo json_encode(['success'=>false, 'alert_type'=>'danger', 'message'=>_l('Thất bại')]);
            }
        } else {
            $data['title'] = '';
            $data['arrGroupPlan'] = [];
            foreach ($this->arrGroupPlan as $groupCode => $group) {
                $data['arrGroupPlan'][] = ['code' => $groupCode, 'name' => $group];
            }

            $data['arrStaff'] = [];
            $staff = get_table_where('tblstaff',array('active'=>1));
            $arrStaff = array();
            foreach ($staff as $key => $value) {
                $arrStaff[$key]['id'] = $value['staffid'];
                $arrStaff[$key]['full_name'] = $value['firstname'].' '.$value['lastname'];
            }
            $data['arrStaff'] = $arrStaff;

            $data['arrPaymentMethod'] = get_table_where('tblpayment_modes',array('active'=>1));
            $data['arrTax'] = get_table_where('tbltaxes');
            $data['arrCurrency'] = get_table_where('tblcurrencies');

            if (!empty($id)) {
                $data['value'] = get_table_where('tblspending_plan', ['id'=>$id], '', 'row_array');
                $data['id'] = $id;
            }

            $this->load->view('admin/spending_plan/submit', $data);
        }
    }

    function delete($id) {
        $result = $this->spending_plan_model->delete($id);
		if ($result['isSuccess']) {
			$response['result'] = 1;
			$response['message'] = $result['message'];
		} else {
			$response['result'] = 0;
			$response['message'] = $result['message'];
		}

		echo json_encode($response);
    }

    public function export_excel() {
        $search_date = $this->input->post('search_date');

        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
        $objPHPExcel->getDefaultStyle()->applyFromArray([
            'font' => array(
                'name'  => 'Times New Roman'
            ),
        ]);

        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');

        $this->db->select('
            tblspending_plan.id as id,
            tblspending_plan.code as code,
            tblspending_plan.date as date,
            tblspending_plan.group_plan as group_plan,
            tblspending_plan.detail as detail,
            CONCAT(tb_create.firstname, " ", tb_create.lastname) as create_by,
            tblspending_plan.receiver as receiver,
            CONCAT(tb_approve_staff.firstname, " ", tb_approve_staff.lastname) as approve_staff,
            CONCAT(tb_spending_staff.firstname, " ", tb_spending_staff.lastname) as spending_staff,
            tblspending_plan.price as price,
            tblspending_plan.tax_rate as tax_rate,
            tblspending_plan.amount as amount,
            tblpayment_modes.name as payment_method,
            tblcurrencies.name as currency,
            tblspending_plan.exchange_rate as exchange_rate,
            tblspending_plan.category_spend as category_spend,
            tblspending_plan.expense as expense,
            tblspending_plan.deadline as deadline,
		');
        $this->db->from('tblspending_plan');
        $this->db->join('tblstaff tb_create', 'tb_create.staffid = tblspending_plan.create_by', 'left');
        $this->db->join('tblstaff tb_approve_staff', 'tb_approve_staff.staffid = tblspending_plan.approve_staff_id', 'left');
        $this->db->join('tblstaff tb_spending_staff', 'tb_spending_staff.staffid = tblspending_plan.spending_staff_id', 'left');
        $this->db->join('tblpayment_modes', 'tblpayment_modes.id = tblspending_plan.payment_method_id', 'left');
        $this->db->join('tblcurrencies', 'tblcurrencies.id = tblspending_plan.currency_id', 'left');

        if (!empty($search_date)) {
            $searchDate = explode(' - ', $search_date);
            $this->db->where('tblspending_plan.date >=' , to_sql_date($searchDate[0]).' 00:00:00');
            $this->db->where('tblspending_plan.date <=' , to_sql_date($searchDate[1]).' 23:59:59');
        }

        $this->db->group_by('tblspending_plan.id');
        $this->db->order_by('tblspending_plan.id', 'desc');

        $result = $this->db->get()->result_array();

        $styleTitle = [
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font' => array(
                'bold' => true,
                'size' => 18,
                'name' => 'Times New Roman'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ];

        $styleHeader = [
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font' => array(
                // 'bold' => true,
                // 'color' => array('rgb' => '111112'),
                'size' => 12,
                'name' => 'Times New Roman'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '4BACC6'),
                'size' => 12,
                // 'bold' => true
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ];

        $stylePlain = [
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font' => array(
                // 'bold' => false,
                // 'color' => array('rgb' => '111112'),
                'size' => 11,
                'name' => 'Times New Roman'
            ),
        ];

        $headerFillColor = [
            'A' => array('rgb' => '92D050'),
        ];

        $cloumns_excel = cloumns_excel();
        $colName = [
            'code' => ucwords(_l('Mã số phiếu')),
            'date' => ucwords(_l('Ngày lập phiếu')),
            'group_plan' => ucwords(_l('Nhóm kế hoạch')),
            'detail' => ucwords(_l('tnh_detail')),
            'create_by' => ucwords(_l('Người lập')),
            'receiver' => ucwords(_l('Người tiếp nhận')),
            'approve_staff' => ucwords(_l('Người duyệt')),
            'spending_staff' => ucwords(_l('Người chi')),
            'price' => ucwords(_l('Số tiền chi')),
            'tax_rate' => (_l('Thuế VAT')),
            'amount' => ucwords(_l('Tổng tiền (VNĐ)')),
            'payment_method' => ucwords(_l('Hình thức thanh toán')),
            'currency' => ucwords(_l('Tiền tệ')),
            'exchange_rate' => ucwords(_l('Tỷ giá')),
            'category_spend' => ucwords(_l('Mục chi')),
            'expense' => ucwords(_l('Khoản chi')),
            'deadline' => ucwords(_l('Thời hạn hoàn thành')),
            'qr' => ('QR'),
        ];
        $aColumns = array_keys($colName);

        $excelRowNum = 1;
        $maxCol = count($colName) - 1;
        $objPHPExcel->getActiveSheet()->mergeCells('A'.($excelRowNum).':'.$cloumns_excel[$maxCol].$excelRowNum);
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$excelRowNum, ('PHIẾU KẾ HOẠCH CHI'))->getStyle("A".$excelRowNum)->applyFromArray($styleTitle);
        // $objPHPExcel->getActiveSheet()->freezePane('A1');
        
        $excelRowNum = 2;
        foreach ($aColumns as $key => $value) {
            foreach($headerFillColor as $colIndex => $color) {
                if ($cloumns_excel[$key] == $colIndex) {
                    $styleHeader['fill']['color'] = $color;
                    unset($headerFillColor[$colIndex]);
                    break;
                }
            }
            $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$key] . $excelRowNum, ($colName[$value]))->getStyle($cloumns_excel[$key] . ($excelRowNum))->applyFromArray($styleHeader);
            $objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$key])->setAutoSize(true);
        }

        $excelRowNum = 3;
        foreach ($result as $key => $aRow) {
			$aRow['id'] = ($key+1);

            foreach ($aColumns as $colIndex => $colCode) {
                if (str_contains($colCode, 'date')) {
                    $cellValue = (isset($aRow[$colCode]) ? _d($aRow[$colCode]) : '');
                } else if ($colCode == 'deadline') {
                    $cellValue = (isset($aRow[$colCode]) ? _d($aRow[$colCode]) : '');
                } else if ($colCode == 'group_plan') {
                    $cellValue = $this->arrGroupPlan[$aRow[$colCode]] ?? '';
                } else {
                    $cellValue = (isset($aRow[$colCode]) ? $aRow[$colCode] : '');
                }

                $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$colIndex] . $excelRowNum, $cellValue)->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain);
            }
            $excelRowNum++;
        }

        $filename = 'Phieu_ke_hoach_chi' . '.xls';
        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="$filename"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        $response =  array(
            'result' => 1,
            'message' => lang('success'),
            'filename' => $filename,
            'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        );
        die(json_encode($response));
    }
}
