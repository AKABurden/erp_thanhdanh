<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suggest_evaluate extends AdminController
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
        $this->type = 0;
        if (!empty($this->input->get('type'))) {
            $this->type = $this->input->get('type');
        }

        $this->preViewSuggestEvaluate = true;
        $this->preViewOwnSuggestEvaluate = true;
        $this->preAddSuggestEvaluate = true;
        $this->preEditSuggestEvaluate = true;
        $this->preApproveSuggestEvaluate = true;
        $this->preDeleteSuggestEvaluate = true;
    }

    public function index()
    {
        if (!$this->preViewSuggestEvaluate && !$this->preViewOwnSuggestEvaluate) {
            access_denied();
        }
        if ($this->type == 'customer') {
            $data['title'] = _l('dt_suggest_evaluate_customer');
        } elseif ($this->type == 'supplier') {
            $data['title'] = _l('dt_suggest_evaluate_supplier');
        } elseif ($this->type == 'quality') {
            $data['title'] = _l('Phiếu Yêu Cầu Kiểm Tra Chất Lượng');
        } elseif ($this->type == 'procedure') {
            $data['title'] = _l('Phiếu Yêu Đánh Giá Quy Trình');
        } elseif ($this->type == 'system') {
            $data['title'] = _l('Phiếu Yêu Đánh Giá Hệ Thống');
        } else {
            $data['title'] = _l('dt_suggest_evaluate');
        }
        $this->load->view('admin/suggest_evaluate/index', $data);
    }

    public function getSuggestEvaluates()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');
        $type = $this->input->post('type');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_suggest_evaluate.id as id',
            'tbl_suggest_evaluate.reference_no as reference_no',
            'tbl_suggest_evaluate.date as date',
            'tblbranch.name as name_branch',
            'tbl_suggest_evaluate.staff_evaluate as staff_evaluate',
            'tbl_category_evaluate.name as name_type_evaluate',
            'tbl_suggest_evaluate.status as status',
            'tbl_suggest_evaluate.created_by as created_by',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_evaluate';
        $where = [];
        $filter = [];

        $join = [
            'INNER JOIN tbl_category_evaluate ON tbl_category_evaluate.id = tbl_suggest_evaluate.type_evaluate_id',
            'INNER JOIN tblbranch ON tblbranch.id = tbl_suggest_evaluate.branch_id',
            'LEFT JOIN tblclients ON tblclients.userid = tbl_suggest_evaluate.object_id AND tbl_suggest_evaluate.object_type="customer"',
            'LEFT JOIN tblsuppliers ON tblsuppliers.id = tbl_suggest_evaluate.object_id AND tbl_suggest_evaluate.object_type="supplier"',
            'LEFT JOIN tbl_category_recommended ON tbl_category_recommended.name_table = "tbl_suggest_evaluate" AND tbl_category_recommended.type="' . $type . '"'
        ];

        if (!empty($type)) {
            array_push($where, 'AND tbl_suggest_evaluate.object_type = "' . $type . '"');
        }

        if (!$this->preViewSuggestEvaluate) {
            array_push($where, 'AND (tbl_suggest_evaluate.created_by = ' . get_staff_user_id() . ')');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_suggest_evaluate.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_suggest_evaluate.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_suggest_evaluate.date_status',
            'tbl_suggest_evaluate.staff_status',
            'tblclients.company as name_client',
            'tblsuppliers.company as name_supplier',
            'tbl_suggest_evaluate.object_type as object_type',
            'tbl_category_recommended.id as category_recommended_id',
            '(SELECT count(tbltasks.id) FROM tbltasks  LEFT JOIN tbl_category_recommended ON tbl_category_recommended.id = tbltasks.category_recommended_id  WHERE suggest_id = tbl_suggest_evaluate.id AND tbl_category_recommended.name_table="tbl_suggest_evaluate" AND tbl_category_recommended.type="' . $type . '") as countTask'
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_evaluate/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left" style="width: 110px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . ($aRow['name_branch']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . get_staff_full_name($aRow['staff_evaluate']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . ($aRow['name_type_evaluate']) . '</div>';
            $object = '';
            if ($aRow['object_type'] == 'customer') {
                $object = $aRow['name_client'];
            } elseif ($aRow['object_type'] == 'supplier') {
                $object = $aRow['name_supplier'];
            }
            $row[] = '<div class="text-left" style="width: 110px">' . ($object) . '</div>';
            if ($aRow['status'] == 0) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 1)\' id=\'agree\' suggest_repalce_id=\'' . $aRow['id'] . '\' value=\'1\' class=\'btn btn-success\'>' . lang('tnh_agree') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-danger po">' . lang('Chưa duyệt') . '</span></div>';
            } else if ($aRow['status'] == 1) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 0)\' id=\'agree\' suggest_repalce_id=\'' . $aRow['id'] . '\' value=\'0\' class=\'btn btn-danger\'>' . lang('Hủy duyệt') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-success po">' . lang('Đã duyệt') . '</span></div>';
                $_data .= '<div style="margin-top: 5px"> Người duyệt: ' . get_staff_full_name($aRow['staff_status']) . '</div>';
            } else {
                $_data = '';
            }
            $row[] = '<div class="text-left" style="width: 100px">' . $_data . '</div>';
            if (!has_permission('tasks', '', 'create') || $aRow['status'] == 0) {
                $_data = '';
            } else {
                $task = '<a class="btn btn-info btn-icon mbot5" onclick="new_task(\'' . admin_url('tasks/task?suggest_id=' . $aRow['id'] . '&category_recommended_id=' . $aRow['category_recommended_id']) . '\')">Tạo công việc</a>';
                if (!empty($aRow['countTask'])) {
                    $data_tasks = get_table_where('tbltasks', ['suggest_id' => $aRow['id'], 'category_recommended_id' => $aRow['category_recommended_id']], '', 'result_array', '', 'tbltasks.id,tbltasks.name');
                    $__data = '';
                    $_data = '<div class="dropdown" style="text-align: center;">
                        <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown">' . $aRow['countTask'] . ' Phiếu
                        </button>';
                    foreach ($data_tasks as $kk => $vv) {
                        $__data .= '<li><a href="' . admin_url('tasks/view') . $vv['id'] . '" class="display-block main-tasks-table-href-name mbot5" onclick="init_task_modal(' . $vv['id'] . '); return false;">' . $vv['name'] . '</a>';
                    }
                    $_data .= '<ul style="top:100%;bottom:unset;left:unset;right: 12%" class="dropdown-menu ch_foso">' . $__data;
                    $_data .= '</ul>';
                    $_data .= '</div>';
                    $task .= $_data;
                }
                $_data = $task;
            }
            $row[] = '<div class="text-left" style="min-width: 100px">' . $_data . '</div>';

            $row[] = '<div class="text-left" style="width: 130px">' . get_staff_full_name($aRow['created_by']) . '</div>';

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_evaluate/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditSuggestEvaluate ? '<a href="' . base_url('admin/suggest_evaluate/detail/' . $aRow['id'] . '?type=' . $type . '') . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteSuggestEvaluate ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/suggest_evaluate/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
            $row[] = '<div style="min-width: 120px">' . $actions . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function detail($id = 0)
    {
        $data = [];
        $this->db->select('tbl_suggest_evaluate.*');
        $this->db->from('tbl_suggest_evaluate');
        $this->db->where('tbl_suggest_evaluate.id', $id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()) {
            if (!empty($dtData) && $dtData['reference_no'] != $this->input->post('reference_no')) {
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_evaluate.reference_no]');
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
            $this->form_validation->set_rules('staff_evaluate', lang("Người đánh giá"), 'required');
            $this->form_validation->set_rules('type_evaluate_id', lang("Loại đánh giá"), 'required');
            if (empty($id)) {
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('suggest_evaluate');
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $type_evaluate_id = $this->input->post('type_evaluate_id');
                    $object_id = !empty($this->input->post('object_id')) ? $this->input->post('object_id') : 0;
                    $staff_evaluate = !empty($this->input->post('staff_evaluate')) ? $this->input->post('staff_evaluate') : 0;
                    $note = ($this->input->post('note'));
                    if ($type_evaluate_id == 1) {
                        $object_type = 'staff';
                    } elseif ($type_evaluate_id == 3) {
                        $object_type = 'customer';
                    } elseif ($type_evaluate_id == 4) {
                        $object_type = 'supplier';
                    } elseif ($type_evaluate_id == 5) {
                        $object_type = 'outsource';
                    } elseif ($type_evaluate_id == 6) {
                        $object_type = 'repair';
                    } elseif ($type_evaluate_id == 7) {
                        $object_type = 'product';
                    } elseif ($type_evaluate_id == 8) {
                        $object_type = 'materials';
                    } elseif ($type_evaluate_id == 11) {
                        $object_type = 'quality';
                    } elseif ($type_evaluate_id == 10) {
                        $object_type = 'system';
                    } elseif ($type_evaluate_id == 9) {
                        $object_type = 'procedure';
                    }
                    $counter = $this->input->post('counter');
                    $items = [];
                    if (!empty($counter)) {
                        foreach ($counter as $key => $value) {
                            $evaluate_id = $this->input->post('evaluate_id')[$value];
                            $category_evaluate_id = $this->input->post('category_evaluate_id')[$value];
                            if (empty($evaluate_id)) {
                                continue;
                            }

                            $result_id = !empty($this->input->post('result')[$value]) ? $this->input->post('result')[$value] : 0;
                            $actual_situation = ($this->input->post('actual_situation')[$value]);
                            $content = ($this->input->post('content')[$value]);
                            $standard = ($this->input->post('standard')[$value]);
                            $items[] = [
                                'evaluate_id' => $evaluate_id,
                                'category_evaluate_id' => $category_evaluate_id,
                                'content' => $content,
                                'actual_situation' => $actual_situation,
                                'result_id' => $result_id,
                                'standard' => $standard,
                            ];
                        }
                    }
                    if (empty($items)) {
                        $data['result'] = 0;
                        $data['message'] = lang('tnh_no_items');
                        echo json_encode($data);
                        die;
                    }
                    $fields = [
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'staff_evaluate' => $staff_evaluate,
                        'type_evaluate_id' => $type_evaluate_id,
                        'object_id' => $object_id,
                        'object_type' => $object_type,
                        'note' => $note,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->insert('tbl_suggest_evaluate', $fields);
                    $id = $this->db->insert_id();
                    if ($id) {
                        if (getReference('suggest_evaluate') == $reference_no) {
                            updateReference('suggest_evaluate');
                        }

                        foreach ($items as $key => $value) {
                            $value['suggest_plan_evaluate_id'] = $id;
                            $this->db->insert('tbl_suggest_evaluate_item', $value);
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_evaluate',
                            'table_obj' => 'tbl_suggest_evaluate',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu đánh giá') . ' [' . $reference_no . ']',
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
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $type_evaluate_id = $this->input->post('type_evaluate_id');
                    $object_id = !empty($this->input->post('object_id')) ? $this->input->post('object_id') : 0;
                    $staff_evaluate = !empty($this->input->post('staff_evaluate')) ? $this->input->post('staff_evaluate') : 0;
                    $time_finish = !empty($this->input->post('time_finish')) ? $this->input->post('time_finish') : null;
                    $note = ($this->input->post('note'));
                    $counter = $this->input->post('counter');
                    if ($type_evaluate_id == 1) {
                        $object_type = 'staff';
                    } elseif ($type_evaluate_id == 3) {
                        $object_type = 'customer';
                    } elseif ($type_evaluate_id == 4) {
                        $object_type = 'supplier';
                    } elseif ($type_evaluate_id == 5) {
                        $object_type = 'outsource';
                    } elseif ($type_evaluate_id == 6) {
                        $object_type = 'repair';
                    } elseif ($type_evaluate_id == 7) {
                        $object_type = 'product';
                    } elseif ($type_evaluate_id == 8) {
                        $object_type = 'materials';
                    }
                    $items = [];
                    if (!empty($counter)) {
                        foreach ($counter as $key => $value) {
                            $evaluate_id = $this->input->post('evaluate_id')[$value];
                            $category_evaluate_id = $this->input->post('category_evaluate_id')[$value];
                            if (empty($evaluate_id)) {
                                continue;
                            }

                            $suggest_plan_evaluate_item_id = !empty($this->input->post('suggest_plan_evaluate_item_id')[$value]) ? $this->input->post('suggest_plan_evaluate_item_id')[$value] : 0;
                            $result_id = !empty($this->input->post('result')[$value]) ? $this->input->post('result')[$value] : 0;
                            $actual_situation = ($this->input->post('actual_situation')[$value]);
                            $content = ($this->input->post('content')[$value]);
                            $standard = ($this->input->post('standard')[$value]);
                            $items[] = [
                                'id' => $suggest_plan_evaluate_item_id,
                                'evaluate_id' => $evaluate_id,
                                'category_evaluate_id' => $category_evaluate_id,
                                'content' => $content,
                                'actual_situation' => $actual_situation,
                                'result_id' => $result_id,
                                'standard' => $standard,
                            ];
                        }
                    }
                    if (empty($items)) {
                        $data['result'] = 0;
                        $data['message'] = lang('tnh_no_items');
                        echo json_encode($data);
                        die;
                    }
                    $fields = [
                        'date' => $date,
                        'staff_evaluate' => $staff_evaluate,
                        'type_evaluate_id' => $type_evaluate_id,
                        'object_id' => $object_id,
                        'object_type' => $object_type,
                        'note' => $note,
                        'branch_id' => $branch_id,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->where('id', $id);
                    $success = $this->db->update('tbl_suggest_evaluate', $fields);
                    if ($success) {
                        $this->db->where('suggest_plan_evaluate_id', $id);
                        $this->db->delete('tbl_suggest_evaluate_item');
                        foreach ($items as $key => $value) {
                            $value['suggest_plan_evaluate_id'] = $id;
                            $this->db->insert('tbl_suggest_evaluate_item', $value);
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_evaluate',
                            'table_obj' => 'tbl_suggest_evaluate',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu đánh giá') . ' [' . $dtData['reference_no'] . ']',
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
                if (!$this->preAddSuggestEvaluate) {
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_suggest_evaluate');
                $data['breadcrumb'] = [array('link' => base_url('admin/suggest_evaluate?type=' . $this->type . ''), 'page' => lang('dt_suggest_evaluate')), array('link' => '#', 'page' => lang('dt_add_suggest_evaluate'))];
            } else {
                if (!$this->preEditSuggestEvaluate) {
                    accessDenied(true);
                }

                if ($dtData['status'] == 1) {
                    set_alert('danger',  'Phiếu đã duyệt không thể sửa !');
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                $this->db->select('tbl_suggest_evaluate_item.*,tbl_category_evaluate_detail.name as name_category_evaluate');
                $this->db->from('tbl_suggest_evaluate_item');
                $this->db->join('tbl_category_evaluate_detail', 'tbl_category_evaluate_detail.id = tbl_suggest_evaluate_item.category_evaluate_id');
                $this->db->where('tbl_suggest_evaluate_item.suggest_plan_evaluate_id', $id);
                $dtItems = $this->db->get()->result_array();
                $items = [];
                if (!empty($dtItems)) {
                    foreach ($dtItems as $key => $value) {
                        $keyNew = $value['name_category_evaluate'];
                        $items[$keyNew]['name'] = $value['name_category_evaluate'];
                        $items[$keyNew]['id'] = $value['category_evaluate_id'];
                        $items[$keyNew]['child'][] = $value;
                    }
                }
                $items = array_values($items);
                $data['dtData'] = $dtData;
                $data['dtItems'] = $items;
                $data['title'] = lang('dt_edit_suggest_evaluates');
                $data['breadcrumb'] = [array('link' => base_url('admin/suggest_evaluate?type=' . $this->type . ''), 'page' => lang('dt_suggest_evaluate')), array('link' => '#', 'page' => lang('dt_edit_suggest_evaluates'))];
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        $data['reference_no'] = getReference('suggest_evaluate');
        $data['dtResult'] = get_table_where('tbl_result');
        $data['type'] = $this->type;
        $type_evaluate_id = 0;
        if ($this->type === 'customer') {
            $type_evaluate_id = 3;
        } elseif ($this->type === 'supplier') {
            $type_evaluate_id = 4;
        } elseif ($this->type === 'quality') {
            $type_evaluate_id = 11;
        } elseif ($this->type === 'procedure') {
            $type_evaluate_id = 9;
        } elseif ($this->type === 'system') {
            $type_evaluate_id = 10;
        }

        $data['type_evaluate_id'] = $type_evaluate_id;
        $data['dtTypeEvaluate'] = get_table_where('tbl_category_evaluate');
        $this->load->view('admin/suggest_evaluate/detail', $data);
    }

    public function view($id)
    {
        $data = [];
        $data['title'] = lang('dt_view_suggest_evaluate');

        $this->db->select('tbl_suggest_evaluate.*,tbl_category_evaluate.name as name_type_evaluate');
        $this->db->from('tbl_suggest_evaluate');
        $this->db->join('tbl_category_evaluate', 'tbl_category_evaluate.id = tbl_suggest_evaluate.type_evaluate_id');
        $this->db->where('tbl_suggest_evaluate.id', $id);
        $dtData = $this->db->get()->row_array();

        $this->db->select('tbl_suggest_evaluate_item.*,
            tbl_result.name as name_result,
            tbl_evaluate.code_evaluate as code_evaluate,
            tbl_evaluate.name_evaluate as name_evaluate,
            tbl_category_evaluate_detail.name as name_category_evaluate
        ');
        $this->db->from('tbl_suggest_evaluate_item');
        $this->db->join('tbl_result', 'tbl_result.id = tbl_suggest_evaluate_item.result_id', 'left');
        $this->db->join('tbl_evaluate', 'tbl_evaluate.id = tbl_suggest_evaluate_item.evaluate_id', 'left');
        $this->db->join('tbl_category_evaluate_detail', 'tbl_category_evaluate_detail.id = tbl_suggest_evaluate_item.category_evaluate_id');
        $this->db->where('tbl_suggest_evaluate_item.suggest_plan_evaluate_id', $id);
        $dtDataItems = $this->db->get()->result_array();
        $items = [];
        if (!empty($dtDataItems)) {
            foreach ($dtDataItems as $key => $value) {
                $keyNew = $value['name_category_evaluate'];
                $items[$keyNew]['name'] = $value['name_category_evaluate'];
                $items[$keyNew]['id'] = $value['category_evaluate_id'];
                $items[$keyNew]['child'][] = $value;
            }
        }
        $items = array_values($items);
        $data['dtData'] = $dtData;
        $data['dtDataItems'] = $items;
        $data['dtResult'] = get_table_where('tbl_result');
        $this->load->view('admin/suggest_evaluate/view', $data);
    }

    public function agree()
    {
        if (!$this->preApproveSuggestEvaluate) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }

        $data = [];
        $suggest_id = $this->input->post('suggest_id');
        $status = $this->input->post('status');

        $this->db->select('tbl_suggest_evaluate.*');
        $this->db->from('tbl_suggest_evaluate');
        $this->db->where('tbl_suggest_evaluate.id', $suggest_id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
        } else {

            if ($status == 0) {
                $this->db->from('tblproduction_report');
                $this->db->where('tblproduction_report.object_type', 'suggest_evaluate');
                $this->db->where('tblproduction_report.object_id', $suggest_id);
                $checkExists = $this->db->count_all_results();
                if (!empty($checkExists)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Đã tạo phiếu báo cáo không phù hợp liên quan vui lòng xóa trước! !');
                    echo json_encode($data);
                    die();
                }
            }

            if (($dtData['status'] == $status)) {
                $data['result'] = 0;
                $data['message'] = lang('Trạng thái đã được cập nhật vui lòng làm mới danh sách');
                echo responseData($data);
                return;
            }

            $date_status = date('Y-m-d H:i:s');
            $staff_status = get_staff_user_id();

            $options = [
                'status' => $status,
                'date_status' => $date_status,
                'staff_status' => $staff_status,
            ];

            $this->db->where('id', $suggest_id);
            $up = $this->db->update('tbl_suggest_evaluate', $options);
            if ($up) {

                insertActivityLog([
                    'type_parent_obj' => 'suggest_evaluate',
                    'table_obj' => 'tbl_suggest_evaluate',
                    'id_obj' => $suggest_id,
                    'name_obj' => $dtData['reference_no'],
                    'content' => lang('Duyệt phiếu yêu cầu đánh giá') . ' [' . $dtData['reference_no'] . ']',
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
        if (!$this->preDeleteSuggestEvaluate) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $data = [];
        $this->db->select('tbl_suggest_evaluate.*');
        $this->db->from('tbl_suggest_evaluate');
        $this->db->where('tbl_suggest_evaluate.id', $id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);
            die();
        }

        if ($dtData['status'] == 1) {
            $data['result'] = 0;
            $data['message'] = lang('Phiếu đã duyệt không thể xóa !');
            echo json_encode($data);
            die();
        }

        $this->db->where('id', $id);
        $success = $this->db->delete('tbl_suggest_evaluate');
        if ($success) {
            $this->db->where('tbl_suggest_evaluate_item.suggest_plan_evaluate_id', $id);
            $this->db->delete('tbl_suggest_evaluate_item');

            insertActivityLog([
                'type_parent_obj' => 'suggest_evaluate',
                'table_obj' => 'tbl_suggest_evaluate',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu đánh giá') . ' [' . $dtData['reference_no'] . ']',
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

    public function searchEvaluates($id = 0)
    {
        $term = $this->input->get('term');
        $type = $this->input->get('types');
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_evaluate.id as id, 
            tbl_evaluate.name_evaluate as text,
            tbl_evaluate.code_evaluate as code,
            tbl_evaluate.name_evaluate as name,
        ', false);
        $this->db->from('tbl_evaluate');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_evaluate.name_evaluate', $term);
            $this->db->or_like('tbl_evaluate.code_evaluate', $term);
            $this->db->group_end();
        }
        $this->db->where('type', $type);
        $this->db->limit($limit);
        $pod = $this->db->get()->result_array();
        if ($type == 'evaluate') {
            $title = lang('Mã đánh giá');
        } elseif ($type == 'educate') {
            $title = lang('Mã đâò tạo');
        }
        $data['results'][] = ['text' => $title, 'children' => $pod];
        if (!empty($id)) {
            $dtData = get_table_where('tbl_evaluate', ['id' => $id], '', 'row_array');
            $data['row'] = ['id' => $dtData['id'], 'text' => $dtData['name_evaluate']];
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
            $type = $this->input->post('type');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();

            $this->db->select('
                tbl_suggest_evaluate.id as id,
                tbl_suggest_evaluate.reference_no as reference_no,
                tbl_suggest_evaluate.date as date,
                tbl_evaluate.code_evaluate as code_evaluate,
                tbl_evaluate.content_evaluate as content_evaluate,
                tbl_category_evaluate.name as name_type_evaluate,
                tbl_category_evaluate_detail.name as name_category_evaluate,
                tbl_suggest_evaluate_item.content as content,
                tbl_suggest_evaluate_item.actual_situation as actual_situation,
                tbl_result.name as name_result,
                tbl_suggest_evaluate.staff_evaluate as staff_evaluate,
                (SELECT GROUP_CONCAT(tblproduction_report.name_report)
                 FROM tblproduction_report
                 WHERE tblproduction_report.object_id = tbl_suggest_evaluate.id AND tblproduction_report.object_type = "suggest_evaluate"
                ) as name_report,
                tbl_suggest_evaluate_item.standard as standard
            ');
            $this->db->from('tbl_suggest_evaluate');
            $this->db->join('tbl_suggest_evaluate_item', 'tbl_suggest_evaluate_item.suggest_plan_evaluate_id = tbl_suggest_evaluate.id');
            $this->db->join('tbl_evaluate', 'tbl_evaluate.id = tbl_suggest_evaluate_item.evaluate_id');
            $this->db->join('tbl_category_evaluate', 'tbl_category_evaluate.id = tbl_suggest_evaluate.type_evaluate_id', 'inner');
            $this->db->join('tbl_category_evaluate_detail', 'tbl_category_evaluate_detail.id = tbl_suggest_evaluate_item.category_evaluate_id', 'left');
            $this->db->join('tbl_result', 'tbl_result.id = tbl_suggest_evaluate_item.result_id', 'left');

            if (!$this->preViewSuggestEvaluate) {
                $this->db->where('(tbl_suggest_evaluate.created_by = ' . get_staff_user_id() . ')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_evaluate.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_evaluate.date <= '" . $end_date_search . "'");
            }

            if (!empty($type)) {
                $this->db->where('(tbl_suggest_evaluate.object_type = "' . $type . '")');
            }

            $this->db->order_by('tbl_suggest_evaluate.id desc');
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
           
            if ($type == 'customer') {
                $title = _l('dt_suggest_evaluate_customer');
            } elseif ($type == 'supplier') {
                $title = _l('dt_suggest_evaluate_supplier');
            } elseif ($type == 'quality') {
                $title = _l('Phiếu Yêu Cầu Kiểm Tra Chất Lượng');
            } elseif ($type == 'procedure') {
                $title = _l('Phiếu Yêu Đánh Giá Quy Trình');
            } elseif ($type == 'system') {
                $title = _l('Phiếu Yêu Đánh Giá Hệ Thống');
            } else {
                $title = _l('dt_suggest_evaluate');
            }
            $objPHPExcel->getActiveSheet()->setCellValue(
                'A1',
                ($title)
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

            $objPHPExcel->getActiveSheet()->mergeCells('A1:O1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow . '', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $sttRow . '', 'Mã Số Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow . '', 'Ngày Lập Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow . '', 'Mã Đánh Giá')->getStyle("D$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow . '', 'Nội Dung Đánh Giá')->getStyle("E$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $sttRow . '', 'Loại Đánh Giá')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $sttRow . '', 'Nhóm Đánh Giá')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $sttRow . '', 'Chi Tiết Đánh Giá')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $sttRow . '', 'Hiện Trạng Thực Tế')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $sttRow . '', 'Kết Quả')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $sttRow . '', 'Người Đánh Giá')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $sttRow . '', 'Báo Cáo Không Phù Hợp')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $sttRow . '', 'Tiêu Chuẩn/ Quy Định')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $sttRow . '', 'QR');
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:N$sttRow")->applyFromArray([
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
            $this->load->library('ciqrcode');
            $rowBegin = $sttRow;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['reference_no']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", _dt($value['date']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['code_evaluate'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", $value['content_evaluate'])->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $value['name_category_evaluate'])->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $value['name_type_evaluate'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", ($value['content']))->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", ($value['actual_situation']))->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", ($value['name_result']))->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", get_staff_full_name($value['staff_evaluate']))->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", ($value['name_report']))->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", $value['standard'])->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);

                    if (!empty($value['barcode'])) {
                        $code = $value['barcode'];
                    } else {
                        $code = 'suggest_evaluate||' . $value['id'];
                        $this->db->where('id', $value['id']);
                        $this->db->update('tbl_suggest_evaluate', ['barcode' => $code]);
                    }
                    $qr = vn_to_str(str_replace('||', '__', $code));
                    $folder = FCPATH . 'uploads/suggest_evaluate/';
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
                        $objDrawing1->setCoordinates('N' . ($rowBegin));
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(42);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", '')->getStyle("N$rowBegin")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:N$rowBegin")->applyFromArray([
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
            $filename = lang('phieu_yeu_cau_danh_gia') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
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

    public function changeResult()
    {
        $id = $this->input->post('id');
        $result_id = $this->input->post('result_id');
        $this->db->where('id', $id);
        $success = $this->db->update('tbl_suggest_evaluate_item', [
            'result_id' => $result_id
        ]);
        if ($success) {
            $data['result'] = true;
            $data['message'] = lang('Thành công');
        } else {
            $data['result'] = false;
            $data['message'] = lang('Thất bại');
        }
        echo json_encode($data);
    }

    public function searchObject($id = 0)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');
        $params = $this->input->get('params');
        $type_evaluate_id = $params['type_evaluate_id'];
        $dtResult = [];
        if ($type_evaluate_id == 3) {
            $this->db->select('
                tblclients.userid as id,
                tblclients.company as text,
            ', false);
            $this->db->from('tblclients');
            if (!empty($term)) {
                $this->db->group_start();
                $this->db->like('tblclients.company', $term);
                $this->db->group_end();
            }
            $this->db->limit($limit);
            $dtResult = $this->db->get()->result_array();
        } elseif ($type_evaluate_id == 4) {
            $this->db->select('
                tblsuppliers.id as id,
                tblsuppliers.company as text,
            ', false);
            $this->db->from('tblsuppliers');
            if (!empty($term)) {
                $this->db->group_start();
                $this->db->like('tblsuppliers.company', $term);
                $this->db->group_end();
            }
            $this->db->limit($limit);
            $dtResult = $this->db->get()->result_array();
        }

        $results = [];

        if (!empty($dtResult)) {
            $results[] = ['text' => lang('Đối tượng'), 'children' => $dtResult];
        }

        $data['results'] = $results;

        if ($id) {
            $arrData = explode('__', $id);
            $id = $arrData[0];
            $object_type = $arrData[1];
            if ($object_type == 'customer') {
                $dtRow = get_table_where('tblclients', ['userid' => $id], '', 'row_array');
                $data['row'] = ['id' => $dtRow['userid'], 'text' => $dtRow['company']];
            } else if ($object_type == 'supplier') {
                $dtRow = get_table_where('tblclients', ['userid' => $id], '', 'row_array');
                $data['row'] = ['id' => $dtRow['userid'], 'text' => $dtRow['company']];
            }
        }
        echo json_encode($data);
    }

    public function changeEvaluate()
    {
        $type_evaluate_id = $this->input->post('type_evaluate_id');
        $this->db->select('tbl_category_evaluate_detail.*');
        $this->db->from('tbl_category_evaluate_detail');
        $this->db->where('category_evaluate_id', $type_evaluate_id);
        $dtResult = $this->db->get()->result_array();
        $html = '';
        $counter = 0;
        if (!empty($dtResult)) {
            foreach ($dtResult as $key => $value) {
                $dtDetail = get_table_where('tbl_evaluate', ['type_evaluate_id' => $type_evaluate_id, 'category_evaluate_id' => $value['id']]);
                $htmlChild = '';
                if (!empty($dtDetail)) {
                    foreach ($dtDetail as $k => $v) {
                        $tdStt = '<div class="stt"></div>';
                        $tdCode = '<div class="code_item">
                             <input type="hidden" name="counter[]" class="counter" value="' . $counter . '">
                             <input type="hidden" name="evaluate_id[' . $counter . ']" class="evaluate_id" value="' . $v['id'] . '">
                             <input type="hidden" name="category_evaluate_id[' . $counter . ']" class="category_evaluate_id" value="' . $value['id'] . '">
                             ' . $v['code_evaluate'] . '
                            </div>';
                        $tdContent = '<div class="td-content" style="width: 200px"><textarea name="content[' . $counter . ']" class="content form-control" cols="2" rows="3"></textarea></div>';
                        $tdActualSituation = '<div class="td-actual_situation"><input type="text" name="actual_situation[' . $counter . ']" class="actual_situation form-control" value=""></div>';
                        $tdStandard = '<div class="standard_item" style="width: 100%"><input type="text" name="standard[' . $counter . ']" class="standard form-control" value=""></div>';
                        $tdActions = '';
                        $htmlChild .= '<tr class="child_' . $value['id'] . '">
                            <td class="text-center">' . $tdStt . '</td>
                            <td>' . $tdCode . '</td>
                            <td style="width: 200px">' . $tdContent . '</td>
                            <td>' . $tdActualSituation . '</td>
                            <td style="width: 150px">' . $tdStandard . '</td>
                            <td class="td-actions text-center">' . $tdActions . '</td>
                        </tr>';
                        $counter++;
                    }
                }
                $tdStt = '<div class="stt">' . (++$key) . '</div>';
                $tdCode = '<div class="code_item">
                     ' . $value['name'] . '
                    </div>';
                $tdContent = '<div></div>';
                $tdActualSituation = '<div></div>';
                $tdStandard = '<div></div>';
                $tdActions = '<a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a>';
                $html .= '<tr class="bg-danger" data-id="' . $value['id'] . '">
                    <td class="text-center">' . $tdStt . '</td>
                    <td>' . $tdCode . '</td>
                    <td style="width: 200px">' . $tdContent . '</td>
                    <td>' . $tdActualSituation . '</td>
                    <td style="width: 150px">' . $tdStandard . '</td>
                    <td class="td-actions text-center">' . $tdActions . '</td>
                </tr>' . $htmlChild;
            }
        }
        $data['html'] = $html;
        echo json_encode($data);
    }
}
