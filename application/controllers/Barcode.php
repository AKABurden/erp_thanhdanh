<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Barcode extends App_Controller
{
    public function set_barcode($code = "", $text = 1)
    {
        $this->load->library('zend');
        $this->zend->load('Zend/Barcode');
        Zend_Barcode::render('code128', 'image', array('text' => $code, 'drawText' => !empty($text) ? true : false));
    }

    public function set_barcode_type($code = "", $text = 1, $type = 'code128')
    {
        $this->load->library('zend');
        $this->zend->load('Zend/Barcode');
        Zend_Barcode::render($type, 'image', array('text' => $code, 'drawText' => !empty($text) ? true : false));
    }


//	public function print_orders_detail($id)
//	{
//		$font_size = get_option('pdf_font_size');
//		if(!empty($font_size)) {
//			$font_size = 'font-size:' . $font_size . 'px;';
//		}
//		else {
//			$font_size = '';
//		}
//		$this->lang->line('vietnamese');
//		$this->lang->load('vietnamese', 'vietnamese');
//		$this->load->model('orders_model');
//		$this->load->model('products_model');
//		$this->load->model('unit_model');
//		ob_end_clean();
//		echo '<style>
//					#header, #nav, .noprint
//					{
//						display: none;
//					}
//			</style>';
//		$data = [];
//		$order = $this->orders_model->rowOrderById($id);
//		$customer = $this->clients_model->rowCustomer($order['customer_id']);
//
//		$contact = get_table_where('tblcontacts', ['userid' => $customer['userid']], '', 'row_array');
//
//		$address_delivery = $this->site_model->rowShippingClient($order['address_delivery_id']);
//		$employee = '';
//		if (!empty($order['employee_id'])) {
//			$employee = get_staff_full_name($order['employee_id']);
//		}
//		$items = $this->orders_model->getOrderItemsByOrderId($id);
//		// $img = file_get_contents(base_url('uploads/company/').get_option('company_logo'));
//		$data['title'] = lang('tnh_print_order');
//		$data['type'] = 'L';
//		$data['img'] = '';
//
//
//		$company_logo = get_option('company_logo');
//		$img = base_url('uploads/company/'.$company_logo);
////		echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">';
//		echo '<div  style="float: left;-webkit-print-color-adjust: exact;"><img  height="80px" src="'.$img.'"></div>';
//		$html = '<div style="text-align: right">';
//		$html .= '<span style="font-weight: bold; font-size: 13px; color: red;">'.get_option('invoice_company_name').'</span><br>';
//		$html .= '<span style="font-size: 10px;">'._l('Địa chỉ').' : '.get_option('invoice_company_address').'</span><br>';
//		$html .= '<span style="font-size: 10px;">'._l('Điện thoại').' : '.get_option('invoice_company_phonenumber').'</span> <span style="font-size: 9px;"> '._l('Fax').' : '.get_option('fax_company').'</span><br>';
//		$html .= '<span style="font-size: 10px;">'._l('Email').' : '.get_option('email_company').'</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 9px;">'._l('tnh_website').' : '.get_option('company_website').'</span><br>';
//		$html .='</div>';
//		echo '<div  style="float: right;-webkit-print-color-adjust: exact;">'.$html.'</div>';
//		echo "<div class='clearfix'></div>";
//		echo '<hr width="100%" style="margin-top: 10px;float:left;"/>';
//		echo "<div class='clearfix'></div>";
//
//
//		$bodyItems = '';
//		$totalBox = 0;
//		if (!empty($items)) {
//			foreach ($items as $key => $value) {
//				$type_item = $value['type_item'];
//				$items_id = $value['item_id'];
//
//				$info = null;
//				if ($type_item == "products") {
//					$info = $this->products_model->rowProduct($items_id);
//					$unit = $this->unit_model->rowUnit($info['unit_id']);
//				} else if ($type_item == "items") {
//					$info = $this->items_model->rowItems($items_id);
//					$unit = $this->unit_model->rowUnit($info['unit']);
//				} else if ($type_item == "materials") {
//					$info = $this->items_model->rowMaterial($items_id);
//					$unit = $this->unit_model->rowUnit($info['unit_id']);
//				}
//
//
//				$tdNumber = '<td class="text-center" style="width: 6%;">' . (++$key) . '</td>';
//				$tdCode = '<td style="width: 17%;">' . $info['code'] . '</td>';
//				$tdName = '<td style="width: 19%;font-family: kozgopromedium;font-size:11px">' . $value['product_name_customer'] . '</td>';
//				$tdUnit = '<td class="text-center" style="width: 8%;">' . $unit['unit'] . '</td>';
//				$tdQuantity = '<td class="text-center" style="width: 10%;">' . formatNumber($value['quantity']) . '</td>';
//
//				$dtBox = $this->orders_model->getOrderItemExchangeBox($value['id']);
//
//				$box = !empty($dtBox['total_quantity_exchange']) ? $dtBox['total_quantity_exchange'] : 0;
//				if (!empty($box)) {
//					$totalBox += $box;
//				}
//
//				$tdBox = '<td class="text-center" style="width: 8%;">' . formatNumber($box) . '</td>';
//
//				$tdUnitPrice = '<td class="text-center" style="width: 12%;">' . formatMoney($value['price']) . '</td>';
//				$tdTax = '<td class="text-right" style="width: 10%;">' . formatMoney($value['tax_amount_item']) . '</td>';
//				$tdDiscount = '<td class="text-right" style="width: 12%;">' . formatMoney($value['discount_percent_amount_item'] + $value['discount_direct_amount_item']) . '</td>';
//				$tdTotalAmount = '<td class="text-right" style="width: 13%;">' . formatMoney($value['total_amount']) . '</td>';
//
//				$dtDateDelivery = get_table_where('tbl_order_item_shippings', ['order_item_id' => $value['id']], '', 'row_array');
//				$dateDelivery = '';
//				if (!empty($dtDateDelivery)) {
//					$dateDelivery = _dhau($dtDateDelivery['date_shipping']);
//				}
//				$tdDateDelivery = '<td class="text-center" style="width: 12%;">' . $dateDelivery . '</td>';
//				$typePrint = get_table_where('tbl_type_print', ['id' => $info['type_print']], '', 'row_array');
//				$name_type_print = '';
//				if (!empty($typePrint)) {
//					$name_type_print = $typePrint['name'];
//				}
//				$tdType = '<td class="text-center" style="width: 13%;">' . $name_type_print . '</td>';
//
//				$tdNote = '<td style="width: 15%;">' . $value['note_item'] . '</td>';
//
//				$htmlOrderColumns = '';
//				if ($type_item == "products") {
//					$thSub = '';
//					$trHtmlChild = '';
//					$ct_counter_item = $value['ct_counter_item'];
//					$productsColumns = $this->products_model->getProductsColumns($items_id);
//					$orderItemsColumns = $this->orders_model->getOrderItemsColumnsByOrderItemId($value['id']);
//					$styleTd = '';
//					if (!empty($productsColumns)) {
//						$styleTd = 'width: ' . 72 / count($productsColumns) . '%';
//						foreach ($productsColumns as $k => $v) {
//							$thSub .= '<th class="text-center" style="' . $styleTd . '">' . $v['name'] . '</th>';
//						}
//					}
//					$orderItemsColumnsNew = [];
//					if ($ct_counter_item > 0) {
//						for ($i = 0; $i < $ct_counter_item; $i++) {
//							$arrNew = [];
//							foreach ($productsColumns as $k => $v) {
//								$columns_name = [];
//								foreach ($orderItemsColumns as $kO => $vO) {
//									if ($vO['counter_items_number'] == $i && $vO['columns_id'] == $v['id']) {
//										$columns_name = [
//											vn_to_str($vO['columns_value']) => $vO['columns_name']
//										];
//										break;
//									}
//								}
//								$arrNew = array_merge($arrNew, $columns_name);
//							}
//							$orderItemsColumnsNew[$i] = $arrNew;
//							$order_code = '';
//							$command = '';
//							$quantity_put = '';
//							$quantity_loss = '';
//							$sample_quantity_item = '';
//							foreach ($orderItemsColumns as $kO => $vO) {
//								if ($vO['columns_value'] == 'order_code' && $i == $vO['counter_items_number']) {
//									$order_code = $vO['columns_name'];
//									$orderItemsColumnsNew[$i]['code'] = $order_code;
//									continue;
//								} else if ($vO['columns_value'] == 'command' && $i == $vO['counter_items_number']) {
//									$command = $vO['columns_name'];
//									$orderItemsColumnsNew[$i]['command'] = $command;
//									continue;
//								} else if ($vO['columns_value'] == 'quantity_put' && $i == $vO['counter_items_number']) {
//									$quantity_put = $vO['columns_name'];
//									$orderItemsColumnsNew[$i]['quantity_put'] = $quantity_put;
//									continue;
//								} else if ($vO['columns_value'] == 'quantity_loss' && $i == $vO['counter_items_number']) {
//									$quantity_loss = $vO['columns_name'];
//									$orderItemsColumnsNew[$i]['quantity_loss'] = $quantity_loss;
//									continue;
//								} else if ($vO['columns_value'] == 'sample_quantity_item' && $i == $vO['counter_items_number']) {
//									$sample_quantity_item = $vO['columns_name'];
//									$orderItemsColumnsNew[$i]['sample_quantity_item'] = $sample_quantity_item;
//									continue;
//								}
//							}
//						}
//					}
//					$orderItemsColumnsNewVs1 = [];
//					if (!empty($orderItemsColumnsNew)) {
//						foreach ($orderItemsColumnsNew as $kkk => $vvv) {
//							$columns_name_new = 'default';
//							if (!empty($productsColumns)) {
//								foreach ($productsColumns as $k => $v) {
//									$name_check = vn_to_str($v['name']);
//									if (!empty($vvv[$name_check])) {
//										$columns_name_new .= $vvv[$name_check] . '__';
//									}
//								}
//							}
//							$columns_name_new = trim($columns_name_new, '__');
//							$check_key = $columns_name_new;
//							if (!empty($orderItemsColumnsNewVs1[$check_key])) {
//								$orderItemsColumnsNewVs1[$check_key]['quantity_put'] += $vvv['quantity_put'];
//								$orderItemsColumnsNewVs1[$check_key]['quantity_loss'] += $vvv['quantity_loss'];
//								$orderItemsColumnsNewVs1[$check_key]['sample_quantity_item'] += $vvv['sample_quantity_item'];
//							} else {
//								$orderItemsColumnsNewVs1[$check_key] = $vvv;
//							}
//						}
//					}
//					$ii = 1;
//					if (!empty($orderItemsColumnsNewVs1)) {
//						foreach ($orderItemsColumnsNewVs1 as $kk => $vv) {
//							$order_code = $vv['code'];
//							$command = $vv['command'];
//							$quantity_put = $vv['quantity_put'];
//							$quantity_loss = $vv['quantity_loss'];
//							$sample_quantity_item = $vv['sample_quantity_item'];
//							$trHtmlColumns = '';
//							$columns_name_new = '';
//							if (!empty($productsColumns)) {
//								foreach ($productsColumns as $k => $v) {
//									$name_check = vn_to_str($v['name']);
//									if (!empty($vv[$name_check])) {
//										$columns_name_new = $vv[$name_check];
//										$trHtmlColumns .= '
//                                        <td class="text-center" style="font-family: kozgopromedium;font-size:11px">
//                                            ' . $columns_name_new . '
//                                        </td>
//                                        ';
//									} else {
//										$trHtmlColumns .= '
//                                        <td class="text-center" style="font-family: kozgopromedium;font-size:11px">
//                                            ' . $columns_name_new . '
//                                        </td>
//                                        ';
//									}
//								}
//							}
//
//							$tdOrderCode = '<td class="text-center">
//                                ' . $order_code . '
//                            </td>';
//
//							$tdCommand = '<td class="text-center">
//                                ' . $command . '
//                            </td>';
//
//							$tdQuantityPut = '<td class="text-center">
//                                ' . formatNumber($quantity_put) . '
//                            </td>';
//
//							$tdQuantityLoss = '<td class="text-center">
//                                ' . formatNumber($quantity_loss) . '
//                            </td>';
//
//							$tdSampleQuantityItem = '<td class="text-center">
//                                ' . (!empty($sample_quantity_item) ? formatNumber($sample_quantity_item) : '') . '
//                            </td>';
//
//							$tdQuantityOld = '<td class="text-center">
//                                ' . (!empty($quantity_put + $quantity_loss + $sample_quantity_item) ? formatNumber($quantity_put + $quantity_loss + $sample_quantity_item) : '') . '
//                            </td>';
//
//
//							if (empty($trHtmlColumns) && empty($order_code)) continue;
//							$stt =  $ii;
//							$tdNumberChild = '<td class="text-center">' . $stt . '</td>';
//							$trHtmlChild .= '<tr class="not-tr">
//                                ' . $tdNumberChild . '
//                                ' . $trHtmlColumns . '
//                                ' . $tdQuantityPut . '
//                                ' . $tdQuantityLoss . '
//                                ' . $tdSampleQuantityItem . '
//                                ' . $tdQuantityOld . '
//                            </tr>';
//							$ii++;
//						}
//					}
//					$htmlOrderColumns .= '<table class="" border="1" style="'.$font_size.'width: 52%;">
//                            <thead>
//                                <tr>
//                                    <th class="text-center" style="width: 4%">
//                                        ' . lang('tnh_numbers') . '
//                                    </th>
//                                     ' . $thSub . '
//                                    <th class="text-center" style="width: 12%">' . lang('tnh_quantity_put') . '</th>
//                                    <th class="text-center" style="width: 12%">' . lang('tnh_quantity_loss') . '</th>
//                                    <th class="text-center" style="width: 12%">' . lang('tnh_sample_quantity') . '</th>
//                                    <th class="text-center" style="width: 12%">' . lang('Tổng số lượng') . '</th>
//                                </tr>
//                            </thead>
//                                <tbody class="child">
//                                    ' . $trHtmlChild . '
//                                </tbody>
//                            </table>
//                        ';
//				}
//
//				$bodyItems .= '<tr nobr="true">
//                    ' . $tdNumber . '
//                    ' . $tdCode . '
//                    ' . $tdName . '
//                    ' . $tdUnit . '
//                    ' . $tdQuantity . '
//                    ' . $tdDateDelivery . '
//                    ' . $tdType . '
//                    ' . $tdNote . '
//                </tr>
//                <tr>
//                    <td colspan="8">
//                        ' . $htmlOrderColumns . '
//                    </td>
//                </tr>
//                ';
//			}
//		}
//
//		$divAddress = !empty($address_delivery['address']) ? '<span>' . _l('tnh_address_delivery') . ': <span>' . $address_delivery['address'] . '</span></span><br>' : '';
//		$divEmployeeCharge = !empty($employee) ? '<span>' . _l('tnh_employees_charge') . ': <span>' . $employee . '</span></span><br>' : '';
//		$divNote = !empty($order['note']) ? '<span>' . _l('tnh_note') . ': <span>' . $order['note'] . '</span></span><br>' : '';
//
//
//		$day = date_format(date_create($order['date']), 'd');
//		$month = date_format(date_create($order['date']), 'm');
//		$year = date_format(date_create($order['date']), 'Y');
//		$message = "";
//		ob_start();
//		stylePdf();
//		$phoneContact = '';
//		if (!empty($contact) && !empty($contact['phonenumber'])) {
//			$phoneContact = ' (' . $contact['phonenumber'] . ')';
//		}
//		$typeOrder = get_table_where('tbl_type_orders', ['id' => $order['type_orders']], '', 'row_array');
//		echo '
//            <table class="" cellspacing="0" cellpadding="5" border="0" style="width: 100%;" style="'.$font_size.'">
//                <tr nobr="true">
//                    <td colspan="8"><h1 class="text-center uppercase" style="font-size: 20px;">' . _l('orders') . '</h1></td>
//                </tr>
//            </table>
//            <table class="" cellspacing="0" cellpadding="0" border="0" style="width: 100%;" style="'.$font_size.'">
//                <tr nobr="true">
//                    <td style="width: 20%;">' . _l('date') . '</td>
//                    <td style="width: 80%;"><b>' . _d($order['date'], true) . '</b></td>
//                </tr>
//                <tr nobr="true">
//                    <td>' . _l('tnh_reference_orders') . '</td>
//                    <td><b>' . $order['reference_no'] . '</b></td>
//                </tr>
//                <tr nobr="true">
//                    <td>' . _l('Loại đơn hàng') . '</td>
//                    <td><b>' . $typeOrder['name'] . '</b></td>
//                </tr>
//                <tr nobr="true">
//                    <td>' . _l('customers') . '</td>
//                    <td><b>' . $customer['company_short'] . '</b></td>
//                </tr>
//                <tr nobr="true">
//                    <td>' . _l('tnh_address_delivery') . '</td>
//                    <td><b>' . $address_delivery['address'] . '</b></td>
//                </tr>
//                <tr nobr="true">
//                    <td>' . _l('Người liên hệ') . '</td>
//                    <td><b>' . (!empty($contact) ? $contact['firstname'] . $phoneContact : '') . '</b></td>
//                </tr>
//                <tr nobr="true">
//                    <td>' . _l('tnh_note') . '</td>
//                    <td><b>' . $order['note'] . '</b></td>
//                </tr>
//            </table>
//            <br><br>
//            <table class="" cellspacing="0" cellpadding="5" border="0" style="width: 100%; border-style: soild; border-color: black;'.$font_size.'">
//                <tr nobr="true" style="background-color: #ddd;-webkit-print-color-adjust: exact;">
//                    <td class="bold text-center" style="width: 6%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_numbers') . '</td>
//                    <td class="bold text-center" style="width: 17%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('Mã TP') . '</td>
//                    <td class="bold text-center" style="width: 19%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('Tên TP') . '</td>
//                    <td class="bold text-center" style="width: 8%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_dvt') . '</td>
//                    <td class="bold text-center" style="width: 10%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('quantity') . '</td>
//                    <td class="bold text-center" style="width: 12%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('Ngày dk giao') . '</td>
//                    <td class="bold text-center" style="width: 13%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('Loại hình in') . '</td>
//                    <td class="bold text-center" style="width: 15%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_note') . '</td>
//                </tr>
//                ' . $bodyItems . '
//                <tr class="bold" nobr="true" style="background-color: #ddd;-webkit-print-color-adjust: exact;">
//                    <th class="text-right" colspan="3">' . _l('tnh_total') . '</th>
//                    <th></th>
//                    <th class="text-center">' . formatNumber($order['total_quantity']) . '</th>
//                    <th></th>
//                    <th></th>
//                    <th></th>
//                </tr>
//            </table>
//            <br><br>
//            <table style="width: 100%" style="'.$font_size.'">
//                <tr nobr="true" class="text-center">
//                    <td></td>
//                    <td></td>
//                    <td><span>Ngày ' . $day . ' tháng ' . $month . ' năm ' . $year . '</span></td>
//                </tr>
//                <tr nobr="true">
//                    <td class="text-center">
//                        <span class="bold">Người Giao</span><br>
//                        <span>(Ký, ghi rõ họ tên)</span>
//                    </td>
//                    <td class="text-center">
//                        <span class="bold">Người Nhận</span><br>
//                        <span>(Ký, ghi rõ họ tên)</span>
//                    </td>
//                    <td class="text-center">
//                        <span class="bold">Trưởng Bộ Phận</span><br>
//                        <span>(Ký, ghi rõ họ tên)</span>
//                    </td>
//                </tr>
//            </table>
//        ';
//
//		return $content = ob_get_contents();
//		ob_end_clean();
//
//		$data['content'] = $content;
//		echo $data['content'];die();
////		$data['pageCustome'] = 'orders_detail';
////		$pdf = @print_pdf_tnh_new($data);
////		$type = 'I';
////		$pdf->Output(slug_it('123') . '.pdf', $type);
//	}

}
