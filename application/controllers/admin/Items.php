<?php

// header('Content-Type: text/html; charset=utf-8');
defined('BASEPATH') or exit('No direct script access allowed');

class Items extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('items_model');
        $this->load->model('unit_model');
        $this->load->model('species_model');
        $this->load->model('category_model');
        // $this->lang->load('vietnamese/form_validation_lang');
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->allowed_file_size = '1024';
        $this->upload_path = get_upload_path_by_type('materials');
        $this->datetime_now = time();
        $this->custom_fields = get_custom_fields('materials');
        $this->show_table_custom_fields = get_table_custom_fields('materials');

        $this->perViewCategory = has_permission('items_category', '', 'view');
        $this->perAddCategory = has_permission('items_category', '', 'create');
        $this->perEditCategory = has_permission('items_category', '', 'edit');
        $this->perDeleteCategory = has_permission('items_category', '', 'delete');
        $this->perExportCategory = has_permission('items_category', '', 'export');

        $this->perViewItems = has_permission('items', '', 'view');
        $this->perAddItems = has_permission('items', '', 'create');
        $this->perEditItems = has_permission('items', '', 'edit');
        $this->perDeleteItems = has_permission('items', '', 'delete');
        $this->perExportItems = has_permission('items', '', 'export');
    }

    public function index()
    {
        if (!$this->perViewItems) {
            accessDenied();
        }
        $data['tnh'] = true;
        $data['path'] = $this->upload_path;
        $data['title'] = _l('tnh_item_materials_list');
        $th = '';
        $targets = 18;
        $script = '';
        if (!empty($this->show_table_custom_fields)) {
            foreach ($this->show_table_custom_fields as $key => $value) {
                $th .= '<th>' . _maybe_translate_custom_field_name($value['name'], $value['slug']) . '</th>';
                $script .= '{
                    "targets": ' . $targets . ', "name": "' . $value['slug'] . '", "width": "80px"
                },';
                $targets++;
            }
        }

        $data['species'] = $this->species_model->getSpecies();
        $data['targetsId'] = $targets;
        $targets++;
        $data['targets'] = $targets;
        $data['script'] = $script;
        $data['th'] = $th;
        $this->load->view('admin/items/manage', $data);
    }

    function getMaterials()
    {
        if (!$this->perViewItems) {
            accessDenied($js = true);
        }

        $category_search = $this->input->post('category_search');
        $materials_search = $this->input->post('materials_search');
        $species_search = $this->input->post('species_search');

        $date_start_search = $this->input->post('date_start_search');
        $date_end_search = $this->input->post('date_end_search');
        $arrBranch = get_branch_staff();
        $is_admin = is_admin();

        $exchange_items = "(
            SELECT
                tbl_exchange_items.item_id,
                GROUP_CONCAT(CONCAT(tblunits.unit, '::', tbl_exchange_items.number_exchange, '') SEPARATOR ':::') as ex
            FROM tbl_exchange_items
            LEFT JOIN tblunits ON tblunits.unitid = tbl_exchange_items.unit_id
            GROUP BY tbl_exchange_items.item_id
        ) as exchange_items";

        $quantityInventory = "(
            SELECT
                SUM(tblwarehouse_items.product_quantity)
            FROM tblwarehouse_items
            INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
            WHERE tblwarehouse_items.id_items = tbl_materials.id AND tblwarehouse_items.type_items = 'nvl' AND tblwarehouse.supplier_id = 0 AND tblwarehouse.id != " . WAREHOUSES_CAPACITY . "
        )";

        $warehouses = "(
            SELECT GROUP_CONCAT(DISTINCT(tblwarehouse.name) SEPARATOR '</br>')
            FROM tblwarehouse_items
            INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
            WHERE tblwarehouse_items.id_items = tbl_materials.id AND tblwarehouse_items.type_items = 'nvl'
        )";

        $datePurchaseMaterial = "(
            SELECT tblimport.date
            FROM tblimport
            INNER JOIN tblimport_items ON tblimport.id = tblimport_items.id_import
            WHERE tblimport_items.type = 'nvl' AND tblimport_items.product_id = tbl_materials.id
            ORDER BY tblimport.date DESC
            LIMIT 1
        )";

        $userPurchaseMaterial = "(
            SELECT CONCAT(tblstaff.firstname, ' ', tblstaff.lastname)
            FROM tblimport
            INNER JOIN tblimport_items ON tblimport.id = tblimport_items.id_import
            LEFT JOIN tblstaff ON tblstaff.staffid = tblimport.staff_create
            WHERE tblimport_items.type = 'nvl' AND tblimport_items.product_id = tbl_materials.id
            ORDER BY tblimport.date DESC
            LIMIT 1
        )";

        $referencePurchaseMaterial = "(
            SELECT CONCAT(tblimport.prefix, ' ', tblimport.code, '')
            FROM tblimport
            INNER JOIN tblimport_items ON tblimport.id = tblimport_items.id_import
            WHERE tblimport_items.type = 'nvl' AND tblimport_items.product_id = tbl_materials.id
            ORDER BY tblimport.date DESC
            LIMIT 1
        )";

        $quantityWarehouseInventory = "(
            SELECT
                GROUP_CONCAT(CONCAT(tblwarehouse.name, ': ', wt.product_quantity) SEPARATOR '</br>')
            FROM (
                SELECT
                    tblwarehouse_items.id_items as id_items,
                    tblwarehouse_items.warehouse_id as warehouse_id,
                    SUM(tblwarehouse_items.product_quantity) as product_quantity
                FROM tblwarehouse_items
                WHERE tblwarehouse_items.type_items = 'nvl'
                GROUP BY tblwarehouse_items.warehouse_id, tblwarehouse_items.id_items
            ) wt
            INNER JOIN tblwarehouse ON tblwarehouse.id = wt.warehouse_id
            WHERE wt.id_items = tbl_materials.id AND tblwarehouse.supplier_id = 0 AND tblwarehouse.id != " . WAREHOUSES_CAPACITY . "
        )";

        $select_custom_fields = "";
        $custom = [];
        $custom_select = [];
        $target = 18;
        if (!empty($this->show_table_custom_fields)) {
            foreach ($this->show_table_custom_fields as $key => $value) {
                $select = "COALESCE((
                    SELECT tblcustomfieldsvalues.value
                    FROM tblcustomfieldsvalues
                    WHERE tblcustomfieldsvalues.fieldto = 'materials' AND tblcustomfieldsvalues.relid = tbl_materials.id AND tblcustomfieldsvalues.fieldid = " . $value['id'] . "
                ), '') ";
                $select_custom_fields .= $select . " as " . $value['slug'] . ", ";
                $custom[] = [
                    'index' => $target,
                    // 'cloumn' => $select,
                    'select' => $value['slug'],
                ];
                $custom_select[$target] = $select;
                $target++;
            }
        }

        $custom[] = ['index' => 9, 'select' => 'quantity_inventory'];
        $custom_select[9] = $quantityInventory;

        $custom[] = ['index' => 14, 'select' => 'warehouses'];
        $custom_select[14] = $warehouses;

        $custom[] = ['index' => 15, 'select' => 'date_warehousing_nearest'];
        $custom_select[15] = $datePurchaseMaterial;

        // $custom[] = ['index' => 14, 'select' => 'user_warehousing_nearest'];
        // $custom_select[14] = $userPurchaseMaterial;

        // $custom[] = ['index' => 15, 'select' => 'reference_warehousing_nearest'];
        // $custom_select[15] = $referencePurchaseMaterial;

        // print_arrays($custom_ordering);
        $this->db->simple_query('SET SESSION group_concat_max_len=1500000000000000');
        $this->datatables->select("
            tbl_materials.id as id,
            tbl_materials.images as images,
            tbl_category_items.code as category_name,
            CONCAT(tbl_materials.code,'__',COALESCE(tblbranch.name,'')) as code,
            tbl_materials.name as name,
            tbl_species.name as name_species,
            tbl_materials.paper as paper,
            tbl_materials.quantitative as quantitative,
            tblunits.unit as unit_name,
            COALESCE($quantityInventory, 0) as quantity_inventory,
            tbl_materials.price_import as price_import,
            tbl_materials.status as status,
            tbl_materials.is_single_use as is_single_use,
            tbl_materials.is_zinc as is_zinc,
            tbl_materials.note as note,
            $warehouses as warehouses,
            $datePurchaseMaterial as date_warehousing_nearest,
            $quantityWarehouseInventory as qty_ws_inventory,
            $select_custom_fields
            tbl_materials.id as id_sort
            ", FALSE)
            ->from('tbl_materials')
            ->join('tbl_category_items', 'tbl_category_items.id = tbl_materials.category_id', 'left')
            ->join('tbl_species', 'tbl_species.id = tbl_materials.species', 'left')
            ->join('tblunits', 'tblunits.unitid = tbl_materials.standard_unit', 'left')
            ->join('tblbranch', 'tblbranch.id = tbl_materials.id_branch', 'left')
            ->join($exchange_items, 'exchange_items.item_id = tbl_materials.id', 'left');

        $this->datatables->custom_ordering($custom);
        $this->datatables->custom_select($custom_select);

        if (!empty($date_start_search)) {
            $this->datatables->where('tbl_materials.date_created >="' . to_sql_date($date_start_search) . ' 00:00:00"');
        }
        if (!empty($date_end_search)) {
            $this->datatables->where('tbl_materials.date_created <="' . to_sql_date($date_end_search) . ' 23:59:59"');
        }

        if (!empty($category_search)) {
            $this->datatables->where('tbl_materials.category_id', $category_search);
        }

        if (!empty($materials_search)) {
            $this->datatables->where('tbl_materials.id', $materials_search);
        }

        if (!empty($species_search)) {
            $this->datatables->where('tbl_species.id', $species_search);
        }

        if (!$is_admin) {
            if (!empty($arrBranch)) {
                $coverStrBranch = implode(",", $arrBranch);
                $this->datatables->where('tbl_materials.id_branch IN (' . $coverStrBranch . ')');
            } else {
                $this->datatables->where('tbl_materials.id', 0);
            }
        }

        $view = '<a class="tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/items/view_item/$1"><i class="fa fa-file-text-o width-icon-actions"></i> ' . lang('view') . '</a>';

        if (!$this->perEditItems) {
            $edit = '';
        } else {
            $edit = '<a class="tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/items/edit_item/$1"><i class="fa fa-pencil width-icon-actions"></i> ' . lang('edit') . '</a>';
        }

        if (!$this->perDeleteItems) {
            $delete = '';
        } else {
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/items/delete_material/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>';
        }

        $copy = $this->perAddItems ? '<a class="tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/items/edit_item/$1/copy"><i class="fa fa-copy width-icon-actions"></i> ' . lang('copy') . '</a>' : '';

        $actions = '
        <div class="dropdown text-center">
            <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
            ' . lang('actions') . '
            <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                <li>' . $view . '</li>
                <li>' . $edit . '</li>
                <li>' . $copy . '</li>
                <li class="not-outside">' . $delete . '</li>
            </ul>
        </div>';

        $this->datatables->add_column('actions', $actions, 'id');
        echo $this->datatables->generate();
    }

    public function add_item()
    {
        if (!$this->perAddItems) {
            accessDenied($js = true);
        }
        $data = [];
        if ($this->input->post()) {
            $this->form_validation->set_rules('category', lang("tnh_item_materials_category"), 'required');
            $this->form_validation->set_rules('name', lang("name"), 'required');
            $this->form_validation->set_rules('unit', lang("unit"), 'required');
            $this->form_validation->set_rules('id_branch', lang("Chi nhánh"), 'required');
            $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_materials.code]');
            if ($this->form_validation->run() == true) {
                $category = $this->input->post('category');
                $name = $this->input->post('name');
                $name_customer = $this->input->post('name_customer');
                $name_supplier = $this->input->post('name_supplier');
                $code = $this->input->post('code');
                $note = $this->input->post('note', false);
                $quantity_begin = number_unformat($this->input->post('quantity_begin'));
                $price_import = number_unformat($this->input->post('price_import'));
                $price_sell = number_unformat($this->input->post('price_sell'));
                $quantity_minimum = number_unformat($this->input->post('quantity_minimum'));
                $id_branch = $this->input->post('id_branch');
                $unit = $this->input->post('unit');
                $note = $this->input->post('note', false);

                $quality_standard = $this->input->post('quality_standard');
                $mode = $this->input->post('mode');
                $mode_id = $this->input->post('mode_id');
                $quantitative = number_unformat($this->input->post('quantitative'));

                $hand_input_code = $this->input->post('hand_input_code');
                $species = $this->input->post('species');
                $material_code_supplier = $this->input->post('material_code_supplier');
                $paper = number_unformat($this->input->post('paper'));
                $standard_cl = $this->input->post('standard_cl');
                $time_payment = number_unformat($this->input->post('time_payment'));
                $pp_check = $this->input->post('pp_check');
                $name_account = $this->input->post('name_account');
                $height = number_unformat($this->input->post('height'));
                $longs = number_unformat($this->input->post('longs'));
                $wide = number_unformat($this->input->post('wide'));

                $time_stock = number_unformat($this->input->post('time_stock'));
                $standard_unit = $this->input->post('standard_unit');
                $unit_payment = $this->input->post('unit_payment');
                $suppliers = $this->input->post('suppliers');
                $is_single_use = $this->input->post('is_single_use');

                $exchange_unit = number_unformat($this->input->post('exchange_unit'));
                $exchange_standard_unit = number_unformat($this->input->post('exchange_standard_unit'));
                $exchange_unit_payment = number_unformat($this->input->post('exchange_unit_payment'));
                $recipe = !empty($this->input->post('recipe')) ? $this->input->post('recipe') : 1;


                $unit_of_measure = $this->input->post('unit_of_measure');
                $packaging_standard = $this->input->post('packaging_standard');
                $npl_standard = $this->input->post('npl_standard');
                $allowable = number_unformat($this->input->post('allowable'));

                if (empty($unit) || empty($standard_unit) || empty($unit_payment)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Vui lòng chọn đơn vị chuẩn, đơn vị kho, đơn vị thanh toán');
                }

                if (empty($exchange_unit) || empty($exchange_standard_unit) || empty($exchange_unit_payment)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Vui lòng nhập quy đổi đơn vị chuẩn, đơn vị kho, đơn vị thanh toán');
                }

                if (empty($hand_input_code)) {
                    $code = handlingCodeMaterial($category, $species, $paper, $quantitative, $material_code_supplier, $suppliers);
                }

                $options = [
                    'unit_of_measure' => $unit_of_measure,
                    'packaging_standard' => $packaging_standard,
                    'npl_standard' => $npl_standard,
                    'allowable' => $allowable,

                    'category_id' => $category,
                    'name' => $name,
                    'name_customer' => $name_customer,
                    'name_supplier' => $name_supplier,
                    'code' => $code,
                    'quantity_begin' => $quantity_begin,
                    'price_import' => $price_import,
                    'price_sell' => $price_sell,
                    'quantity_minimum' => $quantity_minimum,
                    'unit_id' => $unit,
                    'note' => $note,
                    'date_created' => date('Y-m-d H:i:s'),
                    'created_by' => get_staff_user_id(),

                    'quality_standard' => $quality_standard,
                    'mode' => $mode,
                    'quantitative' => $quantitative,

                    'hand_input_code' => $hand_input_code,
                    'species' => $species,
                    'material_code_supplier' => $material_code_supplier,
                    'paper' => $paper,
                    'standard_cl' => $standard_cl,
                    'time_payment' => $time_payment,
                    'pp_check' => $pp_check,
                    'name_account' => $name_account,
                    'height' => $height,
                    'time_stock' => $time_stock,
                    'standard_unit' => $standard_unit,
                    'unit_payment' => $unit_payment,
                    'suppliers' => $suppliers,
                    'is_single_use' => $is_single_use,
                    'longs' => $longs,
                    'wide' => $wide,

                    'exchange_unit' => $exchange_unit,
                    'exchange_standard_unit' => $exchange_standard_unit,
                    'exchange_unit_payment' => $exchange_unit_payment,
                    'recipe' => $recipe,
                    'mode_id' => $mode_id,
                    'id_branch' => $id_branch
                ];

                //exchange
                $exchange = false;
                $ex = $this->input->post('unit_exchange');
                if (!empty($ex)) {
                    foreach ($ex as $key => $value) {
                        if (empty($value)) continue;
                        if ($key != 0) continue;
                        $number_exchange = $this->input->post('number_exchange')[$key];
                        $exchange[$key]['unit_id'] = $value;
                        $exchange[$key]['number_exchange'] = $number_exchange;
                    }
                }

                if (empty($exchange)) {
                    $exchange[$key]['unit_id'] = $unit;
                    $exchange[$key]['number_exchange'] = 1;
                }

                //image
                $this->load->library('upload');
                if (!empty($_FILES['image']) && $_FILES['image']['size'] > 0) {
                    $config['upload_path'] = $this->upload_path;
                    $config['allowed_types'] = $this->image_types;

                    $config['max_size'] = $this->allowed_file_size;
                    // $config['max_width'] = $this->Settings->iwidth;
                    // $config['max_height'] = $this->Settings->iheight;
                    // $config['file_name'] = tnh_vn_to_str($code).'_'.$this->datetime_now;
                    // $config['overwrite'] = TRUE;
                    // //$config['max_filename'] = 25;
                    $config['encrypt_name'] = false;
                    $this->upload->initialize($config);

                    if (!$this->upload->do_upload('image')) {
                        $error = $this->upload->display_errors();
                        $this->session->set_flashdata('error', $error);
                        $data['result'] = 0;
                        $data['message'] = $error;
                        echo json_encode($data);
                        return;
                    }
                    $images = $this->upload->file_name;
                    $options['images'] = $images;
                } else {
                    $options['images'] = NULL;
                }
                //image multiple
                if (!empty($_FILES['images_multiple']) && !empty($_FILES['images_multiple']['size'])) {
                    $fileCount = count($_FILES['images_multiple']['name']);
                    for ($i = 0; $i < $fileCount; $i++) {
                        $_FILES['file']['name'] = $_FILES['images_multiple']['name'][$i];
                        $_FILES['file']['type'] = $_FILES['images_multiple']['type'][$i];
                        $_FILES['file']['tmp_name'] = $_FILES['images_multiple']['tmp_name'][$i];
                        $_FILES['file']['error'] = $_FILES['images_multiple']['error'][$i];
                        $_FILES['file']['size'] = $_FILES['images_multiple']['size'][$i];

                        $config['upload_path'] = $this->upload_path;
                        $config['allowed_types'] = $this->image_types;
                        $config['max_size'] = $this->allowed_file_size;
                        // $config['file_name'] = tnh_vn_to_str($code).'_'.$i.'_'.$this->datetime_now;
                        // $config['overwrite'] = TRUE;
                        // //$config['max_filename'] = 25;
                        $config['encrypt_name'] = false;
                        $this->upload->initialize($config);
                        if ($this->upload->do_upload('file')) {
                            $uploadData[$i] = $this->upload->file_name;
                        }
                    }
                }
                if (!empty($uploadData)) {
                    $options['images_multiple'] = implode('||', $uploadData);
                } else {
                    $options['images_multiple'] = NULL;
                }
                //end image multiple

                //handing warehouse locaiton
                $warehouses = $this->input->post('warehouses');
                if (!empty($warehouses)) {
                    foreach ($warehouses as $key => $value) {
                        if (empty($value)) continue;
                        $location_id = $this->input->post('location')[$key];
                        $materialWarehouse[] = [
                            'warehouse_id' => $value,
                            'location_id' => $location_id,
                        ];
                    }
                }
                //end
                //handing suppliers
                $counter = $this->input->post('counter');
                if (!empty($counter)) {
                    foreach ($counter as $key => $value) {
                        $supplier_id = $this->input->post('suppliers_arr')[$value];
                        if (empty($supplier_id)) continue;
                        $procedure_id = !empty($this->input->post('procedure')[$value]) ?  $this->input->post('procedure')[$value] : false;

                        if (!empty($procedure_id)) {
                            foreach ($procedure_id as $k => $val) {
                                $procedure_id = $this->input->post('procedure')[$value][$k];
                                $sequence = $this->input->post('sequence')[$value][$k];
                                $number_date = $this->input->post('number_date')[$value][$k];
                                $materialSuppliers[] = [
                                    'supplier_id' => $supplier_id,
                                    'procedure_id' => $procedure_id,
                                    'sequence' => $sequence,
                                    'number_date' => $number_date,
                                ];
                            }
                        } else {
                            $materialSuppliers[] = [
                                'supplier_id' => $supplier_id,
                                'procedure_id' => 0,
                                'sequence' => 0,
                                'number_date' => 0,
                            ];
                        }
                    }
                }
                //end

                $id = $this->items_model->insertMaterials($options);
                if ($id) {
                    if (!empty($exchange)) {
                        foreach ($exchange as $key => $value) {
                            $exchange[$key]['item_id'] = $id;
                        }
                        $this->items_model->insertExchangeItems($exchange);
                    }
                    if ($this->input->post('custom_fields')) {
                        handle_custom_fields_post($id, $this->input->post('custom_fields'));
                    }
                    //insert warehouse locaiton
                    if (!empty($materialWarehouse)) {
                        foreach ($materialWarehouse as $key => $value) {
                            $materialWarehouse[$key]['material_id'] = $id;
                        }
                        $this->items_model->insertBatchMaterialWarehouse($materialWarehouse);
                    }
                    //end
                    //insert material suppliers
                    if (!empty($materialSuppliers)) {
                        foreach ($materialSuppliers as $key => $value) {
                            $materialSuppliers[$key]['material_id'] = $id;
                        }
                        $this->items_model->insertBatchMaterialSuppliers($materialSuppliers);
                    }
                    //end

                    insertActivityLog([
                        'type_parent_obj' => 'items',
                        'table_obj' => 'tbl_materials',
                        'id_obj' => $id,
                        'name_obj' => $code,
                        'content' => lang('Tạo mới nguyên phụ liệu') . ' [' . $code . ']',
                        'actions' => 'add'
                    ]);

                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    //remove images
                    if (file_exists($this->upload_path . '' . $images)) {
                        unlink($this->upload_path . '' . $images);
                    }
                    if (!empty($uploadData)) {
                        foreach ($uploadData as $key => $value) {
                            if (file_exists($this->upload_path . '' . $value)) {
                                unlink($this->upload_path . '' . $value);
                            }
                        }
                    }
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            return;
        } else {
            $data['mode_materials'] = $this->category_model->getModeMaterials();
            $data['species'] = $this->species_model->getSpecies();
            $data['procedure_detail'] = $this->site_model->getProcedureClientDetail();
            $data['warehouses'] = $this->site_model->getWarehouse();
            $data['custom_fields'] = $this->custom_fields;
            $data['units'] = $this->unit_model->getUnits();
            $data['branch'] = $this->site_model->getBranch();
            $this->load->view('admin/items/add_item', $data);
        }
    }

    public function edit_item($id, $actions = "edit")
    {
        if (!$this->perEditItems) {
            accessDenied($js = true);
        }
        $data = [];
        $material = $this->items_model->rowMaterial($id);
        if ($actions == 'edit') {
            $data['isMaterial'] = $this->items_model->isMaterialInBOM('materials', $id);
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('category', lang("tnh_item_materials_category"), 'required');
            $this->form_validation->set_rules('name', lang("name"), 'required');
            $this->form_validation->set_rules('unit', lang("unit"), 'required');
            $this->form_validation->set_rules('id_branch', lang("Chi nhánh"), 'required');
            if ($material['code'] != $this->input->post('code', false) || $actions == 'copy') {
                $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_materials.code]');
            }
            if ($this->form_validation->run() == true) {
                // print_arrays($this->input->post());
                $images_old = $actions == 'edit' ? $material['images'] : null;
                $category = $this->input->post('category');
                $name = $this->input->post('name');
                $name_customer = $this->input->post('name_customer');
                $name_supplier = $this->input->post('name_supplier');
                $code = $this->input->post('code');
                $note = $this->input->post('note', false);
                $quantity_begin = number_unformat($this->input->post('quantity_begin'));
                $price_import = number_unformat($this->input->post('price_import'));
                $price_sell = number_unformat($this->input->post('price_sell'));
                $quantity_minimum = number_unformat($this->input->post('quantity_minimum'));
                $unit = $this->input->post('unit');
                $id_branch = $this->input->post('id_branch');
                $note = $this->input->post('note', false);

                $quality_standard = $this->input->post('quality_standard');
                $mode = $this->input->post('mode');
                $mode_id = $this->input->post('mode_id');
                $quantitative = number_unformat($this->input->post('quantitative'));

                $hand_input_code = $this->input->post('hand_input_code');
                $species = $this->input->post('species');
                $material_code_supplier = $this->input->post('material_code_supplier');
                $paper = number_unformat($this->input->post('paper'));
                $standard_cl = $this->input->post('standard_cl');
                $time_payment = number_unformat($this->input->post('time_payment'));
                $pp_check = $this->input->post('pp_check');
                $name_account = $this->input->post('name_account');
                $height = number_unformat($this->input->post('height'));
                $time_stock = number_unformat($this->input->post('time_stock'));
                $standard_unit = $this->input->post('standard_unit');
                $unit_payment = $this->input->post('unit_payment');
                $suppliers = $this->input->post('suppliers');
                $is_single_use = $this->input->post('is_single_use');

                $longs = number_unformat($this->input->post('longs'));
                $wide = number_unformat($this->input->post('wide'));
                $recipe = !empty($this->input->post('recipe')) ? $this->input->post('recipe') : 1;

                $exchange_unit = number_unformat($this->input->post('exchange_unit'));
                $exchange_standard_unit = number_unformat($this->input->post('exchange_standard_unit'));
                $exchange_unit_payment = number_unformat($this->input->post('exchange_unit_payment'));

                $unit_of_measure = $this->input->post('unit_of_measure');
                $packaging_standard = $this->input->post('packaging_standard');
                $npl_standard = $this->input->post('npl_standard');
                $allowable = number_unformat($this->input->post('allowable'));

                if (empty($unit) || empty($standard_unit) || empty($unit_payment)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Vui lòng chọn đơn vị chuẩn, đơn vị kho, đơn vị thanh toán');
                }

                if (empty($exchange_unit) || empty($exchange_standard_unit) || empty($exchange_unit_payment)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Vui lòng nhập quy đổi đơn vị chuẩn, đơn vị kho, đơn vị thanh toán');
                }

                if (empty($hand_input_code)) {
                    $code = handlingCodeMaterial($category, $species, $paper, $quantitative, $material_code_supplier, $suppliers);
                }

                $options = [
                    'unit_of_measure' => $unit_of_measure,
                    'packaging_standard' => $packaging_standard,
                    'npl_standard' => $npl_standard,
                    'allowable' => $allowable,
                    'category_id' => $category,
                    'name' => $name,
                    'code' => $code,
                    'name_customer' => $name_customer,
                    'name_supplier' => $name_supplier,
                    'quantity_begin' => $quantity_begin,
                    'price_import' => $price_import,
                    'price_sell' => $price_sell,
                    'quantity_minimum' => $quantity_minimum,
                    'unit_id' => $unit,
                    'note' => $note,
                    'date_updated' => date('Y-m-d H:i:s'),
                    'updated_by' => get_staff_user_id(),

                    'quality_standard' => $quality_standard,
                    'mode' => $mode,
                    'quantitative' => $quantitative,

                    'hand_input_code' => $hand_input_code,
                    'species' => $species,
                    'material_code_supplier' => $material_code_supplier,
                    'paper' => $paper,
                    'standard_cl' => $standard_cl,
                    'time_payment' => $time_payment,
                    'pp_check' => $pp_check,
                    'name_account' => $name_account,
                    'height' => $height,
                    'time_stock' => $time_stock,
                    'standard_unit' => $standard_unit,
                    'unit_payment' => $unit_payment,
                    'suppliers' => $suppliers,
                    'is_single_use' => $is_single_use,
                    'longs' => $longs,
                    'wide' => $wide,

                    'exchange_unit' => $exchange_unit,
                    'exchange_standard_unit' => $exchange_standard_unit,
                    'exchange_unit_payment' => $exchange_unit_payment,
                    'recipe' => $recipe,
                    'mode_id' => $mode_id,
                    'id_branch' => $id_branch
                ];

                if ($actions == "copy") {
                    $options['date_created'] = date('Y-m-d H:i:s');
                    $options['created_by'] = get_staff_user_id();
                }

                $this->load->library('upload');
                if (!empty($_FILES['image']) && $_FILES['image']['size'] > 0) {
                    $config['upload_path'] = $this->upload_path;
                    $config['allowed_types'] = $this->image_types;
                    $config['max_size'] = $this->allowed_file_size;
                    // $config['max_width'] = $this->Settings->iwidth;
                    // $config['max_height'] = $this->Settings->iheight;
                    // $config['file_name'] = tnh_vn_to_str($code).'_'.$this->datetime_now;
                    // $config['overwrite'] = TRUE;
                    // //$config['max_filename'] = 25;
                    $config['encrypt_name'] = false;
                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload('image')) {
                        $error = $this->upload->display_errors();
                        $this->session->set_flashdata('error', $error);
                        $data['result'] = 0;
                        $data['message'] = $error;
                        echo json_encode($data);
                        return;
                    }
                    $images = $this->upload->file_name;
                    $options['images'] = $images;
                } else {
                    $options['images'] = $images_old;
                }

                // print_arrays($options['images']);

                //image multiple
                $images_multiple_old = $actions == 'edit' ? $material['images_multiple'] : null;
                $images_multiple_old_form = $actions == 'edit' ? $this->input->post('images_old') : null;
                if (!empty($_FILES['images_multiple']) && !empty($_FILES['images_multiple']['size'])) {
                    $fileCount = count($_FILES['images_multiple']['name']);
                    $ct = 0;
                    if (!empty($images_multiple_old)) {
                        $arr_images_multiple_old = explode('||', $images_multiple_old);
                        $image_last = $arr_images_multiple_old[count($arr_images_multiple_old) - 1];
                        $arr_last = explode('_', $image_last);
                        $ct = $arr_last[count($arr_last) - 2] + 1;
                    }
                    for ($i = 0; $i < $fileCount; $i++) {
                        $_FILES['file']['name'] = $_FILES['images_multiple']['name'][$i];
                        $_FILES['file']['type'] = $_FILES['images_multiple']['type'][$i];
                        $_FILES['file']['tmp_name'] = $_FILES['images_multiple']['tmp_name'][$i];
                        $_FILES['file']['error'] = $_FILES['images_multiple']['error'][$i];
                        $_FILES['file']['size'] = $_FILES['images_multiple']['size'][$i];

                        $config['upload_path'] = $this->upload_path;
                        $config['allowed_types'] = $this->image_types;
                        $config['max_size'] = $this->allowed_file_size;
                        // $config['file_name'] = tnh_vn_to_str($code).'_'.$ct.'_'.$this->datetime_now;
                        // $config['overwrite'] = TRUE;
                        // //$config['max_filename'] = 25;
                        $config['encrypt_name'] = false;
                        $this->upload->initialize($config);
                        if ($this->upload->do_upload('file')) {
                            $uploadData[$i] = $this->upload->file_name;
                        }
                        $ct++;
                    }
                }
                if (!empty($uploadData)) {
                    if (!empty($images_multiple_old_form)) {
                        $options['images_multiple'] = implode('||', $images_multiple_old_form) . '||' . implode('||', $uploadData);
                    } else {
                        $options['images_multiple'] = implode('||', $uploadData);
                    }
                } else {
                    if (!empty($images_multiple_old_form)) {
                        $options['images_multiple'] = implode('||', $images_multiple_old_form);
                    } else {
                        $options['images_multiple'] = null;
                    }
                }
                //end


                if ($actions == 'edit') {
                    $up = $this->items_model->updateMaterials($id, $options);
                } else {
                    $up = $this->items_model->insertMaterials($options);
                    $id = $up;
                }

                //exchange
                $exchange = false;
                $ex = $this->input->post('unit_exchange');
                if (!empty($ex)) {
                    foreach ($ex as $key => $value) {
                        if (empty($value)) continue;
                        if ($key != 0) continue;
                        $number_exchange = $this->input->post('number_exchange')[$key];
                        $exchange[$key]['item_id'] = $id;
                        $exchange[$key]['unit_id'] = $value;
                        $exchange[$key]['number_exchange'] = $number_exchange;
                    }
                }

                if (empty($exchange)) {
                    $exchange[$key]['unit_id'] = $unit;
                    $exchange[$key]['number_exchange'] = 1;
                }

                //handing warehouse locaiton
                $warehouses = $this->input->post('warehouses');
                if (!empty($warehouses)) {
                    foreach ($warehouses as $key => $value) {
                        if (empty($value)) continue;
                        $location_id = $this->input->post('location')[$key];
                        $materialWarehouse[] = [
                            'material_id' => $id,
                            'warehouse_id' => $value,
                            'location_id' => $location_id,
                        ];
                    }
                }
                //end

                //handing suppliers
                $counter = $this->input->post('counter');
                if (!empty($counter)) {
                    foreach ($counter as $key => $value) {
                        $supplier_id = $this->input->post('suppliers_arr')[$value];
                        if (empty($supplier_id)) continue;
                        $procedure_id = !empty($this->input->post('procedure')[$value]) ?  $this->input->post('procedure')[$value] : false;
                        // print_arrays()

                        if (!empty($procedure_id)) {
                            foreach ($procedure_id as $k => $val) {
                                $procedure_id = $this->input->post('procedure')[$value][$k];
                                $sequence = $this->input->post('sequence')[$value][$k];
                                $number_date = $this->input->post('number_date')[$value][$k];
                                $materialSuppliers[] = [
                                    'material_id' => $id,
                                    'supplier_id' => $supplier_id,
                                    'procedure_id' => $procedure_id,
                                    'sequence' => $sequence,
                                    'number_date' => $number_date,
                                ];
                            }
                        } else {
                            $materialSuppliers[] = [
                                'material_id' => $id,
                                'supplier_id' => $supplier_id,
                                'procedure_id' => 0,
                                'sequence' => 0,
                                'number_date' => 0,
                            ];
                        }
                    }
                }
                //end

                if ($up) {
                    $this->items_model->deleteExchangeByItemId($id);
                    if (!empty($exchange)) {
                        $this->items_model->insertExchangeItems($exchange);
                    }
                    if ($this->input->post('custom_fields')) {
                        handle_custom_fields_post($id, $this->input->post('custom_fields'));
                    }
                    if (!empty($images)) {
                        if (file_exists($this->upload_path . '' . $images_old)) {
                            @unlink($this->upload_path . '' . $images_old);
                        }
                    }
                    if ($this->input->post('remove_image')) {
                        foreach (explode('||', $images_multiple_old) as $key => $value) {
                            if (!empty($images_multiple_old_form)) {
                                if (!in_array($value, $images_multiple_old_form)) {
                                    if (file_exists($this->upload_path . '' . $value)) {
                                        @unlink($this->upload_path . '' . $value);
                                    }
                                }
                            } else {
                                if (file_exists($this->upload_path . '' . $value)) {
                                    @unlink($this->upload_path . '' . $value);
                                }
                            }
                        }
                    }

                    $this->items_model->deleteMaterialSuppliersByMaterialId($id);
                    $this->items_model->deleteMaterialWarehouseByMaterialId($id);
                    //insert warehouse locaiton
                    if (!empty($materialWarehouse)) {
                        $this->items_model->insertBatchMaterialWarehouse($materialWarehouse);
                    }
                    //end
                    //insert material suppliers
                    if (!empty($materialSuppliers)) {
                        $this->items_model->insertBatchMaterialSuppliers($materialSuppliers);
                    }
                    //end

                    insertActivityLog([
                        'type_parent_obj' => 'items',
                        'table_obj' => 'tbl_materials',
                        'id_obj' => $id,
                        'name_obj' => $code,
                        'content' => lang('Sửa nguyên phụ liệu') . ' [' . $code . ']',
                        'actions' => 'edit'
                    ]);

                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    if (file_exists($this->upload_path . '' . $images)) {
                        unlink($this->upload_path . '' . $images);
                    }
                    if (!empty($uploadData)) {
                        foreach ($uploadData as $key => $value) {
                            if (file_exists($this->upload_path . '' . $value)) {
                                unlink($this->upload_path . '' . $value);
                            }
                        }
                    }
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            return;
        } else {
            $data['mode_materials'] = $this->category_model->getModeMaterials();
            $data['species'] = $this->species_model->getSpecies();
            $data['actions'] = $actions;
            $data['id'] = $id;
            $data['custom_fields'] = $this->custom_fields;
            $data['material_suppliers'] = $this->items_model->getGroupMaterialsuppliers($id);
            $data['procedure_detail'] = $this->site_model->getProcedureClientDetail();
            $data['material_warehouse'] = $this->items_model->getMaterialWarehouse($id);
            $data['warehouses'] = $this->site_model->getWarehouse();
            $data['material'] = $material;
            $data['exchanges'] = $this->items_model->getExchangeItemsByItemId($id);
            $data['units'] = $this->unit_model->getUnits();
            $data['branch'] = $this->site_model->getBranch();
            $this->load->view('admin/items/edit_item', $data);
        }
    }

    function view_item($id)
    {
        if (!$this->perViewItems) {
            accessDenied($js = true);
        }
        $material = $this->items_model->rowMaterial($id);
        $category = $this->items_model->rowCategoryItems($material['category_id']);
        $unit = $this->unit_model->rowUnit($material['unit_id']);
        $exchanges = $this->items_model->getExchangeItemsViewByItemId($id);
        $suppliers = $this->items_model->getGroupMaterialsuppliers($id);
        $warehouses = $this->items_model->getMaterialWarehouse($id);

        $data['suppliers'] = $suppliers;
        $data['warehouses'] = $warehouses;
        $data['category'] = $category;
        $data['material'] = $material;
        $data['exchanges'] = $exchanges;
        $data['unit'] = $unit;
        $data['custom_fields'] = $this->custom_fields;
        $data['created_by'] = get_staff_full_name($material['created_by']);
        if (!empty($material['updated_by'])) {
            $data['updated_by'] = get_staff_full_name($material['updated_by']);
        } else {
            $data['updated_by'] = '';
        }
        $data['id'] = $id;
        $this->load->view('admin/items/view_item', $data);
    }

    function delete_material($id)
    {
        if (!$this->perDeleteItems) {
            accessDenied($js = true);
        }
        $data = [];
        if ($id) {
            $material = $this->items_model->rowMaterial($id);
            if ($this->items_model->checkMaterialUse($id)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_exist_not_delete');
                echo json_encode($data);
                return;
            }
            if ($this->items_model->deleteMaterials($id)) {
                $this->items_model->deleteExchangeByItemId($id);
                $this->items_model->deleteMaterialSuppliersByMaterialId($id);
                $this->items_model->deleteMaterialWarehouseByMaterialId($id);
                deleteCustomFields('materials', $id);
                if (!empty($material['images'])) {
                    if (file_exists($this->upload_path . '' . $material['images'])) {
                        @unlink($this->upload_path . '' . $material['images']);
                    }
                }
                if (!empty($material['images_multiple'])) {
                    foreach (explode('||', $material['images_multiple']) as $key => $value) {
                        if (file_exists($this->upload_path . '' . $value)) {
                            @unlink($this->upload_path . '' . $value);
                        }
                    }
                }

                insertActivityLog([
                    'type_parent_obj' => 'items',
                    'table_obj' => 'tbl_materials',
                    'id_obj' => $id,
                    'name_obj' => $material['code'],
                    'content' => lang('Xóa nguyên phụ liệu') . ' [' . $material['code'] . ']',
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

    function delete_material_multiple()
    {
        if (!$this->perDeleteItems) {
            accessDenied($js = true);
        }
        $data = [];
        if ($this->input->post()) {
            if (!$this->input->post('material_id')) {
                $data['result'] = 0;
                $data['message'] = lang('no_data_exists');
                echo json_encode($data);
                return;
            }
            $errors = '';
            $count = 0;
            foreach ($this->input->post('material_id') as $key => $id) {
                $material = $this->items_model->rowMaterial($id);
                if ($this->items_model->checkMaterialUse($id)) {
                    $errors .= '<div class="text-danger">' . $material['code'] . ' ' . lang('tnh_exist_not_delete') . '</div>';
                    continue;
                }
                if ($this->items_model->deleteMaterials($id)) {
                    $this->items_model->deleteExchangeByItemId($id);
                    $this->items_model->deleteMaterialSuppliersByMaterialId($id);
                    $this->items_model->deleteMaterialWarehouseByMaterialId($id);
                    deleteCustomFields('materials', $id);
                    if (!empty($material['images'])) {
                        if (file_exists($this->upload_path . '' . $material['images'])) {
                            @unlink($this->upload_path . '' . $material['images']);
                        }
                    }
                    if (!empty($material['images_multiple'])) {
                        foreach (explode('||', $material['images_multiple']) as $key => $value) {
                            if (file_exists($this->upload_path . '' . $value)) {
                                @unlink($this->upload_path . '' . $value);
                            }
                        }
                    }
                    $count++;
                }
            }
            if ($count) {
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
            $data['errors'] = $errors;
            echo json_encode($data);
            return;
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function category()
    {
        if (!$this->perViewCategory) {
            accessDenied();
        }
        $data['tnh'] = true;
        $data['title'] = _l('tnh_item_materials_category');
        $this->load->view('admin/items/category', $data);
    }

    public function add_category()
    {
        if (!$this->perAddCategory) {
            accessDenied($js = true);
        }
        $data = [];
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_category_items.code]');
            if ($this->form_validation->run() == true) {
                $name = $this->input->post('name');
                $code = $this->input->post('code');
                $note = $this->input->post('note');
                $parent_id = $this->input->post('parent_id') ? $this->input->post('parent_id') : 0;
                $recipe = !empty($this->input->post('recipe')) ? $this->input->post('recipe') : 0;
                $is_vtsx = !empty($this->input->post('is_vtsx')) ? $this->input->post('is_vtsx') : 0;

                $options = [
                    'name' => $name,
                    'code' => $code,
                    'parent_id' => $parent_id,
                    'note' => $note,
                    'recipe' => $recipe,
                    'is_vtsx' => $is_vtsx,
                ];

                $id = $this->items_model->insertCategoryItems($options);
                if ($id) {
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
            return;
        } else {
            $this->load->view('admin/items/add_category', $data);
        }
    }

    public function edit_category($id)
    {
        if (!$this->perEditCategory) {
            accessDenied($js = true);
        }
        $data = [];
        $category = $this->items_model->rowCategoryItems($id);
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($category['code'] != $this->input->post('code')) {
                $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_category_items.code]');
            }
            if ($this->form_validation->run() == true) {
                $name = $this->input->post('name');
                $code = $this->input->post('code');
                $parent_id = $this->input->post('parent_id') ? $this->input->post('parent_id') : 0;
                $note = $this->input->post('note');
                $recipe = !empty($this->input->post('recipe')) ? $this->input->post('recipe') : 0;
                $is_vtsx = !empty($this->input->post('is_vtsx')) ? $this->input->post('is_vtsx') : 0;

                $options = [
                    'name' => $name,
                    'code' => $code,
                    'note' => $note,
                    'parent_id' => $parent_id,
                    'recipe' => $recipe,
                    'is_vtsx' => $is_vtsx,
                ];

                $id = $this->items_model->updateCategoryItems($id, $options);
                if ($id) {
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
            return;
        } else {
            $data['category'] = $category;
            $this->load->view('admin/items/edit_category', $data);
        }
    }

    function getCategory()
    {
        if (!$this->perViewCategory) {
            accessDenied($js = true);
        }
        $this->datatables->select("
            tbl_category_items.id as id,
            0 as records,
            tbl_category_items.code as code,
            tbl_category_items.name as name,
            tbl_category_items.recipe as recipe,
            tbl_category_items.is_primary as is_primary,
            tbl_category_items.is_machines as is_machines,
            tbl_category_items.note as note,
            '' as sub
            ", FALSE)
            ->from('tbl_category_items');

        $this->datatables->where('tbl_category_items.parent_id', 0);

        if (!$this->perEditCategory) {
            $edit = '';
        } else {
            $edit = '<a class="tnh-modal btn btn-success btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/items/edit_category/$1"><i class="fa fa-pencil"></i></a>';
        }

        if (!$this->perDeleteCategory) {
            $delete = '';
        } else {
            $delete = '<button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="bottom" data-content="
                <button href=\'' . base_url('admin/items/delete_category/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove"></i></button>';
        }

        $this->datatables->add_column('actions', '
            <div>
                ' . $edit . '
                ' . $delete . '
            </div>
        ', 'id');
        $result = json_decode($this->datatables->generate());
        foreach ($result->aaData as $key => $value) {
            $id = $value[0];
            $output = null;
            $result->aaData[$key][8] = $this->recursiveTableCategoryItems($output, $id);
        }
        echo (json_encode($result));
    }

    function recursiveTableCategoryItems(&$output = null, $parent_id = 0, $indent = null, $stt = 1)
    {

        $this->db->select('*');
        $this->db->from('tbl_category_items');
        $this->db->where('tbl_category_items.parent_id', $parent_id);
        $this->db->order_by('tbl_category_items.parent_id');
        $query = $this->db->get()->result_array();

        foreach ($query as $key => $item) {
            if ($item['parent_id'] == $parent_id) {
                $output .= '<tr>
                    <td>' . $indent . '' . $item['code'] . '</td>
                    <td>' . $item['name'] . '</td>
                    <td>
                        <div class="onoffswitch">
                            <input type="checkbox" data-switch-url="' . base_url('admin/items/change_recipe') . '" name="onoffswitch" class="onoffswitch-checkbox" id="c_single_use_' . $item['id'] . '" data-id="' . $item['id'] . '" ' . ($item['recipe'] == 1 ? 'checked' : '') . '>
                            <label class="onoffswitch-label" for="c_single_use_' . $item['id'] . '"></label>
                        </div>
                    </td>
                    <td>' . $item['note'] . '</td>
                    <td>
                        <div>
                        <a class="tnh-modal btn btn-success btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/items/edit_category/' . $item['id'] . '"><i class="fa fa-pencil"></i></a>
                        <button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="bottom" data-content="
                        <button href=\'' . base_url('admin/items/delete_category/' . $item['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                        <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
                        "><i class="fa fa-remove"></i></button>
                        </div>
                    </td>
                </tr>';
                $this->recursiveTableCategoryItems($output, $item['id'], $indent . "|---", ++$stt);
            }
        }

        return $output;
    }

    function delete_category($id)
    {
        if (!has_permission('items_category', '', 'delete')) {
            accessDenied($js = true);
        }
        $data = [];
        if ($id) {
            $row = $this->items_model->rowCategoryItems($id);
            if ($this->items_model->checkExistCategory($id)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_exist_not_delete');
                echo json_encode($data);
                return;
            }
            if ($this->items_model->checkParentId($id)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_please_remove_sub_items');
                echo json_encode($data);
                die;
            }
            if ($this->items_model->deleteCategoryItems($id)) {
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

    function delete_category_multiple()
    {
        if (!has_permission('items_category', '', 'delete')) {
            accessDenied($js = true);
        }
        $data = [];
        if ($this->input->post()) {
            if (!$this->input->post('category_id')) {
                $data['result'] = 0;
                $data['message'] = lang('no_data_exists');
                echo json_encode($data);
                return;
            }

            $errors = '';
            $count = 0;
            foreach ($this->input->post('category_id') as $key => $id) {
                if ($this->items_model->checkExistCategory($id)) {
                    $row = $this->items_model->rowCategoryItems($id);
                    $errors .= '<div class="text-danger">' . $row['code'] . ' ' . lang('tnh_exist_not_delete') . '</div>';
                    continue;
                }
                if ($this->items_model->checkParentId($id)) {
                    $row = $this->items_model->rowCategoryItems($id);
                    $errors .= '<div class="text-danger">' . $row['code'] . ' ' . lang('tnh_please_remove_sub_items') . '</div>';
                    continue;
                }
                if ($this->items_model->deleteCategoryItems($id)) {
                    $count++;
                }
            }
            if ($count) {
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
            $data['errors'] = $errors;
            echo json_encode($data);
            return;
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    function searchCategory()
    {
        $data = [];
        if ($this->input->get()) {
            $q = $this->input->get('q');
            $limit = 50;
            $data = $this->items_model->searchCategory($q, $limit);
        }
        echo json_encode($data);
    }

    function searchMaterials()
    {
        $data = [];
        if ($this->input->get()) {
            $q = $this->input->get('q');
            $limit = 50;
            $data = $this->items_model->searchMaterials($q, $limit);
        }
        echo json_encode($data);
    }

    function export_excel_category()
    {
        if (!has_permission('items_category', '', 'excel')) {
            accessDenied($js = true);
        }
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');

            // print_arrays($this->input->post());
            $cloumns = $this->input->post('cloumns');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();

            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
            $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

            $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

            $this->db->select(implode(',', $cloumns), false);
            $this->db->from('tbl_category_items');
            $data = $this->db->get()->result_array();

            foreach ($cloumns as $key => $value) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$key])->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->SetCellValue($cloumns_excel[$key] . '1', _l('tnh_' . $value))->getStyle($cloumns_excel[$key] . '1')->applyFromArray($style_excel['Background_header']);
            }

            $row = 2;
            foreach ($data as $key => $value) {
                foreach ($cloumns as $k => $val) {
                    $index = $cloumns_excel[$k] . $row;
                    $el = $value[$val];
                    $objPHPExcel->getActiveSheet()->SetCellValue($index, $el)->getStyle($index)->applyFromArray($style_excel['BStyle']);
                }
                $row++;
            }

            $filename = lang('tnh_item_materials_category') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');

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
                'filename' => $filename,
                'message' => lang('success'),
                'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
            );
            die(json_encode($response));
        } else {
            $data['link'] = 'admin/items/export_excel_category';
            $list = [];
            $fields = get_fields_export($table = 'tbl_category_items', $arr_diff = false);
            foreach ($fields as $key => $value) {
                $list[] = [$value => mb_strtoupper(_l('tnh_' . $value), 'UTF-8')];
            }
            $data['list'] = $list;
            $this->load->view('admin/export_excel/export_excel', $data);
        }
    }

    function export_excel_items()
    {
        if (!has_permission('items', '', 'excel')) {
            accessDenied($js = true);
        }
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            $limit_start = $this->input->post('limit_start');
            $limit_end = $this->input->post('limit_end');

            // print_arrays($this->input->post());
            $cloumns = $this->input->post('cloumns');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();

            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
            $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

            $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

            // $this->db->select(implode(',', $cloumns), false);
            // $this->db->from('tbl_category_items');
            // $data = $this->db->get()->result_array();

            $select = '';
            $left_join = '';

            // print_arrays($this->custom_fields);
            foreach ($cloumns as $key => $value) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$key])->setAutoSize(true);

                //custom fields
                $custom = false;
                if (!empty($this->custom_fields)) {
                    foreach ($this->custom_fields as $k => $val) {
                        if ($value == ('custom_fields_' . $val['fieldto'] . '_' . $val['id'])) {

                            $objPHPExcel->getActiveSheet()->SetCellValue($cloumns_excel[$key] . '1', $val['name'])->getStyle($cloumns_excel[$key] . '1')->applyFromArray($style_excel['Background_header']);


                            $value = $val['slug'];
                            $cloumns[$key] = $value;
                            $select .= "COALESCE((
                                            SELECT tblcustomfieldsvalues.value
                                            FROM tblcustomfieldsvalues
                                            WHERE tblcustomfieldsvalues.fieldto = 'materials' AND tblcustomfieldsvalues.relid = tbl_materials.id AND tblcustomfieldsvalues.fieldid = " . $val['id'] . "
                                        ), '') as $value, ";
                            $custom = true;
                            break;
                        }
                    }
                }
                if ($custom == true) continue;
                //end custom fields

                if ($value == "unit_id") {
                    $objPHPExcel->getActiveSheet()->SetCellValue($cloumns_excel[$key] . '1', 'Đơn vị chuẩn')->getStyle($cloumns_excel[$key] . '1')->applyFromArray($style_excel['Background_header']);
                } else {
                    $objPHPExcel->getActiveSheet()->SetCellValue($cloumns_excel[$key] . '1', _l('tnh_' . $value))->getStyle($cloumns_excel[$key] . '1')->applyFromArray($style_excel['Background_header']);
                }

                if ($value == "category_id") {
                    $value = 'category_name';
                    $cloumns[$key] = $value;
                    $select .= "tbl_category_items.name as $value, ";
                    $left_join .= " LEFT JOIN tbl_category_items ON tbl_category_items.id = tbl_materials.category_id";
                } else if ($value == "mode_id") {
                    $value = 'mode_name';
                    $cloumns[$key] = $value;
                    $select .= "tbl_mode_materials.name as $value, ";
                    $left_join .= " LEFT JOIN tbl_mode_materials ON tbl_mode_materials.id = tbl_materials.mode_id";
                } else if ($value == "unit_id") {
                    $value = 'unit_name';
                    $cloumns[$key] = $value;
                    $select .= "tblunits.unit as $value, ";
                    $left_join .= " LEFT JOIN tblunits ON tblunits.unitid = tbl_materials.unit_id";
                } else if ($value == "standard_unit") {
                    $value = 'standard_unit';
                    $cloumns[$key] = $value;
                    $select .= "tb_standard_unit.unit as $value, ";
                    $left_join .= " LEFT JOIN tblunits tb_standard_unit ON tb_standard_unit.unitid = tbl_materials.standard_unit";
                } else if ($value == "unit_payment") {
                    $value = 'unit_payment';
                    $cloumns[$key] = $value;
                    $select .= "tb_unit_payment.unit as $value, ";
                    $left_join .= " LEFT JOIN tblunits tb_unit_payment ON tb_unit_payment.unitid = tbl_materials.unit_payment";
                } else if ($value == "exchange") {
                    $exchange_items = "(
                        SELECT
                            tbl_exchange_items.item_id,
                            GROUP_CONCAT(CONCAT(tblunits.unit, '->', tbl_exchange_items.number_exchange, '') SEPARATOR '\n') as ex
                        FROM tbl_exchange_items
                        LEFT JOIN tblunits ON tblunits.unitid = tbl_exchange_items.unit_id
                        GROUP BY tbl_exchange_items.item_id
                    ) as exchange_items";

                    $value = 'ex';
                    $cloumns[$key] = $value;
                    $select .= "exchange_items.ex as $value, ";
                    $left_join .= " LEFT JOIN $exchange_items ON exchange_items.item_id = tbl_materials.id";
                } else if ($value == "exchange_quantity_manufactures") {
                    $exchange_items = "(
                        SELECT
                            tbl_exchange_items.item_id,
                            GROUP_CONCAT(tbl_exchange_items.number_exchange SEPARATOR '\n') as ex_number
                        FROM tbl_exchange_items
                        LEFT JOIN tblunits ON tblunits.unitid = tbl_exchange_items.unit_id
                        GROUP BY tbl_exchange_items.item_id
                    ) as exchange_items_number";

                    $value = 'ex_number';
                    $cloumns[$key] = $value;
                    $select .= "exchange_items_number.ex_number as $value, ";
                    $left_join .= " LEFT JOIN $exchange_items ON exchange_items_number.item_id = tbl_materials.id";
                } else if ($value == "exchange_unit_manufactures") {
                    $exchange_items = "(
                        SELECT
                            tbl_exchange_items.item_id,
                            GROUP_CONCAT(tblunits.unit SEPARATOR '\n') as ex_unit
                        FROM tbl_exchange_items
                        LEFT JOIN tblunits ON tblunits.unitid = tbl_exchange_items.unit_id
                        GROUP BY tbl_exchange_items.item_id
                    ) as exchange_items_unit";

                    $value = 'ex_unit';
                    $cloumns[$key] = $value;
                    $select .= "exchange_items_unit.ex_unit as $value, ";
                    $left_join .= " LEFT JOIN $exchange_items ON exchange_items_unit.item_id = tbl_materials.id";
                } else if ($value == "species") {
                    $value = 'species';
                    $cloumns[$key] = $value;
                    $select .= "tbl_species.name as $value, ";
                    $left_join .= " LEFT JOIN tbl_species ON tbl_species.id = tbl_materials.species";
                } else if ($value == "suppliers") {
                    $value = 'suppliers';
                    $cloumns[$key] = $value;
                    $select .= "tblsuppliers.company as $value, ";
                    $left_join .= " LEFT JOIN tblsuppliers ON tblsuppliers.id = tbl_materials.suppliers";
                } else if ($value == "id_branch") {
                    $value = 'branch_name';
                    $cloumns[$key] = $value;
                    $select .= "tblbranch.name as $value, ";
                    $left_join .= " LEFT JOIN tblbranch ON tblbranch.id = tbl_materials.id_branch";
                } else {
                    $select .= "tbl_materials.$value, ";
                }
            }
            // print_arrays($cloumns);

            $select = trim($select);
            $select = substr($select, 0, -1);

            $limit = '';
            if (is_numeric($limit_start) && is_numeric($limit_end)) {
                $limit = ' LIMIT ' . ($limit_start) . ', ' . ($limit_end - $limit_start + 1);
            }

            $query = "
                SELECT $select
                FROM tbl_materials
                $left_join
                $limit
            ";
            $data = $this->db->query($query)->result_array();
            $row = 2;
            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    foreach ($cloumns as $k => $val) {
                        $index = $cloumns_excel[$k] . $row;
                        $el = $value[$val];
                        if ($val == "price_import" || $val == "price_sell") {
                            $objPHPExcel->getActiveSheet()->SetCellValue($index, $el)->getStyle($index)->applyFromArray($style_excel['BStyle']);
                            $objPHPExcel->getActiveSheet()->getStyle($index)->getNumberFormat()->setFormatCode('#,##0.00');
                        } else if ($val == "images") {
                            $objPHPExcel->getActiveSheet()->SetCellValue($index, !empty($el) ? base_url('uploads/materials/') . '' . $el : '')->getStyle($index)->applyFromArray($style_excel['BStyle']);
                        } else {
                            $objPHPExcel->getActiveSheet()->SetCellValue($index, $el)->getStyle($index)->applyFromArray($style_excel['BStyle']);
                        }
                    }
                    $row++;
                }
            }

            $filename = lang('materials') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');

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
                'filename' => $filename,
                'message' => lang('success'),
                'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
            );
            die(json_encode($response));
        } else {
            $data['link'] = 'admin/items/export_excel_items';
            $list = [];
            $fields = get_fields_export(
                $table = 'tbl_materials',
                $arr_diff = [
                    'quantity_begin',
                    'mode',
                ],
                $arr_more = [
                    'exchange_quantity_manufactures',
                    'exchange_unit_manufactures',
                ]
            );
            foreach ($fields as $key => $value) {
                if ($value == 'unit_id') {
                    $list[] = [$value => mb_strtoupper(_l('tnh_unit_stock'), 'UTF-8')];
                } else if ($value == 'exchange_quantity_manufactures') {
                    $list[] = [$value => mb_strtoupper(_l('Quy đổi sản xuất'), 'UTF-8')];
                } else if ($value == 'exchange_unit_manufactures') {
                    $list[] = [$value => mb_strtoupper(_l('Đơn vị sản xuất'), 'UTF-8')];
                } else {
                    $list[] = [$value => mb_strtoupper(_l('tnh_' . $value), 'UTF-8')];
                }
            }
            foreach ($this->custom_fields as $key => $value) {
                $list[] = ['custom_fields_' . $value['fieldto'] . '_' . $value['id'] => mb_strtoupper($value['name'], 'UTF-8')];
            }
            $data['list'] = $list;
            $this->load->view('admin/export_excel/export_excel', $data);
        }
    }

    public function import_category()
    {
        if (!has_permission('items_category', '', 'create')) {
            accessDenied();
        }
        if ($this->input->post('save')) {
            $data = [];
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');

            $fullfile = $_FILES['file']['tmp_name'];
            if (empty($fullfile)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data);
                return;
            }
            $extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if ($extension != 'XLSX' && $extension != 'XLS') {
                $data['result'] = 0;
                $data['message'] = lang('tnh_not_format_excel');
                echo json_encode($data);
                return;
            }

            if (empty($fullfile)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data);
                return;
            }

            if (!$this->input->post('fields')) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_fields_not_required');
                echo json_encode($data);
                return;
            }
            // if ($_FILES['userfile']['size'] > $this->allowed_file_size * 1024) {
            //     $this->session->set_flashdata('warning', lang('Không vượt quá '. $this->allowed_file_size. ' size'));
            //     redirect($_SERVER["HTTP_REFERER"]);
            //     return;
            // }
            $inputFileType  = PHPExcel_IOFactory::identify($fullfile);
            $objReader      = PHPExcel_IOFactory::createReader($inputFileType);
            $objReader->setReadDataOnly(true);

            /**  Load $inputFileName to a PHPExcel Object  **/
            $objPHPExcel = $objReader->load("$fullfile");

            $total_sheets = $objPHPExcel->getSheetCount();

            $allSheetName       = $objPHPExcel->getSheetNames();
            $objWorksheet       = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow         = $objWorksheet->getHighestRow();
            $highestColumn      = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            $arraydata          = array();

            $row_start = $this->input->post('row_start') ? $this->input->post('row_start') : 2;
            $row_end = $this->input->post('row_end') ? $this->input->post('row_end') : $highestRow;
            $cloumn_excel = $this->input->post('cloumn_excel');
            $fields = $this->input->post('fields');
            for ($row = $row_start; $row <= $row_end; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    // $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    if (!empty($cloumn_excel[$col])) {
                        $cloumn_current = $cloumn_excel[$col];
                        $index_current = $fields[$col];
                    } else {
                        continue;
                    }
                    $value = $objWorksheet->getCell($cloumn_current . $row)->getValue();
                    $arraydata[$row][$index_current] = $value;
                }
            }
            $options = [];
            $count = 0;
            $errors = '';
            foreach ($arraydata as $key => $value) {
                $parent = !empty($value['parent_id']) ? trim($value['parent_id']) : '';
                $code = !empty($value['code']) ? trim($value['code']) : '';
                $name = !empty($value['name']) ? trim($value['name']) : '';
                $note = !empty($value['note']) ? trim($value['note']) : '';
                if (empty($code) || empty($name)) {
                    continue;
                }

                $parent_id = 0;
                if (!empty($parent)) {
                    $row_parent = $this->items_model->rowCategoryItemsByCode($parent, 'id', 'where');
                    if (!empty($row_parent)) {
                        $parent_id = $row_parent['id'];
                    } else {
                        $errors .= '<div class="text-danger">' . $parent . ' ' . lang('not_data_exists') . '</div>';
                        continue;
                    }
                }

                $options = [
                    'code' => $code,
                    'name' => $name,
                    'note' => $note,
                    'parent_id' => $parent_id
                ];
                if ($this->items_model->checkCategoryItemsByCode($code)) {
                    $errors .= '<div class="text-danger">' . $code . ' ' . lang('tnh_exist_data') . '</div>';
                    continue;
                }
                $id = $this->items_model->insertCategoryItems($options);
                if ($id) {
                    $count++;
                }
            }
            if ($count) {
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
            $data['errors'] = $errors;
            echo json_encode($data);
            // print_arrays($arraydata, $this->input->post());
        } else {
            $data['tnh'] = true;
            $data['title'] = _l('tnh_import_excel');
            $list = [];
            $fields = get_fields_export($table = 'tbl_category_items', $arr_diff = ['id']);
            foreach ($fields as $key => $value) {
                $list[$value] = mb_strtoupper(lang('tnh_' . $value), 'UTF-8');
            }
            $required = [lang('tnh_name'), lang('tnh_code')];
            $data['list'] = $list;
            $data['required'] = $required;
            $this->load->view('admin/items/import_category', $data);
        }
    }

    public function import_items()
    {
        if (!has_permission('items', '', 'create')) {
            accessDenied();
        }
        if ($this->input->post('save')) {
            ini_set('max_execution_time', 600);
            $data = [];
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');

            $fullfile = $_FILES['file']['tmp_name'];
            if (empty($fullfile)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data);
                return;
            }
            $extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if ($extension != 'XLSX' && $extension != 'XLS') {
                $data['result'] = 0;
                $data['message'] = lang('tnh_not_format_excel');
                echo json_encode($data);
                return;
            }

            if (empty($fullfile)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data);
                return;
            }

            if (!$this->input->post('fields')) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_fields_not_required');
                echo json_encode($data);
                return;
            }
            // if ($_FILES['userfile']['size'] > $this->allowed_file_size * 1024) {
            //     $this->session->set_flashdata('warning', lang('Không vượt quá '. $this->allowed_file_size. ' size'));
            //     redirect($_SERVER["HTTP_REFERER"]);
            //     return;
            // }
            $inputFileType  = PHPExcel_IOFactory::identify($fullfile);
            $objReader      = PHPExcel_IOFactory::createReader($inputFileType);
            $objReader->setReadDataOnly(true);

            /**  Load $inputFileName to a PHPExcel Object  **/
            $objPHPExcel = $objReader->load("$fullfile");

            $total_sheets = $objPHPExcel->getSheetCount();

            $allSheetName       = $objPHPExcel->getSheetNames();
            $objWorksheet       = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow         = $objWorksheet->getHighestRow();
            $highestColumn      = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            $arraydata          = array();

            $row_start = $this->input->post('row_start') ? $this->input->post('row_start') : 2;
            $row_end = $this->input->post('row_end') ? $this->input->post('row_end') : $highestRow;
            $cloumn_excel = $this->input->post('cloumn_excel');
            $fields = $this->input->post('fields');
            for ($row = $row_start; $row <= $row_end; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    // $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    if (!empty($cloumn_excel[$col])) {
                        $cloumn_current = $cloumn_excel[$col];
                        $index_current = $fields[$col];
                    } else {
                        continue;
                    }
                    $value = $objWorksheet->getCell($cloumn_current . $row)->getValue();
                    $arraydata[$row][$index_current] = $value;
                }
            }

            $options = [];
            $count = 0;
            $errors = '';

            //option category
            $category_id_1 = $this->input->post('category_id_1'); //where or like
            $category_id_2 = $this->input->post('category_id_2'); //add or continue
            //unit
            $unit_id_1 = $this->input->post('unit_id_1'); //where or like
            $unit_id_2 = $this->input->post('unit_id_2'); //add or continue
            //exchange
            $exchange_1 = $this->input->post('exchange_1'); //where or like
            $exchange_2 = $this->input->post('exchange_2'); //add or continue

            //species
            $species_1 = $this->input->post('species_1'); //where or like
            $species_2 = $this->input->post('species_2'); //add or continue

            //unit_payment
            $unit_payment_1 = $this->input->post('unit_payment_1'); //where or like
            $unit_payment_2 = $this->input->post('unit_payment_2'); //add or continue

            //standard_unit
            $standard_unit_1 = $this->input->post('standard_unit_1'); //where or like
            $standard_unit_2 = $this->input->post('standard_unit_2'); //add or continue

            $unit_manufactures_1 = $this->input->post('unit_manufactures_1'); //where or like
            $unit_manufactures_2 = $this->input->post('unit_manufactures_2'); //add or continue

            //unit
            $unit_of_measure_1 = $this->input->post('unit_of_measure_1'); //where or like
            $unit_of_measure_2 = $this->input->post('unit_of_measure_2'); //add or continue

            //dt
            if (!empty($this->input->post('saveImport'))) {
                $Template = [];
                $Template['setup_colums'] = [];
                foreach ($fields as $key => $value) {
                    $Template['setup_colums'][$key] = [
                        'field' => $value,
                        'rowExcel' => $cloumn_excel[$key]
                    ];
                    $check1 = $this->input->post($value . '_1');
                    if (isset($check1)) {
                        $Template['setup_colums'][$key]['type_data1'] = $check1;
                    }
                    $check2 = $this->input->post($value . '_2');
                    if (isset($check2)) {
                        $Template['setup_colums'][$key]['type_data2'] = $check2;
                    }
                }
                $Template['setup_colums'] = json_encode($Template['setup_colums']);
                $Template['create_by'] = get_staff_user_id();
                $Template['type'] = 'nvl';
                $Template['date_create'] = date('Y-m-d H:i:s');
                $this->db->insert('tbltemplate_import', $Template);
            }
            //end

            //custom fields
            // $custom_fields = [];
            // foreach ($this->custom_fields as $key => $value) {
            //     $custom_fields[$value['fieldto']][$value['id']] = false;
            // }

            // print_arrays($custom_fields, $arraydata);
            $rowT = $row_start - 1;
            $actions = $this->input->post('actions');
            if ($actions == "add") {
                foreach ($arraydata as $key => $value) {
                    $category = !empty($value['category_id']) ? trim($value['category_id']) : '';
                    $code = !empty($value['code']) ? trim($value['code']) : '';
                    $name = !empty($value['name']) ? trim($value['name']) : '';
                    $price_import = !empty($value['price_import']) ? number_unformat($value['price_import']) : 0;
                    $price_sell = !empty($value['price_sell']) ? number_unformat($value['price_sell']) : 0;
                    $unit = !empty($value['unit_id']) ? trim($value['unit_id']) : '';
                    $note = !empty($value['note']) ? trim($value['note']) : '';
                    $exchange = !empty($value['exchange']) ? trim($value['exchange']) : '';

                    $name_customer = !empty($value['name_customer']) ? trim($value['name_customer']) : '';
                    $name_supplier = !empty($value['name_supplier']) ? trim($value['name_supplier']) : '';
                    $quantity_begin = !empty($value['quantity_begin']) ? trim($value['quantity_begin']) : '';
                    $quantity_minimum = !empty($value['quantity_minimum']) ? trim($value['quantity_minimum']) : '';

                    $quality_standard = !empty($value['quality_standard']) ? trim($value['quality_standard']) : '';
                    $mode = !empty($value['mode']) ? trim($value['mode']) : '';
                    $quantitative = !empty($value['quantitative']) ? number_unformat($value['quantitative']) : '';

                    $hand_input_code = 1;
                    $species = !empty($value['species']) ? trim($value['species']) : '';
                    $material_code_supplier = !empty($value['material_code_supplier']) ? trim($value['material_code_supplier']) : '';
                    $paper = !empty($value['paper']) ? number_unformat($value['paper']) : '';
                    $standard_cl = !empty($value['standard_cl']) ? trim($value['standard_cl']) : '';
                    $time_payment = !empty($value['time_payment']) ? number_unformat($value['time_payment']) : '';
                    $pp_check = !empty($value['pp_check']) ? trim($value['pp_check']) : '';
                    $name_account = !empty($value['name_account']) ? trim($value['name_account']) : '';
                    $height = !empty($value['height']) ? number_unformat($value['height']) : '';
                    $time_stock = !empty($value['time_stock']) ? number_unformat($value['time_stock']) : '';
                    $standard_unit = !empty($value['standard_unit']) ? trim($value['standard_unit']) : '';
                    $unit_payment = !empty($value['unit_payment']) ? trim($value['unit_payment']) : '';
                    $suppliers = !empty($value['suppliers']) ? trim($value['suppliers']) : '';
                    $longs = !empty($value['longs']) ? number_unformat($value['longs']) : '';
                    $wide = !empty($value['wide']) ? number_unformat($value['wide']) : '';
                    $mode_id = !empty($value['mode_id']) ? trim($value['mode_id']) : '';

                    $unit_of_measure = !empty($value['unit_of_measure']) ? trim($value['unit_of_measure']) : '';
                    $packaging_standard = !empty($value['packaging_standard']) ? trim($value['packaging_standard']) : '';
                    $npl_standard = !empty($value['npl_standard']) ? trim($value['npl_standard']) : '';

                    $exchange_unit = !empty($value['exchange_unit']) ? number_unformat($value['exchange_unit']) : 1;
                    $exchange_standard_unit = !empty($value['exchange_standard_unit']) ? number_unformat($value['exchange_standard_unit']) : 1;
                    $exchange_unit_payment = !empty($value['exchange_unit_payment']) ? number_unformat($value['exchange_unit_payment']) : 1;
                    $is_single_use = (!empty($value['is_single_use']) && $value['is_single_use'] == 1) ? 1 : 0;
                    $recipe = !empty($value['recipe']) ? number_unformat($value['recipe']) : 1;

                    $id_branch = !empty($value['id_branch']) ? ($value['id_branch']) : 0;

                    $unit_manufactures = !empty($value['unit_manufactures']) ? $value['unit_manufactures'] : '';
                    $exchange_manufactures = !empty($value['exchange_manufactures']) ? number_unformat($value['exchange_manufactures']) : 1;
                    $is_zinc = !empty($value['is_zinc']) ? 1 : 0;

                    if (empty($code) || empty($name) || empty($category) || empty($unit) || empty($standard_unit) || empty($unit_payment) || empty($id_branch)) {
                        continue;
                    }

                    //category
                    if ($category_id_1) {
                        $row_category = $this->items_model->rowCategoryItemsByCode($category, 'id', $category_id_1);
                        if (!empty($row_category)) {
                            $category_id = $row_category['id'];
                        } else if ($category_id_2 == 'add') {
                            $category_id = $this->items_model->insertCategoryItems([
                                'code' => $category,
                                'name' => $category,
                            ]);
                        } else {
                            continue;
                        }
                    }

                    //unit
                    if ($unit_id_1) {
                        $row_unit = $this->unit_model->rowUnitByCode($unit, 'unitid', $unit_id_1);
                        if (!empty($row_unit)) {
                            $unit_id = $row_unit['unitid'];
                        } else if ($unit_id_2 == 'add') {
                            $unit_id = $this->unit_model->insertUnit([
                                'unit' => $unit
                            ]);
                        } else {
                            continue;
                        }
                    }
                    //unit
                    $unit_of_measure_id = '';
                    if ($unit_of_measure_1) {
                        $row_unit = $this->unit_model->rowUnitByCode($unit_of_measure, 'unitid', $unit_of_measure_1);
                        if (!empty($row_unit)) {
                            $unit_of_measure_id = $row_unit['unitid'];
                        } else if ($unit_of_measure_2 == 'add') {
                            $unit_of_measure_id = $this->unit_model->insertUnit([
                                'unit' => $unit
                            ]);
                        } else {
                            continue;
                        }
                    }


                    //unit payment
                    $unit_payment_id = 0;
                    if ($unit_payment_1) {
                        $row_unit_payment = $this->unit_model->rowUnitByCode($unit_payment, 'unitid', $unit_payment_1);
                        if (!empty($row_unit_payment)) {
                            $unit_payment_id = $row_unit_payment['unitid'];
                        } else if ($unit_payment_2 == 'add') {
                            $unit_payment_id = $this->unit_model->insertUnit([
                                'unit' => $unit_payment
                            ]);
                        } else {
                            continue;
                        }
                    }

                    //standard unit
                    $standard_unit_id = 0;
                    if ($standard_unit_1) {
                        $row_standard_unit = $this->unit_model->rowUnitByCode($standard_unit, 'unitid', $standard_unit_1);
                        if (!empty($row_standard_unit)) {
                            $standard_unit_id = $row_standard_unit['unitid'];
                        } else if ($standard_unit_2 == 'add') {
                            $standard_unit_id = $this->unit_model->insertUnit([
                                'unit' => $standard_unit
                            ]);
                        } else {
                            continue;
                        }
                    }

                    //species
                    $species_id = 0;
                    if ($species_1) {
                        $row_species = $this->species_model->rowSpeciesByCode($species, 'id', $species_1);
                        if (!empty($row_species)) {
                            $species_id = $row_species['id'];
                        } else if ($species_2 == 'add') {
                            $species_id = $this->species_model->insertSpecies([
                                'code' => $species,
                                'name' => $species,
                            ]);
                        } else {
                            continue;
                        }
                    }

                    $suppliers_id = 0;
                    if (!empty($suppliers)) {
                        $this->db->select('tblsuppliers.id');
                        $this->db->from('tblsuppliers');
                        $this->db->group_start();
                        $this->db->like('tblsuppliers.code', $suppliers);
                        $this->db->or_like('CONCAT(tblsuppliers.prefix, tblsuppliers.code)', $suppliers, false);
                        $this->db->or_like('CONCAT(tblsuppliers.prefix,"-",tblsuppliers.code)', $suppliers, false);
                        $this->db->or_like('tblsuppliers.company', $suppliers, false);
                        $this->db->group_end();
                        $dtSupplier = $this->db->get()->row_array();
                        if (empty($dtSupplier)) {
                            $errors .= '<div class="text-danger">NCC ' . $suppliers . ' không có trong phần mềm</div>';
                            continue;
                        }
                        $suppliers_id = $dtSupplier['id'];
                    }

                    $mode_id_save = 0;
                    if (!empty($mode_id)) {
                        $dtMode = $this->category_model->rowModeMaterialsByCode($mode_id);
                        if (empty($dtMode)) {
                            $errors .= '<div class="text-danger">Quy cách NPL ' . $mode_id . ' không có trong phần mềm</div>';
                            continue;
                        }
                        $mode_id_save = $dtMode['id'];
                    }


                    //exchange
                    $arr_ex = [];
                    if (!empty($exchange)) {
                        $exchange = explode('//', $exchange);
                        foreach ($exchange as $k => $val) {
                            if (empty($val)) continue;
                            if ($k != 0) continue;
                            $info_ex = explode('=>', $val);
                            $number_exchange = !empty($info_ex[0]) ? number_unformat($info_ex[0]) : 0;
                            $unit_exchange = !empty($info_ex[1]) ? trim($info_ex[1]) : 0;
                            $row_unit_exchange = $this->unit_model->rowUnitByCode($unit_exchange, 'unitid', $exchange_1);
                            if (!empty($row_unit_exchange)) {
                                $unit_exchange_id = $row_unit_exchange['unitid'];
                            } else if ($exchange_2 == 'add') {
                                $unit_exchange_id = $this->unit_model->insertUnit([
                                    'unit' => $unit_exchange
                                ]);
                            } else {
                                continue;
                            }
                            $arr_ex[$k]['unit_id'] = $unit_exchange_id;
                            $arr_ex[$k]['number_exchange'] = $number_exchange;
                        }
                    }

                    if (!empty($unit_manufactures)) {
                        $row_unit_manufactures = $this->unit_model->rowUnitByCode($unit, 'unitid', $unit_manufactures_1);
                        if (!empty($row_unit_manufactures)) {
                            $unit_manufactures_id = $row_unit_manufactures['unitid'];
                        } else if ($unit_manufactures_2 == 'add') {
                            $unit_manufactures_id = $this->unit_model->insertUnit([
                                'unit' => $unit_manufactures
                            ]);
                        } else {
                            continue;
                        }

                        $arr_ex[0]['unit_id'] = $unit_manufactures_id;
                        $arr_ex[0]['number_exchange'] = $exchange_manufactures;
                    }

                    if (empty($arr_ex)) {
                        $arr_ex[0]['unit_id'] = $unit_id;
                        $arr_ex[0]['number_exchange'] = 1;
                    }

                    //custom fields
                    $custom_fields = [];
                    foreach ($this->custom_fields as $k => $val) {
                        if (!empty($value['custom_fields_' . $val['fieldto'] . '_' . $val['id']])) {
                            $custom_fields[$val['fieldto']][$val['id']] = $value['custom_fields_' . $val['fieldto'] . '_' . $val['id']];
                        }
                    }
                    // print_arrays($custom_fields);

                    $_id_branch = 0;
                    if (!empty($id_branch)) {
                        $this->db->select('tblbranch.id');
                        $this->db->from('tblbranch');
                        $this->db->group_start();
                        $this->db->like('tblbranch.name', $id_branch);
                        $this->db->group_end();
                        $dtBranch = $this->db->get()->row_array();
                        if (empty($dtBranch)) {
                            $errors .= '<div class="text-danger">Chi nhánh xưởng ' . $id_branch . ' không có trong phần mềm</div>';
                            continue;
                        }
                        $_id_branch = $dtBranch['id'];
                    }

                    $options = [
                        'category_id' => $category_id,
                        'name' => $name,
                        'code' => $code,
                        'name_customer' => $name_customer,
                        'name_supplier' => $name_supplier,
                        'quantity_begin' => 0,
                        'price_import' => $price_import,
                        'price_sell' => $price_sell,
                        'quantity_minimum' => $quantity_minimum,
                        'unit_id' => $unit_id,
                        'note' => $note,
                        'date_created' => date('Y-m-d H:i:s'),
                        'created_by' => get_staff_user_id(),

                        'quality_standard' => $quality_standard,
                        'mode' => $mode,
                        'quantitative' => $quantitative,

                        'hand_input_code' => $hand_input_code,
                        'species' => $species_id,
                        'material_code_supplier' => $material_code_supplier,
                        'paper' => $paper,
                        'standard_cl' => $standard_cl,
                        'time_payment' => $time_payment,
                        'pp_check' => $pp_check,
                        'name_account' => $name_account,
                        'height' => $height,
                        'time_stock' => $time_stock,
                        'standard_unit' => $standard_unit_id,
                        'unit_payment' => $unit_payment_id,
                        'suppliers' => $suppliers_id,
                        'longs' => $longs,
                        'wide' => $wide,
                        'exchange_unit' => $exchange_unit,
                        'exchange_standard_unit' => $exchange_standard_unit,
                        'exchange_unit_payment' => $exchange_unit_payment,
                        'is_single_use' => $is_single_use,
                        'recipe' => $recipe,
                        'mode_id' => $mode_id_save,
                        'id_branch' => $_id_branch,
                        'is_zinc' => $is_zinc,
                        'unit_of_measure' => $unit_of_measure_id,
                        'packaging_standard' => $packaging_standard,
                        'npl_standard' => $npl_standard
                    ];
                    //check exist
                    if ($this->items_model->checkMaterialsByCode($code)) {
                        $errors .= '<div class="text-danger">' . $code . ' ' . lang('tnh_exist_data') . '</div>';
                        continue;
                    }
                    $id = $this->items_model->insertMaterials($options);
                    if ($id) {
                        $count++;
                        if (!empty($arr_ex)) {
                            foreach ($arr_ex as $key => $value) {
                                $arr_ex[$key]['item_id'] = $id;
                            }
                            $this->items_model->insertExchangeItems($arr_ex);
                        }
                        if (!empty($custom_fields)) {
                            handle_custom_fields_post($id, $custom_fields);
                        }
                    }
                }
            } elseif ($actions == "updated") {
                foreach ($arraydata as $key => $value) {
                    $code = !empty($value['code']) ? trim($value['code']) : '';
                    $dtMaterial = $this->items_model->rowMaterialsByCode($code);
                    ++$rowT;
                    $options = [];

                    if (empty($dtMaterial)) {
                        $errors .= '<div class="text-danger">Mã [' . $code . '] chưa tồn tại trong phần mềm dòng [' . ($rowT) . ']</div>';
                        continue;
                    }

                    $id = $dtMaterial['id'];

                    if (isset($value['category_id'])) {
                        $category = !empty($value['category_id']) ? trim($value['category_id']) : '';
                        $row_category = $this->items_model->rowCategoryItemsByCode($category, 'id', $category_id_1);
                        if (!empty($row_category)) {
                            $category_id = $row_category['id'];
                        } else if ($category_id_2 == 'add') {
                            $category_id = $this->items_model->insertCategoryItems([
                                'code' => $category,
                                'name' => $category,
                            ]);
                        } else {
                            $errors .= '<div class="text-danger">Mã nhóm NPL ' . $category . ' không có trong phần mềm dòng [' . ($rowT) . ']</div>';
                            continue;
                        }

                        $options['category_id'] = $category_id;
                    }

                    //
                    if (isset($value['name'])) {
                        $name = !empty($value['name']) ? trim($value['name']) : '';
                        $options['name'] = $name;
                    }

                    if (isset($value['price_import'])) {
                        $price_import = !empty($value['price_import']) ? number_unformat($value['price_import']) : '';
                        $options['price_import'] = $price_import;
                    }

                    if (isset($value['price_sell'])) {
                        $price_sell = !empty($value['price_sell']) ? number_unformat($value['price_sell']) : '';
                        $options['price_sell'] = $price_sell;
                    }

                    if (isset($value['is_zinc'])) {
                        $is_zinc = !empty($value['is_zinc']) ? 1 : 0;
                        $options['is_zinc'] = $is_zinc;
                    }

                    if (isset($value['unit_id'])) {
                        $unit = !empty($value['unit_id']) ? trim($value['unit_id']) : '';
                        $row_unit = $this->unit_model->rowUnitByCode($unit, 'unitid', $unit_id_1);
                        if (!empty($row_unit)) {
                            $unit_id = $row_unit['unitid'];
                        } else if ($unit_id_2 == 'add') {
                            $unit_id = $this->unit_model->insertUnit([
                                'unit' => $unit
                            ]);
                        } else {
                            $errors .= '<div class="text-danger">Đơn vị chuẩn ' . $unit . ' không có trong phần mềm dòng [' . ($rowT) . ']</div>';
                            continue;
                        }

                        $options['unit_id'] = $unit_id;
                    }

                    if (isset($value['note'])) {
                        $note = !empty($value['note']) ? trim($value['note']) : '';
                        $options['note'] = $note;
                    }

                    if (isset($value['name_customer'])) {
                        $name_customer = !empty($value['name_customer']) ? trim($value['name_customer']) : '';
                        $options['name_customer'] = $name_customer;
                    }

                    if (isset($value['name_supplier'])) {
                        $name_supplier = !empty($value['name_supplier']) ? trim($value['name_supplier']) : '';
                        $options['name_supplier'] = $name_supplier;
                    }

                    if (isset($value['quantity_minimum'])) {
                        $quantity_minimum = !empty($value['quantity_minimum']) ? number_unformat($value['quantity_minimum']) : '';
                        $options['quantity_minimum'] = $quantity_minimum;
                    }

                    if (isset($value['quality_standard'])) {
                        $quality_standard = !empty($value['quality_standard']) ? trim($value['quality_standard']) : '';
                        $options['quality_standard'] = $quality_standard;
                    }

                    if (isset($value['mode'])) {
                        $mode = !empty($value['mode']) ? trim($value['mode']) : '';
                        $options['mode'] = $mode;
                    }

                    if (isset($value['quantitative'])) {
                        $quantitative = !empty($value['quantitative']) ? number_unformat($value['quantitative']) : '';
                        $options['quantitative'] = $quantitative;
                    }

                    if (isset($value['species'])) {
                        $species = !empty($value['species']) ? trim($value['species']) : '';
                        $row_species = $this->species_model->rowSpeciesByCode($species, 'id', $species_1);
                        if (!empty($row_species)) {
                            $species_id = $row_species['id'];
                        } else if ($species_2 == 'add') {
                            $species_id = $this->species_model->insertSpecies([
                                'code' => $species,
                                'name' => $species,
                            ]);
                        } else {
                            $errors .= '<div class="text-danger">Chủng loại ' . $species . ' không có trong phần mềm dòng [' . ($rowT) . ']</div>';
                            continue;
                        }

                        $options['species'] = $species_id;
                    }

                    if (isset($value['material_code_supplier'])) {
                        $material_code_supplier = !empty($value['material_code_supplier']) ? trim($value['material_code_supplier']) : '';
                        $options['material_code_supplier'] = $material_code_supplier;
                    }

                    if (isset($value['paper'])) {
                        $paper = !empty($value['paper']) ? number_unformat($value['paper']) : '';
                        $options['paper'] = $paper;
                    }

                    if (isset($value['standard_cl'])) {
                        $standard_cl = !empty($value['standard_cl']) ? trim($value['standard_cl']) : '';
                        $options['standard_cl'] = $standard_cl;
                    }

                    if (isset($value['time_payment'])) {
                        $time_payment = !empty($value['time_payment']) ? number_unformat($value['time_payment']) : '';
                        $options['time_payment'] = $time_payment;
                    }

                    if (isset($value['pp_check'])) {
                        $pp_check = !empty($value['pp_check']) ? trim($value['pp_check']) : '';
                        $options['pp_check'] = $pp_check;
                    }

                    if (isset($value['name_account'])) {
                        $name_account = !empty($value['name_account']) ? trim($value['name_account']) : '';
                        $options['name_account'] = $name_account;
                    }

                    if (isset($value['height'])) {
                        $height = !empty($value['height']) ? number_unformat($value['height']) : '';
                        $options['height'] = $height;
                    }

                    if (isset($value['time_stock'])) {
                        $time_stock = !empty($value['time_stock']) ? number_unformat($value['time_stock']) : '';
                        $options['time_stock'] = $time_stock;
                    }

                    if (isset($value['standard_unit'])) {
                        $standard_unit = !empty($value['standard_unit']) ? trim($value['standard_unit']) : '';
                        $row_standard_unit = $this->unit_model->rowUnitByCode($standard_unit, 'unitid', $standard_unit_1);
                        if (!empty($row_standard_unit)) {
                            $standard_unit_id = $row_standard_unit['unitid'];
                        } else if ($standard_unit_2 == 'add') {
                            $standard_unit_id = $this->unit_model->insertUnit([
                                'unit' => $standard_unit
                            ]);
                        } else {
                            $errors .= '<div class="text-danger">Đơn vị vào kho ' . $standard_unit . ' không có trong phần mềm dòng [' . ($rowT) . ']</div>';
                            continue;
                        }

                        $options['standard_unit'] = $standard_unit_id;
                    }

                    if (isset($value['unit_payment'])) {
                        $unit_payment = !empty($value['unit_payment']) ? trim($value['unit_payment']) : '';
                        $row_unit_payment = $this->unit_model->rowUnitByCode($unit_payment, 'unitid', $unit_payment_1);
                        if (!empty($row_unit_payment)) {
                            $unit_payment_id = $row_unit_payment['unitid'];
                        } else if ($unit_payment_2 == 'add') {
                            $unit_payment_id = $this->unit_model->insertUnit([
                                'unit' => $unit_payment
                            ]);
                        } else {
                            $errors .= '<div class="text-danger">Đơn vị thanh toán ' . $unit_payment . ' không có trong phần mềm dòng [' . ($rowT) . ']</div>';
                            continue;
                        }

                        $options['unit_payment'] = $unit_payment_id;
                    }

                    if (isset($value['suppliers'])) {
                        $suppliers = trim($value['suppliers']);
                        $this->db->select('tblsuppliers.id');
                        $this->db->from('tblsuppliers');
                        $this->db->group_start();
                        $this->db->like('tblsuppliers.code', $suppliers);
                        $this->db->or_like('CONCAT(tblsuppliers.prefix, tblsuppliers.code)', $suppliers, false);
                        $this->db->or_like('CONCAT(tblsuppliers.prefix,"-",tblsuppliers.code)', $suppliers, false);
                        $this->db->or_like('tblsuppliers.company', $suppliers, false);
                        $this->db->group_end();
                        $dtSupplier = $this->db->get()->row_array();
                        if (empty($dtSupplier)) {
                            $errors .= '<div class="text-danger">NCC ' . $suppliers . ' không có trong phần mềm [' . ($rowT) . ']</div>';
                            continue;
                        }
                        $options['suppliers'] = $dtSupplier['id'];
                    }

                    if (isset($value['longs'])) {
                        $longs = !empty($value['longs']) ? number_unformat($value['longs']) : '';
                        $options['longs'] = $longs;
                    }

                    if (isset($value['wide'])) {
                        $wide = !empty($value['wide']) ? number_unformat($value['wide']) : '';
                        $options['wide'] = $wide;
                    }

                    if (isset($value['exchange_unit'])) {
                        $exchange_unit = !empty($value['exchange_unit']) ? number_unformat($value['exchange_unit']) : 1;
                        $options['exchange_unit'] = $exchange_unit;
                    }

                    if (isset($value['exchange_standard_unit'])) {
                        $exchange_standard_unit = !empty($value['exchange_standard_unit']) ? number_unformat($value['exchange_standard_unit']) : 1;
                        $options['exchange_standard_unit'] = $exchange_standard_unit;
                    }

                    if (isset($value['exchange_unit_payment'])) {
                        $exchange_unit_payment = !empty($value['exchange_unit_payment']) ? number_unformat($value['exchange_unit_payment']) : 1;
                        $options['exchange_unit_payment'] = $exchange_unit_payment;
                    }

                    if (isset($value['is_single_use'])) {
                        $is_single_use = (!empty($value['is_single_use']) && $value['is_single_use'] == 1) ? 1 : 0;
                        $options['is_single_use'] = $is_single_use;
                    }

                    if (isset($value['recipe'])) {
                        $recipe = !empty($value['recipe']) ? number_unformat($value['recipe']) : 1;
                        $options['recipe'] = $recipe;
                    }

                    if (isset($value['packaging_standard'])) {
                        $packaging_standard = !empty($value['packaging_standard']) ? ($value['packaging_standard']) : '';
                        $options['packaging_standard'] = $packaging_standard;
                    }

                    if (isset($value['allowable'])) {
                        $allowable = !empty($value['allowable']) ? number_unformat($value['allowable']) : 0;
                        $options['allowable'] = $allowable;
                    }

                    if (isset($value['unit_manufactures'])) {
                        $unit_manufactures = !empty($value['unit_manufactures']) ? trim($value['unit_manufactures']) : '';
                        $row_unit_manufactures = $this->unit_model->rowUnitByCode($unit_manufactures, 'unitid', $unit_manufactures_1);
                        if (!empty($row_unit_manufactures)) {
                            $unit_manufactures_id = $row_unit_manufactures['unitid'];
                        } else if ($unit_manufactures_2 == 'add') {
                            $unit_manufactures_id = $this->unit_model->insertUnit([
                                'unit' => $unit_manufactures
                            ]);
                        } else {
                            $errors .= '<div class="text-danger">Đơn vị sản xuất ' . $unit_manufactures . ' không có trong phần mềm dòng [' . ($rowT) . ']</div>';
                            continue;
                        }

                        $isMaterial = $this->items_model->isMaterialInBOM('materials', $id);
                        if ($isMaterial) {
                            $errors .= '<div class="text-danger">NPL này đã sử dụng trong BOM không thể sửa đơn vị sản xuất [' . ($rowT) . ']</div>';
                            continue;
                        }

                        $this->db->select('tbl_exchange_items.*');
                        $this->db->from('tbl_exchange_items');
                        $this->db->where('tbl_exchange_items.item_id', $id);
                        $this->db->where('tbl_exchange_items.unit_id >', 0);
                        $exchange_item = $this->db->get()->row_array();
                        if (!empty($exchange_item)) {
                            $count++;
                            $this->db->where('tbl_exchange_items.id', $exchange_item['id']);
                            $this->db->update('tbl_exchange_items', ['unit_id' => $unit_manufactures_id]);
                        }
                    }

                    if (isset($value['exchange_manufactures'])) {
                        $exchange_manufactures = !empty($value['exchange_manufactures']) ? number_unformat($value['exchange_manufactures']) : 1;

                        $this->db->select('tbl_exchange_items.*');
                        $this->db->from('tbl_exchange_items');
                        $this->db->where('tbl_exchange_items.item_id', $id);
                        $this->db->where('tbl_exchange_items.unit_id >', 0);
                        $exchange_item = $this->db->get()->row_array();
                        if (!empty($exchange_item)) {
                            $count++;
                            $this->db->where('tbl_exchange_items.id', $exchange_item['id']);
                            $this->db->update('tbl_exchange_items', ['number_exchange' => $exchange_manufactures]);
                        }
                    }


                    if (isset($value['mode_id'])) {
                        $mode_id = trim($value['mode_id']);
                        $dtMode = $this->category_model->rowModeMaterialsByCode($mode_id);
                        if (empty($dtMode)) {
                            $errors .= '<div class="text-danger">Quy cách NPL ' . $mode_id . ' không có trong phần mềm dòng [' . ($rowT) . ']</div>';
                            continue;
                        }
                        $mode_id_save = $dtMode['id'];
                        $options['mode_id'] = $mode_id_save;
                    }

                    if (isset($value['id_branch'])) {
                        $id_branch =  !empty($value['id_branch']) ? trim($value['id_branch']) : '';
                        $this->db->select('tblbranch.id');
                        $this->db->from('tblbranch');
                        $this->db->group_start();
                        $this->db->like('tblbranch.name', $id_branch);
                        $this->db->group_end();
                        $dtBranch = $this->db->get()->row_array();
                        if (empty($dtBranch)) {
                            $errors .= '<div class="text-danger">Chi nhánh xưởng ' . $id_branch . ' không có trong phần mềm</div>';
                            continue;
                        }
                        $_id_branch = $dtBranch['id'];
                        $options['id_branch'] = $_id_branch;
                    }

                    if (!empty($options)) {
                        $up = $this->items_model->updateMaterials($id, $options);
                        if ($up) {
                            $count++;
                        }
                    }
                }
            }

            if ($count) {
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('tnh_not_data_add');
            }
            $data['errors'] = $errors;
            echo json_encode($data);
            die;
        } else {
            $data['tnh'] = true;
            $data['title'] = _l('tnh_import_excel');
            $list = [];
            // print_arrays($this->custom_fields);
            $fields = get_fields_export($table = 'tbl_materials', $arr_diff = ['id', 'images', 'quantity_begin', 'images_multiple', 'date_created', 'created_by', 'updated_by', 'date_updated', 'hand_input_code', 'status', 'mode'], $arr_more = ['unit_manufactures', 'exchange_manufactures']);
            foreach ($fields as $key => $value) {
                if ($value == 'unit_id') {
                    $list[$value] = mb_strtoupper(lang('tnh_unit_stock'), 'UTF-8');
                } else if ($value == 'exchange') {
                    $list[$value] = mb_strtoupper(lang('Đơn vị quy đổi sản xuất'), 'UTF-8');
                } else if ($value == 'category_id') {
                    $list[$value] = mb_strtoupper(lang('Mã nhóm NPL'), 'UTF-8');
                } else if ($value == 'code') {
                    $list[$value] = mb_strtoupper(lang('Mã nguyên phụ liệu'), 'UTF-8');
                } else if ($value == 'name') {
                    $list[$value] = mb_strtoupper(lang('Tên NPL'), 'UTF-8');
                } else {
                    $list[$value] = mb_strtoupper(lang('tnh_' . $value), 'UTF-8');
                }
            }
            //custom fields
            foreach ($this->custom_fields as $key => $value) {
                $list['custom_fields_' . $value['fieldto'] . '_' . $value['id']] = $value['name'];
            }
            $required = [lang('Mã nhóm NPL'), lang('Tên NPL'), lang('Mã nguyên phụ liệu'), lang('tnh_unit_stock'), lang('tnh_standard_unit'), lang('tnh_unit_payment')];
            $data['list'] = $list;

            $data['list_auto_add'] = [
                'category_id' => mb_strtoupper(lang('Mã nhóm NPL'), 'UTF-8'),
                'species' => mb_strtoupper(lang('tnh_species'), 'UTF-8'),
                'mode_id' => mb_strtoupper(lang('tnh_mode_id'), 'UTF-8'),
                'paper' => mb_strtoupper(lang('tnh_paper'), 'UTF-8'),
                'code' => mb_strtoupper(lang('Mã nguyên phụ liệu'), 'UTF-8'),
                'name' => mb_strtoupper(lang('Tên NPL'), 'UTF-8'),
                'material_code_supplier' => mb_strtoupper(lang('tnh_material_code_supplier'), 'UTF-8'),
                'name_supplier' => mb_strtoupper(lang('tnh_name_supplier'), 'UTF-8'),
                'unit_id' => mb_strtoupper(lang('tnh_unit_id'), 'UTF-8'),
                'exchange_unit' => mb_strtoupper(lang('tnh_exchange_unit'), 'UTF-8'),
                'standard_unit' => mb_strtoupper(lang('tnh_standard_unit'), 'UTF-8'),
                'exchange_standard_unit' => mb_strtoupper(lang('tnh_exchange_standard_unit'), 'UTF-8'),
                'unit_payment' => mb_strtoupper(lang('tnh_unit_payment'), 'UTF-8'),
                'exchange_unit_payment' => mb_strtoupper(lang('tnh_exchange_unit_payment'), 'UTF-8'),
                'unit_manufactures' => mb_strtoupper(lang('tnh_unit_manufactures'), 'UTF-8'),
                'exchange_manufactures' => mb_strtoupper(lang('tnh_exchange_manufactures'), 'UTF-8'),
                'recipe' => mb_strtoupper(lang('tnh_recipe'), 'UTF-8'),
                'price_import' => mb_strtoupper(lang('tnh_price_import'), 'UTF-8'),
                'suppliers' => mb_strtoupper(lang('tnh_suppliers'), 'UTF-8'),
                'name_account' => mb_strtoupper(lang('tnh_name_account'), 'UTF-8'),
                'time_payment' => mb_strtoupper(lang('tnh_time_payment'), 'UTF-8'),
                'time_stock' => mb_strtoupper(lang('tnh_time_stock'), 'UTF-8'),
                'price_sell' => mb_strtoupper(lang('tnh_price_sell'), 'UTF-8'),
                'quantity_minimum' => mb_strtoupper(lang('tnh_quantity_minimum'), 'UTF-8'),
                'note' => mb_strtoupper(lang('tnh_note'), 'UTF-8'),
                'quality_standard' => mb_strtoupper(lang('tnh_quality_standard'), 'UTF-8'),
                'id_branch' => mb_strtoupper(lang('Chi nhánh'), 'UTF-8'),

                'unit_of_measure' => mb_strtoupper(lang('tnh_unit_of_measure'), 'UTF-8'),
                'packaging_standard' => mb_strtoupper(lang('tnh_packaging_standard'), 'UTF-8'),
                'npl_standard' => mb_strtoupper(lang('tnh_npl_standard'), 'UTF-8'),
            ];
            $data['required'] = $required;
            $this->load->view('admin/items/import_items', $data);
        }
    }

    function searchSelect2Materials($id = false)
    {
        $data = [];
        // if ($this->input->get())
        // {
        $term = $this->input->get('term');
        $limit = 50;
        $params = $this->input->get('params');
        $data['results'] = $this->items_model->searchSelect2Materials($term, $limit, $params);
        if ($id) {
            $material = $this->items_model->rowMaterial($id);
            $data['row'] = ['id' => $material['id'], 'text' => $material['name'] . '(' . $material['code'] . ')'];
        }
        // }
        echo json_encode($data);
    }

    function searchSuppliers($id = false)
    {
        $data = [];
        $term = $this->input->get('term');
        $limit = 50;
        $data['results'] = $this->site_model->searchSuppliers($term, $limit);
        if ($id) {
            $supplier = $this->site_model->rowSupplier($id);
            $data['row'] = ['id' => $supplier['id'], 'text' => $supplier['company']];
        }
        echo json_encode($data);
    }

    public function getLocationWarehouses()
    {
        $data = [];
        if ($this->input->get()) {
            $warehouse_id = $this->input->get('warehouse_id');
            $options = recursiveLocationWarehouses($warehouse_id);
            $data['options'] = '<option></option>' . $options;
        }
        echo json_encode($data);
    }

    public function updateExchange()
    {

        $ckExchange = "(
            SELECT COUNT(*)
            FROM tbl_exchange_items
            WHERE tbl_exchange_items.item_id = tbl_materials.id
        )";

        $this->db->select('tbl_materials.*');
        $this->db->from('tbl_materials');
        $this->db->where("$ckExchange = 0");
        $material = $this->db->get()->result_array();

        if (!empty($material)) {
            foreach ($material as $key => $value) {
                $exchange[$key]['item_id'] = $value['id'];
                $exchange[$key]['unit_id'] = $value['unit_id'];
                $exchange[$key]['number_exchange'] = 1;
            }
            if (!empty($exchange)) {
                $this->items_model->insertExchangeItems($exchange);
            }
        }
        // print_arrays($material);
    }

    public function change_status_material($id, $status)
    {
        if ($this->perEditItems) {
            if ($this->input->is_ajax_request()) {
                $this->items_model->updateMaterials($id, ['status' => $status, 'date_status' => date('Y-m-d H:i:s')]);
            }
        }
    }

    public function change_is_single_use($id, $status)
    {
        if ($this->perEditItems) {
            if ($this->input->is_ajax_request()) {
                $this->items_model->updateMaterials($id, ['is_single_use' => $status]);
            }
        }
    }

    public function change_recipe($id, $status)
    {
        if ($this->perEditItems) {
            if ($this->input->is_ajax_request()) {
                $this->items_model->updateCategoryItems($id, ['recipe' => $status]);
            }
        }
    }

    public function is_primary($id, $status)
    {
        if ($this->perEditItems) {
            if ($this->input->is_ajax_request()) {
                $this->items_model->updateCategoryItems($id, ['is_primary' => $status]);
            }
        }
    }
    public function is_machines($id, $status)
    {
        if ($this->perEditItems) {
            if ($this->input->is_ajax_request()) {
                $this->items_model->updateCategoryItems($id, ['is_machines' => $status]);
            }
        }
    }
    public function import_items_suppliers()
    {
        if (!$this->perAddItems) {
            accessDenied();
        }
        if ($this->input->post('save')) {
            $data = [];
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');

            $fullfile = $_FILES['file']['tmp_name'];
            if (empty($fullfile)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data);
                return;
            }
            $extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if ($extension != 'XLSX' && $extension != 'XLS') {
                $data['result'] = 0;
                $data['message'] = lang('tnh_not_format_excel');
                echo json_encode($data);
                return;
            }

            if (empty($fullfile)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data);
                return;
            }

            $inputFileType  = PHPExcel_IOFactory::identify($fullfile);
            $objReader      = PHPExcel_IOFactory::createReader($inputFileType);
            // $objReader->setReadDataOnly(true);

            /**  Load $inputFileName to a PHPExcel Object  **/
            $objPHPExcel = $objReader->load("$fullfile");

            $total_sheets = $objPHPExcel->getSheetCount();

            $allSheetName       = $objPHPExcel->getSheetNames();
            $objWorksheet       = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow         = $objWorksheet->getHighestRow();
            $highestColumn      = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('D');
            $arraydata          = array();

            for ($row = 2; $row <= $highestRow; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value      = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 2][$col] = $value;
                }
            }

            $options = [];
            $count = 0;
            $errors = '';
            $row = 1;
            foreach ($arraydata as $key => $value) {
                // 0: item_code: Mã NPL
                // 1: suppler_1: NCC 1
                // 2: suppler_2: NCC 2
                // 3: suppler_3: NCC 3

                $row++;
                $item_code = $value[0];
                $supplier_1 = $value[1];
                $supplier_2 = $value[2];
                $supplier_3 = $value[3];

                $dtItemCode = $this->items_model->rowMaterialsByCode($item_code);
                if (empty($dtItemCode)) {
                    $errors .= '<div>Dòng [' . $row . '] mặt hàng [' . $item_code . '] không tồn tại trong phần mềm</div>';
                    continue;
                }
                $item_id = $dtItemCode['id'];

                $supplier_id_1 = 0;
                if ($supplier_1) {
                    $dtSupplier1 = $this->items_model->getSuppliersByCodeName($supplier_1);
                    if (empty($dtSupplier1)) {
                        $errors .= '<div>Dòng [' . $row . '] mặt hàng [' . $item_code . '] không thêm được vì nhà cung cấp 1 [' . $supplier_1 . '] không có trong phần mềm</div>';
                        continue;
                    }
                    $supplier_id_1 = $dtSupplier1['id'];
                }

                $supplier_id_2 = 0;
                if ($supplier_2) {
                    $dtSupplier2 = $this->items_model->getSuppliersByCodeName($supplier_2);
                    if (empty($dtSupplier2)) {
                        $errors .= '<div>Dòng [' . $row . '] mặt hàng [' . $item_code . '] không thêm được vì nhà cung cấp 1 [' . $supplier_2 . '] không có trong phần mềm</div>';
                        continue;
                    }
                    $supplier_id_2 = $dtSupplier2['id'];
                }

                $supplier_id_3 = 0;
                if ($supplier_3) {
                    $dtSupplier3 = $this->items_model->getSuppliersByCodeName($supplier_3);
                    if (empty($dtSupplier3)) {
                        $errors .= '<div>Dòng [' . $row . '] mặt hàng [' . $item_code . '] không thêm được vì nhà cung cấp 1 [' . $supplier_3 . '] không có trong phần mềm</div>';
                        continue;
                    }
                    $supplier_id_3 = $dtSupplier3['id'];
                }

                $materialSuppliers = [];
                if ($supplier_id_1) {
                    $materialSuppliers[] = [
                        'material_id' => $item_id,
                        'supplier_id' => $supplier_id_1,
                        'procedure_id' => 0,
                        'sequence' => 0,
                        'number_date' => 0,
                    ];
                }

                if ($supplier_id_2) {
                    $materialSuppliers[] = [
                        'material_id' => $item_id,
                        'supplier_id' => $supplier_id_2,
                        'procedure_id' => 0,
                        'sequence' => 0,
                        'number_date' => 0,
                    ];
                }

                if ($supplier_id_3) {
                    $materialSuppliers[] = [
                        'material_id' => $item_id,
                        'supplier_id' => $supplier_id_3,
                        'procedure_id' => 0,
                        'sequence' => 0,
                        'number_date' => 0,
                    ];
                }

                if (empty($materialSuppliers)) {
                    $errors .= '<div>Dòng [' . $row . '] mặt hàng [' . $item_code . '] không thêm được vì không có nhà cung cấp để thêm</div>';
                    continue;
                }

                // print_arrays($materialSuppliers);
                $this->items_model->deleteMaterialSuppliersByMaterialId($item_id);
                $this->items_model->insertBatchMaterialSuppliers($materialSuppliers);
                $count++;
            }

            $data['errors'] = $errors;
            if ($count) {
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('tnh_not_data_add');
            }
            echo json_encode($data);
            die;
        } else {
            $data['tnh'] = true;
            $data['title'] = _l('tnh_import_items_suppliers');
            $this->load->view('admin/items/import_items_suppliers', $data);
        }
    }

    public function print_qr()
    {
        ob_start();
        $data = [];
        $product_id = $this->input->get('ids');
        $arrId = explode(',', $product_id);
        $title = lang('IN QR NGUYÊN VẬT LIỆU');
        $items = null;
        if (!empty($arrId)) {
            $this->db->select('*');
            $this->db->from('tbl_materials');
            $this->db->where_in('tbl_materials.id', $arrId);
            $items = $this->db->get()->result_array();
        }

        $data['items'] = $items;

        $content = ob_get_contents();

        $data['object'] = "materials";
        $data['hide'] = 'hide';
        $data['title'] = $title;
        $data['content'] = $content;
        ob_end_clean();
        $pdf = print_pdf_qr_dtmv($data);
        $type = 'I';
        if ($type == "S") {
            return $pdf->Output(slug_it('qr') . '.pdf', $type);
        } else {
            $pdf->Output(slug_it('qr') . '.pdf', $type);
        }
    }

    public function change_is_zinc($id, $status)
    {
        if ($this->perEditItems) {
            if ($this->input->is_ajax_request()) {
                $this->items_model->updateMaterials($id, ['is_zinc' => $status]);
            }
        }
    }
    public function exportExcelItems()
    {
        $columsExcel = [
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
            'AA',
            'AB',
            'AC',
            'AD',
            'AE',
            'AF',
            'AG',
            'AH',
            'AI',
            'AJ',
            'AK',
            'AL',
            'AM',
            'AN',
            'AO',
            'AP',
            'AQ',
            'AR',
            'AS',
            'AT',
            'AU',
            'AV',
            'AW',
            'AX',
            'AY',
            'AZ',
            'BA',
            'BB',
            'BC',
            'BD',
            'BE',
            'BF',
            'BG',
            'BH',
            'BI',
            'BJ',
            'BK',
            'BL',
            'BM',
            'BN',
            'BO',
            'BP',
            'BQ',
            'BR',
            'BS',
            'BT',
            'BU',
            'BV',
            'BW',
            'BX',
            'BY',
            'BZ',
            'CA',
            'CB',
            'CC',
            'CD',
            'CE',
            'CF',
            'CG',
            'CH',
            'CI',
            'CJ',
            'CK',
            'CL',
            'CM',
            'CN',
            'CO',
            'CP',
            'CQ',
            'CR',
            'CS',
            'CT',
            'CU',
            'CV',
            'CW',
            'CX',
            'CY',
            'CZ',
            'DA',
            'DB',
            'DC',
            'DD',
            'DE',
            'DF',
            'DG',
            'DH',
            'DI',
            'DJ',
            'DK',
            'DL',
            'DM',
            'DN',
            'DO',
            'DP',
            'DQ',
            'DR',
            'DS',
            'DT',
            'DU',
            'DV',
            'DW',
            'DX',
            'DY',
            'DZ'
        ];
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
            $this->load->library('PHPExcel');
            $inputFileName = 'uploads/import_ch/danh_sach_npl_v2.xls';
            //  Read your Excel workbook
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
                die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
            }
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestRow = $objWorksheet->getHighestRow();
            $check_key = array_search($highestColumn, $columsExcel);
            $listDataId = $this->input->post('listDataId');

            $this->db->select('
            tbl_materials.*,
            unit_exchange.unit as unit_exchange_name,
            unit_of_measure.unit as unit_of_measure_name,
            tbl_exchange_items.number_exchange as number_exchange,
            tblunits.unit as unit_name,
            payment_unit.unit as unit_name_payment,
            stock_unit.unit as unit_name_stock,
            exchange_unit,
            exchange_standard_unit,
            exchange_unit_payment,
            tbl_category_items.code as code_category,
            tbl_category_items.name as name_category,
            tbl_species.code as code_species,
            tbl_species.name as name_species,
            ');
            $this->db->from('tbl_materials');
            $this->db->join('tbl_category_items', 'tbl_category_items.id = tbl_materials.category_id', 'left');
            $this->db->join('tbl_species', 'tbl_species.id = tbl_materials.species', 'left');
            $this->db->join('tblunits', 'tblunits.unitid=tbl_materials.unit_id', 'left');
            $this->db->join('tblunits payment_unit', 'payment_unit.unitid=tbl_materials.unit_payment', 'left');
            $this->db->join('tblunits stock_unit', 'stock_unit.unitid=tbl_materials.standard_unit', 'left');
            $this->db->join('tbl_exchange_items', 'tbl_exchange_items.item_id = tbl_materials.id', 'left');
            $this->db->join('tblunits unit_exchange', 'unit_exchange.unitid=tbl_exchange_items.unit_id', 'left');

            $this->db->join('tblunits unit_of_measure', 'unit_of_measure.unitid=tbl_materials.unit_of_measure', 'left');
            if (!empty($listDataId)) {
                $this->db->where_in('tbl_materials.id', $listDataId, false);
            }

            $this->db->order_by('tbl_materials.id asc');
            $items = $this->db->get()->result_array();

            $dem = 0;
            $row = 3;
            // $exchanges = $this->items_model->getExchangeItemsViewByItemId($id);
            $this->load->library('ciqrcode');

            foreach ($items as $key => $value) {
                $row++;
                $dem++;
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[0] . $row, $dem);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, $value['code_category'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, $value['name_category'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row, $value['code_species'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[4] . $row, $value['name_species'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[5] . $row, $value['height'])->getStyle($columsExcel[6] . $row)->getNumberFormat()->setFormatCode(($value['height']));
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[6] . $row, $value['wide'])->getStyle($columsExcel[6] . $row)->getNumberFormat()->setFormatCode(($value['wide']));
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[7] . $row, $value['unit_of_measure_name'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[8] . $row, $value['code'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[9] . $row, $value['name'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[10] . $row, $value['unit_name'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[11] . $row, $value['unit_name_payment'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[12] . $row, $value['unit_name_stock'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[13] . $row, $value['unit_exchange_name'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[14] . $row, $value['number_exchange'])->getStyle($columsExcel[14] . $row)->getNumberFormat()->setFormatCode(($value['number_exchange']));
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[15] . $row, $value['packaging_standard'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[16] . $row, $value['npl_standard'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[17] . $row, $value['quantity_minimum'])->getStyle($columsExcel[17] . $row)->getNumberFormat()->setFormatCode(($value['quantity_minimum']));
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[18] . $row, $value['time_stock'])->getStyle($columsExcel[18] . $row)->getNumberFormat()->setFormatCode(($value['time_stock']));


                // $code = 'materials||'.$value['id'];
                // $qr = vn_to_str(str_replace('||', '__', $code));
                // $folder = FCPATH . 'uploads/qr_materials/';
                // if (!file_exists($folder)) {
                //     mkdir($folder);
                //     fopen($folder . 'index.html', 'w');
                // }
                // if (!file_exists($folder . 'qrcode' . '/')) {
                //     mkdir($folder . 'qrcode' . '/');
                //     fopen($folder . 'qrcode' . '/' . 'index.html', 'w');
                // }
                // $params['data'] = $code;
                // $params['level'] = 'H';
                // $params['size'] = 40;
                // $params['savename'] = $folder.'qrcode/'. $qr . '.png';
                // $this->ciqrcode->generate($params);
                // $img = ($folder.'qrcode/'. $qr . '.png');
                // if (!empty($img)) {
                //     $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                //     $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                //     $objDrawing1->setPath($img);
                //     $objDrawing1->setWidth(80);
                //     $objDrawing1->setHeight(53);
                //     $objDrawing1->setOffsetX(3);
                //     $objDrawing1->setOffsetY(2);
                //     $objDrawing1->setCoordinates($columsExcel[19]. ($row));
                // }
                // $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(42);
                // $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[19]."$row", '')->getStyle($columsExcel[19]."$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            }

            $objPHPExcel->getActiveSheet()->getStyle('A4:S' . $row)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('A4:S' . $row)->applyFromArray([
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
            $filename = lang('danh_sach_npl') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(50);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(17);
            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
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
