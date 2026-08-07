<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Quote_shipping_unit extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->preView = true;
        $this->preViewOwn = true;
        $this->preAdd = true;
        $this->preEdit = true;
        $this->preApprove = true;
        $this->preDelete = true;
    }

    public function index() {
        if (!$this->preView && !$this->preViewOwn) {
            access_denied();
        }
        $data['title'] = 'Danh Sách Báo Giá Vận Chuyển';
        $this->load->view('admin/quote_shipping_unit/manage', $data);
    }

    public function table()
    {
        $aColumns = [
            'tbl_quote_shipping_unit.id as id',
            'tbl_quote_shipping_unit.date as date',
            'tblsuppliers.code as code_supplier',
            'tblsuppliers.company as company',
            'tbl_quote_shipping_unit.code as code',
            'tbl_quote_shipping_unit.name as name',
            'tblunits.unit as unit',
            'tbl_quote_shipping_unit.price as price',
            'tbl_quote_shipping_unit.name as currencies',
            'tbl_quote_shipping_unit.status as status',
            'tbl_quote_shipping_unit.create_by as create_by',
        ];
        $sWhere = [];
        $join = [
            'LEFT JOIN tblsuppliers ON tblsuppliers.id = tbl_quote_shipping_unit.id_supplier',
            'LEFT JOIN tblunits ON tblunits.unitid = tbl_quote_shipping_unit.unit_id',
            'LEFT JOIN tblcurrencies ON tblcurrencies.id = tbl_quote_shipping_unit.id_currencies',
        ];
        if($this->input->post('materials_search')) {
            $sWhere[] = 'AND tbl_materials.id = "'.$this->input->post('materials_search').'"';
        }
        if($this->input->post('stage_search')) {
            $sWhere[] = 'AND tbl_stages.id = "'.$this->input->post('stage_search').'"';
        }
        $sIndexColumn = 'id';
        $sTable       = 'tbl_quote_shipping_unit';
        $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $sWhere, [
            'staff_status'
        ]);
        $output       = $result['output'];
        $rResult      = $result['rResult'];
        foreach ($rResult as $key =>  $aRow) {
            $row = [];
            $row[] = ($key + 1);
            $row[] = _dt($aRow['date']);
            $row[] = $aRow['code_supplier'];
            $row[] = $aRow['company'];
            $row[] = $aRow['code'];
            $row[] = $aRow['name'];
            $row[] = $aRow['unit'];
            $row[] = number_format_data($aRow['price']);
            $row[] = $aRow['currencies'];
            if ($aRow['status'] == 0) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="'.lang('tnh_agree').'" data-content="<p><a onclick=\'agree(this, '.$aRow['id'].', 1)\' id=\'agree\' id=\''.$aRow['id'].'\' value=\'1\' class=\'btn btn-success\'>'.lang('tnh_agree').'</a><button class=\'btn po-close\'>'.lang('close').'</button></p>" class="label label-danger po">'.lang('Chưa duyệt').'</span></div>';
            } else if ($aRow['status'] == 1) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="'.lang('tnh_agree').'" data-content="<p><a onclick=\'agree(this, '.$aRow['id'].', 0)\' id=\'agree\' id=\''.$aRow['id'].'\' value=\'0\' class=\'btn btn-danger\'>'.lang('Hủy duyệt').'</a><button class=\'btn po-close\'>'.lang('close').'</button></p>" class="label label-success po">'.lang('Đã duyệt').'</span></div>';
                $_data.= '<div style="margin-top: 5px"> Người duyệt: '.get_staff_full_name($aRow['staff_status']).'</div>';
            } else {
                $_data = '';
            }
            $row[] = $_data;

            $fullname_CREATE = get_staff_full_name($aRow['create_by']);
            $profile_CREATE = '<a data-toggle="tooltip" data-title="' . $fullname_CREATE . '" href="' . admin_url('profile/' . $aRow['create_by']) . '">' . staff_profile_image($aRow['create_by'], [
                    'staff-profile-image-small',
                ]) . '</a>';
            $row[] = $profile_CREATE . ' ' . $fullname_CREATE;

            $options = '';
            $options .= '<a class="btn btn-icon btn-default c_modal" href="'.admin_url('quote_shipping_unit/detail/' . $aRow['id']).'"><i class="fa fa-edit"></i></a>';
            $options .= '<a class="btn btn-icon btn-danger c_delete" href="'.admin_url('quote_shipping_unit/delete').'" data-id="'.$aRow['id'].'"><i class="fa fa-remove"></i></a>';
            $row[] = $options;
            $output['aaData'][] = $row;
        }
        echo json_encode($output);die();
    }

    public function detail($id = '')
    {
        if($this->input->post()) {
            $data = $this->input->post();
            if(!empty($id)) {
                $this->db->where('id', $id);
                $quote_shipping_unit = $this->db->get('tbl_quote_shipping_unit')->row();
                if(!empty($quote_shipping_unit->status)) {
                    echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => _l('Phiếu đã duyệt không thể chỉnh sửa')]);die();
                }

                $dataUpdate = [
                    'id_supplier' => !empty($data['id_supplier']) ? $data['id_supplier'] : NULL,
                    'date' => to_sql_date($data['date'], true),
                    'name' => $data['name'],
                    'unit_id' => $data['unit_id'],
                    'price' => number_format_data($data['price'], false),
                    'id_currencies' => $data['id_currencies'],
                    'note' => $this->input->post('note', false),
                ];

                $this->db->where('id', $id);
                $success = $this->db->update('tbl_quote_shipping_unit', $dataUpdate);
                if(!empty($success)) {
                    echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => _l('cong_update_true')]);die();
                }

                echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => _l('cong_update_false')]);die();
            }
            else {
                $dataInsert = [
                    'id_supplier' => !empty($data['id_supplier']) ? $data['id_supplier'] : NULL,
                    'date' => to_sql_date($data['date'], true),
                    'code' => 'BGC-' . date('ymdhis'),
                    'name' => $data['name'],
                    'unit_id' => $data['unit_id'],
                    'price' => number_format_data($data['price'], false),
                    'id_currencies' => $data['id_currencies'],
                    'create_by' => get_staff_user_id(),
                    'note' => $this->input->post('note', false),
                ];
                $success = $this->db->insert('tbl_quote_shipping_unit', $dataInsert);
                if(!empty($success)) {
                    echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => _l('cong_update_true')]);die();
                }

                echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => _l('cong_update_false')]);die();
            }
        }
        else {
            $data['title'] = 'Thêm Báo Giá Vận Chuyển';
            if(!empty($id)) {
                $data['title'] = 'Sửa Báo Giá Vận Chuyển';
                $data['quote_shipping_unit'] = $this->db->get_where('tbl_quote_shipping_unit', array('id' => $id))->row();
            }
            $data['list_suppliers'] = $this->db->get('tblsuppliers')->result_array();
            $data['list_unit'] = $this->db->get('tblunits')->result_array();
            $data['list_currencies'] = $this->db->get('tblcurrencies')->result_array();
            $this->load->view('admin/quote_shipping_unit/detail', $data);
        }

    }

    public function update_status()
    {
        $id = $this->input->post('id');
        $status = $this->input->post('status');
        if($status == 1) {
            $this->db->where('id', $id);
            $success = $this->db->update('tbl_quote_shipping_unit', [
                'status' => 1,
                'staff_status' => get_staff_user_id(),
            ]);
            if(!empty($success)) {
                echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Duyệt thành công']);die();
            }
            echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Duyệt không thành công']);die();
        }
        else {
            $this->db->where('id', $id);
            $success = $this->db->update('tbl_quote_shipping_unit', [
                'status' => 0,
                'staff_status' => 0,
            ]);
            if(!empty($success)) {
                echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Bỏ duyệt thành công']);die();
            }
            echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Bỏ duyệt không thành công']);die();
        }
    }

    public function delete()
    {
        $id = $this->input->post('id');
        if(!empty($id))
        {
            $this->db->where('id', $id);
            $quote_supping_unit = $this->db->get('tbl_quote_shipping_unit')->row();
            if(!empty($quote_supping_unit)) {
                if($quote_supping_unit->status == 1) {
                    echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Phiếu đã duyệt không thể xóa']);die();
                }
                $this->db->where('id', $id);
                $success = $this->db->delete('tbl_quote_shipping_unit');
                if(!empty($success)) {
                    echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Xóa phiếu thành công']);die();
                }
            }
        }
        echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Xóa phiếu không thành công']);die();
    }


    public function exportExcel(){
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
                'tbl_quote_shipping_unit.id as id',
                'tbl_quote_shipping_unit.date as date',
                'tblsuppliers.code as code_supplier',
                'tblsuppliers.company as company',
                'tbl_quote_shipping_unit.code as code',
                'tbl_quote_shipping_unit.name as name',
                'tblunits.unit as unit',
                'tbl_quote_shipping_unit.price as price',
                'tbl_quote_shipping_unit.name as currencies',
                'tbl_quote_shipping_unit.status as status',
                'tbl_quote_shipping_unit.create_by as create_by',
                'tbl_quote_shipping_unit.note as note',
            ]);
            $this->db->from('tbl_quote_shipping_unit');

