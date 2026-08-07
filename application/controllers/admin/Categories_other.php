<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Categories_other extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('categories_other_model');
        // if (!is_admin()) {
        //     accessDenied();
        // 	die;
        // }
    }

    public function standard_customer()
    {
        $data = [];
        $data['title'] = lang('Tiêu chuẩn khách hàng');
        $data['status'] = 1;
        $this->load->view('admin/categories_other/standard', $data);
    }

    public function handlingStandard($id = 0, $status = 0)
    {
        $data = [];
        $standard = $id ? $this->categories_other_model->getStandardById($id) : [];
        if ($this->input->post('save')) {
            $this->form_validation->set_rules('type', lang("type"), 'required');
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($this->form_validation->run() == true) {
                $type = _string($this->input->post('type'));
                $name = _string($this->input->post('name'));
                $numeral = number_unformat($this->input->post('numeral'));
                $storage_time = number_unformat($this->input->post('storage_time'));

                $option = [
                    'type' => $type,
                    'name' => $name,
                    'numeral' => $numeral,
                    'storage_time' => $storage_time,
                    'status' => $status,
                ];

                if ($id) {
                    $ins = $this->categories_other_model->updateStandard($id, $option);
                    $standard_id = $id;
                } else {
                    $ins = $this->categories_other_model->insertStandard($option);
                    $standard_id = $ins;
                }

                if (!empty($standard_id)) {
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
        }

        $data['id'] = $id;
        $data['status'] = $status;
        $data['standard'] = $standard;
        $title = '';
        if ($status == 1) {
            $title = $id ? lang('Sửa tiêu chuẩn khách hàng') : lang('Thêm tiêu chuẩn khách hàng');
        } else if ($status == 2) {
            $title = $id ? lang('Sửa tiêu chuẩn NCC') : lang('Thêm tiêu chuẩn NCC');
        }

        $data['title'] = $title;
        $this->load->view('admin/categories_other/handling_standard', $data);
    }

    public function getStandard()
    {
        $status_search = $this->input->post('status_search');

        $aColumns = [
            'tbl_standard.id as id',
            'tbl_standard.type as type',
            'tbl_standard.name as name',
            'tbl_standard.numeral as numeral',
            'tbl_standard.storage_time as storage_time',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_standard';
        $where        = [];
        $filter = [];

        $join = [];

        array_push($where, " AND tbl_standard.status = " . $status_search . "");
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];

            $edit = '<a class="tnh-modal" href="' . base_url('admin/categories_other/handlingStandard/' . $id . '/' . $status_search) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/categories_other/deleteStandard/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';

            $row = [];
            foreach ($aColumns as $k => $v) {
                $_data = $aRow[$v];
                if ($v == 'actions') {
                    $_data = $actions;
                } else if ($v == 'id') {
                    $_data = '<div class="text-center">
                        <div class="checkbox checkbox-info">
                            <input type="checkbox" name="standard_id[]" id="check-item' . $id . '" value="' . $id . '">
                            <label for="check-item' . $id . '"></label>
                        </div>
                    </div>';
                } else {
                    $_data = '<div class="text-center">' . $_data . '</div>';
                }

                $row[] = $_data;
            }


            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function deleteStandard($id)
    {
        $data = [];
        if ($this->categories_other_model->deleteStandard($id)) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function standard_supplier()
    {
        $data = [];
        $data['title'] = lang('Tiêu chuẩn nhà cung cấp');
        $data['status'] = 2;
        $this->load->view('admin/categories_other/standard', $data);
    }

    //certification
    public function certification_supplier()
    {
        $data = [];
        $data['title'] = lang('Chứng nhận NCC');
        $data['status'] = 1;
        $this->load->view('admin/categories_other/certification', $data);
    }

    public function handlingCertification($id = 0, $status = 0)
    {
        $data = [];
        $certification = $id ? $this->categories_other_model->getCertificationById($id) : [];
        if ($this->input->post('save')) {
            $this->form_validation->set_rules('code', lang("code"), 'required');
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($this->form_validation->run() == true) {
                $code = _string($this->input->post('code'));
                $name = _string($this->input->post('name'));

                $option = [
                    'code' => $code,
                    'name' => $name,
                    'status' => $status,
                ];

                if ($id) {
                    $ins = $this->categories_other_model->updateCertification($id, $option);
                    $certification_id = $id;
                } else {
                    $ins = $this->categories_other_model->insertCertification($option);
                    $certification_id = $ins;
                }

                if (!empty($certification_id)) {
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
        }

        $data['id'] = $id;
        $data['status'] = $status;
        $data['certification'] = $certification;
        $title = '';
        if ($status == 1) {
            $title = $id ? lang('Sửa chứng nhận NCC') : lang('Thêm chứng nhận NCC');
        }

        $data['title'] = $title;
        $this->load->view('admin/categories_other/handling_certification', $data);
    }

    public function getCertification()
    {
        $status_search = $this->input->post('status_search');

        $aColumns = [
            'tbl_certification.id as id',
            'tbl_certification.code as code',
            'tbl_certification.name as name',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_certification';
        $where        = [];
        $filter = [];

        $join = [];

        array_push($where, " AND tbl_certification.status = " . $status_search . "");
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];

            $edit = '<a class="tnh-modal" href="' . base_url('admin/categories_other/handlingCertification/' . $id . '/' . $status_search) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/categories_other/deleteCertification/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';

            $row = [];
            foreach ($aColumns as $k => $v) {
                $_data = $aRow[$v];
                if ($v == 'actions') {
                    $_data = $actions;
                } else if ($v == 'id') {
                    $_data = '<div class="text-center">
                        <div class="checkbox checkbox-info">
                            <input type="checkbox" name="id[]" id="check-item' . $id . '" value="' . $id . '">
                            <label for="check-item' . $id . '"></label>
                        </div>
                    </div>';
                } else {
                    $_data = '<div class="text-center">' . $_data . '</div>';
                }

                $row[] = $_data;
            }


            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function deleteCertification($id)
    {
        $data = [];
        if ($this->categories_other_model->deleteCertification($id)) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    //discount
    public function discount_supplier()
    {
        $data = [];
        $data['title'] = lang('Chiết khấu');
        $data['status'] = 1;
        $this->load->view('admin/categories_other/discount', $data);
    }

    public function discount_supplier_new()
    {
        $data = [];
        $data['title'] = lang('Chiết khấu nhà cung cấp');
        $data['status'] = 3;
        $this->load->view('admin/categories_other/discount', $data);
    }

    public function discount_customer()
    {
        $data = [];
        $data['title'] = lang('Chiết khấu khách hàng');
        $data['status'] = 2;
        $this->load->view('admin/categories_other/discount', $data);
    }

    public function handlingDiscount($id = 0, $status = 0)
    {
        $data = [];
        $discount = $id ? $this->categories_other_model->getDiscountById($id) : [];
        if ($this->input->post('save')) {
            $this->form_validation->set_rules('code', lang("code"), 'required');
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($this->form_validation->run() == true) {
                $code = _string($this->input->post('code'));
                $name = _string($this->input->post('name'));
                $rate = number_unformat($this->input->post('rate'));

                $option = [
                    'code' => $code,
                    'name' => $name,
                    'rate' => $rate,
                    'status' => $status,
                ];

                if ($id) {
                    $ins = $this->categories_other_model->updateDiscount($id, $option);
                    $discount_id = $id;
                } else {
                    $ins = $this->categories_other_model->insertDiscount($option);
                    $discount_id = $ins;
                }

                if (!empty($discount_id)) {
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
        }

        $data['id'] = $id;
        $data['status'] = $status;
        $data['discount'] = $discount;
        $title = '';
        if ($status == 1) {
            $title = $id ? lang('Sửa chiết khấu') : lang('Thêm chiết khấu');
        } else if ($status == 2) {
            $title = $id ? lang('Sửa chiết khấu khách hàng') : lang('Thêm chiết khấu khách hàng');
        } else if ($status == 3) {
            $title = $id ? lang('Sửa chiết khấu nhà cung cấp') : lang('Thêm chiết khấu nhà cung cấp');
        }

        $data['title'] = $title;
        $this->load->view('admin/categories_other/handling_discount', $data);
    }

    public function getDiscount()
    {
        $status_search = $this->input->post('status_search');

        $aColumns = [
            'tbl_discount.id as id',
            'tbl_discount.code as code',
            'tbl_discount.name as name',
            'tbl_discount.rate as rate',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_discount';
        $where        = [];
        $filter = [];

        $join = [];

        array_push($where, " AND tbl_discount.status = " . $status_search . "");
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];

            $edit = '<a class="tnh-modal" href="' . base_url('admin/categories_other/handlingDiscount/' . $id . '/' . $status_search) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/categories_other/deleteDiscount/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';

            $row = [];
            foreach ($aColumns as $k => $v) {
                $_data = $aRow[$v];
                if ($v == 'actions') {
                    $_data = $actions;
                } else if ($v == 'id') {
                    $_data = '<div class="text-center">
                        <div class="checkbox checkbox-info">
                            <input type="checkbox" name="id[]" id="check-item' . $id . '" value="' . $id . '">
                            <label for="check-item' . $id . '"></label>
                        </div>
                    </div>';
                } else {
                    $_data = '<div class="text-center">' . $_data . '</div>';
                }

                $row[] = $_data;
            }


            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function deleteDiscount($id)
    {
        $data = [];
        if ($this->categories_other_model->deleteDiscount($id)) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    //type_orders_items
    public function type_orders_items()
    {
        $data = [];
        $data['title'] = lang('Nhóm đơn hàng');
        $data['status'] = 1;
        $this->load->view('admin/categories_other/type_orders_items', $data);
    }

    public function handlingTypeOrdersItems($id = 0, $status = 0)
    {
        $data = [];
        $typeOrdersItems = $id ? $this->categories_other_model->getTypeOrdersItemsById($id) : [];
        if ($this->input->post('save')) {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($this->form_validation->run() == true) {
                $name = _string($this->input->post('name'));
                $option = [
                    'name' => $name,
                ];

                if ($id) {
                    $ins = $this->categories_other_model->updateTypeOrdersItems($id, $option);
                    $type_orders_items_id = $id;
                } else {
                    $ins = $this->categories_other_model->insertTypeOrdersItems($option);
                    $type_orders_items_id = $ins;
                }

                if (!empty($type_orders_items_id)) {
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
        }

        $data['id'] = $id;
        $data['status'] = $status;
        $data['typeOrdersItems'] = $typeOrdersItems;
        $title = '';
        if ($status == 1) {
            $title = $id ? lang('Sửa nhóm đơn hàng') : lang('Thêm nhóm đơn hàng');
        }

        $data['title'] = $title;
        $this->load->view('admin/categories_other/handling_type_orders_items', $data);
    }

    public function getTypeOrdersItems()
    {
        $status_search = $this->input->post('status_search');
        $aColumns = [
            'tbltype_orders_items.id as id',
            'tbltype_orders_items.name as name',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbltype_orders_items';
        $where        = [];
        $filter = [];

        $join = [];

        // array_push($where, " AND tbltype_orders_items.status = ".$status_search."");
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];

            $edit = '<a class="tnh-modal" href="' . base_url('admin/categories_other/handlingTypeOrdersItems/' . $id . '/' . $status_search) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/categories_other/deleteTypeOrdersItems/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';

            $row = [];
            foreach ($aColumns as $k => $v) {
                $_data = $aRow[$v];
                if ($v == 'actions') {
                    $_data = $actions;
                } else if ($v == 'id') {
                    $_data = '<div class="text-center">
                        <div class="checkbox checkbox-info">
                            <input type="checkbox" name="id[]" id="check-item' . $id . '" value="' . $id . '">
                            <label for="check-item' . $id . '"></label>
                        </div>
                    </div>';
                } else {
                    $_data = '<div class="text-center">' . $_data . '</div>';
                }

                $row[] = $_data;
            }


            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function deleteTypeOrdersItems($id)
    {
        $data = [];

        $isCheck = $this->categories_other_model->checkUseTypeOrdersItems($id);
        if ($isCheck) {
            $data['result'] = 0;
            $data['message'] = lang('Đã sử dụng không thể xóa');
            echo json_encode($data);
            return;
        }

        if ($this->categories_other_model->deleteTypeOrdersItems($id)) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    //classify
    public function classify_orders()
    {
        $data = [];
        $data['title'] = lang('Phân loại đơn hàng');
        $data['status'] = 1;
        $this->load->view('admin/categories_other/classify', $data);
    }

    public function handlingClassify($id = 0, $status = 0)
    {
        $data = [];
        $classify = $id ? $this->categories_other_model->getClassifyById($id) : [];
        if ($this->input->post('save')) {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($this->form_validation->run() == true) {
                $name = _string($this->input->post('name'));
                $option = [
                    'name' => $name,
                    'status' => $status,
                ];

                if ($id) {
                    $ins = $this->categories_other_model->updateClassify($id, $option);
                    $classify_id = $id;
                } else {
                    $ins = $this->categories_other_model->insertClassify($option);
                    $classify_id = $ins;
                }

                if (!empty($classify_id)) {
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
        }

        $data['id'] = $id;
        $data['status'] = $status;
        $data['classify'] = $classify;
        $title = '';
        if ($status == 1) {
            $title = $id ? lang('Sửa phân loại đơn hàng') : lang('Thêm phân loại đơn hàng');
        }

        $data['title'] = $title;
        $this->load->view('admin/categories_other/handling_classify', $data);
    }

    public function getClassify()
    {
        $status_search = $this->input->post('status_search');

        $aColumns = [
            'tbl_classify.id as id',
            'tbl_classify.name as name',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_classify';
        $where        = [];
        $filter = [];

        $join = [];

        array_push($where, " AND tbl_classify.status = " . $status_search . "");
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];

            $edit = '<a class="tnh-modal" href="' . base_url('admin/categories_other/handlingClassify/' . $id . '/' . $status_search) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/categories_other/deleteClassify/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';

            $row = [];
            foreach ($aColumns as $k => $v) {
                $_data = $aRow[$v];
                if ($v == 'actions') {
                    $_data = $actions;
                } else if ($v == 'id') {
                    $_data = '<div class="text-center">
                        <div class="checkbox checkbox-info">
                            <input type="checkbox" name="id[]" id="check-item' . $id . '" value="' . $id . '">
                            <label for="check-item' . $id . '"></label>
                        </div>
                    </div>';
                } else {
                    $_data = '<div class="text-center">' . $_data . '</div>';
                }

                $row[] = $_data;
            }


            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function deleteClassify($id)
    {
        $data = [];
        if ($this->categories_other_model->deleteClassify($id)) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    //planning group
    public function planning_group_manu()
    {
        $data = [];
        $data['title'] = lang('Nhóm kế hoạch');
        $data['status'] = 1;
        $this->load->view('admin/categories_other/planning_group', $data);
    }

    public function handlingPlanningGroup($id = 0, $status = 0)
    {
        $data = [];
        $planning_group = $id ? $this->categories_other_model->getPlanningGroupById($id) : [];
        if ($this->input->post('save')) {
            $this->form_validation->set_rules('code', lang("code"), 'required');
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($this->form_validation->run() == true) {
                $code = _string($this->input->post('code'));
                $name = _string($this->input->post('name'));
                $detail = _string($this->input->post('detail'));
                $option = [
                    'code' => $code,
                    'name' => $name,
                    'detail' => $detail,
                    'status' => $status,
                ];

                if ($id) {
                    $ins = $this->categories_other_model->updatePlanningGroup($id, $option);
                    $_id = $id;
                } else {
                    $ins = $this->categories_other_model->insertPlanningGroup($option);
                    $_id = $ins;
                }

                if (!empty($_id)) {
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
        }

        $data['id'] = $id;
        $data['status'] = $status;
        $data['planning_group'] = $planning_group;
        $title = '';
        if ($status == 1) {
            $title = $id ? lang('Sửa nhóm kế hoạch') : lang('Thêm nhóm kế hoạch');
        }

        $data['title'] = $title;
        $this->load->view('admin/categories_other/handling_planning_group', $data);
    }

    public function getPlanningGroup()
    {
        $status_search = $this->input->post('status_search');

        $aColumns = [
            'tbl_planning_group.id as id',
            'tbl_planning_group.code as code',
            'tbl_planning_group.name as name',
            'tbl_planning_group.detail as detail',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_planning_group';
        $where        = [];
        $filter = [];

        $join = [];

        array_push($where, " AND tbl_planning_group.status = " . $status_search . "");
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];

            $edit = '<a class="tnh-modal" href="' . base_url('admin/categories_other/handlingPlanningGroup/' . $id . '/' . $status_search) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/categories_other/deletePlanningGroup/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';

            $row = [];
            foreach ($aColumns as $k => $v) {
                $_data = $aRow[$v];
                if ($v == 'actions') {
                    $_data = $actions;
                } else if ($v == 'id') {
                    $_data = '<div class="text-center">
                        <div class="checkbox checkbox-info">
                            <input type="checkbox" name="id[]" id="check-item' . $id . '" value="' . $id . '">
                            <label for="check-item' . $id . '"></label>
                        </div>
                    </div>';
                } else {
                    $_data = '<div class="text-center">' . $_data . '</div>';
                }

                $row[] = $_data;
            }


            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function deletePlanningGroup($id)
    {
        $data = [];
        if ($this->categories_other_model->deletePlanningGroup($id)) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    //type plan
    public function type_plan_manu()
    {
        $data = [];
        $data['title'] = lang('Loại kế hoạch');
        $data['status'] = 1;
        $this->load->view('admin/categories_other/type_plan', $data);
    }

    public function handlingTypePlan($id = 0, $status = 0)
    {
        $data = [];
        $type_plan = $id ? $this->categories_other_model->getTypePlanById($id) : [];
        if ($this->input->post('save')) {
            $this->form_validation->set_rules('code', lang("code"), 'required');
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($this->form_validation->run() == true) {
                $code = _string($this->input->post('code'));
                $name = _string($this->input->post('name'));
                $option = [
                    'code' => $code,
                    'name' => $name,
                    'status' => $status,
                ];

                if ($id) {
                    $ins = $this->categories_other_model->updateTypePlan($id, $option);
                    $_id = $id;
                } else {
                    $ins = $this->categories_other_model->insertTypePlan($option);
                    $_id = $ins;
                }

                if (!empty($_id)) {
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
        }

        $data['id'] = $id;
        $data['status'] = $status;
        $data['type_plan'] = $type_plan;
        $title = '';
        if ($status == 1) {
            $title = $id ? lang('Sửa loại kế hoạch') : lang('Thêm loại kế hoạch');
        }

        $data['title'] = $title;
        $this->load->view('admin/categories_other/handling_type_plan', $data);
    }

    public function getTypePlan()
    {
        $status_search = $this->input->post('status_search');

        $aColumns = [
            'tbl_type_plan.id as id',
            'tbl_type_plan.code as code',
            'tbl_type_plan.name as name',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_type_plan';
        $where        = [];
        $filter = [];

        $join = [];

        array_push($where, " AND tbl_type_plan.status = " . $status_search . "");
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];

            $edit = '<a class="tnh-modal" href="' . base_url('admin/categories_other/handlingTypePlan/' . $id . '/' . $status_search) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/categories_other/deleteTypePlan/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';

            $row = [];
            foreach ($aColumns as $k => $v) {
                $_data = $aRow[$v];
                if ($v == 'actions') {
                    $_data = $actions;
                } else if ($v == 'id') {
                    $_data = '<div class="text-center">
                        <div class="checkbox checkbox-info">
                            <input type="checkbox" name="id[]" id="check-item' . $id . '" value="' . $id . '">
                            <label for="check-item' . $id . '"></label>
                        </div>
                    </div>';
                } else {
                    $_data = '<div class="text-center">' . $_data . '</div>';
                }

                $row[] = $_data;
            }


            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function deleteTypePlan($id)
    {
        $data = [];
        if ($this->categories_other_model->deleteTypePlan($id)) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    //materials equipment
    public function materials_equipment()
    {
        $data = [];
        $data['title'] = lang('Thiết bị văn phòng');
        $this->load->view('admin/categories_other/materials_equipment', $data);
    }

    public function handlingMaterialsEquipment($id = 0)
    {
        $data = [];
        $materials_equipment = $id ? $this->categories_other_model->getMaterialsEquipmentById($id) : [];
        if ($this->input->post('save')) {
            $this->form_validation->set_rules('role', lang("Mã vị trí"), 'required');
            if ($this->form_validation->run() == true) {
                $role = $this->input->post('role');
                $supplies = _string($this->input->post('supplies'));
                $quantity = number_unformat($this->input->post('quantity'));
                $quality = _string($this->input->post('quality'));
                $machine = _string($this->input->post('machine'));
                $number = _string($this->input->post('number'));
                $quantity_1 = number_unformat($this->input->post('quantity_1'));
                $quality_1 = _string($this->input->post('quality_1'));
                $detail_machine = _string($this->input->post('detail_machine', false));
                $software = _string($this->input->post('software', false));

                $option = [
                    'role_id' => $role,
                    'supplies' => $supplies,
                    'quantity' => $quantity,
                    'quality' => $quality,
                    'machine' => $machine,
                    'number' => $number,
                    'quantity_1' => $quantity_1,
                    'quality_1' => $quality_1,
                    'detail_machine' => $detail_machine,
                    'software' => $software,
                ];

                if ($id) {
                    $ins = $this->categories_other_model->updateMaterialsEquipment($id, $option);
                    $_id = $id;
                } else {
                    $ins = $this->categories_other_model->insertMaterialsEquipment($option);
                    $_id = $ins;
                }

                if (!empty($_id)) {
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
        }

        $data['id'] = $id;
        $data['materials_equipment'] = $materials_equipment;
        $title = '';
        $title = $id ? lang('Sửa thiết bị văn phòng') : lang('Thêm thiết bị văn phòng');
        $data['roles'] = $this->categories_other_model->getRoles();
        $data['title'] = $title;
        $this->load->view('admin/categories_other/handling_materials_equipment', $data);
    }

    public function getMaterialsEquipment()
    {

        $dtStaff = "(
            SELECT
                tblstaff.role as role_id,
                GROUP_CONCAT(CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) SEPARATOR '</br>') as fullname
            FROM tblstaff
            WHERE tblstaff.active = 1 AND tblstaff.role != 0
            GROUP BY tblstaff.role
        ) tb_staffs";

        $aColumns = [
            'tbl_materials_equipment.id as id',
            'tblroles.code_role as code_role',
            'tblroles.name as name_role',
            'tb_staffs.fullname as staff_role',
            'tbl_materials_equipment.supplies as supplies',
            'tbl_materials_equipment.quantity as quantity',
            'tbl_materials_equipment.quality as quality',
            'tbl_materials_equipment.machine as machine',
            'tbl_materials_equipment.number as number',
            'tbl_materials_equipment.detail_machine as detail_machine',
            'tbl_materials_equipment.quantity_1 as quantity_1',
            'tbl_materials_equipment.software as software',
            'tbl_materials_equipment.quality_1 as quality_1',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_materials_equipment';
        $where        = [];
        $filter = [];

        $join = [
            'INNER JOIN tblroles ON tblroles.roleid = tbl_materials_equipment.role_id',
            'LEFT JOIN ' . $dtStaff . ' ON tb_staffs.role_id = tbl_materials_equipment.role_id'
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];

            $edit = '<a class="tnh-modal" href="' . base_url('admin/categories_other/handlingMaterialsEquipment/' . $id . '/1') . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/categories_other/deleteMaterialsEquipment/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';

            $row = [];
            foreach ($aColumns as $k => $v) {
                $_data = $aRow[$v];
                if ($v == 'actions') {
                    $_data = $actions;
                } else if ($v == 'id') {
                    $_data = '<div class="text-center">
                        <div class="checkbox checkbox-info">
                            <input type="checkbox" name="id[]" id="check-item' . $id . '" value="' . $id . '">
                            <label for="check-item' . $id . '"></label>
                        </div>
                    </div>';
                } else if ($v == 'detail_machine' || $v == 'software') {
                    $_data = '<div class="" style="white-space: break-spaces;">' . $_data . '</div>';
                } else {
                    $_data = '<div class="text-center">' . $_data . '</div>';
                }

                $row[] = $_data;
            }


            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function deleteMaterialsEquipment($id)
    {
        $data = [];
        if ($this->categories_other_model->deleteMaterialsEquipment($id)) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    //conversion formula
    public function conversion_formula_npl()
    {
        $data = [];
        $data['title'] = lang('Công thức quy đổi NPL');
        $data['status'] = 1;
        $this->load->view('admin/categories_other/conversion_formula', $data);
    }

    public function handlingConversionFormula($id = 0, $status = 0)
    {
        $data = [];
        $conversion_formula = $id ? $this->categories_other_model->getConversionFormulaById($id) : [];
        if ($this->input->post('save')) {
            $this->form_validation->set_rules('code', lang("code"), 'required');
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($this->form_validation->run() == true) {
                $code = _string($this->input->post('code'));
                $name = _string($this->input->post('name'));
                $formula = _string($this->input->post('formula'));
                $option = [
                    'code' => $code,
                    'name' => $name,
                    'formula' => $formula,
                    'status' => $status,
                ];

                if ($id) {
                    $ins = $this->categories_other_model->updateConversionFormula($id, $option);
                    $_id = $id;
                } else {
                    $ins = $this->categories_other_model->insertConversionFormula($option);
                    $_id = $ins;
                }

                if (!empty($classify_id)) {
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
        }

        $data['id'] = $id;
        $data['status'] = $status;
        $data['conversion_formula'] = $conversion_formula;
        $title = '';
        if ($status == 1) {
            $title = $id ? lang('Sửa công thức quy đổi NPL') : lang('Thêm công thức quy đổi NPL');
        }

        $data['title'] = $title;
        $this->load->view('admin/categories_other/handling_conversion_formula', $data);
    }

    public function getConversionFormula()
    {
        $status_search = $this->input->post('status_search');

        $aColumns = [
            'tbl_conversion_formula.id as id',
            'tbl_conversion_formula.code as code',
            'tbl_conversion_formula.name as name',
            'tbl_conversion_formula.formula as formula',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_conversion_formula';
        $where        = [];
        $filter = [];

        $join = [];

        array_push($where, " AND tbl_conversion_formula.status = " . $status_search . "");
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];

            $edit = '<a class="tnh-modal" href="' . base_url('admin/categories_other/handlingConversionFormula/' . $id . '/' . $status_search) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/categories_other/deleteConversionFormula/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';

            $row = [];
            foreach ($aColumns as $k => $v) {
                $_data = $aRow[$v];
                if ($v == 'actions') {
                    $_data = $actions;
                } else if ($v == 'id') {
                    $_data = '<div class="text-center">
                        <div class="checkbox checkbox-info">
                            <input type="checkbox" name="id[]" id="check-item' . $id . '" value="' . $id . '">
                            <label for="check-item' . $id . '"></label>
                        </div>
                    </div>';
                } else {
                    $_data = '<div class="text-center">' . $_data . '</div>';
                }

                $row[] = $_data;
            }


            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function deleteConversionFormula($id)
    {
        $data = [];
        if ($this->categories_other_model->deleteConversionFormula($id)) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    //materials special
    public function materials_special()
    {
        $data = [];
        $data['title'] = lang('NPL đặc biệt');
        $data['status'] = 1;
        $this->load->view('admin/categories_other/materials_special', $data);
    }

    public function handlingMaterialsSpecial($id = 0, $status = 0)
    {
        $data = [];
        $materials_special = $id ? $this->categories_other_model->getMaterialsSpecialById($id) : [];
        if ($this->input->post('save')) {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($this->form_validation->run() == true) {
                $name = _string($this->input->post('name'));
                $reason = _string($this->input->post('reason'));
                $option = [
                    'name' => $name,
                    'reason' => $reason,
                ];

                if ($id) {
                    $ins = $this->categories_other_model->updateMaterialsSpecial($id, $option);
                    $_id = $id;
                } else {
                    $ins = $this->categories_other_model->insertMaterialsSpecial($option);
                    $_id = $ins;
                }

                if (!empty($_id)) {
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
        }

        $data['id'] = $id;
        $data['status'] = $status;
        $data['materials_special'] = $materials_special;
        $title = '';
        if ($status == 1) {
            $title = $id ? lang('Sửa NPL đặc biệt') : lang('Thêm NPL đặc biệt');
        }

        $data['title'] = $title;
        $this->load->view('admin/categories_other/handling_materials_special', $data);
    }

    public function getMaterialsSpecial()
    {
        $status_search = $this->input->post('status_search');

        $aColumns = [
            'tbl_materials_special.id as id',
            'tbl_materials_special.name as name',
            'tbl_materials_special.reason as reason',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_materials_special';
        $where        = [];
        $filter = [];

        $join = [];

        // array_push($where, " AND tbl_materials_special.status = ".$status_search."");
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];

            $edit = '<a class="tnh-modal" href="' . base_url('admin/categories_other/handlingMaterialsSpecial/' . $id . '/1') . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/categories_other/deleteMaterialsSpecial/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';

            $row = [];
            foreach ($aColumns as $k => $v) {
                $_data = $aRow[$v];
                if ($v == 'actions') {
                    $_data = $actions;
                } else if ($v == 'id') {
                    $_data = '<div class="text-center">
                        <div class="checkbox checkbox-info">
                            <input type="checkbox" name="id[]" id="check-item' . $id . '" value="' . $id . '">
                            <label for="check-item' . $id . '"></label>
                        </div>
                    </div>';
                } else {
                    $_data = '<div class="text-center">' . $_data . '</div>';
                }

                $row[] = $_data;
            }


            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function deleteMaterialsSpecial($id)
    {
        $data = [];
        if ($this->categories_other_model->deleteMaterialsSpecial($id)) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    //storage time
    public function storage_time_sp()
    {
        $data = [];
        $data['title'] = lang('Thời gian lưu kho SP');
        $data['status'] = 1;
        $this->load->view('admin/categories_other/storage_time', $data);
    }

    public function handlingStorageTime($id = 0, $status = 0)
    {
        $data = [];
        $storage_time = $id ? $this->categories_other_model->getStorageTimeById($id) : [];
        if ($this->input->post('save')) {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($this->form_validation->run() == true) {
                $name = _string($this->input->post('name'));
                $option = [
                    'name' => $name,
                    'status' => $status,
                ];

                if ($id) {
                    $ins = $this->categories_other_model->updateStorageTime($id, $option);
                    $_id = $id;
                } else {
                    $ins = $this->categories_other_model->insertStorageTime($option);
                    $_id = $ins;
                }

                if (!empty($_id)) {
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
        }

        $data['id'] = $id;
        $data['status'] = $status;
        $data['storage_time'] = $storage_time;
        $title = '';
        if ($status == 1) {
            $title = $id ? lang('Sửa thời gian lưu kho SP') : lang('Thêm thời gian lưu kho SP');
        } else if ($status == 2) {
            $title = $id ? lang('Sửa thời gian lưu kho NPL') : lang('Thêm thời gian lưu kho NPL');
        }

        $data['title'] = $title;
        $this->load->view('admin/categories_other/handling_storage_time', $data);
    }

    public function getStorageTime()
    {
        $status_search = $this->input->post('status_search');

        $aColumns = [
            'tbl_storage_time.id as id',
            'tbl_storage_time.name as name',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_storage_time';
        $where        = [];
        $filter = [];

        $join = [];

        array_push($where, " AND tbl_storage_time.status = " . $status_search . "");
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];

            $edit = '<a class="tnh-modal" href="' . base_url('admin/categories_other/handlingStorageTime/' . $id . '/' . $status_search) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/categories_other/deleteStorageTime/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';

            $row = [];
            foreach ($aColumns as $k => $v) {
                $_data = $aRow[$v];
                if ($v == 'actions') {
                    $_data = $actions;
                } else if ($v == 'id') {
                    $_data = '<div class="text-center">
                        <div class="checkbox checkbox-info">
                            <input type="checkbox" name="id[]" id="check-item' . $id . '" value="' . $id . '">
                            <label for="check-item' . $id . '"></label>
                        </div>
                    </div>';
                } else {
                    $_data = '<div class="text-center">' . $_data . '</div>';
                }

                $row[] = $_data;
            }


            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function deleteStorageTime($id)
    {
        $data = [];
        if ($this->categories_other_model->deleteStorageTime($id)) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    //unit warehouse
    public function unit_warehouse_npl()
    {
        $data = [];
        $data['title'] = lang('Đơn vị vào kho NPL');
        $data['status'] = 1;
        $this->load->view('admin/categories_other/unit_warehouse', $data);
    }

    public function unit_package_sp()
    {
        $data = [];
        $data['title'] = lang('Đơn vị đóng gói SP');
        $data['status'] = 2;
        $this->load->view('admin/categories_other/unit_warehouse', $data);
    }

    public function unit_package_npl()
    {
        $data = [];
        $data['title'] = lang('Đơn vị đóng gói NPL');
        $data['status'] = 3;
        $this->load->view('admin/categories_other/unit_warehouse', $data);
    }

    public function unit_machines()
    {
        $data = [];
        $data['title'] = lang('Đơn vị tính thiết bị');
        $data['status'] = 4;
        $this->load->view('admin/categories_other/unit_warehouse', $data);
    }

    public function handlingUnitWarehouse($id = 0, $status = 0)
    {
        $data = [];
        $unit_warehouse = $id ? $this->categories_other_model->getUnitWarehouseById($id) : [];
        if ($this->input->post('save')) {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($this->form_validation->run() == true) {
                $code_unit_warehouse = _string($this->input->post('code_unit_warehouse'));
                $name = _string($this->input->post('name'));
                if (empty($id)) {
                    $this->db->where('status', $status);
                    $this->db->where('code_unit_warehouse', $code_unit_warehouse);
                    $ktCode = $this->db->get('tbl_unit_warehouse')->row();
                    if (!empty($ktCode)) {
                        echo json_encode(['success' => false, 'alert_type' => 'success', 'message' => 'Mã đã tồn tại vui lòng nhập mã khác']);
                        die();
                    }
                } else {
                    $this->db->where('id != "' . $id . '"', false, false);
                    $this->db->where('status', $status);
                    $this->db->where('code_unit_warehouse', $code_unit_warehouse);
                    $ktCode = $this->db->get('tbl_unit_warehouse')->row();
                    if (!empty($ktCode)) {
                        echo json_encode(['success' => false, 'alert_type' => 'success', 'message' => 'Mã đã tồn tại vui lòng nhập mã khác']);
                        die();
                    }
                }

                $option = [
                    'code_unit_warehouse' => $code_unit_warehouse,
                    'name' => $name,
                    'status' => $status,
                ];

                if ($id) {
                    $ins = $this->categories_other_model->updateUnitWarehouse($id, $option);
                    $_id = $id;
                } else {
                    $ins = $this->categories_other_model->insertUnitWarehouse($option);
                    $_id = $ins;
                }

                if (!empty($_id)) {
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
        }

        $data['id'] = $id;
        $data['status'] = $status;
        $data['unit_warehouse'] = $unit_warehouse;
        $title = '';
        if ($status == 1) {
            $title = $id ? lang('Sửa đơn vị vào kho NPL') : lang('Thêm đơn vị vào kho NPL');
        } else if ($status == 2) {
            $title = $id ? lang('Sửa đơn vị đóng gói SP') : lang('Thêm đơn vị đóng gói SP');
        } else if ($status == 3) {
            $title = $id ? lang('Sửa đơn vị đóng gói NPL') : lang('Thêm đơn vị đóng gói NPL');
        } else if ($status == 4) {
            $title = $id ? lang('Sửa đơn vị tính thiết bị') : lang('Thêm đơn vị tính thiết bị');
        }

        $data['title'] = $title;
        $this->load->view('admin/categories_other/handling_unit_warehouse', $data);
    }

    public function getUnitWarehouse()
    {
        $status_search = $this->input->post('status_search');

        $aColumns = [
            'tbl_unit_warehouse.id as id',
            'tbl_unit_warehouse.code_unit_warehouse as code_unit_warehouse',
            'tbl_unit_warehouse.name as name',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_unit_warehouse';
        $where        = [];
        $filter = [];

        $join = [];

        array_push($where, " AND tbl_unit_warehouse.status = " . $status_search . "");
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];

            $edit = '<a class="tnh-modal" href="' . base_url('admin/categories_other/handlingUnitWarehouse/' . $id . '/' . $status_search) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/categories_other/deleteUnitWarehouse/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';

            $row = [];
            foreach ($aColumns as $k => $v) {
                $_data = $aRow[$v];
                if ($v == 'actions') {
                    $_data = $actions;
                } else if ($v == 'id') {
                    $_data = '<div class="text-center">
                        <div class="checkbox checkbox-info">
                            <input type="checkbox" name="id[]" id="check-item' . $id . '" value="' . $id . '">
                            <label for="check-item' . $id . '"></label>
                        </div>
                    </div>';
                } else {
                    $_data = '<div class="text-center">' . $_data . '</div>';
                }

                $row[] = $_data;
            }


            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function deleteUnitWarehouse($id)
    {
        $data = [];
        if ($this->categories_other_model->deleteUnitWarehouse($id)) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    //storage time
    public function storage_time_npl()
    {
        $data = [];
        $data['title'] = lang('Thời gian lưu kho NPL');
        $data['status'] = 2;
        $this->load->view('admin/categories_other/storage_time', $data);
    }

    //inventory type
    public function inventory_type_warehouse()
    {
        $data = [];
        $data['title'] = lang('Loại kiểm kê');
        $data['status'] = 1;
        $this->load->view('admin/categories_other/inventory_type', $data);
    }

    public function handlingInventoryType($id = 0, $status = 0)
    {
        $data = [];
        $inventory_type = $id ? $this->categories_other_model->getInventoryTypeById($id) : [];
        if ($this->input->post('save')) {
            $this->form_validation->set_rules('code', lang("code"), 'required');
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($this->form_validation->run() == true) {
                $code = _string($this->input->post('code'));
                $name = _string($this->input->post('name'));
                $option = [
                    'code' => $code,
                    'name' => $name,
                    'status' => $status,
                ];

                if ($id) {
                    $ins = $this->categories_other_model->updateInventoryType($id, $option);
                    $_id = $id;
                } else {
                    $ins = $this->categories_other_model->insertInventoryType($option);
                    $_id = $ins;
                }

                if (!empty($_id)) {
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
        }

        $data['id'] = $id;
        $data['status'] = $status;
        $data['inventory_type'] = $inventory_type;
        $title = '';
        if ($status == 1) {
            $title = $id ? lang('Sửa loại kiểm kê') : lang('Thêm loại kiểm kê');
        }

        $data['title'] = $title;
        $this->load->view('admin/categories_other/handling_inventory_type', $data);
    }

    public function getInventoryType()
    {
        $status_search = $this->input->post('status_search');

        $aColumns = [
            'tbl_inventory_type.id as id',
            'tbl_inventory_type.code as code',
            'tbl_inventory_type.name as name',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_inventory_type';
        $where        = [];
        $filter = [];

        $join = [];

        array_push($where, " AND tbl_inventory_type.status = " . $status_search . "");
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];

            $edit = '<a class="tnh-modal" href="' . base_url('admin/categories_other/handlingInventoryType/' . $id . '/' . $status_search) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/categories_other/deleteInventoryType/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';

            $row = [];
            foreach ($aColumns as $k => $v) {
                $_data = $aRow[$v];
                if ($v == 'actions') {
                    $_data = $actions;
                } else if ($v == 'id') {
                    $_data = '<div class="text-center">
                        <div class="checkbox checkbox-info">
                            <input type="checkbox" name="id[]" id="check-item' . $id . '" value="' . $id . '">
                            <label for="check-item' . $id . '"></label>
                        </div>
                    </div>';
                } else {
                    $_data = '<div class="text-center">' . $_data . '</div>';
                }

                $row[] = $_data;
            }


            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function deleteInventoryType($id)
    {
        $data = [];
        if ($this->categories_other_model->deleteInventoryType($id)) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    //inventory group
    public function inventory_group_warehouse()
    {
        $data = [];
        $data['title'] = lang('Nhóm kiểm kê');
        $data['status'] = 1;
        $data['label'] = [
            'code_group' => 'Mã nhóm kiểm kê',
            'name_group' => 'Tên nhóm kiểm kê',
            'code' => 'Mã kiểm kê',
            'detail' => 'Chi tiết',
        ];

        $this->load->view('admin/categories_other/inventory_group', $data);
    }

    public function certification_group()
    {
        $data = [];
        $data['title'] = lang('Nhóm chứng nhận');
        $data['status'] = 2;
        $data['label'] = [
            'code_group' => 'Mã nhóm chứng nhận',
            'name_group' => 'Tên nhóm chứng nhận',
            'code' => 'Mã chứng nhận',
            'detail' => 'Chi tiết',
        ];
        $this->load->view('admin/categories_other/inventory_group', $data);
    }

    public function handlingInventoryGroup($id = 0, $status = 0)
    {
        $data = [];
        $inventory_group = $id ? $this->categories_other_model->getInventoryGroupById($id) : [];
        if ($this->input->post('save')) {
            $this->form_validation->set_rules('code_group', lang("Mã nhóm"), 'required');
            $this->form_validation->set_rules('name_group', lang("Tên nhóm"), 'required');
            $this->form_validation->set_rules('code', lang("Mã"), 'required');
            if ($this->form_validation->run() == true) {
                $code_group = _string($this->input->post('code_group'));
                $name_group = _string($this->input->post('name_group'));
                $code = _string($this->input->post('code'));
                $detail = _string($this->input->post('detail'));
                $option = [
                    'code_group' => $code_group,
                    'name_group' => $name_group,
                    'code' => $code,
                    'detail' => $detail,
                    'status' => $status,
                ];

                if ($id) {
                    $ins = $this->categories_other_model->updateInventoryGroup($id, $option);
                    $_id = $id;
                } else {
                    $ins = $this->categories_other_model->insertInventoryGroup($option);
                    $_id = $ins;
                }

                if (!empty($_id)) {
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
        }

        $data['id'] = $id;
        $data['status'] = $status;
        $data['inventory_group'] = $inventory_group;
        $title = '';
        if ($status == 1) {
            $title = $id ? lang('Sửa loại kiểm kê') : lang('Thêm loại kiểm kê');
            $data['label'] = [
                'code_group' => 'Mã nhóm kiểm kê',
                'name_group' => 'Tên nhóm kiểm kê',
                'code' => 'Mã kiểm kê',
                'detail' => 'Chi tiết',
            ];
        } else if ($status == 2) {
            $title = $id ? lang('Sửa nhóm chứng nhận') : lang('Thêm nhóm chứng nhận');
            $data['label'] = [
                'code_group' => 'Mã nhóm chứng nhận',
                'name_group' => 'Tên nhóm chứng nhận',
                'code' => 'Mã chứng nhận',
                'detail' => 'Chi tiết',
            ];
        }

        $data['title'] = $title;
        $this->load->view('admin/categories_other/handling_inventory_group', $data);
    }

    public function getInventoryGroup()
    {
        $status_search = $this->input->post('status_search');

        $aColumns = [
            'tbl_inventory_group.id as id',
            'tbl_inventory_group.code_group as code_group',
            'tbl_inventory_group.name_group as name_group',
            'tbl_inventory_group.code as code',
            'tbl_inventory_group.detail as detail',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_inventory_group';
        $where        = [];
        $filter = [];

        $join = [];

        array_push($where, " AND tbl_inventory_group.status = " . $status_search . "");
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];

            $edit = '<a class="tnh-modal" href="' . base_url('admin/categories_other/handlingInventoryGroup/' . $id . '/' . $status_search) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/categories_other/deleteInventoryGroup/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';

            $row = [];
            foreach ($aColumns as $k => $v) {
                $_data = $aRow[$v];
                if ($v == 'actions') {
                    $_data = $actions;
                } else if ($v == 'id') {
                    $_data = '<div class="text-center">
                        <div class="checkbox checkbox-info">
                            <input type="checkbox" name="id[]" id="check-item' . $id . '" value="' . $id . '">
                            <label for="check-item' . $id . '"></label>
                        </div>
                    </div>';
                } else {
                    $_data = '<div class="text-center">' . $_data . '</div>';
                }

                $row[] = $_data;
            }


            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function deleteInventoryGroup($id)
    {
        $data = [];
        if ($this->categories_other_model->deleteInventoryGroup($id)) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    //import export code
    public function export_code()
    {
        $data = [];
        $data['title'] = lang('Mã số xuất khẩu');
        $data['status'] = 1;
        $this->load->view('admin/categories_other/import_export_code', $data);
    }

    public function import_code()
    {
        $data = [];
        $data['title'] = lang('Mã số nhập khẩu');
        $data['status'] = 2;
        $this->load->view('admin/categories_other/import_export_code', $data);
    }

    public function handlingImportExportCode($id = 0, $status = 0)
    {
        $data = [];
        $import_export_code = $id ? $this->categories_other_model->getImportExportCodeById($id) : [];
        if ($this->input->post('save')) {
            $this->form_validation->set_rules('code', lang("code"), 'required');
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($this->form_validation->run() == true) {
                $code = _string($this->input->post('code'));
                $name = _string($this->input->post('name'));
                $option = [
                    'code' => $code,
                    'name' => $name,
                    'status' => $status,
                ];

                if ($id) {
                    $ins = $this->categories_other_model->updateImportExportCode($id, $option);
                    $_id = $id;
                } else {
                    $ins = $this->categories_other_model->insertImportExportCode($option);
                    $_id = $ins;
                }

                if (!empty($_id)) {
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
        }

        $data['id'] = $id;
        $data['status'] = $status;
        $data['import_export_code'] = $import_export_code;
        $title = '';
        if ($status == 1) {
            $title = $id ? lang('Sửa mã số xuất khẩu') : lang('Thêm mã số xuất khẩu');
        } else {
            $title = $id ? lang('Sửa mã số nhập khẩu') : lang('Thêm mã số nhập khẩu');
        }

        $data['title'] = $title;
        $this->load->view('admin/categories_other/handling_import_export_code', $data);
    }

    public function getImportExportCode()
    {
        $status_search = $this->input->post('status_search');

        $aColumns = [
            'tbl_import_export_code.id as id',
            'tbl_import_export_code.code as code',
            'tbl_import_export_code.name as name',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_import_export_code';
        $where        = [];
        $filter = [];

        $join = [];

        array_push($where, " AND tbl_import_export_code.status = " . $status_search . "");
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];

            $edit = '<a class="tnh-modal" href="' . base_url('admin/categories_other/handlingImportExportCode/' . $id . '/' . $status_search) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/categories_other/deleteImportExportCode/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';

            $row = [];
            foreach ($aColumns as $k => $v) {
                $_data = $aRow[$v];
                if ($v == 'actions') {
                    $_data = $actions;
                } else if ($v == 'id') {
                    $_data = '<div class="text-center">
                        <div class="checkbox checkbox-info">
                            <input type="checkbox" name="id[]" id="check-item' . $id . '" value="' . $id . '">
                            <label for="check-item' . $id . '"></label>
                        </div>
                    </div>';
                } else {
                    $_data = '<div class="text-center">' . $_data . '</div>';
                }

                $row[] = $_data;
            }


            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function deleteImportExportCode($id)
    {
        $data = [];
        if ($this->categories_other_model->deleteImportExportCode($id)) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    //packaging standards
    public function packaging_standards_sp()
    {
        $data = [];
        $data['title'] = lang('Tiêu chuẩn đóng gói sản phẩm');
        $data['status'] = 1;
        $this->load->view('admin/categories_other/packaging_standards', $data);
    }

    public function packaging_standards_npl()
    {
        $data = [];
        $data['title'] = lang('Tiêu chuẩn đóng gói NPL');
        $data['status'] = 2;
        $this->load->view('admin/categories_other/packaging_standards', $data);
    }

    public function handlingPackagingStandards($id = 0, $status = 0)
    {
        $data = [];
        $packaging_standards = $id ? $this->categories_other_model->getPackagingStandardsById($id) : [];
        if ($this->input->post('save')) {
            $this->form_validation->set_rules('code_group', lang("Mã nhóm tiêu chuẩn"), 'required');
            $this->form_validation->set_rules('name_group', lang("Tên nhóm tiêu chuẩn"), 'required');
            $this->form_validation->set_rules('code', lang("Mã tiêu chuẩn"), 'required');
            if ($this->form_validation->run() == true) {
                $code_group = _string($this->input->post('code_group'));
                $name_group = _string($this->input->post('name_group'));
                $code = _string($this->input->post('code'));
                $detail = _string($this->input->post('detail'));
                $option = [
                    'code_group' => $code_group,
                    'name_group' => $name_group,
                    'code' => $code,
                    'detail' => $detail,
                    'status' => $status,
                ];

                if ($id) {
                    $ins = $this->categories_other_model->updatePackagingStandards($id, $option);
                    $_id = $id;
                } else {
                    $ins = $this->categories_other_model->insertPackagingStandards($option);
                    $_id = $ins;
                }

                if (!empty($_id)) {
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
        }

        $data['id'] = $id;
        $data['status'] = $status;
        $data['packaging_standards'] = $packaging_standards;
        $title = '';
        if ($status == 1) {
            $title = $id ? lang('Sửa tiêu chuẩn đóng gói sản phẩm') : lang('Thêm tiêu chuẩn đóng gói sản phẩm');
        } else if ($status == 2) {
            $title = $id ? lang('Sửa tiêu chuẩn đóng gói NPL') : lang('Thêm tiêu chuẩn đóng gói NPL');
        }

        $data['title'] = $title;
        $this->load->view('admin/categories_other/handling_packaging_standards', $data);
    }

    public function getPackagingStandards()
    {
        $status_search = $this->input->post('status_search');

        $aColumns = [
            'tbl_packaging_standards.id as id',
            'tbl_packaging_standards.code_group as code_group',
            'tbl_packaging_standards.name_group as name_group',
            'tbl_packaging_standards.code as code',
            'tbl_packaging_standards.detail as detail',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_packaging_standards';
        $where        = [];
        $filter = [];

        $join = [];

        array_push($where, " AND tbl_packaging_standards.status = " . $status_search . "");
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];

            $edit = '<a class="tnh-modal" href="' . base_url('admin/categories_other/handlingPackagingStandards/' . $id . '/' . $status_search) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/categories_other/deletePackagingStandards/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';

            $row = [];
            foreach ($aColumns as $k => $v) {
                $_data = $aRow[$v];
                if ($v == 'actions') {
                    $_data = $actions;
                } else if ($v == 'id') {
                    $_data = '<div class="text-center">
                        <div class="checkbox checkbox-info">
                            <input type="checkbox" name="id[]" id="check-item' . $id . '" value="' . $id . '">
                            <label for="check-item' . $id . '"></label>
                        </div>
                    </div>';
                } else {
                    $_data = '<div class="text-center">' . $_data . '</div>';
                }

                $row[] = $_data;
            }


            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function deletePackagingStandards($id)
    {
        $data = [];
        if ($this->categories_other_model->deletePackagingStandards($id)) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    //vehicle
    public function vehicle_cl()
    {
        $data = [];
        $data['title'] = lang('Phương tiện');
        $data['status'] = 1;
        $this->load->view('admin/categories_other/vehicle', $data);
    }

    public function handlingVehicle($id = 0, $status = 0)
    {
        $data = [];
        $vehicle = $id ? $this->categories_other_model->getVehicleById($id) : [];
        if ($this->input->post('save')) {
            $this->form_validation->set_rules('code_group', lang("Mã nhóm phương tiện"), 'required');
            $this->form_validation->set_rules('name_group', lang("Tên nhóm phương tiện"), 'required');
            $this->form_validation->set_rules('code', lang("Mã phương tiện"), 'required');
            $this->form_validation->set_rules('name', lang("Tên phương tiện"), 'required');
            if ($this->form_validation->run() == true) {
                $code_group = _string($this->input->post('code_group'));
                $name_group = _string($this->input->post('name_group'));
                $code = _string($this->input->post('code'));
                $name = _string($this->input->post('name'));
                $option = [
                    'code_group' => $code_group,
                    'name_group' => $name_group,
                    'code' => $code,
                    'name' => $name,
                    'status' => $status,
                ];

                if ($id) {
                    $ins = $this->categories_other_model->updateVehicle($id, $option);
                    $_id = $id;
                } else {
                    $ins = $this->categories_other_model->insertVehicle($option);
                    $_id = $ins;
                }

                if (!empty($_id)) {
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
        }

        $data['id'] = $id;
        $data['status'] = $status;
        $data['vehicle'] = $vehicle;
        $title = '';
        if ($status == 1) {
            $title = $id ? lang('Sửa phương tiện') : lang('Thêm phương tiện');
        }

        $data['title'] = $title;
        $this->load->view('admin/categories_other/handling_vehicle', $data);
    }

    public function getVehicle()
    {
        $status_search = $this->input->post('status_search');

        $aColumns = [
            'tbl_vehicle.id as id',
            'tbl_vehicle.code_group as code_group',
            'tbl_vehicle.name_group as name_group',
            'tbl_vehicle.code as code',
            'tbl_vehicle.name as name',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_vehicle';
        $where        = [];
        $filter = [];

        $join = [];

        array_push($where, " AND tbl_vehicle.status = " . $status_search . "");
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];

            $edit = '<a class="tnh-modal" href="' . base_url('admin/categories_other/handlingVehicle/' . $id . '/' . $status_search) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/categories_other/deleteVehicle/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';

            $row = [];
            foreach ($aColumns as $k => $v) {
                $_data = $aRow[$v];
                if ($v == 'actions') {
                    $_data = $actions;
                } else if ($v == 'id') {
                    $_data = '<div class="text-center">
                        <div class="checkbox checkbox-info">
                            <input type="checkbox" name="id[]" id="check-item' . $id . '" value="' . $id . '">
                            <label for="check-item' . $id . '"></label>
                        </div>
                    </div>';
                } else {
                    $_data = '<div class="text-center">' . $_data . '</div>';
                }

                $row[] = $_data;
            }


            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function deleteVehicle($id)
    {
        $data = [];
        if ($this->categories_other_model->deleteVehicle($id)) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }
    //pccc
    public function pccc()
    {
        if (!has_permission('pccc', '', 'view')) {
            access_denied();
        }
        $data = [];
        $data['title'] = lang('Khu Vực PCCC');
        $data['status'] = 1;
        $this->load->view('admin/categories_other/pccc', $data);
    }
    public function handlingpccc($id = 0)
    {
        $data = [];
        $pccc = $id ? $this->categories_other_model->getCleaningById($id) : [];
        if ($this->input->post()) {
            $code_group = _string($this->input->post('code_group'));
            if (empty($id)) {
                // $this->form_validation->set_rules('code_group', lang("Mã khu vực"), 'required|is_unique[tbl_cleaning.code_group]'); 
                // Lấy giá trị của 'type' từ POST request
                $this->db->where('code_group', $code_group);
                $this->db->where('ballot_type', 1);
                $query = $this->db->get('tbl_cleaning');
                if ($query->num_rows() > 0) {
                    // $this->form_validation->set_message('check_unique_code_group_type', 'Mã khu vực đã tồn tại.');
                    echo json_encode([
                        'result' => 0,
                        'message' => 'Mã khu vực đã tồn tại'
                    ]);
                    return;
                }
            } else {
                if ($pccc['code_group'] != $code_group) {
                    // $this->form_validation->set_rules('code_group', lang("Mã khu vực"), 'required|is_unique[tbl_cleaning.code_group]');
                    $this->db->where('code_group', $code_group);
                    $this->db->where('ballot_type', 1);
                    $query = $this->db->get('tbl_cleaning');
                    if ($query->num_rows() > 0) {
                        // $this->form_validation->set_message('check_unique_code_group_type', 'Mã khu vực đã tồn tại.');
                        echo json_encode([
                            'result' => 0,
                            'message' => 'Mã khu vực đã tồn tại'
                        ]);
                        return;
                    }
                }
            }

            $name = _string($this->input->post('name'));
            $data = $this->input->post();
            $detail = $this->input->post('detail');
            if (!empty($id)) {
                if (!has_permission('pccc', '', 'edit')) {
                    ajax_access_denied();
                }

                $this->db->where('id', $id);
                $success = $this->db->update('tbl_cleaning', [
                    'code_group' => $code_group,
                    'name' => $name,
                    'ballot_type' => 1,
                    'note' => (!empty($data['note']) ? $data['note'] : ''),
                ]);
                if (!empty($success)) {
                    $arrayDetailNotDelete = [];
                    if (!empty($detail)) {
                        $arrayDetail = [];
                        if (!file_exists(FCPATH . 'uploads/rel_handling_detail/')) {
                            mkdir(FCPATH . 'uploads/rel_handling_detail/');
                            fopen(FCPATH . 'uploads/rel_handling_detail/index.html', 'w');
                        }
                        foreach ($detail as $key => $value) {
                            if (!empty($value['id'])) {
                                $this->db->where('id', $value['id']);
                                $this->db->where('id_cleaning', $id);
                                $cleaning_detail = $this->db->get('tbl_cleaning_detail')->row();

                                $this->db->where('id', $value['id']);
                                $this->db->where('id_cleaning', $id);
                                $this->db->update('tbl_cleaning_detail', [
                                    'name' => $value['name'],
                                    'note' => $value['note'],
                                ]);
                                $arrayDetailNotDelete[] = $value['id'];
                                if (!empty($_FILES['detail']['name'][$key]['file'])) {
                                    if (!file_exists(FCPATH . 'uploads/rel_handling_detail/' . $value['id'] . '/')) {
                                        mkdir(FCPATH . 'uploads/rel_handling_detail/' . $value['id'] . '/');
                                        fopen(FCPATH . 'uploads/rel_handling_detail/' . $value['id'] . '/index.html', 'w');
                                    }
                                    $_FILES['detail']['name'][$key]['file'] = time() . '_' . preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['detail']['name'][$key]['file']));
                                    if (is_uploaded_file($_FILES['detail']['tmp_name'][$key]['file'])) {
                                        $typeFile = $_FILES['detail']['type'][$key]['file'];
                                        $source_path = $_FILES['detail']['tmp_name'][$key]['file'];
                                        $target_path = FCPATH . 'uploads/rel_handling_detail/' . $value['id'] . '/' . $_FILES['detail']['name'][$key]['file'];
                                        $filename = 'uploads/rel_handling_detail/' . $value['id'] . '/' . $_FILES['detail']['name'][$key]['file'];
                                        if (move_uploaded_file($source_path, $target_path)) {
                                            $this->db->where('id', $value['id']);
                                            $this->db->update('tbl_cleaning_detail', [
                                                'img' => $filename
                                            ]);
                                            if (!empty($cleaning_detail->img)) {
                                                if (file_exists($cleaning_detail->img)) {
                                                    unlink($cleaning_detail->img);
                                                }
                                            }
                                        }
                                    }
                                }
                            } else {
                                $arrayDetail[$key] = [
                                    'id_cleaning' => $id,
                                    'name' => $value['name'],
                                    'note' => $value['note'],
                                    'date_create' => date('Y-m-d H:i:s'),
                                    'create_by' => get_staff_user_id(),
                                ];
                            }
                        }

                        if (!empty($arrayDetail)) {
                            foreach ($arrayDetail as $key => $value) {
                                $ssDetail = $this->db->insert('tbl_cleaning_detail', $arrayDetail[$key]);
                                $id_detail = $this->db->insert_id();
                                if (!empty($id_detail)) {
                                    $arrayDetailNotDelete[] = $id_detail;
                                    if (!empty($_FILES['detail']['name'][$key]['file'])) {
                                        if (!file_exists(FCPATH . 'uploads/rel_handling_detail/' . $id_detail . '/')) {
                                            mkdir(FCPATH . 'uploads/rel_handling_detail/' . $id_detail . '/');
                                            fopen(FCPATH . 'uploads/rel_handling_detail/' . $id_detail . '/index.html', 'w');
                                        }
                                        $_FILES['detail']['name'][$key]['file'] = time() . '_' . preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['detail']['name'][$key]['file']));

                                        if (is_uploaded_file($_FILES['detail']['tmp_name'][$key]['file'])) {
                                            $typeFile = $_FILES['detail']['type'][$key]['file'];
                                            $source_path = $_FILES['detail']['tmp_name'][$key]['file'];
                                            $target_path = FCPATH . 'uploads/rel_handling_detail/' . $id_detail . '/' . $_FILES['detail']['name'][$key]['file'];
                                            $filename = 'uploads/rel_handling_detail/' . $id_detail . '/' . $_FILES['detail']['name'][$key]['file'];
                                            if (move_uploaded_file($source_path, $target_path)) {
                                                $this->db->where('id', $id_detail);
                                                $this->db->update('tbl_cleaning_detail', [
                                                    'img' => $filename
                                                ]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $this->db->select('GROUP_CONCAT(id) as list_id');
                    if (!empty($arrayDetailNotDelete)) {
                        $this->db->where_not_in('id', $arrayDetailNotDelete);
                    }
                    $this->db->where('id_cleaning', $id);
                    $list_id_delete_detail = $this->db->get('tbl_cleaning_detail')->row('list_id');
                    if (!empty($list_id_delete_detail)) {
                        $list_id_delete_detail = explode(',', $list_id_delete_detail);
                        $this->db->where('rel_type', 'rel_handling_detail');
                        $this->db->where_in('rel_id', $list_id_delete_detail);
                        $_files = $this->db->get('tblfiles')->result_array();

                        $this->db->where('rel_type', 'rel_handling_detail');
                        $this->db->where_in('rel_id', $list_id_delete_detail);
                        $this->db->delete('tblfiles');
                        if (!empty($_files)) {
                            foreach ($_files as $key => $value) {
                                $linkUn = FCPATH . $value['file_name'];
                                if (file_exists($linkUn)) {
                                    unlink($linkUn);
                                }
                            }
                        }
                    }

                    if (!empty($arrayDetailNotDelete)) {
                        $this->db->where_not_in('id', $arrayDetailNotDelete);
                    }
                    $this->db->where('id_cleaning', $id);
                    $this->db->delete('tbl_cleaning_detail');


                    echo json_encode([
                        'result' => 1,
                        'message' => 'Cập nhật dữ liệu thành công'
                    ]);
                    return;
                }
                echo json_encode([
                    'result' => 0,
                    'message' => 'Cập nhật dữ liệu không thành công'
                ]);
                return;
            } else {
                if (!has_permission('pccc', '', 'create')) {
                    ajax_access_denied();
                }
                $code_group = _string($this->input->post('code_group'));
                $name = _string($this->input->post('name'));
                $data = $this->input->post();
                $detail = $this->input->post('detail');

                $success = $this->db->insert('tbl_cleaning', [
                    'code_group' => $code_group,
                    'name' => $name,
                    'ballot_type' => 1,
                    'note' => (!empty($data['note']) ? $data['note'] : ''),
                    'date_create' => date('Y-m-d H:i:s'),
                    'create_by' => get_staff_user_id()
                ]);
                if (!empty($success)) {
                    $id = $this->db->insert_id();
                    if (!empty($detail)) {
                        $arrayDetail = [];
                        foreach ($detail as $key => $value) {
                            $arrayDetail[] = [
                                'id_cleaning' => $id,
                                'name' => $value['name'],
                                'note' => !empty($value['note']) ? $value['note'] : '',
                                'date_create' => date('Y-m-d H:i:s'),
                                'create_by' => get_staff_user_id(),
                            ];
                        }
                        if (!empty($arrayDetail)) {
                            // $this->db->insert_batch('tbl_cleaning_detail', $arrayDetail);
                            foreach ($arrayDetail as $key => $value) {
                                $ssDetail = $this->db->insert('tbl_cleaning_detail', $arrayDetail[$key]);
                                $id_detail = $this->db->insert_id();
                                if (!empty($id_detail)) {
                                    $arrayDetailNotDelete[] = $id_detail;
                                    if (!empty($_FILES['detail']['name'][$key]['file'])) {
                                        if (!file_exists(FCPATH . 'uploads/rel_handling_detail/' . $id_detail . '/')) {
                                            mkdir(FCPATH . 'uploads/rel_handling_detail/' . $id_detail . '/');
                                            fopen(FCPATH . 'uploads/rel_handling_detail/' . $id_detail . '/index.html', 'w');
                                        }
                                        $_FILES['detail']['name'][$key]['file'] = time() . '_' . preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['detail']['name'][$key]['file']));

                                        if (is_uploaded_file($_FILES['detail']['tmp_name'][$key]['file'])) {
                                            $typeFile = $_FILES['detail']['type'][$key]['file'];
                                            $source_path = $_FILES['detail']['tmp_name'][$key]['file'];
                                            $target_path = FCPATH . 'uploads/rel_handling_detail/' . $id_detail . '/' . $_FILES['detail']['name'][$key]['file'];
                                            $filename = 'uploads/rel_handling_detail/' . $id_detail . '/' . $_FILES['detail']['name'][$key]['file'];
                                            if (move_uploaded_file($source_path, $target_path)) {
                                                $this->db->where('id', $id_detail);
                                                $this->db->update('tbl_cleaning_detail', [
                                                    'img' => $filename
                                                ]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    echo json_encode([
                        'result' => 1,
                        'message' => 'Thêm mới dữ liệu thành công'
                    ]);
                    return;
                }
                echo json_encode([
                    'result' => 0,
                    'message' => 'Thêm mới dữ liệu không thành công'
                ]);
                return;
            }

            echo json_encode($data);
            return;
        } else {
            $data['id'] = $id;
            $data['pccc'] = $pccc;
            if (!empty($id)) {
                $title = lang('Sửa Khu PCCC');
                $data['pccc']['detail'] = $this->db->get_where('tbl_cleaning_detail', ['id_cleaning' => $id])->result_array();
            } else {
                $title = lang('Thêm Khu PCCC');
            }
            $data['title'] = $title;
            $this->load->view('admin/categories_other/handling_pccc', $data);
        }
    }
    public function getpccc()
    {
        $hasEdit = has_permission('pccc', '', 'edit');
        $hasDelete = has_permission('pccc', '', 'delete');
        $aColumns = [
            'tbl_cleaning.id as id',
            'tbl_cleaning.code_group as code_group',
            'tbl_cleaning.name as name',
            'tbl_cleaning.note as note',
            '(SELECT GROUP_CONCAT(tbl_cleaning_detail.name SEPARATOR "|||") FROM tbl_cleaning_detail WHERE tbl_cleaning_detail.id_cleaning = tbl_cleaning.id) as detail',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_cleaning';
        $where        = ['AND ballot_type = 1'];
        $filter = [];

        $join = [];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];
            $edit = $hasEdit ? ('<a class="c_modal" href="' . base_url('admin/categories_other/handlingpccc/' . $id) . '"><i class="fa fa-edit"></i> ' . lang('Sửa') . '</a>') : '';
            $delete = $hasDelete ? ('<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
            <button href=\'' . base_url('admin/categories_other/deletepccc/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
            <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
        "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>') : '';

            $actions = '
        <div class="dropdown text-center">
            <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
            ' . lang('actions') . '
            <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                <li>' . $edit . '</li>
                <li class="not-outside">' . $delete . '</li>
            </ul>
        </div>';

            $row = [];
            $row[] = '<div class="text-center">
                    <div class="checkbox checkbox-info">
                        <input type="checkbox" name="id[]" id="check-item' . $aRow['id'] . '" value="' . $aRow['id'] . '">
                        <label for="check-item' . $aRow['id'] . '"></label>
                    </div>
                </div>';;
            $row[] = $aRow['code_group'];
            $row[] = $aRow['name'];
            $row[] = '<div class="text-left" style="white-space: break-spaces;">' . $aRow['note'] . '</div>';

            $viewData = '';
            if (!empty($aRow['detail'])) {
                $_data_detail = explode('|||', $aRow['detail']);
                foreach ($_data_detail as $kD => $vD) {
                    $viewData .= ($kD + 1) . '. ' . $vD . '<br/>';
                }
            }
            $_data_detail = '<div class="text-left" style="white-space: break-spaces;">' . $viewData . '</div>';
            $row[] = $_data_detail;
            $row[] = $actions;
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }
    public function remove_file_pccc($id = '')
    {
        if (!has_permission('pccc', '', 'edit')) {
            ajax_access_denied();
        }
        $this->db->where('id', $id);
        $this->db->where('rel_type', 'rel_handling_detail');
        $getFile = $this->db->get('tblfiles')->row();
        if (!empty($getFile)) {
            $linkUn = FCPATH . $getFile->file_name;
            if (file_exists($linkUn)) {
                unlink($linkUn);
            }

            $this->db->where('id', $id);
            $this->db->where('rel_type', 'rel_handling_detail');
            $success = $this->db->delete('tblfiles');
            if (!empty($success)) {
                echo json_encode([
                    'success' => true,
                    'alert_type' => 'success',
                    'message' => 'Xóa File thành công'
                ]);
                die();
            }
        }
        echo json_encode([
            'success' => false,
            'alert_type' => 'danger',
            'message' => 'Xóa File không thành công'
        ]);
        die();
    }
    public function deletepccc($id)
    {
        if (!has_permission('pccc', '', 'delete')) {
            ajax_access_denied();
        }
        $data = [];

        $this->db->where('tbl_cleaning.id', $id);
        $delete_cleaning = $this->db->get('tbl_cleaning')->row();


        if (!empty($delete_cleaning)) {
            $this->db->select('GROUP_CONCAT(id) as list_id');
            if (!empty($arrayDetailNotDelete)) {
                $this->db->where_not_in('id', $arrayDetailNotDelete);
            }
            $this->db->where('id_cleaning', $id);
            $list_id_delete_detail = $this->db->get('tbl_cleaning_detail')->row('list_id');
            if (!empty($list_id_delete_detail)) {
                $list_id_delete_detail = explode(',', $list_id_delete_detail);
                $this->db->where('rel_type', 'rel_handling_detail');
                $this->db->where_in('rel_id', $list_id_delete_detail);
                $_files = $this->db->get('tblfiles')->result_array();

                $this->db->where('rel_type', 'rel_handling_detail');
                $this->db->where_in('rel_id', $list_id_delete_detail);
                $this->db->delete('tblfiles');

                if (!empty($_files)) {
                    foreach ($_files as $key => $value) {
                        $linkUn = FCPATH . $value['file_name'];
                        unlink($linkUn);
                    }
                }
            }
            $this->db->where('tbl_cleaning_detail.id_cleaning', $id);
            $this->db->delete('tbl_cleaning_detail');

            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
        return;
    }
    public function modal_import_pccc()
    {
        if (!has_permission('pccc', '', 'create')) {
            ajax_access_denied();
        }

        $data['title'] = _l('Import Khu Vực PCCC');
        $this->load->view('admin/categories_other/modal_import_pccc', $data);
    }
    public function import_pccc()
    {
        if (!has_permission('pccc', '', 'create')) {
            echo json_encode([
                'success' => false,
                'alert_type' => 'danger',
                'message' => 'Bạn không có quyền Import',
            ]);
            die();
        }

        ob_end_clean();
        ini_set('max_execution_time', 800);
        $dataPost = $this->input->post();
        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $this->load->helper('security');
        $count = 0;
        $errors = '';
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
            $objPHPExcel = $objReader->load("$fullfile");

            $total_sheets = $objPHPExcel->getSheetCount();

            $allSheetName = $objPHPExcel->getSheetNames();
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow = $objWorksheet->getHighestRow();
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('G');
            $arraydata = [];
            $fields = $this->input->post('fields');
            for ($row = 3; $row <= $highestRow; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 3][$col] = $value;

                    if ($col == 6) {
                        $images = $objPHPExcel->getActiveSheet()->getDrawingCollection();
                        if (!empty($images)) {
                            foreach ($images as $key => $image) {
                                $imageCoordinates = $image->getCoordinates();
                                if ($imageCoordinates == 'G' . $row) {
                                    if ($image instanceof PHPExcel_Worksheet_MemoryDrawing) {
                                        ob_start();
                                        call_user_func($image->getRenderingFunction(), $image->getImageResource());
                                        $imageData = ob_get_contents();
                                        ob_end_clean();
                                        $imageName = $image->getIndexedFilename();
                                        $arraydata[$row - 3][$col] = [
                                            'content' => base64_encode($imageData),
                                            'nameFile' => time() . $imageName
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
            }


            $dataArray = [];
            $dataArrayDetail = [];
            $dataArrayKT = [];
            $keyCode = '';
            foreach ($arraydata as $key => $value) {
                if (empty($value[1])) {
                    $value[1] = $keyCode;
                } else {
                    $keyCode = $value[1];
                }

                $id = '';
                if (empty($dataArrayID[$keyCode]) && empty($dataArrayKT[$keyCode]) && !empty($keyCode)) {
                    $this->db->where('code_group', $keyCode);
                    $this->db->where('ballot_type', 1);
                    $kt_cleaning = $this->db->get('tbl_cleaning')->row();

                    if (!empty($kt_cleaning)) {
                        $id = $kt_cleaning->id;
                    }
                    $dataArrayKT[$keyCode] = true;
                }

                if (empty($dataArray[$keyCode])) {
                    $dataArray[$keyCode] = [
                        'id' => !empty($id) ? $id : '',
                        'code_group' => $value[1],
                        'name' => $value[2],
                        'note' => $value[3],
                    ];
                }

                $img = !empty($value[6]) ? $value[6] : NULL;
                $dataArrayDetail[$keyCode][] = [
                    'name' => $value[4],
                    'note' => $value[5],
                    'img' => $img,
                ];
            }

            $count_add = 0;
            $count_edit = 0;
            if (!empty($dataArray)) {
                foreach ($dataArray as $key => $value) {
                    if (empty($value['id'])) {
                        unset($value['id']);
                        $value['create_by'] = get_staff_user_id();
                        $value['ballot_type'] = 1;
                        $success = $this->db->insert('tbl_cleaning', $value);
                        if (!empty($success)) {
                            $count_add++;
                            $count++;
                            $id_cleaning = $this->db->insert_id();
                            foreach ($dataArrayDetail[$value['code_group']] as $k => $v) {
                                $v['create_by'] = get_staff_user_id();
                                $v['id_cleaning'] = $id_cleaning;
                                if (!empty($v['img'])) {
                                    if (!file_exists(FCPATH . 'uploads/rel_handling_detail/' . $id_cleaning . '/')) {
                                        mkdir(FCPATH . 'uploads/rel_handling_detail/' . $id_cleaning . '/');
                                        fopen(FCPATH . 'uploads/rel_handling_detail/' . $id_cleaning . '/index.html', 'w');
                                    }

                                    $dataImg = base64_decode($v['img']['content']);
                                    $nameFile = 'uploads/rel_handling_detail/' . $id_cleaning . '/' . $v['img']['nameFile'];
                                    file_put_contents('uploads/rel_handling_detail/' . $id_cleaning . '/' . $v['img']['nameFile'], $dataImg);
                                    $v['img'] = $nameFile;
                                } else {
                                    unset($v['img']);
                                }
                                $this->db->insert('tbl_cleaning_detail', $v);
                            }
                        }
                    } else {
                        $id_cleaning = $value['id'];
                        unset($value['id']);
                        $value['ballot_type'] = 1;
                        $this->db->where('id', $id_cleaning);
                        $success = $this->db->update('tbl_cleaning', $value);
                        if (!empty($success)) {
                            $count_edit++;
                            $count++;
                            foreach ($dataArrayDetail[$value['code_group']] as $k => $v) {
                                $v['create_by'] = get_staff_user_id();
                                $v['id_cleaning'] = $id_cleaning;

                                if (!empty($v['img'])) {
                                    if (!file_exists(FCPATH . 'uploads/rel_handling_detail/' . $id_cleaning . '/')) {
                                        mkdir(FCPATH . 'uploads/rel_handling_detail/' . $id_cleaning . '/');
                                        fopen(FCPATH . 'uploads/rel_handling_detail/' . $id_cleaning . '/index.html', 'w');
                                    }

                                    $dataImg = base64_decode($v['img']['content']);
                                    $nameFile = 'uploads/rel_handling_detail/' . $id_cleaning . '/' . $v['img']['nameFile'];
                                    file_put_contents('uploads/rel_handling_detail/' . $id_cleaning . '/' . $v['img']['nameFile'], $dataImg);
                                    $v['img'] = $nameFile;
                                } else {
                                    unset($v['img']);
                                }
                                $this->db->insert('tbl_cleaning_detail', $v);
                            }
                        }
                    }
                }
            }

            echo json_encode([
                'success' => true,
                'errors' => $errors,
                'alert_type' => 'success',
                'message' => 'Thêm mới {' . $count_add . '} và cập nhật {' . $count_edit . '} thành công ' . $count . ' khu vực vệ sinh 5S',
            ]);
            die();
        }
        echo json_encode([
            'success' => true,
            'errors' => $errors,
            'alert_type' => 'success',
            'message' => 'Import thành công ' . $count . ' dòng',
        ]);
        die();
    }
    //accreditation
    public function accreditation()
    {
        if (!has_permission('accreditation', '', 'view')) {
            access_denied();
        }
        $data = [];
        $data['title'] = lang('Khu Vực Test/Kiểm định');
        $data['status'] = 1;
        $this->load->view('admin/categories_other/accreditation', $data);
    }
    public function handlingaccreditation($id = 0)
    {
        $data = [];
        $accreditation = $id ? $this->categories_other_model->getCleaningById($id) : [];
        if ($this->input->post()) {
            $code_group = _string($this->input->post('code_group'));
            if (empty($id)) {
                // $this->form_validation->set_rules('code_group', lang("Mã khu vực"), 'required|is_unique[tbl_cleaning.code_group]'); 
                // Lấy giá trị của 'type' từ POST request
                $this->db->where('code_group', $code_group);
                $this->db->where('ballot_type', 2);
                $query = $this->db->get('tbl_cleaning');
                if ($query->num_rows() > 0) {
                    // $this->form_validation->set_message('check_unique_code_group_type', 'Mã khu vực đã tồn tại.');
                    echo json_encode([
                        'result' => 0,
                        'message' => 'Mã khu vực đã tồn tại'
                    ]);
                    return;
                }
            } else {
                if ($accreditation['code_group'] != $code_group) {
                    // $this->form_validation->set_rules('code_group', lang("Mã khu vực"), 'required|is_unique[tbl_cleaning.code_group]');
                    $this->db->where('code_group', $code_group);
                    $this->db->where('ballot_type', 2);
                    $query = $this->db->get('tbl_cleaning');
                    if ($query->num_rows() > 0) {
                        // $this->form_validation->set_message('check_unique_code_group_type', 'Mã khu vực đã tồn tại.');
                        echo json_encode([
                            'result' => 0,
                            'message' => 'Mã khu vực đã tồn tại'
                        ]);
                        return;
                    }
                }
            }

            $name = _string($this->input->post('name'));
            $data = $this->input->post();
            $detail = $this->input->post('detail');
            if (!empty($id)) {
                if (!has_permission('accreditation', '', 'edit')) {
                    ajax_access_denied();
                }

                $this->db->where('id', $id);
                $success = $this->db->update('tbl_cleaning', [
                    'code_group' => $code_group,
                    'name' => $name,
                    'ballot_type' => 2,
                    'note' => (!empty($data['note']) ? $data['note'] : ''),
                ]);
                if (!empty($success)) {
                    $arrayDetailNotDelete = [];
                    if (!empty($detail)) {
                        $arrayDetail = [];
                        if (!file_exists(FCPATH . 'uploads/rel_handling_detail/')) {
                            mkdir(FCPATH . 'uploads/rel_handling_detail/');
                            fopen(FCPATH . 'uploads/rel_handling_detail/index.html', 'w');
                        }
                        foreach ($detail as $key => $value) {
                            if (!empty($value['id'])) {
                                $this->db->where('id', $value['id']);
                                $this->db->where('id_cleaning', $id);
                                $cleaning_detail = $this->db->get('tbl_cleaning_detail')->row();

                                $this->db->where('id', $value['id']);
                                $this->db->where('id_cleaning', $id);
                                $this->db->update('tbl_cleaning_detail', [
                                    'name' => $value['name'],
                                    'note' => $value['note'],
                                ]);
                                $arrayDetailNotDelete[] = $value['id'];
                                if (!empty($_FILES['detail']['name'][$key]['file'])) {
                                    if (!file_exists(FCPATH . 'uploads/rel_handling_detail/' . $value['id'] . '/')) {
                                        mkdir(FCPATH . 'uploads/rel_handling_detail/' . $value['id'] . '/');
                                        fopen(FCPATH . 'uploads/rel_handling_detail/' . $value['id'] . '/index.html', 'w');
                                    }
                                    $_FILES['detail']['name'][$key]['file'] = time() . '_' . preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['detail']['name'][$key]['file']));
                                    if (is_uploaded_file($_FILES['detail']['tmp_name'][$key]['file'])) {
                                        $typeFile = $_FILES['detail']['type'][$key]['file'];
                                        $source_path = $_FILES['detail']['tmp_name'][$key]['file'];
                                        $target_path = FCPATH . 'uploads/rel_handling_detail/' . $value['id'] . '/' . $_FILES['detail']['name'][$key]['file'];
                                        $filename = 'uploads/rel_handling_detail/' . $value['id'] . '/' . $_FILES['detail']['name'][$key]['file'];
                                        if (move_uploaded_file($source_path, $target_path)) {
                                            $this->db->where('id', $value['id']);
                                            $this->db->update('tbl_cleaning_detail', [
                                                'img' => $filename
                                            ]);
                                            if (!empty($cleaning_detail->img)) {
                                                if (file_exists($cleaning_detail->img)) {
                                                    unlink($cleaning_detail->img);
                                                }
                                            }
                                        }
                                    }
                                }
                            } else {
                                $arrayDetail[$key] = [
                                    'id_cleaning' => $id,
                                    'name' => $value['name'],
                                    'note' => $value['note'],
                                    'date_create' => date('Y-m-d H:i:s'),
                                    'create_by' => get_staff_user_id(),
                                ];
                            }
                        }

                        if (!empty($arrayDetail)) {
                            foreach ($arrayDetail as $key => $value) {
                                $ssDetail = $this->db->insert('tbl_cleaning_detail', $arrayDetail[$key]);
                                $id_detail = $this->db->insert_id();
                                if (!empty($id_detail)) {
                                    $arrayDetailNotDelete[] = $id_detail;
                                    if (!empty($_FILES['detail']['name'][$key]['file'])) {
                                        if (!file_exists(FCPATH . 'uploads/rel_handling_detail/' . $id_detail . '/')) {
                                            mkdir(FCPATH . 'uploads/rel_handling_detail/' . $id_detail . '/');
                                            fopen(FCPATH . 'uploads/rel_handling_detail/' . $id_detail . '/index.html', 'w');
                                        }
                                        $_FILES['detail']['name'][$key]['file'] = time() . '_' . preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['detail']['name'][$key]['file']));

                                        if (is_uploaded_file($_FILES['detail']['tmp_name'][$key]['file'])) {
                                            $typeFile = $_FILES['detail']['type'][$key]['file'];
                                            $source_path = $_FILES['detail']['tmp_name'][$key]['file'];
                                            $target_path = FCPATH . 'uploads/rel_handling_detail/' . $id_detail . '/' . $_FILES['detail']['name'][$key]['file'];
                                            $filename = 'uploads/rel_handling_detail/' . $id_detail . '/' . $_FILES['detail']['name'][$key]['file'];
                                            if (move_uploaded_file($source_path, $target_path)) {
                                                $this->db->where('id', $id_detail);
                                                $this->db->update('tbl_cleaning_detail', [
                                                    'img' => $filename
                                                ]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $this->db->select('GROUP_CONCAT(id) as list_id');
                    if (!empty($arrayDetailNotDelete)) {
                        $this->db->where_not_in('id', $arrayDetailNotDelete);
                    }
                    $this->db->where('id_cleaning', $id);
                    $list_id_delete_detail = $this->db->get('tbl_cleaning_detail')->row('list_id');
                    if (!empty($list_id_delete_detail)) {
                        $list_id_delete_detail = explode(',', $list_id_delete_detail);
                        $this->db->where('rel_type', 'rel_handling_detail');
                        $this->db->where_in('rel_id', $list_id_delete_detail);
                        $_files = $this->db->get('tblfiles')->result_array();

                        $this->db->where('rel_type', 'rel_handling_detail');
                        $this->db->where_in('rel_id', $list_id_delete_detail);
                        $this->db->delete('tblfiles');
                        if (!empty($_files)) {
                            foreach ($_files as $key => $value) {
                                $linkUn = FCPATH . $value['file_name'];
                                if (file_exists($linkUn)) {
                                    unlink($linkUn);
                                }
                            }
                        }
                    }

                    if (!empty($arrayDetailNotDelete)) {
                        $this->db->where_not_in('id', $arrayDetailNotDelete);
                    }
                    $this->db->where('id_cleaning', $id);
                    $this->db->delete('tbl_cleaning_detail');


                    echo json_encode([
                        'result' => 1,
                        'message' => 'Cập nhật dữ liệu thành công'
                    ]);
                    return;
                }
                echo json_encode([
                    'result' => 0,
                    'message' => 'Cập nhật dữ liệu không thành công'
                ]);
                return;
            } else {
                if (!has_permission('accreditation', '', 'create')) {
                    ajax_access_denied();
                }
                $code_group = _string($this->input->post('code_group'));
                $name = _string($this->input->post('name'));
                $data = $this->input->post();
                $detail = $this->input->post('detail');

                $success = $this->db->insert('tbl_cleaning', [
                    'code_group' => $code_group,
                    'name' => $name,
                    'ballot_type' => 2,
                    'note' => (!empty($data['note']) ? $data['note'] : ''),
                    'date_create' => date('Y-m-d H:i:s'),
                    'create_by' => get_staff_user_id()
                ]);
                if (!empty($success)) {
                    $id = $this->db->insert_id();
                    if (!empty($detail)) {
                        $arrayDetail = [];
                        foreach ($detail as $key => $value) {
                            $arrayDetail[] = [
                                'id_cleaning' => $id,
                                'name' => $value['name'],
                                'note' => !empty($value['note']) ? $value['note'] : '',
                                'date_create' => date('Y-m-d H:i:s'),
                                'create_by' => get_staff_user_id(),
                            ];
                        }
                        if (!empty($arrayDetail)) {
                            // $this->db->insert_batch('tbl_cleaning_detail', $arrayDetail);
                            foreach ($arrayDetail as $key => $value) {
                                $ssDetail = $this->db->insert('tbl_cleaning_detail', $arrayDetail[$key]);
                                $id_detail = $this->db->insert_id();
                                if (!empty($id_detail)) {
                                    $arrayDetailNotDelete[] = $id_detail;
                                    if (!empty($_FILES['detail']['name'][$key]['file'])) {
                                        if (!file_exists(FCPATH . 'uploads/rel_handling_detail/' . $id_detail . '/')) {
                                            mkdir(FCPATH . 'uploads/rel_handling_detail/' . $id_detail . '/');
                                            fopen(FCPATH . 'uploads/rel_handling_detail/' . $id_detail . '/index.html', 'w');
                                        }
                                        $_FILES['detail']['name'][$key]['file'] = time() . '_' . preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['detail']['name'][$key]['file']));

                                        if (is_uploaded_file($_FILES['detail']['tmp_name'][$key]['file'])) {
                                            $typeFile = $_FILES['detail']['type'][$key]['file'];
                                            $source_path = $_FILES['detail']['tmp_name'][$key]['file'];
                                            $target_path = FCPATH . 'uploads/rel_handling_detail/' . $id_detail . '/' . $_FILES['detail']['name'][$key]['file'];
                                            $filename = 'uploads/rel_handling_detail/' . $id_detail . '/' . $_FILES['detail']['name'][$key]['file'];
                                            if (move_uploaded_file($source_path, $target_path)) {
                                                $this->db->where('id', $id_detail);
                                                $this->db->update('tbl_cleaning_detail', [
                                                    'img' => $filename
                                                ]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    echo json_encode([
                        'result' => 1,
                        'message' => 'Thêm mới dữ liệu thành công'
                    ]);
                    return;
                }
                echo json_encode([
                    'result' => 0,
                    'message' => 'Thêm mới dữ liệu không thành công'
                ]);
                return;
            }

            echo json_encode($data);
            return;
        } else {
            $data['id'] = $id;
            $data['accreditation'] = $accreditation;
            if (!empty($id)) {
                $title = lang('Sửa Khu Test/Kiểm định');
                $data['accreditation']['detail'] = $this->db->get_where('tbl_cleaning_detail', ['id_cleaning' => $id])->result_array();
            } else {
                $title = lang('Thêm Khu Test/Kiểm định');
            }
            $data['title'] = $title;
            $this->load->view('admin/categories_other/handling_accreditation', $data);
        }
    }
    public function getaccreditation()
    {
        $hasEdit = has_permission('accreditation', '', 'edit');
        $hasDelete = has_permission('accreditation', '', 'delete');
        $aColumns = [
            'tbl_cleaning.id as id',
            'tbl_cleaning.code_group as code_group',
            'tbl_cleaning.name as name',
            'tbl_cleaning.note as note',
            '(SELECT GROUP_CONCAT(tbl_cleaning_detail.name SEPARATOR "|||") FROM tbl_cleaning_detail WHERE tbl_cleaning_detail.id_cleaning = tbl_cleaning.id) as detail',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_cleaning';
        $where        = ['AND ballot_type = 2'];
        $filter = [];

        $join = [];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];
            $edit = $hasEdit ? ('<a class="c_modal" href="' . base_url('admin/categories_other/handlingaccreditation/' . $id) . '"><i class="fa fa-edit"></i> ' . lang('Sửa') . '</a>') : '';
            $delete = $hasDelete ? ('<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
            <button href=\'' . base_url('admin/categories_other/deleteaccreditation/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
            <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
        "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>') : '';

            $actions = '
        <div class="dropdown text-center">
            <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
            ' . lang('actions') . '
            <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                <li>' . $edit . '</li>
                <li class="not-outside">' . $delete . '</li>
            </ul>
        </div>';

            $row = [];
            $row[] = '<div class="text-center">
                    <div class="checkbox checkbox-info">
                        <input type="checkbox" name="id[]" id="check-item' . $aRow['id'] . '" value="' . $aRow['id'] . '">
                        <label for="check-item' . $aRow['id'] . '"></label>
                    </div>
                </div>';;
            $row[] = $aRow['code_group'];
            $row[] = $aRow['name'];
            $row[] = '<div class="text-left" style="white-space: break-spaces;">' . $aRow['note'] . '</div>';

            $viewData = '';
            if (!empty($aRow['detail'])) {
                $_data_detail = explode('|||', $aRow['detail']);
                foreach ($_data_detail as $kD => $vD) {
                    $viewData .= ($kD + 1) . '. ' . $vD . '<br/>';
                }
            }
            $_data_detail = '<div class="text-left" style="white-space: break-spaces;">' . $viewData . '</div>';
            $row[] = $_data_detail;
            $row[] = $actions;
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }
    public function remove_file_accreditation($id = '')
    {
        if (!has_permission('accreditation', '', 'edit')) {
            ajax_access_denied();
        }
        $this->db->where('id', $id);
        $this->db->where('rel_type', 'rel_handling_detail');
        $getFile = $this->db->get('tblfiles')->row();
        if (!empty($getFile)) {
            $linkUn = FCPATH . $getFile->file_name;
            if (file_exists($linkUn)) {
                unlink($linkUn);
            }

            $this->db->where('id', $id);
            $this->db->where('rel_type', 'rel_handling_detail');
            $success = $this->db->delete('tblfiles');
            if (!empty($success)) {
                echo json_encode([
                    'success' => true,
                    'alert_type' => 'success',
                    'message' => 'Xóa File thành công'
                ]);
                die();
            }
        }
        echo json_encode([
            'success' => false,
            'alert_type' => 'danger',
            'message' => 'Xóa File không thành công'
        ]);
        die();
    }
    public function deleteaccreditation($id)
    {
        if (!has_permission('accreditation', '', 'delete')) {
            ajax_access_denied();
        }
        $data = [];

        $this->db->where('tbl_cleaning.id', $id);
        $delete_cleaning = $this->db->get('tbl_cleaning')->row();


        if (!empty($delete_cleaning)) {
            $this->db->select('GROUP_CONCAT(id) as list_id');
            if (!empty($arrayDetailNotDelete)) {
                $this->db->where_not_in('id', $arrayDetailNotDelete);
            }
            $this->db->where('id_cleaning', $id);
            $list_id_delete_detail = $this->db->get('tbl_cleaning_detail')->row('list_id');
            if (!empty($list_id_delete_detail)) {
                $list_id_delete_detail = explode(',', $list_id_delete_detail);
                $this->db->where('rel_type', 'rel_handling_detail');
                $this->db->where_in('rel_id', $list_id_delete_detail);
                $_files = $this->db->get('tblfiles')->result_array();

                $this->db->where('rel_type', 'rel_handling_detail');
                $this->db->where_in('rel_id', $list_id_delete_detail);
                $this->db->delete('tblfiles');

                if (!empty($_files)) {
                    foreach ($_files as $key => $value) {
                        $linkUn = FCPATH . $value['file_name'];
                        unlink($linkUn);
                    }
                }
            }
            $this->db->where('tbl_cleaning_detail.id_cleaning', $id);
            $this->db->delete('tbl_cleaning_detail');

            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
        return;
    }
    public function modal_import_accreditation()
    {
        if (!has_permission('accreditation', '', 'create')) {
            ajax_access_denied();
        }

        $data['title'] = _l('Import Khu Vực Test/Kiểm định');
        $this->load->view('admin/categories_other/modal_import_accreditation', $data);
    }
    public function import_accreditation()
    {
        if (!has_permission('accreditation', '', 'create')) {
            echo json_encode([
                'success' => false,
                'alert_type' => 'danger',
                'message' => 'Bạn không có quyền Import',
            ]);
            die();
        }

        ob_end_clean();
        ini_set('max_execution_time', 800);
        $dataPost = $this->input->post();
        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $this->load->helper('security');
        $count = 0;
        $errors = '';
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
            $objPHPExcel = $objReader->load("$fullfile");

            $total_sheets = $objPHPExcel->getSheetCount();

            $allSheetName = $objPHPExcel->getSheetNames();
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow = $objWorksheet->getHighestRow();
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('G');
            $arraydata = [];
            $fields = $this->input->post('fields');
            for ($row = 3; $row <= $highestRow; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 3][$col] = $value;

                    if ($col == 6) {
                        $images = $objPHPExcel->getActiveSheet()->getDrawingCollection();
                        if (!empty($images)) {
                            foreach ($images as $key => $image) {
                                $imageCoordinates = $image->getCoordinates();
                                if ($imageCoordinates == 'G' . $row) {
                                    if ($image instanceof PHPExcel_Worksheet_MemoryDrawing) {
                                        ob_start();
                                        call_user_func($image->getRenderingFunction(), $image->getImageResource());
                                        $imageData = ob_get_contents();
                                        ob_end_clean();
                                        $imageName = $image->getIndexedFilename();
                                        $arraydata[$row - 3][$col] = [
                                            'content' => base64_encode($imageData),
                                            'nameFile' => time() . $imageName
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
            }


            $dataArray = [];
            $dataArrayDetail = [];
            $dataArrayKT = [];
            $keyCode = '';
            foreach ($arraydata as $key => $value) {
                if (empty($value[1])) {
                    $value[1] = $keyCode;
                } else {
                    $keyCode = $value[1];
                }

                $id = '';
                if (empty($dataArrayID[$keyCode]) && empty($dataArrayKT[$keyCode]) && !empty($keyCode)) {
                    $this->db->where('code_group', $keyCode);
                    $this->db->where('ballot_type', 2);
                    $kt_cleaning = $this->db->get('tbl_cleaning')->row();

                    if (!empty($kt_cleaning)) {
                        $id = $kt_cleaning->id;
                    }
                    $dataArrayKT[$keyCode] = true;
                }

                if (empty($dataArray[$keyCode])) {
                    $dataArray[$keyCode] = [
                        'id' => !empty($id) ? $id : '',
                        'code_group' => $value[1],
                        'name' => $value[2],
                        'note' => $value[3],
                    ];
                }

                $img = !empty($value[6]) ? $value[6] : NULL;
                $dataArrayDetail[$keyCode][] = [
                    'name' => $value[4],
                    'note' => $value[5],
                    'img' => $img,
                ];
            }

            $count_add = 0;
            $count_edit = 0;
            if (!empty($dataArray)) {
                foreach ($dataArray as $key => $value) {
                    if (empty($value['id'])) {
                        unset($value['id']);
                        $value['create_by'] = get_staff_user_id();
                        $value['ballot_type'] = 2;
                        $success = $this->db->insert('tbl_cleaning', $value);
                        if (!empty($success)) {
                            $count_add++;
                            $count++;
                            $id_cleaning = $this->db->insert_id();
                            foreach ($dataArrayDetail[$value['code_group']] as $k => $v) {
                                $v['create_by'] = get_staff_user_id();
                                $v['id_cleaning'] = $id_cleaning;
                                if (!empty($v['img'])) {
                                    if (!file_exists(FCPATH . 'uploads/rel_handling_detail/' . $id_cleaning . '/')) {
                                        mkdir(FCPATH . 'uploads/rel_handling_detail/' . $id_cleaning . '/');
                                        fopen(FCPATH . 'uploads/rel_handling_detail/' . $id_cleaning . '/index.html', 'w');
                                    }

                                    $dataImg = base64_decode($v['img']['content']);
                                    $nameFile = 'uploads/rel_handling_detail/' . $id_cleaning . '/' . $v['img']['nameFile'];
                                    file_put_contents('uploads/rel_handling_detail/' . $id_cleaning . '/' . $v['img']['nameFile'], $dataImg);
                                    $v['img'] = $nameFile;
                                } else {
                                    unset($v['img']);
                                }
                                $this->db->insert('tbl_cleaning_detail', $v);
                            }
                        }
                    } else {
                        $id_cleaning = $value['id'];
                        unset($value['id']);
                        $value['ballot_type'] = 2;
                        $this->db->where('id', $id_cleaning);
                        $success = $this->db->update('tbl_cleaning', $value);
                        if (!empty($success)) {
                            $count_edit++;
                            $count++;
                            foreach ($dataArrayDetail[$value['code_group']] as $k => $v) {
                                $v['create_by'] = get_staff_user_id();
                                $v['id_cleaning'] = $id_cleaning;

                                if (!empty($v['img'])) {
                                    if (!file_exists(FCPATH . 'uploads/rel_handling_detail/' . $id_cleaning . '/')) {
                                        mkdir(FCPATH . 'uploads/rel_handling_detail/' . $id_cleaning . '/');
                                        fopen(FCPATH . 'uploads/rel_handling_detail/' . $id_cleaning . '/index.html', 'w');
                                    }

                                    $dataImg = base64_decode($v['img']['content']);
                                    $nameFile = 'uploads/rel_handling_detail/' . $id_cleaning . '/' . $v['img']['nameFile'];
                                    file_put_contents('uploads/rel_handling_detail/' . $id_cleaning . '/' . $v['img']['nameFile'], $dataImg);
                                    $v['img'] = $nameFile;
                                } else {
                                    unset($v['img']);
                                }
                                $this->db->insert('tbl_cleaning_detail', $v);
                            }
                        }
                    }
                }
            }

            echo json_encode([
                'success' => true,
                'errors' => $errors,
                'alert_type' => 'success',
                'message' => 'Thêm mới {' . $count_add . '} và cập nhật {' . $count_edit . '} thành công ' . $count . ' khu vực vệ sinh 5S',
            ]);
            die();
        }
        echo json_encode([
            'success' => true,
            'errors' => $errors,
            'alert_type' => 'success',
            'message' => 'Import thành công ' . $count . ' dòng',
        ]);
        die();
    }

    //cleaning
    public function cleaning_5s()
    {
        if (!has_permission('cleaning_5s', '', 'view')) {
            access_denied();
        }
        $data = [];
        $data['title'] = lang('Khu Vực vệ sinh ATLĐ-5S');
        $data['status'] = 1;
        $this->load->view('admin/categories_other/cleaning', $data);
    }

    public function modal_import_cleaning_5s()
    {
        if (!has_permission('cleaning_5s', '', 'create')) {
            ajax_access_denied();
        }

        $data['title'] = _l('Import Khu Vực Vệ Sinh ATLĐ - 5S');
        $this->load->view('admin/categories_other/modal_import_cleaning_5s', $data);
    }

    public function import_cleaning_5s()
    {
        if (!has_permission('cleaning_5s', '', 'create')) {
            echo json_encode([
                'success' => false,
                'alert_type' => 'danger',
                'message' => 'Bạn không có quyền Import',
            ]);
            die();
        }

        ob_end_clean();
        ini_set('max_execution_time', 800);
        $dataPost = $this->input->post();
        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $this->load->helper('security');
        $count = 0;
        $errors = '';
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
            $objPHPExcel = $objReader->load("$fullfile");

            $total_sheets = $objPHPExcel->getSheetCount();

            $allSheetName = $objPHPExcel->getSheetNames();
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow = $objWorksheet->getHighestRow();
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('I');
            $arraydata = [];
            $fields = $this->input->post('fields');
            for ($row = 3; $row <= $highestRow; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 3][$col] = $value;

                    if ($col == 8) {
                        $images = $objPHPExcel->getActiveSheet()->getDrawingCollection();
                        if (!empty($images)) {
                            foreach ($images as $key => $image) {
                                $imageCoordinates = $image->getCoordinates();
                                if ($imageCoordinates == 'I' . $row) {
                                    if ($image instanceof PHPExcel_Worksheet_MemoryDrawing) {
                                        ob_start();
                                        call_user_func($image->getRenderingFunction(), $image->getImageResource());
                                        $imageData = ob_get_contents();
                                        ob_end_clean();
                                        $imageName = $image->getIndexedFilename();
                                        $arraydata[$row - 3][$col] = [
                                            'content' => base64_encode($imageData),
                                            'nameFile' => time() . $imageName
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $dataArray = [];
            $dataArrayDetail = [];
            $dataArrayKT = [];
            $keyCode = '';
            foreach ($arraydata as $key => $value) {
                if (empty($value[3])) {
                    $value[3] = $keyCode;
                } else {
                    $keyCode = $value[3];
                }

                $id = '';
                if (empty($dataArrayID[$keyCode]) && empty($dataArrayKT[$keyCode]) && !empty($keyCode)) {
                    $this->db->where('code_group', $keyCode);
                    $kt_cleaning = $this->db->get('tbl_cleaning')->row();

                    if (!empty($kt_cleaning)) {
                        $id = $kt_cleaning->id;
                    }
                    $dataArrayKT[$keyCode] = true;
                }

                if (empty($dataArray[$keyCode])) {
                    $dataArray[$keyCode] = [
                        'id' => !empty($id) ? $id : '',
                        'id_code_group' => $value[1],
                        'id_code' => $value[2],
                        'code_group' => $value[3],
                        'name' => $value[4],
                        'note' => $value[5],
                    ];
                }

                $img = !empty($value[8]) ? $value[8] : NULL;
                $dataArrayDetail[$keyCode][] = [
                    'name' => $value[6],
                    'note' => $value[7],
                    'img' => $img,
                ];
            }
            $count_add = 0;
            $count_edit = 0;
            if (!empty($dataArray)) {
                foreach ($dataArray as $key => $value) {
                    $id_code_group = NULL;
                    $id_code = NULL;
                    if (!empty($value['id_code_group'])) {
                        $this->db->where('code', $value['id_code_group']);
                        $kt_code_group = $this->db->get('tbl_area_group')->row();
                        if (!empty($kt_code_group)) {
                            $id_code_group = $kt_code_group->id;
                        }
                    }
                    if (!empty($value['id_code'])) {
                        $this->db->where('code', $value['id_code']);
                        $kt_code = $this->db->get('tbl_area_code')->row();
                        if (!empty($kt_code)) {
                            $id_code = $kt_code->id;
                        }
                    }
                    $value['id_code_group'] = $id_code_group;
                    $value['id_code'] = $id_code;
                    if (empty($value['id'])) {
                        unset($value['id']);
                        $value['create_by'] = get_staff_user_id();
                        $success = $this->db->insert('tbl_cleaning', $value);
                        if (!empty($success)) {
                            $count_add++;
                            $count++;
                            $id_cleaning = $this->db->insert_id();
                            foreach ($dataArrayDetail[$value['code_group']] as $k => $v) {
                                $v['create_by'] = get_staff_user_id();
                                $v['id_cleaning'] = $id_cleaning;
                                if (!empty($v['img'])) {
                                    if (!file_exists(FCPATH . 'uploads/rel_handling_detail/' . $id_cleaning . '/')) {
                                        mkdir(FCPATH . 'uploads/rel_handling_detail/' . $id_cleaning . '/');
                                        fopen(FCPATH . 'uploads/rel_handling_detail/' . $id_cleaning . '/index.html', 'w');
                                    }

                                    $dataImg = base64_decode($v['img']['content']);
                                    $nameFile = 'uploads/rel_handling_detail/' . $id_cleaning . '/' . $v['img']['nameFile'];
                                    file_put_contents('uploads/rel_handling_detail/' . $id_cleaning . '/' . $v['img']['nameFile'], $dataImg);
                                    $v['img'] = $nameFile;
                                } else {
                                    unset($v['img']);
                                }
                                $this->db->insert('tbl_cleaning_detail', $v);
                            }
                        }
                    } else {
                        $id_cleaning = $value['id'];
                        unset($value['id']);
                        $this->db->where('id', $id_cleaning);
                        $success = $this->db->update('tbl_cleaning', $value);
                        if (!empty($success)) {
                            $count_edit++;
                            $count++;
                            foreach ($dataArrayDetail[$value['code_group']] as $k => $v) {
                                $v['create_by'] = get_staff_user_id();
                                $v['id_cleaning'] = $id_cleaning;

                                if (!empty($v['img'])) {
                                    if (!file_exists(FCPATH . 'uploads/rel_handling_detail/' . $id_cleaning . '/')) {
                                        mkdir(FCPATH . 'uploads/rel_handling_detail/' . $id_cleaning . '/');
                                        fopen(FCPATH . 'uploads/rel_handling_detail/' . $id_cleaning . '/index.html', 'w');
                                    }

                                    $dataImg = base64_decode($v['img']['content']);
                                    $nameFile = 'uploads/rel_handling_detail/' . $id_cleaning . '/' . $v['img']['nameFile'];
                                    file_put_contents('uploads/rel_handling_detail/' . $id_cleaning . '/' . $v['img']['nameFile'], $dataImg);
                                    $v['img'] = $nameFile;
                                } else {
                                    unset($v['img']);
                                }
                                $this->db->insert('tbl_cleaning_detail', $v);
                            }
                        }
                    }
                }
            }

            echo json_encode([
                'success' => true,
                'errors' => $errors,
                'alert_type' => 'success',
                'message' => 'Thêm mới {' . $count_add . '} và cập nhật {' . $count_edit . '} thành công ' . $count . ' khu vực vệ sinh 5S',
            ]);
            die();
        }
        echo json_encode([
            'success' => true,
            'errors' => $errors,
            'alert_type' => 'success',
            'message' => 'Import thành công ' . $count . ' dòng',
        ]);
        die();
    }


    public function handlingCleaning($id = 0)
    {
        $data = [];
        $cleaning = $id ? $this->categories_other_model->getCleaningById($id) : [];
        if ($this->input->post()) {
            $code_group = _string($this->input->post('code_group'));
            if (empty($id)) {
                // $this->form_validation->set_rules('code_group', lang("Mã khu vực"), 'required|is_unique[tbl_cleaning.code_group]');
                $this->db->where('code_group', $code_group);
                $this->db->where('ballot_type', 0);
                $query = $this->db->get('tbl_cleaning');
                if ($query->num_rows() > 0) {
                    // $this->form_validation->set_message('check_unique_code_group_type', 'Mã khu vực đã tồn tại.');
                    echo json_encode([
                        'result' => 0,
                        'message' => 'Mã khu vực đã tồn tại'
                    ]);
                    return;
                }
            } else {
                if ($cleaning['code_group'] != $code_group) {
                    // $this->form_validation->set_rules('code_group', lang("Mã khu vực"), 'required|is_unique[tbl_cleaning.code_group]');
                    $this->db->where('code_group', $code_group);
                    $this->db->where('ballot_type', 0);
                    $query = $this->db->get('tbl_cleaning');
                    if ($query->num_rows() > 0) {
                        // $this->form_validation->set_message('check_unique_code_group_type', 'Mã khu vực đã tồn tại.');
                        echo json_encode([
                            'result' => 0,
                            'message' => 'Mã khu vực đã tồn tại'
                        ]);
                        return;
                    }
                }
            }

            $name = _string($this->input->post('name'));
            $data = $this->input->post();
            $detail = $this->input->post('detail');
            $id_code_group = $this->input->post('id_code_group');
            $id_code = $this->input->post('id_code');
            if (!empty($id)) {
                if (!has_permission('cleaning_5s', '', 'edit')) {
                    ajax_access_denied();
                }

                $this->db->where('id', $id);
                $success = $this->db->update('tbl_cleaning', [
                    'code_group' => $code_group,
                    'name' => $name,
                    'id_code_group' => $id_code_group,
                    'id_code' => $id_code,
                    'note' => (!empty($data['note']) ? $data['note'] : ''),
                ]);
                if (!empty($success)) {
                    $arrayDetailNotDelete = [];
                    if (!empty($detail)) {
                        $arrayDetail = [];
                        if (!file_exists(FCPATH . 'uploads/rel_handling_detail/')) {
                            mkdir(FCPATH . 'uploads/rel_handling_detail/');
                            fopen(FCPATH . 'uploads/rel_handling_detail/index.html', 'w');
                        }
                        foreach ($detail as $key => $value) {
                            if (!empty($value['id'])) {
                                $this->db->where('id', $value['id']);
                                $this->db->where('id_cleaning', $id);
                                $cleaning_detail = $this->db->get('tbl_cleaning_detail')->row();

                                $this->db->where('id', $value['id']);
                                $this->db->where('id_cleaning', $id);
                                $this->db->update('tbl_cleaning_detail', [
                                    'name' => $value['name'],
                                    'note' => $value['note'],
                                ]);
                                $arrayDetailNotDelete[] = $value['id'];
                                if (!empty($_FILES['detail']['name'][$key]['file'])) {
                                    if (!file_exists(FCPATH . 'uploads/rel_handling_detail/' . $value['id'] . '/')) {
                                        mkdir(FCPATH . 'uploads/rel_handling_detail/' . $value['id'] . '/');
                                        fopen(FCPATH . 'uploads/rel_handling_detail/' . $value['id'] . '/index.html', 'w');
                                    }
                                    $_FILES['detail']['name'][$key]['file'] = time() . '_' . preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['detail']['name'][$key]['file']));
                                    if (is_uploaded_file($_FILES['detail']['tmp_name'][$key]['file'])) {
                                        $typeFile = $_FILES['detail']['type'][$key]['file'];
                                        $source_path = $_FILES['detail']['tmp_name'][$key]['file'];
                                        $target_path = FCPATH . 'uploads/rel_handling_detail/' . $value['id'] . '/' . $_FILES['detail']['name'][$key]['file'];
                                        $filename = 'uploads/rel_handling_detail/' . $value['id'] . '/' . $_FILES['detail']['name'][$key]['file'];
                                        if (move_uploaded_file($source_path, $target_path)) {
                                            $this->db->where('id', $value['id']);
                                            $this->db->update('tbl_cleaning_detail', [
                                                'img' => $filename
                                            ]);
                                            if (!empty($cleaning_detail->img)) {
                                                if (file_exists($cleaning_detail->img)) {
                                                    unlink($cleaning_detail->img);
                                                }
                                            }
                                        }
                                    }
                                }
                            } else {
                                $arrayDetail[$key] = [
                                    'id_cleaning' => $id,
                                    'name' => $value['name'],
                                    'note' => $value['note'],
                                    'date_create' => date('Y-m-d H:i:s'),
                                    'create_by' => get_staff_user_id(),
                                ];
                            }
                        }
                        if (!empty($arrayDetail)) {
                            foreach ($arrayDetail as $key => $value) {
                                $ssDetail = $this->db->insert('tbl_cleaning_detail', $arrayDetail[$key]);
                                if (!empty($ssDetail)) {
                                    $id_detail = $this->db->insert_id();
                                    $arrayDetailNotDelete[] = $id_detail;

                                    if (!empty($_FILES['detail']['name'][$key]['file'])) {
                                        if (!file_exists(FCPATH . 'uploads/rel_handling_detail/' . $id_detail . '/')) {
                                            mkdir(FCPATH . 'uploads/rel_handling_detail/' . $id_detail . '/');
                                            fopen(FCPATH . 'uploads/rel_handling_detail/' . $id_detail . '/index.html', 'w');
                                        }
                                        $_FILES['detail']['name'][$key]['file'] = time() . '_' . preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['detail']['name'][$key]['file']));

                                        if (is_uploaded_file($_FILES['detail']['tmp_name'][$key]['file'])) {
                                            $typeFile = $_FILES['detail']['type'][$key]['file'];
                                            $source_path = $_FILES['detail']['tmp_name'][$key]['file'];
                                            $target_path = FCPATH . 'uploads/rel_handling_detail/' . $id_detail . '/' . $_FILES['detail']['name'][$key]['file'];
                                            $filename = 'uploads/rel_handling_detail/' . $id_detail . '/' . $_FILES['detail']['name'][$key]['file'];
                                            if (move_uploaded_file($source_path, $target_path)) {
                                                $this->db->where('id', $id_detail);
                                                $this->db->update('tbl_cleaning_detail', [
                                                    'img' => $filename
                                                ]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $this->db->select('GROUP_CONCAT(id) as list_id');
                    if (!empty($arrayDetailNotDelete)) {
                        $this->db->where_not_in('id', $arrayDetailNotDelete);
                    }
                    $this->db->where('id_cleaning', $id);
                    $list_id_delete_detail = $this->db->get('tbl_cleaning_detail')->row('list_id');
                    if (!empty($list_id_delete_detail)) {
                        $list_id_delete_detail = explode(',', $list_id_delete_detail);
                        $this->db->where('rel_type', 'rel_handling_detail');
                        $this->db->where_in('rel_id', $list_id_delete_detail);
                        $_files = $this->db->get('tblfiles')->result_array();

                        $this->db->where('rel_type', 'rel_handling_detail');
                        $this->db->where_in('rel_id', $list_id_delete_detail);
                        $this->db->delete('tblfiles');
                        if (!empty($_files)) {
                            foreach ($_files as $key => $value) {
                                $linkUn = FCPATH . $value['file_name'];
                                if (file_exists($linkUn)) {
                                    unlink($linkUn);
                                }
                            }
                        }
                    }

                    if (!empty($arrayDetailNotDelete)) {
                        $this->db->where_not_in('id', $arrayDetailNotDelete);
                    }
                    $this->db->where('id_cleaning', $id);
                    $this->db->delete('tbl_cleaning_detail');


                    echo json_encode([
                        'result' => 1,
                        'message' => 'Cập nhật dữ liệu thành công'
                    ]);
                    return;
                }
                echo json_encode([
                    'result' => 0,
                    'message' => 'Cập nhật dữ liệu không thành công'
                ]);
                return;
            } else {
                if (!has_permission('cleaning_5s', '', 'create')) {
                    ajax_access_denied();
                }
                $code_group = _string($this->input->post('code_group'));
                $name = _string($this->input->post('name'));
                $data = $this->input->post();
                $detail = $this->input->post('detail');
                $id_code_group = $this->input->post('id_code_group');
                $id_code = $this->input->post('id_code');
                $success = $this->db->insert('tbl_cleaning', [
                    'code_group' => $code_group,
                    'name' => $name,
                    'id_code_group' => $id_code_group,
                    'id_code' => $id_code,
                    'note' => (!empty($data['note']) ? $data['note'] : ''),
                    'date_create' => date('Y-m-d H:i:s'),
                    'create_by' => get_staff_user_id()
                ]);
                if (!empty($success)) {
                    $id = $this->db->insert_id();
                    if (!empty($detail)) {
                        $arrayDetail = [];
                        foreach ($detail as $key => $value) {
                            $arrayDetail[] = [
                                'id_cleaning' => $id,
                                'name' => $value['name'],
                                'note' => !empty($value['note']) ? $value['note'] : '',
                                'date_create' => date('Y-m-d H:i:s'),
                                'create_by' => get_staff_user_id(),
                            ];
                        }
                        if (!empty($arrayDetail)) {
                            $this->db->insert_batch('tbl_cleaning_detail', $arrayDetail);
                        }
                    }
                    echo json_encode([
                        'result' => 1,
                        'message' => 'Thêm mới dữ liệu thành công'
                    ]);
                    return;
                }
                echo json_encode([
                    'result' => 0,
                    'message' => 'Thêm mới dữ liệu không thành công'
                ]);
                return;
            }

            echo json_encode($data);
            return;
        } else {
            $data['id'] = $id;
            $data['cleaning'] = $cleaning;
            if (!empty($id)) {
                $title = lang('Sửa Khu Vực Vệ Sinh ATLĐ-5S');
                $data['cleaning']['detail'] = $this->db->get_where('tbl_cleaning_detail', ['id_cleaning' => $id])->result_array();
            } else {
                $title = lang('Thêm Khu Vực Vệ Sinh ATLĐ-5S');
            }
            $data['title'] = $title;
            $data['dtCodeGroup'] = get_table_where('tbl_area_group');
            $data['dtCode'] = get_table_where('tbl_area_code');
            $this->load->view('admin/categories_other/handling_cleaning', $data);
        }
    }

    public function remove_file($id = '')
    {
        if (!has_permission('cleaning_5s', '', 'edit')) {
            ajax_access_denied();
        }
        $this->db->where('id', $id);
        $this->db->where('rel_type', 'rel_handling_detail');
        $getFile = $this->db->get('tblfiles')->row();
        if (!empty($getFile)) {
            $linkUn = FCPATH . $getFile->file_name;
            if (file_exists($linkUn)) {
                unlink($linkUn);
            }

            $this->db->where('id', $id);
            $this->db->where('rel_type', 'rel_handling_detail');
            $success = $this->db->delete('tblfiles');
            if (!empty($success)) {
                echo json_encode([
                    'success' => true,
                    'alert_type' => 'success',
                    'message' => 'Xóa File thành công'
                ]);
                die();
            }
        }
        echo json_encode([
            'success' => false,
            'alert_type' => 'danger',
            'message' => 'Xóa File không thành công'
        ]);
        die();
    }

    public function getCleaning()
    {
        $hasEdit = has_permission('cleaning_5s', '', 'edit');
        $hasDelete = has_permission('cleaning_5s', '', 'delete');
        $aColumns = [
            'tbl_cleaning.id as id',
            'tbl_area_group.name as namegroup',
            'tbl_area_code.name as namecode',
            'tbl_cleaning.code_group as code_group',
            'tbl_cleaning.name as name',
            'tbl_cleaning.note as note',
            '(SELECT GROUP_CONCAT(tbl_cleaning_detail.name SEPARATOR "|||") FROM tbl_cleaning_detail WHERE tbl_cleaning_detail.id_cleaning = tbl_cleaning.id) as detail',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_cleaning';
        $where        = ['AND ballot_type = 0'];
        $filter = [];

        $join = [
            'LEFT JOIN tbl_area_group ON tbl_area_group.id = tbl_cleaning.id_code_group',
            'LEFT JOIN tbl_area_code ON tbl_area_code.id = tbl_cleaning.id_code',
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];
            $edit = $hasEdit ? ('<a class="c_modal" href="' . base_url('admin/categories_other/handlingCleaning/' . $id) . '"><i class="fa fa-edit"></i> ' . lang('Sửa') . '</a>') : '';
            $delete = $hasDelete ? ('<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/categories_other/deleteCleaning/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>') : '';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';

            $row = [];
            $row[] = '<div class="text-center">
                        <div class="checkbox checkbox-info">
                            <input type="checkbox" name="id[]" id="check-item' . $aRow['id'] . '" value="' . $aRow['id'] . '">
                            <label for="check-item' . $aRow['id'] . '"></label>
                        </div>
                    </div>';;
            $row[] = $aRow['namegroup'];
            $row[] = $aRow['namecode'];
            $row[] = $aRow['code_group'];
            $row[] = $aRow['name'];
            $row[] = '<div class="text-left" style="white-space: break-spaces;">' . $aRow['note'] . '</div>';

            $viewData = '';
            if (!empty($aRow['detail'])) {
                $_data_detail = explode('|||', $aRow['detail']);
                foreach ($_data_detail as $kD => $vD) {
                    $viewData .= ($kD + 1) . '. ' . $vD . '<br/>';
                }
            }
            $_data_detail = '<div class="text-left" style="white-space: break-spaces;">' . $viewData . '</div>';
            $row[] = $_data_detail;
            $row[] = $actions;

            //			foreach ($aColumns as $k => $v) {
            //				$_data = $aRow[$v];
            //				if ($v == 'actions') {
            //					$_data = $actions;
            //				} else if ($v == 'id') {
            //					$_data = '<div class="text-center">
            //                        <div class="checkbox checkbox-info">
            //                            <input type="checkbox" name="id[]" id="check-item'.$id.'" value="'.$id.'">
            //                            <label for="check-item'.$id.'"></label>
            //                        </div>
            //                    </div>';
            //				} else if ($v == 'detail') {
            //					$_data = explode('<br>', $_data);
            //					$viewData = '';
            //					foreach($_data as $kD => $vD) {
            //						$viewData .= ($kD + 1) .'. '. $vD.'<br/>';
            //					}
            //					$_data = '<div class="text-left" style="white-space: break-spaces;">'.$viewData.'</div>';
            //				} else {
            //					$_data = '<div class="text-center">'.$_data.'</div>';
            //				}
            //
            //				$row[] = $_data;
            //			}

            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function deleteCleaning($id)
    {
        if (!has_permission('cleaning_5s', '', 'delete')) {
            ajax_access_denied();
        }
        $data = [];

        $this->db->where('tbl_cleaning.id', $id);
        $delete_cleaning = $this->db->get('tbl_cleaning')->row();


        if (!empty($delete_cleaning)) {
            $this->db->select('GROUP_CONCAT(id) as list_id');
            if (!empty($arrayDetailNotDelete)) {
                $this->db->where_not_in('id', $arrayDetailNotDelete);
            }
            $this->db->where('id_cleaning', $id);
            $list_id_delete_detail = $this->db->get('tbl_cleaning_detail')->row('list_id');
            if (!empty($list_id_delete_detail)) {
                $list_id_delete_detail = explode(',', $list_id_delete_detail);
                $this->db->where('rel_type', 'rel_handling_detail');
                $this->db->where_in('rel_id', $list_id_delete_detail);
                $_files = $this->db->get('tblfiles')->result_array();

                $this->db->where('rel_type', 'rel_handling_detail');
                $this->db->where_in('rel_id', $list_id_delete_detail);
                $this->db->delete('tblfiles');

                if (!empty($_files)) {
                    foreach ($_files as $key => $value) {
                        $linkUn = FCPATH . $value['file_name'];
                        unlink($linkUn);
                    }
                }
            }
            $this->db->where('tbl_cleaning_detail.id_cleaning', $id);
            $this->db->delete('tbl_cleaning_detail');

            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
        return;
    }



    //maintenance group
    public function maintenance_group()
    {
        $data = [];
        $data['title'] = lang('Nhóm bảo dưỡng');
        $data['status'] = 1;
        $this->load->view('admin/categories_other/maintenance_group', $data);
    }

    public function handlingMaintenanceGroup($id = 0, $status = 0)
    {
        $data = [];
        $maintenance_group = $id ? $this->categories_other_model->getMaintenanceGroupById($id) : [];
        if ($this->input->post('save')) {
            $this->form_validation->set_rules('code_group', lang("Mã nhóm BD"), 'required');
            $this->form_validation->set_rules('name', lang("Tên nhóm BD"), 'required');
            if ($this->form_validation->run() == true) {
                $code_group = _string($this->input->post('code_group'));
                $name = _string($this->input->post('name'));
                $detail = _string($this->input->post('detail'));
                $quantity = number_unformat($this->input->post('quantity'));

                $option = [
                    'code_group' => $code_group,
                    'name' => $name,
                    'detail' => $detail,
                    'quantity' => $quantity,
                    'status' => $status,
                ];

                if ($id) {
                    $ins = $this->categories_other_model->updateMaintenanceGroup($id, $option);
                    $_id = $id;
                } else {
                    $ins = $this->categories_other_model->insertMaintenanceGroup($option);
                    $_id = $ins;
                }

                if (!empty($_id)) {
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
        }

        $data['id'] = $id;
        $data['status'] = $status;
        $data['maintenance_group'] = $maintenance_group;
        $title = '';
        if ($status == 1) {
            $title = $id ? lang('Sửa nhóm bảo dưỡng') : lang('Thêm nhóm bảo dưỡng');
        }

        $data['title'] = $title;
        $this->load->view('admin/categories_other/handling_cleaning', $data);
    }

    public function getMaintenanceGroup()
    {
        $status_search = $this->input->post('status_search');

        $aColumns = [
            'tbl_maintenance_group.id as id',
            'tbl_maintenance_group.code_group as code_group',
            'tbl_maintenance_group.name_group as name_group',
            'tbl_maintenance_group.detail as detail',
            'tbl_maintenance_group.quantity as quantity',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_maintenance_group';
        $where        = [];
        $filter = [];

        $join = [];

        array_push($where, " AND tbl_maintenance_group.status = " . $status_search . "");
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];

            $edit = '<a class="tnh-modal" href="' . base_url('admin/categories_other/handlingMaintenanceGroup/' . $id . '/' . $status_search) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/categories_other/deleteMaintenanceGroup/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';

            $row = [];
            foreach ($aColumns as $k => $v) {
                $_data = $aRow[$v];
                if ($v == 'actions') {
                    $_data = $actions;
                } else if ($v == 'id') {
                    $_data = '<div class="text-center">
                        <div class="checkbox checkbox-info">
                            <input type="checkbox" name="id[]" id="check-item' . $id . '" value="' . $id . '">
                            <label for="check-item' . $id . '"></label>
                        </div>
                    </div>';
                } else if ($v == 'detail') {
                    $_data = '<div class="text-left" style="white-space: break-spaces;">' . $_data . '</div>';
                } else {
                    $_data = '<div class="text-center">' . $_data . '</div>';
                }

                $row[] = $_data;
            }


            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function deleteMaintenanceGroup($id)
    {
        $data = [];
        if ($this->categories_other_model->deleteMaintenanceGroup($id)) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }
    function inspection_criteria()
    {
        $data = [];
        $data['title'] = lang('Tiêu chí kiểm');
        $data['status'] = 1;
        $this->load->view('admin/categories_other/inspection_criteria', $data);
    }
    public function getInspection_criteria()
    {
        $status_search = $this->input->post('status_search');

        $aColumns = [
            'tbl_inspection_criteria.id as id',
            'tbl_inspection_criteria.name as name',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_inspection_criteria';
        $where        = [];
        $filter = [];

        $join = [];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];

            $edit = '<a class="tnh-modal" href="' . base_url('admin/categories_other/handlingInspection_criteria/' . $id . '/' . $status_search) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/categories_other/deleteInspection_criteria/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';

            $row = [];
            foreach ($aColumns as $k => $v) {
                $_data = $aRow[$v];
                if ($v == 'actions') {
                    $_data = $actions;
                } else if ($v == 'id') {
                    $_data = '<div class="text-center">
                        <div class="checkbox checkbox-info">
                            <input type="checkbox" name="id[]" id="check-item' . $id . '" value="' . $id . '">
                            <label for="check-item' . $id . '"></label>
                        </div>
                    </div>';
                } else {
                    $_data = '<div class="text-left">' . $_data . '</div>';
                }

                $row[] = $_data;
            }


            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }
    public function handlingInspection_criteria($id = 0, $status = 0)
    {
        $data = [];
        $inspection_criteria = $id ? $this->categories_other_model->getInspection_criteriaById($id) : [];
        if ($this->input->post('save')) {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($this->form_validation->run() == true) {
                $name = _string($this->input->post('name'));
                $option = [
                    'name' => $name,
                ];

                if ($id) {
                    $ins = $this->categories_other_model->updateInspection_criteria($id, $option);
                    $_id = $id;
                } else {
                    $ins = $this->categories_other_model->insertInspection_criteria($option);
                    $_id = $ins;
                }

                if (!empty($_id)) {
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
        }

        $data['id'] = $id;
        $data['status'] = $status;
        $data['inspection_criteria'] = $inspection_criteria;
        $title = '';
        $title = $id ? lang('Sửa tiêu chí kiểm') : lang('Thêm tiêu chí kiểm');

        $data['title'] = $title;
        $this->load->view('admin/categories_other/handling_inspection_criteria', $data);
    }
    //import export code
    public function relate()
    {
        $data = [];
        $data['title'] = lang('Liên quan đến');
        $data['status'] = 1;
        $this->load->view('admin/categories_other/relate', $data);
    }

    public function handlingRelate($id = 0, $status = 0)
    {
        $data = [];
        $relate = $id ? $this->categories_other_model->getRelateById($id) : [];
        if ($this->input->post('save')) {
            if (empty($relate) || (!empty($relate) && (mb_strtolower($relate['code']) != mb_strtolower($this->input->post('code'))))) {
                $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_relate.code]');
            }
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($this->form_validation->run() == true) {
                $code = _string($this->input->post('code'));
                $name = _string($this->input->post('name'));
                $option = [
                    'code' => $code,
                    'name' => $name,
                ];

                if ($id) {
                    $ins = $this->categories_other_model->updateRelate($id, $option);
                    $_id = $id;
                } else {
                    $ins = $this->categories_other_model->insertRelate($option);
                    $_id = $ins;
                }

                if (!empty($_id)) {
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
        }

        $data['id'] = $id;
        $data['status'] = $status;
        $data['relate'] = $relate;
        $title = '';
        $title = $id ? lang('Sửa liên quan đến') : lang('Thêm liên quan đến');

        $data['title'] = $title;
        $this->load->view('admin/categories_other/handling_relate', $data);
    }

    public function getRelate()
    {
        $status_search = $this->input->post('status_search');

        $aColumns = [
            'tbl_relate.id as id',
            'tbl_relate.id as stt',
            'tbl_relate.id as idd',
            'tbl_relate.code as code',
            'tbl_relate.name as name',
            '"" as items',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_relate';
        $where        = [];
        $where        = [
            'AND tbl_relate.parent_id = 0 and tbl_relate.type_show = 1'
        ];
        $filter = [];

        $join = [];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];

            // $edit = '<a class="tnh-modal" href="' . base_url('admin/categories_other/handlingRelate/'.$id.'/'.$status_search) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            // $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
            //     <button href=\'' . base_url('admin/categories_other/deleteRelate/'.$id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
            //     <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            // "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>';

            // $actions = '
            // <div class="dropdown text-center">
            //     <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
            //     ' . lang('actions') . '
            //     <span class="caret"></span>
            //     </button>
            //     <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
            //         <li>' . $edit . '</li>
            //         <li class="not-outside">' . $delete . '</li>
            //     </ul>
            // </div>';
            $addChild = '<a class="btn btn-success btn-icon pull-left tnh-modal active-modal" href="' . admin_url('categories_other/submit_relate/?parent_id=' . $id) . '" data-toggle="modal" data-tnh="modal" data-target="#myModal"><i class="fa fa-plus"></i></a>';
            $edit = '<a class="tnh-modal btn btn-default  btn-icon pull-left" href="' . base_url('admin/categories_other/handlingRelate/' . $id . '/' . $status_search) . '"><i class="fa fa-edit"></i></a>';
            $delete = '<button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
            <button href=\'' . base_url('admin/categories_other/deleteRelate/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
            <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove"></i></button>';
            $actions = '<div class="text-center">' . $addChild . $edit  . $delete . '</div>';
            $row = [];
            foreach ($aColumns as $k => $v) {
                $_data = $aRow[$v];
                if ($v == 'stt') {
                    $_data = '<div class="text-center">' . $start . '</div>';
                } else if ($v == 'idd') {
                    $_data = '<div class="text-center"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" class="rows-child fa fa-caret-right" data-id="' . $aRow['id'] . '"></a></div>';
                } else if ($v == 'items') {
                    $items = '';
                    $this->recursiveTableRelate($items, $id);
                    $_data = $items;
                }
                if ($v == 'actions') {
                    $_data = $actions;
                } else if ($v == 'id') {
                    $_data = '<div class="text-center">
                        <div class="checkbox checkbox-info">
                            <input type="checkbox" name="id[]" id="check-item' . $id . '" value="' . $id . '">
                            <label for="check-item' . $id . '"></label>
                        </div>
                    </div>';
                } else {
                    $_data = '<div class="text-center">' . $_data . '</div>';
                }

                $row[] = $_data;
            }


            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }
    public function recursiveTableRelate(&$output = null, $parent_id = 0, $indent = null, $stt = 1)
    {
        $ktSTT = $stt;
        $this->db->select('tbl_relate.*', false);
        $this->db->select('(
			SELECT 1
			FROM tbl_relate tbl_child
			WHERE tbl_child.parent_id = tbl_relate.id
			limit 1
		) as count_child');
        $this->db->from('tbl_relate');
        $this->db->where('tbl_relate.parent_id', $parent_id);
        $this->db->where('tbl_relate.type_show', 1);
        $this->db->order_by('tbl_relate.parent_id');
        $query = $this->db->get()->result_array();

        foreach ($query as $key => $item) {
            if ($item['parent_id'] == $parent_id) {

                $edit = '<a class="btn btn-info btn-icon pull-left tnh-modal active-modal" href="' . admin_url('categories_other/submit_relate/' . $item['id'] . '?parent_id=' . $item['parent_id']) . '" data-toggle="modal" data-tnh="modal" data-target="#myModal"><i class="fa fa-pencil"></i></a>';

                $delete = '<button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/categories_other/delete/' . $item['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
                "><i class="fa fa-remove"></i></button>';

                $rowChild = !empty($item['count_child']) ? '<a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" class="rows-child-2 fa fa-caret-right" data-id="' . $item['id'] . '" data-parent="' . $item['parent_id'] . '"></a>' : '';

                $outputChild = '';
                $outputHtml = '';
                if (!empty($item['count_child'])) {
                    $this->recursiveTableRelate($outputChild, $item['id'], NULL, ++$stt);
                    $outputHtml = base64_encode($outputChild);
                }
                $output .= '<tr>
                    <td>' . $indent . '' . $item['code'] . '</td>
                    <td>' . $item['name'] . '</td>
                    <td class="text-center">
						' . $edit . '
                        ' . $delete . '
                    </td>
                    <td>' . $outputHtml . '</td>
                </tr>';
                //                $this->recursiveTableRecommendedList($output, $item['id'], $indent . "|---", ++$stt);
            }
        }
        return $output;
    }
    // public function submit_relate($id = null)
    // {
    //     if ($this->input->post()) {
    //         $formData = $this->input->post();

    //         if (empty($id)) { // insert
    //             $this->db->insert('tbl_recommended_list', $formData);
    //             $submitId = $this->db->insert_id();
    //         } else {
    //             $this->db->update('tbl_recommended_list', $formData, ['id' => $id]);
    //             $submitId = $id;
    //         }

    //         $alert_type = false;
    //         $message = _l('Lưu thất bại');
    //         if ($submitId) {
    //             $alert_type = true;
    //             $message = _l('Lưu thành công');
    //         }
    //         echo json_encode(array(
    //             'isSuccess' => $alert_type,
    //             'message' => $message
    //         ));
    //     } else {
    //         $parent_id = $this->input->get('parent_id') ?? null;

    //         if (!empty($parent_id)) {
    //             $data['arrRecommendedList'] = get_table_where('tbl_recommended_list', ['id' => $parent_id]);
    //         } else {
    //             $data['arrRecommendedList'] = get_table_where('tbl_recommended_list', ['parent_id' => 0]);
    //         }

    //         $data['arrType'] = [];
    //         $arrType = getTypeCategoryTasks();
    //         foreach ($arrType as $code => $name) {
    //             $data['arrType'][] = ['code' => $code, 'name' => $name];
    //         }

    //         if (!empty($id)) {
    //             $data['value'] = get_table_where('tbl_recommended_list', ['id' => $id], '', 'row_array');
    //             $data['id'] = $id;
    //             if (!empty($data['value']['parent_id'])) {
    //                 $data['arrRecommendedList'] = get_table_where('tbl_recommended_list', ['id' => $data['value']['parent_id']]);
    //             }
    //         } else {
    //             $data['value']['parent_id'] = $parent_id;
    //         }

    //         $data['title'] = _l('tnh_recommended_list');

    //         $this->load->view('admin/categories_other/submit_relate', $data);
    //     }
    // }
    public function submit_relate($id = null)
    {
        $data = [];
        $relate = $id ? $this->categories_other_model->getRelateById($id) : [];
        $parent_id = $this->input->get('parent_id') ?? null;
        if ($this->input->post('save')) {
            $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_relate.code]');
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($this->form_validation->run() == true) {
                $code = _string($this->input->post('code'));
                $name = _string($this->input->post('name'));
                $parent_id = ($this->input->post('parent_id'));
                $option = [
                    'code' => $code,
                    'name' => $name,
                    'parent_id' => $parent_id,
                ];

                if ($id) {
                    $ins = $this->categories_other_model->updateRelate($id, $option);
                    $_id = $id;
                } else {
                    $ins = $this->categories_other_model->insertRelate($option);
                    $_id = $ins;
                }

                if (!empty($_id)) {
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
        }
        if (!empty($parent_id)) {
            $data['arrRelateList'] = get_table_where('tbl_relate', ['id' => $parent_id]);
        } else {
            $data['arrRelateList'] = get_table_where('tbl_relate', ['parent_id' => 0]);
        }

        $data['id'] = $id;
        $data['parent_id'] = $parent_id;
        $data['status'] = '';
        $data['relate'] = $relate;
        $title = '';
        $title = $id ? lang('Sửa liên quan đến') : lang('Thêm liên quan đến');

        $data['title'] = $title;
        $this->load->view('admin/categories_other/submit_relate', $data);
    }
    public function deleteRelate($id)
    {
        $data = [];
        $check = get_table_where('tblproduction_report', array('recommended_list_group_id' => $id), '', 'row_array');
        if (!empty($check)) {
            $data['result'] = 0;
            $data['message'] = lang('Đã dùng không thể xóa');
            echo json_encode($data);
            die;
        }
        
        // Relate
        if ($this->categories_other_model->deleteRelate($id)) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }
    public function deleteInspection_criteria($id)
    {
        $data = [];
        $check = get_table_where('tbl_setting_production_report_inspection_criteria', array('id_inspection_criteria' => $id), '', 'row_array');
        if (!empty($check)) {
            $data['result'] = 0;
            $data['message'] = lang('Đã dùng không thể xóa');
            echo json_encode($data);
            die;
        }
        // Relate
        $this->db->where('tbl_inspection_criteria.id', $id);
        if ($this->db->delete('tbl_inspection_criteria')) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }
    public function export_excel()
    {
        if (!has_permission('roles', '', 'export')) {
            access_denied();
        }
        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        $style_excel = style_excel();
        $cloumns_excel = cloumns_excel();
        $style_excel['Background_header_one'] = $style_excel['Background_header'];
        $style_excel['Background_header_one']['fill']['color']['rgb'] = '81dcf7';

        $style_excel['Background_header_two'] = $style_excel['Background_header'];
        $style_excel['Background_header_two']['fill']['color']['rgb'] = 'f79e83';

        $style_excel['Background_header_three'] = $style_excel['Background_header'];
        $style_excel['Background_header_three']['fill']['color']['rgb'] = '8ac78c';
        $style_excel['Background_header']['font']['bold'] = true;
        $style_excel['Background_header']['fill']['color']['rgb'] = 'fef7e2';


        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("H")->setWidth(35);

        $s = 0;
        $numberRow = 1;
        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'MÃ LIÊN QUAN')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
        $s++;

        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'TÊN LIÊN QUAN')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
        $s++;

        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'MÃ CHI TIẾT')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
        $s++;

        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'TÊN CHI TIẾT')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
        $s++;
        $numberRow++;


        $this->db->select('(
			SELECT 1
			FROM tbl_relate tbl_child
			WHERE tbl_child.parent_id = tbl_relate.id
			limit 1
		) as count_child');
        $this->db->select('tbl_relate.*');
        $this->db->where('parent_id', 0);
        $this->db->where('type_show', 1);
        $recommended_list_one = $this->db->get('tbl_relate')->result_array();

        foreach ($recommended_list_one as $key => $value) {
            $s = 0;
            $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", $value['code'])->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header_one']);
            $s++;
            $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", $value['name'])->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header_one']);
            $s++;
            $sDefault = $s;
            $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", '')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header_one']);
            $s++;
            $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", '')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header_one']);
            $s++;


            $numberRow++;
            if (!empty($value['count_child'])) {
                $this->db->select('(
					SELECT 1
					FROM tbl_relate tbl_child
					WHERE tbl_child.parent_id = tbl_relate.id
					limit 1
				) as count_child');
                $this->db->select('tbl_relate.*');
                $this->db->where('parent_id', $value['id']);
                $recommended_list_two = $this->db->get('tbl_relate')->result_array();

                foreach ($recommended_list_two as $ktwo => $vtwo) {
                    $sTwo = $sDefault;
                    for ($i = 0; $i < $sTwo; $i++) {
                        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", '')->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_center']);
                    }

                    $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$sTwo]$numberRow", $vtwo['code'])->getStyle("$cloumns_excel[$sTwo]$numberRow")->applyFromArray($style_excel['Background_header_two']);
                    $sTwo++;
                    $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$sTwo]$numberRow", $vtwo['name'])->getStyle("$cloumns_excel[$sTwo]$numberRow")->applyFromArray($style_excel['Background_header_two']);
                    $sTwo++;
                    $numberRow++;
                }
            }
        }



        $filename = lang('DS_lien_quan_den') . '.xls';
        $objPHPExcel->getActiveSheet()->freezePane('A1');

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    //danh mục quy trình
    function process_catalog()
    {
        $data = [];
        $data['title'] = lang('Danh mục quy trình');
        $data['status'] = 1;
        $this->load->view('admin/categories_other/process_catalog', $data);
    }

    public function getProcessCatalog()
    {
        $status_search = $this->input->post('status_search');

        $aColumns = [
            'tbl_process_catalog.id as id',
            'tbl_process_catalog.code as code',
            'tbl_process_catalog.name as name',
            'tbl_process_catalog.steps as steps',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_process_catalog';
        $where        = [];
        $filter = [];

        $join = [];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];

            $edit = '<a class="tnh-modal" href="' . base_url('admin/categories_other/handlingProcessCatalog/' . $id . '/' . $status_search) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/categories_other/deleteProcessCatalog/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';

            $steps = !empty($aRow['steps']) ? json_decode($aRow['steps'], true) : null;

            $divSteps = '';
            if (!empty($steps)) {
                foreach ($steps as $kS => $vS) {
                    $divSteps.= '<div>'.$vS['content'].'</div>';
                }
            }

            $row = [];
            foreach ($aColumns as $k => $v) {
                $_data = $aRow[$v];
                if ($v == 'actions') {
                    $_data = $actions;
                } else if ($v == 'id') {
                    $_data = '<div class="text-center">
                        <div class="checkbox checkbox-info">
                            <input type="checkbox" name="id[]" id="check-item' . $id . '" value="' . $id . '">
                            <label for="check-item' . $id . '"></label>
                        </div>
                    </div>';
                } else if ($v == 'steps') {
                    $_data = $divSteps;
                } else {
                    $_data = '<div class="text-left">' . $_data . '</div>';
                }

                $row[] = $_data;
            }


            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function handlingProcessCatalog($id = 0, $status = 0)
    {
        $data = [];
        $process_catalog = $id ? $this->categories_other_model->getProcessCatalogById($id) : [];
        if ($this->input->post('save')) {
            if (empty($process_catalog) || (!empty($process_catalog) && (mb_strtolower($process_catalog['code']) != mb_strtolower($this->input->post('code'))))) {
                $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_process_catalog.code]');
            }
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($this->form_validation->run() == true) {
                $code = _string($this->input->post('code'));
                $name = _string($this->input->post('name'));
                $steps = $this->input->post('steps');
                $arrSteps = [];
                if (!empty($steps)) {
                    foreach ($steps as $key => $value) {
                        if (empty($value)) continue;
                        $arrSteps[] = [
                            'content' => $value['content']
                        ];
                    }
                }

                $option = [
                    'code' => $code,
                    'name' => $name,
                    'steps' => !empty($arrSteps) ? json_encode($arrSteps, JSON_UNESCAPED_UNICODE) : null,
                ];

                if ($id) {
                    $ins = $this->categories_other_model->updateProcessCatalog($id, $option);
                    $_id = $id;
                } else {
                    $ins = $this->categories_other_model->insertProcessCatalog($option);
                    $_id = $ins;
                }

                if (!empty($_id)) {
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
        }

        $data['id'] = $id;
        $data['status'] = $status;
        $data['process_catalog'] = $process_catalog;
        $title = '';
        $title = $id ? lang('Sửa danh mục quy trình') : lang('Thêm danh mục quy trình');

        $data['title'] = $title;
        $this->load->view('admin/categories_other/handling_process_catalog', $data);
    }

    public function deleteProcessCatalog($id)
    {
        $data = [];
        // Relate
        if ($this->categories_other_model->deleteProcessCatalog($id)) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }
}
