<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Recruitment extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->preViewRequirements = true;
        $this->preAddRequirements = true;
        $this->preEditRequirements= true;
        $this->preDeleteRequirements = true;

        $this->preViewEprofile = true;
        $this->preAddEprofile = true;
        $this->preEditEprofile = true;
        $this->preDeleteEprofile = true;

        $this->list_priority = [
            '1' => [
                'id' => 1,
                'name' => 'Thường',
                'class' => 'default'
            ],
            '2' => [
                'id' => 2,
                'name' => 'Cao',
                'class' => 'danger',
            ],
            '3' => [
                'id' => 3,
                'name' => 'Khẩn',
                'class' => 'warning',
            ],
        ];
        $this->list_status = [
            'draft' => [
                'id' => 'draft',
                'name' => 'Nháp',
                'name_before' => 'Nháp',
                'class' => 'default'
            ],
            'pending' => [
                'id' => 'pending',
                'name' => 'Chờ Duyệt',
                'name_before' => 'Chờ Duyệt',
                'class' => 'warning'
            ],
            'approved' => [
                'id' => 'approved',
                'name' => 'Đã Duyệt',
                'name_before' => 'Duyệt',
                'class' => 'success'
            ],
            'rejected' => [
                'id' => 'rejected',
                'name' => 'Từ Chối',
                'name_before' => 'Từ Chối',
                'class' => 'danger'
            ],
            'closed' => [
                'id' => 'closed',
                'name' => 'Đã Đóng',
                'name_before' => 'Đóng',
                'class' => 'danger'
            ],
        ];
        $this->list_gender = [
            'male' => [
                'id' => 'male',
                'name' => 'Nam',
            ],
            'female' => [
                'id' => 'female',
                'name' => 'Nữ',
            ]
        ];

        $roleLevel = $this->db->get_where('tbl_role_level')->result_array();
        $this->list_role_level = [];
        foreach($roleLevel as $key => $value) {
            $this->list_role_level[$value['id']] = $value;
        }
//        $this->list_role_level = [
//            'Junior' => [
//                'id' => 'Junior',
//                'name' => 'Junior (Sơ cấp)',
//            ],
//            'Senior' => [
//                'id' => 'Senior',
//                'name' => 'Senior (Cao cấp)',
//            ],
//            'Manager' => [
//                'id' => 'Manager',
//                'name' => 'Manager (Quản lý)',
//            ],
//        ];
        $this->list_source = [
            'topcv' => [
                'id' => 'topcv',
                'name' => 'TopCV',
            ],
            'linkedin' => [
                'id' => 'linkedin',
                'name' => 'LinkedIn',
            ],
            'referral' => [
                'id' => 'referral',
                'name' => 'Nội bộ giới thiệu',
            ],
            'other' => [
                'id' => 'other',
                'name' => 'Khác',
            ],
        ];
        $this->list_marital_status = [
            'alone' => [
                'id' => 'alone',
                'name' => lang('tnh_alone'),
            ],
            'marriage' => [
                'id' => 'marriage',
                'name' => lang('tnh_marriage'),
            ],
            'divorce' => [
                'id' => 'divorce',
                'name' => lang('tnh_divorce'),
            ],
        ];
        $this->list_educational = [
            'no_highschool' => [
                'id'   => 'no_highschool',
                'name' => 'Chưa tốt nghiệp THPT',
            ],
            'highschool' => [
                'id'   => 'highschool',
                'name' => 'Tốt nghiệp THPT',
            ],
            'vocational' => [
                'id'   => 'vocational',
                'name' => 'Chứng chỉ nghề',
            ],
            'intermediate' => [
                'id'   => 'intermediate',
                'name' => 'Trung cấp',
            ],
            'college' => [
                'id'   => 'college',
                'name' => 'Cao đẳng',
            ],
            'university' => [
                'id'   => 'university',
                'name' => 'Đại học',
            ],
            'master' => [
                'id'   => 'master',
                'name' => 'Thạc sĩ',
            ],
            'doctor' => [
                'id'   => 'doctor',
                'name' => 'Tiến sĩ',
            ],
            'student' => [
                'id'   => 'student',
                'name' => 'Đang là sinh viên',
            ],
            'other' => [
                'id'   => 'other',
                'name' => 'Khác',
            ],
        ];

        $this->key_list_educational = [
            'chua_tot_nghiep_thpt' => [
                'id' => 'no_highschool',
                'name' => 'Chưa tốt nghiệp THPT',
            ],
            'tot_nghiep_thpt' => [
                'id' => 'highschool',
                'name' => 'Tốt nghiệp THPT',
            ],
            'chung_chi_nghe' => [
                'id' => 'vocational',
                'name' => 'Chứng chỉ nghề',
            ],
            'trung_cap' => [
                'id' => 'intermediate',
                'name' => 'Trung cấp',
            ],
            'cao_dang' => [
                'id' => 'college',
                'name' => 'Cao đẳng',
            ],
            'dai_hoc' => [
                'id' => 'university',
                'name' => 'Đại học',
            ],
            'thac_si' => [
                'id' => 'master',
                'name' => 'Thạc sĩ',
            ],
            'tien_si' => [
                'id' => 'doctor',
                'name' => 'Tiến sĩ',
            ],
            'dang_la_sinh_vien' => [
                'id' => 'student',
                'name' => 'Đang là sinh viên',
            ],
            'khac' => [
                'id' => 'other',
                'name' => 'Khác',
            ],
        ];

        $this->list_academic_ranking = [
            'good' => [
                'id'   => 'good',
                'name' => 'Giỏi',
            ],
            'rather' => [
                'id'   => 'rather',
                'name' => 'Khá',
            ],
            'medium' => [
                'id'   => 'medium',
                'name' => 'Trung bình',
            ],
            'weak' => [
                'id'   => 'weak',
                'name' => 'Yếu',
            ],
        ];
        $this->key_list_academic_ranking = [
            'gioi' => [
                'id'   => 'good',
                'name' => 'Giỏi',
            ],
            'kha' => [
                'id'   => 'rather',
                'name' => 'Khá',
            ],
            'trung_binh' => [
                'id'   => 'medium',
                'name' => 'Trung bình',
            ],
            'yeu' => [
                'id'   => 'weak',
                'name' => 'Yếu',
            ],
        ];

        $this->type_of_work = [
            'fulltime' => [
                'id' => 'fulltime',
                'name' => 'Fulltime',
            ],
            'partime' => [
                'id' => 'partime',
                'name' => 'Partime',
            ],
            'intern' => [
                'id' => 'intern',
                'name' => 'Intern',
            ],
            'contractor' => [
                'id' => 'contractor',
                'name' => 'Contractor',
            ],
        ];
        $this->working_style = [
            'onsite' => [
                'id' => 'onsite',
                'name' => 'Onsite',
            ],
            'hybird' => [
                'id' => 'hybird',
                'name' => 'Hybird',
            ],
            'remote' => [
                'id' => 'remote',
                'name' => 'Remote',
            ]
        ];
        $this->list_reason = [
            '1' => [
                'id' => '1',
                'name' => 'Thay thế',
                'subtext' => 'Thay thế vị trị'
            ],
            '2' => [
                'id' => '2',
                'name' => 'Mới',
                'subtext' => 'Tuyển mới vị trị'
            ],
        ];


        $this->image_types = 'gif|jpg|jpeg|png|tif';
//        tbl_hr_requirements
    }

    public function requirements()
    {
        if (!$this->preViewRequirements) {
            access_denied('requirements');
        }
        $data['title'] = _l('Phiếu yêu cầu tuyển dụng');
        $this->load->view('admin/recruitment/requirements/manage', $data);
    }

    public function table_requirements()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $typeJobChild = "(
                        SELECT
                            job_detail_id,
                            GROUP_CONCAT(CASE WHEN type = 1 THEN name END) AS type_1,
                            GROUP_CONCAT(CASE WHEN type = 2 THEN name END) AS type_2,
                            GROUP_CONCAT(CASE WHEN type = 3 THEN name END) AS type_3,
                            GROUP_CONCAT(CASE WHEN type = 4 THEN name END) AS type_4
                        FROM tbl_job_detail_child
                        GROUP BY job_detail_id
                    )
        ";

        $aColumns = [
            'tbl_hr_requirements.id as id',
            'tbl_hr_requirements.code as code',
//            'tbl_hr_requirements.name as name',
            'tbl_hr_requirements.date as date',
            'tblbranch.name as name_branch',
            'tbl_room.name as room_name',
            'tblroles.name as role',
            'tbl_hr_requirements.workday as workday',
            'tbl_hr_requirements.priority as priority',// mức độ
            'tbl_hr_requirements.status as status',

//            'tbl_hr_requirements.budget_start as budget_start',// ngân sách
//            'tbl_hr_requirements.deadline as deadline',// ngân sách
//            'CONCAT(tblstaff.firstname, tblstaff.lastname) as staff_name',
//            'tbl_hr_requirements.staff_approve as staff_approve',
//            'tbl_hr_requirements.date_approve as date_approve',
//            'tbl_hr_requirements.note as note',
            '(SELECT COUNT(tbl_hr_eprofile.id) FROM tbl_hr_eprofile WHERE tbl_hr_eprofile.id_requirements = tbl_hr_requirements.id) as total_eprofile',
            '(
                SELECT COUNT(tbl_evaluation_employee.id) 
                FROM tbl_evaluation_employee 
                JOIN tbl_hr_eprofile ON tbl_hr_eprofile.id = tbl_evaluation_employee.staff_id AND tbl_evaluation_employee.type = 2
                WHERE tbl_hr_eprofile.id_requirements = tbl_hr_requirements.id
            ) as total_employee',
            '(
                SELECT COUNT(tbl_propose_offer.id) 
                FROM tbl_propose_offer 
                WHERE tbl_propose_offer.id_yctd = tbl_hr_requirements.id
            ) as total_offer',
            '"" as actions',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_hr_requirements';
        $where = [];
        $join = [
            'LEFT JOIN tbl_job_detail ON tbl_job_detail.role_id = tbl_hr_requirements.role_id AND tbl_job_detail.status = 1',
            'LEFT JOIN tblroles ON tblroles.roleid = tbl_hr_requirements.role_id',
            'LEFT JOIN tbl_room ON tbl_room.id = tblroles.id_room',
            'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_hr_requirements.id_employee',
            'LEFT JOIN tblbranch ON tblbranch.id = tbl_hr_requirements.branch',
        ];
        $join[] = "LEFT JOIN $typeJobChild tcdc ON tcdc.job_detail_id = tbl_job_detail.id";
        if (!empty($this->input->post('filterStatus')) && $this->input->post('filterStatus') != 'all') {
            $where[] = 'AND tbl_hr_requirements.status = "'.$this->input->post('filterStatus').'"';
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'budget_end',
            'tcdc.type_1',
            'tcdc.type_2',
            'tcdc.type_3',
            'tcdc.type_4',
            'tbl_hr_requirements.id_jd',
            'hiring_manager'
        ], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" class="rows-child fa fa-caret-right"></a></div>';
            $row[] = '<div class="text-left"><a class="c_modal" href="' . base_url('admin/recruitment/detail_requirements/' . $aRow['id'].'') . '">'.$aRow['code'].'</a></a></div>';
//            $row[] = '<div class="text-left">'.($aRow['name']).'</div>';
            $row[] = '<div class="text-center">'._dt($aRow['date']).'</div>';
            $row[] = '<div class="text-center">'.($aRow['name_branch'] ?? '').'</div>';
            $row[] = '<div class="text-center">'.$aRow['room_name'].'</div>';
            $row[] = '<div class="text-left text-success"><b>'.$aRow['role'].'</b></div>';
