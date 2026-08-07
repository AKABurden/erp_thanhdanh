<?php
defined('BASEPATH') or exit('No direct script access allowed');

class In_and_out_of_work extends AdminController
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

        $this->preViewInandoutofwork = true;
        $this->preViewOwnInandoutofwork = true;
        $this->preAddInandoutofwork = true;
        $this->preEditInandoutofwork = true;
        $this->preApproveInandoutofwork = true;
        $this->preDeleteInandoutofwork = true;
    }

    public function index()
    {
        if (!$this->preViewInandoutofwork && !$this->preViewOwnInandoutofwork) {
            access_denied();
        }
        $data['title'] = lang('ch_in_and_out_of_work');
        $this->load->view('admin/in_and_out_of_work/index', $data);
    }

    public function detail($id = 0)
    {
        $data = [];
        $this->db->select('tbl_in_and_out_of_work.*');
        $this->db->from('tbl_in_and_out_of_work');
        $this->db->where('tbl_in_and_out_of_work.id', $id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()) {
            if (empty($id)) {
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_in_and_out_of_work.reference_no]');
            } else {
                if ($dtData['reference_no'] != $this->input->post('reference_no')) {
                    $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_in_and_out_of_work.reference_no]');
                }
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('id_staff', lang("id_staff"), 'required');
            $this->form_validation->set_rules('time_out', lang("time_out"), 'required');
            $this->form_validation->set_rules('time_in', lang("time_in"), 'required');
            if (empty($id)) {
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('in_and_out_of_work');
                    $date = to_sql_date($this->input->post('date'), true);
                    $time_out = to_sql_date($this->input->post('time_out'), true);
                    $time_in = to_sql_date($this->input->post('time_in'), true);
                    $id_staff = $this->input->post('id_staff');
                    $note_in_out = $this->input->post('note_in_out');
                    $phone = $this->input->post('phone');
                    $counter = $this->input->post('counter');
                    $items = [];
                    $totalmain = 0;
                    if (!empty($counter)) {
                        foreach ($counter as $key => $value) {
                            $detail_reference_no = $this->input->post('detail_reference_no')[$value];
                            $detail_items = $this->input->post('detail_items')[$value];
                            $detail_security = $this->input->post('detail_security')[$value];
                            $in_and_out_of_work_items_id = $this->input->post('in_and_out_of_work_items_id')[$value];
                            if (!empty($in_and_out_of_work_items_id)) {
                                $items[] = [
                                    'id' => $in_and_out_of_work_items_id,
                                    'detail_reference_no' => $detail_reference_no,
                                    'detail_items' => $detail_items,
                                    'detail_security' => $detail_security,
                                ];
                            } else {
                                $items[] = [
                                    'detail_reference_no' => $detail_reference_no,
                                    'detail_items' => $detail_items,
                                    'detail_security' => $detail_security,
                                ];
                            }
                        }
                    }
                    if (empty($items)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Không có chi tiết để thêm');
                        echo json_encode($data);
                        die();
                    }
                    $fields = [
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'time_out' => $time_out,
                        'time_in' => $time_in,
                        'id_staff' => $id_staff,
                        'note_in_out' => $note_in_out,
                        'phone' => $phone,
                        'staff_create' => get_staff_user_id(),
                        'date_create' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->insert('tbl_in_and_out_of_work', $fields);
                    $id = $this->db->insert_id();
                    if ($id) {
                        if (getReference('in_and_out_of_work') == $reference_no) {
                            updateReference('in_and_out_of_work');
                        }
                        if (!empty($items)) {
                            foreach ($items as $key => $value) {
                                $value['in_and_out_of_work_id'] = $id;
                                $this->db->insert('tbl_in_and_out_of_work_items', $value);
                            }
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'in_and_out_of_work',
                            'table_obj' => 'tbl_in_and_out_of_work',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới phiếu ra vào cổng') . ' [' . $reference_no . ']',
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
                    $reference_no = getReference('in_and_out_of_work');
                    $date = to_sql_date($this->input->post('date'), true);
                    $time_out = to_sql_date($this->input->post('time_out'), true);
                    $time_in = to_sql_date($this->input->post('time_in'), true);
                    $id_staff = $this->input->post('id_staff');
                    $note_in_out = $this->input->post('note_in_out');
                    $phone = $this->input->post('phone');
                    $counter = $this->input->post('counter');
                    $items = [];
                    $totalmain = 0;
                    if (!empty($counter)) {
                        foreach ($counter as $key => $value) {
                            $detail_reference_no = $this->input->post('detail_reference_no')[$value];
                            $detail_items = $this->input->post('detail_items')[$value];
                            $detail_security = $this->input->post('detail_security')[$value];
                            $in_and_out_of_work_items_id = $this->input->post('in_and_out_of_work_items_id')[$value];
                            if (!empty($in_and_out_of_work_items_id)) {
                                $items[] = [
                                    'id' => $in_and_out_of_work_items_id,
                                    'detail_reference_no' => $detail_reference_no,
                                    'detail_items' => $detail_items,
                                    'detail_security' => $detail_security,
                                ];
                            } else {
                                $items[] = [
                                    'detail_reference_no' => $detail_reference_no,
                                    'detail_items' => $detail_items,
                                    'detail_security' => $detail_security,
                                ];
                            }
                        }
                    }
                    if (empty($items)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Không có chi tiết để thêm');
                        echo json_encode($data);
                        die();
                    }
                    $fields = [
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'time_out' => $time_out,
                        'time_in' => $time_in,
                        'id_staff' => $id_staff,
                        'note_in_out' => $note_in_out,
                        'phone' => $phone,
                        'staff_update' => get_staff_user_id(),
                        'date_update' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->where('id', $id);
                    $success = $this->db->update('tbl_in_and_out_of_work', $fields);
                    if ($success) {
                        if (!empty($items)) {
                            $this->db->where('in_and_out_of_work_id', $id);
                            $this->db->delete('tbl_in_and_out_of_work_items');
                            foreach ($items as $key => $value) {
                                $value['in_and_out_of_work_id'] = $id;
                                $this->db->insert('tbl_in_and_out_of_work_items', $value);
                            }
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'in_and_out_of_work',
                            'table_obj' => 'tbl_in_and_out_of_work',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu ra vào cổng') . ' [' . $dtData['reference_no'] . ']',
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
                if (!$this->preAddInandoutofwork) {
                    accessDenied(true);
                }

                $data['title'] = lang('ch_add_in_and_out_of_work');
            } else {
                if (!$this->preEditInandoutofwork) {
                    accessDenied(true);
                }
                if (($dtData['status'] == 1)) {
                    refererModel(lang('Phiếu đã duyệt không thể sửa !'));
                }
                $this->db->select('
                    tbl_in_and_out_of_work_items.*
                ');
                $this->db->from('tbl_in_and_out_of_work_items');
                $this->db->where('tbl_in_and_out_of_work_items.in_and_out_of_work_id', $id);
                $dtItems = $this->db->get()->result_array();
                $data['dtData'] = $dtData;
                $data['dtItems'] = $dtItems;
                $data['title'] = lang('ch_edit_in_and_out_of_work');
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        $data['reference_no'] = getReference('in_and_out_of_work');
        $data['dtResult'] = get_table_where('tbl_result');
        $data['dtCategoryMaintenance'] = get_table_where('tbl_category_maintenance');
        $data['dtTypeMaintenance'] = get_table_where('tbl_type_maintenance');
        $data['dtDepartment'] = get_table_where('tbldepartments');
        $data['dtunits'] = get_table_where('tblunits');
        $data['dttaxes'] = get_table_where('tbltaxes');
        $this->load->view('admin/in_and_out_of_work/detail', $data);
    }

    public function getStaff($id = '')
    {
        $this->db->select('
            tblstaff.*,
            tblroles.name as name_roles,
            tbldepartments.name as name_departments,
        ');
        $this->db->from('tblstaff');
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'LEFT');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tblroles.departments_id', 'LEFT');
        $this->db->where('tblstaff.staffid', $id);
        $dtData = $this->db->get()->row_array();
        echo json_encode($dtData);
    }

    public function getInandoutofwork()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_in_and_out_of_work.id as id',
            'tbl_in_and_out_of_work.reference_no as reference_no',
            'tbl_in_and_out_of_work.date as date',
            'employees.code as code_staff',
            'CONCAT(employees.firstname," ",employees.lastname) as fullname',
            'tblroles.name as name_roles',
            'tbldepartments.name as name_departments',
            'tbl_in_and_out_of_work.note_in_out as note_in_out',
            'tbl_in_and_out_of_work.phone as phone',
            'tbl_in_and_out_of_work.time_out as time_out',
            'tbl_in_and_out_of_work.time_in as time_in',
            'tbl_in_and_out_of_work.status as status',
            'tbl_in_and_out_of_work.hundredth_completed as hundredth_completed',
            '(SELECT COUNT(*) FROM tbl_in_and_out_of_work_items WHERE tbl_in_and_out_of_work_items.in_and_out_of_work_id = tbl_in_and_out_of_work.id AND status = "yes") as total_yes',
            '(SELECT COUNT(*) FROM tbl_in_and_out_of_work_items WHERE tbl_in_and_out_of_work_items.in_and_out_of_work_id = tbl_in_and_out_of_work.id AND status = "no") as total_no',
            '(SELECT GROUP_CONCAT(tblproduction_report.reference_no SEPARATOR ", ") FROM tblproduction_report JOIN tbl_in_and_out_of_work_items ON tbl_in_and_out_of_work_items.id = tblproduction_report.in_and_out_of_work_item WHERE tbl_in_and_out_of_work_items.in_and_out_of_work_id = tbl_in_and_out_of_work.id) as report_refs'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_in_and_out_of_work';
        $where = [];

        $join = [
            'LEFT JOIN tblstaff employees ON employees.staffid = tbl_in_and_out_of_work.id_staff',
            'LEFT JOIN tblroles ON tblroles.roleid = employees.role',
            'LEFT JOIN  tbldepartments ON  tbldepartments.departmentid = tblroles.departments_id',
        ];

        if (!$this->preViewInandoutofwork) {
            array_push($where, 'AND tbl_in_and_out_of_work.staff_create =', get_staff_user_id());
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_in_and_out_of_work.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_in_and_out_of_work.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['status_staff'], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $object_type = '';
            if (!empty($aRow['object_type'])) {
                $object_type = _l('object_type_' . $aRow['object_type']) . '';
            }

            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/in_and_out_of_work/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['code_staff']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['fullname']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['name_roles']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['name_departments']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['note_in_out']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['phone']) . '</div>';
            $row[] = '<div class="text-left">' . _dt($aRow['time_out']) . '</div>';
            $row[] = '<div class="text-left">' . _dt($aRow['time_in']) . '</div>';

            // New Columns
            $progress = isset($aRow['hundredth_completed']) ? $aRow['hundredth_completed'] : 0;
            $row[] = '<div class="text-center">' . $progress . '%</div>';

            $row[] = '<div class="text-center"><span class="text-success">' . $aRow['total_yes'] . '</span> / <span class="text-danger">' . $aRow['total_no'] . '</span></div>';

            $row[] = '<div class="text-left">' . ($aRow['report_refs'] ? $aRow['report_refs'] : '') . '</div>';

            // $row[] = '<div class="text-right">' . ($aRow['status']) . '</div>';
            if ($aRow['status'] == 0) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 1)\' id=\'agree\' suggest_purchase_id=\'' . $aRow['id'] . '\' value=\'1\' class=\'btn btn-success\'>' . lang('tnh_agree') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-danger po">' . lang('Chưa duyệt') . '</span></div>';
            } else if ($aRow['status'] == 1) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 0)\' id=\'agree\' suggest_purchase_id=\'' . $aRow['id'] . '\' value=\'0\' class=\'btn btn-danger\'>' . lang('Hủy duyệt') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-success po">' . lang('Đã duyệt') . '</span></div>';
                $_data .= '<div style="margin-top: 5px"> Người duyệt: ' . get_staff_full_name($aRow['status_staff']) . '</div>';
            } else {
                $_data = '';
            }
            $row[] = $_data;
            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/in_and_out_of_work/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditInandoutofwork ? '<a class="tnh-modal" href="' . base_url('admin/in_and_out_of_work/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteInandoutofwork ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/in_and_out_of_work/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . ' ' . lang('phiếu') . '</a>' : '';
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $view . '</li>
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[] = '<div>' . $actions . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function view($id)
    {
        $data = [];
        $data['title'] = lang('ch_view_in_and_out_of_work');

        $this->db->select('
            tbl_in_and_out_of_work.*,
            tblroles.name as name_roles,
            tbldepartments.name as name_departments,
            CONCAT(employees.firstname," ",employees.lastname) as fullname,
        ');
        $this->db->from('tbl_in_and_out_of_work');
        $this->db->join('tblstaff employees', 'employees.staffid = tbl_in_and_out_of_work.id_staff', 'LEFT');
        $this->db->join('tblroles', 'tblroles.roleid = employees.role', 'LEFT');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tblroles.departments_id', 'LEFT');
        $this->db->where('tbl_in_and_out_of_work.id', $id);
        $dtData = $this->db->get()->row_array();

        $this->db->select('
                    tbl_in_and_out_of_work_items.*,
                ');
        $this->db->from('tbl_in_and_out_of_work_items');
        $this->db->where('tbl_in_and_out_of_work_items.in_and_out_of_work_id', $id);
        $dtItems = $this->db->get()->result_array();
        $data['dtData'] = $dtData;
        $data['dtItems'] = $dtItems;
        $this->load->view('admin/in_and_out_of_work/view', $data);
    }

    public function agree()
    {
        if (!$this->preApproveInandoutofwork) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }

        $data = [];
        $suggest_purchase_id = $this->input->post('suggest_purchase_id');
        $status = $this->input->post('status');

        $this->db->select('tbl_in_and_out_of_work.*');
        $this->db->from('tbl_in_and_out_of_work');
        $this->db->where('tbl_in_and_out_of_work.id', $suggest_purchase_id);
        $dtSuggestPurchase = $this->db->get()->row_array();
        if (empty($dtSuggestPurchase)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
        } else {
            if (($dtSuggestPurchase['status'] == $status)) {
                $data['result'] = 0;
                $data['message'] = lang('Trạng thái đã được cập nhật vui lòng làm mới danh sách');
                echo responseData($data);
                return;
            }

            $date_status = date('Y-m-d H:i:s');
            $staff_status = get_staff_user_id();

            $options = [
                'status' => $status,
                'status_date' => $date_status,
                'status_staff' => $staff_status,
            ];

            $this->db->where('id', $suggest_purchase_id);
            $up = $this->db->update('tbl_in_and_out_of_work', $options);
            if ($up) {

                insertActivityLog([
                    'type_parent_obj' => 'in_and_out_of_work',
                    'table_obj' => 'tbl_in_and_out_of_work',
                    'id_obj' => $suggest_purchase_id,
                    'name_obj' => $dtSuggestPurchase['reference_no'],
                    'content' => lang('Duyệt phiếu ra vào cổng') . ' [' . $dtSuggestPurchase['reference_no'] . ']',
                    'actions' => 'approved'
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

    public function delete($id)
    {
        if (!$this->preDeleteInandoutofwork) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $data = [];
        $this->db->select('tbl_in_and_out_of_work.*');
        $this->db->from('tbl_in_and_out_of_work');
        $this->db->where('tbl_in_and_out_of_work.id', $id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);
            die();
        }
        if (($dtData['status'] == 1)) {
            $data['result'] = 0;
            $data['message'] = lang('Đã duyệt không thể xóa');
            echo json_encode($data);
            die();
        }
        $this->db->where('id', $id);
        $success = $this->db->delete('tbl_in_and_out_of_work');
        if ($success) {

            $this->db->where('tbl_in_and_out_of_work_items.in_and_out_of_work_id', $id);
            $this->db->delete('tbl_in_and_out_of_work_items');

            insertActivityLog([
                'type_parent_obj' => 'in_and_out_of_work',
                'table_obj' => 'tbl_in_and_out_of_work',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu ra vào cổng') . ' [' . $dtData['reference_no'] . ']',
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
                tbl_in_and_out_of_work_items.id as id,
                tbl_in_and_out_of_work.reference_no as reference_no,
                tbl_in_and_out_of_work.date as date,
                employees.code as code_staff,
                CONCAT(employees.firstname," ",employees.lastname) as fullname,
                tblroles.name as name_roles,
                tbldepartments.name as name_departments,
                tbl_in_and_out_of_work.note_in_out as note_in_out,
                tbl_in_and_out_of_work.phone as phone,
                tbl_in_and_out_of_work.time_out as time_out,
                tbl_in_and_out_of_work.time_in as time_in,
                tbl_in_and_out_of_work.status_staff as status_staff,
                tbl_in_and_out_of_work.status as status,
                tbl_in_and_out_of_work_items.detail_reference_no as detail_reference_no,
                tbl_in_and_out_of_work_items.detail_items as detail_items,
                tbl_in_and_out_of_work_items.detail_security as detail_security,  
            ');
            $this->db->from('tbl_in_and_out_of_work');
            $this->db->join('tbl_in_and_out_of_work_items', 'tbl_in_and_out_of_work_items.in_and_out_of_work_id = tbl_in_and_out_of_work.id');
            $this->db->join('tblstaff employees', 'employees.staffid = tbl_in_and_out_of_work.id_staff', 'LEFT');
            $this->db->join('tblroles', 'tblroles.roleid = employees.role', 'LEFT');
            $this->db->join('tbldepartments', 'tbldepartments.departmentid = tblroles.departments_id', 'LEFT');

            if (!$this->preViewInandoutofwork) {
                $this->db->where('(tbl_in_and_out_of_work.staff_create = ' . get_staff_user_id() . ' OR tbl_in_and_out_of_work.id_staff = ' . get_staff_user_id() . ' )');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_in_and_out_of_work.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_in_and_out_of_work.date <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tbl_in_and_out_of_work.id desc');
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
                    'name' => 'Times New Roman'
                ),
            ]);
            $objPHPExcel->getActiveSheet()->setCellValue(
                'A1',
                ('Phiếu ra vào cổng - Mang hàng ra cổng')
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
            $objPHPExcel->getActiveSheet()->mergeCells('A1:Q1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow . '', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $sttRow . '', 'Mã Số Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow . '', 'Ngày Lập Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow . '', 'Mã NV');
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow . '', 'Tên Nhân Viên');
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $sttRow . '', 'Vị Trí');
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $sttRow . '', 'Chức Vụ')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $sttRow . '', 'Lý Do Ra Vào Cổng')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $sttRow . '', 'Số Điện Thoại Liên Hệ')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $sttRow . '', 'Chứng Từ Ra Vào Cổng')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $sttRow . '', 'Danh Mục Hàng Hóa Ra Cổng');
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $sttRow . '', 'Thời Gian Ra Cổng')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $sttRow . '', 'Thời Gian Vào Cổng')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $sttRow . '', 'Người Duyệt')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O' . $sttRow . '', 'Bảo Vệ Xác Nhận')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P' . $sttRow . '', 'QR')->getStyle("Q$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:P$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name' => 'Times New Roman'
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
            $this->load->library('ciqrcode');
            $rowBegin = $sttRow;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['reference_no']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", _dt($value['date']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", ($value['code_staff']))->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['fullname']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", ($value['name_roles']))->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $value['name_departments'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", $value['note_in_out'])->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $value['phone'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $value['detail_reference_no'])->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", ($value['detail_items']))->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", _dt($value['time_out']))->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", _dt($value['time_in']))->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", ($value['detail_security']))->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", (!empty($value['status']) ? get_staff_full_name($value['status_staff']) : ''))->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                    if (!empty($value['barcode'])) {
                        $code = $value['barcode'];
                    } else {
                        $code = 'in_and_out_of_work_items||' . $value['id'];
                        $this->db->where('id', $value['id']);
                        $this->db->update('tbl_in_and_out_of_work_items', ['barcode' => $code]);
                    }
                    $qr = vn_to_str(str_replace('||', '__', $code));
                    $folder = FCPATH . 'uploads/in_and_out_of_work/';
                    if (!file_exists($folder)) {
                        mkdir($folder);
                        fopen($folder . 'index.html', 'w');
                    }
                    if (!file_exists($folder . 'qrcode' . '/')) {
                        mkdir($folder . 'qrcode' . '/');
                        fopen($folder . 'qrcode' . '/' . 'index.html', 'w');
                    }
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
                        $objDrawing1->setWidth(80);
                        $objDrawing1->setHeight(53);
                        $objDrawing1->setOffsetX(3);
                        $objDrawing1->setOffsetY(2);
                        $objDrawing1->setCoordinates('P' . ($rowBegin));
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(42);
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", '')->getStyle("P$rowBegin")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:P$rowBegin")->applyFromArray([
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
                }
            }
            $filename = lang('phieu_ra_vao_cong') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(10);
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

    function update_detail_status()
    {
        $id = $this->input->post('id');
        $status = $this->input->post('status');

        $this->db->where('id', $id);
        $this->db->update('tbl_in_and_out_of_work_items', ['status' => $status]);
        // Cập nhật % hoàn thành cho phiếu ra vào cổng
        // Lấy in_and_out_of_work_id từ chi tiết
        $this->db->select('in_and_out_of_work_id');
        $this->db->from('tbl_in_and_out_of_work_items');
        $this->db->where('id', $id);
        $row = $this->db->get()->row();
        if ($row) {
            $in_and_out_of_work_id = $row->in_and_out_of_work_id;
            // Đếm tổng số chi tiết
            $total = $this->db->where('in_and_out_of_work_id', $in_and_out_of_work_id)->count_all_results('tbl_in_and_out_of_work_items');
            // Đếm số chi tiết đã hoàn thành (có status là yes hoặc no)
            $completed = $this->db->where('in_and_out_of_work_id', $in_and_out_of_work_id)
                ->where_in('status', ['yes', 'no'])
                ->count_all_results('tbl_in_and_out_of_work_items');
            $hundredth_completed = $total > 0 ? round($completed * 100 / $total, 2) : 0;
            $this->db->where('id', $in_and_out_of_work_id);
            $this->db->update('tbl_in_and_out_of_work', ['hundredth_completed' => $hundredth_completed]);
        }
        $data['result'] = 1;
        $data['message'] = lang('Cập nhật thành công');
        echo json_encode($data);
    }

