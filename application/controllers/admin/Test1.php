<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Test1 extends AdminController
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
    }

    public function excel_orders_information()
    {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        $style_excel = style_excel();
        $cloumns_excel = cloumns_excel();
        $objPHPExcel = new PHPExcel();

        $customer_search = $this->input->get('customer_search');
        $start_date_search = $this->input->get('start_date_search');
        $end_date_search = $this->input->get('end_date_search');
        $type_orders_search = $this->input->get('type_orders_search');
        $status_orders_search = $this->input->get('status_orders_search');
        $items_search = $this->input->get('items_search');
        $branch_search = $this->input->get('branch_search');

        $str_customer = lang('all');
        if (!empty($customer_search)) {
            $dtCustomer = get_table_where('tblclients', ['userid' => $customer_search], '', 'row_array', '', 'company');
            $str_customer = $dtCustomer['company'];
        }

        $str_start_date = lang('all');
        if (!empty($start_date_search)) {
            $str_start_date = $start_date_search;
        }

        $str_end_date = lang('all');
        if (!empty($start_date_search)) {
            $str_end_date = $start_date_search;
        }

        $str_type_orders = lang('all');
        if (!empty($type_orders_search)) {
            $dtTypeOrders = get_table_where('tbl_type_orders', ['id' => $type_orders_search], '', 'row_array', '', 'name');
            $str_type_orders = $dtTypeOrders['name'];
        }

        $str_status_orders = lang('all');
        if (!empty($status_orders_search)) {
            $dtStatusOrders = get_table_where('tbl_status_orders', ['id' => $status_orders_search], '', 'row_array', '', 'name, day_start, day_end');
            $str_status_orders = $dtStatusOrders['name'];
        }

        $tbDelivery = "(
            SELECT
                tbl_delivery_items.order_item_id as order_item_id,
                GROUP_CONCAT(CONCAT(DATE_FORMAT(tbl_deliveries.date, '%d/%m/%Y'), ' - ', tbl_delivery_items.quantity)) as date_delivery
            FROM tbl_delivery_items
            INNER JOIN tbl_deliveries ON tbl_deliveries.id = tbl_delivery_items.delivery_id
            GROUP BY tbl_delivery_items.order_item_id
        ) tb_delivery_item";

        $tbDateExpectedDelivery = "(
            SELECT
                tbl_order_item_shippings.order_item_id as order_item_id,
                tbl_order_item_shippings.date_shipping as date_shipping
            FROM tbl_order_item_shippings
            GROUP BY tbl_order_item_shippings.order_item_id
        ) tb_order_item_shippings";

        $tbGroupCustomer = '(
            SELECT
                tblcustomer_groups.customer_id as customer_id,
                GROUP_CONCAT(tblcustomers_groups.name) as group_name
            FROM tblcustomer_groups
            INNER JOIN tblcustomers_groups ON tblcustomers_groups.id = tblcustomer_groups.groupid
            GROUP BY tblcustomer_groups.customer_id
        ) tb_customer_group';

        $tbWarehousesProducts = "(
            SELECT
                tblwarehouse_items.id_items as id_items,
                SUM(tblwarehouse_items.product_quantity) as product_quantity
            FROM tblwarehouse_items
            INNER JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion
            LEFT JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id
            WHERE tblwarehouse_items.type_items = 'product' AND tblwarehouse_items.product_quantity > 0 AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != 'orders') or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0)) AND tblwarehouse_items.warehouse_id NOT IN (".WAREHOUSES_SYSTEM.") AND tbllocaltion_warehouses.stage_id = 0
            GROUP BY tblwarehouse_items.id_items
        ) tb_warehouses_products";


        $this->db->select('
            tbl_orders.id as id,
            tbl_orders.date as date,
            tblclients.zcode as zcode,
            tb_customer_group.group_name as brand,
            tbl_orders.reference_no as reference_no,
            tb_order_item_shippings.date_shipping as date_delivery,
            tbl_order_items.product_name_customer as product_name_customer,
            tbl_products.code as item_code,
            tbl_products.product_code_customer as product_code_customer,
            (tbl_order_items.quantity - tbl_order_items.quantity_delivery) as quantity_not_delivery,
            tbl_order_items.quantity as quantity_orders,
            tbl_order_items.quantity_delivery as quantity_delivery,
            (tbl_order_items.quantity - tbl_order_items.quantity_delivery) as quantity_rest,
            0 as quantity_warehouse,
            tbl_order_items.price as price,
            "" as quantity_detail,
            tbl_species.name as name_species,
            tbl_type_print.name as name_type_print,
            tbl_products.images as images,
            tbl_order_items.note_item as note_item,
            tbl_status_orders.name as name_status_orders,
            tbl_status_orders.time as time,
            tbl_type_orders.name as name_type_orders,
            tbl_orders.created_by as created_by,
            tblbranch.name as name_branch,
            tbl_orders.is_cancel as is_cancel,
            tbl_orders.type_items as type_items,
            tbl_order_items.id as order_item_id,
            tbl_order_items.item_id as item_id,
            "" as code_production
        ', false);
        $this->db->from('tbl_orders');
        $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'inner');
        $this->db->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id', 'inner');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_order_items.item_id', 'inner');
        $this->db->join('tblbranch', 'tblbranch.id = tbl_orders.id_branch', 'left');
        $this->db->join('tbl_status_orders', 'tbl_status_orders.id = tbl_orders.status_orders', 'left');
        $this->db->join('tbl_species', 'tbl_species.id = tbl_products.species', 'left');
        $this->db->join('tbl_type_print', 'tbl_type_print.id = tbl_products.type_print', 'left');
        $this->db->join('tbl_type_orders', 'tbl_type_orders.id = tbl_orders.type_orders', 'left');
        $this->db->join($tbDateExpectedDelivery, 'tb_order_item_shippings.order_item_id = tbl_order_items.id', 'left');
        // $this->db->join($tbDelivery, 'tb_delivery_item.order_item_id = tbl_order_items.id', 'left');
        $this->db->join($tbGroupCustomer, 'tb_customer_group.customer_id = tblclients.userid', 'left');
        // $this->db->join($tbWarehousesProducts, 'tb_warehouses_products.id_items = tbl_order_items.item_id', 'left');

        $this->db->order_by('tbl_status_orders.id DESC');

        $where        = [
            'tbl_order_items.type_item = "products"'
        ];

        if (!empty($customer_search)) {
            array_push($where, "AND tbl_orders.customer_id = " . $this->db->escape($customer_search));
        }

        if (!empty($items_search)) {
			$items_search = explode('__', $items_search);
            array_push($where, "AND tbl_order_items.item_id = " . $items_search[0]);
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_orders.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_orders.date <= '" . $end_date_search . "'");
        }

        if (!empty($type_orders_search)) {
            array_push($where, "AND tbl_orders.type_orders = " . $type_orders_search . "");
        }

        if (!empty($status_orders_search)) {
			if(!empty($dtStatusOrders)) {
				if(!empty($dtStatusOrders['day_start']) && !empty($dtStatusOrders['day_end'])) {
					$day_start = $dtStatusOrders['day_start'];
					$day_end = $dtStatusOrders['day_end'];
					$date_ship_start = date("Y-m-d", strtotime("+$day_start day"));
					$date_ship_end = date("Y-m-d", strtotime("+$day_end day"));
					$where[] = 'AND tb_order_item_shippings.date_shipping >= "' . $date_ship_start . '"';
					$where[] = 'AND tb_order_item_shippings.date_shipping <= "' . $date_ship_end . '"';
				}
			}
        }

        if (!empty($branch_search)) {
            array_push($where, "AND tbl_orders.id_branch = " . $branch_search . "");
        }

        $where = implode(' ', $where);
        if (!empty($where)) {
            $this->db->where($where, false, false);
        }
        $rs = $this->db->get()->result_array();

        insertCompanyInfo($objPHPExcel, 'C1:P2');

        $objPHPExcel->getActiveSheet()->setCellValue('E5', 'THỐNG KÊ ĐƠN HÀNG');
        $objPHPExcel->getActiveSheet()->mergeCells("E5:M5");

        $objPHPExcel->getActiveSheet()->getStyle("E5")->applyFromArray($style_excel['c_head']);

        $objPHPExcel->getActiveSheet()->setCellValue('E6', 'Khách hàng: ');
        $objPHPExcel->getActiveSheet()->setCellValue('F6', $str_customer);

        $objPHPExcel->getActiveSheet()->setCellValue('E7', 'Loại đơn hàng: ');
        $objPHPExcel->getActiveSheet()->setCellValue('F7', $str_type_orders);

        $objPHPExcel->getActiveSheet()->setCellValue('E8', 'Trạng thái đơn hàng: ');
        $objPHPExcel->getActiveSheet()->setCellValue('F8', $str_status_orders);

        $objPHPExcel->getActiveSheet()->setCellValue('E9', 'Ngày bắt đầu: ');
        $objPHPExcel->getActiveSheet()->setCellValue('F9', $str_start_date);

        $objPHPExcel->getActiveSheet()->setCellValue('G9', 'Ngày kết thúc: ');
        $objPHPExcel->getActiveSheet()->setCellValue('H9', $str_end_date);

		
		
        $objPHPExcel->getActiveSheet()->setCellValue('A11', 'STT');
	
		$objPHPExcel->getActiveSheet()->setCellValue('B11', 'Nhóm Đơn Hàng');
        $objPHPExcel->getActiveSheet()->setCellValue('C11', 'Hình sản phẩm');

		$objPHPExcel->getActiveSheet()->setCellValue('D11', 'Mã TP');
		$objPHPExcel->getActiveSheet()->setCellValue('E11', 'Trạng Thái ĐH');
		
		
        $objPHPExcel->getActiveSheet()->setCellValue('F11', 'Ngày mở đơn');
        $objPHPExcel->getActiveSheet()->setCellValue('G11', 'Mã KH');
        $objPHPExcel->getActiveSheet()->setCellValue('H11', 'Brand');
        $objPHPExcel->getActiveSheet()->setCellValue('I11', 'Mã ĐĐH');
        $objPHPExcel->getActiveSheet()->setCellValue('J11', 'Ngày giao dự kiến');
        $objPHPExcel->getActiveSheet()->setCellValue('K11', 'Tên TP của khách');
        $objPHPExcel->getActiveSheet()->setCellValue('L11', 'SL chưa giao');
        $objPHPExcel->getActiveSheet()->setCellValue('M11', 'SL đơn hàng');
        $objPHPExcel->getActiveSheet()->setCellValue('N11', 'SL đã giao');
        $objPHPExcel->getActiveSheet()->setCellValue('O11', 'SL còn lại');
        $objPHPExcel->getActiveSheet()->setCellValue('P11', 'Số lượng tồn');
        $objPHPExcel->getActiveSheet()->setCellValue('q11', 'Đơn giá');
        $objPHPExcel->getActiveSheet()->setCellValue('R11', 'Ngày giao hàng - SL chi tiết');
        $objPHPExcel->getActiveSheet()->setCellValue('S11', 'Chủng loại');
        $objPHPExcel->getActiveSheet()->setCellValue('T11', 'Loại hình in');
        $objPHPExcel->getActiveSheet()->setCellValue('U11', 'Ghi chú');
        $objPHPExcel->getActiveSheet()->setCellValue('V11', 'Loại đơn hàng');
        $objPHPExcel->getActiveSheet()->setCellValue('W11', 'LSX');
        $objPHPExcel->getActiveSheet()->setCellValue('X11', 'Người lập đơn');
        $objPHPExcel->getActiveSheet()->setCellValue('Y11', 'Chi nhánh xưởng');
        $objPHPExcel->getActiveSheet()->setCellValue('Z11', 'Trạng thái hủy');


        $objPHPExcel->getActiveSheet()->getStyle("A5:Z11")->applyFromArray([
			'font' => array(
				'bold' => true,
				'color' => array('rgb' => '000000'),
				'size' => 12,
				'name' => 'Times New Roman'
			),
        ]);


        $row = 11;
        $group = '';
        $start = 0;
        $quantity_not_delivery = 0;
        $quantity_orders = 0;
        $quantity_delivery = 0;
        $quantity_rest = 0;
        $quantity_warehouse = 0;
        if (!empty($rs)) {
            $arrOrderItemId = [];
            $arrItemId = [];
            foreach ($rs as $key => $value) {
                $arrOrderItemId[] = $value['order_item_id'];
                $arrItemId[] = $value['item_id'];
            }

            if (!empty($arrOrderItemId)) {
                $arrOrderItemId = array_unique($arrOrderItemId);
                $tbDelivery = "
                    SELECT
                        tbl_delivery_items.order_item_id as order_item_id,
                        GROUP_CONCAT(CONCAT(DATE_FORMAT(tbl_deliveries.date, '%d/%m/%Y'), ' - ', tbl_delivery_items.quantity)) as date_delivery
                    FROM tbl_delivery_items
                    INNER JOIN tbl_deliveries ON tbl_deliveries.id = tbl_delivery_items.delivery_id
                    WHERE tbl_delivery_items.order_item_id IN (".implode(',', $arrOrderItemId).")
                    GROUP BY tbl_delivery_items.order_item_id
                ";
                $listDelivery = $this->db->query($tbDelivery)->result_array();
                if (!empty($listDelivery)) {
                    $listDelivery = array_reduce($listDelivery, function($carry, $item) {
                        $carry[$item['order_item_id']] = $item;
                        return $carry;
                    });
                }

                //production
                $tbProductions = '
                    SELECT
                        tbl_productions_orders_items.production_plan_item_id,
                        GROUP_CONCAT(tbl_productions_orders.reference_no SEPARATOR ", ") as code_production
                    FROM tbl_productions_orders_items
                    JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id
                    WHERE tbl_productions_orders_items.object_item_type = "orders" AND tbl_productions_orders_items.production_plan_item_id IN ('.implode(",", $arrOrderItemId).')
                    GROUP BY tbl_productions_orders_items.production_plan_item_id
                ';
                $listProductions = $this->db->query($tbProductions)->result_array();
                if (!empty($listProductions)) {
                    $listProductions = array_reduce($listProductions, function($carry, $item) {
                        $carry[$item['production_plan_item_id']] = $item;
                        return $carry;
                    });
                }

                
            }

            if (!empty($arrItemId)) {
                $arrItemId = array_unique($arrItemId);
                //warehouses
                $tbWarehousesProducts = "
                    SELECT
                        tblwarehouse_items.id_items as id_items,
                        SUM(tblwarehouse_items.product_quantity) as product_quantity
                    FROM tblwarehouse_items
                    INNER JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion
                    LEFT JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id
                    WHERE tblwarehouse_items.type_items = 'product' AND tblwarehouse_items.product_quantity > 0 AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != 'orders') or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0)) AND tblwarehouse_items.warehouse_id NOT IN (".WAREHOUSES_SYSTEM.") AND tbllocaltion_warehouses.stage_id = 0 AND tblwarehouse_items.id_items IN (".implode(',', $arrItemId).")
                    GROUP BY tblwarehouse_items.id_items
                ";
                $listWarehousesProducts = $this->db->query($tbWarehousesProducts)->result_array();
                if (!empty($listWarehousesProducts)) {
                    $listWarehousesProducts = array_reduce($listWarehousesProducts, function($carry, $item) {
                        $carry[$item['id_items']] = $item;
                        return $carry;
                    });
                }
            }

            foreach ($rs as $key => $aRow) {
                // Sanitize emojis and control characters that corrupt Excel when combined with Drawing blocks
                $fields_to_sanitize = ['item_code', 'zcode', 'brand', 'reference_no', 'product_name_customer', 'note_item', 'name_species', 'name_type_print', 'name_type_orders', 'code_production', 'name_branch', 'name_status_orders'];
                foreach ($fields_to_sanitize as $field) {
                    if (isset($aRow[$field]) && is_string($aRow[$field])) {
                        // Remove 4-byte characters (emojis) and unprintable control chars
                        $aRow[$field] = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $aRow[$field]);
                        $aRow[$field] = preg_replace('/[\x00-\x1F\x7F]/', ' ', $aRow[$field]); // Note: stripped \n and \r
                        $aRow[$field] = mb_substr($aRow[$field], 0, 500); // Prevent PHPExcel CONTINUE record issues
                        if (strpos($aRow[$field], '=') === 0) $aRow[$field] = ' ' . $aRow[$field]; // Prevent formula macro injection
                    }
                }

                $row++;
                $start++;
                $order_item_id = $aRow['order_item_id'];
                $item_id = $aRow['item_id'];
                $dtDelivery = $listDelivery[$order_item_id] ?? null;
                if (!empty($dtDelivery)) $aRow['quantity_detail'] = $dtDelivery['date_delivery'];

                $dtProductions = $listProductions[$order_item_id] ?? null;
                if (!empty($dtProductions)) $aRow['code_production'] = $dtProductions['code_production'];

                $dtWarehousesProducts = $listWarehousesProducts[$item_id] ?? null;
                if (!empty($dtWarehousesProducts)) $aRow['quantity_warehouse'] = $dtWarehousesProducts['product_quantity'];

                $name_status_orders = !empty($aRow['name_status_orders']) ? $aRow['name_status_orders'] : 'Chưa xác định';
                if ($group != $name_status_orders) {
                    $group = $name_status_orders;

                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, (!empty($aRow['time']) ? $aRow['time'] : 'Chưa xác định'));
                    $objPHPExcel->getActiveSheet()->mergeCells("A$row:Z$row");
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $row)->applyFromArray(
                        array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'e3b897')
                            ),
							'font' => array(
								'bold' => true,
								'color' => array('rgb' => '000000'),
								'size' => 12,
								'name' => 'Times New Roman'
							),
                        )
                    );
                    $row++;
                }

				// $po = "(
				// 	SELECT
				// 		GROUP_CONCAt(tbl_productions_orders.reference_no) as reference_no
				// 	FROM tbl_productions_plan_orders
				// 	INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_plan_orders.productions_order_id
				// 	WHERE tbl_productions_plan_orders.productions_plan_id = ".$aRow['id']." AND tbl_productions_plan_orders.object_type = 'orders'
            	// )";
				// $dtPO = $this->db->query($po)->row();

                $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $start);
	
				$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, (!empty($aRow['type_items']) ? ($aRow['type_items'] == '1' ? 'Cố Định' : 'Thay Đổi') : ''));

                // $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
                // $allowedExtensions = ['png', 'jpg'];
                $ext = strtolower(pathinfo($aRow['images'], PATHINFO_EXTENSION));
                if (!empty($aRow['images'])) $aRow['images'] = 'uploads/products/'.$aRow['images'];
                // if ($aRow['images'] != '' && file_exists($aRow['images']) && in_array($ext, $allowedExtensions)) {
                // $arrDiff = [14, 15, 23, 24, 25, 44, 54, 55, 56, 57, 58, 59, 63, 66, 67, 72];
                // $arrDiff = [55, 56, 57, 58, 59, 63, 66, 67, 72];
                // $arrDiff = [63, 66, 67, 72];
                // $arrDiff = [67, 72];
                // $arrDiff = [67, 72];
                $arrDiff = [14];
                if ($aRow['images'] != '' && file_exists($aRow['images']) && !is_dir($aRow['images']) && !in_array($row, $arrDiff)) {
                    $imgSize = @getimagesize($aRow['images']);
                    if ($imgSize !== false && $imgSize[0] > 0 && $imgSize[1] > 0) {
                        $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                        $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                        $objDrawing1->setPath($aRow['images']);

                        list($originalWidth, $originalHeight) = $imgSize;
        
                        $maxWidth = 80;  // Chiều rộng tối đa của ô
                        $maxHeight = 80; // Chiều cao tối đa của ô

                        // Tính tỷ lệ để giữ nguyên khung hình
                        $scale = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
                        $scaledWidth = (int)($originalWidth * $scale);
                        $scaledHeight = (int)($originalHeight * $scale);

                        $objDrawing1->setWidth($scaledWidth);
                        $objDrawing1->setHeight($scaledHeight);

                        $offsetX = (int)(($maxWidth - $scaledWidth) / 2);
                        $offsetY = (int)(($maxHeight - $scaledHeight) / 2);
                        $objDrawing1->setOffsetX($offsetX + 2);
                        $objDrawing1->setOffsetY($offsetY + 2);
                        $objDrawing1->setCoordinates('C' . ($row));
                        $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight($maxHeight);
                    }
                }

				$objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $row, $aRow['item_code'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $name_status_orders);

                $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, _dC($aRow['date']));
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('G' . $row, $aRow['zcode'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('H' . $row, $aRow['brand'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('I' . $row, $aRow['reference_no'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, _dC($aRow['date_delivery']));
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('K' . $row, $aRow['product_name_customer'], PHPExcel_Cell_DataType::TYPE_STRING);

                $objPHPExcel->getActiveSheet()->setCellValue('L' . $row, $aRow['quantity_not_delivery']);
                $objPHPExcel->getActiveSheet()->getStyle("L$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($aRow['quantity_not_delivery']));

                $objPHPExcel->getActiveSheet()->setCellValue('M' . $row, $aRow['quantity_orders']);
                $objPHPExcel->getActiveSheet()->getStyle("M$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($aRow['quantity_orders']));

                $objPHPExcel->getActiveSheet()->setCellValue('N' . $row, $aRow['quantity_delivery']);
                $objPHPExcel->getActiveSheet()->getStyle("N$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($aRow['quantity_delivery']));

                $objPHPExcel->getActiveSheet()->setCellValue('O' . $row, $aRow['quantity_rest']);
                $objPHPExcel->getActiveSheet()->getStyle("O$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($aRow['quantity_rest']));

                $objPHPExcel->getActiveSheet()->setCellValue('P' . $row, $aRow['quantity_warehouse']);
                $objPHPExcel->getActiveSheet()->getStyle("P$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($aRow['quantity_warehouse']));

                $objPHPExcel->getActiveSheet()->setCellValue('Q' . $row, $aRow['price']);
                $objPHPExcel->getActiveSheet()->getStyle("Q$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($aRow['price']));

                $objPHPExcel->getActiveSheet()->setCellValue('R' . $row, $aRow['quantity_detail']);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('S' . $row, $aRow['name_species'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('T' . $row, $aRow['name_type_print'], PHPExcel_Cell_DataType::TYPE_STRING);

                $objPHPExcel->getActiveSheet()->setCellValueExplicit('U' . $row, $aRow['note_item'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('V' . $row, $aRow['name_type_orders'], PHPExcel_Cell_DataType::TYPE_STRING);
                // $objPHPExcel->getActiveSheet()->setCellValueExplicit('W' . $row, (!empty($dtPO->reference_no) ? $dtPO->reference_no : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('W' . $row, $aRow['code_production'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('X' . $row, get_staff_full_name($aRow['created_by']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('Y' . $row, $aRow['name_branch'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue('Z' . $row, $aRow['is_cancel'] ? 'Có' : '');


				$quantity_not_delivery += $aRow['quantity_not_delivery'];
                $quantity_orders += $aRow['quantity_orders'];
                $quantity_delivery += $aRow['quantity_delivery'];
                $quantity_rest += $aRow['quantity_not_delivery'];
                $quantity_warehouse+= (float)$aRow['quantity_warehouse'];
            }
        }

        $row++;
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, 'Tổng cộng');
        $objPHPExcel->getActiveSheet()->setCellValue('L' . $row, $quantity_not_delivery)->getStyle("L$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($quantity_not_delivery));
        $objPHPExcel->getActiveSheet()->setCellValue('M' . $row, $quantity_orders)->getStyle("M$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($quantity_orders));
        $objPHPExcel->getActiveSheet()->setCellValue('N' . $row, $quantity_delivery)->getStyle("N$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($quantity_delivery));
        $objPHPExcel->getActiveSheet()->setCellValue('O' . $row, $quantity_rest)->getStyle("O$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($quantity_rest));
        $objPHPExcel->getActiveSheet()->setCellValue('P' . $row, $quantity_warehouse)->getStyle("P$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($quantity_warehouse));

        $objPHPExcel->getActiveSheet()->getStyle("A$row:Z$row")->applyFromArray([
            'font' => array(
                'bold' => true,
            ),
        ]);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
	
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
		
		
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(15);
        $filename = lang('tnh_excel_orders_information') . '.xlsx';

        $objPHPExcel->getActiveSheet()->getStyle("A11:Z$row")->applyFromArray([
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
			'font' => array(
				'color' => array('rgb' => '000000'),
				'size' => 12,
				'name' => 'Times New Roman'
			),
        ]);

        $objPHPExcel->getActiveSheet()->getStyle("A1:AA$row")->getAlignment()->setWrapText(true);

        $objPHPExcel->getActiveSheet()->freezePane('A1');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');
        exit();
    }
}