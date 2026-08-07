<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Production_order_request extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('production_order_request_model');
        // $this->load->model('products_model');
    }

    public function index() {
        if ($this->input->is_ajax_request()) {
            $data = [];
            $this->app->get_table_data('production_order_request', $data);
        }

        $data['title'] = _l('production_order_request');
        $this->load->view('admin/production_order_request/index', $data);
    }

    public function submit($id = '') {
        if ($this->input->post()) {
            $formData = $this->input->post();
            // var_dump($formData);die;

            $result = $this->production_order_request_model->submit($formData, $id);
            if (!empty($result['submitId'])) {
                echo json_encode(['result'=>true, 'alert_type'=>'success', 'message'=>_l('Thành công')]);
            } else {
                echo json_encode(['result'=>false, 'alert_type'=>'danger', 'message'=>_l('Thất bại')]);
            }
        } else {
            $data['title'] = '';
            $data['arrBrand'] = get_table_where('tbl_brand');

            $data['title'] = 'Thêm ' . lang('production_order_request');
            if (!empty($id)) {
                $data['title'] = 'Sửa ' . lang('production_order_request');
                $data['value'] = $this->production_order_request_model->get($id);
                $data['id'] = $id;
            }

            $data['breadcrumb'] = [array('link' => base_url('admin/production_order_request/'), 'page' => lang('production_order_request')), array('link' => '#', 'page' => $data['title'])];
            $this->load->view('admin/production_order_request/submit', $data);
        }
    }

    public function view($id){
        $data = [];
        $data['title'] = lang('production_order_request');

        $data['value'] = $this->production_order_request_model->get($id);

        $this->load->view('admin/production_order_request/view',$data);
    }

    public function delete($id) {
        $result = $this->production_order_request_model->delete($id);
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
            tblproduction_order_request.*,
            tblproduction_order_request_item.*,
            IF(tbl_products.images IS NOT NULL && tbl_products.images != "", CONCAT("uploads/products/", "", tbl_products.images, ""), "") as image,
            tbl_products.code as item_code,
            tbl_products.name as item_name,
            tbl_products.height as height,
            tbl_products.wide as wide,
            tbl_products.packing as packing,
            tbl_products.quantity_max as quantity_max,
            tbl_products.time_inventory as time_inventory,
            tbl_products.quota_time_change_one as quota_time_change_one,
            GROUP_CONCAT(tb_brand.name SEPARATOR ", ") as brand_name,
            tbl_species.name as specie_name,
            tbl_category_products.name as category_name,
            tb_unit_measure.unit as unit_measure,
            tblunits.unit as unit_name,
        ');
        $this->db->from('tblproduction_order_request_item');
        $this->db->join('tblproduction_order_request', 'tblproduction_order_request.id = tblproduction_order_request_item.production_order_request_id', 'left');
        $this->db->join('tbl_products', 'tbl_products.id = tblproduction_order_request_item.item_id AND tblproduction_order_request_item.item_type = "products"', 'left');
        $this->db->join($tb_brand, 'FIND_IN_SET(tb_brand.id, tblproduction_order_request_item.brand_ids) > 0', 'left');
        $this->db->join('tbl_species', 'tbl_species.id = tbl_products.species', 'left');
        $this->db->join('tbl_category_products', 'tbl_category_products.id = tbl_products.category_id', 'left');
        $this->db->join('tblunits', 'tbl_products.unit_id = tblunits.unitid', 'left');
        $this->db->join('tblunits tb_unit_measure', 'tbl_products.unit_measure = tb_unit_measure.unitid', 'left');
        $this->db->group_by('tblproduction_order_request_item.id');

        if (!empty($search_date)) {
            $searchDate = explode(' - ', $search_date);
            $this->db->where('tblproduction_order_request.date BETWEEN "' . to_sql_date($searchDate[0]) . ' 00:00:00" and "' . to_sql_date($searchDate[1]) . ' 23:59:59"');
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
            'brand_name' => ucwords(_l('Tên Brand')),
            'item_code' => ucwords(_l('Mã thành phẩm')),
            'item_name' => ucwords(_l('Tên thành phẩm')),
            'category_name' => ucwords(_l('Tên Nhóm SP')),
            'specie_name' => ucwords(_l('Tên Mã Chủng Loại SP')),
            'time_norm' => ucwords(_l('Định mức thời gian')),
            'image' => 'Hình Ảnh SP',
            'qr' => ('QR'),
        ];
        $aColumns = array_keys($colName);

        $title = mb_strtoupper(_l('production_order_request'));
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

                if ($colCode == 'image') {
                    $imageUrl = 'assets/images/tnh/no_image.png';
                    if (!empty($aRow['image'])) {
                        // $images = 'uploads/products/' . $value['image'];
                        $imageUrl = $aRow['image'];
                    }
                    if (!empty($imageUrl) && file_exists($imageUrl)) {
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
                    $code = 'production_order_request||'.$aRow['production_order_request_id'];

                    $qr = vn_to_str(str_replace('||', '__', $code));
                    $folder = FCPATH . 'uploads/production_order_request/';
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

        $filename = 'Phieu_yeu_cau_mo_lenh' . '.xls';
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