//    public function import()
//    {
//        $data = [];
//        if (!empty($_FILES)){
//            ini_set('max_execution_time', 800);
//            require_once(APPPATH . 'third_party/PHPExcel/PHPExcel.php');
//
//            $tmpFile = $_FILES['file']['tmp_name'];
//            $ext = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
//
//            if (!in_array($ext, ['XLS', 'XLSX'])) {
//                echo json_encode(['success' => false, 'message' => 'File không hợp lệ']);
//                die;
//            }
//
//            $excel = PHPExcel_IOFactory::load($tmpFile);
//            $sheet = $excel->getActiveSheet();
//            $highestRow = $sheet->getHighestRow();
//
//            $priorityMap = [
//                'Thường' => '1',
//                'Cao'    => '2',
//                'Khẩn'   => '3'
//            ];
//
//            $statusMap = [
//                'Draft'     => 'draft',
//                'Pending'   => 'pending',
//                'Approved'  => 'approved',
//                'Rejected'  => 'rejected',
//                'Closed'    => 'closed'
//            ];
//
//            $count = 0;
//
//            // Dòng 1: header → bắt đầu từ dòng 2
//            for ($row = 2; $row <= $highestRow; $row++) {
//
//                $code = trim($sheet->getCell("A$row")->getValue()) ?? NULL;
//                $name = trim($sheet->getCell("B$row")->getValue()) ?? NULL;
//
//                $date = $sheet->getCell("C$row")->getValue();
//                if(is_numeric($date)) {
//                    $unix = ($date - 25569) * 86400;
//                    $date = date('Y-m-d', $unix);
//                }
//                else {
//                    $date = to_sql_date($sheet->getCell("C$row")->getValue(), true);
//                }
//
//
//                $reason = trim($sheet->getCell("D$row")->getValue()) ?? NULL;
//
//
//                /** ---------------- Nhân viên đề xuất ---------------- */
//                $id_employee = trim($sheet->getCell("E$row")->getValue());
//                $id_employee = $this->db
////                    ->where('CONCAT(firstname," ",lastname) = "'.$staffUsername.'"', false, false)
//                    ->where('code', $id_employee)
//                    ->get('tblstaff')
//                    ->row('staffid');
//                if (!$id_employee) continue;
//
//
//                $role_id = trim($sheet->getCell("F$row")->getValue());
//                $role_id = $this->db
//                    ->where('code_role', $role_id)
//                    ->get('tblroles')
//                    ->row('roleid');
//                if (!$role_id) continue;
//
//
//                $hiring_manager = trim($sheet->getCell("G$row")->getValue());
//                $hiring_manager = $this->db
////                    ->where('CONCAT(firstname," ",lastname) = "'.$staffUsername.'"', false, false)
//                    ->where('code', $hiring_manager)
//                    ->get('tblstaff')
//                    ->row('staffid');
//                if (!$hiring_manager) continue;
//
//                $role_level = trim($sheet->getCell("H$row")->getValue());
//                $role_level = $this->db
////                    ->where('CONCAT(firstname," ",lastname) = "'.$staffUsername.'"', false, false)
//                    ->where('code', $role_level)
//                    ->get('tbl_role_level')
//                    ->row('id');
//                if (!$role_level) continue;
//
//
//                $branch = trim($sheet->getCell("I$row")->getValue());
//                $branch = $this->db
////                    ->where('CONCAT(firstname," ",lastname) = "'.$staffUsername.'"', false, false)
//                    ->where('name', $branch)
//                    ->get('tblbranch')
//                    ->row('id');
//                if (!$branch) continue;
//
//                $priority = $priorityMap[trim($sheet->getCell("J$row")->getValue())] ?? '1';
//
//
//                $status = $statusMap[trim($sheet->getCell("K$row")->getValue())] ?? 'draft';
//
//
//
//                $workday = $sheet->getCell("L$row")->getValue();
//                if(is_numeric($workday)) {
//                    $unix = ($workday - 25569) * 86400;
//                    $workday = date('Y-m-d', $unix);
//                }
//                else {
//                    $workday = to_sql_date($sheet->getCell("L$row")->getValue(), true);
//                }
//
//                $deadline = $sheet->getCell("M$row")->getValue();
//                if(is_numeric($deadline)) {
//                    $unix = ($deadline - 25569) * 86400;
//                    $deadline = date('Y-m-d', $unix);
//                }
//                else {
//                    $deadline = to_sql_date($sheet->getCell("M$row")->getValue(), true);
//                }
//
//
//                $type_of_work = $this->type_of_work[strtolower(trim($sheet->getCell("N$row")->getValue()))]['id'] ?? 'fulltime';
//                $working_style = $this->working_style[strtolower(trim($sheet->getCell("O$row")->getValue()))]['id'] ?? 'fulltime';
//
//                $budget_code = trim($sheet->getCell("P$row")->getValue());
//                $budget_code = $this->db
//                    ->where('code', $budget_code)
//                    ->get('tblcosts')
//                    ->row('id');
//                if (!$budget_code) continue;
//                $budget_start = trim($sheet->getCell("Q$row")->getValue());
//                if(!empty($budget_start)) {
//                    $budget_start = number_format_data($budget_start, false);
//                }
//                $budget_end = trim($sheet->getCell("R$row")->getValue());
//                if(!empty($budget_end)) {
//                    $budget_end = number_format_data($budget_end, false);
//                }
//                $note = trim($sheet->getCell("S$row")->getValue());
//
//                $insertData = [
//                    'code'          => $code,
//                    'name'          => $name,
//                    'reason'          => $reason,
//                    'date'          => $date,
//                    'id_employee'   => $id_employee ?? 0,
//                    'hiring_manager'   => $hiring_manager ?? 0,
//                    'role_level'   => $role_level ?? 0,
//                    'role_id'         => $role_id ?? 0,
//                    'branch'         => $branch ?? 0,
//
//                    'priority'      => $priority,
//                    'status'        => $status,
//                    'workday'        => $workday,
//                    'deadline'        => $deadline,
//                    'type_of_work'        => $type_of_work,
//                    'working_style'        => $working_style,
//                    'budget_code'        => $budget_code,
//                    'budget_start'   => $budget_start ?? 0,
//                    'budget_end'  => $budget_end ?? 0,
//                    'note'          => $note,
//                ];
//
//                // tránh import trùng mã
//                if(!empty($code)) {
//                    if ($this->db->where('code', $code)->get('tbl_hr_requirements')->row()) {
//                        continue;
//                    }
//                }
//                $success = $this->db->insert('tbl_hr_requirements', $insertData);
//                if(!empty($success)) {
//                    $id = $this->db->insert_id();
//                    if (empty($code)) {
//                        $this->db->where('id', $id);
//                        $this->db->update('tbl_hr_requirements', [
//                            'code' => 'YCTD-' . str_pad($id, 6, '0', STR_PAD_LEFT)
//                        ]);
//                    }
//                }
//                $count++;
//            }
//
//            echo json_encode([
//                'success' => true,
//                'message' => 'Import thành công ' . $count . ' yêu cầu tuyển dụng'
//            ]);
//            die;
//        }
//        $data['title'] = _l('Import phiếu ra vào cổng');
//        $this->load->view('admin/in_and_out_of_work/import', $data);
//    }
}
    