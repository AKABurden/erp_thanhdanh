<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Carbon\Carbon;

class Paid_holidays extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->perViewPaidHoliday = has_permission('paid_holidays', '', 'view');
        $this->perAddPaidHoliday = has_permission('paid_holidays', '', 'create');
        $this->perEditPaidHoliday = has_permission('paid_holidays', '', 'edit');
        $this->perDeletePaidHoliday = has_permission('paid_holidays', '', 'delete');
        $this->perApprovePaidHoliday = has_permission('paid_holidays', '', 'approve');
        $this->isAdmin = is_admin();
    }

    public function paid_holiday_leave()
    {
        if (!$this->perViewPaidHoliday){
            access_denied();
        }
        $data = [];
        $data['staff'] = getPersonDeparmentdt(0);
        $data['title'] = lang('Đơn xin nghỉ phép');
        $this->load->view('admin/paid_holidays/paid_holiday_leave', $data);
    }

    public function add_paid_holiday_leave($id = '')
    {
        $data = [];

        if ($this->input->post()) {
            $dataPost = $this->input->post();

            $name = $dataPost['name'];
            $staff_id = $dataPost['staff_id'];
            $staff_agree = $dataPost['staff_agree'];
            $staff_id_replace = $dataPost['staff_id_replace'];
            $pm = $dataPost['pm'];
            $items = [];
            $total_date_phep = 0;
            $year_search = date('Y');
            $year_search_old = date('Y') - 1;
            if ($id == '') {
                if (!$this->perAddPaidHoliday){
                    $data['result'] = 0;
                    $data['message'] = 'Không có quyền tạo !';
                    echo json_encode($data);
                    die();
                }
                $option = [
                    'name' => $name,
                    'staff_id' => $staff_id,
                    'staff_agree' => $staff_agree,
                    'staff_id_replace' => $staff_id_replace,
                    'status' => 0,
                    'created_by' => get_staff_user_id(),
                    'date_created' => date('Y-m-d H:i:s'),
                ];

                if (!empty($pm)) {
                    foreach ($pm as $key => $value) {
                        $conter = $value['conter'];
                        $number_day = number_unformat($value['number_day']);
                        $sub = [];
                        $month_sub = !empty($this->input->post('month_sub')[$conter]) ? $this->input->post('month_sub')[$conter] : false;
                        $total_quantity_sub = 0;
                        if (!empty($month_sub)) {
                            foreach ($month_sub as $k => $val) {
                                if (empty($val)) {
                                    continue;
                                }
                                $quantity_sub = $this->input->post('quantity_sub')[$conter][$k];
                                $sub[] = [
                                    'month' => $val,
                                    'number_day' => $quantity_sub,
                                ];

                                $total_quantity_sub += $quantity_sub;
                            }
                        }
                        if ($total_quantity_sub <= 0) {
                            $data['result'] = 0;
                            $data['message'] = 'Vui lòng nhập số ngày nghỉ !';
                            echo json_encode($data);
                            die();
                        }
                        if ($value['type_magic'] == 1){
                            $total_date_phep += $total_quantity_sub;
                        }
                        $items[] = [
                            'type_magic_id' => $value['type_magic'],
                            'date_start' => to_sql_date($value['date_start']),
                            'date_end' => to_sql_date($value['date_end']),
                            'number_date' => $total_quantity_sub,
                            'day_work' => to_sql_date($value['day_work']),
                            'note' => $value['note'],
                            'sub' => $sub
                        ];
                    }
                }

                if (empty($items)) {
                    $data['result'] = 0;
                    $data['message'] = 'Không có dữ liệu chi tiết';
                    echo json_encode($data);
                    die();
                }

                $this->db->select('tbl_setup_paid_holiday_staff.number_day,number_day_now,number_day_old');
                $this->db->from('tbl_setup_paid_holiday');
                $this->db->join('tbl_setup_paid_holiday_staff','tbl_setup_paid_holiday_staff.id_setup_paid_holiday = tbl_setup_paid_holiday.id');
                $this->db->where('tbl_setup_paid_holiday.year',$year_search);
                $this->db->where('tbl_setup_paid_holiday_staff.staff_id',$staff_id);
                $paid_year = $this->db->get()->row_array();

                $this->db->select('tbl_setup_paid_holiday_staff.number_day');
                $this->db->from('tbl_setup_paid_holiday');
                $this->db->join('tbl_setup_paid_holiday_staff','tbl_setup_paid_holiday_staff.id_setup_paid_holiday = tbl_setup_paid_holiday.id');
                $this->db->where('tbl_setup_paid_holiday.year',$year_search_old);
                $this->db->where('tbl_setup_paid_holiday_staff.staff_id',$staff_id);
                $paid_year_old = $this->db->get()->row_array();

                $this->db->select('COUNT(tbl_timekeeping_detail.id) as number_date');
                $this->db->from('tbl_paid_holiday_leave_detail');
                $this->db->join('tbl_paid_holiday_leave',
                    'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
                $this->db->join('tbl_paid_holiday_leave_detail_month',
                    'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
                $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 1);
                $this->db->where('tbl_paid_holiday_leave.staff_id', $staff_id);
                $this->db->where('tbl_timekeeping_detail.type', 'AL');
                $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search");
                $number_date = $this->db->get()->row_array();

                $this->db->select('COUNT(tbl_timekeeping_detail.id) as number_date');
                $this->db->from('tbl_paid_holiday_leave_detail');
                $this->db->join('tbl_paid_holiday_leave',
                    'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
                $this->db->join('tbl_paid_holiday_leave_detail_month',
                    'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
                $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 1);
                $this->db->where('tbl_paid_holiday_leave.staff_id', $staff_id);
                $this->db->where('tbl_timekeeping_detail.type', 'AL/2');
                $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search");
                $number_date_new = $this->db->get()->row_array();

                $number_date_phep = $number_date['number_date'] + ($number_date_new['number_date'] * 0.5);


                $this->db->select('COUNT(tbl_timekeeping_detail.id) as number_date');
                $this->db->from('tbl_paid_holiday_leave_detail');
                $this->db->join('tbl_paid_holiday_leave',
                    'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
                $this->db->join('tbl_paid_holiday_leave_detail_month',
                    'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
                $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 1);
                $this->db->where('tbl_paid_holiday_leave.staff_id', $staff_id);
                $this->db->where('tbl_timekeeping_detail.type', 'AL');
                $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search_old AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search_old");
                $number_date_old = $this->db->get()->row_array();

                $this->db->select('COUNT(tbl_timekeeping_detail.id) as number_date');
                $this->db->from('tbl_paid_holiday_leave_detail');
                $this->db->join('tbl_paid_holiday_leave',
                    'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
                $this->db->join('tbl_paid_holiday_leave_detail_month',
                    'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
                $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 1);
                $this->db->where('tbl_paid_holiday_leave.staff_id', $staff_id);
                $this->db->where('tbl_timekeeping_detail.type', 'AL/2');
                $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search_old AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search_old");
                $number_date_new_old = $this->db->get()->row_array();

                $number_date_phep_old = $number_date_old['number_date'] + ($number_date_new_old['number_date'] * 0.5);


                $month = 3;
                $day = 31;
                $date_check = date('Y-'.$month.'-'.$day.'');
                $number_day_old = !empty($paid_year['number_day_old']) ? $paid_year['number_day_old'] : 0;
                if (strtotime(date('Y-m-d')) > strtotime($date_check)){
                    $number_date_phep_old = 0;
                }
                $number_date_phep = (!empty($paid_year) && !empty($paid_year['number_day_now'] + $number_day_old ) ? ($paid_year['number_day_now'] + $number_day_old) - $number_date_phep : 0);
                $number_date_phep = $number_date_phep < 0 ? 0 : $number_date_phep;
                if ($total_date_phep > ($number_date_phep)){
                    $data['result'] = 0;
                    $data['message'] = 'Số lượng phép năm không đủ';
                    echo json_encode($data);
                    die();
                }


                $this->db->insert('tbl_paid_holiday_leave', $option);
                $id_insert = $this->db->insert_id();
                if ($id_insert) {
                    foreach ($items as $key => $value) {
                        $value['paid_holiday_leave_id'] = $id_insert;
                        $sub = $value['sub'];
                        unset($value['sub']);
                        $this->db->insert('tbl_paid_holiday_leave_detail', $value);
                        $id_insert_detail = $this->db->insert_id();
                        if ($id_insert_detail) {
                            if (!empty($sub)) {
                                foreach ($sub as $k => $v) {
                                    $v['paid_holiday_leave_detail_id'] = $id_insert_detail;
                                    $this->db->insert('tbl_paid_holiday_leave_detail_month', $v);
                                }
                            }
                        }
                    }
                    $get_code = get_table_where('tbl_paid_holiday_leave', array('id' => $id_insert), '', 'row');
                    activity_log_v2('paid_holiday_leave', 'tbl_paid_holiday_leave', $id_insert, $get_code->name,
                        'Thêm đơn xin nghỉ phép [' . $get_code->name . ']');
                    notificationAddPaidHoliday($id_insert,get_staff_user_id());
                    $this->SendEmailNoti($staff_agree, $id_insert);
                    $data['result'] = 1;
                    $data['message'] = 'Thêm thành công';
                } else {
                    $data['result'] = 0;
                    $data['message'] = 'Thêm thất bại';
                }
            } else {
                if (!$this->perEditPaidHoliday){
                    $data['result'] = 0;
                    $data['message'] = 'Không có quyền tạo !';
                    echo json_encode($data);
                    die();
                }
                $this->db->where('paid_holiday_leave_id', $id);
                $this->db->group_start();
                $this->db->where('status', 1);
                $this->db->or_where('status', 2);
                $this->db->group_end();
                $checkPaid = $this->db->get('tbl_paid_holiday_leave_detail')->row_array();

                if (!empty($checkPaid)) {
                    $data['result'] = 0;
                    $data['message'] = 'Có chi tiết đơn xin phép đã được duyệt không thể sửa';
                    echo json_encode($data);
                    die();
                }
                $option = [
                    'name' => $name,
                    'staff_id' => $staff_id,
                    'staff_agree' => $staff_agree,
                    'staff_id_replace' => $staff_id_replace,
                ];

                if (!empty($pm)) {
                    foreach ($pm as $key => $value) {
                        $conter = $value['conter'];
                        $number_day = number_unformat($value['number_day']);
                        $sub = [];
                        $month_sub = !empty($this->input->post('month_sub')[$conter]) ? $this->input->post('month_sub')[$conter] : false;
                        $total_quantity_sub = 0;
                        if (!empty($month_sub)) {
                            foreach ($month_sub as $k => $val) {
                                if (empty($val)) {
                                    continue;
                                }
                                $quantity_sub = $this->input->post('quantity_sub')[$conter][$k];
                                $sub[] = [
                                    'month' => $val,
                                    'number_day' => $quantity_sub,
                                ];

                                $total_quantity_sub += $quantity_sub;
                            }
                        }
                        if ($total_quantity_sub <= 0) {
                            $data['result'] = 0;
                            $data['message'] = 'Vui lòng nhập số ngày nghỉ !';
                            echo json_encode($data);
                            die();
                        }
                        if ($value['type_magic'] == 1){
                            $total_date_phep += $total_quantity_sub;
                        }
                        $items[] = [
                            'id' => !empty($value['id']) ? $value['id'] : 0,
                            'type_magic_id' => $value['type_magic'],
                            'date_start' => to_sql_date($value['date_start']),
                            'date_end' => to_sql_date($value['date_end']),
                            'number_date' => $total_quantity_sub,
                            'day_work' => to_sql_date($value['day_work']),
                            'note' => $value['note'],
                            'sub' => $sub
                        ];
                    }
                }
                if (empty($items)) {
                    $data['result'] = 0;
                    $data['message'] = 'Không có dữ liệu chi tiết';
                    echo json_encode($data);
                    die();
                }

                $this->db->select('tbl_setup_paid_holiday_staff.number_day');
                $this->db->from('tbl_setup_paid_holiday');
                $this->db->join('tbl_setup_paid_holiday_staff','tbl_setup_paid_holiday_staff.id_setup_paid_holiday = tbl_setup_paid_holiday.id');
                $this->db->where('tbl_setup_paid_holiday.year',$year_search);
                $this->db->where('tbl_setup_paid_holiday_staff.staff_id',$staff_id);
                $paid_year = $this->db->get()->row_array();

                $this->db->select('tbl_setup_paid_holiday_staff.number_day');
                $this->db->from('tbl_setup_paid_holiday');
                $this->db->join('tbl_setup_paid_holiday_staff','tbl_setup_paid_holiday_staff.id_setup_paid_holiday = tbl_setup_paid_holiday.id');
                $this->db->where('tbl_setup_paid_holiday.year',$year_search_old);
                $this->db->where('tbl_setup_paid_holiday_staff.staff_id',$staff_id);
                $paid_year_old = $this->db->get()->row_array();

                $this->db->select('COUNT(tbl_timekeeping_detail.id) as number_date');
                $this->db->from('tbl_paid_holiday_leave_detail');
                $this->db->join('tbl_paid_holiday_leave',
                    'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
                $this->db->join('tbl_paid_holiday_leave_detail_month',
                    'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
                $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 1);
                $this->db->where('tbl_paid_holiday_leave.staff_id', $staff_id);
                $this->db->where('tbl_timekeeping_detail.type', 'AL');
                $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search");
                $number_date = $this->db->get()->row_array();

                $this->db->select('COUNT(tbl_timekeeping_detail.id) as number_date');
                $this->db->from('tbl_paid_holiday_leave_detail');
                $this->db->join('tbl_paid_holiday_leave',
                    'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
                $this->db->join('tbl_paid_holiday_leave_detail_month',
                    'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
                $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 1);
                $this->db->where('tbl_paid_holiday_leave.staff_id', $staff_id);
                $this->db->where('tbl_timekeeping_detail.type', 'AL/2');
                $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search");
                $number_date_new = $this->db->get()->row_array();

                $number_date_phep = $number_date['number_date'] + ($number_date_new['number_date'] * 0.5);

                $number_date_phep = (!empty($paid_year) && !empty($paid_year['number_day'])) ? $paid_year['number_day'] - $number_date_phep : 0;
                $number_date_phep = $number_date_phep < 0 ? 0 : $number_date_phep;

                $this->db->select('COUNT(tbl_timekeeping_detail.id) as number_date');
                $this->db->from('tbl_paid_holiday_leave_detail');
                $this->db->join('tbl_paid_holiday_leave',
                    'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
                $this->db->join('tbl_paid_holiday_leave_detail_month',
                    'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
                $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 1);
                $this->db->where('tbl_paid_holiday_leave.staff_id', $staff_id);
                $this->db->where('tbl_timekeeping_detail.type', 'AL');
                $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search_old AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search_old");
                $number_date_old = $this->db->get()->row_array();

                $this->db->select('COUNT(tbl_timekeeping_detail.id) as number_date');
                $this->db->from('tbl_paid_holiday_leave_detail');
                $this->db->join('tbl_paid_holiday_leave',
                    'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
                $this->db->join('tbl_paid_holiday_leave_detail_month',
                    'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
                $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 1);
                $this->db->where('tbl_paid_holiday_leave.staff_id', $staff_id);
                $this->db->where('tbl_timekeeping_detail.type', 'AL/2');
                $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search_old AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search_old");
                $number_date_new_old = $this->db->get()->row_array();

                $number_date_phep_old = $number_date_old['number_date'] + ($number_date_new_old['number_date'] * 0.5);

                $number_date_phep_old = (!empty($paid_year_old) && !empty($paid_year_old['number_day'])) ? $paid_year_old['number_day'] - $number_date_phep_old : 0;
                $number_date_phep_old = $number_date_phep_old < 0 ? 0 : $number_date_phep_old;

                $month = 3;
                $day = 31;
                $date_check = date('Y-'.$month.'-'.$day.'');
                if (strtotime(date('Y-m-d')) > strtotime($date_check)){
                    $number_date_phep_old = 0;
                }

                if ($total_date_phep > ($number_date_phep + $number_date_phep_old)){
                    $data['result'] = 0;
                    $data['message'] = 'Số lượng phép năm không đủ';
                    echo json_encode($data);
                    die();
                }

                $this->db->where('id', $id);
                $success = $this->db->update('tbl_paid_holiday_leave', $option);
                if ($success) {
                    $arrId = [];
                    $itemsOld = get_table_where('tbl_paid_holiday_leave_detail', ['paid_holiday_leave_id' => $id]);
                    if (!empty($itemsOld)) {
                        foreach ($itemsOld as $key => $value) {
                            $this->db->where('paid_holiday_leave_detail_id', $value['id']);
                            $this->db->delete('tbl_paid_holiday_leave_detail_month');
                        }
                    }
                    foreach ($items as $key => $value) {
                        $checkExisit = get_table_where('tbl_paid_holiday_leave_detail', ['id' => $value['id']], '',
                            'row_array');
                        if (!empty($checkExisit)) {
                            $arrId[] = $checkExisit['id'];
                            $sub = $value['sub'];
                            unset($value['sub']);
                            $this->db->where('id', $value['id']);
                            $this->db->update('tbl_paid_holiday_leave_detail', $value);
                            if (!empty($sub)) {
                                foreach ($sub as $k => $v) {
                                    $v['paid_holiday_leave_detail_id'] = $value['id'];
                                    $this->db->insert('tbl_paid_holiday_leave_detail_month', $v);
                                }
                            }
                        } else {
                            $value['paid_holiday_leave_id'] = $id;
                            $sub = $value['sub'];
                            unset($value['sub']);
                            $this->db->insert('tbl_paid_holiday_leave_detail', $value);
                            $insert_id_item = $this->db->insert_id();
                            if ($insert_id_item) {
                                if (!empty($sub)) {
                                    foreach ($sub as $k => $v) {
                                        $v['paid_holiday_leave_detail_id'] = $insert_id_item;
                                        $this->db->insert('tbl_paid_holiday_leave_detail_month', $v);
                                    }
                                }
                            }
                            $arrId[] = $insert_id_item;
                        }
                    }

                    if (empty($arrId)) {
                        $this->db->where('paid_holiday_leave_id', $id);
                        $this->db->delete('tbl_paid_holiday_leave_detail');
                    } else {
                        $this->db->where('paid_holiday_leave_id', $id);
                        $this->db->where_not_in('id', $arrId);
                        $this->db->delete('tbl_paid_holiday_leave_detail');
                    }

                    $get_code = get_table_where('tbl_paid_holiday_leave', array('id' => $id), '', 'row');
                    activity_log_v2('edit_paid_holiday_leave', 'tbl_paid_holiday_leave', $id, $get_code->name,
                        'Sửa đơn xin nghỉ phép [' . $get_code->name . ']');
                    $this->SendEmailNoti($staff_agree, $id);
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

        
        $data['typeMagic'] = get_table_where('tbl_type_magic', [], 'id ASC', 'result_array');
        if (!empty($id)) {
            $year = date('Y');
            $month = date('m');
            $startdate = date('' . $year . '-' . $month . '-01');
            $newdate = date('' . $year . '-' . $month . '-t');
            $allDate = createDateRangeArray($month,$year);
            $allDateNew = [];
            foreach ($allDate as $key => $value){
                $timestamp = strtotime($value);
                $day = date('D', $timestamp);
                $month = date('M', $timestamp);
                $date = date('d', $timestamp);
                $allDateNew[$key]= [
                    'date' => _dhau($value),
                    'day' => $day,
                    'month' => $month,
                    'date_new' => $date,
                ];
            }
            $data['allDateNew'] = $allDateNew;
            $data['id'] = $id;
            $data['title'] = lang('Sửa đơn xin nghỉ phép');
            $data['paidholiday'] = get_table_where('tbl_paid_holiday_leave', ['id' => $id], '', 'row_array');
            $data['paidholidayDetai'] = get_table_where('tbl_paid_holiday_leave_detail',
                ['paid_holiday_leave_id' => $id]);
	
	
			$arrayOrWhereIn = [];
			if(!empty($data['paidholiday']['staff_id'])) {
				$arrayOrWhereIn[] = $data['paidholiday']['staff_id'];
			}
			if(!empty($data['paidholiday']['staff_id_replace'])) {
				$arrayOrWhereIn[] = $data['paidholiday']['staff_id_replace'];
			}
			$data['staff'] = getPersonDeparmentdt(0, $arrayOrWhereIn);

        } else {
			$data['staff'] = getPersonDeparmentdt(0);
            $data['id'] = '';
            $year = date('Y');
            $month = date('m');
            $startdate = date('' . $year . '-' . $month . '-01');
            $newdate = date('' . $year . '-' . $month . '-t');
            $allDate = createDateRangeArray($month,$year);
            $allDateNew = [];
            foreach ($allDate as $key => $value){
                $timestamp = strtotime($value);
                $day = date('D', $timestamp);
                $month = date('M', $timestamp);
                $date = date('d', $timestamp);
                $allDateNew[$key]= [
                    'date' => _dhau($value),
                    'day' => $day,
                    'month' => $month,
                    'date_new' => $date,
                ];
            }
            $data['allDateNew'] = $allDateNew;

            $data['title'] = lang('Thêm đơn xin nghỉ phép');
        }
        $this->load->view('admin/paid_holidays/add_paid_holiday_leave', $data);
    }

    public function getPaidHolidayLeave()
    {
        $name_search = $this->input->post('name_search');
        $staff_search = $this->input->post('staff_search');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $status_table = $this->input->post('status_table');
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
            'tbl_paid_holiday_leave.id as id',
            'tbl_paid_holiday_leave.name as name',
            'CONCAT(tb_staff.firstname," ",tb_staff.lastname) as name_staff',
            'CONCAT(personel_replace.firstname," ",personel_replace.lastname) as name_staff_replace',
            'tbl_paid_holiday_leave.created_by as created_by',
            'tbl_paid_holiday_leave.staff_agree as staff_agree',
            '1 as action '
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_paid_holiday_leave';
        $where = [
        ];
        $filter = [];
        $join = [
            'INNER JOIN tblstaff tb_staff ON tb_staff.staffid = tbl_paid_holiday_leave.staff_id',
            'INNER JOIN tblstaff ON tblstaff.staffid = tbl_paid_holiday_leave.created_by',
            'LEFT JOIN tblstaff as personel_replace ON personel_replace.staffid = tbl_paid_holiday_leave.staff_id_replace',
            'LEFT JOIN tblroles ON tblroles.roleid = tb_staff.role',
            'LEFT JOIN ' . $tbDepartment . ' ON tb_department.staffid = tb_staff.staffid',
        ];

        if (!empty($name_search)) {
            array_push($where,
                'AND ( tbl_paid_holiday_leave.name like "%' . $name_search . '%")');
        }
        if (!empty($staff_search)) {
            array_push($where,
                'AND ( tbl_paid_holiday_leave.staff_id IN (' . implode(',', $staff_search) . '))');
        }

        if ($status_table != 'all') {
            if ($status_table == 'un_approved') {
                array_push($where,
                    'AND ( tbl_paid_holiday_leave.status = 0)');
            } elseif ($status_table == 'approved') {
                array_push($where,
                    'AND ( tbl_paid_holiday_leave.status = 1)');
            }
        }

//        if ($this->perSuggestPayslipViewOwn && !is_admin()) {
//            $arrIDStaff = employee_manage_staff();
//            if ($arrIDStaff != array()) {
//                $coverStr = implode(",", $arrIDStaff);
//                array_push($where,
//                    'AND ( table_all_item.staff_create IN (' . $coverStr . '))');
//            }
//        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_paid_holiday_leave.date_created as date_created',
            'tbl_paid_holiday_leave.date_status as date_status',
            'tbl_paid_holiday_leave.staff_status as staff_status',
            'tb_department.name_department as name_deparment',
            'tblroles.name as name_roles',
            'tb_staff.phonenumber as telephone',
            'tb_staff.current_accommodation as current_accommodation'
        ], '', [], []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $stt = 1;
        foreach ($rResult as $key => $aRow) {
            $start++;

            $row = array();

            $row[0] = '<div class="text-center"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" id="rows-child-' . $aRow['id'] . '" class="rows-child fa fa-caret-right"></a></div>';
            $role_name = !empty($aRow['name_roles']) ? '(' . $aRow['name_roles'] . ')' : '';
            $info = '<div style="font-style: italic;font-size: 12px">
                <div>Bộ phận: ' . $aRow['name_deparment'] . '</div>
                <div>Địa chỉ: ' . $aRow['current_accommodation'] . '</div>
                <div>Số điện thoại: ' . $aRow['telephone'] . '</div>
            </div>';
            $row[2] = '<div><span style="font-weight: bold">' . $aRow['name_staff'] . '</span>' . $info . '</div>';
            $row[3] = '<div class="text-left">' . $aRow['name_staff_replace'] . '</div>';
            $staff_created = staff_profile_image($aRow['created_by'], array('staff-profile-image-small mright5'),
                    'small', array(
                        'data-toggle' => 'tooltip',
                        'data-title' => ' Vào lúc: ' . _dt($aRow['date_created'])
                    )) . get_staff_full_name($aRow['created_by']) . '<br>';
            $row[4] = '<div class="text-left">' . $staff_created . '<div style="font-style: italic; font-size: 12px">
                ' . _dt($aRow['date_created']) . '
            </div></div>';
            if (!empty($aRow['staff_agree'])) {
                $staff_agree = staff_profile_image($aRow['staff_agree'], array('staff-profile-image-small mright5'),
                        'small', array(
                            'data-toggle' => 'tooltip',
                        )) . get_staff_full_name($aRow['staff_agree']) . '<br>';
            } else {
                $staff_agree = '';
            }
            $row[5] = '<div class="text-left">' . $staff_agree . '<div style="font-style: italic; font-size: 12px"></div></div>';

            $actions = '<div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">' . _l('action') . '
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu h_right" style="width: 190px">';
            $actions .= $this->perEditPaidHoliday ? '<li><a href="" onclick="edit(' . $aRow['id'] . ');return false;" class="text-danger"><i class="fa fa-edit"></i> ' . _l('Sửa đơn nghỉ phép') . '</a></li>' : '';
            $actions .= '<li><a href="' . admin_url('paid_holidays/print_pdf/' . $aRow['id']) . '" target="_blank"><i class="fa fa-file-pdf-o width-icon-actions"></i> ' . _l('In đơn nghỉ phép') . '</a></li>';
            $actions .= $this->perDeletePaidHoliday ? '<li><a href="" onclick="deleteTicket(' . $aRow['id'] . ');return false;" class="text-danger delete-remind"><i class="fa fa-times"></i> ' . _l('Xóa phiếu nghỉ phép') . '</a></li>' : '';
            $actions .= '</ul></div>';
            $row[6] = '<div class="text-center">' . $actions . '</div>';

            $trItems = '';
            $this->db->select('
                tbl_paid_holiday_leave_detail.date_start as date_start,
                tbl_paid_holiday_leave_detail.date_end as date_end,
                tbl_paid_holiday_leave_detail.number_date as number_date,
                tbl_paid_holiday_leave_detail.day_work as day_work,
                tbl_paid_holiday_leave_detail.note as note,
                tbl_type_magic.name as name_type_magic,
                tbl_type_magic.id as id_type_magic,
                tbl_paid_holiday_leave_detail.id as id,
                tbl_paid_holiday_leave_detail.date_status as date_status,
                tbl_paid_holiday_leave_detail.status as status,
                tbl_paid_holiday_leave_detail.staff_status as staff_status,
                tbl_paid_holiday_leave_detail.note_status as note_status,
            ');
            $this->db->from('tbl_paid_holiday_leave_detail');
            $this->db->join('tbl_type_magic', 'tbl_type_magic.id = tbl_paid_holiday_leave_detail.type_magic_id',
                'left');
            $this->db->where('tbl_paid_holiday_leave_detail.paid_holiday_leave_id', $aRow['id']);
            $paidHplidayDetail = $this->db->get()->result_array();
            $countStatus = 0;
            foreach ($paidHplidayDetail as $k => $v) {

                $name_type = '';
                if ($v['id_type_magic'] == 1){
                    $name_type = ' (CP , P 1/2)';
                } elseif($v['id_type_magic'] == 2){
                    $name_type = ' (OD)';
                } elseif($v['id_type_magic'] == 3){
                    $name_type = ' (CH)';
                } elseif($v['id_type_magic'] == 4){
                    $name_type = ' (TS)';
                } elseif($v['id_type_magic'] == 5){
                    $name_type = ' (PKL , PKL 1/2)';
                } elseif($v['id_type_magic'] == 6){
                    $name_type = ' (F)';
                }

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
                $strNote = '';
                if ($v['status'] == 0) {
                    $countStatus ++;
                    $html = "<p><a id='agree_child' value='1' data-id='" . $v['id'] . "' class='btn btn-success btn-icon'>Duyệt</a>
                             <a id='agree_child' data-id= '" . $v['id'] . "' value='2' class='btn btn-danger label not_approve'>Không duyệt</a><br><label style='margin-top:10px' class='label-note hide'>Ghi chú</label><textarea class='form-control hide note_approve_task' name='note_approve_task' rows='3' placeholder=' nhập ghi chú '></textarea>
                             <button style='margin-top:5px;margin-left:5px' class='btn btn-info hide po-save'>Lưu</button>
                             <button class='btn po-close hide btn-icon'>Thoát</button></p>";
                    $strApprove = '<div class="text-center mbot5"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="' . $html . '" class="label label-warning po" data-original-title="Duyệt">Chưa duyệt</span></div>';
                } elseif ($v['status'] == 1) {
                    $html = "<p><a id='agree_child' value='0' data-id='" . $v['id'] . "' class='btn btn-warning btn-icon'>Bỏ duyệt</a>
                             <a id='agree_child' data-id= '" . $v['id'] . "' value='2' class='btn btn-danger label not_approve'>Không duyệt</a><br><label style='margin-top:10px' class='label-note hide'>Ghi chú</label><textarea class='form-control hide note_approve_task' name='note_approve_task' rows='3' placeholder=' nhập ghi chú '></textarea>
                            <button style='margin-top:5px;margin-left:5px' class='btn btn-info hide po-save'>Lưu</button>
                            <button class='btn po-close  btn-icon hide'>Thoát</button></p>";
                    $strApprove = '<div class="text-center mbot5"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="' . $html . '" class="label label-success po" data-original-title="Duyệt">Đã duyệt</span></div>';
                } elseif ($v['status'] == 2) {
                    $html = "<p>
                            <a id='agree_child' value='1' data-id='" . $v['id'] . "' class='btn btn-success btn-icon'>Duyệt</a>
                            <a id='agree_child' value='0' data-id='" . $v['id'] . "' class='btn btn-danger btn-icon'>Bỏ duyệt</a>
                            <button class='btn po-close  btn-icon hide'>Thoát</button></p>";
                    $strApprove = '<div class="text-center mbot5"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="' . $html . '" class="label label-danger po" data-original-title="Duyệt">Không duyệt</span></div>';
                    $strNote = '<div>'.$v['note_status'].'</div>';
                }

                $trItems .= '<tr>
                        <td class="text-center">' . (++$k) . '</td>
                        <td class="text-left">' . $v['name_type_magic'] . $name_type. '</td>
                        <td class="text-left">' . _dhau($v['date_start']) . '</td>
                        <td class="text-left">' . _dhau($v['date_end']) . '</td>
                        <td class="text-center">' . ($v['number_date']) . '</td>
                        <td class="text-left" style="width: 90px;">' . _dhau($v['day_work']) . '</td>
                        <td class="text-left" style="width: 150px;">' . $v['note'] . '</td>
                        <td class="text-left" style="width: 150px;">' . $strApprove .$strNote. $strApproveHtml . '</td>
                    </tr>';
            }
            $_data = '
                <div class="scrolling-stone pr-4 position-absolute h-100 w-100 overflow-auto max-height">
                    <div class="">
                         <div class="col-md-10">
                            <table class="table" style="margin-top: 0px;">
                                <thead>
                                    <tr>
                                        <th style="background: #d9edf7 !important; border: 1px solid #93b4d6 !important; color: #0e5dab !important;" class="text-center" style="width: 50px;">STT</th>
                                        <th style="background: #d9edf7 !important; border: 1px solid #93b4d6 !important; color: #0e5dab !important;" class="text-center" style="width: 100px;">' . lang('Loại phép') . '</th>
                                        <th style="background: #d9edf7 !important; border: 1px solid #93b4d6 !important; color: #0e5dab !important;" class="text-center" style="width: 120px;">' . lang('Từ ngày') . '</th>
                                        <th style="background: #d9edf7 !important; border: 1px solid #93b4d6 !important; color: #0e5dab !important;" class="text-center" style="width: 120px;">' . lang('Đến ngày') . '</th>
                                        <th style="background: #d9edf7 !important; border: 1px solid #93b4d6 !important; color: #0e5dab !important;" class="text-center" style="width: 100px;">' . lang('Số ngày nghỉ') . '</th>
                                        <th style="background: #d9edf7 !important; border: 1px solid #93b4d6 !important; color: #0e5dab !important;" class="text-center" style="width: 80px;">' . lang('Ngày bắt đầu làm lại') . '</th>
                                        <th style="background: #d9edf7 !important; border: 1px solid #93b4d6 !important; color: #0e5dab !important;" class="text-center" style="width: 150px;">' . lang('Ghi chú') . '</th>
                                        <th style="background: #d9edf7 !important; border: 1px solid #93b4d6 !important; color: #0e5dab !important;" class="text-center" style="width: 150px;">' . lang('Trạng thái') . '</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ' . $trItems . '
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-2">
                            '.($countStatus > 0 ? '<div class="btn btn-success" type="button" onclick="clickAgreeAll('.$aRow['id'].')">Duyệt tất cả</div>' : '').'
                        </div>
                    </div>
                </div>
            ';
            $row[7] = '<div class="text-left">' . $_data . '</div>';

            $row[1] = '<div>' . $aRow['name'] . '</div>'.($countStatus > 0 ? '<div style="color: green;font-style: italic">Còn '.$countStatus.' dòng chi tiết chưa duyệt</div>' : '');
            $output['aaData'][] = $row;
            $stt++;

        }
        echo json_encode($output);
    }

    public function get_total()
    {
        $name_search = $this->input->post('name_search');
        $staff_search = $this->input->post('staff_search');

        $this->db->from('tbl_paid_holiday_leave');
        if (!empty($name_search)) {
            $this->db->where('( tbl_paid_holiday_leave.name like "%' . $name_search . '%")');
        }
        if (!empty($staff_search)) {
            $this->db->where('tbl_paid_holiday_leave.staff_id IN (' . implode(',', $staff_search) . ')');
        }
        $data['all'] = $this->db->count_all_results();

        $this->db->from('tbl_paid_holiday_leave');
        $this->db->where('status', 0);
        if (!empty($name_search)) {
            $this->db->where('( tbl_paid_holiday_leave.name like "%' . $name_search . '%")');
        }
        if (!empty($staff_search)) {
            $this->db->where('tbl_paid_holiday_leave.staff_id IN (' . implode(',', $staff_search) . ')');
        }
        $data['un_approved'] = $this->db->count_all_results();

        $this->db->from('tbl_paid_holiday_leave');
        $this->db->where('status', 1);
        if (!empty($name_search)) {
            $this->db->where('( tbl_paid_holiday_leave.name like "%' . $name_search . '%")');
        }
        if (!empty($staff_search)) {
            $this->db->where('tbl_paid_holiday_leave.staff_id IN (' . implode(',', $staff_search) . ')');
        }
        $data['approved'] = $this->db->count_all_results();

        echo json_encode($data);
    }

    public function update_status()
    {
        $id = $this->input->post('id');
        $status = $this->input->post('status');
        if (!empty($id)) {

            $this->db->where('id', $id);
            $paidHoliday = $this->db->get('tbl_paid_holiday_leave')->row_array();
            if ($paidHoliday['status'] == $status) {
                echo json_encode([
                    'success' => false,
                    'message' => _l('Phiếu đang ở trạng thái này không thể duyệt được nữa')
                ]);
                die();
            }

            $checkTime = get_table_where('tbl_timekeeping_detail', ['paid_holiday_id' => $id], '', 'row_array');
            if (!empty($checkTime)) {
                echo json_encode([
                    'success' => false,
                    'message' => _l('Phiếu này đã được áp dụng bên chấm công không thể bỏ duyệt !')
                ]);
                die();
            }

            $data_update = ['status' => $status];
            if (!empty($status)) {
                $data_update['staff_status'] = get_staff_user_id();
                $data_update['date_status'] = date('Y-m-d H:i:s');
            } else {
                $data_update['staff_status'] = null;
                $data_update['date_status'] = null;
                $data_update['status'] = 0;
            }
            $this->db->where('id', $id);
            $success = $this->db->update('tbl_paid_holiday_leave', $data_update);
            if (!empty($success)) {
                $get_code = get_table_where('tbl_paid_holiday_leave', array('id' => $id), '', 'row');
                activity_log_v2('status_paid_holiday_leave', 'tbl_paid_holiday_leave', $id, $get_code->name,
                    'Duyệt đơn xin nghỉ phép [' . $get_code->name . ']');
                echo json_encode([
                    'result' => $success,
                    'message' => _l('cong_update_true')
                ]);
                die();
            }
        }
        echo json_encode([
            'result' => false,
            'message' => _l('cong_update_false')
        ]);
        die();
    }

    public function update_status_child()
    {
        $id = $this->input->post('id');
        $status = $this->input->post('status');
        $note = $this->input->post('note');

        if (!$this->perApprovePaidHoliday){
            echo json_encode([
                'success' => false,
                'message' => _l('Bạn không có quyền duyệt')
            ]);
            die();
        }

        if (!empty($id)) {

            $this->db->where('id', $id);
            $paidHoliday = $this->db->get('tbl_paid_holiday_leave_detail')->row_array();
            if ($paidHoliday['status'] == $status) {
                echo json_encode([
                    'success' => false,
                    'message' => _l('Loại phép này đang ở trạng thái này không thể duyệt được nữa')
                ]);
                die();
            }

//            if ($status == 0) {
//                $checkTime = get_table_where('tbl_timekeeping_detail', ['paid_holiday_detail_id' => $id], '',
//                    'row_array');
//                if (!empty($checkTime)) {
//                    echo json_encode([
//                        'success' => false,
//                        'message' => _l('Loại ngày phép này đã được áp dụng bên chấm công không thể bỏ duyệt !')
//                    ]);
//                    die();
//                }
//            }
//            if ($status == 2) {
//                $checkTime = get_table_where('tbl_timekeeping_detail', ['paid_holiday_detail_id' => $id], '',
//                    'row_array');
//                if (!empty($checkTime)) {
//                    echo json_encode([
//                        'success' => false,
//                        'message' => _l('Loại ngày phép này đã được áp dụng bên chấm công!')
//                    ]);
//                    die();
//                }
//            }

            $this->db->select('
                    tbl_type_magic.name as name_magic,
                    date_start,
                    date_end,
                    tbl_type_magic.id as type_magic_id,
                    paid_holiday_leave_id,
                    tbl_paid_holiday_leave_detail.id as id,
                    tbl_paid_holiday_leave.staff_id
                    ');
            $this->db->from('tbl_paid_holiday_leave_detail');
            $this->db->join('tbl_paid_holiday_leave','tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
            $this->db->join('tbl_type_magic', 'tbl_type_magic.id = tbl_paid_holiday_leave_detail.type_magic_id');
            $this->db->where('tbl_paid_holiday_leave_detail.id', $id);
            $dtPaidHolidayDetail = $this->db->get()->row_array();
            if ($status == 0){

                $paid_holiday_id = $dtPaidHolidayDetail['paid_holiday_leave_id'];
                $paid_holiday_detail_id = $dtPaidHolidayDetail['id'];
                $date_start = $dtPaidHolidayDetail['date_start'];
                $date_end = $dtPaidHolidayDetail['date_end'];
                $this->db->select('tbl_timekeeping_detail.id,tbl_timekeeping_detail.date,tbl_timekeeping_detail.staff_id,tbl_timekeeping.month,tbl_timekeeping.year');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping','tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id');
                $this->db->where('date >=',$date_start);
                $this->db->where('date <=',$date_end);
                $this->db->where('staff_id',$dtPaidHolidayDetail['staff_id']);
                $timeDetails = $this->db->get()->result_array();
                if (!empty($timeDetails)){
                    foreach ($timeDetails as $kk => $vv){
                        $this->db->from('tbl_payroll_item');
                        $this->db->join('tbl_payroll',
                            'tbl_payroll.id = tbl_payroll_item.payroll_id');
                        $this->db->where('tbl_payroll.month', $vv['month']);
                        $this->db->where('tbl_payroll.year', $vv['year']);
                        $this->db->where('tbl_payroll_item.staff_id', $vv['staff_id']);
                        $payrollItem = $this->db->get()->row_array();
                        if (!empty($payrollItem)) {
                            $data['result'] = 0;
                            $data['message'] = 'Loại phép này đã áp dung bên chấm công và đã tính bảng lương không thể bỏ duyệt !';
                            echo json_encode($data);
                            die();
                        }
                    }
                }
            } else {
                $year_search = date('Y');
                $year_search_old = date('Y') - 1;
                $this->db->select('tbl_setup_paid_holiday_staff.number_day,number_day_now,number_day_old');
                $this->db->from('tbl_setup_paid_holiday');
                $this->db->join('tbl_setup_paid_holiday_staff','tbl_setup_paid_holiday_staff.id_setup_paid_holiday = tbl_setup_paid_holiday.id');
                $this->db->where('tbl_setup_paid_holiday.year',$year_search);
                $this->db->where('tbl_setup_paid_holiday_staff.staff_id',$dtPaidHolidayDetail['staff_id']);
                $paid_year = $this->db->get()->row_array();

                $this->db->select('tbl_setup_paid_holiday_staff.number_day');
                $this->db->from('tbl_setup_paid_holiday');
                $this->db->join('tbl_setup_paid_holiday_staff','tbl_setup_paid_holiday_staff.id_setup_paid_holiday = tbl_setup_paid_holiday.id');
                $this->db->where('tbl_setup_paid_holiday.year',$year_search_old);
                $this->db->where('tbl_setup_paid_holiday_staff.staff_id',$dtPaidHolidayDetail['staff_id']);
                $paid_year_old = $this->db->get()->row_array();

                $this->db->select('COUNT(tbl_timekeeping_detail.id) as number_date');
                $this->db->from('tbl_paid_holiday_leave_detail');
                $this->db->join('tbl_paid_holiday_leave',
                    'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
                $this->db->join('tbl_paid_holiday_leave_detail_month',
                    'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
                $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 1);
                $this->db->where('tbl_paid_holiday_leave.staff_id', $dtPaidHolidayDetail['staff_id']);
                $this->db->where('tbl_timekeeping_detail.type', 'AL');
                $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search");
                $number_date = $this->db->get()->row_array();

                $this->db->select('COUNT(tbl_timekeeping_detail.id) as number_date');
                $this->db->from('tbl_paid_holiday_leave_detail');
                $this->db->join('tbl_paid_holiday_leave',
                    'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
                $this->db->join('tbl_paid_holiday_leave_detail_month',
                    'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
                $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 1);
                $this->db->where('tbl_paid_holiday_leave.staff_id', $dtPaidHolidayDetail['staff_id']);
                $this->db->where('tbl_timekeeping_detail.type', 'AL/2');
                $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search");
                $number_date_new = $this->db->get()->row_array();

                $number_date_phep = $number_date['number_date'] + ($number_date_new['number_date'] * 0.5);


                $this->db->select('COUNT(tbl_timekeeping_detail.id) as number_date');
                $this->db->from('tbl_paid_holiday_leave_detail');
                $this->db->join('tbl_paid_holiday_leave',
                    'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
                $this->db->join('tbl_paid_holiday_leave_detail_month',
                    'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
                $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 1);
                $this->db->where('tbl_paid_holiday_leave.staff_id', $dtPaidHolidayDetail['staff_id']);
                $this->db->where('tbl_timekeeping_detail.type', 'AL');
                $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search_old AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search_old");
                $number_date_old = $this->db->get()->row_array();

                $this->db->select('COUNT(tbl_timekeeping_detail.id) as number_date');
                $this->db->from('tbl_paid_holiday_leave_detail');
                $this->db->join('tbl_paid_holiday_leave',
                    'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
                $this->db->join('tbl_paid_holiday_leave_detail_month',
                    'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
                $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 1);
                $this->db->where('tbl_paid_holiday_leave.staff_id', $dtPaidHolidayDetail['staff_id']);
                $this->db->where('tbl_timekeeping_detail.type', 'AL/2');
                $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search_old AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search_old");
                $number_date_new_old = $this->db->get()->row_array();

                $number_date_phep_old = $number_date_old['number_date'] + ($number_date_new_old['number_date'] * 0.5);

                $month = 3;
                $day = 31;
                $date_check = date('Y-'.$month.'-'.$day.'');
                $number_day_old = !empty($paid_year['number_day_old']) ? $paid_year['number_day_old'] : 0;
                if (strtotime(date('Y-m-d')) > strtotime($date_check)){
                    $number_date_phep_old = 0;
                }
                $number_date_phep = (!empty($paid_year) && !empty($paid_year['number_day_now'] + $number_day_old ) ? ($paid_year['number_day_now'] + $number_day_old) - $number_date_phep : 0);
                $number_date_phep = $number_date_phep < 0 ? 0 : $number_date_phep;
                if ($paidHoliday['number_date'] > ($number_date_phep)){
                    $data['result'] = 0;
                    $data['message'] = 'Số lượng phép năm không đủ';
                    echo json_encode($data);
                    die();
                }
            }
            $data_update = ['status' => $status];
            if (!empty($status)) {
                $data_update['staff_status'] = get_staff_user_id();
                $data_update['date_status'] = date('Y-m-d H:i:s');
                $data_update['note_status'] = $note;
            } else {
                $data_update['staff_status'] = null;
                $data_update['date_status'] = null;
                $data_update['status'] = 0;
                $data_update['note_status'] = null;
            }
            $this->db->where('id', $id);
            $success = $this->db->update('tbl_paid_holiday_leave_detail', $data_update);
            if (!empty($success)) {
                $get_code = get_table_where('tbl_paid_holiday_leave',
                    array('id' => $paidHoliday['paid_holiday_leave_id']), '', 'row');
                $this->db->select('
                    tbl_type_magic.name as name_magic,
                    date_start,
                    date_end,
                    tbl_type_magic.id as type_magic_id,
                    paid_holiday_leave_id,
                    tbl_paid_holiday_leave_detail.id as id
                    ');
                $this->db->from('tbl_paid_holiday_leave_detail');
                $this->db->join('tbl_type_magic', 'tbl_type_magic.id = tbl_paid_holiday_leave_detail.type_magic_id');
                $this->db->where('tbl_paid_holiday_leave_detail.id', $id);
                $get_code_child = $this->db->get()->row_array();

                $paidMonth = get_table_where('tbl_paid_holiday_leave_detail_month',['paid_holiday_leave_detail_id' => $get_code_child['id']],'','row_array');


                $type = 'X';
                $typeCheck = $get_code_child['type_magic_id'];
                if ($typeCheck == 1){
                    if ($paidMonth['number_day'] == '0.5'){
                        $type = 'AL/2';
                    } else {
                        $type = 'AL';
                    }
                } elseif ($typeCheck == 5){
                    if ($paidMonth['number_day'] == '0.5'){
                        $type = 'UP/2';
                    } else {
                        $type = 'UP';
                    }
                } elseif ($typeCheck == 3){
                    $type = 'CH';
                } elseif ($typeCheck == 4){
                    $type = 'TS';
                } elseif ($typeCheck == 2){
                    $type = 'OD';
                } elseif ($typeCheck == 6){
                    $type = 'F';
                }
                $paid_holiday_id = $get_code_child['paid_holiday_leave_id'];
                $paid_holiday_detail_id = $get_code_child['id'];
                $date_start = $get_code_child['date_start'];
                $date_end = $get_code_child['date_end'];
                $this->db->select('tbl_timekeeping_detail.id,tbl_timekeeping_detail.date,tbl_timekeeping_detail.staff_id,tbl_timekeeping.month,tbl_timekeeping.year');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping','tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id');
                $this->db->where('date >=',$date_start);
                $this->db->where('date <=',$date_end);
                $this->db->where('staff_id',$get_code->staff_id);
                $timeDetails = $this->db->get()->result_array();
                if (!empty($timeDetails)){
                    foreach ($timeDetails as $kk => $vv){
                        if ($status == 1) {
                            $this->db->where('id', $vv['id']);
                            $this->db->update('tbl_timekeeping_detail', [
                                'type' => $type,
                                'date_updated' => date('Y-m-d H:i:s'),
                                'updated_by' => get_staff_user_id(),
                                'paid_holiday_id' => $paid_holiday_id,
                                'paid_holiday_detail_id' => $paid_holiday_detail_id,
                            ]);
                            notificationAgreePaidHoliday($paid_holiday_id,$paid_holiday_detail_id,get_staff_user_id(),1);
                        } elseif ($status == 2){
                            $this->db->where('id', $vv['id']);
                            $this->db->update('tbl_timekeeping_detail', [
                                'type' => 'KP',
                                'date_updated' => date('Y-m-d H:i:s'),
                                'updated_by' => get_staff_user_id(),
                                'paid_holiday_id' => $paid_holiday_id,
                                'paid_holiday_detail_id' => $paid_holiday_detail_id,
                            ]);
                            notificationAgreePaidHoliday($paid_holiday_id,$paid_holiday_detail_id,get_staff_user_id(),2);
                        } else {
                            $this->db->where('id', $vv['id']);
                            $this->db->update('tbl_timekeeping_detail', [
                                'type' => 'X',
                                'date_updated' => date('Y-m-d H:i:s'),
                                'updated_by' => get_staff_user_id(),
                                'paid_holiday_id' => 0,
                                'paid_holiday_detail_id' => 0,
                            ]);
                        }
                    }
                }

                activity_log_v2('status_paid_holiday_leave_child', 'tbl_paid_holiday_leave_detail', $id,
                    $get_code->name,
                    'Duyệt đơn xin nghỉ phép [' . $get_code->name . '][' . $get_code_child['name_magic'] . ']');
                echo json_encode([
                    'result' => $success,
                    'id' => $paidHoliday['paid_holiday_leave_id'],
                    'message' => _l('cong_update_true')
                ]);
                die();
            }
        }
        echo json_encode([
            'result' => false,
            'message' => _l('cong_update_false')
        ]);
        die();
    }

    public function clickAgreeAll()
    {
        $id = $this->input->post('id');
        if (!$this->perApprovePaidHoliday){
            echo json_encode([
                'result' => false,
                'message' => _l('Bạn không có quyền duyệt')
            ]);
            die();
        }
        if (!empty($id)) {

            $this->db->where('paid_holiday_leave_id', $id);
            $this->db->where('status', 0);
            $paidHoliday = $this->db->get('tbl_paid_holiday_leave_detail')->result_array();

            $status = 1;
            $data_update = ['status' => $status];
            if (!empty($status)) {
                $data_update['staff_status'] = get_staff_user_id();
                $data_update['date_status'] = date('Y-m-d H:i:s');
            } else {
                $data_update['staff_status'] = NULL;
                $data_update['date_status'] = NULL;
                $data_update['status'] = 0;
            }
            $count = 0;
            if (!empty($paidHoliday)){
                foreach ($paidHoliday as $key => $value){
                    $this->db->where('id', $value['id']);
                    $success = $this->db->update('tbl_paid_holiday_leave_detail', $data_update);
                    if (!empty($success)) {
                        $count ++;
                        $get_code = get_table_where('tbl_paid_holiday_leave', array('id' => $id), '', 'row');
                        $this->db->select('
                            tbl_type_magic.name as name_magic,
                            date_start,
                            date_end,
                            tbl_type_magic.id as type_magic_id,
                            paid_holiday_leave_id,
                            tbl_paid_holiday_leave_detail.id as id
                        ');
                        $this->db->from('tbl_paid_holiday_leave_detail');
                        $this->db->join('tbl_type_magic', 'tbl_type_magic.id = tbl_paid_holiday_leave_detail.type_magic_id');
                        $this->db->where('tbl_paid_holiday_leave_detail.id', $value['id']);
                        $get_code_child = $this->db->get()->row_array();

                        $paidMonth = get_table_where('tbl_paid_holiday_leave_detail_month',['paid_holiday_leave_detail_id' => $get_code_child['id']],'','row_array');

                        $type = 'X';
                        $typeCheck = $get_code_child['type_magic_id'];
                        if ($typeCheck == 1){
                            if ($paidMonth['number_day'] == '0.5'){
                                $type = 'AL/2';
                            } else {
                                $type = 'AL';
                            }
                        } elseif ($typeCheck == 5){
                            if ($paidMonth['number_day'] == '0.5'){
                                $type = 'UP/2';
                            } else {
                                $type = 'UP';
                            }
                        } elseif ($typeCheck == 3){
                            $type = 'CH';
                        } elseif ($typeCheck == 4){
                            $type = 'TS';
                        } elseif ($typeCheck == 2){
                            $type = 'OD';
                        } elseif ($typeCheck == 6){
                            $type = 'F';
                        }
                        $paid_holiday_id = $get_code_child['paid_holiday_leave_id'];
                        $paid_holiday_detail_id = $get_code_child['id'];
                        $date_start = $get_code_child['date_start'];
                        $date_end = $get_code_child['date_end'];
                        $this->db->select('tbl_timekeeping_detail.id,tbl_timekeeping_detail.date');
                        $this->db->from('tbl_timekeeping_detail');
                        $this->db->join('tbl_timekeeping','tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id');
                        $this->db->where('date >=',$date_start);
                        $this->db->where('date <=',$date_end);
                        $this->db->where('staff_id',$get_code->staff_id);
                        $timeDetails = $this->db->get()->result_array();
                        if (!empty($timeDetails)){
                            foreach ($timeDetails as $kk => $vv){
                                $this->db->where('id',$vv['id']);
                                $this->db->update('tbl_timekeeping_detail',[
                                    'type' => $type,
                                    'date_updated' => date('Y-m-d H:i:s'),
                                    'updated_by' => get_staff_user_id(),
                                    'paid_holiday_id' => $paid_holiday_id,
                                    'paid_holiday_detail_id' => $paid_holiday_detail_id,
                                ]);
                            }
                        }

                        activity_log_v2(
                            'status_paid_holiday_leave_child',
                            'tbl_paid_holiday_leave_detail',
                            $value['id'],
                            $get_code->name,
                            'Duyệt đơn xin nghỉ phép [' . $get_code->name . '][' . $get_code_child['name_magic'] . ']'
                        );

                    }
                }
            }
            if ($count){
                echo json_encode([
                    'result' => true,
                    'id' => $id,
                    'message' => _l('cong_update_true')
                ]);
                die();
            }

        }
        echo json_encode([
            'result' => false,
            'message' => _l('cong_update_false')
        ]);
        die();
    }

    public function deleteTicket()
    {
        if (!$this->perDeletePaidHoliday){
            echo json_encode([
                'result' => false,
                'message' => _l('Không có quyền xóa!')
            ]);
            die();
        }
        $id = $this->input->post('id');
        if (!empty($id)) {

            $this->db->where('paid_holiday_leave_id', $id);
            $this->db->group_start();
            $this->db->where('status', 1);
            $this->db->or_where('status', 2);
            $this->db->group_end();
            $paidHoliday = $this->db->get('tbl_paid_holiday_leave_detail')->row_array();

            if (!empty($paidHoliday)) {
                echo json_encode([
                    'result' => false,
                    'message' => _l('Có chi tiết đơn xin phép đã được duyệt không thể xóa')
                ]);
                die();
            }
            $get_code = get_table_where('tbl_paid_holiday_leave', array('id' => $id), '', 'row');
            $this->db->where('id', $id);
            $success = $this->db->delete('tbl_paid_holiday_leave');

            $itemOld = get_table_where('tbl_paid_holiday_leave_detail', ['paid_holiday_leave_id' => $id]);

            if (!empty($success)) {

                $this->db->where('paid_holiday_leave_id', $id);
                $this->db->delete('tbl_paid_holiday_leave_detail');

                if (!empty($itemOld)) {
                    foreach ($itemOld as $key => $value) {
                        $this->db->where('paid_holiday_leave_detail_id', $value['id']);
                        $this->db->delete('tbl_paid_holiday_leave_detail_month');
                    }
                }

                activity_log_v2('delete_paid_holiday_leave', 'tbl_paid_holiday_leave', $id, $get_code->name,
                    'Xoá đơn xin nghỉ phép [' . $get_code->name . ']');
                echo json_encode([
                    'result' => $success,
                    'message' => _l('cong_update_true')
                ]);
                die();
            }
        }
        echo json_encode([
            'result' => false,
            'message' => _l('cong_update_false')
        ]);
        die();
    }

    public function checkEdit()
    {
        $id = $this->input->post('id');
        if (!empty($id)) {

            $this->db->where('paid_holiday_leave_id', $id);
            $this->db->where('status', 1);
            $paidHoliday = $this->db->get('tbl_paid_holiday_leave_detail')->row_array();

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

    public function print_pdf($id = '')
    {
        ob_start();
        $data = new stdClass();
        $paidHoliday = get_table_where('tbl_paid_holiday_leave', array('id' => $id), '', 'row_array');

        $tbDepartment = "(
            SELECT
                tblstaff_departments.staffid as staffid,
                GROUP_CONCAT(tbldepartments.name) as name_department
            FROM tbldepartments
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            GROUP BY tblstaff_departments.staffid
        ) tb_department";

        $this->db->select('
            tbl_paid_holiday_leave.id as id,
            tbl_paid_holiday_leave.name as name,
            tbl_paid_holiday_leave.date_created as date_created,
            CONCAT(tb_staff.firstname," ",tb_staff.lastname) as name_staff,
            CONCAT(personel_replace.firstname," ",personel_replace.lastname) as name_staff_replace,
            tb_department.name_department as name_deparment,
            tblroles.name as name_roles,
            tb_staff.phonenumber as telephone,
            tb_staff.current_accommodation as current_accommodation
        ');
        $this->db->from('tbl_paid_holiday_leave');
        $this->db->join('tblstaff as tb_staff', 'tb_staff.staffid = tbl_paid_holiday_leave.staff_id');
        $this->db->join('tblstaff as personel_replace',
            'personel_replace.staffid = tbl_paid_holiday_leave.staff_id_replace', 'left');
        $this->db->join($tbDepartment, 'tb_department.staffid = tb_staff.staffid', 'left');
        $this->db->join('tblroles', 'tblroles.roleid = tb_staff.role', 'left');
        $this->db->where('tbl_paid_holiday_leave.id', $id);
        $paidHoliday = $this->db->get()->row_array();

        $table = '';
        $data->content = '';
        $data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;text-transform: uppercase">' . _l('Đơn xin nghỉ phép') . '</span><br>';

        $day = date('d', strtotime($paidHoliday['date_created']));
        $month = date('m', strtotime($paidHoliday['date_created']));
        $year = date('Y', strtotime($paidHoliday['date_created']));
        $date = _l('ch_day') . ' ' . $day . ' ' . _l('ch_month') . ' ' . $month . ' ' . _l('ch_year') . ' ' . $year;
        $data->content .= '<span style="text-align: center;font-style: italic;">' . $date . '</span><br>';

        $name_roles = '';
        if (!empty($paidHoliday['name_roles'])) {
            $name_roles = ' ( ' . $paidHoliday['name_roles'] . ' )';
        }

        $data->content .= '
            <span style="font-weight: bold;">' . _l('Nhân viên') . ': </span><span>' . $paidHoliday['name_staff'] . '</span><br><br>
            <span style="font-weight: bold;">' . _l('Bộ phận') . ': </span><span>' . $paidHoliday['name_deparment'] . $name_roles . '</span><br><br>
            <span style="font-weight: bold;">' . _l('Địa chỉ liên lạc') . ': </span><span>' . $paidHoliday['current_accommodation'] . '</span><br><br>
            <span style="font-weight: bold;">' . _l('Số điện thoại') . ': </span><span>' . $paidHoliday['telephone'] . '</span><br><br>
            <span style="font-weight: bold;">' . _l('Nhân viên thay thế (nếu có)') . ': </span><span>' . $paidHoliday['name_staff_replace'] . '</span><br><br>';

        $trItems = '';
        $this->db->select('
                tbl_paid_holiday_leave_detail.id as id,
                tbl_paid_holiday_leave_detail.date_start as date_start,
                tbl_paid_holiday_leave_detail.date_end as date_end,
                tbl_paid_holiday_leave_detail.number_date as number_date,
                tbl_paid_holiday_leave_detail.day_work as day_work,
                tbl_paid_holiday_leave_detail.note as note,
                tbl_type_magic.name as name_type_magic
            ');
        $this->db->from('tbl_paid_holiday_leave_detail');
        $this->db->join('tbl_type_magic', 'tbl_type_magic.id = tbl_paid_holiday_leave_detail.type_magic_id', 'inner');
        $this->db->where('tbl_paid_holiday_leave_detail.paid_holiday_leave_id', $id);
        $paidHolidayDetail = $this->db->get()->result_array();
        foreach ($paidHolidayDetail as $k => $v) {
            $trItems .= '<tr>
                        <td style="width: 5%;text-align: center" class="text-center">' . (++$k) . '</td>
                        <td style="width: 20%" class="text-left">' . ($v['name_type_magic']) . '</td>
                        <td style="width: 15%;text-align:left" class="text-left">' . _dhau($v['date_start']) . '</td>
                        <td style="width: 15%" class="text-left">' . _dhau($v['date_end']) . '</td>
                        <td style="width: 10%;text-align: center">' . formatMoney($v['number_date']) . '</td>
                        <td style="width: 15%" class="text-left">' . _dhau($v['day_work']) . '</td>
                        <td style="width: 20%" class="text-left">' . ($v['note']) . '</td>
                    </tr>';
        }

        $data->content .= '<table class="table table-bordered" border="1" width="100%">
                <thead>
                    <tr>
                        <th style="text-align: center;width: 5%">STT</th>
                        <th style="text-align: center;width: 20%">Loại phép</th>
                        <th style="text-align: center;width: 15%">Từ ngày</th>
                        <th style="text-align: center;width: 15%">Đến ngày</th>
                        <th style="text-align: center;width: 10%">Số ngày nghỉ</th>
                        <th style="text-align: center;width: 15%">Ngày làm việc lại</th>
                        <th style="text-align: center;width: 20%">Ghi chú</th>
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
                            <span style="font-weight: bold;">' . _l('Phòng nhân sự') . '</span><br>
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
        $pdf->Output($paidHoliday['name'] . '.pdf', $type);
    }

    public function getInfoByPersonel()
    {
        $data = [];
        $personel_id = $this->input->post('personel_id');
        $year_search = date('Y');
        $year_search_old = $year_search - 1;
        $personel = [];
        if ($personel_id) {

            $tbDepartment = "(
                SELECT
                    tblstaff_departments.staffid as staffid,
                    GROUP_CONCAT(tbldepartments.name) as name_department
                FROM tbldepartments
                JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
                GROUP BY tblstaff_departments.staffid
            ) tb_department";

            $this->db->select('tblstaff.staffid as id,
                CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name_staff,
                tblstaff.phonenumber as phone,
                tblstaff.current_accommodation as address,
                tblroles.name as name_role,
                tb_department.name_department as name_department');
            $this->db->from('tblstaff');
            $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
            $this->db->join($tbDepartment, 'tb_department.staffid = tblstaff.staffid', 'left');
            $this->db->where('tblstaff.staffid', $personel_id);
            $personel = $this->db->get()->row_array();

            $this->db->select('tbl_setup_paid_holiday_staff.number_day,number_day_now,number_day_old');
            $this->db->from('tbl_setup_paid_holiday');
            $this->db->join('tbl_setup_paid_holiday_staff','tbl_setup_paid_holiday_staff.id_setup_paid_holiday = tbl_setup_paid_holiday.id');
            $this->db->where('tbl_setup_paid_holiday.year',$year_search);
            $this->db->where('tbl_setup_paid_holiday_staff.staff_id',$personel_id);
            $paid_year = $this->db->get()->row_array();

            $this->db->select('tbl_setup_paid_holiday_staff.number_day,number_day_now,number_day_old');
            $this->db->from('tbl_setup_paid_holiday');
            $this->db->join('tbl_setup_paid_holiday_staff','tbl_setup_paid_holiday_staff.id_setup_paid_holiday = tbl_setup_paid_holiday.id');
            $this->db->where('tbl_setup_paid_holiday.year',$year_search_old);
            $this->db->where('tbl_setup_paid_holiday_staff.staff_id',$personel_id);
            $paid_year_old = $this->db->get()->row_array();

            $this->db->select('COUNT(tbl_timekeeping_detail.id) as number_date');
            $this->db->from('tbl_paid_holiday_leave_detail');
            $this->db->join('tbl_paid_holiday_leave',
                'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
            $this->db->join('tbl_paid_holiday_leave_detail_month',
                'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
            $this->db->join('tbl_timekeeping_detail',
                'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
            $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
            $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 1);
            $this->db->where('tbl_paid_holiday_leave.staff_id', $personel_id);
            $this->db->where('tbl_timekeeping_detail.type', 'AL');
            $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search");
            $number_date = $this->db->get()->row_array();

            $this->db->select('COUNT(tbl_timekeeping_detail.id) as number_date');
            $this->db->from('tbl_paid_holiday_leave_detail');
            $this->db->join('tbl_paid_holiday_leave',
                'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
            $this->db->join('tbl_paid_holiday_leave_detail_month',
                'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
            $this->db->join('tbl_timekeeping_detail',
                'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
            $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
            $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 1);
            $this->db->where('tbl_paid_holiday_leave.staff_id', $personel_id);
            $this->db->where('tbl_timekeeping_detail.type', 'AL/2');
            $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search");
            $number_date_new = $this->db->get()->row_array();

            $number_date_phep = $number_date['number_date'] + ($number_date_new['number_date'] * 0.5);


            $this->db->select('COUNT(tbl_timekeeping_detail.id) as number_date');
            $this->db->from('tbl_paid_holiday_leave_detail');
            $this->db->join('tbl_paid_holiday_leave',
                'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
            $this->db->join('tbl_paid_holiday_leave_detail_month',
                'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
            $this->db->join('tbl_timekeeping_detail',
                'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
            $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
            $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 1);
            $this->db->where('tbl_paid_holiday_leave.staff_id', $personel_id);
            $this->db->where('tbl_timekeeping_detail.type', 'AL');
            $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search_old AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search_old");
            $number_date_old = $this->db->get()->row_array();

            $this->db->select('COUNT(tbl_timekeeping_detail.id) as number_date');
            $this->db->from('tbl_paid_holiday_leave_detail');
            $this->db->join('tbl_paid_holiday_leave',
                'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
            $this->db->join('tbl_paid_holiday_leave_detail_month',
                'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
            $this->db->join('tbl_timekeeping_detail',
                'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
            $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
            $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 1);
            $this->db->where('tbl_paid_holiday_leave.staff_id', $personel_id);
            $this->db->where('tbl_timekeeping_detail.type', 'AL/2');
            $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search_old AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search_old");
            $number_date_new_old = $this->db->get()->row_array();

            $number_date_phep_old = $number_date_old['number_date'] + ($number_date_new_old['number_date'] * 0.5);


            $month = 3;
            $day = 31;
            $date_check = date('Y-'.$month.'-'.$day.'');
            $number_day_old = !empty($paid_year['number_day_old']) ? $paid_year['number_day_old'] : 0;
            if (strtotime(date('Y-m-d')) > strtotime($date_check)){
                $number_date_phep_old = 0;
            }
            $number_date_phep = (!empty($paid_year) && !empty($paid_year['number_day_now'] + $number_day_old )) ? ($paid_year['number_day_now'] + $number_day_old) - $number_date_phep : 0;
            $number_date_phep = $number_date_phep < 0 ? 0 : $number_date_phep;
        }
        $data['personel'] = $personel;
        $data['number_date_phep'] = $number_date_phep;
        $data['number_date_phep_old'] = $number_date_phep_old;
        echo json_encode($data);
    }

    public function setup_paid_holidays()
    {
        $data = [];
        $data['staff'] = getPersonDeparmentdt(0);
        $data['title'] = lang('Thiết lập phép năm');
        $this->load->view('admin/paid_holidays/setup_paid_holidays', $data);
    }

    public function getSetUpPaidHoliday()
    {
        $year_search = $this->input->post('year_search');
        $staff_search = $this->input->post('staff_search');
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $countQtyStaff = "(
            SELECT 
                tbl_setup_paid_holiday_staff.id_setup_paid_holiday as id_setup_paid_holiday,
                COUNT(tbl_setup_paid_holiday_staff.id) as quantity
            FROM tbl_setup_paid_holiday_staff
            GROUP BY id_setup_paid_holiday
        ) tb_count_qty_staff";
        $aColumns = [
            'tbl_setup_paid_holiday.id as id',
            'tbl_setup_paid_holiday.year as year',
            'tb_count_qty_staff.quantity as total_staff',
            'tbl_setup_paid_holiday.created_by as created_by',
            '1 as action '
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_setup_paid_holiday';
        $where = [
        ];
        $filter = [];
        $join = [
            'INNER JOIN tblstaff ON tblstaff.staffid = tbl_setup_paid_holiday.created_by',
            "LEFT JOIN $countQtyStaff ON tb_count_qty_staff.id_setup_paid_holiday = tbl_setup_paid_holiday.id",
        ];

        if (!empty($year_search)) {
            array_push($where,
                'AND ( tbl_setup_paid_holiday.year IN (' . implode(',', $year_search) . '))');
        }
        if (!empty($staff_search)) {
            array_push($where,
                'AND EXISTS (
                    SELECT
                    FROM tbl_setup_paid_holiday_staff
                    WHERE tbl_setup_paid_holiday_staff.id_setup_paid_holiday = tbl_setup_paid_holiday.id
                    AND ( tbl_setup_paid_holiday_staff.staff_id IN (' . implode(',', $staff_search) . '))
                )');
        }


//        if ($this->perSuggestPayslipViewOwn && !is_admin()) {
//            $arrIDStaff = employee_manage_staff();
//            if ($arrIDStaff != array()) {
//                $coverStr = implode(",", $arrIDStaff);
//                array_push($where,
//                    'AND ( table_all_item.staff_create IN (' . $coverStr . '))');
//            }
//        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_setup_paid_holiday.date_created as date_created'
        ], '', [], []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $stt = 1;
        foreach ($rResult as $key => $aRow) {
            $start++;

            $row = array();

            $row[0] = '<div class="text-center">' . (++$key) . '</div>';
            $row[1] = '<div class="text-center">' . $aRow['year'] . '</div>';

            $row[2] = '<div class="text-center bold" style="font-size: 16px;cursor: pointer" onclick="viewStaff(' . $aRow['id'] . ')">' . formatNumber($aRow['total_staff']) . '</div>';
            $staff_created = staff_profile_image($aRow['created_by'], array('staff-profile-image-small mright5'),
                    'small', array(
                        'data-toggle' => 'tooltip',
                        'data-title' => ' Vào lúc: ' . _dt($aRow['date_created'])
                    )) . get_staff_full_name($aRow['created_by']) . '<br>';
            $row[3] = '<div class="text-left">' . $staff_created . '<div style="font-style: italic; font-size: 12px">
                ' . _dt($aRow['date_created']) . '
            </div></div>';
            $actions = '<div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">' . _l('action') . '
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu h_right" style="width: 190px">';
            $actions .= '<li><a href="" onclick="edit(' . $aRow['id'] . ');return false;" class="text-danger"><i class="fa fa-edit"></i> ' . _l('Sửa thiết lập') . '</a></li>';
            $actions .= '<li><a href="" onclick="deleteTicket(' . $aRow['id'] . ');return false;" class="text-danger delete-remind"><i class="fa fa-times"></i> ' . _l('Xóa thiết lập') . '</a></li>';
            $actions .= '</ul></div>';
            $row[4] = '<div class="text-center">' . $actions . '</div>';

            $output['aaData'][] = $row;
            $stt++;

        }
        echo json_encode($output);
    }

    public function add_setup_paid_holiday($id = '')
    {
        $data = [];

        if ($this->input->post()) {
            $dataPost = $this->input->post();

            $year = $dataPost['year'];
            $pm = $dataPost['pm'];
            $items = [];
            if ($id == '') {
                $option = [
                    'year' => $year,
                    'created_by' => get_staff_user_id(),
                    'date_created' => date('Y-m-d H:i:s'),
                ];

                if (!empty($pm)) {
                    foreach ($pm as $key => $value) {
                        $items[] = [
                            'staff_id' => $value['staff_id'],
                            'number_day_old' => number_unformat($value['number_day_old']),
                            'number_day_now' => number_unformat($value['number_day_now']),
                            'number_day' => number_unformat($value['number_day_old'] + $value['number_day_now']),
                        ];
                    }
                }

                if (empty($items)) {
                    $data['result'] = 0;
                    $data['message'] = 'Không có dữ liệu chi tiết';
                    echo json_encode($data);
                    die();
                }

                $this->db->insert('tbl_setup_paid_holiday', $option);
                $id_insert = $this->db->insert_id();
                if ($id_insert) {
                    foreach ($items as $key => $value) {
                        $value['id_setup_paid_holiday'] = $id_insert;
                        $this->db->insert('tbl_setup_paid_holiday_staff', $value);
                    }
                    $get_code = get_table_where('tbl_setup_paid_holiday', array('id' => $id_insert), '', 'row');
                    activity_log_v2('setup_paid_holiday_leave', 'tbl_setup_paid_holiday', $id_insert, $get_code->year,
                        'Thêm thiết lập phép năm [' . $get_code->year . ']');
                    $data['result'] = 1;
                    $data['message'] = 'Thêm thành công';
                } else {
                    $data['result'] = 0;
                    $data['message'] = 'Thêm thất bại';
                }
            } else {
                $checkPaid = get_table_where('tbl_setup_paid_holiday', array('id' => $id), '', 'row_array');
                $option = [
                    'year' => $year,
                ];

                if (!empty($pm)) {
                    foreach ($pm as $key => $value) {
                        $items[] = [
                            'id' => !empty($value['id']) ? $value['id'] : 0,
                            'staff_id' => $value['staff_id'],
                            'number_day_old' => number_unformat($value['number_day_old']),
                            'number_day_now' => number_unformat($value['number_day_now']),
                            'number_day' => number_unformat($value['number_day_old'] + $value['number_day_now']),
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
                $success = $this->db->update('tbl_setup_paid_holiday', $option);
                if ($success) {
                    $arrId = [];
                    foreach ($items as $key => $value) {
                        $checkExisit = get_table_where('tbl_setup_paid_holiday_staff', ['id' => $value['id']], '',
                            'row_array');
                        if (!empty($checkExisit)) {
                            $arrId[] = $checkExisit['id'];
                            $this->db->where('id', $value['id']);
                            $this->db->update('tbl_setup_paid_holiday_staff', $value);
                        } else {
                            $value['id_setup_paid_holiday'] = $id;
                            $this->db->insert('tbl_setup_paid_holiday_staff', $value);
                            $insert_id_item = $this->db->insert_id();
                            $arrId[] = $insert_id_item;
                        }
                    }

                    if (empty($arrId)) {
                        $this->db->where('id_setup_paid_holiday', $id);
                        $this->db->delete('tbl_setup_paid_holiday_staff');
                    } else {
                        $this->db->where('id_setup_paid_holiday', $id);
                        $this->db->where_not_in('id', $arrId);
                        $this->db->delete('tbl_setup_paid_holiday_staff');
                    }

                    $get_code = get_table_where('tbl_setup_paid_holiday', array('id' => $id), '', 'row');
                    activity_log_v2('edit_setup_paid_holiday_leave', 'tbl_setup_paid_holiday', $id, $get_code->year,
                        'Sửa thiết lập phép năm [' . $get_code->year . ']');
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

        $this->db->select('tblstaff.staffid as id, CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname');
        $this->db->from('tblstaff');
        $this->db->where('active', 1);
        $data['staff'] = $this->db->get()->result_array();
        if (!empty($id)) {
            $data['id'] = $id;
            $data['title'] = lang('Sửa thiết lập phép năm');
            $data['paidholiday'] = get_table_where('tbl_setup_paid_holiday', ['id' => $id], '', 'row_array');
            $data['paidholidayDetai'] = get_table_where('tbl_setup_paid_holiday_staff',
                ['id_setup_paid_holiday' => $id]);

        } else {
            $data['id'] = '';
            $data['title'] = lang('Thêm thiết lập phép năm');
        }
        $this->load->view('admin/paid_holidays/add_setup_paid_holiday', $data);
    }

    public function checkExistYear()
    {
        $data = [];
        $year = !empty($this->input->post('year')) ? $this->input->post('year') : 0;
        $id = !empty($this->input->post('id')) ? $this->input->post('id') : 0;
        $this->db->from('tbl_setup_paid_holiday');
        $this->db->where('year', $year);
        if (!empty($id)) {
            $this->db->where('id != ', $id);
        }
        $checkExisit = $this->db->get()->row_array();
        if (!empty($checkExisit)) {
            $data['result'] = 1;
        } else {
            $data['result'] = 0;
        }
        echo json_encode($data);
        die();
    }

    public function view_staff_setup_paid_holiday($id)
    {
        $data = [];
        $data['title'] = lang('Xem thiết lập phép năm');
        $data['paidholiday'] = get_table_where('tbl_setup_paid_holiday', ['id' => $id], '', 'row_array');
        $data['paidholidayDetai'] = get_table_where('tbl_setup_paid_holiday_staff', ['id_setup_paid_holiday' => $id]);
        $this->load->view('admin/paid_holidays/view_staff_setup_paid_holiday', $data);
    }

    public function deleteSetupPaidHoliday()
    {
        $data = [];
        $id = $this->input->post('id');
        if (!empty($id)) {

            $this->db->where('id', $id);
            $paidHoliday = $this->db->get('tbl_setup_paid_holiday')->row_array();

            $get_code = get_table_where('tbl_setup_paid_holiday', array('id' => $id), '', 'row');
            $this->db->where('id', $id);
            $success = $this->db->delete('tbl_setup_paid_holiday');
            if (!empty($success)) {

                $this->db->where('id_setup_paid_holiday', $id);
                $this->db->delete('tbl_setup_paid_holiday_staff');

                activity_log_v2('delete_setup_paid_holiday_leave', 'tbl_setup_paid_holiday', $id, $get_code->year,
                    'Xoá thiết lập phép năm [' . $get_code->year . ']');
                echo json_encode([
                    'result' => $success,
                    'message' => _l('cong_update_true')
                ]);
                die();
            }
        }
        echo json_encode([
            'result' => false,
            'message' => _l('cong_update_false')
        ]);
        die();
    }

    public function paid_holidays_follow()
    {
        $data = [];
        $data['title'] = lang('Bảng theo dõi phép năm');
        $data['staff'] = getPersonDeparmentdt(0);
        $this->load->view('admin/paid_holidays/paid_holidays_follow', $data);
    }

    public function loadPaidHolidayFollows()
    {
        $data = [];
        $year_search = $this->input->get('year_search');
        $staff_search = $this->input->get('staff_search');
        $year_search_old = $year_search - 1;

        $tHead = '';
        $html = '';
        $tfoot = '';
        $is_admin = is_admin();
        $arrIDStaff = employee_manage_staff();
        $arrPaidHoliday = [];

        $tbDepartment = "(
            SELECT
                tblstaff_departments.staffid as staffid,
                GROUP_CONCAT(tbldepartments.name) as name_department
            FROM tbldepartments
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            GROUP BY tblstaff_departments.staffid
        ) tb_department";

        $this->db->select('
            tblstaff.staffid as id,
            tblstaff.code as code,
            tblstaff.day_in as day_in,
            CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname,
            coalesce(tb_department.name_department,"Khác") as name_department,
            tblroles.name as name_role,
            tbl_setup_paid_holiday_staff.number_day_old as number_day_old,
            tbl_setup_paid_holiday_staff.number_day_now as number_day_now,
            tbl_setup_paid_holiday_staff.number_day as number_day,
            tbl_setup_paid_holiday_staff.id_setup_paid_holiday as id_setup_paid_holiday,
            tbl_setup_paid_holiday.year as year,
        ');
        $this->db->from('tblstaff');
        $this->db->join('tbl_setup_paid_holiday_staff', 'tbl_setup_paid_holiday_staff.staff_id = tblstaff.staffid',
            'inner');
        $this->db->join('tbl_setup_paid_holiday',
            'tbl_setup_paid_holiday.id = tbl_setup_paid_holiday_staff.id_setup_paid_holiday', 'inner');
        $this->db->join($tbDepartment, 'tb_department.staffid = tblstaff.staffid', 'left');
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');

        if (!empty($year_search)) {
            $this->db->where('tbl_setup_paid_holiday.year', $year_search);
        }
        if (!empty($staff_search)) {
            $this->db->where('tblstaff.staffid IN (' . implode(',', $staff_search) . ')');
        }
        $this->db->where('tblstaff.active', 1);
//        if($this->perSyntheticslipViewOwn && !$is_admin) {
//            if (!empty($arrIDStaff)) {
//                $this->db->where_in('tbl_synthetic_payslip.staff_id', $arrIDStaff);
//            }
//        }
        $arrPaidHoliday = $this->db->get()->result_array();


        $tHead = '<tr>
            <th class="text-center" rowspan="2" style="width: 15px;">' . lang('STT') . '</th>
            <th class="text-center" rowspan="2" style="width: 80px;">' . lang('MSNV') . '</th>
            <th class="text-center" rowspan="2" style="width: 120px;">' . lang('Họ & Tên') . '</th>
            <th class="text-center" rowspan="2" style="width: 80px;">' . lang('Ngày Vào Làm') . '</th>
            <th class="text-center" rowspan="2" style="width: 20px;">' . lang('Phép Năm ') . $year_search_old . '</th>
            <th class="text-center" rowspan="2" style="width: 20px;">' . lang('Phép Năm ') . $year_search . '</th>
            <th class="text-center" colspan="12" style="width: 50px;">' . lang('Phép Năm ') . $year_search . '</th>
            <th class="text-center" rowspan="2" style="width: 80px;">' . lang('Tổng Phép Đã Nghĩ') . '</th>
            <th class="text-center" rowspan="2" style="width: 80px;">' . lang('Ngày Phép Còn Lại') . '</th>
        ';

        $thMonth = '';
        $hmtlNew = '';
        foreach (getMonth() as $key => $value) {
            if (empty($key)) {
                continue;
            }
            $thMonth .= ' <th class="text-center" style="width: 60px;">' . $value . '</th>';
            $hmtlNew .= ' <td class="text-center" style="width: 60px;"></td>';
        }
        $tHead .= '<tr>
             ' . $thMonth . '
        </tr>';

        $checkExist = '';
        if (!empty($arrPaidHoliday)) {
            foreach ($arrPaidHoliday as $key => $value) {
                $staff_id = $value['id'];
                $html .= '<tr>';
                $html .= '<td class="text-center">' . (++$key) . '</td>';
                $html .= '<td>' . ($value['code']) . '</td>';
                $html .= '<td>' . ($value['fullname']) . '</td>';
                $html .= '<td>' . (!empty($value['day_in']) ? _dhau($value['day_in']) : '') . '</td>';
                $html .= '<td class="text-center" style="color: red">' . ($value['number_day_old'] > 0 ? $value['number_day_old'] : '') . '</td>';
                $html .= '<td class="text-center" style="color: red">' . ($value['number_day_now']) . '</td>';
                $totalPaid = 0;
                foreach (getMonth() as $kk => $vv) {
                    if (empty($kk)) {
                        continue;
                    }
                    $this->db->select('COUNT(tbl_timekeeping_detail.id) as number_date');
                    $this->db->from('tbl_paid_holiday_leave_detail');
                    $this->db->join('tbl_paid_holiday_leave',
                        'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
                    $this->db->join('tbl_paid_holiday_leave_detail_month',
                        'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
                    $this->db->join('tbl_timekeeping_detail',
                        'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
                    $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
                    $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 1);
                    $this->db->where('tbl_paid_holiday_leave.staff_id', $staff_id);
                    $this->db->where('tbl_timekeeping_detail.type', 'AL');
                    $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search");
                    $this->db->where("tbl_paid_holiday_leave_detail_month.month >= $vv AND tbl_paid_holiday_leave_detail_month.month <= $vv");
                    $number_date = $this->db->get()->row_array();

                    $this->db->select('COUNT(tbl_timekeeping_detail.id) as number_date');
                    $this->db->from('tbl_paid_holiday_leave_detail');
                    $this->db->join('tbl_paid_holiday_leave',
                        'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
                    $this->db->join('tbl_paid_holiday_leave_detail_month',
                        'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
                    $this->db->join('tbl_timekeeping_detail',
                        'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
                    $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
                    $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 1);
                    $this->db->where('tbl_paid_holiday_leave.staff_id', $staff_id);
                    $this->db->where('tbl_timekeeping_detail.type', 'AL/2');
                    $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search");
                    $this->db->where("tbl_paid_holiday_leave_detail_month.month >= $vv AND tbl_paid_holiday_leave_detail_month.month <= $vv");
                    $number_date_new = $this->db->get()->row_array();

                    $html .= ' <td class="text-center" style="width: 60px;">' . (($number_date['number_date'] + ($number_date_new['number_date'] * 0.5)) > 0 ? ($number_date['number_date'] + ($number_date_new['number_date'] * 0.5)) : '') . '</td>';
                    $totalPaid += !empty(($number_date['number_date'] + ($number_date_new['number_date'] * 0.5))) ? ($number_date['number_date'] + ($number_date_new['number_date'] * 0.5)) : 0;
                }
                $totalOld = $value['number_day'] - $totalPaid;
                $html .= '<td class="text-center">' . ($totalPaid > 0 ? ($totalPaid) : '') . '</td>';
                $html .= '<td class="text-center">' . ($totalOld > 0 ? ($totalOld) : '') . '</td>';
                $html .= '</tr>';
            }
        }


        $data['tHead'] = $tHead;
        $data['tfoot'] = $tfoot;
        $data['html'] = $html;
        $this->load->view('admin/paid_holidays/load_view_paid_holidays_follows', $data);
    }

    public function SendEmailNoti($id_staff = 0, $id_paid_holiday = 0)
    {
        return true;
        if (!empty($id_staff) && !empty($id_paid_holiday)) {
            $this->db->where('staffid', $id_staff);
            $this->db->where('active', 1);
            $staff = $this->db->get('tblstaff')->row_array();
            if (!empty($staff)) {
                $this->db->where('id', $id_paid_holiday);
                $paid_holiday = $this->db->get('tbl_paid_holiday_leave')->row();

                $list_staff = $staff['email'];
                $this->load->config('email');
                $template = new StdClass();
                $template->message = get_option('email_header') . '<br/> ' . get_staff_full_name() . ' Vừa thêm bạn vào người duyệt đơn xin nghỉ phép vào lúc ' . _dt(date('Y-m-d H:i:s')) . '<br/>
				 <b>Người xin nghỉ phép:</b> ' . (!empty($paid_holiday->staff_id) ? get_staff_full_name($paid_holiday->staff_id) : 0) . '<br/>
				 <b>Tên phiếu:</b> ' . $paid_holiday->name . '<br/>
				 Vui lòng theo dõi và tiến hành cập nhật!<br/>';
                $template->fromname = get_option('companyname') != '' ? get_option('companyname') : '';
                $template->subject = 'ĐƠN XIN NGHỈ PHÉP';
                $this->email->initialize();
                if (get_option('mail_engine') == 'phpmailer') {
                    $this->email->set_debug_output(function ($err) {
                        return false;
                    });
                }
                $this->email->set_newline(config_item('newline'));
                $this->email->set_crlf(config_item('crlf'));
                $this->email->from(get_option('smtp_email'), $template->fromname);
                $this->email->to($list_staff);
                $systemBCC = get_option('bcc_emails');
                if ($systemBCC != '') {
                    $this->email->bcc($systemBCC);
                }
                $this->email->subject($template->subject);
                $this->email->message($template->message);
                if ($this->email->send(true)) {
                    return true;
                } else {
                }
            }
        }
        return false;
    }

    public function getListDate(){
        $data = [];
        $monthNew = !empty($this->input->post('month')) ? $this->input->post('month') : [date('m')];
        $year = !empty($this->input->post('year')) ? $this->input->post('year') : [date('Y')];
        $dtMonth = $monthNew;
        $monthMin = $monthNew[0];
        $monthMax = array_pop($monthNew);

        $data['startdate'] = date('' . $year . '-' . $monthMin . '-01');
        $data['newdate'] = date('' . $year . '-' . $monthMax . '-t');
        $allDateNew = [];
        $allDate = [];
        foreach ($dtMonth as $kk => $vv){
            $allDateNew = createDateRangeArray($vv,$year);
            $allDate = array_merge($allDate,$allDateNew);
        }
        foreach ($allDate as $key => $value){
            $timestamp = strtotime($value);
            $day = date('D', $timestamp);
            $month = date('M', $timestamp);
            $date = date('d', $timestamp);
            $allDateNew[$key]= [
                'date' => _dhau($value),
                'day' => $day,
                'month' => $month,
                'date_new' => $date,
            ];
        }
        $data['allDateNew'] = $allDateNew;
        echo json_encode($data);
    }

    public function checkSunday(){
        $data = [];
        $dateCheck = $this->input->post('dateCheck');
        $dateCheck = to_sql_date($dateCheck);
        $timestamp = strtotime($dateCheck);
        $day = date('D', $timestamp);
        if ($day == 'Sun'){
            $data['result'] = 1;
        } else {
            $data['result'] = 0;
        }
        echo json_encode($data);

    }

    public function report_paid_holiday_leave(){
        $data = [];
        $data['staff'] = getPersonDeparmentdt(0);
        $data['title'] = lang('Thống kê ngày phép, loại phép');
        $this->load->view('admin/paid_holidays/report_paid_holiday_leave', $data);
    }

    public function getPaidHolidayLeaveReport(){

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

        $this->db->select('COUNT(tbl_timekeeping_detail.id) as number_date,tbl_timekeeping_detail.staff_id');
        $this->db->from('tbl_paid_holiday_leave_detail');
        $this->db->join('tbl_paid_holiday_leave',
            'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
        $this->db->join('tbl_paid_holiday_leave_detail_month',
            'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
        $this->db->join('tbl_timekeeping_detail',
            'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
        $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
        $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 1);
        $this->db->where('(tbl_timekeeping_detail.type = "AL")');
        $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search");
        $this->db->where("tbl_paid_holiday_leave_detail_month.month >= $month_search AND tbl_paid_holiday_leave_detail_month.month <= $month_search");
        $this->db->group_by('tbl_timekeeping_detail.staff_id');
        $number_date_phep = $this->db->get()->result_array();

        $this->db->select('(COUNT(tbl_timekeeping_detail.id) * 0.5) as number_date,tbl_timekeeping_detail.staff_id');
        $this->db->from('tbl_paid_holiday_leave_detail');
        $this->db->join('tbl_paid_holiday_leave',
            'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
        $this->db->join('tbl_paid_holiday_leave_detail_month',
            'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
        $this->db->join('tbl_timekeeping_detail',
            'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
        $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
        $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 1);
        $this->db->where('(tbl_timekeeping_detail.type = "AL/2")');
        $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search");
        $this->db->where("tbl_paid_holiday_leave_detail_month.month >= $month_search AND tbl_paid_holiday_leave_detail_month.month <= $month_search");
        $this->db->group_by('tbl_timekeeping_detail.staff_id');
        $number_date_phep_new = $this->db->get()->result_array();
        $number_date_phep = array_merge($number_date_phep,$number_date_phep_new);
        $number_date_phep_vs1 = [];
        if (!empty($number_date_phep)){
            foreach ($number_date_phep as $key => $value){
                if (!empty($number_date_phep_vs1[$value['staff_id']])){
                    $number_date_phep_vs1[$value['staff_id']]['number_date'] += $value['number_date'];
                } else {
                    $number_date_phep_vs1[$value['staff_id']] = $value;
                }
            }
        }

        $this->db->select('(COUNT(tbl_timekeeping_detail.id)) as number_date,tbl_timekeeping_detail.staff_id');
        $this->db->from('tbl_paid_holiday_leave_detail');
        $this->db->join('tbl_paid_holiday_leave',
            'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
        $this->db->join('tbl_paid_holiday_leave_detail_month',
            'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
        $this->db->join('tbl_timekeeping_detail',
            'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
        $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
        $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 3);
        $this->db->where('(tbl_timekeeping_detail.type = "CH")');
        $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search");
        $this->db->where("tbl_paid_holiday_leave_detail_month.month >= $month_search AND tbl_paid_holiday_leave_detail_month.month <= $month_search");
        $this->db->group_by('tbl_timekeeping_detail.staff_id');
        $number_date_ch = $this->db->get()->result_array();
        $number_date_ch_vs1 = [];
        if (!empty($number_date_ch)){
            foreach ($number_date_ch as $key => $value){
                if (!empty($number_date_ch_vs1[$value['staff_id']])){
                    $number_date_ch_vs1[$value['staff_id']]['number_date'] += $value['number_date'];
                } else {
                    $number_date_ch_vs1[$value['staff_id']] = $value;
                }
            }
        }

        $this->db->select('(COUNT(tbl_timekeeping_detail.id)) as number_date,tbl_timekeeping_detail.staff_id');
        $this->db->from('tbl_timekeeping_detail');
        $this->db->where('(tbl_timekeeping_detail.type = "TDL" OR tbl_timekeeping_detail.type = "TAL" OR tbl_timekeeping_detail.type = "NCT" OR tbl_timekeeping_detail.type = "QTLĐ" 
        OR tbl_timekeeping_detail.type = "QK" OR tbl_timekeeping_detail.type = "GTHV")');
        $this->db->where("DATE_FORMAT(tbl_timekeeping_detail.date, \"%Y\") >= $year_search AND DATE_FORMAT(tbl_timekeeping_detail.date, \"%Y\") <= $year_search");
        $this->db->where("DATE_FORMAT(tbl_timekeeping_detail.date, \"%m\") >= $month_search AND DATE_FORMAT(tbl_timekeeping_detail.date, \"%m\") <= $month_search");
        $this->db->group_by('tbl_timekeeping_detail.staff_id');
        $number_date_lt = $this->db->get()->result_array();
        $number_date_lt_vs1 = [];
        if (!empty($number_date_lt)){
            foreach ($number_date_lt as $key => $value){
                if (!empty($number_date_lt_vs1[$value['staff_id']])){
                    $number_date_lt_vs1[$value['staff_id']]['number_date'] += $value['number_date'];
                } else {
                    $number_date_lt_vs1[$value['staff_id']] = $value;
                }
            }
        }

        $this->db->select('(COUNT(tbl_timekeeping_detail.id)) as number_date,tbl_timekeeping_detail.staff_id');
        $this->db->from('tbl_paid_holiday_leave_detail');
        $this->db->join('tbl_paid_holiday_leave',
            'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
        $this->db->join('tbl_paid_holiday_leave_detail_month',
            'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
        $this->db->join('tbl_timekeeping_detail',
            'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
        $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
        $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 2);
        $this->db->where('(tbl_timekeeping_detail.type = "OD")');
        $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search");
        $this->db->where("tbl_paid_holiday_leave_detail_month.month >= $month_search AND tbl_paid_holiday_leave_detail_month.month <= $month_search");
        $this->db->group_by('tbl_timekeeping_detail.staff_id');
        $number_date_OD = $this->db->get()->result_array();
        $number_date_OD_vs1 = [];
        if (!empty($number_date_OD)){
            foreach ($number_date_OD as $key => $value){
                if (!empty($number_date_OD_vs1[$value['staff_id']])){
                    $number_date_OD_vs1[$value['staff_id']]['number_date'] += $value['number_date'];
                } else {
                    $number_date_OD_vs1[$value['staff_id']] = $value;
                }
            }
        }

        $this->db->select('(COUNT(tbl_timekeeping_detail.id)) as number_date,tbl_timekeeping_detail.staff_id');
        $this->db->from('tbl_paid_holiday_leave_detail');
        $this->db->join('tbl_paid_holiday_leave',
            'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
        $this->db->join('tbl_paid_holiday_leave_detail_month',
            'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
        $this->db->join('tbl_timekeeping_detail',
            'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
        $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
        $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 4);
        $this->db->where('(tbl_timekeeping_detail.type = "TS")');
        $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search");
        $this->db->where("tbl_paid_holiday_leave_detail_month.month >= $month_search AND tbl_paid_holiday_leave_detail_month.month <= $month_search");
        $this->db->group_by('tbl_timekeeping_detail.staff_id');
        $number_date_TS = $this->db->get()->result_array();
        $number_date_TS_vs1 = [];
        if (!empty($number_date_TS)){
            foreach ($number_date_TS as $key => $value){
                if (!empty($number_date_TS_vs1[$value['staff_id']])){
                    $number_date_TS_vs1[$value['staff_id']]['number_date'] += $value['number_date'];
                } else {
                    $number_date_TS_vs1[$value['staff_id']] = $value;
                }
            }
        }

        $this->db->select('COUNT(tbl_timekeeping_detail.id) as number_date,tbl_timekeeping_detail.staff_id');
        $this->db->from('tbl_paid_holiday_leave_detail');
        $this->db->join('tbl_paid_holiday_leave',
            'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
        $this->db->join('tbl_paid_holiday_leave_detail_month',
            'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
        $this->db->join('tbl_timekeeping_detail',
            'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
        $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
        $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 5);
        $this->db->where('(tbl_timekeeping_detail.type = "UP")');
        $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search");
        $this->db->where("tbl_paid_holiday_leave_detail_month.month >= $month_search AND tbl_paid_holiday_leave_detail_month.month <= $month_search");
        $this->db->group_by('tbl_timekeeping_detail.staff_id');
        $number_date_kphep = $this->db->get()->result_array();

        $this->db->select('(COUNT(tbl_timekeeping_detail.id) * 0.5) as number_date,tbl_timekeeping_detail.staff_id');
        $this->db->from('tbl_paid_holiday_leave_detail');
        $this->db->join('tbl_paid_holiday_leave',
            'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
        $this->db->join('tbl_paid_holiday_leave_detail_month',
            'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
        $this->db->join('tbl_timekeeping_detail',
            'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
        $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
        $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 5);
        $this->db->where('(tbl_timekeeping_detail.type = "UP/2")');
        $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search");
        $this->db->where("tbl_paid_holiday_leave_detail_month.month >= $month_search AND tbl_paid_holiday_leave_detail_month.month <= $month_search");
        $this->db->group_by('tbl_timekeeping_detail.staff_id');
        $number_date_kphep_new = $this->db->get()->result_array();
        $number_date_kphep = array_merge($number_date_kphep,$number_date_kphep_new);
        $number_date_kphep_vs1 = [];
        if (!empty($number_date_kphep)){
            foreach ($number_date_kphep as $key => $value){
                if (!empty($number_date_kphep_vs1[$value['staff_id']])){
                    $number_date_kphep_vs1[$value['staff_id']]['number_date'] += $value['number_date'];
                } else {
                    $number_date_kphep_vs1[$value['staff_id']] = $value;
                }
            }
        }


        $this->db->select('SUM(tbl_paid_holiday_leave_detail.number_date) as number_date,tbl_timekeeping_detail.staff_id');
        $this->db->from('tbl_paid_holiday_leave_detail');
        $this->db->join('tbl_paid_holiday_leave',
            'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
        $this->db->join('tbl_paid_holiday_leave_detail_month',
            'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
        $this->db->join('tbl_timekeeping_detail',
            'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
        $this->db->where('tbl_paid_holiday_leave_detail.status', 2);
        $this->db->where('(tbl_timekeeping_detail.type = "KP")');
        $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search");
        $this->db->where("tbl_paid_holiday_leave_detail_month.month >= $month_search AND tbl_paid_holiday_leave_detail_month.month <= $month_search");
        $this->db->group_by('tbl_timekeeping_detail.staff_id');
        $number_date = $this->db->get()->result_array();
        $number_date_vs1 = [];
        if (!empty($number_date)){
            foreach ($number_date as $key => $value){
                if (!empty($number_date_vs1[$value['staff_id']])){
                    $number_date_vs1[$value['staff_id']]['number_date'] += $value['number_date'];
                } else {
                    $number_date_vs1[$value['staff_id']] = $value;
                }
            }
        }



        $aColumns = [
            'tblstaff.staffid as id,
            tblstaff.code as code,
            CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name_staff,
            tb_department.name_department as name_deparment
            '
        ];
        $sIndexColumn = 'staffid';
        $sTable = 'tblstaff';
        $where = [
            'AND tblstaff.status_work != 2'
        ];
        $filter = [];
        $join = [
            'LEFT JOIN tblroles ON tblroles.roleid = tblstaff.role',
            'LEFT JOIN ' . $tbDepartment . ' ON tb_department.staffid = tblstaff.staffid',
        ];

        if (!empty($staff_search)) {
            array_push($where,
                'AND ( tblstaff.staffid IN (' . implode(',', $staff_search) . '))');
        }



        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], 'ORDER BY tblstaff.code asc', [], []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $stt = 1;
        $totalQuantityPhep = 0;
        $totalQuantityCH = 0;
        $totalQuantityLT = 0;
        $totalQuantityOD = 0;
        $totalQuantityTS = 0;
        $totalQuantityKP = 0;
        $totalQuantityKPNew = 0;
        $total_number_date_all = 0;
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = array();

            $total_number_date = 0;

            $row[] = '<div class="text-center">'.(++$key).'</div>';
            $row[] = $aRow['code'];
            $row[] = '<div style="width: 200px">'.$aRow['name_staff'].'</div>';
            $row[] = '<div style="width: 300px">'.$aRow['name_deparment'].'</div>';
            $quantityPhep = !empty($number_date_phep_vs1[$aRow['id']]) ? $number_date_phep_vs1[$aRow['id']]['number_date'] : 0;
            $row[] = '<div class="text-center">'.(!empty($quantityPhep) ? ($quantityPhep) : '').'</div>';
            $quantityCH = !empty($number_date_ch_vs1[$aRow['id']]) ? $number_date_ch_vs1[$aRow['id']]['number_date'] : 0;
            $row[] = '<div class="text-center">'.(!empty($quantityCH) ? ($quantityCH) : '').'</div>';
            $quantityLT = !empty($number_date_lt_vs1[$aRow['id']]) ? $number_date_lt_vs1[$aRow['id']]['number_date'] : 0;
            $row[] = '<div class="text-center">'.(!empty($quantityLT) ? ($quantityLT) : '').'</div>';
            $quantityOD = !empty($number_date_OD_vs1[$aRow['id']]) ? $number_date_OD_vs1[$aRow['id']]['number_date'] : 0;
            $row[] = '<div class="text-center">'.(!empty($quantityOD) ? ($quantityOD) : '').'</div>';
            $quantityTS = !empty($number_date_TS_vs1[$aRow['id']]) ? $number_date_TS_vs1[$aRow['id']]['number_date'] : 0;
            $row[] = '<div class="text-center">'.(!empty($quantityTS) ? ($quantityTS) : '').'</div>';
            $quantityKP = !empty($number_date_kphep_vs1[$aRow['id']]) ? $number_date_kphep_vs1[$aRow['id']]['number_date'] : 0;
            $row[] = '<div class="text-center">'.(!empty($quantityKP) ? ($quantityKP) : '').'</div>';
            $quantityKPNew = !empty($number_date_vs1[$aRow['id']]) ? $number_date_vs1[$aRow['id']]['number_date'] : 0;
            $row[] = '<div class="text-center">'.(!empty($quantityKPNew) ? ($quantityKPNew) : '').'</div>';

            $total_number_date = $quantityPhep + $quantityCH + $quantityLT + $quantityOD + $quantityTS + $quantityKP + $quantityKPNew;
            $row[] = '<div class="text-center bold">'.(!empty($total_number_date) ? ($total_number_date) : '').'</div>';

            $totalQuantityPhep += $quantityPhep;
            $totalQuantityCH += $quantityCH;
            $totalQuantityLT += $quantityLT;
            $totalQuantityOD += $quantityOD;
            $totalQuantityTS += $quantityTS;
            $totalQuantityKP += $quantityKP;
            $totalQuantityKPNew += $quantityKPNew;
            $total_number_date_all += $total_number_date;

            $output['aaData'][] = $row;
            $stt++;

        }
        $output['totalQuantityPhep'] = $totalQuantityPhep;
        $output['totalQuantityCH'] = $totalQuantityCH;
        $output['totalQuantityLT'] = $totalQuantityLT;
        $output['totalQuantityOD'] = $totalQuantityOD;
        $output['totalQuantityTS'] = $totalQuantityTS;
        $output['totalQuantityKP'] = $totalQuantityKP;
        $output['totalQuantityKPNew'] = $totalQuantityKPNew;
        $output['total_number_date_all'] = $total_number_date_all;
        echo json_encode($output);
    }
}