<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Carbon\Carbon;
class Suggest_overtime extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->perViewSuggestOvertime  = has_permission('suggest_overtime','','view');
        $this->perAddSuggestOvertime  = has_permission('suggest_overtime','','create');
        $this->perEditSuggestOvertime  = has_permission('suggest_overtime','','edit');
        $this->perApproveSuggestOvertime  = has_permission('suggest_overtime','','approve');
        $this->perDeleteSuggestOvertime  = has_permission('suggest_overtime','','delete');
    }

    public function index()
    {
        if (!$this->perViewSuggestOvertime){
            access_denied();
        }
        $data = [];
        $data['staff'] = getPersonDeparmentdt(0);
        $data['title'] = lang('Phiếu đề xuất tăng ca');
        $this->load->view('admin/suggest_overtime/index', $data);
    }

    public function getSuggestOvertime(){
        $name_search = $this->input->post('name_search');
        $staff_search = $this->input->post('staff_search');
        $month_search = $this->input->post('month_search');
        $year_search = $this->input->post('year_search');
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $tbDepartment = "(
            SELECT
                tblstaff_departments.staffid as staffid,
                GROUP_CONCAT(tbldepartments.name) as name_department
            FROM tbldepartments
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            GROUP BY tblstaff_departments.staffid
        ) tb_department";


        $aColumns = [
            'tbl_suggest_overtime.id as id',
            'tbl_suggest_overtime.name as name',
            'CONCAT(tb_staff.firstname," ",tb_staff.lastname) as name_staff',
            'tbl_suggest_overtime.month as month',
            'tbl_suggest_overtime.created_by as created_by',
            '1 as action '
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_overtime';
        $where = [
        ];
        $filter = [];
        $join = [
            'INNER JOIN tblstaff tb_staff ON tb_staff.staffid = tbl_suggest_overtime.staff_id',
            'INNER JOIN tblstaff ON tblstaff.staffid = tbl_suggest_overtime.created_by',
            'LEFT JOIN ' . $tbDepartment . ' ON tb_department.staffid = tb_staff.staffid',
        ];

        if (!empty($name_search)) {
            array_push($where,
                'AND ( tbl_suggest_overtime.name like "%' . $name_search . '%")');
        }
        if (!empty($staff_search)) {
            array_push($where,
                'AND ( tbl_suggest_overtime.staff_id IN (' . implode(',', $staff_search) . '))');
        }
        if (!empty($month_search)) {
            array_push($where,
                'AND ( tbl_suggest_overtime.month =  ' . $month_search . ')');
        }
        if (!empty($year_search)) {
            array_push($where,
                'AND ( tbl_suggest_overtime.year =  ' . $year_search . ')');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_suggest_overtime.date_created as date_created',
            'tbl_suggest_overtime.year as year',
            'tb_department.name_department as name_deparment',
        ], '', [], []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $stt = 1;
        foreach ($rResult as $key => $aRow) {
            $start++;

            $row = array();

            $row[] = '<div class="text-center"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" id="rows-child-'.$aRow['id'].'" class="rows-child fa fa-caret-right"></a></div>';
            $row[] = '<div>' . $aRow['name'] . '</div>';
            $info = '<div style="font-style: italic;font-size: 12px">
                <div>Bộ phận: ' . $aRow['name_deparment'] . '</div>
            </div>';
            $row[] = '<div><span style="font-weight: bold">' . $aRow['name_staff'] . '</span>' . $info . '</div>';
            $row[] = '<div class="text-center"><span>' . $aRow['month'] . '/' . $aRow['year'] . '</span></div>';

            $this->db->select('
                tbl_suggest_overtime_detail.id as id,
                tbl_suggest_overtime_detail.date as date,
                tbl_suggest_overtime_detail.hour_overtime as hour_overtime,
                tbl_suggest_overtime_detail.note as note,
                tbl_suggest_overtime_detail.status as status,
                tbl_suggest_overtime_detail.staff_status as staff_status,
                tbl_suggest_overtime_detail.date_status as date_status,
            ');
            $this->db->from('tbl_suggest_overtime_detail');
            $this->db->where('tbl_suggest_overtime_detail.suggest_overtime_id', $aRow['id']);
            $this->db->order_by('tbl_suggest_overtime_detail.date asc');
            $suggestOvertimeDetail = $this->db->get()->result_array();

            $htmlDate = '';
            $htmlDate .= '<table class="tnh-table table-bordered" style="width: 100%;">
                <thead><tr style="font-weight:bold; text-align: center"><td>Ngày</td><td>Số giờ</td></tr></thead>
                <tbody class="table_date">';
            foreach ($suggestOvertimeDetail as $kk => $vv) {
                $htmlDate .= '<tr>
                    <td style="width: 20%; padding: 5px !important;">' ._dhau($vv['date']). '</td>
                    <td style="text-align:center; width: 20%; padding: 5px !important;">' .($vv['hour_overtime']). '</td>
                    </tr>';

            }
            $htmlDate .= '</tbody></table>';
            $row[] = $htmlDate;
            $staff_created = staff_profile_image($aRow['created_by'], array('staff-profile-image-small mright5'),
                    'small', array(
                        'data-toggle' => 'tooltip',
                        'data-title' => ' Vào lúc: ' . _dt($aRow['date_created'])
                    )) . get_staff_full_name($aRow['created_by']) . '<br>';
            $row[] = '<div class="text-left">' . $staff_created . '<div style="font-style: italic; font-size: 12px">
                ' . _dt($aRow['date_created']) . '
            </div></div>';

            $actions = '<div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">' . _l('action') . '
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu h_right" style="width: 200px">';
            $actions .= $this->perEditSuggestOvertime ? '<li><a href="" onclick="edit(' . $aRow['id'] . ');return false;" class="text-danger"><i class="fa fa-edit"></i> ' . _l('Sửa phiếu đề xuất') . '</a></li>' : '';
            $actions .= $this->perAddSuggestOvertime ? '<li><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_overtime/add_date/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-edit"></i> ' . _l('Thêm ngày') . '</a></li>' : '';
            $actions .= '<li><a target="_blank" href="'.base_url().'admin/suggest_overtime/print_pdf/'.$aRow['id'].'"><i class="fa fa-file-pdf-o width-icon-actions"></i> ' . _l('In phiếu đề xuất') . '</a></li>';
            $actions .= $this->perDeleteSuggestOvertime ? '<li><a href="" onclick="deleteTicket(' . $aRow['id'] . ');return false;" class="text-danger delete-remind"><i class="fa fa-times"></i> ' . _l('Xóa phiếu đề xuất') . '</a></li>' : '';
            $actions .= '</ul></div>';
            $row[] = '<div class="text-center">' . $actions . '</div>';

            $trItems = '';

            foreach ($suggestOvertimeDetail as $k => $v) {

                $user_status = $v['staff_status'];
                if (!empty($v['date_status'])) {
                    $date_status = _d($v['date_status']);
                }
                $full_name = get_staff_full_name($user_status);
                $strApproveHtml = '';
                if (!empty($user_status)) {
                    $strApproveHtml = '<a class="mright5 mtop5" data-toggle="tooltip" data-title="' . $full_name . '" href="' . admin_url('profile/' . $user_status) . '">' . staff_profile_image(
                            $user_status,
                            ['staff-profile-image-small mbot5']
                        ) . '</a> <span>' . $full_name . '<br/><i style="font-size: 9px;">' . $date_status . '</i>';
                }

                $strApprove = '';
                if ($v['status'] == 0) {
                    $html = "<p><a id='agree_child' value='1' data-id='" . $v['id'] . "' class='btn btn-success btn-icon'>Duyệt</a><button class='btn po-close  btn-icon'>Thoát</button></p>";
                    $strApprove = '<div class="text-center mbot5"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="' . $html . '" class="label label-warning po" data-original-title="Duyệt">Chưa duyệt</span></div>';
                } elseif ($v['status'] == 1) {
                    $html = "<p><a id='agree_child' value='0' data-id='" . $v['id'] . "' class='btn btn-danger btn-icon'>Bỏ duyệt</a><button class='btn po-close  btn-icon'>Thoát</button></p>";
                    $strApprove = '<div class="text-center mbot5"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="' . $html . '" class="label label-success po" data-original-title="Duyệt">Đã duyệt</span></div>';
                }

                $trItems .= '<tr>
                        <td class="text-center">' . (++$k) . '</td>
                        <td class="text-left">' . _dhau($v['date']) . '</td>
                        <td class="text-center">' . (!empty($v['hour_overtime']) ? ($v['hour_overtime']) : '') . '</td>
                        <td class="text-left" style="width: 100px;">' . $v['note'] . '</td>
                        <td class="text-left" style="width: 100px;">' . $strApprove.$strApproveHtml . '</td>
                    </tr>';
            }
            $_data = '
                <div class="scrolling-stone pr-4 position-absolute h-100 w-100 max-height">
                    <div class="">
                         <div class="col-md-8">
                            <table class="table" style="margin-top: 0px;">
                                <thead>
                                    <tr>
                                        <th style="background: #d9edf7 !important; border: 1px solid #93b4d6 !important; color: #0e5dab !important;width: 30px;" class="text-center">STT</th>
                                        <th style="background: #d9edf7 !important; border: 1px solid #93b4d6 !important; color: #0e5dab !important;width: 100px;" class="text-center">' . lang('Ngày tăng ca') . '</th>
                                        <th style="background: #d9edf7 !important; border: 1px solid #93b4d6 !important; color: #0e5dab !important;width: 120px;" class="text-center">' . lang('Số giờ tăng ca') . '</th>
                                        <th style="background: #d9edf7 !important; border: 1px solid #93b4d6 !important; color: #0e5dab !important;width: 100px;" class="text-center" >' . lang('Ghi chú') . '</th>
                                        <th style="background: #d9edf7 !important; border: 1px solid #93b4d6 !important; color: #0e5dab !important;width: 100px;" class="text-center">' . lang('Trạng thái') . '</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ' . $trItems . '
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            ';
            $row[] = '<div class="text-left">' . $_data . '</div>';
            $output['aaData'][] = $row;
            $stt++;

        }
        echo json_encode($output);
    }

    public function add_suggest_overtime($id = ''){
        $data = [];
        if ($this->input->post()) {
            $dataPost = $this->input->post();
//            print_arrays($dataPost);
            $name = $dataPost['name'];
            $month = $dataPost['month'];
            $year = $dataPost['year'];
            $staff_id = $dataPost['staff_id'];
            $pm = $dataPost['pm'];
            $items = [];
            if ($id == '') {
                if (!$this->perAddSuggestOvertime){
                    $data['result'] = 0;
                    $data['message'] = 'Không có quyền tạo !';
                    echo json_encode($data);
                    die();
                }
                $option = [
                    'name' => $name,
                    'staff_id' => $staff_id,
                    'month' => $month,
                    'year' => $year,
                    'created_by' => get_staff_user_id(),
                    'date_created' => date('Y-m-d H:i:s'),
                ];

                if (!empty($pm)) {
                    foreach ($pm as $key => $value) {
                        $items[] = [
                            'date' => to_sql_date($value['date']),
                            'hour_overtime' => number_unformat($value['hour_overtime']),
                            'note' => $value['note'],
                        ];
                    }
                }

                if (empty($items)) {
                    $data['result'] = 0;
                    $data['message'] = 'Không có dữ liệu chi tiết';
                    echo json_encode($data);
                    die();
                }


                $this->db->insert('tbl_suggest_overtime', $option);
                $id_insert = $this->db->insert_id();
                if ($id_insert) {
                    foreach ($items as $key => $value) {
                        $value['suggest_overtime_id'] = $id_insert;
                        $this->db->insert('tbl_suggest_overtime_detail', $value);
                        $id_insert_detail = $this->db->insert_id();
                    }
                    $get_code = get_table_where('tbl_suggest_overtime', array('id' => $id_insert), '',
                        'row');
                    activity_log_v2('add_suggest_overtime', 'tbl_suggest_overtime', $id_insert,
                        $get_code->name,
                        'Thêm phiếu đề xuất tăng ca [' . $get_code->name . ']');
                    $data['result'] = 1;
                    $data['message'] = 'Thêm thành công';
                } else {
                    $data['result'] = 0;
                    $data['message'] = 'Thêm thất bại';
                }
            } else {
                if (!$this->perEditSuggestOvertime){
                    $data['result'] = 0;
                    $data['message'] = 'Không có quyền tạo !';
                    echo json_encode($data);
                    die();
                }
                $option = [
                    'month' => $month,
                    'year' => $year,
                    'name' => $name,
                    'staff_id' => $staff_id,
                ];

                if (!empty($pm)) {
                    foreach ($pm as $key => $value) {
                        $items[] = [
                            'date' => to_sql_date($value['date']),
                            'hour_overtime' => number_unformat($value['hour_overtime']),
                            'note' => $value['note'],
                            'id' => !empty($value['id']) ? $value['id'] : 0
                        ];
                    }
                }
                if (empty($items)) {
                    $data['result'] = 0;
                    $data['message'] = 'Không có dữ liệu chi tiết';
                    echo json_encode($data);
                    die();
                }
                $this->db->where('id', $id);
                $success = $this->db->update('tbl_suggest_overtime', $option);
                if ($success) {
                    $arrId = [];
                    foreach ($items as $key => $value) {
                        $checkExisit = get_table_where('tbl_suggest_overtime_detail',
                            ['id' => $value['id']], '', 'row_array');
                        if (!empty($checkExisit)) {
                            $arrId[] = $checkExisit['id'];
                            $this->db->where('id', $value['id']);
                            $this->db->update('tbl_suggest_overtime_detail', $value);
                        } else {
                            $value['suggest_overtime_id'] = $id;
                            $this->db->insert('tbl_suggest_overtime_detail', $value);
                            $insert_id_item = $this->db->insert_id();
                            $arrId[] = $insert_id_item;
                        }
                    }

                    if (empty($arrId)) {
                        $this->db->where('suggest_overtime_id', $id);
                        $this->db->delete('tbl_suggest_overtime_detail');
                    } else {
                        $this->db->where('suggest_overtime_id', $id);
                        $this->db->where_not_in('id', $arrId);
                        $this->db->delete('tbl_suggest_overtime_detail');
                    }

                    $get_code = get_table_where('tbl_suggest_overtime', array('id' => $id), '', 'row');
                    activity_log_v2('edit_suggest_overtime', 'tbl_suggest_overtime', $id,
                        $get_code->name,
                        'Sửa phiếu đề xuất tăng ca [' . $get_code->name . ']');
                    $data['result'] = 1;
                    $data['message'] = 'Sửa thành công';
                } else {
                    $data['result'] = 0;
                    $data['message'] = 'Sửa thất bại';
                }
            }

            echo json_encode($data);
            die();
        }

        
        $tbDepartment = "(
            SELECT
                tblstaff_departments.staffid as staffid,
                GROUP_CONCAT(tbldepartments.name) as name_department
            FROM tbldepartments
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            GROUP BY tblstaff_departments.staffid
        ) tb_department";
        $this->db->select('tblstaff.staffid as id,CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname,tb_department.name_department as name_department');
        $this->db->from('tblstaff');
        $this->db->join($tbDepartment, 'tb_department.staffid = tblstaff.staffid', 'left');
        $this->db->where('active', 1);
        $data['staffNew'] = $this->db->get()->result_array();
        if (!empty($id)) {
            $data['id'] = $id;
            $data['title'] = lang('Sửa phiếu đề xuất tăng ca');
            $data['suggestOvertime'] = get_table_where('tbl_suggest_overtime', ['id' => $id], '',
                'row_array');
            $data['suggestOvertimeDetail'] = get_table_where('tbl_suggest_overtime_detail',
                ['suggest_overtime_id' => $id],'date asc','result_array');
	
	
			$arrayOrWhereIn = [];
			if(!empty($data['suggestOvertime']['staff_id'])) {
				$arrayOrWhereIn[] = $data['suggestOvertime']['staff_id'];
			}
			$data['staff'] = getPersonDeparmentdt(0, $arrayOrWhereIn);

        } else {
			$data['staff'] = getPersonDeparmentdt(0);
            $data['id'] = '';
            $data['title'] = lang('Thêm phiếu đề xuất tăng ca');
            $startdate = date('Y-m-01');
            $newdate = date("Y-m-t");
            $month = date('m');
            $year = date('Y');
            $allDate = createDateRangeArray($month, $year);
            foreach ($allDate as $key => $value){
               $allDate[$key] = _dhau($value);
            }
            $data['allDate'] = $allDate;
        }
        $this->load->view('admin/suggest_overtime/add_suggest_overtime', $data);
    }

    public function checkExistsSuggestOvertime()
    {
        $data = [];
        $staff_id = $this->input->post('staff_id');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $id = $this->input->post('id');
        $data['result'] = 0;
        $data['id'] = 0;
        if ($staff_id && $month && $year) {
            $this->db->select('tbl_suggest_overtime.id as id');
            $this->db->from('tbl_suggest_overtime');
            $this->db->where('staff_id', $staff_id);
            $this->db->where('month', $month);
            $this->db->where('year', $year);
            if (!empty($id)) {
                $this->db->where('id !=', $id);
            }
            $result = $this->db->get()->row_array();
            if (!empty($result)) {
                $data['result'] = 1;
                $data['id'] = $result['id'];
            }
        }
        echo json_encode($data);
    }

    public function getDate()
    {
        $data = [];
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $data['result'] = 1;
        $data['startdate'] = date('' . $year . '-' . $month . '-01');
        $data['newdate'] =  date("Y-m-t", strtotime('' . $year . '-' . $month . '-01'));
        $allDate = createDateRangeArray($month,$year);
        foreach ($allDate as $key => $value){
            $allDate[$key] = _dhau($value);
        }
        $data['allDate'] = $allDate;
        echo json_encode($data);
    }

    public function checkEdit(){
        $id = $this->input->post('id');
        if(!empty($id)) {

            $this->db->where('suggest_overtime_id', $id);
            $this->db->where('status', 1);
            $paidHoliday = $this->db->get('tbl_suggest_overtime_detail')->row_array();

            if (!empty($paidHoliday)) {
                echo json_encode([
                    'result' => false,
                    'message' => _l('Có chi tiết đơn xin phép đã được duyệt không thể xóa')
                ]);
                die();
            } else {
                echo json_encode([
                    'result' => true,
                    'message' => ''
                ]);
                die();
            }
        }
    }

    public function update_status_child(){
        if (!$this->perApproveSuggestOvertime){
            echo json_encode([
                'success' => false,
                'message' => _l('Không có quyền duyệt')
            ]);die();
        }
        $id = $this->input->post('id');
        $status = $this->input->post('status');
        if(!empty($id)) {

            $this->db->where('id', $id);
            $paidHoliday = $this->db->get('tbl_suggest_overtime_detail')->row_array();
            if($paidHoliday['status'] == $status) {
                echo json_encode([
                    'success' => false,
                    'message' => _l('Phiếu đề xuất này đang ở trạng thái này không thể duyệt được nữa')
                ]);die();
            }

            if ($status == 0) {
                $checkTime = get_table_where('tbl_business_fee_boiler_overtime_detail', ['suggest_overtime_detail_id' => $id], '',
                    'row_array');
                if (!empty($checkTime)) {
                    echo json_encode([
                        'success' => false,
                        'message' => _l('Ngày đề xuất tăng ca này đã được áp dụng bên phiếu tăng ca không thể bỏ duyệt !')
                    ]);
                    die();
                }
            }

            $data_update = ['status' => $status];
            if(!empty($status)) {
                $data_update['staff_status'] = get_staff_user_id();
                $data_update['date_status'] = date('Y-m-d H:i:s');
            }
            else {
                $data_update['staff_status'] = NULL;
                $data_update['date_status'] = NULL;
                $data_update['status'] = 0;
            }
            $this->db->where('id', $id);
            $success = $this->db->update('tbl_suggest_overtime_detail', $data_update);
            if(!empty($success)) {
                $get_code = get_table_where('tbl_suggest_overtime', array('id' => $paidHoliday['suggest_overtime_id']), '', 'row');
                $this->db->from('tbl_suggest_overtime_detail');
                $this->db->where('tbl_suggest_overtime_detail.id',$id);
                $get_code_child = $this->db->get()->row_array();
                activity_log_v2('status_suggest_overtime_child', 'tbl_suggest_overtime_detail', $id, $get_code->name,
                    'Duyệt phiếu đề xuất tăng ca [' . $get_code->name . '] Ngày ['._dhau($get_code_child['date']).']');
                echo json_encode([
                    'result' => $success,
                    'id' => $paidHoliday['suggest_overtime_id'],
                    'message' => _l('cong_update_true')
                ]);die();
            }
        }
        echo json_encode([
            'result' => false,
            'message' => _l('cong_update_false')
        ]);die();
    }

    public function deleteTicket(){
        if (!$this->perDeleteSuggestOvertime){
            echo json_encode([
                'result' => false,
                'message' => _l('Không có quyền xóa')
            ]);die();
        }
        $id = $this->input->post('id');
        if(!empty($id)) {
            $this->db->where('suggest_overtime_id', $id);
            $this->db->where('status', 1);
            $suggestOvertime = $this->db->get('tbl_suggest_overtime_detail')->row_array();
            if (!empty($suggestOvertime)){
                echo json_encode([
                    'result' => false,
                    'message' => _l('Có chi tiết phiếu đề xuất đã được duyệt không thể xóa')
                ]);die();
            }
            $get_code = get_table_where('tbl_suggest_overtime', array('id' => $id), '', 'row');
            $this->db->where('id', $id);
            $success = $this->db->delete('tbl_suggest_overtime');

            $itemOld = get_table_where('tbl_suggest_overtime_detail',['suggest_overtime_id' => $id]);

            if(!empty($success)) {

                $this->db->where('suggest_overtime_id',$id);
                $this->db->delete('tbl_suggest_overtime_detail');

                activity_log_v2('delete_suggest_overtime', 'tbl_suggest_overtime', $id, $get_code->name,
                    'Xoá phiếu đề xuất tăng ca [' . $get_code->name . ']');
                echo json_encode([
                    'result' => $success,
                    'message' => _l('cong_update_true')
                ]);die();
            }
        }
        echo json_encode([
            'result' => false,
            'message' => _l('cong_update_false')
        ]);die();
    }

    public function print_pdf($id = '')
    {
        ob_start();
        $data = new stdClass();

        $tbDepartment = "(
            SELECT
                tblstaff_departments.staffid as staffid,
                GROUP_CONCAT(tbldepartments.name) as name_department
            FROM tbldepartments
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            GROUP BY tblstaff_departments.staffid
        ) tb_department";

        $this->db->select('
            tbl_suggest_overtime.id as id,
            tbl_suggest_overtime.name as name,
            tbl_suggest_overtime.date_created as date_created,
            CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name_staff,
            tb_department.name_department as name_deparment,
            tblroles.name as name_roles,
        ');
        $this->db->from('tbl_suggest_overtime');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_suggest_overtime.staff_id');
        $this->db->join($tbDepartment, 'tb_department.staffid = tblstaff.staffid', 'left');
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
        $this->db->where('tbl_suggest_overtime.id', $id);
        $businessFeeBoiler = $this->db->get()->row_array();

        $table = '';
        $data->content = '';
        $data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;text-transform: uppercase">' . _l('Phiếu đề xuất tăng ca') . '</span><br>';

        $day = date('d', strtotime($businessFeeBoiler['date_created']));
        $month = date('m', strtotime($businessFeeBoiler['date_created']));
        $year = date('Y', strtotime($businessFeeBoiler['date_created']));
        $date = _l('ch_day') . ' ' . $day . ' ' . _l('ch_month') . ' ' . $month . ' ' . _l('ch_year') . ' ' . $year;
        $data->content .= '<span style="text-align: center;font-style: italic;">' . $date . '</span><br>';


        $data->content .= '
            <span style="font-weight: bold;">' . _l('Nhân viên') . ': </span><span>' . $businessFeeBoiler['name_staff'] . '</span><br><br>
            <span style="font-weight: bold;">' . _l('Bộ phận') . ': </span><span>' . $businessFeeBoiler['name_deparment'] . '</span><br><br>';

        $trItems = '';
        $this->db->select('
                tbl_suggest_overtime_detail.id as id,
                tbl_suggest_overtime_detail.date as date,
                tbl_suggest_overtime_detail.hour_overtime as hour_overtime,
                tbl_suggest_overtime_detail.note as note,
            ');
        $this->db->from('tbl_suggest_overtime_detail');
        $this->db->where('tbl_suggest_overtime_detail.suggest_overtime_id', $id);
        $paidHolidayDetail = $this->db->get()->result_array();
        foreach ($paidHolidayDetail as $k => $v) {

            $trItems .= '<tr>
                        <td style="width: 10%;text-align: center" class="text-center">' . (++$k) . '</td>
                        <td style="width: 30%" class="text-left">' . _dhau($v['date']) . '</td>
                        <td style="width: 15%;text-align:center">' . ($v['hour_overtime']) . '</td>
                        <td style="width: 45%" class="text-left">' . ($v['note']) . '</td>
                    </tr>';
        }

        $data->content .= '<table class="table table-bordered" border="1" width="100%">
                <thead>
                    <tr>
                        <th style="text-align: center;width: 10%;font-weight: bold">STT</th>
                        <th style="text-align: center;width: 30%;font-weight: bold">Ngày tăng ca</th>
                        <th style="text-align: center;width: 15%;font-weight: bold">Số giờ tăng ca</th>
                        <th style="text-align: center;width: 45%;font-weight: bold">Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                    ' . $trItems . '
                </tbody>
              </table><br><br>';
        $date_2 = _l('ch_day') . ' ........ ' . _l('ch_month') . ' ........ ' . _l('ch_year') . ' ........';
        $data->content .= '<span style="text-align: right;font-style: italic;">' . $date_2 . '</span><br>';
        $table = '<table class="table table-bordered" width="100%">
                <thead>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">' . _l('Người lập') . '</span><br>
                            <span>' . _l('ch_signature') . '</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">' . _l('Trưởng bộ phận') . '</span><br>
                            <span>' . _l('ch_signature') . '</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">' . _l('Giám đốc') . '</span><br>
                            <span>' . _l('ch_signature') . '</span>
                        </td>
                    </tr>
                </tbody>
            </table>';
        $data->content .= $table;
        $pdf = print_pdf_P_ch($data);
        $type = 'I';
        $pdf->Output($businessFeeBoiler['name'] . '.pdf', $type);
    }

    public function add_date($id){
        $data = [];
        $dtSuggest = get_table_where('tbl_suggest_overtime',['id' => $id],'','row_array');
        $month = $dtSuggest['month'];
        $year = $dtSuggest['year'];
        $data['startdate'] = date('' . $year . '-' . $month . '-01');
        $data['newdate'] =  date("Y-m-t", strtotime('' . $year . '-' . $month . '-01'));
        $allDate = createDateRangeArray($month,$year);
        foreach ($allDate as $key => $value){
            $allDate[$key] = _dhau($value);
        }
        $data['allDate'] = $allDate;
        $data['dtSuggest'] = $dtSuggest;
        $data['id'] = $id;
        $this->load->view('admin/suggest_overtime/add_date',$data);
    }

    public function checkExistsDate(){
        $data = [];
        $date = $this->input->post('date');
        $id = $this->input->post('id');

        $this->db->from('tbl_suggest_overtime_detail');
        $this->db->where('suggest_overtime_id',$id);
        $this->db->where('date',to_sql_date($date));
        $checkExists = $this->db->get()->row_array();
        if (!empty($checkExists)){
            $data['result'] = 1;
            $data['message'] = lang('Đã tồn tại ngày này rồi');
        } else {
            $data['result'] = 0;
            $data['message'] = '';
        }
        echo json_encode($data);
    }

    public function add_date_post(){
        $data = [];
        $dataPost = $this->input->post();
        $items = [];
        if (!empty($dataPost['pm'])){
            foreach ($dataPost['pm'] as $key => $value){
                $items[] = [
                    'suggest_overtime_id' => $dataPost['id'],
                    'date' =>to_sql_date($value['date']),
                    'hour_overtime' => number_unformat($value['hour_overtime']),
                    'note' => $value['note'],
                ];
            }
        }
        if (empty($items)){
            $data['result'] = 0;
            $data['message'] = lang('Không tồn tại chi tiết');
            echo json_encode($data);die();
        }
        $success = false;
        foreach ($items as $key => $value){
            $success = $this->db->insert('tbl_suggest_overtime_detail',$value);
        }
        if ($success){
            $data['result'] = 1;
            $data['message'] = lang('Thêm thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Thêm thất bại');
        }
        echo json_encode($data);die();
    }
}