//            $MieuTa = '';
//            if(!empty($aRow['type_1'])) {
//                $MieuTa .= "Trách nhiệm: " . $aRow['type_1'] . PHP_EOL;
//            }
//            if(!empty($aRow['type_2'])) {
//                $MieuTa .= "Phạm vi quyền hạn: " . $aRow['type_2'] . PHP_EOL;
//            }
//            if(!empty($aRow['type_3'])) {
//                $MieuTa .= "Yêu cầu công việc: " . $aRow['type_3'] . PHP_EOL;
//            }
//            if(!empty($aRow['type_4'])) {
//                $MieuTa .= "Tiêu chuẩn năng lực: " . $aRow['type_4'];
//            }
//
//            $row[] = '<div class="text-left" style="  white-space: pre-line;">'.($MieuTa).'</div>';

            $row[] = '<div class="text-center">'._dC($aRow['workday']).'</div>';

            $dataPriority = $this->list_priority[$aRow['priority']];
            $row[] = '<div class="text-center text-'.$dataPriority['class'].'">'.($dataPriority['name'] ?? '').'</div>';


//            $row[] = '<div class="text-center">'.number_format($aRow['budget_start']).' - '.number_format($aRow['budget_end']).'</div>';
//            $row[] = '<div class="text-center">'._dt($aRow['deadline']).'</div>';
            $id = $aRow['id']; // ID động
            if($aRow['status'] == 'draft') {
                $content_html = "<p><a data-id='{$aRow['id']}' value='pending' class='status-agree btn btn-warning'>Chờ duyệt</a><button class='btn po-close'>Thoát</button></p>";
            }
            else if($aRow['status'] == 'pending') {
                $content_html = "<p>
                                    <a data-id='{$aRow['id']}' value='approved' class='status-agree btn btn-success'>Duyệt</a>
                                    <a data-id='{$aRow['id']}' value='rejected' class='status-agree btn btn-danger'>Từ Chối</a>
                                <button class='btn po-close'>Thoát</button></p>";
            }
            else {
                $content_html = '';
            }

            $dataStatus = $this->list_status[$aRow['status']];

            $html = '<div class="text-left">';
            if(empty($content_html)) {
                $html .= '<span ';
                $html .= 'class="label label-' . $dataStatus['class'] . ' po" data-original-title="' . $dataStatus['name'] . '">';
                $html .= $dataStatus['name'];
            }
            else {
                $html .= '<span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" ';
                $html .= 'title="" data-content="' . htmlspecialchars($content_html, ENT_QUOTES, 'UTF-8') . '" ';
                $html .= 'class="label label-' . $dataStatus['class'] . ' po" data-original-title="' . $dataStatus['name'] . '">';
                $html .= $dataStatus['name'];
                $html .= '<i class="fa fa-check task-icon task-unfinished-icon" style="margin-bottom: 3px;padding: 2px;"></i>';
                $html .= '</span>';
            }
            $html .= '</div>';
            $row[] = $html;
