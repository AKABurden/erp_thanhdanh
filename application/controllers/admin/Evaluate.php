<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Evaluate extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('evaluate_model');
        $this->load->model('misc_model');
        $this->types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
        $this->upload_path = get_upload_path_by_type('evaluate');
        $this->datetime_now = time();
        $this->tnh = true;
        if (empty($_GET['type'])) {
            $_GET['type'] = 'evaluate';
        }
        if ($_GET['type'] == 'evaluate'){
            $this->preViewEvaluate = has_permission('evaluate','','view');
            $this->preAddEvaluate = has_permission('evaluate','','create');
            $this->preEditEvaluate = has_permission('evaluate','','edit');
            $this->preApproveEvaluate = has_permission('evaluate','','approve');
            $this->preDeleteEvaluate = has_permission('evaluate','','delete');
        } elseif($_GET['type'] == 'educate'){
            $this->preViewEvaluate = has_permission('educate','','view');
            $this->preAddEvaluate = has_permission('educate','','create');
            $this->preEditEvaluate = has_permission('educate','','edit');
            $this->preApproveEvaluate = has_permission('educate','','approve');
            $this->preDeleteEvaluate = has_permission('educate','','delete');
        }
        else
        {
            $this->preViewEvaluate = true;
            $this->preAddEvaluate = true;
            $this->preEditEvaluate = true;
            $this->preApproveEvaluate = true;
            $this->preDeleteEvaluate = true;
        }
    }

    public function index() {
        $data = [];
        if (!$this->preViewEvaluate){
            access_denied();
        }
        $data['title'] = lang('evaluate_view');
        $this->load->view('admin/evaluate/index', $data);
    }

    public function add($id = 0) {
        $data = [];
        $evaluate = $id ? $this->evaluate_model->getEvaluateById($id) : [];
        $type = $this->input->get('type');
        if ($this->input->post('save')) {
            if ((!empty($evaluate) && $evaluate['code_evaluate'] != $this->input->post('code_evaluate')) || empty($evaluate['code_evaluate'])) {
                $this->form_validation->set_rules('code_evaluate', lang("tnh_code_evaluate"), 'required|is_unique[tbl_evaluate.code_evaluate]');
            }
            $this->form_validation->set_rules('name_evaluate', lang("tnh_name_evaluate"), 'required');
            $this->form_validation->set_rules('date_evaluate', lang("tnh_date_evaluate"), 'required');
            if ($_GET['type'] == 'evaluate') {
                $this->form_validation->set_rules('category_evaluate_id', lang("Nhóm đánh giá"), 'required');
                $this->form_validation->set_rules('type_evaluate_id', lang("Loại đánh giá"), 'required');
            }
            if ($this->form_validation->run() == true)
            {
                $data = $this->input->post();
                $date_evaluate = to_sql_date($this->input->post('date_evaluate'), true);
                $code_evaluate = $this->input->post('code_evaluate');
                $name_evaluate = $this->input->post('name_evaluate');
                $category_evaluate_id = !empty($this->input->post('category_evaluate_id')) ? $this->input->post('category_evaluate_id') : 0;
                $type_evaluate_id = !empty($this->input->post('type_evaluate_id')) ? $this->input->post('type_evaluate_id') : 0;
                $content_evaluate = $this->input->post('content_evaluate', false);
                $staff_id = get_staff_user_id();
                $date = date('Y-m-d H:i:s');
                $date_sign = !empty($this->input->post('date_sign') ) ? to_sql_date($this->input->post('date_sign')) : NULL;
                $option = [
                    'date_evaluate' => $date_evaluate,
                    'code_evaluate' => $code_evaluate,
                    'name_evaluate' => $name_evaluate,
                    'category_evaluate_id' => $category_evaluate_id,
                    'type_evaluate_id' => $type_evaluate_id,
                    'content_evaluate' => $content_evaluate,
                    'type' => $type,
                    'time_of_use' => !empty($data['time_of_use']) ? $data['time_of_use'] : NULL,
                    'reissue_date' => !empty($data['reissue_date']) ? to_sql_date($data['reissue_date'], true) : NULL,
                    'unit_of_level' =>  !empty($data['unit_of_level']) ? $data['unit_of_level'] : NULL,
                    'training_unit' =>  !empty($data['training_unit']) ? $data['training_unit'] : NULL,
                    'notary_public' =>  !empty($data['notary_public']) ? $data['notary_public'] : NULL,
                    'adjustment_date' =>  !empty($data['adjustment_date']) ? to_sql_date($data['adjustment_date'], true) : NULL,
                    'date_of_issue' =>  !empty($data['date_of_issue']) ? to_sql_date($data['date_of_issue'], true) : NULL,
                    'active' =>  !empty($data['active']) ? $data['active'] : 0,
                    'date_sign' => $date_sign
                ];
                if ($id) {
                    $option['updated_by'] = $staff_id;
                    $option['date_updated'] = $date;
                    $ins = $this->evaluate_model->updateEvaluate($id, $option);
                    $evaluate_id = $id;
                } else {
                    $option['created_by'] = $staff_id;
                    $option['date_created'] = $date;
                    $ins = $this->evaluate_model->insertEvaluate($option);
                    $evaluate_id = $ins;
                }

                if (!empty($ins)) {
                    if (!empty($evaluate_id)) {
                        if (!empty($_FILES['file']) && is_array($_FILES['file']['name']) && count($_FILES['file']['name']) > 0) {
                            $commentAttachments = handle_evaluate_attachments_array($evaluate_id, 'file');
                            if ($commentAttachments && is_array($commentAttachments)) {
                                foreach ($commentAttachments as $file) {
                                    $file['task_comment_id'] = 0;
                                    $this->misc_model->add_attachment_to_database($evaluate_id, 'evaluate', [$file]);
                                }
                            }
                        }
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
        if (empty($id)){
            if (!$this->preAddEvaluate){
                accessDenied(true);
            }
        } else {
            if (!$this->preEditEvaluate){
                accessDenied(true);
            }
        }

        $data['dtCategoryEvaluate'] = get_table_where('tbl_category_evaluate');
        $data['dtCategoryEvaluateDetail'] = get_table_where('tbl_category_evaluate_detail',['category_evaluate_id' => !empty($evaluate['type_evaluate_id']) ? $evaluate['type_evaluate_id'] : 0]);
        $data['dtTypeEvaluate'] = get_table_where('tbl_type_evaluate',['type' => $type]);
        $data['type'] = $type;
        $data['evaluate'] = $evaluate;
        $data['id'] = $id;
        $data['title'] = $id ? lang('tnh_edit_evaluate') : lang('tnh_add_evaluate');
        $this->load->view('admin/evaluate/add', $data);
    }

    public function getEvaluate() {
        $type = $this->input->post('type_search');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');

        $tbFiles = "(
            SELECT
                tblfiles.rel_id as rel_id,
                COUNT(tblfiles.id) as ct_file
            FROM tblfiles
            WHERE tblfiles.rel_type = 'evaluate'
            GROUP BY tblfiles.rel_id
        ) tb_files";

        $aColumns = [
            'tbl_evaluate.id as id',
            'tbl_evaluate.date_evaluate as date_evaluate',
            'tbl_evaluate.code_evaluate as code_evaluate',
            'tbl_evaluate.name_evaluate as name_evaluate',
            'tbl_evaluate.content_evaluate as content_evaluate',
            'tb_files.ct_file as file',
            'tbl_evaluate.status as status',
            'tbl_evaluate.date_sign as date_sign',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_evaluate';
        $where        = [
        ];
        $filter = [];
        
        $join = [
            'LEFT JOIN tbl_category_evaluate ON tbl_category_evaluate.id = tbl_evaluate.type_evaluate_id AND tbl_evaluate.type = "evaluate"',
            'LEFT JOIN tbl_category_evaluate_detail ON tbl_category_evaluate_detail.id = tbl_evaluate.category_evaluate_id AND tbl_evaluate.type = "evaluate"',
            'LEFT JOIN tbl_type_evaluate ON tbl_type_evaluate.id = tbl_evaluate.type_evaluate_id AND tbl_evaluate.type = "educate"',
            'LEFT JOIN tblstaff staff_status ON staff_status.staffid = tbl_evaluate.user_status',
            'LEFT JOIN '.$tbFiles.' ON tb_files.rel_id = tbl_evaluate.id',
        ];

        array_push($where, "AND tbl_evaluate.type = ".$this->db->escape($type)."");
        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search).' 00:00:00';
            array_push($where, "AND tbl_evaluate.date_evaluate >= '".$start_date_search."'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search).' 23:59:59';
            array_push($where, "AND tbl_evaluate.date_evaluate <= '".$end_date_search."'");
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'CONCAT(staff_status.firstname, " ", staff_status.lastname) as user_status',
            'tbl_category_evaluate.name as name_type_evaluate',
            'tbl_category_evaluate_detail.name as name_category_evaluate',
            'tbl_type_evaluate.name as name_type_education',
            '(
                SELECT GROUP_CONCAT(CONCAT(tblinternal_proposal.id, ",", tblinternal_proposal.code) SEPARATOR "|||")
                FROM tblinternal_proposal 
                JOIN tbl_category_recommended ON tbl_category_recommended.id = tblinternal_proposal.category_recommended_id 
                    AND tbl_category_recommended.type = "license"
                    AND tbl_category_recommended.name_table = "tbl_evaluate"
                WHERE tblinternal_proposal.suggest_id = tbl_evaluate.id
            ) as internal_proposal
            '
        ], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];
            $row[] = $id;
            $row[] = _dt($aRow['date_evaluate']);
            if ($type == 'evaluate') {
                $row[] = $aRow['name_category_evaluate'];
                $row[] = $aRow['name_type_evaluate'];
            } elseif($type == 'educate'){
                $row[] = $aRow['name_type_education'];
            }
            $row[] = $aRow['code_evaluate'];
            $row[] = $aRow['name_evaluate'];
            $row[] = $aRow['content_evaluate'];
            $row[] = '<div class="text-center text-primary"><span class="fa fa-file-text-o"></span> '.formatNumber($aRow['file']).' tập tin'.'</div>';

            if ($aRow['status'] == 1) {
                $user_status = '<div class="mtop10">'.lang('tnh_user_agree').': ' .$aRow['user_status'] . '</div>';
            } else {
                $user_status = '';
            }

            if ($aRow['status'] == 0) {
                $str = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="'.lang('tnh_agree').'" data-content="<p><a id=\'agree-evaluate\' evaluate_id=\'' .$id. '\' value=\'1\' class=\'btn btn-success\'>'.lang('tnh_agree').'</a><button class=\'btn po-close\'>'.lang('close').'</button></p>" class="label label-danger po">'.lang('tnh_un_approved').'</span></div>' . $user_status;
            } elseif ($aRow['status'] == 1) {
                $str = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="'.lang('tnh_agree').'" data-content="<p><a id=\'agree-evaluate\' evaluate_id=\'' .$id. '\' value=\'0\' class=\'btn btn-danger\'>'.lang('tnh_agree').'</a><button class=\'btn po-close\'>'.lang('close').'</button></p>" class="label label-success po">'.lang('tnh_un_approved').'</span></div>' . $user_status;
            }
            $viewInternal = [];
            if(!empty($aRow['internal_proposal'])) {
                $internal_proposal = explode("|||", $aRow['internal_proposal']);
                foreach($internal_proposal as $k => $v) {
                    $detail_internal_proposal = explode(",", $v);
                    $viewInternal[] = '<a class="c_modal" href="'.admin_url('internal_proposal/view/'.$detail_internal_proposal[0]).'">'.$detail_internal_proposal[1].'</a>';
                }
                $str .= '<hr class="mtop5 mbot5"/><div class="text-center">' . implode("<br/>", $viewInternal).'</div>';
            }

            $row[] = '<div>'.$str.'</div>';

            $row[] = !empty($aRow['date_sign']) ? _dhau($aRow['date_sign']) : '';

            $createInternalProposal = '';
            if($aRow['status'] == 1 && empty($viewInternal)) {
                $createInternalProposal = '<a class="c_modal" href="'.admin_url('internal_proposal/add_modal?type_object=evaluate&id_object='.$id.'&type_append='.$type.'').'"><i class="fa fa-hand-paper-o"></i>Tạo Đề Xuất Nội Bộ</a>';
            }
            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/evaluate/view/'.$id.'?type='.$type) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('tnh_view_evaluate') . '</a>';
            $edit = '<a class="tnh-modal" href="' . base_url('admin/evaluate/add/'.$id.'?type='.$type) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit_evaluate') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/evaluate/delete/'.$id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('tnh_delete_evaluate') . '</a>';
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                    <li>' . $view . '</li>
                    <li>' . $edit . '</li>
                    <li>' . $createInternalProposal . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';


            $row[] = $actions;

            
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function delete($id) {
        $data = [];
        if (!$this->preDeleteEvaluate){
            $data['result'] = 0;
            $data['message'] = lang('Không có quyền xóa');
            echo json_encode($data);die();
        }
        if ($this->evaluate_model->deleteEvaluate($id)) {

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'evaluate');
            $attachments = $this->db->get(db_prefix() . 'files')->result_array();
            foreach ($attachments as $at) {
                $this->evaluate_model->remove_evaluate_attachment($at['id']);
            }

            if (is_dir(get_upload_path_by_type('evaluate') . $id)) {
                delete_dir(get_upload_path_by_type('evaluate') . $id);
            }

            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function view($id) {
        $data = [];
        $evaluate = $this->evaluate_model->getEvaluateById($id);

        $dtCategoryEvaluate = get_table_where('tbl_category_evaluate',['id' => $evaluate['category_evaluate_id']],'','row_array');
        $dtTypeEvaluate = get_table_where('tbl_type_evaluate',['id' => $evaluate['type_evaluate_id']],'','row_array');

        $data['attachments'] = $this->evaluate_model->getFileEvaluate($id);
        $data['evaluate'] = $evaluate;
        $data['dtCategoryEvaluate'] = $dtCategoryEvaluate;
        $data['dtTypeEvaluate'] = $dtTypeEvaluate;
        $data['id'] = $id;
        $data['title'] = lang('tnh_view_evaluate');
        $this->load->view('admin/evaluate/view', $data);
    }

    public function remove_evaluate_attachment($id)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->evaluate_model->remove_evaluate_attachment($id));
        }
    }

    public function agree()
    {
        $data = [];
         if (!$this->preApproveEvaluate) {
             $data['result'] = 0;
             $data['message'] = lang('access_denied');
             echo json_encode($data);
             die;
         }
        if ($this->input->get()) {
            $evaluate_id = $this->input->get('evaluate_id');
            $status = $this->input->get('status');
            $evaluate = $this->evaluate_model->getEvaluateById($evaluate_id);
            $date = date('Y-m-d H:i:s');
            $user_id = get_staff_user_id();
            if ($evaluate['status'] == $status) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_please_referesh_table');
                echo json_encode($data);
                die;
            }

            $up = $this->evaluate_model->updateEvaluate($evaluate_id, [
                'status' => $status,
                'date_status' => $date,
                'user_status' => $user_id
            ]);
            if ($up) {
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        }
        echo json_encode($data);
    }

    public function getListCategoryEvaluate(){
        $type_evaluate_id = $this->input->post('type_evaluate_id');
        $this->db->select('tbl_category_evaluate_detail.*');
        $this->db->from('tbl_category_evaluate_detail');
        $this->db->where('category_evaluate_id',$type_evaluate_id);
        $dtResult = $this->db->get()->result_array();
        $data['listCategoryEvaluate'] = $dtResult;
        echo json_encode($data);
    }
}