//            'LEFT JOIN tblsuppliers ON tblsuppliers.id = tbl_quote_shipping_unit.id_supplier',
//            'LEFT JOIN tblunits ON tblunits.unitid = tbl_quote_shipping_unit.unit_id',
//            'LEFT JOIN tblcurrencies ON tblcurrencies.id = tbl_quote_shipping_unit.id_currencies',
            //            $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_suggest_plan_outsource.po_id', 'left');
            $this->db->join('tblsuppliers', 'tblsuppliers.id = tbl_quote_shipping_unit.id_supplier', 'left');
            $this->db->join('tblunits', 'tblunits.unitid = tbl_quote_shipping_unit.unit_id', 'left');

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_plan_outsource.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_plan_outsource.date <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tbl_quote_shipping_unit.id desc');
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
            $objPHPExcel->getActiveSheet()->setCellValue('A1',
                ('DANH SÁCH BÁO GIÁ VẬN CHUYỂN'))->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Ngày Chứng Từ');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Mã Nhà Cung Cấp');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Tên Nhà Cung Cấp');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Mã Chuyến');
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Tên Chuyến');
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'ĐVT');
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Đơn Giá')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Đơn Vị Tiền Tệ')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Nhân Viên Tạo')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Ghi Chú')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:J$sttRow")->applyFromArray([
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
            $rowBegin = $sttRow;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", _dt($value['date']));
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['code_supplier']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", ($value['company']))->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['code']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", ($value['name']))->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $value['unit'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", $value['price'])->getStyle("H$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['price']));
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $value['currencies'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", get_staff_full_name($value['create_by']))->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", ($value['note']))->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:K$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:A$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("L$rowBegin:L$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("G$rowBegin:G$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                }
            }
            $filename = lang('bao_gia_van_chuyen') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(45);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(50);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(20);
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
}