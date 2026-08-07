<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Stock_out_request extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('stock_out_request_model');
        // $this->load->model('products_model');
    }

    public function index() {
        if ($this->input->is_ajax_request()) {
            $data = [];
            $this->app->get_table_data('stock_out_request', $data);
        }

        $data['title'] = _l('stock_out_request');
        $this->load->view('admin/stock_out_request/index', $data);
    }

    public function submit($id = '') {
        if ($this->input->post()) {
            $formData = $this->input->post();
            // var_dump($formData);die;

            $result = $this->stock_out_request_model->submit($formData, $id);
            if (!empty($result['submitId'])) {
                echo json_encode(['result'=>true, 'alert_type'=>'success', 'message'=>_l('Thành công')]);
            } else {
                echo json_encode(['result'=>false, 'alert_type'=>'danger', 'message'=>_l('Thất bại')]);
            }
        } else {
            $data['title'] = '';
            $data['arrBrand'] = get_table_where('tbl_brand');

            $data['title'] = 'Thêm ' . lang('stock_out_request');
            if (!empty($id)) {
                $data['title'] = 'Sửa ' . lang('stock_out_request');
                $data['value'] = $this->stock_out_request_model->get($id);
                // var_dump($data['value']);die;
                $data['id'] = $id;
            }

            $data['breadcrumb'] = [array('link' => base_url('admin/stock_out_request/'), 'page' => lang('stock_out_request')), array('link' => '#', 'page' => $data['title'])];
            $this->load->view('admin/stock_out_request/submit', $data);
        }
    }

    public function view($id){
        $data = [];
        $data['title'] = lang('stock_out_request');

        $data['value'] = $this->stock_out_request_model->get($id);
        // var_dump($data);die;

        $this->load->view('admin/stock_out_request/view',$data);
    }

    public function delete($id) {
        $result = $this->stock_out_request_model->delete($id);
        echo json_encode($result); die();
    }

    public function export_excel() {
        $this->load->library('ciqrcode');
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

        $tb_brand = '(
            SELECT
                tbl_brand.name as name,
                tbl_brand.id as id
            FROM tbl_brand
            GROUP BY tbl_brand.id
        ) as tb_brand';

        $this->db->select('
            tblstock_out_request.*,
            tblstock_out_request_item.*,
            tbl_productions_orders.reference_no as production_order_code,
            tbl_orders.reference_no as order_code,
            tbl_materials.id as item_id,
            tbl_materials.name_supplier as supplier_name,
            tbl_materials.code as item_code,
            tbl_materials.name as item_name,
            tbl_materials.height as height,
            tbl_materials.wide as wide,
            tbl_materials.longs as longs,
            tbl_species.name as specie_name,
            tbl_category_items.name as category_name,
            tblunits.unit as unit_name,
            (tbl_materials.height * tbl_materials.wide * tbl_materials.longs) as total_height
        ');
        $this->db->from('tblstock_out_request_item');
        $this->db->join('tblstock_out_request', 'tblstock_out_request.id = tblstock_out_request_item.stock_out_request_id', 'left');
        $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tblstock_out_request.production_order_id', 'left');
        $this->db->join('tbl_orders', 'tbl_orders.id = tblstock_out_request_item.order_id', 'left');
        $this->db->join('tbl_materials', 'tbl_materials.id = tblstock_out_request_item.item_id AND tblstock_out_request_item.item_type = "materials"', 'left');
        $this->db->join('tbl_species', 'tbl_species.id = tbl_materials.species', 'left');
        $this->db->join('tbl_category_items', 'tbl_category_items.id = tbl_materials.category_id', 'left');
        $this->db->join('tblunits', 'tbl_materials.unit_id = tblunits.unitid', 'left');
        $this->db->group_by('tblstock_out_request_item.id');

        if (!empty($search_date)) {
            $searchDate = explode(' - ', $search_date);
            $this->db->where('tblstock_out_request.date BETWEEN "' . to_sql_date($searchDate[0]) . ' 00:00:00" and "' . to_sql_date($searchDate[1]) . ' 23:59:59"');
        }

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
            'stt' => ucwords(_l('STT')),
            'code' => ucwords(_l('Mã phiếu')),
            'date' => ucwords(_l('Ngày lập phiếu')),
            'production_order_code' => ucwords(_l('Mã Lệnh SX')),
            'order_code' => ucwords(_l('Mã Đơn Hàng')),
            'supplier_name' => ucwords(_l('Tên NCC')),
            'item_code' => ucwords(_l('Mã NPL')),
            'item_name' => ucwords(_l('Tên NPL')),
            'category_name' => ucwords(_l('Tên Nhóm NPL')),
            'specie_name' => ucwords(_l('Tên Mã Chủng Loại SP')),
            'production_quantity' => ucwords(_l('Tổng Số Lượng SX')),
            'stock_quantity' => ucwords(_l('Số Lượng Tồn Kho')),
            'production_require_quantity' => ucwords(_l('Số Lượng Cần SX')),
            'stock_quantity_max' => ucwords(_l('Số Lượng Tồn Cho Phép')),
            'purchase_require_quantity' => ucwords(_l('Số Lượng Cần Mua')),
            'height' => ucwords(_l('Height')),
            'wide' => ucwords(_l('Width')),
            'longs' => ucwords(_l('Độ Dày NPL')),
            'total_height' => ucwords(_l('Tổng Chiều Cao')),
            'quota_time_change_one' => ucwords(_l('Định mức thời gian')),
            'limited_time' => ucwords(_l('Thời Gian Quy Định')),
            'qr' => ('QR'),
        ];
        $aColumns = array_keys($colName);

        $title = mb_strtoupper(_l('stock_out_request'));
        $excelRowNum = 1;
        $maxCol = count($colName) - 1;
        $objPHPExcel->getActiveSheet()->mergeCells('A'.($excelRowNum).':'.$cloumns_excel[$maxCol].$excelRowNum);
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$excelRowNum, $title)->getStyle("A".$excelRowNum)->applyFromArray($styleTitle);
        // $objPHPExcel->getActiveSheet()->freezePane('A1');
        
        $excelRowNum = 2;
        $mergeRowNum = 1;
        foreach ($aColumns as $key => $value) {
            foreach($headerFillColor as $colIndex => $color) {
                if ($cloumns_excel[$key] == $colIndex) {
                    $styleHeader['fill']['color'] = $color;
                    unset($headerFillColor[$colIndex]);
                    break;
                }
            }
            $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$key] . $excelRowNum, ($colName[$value]))->getStyle($cloumns_excel[$key] . ($excelRowNum))->applyFromArray($styleHeader);
            
            if ($value == 'qr') {
                $objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$key])->setWidth(10);
            } else {
                $objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$key])->setAutoSize(true);
            }
        }

        $excelRowNum = 3;
        foreach ($result as $key => $aRow) {
			// $aRow['id'] = ($key+1);

            foreach ($aColumns as $colIndex => $colCode) {
                if (str_contains($colCode, 'date')) {
                    $cellValue = (isset($aRow[$colCode]) ? _d($aRow[$colCode]) : '');
                } else if ($colCode == 'stt') {
                    $cellValue = ($key+1);
                } else {
                    $cellValue = (isset($aRow[$colCode]) ? $aRow[$colCode] : '');
                }

                if ($colCode == 'stock_quantity') {
                    $filter['item'] = $aRow['item_id'].'__nvl';
                    $cellValue = $this->stock_out_request_model->getItemStockQuantity($filter)['stock_quantity'] ?? 0;

                    $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$colIndex] . $excelRowNum, $cellValue)->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain);
                } else if ($colCode == 'image') {
                    $imageUrl = 'assets/images/tnh/no_image.png';

                    if (!empty($aRow['image'])) {
                        // $images = 'uploads/products/' . $value['image'];
                        $imageUrl = $aRow['image'];
                    }
                    
                    if (!empty($imageUrl)) {
                        $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                        $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                        $objDrawing1->setPath($imageUrl);
                        // $objDrawing1->setWidth(80);
                        $objDrawing1->setHeight(53);
                        $objDrawing1->setOffsetX(3);
                        $objDrawing1->setOffsetY(2);
                        $objDrawing1->setCoordinates($cloumns_excel[$colIndex] . $excelRowNum);
    
                        $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$colIndex] . $excelRowNum, '')->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objPHPExcel->getActiveSheet()->getRowDimension($excelRowNum)->setRowHeight(30);
                    }
                } else if ($colCode == 'qr') {
                    $code = 'stock_out_request||'.$aRow['stock_out_request_id'];

                    $qr = vn_to_str(str_replace('||', '__', $code));
                    $folder = FCPATH . 'uploads/stock_out_request/';
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
                    $params['savename'] = $folder.'qrcode/'. $qr . '.png';
                    $this->ciqrcode->generate($params);
                    $img = ($folder.'qrcode/'. $qr . '.png');
                    if (!empty($img)) {
                        $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                        $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                        $objDrawing1->setPath($img);
                        $objDrawing1->setWidth(80);
                        $objDrawing1->setHeight(53);
                        $objDrawing1->setOffsetX(3);
                        $objDrawing1->setOffsetY(2);
                        $objDrawing1->setCoordinates($cloumns_excel[$colIndex] . $excelRowNum);
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($excelRowNum)->setRowHeight(42);
                    $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$colIndex] . $excelRowNum, '')->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain);
                } else {
                    $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$colIndex] . $excelRowNum, $cellValue)->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain);
                }
            }
            $excelRowNum++;
        }

        $filename = 'Phieu_yeu_cau_xuat_kho_npl_ton' . '.xls';
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

    function selectOrder($id = null)
    {
        $result = [];
        $term = $this->input->get('term', TRUE);
        $params = $this->input->get('params', TRUE);
        // $limit = get_option('select2_limit');
        $limit = $this->input->get('limit', TRUE);
        // $limit = 50;
        $filter = [];
        $filter['search'] = $term;
        $filter['limit'] = $limit;
        $filter['production_order_id'] = $params['production_order_id'] ?? null;

        $result['results'] = $this->stock_out_request_model->getOrder($filter);
        if ($id) {
            $filter = [];
            $filter['id'] = $id;
            $result['row'] = $this->stock_out_request_model->getOrder($filter);
        }
        echo json_encode($result);
    }

    function selectItem($id = null)
    {
        $result = [];
        $term = $this->input->get('term', TRUE);
        $params = $this->input->get('params', TRUE);
        // $limit = get_option('select2_limit');
        $limit = $this->input->get('limit', TRUE);
        // $limit = 50;
        $filter = [];
        $filter['search'] = $term;
        $filter['limit'] = $limit;
        $filter['production_order_id'] = $params['production_order_id'] ?? null;
        $filter['order_id'] = $params['order_id'] ?? null;

        $result['results'] = $this->stock_out_request_model->getItemData($filter);
        if ($id) {
            $filter = [];
            $filter['id'] = $id;
            $result['row'] = $this->stock_out_request_model->getItemData($filter);
        }
        echo json_encode($result);
    }

    public function getItemStockQuantity() {
        $filter = [];
        $filter['item'] = $this->input->post('item', TRUE) ?? null;
        if (!empty($filter['item'])) {
            $item = explode('__', $filter['item']);
            $item_id = $item[0];
            $item_type = 'nvl';

            $filter['item'] = $item_id.'__'.$item_type;
        }
        $result = $this->stock_out_request_model->getItemStockQuantity($filter);
        echo json_encode($result);
    }
}
