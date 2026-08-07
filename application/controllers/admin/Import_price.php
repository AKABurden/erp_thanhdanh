<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Import_price extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('suppliers_model');
        $this->load->model('currencies_model');
        $this->load->model('dashboard_model');
        $this->load->model('Suppliers_price_model');
    }
    public function index()
    {
        if (!has_permission('import_price', '', 'view')) {
            access_denied('import_price');
        }
        $data['title'] = _l('dt_improt_price_suppliert');
        $data['data_price'] = get_table_where('tblsuppliers_price');
        $data['price_supplier'] = get_table_where('tblsuppliers');
        $this->load->view('admin/import_price/manage', $data);
    }
    public function table()
    {
        $this->app->get_table_data('import_price');
    }
    public function show_detail_price($id = '', $table = false)
    {

        $data['data'] = $this->Suppliers_price_model->show_list_detail($id);
        $data['supplier_price'] = $this->Suppliers_price_model->show_List($id);
        if (!empty($table)) {
            return $this->load->view('admin/import_price/table', $data, true);
        } else {
            $this->load->view('admin/import_price/view_modal', $data);
        }
    }
    public function delete_import($id)
    {
        if (!has_permission('import_price', '', 'delete')) {
            echo json_encode(array(
                'success' => false,
                'alert_type' => 'warning',
                'message' => _l('ch_no_delete')
            ));
            die;
        }
        $checkExistingId = get_table_where('tblsuppliers_price', array(), '', 'row');
        if (!empty($checkExistingId)) {
            $delete_import = $this->Suppliers_price_model->delete_import($id);
            if ($delete_import) {
                $delete_detail = get_table_where('tblsuppliers_price_detail', array('supplier_price_id' => $id), '', 'result');
                foreach ($delete_detail as $key => $value) {
                    $this->Suppliers_price_model->delete_import_detail($value->id);
                }
            }
            echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => _l('dt_delete_import_success')]);
        } else {
            echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => _l('dt_delete_import_error')]);
        }
    }
    public function import()
    {
        if (!has_permission('import_price', '', 'create')) {
            set_alert('warning', _l('Bạn không có quyền tạo'));
            redirect(admin_url('import_price'));
        }
        if ($this->input->post()) {
            if (isset($_FILES['file_excel']['name']) && $_FILES['file_excel']['name'] != '') {
                $tmpFilePath = $_FILES['file_excel']['tmp_name'];
                $ext = strtolower(pathinfo($_FILES['file_excel']['name'], PATHINFO_EXTENSION));
                $type = $_FILES["file_excel"]["type"];
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    // Setup our new file path
                    $newFilePath = TEMP_FOLDER . $_FILES['file_excel']['name'];
                    if (!file_exists(TEMP_FOLDER)) {
                        mkdir(TEMP_FOLDER, 777);
                    }

                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {

                        $load_result = true;
                        $fd            = fopen($newFilePath, 'r');
                        $rows          = array();
                        if ($ext == 'csv') {
                            while ($row = fgetcsv($fd)) {
                                $rows[] = $row;
                            }
                        } else if ($ext == 'xlsx' || $ext == 'xls') {
                            if ($type == "application/octet-stream" || $type == "application/vnd.ms-excel" || $type == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
                                require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');

                                $inputFileType = PHPExcel_IOFactory::identify($newFilePath);

                                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                                $objReader->setReadDataOnly(true);
                                $objPHPExcel =           $objReader->load($newFilePath);
                                $allSheetName       = $objPHPExcel->getSheetNames();
                                $objWorksheet       = $objPHPExcel->setActiveSheetIndex(0);
                                $highestRow         = $objWorksheet->getHighestRow();
                                $highestColumn      = $objWorksheet->getHighestColumn();
                                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

                                for ($row = 2; $row <= $highestRow; ++$row) {
                                    for ($col = 0; $col < $highestColumnIndex; ++$col) {
                                        $cell = $objWorksheet->getCellByColumnAndRow($col, $row);
                                        $value                     = $cell->getCalculatedValue();
                                        $rows[$row - 1][$col] = $value;
                                    }
                                }
                            }
                        } else {
                            fclose($fd);
                            unlink($newFilePath);
                            redirect('/');
                        }

                        fclose($fd);
                        $data['total_rows_post'] = count($rows);
                        unlink($newFilePath);

                        $query_array = [];
                        $backup_rows = $rows;

                        $result_array = [];


                        $fetch_columns_step = true;
                        $fetch_product_step = false;
                        $columns_found = 0;
                        $product_count = 0;
                        $c = 0;

                        $data = [];
                        $data_ok = true;
                        $reason = "";
                        $dem_temp = 2;
                        $alert['success'] = 0;
                        $alert['fail'] = 0;
                        //
                        $name_price = $this->input->post('name_price');
                        $year = $this->input->post('year');
                        $suppliers_id = $this->input->post('suppliers_id');
                        $ktr_price = get_table_where('tblsuppliers_price', array('supplier_id' => $suppliers_id, 'year' => $year), '', 'row');
                        if (!empty($ktr_price)) {
                            $data['message'] = "Bảng giá năm $year đã được tạo rồi, Vui lòng kiểm tra lại!";
                        } else {
                            $in = array(
                                'year' => $year,
                                'name_price' => $name_price,
                                'supplier_id' => $suppliers_id,
                                'date_create' => date('Y-m-d H:i:s'),
                                'staff_create' => get_staff_user_id(),
                            );
                            $this->db->insert('tblsuppliers_price', $in);
                            $id_supplier_price = $this->db->insert_id();
                            foreach ($rows as $row) {
                                if (empty($row[1])) {
                                    $reason .= "Không Tìm Thấy loại sản phẩm tại dòng " . $dem_temp . "<br />";
                                    $data_ok = false;
                                    $dem_temp++;
                                    continue;
                                } else if (empty($row[0])) {
                                    $reason .= "Không Tìm Thấy sản phẩm tại dòng " . $dem_temp . "<br />";
                                    $data_ok = false;
                                    $dem_temp++;
                                    continue;
                                }

                                if ($row[1] == 'product') {
                                    $checkExisting_SP = get_table_where('tbl_products', array('code' => $row[0]), '', 'row');
                                } else if ($row[1] == 'nvl') {
                                    $checkExisting_SP = get_table_where('tbl_materials', array('code' => $row[0]), '', 'row');
                                }
                                $data_ok = true;
                                if (($row[2] === '')) {
                                    $reason .= "Không tìm thấy giá tại dòng " . $dem_temp . "<br />";
                                    $data_ok = false;
                                    $dem_temp++;
                                    continue;
                                }
                                if (!is_numeric($row[2])) {
                                    $reason .= "Giá không hợp lệ  " . $dem_temp . "<br />";
                                    $data_ok = false;
                                    $dem_temp++;
                                    continue;
                                } else if (empty($checkExisting_SP)) {
                                    $reason .= "Không Tìm Thấy sản phẩm tại dòng " . $dem_temp . "<br />";
                                    $data_ok = false;
                                    $dem_temp++;
                                    continue;
                                } else {
                                    if ($id_supplier_price) {
                                        $data_tmp = array(
                                            'price'            => $row[2],
                                            'product_id'    => $checkExisting_SP->id,
                                            'product_type' => $row[1],
                                            'supplier_price_id' => $id_supplier_price

                                        );

                                        if ($data_ok) {
                                            $this->db->insert('tblsuppliers_price_detail', $data_tmp);
                                            $alert['success']++;
                                        }
                                    }
                                    $dem_temp++;
                                }
                            }
                            $data['message'] = "Nhập thành công " . $alert['success'] . " nội dung. <br />";
                            $data['message'] .= $reason;
                        }
                    }
                }
            }
        }
        $data['data_supplier'] = get_table_where('tblsuppliers');
        $this->load->view('admin/import_price/import', $data);
    }
    public function quantity($id_detai = '', $id = '')
    {
        if (!has_permission('import_price', '', 'edit')) {
            echo json_encode(array(
                'success' => 'warning',
                'messeger' => _l('Bạn không có quyền sửa')
            ));
            die;
        }
        $data = $this->input->post();
        $ktr = get_table_where('tblsuppliers_price_detail', array('id' => $id_detai), '', 'row');

        if (!empty($ktr)) {
            if (empty($total)) {
                $total = 0;
            }
            $total = str_replace(',', '', $data['data_input']);
            $this->db->update('tblsuppliers_price_detail', array('price' => $total), array('id' => $id_detai));
        }
        $totals['id'] = $data['id'];
        $totals['total'] = number_format($total);
        $totals['success'] = 'success';
        $totals['messeger'] = 'Cập nhật giá thành công';
        echo json_encode($totals);
    }
    public function print_pdf($id = '')
    {
        ob_start();
        $data = new stdClass();
        //  $data->title = lang('Bảng giá nhà cung cấp');
        $dataSub = $this->Suppliers_price_model->show_list_detail($id);
        $main = $this->Suppliers_price_model->show_List($id);
        // $supplier = get_table_where('tblsuppliers', array('id' => $dataMain->suppliers_id), '', 'row');
        $table = '';
        $data->content = '';
        // $data->content .= '<span style="text-align: center;">____________________________________________________________________________________________________________________________________________</span><br><br>';
        $data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;">BẢNG GIÁ</span><br><br>';
        // $data->content .= '<span style="text-align: right;font-style: italic;">' . _l('ch_code_p') . ': ' . $dataMain->prefix . '-' . $dataMain->code . '</span><br>';
        // $data->content .= '<span style="text-align: right;font-style: italic;">' . _l('ch_date_p') . ': ' . _d($dataMain->date) . '</span><br><br>';
        $data->content .= '<span style="font-weight: bold;">' . _l('ch_name_suppliers') . ': </span><span>' . $main->company . '</span><br>';
        $data->content .= '<span style="font-weight: bold;">' . _l('dt_set_name_supplier') . ': </span><span>' . $main->name_price . '</span><br>';
        $data->content .= '<span style="font-weight: bold;">' . _l('year') . ': </span><span>' . $main->year . '</span><br><br>';


        $width1 = 'width: 6%;';
        $width2 = 'width: 15%;';
        $width3 = 'width: 55%;';
        $width4 = 'width: 24%;';
        $table = '

            <table class="table table-bordered" border="1" width="100%">
                <thead>
                    <tr>
                        <td style="' . $width1 . 'text-align: center;font-weight: bold;">' . _l('STT') . '</td>
        ';
        $table .= '<td style="' . $width2 . 'text-align: center;font-weight: bold;">' . _l('Hình ảnh') . '</td>';
        $table .= '<td style="' . $width3 . 'text-align: center;font-weight: bold;">' . _l('ch_items_name_t') . '</td>';
        $table .= '<td style="' . $width4 . 'text-align: center;font-weight: bold;">' . _l('Giá') . '</td>';
        $table .= '</tr>
                </thead>
                <tbody>';
        // $sum_quantity = 0;
        // $sum_quantity_net = 0;
        // $sum_price = 0;
        // $sum_promotion_suppliers = 0;
        // $sum_amount = 0;
        foreach ($dataSub as $key => $value) {
            $table .= '<tr nobr="true">';
            $dataItem = get_full_item($value->product_id, $value->product_type);
            $table .= '<td style="' . $width1 . 'text-align: center;">' . ++$key . '</td>';
            $table .= '<td style="' . $width2 . 'text-align: center;"><img src="' . $dataItem->avatar_1 . '" width="50px" height="50px"></td>';
            $table .= '<td style="' . $width3 . 'text-align: left;">' . $dataItem->name . '(' . $dataItem->code . ')</td>';
            $table .= '<td style="' . $width4 . 'text-align: right;">' . formatNumber($value->price) . '</td>';
            $table .= '</tr>';
        }
        $table .= '</tbody></table>';

        //     // if (isset($item_warehouse_localtion_import)) {
        //     if (!empty($dataLocaltion)) {
        //         // $name_parent = str_replace("<i class='fa fa-caret-right text-danger' aria-hidden='true'>","a",$dataLocaltion->name_parent);
        //         $table .= '<td style="' . $width3 . 'text-align: center;">' . $dataLocaltion->name_parent . '</td>';
        //     } else {
        //         $table .= '<td></td>';
        //     }
        //     // }
        //     // if (isset($item_unit_import)) {
        //     $table .= '<td style="' . $width4 . 'text-align: center;">' . $dataItem->unit_name . '</td>';
        //     // }
        //     // if (isset($item_quantity_import)) {
        //     // $table .= '<td style="' . $width5 . 'text-align: center;">' . formatNumber($value['quantity']) . '</td>';
        //     // $sum_quantity += $value['quantity'];
        //     // }
        //     // if (isset($item_quantity_confirm_import)) {
        //     $table .= '<td style="' . $width6 . 'text-align: center;">' . formatNumber($value['quantity_net']) . '</td>';
        //     $sum_quantity_net += $value['quantity_net'];
        //     // }
        //     // if (isset($item_price_import)) {
        //     $table .= '<td style="' . $width7 . 'text-align: right;">' . number_format($value['price']) . '</td>';
        //     $sum_price += $value['price'];
        //     // }
        //     // if (isset($item_promotion_suppliers_import)) {
        //     // $table .= '<td style="' . $width8 . 'text-align: right;">' . number_format($value['promotion_suppliers']) . '</td>';
        //     // $sum_promotion_suppliers += $value['promotion_suppliers'];
        //     // }
        //     // if (isset($item_tax_import)) {
        //     // $table .= '<td style="' . $width9 . 'text-align: center;">' . number_format($value['tax_rate']) . ' %</td>';
        //     // }
        //     // if (isset($item_invoice_total_import)) {
        //     $table .= '<td style="' . $width10 . 'text-align: right;">' . number_format($value['amount']) . '</td>';
        //     $sum_amount += $value['amount'];
        //     // }
        //     // if (isset($item_note_import)) {
        //     $table .= '<td style="' . $width11 . 'text-align: center;">' . $value['note'] . '</td>';
        //     // }
        //     $table .= '</tr>';
        // }
        $data->content .= $table;
        $pdf = print_pdf($data);
        $type = 'I';
        $pdf->Output(slug_it('') . '.pdf', $type);
    }


    public function add_items()
    {
        $data = $this->input->post();
        $items_products = explode('__', $data['items_products']);
        $product_id = $items_products[0];
        $product_type = $items_products[1];

        $this->db->where('product_id', $product_id);
        $this->db->where('product_type', $product_type);
        $this->db->where('supplier_price_id', $data['supplier_price_id']);
        $ktInsert = $this->db->get('tblsuppliers_price_detail')->row();
        if (!empty($ktInsert)) {
            $dataSuccess = [
                'success' => false,
                'alert_type' => 'danger',
                'message' => 'Sản phẩm đã tồn tại trong bảng giá'
            ];
            echo json_encode($dataSuccess);
            die();
        }

        $success = $this->db->insert('tblsuppliers_price_detail', [
            'price' => number_format_data($data['price'], false),
            'product_id' => $product_id,
            'product_type' => $product_type,
            'supplier_price_id' => $data['supplier_price_id'],
        ]);
        $dataSuccess = [
            'success' => false,
            'alert_type' => 'danger',
            'message' => 'Thêm không thành công'
        ];
        if (!empty($success)) {
            $dataSuccess = [
                'success' => true,
                'alert_type' => 'success',
                'message' => 'Thêm thành công',
                'data' => $this->show_detail_price($data['supplier_price_id'], true)
            ];
        }

        echo json_encode($dataSuccess);
        die();
    }


    public function SearchItems()
    {
        $data = [];
        $search = $this->input->get('term');
        $this->db->select(
            'CONCAT(id, "__", "product") as id,
			tbl_products.name as item_name,
			tbl_products.code as text,
			mode,
			tbl_products.price_sell as price,
			concat("product") as type,
			CONCAT("uploads/products/", "", tbl_products.images, "") as images',
            false
        );
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('tbl_products.name', $search);
            $this->db->or_like('tbl_products.code', $search);
            $this->db->group_end();
        }
        $this->db->order_by('tbl_products.name', 'DESC');
        $this->db->limit(50);
        $product = $this->db->get('tbl_products')->result_array();
        if (!empty($product)) {
            $data['results'][] =
                [
                    'text' => _l('Thành phẩm và BTP'),
                    'children' => $product
                ];
        }

        $this->db->select(
            'CONCAT(id, "__", "nvl") as id,
			tbl_materials.name as item_name,
			tbl_materials.code as text,
			mode,
			tbl_materials.price_sell as price,
			concat("nvl") as type,
			CONCAT("uploads/materials/", "", tbl_materials.images, "") as images',
            false
        );
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('tbl_materials.name', $search);
            $this->db->or_like('tbl_materials.code', $search);
            $this->db->group_end();
        }
        $this->db->order_by('tbl_materials.name', 'DESC');
        $this->db->limit(50);
        $product = $this->db->get('tbl_materials')->result_array();
        if (!empty($product)) {
            $data['results'][] =
                [
                    'text' => _l('Nguyên vật liệu'),
                    'children' => $product
                ];
        }
        echo json_encode($data);
        die();
    }
    public function exportExcel()
    {
        if (!has_permission('import_price', '', 'export')) {
            access_denied();
        }
        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        $cloumns = $this->input->post('cloumns');
        $style_excel = style_excel();
        $cloumns_excel = cloumns_excel();
        $style_excel['Background_header_one'] = $style_excel['Background_header'];
        $style_excel['Background_header_one']['fill']['color']['rgb'] = '81dcf7';

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        $objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(20);
        $permissions = get_available_staff_permissions();

        $numberRow = 1;
        $j = 0;
        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$j]$numberRow", 'STT')->getStyle("$cloumns_excel[$j]$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $j++;
        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$j]$numberRow", 'Tên nhà cung cấp')->getStyle("$cloumns_excel[$j]$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $j++;
        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$j]$numberRow", 'Tên bảng giá')->getStyle("$cloumns_excel[$j]$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $j++;
        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$j]$numberRow", 'Loại hàng')->getStyle("$cloumns_excel[$j]$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $j++;
        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$j]$numberRow", 'Mã hàng')->getStyle("$cloumns_excel[$j]$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $j++;
        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$j]$numberRow", 'Tên hàng')->getStyle("$cloumns_excel[$j]$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $j++;
        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$j]$numberRow", 'Giá')->getStyle("$cloumns_excel[$j]$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $j++;
        $this->db->select('
        tblsuppliers_price_detail.*,
        tblsuppliers_price.name_price,
        IF(tblsuppliers_price_detail.product_type = "nvl",tbl_materials.code,tbl_products.code) as code_items,
        IF(tblsuppliers_price_detail.product_type = "nvl",tbl_materials.name,tbl_products.name) as name_items,
        tblsuppliers.company,
        tbltype_items.name as name_type
        ');
        $this->db->join('tblsuppliers_price', 'tblsuppliers_price.id=tblsuppliers_price_detail.supplier_price_id', 'left');
        $this->db->join('tblsuppliers', 'tblsuppliers.id=tblsuppliers_price.supplier_id', 'left');
        $this->db->join('tbltype_items', 'tbltype_items.type=tblsuppliers_price_detail.product_type', 'left');

        $this->db->join('tbl_materials', 'tbl_materials.id=tblsuppliers_price_detail.product_id and tblsuppliers_price_detail.product_type ="nvl"', 'left');
        $this->db->join('tbl_products', 'tbl_products.id=tblsuppliers_price_detail.product_id and tblsuppliers_price_detail.product_type ="product"', 'left');
        $this->db->order_by('tblsuppliers_price.id','ASC');
        $_data = $this->db->get('tblsuppliers_price_detail')->result_array();
        foreach ($_data as $key => $value) {
            $numberRow++;
            $stt = (string)($key + 1);
            $i = 0;
            $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $stt);
            $i++;
            $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['company'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true);
            $i++;
            $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['name_price'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true);
            $i++;
            $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['name_type'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true);
            $i++;
            $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['code_items'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true);
            $i++;
            $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['name_items'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true);
            $i++;
            $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$i]$numberRow", $value['price'])->getStyle("$cloumns_excel[$i]$numberRow")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['price']));
        }

        $objPHPExcel->getActiveSheet()->getStyle('A1:G' . $numberRow)->applyFromArray([
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
        ]);
        $filename = lang('Bang_gia_nha_cung_cap') . '.xls';
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
