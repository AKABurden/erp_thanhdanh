<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Qr_staff extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getListStaff()
    {
        $data = [];
        $name_search = $this->input->post('name_search');
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                    if (!empty($data_post['name_search'])) {
                        $name_search = $data_post['name_search'];
                    }
                }
            }
        }
        $this->db->select(
            'tblstaff.staffid as staffid,
             tblstaff.code as code'
        );
        $this->db->from('tblstaff');
        $this->db->where('status_work !=', 2);
        if (!empty($name_search)) {
            $this->db->group_start();
            $this->db->like('firstname', $name_search);
            $this->db->or_like('lastname', $name_search);
            $this->db->or_like('code', $name_search);
            $this->db->or_like('concat(firstname," ",lastname)', $name_search);
            $this->db->group_end();
        }
        $dtStaff = $this->db->get()->result_array();
        foreach ($dtStaff as $key => $value){
            $staff_name = get_staff_full_name($value['staffid']);
            $staff_image = staff_profile_image_ch($value['staffid']);
            $dtStaff[$key]['staff_name'] = $staff_name;
            $dtStaff[$key]['staff_image'] = $staff_image;
        }
        $data['dtStaff'] = $dtStaff;
        echo json_encode($data);
    }

    public function CheckIn()
    {
        $data['success'] = false;
        $data['type_check'] = -1;
        $data['id_check_in'] = -1;
        $data['message'] = '';
        if ($this->input->post()) {
            $staff_id = $this->input->post('staff_id');
            $date_check = $this->input->post('date_check');


            if (!empty($staff_id) && !empty($date_check)) {
                $day = date("d", strtotime($date_check));
                $hour = date("H:i", strtotime($date_check));
                $month = date("m", strtotime($date_check));
                $year = date("Y", strtotime($date_check));
                $day_check_old = '';
                $month_check_old = '';
                $zero = 0;

                $staff_v1 = get_table_where('tblstaff', ['staffid' => $staff_id], '', 'row_array');
                if ($staff_v1['status_work'] == 2) {
                    $data['success'] = false;
                    $data['type_check_hour'] = -1;
                    $data['staff_name'] = $staff_v1['firstname'] . ' ' . $staff_v1['lastname'];
                    $data['id'] = -1;
                    $data['time'] = $hour;
                    $data['date'] = $day;
                    $data['message'] = 'Nhân viên đã nghỉ việc';
                    echo json_encode($data);
                    die();
                }


                $this->db->select('tbl_timekeeping_detail.*,tbl_timekeeping_detail_hour.hour,tbl_timekeeping_detail_hour.type as type_hour,tbl_timekeeping_detail_hour.id as id_imekeeping_detail_hour,tbl_timekeeping_detail_hour.type_check as type_check,tbl_timekeeping.month as month,tbl_timekeeping.year');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping_detail_hour',
                    'tbl_timekeeping_detail_hour.timekeeping_detail_id =tbl_timekeeping_detail.id', 'left');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id =tbl_timekeeping_detail.timekeeping_id', 'left');
                $this->db->where('tbl_timekeeping_detail.staff_id', $staff_id);
                $this->db->where('tbl_timekeeping_detail.day', $day);
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail_hour.type', 1);
                $check_staff = $this->db->get()->row_array();
                if (!empty($check_staff)) {
                    $day_check_old = $check_staff['day'] - 1;
                    $month_check_old = $check_staff['month'] - 1;

                    if (!empty($month_check_old)) {
                        if ($month_check_old < 10) {
                            $month_check_old = $zero . $month_check_old;
                        }
                    }
                    if ($check_staff['hour'] == null) {

                        $this->db->select('tbl_timekeeping_detail.*,tbl_timekeeping_detail_hour.hour,tbl_timekeeping_detail_hour.type as type_hour,tbl_timekeeping_detail_hour.id as id_imekeeping_detail_hour,tbl_timekeeping_detail_hour.type_check as type_check,tbl_timekeeping.month as month,tbl_timekeeping.year');
                        $this->db->from('tbl_timekeeping_detail');
                        $this->db->join('tbl_timekeeping_detail_hour',
                            'tbl_timekeeping_detail_hour.timekeeping_detail_id =tbl_timekeeping_detail.id', 'left');
                        $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id =tbl_timekeeping_detail.timekeeping_id',
                            'left');
                        $this->db->where('tbl_timekeeping_detail.staff_id', $staff_id);
                        $this->db->where('tbl_timekeeping_detail.day', $day_check_old);
                        $this->db->where('tbl_timekeeping.month', $month);
                        $this->db->where('tbl_timekeeping.year', $year);
                        $this->db->where('tbl_timekeeping_detail_hour.type', 1);
                        $check_staff_old_v2 = $this->db->get()->row_array();
                        if (!empty($check_staff_old_v2['hour'] != null)) {
                            $data['success'] = true;
                            $data['type_check'] = $check_staff_old_v2['type_check'];
                            $data['id_check_in'] = $check_staff_old_v2['id_imekeeping_detail_hour'];
                        } else {
                            $check_date = getAllDateInMonth($month_check_old, $year, 'd/m');
                            $date_month_last = array_pop($check_date);
                            $date_month_last = explode('/', $date_month_last);
                            $date_last = $date_month_last[0];
                            $this->db->select('tbl_timekeeping_detail.*,tbl_timekeeping_detail_hour.hour,tbl_timekeeping_detail_hour.type as type_hour,tbl_timekeeping_detail_hour.id as id_imekeeping_detail_hour,tbl_timekeeping_detail_hour.type_check as type_check,tbl_timekeeping.month as month,tbl_timekeeping.year');
                            $this->db->from('tbl_timekeeping_detail');
                            $this->db->join('tbl_timekeeping_detail_hour',
                                'tbl_timekeeping_detail_hour.timekeeping_detail_id =tbl_timekeeping_detail.id', 'left');
                            $this->db->join('tbl_timekeeping',
                                'tbl_timekeeping.id =tbl_timekeeping_detail.timekeeping_id', 'left');
                            $this->db->where('tbl_timekeeping_detail.staff_id', $staff_id);
                            $this->db->where('tbl_timekeeping_detail.day', $date_last);
                            $this->db->where('tbl_timekeeping.month', $month_check_old);
                            $this->db->where('tbl_timekeeping.year', $year);
                            $this->db->where('tbl_timekeeping_detail_hour.type', 1);
                            $check_staff_old = $this->db->get()->row_array();
                            if ($check_staff_old['hour'] != null) {
                                $data['success'] = true;
                                $data['type_check'] = $check_staff_old['type_check'];
                                $data['id_check_in'] = $check_staff_old['id_imekeeping_detail_hour'];
                            }
                        }
                    } else {
                        $data['success'] = true;
                        $data['type_check'] = $check_staff['type_check'];
                        $data['id_check_in'] = $check_staff['id_imekeeping_detail_hour'];
                    }
                }
            }

        }
        echo json_encode($data);
    }

    public function QRStaffOld()
    {
        $data['success'] = false;
        $data['type_check_hour'] = -1;
        $data['message'] = '';
        if ($this->input->post()) {
            $id_timekeeping_detail_hour = $this->input->post('id_timekeeping_detail_hour');
            $staff_id = $this->input->post('staff_id');
            $date_check = $this->input->post('date_check');
            $type_hour = $this->input->post('type_hour');
            $type_check_in = $this->input->post('type_check_in');
            $type_check = $this->input->post('type_check');
            if (empty($type_check)) {
                $type_check = 1;
            }
            if (!empty($type_hour) && !empty($staff_id) && !empty($date_check)) {
                $day = date("d", strtotime($date_check));
                $hour = date("H:i", strtotime($date_check));
                $month = date("m", strtotime($date_check));
                $year = date("Y", strtotime($date_check));
                $zero = 0;
                $month_check_old = '';

                $staff_v1 = get_table_where('tblstaff', ['staffid' => $staff_id], '', 'row_array');
                if ($staff_v1['status_work'] == 2) {
                    $data['success'] = false;
                    $data['type_check_hour'] = -1;
                    $data['staff_name'] = $staff_v1['firstname'] . ' ' . $staff_v1['lastname'];
                    $data['id'] = -1;
                    $data['time'] = $hour;
                    $data['date'] = $day;
                    $data['message'] = 'Nhân viên đã nghỉ việc';
                    echo json_encode($data);
                    die();
                }

                $this->db->select('tbl_timekeeping_detail.*,tbl_timekeeping_detail_hour.hour,tbl_timekeeping_detail_hour.type as type_hour,tbl_timekeeping_detail_hour.id as id_imekeeping_detail_hour,tbl_timekeeping_detail_hour.type_check as type_check,tbl_timekeeping.month as month,tbl_timekeeping.year');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping_detail_hour',
                    'tbl_timekeeping_detail_hour.timekeeping_detail_id =tbl_timekeeping_detail.id', 'left');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id =tbl_timekeeping_detail.timekeeping_id', 'left');
                $this->db->where('tbl_timekeeping_detail.staff_id', $staff_id);
                $this->db->where('tbl_timekeeping_detail.day', $day);
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail_hour.type', $type_hour);
                if ($type_hour == 1) {
                    $this->db->order_by('tbl_timekeeping_detail_hour.id asc');
                } else {
                    $this->db->order_by('tbl_timekeeping_detail_hour.id desc');
                }


                $check_staff = $this->db->get()->row_array();

                if (!empty($check_staff)) {
                    if ($check_staff['type_hour'] == 1) {
                        if ($check_staff['hour'] != null) {
                            $staff = get_table_where('tblstaff', ['staffid' => $check_staff['staff_id']], '',
                                'row_array');
                            $data['success'] = false;
                            $data['type_check_hour'] = -1;
                            $data['staff_name'] = $staff['firstname'] . ' ' . $staff['lastname'];
                            $data['id'] = $check_staff['id_imekeeping_detail_hour'];
                            $data['time'] = $check_staff['hour'];
                            $data['date'] = $check_staff['date'];
                            $data['message'] = 'Đã check in rồi';
                        } else {

//                            if (strtotime($hour) > strtotime('09:00') && strtotime($hour) < strtotime('13:00')){
//                                $staff = get_table_where('tblstaff', ['staffid' => $check_staff['staff_id']],
//                                    '',
//                                    'row_array');
//                                $data['success'] = false;
//                                $data['type_check_hour'] = -1;
//                                $data['staff_name'] = $staff['firstname'].' '.$staff['lastname'];
//                                $data['id'] = -1;
//                                $data['time'] = $hour;
//                                $data['date'] = $day;
//                                $data['message'] = 'Vui lòng check in trong khoảng thời gian quy định !';
//                                echo json_encode($data);
//                                die();
//                            }
//
//                            if (strtotime($hour) > strtotime('14:00')){
//                                $staff = get_table_where('tblstaff', ['staffid' => $check_staff['staff_id']],
//                                    '',
//                                    'row_array');
//                                $data['success'] = false;
//                                $data['type_check_hour'] = -1;
//                                $data['staff_name'] = $staff['firstname'].' '.$staff['lastname'];
//                                $data['id'] = -1;
//                                $data['time'] = $hour;
//                                $data['date'] = $day;
//                                $data['message'] = 'Vui lòng check in trong khoảng thời gian quy định !';
//                                echo json_encode($data);
//                                die();
//                            }

                            $this->load->library('upload');
                            $file_name = '';
                            if (!empty($_FILES['image']) && !empty($_FILES['image']['name'])) {
                                $_FILES['file']['name'] = time() . '_' . $_FILES['image']['name'];
                                $_FILES['file']['type'] = $_FILES['image']['type'];
                                $_FILES['file']['tmp_name'] = $_FILES['image']['tmp_name'];
                                $_FILES['file']['error'] = $_FILES['image']['error'];
                                $_FILES['file']['size'] = $_FILES['image']['size'];

                                $config['upload_path'] = './uploads/timekeeping_staffs/';
                                $config['allowed_types'] = '*';
                                $this->upload->initialize($config);
                                if ($this->upload->do_upload('file')) {
                                    $file_name = $this->upload->file_name;
                                }
                            }

                            $count_late = 0;
                            $hour_real = $hour;
                            $type_detail = 'X';
                            $staff = get_table_where('tblstaff', ['staffid' => $staff_id], '', 'row_array');
                            if (strtotime($hour) > strtotime('08:00')) {
                                $count_late = 1;
                            }
                            if (strtotime($hour) < strtotime('08:00')) {
                                $hour_real = '08:00';
                            } else {
                                if (strtotime($hour) >= strtotime('12:00') && strtotime($hour) <= strtotime('13:00')) {
                                    $hour_real = '13:00';
                                } else {
                                    $hour_real = $hour;
                                    $checkHour = explode(':', $hour);
                                    if ($checkHour[1] > '20' && $checkHour[1] <= '30') {
                                        $hour_real = $checkHour[0] . ':30';
                                    } elseif ($checkHour[1] > '50' && $checkHour[1] <= '59') {
                                        $hour_real = ($checkHour[0] + 1) . ':00';
                                    } elseif ($checkHour[1] > '00' && $checkHour[1] <= '20') {
                                        $hour_real = $checkHour[0] . ':00';
                                    } elseif ($checkHour[1] >= '31' && $checkHour[1] <= '50') {
                                        $hour_real = $checkHour[0] . ':30';
                                    }
                                }
                            }
                            $hour_late = 0;
                            $hour_late_checkin = 0;
                            $hour_real_new = explode(':', $hour_real);
                            if ($hour_real_new['0'] < 10) {
                                if (strlen($hour_real_new[0]) == 1) {
                                    $hour_real_check = '0' . $hour_real_new[0] . ':' . $hour_real_new[1];
                                } else {
                                    $hour_real_check = $hour_real;
                                }
                            } else {
                                $hour_real_check = $hour_real;
                            }
                            if (strtotime($hour_real_check) <= strtotime('10:00')) {
                                $hour_late = countHourCheckOut('08:00', $hour_real);
                                $hour_late_checkin = $hour_late;
                            } elseif (strtotime($hour_real_check) >= strtotime('13:00')) {
                                $hour_late_new = countHourCheckOut('08:00', $hour_real);
                                $hour_late = countHourCheckOut('01:00', $hour_late_new);
                            }


                            $timekeeping_detail_hour_check_out = get_table_where('tbl_timekeeping_detail_hour',
                                ['timekeeping_detail_id' => $check_staff['id'], 'type' => 2], '', 'row_array');

                            $this->db->where('tbl_timekeeping_detail_hour.id',
                                $check_staff['id_imekeeping_detail_hour']);
                            $this->db->update('tbl_timekeeping_detail_hour', [
                                'hour' => $hour,
                                'hour_real' => $hour_real,
                                'image' => $file_name,
                                'type_check' => $type_check,
                                'type_check_in' => $type_check_in
                            ]);

                            $this->db->where('tbl_timekeeping_detail_hour.id',
                                $timekeeping_detail_hour_check_out['id']);
                            $this->db->update('tbl_timekeeping_detail_hour', ['type_check' => $type_check]);
                            $id_insert_check = $check_staff['id_imekeeping_detail_hour'];


                            $this->db->where('tbl_timekeeping_detail.id', $check_staff['id']);
                            $this->db->update('tbl_timekeeping_detail',
                                [
                                    'count_late' => $count_late,
//                                    'type' => $type_detail,
                                    'paid_holiday_id' => 0,
                                    'paid_holiday_detail_id' => 0,
                                    'count_hour_late' => countHourCheckOutNew($hour_late),
                                    'count_hour_late_new' => countHourCheckOutNew($hour_late_checkin)
                                ]);

                            $staff = get_table_where('tblstaff', ['staffid' => $check_staff['staff_id']], '',
                                'row_array');
                            $data['success'] = true;
                            $data['type_check_hour'] = 1;
                            $data['staff_name'] = $staff['firstname'] . ' ' . $staff['lastname'];
                            $data['id'] = $id_insert_check;
                            $data['time'] = $date_check;
                            $data['type_check'] = $type_check;
                            $data['message'] = 'Check in thành công';
                        }
                    } elseif ($check_staff['type_hour'] == 2) {
                        $count_late_new = 0;
                        //check check out
                        $day_check_old = '';
                        $timekeeping_detail_hour_in_check_old = get_table_where('tbl_timekeeping_detail_hour',
                            ['id' => $id_timekeeping_detail_hour], '', 'row_array');
                        if (!empty($timekeeping_detail_hour_in_check_old)) {
                            $timekeeping_detail_check_day_old = get_table_where('tbl_timekeeping_detail',
                                ['id' => $timekeeping_detail_hour_in_check_old['timekeeping_detail_id']], '',
                                'row_array');
                            if (!empty($timekeeping_detail_check_day_old)) {
                                $day_check_old = $timekeeping_detail_check_day_old['day'];
                            }
                        }
                        if (empty($day_check_old)) {
                            $staff = get_table_where('tblstaff', ['staffid' => $staff_id], '', 'row_array');
                            $data['success'] = false;
                            $data['type_check_hour'] = -1;
                            $data['staff_name'] = $staff['firstname'] . ' ' . $staff['lastname'];
                            $data['id'] = null;
                            $data['time'] = null;
                            $data['date'] = null;
                            $data['message'] = 'Check out lỗi';
                            echo json_encode($data);
                            die();
                        }
                        if ($day - $day_check_old > 0) {
                            $staff = get_table_where('tblstaff', ['staffid' => $staff_id], '', 'row_array');
                            $data['success'] = false;
                            $data['type_check_hour'] = -1;
                            $data['staff_name'] = $staff['firstname'] . ' ' . $staff['lastname'];
                            $data['id'] = null;
                            $data['time'] = null;
                            $data['date'] = null;
                            $data['message'] = 'Vui lòng checkout trong cùng ngày';
                            echo json_encode($data);
                            die();
                        }
                        if (strtotime($hour) > strtotime('23:59')) {
                            $staff = get_table_where('tblstaff', ['staffid' => $staff_id], '', 'row_array');
                            $data['success'] = false;
                            $data['type_check_hour'] = -1;
                            $data['staff_name'] = $staff['firstname'] . ' ' . $staff['lastname'];
                            $data['id'] = null;
                            $data['time'] = null;
                            $data['date'] = null;
                            $data['message'] = 'Vui lòng check out trước 24h';
                            echo json_encode($data);
                            die();
                        }
                        if ($check_staff['hour'] != null) {
                            $staff = get_table_where('tblstaff', ['staffid' => $check_staff['staff_id']], '',
                                'row_array');
                            $data['success'] = false;
                            $data['type_check_hour'] = -1;
                            $data['staff_name'] = $staff['firstname'] . ' ' . $staff['lastname'];
                            $data['id'] = $check_staff['id_imekeeping_detail_hour'];
                            $data['time'] = $check_staff['hour'];
                            $data['date'] = $check_staff['date'];
                            $data['message'] = 'Đã check out rồi';
                        } else {
                            $this->load->library('upload');
                            $file_name = '';
                            if (!empty($_FILES['image']) && !empty($_FILES['image']['name'])) {
                                $_FILES['file']['name'] = time() . '_' . $_FILES['image']['name'];
                                $_FILES['file']['type'] = $_FILES['image']['type'];
                                $_FILES['file']['tmp_name'] = $_FILES['image']['tmp_name'];
                                $_FILES['file']['error'] = $_FILES['image']['error'];
                                $_FILES['file']['size'] = $_FILES['image']['size'];

                                $config['upload_path'] = './uploads/timekeeping_staffs/';
                                $config['allowed_types'] = '*';
                                $this->upload->initialize($config);
                                if ($this->upload->do_upload('file')) {
                                    $file_name = $this->upload->file_name;
                                }
                            }

                            //check check out
                            $day_check_old = '';
                            $month_check_old = '';
                            $year_check_old = '';
                            $timekeeping_detail_hour_in_check_old = get_table_where('tbl_timekeeping_detail_hour',
                                ['id' => $id_timekeeping_detail_hour], '', 'row_array');
                            if (!empty($timekeeping_detail_hour_in_check_old)) {
                                $timekeeping_detail_check_day_old = get_table_where('tbl_timekeeping_detail',
                                    ['id' => $timekeeping_detail_hour_in_check_old['timekeeping_detail_id']], '',
                                    'row_array');
                                if (!empty($timekeeping_detail_check_day_old)) {
                                    $day_check_old = $timekeeping_detail_check_day_old['day'];
                                    $timekeeping = get_table_where('tbl_timekeeping',
                                        ['id' => $timekeeping_detail_check_day_old['timekeeping_id']], '', 'row_array');
                                    $month_check_old = $timekeeping['month'];
                                    $year_check_old = $timekeeping['year'];
                                }
                            }

                            $timekeeping_detail_hour_in_check = get_table_where('tbl_timekeeping_detail_hour', [
                                'timekeeping_detail_id' => $check_staff['id'],
                                'type_check' => $check_staff['type_check'],
                                'type' => 1
                            ], '', 'row_array');

                            $hour_check_in = '';
                            $hour_lunch_break = false;
                            $day_check = '';
                            $file_name_old = '';
                            if (!empty($timekeeping_detail_hour_in_check)) {
                                $hour_check_in = $timekeeping_detail_hour_in_check['hour'];
                                $timekeeping_detail_check_day = get_table_where('tbl_timekeeping_detail',
                                    ['id' => $timekeeping_detail_hour_in_check['timekeeping_detail_id']], '',
                                    'row_array');
                                if (!empty($timekeeping_detail_check_day)) {
                                    $day_check = $timekeeping_detail_check_day['day'];
                                }
                                $file_name_old = $timekeeping_detail_hour_in_check['image'];
                            }
                            if (strtotime($hour_check_in) <= strtotime('12:00')) {
                                $hour_lunch_break = true;
                            }
                            if (!empty($timekeeping_detail_hour_in_check)) {
                                if ($timekeeping_detail_hour_in_check['hour'] == null) {
                                    $staff = get_table_where('tblstaff', ['staffid' => $check_staff['staff_id']],
                                        '',
                                        'row_array');
                                    $data['success'] = false;
                                    $data['type_check_hour'] = -1;
                                    $data['staff_name'] = $staff['firstname'] . ' ' . $staff['lastname'];
                                    $data['id'] = -1;
                                    $data['time'] = $hour;
                                    $data['date'] = $day;
                                    $data['message'] = 'Nhân viên chưa check in không thể check out';
                                    echo json_encode($data);
                                    die();
                                }
                            }
                            if (!empty($timekeeping_detail_hour_in_check)) {
                                if (strtotime($hour) < strtotime($timekeeping_detail_hour_in_check['hour'])) {
                                    $staff = get_table_where('tblstaff', ['staffid' => $check_staff['staff_id']],
                                        '',
                                        'row_array');
                                    $data['success'] = false;
                                    $data['type_check_hour'] = -1;
                                    $data['staff_name'] = $staff['firstname'] . ' ' . $staff['lastname'];
                                    $data['id'] = -1;
                                    $data['time'] = $hour;
                                    $data['date'] = $day;
                                    $data['message'] = 'Giờ ra không thể nhỏ hơn giờ vào';
                                    echo json_encode($data);
                                    die();
                                }
                            }


                            if (strtotime($hour) <= strtotime('17:00')) {
                                if (strtotime($hour) < strtotime('17:00')) {
                                    $count_late_new = 1;
                                }
                                $hour_real = $hour;
                                $checkHour = explode(':', $hour);
                                if ($checkHour[1] > '20' && $checkHour[1] <= '30') {
                                    $hour_real = $checkHour[0] . ':30';
                                } elseif ($checkHour[1] > '50' && $checkHour[1] <= '59') {
                                    $hour_real = ($checkHour[0] + 1) . ':00';
                                } elseif ($checkHour[1] > '00' && $checkHour[1] <= '20') {
                                    $hour_real = $checkHour[0] . ':00';
                                } elseif ($checkHour[1] >= '31' && $checkHour[1] <= '50') {
                                    $hour_real = $checkHour[0] . ':30';
                                }
                                if (strtotime($hour) > strtotime('12:00') && strtotime($hour) < strtotime('13:00')) {
                                    $hour_real = '12:00';
                                }

                                $hour_real_new_check = explode(':', $hour_real);
                                if ($hour_real_new_check['0'] < 10) {
                                    if (strlen($hour_real_new_check[0]) == 1) {
                                        $hour_real_check = '0' . $hour_real_new_check[0] . ':' . $hour_real_new_check[1];
                                    } else {
                                        $hour_real_check = $hour_real;
                                    }
                                } else {
                                    $hour_real_check = $hour_real;
                                }
                                if (strtotime($hour_real_check) >= strtotime('15:00')) {
                                    $hour_late_new = countHourCheckOut($hour_real, '17:00');
                                }

                                $timekeeping_detail_hour_in = get_table_where('tbl_timekeeping_detail_hour', [
                                    'timekeeping_detail_id' => $check_staff['id'],
                                    'type' => 1,
                                    'type_check' => $type_check
                                ], '', 'row_array');

                                if ($check_staff['check_sun'] == 1) {
                                    $dtPersonnel = get_table_where('tblstaff',
                                        ['staffid' => $check_staff['staff_id']], '', 'row_array');
                                    if ($dtPersonnel['status_overtime'] == 1) {
                                        $this->db->select('tbl_suggest_overtime_detail.*');
                                        $this->db->from('tbl_suggest_overtime_detail');
                                        $this->db->join('tbl_suggest_overtime',
                                            'tbl_suggest_overtime.id = tbl_suggest_overtime_detail.suggest_overtime_id');
                                        $this->db->where('staff_id', $check_staff['staff_id']);
                                        $this->db->where('tbl_suggest_overtime_detail.date', $check_staff['date']);
                                        // $this->db->where('tbl_suggest_overtime_detail.status', 1);
                                        $checkSuggestOvertime = $this->db->get()->row_array();
                                        if (!empty($checkSuggestOvertime)) {
                                            $hour_overtime = $checkSuggestOvertime['hour_overtime'];
                                            $startTimeCheck = $timekeeping_detail_hour_in['hour_real'];
                                            $endTimeCheck = $hour_real;
                                            $hour_check_new = $hour;
                                            $new_time_check = countHourCheckOut($startTimeCheck, $endTimeCheck);
                                            $checkLunch = false;
                                            if (strtotime($hour_check_new) >= strtotime('13:00')) {
                                                if ($hour_lunch_break) {
                                                    $new_time_check = countHourCheckOut('01:00', $new_time_check);
                                                    $checkLunch = true;
                                                }
                                            }
                                            if (countHourCheckOutNew($new_time_check) > $hour_overtime) {
                                                $startTimeNew = $check_staff['date'] . ' ' . $startTimeCheck;
                                                $hour_overtime_new = explode('.', $hour_overtime);
                                                if (!empty($hour_overtime_new[1])) {
                                                    $minutes = '0.' . $hour_overtime_new[1];
                                                } else {
                                                    $minutes = 0;
                                                }
                                                $cenvertedTime = date('Y-m-d H:i',
                                                    strtotime('+' . $hour_overtime_new[0] . ' hour +' . ($minutes * 60) . ' minutes',
                                                        strtotime($startTimeNew)));
                                                if ($checkLunch) {
                                                    $cenvertedTime = date('Y-m-d H:i',
                                                        strtotime('+1 hour', strtotime($cenvertedTime)));
                                                }
                                                $hour_real = date('H:i', strtotime($cenvertedTime));
                                                $hour = $hour_real;
                                            }
                                        } else {
                                            $hour_real = $timekeeping_detail_hour_in['hour_real'];
                                        }
                                    }
                                }


                                $hour_real_new = $hour_real;

                                $this->db->where('tbl_timekeeping_detail_hour.id',
                                    $check_staff['id_imekeeping_detail_hour']);
                                $this->db->update('tbl_timekeeping_detail_hour',
                                    [
                                        'hour' => $hour,
                                        'hour_real' => $hour_real,
                                        'image' => $file_name,
                                        'type_check' => $type_check
                                    ]);
                                if (!empty($timekeeping_detail_hour_in)) {
                                    if ($timekeeping_detail_hour_in['hour'] != null) {
                                        $staff = get_table_where('tblstaff', ['staffid' => $staff_id], '',
                                            'row_array');
                                        $hour_check = $hour;
                                        $startTime = $timekeeping_detail_hour_in['hour_real'];
                                        $endTime = $hour_real;
                                        $new_time = countHourCheckOut($startTime, $endTime);

                                        $timekeeping_detail_count_hour_old = get_table_where('tbl_timekeeping_detail_count_hour',
                                            [
                                                'timekeeping_id' => $check_staff['timekeeping_id'],
                                                'timekeeping_detail_id' => $check_staff['id'],
                                                'staff_id' => $check_staff['staff_id'],
                                                'type_check' => $type_check
                                            ], '', 'row_array');
                                        if (!empty($timekeeping_detail_count_hour_old)) {
                                            $this->db->where('id', $timekeeping_detail_count_hour_old['id']);
                                            $this->db->delete('tbl_timekeeping_detail_count_hour');
                                        }

                                        if (strtotime($hour_check) >= strtotime('13:00')) {
                                            if ($hour_lunch_break) {
                                                $new_time = countHourCheckOut('01:00', $new_time);
                                            }
                                        }

                                        $timekeeping_detail_old = get_table_where('tbl_timekeeping_detail',
                                            ['id' => $timekeeping_detail_hour_in['timekeeping_detail_id']], '',
                                            'row_array');
                                        $count_hour = $timekeeping_detail_old['count_hour'];


                                        if ($count_hour == 0) {
                                            $count_hour = '0';
                                        }
                                        $count_hour = countHourDetail($count_hour, countHourCheckOutNew($new_time));

                                        $this->db->insert('tbl_timekeeping_detail_count_hour', [
                                            'timekeeping_id' => $check_staff['timekeeping_id'],
                                            'timekeeping_detail_id' => $check_staff['id'],
                                            'timekeeping_detail_id_old' => 0,
                                            'staff_id' => $check_staff['staff_id'],
                                            'count_hour' => countHourCheckOutNew($new_time),
                                            'count_hour_late' => 0,
                                            'type_check' => $type_check,
                                        ]);

                                        if ((strtotime($endTime) <= strtotime('14:00')) || (strtotime($endTime) <= strtotime('17:00') && strtotime($startTime) >= strtotime('11:00'))) {
                                            $number_day = 0.5;
                                        } else {
                                            $number_day = 1;
                                        }

                                        $this->db->where('tbl_timekeeping_detail.id',
                                            $timekeeping_detail_hour_in['timekeeping_detail_id']);
                                        $this->db->update('tbl_timekeeping_detail', [
                                            'count_late_new' => $count_late_new,
                                            'count_hour' => $count_hour,
                                            'number_day' => $number_day,
                                            'count_hour_late_checkout' => countHourCheckOutNew($hour_late_new)
                                        ]);
                                        $id_insert_checkout = $check_staff['id'];
                                    }
                                }
                            } elseif (strtotime($hour) > strtotime('17:00') && strtotime($hour) <= strtotime('23:59')) {
                                $hour_check_out_type_1 = '17:00';
                                $hour_real = $hour_check_out_type_1;
                                $timekeeping_detail_hour_in = get_table_where('tbl_timekeeping_detail_hour', [
                                    'timekeeping_detail_id' => $check_staff['id'],
                                    'type' => 1,
                                    'type_check' => 1
                                ], '', 'row_array');

                                if (!empty($timekeeping_detail_hour_in)) {
                                    if ($timekeeping_detail_hour_in['hour'] != null) {

                                        $startTime = $timekeeping_detail_hour_in['hour_real'];
                                        $endTime = $hour_check_out_type_1;

                                        $startTimeNew = $startTime;

                                        $new_time = countHourCheckOut($startTime, $endTime);

                                        $timekeeping_detail_count_hour_old = get_table_where('tbl_timekeeping_detail_count_hour',
                                            [
                                                'timekeeping_id' => $check_staff['timekeeping_id'],
                                                'timekeeping_detail_id' => $check_staff['id'],
                                                'staff_id' => $check_staff['staff_id'],
                                                'type_check' => 1
                                            ], '', 'row_array');
                                        if (!empty($timekeeping_detail_count_hour_old)) {
                                            $this->db->where('id', $timekeeping_detail_count_hour_old['id']);
                                            $this->db->delete('tbl_timekeeping_detail_count_hour');
                                        }

                                        if ($hour_lunch_break) {
                                            $new_time = countHourCheckOut('01:00', $new_time);
                                        }

                                        $timekeeping_detail_old = get_table_where('tbl_timekeeping_detail',
                                            ['id' => $timekeeping_detail_hour_in['timekeeping_detail_id']], '',
                                            'row_array');
                                        $count_hour = $timekeeping_detail_old['count_hour'];
                                        if ($count_hour == 0) {
                                            $count_hour = '0';
                                        }
                                        $count_hour = countHourDetail($count_hour,
                                            countHourCheckOutNew($new_time));

                                        $this->db->insert('tbl_timekeeping_detail_count_hour', [
                                            'timekeeping_id' => $check_staff['timekeeping_id'],
                                            'timekeeping_detail_id' => $check_staff['id'],
                                            'timekeeping_detail_id_old' => 0,
                                            'staff_id' => $check_staff['staff_id'],
                                            'count_hour' => countHourCheckOutNew($new_time),
                                            'count_hour_late' => 0,
                                            'type_check' => 1,
                                        ]);

                                        $this->db->where('tbl_timekeeping_detail.id',
                                            $timekeeping_detail_hour_in['timekeeping_detail_id']);
                                        $this->db->update('tbl_timekeeping_detail',
                                            ['count_hour' => $count_hour]);

                                        $checkHour = explode(':', $hour);
                                        $hour_real_new = $hour;
                                        if ($checkHour[1] > '20' && $checkHour[1] <= '30') {
                                            $hour_real_new = $checkHour[0] . ':30';
                                        } elseif ($checkHour[1] > '50' && $checkHour[1] <= '59') {
                                            $hour_real_new = ($checkHour[0] + 1) . ':00';
                                        } elseif ($checkHour[1] > '00' && $checkHour[1] <= '20') {
                                            $hour_real_new = $checkHour[0] . ':00';
                                        } elseif ($checkHour[1] >= '31' && $checkHour[1] <= '50') {
                                            $hour_real_new = $checkHour[0] . ':30';
                                        }

                                        $dtPersonnel = get_table_where('tblstaff',
                                            ['staffid' => $check_staff['staff_id']], '', 'row_array');
                                        if ($dtPersonnel['status_overtime'] == 1) {
                                            $this->db->select('tbl_suggest_overtime_detail.*');
                                            $this->db->from('tbl_suggest_overtime_detail');
                                            $this->db->join('tbl_suggest_overtime',
                                                'tbl_suggest_overtime.id = tbl_suggest_overtime_detail.suggest_overtime_id');
                                            $this->db->where('staff_id', $check_staff['staff_id']);
                                            $this->db->where('tbl_suggest_overtime_detail.date', $check_staff['date']);
                                            // $this->db->where('tbl_suggest_overtime_detail.status', 1);
                                            $checkSuggestOvertime = $this->db->get()->row_array();
                                            if (!empty($checkSuggestOvertime)) {
                                                $hour_overtime = $checkSuggestOvertime['hour_overtime'];
                                                $startTimeCheck = '17:00';
                                                $endTimeCheck = $hour_real_new;
                                                $new_time_check = countHourCheckOut($startTimeCheck, $endTimeCheck);
                                                if (countHourCheckOutNew($new_time_check) > $hour_overtime) {
                                                    $startTimeNewVs1 = $check_staff['date'] . ' ' . $startTimeCheck;
                                                    $hour_overtime_new = explode('.', $hour_overtime);
                                                    if (!empty($hour_overtime_new[1])) {
                                                        $minutes = '0.' . $hour_overtime_new[1];
                                                    } else {
                                                        $minutes = 0;
                                                    }
                                                    $cenvertedTime = date('Y-m-d H:i',
                                                        strtotime('+' . $hour_overtime_new[0] . ' hour +' . ($minutes * 60) . ' minutes',
                                                            strtotime($startTimeNewVs1)));
                                                    $hour_real_new = date('H:i', strtotime($cenvertedTime));
                                                }
                                            } else {
                                                $hour_real_new = '17:00';
                                            }
                                        }

                                        $hour_check_in_type_3 = '17:00';
                                        $hour_check_out_type_3 = $hour_real_new;
                                        $startTime = $hour_check_in_type_3;
                                        $endTime = $hour_check_out_type_3;

                                        $endTimeNew = $endTime;

                                        $new_time = countHourCheckOut($startTime, $endTime);

                                        $timekeeping_detail_old = get_table_where('tbl_timekeeping_detail',
                                            ['id' => $check_staff['id']], '', 'row_array');
                                        $count_hour = $timekeeping_detail_old['count_hour'];
                                        if ($count_hour == 0) {
                                            $count_hour = '0';
                                        }
                                        $count_hour = countHourDetail($count_hour, countHourCheckOutNew($new_time));

                                        $this->db->insert('tbl_timekeeping_detail_count_hour', [
                                            'timekeeping_id' => $check_staff['timekeeping_id'],
                                            'timekeeping_detail_id' => $check_staff['id'],
                                            'timekeeping_detail_id_old' => 0,
                                            'staff_id' => $check_staff['staff_id'],
                                            'count_hour' => countHourCheckOutNew($new_time),
                                            'type_check' => 2,
                                        ]);

                                        if (strtotime($startTimeNew) >= strtotime('11:00')) {
                                            $number_day = 0.5;
                                        } else {
                                            $number_day = 1;
                                        }

                                        $this->db->where('tbl_timekeeping_detail.id', $check_staff['id']);
                                        $this->db->update('tbl_timekeeping_detail', [
                                            'count_hour' => $count_hour,
                                            'count_hour_overtime' => countHourCheckOutNew($new_time),
                                            'number_day' => $number_day
                                        ]);
                                        $id_insert_checkout = $check_staff['id'];

                                        $this->db->where('tbl_timekeeping_detail_hour.id',
                                            $check_staff['id_imekeeping_detail_hour']);
                                        $this->db->update('tbl_timekeeping_detail_hour',
                                            [
                                                'hour' => $hour,
                                                'hour_real' => $hour_real_new,
                                                'image' => $file_name,
                                                'type_check' => $type_check
                                            ]);
                                    }
                                }
                            }
                            $id_timekeeping_detail_new = $check_staff['id'];

                            $checkTime = false;

                            $count_hour_detail_2 = '0';

                            $this->db->select('COALESCE(SUM(tbl_timekeeping_detail_count_hour.count_hour),"0")  as count_hour');
                            $this->db->from('tbl_timekeeping_detail_count_hour');
                            $this->db->where('tbl_timekeeping_detail_count_hour.staff_id',
                                $check_staff['staff_id']);
                            $this->db->where('tbl_timekeeping_detail_count_hour.type_check = 2');
                            $this->db->where('tbl_timekeeping_detail_count_hour.timekeeping_detail_id',
                                $id_timekeeping_detail_new);
                            $this->db->group_by('tbl_timekeeping_detail_count_hour.type_check');
                            $count_hour_detail_2 = $this->db->get()->row_array()['count_hour'];
                            if (empty($count_hour_detail_2)) {
                                $count_hour_detail_2 = '0';
                            }

                            $this->db->select('COALESCE(SUM(tbl_timekeeping_detail_count_hour.count_hour),"0")  as count_hour');
                            $this->db->from('tbl_timekeeping_detail_count_hour');
                            $this->db->where('tbl_timekeeping_detail_count_hour.staff_id',
                                $check_staff['staff_id']);
                            $this->db->where('tbl_timekeeping_detail_count_hour.type_check = 1');
                            $this->db->where('tbl_timekeeping_detail_count_hour.timekeeping_detail_id',
                                $id_timekeeping_detail_new);
                            $this->db->group_by('tbl_timekeeping_detail_count_hour.type_check');
                            $count_hour_detail_1 = $this->db->get()->row_array()['count_hour'];
                            if (!empty($count_hour_detail_1)) {
                                $checkTime = true;
                            }
                            if (empty($count_hour_detail_1)) {
                                $count_hour_detail_1 = '0';
                            }

                            $business_fee = 3;

                            if (!empty($hour_real_new) || ($check_staff['check_sun'] == 1)) {
                                if (strtotime($hour_real_new) > strtotime('17:00') || ($check_staff['check_sun'] == 1)) {

                                    if ($check_staff['check_sun'] == 1) {
                                        $timekeeping_detail_hour_in = get_table_where('tbl_timekeeping_detail_hour', [
                                            'timekeeping_detail_id' => $check_staff['id'],
                                            'type' => 1,
                                            'type_check' => $type_check
                                        ], '', 'row_array');
                                        $hour_start = $timekeeping_detail_hour_in['hour_real'];
                                    } else {
                                        $hour_start = '17:00';
                                    }
                                    $hour_end = $hour_real_new;

                                    $this->db->select('tbl_business_fee_boiler_overtime.id as id');
                                    $this->db->from('tbl_business_fee_boiler_overtime');
                                    $this->db->where('staff_id', $check_staff['staff_id']);
                                    $this->db->where('month', $month);
                                    $this->db->where('year', $year);
                                    $this->db->where('type', $business_fee);
                                    $checkBusinessFee = $this->db->get()->row_array();
                                    $day_new = date("Y-m-d", strtotime($date_check));
                                    if (!empty($checkBusinessFee)) {
                                        $this->db->select('tbl_business_fee_boiler_overtime_detail.id as id');
                                        $this->db->from('tbl_business_fee_boiler_overtime_detail');
                                        $this->db->where('tbl_business_fee_boiler_overtime_detail.business_fee_boiler_overtime_id',
                                            $checkBusinessFee['id']);
                                        $this->db->where('tbl_business_fee_boiler_overtime_detail.date', $day_new);
                                        $checkBusinessFeeDetail = $this->db->get()->row_array();

                                        $dtPersonnel = get_table_where('tblstaff',
                                            ['staffid' => $check_staff['staff_id']], '', 'row_array');
                                        if ($dtPersonnel['status_overtime'] == 1) {
                                            $this->db->select('tbl_suggest_overtime_detail.*');
                                            $this->db->from('tbl_suggest_overtime_detail');
                                            $this->db->join('tbl_suggest_overtime',
                                                'tbl_suggest_overtime.id = tbl_suggest_overtime_detail.suggest_overtime_id');
                                            $this->db->where('staff_id', $check_staff['staff_id']);
                                            $this->db->where('tbl_suggest_overtime_detail.date', $check_staff['date']);
                                            // $this->db->where('tbl_suggest_overtime_detail.status', 1);
                                            $checkSuggestOvertime = $this->db->get()->row_array();
                                            if (!empty($checkSuggestOvertime)) {
                                                $hour_overtime = $checkSuggestOvertime['hour_overtime'];
                                                if ($check_staff['check_sun'] == 0) {
                                                    if ($count_hour_detail_2 <= $hour_overtime) {
                                                        $count_hour_detail_2 = $count_hour_detail_2;
                                                    } else {
                                                        $startTime = $check_staff['date'] . ' ' . $hour_start;
                                                        $hour_overtime_new = explode('.', $hour_overtime);
                                                        if (!empty($hour_overtime_new[1])) {
                                                            $minutes = '0.' . $hour_overtime_new[1];
                                                        } else {
                                                            $minutes = 0;
                                                        }
                                                        $cenvertedTime = date('Y-m-d H:i',
                                                            strtotime('+' . $hour_overtime_new[0] . ' hour +' . ($minutes * 60) . ' minutes',
                                                                strtotime($startTime)));
                                                        $count_hour_detail_2 = $hour_overtime;
                                                        $hour_end = date('H:i', strtotime($cenvertedTime));
                                                    }
                                                    $weekday = $count_hour_detail_2;
                                                    $sunday = 0;
                                                } else {
                                                    $count_hour_detail_new = countHourDetail($count_hour_detail_1,
                                                        $count_hour_detail_2);
                                                    if ($count_hour_detail_new <= $hour_overtime) {
                                                        $count_hour_detail_new = $count_hour_detail_new;
                                                    } else {
                                                        $startTime = $check_staff['date'] . ' ' . $hour_start;
                                                        $hour_overtime_new = explode('.', $hour_overtime);
                                                        if (!empty($hour_overtime_new[1])) {
                                                            $minutes = '0.' . $hour_overtime_new[1];
                                                        } else {
                                                            $minutes = 0;
                                                        }
                                                        $cenvertedTime = date('Y-m-d H:i',
                                                            strtotime('+' . $hour_overtime_new[0] . ' hour +' . ($minutes * 60) . ' minutes',
                                                                strtotime($startTime)));
                                                        $count_hour_detail_new = $count_hour_detail_new;
//                                                        $count_hour_detail_new = $hour_overtime;
//                                                        $hour_end = date('H:i',strtotime($cenvertedTime));
                                                    }
                                                    $weekday = 0;
                                                    $sunday = $count_hour_detail_new;
                                                }
                                                if (!empty($checkBusinessFeeDetail)) {
                                                    $this->db->where('id', $checkBusinessFeeDetail['id']);
                                                    $this->db->update('tbl_business_fee_boiler_overtime_detail', [
                                                        'hour_start' => $hour_start,
                                                        'hour_end' => $hour_end,
                                                        'weekday' => $weekday,
                                                        'sunday' => $sunday,
                                                        'suggest_overtime_id' => $checkSuggestOvertime['suggest_overtime_id'],
                                                        'suggest_overtime_detail_id' => $checkSuggestOvertime['id'],
                                                    ]);
                                                } else {
                                                    $this->db->insert('tbl_business_fee_boiler_overtime_detail', [
                                                        'business_fee_boiler_overtime_id' => $checkBusinessFee['id'],
                                                        'date' => $day_new,
                                                        'type' => 2,
                                                        'hour_start' => $hour_start,
                                                        'hour_end' => $hour_end,
                                                        'weekday' => $weekday,
                                                        'sunday' => $sunday,
                                                        'suggest_overtime_id' => $checkSuggestOvertime['suggest_overtime_id'],
                                                        'suggest_overtime_detail_id' => $checkSuggestOvertime['id'],
                                                    ]);
                                                }
                                            }
                                        } else {
                                            if ($check_staff['check_sun'] == 0) {
                                                $weekday = $count_hour_detail_2;
                                                $sunday = 0;
                                            } else {
                                                $weekday = 0;
                                                $sunday = countHourDetail($count_hour_detail_1, $count_hour_detail_2);
                                            }
                                            if (!empty($checkBusinessFeeDetail)) {
                                                $this->db->where('id', $checkBusinessFeeDetail['id']);
                                                $this->db->update('tbl_business_fee_boiler_overtime_detail', [
                                                    'hour_start' => $hour_start,
                                                    'hour_end' => $hour_end,
                                                    'weekday' => $weekday,
                                                    'sunday' => $sunday,
                                                ]);
                                            } else {
                                                $this->db->insert('tbl_business_fee_boiler_overtime_detail', [
                                                    'business_fee_boiler_overtime_id' => $checkBusinessFee['id'],
                                                    'date' => $day_new,
                                                    'type' => 2,
                                                    'hour_start' => $hour_start,
                                                    'hour_end' => $hour_end,
                                                    'weekday' => $weekday,
                                                    'sunday' => $sunday,
                                                ]);
                                            }
                                        }
                                    } else {
                                        $name_text = get_table_where('tblstaff',
                                            ['staffid' => $check_staff['staff_id']], '', 'row_array');
                                        $status_overtime = $name_text['status_overtime'];
                                        if ($status_overtime == 1) {
                                            $this->db->select('tbl_suggest_overtime_detail.*');
                                            $this->db->from('tbl_suggest_overtime_detail');
                                            $this->db->join('tbl_suggest_overtime',
                                                'tbl_suggest_overtime.id = tbl_suggest_overtime_detail.suggest_overtime_id');
                                            $this->db->where('staff_id', $check_staff['staff_id']);
                                            $this->db->where('tbl_suggest_overtime_detail.date', $check_staff['date']);
                                            // $this->db->where('tbl_suggest_overtime_detail.status', 1);
                                            $checkSuggestOvertime = $this->db->get()->row_array();
                                            if (!empty($checkSuggestOvertime)) {
                                                $hour_overtime = $checkSuggestOvertime['hour_overtime'];
                                                $this->db->insert('tbl_business_fee_boiler_overtime', [
                                                    'name' => $name_text['firstname'] . ' ' . $name_text['lastname'],
                                                    'month' => $month,
                                                    'year' => $year,
                                                    'staff_id' => $check_staff['staff_id'],
                                                    'date_created' => date('Y-m-d H:i:s'),
                                                    'created_by' => 1,
                                                    'type' => $business_fee,
                                                ]);
                                                $id_insert = $this->db->insert_id();
                                                if ($id_insert) {
                                                    if ($check_staff['check_sun'] == 0) {
                                                        if ($count_hour_detail_2 <= $hour_overtime) {
                                                            $count_hour_detail_2 = $count_hour_detail_2;
                                                        } else {
                                                            $startTime = $check_staff['date'] . ' ' . $hour_start;
                                                            $hour_overtime_new = explode('.', $hour_overtime);
                                                            if (!empty($hour_overtime_new[1])) {
                                                                $minutes = '0.' . $hour_overtime_new[1];
                                                            } else {
                                                                $minutes = 0;
                                                            }
                                                            $cenvertedTime = date('Y-m-d H:i',
                                                                strtotime('+' . $hour_overtime_new[0] . ' hour +' . ($minutes * 60) . ' minutes',
                                                                    strtotime($startTime)));
                                                            $count_hour_detail_2 = $hour_overtime;
                                                            $hour_end = date('H:i', strtotime($cenvertedTime));
                                                        }
                                                        $weekday = $count_hour_detail_2;
                                                        $sunday = 0;
                                                    } else {
                                                        $count_hour_detail_new = countHourDetail($count_hour_detail_1,
                                                            $count_hour_detail_2);
                                                        if ($count_hour_detail_new <= $hour_overtime) {
                                                            $count_hour_detail_new = $count_hour_detail_new;
                                                        } else {
                                                            $startTime = $check_staff['date'] . ' ' . $hour_start;
                                                            $hour_overtime_new = explode('.', $hour_overtime);
                                                            if (!empty($hour_overtime_new[1])) {
                                                                $minutes = '0.' . $hour_overtime_new[1];
                                                            } else {
                                                                $minutes = 0;
                                                            }
                                                            $cenvertedTime = date('Y-m-d H:i',
                                                                strtotime('+' . $hour_overtime_new[0] . ' hour +' . ($minutes * 60) . ' minutes',
                                                                    strtotime($startTime)));
                                                            $count_hour_detail_new = $count_hour_detail_new;
//                                                            $count_hour_detail_new = $hour_overtime;
//                                                            $hour_end = date('H:i',strtotime($cenvertedTime));
                                                        }
                                                        $weekday = 0;
                                                        $sunday = $count_hour_detail_new;
                                                    }
                                                    $this->db->insert('tbl_business_fee_boiler_overtime_detail', [
                                                        'business_fee_boiler_overtime_id' => $id_insert,
                                                        'date' => $day_new,
                                                        'type' => 2,
                                                        'hour_start' => $hour_start,
                                                        'hour_end' => $hour_end,
                                                        'weekday' => $weekday,
                                                        'sunday' => $sunday,
                                                        'suggest_overtime_id' => $checkSuggestOvertime['suggest_overtime_id'],
                                                        'suggest_overtime_detail_id' => $checkSuggestOvertime['id'],
                                                    ]);
                                                }
                                            }
                                        } else {
                                            $this->db->insert('tbl_business_fee_boiler_overtime', [
                                                'name' => $name_text['firstname'] . ' ' . $name_text['lastname'],
                                                'month' => $month,
                                                'year' => $year,
                                                'staff_id' => $check_staff['staff_id'],
                                                'date_created' => date('Y-m-d H:i:s'),
                                                'created_by' => 1,
                                                'type' => $business_fee,
                                            ]);
                                            $id_insert = $this->db->insert_id();
                                            if ($id_insert) {
                                                if ($check_staff['check_sun'] == 0) {
                                                    $weekday = $count_hour_detail_2;
                                                    $sunday = 0;
                                                } else {
                                                    $weekday = 0;
                                                    $sunday = countHourDetail($count_hour_detail_1,
                                                        $count_hour_detail_2);
                                                }
                                                $this->db->insert('tbl_business_fee_boiler_overtime_detail', [
                                                    'business_fee_boiler_overtime_id' => $id_insert,
                                                    'date' => $day_new,
                                                    'type' => 2,
                                                    'hour_start' => $hour_start,
                                                    'hour_end' => $hour_end,
                                                    'weekday' => $weekday,
                                                    'sunday' => $sunday,
                                                ]);
                                            }
                                        }
                                    }
                                }
                            }


                            $data_count_hour = [];
                            $date = '';
                            $count_hour_data = '0';
                            $timekeeping_detail_new = get_table_where('tbl_timekeeping_detail',
                                ['id' => $id_timekeeping_detail_new], '', 'row_array');
                            if (!empty($timekeeping_detail_new)) {
                                $date = $timekeeping_detail_new['date'];
                            }
                            $count_hour_data = countHourDetail(0, $count_hour_detail_2);
                            $count_hour_data_new = countHourDetail(0, $count_hour_detail_1);
                            $numberDay = 0;

                            if (!empty($timekeeping_detail_new)) {
                                $numberDay = $timekeeping_detail_new['number_day'];
                            }

                            if ($check_staff['check_sun'] == 0) {
                                $data_count_hour = [
                                    'Ngày' => $date,
                                    'Giờ công' => $count_hour_data_new,
                                    'Số giờ tăng ca' => $count_hour_data
                                ];
                            } else {
                                $data_count_hour = [
                                    'Ngày' => $date,
                                    'Giờ công' => 0,
                                    'Số giờ tăng ca' => countHourDetail($count_hour_detail_1, $count_hour_detail_2),
                                ];
                            }

                            $staff = get_table_where('tblstaff', ['staffid' => $check_staff['staff_id']], '',
                                'row_array');
                            $data['success'] = true;
                            $data['type_check_hour'] = 2;
                            $data['staff_name'] = $staff['firstname'] . ' ' . $staff['lastname'];
                            $data['id'] = $id_insert_checkout;
                            $data['time'] = $date_check;
                            $data['data_count_hour'] = $data_count_hour;
                            $data['message'] = 'Check out thành công';

                        }
                    }
                }
            }
        }

        echo json_encode($data);
    }

    public function QRStaff()
    {
        $data['success'] = false;
        $data['type_check_hour'] = -1;
        $data['message'] = '';
        if ($this->input->post()) {
            $id_timekeeping_detail_hour = $this->input->post('id_timekeeping_detail_hour');
            $staff_id = $this->input->post('staff_id');
            $date_check = $this->input->post('date_check');
            $type_hour = $this->input->post('type_hour');
            $type_check_in = $this->input->post('type_check_in');
            $type_check = $this->input->post('type_check');
            if (empty($type_check)) {
                $type_check = 1;
            }
            if (!empty($type_hour) && !empty($staff_id) && !empty($date_check)) {
                $day = date("d", strtotime($date_check));
                $hour = date("H:i", strtotime($date_check));
                $month = date("m", strtotime($date_check));
                $year = date("Y", strtotime($date_check));
                $zero = 0;
                $month_check_old = '';

                $staff_v1 = get_table_where('tblstaff', ['staffid' => $staff_id], '', 'row_array');
                if ($staff_v1['status_work'] == 3) {
                    $data['success'] = false;
                    $data['type_check_hour'] = -1;
                    $data['staff_name'] = $staff_v1['firstname'] . ' ' . $staff_v1['lastname'];
                    $data['id'] = -1;
                    $data['time'] = $hour;
                    $data['date'] = $day;
                    $data['message'] = 'Nhân viên đã nghỉ việc';
                    echo json_encode($data);
                    die();
                }

                $this->db->select('tbl_timekeeping_detail.*,tbl_timekeeping_detail_hour.hour,tbl_timekeeping_detail_hour.type as type_hour,tbl_timekeeping_detail_hour.id as id_imekeeping_detail_hour,tbl_timekeeping_detail_hour.type_check as type_check,tbl_timekeeping.month as month,tbl_timekeeping.year');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping_detail_hour',
                    'tbl_timekeeping_detail_hour.timekeeping_detail_id =tbl_timekeeping_detail.id', 'left');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id =tbl_timekeeping_detail.timekeeping_id', 'left');
                $this->db->where('tbl_timekeeping_detail.staff_id', $staff_id);
                $this->db->where('tbl_timekeeping_detail.day', $day);
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail_hour.type', $type_hour);
                if ($type_hour == 1) {
                    $this->db->order_by('tbl_timekeeping_detail_hour.id asc');
                } else {
                    $this->db->order_by('tbl_timekeeping_detail_hour.id desc');
                }


                $check_staff = $this->db->get()->row_array();

                if (!empty($check_staff)) {
                    $staff = get_table_where('tblstaff', ['staffid' => $check_staff['staff_id']], '',
                        'row_array');
                    $dtShift = get_table_where('tbl_setup_shift',['id' => $staff['setup_shift_id']],'','row_array');
                    $arrDate = [];
                    $dtDay = get_table_where('tbl_setup_shift_day',['shift_id' => $dtShift['id'],'type' => 1]);
                    if (!empty($dtDay)){
                        foreach ($dtDay as $key => $value){
                            $arrDate[]=$value['day'];
                        }
                    }
                    $arrDateOvertime = [];
                    $dtDay = get_table_where('tbl_setup_shift_day',['shift_id' => $dtShift['id'],'type' => 2]);
                    if (!empty($dtDay)){
                        foreach ($dtDay as $key => $value){
                            $arrDateOvertime[]=$value['day'];
                        }
                    }

                    $arrDateHalftime = [];
                    $arrDateHalftimeHour = [];
                    $dtDay = get_table_where('tbl_setup_shift_day',['shift_id' => $dtShift['id'],'type' => 3]);
                    if (!empty($dtDay)){
                        foreach ($dtDay as $key => $value){
                            $arrDateHalftime[]=$value['day'];
                            $arrDateHalftimeHour[$value['day']]=[
                                'time_start' => $value['time_start'],
                                'time_end' => $value['time_end'],
                                'time_overtime' => $value['time_overtime']
                            ];
                        }
                    }
                    if (empty($dtShift)){
                        $data['success'] = false;
                        $data['type_check_hour'] = -1;
                        $data['staff_name'] = $staff['firstname'] . ' ' . $staff['lastname'];
                        $data['id'] = $check_staff['id_imekeeping_detail_hour'];
                        $data['time'] = $check_staff['hour'];
                        $data['date'] = $check_staff['date'];
                        $data['message'] = 'Không tồn tại ca làm việc!';
                        echo json_encode($data);
                        die();
                    }
                    if (!in_array($check_staff['date_word'],$arrDate) && !in_array($check_staff['date_word'],$arrDateOvertime) && !in_array($check_staff['date_word'],$arrDateHalftime)){
                        $data['success'] = false;
                        $data['type_check_hour'] = -1;
                        $data['staff_name'] = $staff['firstname'] . ' ' . $staff['lastname'];
                        $data['id'] = $check_staff['id_imekeeping_detail_hour'];
                        $data['time'] = $check_staff['hour'];
                        $data['date'] = $check_staff['date'];
                        $data['message'] = 'Ngày này không tồn tại trong ca làm việc!';
                        echo json_encode($data);
                        die();
                    }
                    if ($check_staff['type_hour'] == 1) {
                        if ($check_staff['hour'] != null) {
                            $data['success'] = false;
                            $data['type_check_hour'] = -1;
                            $data['staff_name'] = $staff['firstname'] . ' ' . $staff['lastname'];
                            $data['id'] = $check_staff['id_imekeeping_detail_hour'];
                            $data['time'] = $check_staff['hour'];
                            $data['date'] = $check_staff['date'];
                            $data['message'] = 'Đã check in rồi';
                        } else {
                            if (in_array($check_staff['date_word'],$arrDateHalftime)){
                                if (empty($arrDateHalftimeHour[$check_staff['date_word']]['time_start']) || empty($arrDateHalftimeHour[$check_staff['date_word']]['time_end']) || empty($arrDateHalftimeHour[$check_staff['date_word']]['time_overtime'])){
                                    $data['success'] = false;
                                    $data['type_check_hour'] = -1;
                                    $data['staff_name'] = $staff['firstname'] . ' ' . $staff['lastname'];
                                    $data['id'] = -1;
                                    $data['time'] = $hour;
                                    $data['date'] = $day;
                                    $data['message'] = 'Thời gian bắt đầu, kết thúc, bắt đầu tăng ca. Ngày làm việc nửa ngày không được trống!';
                                    echo json_encode($data);
                                    die();
                                }
                                if (strtotime($hour) > strtotime($arrDateHalftimeHour[$check_staff['date_word']]['time_end'])) {
                                    $data['success'] = false;
                                    $data['type_check_hour'] = -1;
                                    $data['staff_name'] = $staff['firstname'] . ' ' . $staff['lastname'];
                                    $data['id'] = -1;
                                    $data['time'] = $hour;
                                    $data['date'] = $day;
                                    $data['message'] = 'Vui lòng check in trong khoảng thời gian quy định !';
                                    echo json_encode($data);
                                    die();
                                }
                            } else {
                                if (strtotime($hour) > strtotime($dtShift['time_end'])) {
                                    $data['success'] = false;
                                    $data['type_check_hour'] = -1;
                                    $data['staff_name'] = $staff['firstname'] . ' ' . $staff['lastname'];
                                    $data['id'] = -1;
                                    $data['time'] = $hour;
                                    $data['date'] = $day;
                                    $data['message'] = 'Vui lòng check in trong khoảng thời gian quy định !';
                                    echo json_encode($data);
                                    die();
                                }
                            }

                            $this->load->library('upload');
                            $file_name = '';
                            if (!empty($_FILES['image']) && !empty($_FILES['image']['name'])) {
                                $_FILES['file']['name'] = time() . '_' . $_FILES['image']['name'];
                                $_FILES['file']['type'] = $_FILES['image']['type'];
                                $_FILES['file']['tmp_name'] = $_FILES['image']['tmp_name'];
                                $_FILES['file']['error'] = $_FILES['image']['error'];
                                $_FILES['file']['size'] = $_FILES['image']['size'];

                                $config['upload_path'] = './uploads/timekeeping_staffs/';
                                $config['allowed_types'] = '*';
                                $this->upload->initialize($config);
                                if ($this->upload->do_upload('file')) {
                                    $file_name = $this->upload->file_name;
                                }
                            }

                            $count_late = 0;
                            $hour_overtime = 0;
                            $hour_real = $hour;
                            $hour_real_new = $hour;
                            $type_detail = 'X';

                            if (in_array($check_staff['date_word'],$arrDateHalftime)){
                                if (strtotime($hour) > strtotime(fomart_hour($arrDateHalftimeHour[$check_staff['date_word']]['time_start']))) {
                                    $count_late = 1;
                                }
                                if (strtotime($hour) < strtotime(fomart_hour($arrDateHalftimeHour[$check_staff['date_word']]['time_start']))) {
                                    $checkHour = explode(':', $hour);
                                    if ($checkHour[1] > '00' && $checkHour[1] < '09') {
                                        $hour_real_new = $checkHour[0] . ':00';
                                    } elseif ($checkHour[1] >= '09' && $checkHour[1] < '14') {
                                        $hour_real_new = $checkHour[0] . ':08';
                                    } elseif ($checkHour[1] >= '14' && $checkHour[1] < '18') {
                                        $hour_real_new = ($checkHour[0]) . ':14';
                                    } elseif ($checkHour[1] >= '18' && $checkHour[1] < '24') {
                                        $hour_real_new = $checkHour[0] . ':18';
                                    } elseif ($checkHour[1] >= '24' && $checkHour[1] < '28') {
                                        $hour_real_new = $checkHour[0] . ':24';
                                    } elseif ($checkHour[1] >= '28' && $checkHour[1] < '38') {
                                        $hour_real_new = $checkHour[0] . ':28';
                                    } elseif ($checkHour[1] >= '38' && $checkHour[1] < '44') {
                                        $hour_real_new = $checkHour[0] . ':38';
                                    } elseif ($checkHour[1] >= '44' && $checkHour[1] < '48') {
                                        $hour_real_new = $checkHour[0] . ':44';
                                    } elseif ($checkHour[1] >= '48' && $checkHour[1] < '54') {
                                        $hour_real_new = $checkHour[0] . ':48';
                                    } elseif ($checkHour[1] >= '54' && $checkHour[1] < '58') {
                                        $hour_real_new = $checkHour[0] . ':54';
                                    } elseif ($checkHour[1] >= '58' && $checkHour[1] <= '59') {
                                        $hour_real_new = ($checkHour[0] + 1) . ':00';
                                    }
                                    $hour_real_new = $hour;
                                    $hour_overtime = countHourCheckOut($hour_real_new,
                                        fomart_hour($arrDateHalftimeHour[$check_staff['date_word']]['time_start']));
                                    $hour_real = fomart_hour($arrDateHalftimeHour[$check_staff['date_word']]['time_start']);
                                }
                            } else {
                                if ((strtotime($hour) > strtotime(fomart_hour($dtShift['time_start'])) && strtotime($hour) < strtotime(fomart_hour($dtShift['time_start_lunch_break']))) || (strtotime($hour) > strtotime(fomart_hour($dtShift['time_end_lunch_break'])))) {
                                    $count_late = 1;
                                }
                                if (strtotime($hour) < strtotime(fomart_hour($dtShift['time_start']))) {
                                    $checkHour = explode(':', $hour);
                                    if ($checkHour[1] > '00' && $checkHour[1] < '09') {
                                        $hour_real_new = $checkHour[0] . ':00';
                                    } elseif ($checkHour[1] >= '09' && $checkHour[1] < '14') {
                                        $hour_real_new = $checkHour[0] . ':08';
                                    } elseif ($checkHour[1] >= '14' && $checkHour[1] < '18') {
                                        $hour_real_new = ($checkHour[0]) . ':14';
                                    } elseif ($checkHour[1] >= '18' && $checkHour[1] < '24') {
                                        $hour_real_new = $checkHour[0] . ':18';
                                    } elseif ($checkHour[1] >= '24' && $checkHour[1] < '28') {
                                        $hour_real_new = $checkHour[0] . ':24';
                                    } elseif ($checkHour[1] >= '28' && $checkHour[1] < '38') {
                                        $hour_real_new = $checkHour[0] . ':28';
                                    } elseif ($checkHour[1] >= '38' && $checkHour[1] < '44') {
                                        $hour_real_new = $checkHour[0] . ':38';
                                    } elseif ($checkHour[1] >= '44' && $checkHour[1] < '48') {
                                        $hour_real_new = $checkHour[0] . ':44';
                                    } elseif ($checkHour[1] >= '48' && $checkHour[1] < '54') {
                                        $hour_real_new = $checkHour[0] . ':48';
                                    } elseif ($checkHour[1] >= '54' && $checkHour[1] < '58') {
                                        $hour_real_new = $checkHour[0] . ':54';
                                    } elseif ($checkHour[1] >= '58' && $checkHour[1] <= '59') {
                                        $hour_real_new = ($checkHour[0] + 1) . ':00';
                                    }
                                    $hour_real_new = $hour;
                                    $hour_overtime = countHourCheckOut($hour_real_new,
                                        fomart_hour($dtShift['time_start']));
                                    $hour_real = fomart_hour($dtShift['time_start']);
                                } else {
                                    if (strtotime($hour) >= strtotime(fomart_hour($dtShift['time_start_lunch_break'])) && strtotime($hour) <= strtotime(fomart_hour($dtShift['time_end_lunch_break']))) {
                                        if ($dtShift['check_lunch_break'] == 1){
                                            $hour_real = $hour;
                                            $checkHour = explode(':', $hour);
                                            if ($checkHour[1] > '20' && $checkHour[1] <= '30') {
                                                $hour_real = $checkHour[0] . ':30';
                                            } elseif ($checkHour[1] > '50' && $checkHour[1] <= '59') {
                                                $hour_real = ($checkHour[0] + 1) . ':00';
                                            } elseif ($checkHour[1] > '00' && $checkHour[1] <= '20') {
                                                $hour_real = $checkHour[0] . ':00';
                                            } elseif ($checkHour[1] >= '31' && $checkHour[1] <= '50') {
                                                $hour_real = $checkHour[0] . ':30';
                                            }
                                            $hour_real = $hour;
                                        } else {
                                            $hour_real = fomart_hour($dtShift['time_end_lunch_break']);
                                        }
                                    } else {
                                        $hour_real = $hour;
                                        $checkHour = explode(':', $hour);
                                        if ($checkHour[1] > '20' && $checkHour[1] <= '30') {
                                            $hour_real = $checkHour[0] . ':30';
                                        } elseif ($checkHour[1] > '50' && $checkHour[1] <= '59') {
                                            $hour_real = ($checkHour[0] + 1) . ':00';
                                        } elseif ($checkHour[1] > '00' && $checkHour[1] <= '20') {
                                            $hour_real = $checkHour[0] . ':00';
                                        } elseif ($checkHour[1] >= '31' && $checkHour[1] <= '50') {
                                            $hour_real = $checkHour[0] . ':30';
                                        }
                                        $hour_real = $hour;
                                    }
                                }
                            }
                            $hour_late = 0;
                            $hour_late_checkin = 0;
                            $hour_real_new = explode(':', $hour_real);
                            if ($hour_real_new['0'] < 10) {
                                if (strlen($hour_real_new[0]) == 1) {
                                    $hour_real_check = '0' . $hour_real_new[0] . ':' . $hour_real_new[1];
                                } else {
                                    $hour_real_check = $hour_real;
                                }
                            } else {
                                $hour_real_check = $hour_real;
                            }
                            if (in_array($check_staff['date_word'],$arrDateHalftime)){
                                $hour_late = countHourCheckOut(fomart_hour($arrDateHalftimeHour[$check_staff['date_word']]['time_start']), $hour_real);
                            } else {
                                if (strtotime($hour_real_check) <= strtotime(fomart_hour($dtShift['time_start_lunch_break']))) {
                                    $hour_late = countHourCheckOut(fomart_hour($dtShift['time_start']), $hour_real);
                                    $hour_late_checkin = $hour_late;
                                } elseif (strtotime($hour_real_check) > strtotime(fomart_hour($dtShift['time_start_lunch_break'])) && strtotime($hour_real_check) < strtotime(fomart_hour($dtShift['time_end_lunch_break']))) {
                                    if ($dtShift['check_lunch_break'] == 1){
                                        $hour_late = countHourCheckOut(fomart_hour($dtShift['time_start']), $hour_real);
                                    } else {
                                        $hour_late = countHourCheckOut(fomart_hour($dtShift['time_start']),
                                            $dtShift['time_start_lunch_break']);
                                    }
                                } elseif (strtotime($hour_real_check) >= strtotime($dtShift['time_end_lunch_break'])) {
                                    if ($dtShift['check_lunch_break'] == 1){
                                        $hour_late = countHourCheckOut(fomart_hour($dtShift['time_start']), $hour_real);
                                    } else {
                                        $hour_late_new = countHourCheckOut(fomart_hour($dtShift['time_start']),
                                            $hour_real);
                                        $hour_late = countHourCheckOut(countHourCheckOut(fomart_hour($dtShift['time_start_lunch_break']),
                                            fomart_hour($dtShift['time_end_lunch_break'])), $hour_late_new);
                                    }
                                }
                            }


                            $timekeeping_detail_hour_check_out = get_table_where('tbl_timekeeping_detail_hour',
                                ['timekeeping_detail_id' => $check_staff['id'], 'type' => 2], '', 'row_array');

                            $this->db->where('tbl_timekeeping_detail_hour.id',
                                $check_staff['id_imekeeping_detail_hour']);
                            $this->db->update('tbl_timekeeping_detail_hour', [
                                'hour' => $hour,
                                'hour_real' => $hour_real,
                                'image' => $file_name,
                                'type_check' => $type_check,
                                'type_check_in' => $type_check_in
                            ]);

                            $this->db->where('tbl_timekeeping_detail_hour.id',
                                $timekeeping_detail_hour_check_out['id']);
                            $this->db->update('tbl_timekeeping_detail_hour', ['type_check' => $type_check]);
                            $id_insert_check = $check_staff['id_imekeeping_detail_hour'];


                            $this->db->where('tbl_timekeeping_detail.id', $check_staff['id']);
                            $this->db->update('tbl_timekeeping_detail',
                                [
                                    'count_late' => $count_late,
//                                    'type' => $type_detail,
//                                    'paid_holiday_id' => 0,
//                                    'paid_holiday_detail_id' => 0,
                                    'count_hour_late' => countHourCheckOutNew($hour_late),
                                    'count_hour_overtime_new' => countHourCheckOutNew($hour_overtime),
                                    'count_hour_late_new' => countHourCheckOutNew($hour_late_checkin)
                                ]);
                            $data['success'] = true;
                            $data['type_check_hour'] = 1;
                            $data['staff_name'] = $staff['firstname'] . ' ' . $staff['lastname'];
                            $data['id'] = $id_insert_check;
                            $data['time'] = $date_check;
                            $data['type_check'] = $type_check;
                            $data['message'] = 'Check in thành công';
                        }
                    } elseif ($check_staff['type_hour'] == 2) {
                        //check check out
                        $count_late_new = 0;
                        $hour_late_new = 0;
                        $day_check_old = '';
                        $timekeeping_detail_hour_in_check_old = get_table_where('tbl_timekeeping_detail_hour',
                            ['id' => $id_timekeeping_detail_hour], '', 'row_array');
                        if (!empty($timekeeping_detail_hour_in_check_old)) {
                            $timekeeping_detail_check_day_old = get_table_where('tbl_timekeeping_detail',
                                ['id' => $timekeeping_detail_hour_in_check_old['timekeeping_detail_id']], '',
                                'row_array');
                            if (!empty($timekeeping_detail_check_day_old)) {
                                $day_check_old = $timekeeping_detail_check_day_old['day'];
                            }
                        }
                        if (empty($day_check_old)) {
                            $data['success'] = false;
                            $data['type_check_hour'] = -1;
                            $data['staff_name'] = $staff['firstname'] . ' ' . $staff['lastname'];
                            $data['id'] = null;
                            $data['time'] = null;
                            $data['date'] = null;
                            $data['message'] = 'Check out lỗi';
                            echo json_encode($data);
                            die();
                        }
                        if ($day - $day_check_old > 0) {
                            $data['success'] = false;
                            $data['type_check_hour'] = -1;
                            $data['staff_name'] = $staff['firstname'] . ' ' . $staff['lastname'];
                            $data['id'] = null;
                            $data['time'] = null;
                            $data['date'] = null;
                            $data['message'] = 'Vui lòng checkout trong cùng ngày';
                            echo json_encode($data);
                            die();
                        }
                        if (strtotime($hour) > strtotime('23:59')) {
                            $data['success'] = false;
                            $data['type_check_hour'] = -1;
                            $data['staff_name'] = $staff['firstname'] . ' ' . $staff['lastname'];
                            $data['id'] = null;
                            $data['time'] = null;
                            $data['date'] = null;
                            $data['message'] = 'Vui lòng check out trước 24h';
                            echo json_encode($data);
                            die();
                        }
                        if ($check_staff['hour'] != null) {
                            $data['success'] = false;
                            $data['type_check_hour'] = -1;
                            $data['staff_name'] = $staff['firstname'] . ' ' . $staff['lastname'];
                            $data['id'] = $check_staff['id_imekeeping_detail_hour'];
                            $data['time'] = $check_staff['hour'];
                            $data['date'] = $check_staff['date'];
                            $data['message'] = 'Đã check out rồi';
                        } else {
                            $this->load->library('upload');
                            $file_name = '';
                            if (!empty($_FILES['image']) && !empty($_FILES['image']['name'])) {
                                $_FILES['file']['name'] = time() . '_' . $_FILES['image']['name'];
                                $_FILES['file']['type'] = $_FILES['image']['type'];
                                $_FILES['file']['tmp_name'] = $_FILES['image']['tmp_name'];
                                $_FILES['file']['error'] = $_FILES['image']['error'];
                                $_FILES['file']['size'] = $_FILES['image']['size'];

                                $config['upload_path'] = './uploads/timekeeping_staffs/';
                                $config['allowed_types'] = '*';
                                $this->upload->initialize($config);
                                if ($this->upload->do_upload('file')) {
                                    $file_name = $this->upload->file_name;
                                }
                            }

                            //check check out
                            $day_check_old = '';
                            $month_check_old = '';
                            $year_check_old = '';
                            $timekeeping_detail_hour_in_check_old = get_table_where('tbl_timekeeping_detail_hour',
                                ['id' => $id_timekeeping_detail_hour], '', 'row_array');
                            if (!empty($timekeeping_detail_hour_in_check_old)) {
                                $timekeeping_detail_check_day_old = get_table_where('tbl_timekeeping_detail',
                                    ['id' => $timekeeping_detail_hour_in_check_old['timekeeping_detail_id']], '',
                                    'row_array');
                                if (!empty($timekeeping_detail_check_day_old)) {
                                    $day_check_old = $timekeeping_detail_check_day_old['day'];
                                    $timekeeping = get_table_where('tbl_timekeeping',
                                        ['id' => $timekeeping_detail_check_day_old['timekeeping_id']], '', 'row_array');
                                    $month_check_old = $timekeeping['month'];
                                    $year_check_old = $timekeeping['year'];
                                }
                            }

                            $timekeeping_detail_hour_in_check = get_table_where('tbl_timekeeping_detail_hour', [
                                'timekeeping_detail_id' => $check_staff['id'],
                                'type_check' => $check_staff['type_check'],
                                'type' => 1
                            ], '', 'row_array');

                            $hour_check_in = '';
                            $hour_lunch_break = false;
                            $day_check = '';
                            $file_name_old = '';
                            if (!empty($timekeeping_detail_hour_in_check)) {
                                $hour_check_in = $timekeeping_detail_hour_in_check['hour'];
                                $timekeeping_detail_check_day = get_table_where('tbl_timekeeping_detail',
                                    ['id' => $timekeeping_detail_hour_in_check['timekeeping_detail_id']], '',
                                    'row_array');
                                if (!empty($timekeeping_detail_check_day)) {
                                    $day_check = $timekeeping_detail_check_day['day'];
                                }
                                $file_name_old = $timekeeping_detail_hour_in_check['image'];
                            }
                            if (!in_array($check_staff['date_word'],$arrDateHalftime)) {
                                if ($dtShift['check_lunch_break'] == 1){
                                    $hour_lunch_break = false;
                                } else {
                                    if (strtotime($hour_check_in) < strtotime(fomart_hour($dtShift['time_start_lunch_break']))) {
                                        $hour_lunch_break = true;
                                    }
                                }
                            }
                            if (!empty($timekeeping_detail_hour_in_check)) {
                                if ($timekeeping_detail_hour_in_check['hour'] == null) {
                                    $data['success'] = false;
                                    $data['type_check_hour'] = -1;
                                    $data['staff_name'] = $staff['firstname'] . ' ' . $staff['lastname'];
                                    $data['id'] = -1;
                                    $data['time'] = $hour;
                                    $data['date'] = $day;
                                    $data['message'] = 'Nhân viên chưa check in không thể check out';
                                    echo json_encode($data);
                                    die();
                                }
                            }
                            if (!empty($timekeeping_detail_hour_in_check)) {
                                if (strtotime($hour) < strtotime($timekeeping_detail_hour_in_check['hour'])) {
                                    $data['success'] = false;
                                    $data['type_check_hour'] = -1;
                                    $data['staff_name'] = $staff['firstname'] . ' ' . $staff['lastname'];
                                    $data['id'] = -1;
                                    $data['time'] = $hour;
                                    $data['date'] = $day;
                                    $data['message'] = 'Giờ ra không thể nhỏ hơn giờ vào';
                                    echo json_encode($data);
                                    die();
                                }
                            }


                            if (strtotime($hour) <= strtotime('23:59')) {
                                $hour_real = $hour;
                                $checkHour = explode(':', $hour);
                                if (in_array($check_staff['date_word'],$arrDateOvertime)){
                                    if ($checkHour[1] > '00' && $checkHour[1] < '09') {
                                        $hour_real = $checkHour[0] . ':00';
                                    } elseif ($checkHour[1] >= '09' && $checkHour[1] < '14') {
                                        $hour_real = $checkHour[0] . ':08';
                                    } elseif ($checkHour[1] >= '14' && $checkHour[1] < '18') {
                                        $hour_real = ($checkHour[0]) . ':14';
                                    } elseif ($checkHour[1] >= '18' && $checkHour[1] < '24') {
                                        $hour_real = $checkHour[0] . ':18';
                                    } elseif ($checkHour[1] >= '24' && $checkHour[1] < '28') {
                                        $hour_real = $checkHour[0] . ':24';
                                    } elseif ($checkHour[1] >= '28' && $checkHour[1] < '38') {
                                        $hour_real = $checkHour[0] . ':28';
                                    } elseif ($checkHour[1] >= '38' && $checkHour[1] < '44') {
                                        $hour_real = $checkHour[0] . ':38';
                                    } elseif ($checkHour[1] >= '44' && $checkHour[1] < '48') {
                                        $hour_real = $checkHour[0] . ':44';
                                    } elseif ($checkHour[1] >= '48' && $checkHour[1] < '54') {
                                        $hour_real = $checkHour[0] . ':48';
                                    } elseif ($checkHour[1] >= '54' && $checkHour[1] < '58') {
                                        $hour_real = $checkHour[0] . ':54';
                                    } elseif ($checkHour[1] >= '58' && $checkHour[1] <= '59') {
                                        $hour_real = ($checkHour[0] + 1) . ':00';
                                    }
                                    $hour_real = $hour;
                                } elseif (in_array($check_staff['date_word'],$arrDate)){
                                    if (strtotime($hour) < strtotime(fomart_hour($dtShift['time_end']))) {
                                        $count_late_new = 1;
                                    }
                                    if ($checkHour[1] > '20' && $checkHour[1] <= '30') {
                                        $hour_real = $checkHour[0] . ':30';
                                    } elseif ($checkHour[1] > '50' && $checkHour[1] <= '59') {
                                        $hour_real = ($checkHour[0] + 1) . ':00';
                                    } elseif ($checkHour[1] > '00' && $checkHour[1] <= '20') {
                                        $hour_real = $checkHour[0] . ':00';
                                    } elseif ($checkHour[1] >= '31' && $checkHour[1] <= '50') {
                                        $hour_real = $checkHour[0] . ':30';
                                    }
                                    $hour_real = $hour;
                                    if (strtotime($hour) < strtotime(fomart_hour($dtShift['time_end']))) {
                                        if (strtotime($hour_real) >= strtotime('15:00')) {
                                            $hour_late_new = countHourCheckOut($hour_real,
                                                (fomart_hour($dtShift['time_end'])));
                                        }
                                    }
                                } else {
                                    if ($checkHour[1] > '20' && $checkHour[1] <= '30') {
                                        $hour_real = $checkHour[0] . ':30';
                                    } elseif ($checkHour[1] > '50' && $checkHour[1] <= '59') {
                                        $hour_real = ($checkHour[0] + 1) . ':00';
                                    } elseif ($checkHour[1] > '00' && $checkHour[1] <= '20') {
                                        $hour_real = $checkHour[0] . ':00';
                                    } elseif ($checkHour[1] >= '31' && $checkHour[1] <= '50') {
                                        $hour_real = $checkHour[0] . ':30';
                                    }
                                    $hour_real = $hour;
                                }

                                if (!in_array($check_staff['date_word'],$arrDateHalftime)) {
                                    if (strtotime($hour) > strtotime(fomart_hour($dtShift['time_start_lunch_break'])) && strtotime($hour) < strtotime(fomart_hour($dtShift['time_end_lunch_break']))) {
                                        if ($dtShift['check_lunch_break'] == 0){
                                            $hour_real = fomart_hour($dtShift['time_start_lunch_break']);
                                        }
                                    }
                                }
                                $timekeeping_detail_hour_in = get_table_where('tbl_timekeeping_detail_hour', [
                                    'timekeeping_detail_id' => $check_staff['id'],
                                    'type' => 1,
                                    'type_check' => $type_check
                                ], '', 'row_array');
                                $check_new = 1;
                                if (in_array($check_staff['date_word'],$arrDateOvertime)){
                                    $dtPersonnel = get_table_where('tblstaff',
                                        ['staffid' => $check_staff['staff_id']], '', 'row_array');
                                    if ($dtPersonnel['status_overtime'] == 1){
                                        $this->db->select('tbl_suggest_overtime_detail.*');
                                        $this->db->from('tbl_suggest_overtime_detail');
                                        $this->db->join('tbl_suggest_overtime','tbl_suggest_overtime.id = tbl_suggest_overtime_detail.suggest_overtime_id');
                                        $this->db->where('staff_id',$check_staff['staff_id']);
                                        $this->db->where('tbl_suggest_overtime_detail.date',$check_staff['date']);
                                        $this->db->where('tbl_suggest_overtime_detail.status',1);
                                        $checkSuggestOvertime = $this->db->get()->row_array();
                                        if (!empty($checkSuggestOvertime)) {
                                            $hour_overtime = $checkSuggestOvertime['hour_overtime'];
                                            $startTimeCheck = $timekeeping_detail_hour_in['hour_real'];
                                            $endTimeCheck = $hour_real;
                                            $hour_check_new = $hour;
                                            $new_time_check = countHourCheckOut($startTimeCheck, $endTimeCheck);
                                            $checkLunch = false;
                                            if (strtotime($hour_check_new) >= strtotime(fomart_hour($dtShift['time_end_lunch_break']))) {
                                                if ($hour_lunch_break) {
                                                    $new_time_check = countHourCheckOut(countHourCheckOut(fomart_hour($dtShift['time_start_lunch_break']),fomart_hour($dtShift['time_end_lunch_break'])), $new_time_check);
                                                    $checkLunch = true;
                                                }
                                            }
                                            if (countHourCheckOutNew($new_time_check) > $hour_overtime){
                                                $startTimeNew = $check_staff['date'].' '.$startTimeCheck;
                                                $hour_overtime_new = explode('.',$hour_overtime);
                                                if (!empty($hour_overtime_new[1])) {
                                                    $minutes = '0.' . $hour_overtime_new[1];
                                                } else {
                                                    $minutes = 0;
                                                }
                                                $cenvertedTime = date('Y-m-d H:i',strtotime('+'.$hour_overtime_new[0].' hour +'.($minutes * 60).' minutes',strtotime($startTimeNew)));
                                                if ($checkLunch){
                                                    $hour_break = countHourCheckOut(fomart_hour($dtShift['time_start_lunch_break']),fomart_hour($dtShift['time_end_lunch_break']));
                                                    $hour_break = explode(':',$hour_break);
                                                    $cenvertedTime = date('Y-m-d H:i',strtotime('+'.$hour_break[0].' hour +'.$hour_break[1].' minutes',strtotime($cenvertedTime)));
                                                }
                                                $hour_real = date('H:i',strtotime($cenvertedTime));
                                                $hour = $hour_real;
                                            }
                                        } else {
                                            $hour_real = $timekeeping_detail_hour_in['hour_real'];
                                            $check_new = 0;
                                        }
                                    }
                                } else {
                                    $dtPersonnel = get_table_where('tblstaff',
                                        ['staffid' => $check_staff['staff_id']], '', 'row_array');
                                    if ($dtPersonnel['status_overtime'] == 1) {
                                        if (in_array($check_staff['date_word'],$arrDateHalftime)) {
                                            if (strtotime($hour) > strtotime(fomart_hour($arrDateHalftimeHour[$check_staff['date_word']]['time_overtime']))) {
                                                $this->db->select('tbl_suggest_overtime_detail.*');
                                                $this->db->from('tbl_suggest_overtime_detail');
                                                $this->db->join('tbl_suggest_overtime',
                                                    'tbl_suggest_overtime.id = tbl_suggest_overtime_detail.suggest_overtime_id');
                                                $this->db->where('staff_id', $check_staff['staff_id']);
                                                $this->db->where('tbl_suggest_overtime_detail.date',
                                                    $check_staff['date']);
                                                $this->db->where('tbl_suggest_overtime_detail.status', 1);
                                                $checkSuggestOvertime = $this->db->get()->row_array();
                                                if (!empty($checkSuggestOvertime)) {
                                                    $hour_overtime = $checkSuggestOvertime['hour_overtime'];
                                                    $startTimeCheck = fomart_hour($arrDateHalftimeHour[$check_staff['date_word']]['time_overtime']);
                                                    $endTimeCheck = $hour_real;
                                                    $new_time_check = countHourCheckOut($startTimeCheck, $endTimeCheck);
                                                    if (countHourCheckOutNew($new_time_check) > $hour_overtime) {
                                                        $startTimeNewVs1 = $check_staff['date'] . ' ' . $startTimeCheck;
                                                        $hour_overtime_new = explode('.', $hour_overtime);
                                                        if (!empty($hour_overtime_new[1])) {
                                                            $minutes = '0.' . $hour_overtime_new[1];
                                                        } else {
                                                            $minutes = 0;
                                                        }
                                                        $cenvertedTime = date('Y-m-d H:i',
                                                            strtotime('+' . $hour_overtime_new[0] . ' hour +' . ($minutes * 60) . ' minutes',
                                                                strtotime($startTimeNewVs1)));
                                                        $hour_real = date('H:i', strtotime($cenvertedTime));
                                                    }
                                                } else {
                                                    $hour_real = fomart_hour($arrDateHalftimeHour[$check_staff['date_word']]['time_end']);
                                                }
                                            }
                                        } else {
                                            if (strtotime($hour) > strtotime(fomart_hour($dtShift['time_start_overtime']))) {
                                                $this->db->select('tbl_suggest_overtime_detail.*');
                                                $this->db->from('tbl_suggest_overtime_detail');
                                                $this->db->join('tbl_suggest_overtime',
                                                    'tbl_suggest_overtime.id = tbl_suggest_overtime_detail.suggest_overtime_id');
                                                $this->db->where('staff_id', $check_staff['staff_id']);
                                                $this->db->where('tbl_suggest_overtime_detail.date',
                                                    $check_staff['date']);
                                                $this->db->where('tbl_suggest_overtime_detail.status', 1);
                                                $checkSuggestOvertime = $this->db->get()->row_array();
                                                if (!empty($checkSuggestOvertime)) {
                                                    $hour_overtime = $checkSuggestOvertime['hour_overtime'];
                                                    $startTimeCheck = fomart_hour($dtShift['time_start_overtime']);
                                                    $endTimeCheck = $hour_real;
                                                    $new_time_check = countHourCheckOut($startTimeCheck, $endTimeCheck);
                                                    if (countHourCheckOutNew($new_time_check) > $hour_overtime) {
                                                        $startTimeNewVs1 = $check_staff['date'] . ' ' . $startTimeCheck;
                                                        $hour_overtime_new = explode('.', $hour_overtime);
                                                        if (!empty($hour_overtime_new[1])) {
                                                            $minutes = '0.' . $hour_overtime_new[1];
                                                        } else {
                                                            $minutes = 0;
                                                        }
                                                        $cenvertedTime = date('Y-m-d H:i',
                                                            strtotime('+' . $hour_overtime_new[0] . ' hour +' . ($minutes * 60) . ' minutes',
                                                                strtotime($startTimeNewVs1)));
                                                        $hour_real = date('H:i', strtotime($cenvertedTime));
                                                    }
                                                } else {
                                                    $hour_real = fomart_hour($dtShift['time_end']);
                                                }
                                            }
                                        }
                                    }

                                }

                                $hour_real_new = $hour_real;

                                $this->db->where('tbl_timekeeping_detail_hour.id',
                                    $check_staff['id_imekeeping_detail_hour']);
                                $this->db->update('tbl_timekeeping_detail_hour',
                                    [
                                        'hour' => $hour,
                                        'hour_real' => $hour_real,
                                        'image' => $file_name,
                                        'type_check' => $type_check
                                    ]);
                                $timekeeping_detail_hour_in = get_table_where('tbl_timekeeping_detail_hour', [
                                    'timekeeping_detail_id' => $check_staff['id'],
                                    'type' => 1,
                                    'type_check' => $type_check
                                ], '', 'row_array');
                                if (!empty($timekeeping_detail_hour_in)) {
                                    if ($timekeeping_detail_hour_in['hour'] != null) {
                                        $hour_check = $hour;
                                        $startTime = $timekeeping_detail_hour_in['hour_real'];
                                        if (in_array($check_staff['date_word'],$arrDateHalftime)){
                                            if (strtotime($hour) <= strtotime(fomart_hour($arrDateHalftimeHour[$check_staff['date_word']]['time_end']))) {
                                                $endTime = $hour_real;
                                            } else {
                                                $endTime = fomart_hour($arrDateHalftimeHour[$check_staff['date_word']]['time_end']);
                                            }
                                        } else {
                                            if (strtotime($hour) <= strtotime(fomart_hour($dtShift['time_end']))) {
                                                $endTime = $hour_real;
                                            } else {
                                                $endTime = fomart_hour($dtShift['time_end']);
                                            }
                                        }
                                        $new_time = countHourCheckOut($startTime, $endTime);

                                        $timekeeping_detail_count_hour_old = get_table_where('tbl_timekeeping_detail_count_hour',
                                            [
                                                'timekeeping_id' => $check_staff['timekeeping_id'],
                                                'timekeeping_detail_id' => $check_staff['id'],
                                                'staff_id' => $check_staff['staff_id'],
                                                'type_check' => $type_check
                                            ], '', 'row_array');
                                        if (!empty($timekeeping_detail_count_hour_old)) {
                                            $this->db->where('id', $timekeeping_detail_count_hour_old['id']);
                                            $this->db->delete('tbl_timekeeping_detail_count_hour');
                                        }

                                        if (!in_array($check_staff['date_word'],$arrDateHalftime)) {
                                            if (strtotime($hour_check) >= strtotime(fomart_hour($dtShift['time_end_lunch_break']))) {
                                                if ($hour_lunch_break) {
                                                    if ($check_new) {
                                                        $new_time = countHourCheckOut(countHourCheckOut(fomart_hour($dtShift['time_start_lunch_break']),
                                                            fomart_hour($dtShift['time_end_lunch_break'])), $new_time);
                                                    }
                                                }
                                            }
                                        }

                                        $timekeeping_detail_old = get_table_where('tbl_timekeeping_detail',
                                            ['id' => $timekeeping_detail_hour_in['timekeeping_detail_id']], '',
                                            'row_array');
                                        $count_hour = $timekeeping_detail_old['count_hour'];
                                        if ($count_hour == 0) {
                                            $count_hour = '0';
                                        }
                                        if (in_array($check_staff['date_word'],$arrDateOvertime)){
                                            $count_hour = countHourDetail($count_hour, round(countHourCheckOutNew($new_time),1));
                                        } else {
                                            $count_hour = countHourDetail($count_hour, countHourCheckOutNew($new_time));
                                        }

                                        $this->db->insert('tbl_timekeeping_detail_count_hour', [
                                            'timekeeping_id' => $check_staff['timekeeping_id'],
                                            'timekeeping_detail_id' => $check_staff['id'],
                                            'timekeeping_detail_id_old' => 0,
                                            'staff_id' => $check_staff['staff_id'],
                                            'count_hour' => in_array($check_staff['date_word'],$arrDateOvertime) ? round(countHourCheckOutNew($new_time),1) : countHourCheckOutNew($new_time),
                                            'count_hour_late' => 0,
                                            'type_check' => $type_check,
                                        ]);

                                        if (in_array($check_staff['date_word'],$arrDateHalftime)){
                                            $number_day = 0.5;
                                        } else {
                                            if ((strtotime($endTime) <= strtotime(fomart_hour($dtShift['time_end_lunch_break']))) || (strtotime($endTime) <= strtotime(fomart_hour($dtShift['time_end'])) && strtotime($startTime) >= strtotime(fomart_hour($dtShift['time_start_lunch_break'])))) {
                                                $number_day = 0.5;
                                            } else {
                                                $number_day = 1;
                                            }
                                        }

                                        $number_rice = 0;
                                        if (!in_array($check_staff['date_word'],$arrDateHalftime)) {
                                            if (strtotime($hour_real_new) > strtotime(fomart_hour($dtShift['time_rice']))) {
                                                $number_rice = $dtShift['number_rice'];
                                            }
                                        }

                                        $this->db->where('tbl_timekeeping_detail.id',
                                            $timekeeping_detail_hour_in['timekeeping_detail_id']);
                                        $this->db->update('tbl_timekeeping_detail', [
                                            'count_late_new' => $count_late_new,
                                            'count_hour' => $count_hour,
                                            'number_day' => $number_day,
                                            'count_rice' => $number_rice,
                                            'count_hour_late_checkout' => countHourCheckOutNew($hour_late_new)
                                        ]);

                                        if (in_array($check_staff['date_word'],$arrDate) || in_array($check_staff['date_word'],$arrDateHalftime)){
                                            if (in_array($check_staff['date_word'],$arrDateHalftime)){
                                                if (strtotime($hour_real_new) >= strtotime(fomart_hour($arrDateHalftimeHour[$check_staff['date_word']]['time_overtime']))) {
                                                    $hour_check_in_type_3 = fomart_hour($arrDateHalftimeHour[$check_staff['date_word']]['time_overtime']);
                                                    $hour_check_out_type_3 = $hour_real_new;
                                                    $startTime = $hour_check_in_type_3;
                                                    $endTime = $hour_check_out_type_3;


                                                    $new_time = countHourCheckOut($startTime, $endTime);

                                                    $timekeeping_detail_old = get_table_where('tbl_timekeeping_detail',
                                                        ['id' => $check_staff['id']], '', 'row_array');
                                                    $count_hour = $timekeeping_detail_old['count_hour'];
                                                    if ($count_hour == 0) {
                                                        $count_hour = '0';
                                                    }
                                                    $count_hour = countHourDetail($count_hour,
                                                        round(countHourCheckOutNew($new_time), 1));

                                                    $this->db->insert('tbl_timekeeping_detail_count_hour', [
                                                        'timekeeping_id' => $check_staff['timekeeping_id'],
                                                        'timekeeping_detail_id' => $check_staff['id'],
                                                        'timekeeping_detail_id_old' => 0,
                                                        'staff_id' => $check_staff['staff_id'],
                                                        'count_hour' => round(countHourCheckOutNew($new_time), 1),
                                                        'type_check' => 2,
                                                    ]);


                                                    $this->db->where('tbl_timekeeping_detail.id', $check_staff['id']);
                                                    $this->db->update('tbl_timekeeping_detail', [
                                                        'count_hour' => $count_hour,
                                                        'count_hour_overtime' => round(countHourCheckOutNew($new_time),
                                                            1)
                                                    ]);
                                                }
                                            } else {
                                                if (strtotime($hour_real_new) >= strtotime(fomart_hour($dtShift['time_start_overtime']))) {
                                                    $hour_check_in_type_3 = fomart_hour($dtShift['time_start_overtime']);
                                                    $hour_check_out_type_3 = $hour_real_new;
                                                    $startTime = $hour_check_in_type_3;
                                                    $endTime = $hour_check_out_type_3;


                                                    $new_time = countHourCheckOut($startTime, $endTime);

                                                    $timekeeping_detail_old = get_table_where('tbl_timekeeping_detail',
                                                        ['id' => $check_staff['id']], '', 'row_array');
                                                    $count_hour = $timekeeping_detail_old['count_hour'];
                                                    if ($count_hour == 0) {
                                                        $count_hour = '0';
                                                    }
                                                    $count_hour = countHourDetail($count_hour,
                                                        round(countHourCheckOutNew($new_time), 1));

                                                    $this->db->insert('tbl_timekeeping_detail_count_hour', [
                                                        'timekeeping_id' => $check_staff['timekeeping_id'],
                                                        'timekeeping_detail_id' => $check_staff['id'],
                                                        'timekeeping_detail_id_old' => 0,
                                                        'staff_id' => $check_staff['staff_id'],
                                                        'count_hour' => round(countHourCheckOutNew($new_time), 1),
                                                        'type_check' => 2,
                                                    ]);


                                                    $this->db->where('tbl_timekeeping_detail.id', $check_staff['id']);
                                                    $this->db->update('tbl_timekeeping_detail', [
                                                        'count_hour' => $count_hour,
                                                        'count_hour_overtime' => round(countHourCheckOutNew($new_time),
                                                            1)
                                                    ]);
                                                }
                                            }
                                        } else {
                                            if (strtotime($hour_real_new) >= strtotime(fomart_hour($dtShift['time_end']))) {
                                                $hour_check_in_type_3 = fomart_hour($dtShift['time_end']);
                                                $hour_check_out_type_3 = $hour_real_new;
                                                $startTime = $hour_check_in_type_3;
                                                $endTime = $hour_check_out_type_3;


                                                $new_time = countHourCheckOut($startTime, $endTime);

                                                $timekeeping_detail_old = get_table_where('tbl_timekeeping_detail',
                                                    ['id' => $check_staff['id']], '', 'row_array');
                                                $count_hour = $timekeeping_detail_old['count_hour'];
                                                if ($count_hour == 0) {
                                                    $count_hour = '0';
                                                }
                                                $count_hour = countHourDetail($count_hour,
                                                    round(countHourCheckOutNew($new_time), 1));

                                                $this->db->insert('tbl_timekeeping_detail_count_hour', [
                                                    'timekeeping_id' => $check_staff['timekeeping_id'],
                                                    'timekeeping_detail_id' => $check_staff['id'],
                                                    'timekeeping_detail_id_old' => 0,
                                                    'staff_id' => $check_staff['staff_id'],
                                                    'count_hour' => round(countHourCheckOutNew($new_time), 1),
                                                    'type_check' => 2,
                                                ]);

                                                $this->db->where('tbl_timekeeping_detail.id', $check_staff['id']);
                                                $this->db->update('tbl_timekeeping_detail', [
                                                    'count_hour' => $count_hour,
                                                    'count_hour_overtime' => round(countHourCheckOutNew($new_time), 1)
                                                ]);
                                            }
                                        }
                                        $id_insert_checkout = $check_staff['id'];

                                    }
                                }
                            }
                            $id_timekeeping_detail_new = $check_staff['id'];

                            $checkTime = false;

                            $count_hour_detail_2 = '0';

                            $this->db->select('COALESCE(SUM(tbl_timekeeping_detail_count_hour.count_hour),"0")  as count_hour');
                            $this->db->from('tbl_timekeeping_detail_count_hour');
                            $this->db->where('tbl_timekeeping_detail_count_hour.staff_id',
                                $check_staff['staff_id']);
                            $this->db->where('tbl_timekeeping_detail_count_hour.type_check = 2');
                            $this->db->where('tbl_timekeeping_detail_count_hour.timekeeping_detail_id',
                                $id_timekeeping_detail_new);
                            $this->db->group_by('tbl_timekeeping_detail_count_hour.type_check');
                            $count_hour_detail_2 = $this->db->get()->row_array()['count_hour'];
                            if (empty($count_hour_detail_2)) {
                                $count_hour_detail_2 = '0';
                            }

                            $this->db->select('COALESCE(SUM(tbl_timekeeping_detail_count_hour.count_hour),"0")  as count_hour');
                            $this->db->from('tbl_timekeeping_detail_count_hour');
                            $this->db->where('tbl_timekeeping_detail_count_hour.staff_id',
                                $check_staff['staff_id']);
                            $this->db->where('tbl_timekeeping_detail_count_hour.type_check = 1');
                            $this->db->where('tbl_timekeeping_detail_count_hour.timekeeping_detail_id',
                                $id_timekeeping_detail_new);
                            $this->db->group_by('tbl_timekeeping_detail_count_hour.type_check');
                            $count_hour_detail_1 = $this->db->get()->row_array()['count_hour'];
                            if(!empty($count_hour_detail_1)){
                                $checkTime = true;
                            }
                            if (empty($count_hour_detail_1)) {
                                $count_hour_detail_1 = '0';
                            }

                            $business_fee = 3;
                            $tangCaHour = 0;
                            $dtTimekeepingDetail = get_table_where('tbl_timekeeping_detail',['id' => $check_staff['id']],'','row_array');
                            if (!empty($hour_real_new) || (in_array($check_staff['date_word'],$arrDateOvertime))) {
                                if ((strtotime($hour_real_new) > strtotime(fomart_hour($dtShift['time_start_overtime']))) || (in_array($check_staff['date_word'],$arrDateOvertime)) || (in_array($check_staff['date_word'],$arrDateHalftime) && strtotime($hour_real_new) > strtotime(fomart_hour($arrDateHalftimeHour[$check_staff['date_word']]['time_overtime'])))) {

                                    if (in_array($check_staff['date_word'],$arrDateOvertime)){
                                        $timekeeping_detail_hour_in = get_table_where('tbl_timekeeping_detail_hour', [
                                            'timekeeping_detail_id' => $check_staff['id'],
                                            'type' => 1,
                                            'type_check' => $type_check
                                        ], '', 'row_array');

                                        $hour_start = $timekeeping_detail_hour_in['hour_real'];
                                    } else {
                                        if (in_array($check_staff['date_word'],$arrDateHalftime)){
                                            $hour_start = (fomart_hour($arrDateHalftimeHour[$check_staff['date_word']]['time_overtime']));
                                        } else {
                                            $hour_start = (fomart_hour($dtShift['time_start_overtime']));
                                        }
                                    }

                                    $hour_end = $hour;

                                    $this->db->select('tbl_business_fee_boiler_overtime.id as id');
                                    $this->db->from('tbl_business_fee_boiler_overtime');
                                    $this->db->where('staff_id', $check_staff['staff_id']);
                                    $this->db->where('month', $month);
                                    $this->db->where('year', $year);
                                    $this->db->where('type', $business_fee);
                                    $checkBusinessFee = $this->db->get()->row_array();
                                    $day_new = date("Y-m-d", strtotime($date_check));
                                    if (!empty($checkBusinessFee)) {
                                        $this->db->select('tbl_business_fee_boiler_overtime_detail.id as id');
                                        $this->db->from('tbl_business_fee_boiler_overtime_detail');
                                        $this->db->where('tbl_business_fee_boiler_overtime_detail.business_fee_boiler_overtime_id',
                                            $checkBusinessFee['id']);
                                        $this->db->where('tbl_business_fee_boiler_overtime_detail.date', $day_new);
                                        $checkBusinessFeeDetail = $this->db->get()->row_array();

                                        $dtPersonnel = get_table_where('tblstaff',
                                            ['staffid' => $check_staff['staff_id']], '', 'row_array');
                                        if ($dtPersonnel['status_overtime'] == 1){
                                            $this->db->select('tbl_suggest_overtime_detail.*');
                                            $this->db->from('tbl_suggest_overtime_detail');
                                            $this->db->join('tbl_suggest_overtime','tbl_suggest_overtime.id = tbl_suggest_overtime_detail.suggest_overtime_id');
                                            $this->db->where('staff_id',$check_staff['staff_id']);
                                            $this->db->where('tbl_suggest_overtime_detail.date',$check_staff['date']);
                                            $this->db->where('tbl_suggest_overtime_detail.status',1);
                                            $checkSuggestOvertime = $this->db->get()->row_array();
                                            if (!empty($checkSuggestOvertime)){
                                                $hour_overtime = $checkSuggestOvertime['hour_overtime'];
                                                if (in_array($check_staff['date_word'],$arrDate) || in_array($check_staff['date_word'],$arrDateHalftime)) {
                                                    if ($count_hour_detail_2 <= $hour_overtime){
                                                        $count_hour_detail_2 = $count_hour_detail_2;
                                                    } else {
                                                        $startTime = $check_staff['date'].' '.$hour_start;
                                                        $hour_overtime_new = explode('.',$hour_overtime);
                                                        if (!empty($hour_overtime_new[1])) {
                                                            $minutes = '0.' . $hour_overtime_new[1];
                                                        } else {
                                                            $minutes = 0;
                                                        }
                                                        $cenvertedTime = date('Y-m-d H:i',strtotime('+'.$hour_overtime_new[0].' hour +'.($minutes * 60).' minutes',strtotime($startTime)));
                                                        $count_hour_detail_2 = $hour_overtime;
                                                        $hour_end = date('H:i',strtotime($cenvertedTime));
                                                    }
                                                    $weekday = $count_hour_detail_2;
                                                    $sunday = 0;
                                                } else {
                                                    $checkLunch = false;
                                                    if (strtotime($hour_real_new) >= strtotime(fomart_hour($dtShift['time_end_lunch_break']))) {
                                                        if ($hour_lunch_break) {
                                                            $checkLunch = true;
                                                        }
                                                    }
                                                    $count_hour_detail_new = countHourDetail($count_hour_detail_1, $count_hour_detail_2);
                                                    if ($count_hour_detail_new <= $hour_overtime){
                                                        $count_hour_detail_new = $count_hour_detail_new;
                                                    } else {
                                                        $startTime = $check_staff['date'].' '.$hour_start;
                                                        $hour_overtime_new = explode('.',$hour_overtime);
                                                        if (!empty($hour_overtime_new[1])) {
                                                            $minutes = '0.' . $hour_overtime_new[1];
                                                        } else {
                                                            $minutes = 0;
                                                        }
                                                        $cenvertedTime = date('Y-m-d H:i',strtotime('+'.$hour_overtime_new[0].' hour +'.($minutes * 60).' minutes',strtotime($startTime)));
                                                        if ($checkLunch){
                                                            $hour_break = countHourCheckOut(fomart_hour($dtShift['time_start_lunch_break']),fomart_hour($dtShift['time_end_lunch_break']));
                                                            $hour_break = explode(':',$hour_break);
                                                            $cenvertedTime = date('Y-m-d H:i',strtotime('+'.$hour_break[0].' hour +'.$hour_break[1].' minutes',strtotime($cenvertedTime)));
                                                        }
                                                        $count_hour_detail_new = $hour_overtime;
                                                        $hour_end = date('H:i',strtotime($cenvertedTime));
                                                    }
                                                    $weekday = 0;
                                                    $sunday  = $count_hour_detail_new ;
                                                    $tangCaHour = $sunday;
                                                }
                                                if (!empty($checkBusinessFeeDetail)) {
                                                    $this->db->where('id', $checkBusinessFeeDetail['id']);
                                                    $this->db->update('tbl_business_fee_boiler_overtime_detail', [
                                                        'hour_start' =>  $hour_start,
                                                        'hour_end' => $hour_end,
                                                        'weekday' => $weekday,
                                                        'sunday' => $sunday,
                                                        'suggest_overtime_id' => $checkSuggestOvertime['suggest_overtime_id'],
                                                        'suggest_overtime_detail_id' => $checkSuggestOvertime['id'],
                                                    ]);
                                                } else {
                                                    $this->db->insert('tbl_business_fee_boiler_overtime_detail', [
                                                        'business_fee_boiler_overtime_id' => $checkBusinessFee['id'],
                                                        'date' => $day_new,
                                                        'type' => 2,
                                                        'hour_start' => $hour_start,
                                                        'hour_end' => $hour_end,
                                                        'weekday' => $weekday,
                                                        'sunday' => $sunday,
                                                        'suggest_overtime_id' => $checkSuggestOvertime['suggest_overtime_id'],
                                                        'suggest_overtime_detail_id' => $checkSuggestOvertime['id'],
                                                    ]);
                                                }
                                            }
                                        } else {
                                            if (in_array($check_staff['date_word'],$arrDate) || in_array($check_staff['date_word'],$arrDateHalftime)) {
                                                $weekday = $count_hour_detail_2;
                                                $sunday = 0;
                                            } else {
                                                $weekday = 0;
                                                $sunday  = countHourDetail($count_hour_detail_1, $count_hour_detail_2);
                                                $tangCaHour = $sunday;
                                            }
                                            if (!empty($checkBusinessFeeDetail)) {
                                                $this->db->where('id', $checkBusinessFeeDetail['id']);
                                                $this->db->update('tbl_business_fee_boiler_overtime_detail', [
                                                    'hour_start' => $hour_start,
                                                    'hour_end' => $hour_end,
                                                    'weekday' => $weekday,
                                                    'sunday' => $sunday,
                                                ]);
                                            } else {
                                                $this->db->insert('tbl_business_fee_boiler_overtime_detail', [
                                                    'business_fee_boiler_overtime_id' => $checkBusinessFee['id'],
                                                    'date' => $day_new,
                                                    'type' => 2,
                                                    'hour_start' => $hour_start,
                                                    'hour_end' => $hour_end,
                                                    'weekday' => $weekday,
                                                    'sunday' => $sunday,
                                                ]);
                                            }
                                        }
                                    } else {
                                        $name_text = get_table_where('tblstaff',
                                            ['staffid' => $check_staff['staff_id']], '', 'row_array');
                                        $status_overtime = $name_text['status_overtime'];
                                        if ($status_overtime == 1){
                                            $this->db->select('tbl_suggest_overtime_detail.*');
                                            $this->db->from('tbl_suggest_overtime_detail');
                                            $this->db->join('tbl_suggest_overtime','tbl_suggest_overtime.id = tbl_suggest_overtime_detail.suggest_overtime_id');
                                            $this->db->where('staff_id',$check_staff['staff_id']);
                                            $this->db->where('tbl_suggest_overtime_detail.date',$check_staff['date']);
                                            $this->db->where('tbl_suggest_overtime_detail.status',1);
                                            $checkSuggestOvertime = $this->db->get()->row_array();
                                            if (!empty($checkSuggestOvertime)){
                                                $hour_overtime = $checkSuggestOvertime['hour_overtime'];
                                                $this->db->insert('tbl_business_fee_boiler_overtime', [
                                                    'name' => $name_text['firstname'] . ' ' . $name_text['lastname'],
                                                    'month' => $month,
                                                    'year' => $year,
                                                    'staff_id' => $check_staff['staff_id'],
                                                    'date_created' => date('Y-m-d H:i:s'),
                                                    'created_by' => 1,
                                                    'type' => $business_fee,
                                                ]);
                                                $id_insert = $this->db->insert_id();
                                                if ($id_insert) {
                                                    if (in_array($check_staff['date_word'],$arrDate) || in_array($check_staff['date_word'],$arrDateHalftime)) {
                                                        if ($count_hour_detail_2 <= $hour_overtime){
                                                            $count_hour_detail_2 = $count_hour_detail_2;
                                                        } else {
                                                            $startTime = $check_staff['date'].' '.$hour_start;
                                                            $hour_overtime_new = explode('.',$hour_overtime);
                                                            if (!empty($hour_overtime_new[1])) {
                                                                $minutes = '0.' . $hour_overtime_new[1];
                                                            } else {
                                                                $minutes = 0;
                                                            }
                                                            $cenvertedTime = date('Y-m-d H:i',strtotime('+'.$hour_overtime_new[0].' hour +'.($minutes * 60).' minutes',strtotime($startTime)));
                                                            $count_hour_detail_2 = $hour_overtime;
                                                            $hour_end = date('H:i',strtotime($cenvertedTime));
                                                        }
                                                        $weekday = $count_hour_detail_2;
                                                        $sunday = 0;
                                                    } else {
                                                        $checkLunch = false;
                                                        if (strtotime($hour_real_new) >= strtotime(fomart_hour($dtShift['time_end_lunch_break']))) {
                                                            if ($hour_lunch_break) {
                                                                $checkLunch = true;
                                                            }
                                                        }
                                                        $count_hour_detail_new = countHourDetail($count_hour_detail_1, $count_hour_detail_2);
                                                        if ($count_hour_detail_new <= $hour_overtime){
                                                            $count_hour_detail_new = $count_hour_detail_new;
                                                        } else {
                                                            $startTime = $check_staff['date'].' '.$hour_start;
                                                            $hour_overtime_new = explode('.',$hour_overtime);
                                                            if (!empty($hour_overtime_new[1])) {
                                                                $minutes = '0.' . $hour_overtime_new[1];
                                                            } else {
                                                                $minutes = 0;
                                                            }
                                                            $cenvertedTime = date('Y-m-d H:i',strtotime('+'.$hour_overtime_new[0].' hour +'.($minutes * 60).' minutes',strtotime($startTime)));
                                                            if ($checkLunch){
                                                                $hour_break = countHourCheckOut(fomart_hour($dtShift['time_start_lunch_break']),fomart_hour($dtShift['time_end_lunch_break']));
                                                                $hour_break = explode(':',$hour_break);
                                                                $cenvertedTime = date('Y-m-d H:i',strtotime('+'.$hour_break[0].' hour +'.$hour_break[1].' minutes',strtotime($cenvertedTime)));
                                                            }
                                                            $count_hour_detail_new = $hour_overtime;
                                                            $hour_end = date('H:i',strtotime($cenvertedTime));
                                                        }
                                                        $weekday = 0;
                                                        $sunday  = $count_hour_detail_new;
                                                        $tangCaHour = $sunday;
                                                    }
                                                    $this->db->insert('tbl_business_fee_boiler_overtime_detail', [
                                                        'business_fee_boiler_overtime_id' => $id_insert,
                                                        'date' => $day_new,
                                                        'type' => 2,
                                                        'hour_start' => $hour_start,
                                                        'hour_end' => $hour_end,
                                                        'weekday' => $weekday,
                                                        'sunday' => $sunday,
                                                        'suggest_overtime_id' => $checkSuggestOvertime['suggest_overtime_id'],
                                                        'suggest_overtime_detail_id' => $checkSuggestOvertime['id'],
                                                    ]);
                                                }
                                            }
                                        } else {
                                            $this->db->insert('tbl_business_fee_boiler_overtime', [
                                                'name' => $name_text['firstname'] . ' ' . $name_text['lastname'],
                                                'month' => $month,
                                                'year' => $year,
                                                'staff_id' => $check_staff['staff_id'],
                                                'date_created' => date('Y-m-d H:i:s'),
                                                'created_by' => 1,
                                                'type' => $business_fee,
                                            ]);
                                            $id_insert = $this->db->insert_id();
                                            if ($id_insert) {
                                                if (in_array($check_staff['date_word'],$arrDate) || in_array($check_staff['date_word'],$arrDateHalftime)) {
                                                    $weekday = $count_hour_detail_2;
                                                    $sunday = 0;
                                                } else {
                                                    $weekday = 0;
                                                    $sunday  = countHourDetail($count_hour_detail_1, $count_hour_detail_2);
                                                    $tangCaHour = $sunday;
                                                }
                                                $this->db->insert('tbl_business_fee_boiler_overtime_detail', [
                                                    'business_fee_boiler_overtime_id' => $id_insert,
                                                    'date' => $day_new,
                                                    'type' => 2,
                                                    'hour_start' => $hour_start,
                                                    'hour_end' => $hour_end,
                                                    'weekday' => $weekday,
                                                    'sunday' => $sunday,
                                                ]);
                                            }
                                        }
                                    }
                                }
                            }



                            $data_count_hour = [];
                            $date = '';
                            $count_hour_data = '0';
                            $timekeeping_detail_new = get_table_where('tbl_timekeeping_detail',
                                ['id' => $id_timekeeping_detail_new], '', 'row_array');
                            if (!empty($timekeeping_detail_new)) {
                                $date = $timekeeping_detail_new['date'];
                            }
                            $count_hour_data_new = countHourDetail(0, $count_hour_detail_1);

                            $count_hour_data = countHourDetail(0, $count_hour_detail_2);
                            $numberDay = 0;

                            if (!empty($timekeeping_detail_new)) {
                                $numberDay = $timekeeping_detail_new['number_day'];
                            }

                            $dtPersonnel = get_table_where('tblstaff',
                                ['staffid' => $check_staff['staff_id']], '', 'row_array');
                            if ($dtPersonnel['status_overtime'] == 1){
                                $tangCa = $tangCaHour;
                            } else {
                                $tangCa = countHourDetail($count_hour_detail_1, $count_hour_detail_2);
                            }

                            if (in_array($check_staff['date_word'],$arrDate) || in_array($check_staff['date_word'],$arrDateHalftime)) {
                                $data_count_hour = [
                                    'Ngày' => $date,
                                    'Số giờ' => $count_hour_data_new,
                                    'Số giờ tăng ca' => $count_hour_data,
                                ];
                            } else {
                                $data_count_hour = [
                                    'Ngày' => $date,
                                    'Số giờ' => 0,
                                    'Số giờ tăng ca' => $tangCa,
                                ];
                            }

                            $staff = get_table_where('tblstaff', ['staffid' => $check_staff['staff_id']], '',
                                'row_array');
                            $data['success'] = true;
                            $data['type_check_hour'] = 2;
                            $data['staff_name'] = $staff['firstname'] . ' ' . $staff['lastname'];
                            $data['id'] = $id_insert_checkout;
                            $data['time'] = $date_check;
                            $data['data_count_hour'] = $data_count_hour;
                            $data['message'] = 'Check out thành công';

                        }
                    }
                }
            }
        }

        echo json_encode($data);
    }
}