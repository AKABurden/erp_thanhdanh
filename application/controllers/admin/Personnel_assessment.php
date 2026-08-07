<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Personnel_assessment extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->type = 1;
        if (!empty($this->input->get('type'))){
            $this->type = $this->input->get('type');
        }

        $this->preViewPersonnelAssessment = true;
        $this->preAddPersonnelAssessment = true;
        $this->preEditPersonnelAssessment = true;
        $this->preProcessEvaluatePersonnelAssessment = true;
        $this->preDeletePersonnelAssessment = true;
    }

    public function index()
    {
        if (!$this->preViewPersonnelAssessment) {
            access_denied('evaluation_employee');
        }
        $data['dtRoom'] = get_table_where('tbl_room');
        $data['title'] = $this->type == 1 ? _l('Đánh giá nhân viên (CT)') : _l('Đánh giá ứng viên');
        $data['type'] = $this->type;
        $this->load->view('admin/evaluation_employee/index', $data);
    }

    public function getEvaluationEmployee()
    {
        $status_table = $this->input->post('status_table');
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $aColumns = [
            'tbl_evaluation_employee.id as id',
            'tbl_evaluation_employee.code as code',
            '"" as staff',
            'tblroles.code_role as code_role',
            'tbl_room.name as name_room',
            'tbl_role_level.code as code_role_level',
            'tbl_evaluation_employee.type as type',
            'tbl_evaluation_employee.point as point',
            'tbl_evaluation_employee.rating as rating',
            'tbl_evaluation_employee.warning as warning',
            'tbl_evaluation_employee.note as note',
            '"" as actions',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_evaluation_employee';
        $where = [

        ];
        $filter = [];
        $join = [
            'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_evaluation_employee.staff_id AND tbl_evaluation_employee.type = 1',
            'LEFT JOIN tbl_hr_eprofile ON tbl_hr_eprofile.id = tbl_evaluation_employee.staff_id AND tbl_evaluation_employee.type = 2',
            'INNER JOIN tbl_role_level ON tbl_role_level.id = tbl_evaluation_employee.role_level_id',
            'INNER JOIN tblroles ON tblroles.roleid = tbl_evaluation_employee.role_id',
            'LEFT JOIN tbl_room ON tbl_room.id = tblroles.id_room',
            'LEFT JOIN tbl_propose_offer ON tbl_propose_offer.kqpv_id = tbl_hr_eprofile.id',

        ];

        if (!empty($role_id_search)){
            $where[] = 'AND tbl_evaluation_employee.role_id = '.$role_id_search.'';
        }

        if ($status_table != 'all'){
            $where[] = 'AND tbl_room.id = '.$status_table.'';
        }

        $where[] = 'AND tbl_evaluation_employee.type = '.$this->type.'';

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as staff_name',
            'tbl_hr_eprofile.full_name as hr_name',
            'tbl_hr_eprofile.id as hr_eprofile_id',
            'tbl_propose_offer.id as id_propose_offer',
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left"><a href="' . base_url('admin/personnel_assessment/view/' . $aRow['id'].'') . '">'.$aRow['code'].'</a></div>';
            if ($aRow['type'] == 1){
                $htmlStaff = $aRow['staff_name'];
                $htmlType = '<div>Nhân viên</div>';
            } else {
                $htmlStaff = $aRow['hr_name'];
                $htmlType = '<div>Ứng viên</div>';
            }
            $row[] = '<div class="text-center">'.$htmlStaff.'</div>';
            $row[] = '<div class="text-center">'.$aRow['code_role'].'</div>';
            $row[] = '<div class="text-left">'.$aRow['name_room'].'</div>';
            $row[] = '<div class="text-center">'.($aRow['code_role_level']).'</div>';
            $row[] = '<div class="text-center">'.($htmlType).'</div>';
            $row[] = '<div class="text-center">'.$aRow['point'].'</div>';
            $row[] = '<div class="text-left">'.($aRow['rating']).'</div>';
            $row[] = '<div class="text-left">'.($aRow['warning']).'</div>';
            $row[] = '<div class="text-left">'.($aRow['note']).'</div>';
            $view = '<a href="' . base_url('admin/personnel_assessment/view/' . $aRow['id'].'') . '"><i class="fa fa-eye width-icon-actions"></i> ' . lang('Xem chi tiết') . '</a>';
            $evaluate = '<a href="' . base_url('admin/personnel_assessment/process_evaluate/' . $aRow['id'].'') . '"><i class="fa fa-plus width-icon-actions"></i> ' . lang('Đánh giá nhân viên') . '</a>';
            $edit = '<a href="' . base_url('admin/personnel_assessment/detail/' . $aRow['id'].'') . '"><i class="fa fa-edit width-icon-actions"></i> ' . lang('Chỉnh sửa') . '</a>';

            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/personnel_assessment/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete')  . '</a>';

            $created_offer = '';
            if ($aRow['type'] == 2 && empty($aRow['id_propose_offer'])){
                $created_offer = '<a class="btn-modern  tnh-modal" href="' . base_url('admin/propose_offer/handling?kpv_id=' . $aRow['hr_eprofile_id']) . '"><i class="fa fa-plus width-icon-actions"></i> ' . lang('Tạo đề xuất offer') . '</a>';
            }else{
                $created_offer = '<a class="btn-modern  tnh-modal" href="' . base_url('admin/propose_offer/handling/' . $aRow['id_propose_offer']) . '"><i class="fa fa-eye width-icon-actions"></i> ' . lang('Xem offer') . '</a>';

            }
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $view . '</li>
                    <li>' . $evaluate . '</li>
                    <li>' . $edit . '</li>
                    <li>' . $created_offer . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[] = '<div>' . $actions . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function detail($id = 0,$id_hr = 0)
    {
        if ($this->input->post()){
            // $this->form_validation->set_rules('code', lang("Mã mô tả công việc"), 'required');
            $this->form_validation->set_rules('type', lang("Loại đánh giá"), 'required');
            $this->form_validation->set_rules('staff_id', lang("Nhân viên hoặc ứng viên"), 'required');
            $this->form_validation->set_rules('role_id', lang("Mã vị trí"), 'required');
            $this->form_validation->set_rules('role_level_id', lang("Cấp bậc vai trò"), 'required');
            if ($this->form_validation->run() == true) {
                // $code = $this->input->post('code');
                $code = 'DG-'.sprintf('%06d', ch_getMaxID('id', 'tbl_evaluation_employee') + 1);
                $type = $this->input->post('type');
                $staff_id = $this->input->post('staff_id');
                $role_id = $this->input->post('role_id');
                $role_level_id = $this->input->post('role_level_id');
                $note = $this->input->post('note') ?? null;

                $this->db->where('code',$code);
                $this->db->from('tbl_evaluation_employee');
                $this->db->where('id !=', $id);
                $checkExists = $this->db->count_all_results();
                if (!empty($checkExists)){
                    $data['result'] = false;
                    $data['message'] = lang('Mã đánh giá nhân đã tồn tại');
                    echo json_encode($data);die();
                }

                $dataPost = $this->input->post();
                $question_id = $this->input->post('question_id') ?? [];
                $arrQuestion = [];
                if (!empty($question_id)){
                    foreach ($question_id as $key => $value){
                        if (empty($value)){
                            continue;
                        }
                        $dtQuestion = get_table_where('tbl_question_bank',['id' => $value],'','row_array');
                        $evaluation_employee_question_id = $dataPost['evaluation_employee_question_id'][$key] ?? 0;
                        $arrAnswer = [];
                        $this->db->from('tbl_question_bank_answer');
                        $this->db->where('tbl_question_bank_answer.question_bank_id',$value);
                        $dtAnswer = $this->db->get()->result_array();
                        if (!empty($dtAnswer)){
                            foreach ($dtAnswer as $k => $v){
                                $arrAnswer[] = [
                                    'prefix' => $v['prefix'],
                                    'answer' => $v['answer'],
                                    'point' => $v['point'],
                                ];
                            }
                        }
                        $arrQuestion[] = [
                            'question_bank_id' => $value,
                            'weight' => $dtQuestion['weight'],
                            'items' => $arrAnswer
                        ];
                    }
                }
                if (empty($arrQuestion)){
                    $data['result'] = false;
                    $data['message'] = lang('Vui lòng chọn câu hỏi');
                    echo json_encode($data);die();
                }
                if (empty($id)) {
                    $option = [
                        'date' => date('Y-m-d H:i:s'),
                        'code' => $code,
                        'type' => $type,
                        'staff_id' => $staff_id,
                        'role_id' => $role_id,
                        'role_level_id' => $role_level_id,
                        'note' => $note,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s')
                    ];
                } else {
                    $option = [
                        'date' => date('Y-m-d H:i:s'),
                        'code' => $code,
                        'type' => $type,
                        'staff_id' => $staff_id,
                        'role_id' => $role_id,
                        'role_level_id' => $role_level_id,
                        'note' => $note,
                    ];
                }

                if(empty($id)){
                    $this->db->insert('tbl_evaluation_employee',$option);
                    $evaluation_employee_id = $this->db->insert_id();
                    if ($evaluation_employee_id){

                        if (!empty($arrQuestion)){
                            foreach ($arrQuestion as $key => $value){
                                $items = $value['items'];
                                unset($value['items']);
                                $value['evaluation_employee_id'] = $evaluation_employee_id;
                                $this->db->insert('tbl_evaluation_employee_question',$value);
                                $evaluation_employee_question_id = $this->db->insert_id();
                                if (!empty($items)){
                                    foreach ($items as $k => $v){
                                        $v['evaluation_employee_question_id'] = $evaluation_employee_question_id;
                                        $v['evaluation_employee_id'] = $evaluation_employee_id;
                                        $this->db->insert('tbl_evaluation_employee_question_answer',$v);
                                    }
                                }
                            }
                        }
                        if($type == 2) {
                            $this->db->where('id', $staff_id);
                            $ktProfile = $this->db->get('tbl_hr_eprofile')->row();
                            if(!empty($ktProfile)) {
                                $this->db->where('id_step_default', 3);
                                $this->db->where('id_requirements', $ktProfile->id_requirements);
                                $this->db->update('tbl_hr_requirements_step', [
                                    'status' => 1,
                                ]);
                            }
                        }



                        $data['result'] = 1;
                        $data['message'] = lang('Thêm đánh giá thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Thêm đánh giá thất bại');
                    }
                    echo json_encode($data);die();
                } else {
                    $this->db->where('tbl_evaluation_employee.id',$id);
                    $this->db->update('tbl_evaluation_employee',$option);
                    $evaluation_employee_id = $id;
                    if ($evaluation_employee_id){
                        $this->db->where('evaluation_employee_id',$evaluation_employee_id);
                        $this->db->delete('tbl_evaluation_employee_question');


                        $this->db->where('evaluation_employee_id',$evaluation_employee_id);
                        $this->db->delete('tbl_evaluation_employee_question_answer');

                        if (!empty($arrQuestion)){
                            foreach ($arrQuestion as $key => $value){
                                $items = $value['items'];
                                unset($value['items']);
                                $value['evaluation_employee_id'] = $evaluation_employee_id;
                                $this->db->insert('tbl_evaluation_employee_question',$value);
                                $evaluation_employee_question_id = $this->db->insert_id();
                                if (!empty($items)){
                                    foreach ($items as $k => $v){
                                        $v['evaluation_employee_question_id'] = $evaluation_employee_question_id;
                                        $v['evaluation_employee_id'] = $evaluation_employee_id;
                                        $this->db->insert('tbl_evaluation_employee_question_answer',$v);
                                    }
                                }
                            }
                        }
                        $data['result'] = 1;
                        $data['message'] = lang('Sao chép thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Sao chép thất bị');
                    }

                    echo json_encode($data);die();
                }

            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);die();
        }
        if(empty($id)){
            if (!$this->preAddPersonnelAssessment){
                accessDenied($js = true);
            }
            $title = lang('Thêm đánh giá nhân viên (CT)');
            if($this->input->get('type') == 2) {
                $title = lang('Thêm đánh giá ứng viên');
            }
        } else {
            if (!$this->preEditPersonnelAssessment){
                accessDenied($js = true);
            }
            $title = lang('Cập nhập đánh giá nhân viên (CT)');
            $dtData = get_table_where('tbl_evaluation_employee',['id' => $id],'','row_array');
            if($dtData['type'] == 2) {
                $title = lang('Cập nhập đánh giá ứng viên');
            }


            $dtDataQuestionItem = get_table_where('tbl_evaluation_employee_question',['evaluation_employee_id' => $id],'','result_array');
            $arrIdQuestion = array_column($dtDataQuestionItem,'question_bank_id');
            $this->db->from('tbl_question_bank');
            $this->db->where('role_id',$dtData['role_id'] ?? 0);
            $this->db->where('role_level_id',$dtData['role_level_id'] ?? 0);
            $dtDataQuestion = $this->db->get()->result_array();
            $dtDataQuestion = array_reduce($dtDataQuestion, function ($acc, $item) {
                $acc[$item['type']][] = $item;
                return $acc;
            });
            if (!empty($dtData['rating_list'])){
                refererModel(lang('Phiếu này đã đánh giá không thể thay đổi!'));
            }
        }
        $data['title'] = $title;
        $data['dtData'] = $dtData ?? null;
        $data['dtDataQuestion'] = $dtDataQuestion ?? null;
        $data['dtDataQuestionItem'] = $dtDataQuestionItem ?? null;
        $data['arrIdQuestion'] = $arrIdQuestion ?? [];
        $data['id'] = $id;
        $data['type'] = $this->type;

        $this->db->select('tbl_hr_eprofile.id,tbl_hr_eprofile.full_name,tbl_hr_requirements.role_id,tbl_hr_eprofile.role_level as role_level_id');
        $this->db->from('tbl_hr_eprofile');
        $this->db->join('tbl_hr_requirements','tbl_hr_requirements.id = tbl_hr_eprofile.id_requirements');
//        $this->db->join('tbl_job_detail','tbl_job_detail.id = tbl_hr_requirements.id_jd', 'left');
        $this->db->where('tbl_hr_eprofile.id',$id_hr);
        $dtHr = $this->db->get()->row_array();

        $data['dtHr'] = $dtHr;

        $this->load->view('admin/evaluation_employee/detail',$data);
    }

    public function loadDataQuestion()
    {
        $role_id = $this->input->post('role_id');
        $role_level_id = $this->input->post('role_level_id');

        $this->db->from('tbl_question_bank');
        $this->db->where('role_id',$role_id);
        $this->db->where('role_level_id',$role_level_id);
        $dtData = $this->db->get()->result_array();
        $dtData = array_reduce($dtData, function ($acc, $item) {
            $acc[$item['type']][] = $item;
            return $acc;
        });
        $data['dtData'] = $dtData ?? [];
        echo json_encode($data);die();
    }

    public function view($id)
    {
        $data = [];
        $this->db->select(
            'tbl_evaluation_employee.*,
            CONCAT(tblstaff.firstname, " ", tblstaff.lastname, "") as staff_name,
            tbl_hr_eprofile.full_name as hr_name,
            tblroles.code_role as code_role,
            tbl_role_level.code as code_role_level'
        );
        $this->db->from('tbl_evaluation_employee');
        $this->db->join('tblstaff','tblstaff.staffid = tbl_evaluation_employee.staff_id AND tbl_evaluation_employee.type = 1','left');
        $this->db->join('tbl_hr_eprofile','tbl_hr_eprofile.id = tbl_evaluation_employee.staff_id AND tbl_evaluation_employee.type = 2','left');
        $this->db->join('tblroles','tblroles.roleid = tbl_evaluation_employee.role_id','inner');
        $this->db->join('tbl_role_level','tbl_role_level.id = tbl_evaluation_employee.role_level_id','inner');
        $this->db->where('tbl_evaluation_employee.id',$id);
        $data['dtData'] = $this->db->get()->row_array();

        $this->db->select(
            'tbl_evaluation_employee_question.*,
            tbl_question_bank.question,
            tbl_question_bank.type'
        );
        $this->db->from('tbl_evaluation_employee_question');
        $this->db->join('tbl_question_bank','tbl_question_bank.id = tbl_evaluation_employee_question.question_bank_id','inner');
        $this->db->where('tbl_evaluation_employee_question.evaluation_employee_id',$id);
        $dtDataQuestion = $this->db->get()->result_array();
        $countQuestion = count($dtDataQuestion);
        if (!empty($dtDataQuestion)){
            foreach ($dtDataQuestion as $key => $value){
                $this->db->from('tbl_evaluation_employee_question_answer');
                $this->db->where('tbl_evaluation_employee_question_answer.evaluation_employee_id',$id);
                $this->db->where('tbl_evaluation_employee_question_answer.evaluation_employee_question_id',$value['id']);
                $dtDataQuestionAnswer = $this->db->get()->result_array();
                $dtDataQuestion[$key]['dtDataQuestionAnswer'] = $dtDataQuestionAnswer;
            }
        }
        $dtDataQuestion = array_reduce($dtDataQuestion, function ($acc, $item) {
            $acc[$item['type']][] = $item;
            return $acc;
        });
        $data['dtDataQuestion'] = $dtDataQuestion;
        $data['countQuestion'] = $countQuestion;
        $data['dtRatingList'] = get_table_where('tbl_rating_list');
        $data['dtTypeQuestion'] = getTypeQuestion();

        $data['title'] = lang('Xem chi tiết đánh giá nhân viên');
        if($data['dtData']['type'] == 2) {
            $data['title'] = lang('Xem chi tiết phiếu đánh giá ứng viên');
        }
        $this->load->view('admin/evaluation_employee/view',$data);
    }

    public function delete($id)
    {
        if (!$this->preDeletePersonnelAssessment){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_evaluation_employee.*');
        $this->db->from('tbl_evaluation_employee');
        $this->db->where('tbl_evaluation_employee.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_evaluation_employee');
        if ($success){

            $this->db->where('evaluation_employee_id',$id);
            $this->db->delete('tbl_evaluation_employee_question');

            $this->db->where('evaluation_employee_id',$id);
            $this->db->delete('tbl_evaluation_employee_question_answer');

            insertActivityLog([
                'type_parent_obj' => 'evaluation_employee',
                'table_obj' => 'tbl_evaluation_employee',
                'id_obj' => $id,
                'name_obj' => $dtData['code'],
                'content' => lang('Xóa đánh giá nhân viên') . ' [' . $dtData['code'] . ']',
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

    public function searchStaffAndHrProfile($id = '',$typeUrl = '')
    {
        $data = [];
        $term = $this->input->get('term');
        $type = $this->input->get('type');
        $type_staff = $this->input->get('type_staff') ?? 1;
        if ($type_staff == 3){
            $type_staff = 0;
        }
        if (empty($type)){
            $type = $typeUrl;
        }

        $limit = 50;
        if ($type == 1) {
            $this->db->select('tblstaff.staffid as id,  CONCAT(tblstaff.firstname," ",tblstaff.lastname) as text,tblroles.name as name_role,tbl_room.name as name_room');
            $this->db->from('tblstaff');
            $this->db->join('tblroles','tblroles.roleid = tblstaff.role','left');
            $this->db->join('tbl_room','tbl_room.id = tblroles.id_room','left');
            $this->db->where('tblstaff.active', 1);
            $this->db->where('tblstaff.status_work', $type_staff);
            if (!empty($term)) {
                $this->db->group_start();
                $this->db->like('CONCAT(tblstaff.firstname," ",tblstaff.lastname)', $term);
                $this->db->or_like('tblstaff.code', $term);
                $this->db->group_end();
            }
            $staffs = $this->db->get()->result_array();
            $data['results'] = $staffs;
            if ($id) {
                $this->db->select('tblstaff.staffid as id, CONCAT(tblstaff.firstname," ",tblstaff.lastname) as text');
                $this->db->from('tblstaff');
                $this->db->where('tblstaff.active', 1);
                $this->db->where('tblstaff.staffid', $id);
                $staff = $this->db->get()->row_array();
                $data['row'] = [
                    'id' => $staff['id'],
                    'text' => $staff['text'],
                ];

            }
        } elseif ($type == 2){
            $this->db->select('tbl_hr_eprofile.id as id, tbl_hr_eprofile.full_name as text');
            $this->db->from('tbl_hr_eprofile');
            if (!empty($term)) {
                $this->db->group_start();
                $this->db->like('tbl_hr_eprofile.full_name', $term);
                $this->db->group_end();
            }
            $staffs = $this->db->get()->result_array();
            $data['results'] = $staffs;
            if ($id) {
                $this->db->select('tbl_hr_eprofile.id as id, tbl_hr_eprofile.full_name as text');
                $this->db->from('tbl_hr_eprofile');
                $this->db->where('tbl_hr_eprofile.id', $id);
                $staff = $this->db->get()->row_array();
                $data['row'] = [
                    'id' => $staff['id'],
                    'text' => $staff['text'],
                ];

            }
        } else {
            $data['results'] = [];
        }

        echo json_encode($data);
    }

    public function process_evaluate($id)
    {
        if (!$this->preProcessEvaluatePersonnelAssessment){
            accessDenied($js = true);
        }
        $data = [];
        if ($this->input->post()){
            $question = $this->input->post('question') ?? null;
            $arrItem = [];
            if (!empty($question)){
                $answer_id = $this->input->post('answer');
                if (!empty($answer_id)){
                    $this->db->from('tbl_evaluation_employee_question_answer');
                    $this->db->where_in('tbl_evaluation_employee_question_answer.id',$answer_id);
                    $dtAnswers = $this->db->get()->result_array();
                    $dtAnswers = array_reduce($dtAnswers, function ($acc, $item) {
                        $acc[$item['evaluation_employee_question_id']] = $item;
                        return $acc;
                    });
                }

                $this->db->from('tbl_evaluation_employee_question');
                $this->db->where_in('tbl_evaluation_employee_question.id',$question);
                $dtListQuestion = $this->db->get()->result_array();
                $dtListQuestion = array_reduce($dtListQuestion, function ($acc, $item) {
                    $acc[$item['id']] = $item;
                    return $acc;
                });

                $totalPoint = 0;
                foreach ($question as $key => $value){
                    $dtQuestion = $dtListQuestion[$value] ?? [];
                    if (empty($dtQuestion)){
                        continue;
                    }
                    $dtAnswer = $dtAnswers[$value] ?? [];
                    if (empty($dtAnswer)){
                        continue;
                    }
                    $weight = $dtQuestion['weight'];
                    $point_real = ($dtAnswer['point'] * $weight) / 100;
                    $totalPoint += $point_real;
                    $arrItem[] = [
                        'id' => $dtQuestion['id'],
                        'answer' => $dtAnswer['prefix'],
                        'point' => $dtAnswer['point'],
                        'point_real' => $point_real,
                    ];
                }
            }
            $this->db->from('tbl_rating_list');
            $this->db->where('tbl_rating_list.point_start <=', $totalPoint);
            $this->db->where('tbl_rating_list.point_end >', $totalPoint);
            $dtRatingList = $this->db->get()->row_array();

            $this->db->where('tbl_evaluation_employee.id',$id);
            $success = $this->db->update('tbl_evaluation_employee',[
                'rating_list' => $dtRatingList['id'] ?? 0,
                'point' => $totalPoint,
                'rating' => $dtRatingList['rating'],
                'warning' => $dtRatingList['warning'],
            ]);
            if ($success){
                $data['result'] = true;
                $data['id'] = $id;
                $data['message'] = lang('Đánh giá thành công');
                if (!empty($arrItem)){
                    $this->db->update_batch('tbl_evaluation_employee_question', $arrItem, 'id');
                }
            } else {
                $data['result'] = false;
                $data['id'] = $id;
                $data['message'] = lang('Đánh giá thất bại');
            }
            echo json_encode($data);die();
        }
        $this->db->select(
            'tbl_evaluation_employee.*,
            CONCAT(tblstaff.firstname, " ", tblstaff.lastname, "") as staff_name,
            tbl_hr_eprofile.full_name as hr_name,
            tblroles.code_role as code_role,
            tbl_role_level.code as code_role_level'
        );
        $this->db->from('tbl_evaluation_employee');
        $this->db->join('tblstaff','tblstaff.staffid = tbl_evaluation_employee.staff_id AND tbl_evaluation_employee.type = 1','left');
        $this->db->join('tbl_hr_eprofile','tbl_hr_eprofile.id = tbl_evaluation_employee.staff_id AND tbl_evaluation_employee.type = 2','left');
        $this->db->join('tblroles','tblroles.roleid = tbl_evaluation_employee.role_id','inner');
        $this->db->join('tbl_role_level','tbl_role_level.id = tbl_evaluation_employee.role_level_id','inner');
        $this->db->where('tbl_evaluation_employee.id',$id);
        $data['dtData'] = $this->db->get()->row_array();

        $this->db->select(
            'tbl_evaluation_employee_question.*,
            tbl_question_bank.question,
            tbl_question_bank.type'
        );
        $this->db->from('tbl_evaluation_employee_question');
        $this->db->join('tbl_question_bank','tbl_question_bank.id = tbl_evaluation_employee_question.question_bank_id','inner');
        $this->db->where('tbl_evaluation_employee_question.evaluation_employee_id',$id);
        $dtDataQuestion = $this->db->get()->result_array();
        $countQuestion = count($dtDataQuestion);
        if (!empty($dtDataQuestion)){
            foreach ($dtDataQuestion as $key => $value){
                $this->db->from('tbl_evaluation_employee_question_answer');
                $this->db->where('tbl_evaluation_employee_question_answer.evaluation_employee_id',$id);
                $this->db->where('tbl_evaluation_employee_question_answer.evaluation_employee_question_id',$value['id']);
                $dtDataQuestionAnswer = $this->db->get()->result_array();
                $dtDataQuestion[$key]['dtDataQuestionAnswer'] = $dtDataQuestionAnswer;
            }
        }
        $dtDataQuestion = array_reduce($dtDataQuestion, function ($acc, $item) {
            $acc[$item['type']][] = $item;
            return $acc;
        });
        $data['dtDataQuestion'] = $dtDataQuestion;
        $data['countQuestion'] = $countQuestion;
        $title = lang('Đánh giá nhân viên');
        $data['title'] = $title;
        $data['id'] = $id;
        $data['dtRatingList'] = get_table_where('tbl_rating_list');
        $data['dtTypeQuestion'] = getTypeQuestion();
        $this->load->view('admin/evaluation_employee/process_evaluate',$data);
    }

    public function initDashboard()
    {
        $data = [];
        $dtRatingList = get_table_where('tbl_rating_list');
        $dtRoom = get_table_where('tbl_room');
        $this->db->select('tbl_evaluation_employee.*,tbl_room.id as room_id');
        $this->db->from('tbl_evaluation_employee');
        $this->db->join('tblroles','tblroles.roleid = tbl_evaluation_employee.role_id','inner');
        $this->db->join('tbl_room','tbl_room.id = tblroles.id_room','left');
        $this->db->where('tbl_evaluation_employee.type',$this->type);
        $dtData = $this->db->get()->result_array();
        $dtDataRatingList = array_reduce($dtData, function ($acc, $item) {
            $acc[$item['rating_list']][] = $item;
            return $acc;
        });
        foreach ($dtRatingList as $key => $value){
            $items = $dtDataRatingList[$value['id']] ?? [];
            $dtRatingList[$key]['count'] = count($items);
        }
        $dtDataRoom = [];

        foreach ($dtRoom as $room) {
            foreach ($dtRatingList as $rating) {
                $dtDataRoom[$room['id']][$rating['id']] = 0;
            }
        }

        foreach ($dtData as $row) {
            $roomId = $row['room_id'];
            $rating = $row['rating_list'];

            if (isset($dtDataRoom[$roomId][$rating])) {
                $dtDataRoom[$roomId][$rating]++;
            }
        }
        $categories = array_column($dtRoom, 'name'); // name phòng
        $roomIds    = array_column($dtRoom, 'id');
        $series = [];

        foreach ($dtRatingList as $rating) {
            $data = [];

            foreach ($roomIds as $roomId) {
                $data[] = $dtDataRoom[$roomId][$rating['id']];
            }

            $series[] = [
                'name' => $rating['rating'],
                'data' => $data
            ];
        }
        $seriesRating = [];
        $dataNew = [];
        foreach ($dtRatingList as $rating) {
            $dataNew[] = $rating['count'];
        }
        $seriesRating[] = [
            'name' => 'Số lượng', // Tên của series
            'data' => $dataNew, // Mảng chứa tất cả số lượng
            'dataLabels' => [
                'enabled' => true, // Bật hiển thị label trên cột
                'format' => '{y}',  // Hiển thị giá trị y (số lượng)
                'style' => [
                    'fontSize' => '12px', // Kích thước font của label
                    'fontWeight' => 'bold'
                ]
            ]
        ];
        $categoriesRating = array_column($dtRatingList, 'rating');

        $data['result'] = true;
        $data['dtRatingList'] = $dtRatingList;
        $data['dtDataRoom'] = $dtDataRoom;
        $data['categories'] = $categories;
        $data['series'] = $series;
        $data['categoriesRating'] = $categoriesRating;
        $data['seriesRating'] = $seriesRating;
        echo json_encode($data);
    }
}