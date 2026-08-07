<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Moderation_outsource extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');
        $this->load->model('category_model');
        $this->load->model('manufactures_model');
        $this->load->model('business_plan_model');
        $this->load->model('orders_model');
        $this->load->model('tools_supplies_model');

        $this->preViewModerationOutsource = true;
        $this->preViewOwnModerationOutsource = true;
        $this->preEditModerationOutsource  = true;
    }

    public function index()
    {
        if (!$this->preViewModerationOutsource && !$this->preViewOwnModerationOutsource) {
            access_denied();
        }
        $data['title'] = lang('dt_moderation_outsource');
        $this->load->view('admin/moderation_outsource/index', $data);
    }

    public function getModerationOutsources()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');


        $aColumns = [
            'tbl_suggest_outsource.id as id',
            'tbl_suggest_outsource.reference_no as reference_no',
            'tbl_suggest_outsource.date as date',
            'tblsuppliers.company as name_supplier',
            'tbl_suggest_outsource.date_request as date_request',
            'tbl_suggest_outsource.date_delivery as date_delivery',
            'tbl_suggest_outsource.date_go_expected as date_go_expected',
            'tbl_suggest_outsource.date_satisfied_expected as date_satisfied_expected',
            'tbl_orders.reference_no as reference_no_order',
            'tbl_suggest_outsource_item.item_id as item_id',
            'tbl_suggest_outsource_item.type_item as type_item',
            'tbl_suggest_outsource_item.sltin as sltin',
            'tbl_suggest_outsource_item.type_material as type_material',
            'tbl_suggest_outsource_item.quantity_compensation as quantity_compensation',
            'tbl_suggest_outsource_item.quantity_compensation_more as quantity_compensation_more',
            'tbl_suggest_outsource_item.landscape_print_size as landscape_print_size',
            'tbl_stages.name as name_stage',
            'tbl_suggest_outsource_item.number_of_printed_sides as number_of_printed_sides',
            'tbl_suggest_outsource_item.color_number_a as color_number_a',
            'tbl_suggest_outsource_item.color_number_b as color_number_b',
            'tbl_suggest_outsource_item.zinc_number_a as zinc_number_a',
            'tbl_suggest_outsource_item.zinc_number_b as zinc_number_b',
            'tbl_suggest_outsource_item.grape as grape',
            'tbl_suggest_outsource_item.image_mucin as image_mucin',
            'tbl_suggest_outsource_item.image_bongmo as image_bongmo',
            'tbl_suggest_outsource_item.note as note_items'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_outsource';
        $where = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tbl_suggest_outsource_item ON tbl_suggest_outsource_item.suggest_plan_outsource_id = tbl_suggest_outsource.id',
            'LEFT JOIN tbl_orders ON tbl_orders.id = tbl_suggest_outsource_item.order_id AND tbl_suggest_outsource_item.object_type = "order"',
            'LEFT JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_suggest_outsource_item.order_id AND tbl_suggest_outsource_item.object_type = "po"',
            'INNER JOIN tblsuppliers ON tblsuppliers.id = tbl_suggest_outsource_item.supplier_id',
            'LEFT JOIN tblsuppliers_groups ON tblsuppliers_groups.id = tblsuppliers.groups_in',
            'LEFT JOIN tbl_stages ON tbl_stages.id = tbl_suggest_outsource_item.stage_id',
            'LEFT JOIN tbltaxes ON tbltaxes.id = tbl_suggest_outsource_item.tax_id',
            'LEFT JOIN tbl_result ON tbl_result.id = tbl_suggest_outsource_item.result_id',
        ];

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_suggest_outsource.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_suggest_outsource.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_productions_orders.reference_no as reference_no_po',
            'tbl_suggest_outsource_item.material as material',
            'tbl_suggest_outsource_item.print as print',
            'tbl_suggest_outsource.object_type as object_type'
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $suggest_outsource_id = $aRow['id'];
            $item_id = $aRow['item_id'];
            $type_item = $aRow['type_item'];
            $info = null;
            if ($type_item == "products") {
                $info = $this->products_model->rowProduct($item_id);
            }
            $images = base_url('assets/images/tnh/no_image.png');
            if (!empty($info['images'])) {
                $images = base_url('uploads/products/' . $info['images']);
            }

            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="min-width: 120px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_outsource/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . ($aRow['reference_no']) . '</a></div>';
            $row[] = '<div class="text-left" style="min-width: 100px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left" style="min-width: 100px">' . ($aRow['name_supplier']) . '</div>';
            $row[] = '<div class="text-left" style="min-width: 100px">' . _d($aRow['date_request']) . '</div>';
            $row[] = '<div class="text-left" style="min-width: 100px">' . _d($aRow['date_delivery']) . '</div>';
            $row[] = '<div class="text-left" style="min-width: 100px">' . _d($aRow['date_go_expected']) . '</div>';
            $row[] = '<div class="text-left" style="min-width: 100px">' . _d($aRow['date_satisfied_expected']) . '</div>';
            $reference_no_object = '';
            if ($aRow['object_type'] == 'po'){
                $reference_no_object = $aRow['reference_no_po'];
            } else {
                $reference_no_object = $aRow['reference_no_order'];
            }
            $row[] = '<div class="text-left" style="min-width: 100px">' . $reference_no_object . '</div>';
            $row[] = '<div class="text-left" style="min-width: 100px">' . ($info['code']) . '</div>';
            $row[] = '<div class="text-left" style="min-width: 100px">' . ($info['name']) . '</div>';
            $row[] = '<div class="text-center">' . formatNumber($aRow['sltin']) . '</div>';
            $type_material = $aRow['type_material'];
            if ($type_material == "materials") {
                $info_material = $this->items_model->rowMaterial($aRow['material']);
            } else if ($type_material == "tools_supplies") {
                $info_material = $this->tools_supplies_model->rowToolsSupplies($item_id);
            } else {
                $info_material = $this->products_model->rowProduct($item_id);
            }
            $row[] = '<div class="text-left">' . ($info_material['name']) . '</div>';
            $row[] = '<div class="text-center">' . formatNumber($aRow['quantity_compensation']) . '</div>';
            $row[] = '<div class="text-center">' . formatNumber($aRow['quantity_compensation_more']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['landscape_print_size']) . '</div>';
            $row[] = '<div class="text-left"><div class="td-image">
                <div class="preview_image" style="width: auto;">
                    <div class="display-block contract-attachment-wrapper img">
                        <div style="width:45px; margin: auto;"><a href="' . $images . '" data-lightbox="customer-profile" class="display-block mbot5">
                                <div class=""><img src="' . $images . '" style="border-radius: 50%"></div>
                            </a></div>
                    </div>
                </div>
            </div></div>';
            $row[] = '<div class="text-left">'.$aRow['name_stage'].'</div>';
            $print_text = '';
            $print = [
                [
                    'id' => 1,
                    'name' => 'In A-B',
                ],
                [
                    'id' => 2,
                    'name' => 'In Trở',
                ],
                [
                    'id' => 3,
                    'name' => 'In 1 mặt',
                ]
            ];
            foreach ($print as $kk => $vv) {
                if ($vv['id'] == $aRow['print']) {
                    $print_text = $vv['name'];
                    break;
                }
            }
            $row[] = '<div class="text-left" style="min-width: 100px">' . $print_text . '</div>';
            $row[] = '<div class="text-left" style="min-width: 100px">' . formatNumber($aRow['number_of_printed_sides']) . '</div>';
            $row[] = '<div class="text-left" style="min-width: 100px">' . formatNumber($aRow['color_number_a']) . '</div>';
            $row[] = '<div class="text-left" style="min-width: 100px">' . formatNumber($aRow['color_number_b']) . '</div>';
            $row[] = '<div class="text-left" style="min-width: 100px">' . formatNumber($aRow['zinc_number_a']) . '</div>';
            $row[] = '<div class="text-left" style="min-width: 100px">' . formatNumber($aRow['zinc_number_b']) . '</div>';
            $row[] = '<div class="text-left" style="min-width: 100px">' . formatNumber($aRow['grape']) . '</div>';
            $image_mucin = base_url('assets/images/tnh/no_image.png');
            if (!empty($aRow['image_mucin'])) {
                $image_mucin = base_url($aRow['image_mucin']);
            }
            $image_bongmo = base_url('assets/images/tnh/no_image.png');
            if (!empty($aRow['image_bongmo'])) {
                $image_bongmo = base_url($aRow['image_bongmo']);
            }

            $row[] = '<div class="text-left">
            <div class="td-image">
                <div class="preview_image" style="width: auto;">
                    <div class="display-block contract-attachment-wrapper img">
                        <div style="width:45px; margin: auto;"><a href="' . $image_mucin . '" data-lightbox="customer-profile" class="display-block mbot5">
                                <div class=""><img src="' . $image_mucin . '" style="border-radius: 50%"></div>
                            </a></div>
                    </div>
                </div>
            </div>
            </div>';
            $row[] = '<div class="text-left">
            <div class="td-image">
                <div class="preview_image" style="width: auto;">
                    <div class="display-block contract-attachment-wrapper img">
                        <div style="width:45px; margin: auto;"><a href="' . $image_bongmo . '" data-lightbox="customer-profile" class="display-block mbot5">
                                <div class=""><img src="' . $image_bongmo . '" style="border-radius: 50%"></div>
                            </a></div>
                    </div>
                </div>
            </div>
            </div>';
            $row[] = '<div class="text-left">' . ($aRow['note_items']) . '</div>';
            foreach (getListColumTable() as $kk => $vv) {
                $_data = getDataModeration($aRow['id'],$vv['id'],'tbl_suggest_outsource');
                $row[] = '<div class="text-center">'.$_data.'</div>';
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function updateModeration()
    {
        $data = [];
        if (!$this->preEditModerationOutsource) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $suggest_outsource_id = $this->input->post('suggest_outsource_id');
        $suggest_outsource_item_id = $this->input->post('suggest_outsource_item_id');
        $name = $this->input->post('name');
        $value = $this->input->post('value');
        if ($name == 'date_start' || $name == 'date_end') {
            if (!empty($value)) {
                $value = to_sql_date($value, true);
            } else {
                $value = null;
            }
        } elseif ($name == 'time_expected' || $name == 'quota_time') {
            if (!empty($value)) {
                $value = number_unformat($value);
            } else {
                $value = 0;
            }
        }

        $this->db->from('tbl_moderation_outsource');
        $this->db->where('tbl_moderation_outsource.suggest_outsource_id', $suggest_outsource_id);
        $this->db->where('tbl_moderation_outsource.suggest_outsource_item_id', $suggest_outsource_item_id);
        $dtData = $this->db->get()->row_array();
        if (!empty($dtData)) {
            $this->db->where('tbl_moderation_outsource.id', $dtData['id']);
            $success = $this->db->update('tbl_moderation_outsource', [
                $name => $value
            ]);
        } else {
            $success = $this->db->insert('tbl_moderation_outsource', [
                'suggest_outsource_id' => $suggest_outsource_id,
                'suggest_outsource_item_id' => $suggest_outsource_item_id,
                $name => $value
            ]);
        }
        if ($success) {
            $data['result'] = 1;
            $data['message'] = lang('Thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Thất bại');
        }
        echo json_encode($data);
    }

    public function exportExcel()
    {
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            // print_arrays($this->input->post());
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();
            $this->db->select('
                tbl_suggest_outsource.id as id,
                tbl_suggest_outsource.reference_no as reference_no,
                tbl_suggest_outsource.date as date,
                tbl_suggest_outsource.date_request as date_request,
                tbl_suggest_outsource.date_delivery as date_delivery,
                tbl_suggest_outsource.date_go_expected as date_go_expected,
                tbl_suggest_outsource.date_satisfied_expected as date_satisfied_expected,
                tbl_orders.reference_no as reference_no_order,
                tbl_productions_orders.reference_no as reference_no_po,
                tbl_suggest_outsource.object_type as object_type,
                tbl_stages.name as name_stage,
                tbl_suggest_outsource_item.quantity as quantity,
                tbl_suggest_outsource_item.time_expected as time_expected,
                tbl_suggest_outsource_item.date_start_outsource as date_start_outsource,
                tbl_suggest_outsource_item.date_end_outsource as date_end_outsource,
                tblsuppliers_groups.name as name_supplier_group,
                tblsuppliers.company as name_supplier,
                tbl_suggest_outsource_item.price as price,
                tbltaxes.name as name_tax,
                tbl_suggest_outsource_item.grand_total as grand_total,
                tbl_suggest_outsource_item.shipping_unit_outsource as shipping_unit_outsource,
                tbl_suggest_outsource_item.transport_outsource as transport_outsource,
                tbl_suggest_outsource_item.price_transport as price_transport,
                tbl_suggest_outsource_item.amount_transport as amount_transport,
                tbl_result.name as name_result,
                (SELECT GROUP_CONCAT(tblproduction_report.name_report)
                 FROM tblproduction_report
                 WHERE tblproduction_report.object_id = tbl_suggest_outsource.id AND tblproduction_report.object_type = "suggest_outsource"
                ) as name_report,
                tbl_suggest_outsource.staff_plan as staff_plan,
                tbl_suggest_outsource_item.staff_id as staff_id,
                tbl_suggest_outsource_item.item_id as item_id,
                tbl_suggest_outsource_item.type_item as type_item,
                tbl_suggest_outsource_item.sltin as sltin,
                tbl_suggest_outsource_item.quantity_compensation as quantity_compensation,
                tbl_suggest_outsource_item.quantity_compensation_more as quantity_compensation_more,
                tbl_suggest_outsource_item.landscape_print_size as landscape_print_size,

                tbl_suggest_outsource_item.print as print,
                tbl_suggest_outsource_item.number_of_printed_sides as number_of_printed_sides,
                tbl_suggest_outsource_item.color_number_a as color_number_a,
                tbl_suggest_outsource_item.color_number_b as color_number_b,
                tbl_suggest_outsource_item.zinc_number_a as zinc_number_a,
                tbl_suggest_outsource_item.zinc_number_b as zinc_number_b,
                tbl_suggest_outsource_item.grape as grape,
                tbl_suggest_outsource_item.image_mucin as image_mucin,
                tbl_suggest_outsource_item.image_bongmo as image_bongmo,
                tbl_suggest_outsource_item.note as note_items,
                tbl_suggest_outsource_item.type_material as type_material,
                tbl_suggest_outsource_item.material as material,
            ');

            $this->db->from('tbl_suggest_outsource');
            $this->db->join('tbl_suggest_outsource_item', 'tbl_suggest_outsource_item.suggest_plan_outsource_id = tbl_suggest_outsource.id');
            $this->db->join('tbl_orders', 'tbl_orders.id = tbl_suggest_outsource_item.order_id AND tbl_suggest_outsource_item.object_type = "order"', 'left');
            $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_suggest_outsource_item.order_id AND tbl_suggest_outsource_item.object_type = "po"', 'left');
            $this->db->join('tblsuppliers', 'tblsuppliers.id = tbl_suggest_outsource_item.supplier_id', 'left');
            $this->db->join('tblsuppliers_groups', 'tblsuppliers_groups.id = tblsuppliers.groups_in', 'left');
            $this->db->join('tbl_stages', 'tbl_stages.id = tbl_suggest_outsource_item.stage_id', 'left');
            $this->db->join('tbltaxes', 'tbltaxes.id = tbl_suggest_outsource_item.tax_id', 'left');
            $this->db->join('tbl_result', 'tbl_result.id = tbl_suggest_outsource_item.result_id', 'left');

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_outsource.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_outsource.date <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tbl_suggest_outsource.id desc');
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
            $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf(
                        "%0" . $decimals_number . "s",
                        0
                    ) : '');
            $company = get_option('invoice_company_name');
            $address = get_option('invoice_company_address');
            $company_vat = get_option('company_vat');
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name'  => 'Times New Roman'
                ),
            ]);
            $objPHPExcel->getActiveSheet()->setCellValue(
                'A1',
                ('PHIẾU ĐIỀU ĐỘ CÔNG ĐOẠN GIA CÔNG')
            )->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:AB1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow . '', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $sttRow . '', 'Mã Số Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow . '', 'Ngày Lập');
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow . '', 'Nhà Gia Công');
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow . '', 'Ngày Gửi Yêu Cầu Gia Công')->getStyle("E$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $sttRow . '', 'Ngày Giao Hàng');
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $sttRow . '', 'Ngày Đưa Đi Dự Kiến')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $sttRow . '', 'Ngày Về Dự Kiến');
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $sttRow . '', 'Lệnh Sản Xuất/ Đơn hàng')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $sttRow . '', 'Mã Sản Phầm')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $sttRow . '', 'Tên Sản Phầm');
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $sttRow . '', 'Số Lượng Tờ In')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $sttRow . '', 'NVL In')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $sttRow . '', 'Số Lượng Bù Hao (Tờ In)')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O' . $sttRow . '', 'Số Lượng Bù Hao Xuất Thêm (Tờ In)')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P' . $sttRow . '', 'Khổ In (cm)')->getStyle("P$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Q' . $sttRow . '', 'Hình Ảnh')->getStyle("Q$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('R' . $sttRow . '', 'Loại Hình Phủ')->getStyle("R$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('S' . $sttRow . '', 'Cách In')->getStyle("S$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('T' . $sttRow . '', 'Số Mặt In')->getStyle("T$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('U' . $sttRow . '', 'Số Màu - Mặt A')->getStyle("U$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('V' . $sttRow . '', 'Số Màu - Mặt B')->getStyle("V$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('W' . $sttRow . '', 'Số Kẽm- Mặt A')->getStyle("W$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('X' . $sttRow . '', 'Số Kẽm- Mặt B')->getStyle("X$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Y' . $sttRow . '', 'Nhíp Kẽm')->getStyle("Y$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Z' . $sttRow . '', 'Hình ảnh mực in')->getStyle("Z$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AA' . $sttRow . '', 'Hình ảnh bóng phủ')->getStyle("AA$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AB' . $sttRow . '', 'Ghi Chú')->getStyle("AB$sttRow")->getAlignment()->setWrapText(true);
            $i = 27;
            foreach (getListColumTable() as $kk => $vv) {
                $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[($i)].$sttRow,
                    $vv['name'])->getStyle("$cloumns_excel[$i]$sttRow")->getAlignment()->setWrapText(true);
                if ($kk != (count(getListColumTable())) - 1) {
                    $i++;
                }
            }
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:$cloumns_excel[$i]$sttRow")->applyFromArray([
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
                    $item_id = $value['item_id'];
                    $type_item = $value['type_item'];
                    $info = null;
                    if ($type_item == "products") {
                        $info = $this->products_model->rowProduct($item_id);
                    }
                    $images = ('assets/images/tnh/no_image.png');
                    if ($info['images']) {
                        $images = ('uploads/products/' . $info['images']);
                    }
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['reference_no'])->getStyle("B$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", _dt($value['date']))->getStyle("C$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", ($value['name_supplier']))->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", _d($value['date_request']));
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", _d($value['date_delivery']));
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", _d($value['date_go_expected']));
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", _d($value['date_satisfied_expected']));
                    $reference_no_object = '';
                    if ($value['object_type'] == 'po'){
                        $reference_no_object = $value['reference_no_po'];
                    } else {
                        $reference_no_object = $value['reference_no_order'];
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $reference_no_object)->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $info['code'])->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $info['name'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", ($value['sltin']))->getStyle("L$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['sltin']));
                    $type_material = $value['type_material'];
                    if ($type_material == "materials") {
                        $info_material = $this->items_model->rowMaterial($value['material']);
                    } else if ($type_material == "tools_supplies") {
                        $info_material = $this->tools_supplies_model->rowToolsSupplies($item_id);
                    } else {
                        $info_material = $this->products_model->rowProduct($item_id);
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", ($info_material['name']))->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", ($value['quantity_compensation']))->getStyle("N$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['quantity_compensation']));
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", ($value['quantity_compensation_more']))->getStyle("O$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['quantity_compensation_more']));
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", $value['landscape_print_size']);
                    if ($images != '' && file_exists($images)) {
                        $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                        $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                        $objDrawing1->setPath($images);
                        $objDrawing1->setWidth(110);
                        $objDrawing1->setHeight(85);
                        $objDrawing1->setOffsetX(20);
                        $objDrawing1->setOffsetY(5);
                        $objDrawing1->setCoordinates('Q' . ($rowBegin));
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(80);
                    $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin", ($value['name_stage']))->getStyle("R$rowBegin")->getAlignment()->setWrapText(true);


                    $print_text = '';
                    $print = [
                        [
                            'id' => 1,
                            'name' => 'In A-B',
                        ],
                        [
                            'id' => 2,
                            'name' => 'In Trở',
                        ],
                        [
                            'id' => 3,
                            'name' => 'In 1 mặt',
                        ]
                    ];
                    foreach ($print as $kk => $vv) {
                        if ($vv['id'] == $value['print']) {
                            $print_text = $vv['name'];
                            break;
                        }
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("S$rowBegin", ($print_text))->getStyle("S$rowBegin")->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->setCellValue("T$rowBegin", ($value['number_of_printed_sides']))->getStyle("T$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['number_of_printed_sides']));
                    $objPHPExcel->getActiveSheet()->setCellValue("U$rowBegin", ($value['color_number_a']))->getStyle("U$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['color_number_a']));
                    $objPHPExcel->getActiveSheet()->setCellValue("V$rowBegin", ($value['color_number_b']))->getStyle("V$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['color_number_b']));
                    $objPHPExcel->getActiveSheet()->setCellValue("W$rowBegin", ($value['zinc_number_a']))->getStyle("W$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['zinc_number_a']));
                    $objPHPExcel->getActiveSheet()->setCellValue("X$rowBegin", ($value['zinc_number_b']))->getStyle("X$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['zinc_number_b']));
                    $objPHPExcel->getActiveSheet()->setCellValue("Y$rowBegin", ($value['grape']))->getStyle("Y$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['grape']));
                    // $objPHPExcel->getActiveSheet()->setCellValue("X$rowBegin", '');
                    if ($value['image_mucin'] != '' && file_exists($value['image_mucin'])) {
                        $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                        $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                        $objDrawing1->setPath($value['image_mucin']);
                        $objDrawing1->setWidth(110);
                        $objDrawing1->setHeight(85);
                        $objDrawing1->setOffsetX(20);
                        $objDrawing1->setOffsetY(5);
                        $objDrawing1->setCoordinates('Z' . ($rowBegin));
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(80);

                    if ($value['image_bongmo'] != '' && file_exists($value['image_bongmo'])) {
                        $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                        $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                        $objDrawing1->setPath($value['image_bongmo']);
                        $objDrawing1->setWidth(110);
                        $objDrawing1->setHeight(85);
                        $objDrawing1->setOffsetX(20);
                        $objDrawing1->setOffsetY(5);
                        $objDrawing1->setCoordinates('AA' . ($rowBegin));
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(80);
                    $objPHPExcel->getActiveSheet()->setCellValue("AB$rowBegin", $value['note_items']);

                    $colStt = 27;
                    foreach (getListColumTable() as $kk => $vv) {
                        $_data = getDataModeration($value['id'],$vv['id'],'tbl_suggest_outsource','',true);
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",$_data)->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                        if ($kk != (count(getListColumTable())) - 1) {
                            $colStt++;
                        }
                    }
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:$cloumns_excel[$colStt]$rowBegin")->applyFromArray([
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
                    $objPHPExcel->getActiveSheet()->getStyle("K$rowBegin:L$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("R$rowBegin:R$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                }
            }
            $filename = lang('phieu_dieu_do_cong_doan_gia_cong') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(20);
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