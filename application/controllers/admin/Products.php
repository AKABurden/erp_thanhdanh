<?php

// header('Content-Type: text/html; charset=utf-8');
defined('BASEPATH') or exit('No direct script access allowed');

class Products extends AdminController
{
    public function __construct()
    {

        parent::__construct();
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');
        $this->load->model('category_model');
        $this->load->model('departments_model');
        $this->load->model('species_model');
        $this->load->model('columns_model');
        // $this->lang->load('vietnamese/form_validation_lang');
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->allowed_file_size = '1024';
        $this->upload_path = get_upload_path_by_type('products');
        // print_arrays($this->upload_path);
        $this->datetime_now = time();
        $this->custom_fields = get_custom_fields('products');
        $this->show_table_custom_fields = get_table_custom_fields('products');

        //permissions category
        $this->perViewCategory = has_permission('products_category', '', 'view');
        $this->perAddCategory = has_permission('products_category', '', 'create');
        $this->perEditCategory = has_permission('products_category', '', 'edit');
        $this->perDeleteCategory = has_permission('products_category', '', 'delete');
        $this->perExportCategory = has_permission('products_category', '', 'export');

        //permissions products
        $this->perViewProducts = has_permission('products', '', 'view');
        $this->perAddProducts = has_permission('products', '', 'create');
        $this->perEditProducts = has_permission('products', '', 'edit');
        $this->perDeleteProducts = has_permission('products', '', 'delete');
        $this->perExportProducts = has_permission('products', '', 'export');

        //permissions products list bom
        $this->perViewProductsListBom = has_permission('products_list_bom', '', 'view');
        $this->perAddProductsListBom = has_permission('products_list_bom', '', 'create');
        $this->perEditProductsListBom = has_permission('products_list_bom', '', 'edit');
        $this->perDeleteProductsListBom = has_permission('products_list_bom', '', 'delete');

        //permissions products bom
        $this->perViewProductsBom = has_permission('products_bom', '', 'view');
        $this->perPrintProductsBom = has_permission('products_bom', '', 'print');

        $this->list_standard = [
            'standard_carry' => [
                'id' => 'standard_carry',
                'name' => 'Tiêu Chuẩn Bế Của KH',
                'id_key' => 'id_standard_carry'
            ],
            'standard_sample_cover' => [
                'id' => 'standard_sample_cover',
                'name' => 'Tiêu Chuẩn Bìa Mẫu KH Duyệt',
                'id_key' => 'id_standard_sample_cover'
            ],
            'standard_smooth_shine' => [
                'id' => 'standard_smooth_shine',
                'name' => 'Tiêu Chuẩn Bóng Của KH',
                'id_key' => 'id_standard_smooth_shine'
            ],
            'standard_fsc' => [
                'id' => 'standard_fsc',
                'name' => 'Tiêu Chuẩn FSC Của KH',
                'id_key' => 'id_standard_fsc'
            ],
            'standard_delivery_package' => [
                'id' => 'standard_delivery_package',
                'name' => 'Tiêu Chuẩn chuẩn Kiện Hàng Giao Của KH',
                'id_key' => 'id_standard_delivery_package'
            ],
            'standard_membrane' => [
                'id' => 'standard_membrane',
                'name' => 'Tiêu Chuẩn Màng Của KH',
                'id_key' => 'id_standard_membrane'
            ],
            'standard_template' => [
                'id' => 'standard_template',
                'name' => 'Tiêu Chuẩn Mẫu (Y Mẫu, Mẫu TP Tồn Kho, Mẫu Theo SP)',
                'id_key' => 'id_standard_template'
            ],
            'standard_condition_color' => [
                'id' => 'standard_condition_color',
                'name' => 'Điều Kiện Xem Màu( Mắt Thường, Light Box., Ngoài Trời)',
                'id_key' => 'id_standard_condition_color'
            ],
            'standard_color' => [
                'id' => 'standard_color',
                'name' => 'Tiêu Chuẩn Màu Của KH',
                'id_key' => 'id_standard_color'
            ],
            'standard_bin_carton' => [
                'id' => 'standard_bin_carton',
                'name' => 'Tiêu Chuẩn Thùng Carton Của KH',
                'id_key' => 'id_standard_bin_carton'
            ],
            'standard_trame' => [
                'id' => 'standard_trame',
                'name' => 'Tiêu Chuẩn Trame Của KH',
                'id_key' => 'id_standard_trame'
            ],
            'standard_sample_code' => [
                'id' => 'standard_sample_code',
                'name' => 'Mã Bìa Mẫu',
                'id_key' => 'id_standard_sample_code'
            ],
            'standard_methods' => [
                'id' => 'standard_methods',
                'name' => 'Phương Pháp Đo (Đúng Điểm Đo, Đều Màu/Tờ In)',
                'id_key' => 'id_standard_methods'
            ],
            'standard_quality_standards' => [
                'id' => 'standard_quality_standards',
                'name' => 'Tiêu Chuẩn Chất Lượng SP',
                'id_key' => 'id_standard_quality_standards'
            ],
        ];
    }

    public function index()
    {
        if (!$this->perViewProducts) {
            accessDenied();
        }
        $data['tnh'] = true;
        $data['path'] = $this->upload_path;
        $data['title'] = _l('tnh_products');
        $th = '';
        $targets = 15;
        $script = '';
        if (!empty($this->show_table_custom_fields)) {
            foreach ($this->show_table_custom_fields as $key => $value) {
                $th .= '<th>' . _maybe_translate_custom_field_name($value['name'], $value['slug']) . '</th>';
                $script .= '{
                    "targets": ' . $targets . ', "name": "' . $value['slug'] . '", "width": "100px"
                },';
                $targets++;
            }
        }
        $data['targetsId'] = $targets;
        $targets++;
        $data['targets'] = $targets;
        $data['script'] = $script;
        $data['th'] = $th;
        $this->load->view('admin/products/manage', $data);
    }

    function getProducts()
    {
        if (!$this->perViewProducts) {
            accessDenied($js = true);
        }

        $branch_staff = get_array_branch_staff();
        $is_admin = is_admin();

        $type = $this->input->post('status_table');
        $category_search = $this->input->post('category_search');
        $products_search = $this->input->post('products_search');
        $code_bom_search = $this->input->post('code_bom_search');
        $date_start_search = $this->input->post('date_start_search');
        $date_end_search = $this->input->post('date_end_search');

        // Correlated subquery giữ lại cho ordering/filtering
        $quantityInventory = "(
            SELECT
                SUM(tblwarehouse_items.product_quantity)
            FROM tblwarehouse_items
            INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
            INNER JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion
            WHERE tblwarehouse_items.id_items = tbl_products.id AND tblwarehouse_items.type_items = 'product' AND tblwarehouse.supplier_id = 0 AND tblwarehouse.id != " . WAREHOUSES_CAPACITY . " AND tbllocaltion_warehouses.stage_id = 0
        )";

        // Thu thập thông tin custom fields (chỉ giữ metadata, không đưa subquery vào SELECT)
        $custom = [];
        $custom_select = [];
        $custom_field_ids = [];
        $custom_field_slugs = [];
        $target = 15;
        if (!empty($this->show_table_custom_fields)) {
            foreach ($this->show_table_custom_fields as $key => $value) {
                $select = "COALESCE((
                    SELECT tblcustomfieldsvalues.value
                    FROM tblcustomfieldsvalues
                    WHERE tblcustomfieldsvalues.fieldto = 'products' AND tblcustomfieldsvalues.relid = tbl_products.id AND tblcustomfieldsvalues.fieldid = " . $value['id'] . "
                ), '') ";
                $custom[] = [
                    'index' => $target,
                    'select' => $value['slug'],
                ];
                $custom_select[$target] = $select;
                $custom_field_ids[] = $value['id'];
                $custom_field_slugs[$value['id']] = $value['slug'];
                $target++;
            }
        }

        // Giữ custom_select cho ordering/filtering
        $custom[] = ['index' => 7, 'select' => 'quantity_inventory'];
        $custom_select[7] = $quantityInventory;

        // Tạo placeholder select cho custom fields (giá trị sẽ được map bằng PHP sau)
        $select_custom_placeholder = "";
        if (!empty($this->show_table_custom_fields)) {
            foreach ($this->show_table_custom_fields as $value) {
                $select_custom_placeholder .= "'' as " . $value['slug'] . ", ";
            }
        }

        $this->db->simple_query('SET SESSION group_concat_max_len=1500000000000000');

        // Query chính: chỉ lấy dữ liệu cơ bản, không join subquery nặng
        $this->datatables->select("
            tbl_products.id as id,
            tbl_products.images as images,
            tbl_category_products.code as category_name,
            tbl_products.type_products as type_products,
            CONCAT(tbl_products.code, '__', COALESCE(tblbranch.name, '')) as code,
            tbl_products.name as name,
            tblunits.unit as unit_name,
            tbl_products.is_no_stock as status,
            0 as quantity_inventory,
            '' as bm,
            '' as st,
            tbl_products.note as note,
            tbl_products.versions as versions,
            tbl_products.versions_stage as versions_stage,
            '' as qty_ws_inventory,
            {$select_custom_placeholder}
            tbl_products.id as id_sort,
            ", FALSE)
            ->from('tbl_products')
            ->join('tbl_category_products', 'tbl_category_products.id = tbl_products.category_id', 'left')
            ->join('tblbranch', 'tblbranch.id = tbl_products.id_branch', 'left')
            ->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');

        $this->datatables->custom_ordering($custom);
        $this->datatables->custom_select($custom_select);
        $this->datatables->where('tbl_products.is_use_product', 1);
        if (!empty($type)) {
            $this->datatables->where('tbl_products.type_products', $type);
        }

        if (!empty($category_search)) {
            $this->datatables->where('tbl_products.category_id', $category_search);
        }
        if (!empty($date_start_search)) {
            $this->datatables->where('tbl_products.date_created >="' . to_sql_date($date_start_search) . ' 00:00:00"');
        }
        if (!empty($date_end_search)) {
            $this->datatables->where('tbl_products.date_created <="' . to_sql_date($date_end_search) . ' 23:59:59"');
        }

        if (!empty($products_search)) {
            $this->datatables->where('tbl_products.id', $products_search);
        }

        if (!empty($code_bom_search)) {
            $this->datatables->like('tbl_products.code_bom', $code_bom_search);
        }

        if (!$is_admin) {
            if (empty($branch_staff)) $branch_staff = [0];
            $this->datatables->where('tbl_products.id_branch IN (' . implode(',', $branch_staff) . ')', false, false);
        }

        if (!$this->perEditProducts) {
            $edit = '';
        } else {
            $edit = '<a class="tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/products/edit_product/$1"><i class="fa fa-pencil width-icon-actions"></i> ' . lang('edit') . '</a>';
        }

        if (!$this->perAddProducts) {
            $design_bom = '';
            $stages_html = '';
        } else {
            $design_bom = '<a class="tnh-modal design_bom" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/products/design_bom/$1"><i class="fa fa-bomb width-icon-actions"></i> ' . lang('tnh_design_bom') . '</a>';

            $stages_html = '<a class="tnh-modal stages" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/products/design_stages/$1"><i class="fa fa-tasks width-icon-actions"></i> ' . lang('stages') . '</a>';
        }

        if (!$this->perDeleteProducts) {
            $delete = '';
        } else {
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
            <button href=\'' . base_url('admin/products/delete_product/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
            <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>';
        }

        $copy = $this->perAddProducts ? '<a class="tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/products/edit_product/$1/copy"><i class="fa fa-copy width-icon-actions"></i> ' . lang('copy') . '</a>' : '';

        $copyBom = $this->perAddProducts ? '<a class="tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/products/copy_bom/$1"><i class="fa fa-copy width-icon-actions"></i> ' . lang('tnh_copy_bom') . '</a>' : '';

        $copyStages = $this->perAddProducts ? '<a class="tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/products/copy_stages/$1"><i class="fa fa-copy width-icon-actions"></i> ' . lang('tnh_copy_stages') . '</a>' : '';

        $editMore = '<a class="tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/products/edit_more/$1"><i class="fa fa-edit width-icon-actions"></i> ' . lang('tnh_edit_more') . '</a>';

        $actions = '
        <div class="dropdown text-center">
            <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
            ' . lang('actions') . '
            <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-left" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                <li>' . $edit . '</li>
                <li>' . $copy . '</li>
                <li>' . $editMore . '</li>
                <li>' . $design_bom . '</li>
                <li>' . $copyBom . '</li>
                <li>' . $stages_html . '</li>
                <li>' . $copyStages . '</li>
                <li class="not-outside">' . $delete . '</li>
            </ul>
        </div>';

        $this->datatables->add_column('actions', $actions, 'id');

        // Lấy kết quả từ Datatables (JSON string)
        $json_result = $this->datatables->generate();
        $result = json_decode($json_result, true);

        if (!empty($result['aaData'])) {
            // Lấy danh sách product_id từ kết quả (cột đầu tiên = id)
            $product_ids = [];
            foreach ($result['aaData'] as $row) {
                $pid = is_array($row) ? (isset($row['id']) ? $row['id'] : $row[0]) : null;
                if ($pid) $product_ids[] = $pid;
            }
            $product_ids = array_unique($product_ids);

            if (!empty($product_ids)) {
                // Batch queries - chạy riêng rồi map bằng PHP
                $bom_map = $this->_getBOMMap($product_ids);
                $stages_map = $this->_getStagesMap($product_ids);
                $qty_map = $this->_getQuantityInventoryMap($product_ids);
                $qty_ws_map = $this->_getQtyWarehouseMap($product_ids);
                $custom_map = $this->_getCustomFieldsMap($product_ids, $custom_field_ids);

                // Xác định xem aaData dùng key hay index
                $use_keys = isset($result['aaData'][0]['id']);

                foreach ($result['aaData'] as &$row) {
                    if ($use_keys) {
                        $pid = $row['id'];
                        $row['quantity_inventory'] = isset($qty_map[$pid]) ? $qty_map[$pid] : 0;
                        $row['bm'] = isset($bom_map[$pid]) ? $bom_map[$pid] : '';
                        $row['st'] = isset($stages_map[$pid]) ? $stages_map[$pid] : '';
                        $row['qty_ws_inventory'] = isset($qty_ws_map[$pid]) ? $qty_ws_map[$pid] : '';
                        // Custom fields
                        foreach ($custom_field_slugs as $field_id => $slug) {
                            $row[$slug] = isset($custom_map[$pid][$field_id]) ? $custom_map[$pid][$field_id] : '';
                        }
                    } else {
                        $pid = $row[0];
                        // Index mapping: 8=quantity_inventory, 9=bm, 10=st, 14=qty_ws_inventory
                        $row[8] = isset($qty_map[$pid]) ? $qty_map[$pid] : 0;
                        $row[9] = isset($bom_map[$pid]) ? $bom_map[$pid] : '';
                        $row[10] = isset($stages_map[$pid]) ? $stages_map[$pid] : '';
                        $row[14] = isset($qty_ws_map[$pid]) ? $qty_ws_map[$pid] : '';
                        // Custom fields (bắt đầu từ index 15)
                        $cf_index = 15;
                        foreach ($custom_field_slugs as $field_id => $slug) {
                            $row[$cf_index] = isset($custom_map[$pid][$field_id]) ? $custom_map[$pid][$field_id] : '';
                            $cf_index++;
                        }
                    }
                }
                unset($row);
            }
        }

        echo json_encode($result);
    }

    /**
     * Batch query lấy BOM versions theo product_ids
     */
    private function _getBOMMap($product_ids)
    {
        $map = [];
        $ids = implode(',', array_map('intval', $product_ids));
        $query = $this->db->query("
            SELECT product_id, GROUP_CONCAT(versions SEPARATOR ':::') as versions
            FROM tbl_product_versions
            WHERE product_id IN ({$ids})
            GROUP BY product_id
        ");
        foreach ($query->result_array() as $row) {
            $map[$row['product_id']] = $row['versions'];
        }
        return $map;
    }

    /**
     * Batch query lấy stages versions theo product_ids
     */
    private function _getStagesMap($product_ids)
    {
        $map = [];
        $ids = implode(',', array_map('intval', $product_ids));
        $query = $this->db->query("
            SELECT product_id, GROUP_CONCAT(versions SEPARATOR ':::') as versions
            FROM tbl_product_stages
            WHERE product_id IN ({$ids})
            GROUP BY product_id
        ");
        foreach ($query->result_array() as $row) {
            $map[$row['product_id']] = $row['versions'];
        }
        return $map;
    }

    /**
     * Batch query lấy tổng tồn kho theo product_ids
     */
    private function _getQuantityInventoryMap($product_ids)
    {
        $map = [];
        $ids = implode(',', array_map('intval', $product_ids));
        $query = $this->db->query("
            SELECT tblwarehouse_items.id_items as product_id, SUM(tblwarehouse_items.product_quantity) as qty
            FROM tblwarehouse_items
            INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
            INNER JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion
            WHERE tblwarehouse_items.id_items IN ({$ids})
                AND tblwarehouse_items.type_items = 'product'
                AND tblwarehouse.supplier_id = 0
                AND tblwarehouse.id != " . WAREHOUSES_CAPACITY . "
                AND tbllocaltion_warehouses.stage_id = 0
            GROUP BY tblwarehouse_items.id_items
        ");
        foreach ($query->result_array() as $row) {
            $map[$row['product_id']] = $row['qty'];
        }
        return $map;
    }

    /**
     * Batch query lấy tồn kho theo từng kho cho product_ids
     */
    private function _getQtyWarehouseMap($product_ids)
    {
        $map = [];
        $ids = implode(',', array_map('intval', $product_ids));
        $query = $this->db->query("
            SELECT wt.id_items as product_id,
                GROUP_CONCAT(CONCAT(tblwarehouse.name, ': ', wt.product_quantity) SEPARATOR '</br>') as qty_detail
            FROM (
                SELECT
                    tblwarehouse_items.id_items,
                    tblwarehouse_items.warehouse_id,
                    SUM(tblwarehouse_items.product_quantity) as product_quantity
                FROM tblwarehouse_items
                INNER JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion
                WHERE tblwarehouse_items.type_items = 'product'
                    AND tblwarehouse_items.id_items IN ({$ids})
                    AND tbllocaltion_warehouses.stage_id = 0
                GROUP BY tblwarehouse_items.warehouse_id, tblwarehouse_items.id_items
            ) wt
            INNER JOIN tblwarehouse ON tblwarehouse.id = wt.warehouse_id
            WHERE tblwarehouse.supplier_id = 0
                AND tblwarehouse.id != " . WAREHOUSES_CAPACITY . "
            GROUP BY wt.id_items
        ");
        foreach ($query->result_array() as $row) {
            $map[$row['product_id']] = $row['qty_detail'];
        }
        return $map;
    }

    /**
     * Batch query lấy custom fields cho product_ids
     */
    private function _getCustomFieldsMap($product_ids, $field_ids)
    {
        $map = [];
        if (empty($field_ids)) return $map;

        $ids = implode(',', array_map('intval', $product_ids));
        $fids = implode(',', array_map('intval', $field_ids));
        $query = $this->db->query("
            SELECT relid, fieldid, value
            FROM tblcustomfieldsvalues
            WHERE fieldto = 'products'
                AND relid IN ({$ids})
                AND fieldid IN ({$fids})
        ");
        foreach ($query->result_array() as $row) {
            $map[$row['relid']][$row['fieldid']] = $row['value'];
        }
        return $map;
    }

    function check_versions()
    {
        $product_id = $this->input->post('product_id');
        $versions = trim($this->input->post('versions'));
        if ($this->products_model->checkProductVersions($product_id, $versions)) {
            $this->form_validation->set_message('check_versions', lang('exists_versions_products'));
            return false;
        } else {
            return true;
        }
    }

    public function design_bom($id, $bom_id = 0, $actions = 'add')
    {
        if (!$this->perAddProducts) {
            accessDenied($js = true);
        }
        $products = $this->products_model->rowProduct($id);
        if ($products['type_products'] == 'semi_products_outside') {
            refererModel(lang('semi_products_outside_not_design_bom'));
        }
        if (!empty($bom_id)) {
            $bom = $this->products_model->getProductVersionsById($bom_id);
        }
        if ($this->input->post()) {
            $this->form_validation->set_rules('product_id', lang("id"), 'required');
            if (empty($bom) || ($bom['product_id'] != $this->input->post('product_id') && $bom['versions'] != $this->input->post('versions'))) {
                if ($actions != 'edit') {
                    $this->form_validation->set_rules('versions', lang("tnh_versions"), 'required|callback_check_versions');
                }
            }
            if ($this->form_validation->run() == true) {
                $status = "unapplication";
                $versions = trim($this->input->post('versions'));
                $product_id = $this->input->post('product_id');
                $date_start = $this->input->post('date_start') ? to_sql_date($this->input->post('date_start')) : null;
                $date_end = $this->input->post('date_end') ? to_sql_date($this->input->post('date_end')) : null;
                $i = $this->input->post('i');
                $use_version = $this->input->post('use_version');
                $options['versions'] = $versions;
                $options['product_id'] = $product_id;
                $options['date_start'] = $date_start;
                $options['date_end'] = $date_end;
                $options['date_created'] = date('Y-m-d H:i:s');
                $options['created_by'] = get_staff_user_id();
                foreach ($i as $key => $value) {
                    $element_name = trim($this->input->post('element_name_' . $value));
                    if (empty($element_name)) continue;
                    $element_number = $this->input->post('element_number_' . $value);
                    $element_number = 1;
                    $type_element = $this->input->post('type_element_' . $value);
                    $options['element'][$key]['element_name'] = $element_name;
                    $options['element'][$key]['element_number'] = $element_number;
                    $options['element'][$key]['type_element'] = $type_element;
                    $type_design_bom = $this->input->post('type_design_bom_' . $value);
                    if (!empty($type_design_bom)) {
                        foreach ($type_design_bom as $k => $val) {
                            // if ($products['type_products'] != 'products' && $val != 'materials') continue;
                            if ($products['type_products'] != 'products' && $val != 'materials' && $val != 'semi_products_outside' && $val != 'semi_products') continue;
                            $item_id = $this->input->post('items_' . $value)[$k];
                            $element_item_number = number_unformat($this->input->post('element_item_number_' . $value)[$k]);
                            $leadtime = !empty($this->input->post('leadtime_' . $value)[$k]) ? $this->input->post('leadtime_' . $value)[$k] : 0;
                            $stage = !empty($this->input->post('stage_' . $value)[$k]) ? $this->input->post('stage_' . $value)[$k] : 0;
                            $machines_id = !empty($this->input->post('machines_' . $value)[$k]) ? $this->input->post('machines_' . $value)[$k] : 0;
                            $unit_id = $this->input->post('units_' . $value)[$k];
                            $quantity_compensation = number_unformat($this->input->post('quantity_compensation_' . $value)[$k], false);

                            $landscape_print_size = !empty($this->input->post('landscape_print_size_' . $value)[$k]) ? $this->input->post('landscape_print_size_' . $value)[$k] : 0;
                            $vertical_print_size = !empty($this->input->post('vertical_print_size_' . $value)[$k]) ? number_unformat($this->input->post('vertical_print_size_' . $value)[$k]) : 0;
                            $number_children_size = !empty($this->input->post('number_children_size_' . $value)[$k]) ? number_unformat($this->input->post('number_children_size_' . $value)[$k]) : 0;
                            $paper_exchange = !empty($this->input->post('paper_exchange_' . $value)[$k]) ? number_unformat($this->input->post('paper_exchange_' . $value)[$k]) : 0;
                            $hand_input_paper_exchange = !empty($this->input->post('hand_input_paper_exchange_' . $value)[$k]) ? $this->input->post('hand_input_paper_exchange_' . $value)[$k] : 0;
                            if (empty($hand_input_paper_exchange)) {
                                $paper_exchange = 0;
                                if ($number_children_size) {
                                    $paper_exchange = roundNumberFormat(1 / $number_children_size);
                                }
                            }

                            $face = !empty($this->input->post('face_' . $value)[$k]) ? $this->input->post('face_' . $value)[$k] : 0;
                            $face_after = !empty($this->input->post('face_after_' . $value)[$k]) ? $this->input->post('face_after_' . $value)[$k] : 0;

                            // if (empty($unit_id) || empty($stage)) {
                            if (empty($unit_id)) {
                                $data['result'] = 0;
                                // $data['message'] = lang('Vui lòng chọn đơn vị tính và giai đoạn');
                                $data['message'] = lang('Vui lòng chọn đơn vị tính');
                                echo json_encode($data);
                                die;
                            }

                            if (empty($unit_id)) continue;
                            $options['element'][$key]['items'][$k]['type'] = $val;
                            $options['element'][$key]['items'][$k]['item_id'] = $item_id;
                            $options['element'][$key]['items'][$k]['unit_id'] = $unit_id;
                            $options['element'][$key]['items'][$k]['element_item_number'] = $element_item_number;
                            $options['element'][$key]['items'][$k]['leadtime'] = $leadtime;
                            // $options['element'][$key]['items'][$k]['stage'] = $type_element == 1 ? STAGES_MATERIAL : $stage;
                            $options['element'][$key]['items'][$k]['stage'] = $stage;
                            $options['element'][$key]['items'][$k]['machines_id'] = $machines_id;
                            $options['element'][$key]['items'][$k]['quantity_compensation'] = $quantity_compensation;
                            $options['element'][$key]['items'][$k]['type_element_item'] = $type_element;

                            $options['element'][$key]['items'][$k]['landscape_print_size'] = $landscape_print_size;
                            $options['element'][$key]['items'][$k]['vertical_print_size'] = $vertical_print_size;
                            $options['element'][$key]['items'][$k]['number_children_size'] = $number_children_size;
                            $options['element'][$key]['items'][$k]['paper_exchange'] = $paper_exchange;
                            $options['element'][$key]['items'][$k]['hand_input_paper_exchange'] = $hand_input_paper_exchange;
                            $options['element'][$key]['items'][$k]['face'] = $face;
                            $options['element'][$key]['items'][$k]['face_after'] = $face_after;

                            // if ($val == 'materials') {
                            $items_replace = !empty($this->input->post('items_replace' . $value)[$k]) ? $this->input->post('items_replace' . $value)[$k] : false;
                            if (!empty($items_replace)) {
                                foreach ($items_replace as $nn => $vv) {

                                    $element_item_number_replace = $this->input->post('element_item_number_replace' . $value)[$k][$nn];
                                    $leadtime_replace = $this->input->post('leadtime_replace' . $value)[$k][$nn];
                                    $stage_replace = !empty($this->input->post('stage_replace' . $value)[$k][$nn]) ? $this->input->post('stage_replace' . $value)[$k][$nn] : 0;
                                    $unit_id_replace = $this->input->post('units_replace' . $value)[$k][$nn];
                                    if (empty($unit_id_replace)) continue;

                                    $options['element'][$key]['items'][$k]['replace'][$nn]['type_replace'] = $val;
                                    $options['element'][$key]['items'][$k]['replace'][$nn]['item_id_replace'] = $vv;
                                    $options['element'][$key]['items'][$k]['replace'][$nn]['unit_id_replace'] = $unit_id_replace;
                                    $options['element'][$key]['items'][$k]['replace'][$nn]['element_item_number_replace'] = $element_item_number_replace;
                                    $options['element'][$key]['items'][$k]['replace'][$nn]['leadtime_replace'] = $leadtime_replace;
                                    $options['element'][$key]['items'][$k]['replace'][$nn]['stage_replace'] = $stage_replace;
                                }
                            }
                            // }
                        }
                    }
                }
                // print_arrays($this->input->post(), $options);
                $q = $this->products_model->insertBOM($options, $status, $bom_id, $actions);
                if ($q) {

                    if (!empty($use_version)) {
                        $this->products_model->updateProducts($product_id, ['versions' => $versions]);
                    }
                    $data['product_id'] = $product_id;
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
            $list_stages = recursive_stages();
            $list_stages_primary = recursive_stages($list_stages_primary, 0, null, 1);
            $data['id'] = $id;
            $data['bom_id'] = $bom_id;
            $data['products'] = $products;

            if (!empty($bom_id)) {
                $data['bom'] = $bom;
                $html_BOM = '';
                $count_i = 0;
                $count_k = 0;
                $kR = 0;
                $elements = $this->products_model->getVersionsElementByVersionId($bom['id']);
                $isFlagPrimary = false;
                $isFlagExtra = false;

                foreach ($elements as $key => $value) {
                    if ($value['type_element'] == 1) {
                        $isFlagPrimary = true;
                    } else if ($value['type_element'] == 2) {
                        $isFlagExtra = false;
                    }
                }

                if (!$isFlagPrimary) {
                    $html_BOM .= '<tr>';
                    $html_BOM .= '<input type="hidden" name="i[]" id="i" class="form-control i" value="' . $count_i . '">';
                    $html_BOM .= '<td>
                                    <div class="text-center">
                                        <button type="button" class="btn btn-primary btn-icon btn-add-items">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </td>';

                    $html_BOM .= '<td colspan="2">
                                    <input type="text" name="element_name_' . $count_i . '" id="element_name_' . $count_i . '" class="form-control" value="NPL chính" placeholder="' . lang('tnh_element_name') . '" required="required">
                                    <input type="hidden" name="type_element_' . $count_i . '" class="form-control type_element" value="1">
                                    <div class="txt-type-element text-danger mtop5">NPL chính</div>
                                </td>';
                    $html_BOM .= '<td></td>';
                    $html_BOM .= '<td class="hide">
                                    <input type="number" name="element_number_' . $count_i . '" class="form-control" value="' . $value['quantity'] . '" min="1">
                                </td>';
                    $html_BOM .= '<td></td>';
                    $html_BOM .= '<td></td>';
                    $html_BOM .= '<td></td>';
                    $html_BOM .= '<td></td>';
                    // $html_BOM .= '<td></td>';
                    // $html_BOM .= '<td></td>';
                    // $html_BOM .= '<td></td>';
                    $html_BOM .= '<td></td>';
                    // <div class="text-center"><i class="btn btn-danger fa fa-remove remove-element"></i></div>
                    $html_BOM .= '<td>
                                </td>';
                    $html_BOM .= '</tr>';
                    $count_i++;
                }


                foreach ($elements as $key => $value) {
                    if ($value['type_element'] == 1) {
                        $isFlagPrimary = true;
                    } else if ($value['type_element'] == 2) {
                        $isFlagExtra = true;
                    }

                    $html_BOM .= '<tr>';
                    $html_BOM .= '<input type="hidden" name="i[]" id="i" class="form-control i" value="' . $count_i . '">';
                    $html_BOM .= '<td>
                                    <div class="text-center">
                                        <button type="button" class="btn btn-primary btn-icon btn-add-items">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </td>';

                    $html_BOM .= '<td colspan="2">
                                    <input type="text" name="element_name_' . $count_i . '" id="element_name_' . $count_i . '" class="form-control" value="' . $value['element_name'] . '" placeholder="' . lang('tnh_element_name') . '" required="required">
                                    <input type="hidden" name="type_element_' . $count_i . '" class="form-control type_element" value="' . $value['type_element'] . '">
                                    <div class="txt-type-element text-danger mtop5">' . ($value['type_element'] == 1 ? 'NPL chính' : 'NPL phụ') . '</div>
                                </td>';
                    $html_BOM .= '<td></td>';
                    $html_BOM .= '<td>
                                    <input type="number" name="element_number_' . $count_i . '" class="form-control hide" value="' . $value['quantity'] . '" min="0">
                                </td>';
                    $html_BOM .= '<td></td>';
                    $html_BOM .= '<td></td>';
                    $html_BOM .= '<td></td>';
                    $html_BOM .= '<td></td>';
                    $html_BOM .= '<td></td>';
                    // $html_BOM .= '<td></td>';
                    // $html_BOM .= '<td></td>';
                    // $html_BOM .= '<td></td>';
                    // <div class="text-center"><i class="btn btn-danger fa fa-remove remove-element"></i></div>
                    $html_BOM .= '<td>
                                </td>';
                    $html_BOM .= '</tr>';

                    $items = $this->products_model->getElementItemsByElementId($value['id']);
                    foreach ($items as $k => $val) {
                        $option = '<option value=""></option>';
                        $type_design = type_design_bom($products['type_products'] == 'products' ? 'all' : 'not_all');
                        foreach ($type_design as $e => $v) {
                            $option .= '<option ' . ($e == $val['type'] ? 'selected' : '') . ' value="' . $e . '">' . $v . '</option>';
                        }

                        $arr_unit_id = [];
                        $displayProduct = 'none';
                        $displayMaterial = 'none';
                        if ($val['type'] == "semi_products" || $val['type'] == "semi_products_outside") {
                            $info = $this->products_model->rowProduct($val['item_id']);
                            array_push($arr_unit_id, $info['unit_id']);
                            $displayProduct = 'block';
                        } else {
                            $info = $this->items_model->rowMaterial($val['item_id']);
                            $exchange = $this->items_model->getExchangeItemsByItemId($val['item_id']);
                            array_push($arr_unit_id, $info['unit_id']);
                            if (!empty($exchange)) {
                                foreach ($exchange as $ke => $va) {
                                    array_push($arr_unit_id, $va['unit_id']);
                                }
                            }
                            $displayMaterial = 'block';
                        }
                        array_push($arr_unit_id, $val['unit_id']);
                        $option_units = '';
                        if (!empty($arr_unit_id)) {
                            $units = $this->products_model->getUnitsByArrId($arr_unit_id);
                            foreach ($units as $a => $el) {
                                $selected_unit = ($el['unitid'] == $val['unit_id']) ? 'selected' : '';
                                $option_units .= '<option ' . $selected_unit . ' value="' . $el['unitid'] . '">' . $el['unit'] . '</option>';
                            }
                        }

                        // $addMaterialReplace = $val['type'] == "materials" ? 'display: block;' : 'display: none;';
                        $addMaterialReplace = 'display: none;';

                        $html_BOM .= '<tr class="tr-child-item tnh-item-' . $count_i . '">';
                        $html_BOM .= '<td></td>';
                        $html_BOM .= '<input type="hidden" name="iii" id="iii" class="form-control iii" value="' . $count_i . '">';
                        $html_BOM .= '<input type="hidden" name="k[]" id="k" class="form-control k" value="' . $count_k . '">';
                        $html_BOM .= '<td colspan="1" style="width: 150px;">
                            <select name="type_design_bom_' . $count_i . '[' . $count_k . ']" data-none-selected-text="' . lang('type') . '" id="type_design_bom_' . $count_k . '" class="form-control type_design_bom" required="required">
                                ' . $option . '
                            </select>

                            <div class="td-category-products mtop5" style="display: ' . $displayProduct . ';">
                                <select data-none-selected-text="Danh mục" data-live-search="true" id="category_product_search_bom' . $count_k . '" class="form-control category_product_search_bom">
                                    <option value=""></option>
                                    ' . recursiveCategoryProducts() . '
                                </select>
                            </div>
                            <div class="td-category-materials  mtop5" style="display: ' . $displayMaterial . ';">
                                <select data-none-selected-text="Danh mục" data-live-search="true" id="category_material_search_bom' . $count_k . '" class="form-control category_material_search_bom">
                                    <option value=""></option>
                                    ' . recursiveCategoryItems() . '
                                </select>
                            </div>
                            <div class="checkbox checkbox-info" style="margin-top: 5px;">
                                <input type="checkbox" ' . ($val['face'] == 1 ? 'checked' : '') . ' name="face_' . $count_i . '[' . $count_k . ']" id="face_' . $count_i . '[' . $count_k . ']" value="1">
                                <label for="face_' . $count_i . '[' . $count_k . ']">Mặt trước</label>
                            </div>
                            <div class="checkbox checkbox-info">
                                <input type="checkbox" ' . ($val['face_after'] == 2 ? 'checked' : '') . ' name="face_after_' . $count_i . '[' . $count_k . ']" id="face_after_' . $count_i . '[' . $count_k . ']" value="2">
                                <label for="face_after_' . $count_i . '[' . $count_k . ']">Mặt sau</label>
                            </div>
                        <span class="fa fa-plus text-primary mtop10 add-replace"  onclick="getMaterialReplace(this)" style="cursor: pointer;' . $addMaterialReplace . '"> ' . lang('tnh_add_material_replace') . '</span>
                        </td>';
                        // $html_BOM .= '<td colspan="1">
                        // <select name="items_'.$count_i.'[]" placeholder="'.lang('choose').'" data-live-search="true" data-none-selected-text="'.lang('choose').'" id="items_'.$count_k.'" class="form-control" required="required">
                        //     <option value="'.$val['item_id'].'"" selected>'.$info['name'].'</option>
                        // </select>
                        // </td>';

                        // <input type="hidden" name="face_'.$count_i.'['.$count_k.']" class="form-control" value="'.$val['face'].'">
                        // <input type="hidden" name="face_after_'.$count_i.'['.$count_k.']" class="form-control" value="'.$val['face_after'].'">
                        // <div class="text-danger">
                        //     '.($val['face'] == 1 ? lang('Mặt trước') : ($val['face'] == 2 ? lang('Mặt sau') : '')).'
                        //     '.($val['face_after'] == 1 ? lang('Mặt trước') : ($val['face_after'] == 2 ? lang(', Mặt sau') : '')).'
                        // </div>

                        $html_BOM .= '<td colspan="1" style="width: 200px;">
                            <input type="text" name="items_' . $count_i . '[' . $count_k . ']" id="items_' . $count_k . '" data-placeholder="' . lang('choose') . '" class="modal-select2 it" style="width: 100%;" value="' . $val['item_id'] . '" required="required">
                        </td>';
                        $html_BOM .= '<td colspan="1" class="class="td-unit"">
                            <select data-placeholder="' . lang('choose') . '" id="units_' . $count_k . '" name="units_' . $count_i . '[' . $count_k . ']" class="modal-select2 units" style="width: 100%;" required>
                                ' . $option_units . '
                            </select>
                        </td>';

                        $html_BOM .= '<td colspan="">
                            <input type="text" name="landscape_print_size_' . $count_i . '[' . $count_k . ']" class="form-control landscape_print_size" value="' . $val['landscape_print_size'] . '">
                        </td>';

                        // $html_BOM .= '<td colspan="">
                        //     <input type="text" name="vertical_print_size_'.$count_i.'['.$count_k.']" class="form-control number-format vertical_print_size" value="'.$val['vertical_print_size'].'">
                        // </td>';

                        $html_BOM .= '<td colspan="">
                            <input type="text" name="number_children_size_' . $count_i . '[' . $count_k . ']" onchange="calPaperExchange(this)" class="form-control number-format number_children_size" value="' . $val['number_children_size'] . '">
                        </td>';

                        $html_BOM .= '<td colspan="">
                            <input type="text" name="element_item_number_' . $count_i . '[' . $count_k . ']" class="form-control number-format" value="' . $val['quantity'] . '">
                        </td>';

                        $html_BOM .=  '<td colspan="">
                            <input type="text" name="paper_exchange_' . $count_i . '[' . $count_k . ']" class="form-control number-format paper_exchange" ' . (!empty($val['hand_input_paper_exchange']) ? '' : 'readonly') . ' value="' . formatNumber($val['paper_exchange']) . '">
                            <div class="checkbox checkbox-info" style="margin-top: 5px !important;">
                                <input type="checkbox" ' . (!empty($val['hand_input_paper_exchange']) ? 'checked' : '') . ' name="hand_input_paper_exchange_' . $count_i . '[' . $count_k . ']" onchange="handInputPaperExchange(this)" id="hand_input_paper_exchange_' . $count_i . '[' . $count_k . ']" class="hand_input_paper_exchange" value="1">
                                <label for="hand_input_paper_exchange_' . $count_i . '[' . $count_k . ']">Nhập tay</label>
                            </div>
                        </td>';

                        $html_BOM .= '<td colspan="">
                            <input type="text" name="quantity_compensation_' . $count_i . '[' . $count_k . ']" class="form-control number-format" value="' . $val['quantity_compensation'] . '">
                        </td>';

                        // $html_BOM .= '<td colspan="">
                        //     <input type="number" name="leadtime_'.$count_i.'['.$count_k.']" class="form-control" value="'.$val['leadtime'].'">
                        // </td>';

                        // <option selected value="'.$val['stage_id'].'">'.$val['stage_id'].'</option>

                        $htmlStage = '';
                        $dtStageCriterial = $this->products_model->getStageCriterial($val['stage_id']);
                        if (!empty($dtStageCriterial)) {
                            foreach ($dtStageCriterial as $kS => $vS) {
                                $htmlStage .= '<div>Rút kiểm: ' . $vS['withdraw_check'] . ' - Tiêu chuẩn kiểm: ' . $vS['test_standards'] . '</div>';
                            }
                        }

                        $html_BOM .= '<td>
                            <input type="hidden" name="" id="stage_edit_' . $count_k . '" class="form-control stage_edit" value="' . $val['stage_id'] . '">
                            <select name="stage_' . $count_i . '[' . $count_k . ']"  data-live-search="true" onChange="changeStage(this)" data-none-selected-text="" id="stage_' . $count_k . '" class="form-control stage_item ' . ($val['type_element_item'] == 1 ? 'stage_items_primary' : '') . '">
                                <option value=""></option>
                                ' . $list_stages . '
                            </select>
                            <div class="txt-info-stage">' . $htmlStage . '</div>
                        </td>';

                        $machine = $this->products_model->getMachinesById($val['machines_id']);
                        $infoMachine = '';
                        $optionMachine = '';
                        if (!empty($machine)) {
                            $process = $this->category_model->getMachinesProcess($val['machines_id']);
                            $divProcess = '';
                            if (!empty($process)) {
                                foreach ($process as $k => $v) {
                                    $divProcess .= $v['process'] . ', ';
                                }
                            }

                            $infoMachine .= '<div class="mtop5">
                                <div><b>' . lang('tnh_standard') . '</b>: ' . $machine['standard'] . '</div>
                                <div><b>' . lang('tnh_pp_measure') . '</b>: ' . $machine['pp_measure'] . '</div>
                                <div><b>' . lang('tnh_quota_productivity') . '</b>: ' . $machine['quota_productivity'] . '</div>
                                <div><b>' . lang('tnh_operating_gauge') . '</b>: ' . $machine['operating_gauge'] . '</div>
                                <div><b>' . lang('tnh_process') . '</b>: ' . $divProcess . '</div>
                            </div>';
                            $optionMachine = '<option selected value="' . $machine['id'] . '">' . $machine['name'] . '</option>';
                        }
                        // $html_BOM .= '<td>
                        //     <select name="machines_'.$count_i.'['.$count_k.']" onchange="changeMachines(this)"  data-live-search="true" data-none-selected-text="Máy móc" id="machines_'.$count_i.'" class="form-control ajax-search" >
                        //         '.$optionMachine.'
                        //     </select>
                        //     <div class="txt-info-machines">'.$infoMachine.'</div>
                        // </td>';

                        $html_BOM .= '<td colspan="">
                        <div class="text-center"><i class="btn btn-danger fa fa-remove remove-element-item"></i></div>
                        </td>';
                        $html_BOM .= '</tr>';

                        $elementItemsReplace = $this->products_model->getElementItemsReplaceByElementItemId($val['id']);
                        if (!empty($elementItemsReplace)) {
                            $cIII = $count_i;
                            $cKK = $count_k;

                            foreach ($elementItemsReplace as $r => $vr) {

                                //handling unit exchange
                                $arr_unit_id_replace = [];
                                // $infoReplace = $this->items_model->rowMaterial($vr['item_id_replace']);
                                // $exchangeReplace = $this->items_model->getExchangeItemsByItemId($vr['item_id_replace']);
                                // array_push($arr_unit_id_replace, $infoReplace['unit_id']);
                                // if (!empty($exchangeReplace)) {
                                //     foreach ($exchangeReplace as $ke => $va) {
                                //         array_push($arr_unit_id_replace, $va['unit_id']);
                                //     }
                                // }
                                // array_push($arr_unit_id_replace, $vr['unit_id_replace']);

                                if ($vr['type_replace'] == "semi_products" || $vr['type_replace'] == "semi_products_outside") {
                                    $infoReplace = $this->products_model->rowProduct($vr['item_id_replace']);
                                    array_push($arr_unit_id_replace, $infoReplace['unit_id']);
                                } else {
                                    $infoReplace = $this->items_model->rowMaterial($vr['item_id_replace']);
                                    $exchange = $this->items_model->getExchangeItemsByItemId($vr['item_id_replace']);
                                    array_push($arr_unit_id_replace, $infoReplace['unit_id']);
                                    if (!empty($exchange)) {
                                        foreach ($exchange as $ke => $va) {
                                            array_push($arr_unit_id_replace, $va['unit_id']);
                                        }
                                    }
                                }
                                array_push($arr_unit_id_replace, $vr['unit_id_replace']);

                                $option_units = '';
                                if (!empty($arr_unit_id_replace)) {
                                    $units = $this->products_model->getUnitsByArrId($arr_unit_id_replace);
                                    foreach ($units as $a => $el) {
                                        $selected_unit = ($el['unitid'] == $vr['unit_id_replace']) ? 'selected' : '';
                                        $option_units .= '<option ' . $selected_unit . ' value="' . $el['unitid'] . '">' . $el['unit'] . '</option>';
                                    }
                                }
                                //end

                                $html_BOM .= '<tr class="tnh-item-' . $cIII . '-' . $cKK . '">';

                                $html_BOM .= '<td colspan="1">';
                                $html_BOM .= '<input type="hidden" name="typeMaterial[]" id="typeMaterial" class="form-control typeMaterial" value="' . $vr['type_replace'] . '">';
                                $html_BOM .= '<input type="hidden" name="cIII[]" id="cIII" class="form-control cIII" value="' . $cIII . '">';
                                $html_BOM .= '<input type="hidden" name="cKK[]" id="cKK" class="form-control cKK" value="' . $cKK . '">';
                                $html_BOM .= '</td>';

                                $html_BOM .= '<td colspan="2">';
                                $html_BOM .= '<div class="row">';
                                $html_BOM .= '<div class="col-md-1"><i class="fa fa-magic"></i></div>';
                                $html_BOM .= '<div class="col-md-11"><input type="text" name="items_replace' . $cIII . '[' . $cKK . '][]" id="items_replace_' . $kR . '" data-placeholder="' . lang('choose') . '" class="modal-select2 it-replace" style="width: 100%;" value="' . $vr['item_id_replace'] . '"></div>';
                                $html_BOM .= '</div>';
                                $html_BOM .= '</td>';

                                $html_BOM .= '<td colspan="1">';
                                $html_BOM .= '<select data-placeholder="' . lang('choose') . '" name="units_replace' . $cIII . '[' . $cKK . '][]" id="units_replace_' . $kR . '[]" class="modal-select2 units-replace" style="width: 100%;">' . $option_units . '</select>';
                                $html_BOM .= '</td>';

                                $html_BOM .= '<td colspan="1">';
                                $html_BOM .= '<input type="number" name="element_item_number_replace' . $cIII . '[' . $cKK . '][]" class="form-control" value="' . $vr['quantity_replace'] . '">';
                                $html_BOM .= '</td>';

                                $html_BOM .= '<td colspan="1">';
                                $html_BOM .= '<input type="number" name="leadtime_replace' . $cIII . '[' . $cKK . '][]" class="form-control" value="' . $vr['leadtime_replace'] . '">';
                                $html_BOM .= '</td>';

                                $html_BOM .= '<td colspan="1">';
                                $html_BOM .= '
                                <input type="hidden" name="" id="stage_edit_replace' . $kR . '" class="form-control stage_edit" value="' . $vr['stage_id_replace'] . '">
                                <select name="stage_replace' . $cIII . '[' . $cKK . '][]"  data-live-search="true" data-none-selected-text="" id="stage_replace' . $kR . '" class="form-control">
                                    <option value=""></option>
                                    ' . $list_stages . '
                                </select>';
                                $html_BOM .= '</td>';

                                $html_BOM .= '<td colspan="1">';
                                $html_BOM .= '<div class="text-center"><i class="btn btn-danger fa fa-remove remove-element-item-replace"></i></div>';
                                $html_BOM .= '</td>';
                                $html_BOM .= '</tr>';
                                $kR++;
                            }
                        }

                        $count_k++;
                    }
                    $count_i++;
                }
                if (count($elements) == 1) {
                    if (!$isFlagExtra) {
                        $html_BOM .= '<tr>';
                        $html_BOM .= '<input type="hidden" name="i[]" id="i" class="form-control i" value="' . $count_i . '">';
                        $html_BOM .= '<td>
                                        <div class="text-center">
                                            <button type="button" class="btn btn-primary btn-icon btn-add-items">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </td>';

                        $html_BOM .= '<td colspan="2">
                                        <input type="text" name="element_name_' . $count_i . '" id="element_name_' . $count_i . '" class="form-control" value="NPL phụ" placeholder="' . lang('tnh_element_name') . '" required="required">
                                        <input type="hidden" name="type_element_' . $count_i . '" class="form-control type_element" value="2">
                                        <div class="txt-type-element text-danger mtop5">NPL phụ</div>
                                    </td>';
                        $html_BOM .= '<td></td>';
                        $html_BOM .= '<td>
                                        <input type="number" name="element_number_' . $count_i . '" class="form-control" value="' . $value['quantity'] . '" min="0">
                                    </td>';
                        $html_BOM .= '<td></td>';
                        $html_BOM .= '<td></td>';
                        $html_BOM .= '<td></td>';
                        $html_BOM .= '<td></td>';
                        // $html_BOM .= '<td></td>';
                        // $html_BOM .= '<td></td>';
                        // $html_BOM .= '<td></td>';
                        $html_BOM .= '<td></td>';
                        // <div class="text-center"><i class="btn btn-danger fa fa-remove remove-element"></i></div>
                        $html_BOM .= '<td>
                                    </td>';
                        $html_BOM .= '</tr>';
                        $count_i++;
                    }
                }
                $data['html_BOM'] = $html_BOM;
                $data['count_i'] = $count_i;
                $data['count_k'] = $count_k;
                $data['kR'] = $kR;
            }

            $data['actions'] = $actions;
            $data['list_stages'] = $list_stages;
            $data['list_stages_primary'] = $list_stages_primary;
            $this->load->view('admin/products/design_bom', $data);
        }
    }

    public function delete_bom($id)
    {
        if (!$this->perAddProducts) {
            accessDenied($js = true);
        }
        $data = [];
        if (!empty($id)) {
            if ($this->products_model->deleteBOOMById($id)) {
                $data['result'] = 1;
                $data['message'] = lang('success');
                $data['table'] = $id;
                $data['type'] = 'BOM';
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

    public function view_product($id)
    {
        if (!$this->perViewProducts) {
            accessDenied($js = true);
        }
        $data['id'] = $id;
        $product = $this->products_model->rowProduct($id);
        if (!empty($product)) {
            $listArray = [];
            foreach ($this->list_standard as $kField => $vField) {
                if (!empty($product[$vField['id_key']])) {
                    $listArray[] = [
                        'type' => $kField,
                        'id' => $product[$vField['id_key']]
                    ];
                }
            }

            if (!empty($listArray)) {
                foreach ($listArray as $k => $v) {
                    $this->db->or_group_start();
                    $this->db->where('id', $v['id']);
                    $this->db->where('type', $v['type']);
                    $this->db->group_end();
                }
                $list_other = $this->db->get('tbllist_other')->result_array();
            }
            if (!empty($list_other)) {
                foreach ($list_other as $key => $value) {
                    $product[$value['type']] = $value['standard'];
                }
            }
        }

        $exchanges = $this->products_model->getExchangeProductsViewByProductId($id);

        $data['exchanges'] = $exchanges;
        $data['colors'] = $this->products_model->getColorsByProductId($id);
        $data['unit'] = $this->unit_model->rowUnit($product['unit_id']);
        $data['product'] = $product;

        $suppliers = $this->products_model->getGroupProductSuppliers($id);
        $warehouses = $this->products_model->getProductWarehouse($id);
        $data['suppliers'] = $suppliers;
        $data['warehouses'] = $warehouses;

        $versions = $this->products_model->getProductVersionsByProductId($id);
        $BOM = '';
        if (!empty($versions)) {
            foreach ($versions as $key => $value) {
                $delete = '<a type="button" class="po btn btn-danger pull-right" data-container="body" data-html="true" data-toggle="popover" data-placement="bottom" data-content="
                <button href=\'' . base_url('admin/products/delete_bom/' . $value['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
                ">' . lang('delete') . '</a>';

                // $copy = '<a class="btn btn-success pull-right copy-bom" value="'.$value['id'].'" href="javascript:void(0)">'.lang('tnh_copy').'</a>';
                $copy = '';

                // <th>'. lang('quantity') .'</th>
                $BOM .= '<div class="table-responsive">
                            <table id="tb-datatable' . $key . '" data-bom="' . $value['id'] . '" class="tnh-table table-hover table-bordered table-condensed" style="margin-top: 10px;">
                                <thead>
                                    <tr class="" style="background: #5cb0d5;">
                                        <th colspan="4" class="info">
                                            <div>
                                                ' . $value['versions'] . '
                                                ' . $delete . '
                                                <a href="' . base_url("admin/products/design_bom/$id/" . $value['id'] . "/edit") . '" class="btn btn-warning pull-right tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal">' . lang('edit') . '</a>
                                                ' . $copy . '
                                            </div>
                                            <div>
                                            </div>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="text-center" style="width: 80px;">' . lang('tnh_numbers') . '</th>
                                        <th style="width: 700px;" colspan="2">' . lang('tnh_element_name') . '</th>
                                    </tr>
                                </thead>
                                <tbody>';
                $elements = $this->products_model->getVersionsElementByVersionId($value['id']);
                foreach ($elements as $k => $val) {
                    // <td>'. $val['quantity'] .'</td>
                    $BOM .= '<tr>
                                            <td style="width: 80px;" class="text-center"><button class="btn btn-primary cols" data-toggle="collapse" data-target="#demo' . $val['id'] . '">' . (++$k) . '</button></td>
                                            <td colspan="2">
                                                ' . $val['element_name'] . '
                                                <div class="text-danger">' . ($val['type_element'] == 1 ? 'NPL chính' : 'NPL phụ') . '</div>
                                            </td>
                                        </tr>';

                    // <th class="text-center" style="width: 100px;">'. lang('tnh_vertical_print_size') .'</th>
                    $items = $this->products_model->getElementItemsByElementId($val['id']);
                    $BOM .= '<tr id="demo' . $val['id'] . '" class="collapse">
                                            <td colspan="99">
                                                <table class="tbbb tnh-table-sub table-bordered table-condensed table-hover" style="margin-top: 0px;">
                                                    <thead>
                                                        <tr style="background: #4caf50d4;">
                                                            <th style="width: 50px;" class="text-center">#</th>
                                                            <th style="width: 150px;">' . lang('type') . '</th>
                                                            <th style="width: 150px;">' . lang('code') . '</th>
                                                            <th style="width: 150px;">' . lang('name') . '</th>
                                                            <th class="text-center" style="width: 100px;">' . lang('unit') . '</th>
                                                            <th class="text-center" style="width: 100px;">' . lang('tnh_landscape_print_size') . '</th>
                                                            <th class="text-center" style="width: 100px;">' . lang('tnh_number_children_size') . '</th>
                                                            <th class="text-center" style="width: 100px;">' . lang('tnh_exchange_value') . '</th>
                                                            <th class="text-center" style="width: 100px;">' . lang('tnh_paper_exchange') . '</th>
                                                            <th class="text-center" style="width: 100px;">' . lang('tnh_quantity_compensation') . '</th>
                                                            <th class="text-center" style="width: 100px;">' . lang('tnh_stage') . '</th>
                                                            <th class="text-center" style="width: 100px;">' . lang('tnh_withdraw_check') . '</th>
                                                            <th class="text-center" style="width: 100px;">' . lang('tnh_test_standards') . '</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>';
                    foreach ($items as $i => $v) {
                        if ($v['type'] == "semi_products" || $v['type'] == "semi_products_outside") {
                            $info = $this->products_model->rowProduct($v['item_id']);
                        } else {
                            $info = $this->items_model->rowMaterial($v['item_id']);
                        }
                        $stage = $this->site_model->rowStagesById($v['stage_id']);
                        $machine = $this->products_model->getMachinesById($v['machines_id']);
                        $stage_criteria = $this->products_model->getStageCriterial($v['stage_id']);
                        $htmlWithdrawCheck = '';
                        $htmlTestStandards = '';
                        if (!empty($stage_criteria)) {
                            foreach ($stage_criteria as $sC => $vC) {
                                $htmlWithdrawCheck .= '<div class="">' . $vC['withdraw_check'] . '</div>';
                                $htmlTestStandards .= '<div class="">' . $vC['test_standards'] . '</div>';
                            }
                        }

                        $infoMachine = '';
                        $process = $this->category_model->getMachinesProcess($v['machines_id']);
                        $divProcess = '';
                        if (!empty($process)) {
                            foreach ($process as $kk => $vv) {
                                $divProcess .= $vv['process'] . ', ';
                            }
                        }

                        if (!empty($machine)) {
                            $infoMachine .= '<div class="mtop5">
                                                                <div><b>' . lang('tnh_standard') . '</b>: ' . $machine['standard'] . '</div>
                                                                <div><b>' . lang('tnh_pp_measure') . '</b>: ' . $machine['pp_measure'] . '</div>
                                                                <div><b>' . lang('tnh_quota_productivity') . '</b>: ' . $machine['quota_productivity'] . '</div>
                                                                <div><b>' . lang('tnh_operating_gauge') . '</b>: ' . $machine['operating_gauge'] . '</div>
                                                                <div><b>' . lang('tnh_process') . '</b>: ' . $divProcess . '</div>
                                                            </div>';
                        }

                        // <td class="text-center">'. $v['leadtime'] .'</td>
                        // <td class="text-center">'.$machine['name'].'</td>
                        // <td>'.$infoMachine.'</td>
                        // <td class="text-center">'. $v['vertical_print_size'] .'</td>
                        $BOM .= '
                                                                <tr>
                                                                    <td class="text-center">' . (++$i) . '</td>
                                                                    <td>
                                                                        ' . lang($v['type']) . '
                                                                        <div class="text-danger">
                                                                            ' . ($v['face'] == 1 ? lang('Mặt trước') : ($v['face'] == 2 ? lang('Mặt sau') : '')) . '
                                                                            ' . ($v['face_after'] == 1 ? lang('Mặt trước') : ($v['face_after'] == 2 ? lang(', Mặt sau') : '')) . '
                                                                        </div>
                                                                    </td>
                                                                    <td>' . $info['code'] . '</td>
                                                                    <td>' . $info['name'] . '</td>
                                                                    <td class="text-center">' . $v['unit'] . '</td>
                                                                    <td class="text-center">' . $v['landscape_print_size'] . '</td>
                                                                    <td class="text-center">' . $v['number_children_size'] . '</td>
                                                                    <td class="text-center">' . $v['quantity'] . '</td>
                                                                    <td class="text-center">
                                                                        ' . formatnumber($v['paper_exchange']) . '
                                                                        ' . ($v['hand_input_paper_exchange'] ? '<div class="text-danger">Nhập tay</div>' : '') . '
                                                                    </td>
                                                                    <td class="text-center">' . $v['quantity_compensation'] . '</td>
                                                                    <td class="text-center">' . $stage['name'] . '</td>
                                                                    <td class="text-center">' . $htmlWithdrawCheck . '</td>
                                                                    <td class="text-center">' . $htmlTestStandards . '</td>
                                                                </tr>';
                        $elementItemsReplace = $this->products_model->getElementItemsReplaceByElementItemId($v['id']);
                        if (!empty($elementItemsReplace)) {
                            foreach ($elementItemsReplace as $n => $vv) {
                                if ($vv['type_replace'] == "semi_products" || $vv['type_replace'] == "semi_products_outside") {
                                    $infoReplace = $this->products_model->rowProduct($vv['item_id_replace']);
                                    $strTypeReplace = lang('tnh_product_outside_replace');
                                } else {
                                    $infoReplace = $this->items_model->rowMaterial($vv['item_id_replace']);
                                    $strTypeReplace = lang('tnh_material_replace');
                                }
                                // $infoReplace = $this->items_model->rowMaterial($vv['item_id_replace']);
                                $stageReplace = $this->site_model->rowStagesById($vv['stage_id_replace']);
                                $unitReplace = $this->unit_model->rowUnit($vv['unit_id_replace']);
                                $BOM .= '<tr style="background: #9e9e9e94;">
                                                                    <td class="text-center">' . ($i) . '.' . (++$n) . '</td>
                                                                    <td>' . $strTypeReplace . '</td>
                                                                    <td>' . $infoReplace['code'] . '</td>
                                                                    <td>' . $infoReplace['name'] . '</td>
                                                                    <td class="text-center">' . $unitReplace['unit'] . '</td>
                                                                    <td class="text-center">' . $vv['quantity_replace'] . '</td>
                                                                    <td class="text-center">' . $vv['leadtime_replace'] . '</td>
                                                                    <td class="">' . $stageReplace['name'] . '</td>
                                                                </tr>';
                            }
                        }
                    }
                    $BOM .= '
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>';
                }
                $BOM .= '     </tbody>
                            </table>
                        </div>';
            }
        } else {
            $BOM = '<div class="text-center">
                        <div>' . lang('no_data_exists') . '</div><img src="' . base_url() . 'assets/images/table-no-data.png">
                    </div>';
        }
        $data['BOM'] = $BOM;

        //stage
        $stages = $this->products_model->getProductStagesByProductId($id);
        $html_stages = '';
        if (!empty($stages)) {
            foreach ($stages as $key => $value) {
                $items = $this->products_model->getProductStagesVersions($value['id']);
                $delete = '<a type="button" class="po btn btn-danger pull-right" data-container="body" data-html="true" data-toggle="popover" data-placement="bottom" data-content="
                <button href=\'' . base_url('admin/products/delete_product_stage/' . $value['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
                ">' . lang('delete') . '</a>';

                // <th class="text-center">'.lang('tnh_quantity_operation_stage').'</th>
                $html_stages .= '
                            <table data-stages="' . $value['id'] . '" class="tnh-table table-bordered table-hover table-condensed" style="margin-top: 10px;">
                                <thead>
                                    <tr style="background: #5cb0d5;">
                                        <th colspan="8" class="info">
                                            <div>
                                                ' . $value['versions'] . '
                                                ' . $delete . '
                                                <a href="' . base_url("admin/products/design_stages/$id/" . $value['id']) . '" class="btn btn-warning pull-right tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal">' . lang('edit') . '</a>
                                            </div>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="text-center" style="width: 80px;">' . lang('tnh_numbers') . '</th>
                                        <th>' . lang('stages') . '</th>
                                        <th class="text-center">' . lang('Đánh dấu công đoạn phát sinh btp hoặc tp chưa hoàn thiện') . '</th>
                                        <th class="text-center" style="width: 120px;">' . lang('Máy móc') . '</th>
                                        <th class="text-center" style="width: 120px;">' . lang('tnh_final_stage') . '</th>
                                        <th class="text-center" style="width: 120px;">' . lang('Số lần/trên mặt') . '</th>
                                        <th class="text-center" style="width: 120px;">' . lang('Số lần vận hành') . '</th>
                                        <th class="text-center" style="width: 120px;">' . lang('Số đường dao cắt') . '</th>
                                    </tr>
                                </thead>
                                <tbody>';
                foreach ($items as $k => $val) {
                    $str_machines = '';
                    if (!empty($val['machines'])) {
                        $machines = $this->category_model->getMachinesByArrId(explode(',', $val['machines']));
                        if (!empty($machines)) {
                            foreach ($machines as $i => $v) {
                                $str_machines .= $v['name'] . '</br>';
                            }
                        }
                    }

                    $strType = '';
                    if ($val['type'] == 1) {
                        $strType = '<span class="label label-success">' . lang('tnh_semi_finished_product') . '</span>';
                    } else if ($val['type'] == 2) {
                        $strType = '<span class="label label-primary">' . lang('tnh_unfinished_product') . '</span>';
                    } else if ($val['type'] == 6) {
                        $strType = '<span class="label label-warning">' . lang('tnh_prepare_materials') . '</span>';
                    } else if ($val['type'] == 7) {
                        $strType = '<span class="label label-danger">' . lang('tnh_commune') . '</span>';
                    }

                    // <td class="text-center">'.$val['number_hours'].'</td>
                    $html_stages .= '<tr>
                                        <td class="text-center">' . $val['number'] . '</td>
                                        <td>
                                            ' . $val['stage_name'] . '
                                            ' . ($val['face'] == 1 ? '<div class="text-danger">Mặt trước</div>' : '') . '
                                            ' . ($val['face_after'] == 2 ? '<div class="text-danger">Mặt sau</div>' : '') . '
                                        </td>
                                        <td class="text-center">' . $strType . '</td>
                                        <td class="text-center">' . $str_machines . '</td>
                                        <td class="text-center">' . ($val['final_stage'] == 1 ? '<span class="label label-success">' . lang('tnh_final_stage') . '</span>' : '') . '</td>
                                        <td class="text-center">' . $val['number_face'] . '</td>
                                        <td class="text-center">' . $val['number_operations'] . '</td>
                                        <td class="text-center">' . $val['number_cutting'] . '</td>
                                    </tr>';
                }
                $html_stages .= '</tbody>
                            </table>';
            }
        } else {
            $html_stages = '<div class="text-center">
                                <div>' . lang('no_data_exists') . '</div>
                                <img src="' . base_url() . 'assets/images/table-no-data.png">
                            </div>';
        }
        $data['html_stages'] = $html_stages;
        $data['custom_fields'] = $this->custom_fields;
        $data['created_by'] = get_staff_full_name($product['created_by']);
        if (!empty($product['updated_by'])) {
            $data['updated_by'] = get_staff_full_name($product['updated_by']);
        } else {
            $data['updated_by'] = '';
        }
        $data['size'] = $this->products_model->getSizeById($product['size']);
        $data['cost_product'] = $this->products_model->getCostPriceProductBom($id);

        //		[
        //			''
        //		];

        $this->load->view('admin/products/view_product', $data);
    }

    public function add_product()
    {
        if (!$this->perAddProducts) {
            accessDenied($js = true);
        }
        $data = [];
        if ($this->input->post()) {
            $this->form_validation->set_rules('category', lang("category"), 'required');
            $this->form_validation->set_rules('type_products', lang("tnh_type_products"), 'required');
            $this->form_validation->set_rules('name', lang("name"), 'required');
            $this->form_validation->set_rules('unit', lang("unit"), 'required');
            $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_products.code]');
            if ($this->form_validation->run() == true) {
                // print_arrays($this->input->post(), $_FILES);
                $category = $this->input->post('category');
                $type_products = $this->input->post('type_products');
                $name = $this->input->post('name');
                $code = $this->input->post('code');
                $name_customer = $this->input->post('name_customer');
                $name_supplier = $this->input->post('name_supplier');
                $note = $this->input->post('note');
                $price_import = number_unformat($this->input->post('price_import'));
                $price_sell = number_unformat($this->input->post('price_sell'));
                $price_processing = number_unformat($this->input->post('price_processing'));
                $number_labor = number_unformat($this->input->post('number_labor'));
                $quantity_minimum = number_unformat($this->input->post('quantity_minimum'));
                $quantity_max = number_unformat($this->input->post('quantity_max'));
                $number_hours_ap = number_unformat($this->input->post('number_hours_ap'));
                $unit = $this->input->post('unit');
                $colors = $this->input->post('colors');
                $mode = $this->input->post('mode');
                $note = $this->input->post('note');
                $type_print = $this->input->post('type_print');
                $columns_id = $this->input->post('columns_id');

                $size = $this->input->post('size');
                $number_day = $this->input->post('number_day');
                $id_branch = $this->input->post('id_branch');

                //
                $mode_product = $this->input->post('mode_product');
                $stage_mode = $this->input->post('stage_mode');
                $stage_standard = $this->input->post('stage_standard');
                $operating_gauge = $this->input->post('operating_gauge');
                $quota_productivity_h = number_unformat($this->input->post('quota_productivity_h'));
                $quota_power_consumption_h = number_unformat($this->input->post('quota_power_consumption_h'));
                $quota_material_replace_t = number_unformat($this->input->post('quota_material_replace_t'));
                $quota_depreciation_ts_date = number_unformat($this->input->post('quota_depreciation_ts_date'));
                $quota_npl_consumption_one = number_unformat($this->input->post('quota_npl_consumption_one'));
                $quota_time_change_one = number_unformat($this->input->post('quota_time_change_one'));
                $person_charge = $this->input->post('person_charge');
                $property_grant = $this->input->post('property_grant');
                $completion_standard = $this->input->post('completion_standard');
                $control_criteria = $this->input->post('control_criteria');
                $productivity_m_w_n = $this->input->post('productivity_m_w_n');
                $quality_problem = number_unformat($this->input->post('quality_problem'));
                $incident_record = $this->input->post('incident_record');
                $operating_procedure = $this->input->post('operating_procedure');
                $withdraw_check_procedure = $this->input->post('withdraw_check_procedure');
                $prevent_procedure = $this->input->post('prevent_procedure');
                $time_inventory = number_unformat($this->input->post('time_inventory'));

                $customer = $this->input->post('customer');
                $product_code_customer = $this->input->post('product_code_customer');
                $product_name_customer = $this->input->post('product_name_customer');
                $standard_colors = $this->input->post('standard_colors');
                $pp_check = $this->input->post('pp_check');
                $number_child_sue = number_unformat($this->input->post('number_child_sue'));
                $packing = $this->input->post('packing');
                $qr = $this->input->post('qr');
                $time_stock = number_unformat($this->input->post('time_stock'));
                $species =  $this->input->post('species');
                $hand_input_code = $this->input->post('hand_input_code');
                $loss = number_unformat($this->input->post('loss'));
                $quantity_child_sheet = number_unformat($this->input->post('quantity_child_sheet'));
                $quantity_sheet_bale = number_unformat($this->input->post('quantity_sheet_bale'));

                $longs = number_unformat($this->input->post('longs'));
                $wide = number_unformat($this->input->post('wide'));
                $height = number_unformat($this->input->post('height'));

                $sample_cover_code = $this->input->post('sample_cover_code');
                $mold_code = $this->input->post('mold_code');

                $color_size = $this->input->post('color_size');
                $gw = $this->input->post('gw');
                $carton_size = $this->input->post('carton_size');
                $code_bom = $this->input->post('code_bom');
                $brand = $this->input->post('brand');
                $brand_id = $this->input->post('brand_id');
                $classify = $this->input->post('classify');
                $unit_measure = $this->input->post('unit_measure');

                $quantity_child_molds = number_unformat($this->input->post('quantity_child_molds'));
                $quantity_child_molds_offset = number_unformat($this->input->post('quantity_child_molds_offset'));
                $quantity_child_molds_flexo = number_unformat($this->input->post('quantity_child_molds_flexo'));
                $delivery_norms = number_unformat($this->input->post('delivery_norms'));

                $allowable = number_unformat($this->input->post('allowable'));
                $quota = number_unformat($this->input->post('quota'));
                $barrel_size = number_unformat($this->input->post('barrel_size'));

                if (empty($hand_input_code)) {
                    $code = handlingCodeProduct($category, $species, $mode_product, $longs, $wide);
                }

                $conversion_unit = $this->input->post('conversion_unit');
                $conversion_quantity_unit = number_unformat($this->input->post('conversion_quantity_unit'));
                if (empty($conversion_unit)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Vui lòng chọn đơn vị quy đổi');
                    echo json_encode($data);
                    die;
                }

                if (empty($conversion_quantity_unit)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Vui lòng nhập số lượng quy đổi');
                    echo json_encode($data);
                    die;
                }

                $id_standard_sample_code = $this->input->post('id_standard_sample_code');
                if (!empty($id_standard_sample_code)) {
                    $this->db->where('id', $id_standard_sample_code);
                    $this->db->where('type', 'standard_sample_code');
                    $sample_cover_code = $this->db->get('tbllist_other')->row('standard');
                } else {
                    $sample_cover_code = '';
                }

                $options = [
                    'category_id' => $category,
                    'type_products' => $type_products,
                    'name' => $name,
                    'code' => $code,
                    'name_customer' => $name_customer,
                    'name_supplier' => $name_supplier,
                    'price_import' => $price_import,
                    'price_sell' => $price_sell,
                    'price_processing' => $price_processing,
                    'number_labor' => $number_labor,
                    'quantity_minimum' => $quantity_minimum,
                    'quantity_max' => $quantity_max,
                    'number_hours_ap' => $number_hours_ap,
                    'unit_id' => $unit,
                    'mode' => $mode,
                    'note' => $note,
                    'date_created' => date('Y-m-d H:i:s'),
                    'created_by' => get_staff_user_id(),
                    'size' => $size,
                    'number_day' => $number_day,

                    'mode_product' => $mode_product,
                    'stage_mode' => $stage_mode,
                    'stage_standard' => $stage_standard,
                    'operating_gauge' => $operating_gauge,
                    'quota_productivity_h' => $quota_productivity_h,
                    'quota_power_consumption_h' => $quota_power_consumption_h,
                    'quota_material_replace_t' => $quota_material_replace_t,
                    'quota_depreciation_ts_date' => $quota_depreciation_ts_date,
                    'quota_npl_consumption_one' => $quota_npl_consumption_one,
                    'quota_time_change_one' => $quota_time_change_one,
                    'person_charge' => $person_charge,
                    'property_grant' => $property_grant,
                    'completion_standard' => $completion_standard,
                    'control_criteria' => $control_criteria,
                    'productivity_m_w_n' => $productivity_m_w_n,
                    'quality_problem' => $quality_problem,
                    'incident_record' => $incident_record,
                    'operating_procedure' => $operating_procedure,
                    'withdraw_check_procedure' => $withdraw_check_procedure,
                    'prevent_procedure' => $prevent_procedure,
                    'time_inventory' => $time_inventory,

                    'customer' => $customer,
                    'product_code_customer' => $product_code_customer,
                    'product_name_customer' => $product_name_customer,
                    'standard_colors' => $standard_colors,
                    'pp_check' => $pp_check,
                    'number_child_sue' => $number_child_sue,
                    'packing' => $packing,
                    'qr' => $qr,
                    'time_stock' => $time_stock,
                    'species' => $species,
                    'hand_input_code' => $hand_input_code,
                    'loss' => $loss,
                    'quantity_child_sheet' => $quantity_child_sheet,
                    'quantity_sheet_bale' => $quantity_sheet_bale,
                    'type_print' => $type_print,
                    // 'columns_id' => $columns_id,

                    'longs' => $longs,
                    'wide' => $wide,
                    'height' => $height,
                    'sample_cover_code' => $sample_cover_code,
                    'mold_code' => $mold_code,

                    'color_size' => $color_size,
                    'gw' => $gw,
                    'carton_size' => $carton_size,
                    'code_bom' => $code_bom,
                    'quantity_child_molds' => $quantity_child_molds,
                    'quantity_child_molds_offset' => $quantity_child_molds_offset,
                    'quantity_child_molds_flexo' => $quantity_child_molds_flexo,

                    'conversion_unit' => $conversion_unit,
                    'conversion_quantity_unit' => $conversion_quantity_unit,
                    'id_branch' => $id_branch,
                    'brand' => $brand,
                    'brand_id' => $brand_id,
                    'classify' => $classify,
                    'unit_measure' => $unit_measure,
                    'delivery_norms' => $delivery_norms,
                    'allowable' => $allowable,
                    'quota' => $quota,
                    'barrel_size' => $barrel_size,
                ];

                $arrayStandard = [];
                foreach ($this->list_standard as $kField => $vField) {
                    $vFData = $this->input->post($vField['id_key']);
                    $vFData = !empty($vFData) ? $vFData : NULL;
                    $options[$vField['id_key']] = $vFData;
                }

                //exchange
                $exchange = false;
                $ex = $this->input->post('unit_exchange');
                if (!empty($ex)) {
                    foreach ($ex as $key => $value) {
                        if (empty($value)) continue;
                        // break;
                        $number_exchange = $this->input->post('number_exchange')[$key];
                        if (empty($number_exchange)) $number_exchange = 1;
                        $exchange[$key]['unit_id'] = $value;
                        $exchange[$key]['number_exchange'] = $number_exchange;
                    }
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
                    //$config['max_filename'] = 25;
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
                        //$config['max_filename'] = 25;
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
                        $productWarehouse[] = [
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
                        $supplier_id = $this->input->post('suppliers')[$value];
                        if (empty($supplier_id)) continue;
                        $procedure_id = !empty($this->input->post('procedure')[$value]) ?  $this->input->post('procedure')[$value] : false;

                        if (!empty($procedure_id)) {
                            foreach ($procedure_id as $k => $val) {
                                $procedure_id = $this->input->post('procedure')[$value][$k];
                                $sequence = $this->input->post('sequence')[$value][$k];
                                $number_date = $this->input->post('number_date')[$value][$k];
                                $productSuppliers[] = [
                                    'supplier_id' => $supplier_id,
                                    'procedure_id' => $procedure_id,
                                    'sequence' => $sequence,
                                    'number_date' => $number_date,
                                ];
                            }
                        } else {
                            $productSuppliers[] = [
                                'supplier_id' => $supplier_id,
                                'procedure_id' => 0,
                                'sequence' => 0,
                                'number_date' => 0,
                            ];
                        }
                    }
                }
                //end
                $id = $this->products_model->insertProducts($options);
                if ($id) {
                    $arrayStandard = [];
                    foreach ($this->list_standard as $kField => $vField) {
                        $vFData = $this->input->post($vField['id_key']);
                        $vFData = !empty($vFData) ? $vFData : NULL;
                        $options[$vField['id_key']] = $vFData;
                        $arrayStandard[] = [
                            'id' => $vFData,
                            'id_product' => $id
                        ];
                    }
                    if (!empty($arrayStandard)) {
                        $this->db->update_batch('tbllist_other', $arrayStandard, 'id');
                    }

                    if (!empty($colors)) {
                        $cl = [];
                        foreach ($colors as $key => $value) {
                            $cl[] = [
                                'product_id' => $id,
                                'color_id' => $value,
                            ];
                        }
                        $this->products_model->insertBatchProductsColors($cl);
                        //bom
                        $bom_id = $this->input->post('bom_id');
                        if (!empty($bom_id) && $type_products != "semi_products_outside") {
                            $bom = $this->products_model->rowBomById($bom_id);
                            if (!empty($bom)) {
                                $fields = [
                                    'versions' => $bom['versions'],
                                    'product_id' => $id,
                                    'status' => 'unapplication',
                                    'date_start' => $bom['date_start'],
                                    'date_end' => $bom['date_end'],
                                    'date_created' => $bom['date_created'],
                                    'created_by' => $bom['created_by'],
                                ];
                                $bom_element = $this->products_model->getBomsElementByBomId($bom_id);
                                foreach ($bom_element as $key => $value) {
                                    $fields['element'][$key]['element_name'] = $value['element_name'];
                                    $fields['element'][$key]['element_number'] = $value['quantity'];

                                    $type = false;
                                    if ($type_products == 'semi_products') $type = ['materials', 'semi_products_outside', 'semi_products'];
                                    $items = $this->products_model->getBomsElementItemsByBEI($value['id'], $type);
                                    if (!empty($items)) {
                                        foreach ($items as $k => $val) {
                                            $fields['element'][$key]['items'][$k]['type'] = $val['type'];
                                            $fields['element'][$key]['items'][$k]['item_id'] = $val['item_id'];
                                            $fields['element'][$key]['items'][$k]['unit_id'] = $val['unit_id'];
                                            $fields['element'][$key]['items'][$k]['element_item_number'] = $val['quantity'];
                                            $fields['element'][$key]['items'][$k]['leadtime'] = 0;
                                            $fields['element'][$key]['items'][$k]['stage'] = 0;

                                            $elementItemsReplace = $this->products_model->getBomsElementItemsReplace($val['id']);
                                            if (!empty($elementItemsReplace)) {
                                                foreach ($elementItemsReplace as $nn => $vv) {
                                                    $fields['element'][$key]['items'][$k]['replace'][$nn]['type_replace'] = $vv['type_replace'];
                                                    $fields['element'][$key]['items'][$k]['replace'][$nn]['item_id_replace'] = $vv['item_id_replace'];
                                                    $fields['element'][$key]['items'][$k]['replace'][$nn]['unit_id_replace'] = $vv['unit_id_replace'];
                                                    $fields['element'][$key]['items'][$k]['replace'][$nn]['element_item_number_replace'] = $vv['quantity_replace'];
                                                    $fields['element'][$key]['items'][$k]['replace'][$nn]['leadtime_replace'] = 0;
                                                    $fields['element'][$key]['items'][$k]['replace'][$nn]['stage_replace'] = 0;
                                                }
                                            }
                                        }
                                    }
                                }
                                if (!empty($fields)) {
                                    $ib = $this->products_model->insertBOM($fields, 'unapplication', 0, $actions = "add");
                                    if ($ib) {
                                        $this->products_model->updateProducts($id, ['versions' => $bom['versions'], 'bom_id' => $bom_id]);
                                    }
                                }
                            }
                        }
                    }

                    if (!empty($exchange)) {
                        foreach ($exchange as $key => $value) {
                            $exchange[$key]['product_id'] = $id;
                        }
                        $this->products_model->insertExchangeProducts($exchange);
                    }
                    if ($this->input->post('custom_fields')) {
                        handle_custom_fields_post($id, $this->input->post('custom_fields'));
                    }

                    //insert warehouse locaiton
                    if (!empty($productWarehouse)) {
                        foreach ($productWarehouse as $key => $value) {
                            $productWarehouse[$key]['product_id'] = $id;
                        }
                        $this->products_model->insertBatchProductWarehouse($productWarehouse);
                    }
                    //end
                    //insert material suppliers
                    if (!empty($productSuppliers)) {
                        foreach ($productSuppliers as $key => $value) {
                            $productSuppliers[$key]['product_id'] = $id;
                        }
                        $this->products_model->insertBatchProductSuppliers($productSuppliers);
                    }

                    if (!empty($columns_id)) {
                        $arrColumns = [];
                        foreach ($columns_id as $key => $value) {
                            $arrColumns[] = [
                                'product_id' => $id,
                                'columns_id' => $value,
                            ];
                        }
                        $this->products_model->insertBatchProductsColumns($arrColumns);
                    }

                    $this->products_model->handlingDesignStages($id);
                    //end

                    insertActivityLog([
                        'type_parent_obj' => 'products',
                        'table_obj' => 'tbl_products',
                        'id_obj' => $id,
                        'name_obj' => $code,
                        'content' => lang('Thêm mới thành phẩm') . ' [' . $code . ']',
                        'actions' => 'add'
                    ]);
                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    if (file_exists($this->upload_path . '' . $images)) {
                        @unlink($this->upload_path . '' . $images);
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
            $data['columns'] = $this->columns_model->getColumns_full_text();
            $data['species'] = $this->species_model->getSpecies();
            $data['type_print'] = $this->products_model->getTypePrint();
            $data['custom_fields'] = $this->custom_fields;
            $data['units'] = $this->unit_model->getUnits();
            $data['brand'] = get_table_where('tbl_brand');
            $data['boms'] = $this->products_model->getBoms();
            $data['warehouses'] = $this->site_model->getWarehouse();
            $data['procedure_detail'] = $this->site_model->getProcedureClientDetail('products');
            $data['branch'] = $this->site_model->getBranch();
            // $data['units'] = $this->unit_model->getUnits();
            $data['size'] = $this->db->get('tblsize')->result_array();
            $this->load->view('admin/products/add_item', $data);
        }
    }

    public function edit_product($id, $actions = "edit")
    {
        if (!$this->perEditProducts) {
            accessDenied($js = true);
        }
        $data = [];
        $product = $this->products_model->rowProduct($id);
        $id_copy = $id;
        if ($this->input->post()) {

            // if ($id == STAGES_MATERIAL) {
            //     $data['result'] = 0;
            //     $data['message'] = lang('Chuận bị NPL không thể sửa');
            //     echo json_encode($data); die;
            // }

            $this->form_validation->set_rules('category', lang("tnh_item_materials_category"), 'required');
            $this->form_validation->set_rules('name', lang("name"), 'required');
            $this->form_validation->set_rules('unit', lang("unit"), 'required');
            if ($product['code'] != $this->input->post('code') || $actions == 'copy') {
                $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_products.code]');
            }
            if ($this->form_validation->run() == true) {
                $images_old = $actions == 'edit' ? $product['images'] : null;
                $category = $this->input->post('category');
                $type_products = $this->input->post('type_products');
                $name = $this->input->post('name');
                $code = $this->input->post('code');
                $name_customer = $this->input->post('name_customer');
                $name_supplier = $this->input->post('name_supplier');
                $note = $this->input->post('note');
                $price_import = number_unformat($this->input->post('price_import'));
                $price_sell = number_unformat($this->input->post('price_sell'));
                $price_processing = number_unformat($this->input->post('price_processing'));
                $number_labor = number_unformat($this->input->post('number_labor'));
                $quantity_minimum = number_unformat($this->input->post('quantity_minimum'));
                $quantity_max = number_unformat($this->input->post('quantity_max'));
                $number_hours_ap = number_unformat($this->input->post('number_hours_ap'));
                $unit = $this->input->post('unit');
                $colors = $this->input->post('colors');
                $mode = $this->input->post('mode');
                $note = $this->input->post('note');
                $columns_id = $this->input->post('columns_id');
                $size = $this->input->post('size');
                $number_day = $this->input->post('number_day');
                $id_branch = $this->input->post('id_branch');

                //
                $mode_product = $this->input->post('mode_product');
                $stage_mode = $this->input->post('stage_mode');
                $stage_standard = $this->input->post('stage_standard');
                $operating_gauge = $this->input->post('operating_gauge');
                $quota_productivity_h = number_unformat($this->input->post('quota_productivity_h'));
                $quota_power_consumption_h = number_unformat($this->input->post('quota_power_consumption_h'));
                $quota_material_replace_t = number_unformat($this->input->post('quota_material_replace_t'));
                $quota_depreciation_ts_date = number_unformat($this->input->post('quota_depreciation_ts_date'));
                $quota_npl_consumption_one = number_unformat($this->input->post('quota_npl_consumption_one'));
                $quota_time_change_one = number_unformat($this->input->post('quota_time_change_one'));
                $person_charge = $this->input->post('person_charge');
                $property_grant = $this->input->post('property_grant');
                $completion_standard = $this->input->post('completion_standard');
                $control_criteria = $this->input->post('control_criteria');
                $productivity_m_w_n = $this->input->post('productivity_m_w_n');
                $quality_problem = number_unformat($this->input->post('quality_problem'));
                $incident_record = $this->input->post('incident_record');
                $operating_procedure = $this->input->post('operating_procedure');
                $withdraw_check_procedure = $this->input->post('withdraw_check_procedure');
                $prevent_procedure = $this->input->post('prevent_procedure');
                $time_inventory = number_unformat($this->input->post('time_inventory'));

                $customer = $this->input->post('customer');
                $product_code_customer = $this->input->post('product_code_customer');
                $product_name_customer = $this->input->post('product_name_customer');
                $standard_colors = $this->input->post('standard_colors');
                $pp_check = $this->input->post('pp_check');
                $number_child_sue = number_unformat($this->input->post('number_child_sue'));
                $packing = $this->input->post('packing');
                $qr = $this->input->post('qr');
                $time_stock = number_unformat($this->input->post('time_stock'));
                $species =  $this->input->post('species');
                $hand_input_code = $this->input->post('hand_input_code');
                $loss = number_unformat($this->input->post('loss'));
                $quantity_child_sheet = number_unformat($this->input->post('quantity_child_sheet'));
                $quantity_sheet_bale = number_unformat($this->input->post('quantity_sheet_bale'));
                $longs = number_unformat($this->input->post('longs'));
                $wide = number_unformat($this->input->post('wide'));
                $height = number_unformat($this->input->post('height'));
                $type_print = $this->input->post('type_print');
                $sample_cover_code = $this->input->post('sample_cover_code');
                $mold_code = $this->input->post('mold_code');

                $color_size = $this->input->post('color_size');
                $gw = $this->input->post('gw');
                $carton_size = $this->input->post('carton_size');
                $code_bom = $this->input->post('code_bom');
                $brand = $this->input->post('brand');
                $brand_id = $this->input->post('brand_id');
                $classify = $this->input->post('classify');
                $unit_measure = $this->input->post('unit_measure');

                $quantity_child_molds = number_unformat($this->input->post('quantity_child_molds'));
                $quantity_child_molds_offset = number_unformat($this->input->post('quantity_child_molds_offset'));
                $quantity_child_molds_flexo = number_unformat($this->input->post('quantity_child_molds_flexo'));
                $delivery_norms = number_unformat($this->input->post('delivery_norms'));

                $allowable = number_unformat($this->input->post('allowable'));
                $quota = number_unformat($this->input->post('quota'));
                $barrel_size = number_unformat($this->input->post('barrel_size'));


                if (empty($hand_input_code)) {
                    $code = handlingCodeProduct($category, $species, $mode_product, $longs, $wide);
                }

                if ($actions == 'edit') {
                    if ($product['type_products'] != $type_products && $product['type_products'] != 'products') {
                        $this->db->from('tbl_element_items');
                        $this->db->where('tbl_element_items.item_id', $id);
                        $this->db->where_in('tbl_element_items.type', ['semi_products_outside', 'semi_products']);
                        $isSemi = $this->db->count_all_results();
                        if (!empty($isSemi)) {
                            $data['result'] = 0;
                            $data['message'] = lang('Đã sử dụng trong BOM không thể đổi loại thành phẩm');
                            echo json_encode($data);
                            die;
                        }

                        $this->db->from('tbl_boms_element_items');
                        $this->db->where('tbl_boms_element_items.item_id', $id);
                        $this->db->where_in('tbl_boms_element_items.type', ['semi_products_outside', 'semi_products']);
                        $isSemi = $this->db->count_all_results();
                        if (!empty($isSemi)) {
                            $data['result'] = 0;
                            $data['message'] = lang('Đã sử dụng trong BOM không thể đổi loại thành phẩm');
                            echo json_encode($data);
                            die;
                        }
                    }
                }

                $conversion_unit = $this->input->post('conversion_unit');
                $conversion_quantity_unit = number_unformat($this->input->post('conversion_quantity_unit'));
                if (empty($conversion_unit)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Vui lòng chọn đơn vị quy đổi');
                    echo json_encode($data);
                    die;
                }

                if (empty($conversion_quantity_unit)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Vui lòng nhập số lượng quy đổi');
                    echo json_encode($data);
                    die;
                }

                $id_standard_sample_code = $this->input->post('id_standard_sample_code');
                if (!empty($id_standard_sample_code)) {
                    $this->db->where('id', $id_standard_sample_code);
                    $this->db->where('type', 'standard_sample_code');
                    $sample_cover_code = $this->db->get('tbllist_other')->row('standard');
                } else {
                    $sample_cover_code = '';
                }


                $options = [
                    'category_id' => $category,
                    'type_products' => $type_products,
                    'name' => $name,
                    'code' => $code,
                    'name_customer' => $name_customer,
                    'name_supplier' => $name_supplier,
                    'price_import' => $price_import,
                    'price_sell' => $price_sell,
                    'price_processing' => $price_processing,
                    'number_labor' => $number_labor,
                    'quantity_minimum' => $quantity_minimum,
                    'quantity_max' => $quantity_max,
                    'number_hours_ap' => $number_hours_ap,
                    'unit_id' => $unit,
                    'mode' => $mode,
                    'note' => $note,
                    'date_updated' => date('Y-m-d H:i:s'),
                    'updated_by' => get_staff_user_id(),
                    'size' => $size,
                    'number_day' => $number_day,

                    'mode_product' => $mode_product,
                    'stage_mode' => $stage_mode,
                    'stage_standard' => $stage_standard,
                    'operating_gauge' => $operating_gauge,
                    'quota_productivity_h' => $quota_productivity_h,
                    'quota_power_consumption_h' => $quota_power_consumption_h,
                    'quota_material_replace_t' => $quota_material_replace_t,
                    'quota_depreciation_ts_date' => $quota_depreciation_ts_date,
                    'quota_npl_consumption_one' => $quota_npl_consumption_one,
                    'quota_time_change_one' => $quota_time_change_one,
                    'person_charge' => $person_charge,
                    'property_grant' => $property_grant,
                    'completion_standard' => $completion_standard,
                    'control_criteria' => $control_criteria,
                    'productivity_m_w_n' => $productivity_m_w_n,
                    'quality_problem' => $quality_problem,
                    'incident_record' => $incident_record,
                    'operating_procedure' => $operating_procedure,
                    'withdraw_check_procedure' => $withdraw_check_procedure,
                    'prevent_procedure' => $prevent_procedure,
                    'time_inventory' => $time_inventory,

                    'customer' => $customer,
                    'product_code_customer' => $product_code_customer,
                    'product_name_customer' => $product_name_customer,
                    'standard_colors' => $standard_colors,
                    'pp_check' => $pp_check,
                    'number_child_sue' => $number_child_sue,
                    'packing' => $packing,
                    'qr' => $qr,
                    'time_stock' => $time_stock,
                    'species' => $species,
                    'hand_input_code' => $hand_input_code,
                    'loss' => $loss,
                    'quantity_child_sheet' => $quantity_child_sheet,
                    'quantity_sheet_bale' => $quantity_sheet_bale,
                    'type_print' => $type_print,
                    // 'columns_id' => $columns_id,
                    'longs' => $longs,
                    'wide' => $wide,
                    'height' => $height,
                    'sample_cover_code' => $sample_cover_code,
                    'mold_code' => $mold_code,

                    'color_size' => $color_size,
                    'gw' => $gw,
                    'carton_size' => $carton_size,
                    'code_bom' => $code_bom,
                    'quantity_child_molds' => $quantity_child_molds,
                    'quantity_child_molds_offset' => $quantity_child_molds_offset,
                    'quantity_child_molds_flexo' => $quantity_child_molds_flexo,

                    'conversion_unit' => $conversion_unit,
                    'conversion_quantity_unit' => $conversion_quantity_unit,
                    'id_branch' => $id_branch,
                    'brand' => $brand,
                    'brand_id' => $brand_id,
                    'classify' => $classify,
                    'unit_measure' => $unit_measure,
                    'delivery_norms' => $delivery_norms,
                    'allowable' => $allowable,
                    'quota' => $quota,
                    'barrel_size' => $barrel_size,
                ];
                $arrayStandard = [];
                $arrayProductStandardReset = [];
                foreach ($this->list_standard as $kField => $vField) {
                    $vFData = $this->input->post($vField['id_key']);
                    $vFData = !empty($vFData) ? $vFData : NULL;
                    $options[$vField['id_key']] = $vFData;
                    if (!empty($product[$vField['id_key']])) {
                        if ($vFData != $product[$vField['id_key']])
                            $arrayProductStandardReset[] = $product[$vField['id_key']];
                    }
                }


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
                    //$config['max_filename'] = 25;
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

                //image multiple
                $images_multiple_old = $actions == 'edit' ? $product['images_multiple'] : null;
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
                        //$config['max_filename'] = 25;
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

                // print_arrays($options['versions']);
                if ($actions == 'edit') {
                    $up = $this->products_model->updateProducts($id, $options);
                    $arrayStandard = [];
                    foreach ($this->list_standard as $kField => $vField) {
                        $vFData = $this->input->post($vField['id_key']);
                        $vFData = !empty($vFData) ? $vFData : NULL;
                        $options[$vField['id_key']] = $vFData;
                        $arrayStandard[] = [
                            'id' => $vFData,
                            'id_product' => $id
                        ];
                    }
                    if (!empty($arrayProductStandardReset)) {
                        $this->db->where_in('id', $arrayProductStandardReset);
                        $this->db->update('tbllist_other', ['id_product' => NULL]);
                    }

                    if (!empty($arrayStandard)) {
                        $this->db->update_batch('tbllist_other', $arrayStandard, 'id');
                    }
                } else {
                    $up = $this->products_model->insertProducts($options);
                    $id = $up;
                    $arrayStandard = [];
                    foreach ($this->list_standard as $kField => $vField) {
                        $vFData = $this->input->post($vField['id_key']);
                        $vFData = !empty($vFData) ? $vFData : NULL;
                        $options[$vField['id_key']] = $vFData;
                        $arrayStandard[] = [
                            'id' => $vFData,
                            'id_product' => $id
                        ];
                    }
                    if (!empty($arrayStandard)) {
                        $this->db->update_batch('tbllist_other', $arrayStandard, 'id');
                    }
                }

                //exchange
                $exchange = false;
                $ex = $this->input->post('unit_exchange');
                if (!empty($ex)) {
                    foreach ($ex as $key => $value) {
                        if (empty($value)) continue;
                        // break;
                        $number_exchange = $this->input->post('number_exchange')[$key];
                        if (empty($number_exchange)) $number_exchange = 1;
                        $exchange[$key]['product_id'] = $id;
                        $exchange[$key]['unit_id'] = $value;
                        $exchange[$key]['number_exchange'] = $number_exchange;
                    }
                }

                //handing warehouse locaiton
                $warehouses = $this->input->post('warehouses');
                if (!empty($warehouses)) {
                    foreach ($warehouses as $key => $value) {
                        if (empty($value)) continue;
                        $location_id = $this->input->post('location')[$key];
                        $productWarehouse[] = [
                            'product_id' => $id,
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
                        $supplier_id = $this->input->post('suppliers')[$value];
                        if (empty($supplier_id)) continue;
                        $procedure_id = !empty($this->input->post('procedure')[$value]) ?  $this->input->post('procedure')[$value] : false;
                        // print_arrays()

                        if (!empty($procedure_id)) {
                            foreach ($procedure_id as $k => $val) {
                                $procedure_id = $this->input->post('procedure')[$value][$k];
                                $sequence = $this->input->post('sequence')[$value][$k];
                                $number_date = $this->input->post('number_date')[$value][$k];
                                $productSuppliers[] = [
                                    'product_id' => $id,
                                    'supplier_id' => $supplier_id,
                                    'procedure_id' => $procedure_id,
                                    'sequence' => $sequence,
                                    'number_date' => $number_date,
                                ];
                            }
                        } else {
                            $productSuppliers[] = [
                                'product_id' => $id,
                                'supplier_id' => $supplier_id,
                                'procedure_id' => 0,
                                'sequence' => 0,
                                'number_date' => 0,
                            ];
                        }
                    }
                }
                //end
                //handing boms
                $counter_bom = 0;
                $arr_id_bom = [];
                $using = $this->input->post('using');

                $product_versions = $this->input->post('product_versions');
                if (!empty($product_versions)) {
                    foreach ($product_versions as $key => $value) {
                        array_push($arr_id_bom, $value);
                        if ($counter_bom == $using) $options['versions'] = $value;
                        $counter_bom++;
                    }
                }
                $bs = $this->input->post('bs');
                if (!empty($bs)) {
                    foreach ($bs as $i => $el) {
                        $bom_id = $el;
                        $bom = $this->products_model->rowBomById($bom_id);
                        if (!empty($bom)) {
                            $fields[$i] = [
                                'versions' => $bom['versions'],
                                'product_id' => $id,
                                'bm_id' => $bom_id,
                                'status' => 'unapplication',
                                'date_start' => $bom['date_start'],
                                'date_end' => $bom['date_end'],
                                'date_created' => $bom['date_created'],
                                'created_by' => $bom['created_by'],
                            ];
                            $bom_element = $this->products_model->getBomsElementByBomId($bom_id);
                            foreach ($bom_element as $key => $value) {
                                $fields[$i]['element'][$key]['element_name'] = $value['element_name'];
                                $fields[$i]['element'][$key]['element_number'] = $value['quantity'];

                                $type = false;
                                if ($type_products == 'semi_products') $type = ['materials', 'semi_products_outside', 'semi_products'];
                                $items = $this->products_model->getBomsElementItemsByBEI($value['id'], $type);
                                if (!empty($items)) {
                                    foreach ($items as $k => $val) {
                                        $fields[$i]['element'][$key]['items'][$k]['type'] = $val['type'];
                                        $fields[$i]['element'][$key]['items'][$k]['item_id'] = $val['item_id'];
                                        $fields[$i]['element'][$key]['items'][$k]['unit_id'] = $val['unit_id'];
                                        $fields[$i]['element'][$key]['items'][$k]['element_item_number'] = $val['quantity'];
                                        $fields[$i]['element'][$key]['items'][$k]['leadtime'] = 0;
                                        $fields[$i]['element'][$key]['items'][$k]['stage'] = 0;

                                        $elementItemsReplace = $this->products_model->getBomsElementItemsReplace($val['id']);
                                        if (!empty($elementItemsReplace)) {
                                            foreach ($elementItemsReplace as $nn => $vv) {
                                                $fields[$i]['element'][$key]['items'][$k]['replace'][$nn]['type_replace'] = $vv['type_replace'];
                                                $fields[$i]['element'][$key]['items'][$k]['replace'][$nn]['item_id_replace'] = $vv['item_id_replace'];
                                                $fields[$i]['element'][$key]['items'][$k]['replace'][$nn]['unit_id_replace'] = $vv['unit_id_replace'];
                                                $fields[$i]['element'][$key]['items'][$k]['replace'][$nn]['element_item_number_replace'] = $vv['quantity_replace'];
                                                $fields[$i]['element'][$key]['items'][$k]['replace'][$nn]['leadtime_replace'] = 0;
                                                $fields[$i]['element'][$key]['items'][$k]['replace'][$nn]['stage_replace'] = 0;
                                            }
                                        }
                                    }
                                }
                            }
                            if ($counter_bom == $using) $options['versions'] = $bom['versions'];
                        }
                        $counter_bom++;
                    }
                }

                if ($actions == 'copy') {
                    $this->db->select('
                        tbl_product_versions.*
                    ', false);
                    $this->db->from('tbl_product_versions');
                    $this->db->where('tbl_product_versions.product_id', $id_copy);
                    $product_versions = $this->db->get()->result_array();
                    if (!empty($product_versions)) {
                        foreach ($product_versions as $key => $bom) {
                            $c_versions = $bom['versions'];
                            $options = [
                                'versions' => $c_versions,
                                'product_id' => $id,
                                'status' => '',
                                'date_start' => $bom['date_start'],
                                'date_end' => $bom['date_end'],
                                'date_created' => $bom['date_created'],
                                'created_by' => $bom['created_by'],
                            ];

                            $this->db->select('
                                tbl_versions_element.*
                            ', false);
                            $this->db->from('tbl_versions_element');
                            $this->db->where('tbl_versions_element.version_id', $bom['id']);
                            $element = $this->db->get()->result_array();
                            if (!empty($element)) {
                                foreach ($element as $kE => $valE) {
                                    $options['element'][$kE]['element_name'] = $valE['element_name'];
                                    $options['element'][$kE]['element_number'] = $valE['quantity'];
                                    $options['element'][$kE]['type_element'] = $valE['type_element'];

                                    $this->db->select('tbl_element_items.*', false);
                                    $this->db->from('tbl_element_items');
                                    $this->db->where('tbl_element_items.element_id', $valE['id']);
                                    $element_items = $this->db->get()->result_array();
                                    if (!empty($element_items)) {
                                        foreach ($element_items as $kEI => $vEI) {
                                            $options['element'][$kE]['items'][$kEI]['type'] = $vEI['type'];
                                            $options['element'][$kE]['items'][$kEI]['item_id'] = $vEI['item_id'];
                                            $options['element'][$kE]['items'][$kEI]['unit_id'] = $vEI['unit_id'];
                                            $options['element'][$kE]['items'][$kEI]['element_item_number'] = $vEI['quantity'];
                                            $options['element'][$kE]['items'][$kEI]['leadtime'] = $vEI['leadtime'];
                                            $options['element'][$kE]['items'][$kEI]['stage'] = $vEI['stage_id'];
                                            $options['element'][$kE]['items'][$kEI]['machines_id'] = $vEI['machines_id'];
                                            $options['element'][$kE]['items'][$kEI]['quantity_compensation'] = $vEI['quantity_compensation'];
                                            $options['element'][$kE]['items'][$kEI]['type_element_item'] = $vEI['quantity_compensation'];
                                            $options['element'][$kE]['items'][$kEI]['landscape_print_size'] = $vEI['landscape_print_size'];
                                            $options['element'][$kE]['items'][$kEI]['vertical_print_size'] = $vEI['vertical_print_size'];
                                            $options['element'][$kE]['items'][$kEI]['number_children_size'] = $vEI['number_children_size'];
                                            $options['element'][$kE]['items'][$kEI]['paper_exchange'] = $vEI['paper_exchange'];
                                            $options['element'][$kE]['items'][$kEI]['hand_input_paper_exchange'] = $vEI['hand_input_paper_exchange'];
                                            $options['element'][$kE]['items'][$kEI]['face'] = $vEI['face'];
                                            $options['element'][$kE]['items'][$kEI]['face_after'] = $vEI['face_after'];
                                        }
                                    }
                                }
                            }

                            $q = $this->products_model->insertBOM($options);

                            if ($key == 0) {
                                $this->products_model->updateProducts($id, ['versions' => $c_versions]);
                            }
                        }
                    }
                }

                if ($up) {
                    if (!empty($colors)) {
                        $this->products_model->deleteProductsColorsByProductId($id);
                        $cl = [];
                        foreach ($colors as $key => $value) {
                            $cl[] = [
                                'product_id' => $id,
                                'color_id' => $value,
                            ];
                        }
                        $this->products_model->insertBatchProductsColors($cl);
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

                    if (!empty($exchange)) {
                        $this->products_model->deleteExchangeByProductId($id);
                        $this->products_model->insertExchangeProducts($exchange);
                    }

                    if ($this->input->post('custom_fields')) {
                        handle_custom_fields_post($id, $this->input->post('custom_fields'));
                    }

                    $this->products_model->deleteProductSuppliersByProductId($id);
                    $this->products_model->deleteProductWarehouseByProductId($id);
                    //insert warehouse locaiton
                    if (!empty($productWarehouse)) {
                        $this->products_model->insertBatchProductWarehouse($productWarehouse);
                    }
                    //end
                    //insert material suppliers
                    if (!empty($productSuppliers)) {
                        $this->products_model->insertBatchProductSuppliers($productSuppliers);
                    }
                    //end

                    //up bom
                    $deleteBom = $this->products_model->getProductVersionsByNotIdAndProduct($arr_id_bom, $id);
                    if (!empty($deleteBom)) {
                        foreach ($deleteBom as $key => $value) {
                            $this->products_model->deleteBOOMById($value['id']);
                        }
                    }
                    if (!empty($fields)) {
                        foreach ($fields as $key => $field) {
                            if (!$this->products_model->checkProductVersions($id, $field['versions'])) {
                                $ib = $this->products_model->insertBOM($field, 'unapplication', 0, $actions = "add");
                            }
                        }
                    }


                    $this->products_model->deleteProductsColumns($id);
                    if (!empty($columns_id)) {
                        $arrColumns = [];
                        foreach ($columns_id as $key => $value) {
                            $arrColumns[] = [
                                'product_id' => $id,
                                'columns_id' => $value,
                            ];
                        }
                        $this->products_model->insertBatchProductsColumns($arrColumns);
                    }
                    //

                    insertActivityLog([
                        'type_parent_obj' => 'products',
                        'table_obj' => 'tbl_products',
                        'id_obj' => $id,
                        'name_obj' => $code,
                        'content' => lang('Sửa thành phẩm') . ' [' . $code . ']',
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
            // $data['columns'] = $this->columns_model->getColumns();
            $data['columns'] = $this->columns_model->getColumns_full_text();

            $data['actions'] = $actions;
            $data['species'] = $this->species_model->getSpecies();
            $data['type_print'] = $this->products_model->getTypePrint();
            $data['custom_fields'] = $this->custom_fields;
            $data['product'] = $product;
            $data['colors'] = $this->products_model->getColorsByProductId($id);
            $data['units'] = $this->unit_model->getUnits();
            $data['brand'] = get_table_where('tbl_brand');
            $data['procedure_detail'] = $this->site_model->getProcedureClientDetail();
            $data['warehouses'] = $this->site_model->getWarehouse();
            $data['product_suppliers'] = $this->products_model->getGroupProductSuppliers($id);
            $data['product_warehouse'] = $this->products_model->getProductWarehouse($id);
            $data['boms_product'] = $this->products_model->getBomsProducts($id);
            $arr_bom_product = false;
            $data['boms'] = $this->products_model->getBoms($arr_bom_product);
            $data['exchanges'] = $this->products_model->getExchangeProductsByProductId($id);
            $data['branch'] = $this->site_model->getBranch();
            $data['id'] = $id;
            $data['size'] = $this->db->get('tblsize')->result_array();
            $this->load->view('admin/products/edit_item', $data);
        }
    }

    function delete_product($id)
    {
        if (!$this->perDeleteProducts) {
            accessDenied($js = true);
        }
        $data = [];
        if ($id) {
            $product = $this->products_model->rowProduct($id);
            if ($this->products_model->checkExistProducts($id)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_exist_not_delete');
                echo json_encode($data);
                return;
            }
            if ($this->products_model->deleteProducts($id)) {
                $this->products_model->deleteProductsColorsByProductId($id);
                $this->products_model->deleteProductsVersionsByProductId($id);
                $this->products_model->deleteProductStages($id);
                $this->products_model->deleteProductSuppliersByProductId($id);
                $this->products_model->deleteProductWarehouseByProductId($id);
                $this->products_model->deleteExchangeByProductId($id);
                $this->products_model->deleteProductsColumns($id);
                deleteCustomFields('products', $id);
                if (!empty($product['images'])) {
                    if (file_exists($this->upload_path . '' . $product['images'])) {
                        @unlink($this->upload_path . '' . $product['images']);
                    }
                }
                if (!empty($product['images_multiple'])) {
                    foreach (explode('||', $product['images_multiple']) as $key => $value) {
                        if (file_exists($this->upload_path . '' . $value)) {
                            @unlink($this->upload_path . '' . $value);
                        }
                    }
                }
                insertActivityLog([
                    'type_parent_obj' => 'products',
                    'table_obj' => 'tbl_products',
                    'id_obj' => $id,
                    'name_obj' => $product['code'],
                    'content' => lang('Xóa thành phẩm') . ' [' . $product['code'] . ']',
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

    function delete_products_multiple()
    {
        if (!$this->perDeleteProducts) {
            accessDenied($js = true);
        }
        $data = [];
        if ($this->input->post()) {
            if (!$this->input->post('product_id')) {
                $data['result'] = 0;
                $data['message'] = lang('no_data_exists');
                echo json_encode($data);
                return;
            }
            $errors = '';
            $count = 0;
            foreach ($this->input->post('product_id') as $key => $id) {
                if ($this->products_model->checkExistProducts($id)) {
                    $row = $this->products_model->rowProduct($id);
                    $errors .= '<div class="text-danger">' . $row['code'] . ' ' . lang('tnh_exist_not_delete') . '</div>';
                    continue;
                }
                $product = $this->products_model->rowProduct($id);
                if ($this->products_model->deleteProducts($id)) {
                    $count++;
                    $this->products_model->deleteProductsColorsByProductId($id);
                    $this->products_model->deleteProductsVersionsByProductId($id);
                    $this->products_model->deleteProductStages($id);
                    $this->products_model->deleteProductSuppliersByProductId($id);
                    $this->products_model->deleteProductWarehouseByProductId($id);
                    $this->products_model->deleteExchangeByProductId($id);
                    $this->products_model->deleteProductsColumns($id);
                    deleteCustomFields('products', $id);
                    if (!empty($product['images'])) {
                        if (file_exists($this->upload_path . '' . $product['images'])) {
                            @unlink($this->upload_path . '' . $product['images']);
                        }
                        if (!empty($product['images_multiple'])) {
                            foreach (explode('||', $product['images_multiple']) as $key => $value) {
                                if (file_exists($this->upload_path . '' . $value)) {
                                    @unlink($this->upload_path . '' . $value);
                                }
                            }
                        }
                    }
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
        $data['title'] = _l('tnh_category_product');
        $this->load->view('admin/products/category', $data);
    }

    public function add_category()
    {
        if (!$this->perAddCategory) {
            accessDenied($js = true);
        }
        $data = [];
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_category_products.code]');
            if ($this->form_validation->run() == true) {
                $name = $this->input->post('name');
                $code = $this->input->post('code');
                $note = $this->input->post('note');
                $parent_id = $this->input->post('parent_id') ? $this->input->post('parent_id') : 0;
                $customers_groups = $this->input->post('customers_groups');

                $options = [
                    'name' => $name,
                    'code' => $code,
                    'parent_id' => $parent_id,
                    'note' => $note,
                ];

                $id = $this->products_model->insertCategoryProducts($options);
                if ($id) {

                    $arrCustomersGroups = [];
                    if (!empty($customers_groups)) {
                        foreach ($customers_groups as $k => $val) {
                            $arrCustomersGroups[] = [
                                'customers_groups_id' => $val,
                                'category_products_id' => $id,
                            ];
                        }
                        $this->products_model->insertBatchCategoryProductsCustomers($arrCustomersGroups);
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
            return;
        } else {
            $data['customers_groups'] = $this->site_model->getCustomersGroups();
            $this->load->view('admin/products/add_category', $data);
        }
    }

    public function edit_category($id)
    {
        if (!$this->perEditCategory) {
            accessDenied($js = true);
        }
        $data = [];
        $category = $this->products_model->rowCategoryProducts($id);
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($category['code'] != $this->input->post('code')) {
                $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_category_products.code]');
            }
            if ($this->form_validation->run() == true) {
                $name = $this->input->post('name');
                $code = $this->input->post('code');
                $note = $this->input->post('note');
                $parent_id = $this->input->post('parent_id') ? $this->input->post('parent_id') : 0;
                $customers_groups = $this->input->post('customers_groups');

                $options = [
                    'name' => $name,
                    'code' => $code,
                    'parent_id' => $parent_id,
                    'note' => $note,
                ];

                $category_id = $id;
                $id = $this->products_model->updateCategoryProducts($id, $options);
                if ($id) {

                    $this->products_model->deleteCategoryProductsCustomers($id);
                    $arrCustomersGroups = [];
                    if (!empty($customers_groups)) {
                        foreach ($customers_groups as $k => $val) {
                            $arrCustomersGroups[] = [
                                'customers_groups_id' => $val,
                                'category_products_id' => $category_id,
                            ];
                        }
                        $this->products_model->insertBatchCategoryProductsCustomers($arrCustomersGroups);
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
            return;
        } else {
            $data['category_products_customers'] = $this->products_model->getCategoryProductsCustomers($id);
            $data['customers_groups'] = $this->site_model->getCustomersGroups();
            $data['category'] = $category;
            $this->load->view('admin/products/edit_category', $data);
        }
    }

    function getCategory()
    {
        if (!$this->perViewCategory) {
            accessDenied($js = true);
        }

        $tbCategoryProductsCustomers = "(
            SELECT
                tbl_category_products_customers.category_products_id as category_products_id,
                GROUP_CONCAT(tblcustomers_groups.name) as name_group
            FROM tbl_category_products_customers
            INNER JOIN tblcustomers_groups ON tblcustomers_groups.id = tbl_category_products_customers.customers_groups_id
            GROUP BY tbl_category_products_customers.category_products_id
        ) tb_category_products_customers";

        $this->datatables->select("
            tbl_category_products.id as id,
            0 as records,
            tbl_category_products.code as code,
            tbl_category_products.name as name,
            tb_category_products_customers.name_group as category_products_customers,
            tbl_category_products.note as note,
            '' as sub
            ", FALSE)
            ->from('tbl_category_products');
        $this->datatables->join($tbCategoryProductsCustomers, 'tb_category_products_customers.category_products_id = tbl_category_products.id', 'left');
        $this->datatables->where('tbl_category_products.parent_id', 0);

        $edit = $this->perEditCategory ? '<a class="tnh-modal btn btn-success btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/products/edit_category/$1"><i class="fa fa-pencil"></i></a>' : '';

        $delete = $this->perDeleteCategory ? '<button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
        <button href=\'' . base_url('admin/products/delete_category/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
        <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
        "><i class="fa fa-remove"></i></button>' : '';

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
            $result->aaData[$key][6] = $this->recursiveTableCategoryItems($output, $id);
        }
        echo (json_encode($result));
    }

    function recursiveTableCategoryItems(&$output = null, $parent_id = 0, $indent = null, $stt = 1)
    {

        $tbCategoryProductsCustomers = "(
            SELECT
                tbl_category_products_customers.category_products_id as category_products_id,
                GROUP_CONCAT(tblcustomers_groups.name) as name_group
            FROM tbl_category_products_customers
            INNER JOIN tblcustomers_groups ON tblcustomers_groups.id = tbl_category_products_customers.customers_groups_id
            GROUP BY tbl_category_products_customers.category_products_id
        ) tb_category_products_customers";

        $this->db->select('tbl_category_products.*, tb_category_products_customers.name_group as category_products_customers', false);
        $this->db->from('tbl_category_products');
        $this->db->join($tbCategoryProductsCustomers, 'tb_category_products_customers.category_products_id = tbl_category_products.id', 'left');
        $this->db->where('tbl_category_products.parent_id', $parent_id);
        $this->db->order_by('tbl_category_products.parent_id');
        $query = $this->db->get()->result_array();

        foreach ($query as $key => $item) {
            if ($item['parent_id'] == $parent_id) {
                $output .= '<tr>
                    <td>' . $indent . '' . $item['code'] . '</td>
                    <td>' . $item['name'] . '</td>
                    <td>' . $item['category_products_customers'] . '</td>
                    <td>' . $item['note'] . '</td>
                    <td>
                        <div>
                        <a class="tnh-modal btn btn-success btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/products/edit_category/' . $item['id'] . '"><i class="fa fa-pencil"></i></a>
                        <button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="bottom" data-content="
                        <button href=\'' . base_url('admin/products/delete_category/' . $item['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
        if (!$this->perDeleteCategory) {
            accessDenied($js = true);
        }
        $data = [];
        if ($id) {
            if ($this->products_model->checkExistCategory($id)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_exist_not_delete');
                echo json_encode($data);
                return;
            }
            if ($this->products_model->checkParentId($id)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_please_remove_sub_items');
                echo json_encode($data);
                die;
            }
            if ($this->products_model->deleteCategoryProducts($id)) {
                $this->products_model->deleteCategoryProductsCustomers($id);
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
        if (!$this->perDeleteCategory) {
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
                if ($this->products_model->checkExistCategory($id)) {
                    $row = $this->products_model->rowCategoryProducts($id);
                    $errors .= '<div class="text-danger">' . $row['code'] . ' ' . lang('tnh_exist_not_delete') . '</div>';
                    continue;
                }
                if ($this->products_model->checkParentId($id)) {
                    $row = $this->products_model->rowCategoryProducts($id);
                    $errors .= '<div class="text-danger">' . $row['code'] . ' ' . lang('tnh_please_remove_sub_items') . '</div>';
                    continue;
                }
                if ($this->products_model->deleteCategoryProducts($id)) {
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
            $data = $this->products_model->searchCategory($q, $limit);
        }
        echo json_encode($data);
    }

    public function colors()
    {
        $data['tnh'] = true;
        $data['title'] = _l('colors');
        $this->load->view('admin/products/colors', $data);
    }

    public function add_color()
    {
        $data = [];
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_colors.code]');
            if ($this->form_validation->run() == true) {
                $name = $this->input->post('name');
                $code = $this->input->post('code');
                $note = $this->input->post('note');
                $H_color = $this->input->post('color');

                $options = [
                    'name' => $name,
                    'code' => $code,
                    'color' => $H_color,
                    'note' => $note,
                ];

                $id = $this->products_model->insertColors($options);
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
            $this->load->view('admin/products/add_color', $data);
        }
    }

    public function edit_color($id)
    {
        $data = [];
        $color = $this->products_model->rowColors($id);
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($color['code'] != $this->input->post('code')) {
                $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_colors.code]');
            }
            if ($this->form_validation->run() == true) {
                $name = $this->input->post('name');
                $code = $this->input->post('code');
                $H_color = $this->input->post('color');
                $note = $this->input->post('note');

                $options = [
                    'name' => $name,
                    'code' => $code,
                    'color' => $H_color,
                    'note' => $note,
                ];

                $id = $this->products_model->updateColors($id, $options);
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
            $data['color'] = $color;
            $this->load->view('admin/products/edit_color', $data);
        }
    }

    function getColors()
    {
        $this->datatables->select("
            tbl_colors.id as id,
            tbl_colors.code as code,
            tbl_colors.name as name,
            tbl_colors.color as color,
            tbl_colors.note as note,
            ", FALSE)
            ->from('tbl_colors');

        $this->datatables->add_column('actions', '
            <div>
                <a class="tnh-modal btn btn-success btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/products/edit_color/$1"><i class="fa fa-pencil"></i></a>
                <button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                        <button href=\'' . base_url('admin/products/delete_colors/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                        <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
                    "><i class="fa fa-remove"></i></button>
            </div>
        ', 'id');
        $result = json_decode($this->datatables->generate());
        echo (json_encode($result));
    }

    function delete_colors($id)
    {
        $data = [];
        if ($id) {
            if ($this->products_model->checkExistColors($id)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_exist_not_delete');
                echo json_encode($data);
                return;
            }
            if ($this->products_model->deleteColors($id)) {
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

    function searchColors()
    {
        $data = [];
        if ($this->input->get()) {
            $q = $this->input->get('q');
            $limit = 50;
            $data = $this->products_model->searchColors($q, $limit);
        }
        echo json_encode($data);
    }

    function searchSemiProducts()
    {
        $data = [];
        if ($this->input->get()) {
            $q = $this->input->get('q');
            $limit = 50;
            $data = $this->products_model->searchSemiProducts($q, $limit);
        }
        echo json_encode($data);
    }

    function searchSelect2SemiProducts($id = false)
    {
        $data = [];
        // if ($this->input->get())
        // {
        $term = $this->input->get('term');
        $limit = 50;
        $params = $this->input->get('params');
        $data['results'] = $this->products_model->searchSelect2SemiProducts($term, $limit, 'semi_products', $params);
        if ($id) {
            $product = $this->products_model->rowProduct($id);
            $data['row'] = ['id' => $product['id'], 'text' => $product['name'] . '(' . $product['code'] . ')'];
        }
        // }
        echo json_encode($data);
    }

    function searchSelect2SemiProductsOutside($id = false)
    {
        $data = [];
        // if ($this->input->get())
        // {
        $term = $this->input->get('term');
        $limit = 50;
        $params = $this->input->get('params');
        $data['results'] = $this->products_model->searchSelect2SemiProducts($term, $limit, 'semi_products_outside', $params);
        if ($id) {
            $product = $this->products_model->rowProduct($id);
            $data['row'] = ['id' => $product['id'], 'text' => $product['name'] . '(' . $product['code'] . ')'];
        }
        // }
        echo json_encode($data);
    }

    function change_versions()
    {
        if (!$this->perAddProducts) {
            accessDenied($js = true);
        }
        $data = [];
        if ($this->input->post()) {
            $product_id = $this->input->post('product_id');
            $material_bom = $this->input->post('material_bom');
            if ($this->products_model->updateProducts($product_id, ['versions' => $material_bom])) {
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

    //stages
    public function stages()
    {
        $data['tnh'] = true;
        $data['title'] = _l('stages');
        $data['category_stages'] = $this->db->get('tbl_category_stages')->result_array();
        $this->load->view('admin/products/stages', $data);
    }

    public function add_stage()
    {
        $data = [];
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            $this->form_validation->set_rules('category_stages', lang("tnh_category_stages"), 'required');
            $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_stages.code]');
            if ($this->form_validation->run() == true) {
                $category_stages = $this->input->post('category_stages');
                $name = $this->input->post('name');
                $code = $this->input->post('code');
                $note = $this->input->post('note', false);
                $status_qc = !empty($this->input->post('status_qc')) ? $this->input->post('status_qc') : 0;
                $status_default_outsource = !empty($this->input->post('status_default_outsource')) ? $this->input->post('status_default_outsource') : 0;
                $type = !empty($this->input->post('type')) ? $this->input->post('type') : 0;
                $stage_again = !empty($this->input->post('stage_again')) ? $this->input->post('stage_again') : 0;
                $stage_import_outsource = !empty($this->input->post('stage_import_outsource')) ? $this->input->post('stage_import_outsource') : 0;

                $name_category_stage = $this->input->post('name_category_stage');
                $code_category_stage = $this->input->post('code_category_stage');
                $stage_price_gauge = number_unformat($this->input->post('stage_price_gauge'));
                $operating_gauge = $this->input->post('operating_gauge');
                $completion_standard = $this->input->post('completion_standard');
                $control_criteria = $this->input->post('control_criteria');
                $quota_productivity_h = number_unformat($this->input->post('quota_productivity_h'));
                $quota_power_consumption_h = number_unformat($this->input->post('quota_power_consumption_h'));
                $quota_material_replace_t = number_unformat($this->input->post('quota_material_replace_t'));
                $quota_depreciation_ts_date = number_unformat($this->input->post('quota_depreciation_ts_date'));
                $quota_npl_consumption_one = number_unformat($this->input->post('quota_npl_consumption_one'));
                $printcolor = number_unformat($this->input->post('printcolor'));
                $quantity_zinc = number_unformat($this->input->post('quantity_zinc'));
                $number_operations = number_unformat($this->input->post('number_operations'));
                $withdraw_check = $this->input->post('withdraw_check');
                $formula_m2 = !empty($this->input->post('formula_m2')) ? $this->input->post('formula_m2') : 0;
                $time_watch_cards = !empty($this->input->post('time_watch_cards')) ? $this->input->post('time_watch_cards') : NULL;
                $number_watch_cards = !empty($this->input->post('number_watch_cards')) ? $this->input->post('number_watch_cards') : NULL;
                $arrCriteria = [];
                if (!empty($withdraw_check)) {
                    foreach ($withdraw_check as $key => $value) {
                        if (empty($value)) continue;
                        $test_standards = !empty($this->input->post('test_standards')[$key]) ? $this->input->post('test_standards')[$key] : NULL;
                        if (empty($test_standards)) continue;

                        $arrCriteria[] = [
                            'withdraw_check' => $value,
                            'test_standards' => $test_standards,
                        ];
                    }
                }

                $customers_groups_id = $this->input->post('customers_groups_id');
                $arrStagesCustomerPrices = [];
                if (!empty($customers_groups_id)) {
                    foreach ($customers_groups_id as $key => $value) {
                        $price_group_customer = !empty($this->input->post('price_group_customer')[$key]) ? number_unformat($this->input->post('price_group_customer')[$key]) : 0;
                        $arrStagesCustomerPrices[] = [
                            'customers_groups_id' => $value,
                            'price_group_customer' => $price_group_customer,
                        ];
                    }
                }

                $options = [
                    'category_stages' => $category_stages,
                    'name' => $name,
                    'code' => $code,
                    'note' => $note,
                    'parent_id' => 0,
                    'status_qc' => $status_qc,
                    'status_default_outsource' => $status_default_outsource,
                    'type' => $type,
                    'stage_again' => $stage_again,
                    'stage_import_outsource' => $stage_import_outsource,
                    'name_category_stage' => $name_category_stage,
                    'code_category_stage' => $code_category_stage,
                    'stage_price_gauge' => $stage_price_gauge,
                    'operating_gauge' => $operating_gauge,
                    'completion_standard' => $completion_standard,
                    'control_criteria' => $control_criteria,
                    'quota_productivity_h' => $quota_productivity_h,
                    'quota_power_consumption_h' => $quota_power_consumption_h,
                    'quota_material_replace_t' => $quota_material_replace_t,
                    'quota_depreciation_ts_date' => $quota_depreciation_ts_date,
                    'quota_npl_consumption_one' => $quota_npl_consumption_one,
                    'printcolor' => $printcolor,
                    'quantity_zinc' => $quantity_zinc,
                    'number_operations' => $number_operations,
                    'formula_m2' => $formula_m2,
                    'time_watch_cards' => $time_watch_cards,
                    'number_watch_cards' => $number_watch_cards,
                ];
                $id = $this->products_model->insertStages($options);
                if ($id) {

                    if (!empty($arrCriteria)) {
                        foreach ($arrCriteria as $k => $v) {
                            $v['stage_id'] = $id;
                            $this->products_model->insertStageCriteria($v);
                        }
                    }

                    if (!empty($arrStagesCustomerPrices)) {
                        foreach ($arrStagesCustomerPrices as $key => $value) {
                            $arrStagesCustomerPrices[$key]['stage_id'] = $id;
                        }
                        $this->products_model->insertBatchStagesCustomerPrices($arrStagesCustomerPrices);
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
            return;
        } else {
            $data['categoryStages'] = $this->products_model->getCategoryStages();
            $this->load->view('admin/products/add_stage', $data);
        }
    }

    public function edit_stage($id, $actions = 'edit')
    {
        $data = [];
        $stage = $this->products_model->rowStages($id);
        if (empty($stage)) {
            $data['result'] = 0;
            $data['message'] = lang('no_data_exists');
            echo json_encode($data);
            return;
        }
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($stage['code'] != $this->input->post('code') || $actions == 'copy') {
                $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_stages.code]');
            }
            $this->form_validation->set_rules('category_stages', lang("tnh_category_stages"), 'required');
            if ($this->form_validation->run() == true) {
                $category_stages = $this->input->post('category_stages');
                $name = $this->input->post('name');
                $code = $this->input->post('code');
                $note = $this->input->post('note', false);
                $status_qc = !empty($this->input->post('status_qc')) ? $this->input->post('status_qc') : 0;
                $status_default_outsource = !empty($this->input->post('status_default_outsource')) ? $this->input->post('status_default_outsource') : 0;
                $type = !empty($this->input->post('type')) ? $this->input->post('type') : 0;
                $stage_again = !empty($this->input->post('stage_again')) ? $this->input->post('stage_again') : 0;
                $stage_import_outsource = !empty($this->input->post('stage_import_outsource')) ? $this->input->post('stage_import_outsource') : 0;

                $name_category_stage = $this->input->post('name_category_stage');
                $code_category_stage = $this->input->post('code_category_stage');
                $stage_price_gauge = number_unformat($this->input->post('stage_price_gauge'));
                $operating_gauge = $this->input->post('operating_gauge');
                $completion_standard = $this->input->post('completion_standard');
                $control_criteria = $this->input->post('control_criteria');
                $quota_productivity_h = number_unformat($this->input->post('quota_productivity_h'));
                $quota_power_consumption_h = number_unformat($this->input->post('quota_power_consumption_h'));
                $quota_material_replace_t = number_unformat($this->input->post('quota_material_replace_t'));
                $quota_depreciation_ts_date = number_unformat($this->input->post('quota_depreciation_ts_date'));
                $quota_npl_consumption_one = number_unformat($this->input->post('quota_npl_consumption_one'));

                $printcolor = number_unformat($this->input->post('printcolor'));
                $quantity_zinc = number_unformat($this->input->post('quantity_zinc'));
                $number_operations = number_unformat($this->input->post('number_operations'));
                $formula_m2 = !empty($this->input->post('formula_m2')) ? $this->input->post('formula_m2') : 0;

                $time_watch_cards = !empty($this->input->post('time_watch_cards')) ? $this->input->post('time_watch_cards') : NULL;
                $number_watch_cards = !empty($this->input->post('number_watch_cards')) ? $this->input->post('number_watch_cards') : NULL;

                $withdraw_check = $this->input->post('withdraw_check');
                $arrCriteria = [];
                if (!empty($withdraw_check)) {
                    foreach ($withdraw_check as $key => $value) {
                        if (empty($value)) continue;
                        $test_standards = !empty($this->input->post('test_standards')[$key]) ? $this->input->post('test_standards')[$key] : NULL;
                        if (empty($test_standards)) continue;
                        $stage_criteria_id = !empty($this->input->post('stage_criteria_id')[$key]) ? $this->input->post('stage_criteria_id')[$key] : 0;


                        $arrCriteria[] = [
                            'id' => $stage_criteria_id,
                            'stage_id' => $id,
                            'withdraw_check' => $value,
                            'test_standards' => $test_standards,
                        ];
                    }
                }

                $customers_groups_id = $this->input->post('customers_groups_id');
                $arrStagesCustomerPrices = [];
                if (!empty($customers_groups_id)) {
                    foreach ($customers_groups_id as $key => $value) {
                        $price_group_customer = !empty($this->input->post('price_group_customer')[$key]) ? number_unformat($this->input->post('price_group_customer')[$key]) : 0;
                        $stages_customer_prices_id = !empty($this->input->post('stages_customer_prices')[$key]) ? ($this->input->post('stages_customer_prices')[$key]) : 0;

                        $arrStagesCustomerPrices[] = [
                            'id' => $stages_customer_prices_id,
                            'stage_id' => $id,
                            'customers_groups_id' => $value,
                            'price_group_customer' => $price_group_customer,
                        ];
                    }
                }

                $options = [
                    'category_stages' => $category_stages,
                    'name' => $name,
                    'code' => $code,
                    'note' => $note,
                    'status_qc' => $status_qc,
                    'status_default_outsource' => $status_default_outsource,
                    'type' => $type,
                    'stage_again' => $stage_again,
                    'stage_import_outsource' => $stage_import_outsource,

                    'name_category_stage' => $name_category_stage,
                    'code_category_stage' => $code_category_stage,
                    'stage_price_gauge' => $stage_price_gauge,
                    'operating_gauge' => $operating_gauge,
                    'completion_standard' => $completion_standard,
                    'control_criteria' => $control_criteria,
                    'quota_productivity_h' => $quota_productivity_h,
                    'quota_power_consumption_h' => $quota_power_consumption_h,
                    'quota_material_replace_t' => $quota_material_replace_t,
                    'quota_depreciation_ts_date' => $quota_depreciation_ts_date,
                    'quota_npl_consumption_one' => $quota_npl_consumption_one,
                    'printcolor' => $printcolor,
                    'quantity_zinc' => $quantity_zinc,
                    'number_operations' => $number_operations,
                    'formula_m2' => $formula_m2,
                    'time_watch_cards' => $time_watch_cards,
                    'number_watch_cards' => $number_watch_cards,
                    'category_stage_productivity' => $this->input->post('category_stage_productivity') ?? 0,
                    'category_stage_bangiao' => $this->input->post('category_stage_bangiao') ?? 0
                ];
                $stage_id = $id;
                if ($actions == 'edit') {
                    $id = $this->products_model->updateStages($id, $options);
                } else if ($actions == 'copy') {
                    $id = $this->products_model->insertStages($options);
                }

                if ($id) {
                    if ($actions == 'copy') {
                        $withdraw_check = $this->input->post('withdraw_check');
                        $arrCriteria = [];
                        if (!empty($withdraw_check)) {
                            foreach ($withdraw_check as $key => $value) {
                                if (empty($value)) continue;
                                $test_standards = !empty($this->input->post('test_standards')[$key]) ? $this->input->post('test_standards')[$key] : NULL;
                                if (empty($test_standards)) continue;

                                $arrCriteria[] = [
                                    'stage_id' => $id,
                                    'withdraw_check' => $value,
                                    'test_standards' => $test_standards,
                                ];
                            }
                        }

                        $customers_groups_id = $this->input->post('customers_groups_id');
                        $arrStagesCustomerPrices = [];
                        if (!empty($customers_groups_id)) {
                            foreach ($customers_groups_id as $key => $value) {
                                $price_group_customer = !empty($this->input->post('price_group_customer')[$key]) ? number_unformat($this->input->post('price_group_customer')[$key]) : 0;

                                $arrStagesCustomerPrices[] = [
                                    'stage_id' => $id,
                                    'customers_groups_id' => $value,
                                    'price_group_customer' => $price_group_customer,
                                ];
                            }
                        }

                        if (!empty($arrCriteria)) {
                            foreach ($arrCriteria as $k => $v) {
                                $this->products_model->insertStageCriteria($v);
                            }
                        }

                        if (!empty($arrStagesCustomerPrices)) {
                            $this->products_model->insertBatchStagesCustomerPrices($arrStagesCustomerPrices);
                        }
                    } else {
                        $this->products_model->deleteStageCriteria($stage_id);
                        if (!empty($arrCriteria)) {
                            foreach ($arrCriteria as $k => $v) {
                                $this->products_model->insertStageCriteria($v);
                            }
                        }

                        $this->products_model->deleteStagesCustomerPrices($stage_id);
                        if (!empty($arrStagesCustomerPrices)) {
                            $this->products_model->insertBatchStagesCustomerPrices($arrStagesCustomerPrices);
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
            return;
        } else {
            $data['stage_criteria'] = $this->products_model->getStageCriterial($id);
            $data['categoryStages'] = $this->products_model->getCategoryStages();
            $data['stage'] = $stage;
            $data['actions'] = $actions;
            $this->db->select('tbl_category_stages.*');
            $this->db->from('tbl_category_stages');
            $this->db->where('tbl_category_stages.check_productivity', 1);
            $data['categoryStageNangSuat'] = $this->db->get()->result_array();
            $this->load->view('admin/products/edit_stage', $data);
        }
    }

    public function add_stage_sub($id)
    {
        $data = [];
        die;
        $stage = $this->products_model->rowStages($id);
        if (empty($stage)) {
            $data['result'] = 0;
            $data['message'] = lang('no_data_exists');
            echo json_encode($data);
            return;
        }
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_stages.code]');
            if ($this->form_validation->run() == true) {
                $name = $this->input->post('name');
                $code = $this->input->post('code');
                $departments = $this->input->post('departments');
                $number_hours = number_unformat($this->input->post('number_hours'));
                $sequence = $this->input->post('sequence');
                $note = $this->input->post('note');
                $status_qc = !empty($this->input->post('status_qc')) ? $this->input->post('status_qc') : 0;

                $options = [
                    'name' => $name,
                    'code' => $code,
                    'note' => $note,
                    'departments_id' => $departments,
                    'number_hours' => $number_hours,
                    'sequence' => $sequence,
                    'parent_id' => $id,
                    'status_qc' => $status_qc,
                ];

                $id = $this->products_model->insertStages($options);
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
            $data['departments'] = $this->departments_model->getDepartments();
            $data['stage'] = $stage;
            $data['id'] = $id;
            $this->load->view('admin/products/add_stage_sub', $data);
        }
    }

    public function edit_stage_sub($id)
    {
        $data = [];
        $stage = $this->products_model->rowStages($id);
        if (empty($stage)) {
            $data['result'] = 0;
            $data['message'] = lang('no_data_exists');
            echo json_encode($data);
            return;
        }
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($stage['code'] != $this->input->post('code')) {
                $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_stages.code]');
            }
            if ($this->form_validation->run() == true) {
                $name = $this->input->post('name');
                $code = $this->input->post('code');
                $note = $this->input->post('note');
                $departments = $this->input->post('departments');
                $number_hours = number_unformat($this->input->post('number_hours'));
                $sequence = $this->input->post('sequence');
                $status_qc = !empty($this->input->post('status_qc')) ? $this->input->post('status_qc') : 0;

                $options = [
                    'name' => $name,
                    'code' => $code,
                    'note' => $note,
                    'departments_id' => $departments,
                    'number_hours' => $number_hours,
                    'sequence' => $sequence,
                    'status_qc' => $status_qc
                ];

                $id = $this->products_model->updateStages($id, $options);
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
            $data['departments'] = $this->departments_model->getDepartments();
            $data['stage'] = $stage;
            $this->load->view('admin/products/edit_stage_sub', $data);
        }
    }

    function getStages()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=42949672895');
        $this->datatables->select("
            tbl_stages.id as id,
            tbl_stages.id as records,
            tbl_category_stages.code as code_category,
            tbl_category_stages.name as name_category,
            tbl_stages.code as code,
            tbl_stages.name as name,
            tbl_stages.time_watch_cards as time_watch_cards,
            tbl_stages.number_watch_cards as number_watch_cards,
            tbl_stages.status_qc as status_qc,
            tbl_stages.type as type,
            tbl_stages.note as note,
            '' as items,
            tbl_stages.status_default_outsource as status_default_outsource,
            tbl_stages.stage_import_outsource as stage_import_outsource,
            tbl_stages.formula_m2 as formula_m2,
            tbl_stages.is_be as is_be,
            tbl_stages.is_dantrang as is_dantrang,
            tbl_stages.is_ghepsize as is_ghepsize,
            tbl_stages.is_ghikem as is_ghikem,
            tbl_stages.check_productivity as check_productivity,
            tbl_stages.is_bangiao as is_bangiao
            ", FALSE)
            ->from('tbl_stages')
            ->join('tbl_category_stages', 'tbl_category_stages.id = tbl_stages.category_stages', 'left');
        $this->datatables->where('tbl_stages.type_use', 0);
        $this->datatables->where('tbl_stages.parent_id', 0);
        if ($this->input->post('category_stages')) {
            $category_stages = $this->input->post('category_stages');
            if (!is_array($category_stages)) {
                $category_stages = explode(',', $category_stages);
            }
            $this->db->where_in('tbl_stages.category_stages', $category_stages);
        }

        $add_sub = '<a class="tnh-modal btn btn-primary btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/products/add_stage_sub/$1"><i class="fa fa-plus"></i></a>';

        $edit = '<a class="tnh-modal btn btn-success btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/products/edit_stage/$1"><i class="fa fa-pencil"></i></a>';

        $copy = '<a class="tnh-modal btn btn-primary btn-icon" data-tnh="modal" title="' . lang('copy') . '" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/products/edit_stage/$1/copy"><i class="fa fa-copy"></i></a>';

        $delete = '<button type="button" class="btn btn-danger po btn-icon btn-delete" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                        <button href=\'' . base_url('admin/products/delete_stage/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                        <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
                    "><i class="fa fa-remove"></i></button>';

        $this->datatables->add_column('actions', '
            <div class="text-center">
                ' . $edit . '
                ' . $copy . '
                ' . $delete . '
            </div>
        ', 'id');
        $data = json_decode($this->datatables->generate());
        foreach ($data->aaData as $key => $value) {
            $id = $value[0];
            $this->db->select('tbl_stages.*, tbldepartments.name as departments_name')
                ->from('tbl_stages')
                ->join('tbldepartments', 'tbldepartments.departmentid = tbl_stages.departments_id', 'left')
                ->where('tbl_stages.parent_id =', $id);
            $this->db->order_by('tbl_stages.sequence ASC');
            $sub = $this->db->get()->result_array();
            $data->aaData[$key][11] = $sub;

            if ($id == STAGES_MATERIAL || $id == STAGES_COMMUNE) {
                $data->aaData[$key][20] = '';
            }
        }
        echo (json_encode($data));
    }
    function updateis_type_stages($id)
    {
        $data = $this->input->post();
        if ($data['isChecked'] === 'true') {
            if ($data['type'] == '1') {
                $updateData = ['is_be' => 1];
            } elseif ($data['type'] == '2') {
                $updateData = ['is_dantrang' => 1];
            } elseif ($data['type'] == '3') {
                $updateData = ['is_ghepsize' => 1];
            } elseif ($data['type'] == '4') {
                $updateData = ['is_ghikem' => 1];
            } elseif ($data['type'] == '5') {
                $updateData = ['check_productivity' => 1];
            } elseif ($data['type'] == '6') {
                $updateData = ['is_bangiao' => 1];
            }
        } else {
            if ($data['type'] == '1') {
                $updateData = ['is_be' => 0];
            } elseif ($data['type'] == '2') {
                $updateData = ['is_dantrang' => 0];
            } elseif ($data['type'] == '3') {
                $updateData = ['is_ghepsize' => 0];
            } elseif ($data['type'] == '4') {
                $updateData = ['is_ghikem' => 0];
            } elseif ($data['type'] == '5') {
                $updateData = ['check_productivity' => 0];
            } elseif ($data['type'] == '6') {
                $updateData = ['is_bangiao' => 0];
            }
        }
        $this->db->update('tbl_stages', $updateData, ['id' => $id]);
        $response = ['isSuccess' => true, 'message' => 'Thay đổi trạng thái thành công'];
        echo json_encode($response);
    }

    function updateis_type_category_stages($id)
    {
        $data = $this->input->post();
        if ($data['isChecked'] === 'true') {
            if ($data['type'] == '1') {
                $updateData = ['check_productivity' => 1];
            }
            if ($data['type'] == '2') {
                $updateData = ['is_bangiao' => 1];
            }
        } else {
            if ($data['type'] == '1') {
                $updateData = ['check_productivity' => 0];
            }
            if ($data['type'] == '2') {
                $updateData = ['is_bangiao' => 0];
            }
        }
        $this->db->update('tbl_category_stages', $updateData, ['id' => $id]);
        $response = ['isSuccess' => true, 'message' => 'Thay đổi trạng thái thành công'];
        echo json_encode($response);
    }
    function searchStages()
    {
        $data = [];
        if ($this->input->get()) {
            $q = $this->input->get('q');
            $limit = 50;
            $data = $this->products_model->searchStages($q, $limit);
        }
        echo json_encode($data);
    }

    function delete_stage($id)
    {
        $data = [];
        if ($id) {
            // if (!$this->products_model->checkExistCategory($id)) {
            //     $data['result'] = 0;
            //     $data['message'] = lang('tnh_exist_not_delete');
            //     echo json_encode($data);
            //     return;
            // }

            if ($id == STAGE_PRINT_BARCODE) {
                $data['result'] = 0;
                $data['message'] = lang('Giai đoạn này mặc định hệ thống không được xóa');
                echo json_encode($data);
                die;
            }

            if ($id == STAGES_MATERIAL) {
                $data['result'] = 0;
                $data['message'] = lang('Chuận bị NPL không thể xóa');
                echo json_encode($data);
                die;
            }

            if ($id == STAGES_COMMUNE) {
                $data['result'] = 0;
                $data['message'] = lang('Xã không thể xóa');
                echo json_encode($data);
                die;
            }

            if (!empty($this->products_model->checkStagesByParentId($id))) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_please_remove_sub_items');
                echo json_encode($data);
                die;
            }
            if ($this->products_model->checkStagesExist($id)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_exist_not_delete');
                echo json_encode($data);
                die;
            }
            if ($this->products_model->deleteStages($id)) {
                $this->products_model->deleteStageCriteria($id);
                $this->products_model->deleteStagesCustomerPrices($id);
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

    function check_product_stages()
    {
        $product_id = $this->input->post('product_id');
        $versions = trim($this->input->post('versions'));
        if ($this->products_model->checkProductStages($product_id, $versions)) {
            $this->form_validation->set_message('check_product_stages', lang('exists_versions_products'));
            return false;
        } else {
            return true;
        }
    }

    function design_stages($id, $vs_stage_id = false)
    {
        if (!$this->perAddProducts) {
            accessDenied($js = true);
        }
        $products = $this->products_model->rowProduct($id);
        if ($products['type_products'] == 'semi_products_outside') {
            refererModel(lang('semi_products_outside_not_design_stages'));
        }
        if (!empty($vs_stage_id)) {
            $stage = $this->products_model->rowProductStagesById($vs_stage_id);
        }
        if ($this->input->post()) {
            $this->form_validation->set_rules('product_id', lang("id"), 'required');
            if (empty($stage) || ($stage['product_id'] != $this->input->post('product_id') && $stage['versions'] != $this->input->post('versions'))) {
                $this->form_validation->set_rules('versions', lang("tnh_versions"), 'required|callback_check_product_stages');
            }
            if ($this->form_validation->run() == true) {
                // print_arrays($this->input->post());
                $status = "unapplication";
                $versions = trim($this->input->post('versions'));
                $product_id = $this->input->post('product_id');
                $i_stage = $this->input->post('i_stage');
                $stage = $this->input->post('stage');
                $options['versions'] = $versions;
                $options['product_id'] = $product_id;
                $final_stage = $this->input->post('final_stage');
                $use_version = $this->input->post('use_version');
                $isFinal = false;
                foreach ($i_stage as $key => $value) {
                    $stage = $this->input->post('stage')[$key];
                    $number = $this->input->post('number')[$key];
                    $number_hours = number_unformat($this->input->post('number_hours')[$key]);

                    $type = !empty($this->input->post('type')[$key]) ? $this->input->post('type')[$key] : 0;
                    if ($stage == STAGES_MATERIAL) {
                        $type = 6;
                    }

                    $machines = !empty($this->input->post('machines')[$value]) ? implode(',', $this->input->post('machines')[$value]) : NULL;
                    $final = ($final_stage == $value) ? 1 : 0;
                    if ($final) {
                        $isFinal = true;
                    }

                    $face = !empty($this->input->post('face')[$value]) ? $this->input->post('face')[$value] : 0;
                    $face_after = !empty($this->input->post('face_after')[$value]) ? $this->input->post('face_after')[$value] : 0;
                    $number_face = !empty($this->input->post('number_face')[$value]) ? number_unformat($this->input->post('number_face')[$value]) : 0;
                    $number_operations = !empty($this->input->post('number_operations')[$value]) ? number_unformat($this->input->post('number_operations')[$value]) : 0;
                    $number_cutting = !empty($this->input->post('number_cutting')[$value]) ? number_unformat($this->input->post('number_cutting')[$value]) : 0;
                    $quota_time_f1 = !empty($this->input->post('quota_time_f1')[$value]) ? number_unformat($this->input->post('quota_time_f1')[$value]) : 0;
                    $quota_time_f2 = !empty($this->input->post('quota_time_f2')[$value]) ? number_unformat($this->input->post('quota_time_f2')[$value]) : 0;

                    $options['items'][$key]['stage'] = $stage;
                    $options['items'][$key]['number'] = $number;
                    $options['items'][$key]['number_hours'] = $number_hours;
                    $options['items'][$key]['machines'] = $machines;
                    $options['items'][$key]['final_stage'] = $final;
                    $options['items'][$key]['type'] = $type;
                    $options['items'][$key]['face'] = $face;
                    $options['items'][$key]['face_after'] = $face_after;
                    $options['items'][$key]['number_face'] = $number_face;
                    $options['items'][$key]['number_operations'] = $number_operations;
                    $options['items'][$key]['number_cutting'] = $number_cutting;
                    $options['items'][$key]['quota_time_f1'] = $quota_time_f1;
                    $options['items'][$key]['quota_time_f2'] = $quota_time_f2;
                }

                if (!$isFinal) {
                    $data['result'] = 0;
                    $data['message'] = lang('Vui lòng chọn công đoạn cuối');
                    echo json_encode($data);
                    die;
                }

                // print_arrays($options);
                $q = $this->products_model->insertProductStages($options, $status, $vs_stage_id);
                if ($q) {
                    if (!empty($use_version)) {
                        $this->products_model->updateProducts($product_id, ['versions_stage' => $versions]);
                    }

                    $data['product_id'] = $product_id;
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
            $data['id'] = $id;
            $html_stages = '';
            $list_stages = recursive_stages();

            if (!empty($vs_stage_id)) {
                $items = $this->products_model->getProductStagesVersions($vs_stage_id);
                foreach ($items as $key => $value) {

                    // $optionsStages = '';
                    // if (!empty($list_stages)) {
                    //     print_arrays($list_stages);
                    //     foreach ($list_stages as $i => $v) {
                    //         $optionsStages.= '<option value="'.$v['id'].'" '.($value['stage_id'] == $v['id'] ? 'selected' : '').'>'.$v['name'].'</option>';
                    //     }
                    // }

                    // <option value="'.$value['stage_id'].'" selected>'.$value['stage_name'].'</option>

                    // <input type="hidden" name="face[]" class="form-control face" value="'.$value['face'].'">
                    // <input type="hidden" name="face_after[]" class="form-control face" value="'.$value['face_after'].'">
                    // '.($value['face'] == 1 ? '<div class="text-danger mtop5">Mặt trước</div>' : '').'
                    // '.($value['face_after'] == 2 ? '<div class="text-danger mtop5">Mặt sau</div>' : '').'

                    $html_stages .= '<tr class="sortable item">
                                        <input type="hidden" name="i_stage[]" id="i_stage" class="form-control i_stage" value="' . $key . '">
                                        <input type="hidden" name="number[]" id="number" class="form-control number" value="' . $value['number'] . '">
                                        <td class="stt text-center dragger">' . $value['number'] . '</td>

                                        <td>
                                            <select name="stage[]"  data-live-search="true" onchange="_changeStage(this)" data-none-selected-text="' . lang('choose') . '" id="stage_' . $key . '" class="form-control" required="required">
                                                <option value="' . $value['stage_id'] . '" selected>' . $value['stage_name'] . '</option>
                                                ' . $list_stages . '
                                            </select>
                                            <div class="checkbox checkbox-info" style="margin-top: 5px;">
                                                <input type="checkbox" ' . ($value['face'] == 1 ? 'checked' : '') . ' name="face[' . $key . ']" id="face_' . $key . '" value="1">
                                                <label for="face_' . $key . '">Mặt trước</label>
                                            </div>
                                            <div class="checkbox checkbox-info">
                                                <input type="checkbox" ' . ($value['face_after'] == 2 ? 'checked' : '') . ' name="face_after[' . $key . ']" id="face_after_' . $key . '" value="2">
                                                <label for="face_after_' . $key . '">Mặt sau</label>
                                            </div>
                                        </td>';
                    // <td>';
                    $options = '';
                    if (!empty($value['machines'])) {
                        $machines = $this->category_model->getMachinesByArrId(explode(',', $value['machines']));
                        if (!empty($machines)) {
                            foreach ($machines as $i => $v) {
                                $options .= '<option value="' . $v['id'] . '" selected>' . $v['code'] . ' (' . $v['name'] . ')' . '</option>';
                            }
                        }
                    }

                    // <option '.($value['type'] == 1 ? 'selected' : '').' value="1">'.lang('tnh_semi_finished_product').'</option>
                    $selectType = '';
                    if ($value['stage_id'] == STAGES_MATERIAL) {
                        $selectType = '<input type="hidden" name="type[]" value="0">';
                    } else {
                        $selectType = '<select name="type[]" data-none-selected-text="' . lang('type') . '" class="form-control type selectpicker">
                            <option value=""></option>
                            <option ' . ($value['type'] == 2 ? 'selected' : '') . ' value="2">' . lang('tnh_unfinished_product') . '</option>
                        </select>';
                    }

                    // <td>
                    //                         <input type="number" name="number_hours[]" id="input" class="form-control" value="'.$value['number_hours'].'" title="">
                    //                     </td>
                    $html_stages .=     '</td>
                                        <td>
                                            ' . $selectType . '
                                        </td>
                                        <td>
                                            <div class="radio radio-primary">
                                                <input type="radio" name="final_stage" id="final_stage_' . $key . '" value="' . $key . '" ' . ($value['final_stage'] == 1 ? "checked" : '') . '>
                                                <label for="final_stage_' . $key . '">' . lang('choose') . '</label>
                                            </div>
                                        </td>
                                        <td><select name="machines[' . $key . '][]"  data-live-search="true" data-none-selected-text="' . lang('tnh_machines') . '" id="machines_' . $key . '" class="form-control ajax-search">\
                                            <option value=""></option>\
                                            ' . $options . '
                                        </select></td>
                                        <td>
                                            <input type="text" name="number_face[]" placeholder="' . lang('Số lần/trên mặt') . '" class="form-control number-format" value="' . $value['number_face'] . '">
                                        </td>
                                        <td>
                                            <input type="text" name="number_operations[]" placeholder="' . lang('Số lần vận hành') . '" class="form-control number-format" value="' . $value['number_operations'] . '">
                                        </td>
                                        <td>
                                            <input type="text" name="number_cutting[]" placeholder="' . lang('Số đường dao cắt') . '" class="form-control number-format" value="' . $value['number_cutting'] . '">
                                        </td>
                                        <td>
                                            <input type="text" name="quota_time_f1[]" placeholder="' . lang('Định mức TG canh bài mặt 1') . '" class="form-control number-format" value="' . $value['quota_time_f1'] . '">
                                        </td>
                                        <td>
                                            <input type="text" name="quota_time_f2[]" placeholder="' . lang('Định mức TG canh bài mặt 2') . '" class="form-control number-format" value="' . $value['quota_time_f2'] . '">
                                        </td>
                                        <td>
                                            ' . ($value['stage_id'] != STAGES_MATERIAL ? '<div class="text-center"><i class="btn btn-danger fa fa-remove remove-stage"></i></div>' : '') . '
                                        </td>
                                    </tr>';
                }
                $data['items'] = $items;
                $data['stage'] = $stage;
            }

            $data['list_stages_array'] = recursive_stages_array();
            $data['list_stages'] = $list_stages;
            $data['html_stages'] = $html_stages;
            $this->load->view('admin/products/design_stages', $data);
        }
    }

    public function delete_product_stage($id)
    {
        if (!$this->perAddProducts) {
            accessDenied($js = true);
        }
        $data = [];
        if (!empty($id)) {
            if ($this->products_model->deleteProductStagesById($id)) {
                $data['result'] = 1;
                $data['message'] = lang('success');
                $data['table'] = $id;
                $data['type'] = 'stages';
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

    function change_versions_stages()
    {
        if (!$this->perAddProducts) {
            accessDenied($js = true);
        }
        $data = [];
        if ($this->input->post()) {
            $product_id = $this->input->post('product_id');
            $vs_stage = $this->input->post('vs_stage');
            if ($this->products_model->updateProducts($product_id, ['versions_stage' => $vs_stage])) {
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


    function gen_barcode($product_code = NULL, $bcs = 'code128', $height = 30, $text = 1)
    {
        ob_end_clean();
        // $product_code = preg_replace('/[\(\)]/', '', 'TKD6-10 (SRI)');
        $drawText = ($text != 1) ? FALSE : TRUE;
        $this->load->library('zend');
        $this->zend->load('Zend/Barcode');
        $barcodeOptions = array('text' => $product_code, 'barHeight' => $height, 'drawText' => $drawText);
        $rendererOptions = array('horizontalPosition' => 'center', 'verticalPosition' => 'middle');
        Zend_Barcode::render('code128', 'image', $barcodeOptions, $rendererOptions);
    }

    function bom()
    {
        if (!$this->perViewProductsBom) {
            accessDenied();
        }
        $data['tnh'] = true;
        $data['title'] = _l('tnh_bom');
        $this->load->view('admin/products/bom', $data);
    }

    function getBom()
    {
        if (!$this->perViewProductsBom) {
            accessDenied($js = true);
        }

        $materials_search = $this->input->post('materials_search');

        $BOM = "(
            SELECT
                tbl_product_versions.product_id,
                GROUP_CONCAT(CONCAT(tbl_product_versions.versions, '___', tbl_product_versions.id) SEPARATOR ':::') as versions
            FROM tbl_product_versions
            GROUP BY tbl_product_versions.product_id
        ) as BOM";

        $this->datatables->select("
            tbl_products.id as id,
            '0' as records,
            tbl_products.code as code,
            tbl_products.name as name,
            BOM.versions as bm,
            tblunits.unit as unit_name,
            1 as quantity_productions,
            tbl_products.versions as vs,
            ", FALSE)
            ->from('tbl_products')
            ->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left')
            ->join($BOM, 'BOM.product_id = tbl_products.id', 'left');

        if (!empty($materials_search)) {
            $this->datatables->where(' exists (
                SELECT tbl_product_versions.id
                FROM tbl_product_versions
                INNER JOIN tbl_versions_element ON tbl_versions_element.version_id = tbl_product_versions.id
                INNER JOIN tbl_element_items ON tbl_element_items.element_id = tbl_versions_element.id
                WHERE tbl_product_versions.product_id = tbl_products.id AND tbl_element_items.type = "materials" AND tbl_element_items.item_id = ' . $materials_search . '
            )', false, false);
        }

        $this->datatables->where('tbl_products.versions !=', NULL);
        $iDisplayStart = $this->input->post('iDisplayStart');
        $data = json_decode($this->datatables->generate());
        // foreach ($data->aaData as $key => $value) {
        //     $data->aaData[$key][1] = ++$iDisplayStart;
        // }
        echo json_encode($data);
    }

    function show_bom()
    {
        $product_id = $this->input->post('product_id');
        $vs = $this->input->post('vs');
        $quantity = $this->input->post('quantity');
        if (empty($product_id) || empty($vs)) {
            echo lang('not_data_exists');
            die;
        }
        $html_bom = '';
        $version = $this->products_model->getBomByProductIdAndVersions($product_id, $vs);
        if (!empty($version)) {
            $elements = $this->products_model->getVersionsElementByVersionId($version['id']);
            $number_element = 1;
            foreach ($elements as $key => $value) {
                $quantity_element = $quantity * $value['quantity'];
                $html_bom .= '<tr style="background: #80808085;">';
                $html_bom .= '<td class="text-center">' . $number_element . '</td>';
                $html_bom .= '<td>' . $value['element_name'] . '</td>';
                // $html_bom .= '<td>'.$value['element_name'].'</td>';
                $html_bom .= '<td></td>';
                $html_bom .= '<td></td>';
                // $html_bom .= '<td>'.lang('tnh_element').'</td>';
                $html_bom .= '<td></td>';
                $html_bom .= '<td class="text-center"></td>';
                $html_bom .= '<td class="text-center"></td>';
                $html_bom .= '<td class="text-center"></td>';
                $html_bom .= '<td class="text-center"></td>';
                $html_bom .= '<td class="text-center"></td>';
                $html_bom .= '<td class="text-center"></td>';
                $html_bom .= '</tr>';

                $items = $this->products_model->getElementItemsByElementId($value['id']);
                $number_item = 1;
                foreach ($items as $k => $val) {
                    if ($val['type'] == "semi_products") {
                        $info = $this->products_model->rowProduct($val['item_id']);
                    } else {
                        $info = $this->items_model->rowMaterial($val['item_id']);
                    }
                    $quantity_item = $value['quantity'] * $quantity * $val['quantity'];

                    $stage = get_table_where('tbl_stages', ['id' => $val['stage_id']], '', 'row_array');

                    $unit = $this->unit_model->rowUnit($val['unit_id']);
                    $html_bom .= '<tr>';
                    $html_bom .= '<td class="text-center">' . $number_element . '.' . $number_item . '</td>';
                    $html_bom .= '<td>' . $info['code'] . '</td>';
                    $html_bom .= '<td>' . $info['name'] . '</td>';
                    $html_bom .= '<td>' . $unit['unit'] . '</td>';
                    $html_bom .= '<td>' . lang($val['type']) . '</td>';
                    $html_bom .= '<td class="text-center">' . $val['landscape_print_size'] . '</td>';
                    $html_bom .= '<td class="text-center">' . $val['number_children_size'] . '</td>';
                    $html_bom .= '<td class="text-center">' . $val['quantity'] . '</td>';
                    $html_bom .= '<td class="text-center">' . formatNumber($val['paper_exchange']) . '</td>';
                    $html_bom .= '<td class="text-center">' . $val['quantity_compensation'] . '</td>';
                    $html_bom .= '<td class="text-center">' . $stage['name'] . '</td>';
                    $html_bom .= '</tr>';
                    $number_item++;
                }
                $number_element++;
            }
        }
        $data['product_id'] = $product_id;
        $data['html_bom'] = $html_bom;
        $this->load->view('admin/products/show_bom', $data);
    }

    function print_bom()
    {
        if (!$this->perPrintProductsBom) {
            accessDenied($js = true);
        }
        $arr_id = $this->input->post('arr_id');
        $arr_vs = $this->input->post('arr_vs');
        $arr_quantity = $this->input->post('arr_quantity');
        $html_bom = '';
        if (!empty($arr_id)) {
            $number_product = 1;
            foreach ($arr_id as $key => $value) {
                $product_id = $value;
                $vs = $arr_vs[$key];
                $quantity = $arr_quantity[$key];

                $product = $this->products_model->rowProduct($product_id);
                $unit = $this->unit_model->rowUnit($product['unit_id']);

                $html_bom .= '<tr>';
                $html_bom .= '<td class="">' . $number_product . '</td>';
                $html_bom .= '<td>' . $product['code'] . '</td>';
                $html_bom .= '<td>' . $product['name'] . '</td>';
                $html_bom .= '<td>' . $vs . '</td>';
                $html_bom .= '<td>' . $unit['unit'] . '</td>';
                $html_bom .= '<td>' . lang('products') . '</td>';
                $html_bom .= '<td style="text-align: center;" class="text-center"></td>';
                $html_bom .= '<td style="text-align: center;" class="text-center"></td>';
                $html_bom .= '<td style="text-align: center;" class="text-center"></td>';
                $html_bom .= '<td style="text-align: center;" class="text-center"></td>';
                $html_bom .= '<td style="text-align: center;" class="text-center"></td>';
                $html_bom .= '<td style="text-align: center;" class="text-center"></td>';
                $html_bom .= '</tr>';

                $version = $this->products_model->getBomByProductIdAndVersions($product_id, $vs);
                if (!empty($version)) {
                    $elements = $this->products_model->getVersionsElementByVersionId($version['id']);
                    $number_element = 1;
                    foreach ($elements as $key => $value) {
                        $quantity_element = $quantity * $value['quantity'];
                        $html_bom .= '<tr>';
                        $html_bom .= '<td class="">' . $number_product . '.' . $number_element . '</td>';
                        $html_bom .= '<td>' . $value['element_name'] . '</td>';
                        $html_bom .= '<td>' . $value['element_name'] . '</td>';
                        $html_bom .= '<td></td>';
                        $html_bom .= '<td></td>';
                        $html_bom .= '<td>' . lang('tnh_element') . '</td>';
                        $html_bom .= '<td style="text-align: center;" class="text-center"></td>';
                        $html_bom .= '<td style="text-align: center;" class="text-center"></td>';
                        $html_bom .= '<td style="text-align: center;" class="text-center"></td>';
                        $html_bom .= '<td style="text-align: center;" class="text-center"></td>';
                        $html_bom .= '<td style="text-align: center;" class="text-center"></td>';
                        $html_bom .= '<td style="text-align: center;" class="text-center"></td>';
                        $html_bom .= '</tr>';

                        $items = $this->products_model->getElementItemsByElementId($value['id']);
                        $number_item = 1;
                        foreach ($items as $k => $val) {
                            if ($val['type'] == "semi_products") {
                                $info = $this->products_model->rowProduct($val['item_id']);
                            } else {
                                $info = $this->items_model->rowMaterial($val['item_id']);
                            }
                            $quantity_item = $value['quantity'] * $quantity * $val['quantity'];

                            $stage = get_table_where('tbl_stages', ['id' => $val['stage_id']], '', 'row_array');

                            $unit = $this->unit_model->rowUnit($val['unit_id']);
                            $html_bom .= '<tr>';
                            $html_bom .= '<td class="">' . $number_product . '.' . $number_element . '.' . $number_item . '</td>';
                            $html_bom .= '<td>' . $info['code'] . '</td>';
                            $html_bom .= '<td>' . $info['name'] . '</td>';
                            $html_bom .= '<td></td>';
                            $html_bom .= '<td>' . $unit['unit'] . '</td>';
                            $html_bom .= '<td>' . lang($val['type']) . '</td>';
                            $html_bom .= '<td style="text-align: center;" class="text-center">' . $val['landscape_print_size'] . '</td>';
                            $html_bom .= '<td style="text-align: center;" class="text-center">' . $val['number_children_size'] . '</td>';
                            $html_bom .= '<td style="text-align: center;" class="text-center">' . $val['quantity'] . '</td>';
                            $html_bom .= '<td style="text-align: center;" class="text-center">' . formatNumber($val['paper_exchange']) . '</td>';
                            $html_bom .= '<td style="text-align: center;" class="text-center">' . $val['quantity_compensation'] . '</td>';
                            $html_bom .= '<td style="text-align: center;" class="text-center">' . $stage['name'] . '</td>';
                            $html_bom .= '</tr>';
                            $number_item++;
                        }
                        $number_element++;
                    }
                }
                $number_product++;
            }
        }
        $data['html_bom'] = $html_bom;
        $this->load->view('admin/products/print_bom', $data);
    }

    function export_excel_category()
    {
        if (!$this->perExportCategory) {
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
            $this->db->from('tbl_category_products');
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

            $filename = lang('tnh_category_product') . '.xls';
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
            $data['link'] = 'admin/products/export_excel_category';
            $list = [];
            $fields = get_fields_export($table = 'tbl_category_products', $arr_diff = false);
            foreach ($fields as $key => $value) {
                $list[] = [$value => mb_strtoupper(_l('tnh_' . $value), 'UTF-8')];
            }
            $data['list'] = $list;
            $this->load->view('admin/export_excel/export_excel', $data);
        }
    }

    function export_excel_products_old()
    {
        if (!$this->perExportProducts) {
            accessDenied($js = true);
        }
        if ($this->input->post('export_excel')) {
            $limit_start = $this->input->post('limit_start');
            $limit_end = $this->input->post('limit_end');

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

            $select = '';
            $left_join = '';

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
                                        WHERE tblcustomfieldsvalues.fieldto = 'products' AND tblcustomfieldsvalues.relid = tbl_products.id AND tblcustomfieldsvalues.fieldid = " . $val['id'] . "
                                    ), '') as $value, ";
                            $custom = true;
                            break;
                        }
                    }
                }
                if ($custom == true) continue;
                //end custom fields

                $objPHPExcel->getActiveSheet()->SetCellValue($cloumns_excel[$key] . '1', _l('tnh_' . $value))->getStyle($cloumns_excel[$key] . '1')->applyFromArray($style_excel['Background_header']);

                if ($value == "category_id") {
                    $value = 'category_name';
                    $cloumns[$key] = $value;
                    $select .= "tbl_category_products.name as $value, ";
                    $left_join .= " LEFT JOIN tbl_category_products ON tbl_category_products.id = tbl_products.category_id";
                } else if ($value == "unit_id") {
                    $value = 'unit_name';
                    $cloumns[$key] = $value;
                    $select .= "tblunits.unit as $value, ";
                    $left_join .= " LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id";
                } else if ($value == "versions_bom") {
                    $value = 'versions_bom';
                    $cloumns[$key] = $value;
                    $select .= "tbl_products.versions as $value, ";
                } else if ($value == "colors") {
                    $colors = "(
                        SELECT
                            tbl_products_colors.product_id,
                            GROUP_CONCAT(tbl_colors.name SEPARATOR '\n') as color_name
                        FROM tbl_products_colors
                        INNER JOIN tbl_colors ON tbl_products_colors.color_id = tbl_colors.id
                        GROUP BY tbl_products_colors.product_id
                    ) as colors";

                    $value = 'color_name';
                    $cloumns[$key] = $value;
                    $select .= "colors.color_name as $value, ";
                    $left_join .= " LEFT JOIN $colors ON colors.product_id = tbl_products.id";
                } else if ($value == "id_branch") {
                    $value = 'branch_name';
                    $cloumns[$key] = $value;
                    $select .= "tblbranch.name as $value, ";
                    $left_join .= " LEFT JOIN tblbranch ON tblbranch.id = tbl_products.id_branch";
                } else if (
                    $value == "id_standard_carry"
                    || $value == "id_standard_sample_cover"
                    || $value == "id_standard_smooth_shine"
                    || $value == "id_standard_fsc"
                    || $value == "id_standard_delivery_package"
                    || $value == "id_standard_membrane"
                    || $value == "id_standard_template"
                    || $value == "id_standard_condition_color"
                    || $value == "id_standard_color"
                    || $value == "id_standard_bin_carton"
                    || $value == "id_standard_trame"
                    || $value == "id_standard_sample_code"
                    || $value == "id_standard_methods"
                    || $value == "id_standard_quality_standards"
                ) {
                    //                    $cloumns[$key] = $value;
                    $select .= "tbl$value.code as $value, ";
                    $left_join .= " LEFT JOIN tbllist_other tbl$value ON tbl$value.id = tbl_products.$value";
                } else {
                    $select .= "tbl_products.$value, ";
                }
            }

            $select = trim($select);
            $select = substr($select, 0, -1);

            $limit = '';
            if (is_numeric($limit_start) && is_numeric($limit_end)) {
                $limit = ' LIMIT ' . ($limit_start) . ', ' . ($limit_end - $limit_start + 1);
            }

            $query = "
                SELECT $select
                FROM tbl_products
                $left_join
				$limit
            ";

            $data = $this->db->query($query)->result_array();
            // print_arrays($data);
            $this->load->library('ciqrcode');
            $row = 2;
            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    foreach ($cloumns as $k => $val) {
                        $index = $cloumns_excel[$k] . $row;
                        $el = $value[$val];
                        if ($val == "price_import" || $val == "price_sell" || $val == "price_processing") {
                            $objPHPExcel->getActiveSheet()->SetCellValue($index, $el)->getStyle($index)->applyFromArray($style_excel['BStyle']);
                            $objPHPExcel->getActiveSheet()->getStyle($index)->getNumberFormat()->setFormatCode('#,##0.00');
                        } else if ($val == "images") {
                            // $objPHPExcel->getActiveSheet()->SetCellValue($index, !empty($el) ? base_url('uploads/materials/').''.$el : '')->getStyle($index)->applyFromArray($style_excel['BStyle']);
                            $objPHPExcel->getActiveSheet()->SetCellValue($index, !empty($el) ? $el : '')->getStyle($index)->applyFromArray($style_excel['BStyle']);
                        } else if ($val == "type_products") {
                            $objPHPExcel->getActiveSheet()->SetCellValue($index, lang($el))->getStyle($index)->applyFromArray($style_excel['BStyle']);
                        } else if ($val == 'qr') {
                            $code = $value['code'];
                            $qr = $value['code'];
                            $folder = FCPATH . 'uploads/products/';
                            $params['data'] = $code;
                            $params['level'] = 'H';
                            $params['size'] = 40;
                            $params['savename'] = $folder . 'qrcode/' . $qr . '.png';
                            $this->ciqrcode->generate($params);
                            $img = ($folder . 'qrcode/' . $qr . '.png');
                            if (!empty($img)) {
                                $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                                $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                                $objDrawing1->setPath($img);
                                $objDrawing1->setWidth(90);
                                $objDrawing1->setHeight(65);
                                $objDrawing1->setOffsetX(20);
                                $objDrawing1->setOffsetY(2);
                                $objDrawing1->setCoordinates($index);
                            }
                            $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(60);
                            $objPHPExcel->getActiveSheet()->setCellValue($index, $el)->getStyle($index)->applyFromArray($style_excel['BStyle'])->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        } else {
                            $objPHPExcel->getActiveSheet()->SetCellValue($index, $el)->getStyle($index)->applyFromArray($style_excel['BStyle']);
                        }
                    }
                    $row++;
                }
            }

            $filename = lang('products') . '.xls';
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
            // $data['link'] = 'admin/products/export_excel_products';
            $data['link'] = 'admin/products/export_excel_products_old';
            $list = [];
            $fields = get_fields_export(
                $table = 'tbl_products',
                $arr_diff = [
                    'info',
                    'calculated_on_sales',
                    'warranty',
                    'colum_vt1',
                    'colum_vt2',
                    'colum_vt3',
                    'colum_vt4',
                    'is_no_stock',
                    'max_price',
                    'total_business_plan',
                    'total_transfer_business'
                ],
                $arr_more = [
                    'colors',
                    'qr'
                ]
            );
            foreach ($fields as $key => $value) {
                if ($value == "versions") $value = "versions_bom";
                $list[] = [$value => mb_strtoupper(_l('tnh_' . $value), 'UTF-8')];
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
        if (!$this->perAddCategory) {
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
                    $row_parent = $this->products_model->rowCategoryProductsByCode($parent, 'id', 'where');
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
                if ($this->products_model->checkCategoryProductsByCode($code)) {
                    $errors .= '<div class="text-danger">' . $code . ' ' . lang('tnh_exist_data') . '</div>';
                    continue;
                }
                $id = $this->products_model->insertCategoryProducts($options);
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
        } else {
            $data['tnh'] = true;
            $data['title'] = _l('tnh_import_excel');
            $list = [];
            $fields = get_fields_export($table = 'tbl_category_products', $arr_diff = ['id']);
            foreach ($fields as $key => $value) {
                $list[$value] = mb_strtoupper(lang('tnh_' . $value), 'UTF-8');
            }
            $required = [lang('tnh_name'), lang('tnh_code')];
            $data['list'] = $list;
            $data['required'] = $required;
            $this->load->view('admin/products/import_category', $data);
        }
    }

    public function import_products()
    {
        if (!$this->perAddProducts) {
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

            //option category
            $category_id_1 = $this->input->post('category_id_1'); //where or like
            $category_id_2 = $this->input->post('category_id_2'); //add or continue
            //unit
            $unit_id_1 = $this->input->post('unit_id_1'); //where or like
            $unit_id_2 = $this->input->post('unit_id_2'); //add or continue
            //colors
            $colors_1 = $this->input->post('colors_1'); //where or like
            $colors_2 = $this->input->post('colors_2'); //add or continue

            //exchange
            $exchange_1 = $this->input->post('exchange_1'); //where or like
            $exchange_2 = $this->input->post('exchange_2'); //add or continue

            //species
            $species_1 = $this->input->post('species_1'); //where or like
            $species_2 = $this->input->post('species_2'); //add or continue

            //size
            $size_1 = $this->input->post('size_1'); //where or like
            $size_2 = $this->input->post('size_2'); //add or continue

            $conversion_unit_1 = $this->input->post('conversion_unit_1'); //where or like
            $conversion_unit_2 = $this->input->post('conversion_unit_2'); //add or continue

            //unit
            $unit_measure_1 = $this->input->post('unit_measure_1'); //where or like
            $unit_measure_2 = $this->input->post('unit_measure_2'); //add or continue

            //unit
            $brand_id_1 = $this->input->post('brand_id_1'); //where or like
            $brand_id_2 = $this->input->post('brand_id_2'); //add or continue
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
                $Template['type'] = 'product';
                $Template['date_create'] = date('Y-m-d H:i:s');
                $this->db->insert('tbltemplate_import', $Template);
            }
            //end

            // print_arrays($arraydata);
            $rowT = $row_start - 1;
            $actions = $this->input->post('actions');
            if ($actions == "add") {
                foreach ($arraydata as $key => $value) {
                    $category = !empty($value['category_id']) ? trim($value['category_id']) : '';
                    $type_products = !empty($value['type_products']) ? trim($value['type_products']) : '';
                    $code = !empty($value['code']) ? trim($value['code']) : '';
                    $name = !empty($value['name']) ? trim($value['name']) : '';
                    $price_import = !empty($value['price_import']) ? number_unformat($value['price_import']) : 0;
                    $price_sell = !empty($value['price_sell']) ? number_unformat($value['price_sell']) : 0;
                    $price_processing = !empty($value['price_processing']) ? number_unformat($value['price_processing']) : 0;
                    $unit = !empty($value['unit_id']) ? trim($value['unit_id']) : '';
                    $mode = !empty($value['mode']) ? trim($value['mode']) : '';
                    $note = !empty($value['note']) ? trim($value['note']) : '';
                    $number_labor = !empty($value['number_labor']) ? number_unformat($value['number_labor']) : 0;
                    $quantity_minimum = !empty($value['quantity_minimum']) ? number_unformat($value['quantity_minimum']) : 0;
                    $quantity_max = !empty($value['quantity_max']) ? number_unformat($value['quantity_max']) : 0;
                    $colors = !empty($value['colors']) ? trim($value['colors']) : '';
                    $bom_version = !empty($value['bom_id']) ? trim($value['bom_id']) : '';
                    $exchange = !empty($value['exchange']) ? trim($value['exchange']) : '';

                    $name_customer = !empty($value['name_customer']) ? trim($value['name_customer']) : '';
                    $name_supplier = !empty($value['name_supplier']) ? trim($value['name_supplier']) : '';
                    $number_hours_ap = !empty($value['number_hours_ap']) ? trim($value['number_hours_ap']) : '';
                    $number_day = !empty($value['number_day']) ? number_unformat($value['number_day']) : 0;
                    $size = !empty($value['size']) ? trim($value['size']) : 0;

                    $mode_product = !empty($value['mode_product']) ? trim($value['mode_product']) : '';
                    $stage_mode = !empty($value['stage_mode']) ? trim($value['stage_mode']) : '';
                    $stage_standard = !empty($value['stage_standard']) ? trim($value['stage_standard']) : '';
                    $operating_gauge = !empty($value['operating_gauge']) ? trim($value['operating_gauge']) : '';
                    $quota_productivity_h = !empty($value['quota_productivity_h']) ? number_unformat($value['quota_productivity_h']) : '';
                    $quota_power_consumption_h = !empty($value['quota_power_consumption_h']) ? number_unformat($value['quota_power_consumption_h']) : '';
                    $quota_material_replace_t = !empty($value['quota_material_replace_t']) ? number_unformat($value['quota_material_replace_t']) : '';
                    $quota_depreciation_ts_date = !empty($value['quota_depreciation_ts_date']) ? number_unformat($value['quota_depreciation_ts_date']) : '';
                    $quota_npl_consumption_one = !empty($value['quota_npl_consumption_one']) ? number_unformat($value['quota_npl_consumption_one']) : '';
                    $quota_time_change_one = !empty($value['quota_time_change_one']) ? number_unformat($value['quota_time_change_one']) : '';
                    $person_charge = !empty($value['person_charge']) ? trim($value['person_charge']) : '';
                    $property_grant = !empty($value['property_grant']) ? trim($value['property_grant']) : '';
                    $completion_standard = !empty($value['completion_standard']) ? trim($value['completion_standard']) : '';
                    $control_criteria = !empty($value['control_criteria']) ? trim($value['control_criteria']) : '';
                    $productivity_m_w_n = !empty($value['productivity_m_w_n']) ? trim($value['productivity_m_w_n']) : '';
                    $quality_problem = !empty($value['quality_problem']) ? trim($value['quality_problem']) : '';
                    $incident_record = !empty($value['incident_record']) ? trim($value['incident_record']) : '';
                    $operating_procedure = !empty($value['operating_procedure']) ? trim($value['operating_procedure']) : '';
                    $withdraw_check_procedure = !empty($value['withdraw_check_procedure']) ? trim($value['withdraw_check_procedure']) : '';
                    $prevent_procedure = !empty($value['prevent_procedure']) ? trim($value['prevent_procedure']) : '';
                    $time_inventory = !empty($value['time_inventory']) ? number_unformat($value['time_inventory']) : '';

                    $hand_input_code = 1;
                    $customer = !empty($value['customer']) ? trim($value['customer']) : '';
                    $product_code_customer = !empty($value['product_code_customer']) ? trim($value['product_code_customer']) : '';
                    $product_name_customer = !empty($value['product_name_customer']) ? trim($value['product_name_customer']) : '';
                    $standard_colors = !empty($value['standard_colors']) ? trim($value['standard_colors']) : '';
                    $pp_check = !empty($value['pp_check']) ? trim($value['pp_check']) : '';
                    $number_child_sue = !empty($value['number_child_sue']) ? number_unformat($value['number_child_sue']) : '';
                    $packing = !empty($value['packing']) ? trim($value['packing']) : '';
                    $qr = !empty($value['qr']) ? trim($value['qr']) : '';
                    $time_stock = !empty($value['time_stock']) ? number_unformat($value['time_stock']) : '';
                    $species = !empty($value['species']) ? trim($value['species']) : '';
                    $loss = !empty($value['loss']) ? number_unformat($value['loss']) : 0;
                    $quantity_child_sheet = !empty($value['quantity_child_sheet']) ? number_unformat($value['quantity_child_sheet']) : '';
                    $quantity_sheet_bale = !empty($value['quantity_sheet_bale']) ? number_unformat($value['quantity_sheet_bale']) : '';
                    $type_print = !empty($value['type_print']) ? trim($value['type_print']) : '';

                    $height = !empty($value['height']) ? number_unformat($value['height']) : '';
                    $longs = !empty($value['longs']) ? number_unformat($value['longs']) : '';
                    $wide = !empty($value['wide']) ? number_unformat($value['wide']) : '';

                    $sample_cover_code = !empty($value['sample_cover_code']) ? trim($value['sample_cover_code']) : '';
                    $mold_code = !empty($value['mold_code']) ? trim($value['mold_code']) : '';

                    $color_size = !empty($value['color_size']) ? trim($value['color_size']) : '';
                    $gw = !empty($value['gw']) ? trim($value['gw']) : '';
                    $carton_size = !empty($value['carton_size']) ? trim($value['carton_size']) : '';
                    $columns = !empty($value['columns_id']) ? trim($value['columns_id']) : '';
                    $code_bom = !empty($value['code_bom']) ? trim($value['code_bom']) : '';

                    $conversion_unit = !empty($value['conversion_unit']) ? trim($value['conversion_unit']) : '';
                    $conversion_quantity_unit = !empty($value['conversion_quantity_unit']) ? number_unformat($value['conversion_quantity_unit']) : '';
                    $quantity_child_molds = !empty($value['quantity_child_molds']) ? number_unformat($value['quantity_child_molds']) : '';
                    $quantity_child_molds_offset = !empty($value['quantity_child_molds_offset']) ? number_unformat($value['quantity_child_molds_offset']) : '';
                    $quantity_child_molds_flexo = !empty($value['quantity_child_molds_flexo']) ? number_unformat($value['quantity_child_molds_flexo']) : '';

                    $color_formula = !empty($value['color_formula']) ? trim($value['color_formula']) : '';
                    $ball_formula = !empty($value['ball_formula']) ? trim($value['ball_formula']) : '';
                    $id_branch = !empty($value['id_branch']) ? trim($value['id_branch']) : '';
                    $brand = !empty($value['brand']) ? trim($value['brand']) : '';

                    $brand_id = !empty($value['brand_id']) ? trim($value['brand_id']) : '';
                    $classify = !empty($value['classify']) ? trim($value['classify']) : '';
                    $unit_measure = !empty($value['unit_measure']) ? trim($value['unit_measure']) : '';
                    $delivery_norms = !empty($value['delivery_norms']) ? number_unformat($value['delivery_norms']) : 0;

                    if (empty($code) || empty($name) || empty($category) || empty($unit) || empty($type_products) || empty($conversion_unit) || empty($conversion_quantity_unit) || empty($id_branch)) {
                        continue;
                    }

                    if ($type_products != 'products' && $type_products != 'semi_products' && $type_products != 'semi_products_outside') {
                        $errors .= '<div class="text-danger">' . $code . ' ' . lang('không thể thêm được vì không có đúng loại thành phẩm') . '</div>';
                        continue;
                    }
                    $unit_measure_id = '';
                    //unit
                    if ($unit_measure_1) {
                        $row_unit = $this->unit_model->rowUnitByCode($unit_measure, 'unitid', $unit_measure_1);
                        if (!empty($row_unit)) {
                            $unit_measure_id = $row_unit['unitid'];
                        } else if ($unit_measure_2 == 'add') {
                            $unit_measure_id = $this->unit_model->insertUnit([
                                'unit' => $unit_measure
                            ]);
                        } else {
                            continue;
                        }
                    }
                    $brand_id_id = '';
                    //unit
                    if ($brand_id_1) {
                        $row_unit = $this->unit_model->rowBrandByCode($brand_id, 'id', $brand_id_1);
                        if (!empty($row_unit)) {
                            $brand_id_id = $row_unit['id'];
                        } else if ($brand_id_2 == 'add') {
                            $brand_id_id = $this->unit_model->insertBrand([
                                'code' => $brand_id,
                                'name' => $brand_id
                            ]);
                        } else {
                            continue;
                        }
                    }

                    //category
                    if ($category_id_1) {
                        $row_category = $this->products_model->rowCategoryProductsByCode($category, 'id', $category_id_1);
                        if (!empty($row_category)) {
                            $category_id = $row_category['id'];
                        } else if ($category_id_2 == 'add') {
                            $category_id = $this->products_model->insertCategoryProducts([
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

                    if ($conversion_unit_1) {
                        $row_conversion_unit = $this->unit_model->rowUnitByCode($conversion_unit, 'unitid', $conversion_unit_1);
                        if (!empty($row_conversion_unit)) {
                            $conversion_unit_id = $row_conversion_unit['unitid'];
                        } else if ($conversion_unit_2 == 'add') {
                            $conversion_unit_id = $this->unit_model->insertUnit([
                                'unit' => $conversion_unit
                            ]);
                        } else {
                            continue;
                        }
                    }

                    // size
                    $size_id = 0;
                    if ($size_1 && !empty($size)) {
                        $row_size = $this->products_model->rowSizeByCode($size, 'id', $size_1);
                        if (!empty($row_size)) {
                            $size_id = $row_size['id'];
                        } else if ($size_2 == 'add') {
                            $size_id = $this->products_model->insertSize([
                                'name' => $size,
                                'create_by' => get_staff_user_id(),
                                'date_create' => date('Y-m-d H:i:s')
                            ]);
                        } else {
                            continue;
                        }
                    }

                    //colors
                    $arr_colors = [];
                    if (!empty($colors)) {
                        $colors = explode('//', $colors);
                        foreach ($colors as $k => $val) {
                            if (empty($val)) continue;
                            $row_color = $this->products_model->rowColorByCode($val, 'id', $colors_1);
                            if (!empty($row_color)) {
                                $color_id = $row_color['id'];
                            } else if ($colors_2 == 'add') {
                                $color_id = $this->products_model->insertColors([
                                    'code' => $val,
                                    'name' => $val
                                ]);
                            } else {
                                continue;
                            }
                            $arr_colors[$k]['color_id'] = $color_id;
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

                    $type_print_id = 0;
                    if (!empty($type_print)) {
                        $type_print_id = $type_print;
                        // $dtTypePrint = $this->products_model->getTypePrintByCode($type_print);
                        // if (!empty($dtTypePrint)) {
                        //     $type_print_id = $dtTypePrint ['id'];
                        // }
                    }

                    $columns_id = 0;
                    $arrDataColumns = [];
                    if (!empty($columns)) {
                        // $dtColumns = $this->products_model->getColumnsByCode($columns);
                        // if (!empty($dtColumns)) {
                        //     $columns_id = $dtColumns['id'];
                        // } else {
                        //     $errors.= '<div class="text-danger">'.$code.' '.lang('không thể thêm được vì không có columns ['.$columns.']').'</div>';
                        //     continue;
                        // }

                        $arrColumns = explode(',', $columns);
                        $flagFalseColumns = false;
                        if ($arrColumns) {
                            foreach ($arrColumns as $k => $val) {
                                $strColumns = trim($val);
                                $dtColumns = $this->products_model->getColumnsByCode($strColumns);
                                if (!empty($dtColumns)) {
                                    $arrDataColumns[] = [
                                        'columns_id' => $dtColumns['id']
                                    ];
                                } else {
                                    $errors .= '<div class="text-danger">' . $code . ' ' . lang('không thể thêm được vì không có columns [' . $strColumns . ']') . '</div>';
                                    $flagFalseColumns = true;
                                    continue;
                                }
                            }
                        }

                        if ($flagFalseColumns) {
                            continue;
                        }
                    }

                    $customer_id = 0;
                    if (!empty($customer)) {
                        $this->db->select('tblclients.userid');
                        $this->db->from('tblclients');
                        $this->db->group_start();
                        $this->db->like('tblclients.zcode', $customer);
                        $this->db->or_like('tblclients.company', $customer, false);
                        $this->db->group_end();
                        $dtCustomer = $this->db->get()->row_array();
                        if (empty($dtCustomer)) {
                            $errors .= '<div class="text-danger">Khách hàng ' . $customer . ' không có trong phần mềm</div>';
                            continue;
                        }
                        $customer_id = $dtCustomer['userid'];
                    }

                    //custom fields
                    $custom_fields = [];
                    foreach ($this->custom_fields as $k => $val) {
                        if (!empty($value['custom_fields_' . $val['fieldto'] . '_' . $val['id']])) {
                            $custom_fields[$val['fieldto']][$val['id']] = $value['custom_fields_' . $val['fieldto'] . '_' . $val['id']];
                        }
                    }

                    //bom
                    $bom_id = 0;
                    $versions_bom = null;
                    if (!empty($bom_version)) {
                        $bom = $this->products_model->rowBomByVersion($bom_version);
                        if (!empty($bom)) {
                            $bom_id = $bom['id'];
                            $versions_bom = $bom['versions'];
                        }
                    }

                    //exchange
                    $arr_ex = [];
                    $indexExchange = 0;
                    if (!empty($exchange)) {
                        $exchange = explode('//', $exchange);
                        if (!empty($exchange)) {
                            foreach ($exchange as $k => $val) {
                                if (empty($val)) continue;
                                if ($indexExchange > 0) continue;
                                $info_ex = explode('=>', $val);
                                $number_exchange = !empty($info_ex[0]) ? number_unformat($info_ex[0]) : 1;
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
                                $indexExchange++;
                            }
                        }
                    }

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
                    // print_arrays($arr_ex);

                    $options = [
                        'category_id' => $category_id,
                        'type_products' => $type_products,
                        'name' => $name,
                        'code' => $code,
                        'name_customer' => $name_customer,
                        'name_supplier' => $name_supplier,
                        'price_import' => $price_import,
                        'price_sell' => $price_sell,
                        'price_processing' => $price_processing,
                        'number_labor' => $number_labor,
                        'quantity_minimum' => $quantity_minimum,
                        'quantity_max' => $quantity_max,
                        'number_hours_ap' => $number_hours_ap,
                        'unit_id' => $unit_id,
                        'mode' => $mode,
                        'note' => $note,
                        'bom_id' => $bom_id,
                        'versions' => $versions_bom,
                        'date_created' => date('Y-m-d H:i:s'),
                        'created_by' => get_staff_user_id(),
                        'number_day' => $number_day,
                        'size' => $size_id,

                        'mode_product' => $mode_product,
                        'stage_mode' => $stage_mode,
                        'stage_standard' => $stage_standard,
                        'operating_gauge' => $operating_gauge,
                        'quota_productivity_h' => $quota_productivity_h,
                        'quota_power_consumption_h' => $quota_power_consumption_h,
                        'quota_material_replace_t' => $quota_material_replace_t,
                        'quota_depreciation_ts_date' => $quota_depreciation_ts_date,
                        'quota_npl_consumption_one' => $quota_npl_consumption_one,
                        'quota_time_change_one' => $quota_time_change_one,
                        'person_charge' => $person_charge,
                        'property_grant' => $property_grant,
                        'completion_standard' => $completion_standard,
                        'control_criteria' => $control_criteria,
                        'productivity_m_w_n' => $productivity_m_w_n,
                        'quality_problem' => $quality_problem,
                        'incident_record' => $incident_record,
                        'operating_procedure' => $operating_procedure,
                        'withdraw_check_procedure' => $withdraw_check_procedure,
                        'prevent_procedure' => $prevent_procedure,
                        'time_inventory' => $time_inventory,

                        'customer' => $customer_id,
                        'product_code_customer' => $product_code_customer,
                        'product_name_customer' => $product_name_customer,
                        'standard_colors' => $standard_colors,
                        'pp_check' => $pp_check,
                        'number_child_sue' => $number_child_sue,
                        'packing' => $packing,
                        'qr' => $qr,
                        'time_stock' => $time_stock,
                        'species' => $species_id,
                        'hand_input_code' => $hand_input_code,
                        'loss' => $loss,
                        'quantity_child_sheet' => $quantity_child_sheet,
                        'quantity_sheet_bale' => $quantity_sheet_bale,
                        'type_print' => $type_print_id,
                        'height' => $height,
                        'longs' => $longs,
                        'wide' => $wide,
                        'sample_cover_code' => $sample_cover_code,
                        'mold_code' => $mold_code,

                        'color_size' => $color_size,
                        'gw' => $gw,
                        'carton_size' => $carton_size,
                        'columns_id' => $columns_id,
                        'code_bom' => $code_bom,
                        'conversion_unit' => $conversion_unit_id,
                        'conversion_quantity_unit' => $conversion_quantity_unit,
                        'quantity_child_molds' => $quantity_child_molds,
                        'quantity_child_molds_offset' => $quantity_child_molds_offset,
                        'quantity_child_molds_flexo' => $quantity_child_molds_flexo,

                        'color_formula' => $color_formula,
                        'ball_formula' => $ball_formula,
                        'id_branch' => $_id_branch,
                        'unit_measure' => $unit_measure_id,
                        'classify' => $classify,
                        'brand_id' => $brand_id_id,
                        'brand' => $brand,
                        'delivery_norms' => $delivery_norms,
                    ];

                    //check exist
                    if ($this->products_model->checkProductsByCode($code)) {
                        $errors .= '<div class="text-danger">' . $code . ' ' . lang('tnh_exist_data') . '</div>';
                        continue;
                    }
                    $id = $this->products_model->insertProducts($options);
                    if ($id) {
                        $count++;
                        if (!empty($arr_colors)) {
                            $cl = [];
                            foreach ($arr_colors as $key => $value) {
                                $cl[] = [
                                    'product_id' => $id,
                                    'color_id' => $value['color_id'],
                                ];
                            }
                            $this->products_model->insertBatchProductsColors($cl);
                        }
                        if (!empty($arr_ex)) {
                            foreach ($arr_ex as $key => $value) {
                                $arr_ex[$key]['product_id'] = $id;
                            }
                            $this->products_model->insertExchangeProducts($arr_ex);
                        }
                        if (!empty($custom_fields)) {
                            handle_custom_fields_post($id, $custom_fields);
                        }
                        //bom
                        if (!empty($bom) && $type_products != "semi_products_outside") {
                            $fields = [
                                'versions' => $bom['versions'],
                                'product_id' => $id,
                                'status' => 'unapplication',
                                'date_start' => $bom['date_start'],
                                'date_end' => $bom['date_end'],
                                'date_created' => $bom['date_created'],
                                'created_by' => $bom['created_by'],
                            ];
                            $bom_element = $this->products_model->getBomsElementByBomId($bom_id);
                            foreach ($bom_element as $key => $value) {
                                $fields['element'][$key]['element_name'] = $value['element_name'];
                                $fields['element'][$key]['element_number'] = $value['quantity'];

                                $type = false;
                                if ($type_products == 'semi_products') $type = ['materials'];
                                $items = $this->products_model->getBomsElementItemsByBEI($value['id'], $type);
                                if (!empty($items)) {
                                    foreach ($items as $k => $val) {
                                        $fields['element'][$key]['items'][$k]['type'] = $val['type'];
                                        $fields['element'][$key]['items'][$k]['item_id'] = $val['item_id'];
                                        $fields['element'][$key]['items'][$k]['unit_id'] = $val['unit_id'];
                                        $fields['element'][$key]['items'][$k]['element_item_number'] = $val['quantity'];
                                        $fields['element'][$key]['items'][$k]['leadtime'] = 0;
                                        $fields['element'][$key]['items'][$k]['stage'] = 0;

                                        $elementItemsReplace = $this->products_model->getBomsElementItemsReplace($val['id']);
                                        if (!empty($elementItemsReplace)) {
                                            foreach ($elementItemsReplace as $nn => $vv) {
                                                $fields['element'][$key]['items'][$k]['replace'][$nn]['type_replace'] = 'materials';
                                                $fields['element'][$key]['items'][$k]['replace'][$nn]['item_id_replace'] = $vv['item_id_replace'];
                                                $fields['element'][$key]['items'][$k]['replace'][$nn]['unit_id_replace'] = $vv['unit_id_replace'];
                                                $fields['element'][$key]['items'][$k]['replace'][$nn]['element_item_number_replace'] = $vv['quantity_replace'];
                                                $fields['element'][$key]['items'][$k]['replace'][$nn]['leadtime_replace'] = 0;
                                                $fields['element'][$key]['items'][$k]['replace'][$nn]['stage_replace'] = 0;
                                            }
                                        }
                                    }
                                }
                            }
                            if (!empty($fields)) {
                                $ib = $this->products_model->insertBOM($fields, 'unapplication', 0, $actions = "add");
                            }
                        }

                        $this->products_model->handlingDesignStages($id);

                        if (!empty($arrDataColumns)) {
                            $arrColumns = [];
                            foreach ($arrDataColumns as $k => $val) {
                                $arrColumns[] = [
                                    'product_id' => $id,
                                    'columns_id' => $val['columns_id'],
                                ];
                            }
                            $this->products_model->insertBatchProductsColumns($arrColumns);
                        }
                    }
                }
            } elseif ($actions == "updated") {
                foreach ($arraydata as $key => $value) {
                    $code = !empty($value['code']) ? trim($value['code']) : '';
                    $dtProduct = $this->products_model->rowProductByCode($code);
                    ++$rowT;

                    if (empty($dtProduct)) {
                        $errors .= '<div class="text-danger">Mã [' . $code . '] chưa tồn tại trong phần mềm dòng [' . ($rowT) . ']</div>';
                        continue;
                    }
                    $id = $dtProduct['id'];

                    $options = [];
                    if (isset($value['product_name_customer'])) {
                        $product_name_customer = !empty($value['product_name_customer']) ? trim($value['product_name_customer']) : '';
                        $options['product_name_customer'] = $product_name_customer;
                    }

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

                    if (isset($value['price_processing'])) {
                        $price_processing = !empty($value['price_processing']) ? number_unformat($value['price_processing']) : '';
                        $options['price_processing'] = $price_processing;
                    }

                    if (isset($value['quantity_child_molds'])) {
                        $quantity_child_molds = !empty($value['quantity_child_molds']) ? number_unformat($value['quantity_child_molds']) : '';
                        $options['quantity_child_molds'] = $quantity_child_molds;
                    }

                    if (isset($value['color_formula'])) {
                        $color_formula = !empty($value['color_formula']) ? trim($value['color_formula']) : '';
                        $options['color_formula'] = $color_formula;
                    }

                    if (isset($value['ball_formula'])) {
                        $ball_formula = !empty($value['ball_formula']) ? trim($value['ball_formula']) : '';
                        $options['ball_formula'] = $ball_formula;
                    }

                    if (isset($value['brand'])) {
                        $brand = !empty($value['brand']) ? trim($value['brand']) : '';
                        $options['brand'] = $brand;
                    }

                    //unit
                    if (isset($value['unit_id'])) {
                        $unit = !empty($value['unit_id']) ? trim($value['unit_id']) : '';
                        $row_unit = $this->unit_model->rowUnitByCode($unit, 'unitid', $unit_id_1);
                        if (!empty($row_unit)) {
                            $unit_id = $row_unit['unitid'];
                        } elseif ($unit_id_2 == 'add') {
                            $unit_id = $this->unit_model->insertUnit([
                                'unit' => $unit,
                            ]);
                        } else {
                            $errors .= '<div class="text-danger">Mã TP [' . $code . '] chưa cập nhật được vì đơn vị [' . $unit . '] chưa tồn tại trong phần mềm.</div>';
                            continue;
                        }
                        $options['unit_id'] = $unit_id;
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

                    if (isset($value['conversion_unit'])) {
                        $conversion_unit = !empty($value['conversion_unit']) ? trim($value['conversion_unit']) : '';
                        $row_conversion_unit = $this->unit_model->rowUnitByCode($conversion_unit, 'unitid', $conversion_unit_1);
                        if (!empty($row_conversion_unit)) {
                            $conversion_unit_id = $row_conversion_unit['unitid'];
                        } else if ($conversion_unit_2 == 'add') {
                            $conversion_unit_id = $this->unit_model->insertUnit([
                                'unit' => $conversion_unit
                            ]);
                        } else {
                            $errors .= '<div class="text-danger">Mã TP [' . $code . '] chưa cập nhật được vì đơn vị quy đổi [' . $conversion_unit . '] chưa tồn tại trong phần mềm.</div>';
                            continue;
                        }
                        $options['conversion_unit'] = $conversion_unit_id;
                    }

                    if (isset($value['conversion_quantity_unit'])) {
                        $conversion_quantity_unit = !empty($value['conversion_quantity_unit']) ? number_unformat($value['conversion_quantity_unit']) : 0;
                        if (empty($conversion_quantity_unit)) {
                            $errors .= '<div class="text-danger">Mã TP [' . $code . '] chưa cập nhật được vì số lượng quy đổi [' . $conversion_quantity_unit . '] chưa được nhập.</div>';
                            continue;
                        }
                        $options['conversion_quantity_unit'] = $conversion_quantity_unit;
                    }

                    if (isset($value['mode'])) {
                        $mode = !empty($value['mode']) ? trim($value['mode']) : '';
                        $options['mode'] = $mode;
                    }

                    if (isset($value['note'])) {
                        $note = !empty($value['note']) ? trim($value['note']) : '';
                        $options['note'] = $note;
                    }

                    if (isset($value['number_labor'])) {
                        $number_labor = !empty($value['number_labor']) ? number_unformat($value['number_labor']) : '';
                        $options['number_labor'] = $number_labor;
                    }

                    if (isset($value['quantity_minimum'])) {
                        $quantity_minimum = !empty($value['quantity_minimum']) ? number_unformat($value['quantity_minimum']) : '';
                        $options['quantity_minimum'] = $quantity_minimum;
                    }

                    if (isset($value['quantity_max'])) {
                        $quantity_max = !empty($value['quantity_max']) ? number_unformat($value['quantity_max']) : '';
                        $options['quantity_max'] = $quantity_max;
                    }

                    if (isset($value['name_customer'])) {
                        $name_customer = !empty($value['name_customer']) ? trim($value['name_customer']) : '';
                        $options['name_customer'] = $name_customer;
                    }

                    if (isset($value['name_supplier'])) {
                        $name_supplier = !empty($value['name_supplier']) ? trim($value['name_supplier']) : '';
                        $options['name_supplier'] = $name_supplier;
                    }

                    if (isset($value['number_hours_ap'])) {
                        $number_hours_ap = !empty($value['number_hours_ap']) ? number_unformat($value['number_hours_ap']) : '';
                        $options['number_hours_ap'] = $number_hours_ap;
                    }

                    if (isset($value['number_day'])) {
                        $number_day = !empty($value['number_day']) ? number_unformat($value['number_day']) : '';
                        $options['number_day'] = $number_day;
                    }

                    if (isset($value['mode_product'])) {
                        $mode_product = !empty($value['mode_product']) ? trim($value['mode_product']) : '';
                        $options['mode_product'] = $mode_product;
                    }

                    if (isset($value['stage_mode'])) {
                        $stage_mode = !empty($value['stage_mode']) ? trim($value['stage_mode']) : '';
                        $options['stage_mode'] = $stage_mode;
                    }

                    if (isset($value['stage_standard'])) {
                        $stage_standard = !empty($value['stage_standard']) ? trim($value['stage_standard']) : '';
                        $options['stage_standard'] = $stage_standard;
                    }

                    if (isset($value['operating_gauge'])) {
                        $operating_gauge = !empty($value['operating_gauge']) ? trim($value['operating_gauge']) : '';
                        $options['operating_gauge'] = $operating_gauge;
                    }

                    if (isset($value['quota_productivity_h'])) {
                        $quota_productivity_h = !empty($value['quota_productivity_h']) ? number_unformat($value['quota_productivity_h']) : '';
                        $options['quota_productivity_h'] = $quota_productivity_h;
                    }

                    if (isset($value['quota_power_consumption_h'])) {
                        $quota_power_consumption_h = !empty($value['quota_power_consumption_h']) ? number_unformat($value['quota_power_consumption_h']) : '';
                        $options['quota_power_consumption_h'] = $quota_power_consumption_h;
                    }

                    if (isset($value['quota_material_replace_t'])) {
                        $quota_material_replace_t = !empty($value['quota_material_replace_t']) ? number_unformat($value['quota_material_replace_t']) : '';
                        $options['quota_material_replace_t'] = $quota_material_replace_t;
                    }

                    if (isset($value['quota_depreciation_ts_date'])) {
                        $quota_depreciation_ts_date = !empty($value['quota_depreciation_ts_date']) ? number_unformat($value['quota_depreciation_ts_date']) : '';
                        $options['quota_depreciation_ts_date'] = $quota_depreciation_ts_date;
                    }

                    if (isset($value['quota_npl_consumption_one'])) {
                        $quota_npl_consumption_one = !empty($value['quota_npl_consumption_one']) ? number_unformat($value['quota_npl_consumption_one']) : '';
                        $options['quota_npl_consumption_one'] = $quota_npl_consumption_one;
                    }

                    if (isset($value['quota_time_change_one'])) {
                        $quota_time_change_one = !empty($value['quota_time_change_one']) ? number_unformat($value['quota_time_change_one']) : '';
                        $options['quota_time_change_one'] = $quota_time_change_one;
                    }

                    if (isset($value['person_charge'])) {
                        $person_charge = !empty($value['person_charge']) ? trim($value['person_charge']) : '';
                        $options['person_charge'] = $person_charge;
                    }

                    if (isset($value['property_grant'])) {
                        $property_grant = !empty($value['property_grant']) ? trim($value['property_grant']) : '';
                        $options['property_grant'] = $property_grant;
                    }

                    if (isset($value['completion_standard'])) {
                        $completion_standard = !empty($value['completion_standard']) ? trim($value['completion_standard']) : '';
                        $options['completion_standard'] = $completion_standard;
                    }

                    if (isset($value['control_criteria'])) {
                        $control_criteria = !empty($value['control_criteria']) ? trim($value['control_criteria']) : '';
                        $options['control_criteria'] = $control_criteria;
                    }

                    if (isset($value['productivity_m_w_n'])) {
                        $productivity_m_w_n = !empty($value['productivity_m_w_n']) ? trim($value['productivity_m_w_n']) : '';
                        $options['productivity_m_w_n'] = $productivity_m_w_n;
                    }

                    if (isset($value['incident_record'])) {
                        $incident_record = !empty($value['incident_record']) ? trim($value['incident_record']) : '';
                        $options['incident_record'] = $incident_record;
                    }

                    if (isset($value['operating_procedure'])) {
                        $operating_procedure = !empty($value['operating_procedure']) ? trim($value['operating_procedure']) : '';
                        $options['operating_procedure'] = $operating_procedure;
                    }

                    if (isset($value['withdraw_check_procedure'])) {
                        $withdraw_check_procedure = !empty($value['withdraw_check_procedure']) ? trim($value['withdraw_check_procedure']) : '';
                        $options['withdraw_check_procedure'] = $withdraw_check_procedure;
                    }

                    if (isset($value['prevent_procedure'])) {
                        $prevent_procedure = !empty($value['prevent_procedure']) ? trim($value['prevent_procedure']) : '';
                        $options['prevent_procedure'] = $prevent_procedure;
                    }

                    if (isset($value['time_inventory'])) {
                        $time_inventory = !empty($value['time_inventory']) ? trim($value['time_inventory']) : '';
                        $options['time_inventory'] = $time_inventory;
                    }

                    if (isset($value['product_code_customer'])) {
                        $product_code_customer = !empty($value['product_code_customer']) ? trim($value['product_code_customer']) : '';
                        $options['product_code_customer'] = $product_code_customer;
                    }

                    if (isset($value['product_name_customer'])) {
                        $product_name_customer = !empty($value['product_name_customer']) ? trim($value['product_name_customer']) : '';
                        $options['product_name_customer'] = $product_name_customer;
                    }

                    if (isset($value['standard_colors'])) {
                        $standard_colors = !empty($value['standard_colors']) ? trim($value['standard_colors']) : '';
                        $options['standard_colors'] = $standard_colors;
                    }

                    if (isset($value['pp_check'])) {
                        $pp_check = !empty($value['pp_check']) ? trim($value['pp_check']) : '';
                        $options['pp_check'] = $pp_check;
                    }

                    if (isset($value['number_child_sue'])) {
                        $number_child_sue = !empty($value['number_child_sue']) ? number_unformat($value['number_child_sue']) : '';
                        $options['number_child_sue'] = $number_child_sue;
                    }

                    if (isset($value['packing'])) {
                        $packing = !empty($value['packing']) ? trim($value['packing']) : '';
                        $options['packing'] = $packing;
                    }

                    if (isset($value['qr'])) {
                        $qr = !empty($value['qr']) ? trim($value['qr']) : '';
                        $options['qr'] = $qr;
                    }

                    if (isset($value['time_stock'])) {
                        $time_stock = !empty($value['qr']) ? number_unformat($value['time_stock']) : '';
                        $options['time_stock'] = $time_stock;
                    }

                    if (isset($value['loss'])) {
                        $loss = !empty($value['loss']) ? number_unformat($value['loss']) : 0;
                        $options['loss'] = $loss;
                    }

                    if (isset($value['quantity_child_sheet'])) {
                        $quantity_child_sheet = !empty($value['quantity_child_sheet']) ? number_unformat($value['quantity_child_sheet']) : '';
                        $options['quantity_child_sheet'] = $quantity_child_sheet;
                    }

                    if (isset($value['quantity_sheet_bale'])) {
                        $quantity_sheet_bale = !empty($value['quantity_sheet_bale']) ? number_unformat($value['quantity_sheet_bale']) : '';
                        $options['quantity_sheet_bale'] = $quantity_sheet_bale;
                    }

                    if (isset($value['type_print'])) {
                        $type_print = !empty($value['type_print']) ? trim($value['type_print']) : '';
                        $options['type_print'] = $type_print;
                    }

                    if (isset($value['height'])) {
                        $height = !empty($value['height']) ? number_unformat($value['height']) : '';
                        $options['height'] = $height;
                    }

                    if (isset($value['longs'])) {
                        $longs = !empty($value['longs']) ? number_unformat($value['longs']) : '';
                        $options['longs'] = $longs;
                    }

                    if (isset($value['wide'])) {
                        $wide = !empty($value['wide']) ? number_unformat($value['wide']) : '';
                        $options['wide'] = $wide;
                    }

                    if (isset($value['sample_cover_code'])) {
                        $sample_cover_code = !empty($value['sample_cover_code']) ? trim($value['sample_cover_code']) : '';
                        $options['sample_cover_code'] = $sample_cover_code;
                    }

                    if (isset($value['mold_code'])) {
                        $mold_code = !empty($value['mold_code']) ? trim($value['mold_code']) : '';
                        $options['mold_code'] = $mold_code;
                    }

                    if (isset($value['color_size'])) {
                        $color_size = !empty($value['color_size']) ? trim($value['color_size']) : '';
                        $options['color_size'] = $color_size;
                    }

                    if (isset($value['gw'])) {
                        $gw = !empty($value['gw']) ? trim($value['gw']) : '';
                        $options['gw'] = $gw;
                    }

                    if (isset($value['carton_size'])) {
                        $gw = !empty($value['carton_size']) ? trim($value['carton_size']) : '';
                        $options['carton_size'] = $carton_size;
                    }

                    if (isset($value['code_bom'])) {
                        $code_bom = !empty($value['code_bom']) ? trim($value['code_bom']) : '';
                        $options['code_bom'] = $code_bom;
                    }

                    if (isset($value['quantity_child_molds_offset'])) {
                        $quantity_child_molds_offset = !empty($value['quantity_child_molds_offset']) ? number_unformat($value['quantity_child_molds_offset']) : 0;
                        $options['quantity_child_molds_offset'] = $quantity_child_molds_offset;
                    }

                    if (isset($value['quantity_child_molds_flexo'])) {
                        $quantity_child_molds_flexo = !empty($value['quantity_child_molds_flexo']) ? number_unformat($value['quantity_child_molds_flexo']) : 0;
                        $options['quantity_child_molds_flexo'] = $quantity_child_molds_flexo;
                    }

                    if (isset($value['allowable'])) {
                        $allowable = !empty($value['allowable']) ? number_unformat($value['allowable']) : 0;
                        $options['allowable'] = $allowable;
                    }

                    $columns_id = 0;
                    $arrDataColumns = [];

                    if (isset($value['columns_id'])) {
                        $columns = !empty($value['columns_id']) ? trim($value['columns_id']) : '';
                        $arrColumns = explode(',', $columns);
                        $flagFalseColumns = false;
                        if ($arrColumns) {
                            foreach ($arrColumns as $k => $val) {
                                $strColumns = trim($val);
                                $dtColumns = $this->products_model->getColumnsByCode($strColumns);
                                if (!empty($dtColumns)) {
                                    $arrDataColumns[] = [
                                        'columns_id' => $dtColumns['id']
                                    ];
                                } else {
                                    $errors .= '<div class="text-danger">' . $code . ' ' . lang('không thể cập nhật được vì không có columns [' . $strColumns . ']') . ' dòng [' . ($rowT) . ']</div>';
                                    $flagFalseColumns = true;
                                    continue;
                                }
                            }
                        }

                        if ($flagFalseColumns) {
                            continue;
                        }
                    }


                    if (isset($value['size'])) {
                        $size_id = 0;
                        $size = !empty($value['size']) ? trim($value['size']) : '';
                        if ($size_1 && !empty($size)) {
                            $row_size = $this->products_model->rowSizeByCode($size, 'id', $size_1);
                            if (!empty($row_size)) {
                                $size_id = $row_size['id'];
                            } elseif ($size_2 == 'add') {
                                $size_id = $this->products_model->insertSize([
                                    'name' => $size,
                                    'create_by' => get_staff_user_id(),
                                    'date_create' => date('Y-m-d H:i:s'),
                                ]);
                            } else {
                                $errors .= '<div class="text-danger">Mã TP [' . $code . '] chưa cập nhật được vì kích thước [' . $size . '] chưa tồn tại trong phần mềm.</div>';
                                continue;
                            }
                            $options['size'] = $size_id;
                        }
                    }

                    if (isset($value['customer'])) {
                        $customer = !empty($value['customer']) ? trim($value['customer']) : '';
                        $this->db->select('tblclients.userid');
                        $this->db->from('tblclients');
                        $this->db->group_start();
                        $this->db->like('tblclients.zcode', $customer);
                        $this->db->or_like('tblclients.company', $customer, false);
                        $this->db->group_end();
                        $dtCustomer = $this->db->get()->row_array();
                        if (empty($dtCustomer)) {
                            $errors .= '<div class="text-danger">Mã TP [' . $code . '] chưa cập nhật được vì khách hàng [' . $customer . '] chưa tồn tại trong phần mềm.</div>';
                            continue;
                        }
                        $options['customer'] = $dtCustomer['userid'];
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
                            $errors .= '<div class="text-danger">Mã TP [' . $code . '] chưa cập nhật được vì chủng loại [' . $species . '] chưa tồn tại trong phần mềm.</div>';
                            continue;
                        }

                        $options['species'] = $species_id;
                    }

                    foreach ($this->list_standard as $kfield => $vfield) {
                        if (isset($value[$vfield['id_key']])) {
                            $data_field = !empty($value[$vfield['id_key']]) ? trim($value[$vfield['id_key']]) : '';
                            $row_type_field = $this->db->get_where('tbllist_other', [
                                'standard' => $code,
                                'type' => $kfield
                            ])->row_array();
                            if (!empty($row_type_field)) {
                                $row_id = $row_type_field['id'];
                            } else if ($species_2 == 'add') {
                                $row_list_other = $this->db->insert('tbllist_other', [
                                    'standard' => $code,
                                    'create_by' => get_staff_user_id(),
                                    'type' => $kfield
                                ]);
                                $row_id = $this->db->insert_id();
                            } else {
                                $errors .= '<div class="text-danger">Tiêu chuẩn ' . $vfield['name'] . ' [' . $code . '] vì đã tồn tại trong phần mềm.</div>';
                                continue;
                            }

                            $options[$vfield['id_key']] = $row_id;

                            if ($vfield['id_key'] == 'id_standard_sample_code') {
                                if (!empty($code)) {
                                    $options['sample_cover_code'] = $code;
                                }
                            }
                        }
                    }
                    if (!empty($options)) {
                        $up = $this->products_model->updateProducts($id, $options);
                        if ($up) {

                            if (!empty($arrDataColumns)) {
                                $this->products_model->deleteProductsColumns($id);
                                $arrColumns = [];
                                foreach ($arrDataColumns as $k => $val) {
                                    $arrColumns[] = [
                                        'product_id' => $id,
                                        'columns_id' => $val['columns_id'],
                                    ];
                                }
                                $this->products_model->insertBatchProductsColumns($arrColumns);
                            }

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
                if ($actions == "updated") {
                    $data['message'] = lang('Không có dữ liệu được cập nhật');
                } else {
                    $data['message'] = lang('tnh_not_data_add');
                }
            }
            $data['errors'] = $errors;
            echo json_encode($data);
            die;
        } else {
            $data['tnh'] = true;
            $data['title'] = _l('tnh_import_excel');
            $list = [];
            $fields = get_fields_export($table = 'tbl_products', $arr_diff = ['id', 'images', 'quantity_begin', 'versions', 'versions_stage', 'info', 'images_multiple', 'date_created', 'created_by', 'updated_by', 'date_updated', 'calculated_on_sales', 'warranty', 'hand_input_code', 'status', 'number_child_sue', 'name_customer', 'sample_cover_code', 'max_price', 'total_business_plan', 'total_transfer_business'], $arr_more = ['colors']);
            foreach ($fields as $key => $value) {
                if ($value == "name_customer") {
                    $list[$value] = mb_strtoupper(lang('tnh_product_name_customer'), 'UTF-8');
                } else if ($value == "name_supplier") {
                    $list[$value] = mb_strtoupper(lang('tnh_product_name_supplier'), 'UTF-8');
                } else {
                    $list[$value] = mb_strtoupper(lang('tnh_' . $value), 'UTF-8');
                }
            }
            foreach ($this->list_standard as $kfield => $vfield) {
                $list[$vfield['id_key']] = mb_strtoupper(lang($vfield['name']), 'UTF-8');
            }

            //custom fields
            foreach ($this->custom_fields as $key => $value) {
                $list['custom_fields_' . $value['fieldto'] . '_' . $value['id']] = $value['name'];
            }
            $required = [lang('tnh_category_id'), lang('tnh_type_products'), lang('tnh_name'), lang('tnh_code'), lang('tnh_unit_id'), lang('tnh_conversion_unit'), lang('tnh_conversion_quantity_unit'), lang('tnh_id_branch')];
            $data['list'] = $list;



            $data['list_auto_add'] = [
                'category_id' => mb_strtoupper(lang('tnh_category_id'), 'UTF-8'),
                'type_products' => mb_strtoupper(lang('tnh_type_products'), 'UTF-8'),
                'species' => mb_strtoupper(lang('tnh_species'), 'UTF-8'),
                'mode' => mb_strtoupper(lang('tnh_mode'), 'UTF-8'),
                'mode_product' => mb_strtoupper(lang('tnh_mode_product'), 'UTF-8'),
                'code' => mb_strtoupper(lang('tnh_code'), 'UTF-8'),
                'name' => mb_strtoupper(lang('tnh_name'), 'UTF-8'),
                'unit_id' => mb_strtoupper(lang('tnh_unit_id'), 'UTF-8'),
                'bom_id' => mb_strtoupper(lang('tnh_bom_id'), 'UTF-8'),
                'customer' => mb_strtoupper(lang('tnh_customer'), 'UTF-8'),
                'product_code_customer' => mb_strtoupper(lang('tnh_product_code_customer'), 'UTF-8'),
                'product_name_customer' => mb_strtoupper(lang('tnh_product_name_customer'), 'UTF-8'),
                'packing' => mb_strtoupper(lang('tnh_packing'), 'UTF-8'),
                'qr' => mb_strtoupper(lang('tnh_qr'), 'UTF-8'),
                'time_stock' => mb_strtoupper(lang('tnh_time_stock'), 'UTF-8'),
                'loss' => mb_strtoupper(lang('tnh_loss'), 'UTF-8'),
                'quantity_child_sheet' => mb_strtoupper(lang('tnh_quantity_child_sheet'), 'UTF-8'),
                'quantity_sheet_bale' => mb_strtoupper(lang('tnh_quantity_sheet_bale'), 'UTF-8'),
                'type_print' => mb_strtoupper(lang('tnh_type_print'), 'UTF-8'),
                'columns_id' => mb_strtoupper(lang('tnh_columns_id'), 'UTF-8'),
                'longs' => mb_strtoupper(lang('tnh_longs'), 'UTF-8'),
                //                'sample_cover_code' => mb_strtoupper(lang('tnh_sample_cover_code'), 'UTF-8'),
                'mold_code'  => mb_strtoupper(lang('tnh_mold_code'), 'UTF-8'),
                'quantity_child_molds'  => mb_strtoupper(lang('tnh_quantity_child_molds'), 'UTF-8'),
                'conversion_unit'  => mb_strtoupper(lang('tnh_conversion_unit'), 'UTF-8'),
                'conversion_quantity_unit'  => mb_strtoupper(lang('tnh_conversion_quantity_unit'), 'UTF-8'),
                'id_branch'  => mb_strtoupper(lang('tnh_id_branch'), 'UTF-8'),
                'brand_id'  => mb_strtoupper(lang('tnh_brand_id'), 'UTF-8'),
                'classify'  => mb_strtoupper(lang('tnh_classify'), 'UTF-8'),
                'unit_measure'  => mb_strtoupper(lang('tnh_unit_measure'), 'UTF-8'),
            ];

            foreach ($this->list_standard as $kfield => $vfield) {
                $data['list_auto_add'][$vfield['id_key']] = mb_strtoupper(lang($vfield['name']), 'UTF-8');
            }

            $data['required'] = $required;
            $this->load->view('admin/products/import_products', $data);
        }
    }

    function searchProducts()
    {
        $data = [];
        // if ($this->input->get())
        // {
        $q = $this->input->get('q');
        $limit = get_option('select2_limit');
        $data = $this->products_model->searchProducts($q, $limit);
        // }
        echo json_encode($data);
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

    function searchProductAndGoods($id = false)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');
        $products = $this->products_model->searchProductsSelect2($term, $limit);
        $items = $this->products_model->searchItemsSelect2($term, $limit);
        $data['results'] = [
            [
                'text' => lang('products'),
                'children' => $products
            ],
            // [
            //     'text' => lang('ch_items'), 'children' => $items
            // ]
        ];
        if ($id) {
            $dt = explode('__', $id);
            $id = $dt[0];
            $type_item = $dt[1];
            if ($type_item == "products") {
                $product = $this->products_model->rowProduct($id);
                $data['row'] = ['id' => $product['id'] . '__' . 'products', 'text' => $product['name'] . '(' . $product['code'] . ')'];
            } else if ($type_item == "items") {
                $item = $this->items_model->rowItems($id);
                $data['row'] = ['id' => $item['id'] . '__' . 'items', 'text' => $item['name'] . '(' . $item['code'] . ')'];
            }
        }
        echo json_encode($data);
    }

    function searchProductAndGoodsMaterials($id = false)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');
        $products = $this->products_model->searchProductsSelect2($term, $limit);
        // $items = $this->products_model->searchItemsSelect2($term, $limit);
        $materials = $this->products_model->searchMaterialsSelect2($term, $limit);
        $data['results'] = [
            [
                'text' => lang('products'),
                'children' => $products
            ],
            // [
            //     'text' => lang('ch_items'), 'children' => $items
            // ],
            // [
            //     'text' => lang('materials'), 'children' => $materials
            // ]
        ];
        if ($id) {
            $dt = explode('__', $id);
            $id = $dt[0];
            $type_item = $dt[1];
            if ($type_item == "products") {
                $product = $this->products_model->rowProduct($id);
                $data['row'] = ['id' => $product['id'] . '__' . 'products', 'text' => $product['code'], 'item_code' => $product['code']];
            } else if ($type_item == "items") {
                $item = $this->items_model->rowItems($id);
                $data['row'] = ['id' => $item['id'] . '__' . 'items', 'text' => $item['code'] . '(' . $item['name'] . ')', 'item_code' => $item['code']];
            } else if ($type_item == "materials") {
                $item = $this->items_model->rowMaterial($id);
                $data['row'] = ['id' => $item['id'] . '__' . 'items', 'text' => $item['code'] . '(' . $item['name'] . ')', 'item_code' => $item['code']];
            }
        }
        echo json_encode($data);
    }

    public function rowItem()
    {
        $data = [];
        if ($this->input->get()) {
            $item_id = $this->input->get('item_id');
            $type = $this->input->get('type');
            $arr_unit_id = [];
            $selected = '';
            if ($type == 'semi_products' || $type == 'semi_products_outside') {
                $semi_products = $this->products_model->rowProduct($item_id);
                $selected = $semi_products['unit_id'];
                array_push($arr_unit_id, $semi_products['unit_id']);
            } else {
                $material = $this->items_model->rowMaterial($item_id);
                $exchange = $this->items_model->getExchangeItemsByItemId($item_id);
                // $selected = $material['unit_id'];
                // array_push($arr_unit_id, $material['unit_id']);
                if (!empty($exchange)) {
                    foreach ($exchange as $key => $value) {
                        if ($key != 0) continue;
                        array_push($arr_unit_id, $value['unit_id']);
                        $selected = $value['unit_id'];
                    }
                }
            }
            $units = false;
            if (!empty($arr_unit_id)) {
                $units = $this->products_model->getUnitsByArrId($arr_unit_id);
            }
            $data['units'] = $units;
            $data['selected'] = $selected;
        }
        echo json_encode($data);
    }

    public function list_bom()
    {
        if (!$this->perViewProductsListBom) {
            accessDenied();
        }
        $data['tnh'] = true;
        $data['title'] = lang('tnh_list_bom');
        $this->load->view('admin/products/list_bom', $data);
    }

    public function add_bom($id = 0, $actions = 'add')
    {
        if ($actions == "add" || $actions == "copy") {
            if (!$this->perAddProductsListBom) {
                accessDenied($js = true);
            }
        } else if ($actions == "edit") {
            if (!$this->perEditProductsListBom) {
                accessDenied($js = true);
            }
        }

        if (!empty($id)) {
            $bom = $this->products_model->rowBomById($id);
        }
        if ($this->input->post()) {
            if ($actions == 'add' || $actions == 'copy') {
                $this->form_validation->set_rules('versions', lang("tnh_versions"), 'required|is_unique[tbl_boms.versions]');
            } else if ($actions == 'edit') {
                if (!empty($bom) && $bom['versions'] != trim($this->input->post('versions'))) {
                    $this->form_validation->set_rules('versions', lang("tnh_versions"), 'required|is_unique[tbl_boms.versions]');
                } else {
                    $this->form_validation->set_rules('versions', lang("tnh_versions"), 'required');
                }
            }
            if ($this->form_validation->run() == true) {
                $status = "unapplication";
                $versions = trim($this->input->post('versions'));
                $date_start = $this->input->post('date_start') ? to_sql_date($this->input->post('date_start')) : null;
                $date_end = $this->input->post('date_end') ? to_sql_date($this->input->post('date_end')) : null;
                $i = $this->input->post('i');

                $options['versions'] = $versions;
                $options['date_start'] = $date_start;
                $options['date_end'] = $date_end;
                foreach ($i as $key => $value) {
                    $element_name = trim($this->input->post('element_name_' . $value));
                    if (empty($element_name)) continue;
                    $element_number = $this->input->post('element_number_' . $value);
                    $element_number = 1;
                    $type_element = $this->input->post('type_element_' . $value);

                    $options['element'][$key]['element_name'] = $element_name;
                    $options['element'][$key]['element_number'] = $element_number;
                    $options['element'][$key]['type_element'] = $type_element;
                    $type_design_bom = $this->input->post('type_design_bom_' . $value);
                    if (!empty($type_design_bom)) {
                        foreach ($type_design_bom as $k => $val) {
                            // if ($products['type_products'] != 'products' && $val != 'materials') continue;

                            $item_id = $this->input->post('items_' . $value)[$k];
                            $element_item_number = number_unformat($this->input->post('element_item_number_' . $value)[$k]);
                            $unit_id = $this->input->post('units_' . $value)[$k];
                            $stage = !empty($this->input->post('stage_' . $value)[$k]) ? $this->input->post('stage_' . $value)[$k] : 0;
                            $quantity_compensation = number_unformat($this->input->post('quantity_compensation_' . $value)[$k]);
                            $landscape_print_size = !empty($this->input->post('landscape_print_size_' . $value)[$k]) ? $this->input->post('landscape_print_size_' . $value)[$k] : 0;
                            $vertical_print_size = !empty($this->input->post('vertical_print_size_' . $value)[$k]) ? number_unformat($this->input->post('vertical_print_size_' . $value)[$k]) : 0;
                            $number_children_size = !empty($this->input->post('number_children_size_' . $value)[$k]) ? number_unformat($this->input->post('number_children_size_' . $value)[$k]) : 0;
                            $paper_exchange = !empty($this->input->post('paper_exchange_' . $value)[$k]) ? number_unformat($this->input->post('paper_exchange_' . $value)[$k]) : 0;
                            $hand_input_paper_exchange = !empty($this->input->post('hand_input_paper_exchange_' . $value)[$k]) ? $this->input->post('hand_input_paper_exchange_' . $value)[$k] : 0;
                            if (empty($hand_input_paper_exchange)) {
                                $paper_exchange = 0;
                                if ($number_children_size) {
                                    $paper_exchange = roundNumberFormat(1 / $number_children_size);
                                }
                            }

                            $face = !empty($this->input->post('face_' . $value)[$k]) ? $this->input->post('face_' . $value)[$k] : 0;
                            $face_after = !empty($this->input->post('face_after_' . $value)[$k]) ? $this->input->post('face_after_' . $value)[$k] : 0;

                            if (empty($unit_id)) {
                                $data['result'] = 0;
                                $data['message'] = lang('Vui lòng chọn đơn vị tính');
                                echo json_encode($data);
                                die;
                            }
                            if (empty($unit_id)) continue;
                            $options['element'][$key]['items'][$k]['type'] = $val;
                            $options['element'][$key]['items'][$k]['item_id'] = $item_id;
                            $options['element'][$key]['items'][$k]['unit_id'] = $unit_id;
                            $options['element'][$key]['items'][$k]['element_item_number'] = $element_item_number;
                            $options['element'][$key]['items'][$k]['stage'] = $stage;
                            $options['element'][$key]['items'][$k]['quantity_compensation'] = $quantity_compensation;
                            $options['element'][$key]['items'][$k]['type_element_item'] = $type_element;
                            $options['element'][$key]['items'][$k]['landscape_print_size'] = $landscape_print_size;
                            $options['element'][$key]['items'][$k]['vertical_print_size'] = $vertical_print_size;
                            $options['element'][$key]['items'][$k]['number_children_size'] = $number_children_size;
                            $options['element'][$key]['items'][$k]['paper_exchange'] = $paper_exchange;
                            $options['element'][$key]['items'][$k]['hand_input_paper_exchange'] = $hand_input_paper_exchange;
                            $options['element'][$key]['items'][$k]['face'] = $face;
                            $options['element'][$key]['items'][$k]['face_after'] = $face_after;

                            // if ($val == 'materials') {
                            $items_replace = !empty($this->input->post('items_replace' . $value)[$k]) ? $this->input->post('items_replace' . $value)[$k] : false;
                            if (!empty($items_replace)) {
                                foreach ($items_replace as $nn => $vv) {
                                    $element_item_number_replace = $this->input->post('element_item_number_replace' . $value)[$k][$nn];
                                    $unit_id_replace = $this->input->post('units_replace' . $value)[$k][$nn];
                                    if (empty($unit_id_replace)) continue;

                                    $options['element'][$key]['items'][$k]['replace'][$nn]['type_replace'] = $val;
                                    $options['element'][$key]['items'][$k]['replace'][$nn]['item_id_replace'] = $vv;
                                    $options['element'][$key]['items'][$k]['replace'][$nn]['unit_id_replace'] = $unit_id_replace;
                                    $options['element'][$key]['items'][$k]['replace'][$nn]['element_item_number_replace'] = $element_item_number_replace;
                                }
                            }
                            // }
                        }
                    }
                }
                // print_arrays($options);
                $q = $this->products_model->insertCategoryBOM($options, $status, $id, $actions);
                if ($q) {
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
            $list_stages = recursive_stages();
            $list_stages_primary = recursive_stages($list_stages_primary, 0, null, 1);
            if (!empty($bom)) {
                $data['bom'] = $bom;
                $html_BOM = '';
                $count_i = 0;
                $count_k = 0;
                $kR = 0;

                $elements = $this->products_model->getBomsElementByBomId($bom['id']);
                foreach ($elements as $key => $value) {
                    $html_BOM .= '<tr>';
                    $html_BOM .= '<input type="hidden" name="i[]" id="i" class="form-control i" value="' . $count_i . '">';
                    $html_BOM .= '<td>
                                    <div class="text-center">
                                        <button type="button" class="btn btn-primary btn-icon btn-add-items">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </td>';

                    $html_BOM .= '<td colspan="2">
                                    <input type="text" name="element_name_' . $count_i . '" id="element_name_' . $count_i . '" class="form-control" value="' . $value['element_name'] . '" placeholder="' . lang('tnh_element_name') . '" required="required">
                                    <input type="hidden" name="type_element_' . $count_i . '" class="form-control type_element" value="' . $value['type_element'] . '">
                                    <div class="txt-type-element text-danger mtop5">' . ($value['type_element'] == 1 ? 'NPL chính' : 'NPL phụ') . '</div>
                                </td>';
                    $html_BOM .= '<td></td>';
                    $html_BOM .= '<td class="hide">
                                    <input type="number" name="element_number_' . $count_i . '" class="form-control" value="' . $value['quantity'] . '" min="1">
                                </td>';

                    $html_BOM .= '<td></td>';
                    $html_BOM .= '<td></td>';
                    $html_BOM .= '<td></td>';
                    $html_BOM .= '<td></td>';
                    $html_BOM .= '<td></td>';
                    $html_BOM .= '<td></td>';
                    $html_BOM .= '<td></td>';
                    // $html_BOM .= '<td>
                    //                 <div class="text-center"><i class="btn btn-danger fa fa-remove remove-element"></i></div>
                    //             </td>';
                    $html_BOM .= '</tr>';

                    $items = $this->products_model->getBomsElementItemsByBEI($value['id']);
                    foreach ($items as $k => $val) {
                        $option = '<option value=""></option>';
                        $type_design = type_design_bom('all');
                        foreach ($type_design as $e => $v) {
                            $option .= '<option ' . ($e == $val['type'] ? 'selected' : '') . ' value="' . $e . '">' . $v . '</option>';
                        }

                        $arr_unit_id = [];
                        $displayProduct = 'none';
                        $displayMaterial = 'none';
                        if ($val['type'] == "semi_products" || $val['type'] == "semi_products_outside") {
                            $info = $this->products_model->rowProduct($val['item_id']);
                            array_push($arr_unit_id, $info['unit_id']);
                            $displayProduct = 'block';
                        } else {
                            $info = $this->items_model->rowMaterial($val['item_id']);
                            $exchange = $this->items_model->getExchangeItemsByItemId($val['item_id']);
                            array_push($arr_unit_id, $info['unit_id']);
                            if (!empty($exchange)) {
                                foreach ($exchange as $ke => $va) {
                                    array_push($arr_unit_id, $va['unit_id']);
                                }
                            }
                            $displayMaterial = 'block';
                        }
                        array_push($arr_unit_id, $val['unit_id']);
                        $option_units = '';
                        if (!empty($arr_unit_id)) {
                            $units = $this->products_model->getUnitsByArrId($arr_unit_id);
                            foreach ($units as $a => $el) {
                                $selected_unit = ($el['unitid'] == $val['unit_id']) ? 'selected' : '';
                                $option_units .= '<option ' . $selected_unit . ' value="' . $el['unitid'] . '">' . $el['unit'] . '</option>';
                            }
                        }

                        $addMaterialReplace = $val['type'] == "materials" ? 'display: block;' : 'display: none;';
                        $addMaterialReplace = 'display: block;';

                        $html_BOM .= '<tr class="tnh-item-' . $count_i . '">';
                        $html_BOM .= '<td></td>';
                        $html_BOM .= '<input type="hidden" name="iii" id="iii" class="form-control iii" value="' . $count_i . '">';
                        $html_BOM .= '<input type="hidden" name="k[]" id="k" class="form-control k" value="' . $count_k . '">';
                        $html_BOM .= '<td colspan="1" style="width: 200px;">
                            <select name="type_design_bom_' . $count_i . '[' . $count_k . ']" data-none-selected-text="' . lang('type') . '" id="type_design_bom_' . $count_k . '" class="form-control type_design_bom" required="required">
                                ' . $option . '
                            </select>

                            <div class="td-category-products mtop5" style="display: ' . $displayProduct . ';">
                                <select data-none-selected-text="Danh mục" data-live-search="true" id="category_product_search_bom' . $count_k . '" class="form-control category_product_search_bom">
                                    <option value=""></option>
                                    ' . recursiveCategoryProducts() . '
                                </select>
                            </div>
                            <div class="td-category-materials  mtop5" style="display: ' . $displayMaterial . ';">
                                <select data-none-selected-text="Danh mục" data-live-search="true" id="category_material_search_bom' . $count_k . '" class="form-control category_material_search_bom">
                                    <option value=""></option>
                                    ' . recursiveCategoryItems() . '
                                </select>
                            </div>
                            <div class="checkbox checkbox-info" style="margin-top: 5px;">
                                <input type="checkbox" ' . ($val['face'] == 1 ? 'checked' : '') . ' name="face_' . $count_i . '[' . $count_k . ']" id="face_' . $count_i . '[' . $count_k . ']" value="1">
                                <label for="face_' . $count_i . '[' . $count_k . ']">Mặt trước</label>
                            </div>
                            <div class="checkbox checkbox-info">
                                <input type="checkbox" ' . ($val['face_after'] == 2 ? 'checked' : '') . ' name="face_after_' . $count_i . '[' . $count_k . ']" id="face_after_' . $count_i . '[' . $count_k . ']" value="2">
                                <label for="face_after_' . $count_i . '[' . $count_k . ']">Mặt sau</label>
                            </div>
                        </td>';

                        $html_BOM .= '<td colspan="1">
                            <input type="text" name="items_' . $count_i . '[' . $count_k . ']" id="items_' . $count_k . '" data-placeholder="' . lang('choose') . '" class="modal-select2 it" style="width: 100%;" value="' . $val['item_id'] . '" required="required">
                        </td>';

                        $html_BOM .= '<td colspan="1" class="class="td-unit"">
                            <select data-placeholder="' . lang('choose') . '" id="units_' . $count_k . '" name="units_' . $count_i . '[' . $count_k . ']" class="modal-select2 units" style="width: 100%;" required>
                                ' . $option_units . '
                            </select>
                        </td>';

                        $html_BOM .= '<td colspan="">
                            <input type="text" name="landscape_print_size_' . $count_i . '[' . $count_k . ']" class="form-control landscape_print_size" value="' . $val['landscape_print_size'] . '">
                        </td>';

                        $html_BOM .= '<td colspan="">
                            <input type="text" name="number_children_size_' . $count_i . '[' . $count_k . ']" onchange="calPaperExchange(this)" class="form-control number-format number_children_size" value="' . $val['number_children_size'] . '">
                        </td>';

                        $html_BOM .= '<td colspan="">
                            <input type="text" name="element_item_number_' . $count_i . '[' . $count_k . ']" class="form-control number-format" value="' . $val['quantity'] . '">
                        </td>';

                        $html_BOM .=  '<td colspan="">
                            <input type="text" name="paper_exchange_' . $count_i . '[' . $count_k . ']" class="form-control number-format paper_exchange" ' . (!empty($val['hand_input_paper_exchange']) ? '' : 'readonly') . ' value="' . formatNumber($val['paper_exchange']) . '">
                            <div class="checkbox checkbox-info" style="margin-top: 5px !important;">
                                <input type="checkbox" ' . (!empty($val['hand_input_paper_exchange']) ? 'checked' : '') . ' name="hand_input_paper_exchange_' . $count_i . '[' . $count_k . ']" onchange="handInputPaperExchange(this)" id="hand_input_paper_exchange_' . $count_i . '[' . $count_k . ']" class="hand_input_paper_exchange" value="1">
                                <label for="hand_input_paper_exchange_' . $count_i . '[' . $count_k . ']">Nhập tay</label>
                            </div>
                        </td>';

                        $html_BOM .= '<td colspan="">
                            <input type="text" name="quantity_compensation_' . $count_i . '[' . $count_k . ']" class="form-control number-format" value="' . $val['quantity_compensation'] . '">
                        </td>';

                        $htmlStage = '';
                        $dtStageCriterial = $this->products_model->getStageCriterial($val['stage_id']);
                        if (!empty($dtStageCriterial)) {
                            foreach ($dtStageCriterial as $kS => $vS) {
                                $htmlStage .= '<div>Rút kiểm: ' . $vS['withdraw_check'] . ' - Tiêu chuẩn kiểm: ' . $vS['test_standards'] . '</div>';
                            }
                        }

                        $html_BOM .= '<td>
                            <input type="hidden" name="" id="stage_edit_' . $count_k . '" class="form-control stage_edit" value="' . $val['stage_id'] . '">
                            <select name="stage_' . $count_i . '[' . $count_k . ']"  data-live-search="true" onChange="changeStage(this)" data-none-selected-text="" id="stage_' . $count_k . '" class="form-control stage_item ' . ($val['type_element_item'] == 1 ? 'stage_items_primary' : '') . '">
                                <option value=""></option>
                                ' . $list_stages . '
                            </select>
                            <div class="txt-info-stage">' . $htmlStage . '</div>
                        </td>';

                        $html_BOM .= '<td colspan="">
                        <div class="text-center"><i class="btn btn-danger fa fa-remove remove-element-item"></i></div>
                        </td>';
                        $html_BOM .= '</tr>';

                        $elementItemsReplace = $this->products_model->getBomsElementItemsReplace($val['id']);
                        if (!empty($elementItemsReplace)) {
                            $cIII = $count_i;
                            $cKK = $count_k;

                            foreach ($elementItemsReplace as $r => $vr) {

                                //handling unit exchange
                                $arr_unit_id_replace = [];
                                // $infoReplace = $this->items_model->rowMaterial($vr['item_id_replace']);
                                // $exchangeReplace = $this->items_model->getExchangeItemsByItemId($vr['item_id_replace']);
                                // array_push($arr_unit_id_replace, $infoReplace['unit_id']);
                                // if (!empty($exchangeReplace)) {
                                //     foreach ($exchangeReplace as $ke => $va) {
                                //         array_push($arr_unit_id_replace, $va['unit_id']);
                                //     }
                                // }
                                // array_push($arr_unit_id_replace, $vr['unit_id_replace']);

                                if ($vr['type_replace'] == "semi_products" || $vr['type_replace'] == "semi_products_outside") {
                                    $infoReplace = $this->products_model->rowProduct($vr['item_id_replace']);
                                    array_push($arr_unit_id_replace, $infoReplace['unit_id']);
                                } else {
                                    $infoReplace = $this->items_model->rowMaterial($vr['item_id_replace']);
                                    $exchange = $this->items_model->getExchangeItemsByItemId($vr['item_id_replace']);
                                    array_push($arr_unit_id_replace, $infoReplace['unit_id']);
                                    if (!empty($exchange)) {
                                        foreach ($exchange as $ke => $va) {
                                            array_push($arr_unit_id_replace, $va['unit_id']);
                                        }
                                    }
                                }
                                array_push($arr_unit_id_replace, $vr['unit_id_replace']);

                                $option_units = '';
                                if (!empty($arr_unit_id_replace)) {
                                    $units = $this->products_model->getUnitsByArrId($arr_unit_id_replace);
                                    foreach ($units as $a => $el) {
                                        $selected_unit = ($el['unitid'] == $vr['unit_id_replace']) ? 'selected' : '';
                                        $option_units .= '<option ' . $selected_unit . ' value="' . $el['unitid'] . '">' . $el['unit'] . '</option>';
                                    }
                                }
                                //end

                                $html_BOM .= '<tr class="tnh-item-' . $cIII . '-' . $cKK . '">';

                                $html_BOM .= '<td colspan="1">';
                                $html_BOM .= '<input type="hidden" name="typeMaterial[]" id="typeMaterial" class="form-control typeMaterial" value="' . $vr['type_replace'] . '">';
                                $html_BOM .= '<input type="hidden" name="cIII[]" id="cIII" class="form-control cIII" value="' . $cIII . '">';
                                $html_BOM .= '<input type="hidden" name="cKK[]" id="cKK" class="form-control cKK" value="' . $cKK . '">';
                                $html_BOM .= '</td>';

                                $html_BOM .= '<td colspan="2">';
                                $html_BOM .= '<div class="row">';
                                $html_BOM .= '<div class="col-md-1"><i class="fa fa-magic"></i></div>';
                                $html_BOM .= '<div class="col-md-11"><input type="text" name="items_replace' . $cIII . '[' . $cKK . '][]" id="items_replace_' . $kR . '" data-placeholder="' . lang('choose') . '" class="modal-select2 it-replace" style="width: 100%;" value="' . $vr['item_id_replace'] . '"></div>';
                                $html_BOM .= '</div>';
                                $html_BOM .= '</td>';

                                $html_BOM .= '<td colspan="1">';
                                $html_BOM .= '<select data-placeholder="' . lang('choose') . '" name="units_replace' . $cIII . '[' . $cKK . '][]" id="units_replace_' . $kR . '[]" class="modal-select2 units-replace" style="width: 100%;">' . $option_units . '</select>';
                                $html_BOM .= '</td>';

                                $html_BOM .= '<td colspan="1">';
                                $html_BOM .= '<input type="number" name="element_item_number_replace' . $cIII . '[' . $cKK . '][]" class="form-control" value="' . $vr['quantity_replace'] . '">';
                                $html_BOM .= '</td>';

                                $html_BOM .= '<td colspan="1">';
                                $html_BOM .= '<div class="text-center"><i class="btn btn-danger fa fa-remove remove-element-item-replace"></i></div>';
                                $html_BOM .= '</td>';
                                $html_BOM .= '</tr>';
                                $kR++;
                            }
                        }
                        $count_k++;
                    }
                    $count_i++;
                }
                $data['html_BOM'] = $html_BOM;
                $data['count_i'] = $count_i;
                $data['count_k'] = $count_k;
                $data['kR'] = $kR;
            }

            $data['id'] = $id;
            $data['actions'] = $actions;
            $data['list_stages'] = $list_stages;
            $data['list_stages_primary'] = $list_stages_primary;
            $data['title'] = lang('tnh_add_bom');
            $this->load->view('admin/products/add_bom', $data);
        }
    }

    public function getBoms()
    {
        if (!$this->perViewProductsListBom) {
            accessDenied($js = true);
        }

        $this->datatables
            ->select("
            tbl_boms.id as id,
            tbl_boms.versions as versions,
            tbl_boms.date_start as date_start,
            tbl_boms.date_end as date_end,
            tbl_boms.date_created as date_created,
            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname,'') as created_by
            ", false)
            ->from('tbl_boms')
            ->join('tblstaff', 'tblstaff.staffid = tbl_boms.created_by', 'left');

        $view = '<a class="tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/products/view_bom/$1"><i class="fa fa-file-text-o width-icon-actions"></i> ' . lang('view') . '</a>';

        $edit = $this->perEditProductsListBom ? '<a class="tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/products/add_bom/$1/edit"><i class="fa fa-edit width-icon-actions"></i> ' . lang('edit') . '</a>' : '';

        $copy = $this->perAddProductsListBom ? '<a class="tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/products/add_bom/$1/copy"><i class="fa fa-copy width-icon-actions"></i> ' . lang('copy') . '</a>' : '';

        $delete = $this->perDeleteProductsListBom ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
            <button href=\'' . base_url('admin/products/delete_category_bom/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
            <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
        "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>' : '';

        $actions = '
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
            ' . lang('actions') . '
            <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                <li>' . $view . '</li>
                <li>' . $edit . '</li>
                <li class="not-outside">' . $delete . '</li>
            </ul>
        </div>';

        $this->datatables->add_column('actions', $actions, 'id');
        echo $this->datatables->generate();
    }

    public function view_bom($id)
    {
        if (!$this->perViewProductsListBom) {
            accessDenied($js = true);
        }
        $bom = $this->products_model->rowBomById($id);
        $bom_element = $this->products_model->getBomsElementByBomId($id);

        $bom_html = '';
        if (!empty($bom_element)) {
            $bom_html .=
                '<div class="table-responsive">
                        <table id="tb-datatable" data-bom="1" class="tnh-table table-hover table-bordered table-condensed" style="margin-top: 10px;">
                            <thead>
                                <tr>
                                    <th class="text-center">' . lang('tnh_numbers') . '</th>
                                    <th colspan="2">' . lang('tnh_element_name') . '</th>
                                </tr>
                            </thead>
                            <tbody>';
            foreach ($bom_element as $k => $val) {
                $bom_html .=
                    '<tr>
                                        <td style="width: 80px;" class="text-center"><button class="btn btn-primary cols" data-toggle="collapse" data-target="#demo' . $val['id'] . '">' . (++$k) . '</button></td>
                                        <td style="width: 700px;" colspan="2">' . $val['element_name'] . '</td>
                                    </tr>';
                $items = $this->products_model->getBomsElementItemsByBEI($val['id']);
                $bom_html .=
                    '<tr id="demo' . $val['id'] . '" class="collapse in">
                                        <td colspan="99" style="overflow: hidden;">
                                            <table class="tbbb tnh-table-sub table-bordered table-condensed table-hover" style="margin-top: 0px;">
                                                <thead>
                                                    <tr style="background: #4caf50d4;">
                                                        <th style="width: 50px;" class="text-center">#</th>
                                                        <th style="width: 150px;">' . lang('type') . '</th>
                                                        <th style="width: 150px;">' . lang('code') . '</th>
                                                        <th style="width: 150px;">' . lang('name') . '</th>
                                                        <th class="text-center" style="width: 100px;">' . lang('unit') . '</th>
                                                        <th class="text-center" style="width: 100px;">' . lang('tnh_landscape_print_size') . '</th>
                                                        <th class="text-center" style="width: 100px;">' . lang('tnh_number_children_size') . '</th>
                                                        <th class="text-center" style="width: 100px;">' . lang('tnh_exchange_value') . '</th>
                                                        <th class="text-center" style="width: 100px;">' . lang('tnh_paper_exchange') . '</th>
                                                        <th class="text-center" style="width: 100px;">' . lang('tnh_quantity_compensation') . '</th>
                                                        <th class="text-center" style="width: 100px;">' . lang('tnh_stage') . '</th>
                                                        <th class="text-center" style="width: 100px;">' . lang('tnh_withdraw_check') . '</th>
                                                        <th class="text-center" style="width: 100px;">' . lang('tnh_test_standards') . '</th>
                                                    </tr>
                                                </thead>
                                                <tbody>';
                foreach ($items as $i => $v) {
                    if ($v['type'] == "semi_products" || $v['type'] == "semi_products_outside") {
                        $info = $this->products_model->rowProduct($v['item_id']);
                    } else {
                        $info = $this->items_model->rowMaterial($v['item_id']);
                    }
                    $stage = $this->site_model->rowStagesById($v['stage_id']);
                    $stage_criteria = $this->products_model->getStageCriterial($v['stage_id']);
                    $htmlWithdrawCheck = '';
                    $htmlTestStandards = '';

                    if (!empty($stage_criteria)) {
                        foreach ($stage_criteria as $sC => $vC) {
                            $htmlWithdrawCheck .= '<div class="">' . $vC['withdraw_check'] . '</div>';
                            $htmlTestStandards .= '<div class="">' . $vC['test_standards'] . '</div>';
                        }
                    }

                    $bom_html .= '<tr>
                                                        <td class="text-center">' . (++$i) . '</td>
                                                        <td>
                                                            ' . lang($v['type']) . '
                                                            <div class="text-danger">
                                                                ' . ($v['face'] == 1 ? lang('Mặt trước') : ($v['face'] == 2 ? lang('Mặt sau') : '')) . '
                                                                ' . ($v['face_after'] == 1 ? lang('Mặt trước') : ($v['face_after'] == 2 ? lang(', Mặt sau') : '')) . '
                                                            </div>
                                                        </td>
                                                        <td>' . $info['code'] . '</td>
                                                        <td>' . $info['name'] . '</td>
                                                        <td class="text-center">' . $v['unit'] . '</td>
                                                        <td class="text-center">' . $v['landscape_print_size'] . '</td>
                                                        <td class="text-center">' . $v['number_children_size'] . '</td>
                                                        <td class="text-center">' . $v['quantity'] . '</td>
                                                        <td class="text-center">
                                                            ' . formatnumber($v['paper_exchange']) . '
                                                            ' . ($v['hand_input_paper_exchange'] ? '<div class="text-danger">Nhập tay</div>' : '') . '
                                                        </td>
                                                        <td class="text-center">' . $v['quantity_compensation'] . '</td>
                                                        <td class="text-center">' . $stage['name'] . '</td>
                                                        <td class="text-center">' . $htmlWithdrawCheck . '</td>
                                                        <td class="text-center">' . $htmlTestStandards . '</td>
                                                    </tr>';
                    $elementItemsReplace = $this->products_model->getBomsElementItemsReplace($v['id']);
                    if (!empty($elementItemsReplace)) {
                        foreach ($elementItemsReplace as $n => $vv) {
                            if ($vv['type_replace'] == "semi_products" || $vv['type_replace'] == "semi_products_outside") {
                                $infoReplace = $this->products_model->rowProduct($vv['item_id_replace']);
                                $strTypeReplace = lang('tnh_product_outside_replace');
                            } else {
                                $infoReplace = $this->items_model->rowMaterial($vv['item_id_replace']);
                                $strTypeReplace = lang('tnh_material_replace');
                            }
                            // $infoReplace = $this->items_model->rowMaterial($vv['item_id_replace']);
                            $unitReplace = $this->unit_model->rowUnit($vv['unit_id_replace']);
                            $bom_html .= '<tr style="background: #9e9e9e94;">
                                                                <td class="text-center">' . ($i) . '.' . (++$n) . '</td>
                                                                <td>' . $strTypeReplace . '</td>
                                                                <td>' . $infoReplace['code'] . '</td>
                                                                <td>' . $infoReplace['name'] . '</td>
                                                                <td class="text-center">' . $unitReplace['unit'] . '</td>
                                                                <td class="text-center">' . $vv['quantity_replace'] . '</td>
                                                            </tr>';
                        }
                    }
                }
                $bom_html .= '
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>';
            }
            $bom_html .= '</tbody>
                        </table>
                    </div>';
        }
        $data['created_by'] = get_staff_full_name($bom['created_by']);
        if ($bom['updated_by']) {
            $data['updated_by'] = get_staff_full_name($bom['updated_by']);
        }
        $data['bom'] = $bom;
        $data['bom_html'] = $bom_html;
        $data['id'] = $id;
        $this->load->view('admin/products/view_bom', $data);
    }

    function delete_category_bom($id)
    {
        if (!$this->perDeleteProductsListBom) {
            accessDenied($js = true);
        }
        $data = [];
        if ($id) {
            $bom = $this->products_model->rowBomById($id);
            if ($this->products_model->deleteCategoryBomById($id)) {
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

    public function getList_items()
    {
        if ($this->input->post()) {
            $result = array();
            $data = $this->input->post();
            foreach ($data['arrID'] as $key => $value) {
                $result[$key] = get_table_where('tbl_products', array('id' => $value), '', 'row');
            }
            echo json_encode($result);
        }
    }

    public function pdf()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $result = array();
            $result['total_number'] = 0;
            foreach ($data['item'] as $key => $value) {
                $result['item'][$key] = get_table_where('tbl_products', array('id' => $value['id_item']), '', 'row');
                $result['item'][$key]->price = $result['item'][$key]->price_import;
                $result['item'][$key]->quantity_print = $value['quantity_print'];
                $result['total_number'] += $value['quantity_print'];
            }
            $result['print_show'] = $data['type_show'];
            if ($data['type_size'] == 0) {
                $this->load->view('admin/invoice_items/printBarcode_1_stamp', $result);
            } else if ($data['type_size'] == 1) {
                $this->load->view('admin/invoice_items/printBarcode_2_stamp', $result);
            } else if ($data['type_size'] == 2) {
                $this->load->view('admin/invoice_items/printBarcode_3_stamp', $result);
            } else if ($data['type_size'] == 3) {
                $this->load->view('admin/invoice_items/printBarcode_100_stamp', $result);
            }
        }
    }

    /**
     * 01/07/2020
     */
    public function import_bom()
    {
        if (!$this->perAddProducts) {
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
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('O');
            $arraydata          = array();

            $row_start = $this->input->post('row_start') ? $this->input->post('row_start') : 4;
            $row_end = $this->input->post('row_end') ? $this->input->post('row_end') : $highestRow;
            for ($row = $row_start; $row <= $row_end; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value      = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 2][$col] = $value;
                }
            }

            $options = [];
            $count = 0;
            $errors = '';
            $cRow = 4;
            $index_parent = 0;
            $index_parent_element = 0;
            $pCode = '';
            $pVersions = '';
            $pElement = '';
            $dataImport = [];

            foreach ($arraydata as $key => $value) {
                // 0: product_code
                // 1: product_name
                // 2: version_bom
                // 3: element_name
                // 4: type
                // 5: item_code
                // 6: item_name
                // 7: unit
                // 8: landscape_print_size
                // 9: vertical_print_size
                // 10: number_children_size
                // 11: quantity
                // 12: paper_exchange
                // 13: quantity_compensation
                // 14: stage

                $product_code = trim($value[0]);
                $product_name = trim($value[1]);
                $version_bom = trim($value[2]);
                // $date_start = $value[3];
                // if (gettype($date_start) == 'double' || gettype($date_start) == 'int')
                // {
                //     $date_start = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($date_start));
                // } else if (gettype($date_start) == 'string') {
                //     $date_start = to_sql_date($date_start);
                // }
                // $date_end = $value[4];
                // if (gettype($date_end) == 'double' || gettype($date_end) == 'int')
                // {
                //     $date_end = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($date_end));
                // } else if (gettype($date_end) == 'string') {
                //     $date_end = to_sql_date($date_end);
                // }

                // $element_name = trim($value[5]);
                // $type = trim($value[6]);
                // $item_code = trim($value[7]);
                // $item_name = trim($value[8]);
                // $unit = trim($value[9]);
                // $quantity = number_unformat($value[10]);
                // $leadtime = number_unformat($value[11]);
                // $stage = trim($value[12]);
                // $machines = trim($value[13]);
                // $quantity_compensation = number_unformat($value[14]);
                $type_element = 1;
                $machines = 0;

                $element_name = trim($value[3]);
                $type = trim($value[4]);
                $item_code = trim($value[5]);
                $item_name = trim($value[6]);
                $unit = trim($value[7]);
                $landscape_print_size = trim($value[8]);
                $vertical_print_size = number_unformat($value[9]);
                $number_children_size = number_unformat($value[10]);
                $quantity = number_unformat($value[11]);
                $paper_exchange = number_unformat($value[12]);
                $quantity_compensation = number_unformat($value[13], false);
                $stage = trim($value[14]);
                $leadtime = 0;

                $hand_input_paper_exchange = 0;
                if (!empty($paper_exchange)) {
                    $hand_input_paper_exchange = 1;
                } else {
                    if (!empty($number_children_size)) {
                        $paper_exchange = roundNumberFormat(1 / $number_children_size);
                    }
                }

                //handling product
                if ((!empty($product_code) && $product_code != $pCode) || ($version_bom != $pVersions)) {
                    $dataImport[$index_parent]['product_code'] = $product_code;
                    $dataImport[$index_parent]['product_name'] = $product_name;
                    $dataImport[$index_parent]['version_bom'] = $version_bom;
                    // $dataImport[$index_parent]['date_start'] = $date_start;
                    // $dataImport[$index_parent]['date_end'] = $date_end;
                    $dataImport[$index_parent]['date_start'] = NULL;
                    $dataImport[$index_parent]['date_end'] = NULL;

                    $parent_current = $index_parent;
                    $pCode = $product_code;
                    $pVersions = $version_bom;
                    $index_parent++;

                    $pElement = '';
                    $index_parent_element = 0;
                }
                //end handling product

                //handling element
                if (!empty($element_name) && mb_strtolower(tnh_vn_to_str_cs($element_name), 'UTF-8') != mb_strtolower(tnh_vn_to_str_cs($pElement), 'UTF-8')) {

                    if (mb_strtolower(tnh_vn_to_str_cs($element_name), 'UTF-8') == 'nguyen vat lieu chinh') {
                        $type_element = 1;
                    } else if (mb_strtolower(tnh_vn_to_str_cs($element_name), 'UTF-8') == 'nguyen vat lieu phu') {
                        $type_element = 2;
                    } else {
                        $type_element = 1;
                    }

                    $dataImport[$parent_current]['element'][$index_parent_element] = [
                        'element_name' => $element_name,
                        'quantity_element' => 1,
                        'type_element' => $type_element,
                    ];

                    $parent_current_element = $index_parent_element;
                    $pElement = $element_name;
                    $index_parent_element++;

                    $dataImport[$parent_current]['element'][$parent_current_element]['items'][] = [
                        'type' => $type,
                        'item_code' => $item_code,
                        'item_name' => $item_name,
                        'unit' => $unit,
                        'quantity' => $quantity,
                        'leadtime' => $leadtime,
                        'stage' => $stage,
                        'machines' => $machines,
                        'type_element_item' => $type_element,
                        'quantity_compensation' => $quantity_compensation,

                        'landscape_print_size' => $landscape_print_size,
                        'vertical_print_size' => $vertical_print_size,
                        'number_children_size' => $number_children_size,
                        'paper_exchange' => $paper_exchange,
                        'hand_input_paper_exchange' => $hand_input_paper_exchange,
                    ];
                    continue;
                }
                //end handling element
                if (empty($item_code)) continue;
                $dataImport[$parent_current]['element'][$parent_current_element]['items'][] = [
                    'type' => $type,
                    'item_code' => $item_code,
                    'item_name' => $item_name,
                    'unit' => $unit,
                    'quantity' => $quantity,
                    'leadtime' => $leadtime,
                    'stage' => $stage,
                    'machines' => $machines,
                    'type_element_item' => $type_element,
                    'quantity_compensation' => $quantity_compensation,

                    'landscape_print_size' => $landscape_print_size,
                    'vertical_print_size' => $vertical_print_size,
                    'number_children_size' => $number_children_size,
                    'paper_exchange' => $paper_exchange,
                    'hand_input_paper_exchange' => $hand_input_paper_exchange,
                ];
            }

            // print_arrays($dataImport);
            //handling import bom
            if (!empty($dataImport)) {
                foreach ($dataImport as $key => $value) {
                    $optionImport = [];

                    $product_code = $value['product_code'];
                    if (!$product_code) {
                        continue;
                    }

                    $product_name = $value['product_name'];
                    // $product = $this->products_model->rowProductByCodeBom($product_code);
                    $product = $this->products_model->rowProductByCode($product_code);
                    if (!$product) {
                        $errors .= '<div class="text-danger">' . $product_code . '[' . $product_name . '] ' . lang('chưa có trong phần mềm') . '.</div>';
                        continue;
                    }

                    if ($product['type_products'] == "semi_products_outside") {
                        $errors .= '<div class="text-danger">' . $product_code . '[' . $product_name . '] là bán thành phẩm mua ngoài không thêm thể thêm BOM</div>';
                        continue;
                    }

                    $product_id = $product['id'];
                    $versions = $value['version_bom'];
                    if ($this->products_model->checkProductVersions($product_id, $versions)) {
                        $errors .= '<div class="text-danger">' . $product_code . '[' . $product_name . '] thêm không được vì phiên bản ' . $versions . ' đã tồn tại trong phần mềm.</div>';
                        continue;
                    }

                    $elements = $value['element'];
                    if (empty($elements)) {
                        $errors .= '<div class="text-danger">' . $product_code . '[' . $product_name . '] thêm không được vì thành phần không có';
                        continue;
                    }

                    //handling save
                    $optionImport = [
                        'versions' => $versions,
                        'product_id' => $product_id,
                        'date_start' => $value['date_start'],
                        'date_end' => $value['date_end'],
                        'date_created' => date('Y-m-d H:i:s'),
                        'created_by' => get_staff_user_id(),
                        'element' => []
                    ];
                    //
                    // print_arrays($element);
                    $flag = true;
                    foreach ($elements as $k => $val) {
                        $element_name = $val['element_name'];
                        $quantity_element = $val['quantity_element'];
                        $type_element = $val['type_element'];

                        //handling save
                        $optionImport['element'][$k] = [
                            'element_name' => $element_name,
                            'element_number' => $quantity_element,
                            'type_element' => $type_element,
                        ];
                        //

                        $items = $val['items'];

                        if (empty($items)) {
                            $errors .= '<div class="text-danger">' . $product_code . '[' . $product_name . '] thêm không được vì mặt hàng trong thành phần không có';
                            break;
                        }

                        $nn = 0;
                        $kParent = 0;
                        $minusPre = 1;
                        $type_pre = 0;
                        foreach ($items as $i => $v) {
                            $type = $v['type'];
                            if ($type != 1 && $type != 2 && $type != 3 && $type != 4) {
                                $errors .= '<div class="text-danger">' . $product_code . '[' . $product_name . '] thêm không được vì Loại ' . $type . ' không đúng định dạng [1, 2, 3, 4].</div>';
                                $flag = false;
                                break;
                            }

                            // if (empty($v['landscape_print_size']) || empty($v['vertical_print_size']) || empty($v['number_children_size']) || empty($v['quantity'])) {
                            //     $errors.= '<div class="text-danger">'.$product_code.'['.$product_name.'] chưa có đủ các trường bắt buộc</div>';
                            //     $flag = false;
                            //     break;
                            // }

                            $item_code = $v['item_code'];
                            $item_name = $v['item_name'];
                            if ($type == 1) {
                                $typeItem = "materials";
                                $infoItem = $this->products_model->rowMaterialByCode($item_code);
                            } else if ($type == 2) {
                                $typeItem = "semi_products";
                                $infoItem = $this->products_model->rowProductByCode($item_code);
                            } else if ($type == 3) {
                                $typeItem = "semi_products_outside";
                                $infoItem = $this->products_model->rowProductByCode($item_code);
                            } else if ($type == 4) {
                                continue;
                                //NVL thay thế
                                $indexPar = $i - $minusPre;
                                $type_pre = $items[$indexPar]['type'];

                                if ($type_pre == 1) {
                                    $typeItem = "materials";
                                    $infoItem = $this->products_model->rowMaterialByCode($item_code);
                                } else if ($type_pre == 2) {
                                    $typeItem = "semi_products";
                                    $infoItem = $this->products_model->rowProductByCode($item_code);
                                } else if ($type_pre == 3) {
                                    $typeItem = "semi_products_outside";
                                    $infoItem = $this->products_model->rowProductByCode($item_code);
                                }
                                //
                            }

                            $unit = $v['unit'];
                            $infoUnit = $this->unit_model->rowUnitByCode($unit, 'unitid', "where");
                            if (empty($infoUnit)) {
                                $errors .= '<div class="text-danger">' . $product_code . '[' . $product_name . '] thêm không được đơn vị [' . $unit . '] không có trong phần mềm.</div>';
                                $flag = false;
                                break;
                            }

                            if (empty($infoItem)) {
                                $errors .= '<div class="text-danger">' . $product_code . '[' . $product_name . '] thêm không được vì NVL/BTP ' . $item_code . '[' . $item_name . '] không có trong phần mềm.</div>';
                                $flag = false;
                                break;
                            } else {
                                // if ($type == 1 || $type == 4) {
                                if ($type == 1 || $type_pre == 1) {
                                    //material
                                    $unitExchange = $this->products_model->rowExchangeItems($infoItem['id'], $infoUnit['unitid']);
                                    // if ($infoUnit['unitid'] != $infoItem['unit_id'] && empty($unitExchange))
                                    if (empty($unitExchange)) {
                                        $errors .= '<div class="text-danger">' . $product_code . '[' . $product_name . '] thêm không được vì NVL/BTP ' . $item_code . '[' . $item_name . '] không có đơn vị [' . $unit . '].</div>';
                                        $flag = false;
                                        break;
                                    }
                                } else if ($type == 2 || $type == 3 || $type_pre == 2 || $type_pre == 3) {
                                    if ($infoUnit['unitid'] != $infoItem['unit_id']) {
                                        $errors .= '<div class="text-danger">' . $product_code . '[' . $product_name . '] thêm không được vì NVL/BTP ' . $item_code . '[' . $item_name . '] không có đơn vị [' . $unit . '].</div>';
                                        $flag = false;
                                        break;
                                    }
                                }

                                $stage = $v['stage'];
                                $stage_id = 0;
                                if (!empty($stage)) {
                                    $infoStage = $this->products_model->rowStageByCode($stage);
                                    if (empty($infoStage)) {
                                        $errors .= '<div class="text-danger">' . $product_code . '[' . $product_name . '] thêm không được vì giai đoạn [' . $stage . '] không có trong phần mềm.</div>';
                                        $flag = false;
                                        break;
                                    }
                                    $stage_id = $infoStage['id'];
                                } else {
                                    // $errors.= '<div class="text-danger">'.$product_code.'['.$product_name.'] thêm không được vì chưa nhập giai đoạn.</div>';
                                }

                                $machines = $v['machines'];
                                $machines_id = 0;
                                if (!empty($machines)) {
                                    $infoMachines = $this->products_model->rowMachinesByCode($machines);
                                    if (empty($infoMachines)) {
                                        $errors .= '<div class="text-danger">' . $product_code . '[' . $product_name . '] thêm không được vì máy móc [' . $machines . '] không có trong phần mềm.</div>';
                                        $flag = false;
                                        break;
                                    }
                                    $machines_id = $infoMachines['id'];
                                }

                                if ($type == 4) {
                                    //kiểm tra type trước nó có phải là 1:NVL ko
                                    $indexPar = $i - $minusPre;
                                    // if (empty($items[$indexPar])) {
                                    //     $errors.= '<div class="text-danger">'.$product_code.'['.$product_name.'] thêm không được vì NVL thay thế phải nằm sau NVL.</div>';
                                    //     break;
                                    // }

                                    $type_pre = $items[$indexPar]['type'];
                                    if ($type_pre == 4) {
                                        // $errors.= '<div class="text-danger">'.$product_code.'['.$product_name.'] thêm không được vì NVL thay thế phải nằm sau NVL.</div>';
                                        $errors .= '<div class="text-danger">' . $product_code . '[' . $product_name . '] thêm không được vì NVL thay thế phải nằm sau NVL chính.</div>';
                                        $flag = false;
                                        break;
                                    }

                                    $optionImport['element'][$k]['items'][$indexPar]['replace'][] = [
                                        'type_replace' => $typeItem,
                                        'item_id_replace' => $infoItem['id'],
                                        'unit_id_replace' => $infoUnit['unitid'],
                                        'element_item_number_replace' => $v['quantity'],
                                        'leadtime_replace' => $v['leadtime'],
                                        'stage_replace' => $stage_id,
                                    ];
                                    $minusPre++;
                                    $nn++;
                                    continue;
                                } else {
                                    $nn = 0;
                                    $minusPre = 1;
                                }

                                //handling save
                                $optionImport['element'][$k]['items'][$i] = [
                                    'type' => $typeItem,
                                    'item_id' => $infoItem['id'],
                                    'unit_id' => $infoUnit['unitid'],
                                    'element_item_number' => $v['quantity'],
                                    'leadtime' => $v['leadtime'],
                                    'stage' => $stage_id,
                                    'machines_id' => $machines_id,
                                    'type_element_item' => $v['type_element_item'],
                                    'quantity_compensation' => $v['quantity_compensation'],

                                    'landscape_print_size' => $v['landscape_print_size'],
                                    'vertical_print_size' => $v['vertical_print_size'],
                                    'number_children_size' => $v['number_children_size'],
                                    'paper_exchange' => $v['paper_exchange'],
                                    'hand_input_paper_exchange' => $v['hand_input_paper_exchange'],
                                ];
                                //
                            }
                        }
                    }
                    if ($flag == false) {
                        continue;
                    }

                    // print_arrays($optionImport);
                    $status = "unapplication";
                    $q = $this->products_model->insertBOM($optionImport, $status, $bom_id = 0, $actions = "add");
                    if ($q) {
                        $this->products_model->updateProducts($product_id, ['versions' => $versions]);
                        $count++;
                    }
                }
            }
            //end handling import bom
            //handling show nofitications
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
            $data['title'] = _l('tnh_import_bom');
            $this->load->view('admin/products/import_bom', $data);
        }
    }

    public function updateProductsStages()
    {
        $this->db->select('tbl_products.id');
        $this->db->from('tbl_products');
        $this->db->where('NOT EXISTS (
            SELECT tbl_product_stages.id
            FROM tbl_product_stages
            WHERE tbl_product_stages.product_id = tbl_products.id
        )');
        $products = $this->db->get()->result_array();
        if (!empty($products)) {
            foreach ($products as $key => $value) {
                $this->products_model->handlingDesignStages($value['id']);
            }
        }
    }

    public function checkStageImportOusource()
    {
        $data = [];
        $id = $this->input->post('id');
        $this->db->select('tbl_stages.*');
        $this->db->from('tbl_stages');
        $this->db->where('stage_import_outsource', 1);
        if (!empty($id)) {
            $this->db->where('id !=', $id);
        }
        $checkStage = $this->db->get()->row_array();
        if (!empty($checkStage)) {
            $data['result'] = true;
        } else {
            $data['result'] = false;
        }
        echo json_encode($data);
        die;
    }

    public function checkStageOusource()
    {
        $data = [];
        $id = $this->input->post('id');
        $this->db->select('tbl_stages.*');
        $this->db->from('tbl_stages');
        $this->db->where('status_default_outsource', 1);
        if (!empty($id)) {
            $this->db->where('id !=', $id);
        }
        $checkStage = $this->db->get()->row_array();
        if (!empty($checkStage)) {
            $data['result'] = true;
        } else {
            $data['result'] = false;
        }
        echo json_encode($data);
        die;
    }

    public function category_stages()
    {
        if (!$this->perViewCategory) {
            accessDenied();
        }
        $data['tnh'] = true;
        $data['title'] = _l('tnh_category_stages');
        $this->load->view('admin/products/category_stages', $data);
    }

    public function add_category_stages($id = 0)
    {
        if (!$this->perAddCategory) {
            accessDenied($js = true);
        }
        $data = [];
        $categoryStages = [];
        if (!empty($id)) {
            $categoryStages = $this->products_model->rowCategoryStages($id);
        }
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if (empty($id) || (!empty($categoryStages) && $categoryStages['code'] != $this->input->post('code'))) {
                $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_category_stages.code]');
            }
            if ($this->form_validation->run() == true) {
                $name = $this->input->post('name');
                $code = $this->input->post('code');
                $is_in = !empty($this->input->post('is_in')) ? '1' : '0';
                $check_offset = !empty($this->input->post('check_offset')) ? 1 : 0;
                $type_productionlist_id = $this->input->post('type_productionlist_id');

                $options = [
                    'name' => $name,
                    'code' => $code,
                    'is_in' => $is_in,
                    'check_offset' => $check_offset,
                    'type_productionlist_id' => $type_productionlist_id,
                ];

                if (!empty($id)) {
                    $rs = $this->products_model->updateCategoryStages($id, $options);
                } else {
                    $rs = $this->products_model->insertCategoryStages($options);
                }
                if ($rs) {
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
            $data['type_production_list'] = $this->products_model->getTypeProductionList();
            $data['categoryStages'] = $categoryStages;
            $data['id'] = $id;
            $this->load->view('admin/products/add_category_stages', $data);
        }
    }

    function getCategoryStages()
    {
        if (!$this->perViewCategory) {
            accessDenied($js = true);
        }

        $this->datatables->select("
            tbl_category_stages.id as id,
            tbl_category_stages.code as code,
            tbl_category_stages.name as name,
            tbl_type_productionlist.code as type,
            tbl_category_stages.check_productivity as check_productivity,
            tbl_category_stages.is_bangiao as is_bangiao
        ", FALSE)
            ->from('tbl_category_stages');
        $this->datatables->where('tbl_category_stages.type_use', 0);
        $this->datatables->join('tbl_type_productionlist', 'tbl_type_productionlist.id = tbl_category_stages.type_productionlist_id', 'left');

        $edit = $this->perEditCategory ? '<a class="tnh-modal btn btn-success btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/products/add_category_stages/$1"><i class="fa fa-pencil"></i></a>' : '';

        $delete = $this->perDeleteCategory ? '<button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
        <button href=\'' . base_url('admin/products/delete_category_stages/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
        <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
        "><i class="fa fa-remove"></i></button>' : '';

        $this->datatables->add_column('actions', '
            <div class="text-center">
                ' . $edit . '
                ' . $delete . '
            </div>
        ', 'id');
        echo $this->datatables->generate();
    }

    function delete_category_stages($id)
    {
        if (!$this->perDeleteCategory) {
            accessDenied($js = true);
        }
        $data = [];
        if ($id) {
            if ($this->products_model->checkExistCategoryStages($id)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_exist_not_delete');
                echo json_encode($data);
                return;
            }

            if ($this->products_model->deleteCategoryStages($id)) {
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

    public function change_status_products($id, $status)
    {
        if ($this->perEditProducts) {
            if ($this->input->is_ajax_request()) {
                $this->products_model->updateProducts($id, ['is_no_stock' => $status]);
            }
        }
    }

    public function changeMachines()
    {
        $data = [];
        $machines_id = $this->input->post('machines_id');
        $this->db->select('tbl_machines.*');
        $this->db->from('tbl_machines');
        $this->db->where('tbl_machines.id', $machines_id);
        $machines = $this->db->get()->row_array();
        $info = '';
        if (!empty($machines)) {

            $process = $this->category_model->getMachinesProcess($machines_id);
            $divProcess = '';
            if (!empty($process)) {
                foreach ($process as $k => $v) {
                    $divProcess .= $v['process'] . ', ';
                }
            }

            $info .= '<div class="mtop5">
                <div><b>' . lang('tnh_standard') . '</b>: ' . $machines['standard'] . '</div>
                <div><b>' . lang('tnh_pp_measure') . '</b>: ' . $machines['pp_measure'] . '</div>
                <div><b>' . lang('tnh_quota_productivity') . '</b>: ' . $machines['quota_productivity'] . '</div>
                <div><b>' . lang('tnh_operating_gauge') . '</b>: ' . $machines['operating_gauge'] . '</div>
                <div><b>' . lang('tnh_process') . '</b>: ' . $divProcess . '</div>
            </div>';
        }
        $data['info'] = $info;
        echo json_encode($data);
    }

    public function list_stages_price()
    {
        $data['tnh'] = true;
        $data['title'] = _l('tnh_list_stages_price');
        $this->load->view('admin/products/list_stages_price', $data);
    }

    public function formula_m2($id, $status)
    {
        if ($this->perEditProducts) {
            if ($this->input->is_ajax_request()) {
                $this->products_model->updateStages($id, ['formula_m2' => $status]);
            }
        }
    }

    public function copy_bom($id)
    {
        if (!$this->perAddProducts) {
            accessDenied($js = true);
        }

        $products = $this->products_model->rowProduct($id);
        if ($products['type_products'] == 'semi_products_outside') {
            refererModel(lang('BTP mua ngoài không có thiết kế BOM'));
            die;
        }

        if ($this->input->post('save')) {
            $data = [];

            $bom = $this->input->post('bom');
            if (empty($bom)) {
                $data['result'] = 0;
                $data['message'] = lang('Vui lòng chọn thành phẩm sao chép BOM');
                echo json_encode($data);
                die;
            }

            $this->db->select('
                tbl_product_versions.*
            ', false);
            $this->db->from('tbl_product_versions');
            $this->db->where('tbl_product_versions.product_id', $bom);
            $product_versions = $this->db->get()->result_array();
            if (!empty($product_versions)) {
                foreach ($product_versions as $key => $bom) {

                    $this->db->from('tbl_product_versions');
                    $this->db->where('tbl_product_versions.product_id', $id);
                    $this->db->where('tbl_product_versions.versions', $bom['versions']);
                    $isVersions = $this->db->count_all_results();
                    $c_versions = $bom['versions'];
                    if (!empty($isVersions)) {
                        $c_versions = $bom['versions'] . time();
                    }

                    $options = [
                        'versions' => $c_versions,
                        'product_id' => $id,
                        'status' => '',
                        'date_start' => $bom['date_start'],
                        'date_end' => $bom['date_end'],
                        'date_created' => $bom['date_created'],
                        'created_by' => $bom['created_by'],
                    ];

                    $this->db->select('
                        tbl_versions_element.*
                    ', false);
                    $this->db->from('tbl_versions_element');
                    $this->db->where('tbl_versions_element.version_id', $bom['id']);
                    $element = $this->db->get()->result_array();
                    if (!empty($element)) {
                        foreach ($element as $kE => $valE) {
                            $options['element'][$kE]['element_name'] = $valE['element_name'];
                            $options['element'][$kE]['element_number'] = $valE['quantity'];
                            $options['element'][$kE]['type_element'] = $valE['type_element'];

                            $this->db->select('tbl_element_items.*', false);
                            $this->db->from('tbl_element_items');
                            $this->db->where('tbl_element_items.element_id', $valE['id']);
                            $element_items = $this->db->get()->result_array();
                            if (!empty($element_items)) {
                                foreach ($element_items as $kEI => $vEI) {
                                    $options['element'][$kE]['items'][$kEI]['type'] = $vEI['type'];
                                    $options['element'][$kE]['items'][$kEI]['item_id'] = $vEI['item_id'];
                                    $options['element'][$kE]['items'][$kEI]['unit_id'] = $vEI['unit_id'];
                                    $options['element'][$kE]['items'][$kEI]['element_item_number'] = $vEI['quantity'];
                                    $options['element'][$kE]['items'][$kEI]['leadtime'] = $vEI['leadtime'];
                                    $options['element'][$kE]['items'][$kEI]['stage'] = $vEI['stage_id'];
                                    $options['element'][$kE]['items'][$kEI]['machines_id'] = $vEI['machines_id'];
                                    $options['element'][$kE]['items'][$kEI]['quantity_compensation'] = $vEI['quantity_compensation'];
                                    $options['element'][$kE]['items'][$kEI]['type_element_item'] = $vEI['quantity_compensation'];

                                    $options['element'][$kE]['items'][$kEI]['landscape_print_size'] = $vEI['landscape_print_size'];
                                    $options['element'][$kE]['items'][$kEI]['vertical_print_size'] = $vEI['vertical_print_size'];
                                    $options['element'][$kE]['items'][$kEI]['number_children_size'] = $vEI['number_children_size'];
                                    $options['element'][$kE]['items'][$kEI]['paper_exchange'] = $vEI['paper_exchange'];
                                    $options['element'][$kE]['items'][$kEI]['hand_input_paper_exchange'] = $vEI['hand_input_paper_exchange'];
                                    $options['element'][$kE]['items'][$kEI]['face'] = $vEI['face'];
                                    $options['element'][$kE]['items'][$kEI]['face_after'] = $vEI['face_after'];
                                }
                            }
                        }
                    }

                    $q = $this->products_model->insertBOM($options);

                    if ($key == 0) {
                        $this->products_model->updateProducts($id, ['versions' => $c_versions]);
                    }
                }

                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('Không có BOM sao chép');
            }
            echo json_encode($data);
            die;
        }

        $data['id'] = $id;
        $data['products'] = $products;
        $this->load->view('admin/products/copy_bom', $data);
    }

    public function searchBOM()
    {
        $term = $this->input->get('term');
        $limit = 50;
        $params = $this->input->get('params');

        $type_products = $params['type_products'];

        $data = [];
        $this->db->select('
            tbl_products.id as id,
            CONCAT(tbl_products.name, "(", tbl_products.code,")") as text 
        ', false);
        $this->db->from('tbl_products');
        if ($type_products == "semi_products") {
            $this->db->where('tbl_products.type_products', 'semi_products');
        }

        $this->db->where(' exists (
            SELECT tbl_product_versions.id
            FROM tbl_product_versions
            WHERE tbl_product_versions.product_id = tbl_products.id
        )', false, false);

        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_products.code', $term);
            $this->db->or_like('tbl_products.name', $term);
            $this->db->group_end();
        }

        $this->db->limit($limit);
        $data['results'] = $this->db->get()->result_array();
        echo json_encode($data);
    }

    public function modal_excel_stages()
    {
        $data['title'] = _l('tnh_import_stages');
        $this->load->view('admin/products/import_stages', $data);
    }

    public function import_stages()
    {
        ob_end_clean();
        ini_set('max_execution_time', 800);
        $dataPost = $this->input->post();
        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $this->load->helper('security');
        $count = 0;
        $data = [];
        if (!empty($_FILES['file'])) {
            $fullfile = $_FILES['file']['tmp_name'];
            $nameFile = $_FILES['file']['name'];
            $extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if ($extension != 'XLSX' && $extension != 'XLS') {
                echo json_encode(['success' => false, 'alert_type' => 'success', 'message' => _l('cong_not_type')]);
                die();
            }
            $inputFileType = PHPExcel_IOFactory::identify($fullfile);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            // $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load("$fullfile");

            $total_sheets = $objPHPExcel->getSheetCount();

            $allSheetName       = $objPHPExcel->getSheetNames();
            $objWorksheet       = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow         = $objWorksheet->getHighestRow();
            $highestColumn      = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('F');
            $arraydata          = array();

            $fields = $this->input->post('fields');
            for ($row = 2; $row <= $highestRow; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value      = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 2][$col] = $value;
                }
            }
            foreach ($arraydata as $key => $value) {
                $code_category = $value[0];
                $code = $value[1];
                $name = $value[2];
                $note = $value[3];
                $time_watch_cards = $value[4];
                $number_watch_cards = $value[5];

                if (empty($code) || empty($name) || empty($code_category)) {
                    continue;
                }

                $dtCategoryStages = $this->products_model->getCategoryStagesByCode($code_category);
                if (empty($dtCategoryStages)) continue;

                $options = [
                    'code' => $code,
                    'name' => $name,
                    'category_stages' => $dtCategoryStages['id'],
                    'note' => $note,
                    'time_watch_cards' => $time_watch_cards,
                    'number_watch_cards' => $number_watch_cards,
                ];
                $checkCode = $this->products_model->checkCodeExistStages($code);
                if (!empty($checkCode['id'])) {
                    $rs = $this->products_model->updateStages($checkCode['id'], $options);
                } else {
                    $rs = $this->products_model->insertStages($options);
                }

                if ($rs) {
                    $count++;
                }
            }
        }
        echo json_encode(
            [
                'success' => true,
                'alert_type' => 'success',
                'message' => 'Import thành công ' . $count . ' dòng',
            ]
        );
        die();
    }

    public function modal_excel_category_stages()
    {
        $data['title'] = _l('tnh_import_category_stages');
        $this->load->view('admin/products/import_category_stages', $data);
    }

    public function import_category_stages()
    {
        ob_end_clean();
        ini_set('max_execution_time', 800);
        $dataPost = $this->input->post();
        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $this->load->helper('security');
        $count = 0;
        $data = [];
        if (!empty($_FILES['file'])) {
            $fullfile = $_FILES['file']['tmp_name'];
            $nameFile = $_FILES['file']['name'];
            $extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if ($extension != 'XLSX' && $extension != 'XLS') {
                echo json_encode(['success' => false, 'alert_type' => 'success', 'message' => _l('cong_not_type')]);
                die();
            }
            $inputFileType = PHPExcel_IOFactory::identify($fullfile);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            // $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load("$fullfile");

            $total_sheets = $objPHPExcel->getSheetCount();

            $allSheetName       = $objPHPExcel->getSheetNames();
            $objWorksheet       = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow         = $objWorksheet->getHighestRow();
            $highestColumn      = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('B');
            $arraydata          = array();

            $fields = $this->input->post('fields');
            for ($row = 2; $row <= $highestRow; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value      = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 2][$col] = $value;
                }
            }

            foreach ($arraydata as $key => $value) {
                $code = $value[0];
                $name = $value[1];

                if (empty($code) || empty($name)) {
                    continue;
                }

                $checkCode = $this->products_model->checkCodeExistCategoryStages($code);
                if (!empty($checkCode)) continue;

                $options = [
                    'code' => $code,
                    'name' => $name,
                ];

                $rs = $this->products_model->insertCategoryStages($options);
                if ($rs) {
                    $count++;
                }
            }
        }
        echo json_encode(
            [
                'success' => true,
                'alert_type' => 'success',
                'message' => 'Import thành công ' . $count . ' dòng',
            ]
        );
        die();
    }

    public function copy_stages($id)
    {
        if (!$this->perAddProducts) {
            accessDenied($js = true);
        }

        $products = $this->products_model->rowProduct($id);
        if ($products['type_products'] == 'semi_products_outside') {
            refererModel(lang('BTP mua ngoài không có thiết kế công đoạn'));
            die;
        }

        if ($this->input->post('save')) {
            $data = [];

            $stages = $this->input->post('stages');
            if (empty($stages)) {
                $data['result'] = 0;
                $data['message'] = lang('Vui lòng chọn thành phẩm sao chép công đoạn');
                echo json_encode($data);
                die;
            }

            $this->db->select('
                tbl_product_stages.*
            ', false);
            $this->db->from('tbl_product_stages');
            $this->db->where('tbl_product_stages.product_id', $stages);
            $product_stages = $this->db->get()->result_array();
            if (!empty($product_stages)) {
                foreach ($product_stages as $key => $stage) {

                    $this->db->from('tbl_product_stages');
                    $this->db->where('tbl_product_stages.product_id', $id);
                    $this->db->where('tbl_product_stages.versions', $stage['versions']);
                    $isVersions = $this->db->count_all_results();
                    $c_versions = $stage['versions'];
                    if (!empty($isVersions)) {
                        $c_versions = $stage['versions'] . time();
                    }

                    $options = [];
                    $options = [
                        'versions' => $c_versions,
                        'product_id' => $id,
                        'status' => 'unapplication',
                    ];

                    $this->db->select('tbl_product_stages_versions.*');
                    $this->db->from('tbl_product_stages_versions');
                    $this->db->where('tbl_product_stages_versions.version_id', $stage['id']);
                    $product_stages_versions = $this->db->get()->result_array();

                    if (!empty($product_stages_versions)) {
                        foreach ($product_stages_versions as $k => $val) {
                            $options['items'][$k]['stage'] = $val['stage_id'];
                            $options['items'][$k]['number'] = $val['number'];
                            $options['items'][$k]['number_hours'] = $val['number_hours'];
                            $options['items'][$k]['machines'] = $val['machines'];
                            $options['items'][$k]['final_stage'] = $val['final_stage'];
                            $options['items'][$k]['type'] = $val['type'];
                            $options['items'][$k]['face'] = $val['face'];
                            $options['items'][$k]['face_after'] = $val['face_after'];
                            $options['items'][$k]['number_face'] = $val['number_face'];
                            $options['items'][$k]['number_operations'] = $val['number_operations'];
                            $options['items'][$k]['number_cutting'] = $val['number_cutting'];
                            $options['items'][$k]['quota_time_f1'] = $val['quota_time_f1'];
                            $options['items'][$k]['quota_time_f2'] = $val['quota_time_f2'];
                        }
                    }
                    $q = $this->products_model->insertProductStages($options, 'unapplication', 0);
                    if ($key == 0) {
                        $this->products_model->updateProducts($id, ['versions_stage' => $c_versions]);
                    }
                }

                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('Không có công đoạn để sao chép');
            }
            echo json_encode($data);
            die;
        }

        $data['id'] = $id;
        $data['products'] = $products;
        $this->load->view('admin/products/copy_stages', $data);
    }

    public function searchDesignStages()
    {
        $term = $this->input->get('term');
        $limit = 50;
        $params = $this->input->get('params');

        $type_products = $params['type_products'];

        $data = [];
        $this->db->select('
            tbl_products.id as id,
            CONCAT(tbl_products.name, "(", tbl_products.code,")") as text 
        ', false);

        $this->db->from('tbl_products');
        if ($type_products == "semi_products") {
            $this->db->where('tbl_products.type_products', 'semi_products');
        }

        $this->db->where(' exists (
            SELECT tbl_product_stages.id
            FROM tbl_product_stages
            WHERE tbl_product_stages.product_id = tbl_products.id
        )', false, false);

        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_products.code', $term);
            $this->db->or_like('tbl_products.name', $term);
            $this->db->group_end();
        }

        $this->db->limit($limit);
        $data['results'] = $this->db->get()->result_array();
        echo json_encode($data);
    }

    public function import_stage()
    {
        // echo 'Chức năng này không còn khả dụng'; die;
        if (!$this->perAddProducts) {
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
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('J');
            $arraydata          = array();

            $actions = $this->input->post('actions');
            $fields = $this->input->post('fields');
            for ($row = 3; $row <= $highestRow; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value      = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 2][$col] = $value;
                }
            }

            $options = [];
            $count = 0;
            $errors = '';
            $cRow = 3;
            $index_parent = 0;
            $index_parent_element = 0;
            $pCode = '';
            $pVersions = '';
            $dataImport = [];
            foreach ($arraydata as $key => $value) {
                // 0: product_code
                // 1: product_name
                // 2: version_stages
                // 3: stages
                // 4: type
                // 5: final_stage
                // 6: machines
                // 7: number_face
                // 8: number_operations
                // 9: number_cutting

                $product_code = trim($value[0]);
                $product_name = trim($value[1]);
                $version_stages = trim($value[2]);
                $stages = trim($value[3]);
                $type = trim($value[4]);
                $final_stage = trim($value[5]);
                $machines = trim($value[6]);
                $number_face = number_unformat($value[7]);
                $number_operations = number_unformat($value[8]);
                $number_cutting = number_unformat($value[9]);

                //handling product
                if ((!empty($product_code) && $product_code != $pCode) || (!empty($version_stages) && $version_stages != $pVersions)) {
                    $dataImport[$index_parent]['product_code'] = $product_code;
                    $dataImport[$index_parent]['product_name'] = $product_name;
                    $dataImport[$index_parent]['version_stages'] = $version_stages;

                    $parent_current = $index_parent;
                    $pVersions = $version_stages;
                    $pCode = $product_code;
                    $index_parent++;

                    $pElement = '';
                    $index_parent_element = 0;
                }
                //end handling product

                //end handling element
                if (empty($stages)) continue;
                $dataImport[$parent_current]['items'][] = [
                    'stage' => $stages,
                    'type' => $type,
                    'final_stage' => $final_stage,
                    'machines' => $machines,
                    'number_face' => $number_face,
                    'number_operations' => $number_operations,
                    'number_cutting' => $number_cutting,
                ];
            }

            // print_arrays($dataImport);
            //handling import bom

            if (!empty($dataImport)) {
                // if ($actions == 'add') {
                foreach ($dataImport as $key => $value) {
                    $optionImport = [];
                    $product_code = $value['product_code'];
                    $product_name = $value['product_name'];
                    $product = $this->products_model->rowProductByCode($product_code);
                    if (!$product) {
                        $errors .= '<div class="text-danger">' . $product_code . '[' . $product_name . '] ' . lang('không tòn tại trong phần mềm') . '.</div>';
                        continue;
                    }

                    if ($product['type_products'] == "semi_products_outside") {
                        $errors .= '<div class="text-danger">' . $product_code . '[' . $product_name . '] là bán thành phẩm mua ngoài không thêm thể thêm giai đoạn</div>';
                        continue;
                    }

                    $product_id = $product['id'];
                    $versions = $value['version_stages'];
                    if ($this->products_model->checkProductStages($product_id, $versions)) {
                        $errors .= '<div class="text-danger">' . $product_code . '[' . $product_name . '] thêm không được vì phiên bản ' . $versions . ' đã tồn tại trong phần mềm.</div>';
                        continue;
                    }

                    if (empty($versions)) {
                        $errors .= '<div class="text-danger">' . $product_code . '[' . $product_name . '] thêm không được vì phiên bản ' . $versions . ' bỏ trống.</div>';
                        continue;
                    }

                    $items = $value['items'];
                    if (empty($items)) {
                        $errors .= '<div class="text-danger">' . $product_code . '[' . $product_name . '] không có giai đoạn để thêm phiên bản.</div>';
                        continue;
                    }

                    $options = [];
                    $options['versions'] = $versions;
                    $options['product_id'] = $product_id;

                    $flagErr = false;
                    $flagStagesMaterial = false;
                    $stt = 0;

                    $flagStagesAddMore = false;
                    foreach ($items as $k => $val) {
                        $stage = $val['stage'];
                        $dtStage = $this->products_model->rowStageByCode($stage);
                        if (!empty($dtStage) && $dtStage['id'] == STAGES_MATERIAL) {
                            $flagStagesAddMore = true;
                        }
                    }

                    if (!$flagStagesAddMore) {
                        array_unshift($items, [
                            'stage' => 'Chuẩn bị NPL',
                            'type' => 0,
                            'final_stage' => 0,
                            'machines' => null,
                            'number_face' => 0,
                            'number_operations' => 0,
                            'number_cutting' => 0,
                        ]);
                        $items = array_values($items);
                    }

                    foreach ($items as $k => $val) {
                        $stage = $val['stage'];
                        $type = !empty($val['type']) ? $val['type'] : 0;
                        $final_stage = $val['final_stage'];
                        $machines = $val['machines'];
                        $number_face = $val['number_face'];
                        $number_operations = $val['number_operations'];
                        $number_cutting = $val['number_cutting'];

                        $dtStage = $this->products_model->rowStageByCode($stage);
                        if (empty($dtStage)) {
                            $errors .= '<div class="text-danger">' . $product_code . '[' . $product_name . '] thêm không được vì giai đoạn ' . $stage . ' chưa tồn tại trong phần mềm.</div>';
                            $flagErr = true;
                            break;
                        }

                        if ($type != 0 && $type != 1 && $type != 2) {
                            $errors .= '<div class="text-danger">' . $product_code . '[' . $product_name . '] Đánh dấu công đoạn không đúng định dạng</div>';
                            $flagErr = true;
                            break;
                        }

                        $machine_id = 0;
                        if (!empty($machines)) {
                            $this->db->select('tbl_machines.id', false);
                            $this->db->from('tbl_machines');
                            $this->db->where('tbl_machines.code', $machines);
                            $dtMachine = $this->db->get()->row_array();
                            if (empty($dtMachine)) {
                                $errors .= '<div class="text-danger">' . $product_code . '[' . $product_name . '] không tìm thấy máy móc [' . $machines . ']</div>';
                                $flagErr = true;
                                break;
                            }

                            $machine_id = $dtMachine['id'];
                        }

                        if ($k == 1) {
                            $type = 2;
                        } else {
                            $type = 0;
                        }

                        if ($dtStage['id'] == STAGES_MATERIAL) {
                            $type = 6;
                            $flagStagesMaterial = true;
                        }

                        $options['items'][$k]['stage'] = $dtStage['id'];
                        $options['items'][$k]['number'] = ++$stt;
                        $options['items'][$k]['number_hours'] = 0;
                        $options['items'][$k]['machines'] = $machine_id;
                        $options['items'][$k]['final_stage'] = $final_stage;
                        $options['items'][$k]['type'] = $type;
                        $options['items'][$k]['number_face'] = $number_face;
                        $options['items'][$k]['number_operations'] = $number_operations;
                        $options['items'][$k]['number_cutting'] = $number_cutting;
                    }

                    if (!$flagStagesMaterial) {
                        $errors .= '<div class="text-danger">' . $product_code . '[' . $product_name . '] vui lòng có công đoạn chuẩn bị NPL</div>';
                        $flagErr = true;
                        break;
                        continue;
                    }

                    if ($flagErr) {
                        continue;
                    }

                    // print_arrays($options);
                    $status = "unapplication";
                    $q = $this->products_model->insertProductStages($options, $status, 0);
                    if ($q) {
                        $this->products_model->updateProducts($product_id, ['versions_stage' => $versions]);
                        $count++;
                    }
                }
                // } else if ($actions == 'update') {
                //     var_dump('ok');die;
                //     foreach ($dataImport as $key => $value) {
                //         $optionImport = [];
                //         $product_code = $value['product_code'];
                //         $product_name = $value['product_name'];
                //         $product = $this->products_model->rowProductByCode($product_code);
                //         if (!$product) {
                //             $errors.= '<div class="text-danger">'.$product_code.'['.$product_name.'] '.lang('không tòn tại trong phần mềm').'.</div>';
                //             continue;
                //         }

                //         if ($product['type_products'] == "semi_products_outside") {
                //             $errors.= '<div class="text-danger">'.$product_code.'['.$product_name.'] là bán thành phẩm mua ngoài không thêm thể thêm giai đoạn</div>';
                //             continue;
                //         }

                //         $product_id = $product['id'];
                //         $versions = $value['version_stages'];
                //         if (!$this->products_model->checkProductStages($product_id, $versions))
                //         {
                //             $errors.= '<div class="text-danger">'.$product_code.'['.$product_name.'] thêm không được vì phiên bản '.$versions.' không tồn tại trong phần mềm.</div>';
                //             continue;
                //         }
                //         $this->db->select('tbl_product_stages.id as id');
                //         $this->db->from('tbl_product_stages');
                //         $this->db->where('tbl_product_stages.product_id', $product_id);
                //         $this->db->where('tbl_product_stages.versions', $versions);
                //         $version_id = $this->db->get()->row_array()['id'];

                //         $items = $value['items'];
                //         if (empty($items)) {
                //             $errors.= '<div class="text-danger">'.$product_code.'['.$product_name.'] không có giai đoạn để thêm phiên bản.</div>';
                //             continue;
                //         }

                //         $options = [];
                //         $options['versions'] = $versions;
                //         $options['product_id'] = $product_id;

                //         $flagErr = false;
                //         $flagStagesMaterial = false;
                //         $stt = 0;

                //         $flagStagesAddMore = false;
                //         foreach ($items as $k => $val) {
                //             $stage = $val['stage'];
                //             $dtStage = $this->products_model->rowStageByCode($stage);
                //             if (!empty($dtStage) && $dtStage['id'] == STAGES_MATERIAL) {
                //                 $flagStagesAddMore = true;
                //             } 
                //         }

                //         if (!$flagStagesAddMore) {
                //             array_unshift($items , [
                //                 'stage' => 'Chuẩn bị NPL',
                //                 'type' => 0,
                //                 'final_stage' => 0,
                //                 'machines' => null,
                //             ]);
                //             $items = array_values($items);
                //         }

                //         foreach ($items as $k => $val) {
                //             $stage = $val['stage'];
                //             $type = !empty($val['type']) ? $val['type'] : 0;
                //             $final_stage = $val['final_stage'];
                //             $machines = $val['machines'];

                //             $dtStage = $this->products_model->rowStageByCode($stage);
                //             if (empty($dtStage)) {
                //                 $errors.= '<div class="text-danger">'.$product_code.'['.$product_name.'] thêm không được vì giai đoạn '.$stage.' chưa tồn tại trong phần mềm.</div>';
                //                 $flagErr = true;
                //                 break;
                //             }

                //             if ($type != 0 && $type != 1 && $type != 2) {
                //                 $errors.= '<div class="text-danger">'.$product_code.'['.$product_name.'] Đánh dấu công đoạn không đúng định dạng</div>';
                //                 $flagErr = true;
                //                 break;
                //             }

                //             $machine_id = 0;
                //             if (!empty($machines)) {
                //                 $this->db->select('tbl_machines.id', false);
                //                 $this->db->from('tbl_machines');
                //                 $this->db->where('tbl_machines.code', $machines);
                //                 $dtMachine = $this->db->get()->row_array();
                //                 if (empty($dtMachine)) {
                //                     $errors.= '<div class="text-danger">'.$product_code.'['.$product_name.'] không tìm thấy máy móc ['.$machines.']</div>';
                //                     $flagErr = true;
                //                     break;
                //                 }

                //                 $machine_id = $dtMachine['id'];
                //             }

                //             if ($k == 1) {
                //                 $type = 2;
                //             } else {
                //                 $type = 0;
                //             }

                //             if ($dtStage['id'] == STAGES_MATERIAL) {
                //                 $type = 6;
                //                 $flagStagesMaterial = true;
                //             } 

                //             $options['items'][$k]['stage'] = $dtStage['id'];
                //             $options['items'][$k]['number'] = ++$stt;
                //             $options['items'][$k]['number_hours'] = 0;
                //             $options['items'][$k]['machines'] = $machine_id;
                //             $options['items'][$k]['final_stage'] = $final_stage;
                //             $options['items'][$k]['type'] = $type;
                //         }

                //         if (!$flagStagesMaterial) {
                //             $errors.= '<div class="text-danger">'.$product_code.'['.$product_name.'] vui lòng có công đoạn chuẩn bị NPL</div>';
                //             $flagErr = true;
                //             break;
                //             continue;
                //         }

                //         if ($flagErr) {
                //             continue;
                //         }

                //         // print_arrays($options);
                //         $status = "unapplication";
                //         $q = $this->products_model->insertProductStages($options, $status, $version_id);
                //         if ($q) {
                //             $this->products_model->updateProducts($product_id, ['versions_stage' => $versions]);
                //             $count++;
                //         }
                //     }
                // }
            }
            //end handling import bom

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
            $data['title'] = _l('tnh_import_stages');
            $this->load->view('admin/products/import_stages_new', $data);
        }
    }

    public function searchBOMSample()
    {
        $term = $this->input->get('term');
        $limit = 50;
        $params = $this->input->get('params');

        $data = [];
        $this->db->select('
        tbl_boms.id as id,
            tbl_boms.versions as text 
        ', false);
        $this->db->from('tbl_boms');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_boms.versions', $term);
            $this->db->or_like('tbl_boms.versions', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $data['results'] = $this->db->get()->result_array();
        echo json_encode($data);
    }

    public function getDataBomSample()
    {
        $data = [];

        $bom_sample = $this->input->post('bom_sample');
        $products['type_products'] = $this->input->post('type_products');

        $this->db->select('
            tbl_boms_element.*
        ', false);
        $this->db->from('tbl_boms_element');
        $this->db->where('tbl_boms_element.bom_id', $bom_sample);
        $elements = $this->db->get()->result_array();

        $html_BOM = '';
        $count_i = 0;
        $count_k = 0;
        $kR = 0;
        $list_stages = recursive_stages();
        $list_stages_primary = recursive_stages($list_stages_primary, 0, null, 1);

        foreach ($elements as $key => $value) {
            if ($value['type_element'] == 1) {
                $isFlagPrimary = true;
            } else if ($value['type_element'] == 2) {
                $isFlagExtra = true;
            }

            $html_BOM .= '<tr>';
            $html_BOM .= '<input type="hidden" name="i[]" id="i" class="form-control i" value="' . $count_i . '">';
            $html_BOM .= '<td>
                            <div class="text-center">
                                <button type="button" class="btn btn-primary btn-icon btn-add-items">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </td>';

            $html_BOM .= '<td colspan="2">
                            <input type="text" name="element_name_' . $count_i . '" id="element_name_' . $count_i . '" class="form-control" value="' . $value['element_name'] . '" placeholder="' . lang('tnh_element_name') . '" required="required">
                            <input type="hidden" name="type_element_' . $count_i . '" class="form-control type_element" value="' . $value['type_element'] . '">
                            <div class="txt-type-element text-danger mtop5">' . ($value['type_element'] == 1 ? 'NPL chính' : 'NPL phụ') . '</div>
                        </td>';
            $html_BOM .= '<td></td>';
            $html_BOM .= '<td>
                            <input type="number" name="element_number_' . $count_i . '" class="form-control hide" value="' . $value['quantity'] . '" min="0">
                        </td>';
            $html_BOM .= '<td></td>';
            $html_BOM .= '<td></td>';
            $html_BOM .= '<td></td>';
            $html_BOM .= '<td></td>';
            $html_BOM .= '<td></td>';
            $html_BOM .= '<td>
                        </td>';
            $html_BOM .= '</tr>';

            // $items = $this->products_model->getElementItemsByElementId($value['id']);

            $this->db->select('tbl_boms_element_items.*');
            $this->db->from('tbl_boms_element_items');
            $this->db->where('tbl_boms_element_items.bom_element_id', $value['id']);
            if ($products['type_products'] != "products") {
                $this->db->where('tbl_boms_element_items.type !=', 'semi_products');
            }
            $items = $this->db->get()->result_array();

            foreach ($items as $k => $val) {
                $option = '<option value=""></option>';
                $type_design = type_design_bom($products['type_products'] == 'products' ? 'all' : 'not_all');
                foreach ($type_design as $e => $v) {
                    $option .= '<option ' . ($e == $val['type'] ? 'selected' : '') . ' value="' . $e . '">' . $v . '</option>';
                }

                $arr_unit_id = [];
                $displayProduct = 'none';
                $displayMaterial = 'none';
                if ($val['type'] == "semi_products" || $val['type'] == "semi_products_outside") {
                    $info = $this->products_model->rowProduct($val['item_id']);
                    array_push($arr_unit_id, $info['unit_id']);
                    $displayProduct = 'block';
                } else {
                    $info = $this->items_model->rowMaterial($val['item_id']);
                    $exchange = $this->items_model->getExchangeItemsByItemId($val['item_id']);
                    array_push($arr_unit_id, $info['unit_id']);
                    if (!empty($exchange)) {
                        foreach ($exchange as $ke => $va) {
                            array_push($arr_unit_id, $va['unit_id']);
                        }
                    }
                    $displayMaterial = 'block';
                }
                array_push($arr_unit_id, $val['unit_id']);
                $option_units = '';
                if (!empty($arr_unit_id)) {
                    $units = $this->products_model->getUnitsByArrId($arr_unit_id);
                    foreach ($units as $a => $el) {
                        $selected_unit = ($el['unitid'] == $val['unit_id']) ? 'selected' : '';
                        $option_units .= '<option ' . $selected_unit . ' value="' . $el['unitid'] . '">' . $el['unit'] . '</option>';
                    }
                }

                $addMaterialReplace = 'display: none;';

                $html_BOM .= '<tr class="tr-child-item tnh-item-' . $count_i . '">';
                $html_BOM .= '<td></td>';
                $html_BOM .= '<input type="hidden" name="iii" id="iii" class="form-control iii" value="' . $count_i . '">';
                $html_BOM .= '<input type="hidden" name="k[]" id="k" class="form-control k" value="' . $count_k . '">';
                $html_BOM .= '<td colspan="1" style="width: 150px;">
                    <select name="type_design_bom_' . $count_i . '[' . $count_k . ']" data-none-selected-text="' . lang('type') . '" id="type_design_bom_' . $count_k . '" class="form-control type_design_bom" required="required">
                        ' . $option . '
                    </select>

                    <div class="td-category-products mtop5" style="display: ' . $displayProduct . ';">
                        <select data-none-selected-text="Danh mục" data-live-search="true" id="category_product_search_bom' . $count_k . '" class="form-control category_product_search_bom">
                            <option value=""></option>
                            ' . recursiveCategoryProducts() . '
                        </select>
                    </div>
                    <div class="td-category-materials  mtop5" style="display: ' . $displayMaterial . ';">
                        <select data-none-selected-text="Danh mục" data-live-search="true" id="category_material_search_bom' . $count_k . '" class="form-control category_material_search_bom">
                            <option value=""></option>
                            ' . recursiveCategoryItems() . '
                        </select>
                    </div>
                    <div class="checkbox checkbox-info" style="margin-top: 5px;">
                        <input type="checkbox" ' . ($val['face'] == 1 ? 'checked' : '') . ' name="face_' . $count_i . '[' . $count_k . ']" id="face_' . $count_i . '[' . $count_k . ']" value="1">
                        <label for="face_' . $count_i . '[' . $count_k . ']">Mặt trước</label>
                    </div>
                    <div class="checkbox checkbox-info">
                        <input type="checkbox" ' . ($val['face_after'] == 2 ? 'checked' : '') . ' name="face_after_' . $count_i . '[' . $count_k . ']" id="face_after_' . $count_i . '[' . $count_k . ']" value="2">
                        <label for="face_after_' . $count_i . '[' . $count_k . ']">Mặt sau</label>
                    </div>
                <span class="fa fa-plus text-primary mtop10 add-replace"  onclick="getMaterialReplace(this)" style="cursor: pointer;' . $addMaterialReplace . '"> ' . lang('tnh_add_material_replace') . '</span>
                </td>';

                $html_BOM .= '<td colspan="1" style="width: 200px;">
                    <input type="text" name="items_' . $count_i . '[' . $count_k . ']" id="items_' . $count_k . '" data-placeholder="' . lang('choose') . '" class="modal-select2 it" style="width: 100%;" value="' . $val['item_id'] . '" required="required">
                </td>';
                $html_BOM .= '<td colspan="1" class="class="td-unit"">
                    <select data-placeholder="' . lang('choose') . '" id="units_' . $count_k . '" name="units_' . $count_i . '[' . $count_k . ']" class="modal-select2 units" style="width: 100%;" required>
                        ' . $option_units . '
                    </select>
                </td>';

                $html_BOM .= '<td colspan="">
                    <input type="text" name="landscape_print_size_' . $count_i . '[' . $count_k . ']" class="form-control landscape_print_size" value="' . $val['landscape_print_size'] . '">
                </td>';

                $html_BOM .= '<td colspan="">
                    <input type="text" name="number_children_size_' . $count_i . '[' . $count_k . ']" onchange="calPaperExchange(this)" class="form-control number-format number_children_size" value="' . $val['number_children_size'] . '">
                </td>';

                $html_BOM .= '<td colspan="">
                    <input type="text" name="element_item_number_' . $count_i . '[' . $count_k . ']" class="form-control number-format" value="' . $val['quantity'] . '">
                </td>';

                $html_BOM .=  '<td colspan="">
                    <input type="text" name="paper_exchange_' . $count_i . '[' . $count_k . ']" class="form-control number-format paper_exchange" ' . (!empty($val['hand_input_paper_exchange']) ? '' : 'readonly') . ' value="' . formatNumber($val['paper_exchange']) . '">
                    <div class="checkbox checkbox-info" style="margin-top: 5px !important;">
                        <input type="checkbox" ' . (!empty($val['hand_input_paper_exchange']) ? 'checked' : '') . ' name="hand_input_paper_exchange_' . $count_i . '[' . $count_k . ']" onchange="handInputPaperExchange(this)" id="hand_input_paper_exchange_' . $count_i . '[' . $count_k . ']" class="hand_input_paper_exchange" value="' . $val['vertical_print_size'] . '">
                        <label for="hand_input_paper_exchange_' . $count_i . '[' . $count_k . ']">Nhập tay</label>
                    </div>
                </td>';

                $html_BOM .= '<td colspan="">
                    <input type="text" name="quantity_compensation_' . $count_i . '[' . $count_k . ']" class="form-control number-format" value="' . $val['quantity_compensation'] . '">
                </td>';

                $htmlStage = '';
                $dtStageCriterial = $this->products_model->getStageCriterial($val['stage_id']);
                if (!empty($dtStageCriterial)) {
                    foreach ($dtStageCriterial as $kS => $vS) {
                        $htmlStage .= '<div>Rút kiểm: ' . $vS['withdraw_check'] . ' - Tiêu chuẩn kiểm: ' . $vS['test_standards'] . '</div>';
                    }
                }

                $html_BOM .= '<td>
                    <input type="hidden" name="" id="stage_edit_' . $count_k . '" class="form-control stage_edit" value="' . $val['stage_id'] . '">
                    <select name="stage_' . $count_i . '[' . $count_k . ']"  data-live-search="true" onChange="changeStage(this)" data-none-selected-text="" id="stage_' . $count_k . '" class="form-control stage_item ' . ($val['type_element_item'] == 1 ? 'stage_items_primary' : '') . '">
                        <option value=""></option>
                        ' . $list_stages . '
                    </select>
                    <div class="txt-info-stage">' . $htmlStage . '</div>
                </td>';

                // $machine = $this->products_model->getMachinesById($val['machines_id']);
                // $infoMachine = '';
                // $optionMachine = '';
                // if (!empty($machine)) {
                //     $process = $this->category_model->getMachinesProcess($val['machines_id']);
                //     $divProcess = '';
                //     if (!empty($process)) {
                //         foreach ($process as $k => $v) {
                //             $divProcess.= $v['process'].', ';
                //         }
                //     }

                //     $infoMachine.= '<div class="mtop5">
                //         <div><b>'.lang('tnh_standard').'</b>: '.$machine['standard'].'</div>
                //         <div><b>'.lang('tnh_pp_measure').'</b>: '.$machine['pp_measure'].'</div>
                //         <div><b>'.lang('tnh_quota_productivity').'</b>: '.$machine['quota_productivity'].'</div>
                //         <div><b>'.lang('tnh_operating_gauge').'</b>: '.$machine['operating_gauge'].'</div>
                //         <div><b>'.lang('tnh_process').'</b>: '.$divProcess.'</div>
                //     </div>';
                //     $optionMachine = '<option selected value="'.$machine['id'].'">'.$machine['name'].'</option>';
                // }

                $html_BOM .= '<td colspan="">
                <div class="text-center"><i class="btn btn-danger fa fa-remove remove-element-item"></i></div>
                </td>';
                $html_BOM .= '</tr>';

                $count_k++;
            }
            $count_i++;
        }

        $data['html_BOM'] = $html_BOM;
        $data['count_k'] = $count_k;
        $data['count_i'] = $count_i;

        echo json_encode($data);
    }

    public function edit_more($id)
    {
        $data = [];
        $product = $this->products_model->rowProduct($id);
        if (empty($product)) {
            refererModel(lang('not_data'));
            die;
        }

        if ($this->input->post('save')) {
            $color_formula = $this->input->post('color_formula', false);
            $ball_formula = $this->input->post('ball_formula', false);

            $up = $this->products_model->updateProducts($id, [
                'color_formula' => $color_formula,
                'ball_formula' => $ball_formula,
            ]);

            $data['result'] = 1;
            $data['message'] = lang('success');
            echo json_encode($data);
            die;
        }

        $data['product'] = $product;
        $this->load->view('admin/products/edit_more', $data);
    }

    public function export_product_stage()
    {
        $arrProduct_stage = [];
        $arrProduct_stage_version = [];
        // if (!empty($this->input->post('product_id'))) {

        $arrProduct_id = $this->input->post('product_id');

        if (empty($arrProduct_id)) {
            $arrProduct = get_table_where('tbl_products', '', '', 'result_array', '', 'id');
            foreach ($arrProduct as $key => $value) {
                $arrProduct_id[] = $value['id'];
            }
        }

        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
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

        $cloumns[] = ['name' => 'Mã thành phẩm'];
        $cloumns[] = ['name' => 'Tên thành phẩm'];
        $cloumns[] = ['name' => 'Phiên bản'];
        $cloumns[] = ['name' => 'Mã giai đoạn'];
        $cloumns[] = ['name' => 'Đánh dấu công đoạn'];
        $cloumns[] = ['name' => 'Giai đoạn cuối'];
        $cloumns[] = ['name' => 'Máy móc'];
        foreach ($cloumns as $key => $value) {
            $objPHPExcel->getActiveSheet()->SetCellValue($cloumns_excel[$key] . '1', $value['name'])->getStyle($cloumns_excel[$key] . '1')->applyFromArray($style_excel['Background_header']);
            $objPHPExcel->getActiveSheet()->SetCellValue($cloumns_excel[$key] . '2', '')->getStyle($cloumns_excel[$key] . '2')->applyFromArray($style_excel['Background_header']);
            $objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$key])->setAutoSize(true);
        }

        $row = 2;
        foreach ($arrProduct_id as $key => $value) {
            $product = $this->products_model->rowProduct($value);

            $product_stage = $this->products_model->getProductStagesByProductId($value);
            $arrProduct_stage[] = $product_stage;

            foreach ($product_stage as $k => $val) {
                $product_stage_version = $this->products_model->getProductStagesVersions($val['id']);

                foreach ($product_stage_version as $k2 => $val2) {
                    $row++;
                    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $row, $product['code'])->getStyle('A' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $row, $product['name'])->getStyle('B' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $row, $val['versions'])->getStyle('C' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $row, $val2['stage_code'])->getStyle('D' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('E' . $row, ($val2['type'] == 2 ? 2 : ''))->getStyle('E' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $row, ($val2['final_stage'] == 1 ? 1 : ''))->getStyle('F' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('G' . $row, ($val2['machine_code']))->getStyle('G' . $row)->applyFromArray($style_excel['BStyle']);
                }
            }
        }
        // var_dump($product_stage_version);die;

        $filename = 'Cong_doan_san_pham.xls';
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
        // } else {
        //     $response =  array(
        //         'result' => 0,
        //         'message' => lang('Vui lòng chọn Thành phẩm'),
        //     );
        //     die(json_encode($response));
        // }
    }

    public function export_product_stage_bom()
    {
        $arrProduct_stage = [];
        $arrProduct_stage_version = [];
        // if (!empty($this->input->post('product_id'))) {

        $arrProduct_id = $this->input->post('product_id');

        if (empty($arrProduct_id)) {
            $arrProduct = get_table_where('tbl_products', '', '', 'result_array', '', 'id');
            foreach ($arrProduct as $key => $value) {
                $arrProduct_id[] = $value['id'];
            }
        }

        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
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

        $cloumns[] = ['name' => 'Mã thành phẩm'];
        $cloumns[] = ['name' => 'Tên thành phẩm'];
        $cloumns[] = ['name' => 'Phiên bản'];
        $cloumns[] = ['name' => 'Mã giai đoạn'];
        $cloumns[] = ['name' => 'Đánh dấu công đoạn'];
        $cloumns[] = ['name' => 'Giai đoạn cuối'];
        $cloumns[] = ['name' => 'Máy móc'];
        $cloumns[] = ['name' => 'Phiên bản BOM'];
        $cloumns[] = ['name' => 'Thành phần'];
        $cloumns[] = ['name' => 'Mã nguyên phụ liệu'];
        $cloumns[] = ['name' => 'Tên nguyên phụ liệu'];
        $cloumns[] = ['name' => 'Loại'];
        $cloumns[] = ['name' => 'Đơn vị'];
        $cloumns[] = ['name' => 'Khổ in ngang - dọc (tờ) - cm'];
        $cloumns[] = ['name' => 'SL con/ Khổ in'];
        $cloumns[] = ['name' => 'Giá trị quy đổi (tính trên tờ in)'];
        $cloumns[] = ['name' => 'Số tờ quy đổi'];
        $cloumns[] = ['name' => 'Số lượng bù hao (khổ liệu)'];
        $cloumns[] = ['name' => 'Công đoạn'];
        foreach ($cloumns as $key => $value) {
            $objPHPExcel->getActiveSheet()->SetCellValue($cloumns_excel[$key] . '2', $value['name'])->getStyle($cloumns_excel[$key] . '2')->applyFromArray($style_excel['Background_header']);
            // $objPHPExcel->getActiveSheet()->SetCellValue($cloumns_excel[$key] . '2', '')->getStyle($cloumns_excel[$key] . '2')->applyFromArray($style_excel['Background_header']);
            $objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$key])->setAutoSize(true);
        }

        $objPHPExcel->getActiveSheet()->mergeCells('A1:B1');
        $objPHPExcel->getActiveSheet()->mergeCells('C1:G1');
        $objPHPExcel->getActiveSheet()->mergeCells('H1:S1');
        $syleHeaderCustom = $style_excel['Background_header'];
        // var_dump($syleHeaderCustom);die;
        $syleHeaderCustom['fill']['color']['rgb'] = '5B9BD5';
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'THÀNH PHẨM')->getStyle('A1')->applyFromArray($syleHeaderCustom);
        $syleHeaderCustom['fill']['color']['rgb'] = 'B4C6E7';
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'CÔNG ĐOẠN')->getStyle('C1')->applyFromArray($syleHeaderCustom);
        $syleHeaderCustom['fill']['color']['rgb'] = 'FFFF00';
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'ĐỊNH MỨC BOM')->getStyle('H1')->applyFromArray($syleHeaderCustom);


        $row = 2;
        foreach ($arrProduct_id as $key => $value) {
            $product = $this->products_model->rowProduct($value);

            $product_stage = $this->products_model->getProductStagesByProductId($value);
            $arrProduct_stage[] = $product_stage;

            $product_norms = $this->getNormsByProductId($value);
            // $count1 = count($product_stage_version);
            $normCount = count($product_norms);
            // if ($count1 >= $count2) {
            //     $maxRowProduct = $count1;
            // } else {
            //     $maxRowProduct = $count2;
            // }

            foreach ($product_stage as $k => $val) {
                $product_stage_version = $this->products_model->getProductStagesVersions($val['id']);
                $maxRowProduct = count($product_stage_version);

                // foreach ($product_stage_version as $k2 => $val2) {
                for ($i = 0; $i < $maxRowProduct; $i++) {
                    $val2 = $product_stage_version[$i] ?? null;
                    $val3 = $product_norms[$i] ?? null;
                    $row++;
                    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $row, $product['code'])->getStyle('A' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $row, $product['name'])->getStyle('B' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $row, $val['versions'])->getStyle('C' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $row, ($val2['stage_code'] ?? ''))->getStyle('D' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('E' . $row, (!empty($val2['type']) ? ($val2['type'] == 2 ? 2 : '') : ''))->getStyle('E' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $row, (!empty($val2['final_stage']) ? ($val2['final_stage'] == 1 ? 1 : '') : ''))->getStyle('F' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('G' . $row, ($val2['machine_code'] ?? ''))->getStyle('G' . $row)->applyFromArray($style_excel['BStyle']);

                    $objPHPExcel->getActiveSheet()->SetCellValue('H' . $row, ($val3['versions'] ?? ''))->getStyle('H' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('I' . $row, ($val3['element_name'] ?? ''))->getStyle('I' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('J' . $row, (!empty($val3['code_name']) ? (explode('|||', $val3['code_name'])[0] ?? '') : ''))->getStyle('J' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('K' . $row, (!empty($val3['code_name']) ? (explode('|||', $val3['code_name'])[1] ?? '') : ''))->getStyle('K' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('L' . $row, (!empty($val3['type']) ? _l($val3['type']) : ''))->getStyle('L' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('M' . $row, ($val3['unit_name'] ?? ''))->getStyle('M' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('N' . $row, ($val3['landscape_print_size'] ?? ''))->getStyle('N' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('O' . $row, ($val3['number_children_size'] ?? ''))->getStyle('O' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('P' . $row, ($val3['quantity'] ?? ''))->getStyle('P' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('Q' . $row, ($val3['paper_exchange'] ?? ''))->getStyle('Q' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('R' . $row, ($val3['quantity_compensation'] ?? ''))->getStyle('R' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('S' . $row, ($val3['stage_name'] ?? ''))->getStyle('S' . $row)->applyFromArray($style_excel['BStyle']);

                    $normCount--;
                }
            }

            if ($normCount >= 1) {
                for ($i = 0; $i < $normCount; $i++) {
                    $row++;
                    $val2 = null;
                    $val3 = $product_norms[$i] ?? null;

                    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $row, $product['code'])->getStyle('A' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $row, $product['name'])->getStyle('B' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $row, $val['versions'])->getStyle('C' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $row, ($val2['stage_code'] ?? ''))->getStyle('D' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('E' . $row, (!empty($val2['type']) ? ($val2['type'] == 2 ? 2 : '') : ''))->getStyle('E' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $row, (!empty($val2['final_stage']) ? ($val2['final_stage'] == 1 ? 1 : '') : ''))->getStyle('F' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('G' . $row, ($val2['machine_code'] ?? ''))->getStyle('G' . $row)->applyFromArray($style_excel['BStyle']);

                    $objPHPExcel->getActiveSheet()->SetCellValue('H' . $row, ($val3['versions'] ?? ''))->getStyle('H' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('I' . $row, ($val3['element_name'] ?? ''))->getStyle('I' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('J' . $row, (!empty($val3['code_name']) ? (explode('|||', $val3['code_name'])[0] ?? '') : ''))->getStyle('J' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('K' . $row, (!empty($val3['code_name']) ? (explode('|||', $val3['code_name'])[1] ?? '') : ''))->getStyle('K' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('L' . $row, (!empty($val3['type']) ? _l($val3['type']) : ''))->getStyle('L' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('M' . $row, ($val3['unit_name'] ?? ''))->getStyle('M' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('N' . $row, ($val3['landscape_print_size'] ?? ''))->getStyle('N' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('O' . $row, ($val3['number_children_size'] ?? ''))->getStyle('O' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('P' . $row, ($val3['quantity'] ?? ''))->getStyle('P' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('Q' . $row, ($val3['paper_exchange'] ?? ''))->getStyle('Q' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('R' . $row, ($val3['quantity_compensation'] ?? ''))->getStyle('R' . $row)->applyFromArray($style_excel['BStyle']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('S' . $row, ($val3['stage_name'] ?? ''))->getStyle('S' . $row)->applyFromArray($style_excel['BStyle']);
                }
            }
        }
        // var_dump($product_stage_version);die;

        $filename = 'Cong_doan_bom_san_pham.xls';
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
        // } else {
        //     $response =  array(
        //         'result' => 0,
        //         'message' => lang('Vui lòng chọn Thành phẩm'),
        //     );
        //     die(json_encode($response));
        // }
    }

    public function getNormsByProductId($id)
    {
        $product = "(
            SELECT CONCAT(tbl_products.code, '|||', tbl_products.name)
            FROM tbl_products
            WHERE tbl_element_items.item_id = tbl_products.id
        )";
        $material = "(
            SELECT CONCAT(tbl_materials.code, '|||', tbl_materials.name)
            FROM tbl_materials
            WHERE tbl_element_items.item_id = tbl_materials.id
        )";

        $this->db->select('
            tbl_product_versions.versions,
            tbl_versions_element.element_name,
            tbl_element_items.type, 
            tbl_element_items.item_id, 
            tbl_element_items.quantity, 
            tblunits.unit as unit_name, 
            IF(tbl_element_items.type = "materials", ' . $material . ', ' . $product . ') as code_name,
            tbl_element_items.landscape_print_size as landscape_print_size,
            tbl_element_items.number_children_size as number_children_size,
            tbl_element_items.quantity as quantity,
            tbl_element_items.paper_exchange as paper_exchange,
            tbl_element_items.quantity_compensation as quantity_compensation,
            tbl_stages.name as stage_name
        ');
        $this->db->from('tbl_product_versions');
        $this->db->join('tbl_versions_element', 'tbl_versions_element.version_id = tbl_product_versions.id', 'inner');
        $this->db->join('tbl_element_items', 'tbl_element_items.element_id = tbl_versions_element.id', 'inner');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_element_items.unit_id', 'left');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_element_items.stage_id', 'left');
        $this->db->where('tbl_product_versions.product_id', $id);
        $result = $this->db->get()->result_array();
        // echo json_encode($result);
        // echo '<pre>'; var_dump($result);die;
        return $result;
    }

    public function print_qr_old()
    {
        $arrProduct_id = $this->input->post('product_id');
        ob_start();
        // $data = new stdClass();
        $data['title'] = lang('QR');

        $data['content'] = 'test qr code';
        $data['data'] = [
            [
                'code' => 'test',
                'name' => 'test nè'
            ],
            [
                'code' => 'test2',
                'name' => 'test nè'
            ],
            [
                'code' => 'test3',
                'name' => 'test nè'
            ],
            [
                'code' => 'test4',
                'name' => 'test nè'
            ]
        ];
        $pdf = print_pdf_item_qrcode($data);
        $type = 'I';
        $pdf->Output(slug_it('') . '.pdf', $type);
    }

    public function print_qr()
    {
        ob_start();
        $data = [];
        $product_id = $this->input->get('ids');
        $arrId = explode(',', $product_id);
        $title = lang('IN QR THÀNH PHẨM');
        $items = null;
        if (!empty($arrId)) {
            $this->db->select('*');
            $this->db->from('tbl_products');
            $this->db->where_in('tbl_products.id', $arrId);
            $items = $this->db->get()->result_array();
        }

        $data['items'] = $items;

        $content = ob_get_contents();

        $data['object'] = "products";
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

    public function print_qr_stage()
    {
        ob_start();
        $data = [];
        $product_id = $this->input->get('ids');
        $arrId = explode(',', $product_id);
        $title = lang('IN QR CÔNG ĐOẠN');
        $items = null;
        if (!empty($arrId)) {
            $this->db->select('tbl_stages.id as id, tbl_stages.code as code, tbl_stages.name as name');
            $this->db->from('tbl_stages');
            $this->db->where_in('tbl_stages.id', $arrId);
            $items = $this->db->get()->result_array();
        }

        $data['items'] = $items;

        $content = ob_get_contents();

        $data['object'] = "stages";
        $data['hide'] = 'hide';
        $data['title'] = $title;
        $data['content'] = $content;
        ob_end_clean();
        $pdf = print_pdf_qr_dt($data);
        $type = 'I';
        if ($type == "S") {
            return $pdf->Output(slug_it('qr') . '.pdf', $type);
        } else {
            $pdf->Output(slug_it('qr') . '.pdf', $type);
        }
    }

    public function update_stage()
    {
        // echo 'Chức năng này không còn khả dụng'; die;
        if (!$this->perAddProducts) {
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
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('H');
            $arraydata          = array();

            $actions = $this->input->post('actions');
            $fields = $this->input->post('fields');
            for ($row = 3; $row <= $highestRow; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value      = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 2][$col] = $value;
                }
            }

            $not_machine = $this->input->post('not_machine');
            $options = [];
            $count = 0;
            $errors = '';
            $cRow = 2;
            $index_parent = 0;
            $index_parent_element = 0;
            $pCode = '';
            $pVersions = '';
            $dataImport = [];
            // print_arrays($arraydata);
            foreach ($arraydata as $key => $value) {
                // 0: product_code
                // 1: product_name
                // 2: version_stages
                // 3: stages
                // 4: machines
                // 5: number_face
                // 6: number_operations
                // 7: number_cutting

                $product_code = trim($value[0]);
                $product_name = trim($value[1]);
                $version_stages = trim($value[2]);
                $stages = trim($value[3]);
                $machines = trim($value[4]);
                $number_face = number_unformat($value[5]);
                $number_operations = number_unformat($value[6]);
                $number_cutting = number_unformat($value[7]);

                if (empty($stages)) continue;
                $dataImport[] = [
                    'product_code' => $product_code,
                    'product_name' => $product_name,
                    'version_stages' => $version_stages,
                    'stages' => $stages,
                    'machines' => $machines,
                    'number_face' => $number_face,
                    'number_operations' => $number_operations,
                    'number_cutting' => $number_cutting,
                ];
            }

            if (!empty($dataImport)) {
                $arrUpdate = [];
                foreach ($dataImport as $key => $value) {
                    $cRow++;
                    $product_code = $value['product_code'];
                    $product_name = $value['product_name'];
                    $version_stages = $value['version_stages'];
                    $stages = $value['stages'];
                    $machines = $value['machines'];
                    $number_face = !empty($value['number_face']) ? $value['number_face'] : 0;
                    $number_operations = !empty($value['number_operations']) ? $value['number_operations'] : 0;
                    $number_cutting = !empty($value['number_cutting']) ? $value['number_cutting'] : 0;

                    $product = $this->products_model->rowProductByCode($product_code);
                    if (!$product) {
                        $errors .= '<div class="text-danger">Dòng [' . $cRow . '] không cập nhật được do có thành phẩm ' . $product_code . '[' . $product_name . '] ' . lang('không tồn tại trong phần mềm') . '.</div>';
                        continue;
                    }

                    $product_id = $product['id'];
                    $this->db->select('
                        tbl_product_stages.id as id,
                    ', false);
                    $this->db->from('tbl_product_stages');
                    $this->db->where('tbl_product_stages.product_id', $product_id);
                    $this->db->where('tbl_product_stages.versions', $version_stages);
                    $product_stages = $this->db->get()->row_array();

                    if (empty($product_stages)) {
                        $errors .= '<div class="text-danger">Dòng [' . $cRow . '] không cập nhật được do phiên bản [' . $version_stages . '] không có trong phần mềm</div>';
                        continue;
                    }

                    $version_id = $product_stages['id'];
                    $dtStage = $this->products_model->rowStageByCode($stages);
                    if (empty($dtStage)) {
                        $errors .= '<div class="text-danger">Dòng [' . $cRow . '] không cập nhật được do giai đoạn [' . $stages . '] chưa tồn tại trong phần mềm.</div>';
                        continue;
                    }
                    $stage_id = $dtStage['id'];

                    if (empty($not_machine)) {
                        $machine_id = 0;
                        if (!empty($machines)) {
                            $this->db->select('tbl_machines.id', false);
                            $this->db->from('tbl_machines');
                            $this->db->where('tbl_machines.code', $machines);
                            $dtMachine = $this->db->get()->row_array();
                            if (empty($dtMachine)) {
                                $errors .= '<div class="text-danger">Dòng [' . $cRow . '] không cập nhật được do không tìm thấy máy móc [' . $machines . ']</div>';
                                continue;
                            }
                            $machine_id = $dtMachine['id'];
                        }
                    }

                    $this->db->select('tbl_product_stages_versions.id as id');
                    $this->db->from('tbl_product_stages_versions');
                    $this->db->where('tbl_product_stages_versions.version_id', $version_id);
                    $this->db->where('tbl_product_stages_versions.stage_id', $stage_id);
                    $product_stages_versions = $this->db->get()->result_array();
                    if (empty($product_stages_versions)) {
                        $errors .= '<div class="text-danger">Dòng [' . $cRow . '] không cập nhật được do không tìm thấy đúng các thông tin để cập nhật</div>';
                        continue;
                    }

                    foreach ($product_stages_versions as $k => $v) {
                        if (empty($not_machine)) {
                            $arrUpdate[] = [
                                'id' => $v['id'],
                                'machines' => $machine_id,
                                'number_face' => $number_face,
                                'number_operations' => $number_operations,
                                'number_cutting' => $number_cutting,
                            ];
                        } else {
                            $arrUpdate[] = [
                                'id' => $v['id'],
                                'number_face' => $number_face,
                                'number_operations' => $number_operations,
                                'number_cutting' => $number_cutting,
                            ];
                        }
                    }
                }

                if (!empty($arrUpdate)) {
                    $this->db->update_batch('tbl_product_stages_versions', $arrUpdate, 'id');
                    $count = 1;
                }
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
            $data['title'] = _l('tnh_update_stages');
            $this->load->view('admin/products/update_stage', $data);
        }
    }

    public function import_bom_additional()
    {
        if (!$this->perAddProducts) {
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

            /**  Load $inputFileName to a PHPExcel Object  **/
            $objPHPExcel = $objReader->load("$fullfile");

            $total_sheets = $objPHPExcel->getSheetCount();

            $allSheetName       = $objPHPExcel->getSheetNames();
            $objWorksheet       = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow         = $objWorksheet->getHighestRow();
            $highestColumn      = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('M');
            $arraydata          = array();

            $row_start = $this->input->post('row_start') ? $this->input->post('row_start') : 3;
            $row_end = $this->input->post('row_end') ? $this->input->post('row_end') : $highestRow;
            for ($row = $row_start; $row <= $row_end; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value      = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 2][$col] = $value;
                }
            }

            $options = [];
            $count = 0;
            $errors = '';
            $cRow = 3;
            $index_parent = 0;
            $index_parent_element = 0;
            $pCode = '';
            $pVersions = '';
            $pElement = '';
            $dataImport = [];
            foreach ($arraydata as $key => $value) {
                // 0: product_code
                // 1: version_bom
                // 2: item_code
                // 3: item_name
                // 4: type
                // 5: unit
                // 6: landscape_print_size
                // 7: number_children_size
                // 8: quantity
                // 9: paper_exchange
                // 10: quantity_compensation
                // 11: stage
                // 12: face

                $product_code = trim($value[0]);
                $version_bom = trim($value[1]);
                $item_code = trim($value[2]);
                $item_name = trim($value[3]);
                $type = trim($value[4]);
                $unit = trim($value[5]);
                $landscape_print_size = trim($value[6]);
                $number_children_size = number_unformat($value[7]);
                $quantity = number_unformat($value[8]);
                $paper_exchange = number_unformat($value[9]);
                $quantity_compensation = number_unformat($value[10], false);
                $stage = trim($value[11]);
                $face = trim($value[12]);

                $leadtime = 0;
                $type_element = 1;

                $hand_input_paper_exchange = 0;
                if (!empty($paper_exchange)) {
                    $hand_input_paper_exchange = 1;
                } else {
                    if (!empty($number_children_size)) {
                        $paper_exchange = roundNumberFormat(1 / $number_children_size);
                    }
                }

                $dataImport[] = [
                    'product_code' => $product_code,
                    'type' => $type,
                    'item_code' => $item_code,
                    'item_name' => $item_name,
                    'unit' => $unit,
                    'quantity' => $quantity,
                    'leadtime' => $leadtime,
                    'stage' => $stage,
                    'machines' => 0,
                    'type_element_item' => $type_element,
                    'quantity_compensation' => $quantity_compensation,
                    'landscape_print_size' => $landscape_print_size,
                    'vertical_print_size' => 0,
                    'number_children_size' => $number_children_size,
                    'paper_exchange' => $paper_exchange,
                    'hand_input_paper_exchange' => $hand_input_paper_exchange,
                    'face' => $face,
                    'version_bom' => $version_bom,
                    'cRow' => $cRow,
                ];

                $cRow++;
            }

            if (!empty($dataImport)) {
                $arrInsertBOM = [];
                foreach ($dataImport as $key => $value) {
                    $_cRow = $value['cRow'];
                    $product_code = $value['product_code'];
                    if (!$product_code) {
                        $errors .= '<div class="text-danger">Dòng [' . $_cRow . '] vui lòng nhập mã thành phẩm.</div>';
                        continue;
                    }

                    $product = $this->products_model->rowProductByCode($product_code);
                    if (empty($product)) {
                        $errors .= '<div class="text-danger">Dòng [' . $_cRow . '] mã thành phẩm [' . $product_code . '] ' . lang('chưa có trong phần mềm') . '.</div>';
                        continue;
                    }

                    $product_id = $product['id'];
                    $item_code = $value['item_code'];
                    $material = $this->items_model->rowMaterialsByCode($item_code);
                    if (empty($material)) {
                        $errors .= '<div class="text-danger">Dòng [' . $_cRow . '] mã thành phẩm [' . $product_code . '] thêm không được không có mã NVL [' . $item_code . '] trong phần mềm.</div>';
                        continue;
                    }
                    $item_id = $material['id'];

                    $unit = $value['unit'];
                    $infoUnit = $this->unit_model->rowUnitByCode($unit, 'unitid', "where");
                    if (empty($infoUnit)) {
                        $errors .= '<div class="text-danger">Dòng [' . $_cRow . '] mã thành phẩm [' . $product_code . '] thêm không được đơn vị [' . $unit . '] không có trong phần mềm.</div>';
                        continue;
                    }
                    $unit_id = $infoUnit['unitid'];

                    $unitExchange = $this->products_model->rowExchangeItems($material['id'], $infoUnit['unitid']);
                    if (empty($unitExchange)) {
                        $errors .= '<div class="text-danger">Dòng ' . $product_code . ' thêm không được vì NVL/BTP [' . $item_code . '] không có đơn vị [' . $unit . '].</div>';
                        continue;
                    }

                    $face = $value['face'];
                    if (!empty($face) && !in_array($face, [1, 2, 3])) {
                        $errors .= '<div class="text-danger">Dòng [' . $_cRow . '] mã thành phẩm [' . $product_code . '] thêm không được vì mặt in không đúng định dạng [0, 1, 2, 3].</div>';
                        continue;
                    }

                    $stage = $value['stage'];
                    $stage_id = 0;
                    if (!empty($stage)) {
                        $infoStage = $this->products_model->rowStageByCode($stage);
                        if (empty($infoStage)) {
                            $errors .= '<div class="text-danger">Dòng [' . $_cRow . '] mã thành phẩm [' . $product_code . '] thêm không được vì giai đoạn [' . $stage . '] không có trong phần mềm.</div>';
                            continue;
                        }
                        $stage_id = $infoStage['id'];
                    }

                    $version_bom = $value['version_bom'];
                    $this->db->select('
                        tbl_product_versions.id as id
                    ', false);
                    $this->db->from('tbl_product_versions');
                    $this->db->where('tbl_product_versions.product_id', $product_id);
                    $this->db->where('tbl_product_versions.versions', $version_bom);
                    $dtProductVersions = $this->db->get()->row_array();
                    if (empty($dtProductVersions)) {
                        $errors .= '<div class="text-danger">Dòng [' . $_cRow . '] mã thành phẩm [' . $product_code . '] thêm không được vì phiên bản BOM [' . $version_bom . '] không có trong thành phẩm.</div>';
                        continue;
                    }

                    $version_id = $dtProductVersions['id'];

                    $this->db->select('tbl_versions_element.id', false);
                    $this->db->from('tbl_versions_element');
                    $this->db->where('tbl_versions_element.version_id', $version_id);
                    $this->db->where('tbl_versions_element.type_element', 1);
                    $dtVersionsElement = $this->db->get()->row_array();
                    if (empty($dtVersionsElement)) {
                        $errors .= '<div class="text-danger">Dòng [' . $_cRow . '] mã thành phẩm [' . $product_code . '] thêm không được vì phiên bản BOM [' . $version_bom . '] không có trong thành phẩm.</div>';
                        continue;
                    }

                    $element_id = $dtVersionsElement['id'];

                    $this->db->select('
                        tbl_versions_element.id
                    ', false);
                    $this->db->from('tbl_versions_element');
                    $this->db->join('tbl_element_items', 'tbl_element_items.element_id = tbl_versions_element.id');
                    $this->db->where('tbl_versions_element.version_id', $version_id);
                    $this->db->where('tbl_element_items.item_id', $item_id);
                    $this->db->limit(1);
                    $dtElementItems = $this->db->get()->row_array();
                    if (!empty($dtElementItems)) {
                        $errors .= '<div class="text-danger">Dòng [' . $_cRow . '] mã thành phẩm [' . $item_code . '] thêm không được vì mã NPL [' . $version_bom . '] đã có trong thành phẩm.</div>';
                        continue;
                    }

                    $temp_face = 0;
                    $temp_face_after = 0;
                    if ($face == 1) {
                        $temp_face = 1;
                    } else if ($face == 2) {
                        $temp_face_after = 2;
                    } else if ($face == 3) {
                        $temp_face = 1;
                        $temp_face_after = 2;
                    }

                    $arrInsertBOM[] = [
                        'element_id' => $element_id,
                        'type' => 'materials',
                        'item_id' => $item_id,
                        'unit_id' => $unit_id,
                        'quantity' => $value['quantity'],
                        'leadtime' => 0,
                        'stage_id' => $stage_id,
                        'machines_id' => 0,
                        'quantity_compensation' => $value['quantity_compensation'],
                        'type_element_item' => 1,
                        'landscape_print_size' => $value['landscape_print_size'],
                        'vertical_print_size' => $value['vertical_print_size'],
                        'number_children_size' => $value['number_children_size'],
                        'paper_exchange' => $value['paper_exchange'],
                        'hand_input_paper_exchange' => $value['hand_input_paper_exchange'],
                        'face' => $temp_face,
                        'face_after' => $temp_face_after,
                    ];
                }
            }

            if (!empty($arrInsertBOM)) {
                $this->db->insert_batch('tbl_element_items', $arrInsertBOM);
                $count = 1;
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
            $data['title'] = _l('tnh_import_bom_additional');
            $this->load->view('admin/products/import_bom_additional', $data);
        }
    }

    function export_excel_products()
    {
        if (!$this->perExportProducts) {
            accessDenied($js = true);
        }
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');

            $this->db->select('
                tbl_products.id,
                tbl_brand.name as brand_name,
                tbl_products.classify,
                tblclients.zcode,
                tblclients.company,
                tbl_category_products.code as code_category,
                tbl_category_products.name as name_category,
                tbl_species.code as code_species,
                tbl_species.name as name_species,
                tblunits.unit as unit_name,
                tbl_products.wide,
                tbl_products.height,
                unit_measure.unit as unit_measure_name,
                tbl_products.code,
                tbl_products.name,
                tbl_products.quantity_minimum,
                tbl_products.time_inventory,
            ');
            $this->db->from('tbl_products');
            $this->db->join('tbl_brand', 'tbl_brand.id = tbl_products.brand_id', 'left');
            $this->db->join('tblclients', 'tblclients.userid = tbl_products.customer', 'left');
            $this->db->join('tbl_category_products', 'tbl_category_products.id = tbl_products.category_id', 'left');
            $this->db->join('tbl_species', 'tbl_species.id = tbl_products.species', 'left');
            $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');
            $this->db->join('tblunits unit_measure', 'tblunits.unitid = tbl_products.unit_measure', 'left');
            $dProducts = $this->db->get()->result_array();

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
                ('THÔNG TIN THÀNH PHẨM')
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
            $objPHPExcel->getActiveSheet()->mergeCells('A1:R1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow . '', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $sttRow . '', 'Tên Brand');
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow . '', 'Phân Loại');
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow . '', 'Mã Khách Hàng')->getStyle("D$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow . '', 'Tên Khách Hàng')->getStyle("E$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $sttRow . '', 'Mã Nhóm SP');
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $sttRow . '', 'Tên Nhóm SP');
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $sttRow . '', 'Mã Chủng Loại SP')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $sttRow . '', 'Tên Mã Chủng Loại SP')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $sttRow . '', 'ĐV Tính SP')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $sttRow . '', 'Kích Thước SP');
            $objPHPExcel->getActiveSheet()->mergeCells('K' . $sttRow . ':L' . $sttRow . '');
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $sttRow . '', 'ĐV Đo SP')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $sttRow . '', 'Mã Thành Phẩm')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O' . $sttRow . '', 'Tên Thành Phẩm')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P' . $sttRow . '', 'Tiêu Chuẩn Đóng Gói')->getStyle("P$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Q' . $sttRow . '', 'Số Lượng Tồn Cho Phép')->getStyle("Q$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('R' . $sttRow . '', 'Thời Gian Tồn Kho')->getStyle("R$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:R$sttRow")->applyFromArray([
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
                    'color' => array('rgb' => 'FFFF00'),
                ),
            ]);
            $sttRow++;
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $sttRow . '', 'Height');
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $sttRow . '', 'Width')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $sttRow . '', 'Ghép Mã : Nhóm+Chủng Loại+STT')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O' . $sttRow . '', 'Ghép Tên : Nhóm+Chủng Loại+Kích Thước+Mã Brand+STT')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:R$sttRow")->applyFromArray([
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
                    'color' => array('rgb' => 'FFFF00'),
                ),
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A' . ($sttRow - 1) . ':A' . $sttRow . '');
            $objPHPExcel->getActiveSheet()->mergeCells('B' . ($sttRow - 1) . ':B' . $sttRow . '');
            $objPHPExcel->getActiveSheet()->mergeCells('C' . ($sttRow - 1) . ':C' . $sttRow . '');
            $objPHPExcel->getActiveSheet()->mergeCells('D' . ($sttRow - 1) . ':D' . $sttRow . '');
            $objPHPExcel->getActiveSheet()->mergeCells('E' . ($sttRow - 1) . ':E' . $sttRow . '');
            $objPHPExcel->getActiveSheet()->mergeCells('F' . ($sttRow - 1) . ':F' . $sttRow . '');
            $objPHPExcel->getActiveSheet()->mergeCells('G' . ($sttRow - 1) . ':G' . $sttRow . '');
            $objPHPExcel->getActiveSheet()->mergeCells('H' . ($sttRow - 1) . ':H' . $sttRow . '');
            $objPHPExcel->getActiveSheet()->mergeCells('I' . ($sttRow - 1) . ':I' . $sttRow . '');
            $objPHPExcel->getActiveSheet()->mergeCells('J' . ($sttRow - 1) . ':J' . $sttRow . '');
            $objPHPExcel->getActiveSheet()->mergeCells('M' . ($sttRow - 1) . ':M' . $sttRow . '');
            $objPHPExcel->getActiveSheet()->mergeCells('P' . ($sttRow - 1) . ':P' . $sttRow . '');
            $objPHPExcel->getActiveSheet()->mergeCells('Q' . ($sttRow - 1) . ':Q' . $sttRow . '');
            $objPHPExcel->getActiveSheet()->mergeCells('R' . ($sttRow - 1) . ':R' . $sttRow . '');
            $rowBegin = $sttRow;
            if (!empty($dProducts)) {
                foreach ($dProducts as $key => $value) {
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['brand_name']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['classify']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", ($value['zcode']))->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['company']));
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", ($value['code_category']))->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", ($value['name_category']))->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", ($value['code_species']));
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", ($value['name_species']));
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $value['unit_name']);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $value['wide'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $value['height'])->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", $value['unit_measure_name'])->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", ($value['code']))->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", $value['name'])->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", '')->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin", $value['quantity_minimum'])->getStyle("Q$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin", $value['time_inventory'])->getStyle("R$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:R$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:A$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("C$rowBegin:C$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("J$rowBegin:J$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("M$rowBegin:M$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("K$rowBegin:K$rowBegin")->applyFromArray([
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
                    $objPHPExcel->getActiveSheet()->getStyle("Q$rowBegin:R$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                }
            }
            $filename = lang('thong_tin_thanh_pham') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setWidth(20);
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

    //	public function update_sample_cover_code() {
    //		$this->db->where('id_standard_sample_code is not null', false, false);
    //		$products = $this->db->get('tbl_products')->result_array();
    //		$arrayList = [];
    //		foreach($products as $key => $value) {
    //			$arrayList[] = [
    //				'id' => $value['id_standard_sample_code'],
    //				'id_product' => $value['id']
    //			];
    //		}
    //		$this->db->update_batch('tbllist_other', $arrayList, 'id');
    //
    ////		$this->db->select('tbl_products.id, tbllist_other.id  as id_other');
    ////		$this->db->where('sample_cover_code != ""', false, false);
    ////		$this->db->join('tbllist_other', 'tbllist_other.standard = tbl_products.sample_cover_code');
    ////		$products = $this->db->get('tbl_products')->result_array();
    ////		$arrayList = [];
    ////		foreach($products as $key => $value) {
    ////			$arrayList[] = [
    ////				'id' => $value['id'],
    ////				'id_standard_sample_code' => $value['id_other']
    ////			];
    ////		}
    ////		$this->db->update_batch('tbl_products', $arrayList, 'id');
    //
    //	}

    //	public function update_sample_cover_code() {
    //		$this->db->where('sample_cover_code != ""', false, false);
    //		$this->db->group_by('sample_cover_code');
    //		$products = $this->db->get('tbl_products')->result_array();
    //		if(!empty($products)) {
    //			$array_standard_sample_code = [];
    //			foreach($products as $key => $value) {
    //				$array_standard_sample_code[] = [
    //					'code' => $value['sample_cover_code'],
    //					'name' => $value['sample_cover_code'],
    //					'create_by' => get_staff_user_id(),
    //					'type' => 'standard_sample_code',
    //					'standard' => ''
    //				];
    //			}
    //			if(!empty($array_standard_sample_code)) {
    //				$this->db->insert_batch('tbllist_other', $array_standard_sample_code);
    //			}
    //		}
    //
    //		$this->db->where('type', 'standard_sample_code');
    //		$standard_sample_code = $this->db->get('tbllist_other')->result_array();
    //		$array_list_other = [];
    //		if(!empty($standard_sample_code)) {
    //			foreach($standard_sample_code as $key => $value) {
    //				$array_list_other[$value['code']] = $value['id'];
    //			}
    //		}
    //
    //
    //		$this->db->where('sample_cover_code != ""', false, false);
    //		$list_products = $this->db->get('tbl_products')->result_array();
    //		if(!empty($list_products)) {
    //			$arrayCodeProduct = [];
    //			foreach($list_products as $key => $value) {
    //				if(!empty($array_list_other[$value['sample_cover_code']])) {
    //					$arrayCodeProduct[] = [
    //						'id' => $value['id'],
    //						'id_standard_sample_code' => $array_list_other[$value['sample_cover_code']]
    //					];
    //				}
    //			}
    //			if(!empty($arrayCodeProduct)) {
    //				$this->db->update_batch('tbl_products', $arrayCodeProduct, 'id');
    //			}
    //		}
    //	}
}
