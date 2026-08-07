<?php

use GuzzleHttp\Psr7\Response;

defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'controllers/api_app/Api_Controller.php');

class Api_Paid_holiday extends Api_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->allowed_file_size = '1024';
        $this->upload_path = get_upload_path_by_type('products');
        $this->datetime_now = time();

        $tokenAccount = '';
        $data_post = file_get_contents('php://input');
        if (!empty($data_post)) {
            if (!is_array($data_post)) {
                $data_post = json_decode($data_post, true);
                if (!empty($data_post['tokenAccount'])) {
                    $tokenAccount = $data_post['tokenAccount'];
                }
            }
        }
        $staffid = checkTokenLoginApp($tokenAccount);
        // $staffid = 1;
        $staff = get_table_where('tblstaff', array('staffid' => $staffid), '', 'row');
        if (!empty($staff)) {
            $this->staffid = $staffid;
        } else {
            echo json_encode([
                'code' => 111,
                'message' => 'User không tồn tại',
                'result' => false,
            ]);
            die;
        }


        $this->perApprovePaidHoliday = has_permission('paid_holidays', $this->staffid, 'approve');
        $this->isAdmin = is_admin();
    }


    public function getListPaidHoliday($page = 1, $limit = 10)
    {
        $result = [];
        $start = ($page - 1) * $limit;

        $data = [];

        $name_search = $this->input->post('name_search');
        $staff_search = $this->input->post('staff_search');
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                    if (!empty($data_post['name_search'])) {
                        $name_search = $data_post['name_search'];
                    }
                    if (!empty($data_post['staff_search'])) {
                        $staff_search = $data_post['staff_search'];
                    }
                }
            }
        }
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
            CONCAT(tb_staff.firstname," ",tb_staff.lastname) as name_staff,
            CONCAT(personel_replace.firstname," ",personel_replace.lastname) as name_staff_replace,
            tbl_paid_holiday_leave.created_by as created_by,
            tbl_paid_holiday_leave.staff_agree as staff_agree,
            tbl_paid_holiday_leave.date_created as date_created,
            tbl_paid_holiday_leave.date_status as date_status,
            tbl_paid_holiday_leave.staff_status as staff_status,
            tb_department.name_department as name_deparment,
            tblroles.name as name_roles,
            tb_staff.phonenumber as telephone,
            tb_staff.current_accommodation as current_accommodation
        ');
        $this->db->from('tbl_paid_holiday_leave');
        $this->db->join('tblstaff tb_staff', 'tb_staff.staffid = tbl_paid_holiday_leave.staff_id', 'inner');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_paid_holiday_leave.created_by', 'inner');
        $this->db->join('tblstaff personel_replace',
            'personel_replace.staffid = tbl_paid_holiday_leave.staff_id_replace', 'left');
        $this->db->join('tblroles', 'tblroles.roleid = tb_staff.role', 'left');
        $this->db->join($tbDepartment, 'tb_department.staffid = tb_staff.staffid', 'left');

        if (!empty($name_search)) {
            $this->db->where('( tbl_paid_holiday_leave.name like "%' . $name_search . '%")');
        }
        if (!empty($staff_search)) {
            $this->db->where('( tbl_paid_holiday_leave.staff_id IN (' . implode(',', $staff_search) . '))');
        }
        $this->db->limit($limit, $start);
        $result = $this->db->get()->result_array();
        foreach ($result as $key => $aRow) {
            $staff_created = get_staff_full_name($aRow['created_by']);
            $staff_created_image = staff_profile_image_ch($aRow['created_by']);
            $result[$key]['staff_created'] = $staff_created;
            $result[$key]['staff_created_image'] = $staff_created_image;

            $staff_agree = get_staff_full_name($aRow['staff_agree']);
            $staff_agree_image = staff_profile_image_ch($aRow['staff_agree']);

            $result[$key]['staff_agree'] = $staff_agree;
            $result[$key]['staff_agree_image'] = $staff_agree_image;

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
                tbl_paid_holiday_leave_detail_month.month as month,
            ');
            $this->db->from('tbl_paid_holiday_leave_detail');
            $this->db->join('tbl_type_magic', 'tbl_type_magic.id = tbl_paid_holiday_leave_detail.type_magic_id',
                'left');
            $this->db->join('tbl_paid_holiday_leave_detail_month', 'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id',
                'left');
            $this->db->where('tbl_paid_holiday_leave_detail.paid_holiday_leave_id', $aRow['id']);
            $paidHplidayDetail = $this->db->get()->result_array();
            $countStatus = 0;
            foreach ($paidHplidayDetail as $k => $v) {

                $name_type = '';
                if ($v['id_type_magic'] == 1) {
                    $name_type = ' (CP , P 1/2)';
                } elseif ($v['id_type_magic'] == 2) {
                    $name_type = ' (OD)';
                } elseif ($v['id_type_magic'] == 3) {
                    $name_type = ' (CH)';
                } elseif ($v['id_type_magic'] == 4) {
                    $name_type = ' (TS)';
                } elseif ($v['id_type_magic'] == 5) {
                    $name_type = ' (PKL , PKL 1/2)';
                } elseif ($v['id_type_magic'] == 6) {
                    $name_type = ' (F)';
                }

                $user_status = $v['staff_status'];
                if (!empty($v['date_status'])) {
                    $date_status = _d($v['date_status']);
                }
                $user_status_name = get_staff_full_name($user_status);
                $user_status_image = staff_profile_image_ch($user_status);

                $paidHplidayDetail[$k]['user_status_name'] = $user_status_name;
                $paidHplidayDetail[$k]['user_status_image'] = $user_status_image;

                $strApprove = '';
                $strNote = '';
                if ($v['status'] == 0) {
                    $countStatus++;
                    $strApprove = lang('Chưa duyệt');
                } elseif ($v['status'] == 1) {
                    $strApprove = lang('Đã duyệt');
                } elseif ($v['status'] == 2) {
                    $strApprove = lang('Không duyệt');
                    $strNote = $v['note_status'];
                }

                $paidHplidayDetail[$k]['name_type'] = $name_type;
                $paidHplidayDetail[$k]['strApprove'] = $strApprove;
                $paidHplidayDetail[$k]['strNote'] = $strNote;
                $paidHplidayDetail[$k]['countStatus'] = $countStatus;
            }
            $result[$key]['paidHplidayDetail'] = $paidHplidayDetail;
        }

        $this->db->select('tbl_paid_holiday_leave.id', false);
        $startNest = ($page) * $limit;
        $this->db->limit(1, $startNest);

        $this->db->from('tbl_paid_holiday_leave');
        $this->db->join('tblstaff tb_staff', 'tb_staff.staffid = tbl_paid_holiday_leave.staff_id', 'inner');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_paid_holiday_leave.created_by', 'inner');
        $this->db->join('tblstaff personel_replace',
            'personel_replace.staffid = tbl_paid_holiday_leave.staff_id_replace', 'left');
        $this->db->join('tblroles', 'tblroles.roleid = tb_staff.role', 'left');
        $this->db->join($tbDepartment, 'tb_department.staffid = tb_staff.staffid', 'left');

        if (!empty($name_search)) {
            $this->db->where('( tbl_paid_holiday_leave.name like "%' . $name_search . '%")');
        }
        if (!empty($staff_search)) {
            $this->db->where('( tbl_paid_holiday_leave.staff_id IN (' . implode(',', $staff_search) . '))');
        }

        $data['next'] = $this->db->get()->num_rows();

        $data['result'] = $result;

        echo json_encode($data);
    }

    public function getDetailPaidHoliday($id)
    {
        $data = [];
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
        $this->db->where('tbl_paid_holiday_leave_detail.paid_holiday_leave_id', $id);
        $paidHplidayDetail = $this->db->get()->result_array();
        $countStatus = 0;
        foreach ($paidHplidayDetail as $k => $v) {

            $name_type = '';
            if ($v['id_type_magic'] == 1) {
                $name_type = ' (CP , P 1/2)';
            } elseif ($v['id_type_magic'] == 2) {
                $name_type = ' (OD)';
            } elseif ($v['id_type_magic'] == 3) {
                $name_type = ' (CH)';
            } elseif ($v['id_type_magic'] == 4) {
                $name_type = ' (TS)';
            } elseif ($v['id_type_magic'] == 5) {
                $name_type = ' (PKL , PKL 1/2)';
            } elseif ($v['id_type_magic'] == 6) {
                $name_type = ' (F)';
            }

            $user_status = $v['staff_status'];
            if (!empty($v['date_status'])) {
                $date_status = _d($v['date_status']);
            }
            $user_status_name = get_staff_full_name($user_status);
            $user_status_image = staff_profile_image_ch($user_status);

            $paidHplidayDetail[$k]['user_status_name'] = $user_status_name;
            $paidHplidayDetail[$k]['user_status_image'] = $user_status_image;

            $strApprove = '';
            $strNote = '';
            if ($v['status'] == 0) {
                $countStatus++;
                $strApprove = lang('Chưa duyệt');
            } elseif ($v['status'] == 1) {
                $strApprove = lang('Đã duyệt');
            } elseif ($v['status'] == 2) {
                $strApprove = lang('Không duyệt');
                $strNote = $v['note_status'];
            }

            $paidHplidayDetail[$k]['name_type'] = $name_type;
            $paidHplidayDetail[$k]['strApprove'] = $strApprove;
            $paidHplidayDetail[$k]['strNote'] = $strApprove;
            $paidHplidayDetail[$k]['countStatus'] = $countStatus;
        }
        $data['paidHplidayDetail'] = $paidHplidayDetail;
        echo json_encode($data);
    }

    public function add_paid_holiday()
    {
        $data = [];

        $dataPost = $this->input->post('data');
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                    if (!empty($data_post['data'])) {
                        $dataPost = $data_post['data'];
                    }
                }
            }
        }

        $name = $dataPost['name'];
        $staff_id = $dataPost['staff_id'];
        $staff_agree = $dataPost['staff_agree'];
        $staff_id_replace = $dataPost['staff_id_replace'];
        $pm = $dataPost['items'];
        $items = [];
        $total_date_phep = 0;
        $year_search = date('Y');
        $year_search_old = date('Y') - 1;
        $option = [
            'name' => $name,
            'staff_id' => $staff_id,
            'staff_agree' => $staff_agree,
            'staff_id_replace' => $staff_id_replace,
            'status' => 0,
            'created_by' => $this->staffid,
            'date_created' => date('Y-m-d H:i:s'),
        ];

        if (!empty($pm)) {
            foreach ($pm as $key => $value) {

                $number_day = number_unformat($value['number_day']);
                $month_detail = ($value['month_detail']);
                $sub = [];

                $total_quantity_sub = 0;
                $quantity_sub = $number_day;
                $sub[] = [
                    'month' => $month_detail,
                    'number_day' => $quantity_sub,
                ];

                $total_quantity_sub += $quantity_sub;
                if ($total_quantity_sub <= 0) {
                    $data['result'] = 0;
                    $data['message'] = 'Vui lòng nhập số ngày nghỉ !';
                    echo json_encode($data);
                    die();
                }
                if ($value['type_magic'] == 1) {
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

        $this->db->select('tbl_setup_paid_holiday_staff.number_day');
        $this->db->from('tbl_setup_paid_holiday');
        $this->db->join('tbl_setup_paid_holiday_staff',
            'tbl_setup_paid_holiday_staff.id_setup_paid_holiday = tbl_setup_paid_holiday.id');
        $this->db->where('tbl_setup_paid_holiday.year', $year_search);
        $this->db->where('tbl_setup_paid_holiday_staff.staff_id', $staff_id);
        $paid_year = $this->db->get()->row_array();

        $this->db->select('tbl_setup_paid_holiday_staff.number_day');
        $this->db->from('tbl_setup_paid_holiday');
        $this->db->join('tbl_setup_paid_holiday_staff',
            'tbl_setup_paid_holiday_staff.id_setup_paid_holiday = tbl_setup_paid_holiday.id');
        $this->db->where('tbl_setup_paid_holiday.year', $year_search_old);
        $this->db->where('tbl_setup_paid_holiday_staff.staff_id', $staff_id);
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
        $date_check = date('Y-' . $month . '-' . $day . '');
        if (strtotime(date('Y-m-d')) > strtotime($date_check)) {
            $number_date_phep_old = 0;
        }

        if ($total_date_phep > ($number_date_phep + $number_date_phep_old)) {
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
            notificationAddPaidHoliday($id_insert,$this->staffid);
            $this->SendEmailNoti($staff_agree, $id_insert);
            $data['result'] = 1;
            $data['message'] = 'Thêm thành công';
        } else {
            $data['result'] = 0;
            $data['message'] = 'Thêm thất bại';
        }

        echo json_encode($data);
    }

    public function getTypeMagic(){
        $data = [];
        $this->db->select('tbl_type_magic.*');
        $this->db->from('tbl_type_magic');
        $this->db->order_by('tbl_type_magic.id asc');
        $dt = $this->db->get()->result_array();
        $data['result'] = $dt;
        echo json_encode($data);
    }

    public function update_status_child()
    {
        $id = $this->input->post('id');
        $status = $this->input->post('status');
        $note = $this->input->post('note');

        $dataPost = $this->input->post('data');
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                    if (!empty($data_post['id'])) {
                        $id = $data_post['id'];
                    }
                    if (!empty($data_post['status'])) {
                        $status = $data_post['status'];
                    }
                    if (!empty($data_post['note'])) {
                        $note = $data_post['note'];
                    }
                }
            }
        }

        if (!$this->perApprovePaidHoliday){
            echo json_encode([
                'success' => false,
                'message' => _l('Bạn không có quyền duyệt')
            ]);
            die();
        }

        if (empty($id)){
            echo json_encode([
                'success' => false,
                'message' => _l('Vui lòng truyền đủ dữ liệu')
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


            if ($status == 0){
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
                            $data['message'] = 'Loại phép này đã ap dung bên chấm công và đã tính bảng lương không thể bỏ duyệt !';
                            echo json_encode($data);
                            die();
                        }
                    }
                }
            }

            $data_update = ['status' => $status];
            if (!empty($status)) {
                $data_update['staff_status'] = $this->staffid;
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
                            notificationAgreePaidHoliday($paid_holiday_id,$paid_holiday_detail_id,$this->staffid,1);
                        } elseif($status == 2){
                            $this->db->where('id', $vv['id']);
                            $this->db->update('tbl_timekeeping_detail', [
                                'type' => 'KP',
                                'date_updated' => date('Y-m-d H:i:s'),
                                'updated_by' => $this->staffid,
                                'paid_holiday_id' => $paid_holiday_id,
                                'paid_holiday_detail_id' => $paid_holiday_detail_id,
                            ]);
                            notificationAgreePaidHoliday($paid_holiday_id,$paid_holiday_detail_id,$this->staffid,2);
                        } else {
                            $this->db->where('id', $vv['id']);
                            $this->db->update('tbl_timekeeping_detail', [
                                'type' => 'X',
                                'date_updated' => date('Y-m-d H:i:s'),
                                'updated_by' => $this->staffid,
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

    public function deleteTicket()
    {
        $id = $this->input->post('id');
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                    if (!empty($data_post['id'])) {
                        $id = $data_post['id'];
                    }
                }
            }
        }

        if (empty($id)){
            echo json_encode([
                'success' => false,
                'message' => _l('Vui lòng truyền đủ dữ liệu')
            ]);
            die();
        }

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
                    'message' => _l('Xóa thành công')
                ]);
                die();
            }
        }
        echo json_encode([
            'result' => false,
            'message' => _l('Xóa thất bại')
        ]);
        die();
    }

    public function update_paid_holiday(){
        $data = [];

        $dataPost = $this->input->post('data');
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                    if (!empty($data_post['data'])) {
                        $dataPost = $data_post['data'];
                    }
                }
            }
        }
        $name = $dataPost['name'];
        $staff_id = $dataPost['staff_id'];
        $staff_agree = $dataPost['staff_agree'];
        $staff_id_replace = $dataPost['staff_id_replace'];
        $pm = $dataPost['items'];
        $items = [];
        $total_date_phep = 0;
        $year_search = date('Y');
        $year_search_old = date('Y') - 1;

        $id = !empty($dataPost['id']) ? $dataPost['id'] : 0;

        $checkExisit = get_table_where('tbl_paid_holiday_leave',['id' => $id],'','row_array');

        if (empty($checkExisit)){
            $data['result'] = 0;
            $data['message'] = 'Không tồn tại phiếu';
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
                $number_day = number_unformat($value['number_day']);
                $sub = [];
                $month_detail = ($value['month_detail']);
                $total_quantity_sub = 0;

                $quantity_sub = $number_day;
                $sub[] = [
                    'month' => $month_detail,
                    'number_day' => $quantity_sub,
                ];
                $total_quantity_sub += $quantity_sub;
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
        echo json_encode($data);
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
                $template->message = get_option('email_header') . '<br/> ' . get_staff_full_name($this->staffid) . ' Vừa thêm bạn vào người duyệt đơn xin nghỉ phép vào lúc ' . _dt(date('Y-m-d H:i:s')) . '<br/>
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
}