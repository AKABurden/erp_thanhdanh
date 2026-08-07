<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Decision_bonus_discipline extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->preViewDecisionBonusDeiscipline = true;
        $this->preAddDecisionBonusDeiscipline = true;
        $this->preDeleteDecisionBonusDeiscipline = true;
        $this->preEditDecisionBonusDeiscipline = true;
    }

    public function index()
    {
        if (!$this->preViewDecisionBonusDeiscipline) {
            access_denied();
        }
        $data['title'] = lang('dt_decision_bonus_discipline');
        $this->load->view('admin/decision_bonus_discipline/index', $data);
    }

    public function getDecisionBonusDisciplines()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_decision_bonus_discipline.id as id',
            'tbl_decision_bonus_discipline.reference_no as reference_no',
            'tbl_suggest_bonus_disciplines.date as date',
            'tbl_suggest_bonus_disciplines.reference_no as reference_no_suggest',
            'tbl_type_bonus_discipline.name as name_type_bonus_discipline',
            'tbl_suggest_bonus_disciplines.grand_total as grand_total',
            'IF(tbl_suggest_bonus_disciplines.object_type = "department",tbldepartments.name,CONCAT(tblstaff.firstname," ",tblstaff.lastname)) as object_name',
            'tbl_decision_bonus_discipline.status as status',
            'tbl_decision_bonus_discipline.created_by as created_by',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_decision_bonus_discipline';
        $where = [

        ];
        $filter = [];

        $join = [
            'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_decision_bonus_discipline.object_id AND tbl_decision_bonus_discipline.object_type = "staff"',
            'LEFT JOIN tbldepartments ON tbldepartments.departmentid = tbl_decision_bonus_discipline.object_id AND tbl_decision_bonus_discipline.object_type = "department"',
            'INNER JOIN tbl_suggest_bonus_disciplines ON tbl_suggest_bonus_disciplines.id = tbl_decision_bonus_discipline.suggest_bonus_discipline_id',
            'INNER JOIN tbl_type_bonus_discipline ON tbl_type_bonus_discipline.id = tbl_decision_bonus_discipline.type_quota_bonus_discipline_id',
        ];

        if (!$this->preViewDecisionBonusDeiscipline) {
            array_push($where, 'AND (tbl_decision_bonus_discipline.created_by = ' . get_staff_user_id() . ')');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_decision_bonus_discipline.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_decision_bonus_discipline.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_decision_bonus_discipline.date_status',
            'tbl_decision_bonus_discipline.staff_status',
            'tbl_suggest_bonus_disciplines.id as suggest_id',
            'tbl_type_bonus_discipline.color as color',
            '(SELECT CONCAT(tblinternal_proposal.id, "__", tblinternal_proposal.code) FROM tblinternal_proposal WHERE tblinternal_proposal.decision_bonus_discipline_id = tbl_decision_bonus_discipline.id LIMIT 1) as internal_proposal',
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $_data = '';
            if (!empty($aRow['internal_proposal'])) {
                $internal_proposal = explode('__', $aRow['internal_proposal']);
                $_data = '<span class="label label-success pull-left mtop5 text-center" style="padding-top: 1px;padding-bottom: 1px;">Phiếu: <a class="c_modal" href="' . (admin_url('internal_proposal/view/' . $internal_proposal[0])) . '">' . $internal_proposal[1] . '</a></span>';
            }
            $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/decision_bonus_discipline/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>
                ' . $_data . '
            ';
            $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_bonus_discipline/view/' . $aRow['suggest_id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no_suggest'] . '</a></div>';
            $row[] = '<div class="text-left"><div class="label " style="color: ' . $aRow['color'] . ';border:1px solid ' . $aRow['color'] . '">' . $aRow['name_type_bonus_discipline'] . '</div></div>';
            $row[] = '<div class="text-right">' . formatMoney($aRow['grand_total']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['object_name']) . '</div>';
            if ($aRow['status'] == 0) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 1)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'1\' class=\'btn btn-success\'>' . lang('tnh_agree') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-danger po">' . lang('Chưa duyệt') . '</span></div>';
            } elseif ($aRow['status'] == 1) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 0)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'0\' class=\'btn btn-danger\'>' . lang('Hủy duyệt') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-success po">' . lang('Đã duyệt') . '</span></div>';
                $_data .= '<div style="margin-top: 5px"> Người duyệt: ' . get_staff_full_name($aRow['staff_status']) . '</div>';
            } else {
                $_data = '';
            }
            $row[] = '<div class="text-left">' . $_data . '</div>';
            $row[] = '<div class="text-left">' . get_staff_full_name($aRow['created_by']) . '</div>';

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/decision_bonus_discipline/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditDecisionBonusDeiscipline ? '<a class="tnh-modal" href="' . base_url('admin/decision_bonus_discipline/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteDecisionBonusDeiscipline ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/decision_bonus_discipline/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
        $this->db->select('tbl_decision_bonus_discipline.*,
             IF(tbl_decision_bonus_discipline.object_type = "department",tbldepartments.name,CONCAT(tblstaff.firstname," ",tblstaff.lastname)) as object_name,
        ');
        $this->db->from('tbl_decision_bonus_discipline');
        $this->db->join('tblstaff',
            'tblstaff.staffid = tbl_decision_bonus_discipline.object_id AND tbl_decision_bonus_discipline.object_type = "staff"',
            'left');
        $this->db->join('tbldepartments',
            'tbldepartments.departmentid = tbl_decision_bonus_discipline.object_id AND tbl_decision_bonus_discipline.object_type = "department"',
            'left');
        $this->db->where('tbl_decision_bonus_discipline.id', $id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()) {
            if (!empty($id)) {
                if ($dtData['reference_no'] != $this->input->post('reference_no')) {
                    $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"),
                        'trim|required|is_unique[tbl_decision_bonus_discipline.reference_no]');
                }
            } else {
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"),
                    'required|is_unique[tbl_decision_bonus_discipline.reference_no]');
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('suggest_bonus_discipline_id', lang("Phiếu yêu cầu khen thưởng-kỷ luật"),
                'required');
            $this->form_validation->set_rules('type_quota_bonus_discipline_id',
                lang("Loại định mức khen thưởng-kỷ luật"), 'required');
            $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
            if (empty($id)) {
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('decision_bonus_discipline');
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $suggest_bonus_discipline_id = $this->input->post('suggest_bonus_discipline_id');
                    $kpi_id = !empty($this->input->post('kpi_id')) ? $this->input->post('kpi_id') : 0;
                    $dtSuggestBonus = get_table_where('tbl_suggest_bonus_disciplines',
                        ['id' => $suggest_bonus_discipline_id], '', 'row_array');
                    if (empty($dtSuggestBonus)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Không tồn tại phiếu yêu cầu');
                        echo json_encode($data);
                        die();
                    }
                    $note = ($this->input->post('note'));
                    $type_quota_bonus_disciplines_id = $dtSuggestBonus['type_quota_bonus_disciplines_id'];
                    $object_type = $dtSuggestBonus['object_type'];
                    $object_id = $dtSuggestBonus['object_id'];
                    $grand_total = $dtSuggestBonus['grand_total'];
                    $fields = [
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'suggest_bonus_discipline_id' => $suggest_bonus_discipline_id,
                        'type_quota_bonus_discipline_id' => $type_quota_bonus_disciplines_id,
                        'object_type' => $object_type,
                        'object_id' => $object_id,
                        'grand_total' => $grand_total,
                        'note' => $note,
                        'kpi_id' => $kpi_id,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->insert('tbl_decision_bonus_discipline', $fields);
                    $id = $this->db->insert_id();
                    if ($id) {
                        if (getReference('decision_bonus_discipline') == $reference_no) {
                            updateReference('decision_bonus_discipline');
                        }

                        $this->load->library('upload');
                        if (isset($_FILES['file']['name']) && ($_FILES['file']['name'] != '' || is_array($_FILES['file']['name']) && count($_FILES['file']['name']) > 0)) {
                            if (!is_array($_FILES['file']['name'])) {
                                $_FILES['file']['name'] = [$_FILES['file']['name']];
                                $_FILES['file']['type'] = [$_FILES['file']['type']];
                                $_FILES['file']['tmp_name'] = [$_FILES['file']['tmp_name']];
                                $_FILES['file']['error'] = [$_FILES['file']['error']];
                                $_FILES['file']['size'] = [$_FILES['file']['size']];
                            }
                            $path = FCPATH.'uploads/decision_bonus_discipline/';
                            if (!file_exists($path)) {
                                mkdir($path);
                                fopen($path.'index.html', 'w');
                            }
                            if (!file_exists($path.$id.'/')) {
                                mkdir($path.$id.'/');
                                fopen($path.$id.'/'.'index.html', 'w');
                            }
                            for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
                                $tmpFilePath = $_FILES['file']['tmp_name'][$i];
                                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                                    $filename = vn_to_str(unique_filename($path, $_FILES['file']['name'][$i]));
                                    if (!_upload_extension_allowed($filename)) {
                                        continue;
                                    }
                                    $newFilePath = $path .$id.'/'. $filename;
                                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                                        $typeFile = $_FILES['file']['type'][$i];
                                        if (file_exists($newFilePath)) {
                                            $this->db->insert('tblfiles', [
                                                'rel_id' => $id,
                                                'rel_type' => 'decision_bonus',
                                                'file_name' => $filename,
                                                'filetype' => $typeFile,
                                                'staffid' => get_staff_user_id(),
                                                'dateadded' => date('Y-m-d H:i:s'),
                                            ]);
                                        }
                                    }
                                }
                            }
                        }

                        insertActivityLog([
                            'type_parent_obj' => 'decision_bonus_discipline',
                            'table_obj' => 'tbl_decision_bonus_discipline',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới quyết định khen thưởng-kỷ luật') . ' [' . $reference_no . ']',
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
                    $suggest_bonus_discipline_id = $this->input->post('suggest_bonus_discipline_id');
                    $kpi_id = !empty($this->input->post('kpi_id')) ? $this->input->post('kpi_id') : 0;
                    $dtSuggestBonus = get_table_where('tbl_suggest_bonus_disciplines',
                        ['id' => $suggest_bonus_discipline_id], '', 'row_array');
                    if (empty($dtSuggestBonus)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Không tồn tại phiếu yêu cầu');
                        echo json_encode($data);
                        die();
                    }
                    $type_quota_bonus_disciplines_id = $dtSuggestBonus['type_quota_bonus_disciplines_id'];
                    $object_type = $dtSuggestBonus['object_type'];
                    $object_id = $dtSuggestBonus['object_id'];
                    $note = ($this->input->post('note'));
                    $grand_total = $dtSuggestBonus['grand_total'];
                    $fields = [
                        'date' => $date,
                        'suggest_bonus_discipline_id' => $suggest_bonus_discipline_id,
                        'type_quota_bonus_discipline_id' => $type_quota_bonus_disciplines_id,
                        'object_type' => $object_type,
                        'object_id' => $object_id,
                        'note' => $note,
                        'kpi_id' => $kpi_id,
                        'grand_total' => $grand_total,
                        'branch_id' => $branch_id,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                    ];

                    $this->db->where('id', $id);
                    $success = $this->db->update('tbl_decision_bonus_discipline', $fields);
                    if ($success) {

                        $this->load->library('upload');

                        $file_old = !empty($this->input->post('file_old')) ? $this->input->post('file_old') : [];
                        $dtFileOld = $this->db->get_where('tblfiles',
                            ['rel_type' => 'decision_bonus', 'rel_id' => $id])->result_array();
                        if (!empty($dtFileOld)) {
                            foreach ($dtFileOld as $kk => $vv) {
                                if (!in_array($vv['file_name'], $file_old)) {
                                    $this->db->where('id', $vv['id']);
                                    $this->db->delete('tblfiles');
                                    $fileUnset = FCPATH.'uploads/decision_bonus_discipline/'.$id.'/'.$vv['file_name'];
                                    if (file_exists($fileUnset)) {
                                        @unlink($fileUnset);
                                    }
                                }
                            }
                        }

                        if (isset($_FILES['file']['name']) && ($_FILES['file']['name'] != '' || is_array($_FILES['file']['name']) && count($_FILES['file']['name']) > 0)) {
                            if (!is_array($_FILES['file']['name'])) {
                                $_FILES['file']['name'] = [$_FILES['file']['name']];
                                $_FILES['file']['type'] = [$_FILES['file']['type']];
                                $_FILES['file']['tmp_name'] = [$_FILES['file']['tmp_name']];
                                $_FILES['file']['error'] = [$_FILES['file']['error']];
                                $_FILES['file']['size'] = [$_FILES['file']['size']];
                            }
                            $path = FCPATH.'uploads/decision_bonus_discipline/';
                            if (!file_exists($path)) {
                                mkdir($path);
                                fopen($path.'index.html', 'w');
                            }
                            if (!file_exists($path.$id.'/')) {
                                mkdir($path.$id.'/');
                                fopen($path.$id.'/'.'index.html', 'w');
                            }
                            for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
                                $tmpFilePath = $_FILES['file']['tmp_name'][$i];
                                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                                    $filename = vn_to_str(unique_filename($path, $_FILES['file']['name'][$i]));
                                    if (!_upload_extension_allowed($filename)) {
                                        continue;
                                    }
                                    $newFilePath = $path .$id.'/'. $filename;
                                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                                        $typeFile = $_FILES['file']['type'][$i];
                                        if (file_exists($newFilePath)) {
                                            $this->db->insert('tblfiles', [
                                                'rel_id' => $id,
                                                'rel_type' => 'decision_bonus',
                                                'file_name' => $filename,
                                                'filetype' => $typeFile,
                                                'staffid' => get_staff_user_id(),
                                                'dateadded' => date('Y-m-d H:i:s'),
                                            ]);
                                        }
                                    }
                                }
                            }
                        }

                        insertActivityLog([
                            'type_parent_obj' => 'decision_bonus_discipline',
                            'table_obj' => 'tbl_decision_bonus_discipline',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu quyết định khen thưởng-kỷ luật') . ' [' . $dtData['reference_no'] . ']',
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
                if (!$this->preAddDecisionBonusDeiscipline) {
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_decision_bonus_discipline');
            } else {
                if (!$this->preEditDecisionBonusDeiscipline) {
                    accessDenied(true);
                }

                if ($dtData['status'] == 1) {
                    refererModel(lang('Phiếu đã duyệt không thể sửa !'));

                }

                $this->db->from('tblfiles');
                $this->db->where('tblfiles.rel_id',$id);
                $this->db->where('tblfiles.rel_type','decision_bonus');
                $dtFile = $this->db->get()->result_array();

                $data['dtFile'] = $dtFile;
                $data['dtData'] = $dtData;
                $data['title'] = lang('dt_edit_decision_bonus_discipline');
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['deparment'] = get_table_where('tbldepartments');
        $data['id'] = $id;
        $data['reference_no'] = getReference('decision_bonus_discipline');
        $data['typeBounsDiscipline'] = get_table_where('tbl_type_bonus_discipline');
        $this->load->view('admin/decision_bonus_discipline/detail', $data);
    }

    public function view($id)
    {
        $data = [];
        $data['title'] = lang('dt_view_decision_bonus_discipline');
        $this->db->select('tbl_decision_bonus_discipline.*,
            IF(tbl_decision_bonus_discipline.object_type = "department",tbldepartments.name,CONCAT(tblstaff.firstname," ",tblstaff.lastname)) as object_name,
            tbl_type_bonus_discipline.name as name_type_bonus_discipline,
            tbl_quota_bonus_discipline.name as name_quota_bonus_discipline,
            tbl_suggest_bonus_disciplines.reference_no as reference_no_suggest,
            tbl_kpi.reference_no as reference_no_kpi,
            tbl_type_bonus_discipline.color as color,
        ');
        $this->db->from('tbl_decision_bonus_discipline');
        $this->db->join('tblstaff',
            'tblstaff.staffid = tbl_decision_bonus_discipline.object_id AND tbl_decision_bonus_discipline.object_type = "staff"',
            'left');
        $this->db->join('tbldepartments',
            'tbldepartments.departmentid = tbl_decision_bonus_discipline.object_id AND tbl_decision_bonus_discipline.object_type = "department"',
            'left');
        $this->db->join('tbl_suggest_bonus_disciplines',
            'tbl_suggest_bonus_disciplines.id = tbl_decision_bonus_discipline.suggest_bonus_discipline_id', 'inner');
        $this->db->join('tbl_quota_bonus_discipline',
            'tbl_quota_bonus_discipline.id = tbl_suggest_bonus_disciplines.quota_bonus_disciplines_id', 'inner');
        $this->db->join('tbl_type_bonus_discipline',
            'tbl_type_bonus_discipline.id = tbl_decision_bonus_discipline.type_quota_bonus_discipline_id', 'inner');
        $this->db->join('tbl_kpi',
            'tbl_kpi.id = tbl_decision_bonus_discipline.kpi_id', 'left');
        $this->db->where('tbl_decision_bonus_discipline.id', $id);
        $dtData = $this->db->get()->row_array();
        $data['dtData'] = $dtData;
        $this->load->view('admin/decision_bonus_discipline/view', $data);
    }

    public function agree()
    {
        if (!$this->preAddDecisionBonusDeiscipline) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }

        $data = [];
        $suggest_id = $this->input->post('suggest_id');
        $status = $this->input->post('status');

        $this->db->select('tbl_decision_bonus_discipline.*');
        $this->db->from('tbl_decision_bonus_discipline');
        $this->db->where('tbl_decision_bonus_discipline.id', $suggest_id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
        } else {

            $this->db->from('tblinternal_proposal');
            $this->db->where('tblinternal_proposal.decision_bonus_discipline_id',$suggest_id);
            $checkExits = $this->db->count_all_results();
            if (!empty($checkExits)){
                $data['result'] = 0;
                $data['message'] = lang('Đã tồn tại phiếu đề xuất nội bộ không thể bỏ duyệt!');
                echo responseData($data); return;
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
            $up = $this->db->update('tbl_decision_bonus_discipline', $options);
            if ($up) {

                insertActivityLog([
                    'type_parent_obj' => 'decision_bonus_disciplines',
                    'table_obj' => 'tbl_decision_bonus_discipline',
                    'id_obj' => $suggest_id,
                    'name_obj' => $dtData['reference_no'],
                    'content' => lang('Duyệt phiếu quyết định khen thưởng-kỷ luật') . ' [' . $dtData['reference_no'] . ']',
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
        if (!$this->preDeleteDecisionBonusDeiscipline) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $data = [];
        $this->db->select('tbl_decision_bonus_discipline.*');
        $this->db->from('tbl_decision_bonus_discipline');
        $this->db->where('tbl_decision_bonus_discipline.id', $id);
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
        $success = $this->db->delete('tbl_decision_bonus_discipline');
        if ($success) {

            $this->db->from('tblfiles');
            $this->db->where('tblfiles.rel_id',$id);
            $this->db->where('tblfiles.rel_type','decision_bonus');
            $dtFile = $this->db->get()->result_array();
            if (!empty($dtFile)){
                foreach ($dtFile as $key => $value){
                    $this->db->where('id', $value['id']);
                    $this->db->delete('tblfiles');
                    $fileUnset = FCPATH . 'uploads/decision_bonus_discipline/' . $id . '/' . $value['file_name'];
                    if (file_exists($fileUnset)) {
                        @unlink($fileUnset);
                    }
                }
            }

            insertActivityLog([
                'type_parent_obj' => 'decision_bonus_disciplines',
                'table_obj' => 'tbl_decision_bonus_discipline',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu quyết định khen thưởng-kỷ luật') . ' [' . $dtData['reference_no'] . ']',
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

    public function searchSuggestBonusDisciplines($id = 0)
    {
        $term = $this->input->get('term');
        $type_quota_bonus_disciplines_id = !empty($this->input->get('type_quota_bonus_disciplines_id')) ? $this->input->get('type_quota_bonus_disciplines_id') : 0;
        $object_type = !empty($this->input->get('object_type')) ? $this->input->get('object_type') : null;
        if ($object_type == 'staff') {
            $object_id = !empty($this->input->get('object_id')) ? $this->input->get('object_id') : 0;
        } else {
            $object_id = !empty($this->input->get('object_id_new')) ? $this->input->get('object_id_new') : 0;
        }

        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_suggest_bonus_disciplines.id as id, 
            CONCAT(tbl_suggest_bonus_disciplines.reference_no,"(",tbl_quota_bonus_discipline.name,")") as text
        ', false);
        $this->db->from('tbl_suggest_bonus_disciplines');
        $this->db->join('tbl_quota_bonus_discipline',
            'tbl_quota_bonus_discipline.id = tbl_suggest_bonus_disciplines.quota_bonus_disciplines_id');
        $this->db->where('tbl_suggest_bonus_disciplines.type_quota_bonus_disciplines_id',
            $type_quota_bonus_disciplines_id);
        $this->db->where('tbl_suggest_bonus_disciplines.status', 1);
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_suggest_bonus_disciplines.reference_no', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $dtData = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Phiếu yêu cầu khen thưởng-kỷ luật'), 'children' => $dtData];
        if (!empty($id)) {
            $this->db->select('tbl_suggest_bonus_disciplines.*,tbl_quota_bonus_discipline.name as name_quota');
            $this->db->from('tbl_suggest_bonus_disciplines');
            $this->db->join('tbl_quota_bonus_discipline',
                'tbl_quota_bonus_discipline.id = tbl_suggest_bonus_disciplines.quota_bonus_disciplines_id');
            $this->db->where('tbl_suggest_bonus_disciplines.id', $id);
            $dtDataRow = $this->db->get()->row_array();
            $data['row'] = [
                'id' => $dtDataRow['id'],
                'text' => $dtDataRow['reference_no'] . '(' . $dtDataRow['name_quota'] . ')'
            ];
        }
        echo json_encode($data);
    }

    public function searchKPI($id = 0)
    {
        $term = $this->input->get('term');
        $suggest_bonus_discipline_id = !empty($this->input->get('suggest_bonus_discipline_id')) ? $this->input->get('suggest_bonus_discipline_id') : 0;
        $limit = get_option('select2_limit');

        $this->db->select('tbl_suggest_bonus_disciplines.*');
        $this->db->from('tbl_suggest_bonus_disciplines');
        $this->db->where('tbl_suggest_bonus_disciplines.id',$suggest_bonus_discipline_id);
        $dtSuggest = $this->db->get()->row_array();

        $object_type = '';
        $object_id = 0;
        if (!empty($dtSuggest)){
            $object_type = $dtSuggest['object_type'];
            $object_id = $dtSuggest['object_id'];
        }
        if ($object_type == 'staff'){
            $object_type = 1;
        } elseif ($object_type == 'department'){
            $object_type = 2;
        }

        $this->db->select('
            tbl_kpi.id as id, 
            tbl_kpi.reference_no as text
        ', false);
        $this->db->from('tbl_kpi');
        $this->db->where('tbl_kpi.type_kpi',$object_type);
        $this->db->where('tbl_kpi.staff', $object_id);
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_kpi.reference_no', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $dtData = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Phiếu KPI'), 'children' => $dtData];
        if (!empty($id)) {
            $this->db->select('tbl_kpi.*');
            $this->db->from('tbl_kpi');
            $this->db->where('tbl_kpi.id', $id);
            $dtDataRow = $this->db->get()->row_array();
            $data['row'] = [
                'id' => $dtDataRow['id'],
                'text' => $dtDataRow['reference_no']
            ];
        }
        echo json_encode($data);
    }

    public function getDataSuggestBonus()
    {
        $data = [];
        $suggest_bonus_discipline_id = $this->input->post('suggest_bonus_discipline_id');

        $this->db->select('tbl_suggest_bonus_disciplines.*,
           IF(tbl_suggest_bonus_disciplines.object_type = "department",tbldepartments.name,CONCAT(tblstaff.firstname," ",tblstaff.lastname)) as object_name,
        ');
        $this->db->from('tbl_suggest_bonus_disciplines');
        $this->db->join('tblstaff',
            'tblstaff.staffid = tbl_suggest_bonus_disciplines.object_id AND tbl_suggest_bonus_disciplines.object_type = "staff"',
            'left');
        $this->db->join('tbldepartments',
            'tbldepartments.departmentid = tbl_suggest_bonus_disciplines.object_id AND tbl_suggest_bonus_disciplines.object_type = "department"',
            'left');
        $this->db->where('tbl_suggest_bonus_disciplines.id', $suggest_bonus_discipline_id);
        $dtData = $this->db->get()->row_array();
        $data['dtData'] = $dtData;
        echo json_encode($data);
    }

}