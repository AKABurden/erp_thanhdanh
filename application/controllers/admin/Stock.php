<?php

// header('Content-Type: text/html; charset=utf-8');
defined('BASEPATH') or exit('No direct script access allowed');

class Stock extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');
        $this->load->model('category_model');
        $this->load->model('manufactures_model');
        $this->load->model('purchases_model');
        $this->load->model('business_plan_model');
        $this->load->model('orders_model');
        $this->load->model('departments_model');
        $this->load->model('stock_model');
        $this->load->model('tools_supplies_model');
        $this->load->model('transfer_model');
        // $this->lang->load('vietnamese/form_validation_lang');
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->allowed_file_size = '1024';
        $this->upload_path = get_upload_path_by_type('products');
        $this->datetime_now = time();
        $this->tnh = true;

        //permission exporting production
        $this->perViewExportingProducion = has_permission('stock_exporting_producion', '', 'view');
        $this->perViewOwnExportingProducion = has_permission('stock_exporting_producion', '', 'view_own');
        $this->perAddExportingProducion = has_permission('stock_exporting_producion', '', 'create');
        $this->perEditExportingProducion = has_permission('stock_exporting_producion', '', 'edit');
        $this->perDeleteExportingProducion = has_permission('stock_exporting_producion', '', 'delete');
        $this->perApproveExportingProducion = has_permission('stock_exporting_producion', '', 'approve');
        $this->perApproveWarehouseExportingProducion = has_permission('stock_exporting_producion', '', 'approve_warehouse');

        //permission purchase products
        $this->perViewPurchaseProducts = has_permission('stock_purchase_products', '', 'view');
        $this->perViewOwnPurchaseProducts = has_permission('stock_purchase_products', '', 'view_own');
        $this->perAddPurchaseProducts = has_permission('stock_purchase_products', '', 'create');
        $this->perEditPurchaseProducts = has_permission('stock_purchase_products', '', 'edit');
        $this->perDeletePurchaseProducts = has_permission('stock_purchase_products', '', 'delete');
        $this->perPrintPurchaseProducts = has_permission('stock_purchase_products', '', 'print');
        $this->perApproveWarehousePurchaseProducts = has_permission('stock_purchase_products', '', 'approve_warehouse');

        $this->perApproveWarehouseErrPP = has_permission('stock_purchase_products', '', 'import');
        $this->isAdmin = is_admin();
    }

    public function exporting_producion()
    {
        if (!$this->perViewExportingProducion && !$this->perViewOwnExportingProducion) {
            accessDenied();
        }

        $data['title'] = lang('tnh_exporting_stock_producion');
        $data['tnh'] = $this->tnh;
        $data['branch'] = getListBranch();
        $this->load->view('admin/stock/exporting_producion', $data);
    }

    public function getExportingProductions()
    {
        if (!$this->perViewExportingProducion && !$this->perViewOwnExportingProducion) {
            accessDenied($js = true);
        }
        $arrBranch = get_branch_staff();
        $productions_orders_search = $this->input->post('productions_orders_search');
        $productions_plan_search = $this->input->post('productions_plan_search');
        $branch_search = $this->input->post('branch_search');

        $sugs = "(
            SELECT tbl_suggest_exporting.id, tbl_suggest_exporting.reference_no
            FROM tbl_suggest_exporting
            WHERE tbl_suggest_exporting.pattern_id = 0
        ) as sugs";

        $this->datatables
            ->select("
                0 as number_records,
                tbl_suggest_exporting.id as id,
                tbl_suggest_exporting.date_convert_stock as date,
                CONCAT(tbl_suggest_exporting.reference_stock,'__',COALESCE(tblbranch.name,' ')) as reference_stock,
                tbl_productions_orders.reference_no as reference_no,
                tbl_productions_orders_details.reference_no as reference_production_detail,
                tbl_suggest_exporting.export_name as export_name,
                tblwarehouse.name as warehouse_name,
                tbl_suggest_exporting.note as note,
                CONCAT(tblstaff.firstname, ' ', tblstaff.lastname,'') as created_by,
                tbl_suggest_exporting.status_stock as status_stock,
                '' as user_status,
                tbl_suggest_exporting.type as type,
                tbl_suggest_exporting.warehouseman_id as warehouseman_id,
                tbl_purchase_internal.reference_no as purchase_internal
            ", false)
            ->from('tbl_suggest_exporting')
            ->join('tbl_productions_orders_details', 'tbl_productions_orders_details.id = tbl_suggest_exporting.productions_orders_details_id', 'left')
            ->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_suggest_exporting.po_id', 'left')
            // ->join($sugs, 'sugs.id = tbl_suggest_exporting.pattern_id', 'left')
            ->join('tblstaff', 'tblstaff.staffid = tbl_suggest_exporting.convert_stock_by', 'left')
            ->join('tblbranch', 'tblbranch.id = tbl_suggest_exporting.branch_id', 'left')
            ->join('tbl_purchase_internal', 'tbl_purchase_internal.id = tbl_suggest_exporting.purchase_internal_id', 'left')
            ->join('tblwarehouse', 'tblwarehouse.id = tbl_suggest_exporting.warehouse_id', 'left');
        // ->join('tblstaff staff_status', 'staff_status.staffid = tbl_suggest_exporting.user_status', 'left');

        $this->datatables->where('tbl_suggest_exporting.status_stock IS NOT NULL');

        if (!$this->perViewExportingProducion) {
            $this->datatables->where('tbl_suggest_exporting.created_by', get_staff_user_id());
        }

        if (!$this->isAdmin) {
            if (!empty($arrBranch)) {
                $coverStrBranch = implode(",", $arrBranch);
                $this->datatables->where('tbl_suggest_exporting.branch_id IN (' . $coverStrBranch . ')');
            } else {
                $this->datatables->where('tbl_suggest_exporting.id', 0);
            }
        }

        if (!empty($branch_search)) {
            $this->datatables->where('tbl_suggest_exporting.branch_id', $branch_search);
        }

        if (!empty($productions_orders_search)) {
            $this->datatables->where('(
                tbl_suggest_exporting.po_id = ' . $productions_orders_search . '
                OR exists (
                    SELECT tbl_productions_orders_details.id
                    FROM tbl_productions_orders_details
                    WHERE tbl_productions_orders_details.id = tbl_suggest_exporting.productions_orders_details_id AND tbl_productions_orders_details.productions_orders_id = ' . $productions_orders_search . '
                )
            )', false, false);
        }

        if (!empty($productions_plan_search)) {
            $this->datatables->where('( 
                exists (
                    SELECT tbl_productions_orders_items.id
                    FROM tbl_productions_orders_details
                    INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id
                    WHERE tbl_productions_orders_details.id = tbl_suggest_exporting.productions_orders_details_id AND tbl_productions_orders_items.plan_id = ' . $productions_plan_search . '
                )
                OR
                exists (
                    SELECT tbl_productions_orders_items.id
                    FROM tbl_productions_orders_items
                    WHERE tbl_productions_orders_items.productions_orders_id = tbl_suggest_exporting.po_id AND tbl_productions_orders_items.plan_id = ' . $productions_plan_search . '
                )
            )', false, false);
        }

        $view = '<a class="tnh-modal" title="' . lang('view') . '" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url('admin/stock/view_exporting_production/$1') . '"><i class="fa fa-file-text-o width-icon-actions"></i> ' . lang('view') . '</a>';

        $edit = $this->perEditExportingProducion ? '<a class="tnh-edit" title="' . lang('edit') . '" href="' . base_url('admin/stock/edit_exporting_production/$1') . '"><i class="fa fa-edit width-icon-actions"></i> ' . lang('edit') . '</a>' : '';

        $delete = $this->perDeleteExportingProducion ? '<a type="button" class="po tnh-delete" title="' . lang('delete') . '" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
            <button href=\'' . base_url('admin/stock/delete_suggest_exporting/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
            <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
        "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>' : '';

        $print = '<a href="' . base_url('admin/stock/print_exporting_production/$1') . '" target="_blank"><i class="fa fa-print"></i> ' . lang('print') . '</a>';

        $actions = '
        <div class="dropdown text-center">
            <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
            ' . lang('actions') . '
            <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                <li>' . $view . '</li>
                <li>' . $print . '</li>
                <li class="not-outside">' . $delete . '</li>
            </ul>
        </div>';

        $this->datatables->add_column('actions', $actions, 'id');
        $iDisplayStart = $this->input->post('iDisplayStart');
        $data = json_decode($this->datatables->generate());
        foreach ($data->aaData as $key => $value) {
            $data->aaData[$key][0] = ++$iDisplayStart;
        }
        echo json_encode($data);
    }

    public function view_exporting_production($id)
    {
        if (!$this->perViewExportingProducion && !$this->perViewOwnExportingProducion) {
            accessDenied($js = true);
        }

        $suggest_exporting = $this->manufactures_model->rowSuggestExporting($id);
        if (!$this->perViewExportingProducion) {
            // checkMyData($suggest_exporting['created_by'], true);
        }
        $items = $this->manufactures_model->getSuggestExportingItemsView($id);
        $warehouse = $this->stock_model->rowWarehouse($suggest_exporting['warehouse_id']);
        $sug = $this->manufactures_model->rowSuggestExporting($suggest_exporting['pattern_id']);
        $data['items'] = $items;
        $data['pod'] = $this->manufactures_model->rowProductionsOrdersDetais($suggest_exporting['productions_orders_details_id']);
        $data['created_by'] = get_staff_full_name($suggest_exporting['convert_stock_by']);
        $data['warehouse'] = $warehouse;
        if ($suggest_exporting['updated_by'] && $suggest_exporting['type'] == 2) {
            $data['updated_by'] = get_staff_full_name($suggest_exporting['updated_by']);
        }
        $data['suggest_exporting'] = $suggest_exporting;
        $data['sug'] = $sug;
        $data['po'] = get_table_where('tbl_productions_orders', ['id' => $suggest_exporting['po_id']], '', 'row_array');
        $data['id'] = $id;
        $this->load->view('admin/stock/view_exporting_production', $data);
    }

    public function add_exporting_production($id_pod = 0)
    {
        if (!$this->perAddExportingProducion) {
            accessDenied();
        }

        // set_alert('danger', lang('Chức năng này đang hoàn thiện'));
        // redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'admin');

        if ($this->input->post('add')) {
            $data = [];
            $this->form_validation->set_rules('reference_no', lang("tnh_reference_stock"), 'required|is_unique[tbl_suggest_exporting.reference_stock]');
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
            // $this->form_validation->set_rules('export_name', lang("tnh_export_name"), 'required');
            // $this->form_validation->set_rules('warehouses', lang("tnh_warehouses"), 'required');
            if ($this->form_validation->run() == true) {
                // print_arrays($this->input->post());
                // $reference_stock = $this->input->post('reference_no');

                $reference_stock = getReference('stock');
                $date = to_sql_date($this->input->post('date'), true);
                $productions_orders_detail_id = $this->input->post('productions_orders_detail_id');
                $note = $this->input->post('note');
                $export_name = $this->input->post('export_name') ? $this->input->post('export_name') : '';
                $warehouses = $this->input->post('warehouses');
                $items = $this->input->post('items_id');
                $total_quantity = 0;
                $count_items = 0;
                $total_quantity_exchange = 0;
                $type = 2;
                $save_and_warehouse = !empty($this->input->post('save_and_warehouse')) ? $this->input->post('save_and_warehouse') : 0;
                $errors = false;
                $total_quantity_warehouse = 0;
                $total_quantity_payment = 0;
                $po_id = $this->input->post('po_id');
                $branch_id = $this->input->post('branch_id');

                $arr_id = [];
                $arr_info = [];
                $arrSumMaterials = [];
                if (!empty($items)) {
                    foreach ($items as $key => $value) {
                        if (empty($value)) continue;
                        $unit_id = $this->input->post('unit_id')[$key];
                        $arrUnitId = explode('__', $unit_id);
                        $unit_id = $arrUnitId[0];
                        $unit_parent_id = $this->input->post('unit_parent_id')[$key];
                        $number_exchange = $this->input->post('number_exchange')[$key];
                        // $location = $this->input->post('locations')[$key];
                        $dtLocation = $this->input->post('locations')[$key];
                        $quantity_export = number_unformat($this->input->post('quantity')[$key]);
                        $arr_item = explode('__', $value);
                        $type_item = $arr_item[0];
                        $item_id = $arr_item[1];

                        $is_single_use = 0;
                        $quantity_warehouse = 0;
                        $unit_warehouse = 0;
                        $exchange_warehouse = 1;
                        $quantity_payment = 0;
                        $unit_payment = 0;
                        $exchange_payment = 1;
                        $exchange_unit = 1;
                        $recipe = 1;

                        $longs = 1;
                        $wide = 1;
                        $paper = 1;
                        $conversion_unit = 0;
                        if ($type_item == "semi_products_outside" || $type_item == "semi_products") {
                            $info_item = $this->products_model->rowProduct($item_id);
                            $conversion_unit = $info_item['conversion_unit'];
                            $unit_warehouse = $info_item['conversion_unit'];
                            $exchange_warehouse = $info_item['conversion_quantity_unit'];
                            $unit_payment = 0;
                        } else if ($type_item == "materials") {
                            $info_item = $this->items_model->rowMaterial($item_id);
                            $unit_warehouse = $info_item['standard_unit'];
                            $exchange_warehouse = !empty($info_item['exchange_standard_unit']) ? $info_item['exchange_standard_unit'] : 1;

                            $unit_payment = $info_item['unit_payment'];
                            $exchange_payment = !empty($info_item['exchange_unit_payment']) ? $info_item['exchange_unit_payment'] : 1;

                            $exchange_unit = $info_item['exchange_unit'];

                            $longs = $info_item['longs'];
                            $wide = $info_item['wide'];
                            $paper = $info_item['paper'];
                            $recipe = $info_item['recipe'];
                            $is_single_use = $info_item['is_single_use'];
                            $unit_parent_id = $info_item['unit_id'];
                        } else if ($type_item == "tools_supplies") {
                            $info_item = $this->tools_supplies_model->rowToolsSupplies($item_id);
                        }

                        if (empty($info_item)) continue;
                        if (empty($dtLocation)) continue;

                        $dtLocation = explode('__', $dtLocation);
                        $warehouses = $dtLocation[0];
                        $location = $dtLocation[1];

                        $lot_code = $dtLocation[2];
                        if (empty($lot_code) || $lot_code === 'NULL' || $lot_code === 'null' || $lot_code == null) {
                            $lot_code = NULL;
                        }

                        $date_sx = $dtLocation[3];
                        if (empty($date_sx) || $date_sx === 'NULL' || $date_sx === 'null' || $date_sx == null) {
                            $date_sx = NULL;
                        }

                        $date_sd = $dtLocation[4];
                        if (empty($date_sd) || $date_sd === 'NULL' || $date_sd === 'null' || $date_sd == null) {
                            $date_sd = NULL;
                        }

                        $date_use = $dtLocation[5];
                        if (empty($date_use) || $date_use === 'NULL' || $date_use === 'null' || $date_use == null) {
                            $date_use = NULL;
                        }

                        if (empty($location) || empty($warehouses)) {
                            $errors = lang('tnh_location_warehouse_required');
                            break;
                        }

                        if ($type_item == "semi_products_outside" || $type_item == "semi_products") {
                            $quantity_exchange = roundNumberFormat($quantity_export / $number_exchange, 4);
                            if ($unit_id == $conversion_unit) {
                                $quantity_warehouse = $quantity_export;
                            } else {
                                $quantity_warehouse = roundNumberFormat($quantity_export * $exchange_warehouse, 4);
                            }
                            $quantity_payment = 0;
                        } else {


                            // $quantity_exchange = roundNumberFormat($quantity_export * $number_exchange / $exchange_unit, 4);
                            // $quantity_warehouse = roundNumberFormat($quantity_exchange / $exchange_warehouse * $exchange_unit, 4);
                            // if ($recipe == 1) {
                            //     $quantity_payment = roundNumberFormat($quantity_exchange / $exchange_payment * $exchange_unit, 4);
                            // } else if ($recipe == 2) {
                            //     $quantity_payment = roundNumberFormat($quantity_exchange / $exchange_payment * $exchange_unit * $paper / 100, 4);
                            // } else if ($recipe == 3) {
                            //     $quantity_payment = roundNumberFormat($quantity_exchange / $exchange_payment * $exchange_unit * $longs * $wide / 10000, 4);
                            // }
                            $quantity_warehouse = $quantity_export;
                            $quantity_exchange = roundNumberFormat(($quantity_warehouse * $exchange_warehouse) / $exchange_unit, 4);
                            if ($recipe == 1) {
                                $quantity_payment = roundNumberFormat(($quantity_exchange / $exchange_payment) * $exchange_unit, 4);
                            } elseif ($recipe == 2) {
                                $quantity_payment = roundNumberFormat(($quantity_exchange / $exchange_payment) * $paper / 100, 4);
                            } elseif ($recipe == 3) {
                                $quantity_payment = roundNumberFormat(($quantity_exchange / $exchange_payment) * ($longs  * $wide) / 10000, 4);
                            }
                        }
                        $exporting_items[] = [
                            'type_item' => $type_item,
                            'item_id' => $item_id,
                            'item_code' => $info_item['code'],
                            'item_name' => $info_item['name'],
                            'unit_id' => $unit_id,
                            'quantity_export' => $quantity_export,
                            'unit_parent_id' => $unit_parent_id,
                            'number_exchange' => $number_exchange,
                            'quantity_exchange' => $quantity_exchange,
                            'location_id' => $location,
                            'warehouse_item_id' => $warehouses,
                            'lot_code' => $lot_code,
                            'date_sx' => $date_sx,
                            'date_sd' => $date_sd,
                            'date_use' => $date_use,
                            'quantity_warehouse' => $quantity_warehouse,
                            'unit_warehouse' => $unit_warehouse,
                            'exchange_warehouse' => $exchange_warehouse,
                            'quantity_payment' => $quantity_payment,
                            'unit_payment' => $unit_payment,
                            'exchange_payment' => $exchange_payment,
                            'is_single_use' => $is_single_use
                        ];
                        $total_quantity += $quantity_export;
                        $total_quantity_exchange += $quantity_exchange;
                        $total_quantity_warehouse += $quantity_warehouse;
                        $total_quantity_payment += $quantity_payment;

                        $item_ccs_id = $type_item . '__' . $item_id . '__' . $warehouses . '__' . $location . '__' . $lot_code . '__' . $date_sx . '__' . $date_sd . '__' . $date_use;
                        if (empty($arrSumMaterials[$item_ccs_id])) {
                            $arrSumMaterials[$item_ccs_id] = [
                                'item_ccs_id' => $item_ccs_id,
                                'item_cs_id' => $item_id,
                                'item_id_mt' => $item_id,
                                'item_type' => $type_item,
                                'item_code' => $info_item['code'],
                                'item_name' => $info_item['name'],
                                'location_id' => $location,
                                'warehouse_item_id' => $warehouses,
                                'lot_code' => $lot_code,
                                'date_sx' => $date_sx,
                                'date_sd' => $date_sd,
                                'date_use' => $date_use,
                                'quantity_warehouse' => $quantity_warehouse,
                            ];
                        } else {
                            $arrSumMaterials[$item_ccs_id]['quantity_warehouse'] = $arrSumMaterials[$item_ccs_id]['quantity_warehouse'] + $quantity_warehouse;
                        }
                        // $index = array_search($str_item_id, $arr_id);
                        // if ($index === false) {
                        //     $arr_id[] = $str_item_id;
                        //     $arr_info[] = [
                        //         "quantity" => $quantity_exchange,
                        //         "item_code" => $info_item['code'],
                        //         "item_name" => $info_item['name'],
                        //     ];
                        // } else {
                        //     $arr_info[$index]['quantity'] = $arr_info[$index]['quantity'] + $quantity_exchange;
                        // }
                    }
                }

                if (empty($exporting_items)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_no_items');
                    echo json_encode($data);
                    die;
                }

                $count_items = count($exporting_items);
                $staff_general = get_staff_user_id();
                $fields = [
                    'productions_orders_details_id' => $productions_orders_detail_id,
                    'reference_no' => null,
                    'reference_stock' => $reference_stock,
                    'date' => $date,
                    'export_name' => $export_name,
                    'note' => $note,
                    'status' => 'un_approved',
                    'total_quantity' => $total_quantity,
                    'count_items' => $count_items,
                    'total_quantity_exchange' => $total_quantity_exchange,
                    'created_by' => $staff_general,
                    'date_created' => date('Y-m-d H:i:s'),
                    'convert_stock_by' => $staff_general,
                    'date_convert_stock' => date('Y-m-d H:i:s'),
                    'status_stock' => 'un_approved_stock',
                    'type' => '2',
                    'date_convert_stock' => $date,
                    'warehouse_id' => 0,
                    'save_and_warehouse' => $save_and_warehouse,
                    'total_quantity_warehouse' => $total_quantity_warehouse,
                    'total_quantity_payment' => $total_quantity_payment,
                    'po_id' => $po_id,
                    'branch_id' => $branch_id
                ];

                if ($this->perApproveExportingProducion && $this->perApproveWarehouseExportingProducion) {
                    if (!empty($save_and_warehouse)) {
                        $fields['status_stock'] = 'approved';
                        $fields['date_stock'] = date('Y-m-d H:i:s');
                        $fields['user_stock'] = $staff_general;

                        // foreach ($arr_id as $key => $value) {
                        //     $str_item_id = explode('__', $value);
                        //     $type_item = $str_item_id[0];
                        //     $item_id = $str_item_id[1];
                        //     $location = $str_item_id[2];
                        //     $warehouses = $str_item_id[3];
                        //     $quantity = $arr_info[$key]['quantity'];
                        //     $item_code = $arr_info[$key]['item_code'];
                        //     $item_name = $arr_info[$key]['item_name'];

                        //     $dtWarehouse = $this->site_model->rowWarehouseById($warehouses);
                        //     $dtLocation = $this->site_model->rowLocationWarehouseById($location);
                        //     $quantityWarehouses = $this->manufactures_model->getTotalQuantityWarehouseLocationAndWarehouse($item_id, $type_item, $location, $warehouses)['total_quantity'];
                        //     if ($quantity > $quantityWarehouses) {
                        //         $errors .= '<div>Mặt hàng [' . $item_code . '] không đủ số lượng để duyệt kho [' . $dtWarehouse['name'] . '] vị trí [' . $dtLocation['name'] . ']</div>';
                        //     }
                        // }

                        if (!empty($arrSumMaterials)) {
                            foreach ($arrSumMaterials as $k => $val) {
                                $item_id_mt = $val['item_id_mt'];
                                $item_type_mt = $val['item_type'];
                                $location_id = $val['location_id'];
                                $warehouse_item_id = $val['warehouse_item_id'];
                                $lot_code = !empty($val['lot_code']) ? $val['lot_code'] : NULL;
                                $date_sx = !empty($val['date_sx']) ? $val['date_sx'] : NULL;
                                $date_sd = !empty($val['date_sd']) ? $val['date_sd'] : NULL;
                                $date_use = !empty($val['date_use']) ? $val['date_use'] : NULL;
                                $quantity_warehouse = $val['quantity_warehouse'];
                                $code = $val['item_code'];

                                $quantityW = $this->manufactures_model->getTotalQuantitW($item_id_mt, $item_type_mt, $location_id, $warehouse_item_id, $lot_code, $date_sx, $date_sd, $date_use);
                                if ($quantity_warehouse > $quantityW['total_quantity']) {
                                    $data['result'] = 0;
                                    $data['message'] = 'Mã ' . $code . ' không đủ số lượng trong kho để xuất';
                                    echo json_encode($data);
                                    die;
                                }
                            }
                        }
                    }
                }

                if (!empty($errors)) {
                    $data['result'] = 0;
                    $data['message'] = $errors;
                    echo json_encode($data);
                    die;
                }

                // print_arrays($exporting_items);
                $suggest_exporting_id = $this->manufactures_model->insertSuggestExporting($fields);
                if ($suggest_exporting_id) {
                    if (getReference('stock') == $reference_stock) {
                        updateReference('stock');
                    }
                    foreach ($exporting_items as $key => $value) {
                        $exporting_items[$key]['suggest_exporting_id'] = $suggest_exporting_id;
                    }
                    $this->manufactures_model->insertBatchSuggestExportingItems($exporting_items);

                    @pusherTNHNotfication();
                    $pod = $this->manufactures_model->rowProductionsOrdersDetais($productions_orders_detail_id);
                    if ($pod) {
                        $content = lang('tnh_his_add_exporting_producion');
                        $content = str_replace('{$1}', $reference_stock, $content);
                        $content = str_replace('{$2}', $pod['reference_no'], $content);
                    } else {
                        $content = 'Tạo xuất kho sản xuất ' . $reference_stock;
                    }


                    insertActivityLog([
                        'type_parent_obj' => 'exporting_producion',
                        'table_obj' => 'tbl_suggest_exporting',
                        'id_obj' => $suggest_exporting_id,
                        'name_obj' => $reference_stock,
                        'content' => $content,
                        'actions' => 'add'
                    ]);

                    if ($this->perApproveExportingProducion && $this->perApproveWarehouseExportingProducion) {
                        if (!empty($save_and_warehouse)) {
                            $id = $suggest_exporting_id;
                            $_data = array(
                                'warehouseman_id' => get_staff_user_id(),
                                'date_warehouseman' => date('Y-m-d H:i:s')
                            );

                            if (!test_quantity_exporting_producion_warehouses($suggest_exporting_id)) {
                                $data['result'] = 1;
                                $data['message'] = lang('test_quantyti_time_return');
                                echo json_encode($data);
                                die;
                            } else {
                                $success = $this->db->update('tbl_suggest_exporting', $_data, array('id' => $suggest_exporting_id));
                                if ($success) {
                                    log_activity('Export Warehouses items approved [ID export_warehouses: ' . $suggest_exporting_id);
                                    $this->stock_model->decreaseWarehouse($suggest_exporting_id);

                                    $suggest_exporting = $this->manufactures_model->rowSuggestExporting($suggest_exporting_id);
                                    $pod = $this->manufactures_model->rowProductionsOrdersDetais($suggest_exporting['productions_orders_details_id']);
                                    if ($pod) {
                                        $content = lang('tnh_his_warehouse_exporting_producion');
                                        $content = str_replace('{$1}', $suggest_exporting['reference_stock'], $content);
                                        $content = str_replace('{$2}', $pod['reference_no'], $content);
                                    } else {
                                        $content = lang('uyệt kho cho xuất kho sản xuất phiếu') . '[' . $suggest_exporting['reference_stock'] . ']';
                                    }


                                    insertActivityLog([
                                        'type_parent_obj' => 'exporting_producion',
                                        'table_obj' => 'tbl_suggest_exporting',
                                        'id_obj' => $id,
                                        'name_obj' => $suggest_exporting['reference_stock'],
                                        'content' => $content,
                                        'actions' => 'warehouse'
                                    ]);
                                }
                            }
                        }
                    }

                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            die;
        } else {
            $data['reference_no'] = getReference('stock');
            $data['warehouses'] = $this->stock_model->getWarehouses();
            $data['breadcrumb'] = [array('link' => base_url('admin/stock/exporting_producion'), 'page' => lang('tnh_exporting_stock_producion')), array('link' => '#', 'page' => lang('tnh_add_exporting_production'))];
            $data['title'] = lang('tnh_add_exporting_production');
            $data['tnh'] = $this->tnh;
            $data['id_pod'] = $id_pod;
            $this->load->view('admin/stock/add_exporting_production', $data);
        }
    }

    public function edit_exporting_production($id)
    {
        if (!$this->perEditExportingProducion) {
            accessDenied();
        }

        refererModel(lang('Chức năng này hiện không khả dụng'));
        die;

        $suggest_exporting = $this->manufactures_model->rowSuggestExporting($id);
        if ($suggest_exporting['type'] != 2) {
            refererModel(lang('tnh_no_edit_convert_to_stock'));
        }
        if ($suggest_exporting['status_stock'] != 'un_approved_stock') {
            refererModel(lang('browsed_cannot_be_edited'));
        }
        // checkMyData($suggest_exporting['created_by']);

        if ($this->input->post('edit')) {
            $data = [];
            if ($suggest_exporting['reference_stock'] != $this->input->post('reference_no')) {
                $this->form_validation->set_rules('reference_no', lang("tnh_reference_stock"), 'required|is_unique[tbl_suggest_exporting.reference_stock]');
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('export_name', lang("tnh_export_name"), 'required');
            // $this->form_validation->set_rules('warehouses', lang("tnh_warehouses"), 'required');
            if ($this->form_validation->run() == true) {
                $reference_stock = $this->input->post('reference_no');
                $date = to_sql_date($this->input->post('date'), true);
                // $productions_orders_detail_id = $this->input->post('productions_orders_detail_id');
                $note = $this->input->post('note');
                $export_name = $this->input->post('export_name');
                $warehouses = $this->input->post('warehouses');
                $items = $this->input->post('items_id');
                $total_quantity = 0;
                $count_items = 0;
                $total_quantity_exchange = 0;
                $type = 2;
                $save_and_warehouse = !empty($this->input->post('save_and_warehouse')) ? $this->input->post('save_and_warehouse') : 0;
                $errors = false;

                //insert
                $arr_idd = [];
                $arr_infoo = [];
                if (!empty($items)) {
                    foreach ($items as $key => $value) {
                        if (empty($value)) continue;
                        $unit_id = $this->input->post('unit_id')[$key];
                        $arrUnitId = explode('__', $unit_id);
                        $unit_id = $arrUnitId[0];

                        $unit_parent_id = $this->input->post('unit_parent_id')[$key];
                        $number_exchange = $this->input->post('number_exchange')[$key];
                        $dtLocation = $this->input->post('locations')[$key];
                        $quantity_export = number_unformat($this->input->post('quantity')[$key]);
                        $arr_item = explode('__', $value);
                        $type_item = $arr_item[0];
                        $item_id = $arr_item[1];
                        if ($type_item == "semi_products_outside" || $type_item == "semi_products") {
                            $info_item = $this->products_model->rowProduct($item_id);
                        } else if ($type_item == "materials") {
                            $info_item = $this->items_model->rowMaterial($item_id);
                        } else if ($type_item == "tools_supplies") {
                            $info_item = $this->tools_supplies_model->rowToolsSupplies($item_id);
                        }

                        if (empty($info_item)) continue;
                        if (empty($dtLocation)) continue;

                        $dtLocation = explode('__', $dtLocation);
                        $warehouses = $dtLocation[0];
                        $location = $dtLocation[1];

                        if (empty($location) || empty($warehouses)) {
                            $errors = lang('tnh_location_warehouse_required');
                            break;
                        }

                        $quantity_exchange = $quantity_export / $number_exchange;
                        $exporting_items[] = [
                            'suggest_exporting_id' => $id,
                            'type_item' => $type_item,
                            'item_id' => $item_id,
                            'item_code' => $info_item['code'],
                            'item_name' => $info_item['name'],
                            'unit_id' => $unit_id,
                            'quantity_export' => $quantity_export,
                            'unit_parent_id' => $unit_parent_id,
                            'number_exchange' => $number_exchange,
                            'quantity_exchange' => $quantity_exchange,
                            'location_id' => $location,
                            'warehouse_item_id' => $warehouses,
                        ];
                        $total_quantity += $quantity_export;
                        $total_quantity_exchange += $quantity_exchange;

                        $str_item_id = $type_item . '__' . $item_id . '__' . $location . '__' . $warehouses;
                        $index = array_search($str_item_id, $arr_idd);
                        if ($index === false) {
                            $arr_idd[] = $str_item_id;
                            $arr_infoo[] = [
                                "quantity" => $quantity_exchange,
                                "item_code" => $info_item['code'],
                                "item_name" => $info_item['name'],
                            ];
                        } else {
                            $arr_infoo[$index]['quantity'] = $arr_infoo[$index]['quantity'] + $quantity_exchange;
                        }
                    }
                }
                //updated
                $arr_id = [];
                $suggest_exporting_items_id = $this->input->post('suggest_exporting_items_id');
                if (!empty($suggest_exporting_items_id)) {
                    foreach ($suggest_exporting_items_id as $key => $value) {
                        if (empty($value)) continue;
                        $row = $this->stock_model->rowSuggestExportingItems($value);
                        if (empty($row)) continue;
                        array_push($arr_id, $value);
                        $number_exchange = $row['number_exchange'];
                        $quantity_export = number_unformat($this->input->post('quantity_edit')[$key]);
                        $dtLocation = $this->input->post('locations_edit')[$key];
                        $quantity_exchange = $quantity_export / $number_exchange;
                        $dtLocation = explode('__', $dtLocation);
                        $warehouses = $dtLocation[0];
                        $location = $dtLocation[1];

                        if (empty($location) || empty($warehouses)) {
                            $errors = lang('tnh_location_warehouse_required');
                            break;
                        }

                        $exporting_items_up[] = [
                            'id' => $value,
                            'quantity_export' => $quantity_export,
                            'quantity_exchange' => $quantity_exchange,
                            'location_id' => $location,
                            'warehouse_item_id' => $warehouses,
                        ];
                        $total_quantity += $quantity_export;
                        $total_quantity_exchange += $quantity_exchange;

                        //
                        $str_item_id = $row['type_item'] . '__' . $row['item_id'] . '__' . $location . '__' . $warehouses;
                        $index = array_search($str_item_id, $arr_idd);
                        if ($index === false) {
                            $arr_idd[] = $str_item_id;
                            $arr_infoo[]['quantity'] = $quantity_exchange;
                            $arr_infoo[$index]['item_code'] = $row['item_code'];
                            $arr_infoo[$index]['item_name'] = $row['item_name'];
                        } else {
                            $arr_infoo[$index]['quantity'] = $arr_infoo[$index]['quantity'] + $quantity_exchange;
                        }
                    }
                }
                //
                if (empty($exporting_items) && empty($exporting_items_up)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_no_items');
                    echo json_encode($data);
                    die;
                }



                $count_items = (!empty($exporting_items) ? count($exporting_items) : 0) + (!empty($exporting_items_up) ? count($exporting_items_up) : 0);
                $fields = [
                    'reference_no' => null,
                    'reference_stock' => $reference_stock,
                    'date' => $date,
                    'export_name' => $export_name,
                    'note' => $note,
                    'total_quantity' => $total_quantity,
                    'count_items' => $count_items,
                    'total_quantity_exchange' => $total_quantity_exchange,
                    'updated_by' => get_staff_user_id(),
                    'date_updated' => date('Y-m-d H:i:s'),
                    'date_convert_stock' => $date,
                    'save_and_warehouse' => $save_and_warehouse
                ];

                if ($this->perApproveExportingProducion && $this->perApproveWarehouseExportingProducion) {
                    if (!empty($save_and_warehouse)) {
                        $fields['status_stock'] = 'approved';
                        $fields['date_stock'] = date('Y-m-d H:i:s');
                        $fields['user_stock'] = get_staff_user_id();

                        foreach ($arr_idd as $key => $value) {
                            $str_item_id = explode('__', $value);
                            $type_item = $str_item_id[0];
                            $item_id = $str_item_id[1];
                            $location = $str_item_id[2];
                            $warehouses = $str_item_id[3];
                            $quantity = $arr_infoo[$key]['quantity'];
                            $item_code = $arr_infoo[$key]['item_code'];
                            $item_name = $arr_infoo[$key]['item_name'];

                            $dtWarehouse = $this->site_model->rowWarehouseById($warehouses);
                            $dtLocation = $this->site_model->rowLocationWarehouseById($location);
                            $quantityWarehouses = $this->manufactures_model->getTotalQuantityWarehouseLocationAndWarehouse($item_id, $type_item, $location, $warehouses)['total_quantity'];
                            if ($quantity > $quantityWarehouses) {
                                $errors .= '<div>Mặt hàng [' . $item_code . '] không đủ số lượng để duyệt kho [' . $dtWarehouse['name'] . '] vị trí [' . $dtLocation['name'] . ']</div>';
                            }
                        }
                    }
                }

                if (!empty($errors)) {
                    $data['result'] = 0;
                    $data['message'] = $errors;
                    echo json_encode($data);
                    die;
                }

                $up = $this->manufactures_model->updateSuggestExportingById($id, $fields);
                if ($up) {
                    //delete
                    $delete = $this->manufactures_model->getSuggestExportingItemsByNotArrId($arr_id, $id);
                    if (!empty($delete)) {
                        foreach ($delete as $key => $value) {
                            $this->manufactures_model->deleteSuggestExportingItemsById($value['id']);
                        }
                    }
                    //add
                    if (!empty($exporting_items)) {
                        $this->manufactures_model->insertBatchSuggestExportingItems($exporting_items);
                    }
                    //edit
                    if (!empty($exporting_items_up)) {
                        $this->manufactures_model->updateBatchSuggestExportingItemsById($exporting_items_up);
                    }

                    $pod = $this->manufactures_model->rowProductionsOrdersDetais($suggest_exporting['productions_orders_details_id']);
                    $content = lang('tnh_his_edit_exporting_producion');
                    $content = str_replace('{$1}', $reference_stock, $content);
                    $content = str_replace('{$2}', $pod['reference_no'], $content);

                    insertActivityLog([
                        'type_parent_obj' => 'exporting_producion',
                        'table_obj' => 'tbl_suggest_exporting',
                        'id_obj' => $id,
                        'name_obj' => $reference_stock,
                        'content' => $content,
                        'actions' => 'edit'
                    ]);

                    if ($this->perApproveExportingProducion && $this->perApproveWarehouseExportingProducion) {
                        if (!empty($save_and_warehouse)) {
                            $suggest_exporting_id = $id;
                            $_data = array(
                                'warehouseman_id' => get_staff_user_id(),
                                'date_warehouseman' => date('Y-m-d H:i:s')
                            );

                            if (!test_quantity_exporting_producion_warehouses($suggest_exporting_id)) {
                                $data['result'] = 1;
                                $data['message'] = lang('test_quantyti_time_return');
                                echo json_encode($data);
                                die;
                            } else {
                                $success = $this->db->update('tbl_suggest_exporting', $_data, array('id' => $suggest_exporting_id));
                                if ($success) {
                                    log_activity('Export Warehouses items approved [ID export_warehouses: ' . $suggest_exporting_id);
                                    $this->stock_model->decreaseWarehouse($suggest_exporting_id);

                                    $suggest_exporting = $this->manufactures_model->rowSuggestExporting($suggest_exporting_id);
                                    $pod = $this->manufactures_model->rowProductionsOrdersDetais($suggest_exporting['productions_orders_details_id']);
                                    $content = lang('tnh_his_warehouse_exporting_producion');
                                    $content = str_replace('{$1}', $suggest_exporting['reference_stock'], $content);
                                    $content = str_replace('{$2}', $pod['reference_no'], $content);

                                    insertActivityLog([
                                        'type_parent_obj' => 'exporting_producion',
                                        'table_obj' => 'tbl_suggest_exporting',
                                        'id_obj' => $id,
                                        'name_obj' => $suggest_exporting['reference_stock'],
                                        'content' => $content,
                                        'actions' => 'warehouse'
                                    ]);
                                }
                            }
                        }
                    }

                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            die;
        } else {
            $suggest_exporting_items = $this->stock_model->getSuggestExportingItemsForStock($id);
            $data['suggest_exporting'] = $suggest_exporting;
            $data['warehouses'] = $this->stock_model->getWarehouses();
            $data['production_orders_detail'] = $this->manufactures_model->rowProductionsOrdersDetais($suggest_exporting['productions_orders_details_id']);
            $data['suggest_exporting_items'] = $suggest_exporting_items;
            $data['id'] = $id;
            $data['breadcrumb'] = [array('link' => base_url('admin/stock/exporting_producion'), 'page' => lang('tnh_exporting_stock_producion')), array('link' => '#', 'page' => lang('tnh_edit_exporting_production'))];
            $data['title'] = lang('tnh_edit_exporting_production');
            $data['tnh'] = $this->tnh;
            $this->load->view('admin/stock/edit_exporting_production', $data);
        }
    }

    public function searchProductionsOrdersDetail($id = false)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');
        $data['results'] = $this->stock_model->searchProductionsOrdersDetailsForStockNew($term, $limit);
        if ($id) {
            $this->db->select('
                tbl_productions_orders_details.id as id, 
                CONCAT(tbl_productions_orders_details.reference_no, "(", tbl_productions_orders_items.items_name, ")") as text', false);
            $this->db->from('tbl_productions_orders_details');
            $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id', 'left');
            $this->db->where('tbl_productions_orders_details.id', $id);
            $result = $this->db->get()->row_array();
            if ($result) {
                $data['row'] = ['id' => $result['id'], 'text' => $result['text']];
            }
            // $data['row'] = ['id' => $product['id'], 'text' => $product['code']];
        }
        echo json_encode($data);
    }

    public function getProductionsOrdersDetailAll()
    {
        $data = [];
        $id_pod = $this->input->post('id_pod');
        $warehouse_id = $this->input->post('warehouse_id');
        $items = [];
        if ($id_pod) {
            $term = false;
            $limit = 10000;
            $material = $this->stock_model->searchMaterialProductionsOrders($term, $limit, $id_pod);
            $semi_product_outside = $this->stock_model->searchSemiProductProductionsOrders($term, $limit, $id_pod);
            $semi_products = $this->stock_model->searchSemiProductsProductionsOrders($term, $limit, $id_pod);

            $items = array_merge($items, $material, $semi_product_outside, $semi_products);

            foreach ($items as $key => $value) {
                $item = $value['id'];
                if (!empty($item)) {
                    $item = explode('__', $item);
                    $type_item = $item[0];
                    $item_id = $item[1];

                    $this->db->select("
                        COALESCE(SUM(tbl_suggest_exporting_items.quantity_export), 0) as quantity_export
                    ");
                    $this->db->from('tbl_suggest_exporting');
                    $this->db->join('tbl_suggest_exporting_items', 'tbl_suggest_exporting_items.suggest_exporting_id = tbl_suggest_exporting.id');
                    $this->db->where('tbl_suggest_exporting.reference_stock IS NOT NULL');
                    $this->db->where('tbl_suggest_exporting.status_stock =', 'approved');
                    $this->db->where('tbl_suggest_exporting.warehouseman_id >', 0);
                    $this->db->where('(tbl_suggest_exporting.type_pattern_id IN (1, 2) OR tbl_suggest_exporting.type IN (1, 2))');
                    $this->db->where('tbl_suggest_exporting_items.type_item', $type_item);
                    $this->db->where('tbl_suggest_exporting_items.item_id', $item_id);
                    $this->db->where('tbl_suggest_exporting.productions_orders_details_id', $id_pod);

                    $quantityExport = $this->db->get()->row_array()['quantity_export'];
                    $rest = $value['quantity'] - $quantityExport;
                    if ($rest <= 0) {
                        unset($items[$key]);
                        continue;
                    }

                    $value['quantity'] = $rest;

                    $warehouses = $this->stock_model->getWarehouseItemsByItemIdAndTypeAndWarehouse($item_id, $type_item, $warehouse_id);
                    foreach ($warehouses as $k => $val) {
                        $warehouses[$k]['location_name'] = recursiveLocations($val['localtion']);
                    }
                    $items[$key]['warehouses'] = $warehouses;
                }
            }

            $data['items'] = $items;
        }
        echo json_encode($data);
    }

    public function searchItemsByProductionDetail($id = false)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');
        $params = $this->input->get('params');
        $productions_orders_detail_id = $params['productions_orders_detail_id'];
        $material = $this->stock_model->searchMaterialProductionsOrders($term, $limit, $productions_orders_detail_id);
        $semi_product_outside = $this->stock_model->searchSemiProductProductionsOrders($term, $limit, $productions_orders_detail_id);
        $semi_products = $this->stock_model->searchSemiProductsProductionsOrders($term, $limit, $productions_orders_detail_id);
        $tools_supplies = $this->stock_model->searchToolsSupplies($term, $limit);

        $results = [];
        if (!empty($material)) {
            $results[] = ['text' => lang('tnh_item_materials_by_bom'), 'children' => $material];
        }
        if (!empty($semi_products)) {
            $results[] = ['text' => lang('semi_products'), 'children' => $semi_products];
        }
        if (!empty($semi_product_outside)) {
            $results[] = ['text' => lang('semi_products_outside'), 'children' => $semi_product_outside];
        }
        if (!empty($tools_supplies)) {
            $results[] = ['text' => lang('tnh_tools_supplies'), 'children' => $tools_supplies];
        }
        $data['results'] = $results;

        if ($id) {
            // $data['row'] = ['id' => $product['id'], 'text' => $product['code']];
        }
        echo json_encode($data);
    }

    function refereshReferenceProductionsOrders()
    {
        $data = [];
        if ($this->input->get('referesh')) {
            $reference_no = getReference('stock');
            if ($this->stock_model->checkExistSuggestExportingReferenceStock($reference_no)) {
                $ct = countReferenceMinus('stock');
                $this->db->select("MAX(right(tbl_suggest_exporting.reference_stock, char_length(tbl_suggest_exporting.reference_stock) - $ct) + 0) as reference_no", false);
                $this->db->from('tbl_suggest_exporting');
                $rs = $this->db->get()->row_array();

                $max = $rs['reference_no'];
                $max++;
                // $max = subReference($max);
                updateReferenceNormal('stock', $max);
                $reference_no = getReference('stock');
            }
            $data['reference_no'] = $reference_no;
            $data['message'] = lang('tnh_referesh_success');
        }
        echo json_encode($data);
    }

    public function delete_suggest_exporting($id)
    {
        $data = [];

        //check permision
        if (!$this->perDeleteExportingProducion) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }
        //end

        if ($id) {
            $flag = false;
            $suggest_exporting = $this->manufactures_model->rowSuggestExporting($id);
            $suggest_exporting_items = $this->manufactures_model->getSuggestExportingItems($id);
            if ($suggest_exporting['status_stock'] != 'un_approved_stock') {
                $data['result'] = 0;
                $data['message'] = lang('browsed_cannot_be_deleted');
                echo json_encode($data);
                die;
            }

            if (!checkMyDataTF($suggest_exporting['created_by'])) {
                $data['result'] = 0;
                $data['message'] = lang('access_denied');
                echo json_encode($data);
                die;
            }

            if ($suggest_exporting['type'] == 2) {
                if ($this->manufactures_model->deleteSuggestExportingById($id)) {
                    $this->manufactures_model->deleteSuggestExportingItems($id);
                    $flag = true;
                }
            } else if ($suggest_exporting['type'] == 5) {
                if ($this->manufactures_model->deleteSuggestExportingById($id)) {
                    $this->manufactures_model->deleteSuggestExportingItems($id);
                    $flag = true;

                    foreach ($suggest_exporting_items as $key => $value) {
                        $this->manufactures_model->updateQuantityExportPurchaseInternal($value['id_purchase_internal_item'], $value['quantity_exchange'], $minus = 2);
                    }
                }
            } else {
                if ($this->manufactures_model->deleteSuggestExportingById($id)) {
                    $this->manufactures_model->deleteSuggestExportingItems($id);

                    $totalQtySug = 0;
                    foreach ($suggest_exporting_items as $key => $value) {
                        $this->manufactures_model->updateSetSuggestExportingItem($value['pattern_item_id'], $value['quantity_export'], $minus = 1);
                        $totalQtySug += $value['quantity_export'];
                    }

                    $statusSug = "";
                    if ($this->manufactures_model->checkExiSuggestExportingItems($suggest_exporting['pattern_id'])) {
                        $statusSug = "apart";
                    }
                    $this->manufactures_model->updateSetSuggestExporting($suggest_exporting['pattern_id'], $totalQtySug, $statusSug, $minus = 1);

                    $flag = true;
                }
                // $up = $this->manufactures_model->updateSuggestExportingById($id, [
                //     'reference_stock' => null,
                //     'status_stock' => null,
                //     'date_convert_stock' => null,
                //     'convert_stock_by' => null
                // ]);
                // if ($up) {
                //     $flag = true;
                // }
            }
            if ($flag) {

                $pod = $this->manufactures_model->rowProductionsOrdersDetais($suggest_exporting['productions_orders_details_id']);
                if ($pod) {
                    $content = lang('tnh_his_delete_exporting_producion');
                    $content = str_replace('{$1}', $suggest_exporting['reference_stock'], $content);
                    $content = str_replace('{$2}', $pod['reference_no'], $content);
                } else {
                    $content = lang('Xóa xuất kho sản xuất phiếu [' . $suggest_exporting['reference_stock'] . ']');
                }

                if ($suggest_exporting['po_id'] && $suggest_exporting['stage_id'] == STAGES_MATERIAL) {
                    $this->manufactures_model->updateProductionsOrdersItemsStagesUnPO($suggest_exporting['po_id'], $suggest_exporting['stage_id']);
                    
                    //Check
                    $this->manufactures_model->checkPrepareMaterialsTotal(['po_id' => $suggest_exporting['po_id']]);
                }

                insertActivityLog([
                    'type_parent_obj' => 'exporting_producion',
                    'table_obj' => 'tbl_suggest_exporting',
                    'id_obj' => $id,
                    'name_obj' => $suggest_exporting['reference_stock'],
                    'content' => $content,
                    'actions' => 'delete'
                ]);

                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function rowItem()
    {
        $data = [];
        if ($this->input->post()) {
            $item = $this->input->post('item_id');
            $warehouse_id = $this->input->post('warehouse_id');
            if (!empty($item)) {
                $item = explode('__', $item);
                $type_item = $item[0];
                $item_id = $item[1];

                $warehouses = $this->stock_model->getWarehouseItemsByItemIdAndTypeAndWarehouse($item_id, $type_item, $warehouse_id);
                foreach ($warehouses as $key => $value) {
                    $warehouses[$key]['location_name'] = recursiveLocations($value['localtion']);
                }
                $data['warehouses'] = $warehouses;
            }
        }
        echo json_encode($data);
    }

    public function searchMaterialAndSemiProducts($id = false)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');
        $params = $this->input->get('params');
        $material = $this->stock_model->searchMaterialsUnitWarehouse($term, $limit);
        $semi_product_outside = $this->stock_model->searchSemiProductsOutside($term, $limit);
        $semi_products = $this->stock_model->searchSemiProducts($term, $limit);
        $tools_supplies = $this->stock_model->searchToolsSupplies($term, $limit);

        $results = [];
        if (!empty($material)) {
            $results[] = ['text' => lang('materials'), 'children' => $material];
        }
        if (!empty($semi_products)) {
            $results[] = ['text' => lang('semi_products'), 'children' => $semi_products];
        }
        if (!empty($semi_product_outside)) {
            $results[] = ['text' => lang('semi_products_outside'), 'children' => $semi_product_outside];
        }
        if (!empty($tools_supplies)) {
            $results[] = ['text' => lang('tnh_tools_supplies'), 'children' => $tools_supplies];
        }
        $data['results'] = $results;

        if ($id) {
            // $data['row'] = ['id' => $product['id'], 'text' => $product['code']];
        }
        echo json_encode($data);
    }

    public function rowMaterialOrSemiProduct()
    {
        $data = [];
        if ($this->input->post()) {
            $item_id = $this->input->post('item_id');
            $unit = $this->input->post('unit');
            $item = false;
            $arr_unit_id = [];
            $arr_number_exchange = [];
            $number_exchange = 1;
            if (!empty($item_id)) {
                $arr = explode('__', $item_id);
                $type_item = $arr[0];
                $id = $arr[1];
                if ($type_item == "semi_products_outside" || $type_item == "semi_products") {
                    $semi_products_outside = $this->products_model->rowProduct($id);
                    $selected = $semi_products_outside['unit_id'];
                    array_push($arr_unit_id, $semi_products_outside['unit_id']);
                    $arr_number_exchange[$semi_products_outside['unit_id']]['number_exchange'] = 1;
                    $item = $semi_products_outside;
                } else if ($type_item == "materials") {
                    $material = $this->items_model->rowMaterial($id);
                    $exchange = $this->items_model->getExchangeItemsByItemId($id);
                    $selected = $material['unit_id'];
                    array_push($arr_unit_id, $material['unit_id']);
                    $selected = $material['unit_id'];
                    $arr_number_exchange[$material['unit_id']]['number_exchange'] = 1;
                    if (!empty($exchange)) {
                        foreach ($exchange as $key => $value) {
                            array_push($arr_unit_id, $value['unit_id']);
                            $arr_number_exchange[$value['unit_id']]['number_exchange'] = $value['number_exchange'];
                            // $selected = $value['unit_id'];
                        }
                    }
                    $item = $material;
                } else if ($type_item == "tools_supplies") {
                    $tools_supplies = $this->tools_supplies_model->rowToolsSupplies($id);
                    $selected = $tools_supplies['unit_id'];
                    array_push($arr_unit_id, $tools_supplies['unit_id']);
                    $arr_number_exchange[$tools_supplies['unit_id']]['number_exchange'] = 1;
                    $item = $tools_supplies;
                }
                $units = false;
                if (!empty($arr_unit_id)) {
                    $units = $this->products_model->getUnitsByArrId($arr_unit_id);
                    foreach ($units as $key => $value) {
                        $units[$key]['number_exchange'] = $arr_number_exchange[$value['unitid']]['number_exchange'];
                    }
                }
                $data['item'] = $item;
                $data['units'] = $units;
                $data['selected'] = $selected;
                $data['number_exchange'] = $number_exchange;
            }
        }
        echo json_encode($data);
    }

    public function agreeStock()
    {
        $data = [];
        if (!$this->perApproveExportingProducion) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }
        if ($this->input->get()) {
            $suggest_exporting_id = $this->input->get('suggest_exporting_id');
            $status = $this->input->get('status');
            $suggest_exporting = $this->manufactures_model->rowSuggestExporting($suggest_exporting_id);
            if ($suggest_exporting['warehouseman_id'] > 0) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_agree_warehoused');
                echo json_encode($data);
                die;
            }
            $date = date('Y-m-d H:i:s');
            $user_id = get_staff_user_id();
            if ($suggest_exporting['status_stock'] == $status) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_please_referesh_table');
                echo json_encode($data);
                die;
            }

            $up = $this->manufactures_model->updateSuggestExportingById($suggest_exporting_id, [
                'status_stock' => $status,
                'date_stock' => $date,
                'user_stock' => $user_id
            ]);

            if ($up) {
                @pusherTNHNotfication();
                $pod = $this->manufactures_model->rowProductionsOrdersDetais($suggest_exporting['productions_orders_details_id']);
                if ($pod) {
                    $content = lang('tnh_his_agree_exporting_producion');
                    $content = str_replace('{$1}', $suggest_exporting['reference_stock'], $content);
                    $content = str_replace('{$2}', $pod['reference_no'], $content);
                } else {
                    $content = lang('Thay đổi trạng thái xuất kho sản xuất phiếu [' . $suggest_exporting['reference_stock'] . ']');
                }


                insertActivityLog([
                    'type_parent_obj' => 'exporting_producion',
                    'table_obj' => 'tbl_suggest_exporting',
                    'id_obj' => $suggest_exporting_id,
                    'name_obj' => $suggest_exporting['reference_stock'],
                    'content' => $content,
                    'actions' => 'agree'
                ]);
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        }
        echo json_encode($data);
    }

    public function purchase_products()
    {
        if (!$this->perViewPurchaseProducts && !$this->perViewOwnPurchaseProducts) {
            accessDenied();
        }
        $arrBranch = get_branch_staff();
        $data['tnh'] = $this->tnh;
        $this->db->from('tbl_purchase_products');
        if (!$this->isAdmin) {
            if (!empty($arrBranch)) {
                $coverStrBranch = implode(",", $arrBranch);
                $this->db->where('tbl_purchase_products.branch_id IN (' . $coverStrBranch . ')');
            } else {
                $this->db->where('tbl_purchase_products.id', 0);
            }
        }
        $all = $this->db->count_all_results();

        $this->db->from('tbl_purchase_products');
        $this->db->where('warehouseman_id !=', 0);
        $this->db->where('EXISTS(
            SELECT 1
            FROM tbl_productions_orders_details
            WHERE tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id
            AND tbl_productions_orders_details.object_type = "orders"
        )');
        $this->db->where('tbl_purchase_products.is_errors', 0);
        if (!$this->isAdmin) {
            if (!empty($arrBranch)) {
                $coverStrBranch = implode(",", $arrBranch);
                $this->db->where('tbl_purchase_products.branch_id IN (' . $coverStrBranch . ')');
            } else {
                $this->db->where('tbl_purchase_products.id', 0);
            }
        }
        $approved = $this->db->count_all_results();

        $this->db->from('tbl_purchase_products');
        $this->db->where('warehouseman_id !=', 0);
        $this->db->where('type_business_plan', 0);
        $this->db->where('EXISTS(
            SELECT 1
            FROM tbl_productions_orders_details
            WHERE tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id
            AND tbl_productions_orders_details.object_type = "business_plan"
        )');
        $this->db->where('tbl_purchase_products.is_errors', 0);
        if (!$this->isAdmin) {
            if (!empty($arrBranch)) {
                $coverStrBranch = implode(",", $arrBranch);
                $this->db->where('tbl_purchase_products.branch_id IN (' . $coverStrBranch . ')');
            } else {
                $this->db->where('tbl_purchase_products.id', 0);
            }
        }
        $approved_business = $this->db->count_all_results();

        $this->db->from('tbl_purchase_products');
        $this->db->where('warehouseman_id', 0);
        $this->db->where('type_business_plan', 0);
        $this->db->where('EXISTS(
            SELECT 1
            FROM tbl_productions_orders_details
            WHERE tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id
            AND tbl_productions_orders_details.object_type = "business_plan"
        )');
        $this->db->where('tbl_purchase_products.is_errors', 0);
        if (!$this->isAdmin) {
            if (!empty($arrBranch)) {
                $coverStrBranch = implode(",", $arrBranch);
                $this->db->where('tbl_purchase_products.branch_id IN (' . $coverStrBranch . ')');
            } else {
                $this->db->where('tbl_purchase_products.id', 0);
            }
        }
        $un_approve = $this->db->count_all_results();

        $this->db->from('tbl_purchase_products');
        $this->db->where('warehouseman_id', 0);
        $this->db->where('type_business_plan', 1);
        $this->db->where('EXISTS(
            SELECT 1
            FROM tbl_productions_orders_details
            WHERE tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id
            AND tbl_productions_orders_details.object_type = "business_plan"
        )');
        $this->db->where('tbl_purchase_products.is_errors', 0);
        if (!$this->isAdmin) {
            if (!empty($arrBranch)) {
                $coverStrBranch = implode(",", $arrBranch);
                $this->db->where('tbl_purchase_products.branch_id IN (' . $coverStrBranch . ')');
            } else {
                $this->db->where('tbl_purchase_products.id', 0);
            }
        }
        $un_approved_chuyen = $this->db->count_all_results();

        $this->db->from('tbl_purchase_products');
        $this->db->where('warehouseman_id !=', 0);
        $this->db->where('type_business_plan', 1);
        $this->db->where('EXISTS(
            SELECT 1
            FROM tbl_productions_orders_details
            WHERE tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id
            AND tbl_productions_orders_details.object_type = "business_plan"
        )');
        $this->db->where('tbl_purchase_products.is_errors', 0);
        if (!$this->isAdmin) {
            if (!empty($arrBranch)) {
                $coverStrBranch = implode(",", $arrBranch);
                $this->db->where('tbl_purchase_products.branch_id IN (' . $coverStrBranch . ')');
            } else {
                $this->db->where('tbl_purchase_products.id', 0);
            }
        }
        $approved_chuyen = $this->db->count_all_results();

        $this->db->from('tbl_purchase_products');
        $this->db->where('tbl_purchase_products.is_errors', 1);
        if (!$this->isAdmin) {
            if (!empty($arrBranch)) {
                $coverStrBranch = implode(",", $arrBranch);
                $this->db->where('tbl_purchase_products.branch_id IN (' . $coverStrBranch . ')');
            } else {
                $this->db->where('tbl_purchase_products.id', 0);
            }
        }
        $tab_err = $this->db->count_all_results();

        $data['all'] = $all;
        $data['approved'] = $approved;
        $data['un_approve'] = $un_approve;
        $data['approved_business'] = $approved_business;
        $data['un_approved_chuyen'] = $un_approved_chuyen;
        $data['approved_chuyen'] = $approved_chuyen;
        $data['tab_err'] = $tab_err;
        $data['branch'] = getListBranch();
        $data['title'] = lang('purchase_products');
        $this->load->view('admin/stock/purchase_products', $data);
    }

    public function add_purchase_product()
    {
        if (!$this->perAddPurchaseProducts) {
            accessDenied();
        }

        if ($this->input->post('add')) {
            $data = [];
            $this->form_validation->set_rules('reference_no', lang("tnh_reference_purchase_products"), 'required|is_unique[tbl_purchase_products.reference_no]');
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('warehouses', lang("tnh_warehouses"), 'required');
            $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
            if ($this->form_validation->run() == true) {
                // print_arrays($this->input->post());
                // $reference_no = $this->input->post('reference_no');
                $reference_no = getReference('purchase_products');
                $date = to_sql_date($this->input->post('date'), true);
                $note = $this->input->post('note');
                $warehouses = $this->input->post('warehouses');
                $count_items = 0;
                $total_quantity = 0;
                $counter = $this->input->post('counter');
                $branch_id = $this->input->post('branch_id');
                $save_and_warehouse = !empty($this->input->post('save_and_warehouse')) ? $this->input->post('save_and_warehouse') : 0;
                $po_id = $this->input->post('po_id');
                $is_pass = $this->input->post('is_pass');

                if (!empty($counter)) {
                    foreach ($counter as $key => $value) {
                        $location_id = $this->input->post('location_id')[$value];
                        if (empty($location_id)) continue;
                        $items_id = $this->input->post('items_id')[$value];
                        $arr_item = explode('__', $items_id);
                        $type_item = $arr_item[1];
                        $item_id = $arr_item[0];
                        $unit_id = 0;
                        $conversion_quantity_unit = 1;
                        if ($type_item == "products") {
                            $info_item = $this->products_model->rowProduct($item_id);
                            $unit_id = $info_item['conversion_unit'];
                            $conversion_quantity_unit = $info_item['conversion_quantity_unit'];
                        }
                        if (empty($info_item)) continue;


                        if (empty($unit_id)) {
                            $data['result'] = 0;
                            $data['message'] = lang('Mặt hàng chưa có đơn vị sản xuất');
                            echo json_encode($data);
                            die;
                        }

                        $item_code = $info_item['code'];
                        $item_name = $info_item['name'];
                        $quantity = number_unformat($this->input->post('quantity')[$value]);
                        $note_item = $this->input->post('note_items')[$value];

                        $quantity_stock = $quantity;
                        $quantity_unit = roundNumberFormat($quantity / $conversion_quantity_unit, 4);

                        //exchange
                        $exchange = [];
                        if ($type_item == "products" || $type_item == "semi_products" || $type_item == "semi_products_outside") {
                            $exchangeUnits = $this->products_model->getExchangeProductsByProductId($item_id);
                            if (!empty($exchangeUnits)) {
                                foreach ($exchangeUnits as $k => $val) {
                                    if (empty($val)) continue;
                                    $quantity_exchange = $val['number_exchange'];
                                    $total_quantity_exchange = $quantity / $quantity_exchange;
                                    $exchange[] = [
                                        'unit_id' => $val['unit_id'],
                                        'quantity_exchange' => $quantity_exchange,
                                        'total_quantity_exchange' => $total_quantity_exchange,
                                    ];
                                }
                            }
                        }
                        //end exchange

                        $purchaseProductItems[] = [
                            'type_item' => $type_item,
                            'item_id' => $item_id,
                            'location_id' => $location_id,
                            'item_code' => $item_code,
                            'item_name' => $item_name,
                            'quantity' => $quantity,
                            'note_item' => $note_item,
                            'exchange' => $exchange,
                            'quantity_stock' => $quantity_stock,
                            'quantity_unit' => $quantity_unit,
                            'unit_id' => $unit_id,
                            'conversion_quantity_unit' => $conversion_quantity_unit,
                        ];

                        $total_quantity += $quantity;
                        $count_items++;
                    }
                }

                if (empty($purchaseProductItems)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_no_items');
                    echo json_encode($data);
                    die;
                }

                $fields = [
                    'reference_no' => $reference_no,
                    'date' => $date,
                    'warehouse_id' => $warehouses,
                    'count_items' => $count_items,
                    'total_quantity' => $total_quantity,
                    'note' => $note,
                    'status' => 'un_approved',
                    'created_by' => get_staff_user_id(),
                    'date_created' => date('Y-m-d H:i:s'),
                    'save_and_warehouse' => $save_and_warehouse,
                    'branch_id' => $branch_id,
                    'po_id' => $po_id,
                    'is_pass' => $is_pass,
                ];

                // if ($this->perApproveWarehousePurchaseProducts) {
                //     if (!empty($save_and_warehouse)) {
                //         $fields['status_stock'] = 'approved';
                //         $fields['date_stock'] = date('Y-m-d H:i:s');
                //         $fields['user_stock'] = $staff_general;
                //     }
                // }

                $id = $this->stock_model->insertPurchaseProducts($fields);
                if ($id) {
                    if (getReference('purchase_products') == $reference_no) {
                        updateReference('purchase_products');
                    }
                    // foreach ($purchaseProductItems as $key => $value) {
                    //     $purchaseProductItems[$key]['purchase_product_id'] = $id;
                    // }
                    // $this->stock_model->insertBatchPurchaseProductItems($purchaseProductItems);

                    foreach ($purchaseProductItems as $key => $value) {
                        $value['purchase_product_id'] = $id;
                        $exchange = $value['exchange'];
                        unset($value['exchange']);

                        $purchase_product_item_id = $this->stock_model->insertPurchaseProductItems($value);
                        if ($purchase_product_item_id) {
                            if (!empty($exchange)) {
                                foreach ($exchange as $k => $val) {
                                    $val['purchase_product_items_id'] = $purchase_product_item_id;
                                    $this->stock_model->insertPurchaseProductItemExchange($val);
                                }
                            }
                        }
                    }

                    //
                    if ($this->perApproveWarehousePurchaseProducts) {
                        if (!empty($save_and_warehouse)) {
                            $purchaseProduct = $this->stock_model->rowPurchaseProducts($id);
                            $_data = array(
                                'status' => 'approved',
                                'warehouseman_id' => get_staff_user_id(),
                                'date_warehouseman' => date('Y-m-d H:i:s')
                            );
                            $success = $this->db->update('tbl_purchase_products', $_data, array('id' => $id));
                            if ($success) {
                                log_activity('Import Warehouses items approved [ID export_warehouses: ' . $id);
                                $this->stock_model->decreaseWarehouse_purchase_products($id);

                                //activity log
                                if (!empty($purchaseProduct['productions_orders_details_id'])) {
                                    $pod = $this->manufactures_model->rowProductionsOrdersDetais($purchaseProduct['productions_orders_details_id']);
                                    $content = lang('tnh_his_warehouse_purchase_product_pod');
                                    $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                                    $content = str_replace('{$2}', $pod['reference_no'], $content);
                                } else {
                                    $content = lang('tnh_his_warehouse_purchase_product');
                                    $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                                }
                                insertActivityLog([
                                    'type_parent_obj' => 'purchase_products',
                                    'table_obj' => 'tbl_purchase_products',
                                    'id_obj' => $id,
                                    'name_obj' => $purchaseProduct['reference_no'],
                                    'content' => $content,
                                    'actions' => 'warehouse'
                                ]);
                                //end activity log
                            }
                        }
                    }

                    @pusherTNHNotfication();
                    $content = lang('tnh_his_add_purchase_product');
                    $content = str_replace('{$1}', $reference_no, $content);
                    insertActivityLog([
                        'type_parent_obj' => 'purchase_products',
                        'table_obj' => 'tbl_purchase_products',
                        'id_obj' => $id,
                        'name_obj' => $reference_no,
                        'content' => $content,
                        'actions' => 'add'
                    ]);

                    $data['result'] = 1;
                    $data['message'] = lang('success');
                    set_alert('success', lang('success'));
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            die;
        }
        $warehouses = $this->site_model->getWarehouse();
        $data['warehouses'] = $warehouses;
        $data['tnh'] = $this->tnh;
        $data['reference_no'] = getReference('purchase_products');
        $data['title'] = lang('tnh_add_purchase_product');
        $data['breadcrumb'] = [array('link' => base_url('admin/stock/purchase_products'), 'page' => lang('purchase_products')), array('link' => '#', 'page' => lang('tnh_add_purchase_product'))];
        $this->load->view('admin/stock/add_purchase_product', $data);
    }

    public function edit_purchase_product($id)
    {
        if (!$this->perEditPurchaseProducts) {
            accessDenied();
        }
        $purchaseProduct = $this->stock_model->rowPurchaseProducts($id);
        checkMyData($purchaseProduct['created_by']);
        $items = $this->stock_model->getPurchaseProductItems($id);
        if (empty($purchaseProduct)) {
            set_alert('danger', lang('no_data_exists'));
            redirect($_SERVER["HTTP_REFERER"]);
            die;
        }
        if ($purchaseProduct['status'] == "approved") {
            set_alert('danger', lang('browsed_cannot_be_edited'));
            redirect($_SERVER["HTTP_REFERER"]);
            die;
        }

        if ($purchaseProduct['productions_orders_details_id'] > 0) {
            set_alert('danger', lang('tnh_created_lsxct_not_edit'));
            redirect($_SERVER["HTTP_REFERER"]);
            die;
        }

        if ($this->input->post('edit')) {
            $data = [];
            if ($this->input->post('reference_no') != $purchaseProduct['reference_no']) {
                $this->form_validation->set_rules('reference_no', lang("tnh_reference_deliveries"), 'required|is_unique[tbl_purchase_products.reference_no]');
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('warehouses', lang("tnh_warehouses"), 'required');
            $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
            if ($this->form_validation->run() == true) {
                // print_arrays($this->input->post());
                // $reference_no = $this->input->post('reference_no');
                $date = to_sql_date($this->input->post('date'), true);
                $note = $this->input->post('note');
                $warehouses = $this->input->post('warehouses');
                $count_items = 0;
                $total_quantity = 0;
                $counter = $this->input->post('counter');
                $branch_id = $this->input->post('branch_id');
                $save_and_warehouse = !empty($this->input->post('save_and_warehouse')) ? $this->input->post('save_and_warehouse') : 0;
                $po_id = $this->input->post('po_id');
                $is_pass = $this->input->post('is_pass');

                if (!empty($counter)) {
                    foreach ($counter as $key => $value) {
                        $location_id = $this->input->post('location_id')[$value];
                        if (empty($location_id)) continue;
                        $items_id = $this->input->post('items_id')[$value];
                        $arr_item = explode('__', $items_id);
                        $type_item = $arr_item[1];
                        $item_id = $arr_item[0];
                        $unit_id = 0;
                        $conversion_quantity_unit = 1;

                        if ($type_item == "products") {
                            $info_item = $this->products_model->rowProduct($item_id);
                            $unit_id = $info_item['conversion_unit'];
                            $conversion_quantity_unit = $info_item['conversion_quantity_unit'];
                        }
                        if (empty($info_item)) continue;
                        $item_code = $info_item['code'];
                        $item_name = $info_item['name'];
                        $quantity = number_unformat($this->input->post('quantity')[$value]);
                        $note_item = $this->input->post('note_items')[$value];

                        $quantity_stock = $quantity;
                        $quantity_unit = roundNumberFormat($quantity / $conversion_quantity_unit, 4);

                        //exchange
                        $exchange = [];
                        if ($type_item == "products" || $type_item == "semi_products" || $type_item == "semi_products_outside") {
                            $exchangeUnits = $this->products_model->getExchangeProductsByProductId($item_id);
                            if (!empty($exchangeUnits)) {
                                foreach ($exchangeUnits as $k => $val) {
                                    if (empty($val)) continue;
                                    $quantity_exchange = $val['number_exchange'];
                                    $total_quantity_exchange = $quantity / $quantity_exchange;
                                    $exchange[] = [
                                        'unit_id' => $val['unit_id'],
                                        'quantity_exchange' => $quantity_exchange,
                                        'total_quantity_exchange' => $total_quantity_exchange,
                                    ];
                                }
                            }
                        }
                        //end exchange

                        $purchaseProductItems[] = [
                            'purchase_product_id' => $id,
                            'type_item' => $type_item,
                            'item_id' => $item_id,
                            'location_id' => $location_id,
                            'item_code' => $item_code,
                            'item_name' => $item_name,
                            'quantity' => $quantity,
                            'note_item' => $note_item,
                            'exchange' => $exchange,
                            'quantity_stock' => $quantity_stock,
                            'quantity_unit' => $quantity_unit,
                            'unit_id' => $unit_id,
                            'conversion_quantity_unit' => $conversion_quantity_unit,
                        ];

                        $total_quantity += $quantity;
                        $count_items++;
                    }
                }

                if (empty($purchaseProductItems)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_no_items');
                    echo json_encode($data);
                    die;
                }

                $fields = [
                    // 'reference_no' => $reference_no,
                    'date' => $date,
                    'warehouse_id' => $warehouses,
                    'count_items' => $count_items,
                    'total_quantity' => $total_quantity,
                    'note' => $note,
                    'status' => 'un_approved',
                    'updated_by' => get_staff_user_id(),
                    'date_updated' => date('Y-m-d H:i:s'),
                    'save_and_warehouse' => $save_and_warehouse,
                    'branch_id' => $branch_id,
                    'po_id' => $po_id,
                    'is_pass' => $is_pass,
                ];

                $up = $this->stock_model->updatePurchaseProducts($id, $fields);
                if ($up) {
                    $this->stock_model->deletePurchaseProductItems($id);
                    foreach ($items as $key => $value) {
                        $this->stock_model->deletePurchaseProductItemExchange($value['id']);
                    }
                    // $this->stock_model->insertBatchPurchaseProductItems($purchaseProductItems);

                    foreach ($purchaseProductItems as $key => $value) {
                        $value['purchase_product_id'] = $id;
                        $exchange = $value['exchange'];
                        unset($value['exchange']);

                        $purchase_product_item_id = $this->stock_model->insertPurchaseProductItems($value);
                        if ($purchase_product_item_id) {
                            if (!empty($exchange)) {
                                foreach ($exchange as $k => $val) {
                                    $val['purchase_product_items_id'] = $purchase_product_item_id;
                                    $this->stock_model->insertPurchaseProductItemExchange($val);
                                }
                            }
                        }
                    }

                    if ($this->perApproveWarehousePurchaseProducts) {
                        if (!empty($save_and_warehouse)) {
                            $purchaseProduct = $this->stock_model->rowPurchaseProducts($id);
                            $_data = array(
                                'status' => 'approved',
                                'warehouseman_id' => get_staff_user_id(),
                                'date_warehouseman' => date('Y-m-d H:i:s')
                            );
                            $success = $this->db->update('tbl_purchase_products', $_data, array('id' => $id));
                            if ($success) {
                                log_activity('Import Warehouses items approved [ID export_warehouses: ' . $id);
                                $this->stock_model->decreaseWarehouse_purchase_products($id);

                                //activity log
                                if (!empty($purchaseProduct['productions_orders_details_id'])) {
                                    $pod = $this->manufactures_model->rowProductionsOrdersDetais($purchaseProduct['productions_orders_details_id']);
                                    $content = lang('tnh_his_warehouse_purchase_product_pod');
                                    $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                                    $content = str_replace('{$2}', $pod['reference_no'], $content);
                                } else {
                                    $content = lang('tnh_his_warehouse_purchase_product');
                                    $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                                }
                                insertActivityLog([
                                    'type_parent_obj' => 'purchase_products',
                                    'table_obj' => 'tbl_purchase_products',
                                    'id_obj' => $id,
                                    'name_obj' => $purchaseProduct['reference_no'],
                                    'content' => $content,
                                    'actions' => 'warehouse'
                                ]);
                                //end activity log
                            }
                        }
                    }

                    $content = lang('tnh_his_edit_purchase_product');
                    $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                    insertActivityLog([
                        'type_parent_obj' => 'purchase_products',
                        'table_obj' => 'tbl_purchase_products',
                        'id_obj' => $id,
                        'name_obj' => $purchaseProduct['reference_no'],
                        'content' => $content,
                        'actions' => 'edit'
                    ]);

                    $data['result'] = 1;
                    $data['message'] = lang('success');
                    set_alert('success', lang('success'));
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            die;
        }

        $bodyItems = '';
        $counter = 0;
        $warehouse_id = $purchaseProduct['warehouse_id'];
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];

                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($value['unit_id']);
                    if (!empty($info['images'])) {
                        $images = base_url('uploads/products/' . $info['images']);
                    }
                }
                if (empty($images)) {
                    $images = base_url('assets/images/tnh/no_image.png');
                }

                $htmlExchange = '';
                if ($type_item == "products") {
                    $exchange_units = $this->stock_model->getPurchaseProductItemExchange($value['id']);
                    if (!empty($exchange_units)) {
                        foreach ($exchange_units as $k => $val) {
                            $htmlExchange .= '<div class="list-exchange">
                                <input type="hidden" class="form-control number-exchange" value="' . $val['quantity_exchange'] . '">
                                <span>' . $val['unit_name'] . ': </span>
                                <span class="text-number-exchange">' . formatNumber($val['total_quantity_exchange']) . '</span>
                            </div>';
                        }
                    }
                }

                $locations = recursiveLocationWarehouses($warehouse_id);
                $tdNumber = '<div class="td-number text-center">' . (++$key) . '</div>';
                $tdCode = '<div class="td-code mbot10"><input type="hidden" name="counter[' . $counter . ']" id="counter" class="form-control counter" value="' . $counter . '">
                        <input type="text" name="items_id[' . $counter . ']" id="items_' . $counter . '" class="items_id" style="width: 100%;" data-placeholder="' . lang('choose') . '" value="' . $items_id . '__' . $type_item . '"></div>' .
                    '<div class="type-item"></div>' .
                    '<div><div class="row-options"><a href="javascript:void(0)"class="text-danger delete-remind remove-row" onclick="removeRow(this)">' . lang('delete') . '</a></div></div>';
                $tdImage = '<div class="td-image">' .
                    '<div class="preview_image" style="width: auto;">' .
                    '<div class="display-block contract-attachment-wrapper img">' .
                    '<div style="width:45px;">' .
                    '<a href="' . $images . '" data-lightbox="customer-profile" class="display-block mbot5">' .
                    '<div class="">' .
                    '<img src="' . $images . '" style="border-radius: 50%">' .
                    '</div>' .
                    '</a>' .
                    '</div>' .
                    '</div>' .
                    '</div>' .
                    '</div>';
                $tdName = '<div class="td-item-name">' . $value['item_name'] . '</div>';
                $tdUnit = '<div class="td-unit">' . $unit['unit'] . '</div>';
                $tdPosition = '<div class="td-position">
                    <input type="hidden" name="localtion_current[' . $counter . ']" id="localtion_current[]" class="form-control localtion_current" value="' . $value['location_id'] . '">
                    <select data-placeholder="' . lang('choose') . '" name="location_id[' . $counter . ']" id="locations" class="locations" style="width: 100%;"><option value=""></option>' . $locations . '</select>
                </div>';
                $tdQuantity = '<div class="td-quantity"><input type="text" name="quantity[' . $counter . ']" id="quantity[]" class="form-control quantity number-format" style="width: 100%;" value="' . formatNumber($value['quantity']) . '"><div class="show-exchange text-primary mtop5 hide">' . $htmlExchange . '</span></div></div>';
                $tdNote = '<div class="td-note"><textarea name="note_items[' . $counter . ']" id="note_items[]" class="form-control" rows="3">' . $value['note_item'] . '</textarea></div>';
                $tdActions = '<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row" onclick="removeRow(this)"></i></div>';

                $bodyItems .= '<tr>
                    <td>' . $tdNumber . '</td>
                    <td>' . $tdCode . '</td>
                    <td>' . $tdImage . '</td>
                    <td>' . $tdName . '</td>
                    <td>' . $tdUnit . '</td>
                    <td>' . $tdPosition . '</td>
                    <td>' . $tdQuantity . '</td>
                    <td>' . $tdNote . '</td>
                    <td>' . $tdActions . '</td>
                </tr>';

                $counter++;
            }
        }

        $warehouses = $this->site_model->getWarehouse();
        $data['locations'] = !empty($locations) ? $locations : '';
        $data['bodyItems'] = $bodyItems;
        $data['counter'] = $counter;
        $data['warehouses'] = $warehouses;
        $data['purchaseProduct'] = $purchaseProduct;
        $data['tnh'] = $this->tnh;
        $data['id'] = $id;
        $data['title'] = lang('tnh_edit_purchase_product');
        $data['breadcrumb'] = [array('link' => base_url('admin/stock/purchase_products'), 'page' => lang('purchase_products')), array('link' => '#', 'page' => lang('tnh_edit_purchase_product'))];
        $this->load->view('admin/stock/edit_purchase_product', $data);
    }

    public function getLocationWarehouses()
    {
        $data = [];
        if ($this->input->post()) {
            $warehouse_id = $this->input->post('warehouse_id');
            $data['locations'] = recursiveLocationWarehouses($warehouse_id);
        }
        echo json_encode($data);
    }

    public function refereshReferencePurchaseProducts()
    {
        $data = [];
        if ($this->input->get('referesh')) {
            $reference_no = getReference('purchase_products');
            if ($this->stock_model->checkExistPurchaseProducts($reference_no)) {
                $ct = countReferenceMinus('purchase_products');
                $this->db->select("MAX(right(tbl_purchase_products.reference_no, char_length(tbl_purchase_products.reference_no) - $ct) + 0) as reference_no", false);
                $this->db->from('tbl_purchase_products');
                $rs = $this->db->get()->row_array();

                $max = $rs['reference_no'];
                $max++;
                updateReferenceNormal('purchase_products', $max);
                $reference_no = getReference('purchase_products');
            }
            $data['reference_no'] = $reference_no;
            $data['message'] = lang('tnh_referesh_success');
        }
        echo json_encode($data);
    }

    public function getPurchaseProducts()
    {
        if (!$this->perViewPurchaseProducts && !$this->perViewOwnPurchaseProducts) {
            accessDenied($js = true);
        }
        $arrBranch = get_branch_staff();
        $status_table = $this->input->post('status_table');
        $items_search = $this->input->post('items_search');
        $productions_orders_search = $this->input->post('productions_orders_search');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $branch_search = $this->input->post('branch_search');
        $tbOrders = "(
            SELECT
                tbl_orders.id as object_id,
                tbl_orders.reference_no as reference_no
            FROM tbl_orders
            WHERE tbl_orders.status_productions_orders != 0
        ) tb_orders";

        $tbBusinessPlan = "(
            SELECT
                tbl_business_plan.id as object_id,
                tbl_business_plan.reference_no as reference_no
            FROM tbl_business_plan
            WHERE tbl_business_plan.status_productions_orders != 0
        ) tb_business_plan";

        $tbTranferBusiness = "(
            SELECT
                tbl_tranfer_business_item.business_plan_item_id as business_plan_item_id,
                SUM(tbl_tranfer_business_item.quantity) as quantity
            FROM tbl_tranfer_business_item
            GROUP BY tbl_tranfer_business_item.business_plan_item_id
        ) tb_tranfer_business";

        // CONCAT(tbl_purchase_products.reference_no,'__',tbl_purchase_products.type_business_plan,'__',IF(tbl_productions_orders_details.object_type = 'orders',1,IF(tbl_productions_orders_details.object_type = 'business_plan',2,-1))) as reference_no,
        $this->datatables
            ->select("
                tbl_purchase_products.id as id,
                tbl_purchase_products.date as date,
                CONCAT(tbl_purchase_products.reference_no,'__',tbl_purchase_products.type_business_plan,'__',IF(tbl_purchase_products.is_errors = 1, -1, (IF(tbl_productions_orders_details.object_type = 'orders', 1, IF(tbl_productions_orders_details.object_type = 'business_plan', 2,-1)))),'__',COALESCE(tblbranch.name,''),'__',tbl_purchase_products.final_stage) as reference_no,
                COALESCE(tbl_productions_orders.reference_no, po_temp.reference_no) as reference_no_po,
                tbl_productions_orders_details.reference_no as reference_pod,
                IF(tbl_productions_orders_details.object_type = 'orders',tb_orders.reference_no,tb_business_plan.reference_no) as 'order',
                tblwarehouse.name as warehouse_name,
                tbl_purchase_products.total_quantity as total_quantity,
                IF(tbl_purchase_products.final_stage = 1,COALESCE(tb_tranfer_business.quantity,0),0) as total_quantity1,
                IF(tbl_purchase_products.final_stage = 1,(tbl_purchase_products.total_quantity - COALESCE(tb_tranfer_business.quantity,0)),0) as total_quantity2,
                CONCAT(tblstaff.firstname, ' ', tblstaff.lastname,'') as created_by,
                tbl_purchase_products.status as status,
                CONCAT(staff_warehouse.firstname, ' ', staff_warehouse.lastname,'') as staff_name_warehouse,
                tbl_purchase_products.note as note,
            ", false)
            ->from('tbl_purchase_products')
            ->join('tbl_productions_orders_details', 'tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id', 'left')
            ->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id', 'left')
            ->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id', 'left')
            ->join('tbl_productions_orders po_temp', 'po_temp.id = tbl_purchase_products.po_id', 'left')
            ->join('tblstaff', 'tblstaff.staffid = tbl_purchase_products.created_by', 'left')
            ->join('tblstaff staff_warehouse', 'staff_warehouse.staffid = tbl_purchase_products.warehouseman_id', 'left')
            ->join('tblbranch', 'tblbranch.id = tbl_purchase_products.branch_id', 'left')
            ->join($tbOrders, 'tb_orders.object_id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"', 'left')
            ->join($tbBusinessPlan, 'tb_business_plan.object_id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "business_plan"', 'left')
            ->join($tbTranferBusiness, 'tb_tranfer_business.business_plan_item_id = tbl_productions_orders_items.production_plan_item_id AND tbl_productions_orders_items.object_item_type = "business_plan"', 'left')
            ->join('tblwarehouse', 'tblwarehouse.id = tbl_purchase_products.warehouse_id', 'left');

        if (!$this->perViewPurchaseProducts) {
            $this->datatables->where('tbl_purchase_products.created_by', get_staff_user_id());
        }
        if (!empty($items_search)) {
            $items_search = explode('__', $items_search);
            $this->datatables->where('EXISTS (
                SELECT tbl_purchase_product_items.purchase_product_id
                FROM tbl_purchase_product_items
                WHERE tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id
                AND tbl_purchase_product_items.item_id = ' . $items_search[0] . '
            )');
        }

        if (!empty($productions_orders_search)) {
            $this->datatables->where('(
                EXISTS (
                    SELECT tbl_productions_orders_details.id
                    FROM tbl_productions_orders_details
                    WHERE tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id
                    AND tbl_productions_orders_details.productions_orders_id = ' . $productions_orders_search . '
                ) OR tbl_purchase_products.po_id = ' . $productions_orders_search . '
            )');
        }

        if (!empty($branch_search)) {
            $this->datatables->where('tbl_purchase_products.branch_id', $branch_search);
        }

        if (!$this->isAdmin) {
            if (!empty($arrBranch)) {
                $coverStrBranch = implode(",", $arrBranch);
                $this->datatables->where('tbl_purchase_products.branch_id IN (' . $coverStrBranch . ')');
            } else {
                $this->datatables->where('tbl_purchase_products.id', 0);
            }
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            $this->datatables->where('tbl_purchase_products.date >= ', $start_date_search);
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            $this->datatables->where('tbl_purchase_products.date <= ', $end_date_search);
        }
        if ($status_table != 'all') {
            if ($status_table == 'un_approved') {
                $this->datatables->where('tbl_purchase_products.warehouseman_id = 0 AND tbl_purchase_products.type_business_plan = 0 AND EXISTS (
                    SELECT 1
                    FROM tbl_productions_orders_details
                    WHERE tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id
                    AND tbl_productions_orders_details.object_type = "business_plan"
                )');
            } elseif ($status_table == 'approved') {
                $this->datatables->where('tbl_purchase_products.warehouseman_id != 0 AND EXISTS (
                    SELECT 1
                    FROM tbl_productions_orders_details
                    WHERE tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id
                    AND tbl_productions_orders_details.object_type = "orders"
                )');
            } elseif ($status_table == 'approved_business') {
                $this->datatables->where('tbl_purchase_products.warehouseman_id != 0 AND tbl_purchase_products.type_business_plan = 0 AND EXISTS (
                    SELECT 1
                    FROM tbl_productions_orders_details
                    WHERE tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id
                    AND tbl_productions_orders_details.object_type = "business_plan"
                )');
            } elseif ($status_table == 'un_approved_chuyen') {
                $this->datatables->where('tbl_purchase_products.warehouseman_id = 0 AND tbl_purchase_products.type_business_plan = 1 AND EXISTS (
                    SELECT 1
                    FROM tbl_productions_orders_details
                    WHERE tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id
                    AND tbl_productions_orders_details.object_type = "business_plan"
                )');
            } elseif ($status_table == 'approved_chuyen') {
                $this->datatables->where('tbl_purchase_products.warehouseman_id != 0 AND tbl_purchase_products.type_business_plan = 1 AND EXISTS (
                    SELECT 1
                    FROM tbl_productions_orders_details
                    WHERE tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id
                    AND tbl_productions_orders_details.object_type = "business_plan"
                )');
            } elseif ($status_table == 'is_pass') {
                $this->datatables->where('tbl_purchase_products.is_pass', 1);
            }
        }

        if ($status_table == 'tab_err') {
            $this->datatables->where('tbl_purchase_products.is_errors', 1);
        } else {
            $this->datatables->where('tbl_purchase_products.is_errors', 0);
        }

        $this->db->order_by('tbl_purchase_products.id DESC');

        $view = '<a class="tnh-modal" title="' . lang('view') . '" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url('admin/stock/view_purchase_product/$1') . '"><i class="fa fa-file-text-o width-icon-actions"></i> ' . lang('view') . ' ' . lang('tnh_purchase_warehouse') . '</a>';

        $edit = $this->perEditPurchaseProducts ? '<a class="tnh-edit" title="' . lang('edit') . '" href="' . base_url('admin/stock/edit_purchase_product/$1') . '"><i class="fa fa-edit width-icon-actions"></i> ' . lang('edit') . ' ' . lang('tnh_purchase_warehouse') . '</a>' : '';

        $print = $this->perPrintPurchaseProducts ? '<a href="' . base_url('admin/stock/print_purchase_product/$1') . '" target="_blank"><i class="fa fa-print"></i> ' . lang('print') . ' ' . lang('tnh_purchase_warehouse') . '</a>' : '';

        $print_new = $this->perPrintPurchaseProducts ? '<a href="' . base_url('admin/stock/print_purchase_product_new/$1') . '" target="_blank"><i class="fa fa-print"></i> ' . lang('print') . ' ' . lang('thẻ kho thành phẩm') . '</a>' : '';

        $delete = $this->perDeletePurchaseProducts ? '<a type="button" class="po tnh-delete" title="' . lang('delete') . '" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
            <button href=\'' . base_url('admin/stock/delete_purchase_product/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
            <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
        "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . ' ' . lang('tnh_purchase_warehouse') . '</a>' : '';

        $printBarcode = '<a href="' . base_url() . 'admin/stock/print_barcode/$1"><i class="fa fa-barcode"></i> ' . lang('tnh_print_barcode') . '</a>';

        $actions = '
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
            ' . lang('actions') . '
            <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                <li>' . $view . '</li>
                <li>' . $edit . '</li>
                <li>' . $print . '</li>
                <li>' . $print_new . '</li>
                <li class="not-outside">' . $delete . '</li>
            </ul>
        </div>';

        $this->datatables->add_column('actions', $actions, 'id');
        $iDisplayStart = $this->input->post('iDisplayStart');
        $data = json_decode($this->datatables->generate());
        // print_arrays($this->db->last_query());
        // foreach ($data->aaData as $key => $value) {
        //     $data->aaData[$key][0] = ++$iDisplayStart;
        // }
        echo json_encode($data);
    }

    public function view_purchase_product($id)
    {
        if (!$this->perViewPurchaseProducts && !$this->perViewOwnPurchaseProducts) {
            accessDenied($js = true);
        }
        $purchaseProduct = $this->stock_model->rowPurchaseProducts($id);
        if (!$this->perViewPurchaseProducts) {
            checkMyData($purchaseProduct['created_by'], true);
        }
        $items = $this->stock_model->getPurchaseProductItems($id);
        $warehouse = $this->stock_model->rowWarehouse($purchaseProduct['warehouse_id']);
        $pod = $this->manufactures_model->rowProductionsOrdersDetais($purchaseProduct['productions_orders_details_id']);

        $bodyItems = '';
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];
                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $dtUnit = $this->unit_model->rowUnit($info['unit_id']);
                    $unit = $this->unit_model->rowUnit($value['unit_id']);
                    if (!empty($info['images'])) {
                        $images = base_url('uploads/products/' . $info['images']);
                    }
                }
                if (empty($images)) {
                    $images = base_url('assets/images/tnh/no_image.png');
                }

                $location = str_replace('->', '<i class="fa fa-caret-right text-danger" aria-hidden="true"></i>', recursiveLocations($value['location_id']));

                //
                // $exchange = $this->stock_model->getPurchaseProductItemExchangeView($value['id']);
                // $html_exchange = '';
                // if (!empty($exchange)) {
                //     foreach ($exchange as $k => $val) {
                //         $html_exchange .= '<div class="">' .
                //             '<div class="col-md-12" style="padding: 0px;">' . $val['unit_name'] . ' - ' . $val['quantity_exchange'] . '(' . formatNumber($val['total_quantity_exchange']) . ')</div>' .
                //             '</div>';
                //     }
                // }
                //

                $html_exchange = '<div class="">' .
                    '<div class="col-md-12" style="padding: 0px;">' . $dtUnit['unit'] . '(' . formatNumber($value['quantity_unit']) . ')</div>' .
                    '</div>';

                $tdNumber = '<td class="text-center">' . (++$key) . '</td>';
                $tdImages = '<td>
                    <div class="td-image"><div class="preview_image" style="width: auto;"><div class="display-block contract-attachment-wrapper img"><div style="width:45px; margin: auto;"><a href="' . $images . '" data-lightbox="customer-profile" class="display-block mbot5"><div class=""><img src="' . $images . '" style="border-radius: 50%"></div></a></div></div></div></div>
                </td>';
                $tdCode = '<td>' . $info['code'] . '</td>';
                $tdName = '<td>' . $info['name'] . '</td>';
                $tdUnit = '<td class="text-center">' . $unit['unit'] . '</td>';
                $tdLocation = '<td>' . $location . '</td>';
                $tdQuantity = '<td class="text-center">' . formatNumber($value['quantity']) . '</td>';
                $tdUnitExchange = '<td>' . $html_exchange . '</td>';
                $tdPrice = '<td class="text-center">' . formatNumber($value['price']) . '</td>';
                $tdAmount = '<td class="text-right">' . formatNumber($value['amount']) . '</td>';
                $tdNote = '<td>' . $value['note_item'] . '</td>';

                $bodyItems .= '<tr>
                    ' . $tdNumber . '
                    ' . $tdImages . '
                    ' . $tdCode . '
                    ' . $tdName . '
                    ' . $tdUnit . '
                    ' . $tdLocation . '
                    ' . $tdQuantity . '
                    ' . $tdUnitExchange . '
                    ' . $tdPrice . '
                    ' . $tdAmount . '
                    ' . $tdNote . '
                </tr>';
            }
        }

        $data['id'] = $id;
        $data['created_by'] = get_staff_full_name($purchaseProduct['created_by']);
        if ($purchaseProduct['updated_by']) {
            $data['updated_by'] = get_staff_full_name($purchaseProduct['updated_by']);
        }
        $data['pod'] = $pod;
        $data['purchaseProduct'] = $purchaseProduct;
        $data['items'] = $items;
        $data['bodyItems'] = $bodyItems;
        $data['warehouse'] = $warehouse;
        $data['task'] = $this->site_model->rowTasks($purchaseProduct['task_id']);
        $data['shiftWork'] = $this->site_model->getShiftWorkById($data['task']['shift_work']);
        $this->load->view('admin/stock/view_purchase_product', $data);
    }
    private function _api_row($r)
    {
        // Chuẩn hóa row theo format frontend đang dùng
        return [
            'stage_id' => $r['id'],
            'order_code' => $r['reference_no'],
            'sku'        => $r['items_code'],
            'stage_idd' => $r['stage_id'],
            'stage'      => $r['stage_name'],
            'qty_plan'   => (int) $r['quantity'],
            'qty_done'   => (int) $r['quantity_done'],
            'qty_todo'   => (int) ($r['quantity'] - $r['quantity_done']),
            'percent'    => round(($r['quantity_done'] / $r['quantity'] * 100), 2),
            'bar_color'  => $this->_getColor(($r['quantity_done'] / $r['quantity'] * 100)),
        ];
    }
    private function _getColor($percent)
    {
        if ($percent >= 75) return '#22c55e'; // green
        if ($percent >= 40) return '#facc15'; // yellow
        return '#ef4444'; // red
    }
    public function delete_purchase_product($id)
    {
        $data = [];
        //check permission
        if (!$this->perDeletePurchaseProducts) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }
        //end
        if ($id) {
            $purchaseProduct = $this->stock_model->rowPurchaseProducts($id);
            $items = $this->stock_model->getPurchaseProductItems($id);
            if ($purchaseProduct['status'] != 'un_approved') {
                $data['result'] = 0;
                $data['message'] = lang('browsed_cannot_be_deleted');
                echo json_encode($data);
                die;
            }
            if ($this->stock_model->deletePurchaseProducts($id)) {
                $this->stock_model->deletePurchaseProductItems($id);
                foreach ($items as $key => $value) {
                    $this->stock_model->deletePurchaseProductItemExchange($value['id']);
                }

                $this->manufactures_model->deletePurchaseProductPoisub($id);
                if ($purchaseProduct['productions_orders_details_id'] > 0) {
                    if (!empty($purchaseProduct['final_stage'])) {
                        $this->manufactures_model->updateQuantityWarehoused($purchaseProduct['productions_orders_details_id'], $purchaseProduct['total_quantity'], $minus = 1);

                        // $this->manufactures_model->updateSetPOD($purchaseProduct['productions_orders_details_id'], $purchaseProduct['total_quantity'], $minus = 1);
                    }


                    $pod = $this->manufactures_model->rowProductionsOrdersDetais($purchaseProduct['productions_orders_details_id']);
                    $content = lang('tnh_his_delete_purchase_product_pod');
                    $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                    $content = str_replace('{$2}', $pod['reference_no'], $content);
                } else {
                    $content = lang('tnh_his_delete_purchase_product');
                    $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                }
                $this->db->select('
                        tbl_productions_orders_items_stages.id as id,
                        tbl_stages.name as stage_name, 
                        tbl_stages.id as stage_id,
                        tbl_productions_orders.reference_no,
                        tbl_productions_orders_items.items_code,
                        tbl_productions_orders_items.items_name,    
                        tbl_productions_orders_items.quantity,    
                        SUM(tbl_purchase_products.total_quantity) as quantity_done,    
                    ', false);
                $this->db->from('tbl_productions_orders_items_stages');
                $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
                $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items_stages.productions_orders_items_id', 'left');
                $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_items_stages.productions_orders_items_id', 'left');
                $this->db->join('tbl_purchase_products', 'tbl_purchase_products.pois_id = tbl_productions_orders_items_stages.id', 'left');
                $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_details.productions_orders_id', 'left');
                $this->db->where('tbl_productions_orders_items_stages.id', $purchaseProduct['pois_id']);
                $this->db->where('tbl_productions_orders_details.date_created >', '2025-09-01 00:00:00');
                $this->db->group_by('tbl_productions_orders_items_stages.id');
                // $this->db->order_by('tbl_productions_orders_items_stages.id ASC,tbl_productions_orders_items_stages.number ASC');
                $this->db->order_by('tbl_stages.id DESC');
                $rows = $this->db->get()->result_array();
                foreach ($rows as $key => $value) {
                    $updatedRow = $this->_api_row($value);
                    sendSocket([
                        'action'     => 'update',
                        'updatedRow' => $updatedRow
                    ], [], 'loadProgress');
                }
                insertActivityLog([
                    'type_parent_obj' => 'purchase_product',
                    'table_obj' => 'tbl_purchase_product',
                    'id_obj' => $id,
                    'name_obj' => $purchaseProduct['reference_no'],
                    'content' => $content,
                    'actions' => 'delete'
                ]);

                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function print_purchase_product_new($id)
    {
        if (!$this->perPrintPurchaseProducts) {
            accessDenied();
        }
        $data = [];
        $purchaseProduct = $this->stock_model->rowPurchaseProducts($id);
        $items = $this->stock_model->getPurchaseProductItems($id);

        $this->db->select('tbl_productions_orders.reference_no');
        $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_details.productions_orders_id');
        $this->db->where('tbl_productions_orders_details.id', $purchaseProduct['productions_orders_details_id']);
        $reference_no = $this->db->get('tbl_productions_orders_details')->row('reference_no');

        $data['title'] = lang('print') . ' ' . lang('thẻ kho thành phẩm');
        $data['type'] = 'P';
        $data['img'] = '';
        $items = $items;
        ob_end_clean();
        $bodyItems = '';
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];
                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                }
                $bodyItems = '<tr nobr="true">
                    <td style="width: 25%;text-align: center;">' . _dC($purchaseProduct['date']) . '</td>
                    <td style="width: 25%;text-align: center;">' . formatNumber($value['quantity']) . '</td>
                    <td style="width: 25%;"></td>
                    <td style="width: 25%;"></td>
                </tr>';
                for ($i = 0; $i < 20; $i++) {
                    $bodyItems .= '<tr nobr="true">
						<td style="width: 25%;text-align: center;"></td>
						<td style="width: 25%;text-align: center;"></td>
						<td style="width: 25%;"></td>
						<td style="width: 25%;"></td>
						</tr>';
                }

                ob_start();
                stylePdf();
                echo '
				<h1 class="text-center uppercase">' . lang('Thẻ kho Thành phẩm') . '</h1>
				<div>
					 <span>' . _l('Mã SP') . ': <span class="bold">' . $info['code'] . '</span></span><br>
					 <span>' . _l('Tên SP') . ': <span class="bold">' . $info['name'] . '</span></span><br>
					 <span>' . _l('Mã LSX') . ': <span class="bold">' . $reference_no . '</span></span><br>
				</div>
				<table class="table-items" cellspacing="0" cellpadding="5" border="1">
					<thead>
						<tr>
							<th class="bold text-center" style="width: 25%;">' . _l('Ngày') . '</th>
							<th class="bold text-center" style="width: 25%;">' . _l('SL Nhập') . '</th>
							<th class="bold text-center" style="width: 25%;">' . _l('SL Xuất') . '</th>
							<th class="bold text-center" style="width: 25%;">' . _l('Ký Tên') . '</th>
						</tr>
					</thead>
					<tbody>
						' . $bodyItems . '
					</tbody>
				</table>
			';
                $content[] = ob_get_contents();
                ob_end_clean();
            }
        }

        $data['content'] = $content;
        $pdf = @print_pdf_page_a5($data);
        $type = 'I';
        $pdf->Output(slug_it('123') . '.pdf', $type);
    }

    public function print_purchase_product($id)
    {
        if (!$this->perPrintPurchaseProducts) {
            accessDenied();
        }
        ob_end_clean();
        $data = [];
        $purchaseProduct = $this->stock_model->rowPurchaseProducts($id);
        $items = $this->stock_model->getPurchaseProductItems($id);
        $warehouse = $this->stock_model->rowWarehouse($purchaseProduct['warehouse_id']);
        // $img = file_get_contents(base_url('uploads/company/').get_option('company_logo'));
        $data['title'] = lang('print') . ' ' . lang('tnh_purchase_warehouse');
        $data['type'] = 'P';
        $data['img'] = '';

        $dtPod = get_table_where('tbl_productions_orders_details', ['id' => $purchaseProduct['productions_orders_details_id']], '', 'row_array');

        if ($purchaseProduct['po_id']) {
            $dtPo = get_table_where('tbl_productions_orders', ['id' => $purchaseProduct['po_id']], '', 'row_array');
        } else {
            $this->db->select('tbl_productions_orders.*', false);
            $this->db->from('tbl_productions_orders_details');
            $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_details.productions_orders_id');
            $this->db->where('tbl_productions_orders_details.id', $purchaseProduct['productions_orders_details_id']);
            $dtPo = $this->db->get()->row_array();
        }

        $bodyItems = '';
        $totalBox = 0;
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];
                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($value['unit_id']);
                }

                $location = recursiveLocations($value['location_id']);

                $dtBox = $this->stock_model->getPurchaseProductItemExchangeBox($value['id']);

                $box = !empty($dtBox['total_quantity_exchange']) ? $dtBox['total_quantity_exchange'] : 0;
                if (!empty($box)) {
                    $totalBox += $box;
                }

                $tdNumber = '<td class="text-center" style="width: 5%;">' . (++$key) . '</td>';
                $tdName = '<td style="width: 40%; text-align: left;">' . $info['name'] . '(' . $info['code'] . ')</td>';
                $tdUnit = '<td class="text-center" style="width: 10%;">' . $unit['unit'] . '</td>';
                $tdLocations = '<td style="width: 20%;">' . $location . '</td>';
                $tdQuantity = '<td class="text-center" style="width: 10%;">' . formatNumber($value['quantity']) . '</td>';
                $tdBox = '<td class="text-center" style="width: 10%;">' . formatNumber($box) . '</td>';
                $tdNote = '<td style="width: 15%;">' . $value['note_item'] . '</td>';

                $bodyItems .= '<tr nobr="true">
                    ' . $tdNumber . '
                    ' . $tdName . '
                    ' . $tdUnit . '
                    ' . $tdLocations . '
                    ' . $tdQuantity . '
                    ' . $tdNote . '
                </tr>';
            }
        }

        $day = date_format(date_create($purchaseProduct['date']), 'd');
        $month = date_format(date_create($purchaseProduct['date']), 'm');
        $year = date_format(date_create($purchaseProduct['date']), 'Y');
        $message = "";
        ob_start();
        stylePdf();
        echo '
            <h1 class="text-center uppercase">' . lang('purchase_products') . '</h1>
            <span class="text-right">
                <span class="italic">' . _l('tnh_reference_purchase_products') . ': ' . $purchaseProduct['reference_no'] . '</span><br>
                <span class="italic">' . _l('date') . ': ' . _d($purchaseProduct['date'], true) . '</span>
            </span>
            <p>
                <span>' . _l('tnh_warehouses') . ': <span class="bold">' . $warehouse['name'] . '</span></span><br>
                <span>' . _l('Lệnh sản xuất chi tiết') . ': <span class="bold">' . (!empty($dtPod['reference_no']) ? $dtPod['reference_no'] : '') . '</span></span><br>
                <span>' . _l('Lệnh sản xuất tổng') . ': <span class="bold">' . (!empty($dtPo['reference_no']) ? $dtPo['reference_no'] : '') . '</span></span><br>
            </p>
            <table class="table-items" cellspacing="0" cellpadding="5" border="1">
                <thead>
                    <tr>
                        <th class="bold text-center" style="width: 5%;">' . _l('tnh_numbers') . '</th>
                        <th class="bold text-center" style="width: 40%;">' . _l('tnh_its') . '</th>
                        <th class="bold text-center" style="width: 10%;">' . _l('tnh_dvt') . '</th>
                        <th class="bold text-center" style="width: 20%;">' . _l('tnh_position') . '</th>
                        <th class="bold text-center" style="width: 10%;">' . _l('quantity') . '</th>
                        <th class="bold text-center" style="width: 15%;">' . _l('tnh_note') . '</th>
                    </tr>
                </thead>
                <tbody>
                    ' . $bodyItems . '
                </tbody>
                <tfoot>
                    <tr class="bold">
                        <th class="text-right" colspan="4">' . _l('tnh_total') . '</th>
                        <th class="text-center">' . formatNumber($purchaseProduct['total_quantity']) . '</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
            <p class="text-right"><span>Ngày ' . $day . ' tháng ' . $month . ' năm ' . $year . '</span></p>
            <table style="width: 100%">
                <tr>
                    <td class="text-center">
                        <span class="bold">' . _l('tnh_deliver') . '</span><br>
                        <span>' . _l('tnh_sign_write_full_name') . '</span>
                    </td>
                    <td class="text-center">
                        <span class="bold">' . _l('tnh_receiver') . '</span><br>
                        <span>' . _l('tnh_sign_write_full_name') . '</span>
                    </td>
                    <td class="text-center">
                        <span class="bold">' . _l('tnh_stocker') . '</span><br>
                        <span>' . _l('tnh_sign_write_full_name') . '</span>
                    </td>
                </tr>
            </table>
        ';

        $content = ob_get_contents();
        ob_end_clean();

        $data['content'] = $content;
        $pdf = @print_pdf_tnh($data);
        $type = 'I';
        $pdf->Output(slug_it('123') . '.pdf', $type);
    }
    // hau
    public function confirm_warehous()
    {
        if (!has_permission('stock_exporting_producion', '', 'approve_warehouse')) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_q_warehouse')
            ));
            die;
        }
        $id = $this->input->post('id');
        $warehouseman_id = $this->input->post('warehouseman_id');
        $ktr = get_table_where('tbl_suggest_exporting', array('id' => $id), '', 'row');
        if (empty($warehouseman_id)) {
            if (!empty($ktr->warehouseman_id)) {
                echo json_encode(array(
                    'alert_type' => 'warning',
                    'message' => _l('ch_export_confirm_warehous')
                ));
                die;
            }
            $_data = array(
                'warehouseman_id' => get_staff_user_id(),
                'date_warehouseman' => date('Y-m-d H:i:s')
            );
            if (!test_quantity_exporting_producion_warehouses($id)) {
                $data['success'] = false;
                $data['message'] = array(
                    'alert_type' => 'warning',
                    'message' => _l('test_quantyti_time_return')
                );
                $data['item'] = get_items_quantity_exporting_warehouses($id);
                echo json_encode($data);
                die;
            } else {
                $success    = $this->db->update('tbl_suggest_exporting', $_data, array('id' => $id));
                $alert_type = 'warning';
                $message    = _l('ch_no_successful_approval');
                if ($success) {
                    $alert_type = 'success';
                    $message    = _l('ch_successful_approval');
                    log_activity('Export Warehouses items approved [ID export_warehouses: ' . $id);
                    $this->stock_model->decreaseWarehouse($id);
                }

                //notfication and activity log
                @pusherTNHNotfication();
                $suggest_exporting = $this->manufactures_model->rowSuggestExporting($id);
                $pod = $this->manufactures_model->rowProductionsOrdersDetais($suggest_exporting['productions_orders_details_id']);
                if ($pod) {
                    $content = lang('tnh_his_warehouse_exporting_producion');
                    $content = str_replace('{$1}', $suggest_exporting['reference_stock'], $content);
                    $content = str_replace('{$2}', $pod['reference_no'], $content);
                } else {
                    $content = lang('Duyệt kho cho xuất kho sản xuất phiếu [' . $suggest_exporting['reference_stock'] . ']');
                }

                insertActivityLog([
                    'type_parent_obj' => 'exporting_producion',
                    'table_obj' => 'tbl_suggest_exporting',
                    'id_obj' => $id,
                    'name_obj' => $suggest_exporting['reference_stock'],
                    'content' => $content,
                    'actions' => 'warehouse'
                ]);
                //

                echo json_encode(array(
                    'alert_type' => 'success',
                    'message' => _l('ch_export_confirm')
                ));
                die;
            }
        } else {
            if (empty($ktr->warehouseman_id)) {
                echo json_encode(array(
                    'alert_type' => 'warning',
                    'message' => _l('ch_exsit_cancel_confirm_warehouse')
                ));
                die;
            } else {
                $_data = array(
                    'warehouseman_id' => 0,
                    'date_warehouseman' => NULL
                );
                $success    = $this->db->update('tbl_suggest_exporting', $_data, array('id' => $id));
                if ($success) {
                    $exporting = get_table_where('tbl_suggest_exporting', array('id' => $id), '', 'row');
                    $items = get_table_where('tbl_suggest_exporting_items', array('suggest_exporting_id' => $id));
                    $this->stock_model->increaseadWarehouse($id, $items, $exporting->warehouse_id);

                    //notfication and activity log
                    @pusherTNHNotfication();
                    $suggest_exporting = $this->manufactures_model->rowSuggestExporting($id);
                    $pod = $this->manufactures_model->rowProductionsOrdersDetais($suggest_exporting['productions_orders_details_id']);
                    if ($pod) {
                        $content = lang('tnh_his_un_warehouse_exporting_producion');
                        $content = str_replace('{$1}', $suggest_exporting['reference_stock'], $content);
                        $content = str_replace('{$2}', $pod['reference_no'], $content);
                    } else {
                        $content = lang('Duyệt kho cho xuất kho sản xuất phiếu [' . $suggest_exporting['reference_stock'] . ']');
                    }

                    insertActivityLog([
                        'type_parent_obj' => 'exporting_producion',
                        'table_obj' => 'tbl_suggest_exporting',
                        'id_obj' => $id,
                        'name_obj' => $suggest_exporting['reference_stock'],
                        'content' => $content,
                        'actions' => 'un_warehouse'
                    ]);
                    //
                    echo json_encode(array(
                        'alert_type' => 'success',
                        'message' => _l('Hủy duyệt kho thành công!')
                    ));
                    die;
                }
            }
        }
    }
    public function confirm_warehous_purchase_internal()
    {
        // if(!has_permission('stock_purchase_products', '', 'approve_warehouse'))
        // {
        //     echo json_encode(array(
        //         'alert_type' => 'warning',
        //         'message' => _l('ch_q_warehouse')
        //         ));die;
        // }
        $id = $this->input->post('id');
        $warehouseman_id = $this->input->post('warehouseman_id');
        $ktr = get_table_where('tbl_purchase_internal', array('id' => $id), '', 'row');
        $purchaseInternal = $this->stock_model->rowPurchaseInternal($id);

        if (empty($warehouseman_id)) {
            if (!empty($ktr->warehouseman_id)) {
                echo json_encode(array(
                    'alert_type' => 'warning',
                    'message' => _l('ch_export_confirm_warehous')
                ));
                die;
            }
            $_data = array(
                'warehouseman_id' => get_staff_user_id(),
                'date_warehouseman' => date('Y-m-d H:i:s')
            );
            $success    = $this->db->update('tbl_purchase_internal', $_data, array('id' => $id));
            $alert_type = 'warning';
            $message    = _l('ch_no_successful_approval');
            if ($success) {
                $alert_type = 'success';
                $message    = _l('ch_successful_approval');
                log_activity('Import Warehouses items approved [ID export_warehouses: ' . $id);
                $this->stock_model->decreaseWarehouse_purchase_internal($id);
            }

            //activity log
            if (!empty($purchaseInternal['pod_id'])) {
                $pod = $this->manufactures_model->rowProductionsOrdersDetais($purchaseInternal['pod_id']);
                $content = lang('tnh_his_warehouse_purchase_internal_pod');
                $content = str_replace('{$1}', $purchaseInternal['reference_no'], $content);
                $content = str_replace('{$2}', $pod['reference_no'], $content);
            } else {
                $content = lang('tnh_his_warehouse_purchase_internal');
                $content = str_replace('{$1}', $purchaseInternal['reference_no'], $content);
            }

            insertActivityLog([
                'type_parent_obj' => 'purchase_internal',
                'table_obj' => 'tbl_purchase_internal',
                'id_obj' => $id,
                'name_obj' => $purchaseInternal['reference_no'],
                'content' => $content,
                'actions' => 'warehouse'
            ]);
            //

            echo json_encode(array(
                'alert_type' => 'success',
                'message' => _l('ch_export_confirm')
            ));
            die;
        } else {
            if (empty($ktr->warehouseman_id)) {
                echo json_encode(array(
                    'alert_type' => 'warning',
                    'message' => _l('ch_exsit_cancel_confirm_warehouse')
                ));
                die;
            } else {
                $test_quantity = get_table_where('tblwarehouse_product', array('import_id' => $id, 'quantity_export >' => 0, 'type_export ' => 20), '', 'row');
                if (!empty($test_quantity)) {
                    echo json_encode(array(
                        'alert_type' => 'warning',
                        'message' => _l('Số lượng không đủ để bỏ duyệt kho'),
                    ));
                    die;
                }
                $_data = array(
                    'warehouseman_id' => 0,
                    'date_warehouseman' => NULL
                );
                $success    = $this->db->update('tbl_purchase_internal', $_data, array('id' => $id));
                if ($success) {
                    // $exporting = get_table_where('tbl_purchase_products',array('id'=>$id),'','row');
                    // $items = get_table_where('tbl_purchase_product_items',array('suggest_exporting_id'=>$id));
                    $this->stock_model->increaseadWarehouse_purchase_internal($id);

                    //activity log
                    if (!empty($purchaseInternal['pod_id'])) {
                        $pod = $this->manufactures_model->rowProductionsOrdersDetais($purchaseInternal['pod_id']);
                        $content = lang('tnh_his_un_warehouse_purchase_internal_pod');
                        $content = str_replace('{$1}', $purchaseInternal['reference_no'], $content);
                        $content = str_replace('{$2}', $pod['reference_no'], $content);
                    } else {
                        $content = lang('tnh_his_un_warehouse_purchase_internal');
                        $content = str_replace('{$1}', $purchaseInternal['reference_no'], $content);
                    }

                    insertActivityLog([
                        'type_parent_obj' => 'purchase_internal',
                        'table_obj' => 'tbl_purchase_internal',
                        'id_obj' => $id,
                        'name_obj' => $purchaseInternal['reference_no'],
                        'content' => $content,
                        'actions' => 'un_warehouse'
                    ]);
                    //

                    echo json_encode(array(
                        'alert_type' => 'success',
                        'message' => _l('Hủy duyệt kho thành công!')
                    ));
                    die;
                }
            }
        }
    }
    public function confirm_warehous_purchase_products()
    {
        if (!has_permission('stock_purchase_products', '', 'approve_warehouse') && !has_permission('stock_purchase_products', '', 'approve')) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_q_warehouse')
            ));
            die;
        }
        $id = $this->input->post('id');
        $warehouseman_id = $this->input->post('warehouseman_id');
        $ktr = get_table_where('tbl_purchase_products', array('id' => $id), '', 'row');
        if (empty($ktr)) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('Không tồn tại phiếu !')
            ));
            die;
        }

        if ($ktr->is_errors == 1) {
            if (!$this->perApproveWarehouseErrPP) {
                echo json_encode(array(
                    'alert_type' => 'warning',
                    'message' => _l('ch_q_warehouse')
                ));
                die;
            }
        } else if ($ktr->type_business_plan == 0) {
            if (!has_permission('stock_purchase_products', '', 'approve_warehouse')) {
                echo json_encode(array(
                    'alert_type' => 'warning',
                    'message' => _l('ch_q_warehouse')
                ));
                die;
            }
        } else {
            if (!has_permission('stock_purchase_products', '', 'approve')) {
                echo json_encode(array(
                    'alert_type' => 'warning',
                    'message' => _l('ch_q_warehouse')
                ));
                die;
            }
        }

        if (!empty($this->input->post('items'))) {
            foreach ($this->input->post('items') as $key => $value) {
                $dtLocation = get_table_where('tbllocaltion_warehouses', ['id' => $value['location_id']], '', 'row_array');
                $dtItem = get_table_where('tbl_purchase_product_items', ['id' => $value['id']], '', 'row_array');
                $this->db->update(
                    'tbl_purchase_product_items',
                    array('location_id' => $value['location_id']),
                    array('id' => $value['id'])
                );
                insertActivityLog([
                    'type_parent_obj' => 'purchase_products',
                    'table_obj' => 'tbl_purchase_products',
                    'id_obj' => $id,
                    'name_obj' => $ktr->reference_no,
                    'content' => lang('Thay đổi vị trí kho ' . $dtLocation['name'] . ' mặt hàng ' . $dtItem['item_name'] . '') . ' [' . $ktr->reference_no . ']',
                    'actions' => 'edit'
                ]);
            }
        }


        $purchaseProduct = $this->stock_model->rowPurchaseProducts($id);


        $dtTranferBusiness = [];
        if ($purchaseProduct['final_stage'] == 1) {

            $tbTranfer = "(
                SELECT 
                    tbltransfer_warehouse_detail.tranfer_business_item_id as tranfer_business_item_id,
                    SUM(tbltransfer_warehouse_detail.quantity_net) as quantity_hold
                FROM tbltransfer_warehouse_detail
                WHERE tbltransfer_warehouse_detail.tranfer_business_item_id != 0
                GROUP BY tbltransfer_warehouse_detail.tranfer_business_item_id
            ) tb_tranfer";

            $this->db->select('tbl_productions_orders_details.id as pod_id,
                tbl_tranfer_business_item.*,
                (tbl_tranfer_business_item.quantity - coalesce(tb_tranfer.quantity_hold,0)) as quantity
                ');
            $this->db->from('tbl_productions_orders_details');
            $this->db->join(
                'tbl_productions_orders_items',
                'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id'
            );
            $this->db->join(
                'tbl_tranfer_business_item',
                'tbl_tranfer_business_item.business_plan_item_id = tbl_productions_orders_items.production_plan_item_id AND tbl_productions_orders_items.object_item_type = "business_plan"'
            );
            $this->db->join(
                'tbl_tranfer_business',
                'tbl_tranfer_business.id = tbl_tranfer_business_item.tranfer_business_id'
            );
            $this->db->join(
                'tbl_purchase_products',
                'tbl_purchase_products.productions_orders_details_id = tbl_productions_orders_details.id'
            );
            $this->db->join(
                $tbTranfer,
                'tb_tranfer.tranfer_business_item_id = tbl_tranfer_business_item.id',
                'left'
            );
            $this->db->where('tbl_productions_orders_details.id', $purchaseProduct['productions_orders_details_id']);
            $this->db->where('tbl_purchase_products.id', $id);
            $this->db->where('tbl_purchase_products.final_stage', 1);
            $this->db->where('(tbl_tranfer_business_item.quantity - coalesce(tb_tranfer.quantity_hold,0)) > 0');
            $this->db->order_by('tbl_tranfer_business.date_created asc');
            $dtTranferBusiness = $this->db->get()->result_array();
        }

        if ($warehouseman_id == '1') {
            if (!empty($ktr->warehouseman_id)) {
                echo json_encode(array(
                    'alert_type' => 'warning',
                    'message' => _l('ch_export_confirm_warehous')
                ));
                die;
            }
            $_data = array(
                'status' => 'approved',
                'warehouseman_id' => get_staff_user_id(),
                'date_warehouseman' => date('Y-m-d H:i:s')
            );
            $success    = $this->db->update('tbl_purchase_products', $_data, array('id' => $id));
            $alert_type = 'warning';
            $message    = _l('ch_no_successful_approval');
            if ($success) {
                $alert_type = 'success';
                $message    = _l('ch_successful_approval');
                log_activity('Import Warehouses items approved [ID export_warehouses: ' . $id);
                $this->stock_model->decreaseWarehouse_purchase_products($id);

                if (!empty($purchaseProduct['final_stage'])) {
                    calCostingFinishedProduct($purchaseProduct['productions_orders_details_id']);
                }
                //dt
                $quantityCheck = $purchaseProduct['total_quantity'];
                $tranferItems = [];
                $transfer = [];
                $transfer_to_tranfer_business = [];
                $total = 0;
                if ($ktr->type_business_plan == 1) {
                    if (!empty($dtTranferBusiness)) {
                        foreach ($dtTranferBusiness as $kk => $vv) {
                            if ($quantityCheck > 0) {
                                $info = $this->products_model->rowProduct($vv['item_id']);
                                $conversion_quantity_unit = $info['conversion_quantity_unit'];
                                $exchange_unit = 1;
                                $exchange_stock = $info['conversion_quantity_unit'];
                                $orderCheck = get_table_where(
                                    'tbl_order_items',
                                    ['id' => $vv['order_item_id']],
                                    '',
                                    'row_array'
                                );

                                if ($orderCheck['unit_id'] == $info['unit_id']) {
                                    $quantity_unit = $orderCheck['total_quantity_item'];
                                    $quantity_stock = roundNumberFormat(
                                        $quantity_unit * $conversion_quantity_unit,
                                        4
                                    );
                                } else {
                                    $quantity_stock = $orderCheck['total_quantity_item'];
                                    $quantity_unit = roundNumberFormat(
                                        $quantity_stock / $conversion_quantity_unit,
                                        4
                                    );
                                }
                                $quantity_order = $quantity_stock;
                                $order_item_id = $vv['order_item_id'];

                                $this->db->select('coalesce(SUM(quantity),0) + coalesce(SUM(quantity_loss),0) + coalesce(SUM(quantity_sample),0) as quantity');
                                $this->db->from('tbl_delivery_items');
                                $this->db->join('tbl_deliveries', 'tbl_deliveries.id = tbl_delivery_items.delivery_id');
                                $this->db->where('tbl_deliveries.warehouseman_id != 0');
                                $this->db->where('order_item_id', $vv['order_item_id']);
                                $deliveryCheck = $this->db->get()->row_array();

                                $quantity_delivery = 0;
                                if (!empty($deliveryCheck)) {
                                    $quantity_delivery = $deliveryCheck['quantity'];
                                }

                                if ($orderCheck['unit_id'] == $info['unit_id']) {
                                    $quantity_unit_delivery = $quantity_delivery;
                                    $quantity_stock_delivery = roundNumberFormat(
                                        $quantity_unit_delivery * $conversion_quantity_unit,
                                        4
                                    );
                                } else {
                                    $quantity_stock_delivery = $quantity_delivery;
                                    $quantity_unit_delivery = roundNumberFormat(
                                        $quantity_stock_delivery / $conversion_quantity_unit,
                                        4
                                    );
                                }
                                $quantity_delivery = $quantity_stock_delivery;

                                $tb_warehouse_product = "(
                                    SELECT SUM(tblwarehouse_product.quantity_export) as quantity_export,
                                    tblwarehouse_product.warehouse_id,
                                    tblwarehouse_product.localtion,
                                    tblwarehouse_product.import_id,
                                    tblwarehouse_product.product_id,
                                    tblwarehouse_product.type_items,
                                    tblwarehouse_product.lot_code,
                                    tblwarehouse_product.date_sx,
                                    tblwarehouse_product.date_sd,
                                    tblwarehouse_product.date_use
                                    FROM `tblwarehouse_product` 
                                    WHERE type_export = 2
                                    GROUP BY `warehouse_id`,`localtion`,`import_id`,`product_id`,`type_items`,lot_code,date_sx,date_sd,date_use
                                ) tb_warehouse_product";

                                $tbTranfer = "(
                                    SELECT 
                                        tbltransfer_warehouse_detail.order_id_item as order_id_item,
                                        SUM(tbltransfer_warehouse_detail.quantity_net - COALESCE(tb_warehouse_product.quantity_export,0)) as quantity_hold
                                    FROM tbltransfer_warehouse_detail
                                    LEFT JOIN $tb_warehouse_product ON tb_warehouse_product.warehouse_id = tbltransfer_warehouse_detail.warehouses_to
                                    AND tb_warehouse_product.localtion = tbltransfer_warehouse_detail.localtion_to
                                    AND tb_warehouse_product.import_id = tbltransfer_warehouse_detail.id_transfer
                                    AND tb_warehouse_product.product_id = tbltransfer_warehouse_detail.id_items
                                    AND tb_warehouse_product.type_items = tbltransfer_warehouse_detail.type
                                    AND COALESCE(tb_warehouse_product.lot_code,-1) = COALESCE(tbltransfer_warehouse_detail.lot_code,-1)
                                    AND COALESCE(tb_warehouse_product.date_sx,-1) = COALESCE(tbltransfer_warehouse_detail.date_sx,-1)
                                    AND COALESCE(tb_warehouse_product.date_sd,-1) = COALESCE(tbltransfer_warehouse_detail.date_sd,-1)
                                    AND COALESCE(tb_warehouse_product.date_use,-1) = COALESCE(tbltransfer_warehouse_detail.date_use,-1)
                                    WHERE (tbltransfer_warehouse_detail.quantity_net - COALESCE(tb_warehouse_product.quantity_export,0)) > 0 
                                    AND tbltransfer_warehouse_detail.order_id_item = $order_item_id
                                )";
                                $dtTranferItemW = $this->db->query($tbTranfer)->row_array();

                                $quantity_hold = 0;
                                if (!empty($dtTranferItemW)) {
                                    $quantity_hold = (float)$dtTranferItemW['quantity_hold'];
                                }

                                $quantityNeedHold = $quantity_order - $quantity_delivery - $quantity_hold;
                                if ($quantityNeedHold <= 0) {
                                    continue;
                                }
                                $vv['quantity'] = $quantityNeedHold > $vv['quantity'] ? $vv['quantity'] : $quantityNeedHold;
                                $quantitynet = $quantityCheck;
                                $quantityCheck = $quantityCheck - $vv['quantity'];
                                $itemType = 'product';
                                $purchaseProductItem = get_table_where(
                                    'tbl_purchase_product_items',
                                    ['purchase_product_id' => $purchaseProduct['id']],
                                    '',
                                    'row_array'
                                );
                                $get_item = get_items($vv['item_id'], $itemType);
                                $locations = get_table_where('tbllocaltion_warehouses', array(
                                    'warehouse' => WAREHOUSES_HOLD,
                                    'tranfer_business_id' => $vv['tranfer_business_id'],
                                    'order_id' => $vv['order_id'],
                                ), '', 'row');
                                if (empty($locations)) {
                                    $tranferBusinessCheck = get_table_where(
                                        'tbl_tranfer_business',
                                        array('id' => $vv['tranfer_business_id']),
                                        '',
                                        'row'
                                    );
                                    $orderCheck = get_table_where(
                                        'tbl_orders',
                                        array('id' => $vv['order_id']),
                                        '',
                                        'row'
                                    );
                                    $in_local = array();
                                    $in_local['name'] = $tranferBusinessCheck->reference_no . '__' . $orderCheck->reference_no;
                                    $in_local['code'] = $tranferBusinessCheck->reference_no . '__' . $orderCheck->reference_no;
                                    $in_local['name_parent'] = $tranferBusinessCheck->reference_no . '__' . $orderCheck->reference_no;
                                    $in_local['warehouse'] = WAREHOUSES_HOLD;
                                    $in_local['child'] = 1;
                                    $in_local['create_by'] = get_staff_user_id();
                                    $in_local['date_create'] = date('Y-m-d H:i:s');
                                    $in_local['status'] = 0;
                                    $in_local['lever'] = 1;
                                    $in_local['productions_plan_id'] = 0;
                                    $in_local['pod_id'] = $vv['pod_id'];
                                    $in_local['stage_id'] = 0;
                                    $in_local['stage_id_import_outsource'] = 0;
                                    $in_local['order_item_id'] = 0;
                                    $in_local['order_id'] = $vv['order_id'];
                                    $in_local['tranfer_business_id'] = $vv['tranfer_business_id'];
                                    $this->db->insert('tbllocaltion_warehouses', $in_local);
                                    $location_to = $this->db->insert_id();
                                } else {
                                    $location_to = $locations->id;
                                }
                                $conversion_quantity_unit = $info['conversion_quantity_unit'];
                                $unit_id = $purchaseProductItem['unit_id'];
                                $exchange_unit = 1;
                                $exchange_stock = $info['conversion_quantity_unit'];
                                $exchange_payment = 1;
                                if ($quantityCheck < 0) {
                                    if ($info['unit_id'] == $unit_id) {
                                        $quantity_unit = $quantitynet;
                                        $quantity_stock = roundNumberFormat(
                                            $quantity_unit * $conversion_quantity_unit,
                                            4
                                        );
                                        $quantity_payment = $quantity_unit;
                                    } else {
                                        $quantity_stock = $quantitynet;
                                        $quantity_unit = roundNumberFormat(
                                            $quantity_stock / $conversion_quantity_unit,
                                            4
                                        );
                                        $quantity_payment = $quantity_stock;
                                    }
                                    $amountTranfer = $get_item->price * $quantitynet;
                                    $tranferItems[] = [
                                        'order_id_item' => $vv['order_item_id'],
                                        'id_items' => $vv['item_id'],
                                        'quantity' => $quantitynet,
                                        'quantity_net' => $quantitynet,
                                        'type' => $itemType,
                                        'note' => '',
                                        'warehouses_to' => WAREHOUSES_HOLD,
                                        'warehouses_id' => $purchaseProduct['warehouse_id'],
                                        'localtion_id' => $purchaseProductItem['location_id'],
                                        'localtion_to' => $location_to,
                                        'price' => $get_item->price,
                                        'amount' => $amountTranfer,
                                        'quantity_unit' => $quantity_unit,
                                        'quantity_stock' => $quantity_stock,
                                        'quantity_payment' => $quantitynet,
                                        'exchange_unit' => $exchange_unit,
                                        'exchange_stock' => $exchange_stock,
                                        'exchange_payment' => $exchange_payment,
                                        'date_sx' => null,
                                        'date_sd' => null,
                                        'date_use' => null,
                                        'lot_code' => null,
                                        'unit_id' => $unit_id,
                                        'tranfer_business_item_id' => $vv['id'],
                                        'tranfer_business_id' => $vv['tranfer_business_id'],
                                    ];
                                    $transfer_to_tranfer_business[] = [
                                        'tranfer_business_id' => $vv['tranfer_business_id'],
                                        'order_id' => $vv['order_id'],
                                        'pod_id' => $vv['pod_id'],
                                    ];
                                } else {
                                    if ($info['unit_id'] == $unit_id) {
                                        $quantity_unit = $vv['quantity'];
                                        $quantity_stock = roundNumberFormat(
                                            $quantity_unit * $conversion_quantity_unit,
                                            4
                                        );
                                        $quantity_payment = $quantity_unit;
                                    } else {
                                        $quantity_stock = $vv['quantity'];
                                        $quantity_unit = roundNumberFormat(
                                            $quantity_stock / $conversion_quantity_unit,
                                            4
                                        );
                                        $quantity_payment = $quantity_stock;
                                    }
                                    $amountTranfer = $get_item->price * $vv['quantity'];
                                    $tranferItems[] = [
                                        'order_id_item' => $vv['order_item_id'],
                                        'id_items' => $vv['item_id'],
                                        'quantity' => $vv['quantity'],
                                        'quantity_net' => $vv['quantity'],
                                        'type' => $itemType,
                                        'note' => '',
                                        'warehouses_to' => WAREHOUSES_HOLD,
                                        'warehouses_id' => $purchaseProduct['warehouse_id'],
                                        'localtion_id' => $purchaseProductItem['location_id'],
                                        'localtion_to' => $location_to,
                                        'price' => $get_item->price,
                                        'amount' => $amountTranfer,
                                        'quantity_unit' => $quantity_unit,
                                        'quantity_stock' => $quantity_stock,
                                        'quantity_payment' => $vv['quantity'],
                                        'exchange_unit' => $exchange_unit,
                                        'exchange_stock' => $exchange_stock,
                                        'exchange_payment' => $exchange_payment,
                                        'date_sx' => null,
                                        'date_sd' => null,
                                        'date_use' => null,
                                        'lot_code' => null,
                                        'unit_id' => $unit_id,
                                        'tranfer_business_item_id' => $vv['id'],
                                        'tranfer_business_id' => $vv['tranfer_business_id'],
                                    ];
                                    $total += $amountTranfer;
                                    $transfer_to_tranfer_business[] = [
                                        'tranfer_business_id' => $vv['tranfer_business_id'],
                                        'order_id' => $vv['order_id'],
                                        'pod_id' => $vv['pod_id'],
                                    ];
                                }
                            }
                        }
                        $statusTransfer = 2;
                        $staffIdTransfer = get_staff_user_id();
                        $dateTransfer = date('Y-m-d H:i:s');
                        $history_status = '|' . $staffIdTransfer . ',' . $dateTransfer;

                        if (!empty($tranferItems)) {

                            $transfer = array(
                                'code' => sprintf('%06d', ch_getMaxID('id', 'tbltransfer_warehouse') + 1),
                                'prefix' => get_option('prefix_transfer'),
                                'note' => '',
                                'warehouse_id' => 0,
                                'warehouse_to' => 0,
                                'date' => date('Y-m-d'),
                                'staff_id' => get_staff_user_id(),
                                'date_create' => date('Y-m-d H:i:s'),
                                'status' => 2,
                                'history_status' => '|' . get_staff_user_id() . ',' . date('Y-m-d H:i:s'),
                                'total' => $total,
                                'purchase_product_id' => $id,
                            );
                            $this->db->insert('tbltransfer_warehouse', $transfer);
                            $transfer_id = $this->db->insert_id();
                            if ($transfer_id) {
                                foreach ($tranferItems as $k => $v) {
                                    $v['id_transfer'] = $transfer_id;
                                    $this->db->insert('tbltransfer_warehouse_detail', $v);
                                    $ins = $this->db->insert_id();
                                    if ($ins) {
                                        $tranfer_business_item = get_table_where('tbl_tranfer_business_item', array(
                                            'id' => $v['tranfer_business_item_id'],
                                        ), '', 'row');
                                        $quantity_hold = $tranfer_business_item->quantity_hold + $v['quantity_net'];
                                        $this->db->update(
                                            'tbl_tranfer_business_item',
                                            array('quantity_hold' => $quantity_hold),
                                            array('id' => $tranfer_business_item->id)
                                        );
                                    }
                                }
                                if (!empty($transfer_to_tranfer_business)) {
                                    foreach ($transfer_to_tranfer_business as $k => $v) {
                                        $v['tranfer_id'] = $transfer_id;
                                        $this->db->insert('tbl_tranfer_to_tranfer_business', $v);
                                    }
                                }

                                if (!test_quantity_tranfer($transfer_id)) {
                                } else {
                                    $dataTransfer = array(
                                        'warehouseman_id' => $staffIdTransfer,
                                        'warehouseman_date' => $dateTransfer,
                                    );
                                    $this->db->update(
                                        'tbltransfer_warehouse',
                                        $dataTransfer,
                                        array('id' => $transfer_id)
                                    );
                                    $this->transfer_model->increaseTranfersWarehouse($transfer_id);
                                }
                            }
                        }
                    }
                }
                //end
            }

            //activity log
            if (!empty($purchaseProduct['productions_orders_details_id'])) {
                $pod = $this->manufactures_model->rowProductionsOrdersDetais($purchaseProduct['productions_orders_details_id']);
                $content = lang('tnh_his_warehouse_purchase_product_pod');
                $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                $content = str_replace('{$2}', $pod['reference_no'], $content);
            } else {
                $content = lang('tnh_his_warehouse_purchase_product');
                $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
            }
            insertActivityLog([
                'type_parent_obj' => 'purchase_products',
                'table_obj' => 'tbl_purchase_products',
                'id_obj' => $id,
                'name_obj' => $purchaseProduct['reference_no'],
                'content' => $content,
                'actions' => 'warehouse'
            ]);
            //end activity log

            calCostingSuggProduct($id);
            echo json_encode(array(
                'alert_type' => 'success',
                'message' => _l('ch_export_confirm')
            ));
            die;
        } else {
            if (empty($ktr->warehouseman_id)) {
                echo json_encode(array(
                    'alert_type' => 'warning',
                    'message' => _l('ch_exsit_cancel_confirm_warehouse')
                ));
                die;
            } else {
                if ($ktr->grand_total > 0) {
                    echo json_encode(array(
                        'alert_type' => 'warning',
                        'message' => _l('ch_tingiathanh'),
                    ));
                    die;
                }
                $test_quantity = get_table_where('tblwarehouse_product', array('import_id' => $id, 'quantity_export >' => 0, 'type_export ' => 18), '', 'row');
                if (!empty($test_quantity)) {
                    echo json_encode(array(
                        'alert_type' => 'warning',
                        'message' => _l('ch_quantity_nd'),
                    ));
                    die;
                }
                $_data = array(
                    'status' => 'un_approved',
                    'warehouseman_id' => 0,
                    'date_warehouseman' => NULL
                );
                $success    = $this->db->update('tbl_purchase_products', $_data, array('id' => $id));
                if ($success) {
                    // $exporting = get_table_where('tbl_purchase_products',array('id'=>$id),'','row');
                    // $items = get_table_where('tbl_purchase_product_items',array('suggest_exporting_id'=>$id));
                    $this->stock_model->increaseadWarehouse_purchase_products($id);

                    //activity log
                    if (!empty($purchaseProduct['productions_orders_details_id'])) {
                        $pod = $this->manufactures_model->rowProductionsOrdersDetais($purchaseProduct['productions_orders_details_id']);
                        $content = lang('tnh_his_un_warehouse_purchase_product_pod');
                        $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                        $content = str_replace('{$2}', $pod['reference_no'], $content);
                    } else {
                        $content = lang('tnh_his_un_warehouse_purchase_product');
                        $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                    }
                    insertActivityLog([
                        'type_parent_obj' => 'purchase_products',
                        'table_obj' => 'tbl_purchase_products',
                        'id_obj' => $id,
                        'name_obj' => $purchaseProduct['reference_no'],
                        'content' => $content,
                        'actions' => 'un_warehouse'
                    ]);
                    //end activity log

                    calCostingSuggProduct($id);

                    echo json_encode(array(
                        'alert_type' => 'success',
                        'message' => _l('Hủy duyệt kho thành công!')
                    ));
                    die;
                }
            }
        }
    }

    public function purchase_internal()
    {
        $data['title'] = lang('purchase_internal');
        $data['tnh'] = $this->tnh;
        $data['branch'] = getListBranch();
        $this->load->view('admin/stock/purchase_internal', $data);
    }

    public function add_purchase_internal()
    {
        //        set_alert('danger', lang('Chức nặng này hiện đăng bảo trì'));
        //        redirect($_SERVER["HTTP_REFERER"]);
        //        die;
        if ($this->input->post('add')) {
            $data = [];
            $this->form_validation->set_rules('reference_no', lang("tnh_reference_purchase_internal"), 'required|is_unique[tbl_purchase_internal.reference_no]');
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('enter_name', lang("tnh_enter_name"), 'required');
            $this->form_validation->set_rules('warehouses', lang("tnh_warehouses"), 'required');
            $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
            if ($this->form_validation->run() == true) {
                $reference_no = $this->input->post('reference_no');
                $date = to_sql_date($this->input->post('date'), true);
                $productions_orders_detail_id = $this->input->post('productions_orders_detail_id') ? $this->input->post('productions_orders_detail_id') : 0;
                $note = $this->input->post('note');
                $enter_name = $this->input->post('enter_name');
                $warehouses = $this->input->post('warehouses');
                $branch_id = $this->input->post('branch_id');

                $items = $this->input->post('items_id');
                $total_quantity = 0;
                $grand_total = 0;
                $count_items = 0;

                if (!empty($items)) {
                    foreach ($items as $key => $value) {
                        if (empty($value)) continue;
                        $location = $this->input->post('locations')[$key];
                        $quantity = number_unformat($this->input->post('quantity')[$key]);
                        $arr_item = explode('__', $value);
                        $type_item = $arr_item[0];
                        $item_id = $arr_item[1];
                        if ($type_item == "semi_products_outside") {
                            $info_item = $this->products_model->rowProduct($item_id);
                        } else if ($type_item == "materials") {
                            $info_item = $this->items_model->rowMaterial($item_id);
                        } else if ($type_item == "tools_supplies") {
                            $info_item = $this->tools_supplies_model->rowToolsSupplies($item_id);
                        }

                        if (empty($info_item)) continue;
                        if (empty($location)) {
                            $errors = lang('tnh_location_warehouse_required');
                            break;
                        }

                        $price = number_unformat($this->input->post('price')[$key]);
                        $amount = $quantity * $price;

                        $note_item = $this->input->post('note_item')[$key];

                        $its[] = [
                            'type_item' => $type_item,
                            'item_id' => $item_id,
                            'item_code' => $info_item['code'],
                            'item_name' => $info_item['name'],
                            'unit_id' => $info_item['unit_id'],
                            'location_id' => $location,
                            'quantity' => $quantity,
                            'price' => $price,
                            'amount' => $amount,
                            'note_item' => $note_item,
                        ];
                        $total_quantity += $quantity;
                        $grand_total += $amount;
                    }
                }

                if (!empty($errors)) {
                    $data['result'] = 0;
                    $data['message'] = $errors;
                    echo json_encode($data);
                    die;
                }

                if (empty($its)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_no_items');
                    echo json_encode($data);
                    die;
                }
                $count_items = count($its);
                $fields = [
                    'pod_id' => $productions_orders_detail_id,
                    'reference_no' => $reference_no,
                    'date' => $date,
                    'enter_name' => $enter_name,
                    'note' => $note,
                    'status' => 'un_approved',
                    'total_quantity' => $total_quantity,
                    'grand_total' => $grand_total,
                    'count_items' => $count_items,
                    'warehouse_id' => $warehouses,
                    'created_by' => get_staff_user_id(),
                    'date_created' => date('Y-m-d H:i:s'),
                    'status' => 'un_approved',
                    'type' => 1,
                    'branch_id' => $branch_id,
                ];

                $id = $this->stock_model->insertPurchaseInternal($fields);
                if ($id) {
                    if (getReference('purchase_internal') == $reference_no) {
                        updateReference('purchase_internal');
                    }

                    foreach ($its as $key => $value) {
                        $its[$key]['purchase_internal_id'] = $id;
                    }
                    $this->stock_model->insertBatchPurchaseInternalItems($its);

                    @pusherTNHNotfication();
                    if (!empty($productions_orders_detail_id)) {
                        $pod = $this->manufactures_model->rowProductionsOrdersDetais($productions_orders_detail_id);
                        $content = lang('tnh_his_add_purchase_internal_pod');
                        $content = str_replace('{$1}', $reference_no, $content);
                        $content = str_replace('{$2}', $pod['reference_no'], $content);
                    } else {
                        $content = lang('tnh_his_add_purchase_internal');
                        $content = str_replace('{$1}', $reference_no, $content);
                    }

                    insertActivityLog([
                        'type_parent_obj' => 'purchase_internal',
                        'table_obj' => 'tbl_purchase_internal',
                        'id_obj' => $id,
                        'name_obj' => $reference_no,
                        'content' => $content,
                        'actions' => 'add'
                    ]);

                    $data['result'] = 1;
                    $data['message'] = lang('success');
                    set_alert('message', lang('success'));
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
        } else {
            $data['reference_no'] = getReference('purchase_internal');
            $data['warehouses'] = $this->stock_model->getWarehouses();
            $data['breadcrumb'] = [array('link' => base_url('admin/stock/purchase_internal'), 'page' => lang('purchase_internal')), array('link' => '#', 'page' => lang('tnh_add_purchase_internal'))];
            $data['title'] = lang('tnh_add_purchase_internal');
            $data['tnh'] = $this->tnh;
            $this->load->view('admin/stock/add_purchase_internal', $data);
        }
    }

    function refereshReferencePurchaseInternal()
    {
        $data = [];
        if ($this->input->get('referesh')) {
            $reference_no = getReference('purchase_internal');
            if ($this->stock_model->checkExistPurchaseInternal($reference_no)) {
                $ct = countReferenceMinus('purchase_internal');
                $this->db->select("MAX(right(tbl_purchase_internal.reference_no, char_length(tbl_purchase_internal.reference_no) - $ct) + 0) as reference_no", false);
                $this->db->from('tbl_purchase_internal');
                $rs = $this->db->get()->row_array();

                $max = $rs['reference_no'];
                $max++;
                // $max = subReference($max);
                updateReferenceNormal('purchase_internal', $max);
                $reference_no = getReference('purchase_internal');
            }
            $data['reference_no'] = $reference_no;
            $data['message'] = lang('tnh_referesh_success');
        }
        echo json_encode($data);
    }

    public function searchItemsForPurchaseInternal($id = false)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');
        $params = $this->input->get('params');
        $productions_orders_detail_id = $params['productions_orders_detail_id'];

        if (!empty($productions_orders_detail_id)) {
            $material = $this->stock_model->searchMaterialsPODExportWarehouse($productions_orders_detail_id, $term, $limit, 'materials');
            $semi_product_outside = $this->stock_model->searchMaterialsPODExportWarehouse($productions_orders_detail_id, $term, $limit, 'semi_products_outside');
            $tools_supplies = $this->stock_model->searchMaterialsPODExportWarehouse($productions_orders_detail_id, $term, $limit, 'tools_supplies');
        } else {
            $material = $this->stock_model->searchMaterialsAndUnit($term, $limit);
            $semi_product_outside = $this->stock_model->searchSemiProductsOutsideAndUnit($term, $limit);
            $tools_supplies = $this->stock_model->searchToolsSuppliesAndUnit($term, $limit);
        }

        $results = [];
        if (!empty($material)) {
            $results[] = ['text' => lang('tnh_materials'), 'children' => $material];
        }
        if (!empty($semi_product_outside)) {
            $results[] = ['text' => lang('semi_products_outside'), 'children' => $semi_product_outside];
        }
        if (!empty($tools_supplies)) {
            $results[] = ['text' => lang('tnh_tools_supplies'), 'children' => $tools_supplies];
        }
        $data['results'] = $results;

        if ($id) {
            // $data['row'] = ['id' => $product['id'], 'text' => $product['code']];
        }
        echo json_encode($data);
    }

    public function getPurchaseInternal()
    {
        $branch_search = $this->input->post('branch_search');
        $arrBranch = get_branch_staff();
        $this->datatables
            ->select("
                0 as number_records,
                tbl_purchase_internal.id as id,
                tbl_purchase_internal.date as date,
                CONCAT(tbl_purchase_internal.reference_no,'__',COALESCE(tblbranch.name,'')) as reference_no,
                tbl_productions_orders_details.reference_no as reference_production_detail,
                tbl_purchase_internal.enter_name as enter_name,
                tblwarehouse.name as warehouse_name,
                tbl_purchase_internal.total_quantity as total_quantity,
                tbl_purchase_internal.note as note,
                CONCAT(tblstaff.firstname, ' ', tblstaff.lastname,'') as created_by,
                tbl_purchase_internal.status as status,
                tbl_purchase_internal.warehouseman_id as warehouseman_id
            ", false)
            ->from('tbl_purchase_internal')
            ->join('tbl_productions_orders_details', 'tbl_productions_orders_details.id = tbl_purchase_internal.pod_id', 'left')
            ->join('tblstaff', 'tblstaff.staffid = tbl_purchase_internal.created_by', 'left')
            ->join('tblbranch', 'tblbranch.id = tbl_purchase_internal.branch_id', 'left')
            ->join('tblwarehouse', 'tblwarehouse.id = tbl_purchase_internal.warehouse_id', 'left');

        // if (!$this->perViewExportingProducion) {
        //     $this->datatables->where('tbl_suggest_exporting.created_by', get_staff_user_id());
        // }

        if (!empty($branch_search)) {
            $this->datatables->where('tbl_purchase_internal.branch_id', $branch_search);
        }

        if (!$this->isAdmin) {
            if (!empty($arrBranch)) {
                $coverStrBranch = implode(",", $arrBranch);
                $this->datatables->where('tbl_purchase_internal.branch_id IN (' . $coverStrBranch . ')');
            } else {
                $this->datatables->where('tbl_purchase_internal.id', 0);
            }
        }

        $view = '<a class="tnh-modal" title="' . lang('view') . '" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url('admin/stock/view_purchase_internal/$1') . '"><i class="fa fa-file-text-o width-icon-actions"></i> ' . lang('view') . '</a>';

        $edit = '<a class="tnh-edit" title="' . lang('edit') . '" href="' . base_url('admin/stock/edit_purchase_internal/$1') . '"><i class="fa fa-edit width-icon-actions"></i> ' . lang('edit') . '</a>';

        $delete = '<a type="button" class="po tnh-delete" title="' . lang('delete') . '" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
            <button href=\'' . base_url('admin/stock/delete_purchase_internal/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
            <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
        "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>';

        $print = '<a href="' . base_url('admin/stock/print_purchase_internal/$1') . '" target="_blank"><i class="fa fa-print"></i> ' . lang('print') . '</a>';

        $actions = '
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
            ' . lang('actions') . '
            <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                <li>' . $view . '</li>
                <li>' . $print . '</li>
                <li class="not-outside">' . $delete . '</li>
            </ul>
        </div>';

        $this->datatables->add_column('actions', $actions, 'id');
        $iDisplayStart = $this->input->post('iDisplayStart');
        $data = json_decode($this->datatables->generate());
        foreach ($data->aaData as $key => $value) {
            $data->aaData[$key][0] = ++$iDisplayStart;
        }
        echo json_encode($data);
    }

    public function view_purchase_internal($id)
    {
        // if (!$this->perViewPurchaseProducts && !$this->perViewOwnPurchaseProducts) {
        //     accessDenied($js = true);
        // }
        $purchaseInternal = $this->stock_model->rowPurchaseInternal($id);
        // if (!$this->perViewPurchaseProducts) {
        //     checkMyData($purchaseInternal['created_by'], true);
        // }
        $items = $this->stock_model->getPurchaseInternalItems($id);
        $warehouse = $this->stock_model->rowWarehouse($purchaseInternal['warehouse_id']);
        $pod = $this->manufactures_model->rowProductionsOrdersDetais($purchaseInternal['pod_id']);

        $data['id'] = $id;
        $data['created_by'] = get_staff_full_name($purchaseInternal['created_by']);
        if ($purchaseInternal['updated_by']) {
            $data['updated_by'] = get_staff_full_name($purchaseInternal['updated_by']);
        }
        $data['pod'] = $pod;
        $data['purchaseInternal'] = $purchaseInternal;
        $data['items'] = $items;
        $data['warehouse'] = $warehouse;
        $this->load->view('admin/stock/view_purchase_internal', $data);
    }

    public function delete_purchase_internal($id)
    {
        $data = [];
        //check permission
        // if (!$this->perDeletePurchaseProducts) {
        //     $data['result'] = 0;
        //     $data['message'] = lang('access_denied');
        //     echo json_encode($data); die;
        // }
        //end
        if ($id) {
            $purchaseInternal = $this->stock_model->rowPurchaseInternal($id);
            if ($purchaseInternal['status'] != 'un_approved') {
                $data['result'] = 0;
                $data['message'] = lang('browsed_cannot_be_deleted');
                echo json_encode($data);
                die;
            }
            if ($this->stock_model->deletePurchaseInternal($id)) {
                $this->stock_model->deletePurchaseInternalItems($id);

                if (!empty($purchaseInternal['pod_id'])) {
                    $pod = $this->manufactures_model->rowProductionsOrdersDetais($purchaseInternal['pod_id']);
                    $content = lang('tnh_his_delete_purchase_internal_pod');
                    $content = str_replace('{$1}', $purchaseInternal['reference_no'], $content);
                    $content = str_replace('{$2}', $pod['reference_no'], $content);
                } else {
                    $content = lang('tnh_his_delete_purchase_internal');
                    $content = str_replace('{$1}', $purchaseInternal['reference_no'], $content);
                }

                insertActivityLog([
                    'type_parent_obj' => 'purchase_internal',
                    'table_obj' => 'tbl_purchase_internal',
                    'id_obj' => $id,
                    'name_obj' => $purchaseInternal['reference_no'],
                    'content' => $content,
                    'actions' => 'delete'
                ]);

                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function edit_purchase_internal($id)
    {
        set_alert('danger', lang('Chức nặng này hiện đăng bảo trì'));
        redirect($_SERVER["HTTP_REFERER"]);
        die;

        $purchaseInternal = $this->stock_model->rowPurchaseInternal($id);
        if ($purchaseInternal['status'] == "approved") {
            set_alert('danger', lang('browsed_cannot_be_edited'));
            redirect($_SERVER["HTTP_REFERER"]);
            die;
        }
        $purchaseInternalItems = $this->stock_model->getPurchaseInternalItems($id);

        if ($this->input->post('edit')) {
            $data = [];
            if ($purchaseInternal['reference_no'] != $this->input->post('reference_no')) {
                $this->form_validation->set_rules('reference_no', lang("tnh_reference_purchase_internal"), 'required|is_unique[tbl_purchase_internal.reference_no]');
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('enter_name', lang("tnh_enter_name"), 'required');
            $this->form_validation->set_rules('warehouses', lang("tnh_warehouses"), 'required');
            $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
            if ($this->form_validation->run() == true) {
                $reference_no = $this->input->post('reference_no');
                $date = to_sql_date($this->input->post('date'), true);
                $productions_orders_detail_id = $this->input->post('productions_orders_detail_id') ? $this->input->post('productions_orders_detail_id') : 0;
                $note = $this->input->post('note');
                $enter_name = $this->input->post('enter_name');
                $warehouses = $this->input->post('warehouses');
                $branch_id = $this->input->post('branch_id');

                $items = $this->input->post('items_id');
                $total_quantity = 0;
                $grand_total = 0;
                $count_items = 0;

                if (!empty($items)) {
                    foreach ($items as $key => $value) {
                        if (empty($value)) continue;
                        $location = $this->input->post('locations')[$key];
                        $quantity = number_unformat($this->input->post('quantity')[$key]);
                        $arr_item = explode('__', $value);
                        $type_item = $arr_item[0];
                        $item_id = $arr_item[1];
                        if ($type_item == "semi_products_outside") {
                            $info_item = $this->products_model->rowProduct($item_id);
                        } else if ($type_item == "materials") {
                            $info_item = $this->items_model->rowMaterial($item_id);
                        } else if ($type_item == "tools_supplies") {
                            $info_item = $this->tools_supplies_model->rowToolsSupplies($item_id);
                        }

                        if (empty($info_item)) continue;
                        if (empty($location)) {
                            $errors = lang('tnh_location_warehouse_required');
                            break;
                        }

                        $price = number_unformat($this->input->post('price')[$key]);
                        $amount = $quantity * $price;

                        $note_item = $this->input->post('note_item')[$key];

                        $its[] = [
                            'purchase_internal_id' => $id,
                            'type_item' => $type_item,
                            'item_id' => $item_id,
                            'item_code' => $info_item['code'],
                            'item_name' => $info_item['name'],
                            'unit_id' => $info_item['unit_id'],
                            'location_id' => $location,
                            'quantity' => $quantity,
                            'price' => $price,
                            'amount' => $amount,
                            'note_item' => $note_item,
                        ];
                        $total_quantity += $quantity;
                        $grand_total += $amount;
                    }
                }

                if (!empty($errors)) {
                    $data['result'] = 0;
                    $data['message'] = $errors;
                    echo json_encode($data);
                    die;
                }

                if (empty($its)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_no_items');
                    echo json_encode($data);
                    die;
                }
                $count_items = count($its);
                $fields = [
                    // 'pod_id' => $productions_orders_detail_id,
                    'reference_no' => $reference_no,
                    'date' => $date,
                    'enter_name' => $enter_name,
                    'note' => $note,
                    'status' => 'un_approved',
                    'total_quantity' => $total_quantity,
                    'grand_total' => $grand_total,
                    'count_items' => $count_items,
                    'warehouse_id' => $warehouses,
                    'branch_id' => $branch_id,
                    'updated_by' => get_staff_user_id(),
                    'date_updated' => date('Y-m-d H:i:s'),
                    // 'status' => 'un_approved',
                ];

                $up = $this->stock_model->updatePurchaseInternal($id, $fields);
                if ($up) {
                    $this->stock_model->deletePurchaseInternalItems($id);
                    $this->stock_model->insertBatchPurchaseInternalItems($its);

                    if (!empty($productions_orders_detail_id)) {
                        $pod = $this->manufactures_model->rowProductionsOrdersDetais($productions_orders_detail_id);
                        $content = lang('tnh_his_edit_purchase_internal_pod');
                        $content = str_replace('{$1}', $reference_no, $content);
                        $content = str_replace('{$2}', $pod['reference_no'], $content);
                    } else {
                        $content = lang('tnh_his_edit_purchase_internal');
                        $content = str_replace('{$1}', $reference_no, $content);
                    }

                    insertActivityLog([
                        'type_parent_obj' => 'purchase_internal',
                        'table_obj' => 'tbl_purchase_internal',
                        'id_obj' => $id,
                        'name_obj' => $reference_no,
                        'content' => $content,
                        'actions' => 'add'
                    ]);

                    $data['result'] = 1;
                    $data['message'] = lang('success');
                    set_alert('message', lang('success'));
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
        } else {
            $pod = $this->manufactures_model->rowProductionsOrdersDetais($purchaseInternal['pod_id']);
            $data['id'] = $id;
            $data['pod'] = $pod;
            $data['purchaseInternal'] = $purchaseInternal;
            $data['purchaseInternalItems'] = $purchaseInternalItems;
            $data['warehouses'] = $this->stock_model->getWarehouses();
            $data['locations'] = recursiveLocationWarehouses($purchaseInternal['warehouse_id']);
            $data['breadcrumb'] = [array('link' => base_url('admin/stock/purchase_internal'), 'page' => lang('purchase_internal')), array('link' => '#', 'page' => lang('tnh_edit_purchase_internal'))];
            $data['title'] = lang('tnh_edit_purchase_internal');
            $data['tnh'] = $this->tnh;
            $this->load->view('admin/stock/edit_purchase_internal', $data);
        }
    }

    public function agreePurchaseInternal()
    {
        $data = [];
        // if (!$this->perApproveExportingProducion) {
        //     $data['result'] = 0;
        //     $data['message'] = lang('access_denied');
        //     echo json_encode($data); die;
        // }
        if ($this->input->get()) {
            $purchase_internal_id = $this->input->get('purchase_internal_id');
            $status = $this->input->get('status');
            $purchaseInternal = $this->stock_model->rowPurchaseInternal($purchase_internal_id);
            // if ($suggest_exporting['warehouseman_id'] > 0) {
            //     $data['result'] = 0;
            //     $data['message'] = lang('tnh_agree_warehoused');
            //     echo json_encode($data); die;
            // }
            $date = date('Y-m-d H:i:s');
            $user_id = get_staff_user_id();
            if ($purchaseInternal['status'] == $status) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_please_referesh_table');
                echo json_encode($data);
                die;
            }
            if ($purchaseInternal['warehouseman_id'] != 0) {
                $data['result'] = 0;
                $data['message'] = lang('alert_comfi_ch');
                echo json_encode($data);
                die;
            }
            $up = $this->stock_model->updatePurchaseInternal($purchase_internal_id, [
                'status' => $status,
                'date_status' => $date,
                'user_status' => $user_id
            ]);

            if ($up) {

                @pusherTNHNotfication();
                if (!empty($purchaseInternal['pod_id'])) {
                    $pod = $this->manufactures_model->rowProductionsOrdersDetais($purchaseInternal['pod_id']);
                    $content = lang('tnh_his_agree_purchase_internal_pod');
                    $content = str_replace('{$1}', $purchaseInternal['reference_no'], $content);
                    $content = str_replace('{$2}', $pod['reference_no'], $content);
                } else {
                    $content = lang('tnh_his_agree_purchase_internal');
                    $content = str_replace('{$1}', $purchaseInternal['reference_no'], $content);
                }

                insertActivityLog([
                    'type_parent_obj' => 'purchase_internal',
                    'table_obj' => 'tbl_purchase_internal',
                    'id_obj' => $purchase_internal_id,
                    'name_obj' => $purchaseInternal['reference_no'],
                    'content' => $content,
                    'actions' => 'agree'
                ]);

                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        }
        echo json_encode($data);
    }

    public function print_purchase_internal($id)
    {
        ob_end_clean();
        $data = [];
        $purchaseInternal = $this->stock_model->rowPurchaseInternal($id);
        $items = $this->stock_model->getPurchaseInternalItems($id);
        $pod = $this->manufactures_model->rowProductionsOrdersDetais($purchaseInternal['pod_id']);
        $warehouse = $this->stock_model->rowWarehouse($purchaseInternal['warehouse_id']);

        $data['title'] = lang('purchase_internal');
        $data['type'] = 'P';
        $data['img'] = '';

        $bodyItems = '';
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $location = recursiveLocations($value['location_id']);

                $tdNumber = '<td class="text-center" style="width: 7%;">' . (++$key) . '</td>';
                $tdName = '<td style="width: 38%;">
                        <span>' . $value['item_code'] . '</span>
                        <br>
                        <span>' . $value['item_name'] . '</span>
                </td>';
                $tdUnit = '<td class="text-center" style="width: 7%;">' . $value['unit_name'] . '</td>';
                $tdLocation = '<td class="text-center" style="width: 18%;">' . $location . '</td>';
                $tdQuantity = '<td class="text-center" style="width: 15%;">' . formatNumber($value['quantity']) . '</td>';
                $tdNoteItem = '<td class="text-center" style="width: 15%;">' . $value['note_item'] . '</td>';

                $bodyItems .= '<tr nobr="true">
                    ' . $tdNumber . '
                    ' . $tdName . '
                    ' . $tdUnit . '
                    ' . $tdLocation . '
                    ' . $tdQuantity . '
                    ' . $tdNoteItem . '
                </tr>';
            }
        }

        $day = date_format(date_create($purchaseInternal['date']), 'd');
        $month = date_format(date_create($purchaseInternal['date']), 'm');
        $year = date_format(date_create($purchaseInternal['date']), 'Y');
        $message = "";
        ob_start();
        stylePdf();

        echo '
            <h1 class="text-center uppercase">' . lang('purchase_internal') . '</h1>
            <span class="text-right">
                <span class="italic">' . _l('tnh_reference_purchase_internal') . ': ' . $purchaseInternal['reference_no'] . '</span><br>
                <span class="italic">' . _l('date') . ': ' . _d($purchaseInternal['date'], true) . '</span>
            </span>
            <p>
                <span>' . _l('tnh_reference_productions_orders_details') . ': <span>' . (!empty($pod) ? $pod['reference_no'] : '') . '</span></span><br>
                <span>' . _l('tnh_enter_name') . ': <span>' . $purchaseInternal['enter_name'] . '</span></span><br>
                <span>' . _l('tnh_warehouses') . ': <span>' . $warehouse['name'] . '</span></span><br>
                <span>' . _l('tnh_note') . ': <span>' . $purchaseInternal['note'] . '</span></span><br>
            </p>
            <table class="table-items" cellspacing="0" cellpadding="5" border="1">
                <thead>
                    <tr>
                        <th class="bold text-center" style="width: 7%;">' . _l('tnh_numbers') . '</th>
                        <th class="bold text-center" style="width: 38%;">' . _l('materials') . '</th>
                        <th class="bold text-center" style="width: 7%;">' . _l('tnh_dvt') . '</th>
                        <th class="bold text-center" style="width: 18%;">' . _l('tnh_location_warehouse') . '</th>
                        <th class="bold text-center" style="width: 15%;">' . _l('quantity') . '</th>
                        <th class="bold text-center" style="width: 15%;">' . _l('note') . '</th>
                    </tr>
                </thead>
                <tbody>
                    ' . $bodyItems . '
                </tbody>
                <tfoot>
                    <tr class="bold">
                        <th class="text-right" colspan="2">' . _l('tnh_total') . '</th>
                        <th></th>
                        <th></th>
                        <th class="text-center">' . formatNumber($purchaseInternal['total_quantity']) . '</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
            <p class="text-right"><span>Ngày ' . $day . ' tháng ' . $month . ' năm ' . $year . '</span></p>
            <table style="width: 100%">
                <tr>
                    <td class="text-center">
                        <span class="bold">' . lang('tnh_deliver') . '</span><br>
                        <span>' . lang('tnh_sign_write_full_name') . '</span>
                    </td>
                    <td class="text-center">
                        <span class="bold">' . lang('tnh_receiver') . '</span><br>
                        <span>' . lang('tnh_sign_write_full_name') . '</span>
                    </td>
                    <td class="text-center">
                        <span class="bold">' . lang('tnh_stocker') . '</span><br>
                        <span>' . lang('tnh_sign_write_full_name') . '</span>
                    </td>
                </tr>
            </table>
        ';

        $content = ob_get_contents();
        ob_end_clean();

        $data['content'] = $content;
        $pdf = print_pdf_tnh($data);
        $type = 'I';
        $pdf->Output(slug_it('123') . '.pdf', $type);
    }

    public function print_exporting_production($id)
    {
        ob_end_clean();
        $data = [];
        $suggest_exporting = $this->manufactures_model->rowSuggestExporting($id);
        $pod = $this->manufactures_model->rowProductionsOrdersDetais($suggest_exporting['productions_orders_details_id']);
        $warehouse = $this->stock_model->rowWarehouse($suggest_exporting['warehouse_id']);
        $items = $this->manufactures_model->getSuggestExportingItemsView($id);
        $number_print = GetNumberPrint($id, '2');
        $text_number = '<div class="text-left" style="font-size: 9px">
        <span class="bold">' . _l('Lần in') . ':</span> <span>' . $number_print['number'] . '</span><br>
        <span class="bold">' . _l('Giờ in') . ':</span> <span>' . _dt($number_print['date']) . '</span></div>';
        $data['number_print'] = $text_number;


        $data['title'] = lang('exporting_producion');
        $data['type'] = 'P';
        $data['img'] = '';
        $data['type_print'] = '';
        $bodyItems = '';
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $location = recursiveLocations($value['location_id']);
                $warehouse = $this->stock_model->rowWarehouse($value['warehouse_item_id']);

                $tdNumber = '<td class="text-center" style="width: 6%;">' . (++$key) . '</td>';
                $tdName = '<td style="width: 30%;">
                        <span>' . $value['item_code'] . '</span>
                        <br>
                        <span>' . $value['item_name'] . '</span>
                </td>';
                $tdUnit = '<td class="text-center" style="width: 6%;">' . $value['unit_name'] . '</td>';
                $tdLocation = '<td style="width: 12%;" class="text-center">' . $warehouse['name'] . '->' . $location . '</td>';
                $tdQuantity = '<td class="text-center" style="width: 10%;">' . formatNumber($value['quantity_export']) . '</td>';
                $tdValueExchange = '<td class="text-center" style="width: 18%;">' . formatNumber($value['number_exchange']) . '</td>';
                $tdQuantityExchange = '<td class="text-center" style="width: 18%;">' . formatNumber($value['quantity_exchange']) . '</td>';

                $bodyItems .= '<tr nobr="true">
                    ' . $tdNumber . '
                    ' . $tdName . '
                    ' . $tdUnit . '
                    ' . $tdLocation . '
                    ' . $tdQuantity . '
                    ' . $tdValueExchange . '
                    ' . $tdQuantityExchange . '
                </tr>';
            }
        }

        $day = date_format(date_create($suggest_exporting['date_convert_stock']), 'd');
        $month = date_format(date_create($suggest_exporting['date_convert_stock']), 'm');
        $year = date_format(date_create($suggest_exporting['date_convert_stock']), 'Y');
        $message = "";
        ob_start();
        stylePdf();

        echo '
            <h1 class="text-center uppercase">' . lang('exporting_producion') . '</h1>
            <span class="text-right">
                <span class="italic">' . _l('tnh_reference_stock') . ': ' . $suggest_exporting['reference_stock'] . '</span><br>
                <span class="italic">' . _l('date') . ': ' . _d($suggest_exporting['date_convert_stock'], true) . '</span>
            </span>
            <p>
                <span>' . _l('tnh_reference_no_suggest') . ': <span>' . $suggest_exporting['reference_no'] . '</span></span><br>
                <span>' . _l('tnh_reference_productions_orders_details') . ': <span>' . (!empty($pod) ? $pod['reference_no'] : '') . '</span></span><br>
                <span>' . _l('tnh_export_name') . ': <span>' . $suggest_exporting['export_name'] . '</span></span><br>
                <span>' . _l('tnh_note') . ': <span>' . $suggest_exporting['note'] . '</span></span><br>
            </p>
            <table class="table-items" cellspacing="0" cellpadding="5" border="1">
                <thead>
                    <tr>
                        <th class="bold text-center" style="width: 6%;">' . _l('tnh_numbers') . '</th>
                        <th class="bold text-center" style="width: 30%;">' . _l('materials') . '</th>
                        <th class="bold text-center" style="width: 6%;">' . _l('tnh_dvt') . '</th>
                        <th class="bold text-center" style="width: 12%;">' . _l('tnh_location_warehouse') . '</th>
                        <th class="bold text-center" style="width: 10%;">' . _l('quantity') . '</th>
                        <th class="bold text-center" style="width: 18%;">' . _l('tnh_value_exchange') . '</th>
                        <th class="bold text-center" style="width: 18%;">' . _l('tnh_quantity_exchange') . '</th>
                    </tr>
                </thead>
                <tbody>
                    ' . $bodyItems . '
                </tbody>
                <tfoot>
                    <tr class="bold">
                        <th class="text-right" colspan="2">' . _l('tnh_total') . '</th>
                        <th></th>
                        <th></th>
                        <th class="text-center">' . formatNumber($suggest_exporting['total_quantity']) . '</th>
                        <th></th>
                        <th class="text-center">' . formatNumber($suggest_exporting['total_quantity_exchange']) . '</th>
                    </tr>
                </tfoot>
            </table>
            <p class="text-right"><span>Ngày ' . $day . ' tháng ' . $month . ' năm ' . $year . '</span></p>
            <table style="width: 100%">
                <tr>
                    <td class="text-center">
                        <span class="bold">' . _l('tnh_deliver') . '</span><br>
                        <span>' . _l('tnh_sign_write_full_name') . '</span>
                    </td>
                    <td class="text-center">
                        <span class="bold">' . _l('tnh_receiver') . '</span><br>
                        <span>' . _l('tnh_sign_write_full_name') . '</span>
                    </td>
                    <td class="text-center">
                        <span class="bold">' . _l('tnh_stocker') . '</span><br>
                        <span>' . _l('tnh_sign_write_full_name') . '</span>
                    </td>
                </tr>
            </table>
        ';

        $content = ob_get_contents();
        ob_end_clean();

        $data['content'] = $content;
        $pdf = print_pdf_tnh($data);
        $type = 'I';
        $pdf->Output(slug_it('123') . '.pdf', $type);
    }

    public function searchPurchaseProduct($id = false)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');
        $data['results'] = $this->stock_model->searchPurchaseProduct($term, $limit);
        if ($id) {
            // $data['row'] = ['id' => $product['id'], 'text' => $product['code']];
        }
        echo json_encode($data);
    }

    public function print_barcode($id)
    {
        ob_end_clean();
        $data = [];
        $data['title'] = lang('tnh_print_barcode');
        $data['type'] = 'P';

        $purchaseProduct = $this->stock_model->rowPurchaseProducts($id);
        $product = $this->products_model->rowProduct($id);
        $pod = $this->manufactures_model->rowProductionsOrdersDetais($purchaseProduct['productions_orders_details_id']);
        $date = date_format(date_create($purchaseProduct['date']), 'd/m/Y');
        $dateFiveYear = date("d/m/Y", strtotime(date("Y-m-d", strtotime($purchaseProduct['date'])) . " + 5 year"));

        $listTrHtml = '';
        $items = $this->stock_model->getPurchaseProductItems($id);
        foreach ($items as $key => $value) {
            $type_item = $value['type_item'];
            $items_id = $value['item_id'];
            if ($type_item == "products") {
                $info = $this->products_model->rowProduct($items_id);
                $unit = $this->unit_model->rowUnit($info['unit_id']);
            }
            $code = $info['code'];

            $barcode = file_get_contents(genBarcode($code, 'code128', 20, 0));
            $barcode = '<br><br><span> Lot No. ' . $pod['reference_no'] . '</span><br><img width="180" height="30" src="data:image/png;base64,' . base64_encode($barcode) . '"/><br><span> Mfg. Date: ' . $date . '</span><br><span> Exp. Date: ' . $dateFiveYear . '</span>';

            $items[$key]['barcode'] = $barcode;

            $trHtml = '<tr>
                <td class="text-center" style="">' . $barcode . '</td>
                <td class="text-center">' . $barcode . '</td>
                <td class="text-center">' . $barcode . '</td>
                <td class="text-center">' . $barcode . '</td>
                <td class="text-center">' . $barcode . '</td>
            </tr>';

            for ($i = 0; $i < 13; $i++) {
                $listTrHtml .= $trHtml;
            }
        }

        ob_start();
        stylePdf();
        echo '
            <table cellpadding="15">
                <tbody>
                    ' . $listTrHtml . '
                </tbody>
            </table
        ';

        $content = ob_get_contents();
        ob_end_clean();
        $data['content'] = $content;
        $data['items'] = $items;
        $data['showHeader'] = "hide";
        $data['size_brandcode'] = 6;
        $pdf = print_pdf_brandcode_tnh($data);
        $type = 'I';
        $pdf->Output(slug_it('123') . '.pdf', $type);
    }

    public function getExchangeProduct()
    {
        $data = [];
        $htmlExchange = '';
        if ($this->input->get()) {
            $item_id = $this->input->get('item_id');
            if (!empty($item_id)) {
                $arr = explode("__", $item_id);
                // $type = $arr[1];
                $id = $arr[0];
                $type_item = $arr[1];

                if ($type_item == "products" || $type_item == "semi_products" || $type_item == "semi_products_outside") {
                    $exchange = $this->site_model->getExchangeProducts($id);
                    // $data['exchange'] = $exchange;
                    if (!empty($exchange)) {
                        foreach ($exchange as $k => $val) {
                            $htmlExchange .= '<div class="list-exchange">
                                <input type="hidden" class="form-control number-exchange" value="' . $val['number_exchange'] . '">
                                <span>' . $val['unit_name'] . ': </span>
                                <span class="text-number-exchange">0</span>
                            </div>';
                        }
                    }
                }
            }
        }
        $data['htmlExchange'] = $htmlExchange;
        echo json_encode($data);
    }

    public function rowLocationWarehouse()
    {
        $data = [];
        if ($this->input->post()) {
            $item = $this->input->post('item_id');
            $warehouse_id = $this->input->post('warehouse_id');
            if (!empty($item)) {
                $item = explode('__', $item);
                $type_item = $item[0];
                $item_id = $item[1];

                $warehouses = $this->stock_model->getWarehouseItems($item_id, $type_item);
                $valSelected = '';
                foreach ($warehouses as $key => $value) {
                    $warehouses[$key]['location_name'] = $value['name_warehouse'] . '->' . recursiveLocations($value['localtion']);
                    if (empty($valSelected)) {
                        if ($value['warehouse_id'] == 8) {
                            $valSelected = $value['warehouse_id'] . '__' . $value['localtion'];
                        }
                    }
                }
                $data['warehouses'] = $warehouses;
                $data['valSelected'] = $valSelected;
            }
        }
        echo json_encode($data);
    }

    public function rowLocationWarehouseNew()
    {
        $data = [];
        $results = [];
        if ($this->input->post()) {
            $item = $this->input->post('item_id');
            $warehouse_id = $this->input->post('warehouse_id');
            if (!empty($item)) {
                $item = explode('__', $item);
                $type_item = $item[0];
                $item_id = $item[1];

                $item_type = $type_item;
                if ($item_type == "materials") {
                    $item_type = "nvl";
                } else if ($item_type == "tools_supplies") {
                    $item_type = "tools";
                } else {
                    $item_type = "product";
                }

                $this->db->select('tblwarehouse.id, tblwarehouse.name');
                $this->db->from('tblwarehouse');
                $this->db->where('tblwarehouse.id !=', WAREHOUSES_CAPACITY);
                if (!empty($item_id)) {
                    $this->db->where('(
                        EXISTS (
                            SELECT tblwarehouse_items.id
                            FROM tblwarehouse_items
                            WHERE tblwarehouse_items.type_items = "' . $item_type . '" AND tblwarehouse_items.id_items = "' . $item_id . '" AND tblwarehouse.id = tblwarehouse_items.warehouse_id
                        )
                    )');
                }
                $warehouses = $this->db->get()->result_array();

                if (!empty($warehouses)) {
                    foreach ($warehouses as $key => $value) {
                        $warehouse_id = $value['id'];

                        $tbQuantityWarehouses = '(
                            SELECT
                                tblwarehouse_items.localtion as localtion_id,
                                tblwarehouse_items.lot_code, 
                                tblwarehouse_items.date_sx, 
                                tblwarehouse_items.date_sd, 
                                tblwarehouse_items.date_use,
                                SUM(tblwarehouse_items.product_quantity_unit) as product_quantity
                            FROM tblwarehouse_items
                            WHERE tblwarehouse_items.type_items = "' . $item_type . '" AND tblwarehouse_items.id_items = "' . $item_id . '" AND tblwarehouse_items.warehouse_id = "' . $warehouse_id . '"
                            GROUP BY tblwarehouse_items.localtion, tblwarehouse_items.lot_code, tblwarehouse_items.date_sx, tblwarehouse_items.date_sd, tblwarehouse_items.date_use
                        ) tb_quantity_warehouses';

                        $this->db->select('
                            CONCAT(tbllocaltion_warehouses.warehouse, "__", tbllocaltion_warehouses.id, "__", coalesce(tb_quantity_warehouses.lot_code, "NULL"), "__", coalesce(tb_quantity_warehouses.date_sx, "NULL"), "__", coalesce(tb_quantity_warehouses.date_sd, "NULL"), "__", coalesce(tb_quantity_warehouses.date_use, "NULL")) as id,
                            CONCAT(tbllocaltion_warehouses.name, , "(SL chuẩn: ", tb_quantity_warehouses.product_quantity,")") as text,
                            tb_quantity_warehouses.lot_code as lot_code,
                            tb_quantity_warehouses.date_sx as date_sx,
                            tb_quantity_warehouses.date_sd as date_sd,
                            tb_quantity_warehouses.date_use as date_use,
                            tbllocaltion_warehouses.name as name,
                            tb_quantity_warehouses.product_quantity as product_quantity
                        ', false);
                        $this->db->from('tbllocaltion_warehouses');
                        $this->db->join($tbQuantityWarehouses, 'tb_quantity_warehouses.localtion_id = tbllocaltion_warehouses.id');
                        $this->db->where('tbllocaltion_warehouses.warehouse', $warehouse_id);
                        $this->db->where('tb_quantity_warehouses.product_quantity >', 0);
                        $this->db->group_start();
                        $this->db->where('tbllocaltion_warehouses.pod_id', 0);
                        $this->db->or_where('exists (
                            SELECT tbl_productions_orders_details.id
                            FROM tbl_productions_orders_details
                            WHERE tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id AND tbl_productions_orders_details.object_type = "business_plan"
                        )', false, false);
                        $this->db->group_end();
                        $location_warehouses = $this->db->get()->result_array();
                        if (!empty($location_warehouses)) {
                            foreach ($location_warehouses as $k => $val) {
                                $name_location = '';

                                $product_quantity = $val['product_quantity'];
                                $lot_code = $val['lot_code'] ? ' - Lot: ' . $val['lot_code'] : '';
                                $date_sx = $val['date_sx'] ? ' - Ngày SX: ' . _d($val['date_sx']) : '';
                                $date_sd = $val['date_sd'] ? ' - Ngày SD: ' . _d($val['date_sd']) : '';

                                // $location_warehouses[$k]['text'] = $value['name'].' - '.$val['text'].$lot_code.$date_sx.$date_sd;
                                $location_warehouses[$k]['text'] = $val['text'] . $lot_code . $date_sx . $date_sd;
                            }
                        }

                        $results[] = ['text' => $value['name'], 'children' => $location_warehouses];
                    }
                }
            }
        }
        $data['results'] = $results;
        echo json_encode($data);
    }
    public function rowLocationWarehouseNew_warehouse()
    {
        $data = [];
        $results = [];
        if ($this->input->post()) {
            $item = $this->input->post('item_id');
            $warehouse_id = $this->input->post('warehouse_id');
            if (!empty($item)) {
                $item = explode('__', $item);
                $type_item = $item[0];
                $item_id = $item[1];

                $item_type = $type_item;
                if ($item_type == "materials") {
                    $item_type = "nvl";
                } else if ($item_type == "tools_supplies") {
                    $item_type = "tools";
                } else {
                    $item_type = "product";
                }

                $this->db->select('tblwarehouse.id, tblwarehouse.name');
                $this->db->from('tblwarehouse');
                $this->db->where('tblwarehouse.id !=', WAREHOUSES_CAPACITY);
                if (!empty($item_id)) {
                    $this->db->where('(
                        EXISTS (
                            SELECT tblwarehouse_items.id
                            FROM tblwarehouse_items
                            WHERE tblwarehouse_items.type_items = "' . $item_type . '" AND tblwarehouse_items.id_items = "' . $item_id . '" AND tblwarehouse.id = tblwarehouse_items.warehouse_id
                        )
                    )');
                }
                $warehouses = $this->db->get()->result_array();

                if (!empty($warehouses)) {
                    foreach ($warehouses as $key => $value) {
                        $warehouse_id = $value['id'];

                        $tbQuantityWarehouses = '(
                            SELECT
                                tblwarehouse_items.localtion as localtion_id,
                                tblwarehouse_items.lot_code, 
                                tblwarehouse_items.date_sx, 
                                tblwarehouse_items.date_sd, 
                                tblwarehouse_items.date_use,
                                SUM(tblwarehouse_items.product_quantity) as product_quantity
                            FROM tblwarehouse_items
                            WHERE tblwarehouse_items.type_items = "' . $item_type . '" AND tblwarehouse_items.id_items = "' . $item_id . '" AND tblwarehouse_items.warehouse_id = "' . $warehouse_id . '"
                            GROUP BY tblwarehouse_items.localtion, tblwarehouse_items.lot_code, tblwarehouse_items.date_sx, tblwarehouse_items.date_sd, tblwarehouse_items.date_use
                        ) tb_quantity_warehouses';

                        $this->db->select('
                            CONCAT(tbllocaltion_warehouses.warehouse, "__", tbllocaltion_warehouses.id, "__", coalesce(tb_quantity_warehouses.lot_code, "NULL"), "__", coalesce(tb_quantity_warehouses.date_sx, "NULL"), "__", coalesce(tb_quantity_warehouses.date_sd, "NULL"), "__", coalesce(tb_quantity_warehouses.date_use, "NULL")) as id,
                            CONCAT(tbllocaltion_warehouses.name, , "(SL kho: ", tb_quantity_warehouses.product_quantity,")") as text,
                            tb_quantity_warehouses.lot_code as lot_code,
                            tb_quantity_warehouses.date_sx as date_sx,
                            tb_quantity_warehouses.date_sd as date_sd,
                            tb_quantity_warehouses.date_use as date_use,
                            tbllocaltion_warehouses.name as name,
                            tb_quantity_warehouses.product_quantity as product_quantity
                        ', false);
                        $this->db->from('tbllocaltion_warehouses');
                        $this->db->join($tbQuantityWarehouses, 'tb_quantity_warehouses.localtion_id = tbllocaltion_warehouses.id');
                        $this->db->where('tbllocaltion_warehouses.warehouse', $warehouse_id);
                        $this->db->where('tb_quantity_warehouses.product_quantity >', 0);
                        $this->db->group_start();
                        $this->db->where('tbllocaltion_warehouses.pod_id', 0);
                        $this->db->or_where('exists (
                            SELECT tbl_productions_orders_details.id
                            FROM tbl_productions_orders_details
                            WHERE tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id AND tbl_productions_orders_details.object_type = "business_plan"
                        )', false, false);
                        $this->db->group_end();
                        $location_warehouses = $this->db->get()->result_array();
                        if (!empty($location_warehouses)) {
                            foreach ($location_warehouses as $k => $val) {
                                $name_location = '';

                                $product_quantity = $val['product_quantity'];
                                $lot_code = $val['lot_code'] ? ' - Lot: ' . $val['lot_code'] : '';
                                $date_sx = $val['date_sx'] ? ' - Ngày SX: ' . _d($val['date_sx']) : '';
                                $date_sd = $val['date_sd'] ? ' - Ngày SD: ' . _d($val['date_sd']) : '';

                                // $location_warehouses[$k]['text'] = $value['name'].' - '.$val['text'].$lot_code.$date_sx.$date_sd;
                                $location_warehouses[$k]['text'] = $val['text'] . $lot_code . $date_sx . $date_sd;
                            }
                        }

                        $results[] = ['text' => $value['name'], 'children' => $location_warehouses];
                    }
                }
            }
        }
        $data['results'] = $results;
        echo json_encode($data);
    }
    public function calCostingSuggProductC($purchase_products_id)
    {
        calCostingSuggProduct($purchase_products_id);
    }

    public function get_items_purchase_product()
    {
        $id = $this->input->post('id');
        $data['purchase_product_items'] = get_table_where('tbl_purchase_product_items', array('purchase_product_id' => $id));
        $purchase_product = get_table_where('tbl_purchase_products', ['id' => $id], '', 'row_array');
        foreach ($data['purchase_product_items'] as $key => $value) {
            $this->db->select('tbllocaltion_warehouses.*');
            $this->db->from('tbllocaltion_warehouses');
            $this->db->where('tbllocaltion_warehouses.warehouse', $purchase_product['warehouse_id']);
            $this->db->where('(pod_id = 0 OR pod_id = ' . $value['productions_orders_details_id'] . ' OR tbllocaltion_warehouses.id = ' . $value['location_id'] . ' )');
            $this->db->where('order_id', 0);
            $this->db->where('productions_plan_id', 0);
            $dtLocationWarehouse = $this->db->get()->result_array();
            $string = '<option></option>';
            if (!empty($dtLocationWarehouse)) {
                foreach ($dtLocationWarehouse as $kk => $vv) {
                    $string .= '<option value="' . $vv['id'] . '">' . $vv['name'] . '</option>';
                }
            }
            $data['purchase_product_items'][$key]['local'] = $string;
        }

        echo json_encode($data);
    }
}
