<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Yct extends AdminController
{
    function __construct()
    {
        parent::__construct();
        // $this->perViewBusinessPlan = has_permission('business_plan', '', 'view');
        // $this->load->model('business_plan_model');
        $this->load->model('products_model');
        $this->load->model('quotes_model');
        $this->load->model('unit_model');
        $this->perPrintQuotes = has_permission('quotes', '', 'print');

    }
    public function export_machines()
    {
        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        // print_arrays($this->input->post());
        // $cloumns = $this->input->post('cloumns');
        $style_excel = style_excel();
        // $cloumns_excel = cloumns_excel();
        
        $category_hand_over_search = $this->input->post('category_hand_over_search');

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);


        $numberRow = 2;
        $objPHPExcel->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);

        $objPHPExcel->getActiveSheet()->SetCellValue("A$numberRow", 'STT')->getStyle("A$numberRow")->applyFromArray($style_excel['Background_header']);
        $objPHPExcel->getActiveSheet()->SetCellValue("B$numberRow", 'Công đoạn')->getStyle("B$numberRow")->applyFromArray($style_excel['Background_header']);
        $objPHPExcel->getActiveSheet()->SetCellValue("C$numberRow", 'Mã loại bàn giao')->getStyle("C$numberRow")->applyFromArray($style_excel['Background_header']);
        $objPHPExcel->getActiveSheet()->SetCellValue("D$numberRow", 'Tên loại bàn giao')->getStyle("D$numberRow")->applyFromArray($style_excel['Background_header']);
        $objPHPExcel->getActiveSheet()->SetCellValue("E$numberRow", 'Tên tiêu chí bàn giao')->getStyle("E$numberRow")->applyFromArray($style_excel['Background_header']);
        $objPHPExcel->getActiveSheet()->SetCellValue("F$numberRow", 'Tiêu chuẩn')->getStyle("F$numberRow")->applyFromArray($style_excel['Background_header']);
        $objPHPExcel->getActiveSheet()->SetCellValue("G$numberRow", 'Phương pháp')->getStyle("G$numberRow")->applyFromArray($style_excel['Background_header']);
        $numberRow++;

        $stt = 1;
        $this->db->select('
                            tbl_stages.code as code_stage,
                            tbl_category_hand_over.code as category_hand_over_code,
                            tbl_category_hand_over.name as category_hand_over_name,
                            tbl_hand_over_task.name as name,
                            tbl_packaging.code as code_standard,
                            tbl_hand_over_task.method as method');
        $this->db->join('tbl_category_hand_over', 'tbl_category_hand_over.id = tbl_hand_over_task.category_hand_over_id', 'inner');
        $this->db->join('tbl_packaging', 'tbl_packaging.id = tbl_hand_over_task.standard', 'left');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_hand_over_task.id_stage', 'left');
        if (!empty($category_hand_over_search)) {
            $this->db->where('tbl_hand_over_task.category_hand_over_id', $category_hand_over_search);
		}
        $hand_over_task = $this->db->get('tbl_hand_over_task')->result_array();
        
        if (!empty($hand_over_task)) {
            foreach ($hand_over_task as $key => $value) {
                $code_stage = $value['code_stage'];
                $category_hand_over_code = $value['category_hand_over_code'];
                $category_hand_over_name = $value['category_hand_over_name'];
                $name = $value['name'];
                $code_standard = $value['code_standard'];
                $method = $value['method'];

                $objPHPExcel->getActiveSheet()->SetCellValue("A$numberRow", $stt)->getStyle("A$numberRow")->applyFromArray($style_excel['BStyle_center']);
                $objPHPExcel->getActiveSheet()->SetCellValue("B$numberRow", $code_stage)->getStyle("B$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->SetCellValue("C$numberRow", $category_hand_over_code)->getStyle("C$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->SetCellValue("D$numberRow", $category_hand_over_name)->getStyle("D$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->SetCellValue("E$numberRow", $name)->getStyle("E$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->SetCellValue("F$numberRow", $code_standard)->getStyle("F$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->SetCellValue("G$numberRow", $method)->getStyle("G$numberRow")->applyFromArray($style_excel['BStyle_left']);
                
                $numberRow++;
                $stt++;
            }
        }


        $filename = lang('Tieu_chi_ban_giao') . '.xls';
        $objPHPExcel->getActiveSheet()->freezePane('A1');

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function staff_departments($id)
    {
        $this->load->model('departments_model');
        $staff_departments = $this->departments_model->get_staff_departments($id);
        var_dump($staff_departments);
    }

    public function test ()
    {
        var_dump(empty("1"));
    }

    public function print_pdf($id, $type_pdf = 'I')
	{
		if (!$this->perPrintQuotes) {
			accessDenied();
		}
		ob_end_clean();
		$data = [];
		$quote = $this->quotes_model->rowQuotesById($id);
		$address_delivery = $this->site_model->rowShippingClient($quote['address_delivery_id']);
		$area = $this->site_model->rowDeliveryArea($address_delivery['city_shipping'], $address_delivery['district_shipping']);
		$companyCustomer = '';
		$addressCompany = '';
		$emailCompany = '';

		$personContact = '';
		$phoneContact = '';
		$emailContact = '';
		$customer = $this->clients_model->rowCustomer($quote['customer_id']);
		$codeCustomer = $customer['zcode'];
		$companyCustomer = $customer['company_short'];

		$contact = $this->site_model->rowContact($quote['person_contact_id']);
        // var_dump($contact);die;
		$personContact = $contact['firstname'];
		$phoneContact = $contact['phonenumber'];
		$emailContact = $contact['email'];

		$created_by = get_staff_full_name($quote['created_by']);
        $staff_create = get_table_where('tblstaff', ['staffid'=>$quote['created_by']], '', 'row_array');


		if (!empty($quote['employee_id'])) {
			$staff = $this->site_model->rowStaffById($quote['employee_id']);
		}

        $this->db->select('moq, moq_to');
		$this->db->from('tbl_quote_items');
		$this->db->where('quote_id', $id);
		$this->db->order_by('moq', 'asc');
		$this->db->order_by('moq_to', 'asc');
        $this->db->group_by('moq, moq_to');
		$moqThData =  $this->db->get()->result_array();
        // echo '<pre>';var_dump($moqThData);die;
        $moqTh = ''; $moqTh2 = '';
        foreach ($moqThData as $key => $value) {
            if ($value['moq'] % 1000 == 0) {
                $moq = ($value['moq'] / 1000) . 'K';
            } else {
                $moq = $value['moq'];
            }
            
            if ($value['moq_to'] % 1000 == 0) {
                $moq_to = ($value['moq_to'] / 1000) . 'K';
            } else {
                $moq_to = $value['moq_to'];
            }

            $moqTh .= '<th style="font-weight: nomal" rowspan="1" colspan="3">MOQ '.$moq.' - '.$moq_to.'</th>';
            $moqTh2 .= '<th>'.$codeCustomer.'</th><th>Thành Danh</th><th>%</th>';
        }

		$data['title'] = lang('tnh_quotes');
		$data['type'] = 'L';
		$data['img'] = '';

		$tHead = '<tr nobr="true" class="text-center bold" style="font-size: 10px">
            <th rowspan="2" style=""><span>' . _l('STT') . '</span></th>
            <th rowspan="2" style=""><span>"BRAND<br>(Nhãn Hiệu)"</span></th>
            <th rowspan="2" style=""><span>Tên Gọi Khách Hàng</span></th>
            <th rowspan="2" style=""><span>Item Code-Giá Khách Hàng</span></th>
            <th rowspan="2" style=""><span>Photo<br>(Hình ảnh)</span></th>
            <th rowspan="2" style=""><span>Flat Size<br>(Kích thước) (mm)</span></th>
            <th rowspan="2" style=""><span>Thanh Danh item code<br>(Mã Thành Danh)</span></th>
            <th rowspan="2" style=""><span>Thanh Danh item name<br>(Tên Thành Danh)</span></th>
            <th rowspan="2" style=""><span>UoM<br>(Đơn vị tính)</span></th>'
            .$moqTh.
            '<th rowspan="2" style=""><span>Leadtime<br>(thời gian xử lý)</span></th>
            <th rowspan="2" style=""><span>Yêu cầu đặc biệt</span></th>
        </tr>
        <tr nobr="true" class="text-center bold" style="font-size: 10px">
            '.$moqTh2.'
        </tr>
        ';

		$bodyItems = '';
		$this->db->select('*');
		$this->db->from('tbl_quote_items');
		$this->db->where('quote_id', $id);
		$this->db->order_by('type_item', 'desc');
		$this->db->order_by('item_id', 'desc');
        $this->db->group_by('item_id, type_item, technical_explanation, note_item');
		$items =  $this->db->get()->result_array();
        // echo '<pre>';var_dump($items);die;

		if (!empty($items)) {
			$items_id_current = $items[0]['item_id'];
			$rowspan = 0;
			$arrItems = [];
			$stt = 1;
			$arrKey = 0;
			foreach ($items as $key => $value) {
				$type_item = $value['type_item'];
				$items_id = $value['item_id'];

				$info = $this->products_model->rowProduct($items_id);
                // var_dump($info);die;
				$unit = $this->unit_model->rowUnit($info['unit_id']);

				if ($items_id_current != $items_id) {
					foreach($arrItems as $itemKey => $itemValue) {
						// if ($itemKey == 0) {
                            $moqTd = '';
                            if (!empty($moqThData)) {
                                foreach ($moqThData as $moqThDataValue) {
                                    $this->db->select('*');
                                    $this->db->from('tbl_quote_items');
                                    $this->db->where('quote_id', $id);
                                    $this->db->where('item_id', $value['item_id']);
                                    $this->db->where('type_item', $value['type_item']);
                                    $this->db->where('moq', $moqThDataValue['moq']);
                                    $this->db->where('moq_to', $moqThDataValue['moq_to']);
                                    $itemMoq =  $this->db->get()->row_array();
                                    
                                    $arrItemMoq['tdMOQ'] = '<td class="text-center" style="width: 9%; font-size: 11px;">' . formatNumber($itemMoq['moq']) . ' - ' . formatNumber($itemMoq['moq_to']) . '</td>';
    
                                    $arrItemMoq['tdPrice'] = '<td class="text-center" style="width: 8%; font-size: 11px;">' . formatNumber($itemMoq['unit_price']) . '</td>';
    
                                    $unit_price_discount = $itemMoq['unit_price'];
                                    if ($itemMoq['discount_precent_item'] > 0) {
                                        $unit_price_discount = $unit_price_discount - $unit_price_discount * $itemMoq['discount_precent_item']/100;
                                    }
                                    $arrItemMoq['tdPriceDiscount'] = '<td class="text-center" style="font-size: 11px;">' . formatNumber($unit_price_discount, 0) . '</td>';
                                    $arrItemMoq['tdPriceCurrency'] = '<td class="text-center" style="width: 8%; font-size: 11px;">' . formatNumber($itemMoq['unit_price']/$quote['amount_to_vnd']) . '</td>';
                                    $arrItemMoq['tdPriceCurrencyDiscount'] = '<td class="text-center" style="">' . formatNumber($unit_price_discount/$quote['amount_to_vnd']) . '</td>';
    
                                    $arrItemMoq['tdDiscount'] = '<td class="text-center" style="">' . formatNumber($itemMoq['discount_precent_item']) . '</td>';

                                    $moqTd .= $arrItemMoq['tdPriceDiscount'].$arrItemMoq['tdPriceCurrencyDiscount'].$arrItemMoq['tdDiscount'];
                                }
                            }
							
                                $bodyItems .= '<tr nobr="true" style="font-size: 10px">
                                ' . $tdNumber . '
                                ' . $tdBrand . '
                                <td></td>
                                <td></td>
                                '.$tdImages.'
                                '.$tdSize.'
                                ' . $tdCode . '
                                ' . $tdCode . '
                                ' . $tdUnit . '
                                '.$moqTd.'
                                ' . $itemValue['tdLeadTime'] . '
                                ' . $tdDescription . '
                                '
                            .'</tr>';
					}
					$arrItems = array();
					$rowspan = 0;
					$stt++;
					$items_id_current = $items_id;
					$arrKey = 0;
				}

				// $rowspan++;
				$tdNumber = '<td rowspan="'.$rowspan.'" class="text-center" style="">' . ($stt) . '</td>';
				$tdCode = '<td rowspan="'.$rowspan.'" style="" class="text-left">' . $info['code'] . '</td>';
				$tdDescription = '<td rowspan="'.$rowspan.'" style="" class="text-center">'.$value['technical_explanation'].'</td>';
				$tdBrand = '<td rowspan="'.$rowspan.'" style="" class="text-center"></td>';
				$tdUnit = '<td rowspan="'.$rowspan.'" class="text-center" style="">' . $unit['unit'] . '</td>';

				$arrItems[$arrKey]['tdMOQ'] = '<td class="text-center" style=" font-size: 11px;">' . formatNumber($value['moq']) . ' - ' . formatNumber($value['moq_to']) . '</td>';

				$arrItems[$arrKey]['tdPrice'] = '<td class="text-center" style=" font-size: 11px;">' . formatNumber($value['unit_price']) . '</td>';
                
                if (!empty($info['images'])) {
                    $imgSrc = base_url('uploads/products/'.$info['images']);
                    if (!file_exists($imgSrc)){
                        $imgSrc = base_url('assets/images/tnh/no_image.png');
                    }
                    $tdImages = '<td><img src="'.$imgSrc.'"></td>';
                } else {
                    $tdImages = '<td><img src="'.base_url('assets/images/tnh/no_image.png').'"></td>';
                }
                $tdSize = '<td class="text-center">'.$info['size'].'</td>';
                $unit_price_discount = $value['unit_price'];
                if ($value['discount_precent_item'] > 0) {
                    $unit_price_discount = $unit_price_discount - $unit_price_discount * $value['discount_precent_item']/100;
                }
                $arrItems[$arrKey]['tdPriceDiscount'] = '<td class="text-center" style=" font-size: 11px;">' . formatNumber($unit_price_discount, 0) . '</td>';
				$arrItems[$arrKey]['tdPriceCurrency'] = '<td class="text-center" style=" font-size: 11px;">' . formatNumber($value['unit_price']/$quote['amount_to_vnd']) . '</td>';
				$arrItems[$arrKey]['tdPriceCurrencyDiscount'] = '<td class="text-center" style=" font-size: 11px;">' . formatNumber($unit_price_discount/$quote['amount_to_vnd']) . '</td>';

				$arrItems[$arrKey]['tdDiscount'] = '<td class="text-center" style=" font-size: 11px;">' . formatNumber($value['discount_precent_item']) . '</td>';
				$arrItems[$arrKey]['tdLeadTime'] = '<td class="text-center" style=" font-size: 11px;">' . formatNumber($value['lead_time']) . '</td>';
				$arrKey++;

			}
		}
		foreach($arrItems as $itemKey => $itemValue) {
                $bodyItems .= '<tr nobr="true">
                                ' . $tdNumber . '
                                ' . $tdBrand . '
                                <td></td>
                                <td></td>
                                '.$tdImages.'
                                '.$tdSize.'
                                ' . $tdCode . '
                                ' . $tdCode . '
                                ' . $tdUnit . '
                                '.$moqTd.'
                                ' . $itemValue['tdLeadTime'] . '
                                ' . $tdDescription . '
                                '
                            .'</tr>';
		}
		$trTotal = '<tr class="bold">
            <td colspan="2" class="text-center">TỔNG CỘNG</td>
            <td></td>
            <td class="text-center">' . formatNumber($quote['total_quantity']) . '</td>
            <td></td>
            <td></td>
            <td class="text-right"></td>
            <td></td>
        </tr>';

		$trVat = '<tr class="bold">
            <td colspan="2" class="text-center">THUẾ</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-right">' . formatMoney($quote['total_tax']) . ' VNĐ</td>
            <td></td>
        </tr>';


		$grandTotal = $quote['grand_total'];
		$trGrandTotal = '<tr class="bold">
            <td colspan="2" class="text-center">THÀNH TIỀN</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-right">' . formatMoney($grandTotal) . ' VNĐ</td>
            <td></td>
            <td></td>
        </tr>';

		$trWordPayment =  '<tr class="bold">
            <td colspan="11">Số tiền bằng chữ: ' . ucfirst(convert_number_to_words($grandTotal)) . '</td>
        </tr>';

		$day = date('d');
		$month = date('m');
		$year = date('Y');
		$message = "";

		ob_start();
		stylePdf();
		$company_logo = get_option('company_logo');
		$img = base_url('uploads/company/'.$company_logo);

		// Thanh Danh 3D Printing Co.,Ltd
        $html = '<div style="text-align: left">';
				$html .= '<span style="font-weight: bold; font-size: 11px; color: black;">'.get_option('invoice_company_name').'</span><br>';
				$html .= '<span style="font-size: 10px;">'._l('Địa chỉ').' : '.get_option('invoice_company_address').'</span><br>';
				$html .= '<span style="font-size: 10px;">'._l('Điện thoại').' : '.get_option('invoice_company_phonenumber').'</span> <span style="font-size: 9px;"> '._l('Fax').' : '.get_option('fax_company').'</span><br>';
				$html .= '<span style="font-size: 10px;">'._l('Email').' : '.get_option('email_company').'</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 9px;">'._l('tnh_website').' : '.get_option('company_website').'</span><br>';
				$html .='</div>';
		echo '<table class="" cellspacing="0" cellpadding="0" border="0" style="width: 100%; font-size: 10px">
                <tr nobr="true">
                    <td style="width: 15%;" rowspan="1"><span><img width="125" height="75" src="'.$img.'"></span></td>
                    <td style="width: 45%;" rowspan="1">'.$html.'</td>
                    <td style="width: 40%" class="text-center"><h1 style="color: black; font-size: 21px">BẢNG BÁO GIÁ / QUOTATION</h1>
                    <br>
                    <table border="1">
                        <tr class="text-center">
                            <td colspan="5">Loại Sản Phẩm</td>
                        </tr>
                        <tr class="text-center">
                            <td style="width: 20%">Hangtag</td>
                            <td style="width: 20%"></td>
                            <td style="width: 20%">Thay đổi</td>
                            <td style="width: 20%">Cố định</td>
                            <td style="width: 20%"></td>
                        </tr>
                        <tr class="text-center">
                            <td style="width: 20%">Label</td>
                            <td style="width: 20%"></td>
                            <td style="width: 20%"></td>
                            <td style="width: 20%"></td>
                            <td style="width: 20%"></td>
                        </tr>
                    </table>
                    </td>
                    <td style="width: 20%;"></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 12.5%;"><span><span class="bold">Khách hàng: </span></span></td>
                    <td style="width: 35%;">'.$companyCustomer.'</td>
                    <td style="width: 5%;"></td>
                    <td style="width: 14%;"><span class="bold">MÃ BÁO GIÁ</span></td>
                    <td style="width: 30.5%;"><span>' . $quote['reference_no'] . '</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 12.5%;"><span><span class="bold">Người yêu cầu báo giá:</span></span></td>
                    <td style="width: 35%;">'.$created_by.'</td>
                    <td style="width: 5%;"></td>
                    <td style="width: 14%;"><span class="bold">Người thực hiện báo giá: </span></td>
                    <td style="width: 30.5%;">'.$created_by.'</td>
                </tr>
                <tr nobr="true">
                    <td style="width: 23.75%;"><span class="bold">Email: </span>'.$staff_create['email'].'</td>
                    <td style="width: 23.75%;"><span class="bold">Số liên hệ: </span>'.$staff_create['phonenumber'].'</td>
                    <td style="width: 5%;"></td>
                    <td style="width: 23.75%;"><span class="bold">Email: </span>'.$staff_create['email'].'</td>
                    <td style="width: 23.75%;"><span class="bold">Số liên hệ: </span>'.$staff_create['phonenumber'].'</td>
                </tr>
                <tr nobr="true">
                    <td style="width: 12.5%;"><span><span class="bold">Địa chỉ công ty:</span></span></td>
                    <td style="width: 35%;">'.$customer['address'].'</td>
                    <td style="width: 5%;"></td>
                    <td style="width: 14%;"><span class="bold">Ngày báo giá: </span></td>
                    <td style="width: 30.5%;"><span>'._d($quote['date']).'</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 12.5%;"><span><span class="bold">Địa điểm giao hàng:</span></span></td>
                    <td style="width: 35%;">'.$quote['ship_to'].'</td>
                    <td style="width: 5%;"></td>
                    <td style="width: 17%;"><span class="bold">Ngày hết hạn: </span></td>
                    <td style="width: 30.5%;"><span>' . (!empty($quote['expiration_date']) ? _d($quote['expiration_date']) : '') . '</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 12.5%;"><span><span class="bold">Người nhận hàng:</span> </span></td>
                    <td style="width: 35%;">'.$personContact.'</td>
                    <td style="width: 5%;"></td>
                    <td style="width: 17%;"><span class="bold">Hình thức thanh toán:</span></td>
                    <td style="width: 30.5%;"><span>'.$quote['payment_term'].'</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 23.75%;"><span class="bold">Email: </span>'.$emailContact.'</td>
                    <td style="width: 23.75%;"><span class="bold">Số liên hệ: </span>'.$phoneContact.'</td>
                    <td style="width: 5%;"></td>
                </tr>
            </table>
            <br><br><table class="" cellspacing="0" cellpadding="5" border="1" style="width: 100%;">
                <thead>
                    ' . $tHead . '
                </thead>
                <tbody>
                    ' . $bodyItems . '
                <tbody>
            </table>
            </table><br><br><table class="" cellspacing="0" cellpadding="3" border="0" style="width: 100%;">
                <tr nobr="true">
                    <td class="text-right" style="width: 100%;">
                         Month ……………..Date ………………………Year
                    </td>
                </tr>
                <tr nobr="true" class="text-center bold">
                    <td style="width: 30%;"></td>
                    <td style="width: 45%;" class="text-center"><span class="bold">Khách hàng xác nhận / Vendor confirmed</span></td>
                    <td style="width: 25%;" class="text-center"><span class="bold">Ký tên / Authorized Signature</span></td>
                </tr>
            </table><br><br><br>
            <div><span class="bold">Lưu ý / Notes:</span><br>'.$quote['note'].'</div>
        ';

		$data['pageCustome'] = 'quotes';
		$content = ob_get_contents();
		ob_end_clean();

		$barcode = file_get_contents(genBarcode($quote['reference_no']));
		$barcode = '<img style="width: 130px;" src="data:image/png;base64,' . base64_encode($barcode) . '"/>';

		$data['showHeader'] = 'hide';
		$data['type_print'] = 'quotes';
		$data['content'] = $content;
		$data['barcode'] = '';
		$pdf = @print_pdf_tnh($data);
		$type = $type_pdf;
		if ($type == "S") {
			return $pdf->Output(slug_it('quotes') . '.pdf', $type);
		} else {
			$pdf->Output(slug_it('quotes') . '.pdf', $type);
		}
	}
}
