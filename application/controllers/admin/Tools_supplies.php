<?php

// header('Content-Type: text/html; charset=utf-8');
defined('BASEPATH') or exit('No direct script access allowed');

class Tools_supplies extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tools_supplies_model');
        $this->load->model('unit_model');
        // $this->lang->load('vietnamese/form_validation_lang');
        // $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->image_types = "*";
        $this->allowed_file_size = '2048';
        $this->upload_path = get_upload_path_by_type('tools_supplies');
        $this->datetime_now = time();
        $this->custom_fields = get_custom_fields('tools_supplies');
        $this->show_table_custom_fields = get_table_custom_fields('tools_supplies');

        //permissions category
        $this->perViewCategory = has_permission('tools_supplies_category', '', 'view');
        $this->perAddCategory = has_permission('tools_supplies_category', '', 'create');
        $this->perEditCategory = has_permission('tools_supplies_category', '', 'edit');
        $this->perDeleteCategory = has_permission('tools_supplies_category', '', 'delete');
        $this->perExportCategory = has_permission('tools_supplies_category', '', 'export');

        //permissions tools
        $this->perViewToolsSupplies = has_permission('tools_supplies', '', 'view');
        $this->perAddToolsSupplies = has_permission('tools_supplies', '', 'create');
        $this->perEditToolsSupplies = has_permission('tools_supplies', '', 'edit');
        $this->perDeleteToolsSupplies = has_permission('tools_supplies', '', 'delete');
        $this->perExportToolsSupplies = has_permission('tools_supplies', '', 'export');
    }

    public function index()
    {
        if (!$this->perViewToolsSupplies) {
            accessDenied();
        }
        $data['tnh'] = true;
        $data['path'] = $this->upload_path;
        $data['title'] = _l('tnh_tools_supplies_list');
        $th = '';
        $targets = 15;
        $script = '';
        if (!empty($this->show_table_custom_fields))
        {
            foreach ($this->show_table_custom_fields as $key => $value) {
                $th.= '<th>'._maybe_translate_custom_field_name($value['name'], $value['slug']).'</th>';
                $script.= '{
                    "targets": '.$targets.', "name": "'.$value['slug'].'", "width": "100px"
                },';
                $targets++;
            }
        }
        $data['targets'] = $targets;
        $data['script'] = $script;
        $data['th'] = $th;
        $this->load->view('admin/tools_supplies/manage', $data);
    }

    function getToolsSupplies()
    {
        if (!$this->perViewToolsSupplies) {
            accessDenied($js = true);
        }
        $status_table = $this->input->post('status_table');

        $quantityInventory = "(
            SELECT
                SUM(tblwarehouse_items.product_quantity)
            FROM tblwarehouse_items
            INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
            WHERE tblwarehouse_items.id_items = tbl_tools_supplies.id AND tblwarehouse_items.type_items = 'tools' AND tblwarehouse.supplier_id = 0
        )";

        $warehouses = "(
            SELECT GROUP_CONCAT(DISTINCT(tblwarehouse.name) SEPARATOR '</br>')
            FROM tblwarehouse_items
            INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
            WHERE tblwarehouse_items.id_items = tbl_tools_supplies.id AND tblwarehouse_items.type_items = 'tools'
        )";

        $datePurchaseTools = "(
            SELECT tblimport.date
            FROM tblimport
            INNER JOIN tblimport_items ON tblimport.id = tblimport_items.id_import
            WHERE tblimport_items.type = 'tools' AND tblimport_items.product_id = tbl_tools_supplies.id
            ORDER BY tblimport.date DESC
            LIMIT 1
        )";

        $userPurchaseTools = "(
            SELECT CONCAT(tblstaff.firstname, ' ', tblstaff.lastname)
            FROM tblimport
            INNER JOIN tblimport_items ON tblimport.id = tblimport_items.id_import
            LEFT JOIN tblstaff ON tblstaff.staffid = tblimport.staff_create
            WHERE tblimport_items.type = 'tools' AND tblimport_items.product_id = tbl_tools_supplies.id
            ORDER BY tblimport.date DESC
            LIMIT 1
        )";

        $referencePurchaseTools = "(
            SELECT CONCAT(tblimport.prefix, ' ', tblimport.code, '')
            FROM tblimport
            INNER JOIN tblimport_items ON tblimport.id = tblimport_items.id_import
            WHERE tblimport_items.type = 'tools' AND tblimport_items.product_id = tbl_tools_supplies.id
            ORDER BY tblimport.date DESC
            LIMIT 1
        )";

        $select_custom_fields = "";
        $custom = [];
        $custom_select = [];
        $target = 15;
        if (!empty($this->show_table_custom_fields))
        {
            foreach ($this->show_table_custom_fields as $key => $value) {
                $select = "COALESCE((
                    SELECT tblcustomfieldsvalues.value
                    FROM tblcustomfieldsvalues
                    WHERE tblcustomfieldsvalues.fieldto = 'tools_supplies' AND tblcustomfieldsvalues.relid = tbl_tools_supplies.id AND tblcustomfieldsvalues.fieldid = ".$value['id']."
                ), '') ";
                $select_custom_fields.= ", ". $select." as ".$value['slug'];
                $custom[] = [
                    'index' => $target,
                    // 'cloumn' => $select,
                    'select' => $value['slug'],
                ];
                $custom_select[$target] = $select;
                $target++;
            }
        }

        $custom[] = ['index' => 10, 'select' => 'quantity_inventory'];
        $custom_select[10] = $quantityInventory;

        $custom[] = ['index' => 11, 'select' => 'warehouses'];
        $custom_select[11] = $warehouses;

        $custom[] = ['index' => 12, 'select' => 'date_warehousing_nearest'];
        $custom_select[12] = $datePurchaseTools;

        $custom[] = ['index' => 13, 'select' => 'user_warehousing_nearest'];
        $custom_select[13] = $userPurchaseTools;

        $custom[] = ['index' => 14, 'select' => 'reference_warehousing_nearest'];
        $custom_select[14] = $referencePurchaseTools;

        $this->datatables->select("
            tbl_tools_supplies.id as id,
            tbl_tools_supplies.images as images,
            tbl_category_tools_supplies.name as category_name,
            tbl_tools_supplies.type as type,
            tbl_tools_supplies.code as code,
            tbl_tools_supplies.name as name,
            tblunits.unit as unit_name,
            tbl_tools_supplies.price_import as price_import,
            tbl_tools_supplies.price_import as price_import,
            tbl_tools_supplies.quantity_minimum as quantity_minimum,
            tbl_tools_supplies.note as note,
            COALESCE($quantityInventory, 0) as quantity_inventory,
            $warehouses as warehouses,
            $datePurchaseTools as date_warehousing_nearest,
            $userPurchaseTools as user_warehousing_nearest,
            $referencePurchaseTools as reference_warehousing_nearest
            $select_custom_fields
            ", FALSE)
        ->from('tbl_tools_supplies')
        ->join('tbl_category_tools_supplies', 'tbl_category_tools_supplies.id = tbl_tools_supplies.category_id', 'left')
        ->join('tblunits', 'tblunits.unitid = tbl_tools_supplies.unit_id', 'left');

        if (!empty($status_table)) {
            $this->datatables->where('tbl_tools_supplies.type', $status_table);
        }

        $this->datatables->custom_ordering($custom);
        $this->datatables->custom_select($custom_select);

        $edit = $this->perEditToolsSupplies ? '<a class="tnh-modal btn btn-success btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="'.base_url().'admin/tools_supplies/edit_item/$1"><i class="fa fa-pencil"></i></a>' : '';
        $delete = $this->perDeleteToolsSupplies ? '<button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
        <button href=\''.base_url('admin/tools_supplies/delete_tools_supplies/$1').'\' class=\'btn btn-danger po-delete-json\'>'.lang('delete').'</button>
        <button class=\'btn btn-default po-close\'>'.lang('close').'</button>
        "><i class="fa fa-remove"></i></button>' : '';

        $copy = $this->perAddToolsSupplies ? '<a class="tnh-modal btn btn-primary btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="'.base_url().'admin/tools_supplies/edit_item/$1/copy"><i class="fa fa-copy width-icon-actions"></i></a>' : '';

        $this->datatables->add_column('actions', '
            <div>
                '.$edit.'
                '.$copy.'
                '.$delete.'
            </div>
        ', 'id');
        echo $this->datatables->generate();
    }

    public function add_item()
    {
        if (!$this->perAddToolsSupplies) {
            accessDenied($js = true);
        }
        $data = [];
        if ($this->input->post())
        {
            $this->form_validation->set_rules('category', lang("tnh_category_tools_supplies"), 'required');
            $this->form_validation->set_rules('name', lang("name"), 'required');
            $this->form_validation->set_rules('unit', lang("unit"), 'required');
            $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_tools_supplies.code]');
            if ($this->form_validation->run() == true)
            {
                // print_arrays($this->input->post(), $_FILES);
                $category = $this->input->post('category');
                $name = $this->input->post('name');
                $code = $this->input->post('code');
                $note = $this->input->post('note');
                $price_import = number_unformat($this->input->post('price_import'));
                $unit = $this->input->post('unit');
                $note = $this->input->post('note');
                $type = $this->input->post('type');
                $quantity_minimum = $this->input->post('quantity_minimum');

                $options = [
                    'category_id' => $category,
                    'name' => $name,
                    'code' => $code,
                    'price_import' => $price_import,
                    'unit_id' => $unit,
                    'note' => $note,
                    'type' => $type,
                    'quantity_minimum' => $quantity_minimum,
                ];

                //image
                $this->load->library('upload');
                if (!empty($_FILES['image']) && $_FILES['image']['size'] > 0) {
                    $config['upload_path'] = $this->upload_path;
                    $config['allowed_types'] = $this->image_types;
                    $config['max_size'] = $this->allowed_file_size;
                    // $config['max_width'] = $this->Settings->iwidth;
                    // $config['max_height'] = $this->Settings->iheight;
                    // $config['file_name'] = vn_to_str($code).'_'.$this->datetime_now;
                    // $config['overwrite'] = TRUE;
                    // $config['max_filename'] = 25;
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
                $id = $this->tools_supplies_model->insertToolsSupplies($options);
                if ($id) {
                    if ($this->input->post('custom_fields')) {
                        handle_custom_fields_post($id, $this->input->post('custom_fields'));
                    }
                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    if (file_exists($this->upload_path.''.$images)) {
                        unlink($this->upload_path.''.$images);
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
            $data['custom_fields'] = $this->custom_fields;
            $data['units'] = $this->unit_model->getUnits();
            $data['categories'] = $this->tools_supplies_model->getCategoryTools();
            $this->load->view('admin/tools_supplies/add_item', $data);
        }
    }

    public function edit_item($id, $actions = "edit")
    {
        if (!$this->perEditToolsSupplies) {
            accessDenied($js = true);
        }
        $data = [];
        $tools_supplies = $this->tools_supplies_model->rowToolsSupplies($id);
        if ($this->input->post())
        {
            $this->form_validation->set_rules('category', lang("tnh_category_tools_supplies"), 'required');
            $this->form_validation->set_rules('name', lang("name"), 'required');
            $this->form_validation->set_rules('unit', lang("unit"), 'required');
            if ($tools_supplies['code'] != $this->input->post('code') || $actions == 'copy')
            {
                $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_tools_supplies.code]');
            }
            if ($this->form_validation->run() == true)
            {
                $images_old = $actions == 'edit' ? $tools_supplies['images'] : null;
                $category = $this->input->post('category');
                $name = $this->input->post('name');
                $code = $this->input->post('code');
                $note = $this->input->post('note');
                $price_import = number_unformat($this->input->post('price_import'));
                $unit = $this->input->post('unit');
                $note = $this->input->post('note');
                $type = $this->input->post('type');
                $quantity_minimum = $this->input->post('quantity_minimum');

                $options = [
                    'category_id' => $category,
                    'type' => $type,
                    'name' => $name,
                    'code' => $code,
                    'price_import' => $price_import,
                    'unit_id' => $unit,
                    'note' => $note,
                    'quantity_minimum' => $quantity_minimum,
                ];

                $this->load->library('upload');
                if (!empty($_FILES['image']) && $_FILES['image']['size'] > 0) {
                    $config['upload_path'] = $this->upload_path;
                    $config['allowed_types'] = $this->image_types;
                    $config['max_size'] = $this->allowed_file_size;
                    // $config['max_width'] = $this->Settings->iwidth;
                    // $config['max_height'] = $this->Settings->iheight;
                    // $config['file_name'] = vn_to_str($code).'_'.$this->datetime_now;
                    // $config['overwrite'] = TRUE;
                    // $config['max_filename'] = 25;
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

                if ($actions == 'edit')
                {
                    $up = $this->tools_supplies_model->updateToolsSupplies($id, $options);
                } else {
                    $up = $this->tools_supplies_model->insertToolsSupplies($options);
                    $id = $up;
                }
                if ($up) {
                    if ($this->input->post('custom_fields')) {
                        handle_custom_fields_post($id, $this->input->post('custom_fields'));
                    }
                    if (!empty($images)) {
                        if (file_exists($this->upload_path.''.$images_old)) {
                            @unlink($this->upload_path.''.$images_old);
                        }
                    }

                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    if (file_exists($this->upload_path.''.$images)) {
                        unlink($this->upload_path.''.$images);
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
            $data['actions'] = $actions;
            $data['custom_fields'] = $this->custom_fields;
            $data['tools_supplies'] = $tools_supplies;
            $data['units'] = $this->unit_model->getUnits();
            $data['categories'] = $this->tools_supplies_model->getCategoryTools();
            $this->load->view('admin/tools_supplies/edit_item', $data);
        }
    }

    function delete_tools_supplies($id)
    {
        if (!$this->perDeleteToolsSupplies) {
            accessDenied($js = true);
        }
        $data = [];
        if ($id) {
            $tools_supplies = $this->tools_supplies_model->rowToolsSupplies($id);
            if ($this->tools_supplies_model->checkToolsSupplieslUse($id)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_exist_not_delete');
                echo json_encode($data); return;
            }
            if ($this->tools_supplies_model->deleteToolsSupplies($id)) {
                if (!empty($tools_supplies['images'])) {
                    if (file_exists($this->upload_path.''.$tools_supplies['images'])) {
                        @unlink($this->upload_path.''.$tools_supplies['images']);
                    }
                }
                deleteCustomFields('tools_supplies', $id);
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

    function delete_tools_supplies_multiple()
    {
        if (!$this->perDeleteToolsSupplies) {
            accessDenied($js = true);
        }
        $data = [];
        if ($this->input->post()) {
            if (!$this->input->post('tools_supplies_id')) {
                $data['result'] = 0;
                $data['message'] = lang('no_data_exists');
                echo json_encode($data); return;
            }
            $errors = '';
            $count = 0;
            foreach ($this->input->post('tools_supplies_id') as $key => $id) {
                $tools_supplies = $this->tools_supplies_model->rowToolsSupplies($id);
                if ($this->tools_supplies_model->checkToolsSupplieslUse($id)) {
                    $errors .= '<div class="text-danger">'.$tools_supplies['code'].' '.lang('tnh_exist_not_delete').'</div>';
                    continue;
                }
                if ($this->tools_supplies_model->deleteToolsSupplies($id)) {
                    if (!empty($tools_supplies['images'])) {
                        if (file_exists($this->upload_path.''.$tools_supplies['images'])) {
                            @unlink($this->upload_path.''.$tools_supplies['images']);
                        }
                    }
                    deleteCustomFields('tools_supplies', $id);
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
            echo json_encode($data); return;
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
        $data['title'] = _l('tnh_category_tools_supplies');
        $this->load->view('admin/tools_supplies/category', $data);
    }

    public function add_category()
    {
        if (!$this->perAddCategory) {
            accessDenied($js = true);
        }

        $data = [];
        if ($this->input->post())
        {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_category_tools_supplies.code]');
            if ($this->form_validation->run() == true)
            {
                $name = $this->input->post('name');
                $code = $this->input->post('code');
                $note = $this->input->post('note');

                $options = [
                    'name' => $name,
                    'code' => $code,
                    'note' => $note,
                ];

                $id = $this->tools_supplies_model->insertCategoryToolsSupplies($options);
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
            $this->load->view('admin/tools_supplies/add_category', $data);
        }
    }

    public function edit_category($id)
    {
        if (!$this->perEditCategory) {
            accessDenied($js = true);
        }
        $data = [];
        $category = $this->tools_supplies_model->rowCategoryToolsSupplies($id);
        if ($this->input->post())
        {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($category['code'] != $this->input->post('code'))
            {
                $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_category_tools_supplies.code]');
            }
            if ($this->form_validation->run() == true)
            {
                $name = $this->input->post('name');
                $code = $this->input->post('code');
                $note = $this->input->post('note');

                $options = [
                    'name' => $name,
                    'code' => $code,
                    'note' => $note,
                ];

                $id = $this->tools_supplies_model->updateCategoryToolsSupplies($id, $options);
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
            $this->load->view('admin/tools_supplies/edit_category', $data);
        }
    }

    function getCategory()
    {
        if (!$this->perViewCategory) {
            accessDenied($js = true);
        }
        $this->datatables->select("
            tbl_category_tools_supplies.id as id,
            tbl_category_tools_supplies.code as code,
            tbl_category_tools_supplies.name as name,
            tbl_category_tools_supplies.note as note,
            ", FALSE)
        ->from('tbl_category_tools_supplies');

        $edit = $this->perEditCategory ? '<a class="tnh-modal btn btn-success btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="'.base_url().'admin/tools_supplies/edit_category/$1"><i class="fa fa-pencil"></i></a>' : '';
        $delete = $this->perDeleteCategory ? '<button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="bottom" data-content="
        <button href=\''.base_url('admin/tools_supplies/delete_category/$1').'\' class=\'btn btn-danger po-delete-json\'>'.lang('delete').'</button>
        <button class=\'btn btn-default po-close\'>'.lang('close').'</button>
        "><i class="fa fa-remove"></i></button>' : '';

        $this->datatables->add_column('actions', '
            <div>
                '.$edit.'
                '.$delete.'
            </div>
        ', 'id');
        $result = json_decode($this->datatables->generate());
        echo (json_encode($result));
    }

    function delete_category($id)
    {
        if (!$this->perDeleteCategory) {
            accessDenied($js = true);
        }
        $data = [];
        if ($id) {
            if ($this->tools_supplies_model->checkExistCategory($id)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_exist_not_delete');
                echo json_encode($data);
                return;
            }
            if ($this->tools_supplies_model->deleteCategoryToolsSupplies($id)) {
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
                echo json_encode($data); return;
            }
            $errors = '';
            $count = 0;
            foreach ($this->input->post('category_id') as $key => $id) {
                if ($this->tools_supplies_model->checkExistCategory($id)) {
                    $row = $this->tools_supplies_model->rowCategoryToolsSupplies($id);
                    $errors .= '<div class="text-danger">'.$row['code'].' '.lang('tnh_exist_not_delete').'</div>';
                    continue;
                }
                if ($this->tools_supplies_model->deleteCategoryToolsSupplies($id)) {
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
            echo json_encode($data); return;
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    function searchCategory()
    {
        $data = [];
        if ($this->input->get())
        {
            $q = $this->input->get('q');
            $limit = 50;
            $data = $this->tools_supplies_model->searchCategoryToolsSupplies($q, $limit);
        }
        echo json_encode($data);
    }

    function searchMaterials()
    {
        $data = [];
        if ($this->input->get())
        {
            $q = $this->input->get('q');
            $limit = 50;
            $data = $this->items_model->searchMaterials($q, $limit);
        }
        echo json_encode($data);
    }

    function searchToolsSupplies($id = false)
    {
        $data = [];
        $term = $this->input->get('term');
        $limit = 50;
        $data['results'] = $this->tools_supplies_model->searchToolsSupplies($term, $limit);
        if ($id) {
            // $productionOrder = $this->manufactures_model->rowProductionsOrdersById($id);
            // $data['row'] = ['id' => $productionOrder['id'], 'text' => $productionOrder['reference_no']];
        }
        echo json_encode($data);
    }

    function export_excel_category()
    {
        if (!$this->perExportCategory) {
            accessDenied($js = true);
        }
        if ($this->input->post('export_excel'))
        {
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
            $this->db->from('tbl_category_tools_supplies');
            $data = $this->db->get()->result_array();

            foreach($cloumns as $key => $value)
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$key])->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->SetCellValue($cloumns_excel[$key].'1', _l('tnh_'.$value))->getStyle($cloumns_excel[$key].'1')->applyFromArray($style_excel['Background_header']);
            }

            $row = 2;
            foreach ($data as $key => $value) {
                foreach ($cloumns as $k => $val) {
                    $index = $cloumns_excel[$k].$row;
                    $el = $value[$val];
                    $objPHPExcel->getActiveSheet()->SetCellValue($index, $el)->getStyle($index)->applyFromArray($style_excel['BStyle']);
                }
                $row++;
            }

            $filename = lang('tnh_category_tools_supplies').'.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');

            ob_start();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="$filename"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
            $objWriter->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();

            $response =  array(
                'result' => 1,
                'filename' => $filename,
                'message' => lang('success'),
                'file' => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData)
            );
            die(json_encode($response));
        } else {
            $data['link'] = 'admin/tools_supplies/export_excel_category';
            $list = [];
            $fields = get_fields_export($table = 'tbl_category_tools_supplies', $arr_diff = false);
            foreach ($fields as $key => $value) {
                $list[] = [$value => mb_strtoupper(_l('tnh_' . $value), 'UTF-8')];
            }
            $data['list'] = $list;
            $this->load->view('admin/export_excel/export_excel', $data);
        }
    }

    function export_excel_tools_supplies()
    {
        if (!$this->perExportToolsSupplies) {
            accessDenied($js = true);
        }
        if ($this->input->post('export_excel'))
        {
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

            foreach($cloumns as $key => $value)
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$key])->setAutoSize(true);

                //custom fields
                $custom = false;
                if (!empty($this->custom_fields)) {
                    foreach ($this->custom_fields as $k => $val) {
                        if ($value == ('custom_fields_'.$val['fieldto'].'_'.$val['id']))
                        {

                            $objPHPExcel->getActiveSheet()->SetCellValue($cloumns_excel[$key].'1', $val['name'])->getStyle($cloumns_excel[$key].'1')->applyFromArray($style_excel['Background_header']);

                            $value = $val['slug'];
                            $cloumns[$key] = $value;
                            $select.= "COALESCE((
                                        SELECT tblcustomfieldsvalues.value
                                        FROM tblcustomfieldsvalues
                                        WHERE tblcustomfieldsvalues.fieldto = 'tools_supplies' AND tblcustomfieldsvalues.relid = tbl_tools_supplies.id AND tblcustomfieldsvalues.fieldid = ".$val['id']."
                                    ), '') as $value, ";
                            $custom = true;
                            break;
                        }
                    }
                }
                if ($custom == true) continue;
                //end custom fields

                $objPHPExcel->getActiveSheet()->SetCellValue($cloumns_excel[$key].'1', _l('tnh_'.$value))->getStyle($cloumns_excel[$key].'1')->applyFromArray($style_excel['Background_header']);

                if ($value == "category_id")
                {
                    $value = 'category_name';
                    $cloumns[$key] = $value;
                    $select.= "tbl_category_tools_supplies.name as $value, ";
                    $left_join.= " LEFT JOIN tbl_category_tools_supplies ON tbl_category_tools_supplies.id = tbl_tools_supplies.category_id";
                }
                else if ($value == "unit_id")
                {
                    $value = 'unit_name';
                    $cloumns[$key] = $value;
                    $select.= "tblunits.unit as $value, ";
                    $left_join.= " LEFT JOIN tblunits ON tblunits.unitid = tbl_tools_supplies.unit_id";
                }
                else
                {
                    $select.= "tbl_tools_supplies.$value, ";
                }
            }

            $select = trim($select);
            $select = substr($select, 0, -1);
            $query = "
                SELECT $select
                FROM tbl_tools_supplies
                $left_join
            ";
            $data = $this->db->query($query)->result_array();
            // print_arrays($data);
            $row = 2;
            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    foreach ($cloumns as $k => $val) {
                        $index = $cloumns_excel[$k].$row;
                        $el = $value[$val];
                        if ($val == "price_import" || $val == "price_sell" || $val == "price_processing") {
                            $objPHPExcel->getActiveSheet()->SetCellValue($index, $el)->getStyle($index)->applyFromArray($style_excel['BStyle']);
                            $objPHPExcel->getActiveSheet()->getStyle($index)->getNumberFormat()->setFormatCode('#,##0.00');
                        }
                        else if ($val == "images")
                        {
                            $objPHPExcel->getActiveSheet()->SetCellValue($index, !empty($el) ? base_url('uploads/materials/').''.$el : '')->getStyle($index)->applyFromArray($style_excel['BStyle']);
                        }
                        else if ($val == "type")
                        {
                            $objPHPExcel->getActiveSheet()->SetCellValue($index, lang($el))->getStyle($index)->applyFromArray($style_excel['BStyle']);
                        }
                        else {
                            $objPHPExcel->getActiveSheet()->SetCellValue($index, $el)->getStyle($index)->applyFromArray($style_excel['BStyle']);
                        }
                    }
                    $row++;
                }
            }

            $filename = lang('tnh_tools_supplies').'.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');

            ob_start();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="$filename"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
            $objWriter->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();

            $response =  array(
                'result' => 1,
                'filename' => $filename,
                'message' => lang('success'),
                'file' => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData)
            );
            die(json_encode($response));
        } else {
            $data['link'] = 'admin/tools_supplies/export_excel_tools_supplies';
            $list = [];
            $fields = get_fields_export($table = 'tbl_tools_supplies',
                $arr_diff = [
                    'price_sell'
                ],
                $arr_more = [
                ]
            );
            foreach ($fields as $key => $value) {
                $list[] = [$value => mb_strtoupper(_l('tnh_' . $value), 'UTF-8')];
            }
            foreach ($this->custom_fields as $key => $value) {
                $list[] = ['custom_fields_'.$value['fieldto'].'_'.$value['id'] => mb_strtoupper($value['name'], 'UTF-8')];
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
        if ($this->input->post('save'))
        {
            $data = [];
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');

            $fullfile = $_FILES['file']['tmp_name'];
            if (empty($fullfile)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data); return;
            }
            $extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if($extension != 'XLSX' && $extension != 'XLS'){
                $data['result'] = 0;
                $data['message'] = lang('tnh_not_format_excel');
                echo json_encode($data); return;
            }

            if (empty($fullfile)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data); return;
            }

            if (!$this->input->post('fields')) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_fields_not_required');
                echo json_encode($data); return;
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
                    if (!empty($cloumn_excel[$col]))
                    {
                        $cloumn_current = $cloumn_excel[$col];
                        $index_current = $fields[$col];
                    } else {
                        continue;
                    }
                    $value = $objWorksheet->getCell($cloumn_current.$row)->getValue();
                    $arraydata[$row][$index_current] = $value;
                }
            }
            $options = [];
            $count = 0;
            $errors = '';
            foreach ($arraydata as $key => $value) {
                $code = !empty($value['code']) ? trim($value['code']) : '';
                $name = !empty($value['name']) ? trim($value['name']) : '';
                $note = !empty($value['note']) ? trim($value['note']) : '';
                if (empty($code) || empty($name)) {
                    continue;
                }
                $options = [
                    'code' => $code,
                    'name' => $name,
                    'note' => $note,
                ];
                if ($this->tools_supplies_model->checkCategoryToolsSuppliesByCode($code)) {
                    $errors.= '<div class="text-danger">'.$code.' '.lang('tnh_exist_data').'</div>';
                    continue;
                }
                $id = $this->tools_supplies_model->insertCategoryToolsSupplies($options);
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
            $fields = get_fields_export($table = 'tbl_category_tools_supplies', $arr_diff = ['id']);
            foreach ($fields as $key => $value) {
                $list[$value] = mb_strtoupper(lang('tnh_' . $value), 'UTF-8');
            }
            $required = [lang('tnh_name'), lang('tnh_code')];
            $data['list'] = $list;
            $data['required'] = $required;
            $this->load->view('admin/tools_supplies/import_category', $data);
        }
    }

    public function import_tools_supplies()
    {
        if (!$this->perAddToolsSupplies) {
            accessDenied();
        }
        if ($this->input->post('save'))
        {
            $data = [];
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');

            $fullfile = $_FILES['file']['tmp_name'];
            if (empty($fullfile)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data); return;
            }
            $extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if($extension != 'XLSX' && $extension != 'XLS'){
                $data['result'] = 0;
                $data['message'] = lang('tnh_not_format_excel');
                echo json_encode($data); return;
            }

            if (empty($fullfile)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data); return;
            }

            if (!$this->input->post('fields')) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_fields_not_required');
                echo json_encode($data); return;
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
                    if (!empty($cloumn_excel[$col]))
                    {
                        $cloumn_current = $cloumn_excel[$col];
                        $index_current = $fields[$col];
                    } else {
                        continue;
                    }
                    $value = $objWorksheet->getCell($cloumn_current.$row)->getValue();
                    $arraydata[$row][$index_current] = $value;
                }
            }

            $options = [];
            $count = 0;
            $errors = '';

            //option category
            $category_id_1 = $this->input->post('category_id_1');//where or like
            $category_id_2 = $this->input->post('category_id_2');//add or continue
            //unit
            $unit_id_1 = $this->input->post('unit_id_1');//where or like
            $unit_id_2 = $this->input->post('unit_id_2');//add or continue

            // print_arrays($arraydata);
            foreach ($arraydata as $key => $value) {
                $category = !empty($value['category_id']) ? trim($value['category_id']) : '';
                $type = !empty($value['type']) ? trim($value['type']) : '';
                $code = !empty($value['code']) ? trim($value['code']) : '';
                $name = !empty($value['name']) ? trim($value['name']) : '';
                $price_import = !empty($value['price_import']) ? number_unformat($value['price_import']) : 0;
                $unit = !empty($value['unit_id']) ? trim($value['unit_id']) : '';
                $note = !empty($value['note']) ? trim($value['note']) : '';

                if (empty($code) || empty($name) || empty($category) || empty($unit)) {
                    continue;
                }
                //category
                if ($category_id_1) {
                    $row_category = $this->tools_supplies_model->rowCategoryToolsSuppliesByCode($category, 'id', $category_id_1);
                    if (!empty($row_category)) {
                        $category_id = $row_category['id'];
                    } else if ($category_id_2 == 'add') {
                        $category_id = $this->tools_supplies_model->insertCategoryToolsSupplies([
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

                //custom fields
                $custom_fields = [];
                foreach ($this->custom_fields as $k => $val) {
                    if (!empty($value['custom_fields_'.$val['fieldto'].'_'.$val['id']]))
                    {
                        $custom_fields[$val['fieldto']][$val['id']] = $value['custom_fields_'.$val['fieldto'].'_'.$val['id']];
                    }
                }

                $options = [
                    'category_id' => $category_id,
                    'name' => $name,
                    'code' => $code,
                    'price_import' => $price_import,
                    'unit_id' => $unit_id,
                    'note' => $note,
                    'type' => $type,
                ];

                //check exist
                if ($this->tools_supplies_model->checkToolsSuppliesByCode($code)) {
                    $errors.= '<div class="text-danger">'.$code.' '.lang('tnh_exist_data').'</div>';
                    continue;
                }
                $id = $this->tools_supplies_model->insertToolsSupplies($options);
                if ($id) {
                    if (!empty($custom_fields)) {
                        handle_custom_fields_post($id, $custom_fields);
                    }
                    $count++;
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
            echo json_encode($data); die;
        } else {
            $data['tnh'] = true;
            $data['title'] = _l('tnh_import_excel');
            $list = [];
            $fields = get_fields_export($table = 'tbl_tools_supplies', $arr_diff = ['id', 'images', 'price_sell'], $arr_more = []);
            foreach ($fields as $key => $value) {
                $list[$value] = mb_strtoupper(lang('tnh_' . $value), 'UTF-8');
            }
            //custom fields
            foreach ($this->custom_fields as $key => $value) {
                $list['custom_fields_'.$value['fieldto'].'_'.$value['id']] = _maybe_translate_custom_field_name($value['name'], $value['slug']);
            }
            $required = [lang('tnh_category_id'), lang('tnh_type'), lang('tnh_name'), lang('tnh_code')];
            $data['list'] = $list;
            $data['required'] = $required;
            $this->load->view('admin/tools_supplies/import_tools_supplies', $data);
        }
    }

    public function print_qr()
    {
        ob_start();
        $data = [];
        $product_id = $this->input->get('ids');
        $arrId = explode(',', $product_id);
        $title = lang('IN QR CÔNG CỤ - VẬT TƯ');
        $items = null;
        if (!empty($arrId)) {
            $this->db->select('*');
            $this->db->from('tbl_tools_supplies');
            $this->db->where_in('tbl_tools_supplies.id', $arrId);
            $items = $this->db->get()->result_array();
        }

        $data['items'] = $items;

        $content = ob_get_contents();

        $data['object'] = "tools_supplies";
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
}