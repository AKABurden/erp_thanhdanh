<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Kpi extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('kpi_model');
        $this->load->model('misc_model');
        $this->types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
        $this->upload_path = get_upload_path_by_type('evaluate');
        $this->datetime_now = time();
        $this->tnh = true;

        $this->perViewKpiCriteria = has_permission('kpi_criteria', '', 'view');
        $this->perViewOwnKpiCriteria = has_permission('kpi_criteria', '', 'view_own');
        $this->perAddKpiCriteria = has_permission('kpi_criteria', '', 'create');
        $this->perEditKpiCriteria = has_permission('kpi_criteria', '', 'edit');
        $this->perDeleteKpiCriteria = has_permission('kpi_criteria', '', 'delete');

        $this->perViewKpi = has_permission('kpi', '', 'view');
        $this->perViewOwnKpi = has_permission('kpi', '', 'view_own');
        $this->perAddKpi = has_permission('kpi', '', 'create');
        $this->perEditKpi = has_permission('kpi', '', 'edit');
        $this->perDeleteKpi = has_permission('kpi', '', 'delete');
    }

    public function criteria() {
        $data = [];

        if (!$this->perViewKpiCriteria && !$this->perViewOwnKpiCriteria) {
            accessDenied();
        }

        $data['staffs'] = $this->site_model->getStaffAll();
        $data['departments'] = $this->kpi_model->getDepartments();
        $data['roles'] = $this->kpi_model->getRoles();
        $data['title'] = lang('tnh_kpi_criteria');
        $this->load->view('admin/kpi/criteria', $data);
    }

    public function handling_criteria($id = 0) {
        if (empty($id) && empty($this->perAddKpiCriteria)) {
            accessDenied(true);
        }

        if (!empty($id) && empty($this->perEditKpiCriteria)) {
            accessDenied(true);
        }

        $data = [];
        $kpi_criteria = $id ? $this->kpi_model->getKpiCriteriaById($id) : [];
        if ($this->input->post('save')) {
            $this->form_validation->set_rules('code_criteria', lang("Mã tiêu chí"), 'required');
			if (!empty($kpi_criteria) && $kpi_criteria['code_criteria'] != $this->input->post('code_criteria') || empty($id)) {
				$this->form_validation->set_rules('code_criteria', lang("Mã tiêu chí"), 'required|is_unique[tbl_kpi_criteria.code_criteria]');
			}
            $this->form_validation->set_rules('criteria', lang("tnh_criteria"), 'required');
            // $this->form_validation->set_rules('staff', lang("staff"), 'required');
            $this->form_validation->set_rules('end_date', lang("end_date"), 'required');
            $this->form_validation->set_rules('start_date', lang("start_date"), 'required');
            // $this->form_validation->set_rules('department[]', lang("department"), 'required');
            // $this->form_validation->set_rules('role[]', lang("role"), 'required');
            if ($this->form_validation->run() == true)
            {
                $code_criteria = $this->input->post('code_criteria');
                $criteria = $this->input->post('criteria');
                $note_criteria = $this->input->post('note_criteria');
                $unit = $this->input->post('unit');
                $target = $this->input->post('target');
                $department = $this->input->post('department');
                $role = $this->input->post('role');
                $staff = $this->input->post('staff');
                $_department = $this->input->post('_department');
                $type = $this->input->post('type');
                $start_date = $this->input->post('start_date') ? to_sql_date($this->input->post('start_date')) : null;
                $end_date = $this->input->post('end_date') ? to_sql_date($this->input->post('end_date')) : null;

                $weight_number = number_unformat($this->input->post('weight_number'));
                $not_reached = $this->input->post('not_reached');
                $not_reached_from = ($this->input->post('not_reached_from'));
                $not_reached_to = number_unformat($this->input->post('not_reached_to'));
                if ($not_reached != 4) $not_reached_to = 0;

                $need_keep_trying = $this->input->post('need_keep_trying');
                $need_keep_trying_from = ($this->input->post('need_keep_trying_from'));
                $need_keep_trying_to = number_unformat($this->input->post('need_keep_trying_to'));
                if ($need_keep_trying != 4) $need_keep_trying_to = 0;

                $obtain = $this->input->post('obtain');
                $obtain_from = ($this->input->post('obtain_from'));
                $obtain_to = number_unformat($this->input->post('obtain_to'));
                if ($obtain != 4) $obtain_to = 0;

                $pass = $this->input->post('pass');
                $pass_from = ($this->input->post('pass_from'));
                $pass_to = number_unformat($this->input->post('pass_to'));
                if ($pass != 4) $pass_to = 0;

                // if (empty($department)) {
                //     $data['result'] = 0;
                //     $data['message'] = lang('Vui lòng chọn phòng ban');
                //     echo json_encode($data); die;
                // }

                // if (empty($role)) {
                //     $data['result'] = 0;
                //     $data['message'] = lang('Vui lòng chọn chức vụ');
                //     echo json_encode($data); die;
                // }

				if (empty($code_criteria)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Vui lòng nhập mã tiêu chí');
                    echo json_encode($data); die;
                }

                $behavior_discipline = $this->input->post('behavior_discipline');
                if ($type == 1 && empty($staff) && empty($behavior_discipline)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Vui lòng chọn nhân viên');
                    echo json_encode($data); die;
                }

                if ($type == 1 && empty($staff) && empty($behavior_discipline)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Vui lòng chọn nhân viên');
                    echo json_encode($data); die;
                }

                if ($type == 2 && empty($_department) && empty($behavior_discipline)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Vui lòng chọn phòng ban');
                    echo json_encode($data); die;
                }

                if (empty($start_date) || empty($end_date)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Vui lòng nhập ngày bắt đầu và kết thúc');
                    echo json_encode($data); die;
                }

                if ($type != 1 && $type != 2) {
                    $data['result'] = 0;
                    $data['message'] = lang('Không đúng loại');
                    echo json_encode($data); die;
                }

                $option = [
                    'code_criteria' => $code_criteria,
                    'criteria' => $criteria,
                    'unit' => $unit,
                    'target' => $target,
                    'weight_number' => $weight_number,
                    'not_reached' => $not_reached,
                    'not_reached_from' => $not_reached_from,
                    'not_reached_to' => $not_reached_to,
                    'need_keep_trying' => $need_keep_trying,
                    'need_keep_trying_from' => $need_keep_trying_from,
                    'need_keep_trying_to' => $need_keep_trying_to,
                    'obtain' => $obtain,
                    'obtain_from' => $obtain_from,
                    'obtain_to' => $obtain_to,
                    'pass' => $pass,
                    'pass_from' => $pass_from,
                    'pass_to' => $pass_to,
                    'note_criteria' => !empty($note_criteria) ? $note_criteria : NULL,
                    'staff' => $type == 1 ? $staff : $_department,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'behavior_discipline' => $behavior_discipline,
                    'type' => $type,
                ];

                if ($id) {
                    $ins = $this->kpi_model->updateKpiCriteria($id, $option);
                    $kpi_criteria_id = $id;
                } else {
                    $ins = $this->kpi_model->insertKpiCriteria($option);
                    $kpi_criteria_id = $ins;
                }

                if (!empty($ins)) {

                    if ($id) {
                        $this->kpi_model->deleteKpiCriteriaDepartment($kpi_criteria_id);
                        $this->kpi_model->deleteKpiCriteriaRoles($kpi_criteria_id);
                    }

                    if (!empty($department)) {
                        $arrDepartment = [];
                        foreach ($department as $key => $value) {
                            $arrDepartment[] = [
                                'kpi_criteria_id' => $kpi_criteria_id,
                                'department_id' => $value,
                            ];
                        }
                        $this->kpi_model->insertBatchKpiCriteriaDepartment($arrDepartment);
                    }

                    if (!empty($role)) {
                        $arrRole = [];
                        foreach ($role as $key => $value) {
                            $arrRole[] = [
                                'kpi_criteria_id' => $kpi_criteria_id,
                                'role_id' => $value,
                            ];
                        }
                        $this->kpi_model->insertBatchKpiCriteriaRoles($arrRole);
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
        }

        $data['staffs'] = $this->site_model->getStaffAll();
        $data['kpi_criteria'] = $kpi_criteria;
        $data['departments'] = $this->kpi_model->getDepartments();
        $data['roles'] = $this->kpi_model->getRoles();
        $data['id'] = $id;
        $data['title'] = $id ? lang('tnh_edit_kpi_criteria') : lang('tnh_add_kpi_criteria');
        $this->load->view('admin/kpi/handling_criteria', $data);
    }

    public function getKpiCriteria() {
        if (!$this->perViewKpiCriteria && !$this->perViewOwnKpiCriteria) {
            die;
        }

        $department_search = $this->input->post('department_search');
        $role_search = $this->input->post('role_search');
        $staff_search = $this->input->post('staff_search');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');

        $tbDepartment = "(
            SELECT
                tbl_kpi_criteria_department.kpi_criteria_id as kpi_criteria_id,
                GROUP_CONCAT(tbldepartments.name SEPARATOR '<br>') as name_department
            FROM tbl_kpi_criteria_department
            INNER JOIN tbldepartments ON tbldepartments.departmentid = tbl_kpi_criteria_department.department_id
            GROUP BY tbl_kpi_criteria_department.kpi_criteria_id
        ) tb_department";

        $tbRole = "(
            SELECT
                tbl_kpi_criteria_roles.kpi_criteria_id as kpi_criteria_id,
                GROUP_CONCAT(tblroles.name SEPARATOR '<br>') as name_role
            FROM tbl_kpi_criteria_roles
            INNER JOIN tblroles ON tblroles.roleid = tbl_kpi_criteria_roles.role_id
            GROUP BY tbl_kpi_criteria_roles.kpi_criteria_id
        ) tb_role";

        $aColumns = [
            'tbl_kpi_criteria.id as id',
            'tbl_kpi_criteria.start_date as start_date',
            'tbl_kpi_criteria.end_date as end_date',
            'tbl_kpi_criteria.code_criteria as code_criteria',
            'tbl_kpi_criteria.criteria as criteria',
            'IF (tbl_kpi_criteria.type = 1, CONCAT(tblstaff.firstname, " ", tblstaff.lastname), IF (tbl_kpi_criteria.type = 2, tbldepartments.name, "")) as staff_name',
            'tbl_kpi_criteria.unit as unit',
            'tbl_kpi_criteria.target as target',
            'tbl_kpi_criteria.weight_number as weight_number',
            'tbl_kpi_criteria.note_criteria as note_criteria',
            '"" as actions',
            '"" as info',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_kpi_criteria';
        $where        = [
        ];
        $filter = [
        ];
        
        $join = [
            'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_kpi_criteria.staff AND tbl_kpi_criteria.type = 1',
            'LEFT JOIN tbldepartments ON tbldepartments.departmentid = tbl_kpi_criteria.staff AND tbl_kpi_criteria.type = 2',
            // 'LEFT JOIN '.$tbDepartment.' ON tbl_kpi_criteria.id = tb_department.kpi_criteria_id',
            // 'LEFT JOIN '.$tbRole.' ON tbl_kpi_criteria.id = tb_role.kpi_criteria_id',
        ];

        if (!empty($department_search)) {
            // array_push($where, " AND exists (
            //     SELECT tbl_kpi_criteria_department.id
            //     FROM tbl_kpi_criteria_department
            //     WHERE tbl_kpi_criteria_department.department_id IN (".implode(',', $department_search).") AND tbl_kpi_criteria_department.kpi_criteria_id = tbl_kpi_criteria.id
            // )");
            array_push($where, " AND tbldepartments.departmentid IN (".implode(',', $department_search).")");
        }

        if (!empty($role_search)) {
            array_push($where, " AND exists (
                SELECT tbl_kpi_criteria_roles.id
                FROM tbl_kpi_criteria_roles
                WHERE tbl_kpi_criteria_roles.role_id IN (".implode(',', $role_search).") AND tbl_kpi_criteria_roles.kpi_criteria_id = tbl_kpi_criteria.id
            )");
        }

        if (!empty($staff_search)) {
            array_push($where, " AND tbl_kpi_criteria.staff IN (".implode(',', $staff_search).") ");
            array_push($where, " AND tblstaff.staffid IN (".implode(',', $staff_search).") ");
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search);
            array_push($where, " AND tbl_kpi_criteria.start_date <= '$start_date_search' ");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search);
            array_push($where, " AND tbl_kpi_criteria.end_date >= '$end_date_search' ");
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_kpi_criteria.not_reached as not_reached',
            'tbl_kpi_criteria.not_reached_from as not_reached_from',
            'tbl_kpi_criteria.need_keep_trying as need_keep_trying',
            'tbl_kpi_criteria.need_keep_trying_from as need_keep_trying_from',
            'tbl_kpi_criteria.need_keep_trying_to as need_keep_trying_to',
            'tbl_kpi_criteria.obtain as obtain',
            'tbl_kpi_criteria.obtain_from as obtain_from',
            'tbl_kpi_criteria.obtain_to as obtain_to',
            'tbl_kpi_criteria.pass as pass',
            'tbl_kpi_criteria.pass_from as pass_from',
            'tbl_kpi_criteria.pass_to as pass_to',
            'tbl_kpi_criteria.behavior_discipline as behavior_discipline',
        ], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $id = $aRow['id'];

            $behavior_discipline = $aRow['behavior_discipline'];
            $str_behavior_discipline = '';
            if ($behavior_discipline == 1) {
                $str_behavior_discipline = '<div class="text-danger text-center">'.lang('tnh_behavior_discipline').'</div>';
            }

            $row[0] = $id;
			$row[1] = '<div class="text-center">'.($aRow['start_date'] ? _d($aRow['start_date']) : '').'</div>';
			$row[2] = '<div class="text-center">'.($aRow['end_date'] ? _d($aRow['end_date']) : '').'</div>';
			$row[3] = '<div class="text-center">'.$aRow['code_criteria'].'</div>'.$str_behavior_discipline;
            $row[4] = '<div class="text-center">'.$aRow['criteria'].'</div>';
            $row[5] = '<div class="text-center">'.$aRow['staff_name'].'</div>';
            $row[6] = '<div class="text-center">'.$aRow['unit'].'</div>';
            $row[7] = '<div class="text-center">'.$aRow['target'].'</div>';
            $row[8] = '<div class="text-center">'.$aRow['weight_number'].'</div>';
            $row[9] = '<div class="text-center">'.$aRow['note_criteria'].'</div>';

            $edit = '<a class="tnh-modal" href="' . base_url('admin/kpi/handling_criteria/'.$id) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit_kpi_criteria') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/kpi/delete_criteria/'.$id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('tnh_delete_kpi_criteria') . '</a>';

            if ($behavior_discipline) {
                $delete = '';
            }

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

            $row[10] = $actions;
            $row[11] = '<div class="row">
                <div class="col-md-6">
                    <table class="table table-hover dataTable">
                        <tr style="border-top: 1px solid #cedae6 !important; background: #0e306347 !important;">
                            <td style="width: 130px;" class="text-center">'.lang('name').'</td>
                            <td style="width: 130px;" class="text-center">'.lang('tnh_recipe').'</td>
                            <td class="text-center">'.lang('tnh_measure').'</td>
                        </tr>
                        <tr>
                            <td>1.'.lang('tnh_not_reached').'</td>
                            <td class="text-center">'.($aRow['not_reached'] ? calRecipe($aRow['not_reached']) : '').'</td>
                            <td class="text-center">'.$aRow['not_reached_from'].'</td>
                        </tr>
                        <tr>
                            <td>2.'.lang('tnh_need_keep_trying').'</td>
                            <td class="text-center">'.($aRow['need_keep_trying'] ? calRecipe($aRow['need_keep_trying']) : '').'</td>
                            <td class="text-center">'.$aRow['need_keep_trying_from'].'</td>
                        </tr>
                        <tr>
                            <td>3.'.lang('tnh_obtain').'</td>
                            <td class="text-center">'.($aRow['obtain'] ? calRecipe($aRow['obtain']) : '').'</td>
                            <td class="text-center">'.$aRow['obtain_from'].'</td>
                        </tr>
                        <tr>
                            <td>4.'.lang('tnh_pass').'</td>
                            <td class="text-center">'.($aRow['pass'] ? calRecipe($aRow['pass']) : '').'</td>
                            <td class="text-center">'.$aRow['pass_from'].'</td>
                        </tr>
                    </table>
                </div>
            </div>';
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function delete_criteria($id) {
        $data = [];

        if (!$this->perDeleteKpiCriteria) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die;
        }

        $isKpiCriteria = $this->kpi_model->isKpiCriteria($id);
        if (!empty($isKpiCriteria)) {
            $data['result'] = 0;
            $data['message'] = lang('tnh_exist_not_delete');
            echo json_encode($data); die;
        }

        $kpi = $this->kpi_model->getKpiById($id);
        if ($kpi['behavior_discipline']) {
            $data['result'] = 0;
            $data['message'] = lang('Hệ thống không thể xóa');
            echo json_encode($data); die;
        }

        if ($this->kpi_model->deleteKpiCriteria($id)) {
            $this->kpi_model->deleteKpiCriteriaDepartment($id);
            $this->kpi_model->deleteKpiCriteriaRoles($id);
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function list() {
        $data = [];

        if (!$this->perViewKpi && !$this->perViewOwnKpi) {
            accessDenied();
        }

        $data['departments'] = $this->kpi_model->getDepartments();
        $data['title'] = lang('tnh_kpi_list');
        $data['staffs'] = $this->site_model->getStaffAll();
        $this->load->view('admin/kpi/list_kpi', $data);
    }

    public function handling($id = 0) {
        $data = [];

        if (empty($id) && empty($this->perAddKpi)) {
            accessDenied(true);
        }

        if (!empty($id) && empty($this->perEditKpi)) {
            accessDenied(true);
        }

        $where = [];
        if (!$this->perViewKpi) {
            $where = $this->kpi_model->getWhereKpi($where, true);
        }

        $kpi = $id ? $this->kpi_model->getKpiById($id, $where) : NULL;
        if ($id && empty($kpi)) {
            set_alert('danger', lang('no_data_exists'));
            redirect($_SERVER["HTTP_REFERER"]);
            die;
        }

        if ($this->input->post('save')) {
            if ((!empty($kpi) && $kpi['reference_no'] != $this->input->post('reference_no')) || empty($kpi['reference_no'])) {
                $this->form_validation->set_rules('reference_no', lang("tnh_reference_no"), 'trim|required|is_unique[tbl_kpi.reference_no]');
            }
            // $this->form_validation->set_rules('month', lang("month"), 'required');
            // $this->form_validation->set_rules('year', lang("year"), 'required');
            // $this->form_validation->set_rules('staff', lang("staff"), 'required');
            $this->form_validation->set_rules('start_date', lang("start_date"), 'required');
            $this->form_validation->set_rules('end_date', lang("end_date"), 'required');
            // $this->form_validation->set_rules('target_reception_time', lang("tnh_target_reception_time"), 'required');
            if ($this->form_validation->run() == true) {
                $reference_no = $id ? $kpi['reference_no'] : getReference('kpi');
                // $month = $this->input->post('month');
                // $year = $this->input->post('year');
                $start_date = to_sql_date($this->input->post('start_date'));
                $end_date = to_sql_date($this->input->post('end_date'));
                $staff = $this->input->post('staff');
                $note = $this->input->post('note');
                // $target_reception_time = to_sql_date($this->input->post('target_reception_time'));
                $target_reception_time = null;
                $advantage = $this->input->post('advantage');
                $fix_try = $this->input->post('fix_try');
                $type_kpi = $this->input->post('type_kpi');
                $_department = $this->input->post('_department');
                if ($type_kpi == 1) {
                    if (empty($staff)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Vui lòng chọn nhân viên');
                        echo json_encode($data); die;
                    }
                } else if ($type_kpi == 2) {
                    if (empty($_department)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Vui lòng chọn phòng ban');
                        echo json_encode($data); die;
                    }
                }

                $date_all = date('Y-m-d H:i:s');
                $staff_all = get_staff_user_id();

                $total_result = 0;
                $total_point_no_coefficient = 0;
                $total_point_with_coefficient = 0;
                $total_weight_number = 0;

                $kpi_criteria_id = $this->input->post('kpi_criteria_id');
                $arr_kpi_items = [];
                $grand_total_weight_number = 0;
                if (!empty($kpi_criteria_id)) {
                    foreach ($kpi_criteria_id as $key => $value) {
                        $result = !empty($this->input->post('result')[$key]) ? $this->input->post('result')[$key] : 0;
                        $counter = $this->input->post('counter')[$key];
                        $chonse = !empty($this->input->post('chonse')[$counter]) ? $this->input->post('chonse')[$counter] : 0;
                        $type =  !empty($this->input->post('type')[$key]) ? $this->input->post('type')[$key] : 0;
                        $dtKpiCriteria = $this->kpi_model->getKpiCriteriaById($value);
                        if (empty($dtKpiCriteria)) {
                            continue;
                        }


                        $not_reached = $dtKpiCriteria['not_reached'];
                        $not_reached_from = $dtKpiCriteria['not_reached_from'];
                        $not_reached_to = $dtKpiCriteria['not_reached_to'];
                        $need_keep_trying = $dtKpiCriteria['need_keep_trying'];
                        $need_keep_trying_from = $dtKpiCriteria['need_keep_trying_from'];
                        $need_keep_trying_to = $dtKpiCriteria['need_keep_trying_to'];
                        $obtain = $dtKpiCriteria['obtain'];
                        $obtain_from = $dtKpiCriteria['obtain_from'];
                        $obtain_to = $dtKpiCriteria['obtain_to'];
                        $pass = $dtKpiCriteria['pass'];
                        $pass_from = $dtKpiCriteria['pass_from'];
                        $pass_to = $dtKpiCriteria['pass_to'];

                        $point_no_coefficient = 0;
                        $point_with_coefficient = 0;

                        if ($not_reached == 1  && $result > $not_reached_from) {
                            $point_no_coefficient = $point_no_coefficient + 1;
                        } else if ($not_reached == 2  && $result < $not_reached_from) {
                            $point_no_coefficient = $point_no_coefficient + 1;
                        } else if ($not_reached == 3  && $result == $not_reached_from) {
                            $point_no_coefficient = $point_no_coefficient + 1;
                        } else if ($not_reached == 4  && $result >= $not_reached_from && $result >= $not_reached_to) {
                            $point_no_coefficient = $point_no_coefficient + 1;
                        }
            
                        if ($need_keep_trying == 1  && $result > $need_keep_trying_from) {
                            $point_no_coefficient = $point_no_coefficient + 2;
                        } else if ($need_keep_trying == 2  && $result < $need_keep_trying_from) {
                            $point_no_coefficient = $point_no_coefficient + 2;
                        } else if ($need_keep_trying == 3  && $result == $need_keep_trying_from) {
                            $point_no_coefficient = $point_no_coefficient + 2;
                        } else if ($need_keep_trying == 4  && $result >= $need_keep_trying_from && $result >= $need_keep_trying_to) {
                            $point_no_coefficient = $point_no_coefficient + 2;
                        }
            
                        if ($obtain == 1  && $result > $obtain_from) {
                            $point_no_coefficient = $point_no_coefficient + 3;
                        } else if ($obtain == 2  && $result < $obtain_from) {
                            $point_no_coefficient = $point_no_coefficient + 3;
                        } else if ($obtain == 3  && $result == $obtain_from) {
                            $point_no_coefficient = $point_no_coefficient + 3;
                        } else if ($obtain == 4  && $result >= $obtain_from && $result >= $obtain_to) {
                            $point_no_coefficient = $point_no_coefficient + 3;
                        }
            
                        if ($pass == 1  && $result > $pass_from) {
                            $point_no_coefficient = $point_no_coefficient + 4;
                        } else if ($pass == 2  && $result < $pass_from) {
                            $point_no_coefficient = $point_no_coefficient + 4;
                        } else if ($pass == 3  && $result == $pass_from) {
                            $point_no_coefficient = $point_no_coefficient + 4;
                        } else if ($pass == 4  && $result >= $pass_from && $result >= $pass_to) {
                            $point_no_coefficient = $point_no_coefficient + 4;
                        }

                        $target = $dtKpiCriteria['target'];
                        $weight_number = $dtKpiCriteria['weight_number'];

                        //replace item
                        $target = !empty($this->input->post('_target')[$key]) ? $this->input->post('_target')[$key] : '';
                        $weight_number = !empty($this->input->post('_weight_number')[$key]) ? number_unformat($this->input->post('_weight_number')[$key]) : '';
                        $not_reached_from = !empty($this->input->post('_not_reached_from')[$key]) ? $this->input->post('_not_reached_from')[$key] : '';
                        $need_keep_trying_from = !empty($this->input->post('_need_keep_trying_from')[$key]) ? $this->input->post('_need_keep_trying_from')[$key] : '';
                        $obtain_from = !empty($this->input->post('_obtain_from')[$key]) ? $this->input->post('_obtain_from')[$key] : '';
                        $pass_from = !empty($this->input->post('_pass_from')[$key]) ? $this->input->post('_pass_from')[$key] : '';

                        // $point_with_coefficient = $point_no_coefficient * $weight_number;
                        $result = $chonse * $weight_number;
                        $point_with_coefficient = $result;

                        $kpi_item_id = !empty($this->input->post('kpi_item_id')[$key]) ? $this->input->post('kpi_item_id')[$key] : 0;
                        // $violationRecords = $this->kpi_model->getViolationRecords($dtKpiCriteria['id'], $month, $year);

                        if ($type == 0) {
                            $grand_total_weight_number+= $weight_number;
                        }

                        $arr_kpi_items[] = [
                            'id' => $kpi_item_id,
                            'kpi_criteria_id' => $dtKpiCriteria['id'],
                            'result' => $result,
                            'point_no_coefficient' => $point_no_coefficient,
                            'point_with_coefficient' => $point_with_coefficient,

                            'not_reached' => $not_reached,
                            'not_reached_from' => $not_reached_from,
                            'not_reached_to' => $not_reached_to,
                            'need_keep_trying' => $need_keep_trying,
                            'need_keep_trying_from' => $need_keep_trying_from,
                            'need_keep_trying_to' => $need_keep_trying_to,
                            'obtain' => $obtain,
                            'obtain_from' => $obtain_from,
                            'obtain_to' => $obtain_to,
                            'pass' => $pass,
                            'pass_from' => $pass_from,
                            'pass_to' => $pass_to,

                            'target' => $target,
                            'weight_number' => $weight_number,
                            'chonse' => $chonse,
                            'type' => $type,
                            'violation_records' => !empty($violationRecords) ? $violationRecords['count_violation_records'] : 0,
                        ];

                        $total_point_with_coefficient+= $point_with_coefficient;
                        $total_point_no_coefficient+= $point_no_coefficient;
                        $total_weight_number+= $weight_number;
                        $total_result+= $result;
                    }
                }

                // print_arrays($arr_kpi_items);

                if ($grand_total_weight_number != WARNING_WEIGHT_NUMBER_KPI) {
                    $data['result'] = 0;
                    $data['message'] = lang('Tổng trọng số I phải = '.WARNING_WEIGHT_NUMBER_KPI);
                    echo json_encode($data);
                }

                $not_reached = get_option('not_reached');
                $not_reached_from = get_option('not_reached_from');
                $not_reached_to = get_option('not_reached_to');
                $need_keep_trying = get_option('need_keep_trying');
                $need_keep_trying_from = get_option('need_keep_trying_from');
                $need_keep_trying_to = get_option('need_keep_trying_to');
                $obtain = get_option('obtain');
                $obtain_from = get_option('obtain_from');
                $obtain_to = get_option('obtain_to');
                $pass = get_option('pass');
                $pass_from = get_option('pass_from');
                $pass_to = get_option('pass_to');

                //
                $trouble_violation = $this->input->post('trouble_violation');
                $arrKpiTroubleViolation = [];
                $total_violation_point = 0;
                if (!empty($trouble_violation)) {
                    foreach ($trouble_violation as $key => $value) {
                        $kpi_trouble_violation_id = !empty($value['kpi_trouble_violation_id']) ? $value['kpi_trouble_violation_id'] : 0;
                        $trouble_violation_point_id = $value['trouble_violation_point_id'];
                        $violation_point = number_unformat($value['violation_point']);
                        $count_vote = $value['count_vote'];
                        $production_report_id = $value['production_report_id'];
                        $arrProductionReport = [];
                        if (!empty($production_report_id[0])) {
                            $arrProductionReport = explode('|||', $production_report_id);
                        }
                        $arrProductionReport = array_filter($arrProductionReport, function($value){
                            if ($value == null || $value == 'null') {
                                return false;
                            } else {
                                return true;
                            }
                        });

                        $arrKpiTroubleViolation[] = [
                            'id' => $kpi_trouble_violation_id,
                            'trouble_violation_point_id' => $trouble_violation_point_id,
                            'violation_point' => $violation_point,
                            'count_vote' => $count_vote,
                            'arrProductionReport' => $arrProductionReport,
                        ];
                        $total_violation_point+= $violation_point;
                    }
                }

                $bonus = $this->input->post('bonus');
                $arrKpiBonus = [];
                $total_point_bonus = 0;
                if (!empty($bonus)) {
                    foreach ($bonus as $key => $value) {
                        $kpi_bonus_id = !empty($value['kpi_bonus_id']) ? $value['kpi_bonus_id'] : 0;
                        $content_bonus = $value['content'];
                        if (empty($content_bonus)) continue;
                        $point = number_unformat($value['point']);

                        $arrKpiBonus[] = [
                            'id' => $kpi_bonus_id,
                            'content' => $content_bonus,
                            'point' => $point,
                        ];
                        $total_point_bonus+= $point;
                    }
                }

                $point_kpi = ($total_point_with_coefficient - $total_violation_point + $total_point_bonus)/$total_weight_number;
                $result_kpi = 0;
                if ($not_reached == 1  && $point_kpi > $not_reached_from) {
                    $result_kpi = 1;
                } else if ($not_reached == 2  && $point_kpi < $not_reached_from) {
                    $result_kpi = 1;
                } else if ($not_reached == 3  && $point_kpi == $not_reached_from) {
                    $result_kpi = 1;
                } else if ($not_reached == 4  && $point_kpi >= $not_reached_from && $point_kpi <= $not_reached_to) {
                    $result_kpi = 1;
                }
    
                if ($need_keep_trying == 1  && $point_kpi > $need_keep_trying_from) {
                    $result_kpi = 2;
                } else if ($need_keep_trying == 2  && $point_kpi < $need_keep_trying_from) {
                    $result_kpi = 2;
                } else if ($need_keep_trying == 3  && $point_kpi == $need_keep_trying_from) {
                    $result_kpi = 2;
                } else if ($need_keep_trying == 4  && $point_kpi >= $need_keep_trying_from && $point_kpi <= $need_keep_trying_to) {
                    $result_kpi = 2;
                }
    
                if ($obtain == 1  && $point_kpi > $obtain_from) {
                    $result_kpi = 3;
                } else if ($obtain == 2  && $point_kpi < $obtain_from) {
                    $result_kpi = 3;
                } else if ($obtain == 3  && $point_kpi == $obtain_from) {
                    $result_kpi = 3;
                } else if ($obtain == 4  && $point_kpi >= $obtain_from && $point_kpi <= $obtain_to) {
                    $result_kpi = 3;
                }
    
                if ($pass == 1  && $point_kpi > $pass_from) {
                    $result_kpi = 4;
                } else if ($pass == 2  && $point_kpi < $pass_from) {
                    $result_kpi = 4;
                } else if ($pass == 3  && $point_kpi == $pass_from) {
                    $result_kpi = 4;
                } else if ($pass == 4  && $point_kpi >= $pass_from && $point_kpi <= $pass_to) {
                    $result_kpi = 4;
                }

                // print_arrays($result_kpi, '<br>', $point_kpi);
                $option = [
                    'reference_no' => $reference_no,
                    // 'month' => $month,
                    // 'year' => $year,
                    'staff' => $type_kpi == 1 ? $staff : $_department,
                    'note' => $note,
                    'point_kpi' => $point_kpi,
                    'result_kpi' => $result_kpi,

                    'not_reached' => $not_reached,
                    'not_reached_from' => $not_reached_from,
                    'not_reached_to' => $not_reached_to,
                    'need_keep_trying' => $need_keep_trying,
                    'need_keep_trying_from' => $need_keep_trying_from,
                    'need_keep_trying_to' => $need_keep_trying_to,
                    'obtain' => $obtain,
                    'obtain_from' => $obtain_from,
                    'obtain_to' => $obtain_to,
                    'pass' => $pass,
                    'pass_from' => $pass_from,
                    'pass_to' => $pass_to,

                    'total_result' => $total_result,
                    'total_point_no_coefficient' => $total_point_no_coefficient,
                    'total_point_with_coefficient' => $total_point_with_coefficient,
                    'total_weight_number' => $total_weight_number,

                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'target_reception_time' => $target_reception_time,
                    'advantage' => $advantage,
                    'fix_try' => $fix_try,
                    'type_kpi' => $type_kpi,
                    'total_point_bonus' => $total_point_bonus
                ];

                if ($id) {
                    $option['updated_by'] = $staff_all;
                    $option['date_updated'] = $date_all;
                    $rs = $this->kpi_model->updateKpi($id, $option);
                    $kpi_id = $id;
                } else {
                    $option['created_by'] = $staff_all;
                    $option['date_created'] = $date_all;
                    $kpi_id = $this->kpi_model->insertKpi($option);
                }

                if ($kpi_id) {

                    if ($reference_no == getReference('kpi')) {
                        updateReference('kpi');
                    }

                    if ($id) {
                        $this->kpi_model->deleteKpiItems($id);
                        $this->kpi_model->deleteKpiTroubleViolation($id);
                        $this->kpi_model->deleteKpiTroubleViolationItems($id);
                        $this->kpi_model->deleteKpiBonus($id);
                    }

                    if (!empty($arr_kpi_items)) {
                        foreach ($arr_kpi_items as $key => $value) {
                            $arr_kpi_items[$key]['kpi_id'] = $kpi_id;
                        }
                        $this->kpi_model->insertBatchKpiItems($arr_kpi_items);
                    }

                    if (!empty($arrKpiTroubleViolation)) {
                        $arrTroubleViolationItems = [];
                        foreach ($arrKpiTroubleViolation as $key => $value) {
                            $arrProductionReport = $value['arrProductionReport'];
                            unset($value['arrProductionReport']);
                            $value['kpi_id'] = $kpi_id;
                            $kpi_trouble_violation_id = $this->kpi_model->insertKpiTroubleViolation($value);
                            if ($kpi_trouble_violation_id) {
                                if (!empty($arrProductionReport)) {
                                    foreach ($arrProductionReport as $kA => $vA) {
                                        $arrTroubleViolationItems[] = [
                                            'kpi_trouble_violation_id' => $kpi_trouble_violation_id,
                                            'kpi_id' => $kpi_id,
                                            'production_report_id' => $vA,
                                        ];
                                    }
                                }
                            }
                        }

                        if (!empty($arrTroubleViolationItems)) {
                            $this->kpi_model->insertBatchKpiTroubleViolationItems($arrTroubleViolationItems);
                        }
                    }

                    if (!empty($arrKpiBonus)) {
                        foreach ($arrKpiBonus as $key => $value) {
                            $arrKpiBonus[$key]['kpi_id'] = $kpi_id;
                        }
                        $this->kpi_model->insertBatchKpiBonus($arrKpiBonus);
                    }

                    if (empty($id)) {
                        noti_custom('kpi', $kpi_id, $staff_all, 0, '', ['actions' => 'add']);
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
        }

        $data['id'] = $id;
        $data['kpi'] = $kpi;
        $data['staffs'] = $this->site_model->getStaffAll();
        $data['title'] = $id ? lang('tnh_edit_kpi') : lang('tnh_add_kpi');
        $data['breadcrumb'] = [array('link' => base_url('admin/kpi/list'), 'page' => lang('tnh_kpi_list')), array('link' => '#', 'page' => $data['title'])];
        $data['departments'] = $this->kpi_model->getDepartments();
        $this->load->view('admin/kpi/handling', $data);
    }

    public function loadDataKpi() {
        $this->load->view('admin/kpi/load_data_kpi_new');
    }

    public function getListKpi() {

        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $staff = $this->input->post('staff');
        $department_search = $this->input->post('department_search');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');

        $staffDepartments = "(
            SELECT
                tblstaff_departments.staffid as staffid,
                GROUP_CONCAT(tbldepartments.name) as name_department 
            FROM tblstaff_departments
            INNER JOIN tbldepartments ON tbldepartments.departmentid = tblstaff_departments.departmentid
            GROUP BY tblstaff_departments.staffid
        ) tb_staff_departments";

        $tbStaff = "(
            SELECT 
                tblstaff.staffid as staffid,
                tblstaff.firstname as firstname,
                tblstaff.lastname as lastname,
                CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as fullname,
                tblroles.name as name_role,
                tb_staff_departments.name_department as name_department
            FROM tblstaff
            LEFT JOIN tblroles ON tblroles.roleid = tblstaff.role
            LEFT JOIN $staffDepartments ON tb_staff_departments.staffid = tblstaff.staffid
        ) tb_staff";

        $aColumns = [
            'tbl_kpi.id as id',
            'tbl_kpi.start_date as start_date',
            'tbl_kpi.end_date as end_date',
            'tbl_kpi.target_reception_time as target_reception_time',
            'tbl_kpi.type_kpi as type_kpi',
            'tbl_kpi.reference_no as reference_no',
            'IF (tbl_kpi.type_kpi = 1, tb_staff.fullname, IF (tbl_kpi.type_kpi = 2, tbldepartments.name, "")) as staff_name',
            'tbl_kpi.point_kpi as point_kpi',
            'tbl_kpi.result_kpi as result_kpi',
            'tbl_kpi.fix_try as fix_try',
            '"" as actions',
            '"" as info',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_kpi';
        $where        = [
        ];
        $filter = [
        ];

        if (!$this->perViewKpi) {
            $where = $this->kpi_model->getWhereKpi($where);
        }

        if (!empty($month)) {
            array_push($where, ' AND tbl_kpi.month = "'.$month.'"');
        }

        if (!empty($year)) {
            array_push($where, ' AND tbl_kpi.year = "'.$year.'"');
        }

        if (!empty($staff)) {
            array_push($where, ' AND tbl_kpi.staff = "'.$staff.'" AND tbl_kpi.type_kpi = 1 ');
        }

        if (!empty($department_search)) {
            array_push($where, ' AND tbl_kpi.staff = "'.$department_search.'" AND tbl_kpi.type_kpi = 2 ');
        }
        
        $join = [
            'LEFT JOIN '.$tbStaff.' ON tb_staff.staffid = tbl_kpi.staff AND tbl_kpi.type_kpi = 1',
            'LEFT JOIN tbldepartments ON tbldepartments.departmentid = tbl_kpi.staff AND tbl_kpi.type_kpi = 2',
            // 'LEFT JOIN '.$tbDepartment.' ON tbl_kpi_criteria.id = tb_department.kpi_criteria_id',
            // 'LEFT JOIN '.$tbRole.' ON tbl_kpi_criteria.id = tb_role.kpi_criteria_id',
        ];

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search);
            array_push($where, " AND tbl_kpi.start_date <= '$start_date_search' ");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search);
            array_push($where, " AND tbl_kpi.end_date >= '$end_date_search' ");
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tb_staff.name_role as name_role',
            'tb_staff.name_department as name_department',
            'tbl_kpi.advantage as advantage',
            'tbl_kpi.note as note',
            // 'tbl_kpi.month as month',
            // 'tbl_kpi.year as year',
        ], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $id = $aRow['id'];
            $result_kpi = $aRow['result_kpi'];

            $name_role = $aRow['name_role'];
            $name_department = $aRow['name_department'];
            $strStaffInfo = '';
            if (!empty($name_role)) {
                $strStaffInfo.= '<div>Chức vụ: '.$name_role.'</div>';
            }

            if (!empty($name_department)) {
                $strStaffInfo.= '<div>Phòng ban: '.$name_department.'</div>';
            }

            // $row[0] = $aRow['id'];
            $row[0] = $start;
            $row[1] = '<div class="text-center">'.($aRow['start_date'] ? _d($aRow['start_date']) : '').'</div>';
            $row[2] = '<div class="text-center">'.($aRow['end_date'] ? _d($aRow['end_date']) : '').'</div>';
            $row[3] = '<div class="text-center">'.($aRow['target_reception_time'] ? _d($aRow['target_reception_time']) : '').'</div>';
            $row[4] = $aRow['reference_no'];

            $type_kpi = $aRow['type_kpi'];
            $strTypeKpi = '';
            if ($type_kpi == 1) {
                $strTypeKpi.= '<span class="label label-success">'.lang('staff').'</span>';
            } else if ($type_kpi == 2) {
                $strTypeKpi.= '<span class="label label-primary">'.lang('department').'</span>';
            }

            $row[5] = '<div class="text-center">'.$strTypeKpi.'</div>';
            // $row[5] = '<div class="bold">'.$aRow['staff_name'].'</div>'.$strStaffInfo;
            $row[6] = '<div class="bold">'.$aRow['staff_name'].'</div>';
            $row[7] = '<div class="text-center">'.formatNumber($aRow['point_kpi']).'</div>';
            $row[8] = '<div class="text-center">'.(!empty($result_kpi) ? calResult($result_kpi) : '').'</div>';
            $row[9] = $aRow['fix_try'];

            $view = '<a class="tnh-modal" href="' . base_url('admin/kpi/view_kpi/'.$id) . '"><i class="fa fa-file-text-o"></i> ' . lang('tnh_view_kpi') . '</a>';

            $edit = $this->perEditKpi ? '<a class="" href="' . base_url('admin/kpi/handling/'.$id) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit_kpi') . '</a>' : '';
            $delete = $this->perDeleteKpi ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/kpi/delete_kpi/'.$id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('tnh_delete_kpi') . '</a>' : '';

            $actions = '
            <div class="dropdown text-center">
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

            $this->db->select('tbl_kpi_items.*, tbl_kpi_criteria.criteria, tbl_kpi_criteria.unit, tbl_kpi_criteria.target, tbl_kpi_criteria.note_criteria as note_criteria', false);
            $this->db->from('tbl_kpi_items');
            $this->db->join('tbl_kpi_criteria', 'tbl_kpi_criteria.id = tbl_kpi_items.kpi_criteria_id');
            $this->db->where('tbl_kpi_items.kpi_id', $id);
            $kpi_items = $this->db->get()->result_array();
            $trItems = '';
            $total_weight_number = 0;
            $total_point_with_coefficient = 0;
            if (!empty($kpi_items)) {
                foreach ($kpi_items as $key => $value) {

                    $str_not_reached = '';
                    if ($value['not_reached']) {
                        $str_not_reached = calRecipe($value['not_reached']).''.$value['not_reached_from'].($value['not_reached'] == 4 ? ' - '.$value['not_reached_to'] : '');
                    }

                    $str_need_keep_trying = '';
                    if ($value['need_keep_trying']) {
                        $str_need_keep_trying = calRecipe($value['need_keep_trying']).''.$value['need_keep_trying_from'].($value['need_keep_trying'] == 4 ? ' - '.$value['need_keep_trying_to'] : '');
                    }

                    $str_obtain = '';
                    if ($value['obtain']) {
                        $str_obtain = calRecipe($value['obtain']).''.$value['obtain_from'].($value['obtain'] == 4 ? ' - '.$value['obtain_to'] : '');
                    }

                    $str_pass = '';
                    if ($value['pass']) {
                        $str_pass = calRecipe($value['pass']).''.$value['pass_from'].($value['pass'] == 4 ? ' - '.$value['pass_to'] : '');
                    }

                    // $violationRecords = $this->kpi_model->getViolationRecords($value['kpi_criteria_id'], $aRow['month'], $aRow['year']);
                    // <td class="text-center">'.formatNumber($value['point_no_coefficient']).'</td>
                    // <td class="text-center">'.formatNumber($value['point_with_coefficient']).'</td>
                    // <td class="text-center">'.(!empty($violationRecords) ? $violationRecords['count_violation_records'] : 0).'</td>

                    $trItems.= '<tr>
                        <td class="text-center">'.(++$key).'</td>
                        <td>'.$value['criteria'].'</td>
                        <td class="text-center">'.$value['unit'].'</td>
                        <td class="text-center">'.$value['target'].'</td>
                        <td class="text-center">'.$value['weight_number'].'</td>
                        <td class="text-center">'.$str_not_reached.'</td>
                        <td class="text-center">'.$str_need_keep_trying.'</td>
                        <td class="text-center">'.$str_obtain.'</td>
                        <td class="text-center">'.$str_pass.'</td>
                        <td class="text-center">'.formatNumber($value['result']).'</td>
                        <td class="text-center">'.$value['note_criteria'].'</td>
                    </tr>';

                    $total_weight_number+= $value['weight_number'];
                    $total_point_with_coefficient+= $value['point_with_coefficient'];
                }

            }

            // <td class="text-center" rowspan="2">'.lang('Điểm(Chưa hệ số)').'</td>
			// <td class="text-center" rowspan="2">'.lang('Điểm(Có hệ số)').'</td>
			// <td class="text-center" style="width: 100px;" rowspan="2">'.lang('Vi phạm (đã duyệt)').'</td>

            $row[10] = $actions;
            $row[11] = '<div class="row">
                <div class="col-md-12">
                    <table class="table table-hover dataTable">
                        <tr style="border-top: 1px solid #cedae6 !important; background: #0e306347 !important;">
							<td class="text-center" rowspan="2">'.lang('STT').'</td>
							<td class="text-center" rowspan="2" style="width: 200px;">'.lang('Tiêu chí').'</td>
							<td class="text-center" rowspan="2">'.lang('Đvt').'</td>
							<td class="text-center" rowspan="2">'.lang('Mục tiêu').'</td>
							<td class="text-center" rowspan="2">'.lang('tnh_weight_number').'</td>
							<td class="text-center">1.'.lang('tnh_not_reached').'</td>
							<td class="text-center">2.'.lang('tnh_need_keep_trying').'</td>
							<td class="text-center">3.'.lang('tnh_obtain').'</td>
							<td class="text-center">4.'.lang('tnh_pass').'</td>
							<td class="text-center" style="width: 80px;" rowspan="2">'.lang('Kết quả').'</td>
							<td class="text-center" style="width: 130px;" rowspan="2">'.lang('Phương pháp đánh giá').'</td>
						</tr>
						<tr style="background: #bbc5d3;">
                            <td class="text-center">'.lang('(1 điểm)').'</td>
                            <td class="text-center">'.lang('(2 điểm)').'</td>
                            <td class="text-center">'.lang('(3 điểm)').'</td>
                            <td class="text-center">'.lang('(4 điểm)').'</td>
						</tr>
                        '.$trItems.'
                        <tr class="not-tr bold uppercase text-danger" style="background: #bbc5d340;">
                            <td colspan="4" class="text-center">'.lang('tnh_total').'</td>
                            <td class="text-center">'.formatNumber($total_weight_number).'</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="txt-total_point_with_coefficient text-center">'.formatNumber($total_point_with_coefficient).'</td>
                            <td></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row mtop10">
                <div class="col-md-6">
                    <label class="bold">'.lang('Ưu điểm:').'</label>
                    <div>'.$aRow['advantage'].'</div>
                </div>
                <div class="col-md-6">
                    <label class="bold">'.lang('Các nhận xét khác:').'</label>
                    <div>'.$aRow['note'].'</div>
                </div>
            </div>';
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function delete_kpi($id) {
        $data = [];

        if (!$this->perDeleteKpi) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die;
        }

        $where = [];
        if (!$this->perViewKpi) {
            $where = $this->kpi_model->getWhereKpi($where, true);
        }
        $kpi = $this->kpi_model->getKpiById($id, $where);
        if (empty($kpi)) {
            $data['result'] = 0;
            $data['message'] = lang('no_data_exists');
            echo json_encode($data); die;
        }

        if ($this->kpi_model->deleteKpi($id)) {
            $this->kpi_model->deleteKpiItems($id);
            $this->kpi_model->deleteKpiTroubleViolation($id);
            $this->kpi_model->deleteKpiTroubleViolationItems($id);

            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function changeRole() {
        $data = [];
        $department_id = $this->input->post('department_id');
        $department = $this->kpi_model->getRoleDepartment($department_id);
        $data['department'] = $department;
        echo json_encode($data);
    }



	public function modal_import_criteria() {
		$data['title'] = 'Import tiêu chí KPI';
		$this->load->view('admin/kpi/import_criteria', $data);
	}

	public function import_criteria_old() {
		ob_end_clean();
		ini_set('max_execution_time', 800);
		$dataPost = $this->input->post();
		require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
		$this->load->helper('security');

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
			$objReader->setReadDataOnly(true);
			$objPHPExcel = $objReader->load("$fullfile");
			$total_sheets = $objPHPExcel->getSheetCount();
			$allSheetName = $objPHPExcel->getSheetNames();

			$vaKey = '';
			$process = [];
			$listRow = [
				1 => 'code_criteria',
				2 => 'criteria',
				3 => 'unit',
				4 => 'target',
				5 => 'weight_number',
				6 => 'department',
				7 => 'role',

				8 => 'not_reached',
				9 => 'not_reached_from',
				10 => 'not_reached_to',

				11 => 'need_keep_trying',
				12 => 'need_keep_trying_from',
				13 => 'need_keep_trying_to',

				14 => 'obtain',
				15 => 'obtain_from',
				16 => 'obtain_to',

				17 => 'pass',
				18 => 'pass_from',
				19 => 'pass_to',

				20 => 'note_criteria',
			];
			$data = [];
			for ($sheet = 0; $sheet < $total_sheets; $sheet++) {
				$objWorksheet = $objPHPExcel->setActiveSheetIndex($sheet);
				$highestRow = $objWorksheet->getHighestRow();
				$highestColumn = $objWorksheet->getHighestColumn();
				$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
				$vaKey = '';
				for ($i = 3; $i <= $highestRow; $i++) {
					$redata = [];
					for ($j = 1; $j <= $highestColumnIndex; $j++) {
						$Val = $objWorksheet->getCellByColumnAndRow($j, $i)->getValue();
						if(empty($listRow[$j])) {
							continue;
						}
						if($Val == NULL) {
							continue;
						}
						$redata[$listRow[$j]] = trim($Val);
					}
					if(!empty($redata)) {
						$data[] = $redata;
					}
				}
			}
		}


		$count = 0;
		if (!empty($data)) {
			$keyEvent = [
				'>' => 1,
				'<' => 2,
				'=' => 3,
				'><' => 4,
			];
			foreach ($data as $value) {
				$id_departments = [];
				if(!empty($value['department'])) {
					$department = explode(',', $value['department']);
					foreach ($department as $k => $v) {
						$this->db->where('code', trim(trim($v), "\n"));
						$get_departments = $this->db->get('tbldepartments')->row();
						if (!empty($get_departments)) {
							$id_departments[] = $get_departments->departmentid;
						}
					}
				}

				$id_role = [];
				if(!empty($value['role'])) {
					$role = explode(',', $value['role']);
					foreach ($role as $k => $v) {
						$this->db->where('name', trim(trim($v), "\n"));
						$get_role = $this->db->get('tblroles')->row();
						if (!empty($get_role)) {
							$id_role[] = $get_role->roleid;
						}
					}
				}


				$not_reached = !empty($value['not_reached']) ? $keyEvent[$value['not_reached']] : '0';
				if($not_reached < 4) {
					$value['not_reached_to'] = 0;
				}

				$need_keep_trying = !empty($value['need_keep_trying']) ? $keyEvent[$value['need_keep_trying']] : 0;
				if($need_keep_trying < 4) {
					$value['need_keep_trying_to'] = 0;
				}

				$obtain = !empty($value['obtain']) ? $keyEvent[$value['obtain']] : 0;
				if($obtain < 4) {
					$value['obtain_to'] = 0;
				}

				$pass = !empty($value['pass']) ? $keyEvent[$value['pass']] : 0;
				if($pass < 4) {
					$value['pass_to'] = 0;
				}


				$dataImport = [
					'code_criteria' => 	$value['code_criteria'],
					'criteria' => 	$value['criteria'],
					'unit' => 	!empty($value['unit']) ? $value['unit'] : '',
					'target' => 	$value['target'],
					'weight_number' => 	$value['weight_number'],
					'not_reached' => $not_reached,
					'not_reached_to' => !empty($value['not_reached_to']) ? number_format_data($value['not_reached_to'], false) : 0,
					'not_reached_from' => !empty($value['not_reached_from']) ? number_format_data($value['not_reached_from'], false) : 0,

					'need_keep_trying' => $need_keep_trying,
					'need_keep_trying_to' => !empty($value['need_keep_trying_to']) ? number_format_data($value['need_keep_trying_to'], false) : 0,
					'need_keep_trying_from' => !empty($value['need_keep_trying_from']) ? number_format_data($value['need_keep_trying_from'], false) : 0,

					'obtain' => $obtain,
					'obtain_to' => !empty($value['obtain_to']) ? number_format_data($value['obtain_to'], false) : 0,
					'obtain_from' => !empty($value['obtain_from']) ? number_format_data($value['obtain_from'], false) : 0,

					'pass' => $pass,
					'pass_to' => !empty($value['pass_to']) ? number_format_data($value['pass_to'], false) : 0,
					'pass_from' => !empty($value['pass_from']) ? number_format_data($value['pass_from'], false) : 0,
					'note_criteria' => !empty($value['note_criteria']) ? $value['note_criteria'] : NULL,
				];

				$this->db->where('code_criteria', trim($value['code_criteria']));
				$kpi_criteria = $this->db->get('tbl_kpi_criteria')->row();
				if(!empty($kpi_criteria)) {
					continue;
				}


				$success = $this->db->insert('tbl_kpi_criteria', $dataImport);
				if(!empty($success)) {
					$count++;

					$id_kpi_criteria = $this->db->insert_id();

					if(!empty($id_departments)) {
						foreach($id_departments as $k => $v) {
							$this->db->insert('tbl_kpi_criteria_department', [
								'kpi_criteria_id' => $id_kpi_criteria,
								'department_id' => $v,
							]);
						}
					}

					if(!empty($id_role)) {
						foreach($id_role as $k => $v) {
							$this->db->insert('tbl_kpi_criteria_roles', [
								'kpi_criteria_id' => $id_kpi_criteria,
								'role_id' => $v,
							]);
						}
					}
				}

			}
		}
		echo json_encode(
			[
				'success' => (!empty($count) ? $count : false),
				'alert_type' => (!empty($count) ? 'success' : 'danger'),
				'message' => 'Import thành công ' . $count . ' Items',
			]
		);
		die();
	}

    public function import_criteria() {
		ob_end_clean();

        if (!$this->perAddKpiCriteria) {
            accessDenied();
        }

		ini_set('max_execution_time', 800);
		$dataPost = $this->input->post();
		require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
		$this->load->helper('security');

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
			$objReader->setReadDataOnly(true);
			$objPHPExcel = $objReader->load("$fullfile");
			$total_sheets = $objPHPExcel->getSheetCount();
			$allSheetName = $objPHPExcel->getSheetNames();

			$vaKey = '';
			$process = [];
			$listRow = [
				1 => 'start_date',
				2 => 'end_date',
				3 => 'code_criteria',
				4 => 'criteria',
				5 => 'unit',
				6 => 'target',
				7 => 'weight_number',
				8 => 'type',
				9 => 'staff',
				// 7 => 'role',

				10 => 'not_reached',
				11 => 'not_reached_from',
				12 => 'not_reached_to',

				13 => 'need_keep_trying',
				14 => 'need_keep_trying_from',
				15 => 'need_keep_trying_to',

				16 => 'obtain',
				17 => 'obtain_from',
				18 => 'obtain_to',

				19 => 'pass',
				20 => 'pass_from',
				21 => 'pass_to',

				22 => 'note_criteria',
			];
			$data = [];
			for ($sheet = 0; $sheet < $total_sheets; $sheet++) {
				$objWorksheet = $objPHPExcel->setActiveSheetIndex($sheet);
				$highestRow = $objWorksheet->getHighestRow();
				$highestColumn = $objWorksheet->getHighestColumn();
				$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
				$vaKey = '';
				for ($i = 3; $i <= $highestRow; $i++) {
					$redata = [];
					for ($j = 1; $j <= $highestColumnIndex; $j++) {
						// $Val = $objWorksheet->getCellByColumnAndRow($j, $i)->getValue();
						$Val = $objWorksheet->getCellByColumnAndRow($j, $i)->getCalculatedValue();
						if(empty($listRow[$j])) {
							continue;
						}
						if($Val == NULL) {
							continue;
						}
						$redata[$listRow[$j]] = trim($Val);
					}
					if(!empty($redata)) {
						$data[] = $redata;
					}
				}
			}
		}

        // print_arrays($data);
		$count = 0;
        $error = '';
        $row = 2;
		if (!empty($data)) {
			$keyEvent = [
				'>' => 1,
				'<' => 2,
				'=' => 3,
				'><' => 4,
			];
			foreach ($data as $value) {
                $row++;
				$id_departments = [];
				if(!empty($value['department'])) {
					$department = explode(',', $value['department']);
					foreach ($department as $k => $v) {
						$this->db->where('code', trim(trim($v), "\n"));
						$get_departments = $this->db->get('tbldepartments')->row();
						if (!empty($get_departments)) {
							$id_departments[] = $get_departments->departmentid;
						}
					}
				}

				$id_role = [];
				if(!empty($value['role'])) {
					$role = explode(',', $value['role']);
					foreach ($role as $k => $v) {
						$this->db->where('name', trim(trim($v), "\n"));
						$get_role = $this->db->get('tblroles')->row();
						if (!empty($get_role)) {
							$id_role[] = $get_role->roleid;
						}
					}
				}

                if (empty($value['start_date']) || empty($value['end_date']) || empty($value['code_criteria']) || empty($value['criteria']) || empty($value['staff'])) {
                    $error.= '<div class="text-danger">Dòng ['.$row.'] vui lòng nhập mã KPI, tiêu chí, nhân viên, ngày bắt đầu, ngày kết thúc</div>';
                    continue;
                }

                $start_date = $value['start_date'];
                $end_date = $value['end_date'];
                if (gettype($start_date) == 'double' || gettype($start_date) == 'int') {
                    $start_date = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($start_date));
                } elseif (gettype($start_date) == 'string') {
                    $date_format = 'Y-m-d';
                    $start_date = to_sql_date($start_date);
                    $dateCheck = DateTime::createFromFormat($date_format, $start_date);
                    if (!$dateCheck || $dateCheck->format($date_format) !== $start_date || $start_date == '1970-01-01 07:00:00' || $start_date == '1970-01-01') {
                        $error.= '<div>Dòng ['.$row.'] ngày bắt đầu không đúng định dạng</div>';
                        continue;
                    }
                }

                if (gettype($end_date) == 'double' || gettype($end_date) == 'int') {
                    $end_date = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($end_date));
                } elseif (gettype($end_date) == 'string') {
                    $date_format = 'Y-m-d';
                    $end_date = to_sql_date($end_date);
                    $dateCheck = DateTime::createFromFormat($date_format, $end_date);
                    if (!$dateCheck || $dateCheck->format($date_format) !== $end_date || $end_date == '1970-01-01 07:00:00' || $end_date == '1970-01-01') {
                        $error.= '<div>Dòng ['.$row.'] ngày kết thúc không đúng định dạng</div>';
                        continue;
                    }
                }

                if (!empty($value['type']) && ($value['type'] == 1 || $value['type'] == 2)) {
                    $object_type = $value['type'];
                } else {
                    $error.= '<div class="text-danger">Dòng ['.$row.'] không xác định được Nhân viên/Phòng ban</div>';
                    continue;
                }

                if ($object_type == 1) {
                    $staff = $value['staff'];
                    $this->db->select('
                        tblstaff.staffid as staffid,
                    ', false);
                    $this->db->from('tblstaff');
                    $this->db->where('REPLACE(CONCAT(tblstaff.firstname, " ", tblstaff.lastname), " ", "") = "' . trim(str_replace(' ', '', $staff)) . '"', false, false);
                    $dtStaff = $this->db->get()->row_array();
                    if (empty($dtStaff)) {
                        $error.= '<div class="text-danger">Dòng ['.$row.'] không tìm thấy nhân viên ['.$staff.']</div>';
                        continue;
                    }
    
                    $staff_id = $dtStaff['staffid'];
                } else if ($object_type == 2) {
                    $department = $value['staff'];
                    $this->db->select('tbldepartments.departmentid');
                    $this->db->from('tbldepartments');
                    $this->db->where('tbldepartments.code', $department);
                    $dtDepartment = $this->db->get()->row_array();

                    if (empty($dtDepartment)) {
                        $error.= '<div class="text-danger">Dòng ['.$row.'] không tìm thấy Phòng ban ['.$department.']</div>';
                        continue;
                    }
                    $staff_id = $dtDepartment['departmentid'];
                }

				$not_reached = !empty($value['not_reached']) ? $keyEvent[$value['not_reached']] : '0';
				if($not_reached < 4) {
					$value['not_reached_to'] = 0;
				}

				$need_keep_trying = !empty($value['need_keep_trying']) ? $keyEvent[$value['need_keep_trying']] : 0;
				if($need_keep_trying < 4) {
					$value['need_keep_trying_to'] = 0;
				}

				$obtain = !empty($value['obtain']) ? $keyEvent[$value['obtain']] : 0;
				if($obtain < 4) {
					$value['obtain_to'] = 0;
				}

				$pass = !empty($value['pass']) ? $keyEvent[$value['pass']] : 0;
				if($pass < 4) {
					$value['pass_to'] = 0;
				}

				$dataImport = [
					'code_criteria' => 	$value['code_criteria'],
					'criteria' => $value['criteria'],
					'unit' => !empty($value['unit']) ? $value['unit'] : '',
					'target' => $value['target'],
					'weight_number' => $value['weight_number'],
					'not_reached' => $not_reached,
					'not_reached_to' => !empty($value['not_reached_to']) ? number_format_data($value['not_reached_to'], false) : 0,
					'not_reached_from' => !empty($value['not_reached_from']) ? number_format_data($value['not_reached_from'], false) : 0,

					'need_keep_trying' => $need_keep_trying,
					'need_keep_trying_to' => !empty($value['need_keep_trying_to']) ? number_format_data($value['need_keep_trying_to'], false) : 0,
					'need_keep_trying_from' => !empty($value['need_keep_trying_from']) ? number_format_data($value['need_keep_trying_from'], false) : 0,

					'obtain' => $obtain,
					'obtain_to' => !empty($value['obtain_to']) ? number_format_data($value['obtain_to'], false) : 0,
					'obtain_from' => !empty($value['obtain_from']) ? number_format_data($value['obtain_from'], false) : 0,

					'pass' => $pass,
					'pass_to' => !empty($value['pass_to']) ? number_format_data($value['pass_to'], false) : 0,
					'pass_from' => !empty($value['pass_from']) ? number_format_data($value['pass_from'], false) : 0,
					'note_criteria' => !empty($value['note_criteria']) ? $value['note_criteria'] : NULL,
                    'type' => $object_type,
                    'staff' => $staff_id,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
				];

				$this->db->where('code_criteria', trim($value['code_criteria']));
				$kpi_criteria = $this->db->get('tbl_kpi_criteria')->row();
				if(!empty($kpi_criteria)) {
                    $error.= '<div class="text-danger">Dòng ['.$row.'] đã tồn tại mã KPI ['.$value['code_criteria'].']</div>';
					continue;
				}

				$success = $this->db->insert('tbl_kpi_criteria', $dataImport);
				if(!empty($success)) {
					$count++;

					$id_kpi_criteria = $this->db->insert_id();

					if(!empty($id_departments)) {
						foreach($id_departments as $k => $v) {
							$this->db->insert('tbl_kpi_criteria_department', [
								'kpi_criteria_id' => $id_kpi_criteria,
								'department_id' => $v,
							]);
						}
					}

					if(!empty($id_role)) {
						foreach($id_role as $k => $v) {
							$this->db->insert('tbl_kpi_criteria_roles', [
								'kpi_criteria_id' => $id_kpi_criteria,
								'role_id' => $v,
							]);
						}
					}
				}

			}
		}
		echo json_encode(
			[
				'success' => (!empty($count) ? $count : false),
				'alert_type' => (!empty($count) ? 'success' : 'danger'),
				'error' => $error,
				'message' => 'Import thành công ' . $count . ' Items',
			]
		);
		die();
	}

    public function loadKpiCriteria() {
        $data = [];

        $start_date = $this->input->post('start_date') ? to_sql_date($this->input->post('start_date')) : null;
        $end_date = $this->input->post('end_date') ? to_sql_date($this->input->post('end_date')) : null;
        $staff = $this->input->post('staff');
        $type_kpi = $this->input->post('type_kpi');
        $department = $this->input->post('department');

        if (empty($staff)) $staff = 0;
        if (empty($department)) $department = 0;

        $this->db->select('*');
        $this->db->from('tblstaff_departments');
        $this->db->where('tblstaff_departments.staffid', $staff);
        $staff_departments = $this->db->get()->result_array();
        $arrDepartment = [];
        if (!empty($staff_departments)) {
            foreach ($staff_departments as $key => $value) {
                $arrDepartment[] = $value['departmentid'];
            }
        }
        $arrDepartment = array_unique($arrDepartment);

        $strWhere = '';
        if (!empty($arrDepartment)) {
            $strWhere = ' OR (tbl_kpi_criteria.staff IN ('.implode(',', $arrDepartment).') AND tbl_kpi_criteria.type = 2)';
        }

        $this->db->select('tbl_kpi_criteria.*');
        $this->db->from('tbl_kpi_criteria');

        if ($type_kpi == 1) {
            $this->db->where('((tbl_kpi_criteria.staff = '.$staff.' AND tbl_kpi_criteria.type = 1) '.$strWhere.' )', false, false);
        } else if ($type_kpi == 2) {
            $this->db->where('(tbl_kpi_criteria.staff = '.$department.' AND tbl_kpi_criteria.type = 2)', false, false);
        }

        // $this->db->where('tbl_kpi_criteria.start_date <=', $start_date);
        // $this->db->where('tbl_kpi_criteria.end_date >=', $end_date);
        $this->db->where('(tbl_kpi_criteria.behavior_discipline = 0 OR tbl_kpi_criteria.behavior_discipline IS NULL)', false, false);
        $kpi_criteria = $this->db->get()->result_array();
        $data['kpi_criteria'] = $kpi_criteria;
        echo json_encode($data);
    }

    public function loadDataItemKpi() {
        $data = [];

        $criteria_kpi = $this->input->post('criteria_kpi');

        $this->db->select('
            tbl_kpi_criteria.*
        ', false);
        $this->db->from('tbl_kpi_criteria');
        $this->db->where_in('tbl_kpi_criteria.id', $criteria_kpi);
        $kpi_criteria = $this->db->get()->result_array();
        $data['kpi_criteria'] = $kpi_criteria;
        echo json_encode($data);
    }

    public function view_kpi($id) {
        $data = [];
        if (!$this->perViewKpi && !$this->perViewOwnKpi) {
            refererModel(lang('access_denied')); die;
        }

        $where = [];
        if (!$this->perViewKpi) {
            $where = $this->kpi_model->getWhereKpi($where, true);
        }

        $kpi = $id ? $this->kpi_model->getKpiById($id, $where) : NULL;
        if (empty($kpi)) {
            refererModel(lang('no_data_exists')); die;
        }

        $type_kpi = $kpi['type_kpi'];
        $staff = $kpi['staff'];
        $staff_name = '';
        $role = '';
        $department = '';

        if ($type_kpi == 1) {
            $dtStaff = $this->kpi_model->getStaffById($staff);
            if ($dtStaff) {
                $staff_name = $dtStaff['fullname'];
                $role = $dtStaff['name_role'];
                $department = $dtStaff['name_department'];
            }
        } else if ($type_kpi == 2) {
            $dtDepartment = $this->kpi_model->getDepartmentsById($staff);
            if ($dtDepartment) {
                $staff_name = $dtDepartment['name'];
            }
        }

        $data['kpi'] = $kpi;
        $data['id'] = $id;
        $data['staff_name'] = $staff_name;
        $data['role'] = $role;
        $data['department'] = $department;
        $this->load->view('admin/kpi/view_kpi', $data);
    }

    public function loadDataError() {
        $data = [];

        $start_date = $this->input->post('start_date') ? to_sql_date($this->input->post('start_date')).' 00:00:00' : null;
        $end_date = $this->input->post('end_date') ? to_sql_date($this->input->post('end_date')).' 23:59:59' : null;
        $staff = $this->input->post('staff');
        $type_kpi = $this->input->post('type_kpi');
        $department = $this->input->post('department');

        if (empty($staff)) $staff = 0;
        if (empty($department)) $department = 0;

        $wherePR = '';
        if ($type_kpi == 1) {
            $wherePR = ' AND tblproduction_report.responsible_type = "staff" AND tblproduction_report.staff_responsible = '.$staff.' ';
        } else if ($type_kpi == 2) {
            $wherePR = ' AND tblproduction_report.responsible_type = "department" AND tblproduction_report.department_responsible = '.$department.' ';
        }

        if (!empty($start_date)) {
            $wherePR.= ' AND tblproduction_report.date >= "'.$start_date.'" ';
        }

        if (!empty($end_date)) {
            $wherePR.= ' AND tblproduction_report.date <= "'.$end_date.'" ';
        }

        $this->db->simple_query('SET SESSION group_concat_max_len=1844674407370955161');
        $production_report = "(
            SELECT
                tblproduction_report.trouble_violation_point_id as trouble_violation_point_id,
                GROUP_CONCAT(tblproduction_report.id SEPARATOR '|||') as production_report_id, 
                SUM(tblproduction_report.trouble_violation_point) as trouble_violation_point
            FROM tblproduction_report
            WHERE tblproduction_report.trouble_violation_point_id > 0 $wherePR
            GROUP BY tblproduction_report.trouble_violation_point_id
        ) tb_production_report";

        $this->db->select('
            tbltrouble_violation_point.id as id,
            tbltrouble_violation_point.name as name,
            tbltrouble_violation_point.point as point,
            tb_production_report.production_report_id as production_report_id,
            tb_production_report.trouble_violation_point as violation_point,
        ');
        $this->db->from('tbltrouble_violation_point');
        $this->db->join($production_report, 'tb_production_report.trouble_violation_point_id = tbltrouble_violation_point.id', 'left');
        $trouble_violation_point = $this->db->get()->result_array();
        foreach ($trouble_violation_point as $key => $value) {
            $arrProductionReportId = explode('|||', $value['production_report_id']);
            $count_vote = !empty($arrProductionReportId[0]) ? count($arrProductionReportId) : 0;
            $trouble_violation_point[$key]['count_vote'] = $count_vote;
            $trouble_violation_point[$key]['violation_point'] = (float)$value['violation_point'];
        }

        $data['trouble_violation_point'] = $trouble_violation_point;
        echo json_encode($data);
    }

    public function test() {
        noti_custom('kpi', 13, 1, 0, '', ['actions' => 'add']);
    }
}