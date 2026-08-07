<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Job_detail extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->preViewJobDetail = true;
        $this->preAddJobDetail = true;
        $this->preDeleteJobDetail = true;
    }

    public function index()
    {
        if (!$this->preViewJobDetail) {
            access_denied('job_detail');
        }
        $data['title'] = _l('Mô tả công việc');
        $this->load->view('admin/job_detail/index', $data);
    }

    public function getJobDetail(){
        $role_id_search = $this->input->post('role_id_search') ?? '';
        $filterStatus = $this->input->post('filterStatus');
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $tb_other1 = "(
            SELECT 
                GROUP_CONCAT(tbl_job_detail_child.name) as name_other1,
                tbl_job_detail_child.job_detail_id
            FROM tbl_job_detail_child
            WHERE tbl_job_detail_child.type = 1
            GROUP BY tbl_job_detail_child.job_detail_id
        ) tb_other1";

        $tb_other2 = "(
            SELECT 
                GROUP_CONCAT(tbl_job_detail_child.name) as name_other2,
                tbl_job_detail_child.job_detail_id
            FROM tbl_job_detail_child
            WHERE tbl_job_detail_child.type = 2
            GROUP BY tbl_job_detail_child.job_detail_id
        ) tb_other2";

        $tb_other3 = "(
            SELECT 
                GROUP_CONCAT(tbl_job_detail_child.name) as name_other3,
                tbl_job_detail_child.job_detail_id
            FROM tbl_job_detail_child
            WHERE tbl_job_detail_child.type = 3
            GROUP BY tbl_job_detail_child.job_detail_id
        ) tb_other3";

        $tb_other4 = "(
            SELECT 
                GROUP_CONCAT(tbl_job_detail_child.name) as name_other4,
                tbl_job_detail_child.job_detail_id
            FROM tbl_job_detail_child
            WHERE tbl_job_detail_child.type = 4
            GROUP BY tbl_job_detail_child.job_detail_id
        ) tb_other4";

        $aColumns = [
            'tbl_job_detail.id as id',
            'tbl_job_detail.code as code',
            'tblroles.code_role as code_role',
            'tbl_job_detail.version as version',
            'tbl_job_detail.status as status',
            'tbl_job_detail.title as title',
            'tbl_job_detail.goal as goal',
            'tb_other1.name_other1 as responsibility',
            'tb_other2.name_other2 as scope',
            'tb_other3.name_other3 as request',
            'tb_other4.name_other4 as standard',
            'tbl_job_detail.date_issue as date_issue',
            'tbl_job_detail.month_review as month_review',
            'tbl_job_detail.last_review_date as last_review_date',
            'tbl_job_detail.date_end as date_end',
            'tbl_job_detail.link_jd_doc as link_jd_doc',
            'tbl_job_detail.note as note',
            '"" as actions',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_job_detail';
        $where = [

        ];
        $filter = [];
        $join = [
            'INNER JOIN tblroles ON tblroles.roleid = tbl_job_detail.role_id',
            'LEFT JOIN '.$tb_other1.' ON tb_other1.job_detail_id = tbl_job_detail.id',
            'LEFT JOIN '.$tb_other2.' ON tb_other2.job_detail_id = tbl_job_detail.id',
            'LEFT JOIN '.$tb_other3.' ON tb_other3.job_detail_id = tbl_job_detail.id',
            'LEFT JOIN '.$tb_other4.' ON tb_other4.job_detail_id = tbl_job_detail.id',
        ];
        if (is_numeric($filterStatus)){
            $where[] = 'AND tbl_job_detail.status = '.$filterStatus.'';
        }
        if (!empty($role_id_search)){
            $where[] = 'AND tbl_job_detail.role_id = '.$role_id_search.'';
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left"><a class="tnh-modal" href="' . base_url('admin/job_detail/view/' . $aRow['id']) . '">'.$aRow['code'].'</a></div>';
            $row[] = '<div class="text-left">'.$aRow['code_role'].'</div>';
            $row[] = '<div class="text-left">'.$aRow['version'].'</div>';
            $checked = '';
            if ($aRow['status'] == 1) {
                $checked = 'checked';
            }
            $_data = '<div class="onoffswitch">
                        <input type="checkbox" data-switch-url-2="' . admin_url() . 'job_detail/changeStatus" name="onoffswitch_new" class="onoffswitch-checkbox" id="c_new' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . $checked . '>
                        <label class="onoffswitch-label" for="c_new' . $aRow['id'] . '"></label>
                    </div>';
            $row[] = '<div class="text-left">'.$_data.'</div>';
            $row[] = '<div class="text-left">'.($aRow['title']).'</div>';
            $row[] = '<div class="text-left">'.($aRow['goal']).'</div>';
            $row[] = '<div class="text-left">'.$aRow['responsibility'].'</div>';
            $row[] = '<div class="text-left">'.$aRow['scope'].'</div>';
            $row[] = '<div class="text-left">'.$aRow['request'].'</div>';
            $row[] = '<div class="text-left">'.$aRow['standard'].'</div>';
            $row[] = '<div class="text-left">'.(!empty($aRow['date_issue']) ? _dhau($aRow['date_issue']) : '').'</div>';
            $row[] = '<div class="text-center">'.(!empty($aRow['month_review']) ? ($aRow['month_review']) : '').'</div>';
            $row[] = '<div class="text-left">'.(!empty($aRow['last_review_date']) ? _dhau($aRow['last_review_date']) : '').'</div>';
            $row[] = '<div class="text-left">'.(!empty($aRow['date_end']) ? _dhau($aRow['date_end']) : '').'</div>';
            $row[] = '<div class="text-left">'.($aRow['link_jd_doc']).'</div>';
            $row[] = '<div class="text-left">'.($aRow['note']).'</div>';


            $view = '<a class="tnh-modal" href="' . base_url('admin/job_detail/view/' . $aRow['id']) . '"><i class="fa fa-eye width-icon-actions"></i> ' . lang('Xem chi tiết') . '</a>';
            $copy = '<a class="tnh-modal" href="' . base_url('admin/job_detail/detail/' . $aRow['id'].'/copy') . '"><i class="fa fa-plus width-icon-actions"></i> ' . lang('Tạo phiên bản mới') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/job_detail/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete')  . '</a>';
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $view . '</li>
                    <li>' . $copy . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[] = '<div>' . $actions . '</div>';
            $output['aaData'][] = $row;
        }

        $output['total'] = [];
        $output['total'][1] = $this->db->get_where('tbl_job_detail', ['status' => 1])->num_rows();
        $output['total'][0] = $this->db->get_where('tbl_job_detail', ['status' => 0])->num_rows();
        $output['total']['all'] = ($output['total'][1] + $output['total'][0]);
        echo json_encode($output);
    }

    public function detail($id = 0,$action = 'add'){
        if ($this->input->post()){
            // $this->form_validation->set_rules('code', lang("Mã mô tả công việc"), 'required');
            $this->form_validation->set_rules('title', lang("Tiêu đề"), 'required');
            // $this->form_validation->set_rules('version', lang("Version"), 'required');
            $this->form_validation->set_rules('date_issue', lang("Ngày phát hành"), 'required');
            $this->form_validation->set_rules('role_id', lang("Mã vị trí"), 'required');
            if ($this->form_validation->run() == true) {
                // $code = $this->input->post('code');
                $code = 'JD-'.sprintf('%06d', ch_getMaxID('id', 'tbl_job_detail') + 1);
                $title = $this->input->post('title');
                // $version = $this->input->post('version');
                $version = 'v.'.(ch_getMaxID('id', 'tbl_job_detail') + 1);

                $date_issue = $this->input->post('date_issue');
                $month_review = $this->input->post('month_review') ?? 0;
                $goal = $this->input->post('goal');
                $note = $this->input->post('note');
                $link_jd_doc = $this->input->post('link_jd_doc');
                $role_id = $this->input->post('role_id');
                $date_end = date('Y-m-d', strtotime('+'.$month_review.' month', strtotime($date_issue)));

                $this->db->where('code',$code);
                $this->db->from('tbl_job_detail');
                $checkExists = $this->db->count_all_results();
                if (!empty($checkExists)){
                    $data['result'] = false;
                    $data['message'] = lang('Mã mô tả công việc đã tồn tại');
                    echo json_encode($data);die();
                }

                $this->db->where('role_id',$role_id);
                $this->db->where('version',$version);
                $this->db->from('tbl_job_detail');
                $checkExists = $this->db->count_all_results();
                if (!empty($checkExists)){
                    $data['result'] = false;
                    $data['message'] = lang('Version vị trí đã tồn tại');
                    echo json_encode($data);die();
                }



                $dataPost = $this->input->post();
                $counterOther1 = $this->input->post('counterOther1') ?? [];
                $arrOther1 = [];
                if (!empty($counterOther1)){
                    foreach ($counterOther1 as $key => $value){
                        $type = $dataPost['type_other1'][$key] ?? 1;
                        $name = $dataPost['name_other1'][$key] ?? null;
                        $arrOther1[] = [
                            'name' => $name,
                            'type' => $type,
                        ];
                    }
                }

                $counterOther2 = $this->input->post('counterOther2') ?? [];
                $arrOther2 = [];
                if (!empty($counterOther2)){
                    foreach ($counterOther2 as $key => $value){
                        $type = $dataPost['type_other2'][$key] ?? 2;
                        $name = $dataPost['name_other2'][$key] ?? null;
                        $arrOther2[] = [
                            'name' => $name,
                            'type' => $type,
                        ];
                    }
                }

                $counterOther3 = $this->input->post('counterOther3') ?? [];
                $arrOther3 = [];
                if (!empty($counterOther3)){
                    foreach ($counterOther3 as $key => $value){
                        $type = $dataPost['type_other3'][$key] ?? 3;
                        $name = $dataPost['name_other3'][$key] ?? null;
                        $arrOther3[] = [
                            'name' => $name,
                            'type' => $type,
                        ];
                    }
                }

                $counterOther4 = $this->input->post('counterOther4') ?? [];
                $arrOther4 = [];
                if (!empty($counterOther4)){
                    foreach ($counterOther4 as $key => $value){
                        $type = $dataPost['type_other4'][$key] ?? 4;
                        $name = $dataPost['name_other4'][$key] ?? null;
                        $arrOther4[] = [
                            'name' => $name,
                            'type' => $type,
                        ];
                    }
                }

                $option = [
                    'date' => date('Y-m-d H:i:s'),
                    'code' => $code,
                    'title' => $title,
                    'goal' => $goal,
                    'version' => $version,
                    'status' => 1,
                    'date_issue' => to_sql_date($date_issue),
                    'month_review' => $month_review,
                    'last_review_date' => to_sql_date($date_issue),
                    'date_end' => $date_end,
                    'link_jd_doc' => $link_jd_doc,
                    'note' => $note,
                    'role_id' => $role_id,
                    'created_by' => get_staff_user_id(),
                    'date_created' => date('Y-m-d H:i:s')
                ];

                if(empty($id)){
                    $this->db->insert('tbl_job_detail',$option);
                    $job_detail_id = $this->db->insert_id();
                    if ($job_detail_id){
                        $this->db->where('role_id', $role_id);
                        $this->db->where('id != "'.$job_detail_id.'"', false, false);
                        $this->db->update('tbl_job_detail', [
                            'status' => 0
                        ]);
                        $this->db->where('type',1);
                        $this->db->where('job_detail_id',$job_detail_id);
                        $this->db->delete('tbl_job_detail_child');
                        if (!empty($arrOther1)){
                            foreach ($arrOther1 as $key => $value){
                                $arrOther1[$key]['job_detail_id'] = $job_detail_id;
                            }

                            $this->db->insert_batch('tbl_job_detail_child',$arrOther1);
                        }

                        $this->db->where('type',2);
                        $this->db->where('job_detail_id',$job_detail_id);
                        $this->db->delete('tbl_job_detail_child');
                        if (!empty($arrOther2)){
                            foreach ($arrOther2 as $key => $value){
                                $arrOther2[$key]['job_detail_id'] = $job_detail_id;
                            }
                            $this->db->insert_batch('tbl_job_detail_child',$arrOther2);
                        }

                        $this->db->where('type',3);
                        $this->db->where('job_detail_id',$job_detail_id);
                        $this->db->delete('tbl_job_detail_child');
                        if (!empty($arrOther3)){
                            foreach ($arrOther3 as $key => $value){
                                $arrOther3[$key]['job_detail_id'] = $job_detail_id;
                            }
                            $this->db->insert_batch('tbl_job_detail_child',$arrOther3);
                        }

                        $this->db->where('type',4);
                        $this->db->where('job_detail_id',$job_detail_id);
                        $this->db->delete('tbl_job_detail_child');

                        if (!empty($arrOther4)){
                            foreach ($arrOther4 as $key => $value){
                                $arrOther4[$key]['job_detail_id'] = $job_detail_id;
                            }
                            $this->db->insert_batch('tbl_job_detail_child',$arrOther4);
                        }
                        $data['result'] = 1;
                        $data['message'] = lang('Thêm thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Thêm thất bị');
                    }
                    echo json_encode($data);die();
                } else {
                    if ($action == 'copy'){
                        $this->db->insert('tbl_job_detail',$option);
                        $job_detail_id = $this->db->insert_id();
                        if ($job_detail_id){
                            $this->db->where('role_id', $role_id);
                            $this->db->where('id != "'.$job_detail_id.'"', false, false);
                            $this->db->update('tbl_job_detail', [
                                'status' => 0
                            ]);
                            $this->db->where('type',1);
                            $this->db->where('job_detail_id',$job_detail_id);
                            $this->db->delete('tbl_job_detail_child');
                            if (!empty($arrOther1)){
                                foreach ($arrOther1 as $key => $value){
                                    $arrOther1[$key]['job_detail_id'] = $job_detail_id;
                                }

                                $this->db->insert_batch('tbl_job_detail_child',$arrOther1);
                            }

                            $this->db->where('type',2);
                            $this->db->where('job_detail_id',$job_detail_id);
                            $this->db->delete('tbl_job_detail_child');
                            if (!empty($arrOther2)){
                                foreach ($arrOther2 as $key => $value){
                                    $arrOther2[$key]['job_detail_id'] = $job_detail_id;
                                }
                                $this->db->insert_batch('tbl_job_detail_child',$arrOther2);
                            }

                            $this->db->where('type',3);
                            $this->db->where('job_detail_id',$job_detail_id);
                            $this->db->delete('tbl_job_detail_child');
                            if (!empty($arrOther3)){
                                foreach ($arrOther3 as $key => $value){
                                    $arrOther3[$key]['job_detail_id'] = $job_detail_id;
                                }
                                $this->db->insert_batch('tbl_job_detail_child',$arrOther3);
                            }

                            $this->db->where('type',4);
                            $this->db->where('job_detail_id',$job_detail_id);
                            $this->db->delete('tbl_job_detail_child');

                            if (!empty($arrOther4)){
                                foreach ($arrOther4 as $key => $value){
                                    $arrOther4[$key]['job_detail_id'] = $job_detail_id;
                                }
                                $this->db->insert_batch('tbl_job_detail_child',$arrOther4);
                            }
                            $data['result'] = 1;
                            $data['message'] = lang('Sao chép thành công');
                        } else {
                            $data['result'] = 0;
                            $data['message'] = lang('Sao chép thất bị');
                        }
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
            $title = lang('Thêm mới mô tả công việc');
        } else {
            $title = lang('Tạo phiên bản mới mô tả công việc');
            $dtData = get_table_where('tbl_job_detail',['id' => $id],'','row_array');
        }
        $data['title'] = $title;
        $data['dtData'] = $dtData ?? null;
        $data['id'] = $id;
        $data['action'] = $action;

        $this->load->view('admin/job_detail/detail',$data);
    }

    public function changeStatus($id, $status){
        $data = [];
        $this->db->where('id', $id);
        $success = $this->db->update('tbl_job_detail', [
            'status' => $status
        ]);

        if ($success) {
            if($status == 1) {
                $job_detail = $this->db->get_where('tbl_job_detail', ['id' => $id])->row();
                if(!empty($job_detail)){
                    $this->db->where('role_id', $job_detail->role_id);
                    $this->db->where('id != "'.$job_detail->id.'"', false, false);
                    $this->db->update('tbl_job_detail', [
                        'status' => 0
                    ]);
                }
            }
            $data['result'] = 1;
            $data['message'] = lang('Thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Thất bại');
        }
        echo json_encode($data);
    }

    public function view($id)
    {
        $data['title'] = lang('Xem chi tiết mô tả công việc');
        $this->db->select('tbl_job_detail.*,tblroles.code_role as code_role');
        $this->db->from('tbl_job_detail');
        $this->db->join('tblroles','tblroles.roleid = tbl_job_detail.role_id','left');
        $this->db->where('tbl_job_detail.id', $id);
        $dtData = $this->db->get()->row_array();
        $data['dtData'] = $dtData;

        $this->db->select('tbl_job_detail_child.*');
        $this->db->from('tbl_job_detail_child');
        $this->db->where('job_detail_id', $id);
        $dtDataChild = $this->db->get()->result_array();
        $data['dtDataChild'] = $dtDataChild;

        $this->load->view('admin/job_detail/view',$data);
    }

    public function delete($id){
        if (!$this->preDeleteJobDetail){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_job_detail.*');
        $this->db->from('tbl_job_detail');
        $this->db->where('tbl_job_detail.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_job_detail');
        if ($success){

            $this->db->where('job_detail_id',$id);
            $this->db->delete('tbl_job_detail_child');

            insertActivityLog([
                'type_parent_obj' => 'job_detail',
                'table_obj' => 'tbl_job_detail',
                'id_obj' => $id,
                'name_obj' => $dtData['code'],
                'content' => lang('Xóa mô tả công việc') . ' [' . $dtData['code'] . ']',
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

    public function exportExcel(){
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            $role_id_search = $this->input->post('role_id_search');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();

            $staff_id = get_staff_user_id();
            $tb_other1 = "(
                SELECT 
                    GROUP_CONCAT(tbl_job_detail_child.name) as name_other1,
                    tbl_job_detail_child.job_detail_id
                FROM tbl_job_detail_child
                WHERE tbl_job_detail_child.type = 1
                GROUP BY tbl_job_detail_child.job_detail_id
            ) tb_other1";

            $tb_other2 = "(
                SELECT 
                    GROUP_CONCAT(tbl_job_detail_child.name) as name_other2,
                    tbl_job_detail_child.job_detail_id
                FROM tbl_job_detail_child
                WHERE tbl_job_detail_child.type = 2
                GROUP BY tbl_job_detail_child.job_detail_id
            ) tb_other2";

            $tb_other3 = "(
                SELECT 
                    GROUP_CONCAT(tbl_job_detail_child.name) as name_other3,
                    tbl_job_detail_child.job_detail_id
                FROM tbl_job_detail_child
                WHERE tbl_job_detail_child.type = 3
                GROUP BY tbl_job_detail_child.job_detail_id
            ) tb_other3";

            $tb_other4 = "(
                SELECT 
                    GROUP_CONCAT(tbl_job_detail_child.name) as name_other4,
                    tbl_job_detail_child.job_detail_id
                FROM tbl_job_detail_child
                WHERE tbl_job_detail_child.type = 4
                GROUP BY tbl_job_detail_child.job_detail_id
            ) tb_other4";

            $this->db->select('
                tbl_job_detail.id as id,
                tbl_job_detail.code as code,
                tblroles.code_role as code_role,
                tbl_job_detail.version as version,
                tbl_job_detail.status as status,
                tbl_job_detail.title as title,
                tbl_job_detail.goal as goal,
                tb_other1.name_other1 as responsibility,
                tb_other2.name_other2 as scope,
                tb_other3.name_other3 as request,
                tb_other4.name_other4 as standard,
                tbl_job_detail.date_issue as date_issue,
                tbl_job_detail.month_review as month_review,
                tbl_job_detail.last_review_date as last_review_date,
                tbl_job_detail.date_end as date_end,
                tbl_job_detail.link_jd_doc as link_jd_doc,
                tbl_job_detail.note as note
            ');
            $this->db->from('tbl_job_detail');
            $this->db->join('tblroles','tblroles.roleid = tbl_job_detail.role_id','inner');
            $this->db->join($tb_other1,'tb_other1.job_detail_id = tbl_job_detail.id','left');
            $this->db->join($tb_other2,'tb_other2.job_detail_id = tbl_job_detail.id','left');
            $this->db->join($tb_other3,'tb_other3.job_detail_id = tbl_job_detail.id','left');
            $this->db->join($tb_other4,'tb_other4.job_detail_id = tbl_job_detail.id','left');


            if (!empty($role_id_search)) {
                $this->db->where("tbl_job_detail.role_id = $role_id_search");
            }

            $this->db->order_by('tbl_job_detail.id desc');
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
            $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf("%0" . $decimals_number . "s",
                        0) : '');
            $company = get_option('invoice_company_name');
            $address = get_option('invoice_company_address');
            $company_vat = get_option('company_vat');
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name'  => 'Times New Roman'
                ),
            ]);

            insertCompanyInfo($objPHPExcel, 'C1:Q2', 'A1');

            $objPHPExcel->getActiveSheet()->setCellValue('A5',
                ('PHIẾU MÔ TẢ CÔNG VIỆC'))->getStyle("A5")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A5:Q5');
            $sttRow = 2 + 4;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Mã công việc')->getStyle("B$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Mã vị trí');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Version');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Trạng thái')->getStyle("E$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Tiêu đề công việc')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Mục tiêu')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Trách nhiệm')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Phạm vi quyền hạn')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Yêu cầu công việc')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Tiêu chuẩn năng lực')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Ngày ban hành')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$sttRow.'', 'Thời gian hết hạn')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$sttRow.'', 'Ngày ban hành mới nhất')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$sttRow.'', 'Ngày hết hạn')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$sttRow.'', 'Đường dẫn tài liệu')->getStyle("P$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$sttRow.'', 'Ghi chú')->getStyle("Q$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:Q$sttRow")->applyFromArray([
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
                    $htmlStatus = $value['status'] == 1 ? 'Hoạt động' : 'Không hoạt động';
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['code']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['code_role']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['version'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", $htmlStatus)->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $value['title'])->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin",$value['goal'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin",$value['responsibility'])->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin",$value['scope'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $value['request'])->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $value['standard'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", !empty($value['date_issue']) ? _dhau($value['date_issue']) : '')->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", $value['month_review'])->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", !empty($value['last_review_date']) ? _dhau($value['last_review_date']) : '')->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", !empty($value['date_end']) ? _dhau($value['date_end']) : '')->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", $value['link_jd_doc'] )->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin", $value['note'] )->getStyle("Q$rowBegin")->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:Q$rowBegin")->applyFromArray([
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
                }
            }
            $filename = lang('phieu_mo_ta_cong_viec') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(30);
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

    public function import()
    {
        $data = [];
        if (!empty($_FILES)){
            ini_set('max_execution_time', 800);
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
                // $objReader->setReadDataOnly(true);
                $objPHPExcel = $objReader->load("$fullfile");

                $total_sheets = $objPHPExcel->getSheetCount();

                $allSheetName = $objPHPExcel->getSheetNames();
                $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
                $highestRow = $objWorksheet->getHighestRow();
                $highestColumn = $objWorksheet->getHighestColumn();
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('Q');
                $arraydata = array();

                $fields = $this->input->post('fields');
                for ($row = 2; $row <= $highestRow; ++$row) {
                    for ($col = 0; $col < $highestColumnIndex; ++$col) {
                        $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                        $arraydata[$row - 2][$col] = $value;
                    }
                }
                $dataArray = [];
                $arrData = [];
                $statusMap = [
                    "Hoạt động" => 1,
                    "Không hoạt động" => 0
                ];
                $count = 0;
                foreach ($arraydata as $key => $value) {

                    $code = (preg_replace('/\s+/', ' ', trim($value[1])));
                    $code_role = (preg_replace('/\s+/', ' ', trim($value[2])));
                    $version = (preg_replace('/\s+/', ' ', trim($value[3])));
                    $status = (preg_replace('/\s+/', ' ', trim($value[4])));
                    $title = (preg_replace('/\s+/', ' ', trim($value[5])));
                    $goal = (preg_replace('/\s+/', ' ', trim($value[6])));
                    $responsibility = (preg_replace('/\s+/', ' ', trim($value[7])));
                    $scope = (preg_replace('/\s+/', ' ', trim($value[8])));
                    $request = (preg_replace('/\s+/', ' ', trim($value[9])));
                    $standard = (preg_replace('/\s+/', ' ', trim($value[10])));
                    $date_issue = (preg_replace('/\s+/', ' ', trim($value[11])));
                    $month_review = (preg_replace('/\s+/', ' ', trim($value[12])));
                    $last_review_date = (preg_replace('/\s+/', ' ', trim($value[13])));
                    $date_end = (preg_replace('/\s+/', ' ', trim($value[14])));
                    $link_jd_doc = (preg_replace('/\s+/', ' ', trim($value[15])));
                    $note = (preg_replace('/\s+/', ' ', trim($value[16])));

                    $this->db->from('tblroles');
                    $this->db->where('code_role',$code_role);
                    $dtRole = $this->db->get()->row_array();
                    if (empty($dtRole)){
                        $errors .= lang('Mã '.$code_role.' vị trí không tồn tại');
                        continue;
                    }

                    $this->db->where('code',$code);
                    $this->db->from('tbl_job_detail');
                    $checkExists = $this->db->count_all_results();
                    if (!empty($checkExists)){
                        $errors .= lang('Mã '.$code.' mô tả công việc đã tồn tại');
                        continue;
                    }

                    $role_id = $dtRole['roleid'];

                    $this->db->where('role_id',$role_id);
                    $this->db->where('version',$version);
                    $this->db->from('tbl_job_detail');
                    $checkExists = $this->db->count_all_results();
                    if (!empty($checkExists)){
                        $errors .= lang('Version '.$version.' vị trí '.$code.' đã tồn tại');
                        continue;
                    }
                    $arrOther1 = [];
                    if (!empty($responsibility)){
                        $responsibility = explode(',',$responsibility);
                        if (!empty($responsibility)){
                            foreach ($responsibility as $key => $value){
                                $arrOther1[] = [
                                    'name' => $value,
                                    'type' => 1,
                                ];
                            }
                        }
                    }
                    $arrOther2 = [];
                    if (!empty($scope)){
                        $scope = explode(',',$scope);
                        if (!empty($scope)){
                            foreach ($scope as $key => $value){
                                $arrOther2[] = [
                                    'name' => $value,
                                    'type' => 2,
                                ];
                            }
                        }
                    }
                    $arrOther3 = [];
                    if (!empty($request)){
                        $request = explode(',',$request);
                        if (!empty($request)){
                            foreach ($request as $key => $value){
                                $arrOther3[] = [
                                    'name' => $value,
                                    'type' => 3,
                                ];
                            }
                        }
                    }
                    $arrOther4 = [];
                    if (!empty($standard)){
                        $standard = explode(',',$standard);
                        if (!empty($standard)){
                            foreach ($standard as $key => $value){
                                $arrOther4[] = [
                                    'name' => $value,
                                    'type' => 4,
                                ];
                            }
                        }
                    }

                    $status = trim($status); // loại bỏ khoảng trắng đầu/cuối

                    $valueStatus = 1;
                    if (isset($statusMap[$status])) {
                        $valueStatus = $statusMap[$status];
                    }

                    $option = [
                        'date' => date('Y-m-d H:i:s'),
                        'code' => $code,
                        'title' => $title,
                        'goal' => $goal,
                        'version' => $version,
                        'status' => $valueStatus,
                        'date_issue' => to_sql_date($date_issue),
                        'month_review' => $month_review,
                        'last_review_date' => to_sql_date($last_review_date),
                        'date_end' => to_sql_date($date_end),
                        'link_jd_doc' => $link_jd_doc,
                        'note' => $note,
                        'role_id' => $role_id,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s')
                    ];
                    $this->db->insert('tbl_job_detail',$option);
                    $job_detail_id = $this->db->insert_id();
                    if ($job_detail_id){
                        $this->db->where('type',1);
                        $this->db->where('job_detail_id',$job_detail_id);
                        $this->db->delete('tbl_job_detail_child');
                        if (!empty($arrOther1)){
                            foreach ($arrOther1 as $key => $value){
                                $arrOther1[$key]['job_detail_id'] = $job_detail_id;
                            }

                            $this->db->insert_batch('tbl_job_detail_child',$arrOther1);
                        }

                        $this->db->where('type',2);
                        $this->db->where('job_detail_id',$job_detail_id);
                        $this->db->delete('tbl_job_detail_child');
                        if (!empty($arrOther2)){
                            foreach ($arrOther2 as $key => $value){
                                $arrOther2[$key]['job_detail_id'] = $job_detail_id;
                            }
                            $this->db->insert_batch('tbl_job_detail_child',$arrOther2);
                        }

                        $this->db->where('type',3);
                        $this->db->where('job_detail_id',$job_detail_id);
                        $this->db->delete('tbl_job_detail_child');
                        if (!empty($arrOther3)){
                            foreach ($arrOther3 as $key => $value){
                                $arrOther3[$key]['job_detail_id'] = $job_detail_id;
                            }
                            $this->db->insert_batch('tbl_job_detail_child',$arrOther3);
                        }

                        $this->db->where('type',4);
                        $this->db->where('job_detail_id',$job_detail_id);
                        $this->db->delete('tbl_job_detail_child');

                        if (!empty($arrOther4)){
                            foreach ($arrOther4 as $key => $value){
                                $arrOther4[$key]['job_detail_id'] = $job_detail_id;
                            }
                            $this->db->insert_batch('tbl_job_detail_child',$arrOther4);
                        }
                        $count ++;
                    }
                }
                echo json_encode(
                    [
                        'success' => true,
                        'errors' => $errors,
                        'alert_type' => 'success',
                        'message' => 'Thêm mới thành công ' . $count . ' mô tả công việc',
                    ]
                );
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
        $data['title'] = _l('Import mô tả công việc');
        $this->load->view('admin/job_detail/import', $data);
    }
}