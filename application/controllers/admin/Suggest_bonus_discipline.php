<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suggest_bonus_discipline extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->preViewSuggestBonusDiscipline = true;
        $this->preViewOwnSuggestBonusDiscipline = true;
        $this->preAddSuggestBonusDiscipline = true;
        $this->preEditSuggestBonusDiscipline = true;
        $this->preApproveSuggestBonusDiscipline = true;
        $this->preDeleteSuggestBonusDiscipline = true;
    }

    public function index()
    {
        if (!$this->preViewSuggestBonusDiscipline && !$this->preViewOwnSuggestBonusDiscipline) {
            access_denied();
        }
        $data['title'] = _l('dt_suggest_bonus_discipline');
        $this->load->view('admin/suggest_bonus_discipline/index', $data);
    }

    public function getSuggestBonusDisciplines()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_suggest_bonus_disciplines.id as id',
            'tbl_suggest_bonus_disciplines.reference_no as reference_no',
            'tbl_suggest_bonus_disciplines.date as date',
            'IF(tbl_suggest_bonus_disciplines.object_type = "department",tbldepartments.name,CONCAT(tblstaff.firstname," ",tblstaff.lastname)) as object_name',
            'tbl_type_bonus_discipline.name as name_type_bonus_discipline',
            'tbl_suggest_bonus_disciplines.grand_total as grand_total',
            'tbl_quota_bonus_discipline.name as name_quota_bonus_discipline',
            'tblproduction_report.name_report as name_report',
            'tbl_suggest_bonus_disciplines.status as status',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_bonus_disciplines';
        $where = [];
        $filter = [];

        $join = [
            'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_suggest_bonus_disciplines.object_id AND tbl_suggest_bonus_disciplines.object_type = "staff"',
            'LEFT JOIN tbldepartments ON tbldepartments.departmentid = tbl_suggest_bonus_disciplines.object_id AND tbl_suggest_bonus_disciplines.object_type = "department"',
            'INNER JOIN tbl_quota_bonus_discipline ON tbl_quota_bonus_discipline.id = tbl_suggest_bonus_disciplines.quota_bonus_disciplines_id',
            'INNER JOIN tbl_type_bonus_discipline ON tbl_type_bonus_discipline.id = tbl_suggest_bonus_disciplines.type_quota_bonus_disciplines_id',
            'LEFT JOIN tblinternal_proposal ON tblinternal_proposal.id = tbl_suggest_bonus_disciplines.propose_kpi',
            'LEFT JOIN tbl_suggest_kpi ON tbl_suggest_kpi.id = tbl_suggest_bonus_disciplines.suggest_kpi',
            'LEFT JOIN tblproduction_report ON tblproduction_report.id = tbl_suggest_bonus_disciplines.production_report_id',
        ];

        if (!$this->preViewSuggestBonusDiscipline) {
            array_push($where, 'AND (tbl_suggest_bonus_disciplines.created_by = ' . get_staff_user_id() . ')');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_suggest_bonus_disciplines.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_suggest_bonus_disciplines.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_suggest_bonus_disciplines.date_status',
            'tbl_suggest_bonus_disciplines.staff_status',
            'tbl_type_bonus_discipline.color',
            'tblinternal_proposal.code as code_internal',
            'tblinternal_proposal.id as id_internal',
            'tbl_suggest_kpi.id as suggest_kpi_id',
            'tbl_suggest_kpi.reference_no as reference_no_kpi',
            'tblproduction_report.reference_no as reference_no_report',
            'tblproduction_report.id as id_report',
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_bonus_discipline/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $htmlKpi = '';
            if (!empty($aRow['reference_no_kpi'])) {
                $htmlKpi = '<div style="border: 1px solid green;border-radius: 5px;padding: 5px;color: green"><div>Phiếu YCĐG KPI</div><a style="color: green" class="tnh-modal" href="' . base_url('admin/kpi/view_suggest_kpi/' . $aRow['suggest_kpi_id']) . '">' . $aRow['reference_no_kpi'] . '</a></div>';
            }
            $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/internal_proposal/view/' . $aRow['id_internal']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['code_internal'] . '</a></div>' . $htmlKpi;
            $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['object_name']) . '</div>';
            $row[] = '<div class="text-right">' . formatMoney($aRow['grand_total']) . '</div>';
            $row[] = '<div class="text-left"><div class="label " style="color: ' . $aRow['color'] . ';border:1px solid ' . $aRow['color'] . '">' . $aRow['name_type_bonus_discipline'] . '</div></div>';
            $row[] = '<div class="text-left">' . $aRow['name_quota_bonus_discipline'] . '</div>';
            $row[] = '<div class="text-left"><a class="c_modal" href="' . (admin_url('production_report/modal/' . $aRow['id_report'])) . '">' . (!empty($aRow['name_report']) ? $aRow['name_report'] .'-'.$aRow['reference_no_report'] : ''). '</a></div>';
            if ($aRow['status'] == 0) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 1)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'1\' class=\'btn btn-success\'>' . lang('tnh_agree') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-danger po">' . lang('Chưa duyệt') . '</span></div>';
            } elseif ($aRow['status'] == 1) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 0)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'0\' class=\'btn btn-danger\'>' . lang('Hủy duyệt') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-success po">' . lang('Đã duyệt') . '</span></div>';
                $_data .= '<div style="margin-top: 5px"> Người duyệt: ' . get_staff_full_name($aRow['staff_status']) . '</div>';
            } else {
                $_data = '';
            }
            $row[] = '<div class="text-left">' . $_data . '</div>';

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_bonus_discipline/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditSuggestBonusDiscipline ? '<a class="tnh-modal" href="' . base_url('admin/suggest_bonus_discipline/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteSuggestBonusDiscipline ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/suggest_bonus_discipline/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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

    public function detail($id = 0)
    {
        $data = [];
        $this->db->select('tbl_suggest_bonus_disciplines.*');
        $this->db->from('tbl_suggest_bonus_disciplines');
        $this->db->where('tbl_suggest_bonus_disciplines.id', $id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()) {
            if (!empty($id)) {
                if ($dtData['reference_no'] != $this->input->post('reference_no')) {
                    $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_bonus_disciplines.reference_no]');
                }
            } else {
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'required|is_unique[tbl_suggest_bonus_disciplines.reference_no]');
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('quota_bonus_disciplines_id', lang("Định mức khen thưởng-kỷ luật"), 'required');
            $this->form_validation->set_rules('type_quota_bonus_disciplines_id', lang("Loại định mức khen thưởng-kỷ luật"), 'required');
            $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
            if (empty($id)) {
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('suggest_bonus_disciplines');
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $quota_bonus_disciplines_id = $this->input->post('quota_bonus_disciplines_id');
                    $type_quota_bonus_disciplines_id = $this->input->post('type_quota_bonus_disciplines_id');
                    $precious_id = $this->input->post('precious_id');
                    $production_report_id = !empty($this->input->post('production_report_id')) ? $this->input->post('production_report_id') : 0;
                    $propose_kpi = !empty($this->input->post('propose_kpi')) ? $this->input->post('propose_kpi') : 0;
                    $grand_total = !empty($this->input->post('grand_total')) ? number_unformat($this->input->post('grand_total')) : 0;
                    $object_type = $this->input->post('object_type');
                    if ($object_type == 'staff') {
                        $object_id = ($this->input->post('object_id'));
                    } else {
                        $object_id = ($this->input->post('object_id_new'));
                    }

                    $note = ($this->input->post('note'));
                    $suggest_kpi = 0;
                    if (!empty($propose_kpi)) {
                        $dtInternal = get_table_where('tblinternal_proposal', ['id' => $propose_kpi], '', 'row_array');
                        $suggest_kpi = !empty($dtInternal) ? $dtInternal['suggest_id'] : 0;
                    }
                    $fields = [
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'quota_bonus_disciplines_id' => $quota_bonus_disciplines_id,
                        'type_quota_bonus_disciplines_id' => $type_quota_bonus_disciplines_id,
                        'object_type' => $object_type,
                        'object_id' => $object_id,
                        'precious_id' => $precious_id,
                        'grand_total' => $grand_total,
                        'note' => $note,
                        'propose_kpi' => $propose_kpi,
                        'suggest_kpi' => $suggest_kpi,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                        'production_report_id' => $production_report_id,
                    ];
                    $this->db->insert('tbl_suggest_bonus_disciplines', $fields);
                    $id = $this->db->insert_id();
                    if ($id) {
                        if (getReference('suggest_bonus_disciplines') == $reference_no) {
                            updateReference('suggest_bonus_disciplines');
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_bonus_discipline',
                            'table_obj' => 'tbl_suggest_bonus_disciplines',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu khen thưởng-kỷ luật') . ' [' . $reference_no . ']',
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
                    $quota_bonus_disciplines_id = $this->input->post('quota_bonus_disciplines_id');
                    $type_quota_bonus_disciplines_id = $this->input->post('type_quota_bonus_disciplines_id');
                    $precious_id = $this->input->post('precious_id');
                    $production_report_id = !empty($this->input->post('production_report_id')) ? $this->input->post('production_report_id') : 0;
                    $propose_kpi = !empty($this->input->post('propose_kpi')) ? $this->input->post('propose_kpi') : 0;
                    $grand_total = !empty($this->input->post('grand_total')) ? number_unformat($this->input->post('grand_total')) : 0;
                    $object_type = $this->input->post('object_type');
                    if ($object_type == 'staff') {
                        $object_id = ($this->input->post('object_id'));
                    } else {
                        $object_id = ($this->input->post('object_id_new'));
                    }
                    $note = ($this->input->post('note'));
                    $suggest_kpi = 0;
                    if (!empty($propose_kpi)) {
                        $dtInternal = get_table_where('tblinternal_proposal', ['id' => $propose_kpi], '', 'row_array');
                        $suggest_kpi = !empty($dtInternal) ? $dtInternal['suggest_id'] : 0;
                    }
                    $fields = [
                        'date' => $date,
                        'quota_bonus_disciplines_id' => $quota_bonus_disciplines_id,
                        'type_quota_bonus_disciplines_id' => $type_quota_bonus_disciplines_id,
                        'object_type' => $object_type,
                        'object_id' => $object_id,
                        'note' => $note,
                        'propose_kpi' => $propose_kpi,
                        'suggest_kpi' => $suggest_kpi,
                        'precious_id' => $precious_id,
                        'grand_total' => $grand_total,
                        'branch_id' => $branch_id,
                        'production_report_id' => $production_report_id,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->where('id', $id);
                    $success = $this->db->update('tbl_suggest_bonus_disciplines', $fields);
                    if ($success) {
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_bonus_disciplines',
                            'table_obj' => 'tbl_suggest_bonus_disciplines',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu khen thưởng-kỷ luật') . ' [' . $dtData['reference_no'] . ']',
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
                if (!$this->preAddSuggestBonusDiscipline) {
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_request_bonus_discipline');
            } else {
                if (!$this->preEditSuggestBonusDiscipline) {
                    accessDenied(true);
                }

                if ($dtData['status'] == 1) {
                    refererModel(lang('Phiếu đã duyệt không thể sửa !'));
                }
                $data['dtData'] = $dtData;
                $data['title'] = lang('dt_edit_request_bonus_discipline');
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['deparment'] = get_table_where('tbldepartments');
        $data['id'] = $id;
        $data['reference_no'] = getReference('suggest_bonus_disciplines');
        $data['typeBounsDiscipline'] = get_table_where('tbl_type_bonus_discipline');
        $data['dtPrecious'] = get_table_where('tbl_precious');
        $this->load->view('admin/suggest_bonus_discipline/detail', $data);
    }

    public function view($id)
    {
        $data = [];
        $data['title'] = lang('dt_view_request_bonus_discipline');
        $this->db->select('tbl_suggest_bonus_disciplines.*,
            IF(tbl_suggest_bonus_disciplines.object_type = "department",tbldepartments.name,CONCAT(tblstaff.firstname," ",tblstaff.lastname)) as object_name,
            tbl_type_bonus_discipline.name as name_type_bonus_discipline,
            tbl_quota_bonus_discipline.name as name_quota_bonus_discipline,
            tbl_type_bonus_discipline.color as color,
            tblinternal_proposal.code as code_internal,
            tbl_suggest_kpi.reference_no as reference_no_suggest_kpi,
        ');
        $this->db->from('tbl_suggest_bonus_disciplines');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_suggest_bonus_disciplines.object_id AND tbl_suggest_bonus_disciplines.object_type = "staff"', 'left');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tbl_suggest_bonus_disciplines.object_id AND tbl_suggest_bonus_disciplines.object_type = "department"', 'left');
        $this->db->join('tbl_quota_bonus_discipline', 'tbl_quota_bonus_discipline.id = tbl_suggest_bonus_disciplines.quota_bonus_disciplines_id', 'inner');
        $this->db->join('tbl_type_bonus_discipline', 'tbl_type_bonus_discipline.id = tbl_suggest_bonus_disciplines.type_quota_bonus_disciplines_id', 'inner');
        $this->db->join('tblinternal_proposal', 'tblinternal_proposal.id = tbl_suggest_bonus_disciplines.propose_kpi', 'left');
        $this->db->join('tbl_suggest_kpi', 'tbl_suggest_kpi.id = tbl_suggest_bonus_disciplines.suggest_kpi', 'left');
        $this->db->where('tbl_suggest_bonus_disciplines.id', $id);
        $dtData = $this->db->get()->row_array();
        $data['dtData'] = $dtData;
        $this->load->view('admin/suggest_bonus_discipline/view', $data);
    }

    public function agree()
    {
        if (!$this->preApproveSuggestBonusDiscipline) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }

        $data = [];
        $suggest_id = $this->input->post('suggest_id');
        $status = $this->input->post('status');

        $this->db->select('tbl_suggest_bonus_disciplines.*');
        $this->db->from('tbl_suggest_bonus_disciplines');
        $this->db->where('tbl_suggest_bonus_disciplines.id', $suggest_id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
        } else {

            $this->db->from('tbl_decision_bonus_discipline');
            $this->db->where('tbl_decision_bonus_discipline.suggest_bonus_discipline_id', $suggest_id);
            $checkExits = $this->db->count_all_results();
            if (!empty($checkExits)) {
                $data['result'] = 0;
                $data['message'] = lang('Đã tồn tại phiếu quyết định không thể bỏ duyệt!');
                echo responseData($data);
                return;
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
            $up = $this->db->update('tbl_suggest_bonus_disciplines', $options);
            if ($up) {

                insertActivityLog([
                    'type_parent_obj' => 'suggest_bonus_disciplines',
                    'table_obj' => 'tbl_suggest_bonus_disciplines',
                    'id_obj' => $suggest_id,
                    'name_obj' => $dtData['reference_no'],
                    'content' => lang('Duyệt phiếu yêu cầu khen thưởng-kỷ luật') . ' [' . $dtData['reference_no'] . ']',
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
        if (!$this->preDeleteSuggestBonusDiscipline) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $data = [];
        $this->db->select('tbl_suggest_bonus_disciplines.*');
        $this->db->from('tbl_suggest_bonus_disciplines');
        $this->db->where('tbl_suggest_bonus_disciplines.id', $id);
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
        $success = $this->db->delete('tbl_suggest_bonus_disciplines');
        if ($success) {

            insertActivityLog([
                'type_parent_obj' => 'suggest_bonus_disciplines',
                'table_obj' => 'tbl_suggest_bonus_disciplines',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu khen thưởng-kỷ luật') . ' [' . $dtData['reference_no'] . ']',
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

    public function searchQuotaBonusDisciplines($id = 0, $precious_id = 0)
    {
        $term = $this->input->get('term');
        $params = $this->input->get('params');
        if (empty($precious_id)) {
            $precious_id = $this->input->get('precious_id');
        }
        $type_bonus_disciplines_id = !empty($params['type_bonus_disciplines_id']) ? $params['type_bonus_disciplines_id'] : 0;
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_quota_bonus_discipline.id as id, 
            CONCAT(tbl_quota_bonus_discipline.name," (",tbl_quota_bonus_discipline.code,")","",IF(tbl_quota_bonus_discipline.default_new = 1,"-Công Thức",""),"") as text,
            0 as grand_total
        ', false);
        $this->db->from('tbl_quota_bonus_discipline');
        $this->db->join('tbl_quota_precious', 'tbl_quota_precious.quota_id = tbl_quota_bonus_discipline.id');
        $this->db->where('tbl_quota_precious.precious_id', $precious_id);
        $this->db->where('tbl_quota_bonus_discipline.type', $type_bonus_disciplines_id);
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_quota_bonus_discipline.name', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $dtData = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Định mức khen thưởng-kỷ luật'), 'children' => $dtData];
        if (!empty($id)) {
            $this->db->select('            
                tbl_quota_bonus_discipline.id as id, 
                CONCAT(tbl_quota_bonus_discipline.name," (",tbl_quota_bonus_discipline.code,")","",IF(tbl_quota_bonus_discipline.default_new = 1,"-Công Thức",""),"") as text');
            $this->db->from('tbl_quota_bonus_discipline');
            $this->db->join('tbl_quota_precious', 'tbl_quota_precious.quota_id = tbl_quota_bonus_discipline.id');
            $this->db->where('tbl_quota_precious.precious_id', $precious_id);
            $this->db->where('tbl_quota_bonus_discipline.id', $id);
            $dtDataRow = $this->db->get()->row_array();
            $data['row'] = ['id' => $dtDataRow['id'], 'text' => $dtDataRow['text']];
        }
        echo json_encode($data);
    }

    public function searchProposeKpi($id = 0)
    {
        $term = $this->input->get('term');
        $params = $this->input->get('params');
        $object_id = !empty($this->input->get('object_id')) ? $this->input->get('object_id') : 0;
        $limit = get_option('select2_limit');
        $this->db->select('
            tblinternal_proposal.id as id, 
            CONCAT(tblinternal_proposal.code,"(",tbl_suggest_kpi.reference_no,")") as text
        ', false);
        $this->db->from('tblinternal_proposal');
        $this->db->join('tbl_suggest_kpi', 'tbl_suggest_kpi.id = tblinternal_proposal.suggest_id');
        $this->db->where('tbl_suggest_kpi.staff_suggest', $object_id);
        $this->db->where('EXISTS (
            SELECT 1
            FROM tbl_category_recommended
            WHERE tbl_category_recommended.id = tblinternal_proposal.category_recommended_id
            AND tbl_category_recommended.type_kpi = 1
        )');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tblinternal_proposal.code', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $dtData = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Phiếu đề xuất đánh giá KPI'), 'children' => $dtData];
        if (!empty($id)) {
            $this->db->select('            
                tblinternal_proposal.id as id, 
                CONCAT(tblinternal_proposal.code,"(",tbl_suggest_kpi.reference_no,")") as text');
            $this->db->from('tblinternal_proposal');
            $this->db->join('tbl_suggest_kpi', 'tbl_suggest_kpi.id = tblinternal_proposal.suggest_id');
            $this->db->where('tblinternal_proposal.id', $id);
            $dtDataRow = $this->db->get()->row_array();
            $data['row'] = ['id' => $dtDataRow['id'], 'text' => $dtDataRow['text']];
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
               tbl_suggest_recruitment.id as id,
               tbl_suggest_recruitment.reference_no as reference_no,
               tbl_suggest_recruitment.date as date,
               tbl_suggest_recruitment.position_recruitment as position_recruitment,
               tbl_suggest_recruitment.content_work as content_work,
               tbl_suggest_recruitment.kpis as kpis,
               tbl_suggest_recruitment.note as note,
               tbl_suggest_recruitment.quantity as quantity,
               tbl_suggest_recruitment.time_work as time_work,
               tbl_suggest_recruitment.gender as gender,
               tbl_suggest_recruitment.completion_time_limit as completion_time_limit,
               tbl_suggest_recruitment.standard as standard,
               tbl_suggest_recruitment.barcode as barcode,
            ');
            $this->db->from('tbl_suggest_recruitment');

            if (!$this->preViewSuggestRecruitment) {
                $this->db->where('(tbl_suggest_recruitment.created_by = ' . get_staff_user_id() . ')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_recruitment.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_recruitment.date <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tbl_suggest_recruitment.id desc');
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
                ('PHIẾU YÊU CẦU TUYỂN DỤNG')
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
            $objPHPExcel->getActiveSheet()->mergeCells('A1:M1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow . '', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $sttRow . '', 'Mã Số Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow . '', 'Ngày Lập Phiếu YCTD')->getStyle("C$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow . '', 'Vị Trí Tuyển Dụng')->getStyle("D$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow . '', 'Mô Tả Công Việc')->getStyle("E$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $sttRow . '', 'KPIs')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $sttRow . '', 'Lý Do')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $sttRow . '', 'Số Lượng')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $sttRow . '', 'Thời Gian Làm Việc')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $sttRow . '', 'Giới Tính')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $sttRow . '', 'Định Mức Thời Gian Hoàn Thành)')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $sttRow . '', 'Tiêu Chuẩn/ Quy Định')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $sttRow . '', 'QR')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:M$sttRow")->applyFromArray([
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
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['position_recruitment'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", $value['content_work'])->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $value['kpis'])->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $value['note'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", $value['quantity'])->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $value['time_work'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    if ($value['gender'] == "male") {
                        $htmlGender = 'Nam';
                    } elseif ($value['gender'] == "female") {
                        $htmlGender = 'Nữ';
                    } elseif ($value['gender'] == "other") {
                        $htmlGender = 'Khác';
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $htmlGender)->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $value['completion_time_limit'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $value['standard'])->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);

                    if (!empty($value['barcode'])) {
                        $code = $value['barcode'];
                    } else {
                        $code = 'suggest_recruitment||' . $value['id'];
                        $this->db->where('id', $value['id']);
                        $this->db->update('tbl_suggest_recruitment', ['barcode' => $code]);
                    }
                    $qr = vn_to_str(str_replace('||', '__', $code));
                    $folder = FCPATH . 'uploads/suggest_recruitment/';
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
                        $objDrawing1->setCoordinates('M' . ($rowBegin));
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(42);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", '')->getStyle("M$rowBegin")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:M$rowBegin")->applyFromArray([
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
                    $objPHPExcel->getActiveSheet()->getStyle("H$rowBegin:J$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                }
            }
            $filename = lang('phieu_yeu_cau_tuyen_dung') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
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
    public function get_price()
    {
        $data = $this->input->post();
        $price = 0;
        if (!empty($data['quota_bonus_disciplines_id'])) {
            $quota_bonus_disciplines_id = $data['quota_bonus_disciplines_id'];
            $salary_bhxh = 0;
            $quota_bonus_discipline =  get_table_where('tbl_quota_bonus_discipline', array('id' => $data['quota_bonus_disciplines_id']), '', 'row_array');
            $quota_precious =  get_table_where('tbl_quota_precious', array('quota_id' => $quota_bonus_discipline['id']), '', 'row_array');
            if ($quota_bonus_discipline['default_new'] == 0) {
                $price = $quota_precious['value'];
            } else {
                if ($quota_bonus_disciplines_id == 9 || $quota_bonus_disciplines_id == 10  || $quota_bonus_disciplines_id == 11 || $quota_bonus_disciplines_id == 12 || $quota_bonus_disciplines_id == 13 || $quota_bonus_disciplines_id == 2 || $quota_bonus_disciplines_id == 8 || $quota_bonus_disciplines_id == 7) {
                    if ($quota_bonus_disciplines_id == 9) {
                        $heso = 2;
                    }
                    if ($quota_bonus_disciplines_id == 10) {
                        $heso = 1.5;
                    }
                    if ($quota_bonus_disciplines_id == 11) {
                        $heso = 1;
                    }
                    if ($quota_bonus_disciplines_id == 12) {
                        $heso = 0.8;
                    }
                    if ($quota_bonus_disciplines_id == 13) {
                        $heso = 0.6;
                    }
                    if ($quota_bonus_disciplines_id == 2) {
                        $heso = 0.3;
                    }
                    if (!empty($data['object_id'])) {
                        $staff =  get_table_where('tblstaff', array('staffid' => $data['object_id']), '', 'row_array', '', 'salary_bhxh,day_in');
                        $salary_bhxh = $staff['salary_bhxh'];
                        if ($quota_bonus_disciplines_id == 8) {
                            if (!empty($staff['day_in'])) {
                                $today = new DateTime();
                                $otherDate = new DateTime($staff['day_in']);
                                $interval = $today->diff($otherDate);
                                $days = $interval->format('%a');
                                if($days >= 365){
                                    $price = $salary_bhxh;
                                }elseif($days < 365){
                                    $price = $salary_bhxh*($days/365);
                                }
                            }
                        } elseif ($quota_bonus_disciplines_id == 7) {
                            if (!empty($staff['day_in'])) {
                                $today = new DateTime();
                                $otherDate = new DateTime($staff['day_in']);
                                $interval = $today->diff($otherDate);
                                $days = $interval->format('%a');
                                if($days >= 1825){
                                    $dayss = round(($days/365),2);
                                    $price = 100000*($dayss);
                                }
                            }
                        } else {
                            $price = $salary_bhxh * $heso;
                        }
                    }
                    if ($quota_bonus_disciplines_id == 9) {
                        $price = 0;
                    }
                    if ($quota_bonus_disciplines_id == 10) {
                        $price = 0;
                    }
                    if ($quota_bonus_disciplines_id == 11) {
                        $price = 0;
                    }
                    if ($quota_bonus_disciplines_id == 12) {
                        $price = 0;
                    }
                    if ($quota_bonus_disciplines_id == 13) {
                        $price = 0;
                    }
                }
            }
        }
        echo $price;
    }
}