//            $row[] = '<div class="text-center">'.($aRow['staff_name']).'</div>';
//
//            $row[] = '<div class="text-center">'.(!empty($aRow['staff_approve']) ? get_staff_full_name($aRow['staff_approve']) : '').'</div>';
//            $row[] = '<div class="text-center">'.(!empty($aRow['date_approve']) ? _dt($aRow['date_approve']) : '').'</div>';
//            $row[] = '<div class="text-center">'.($aRow['note'] ?? '').'</div>';

            $row[] = '<div class="text-center">' . (!empty($aRow['total_eprofile']) ? number_format_data($aRow['total_eprofile']) :  '').'</div>';
            $row[] = '<div class="text-center">' . (!empty($aRow['total_employee']) ? number_format_data($aRow['total_employee']) :  '').'</div>';
            $row[] = '<div class="text-center">' . (!empty($aRow['total_offer']) ? number_format_data($aRow['total_offer']) :  '').'</div>';

            $create_eprofile = '<a class="c_modal" href="' . base_url('admin/recruitment/detail_eprofile/0/' . $aRow['id'].'') . '"><i class="fa fa-plus width-icon-actions"></i> ' . lang('Thêm hồ hơ ứng viên (E-Profile)') . '</a>';
            if($aRow['status'] != 'approved') {
                $create_eprofile = '';
            }

            $edit = '<a class="c_modal" href="' . base_url('admin/recruitment/detail_requirements/' . $aRow['id'].'') . '"><i class="fa fa-edit width-icon-actions"></i> ' . lang('Chỉnh sửa') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/recruitment/delete_requirements/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete')  . '</a>';
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $edit . '</li>
                    <li>' . $create_eprofile . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[] = '<div>' . $actions . '</div>';

            $row['detail_html'] = $this->viewStep($aRow['id'], $aRow['id_jd'], $aRow['hiring_manager']);
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    private function viewStep($id_requirements = 0, $id_jd = 0, $hiring_manager = 0) {
        $list_step = $this->db->get_where('tbl_hr_requirements_step', ['id_requirements' => $id_requirements])->result_array();
        if(!empty($list_step)) {
            $this->db->where('name_table', 'tbl_hr_requirements');
            $category_recommended = $this->db->get_where('tbl_category_recommended')->row();

            if(!empty($id_jd)) {
                $this->db->select('tbl_job_detail.*, tblroles.id_room');
                $this->db->from('tbl_job_detail');
                $this->db->join('tblroles', 'tblroles.roleid = tbl_job_detail.role_id', 'left');
                $this->db->where('tbl_job_detail.id', $id_jd);
                $jobDetail = $this->db->get()->row_array();
            }

            $id_room = $jobDetail['id_room'] ?? 0;

            $liStep = [];
            $liName = [];
            foreach ($list_step as $key => $value) {
                $liStep[] = '<li class="initli" style="list-style-type: none;width: 110px;float: left;font-size: 12px;position: relative;text-align: center;color: #7d7d7d;z-index: 0;font-size: 9px;"></li>';
                $viewEventCreate = '';
                $classStep = '';
                if(empty($value['status'])) {
                    $viewEventCreate = 'onclick="new_task(\''.admin_url('tasks/task?suggest_id='.$value['id_requirements'].'&rel_append_id='.$value['id'].'&id_room='.$id_room.'&category_recommended_id=' . $category_recommended->id).'\')"';
                    if(!is_admin()) {
                        $staffNow = get_staff_user_id();
                        if($staffNow != $hiring_manager) {
                            $viewEventCreate = 'onclick="alert_float(\'danger\', \'Chỉ có nhân viên quản tuyển dụng mới được duyệt tiến trình\')"';
                        }
                    }
                }
                else {
                    $classStep = 'active';
                    $viewEventCreate = 'onclick="init_task_modal('.$value['id_tasks'].'); return false;"';
                }


                if($value['id_step_default'] > 1) {
                    $viewEventCreate = '';
                }
                else {
                    $viewEventCreate = '<div class="text-center" style="font-size: 18px; cursor: pointer;">
                                            <i style="" class="wrap-icon-check fa fa-check-circle-o" '.$viewEventCreate.'></i></a>
                                        </div>
                                        ';
                }

                if($value['id_step_default'] == 2 && !empty($value['status'])) {
                    $this->db->where('tbl_hr_eprofile.id_requirements', $id_requirements);
                    $count_hr_eprofile = $this->db->get('tbl_hr_eprofile')->num_rows();
                    $value['name'] .= ' ('.($count_hr_eprofile ?? 0).' HỒ SƠ)';
                }
                else if($value['id_step_default'] == 3 && !empty($value['status'])) {
                    $this->db->where('tbl_hr_eprofile.id_requirements', $id_requirements);
                    $this->db->join('tbl_hr_eprofile', 'tbl_hr_eprofile.id = tbl_evaluation_employee.staff_id');
                    $this->db->where('tbl_evaluation_employee.type', 2);
                    $count_evaluation = $this->db->get('tbl_evaluation_employee')->num_rows();
                    $value['name'] .= ' ('.($count_evaluation ?? 0).' PHIẾU)';
                }
                else if($value['id_step_default'] == 4 && !empty($value['status'])) {
                    $this->db->where('tbl_propose_offer.id_yctd', $id_requirements);
                    $this->db->join('tbl_propose_offer', ' tbl_propose_offer.id = tbl_checklist_profile.offer_id');
                    $allCheckList = $this->db->get('tbl_checklist_profile')->num_rows();
                    if(!empty($allCheckList)) {
                        $this->db->where('tbl_propose_offer.id_yctd', $id_requirements);
                        $this->db->where('status', ' S8');
                        $this->db->join('tbl_propose_offer', ' tbl_propose_offer.id = tbl_checklist_profile.offer_id');
                        $allS8 = $this->db->get('tbl_checklist_profile')->num_rows();
                        $value['name'] .= ' ('.$allS8 . '/' . $allCheckList.')';
                    }
                }



                $liName[] = '<li style="" class="pointer '.$classStep.'">
								'.$value['name'].'
								<div class="wrap-title-process" style="">
								    '.$viewEventCreate.'
								</div>
							</li>';
            }
            $liName = implode('', $liName);
            $liStep = implode('', $liStep);

            $data = '<div class="display: table; justify-content: center;">
                        <ul class="progressbar" style="display: flex;">' . $liStep . '</ul>
                        <ul class="progressbar" style="display: flex;">' . $liName . '</ul>
                 </div>';
        }
        else {
            $data = '<b class="text-danger">Chưa thiết lập quy trình.</b>';
        }
        return $data;
    }

    public function count_all_requirements() {
        $total = $this->db->count_all('tbl_hr_requirements');
        $data = [
            'all' => $total
        ];
        $list_status = $this->db
            ->select('COUNT(*) as count, status')
            ->group_by('status')
            ->get('tbl_hr_requirements')
            ->result_array();
        foreach($list_status as $key => $value) {
            $data[$value['status']] = $value['count'];
        }

        echo json_encode($data);die();
    }

    public function detail_requirements($id = '') {
        if($this->input->post()) {
            $data = $this->input->post();
            if(!empty($id)) {

                $requirements = $this->db->get_where('tbl_hr_requirements', ['id' => $id])->row();
                if($requirements->status != 'draft' && $requirements->status != 'pending') {
                    echo json_encode([
                        'success' => false,
                        'alert_type' => 'danger',
                        'message' => 'Chỉ được phép chỉnh sửa phiếu yêu cầu tuyển dụng ở trạng thái Nháp hoặc Chờ duyệt'
                    ]);die();
                }
                $dataUpdate = [
                    'name' => $data['name'] ?? '',
                    'role_id' => $data['role_id'],
                    'date' => to_sql_date($data['date']),
                    'id_employee' => $data['id_employee'],
                    'priority' => $data['priority'],
                    'quantity' => $data['quantity'] ?? 1,
                    'budget_start' => number_format_data($data['budget_start'], false),
                    'budget_end' => number_format_data($data['budget_end'], false),
                    'deadline' => !empty($data['deadline']) ? to_sql_date($data['deadline'], true) : NULL,
                    'note' => $data['note'] ?? '',

                    'workday' => !empty($data['workday']) ? to_sql_date($data['workday']) : NULL,
                    'hiring_manager' => $data['hiring_manager'] ?? 0,
                    'branch' => $data['branch'] ?? NULL,
                    'type_of_work' => $data['type_of_work'] ?? NULL,
                    'working_style' => $data['working_style'] ?? NULL,
                    'budget_code' => $data['budget_code'] ?? NULL,
                    'staff_budget' => $data['staff_budget'] ?? NULL,
                    'role_level' => $data['role_level'] ?? NULL,
                    'reason' => $data['reason'] ?? 2,
                ];
                $success = $this->db->update('tbl_hr_requirements', $dataUpdate, ['id' => $id]);
                if(!empty($success)) {
                    echo json_encode([
                        'success' => true,
                        'alert_type' => 'success',
                        'message' => 'Cập nhật phiếu yêu cầu tuyển dụng thành công'
                    ]);die();
                }
                else {
                    echo json_encode([
                        'success' => false,
                        'alert_type' => 'danger',
                        'message' => 'Cập nhật phiếu yêu cầu tuyển dụng thất bại'
                    ]);die();
                }
            }
            else {
                $dataInsert = [
                    'name' => $data['name'] ?? '',
                    'role_id' => $data['role_id'],
                    'date' => to_sql_date($data['date']),
                    'id_employee' => $data['id_employee'],
                    'priority' => $data['priority'],
                    'quantity' => $data['quantity'] ?? 1,
                    'budget_start' => number_format_data($data['budget_start'], false),
                    'budget_end' => number_format_data($data['budget_end'], false),
                    'deadline' => to_sql_date($data['deadline'], true),
                    'note' => to_sql_date($data['note'], true),

                    'workday' => to_sql_date($data['workday']),
                    'hiring_manager' => $data['hiring_manager'] ?? 0,
                    'branch' => $data['branch'] ?? NULL,
                    'type_of_work' => $data['type_of_work'] ?? NULL,
                    'working_style' => $data['working_style'] ?? NULL,
                    'budget_code' => $data['budget_code'] ?? NULL,
                    'staff_budget' => $data['staff_budget'] ?? NULL,
                    'role_level' => $data['role_level'] ?? NULL,
                    'reason' => $data['reason'] ?? 2,
                ];
                $success = $this->db->insert('tbl_hr_requirements', $dataInsert);
                if(!empty($success)) {
                    $id = $this->db->insert_id();
                    $this->db->where('id', $id);
                    $this->db->update('tbl_hr_requirements', [
                        'code' => 'YCTD-' . str_pad($id, 6, '0', STR_PAD_LEFT)
                    ]);
                    echo json_encode([
                        'success' => true,
                        'alert_type' => 'success',
                        'message' => 'Thêm phiếu yêu cầu tuyển dụng thành công'
                    ]);die();
                }
                else {
                    echo json_encode([
                        'success' => false,
                        'alert_type' => 'danger',
                        'message' => 'Thêm phiếu yêu cầu tuyển dụng thất bại'
                    ]);die();
                }
            }
        }
        else {
            if(!empty($id)) {
                $data['requirement'] = $this->db->get_where('tbl_hr_requirements', ['id' => $id])->row_array();
                if($data['requirement']['status'] != 'draft' && $data['requirement']['status'] != 'pending') {
                    $this->view_requirements($id);return;
                }
                $data['title'] = 'Sửa phiếu yêu cầu tuyển dụng';
                $this->db->select('tbl_job_detail.*, tblroles.name as role_name, tbl_room.name as room_name');
                $this->db->from('tbl_job_detail');
                $this->db->join('tblroles','tblroles.roleid = tbl_job_detail.role_id','left');
                $this->db->join('tbl_room', 'tbl_room.id = tblroles.id_room', 'left');
                $this->db->where('tbl_job_detail.role_id', $data['requirement']['role_id']);
                $this->db->where('tbl_job_detail.status', 1);
                $jobDetail = $this->db->get()->row_array();
                if (!empty($jobDetail)){

                    $data['requirement']['type_1'] = $this->db->select('GROUP_CONCAT(name SEPARATOR "<br>") as listname')->get_where('tbl_job_detail_child',['job_detail_id' => $jobDetail['id'], 'type' => 1])->row('listname');// Trách nhiệm
                    $data['requirement']['type_1'] = $data['requirement']['type_1'] ?? 'Chưa cập nhật';

                    $data['requirement']['type_2'] = $this->db->select('GROUP_CONCAT(name SEPARATOR "<br>") as listname')->get_where('tbl_job_detail_child',['job_detail_id' => $jobDetail['id'], 'type' => 2])->row('listname');// Phạm vi quyền hạn
                    $data['requirement']['type_2'] = $data['requirement']['type_2'] ?? 'Chưa cập nhật';

                    $data['requirement']['type_3'] = $this->db->select('GROUP_CONCAT(name SEPARATOR "<br>") as listname')->get_where('tbl_job_detail_child',['job_detail_id' => $jobDetail['id'], 'type' => 3])->row('listname');// yêu cầu công việc
                    $data['requirement']['type_3'] = $data['requirement']['type_3'] ?? 'Chưa cập nhật';

                    $data['requirement']['type_4'] = $this->db->select('GROUP_CONCAT(name SEPARATOR "<br>") as listname')->get_where('tbl_job_detail_child',['job_detail_id' => $jobDetail['id'], 'type' => 4])->row('listname');//tiêu chuẩn năng lực
                    $data['requirement']['type_4'] = $data['requirement']['type_4'] ?? 'Chưa cập nhật';

                    $data['requirement']['role_name'] = $jobDetail['role_name'];
                    $data['requirement']['room_name'] = $jobDetail['room_name'];
                }
            }
            else {
                $data['title'] = 'Thêm phiếu yêu cầu tuyển dụng';
            }
//            $this->db->select('tbl_job_detail.*, CONCAT(title, " (", version, ")") as title_version');
//            $this->db->where('')
//            $data['list_job_detail'] = $this->db->get('tbl_job_detail')->result_array();
            $data['list_staff'] = $this->db->get_where('tblstaff', ['active' => 1])->result_array();


            $this->db->select('tblstaff.staffid, tblstaff.firstname, tblstaff.lastname');
            $this->db->join('tblstaff_departments', 'tblstaff_departments.staffid = tblstaff.staffid', 'left');
            $this->db->join('tbldepartments', 'tbldepartments.departmentid = tblstaff_departments.departmentid', 'left');
            $this->db->join('tbl_room', 'tbl_room.id = tbldepartments.room_id', 'left');
            $this->db->where('tbl_room.code', CODE_MANAGE_HUMAN);
            $data['list_staff_manage_human'] = $this->db->get_where('tblstaff', ['tblstaff.active' => 1])->result_array();

            $data['list_branch'] = $this->db->get_where('tblbranch')->result_array();
            $data['list_staff'] = $this->db->get_where('tblstaff', ['active' => 1])->result_array();
            $data['role_level'] = $this->db->get_where('tbl_role_level')->result_array();

            $data['costs'] = $this->db->get_where('tblcosts', ['type' => (ID_TYPE_COST_REQUIREMENTS ?? 5)])->result_array();
            $data['list_role'] = $this->db->get_where('tblroles', ['active_role' => 1, 'type' => 0])->result_array();
            $this->load->view('admin/recruitment/requirements/detail', $data);
        }
    }

    public function view_requirements($id = '') {

        $data['requirement'] = $this->db->get_where('tbl_hr_requirements', ['id' => $id])->row_array();
        $data['title'] = 'Phiếu yêu cầu tuyển dụng';
        $this->db->select('tbl_job_detail.*, tblroles.name as role_name, tbl_room.name as room_name');
        $this->db->from('tbl_job_detail');
        $this->db->join('tblroles','tblroles.roleid = tbl_job_detail.role_id','left');
        $this->db->join('tbl_room','tbl_room.id = tblroles.id_room','left');
        $this->db->where('tbl_job_detail.role_id', $data['requirement']['role_id']);
        $this->db->where('tbl_job_detail.status', 1);
        $jobDetail = $this->db->get()->row_array();;
        $data['requirement']['job_detail'] = $jobDetail;
        if (!empty($jobDetail)){

            $data['requirement']['type_1'] = $this->db->select('GROUP_CONCAT(name SEPARATOR "<br>") as listname')->get_where('tbl_job_detail_child',['job_detail_id' => $jobDetail['id'], 'type' => 1])->row('listname');// Trách nhiệm
            $data['requirement']['type_1'] = $data['requirement']['type_1'] ?? 'Chưa cập nhật';

            $data['requirement']['type_2'] = $this->db->select('GROUP_CONCAT(name SEPARATOR "<br>") as listname')->get_where('tbl_job_detail_child',['job_detail_id' => $jobDetail['id'], 'type' => 2])->row('listname');// Phạm vi quyền hạn
            $data['requirement']['type_2'] = $data['requirement']['type_2'] ?? 'Chưa cập nhật';

            $data['requirement']['type_3'] = $this->db->select('GROUP_CONCAT(name SEPARATOR "<br>") as listname')->get_where('tbl_job_detail_child',['job_detail_id' => $jobDetail['id'], 'type' => 3])->row('listname');// yêu cầu công việc
            $data['requirement']['type_3'] = $data['requirement']['type_3'] ?? 'Chưa cập nhật';

            $data['requirement']['type_4'] = $this->db->select('GROUP_CONCAT(name SEPARATOR "<br>") as listname')->get_where('tbl_job_detail_child',['job_detail_id' => $jobDetail['id'], 'type' => 4])->row('listname');//tiêu chuẩn năng lực
            $data['requirement']['type_4'] = $data['requirement']['type_4'] ?? 'Chưa cập nhật';

            $data['requirement']['role_name'] = $jobDetail['role_name'];
            $data['requirement']['room_name'] = $jobDetail['room_name'];
            $data['requirement']['data_priority'] = $this->list_priority[$data['requirement']['priority']];
        }
        $this->db->select('tbl_job_detail.*, CONCAT(title, " (", version, ")") as title_version');
        $data['list_job_detail'] = $this->db->get('tbl_job_detail')->result_array();
//        $data['list_staff'] = $this->db->get_where('tblstaff', ['active' => 1])->result_array();

        $data['requirement']['branch_name'] = $this->db->get_where('tblbranch', ['id' => $data['requirement']['branch']])->row('name');
        $data['requirement']['role_level_name'] = $this->db->get_where('tbl_role_level', ['id' => $data['requirement']['role_level']])->row('code');

        $data['requirement']['budget_name'] = $this->db->get_where('tblcosts', ['id' => $data['requirement']['budget_code']])->row('code');
        $data['requirement']['type_of_work_name'] = $this->type_of_work[$data['requirement']['type_of_work']]['name'] ?? '';
        $data['requirement']['working_style_name'] = $this->working_style[$data['requirement']['working_style']]['name'] ?? '';
        $data['requirement']['reason_name'] = $this->list_reason[$data['requirement']['reason']]['name'] ?? '';

        $this->load->view('admin/recruitment/requirements/view', $data);
    }

    public function get_jd_details($id_jd = '') {
        $this->db->select('tbl_job_detail.*, tblroles.name as role_name, tbl_room.name as room_name');
        $this->db->from('tbl_job_detail');
        $this->db->join('tblroles','tblroles.roleid = tbl_job_detail.role_id');
        $this->db->join('tbl_room', 'tbl_room.id = tblroles.id_room', 'left');
        $this->db->where('tbl_job_detail.id',$id_jd);
        $this->db->where('tbl_job_detail.status', 1);
        $jobDetail = $this->db->get()->row_array();
        if (!empty($jobDetail)){
            $jobDetail['type_1'] = $this->db->select('GROUP_CONCAT(name SEPARATOR "<br>") as listname')->get_where('tbl_job_detail_child',['job_detail_id' => $id_jd, 'type' => 1])->row('listname');/// Trách nhiệm
            $jobDetail['type_2'] = $this->db->select('GROUP_CONCAT(name SEPARATOR "<br>") as listname')->get_where('tbl_job_detail_child',['job_detail_id' => $id_jd, 'type' => 2])->row('listname');// Phạm vi quyền hạn
            $jobDetail['type_3'] = $this->db->select('GROUP_CONCAT(name SEPARATOR "<br>") as listname')->get_where('tbl_job_detail_child',['job_detail_id' => $id_jd, 'type' => 3])->row('listname');// yêu cầu công việc
            $jobDetail['type_4'] = $this->db->select('GROUP_CONCAT(name SEPARATOR "<br>") as listname')->get_where('tbl_job_detail_child',['job_detail_id' => $id_jd, 'type' => 4])->row('listname');//tiêu chuẩn năng lực

            $data['result'] = 1;
            $data['data'] = $jobDetail;
        } else {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
        }
        echo json_encode($data);die();
    }

    public function get_jd_to_role_details($id_role = '')
    {
        $this->db->select('tbl_job_detail.*, tblroles.name as role_name, tbl_room.name as room_name');
        $this->db->from('tbl_job_detail');
        $this->db->join('tblroles','tblroles.roleid = tbl_job_detail.role_id');
        $this->db->join('tbl_room', 'tbl_room.id = tblroles.id_room', 'left');
        $this->db->where('tbl_job_detail.role_id',$id_role);
        $this->db->where('tbl_job_detail.status', 1);
        $jobDetail = $this->db->get()->row_array();
        if (!empty($jobDetail)){
            $jobDetail['type_1'] = $this->db->select('GROUP_CONCAT(name SEPARATOR "<br>") as listname')->get_where('tbl_job_detail_child',['job_detail_id' => $jobDetail['id'], 'type' => 1])->row('listname');/// Trách nhiệm
            $jobDetail['type_2'] = $this->db->select('GROUP_CONCAT(name SEPARATOR "<br>") as listname')->get_where('tbl_job_detail_child',['job_detail_id' => $jobDetail['id'], 'type' => 2])->row('listname');// Phạm vi quyền hạn
            $jobDetail['type_3'] = $this->db->select('GROUP_CONCAT(name SEPARATOR "<br>") as listname')->get_where('tbl_job_detail_child',['job_detail_id' => $jobDetail['id'], 'type' => 3])->row('listname');// yêu cầu công việc
            $jobDetail['type_4'] = $this->db->select('GROUP_CONCAT(name SEPARATOR "<br>") as listname')->get_where('tbl_job_detail_child',['job_detail_id' => $jobDetail['id'], 'type' => 4])->row('listname');//tiêu chuẩn năng lực
            $data['result'] = 1;
            $data['alert_type'] = 'success';
            $data['data'] = $jobDetail;
        } else {
            $data['result'] = 0;
            $data['alert_type'] = 'warning';
            $data['message'] = lang('JD vị trí này chưa được tạo');
        }
        echo json_encode($data);die();
    }

    public function change_status_requirements() {
        $id = $this->input->get('id');
        $status = $this->input->get('status');

        $arrayUpdate = ['status' => $status];
        if($status == 'approved' || $status == 'rejected') {
            $arrayUpdate['staff_approve'] = get_staff_user_id();
            $arrayUpdate['date_approve'] = date('Y-m-d H:i:s');
        }
        if(!empty($arrayUpdate)) {
            $this->db->where('id', $id);
            $success = $this->db->update('tbl_hr_requirements', $arrayUpdate);
        }
        if(!empty($success)) {
            if($status == 'approved') {
                $this->db->select('tbl_hr_requirements.*, id_room');
                $this->db->join('tblroles', 'tblroles.roleid = tbl_hr_requirements.role_id');
                $hr_requirements = $this->db->get_where('tbl_hr_requirements', ['tbl_hr_requirements.id' => $id])->row();

                $this->db->where('name_table', 'tbl_hr_requirements');
                $category_recommended = $this->db->get_where('tbl_category_recommended')->row();
                $listStepDefault = $this->db->order_by('step', 'asc')->get('tbl_hr_requirements_default_step')->result_array();

                if(!empty($listStepDefault)) {
                    foreach($listStepDefault as $key => $value) {
                        $this->db->insert('tbl_hr_requirements_step', [
                            'name' => $value['name'],
                            'id_step_default' => $value['id'],
                            'id_requirements' => $id,
                            'status' => 0,
                            'create_by' => get_staff_user_id(),
                        ]);
                        if($value['step'] == 1) {
                            $id_append_id = $this->db->insert_id();
                            $url_task = admin_url('tasks/task?suggest_id='.$id.'&rel_append_id='.$id_append_id.'&id_room='.$hr_requirements->id_room.'&category_recommended_id=' . $category_recommended->id);
                        }

                    }
                }

            }
            echo json_encode([
                'success' => true,
                'alert_type' => 'success',
                'message' => 'Cập nhật trạng thái thành công',
                'url_task' => $url_task ?? false
            ]);die();
        }
        echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Cập nhật trạng thái không thành công']);die();
    }

    public function exportExcelRequirements()
    {
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');

            /* ================= 1. QUERY DATA ================= */

            // Subquery lấy chi tiết JD (Giữ nguyên logic của bạn)
            $typeJobChild = "(
            SELECT
                job_detail_id,
                GROUP_CONCAT(CASE WHEN type = 1 THEN name END SEPARATOR '\n') AS type_1,
                GROUP_CONCAT(CASE WHEN type = 2 THEN name END SEPARATOR '\n') AS type_2,
                GROUP_CONCAT(CASE WHEN type = 3 THEN name END SEPARATOR '\n') AS type_3,
                GROUP_CONCAT(CASE WHEN type = 4 THEN name END SEPARATOR '\n') AS type_4
            FROM tbl_job_detail_child
            GROUP BY job_detail_id
        )";

            $this->db->select('
            tbl_hr_requirements.id,
            tbl_hr_requirements.code,
            tbl_hr_requirements.name as req_name,
            tbl_hr_requirements.date,
            tbl_hr_requirements.priority,
            tbl_hr_requirements.quantity,
            tbl_hr_requirements.budget_start,
            tbl_hr_requirements.budget_end,
            tbl_hr_requirements.deadline,
            tbl_hr_requirements.workday,
            tbl_hr_requirements.status,
            tbl_hr_requirements.note,
            
            tbl_hr_requirements.type_of_work,
            tbl_hr_requirements.working_style,
            tbl_hr_requirements.reason,
            
            CONCAT(tbl_job_detail.code, " (v", tbl_job_detail.version, ")") as jd_version,
            tblroles.name as role_name,
            tbl_room.name as room_name,
            
            tblbranch.name as branch_name,
            tbl_role_level.code as role_level_name,
            tblcosts.code as budget_code_name,
            CONCAT(staff_create.firstname, " ", staff_create.lastname) as staff_name,
            CONCAT(staff_approve.firstname, " ", staff_approve.lastname) as staff_approve_name,
            CONCAT(staff_hiring.firstname, " ", staff_hiring.lastname) as hiring_manager_name,
            CONCAT(staff_budget.firstname, " ", staff_budget.lastname) as budget_owner_name,
            tcdc.type_1,
            tcdc.type_2,
            tcdc.type_3,
            tcdc.type_4
        ');

            $this->db->from('tbl_hr_requirements');

            // Joins
            $this->db->join('tbl_job_detail', 'tbl_job_detail.role_id = tbl_hr_requirements.role_id AND tbl_job_detail.status =1', 'left');
            $this->db->join('tblroles', 'tblroles.roleid = tbl_job_detail.role_id', 'left');
            $this->db->join('tbl_room', 'tbl_room.id = tblroles.id_room', 'left'); // Thêm phòng ban
            $this->db->join('tblbranch', 'tblbranch.id = tbl_hr_requirements.branch', 'left'); // Thêm chi nhánh
            $this->db->join('tbl_role_level', 'tbl_role_level.id = tbl_hr_requirements.role_level', 'left'); // Thêm cấp bậc
            $this->db->join('tblcosts', 'tblcosts.id = tbl_hr_requirements.budget_code', 'left'); // Thêm mã ngân sách

            // Joins Nhân sự (Alias rõ ràng để tránh trùng lặp)
            $this->db->join('tblstaff as staff_create', 'staff_create.staffid = tbl_hr_requirements.id_employee', 'left');
            $this->db->join('tblstaff as staff_approve', 'staff_approve.staffid = tbl_hr_requirements.staff_approve', 'left');
            $this->db->join('tblstaff as staff_hiring', 'staff_hiring.staffid = tbl_hr_requirements.hiring_manager', 'left');
            $this->db->join('tblstaff as staff_budget', 'staff_budget.staffid = tbl_hr_requirements.staff_budget', 'left');

            $this->db->join("$typeJobChild tcdc", 'tcdc.job_detail_id = tbl_job_detail.id', 'left');

            $this->db->order_by('tbl_hr_requirements.id DESC');
            $data = $this->db->get()->result_array();

            /* ================= 2. INIT EXCEL ================= */
            $objPHPExcel = new PHPExcel();
            $sheet = $objPHPExcel->getActiveSheet();
            $sheet->setTitle('DS Yeu Cau Tuyen Dung');

            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => ['name' => 'Times New Roman', 'size' => 11]
            ]);

            /* ================= 3. TITLE ================= */
            // Merge đến cột W (cột cuối cùng)
            $sheet->setCellValue('A1', 'DANH SÁCH CHI TIẾT YÊU CẦU TUYỂN DỤNG');
            $sheet->mergeCells('A1:W1');
            $sheet->getStyle('A1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '0E3063']],
                'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER]
            ]);

            /* ================= 4. HEADER MAPPING ================= */
            $rowHeader = 3;
            $headers = [
                'A' => 'STT',
                'B' => 'Mã phiếu',
                'C' => 'Tên phiếu YC',
                'D' => 'Mã JD / Version',
                'E' => 'Vị trí (Vai trò)',
                'F' => 'Phòng ban',
                'G' => 'Cấp bậc',
                'H' => 'Số lượng',
                'I' => 'Địa điểm (Chi nhánh)',
                'J' => 'Ngày tạo',
                'K' => 'Deadline',
                'L' => 'Ngày nhận việc',
                'M' => 'Người đề xuất',
                'N' => 'Quản lý tuyển dụng',
                'O' => 'Ngân sách (Min - Max)',
                'P' => 'Mã ngân sách',
                'Q' => 'Chủ sở hữu ngân sách',
                'R' => 'Loại hình làm việc',
                'S' => 'Hình thức làm việc',
                'T' => 'Lý do',
                'U' => 'Mô tả chi tiết (JD)',
                'V' => 'Ghi chú',
                'W' => 'Trạng thái',
            ];

            foreach ($headers as $col => $text) {
                $sheet->setCellValue($col . $rowHeader, $text);
                // Auto width sơ bộ
                $sheet->getColumnDimension($col)->setWidth(20);
            }

            // Tinh chỉnh width cho các cột dài
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('C')->setWidth(30); // Tên phiếu
            $sheet->getColumnDimension('U')->setWidth(50); // Mô tả
            $sheet->getColumnDimension('V')->setWidth(30); // Ghi chú

            $sheet->getStyle("A$rowHeader:W$rowHeader")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => [
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ],
                'borders' => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]],
                'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => '4E73DF']] // Màu xanh giống Modal
            ]);

            /* ================= 5. LOOP DATA ================= */
            $row = $rowHeader;
            foreach ($data as $key => $item) {
                $row++;

                // Xử lý Mô tả chi tiết
                $MieuTa = '';
                if (!empty($item['type_1'])) $MieuTa .= "- TRÁCH NHIỆM:\n" . strip_tags($item['type_1']) . "\n\n";
                if (!empty($item['type_2'])) $MieuTa .= "- QUYỀN HẠN:\n" . strip_tags($item['type_2']) . "\n\n";
                if (!empty($item['type_3'])) $MieuTa .= "- YÊU CẦU:\n" . strip_tags($item['type_3']) . "\n\n";
                if (!empty($item['type_4'])) $MieuTa .= "- NĂNG LỰC:\n" . strip_tags($item['type_4']);

                // Mapping dữ liệu từ Array config (giả định biến class có sẵn như trong Controller View)
                $txt_type_of_work = isset($this->type_of_work[$item['type_of_work']]) ? $this->type_of_work[$item['type_of_work']]['name'] : '';
                $txt_working_style = isset($this->working_style[$item['working_style']]) ? $this->working_style[$item['working_style']]['name'] : '';
                $txt_reason = isset($this->list_reason[$item['reason']]) ? $this->list_reason[$item['reason']]['name'] : '';
                $txt_priority = isset($this->list_priority[$item['priority']]) ? $this->list_priority[$item['priority']]['name'] : '';
                $txt_status = isset($this->list_status[$item['status']]) ? $this->list_status[$item['status']]['name'] : '';

                // Đổ dữ liệu
                $sheet->setCellValue("A$row", $key + 1);
                $sheet->setCellValue("B$row", $item['code']);
                $sheet->setCellValue("C$row", $item['req_name']);
                $sheet->setCellValue("D$row", $item['jd_version']);
                $sheet->setCellValue("E$row", $item['role_name']);
                $sheet->setCellValue("F$row", $item['room_name']);
                $sheet->setCellValue("G$row", $item['role_level_name']);
                $sheet->setCellValue("H$row", $item['quantity']);
                $sheet->setCellValue("I$row", $item['branch_name']);
                $sheet->setCellValue("J$row", _dt($item['date']));
                $sheet->setCellValue("K$row", _dt($item['deadline']));
                $sheet->setCellValue("L$row", _dt($item['workday']));
                $sheet->setCellValue("M$row", $item['staff_name']);
                $sheet->setCellValue("N$row", $item['hiring_manager_name']);
                $sheet->setCellValue("O$row", number_format($item['budget_start']) . ' - ' . number_format($item['budget_end']));
                $sheet->setCellValue("P$row", $item['budget_code_name']);
                $sheet->setCellValue("Q$row", $item['budget_owner_name']);
                $sheet->setCellValue("R$row", $txt_type_of_work);
                $sheet->setCellValue("S$row", $txt_working_style);
                $sheet->setCellValue("T$row", $txt_reason);
                $sheet->setCellValue("U$row", $MieuTa);
                $sheet->setCellValue("V$row", $item['note']); // Đã sửa: Lấy từ DB thay vì mảng mapping
                $sheet->setCellValue("W$row", $txt_status);

                // Style cho dòng
                $sheet->getStyle("A$row:W$row")->applyFromArray([
                    'borders' => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]],
                    'alignment' => ['vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP] // Căn trên để dễ đọc text dài
                ]);

                // Wrap text cho cột Mô tả và Ghi chú
                $sheet->getStyle("U$row:V$row")->getAlignment()->setWrapText(true);
            }

            /* ================= 6. OUTPUT ================= */
            $filename = 'danh_sach_yeu_cau_tuyen_dung_' . date('d-m-Y') . '.xls';
            ob_start();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $writer->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();

            echo json_encode([
                'result' => 1,
                'filename' => $filename,
                'message' => 'Xuất dữ liệu thành công!',
                'file' => 'data:application/vnd.ms-excel;base64,' . base64_encode($xlsData)
            ]);
            die;
        }
    }

    public function delete_requirements($id){
        if (!$this->preDeleteRequirements){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->from('tbl_hr_requirements');
        $this->db->where('tbl_hr_requirements.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }

        if($dtData['status'] != 'draft' && $dtData['status'] != 'pending') {
            $data['result'] = 0;
            $data['message'] = 'Chỉ được phép xóa phiếu yêu cầu tuyển dụng ở trạng thái Nháp hoặc Chờ duyệt';
            echo json_encode($data);die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_hr_requirements');
        if ($success) {
            insertActivityLog([
                'type_parent_obj' => 'hr_requirements',
                'table_obj' => 'tbl_hr_requirements',
                'id_obj' => $id,
                'name_obj' => $dtData['code'],
                'content' => lang('Xóa phiếu yêu cầu tuyển dụng') . ' [' . $dtData['code'] . ']',
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

    public function importRequirements()
    {
        $data = [];
        if (!empty($_FILES)){
            ini_set('max_execution_time', 800);
            require_once(APPPATH . 'third_party/PHPExcel/PHPExcel.php');

            $tmpFile = $_FILES['file']['tmp_name'];
            $ext = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, ['XLS', 'XLSX'])) {
                echo json_encode(['success' => false, 'message' => 'File không hợp lệ']);
                die;
            }

            $excel = PHPExcel_IOFactory::load($tmpFile);
            $sheet = $excel->getActiveSheet();
            $highestRow = $sheet->getHighestRow();

            $priorityMap = [
                'Thường' => '1',
                'Cao'    => '2',
                'Khẩn'   => '3'
            ];

            $statusMap = [
                'Draft'     => 'draft',
                'Pending'   => 'pending',
                'Approved'  => 'approved',
                'Rejected'  => 'rejected',
                'Closed'    => 'closed'
            ];

            $count = 0;

            // Dòng 1: header → bắt đầu từ dòng 2
            for ($row = 2; $row <= $highestRow; $row++) {

                $code = trim($sheet->getCell("A$row")->getValue()) ?? NULL;
                $name = trim($sheet->getCell("B$row")->getValue()) ?? NULL;

                $date = $sheet->getCell("C$row")->getValue();
                if(is_numeric($date)) {
                    $unix = ($date - 25569) * 86400;
                    $date = date('Y-m-d', $unix);
                }
                else {
                    $date = to_sql_date($sheet->getCell("C$row")->getValue(), true);
                }


                $reason = trim($sheet->getCell("D$row")->getValue()) ?? NULL;


                /** ---------------- Nhân viên đề xuất ---------------- */
                $id_employee = trim($sheet->getCell("E$row")->getValue());
                $id_employee = $this->db
//                    ->where('CONCAT(firstname," ",lastname) = "'.$staffUsername.'"', false, false)
                    ->where('code', $id_employee)
                    ->get('tblstaff')
                    ->row('staffid');
                if (!$id_employee) continue;


                $role_id = trim($sheet->getCell("F$row")->getValue());
                $role_id = $this->db
                    ->where('code_role', $role_id)
                    ->get('tblroles')
                    ->row('roleid');
                if (!$role_id) continue;


                $hiring_manager = trim($sheet->getCell("G$row")->getValue());
                $hiring_manager = $this->db
//                    ->where('CONCAT(firstname," ",lastname) = "'.$staffUsername.'"', false, false)
                    ->where('code', $hiring_manager)
                    ->get('tblstaff')
                    ->row('staffid');
                if (!$hiring_manager) continue;

                $role_level = trim($sheet->getCell("H$row")->getValue());
                $role_level = $this->db
//                    ->where('CONCAT(firstname," ",lastname) = "'.$staffUsername.'"', false, false)
                    ->where('code', $role_level)
                    ->get('tbl_role_level')
                    ->row('id');
                if (!$role_level) continue;


                $branch = trim($sheet->getCell("I$row")->getValue());
                $branch = $this->db
//                    ->where('CONCAT(firstname," ",lastname) = "'.$staffUsername.'"', false, false)
                    ->where('name', $branch)
                    ->get('tblbranch')
                    ->row('id');
                if (!$branch) continue;

                $priority = $priorityMap[trim($sheet->getCell("J$row")->getValue())] ?? '1';


                $status = $statusMap[trim($sheet->getCell("K$row")->getValue())] ?? 'draft';



                $workday = $sheet->getCell("L$row")->getValue();
                if(is_numeric($workday)) {
                    $unix = ($workday - 25569) * 86400;
                    $workday = date('Y-m-d', $unix);
                }
                else {
                    $workday = to_sql_date($sheet->getCell("L$row")->getValue(), true);
                }

                $deadline = $sheet->getCell("M$row")->getValue();
                if(is_numeric($deadline)) {
                    $unix = ($deadline - 25569) * 86400;
                    $deadline = date('Y-m-d', $unix);
                }
                else {
                    $deadline = to_sql_date($sheet->getCell("M$row")->getValue(), true);
                }


                $type_of_work = $this->type_of_work[strtolower(trim($sheet->getCell("N$row")->getValue()))]['id'] ?? 'fulltime';
                $working_style = $this->working_style[strtolower(trim($sheet->getCell("O$row")->getValue()))]['id'] ?? 'fulltime';

                $budget_code = trim($sheet->getCell("P$row")->getValue());
                $budget_code = $this->db
                    ->where('code', $budget_code)
                    ->get('tblcosts')
                    ->row('id');
                if (!$budget_code) continue;
                $budget_start = trim($sheet->getCell("Q$row")->getValue());
                if(!empty($budget_start)) {
                    $budget_start = number_format_data($budget_start, false);
                }
                $budget_end = trim($sheet->getCell("R$row")->getValue());
                if(!empty($budget_end)) {
                    $budget_end = number_format_data($budget_end, false);
                }
                $note = trim($sheet->getCell("S$row")->getValue());

                $insertData = [
                    'code'          => $code,
                    'name'          => $name,
                    'reason'          => $reason,
                    'date'          => $date,
                    'id_employee'   => $id_employee ?? 0,
                    'hiring_manager'   => $hiring_manager ?? 0,
                    'role_level'   => $role_level ?? 0,
                    'role_id'         => $role_id ?? 0,
                    'branch'         => $branch ?? 0,

                    'priority'      => $priority,
                    'status'        => $status,
                    'workday'        => $workday,
                    'deadline'        => $deadline,
                    'type_of_work'        => $type_of_work,
                    'working_style'        => $working_style,
                    'budget_code'        => $budget_code,
                    'budget_start'   => $budget_start ?? 0,
                    'budget_end'  => $budget_end ?? 0,
                    'note'          => $note,
                ];

                // tránh import trùng mã
                if(!empty($code)) {
                    if ($this->db->where('code', $code)->get('tbl_hr_requirements')->row()) {
                        continue;
                    }
                }
                $success = $this->db->insert('tbl_hr_requirements', $insertData);
                if(!empty($success)) {
                    $id = $this->db->insert_id();
                    if (empty($code)) {
                        $this->db->where('id', $id);
                        $this->db->update('tbl_hr_requirements', [
                            'code' => 'YCTD-' . str_pad($id, 6, '0', STR_PAD_LEFT)
                        ]);
                    }
                }
                $count++;
            }

            echo json_encode([
                'success' => true,
                'message' => 'Import thành công ' . $count . ' yêu cầu tuyển dụng'
            ]);
            die;
        }
        $data['title'] = _l('Import danh sách yêu cầu tuyển dụng');
        $this->load->view('admin/recruitment/requirements/import', $data);
    }




    ////////------------------------------------------------E PROFILE--------------------------------//
    ///

    public function eprofile()
    {
        $data['title'] = 'Quản lý hồ sơ ứng viên';
        $this->load->view('admin/recruitment/eprofile/manage', $data);
    }

    public function table_eprofile()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_hr_eprofile.id as id',
            'tbl_hr_eprofile.role_level as role_level',
            'tbl_hr_eprofile.source as source',
            'tbl_hr_requirements.code as code',
            'tbl_hr_eprofile.avatar as avatar',
            'tbl_hr_eprofile.full_name as full_name',
            'tbl_hr_eprofile.email as email',
            'tbl_hr_eprofile.date_of_birth as date_of_birth',
            'tbl_hr_eprofile.gender as gender',
            'tbl_hr_eprofile.marital_status as marital_status',
            'tbl_hr_eprofile.current_address as current_address',
            'tbl_hr_eprofile.id_card as id_card',
            'tbl_hr_eprofile.date_of_issue as date_of_issue',
            'tbl_hr_eprofile.educational as educational',
            'tbl_hr_eprofile.training_school as training_school',
            'tbl_hr_eprofile.academic_ranking as academic_ranking',
//            'tbl_hr_eprofile.the_company_did as the_company_did',
//            'tbl_hr_eprofile.job_title as job_title',
//            'tbl_hr_eprofile.achievements as achievements',
            'tbl_hr_eprofile.info_other as info_other',
            'tbl_hr_eprofile.hr_note as hr_note',
            'tbl_hr_eprofile.years_of_experience as years_of_experience',
            'tbl_hr_eprofile.expected_salary as expected_salary',
            'tbl_hr_eprofile.cv_link as cv_link',
            'tbl_hr_eprofile.status as status',
//            '(SELECT tbl_evaluation_employee.id FROM tbl_evaluation_employee WHERE tbl_evaluation_employee.staff_id = tbl_hr_eprofile.id AND tbl_evaluation_employee.type = 2) as evaluation_id',
            'tbl_propose_offer.id as id_offer',
            'tbl_probationary_assessment.code as code_assessment',
            '"" as actions',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_hr_eprofile';
        $where = [];
        $join = [
            'INNER JOIN tbl_hr_requirements ON tbl_hr_requirements.id = tbl_hr_eprofile.id_requirements',
            'LEFT JOIN tbl_propose_offer ON tbl_propose_offer.kqpv_id = tbl_hr_eprofile.id',
            'LEFT JOIN tbl_checklist_profile ON tbl_checklist_profile.offer_id = tbl_propose_offer.id',
            'LEFT JOIN tbl_probationary_assessment ON tbl_probationary_assessment.staff_id = tbl_checklist_profile.staff_id',
            'LEFT JOIN tbl_result_checklist ON tbl_result_checklist.id = tbl_probationary_assessment.rating_list',
            'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_checklist_profile.staff_id',
        ];
        if (!empty($this->input->post('filterStatus')) && $this->input->post('filterStatus') != 'all') {
            $where[] = 'AND tbl_hr_requirements.status = "'.$this->input->post('filterStatus').'"';
        }
        if($this->input->post('id_requirements')) {
            $where[] = 'AND tbl_hr_eprofile.id_requirements = '.$this->input->post('id_requirements').'';
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_hr_eprofile.id_requirements',
            'tbl_propose_offer.ma_offer as ma_offer',
            'tbl_propose_offer.trang_thai as trang_thai_offer',
            'tbl_checklist_profile.id as id_check_list',
            'tbl_checklist_profile.ma_checklist as ma_checklist',
            'tbl_checklist_profile.status as status_checklist_profile',
            'tbl_probationary_assessment.id as id_probationary_assessment',
            'tbl_result_checklist.name as name_result_checklist',
            'tbl_result_checklist.color as color_result_checklist',
            'tblstaff.day_in as day_in',
        ], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];

        $arrId = array_column($rResult,'id');
        if (!empty($arrId)){
            $this->db->from('tbl_evaluation_employee');
            $this->db->where_in('tbl_evaluation_employee.staff_id',$arrId);
            $this->db->where('tbl_evaluation_employee.type', 2);
            $dtEvaluation = $this->db->get()->result_array();
            $dtEvaluation = array_reduce($dtEvaluation, function ($acc, $item) {
                $acc[$item['staff_id']][] = $item;
                return $acc;
            });
        }
        foreach ($rResult as $key => $aRow) {
            $row = array();
//            $row[] = '<div class="text-center"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" class="rows-child fa fa-caret-right"></a></div>';
            $row[] = '<div class="text-center">' . ($key + 1).'</div>';
            $row[] = '<div class="text-left"><a class="c_modal" href="' . base_url('admin/recruitment/detail_requirements/' . $aRow['id_requirements'].'') . '">'.$aRow['code'].'</a></a></div>';
            $role_level = $this->list_role_level[$aRow['role_level']] ?? [];
            $row[] = '<div class="text-center">'.($role_level['name'] ?? '').'</div>';

            $source = $this->list_source[$aRow['source']] ?? [];
            $row[] = '<div class="text-center">'.($source['name'] ?? '').'</div>';
            $row[] = '<img src="'.(!empty($aRow['avatar']) ? base_url($aRow['avatar']) : base_url('assets/images/user-placeholder.jpg')).'" class="img img-responsive staff-profile-image-small pull-left">';
            $row[] = '<div class="text-left"><a class="c_modal" href="' . base_url('admin/recruitment/detail_eprofile/' . $aRow['id'].'') . '">'.$aRow['full_name'].'</a></a></div>';
            $row[] = '<div class="text-left">'.($aRow['email'] ?? '').'</div>';
            $row[] = '<div class="text-left">'.(!empty($aRow['date_of_birth']) ? _dC($aRow['date_of_birth']) : '').'</div>';

            $gender = $this->list_gender[$aRow['gender']] ?? [];
            $row[] = '<div class="text-center">'.($gender['name'] ?? '').'</div>';


            $marital_status = $this->list_marital_status[$aRow['marital_status']] ?? [];
            $row[] = '<div class="text-center">'.($marital_status['name'] ?? '').'</div>';
            $row[] = '<div class="text-center">'.($aRow['current_address'] ?? '').'</div>';
            $row[] = '<div class="text-center">'.($aRow['id_card'] ?? '').'</div>';
            $row[] = '<div class="text-left">'.(!empty($aRow['date_of_issue']) ? _dC($aRow['date_of_issue']) : '').'</div>';

            $educational = $this->list_educational[$aRow['educational']] ?? [];
            $row[] = '<div class="text-center">'.($educational['name'] ?? '').'</div>';
            $row[] = '<div class="text-center">'.($aRow['training_school'] ?? '').'</div>';


            $academic_ranking = $this->list_academic_ranking[$aRow['academic_ranking']] ?? [];
            $row[] = '<div class="text-center">'.($academic_ranking['name'] ?? '').'</div>';
//            $row[] = '<div class="text-center">'.($aRow['the_company_did'] ?? '').'</div>';
//            $row[] = '<div class="text-center">'.($aRow['job_title'] ?? '').'</div>';
//            $row[] = '<div class="text-center">'.($aRow['achievements'] ?? '').'</div>';
            $row[] = '<div class="text-center">'.($aRow['info_other'] ?? '').'</div>';
            $row[] = '<div class="text-center">'.($aRow['hr_note'] ?? '').'</div>';
            $row[] = '<div class="text-center">'.($aRow['years_of_experience'] ?? '').'</div>';
            $row[] = '<div class="text-center">'.number_format_data($aRow['expected_salary'] ?? 0).'</div>';
            $row[] = '<div class="text-center">'.(!empty($aRow['cv_link']) ? '<a target="_blank" href="'.$aRow['cv_link'].'">Link</a>' : '').'</div>';

            $status = '<span class="label label-danger" data-original-title="Đã đánh giá">Chưa đánh giá</span>';
            $htmlEvaluation = [];
            $itemEvaluation = $dtEvaluation[$aRow['id']] ?? [];
            if (!empty($itemEvaluation)){
                foreach ($itemEvaluation as $k => $v){
                    $htmlEvaluation[] = '<a target="_blank" class="btn-as" title="'.$v['rating'].'" href="'.admin_url('personnel_assessment/process_evaluate/' . $v['id']).'">'.$v['code'].' ('.$v['point'].'/5)</a>';
                }
            }
            else {
                $htmlEvaluation[] = $status;
            }
            $row[] = '<div class="text-center as-container">'.implode("<br/>", $htmlEvaluation).'</div>';

            $html_offer = '';
            if(!empty($aRow['id_offer'])) {
                $listStatusOffer = [
                    'DRAFT' => 'Nháp',
                    'DANG_CHO_DUYET' => 'Chờ duyệt',
                    'DA_DUYET' => 'Đã duyệt',
                    'DA_GUI' => 'Đã gửi Offer',
                    'CHAP_NHAN' => 'Đã chấp nhận',
                    'TU_CHOI' => 'Đã từ chối'
                ];
                $html_offer = '<a class="tnh-modal btn-as" title="'.$listStatusOffer[$aRow['trang_thai_offer']].'" href="'.admin_url('propose_offer/handling/' . $aRow['id_offer']).'">'.$aRow['ma_offer'].' / <span>'.$listStatusOffer[$aRow['trang_thai_offer']].'</span></a>';
            }
            $row[] = '<div class="text-center as-container">'.$html_offer.'</div>';


            $html_checklist = '';
            if(!empty($aRow['id_check_list'])) {
                $listStatusChecklist = [
                    'S6' => 'S6: Đang Đối Chiếu',
                    'S7' => 'S7: Đã Check',
                    'S8' => 'S8: Thử Việc',
                    'S9' => 'S9: Chính Thức'
                ];
                $html_checklist = '<a title="'.$listStatusChecklist[$aRow['status_checklist_profile']].'" title="'.$aRow['status_checklist_profile'].'" class="tnh-modal btn-as" href="'.admin_url('checklist_profile/handling/' . $aRow['id_check_list']).'">'.$aRow['ma_checklist'].' / <span>'.$listStatusChecklist[$aRow['status_checklist_profile']].'</span></a>';
            }
            $row[] = '<div class="text-center as-container">'.$html_checklist.'</div>';



            $html_code_assessment = [];
            if(!empty($aRow['code_assessment'])) {
                $html_code_assessment[] = '<a target="_blank" class="tnh-modal" href="'.admin_url('probationary_assessment/detail/' . $aRow['id_probationary_assessment']).'">'.$aRow['code_assessment'].'</a>';
            }
            else if(!empty($html_checklist)) {
                $date = new DateTime($aRow['day_in'] ?? date('Y-m-d'));
                $date->modify('+2 weeks');
                $dayWeek = $date->format('Y-m-d');
                $html_code_assessment[] = '<span class="label label-warning" data-original-title="Chưa đánh giá">Đang chờ đánh giá - Ngày ĐG: '._d($dayWeek).'</span>';
            }
            $row[] = '<div class="text-center as-container">'.implode("<br/>", $html_code_assessment).'</div>';


            $htmlResult = '';
            if(!empty($aRow['name_result_checklist'])) {
                $htmlResult = '<span class="label label-default" style="border:1px solid;color:white;background: '.$aRow['color_result_checklist'].'">'.$aRow['name_result_checklist'].'</span>';
            }
            $row[] = '<div class="text-center">'.$htmlResult.'</div>';

            $add_evaluation = '';
            if(empty($itemEvaluation)) {
                $add_evaluation = '<a target="_blank" href="' . base_url('admin/personnel_assessment/detail/0/' . $aRow['id'].'?type=2') . '"><i class="fa fa-plus width-icon-actions"></i> ' . lang('Tạo phiếu đánh giá') . '</a>';
            }
            elseif(empty($html_offer)) {
                $add_evaluation = '<a href="'.admin_url('propose_offer/handling?kpv_id=' . $aRow['id']).'" class="tnh-modal"><i class="fa fa-plus"></i> Tạo Offer</a>';
            }
            elseif(!empty($html_offer) && empty($html_checklist) && $aRow['trang_thai_offer'] == 'DA_GUI') {
                $add_evaluation = '<a 
                href="'.admin_url('checklist_profile/createFromOfferPut/'  .$aRow['id_offer']).'"
                href2="'.admin_url('checklist_profile/handling/').'"
                 class="c_check_url"><i class="fa fa-plus"></i> Tạo Checklist Hồ Sơ</a>';
            }


            $edit = '<a class="c_modal" href="' . base_url('admin/recruitment/detail_eprofile/' . $aRow['id'].'/' . $aRow['id_requirements']) . '"><i class="fa fa-edit width-icon-actions"></i> ' . lang('Chỉnh sửa') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/recruitment/delete_eprofile/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete')  . '</a>';
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $edit . '</li>
                    <li>' . $add_evaluation . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[] = '<div>' . $actions . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function detail_eprofile($id = '0', $id_requirements = '0')
    {
        if($this->input->post()) {
            $data = $this->input->post();
            if(!empty($id)) {
                $dataUpdate = [
                    'full_name' => $data['full_name'],
                    'date_of_birth' => to_sql_date($data['date_of_birth']),
                    'phone_number' => $data['phone_number'],
                    'email' => $data['email'],
                    'current_address' => $data['current_address'] ?? NULL,
                    'gender' => $data['gender'] ?? '',
                    'marital_status' => $data['marital_status'] ?? '',
                    'id_card' => $data['id_card'] ?? NULL,
                    'date_of_issue' => !empty($data['date_of_issue']) ? to_sql_date($data['date_of_issue']) : NULL,
                    'educational' => $data['educational'] ?? NULL,
                    'training_school' => $data['training_school'] ?? NULL,
                    'academic_ranking' => $data['academic_ranking'] ?? NULL,
//                    'the_company_did' => $data['the_company_did'] ?? NULL,
//                    'job_title' => $data['job_title'] ?? NULL,
//                    'achievements' => $data['achievements'] ?? NULL,
                    'info_other' => $data['info_other'] ?? NULL,
                    'hr_note' => $data['hr_note'] ?? NULL,
                    'years_of_experience' => !empty($data['years_of_experience']) ? number_format_data($data['years_of_experience'], false) : NULL,
                    'expected_salary' => !empty($data['expected_salary']) ? number_format_data($data['expected_salary'], false) : NULL,
                    'role_level' => $data['role_level'] ?? NULL,
                    'source' => $data['source'] ?? NULL,
                    'cv_link' => $data['cv_link'] ?? NULL,
                    'referrer_name' => $data['referrer_name'] ?? NULL,
                ];
                $this->db->where('id', $id);
                $success = $this->db->update('tbl_hr_eprofile', $dataUpdate);
                if(!empty($success)) {

                    if(!empty($data['file_delete'])) {
                        foreach($data['file_delete'] as $file_id) {
                            $file = $this->db->get_where('tblfiles', ['id' => $file_id, 'rel_type' => 'eprofile'])->row();
                            if(!empty($file)) {
                                if (file_exists($file->external_link)) {
                                    unlink($file->external_link);
                                }
                                $this->db->where('id', $file_id);
                                $this->db->delete('tblfiles');
                            }
                        }
                    }

                    $this->db->where('id_eprofile', $id);
                    $this->db->delete('tbl_hr_eprofile_job');

                    if(!empty($data['the_company_did'])) {
                        foreach ($data['the_company_did'] as $key => $value) {
                            $this->db->insert('tbl_hr_eprofile_job', [
                                'id_eprofile' => $id,
                                'the_company_did' => $data['the_company_did'][$key],
                                'job_title' => $data['job_title'][$key],
                                'year_job' => $data['year_job'][$key],
                                'achievements' => $data['achievements'][$key],
                                'create_by' => get_staff_user_id(),
                            ]);
                        }
                    }


                    $folder = tnh_vn_to_str($id);
                    if (!is_dir('uploads/eprofile/')) {
                        mkdir('./uploads/eprofile/', 0777, TRUE);
                    }
                    if (!is_dir('uploads/eprofile/'.$folder)) {
                        mkdir('./uploads/eprofile/' . $folder, 0777, TRUE);
                    }
                    $upload_path = 'uploads/eprofile/'.$folder;
                    $this->load->library('upload');
                    if (!empty($_FILES['avatar']) && $_FILES['avatar']['size'] > 0) {
                        $config['upload_path'] = $upload_path;
                        $config['allowed_types'] = $this->image_types;
                        // $config['max_size'] = $this->allowed_file_size;
                        // $config['max_width'] = $this->Settings->iwidth;
                        // $config['max_height'] = $this->Settings->iheight;
                        // $config['overwrite'] = TRUE;
                        //$config['max_filename'] = 25;
                        $config['encrypt_name'] = false;
                        $this->upload->initialize($config);

                        if (!$this->upload->do_upload('avatar')) {
                            $error = $this->upload->display_errors();
                            $data['result'] = 0;
                            $data['message'] = $error;
                            echo json_encode($data);
                            return;
                        }
                        $avatar = $upload_path .'/' . $this->upload->file_name;
                        $this->db->where('id', $id);
                        $this->db->update('tbl_hr_eprofile', [
                            'avatar' => $avatar,
                        ]);
                    }

                    $uploadData = [];
                    if (!empty($_FILES['attachments']) && !empty($_FILES['attachments']['size'])) {
                        $fileCount = count($_FILES['attachments']['name']);
                        for ($i = 0; $i < $fileCount; $i++) {
                            $_FILES['file']['name'] = $_FILES['attachments']['name'][$i];
                            $_FILES['file']['type'] = $_FILES['attachments']['type'][$i];
                            $_FILES['file']['tmp_name'] = $_FILES['attachments']['tmp_name'][$i];
                            $_FILES['file']['error'] = $_FILES['attachments']['error'][$i];
                            $_FILES['file']['size'] = $_FILES['attachments']['size'][$i];

                            $config['upload_path'] = 'uploads/eprofile/'.$folder;
                            $config['allowed_types'] = '*';

                            $this->upload->initialize($config);
                            if ($this->upload->do_upload('file')) {
                                $uploadData[$i]['name'] = $this->upload->file_name;
                                $uploadData[$i]['extension'] = $_FILES['attachments']['type'][$i];
                                $uploadData[$i]['size'] = $_FILES['attachments']['size'][$i];
                                $uploadData[$i]['update_by'] = get_staff_user_id();
                                $uploadData[$i]['date_updated'] = date('Y-m-d H:i:s');
                            } else {
                                $error = $this->upload->display_errors();
                                $this->session->set_flashdata('error', $error);
                                $data['result'] = 0;
                                $data['message'] = $error;
                                echo json_encode($data);
                                return;
                            }
                        }
                        if(!empty($uploadData)) {
                            foreach ($uploadData as $file) {
                                $nameFile = explode('.', $file['name']);
                                $external = $nameFile[count($nameFile) - 1];
                                $this->db->insert('tblfiles', [
                                    'rel_id' => $id,
                                    'rel_type' => 'eprofile',
                                    'file_name' => $file['name'],
                                    'filetype' => $file['extension'],
                                    'external_link' => $upload_path .'/' . $file['name'],
                                    'external' => $external,
                                    'dateadded' => date('Y-m-d H:i:s'),
                                ]);
                            }
                        }
                    }
                    echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Cập nhật hồ sơ ứng viên thành công']);die();
                }
                echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Cập nhật hồ sơ ứng viên thất bại']);die();
            }
            else {
                if(empty($data['id_requirements'])) {
                    echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Thêm hồ sơ ứng viên thất bại']);die();
                }
                $dataInsert = [
                    'id_requirements' => $data['id_requirements'],
                    'full_name' => $data['full_name'],
                    'date_of_birth' => to_sql_date($data['date_of_birth']),
                    'phone_number' => $data['phone_number'],
                    'email' => $data['email'],
                    'current_address' => $data['current_address'] ?? NULL,
                    'gender' => $data['gender'] ?? '',
                    'marital_status' => $data['marital_status'] ?? '',
                    'id_card' => $data['id_card'] ?? NULL,
                    'date_of_issue' => !empty($data['date_of_issue']) ? to_sql_date($data['date_of_issue']) : NULL,
                    'educational' => $data['educational'] ?? NULL,
                    'training_school' => $data['training_school'] ?? NULL,
                    'academic_ranking' => $data['academic_ranking'] ?? NULL,
//                    'the_company_did' => $data['the_company_did'] ?? NULL,
//                    'job_title' => $data['job_title'] ?? NULL,
//                    'achievements' => $data['achievements'] ?? NULL,
                    'info_other' => $data['info_other'] ?? NULL,
                    'hr_note' => $data['hr_note'] ?? NULL,
                    'years_of_experience' => !empty($data['years_of_experience']) ? number_format_data($data['years_of_experience'], false) : NULL,
                    'expected_salary' => !empty($data['expected_salary']) ? number_format_data($data['expected_salary'], false) : NULL,
                    'role_level' => $data['role_level'] ?? NULL,
                    'source' => $data['source'] ?? NULL,
                    'cv_link' => $data['cv_link'] ?? NULL,
                    'referrer_name' => $data['referrer_name'] ?? NULL,
                    'create_by' => get_staff_user_id(),
                ];

                $success = $this->db->insert('tbl_hr_eprofile', $dataInsert);
                if(!empty($success)) {
                    $id = $this->db->insert_id();
                    if(!empty($data['the_company_did'])) {
                        foreach ($data['the_company_did'] as $key => $value) {
                            $this->db->insert('tbl_hr_eprofile_job', [
                                'id_eprofile' => $id,
                                'the_company_did' => $data['the_company_did'][$key],
                                'job_title' => $data['job_title'][$key],
                                'year_job' => $data['year_job'][$key],
                                'achievements' => $data['achievements'][$key],
                                'create_by' => get_staff_user_id(),
                            ]);
                        }
                    }

                    $folder = tnh_vn_to_str($id);

                    if (!is_dir('uploads/eprofile/')) {
                        mkdir('./uploads/eprofile/', 0777, TRUE);
                    }
                    if (!is_dir('uploads/eprofile/'.$folder)) {
                        mkdir('./uploads/eprofile/' . $folder, 0777, TRUE);
                    }
                    $this->load->library('upload');
                    $upload_path = 'uploads/eprofile/'.$folder;
                    if (!empty($_FILES['avatar']) && $_FILES['avatar']['size'] > 0) {
                        $config['upload_path'] = $upload_path;
                        $config['allowed_types'] = $this->image_types;
                        // $config['max_size'] = $this->allowed_file_size;
                        // $config['max_width'] = $this->Settings->iwidth;
                        // $config['max_height'] = $this->Settings->iheight;
                        // $config['overwrite'] = TRUE;
                        //$config['max_filename'] = 25;
                        $config['encrypt_name'] = false;
                        $this->upload->initialize($config);

                        if (!$this->upload->do_upload('avatar')) {
                            $error = $this->upload->display_errors();
                            $data['result'] = 0;
                            $data['message'] = $error;
                            echo json_encode($data);
                            return;
                        }
                        $avatar = $upload_path .'/' . $this->upload->file_name;
                        $this->db->where('id', $id);
                        $this->db->update('tbl_hr_eprofile', [
                            'avatar' => $avatar,
                        ]);
                    }
                    $uploadData = [];
                    if (!empty($_FILES['attachments']) && !empty($_FILES['attachments']['size'])) {
                        $fileCount = count($_FILES['attachments']['name']);
                        for ($i = 0; $i < $fileCount; $i++) {
                            $_FILES['file']['name'] = $_FILES['attachments']['name'][$i];
                            $_FILES['file']['type'] = $_FILES['attachments']['type'][$i];
                            $_FILES['file']['tmp_name'] = $_FILES['attachments']['tmp_name'][$i];
                            $_FILES['file']['error'] = $_FILES['attachments']['error'][$i];
                            $_FILES['file']['size'] = $_FILES['attachments']['size'][$i];

                            $config['upload_path'] = 'uploads/eprofile/'.$folder;
                            $config['allowed_types'] = '*';

                            $this->upload->initialize($config);
                            if ($this->upload->do_upload('file')) {
                                $uploadData[$i]['name'] = $this->upload->file_name;
                                $uploadData[$i]['extension'] = $_FILES['attachments']['type'][$i];
                                $uploadData[$i]['size'] = $_FILES['attachments']['size'][$i];
                                $uploadData[$i]['update_by'] = get_staff_user_id();
                                $uploadData[$i]['date_updated'] = date('Y-m-d H:i:s');
                            } else {
                                $error = $this->upload->display_errors();
                                $this->session->set_flashdata('error', $error);
                                $data['result'] = 0;
                                $data['message'] = $error;
                                echo json_encode($data);
                                return;
                            }
                        }
                        if(!empty($uploadData)) {
                            foreach ($uploadData as $file) {
                                $nameFile = explode('.', $file['name']);
                                $external = $nameFile[count($nameFile) - 1];
                                $this->db->insert('tblfiles', [
                                    'rel_id' => $id,
                                    'rel_type' => 'eprofile',
                                    'file_name' => $file['name'],
                                    'filetype' => $file['extension'],
                                    'external_link' => $upload_path .'/' . $file['name'],
                                    'external' => $external,
                                    'dateadded' => date('Y-m-d H:i:s'),
                                ]);
                            }
                        }
                    }


                    $this->db->where('id_step_default', 2);
                    $this->db->where('id_requirements', $data['id_requirements']);
                    $this->db->update('tbl_hr_requirements_step', [
                        'status' => 1,
                    ]);

                    echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Thêm hồ sơ ứng viên thành công']);die();
                }
                echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Thêm hồ sơ ứng viên thất bại']);die();
                //avatar
            }
        }
        else {
            $data['title'] = 'Thêm hồ sơ ứng viên';
            if(!empty($id)) {
                $data['title'] = 'Cập nhật hồ sơ ứng viên';
                $data['eprofile'] = $this->db->get_where('tbl_hr_eprofile', ['id' => $id])->row_array();
                $data['eprofile_job'] = $this->db->get_where('tbl_hr_eprofile_job', ['id_eprofile' => $id])->result_array();

                $data['attachments'] = $this->db->get_where('tblfiles', [
                    'rel_id' => $id,
                    'rel_type' => 'eprofile',
                ])->result_array();

            }
            else {
                $data['list_requirements'] = $this->db->get_where('tbl_hr_requirements', ['status' => 'approved'])->result_array();
            }
            $data['id_requirements'] = $id_requirements ?? 0;
            if(!empty($data['id_requirements'])) {
                $data['requirements'] = $this->db->get_where('tbl_hr_requirements', ['status' => 'approved', 'id' => $id_requirements])->row();
            }


            $this->load->view('admin/recruitment/eprofile/detail', $data);
        }
    }

    public function delete_eprofile($id = '') {
        $offer = $this->db->get_where('tbl_propose_offer', ['kqpv_id' => $id])->row();
        if ($offer) {
            $data['result'] = 0;
            $data['message'] = 'Không thể xóa hồ sơ ứng viên đã có Offer.';
            echo json_encode($data);die();
        }
        
        $this->db->where('id', $id);
        $success = $this->db->delete('tbl_hr_eprofile');
        $data = [];
        if ($success) {
            $list_file = $this->db->get_where('tblfiles', ['rel_id' => $id, 'rel_type' => 'eprofile'])->result();
            foreach($list_file as $file) {
                if(!empty($file)) {
                    if (file_exists($file->external_link)) {
                        unlink($file->external_link);
                    }
                    $this->db->where('id', $file->id);
                    $this->db->delete('tblfiles');
                }
            }
            $data['result'] = 1;
            $data['message'] = lang('Xóa thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Xóa thất bại');
        }
        echo json_encode($data);die();
    }


    public function importEprofile()
    {
        $data = [];
        if (!empty($_FILES)){
            ini_set('max_execution_time', 800);
            require_once(APPPATH . 'third_party/PHPExcel/PHPExcel.php');

            $tmpFile = $_FILES['file']['tmp_name'];
            $ext = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, ['XLS', 'XLSX'])) {
                echo json_encode(['success' => false, 'message' => 'File không hợp lệ']);
                die;
            }

            $excel = PHPExcel_IOFactory::load($tmpFile);
            $sheet = $excel->getActiveSheet();
            $highestRow = $sheet->getHighestRow();

            $priorityMap = [
                'Thường' => '1',
                'Cao'    => '2',
                'Khẩn'   => '3'
            ];

            $statusMap = [
                'Draft'     => 'draft',
                'Pending'   => 'pending',
                'Approved'  => 'approved',
                'Rejected'  => 'rejected',
                'Closed'    => 'closed'
            ];

            $count = 0;

            // Dòng 1: header → bắt đầu từ dòng 2
            for ($row = 2; $row <= $highestRow; $row++) {
                $dataInsert = [];
                $id_requirements = trim($sheet->getCell("A$row")->getValue());
                $id_requirements = $this->db
                    ->where('code', $id_requirements)
                    ->get('tbl_hr_requirements')
                    ->row('id');
                if (!$id_requirements) continue;
                $dataInsert['id_requirements'] = $id_requirements;

                $source = trim($sheet->getCell("B$row")->getValue()) ?? NULL;
                $dataInsert['source'] = $source;

                $full_name = trim($sheet->getCell("C$row")->getValue()) ?? NULL;
                $dataInsert['full_name'] = $full_name;

                $phone_number = trim($sheet->getCell("D$row")->getValue()) ?? NULL;
                $dataInsert['phone_number'] = $phone_number;


                $email = trim($sheet->getCell("E$row")->getValue()) ?? NULL;
                $dataInsert['email'] = $email;


                $date_of_birth = $sheet->getCell("F$row")->getValue();
                if(is_numeric($date_of_birth)) {
                    $unix = ($date_of_birth - 25569) * 86400;
                    $date_of_birth = date('Y-m-d', $unix);
                }
                else {
                    $date_of_birth = to_sql_date($sheet->getCell("F$row")->getValue(), true);
                }
                $dataInsert['date_of_birth'] = $date_of_birth;

                $gender = trim($sheet->getCell("G$row")->getValue()) ?? NULL;
                if(mb_strtolower($gender) == 'nam') {
                    $gender = 'male';
                }
                else {
                    $gender = 'female';
                }
                $dataInsert['gender'] = $gender;


                $marital_status = trim($sheet->getCell("H$row")->getValue()) ?? NULL;
                $dataInsert['marital_status'] = $marital_status;

                $current_address = trim($sheet->getCell("I$row")->getValue()) ?? NULL;
                $dataInsert['current_address'] = $current_address;

                $id_card = trim($sheet->getCell("J$row")->getValue()) ?? NULL;
                $dataInsert['id_card'] = $id_card;

                $date_of_issue = $sheet->getCell("K$row")->getValue();
                if(is_numeric($date_of_issue)) {
                    $unix = ($date_of_issue - 25569) * 86400;
                    $date_of_issue = date('Y-m-d', $unix);
                }
                else {
                    $date_of_issue = to_sql_date($sheet->getCell("K$row")->getValue(), true);
                }
                $dataInsert['date_of_issue'] = $date_of_issue;


                $educational = trim($sheet->getCell("L$row")->getValue()) ?? NULL;
                $dataInsert['educational'] = $this->key_list_educational[$this->to_key($educational)]['id'] ?? NULL;

                $dataInsert['training_school'] = trim($sheet->getCell("M$row")->getValue()) ?? NULL;


                $academic_ranking = trim($sheet->getCell("N$row")->getValue()) ?? NULL;
                $dataInsert['academic_ranking'] = $this->key_list_academic_ranking[$this->to_key($academic_ranking)]['id'] ?? NULL;

                $years_of_experience = trim($sheet->getCell("O$row")->getValue()) ?? NULL;
                $dataInsert['years_of_experience'] = $years_of_experience;




                $insertITem = [];
                $the_company_did = trim($sheet->getCell("P$row")->getValue()) ?? NULL;
                $insertITem['the_company_did'] = $the_company_did;

                $job_title = trim($sheet->getCell("Q$row")->getValue()) ?? NULL;
                $insertITem['job_title'] = $job_title;

                $year_job = trim($sheet->getCell("R$row")->getValue()) ?? NULL;
                $insertITem['year_job'] = $year_job;

                $achievements = trim($sheet->getCell("S$row")->getValue()) ?? NULL;
                $insertITem['achievements'] = $achievements;

                $expected_salary = trim($sheet->getCell("T$row")->getValue()) ?? NULL;
                if(!empty($expected_salary)) {
                    $dataInsert['expected_salary'] = number_format_data($expected_salary, false);
                }

                $cv_link = trim($sheet->getCell("U$row")->getValue()) ?? NULL;
                $dataInsert['cv_link'] = $cv_link;

                $hr_note = trim($sheet->getCell("V$row")->getValue()) ?? NULL;
                $dataInsert['hr_note'] = $hr_note;

                // tránh import trùng mã
                if(!empty($id_card)) {
                    if ($this->db->where('id_card', $id_card)->get(' tbl_hr_eprofile')->row()) {
                        continue;
                    }
                }
                $success = $this->db->insert('tbl_hr_eprofile', $dataInsert);
                if(!empty($success)) {
                    $id = $this->db->insert_id();
                    if(!empty($insertITem)) {
                        $insertITem['id_eprofile'] = $id;
                        $insertITem['create_by'] = get_staff_user_id();
                        $this->db->insert('tbl_hr_eprofile_job', $insertITem);
                    }
                }
                $count++;
            }

            echo json_encode([
                'success' => true,
                'message' => 'Import thành công ' . $count . ' yêu cầu tuyển dụng'
            ]);
            die;
        }
        $data['title'] = _l('Import danh sách hồ sơ ứng viên');
        $this->load->view('admin/recruitment/eprofile/import', $data);
    }

    function to_key($string) {
        $string = mb_strtolower($string, 'UTF-8');

        $accents = [
            'à','á','ạ','ả','ã','â','ầ','ấ','ậ','ẩ','ẫ','ă','ằ','ắ','ặ','ẳ','ẵ',
            'è','é','ẹ','ẻ','ẽ','ê','ề','ế','ệ','ể','ễ',
            'ì','í','ị','ỉ','ĩ',
            'ò','ó','ọ','ỏ','õ','ô','ồ','ố','ộ','ổ','ỗ','ơ','ờ','ớ','ợ','ở','ỡ',
            'ù','ú','ụ','ủ','ũ','ư','ừ','ứ','ự','ử','ữ',
            'ỳ','ý','ỵ','ỷ','ỹ',
            'đ'
        ];

        $replace = [
            'a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
            'e','e','e','e','e','e','e','e','e','e','e',
            'i','i','i','i','i',
            'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
            'u','u','u','u','u','u','u','u','u','u','u',
            'y','y','y','y','y',
            'd'
        ];

        $string = str_replace($accents, $replace, $string);
        $string = preg_replace('/[^a-z0-9\s]/', '', $string);
        $string = preg_replace('/\s+/', '_', $string);

        return $string;
    }

}