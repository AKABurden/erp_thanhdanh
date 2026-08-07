<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Category_maintenace extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function machines_size()
    {
        $this->perAddMachineSize = true;
        $perViewMachineSize = true;
        if (!$perViewMachineSize) {
            access_denied('machines_size');
        }
        $data['title'] = _l('dt_machines_size');
        $this->load->view('admin/category_maintenace/machines_size', $data);
    }

    public function getMachinesSize()
    {
        $perEditMachinesSize = true;
        $perDeleteMachinesSize = true;
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_machines_size.id as id',
            'tbl_machines_size.size as size',
            'tbl_machines_size.note as note'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_machines_size';
        $where = [
            'AND tbl_machines_size.type = 1'
        ];
        $filter = [];

        $join = [
        ];


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left">' . $aRow['size'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['note'] . '</div>';

            $edit = $perEditMachinesSize ? '<a class="tnh-modal" href="' . base_url('admin/category_maintenace/detail_machines_size/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . '</a>' : '';

            $delete = $perDeleteMachinesSize ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/category_maintenace/delete_machines_size/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>' : '';
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[] = '<div>' . $actions . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function detail_machines_size($id = 0)
    {
        $data = [];
        $perEditMachinesSize = true;
        $perAddMachinesSize = true;
        $this->db->select('tbl_machines_size.*');
        $this->db->from('tbl_machines_size');
        $this->db->where('tbl_machines_size.id', $id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()) {
            $this->form_validation->set_rules('size', lang("Kích thước"), 'required');
            if (empty($id)) {
                if ($this->form_validation->run() == true) {
                    $size = ($this->input->post('size'));
                    $note = ($this->input->post('note'));
                    $fields = [
                        'size' => $size,
                        'note' => $note,
                        'type' => 1,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->insert('tbl_machines_size', $fields);
                    $id = $this->db->insert_id();
                    if ($id) {
                        insertActivityLog([
                            'type_parent_obj' => 'machines_size',
                            'table_obj' => 'tbl_machines_size',
                            'id_obj' => $id,
                            'name_obj' => $size,
                            'content' => lang('Thêm mới kích thước thiết bị') . ' [' . $size . ']',
                            'actions' => 'add'
                        ]);
                        $data['result'] = 1;
                        $data['message'] = lang('Thêm thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Thêm thất bại');
                    }
                } else {
                    $data['result'] = 0;
                    $data['message'] = validation_errors();
                }
                echo json_encode($data);
                die();
            } else {
                if ($this->form_validation->run() == true) {
                    $size = ($this->input->post('size'));
                    $note = ($this->input->post('note'));
                    $fields = [
                        'size' => $size,
                        'note' => $note,
                    ];
                    $this->db->where('id', $id);
                    $success = $this->db->update('tbl_machines_size', $fields);
                    if ($success) {
                        insertActivityLog([
                            'type_parent_obj' => 'machines_size',
                            'table_obj' => 'tbl_machines_size',
                            'id_obj' => $id,
                            'name_obj' => $dtData['size'],
                            'content' => lang('Sửa kích thước thiết bị') . ' [' . $dtData['size'] . ']',
                            'actions' => 'edit'
                        ]);
                        $data['result'] = 1;
                        $data['message'] = lang('Sửa thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Sửa thất bại');
                    }
                } else {
                    $data['result'] = 0;
                    $data['message'] = validation_errors();
                }
                echo json_encode($data);
                die();
            }
        } else {
            if (empty($id)) {
                if (!$perAddMachinesSize) {
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_machines_size');
            } else {
                if (!$perEditMachinesSize) {
                    accessDenied(true);
                }
                $data['dtData'] = $dtData;
                $data['title'] = lang('dt_edit_machines_size');
            }
        }
        $data['id'] = $id;
        $this->load->view('admin/category_maintenace/detail_machines_size', $data);
    }

    public function delete_machines_size($id)
    {
        $preDeleteMachinesSize = true;
        if (!$preDeleteMachinesSize) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $data = [];
        $this->db->select('tbl_machines_size.*');
        $this->db->from('tbl_machines_size');
        $this->db->where('tbl_machines_size.id', $id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);
            die();
        }

        $this->db->where('id', $id);
        $success = $this->db->delete('tbl_machines_size');
        if ($success) {

            insertActivityLog([
                'type_parent_obj' => 'machines_size',
                'table_obj' => 'tbl_machines_size',
                'id_obj' => $id,
                'name_obj' => $dtData['size'],
                'content' => lang('Xóa kích thước thiết bị') . ' [' . $dtData['size'] . ']',
                'actions' => 'delete'
            ]);
            $data['result'] = 1;
            $data['message'] = lang('Xóa thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Xóa thất bại');
        }
        echo json_encode($data);
    }

    public function operating_size()
    {
        $this->perAddOperatingSize = true;
        $perViewOperatingSize = true;
        if (!$perViewOperatingSize) {
            access_denied('operating_size');
        }
        $data['title'] = _l('dt_operating_size');
        $this->load->view('admin/category_maintenace/operating_size', $data);
    }

    public function getOperatingSize()
    {
        $perEditOperatingSize = true;
        $perDeleteOperatingSize = true;
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_machines_size.id as id',
            'tbl_machines_size.size as size',
            'tbl_machines_size.note as note'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_machines_size';
        $where = [
            'AND tbl_machines_size.type = 2'
        ];
        $filter = [];

        $join = [
        ];


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left">' . $aRow['size'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['note'] . '</div>';

            $edit = $perEditOperatingSize ? '<a class="tnh-modal" href="' . base_url('admin/category_maintenace/detail_operating_size/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . '</a>' : '';

            $delete = $perDeleteOperatingSize ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/category_maintenace/delete_operating_size/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>' : '';
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[] = '<div>' . $actions . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function detail_operating_size($id = 0)
    {
        $data = [];
        $perEditOperatingSize = true;
        $perAddOperatingSize = true;
        $this->db->select('tbl_machines_size.*');
        $this->db->from('tbl_machines_size');
        $this->db->where('tbl_machines_size.id', $id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()) {
            $this->form_validation->set_rules('size', lang("Kích thước"), 'required');
            if (empty($id)) {
                if ($this->form_validation->run() == true) {
                    $size = ($this->input->post('size'));
                    $note = ($this->input->post('note'));
                    $fields = [
                        'size' => $size,
                        'note' => $note,
                        'type' => 2,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->insert('tbl_machines_size', $fields);
                    $id = $this->db->insert_id();
                    if ($id) {
                        insertActivityLog([
                            'type_parent_obj' => 'machines_size',
                            'table_obj' => 'tbl_machines_size',
                            'id_obj' => $id,
                            'name_obj' => $size,
                            'content' => lang('Thêm mới kích thước vận hành') . ' [' . $size . ']',
                            'actions' => 'add'
                        ]);
                        $data['result'] = 1;
                        $data['message'] = lang('Thêm thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Thêm thất bại');
                    }
                } else {
                    $data['result'] = 0;
                    $data['message'] = validation_errors();
                }
                echo json_encode($data);
                die();
            } else {
                if ($this->form_validation->run() == true) {
                    $size = ($this->input->post('size'));
                    $note = ($this->input->post('note'));
                    $fields = [
                        'size' => $size,
                        'note' => $note,
                    ];
                    $this->db->where('id', $id);
                    $success = $this->db->update('tbl_machines_size', $fields);
                    if ($success) {
                        insertActivityLog([
                            'type_parent_obj' => 'machines_size',
                            'table_obj' => 'tbl_machines_size',
                            'id_obj' => $id,
                            'name_obj' => $dtData['size'],
                            'content' => lang('Sửa kích thước vận hành') . ' [' . $dtData['size'] . ']',
                            'actions' => 'edit'
                        ]);
                        $data['result'] = 1;
                        $data['message'] = lang('Sửa thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Sửa thất bại');
                    }
                } else {
                    $data['result'] = 0;
                    $data['message'] = validation_errors();
                }
                echo json_encode($data);
                die();
            }
        } else {
            if (empty($id)) {
                if (!$perAddOperatingSize) {
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_operating_size');
            } else {
                if (!$perEditOperatingSize) {
                    accessDenied(true);
                }
                $data['dtData'] = $dtData;
                $data['title'] = lang('dt_edit_operating_size');
            }
        }
        $data['id'] = $id;
        $this->load->view('admin/category_maintenace/detail_operating_size', $data);
    }

    public function delete_operating_size($id)
    {
        $preDeleteOperatingSize = true;
        if (!$preDeleteOperatingSize) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $data = [];
        $this->db->select('tbl_machines_size.*');
        $this->db->from('tbl_machines_size');
        $this->db->where('tbl_machines_size.id', $id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);
            die();
        }

        $this->db->where('id', $id);
        $success = $this->db->delete('tbl_machines_size');
        if ($success) {

            insertActivityLog([
                'type_parent_obj' => 'machines_size',
                'table_obj' => 'tbl_machines_size',
                'id_obj' => $id,
                'name_obj' => $dtData['size'],
                'content' => lang('Xóa kích thước vận hàng') . ' [' . $dtData['size'] . ']',
                'actions' => 'delete'
            ]);
            $data['result'] = 1;
            $data['message'] = lang('Xóa thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Xóa thất bại');
        }
        echo json_encode($data);
    }

    public function index()
    {
        $this->perAddCategoryMaintenance = true;
        $perViewCategoryMaintenance = true;
        if (!$perViewCategoryMaintenance) {
            access_denied('category_maintenance');
        }
        $data['title'] = _l('dt_add_category_maintenance');
        $this->load->view('admin/category_maintenace/index', $data);
    }

    public function getCategoryMaintenance()
    {
        $perEditCategoryMaintenance = true;
        $perDeleteCategoryMaintenance = true;
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_category_maintenance.id as id',
            'tbl_category_maintenance.code as code',
            'tbl_category_maintenance.name as name',
            'tbl_category_maintenance.detail as detail'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_category_maintenance';
        $where = [
        ];
        $filter = [];

        $join = [
        ];


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left">' . $aRow['code'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['name'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['detail'] . '</div>';

            $edit = $perEditCategoryMaintenance ? '<a class="tnh-modal" href="' . base_url('admin/category_maintenace/detail_category_maintenance/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . '</a>' : '';

            $delete = $perDeleteCategoryMaintenance ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/category_maintenace/delete_category_maintenance/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>' : '';
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[] = '<div>' . $actions . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function detail_category_maintenance($id = 0)
    {
        $data = [];
        $perEditCategoryMaintenance = true;
        $perAddCategoryMaintenance = true;
        $this->db->select('tbl_category_maintenance.*');
        $this->db->from('tbl_category_maintenance');
        $this->db->where('tbl_category_maintenance.id', $id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()) {
            if (!empty($id)){
                if ($dtData['code'] != $this->input->post('code')) {
                    $this->form_validation->set_rules('code', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_category_maintenance.code]');
                }
            } else {
                $this->form_validation->set_rules('code', lang("Mã Phiếu"), 'required|is_unique[tbl_category_maintenance.code]');
            }
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if (empty($id)) {
                if ($this->form_validation->run() == true) {
                    $code = ($this->input->post('code'));
                    $name = ($this->input->post('name'));
                    $detail = ($this->input->post('detail'));
                    $fields = [
                        'code' => $code,
                        'name' => $name,
                        'detail' => $detail,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->insert('tbl_category_maintenance', $fields);
                    $id = $this->db->insert_id();
                    if ($id) {
                        insertActivityLog([
                            'type_parent_obj' => 'category_maintenance',
                            'table_obj' => 'tbl_category_maintenance',
                            'id_obj' => $id,
                            'name_obj' => $code,
                            'content' => lang('Thêm mới nhóm bảo dưỡng') . ' [' . $code . ']',
                            'actions' => 'add'
                        ]);
                        $data['result'] = 1;
                        $data['message'] = lang('Thêm thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Thêm thất bại');
                    }
                } else {
                    $data['result'] = 0;
                    $data['message'] = validation_errors();
                }
                echo json_encode($data);
                die();
            } else {
                if ($this->form_validation->run() == true) {
                    $code = ($this->input->post('code'));
                    $name = ($this->input->post('name'));
                    $detail = ($this->input->post('detail'));
                    $fields = [
                        'code' => $code,
                        'name' => $name,
                        'detail' => $detail,
                    ];
                    $this->db->where('id', $id);
                    $success = $this->db->update('tbl_category_maintenance', $fields);
                    if ($success) {
                        insertActivityLog([
                            'type_parent_obj' => 'category_maintenance',
                            'table_obj' => 'tbl_category_maintenance',
                            'id_obj' => $id,
                            'name_obj' => $dtData['code'],
                            'content' => lang('Sửa nhóm bảo dưỡng') . ' [' . $dtData['code'] . ']',
                            'actions' => 'edit'
                        ]);
                        $data['result'] = 1;
                        $data['message'] = lang('Sửa thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Sửa thất bại');
                    }
                } else {
                    $data['result'] = 0;
                    $data['message'] = validation_errors();
                }
                echo json_encode($data);
                die();
            }
        } else {
            if (empty($id)) {
                if (!$perAddCategoryMaintenance) {
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_category_maintenance');
            } else {
                if (!$perEditCategoryMaintenance) {
                    accessDenied(true);
                }
                $data['dtData'] = $dtData;
                $data['title'] = lang('dt_edit_category_maintenance');
            }
        }
        $data['id'] = $id;
        $this->load->view('admin/category_maintenace/detail_category_maintenance', $data);
    }

    public function delete_category_maintenance($id)
    {
        $preDeleteCategoryMaintenance = true;
        if (!$preDeleteCategoryMaintenance) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $data = [];
        $this->db->select('tbl_category_maintenance.*');
        $this->db->from('tbl_category_maintenance');
        $this->db->where('tbl_category_maintenance.id', $id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);
            die();
        }

        $this->db->from('tbl_suggest_maintenance');
        $this->db->where('tbl_suggest_maintenance.category_maintenance',$id);
        $checkExists = $this->db->count_all_results();
        if (!empty($checkExists)){
            $data['result'] = 0;
            $data['message'] = lang('Nhóm bảo dưỡng đã được sử dụng không thể xóa');
            echo json_encode($data);
            die();
        }

        $this->db->where('id', $id);
        $success = $this->db->delete('tbl_category_maintenance');
        if ($success) {

            insertActivityLog([
                'type_parent_obj' => 'category_maintenance',
                'table_obj' => 'tbl_category_maintenance',
                'id_obj' => $id,
                'name_obj' => $dtData['code'],
                'content' => lang('Xóa nhóm bảo dưỡng') . ' [' . $dtData['code'] . ']',
                'actions' => 'delete'
            ]);
            $data['result'] = 1;
            $data['message'] = lang('Xóa thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Xóa thất bại');
        }
        echo json_encode($data);
    }
}