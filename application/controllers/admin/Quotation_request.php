<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Quotation_request extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('quotation_request_model');
        $this->load->model('products_model');
    }

    public function index() {
        if ($this->input->is_ajax_request()) {
            $data = [];
            $this->app->get_table_data('quotation_request', $data);
        }

        $data['title'] = _l('quotation_request');
        $this->load->view('admin/quotation_request/manage', $data);
    }

    public function submit($id = '') {
        if ($this->input->post()) {
            $formData = $this->input->post();
            // var_dump($formData);die;
            $formData['client_id'] = ((explode('__', $formData['client_id'])[1]) ?? null);

            $result = $this->quotation_request_model->submit($formData, $id);
            if (!empty($result['submitId'])) {
                echo json_encode(['result'=>true, 'alert_type'=>'success', 'message'=>_l('Thành công')]);
            } else {
                echo json_encode(['result'=>false, 'alert_type'=>'danger', 'message'=>_l('Thất bại')]);
            }
        } else {
            $data['title'] = '';
            $data['arrBrand'] = get_table_where('tbl_brand');

            $data['title'] = 'Thêm ' . lang('quotation_request');
            if (!empty($id)) {
                $data['title'] = 'Sửa ' . lang('quotation_request');
                $data['value'] = $this->quotation_request_model->get($id);
                // var_dump($data['value']);die;
                $data['id'] = $id;
            }

            $data['breadcrumb'] = [array('link' => base_url('admin/quotation_request/'), 'page' => lang('quotation_request')), array('link' => '#', 'page' => $data['title'])];
            $this->load->view('admin/quotation_request/submit', $data);
        }
    }

    public function delete($id) {
        $result = $this->quotation_request_model->delete($id);
        echo json_encode($result); die();
    }

    function searchProductsSelect2($id = false)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');
        $data['results'] = $this->products_model->searchProductsSelect2($term, $limit);
        if ($id) {
            $product = $this->products_model->rowProduct($id);
            $data['row'] = ['id' => $product['id'], 'text' => $product['code']];
        }
        echo json_encode($data);
    }

    public function view($id){
        $data = [];
        $data['title'] = lang('quotation_request');

        $data['value'] = $this->quotation_request_model->get($id);

        $this->load->view('admin/quotation_request/view',$data);
    }

    public function export_excel() {
        $search_date = $this->input->post('search_date');
        $client_id = $this->input->post('client_id');

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

        $tb_customer_group = '(
            SELECT
                GROUP_CONCAT(tblcustomers_groups.name) as name,
                tblcustomer_groups.customer_id AS client_id
            FROM tblcustomer_groups
            INNER JOIN tblcustomers_groups ON tblcustomers_groups.id = tblcustomer_groups.groupid
            GROUP BY tblcustomer_groups.customer_id
        ) as tb_customer_group';
        $this->db->select('
            tblquotation_request.*,
            tblquotation_request_item.*,
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
            tblclients.company as client_name,
            tblclients.zcode as client_code,
            tb_customer_group.name as client_group,
        ');
        $this->db->from('tblquotation_request_item');
        $this->db->join('tblquotation_request', 'tblquotation_request.id = tblquotation_request_item.quotation_request_id', 'left');
        $this->db->join('tbl_products', 'tbl_products.id = tblquotation_request_item.item_id AND tblquotation_request_item.item_type = "products"', 'left');
        $this->db->join($tb_brand, 'FIND_IN_SET(tb_brand.id, tblquotation_request_item.brand_ids) > 0', 'left');
        $this->db->join('tbl_species', 'tbl_species.id = tbl_products.species', 'left');
        $this->db->join('tbl_category_products', 'tbl_category_products.id = tbl_products.category_id', 'left');
        $this->db->join('tblunits', 'tbl_products.unit_id = tblunits.unitid', 'left');
        $this->db->join('tblunits tb_unit_measure', 'tbl_products.unit_measure = tb_unit_measure.unitid', 'left');
        $this->db->join('tblclients', 'tblclients.userid = tblquotation_request.client_id', 'left');
        $this->db->join($tb_customer_group, 'tb_customer_group.client_id = tblquotation_request.client_id', 'left');
        $this->db->group_by('tblquotation_request_item.id');

        if (!empty($search_date)) {
            $searchDate = explode(' - ', $search_date);
            $this->db->where('tblquotation_request.date BETWEEN "' . to_sql_date($searchDate[0]) . ' 00:00:00" and "' . to_sql_date($searchDate[1]) . ' 23:59:59"');
        }
        if (!empty($client_id)) {
            $client = explode('__', $client_id)[1] ?? null;
            if (!empty($client)) {
                $this->db->where('tblquotation_request.client_id = ' . $client);
            }
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
            'client_group' => ucwords(_l('Phân loại khách hàng')),
            'client_code' => ucwords(_l('Mã khách hàng')),
            'client_name' => ucwords(_l('Tên Khách hàng')),
            'category_name' => ucwords(_l('Tên nhóm SP')),
            'specie_name' => ucwords(_l('Tên chủng loại')),
            'unit' => (_l('ĐV tính SP')),
            'height' => ucwords(_l('Height')),
            'width' => ucwords(_l('Width')),
            'unit_measure' => ucwords(_l('ĐV Đo SP')),
            'item_code' => ucwords(_l('Mã thành phẩm')),
            'item_name' => ucwords(_l('Tên thành phẩm')),
            'packing' => ucwords(_l('Tiêu chuẩn Đóng gói')),
            'max_quantity' => ucwords(_l('Số lượng tồn cho phép')),
            'time_inventory' => ucwords(_l('Thời gian tồn kho')),
            'quota_time_change_one' => ucwords(_l('Định mức thời gian')),
            'image' => 'Hình Ảnh SP',
            'qr' => ('QR'),
        ];
        $aColumns = array_keys($colName);
        $arrNotMergeCell = [ // Không Merge dòng
            'height',
            'width',
        ];
        $arrMergeCol = [ // Merge cột
            'Kích Thước SP' => 'height||width',
        ];

        $excelRowNum = 1;
        $maxCol = count($colName) - 1;
        $objPHPExcel->getActiveSheet()->mergeCells('A'.($excelRowNum).':'.$cloumns_excel[$maxCol].$excelRowNum);
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$excelRowNum, ('PHIẾU YÊU CẦU BÁO GIÁ'))->getStyle("A".$excelRowNum)->applyFromArray($styleTitle);
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

            if (!in_array($value, $arrNotMergeCell)) { // Mặc định là merge dòng
                $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$key] . $excelRowNum, ($colName[$value]))->getStyle($cloumns_excel[$key].($excelRowNum).':'.$cloumns_excel[$key].($excelRowNum+$mergeRowNum))->applyFromArray($styleHeader); // Điền Tiêu đề
                $objPHPExcel->getActiveSheet()->mergeCells($cloumns_excel[$key].($excelRowNum).':'.$cloumns_excel[$key].($excelRowNum+$mergeRowNum));
            } else {
                $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$key].($excelRowNum+$mergeRowNum), ($colName[$value]))->getStyle($cloumns_excel[$key].($excelRowNum+$mergeRowNum))->applyFromArray($styleHeader);
            }
            if ($value == 'item_name') {
                $width = 55;
            } else if ($value == 'brand_name') {
                $width = 40;
            } else {
                $width = $objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$key])->getWidth();
            }
            $objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$key])->setWidth($width);
            // $objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$key])->setAutoSize(true);
        }
        foreach($arrMergeCol as $mergeTitle => $mergeCol) {
            $arrCol = explode('||', $mergeCol);
            $colNumStart = array_search($arrCol[0], $aColumns);
            $colNumEnd = array_search($arrCol[1], $aColumns);
            $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$colNumStart].($excelRowNum), ($mergeTitle))->getStyle($cloumns_excel[$colNumStart].($excelRowNum).':'.$cloumns_excel[$colNumEnd].($excelRowNum))->applyFromArray($styleHeader);
            $objPHPExcel->getActiveSheet()->mergeCells($cloumns_excel[$colNumStart].($excelRowNum).':'.$cloumns_excel[$colNumEnd].($excelRowNum));
        }

        $excelRowNum = 4;
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
                    
                    if (!empty($imageUrl)) {
                        $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                        $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                        $objDrawing1->setPath($imageUrl);
                        $objDrawing1->setWidth(30);
                        $objDrawing1->setHeight(30);
                        $objDrawing1->setOffsetX(20);
                        $objDrawing1->setOffsetY(5);
                        $objDrawing1->setCoordinates($cloumns_excel[$colIndex] . $excelRowNum);
    
                        $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$colIndex] . $excelRowNum, '')->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objPHPExcel->getActiveSheet()->getRowDimension($excelRowNum)->setRowHeight(30);
                    }
                } else {
                    $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$colIndex] . $excelRowNum, $cellValue)->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain);
                }
            }
            $excelRowNum++;
        }

        $filename = 'Phieu_yeu_cau_bao_gia' . '.xls';
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