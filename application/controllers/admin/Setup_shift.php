<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Setup_shift extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->perViewSetUpShift = has_permission('setup_shift','','view');
        $this->perAddSetUpShift = has_permission('setup_shift','','create');
        $this->perEditSetUpShift = has_permission('setup_shift','','edit');
        $this->perDeleteSetUpShift = has_permission('setup_shift','','delete');
    }

    public function index(){
        $data = [];
        if (!$this->perViewSetUpShift){
            access_denied();
        }
        $data['title'] = lang('dt_setup_shift');
        $this->load->view('admin/setup_shift/index',$data);
    }

    public function getSetupShifts(){
        $aColumns = [
            'tbl_setup_shift.id as id',
            'tbl_setup_shift.name as name',
            '"" as actions',
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_setup_shift';
        $where        = [
        ];
        $filter = [];

        $join = [
        ];


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_setup_shift.time_start',
            'tbl_setup_shift.time_end',
            'tbl_setup_shift.time_start_lunch_break',
            'tbl_setup_shift.time_end_lunch_break',
            'tbl_setup_shift.time_start_overtime',
            'tbl_setup_shift.time_rice',
        ], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow){
            $row = array();
            $setup_shift_id = $aRow['id'];
            $row[] = '<div class="text-center">'.(++$key).'</div>';
            $row[] = '<div>'.$aRow['name'].'</div>';
            $row[] = '<div class="text-center">'.(fomart_hour($aRow['time_start']).' - '.fomart_hour($aRow['time_end'])).'</div>';
            $row[] = '<div class="text-center">'.(fomart_hour($aRow['time_start_lunch_break']).' - '.fomart_hour($aRow['time_end_lunch_break'])).'</div>';
            $row[] = '<div class="text-center">'.fomart_hour($aRow['time_start_overtime']).'</div>';
            $row[] = '<div class="text-center">'.fomart_hour($aRow['time_rice']).'</div>';

            $edit = $this->perEditSetUpShift ? '<a  class="tnh-modal" data-tnh="modal" data-toggle="modal"
               data-target="#myModal" href="' . base_url('admin/setup_shift/detail/'.$setup_shift_id) . '"><i class="fa fa-edit"></i> ' . lang('edit')  . '</a>' : '';
            $delete = $this->perDeleteSetUpShift ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/setup_shift/delete_setup_shift/'.$setup_shift_id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>' : '';
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-left" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[] = $actions;
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function detail($id = 0){
        $data = [];
        if ($this->input->post()) {
            if (empty($id)) {
                $this->form_validation->set_rules('name', lang("dt_name_setup_shift"), 'required|is_unique[tbl_setup_shift.name]');
                if ($this->form_validation->run() == true) {
                    $name = $this->input->post('name');
                    $time_start = $this->input->post('time_start');
                    $color = $this->input->post('color');
                    $time_end = $this->input->post('time_end');
                    $time_start_lunch_break = $this->input->post('time_start_lunch_break');
                    $time_end_lunch_break = $this->input->post('time_end_lunch_break');
                    $time_start_overtime = $this->input->post('time_start_overtime');
                    $time_rice = $this->input->post('time_rice');
                    $number_rice = number_unformat($this->input->post('number_rice'));
                    $number_hour = number_unformat($this->input->post('number_hour'));
                    $total_date = number_unformat($this->input->post('total_date'));
                    $check_lunch_break = !empty($this->input->post('check_lunch_break')) ? $this->input->post('check_lunch_break') : 0;

                    $day = !empty($this->input->post('day')) ? $this->input->post('day') : [];
                    $day_overtime = !empty($this->input->post('day_overtime')) ? $this->input->post('day_overtime') : [];
                    $day_halftime = !empty($this->input->post('day_halftime')) ? $this->input->post('day_halftime') : [];

                    $arrDay = [];
                    if (!empty($day)){
                        foreach ($day as $kk => $vv){
                            $arrDay[] = [
                                'day' => $vv,
                                'type' => 1
                            ];
                        }
                    }

                    if (!empty($day_overtime)){
                        foreach ($day_overtime as $kk => $vv){
                            $arrDay[] = [
                                'day' => $vv,
                                'type' => 2
                            ];
                        }
                    }

                    if (!empty($day_halftime)){
                        foreach ($day_halftime as $kk => $vv){
                            $time_start_child = !empty($this->input->post('time_start_child')[$vv]) ? $this->input->post('time_start_child')[$vv] : null;
                            $time_end_child = !empty($this->input->post('time_end_child')[$vv]) ? $this->input->post('time_end_child')[$vv] : null;
                            $time_overtime_child = !empty($this->input->post('time_overtime_child')[$vv]) ? $this->input->post('time_overtime_child')[$vv] : null;
                            $number_hour_child = !empty($this->input->post('number_hour_child')[$vv]) ? number_unformat($this->input->post('number_hour_child')[$vv]) : null;
                            $arrDay[] = [
                                'day' => $vv,
                                'type' => 3,
                                'time_start' => $time_start_child,
                                'time_end' => $time_end_child,
                                'time_overtime' => $time_overtime_child,
                                'number_hour' => $number_hour_child,
                            ];
                        }
                    }

                    if (empty($arrDay)){
                        $data['result'] = 0;
                        $data['message'] = lang('Vui lòng chọn ngày làm việc');
                        echo json_encode($data);die();
                    }

                    if (strtotime($time_end) < strtotime($time_start)){
                        $data['result'] = 0;
                        $data['message'] = lang('Thời gian kết thúc không thể nhỏ hơn thời gian bắt đầu!');
                        echo json_encode($data);die();
                    }

                    if (strtotime($time_start_lunch_break) < strtotime($time_start) || strtotime($time_start_lunch_break) > strtotime($time_end)){
                        $data['result'] = 0;
                        $data['message'] = lang('Thời gian bắt đầu nghỉ trưa phải nằm trong khoảng thời gian bắt đầu và kết thúc!');
                        echo json_encode($data);die();
                    }

                    if (strtotime($time_end_lunch_break) < strtotime($time_start) || strtotime($time_end_lunch_break) > strtotime($time_end)){
                        $data['result'] = 0;
                        $data['message'] = lang('Thời gian kết thúc nghỉ trưa phải nằm trong khoảng thời gian bắt đầu và kết thúc!');
                        echo json_encode($data);die();
                    }

                    if (strtotime($time_end_lunch_break) < strtotime($time_start_lunch_break)){
                        $data['result'] = 0;
                        $data['message'] = lang('Thời gian kết thúc nghỉ trưa không thể nhỏ hơn thời gian bắt đầu nghỉ trưa!');
                        echo json_encode($data);die();
                    }

                    if (strtotime($time_start_overtime) < strtotime($time_end)){
                        $data['result'] = 0;
                        $data['message'] = lang('Thời gian bắt đầu tăng ca phải lớn hơn thời gian kết thúc!');
                        echo json_encode($data);die();
                    }


                    if (strtotime($time_rice) < strtotime($time_start) || strtotime($time_rice) > strtotime($time_end)){
                        $data['result'] = 0;
                        $data['message'] = lang('Thời gian được tính phần cơm phải nằm trong khoảng thời gian bắt đầu và kết thúc!');
                        echo json_encode($data);die();
                    }

                    $arrCheck = [];
                    foreach ($arrDay as $kk => $vv){
                        if (!empty($arrCheck[$vv['day']])){
                            $arrCheck[$vv['day']]['count'] += 1;
                        } else {
                            $arrCheck[$vv['day']]['count'] = 1;
                        }
                    }

                    foreach ($arrCheck as $kk => $vv){
                        if ($vv['count'] > 1){
                            $data['result'] = 0;
                            $data['message'] = lang('Ngày làm việc và ngày tăng ca không được trùng nhau!');
                            echo json_encode($data);die();
                        }
                    }

                    $options = [
                        'name' => $name,
                        'time_start' => $time_start,
                        'time_end' => $time_end,
                        'time_start_lunch_break' => $time_start_lunch_break,
                        'time_end_lunch_break' => $time_end_lunch_break,
                        'time_start_overtime' => $time_start_overtime,
                        'time_rice' => $time_rice,
                        'number_rice' => $number_rice,
                        'color' => $color,
                        'check_lunch_break' => $check_lunch_break,
                        'number_hour' => $number_hour,
                        'total_date' => $total_date,
                    ];

                    $this->db->insert('tbl_setup_shift',$options);
                    $id = $this->db->insert_id();
                    if ($id) {
                        if (!empty($arrDay)){
                            foreach ($arrDay as $kk => $vv){
                                $vv['shift_id'] = $id;
                                $this->db->insert('tbl_setup_shift_day',$vv);
                            }
                        }
                        activity_log_v2(
                            'setup_shift',
                            'tbl_setup_shift',
                            $id,
                            $name,
                            'Thêm mới thiết lập ca làm việc [' . $name . ']'
                        );
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
                echo json_encode($data);die();
            } else {
                $dtSetupShift = get_table_where('tbl_setup_shift',['id' => $id],'','row_array');
                if ($dtSetupShift['name'] != $this->input->post('name')) {
                    $this->form_validation->set_rules('name', lang("dt_name_setup_shift"), 'required|is_unique[tbl_setup_shift.name]');
                }
                $this->form_validation->set_rules('time_start', lang("dt_time_start_setup_shift"), 'required');
                if ($this->form_validation->run() == true) {
                    $name = $this->input->post('name');
                    $time_start = $this->input->post('time_start');
                    $color = $this->input->post('color');
                    $time_end = $this->input->post('time_end');
                    $time_start_lunch_break = $this->input->post('time_start_lunch_break');
                    $time_end_lunch_break = $this->input->post('time_end_lunch_break');
                    $time_start_overtime = $this->input->post('time_start_overtime');
                    $time_rice = $this->input->post('time_rice');
                    $number_rice = number_unformat($this->input->post('number_rice'));
                    $number_hour = number_unformat($this->input->post('number_hour'));
                    $total_date = number_unformat($this->input->post('total_date'));
                    $check_lunch_break = !empty($this->input->post('check_lunch_break')) ? $this->input->post('check_lunch_break') : 0;
                    $day = !empty($this->input->post('day')) ? $this->input->post('day') : [];
                    $day_overtime = !empty($this->input->post('day_overtime')) ? $this->input->post('day_overtime') : [];
                    $day_halftime = !empty($this->input->post('day_halftime')) ? $this->input->post('day_halftime') : [];

                    $arrDay = [];
                    if (!empty($day)){
                        foreach ($day as $kk => $vv){
                            $arrDay[] = [
                               'day' => $vv,
                                'type' => 1
                            ];
                        }
                    }

                    if (!empty($day_overtime)){
                        foreach ($day_overtime as $kk => $vv){
                            $arrDay[] = [
                                'day' => $vv,
                                'type' => 2
                            ];
                        }
                    }

                    if (!empty($day_halftime)){
                        foreach ($day_halftime as $kk => $vv){
                            $time_start_child = !empty($this->input->post('time_start_child')[$vv]) ? $this->input->post('time_start_child')[$vv] : null;
                            $time_end_child = !empty($this->input->post('time_end_child')[$vv]) ? $this->input->post('time_end_child')[$vv] : null;
                            $time_overtime_child = !empty($this->input->post('time_overtime_child')[$vv]) ? $this->input->post('time_overtime_child')[$vv] : null;
                            $number_hour_child = !empty($this->input->post('number_hour_child')[$vv]) ? number_unformat($this->input->post('number_hour_child')[$vv]) : null;
                            $arrDay[] = [
                                'day' => $vv,
                                'type' => 3,
                                'time_start' => $time_start_child,
                                'time_end' => $time_end_child,
                                'time_overtime' => $time_overtime_child,
                                'number_hour' => $number_hour_child,
                            ];
                        }
                    }

                    if (empty($arrDay)){
                        $data['result'] = 0;
                        $data['message'] = lang('Vui lòng chọn ngày làm việc');
                        echo json_encode($data);die();
                    }


                    if (strtotime($time_end) < strtotime($time_start)){
                        $data['result'] = 0;
                        $data['message'] = lang('Thời gian kết thúc không thể nhỏ hơn thời gian bắt đầu!');
                        echo json_encode($data);die();
                    }

                    if (strtotime($time_start_lunch_break) < strtotime($time_start) || strtotime($time_start_lunch_break) > strtotime($time_end)){
                        $data['result'] = 0;
                        $data['message'] = lang('Thời gian bắt đầu nghỉ trưa phải nằm trong khoảng thời gian bắt đầu và kết thúc!');
                        echo json_encode($data);die();
                    }

                    if (strtotime($time_end_lunch_break) < strtotime($time_start) || strtotime($time_end_lunch_break) > strtotime($time_end)){
                        $data['result'] = 0;
                        $data['message'] = lang('Thời gian kết thúc nghỉ trưa phải nằm trong khoảng thời gian bắt đầu và kết thúc!');
                        echo json_encode($data);die();
                    }

                    if (strtotime($time_end_lunch_break) < strtotime($time_start_lunch_break)){
                        $data['result'] = 0;
                        $data['message'] = lang('Thời gian kết thúc nghỉ trưa không thể nhỏ hơn thời gian bắt đầu nghỉ trưa!');
                        echo json_encode($data);die();
                    }

                    if (strtotime($time_start_overtime) < strtotime($time_end)){
                        $data['result'] = 0;
                        $data['message'] = lang('Thời gian bắt đầu tăng ca phải lớn hơn thời gian kết thúc!');
                        echo json_encode($data);die();
                    }


                    if (strtotime($time_rice) < strtotime($time_start) || strtotime($time_rice) > strtotime($time_end)){
                        $data['result'] = 0;
                        $data['message'] = lang('Thời gian được tính phần cơm phải nằm trong khoảng thời gian bắt đầu và kết thúc!');
                        echo json_encode($data);die();
                    }

                    $arrCheck = [];
                    foreach ($arrDay as $kk => $vv){
                        if (!empty($arrCheck[$vv['day']])){
                            $arrCheck[$vv['day']]['count'] += 1;
                        } else {
                            $arrCheck[$vv['day']]['count'] = 1;
                        }
                    }

                    foreach ($arrCheck as $kk => $vv){
                        if ($vv['count'] > 1){
                            $data['result'] = 0;
                            $data['message'] = lang('Ngày làm việc và ngày tăng ca và ngày làm việc nửa ngày không được trùng nhau!');
                            echo json_encode($data);die();
                        }
                    }


                    $options = [
                        'name' => $name,
                        'time_start' => $time_start,
                        'time_end' => $time_end,
                        'time_start_lunch_break' => $time_start_lunch_break,
                        'time_end_lunch_break' => $time_end_lunch_break,
                        'time_start_overtime' => $time_start_overtime,
                        'time_rice' => $time_rice,
                        'number_rice' => $number_rice,
                        'color' => $color,
                        'check_lunch_break' => $check_lunch_break,
                        'number_hour' => $number_hour,
                        'total_date' => $total_date,
                    ];

                    $this->db->where('id',$id);
                    $sucess = $this->db->update('tbl_setup_shift',$options);
                    if ($sucess) {
                        $this->db->where('shift_id',$id);
                        $this->db->delete('tbl_setup_shift_day');
                        if (!empty($arrDay)){
                            foreach ($arrDay as $kk => $vv){
                                $vv['shift_id'] = $id;
                                $this->db->insert('tbl_setup_shift_day',$vv);
                            }
                        }
                        activity_log_v2(
                            'setup_shift',
                            'tbl_setup_shift',
                            $id,
                            $dtSetupShift['name'],
                            'Sửa thiết lập ca làm việc [' . $dtSetupShift['name'] . ']'
                        );
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
                echo json_encode($data);die();
            }
        }

        if (empty($id)){
            if (!$this->perAddSetUpShift){
                accessDenied($js = true);
            }
            $data['title'] = lang('dt_add_setup_shift');
            $data['id'] = 0;
        } else {
            if (!$this->perEditSetUpShift){
                accessDenied($js = true);
            }
            $dtSetupShift = get_table_where('tbl_setup_shift',['id' => $id],'','row_array');
            $arrDate = [];
            $dtDay = get_table_where('tbl_setup_shift_day',['shift_id' => $id,'type' => 1]);
            if (!empty($dtDay)){
                foreach ($dtDay as $key => $value){
                    $arrDate[]=$value['day'];
                }
            }
            $arrDateHalftime = [];
            $arrDateHalftimeHour = [];
            $dtDay = get_table_where('tbl_setup_shift_day',['shift_id' => $id,'type' => 3]);
            if (!empty($dtDay)){
                foreach ($dtDay as $key => $value){
                    $arrDateHalftime[]=$value['day'];
                    $arrDateHalftimeHour[$value['day']]=[
                        'time_start' => $value['time_start'],
                        'time_end' => $value['time_end'],
                        'time_overtime' => $value['time_overtime'],
                        'number_hour' => $value['number_hour']
                    ];
                }
            }
            $arrDateOvertime = [];
            $dtDay = get_table_where('tbl_setup_shift_day',['shift_id' => $id,'type' => 2]);
            if (!empty($dtDay)){
                foreach ($dtDay as $key => $value){
                    $arrDateOvertime[]=$value['day'];
                }
            }
            $data['arrDate'] = $arrDate;
            $data['arrDateOvertime'] = $arrDateOvertime;
            $data['arrDateHalftime'] = $arrDateHalftime;
            $data['arrDateHalftimeHour'] = $arrDateHalftimeHour;
            $data['title'] = lang('dt_edit_setup_shift');
            $data['dtSetupShift'] = $dtSetupShift;
            $data['id'] = $id;
        }

        $this->load->view('admin/setup_shift/detail',$data);
    }

    public function delete_setup_shift($id){
        $data = [];
        if (!$this->perDeleteSetUpShift){
            $data['result'] = 0;
            $data['message'] = lang('Không có quyền xóa!');
            echo json_encode($data);die();
        }
        $this->db->from('tblstaff');
        $this->db->where('tblstaff.setup_shift_id',$id);
        $check_exists = $this->db->count_all_results();
        if ($check_exists){
            $data['result'] = 0;
            $data['message'] = lang('Ca làm việc đã sử dụng không thể xóa!');
            echo json_encode($data);die();
        }
        $dtSetupShift = get_table_where('tbl_setup_shift',['id' => $id],'','row_array');
        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_setup_shift');
        if ($success){
            activity_log_v2(
                'setup_shift',
                'tbl_setup_shift',
                $id,
                $dtSetupShift['name'],
                'Xóa thiết lập ca làm việc [' . $dtSetupShift['name'] . ']'
            );
            $data['result'] = 1;
            $data['message'] = lang('Xóa thành công!');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Xóa thất bại!');
        }
        echo json_encode($data);
    }
}