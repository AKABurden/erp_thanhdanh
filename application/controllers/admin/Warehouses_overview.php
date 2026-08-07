<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Warehouses_overview extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['warehouses'] = get_table_where('tblwarehouse');
        $data['type_items'] = get_table_where('tbltype_items', array('active' => 1));
        $data['title']      = 'Tổng quan chi tiết kho';
        $this->load->view('admin/warehouses_overview/manage', $data);
    }

    public function table_warehouses_overview_nvl()
    {
        $this->app->get_table_data('warehouses_overview_nvl');
    }

    public function export_excel()
    {
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();

        $warehouse_id       = $this->input->get('warehouse_id');
        $type_items         = $this->input->get('type_items');
        $category_id        = $this->input->get('category_id');
        $custom_item_select = $this->input->get('custom_item_select');
        $lot_code           = $this->input->get('lot_code');
        $localtion          = $this->input->get('localtion');

        // Fetch target warehouses
        $this->db->select('id, name');
        if (!empty($warehouse_id)) {
            if (is_array($warehouse_id)) {
                $wh_ids = array_filter(array_map('intval', $warehouse_id));
                if (!empty($wh_ids)) {
                    $this->db->where_in('id', $wh_ids);
                }
            } else if (is_numeric($warehouse_id)) {
                $this->db->where('id', intval($warehouse_id));
            } else if (is_string($warehouse_id) && strpos($warehouse_id, ',') !== false) {
                $wh_ids = array_filter(array_map('intval', explode(',', $warehouse_id)));
                if (!empty($wh_ids)) {
                    $this->db->where_in('id', $wh_ids);
                }
            }
        }
        $warehouses = $this->db->get('tblwarehouse')->result_array();

        if (empty($warehouses)) {
            $warehouses = get_table_where('tblwarehouse');
        }

        $sheetIndex = 0;

        foreach ($warehouses as $wh) {
            if ($sheetIndex > 0) {
                $objPHPExcel->createSheet($sheetIndex);
            }
            $sheet = $objPHPExcel->setActiveSheetIndex($sheetIndex);

            // Clean title for sheet name (max 30 chars, no special chars)
            $clean_title = preg_replace('/[\:\/\\\?\*\[\]]/', '', $wh['name']);
            $sheet_title = mb_substr($clean_title, 0, 30, 'UTF-8');
            if (empty($sheet_title)) {
                $sheet_title = 'Kho ' . $wh['id'];
            }
            $sheet->setTitle($sheet_title);

            // Set Header Style
            $styleHeader = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => ['rgb' => '337AB7']
                ],
                'alignment' => [
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]
                ]
            ];

            $headers = [
                'STT', 'Mã hàng', 'Tên hàng', 'ĐVT', 'Mã Lô',
                'Quy cách', 'Vị trí kho', 'Số lượng tồn',
                'Ngày sản xuất', 'Hạn sử dụng', 'Hạn dùng', 'Giá trị kho'
            ];

            $colLetters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];

            // Header title inside worksheet
            $sheet->setCellValue('A1', 'TỔNG QUAN KHO: ' . mb_strtoupper($wh['name'], 'UTF-8'));
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

            // Write column headers at row 3
            foreach ($headers as $idx => $h) {
                $sheet->setCellValue($colLetters[$idx] . '3', $h);
            }
            $sheet->getStyle('A3:L3')->applyFromArray($styleHeader);

            // Query items for this specific warehouse
            $this->db->select('
                tblwarehouse_product.*,
                tbllocaltion_warehouses.name_parent as localtion_name,
                SUM(tblwarehouse_product.quantity_left) as total_quantity,
                SUM(tblwarehouse_product.product_quantity_payment_left * tblwarehouse_product.price) as total_price
            ');
            $this->db->from('tblwarehouse_product');
            $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_product.localtion', 'left');
            $this->db->join('tbl_materials', 'tbl_materials.id = tblwarehouse_product.product_id AND tblwarehouse_product.type_items = "nvl"', 'left');
            $this->db->join('tbl_products', 'tbl_products.id = tblwarehouse_product.product_id AND (tblwarehouse_product.type_items = "product" OR tblwarehouse_product.type_items = "products")', 'left');

            if (!empty($type_items)) {
                if ($type_items == 'product') {
                    $this->db->where_in('tblwarehouse_product.type_items', array('product', 'products'));
                } else {
                    $this->db->where('tblwarehouse_product.type_items', $type_items);
                }
            } else {
                $this->db->where_in('tblwarehouse_product.type_items', array('nvl', 'product', 'products'));
            }

            $this->db->where('tblwarehouse_product.warehouse_id', $wh['id']);

            if (!empty($category_id)) {
                $this->db->group_start();
                $this->db->where('tbl_materials.category_id', $category_id);
                $this->db->or_where('tbl_products.category_id', $category_id);
                $this->db->group_end();
            }
            if (!empty($custom_item_select)) {
                $this->db->where('tblwarehouse_product.product_id', $custom_item_select);
            }
            if (!empty($lot_code)) {
                $this->db->where('tblwarehouse_product.lot_code', $lot_code);
            }
            if (!empty($localtion)) {
                $child_locations = [];
                get_full_childs_id($localtion, $child_locations);
                if (!empty($child_locations)) {
                    $this->db->where_in('tblwarehouse_product.localtion', $child_locations);
                }
            }

            $this->db->group_by('tblwarehouse_product.product_id, tblwarehouse_product.type_items, tblwarehouse_product.localtion, tblwarehouse_product.lot_code, date_sx, date_sd, date_use');
            $this->db->having('SUM(tblwarehouse_product.quantity_left) > 0');
            $this->db->order_by('tblwarehouse_product.product_id ASC, tblwarehouse_product.localtion ASC');

            $items = $this->db->get()->result_array();

            $rowNum    = 4;
            $stt       = 1;
            $sum_qty   = 0;
            $sum_total = 0;

            foreach ($items as $item) {
                $get_item_info = get_items($item['product_id'], $item['type_items']);

                $warehouse_detail = get_table_where('tblwarehouse_product', array(
                    'warehouse_id'  => $wh['id'],
                    'product_id'    => $item['product_id'],
                    'type_items'    => $item['type_items'],
                    'localtion'     => $item['localtion'],
                    'type_export'   => 18,
                    'quantity_left >' => 0
                ));
                $spec_text = '';
                if (!empty($warehouse_detail)) {
                    foreach ($warehouse_detail as $v_det) {
                        $purchase_products = get_table_where('tbl_purchase_products', array('id' => $v_det['import_id']), '', 'row');
                        if (!empty($purchase_products->productions_orders_details_id)) {
                            $order_production_details = get_table_where('tbl_productions_orders_details', array('id' => $purchase_products->productions_orders_details_id), '', 'row');
                            if (!empty($order_production_details)) {
                                $spec_text .= $order_production_details->reference_no . ' :SL( ' . $v_det['quantity_left'] . ' ); ';
                            }
                        }
                    }
                }

                $sheet->setCellValue('A' . $rowNum, $stt);
                $sheet->setCellValue('B' . $rowNum, isset($get_item_info->code) ? $get_item_info->code : '');
                $sheet->setCellValue('C' . $rowNum, isset($get_item_info->name) ? $get_item_info->name : '');
                $sheet->setCellValue('D' . $rowNum, isset($get_item_info->unit_name_stock) ? $get_item_info->unit_name_stock : '');
                $sheet->setCellValue('E' . $rowNum, $item['lot_code']);
                $sheet->setCellValue('F' . $rowNum, $spec_text);
                $sheet->setCellValue('G' . $rowNum, $item['localtion_name']);
                $sheet->setCellValue('H' . $rowNum, $item['total_quantity']);
                $sheet->setCellValue('I' . $rowNum, _d($item['date_sx']));
                $sheet->setCellValue('J' . $rowNum, _d($item['date_sd']));
                $sheet->setCellValue('K' . $rowNum, $item['date_use']);
                $sheet->setCellValue('L' . $rowNum, $item['total_price']);

                $sum_qty   += $item['total_quantity'];
                $sum_total += $item['total_price'];

                $rowNum++;
                $stt++;
            }

            // Summary Row
            $sheet->setCellValue('A' . $rowNum, 'TỔNG CỘNG');
            $sheet->mergeCells('A' . $rowNum . ':G' . $rowNum);
            $sheet->setCellValue('H' . $rowNum, $sum_qty);
            $sheet->setCellValue('L' . $rowNum, $sum_total);

            $sheet->getStyle('A' . $rowNum . ':L' . $rowNum)->getFont()->setBold(true);
            $sheet->getStyle('A3:L' . $rowNum)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

            foreach ($colLetters as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $sheetIndex++;
        }

        $objPHPExcel->setActiveSheetIndex(0);

        $filename = 'Tong_Quan_Kho_' . ($type_items ? strtoupper($type_items) . '_' : '') . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
}
